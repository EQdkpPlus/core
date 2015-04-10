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

if(!defined('EQDKP_INC')) {
	header('HTTP/1.0 404 Not Found');exit;
}
class inst_settings extends install_generic {
	public static $before 		= 'encryptionkey';
	public static $ajax			= 'ajax';
	
	public $next_button		= 'inst_db';
	public $head_js			= "
		$('#game').change(function() {
			$('#game_lang').find('option').remove();
			$.post('index.php', { requestid: $(this).val(), ajax: 'games' } , function(data){ $('#game_lang').append(data) });
		});";
	
	//default settings
	private $def_game			= 'dummy';
	private $def_game_lang		= '';
	private $def_lang			= '';
	private $def_locale			= '';
	private $def_server_path	= '';
	private $def_timezone		= '';
	private $def_startday		= '';
	private $config_data		= array(
		'default_style'					=> '1',
		'default_alimit'				=> '100',
		'default_elimit'				=> '100',
		'default_ilimit'				=> '100',
		'default_nlimit'				=> '10',
		'default_rlimit'				=> '100',
		'guildtag'						=> 'My Guild',
		'dkp_name'						=> 'DKP',
		'hide_inactive'					=> '0',
		'inactive_period'				=> '0',
		'active_point_adj'				=> '0.00',
		'inactive_point_adj'			=> '0.00',
		'start_page'					=> 'news',
		'cookie_path'					=> '/',
		'session_length'				=> '3600',
		'session_cleanup'				=> '0',
		'session_last_cleanup'			=> '0',
		'enable_gzip'					=> '0',
		'account_activation'			=> 1,
		'default_style_overwrite'		=> '0',
		'enable_captcha'				=> '1',
		'pk_attendance90'				=> '1',
		'pk_lastraid'					=> '1',
		'class_color'					=> '1',
		'pk_newsloot_limit'				=> 'all',
		'debug'							=> '0',
		'pk_maintenance_mode'			=> '1',
		'enable_comments'				=> '1',
		'itemhistory_dia' 				=> '1',
		'color_items' 					=> array(34, 67),
		'auth_method'					=> 'db',
		'failed_logins_inactivity'		=> 5,
		'thumbnail_defaultsize'			=> 500,
		'seo_extension'					=> 1,
		'round_activate'				=> 1,
		'round_precision'				=> 2,
		'custom_logo'					=> 'logo.svg',
		'mainmenu'						=> 'a:8:{i:0;a:1:{s:4:"item";a:2:{s:4:"hash";s:32:"d41d8cd98f00b204e9800998ecf8427e";s:6:"hidden";s:1:"0";}}i:1;a:1:{s:4:"item";a:2:{s:4:"hash";s:32:"e2672c7758bc5f8bb38ddb4b60fa530c";s:6:"hidden";s:1:"0";}}i:2;a:2:{s:4:"item";a:2:{s:4:"hash";s:32:"92f04bcfb72b27949ee68f52a412acac";s:6:"hidden";s:1:"0";}s:7:"_childs";a:1:{i:0;a:1:{s:4:"item";a:2:{s:4:"hash";s:32:"7809b1008f1d915120b3b549ca033e1f";s:6:"hidden";s:1:"0";}}}}i:3;a:2:{s:4:"item";a:2:{s:4:"hash";s:32:"ca65b9cf176197c365f17035270cc9f1";s:6:"hidden";s:1:"0";}s:7:"_childs";a:4:{i:1;a:1:{s:4:"item";a:2:{s:4:"hash";s:32:"0e6acee4fa4635f2c25acbf0bad6c445";s:6:"hidden";s:1:"0";}}i:2;a:1:{s:4:"item";a:2:{s:4:"hash";s:32:"53433bf03b32b055f789428e95454cec";s:6:"hidden";s:1:"0";}}i:3;a:1:{s:4:"item";a:2:{s:4:"hash";s:32:"c1ec6e24e3276e17e3edcb08655d9181";s:6:"hidden";s:1:"0";}}i:4;a:1:{s:4:"item";a:2:{s:4:"hash";s:32:"65d93e089c21a737b601f81e70921b8b";s:6:"hidden";s:1:"0";}}}}i:4;a:1:{s:4:"item";a:2:{s:4:"hash";s:32:"8f9bfe9d1345237cb3b2b205864da075";s:6:"hidden";s:1:"0";}}i:5;a:2:{s:4:"item";a:2:{s:4:"hash";s:32:"ebc90e9afa50f8383d4f93ce9944b8dd";s:6:"hidden";s:1:"0";}s:7:"_childs";a:2:{i:5;a:1:{s:4:"item";a:2:{s:4:"hash";s:32:"276753faf0f1a394d24bea5fa54a4e6b";s:6:"hidden";s:1:"0";}}i:6;a:1:{s:4:"item";a:2:{s:4:"hash";s:32:"cd5f542b7201c8d9b8f697f97a2dcc52";s:6:"hidden";s:1:"0";}}}}i:6;a:1:{s:4:"item";a:2:{s:4:"hash";s:32:"292299380781735bd110e74fe0ada4ac";s:6:"hidden";s:1:"0";}}i:7;a:1:{s:4:"item";a:2:{s:4:"hash";s:32:"2a91cf06beec2894ebd9266c884558c3";s:6:"hidden";s:1:"0";}}}',
		'cookie_euhint_show'			=> 1,
		'embedly_key'					=> '9bda60ed633e403e866dbca5f4c8e56d',
		'enable_registration'			=> 1,
		'enable_points'					=> 1,
		'enable_embedly'				=> 1,
		'enable_leaderboard'			=> 1,
			
		// Calendar settings
		'calendar_addevent_mode'		=> 'raid',
		'calendar_raid_guests'			=> '1',
		'calendar_raid_classbreak'		=> '5',
		'calendar_raid_status'			=> array(0,1,2,3,4),
		'calendar_raid_nsfilter' 		=> array (0 => 'twinks',1 => 'inactive',2 => 'hidden'),
		'calendar_addraid_deadline'		=> '1',
		'calendar_addraid_duration'		=> '120',
		'calendar_repeat_crondays'		=> '40',
		'calendar_email_statuschange'	=> '1',
	);
	private $auth_ids			= array();
	private $sql_error			= false;
	
