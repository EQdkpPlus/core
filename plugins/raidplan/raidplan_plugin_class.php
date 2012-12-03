<?php
/******************************
 * EQdkp Raid Planner
 * Copyright 2005 by A.Stranger
 * Continued 2006 by Urox and Wallenium 
 * ------------------
 * config.php
 * Began: Tue June 1, 2006
 * Changed: Tue June 1, 2006
 * 
 ******************************/
if ( !defined('EQDKP_INC') )
{
    die('You cannot access this file directly.');
}

// Set table names
global $table_prefix;
if (!defined('RP_RAIDS_TABLE')) { define('RP_RAIDS_TABLE', $table_prefix . 'raidplan_raids'); }
if (!defined('RP_CLASSES_TABLE')) { define('RP_CLASSES_TABLE', $table_prefix . 'raidplan_raid_classes'); }
if (!defined('RP_ATTENDEES_TABLE')) { define('RP_ATTENDEES_TABLE', $table_prefix . 'raidplan_raid_attendees'); }
if (!defined('RP_WILDCARD_TABLE')) { define('RP_WILDCARD_TABLE', $table_prefix . 'raidplan_wildcards'); }

class raidplan_Plugin_Class extends EQdkp_Plugin
{
    function raidplan_plugin_class($pm)
    {
        global $eqdkp_root_path, $user, $SID;
        
		// Call our parent's constructor
        $this->eqdkp_plugin($pm);
		
		// Get language pack
        $this->pm->get_language_pack('raidplan');

		// Data for this plugin
        $this->add_data(array(
            'name'					=> 'Raid Planner',
            'code'					=> 'raidplan',
            'path'					=> 'raidplan',
            'contact'				=> 'maverick@gmx.com',
            'template_path'	=> 'plugins/raidplan/templates/',
            'version'				=> '2.02')
        );
        
		// Register our permissions
        $this->add_permission('501', 'a_raidplan_add',    'N', $user->lang['add']);
        $this->add_permission('502', 'a_raidplan_update', 'N', $user->lang['update']);
        $this->add_permission('503', 'a_raidplan_delete', 'N', $user->lang['delete']);
        $this->add_permission('506', 'a_raidplan_config', 'N', $user->lang['config']);
        $this->add_permission('504', 'u_raidplan_list',   'N', $user->lang['list']);
        $this->add_permission('505', 'u_raidplan_view',   'N', $user->lang['view']);
       
		
        // Add Menus
		$this->add_menu('main_menu1', $this->gen_main_menu1());
		$this->add_menu('admin_menu', $this->gen_admin_menu());
		
		// Add Hooks
		$this->add_hook('/admin/manage_users.php?action=settings', 'UserSettingHook');
		$this->add_hook('/admin/manage_users.php?action=update', 'UserUpdateHook');

        // Define installation
        // -----------------------------------------------------
		$steps=6;
		for ($i = 1; $i <= $steps; $i++)
		{
			$this->add_sql(SQL_INSTALL, $this->create_raidplan_tables("step".$i."a"));
			$this->add_sql(SQL_INSTALL, $this->create_raidplan_tables("step".$i."b"));
		}	
		
		// Installation of Settings
		$this->InsertIntoTable('rp_show_ranks', '1'); # Show ranks in raid planner?
		$this->InsertIntoTable('rp_short_rank', '1'); # Show only short ranks?
		$this->InsertIntoTable('rp_send_email', '0'); # Email raids to all users
		$this->InsertIntoTable('rp_roll_systm', '1'); # Should we use the roll-system?
		$this->InsertIntoTable('rp_wildcard', '1');   # Should we use the wildcard-system?
		$this->InsertIntoTable('rp_use_css', '1');    # Should we add the .css file in the plugin's template folder?
		$this->InsertIntoTable('rp_last_days', '7');  # show recent raids: last x days
		$this->InsertIntoTable('rp_max_status', '7'); # Max Status show (Min 2 Max 4)
		$this->InsertIntoTable('rp_auto_hash', 'dgt_is_kewl');  # Autojoin Secret Hash
		$this->InsertIntoTable('rp_auto_path', './lua_dl/');  # Autojoin Secret Path

        // Define uninstallation
        // -----------------------------------------------------
		$steps=6;
		for ($i = 1; $i <= $steps; $i++)
		{
			$this->add_sql(SQL_UNINSTALL, $this->create_raidplan_tables("step".$i."a"));
		}	
}

