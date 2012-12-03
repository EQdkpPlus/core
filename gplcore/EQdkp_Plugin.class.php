<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * EQdkp_Plugin.class.php
 * Began: Sun Mar 15 2003
 *
 * $Id$
 *
 ******************************/

if ( !defined('EQDKP_INC') )
{
    header('HTTP/1.0 404 Not Found');
    exit;
}

/**
* EQdkp Plugin class
* Framework for individual classes
* Is used only as an extension to a plugin's actual class
*/

class EQdkp_Plugin
{
    var $pm;                                // Plugin manager       @var pm

    var $data          = array();           // Plugin data          @var data
    var $permissions   = array();           // Permission data      @var permissions

    var $dependencies = array();            // Dependency data      @var dependencies

    var $install_sql   = array();           // Install queries      @var install_sql
    var $uninstall_sql = array();           // Uninstall queries    @var uninstall_sql

    var $hooks         = array();           // Hook calls           @var hooks
    var $menus         = array();           // Menu definitions     @var menus

    var $portal_modules = array();          // Registered portal modules  @var portal_modules
		var $exchange_modules = array();        // Registered exchange modules  @var exchange_modules
		var $exchange_feeds = array();        	// Registered exchange-feeds  @var exchange_modules
    var $pdh_read_modules = array();        // Registered pdh read modules  @var pdh_read_modules
    var $pdh_write_modules = array();        // Registered pdh write modules  @var pdh_write_modules

    // ---------------------------------------------------------
    // Core methods
    // ---------------------------------------------------------

    /**
    * Constructor
    * Stores a reference to the EQdkp Plugin Manager object
    *
    * @param $pm Plugin Manager object
    */
    function eqdkp_plugin($pm = '')
    {
        if ( !empty($pm) )
        {
            $this->pm = &$pm;
        }
    }

    // ---------------------------------------------------------
    // Install / Uninstall methods
    // ---------------------------------------------------------

    /**
    * Add an SQL query to the appropriate array
    *
    * @param $type SQL_INSTALL | SQL_UNINSTALL
    * @param $sql Query string
    * @param $order Step
    */
    function add_sql($type, $sql, $order = '')
    {
        switch ( $type )
        {
            case SQL_INSTALL:
                $order = ( $order == '' ) ? (count($this->install_sql) + 2) : $order;
                $this->install_sql[$order] = $sql;
                break;

            case SQL_UNINSTALL:
                $order = ( $order == '' ) ? (count($this->uninstall_sql) + 3) : $order;
                $this->uninstall_sql[$order] = $sql;
                break;
        }
    }

    /**
    * Run an SQL query from the appropriate array
    *
    * @param $type SQL_INSTALL | SQL_UNINSTALL
    * @return bool
    */
    function run_sql($type)
    {
        global $db, $user, $acl;

        $permissions = $this->get_permissions();
        ksort($permissions);

        switch ( $type )
        {
            case SQL_INSTALL:
                // Check if we need to add permissions
                if ( count($permissions) > 0 )
                {

                    foreach ( $permissions as $auth_value => $permission )
                    {
												$acl->update_auth_option($auth_value, $permission['auth_default']);
                    }

                  	//Grant permissions to installing user
										if ( $user->data['user_id'] != ANONYMOUS ){
											$permission_array = array();
											foreach ($permissions as $auth_value => $permission) {
												$permission_array[$acl->get_auth_id($auth_value)] = "Y";
											}
											$acl->update_user_permissions($permission_array, $user->data['user_id']);
										}
															
										//Grant permissions to groups
										foreach ($permissions as $auth_value => $permission) {
											
											if ($permission['groups']){
	
												foreach($permission['groups'] as $key=>$group_id){

													$db->query("DELETE FROM __auth_groups WHERE group_id = ".$db->escape($group_id)." AND auth_id = ".$db->escape($acl->get_auth_id($auth_value)));
													$db->query("INSERT INTO __auth_groups (group_id, auth_id, auth_setting) VALUES (".$db->escape($group_id).", ".$db->escape($acl->get_auth_id($auth_value)).", 'Y')");
												}											
											}
										}

                }

                // Set the plugin as 'installed'
                $sql = "UPDATE __plugins
                        SET `plugin_installed` = '1'
                        WHERE `plugin_code` = '" . $this->get_data('code') . "'";
                $this->add_sql(SQL_INSTALL, $sql, 1);
                
                ksort($this->install_sql);
                foreach ( $this->install_sql as $sql )
                {
                    if ( !$db->query($sql) )
                    {
                        // SQL_DB has its own error handling
                        return array(0 => false, 1 => $db->sql_error($sql));
                    }
                }

                $this->pm->set_db_info($this->get_data('code'), 'plugin_version', $this->get_data('version'));
                $this->pm->set_db_info($this->get_data('code'), 'plugin_build', $this->get_data('build'));
				break;

            case SQL_UNINSTALL:
                // Check if we need to remove permissions
                if ( count($permissions) > 0 )
                {
                    $in_clause = '';
                   
				    foreach ( $permissions as $auth_value => $permission )
                    {
                        $in_clause .= $acl->get_auth_id($auth_value) . ',';
						            $acl->del_auth_option($auth_value);
                    }
                    $in_clause = preg_replace('/,$/', '', $in_clause);

                    // Auth Users
                    $sql = "DELETE FROM __auth_users
                            WHERE `auth_id` IN ({$in_clause})";
                    $this->add_sql(SQL_UNINSTALL, $sql, 0);
					
					// Auth Groups
                    $sql = "DELETE FROM __auth_groups
                            WHERE `auth_id` IN ({$in_clause})";
                    $this->add_sql(SQL_UNINSTALL, $sql, 1);
					
					
                }

                // Set the plugin as 'uninstalled'
                $sql = "UPDATE __plugins
                        SET `plugin_installed` = '0'
                        WHERE `plugin_code` = '" . $this->get_data('code') . "'";
                $this->add_sql(SQL_UNINSTALL, $sql, 2);

                ksort($this->uninstall_sql);
                foreach ( $this->uninstall_sql as $sql )
                {
                    if ( !$db->query($sql) )
                    {
                        return false;
                    }
                }
                break;

            default:
                $this->pm->error_append('Run SQL', 'EQdkp_Plugin::run_sql() called without a valid type specified.');
        }

        $this->pm->error_check(true);

        return true;
    }

