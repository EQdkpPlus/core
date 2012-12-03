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
echo $init->Header($eqdkp_root_path);
echo "<script language='JavaScript' type='text/javascript' src='../itemstats/overlib/overlib.js'></script>";

// Save this shit
if ($_POST['save_plus']){
	// get an array with the config fields of the DB
		$isplusconfarray = $plusdb->CheckDBFields('plus_config', 'config_name');

  // global config
    $plusdb->UpdateConfig('pk_class_color', $_POST['pk_class_color'], $isplusconfarray); # Coloured Classnames?
		$plusdb->UpdateConfig('pk_quickdkp', $_POST['pk_quickdkp'], $isplusconfarray); # Show Quickdkp?
		$plusdb->UpdateConfig('pk_bossloot', $_POST['pk_bossloot'], $isplusconfarray); # Show Bossloot?
		$plusdb->UpdateConfig('pk_updatecheck', $_POST['pk_updatecheck'], $isplusconfarray); # Enable Update Warnings & VersionCheck
		$plusdb->UpdateConfig('pk_windowtime', $_POST['pk_windowtime'], $isplusconfarray);
		$plusdb->UpdateConfig('pk_newsloot_limit', $_POST['pk_newsloot_limit'], $isplusconfarray);
	// multidkp
		$plusdb->UpdateConfig('pk_multidkp', $_POST['pk_multidkp'], $isplusconfarray); # Multidkp on/off
		$plusdb->UpdateConfig('pk_multiTooltip', $_POST['pk_multiTooltip'], $isplusconfarray); # Multidkp on/off
		$plusdb->UpdateConfig('pk_multiSmarttip', $_POST['pk_multiSmarttip'], $isplusconfarray); # Multidkp on/off
	// Listmembers
		$plusdb->UpdateConfig('pk_leaderboard', $_POST['pk_leaderboard'], $isplusconfarray); # Show the leaderbaord?
		$plusdb->UpdateConfig('pk_leaderboard_2row', $_POST['pk_leaderboard_2row'], $isplusconfarray); # Show the leaderbaord?
		$plusdb->UpdateConfig('pk_leaderboard_limit', $_POST['pk_leaderboard_limit'], $isplusconfarray); # Show the leaderbaord?
		
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
	// Boss Counter
		$plusdb->UpdateConfig('pk_bosscount', $_POST['pk_bosscount'], $isplusconfarray);   # Show BossCounter
	// Itemstats
		$plusdb->UpdateConfig('pk_itemstats', $_POST['pk_itemstats'], $isplusconfarray);   # Enable Itemstats
		$plusdb->UpdateConfig('pk_itemstats_debug', $_POST['pk_itemstats_debug'], $isplusconfarray);   # Enable Itemstats
		
		$plusdb->UpdateConfig('pk_is_icon_loc', $_POST['pk_is_icon_loc'], $isplusconfarray);   # Enable Itemstats
		$plusdb->UpdateConfig('pk_is_icon_ext', $_POST['pk_is_icon_ext'], $isplusconfarray);   # Enable Itemextension
		$plusdb->UpdateConfig('pk_itemlanguage', $_POST['pk_itemlanguage'], $isplusconfarray);   # Item Language
		$plusdb->UpdateConfig('pk_trans_en_de', $_POST['pk_trans_en_de'], $isplusconfarray);   # Translate English Items into German
		$plusdb->UpdateConfig('pk_trans_de_en', $_POST['pk_trans_de_en'], $isplusconfarray);   # Translate German Items into English
		$data_saved = true;
}

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
   $output .= $html-> DropDown('pk_newsloot_limit', $newsloot_limit , $conf['pk_newsloot_limit'], $plang['pk_set_newsloot_limit'], $plang['pk_help_newsloot_limit'] );
   $output .= "<br />";
   $output .= "<hr />";
   $output .= $html->CheckBox('pk_updatecheck', $plang['pk_set_Updatecheck'] , $conf['pk_updatecheck'], $plang['pk_help_autowarning']);
   $output .= "<br />";   
   $output .= $html->TextField('pk_windowtime', '4', $conf['pk_windowtime'] ,$plang['pk_window_time1'] , $plang['pk_help_warningtime'])." ".$plang['pk_window_time2'];
   $output .= $tabs->endTab();
   $output .= $tabs->startTab($plang['pk_tab_multidkp'], 'config2');
