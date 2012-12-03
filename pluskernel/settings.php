<?php
/******************************
 * EQDKP PLUGIN: PLUSkernel
 * (c) 2006 by WalleniuM [Simon Wallmann]
 * http://www.kompsoft.de
 * ------------------
 * pluskernel_plugin_class.php
 * Changed: November 07, 2006
 *
 ******************************/

define('EQDKP_INC', true);
$eqdkp_root_path = '../';
$data_saved = false;
include_once($eqdkp_root_path . 'common.php');
include_once('include/update.class.php');
include_once('include/init.class.php');
include_once('include/komptab.class.php');
include_once('include/html.class.php');
include_once('include/db.class.php');

// the language include part
global $user, $eqdkp;
// Set up language array
if ( (isset($user->data['user_id'])) && ($user->data['user_id'] != ANONYMOUS) && (!empty($user->data['user_lang'])) )
{
	$lang_name = $user->data['user_lang'];
}else{
	$lang_name = $eqdkp->config['default_lang'];
}
$lang_path = $eqdkp_root_path.'pluskernel/language/'.$lang_name.'/';
include($lang_path . 'lang_main.php');
// end of language part

// Check user permission
$user->check_auth('a_config_man');

// Init
$init = new InitPlus();
$html = new htmlPlus();
$tabs = new kompTabs();
$plusdb = new dbPlus();

// get the config
$conf = $plusdb->InitConfig();

// Build the Dropdown Arrays:
$itemstats_language = array(
	'german'							=> $plang['pk_german'],
	'english'							=> $plang['pk_english'],
	'french'							=> $plang['pk_french']
);

// Build the Dropdown Arrays:
$newsloot_limit = array(
	'all'							=> 0,
	'5'								=> 5,
	'10'							=> 10,
	'15'							=> 15,
	'20'							=> 20
);

// Build the Dropdown Arrays:
$realm_region = array(
	'eu'							=> "eu",
	'us'							=> "us",
);

	//Databases for Itemsearch
$a_itemstats_site= array(
	'allakhazam'						=> "allakhazam",
	'buffed'							=> "buffed",
	'wowhead'							=> "wowhead",
	'thottbot'							=> "thottbot",
	'judgehype'							=> "judgehype",
	'wowdbu'							=> "wowdbu",
	'amory'								=> "amory",
);

//Itemstats allakhazam Language
$a_allakhazam_language= array(
	'enUS'						=> "enUS",
	'deDE'						=> "deDE",
	'frFR'						=> "frFR",
	'esES'						=> "esES",
	'koKR'						=> "koKR",
	'zhCN'						=> "zhCN",
	'zhTW'						=> "zhTW",

);


//Item default language
$a_Item_default_language= array(
	'en'						=> "en",
	'de'						=> "de",
	'fr'						=> "fr",
);

//Item default language
$a_is_integration_mode= array(
	'normal'						=> "normal",
	'script'						=> "script",
);

//Item default language
$a_is_tooltip_displayer= array(
	'overlib'						=> "overlib",
	'light'							=> "light",
);

echo $init->Header($eqdkp_root_path);
echo "<script language='JavaScript' type='text/javascript' src='../itemstats/overlib/overlib.js'></script>";

