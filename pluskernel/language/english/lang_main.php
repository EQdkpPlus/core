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
$plang['pluskernel']          	= 'PLUS Config';
$plang['pk_adminmenu']         = 'PLUS Config';
$plang['pk_settings']					= 'Settings';
$plang['pk_date_settings']			= 'd.m.y';

//---- Javascript stuff ----
$plang['pk_plus_about']				= 'About EQDKP PLUS';
$plang['updates']							= 'Available Updates';
$plang['loading']							= 'Loading...';
$plang['pk_config_header']			= 'EQDKP PLUS Settings';
$plang['pk_close_jswin1']      = 'Close the'; 
$plang['pk_close_jswin2']     	= 'window before opening it again!';
$plang['pk_help_header']				= 'Help';

//---- Updater Stuff ----
$plang['pk_alt_attention']			= 'Attention';
$plang['pk_alt_ok']						= 'Everything OK!';
$plang['pk_updates_avail']			= 'Updates available';
$plang['pk_updates_navail']		= 'NO Updates available';
$plang['pk_no_updates']				= 'Your Versions are all up to date. There are no newer Versions available.';
$plang['pk_act_version']				= 'New Version';
$plang['pk_inst_version']			= 'Installed';
$plang['pk_changelog']					= 'Changelog';
$plang['pk_download']					= 'Download';
$plang['pk_upd_information']		= 'Information';
$plang['pk_enabled']						= 'disabled';
$plang['pk_disabled']					= 'enabled';
$plang['pk_auto_updates1']			= 'The automatic update warning is';
$plang['pk_auto_updates2']			= 'If you disabled this setting, please recheck regulary for updates to prevent hacks and stay up to date..';
$plang['pk_module_name']				= 'Module name';
$plang['pk_plugin_level']			= 'Level';
$plang['pk_release_date']			= 'Release';
$plang['pk_alt_error']					= 'Error';
$plang['pk_no_conn_header']		= 'Connection Error';
$plang['pk_no_server_conn']		= 'An error ocurred while trying to contact the update server, either 
																 your host do not allow outbound connections or the error was caused 
																 by a network problem. Please visit the eqdkp-forum to make 
																 sure you are running the latest version.';
$plang['pk_reset_warning']			= 'Reset Warning';

//---- Update Levels ----
$plang['pk_level_other']				= 'Other';
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
$plang['pk_version']						= 'Version';
$plang['pk_created by']				= 'Created by';
$plang['web_url']							= 'Web';
$plang['personal_url']					= 'Private';
$plang['pk_credits']						= 'Credits';
$plang['pk_sponsors']					= 'Donators';
$plang['pk_plugins']						= 'PlugIns';
$plang['pk_modifications']			= 'Mods';
$plang['pk_themes']						= 'Styles';
$plang['pk_additions']					= 'Code Additions';
$plang['pk_tab_stuff']					= 'EQDKP Team';
$plang['pk_tab_help']					= 'Help';

//---- Settings ----
$plang['pk_save']							= 'Save';
$plang['pk_save_title']				= '';
$plang['pk_succ_saved']				= 'The settings was successfully saved';
 // Tabs
$plang['pk_tab_global']				= 'Global';
$plang['pk_tab_multidkp']			= 'multiDKP';
$plang['pk_tab_links']					= 'Links';
$plang['pk_tab_bosscount']			= 'BossCounter';
$plang['pk_tab_listmemb']			= 'Listmembers';
$plang['pk_tab_itemstats']			= 'Itemstats';
// Global
$plang['pk_set_QuickDKP']			= 'Show QuickDKP';
$plang['pk_set_Bossloot']			= 'Show bossloot ?';
$plang['pk_set_ClassColor']		= 'Colored Classnames';
$plang['pk_set_Updatecheck']		= 'Enable Updatecheck';
$plang['pk_window_time1']			= 'Show Window every';
$plang['pk_window_time2']			= 'Minutes';
// MultiDKP
$plang['pk_set_multidkp']			= 'Enable MultiDKP';
// Listmembers
$plang['pk_set_leaderboard']		= 'Show Leaderboard';
$plang['pk_set_lb_solo']				= 'Show Leaderboard per account';
$plang['pk_set_rank']					= 'Show Rank';
$plang['pk_set_rank_icon']			= 'Show Rank Icon';
$plang['pk_set_level']					= 'Show Level';
$plang['pk_set_lastloot']			= 'Show Last loot';
$plang['pk_set_lastraid']			= 'Show Last raid';
$plang['pk_set_attendance']		= 'Show Raid Attendance';
$plang['pk_set_attendance30']	= 'Show Raid Attendance 30 Day';
$plang['pk_set_attendance60']	= 'Show Raid Attendance 60 Day';
$plang['pk_set_attendance90']	= 'Show Raid Attendance 90 Day';
$plang['pk_set_attendanceAll']	= 'Show Raid Attendance Lifetime';

