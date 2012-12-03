<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2006
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2006-2008 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev$
 *
 * $Id$
 */

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = '../';
$data_saved = false;
include_once($eqdkp_root_path . 'common.php');
include_once('include/html.class.php');

// Load the language
$plang = $pluslang->NormalLanguage();

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
	'kr'							=> "kr",
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

	//Databases for Itemsearch
$a_itemstats_kombis= array(
	'armory_wowhead'   => "Armory & WoWHead",
	'wowhead_armory'   => "WoWHead & Armory",
	'armory'   		   => "armory",
	'wowhead'   	   => "wowhead",
	'buffed'           => "buffed"
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

//ItemStats max_execution_time
$is_abs_max = (ini_get('max_execution_time')*0.9);
$is_max_execution_times = array();
for($i=0; $i <= $is_abs_max; $i++){
  $is_max_execution_times[$i] = $i;
}

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

$a_modelviewer = array(
	'0'				=> 'WoWHead',
	'1'				=> 'Thottbot',
	'2'				=> 'SpeedyDragon'
);

 $a_air_theme=array(
 	'grey' => $plang['pk_air_lytebox_grey'],
 	'red' => $plang['pk_air_lytebox_red'],
 	'blue' => $plang['pk_air_lytebox_blue'],
 	'green' => $plang['pk_air_lytebox_green'],
 	'gold' => $plang['pk_air_lytebox_gold']
 	);

$a_factions[0] = '';
$sql = "SELECT faction_id, faction_name from __factions";
if (($faction_result = $db->query($sql)))
{
	while ($fac = $db->fetch_record($faction_result) )
	{
		$a_factions[$fac['faction_id']] = $fac['faction_name'];
	}
}

// Save this shit
if ($_POST['save_plus']){

	//Clear Cache
	$pdc->del_suffix('');
    System_Message($user->lang['plus_cache_reset_done'],$user->lang['plus_cache_reset_name']);

	// get an array with the config fields of the DB
	$isplusconfarray = $plusdb->CheckDBFields('plus_config', 'config_name');

  	// global config
  	$plusdb->UpdateConfig('pk_class_color', $_POST['pk_class_color'], $isplusconfarray); # Coloured Classnames?
	$plusdb->UpdateConfig('pk_itemhistory_dia', $_POST['pk_itemhistory_dia'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_disable_comments', $_POST['pk_disable_comments'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_disable_3dmember', $_POST['pk_disable_3dmember'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_disable_3ditem', $_POST['pk_disable_3ditem'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_default_modelviewer', $_POST['pk_default_modelviewer'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_newsloot_limit', $_POST['pk_newsloot_limit'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_servername', htmlspecialchars_decode($_POST['pk_servername'], ENT_QUOTES), $isplusconfarray);
	$plusdb->UpdateConfig('pk_server_region', $_POST['pk_server_region'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_faction', $_POST['pk_faction'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_round_activate', $_POST['pk_round_activate'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_round_precision', $_POST['pk_round_precision'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_debug', $_POST['pk_debug'], $isplusconfarray);

   if (!$_HMODE)
    {
		$plusdb->UpdateConfig('pk_hide_shop', $_POST['pk_hide_shop'], $isplusconfarray);
		$plusdb->UpdateConfig('pk_updatecheck', $_POST['pk_updatecheck'], $isplusconfarray); # Enable Update Warnings & VersionCheck
		$plusdb->UpdateConfig('pk_windowtime', $_POST['pk_windowtime'], $isplusconfarray);
    }


	//NoDKP
	$plusdb->UpdateConfig('pk_noDKP', $_POST['pk_noDKP'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_noRoster', $_POST['pk_noRoster'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_noRaids', $_POST['pk_noRaids'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_noEvents', $_POST['pk_noEvents'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_noItemPrices', $_POST['pk_noItemPrices'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_noItemHistoy', $_POST['pk_noItemHistoy'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_noStats', $_POST['pk_noStats'], $isplusconfarray);

	//Rss
   if (!$_HMODE)
    {
		$plusdb->UpdateConfig('pk_showRss', $_POST['pk_showRss'], $isplusconfarray);
    }

	$plusdb->UpdateConfig('pk_Rss_Style', $_POST['pk_Rss_Style'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_Rss_lang', $_POST['pk_Rss_lang'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_Rss_count', $_POST['pk_Rss_count'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_Rss_checkURL', $_POST['pk_Rss_checkURL'], $isplusconfarray);


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
	$plusdb->UpdateConfig('pk_leaderboard_normal', $_POST['pk_leaderboard_normal'], $isplusconfarray); # zeige spieler mit 0 dkp?

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
	$plusdb->ProcessLinks($_POST['linkname'], $_POST['linkurl'], $_POST['linkwindow'], $_POST['link_menu'],  $isplusconfarray); # Save the links in the Database

	// Itemstats
	$plusdb->UpdateConfig('pk_itemstats', $_POST['pk_itemstats'], $isplusconfarray);   # Enable Itemstats
	$plusdb->UpdateConfig('pk_itemstats_debug', $_POST['pk_itemstats_debug'], $isplusconfarray);   # Debug
	$plusdb->UpdateConfig('pk_is_autosearch', $_POST['pk_is_autosearch'], $isplusconfarray);   # If the object is not on the cache, Itemstats will search it on data website (Allakhazam, etc.)
  $plusdb->UpdateConfig('pk_itemstats', $_POST['pk_itemstats'], $isplusconfarray);
  $plusdb->UpdateConfig('pk_itemstats_max_execution_time', $_POST['pk_itemstats_max_execution_time'], $isplusconfarray);

	if(strtolower($eqdkp->config['default_game']) == 'wow')
	{
		$plusdb->UpdateConfig('pk_is_webdb', $_POST['pk_is_webdb'], $isplusconfarray);   # Itemstats Prio

		$plusdb->UpdateConfig('pk_is_icon_loc', $_POST['pk_is_icon_loc'], $isplusconfarray);     # Icon Location
		$plusdb->UpdateConfig('pk_is_icon_ext', $_POST['pk_is_icon_ext'], $isplusconfarray);     # Itemextension
	}

	if(strtolower($eqdkp->config['default_game']) == 'lotro')
	{
		$plusdb->UpdateConfig('pk_is_path_sockets_image', $_POST['pk_is_path_sockets_image'], $isplusconfarray);   # Sockets images path (only for Allakhazam objects)
	}
	
	if(strtolower($eqdkp->config['default_game']) == 'runesofmagic' OR strtolower($eqdkp->config['default_game']) == 'aion')
	{
		$plusdb->UpdateConfig('pk_is_useitemlist', $_POST['pk_is_useitemlist'], $isplusconfarray);
	}


	//Bridge
	$plusdb->UpdateConfig('pk_bridge_cms_InlineUrl', 	$_POST['pk_bridge_cms_InlineUrl'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_bridge_cms_active', 		$_POST['pk_bridge_cms_active'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_disable_reg', $_POST['pk_disable_reg'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_bridge_cms_deac_reg', 	$_POST['pk_bridge_cms_deac_reg'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_bridge_cms_register_url', 	$_POST['pk_bridge_cms_register_url'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_bridge_cms_deac_inline_', 	$_POST['pk_bridge_cms_deac_inline_'], $isplusconfarray);

	if (!$_HMODE)
	{
		$plusdb->UpdateConfig('pk_bridge_cms_group', 		$_POST['pk_bridge_cms_group'], $isplusconfarray);
		$plusdb->UpdateConfig('pk_bridge_cms_sel', 			$_POST['pk_bridge_cms_sel'], $isplusconfarray);
		$plusdb->UpdateConfig('pk_bridge_cms_tableprefix', 	$_POST['pk_bridge_cms_tableprefix'], $isplusconfarray);
		$plusdb->UpdateConfig('pk_bridge_cms_otherDB', 		$_POST['pk_bridge_cms_otherDB'], $isplusconfarray);
		$plusdb->UpdateConfig('pk_bridge_cms_db', 			$_POST['pk_bridge_cms_db'], $isplusconfarray);
		$plusdb->UpdateConfig('pk_bridge_cms_host', 		$_POST['pk_bridge_cms_host'], $isplusconfarray);
		$plusdb->UpdateConfig('pk_bridge_cms_user', 		$_POST['pk_bridge_cms_user'], $isplusconfarray);
		$plusdb->UpdateConfig('pk_bridge_cms_pass', 		$_POST['pk_bridge_cms_pass'], $isplusconfarray);
	}

	#recrutment
	$plusdb->ProcesspRecruitment($_POST['pk_recruitment']);
	$plusdb->UpdateConfig('pk_recruitment_active', 		$_POST['pk_recruitment_active'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_recruitment_url', 		$_POST['pk_recruitment_url'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_recruitment_url_emb', 	$_POST['pk_recruitment_url_emb'], $isplusconfarray);

	$plusdb->UpdateConfig('pk_getdkp_active',   $_POST['pk_getdkp_active'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_getdkp_rp', 		  $_POST['pk_getdkp_rp'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_getdkp_itemids', 	$_POST['pk_getdkp_itemids'], $isplusconfarray);
	$plusdb->UpdateConfig('pk_getdkp_link', 		$_POST['pk_getdkp_link'], $isplusconfarray);

	#auto image resize
 	$plusdb->UpdateConfig('pk_air_enable', $_POST['pk_air_enable'], $isplusconfarray);
 	$plusdb->UpdateConfig('pk_air_max_resize_width', $_POST['pk_air_max_resize_width'], $isplusconfarray);
 	$plusdb->UpdateConfig('pk_air_lytebox_theme', $_POST['pk_air_lytebox_theme'], $isplusconfarray);
 	$plusdb->UpdateConfig('pk_air_lytebox_auto_resize', $_POST['pk_air_lytebox_auto_resize'], $isplusconfarray);
 	$plusdb->UpdateConfig('pk_air_lytebox_animation', $_POST['pk_air_lytebox_animation'], $isplusconfarray);

 	#SMS
 	$plusdb->UpdateConfig('pk_sms_disable', $_POST['pk_sms_disable'], $isplusconfarray);
 	$plusdb->UpdateConfig('pk_sms_username', $_POST['pk_sms_username'], $isplusconfarray);
 	$plusdb->UpdateConfig('pk_sms_password', $_POST['pk_sms_password'], $isplusconfarray);

	# Library Settings
	$plusdb->UpdateConfig('lib_email_method', $_POST['lib_email_method'], $isplusconfarray);
	$plusdb->UpdateConfig('lib_email_sender_email', $_POST['lib_email_sender_email'], $isplusconfarray);
	$plusdb->UpdateConfig('lib_email_sender_name', $_POST['lib_email_sender_name'], $isplusconfarray);
	$plusdb->UpdateConfig('lib_email_sendmail_path', $_POST['lib_email_sendmail_path'], $isplusconfarray);
	$plusdb->UpdateConfig('lib_email_smtp_host', $_POST['lib_email_smtp_host'], $isplusconfarray);
	$plusdb->UpdateConfig('lib_email_smtp_auth', $_POST['lib_email_smtp_auth'], $isplusconfarray);
	$plusdb->UpdateConfig('lib_email_smtp_user', $_POST['lib_email_smtp_user'], $isplusconfarray);
	$plusdb->UpdateConfig('lib_email_smtp_pw', $_POST['lib_email_smtp_pw'], $isplusconfarray);
	$plusdb->UpdateConfig('lib_recaptcha_okey', $_POST['lib_recaptcha_okey'], $isplusconfarray);
	$plusdb->UpdateConfig('lib_recaptcha_pkey', $_POST['lib_recaptcha_pkey'], $isplusconfarray);

	$data_saved = true;
	
	// Image width is new... flush thumbs folder..
	if($conf['pk_air_max_resize_width'] != $in->get('pk_air_max_resize_width')){
		$pcache->Delete($pcache->FolderPath('news/thumb', 'eqdkp'));
	}
	
	
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

   #Output
   ($data_saved == true) ? System_Message($plang['pk_succ_saved'], $plang['pk_save_title'], 'green') : '';
   $output .= $html->StartForm('settings', $_SERVER["PHP_SELF"] );
   $output .= $tabs->startPane('config');
   $output .= $tabs->startTab($plang['pk_tab_global'], 'config1');

   # Tab  Global
   #############
   $output .= $tabs->startTable();
   $output .= $tabs->tableheader($plang['pk_set_global_tab_head']);

   if (strtolower($eqdkp->config['default_game']) == 'wow')
   {
		include_once('include/wow/server_eu.php');
		include_once('include/wow/server_us.php');

		//Autocomplete

		$output .= "  <style>
	                  ul.jq-ui-autocomplete {
	                    position: absolute;
	                    overflow: hidden;
	                    background-color: #fff;
	                    border: 1px solid #aaa;
	                    margin: 0px;
	                    padding: 0;
	                    list-style: none;
	                    font: Verdana, Arial, sans-serif;
	                    color: #333;
	                    z-index: 2000;
	                  }
	                  ul.jq-ui-autocomplete li {
	                    display: block;
	                    padding: .3em .5em .3em .3em;
	                    overflow: hidden;
	                    width: 100%;
	                  }

	                  ul.jq-ui-autocomplete li.active {
	                    background-color: #3875d7;
	                    color: #fff;
	                  }
	                </style>";

    // Build the List...
    function implode_wrapped($before, $after, $glue, $array){
      $output = '';
      foreach($array as $item){
          $output .= $before . $item . $after . $glue;
      }
      return substr($output, 0, -strlen($glue));
    }
    $serverarry = array_merge($se,$s);
    $server_list = implode_wrapped('"','"', ",", $serverarry);

		// Generate the JS Output
		$output .= '<script>
                var wowservers = ['.$server_list.'];

                $().ready(function() {
                  $("#autocomplete").autocomplete(wowservers);
                });
                </script>';
	}

   $output .= $html->AutoTextField('pk_servername', '20', htmlspecialchars($conf['pk_servername'], ENT_QUOTES) ,$plang['pk_set_servername'] , $plang['pk_help_servername'],'autocomplete');
   $output .= $html->DropDown('pk_server_region', $realm_region , $conf['pk_server_region'], $plang[pk_set_server_region], $plang[pk_help_server_region]);
   $output .= $html->DropDown('pk_faction', $a_factions ,$conf['pk_faction'], $plang[pk_faction] , $plang[pk_faction]);

   $output .= $tabs->tableheader($plang['pk_set_eqdkp_tab_head']);
   $output .= $html->CheckBox('pk_class_color', $plang['pk_set_ClassColor'] , $conf['pk_class_color'], $plang['pk_help_colorclassnames']);
   $output .= $html->CheckBox('pk_itemhistory_dia', $plang['pk_set_itemhistory_dia'] , $conf['pk_itemhistory_dia'], $plang['pk_help_itemhistory_dia']);
   $output .= $html->CheckBox('pk_disable_comments', $plang['pk_set_comments_disable'] , $conf['pk_disable_comments'], $plang['pk_hel_pcomments_disable']);
   $output .= $html->CheckBox('pk_disable_3dmember', $plang['pk_set_dis_3dmember'] , $conf['pk_disable_3dmember'], $plang['pk_help_dis_3dmember']);
   $output .= $html->CheckBox('pk_disable_3ditem', $plang['pk_set_dis_3ditem'] , $conf['pk_disable_3ditem'], $plang['pk_help_dis_3item']);
   $output .= $html->DropDown('pk_default_modelviewer', $a_modelviewer , $conf['pk_default_modelviewer'],$plang['pk_set_modelviewer_default'], $plang['pk_set_modelviewer_default'] );
   $output .= $html->CheckBox('pk_round_activate', $plang['pk_set_round_activate'] , $conf['pk_round_activate'], $plang['pk_help_round_activate']);
   $output .= $html->TextField('pk_round_precision', '2', $conf['pk_round_precision'] ,$plang['pk_set_round_precision'] , $plang['pk_help_round_precision']) ;
   $output .= $html->DropDown('pk_newsloot_limit', $newsloot_limit , $conf['pk_newsloot_limit'],$plang['pk_set_newsloot_limit'], $plang['pk_help_newsloot_limit'] );

   if (!$_HMODE)
   {
		$output .= $html->CheckBox('pk_updatecheck', $plang['pk_set_Updatecheck'] , $conf['pk_updatecheck'], $plang['pk_help_autowarning']);
   		$output .= $html->TextField('pk_windowtime', '4', $conf['pk_windowtime'] ,$plang['pk_window_time1']." ".$plang['pk_window_time2'] , $plang['pk_help_warningtime']);
   		$output .= $html->CheckBox('pk_hide_shop', $plang['pk_set_hide_shop'] , $conf['pk_hide_shop'], $plang['pk_help_hide_shop']);
   }



   $output .= $html->DropDown('pk_debug', $a_debug_mode , $conf['pk_debug'], $plang[pk_set_debug], $plang[pk_help_debug]);
   $output .= $tabs->endTable() ;

   //Roster Mode - NoDKP
   $output .= $tabs->startTable();
   $output .= $tabs->tableheader($plang['pk_set_features']);
   $output .= $html->CheckBox('pk_noDKP', $plang['pk_set_noDKP'] , $conf['pk_noDKP'], $plang['pk_help_noDKP']);
   $output .= $html->CheckBox('pk_noRoster', $plang['pk_set_noRoster'] , $conf['pk_noRoster'], $plang['pk_help_noRoster']);
   $output .= $html->CheckBox('pk_noRaids', $plang['pk_set_noRaids'] , $conf['pk_noRaids'], $plang['pk_help_noRaids']);
   $output .= $html->CheckBox('pk_noEvents', $plang['pk_set_noEvents'] , $conf['pk_noEvents'], $plang['pk_help_noEvents']);
   $output .= $html->CheckBox('pk_noItemPrices', $plang['pk_set_noItemPrices'] , $conf['pk_noItemPrices'], $plang['pk_help_noItemPrices']);
   $output .= $html->CheckBox('pk_noItemHistoy', $plang['pk_set_noItemHistoy'] , $conf['pk_noItemHistoy'], $plang['pk_help_noItemHistoy']);
   $output .= $html->CheckBox('pk_noStats', $plang['pk_set_noStats'] , $conf['pk_noStats'], $plang['pk_help_noStats']);
   $output .= $tabs->endTable() ;

   //Contact
   $output .= $tabs->startTable();
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
   ############# END Global Tab   #############


   # Tab Rss
   ###############
   $output .= $tabs->startTab($plang['pk_set_rss_tab'], 'config2');
   $output .= $tabs->startTable();
   $output .= $tabs->tableheader($plang['pk_set_rss_tab_head']);
   if (!$_HMODE)
   {
   		$output .= $html->CheckBox('pk_showRss', $plang['pk_set_Show_rss'] , $conf['pk_showRss'], $plang['pk_help_Show_rss']);
   }

   $output .= $html->DropDown('pk_Rss_Style', $a_rss_style , $conf['pk_Rss_Style'],$plang['pk_set_Show_rss_style'], $plang['pk_help_Show_rss_style'] );
   $output .= $html->DropDown('pk_Rss_lang', $a_rss_lang , $conf['pk_Rss_lang'],$plang['pk_set_Show_rss_lang'], $plang['pk_help_Show_rss_lang'] );
   $output .= $html->TextField('pk_Rss_count', '5', $conf['pk_Rss_count'] ,$plang['pk_set_Show_rss_count'] , $plang['pk_help_Show_rss_count']);
   $output .= $html->CheckBox('pk_Rss_checkURL', $plang['pk_set_rss_chekurl'] , $conf['pk_Rss_checkURL'], $plang['pk_help_rss_chekurl']);
   $output .= $tabs->endTable() ;
   $output .= $tabs->endTab();

   # Tab MultiDKP
   ###############
   $output .= $tabs->startTab($plang['pk_tab_multidkp'], 'config3');
   $output .= $tabs->startTable();
   $output .= $tabs->tableheader($plang['pk_set_multidkp_tab_head']);
   $output .= $html->CheckBox('pk_multidkp', $plang['pk_set_multidkp'] , $conf['pk_multidkp'], $plang['pk_help_multidkp']);
   $output .= $html->CheckBox('pk_multiTooltip', $plang['pk_set_multi_Tooltip'] , $conf['pk_multiTooltip'],$plang['pk_help_dkptooltip']);
   $output .= $html->CheckBox('pk_multiSmarttip', $plang['pk_set_multi_smartTooltip'] , $conf['pk_multiSmarttip'],$plang['pk_help_smarttooltip']);
   $output .= $tabs->endTable() ;
   $output .= $tabs->endTab();

   #Tab Links
   #############
   $output .= $tabs->startTab($plang['pk_tab_links'], 'config4');
   $output .= $tabs->startTable();
   $output .= $tabs->tableheader($plang['pk_set_links_tab_head']);
   $output .= $html->CheckBox('pk_links', $plang['pk_set_links'] , $conf['pk_links'], $plang['pk_help_links']);
   $output .= $tabs->endTable() ;

   // the add Link thing
   $output .= "<table width='100%' border='0' cellspacing='1' cellpadding='2'>
  								<tr>
    								<th align='left'>".$plang['pk_set_linkname']."</th>
    								<th align='left'>".$plang['pk_set_linkurl']."</th>
    								<th align='left'>".$plang['pk_set_link_type_header']."</th>
    								<th align='left'>".$plang['pk_set_link_type_menu']."</th>
  								</tr>";


  // get the links stuff
	$customlink = array();
 	$sql = 'SELECT link_id, link_name, link_url, link_window, link_menu
         	FROM '.PLUS_LINKS_TABLE.' ORDER BY link_id';
   $result = $db->query($sql);

	$a_linkMode= array(
		'0'				=> $plang['pk_set_link_type_self'],
		'1'				=> $plang['pk_set_link_type_link'],
		'2'				=> $plang['pk_set_link_type_iframe'],
		'3'				=> $plang['pk_set_link_type_D_iframe'],
	);

	$a_linkMenu= array(
		'0'				=> 'Links Menu',
		'1'				=> 'EQdkp Menu',
		'2'				=> 'User Menu',
		'3'				=> $plang['pk_set_link_type_menuH']
	);


   while ( $row = $db->fetch_record($result) )
   {
   		$output .= "<tr class='".$eqdkp->switch_row_class()."'>
						<td nowrap='nowrap'><input type='text' name='linkname[".$row['link_id']."]' size='30' maxlength='85' value='".$row['link_name']."' class='input' /></td>
						<td nowrap='nowrap'><input type='text' name='linkurl[".$row['link_id']."]' size='30' maxlength='85' value='".$row['link_url']."' class='input' /></td>
						<td nowrap='nowrap'>".$html->DropDown('linkwindow['.$row['link_id'].']', $a_linkMode , $row['link_window'],$plang[''], $plang['pk_set_link_type_iframe_help'] ,true)."</td>
						<td nowrap='nowrap'>".$html->DropDown('link_menu['.$row['link_id'].']', $a_linkMenu , $row['link_menu'],$plang[''], "" ,true)."</td>
						</tr>";
 		$max_id = ( $max_id < $row['link_id'] ) ? $row['link_id'] : $max_id;
   }
   	$newid = ($max_id +1);
    $output .= " <tr class='".$eqdkp->switch_row_class()."'>
     							<td nowrap='nowrap'><input type='text' name='linkname[".$newid."]' size='35' maxlength='85' value='' class='input' /></td>
    							<td nowrap='nowrap'><input type='text' name='linkurl[".$newid."]' size='35' maxlength='85' value='' class='input' /></td>
  								<td nowrap='nowrap'>".$html->DropDown('linkwindow['.$newid.']', $a_linkMode , 0,$plang[''], $plang['pk_set_link_type_iframe_help'] ,true)."</td>
  								<td nowrap='nowrap'>".$html->DropDown('link_menu['.$newid.']', $a_linkMenu , 0,$plang[''], "" ,true)."</td>
  							</tr>
								</table>";
   $output .= $tabs->endTab();

   #Tab Listmembers
   ################
   $output .= $tabs->startTab($plang['pk_tab_listmemb'], 'config5');
   $output .= $tabs->startTable();
   $output .= $tabs->tableheader($plang['pk_set_leaderboard_tab_head']);
   $output .= $html->CheckBox('pk_leaderboard', $plang['pk_set_leaderboard'] , $conf['pk_leaderboard'], $plang['pk_help_lm_leaderboard']);
   #$output .= $html->CheckBox('pk_leaderboard_2row', $plang['pk_set_leaderboard_2row'] , $conf['pk_leaderboard_2row'], $plang['pk_help_leaderboard_2row']);
   $output .= $html->TextField('pk_leaderboard_limit', '2', $conf['pk_leaderboard_limit'], $plang['pk_set_leaderboard_limit'], $plang['pk_help_leaderboard_limit']);
   $output .= $html->CheckBox('pk_leaderboard_hide_zero', $plang['pk_set_leaderboard_zero'] , $conf['pk_leaderboard_hide_zero'], $plang['pk_help_leaderboard_zero']);
   $output .= $html-> DropDown('pk_default_multi', $a_multidkps , $conf['pk_default_multi'], $plang['pk_set_default_multi'], $plang['pk_help_default_multi']);
   $output .= $html->CheckBox('pk_leaderboard_normal', $plang['pk_set_normal_leaderbaord'] , $conf['pk_leaderboard_normal'], $plang['pk_help_normal_leaderbaord']);

   $output .= $tabs->tableheader($plang['pk_set_listmembers_tab_head']);
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

	   $output .= $tabs->tableheader($plang['pk_set_cmplugin_tab_head']);
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

      #Tab auto image resize
   ##########
	$output.=$tabs->startTab($plang['pk_set_news_tab'], 'config6');
	$output .= $tabs->startTable();
	$output .= $tabs->tableheader($plang['pk_air_img_resize_options']);
	$output .= $html->CheckBox('pk_air_enable', $plang['pk_air_img_resize_enable'], $conf['pk_air_enable'], $plang['pk_air_img_resize_enable']);
	$output .= $html->TextField('pk_air_max_resize_width', 4, $conf['pk_air_max_resize_width'], $plang['pk_air_max_post_img_resize_width'], $plang['pk_air_max_post_img_resize_width']);
	$output .= $html->DropDown('pk_air_lytebox_theme', $a_air_theme, $conf['pk_air_lytebox_theme'], $plang['pk_air_lytebox_theme'], $plang['pk_air_lytebox_theme_explain']);
	$output .= $html->CheckBox('pk_air_lytebox_auto_resize', $plang['pk_air_lytebox_auto_resize'], $conf['pk_air_lytebox_auto_resize'], $plang['pk_air_lytebox_auto_resize_explain']);
	$output .= $html->CheckBox('pk_air_lytebox_animation', $plang['pk_air_lytebox_animation'], $conf['pk_air_lytebox_animation'], $plang['pk_air_lytebox_animation_explain']);
	$output .= $tabs->endTable() ;
	$output .= $tabs->endTab();


   #Tab Itemstats
   ###############
   $games_with_itemstats = array('wow', 'lotro', 'runesofmagic', 'aion');
   if(in_array(strtolower($eqdkp->config['default_game']), $games_with_itemstats))
   {

	   $output .= $tabs->startTab($plang['pk_tab_itemstats'], 'config7');
	   $output .= $tabs->startTable();
	   $output .= $tabs->tableheader($plang['pk_set_itemstats_tab_head']);
	   $output .= $html->CheckBox('pk_itemstats', $plang['pk_set_itemstats'] , $conf['pk_itemstats'], $plang['pk_help_itemstats_on']);
	   $output .= $html->CheckBox('pk_itemstats_debug', $plang['pk_set_itemstats_debug'] , $conf['pk_itemstats_debug'], $plang['pk_help_itemstats_debug']);
	   $output .= $html->CheckBox('pk_is_autosearch', $plang['pk_is_set_autosearch'] , $conf['pk_is_autosearch'], $plang['pk_is_help_autosearch']);
	   $output .= $html->DropDown('pk_itemstats_max_execution_time', $is_max_execution_times, $conf['pk_itemstats_max_execution_time'], $plang['pk_itemstats_max_execution_time'], $plang['pk_itemstats_max_execution_time_explain']);
		if(strtolower($eqdkp->config['default_game']) == 'lotro')
		{
			$output .= $html->TextField('pk_is_path_sockets_image', '40', $conf['pk_is_path_sockets_image'], $plang['pk_is_set_patch_sockets'], $plang['pk_is_help_patch_sockets']);
		}
		elseif (strtolower($eqdkp->config['default_game']) == 'aion' OR strtolower($eqdkp->config['default_game']) == 'runesofmagic')
		{
			$output .= $html->CheckBox('pk_is_useitemlist', $plang['pk_is_useitemlist'], $conf['pk_is_useitemlist'], $plang['pk_help_useitemlist']);
		}
		elseif(strtolower($eqdkp->config['default_game']) == 'wow')
		{
			$output .= $tabs->tableheader($plang['pk_is_set_prio']);
			$output .= $html->TextField('pk_is_icon_ext', '10', $conf['pk_is_icon_ext'], $plang['pk_set_icon_ext'], $plang['pk_help_itemstats_icon_ext']);
			$output .= $html->TextField('pk_is_icon_loc', '40', $conf['pk_is_icon_loc'], $plang['pk_set_icon_loc'], $plang['pk_help_itemstats_icon_url']);
			$output .= $html-> DropDown('pk_is_webdb', $a_itemstats_kombis , $conf['pk_is_webdb'], $plang['pk_is_set_prio'] , $plang['pk_is_help_prio']);
		}

	   $output .= $tabs->tableheader($plang['pk_set_updates_tab_head']);
	   $output .= $tabs->tablerow($plang['pk_is_help']. '<a href='.$eqdkp_root_path.'admin/updateitemstats.php>Update Itemstats</a>');

	   $output .= $tabs->endTable() ;
	   $output .= $tabs->endTab();

   }

   #Tab Bridge
   #############
	$a_bridges = array(
	'phpbb3'				=> 'phpBB 3.x',
	'phpbb2'                => 'phpBB 2.x',
	'joomla'				=> 'Joomla 1.5.x',
	'vbulletin'				=> 'VBulletin Board 3',
	'vb4'					=> 'VBulletin Board 4',
	'e107'                  => 'e107 CMS',
	'wbb2'					=> 'Woltlab Burning Board 2',
	'wbb3'                  => 'Woltlab Burning Board 3',
	'smf'                   => 'Simple Machines Forum 1.9.x',
	'smf2'					=> 'Simple Machines Forum 2 RC',
	'evo102'     			=> 'Nuke-Evolution 1.02GER01',
	'php-fusion'			=> 'PHP-Fusion 7',
	);

   $output .= $tabs->startTab($plang['pk_set_cmsbridge_tab'], 'config8');
   $output .= $tabs->startTable();
   $output .= $tabs->tableheader($plang['pk_set_bridge_inline']);
   $output .= $tabs->tablerow($plang['pk_help_bridge_inline']);
   $output .= $html->TextField('pk_bridge_cms_InlineUrl', '60', $conf['pk_bridge_cms_InlineUrl'], $plang['pk_set_bridge_inline_url'], $plang['pk_help_bridge_inline_url']);
   $output .= $html->TextField('pk_bridge_cms_register_url', '60', $conf['pk_bridge_cms_register_url'], $plang['pk_set_cms_register_url'], $plang['pk_help_cms_register_url']);

   $output .= $tabs->endTable() ;

   $output .= $tabs->startTable();
   $output .= $tabs->tableheader($plang['pk_set_bridgeconfig_tab_head']);
   $output .= $tabs->tablerow($plang['pk_set_bridge_help']);
   $output .= $html->CheckBox('pk_bridge_cms_active', $plang['pk_set_bridge_activate'] , $conf['pk_bridge_cms_active'], $plang['pk_help_bridge_activate']);
   $output .= $html->CheckBox('pk_bridge_cms_deac_reg', $plang['pk_set_bridge_dectivate_eq_reg'] , $conf['pk_bridge_cms_deac_reg'], $plang['pk_help_bridge_dectivate_eq_reg']);
   $output .= $html->CheckBox('pk_disable_reg', $plang['pk_set_disregister'] , $conf['pk_disable_reg'], $plang['pk_help_disregister']);

	if (!$_HMODE)
	{
   		$output .= $html->TextField('pk_bridge_cms_group', '20', $conf['pk_bridge_cms_group'], $plang['pk_set_bridge_group'], $plang['pk_help_bridge_group']);
		$output .= $html->DropDown('pk_bridge_cms_sel', $a_bridges , $conf['pk_bridge_cms_sel'], $plang['pk_set_bridge_cms'], $plang['pk_help_bridge_cms']);
		$output .= $html->TextField('pk_bridge_cms_tableprefix', '20', $conf['pk_bridge_cms_tableprefix'], $plang['pk_set_bridge_prefix'], $plang['pk_help_bridge_prefix']);
	    $output .= $html->CheckBox('pk_bridge_cms_otherDB', $plang['pk_set_bridge_acess'] , $conf['pk_bridge_cms_otherDB'], $plang['pk_help_bridge_acess']);
	    $output .= $tabs->tableheader($plang['pk_help_bridge_acess']);
	    $output .= $html->TextField('pk_bridge_cms_db', '20', $conf['pk_bridge_cms_db'],$plang['pk_set_bridge_database'], $plang['pk_help_bridge_database']);
	    $output .= $html->TextField('pk_bridge_cms_host', '20', $conf['pk_bridge_cms_host'], $plang['pk_set_bridge_host'], $plang['pk_help_bridge_host']);
	    $output .= $html->TextField('pk_bridge_cms_user', '20', $conf['pk_bridge_cms_user'], $plang['pk_set_bridge_username'], $plang['pk_help_bridge_username']);
	    $output .= $html->TextField('pk_bridge_cms_pass', '20', $conf['pk_bridge_cms_pass'], $plang['pk_set_bridge_password'], $plang['pk_help_bridge_password'],'password');
    }

   $output .= $tabs->endTable() ;
   $output .= $tabs->endTab();

   #Tab recruitment
   #################
   $output .= $tabs->startTab($plang['pk_set_recruitment_tab'], 'config9');
   $output .= $tabs->startTable();
   $output .= $tabs->tableheader($plang['pk_set_recruitment_header']);
   $output .= $html->CheckBox('pk_recruitment_active', $plang['pk_set_recruitment'] , $conf['pk_recruitment_active'], $plang['pk_help_recruitment']);
   $output .= $html->TextField('pk_recruitment_url', '60', $conf['pk_recruitment_url'], $plang['pk_set_recruitment_contact_type'], $plang['pk_help_recruitment_contact_type']);
   $output .= $html->CheckBox('pk_recruitment_url_emb', $plang['pk_set_recruit_embedded'] , $conf['pk_recruitment_url_emb'], $plang['pk_help_recruit_embedded']);

   $output .= $tabs->endTable() ;

   $output .= "<table width='100%' border='0' cellspacing='1' cellpadding='2'>
   				<tr>
				<th align='left'>".$plang['pk_recruitment_count']."</th>";

   	if($eqdkp->config['default_game'] == 'WoW')
	{
				$output .= "<th align='left'>".$plang['ps_recruitment_spec']."</th>";
	}
	$output .= "<th align='left'>".$user->lang['class']."</th>
				</tr>";

   	$customlink = array();

 	$sql = 'SELECT class_name , class_id
         	FROM __classes where class_id != 0 group by class_name ORDER BY class_name';
  	$result = $db->query($sql);

   	while ( $row = $db->fetch_record($result) )
   	{

		if($eqdkp->config['default_game'] == 'WoW')
		{
			$i = 0 ;
			$specArray = $user->lang['talents'][renameClasstoenglish($row['class_name']) ];
			$specArray[] = "";

			if (is_array($specArray))
			{
		   		foreach ($specArray as $specname)
		   		{

		   		   $icon = "";
		   		   $class_name = str_replace(' ','',strtolower(renameClasstoenglish($row['class_name'])));
		   		   $img = $eqdkp_root_path."games/WoW/talents/".$class_name.$i++.".png" ;
		   		   if (file_exists($img))
		   		   {
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
		   	   $output .= "<td colspan=2>".get_ClassIcon($row['class_name'],$row['class_id'] ).' '.$row['class_name']. " </td>";
		   	   $output .= "</tr>";
			}

   		}else
   		{
	   	   $output .= "<tr class='".$eqdkp->switch_row_class()."'>" ;
	   	   $output .= "<td>".$plang['pk_recruitment_count'];
	   	   $output .= $html->TextField("pk_recruitment[".$row['class_id']."]", '3', $conf['pk_recruitment_class['.$row['class_id'].']'],'', '','text',true)."</td>" ;
	   	   $output .= "<td colspan=2>".get_ClassIcon($row['class_name'],$row['class_id']).' '.$row['class_name']. " </td>";
	   	   $output .= "</tr>";
   		}

   	}
   $output .= "</table>";
   $output .= $tabs->endPane();


   #Tab GetDKP
   #############
   $output .= $tabs->startTab($plang['pk_set_getdkp_tab'], 'config10');
   $output .= $tabs->startTable();
   $output .= $tabs->tableheader($plang['pk_getdkp_th']);
   $output .= $html->CheckBox('pk_getdkp_active', $plang['pk_set_getdkp_active'] , $conf['pk_getdkp_active'], $plang['pk_help_getdkp_active']);
   $output .= $html->CheckBox('pk_getdkp_rp', $plang['pk_set_getdkp_rp'] , $conf['pk_getdkp_rp'], $plang['pk_help_getdkp_rp']);
   $output .= $html->CheckBox('pk_getdkp_itemids', $plang['pk_set_getdkp_items'] , $conf['pk_getdkp_itemids'], $plang['pk_help_getdkp_items']);
   #$output .= $html->CheckBox('pk_getdkp_link', $plang['pk_set_getdkp_link'] , $conf['pk_getdkp_link'], $plang['pk_help_getdkp_link']);
   $output .= $tabs->endTable() ;
   $output .= $tabs->endTab();

   #Tab SMS
   #############
   $output .= $tabs->startTab($plang['pk_set_sms_tab'], 'config11');
   $output .= $tabs->startTable();
   $output .= $tabs->tableheader($plang['pk_set_sms_header'] . " - " . $plang['pk_set_sms_info']);
   $output .= $html->CheckBox('pk_sms_disable', $plang['pk_set_sms_deactivate'] , $conf['pk_sms_disable'], $plang['pk_set_sms_deactivate']);
   $output .= $html->TextField("pk_sms_username", '15', $conf['pk_sms_username'],$plang['pk_set_sms_username'], $plang['pk_set_sms_username'],'text',false);
   $output .= $html->TextField("pk_sms_password", '15', $conf['pk_sms_password'],$plang['pk_set_sms_pass'], $plang['pk_set_sms_pass'],'text',false) ;
   	if ($_HMODE)
	{
   		$output .= $tabs->tableheader($plang['pk_set_sms_info_temp'].$_HMODE_LINK) ;
	}else
	{
		$output .= $tabs->tableheader($plang['pk_set_sms_info_temp'].$user->lang['sms_info_account_link']) ;
	}
   $output .= $tabs->endTable() ;
   $output .= $tabs->endTab();

   #Tab Library Settings
   #############
   $output .= $tabs->startTab($plang['pk_set_libraries_tab'], 'config12');
   $output .= $tabs->startTable();
			$maildropdwn = array(
										'mail'      => $plang['lib_email_mail'],
										'sendmail'  => $plang['lib_email_sendmail'],
										'smtp'      => $plang['lib_email_smtp'],
										);
		$output .= $tabs->tableheader($plang['pk_set_email_header']);
		$output .= $html->DropDown('lib_email_method', $maildropdwn , $conf['lib_email_method'], $plang['lib_email_method'], $plang['lib_email_method']);
		$output .= $html->TextField("lib_email_sender_email", '30', $conf['lib_email_sender_email'],$plang['lib_email_sender_email'], $plang['lib_email_sender_email'],'text',false);
		$output .= $html->TextField("lib_email_sender_name", '30', $conf['lib_email_sender_name'],$plang['lib_email_sender_name'], $plang['lib_email_sender_name'],'text',false);
		$output .= $html->TextField("lib_email_sendmail_path", '30', $conf['lib_email_sendmail_path'],$plang['lib_email_sendmail_path'], $plang['lib_email_sendmail_path'],'text',false);
		$output .= $html->TextField("lib_email_smtp_host", '30', $conf['lib_email_smtp_host'],$plang['lib_email_smtp_host'], $plang['lib_email_smtp_host'],'text',false);
		$output .= $html->CheckBox('lib_email_smtp_auth', $plang['lib_email_smtp_auth'] , $conf['lib_email_smtp_auth'], $plang['lib_email_smtp_auth']);
		$output .= $html->TextField("lib_email_smtp_user", '30', $conf['lib_email_smtp_user'],$plang['lib_email_smtp_user'], $plang['lib_email_smtp_user'],'text',false);
		$output .= $html->TextField("lib_email_smtp_pw", '30', $conf['lib_email_smtp_pw'],$plang['lib_email_smtp_password'], $plang['lib_email_smtp_password'],'password',false);

		$output .= $tabs->tableheader($plang['pk_set_recaptcha_header']);
		$output .= $html->TextField("lib_recaptcha_okey", '30', $conf['lib_recaptcha_okey'],$plang['lib_recaptcha_okey'], $plang['lib_recaptcha_okey_help'],'text',false);
		$output .= $html->TextField("lib_recaptcha_pkey", '30', $conf['lib_recaptcha_pkey'],$plang['lib_recaptcha_pkey'], $plang['lib_recaptcha_pkey_help'],'text',false);

		$output .= $tabs->endTable() ;
		$output .= $tabs->endTab();

   //Close
   $output .= "<br />";
   $output .= "<div align='center'>";
   $output .= $html->Button('save_plus', $plang['pk_save']);
   $output .= "</div>";
   $output .= $html->EndForm();

    $tpl->assign_vars(array(
	        'OUTPUT'			=> $output,
	  ));

   $eqdkp->set_vars(array(
    'page_title'    => sprintf($user->lang['title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['config_plus'],
    'template_file' => 'admin/plussettings.html',
    'display'       => true)
);

?>