// Save this shit
if ($_POST['save_plus']){
	// get an array with the config fields of the DB
		$isplusconfarray = $plusdb->CheckDBFields('plus_config', 'config_name');

  // global config
    $plusdb->UpdateConfig('pk_class_color', $_POST['pk_class_color'], $isplusconfarray); # Coloured Classnames?
		$plusdb->UpdateConfig('pk_quickdkp', $_POST['pk_quickdkp'], $isplusconfarray); # Show Quickdkp?
		$plusdb->UpdateConfig('pk_updatecheck', $_POST['pk_updatecheck'], $isplusconfarray); # Enable Update Warnings & VersionCheck
		$plusdb->UpdateConfig('pk_windowtime', $_POST['pk_windowtime'], $isplusconfarray);
		$plusdb->UpdateConfig('pk_newsloot_limit', $_POST['pk_newsloot_limit'], $isplusconfarray);
		$plusdb->UpdateConfig('pk_servername', htmlspecialchars_decode($_POST['pk_servername'], ENT_QUOTES), $isplusconfarray);
		$plusdb->UpdateConfig('pk_server_region', $_POST['pk_server_region'], $isplusconfarray);

		$plusdb->UpdateConfig('pk_round_activate', $_POST['pk_round_activate'], $isplusconfarray);
		$plusdb->UpdateConfig('pk_round_precision', $_POST['pk_round_precision'], $isplusconfarray);

	// multidkp
		$plusdb->UpdateConfig('pk_multidkp', $_POST['pk_multidkp'], $isplusconfarray); # Multidkp on/off
		$plusdb->UpdateConfig('pk_multiTooltip', $_POST['pk_multiTooltip'], $isplusconfarray); # Multidkp on/off
		$plusdb->UpdateConfig('pk_multiSmarttip', $_POST['pk_multiSmarttip'], $isplusconfarray); # Multidkp on/off
		$plusdb->UpdateConfig('pk_default_multi', $_POST['pk_default_multi'], $isplusconfarray); # Multidkp on/off

	// Listmembers
		$plusdb->UpdateConfig('pk_leaderboard', $_POST['pk_leaderboard'], $isplusconfarray); # Show the leaderbaord?
		$plusdb->UpdateConfig('pk_leaderboard_2row', $_POST['pk_leaderboard_2row'], $isplusconfarray); # 2 reihen?
		$plusdb->UpdateConfig('pk_leaderboard_limit', $_POST['pk_leaderboard_limit'], $isplusconfarray); # limit der anzeige auf
		$plusdb->UpdateConfig('pk_leaderboard_hide_zero', $_POST['pk_leaderboard_hide_zero'], $isplusconfarray); # zeige spieler mit 0 dkp?

		$plusdb->UpdateConfig('pk_rank', $_POST['pk_rank'], $isplusconfarray); # Show the rank?
		$plusdb->UpdateConfig('pk_rank_icon', $_POST['pk_rank_icon'], $isplusconfarray); # Rank icon
		$plusdb->UpdateConfig('pk_level', $_POST['pk_level'], $isplusconfarray); # Show Level
		$plusdb->UpdateConfig('pk_lastloot', $_POST['pk_lastloot'], $isplusconfarray);   # Show Last Loot
		$plusdb->UpdateConfig('pk_lastraid', $_POST['pk_lastraid'], $isplusconfarray);   # Show Last Raid
		$plusdb->UpdateConfig('pk_attendanceAll', $_POST['pk_attendanceAll'], $isplusconfarray);   # Show Raid Attendance
		$plusdb->UpdateConfig('pk_attendance90', $_POST['pk_attendance90'], $isplusconfarray);   # Show Raid Attendance
		$plusdb->UpdateConfig('pk_attendance30', $_POST['pk_attendance30'], $isplusconfarray);   # Show Raid Attendance
		$plusdb->UpdateConfig('pk_attendance60', $_POST['pk_attendance60'], $isplusconfarray);   # Show Raid Attendance
		$plusdb->UpdateConfig('pk_showclasscolumn', $_POST['pk_showclasscolumn'], $isplusconfarray);

		$plusdb->UpdateConfig('pk_show_skill', $_POST['pk_show_skill'], $isplusconfarray);
		$plusdb->UpdateConfig('pk_show_arkan_resi', $_POST['pk_show_arkan_resi'], $isplusconfarray);
		$plusdb->UpdateConfig('pk_show_fire_resi', $_POST['pk_show_fire_resi'], $isplusconfarray);
		$plusdb->UpdateConfig('pk_show_fire_resi', $_POST['pk_show_fire_resi'], $isplusconfarray);
		$plusdb->UpdateConfig('pk_show_nature_resi', $_POST['pk_show_nature_resi'], $isplusconfarray);
		$plusdb->UpdateConfig('pk_show_ice_resi', $_POST['pk_show_ice_resi'], $isplusconfarray);
		$plusdb->UpdateConfig('pk_show_shadow_resi', $_POST['pk_show_shadow_resi'], $isplusconfarray);
		$plusdb->UpdateConfig('pk_show_profiles', $_POST['pk_show_profiles'], $isplusconfarray);

	// Showmembers

	// Links
		$plusdb->UpdateConfig('pk_links', $_POST['pk_links'], $isplusconfarray);   # Show Links
		$plusdb->ProcessLinks($_POST['linkname'], $_POST['linkurl'], $_POST['linkwindow'], $isplusconfarray); # Save the links in the Database
	// Itemstats
		$plusdb->UpdateConfig('pk_itemstats', $_POST['pk_itemstats'], $isplusconfarray);   # Enable Itemstats
		$plusdb->UpdateConfig('pk_itemstats_debug', $_POST['pk_itemstats_debug'], $isplusconfarray);   # Debug
		$plusdb->UpdateConfig('pk_is_icon_loc', $_POST['pk_is_icon_loc'], $isplusconfarray);     # Icon Location
		$plusdb->UpdateConfig('pk_is_icon_ext', $_POST['pk_is_icon_ext'], $isplusconfarray);     # Itemextension

		$plusdb->UpdateConfig('pk_is_prio_first', $_POST['pk_is_prio_first'], $isplusconfarray);   # First Site to Search for
		$plusdb->UpdateConfig('pk_is_prio_second', $_POST['pk_is_prio_second'], $isplusconfarray); # Second Site to Search for
		$plusdb->UpdateConfig('pk_is_prio_third', $_POST['pk_is_prio_third'], $isplusconfarray);   # Third Site to Search for
		$plusdb->UpdateConfig('pk_is_prio_fourth', $_POST['pk_is_prio_fourth'], $isplusconfarray); # Fourth Site to Search for

		$plusdb->UpdateConfig('pk_is_itemlanguage', $_POST['pk_is_itemlanguage'], $isplusconfarray);   # Language default for Item's Id when not specified
		$plusdb->UpdateConfig('pk_is_itemlanguage_alla', $_POST['pk_is_itemlanguage_alla'], $isplusconfarray);   #Allakhazam languages search

		$plusdb->UpdateConfig('pk_is_patch_cache', $_POST['pk_is_patch_cache'], $isplusconfarray);   #The path for custom item, it's based on Itemstats directory path.
		$plusdb->UpdateConfig('pk_is_autosearch', $_POST['pk_is_autosearch'], $isplusconfarray);   # If the object is not on the cache, Itemstats will search it on data website (Allakhazam, etc.)
#		$plusdb->UpdateConfig('pk_is_integration_mode', $_POST['pk_is_integration_mode'], $isplusconfarray);   #integration mode
#		$plusdb->UpdateConfig('pk_is_tooltip_css', $_POST['pk_is_tooltip_css'], $isplusconfarray);   # Choose the tooltip style.
#		$plusdb->UpdateConfig('pk_is_tooltip_js', $_POST['pk_is_tooltip_js'], $isplusconfarray);   # Choose the tooltip displayer
		$plusdb->UpdateConfig('pk_is_path_sockets_image', $_POST['pk_is_path_sockets_image'], $isplusconfarray);   # Sockets images path (only for Allakhazam and Buffed objects)

		#$plusdb->UpdateConfig('pk_is_', $_POST['pk_is_'], $isplusconfarray);   #
		#$plusdb->UpdateConfig('pk_is_', $_POST['pk_is_'], $isplusconfarray);   #
		#$plusdb->UpdateConfig('pk_is_', $_POST['pk_is_'], $isplusconfarray);   #
		#$plusdb->UpdateConfig('pk_trans_en_de', $_POST['pk_trans_en_de'], $isplusconfarray);
		#$plusdb->UpdateConfig('pk_trans_de_en', $_POST['pk_trans_de_en'], $isplusconfarray);

		$data_saved = true;
}


   // Default Leaderboard
  $a_multidkps = array('0' => 'none') ;
  $sql = 'SELECT multidkp_id, multidkp_name
          FROM ' . MULTIDKP_TABLE . '
          WHERE multidkp_name IS NOT NULL' ;
  if (($multi_result = $db->query($sql)))
	{
		while ($multi = $db->fetch_record($multi_result) )
		{
			$a_multidkps[$multi['multidkp_id']] = $multi['multidkp_name'];
  	}
	}

