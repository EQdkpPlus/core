<?php
// Last Edit: 12.10.2006 by Achaz concerning permissions and Settings
// and AdminMenu etc.

if ( !defined('EQDKP_INC') )
{
    die('You cannot access this file directly.');
}

class ts_plugin_class extends EQdkp_Plugin
{
    function ts_plugin_class($pm)
    {
        global $eqdkp_root_path, $user, $SID;

        $this->eqdkp_plugin($pm);
        $this->pm->get_language_pack('ts');

        $this->add_data(array(
            'name'          => 'CNSDEV Tradeskills',
            'code'          => 'ts',
            'path'          => 'ts',
            'contact'       => 'cns@cnsdev.dk - Achaz@lionforge.de', // Edit Achaz
            'template_path' => 'plugins/ts/templates/',
            'version'       => '0.97.5Beta') //Edit Achaz
        );

        $this->add_menu('main_menu1', $this->gen_main_menu());
        $this->add_menu('settings', $this->gen_settings_menu());
	#If you like the old link (next to the Logout link more, then uncomment this line)
	
        // Register our permissions
//------Edit Achaz 1 line--------
             $this->add_permission('603', 'a_ts_admin', 'N', $user->lang['tsp_admin']);
             $this->add_permission('600', 'u_ts_manage',    'N', $user->lang['tsp_manage']);
//------Edit Achaz 1 line--------
             $this->add_permission('602', 'u_ts_confirm',  'N' , $user->lang['tsp_confirm']);
             $this->add_permission('601', 'u_ts_list',    'Y', $user->lang['tsp_list']);
	     
             $this->add_menu('admin_menu', $this->gen_admin_menu()); //Edit Achaz



        // Define (un)installation
        // -----------------------------------------------------
        // Thanks to Roster and raidplanner? for this code.
		$steps=13;
		for ($i = 1; $i <= $steps; $i++)
		{
			$this->add_sql(SQL_INSTALL, $this->create_ts_tables("step".$i."b"));
			if($i < 6) { $this->add_sql(SQL_UNINSTALL, $this->create_ts_tables("step".$i."a")); }
		}
	//Edit Achaz 3 lines ----
	$this->InsertIntoTable('ts_restrict_professions', '1'); # Restrict to 2 Professions per Charakter?
	//$this->InsertIntoTable('ts_show_cooking', '0');		# Show cooking as a Profession? -> other mechanism
	$this->InsertIntoTable('ts_use_infosite', 'buffed');	# Which Infosite should be used?
	$this->InsertIntoTable('ts_single_show', '');	# Use single show only
    }