    /**
    * Provide feedback to the plugins.php script
    *
    * @param $type SQL_INSTALL | SQL_UNINSTALL
    * @param $show_error Show error message(s)
    */
    function message($type, $show_error = true)
    {
        global $db, $tpl, $core, $user;
        global $start_time, $eqdkp_root_path;

        //Generic install statement
        $plugin_name = $this->get_data('name');

        switch ( $type )
        {
            case SQL_INSTALL:
                $install_uninstall = 'install';
                $installed_statusmsg = $user->lang['plugin_inst_installed'];
                break;
            case SQL_UNINSTALL:
                $install_uninstall = 'uninstall';
                $installed_statusmsg = $user->lang['plugin_inst_uninstalled'];
                break;
        }

        if ( ($this->pm->is_error()) && ($show_error) )
        {
          $title = $user->lang['plugin_inst_error'];
          $text  = sprintf($user->lang['plugin_inst_errormsg1'],$install_uninstall,$this->pm->get_errors());
          $text .= sprintf($user->lang['plugin_inst_errormsg2'],$plugin_name,$installed_statusmsg);

          // Use regular die if $tpl is invalid or message_die is undefined
          if ( (!is_object($tpl)) || (!function_exists('message_die')) )
          {
            die($text);
          }
          else
          {
            message_die($text, $title);
          }
        }
        else
        {
            // We're better than before: no more "stop" on install/uninstall
            // I know, not the best way to submit the variables.. but better than GET...
            $betterMSG = "<html>
                            <head>
                              <title></title>
                              <script language='javascript'>
                                function MyFormSubmit(){
                                  document.installprogress.submit();
                                }
                              </script>
                            </head>
                            <body onload='MyFormSubmit()'>
                              <form action='manage_plugins.php' method='post' name='installprogress'>
                                <input type='hidden' name='progressChange' value='true' />
                                <input type='hidden' name='prog_message' value='".addslashes(sprintf($user->lang['plugin_inst_message'], $plugin_name, $installed_statusmsg))."' />
                              </form>
                          </body>
                          </html>";
            echo $betterMSG;
        }
    }

    // ---------------------------------------------------------
    // Registration methods
    // ---------------------------------------------------------

