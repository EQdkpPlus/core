<?php
/******************************
 * EQDKP PLUGIN: PLUSkernel
 * (c) 2006 by WalleniuM [Simon Wallmann]
 * http://www.kompsoft.de   
 * ------------------
 * lang_main.php (english)
 * Changed: October 31, 2006
 * 
 ******************************/

//---- Main ----
$lang['pluskernel']          	= 'PLUS Config';
$lang['pk_adminmenu']         = 'PLUS Config';
$lang['pk_settings']					= 'Settings';
$lang['pk_date_settings']			= 'd.m.y';

//---- Javascript stuff ----
$lang['pk_plus_about']				= 'About EQDKP PLUS';
$lang['updates']							= 'Available Updates';
$lang['loading']							= 'Loading...';
$lang['pk_config_header']			= 'EQDKP PLUS Settings';
$lang['pk_close_jswin1']      = 'Close the'; 
$lang['pk_close_jswin2']     	= 'window before opening it again!';
$lang['pk_help_header']				= 'Help';

//---- Updater Stuff ----
$lang['pk_alt_attention']			= 'Attention';
$lang['pk_alt_ok']						= 'Everything OK!';
$lang['pk_updates_avail']			= 'Updates available';
$lang['pk_updates_navail']		= 'NO Updates available';
$lang['pk_no_updates']				= 'Your Versions are all up to date. There are no newer Versions available.';
$lang['pk_act_version']				= 'New Version';
$lang['pk_inst_version']			= 'Installed';
$lang['pk_changelog']					= 'Changelog';
$lang['pk_download']					= 'Download';
$lang['pk_upd_information']		= 'Information';
$lang['pk_enabled']						= 'disabled';
$lang['pk_disabled']					= 'enabled';
$lang['pk_auto_updates1']			= 'The automatic update warning is';
$lang['pk_auto_updates2']			= 'If you disabled this setting, please recheck regulary for updates to prevent hacks and stay up to date..';
$lang['pk_module_name']				= 'Module name';
$lang['pk_plugin_level']			= 'Level';
$lang['pk_release_date']			= 'Release';
$lang['pk_alt_error']					= 'Error';
$lang['pk_no_conn_header']		= 'Connection Error';
$lang['pk_no_server_conn']		= 'An error ocurred while trying to contact the update server, either 
																 your host do not allow outbound connections or the error was caused 
																 by a network problem. Please visit the eqdkp-forum to make 
																 sure you are running the latest version.';
$lang['pk_reset_warning']			= 'Reset Warning';

//---- Update Levels ----
$lang['pk_level_other']				= 'Other';
$updatelevel = array (
	'Bugfix'										=> 'Bugfix',
	'Feature Release'						=> 'Feature Release',	
	'Security Update'						=> 'Security Update',			
	'New version'								=> 'New version',	
	'Release Candidate'					=> 'Release Candidate',
	'Public Beta'								=> 'Public Beta',
	'Closed Beta'								=> 'Closed Beta',
	'Alpha'											=> 'Alpha',
);

//---- About ----
$lang['pk_version']						= 'Version';
$lang['pk_created by']				= 'Created by';
$lang['web_url']							= 'Web';
$lang['personal_url']					= 'Private';
$lang['pk_credits']						= 'Credits';
$lang['pk_sponsors']					= 'Donators';
$lang['pk_plugins']						= 'PlugIns';
$lang['pk_modifications']			= 'Mods';
$lang['pk_themes']						= 'Styles';
$lang['pk_additions']					= 'Code Additions';
$lang['pk_tab_stuff']					= 'EQDKP Team';
$lang['pk_tab_help']					= 'Help';

//---- Settings ----
$lang['pk_save']							= 'Save';
$lang['pk_save_title']				= '';
$lang['pk_succ_saved']				= 'The settings was successfully saved';
 // Tabs