    /**
	* Generate raidplan menus
	*
	* @return array
	*/
    function gen_main_menu1()
    {
		global $user, $SID, $eqdkp_root_path;
		
        if ($this->pm->check(PLUGIN_INSTALLED, 'raidplan') && $user->check_auth('u_raidplan_', false))
        {
            global $db, $user;

            $main_menu1 = array(
                array(
					'link' => 'plugins/' . $this->get_data('path') . '/listraids.php' . $SID, 
					'text' => $user->lang['rp_usermenu_raidplaner'],
					'check' => 'u_raid_list'
				)
            );

            return $main_menu1;
        }
        return;
    }

    /**
	* Generate raidplan admin menu
	*
	* @return array
	*/
    function gen_admin_menu()
    {
		global $user, $SID, $eqdkp;
		$dkpurl = "http://". trim($eqdkp->config['server_name'], "\\/"). "/" .trim($eqdkp->config['server_path'], "\\/"). "/";
		
        if ($this->pm->check(PLUGIN_INSTALLED, 'raidplan') && $user->check_auth('a_raidplan_', false))
        {
            global $db, $user, $eqdkp_root_path;
						
			$admin_menu = array(
				'raidplan' => array(
					0 => $user->lang['raidplan'],
					1 => array(
						'link' => $dkpurl . 'plugins/raidplan/admin/addraid.php' . $SID,
						'text' => $user->lang['add'],
						'check' => 'a_raidplan_'),
					2 => array(
						'link' => $dkpurl . 'plugins/raidplan/admin/index.php' . $SID,
						'text' => $user->lang['list'],
						'check' => 'a_raidplan_'),
					3 => array(
						'link' => $dkpurl . 'plugins/raidplan/admin/settings.php' . $SID,
						'text' => $user->lang['settings'],
						'check' => 'a_raidplan_')
				)
			);

            return $admin_menu;
        }
        return;
    }