	public function __construct() {
		parent::__construct();
		$this->config_data['plus_version'] = VERSION_INT;
	}
	
	public static function before() {
		return self::$before;
	}

	public static function ajax() {
		return self::$ajax;
	}
	
	public function ajax_out($ajax=true, $sgame='') {
		$content = '';
		if(($this->in->exists('ajax') && $this->in->get('ajax') == 'games') || !$ajax){
			$sgame = ($sgame) ? $sgame : $this->in->get('requestid');
			$langfiles = sdir($this->root_path . 'games/'.$sgame.'/language/', '*.php', '.php');
			foreach($langfiles as $file) {
				$selected = ($this->def_game_lang == $file) ? ' selected="selected"' : '';
				$content .= '<option value="'.$file.'"'.$selected.'>'.ucfirst($file).'</option>';
			}
			if($ajax) {
				echo $content;
				exit;
			}
		}
		return $content;
	}

	public function get_output() {
		$games = array();
		$this->game = registry::register('game', array(true, $this->in->get('inst_lang'))); //set lang_name in game-class

		foreach($this->game->get_games() as $sgame){
			$games[$sgame] = $this->game->game_name($sgame);
		}
		// Build the default language & Locales dropdowns
		if(!$this->def_lang) $this->def_lang = $this->in->get('inst_lang');
		if(!$this->def_game_lang) $this->def_game_lang = $this->in->get('inst_lang');
		if(!$this->def_server_path) $this->def_server_path = str_replace('install/index.php', '', $this->env->phpself);
		if(!$this->def_timezone) $this->def_timezone = date_default_timezone_get();
		if(!$this->def_startday) $this->def_startday = ($this->in->get('inst_lang') == 'german') ? 'monday' : 'sunday';
		$langs = sdir($this->root_path.'language');
		foreach($langs as $slang) {
			if(!is_file($this->root_path.'language/'.$slang.'/lang_main.php')) continue;
			include($this->root_path.'language/'.$slang.'/lang_main.php');
			$lang_name_tp = (($lang['ISO_LANG_NAME']) ? $lang['ISO_LANG_NAME'].' ('.$lang['ISO_LANG_SHORT'].')' : ucfirst($slang));
			$language_array[$slang]					= (isset($lang['ISO_LANG_NAME'])) ? $lang['ISO_LANG_NAME'] : ucfirst($slang);
			$locale_array[$lang['ISO_LANG_SHORT']]	= $lang_name_tp;
			if($slang == $this->def_lang && !$this->def_locale) $this->def_locale = $lang['ISO_LANG_SHORT'];
		}
		$startdays = array('sunday' => $this->lang['sunday'], 'monday' => $this->lang['monday']);
		
		registry::load('timehandler');
		$content = '<table class="no-borders" style="border-collapse: collapse;" width="100%">
						<tr>
							<th class="" colspan="2">'.$this->lang['lang_config'].'</th>
						</tr>
						<tr>
							<td align="right"><strong>'.$this->lang['default_lang'].':</strong></td>
							<td>'.new hdropdown('default_lang', array('options' => $language_array, 'value' => $this->def_lang)).'</td>
						</tr>
						<tr>
							<td align="right"><strong>'.$this->lang['default_locale'].':</strong></td>
							<td>'.new hdropdown('default_locale', array('options' => $locale_array, 'value' => $this->def_locale)).'</td>
						</tr>
						<tr>
							<th class="" colspan="2">'.$this->lang['game_config'].'</th>
						</tr>
						<tr>
							<td colspan="2">
								<div class="infobox infobox-large infobox-blue clearfix">
									<i class="fa fa-info-circle fa-4x pull-left"></i>'.$this->lang['game_info'].'
								</div>
							</td>
						</tr>
						<tr>
							<td align="right"><strong>'.$this->lang['default_game'].':</strong></td>
							<td>'.new hdropdown('game', array('options' => $games, 'value' => $this->def_game)).' <select name="game_lang" id="game_lang">'.self::ajax_out(false, $this->def_game).'</select></td>
						</tr>
						<tr>
							<th class="" colspan="2">'.$this->lang['server_config'].'</th>
						</tr>
						<tr>
							<td align="right"><strong>'.$this->lang['server_path'].':</strong></td>
							<td><input type="text" name="server_path" size="25" value="'.$this->def_server_path.'" class="input" /></td>
						</tr>
						<tr>
							<td align="right"><strong>'.$this->lang['timezone'].':</strong></td>
							<td>'.new hdropdown('timezone', array('options' => timehandler::fetch_timezones(), 'value' => $this->def_timezone)).'</td>
						</tr>
						<tr>
							<td align="right"><strong>'.$this->lang['startday'].':</strong></td>
							<td>'.new hdropdown('startday', array('options' => $startdays, 'value' => $this->def_startday)).'</td>
						</tr>
					</table>';
		return $content;
	}
	
