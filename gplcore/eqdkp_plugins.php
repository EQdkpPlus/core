<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * eqdkp_plugins.php
 * Began: Sun Mar 15 2003
 *
 * $Id$
 *
 ******************************/

if ( !defined('EQDKP_INC') ) {
    header('HTTP/1.0 404 Not Found');
    exit;
}

// Plugin states
define('PLUGIN_INITIALIZED', 1);
define('PLUGIN_REGISTERED',  2);
define('PLUGIN_INSTALLED',   4);
define('PLUGIN_UNINSTALLED', 8);
define('PLUGIN_DISABLED', 	 16);
define('PLUGIN_ALL', PLUGIN_INITIALIZED
                     | PLUGIN_REGISTERED 
                     | PLUGIN_INSTALLED 
                     | PLUGIN_UNINSTALLED 
                     | PLUGIN_DISABLED);

// SQL types
define('SQL_INSTALL',   1);
define('SQL_UNINSTALL', 2);

/**
* EQdkp Plugin Manager class
* Framework for all plugins
* Available to every page as $pm
*/

class EQdkp_Plugin_Manager {
    var $registered  = array();             // Registered plugins       @var registered
    var $installed   = array();             // Installed plugin         @var installed
    var $uninstalled = array();             // Uninstalled plugins      @var uninstalled

    var $plugins     = array();             // Plugin objects           @var plugins
		var $hooks 			 = array();

    var $errors      = array();             // Error messages           @var errors
    var $error_die   = true;                // Die on errors?           @var error_die
    var $debug       = false;               // Debug?                   @var debug

    var $valid_data_types = array(
                                'id',
                                'code',
                                'name', 
                                'installed',
                                'contact', 
                                'path', 
                                'template_path',
                                'version', 
                                'description', 
                                'long_description',
                                'manuallink', 
                                'homepage', 
                                'imageurl',
                                'author', 
                                'plus_version', 
                                'build',
                                'icon'
                              );

    var $valid_dependency_types = array(
                                    'plus_version',
                                    'lib_version',
                                    'games',
                                    'php_functions'
                                  );

    var $plugin_db_values = array();
    
    // ---------------------------------------------------------
    // Core methods
    // ---------------------------------------------------------

    /**
    * Constructor
    * Populate the registered, installed and uninstalled arrays, initializing
    *   any installed plugins
    *
    * @param    bool    $error_die Die on error messages?
    * @param    bool    $debug Output debug messages?
    */
    public function __construct($error_die = false, $debug = false)
    {
        global $db;

        $this->error_die = $error_die;
        $this->debug = ( $debug ) ? true : false;

        // Populate arrays of registered/installed/uninstalled plugins
        $sql = "SELECT plugin_code, plugin_path, plugin_installed, plugin_version, plugin_build 
                FROM __plugins
                ORDER BY plugin_name";
        $result = $db->query($sql);

        while ( $row = $db->fetch_record($result) )
        {
            $plugin_code = $row['plugin_code'];
            $plugin_path = $row['plugin_path'];

            // Add this plugin's code to our registered list
            $this->registered[$plugin_code] = $plugin_code;
            
            // Add plugin db version to our db version list
            $this->plugin_db_values[$plugin_code]['plugin_version'] = $row['plugin_version'];
            $this->plugin_db_values[$plugin_code]['plugin_build'] = $row['plugin_build'];
            
            // Add the plugin code to either the installed or uninstalled list
            if ( $row['plugin_installed'] == 1 )
            {
                $this->installed[$plugin_code] = $plugin_code;

                // Attempt to initialize the installed plugin
                if ( !$this->initialize($plugin_code, $plugin_path) )
                {
                    $this->error_append('Plugin Manager Instantiation', $plugin_code .' is installed but could not be initialized.', true);
                }else{
                    $this->register_pdh_modules($plugin_code, $plugin_path);
										$this->register_exchange_modules($plugin_code, $plugin_path);
										$this->register_exchange_feeds($plugin_code, $plugin_path);
                    if(method_exists($this->plugins[$plugin_code], 'autorun')){
                      $this->plugins[$plugin_code]->autorun();
                    }
										$this->register_hooks($plugin_code);
                }
            }
            else
            {
                $this->uninstalled[$plugin_code] = $plugin_code;
            }
        }
        $db->free_result($result);

        // Output any errors that may have occurred
        $this->error_check(true);
    }

