<?php
/******************************
 * EQDKP PLUGIN: PLUSkernel
 * (c) 2007 by EQDKP Plus Dev Team
 * originally written by S.Wallmann
 * http://www.eqdkp-plus.com
 * ------------------
 * settings.php
 * Start: 2006
 * $Id$
 ******************************/

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = '../';
$data_saved = false;
include_once($eqdkp_root_path . 'common.php');
include_once('include/html.class.php');
include_once('include/inc.wow_server_eu.php');
include_once('include/inc.wow_server_us.php');


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
$tabs = new Tabs();

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
	'allakhazam'       => "allakhazam",
	'buffed'           => "buffed",
	'armory'           => "armory",
	'wowhead'          => "wowhead",
	'thottbot'         => "thottbot",
	'judgehype'        => "judgehype",
	'wowdbu'           => "wowdbu"

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
	'normal'				=> "normal",
	'script'				=> "script",
);

//Item default language
$a_is_tooltip_displayer= array(
	'overlib'				=> "overlib",
	'light'					=> "light",
);

$a_debug_mode= array(
	'0'				=> $plang['pk_set_debug_type0'],
	'1'				=> $plang['pk_set_debug_type1'],
	'2'				=> $plang['pk_set_debug_type2'],
	'3'				=> $plang['pk_set_debug_type3'],
);

$a_rss_style = array(
	'0'				=> $plang['pk_set_Show_rss_style_both'],
	'1'				=> $plang['pk_set_Show_rss_style_v'],
	'2'				=> $plang['pk_set_Show_rss_style_h']
);

$a_rss_lang = array(
	'de'				=> $plang['pk_set_Show_rss_lang_de'],
	'eng'				=> $plang['pk_set_Show_rss_lang_eng']

);


echo "<script language='JavaScript' type='text/javascript' src='../itemstats/overlib/overlib.js'></script>";