	public function get_filled_output() {
		$this->def_lang = $this->core->config('default_lang');
		$this->def_locale = $this->core->config('default_locale');
		$this->def_game = $this->core->config('default_game');
		$this->def_game_lang = $this->core->config('game_language');
		$this->def_server_path = $this->core->config('server_path');
		$this->def_timezone = $this->core->config('timezone');
		$this->def_startday = $this->core->config('date_startday');
		return $this->get_output();
	}
	
	public function parse_input() {
		$this->def_lang = $this->in->get('default_lang', $this->in->get('inst_lang'));
		$this->def_locale = $this->in->get('default_locale');
		$this->def_game = $this->in->get('game');
		$this->def_game_lang = $this->in->get('game_lang');
		$this->def_server_path = $this->in->get('server_path');
		$this->def_timezone = $this->in->get('timezone');
		$this->def_startday = $this->in->get('startday');
		//first setup structure
		$sqls = $this->parse_sql_file($this->root_path.'install/schemas/mysql_structure.sql');

		foreach($sqls as $sql) {
			if(!$this->do_sql($sql)) return false;
			//recognize table
			if(strpos($sql, 'DROP TABLE') === false) {
				preg_match('/(CREATE\sTABLE\sIF\sNOT\sEXISTS\s?`?|CREATE\sTABLE\s?`?)([a-zA-Z_0-9]*)(`?\s?\(.*)/U', $sql, $arrMatches);
				if(isset($arrMatches[2])){
					$this->data['installed_tables'][] = $arrMatches[2];
				}
			}
		}
		//structure set up, fill with data now
		$sqls = $this->parse_sql_file($this->root_path.'install/schemas/mysql_data.sql');
		foreach($sqls as $sql) {
			if(!$this->do_sql($sql)) return false;
		}
		$this->check_data_folder();
		$this->set_config();
		$this->game->installGame($this->def_game, $this->def_game_lang);
		$this->handleArticles();
		
		if(!$this->install_permissions()) return false;
		return true;
	}
	