// Links
$plang['pk_set_links']					= 'Enable Links';
$plang['pk_set_linkurl']				= 'Link URL';
$plang['pk_set_linkname']			= 'Link name';
$plang['pk_set_newwindow']			= 'open in new window ?';
// BossCounter
$plang['pk_set_bosscounter']		= 'Show Bosscounter';
//Itemstats
$plang['pk_set_itemstats']			= 'Enable Itemstats';
$plang['pk_is_language']				= 'Itemstats language';
$plang['pk_german']						=	'German';
$plang['pk_english']						= 'English';
$plang['pk_french']						= 'French';
$plang['pk_set_icon_ext']			= '';
$plang['pk_set_icon_loc']			= '';
$plang['pk_set_en_de']					= 'Translate Items from English to German';
$plang['pk_set_de_en']					= 'Translate Items from German to English';

################
# new sort
###############

//MultiDKP
//

$plang['pk_set_multi_Tooltip']						= 'Show DKP tooltip';
$plang['pk_set_multi_smartTooltip']			= 'Smart tooltip';

//Help
$plang['pk_help_colorclassnames']				= "If activated, the players will be shown with the WoW colors of their class and their class icon.";
$plang['pk_help_quickdkp']								= "Shows the logged-in user the points off all members that are assigned to him above the menu.";
$plang['pk_help_boosloot']								= "If active, you can click the boss names in the raid notes and the bosscounter to have a detailed overview of the dropped items. If inactive, it will be linked to Blasc.de (Only activate if you enter a raid for each single boss)";
$plang['pk_help_autowarning']						= "Warns the administrator when he logs in, if updates are available.";
$plang['pk_help_warningtime']						= "How often should the warning appear?";
$plang['pk_help_multidkp']								= "MultiDKP allows the management and overview of seperate accounts. Activates the management and overview of the MultiDKP accounts.";
$plang['pk_help_dkptooltip']							= "If activated, a tooltip with detailed information about the calculation of the points will be shown, when the mouse hovers over the different points.";
$plang['pk_help_smarttooltip']						= "Shortened overview of the tooltips (activate if you got more than three events per account)";
$plang['pk_help_links']									= "In this menu you are able to define different links, which will be displayed in the main menu.";
$plang['pk_help_bosscounter']						= "If activated, a table will be displayed below the main menu with the bosskills. The administration is being managed by the plugin Bossprogress";
$plang['pk_help_lm_leaderboard']					= "If activated, a leaderboard will be displayed above the scoretable. A leaderboard is a table, where the dkp of each class is displayed sorted in decending order.";
$plang['pk_help_lm_rank']								= "An extra column is being displayed, which displays the rank of the member.";
$plang['pk_help_lm_rankicon']						= "Instead of the rank name, an icon is being displayed. Which items are available you can check in the folder \images\rank";
$plang['pk_help_lm_level']								= "An additional column is being displayed, which displays the level of the member. ";
$plang['pk_help_lm_lastloot']						= "An extra colums is being displayed, showing the date a member received his latest item.";
$plang['pk_help_lm_lastraid']						= "An extra column is being displayed, showing the date of the latest raid a member has been participated in.";
$plang['pk_help_lm_atten30']							= "An extra column is being displayed, showing a members participation in raid during the last 30 days (in percent).";
$plang['pk_help_lm_atten60']							= "An extra column is being displayed, showing a members participation in raid during the last 60 days (in percent). ";
$plang['pk_help_lm_atten90']							= "An extra column is being displayed, showing a members participation in raid during the last 90 days (in percent). ";
$plang['pk_help_lm_attenall']						= "An extra column is being displayed, showing a members overall raid participation (in percent).";
$plang['pk_help_itemstats_on']						= "Itemstats is requesting information about items entered in EQDKP in the WOW databases (Blasc, Allahkazm, Thottbot). These will be displayed in the color of the items quality including the known WOW tooltip. When active, items will be shown with a mouseover tooltip, similar to WOW.";
$plang['pk_help_itemstats_search']				= "Which database should Itemstats use first to lookup information? Blasc or Allakhazam?";
$plang['pk_help_itemstats_icon_ext']			= "Filename extension of the pictures to be shown. Usually .png or .jpg.";
$plang['pk_help_itemstats_icon_url']						= "Please enter the URL where you Itemstats pictures are being located. German: http://www.buffed.de/images/wow/32/ in 32x32 or http://www.buffed.de/images/wow/64/ in 64x64 pixels.English at Allakzam: http://www.buffed.de/images/wow/32/";
$plang['pk_help_itemstats_translate_deeng']		= "If active, information of the tooltips will be requested in german, even when the item is being entered in english.";
$plang['pk_help_itemstats_translate_engde']		= "If active, information of the tooltips will be requested in English, even if the item is being entered in german.";

