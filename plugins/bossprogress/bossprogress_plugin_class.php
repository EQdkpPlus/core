<?php
/******************************
 * EQdkp Bossprogress2
 * by sz3
 * 
 * Additional Credits should go to 
 * Corgan's bosscounter mod
 * Wallenium's ItemSpecials plugin
 * Magnus' raidprogress plugin
 * 
 * which all lend inspiration and/or code bits 
 *  
 * Copyright 2006
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * bossprogress_plugin_class.php
 * 02.10.06 sz3
 ******************************/

if (!defined('EQDKP_INC')) {
	die('You cannot access this file directly.');
}

include_once ('include/data.php');

//global $bzone;

class bossprogress_Plugin_Class extends EQdkp_Plugin {
	function bossprogress_plugin_class($pm) {

$bo_kc[] = array();
$bo_fkd[] = array();
$bo_lkd[] = array();

$zo_vc[] = array();
$zo_fvd[] = array();
$zo_lvd[] = array();

$bzone = array (
	'misc' => array (
		'azuregos',
		'kazzak'
	),
	'bwl' => array (
		'razorgore',
		'vaelastrasz',
		'lashlayer',
		'firemaw',
		'ebonroc',
		'flamegor',
		'chromaggus',
		'nefarian'
	),
	'onyxia' => array (
		'onyxia'
	),
	'dream' => array (
		'ysondre',
		'taerar',
		'emeriss',
		'lethon'
	),
	'mc' => array (
		'lucifron',
		'magmadar',
		'gehennas',
		'garr',
		'geddon',
		'shazzrah',
		'sulfuron',
		'golemagg',
		'majordomo',
		'ragnaros'
	),
	'zg' => array (
		'mandokir',
		'jindo',
		'gahzranka',
		'grilek',
		'hazzarah',
		'renataki',
		'wushoolay',
		'thekal',
		'arlokk',
		'jeklik',
		'marli',
		'venoxis',
		'hakkar'
	),
	'aq20' => array (
		'kurinnaxx',
		'rajaxx',
		'ayamiss',
		'buru',
		'moam',
		'ossirian'
	),
	'aq40' => array (
		'skeram',
		'kri',
		'yauj',
		'vem',
		'sartura',
		'fankriss',
		'huhuran',
		'viscidus',
		'veknilash',
		'veklor',
		'ouro',
		'cthun'
	),
	'naxx' => array (
		'anubrekhan',
		'faerlina',
		'maexxna',
		'noth',
		'heigan',
		'loatheb',
		'patchwerk',
		'grobbulus',
		'gluth',
		'thaddius',
		'razuvious',
		'gothik',
		'korthazz',
		'blaumeux',
		'mograine',
		'zeliek',
		'sapphiron',
		'kelthuzad',		
	)
);

		#include ('include/data.php');
		
		global $eqdkp_root_path, $user, $SID, $table_prefix;

		$this->eqdkp_plugin($pm);
		$this->pm->get_language_pack('bossprogress');

		$this->add_data(array (
			'name' => 'EQdkp Bossprogress',
			'code' => 'bossprogress',
			'path' => 'bossprogress',
			'contact' => 'doom.am@gmx.de',
			'template_path' => 'plugins/bossprogress/templates/',
			'version' => '2.0beta14'
		));

		//Permissions
		$this->add_permission('2302', 'a_bossprogress_conf', 'N', $user->lang['is_bp_conf']);
		$this->add_permission('2301', 'u_bossprogress_view', 'Y', $user->lang['is_bp_view']);

		//Menus
		$this->add_menu('main_menu1', $this->gen_main_menu1());
		$this->add_menu('admin_menu', $this->gen_admin_menu());
		
		//SQL Config "File"
		//Create table on install
		$sql = "CREATE TABLE IF NOT EXISTS " . $table_prefix . "bp_config (`config_name` varchar(255) NOT NULL default '',
		        `config_value` varchar(255) default NULL, PRIMARY KEY  (`config_name`))";
		$this->add_sql(SQL_INSTALL, $sql);

		// Set default values on install
		$this->InsertIntoTable('dynZone', '1');
		$this->InsertIntoTable('dynBoss', '1');
		$this->InsertIntoTable('bossInfo', 'rnote');
		$this->InsertIntoTable('zoneInfo', 'rname');
		$this->InsertIntoTable('zhiType', '0');

		$this->InsertIntoTable('detailBoss', '1');
		$this->InsertIntoTable('showSB', '0');

		$this->InsertIntoTable('nameDelim', ',');
		$this->InsertIntoTable('noteDelim', ',');
		$this->InsertIntoTable('tables', '');
		
		foreach ($bzone as $zone => $bosses){
			if (strcmp($user->lang[$zone][long], $user->lang[$zone][short])){
				$zonestring = "''". str_replace("'", "''", $user->lang[$zone][long]) . "'', ''" .  str_replace("'", "''", $user->lang[$zone][short]) . "''";
			}else{
				$zonestring = "''". str_replace("'", "''", $user->lang[$zone][long]) . "''";
			}

			$this->InsertIntoTable('pz_'. $zone, $zonestring);
			foreach($bosses as $boss){
				if (strcmp($user->lang[$boss][long], $user->lang[$boss][short])){ 
					$bossstring = "''" . str_replace("'", "''",  $user->lang[$boss][long]) . "'', ''" . str_replace("'", "''", $user->lang[$boss][short]) . "''";
				}else{
					$bossstring = "''" . str_replace("'", "''",  $user->lang[$boss][long]) . "''";
				}
				$this->InsertIntoTable('pb_'.$boss, $bossstring);
			}
		}
	
		//Drop table on deinstall
		$this->add_sql(SQL_UNINSTALL, "DROP TABLE IF EXISTS " . $table_prefix . "bp_config");
	}

	function gen_main_menu1() {
		if ($this->pm->check(PLUGIN_INSTALLED, 'bossprogress')) {
			global $db, $user, $SID;

			$main_menu1 = array (
				array (
					'link' => 'plugins/' . $this->get_data('path') . '/bossprogress.php' . $SID,
					'text' => $user->lang['link'],
					'check' => 'u_bossprogress_view'
			));

			return $main_menu1;
		}
		return;
	}

	function gen_admin_menu() {
		if ($this->pm->check(PLUGIN_INSTALLED, 'bossprogress')) {
			global $db, $user, $SID, $eqdkp_root_path, $eqdkp;
	
			$admin_menu = array (
				'bossprogress' => array (
					0 => $user->lang['is_adminmenu_bp'],
					1 => array (
						'link' => $eqdkp_root_path . 'plugins/bossprogress/admin/settings.php' . $SID,
						'text' => $user->lang['is_bp_conf'],
						'check' => 'a_bossprogress_conf'
					)
				)
			);

			return $admin_menu;
		}
		return;
	}

	function InsertIntoTable($fieldname, $insertvalue) {
		global $eqdkp_root_path, $user, $SID, $table_prefix;
		$sql = "INSERT INTO " . $table_prefix . "bp_config VALUES ('" . $fieldname . "', '" . $insertvalue . "');";
		$this->add_sql(SQL_INSTALL, $sql);
	}

}
?>