    /**
    * Register a plugin in the database
    *
    * @param $plugin_code
    * @return bool
    */
    function register($plugin_code)
    {
        global $db;

        $sql = "SELECT plugin_installed
                FROM __plugins
                WHERE `plugin_code` = '{$plugin_code}'";
        $registered = $db->query_first($sql);
        unset($sql);

        if ( ($registered != '0') && ($registered != '1') )
        {
            $plugin_path    = $this->get_data('path');
            $plugin_name    = $this->get_data('name');
            $plugin_contact = $this->get_data('contact');
            $plugin_version = $this->get_data('version');

            $query = $db->build_query('INSERT', array(
                'plugin_name'      => $plugin_name,
                'plugin_code'      => $plugin_code,
                'plugin_installed' => '0',
                'plugin_path'      => $plugin_path,
                'plugin_contact'   => $plugin_contact,
                'plugin_version'   => $plugin_version)
            );

            if ( !$db->query("INSERT INTO __plugins {$query}") )
            {
                return false;
            }
            unset($query, $plugin_path, $plugin_name, $plugin_contact, $plugin_version);
        }

        return true;
    }

    // ---------------------------------------------------------
    // Portal module methods
    // ---------------------------------------------------------

    /**
    * Add portal module for this plugin
    *
    * @param $module_name
    */
    function add_portal_module($module_name){
      $this->portal_modules[] = $module_name;
    }

    /**
    * Return portal module array for this object
    *
    * @return array
    */
    function get_portal_modules(){
      return $this->portal_modules;
    }
		
		/**
    * Add exchange module for this plugin
    *
    * @param $module_name
    */
    function add_exchange_module($module_name, $feed = false, $feed_url=''){
      if (!$feed){
				$this->exchange_modules[] = $module_name;
			} else {
				$this->exchange_feeds[] = array('name'	=> $module_name, 'url' => $feed_url);
			}
			
    }
		
		/**
    * Return exchange module array for this object
    *
    * @return array
    */
    function get_exchange_modules($feeds = false){
      if ($feeds) {
				return $this->exchange_feeds;
			} else {
				return $this->exchange_modules;
			}
			
    }
		

    // ---------------------------------------------------------
    // PDH read module methods
    // ---------------------------------------------------------

    /**
    * Add PDH read module for this plugin
    *
    * @param $module_name
    */
    function add_pdh_read_module($module_name){
      $this->pdh_read_modules[] = $module_name;
    }

    /**
    * Return PDH read module array for this object
    *
    * @return array
    */
    function get_pdh_read_modules(){
      return $this->pdh_read_modules;
    }

    // ---------------------------------------------------------
    // PDH write module methods
    // ---------------------------------------------------------

    /**
    * Add PDH write module for this plugin
    *
    * @param $module_name
    */
    function add_pdh_write_module($module_name){
      $this->pdh_write_modules[] = $module_name;
    }

    /**
    * Return PDH write module array for this object
    *
    * @return array
    */
    function get_pdh_write_modules(){
      return $this->pdh_write_modules;
    }

    // ---------------------------------------------------------
    // Permission methods
    // ---------------------------------------------------------

    /**
    * Add permission options for this plugin
    *
    * @param $auth_id
    * @param $auth_value
    * @param $auth_default
    * @param $text Text describing this permission (checkbox)
    */
    function add_permission($auth_type, $auth_value, $auth_default, $text, $groups='')
    {

				$auth_value = $auth_type.'_'.$this->get_data('code').'_'.$auth_value;
				$this->permissions[$auth_value] = array(
            'auth_value'   => $auth_value,
            'auth_default' => $auth_default,
            'text'         => $text,
						'groups'       => $groups,
						);
    }

    /**
    * Check if any permissions are set
    *
    * @return bool
    */
    function is_permissions()
    {
        return ( count($this->permissions) ) ? true : false;
    }

    /**
    * Return permission array for this object
    *
    * @return array
    */
    function get_permissions()
    {
        return $this->permissions;
    }

    /**
    * Add this object's permission options in the form of an
    * array to an existing permission boxes array
    *
    * @return array
    */
    function permission_boxes()
    {
        global $user, $eqdkp_root_path;

        $cbox_array = array();
				// Look for $user->lang['<code>_plugin'] - otherwise just use $user->lang['<code>']
				$code = $this->get_data('code');
				$cbox_group = ( isset($user->lang[$code . '_plugin']) ) ? $user->lang[$code . '_plugin'] : $user->lang[$code];	
				$admin_menu = $this->get_menu('admin_menu');
				$plugin_icon = ($admin_menu[$this->get_data('code')][$this->get_data('code')]['icon']) ? $eqdkp_root_path.'images/admin/'.$admin_menu[$this->get_data('code')][$this->get_data('code')]['icon'] : $eqdkp_root_path."images/admin/plugin.png";
				
				$cbox_group = '<img src="'.	$plugin_icon.'"> ' .$cbox_group ;

        foreach ( $this->permissions as $auth_id => $permissions )
        {
            // All plugin permissions are italic, a_* should be bold/italic
            $text = '<i>' . (( preg_match('/^a_/', $permissions['auth_value']) ) ? '<img src="'.$eqdkp_root_path.'images/admin/updates.png" alt="'.$user->lang['admin_right_icon_title'].'" title="'.$user->lang['admin_right_icon_title'].'"><b>' . $permissions['text'] . '</b>' : $permissions['text']) . '</i>';
						
            $cbox_array[$cbox_group][] = array(
                'CBNAME'    => $permissions['auth_value'],
                'TEXT'      => $text);
        }

        return $cbox_array;
    }