$plang['pk_set_leaderboard_2row']					= 'Leaderboard in 2 lines';
$plang['pk_set_leaderboard_limit']					= 'limitation of the display';

$plang['pk_help_leaderboard_2row']					= 'If active, the Leaderboard will be displayed in two lines with 4 or 5 classes each.';
$plang['pk_help_leaderboard_limit']				= 'If a numeric number is being entered, the Leaderboard will be restricted to the entered number of members. The number 0 represents no restrictions.';

$plang['pk_set_newsloot_limit']						= 'newsloot limit';
$plang['pk_help_newsloot_limit']						= 'How many items should be displayed in the news? This restricts the display of items, which will be displeyed in the news. The number 0 represents no restrictions.';

$plang['pk_set_itemstats_debug']						= 'debug Modus';
$plang['pk_help_itemstats_debug']					= 'Wenn aktiviert, werden alle Schritte von Itemstats in die Datei /itemstats/includes_de/debug.txt geschrieben. Diese MUSS aber beschreibbar sein. CHMOD 777 !!!';

$plang['pk_set_showclasscolumn']						= 'show classes column';
$plang['pk_help_showclasscolumn']					= 'If activated, an extra column is being displayed showing the class of the player.' ;

$plang['pk_set_show_skill']								= 'show skill column';
$plang['pk_help_show_skill']								= 'If activated, an extra column is being displayed showing the skill of the player.';

$plang['pk_set_show_arkan_resi']						= 'show arcan resistance column';
$plang['pk_help_show_arkan_resi']					= 'If activated, an extra column is being displayed showing the arcane resistance of the player.';

$plang['pk_set_show_fire_resi']						= 'show fire resistance column';
$plang['pk_help_show_fire_resi']						= 'If activated, an extra column is being displayed showing the fire resistance of the player.';

$plang['pk_set_show_nature_resi']					= 'show nature resistance column';
$plang['pk_help_show_nature_resi']					= 'If activated, an extra column is being displayed showing the nature resistance of the player.';

$plang['pk_set_show_ice_resi']							= 'show ice resistance column';
$plang['pk_help_show_ice_resi']						= 'If activated, an extra column is being displayed showing the frost resistance of the player.';

$plang['pk_set_show_shadow_resi']					= 'show shadow resistance column';
$plang['pk_help_show_shadow_resi']					= 'If activated, an extra column is being displayed showing the shadow resistance of the player.';

$plang['pk_set_show_profils']							= 'show profile link column';
$plang['pk_help_show_profils']							= 'If activated, an extra column is being displayed showing the links to the profile.';


?>