// Output
	 $output  = ($data_saved == true) ? $html->MsgBox($plang['pk_save_title'], $plang['pk_succ_saved'], 'images/ok.png', '90%', true, '36px', '36px', true).'<br/>' : '';
	 $output .= $html->StartForm('settings', 'settings.php');
   $output .= $tabs->startPane('config');
   $output .= $tabs->startTab($plang['pk_tab_global'], 'config1');
// Global
   $output .= $html->CheckBox('pk_class_color', $plang['pk_set_ClassColor'] , $conf['pk_class_color'], $plang['pk_help_colorclassnames']);
   $output .= "<br />";
   $output .= $html->CheckBox('pk_quickdkp', $plang['pk_set_QuickDKP'] , $conf['pk_quickdkp'], $plang['pk_help_quickdkp']);
   $output .= "<br />";
   $output .= "<hr />";
   $output .= $html->CheckBox('pk_round_activate', $plang['pk_set_round_activate'] , $conf['pk_round_activate'], $plang['pk_help_round_activate']);
   $output .= "<br />";
   $output .= $html->TextField('pk_round_precision', '2', $conf['pk_round_precision'] ,"" , $plang['pk_help_round_precision']). " ". $plang['pk_set_round_precision'] ;
   $output .= "<br />";
   $output .= "<hr />";

   $output .= $html-> DropDown('pk_newsloot_limit', $newsloot_limit , $conf['pk_newsloot_limit'], "", $plang['pk_help_newsloot_limit'] ). " ".$plang['pk_set_newsloot_limit'];
   $output .= "<br />";
   $output .= "<hr />";
   $output .= $html->CheckBox('pk_updatecheck', $plang['pk_set_Updatecheck'] , $conf['pk_updatecheck'], $plang['pk_help_autowarning']);
   $output .= "<br />";
   $output .= $html->TextField('pk_windowtime', '4', $conf['pk_windowtime'] ,$plang['pk_window_time1'] , $plang['pk_help_warningtime'])." ".$plang['pk_window_time2'];
   $output .= "<br />";
   $output .= "<hr />";
   $output .= "<br />";
   $output .= $html->TextField('pk_servername', '20', htmlspecialchars($conf['pk_servername'], ENT_QUOTES) ,$plang['pk_set_servername'] , $plang['pk_help_servername']);
   $output .= $html-> DropDown('pk_server_region', $realm_region , $conf['pk_server_region'], '', '');
   $output .= $tabs->endTab();
   $output .= $tabs->startTab($plang['pk_tab_multidkp'], 'config2');