	public function undo() {
		//remove installed tables from database
		$this->data['installed_tables'] = array_unique($this->data['installed_tables']);
		foreach($this->data['installed_tables'] as $key => $table) {
			if($this->db->query("DROP TABLE IF EXISTS ".$table.";")) unset($this->data['installed_tables'][$key]);
		}		
	}
	
	private function set_config() {
		$this->config_data['server_path']		= $this->def_server_path;
		$this->config_data['default_lang']		= $this->def_lang;
		$this->config_data['default_locale']	= $this->def_locale;
		$this->config_data['cookie_name']		= "eqdkp_".substr(md5(generateRandomBytes()), 4, 6);
		$this->config_data['default_game']		= $this->def_game;
		$this->config_data['game_language']		= $this->def_game_lang;
		$this->config_data['eqdkp_start']		= time();
		$this->config_data['timezone']			= $this->def_timezone;
		$this->config_data['date_startday']	= $this->def_startday;
		$this->config_data['default_date_time'] = (isset($this->lang['time_format'])) ? $this->lang['time_format'] : 'H:i';
		$this->config_data['default_date_short'] = (isset($this->lang['date_short_format'])) ? $this->lang['date_short_format'] : 'm/d/Y';
		$this->config_data['default_date_long'] = (isset($this->lang['date_long_format'])) ? $this->lang['date_long_format'] : 'F j, Y';
		$this->config_data['eqdkp_layout']		= 'normal';
		$this->config_data['pdc'] = array(
			'mode' => 'file',
			'prefix' => $this->table_prefix,
			'dttl' => 86400
		);
		//config-data complete
		$this->config->install_set($this->config_data);
		
		$this->pfh->copy($this->root_path.'templates/maintenance/images/logo.svg',  $this->pfh->FolderPath('','files').'logo.svg');
	}
	
	private function parse_sql_file($filename) {
		$file = file_get_contents($filename);
		$sqls = explode(";\n", $file);
		$sqls = preg_replace('/^#.*$/m', '', $sqls);
		$sqls = preg_replace('/\s{2,}/', ' ', $sqls);
		//$sqls = preg_replace('/\v/', '', $sqls);
		return $sqls;
	}
	
	private function do_sql($sql) {
		if($sql && !$this->sql_error) {
			$objQuery = $this->db->query($sql.';');
			if (!$objQuery){		
				$this->pdl->log('install_error', 'SQL-Error:<br />Query: '.$sql.';<br />Code: '.$this->db->errno.'<br />Message: '.$this->db->error);
				$this->undo();
				$this->sql_error = true;
				return false;
			}
		}
		return true;
	}
	