    /**
    * Perform a check on $plugin_code
    *
    * @param    int     $check PLUGIN_INITIALIZED | PLUGIN_REGISTERED | PLUGIN_INSTALLED | PLUGIN_UNINSTALLED
    * @param    string  $plugin_code
    * @return bool
    */
    function check($check, $plugin_code)
    {
        $retval = false;

        if ( is_null($plugin_code) || empty($plugin_code) || !is_string($plugin_code) )
        {
            $retval = false;
        }
        else
        {
        switch ( $check )
        {
            case PLUGIN_INITIALIZED:
                    $retval = isset($this->plugins[$plugin_code]);
                break;

            case PLUGIN_REGISTERED:
                    $retval = isset($this->registered[$plugin_code]);
                break;

            case PLUGIN_INSTALLED:
                    $retval = isset($this->installed[$plugin_code]);
                break;

            case PLUGIN_UNINSTALLED:
                    $retval = isset($this->uninstalled[$plugin_code]);
                break;

            case PLUGIN_DISABLED:
            		$dsoutput = (isset($this->plugins[$plugin_code]) && isset($this->uninstalled[$plugin_code])) ? true : false;
                $retval = ( is_null($plugin_code) ) ? false : $dsoutput;
                break;

            default:
                $retval = false;
        }
        }
        return $retval;
    }

    // ---------------------------------------------------------
    // Enable / Disable / Initialize / Register / Install / Uninstall / Activate methods
    // ---------------------------------------------------------

		 /**
    * Attempt to enable a plugin
    *
    * @param    string  $plugin_code Plugin to initialize
    * @param    string  $plugin_path Optional, directory where the plugin resides
    * @return   bool
    */
    function enable($plugin_code){

		}
    
    function disable($plugin_code){
    }
    
    /**
    * Attempt to initialize a plugin
    *
    * @param    string  $plugin_code Plugin to initialize
    * @param    string  $plugin_path Optional, directory where the plugin resides
    * @return   bool
    */
    function initialize($plugin_code, $plugin_path = '')
    {
        global $db, $eqdkp_root_path;

        // Prevent re-initializing the same plugin
        if ( $this->check(PLUGIN_INITIALIZED, $plugin_code) )
        {
            return false;
        }

        $plugin_path = ( !empty($plugin_path) ) ? $plugin_path : $plugin_code;
        $plugin_dir  = $eqdkp_root_path . 'plugins/' . $plugin_path . '/';

        if ( !is_dir($plugin_dir) )
        {
            // Couldn't find this plugin's directory
            //$this->error_append('Initialization', 'Directory "' . $plugin_dir . '" does not exist.', true);

            // Directory doesn't exist, we'll never get this plugin inititalized
            // Remove its entry from the database
            $sql = "DELETE FROM __plugins
                    WHERE `plugin_code` = '{$plugin_code}'";
            $db->query($sql);
        }
        else
        {
            // Search for a file named <code>_plugin_class.php containing a class named <code>_Plugin_Class
            $plugin_class      = $plugin_code . '_Plugin_Class';
            $plugin_class_file = $plugin_dir . strtolower($plugin_class) . '.php';

            if ( !is_file($plugin_class_file) )
            {
                // Plugin's class file doesn't exist
                //$this->error_append('Initialization', 'File "' . $plugin_class_file . '" does not exist.', true);

                // Plugin class file doesn't exist, remove its entry from the database
                $sql = "DELETE FROM __plugins
                        WHERE `plugin_code` = '{$plugin_code}'";
                $db->query($sql);
								
								//Check if it's a portal module
								if (is_file($plugin_dir.'module.php')){
									$this->error_append('Common Error', '"' . $plugin_code . '" is NOT a Plugin, but a Portal-Module. Move the '.$plugin_code.'-Folder from the plugin-Folder to the portal-Folder.', true);
								}
								
            }
            else
            {
                include_once($plugin_class_file);

                if ( !class_exists($plugin_class) )
                {
                    // Plugin's class definition does not exist
                    $this->error_append('Initialization', 'Class "' . $plugin_class . '" does not exist.', true);
                }
                else
                {
                    $plugin_object = new $plugin_class($this);

                    if ( !is_object($plugin_object) )
                    {
                        // Plugin's class failed to instantiate
                        $this->error_append('Initialization', 'Class "' . $plugin_class . '" failed to instantiate.', true);
                    }
                    else
                    {

                        $this->plugins[$plugin_code] = $plugin_object;
                    }
                } // class_exists
            } // is_file
        } // is_dir
        unset($plugin_object, $plugin_class, $plugin_class_file, $plugin_path, $plugin_dir);
        if ( !$this->check(PLUGIN_INITIALIZED, $plugin_code) )
        {
            // Initialization failed				
            $this->error_append('Initialization', 'Plugin "' . $plugin_code . '" could not be initialized.', true);
        }
        
        $this->error_check(true);

        // If we get to here, there were no errors (since error_check didn't kill our output)
        return true;
    }