$lang['pk_tab_global']				= 'Global';
$lang['pk_tab_multidkp']			= 'multiDKP';
$lang['pk_tab_links']					= 'Links';
$lang['pk_tab_bosscount']			= 'BossCounter';
$lang['pk_tab_listmemb']			= 'Listmembers';
$lang['pk_tab_itemstats']			= 'Itemstats';
// Global
$lang['pk_set_QuickDKP']			= 'Show QuickDKP';
$lang['pk_set_Bossloot']			= 'Show bossloot ?';
$lang['pk_set_ClassColor']		= 'Colored Classnames';
$lang['pk_set_Updatecheck']		= 'Enable Updatecheck';
$lang['pk_window_time1']			= 'Show Window every';
$lang['pk_window_time2']			= 'Minutes';
// MultiDKP
$lang['pk_set_multidkp']			= 'Enable MultiDKP';
// Listmembers
$lang['pk_set_leaderboard']		= 'Show Leaderboard';
$lang['pk_set_lb_solo']				= 'Show Leaderboard per account';
$lang['pk_set_rank']					= 'Show Rank';
$lang['pk_set_rank_icon']			= 'Show Rank Icon';
$lang['pk_set_level']					= 'Show Level';
$lang['pk_set_lastloot']			= 'Show Last loot';
$lang['pk_set_lastraid']			= 'Show Last raid';
$lang['pk_set_attendance']		= 'Show Raid Attendance';
$lang['pk_set_attendance30']	= 'Show Raid Attendance 30 Day';
$lang['pk_set_attendance60']	= 'Show Raid Attendance 60 Day';
$lang['pk_set_attendance90']	= 'Show Raid Attendance 90 Day';
$lang['pk_set_attendanceAll']	= 'Show Raid Attendance Lifetime';

// Links
$lang['pk_set_links']					= 'Enable Links';
$lang['pk_set_linkurl']				= 'Link URL';
$lang['pk_set_linkname']			= 'Link name';
$lang['pk_set_newwindow']			= 'open in new window ?';
// BossCounter
$lang['pk_set_bosscounter']		= 'Show Bosscounter';
//Itemstats
$lang['pk_set_itemstats']			= 'Enable Itemstats';
$lang['pk_is_language']				= 'Itemstats language';
$lang['pk_german']						=	'German';
$lang['pk_english']						= 'English';
$lang['pk_french']						= 'French';
$lang['pk_set_icon_ext']			= '';
$lang['pk_set_icon_loc']			= '';
$lang['pk_set_en_de']					= 'Translate Items from English to German';
$lang['pk_set_de_en']					= 'Translate Items from German to English';

################
# new sort
###############

//MultiDKP
//

$lang['pk_set_multi_Tooltip']						= 'Show DKP tooltip';
$lang['pk_set_multi_smartTooltip']			= 'Smart tooltip';

//Help
$lang['pk_help_colorclassnames']				= "If activated, the players will be shown with the WoW colors of their class and their class icon.";
$lang['pk_help_quickdkp']								= "Shows the logged-in user the points off all members that are assigned to him above the menu.";
$lang['pk_help_boosloot']								= "If active, you can click the boss names in the raid notes and the bosscounter to have a detailed overview of the dropped items. If inactive, it will be linked to Blasc.de (Only activate if you enter a raid for each single boss)";
$lang['pk_help_autowarning']						= "Warns the administrator when he logs in, if updates are available.";
$lang['pk_help_warningtime']						= "How often should the warning appear?";
$lang['pk_help_multidkp']								= "MultiDKP allows the management and overview of seperate accounts. Activates the management and overview of the MultiDKP accounts.";
$lang['pk_help_dkptooltip']							= "If activated, a tooltip with detailed information about the calculation of the points will be shown, when the mouse hovers over the different points.";
$lang['pk_help_smarttooltip']						= "Shortened overview of the tooltips (activate if you got more than three events per account)";
$lang['pk_help_links']									= "In this menu you are able to define different links, which will be displayed in the main menu.";
$lang['pk_help_bosscounter']						= "If activated, a table will be displayed below the main menu with the bosskills. The administration is being managed by the plugin Bossprogress";
$lang['pk_help_lm_leaderboard']					= "If activated, a leaderboard will be displayed above the scoretable. A leaderboard is a table, where the dkp of each class is displayed sorted in decending order.";
$lang['pk_help_lm_rank']								= "An extra column is being displayed, which displays the rank of the member.";
$lang['pk_help_lm_rankicon']						= "Instead of the rank name, an icon is being displayed. Which items are available you can check in the folder \images\rank";
$lang['pk_help_lm_level']								= "An additional column is being displayed, which displays the level of the member. ";
$lang['pk_help_lm_lastloot']						= "An extra colums is being displayed, showing the date a member received his latest item.";
$lang['pk_help_lm_lastraid']						= "An extra column is being displayed, showing the date of the latest raid a member has been participated in.";
$lang['pk_help_lm_atten30']							= "An extra column is being displayed, showing a members participation in raid during the last 30 days (in percent).";
$lang['pk_help_lm_atten60']							= "An extra column is being displayed, showing a members participation in raid during the last 60 days (in percent). ";
$lang['pk_help_lm_atten90']							= "An extra column is being displayed, showing a members participation in raid during the last 90 days (in percent). ";
$lang['pk_help_lm_attenall']						= "An extra column is being displayed, showing a members overall raid participation (in percent).";
$lang['pk_help_itemstats_on']						= "Itemstats is requesting information about items entered in EQDKP in the WOW databases (Blasc, Allahkazm, Thottbot). These will be displayed in the color of the items quality including the known WOW tooltip. When active, items will be shown with a mouseover tooltip, similar to WOW.";
$lang['pk_help_itemstats_search']				= "Which database should Itemstats use first to lookup information? Blasc or Allakhazam?";
$lang['pk_help_itemstats_icon_ext']			= "Filename extension of the pictures to be shown. Usually .png or .jpg.";
$lang['pk_help_itemstats_icon_url']						= "Please enter the URL where you Itemstats pictures are being located. German: http://www.buffed.de/images/wow/32/ in 32x32 or http://www.buffed.de/images/wow/64/ in 64x64 pixels.English at Allakzam: http://www.buffed.de/images/wow/32/";
$lang['pk_help_itemstats_translate_deeng']		= "If active, information of the tooltips will be requested in german, even when the item is being entered in english.";
$lang['pk_help_itemstats_translate_engde']		= "If active, information of the tooltips will be requested in English, even if the item is being entered in german.";