    /**
	* Get SQL to create the ts tables
	*
	* @return string Table creation SQL
	*/
	function create_ts_tables($step)
	{
		global $table_prefix, $eqdkp;
		$sql = "";



		if($eqdkp->config['default_game'] == 'WoW_german')
		{
			$Enchanting = 'Verzauberkunst';
			$Leatherworking = 'Lederverarbeitung';
			$Blacksmithing = 'Schmiedekunst';
			$Engineering = 'Ingenieurskunst';
			$Tailoring = 'Schneiderei';
			$Alchemy = 'Alchimie';
			$Cooking = 'Kochen'; // Edit Achaz
    			$Jewelery = 'Juwelenschleifen';
		}
		else
		{
			$Enchanting = 'Enchanting';
			$Leatherworking = 'Leatherworking';
			$Blacksmithing = 'Blacksmithing';
			$Engineering = 'Engineering';
			$Tailoring = 'Tailoring';
			$Alchemy = 'Alchemy';
			$Cooking = 'Cooking'; //Edit Achaz
    			$Jewelery = 'Jewelcrafting';
		}


		switch ($step)
		{
			case "step1a":
				$sql = "DROP TABLE IF EXISTS " . $table_prefix . "tradeskill_recipes";
				break;
			case "step1b":
				$sql = "CREATE TABLE IF NOT EXISTS " . $table_prefix . "tradeskill_recipes (
					id int(11) NOT NULL auto_increment,
					recipe_name varchar(255) NOT NULL default '',
					reagents text NOT NULL,
					trade_id int(11) NOT NULL default '0',
					addedby tinyint(6) NOT NULL default '0',
					quality int(11) NOT NULL default '0',
					PRIMARY KEY  (id))";
				break;
			case "step2a":
				$sql = "DROP TABLE IF EXISTS " . $table_prefix . "tradeskill_users";
				break;
			case "step2b":
				$sql = "CREATE TABLE IF NOT EXISTS " . $table_prefix . "tradeskill_users (
					id int(11) NOT NULL auto_increment,
					member_id int(11) NOT NULL default '0',
					trade_id int(11) NOT NULL default '0',
					PRIMARY KEY  (id))";
				break;
			case "step3a":
				$sql = "DROP TABLE IF EXISTS " . $table_prefix . "tradeskills";
				break;
			case "step3b":
				$sql = "CREATE TABLE IF NOT EXISTS " . $table_prefix . "tradeskills (
					trade_id int(11) NOT NULL auto_increment,
					trade_icon text NOT NULL,
					trade_name varchar(255) NOT NULL default '',
					inuse ENUM( '0', '1' ) DEFAULT '1',
					PRIMARY KEY  (trade_id))";
				break;
			case "step4a":
				$sql = "DROP TABLE IF EXISTS " . $table_prefix . "user_tradeskills";
				break;
			case "step4b":
				$sql = "CREATE TABLE IF NOT EXISTS " . $table_prefix . "user_tradeskills (
					id int(11) NOT NULL auto_increment,
					trade_id int(11) NOT NULL default '0',
					member_id int(11) NOT NULL default '0',
					ps int(11) NOT NULL default '0',
					hide int(11) NOT NULL default '0',
					PRIMARY KEY  (id))";
				break;
				//Edit Achaz cases 5a/b -------------------------
			case "step5a":
				$sql = "DROP TABLE IF EXISTS " . $table_prefix . "tradeskill_config";
				break;
			case "step5b":
				$sql = "CREATE TABLE IF NOT EXISTS " . $table_prefix . "tradeskill_config (
					config_name varchar(255) NOT NULL default '',
					config_value varchar(255) default NULL,
					PRIMARY KEY (config_name))";
				break;	
			case "step6b":
				$sql = "INSERT INTO " . $table_prefix . "tradeskills VALUES (1, 'http://wow.allakhazam.com/images/icons/Spell_Holy_GreaterHeal.png', '" . $Enchanting . "','0')";
				break;
			case "step7b":
				$sql = "INSERT INTO " . $table_prefix . "tradeskills VALUES (2, 'http://wow.allakhazam.com/images/icons/INV_Misc_LeatherScrap_03.png', '" . $Leatherworking . "','0')";
				break;
			case "step8b":
				$sql = "INSERT INTO " . $table_prefix . "tradeskills VALUES (3, 'http://wow.allakhazam.com/images/icons/INV_Stone_GrindingStone_01.png', '" . $Blacksmithing . "','0')";
				break;
			case "step9b":
				$sql = "INSERT INTO " . $table_prefix . "tradeskills VALUES (4, 'http://wow.allakhazam.com/images/icons/INV_Misc_Bomb_06.png', '" . $Engineering . "','0')";
				break;
			case "step10b":
				$sql = "INSERT INTO " . $table_prefix . "tradeskills VALUES (5, 'http://wow.allakhazam.com/images/icons/INV_Chest_Cloth_04.png', '" . $Tailoring . "','0')";
				break;
			case "step11b":
				$sql = "INSERT INTO " . $table_prefix . "tradeskills VALUES (6, 'http://wow.allakhazam.com/images/icons/INV_Potion_20.png', '" . $Alchemy . "','0')";
				break;
				//Edit achaz 12b
			case "step12b":
				$sql = "INSERT INTO " . $table_prefix . "tradeskills VALUES (7, 'http://wow.allakhazam.com/images/icons/INV_Misc_Food_15.png', '" . $Cooking . "','0')";
				break;
			case "step13b":
				$sql = "INSERT INTO " . $table_prefix . "tradeskills VALUES (8, 'http://wow.allakhazam.com/images/icons/INV_Misc_QuestionMark.png', '" . $Jewelery . "','0')";
				break;
			}
		return $sql;
	}

    function gen_main_menu()
    {
        if ( $this->pm->check(PLUGIN_INSTALLED, 'ts') )
        {
            global $db, $user, $SID;

            $main_menu = array(
                array('link' => 'plugins/' . $this->get_data('path') . '/index.php'  . $SID, 'text' => $user->lang['ts_list'], 'check' => 'u_ts_list')
            );

            return $main_menu;
        }
        return;
    }

    function gen_settings_menu()
    {
        if ( $this->pm->check(PLUGIN_INSTALLED, 'ts') )
        {
            global $db, $user, $SID;

        $settings_menu = array(
            $user->lang['ts'] => array(
                0 => '<a href="plugins/ts/tradeskills.php' . $SID . '">' . $user->lang['ts_settings'] . '</a>',
            )
        );

            return $settings_menu;
        }
        return;
    }

   /**
    	* Generate ts admin menu
    	*
    	* @return array
    	*/
    	
//Edit by Achaz -- thx to Raidplanner
    function gen_admin_menu()
    {


	if ($this->pm->check(PLUGIN_INSTALLED, 'ts') )
	{
		global $db, $user, $user, $SID, $eqdkp;
          	$dkpurl = "http://". trim($eqdkp->config['server_name'], "\\/"). "/" .trim($eqdkp->config['server_path'], "\\/"). "/";
			$admin_menu = array(
				'ts' => array(
					0 => $user->lang['ts'],
					1 => array(
						'link' => $dkpurl . 'plugins/ts/admin/settings.php' . $SID,
						'text' => $user->lang['ts_adminsettings'],
						'check' => 'a_ts_admin'),
					2 => array(
						'link' => $dkpurl . 'plugins/ts/admin/adminindex_add.php' . $SID,
						'text' => $user->lang['ts_administration_add'],
						'check' => 'a_ts_admin'),
					3 => array(
						'link' => $dkpurl . 'plugins/ts/admin/adminmemberskills.php' . $SID,
						'text' => $user->lang['ts_admin_skills'],
						'check' => 'a_ts_admin')
				)
			);
		return $admin_menu;
	}
	return;
    }


    function InsertIntoTable($fieldname,$insertvalue) // Edit by Achaz -- thx to Raidplanner
    {
	        global $eqdkp_root_path, $user, $SID, $table_prefix;
	                    $sql = "INSERT INTO " . $table_prefix . "tradeskill_config VALUES ('".$fieldname."', '".$insertvalue."');";
	                    $this->add_sql(SQL_INSTALL, $sql);
    }

}     
?>