    /**
    * Register a specific plugin, or all available plugins
    *
    * @param $s_plugin_code
    * @return bool
    */
    function register($s_plugin_code = '')
    {
        global $eqdkp_root_path;

        // s_plugin_code - as in static, this plugin_code doesn't change
        // d_plugin_code - as in dynamic, this will change with each iteration of readdir()

        // Prevent re-registering the same plugin
        if ( $this->check(PLUGIN_REGISTERED, $s_plugin_code) )
        {
            return true;
        }

        // Search for plugins and make sure they are registered
        if ( $dir = @opendir($eqdkp_root_path . 'plugins/') )
        {
            while ( $d_plugin_code = @readdir($dir) )
            {
                $cwd = $eqdkp_root_path . 'plugins/' . $d_plugin_code;
                if ( valid_folder($cwd) && substr($d_plugin_code, 0, 1) != '_' )
                {
                    // If $d_plugin_code is in our array of registered codes,
                    // continue with the next iteration of the while loop
                    if ( in_array($d_plugin_code, $this->registered) )
                    {
                        continue;
                    }
                    else
                    {
                        // If $s_plugin_code is defined, only register that plugin
                        // Otherwise, register all unregistered plugins
                        if ( (!empty($s_plugin_code)) && ($s_plugin_code != $d_plugin_code) )
                        {
                            // Break out of the loop if $s_plugin_code is defined
                            // and this directory doesn't belong to $s_plugin_code
                            continue;
                        }
                        else
                        {
                            if ( !$this->initialize($d_plugin_code) )
                            {
                                // Couldn't initialize
                                $this->error_append('Registration', 'Plugin "' . $d_plugin_code . '" could not be initialized.', true);
                            }
                            else
                            {
                                // Get the plugin's object
                                $plugin_object = $this->get_plugin($d_plugin_code);

                                if ( !is_object($plugin_object) )
                                {
                                    // Object couldn't be instantiated
                                    $this->error_append('Registration', 'Plugin class for "' . $d_plugin_code . '" could not be initialized.', true);
                                }
                                else
                                {
                                    // Make the plugin register itself
                                    $plugin_object->register($d_plugin_code);

                                    // Update the plugin manager arrays
                                    $this->registered[$d_plugin_code] = $d_plugin_code;
                                    $this->uninstalled[$d_plugin_code] = $d_plugin_code;
                                }
                            }
                        }
                    }
                } // is directory
                unset($plugin_object, $d_plugin_code, $cwd);
            } // readdir
        } // opendir
        else
        {
            $this->error_append('Registration', 'Call to fopen() failed. Could not access plugins directory.');
        }

        unset($dir);

        // If a specific plugin was given, check to make sure it was registered
        if ( !empty($s_plugin_code) )
        {
            if ( !$this->check(PLUGIN_REGISTERED, $plugin_code) )
            {
                // Initialization failed
                $this->error_append('Registration', 'Plugin "' . $plugin_code . '" could not be registered.', true);
            }
        }
        $this->error_check(true);

        return true;
    }