  /**
	* Get SQL to create the roster table
	*
	* @return string Table creation SQL
	*/
	function create_raidplan_tables($step)
	{
		global $table_prefix;
		$sql = "";
		switch ($step)
		{
			case "step1a":
				$sql = "DROP TABLE IF EXISTS " . $table_prefix . "raidplan_raids";
				break;
			case "step1b":
				$sql = "CREATE TABLE IF NOT EXISTS " . $table_prefix . "raidplan_raids (
						raid_id mediumint(8) unsigned NOT NULL auto_increment,
						raid_name varchar(255) default NULL,
						raid_date int(11) NOT NULL default '0',
						raid_date_invite int(11) NOT NULL default '0',
						raid_date_subscription int(11) NOT NULL default '0',
						raid_note text default NULL,
						raid_value float(6,2) default NULL,
						raid_attendees mediumint(8) NOT NULL default '0',
						raid_added_by varchar(30) NOT NULL default '',
						raid_updated_by varchar(30) default NULL,
						PRIMARY KEY  (raid_id)
						) ";
				break;
			case "step2a":
				$sql = "DROP TABLE IF EXISTS " . $table_prefix . "raidplan_raid_classes";
				break;
			case "step2b":
				$sql = "CREATE TABLE IF NOT EXISTS " . $table_prefix . "raidplan_raid_classes (
						raid_id mediumint(8) unsigned NOT NULL default '0',
						class_name varchar(50) default NULL,
						class_count smallint(3) unsigned NOT NULL default '0',
						KEY raid_id (raid_id)
						)";
				break;
			case "step3a":
				$sql = "DROP TABLE IF EXISTS " . $table_prefix . "raidplan_raid_attendees";
				break;
			case "step3b":
				$sql = "CREATE TABLE IF NOT EXISTS " . $table_prefix . "raidplan_raid_attendees (
						raid_id mediumint(8) unsigned NOT NULL default '0',
						member_id mediumint(5) NOT NULL default '0',
						attendees_subscribed tinyint(1) NOT NULL default '0',
						attendees_note text default NULL,
						attendees_signup_time int(11) NOT NULL default '0',
						confirmed tinyint(1) NOT NULL default '0',
						attendees_random mediumint(4) NOT NULL default '0',
						KEY raid_id (raid_id),
						KEY member_name (member_id))";
				break;
			case "step4a":
				$sql = "DROP TABLE IF EXISTS " . $table_prefix . "raidplan_wildcards";
				break;
			case "step4b":
				$sql = "CREATE TABLE IF NOT EXISTS " . $table_prefix . "raidplan_wildcards (
						user_name varchar(25) default NULL,
						wildcard tinyint(1) NOT NULL default '0',
						KEY user_name (user_name))";
				break;
			case "step5a":
				$sql = "DROP TABLE IF EXISTS " . $table_prefix . "raidplan_classes";
				break;
			case "step5b":
				$sql = "CREATE TABLE IF NOT EXISTS " . $table_prefix . "raidplan_classes (
						event_name varchar(50) NOT NULL default '',
						class_name varchar(50) NOT NULL default '',
						class_count smallint(3) NOT NULL default '0')";
				break;
			case "step6a":
				$sql = "DROP TABLE IF EXISTS " . $table_prefix . "raidplan_config";
				break;
			case "step6b":
				$sql = "CREATE TABLE IF NOT EXISTS " . $table_prefix . "raidplan_config (
						`config_name` varchar(255) NOT NULL default '',
        `config_value` varchar(255) default NULL,
        PRIMARY KEY  (`config_name`))";
				break;	
			
		}
		return $sql;
	}
// Install the config settings
  function InsertIntoTable($fieldname,$insertvalue)
      {
        global $eqdkp_root_path, $user, $SID, $table_prefix;
		    $sql = "INSERT INTO " . $table_prefix . "raidplan_config VALUES ('".$fieldname."', '".$insertvalue."');";
		    $this->add_sql(SQL_INSTALL, $sql);
      }

	/******************************
	* Hooks
	******************************/
	// Hook to show wildcards in user management
	function UserSettingHook()
	{
		global $db, $tpl;
		$tpl->assign_vars(array(
				'L_WILDCARD'		=> "Raidplaner Freikarte"));
		$user_name = (isset($_GET['name'])) ? $_GET['name'] : "";
		if ($user_name == "") { return; }
		$sql = "SELECT user_name, wildcard
				FROM " . RP_WILDCARD_TABLE . "
				WHERE user_name='" . $user_name . "'
				AND wildcard=1";
		$result = $db->query($sql);
		if ($row = $db->fetch_record($result))
		{
			$tpl->assign_vars(array(
				'S_RAIDPLAN'		=> true,
				'WILDCARD_CHECKED'	=> ' checked="checked"'));
		}
	}
	
	// Hook to modify wildcards in user management
	function UserUpdateHook()
	{
		global $db, $tpl;
		
		$user_name = (isset($_POST['name'])) ? $_POST['name'] : "";
		if ($user_name == "") { break; }
		$sql = "DELETE FROM " . RP_WILDCARD_TABLE . "
				WHERE user_name='" . $user_name . "'";
		$db->query($sql);
		
		if (isset($_POST['wildcard_set'])) 
		{
			$sql = "INSERT INTO " . RP_WILDCARD_TABLE . "
					(user_name, wildcard)
					VALUES ('" . $user_name . "', 1)";
			$db->query($sql);
		}
	}
}
?>
