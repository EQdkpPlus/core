<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2011
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
if(!defined('EQDKP_INC')) {
	header('HTTP/1.0 404 Not Found');exit;
}
class inst_settings extends install_generic {
	public static $shortcuts = array('pdl', 'in', 'html', 'game', 'config', 'db', 'pfh', 'core', 'time');
	public static $before 		= 'encryptionkey';
	public static $ajax			= 'ajax';
	
	public $next_button		= 'inst_db';
	public $head_js			= "
		$('#game').change(function() {
			$('#game_lang').find('option').remove();
			$.post('index.php', { requestid: $(this).val(), ajax: 'games' } , function(data){ $('#game_lang').append(data) });
		});";
	
	//default settings
	private $def_game			= 'wow';
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
		'inactive_period'				=> '99',
		'active_point_adj'				=> '0.00',
		'inactive_point_adj'			=> '0.00',
		'start_page'					=> 'viewnews.php',
		'cookie_path'					=> '/',
		'session_length'				=> '3600',
		'session_cleanup'				=> '0',
		'session_last_cleanup'			=> '0',
		'enable_gzip'					=> '0',
		'account_activation'			=> '1',
		'default_style_overwrite'		=> '0',
		'enable_newscategories'			=> '0',
		'upload_allowed_extensions'		=> 'zip,rar,jpg,bmp,gif,png',
		'pk_updatecheck'				=> '1',
		'pk_enable_captcha'				=> '1',
		'lib_recaptcha_okey'			=> '6LdKQMUSAAAAAOFATjZq_IyMruO1jxQL-rSVNF-g',
		'lib_recaptcha_pkey'			=> '6LdKQMUSAAAAAC-pf92A4AVGjBOImTD9eIGr2WH7',
		'pk_attendance90'				=> '1',
		'pk_lastraid'					=> '1',
		'pk_class_color'				=> '1',
		'pk_newsloot_limit'				=> 'all',
		'pk_debug'						=> '0',
		'pk_maintenance_mode'			=> '1',
		'pk_enable_comments'			=> '1',
		'pk_itemhistory_dia' 			=> '1',
		'pk_color_items' 				=> 'a:2:{i:0;s:2:"34";i:1;s:2:"67";}',
		'auth_method'					=> 'db',
		'sort_menu1' => 'a:8:{s:32:"e6b102bd046c62ceb7604249d87e0df7";a:2:{s:4:"sort";i:0;s:4:"hide";N;}s:32:"54193562337cfc824e8ea2d79c09904a";a:2:{s:4:"sort";i:1;s:4:"hide";N;}s:32:"9dfc22859008eb0a92a20afabf5a7b32";a:2:{s:4:"sort";i:2;s:4:"hide";N;}s:32:"268f3f919a94c379e9b8ec147c4799a0";a:2:{s:4:"sort";i:3;s:4:"hide";N;}s:32:"6c4298cc62394cfe28ed5a6ab52888fa";a:2:{s:4:"sort";i:4;s:4:"hide";N;}s:32:"50d8cb46ce022263c862cf839b617f0b";a:2:{s:4:"sort";i:5;s:4:"hide";N;}s:32:"42500df250c8fdb9ff6d316515b25157";a:2:{s:4:"sort";i:6;s:4:"hide";N;}s:32:"446829091172dd8c40e3fe6e165a514a";a:2:{s:4:"sort";i:8;s:4:"hide";N;}}',
		'sort_menu2' => 'a:4:{s:32:"bb440d35d6365219ee86fc31b1f071fd";a:2:{s:4:"sort";i:0;s:4:"hide";N;}s:32:"3a8270dae2b17999b935edc868c22ceb";a:2:{s:4:"sort";i:1;s:4:"hide";N;}s:32:"def968f48f9a8de4bc35ef4a4dac2646";a:2:{s:4:"sort";i:2;s:4:"hide";N;}s:32:"ace854fe0ae1654494f1c204d1dc914a";a:2:{s:4:"sort";i:4;s:4:"hide";N;}}',
		'sort_menu4' => 'a:4:{s:32:"828e0013b8f3bc1bb22b4f57172b019d";a:2:{s:4:"sort";i:0;s:4:"hide";N;}s:32:"9dfc22859008eb0a92a20afabf5a7b32";a:2:{s:4:"sort";i:1;s:4:"hide";N;}s:32:"54193562337cfc824e8ea2d79c09904a";a:2:{s:4:"sort";i:2;s:4:"hide";N;}s:32:"b20ed7917891aa92b0e4ff1c1ea2080d";a:2:{s:4:"sort";i:4;s:4:"hide";N;}}',
		'eqdkpm_shownote'				=> '1',
		
		
		// Calendar settings
		'calendar_addevent_mode'		=> 'raid',
		'calendar_raid_guests'			=> '1',
		'calendar_raid_classbreak'		=> '5',
		'calendar_raid_status'			=> 'a:5:{i:0;s:1:"0";i:1;s:1:"1";i:2;s:1:"2";i:3;s:1:"3";i:4;s:1:"4";}',
		'calendar_raid_nsfilter'		=> 'a:4:{i:0;s:6:"twinks";i:1;s:8:"inactive";i:2;s:6:"hidden";i:3;s:7:"special";}',
		'calendar_addraid_deadline'		=> '1',
		'calendar_addraid_duration'		=> '120',
		'calendar_repeat_crondays'		=> '40',
		'calendar_raid_shownotes'		=> 'a:2:{i:0;s:1:"2";i:1;s:1:"3";}',
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
		$this->game->__construct(true, $this->in->get('inst_lang')); //set lang_name in game-class
		foreach($this->game->get_games() as $sgame){
			$games[$sgame] = $this->game->game_name($sgame);
		}
		// Build the default language & Locales dropdowns
		if(!$this->def_lang) $this->def_lang = $this->in->get('inst_lang');
		if(!$this->def_game_lang) $this->def_game_lang = $this->in->get('inst_lang');
		if(!$this->def_server_path) $this->def_server_path = str_replace('install/index.php', '', $_SERVER['SCRIPT_NAME']);
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
		$content = '<table class="ui-widget" style="border-collapse: collapse;" width="100%">
						<tr>
							<th class="ui-state-default" colspan="2">'.$this->lang['lang_config'].'</th>
						</tr>
						<tr>
							<td align="right"><strong>'.$this->lang['default_lang'].':</strong></td>
							<td>'.$this->html->DropDown('default_lang', $language_array, $this->def_lang).'</td>
						</tr>
						<tr>
							<td align="right"><strong>'.$this->lang['default_locale'].':</strong></td>
							<td>'.$this->html->DropDown('default_locale', $locale_array, $this->def_locale).'</td>
						</tr>
						<tr>
							<th class="ui-state-default" colspan="2">'.$this->lang['game_config'].'</th>
						</tr>
						<tr>
							<td align="right"><strong>'.$this->lang['default_game'].':</strong></td>
							<td>'.$this->html->DropDown('game', $games, $this->def_game).' <select name="game_lang" id="game_lang">'.self::ajax_out(false, $this->def_game).'</select></td>
						</tr>
						<tr>
							<th class="ui-state-default" colspan="2">'.$this->lang['server_config'].'</th>
						</tr>
						<tr>
							<td align="right"><strong>'.$this->lang['server_path'].':</strong></td>
							<td><input type="text" name="server_path" size="25" value="'.$this->def_server_path.'" class="input" /></td>
						</tr>
						<tr>
							<td align="right"><strong>'.$this->lang['timezone'].':</strong></td>
							<td>'.$this->html->DropDown('timezone', timehandler::fetch_timezones(), $this->def_timezone).'</td>
						</tr>
						<tr>
							<td align="right"><strong>'.$this->lang['startday'].':</strong></td>
							<td>'.$this->html->DropDown('startday', $startdays, $this->def_startday).'</td>
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
		$this->def_startday = $this->core->config('pk_date_startday');
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
			if(strpos($sql, 'DROP TABLE') === false) $this->data['installed_tables'][] = preg_replace('/(CREATE\sTABLE\sIF\sNOT\sEXISTS\s?`?|CREATE\sTABLE\s?`?)([a-zA-Z_0-9]*)(`?\s?\(.*)/', '\2', $sql);
		}
		//structure set up, fill with data now
		$sqls = $this->parse_sql_file($this->root_path.'install/schemas/mysql_data.sql');
		foreach($sqls as $sql) {
			if(!$this->do_sql($sql)) return false;
		}
		$this->check_data_folder();
		$this->set_config();
		$this->game->ChangeGame($this->def_game, $this->def_game_lang);
		$this->InsertStartNews();
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
		$this->config_data['cookie_name']		= "eqdkp_".substr(md5(rand().rand().rand()), 4, 6);
		$this->config_data['default_game']		= $this->def_game;
		$this->config_data['game_language']		= $this->def_game_lang;
		$this->config_data['eqdkp_start']		= time();
		$this->config_data['timezone']			= $this->def_timezone;
		$this->config_data['pk_date_startday']	= $this->def_startday;
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
		if($sql && !$this->sql_error && !$this->db->query($sql.';')) {
			$error = $this->db->error($sql, true);
			$this->pdl->log('install_error', 'SQL-Error:<br />Query: '.$sql.';<br />Code: '.$error['code'].'<br />Message: '.$error['message']);
			$this->undo();
			$this->sql_error = true;
			return false;
		}
		return true;
	}
	
	private function install_permissions() {
		$b[] = $this->do_sql("INSERT INTO __groups_user (`groups_user_id`, `groups_user_name`, `groups_user_desc`, `groups_user_deletable`, `groups_user_default`, `groups_user_hide`) VALUES
			(1,'".$this->lang['grp_guest']."','".$this->lang['grp_guest_desc']."','0','0','1'),
			(2,'".$this->lang['grp_super_admins']."','".$this->lang['grp_super_admins_desc']."','0','0','0'),
			(3,'".$this->lang['grp_admins']."','".$this->lang['grp_admins_desc']."','0','0','0'),
			(4,'".$this->lang['grp_member']."','".$this->lang['grp_member_desc']."','0','1','0'),
			(5,'".$this->lang['grp_officers']."','".$this->lang['grp_officers_desc']."','1','0','0'),
			(6,'".$this->lang['grp_writers']."','".$this->lang['grp_writers_desc']."','1','0','0');
		");
		$b[] = $this->InsertGroupPermissions(1, array('u_event_view', 'u_item_view', 'u_raid_view', 'u_member_view', 'u_userlist', 'u_news_view', 'u_search', 'u_roster_list'));
		$b[] = $this->InsertGroupPermissions(3, false, array('a_backup', 'a_logs_del', 'a_maintenance', 'a_reset', 'a_files_man'));
		$b[] = $this->InsertGroupPermissions(4, array('u_event_view', 'u_item_view', 'u_raid_view', 'u_member_view', 'u_member_man', 'u_member_add', 'u_member_conn', 'u_member_del', 'u_userlist', 'u_calendar_view', 'u_news_view', 'u_search', 'u_usermailer', 'u_roster_list'));
		$b[] = $this->InsertGroupPermissions(5, array('a_event_add', 'a_event_upd', 'a_event_del', 'a_item_add', 'a_item_upd', 'a_item_del', 'a_raid_add', 'a_raid_upd', 'a_raid_del', 'a_members_man', 'a_calendars_man', 'a_cal_event_man'));
		$b[] = $this->InsertGroupPermissions(6, array('a_news_add', 'a_news_upd', 'a_news_del'));
		if(in_array(false, $b, true)) return false;
		return true;
	}
	
	private function InsertStartNews(){
		$this->do_sql("INSERT INTO `__news` (`news_headline`, `news_message`, `news_date`, `user_id`, `showRaids_id`, `extended_message`, `nocomments`, `news_permissions`, `news_flags`, `news_category`, `news_start`, `news_stop`) VALUES 
		('".$this->lang['welcome_news_title']."', '".$this->lang['welcome_news']."', ".time().", 1, '', '', 0, 0, 1, 1, '', '');");
	}
	
	private function InsertGroupPermissions($grp_id, $perms=false, $noperms=false) {
		$this->init_auth_ids();
		$sqls = array();
		if(is_array($perms)) {
			foreach($perms as $value) {
				$sqls[] = "(".$this->db->escape($grp_id).", ".$this->db->escape($this->auth_ids[$value]).", 'Y')";
			}
		} elseif(is_array($noperms)) {
			$noperms = array_flip($noperms);
			foreach($this->auth_ids as $value => $id) {
				if(!isset($noperms[$value])) $sqls[] = "(".$this->db->escape($grp_id).", ".$this->db->escape($id).", 'Y')";
			}
		} else {
			return true;
		}
		if(count($sqls) > 1) return $this->do_sql("INSERT __auth_groups (group_id, auth_id, auth_setting) VALUES ".implode(', ', $sqls).";");
	}
	
	private function init_auth_ids() {
		if(!empty($this->auth_ids)) return true;
		$result = $this->db->query("SELECT auth_id, auth_value FROM __auth_options;");
		while($row = $this->db->fetch_record($result)) {
			$this->auth_ids[$row['auth_value']] = $row['auth_id'];
		}
		$this->db->free_result($result);
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
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_inst_settings', inst_settings::$shortcuts);
?>