    /**
    * Install a plugin
    *
    * @param $plugin_code
    * @return bool
    */
    function install($plugin_code)
    {
    global $user;//, $db;

        // Prevent re-installing this plugin
        if ( $this->check(PLUGIN_INSTALLED, $plugin_code) )
        {
            return true;
        }

        // Initialize the plugin - initialize() prevents this if it needs to
        $this->initialize($plugin_code);

        // Get the plugin object
        $plugin_object = $this->get_plugin($plugin_code);
        if ( !is_object($plugin_object) )
        {
            $this->error_append('Instantiation', 'Plugin "' . $plugin_code . '" failed to create a valid object.', true);
        }
        else
        {
            $plugin_path = $plugin_object->get_data('path');
            if(empty($plugin_path))
              $plugin_path = $plugin_code;
              
            $this->register_pdh_modules($plugin_code, $plugin_path);
            //if there is a pre install method, call it
            if ( method_exists($plugin_object, 'pre_install') ) {
              $plugin_object->pre_install();
            }
            // Run the install
            $sql_install_return_value = $plugin_object->run_sql(SQL_INSTALL);
            if ( is_array($sql_install_return_value) && !($sql_install_return_value[0]) )
            {
                $this->error_append('Installation', 'Plugin "' . $plugin_code . '" installation SQL failed.', true);
                $this->error_append('Installation', 'Error in SQL command: <br>"'.substr($sql_install_return_value[1], 0, -6) . '".', true);
                $this->error_append('Note', $user->lang['plugin_inst_sql_note'], true);
            }
            else
            {
                // Remove this item from our uninstalled array
                unset($this->uninstalled[$plugin_code]);

                // Add it to our installed array
                $this->installed[$plugin_code] = $plugin_code;
            }
        }

        if ( !$this->check(PLUGIN_INSTALLED, $plugin_code) )
        {
            $this->error_append('Installation', 'Plugin "' . $plugin_code . '" failed to install.', true);
            $this->unregister_pdh_modules($plugin_code);
        }else{
          //if there is a post install method, call it
          if ( method_exists($plugin_object, 'post_install') ) {
            $plugin_object->post_install();
          }
        }
        
        $this->error_check(true);
        return true;
    }

    /**
    * Uninstall a plugin
    *
    * @param $plugin_code
    * @return bool
    */
    function uninstall($plugin_code)
    {
        global $pcomments;
				if ( $this->check(PLUGIN_UNINSTALLED, $plugin_code) )
        {
            return true;
        }
				
				//Delete Comments
				$pcomments->Uninstall($plugin_code);
				
        $this->initialize($plugin_code);

        // Get the plugin object
        $plugin_object = $this->get_plugin($plugin_code);
        if ( !is_object($plugin_object) )
        {
            $this->error_append('Instantiation', 'Plugin "' . $plugin_code . '" failed to create a valid object.', true);
        }
        else
        {
            //if there is a pre uninstall method, call it
            if ( method_exists($plugin_object, 'pre_uninstall') ) {
              $plugin_object->pre_uninstall();
            }
            // Run the uninstall
            if ( !$plugin_object->run_sql(SQL_UNINSTALL) )
            {
                $this->error_append('Uninstallation', 'Plugin "' . $plugin_code . '" uninstallation SQL failed.', true);
            }
            else
            {
                // Remove this item from our installed array
                unset($this->installed[$plugin_code]);

                // Add it to our uninstalled array
                $this->uninstalled[$plugin_code] = $plugin_code;
            }
        }

        if ( !$this->check(PLUGIN_UNINSTALLED, $plugin_code) )
        {
            $this->error_append('Uninstallation', 'Plugin "' . $plugin_code . '" failed to uninstall.', true);
        }else{
            //if there is a pre uninstall function, call it
            if ( method_exists($plugin_object, 'post_uninstall') ) {
              $plugin_object->post_uninstall();
            }
        }

        $this->error_check(true);

        return true;
    }

    // ---------------------------------------------------------
    // Plugin methods
    // ---------------------------------------------------------
   
    /**
    * Return $plugin_code's associated object
    *
    * @param $plugin_code
    * @return mixed Object / false
    */
    function get_plugin($plugin_code)
    {
        return ( @is_object($this->plugins[$plugin_code]) ) ? $this->plugins[$plugin_code] : false;
    }

    /**
    * Get multiple plugins
    *
    * @param $filter Type of plugins to get (can be |'d together using the PLUGIN_x constants)
    * @return array Associative array of plugin objects as code => object
    */
    function get_plugins($filter = PLUGIN_INSTALLED)
    {
        $retval = array();
        $unset_array = array();

        // Get initialized plugins
        if ( $filter & PLUGIN_INITIALIZED )
        {
            $retval = array_merge($retval, $this->plugins);
        }

        // Get installed plugins
        if ( $filter & PLUGIN_INSTALLED )
        {
            foreach ( $this->installed as $plugin_code )
            {
                // Initialize the plugin if it's not already
                if ( !$this->check(PLUGIN_INITIALIZED, $plugin_code) )
                {
                    $this->initialize($plugin_code);
                }

                $retval[$plugin_code] = $this->get_plugin($plugin_code);
            }
        }

        // Get uninstalled plugins
        if ( $filter & PLUGIN_UNINSTALLED )
        {
            foreach ( $this->uninstalled as $plugin_code )
            {
                if ( !$this->check(PLUGIN_INITIALIZED, $plugin_code) )
                {
                    $this->initialize($plugin_code);
                    $unset_array[] = $plugin_code;
                }

                $retval[$plugin_code] = $this->get_plugin($plugin_code);
            }
        }

        // Unset any plugins that may have been initialized just for this purpose
        foreach ( $unset_array as $plugin_code )
        {
            unset($this->plugins[$plugin_code]);
        }

        return $retval;
    }

