<?php
if ( !defined('EQDKP_INC') )
{
    die('You cannot access this file directly.');
}

class ctrt_Plugin_Class extends EQdkp_Plugin
{
    function ctrt_plugin_class($pm)
    {
        global $eqdkp_root_path, $user, $SID, $table_prefix;
        
        $this->eqdkp_plugin($pm);
        $this->pm->get_language_pack('ctrt');

        $this->add_data(array(
            'name'          => 'CT_RaidTrackerImport',
            'code'          => 'ctrt',
            'path'          => 'ctrt',
            'contact'       => 'CTRaidTrackerImport@freddy.eu.org',
            'template_path' => 'plugins/ctrt/templates/',
            'version'       => '1.13 for EQDKP Plus')
        );
        
        #$this->add_menu('main_menu2', $this->gen_main_menu2()); # If you like the old link (next to the Logout link more, then uncomment this line)
        $this->add_menu('admin_menu', $this->gen_admin_menu());
        
        
        // Define installation
        // -----------------------------------------------------
        
        
//       	$this->add_sql(SQL_INSTALL, "ALTER TABLE `".ITEMS_TABLE."` ADD `game_itemid` INT(10) UNSIGNED NULL;");
       	
//        $sql = "CREATE TABLE IF NOT EXISTS " . $table_prefix . "plugin_ctrt_twinks (
//        `config_name` varchar(255) NOT NULL default '',
//        `config_value` varchar(255) default NULL,
//        PRIMARY KEY  (`config_name`)
//        )";
//        
//		    $this->add_sql(SQL_INSTALL, $sql);
//
//        $sql = "CREATE TABLE IF NOT EXISTS " . $table_prefix . "plugin_ctrt_trigger (
//        `config_name` varchar(255) NOT NULL default '',
//        `config_value` varchar(255) default NULL,
//        PRIMARY KEY  (`config_name`)
//        )";
//        
//		    $this->add_sql(SQL_INSTALL, $sql);       	

        // Define uninstallation
        // -----------------------------------------------------
//        $this->add_sql(SQL_UNINSTALL, "ALTER TABLE `".ITEMS_TABLE."` DROP `game_itemid`;");
//        $this->add_sql(SQL_UNINSTALL, "DROP TABLE IF EXISTS " . $table_prefix . "plugin_ctrt_twinks");
//				$this->add_sql(SQL_UNINSTALL, "DROP TABLE IF EXISTS " . $table_prefix . "plugin_ctrt_twinks");
    }
    
    function gen_main_menu2()
    {
        if ( $this->pm->check(PLUGIN_INSTALLED, 'ctrt') )
        {
            global $db, $user;
            
            $main_menu2 = array(
                array('link' => 'plugins/' . $this->get_data('path') . '/index.php' . $SID, 'text' => $user->lang['import_ctrt_data'], 'check' => 'a_raid_add')
            );
            
            return $main_menu2;
        }
        return;
    }
    
    function gen_admin_menu()
    {
        if ( $this->pm->check(PLUGIN_INSTALLED, 'ctrt') )
        {
            global $db, $user, $SID, $eqdkp;
            $dkpurl = "http://". trim($eqdkp->config['server_name'], "\\/"). "/" .trim($eqdkp->config['server_path'], "\\/"). "/";
				    $admin_menu = array(
				    		'raids' => array(
				            0 => $user->lang['raids'],
				           	1 => array('link' => $dkpurl . 'admin/addraid.php' . $SID,   'text' => $user->lang['add'],  'check' => 'a_raid_add'),
				            2 => array('link' => $dkpurl . 'plugins/ctrt/index.php' . $SID, 'text' => $user->lang['import_ctrt_data'], 'check' => 'a_raid_add'),
				            3 => array('link' => $dkpurl . 'admin/listraids.php' . $SID, 'text' => $user->lang['list'], 'check' => 'a_raid_')
				        )
				     );
            
            return $admin_menu;
        }
        return;
    }
    
}
?>