// MultiDKP
   $output .= $html->CheckBox('pk_multidkp', $plang['pk_set_multidkp'] , $conf['pk_multidkp'], $plang['pk_help_multidkp']);
   $output .= "<br />";
   $output .= $html->CheckBox('pk_multiTooltip', $plang['pk_set_multi_Tooltip'] , $conf['pk_multiTooltip'],$plang['pk_help_dkptooltip']);
   $output .= "<br />";
   $output .= $html->CheckBox('pk_multiSmarttip', $plang['pk_set_multi_smartTooltip'] , $conf['pk_multiSmarttip'],$plang['pk_help_smarttooltip']);
   $output .= "<br />";

   $output .= $tabs->endTab();
   $output .= $tabs->startTab($plang['pk_tab_links'], 'config3');
// Links
   $output .= $html->CheckBox('pk_links', $plang['pk_set_links'] , $conf['pk_links'], $plang['pk_help_links']);
   $output .= "<br /><br />";

   // the add Link thing
   $output .= "<table width='100%' border='0' cellspacing='1' cellpadding='2'>
  								<tr>
    								<th align='left'>".$plang['pk_set_linkname']."</th>
    								<th align='left'>".$plang['pk_set_linkurl']."</th>
    								<th align='left'>".$plang['pk_set_newwindow']."</th>
  								</tr>";

  // get the links stuff
	$customlink = array();
 	$sql = 'SELECT link_id, link_name, link_url, link_window
         FROM '.$table_prefix.'plus_links ORDER BY link_id';
  $result = $db->query($sql);
   while ( $row = $db->fetch_record($result) )
   {
   		$output .= "<tr class='".$eqdkp->switch_row_class()."'>
    							<td nowrap='nowrap'><input type='text' name='linkname[".$row['link_id']."]' size='17' maxlength='50' value='".$row['link_name']."' class='input' /></td>
    							<td nowrap='nowrap'><input type='text' name='linkurl[".$row['link_id']."]' size='28' maxlength='75' value='".$row['link_url']."' class='input' /></td>
 									<td nowrap='nowrap'>".$html->CheckBox('linkwindow['.$row['link_id'].']', '' , $row['link_window'])."</td>
 									</tr>";
 			$max_id = ( $max_id < $row['link_id'] ) ? $row['link_id'] : $max_id;
   }
   	$newid = ($max_id +1);
    $output .= " <tr class='".$eqdkp->switch_row_class()."'>
     							<td nowrap='nowrap'><input type='text' name='linkname[".$newid."]' size='20' maxlength='75' value='' class='input' /></td>
    							<td nowrap='nowrap'><input type='text' name='linkurl[".$newid."]' size='30' maxlength='75' value='' class='input' /></td>
  								<td nowrap='nowrap'>".$html->CheckBox('linkwindow['.$newid.']', '' , '')."</td>
  							</tr>
								</table>";
   // end of Links thing

   $output .= $tabs->endTab();
   $output .= $tabs->startTab($plang['pk_tab_listmemb'], 'config5');