    /**
    * Get a plugin's language file
    *
    * @param $plugin_code Either a specific plugin, or 'all'
    * @return bool
    */
    function get_language_pack($plugin_code = 'all')
    {
        global $eqdkp_root_path, $user;

        // Recursive if we're getting the language packs for every plugin
        if ( $plugin_code == 'all' )
        {
            foreach ( $this->installed as $plugin_code )
            {
                $this->get_language_pack($plugin_code);
            }
        }
        else
        {
            $lang_file = $eqdkp_root_path . 'plugins/' . $plugin_code . '/language/' . $user->lang_name . '/lang_main.php';

            if ( file_exists($lang_file) )
            {
                include_once($lang_file);

                $user->lang = ( @is_array($lang) ) ? array_merge($user->lang, $lang) : $user->lang;
		        //Add english language fallback code | sz3
            }else{
                $lang_file = $eqdkp_root_path . 'plugins/' . $plugin_code . '/language/english/lang_main.php';
                if ( file_exists($lang_file) ){
                    include_once($lang_file);
                    $user->lang = ( @is_array($lang) ) ? array_merge($user->lang, $lang) : $user->lang;
		            }//end of addition
           }
        }
        return true;
    }

    // ---------------------------------------------------------
    // Hook methods
    // ---------------------------------------------------------

    /**
    * Add plugins' permission box arrays to an existing permissions array
    * Modifies the array by reference
    *
    * @param $cbox_array Array we're modifying
    * @return bool
    */
    function generate_permission_boxes(&$cbox_array)
    {
        global $user;

        foreach ( $this->get_plugins(PLUGIN_INSTALLED) as $plugin_code => $plugin_object )
        {
            if ( $plugin_object->is_permissions() )
            {
                $cbox_array = array_merge($cbox_array, $plugin_object->permission_boxes());
            }
        }

        return true;
    }

    /**
    * Call hooks for a specific page
    *
    * @param $page
    * @return mixed Result of <plugin_object>::do_hook()
    */
    function do_hooks($page, $params = array())
    {
        global $eqdkp_root_path;

        $retval = array();

        $page = $this->shorten_hook_page($page);
				
        foreach ( $this->get_plugins() as $plugin_code => $plugin_object )
        {
            $retval[$plugin_code] = $plugin_object->do_hook($page, $params);
        }

        return $retval;
    }
		
		function do_hook($page, $plugin_code, $params = array()){
			$plugin_object = $this->get_plugin($plugin_code);
			
			$page = $this->shorten_hook_page($page);
			
			if ($plugin_object){
				$retval = $plugin_object->do_hook($page, $params);
				return $retval;
			}
			
			return false;
		}

		function shorten_hook_page($page){
			$request = ( isset($_SERVER['REQUEST_URI']) ) ? $_SERVER['REQUEST_URI'] : $_SERVER['SCRIPT_NAME'] . (( isset($_SERVER['QUERY_STRING']) ) ? '?' . $_SERVER['QUERY_STRING'] : '');

        // If we've been handed a raw URL string, shorten it appropriately
        if ( $request == $page )
        {
            $slash_count = substr_count($eqdkp_root_path, '/');
            $url = split('/', $request);
            $url = array_slice($url, -$count);
            $page = join('/', $url);
        }
				
				return $page;
		}

		function register_hooks($plugin_code){
				$hooks = $this->plugins[$plugin_code]->get_hooks();
				foreach ($hooks as $page => $function){
						$this->hooks[$page][$plugin_code] = $function;
				}
		}

    /**
    * Return log actions
    *
    * @return array
    */
    function get_log_actions()
    {
        $retval = array();

        foreach ( $this->get_plugins() as $plugin_code => $plugin_object )
        {
            foreach ( $plugin_object->get_log_actions() as $action_type => $lang_string )
            {
                $retval[$action_type] = $lang_string;
            }
        }

        return $retval;
    }

