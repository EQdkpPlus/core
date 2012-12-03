<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2002
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2010 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 * 
 * $Id$
 */

// IN_ADMIN not yet defined, display the main admin page
if ( !defined('IN_ADMIN') ){

	function get_news_name($news_id){
		global $db;

		$news_id = intval($news_id);

		$sql = "SELECT news_headline FROM __news WHERE news_id='" . $news_id . "'";
		$news_name = $db->query_first($sql);

		return ( !empty($news_name) ) ? $news_name : 'Unknown';
	}

	// ---------------------------------------------------------
	// Display the main admin page
	// ---------------------------------------------------------
	define('EQDKP_INC', true);
	define('IN_ADMIN', true);
	$eqdkp_root_path = './../';
	include_once($eqdkp_root_path . 'common.php');

	$user->check_auth('a_');

	// Start of RSS Feeds
	if($in->get('rssajax')){
		$rsscachetime = 48;

		// Check if the cached Data is up2date, if not, update!
		if($core->config['pk_rssfeed_lefttime'] < (time()+($rsscachetime*3600))){
			$rssleft_out = $urlreader->GetURL('http://rss.eqdkp-plus.com/notifications.xml');
			$save_array = array(
				'pk_rssfeed_righttime'	=> time(),
				'pk_rssfeed_rightdata'	=> $xmltools->prepareSave($rssleft_out)
			);
			$core->config_set($save_array);
		}else{
			$rssleft_out = $xmltools->prepareLoad($core->config['pk_rssfeed_leftdata']);
		}
		if($core->config['pk_rssfeed_twittertime'] < (time()+($rsscachetime*3600))){
			$rsstwitter_out = $urlreader->GetURL('http://rss.eqdkp-plus.com/twitter.xml');
			$save_array = array(
				'pk_rssfeed_twittertime' => time(),
				'pk_rssfeed_twitterdata' => $xmltools->prepareSave($rsstwitter_out)
			);
			$core->config_set($save_array);
		}else{
			$rsstwitter_out = $xmltools->prepareLoad($core->config['pk_rssfeed_twitterdata']);
		}

		// Output the RSS Data
		header('content-type: application/xml');
		switch($in->get('rssajax')){
			case 'notification':	print $rssleft_out;			break;
			case 'twitter':				print $rsstwitter_out;	break;
		}
		die();
	}// End of RSS Feeds

	/****************************************************************
	 * STATISTICS
	 ****************************************************************/
	$days					= ((time() - $core->config['eqdkp_start']) / 86400);

	$total_members_			= count($pdh->get('member', 'id_list'));    
	$total_members_active	= count($pdh->get('member', 'id_list', array(true)));
	$total_members_inactive = $total_members_ - $total_members_active;
	$total_members			= $total_members_active . ' / ' . $total_members_inactive;

	$total_raids			= count($pdh->get('raid', 'id_list'));  
	$raids_per_day			= sprintf("%.2f", ($total_raids / $days));

	$total_items			= count($pdh->get('item', 'id_list')); 
	$items_per_day			= sprintf("%.2f", ($total_items / $days));

	$total_logs				= $db->query_first('SELECT count(*) FROM __logs');

	if ( $raids_per_day > $total_raids ){
		$raids_per_day = $total_raids;
	}
	if ( $items_per_day > $total_items ){
		$items_per_day = $total_items;
	}

	// DB Size - MySQL Only
	if ( DBTYPE == 'mysql' ){
		$result = $db->query('SELECT VERSION() AS mysql_version');
		if ($row = $db->fetch_record($result)){
			$version = $row['mysql_version'];

			if (preg_match('/^(3\.23|4\.|5\.)/', $version)){
				$db_name = ( preg_match('/^(3\.23\.[6-9])|(3\.23\.[1-9][1-9])|(4\.)|(5\.)/', $version) ) ? "`$dbname`" : $dbname;

				$sql = 'SHOW TABLE STATUS FROM '.$db_name;
				$result = $db->query($sql);

				$dbsize = 0;
				while ( $row = $db->fetch_record($result) ){
					if ( $row['Type'] != 'MRG_MyISAM' ){
						if ( $table_prefix != '' ){
							if ( strstr($row['Name'], $table_prefix) ){
								$dbsize += $row['Data_length'] + $row['Index_length'];
							}
						}else{
							$dbsize += $row['Data_length'] + $row['Index_length'];
						}
					}
				}
			}else{
				$dbsize = $user->lang['not_available'];
			}
		}else{
			$dbsize = $user->lang['not_available'];
		}
	}else{
		$dbsize = $user->lang['not_available'];
	}

	if(is_int($dbsize)){
		$dbsize = ( $dbsize >= 1048576 ) ? sprintf('%.2f MB', ($dbsize / 1048576)) : (($dbsize >= 1024) ? sprintf('%.2f KB', ($dbsize / 1024)) : sprintf('%.2f Bytes', $dbsize));
	}

	// Who's Online
	$sql = 'SELECT s.*, u.username
			FROM ( __sessions s
			LEFT JOIN __users u
			ON u.user_id = s.session_user_id )
			GROUP BY u.username, s.session_ip
			ORDER BY u.username, s.session_current DESC';
	$result = $db->query($sql);
	while ($row = $db->fetch_record($result)){
		$tpl->assign_block_vars('online_row', array(
			'ROW_CLASS'		=> $core->switch_row_class(),
			'USERNAME'		=> ( !empty($row['username']) ) ? $row['username'] : $user->lang['anonymous'],
			'LOGIN'			=> date($user->style['date_time'], $row['session_start']),
			'LAST_UPDATE'	=> date($user->style['date_time'], $row['session_current']),
			'LOCATION'		=> resolve_eqdkp_page($row['session_page']),
			'BROWSER'		=> resolve_browser($row['session_browser']),
			'IP_ADDRESS'	=> $row['session_ip'])
		);
	}
	$online_count = $db->num_rows($result);

	// Log Actions
	$s_logs = false;
	if ($user->check_auth('a_logs_view', false)){
		$logfiles = $logs->GetList('', false, 10);

		if (is_array($logfiles)){
			foreach($logfiles as $log){
				$row = $log['raw'];
				$logline = resolve_logs($row);
				// Show the log if we have a valid line for it
				if ( isset($logline) ){
					$tpl->assign_block_vars('actions_row', array(
						'ROW_CLASS'		=> $core->switch_row_class(),
						'USER'			=> $pdh->get('user', 'name', array($row['user_id'])),
						'DATE'			=> $time->date($user->style['date_time'], $row['log_date']),
						'IP'			=> $row['log_ipaddress'],
						'U_VIEW_LOG'	=> 'manage_logs.php?logid='.$row['log_id'],
						'ACTION'		=> stripslashes($logline)
					));

					unset($logline);
				}
				$s_logs = true;
			}
		}
	}

	// The Jquery Things & Update Check
	$UpdateCheck  = new UpdateCheck();
	$jquery->Tab_header('admininfos_tabs');
	$jquery->rssFeeder('notifications',	"index.php?rssajax=notification", '3', '200');
	$jquery->rssFeeder('twitterfeed',	"index.php?rssajax=twitter");

	$tpl->assign_vars(array(
		'S_LOGS'				=> $s_logs,
		'PLUS_UPDATE_CHECK'		=> $UpdateCheck->OutputHTML(),

		// Info Tabs
		'L_TAB_UPDATECHECK'		=> $user->lang['adminc_updtcheck'],
		'L_TAB_STATISTIC'		=> $user->lang['adminc_statistics'],
		'L_TAB_SERVER'			=> $user->lang['adminc_server'],
		'L_TAB_SUPPORT'			=> $user->lang['adminc_support'],
		'L_TAB_NEWS'			=> $user->lang['adminc_news'],

		// Server Information
		'L_SERVERINFO_PHPS'		=> $user->lang['adminc_phpname'],
		'L_SERVERINFO_PHPV'		=> $user->lang['adminc_phpvalue'],
		'SERVERINFO_SAFEMODE'	=> get_php_setting('safe_mode',1,0),
		'SERVERINFO_REGGLOBAL'	=> get_php_setting('register_globals',1,0),
		'SERVERINFO_CURL'		=> get_curl_setting(1),
		'SERVERINFO_FOPEN'		=> check_PHP_Function('fopen',1),
		'SERVERINFO_MYSQL'		=> 'Client ('.mysql_get_client_info().')<br/>Server ('.mysql_get_server_info().')',
		'SERVERINFO_PHP'		=> (((phpversion() >= REQUIRED_PHP_VERSION) ? '<span class="positive">' : '<span class="negative">').phpversion().'</span>'),
		'SERVERINFO_STRICT'		=> (($strict_mode) ? '<span class="negative">YES</span>' : '<span class="positive">NO</span>'),
		'L_SUPPORT_INTRO'		=> $user->lang['adminc_support_intro'],
		'L_SUPPORT_TOUR'		=> $user->lang['adminc_support_tour'],
		'L_SUPPORT_WIKI'		=> $user->lang['adminc_support_wiki'],
		'L_SUPPORT_BUGTRACKER'	=> $user->lang['adminc_support_bugtracker'],
		'L_SUPPORT_FORUMS'		=> $user->lang['adminc_support_forums'],

		'L_STATISTICS'			=> $user->lang['statistics'],
		'L_NUMBER_OF_MEMBERS'	=> $user->lang['number_of_members'],
		'L_NUMBER_OF_RAIDS'		=> $user->lang['number_of_raids'],
		'L_NUMBER_OF_ITEMS'		=> $user->lang['number_of_items'],
		'L_DATABASE_SIZE'		=> $user->lang['database_size'],
		'L_NUMBER_OF_LOGS'		=> $user->lang['number_of_logs'],
		'L_RAIDS_PER_DAY'		=> $user->lang['raids_per_day'],
		'L_ITEMS_PER_DAY'		=> $user->lang['items_per_day'],
		'L_EQDKP_STARTED'		=> $user->lang['eqdkp_started'],
		'L_BROWSER'				=> $user->lang['browser'],

		'NUMBER_OF_MEMBERS'		=> $total_members,
		'NUMBER_OF_RAIDS'		=> $total_raids,
		'NUMBER_OF_ITEMS'		=> $total_items,
		'DATABASE_SIZE'			=> $dbsize,
		'NUMBER_OF_LOGS'		=> $total_logs,
		'RAIDS_PER_DAY'			=> $raids_per_day,
		'ITEMS_PER_DAY'			=> $items_per_day,
		'EQDKP_STARTED'			=> date($user->style['date_time'], $core->config['eqdkp_start']),

		'L_WHO_ONLINE'			=> $user->lang['who_online'],
		'L_USERNAME'			=> $user->lang['username'],
		'L_LOGIN'				=> $user->lang['logged_in'],
		'L_LAST_UPDATE'			=> $user->lang['last_update'],
		'L_LOCATION'			=> $user->lang['location'],
		'L_IP_ADDRESS'			=> $user->lang['ip_address'],
		'L_RSS_HEAD1'			=> $user->lang['rssadmin_head1'],
		'L_RSS_HEAD2'			=> $user->lang['rssadmin_head2'],

		'L_NEW_ACTIONS'			=> $user->lang['new_actions'],
		'L_VIEW_ALL_ACTIONS'	=> $user->lang['view_all_actions'],

		'SHOW_PLUSUPDATE'		=> $UpdateCheck->CheckStatus(),
		'SHOW_UPDATE_WARNING'	=> $show_update_warning,
		'SHOW_BETA_WARNING'		=> EQDKPPLUS_VERSION_BETA,
		'BETA_WARNING'			=> $user->lang['beta_warning'],
		'L_NEW_VERSION_NOT_IMG'	=> "<img src='../images/false.png'>",
		'ONLINE_FOOTCOUNT'		=> sprintf($user->lang['online_footcount'], $online_count)
	));

	// Activate the proper Tab
	if($UpdateCheck->CheckStatus()){
		$jquery->Tab_Select('admininfos_tabs', '1');
	}
	
	$core->set_vars(array(
		'page_title'	=> $user->lang['admin_index_title'],
		'template_file'	=> 'admin/admin_index.html',
		'display'		=> true)
	);

// IN_ADMIN already defined, just output the menu
}else{

	// Update Batch
	$updtbatch = '';

	// Check for required Plugin Database Updates
	$UpdateCheck  = new UpdateCheck();
	if($UpdateCheck->UpdateCount() > 0){
		$updtbatch = '&nbsp;&nbsp;<span class="update_available">'.$UpdateCheck->UpdateCount().'</span>';
	}

	//new plugin-update-check (only minor, without db-updates)
	include_once($eqdkp_root_path.'maintenance/includes/mmtaskmanager.class.php');
	$mmt = new mmtaskmanager();
	$tasks = $mmt->get_task_list(true);
	unset($mmt);
	foreach($pm->installed as $plugin) {
		$cur = $pm->get_plugin($plugin);
		$ver_comp = compareVersion($cur->version, $pm->plugin_db_values[$plugin]['plugin_version']);
		if($ver_comp == 1) {
			$found = false;
			foreach($tasks as $task => $file) {
				if(strpos($task, 'update_'.$cur->get_data('path')) === 0) {
					include_once($file);
					$cur_task = new $task();
					if(compareVersion($cur_task->version, $pm->plugin_db_values[$plugin]['plugin_version']) == 1) {
						unset($cur_task);
						$found = true;
					}
				}
			}
			if(!$found) {
				$db->query("UPDATE __plugins SET plugin_version = '".$cur->version."' WHERE plugin_path = '".$cur->get_data('path')."';");
			}
		} elseif($ver_comp == 0 AND isset($cur->build) AND $cur->build > $pm->plugin_db_values[$plugin]['plugin_build']) {
			$pm->set_db_info($plugin, 'plugin_build', $cur->build);
		}
		unset($cur);
	}

	//game-update-check
	if(compareVersion($game->gameVersion(), $core->config['game_version']) == 1) {
		//check for game-task
		$found = false;
		foreach($tasks as $task => $file) {
			if(strpos($task, 'update_'.$core->config['default_game']) === 0) {
				include_once($file);
				$cur_task = new $task();
				if(compareVersion($cur_task->version, $core->config['game_version']) == 1) {
					$found = true;
					unset($cur_task);
				}
			}
		}
		if(!$found) {
			$game->ChangeGame($core->config['default_game'], $core->config['game_language']);
		}
	}

	// Menu
	$admin_menu = array(
		'members' => array(
			'icon'	=> 'members.png',
			'name'	=> $user->lang['chars'],
			1		=> array('link' => 'admin/manage_alts.php'.$SID,			'text' => $user->lang['alt_manager'],			'check' => 'a_members_man',	'icon'	=> 'alts.png'),
			2		=> array('link' => 'admin/manage_members.php'.$SID,			'text' => $user->lang['manage_members'],		'check' => 'a_members_man',	'icon'	=> 'members.png'),
			3		=> array('link' => 'admin/manage_items.php'.$SID,			'text' => $user->lang['manitems_title'],		'check' => 'a_item_',		'icon' => 'items.png'),
			4		=> array('link' => 'admin/manage_adjustments.php'.$SID,		'text' => $user->lang['manadjs_title'],			'check' => 'a_indivadj_',	'icon' => 'lable.png'),
			5		=> array('link' => 'admin/manage_ranks.php'.$SID,			'text' => $user->lang['manrank_title'],			'check' => 'a_members_man',	'icon' => 'ruby.png'),
			6		=> array('link' => 'admin/manage_profilefields.php'.$SID,	'text' => $user->lang['manage_pf_menue'],		'check' => 'a_config_man',	'icon' => 'profilefields.png'),
			//7		=> array('link' => 'admin/manage_auto_points.php'.$SID,		'text' => $user->lang['manage_auto_points'],	'check' => 'a_config_man',	'icon' => 'calculator_edit.png'),
		),
		'users' => array(
			'icon'	=> 'users.png',
			'name'	=> $user->lang['users'],
			1		=> array('link' => 'admin/manage_users.php'.$SID,				'text' => $user->lang['manage_users'],		'check' => 'a_users_man',	'icon' => 'users.png'),
			2		=> array('link' => 'admin/manage_user_groups.php'.$SID,			'text' => $user->lang['manage_user_groups'],'check' => 'a_users_man',	'icon' => 'groups.png'),
			3		=> array('link' => 'admin/maintenance_user.php'.$SID,			'text' => $user->lang['maintenanceuser_user'],'check' => 'a_maintenance','icon' => 'maintenance_user.png'),
		),
		'plugins' => array(
			'name'	=> $user->lang['plugins'],
			1		=> array('link' => 'admin/manage_plugins.php'.$SID,				'text' => $user->lang['manage_plugins'],	'check' => 'a_plugins_man',	'icon' => 'plugin_manage.png'),
		),
		'portal'	=> array(
			'icon'	=> 'portal.png',
			'name'	=> $user->lang['portal'],
			1		=> array('link' => 'admin/manage_portal.php'.$SID,				'text' => $user->lang['portalmanager'],		'check' => 'a_config_man',	'icon' => 'portal.png'),
			2		=> array('link' => 'admin/manage_news.php'.$SID,				'text' => $user->lang['manage_news'],		'check' => 'a_news_',		'icon' => 'news.png'),
			3		=> array('link' => 'admin/manage_infopages.php'.$SID,			'text' => $user->lang['info_manage_pages'],	'check' => 'a_infopages_man','icon' => 'page_gear.png'),
			4		=> array('link' => 'admin/manage_pagelayouts.php'.$SID,			'text' => $user->lang['page_manager'],		'check' => 'a_config_man',	'icon' => 'pages.png'),
			5		=> array('link' => 'admin/manage_menus.php'.$SID,				'text' => $user->lang['manage_menus'],		'check' => 'a_config_man',	'icon' => 'menus.png'),
		),
		'raids'	=> array(
			'icon'	=> 'note.png',
			'name'	=> $user->lang['raids'],
			1		=> array('link' => 'admin/manage_raids.php'.$SID,				'text' => $user->lang['manage_raids'],		'check' => 'a_raid_add',	'icon' => 'note.png'),
			2		=> array('link' => 'admin/manage_events.php'.$SID,				'text' => $user->lang['manevents_title'],	'check' => 'a_event_upd',	'icon' => 'date.png'),
			3		=> array('link' => 'admin/manage_multidkp.php'.$SID,			'text' => $user->lang['manmdkp_title'],		'check' => 'a_event_upd',	'icon' => 'package.png'),
			4		=> array('link' => 'admin/manage_itempools.php'.$SID,			'text' => $user->lang['manitempools_title'],'check' => 'a_event_upd',	'icon' => 'items.png'),
		),
		'general' => array(
			'icon'	=> 'settings.png',
			'name'	=> $user->lang['general_admin'],
			1		=> array('link' => 'admin/settings.php'.$SID,					'text' => $user->lang['configuration'],		'check' => 'a_config_man',	'icon' => 'settings.png'),
			2		=> array('link' => 'admin/manage_logs.php'.$SID,				'text' => $user->lang['view_logs'],			'check' => 'a_logs_view',	'icon' => 'script.png'),
			3		=> array('link' => 'admin/manage_cache.php'.$SID,				'text' => $user->lang['pdc_manager'],		'check' => 'a_config_man',	'icon' => 'server.png'),
			4		=> array('link' => 'admin/manage_tasks.php'.$SID,				'text' => $user->lang['mantasks_title'],	'check' => 'a_config_man',	'icon' => 'tasks.png'),
			5		=> array('link' => 'admin/backup.php'.$SID,						'text' => $user->lang['backup'],			'check' => 'a_backup',		'icon' => 'save.png'),
			6		=> array('link' => 'admin/reset.php'.$SID,						'text' => $user->lang['reset'],				'check' => 'a_config_man',	'icon' => 'delete.png'),
			7		=> array('link' => 'maintenance/task_manager.php'.$SID,			'text' => $user->lang['maintenance'],		'check' => 'a_maintenance',	'icon' => 'cog.png'),
			8		=> array('link' => 'admin/database_info.php'.$SID,				'text' => $user->lang['mysql_info'],		'check' => 'a_config_man',	'icon' => 'information.png'),
			9		=> array('link' => 'admin/manage_bridge.php'.$SID,				'text' => $user->lang['manage_bridge'],		'check' => 'a_config_man',	'icon' => 'link.png'),
			10		=> array('link' => 'admin/manage_crons.php'.$SID,				'text' => $user->lang['manage_cronjobs'],	'check' => 'a_config_man',	'icon' => 'clock.png')
		),
		'styles'	=> array(
			'icon'	=> 'styles.png',
			'name'	=> $user->lang['styles'],
			1		=> array('link' => 'admin/styles.php'.$SID.'&mode=create',		'text' => $user->lang['eq_style_install'],	'check' => 'a_styles_man',	'icon' => 'palette.png'),
			2		=> array('link' => 'admin/styles.php'.$SID,						'text' => $user->lang['manage'],			'check' => 'a_styles_man',	'icon' => 'styles.png')
		),
		'extensions'	=> array(
			'icon'	=> 'extensions.png',
			'name'	=> $user->lang['manage_extensions'].$updtbatch,
			1		=> array('link' => 'admin/extensions.php'.$SID,					'text' => $user->lang['extensions_install'],'check' => 'a_config_man',	'icon' => 'extension_add.png'),
		),
	);

	// Now get plugin hooks for the menu
	$admin_menu = (is_array($pm->get_menus('admin_menu'))) ? array_merge_recursive($admin_menu, array('plugins'=>$pm->get_menus('admin_menu'))) : $admin_menu;

	//Now get the favorits if enabled
	if ($core->config['enable_admin_favs'] == 1){
		$favs_array = unserialize(stripslashes($core->config['admin_favs']));
		$admin_menu['favorits']['icon'] = 'favorits.png';
		$admin_menu['favorits']['name'] = $user->lang['favorits'];
		$i = 1;
		if (is_array($favs_array) && count($favs_array) > 0){
			foreach ($favs_array as $fav){
				$items = explode('|', $fav);
				$adm = $admin_menu;
				foreach ($items as $item){
					$adm = $adm[$item];
				}
				if ($adm['link']){
					$admin_menu['favorits'][$i] = array(
						'link' => $adm['link'],
						'text'	=> $adm['text'].((count($items) == 3) ? ' ('.$user->lang[$items[1]].')': ''),
						'check'	=> $adm['check'],
						'icon'	=> $adm['icon'],
					);
				}
				$i++;
			}
		} else { //If there are no links, point to the favorits-management
			$admin_menu['favorits'][1] = array(
				'link' => 'admin/manage_menus.php'.$SID.'#admin',
				'text'	=> $user->lang['manage_menus'],
				'check'	=> 'a_config_man',
				'icon'	=> 'menus.png',
			);
		}
	}

	$tpl->assign_vars(array(
		'L_ADMINISTRATION'	=> $user->lang['administration'],
		'L_ADMIN_INDEX'		=> $user->lang['admin_index'],
		'L_EQDKP_INDEX'		=> $user->lang['eqdkp_index'],
		'ADMIN_MENU'		=> $jquery->SuckerFishMenu($admin_menu,'sf-menu',$eqdkp_root_path.'images/admin/')
	));

	// Some functions for this page..
	function get_curl_setting($colour=0){
		$r =  (function_exists('curl_version') ? 1 : 0);
		if ($r){
			if (is_array(curl_version())){
				$version = curl_version();
				$version = $version['version'];
			} else {
				$version = curl_version();
			}
		}
		if ($colour) {
			$r = $r ? '<span class="positive">ON</span> ('.$version.')' : '<span class="negative">OFF</span>';
			return $r;
		} else {
			return $r ? 'ON' : 'OFF';
		}
	}

	function get_php_setting($val, $colour=0, $yn=1) {
		$r =  (ini_get($val) == '1' ? 1 : 0);
		if ($colour) {
			if ($yn) {
				$r = $r ? '<span class="positive">ON</span>' : '<span class="negative">OFF</span>';
			} else {
				$r = $r ? '<span class="negative">ON</span>' : '<span class="positive">OFF</span>';
			}
			return $r;
		} else {
			return $r ? 'ON' : 'OFF';
		}
	}

	function check_PHP_Function($_function,$colour=0){
		$r =  (function_exists($_function) ? 1 : 0);
		if ($colour) {
			$r = $r ? '<span class="positive">ON</span>' : '<span class="negative">OFF</span>';
			return $r;
		} else {
			return $r ? 'ON' : 'OFF';
		}
	}
}
?>