// Save this shit
if ($_POST['save_plus']){
	// get an array with the config fields of the DB
		$isplusconfarray = $plusdb->CheckDBFields('plus_config', 'config_name');

  	// global config
    $plusdb->UpdateConfig('pk_class_color', $_POST['pk_class_color'], $isplusconfarray); # Coloured Classnames?
	$plusdb->UpdateConfig('pk_itemhistory_dia', $_POST['pk_itemhistory_dia'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_disable_comments', $_POST['pk_disable_comments'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_updatecheck', $_POST['pk_updatecheck'], $isplusconfarray); # Enable Update Warnings & VersionCheck
	$plusdb->UpdateConfig('pk_windowtime', $_POST['pk_windowtime'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_newsloot_limit', $_POST['pk_newsloot_limit'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_servername', htmlspecialchars_decode($_POST['pk_servername'], ENT_QUOTES), $isplusconfarray);
	$plusdb->UpdateConfig('pk_server_region', $_POST['pk_server_region'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_round_activate', $_POST['pk_round_activate'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_round_precision', $_POST['pk_round_precision'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_debug', $_POST['pk_debug'], $isplusconfarray);

	//Portal
	$plusdb->UpdateConfig('pk_show_dkpinfo', $_POST['pk_show_dkpinfo'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_quickdkp', $_POST['pk_quickdkp'], $isplusconfarray); # Show Quickdkp?
	$plusdb->UpdateConfig('pk_nextraids_deactive', $_POST['pk_nextraids_deactive'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_nextraids_limit', $_POST['pk_nextraids_limit'], $isplusconfarray);

	$plusdb->UpdateConfig('pk_last_items_deactive', $_POST['pk_last_items_deactive'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_last_items_limit', $_POST['pk_last_items_limit'], $isplusconfarray);


	//Rss
	$plusdb->UpdateConfig('pk_showRss', $_POST['pk_showRss'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_Rss_Style', $_POST['pk_Rss_Style'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_Rss_lang', $_POST['pk_Rss_lang'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_Rss_count', $_POST['pk_Rss_count'], $isplusconfarray);

	//Contact
	$plusdb->UpdateConfig('pk_contact_disable', $_POST['pk_contact_disable'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_contact_name', $_POST['pk_contact_name'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_contact_email', $_POST['pk_contact_email'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_contact_website', $_POST['pk_contact_website'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_contact_irc', $_POST['pk_contact_irc'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_contact_admin_messenger', $_POST['pk_contact_admin_messenger'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_contact_custominfos', $_POST['pk_contact_custominfos'], $isplusconfarray);

	// multidkp
	$plusdb->UpdateConfig('pk_multidkp', $_POST['pk_multidkp'], $isplusconfarray); # Multidkp on/off
	$plusdb->UpdateConfig('pk_multiTooltip', $_POST['pk_multiTooltip'], $isplusconfarray); # Multidkp on/off
	$plusdb->UpdateConfig('pk_multiSmarttip', $_POST['pk_multiSmarttip'], $isplusconfarray); # Multidkp on/off
	$plusdb->UpdateConfig('pk_default_multi', $_POST['pk_default_multi'], $isplusconfarray); # Multidkp on/off

	// Listmembers
	$plusdb->UpdateConfig('pk_leaderboard', $_POST['pk_leaderboard'], $isplusconfarray); # Show the leaderbaord?
	#$plusdb->UpdateConfig('pk_leaderboard_2row', $_POST['pk_leaderboard_2row'], $isplusconfarray); # 2 reihen?
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
	$plusdb->UpdateConfig('pk_is_autosearch', $_POST['pk_is_autosearch'], $isplusconfarray);   # If the object is not on the cache, Itemstats will search it on data website (Allakhazam, etc.)
	$plusdb->UpdateConfig('pk_is_patch_cache', $_POST['pk_is_patch_cache'], $isplusconfarray);   #The path for custom item, it's based on Itemstats directory path.

	if(strtolower($eqdkp->config['default_game']) == 'wow')
	{
		$plusdb->UpdateConfig('pk_is_icon_loc', $_POST['pk_is_icon_loc'], $isplusconfarray);     # Icon Location
		$plusdb->UpdateConfig('pk_is_icon_ext', $_POST['pk_is_icon_ext'], $isplusconfarray);     # Itemextension
		$plusdb->UpdateConfig('pk_is_path_sockets_image', $_POST['pk_is_path_sockets_image'], $isplusconfarray);   # Sockets images path (only for Allakhazam and Buffed objects)
		$plusdb->UpdateConfig('pk_is_prio_first', $_POST['pk_is_prio_first'], $isplusconfarray);   # First Site to Search for
		$plusdb->UpdateConfig('pk_is_prio_second', $_POST['pk_is_prio_second'], $isplusconfarray); # Second Site to Search for
		$plusdb->UpdateConfig('pk_is_prio_third', $_POST['pk_is_prio_third'], $isplusconfarray);   # Third Site to Search for
		$plusdb->UpdateConfig('pk_is_prio_fourth', $_POST['pk_is_prio_fourth'], $isplusconfarray); # Fourth Site to Search for

		$plusdb->UpdateConfig('pk_is_itemlanguage', $_POST['pk_is_itemlanguage'], $isplusconfarray);   # Language default for Item's Id when not specified
		$plusdb->UpdateConfig('pk_is_itemlanguage_alla', $_POST['pk_is_itemlanguage_alla'], $isplusconfarray);   #Allakhazam languages search
	}

	//Bridge
	$plusdb->UpdateConfig('pk_bridge_cms_active', 		$_POST['pk_bridge_cms_active'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_bridge_cms_otherDB', 		$_POST['pk_bridge_cms_otherDB'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_bridge_cms_deac_reg', 	$_POST['pk_bridge_cms_deac_reg'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_bridge_cms_sel', 			$_POST['pk_bridge_cms_sel'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_bridge_cms_host', 		$_POST['pk_bridge_cms_host'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_bridge_cms_user', 		$_POST['pk_bridge_cms_user'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_bridge_cms_pass', 		$_POST['pk_bridge_cms_pass'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_bridge_cms_db', 			$_POST['pk_bridge_cms_db'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_bridge_cms_tableprefix', 	$_POST['pk_bridge_cms_tableprefix'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_bridge_cms_group', 		$_POST['pk_bridge_cms_group'], $isplusconfarray);

	$plusdb->UpdateConfig('pk_bridge_cms_InlineUrl', 	$_POST['pk_bridge_cms_InlineUrl'], $isplusconfarray);

	#recrutment
	$plusdb->ProcesspRecruitment($_POST['pk_recruitment']);
	$plusdb->UpdateConfig('pk_recruitment_active', 		$_POST['pk_recruitment_active'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_recruitment_url', 		$_POST['pk_recruitment_url'], $isplusconfarray);


	$data_saved = true;
} # if save plus

  // get the config
  $conf = $plusdb->InitConfig();

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

	##############################################################################################################################
	########################################################### OUTPUT ###########################################################
	##############################################################################################################################

	// Output
   $output  = ($data_saved == true) ? $html->MsgBox($plang['pk_save_title'], $plang['pk_succ_saved'], 'images/ok.png', '90%', true, '36px', '36px', true).'<br/>' : '';
   $output .= $html->StartForm('settings', 'settings.php');
   $output .= $tabs->startPane('config');
   $output .= $tabs->startTab($plang['pk_tab_global'], 'config1');

   // Global
   $output .= $tabs->startTable();
   $output .= $tabs->tableheader('Global');
   $output .= $html->CheckBox('pk_class_color', $plang['pk_set_ClassColor'] , $conf['pk_class_color'], $plang['pk_help_colorclassnames']);
   $output .= $html->CheckBox('pk_itemhistory_dia', $plang['pk_set_itemhistory_dia'] , $conf['pk_itemhistory_dia'], $plang['pk_help_itemhistory_dia']);
   $output .= $html->CheckBox('pk_disable_comments', $plang['pk_set_comments_disable'] , $conf['pk_disable_comments'], $plang['pk_hel_pcomments_disable']);
   $output .= $html->CheckBox('pk_round_activate', $plang['pk_set_round_activate'] , $conf['pk_round_activate'], $plang['pk_help_round_activate']);
   $output .= $html->TextField('pk_round_precision', '2', $conf['pk_round_precision'] ,$plang['pk_set_round_precision'] , $plang['pk_help_round_precision']) ;
   $output .= $html->DropDown('pk_newsloot_limit', $newsloot_limit , $conf['pk_newsloot_limit'],$plang['pk_set_newsloot_limit'], $plang['pk_help_newsloot_limit'] );
   $output .= $html->CheckBox('pk_updatecheck', $plang['pk_set_Updatecheck'] , $conf['pk_updatecheck'], $plang['pk_help_autowarning']);
   $output .= $html->TextField('pk_windowtime', '4', $conf['pk_windowtime'] ,$plang['pk_window_time1']." ".$plang['pk_window_time2'] , $plang['pk_help_warningtime']);
   $output .= $html->TextField('pk_servername', '20', htmlspecialchars($conf['pk_servername'], ENT_QUOTES) ,$plang['pk_set_servername'] , $plang['pk_help_servername']);
   $output .= $html->DropDown('pk_server_region', $realm_region , $conf['pk_server_region'], $plang[pk_set_server_region], $plang[pk_help_server_region]);

   #$output .= $html->DropDown('pk_server_eu', $se ,'', 'EU Server', $plang[pk_help_server_region]); -> Dropdown with WoW-EU Server
   #$output .= $html->DropDown('pk_server_us', $s ,'', 'US Server', $plang[pk_help_server_region]); -> Dropdown with WoW-US Server

   $output .= $html->DropDown('pk_debug', $a_debug_mode , $conf['pk_debug'], $plang[pk_set_debug], $plang[pk_help_debug]);

   #Portal
   $output .= $tabs->tableheader('Portal');
   $output .= $html->CheckBox('pk_quickdkp', $plang['pk_set_QuickDKP'] , $conf['pk_quickdkp'], $plang['pk_help_quickdkp']);
   $output .= $html->CheckBox('pk_show_dkpinfo', $plang['pk_set_dkp_info'] , $conf['pk_show_dkpinfo'], $plang['pk_help_dkp_info']);
   #$output .= $html->CheckBox('pk_nextraids_deactive', $plang['pk_set_nextraids_deactive'] , $conf['pk_nextraids_deactive'], $plang['pk_help_nextraids_deactive']);
   #$output .= $html->TextField('pk_nextraids_limit', '2', intval($conf['pk_nextraids_limit']) ,$plang['pk_set_nextraids_limit'] , $plang['pk_help_nextraids_limit']);
   #$output .= $html->CheckBox('pk_last_items_deactive', $plang['pk_set_lastitems_deactive'] , $conf['pk_last_items_deactive'], $plang['pk_help_lastitems_deactive']);
   #$output .= $html->TextField('pk_last_items_limit', '2', intval($conf['pk_last_items_limit']) ,$plang['pk_set_lastitems_limit'] , $plang['pk_help_lastitems_limit']);

   //Rss
   $output .= $tabs->tableheader('RSS-News');
   $output .= $html->CheckBox('pk_showRss', $plang['pk_set_Show_rss'] , $conf['pk_showRss'], $plang['pk_help_Show_rss']);
   $output .= $html->DropDown('pk_Rss_Style', $a_rss_style , $conf['pk_Rss_Style'],$plang['pk_set_Show_rss_style'], $plang['pk_help_Show_rss_style'] );
   $output .= $html->DropDown('pk_Rss_lang', $a_rss_lang , $conf['pk_Rss_lang'],$plang['pk_set_Show_rss_lang'], $plang['pk_help_Show_rss_lang'] );
   $output .= $html->TextField('pk_Rss_count', '5', $conf['pk_Rss_count'] ,$plang['pk_set_Show_rss_count'] , $plang['pk_help_Show_rss_count']);

   //Contact
   $output .= $tabs->tableheader($plang['pk_contact']);
   #$output .= $html->CheckBox('pk_contact_disable', $plang['pk_contact_disable'] , $conf['pk_contact_disable'], $plang['pk_contact_disable']);
   $output .= $html->TextField('pk_contact_name', '40', htmlspecialchars($conf['pk_contact_name'], ENT_QUOTES) ,$plang['pk_contact_name'] , $plang['pk_contact_name']);
   $output .= $html->TextField('pk_contact_email', '40', htmlspecialchars($conf['pk_contact_email'], ENT_QUOTES) ,$plang['pk_contact_email'] , $plang['pk_contact_email']);
   $output .= $html->TextField('pk_contact_website', '40', htmlspecialchars($conf['pk_contact_website'], ENT_QUOTES) ,$plang['pk_contact_website'] , $plang['pk_contact_website']);
   $output .= $html->TextField('pk_contact_irc', '40', htmlspecialchars($conf['pk_contact_irc'], ENT_QUOTES) ,$plang['pk_contact_irc'] , $plang['pk_contact_irc']);
   $output .= $html->TextField('pk_contact_admin_messenger', '40', htmlspecialchars($conf['pk_contact_admin_messenger'], ENT_QUOTES) ,$plang['pk_contact_admin_messenger'] , $plang['pk_contact_admin_messenger']);
   $output .= $html->TextField('pk_contact_custominfos', '50', htmlspecialchars($conf['pk_contact_custominfos'], ENT_QUOTES) ,$plang['pk_contact_custominfos'] , $plang['pk_contact_custominfos']);

   $output .= $tabs->endTable() ;
   $output .= $tabs->endTab();


   // MultiDKP
   $output .= $tabs->startTab($plang['pk_tab_multidkp'], 'config2');
   $output .= $tabs->startTable();
   $output .= $tabs->tableheader('MultiDKP');
   $output .= $html->CheckBox('pk_multidkp', $plang['pk_set_multidkp'] , $conf['pk_multidkp'], $plang['pk_help_multidkp']);
   $output .= $html->CheckBox('pk_multiTooltip', $plang['pk_set_multi_Tooltip'] , $conf['pk_multiTooltip'],$plang['pk_help_dkptooltip']);
   $output .= $html->CheckBox('pk_multiSmarttip', $plang['pk_set_multi_smartTooltip'] , $conf['pk_multiSmarttip'],$plang['pk_help_smarttooltip']);
   $output .= $tabs->endTable() ;
   $output .= $tabs->endTab();

	// Links
   $output .= $tabs->startTab($plang['pk_tab_links'], 'config3');
   $output .= $tabs->startTable();
   $output .= $tabs->tableheader('Links');
   $output .= $html->CheckBox('pk_links', $plang['pk_set_links'] , $conf['pk_links'], $plang['pk_help_links']);
   $output .= $tabs->endTable() ;

   // the add Link thing
   $output .= "<table width='100%' border='0' cellspacing='1' cellpadding='2'>
  								<tr>
    								<th align='left'>".$plang['pk_set_linkname']."</th>
    								<th align='left'>".$plang['pk_set_linkurl']."</th>
    								<th align='left'>".$plang['pk_set_link_type_header']."</th>
  								</tr>";


  // get the links stuff
	$customlink = array();
 	$sql = 'SELECT link_id, link_name, link_url, link_window
         	FROM '.PLUS_LINKS_TABLE.' ORDER BY link_id';
  $result = $db->query($sql);
   while ( $row = $db->fetch_record($result) )
   {
   		$output .= "<tr class='".$eqdkp->switch_row_class()."'>
    							<td nowrap='nowrap'><input type='text' name='linkname[".$row['link_id']."]' size='30' maxlength='85' value='".$row['link_name']."' class='input' /></td>
    							<td nowrap='nowrap'><input type='text' name='linkurl[".$row['link_id']."]' size='30' maxlength='85' value='".$row['link_url']."' class='input' /></td>
 								<td nowrap='nowrap'>".
   								$html->RadioBox('linkwindow['.$row['link_id'].']', $plang['pk_set_link_type_self'] , $row['link_window'],$plang['pk_set_link_type_help'] ,0,true).' '.
   								$html->RadioBox('linkwindow['.$row['link_id'].']', $plang['pk_set_link_type_link'] , $row['link_window'],'',1,true).' '.
   								$html->RadioBox('linkwindow['.$row['link_id'].']', $plang['pk_set_link_type_iframe'] , $row['link_window'],$plang['pk_set_link_type_iframe_help'],2,true)
   								."</td></tr>";
 			$max_id = ( $max_id < $row['link_id'] ) ? $row['link_id'] : $max_id;
   }
   	$newid = ($max_id +1);
    $output .= " <tr class='".$eqdkp->switch_row_class()."'>
     							<td nowrap='nowrap'><input type='text' name='linkname[".$newid."]' size='35' maxlength='85' value='' class='input' /></td>
    							<td nowrap='nowrap'><input type='text' name='linkurl[".$newid."]' size='35' maxlength='85' value='' class='input' /></td>
  								<td nowrap='nowrap'>".
    							$html->RadioBox('linkwindow['.$newid.']', $plang['pk_set_link_type_self'] , '',$plang['pk_set_link_type_help'],0,true).' '.
    							$html->RadioBox('linkwindow['.$newid.']', $plang['pk_set_link_type_link'] , '','',1,true).' '.
    							$html->RadioBox('linkwindow['.$newid.']', $plang['pk_set_link_type_iframe'] , '',$plang['pk_set_link_type_iframe_help'],2,true)
    							."</td>
  							</tr>
								</table>";
   $output .= $tabs->endTab();

	// Listmembers
   $output .= $tabs->startTab($plang['pk_tab_listmemb'], 'config5');
   $output .= $tabs->startTable();
   $output .= $tabs->tableheader('Leaderboard');
   $output .= $html->CheckBox('pk_leaderboard', $plang['pk_set_leaderboard'] , $conf['pk_leaderboard'], $plang['pk_help_lm_leaderboard']);
   #$output .= $html->CheckBox('pk_leaderboard_2row', $plang['pk_set_leaderboard_2row'] , $conf['pk_leaderboard_2row'], $plang['pk_help_leaderboard_2row']);
   $output .= $html->TextField('pk_leaderboard_limit', '2', $conf['pk_leaderboard_limit'], $plang['pk_set_leaderboard_limit'], $plang['pk_help_leaderboard_limit']);
   $output .= $html->CheckBox('pk_leaderboard_hide_zero', $plang['pk_set_leaderboard_zero'] , $conf['pk_leaderboard_hide_zero'], $plang['pk_help_leaderboard_zero']);
   $output .= $html-> DropDown('pk_default_multi', $a_multidkps , $conf['pk_default_multi'], $plang['pk_set_default_multi'], $plang['pk_help_default_multi']);
   $output .= $tabs->tableheader('Listmembers');
   $output .= $html->CheckBox('pk_rank', $plang['pk_set_rank'] , $conf['pk_rank'], $plang['pk_help_lm_rank']);
   $output .= $html->CheckBox('pk_rank_icon', $plang['pk_set_rank_icon'] ,  $conf['pk_rank_icon'], $plang['pk_help_lm_rankicon']);
   $output .= $html->CheckBox('pk_level', $plang['pk_set_level'] , $conf['pk_level'], $plang['pk_help_lm_level']);
   $output .= $html->CheckBox('pk_lastloot', $plang['pk_set_lastloot'] , $conf['pk_lastloot'], $plang['pk_help_lm_lastloot']);
   $output .= $html->CheckBox('pk_lastraid', $plang['pk_set_lastraid'] , $conf['pk_lastraid'], $plang['pk_help_lm_lastraid']);
   $output .= $html->CheckBox('pk_attendance30', $plang['pk_set_attendance30'] , $conf['pk_attendance30'], $plang['pk_help_lm_atten30']);
   $output .= $html->CheckBox('pk_attendance60', $plang['pk_set_attendance60'] , $conf['pk_attendance60'], $plang['pk_help_lm_atten60']);
   $output .= $html->CheckBox('pk_attendance90', $plang['pk_set_attendance90'] , $conf['pk_attendance90'], $plang['pk_help_lm_atten90']);
   $output .= $html->CheckBox('pk_attendanceAll', $plang['pk_set_attendanceAll'] , $conf['pk_attendanceAll'], $plang['pk_help_lm_attenall']);
   $output .= $html->CheckBox('pk_showclasscolumn', $plang['pk_set_showclasscolumn'] , $conf['pk_showclasscolumn'], $plang['pk_help_showclasscolumn']);


   if ($pm->check(PLUGIN_INSTALLED, 'charmanager'))
	 {

	   $output .= $tabs->tableheader('Charmanager Plugin');
	   $output .= $html->CheckBox('pk_show_skill', $plang['pk_set_show_skill'] , $conf['pk_show_skill'], $plang['pk_help_show_skill']);
	   $output .= $html->CheckBox('pk_show_arkan_resi', $plang['pk_set_show_arkan_resi'] , $conf['pk_show_arkan_resi'], $plang['pk_help_show_arkan_resi']);
	   $output .= $html->CheckBox('pk_show_fire_resi', $plang['pk_set_show_fire_resi'] , $conf['pk_show_fire_resi'], $plang['pk_help_show_fire_resi']);
	   $output .= $html->CheckBox('pk_show_nature_resi', $plang['pk_set_show_nature_resi'] , $conf['pk_show_nature_resi'], $plang['pk_help_show_nature_resi']);
	   $output .= $html->CheckBox('pk_show_ice_resi', $plang['pk_set_show_ice_resi'] , $conf['pk_show_ice_resi'], $plang['pk_help_show_ice_resi']);
	   $output .= $html->CheckBox('pk_show_shadow_resi', $plang['pk_set_show_shadow_resi'] , $conf['pk_show_shadow_resi'], $plang['pk_help_show_shadow_resi']);
	   $output .= $html->CheckBox('pk_show_profiles', $plang['pk_set_show_profils'] , $conf['pk_show_profiles'], $plang['pk_help_show_profils']);
	 }

   $output .= $tabs->endTable() ;
   $output .= $tabs->endTab();


   // Itemstats
   if( (strtolower($eqdkp->config['default_game']) == 'wow') or (strtolower($eqdkp->config['default_game']) == 'lotro'))
   {
	   $output .= $tabs->startTab($plang['pk_tab_itemstats'], 'config6');
	   $output .= $tabs->startTable();
	   $output .= $tabs->tableheader('Itemstats');
	   $output .= $html->CheckBox('pk_itemstats', $plang['pk_set_itemstats'] , $conf['pk_itemstats'], $plang['pk_help_itemstats_on']);
	   $output .= $html->CheckBox('pk_itemstats_debug', $plang['pk_set_itemstats_debug'] , $conf['pk_itemstats_debug'], $plang['pk_help_itemstats_debug']);
	   $output .= $html->CheckBox('pk_is_autosearch', $plang['pk_is_set_autosearch'] , $conf['pk_is_autosearch'], $plang['pk_is_help_autosearch']);
	   $output .= $html->TextField('pk_is_patch_cache', '40', $conf['pk_is_patch_cache'], $plang['pk_is_set_patch_cache'], $plang['pk_is_help_patch_cache']);

	   	if(strtolower($eqdkp->config['default_game']) == 'wow')
		{
		   $output .= $html->TextField('pk_is_path_sockets_image', '40', $conf['pk_is_path_sockets_image'], $plang['pk_is_set_patch_sockets'], $plang['pk_is_help_patch_sockets']);

		   $output .= $tabs->tableheader($plang['pk_is_set_prio']);
		   $output .= $html->TextField('pk_is_icon_ext', '10', $conf['pk_is_icon_ext'], $plang['pk_set_icon_ext'], $plang['pk_help_itemstats_icon_ext']);
		   $output .= $html->TextField('pk_is_icon_loc', '40', $conf['pk_is_icon_loc'], $plang['pk_set_icon_loc'], $plang['pk_help_itemstats_icon_url']);
		   $output .= $html-> DropDown('pk_is_prio_first', $a_itemstats_site , $conf['pk_is_prio_first'], "1 ", $plang['pk_is_help_prio']);
		   $output .= $html-> DropDown('pk_is_prio_second', $a_itemstats_site , $conf['pk_is_prio_second'], "2 ", $plang['pk_is_help_prio']);
		   $output .= $html-> DropDown('pk_is_prio_third', $a_itemstats_site , $conf['pk_is_prio_third'], "3 ", $plang['pk_is_help_prio']);
		   $output .= $html-> DropDown('pk_is_prio_fourth', $a_itemstats_site , $conf['pk_is_prio_fourth'], "4 ", $plang['pk_is_help_prio']);
		   $output .= $html-> DropDown('pk_is_itemlanguage_alla', $a_allakhazam_language , $conf['pk_is_itemlanguage_alla'], $plang['pk_is_set_alla_lang'], $plang['pk_is_help_alla_lang']);
		   $output .= $html-> DropDown('pk_is_itemlanguage', $a_Item_default_language , $conf['pk_is_itemlanguage'], $plang['pk_is_set_lang'], $plang['pk_is_help_lang']);
		}
	   $output .= $tabs->tableheader('Update');
	   $output .= $tabs->tablerow($plang['pk_is_help']. '<a href='.$eqdkp_root_path.'admin/updateitemstats.php>Update Itemstats</a>');



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

	   $output .= $tabs->endTable() ;
	   $output .= $tabs->endTab();

   }

	$a_bridges = array(
	'phpbb3'				=> 'phpBB 3',
	'phpbb2'                => 'phpBB 2',
	'joomla'				=> 'Joomla',
	'vbulletin'				=> 'VBulletin Board 3',
	'e107'                  => 'e107 CMS',
	'wbb3'                  => 'Woltlab Burning Board 3',
	'smf'                   => 'Simple Machines Forum'
	);

   //Bridge
   $output .= $tabs->startTab('CMS-Bridge', 'config7');
   $output .= $tabs->startTable();
   $output .= $tabs->tableheader('Bridge Config');
   $output .= $tabs->tablerow($plang['pk_set_bridge_help']);
   $output .= $html->CheckBox('pk_bridge_cms_active', $plang['pk_set_bridge_activate'] , $conf['pk_bridge_cms_active'], $plang['pk_help_bridge_activate']);
   $output .= $html->CheckBox('pk_bridge_cms_deac_reg', $plang['pk_set_bridge_dectivate_eq_reg'] , $conf['pk_bridge_cms_deac_reg'], $plang['pk_help_bridge_dectivate_eq_reg']);
   $output .= $html->DropDown('pk_bridge_cms_sel', $a_bridges , $conf['pk_bridge_cms_sel'], $plang['pk_set_bridge_cms'], $plang['pk_help_bridge_cms']);
   $output .= $html->TextField('pk_bridge_cms_tableprefix', '20', $conf['pk_bridge_cms_tableprefix'], $plang['pk_set_bridge_prefix'], $plang['pk_help_bridge_prefix']);
   $output .= $html->TextField('pk_bridge_cms_group', '20', $conf['pk_bridge_cms_group'], $plang['pk_set_bridge_group'], $plang['pk_help_bridge_group']);
   $output .= $html->CheckBox('pk_bridge_cms_otherDB', $plang['pk_set_bridge_acess'] , $conf['pk_bridge_cms_otherDB'], $plang['pk_help_bridge_acess']);

   $output .= $tabs->tableheader($plang['pk_help_bridge_acess']);
   $output .= $html->TextField('pk_bridge_cms_db', '20', $conf['pk_bridge_cms_db'],$plang['pk_set_bridge_database'], $plang['pk_help_bridge_database']);
   $output .= $html->TextField('pk_bridge_cms_host', '20', $conf['pk_bridge_cms_host'], $plang['pk_set_bridge_host'], $plang['pk_help_bridge_host']);
   $output .= $html->TextField('pk_bridge_cms_user', '20', $conf['pk_bridge_cms_user'], $plang['pk_set_bridge_username'], $plang['pk_help_bridge_username']);
   $output .= $html->TextField('pk_bridge_cms_pass', '20', $conf['pk_bridge_cms_pass'], $plang['pk_set_bridge_password'], $plang['pk_help_bridge_password'],'password');

   $output .= $tabs->tableheader($plang['pk_set_bridge_inline']);
   $output .= $tabs->tablerow($plang['pk_help_bridge_inline']);
   $output .= $html->TextField('pk_bridge_cms_InlineUrl', '40', $conf['pk_bridge_cms_InlineUrl'], $plang['pk_set_bridge_inline_url'], $plang['pk_help_bridge_inline_url']);
   $output .= $tabs->endTable() ;
   $output .= $tabs->endTab();

   //recruitment

   $output .= $tabs->startTab($plang['pk_set_recruitment_tab'], 'config8');
   $output .= $tabs->startTable();
   $output .= $tabs->tableheader($plang['pk_set_recruitment_header']);
   $output .= $html->CheckBox('pk_recruitment_active', $plang['pk_set_recruitment'] , $conf['pk_recruitment_active'], $plang['pk_help_recruitment']);

   $output .= $html->TextField('pk_recruitment_url', '40', $conf['pk_recruitment_url'], $plang['pk_set_recruitment_contact_type'], $plang['pk_help_recruitment_contact_type']);
   $output .= $tabs->endTable() ;

   $output .= "<table width='100%' border='0' cellspacing='1' cellpadding='2'>
   				<tr>
				<th align='left'>".$plang['pk_recruitment_count']."</th>
				<th align='left'>".$plang['ps_recruitment_spec']."</th>
				<th align='left'>".$user->lang['class']."</th>
				</tr>";

   	$customlink = array();
 	$sql = 'SELECT class_id ,class_name
         	FROM '.CLASS_TABLE.' ORDER BY class_name';
  	$result = $db->query($sql);

   	while ( $row = $db->fetch_record($result) )
   	{

		if($eqdkp->config['default_game'] == 'WoW')
		{
			$i = 0 ;
			$specArray = $user->lang['talents'][renameClasstoenglish($row['class_name'])];
			if (is_array($specArray))
			{
		   		foreach ($specArray as $specname)
		   		{

		   		   $img = $eqdkp_root_path."games/WoW/talents/".strtolower(renameClasstoenglish($row['class_name'])).$i++.".png" ;
		   		   if (file_exists($img)) {
		   		   	$icon = "<img src='".$img."' alt='".$img."'>";
		   		   }

		   		   $output .= "<tr class='".$eqdkp->switch_row_class()."'>" ;
		   	   	   $output .= "<td>".$plang['pk_recruitment_count'];
			   	   $output .= $html->TextField("pk_recruitment[".$row['class_id']."][".$i."]", '3', $conf['pk_recruitment_class['.$row['class_id'].']['.$i.']'],'', '','text',true)."</td>" ;
			   	   $output .= "<td >".$icon.$specname. " </td>";
			   	   $output .= "<td >".get_ClassIcon($row['class_name']).' '.$row['class_name']. " </td>";
			   	   $output .= "</tr>";
		   		}
			}else
			{
		   	   $output .= "<tr class='".$eqdkp->switch_row_class()."'>" ;
		   	   $output .= "<td>".$plang['pk_recruitment_count'];
		   	   $output .= $html->TextField("pk_recruitment[".$row['class_id']."]", '3', $conf['pk_recruitment_class['.$row['class_id'].']'],'', '','text',true)."</td>" ;
		   	   $output .= "<td colspan=2>".get_ClassIcon($row['class_name']).' '.$row['class_name']. " </td>";
		   	   $output .= "</tr>";
			}

   		}else
   		{
	   	   $output .= "<tr class='".$eqdkp->switch_row_class()."'>" ;
	   	   $output .= "<td>".$plang['pk_recruitment_count'];
	   	   $output .= $html->TextField("pk_recruitment[".$row['class_id']."]", '3', $conf['pk_recruitment_class['.$row['class_id'].']'],'', '','text',true)."</td>" ;
	   	   $output .= "<td colspan=2>".get_ClassIcon($row['class_name']).' '.$row['class_name']. " </td>";
	   	   $output .= "</tr>";
   		}

   	}
   $output .= "</table>";


   //Close
   $output .= $tabs->endPane();
   $output .= "<br />";
   $output .= "<div align='center'>";
   $output .= $html->Button('save_plus', $plang['pk_save']);
   $output .= "</div>";
   $output .= $html->EndForm();

    $tpl->assign_vars(array(
	        'OUTPUT'			=> $output,
	  ));

   $eqdkp->set_vars(array(
    'page_title'    => sprintf($user->lang['title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['listraids_title'],
    'template_file' => 'admin/plussettings.html',
    'display'       => true)
);

?>

