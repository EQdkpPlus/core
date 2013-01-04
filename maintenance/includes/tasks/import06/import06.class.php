<?php
/*
* Project:     EQdkp-Plus
* License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
* Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
* -----------------------------------------------------------------------
* Began:       2009
* Date:        $Date$
* -----------------------------------------------------------------------
* @author      $Author$
* @copyright   2006-2009 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
* @link        http://eqdkp-plus.com
* @package     eqdkp-plus
* @version     $Rev$
*
* $Id$
*/

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class import06 extends task {
	public static function __shortcuts() {
		$shortcuts = array('db', 'in', 'pdl', 'user', 'config', 'encrypt', 'time');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public $author = 'Hoofy';
	public $version = '1.0.0';
	public $form_method = 'post';
	public $name = 'Import 0.6';
	public $type = 'import';

	protected $old_db_data = array(false);
	protected $old = array(false, '');  //Holds old DB
	protected $new = array(false, '');  //Holds new DB

	protected $path = '';

	public function __construct() {
		parent::__construct();
		//set step order
		$this->step_order = array('first', 'users_auths', 'config_news_log', 'events', 'multidkp', 'members', 'raids', 'items', 'adjustments', 'dkp_check', 'plugins_portal');
		$this->use_steps = true;
		$this->parse_only = false;
	}

	public function is_applicable() {
		//import can be done always, no restrictions here
		return true;
	}

	public function is_necessary() {
		return false;
	}

	public function construct() {
		$this->new[0] = $this->db;
		$this->new[1] = $this->table_prefix;
		$this->new[2] = $this->dbname;
	}

	public function destruct()
	{
		unset($this->old);
		unset($this->new);
	}

	protected function connect2olddb() {
		if(!is_object($this->old[0])) {
			if($this->step_data['old_db_data'][0] === true) {
				$this->old[0] = dbal::factory(array('dbtype' => $this->dbtype));
				$this->old[0]->open($this->step_data['old_db_data']['host'], $this->step_data['old_db_data']['name'], $this->step_data['old_db_data']['user'], $this->step_data['old_db_data']['pass']);
				$this->old[1] = $this->step_data['old_db_data']['prefix'];
				$this->old[2] = $this->step_data['old_db_data']['name'];
			} else {
				$this->old[0] = $this->new[0];
				$this->old[1] = $this->step_data['old_db_data']['prefix'];
				$this->old[2] = $this->new[2];
			}
		}
		return ($this->old[0]->query("SELECT config_value FROM ".$this->old[1]."config WHERE config_name = 'plus_version'")) ? true : false;
	}

	protected function get($key, $default='', $array=false) {
		return ($array) ? $this->in->getArray($key, $default) : $this->in->get($key, $default);
	}
	
	protected function js_select_global() {
		return "<script type='text/javascript'>
			function UpdateChecked(value) {
				var inputs = document.getElementById('select_table').getElementsByTagName('input');
				for(i=0; i<inputs.length; i++) {
					if(inputs[i].className == 'select_me' && inputs[i].checked) {
						var id = inputs[i].id;
						var select = document.getElementById('select_'+id);
						select.value = value;
					}
				}
			}
			function SelectAll() {
				var inputs = document.getElementById('select_table').getElementsByTagName('input');
				for(i=0; i<inputs.length; i++) {
					if(inputs[i].className == 'select_me') {
						if(inputs[i].checked) {
							inputs[i].checked = false;
						} else {
							inputs[i].checked = true;
						}
					}
				}
			}
			</script>";
	}

	protected function js_mark_boxes($num, $id_prefix) {
		return "<script type='text/javascript'>function mark_boxes(num, id_prefix) {
						var i=0;
						while(i<num) {
							var current_input = document.getElementById(id_prefix+i);
							if(current_input.checked == true) {
								current_input.checked = false;
							} else {
								current_input.checked = true;
							}
							i = i+1;
						}
					}</script>".'<a href="#" onclick="javascript:mark_boxes('.$num.',\''.$id_prefix.'\')" style="cursor:pointer;">'.$this->lang['select_all'].'</a>';
	}

	protected function select_field($name, $data, $add='') {
		$retu = "<select name='".$name."'".$add."><option value='0'>".$this->lang['no_import']."</option>";
		foreach($data as $id => $value) {
			$retu .= "<option value='".$id."'>".$value."</option>";
		}
		return $retu."</select>";
	}

	protected function table_by_name($table_name) {
		$result = $this->old[0]->query("SHOW CREATE TABLE ".$this->old[1].$table_name.";");
		while ( $row = $this->old[0]->fetch_record($result) ) {
			$sql = str_replace($this->old[1], $this->new[1], $row["Create Table"]);
		}
		$this->new[0]->query("DROP TABLE IF EXISTS ".$this->new[1].$table_name.";");
		$this->new[0]->query($sql);
		$data = array();
		$ind = 0;
		$result = $this->old[0]->query("SELECT * FROM ".$this->old[1].$table_name.";");
		while ( $row = $this->old[0]->fetch_record($result) ) {
			foreach($row as $field => $value) {
				$data[$ind][$field] = $this->old[0]->escape($value);
			}
			$ind++;
		}
		if($data) {
			$sql = "INSERT INTO ".$this->new[1].$table_name." (`".implode('`, `', array_keys(current($data)))."`) VALUES ";
			$sqls = array();
			$counter = 0;
			foreach($data as $row) {
				$k = $counter/1000;
				settype($k, 'int');
				$sqls[$k][] = "('".implode("', '", $row)."')";
				$counter++;
			}
			$retus = array();
			foreach($sqls as $ssql) {
				$retus[] = $this->new[0]->query($sql.implode(', ', $ssql).";");
			}
			return (in_array(false, $retus, true)) ? false : true;
		}
		return true;
	}

	protected function first_step() {
		$this->current_step = 'first';
		$output = '<table cellpadding="1" cellspacing="1" align="center" class="task_table colorswitch" width="60%" >';
		$output .= '	<tr>';
		$output .= '	  <th width="40px" class="th_sub">&nbsp;</th><th colspan="2" class="th_sub">'.$this->lang['import_steps'].'</th>';
		$output .= '	</tr>';
		foreach($this->step_order as $ikey => $value) {
			if($ikey != 0) {
				$output .= '    <tr><td colspan="3"><label><input type="checkbox" name="import_steps[]" value="'.$ikey.'" checked="checked" />'.$this->lang[$value].'</label></td>    </tr>';
			}
		}
		$output .= '<tr><th colspan="3" class="th_sub">'.$this->lang['database_info'].'</th></tr>';
		$output .= '<tr><td colspan="2">'.$this->lang['table_prefix'].'</td><td><input type="text" name="table_prefix" value="eqdkp_" /></td></tr>';
		$output .= '<tr><td colspan="2">'.$this->lang['database_other'].'</td><td width="50%"><input type="checkbox" name="db_else" value="1" /></td></tr>';
		$output .= '<tr><td colspan="2">'.$this->lang['host'].'</td><td><input type="text" name="db_host" value="localhost" /></td></tr>';
		$output .= '<tr><td colspan="2">'.$this->lang['db_name'].'</td><td><input type="text" name="db_name" value="" /></td></tr>';
		$output .= '<tr><td colspan="2">'.$this->lang['user'].'</td><td><input type="text" name="db_user" value="" /></td></tr>';
		$output .= '<tr><td colspan="2">'.$this->lang['password'].'</td><td><input type="password" name="db_pass" value="" /></td></tr>';
		$output .= '<tr><th colspan="3" class="th_sub"><input type="submit" name="first" value="'.$this->lang['submit'].'" class="mainoption"/><input type="submit" name="no_import" value="'.$this->lang['dont_import'].'" class="mainoption"/></th></tr></table>';
		return $output;
	}

	protected function parse_first_step() {
		if(!$this->get('first') AND !$this->get('no_import')) {
			$this->pdl->log('maintenance', 'Nothing to do.');
			return false;
		}
		if($this->get('no_import')) {
			return true;
		}
		if($this->get('db_else')) {
			$this->step_data['old_db_data'] = array(true, 'host' => $this->get('db_host'), 'name' => $this->get('db_name'), 'user' => $this->get('db_user'), 'pass' => $this->get('db_pass'), 'prefix' => $this->get('table_prefix'));
		} else {
			if(!$this->get('table_prefix')) {
				$this->pdl->log('maintenance', 'No table_prefix specified.');
				return false;
			}
			$this->step_data['old_db_data'] = array(false, 'prefix' => $this->get('table_prefix'));
		}
		$this->steps = $this->get('import_steps', 'int', true);
		if($this->connect2olddb()) {
			return true;
		} else {
			$this->pdl->log('maintenance', 'Could not connect to database, from which data shall be imported.');
			return false;
		}
	}

	protected function get_step($step) {
		return $this->$step();
	}

	protected function parse_step($step) {
		return $this->{'parse_'.$step}();
	}

	protected function users_auths() {
		$this->current_step = 'users_auths';
		$output = '<table width="60%" cellpadding="1" cellspacing="1" align="center" class="task_table colorswitch">';
		$output .= '<tr><td colspan="3" class="row2">'.$this->lang['your_user'].'{USER_SELECT}</td></tr>';
		$output .= '<tr><th colspan="3" class="th_sub">'.$this->lang['which_users'].' '.$this->js_mark_boxes('{NUM}', 'u_').'</th></tr>';
		$output .= '<tr><td colspan="3" align="center">
						<div class="errorbox roundbox">
							<div class="icon_false">'.$this->lang['notice_admin_perm'].'</div>
						</div>	
				</td></tr>';
		//build userlist
		$users = array();
		$admins = array();
		$this->connect2olddb();
		//look for users with admin-rights
		$result = $this->old[0]->query("SELECT u.user_id FROM ".$this->old[1]."auth_users u, ".$this->old[1]."auth_options o WHERE u.auth_id = o.auth_id AND o.auth_value IN ('a_users_man', 'a_config_man') AND u.auth_setting = 'Y';");
		while ( $row = $this->old[0]->fetch_record($result) ) {
			$admins[] = $row['user_id'];
		}
		$this->old[0]->free_result($result);
		$result = $this->old[0]->query("SELECT username, user_id FROM ".$this->old[1]."users ORDER BY username ASC;");
		$c = 0;
		while ( $row = $this->old[0]->fetch_record($result) ) {
			$users[$c]['id'] = $row['user_id'];
			$users[$c]['name'] = $row['username'];
			if(in_array($row['user_id'], $admins)) {
				$users[$c]['admin'] = true;
			}
			$c++;
		}
		unset($admins);

		$u_sel = '<select name="your_user"><option value="0">'.$this->lang['no_user'].'</option>';
		foreach($users as $suser) {
			$sel = ($this->user->data['username'] == $suser['name']) ? " selected='selected'" : "";
			$u_sel .= "<option value='".$suser['id']."'".$sel.">".$suser['name']."</option>";
		}
		$output = str_replace('{USER_SELECT}', $u_sel, $output);

		$max = count($users);
		$output = str_replace('{NUM}', $max, $output);
		$rows = $max/3;
		settype($rows,'int');
		if($rows < ($max/3)) {
			$rows++;
		}
		$done = array();
		function get_output($num, $users, $lang_admin, $colspan='') {
			$out = "<td width='33%'".$colspan."><input type='checkbox' name='user_id[]' id='u_".$num."' value='".$users[$num]['id']."' checked='checked' />";
			$checked = ($users[$num]['admin']) ? "checked='checked' " : '';
			$out .= $users[$num]['name']." (<input type='checkbox' name='admin[".$users[$num]['id']."]' value='1' ".$checked."> ".$lang_admin.")</td>";
			return $out;
		}
		for($i=0; $i<$rows; $i++) {
			$one = false;
			$two = false;
			$output .= "<tr>";
			if(!in_array($i, $done) AND $i < $max) {
				$output .= get_output($i, $users, $this->lang['admin']);
				$one = true;
			}
			if(!in_array(($i+$rows), $done) AND ($i+$rows) < $max) {
				$output .= get_output($i+$rows, $users, $this->lang['admin'], (($one) ? '' : " colspan='2'"));
				$two = true;
			}
			if(!in_array(($i+2*$rows), $done) AND ($i+2*$rows) < $max) {
				$colspan = ($one) ? (($two) ? '' : " colspan='2'") : (($two) ? " colspan='2'" : " colspan='3'");
				$output .= get_output($i+2*$rows, $users, $this->lang['admin'], $colspan);
			}
			$output .= "</tr>";
			$done[] = $i;
			$done[] = ($i+$rows);
			$done[] = ($i+2*$rows);
		}
		$output .= "<tr><th colspan='3' class='th_sub'><input type='submit' name='users_auths' value='".$this->lang['submit']."' class='mainoption'/></th></tr></table>";
		return $output;
	}

	protected function parse_users_auths() {
		if(!$this->get('users_auths')) {
			return false;
		}
		if(is_array($this->get('user_id', 'int', true)) && count($this->get('user_id', 'int', true)) > 0) {
			$users = array();
			$this->connect2olddb();
			if($this->get('your_user', 0)) {
				$this->step_data['replace_users'][$this->get('your_user')] = $this->user->data['user_id'];
			}
			$users2import = $this->get('user_id', 'int', true);
			if(!in_array($this->get('your_user'), $users2import) AND $this->get('your_user')) {
				array_push($users2import, $this->get('your_user'));
			}
			//need room for our current user
			if(in_array($this->user->data['user_id'], $users2import) AND $this->user->data['user_id'] != $this->get('your_user')) {
				$id = $this->user->data['user_id'];
				while(in_array($id, $users2import)) {
					$id++;
				}
				$this->step_data['replace_users'][$this->user->data['user_id']] = $id;
			}
			$sql = "SELECT * FROM ".$this->old[1]."users WHERE user_id IN (".implode(',',$users2import).");";
			$result = $this->old[0]->query($sql);
			while ( $row = $this->old[0]->fetch_record($result) ) {
				if($row['user_id'] == $this->user->data['user_id'] AND $this->user->data['user_id'] != $this->get('your_user')) {
					$row['user_id'] = $id;
				}
				if($row['user_id'] != $this->get('your_user') AND $row['username'] != $this->user->data['username']) {
					foreach($row as $field => $value) {
						if($field == 'user_style') {
							$users[$row['user_id']][$field] = $this->config->get('default_style');
						} elseif ($field == 'privacy_settings' && $value != NULL){
							$priv = unserialize($value);
							if (isset($priv['priv_set']) && intval($priv['priv_set']) == 0){
								$priv['priv_set'] = 1;
							}
							if (isset($priv['priv_phone']) && intval($priv['priv_phone']) == 0){
								$priv['priv_phone'] = 1;
							}
							$users[$row['user_id']][$field] = serialize($priv);
						} elseif($field == 'user_email'){
							$users[$row['user_id']][$field] = $this->encrypt->encrypt($value);
						} elseif($field == 'birthday') {
							list($d,$m,$y) = explode('.', $row['birthday']);
							$users[$row['user_id']][$field] = $this->time->mktime(0,0,0,$m,$d,$y);
						} elseif($field != 'user_newpassword') {
							$users[$row['user_id']][$field] = isset($value) ? $value : NULL;
						}
					}
					$users[$row['user_id']]['exchange_key'] = md5(uniqid());
					$users[$row['user_id']]['user_timezone'] = $this->config->get('timezone');
				}
			}
			$this->old[0]->free_result($result);

			$result = $this->new[0]->query("SELECT * FROM ".$this->new[1]."users WHERE user_id = '".$this->user->data['user_id']."';");
			while ( $row = $this->new[0]->fetch_record($result) ) {
				$pass = empty($users);
				foreach($row as $field => $value) {
					if($pass || in_array($field, array_keys(current($users)))) {
						$users[$row['user_id']][$field] = $value;
					}
				}
			}
			$this->new[0]->free_result($result);

			$this->new[0]->query("TRUNCATE ".$this->new[1]."users;");
			$this->new[0]->query("INSERT INTO ".$this->new[1]."users :params;", $users);
			
			$this->new[0]->query("UPDATE ".$this->new[1]."users SET rules = '".$this->new[0]->escape(1)."' WHERE user_id = '".$this->user->data['user_id']."';");
			
			//user-permissions
			$this->new[0]->query("DELETE FROM ".$this->new[1]."groups_users WHERE user_id != '".$this->user->data['user_id']."';");
			$sql = "INSERT INTO ".$this->new[1]."groups_users (`user_id`, `group_id`) VALUES ";
			$sqls = array();
			foreach($users as $user_id => $data) {
				if($user_id != $this->user->data['user_id']) {
					$sqls[] = "('".$user_id."', '".(($this->get('admin:'.$user_id)) ? '3' : '4')."')";
				}
			}
			if($sqls) {
				$this->new[0]->query($sql.implode(', ', $sqls).';');
			}
		}
		return true;
	}

	protected function config_news_log() {
		$this->current_step = 'config_news_log';
		$output = '<table width="60%" cellpadding="1" cellspacing="1" align="center" class="task_table">';
		$output .= '<tr><td colspan="2" class="th_sub">'.$this->lang['import'].'</th><th>'.$this->lang['older_than'].'</td></tr>';
		$output .= '<tr><td width="50%">'.$this->lang['config'].'</td><td width="40"><input type="checkbox" name="config" value="1" checked="checked" /></td><td> ---- </td></tr>';
		$output .= '<tr><td>'.$this->lang['news'].'</td><td><input type="checkbox" name="news" value="1" checked="checked" /></td><td><input type="text" name="news_date" value="0" />'.$this->lang['enter_date_format'].'</td></tr>';
		$output .= '<tr><td>'.$this->lang['log'].'</td><td><input type="checkbox" name="log" value="1" checked="checked" /></td><td><input type="text" name="logs_date" value="0" />'.$this->lang['enter_date_format'].'</td></tr>';
		$output .= '<tr><th colspan="3" class="th_sub"><input type="submit" name="config_news_log" value="'.$this->lang['submit'].'" class="mainoption"/></th></tr></table>';
		return $output;
	}

	protected function parse_config_news_log() {
		if(!$this->get('config_news_log')) {
			return false;
		}
		$this->connect2olddb();
		if($this->get('config')) {
			$configs = array();
			$ignore = array('cookie_domain', 'cookie_name', 'cookie_path', 'plus_version', 'server_name', 'server_path', 'server_port', 'session_cleanup', 'session_last_cleanup', 'default_style', 'cmsbridge_active', 'start_page',
						/* Portal: */'pk_latestposts_bbmodule', 'pk_latestposts_url', 'pk_latestposts_dbprefix', 'pk_latestposts_trimtitle', 'pk_latestposts_dbmode', 'pk_latestposts_dbhost', 'pk_latestposts_dbname', 'pk_latestposts_dbuser', 'pk_latestposts_dbpassword'
			);
			$sql = "SELECT * FROM ".$this->old[1]."plus_config;";
			$result = $this->old[0]->query($sql);
			$white_list = array_keys($this->config->get_config());
			while ( $row = $this->old[0]->fetch_record($result) ) {
				if(!in_array($row['config_name'], $ignore) AND in_array($row['config_name'], $white_list)) {
					$configs[$row['config_name']] = $row['config_value'];
				}
			}
			$new_games = array('Aion' => 'aion', 'Allods'=>'allods', 'AoC' => 'aoc', 'Atlantica' => 'atlantica', 'DAoC' => 'daoc', 'Everquest' => 'eq', 'Everquest2' => 'eq2', 'ffxi' => 'ffxi', 'LOTRO' => 'lotro', 'Rift'=> 'rift', 'RunesOfMagic' => 'rom', 'shakesfidget' => 'shakesfidget', 'sto'=>'sto', 'swtor'=>'swtor', 'Tera'=>'tera','TR' => 'tr', 'Vanguard-SoH' => 'vanguard', 'Warhammer' => 'warhammer', 'WoW' => 'wow');
			$sql = "SELECT * FROM ".$this->old[1]."config;";
			$classcolors = array();
			$result = $this->old[0]->query($sql);
			while ( $row = $this->old[0]->fetch_record($result) ) {
				if(!in_array($row['config_name'], $ignore) AND in_array($row['config_name'], $white_list)) {
					$configs[$row['config_name']] = $row['config_value'];
				}
				if($row['config_name'] == 'default_game') {
					$configs['default_game'] = $new_games[$row['config_value']];
				}
				if($row['config_name'] == 'pk_multidkp') $this->step_data['import_data']['pk_mdkp'] = $row['config_value'];
				if($row['config_name'] == 'default_style') {
					//import classcolor
					$cc_res = $this->old[0]->query("SELECT class, color FROM ".$this->old[1]."classcolors WHERE template = '".$row['config_value']."';");
					while( $row = $this->old[0]->fetch_row($cc_res) ) {
						$classcolors[$row['class']] = $row['color'];
					}
				}
				if($row['config_name'] == 'admin_email') $configs['admin_email'] = $this->encrypt->encrypt($row['config_value']);
			}
			$this->old[0]->free_result($result);
			//some special handling for certain configs
			$configs['pk_itemhistory_dia'] = ($configs['pk_itemhistory_dia']) ? 0 : 1;
			$game_lang_conv = array('en' => 'english', 'de' => 'german', 'es' => 'spanish', 'fr' => 'french', 'ru' => 'russian');
			$configs['game_language'] = $game_lang_conv[$configs['game_language']];
			$this->config->set($configs);
			unset($white_list);
			unset($configs);
			unset($game_lang_conv);
			//import classcolor
			if(!empty($classcolors) && is_array($classcolors)) {
				$template = $this->config->get('default_style');
				foreach($classcolors as $class => $color) {
					$class = registry::register('game')->get_id('classes', $class);
					if($class) $this->new[0]->query("REPLACE INTO ".$this->new[1]."classcolors :params;", array('template' => $template, 'class_id' => $class, 'color' => $color));
				}
			}
		}
		if($this->get('news')) {
			$news = array();
			list($d,$m,$y) = explode('.',$this->get('news_date', '1.1.1970'));
			$date = mktime(0,0,0,$m,$d,$y);
			$sql = "SELECT * FROM ".$this->old[1]."news WHERE news_date > ".$date.";";
			$result = $this->old[0]->query($sql);
			while ( $row = $this->old[0]->fetch_record($result) ) {
				foreach($row as $field => $value) {
					if($field == 'news_message') $value = registry::register('bbcode')->toHTML($value);
					if($field != 'news_id') {
						$news[$row['news_id']][$field] = $value;
					}
				}
			}
			$this->old[0]->free_result($result);
			$this->new[0]->query("TRUNCATE ".$this->new[1]."news;");
			$sql = "INSERT INTO ".$this->new[1]."news (news_id, news_headline, news_message, news_date, user_id, showRaids_id, extended_message, nocomments, news_permissions, news_flags) VALUES ";
			$sqls = array();
			foreach($news as $id => $newss) {
				if(isset($this->step_data['replace_users'][$newss['user_id']])) {
					$newss['user_id'] = $this->step_data['replace_users'][$newss['user_id']];
				}
				$sqls[] = "('".$this->new[0]->escape($id)."', '".$this->new[0]->escape($newss['news_headline'])."', '".$this->new[0]->escape($newss['news_message'])."', '".$this->new[0]->escape($newss['news_date'])."', '".$this->new[0]->escape($newss['user_id'])."', '".$this->new[0]->escape($newss['showRaids_id'])."', '".$this->new[0]->escape($newss['extended_message'])."', '".$this->new[0]->escape($newss['nocomments'])."', '".$this->new[0]->escape($newss['news_permissions'])."', '".$this->new[0]->escape($newss['news_flags'])."')";
			}
			$this->new[0]->query($sql.implode(', ', $sqls).';');
			unset($news);
			$comments = array();
			$sql = "SELECT * FROM ".$this->old[1]."comments;";
			$result = $this->old[0]->query($sql);
			while ( $row = $this->old[0]->fetch_record($result) ) {
				foreach($row as $field => $value) {
					$comments[$row['id']][$field] = $this->new[0]->escape($value);
				}
			}
			$this->old[0]->free_result($result);
			$this->new[0]->query("TRUNCATE ".$this->new[1]."comments;");
			if (count($comments) > 0){
				$sql = "INSERT INTO ".$this->new[1]."comments (`".implode('`, `', array_keys(current($comments)))."`) VALUES ";
				$sqls = array();
				foreach($comments as $comment) {
					$sqls[] = "('".implode("', '", $comment)."')";
				}
				$this->new[0]->query($sql.implode(', ', $sqls).';');
			}
			unset($comments);
		}
		if($this->get('log')) {
			$logs = array();
			list($d,$m,$y) = explode('.',$this->get('logs_date', '1.1.1970'));
			$date = mktime(0,0,0,$m,$d,$y);
			$sql = "SELECT * FROM ".$this->old[1]."logs WHERE log_date > ".$date." LIMIT 5000;";
			$result = $this->old[0]->query($sql);
			include($this->root_path.'maintenance/includes/tasks/import06/plugin_logs.php');
			while ( $row = $this->old[0]->fetch_record($result) ) {
				$current_log = array();
				if(isset($this->step_data['replace_users'][$row['admin_id']])) {
					$row['admin_id'] = $this->step_data['replace_users'][$row['admin_id']];
				}
				$current_log['log_id'] = $this->new[0]->escape($row['log_id']);
				$current_log['log_date'] = $this->new[0]->escape($row['log_date']);
				eval($row['log_action']);
				if(isset($log_action['header'])) preg_match("/\{L_(.+)\}/", $log_action['header'], $to_replace);
				else preg_match("/\{L_(.+)\}/", $this->new[0]->escape($row['log_type']), $to_replace);
				if(!isset($to_replace[1]) || !$to_replace[1]) continue;
				unset($log_action['header']);
				$current_log['log_value'] = $this->new[0]->escape(serialize($log_action));
				$current_log['log_ipaddress'] = $this->new[0]->escape($row['log_ipaddress']);
				$current_log['log_sid'] = $this->new[0]->escape($row['log_sid']);
				$current_log['log_result'] = ($this->new[0]->escape($row['log_result']) == '{L_SUCCESS}') ? 1 : 0;
				$current_log['log_tag'] = strtolower($to_replace[1]);
				$current_log['log_plugin'] = (in_array($row['log_type'], array_keys($plugin_logs))) ? $this->new[0]->escape($plugin_logs[$row['log_type']]) : 'core';
				$current_log['log_flag'] = 1;
				$current_log['user_id'] = $this->new[0]->escape($row['admin_id']);
				if(in_array($current_log['log_plugin'], $copy)) $logs[$row['log_id']] = $current_log;
			}
			$this->old[0]->free_result($result);
			$this->new[0]->query("TRUNCATE ".$this->new[1]."logs;");
			$sql_pre = "INSERT INTO ".$this->new[1]."logs (".implode(', ', array_keys(current($logs))).") VALUES ";
			$sqls = array();
			$counter = 0;
			//split in 1000 logs per sql to prevent mysql-server going away
			foreach($logs as $id => $log) {
				$k = $counter/1000;
				settype($k, 'int');
				$sqls[$k][] = "('".implode("', '", $log)."')";
				$counter++;
			}
			foreach($sqls as $sql_post) {
				$this->new[0]->query($sql_pre.implode(', ', $sql_post).';');
			}
			unset($logs);
		}
		return true;
	}

	protected function events() {
		$this->current_step = 'events';
		$output = '<table width="60%" cellpadding="1" cellspacing="1" align="center" class="task_table colorswitch">';
		$output .= '<tr><td colspan="3" class="th_sub">'.$this->lang['which_events'].' '.$this->js_mark_boxes('{NUM}', 'e_').'</td></tr>';
		//build eventlist
		$events = array();
		$this->connect2olddb();
		$result = $this->old[0]->query("SELECT event_name, event_id FROM ".$this->old[1]."events ORDER BY event_name ASC;");
		$c = 0;
		while ( $row = $this->old[0]->fetch_Record($result) ) {
			$events[$c]['id'] = $row['event_id'];
			$events[$c]['name'] = $row['event_name'];
			$c++;
		}

		$max = count($events);
		$output = str_replace('{NUM}', $max, $output);
		$rows = intval($max/3);
		if($rows < ($max/3)) {
			$rows++;
		}
		$done = array();
		for($i=0; $i<$rows; $i++) {
			$one = false;
			$two = false;
			$output .= "<tr>";
			if(!in_array($i, $done) AND $i < $max) {
				$output .= "<td width='33%'><input type='checkbox' name='event_id[]' id='e_".$i."' value='".$events[$i]['id']."' />".$events[$i]['name']."</td>";
				$one = true;
			}
			if(!in_array(($i+$rows), $done) AND ($i+$rows) < $max) {
				$output .= "<td width='33%'><input type='checkbox' name='event_id[]' id='e_".($i+$rows)."' value='".$events[$i+$rows]['id']."' />".$events[$i+$rows]['name']."</td>";
				$two = true;
			}
			if(!in_array(($i+2*$rows), $done) AND ($i+2*$rows) < $max) {
				$colspan = ($one) ? (($two) ? '' : "colspan='2'") : (($two) ? "colspan='2'" : "colspan='3'");
				$output .= "<td width='33%' ".$colspan."><input type='checkbox' name='event_id[]' id='e_".($i+2*$rows)."' value='".$events[$i+2*$rows]['id']."' />".$events[$i+2*$rows]['name']."</td>";
			}
			$output .= "</tr>";
			$done[] = $i;
			$done[] = ($i+$rows);
			$done[] = ($i+2*$rows);
		}
		$output .= "<tr><th colspan='3' class='th_sub'><input type='submit' name='events' value='".$this->lang['submit']."' class='mainoption'/></th></tr></table>";
		return $output;
	}

	protected function parse_events() {
		if(!$this->get('events')) {
			return false;
		}
		if(is_array($this->get('event_id', 'int', true))) {
			$events = array();
			$this->connect2olddb();
			$events2import = $this->get('event_id', 'int', true);
			$sql = "SELECT * FROM ".$this->old[1]."events WHERE event_id IN (".implode(',',$events2import).");";
			$result = $this->old[0]->query($sql);
			while ( $row = $this->old[0]->fetch_record($result) ) {
				foreach($row as $field => $value) {
					$events[$row['event_id']][$field] = $this->new[0]->escape($value);
				}
			}
			$this->old[0]->free_result($result);
			
			if(!$events) return true; 	//no events to import
			
			$this->new[0]->query("TRUNCATE ".$this->new[1]."events;");
			$fields = "`".implode('`, `', array_keys(current($events)))."`";
			$sql = "INSERT INTO ".$this->new[1]."events (".$fields.") VALUES ";
			$sqls = array();
			foreach($events as $event) {
				$sqls[] = "('".implode("', '", $event)."')";
			}
			$this->new[0]->query($sql.implode(', ', $sqls).';');
			unset($events);
		}
		return true;
	}

	protected function multidkp() {
		$this->current_step = 'multidkp';
		$this->connect2olddb();
		$sql = "SELECT multidkp_id, multidkp_name FROM ".$this->old[1]."multidkp ORDER BY multidkp_name DESC;";
		$result = $this->old[0]->query($sql);
		$multis = array();
		while ( $row = $this->old[0]->fetch_record($result) ) {
			$multis[] = array('name' => $row['multidkp_name'], 'id' => $row['multidkp_id']);
		}
		$output = '<table width="60%" cellpadding="1" cellspacing="1" align="center" class="task_table colorswitch">';
		//no multidkp, ask for name and desc
		if(count($multis) == 0) {
			$output .= '<tr><th colspan="3">'.$this->lang['no_multi_found'].'</th></tr>';
			$output .= '<tr><td width="33%">'.$this->lang['multi_name'].' <input type="text" name="multi_name" value="" /></td>';
			$output .= '<td colspan="2">'.$this->lang['multi_desc'].' <input type="text" name="multi_desc" value="" /></td></tr>';
		} else {
			$output .= '<tr><td colspan="3" class="th_sub">'.$this->lang['which_multis'].' '.$this->js_mark_boxes('{NUM}', 'm_').'</td></tr>';
			$max = count($multis);
			$output = str_replace('{NUM}', $max, $output);
			$rows = $max/3;
			settype($rows,'int');
			if($rows < ($max/3)) {
				$rows++;
			}
			$rows = ($rows < 1) ? 1 : $rows;
			$done = array();
			for($i=0; $i<$rows; $i++) {
				$one = false;
				$two = false;
				$output .= "<tr>";
				if(!in_array($i, $done) AND $i <= ($max-1)) {
					$output .= "<td width='33%'><input type='checkbox' name='multi_id[]' id='m_".$i."' value='".$multis[$i]['id']."' />".$multis[$i]['name']."</td>";
					$one = true;
				}
				if(!in_array(($i+$rows), $done) AND ($i+$rows) <= ($max-1)) {
					$output .= "<td width='33%'><input type='checkbox' name='multi_id[]' id='m_".($i+$rows)."' value='".$multis[$i+$rows]['id']."' />".$multis[$i+$rows]['name']."</td>";
					$two = true;
				}
				if(!in_array(($i+2*$rows), $done) AND ($i+2*$rows) <= ($max-1)) {
					$colspan = ($one) ? (($two) ? '' : "colspan='2'") : (($two) ? "colspan='2'" : "colspan='3'");
					$output .= "<td width='33%' ".$colspan."><input type='checkbox' name='multi_id[]' id='m_".($i+2*$rows)."' value='".$multis[$i+2*$rows]['id']."' 	/>".$multis[$i+2*$rows]['name']."</td>";
				}
				$output .= "</tr>";
				$done[] = $i;
				$done[] = ($i+$rows);
				$done[] = ($i+2*$rows);
			}
		}
		$output .= "<tr><th colspan='3' class='th_sub'><input type='submit' name='multidkp' value='".$this->lang['submit']."' class='mainoption'/></th></tr></table>";
		return $output;

	}

	protected function parse_multidkp() {
		if(!$this->get('multidkp')) {
			return false;
		}
		if(is_array($this->get('multi_id', 'int', true)) && count($this->get('multi_id', 'int', true)) > 0) {
			$multis = array();
			$this->connect2olddb();
			$multis2import = $this->get('multi_id', 'int', true);
			$sql = "SELECT * FROM ".$this->old[1]."multidkp WHERE multidkp_id IN (".implode(',',$multis2import).");";
			$result = $this->old[0]->query($sql);
			while ( $row = $this->old[0]->fetch_record($result) ) {
				$multis[$row['multidkp_id']]['name'] = $row['multidkp_name'];
				$multis[$row['multidkp_id']]['desc'] = $row['multidkp_disc'];
			}
			$this->old[0]->free_result($result);
			$this->new[0]->query("TRUNCATE ".$this->new[1]."multidkp;");
			$sql = "INSERT INTO ".$this->new[1]."multidkp (multidkp_id, multidkp_name, multidkp_desc) VALUES ";
			$sqls = array();
			foreach($multis as $multi_id => $multi) {
				$sqls[] = "('".$this->new[0]->escape($multi_id)."', '".$this->new[0]->escape($multi['name'])."', '".$this->new[0]->escape($multi['desc'])."')";
			}
			$this->new[0]->query($sql.implode(', ', $sqls).';');
			$multi2ev = array();
			$sql = "SELECT m.multidkp2event_id, m.multidkp2event_multi_id, e.event_id FROM ".$this->old[1]."multidkp2event m, ".$this->old[1]."events e WHERE m.multidkp2event_multi_id IN (".implode(',',$multis2import).") AND m.multidkp2event_eventname = e.event_name;";
			$result = $this->old[0]->query($sql);
			while ( $row = $this->old[0]->fetch_record($result) ) {
				$multi2ev[$row['multidkp2event_id']]['multi_id'] = $row['multidkp2event_multi_id'];
				$multi2ev[$row['multidkp2event_id']]['event_id'] = $row['event_id'];
			}
			$this->old[0]->free_result($result);
			$this->new[0]->query("TRUNCATE ".$this->new[1]."multidkp2event;");
			$sql = "INSERT INTO ".$this->new[1]."multidkp2event (multidkp2event_multi_id, multidkp2event_event_id) VALUES ";
			$sqls = array();
			foreach($multi2ev as $m2e_id => $mu2ev) {
				$sqls[] = "('".$this->new[0]->escape($mu2ev['multi_id'])."', '".$this->new[0]->escape($mu2ev['event_id'])."')";
			}
			$this->new[0]->query($sql.implode(', ', $sqls).';');
			unset($multi2ev);
			$this->new[0]->query("TRUNCATE ".$this->new[1]."itempool;");
			$this->new[0]->query("TRUNCATE ".$this->new[1]."multidkp2itempool;");
			$sql1 = "INSERT INTO ".$this->new[1]."itempool (itempool_id, itempool_name, itempool_desc) VALUES ";
			$sql2 = "INSERT INTO ".$this->new[1]."multidkp2itempool (multidkp2itempool_itempool_id, multidkp2itempool_multi_id) VALUES ";
			$sqls1 = array();
			$sqls2 = array();
			$iid = 1;
			foreach($multis as $id => $multi) {
				$sqls1[] = "('".$iid."', '".$this->new[0]->escape($multi['name'])."', '".$this->new[0]->escape($multi['desc'])."')";
				$sqls2[] = "('".$iid."', '".$id."')";
				$iid++;
			}
			if(count($sqls1)) $this->new[0]->query($sql1.implode(', ', $sqls1).';');
			else $this->new[0]->query($sql1."('1', 'default', 'Default Itempool');");
			if(count($sqls2)) $this->new[0]->query($sql2.implode(', ', $sqls2),';');
			else $this->new[0]->query($sql2."('1', '1');");
			unset($multis);
		} else {
			$this->new[0]->query("TRUNCATE ".$this->new[1]."multidkp;");
			$this->new[0]->query("INSERT INTO ".$this->new[1]."multidkp (multidkp_name, multidkp_desc) VALUES ('".$this->get('multi_name', 'default')."', '".$this->get('multi_desc', 'default')."');");
			$id = $this->new[0]->insert_id();
			$this->new[0]->query("TRUNCATE ".$this->new[1]."multidkp2event;");
			$sql = "INSERT INTO ".$this->new[1]."multidkp2event (multidkp2event_multi_id, multidkp2event_event_id) VALUES ";
			$sqls = array();
			$result = $this->new[0]->query("SELECT event_id FROM ".$this->new[1]."events;");
			while ( $row = $this->new[0]->fetch_record($result) ) {
				$sqls[] = "('".$id."', '".$row['event_id']."')";
			}
			$this->new[0]->free_result($result);
			$this->new[0]->query($sql.implode(', ', $sqls).';');
			$this->new[0]->query("TRUNCATE ".$this->new[1]."multidkp2itempool;");
			$this->new[0]->query("INSERT INTO ".$this->new[1]."multidkp2itempool (multidkp2itempool_itempool_id, multidkp2itempool_multi_id) VALUES ('1', '".$id."');");
		}
		return true;
	}

	protected function members() {
		$this->current_step = 'members';
		$this->connect2olddb();
		$sql = "SELECT member_id, member_name FROM ".$this->old[1]."members ORDER BY member_name ASC;";
		$result = $this->old[0]->query($sql);
		$members = array();
		$cc = 0;
		while ( $row = $this->old[0]->fetch_record($result) ) {
			$members[$cc]['name'] = $row['member_name'];
			$members[$cc]['id'] = $row['member_id'];
			$cc++;
		}
		$this->old[0]->free_result($result);

		$output = '<table width="60%" cellpadding="1" cellspacing="1" align="center" class="task_table colorswitch">';
		$output .= '<tr><td colspan="3" class="th_sub">'.$this->lang['which_members'].' '.$this->js_mark_boxes('{NUM}', 'm_').'</td></tr>';
		$max = count($members);
		$output = str_replace('{NUM}', $max, $output);
		$rows = $max/3;
		settype($rows,'int');
		if($rows < ($max/3)) {
			$rows++;
		}
		$done = array();
		for($i=0; $i<$rows; $i++) {
			$one = false;
			$two = false;
			$output .= "<tr>";
			if(!in_array($i, $done) AND $i < $max) {
				$output .= "<td width='33%'><input type='checkbox' name='member_id[]' id='m_".$i."' value='".$members[$i]['id']."' />".$members[$i]['name']."</td>";
				$one = true;
			}
			if(!in_array(($i+$rows), $done) AND ($i+$rows) < $max) {
				$output .= "<td width='33%'><input type='checkbox' name='member_id[]' id='m_".($i+$rows)."' value='".$members[$i+$rows]['id']."' />".$members[$i+$rows]['name']."</td>";
				$two = true;
			}
			if(!in_array(($i+2*$rows), $done) AND ($i+2*$rows) < $max) {
				$colspan = ($one) ? (($two) ? '' : "colspan='2'") : (($two) ? "colspan='2'" : "colspan='3'");
				$output .= "<td width='33%' ".$colspan."><input type='checkbox' name='member_id[]' id='m_".($i+2*$rows)."' value='".$members[$i+2*$rows]['id']."' 	/>".$members[$i+2*$rows]['name']."</td>";
			}
			$output .= "</tr>";
			$done[] = $i;
			$done[] = ($i+$rows);
			$done[] = ($i+2*$rows);
		}
		$output .= "<tr><th colspan='3'><input type='checkbox' name='ranks' value='true' />".$this->lang['import_ranks']."</th></tr>";
		$output .= "<tr><th colspan='3'>".$this->lang['create_special_members']."</th></tr>";
		for($i=0; $i<6; $i++) {
			$output .= (($i == 0 || $i == 3) ? "<tr>" : "")."<td><input type='text' name='special_members[]' value='' /></td>".(($i == 2 || $i == 5) ? "</tr>" : "");
		}
		$output .= "<tr><th colspan='3' class='th_sub'><input type='submit' name='members' value='".$this->lang['submit']."' class='mainoption'/></th></tr></table>";
		return $output;
	}

	protected function parse_members() {
		if(!$this->get('members')) {
			return false;
		}
		if($this->get('ranks')) {
			$ranks = array();
			$this->connect2olddb();
			$result = $this->old[0]->query("SELECT * FROM ".$this->old[1]."member_ranks WHERE rank_id > 0 ORDER BY rank_name DESC;");
			while ( $row = $this->old[0]->fetch_record($result) ) {
				$ranks[$row['rank_id']]['name'] = $row['rank_name'];
				$ranks[$row['rank_id']]['hide'] = ($row['rank_hide']) ? '1' : '0';
				$ranks[$row['rank_id']]['prefix'] = $row['rank_prefix'];
				$ranks[$row['rank_id']]['suffix'] = $row['rank_suffix'];
			}
			$this->old[0]->free_result($result);
			$sql = "REPLACE INTO ".$this->new[1]."member_ranks (rank_id, rank_name, rank_hide, rank_prefix, rank_suffix) VALUES ";
			$sqls = array();
			foreach($ranks as $rank_id => $rank) {
				$sqls[] = "('".$this->new[0]->escape($rank_id)."', '".$this->new[0]->escape($rank['name'])."', '".$rank['hide']."', '".$this->new[0]->escape($rank['prefix'])."', '".$this->new[0]->escape($rank['suffix'])."')";
			}
			$this->new[0]->query($sql.implode(', ', $sqls).';');
		}
		if(is_array($this->get('member_id', 'int', true))) {
			$members = array();
			$m2cr = array();
			$this->connect2olddb();
			$members2import = $this->get('member_id', 'int', true);
			$factions = array();
			if($result = $this->old[0]->query("SELECT faction, member_id FROM ".$this->old[1]."member_additions;")) {
				while($row = $this->old[0]->fetch_row($result)) {
					$factions[$row['member_id']] = $row['faction'];
				}
				$profilefields = array();
				$pf_res = $this->new[0]->query("SELECT name FROM ".$this->new[1]."member_profilefields;");
				while($row = $this->new[0]->fetch_row($pf_res)) $profilefields[$row['name']] = '';
				$this->new[0]->free_result($pf_res);
				unset($pf_res);
			}
			$sql = "SELECT m.*, r.race_name, c.class_name FROM ".$this->old[1]."members m, ".$this->old[1]."races r, ".$this->old[1]."classes c WHERE r.race_id = m.member_race_id AND c.class_id = m.member_class_id AND m.member_id IN (".implode(',',$members2import).");";
			$result = $this->old[0]->query($sql);
			while ( $row = $this->old[0]->fetch_record($result) ) {
				foreach($row as $field => $value) {
					if(in_array($field, array('member_id', 'member_name', 'member_status', 'member_level', 'member_race_id', 'member_class_id', 'member_rank_id'))) {
						$members[$row['member_id']][$field] = $this->new[0]->escape($value);
					} elseif($field == 'member_current') {
						$this->step_data['import_data']['members'][$row['member_id']] = $this->new[0]->escape($value);
					}
				}
				$members[$row['member_id']]['member_main_id'] = $row['member_id'];
				if(isset($factions[$row['member_id']])) {
					$profilefields['faction'] = $factions[$row['member_id']];
				}
				$members[$row['member_id']]['profiledata'] = registry::register('xmltools')->Array2Database($profilefields);
				$m2cr[$row['member_id']]['class'] = $row['class_name'];
				$m2cr[$row['member_id']]['race'] = $row['race_name'];
			}
			$this->old[0]->free_result($result);
			$this->new[0]->query("TRUNCATE ".$this->new[1]."members;");
			$fields = "`".implode('`, `', array_keys(current($members)))."`";
			$sql = "INSERT INTO ".$this->new[1]."members (".$fields.") VALUES ";
			$sqls = array();
			foreach($members as $member) {
				$member['member_race_id'] = registry::register('game')->get_id('races', $m2cr[$member['member_id']]['race']);
				$member['member_class_id'] = registry::register('game')->get_id('classes', $m2cr[$member['member_id']]['class']);
				if(!$member['member_class_id']) $member['member_class_id'] = 0;
				if(!$member['member_race_id']) $member['member_race_id'] = 0;
				$sqls[] = "('".implode("', '", $member)."')";
			}
			$this->new[0]->query($sql.implode(', ', $sqls).';');

			$members = array();
			$sql = "SELECT * FROM ".$this->old[1]."member_user WHERE member_id IN (".implode(',',$members2import).");";
			$result = $this->old[0]->query($sql);
			while ( $row = $this->old[0]->fetch_record($result) ) {
				$members[$row['member_id']] = (isset($this->step_data['replace_users'][$row['user_id']])) ? $this->step_data['replace_users'][$row['user_id']] : $row['user_id'];
			}
			if($members) {
				$this->old[0]->free_result($result);
				$user_ids = array();
				$result = $this->new[0]->query("SELECT user_id FROM ".$this->new[1]."users;");
				while ( $row = $this->new[0]->fetch_record($result) ) {
					$user_ids[] = $row['user_id'];
				}
				if($user_ids) {
					$this->new[0]->free_result($result);
					$this->new[0]->query("TRUNCATE ".$this->new[1]."member_user;");
					$sql = "INSERT INTO ".$this->new[1]."member_user (member_id, user_id) VALUES ";
					$sqls = array();
					foreach($members as $mid => $uid) {
						if(in_array($uid, $user_ids)) {
							$sqls[] = "('".$mid."', '".$uid."')";
						}
					}
					if(count($sqls) > 0) $this->new[0]->query($sql.implode(', ', $sqls).';');
					unset($members);
				}
			}
		}
		if(is_array($this->get('special_members', 'string', true))) {
			$specials = $this->get('special_members', 'string', true);
			$ids = array();
			foreach($specials as $special_name) {
				if($special_name) {
					$this->new[0]->query("INSERT INTO ".$this->new[1]."members (member_name, profiledata) VALUES ('".$special_name."', '');");
					$ids[] = $this->new[0]->insert_id();
				}
			}
			$this->config->set('special_members', serialize($ids));
		}
		//set flag for char-creation-date necessecity
		$this->config->set('char_creation_date_update', 1);
		return true;
	}

	protected function raids() {
		$this->current_step = 'raids';
		$this->connect2olddb();
		$sql = "SELECT raid_id, raid_name FROM ".$this->old[1]."raids;";
		$result = $this->old[0]->query($sql);
		$raids = array();
		while ( $row = $this->old[0]->fetch_record($result) ) {
			$raids[$row['raid_id']] = $row['raid_name'];
		}
		$this->old[0]->free_result($result);
		$events = array();
		$result = $this->new[0]->query("SELECT event_id, event_name FROM ".$this->new[1]."events ORDER BY event_name ASC;");
		while ( $row = $this->new[0]->fetch_record($result) ) {
			$events[$row['event_id']] = $row['event_name'];
		}
		$this->new[0]->free_result($result);
		$wrong_raids = array();
		foreach($raids as $raid_id => $name) {
			$key = array_search($name, $events);
			if(!$key) {
				$wrong_raids[$raid_id] = $name;
			} else {
				$this->step_data['import_data']['raids'][$raid_id] = $key;
			}
		}
		$output = $this->js_select_global();
		$output .= '<table width="60%" cellpadding="1" cellspacing="1" align="center" class="task_table colorswitch" id="select_table">';
		if(count($wrong_raids) > 0) {
			$output .= "<tr><td colspan='2'>".$this->lang['raids_with_no_event']."</td></tr>";
			$output .= "<tr><td onclick='SelectAll();' style='cursor:pointer;'>".$this->lang['negate_selection']."</td><td>".$this->lang['change_checked_to'].": ".$this->select_field('global_event', $events, " onchange='UpdateChecked(this.value);'")."</td></tr>";
			foreach($wrong_raids as $raid_id => $name) {
				$output .= "<tr><td width='50%'><input type='checkbox' name='raid_ev[]' class='select_me' id='raid_".$raid_id."' />".$this->lang['raid_id'].": ".$raid_id.", ".$this->lang['event_name'].": ".$name."</td>";
				$output .= "<td>".$this->select_field('raid_event['.$raid_id.']', $events, " id='select_raid_".$raid_id."'")."</td></tr>";
			}
		} else {
			$output .= "<tr><td width='100%'>".$this->lang['no_problems']."</td></tr>";
		}
		$output .= "<tr><th colspan='2' class='th_sub'><input type='submit' name='raids' value='".$this->lang['submit']."' class='mainoption'/></th></tr></table>";
		return $output;
	}

	protected function parse_raids() {
		if(!$this->get('raids')) {
			return false;
		}
		$raids = array();
		$this->connect2olddb();
		$result = $this->old[0]->query("SELECT * FROM ".$this->old[1]."raids;");
		while ( $row = $this->old[0]->fetch_record($result) ) {
			foreach($row as $field => $value) {
				if($field != 'raid_name') {
					$raids[$row['raid_id']][$field] = $this->new[0]->escape($value);
				}
			}
			if($this->step_data['import_data']['raids'][$row['raid_id']]) {
				$raids[$row['raid_id']]['event_id'] = $this->step_data['import_data']['raids'][$row['raid_id']];
			} else {
				$raids[$row['raid_id']]['event_id'] = $this->get('raid_event:'.$row['raid_id']);
			}
		}
		$this->old[0]->free_result($result);
		unset($this->step_data['import_data']['raids']);
		$fields = "`".implode('`, `', array_keys(current($raids)))."`";
		$this->new[0]->query("TRUNCATE ".$this->new[1]."raids;");
		$this->new[0]->query("TRUNCATE ".$this->new[1]."raid_attendees;");
		$sql = "INSERT INTO ".$this->new[1]."raids (".$fields.") VALUES ";
		$sqls = array();
		$ra_ids = array();
		foreach($raids as $raid_i => $raid) {
			$sqls[] = "('".implode("', '", $raid)."')";
			$ra_ids[] = $raid_i;
		}
		$this->new[0]->query($sql.implode(', ', $sqls).';');
		unset($raids);
		$raid_attendees = array();
		$sql = "SELECT raid_id, member_name FROM ".$this->old[1]."raid_attendees WHERE raid_id IN (".implode(',',$ra_ids).");";
		$result = $this->old[0]->query($sql);
		while ( $row = $this->old[0]->fetch_record($result) ) {
			$raid_attendees[$row['raid_id']][] = $row['member_name'];
		}
		$this->old[0]->free_result($result);
		$members = array();
		$result = $this->new[0]->query("SELECT member_name, member_id FROM ".$this->new[1]."members;");
		while ( $row = $this->new[0]->fetch_record($result) ) {
			$members[$row['member_name']] = $row['member_id'];
		}
		$this->new[0]->free_result($result);
		$raid_atts = array();
		foreach($raid_attendees as $raid_id => $atts) {
			foreach($atts as $name) {
				if($members[$name]) {
					$raid_atts[$raid_id][] = $members[$name];
				}
			}
		}
		$sql = "INSERT INTO ".$this->new[1]."raid_attendees (raid_id, member_id) VALUES ";
		$sqls = array();
		foreach($raid_atts as $raid_id => $atts) {
			foreach($atts as $member_id) {
				$sqls[] = "('".$raid_id."', '".$member_id."')";
			}
		}
		$this->new[0]->query($sql.implode(', ', $sqls).';');
		unset($raid_atts);
		unset($raid_attendees);
		return true;
	}

	protected function items() {
		$this->current_step = 'items';
		$items = array();
		$this->connect2olddb();
		$result = $this->old[0]->query("SELECT item_id, item_name, item_buyer, raid_id, item_date FROM ".$this->old[1]."items;");
		while ( $row = $this->old[0]->fetch_record($result) ) {
			$items[$row['item_id']]['name'] = stripslashes($row['item_name']);
			$items[$row['item_id']]['buyer'] = $row['item_buyer'];
			$items[$row['item_id']]['raid'] = $row['raid_id'];
			$items[$row['item_id']]['date'] = $row['item_date'];
		}
		$this->old[0]->free_result($result);
		$raids = array();
		$raid_ids = array();
		$result = $this->new[0]->query("SELECT r.raid_id, e.event_name, r.raid_date, r.raid_note  FROM ".$this->new[1]."raids r, ".$this->new[1]."events e WHERE e.event_id = r.event_id;");
		while ( $row = $this->new[0]->fetch_record($result) ) {
			$raids[$row['raid_id']] = date($this->lang['date_format'], $row['raid_date']).' '.$row['event_name'].': '.((strlen($row['raid_note']) > 50) ? substr($row['raid_note'], 0, 45).' (...)' : $row['raid_note']);
			$raid_ids[] = $row['raid_id'];
		}
		$this->new[0]->free_result($result);
		$members = array();
		$result = $this->new[0]->query("SELECT member_id, member_name FROM ".$this->new[1]."members;");
		while ( $row = $this->new[0]->fetch_record($result) ) {
			$members[$row['member_id']] = $row['member_name'];
		}
		$this->new[0]->free_result($result);
		asort($members);
		$no_member_items = array();
		$no_raid_items = array();
		foreach($items as $item_id => $item) {
			$key = $this->array_isearch($item['buyer'], $members);
			if(!$key OR !$item['buyer']) {
				$no_member_items[$item_id] = $item;
			}
			if(!in_array($item['raid'], $raid_ids)) {
				$no_raid_items[$item_id] = $item;
			}
		}
		$output = "<script type='text/javascript'>
					function Updatefollowing(elem) {
						var value = elem.value;
						var class = elem.className;
						var selects = document.getElementsByTagName('select');
						for(i=0;i<selects.length;i++) {
							if(selects[i].className == class) {
								selects[i].value = value;
							}
						}
					}
					</script>";
		$output .= '<table width="60%" cellpadding="1" cellspacing="1" align="center" class="task_table colorswitch" id="select_table">';
		if(count($no_raid_items) == 0 AND count($no_member_items) == 0) {
			$output .= "<tr><td width='100%' class='th_sub'>".$this->lang['no_problems']."</td></tr>";
		} else {
			if(count($no_raid_items) > 0) {
				$output .= "<tr><td colspan='2' class='th_sub'>".$this->lang['items_without_raid']."</td></tr>";
				foreach($no_raid_items as $item_id => $item) {
					$output .= "<tr><td width='50%'>".date($this->lang['date_format'], $item['date']).' '.$item['name'].' -> '.$item['buyer']."</td>";
					$output .= "<td>".$this->select_field('item_raid['.$item_id.']', $raids)."</td></tr>";
				}
			}
			if(count($no_member_items) > 0) {
				$new_data = array();
				foreach($no_member_items as $item_id => $item) {
					$new_data[$item['buyer']][$item_id] = $item;
				}
				ksort($new_data);
				$output .= "<tr><td colspan='2' class='th_sub'>".$this->lang['items_without_member']."</td></tr>";
				$k = 0;
				$less2items = false;
				foreach($new_data as $buyer => $items) {
					if(count($items) <= 2) {
						$less2items = true;
						continue;
					}
					$output .= "<tr id='items_".$k."'><td class='th_sub'>".$this->lang['item_buyer'].': '.$buyer."</td><td class='th_sub'>".$this->lang['change_item_to'].': '.$this->select_field('item_glob[]', $members, " onchange='Updatefollowing(this);' class='items_".$k."'").'</td>';
					foreach($items as $item_id => $item) {
						$output .= "<tr><td width='50%'>".date($this->lang['date_format'], $item['date']).' '.$item['name']."</td>";
						$output .= "<td>".$this->select_field('item_member['.$item_id.']', $members, " class='items_".$k."'")."</td></tr>";
					}
					$k++;
				}
				if($less2items) {
					$output .= "<tr><td class='th_sub' colspan='2'>".$this->lang['item_buyer_2']."</td></tr>";
					foreach($new_data as $buyer => $items) {
						if(count($items) > 2) continue;
						$output .= "<tr><td width='50%'>".date($this->lang['date_format'], $item['date']).' '.$item['name']." -> ".$buyer."</td>";
						$output .= "<td>".$this->select_field('item_member['.$item_id.']', $members)."</td></tr>";
					}
				}
			}
		}
		$output .= "<tr><th colspan='2' class='th_sub'><input type='submit' name='items' value='".$this->lang['submit']."' class='mainoption' /></th></tr></table>";
		return $output;

	}
	
	private function array_isearch($needle, $array) {
		foreach($array as $key => $val) {
			if(strlen($val) == strlen($needle) AND stripos($val, $needle) === 0) {
				return $key;
			}
		}
		return false;
	}

	protected function parse_items() {
		if(!$this->get('items')) {
			return false;
		}
		$items = array();
		$raid2itempool = array();
		$sql = "SELECT mi.multidkp2itempool_itempool_id, r.raid_id FROM ".$this->new[1]."raids r LEFT JOIN (".$this->new[1]."multidkp2itempool mi LEFT JOIN ".$this->new[1]."multidkp2event me ON mi.multidkp2itempool_multi_id = me.multidkp2event_multi_id) ON me.multidkp2event_event_id = r.event_id;";
		$result = $this->new[0]->query($sql);
		while ( $row = $this->new[0]->fetch_record($result) ) {
			$raid2itempool[$row['raid_id']] = $row['multidkp2itempool_itempool_id'];
		}
		$this->new[0]->free_result($result);
		$this->connect2olddb();
		$members = array();
		$result = $this->new[0]->query("SELECT member_id, member_name FROM ".$this->new[1]."members;");
		while ( $row = $this->new[0]->fetch_record($result) ) {
			$members[$row['member_id']] = $row['member_name'];
		}
		$this->old[0]->free_result($result);
		$result = $this->old[0]->query("SELECT * FROM ".$this->old[1]."items;");
		while ( $row = $this->old[0]->fetch_record($result) ) {
			if(is_array($this->get('item_raid', 'int', true)) AND in_array($row['item_id'], $this->get('item_raid', 'int', true))) {
				$row['raid_id'] = $this->get('item_raid:'.$row['item_id'], 0);
			}
			if(is_array($this->get('item_member', 'int', true)) AND in_array($row['item_id'], array_keys($this->get('item_member', 'int', true)))) {
				$row['member_id'] = $this->get('item_member:'.$row['item_id'], 0);
			}
			$row['member_id'] = $this->array_isearch($row['item_buyer'], $members);
			if(!$row['member_id']) continue;
			unset($row['item_buyer']);
			unset($row['item_ctrt_wowitemid']);
			foreach($row as $field => $value) {
				$value = stripslashes($value);
				$items[$row['item_id']][$field] = ($value != '') ? $value : null;
			}
		}
		unset($this->step_data['import_data']['items']);
		$this->old[0]->free_result($result);
		$this->new[0]->query("TRUNCATE ".$this->new[1]."items;");
		$i = $n = 0;
		$sqls = array();
		foreach($items as $item) {
			$item['itempool_id'] = $raid2itempool[$item['raid_id']];
			$sqls[$n][] = $item;
			if (($i % 100) == 0 && $i > 0) $n++;
			$i++;
		}
		foreach ($sqls as $arrQuerys) {
			$this->new[0]->query("INSERT INTO ".$this->new[1]."items :params", $arrQuerys);
		}
		unset($items);
		return true;
	}

	protected function adjustments() {
		$this->current_step = 'adjustments';
		$adjs = array();
		$this->connect2olddb();
		$result = $this->old[0]->query("SELECT adjustment_id, adjustment_reason, member_name, raid_name, adjustment_date FROM ".$this->old[1]."adjustments;");
		while ( $row = $this->old[0]->fetch_record($result) ) {
			$adjs[$row['adjustment_id']]['reason'] = $row['adjustment_reason'];
			$adjs[$row['adjustment_id']]['member'] = $row['member_name'];
			$adjs[$row['adjustment_id']]['event'] = $row['raid_name'];
			$adjs[$row['adjustment_id']]['date'] = $row['adjustment_date'];
		}
		$this->old[0]->free_result($result);
		$events = array();
		$result = $this->new[0]->query("SELECT event_name, event_id FROM ".$this->new[1]."events ORDER BY event_name ASC;");
		while ( $row = $this->new[0]->fetch_record($result) ) {
			$events[$row['event_id']] = $row['event_name'];
		}
		$this->new[0]->free_result($result);
		$members = array();
		$result = $this->new[0]->query("SELECT member_name, member_id FROM ".$this->new[1]."members ORDER BY member_name ASC;");
		while ( $row = $this->new[0]->fetch_record($result) ) {
			$members[$row['member_id']] = $row['member_name'];
		}
		$this->new[0]->free_result($result);
		$wrong_member_adj = array();
		$wrong_event_adj = array();
		foreach($adjs as $adj_id => $adj) {
			$key = array_search($adj['member'], $members);
			if(!$key) {
				$wrong_member_adj[$adj_id] = $adj;
			} else {
				$this->step_data['import_data']['adjustments'][$adj_id]['member_id'] = $key;
			}
			$key = array_search($adj['event'], $events);
			if(!$key) {
				$wrong_event_adj[$adj_id] = $adj;
			} else {
				$this->step_data['import_data']['adjustments'][$adj_id]['event_id'] = $key;
			}
		}
		$output = '<table width="60%" cellpadding="1" cellspacing="1" align="center" class="task_table colorswitch">';
		if(count($wrong_member_adj) == 0 AND count($wrong_event_adj) == 0) {
			$output .= "<tr><td width='100%' class='th_sub'>".$this->lang['no_problems']."</td></tr>";
		} else {
			if(count($wrong_event_adj) > 0) {
				$output .= "<tr><td colspan='2' class='th_sub'>".$this->lang['adjs_without_event']."</td></tr>";
				foreach($wrong_event_adj as $adj_id => $adj) {
					$output .= "<tr><td width='50%'>".date($this->lang['date_format'], $adj['date']).' '.$adj['event'].': '.$adj['member']." (".$adj['reason'].")</td>";
					$output .= "<td>".$this->select_field('adj_event['.$adj_id.']', $events)."</td></tr>";
				}
			}
			if(count($wrong_member_adj) > 0) {
				$output .= "<tr><td colspan='2' class='th_sub'>".$this->lang['adjs_without_member']."</td></tr>";
				foreach($wrong_member_adj as $adj_id => $adj) {
					$output .= "<tr><td width='50%'>".date($this->lang['date_format'], $adj['date']).' '.$adj['event'].': '.$adj['member']." (".$adj['reason'].")</td>";
					$output .= "<td>".$this->select_field('adj_member['.$adj_id.']', $members)."</td></tr>";
				}
			}
		}
		$output .= "<tr><th colspan='2' class='th_sub'><input type='submit' name='adjustments' value='".$this->lang['submit']."' class='mainoption' /></th></tr></table>";
		return $output;
	}

	protected function parse_adjustments() {
		if(!$this->get('adjustments')) {
			return false;
		}
		$adjs = array();
		$this->connect2olddb();
		$result = $this->old[0]->query("SELECT * FROM ".$this->old[1]."adjustments;");
		while ( $row = $this->old[0]->fetch_record($result) ) {
			if(is_array($this->get('adj_event', 'int', true)) AND in_array($row['adjustment_id'], $this->get('adj_event', 'int', true))) {
				if(!$this->get('adj_event:'.$row['adjustment_id'], 0)) continue;
				$row['event_id'] = $this->get('adj_event:'.$row['adjustment_id'], 0);
			} else {
				$row['event_id'] = $this->step_data['import_data']['adjustments'][$row['adjustment_id']]['event_id'];
			}
			if(is_array($this->get('adj_member', 'int', true)) AND in_array($row['adjustment_id'], $this->get('adj_member', 'int', true))) {
				if(!$this->get('adj_member:'.$row['adjustment_id'], 0)) continue;
				$row['member_id'] = $this->get('adj_member:'.$row['adjustment_id'], 0);
			} else {
				$row['member_id'] = $this->step_data['import_data']['adjustments'][$row['adjustment_id']]['member_id'];
			}
			unset($row['member_name']);
			unset($row['raid_name']);
			foreach($row as $field => $value) {
				$value = $this->new[0]->escape($value);
				$adjs[$row['adjustment_id']][$field] = ($value != '') ? $value : null;
			}
		}
		unset($this->step_data['import_data']['adjustments']);
		$this->old[0]->free_result($result);
		$this->new[0]->query("TRUNCATE ".$this->new[1]."adjustments;");
		$this->new[0]->query("INSERT INTO ".$this->new[1]."adjustments :params;", $adjs);
		unset($adjs);
		return true;
	}

	protected function dkp_check() {
		$this->current_step = 'dkp_check';
		if(!$this->step_data['import_data']['pk_mdkp']) {
			$this->connect2olddb();
			$raids = array();
			$sql = "SELECT m.raid_id, m.member_id, r.raid_value FROM ".$this->new[1]."raid_attendees m, ".$this->new[1]."raids r WHERE r.raid_id = m.raid_id;";
			$result = $this->new[0]->query($sql);
			while ( $row = $this->new[0]->fetch_record($result) ) {
				$raids[$row['member_id']][$row['raid_id']] = $row['raid_value'];
			}
			$items = array();
			$sql = "SELECT member_id, item_id, item_value FROM ".$this->new[1]."items;";
			$result = $this->new[0]->query($sql);
			while ( $row = $this->new[0]->fetch_record($result) ) {
				$items[$row['member_id']][$row['item_id']] = $row['item_value'];
			}
			$adjs = array();
			$result = $this->new[0]->query("SELECT member_id, adjustment_value, adjustment_id FROM ".$this->new[1]."adjustments;");
			while ( $row = $this->new[0]->fetch_record($result) ) {
				$adjs[$row['member_id']][$row['adjustment_id']] = $row['adjustment_value'];
			}
			$members = array();
			$result = $this->new[0]->query("SELECT member_name, member_id FROM ".$this->new[1]."members;");
			while ( $row = $this->new[0]->fetch_record($result) ) {
				$members[$row['member_id']] = $row['member_name'];
			}
			$events = array();
			$result = $this->new[0]->query("SELECT event_name, event_id FROM ".$this->new[1]."events;");
			while ( $row = $this->new[0]->fetch_record($result) ) {
				$events[$row['event_id']] = $row['event_name'];
			}
			$this->new[0]->free_result($result);
			$members_with_diff = array();
			if(is_array($this->step_data['import_data']['members'])) {
			foreach($this->step_data['import_data']['members'] as $mem_id => $mem_cur) {
				$adj_val = 0;
				$item_val = 0;
				$raid_val = 0;
				foreach($adjs[$mem_id] as $val) {
					$adj_val += $val;
				}
				foreach($raids[$mem_id] as $val) {
					$raid_val += $val;
				}
				foreach($items[$mem_id] as $val) {
					$item_val += $val;
				}
				$total = $raid_val - $item_val + $adj_val;
				if($total != $mem_cur) {
					$members_with_diff[$mem_id] = $total - $mem_cur;
				}
			}
			}
			$this->step_data['import_data']['members'] = $members_with_diff;
			if(count($members_with_diff) > 0) {
				$event_options = '';
				foreach($events as $id => $name) {
					$event_options .= "<option value='".$id."'>".$name."</option>";
				}
				$output = "<table width='60%' cellpadding='1' cellspacing='1' class='task_table colorswitch'>";
				$output .= "<tr><td colspan='3' class='th_sub'>".$this->lang['member_with_diff']."</td></tr>";
				foreach($members_with_diff as $mem_id => $diff) {
					$output .= "<tr><td width='50%'>".$members[$mem_id]."</td>";
					$output .= "<td width='25%'><input type='radio' name='dmember[".$mem_id."]' value='1' />".$this->lang['mem_diff_create_adj']." <input type='select' name='dmem_ev[".$mem_id."]'>".$event_options."</select></td>";
					$output .= "<td width='25%'><input type='radio' name='dmember[".$mem_id."]' value='0' />".$this->lang['mem_diff_ignore']."</td></tr>";
				}
				$output .= "</table>";
			}
		}
		if(!$output) {
			$output = $this->lang['no_problems'];
		}
		return $output."<input type='submit' name='dkp_check' value='".$this->lang['submit']."' class='mainoption' /></table>";
	}

	protected function parse_dkp_check() {
		if(!$this->get('dkp_check')) {
			return false;
		}
		$sql = "INSERT INTO ".$this->new[1]."adjustments (adjustment_value, adjustment_reason, member_id, adjustment_date, event_id) VALUES ";
		$sqls = array();
		$insert = false;
		if(is_array($this->get('dmember', 'string', true))) {
			foreach($this->get('dmember', 'string', true) as $mem_id => $value) {
				settype($mem_id, 'int');
				$event_id = $this->get('dmem_ev:'.$mem_id, 0);
				if($value) {
					$insert = true;
					$sqls[] = "('".$this->step_data['import_data'][$mem_id]."', '".$this->lang['mem_diff_adj_reason']."', '".$mem_id."', '".time()."', '".$event_id."')";
				}
			}
			if($insert) {
				$this->new[0]->query($sql.implode(', ', $sqls).';');
			}
		}
		return true;
	}

	protected function plugins_portal() {
		$this->current_step = 'plugins_portal';
		$this->connect2olddb();
		$new_plugins = array();
		$result = $this->new[0]->query("SELECT code FROM ".$this->new[1]."plugins WHERE status = '1';");
		while ( $row = $this->new[0]->fetch_record($result) ) {
			$new_plugins[] = $row['code'];
		}
		$this->new[0]->free_result($result);
		$plugins = array();
		$result = $this->old[0]->query("SELECT plugin_name, plugin_code, plugin_path FROM ".$this->old[1]."plugins WHERE plugin_installed = '1';");
		while ( $row = $this->old[0]->fetch_record($result) ) {
			if(is_file($this->root_path.'plugins/'.$row['plugin_path'].'/'.$row['plugin_code'].'_plugin_class.php')) {
				$plugins[] = array('name' => $row['plugin_name'], 'code' => $row['plugin_code'], 'path' => $row['plugin_path']);
			}
		}
		$output = "<style type='text/css'>
					.plugin {
						cursor: pointer;
					}
					.install {
						display: inline;
					}
					.installed {
						display: none;
					}
				</style>";
		$output .= "<script type='text/javascript'>
						function install_plugin(code) {
							document.getElementById(code+'_iframe').src = '".$this->root_path."admin/manage_extensions.php?cat=1&mode=install&code='+code;
							document.getElementById(code+'_install').className = 'installed';
							document.getElementById(code+'_installed').className = 'install';
						}
						function uninstall_plugin(code) {
							document.getElementById(code+'_iframe').src = '".$this->root_path."admin/manage_extensions.php?cat=1&mode=uninstall&code='+code;
							document.getElementById(code+'_install').className = 'install';
							document.getElementById(code+'_installed').className = 'installed';
						}
					</script>";
		$output .= "<table width='100%' cellpadding='1' cellspacing='1' class='task_table colorswitch'><tr><td colspan='2' class='th_sub'>".$this->lang['which_plugins']."</td></tr>";
		foreach($plugins as $plugin) {
			$class1 = (in_array($plugin['code'], $new_plugins)) ? 'installed' : 'install';
			$class2 = (in_array($plugin['code'], $new_plugins)) ? 'install' : 'installed';
			$output .= "<tr><td width='200px'>".$plugin['name']."</td><td>";
			$output .= "<div id='".$plugin['code']."_install' class='".$class1."'><a onclick=\"javascript:install_plugin('".$plugin['code']."')\" class='plugin'>".$this->lang['install']."</a></div>";
			$output .= "<div id='".$plugin['code']."_installed' class='".$class2."'>".$this->lang['installed']."&nbsp;<a onclick=\"javascript:uninstall_plugin('".$plugin['code']."')\" class='plugin'>".$this->lang['uninstall']."</a></div>";
			$output .= "<iframe id='".$plugin['code']."_iframe' style='display:none;' src='".$this->root_path."admin/manage_extensions.php'></iframe></td></tr>";
		}
		$portals = array();
		$result = $this->old[0]->query("SELECT path, plugin, name FROM ".$this->old[1]."portal WHERE enabled = '1';");
		while ( $row = $this->old[0]->fetch_record($result) ) {
			$path =($row['plugin']) ? 'plugins/'.$row['plugin'].'/portal/'.$row['path'].'.php' : 'portal/'.$row['path'].'/module.php';
			if(is_file($this->root_path.$path)) {
				$portals[] = array('name' => $row['name'], 'path' => $row['path']);
			}
		}
		if($portals) {
			$output .= "<tr><th colspan='2'>".$this->lang['which_portals']."</th></tr>";
		}
		foreach($portals as $portal) {
			$output .= "<tr><td colspan='2'><input type='checkbox' name='portals[".$portal['path']."]' value='".$portal['plugin']."' />".$portal['name']."</td></tr>";
		}
		$output .= "<tr><th colspan='2' class='th_sub'><input type='submit' name='plugins_portal' value='".$this->lang['submit']."' class='mainoption' /></th></tr></table>";
		return $output;
	}

	protected function parse_plugins_portal() {
		if(!$this->get('plugins_portal')) {
			return false;
		}
		$this->connect2olddb();
		//get installed plugins
		$result = $this->new[0]->query("SELECT code FROM ".$this->new[1]."plugins WHERE status = '1';");
		$pluggs = array();
		while($row = $this->new[0]->fetch_row($result)) {
			$pluggs[] = $row['code'];
		}
		include_once($this->root_path.'maintenance/includes/tasks/import06/plugin_list.php');
		$tables = array();
		foreach($plugin_names as $code => $info) {
			if(!in_array($code, $pluggs)) continue;
			if($info['extra_tables']) {
				foreach($info['extra_tables'] as $table_name) {
					if(!in_array($table_name, $tables)) {
						$tables[] = $table_name;
					}
				}
			}
		}
		foreach($tables as $table_name) {
			$this->table_by_name($table_name);
		}
		foreach($plugin_names as $code => $info) {
			if(!in_array($code, $pluggs)) continue;
			$version = $this->old[0]->query_first("SELECT config_value FROM ".$this->old[1].$info['table']." WHERE config_name = '".$info['fieldprefix']."inst_version';");
			$this->new[0]->query("UPDATE ".$this->new[1]."plugins SET version = '".$version."' WHERE code = '".$code."';");
		}
		if(is_array($this->get('portals', 'string', true))) {
			$tables = array();
			foreach($this->get('portals', 'string', true) as $code => $plugin) {
				$path = $this->root_path.(($plugin != '') ? 'plugins/'.$plugin.'/portal/'.$code.'.php' : 'portal/'.$code.'/module.php');
				if(is_file($path)) {
					include($path);
					if($portal_module[$code]['custom_tables']) {
						foreach($portal_module[$code]['custom_tables'] as $table) {
							$tables[] = $table;
						}
					}
				}
			}
			foreach($tables as $table) {
				$this->table_by_name($table);
			}
		}
		return true;
	}

	protected function step_end() {
		registry::register('datacache')->flush();
		return ($this->get('no_import')) ? $this->lang['nothing_imported'] : $this->lang['import_end'];
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_import06', import06::__shortcuts());
?>