    /**
    * Generate a menu
    *
    * @param $menu_name
    * @deprec Array format removes the need for this
    * @return string Menu
    */
    function generate_menu($menu_name)
    {
        $menu_array = array();

        foreach ( $this->get_plugins(PLUGIN_INSTALLED) as $plugin_code => $plugin_object )
        {
            $menu_array = array_merge($menu_array, $plugin_object->get_menu($menu_name));
        }

        $menu_string = '';

        foreach ( $menu_array as $plugin_code => $menu )
        {
            if ( ($menu != '') && ($this->check(PLUGIN_INSTALLED, $plugin_code)) )
            {
                $menu_string .= ' | ' . $menu;
            }
        }

        return $menu_string;
    }

    /**
    * Get a menu array
    *
    * @param $menu_name
    * @return array
    */
    function get_menus($menu_name = 'admin_menu')
    {
        $menu_array = array();

        foreach ( $this->get_plugins(PLUGIN_INSTALLED) as $plugin_code => $plugin_object )
        {
            $plugin_array = $plugin_object->get_menu($menu_name);
            $menu_array = (is_array($plugin_array[$plugin_code])) ? array_merge($menu_array, $plugin_array[$plugin_code]) : $menu_array;
        }

        return $menu_array;
    }

    // ---------------------------------------------------------
    // Error methods
    // ---------------------------------------------------------

    /**
    * Add an error message to the error buffer
    *
    * @param $location Part of the class the error occurred in
    * @param $error Error message
    * @param $force Add this message regardless of $this->debug?
    * @return bool
    */
    function error_append($location, $error, $force = false)
    {
        if ( ($this->debug) || ($force) )
        {
            $this->errors[] = '<b>' . $location . ':</b> ' . $error;

            return true;
        }

        return false;
    }

    /**
    * Check to see if errors exist in the queue
    *
    * @return bool
    */
    function is_error()
    {
        return ( sizeof($this->errors) > 0 ) ? true : false;
    }

    /**
    * Output error messages if we need to
    *
    * @param $die Can override $this->error_die
    * @return bool
    */
    function error_check($die = false)
    {
        if ( ($this->is_error()) && (($this->error_die) || ($die)) )
        {
            $message = '<b>The following error(s) occurred:</b><br />';
            foreach ( $this->errors as $error )
            {
                $message .= $error . '<br />';
            }
            message_die("$message");
        }
        else
        {
            // Wipe the error buffer
            $this->errors = array();

            return false;
        }
    }

    // ---------------------------------------------------------
    // Data methods
    // ---------------------------------------------------------

