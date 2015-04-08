<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2015 EQdkp-Plus Developer Team
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU Affero General Public License as published
 *	by the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU Affero General Public License for more details.
 *
 *	You should have received a copy of the GNU Affero General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if(!class_exists('admin_index')) {
class admin_index extends gen_class {
	public static $shortcuts = array(
		'puf'		=> 'urlfetcher',
		'UpdateCheck'	=> 'repository',
	);

	protected $core_updates			= '';
	protected $extension_updates	= '';
	protected $rsscachetime			= 3;
	public $admin_menu				= '';
	public $admin_functions			= NULL;

	public function __construct(){
		$this->user->check_auth('a_');
		if($this->in->exists('rssajax')) {
			if($this->in->get('rssajax') == 'twitter') $this->ajax_twitterfeed();
			if($this->in->get('rssajax') == 'notification') $this->ajax_notification();
		}
		
		include_once($this->root_path.'core/admin_functions.class.php');
		$this->admin_functions = register('admin_functions');
		
		if ($this->in->exists('ip_resolve')) $this->resolve_ip();
		
		$this->updatecheck();
		$this->adminmenu_output();
	}

	private function resolve_ip(){
		header('content-type: text/html; charset=UTF-8');
		$out = "Could not resolve IP";
		if ($this->in->get('ip_resolve') != ""){
			$return = $this->admin_functions->resolve_ip($this->in->get('ip_resolve'));
			if ($return){
				$out = ($return['city'] != "") ? $return['city'].'<br />' : '';
				$out .= ($return['regionName'] != "") ? $return['regionName'].'<br />' : '';
				$out .= ($return['countryName'] != "") ? $return['countryName'] : '';
					
				if (!strlen($out)) $out = "Could not resolve IP"; 
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

	}
	
	public function adminmenu_output(){
		$this->admin_menu = $this->admin_functions->adminmenu(true, $this->core_updates, $this->extension_updates);
		
		// menu output
		$this->tpl->assign_vars(array(
			'L_ADMINISTRATION'	=> $this->user->lang('administration'),
			'L_ADMIN_INDEX'		=> $this->user->lang('admin_index'),
			'L_EQDKP_INDEX'		=> $this->user->lang('eqdkp_index'),
			'ADMIN_MENU'		=> $this->jquery->SuckerFishMenu($this->admin_menu,'sf-admin',$this->root_path.'images/admin/', false, 'sf-menu'),
			'ADMIN_MENU_MOBILE' => $this->jquery->MenuConstruct_html($this->admin_functions->adminmenu(false, "", "", 'adminmenu-mobile'), 'adminmenu-mobile', $this->root_path.'images/admin/', false, ""),
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
				'screen_name' => EQDKP_TWITTER_SCREENNAME,
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
		header('content-type: text/html; charset=UTF-8');
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
		
		$objTotalRaids			= $this->db->query('SELECT count(*) as count FROM __raids', true);
		$total_raids			= $objTotalRaids['count'];
		$raids_per_day			= sprintf("%.2f", ($total_raids / $days));
		$objTotalItems			= $this->db->query('SELECT count(*) as count FROM __items', true);
		$total_items			= $objTotalItems['count'];
		$items_per_day			= sprintf("%.2f", ($total_items / $days));
		$objTotalLogs			= $this->db->query('SELECT count(*) as count FROM __logs', true);
		$total_logs				= $objTotalLogs['count'];

		if ( (float)$raids_per_day > (float)$total_raids ){
			$raids_per_day = $total_raids;
		}
		if ( (float)$items_per_day > (float)$total_items ){
			$items_per_day = $total_items;
		}

		$arrTables = $this->db->listTables();
		$dbsize = 0;
		foreach ($arrTables as $key => $strTablename){			
			$dbsize += $this->db->getSizeOf($strTablename);;
		}

		if(is_int($dbsize)){
			$dbsize = ( $dbsize >= 1048576 ) ? sprintf('%.2f MB', ($dbsize / 1048576)) : (($dbsize >= 1024) ? sprintf('%.2f KB', ($dbsize / 1024)) : sprintf('%.2f Bytes', $dbsize));
		}
	
		$this->tpl->add_js('
			$(".ip_resolver").each(function() {
				$(this).qtip({
					content: {
						text: function(event, api) {
							$.ajax({
								url: \'index.php'.$this->SID.'\',
								data: { ip_resolve: $(this).html() }
							})
							.then(function(content) {
								api.set(\'content.text\', content);
							}, function(xhr, status, error) {
								api.set(\'content.text\', status + \': \' + error);
							});
							return \'<i class="fa fa-refresh fa-spin fa-lg"></i>\';
						}
					},
					position: {
						at: "bottom right",
						my: "top right"
					},
					style: {
						width: 150,
						tip: {
							corner: true,
							height: 20
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
							WHERE s.session_current > ?
							GROUP BY u.username, s.session_ip
							ORDER BY u.username, s.session_current DESC';
		$result = $this->db->prepare($sql)->execute($this->time->time-600);
		$arrOnlineUsers = $arrBots = array();
		if ($result){
			while ($row = $result->fetchAssoc()){
				$isBot = $this->env->is_bot($row['session_browser']) ? true : false;
				if(!$isBot || ($isBot && !in_array($this->env->is_bot($row['session_browser']), $arrBots))){
					$arrOnlineUsers[] = $row;
					if($isBot) $arrBots[] = $this->env->is_bot($row['session_browser']);
				}				
			}
			$online_count = count($arrOnlineUsers);
		} else $online_count = 0;
		
		if($online_count){
			foreach($arrOnlineUsers as $row){
				$username = ( !empty($row['username']) ) ? $row['username'] : (($this->env->is_bot($row['session_browser'])) ? $this->env->is_bot($row['session_browser']) : $this->user->lang('anonymous'));
				$this->tpl->assign_block_vars('online_row', array(
						'USERNAME'		=> sanitize($username),
						'LOGIN'			=> $this->time->user_date($row['session_start'], true),
						'LAST_UPDATE'	=> $this->time->createTimeTag($row['session_current'], $this->time->user_date($row['session_current'], true)),
						'LOCATION'		=> $this->admin_functions->resolve_eqdkp_page($row['session_page']),
						'BROWSER'		=> $this->admin_functions->resolve_browser($row['session_browser']),
						'IP_ADDRESS'	=> sanitize($row['session_ip']))
				);
			}
		}

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
			'SERVERINFO_MYSQL'		=> 'Client ('.$this->db->client_version.')<br/>Server ('.$this->db->server_version.')',
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
			'SHOW_PHP_WARNING'		=> (version_compare(PHP_VERSION, VERSION_PHP_RQ, '<=') && !defined('EQDKP_DISABLE_PHP_CHECK')) ? true : false,
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
?>