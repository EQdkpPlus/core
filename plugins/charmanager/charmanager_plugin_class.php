<?php
/******************************
 * EQDKP PLUGIN: Charmanager
 * (c) 2006 - 2007 by WalleniuM [Simon Wallmann]
 * http://www.wallenium.de   
 * ------------------
 * charmanager_plugin_class.php
 * Changed: January 10, 2006
 * 
 ******************************/

if ( !defined('EQDKP_INC') )
{
    die('You cannot access this file directly.');
}
class charmanager_plugin_class extends EQdkp_Plugin
{
    function charmanager_plugin_class($pm)
    {
        global $eqdkp_root_path, $user, $table_prefix, $SID;
        
        $this->eqdkp_plugin($pm);
        $this->pm->get_language_pack('charmanager');
        
        $this->add_data(array(
            'name'          => 'Character Manager',
            'code'          => 'charmanager',
            'path'          => 'charmanager',
            'contact'       => 'webmaster@wallenium.de',
            'template_path' => 'plugins/charmanager/templates/',
            'version'       => '1.0.3')
        );
        
        // Add Menus
        $this->add_menu('main_menu1', $this->gen_main_menu1());
	      $this->add_menu('settings', $this->gen_settings_menu()); 

        // Register our permissions
        $this->add_permission('954', 'a_charmanager_edit',  	'N', $user->lang['uc_edit_all']);
	      $this->add_permission('955', 'u_charmanager_manage',  'Y', $user->lang['uc_manage']);
	      $this->add_permission('956', 'u_charmanager_add',  		'Y', $user->lang['uc_add']);
	      $this->add_permission('957', 'u_charmanager_view',  	'Y', $user->lang['uc_view']);
     
     		// Define installation
        // -----------------------------------------------------
    		$sql = "CREATE TABLE IF NOT EXISTS " . $table_prefix . "member_additions (
				    		`id` int(10) NOT NULL auto_increment, 
   							`member_id` smallint(5) default NULL, 
   							`picture` varchar(255) NOT NULL default '', 
   							`fir` SMALLINT(6) NOT NULL default '0', 
   							`nr` SMALLINT(6) NOT NULL default '0', 
   							`sr` SMALLINT(6) NOT NULL default '0', 
   							`ar` SMALLINT(6) NOT NULL default '0', 
   							`frr` SMALLINT(6) NOT NULL default '0', 
   							`skill_1` SMALLINT(6) NOT NULL default '0', 
   							`skill_2` SMALLINT(6) NOT NULL default '0', 
   							`skill_3` SMALLINT(6) NOT NULL default '0', 
   							`gender` varchar(255) NOT NULL default '', 
   							`guild` varchar(255) NOT NULL default '', 
   							`blasc_id` varchar(255) NOT NULL default '', 
   							`ct_profile` varchar(255) NOT NULL default '', 
   							`curse_profiler` varchar(255) NOT NULL default '', 
   							`allakhazam` varchar(255) NOT NULL default '', 
   							`talentplaner` varchar(255) NOT NULL default '', 
   			PRIMARY KEY  (`id`) 
 		)";
				$this->add_sql(SQL_INSTALL, $sql);
    }

		function gen_main_menu1()
	    {
			global $user, $SID;

	        if ($this->pm->check(PLUGIN_INSTALLED, 'charmanager') && $user->check_auth('u_charmanager_view', false))
	        {
	            global $db, $user, $eqdkp;
	            $main_menu1 = array(
	                array(
						'link' => 'plugins/charmanager/listprofiles.php' . $SID,
						'text' => $user->lang['uc_enu_profiles'],
						'check' => ''
					)
	            );

	            return $main_menu1;
	        }
	        return;
    }

    function gen_settings_menu()
    {
    	global $db, $user, $SID, $eqdkp;
    	if ( $this->pm->check(PLUGIN_INSTALLED, 'charmanager') &&  $user->check_auth('u_charmanager_manage', false))
      	{
        	$dkpurl = "http://". trim($eqdkp->config['server_name'], "\\/"). "/" .trim($eqdkp->config['server_path'], "\\/"). "/";
        	$settings_menu = array(
          										$user->lang['charmanager'] => array(
          																	0 => '<a href="'. $dkpurl . 'plugins/charmanager/index.php' . $SID . '">' . $user->lang['uc_manage_chars'] . '</a>',
                															)
        												);
            return $settings_menu;
        }
        return;
    }    
}
?>