    /**
    * Add plugin data to $plugin_code
    *
    * @param $plugin_code
    * @param $type Data type (may be a relational array of type => data)
    * @param $data Data value
    * @return bool
    */
    function add_data($plugin_code, $type, $data = '')
    {
        if ( $this->check(PLUGIN_INITIALIZED, $plugin_code) )
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
                if ( in_array($type, $this->valid_data_types) )
                {
                    $plugin_object = $this->plugins[$plugin_code];

                    if ( is_object($plugin_object) )
                    {
                        $plugin_object->add_data($type, $data);
                    }

                    return true;
                }
            }
        }
        $this->error_append('Add Data', 'Invalid data type ("' . $type . '").');

        return false;
    }

    /**
    * Get plugin data for $plugin_code
    * @param $plugin_code
    * @param $type Data type
    */
    function get_data($plugin_code, $type)
    {
        if ( $this->check(PLUGIN_INITIALIZED, $plugin_code) )
        {
            if ( in_array($type, $this->valid_data_types) )
            {
                $plugin_object = $this->plugins[$plugin_code];

                if ( is_object($plugin_object) )
                {
                    return $plugin_object->get_data($type);
                }
            }
        }
        $this->error_append('Get Data', 'Invalid data type ("' . $type . '").');

        return false;
    }
		
    function get_db_info($plugin_code, $info){
      switch($info){
        case 'plugin_version':
                          return (!empty( $this->plugin_db_values[$plugin_code]['plugin_version'])) ?  $this->plugin_db_values[$plugin_code]['plugin_version'] : false;
                          break;
        case 'plugin_build':
                          return $this->plugin_db_values[$plugin_code]['plugin_build'];
                          break;
        default:
              return false;
      }
    }
    
    function set_db_info($plugin_code, $info, $value){
    global $db;
      switch($info){
        case 'plugin_version':
                          break;
        case 'plugin_build':
                          $value = ($value == false) ? '' : $value;
                          break;
        default:
              return false;
      }
      
      $sql = "UPDATE __plugins
        SET " . $info . " = '" . $value . "'
        WHERE plugin_code='" . $plugin_code . "'";
      return $db->query($sql);        
    }
		
		function register_exchange_modules($plugin_code, $plugin_path){
			global $pex, $eqdkp_root_path;
			$em = $this->plugins[$plugin_code]->get_exchange_modules();
			foreach($em as $module_name){
        $module_dir  = 'plugins/' . $plugin_path . '/exchange/'.$module_name;
        $pex->register_module($module_name, $module_dir);
      }
		}
		
		function register_exchange_feeds($plugin_code, $plugin_path){
			global $pex, $eqdkp_root_path;
			$em = $this->plugins[$plugin_code]->get_exchange_modules(true);
			foreach($em as $module){
        $pex->register_feed($module['name'], $module['url'], $plugin_code);
      }
		}

    // ---------------------------------------------------------
    // PDH method
    // ---------------------------------------------------------
    
    function register_pdh_modules($plugin_code, $plugin_path){
    global $pdh, $eqdkp_root_path;
      //register pdh read modules
      $rm = $this->plugins[$plugin_code]->get_pdh_read_modules();
      foreach($rm as $module_name){
        $module_dir  = $eqdkp_root_path . 'plugins/' . $plugin_path . '/pdh/read/'.$module_name;
        $pdh->register_read_module($module_name, $module_dir);
      }
      //register pdh write modules
      $wm = $this->plugins[$plugin_code]->get_pdh_write_modules();
      foreach($wm as $module_name){
        $module_dir  = $eqdkp_root_path . 'plugins/' . $plugin_path . '/pdh/write/'.$module_name;
        $pdh->register_write_module($module_name, $module_dir);
      }
    }
    
    function unregister_pdh_modules($plugin_code){
    global $pdh;
      //unregister pdh read modules
      $rm = $this->plugins[$plugin_code]->get_pdh_read_modules();
      foreach($rm as $module_name){
        $pdh->unregister_read_module($module_name);
      }
      //unregister pdh write modules
      $wm = $this->plugins[$plugin_code]->get_pdh_write_modules();
      foreach($wm as $module_name){
        $pdh->unregister_write_module($module_name);
      }
    }

    // ---------------------------------------------------------
    // Dependency methods
    // ---------------------------------------------------------
    /**
    * Check plugin dependencies for $plugin_code
    * @param $plugin_code
    * @param $dependency
    */
    function check_dependency($plugin_code, $dependency)
    {
        if ( $this->check(PLUGIN_INITIALIZED, $plugin_code) )
        {
            if ( !in_array($dependency, $this->valid_dependency_types) )
            {
                $this->error_append('Check dependency', 'Invalid dependency type ("' . $dependency . '").');
                return false;
            }

            $plugin_object = $this->plugins[$plugin_code];

            if ( is_object($plugin_object) )
            {
                //plugin might have its own dependency check function
                if( method_exists ($plugin_object, 'check_dependency') )
                {
                  return $plugin_object->check_dependency($dependency);
                }
                //or maybe not, call defaults
                else
                {
                  $deps = $plugin_object->get_dependency($dependency);
                  //dependency not set
                  if( $deps == false )
                  {
                    return true;
                  }
                  
                  switch ($dependency){
                      case 'plus_version':  
                        global $core;
                        $check_result = compareVersion($core->config['plus_version'], $deps, '>=');
                        $check_result = ( $check_result >= 0 ) ? true : false;
                        break;
                      
                      case 'lib_version':  
                        global $libloader;
                        $check_result = compareVersion($libloader->get_version(), $deps);
                        $check_result = ( $check_result >= 0 ) ? true : false;
                        break;
                      
                      case 'games':
                        global $core;
                        $check_result = in_array($core->config['default_game'], $deps);
                        break;
                      case 'php_functions':
                        foreach($deps as $function){
                          $check_result = function_exists($function);
                          if($check_result == false)
                            break;
                        }
                        break;
                      default:
                        $check_result = true;
                  }
                  return $check_result;
                }
            }
        }

        return false;
    }
}
?>