// MultiDKP
   $output .= $html->CheckBox('pk_multidkp', $plang['pk_set_multidkp'] , $conf['pk_multidkp'], $plang['pk_help_multidkp']);
   $output .= "<br />";
   $output .= $html->CheckBox('pk_multiTooltip', $plang['pk_set_multi_Tooltip'] , $conf['pk_multiTooltip'],$plang['pk_help_dkptooltip']);
   $output .= "<br />";
   $output .= $html->CheckBox('pk_multiSmarttip', $plang['pk_set_multi_smartTooltip'] , $conf['pk_multiSmarttip'],$plang['pk_help_smarttooltip']);
   
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
   $output .= $tabs->startTab($plang['pk_tab_bosscount'], 'config4');
// Bosscounter
   $output .= $html->CheckBox('pk_bosscount', $plang['pk_set_bosscounter'] , $conf['pk_bosscount'], $plang['pk_help_bosscounter']);
   $output .= "<br />";
   $output .= $html->CheckBox('pk_bossloot', $plang['pk_set_Bossloot'] , $conf['pk_bossloot'], $plang['pk_help_boosloot']);
   $output .= $tabs->endTab();
   $output .= $tabs->startTab($plang['pk_tab_listmemb'], 'config5');
// Listmembers
// 
   $output .= $html->CheckBox('pk_leaderboard', $plang['pk_set_leaderboard'] , $conf['pk_leaderboard'], $plang['pk_help_lm_leaderboard']);
   $output .= $html->CheckBox('pk_leaderboard_2row', $plang['pk_set_leaderboard_2row'] , $conf['pk_leaderboard_2row'], $plang['pk_help_leaderboard_2row']);
   $output .= $html->TextField('pk_leaderboard_limit', '2', $conf['pk_leaderboard_limit'], $plang['pk_set_leaderboard_limit'], $plang['pk_help_leaderboard_limit']);
   $output .= "<br />";
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
   $output .= "<br />";
   
   if ($pm->check(PLUGIN_INSTALLED, 'charmanager')) 
	 { 
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
   $output .= $html-> DropDown('pk_is_language', $itemstats_language , $conf['pk_is_language'], $plang['pk_is_language'], $plang['pk_help_itemstats_search']);
   $output .= "<br />";
   $output .= $html->TextField('pk_is_icon_ext', '10', $conf['pk_is_icon_ext'], $plang['pk_set_icon_ext'], $plang['pk_help_itemstats_icon_ext']);
   $output .= "<br />";
   $output .= $html->TextField('pk_is_icon_loc', '40', $conf['pk_is_icon_loc'], $plang['pk_set_icon_loc'], $plang['pk_help_itemstats_icon_url']);
   $output .= "<br />";
   $output .= $html->CheckBox('pk_trans_en_de', $plang['pk_set_en_de'] , $conf['pk_trans_en_de'], $plang['pk_help_itemstats_translate_deeng']);
   $output .= "<br />";
	 $output .= $html->CheckBox('pk_trans_de_en', $plang['pk_set_de_en'] , $conf['pk_trans_de_en'], $plang['pk_help_itemstats_translate_engde']);
   $output .= $tabs->endTab();
   $output .= $tabs->endPane();
   $output .= "<br />";
   $output .= "<div align='center'>";
   $output .= $html->Button('save_plus', $plang['pk_save']);
   $output .= "</div>";
   $output .= $html->EndForm();
   echo $output;
?>