// Listmembers
//
   $output .= "Leaderboard <br />";
   $output .= $html->CheckBox('pk_leaderboard', $plang['pk_set_leaderboard'] , $conf['pk_leaderboard'], $plang['pk_help_lm_leaderboard']);
   $output .= "<br />";
   $output .= $html->CheckBox('pk_leaderboard_2row', $plang['pk_set_leaderboard_2row'] , $conf['pk_leaderboard_2row'], $plang['pk_help_leaderboard_2row']);
   $output .= "<br />";
   $output .= $html->TextField('pk_leaderboard_limit', '2', $conf['pk_leaderboard_limit'], $plang['pk_set_leaderboard_limit'], $plang['pk_help_leaderboard_limit']);
   $output .= "<br />";
   $output .= $html->CheckBox('pk_leaderboard_hide_zero', $plang['pk_set_leaderboard_zero'] , $conf['pk_leaderboard_hide_zero'], $plang['pk_help_leaderboard_zero']);
   $output .= "<br />";
   $output .= $html-> DropDown('pk_default_multi', $a_multidkps , $conf['pk_default_multi'], $plang['pk_set_default_multi'], $plang['pk_help_default_multi']);
   $output .= "<hr>";

   $output .= "Listmembers <br />";
   $output .= $html->CheckBox('pk_rank', $plang['pk_set_rank'] , $conf['pk_rank'], $plang['pk_help_lm_rank']);
   $output .= "<br />";
   $output .= $html->CheckBox('pk_rank_icon', $plang['pk_set_rank_icon'] ,  $conf['pk_rank_icon'], $plang['pk_help_lm_rankicon']);
   $output .= "<br />";
   $output .= $html->CheckBox('pk_level', $plang['pk_set_level'] , $conf['pk_level'], $plang['pk_help_lm_level']);
   $output .= "<br />";
   $output .= $html->CheckBox('pk_lastloot', $plang['pk_set_lastloot'] , $conf['pk_lastloot'], $plang['pk_help_lm_lastloot']);
   $output .= "<br />";
   $output .= $html->CheckBox('pk_lastraid', $plang['pk_set_lastraid'] , $conf['pk_lastraid'], $plang['pk_help_lm_lastraid']);
   $output .= "<br />";
   $output .= $html->CheckBox('pk_attendance30', $plang['pk_set_attendance30'] , $conf['pk_attendance30'], $plang['pk_help_lm_atten30']);
   $output .= "<br />";
   $output .= $html->CheckBox('pk_attendance60', $plang['pk_set_attendance60'] , $conf['pk_attendance60'], $plang['pk_help_lm_atten60']);
   $output .= "<br />";
   $output .= $html->CheckBox('pk_attendance90', $plang['pk_set_attendance90'] , $conf['pk_attendance90'], $plang['pk_help_lm_atten90']);
   $output .= "<br />";
   $output .= $html->CheckBox('pk_attendanceAll', $plang['pk_set_attendanceAll'] , $conf['pk_attendanceAll'], $plang['pk_help_lm_attenall']);
   $output .= "<br />";
   $output .= $html->CheckBox('pk_showclasscolumn', $plang['pk_set_showclasscolumn'] , $conf['pk_showclasscolumn'], $plang['pk_help_showclasscolumn']);
   $output .= "<br />";

   if ($pm->check(PLUGIN_INSTALLED, 'charmanager'))
	 {
	   $output .= "<hr>";
	   $output .= "Charmanager Plugin:";
	   $output .= "<br />";
	   $output .= $html->CheckBox('pk_show_skill', $plang['pk_set_show_skill'] , $conf['pk_show_skill'], $plang['pk_help_show_skill']);
	   $output .= "<br />";
	   $output .= $html->CheckBox('pk_show_arkan_resi', $plang['pk_set_show_arkan_resi'] , $conf['pk_show_arkan_resi'], $plang['pk_help_show_arkan_resi']);
	   $output .= "<br />";
	   $output .= $html->CheckBox('pk_show_fire_resi', $plang['pk_set_show_fire_resi'] , $conf['pk_show_fire_resi'], $plang['pk_help_show_fire_resi']);
	   $output .= "<br />";
	   $output .= $html->CheckBox('pk_show_nature_resi', $plang['pk_set_show_nature_resi'] , $conf['pk_show_nature_resi'], $plang['pk_help_show_nature_resi']);
	   $output .= "<br />";
	   $output .= $html->CheckBox('pk_show_ice_resi', $plang['pk_set_show_ice_resi'] , $conf['pk_show_ice_resi'], $plang['pk_help_show_ice_resi']);
	   $output .= "<br />";
	   $output .= $html->CheckBox('pk_show_shadow_resi', $plang['pk_set_show_shadow_resi'] , $conf['pk_show_shadow_resi'], $plang['pk_help_show_shadow_resi']);
	   $output .= "<br />";
	   $output .= $html->CheckBox('pk_show_profiles', $plang['pk_set_show_profils'] , $conf['pk_show_profiles'], $plang['pk_help_show_profils']);
	   $output .= "<br />";
	 }


   $output .= $tabs->endTab();
   $output .= $tabs->startTab($plang['pk_tab_itemstats'], 'config6');
