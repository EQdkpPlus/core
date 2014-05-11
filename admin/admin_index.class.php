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
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 *
 * $Id$
 */
if(!class_exists('admin_index')) {
class admin_index extends gen_class {
	public static $shortcuts = array('user', 'in', 'core', 'config', 'tpl', 'game', 'jquery', 'pm', 'time', 'pdh', 'db', 'pdc',
		'xmltools'	=> 'xmltools',
		'puf'		=> 'urlfetcher',
		'UpdateCheck'	=> 'repository',
	);

	protected $extension_updates	= '';
	protected $rsscachetime			= 48;
	public $admin_menu				= '';

	public function __construct(){
		$this->user->check_auth('a_');
		if($this->in->exists('rssajax')) {
			if($this->in->get('rssajax') == 'twitter') $this->ajax_twitterfeed();
			if($this->in->get('rssajax') == 'notification') $this->ajax_notification();
		}
		if ($this->in->exists('ip_resolve')) $this->resolve_ip();
		$this->updatecheck();
		$this->adminmenu_output();
	}

	private function resolve_ip(){
		$out = "Could not resolve IP";
		if ($this->in->get('ip_resolve') != ""){
			$return = $this->puf->fetch("http://www.geoplugin.net/php.gp?ip=".$this->in->get('ip_resolve'));
			if ($return){
				$unserialized = @unserialize($return);
				if ($unserialized){
					$out = ($unserialized['geoplugin_city'] != "") ? $unserialized['geoplugin_city'].'<br />' : '';
					$out .= ($unserialized['geoplugin_regionName'] != "") ? $unserialized['geoplugin_regionName'].'<br />' : '';
					$out .= ($unserialized['geoplugin_countryName'] != "") ? $unserialized['geoplugin_countryName'] : '';
						
					if (!strlen($out)) $out = "Could not resolve IP"; 
				}
			}
		}
		echo $out;
		exit;
	}
	
	private function updatecheck(){
		// Check for required Plugin Database Updates
		$this->core_updates			= $this->UpdateCheck->UpdateBadge('uc_coreupdate_available', true);
		$this->extension_updates	= $this->UpdateCheck->UpdateBadge('uc_update_available');

		//new plugin-update-check (only minor, without db-updates)
		$this->pm->plugin_update_check();
		
		/* DISABLED - It doesnt make sense to reinstall the game if an update occured, since it is impossible for the system to know wether it overwrites data changed by the user
						Use maintenance Updates for adding new things to the database (placed in e.g. games/wow/updates)
		//game-update-check
		if(compareVersion($this->game->gameVersion(), $this->config->get('game_version')) == 1) {
			//check for game-task
			$found = false;
			if(!isset($tasks)) $tasks = registry::register('mmtaskmanager')->get_task_list(true);
			foreach($tasks as $task => $file) {
				if(strpos($task, 'update_'.$this->config->get('default_game')) === 0) {
					include_once($file);
					if(compareVersion(registry::register($task)->version, $this->config->get('game_version')) == 1) {
						$found = true;
					}
				}
			}
			//no update: reinstall the game
			if(!$found) {
				$this->game->ChangeGame($this->config->get('default_game'), $this->config->get('game_language'));
			}
		}
		*/
	}

	public function adminmenu($blnShowBadges = true){
		$admin_menu = array(
			'members' => array(
				'icon'	=> 'manage_members.png',
				'name'	=> $this->user->lang('chars'),
				1		=> array('link' => 'admin/manage_members.php'.$this->SID,			'text' => $this->user->lang('manage_members'),	'check' => 'a_members_man',	'icon'	=> 'manage_members.png'),
				2		=> array('link' => 'admin/manage_items.php'.$this->SID,			'text' => $this->user->lang('manitems_title'),	'check' => 'a_item_',		'icon' => 'manage_items.png'),
				3		=> array('link' => 'admin/manage_adjustments.php'.$this->SID,		'text' => $this->user->lang('manadjs_title'),		'check' => 'a_indivadj_',	'icon' => 'manage_adjustments.png'),
				4		=> array('link' => 'admin/manage_ranks.php'.$this->SID,			'text' => $this->user->lang('manrank_title'),		'check' => 'a_members_man',	'icon' => 'manage_ranks.png'),
				5		=> array('link' => 'admin/manage_profilefields.php'.$this->SID,	'text' => $this->user->lang('manage_pf_menue'),	'check' => 'a_config_man',	'icon' => 'manage_profilefields.png'),
				6		=> array('link' => 'admin/manage_roles.php'.$this->SID,			'text' => $this->user->lang('rolemanager'),		'check' => 'a_config_man',	'icon' => 'manage_roles.png'),
				7		=> array('link' => 'admin/manage_auto_points.php'.$this->SID,		'text' => $this->user->lang('manage_auto_points'),'check' => 'a_config_man',	'icon' => 'manage_auto_points.png'),
			),
			'users' => array(
				'icon'	=> 'manage_users.png',
				'name'	=> $this->user->lang('users'),
				1		=> array('link' => 'admin/manage_users.php'.$this->SID,			'text' => $this->user->lang('manage_users'),		'check' => 'a_users_man',	'icon' => 'manage_users.png'),
				2		=> array('link' => 'admin/manage_user_groups.php'.$this->SID,		'text' => $this->user->lang('manage_user_groups'),'check' => 'a_users_man',	'icon' => 'manage_user_groups.png'),
				3		=> array('link' => 'admin/manage_maintenance_user.php'.$this->SID,'text' => $this->user->lang('maintenanceuser_user'),'check' => 'a_maintenance','icon' => 'manage_maintenance_user.png'),
				4		=> array('link' => 'admin/manage_massmail.php'.$this->SID,'text' => $this->user->lang('massmail'),'check' => 'a_users_massmail','icon' => 'manage_massmail.png'),
			),
			'extensions' => array(
				'name'	=> $this->user->lang('extensions').(($blnShowBadges) ? $this->extension_updates : ''),
				1		=> array('link' => 'admin/manage_extensions.php'.$this->SID,		'text' => $this->user->lang('extension_repo'),'check' => 'a_config_man',	'icon' => 'manage_extension.png'),
			),
			'portal'	=> array(
				'icon'	=> 'manage_portal.png',
				'name'	=> $this->user->lang('portal'),
				1		=> array('link' => 'admin/manage_portal.php'.$this->SID,			'text' => $this->user->lang('portalmanager'),		'check' => 'a_config_man',	'icon' => 'manage_portal.png'),
				2		=> array('link' => 'admin/manage_news.php'.$this->SID,			'text' => $this->user->lang('manage_news'),		'check' => 'a_news_',		'icon' => 'manage_news.png'),
				3		=> array('link' => 'admin/manage_pages.php'.$this->SID,		'text' => $this->user->lang('info_manage_pages'),	'check' => 'a_pages_man','icon' => 'manage_pages.png'),
				4		=> array('link' => 'admin/manage_pagelayouts.php'.$this->SID,		'text' => $this->user->lang('page_manager'),		'check' => 'a_config_man',	'icon' => 'manage_pagelayouts.png'),
				5		=> array('link' => 'admin/manage_menus.php'.$this->SID,			'text' => $this->user->lang('manage_menus'),		'check' => 'a_config_man',	'icon' => 'manage_menus.png'),
			),
			'raids'	=> array(
				'icon'	=> 'manage_raids.png',
				'name'	=> $this->user->lang('raids'),
				1		=> array('link' => 'admin/manage_raids.php'.$this->SID,			'text' => $this->user->lang('manage_raids'),		'check' => 'a_raid_add',	'icon' => 'manage_raids.png'),
				2		=> array('link' => 'admin/manage_events.php'.$this->SID,			'text' => $this->user->lang('manevents_title'),	'check' => 'a_event_upd',	'icon' => 'manage_events.png'),
				3		=> array('link' => 'admin/manage_multidkp.php'.$this->SID,		'text' => $this->user->lang('manmdkp_title'),		'check' => 'a_event_upd',	'icon' => 'manage_multidkp.png'),
				4		=> array('link' => 'admin/manage_itempools.php'.$this->SID,		'text' => $this->user->lang('manitempools_title'),'check' => 'a_event_upd',	'icon' => 'manage_itempools.png'),
				5		=> array('link' => 'admin/manage_export.php'.$this->SID,		'text' => $this->user->lang('manexport_title'),'check' => 'a_',	'icon' => 'manage_export.png'),
			),
			'calendar'	=> array(
				'icon'	=> 'manage_calendars.png',
				'name'	=> $this->user->lang('calendars'),
				1		=> array('link' => 'admin/manage_calendars.php'.$this->SID,		'text' => $this->user->lang('manage_calendars'),	'check' => 'a_calendars_man',	'icon' => 'manage_calendars2.png'),
				2		=> array('link' => 'admin/manage_calevents.php'.$this->SID,		'text' => $this->user->lang('manage_calevents'),	'check' => 'a_cal_event_man',	'icon' => 'manage_calevents.png'),
			),
			'general' => array(
				'icon'	=> 'manage_settings.png',
				'name'	=> $this->user->lang('general_admin'),
				1		=> array('link' => 'admin/manage_settings.php'.$this->SID,		'text' => $this->user->lang('configuration'),		'check' => 'a_config_man',	'icon' => 'manage_settings.png'),
				2		=> array('link' => 'admin/manage_logs.php'.$this->SID,			'text' => $this->user->lang('view_logs'),			'check' => 'a_logs_view',	'icon' => 'manage_logs.png'),
				3		=> array('link' => 'admin/manage_tasks.php'.$this->SID,			'text' => $this->user->lang('mantasks_title'),		'check' => array('OR', array('a_users_man', 'a_members_man')),	'icon' => 'manage_tasks.png'),
				4		=> array('link' => 'admin/manage_bridge.php'.$this->SID,			'text' => $this->user->lang('manage_bridge'),	'check' => 'a_config_man',	'icon' => 'manage_bridge.png'),
				5		=> array('link' => 'admin/manage_crons.php'.$this->SID,			'text' => $this->user->lang('manage_cronjobs'),		'check' => 'a_config_man',	'icon' => 'manage_crons.png')
			),
			'maintenance' => array(
				'icon'	=> 'task_manager.png',
				'name'	=> $this->user->lang('menu_maintenance').(($blnShowBadges) ? $this->core_updates : ''),
				1		=> array('link' => 'maintenance/task_manager.php'.$this->SID,		'text' => $this->user->lang('maintenance'),		'check' => 'a_maintenance',	'icon' => 'task_manager.png'),
				2		=> array('link' => 'admin/manage_live_update.php'.$this->SID,		'text' => $this->user->lang('liveupdate'),		'check' => 'a_maintenance',	'icon' => 'manage_live_update.png'),
				3		=> array('link' => 'admin/manage_backup.php'.$this->SID,			'text' => $this->user->lang('backup'),			'check' => 'a_backup',		'icon' => 'manage_backup.png'),
				4		=> array('link' => 'admin/manage_reset.php'.$this->SID,			'text' => $this->user->lang('reset'),				'check' => 'a_config_man',	'icon' => 'manage_reset.png'),
				5		=> array('link' => 'admin/manage_cache.php'.$this->SID,			'text' => $this->user->lang('pdc_manager'),		'check' => 'a_config_man',	'icon' => 'manage_cache.png'),
				6		=> array('link' => 'admin/info_database.php'.$this->SID,			'text' => $this->user->lang('mysql_info'),		'check' => 'a_config_man',	'icon' => 'info_database.png'),				
			),
		);

		// Now get plugin hooks for the menu
		$admin_menu = (is_array($this->pm->get_menus('admin_menu'))) ? array_merge_recursive($admin_menu, array('extensions'=>$this->pm->get_menus('admin_menu'))) : $admin_menu;

		//Now get the admin-favorits
		$favs_array = array();
		if($this->config->get('admin_favs')) {
			$favs_array = @unserialize(stripslashes($this->config->get('admin_favs')));
		}
		$admin_menu['favorits']['icon'] = 'favorites.png';
		$admin_menu['favorits']['name'] = $this->user->lang('favorits');
		//Style Management
		$admin_menu['favorits'][1] = array(
			'link' => 'admin/manage_extensions.php'.$this->SID.'&tab=1',
			'text'	=> $this->user->lang('styles_title'),
			'check'	=> 'a_extensions_man',
			'icon'	=> 'manage_styles.png',
		);
			
		$i = 2;
		if (is_array($favs_array) && count($favs_array) > 0){
			foreach ($favs_array as $fav){
				$items = explode('|', $fav);
				$adm = $admin_menu;
				foreach ($items as $item){
					$latest = $adm;
					$adm = (isset($adm[$item])) ? $adm[$item] : false;
				}
				if (isset($adm['link'])){
					$admin_menu['favorits'][$i] = array(
						'link' => $adm['link'],
						'text'	=> $adm['text'].((count($items) == 3) ? ' ('.$latest['name'].')': ''),
						'check'	=> $adm['check'],
						'icon'	=> $adm['icon'],
					);
				}
				$i++;
			}
		} else { //If there are no links, point to the favorits-management
			$admin_menu['favorits'][2] = array(
				'link' => 'admin/manage_menus.php'.$this->SID.'&tab=4',
				'text'	=> $this->user->lang('manage_menus'),
				'check'	=> 'a_config_man',
				'icon'	=> 'manage_menus.png',
			);
		}
		
		return $admin_menu;
	}
	
	public function adminmenu_output(){
		$this->admin_menu = $this->adminmenu();
		
		// menu output
		$this->tpl->assign_vars(array(
			'L_ADMINISTRATION'	=> $this->user->lang('administration'),
			'L_ADMIN_INDEX'		=> $this->user->lang('admin_index'),
			'L_EQDKP_INDEX'		=> $this->user->lang('eqdkp_index'),
			'ADMIN_MENU'		=> $this->jquery->SuckerFishMenu($this->admin_menu,'sf-menu',$this->root_path.'images/admin/')
		));
	}

	public function ajax_twitterfeed(){
		$data = $this->pdc->get('core.twitterfeed_data');
		if ($data != null){
		//there is cached data
			$rsstwitter_out = $this->xmltools->prepareLoad($data);
		} else {
		//expired or not available, update from Server
			include_once($this->root_path.'libraries/twitter/codebird.class.php');
			Codebird::setConsumerKey(TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET); // static, see 'Using multiple Codebird instances'

			$cb = Codebird::getInstance();
			$cb->setToken(TWITTER_OAUTH_TOKEN, TWITTER_OAUTH_SECRET);
			$params = array(
				'screen_name' => "EQdkpPlus",
			);
			$objJSON = $cb->statuses_userTimeline($params);
			
			if ($objJSON) {
								
				require_once($this->root_path.'core/feed.class.php');
				$feed				= registry::register('feed');
				$feed->title		= "EQdkp Plus Twitter";
				$feed->description	= "EQdkp Plus Twitter";
				$feed->published	= time();
				$feed->language		= 'EN-EN';
				
				if ($objJSON){
				
					foreach($objJSON as $key=>$objEntry){
						if ($objEntry->in_reply_to_user_id_str != "") continue;
						if (strpos($objEntry->text, '@') === 0) continue;
						//print_r($objEntry->text);
						
						$truncated = $objEntry->text;
						if (strlen($objEntry->text) > 40){
							$truncated = substr($objEntry->text,0,strpos($objEntry->text,' ',40));
							if ($truncated != '') {
								$truncated = $truncated.'...';
							}
						}
						
						$rssitem = registry::register('feeditems', array($key));
						$rssitem->title			= $truncated;
						$rssitem->description	= $objEntry->text;
						$rssitem->published		= $objEntry->created_at;
						$rssitem->author		= 'EQdkp Plus';
						$rssitem->link			= "https://twitter.com/EQdkpPlus/status/".$objEntry->id_str;
						$feed->addItem($rssitem);
						
					}

				}
				
				$rsstwitter_out = $feed->show();
				$this->pdc->put('core.twitterfeed_data', $this->xmltools->prepareSave($rsstwitter_out), $this->rsscachetime*3600);
				
			} else {
				$expiredData = $this->pdc->get('core.twitterfeed_data', false, false, true);		
				
				$this->pdc->put('core.twitterfeed_data', ($expiredData != null) ? $expiredData : "", 6*3600);
				$rsstwitter_out = ($expiredData != null) ? $this->xmltools->prepareLoad($expiredData) : "";
			}
		}
		header('content-type: text/html; charset=UTF-8');
		print $rsstwitter_out;
		exit;
	}

	public function ajax_notification(){
		$data = $this->pdc->get('core.notifications_data');
		if ($data != null){
		//there is cached data
			$rss_out = $this->xmltools->prepareLoad($data);		
		} else {
		//expired or not available, update from Server
			$fetchedData = $this->puf->fetch(EQDKP_NOTIFICATIONS_URL);
			if ($fetchedData) {
				$this->pdc->put('core.notifications_data', $this->xmltools->prepareSave($fetchedData), $this->rsscachetime*3600);
				$rss_out = $fetchedData;
			} else {
				$expiredData = $this->pdc->get('core.notifications_data', false, false, true);		
				
				$this->pdc->put('core.notifications_data', ($expiredData != null) ? $expiredData : "", 6*3600);
				$rss_out = ($expiredData != null) ? $this->xmltools->prepareLoad($expiredData) : "";
			}
		}

		print $rss_out;
		exit;
	}

	public function display(){
		/****************************************************************
		* STATISTICS
		****************************************************************/
		$days					= (($this->time->time - $this->config->get('eqdkp_start')) / 86400);

		$total_members_			= count($this->pdh->get('member', 'id_list'));
		$total_members_active	= count($this->pdh->get('member', 'id_list', array(true)));
		$total_members_inactive	= $total_members_ - $total_members_active;
		$total_members			= $total_members_active . ' / ' . $total_members_inactive;

		$total_raids			= $this->db->query_first('SELECT count(*) FROM __raids');
		$raids_per_day			= sprintf("%.2f", ($total_raids / $days));

		$total_items			= $this->db->query_first('SELECT count(*) FROM __items');
		$items_per_day			= sprintf("%.2f", ($total_items / $days));
		$total_logs				= $this->db->query_first('SELECT count(*) FROM __logs');

		if ( (float)$raids_per_day > (float)$total_raids ){
			$raids_per_day = $total_raids;
		}
		if ( (float)$items_per_day > (float)$total_items ){
			$items_per_day = $total_items;
		}

		$arrTables = $this->db->get_table_information();
		$dbsize = 0;
		foreach ($arrTables as $key => $value){
			$dbsize += $value['data_length'] + $value['index_length'];
		}

		if(is_int($dbsize)){
			$dbsize = ( $dbsize >= 1048576 ) ? sprintf('%.2f MB', ($dbsize / 1048576)) : (($dbsize >= 1024) ? sprintf('%.2f KB', ($dbsize / 1024)) : sprintf('%.2f Bytes', $dbsize));
		}
	
		$this->tpl->add_js('
			$(".ip_resolver").each(function() {
				$(this).qtip({
					content: {
						text: \'<img class="throbber" src="images/global/loading.gif" alt="Loading..." />\',
						ajax: {
							url: \'index.php'.$this->SID.'\',
							data: { ip_resolve: $(this).html() }
						},
					},
					position: {
						at: "bottom right",
						my: "top right"
					},
					style: {
						width: 150,
						tip: {
							corner: true,
							width: 20
						},
						widget: true
					}
				});
			});', 'docready');
		
		// Who's Online
		$sql = 'SELECT s.*, u.username
							FROM ( __sessions s
							LEFT JOIN __users u
							ON u.user_id = s.session_user_id )
							GROUP BY u.username, s.session_ip
							ORDER BY u.username, s.session_current DESC';
		$result = $this->db->query($sql);
		while ($row = $this->db->fetch_record($result)){
			$this->tpl->assign_block_vars('online_row', array(
				'USERNAME'		=> ( !empty($row['username']) ) ? $row['username'] : $this->user->lang('anonymous'),
				'LOGIN'			=> $this->time->user_date($row['session_start'], true),
				'LAST_UPDATE'	=> $this->time->user_date($row['session_current'], true),
				'LOCATION'		=> resolve_eqdkp_page($row['session_page']),
				'BROWSER'		=> resolve_browser($row['session_browser']),
				'IP_ADDRESS'	=> $row['session_ip'])
			);
		}
		$online_count = $this->db->num_rows($result);
		
		//$this->jquery->qtip('.ip_resolver', 'Loading', array('ajax' => "url: '".$this->root_path."admin/index.php".$this->SID."', type:'GET', data:{ip_resolve: $(this).html()}"));
		

		// Log Actions
		$s_logs = false;
		$logs_table = '';
		if ($this->user->check_auth('a_logs_view', false)) {
			$logfiles = $this->pdh->get('logs', 'lastxlogs', array(10));
			if (is_array($logfiles) && count($logfiles) > 0) {
				$log_setts = $this->pdh->get_page_settings('admin_index', 'hptt_latest_logs');
				$hptt = registry::register('html_pdh_tag_table', array($log_setts, $logfiles, $logfiles, array('%link_url%' => 'manage_logs.php', '%link_url_suffix%' => '')));
				$logs_table = $hptt->get_html_table('','',null,null,'<a href="'.$this->root_path.'admin/manage_logs.php'.$this->SID.'">'.$this->user->lang('view_all_actions').'</a>');
				$s_logs = true;
				unset($hptt);
			}
		}

		// The Jquery Things & Update Check
		$this->jquery->Tab_header('admininfos_tabs');
		$this->jquery->rssFeeder('notifications',	"index.php".$this->SID."&rssajax=notification", '3', '999');
		$this->jquery->rssFeeder('twitterfeed',	"index.php".$this->SID."&rssajax=twitter");

		$this->tpl->assign_vars(array(
			//Logs
			'S_LOGS'				=> $s_logs,
			'LOGS_TABLE'			=> $logs_table,

			// Server Information
			'SERVERINFO_SAFEMODE'	=> $this->get_php_setting('safe_mode',1,0),
			'SERVERINFO_REGGLOBAL'	=> $this->get_php_setting('register_globals',1,0),
			'SERVERINFO_CURL'		=> $this->get_curl_setting(1),
			'SERVERINFO_FOPEN'		=> $this->check_PHP_Function('fopen',1),
			'SERVERINFO_MYSQL'		=> 'Client ('.$this->db->client_version().')<br/>Server ('.$this->db->server_version().')',
			'SERVERINFO_PHP'		=> (((phpversion() >= VERSION_PHP_RQ) ? '<span class="positive">' : '<span class="negative">').phpversion().'</span>'),

			'NUMBER_OF_MEMBERS'		=> $total_members,
			'NUMBER_OF_RAIDS'		=> $total_raids,
			'NUMBER_OF_ITEMS'		=> $total_items,
			'DATABASE_SIZE'			=> $dbsize,
			'NUMBER_OF_LOGS'		=> $total_logs,
			'RAIDS_PER_DAY'			=> $raids_per_day,
			'ITEMS_PER_DAY'			=> $items_per_day,
			'EQDKP_STARTED'			=> $this->time->user_date($this->config->get('eqdkp_start'), true),
			'SHOW_BETA_WARNING'		=> VERSION_WIP,
			'SHOW_PHP_WARNING'		=> (version_compare(PHP_VERSION, VERSION_PHP_REC, '<=')) ? true : false,
			'ONLINE_FOOTCOUNT'		=> sprintf($this->user->lang('online_footcount'), $online_count),
			'SHOW_LIMITED_FUNCS'	=> false,
			'DATABASE_NAME'			=> $this->dbname,
			'TABLE_PREFIX'			=> $this->table_prefix,
			'DATA_FOLDER'			=> md5($this->table_prefix.$this->dbname),
			'EQDKP_VERSION'			=> 'FILE: '.VERSION_INT.', DB: '.$this->config->get('plus_version'),
			
			'S_WHO_IS_ONLINE'		=> $this->user->check_group(2, false),
		));

		//Check permissions of config.php
		if (!defined('EQDKP_DISABLE_CONFIG_CHECK') && file_exists($this->root_path.'config.php')){
			if ((int)substr(decoct(fileperms($this->root_path.'config.php')),3) > 644){
				$this->tpl->assign_var('SHOW_LIMITED_FUNCS', true);
				$this->tpl->assign_block_vars('lim_funcs', array(
					'TEXT'		=> $this->user->lang('config_writable'),
				));
			}
		}

		if(!function_exists('date_create_from_format')) {
			$this->tpl->assign_var('SHOW_LIMITED_FUNCS', true);
			$this->tpl->assign_block_vars('lim_funcs', array(
				'TEXT'		=> $this->user->lang('lim_func_fromformat'),
			));
		}

		$this->core->set_vars(array(
		'page_title'	=> $this->user->lang('admin_index_title'),
		'template_file'	=> 'admin/admin_index.html',
		'display'		=> true)
		);
	}

	// Helper Functions
	private function get_curl_setting($colour=0){
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
			return ($r) ? '<span class="positive">'.$this->user->lang('cl_on').'</span> ('.$version.')' : '<span class="negative">'.$this->user->lang('cl_off').'</span>';
		} else {
			return ($r) ? $this->user->lang('cl_on') : $this->user->lang('cl_off');
		}
	}

	private function get_php_setting($val, $colour=0, $yn=1) {
		$r =  (ini_get($val) == '1' ? 1 : 0);
		if ($colour){
			if ($yn){
				$r = $r ? '<span class="positive">'.$this->user->lang('cl_on').'</span>' : '<span class="negative">'.$this->user->lang('cl_off').'</span>';
			} else {
				$r = $r ? '<span class="negative">'.$this->user->lang('cl_on').'</span>' : '<span class="positive">'.$this->user->lang('cl_off').'</span>';
			}
			return $r;
		} else {
			return ($r) ? $this->user->lang('cl_on') : $this->user->lang('cl_off');
		}
	}

	private function check_PHP_Function($_function,$colour=0){
		$r =  (function_exists($_function) ? 1 : 0);
		if ($colour) {
			return ($r) ? '<span class="positive">'.$this->user->lang('cl_on').'</span>' : '<span class="negative">'.$this->user->lang('cl_off').'</span>';
		} else {
			return ($r) ? $this->user->lang('cl_on') : $this->user->lang('cl_off');
		}
	}
}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_admin_index', admin_index::$shortcuts);
?>