	private function install_permissions() {
		$b[] = $this->do_sql("INSERT INTO __groups_user (`groups_user_id`, `groups_user_name`, `groups_user_desc`, `groups_user_deletable`, `groups_user_default`, `groups_user_hide`) VALUES
			(1,".$this->db->escapeString($this->lang['grp_guest']).",".$this->db->escapeString($this->lang['grp_guest_desc']).",'0','0','1'),
			(2,".$this->db->escapeString($this->lang['grp_super_admins']).",".$this->db->escapeString($this->lang['grp_super_admins_desc']).",'0','0','0'),
			(3,".$this->db->escapeString($this->lang['grp_admins']).",".$this->db->escapeString($this->lang['grp_admins_desc']).",'0','0','0'),
			(4,".$this->db->escapeString($this->lang['grp_member']).",".$this->db->escapeString($this->lang['grp_member_desc']).",'0','1','0'),
			(5,".$this->db->escapeString($this->lang['grp_officers']).",".$this->db->escapeString($this->lang['grp_officers_desc']).",'1','0','0'),
			(6,".$this->db->escapeString($this->lang['grp_writers']).",".$this->db->escapeString($this->lang['grp_writers_desc']).",'1','0','0');
		");
		$b[] = $this->InsertGroupPermissions(1, array('u_userlist', 'u_search'));
		$b[] = $this->InsertGroupPermissions(3, false, array('a_backup', 'a_logs_del', 'a_maintenance', 'a_reset', 'a_files_man'));
		$b[] = $this->InsertGroupPermissions(4, array('u_member_man', 'u_member_add', 'u_member_conn', 'u_member_del', 'u_userlist', 'u_calendar_view', 'u_search', 'u_usermailer', 'u_files_man'));
		$b[] = $this->InsertGroupPermissions(5, array('a_event_add', 'a_event_upd', 'a_event_del', 'a_item_add', 'a_item_upd', 'a_item_del', 'a_raid_add', 'a_raid_upd', 'a_raid_del', 'a_members_man', 'a_calendars_man', 'a_cal_event_man', 'a_articles_man'));
		$b[] = $this->InsertGroupPermissions(6, array('a_articles_man', 'a_files_man'));
		if(in_array(false, $b, true)) return false;
		return true;
	}
	
	private function handleArticles(){
		//Language for Categories
		$this->do_sql("UPDATE __article_categories SET name=".$this->db->escapeString($this->lang['category1'])." WHERE id=1;");
		$this->do_sql("UPDATE __article_categories SET name=".$this->db->escapeString($this->lang['category2'])." WHERE id=2;");
		$this->do_sql("UPDATE __article_categories SET name=".$this->db->escapeString($this->lang['category3'])." WHERE id=3;");
		$this->do_sql("UPDATE __article_categories SET name=".$this->db->escapeString($this->lang['category4'])." WHERE id=4;");
		$this->do_sql("UPDATE __article_categories SET name=".$this->db->escapeString($this->lang['category5'])." WHERE id=5;");
		$this->do_sql("UPDATE __article_categories SET name=".$this->db->escapeString($this->lang['category6'])." WHERE id=6;");
		$this->do_sql("UPDATE __article_categories SET name=".$this->db->escapeString($this->lang['category7'])." WHERE id=7;");
		$this->do_sql("UPDATE __article_categories SET name=".$this->db->escapeString($this->lang['category8'])." WHERE id=8;");
		$this->do_sql("UPDATE __article_categories SET name=".$this->db->escapeString($this->lang['category9'])." WHERE id=9;");
		
		
		//Language for Default Pagetitles
		$this->do_sql("UPDATE __articles SET title=".$this->db->escapeString($this->lang['article5'])." WHERE id=5;");
		$this->do_sql("UPDATE __articles SET title=".$this->db->escapeString($this->lang['article6'])." WHERE id=6;");
		$this->do_sql("UPDATE __articles SET title=".$this->db->escapeString($this->lang['article7'])." WHERE id=7;");
		$this->do_sql("UPDATE __articles SET title=".$this->db->escapeString($this->lang['article8'])." WHERE id=8;");
		$this->do_sql("UPDATE __articles SET title=".$this->db->escapeString($this->lang['article9'])." WHERE id=9;");
		$this->do_sql("UPDATE __articles SET title=".$this->db->escapeString($this->lang['article10'])." WHERE id=10;");
		$this->do_sql("UPDATE __articles SET title=".$this->db->escapeString($this->lang['article12'])." WHERE id=12;");
		$this->do_sql("UPDATE __articles SET title=".$this->db->escapeString($this->lang['article13'])." WHERE id=13;");
		$this->do_sql("UPDATE __articles SET title=".$this->db->escapeString($this->lang['article14'])." WHERE id=14;");
		$this->do_sql("UPDATE __articles SET title=".$this->db->escapeString($this->lang['article15'])." WHERE id=15;");
		$this->do_sql("UPDATE __articles SET title=".$this->db->escapeString($this->lang['article16'])." WHERE id=16;");
		
		
		//Disclaimer & Privacy Policy
		if (is_file($this->root_path.'language/'.$this->language.'/disclaimer.php')){
			include_once($this->root_path.'language/'.$this->language.'/disclaimer.php');
			$this->do_sql("UPDATE __articles SET text=".$this->db->escapeString($privacy)." WHERE id=15;");
			$this->do_sql("UPDATE __articles SET text=".$this->db->escapeString($disclaimer)." WHERE id=16;");
		} else {
			$this->do_sql("DELETE FROM __articles WHERE id=15;");
			$this->do_sql("DELETE FROM __articles WHERE id=16;");
		}	
		
		//Startnews
		$this->do_sql("INSERT INTO `__articles` (`id`, `title`, `text`, `category`, `featured`, `comments`, `votes`, `published`, `show_from`, `show_to`, `user_id`, `date`, `previewimage`, `alias`, `hits`, `sort_id`, `tags`, `votes_count`, `votes_sum`, `votes_users`, `last_edited`, `last_edited_user`) VALUES 
		(1, ".$this->db->escapeString($this->lang['feature_news_title']).", ".$this->db->escapeString($this->lang['feature_news']).", 2, 1, 1, 0, 1, '', '', 1, ".(time()-5).", '', 'new-features', 0, 0, 'a:1:{i:0;s:0:\"\";}', 0, 0, '', ".(time()-5).", 1);");
		
		$this->do_sql("INSERT INTO `__articles` (`id`, `title`, `text`, `category`, `featured`, `comments`, `votes`, `published`, `show_from`, `show_to`, `user_id`, `date`, `previewimage`, `alias`, `hits`, `sort_id`, `tags`, `votes_count`, `votes_sum`, `votes_users`, `last_edited`, `last_edited_user`) VALUES 
		(11, ".$this->db->escapeString($this->lang['welcome_news_title']).", ".$this->db->escapeString($this->lang['welcome_news']).", 2, 1, 1, 0, 1, '', '', 1, ".time().", '', 'welcome', 0, 0, 'a:1:{i:0;s:0:\"\";}', 0, 0, '', ".time().", 1);");
		
		//Update Article Date
		$this->do_sql("UPDATE __articles SET date=".time().", last_edited=".time().";");
	}
	
	private function InsertGroupPermissions($grp_id, $perms=false, $noperms=false) {
		$this->init_auth_ids();
		$sqls = array();
		if(is_array($perms)) {
			foreach($perms as $value) {
				$sqls[] = "(".$grp_id.", ".$this->auth_ids[$value].", 'Y')";
			}
		} elseif(is_array($noperms)) {
			$noperms = array_flip($noperms);
			foreach($this->auth_ids as $value => $id) {
				if(!isset($noperms[$value])) $sqls[] = "(".$grp_id.", ".$id.", 'Y')";
			}
		} else {
			return true;
		}
		if(count($sqls) > 1) return $this->do_sql("INSERT __auth_groups (group_id, auth_id, auth_setting) VALUES ".implode(', ', $sqls).";");
	}
	
	private function init_auth_ids() {
		if(!empty($this->auth_ids)) return true;
		$result = $this->db->query("SELECT auth_id, auth_value FROM __auth_options;");
		if($result){
			while($row = $result->fetchAssoc()) {
				$this->auth_ids[$row['auth_value']] = $row['auth_id'];
			}
		}
	}
	
	private function check_data_folder() {
		// Try to locate & delete the localconf file
		$lconffile = $this->pfh->FolderPath('config', 'eqdkp')."localconf.php";
		if(is_file($lconffile)){
			$this->pfh->Delete($lconffile);
		}
		// and same for the cache-folder
		$cachefolder = $this->pfh->FolderPath('', 'cache');
		if(is_dir($cachefolder)) {
			$this->pfh->Delete($cachefolder);
		}
	}
}
?>