// Itemstats
   $output .= $html->CheckBox('pk_itemstats', $plang['pk_set_itemstats'] , $conf['pk_itemstats'], $plang['pk_help_itemstats_on']);
   $output .= "<br />";
   $output .= $html->CheckBox('pk_itemstats_debug', $plang['pk_set_itemstats_debug'] , $conf['pk_itemstats_debug'], $plang['pk_help_itemstats_debug']);
   $output .= "<br />";
   $output .= $html->CheckBox('pk_is_autosearch', $plang['pk_is_set_autosearch'] , $conf['pk_is_autosearch'], $plang['pk_is_help_autosearch']);

   $output .= "<br />";
   $output .= $html->TextField('pk_is_icon_ext', '10', $conf['pk_is_icon_ext'], $plang['pk_set_icon_ext'], $plang['pk_help_itemstats_icon_ext']);
   $output .= "<br />";
   $output .= $html->TextField('pk_is_icon_loc', '40', $conf['pk_is_icon_loc'], $plang['pk_set_icon_loc'], $plang['pk_help_itemstats_icon_url']);

   $output .= "<br />";
   $output .= $html->TextField('pk_is_patch_cache', '40', $conf['pk_is_patch_cache'], $plang['pk_is_set_patch_cache'], $plang['pk_is_help_patch_cache']);
   $output .= "<br />";
   $output .= $html->TextField('pk_is_path_sockets_image', '40', $conf['pk_is_path_sockets_image'], $plang['pk_is_set_patch_sockets'], $plang['pk_is_help_patch_sockets']);

   $output .= "<hr />";

   $output .= $plang['pk_is_set_prio'];
   $output .= "<br />";
   $output .= $html-> DropDown('pk_is_prio_first', $a_itemstats_site , $conf['pk_is_prio_first'], "1 ", $plang['pk_is_help_prio']);
   $output .= "<br />";
   $output .= $html-> DropDown('pk_is_prio_second', $a_itemstats_site , $conf['pk_is_prio_second'], "2 ", $plang['pk_is_help_prio']);
   $output .= "<br />";
   $output .= $html-> DropDown('pk_is_prio_third', $a_itemstats_site , $conf['pk_is_prio_third'], "3 ", $plang['pk_is_help_prio']);
   $output .= "<br />";
   $output .= $html-> DropDown('pk_is_prio_fourth', $a_itemstats_site , $conf['pk_is_prio_fourth'], "4 ", $plang['pk_is_help_prio']);
   $output .= "<br />";

   $output .= $html-> DropDown('pk_is_itemlanguage_alla', $a_allakhazam_language , $conf['pk_is_itemlanguage_alla'], $plang['pk_is_set_alla_lang'], $plang['pk_is_help_alla_lang']);
   $output .= "<br />";
   $output .= $html-> DropDown('pk_is_itemlanguage', $a_Item_default_language , $conf['pk_is_itemlanguage'], $plang['pk_is_set_lang'], $plang['pk_is_help_lang']);
   $output .= "<br />";

   #$output .= "<br />";
   #$output .= $html-> DropDown('pk_is_language', $itemstats_language , $conf['pk_is_language'], $plang['pk_is_language'], $plang['pk_help_itemstats_search']);

   #$output .= $html-> DropDown('pk_is_integration_mode', $a_is_integration_mode , $conf['pk_is_integration_mode'], $plang['pk_is_set_integration_mode'], $plang['pk_is_help_integration_mode']);
   #$output .= "<br />";
   #$output .= $html-> DropDown('pk_is_tooltip_js', $a_is_tooltip_displayer , $conf['pk_is_tooltip_js'], $plang['pk_is_set_tooltip_js'], $plang['pk_is_help_tooltip_js']);
   #$output .= "<br />";


   #$output .= "<br />";
   #$output .= $html->CheckBox('pk_trans_en_de', $plang['pk_set_en_de'] , $conf['pk_trans_en_de'], $plang['pk_help_itemstats_translate_deeng']);
   #$output .= "<br />";
   #$output .= $html->CheckBox('pk_trans_de_en', $plang['pk_set_de_en'] , $conf['pk_trans_de_en'], $plang['pk_help_itemstats_translate_engde']);
   $output .= $tabs->endTab();
   $output .= $tabs->endPane();
   $output .= "<br />";
   $output .= "<div align='center'>";
   $output .= $html->Button('save_plus', $plang['pk_save']);
   $output .= "</div>";
   $output .= $html->EndForm();
   echo $output;

?>