    // ---------------------------------------------------------
    // Menu methods
    // ---------------------------------------------------------

    /**
    * Add a link to a menu
    *
    * @param $menu_name
    * @param $menu_item
    */
    function add_menu($menu_name, $menu_item)
    {
        $this->menus[$menu_name][ $this->get_data('code') ] = $menu_item;
    }

    /**
    * Return a menu definition
    *
    * @param $menu_name
    */
    function get_menu($menu_name)
    {
        return ( isset($this->menus[$menu_name]) ) ? $this->menus[$menu_name] : false;
    }

    // ---------------------------------------------------------
    // Hook methods
    // ---------------------------------------------------------

    /**
    * Add a hook definition
    *
    * @param $page
    * @param $hook_name
    */
    function add_hook($page, $hook_name)
    {
        $this->hooks[$page] = $hook_name;
    }
		
		/**
    * Get hooks
    */
    function get_hooks()
    {
        return $this->hooks;
    }

    /**
    * Perform a hook call
    *
    * @param $s_page Page with the hook
		* @param $params Params from the page
    * @return array
    */
    function do_hook($s_page, $params = array())
    {
        $retval = array();
				$hook = $this->hooks[$s_page];
				
				if (strlen($hook) && method_exists($this, $hook)){
					$retval = $this->$hook($params);
				}


        return $retval;
    }

    // ---------------------------------------------------------
    // Data methods
    // ---------------------------------------------------------

    /**
    * Add plugin data to this object
    *
    * @param $type Data type (may be a relational array of type => data)
    * @param $data Data value
    * @return bool
    */
    function add_data($type, $data = '')
    {
        if ( is_array($type) )
        {
            foreach ( $type as $i_type => $data )
            {
                $this->add_data($i_type, $data);
            }
        }
        else
        {
            if ( in_array($type, $this->pm->valid_data_types) )
            {
                $this->data[$type] = $data;

                return true;
            }
            else
            {
                message_die('Invalid data type ("' . $type . '").', 'Data error');

                return false;
            }
        }
    }

    /**
    * Get plugin data for this object
    *
    * @param $type Data type
    */
    function get_data($type)
    {
        if ( in_array($type, $this->pm->valid_data_types) )
        {
            return ( isset($this->data[$type]) ) ? $this->data[$type] : false;
        }
        else
        {
            message_die('Invalid data type ("' . $type . '").', 'Data error');

            return false;
        }
    }

    // ---------------------------------------------------------
    // Dependency methods
    // ---------------------------------------------------------

    /**
    * Add plugin dependency to this object
    *
    * @param $type dependency type (may be a relational array of type => dependency)
    * @param $dependency dependency value
    * @return bool
    */
    function add_dependency($type, $dependency = '')
    {
        if ( is_array($type) )
        {
            foreach ( $type as $i_type => $dependency )
            {
                $this->add_dependency($i_type, $dependency);
            }
        }
        else
        {
            if ( in_array($type, $this->pm->valid_dependency_types) )
            {
                $this->dependency[$type] = $dependency;

                return true;
            }
            else
            {
                message_die('Invalid dependency type ("' . $type . '").', 'dependency error');

                return false;
            }
        }
    }

    /**
    * Get plugin dependency for this object
    *
    * @param $type dependency type
    */
    function get_dependency($type)
    {
        if ( in_array($type, $this->pm->valid_dependency_types) )
        {
            return ( isset($this->dependency[$type]) ) ? $this->dependency[$type] : false;
        }
        else
        {
            message_die('Invalid dependency type ("' . $type . '").', 'dependency error');

            return false;
        }
    }
    

    /***************************************
  	* Insert the config value into the DB  *
  	* @return --                           *
  	****************************************/
    function insertConfig($tablename, $tarray){
      foreach($tarray as $fieldname=>$insertvalue){
  		  $sql = "INSERT INTO __$tablename VALUES ('$fieldname', '$insertvalue');";
  		  $this->add_sql(SQL_INSTALL, $sql);
  		}
    }
}
?>