$lang['pk_set_leaderboard_2row']					= 'Leaderboard in 2 lines';
$lang['pk_set_leaderboard_limit']					= 'limitation of the display';

$lang['pk_help_leaderboard_2row']					= 'If active, the Leaderboard will be displayed in two lines with 4 or 5 classes each.';
$lang['pk_help_leaderboard_limit']				= 'If a numeric number is being entered, the Leaderboard will be restricted to the entered number of members. The number 0 represents no restrictions.';

$lang['pk_set_newsloot_limit']						= 'newsloot limit';
$lang['pk_help_newsloot_limit']						= 'How many items should be displayed in the news? This restricts the display of items, which will be displeyed in the news. The number 0 represents no restrictions.';

$lang['pk_set_itemstats_debug']						= 'debug Modus';
$lang['pk_help_itemstats_debug']					= 'Wenn aktiviert, werden alle Schritte von Itemstats in die Datei /itemstats/includes_de/debug.txt geschrieben. Diese MUSS aber beschreibbar sein. CHMOD 777 !!!';

$lang['pk_set_showclasscolumn']						= 'show classes column';
$lang['pk_help_showclasscolumn']					= 'If activated, an extra column is being displayed showing the class of the player.' ;

$lang['pk_set_show_skill']								= 'show skill column';
$lang['pk_help_show_skill']								= 'If activated, an extra column is being displayed showing the skill of the player.';

$lang['pk_set_show_arkan_resi']						= 'show arcan resistance column';
$lang['pk_help_show_arkan_resi']					= 'If activated, an extra column is being displayed showing the arcane resistance of the player.';

$lang['pk_set_show_fire_resi']						= 'show fire resistance column';
$lang['pk_help_show_fire_resi']						= 'If activated, an extra column is being displayed showing the fire resistance of the player.';

$lang['pk_set_show_nature_resi']					= 'show nature resistance column';
$lang['pk_help_show_nature_resi']					= 'If activated, an extra column is being displayed showing the nature resistance of the player.';

$lang['pk_set_show_ice_resi']							= 'show ice resistance column';
$lang['pk_help_show_ice_resi']						= 'If activated, an extra column is being displayed showing the frost resistance of the player.';

$lang['pk_set_show_shadow_resi']					= 'show shadow resistance column';
$lang['pk_help_show_shadow_resi']					= 'If activated, an extra column is being displayed showing the shadow resistance of the player.';

$lang['pk_set_show_profils']							= 'show profile link column';
$lang['pk_help_show_profils']							= 'If activated, an extra column is being displayed showing the links to the profile.';


?>