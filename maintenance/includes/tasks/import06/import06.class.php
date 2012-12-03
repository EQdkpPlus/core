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

if ( !class_exists( "task" ) ) {
  require_once($eqdkp_root_path . 'maintenance/includes/task.aclass.php');
}

class import06 extends task
{
    public $author = 'hoofy_leon';
    public $version = '1.0.0';
	public $form_method = 'POST';
	public $name = 'Import 0.6';
	public $type = 'import';

    protected $old_db_data = array(false);
	protected $old = array(false, '');  //Holds old DB
	protected $new = array(false, '');  //Holds new DB

    protected $path = '';

    public function __construct()
    {
    	global $eqdkp_root_path;
    	$this->path = $eqdkp_root_path;
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
		global $db, $table_prefix, $dbname;
		$this->new[0] = $db;
		$this->new[1] = $table_prefix;
		$this->new[2] = $dbname;
	}

    public function destruct()
    {
    	global $core;
    	unset($this->old);
    	unset($this->new);
    }

    protected function connect2olddb()
    {
    	global $sql_db;
    	if(!is_object($this->old[0])) {
    		if($this->step_data['old_db_data'][0] === true) {
    			$this->old[0] = new $sql_db();
    			$this->old[0]->sql_connect($this->step_data['old_db_data']['host'], $this->step_data['old_db_data']['name'], $this->step_data['old_db_data']['user'], $this->step_data['old_db_data']['pass']);
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

    protected function get($key, $default='', $array=false)
    {
    	global $in;
    	return ($array) ? $in->getArray($key, $default) : $in->get($key, $default);
    }

    protected function js_mark_boxes($num, $id_prefix)
    {
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

    protected function select_field($name, $data)
    {
    	$retu = "<select name='".$name."'><option value='0'>".$this->lang['no_import']."</option>";
    	foreach($data as $id => $value) {
    		$retu .= "<option value='".$id."'>".$value."</option>";
    	}
    	return $retu."</select>";
    }

    protected function table_by_name($table_name)
    {
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
		    foreach($data as $row) {
		    	$sqls[] = "('".implode("', '", $row)."')";
    		}
			return $this->new[0]->query($sql.implode(', ',$sqls).";");
    	}
		return true;
	}

	protected function first_step()
	{
		global $core;
		$this->current_step = 'first';
		$output = '<table cellpadding="1" cellspacing="1" align="center" class="task_table" width="60%" >';
		$output .= '	<tr>';
		$output .= '	  <th width="40px" class="th_sub">&nbsp;</th><th colspan="2" class="th_sub">'.$this->lang['import_steps'].'</th>';
		$output .= '	</tr>';
		foreach($this->step_order as $ikey => $value) {
			if($ikey != 0) {
				$output .= '    <tr class="'.$core->switch_row_class().'"><td colspan="3"><label><input type="checkbox" name="import_steps[]" value="'.$ikey.'" checked="checked">'.$this->lang[$value].'</label></td>    </tr>';
			}
		}
		$output .= '<tr><th colspan="3" class="th_sub">'.$this->lang['database_info'].'</th></tr>';
		$output .= '<tr class="'.$core->switch_row_class().'"><td colspan="2">'.$this->lang['table_prefix'].'</td><td><input type="text" name="table_prefix" value="eqdkp_" /></td></tr>';
		$output .= '<tr class="'.$core->switch_row_class().'"><td colspan="2">'.$this->lang['database_other'].'</td><td width="50%"><input type="checkbox" name="db_else" value="1" /></td></tr>';
		$output .= '<tr class="'.$core->switch_row_class().'"><td colspan="2">'.$this->lang['host'].'</td><td><input type="text" name="db_host" value="localhost" /></td></tr>';
		$output .= '<tr class="'.$core->switch_row_class().'"><td colspan="2">'.$this->lang['db_name'].'</td><td><input type="text" name="db_name" value="" /></td></tr>';
		$output .= '<tr class="'.$core->switch_row_class().'"><td colspan="2">'.$this->lang['user'].'</td><td><input type="text" name="db_user" value="" /></td></tr>';
		$output .= '<tr class="'.$core->switch_row_class().'"><td colspan="2">'.$this->lang['password'].'</td><td><input type="password" name="db_pass" value="" /></td></tr>';
		$output .= '<tr><th colspan="3" class="th_sub"><input type="submit" name="first" value="'.$this->lang['submit'].'" class="mainoption"/><input type="submit" name="no_import" value="'.$this->lang['dont_import'].'" class="mainoption"/></th></tr></table>';
		return $output;
	}

	protected function parse_first_step()
	{
		global $pdl;
		if(!$_POST['first'] AND !$_POST['no_import']) {
			$pdl->log('maintenance', 'Nothing to do.');
			return false;
		}
		if($_POST['no_import']) {
			return true;
		}
		if($_POST['db_else']) {
			$this->step_data['old_db_data'] = array(true, 'host' => $this->get('db_host'), 'name' => $this->get('db_name'), 'user' => $this->get('db_user'), 'pass' => $this->get('db_pass'), 'prefix' => $this->get('table_prefix'));
		} else {
			if(!$this->get('table_prefix')) {
				$pdl->log('maintenance', 'No table_prefix specified.');
				return false;
			}
			$this->step_data['old_db_data'] = array(false, 'prefix' => $this->get('table_prefix'));
		}
		$this->steps = $this->get('import_steps', 'int', true);
		if($this->connect2olddb()) {
			return true;
		} else {
			$pdl->log('maintenance', 'Could not connect to database, from which data shall be imported.');
			return false;
		}
	}

	protected function get_step($step) {
		return $this->$step();
	}

	protected function parse_step($step) {
		return $this->{'parse_'.$step}();
	}

	protected function users_auths()
	{
		global $core;
		$this->current_step = 'users_auths';
        $output = '<table width="60%" cellpadding="1" cellspacing="1" align="center" class="task_table">';
        $output .= '<tr><td colspan="3" class="row2">'.$this->lang['your_user'].'{USER_SELECT}</td></tr>';
        $output .= '<tr><th colspan="3" class="th_sub">'.$this->lang['which_users'].' '.$this->js_mark_boxes('{NUM}', 'u_').'</th></tr>';
        $output .= '<tr><td colspan="3" align="center">
				<table width="100%" border="0" cellspacing="0" cellpadding="2" class="errortable">
				<tr>
					<td width="50" align="left" class="row1"><img src="../images/false.png" alt="" height="35"></td>
					<td width="100%" align="center" class="row1"><strong>'.$this->lang['notice_admin_perm'].'</strong>
						</td>
				</tr>
				</table>	
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
		foreach($users as $user) {
			$u_sel .= "<option value='".$user['id']."'>".$user['name']."</option>";
		}
		$output = str_replace('{USER_SELECT}', $u_sel, $output);

		$max = count($users);
        $output = str_replace('{NUM}', $max, $output);
		$rows = $max/3 +1;
		settype($rows,'int');
		$done = array();
		function get_output($num, $users, $lang_admin, $colspan='') {
			$out = "<td width='33%'".$colspan."><input type='checkbox' name='user_id[]' id='u_".$num."' value='".$users[$num]['id']."' />";
			$checked = ($users[$num]['admin']) ? "checked='checked' " : '';
			$out .= $users[$num]['name']." (<input type='checkbox' name='admin[".$users[$num]['id']."]' value='1' ".$checked."> ".$lang_admin.")</td>";
			return $out;
		}
		for($i=0; $i<$rows; $i++) {
			$one = false;
			$two = false;
			$output .= "<tr class='".$core->switch_row_class()."'>";
			if(!in_array($i, $done)) {
				$output .= get_output($i, $users, $this->lang['admin']);
				$one = true;
			}
			if(!in_array(($i+$rows), $done) AND $max > 1) {
				$output .= get_output($i+$rows, $users, $this->lang['admin']);
				$two = true;
			}
			if(!in_array(($i+2*$rows), $done) AND $max > 2) {
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

	protected function parse_users_auths()
	{
		global $user, $core;
		if(!$_POST['users_auths']) {
			return false;
		}
		if($_POST['user_id']) {
			$users = array();
			$this->connect2olddb();
			if($this->get('your_user', 0)) {
				$this->old_db_data['replace_users'][$this->get('your_user')] = $user->data['user_id'];
			}
			$users2import = $this->get('user_id', 'int', true);
			if(!in_array($this->get('your_user'), $users2import) AND $this->get('your_user')) {
				array_push($users2import, $this->get('your_user'));
			}
			//need room for our current user
			if(in_array($user->data['user_id'], $users2import) AND $user->data['user_id'] != $this->get('your_user')) {
				$id = $user->data['user_id'];
				while(in_array($id, $users2import)) {
					$id++;
				}
			}
			$this->old_db_data['replace_users'][$user->data['user_id']] = $id;
			if($key) unset($users2import[$key]);
			$sql = "SELECT * FROM ".$this->old[1]."users WHERE user_id IN (".implode(',',$users2import).");";
			$result = $this->old[0]->query($sql);
			while ( $row = $this->old[0]->fetch_record($result) ) {
				if($row['user_id'] == $user->data['user_id'] AND $user->data['user_id'] != $this->get('your_user')) {
					$row['user_id'] = $id;
                }
                if($row['user_id'] != $this->get('your_user')) {
					foreach($row as $field => $value) {
						if($field == 'user_style') {
							$users[$row['user_id']][$field] = $this->new[0]->escape($core->config['default_style']);
						} else {
							$users[$row['user_id']][$field] = $this->new[0]->escape($value);
						}
					}
				}
			}
			$rules = 0;
			$this->old[0]->free_result($result);
			$result = $this->new[0]->query("SELECT * FROM ".$this->new[1]."users WHERE user_id = '".$user->data['user_id']."';");
			while ( $row = $this->new[0]->fetch_record($result) ) {
				foreach($row as $field => $value) {
					if(in_array($field, array_keys(current($users)))) {
						$users[$row['user_id']][$field] = $this->new[0]->escape($value);
					}
					if($field == 'rules') {
						$rules = $value;
					}
				}
			}
			$this->new[0]->free_result($result);
			$this->new[0]->query("TRUNCATE ".$this->new[1]."users;");
			$fields = '`'.implode('`, `', array_keys(current($users))).'`';
			$sql = "INSERT INTO ".$this->new[1]."users (".$fields.") VALUES ";
			$sqls = array();
			foreach($users as $suser) {
				$sqls[] = "('".implode("', '", $suser)."')";
			}
			$this->new[0]->query($sql.implode(', ', $sqls).';');
			
			if($rules) {
				$this->new[0]->query("UPDATE ".$this->new[1]."users SET rules = '".$this->new[0]->escape($value)."' WHERE user_id = '".$user->data['user_id']."';");
			}
			
			//user-permissions
			$sql = "INSERT INTO ".$this->new[1]."groups_users (`user_id`, `group_id`) VALUES ";
			$sqls = array();
			foreach($users as $user_id => $data) {
				if($user_id != $user->data['user_id']) {
					$sqls[] = "('".$user_id."', '".(($_POST['admin'][$user_id]) ? '3' : '4')."')";
				}
			}
			$this->new[0]->query($sql.implode(', ', $sqls).';');
		}
		return true;
	}

	protected function config_news_log()
	{
		$this->current_step = 'config_news_log';
		$output = '<table width="60%" cellpadding="1" cellspacing="1" align="center" class="task_table">';
		$output .= '<tr><td colspan="2" class="th_sub">'.$this->lang['import'].'</th><th>'.$this->lang['older_than'].'</td></tr>';
		$output .= '<tr><td width="50%">'.$this->lang['config'].'</td><td width="40"><input type="checkbox" name="config" value="1" checked="checked" /></td><td> ---- </td></tr>';
		$output .= '<tr><td>'.$this->lang['news'].'</td><td><input type="checkbox" name="news" value="1" checked="checked" /></td><td><input type="text" name="news_date" value="0" />'.$this->lang['enter_date_format'].'</td></tr>';
		$output .= '<tr><td>'.$this->lang['log'].'</td><td><input type="checkbox" name="log" value="1" checked="checked" /></td><td><input type="text" name="logs_date" value="0" />'.$this->lang['enter_date_format'].'</td></tr>';
		$output .= '<tr><th colspan="3" class="th_sub"><input type="submit" name="config_news_log_styles" value="'.$this->lang['submit'].'" class="mainoption"/></th></tr></table>';
		return $output;
	}

	protected function parse_config_news_log()
	{
		global $user, $core;
		if(!$_POST['config_news_log_styles']) {
			return false;
		}
		$this->connect2olddb();
		if($_POST['config']) {
			$configs = array();
			$ignore = array('cookie_domain', 'cookie_name', 'cookie_path', 'plus_version', 'server_name', 'server_path', 'server_port', 'session_cleanup', 'session_last_cleanup', 'default_style');
			$sql = "SELECT * FROM ".$this->old[1]."plus_config;";
			$result = $this->old[0]->query($sql);
			while ( $row = $this->old[0]->fetch_record($result) ) {
				if(!in_array($row['config_name'], $ignore) AND in_array($row['config_name'], array_keys($core->config))) {
					$configs[$row['config_name']] = $row['config_value'];
				}
			}
			$new_games = array('Aion' => 'aion', 'AoC' => 'aoc', 'Atlantica' => 'atlantica', 'DAoC' => 'daoc', 'Everquest' => 'eq', 'Everquest2' => 'eq2', 'ffxi' => 'ffxi', 'LOTRO' => 'lotro', 'RunesOfMagic', 'rom', 'shakesfidget' => 'shakesfidget', 'TR' => 'tr', 'Vanguard-SoH' => 'vanguard', 'Warhammer' => 'warhammer', 'WoW' => 'wow');
			$sql = "SELECT * FROM ".$this->old[1]."config;";
			$result = $this->old[0]->query($sql);
			while ( $row = $this->old[0]->fetch_record($result) ) {
				if(!in_array($row['config_name'], $ignore) AND in_array($row['config_name'], array_keys($core->config))) {
					$configs[$row['config_name']] = $row['config_value'];
				}
				if($row['config_name'] == 'default_game') {
					$configs['default_game'] = $new_games[$row['config_value']];
				}
			}
            $this->step_data['import_data']['pk_mdkp'] = $configs['pk_multidkp'];
            unset($configs['pk_multidkp']);
			$core->config_set($configs);
			unset($configs);
		}
		if($_POST['news']) {
			$news = array();
			list($d,$m,$y) = explode('.',$this->get('news_date', '0.0.0'));
			$date = mktime(0,0,0,$m,$d,$y);
			$sql = "SELECT * FROM ".$this->old[1]."news WHERE news_date > ".$date.";";
			$result = $this->old[0]->query($sql);
			if($result);
			while ( $row = $this->old[0]->fetch_record($result) ) {
				foreach($row as $field => $value) {
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
				if($this->old_db_data['replace_user'][$newss['user_id']]) {
					$newss['user_id'] = $this->old_db_data['replace_user'][$newss['user_id']];
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
			$sql = "INSERT INTO ".$this->new[1]."comments (`".implode('`, `', array_keys(current($comments)))."`) VALUES ";
			$sqls = array();
			foreach($comments as $comment) {
				$sqls[] = "('".implode("', '", $comment)."')";
			}
			$this->new[0]->query($sql.implode(', ', $sqls).';');
			unset($comments);
		}
		if($_POST['log']) {
			$logs = array();
			list($d,$m,$y) = explode('.',$this->get('logs_date', '0.0.0'));
			$date = mktime(0,0,0,$m,$d,$y);
			$sql = "SELECT * FROM ".$this->old[1]."logs WHERE log_date > ".$date." LIMIT 1000;";
			$result = $this->old[0]->query($sql);
			while ( $row = $this->old[0]->fetch_record($result) ) {
				if($this->old_db_data['replace_user'][$row['admin_id']]) {
					$row['admin_id'] = $this->old_db_data['replace_user'][$row['admin_id']];
				}
				$logs[$row['log_id']]['log_date'] = $row['log_date'];
				$logs[$row['log_id']]['log_tag'] = $row['log_type'];
				$logs[$row['log_id']]['log_action'] = $row['log_action'];
				$logs[$row['log_id']]['log_ipaddress'] = $row['log_ipaddress'];
				$logs[$row['log_id']]['log_sid'] = $row['log_sid'];
				$logs[$row['log_id']]['log_result'] = $row['log_result'];
				$logs[$row['log_id']]['admin_id'] = $row['admin_id'];
			}
			$this->old[0]->free_result($result);
			$this->new[0]->query("TRUNCATE ".$this->new[1]."logs;");
			$sql = "INSERT INTO ".$this->new[1]."logs (log_id, log_date, log_tag, log_action, log_ipaddress, log_sid, log_result, admin_id) VALUES ";
			$sqls = array();
			foreach($logs as $id => $log) {
				$sqls[] = "('".$this->new[0]->escape($id)."', '".$this->new[0]->escape($log['log_date'])."', '".$this->new[0]->escape($log['log_tag'])."', '".$this->new[0]->escape($log['log_action'])."', '".$this->new[0]->escape($log['log_ipaddress'])."', '".$this->new[0]->escape($log['log_sid'])."', '".$this->new[0]->escape($log['log_result'])."', '".$this->new[0]->escape($log['admin_id'])."')";
			}
			$sql .= implode(', ', $sqls).';';
			$this->new[0]->query($sql);
			unset($logs);
		}
		return true;
	}
	
	protected function styles()
	{
		$this->current_step = 'styles';
	}
	
	protected function parse_styles()
	{
		if($_POST['styles']) {
			$styles = array();
			$style_configs = array();

			$sql = "SELECT `style_id`, `style_name`, `template_path`, `body_background`, `body_link`, `body_link_style`, `body_hlink`, `body_hlink_style`, `header_link`, `header_link_style`, `header_hlink`, `header_hlink_style`, `tr_color1`, `tr_color2`, `th_color1`, `fontface1`, `fontface2`, `fontface3`, `fontsize1`, `fontsize2`, `fontsize3`, `fontcolor1`, `fontcolor2`, `fontcolor3`, `fontcolor_neg`, `fontcolor_pos`, `table_border_width`, `table_border_color`, `table_border_style`, `input_color`, `input_border_width`, `input_border_color`, `input_border_style` FROM ".$this->old[1]."styles;";
			$result = $this->old[0]->query($sql);
			while ( $row = $this->old[0]->fetch_record($result) ) {
				foreach($row as $field => $value) {
					$styles[$row['style_id']][$field] = $value;
				}
			}
			$this->old[0]->free_result($result);

			$sql = "SELECT * FROM ".$this->old[1]."style_config;";
			$result = $this->old[0]->query($sql);
			while ( $row = $this->old[0]->fetch_record($result) ) {
				foreach($row as $field => $value) {
					$style_configs[$row['style_id']][$field] = $this->new[0]->escape($value);
				}
			}
			$this->old[0]->free_result($result);
			$this->new[0]->query("TRUNCATE ".$this->new[1]."styles;");
			$style_fields = "`".implode('`, `', array_keys(current($styles)))."`";
			$sql = "INSERT INTO ".$this->new[1]."styles (".$style_fields.") VALUES ";
			$sqls = array();
			foreach($styles as $style) {
				$sqls[] = "('".implode("', '", $style)."')";
			}
			$this->new[0]->query($sql.implode(', ', $sqls).';');
			unset($styles);
		}
	}

	protected function events()
	{
		global $core;
		$this->current_step = 'events';
        $output = '<table width="60%" cellpadding="1" cellspacing="1" align="center" class="task_table">';
        $output .= '<tr><td colspan="3" class="th_sub">'.$this->lang['which_events'].' '.$this->js_mark_boxes('{NUM}', 'e_').'</td></tr>';
        //build eventlist
        $events = array();
		$this->connect2olddb();
		$result = $this->old[0]->query("SELECT event_name, event_id FROM ".$this->old[1]."events ORDER BY event_name DESC;");
		$c = 0;
		while ( $row = $this->old[0]->fetch_Record($result) ) {
			$events[$c]['id'] = $row['event_id'];
			$events[$c]['name'] = $row['event_name'];
			$c++;
		}

		$max = count($events);
        $output = str_replace('{NUM}', $max, $output);
		$rows = $max/3;
		settype($rows,'int');
		$done = array();
		for($i=0; $i<$rows; $i++) {
			$one = false;
			$two = false;
			$output .= "<tr class='".$core->switch_row_class()."'>";
			if(!in_array($i, $done)) {
				$output .= "<td width='33%'><input type='checkbox' name='event_id[]' id='e_".$i."' value='".$events[$i]['id']."' />".$events[$i]['name']."</td>";
				$one = true;
			}
			if(!in_array(($i+$rows), $done)) {
				$output .= "<td width='33%'><input type='checkbox' name='event_id[]' id='e_".($i+$rows)."' value='".$events[$i+$rows]['id']."' />".$events[$i+$rows]['name']."</td>";
				$two = true;
			}
			if(!in_array(($i+2*$rows), $done)) {
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

	protected function parse_events()
	{
		if(!$_POST['events']) {
			return false;
		}
		if($_POST['event_id']) {
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

    protected function multidkp()
    {
    	global $core;
			
			$this->current_step = 'multidkp';
    	$this->connect2olddb();
    	$sql = "SELECT multidkp_id, multidkp_name FROM ".$this->old[1]."multidkp ORDER BY multidkp_name DESC;";
    	$result = $this->old[0]->query($sql);
    	$multis = array();
    	while ( $row = $this->old[0]->fetch_record($result) ) {
    		$multis[$row['multidkp_id']-1]['name'] = $row['multidkp_name'];
    		$multis[$row['multidkp_id']-1]['id'] = $row['multidkp_id'];
    	}
        $output = '<table width="60%" cellpadding="1" cellspacing="1" align="center" class="task_table">';
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
			$rows = ($rows < 1) ? 1 : $rows;
			$done = array();
			for($i=0; $i<=$rows; $i++) {
				$one = false;
				$two = false;
				$output .= "<tr class='".$core->switch_row_class()."'>";
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

    protected function parse_multidkp()
    {
    	if(!$_POST['multidkp']) {
    		return false;
    	}
    	if($_POST['multi_id']) {
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
    		$sql = "INSERT INTO ".$this->new[1]."multidkp2event (multidkp2event_id, multidkp2event_multi_id, multidkp2event_event_id) VALUES ";
    		$sqls = array();
    		foreach($multi2ev as $m2e_id => $mu2ev) {
    			$sqls[] = "('".$this->new[0]->escape($m2e_id)."', '".$this->new[0]->escape($mu2ev['multi_id'])."', '".$this->new[0]->escape($mu2ev['event_id'])."')";
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
            $this->new[0]->query($sql1.implode(', ', $sqls1).';');
            $this->new[0]->query($sql2.implode(', ', $sqls2),';');
    		unset($multis);
    	} else {
    		$this->new[0]->query("TRUNCATE ".$this->new[1]."multidkp;");
    		$this->new[0]->query("INSERT INTO ".$this->new[1]."multidkp (multidkp_name, multidkp_desc) VALUES ('".$this->get('multi_name', 'default')."', '".$this->get('multi_desc', 'default')."');");
    		$id = $this->new[0]->sql_lastid();
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

	protected function members()
	{
		global $core;
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

        $output = '<table width="60%" cellpadding="1" cellspacing="1" align="center" class="task_table">';
        $output .= '<tr><td colspan="3" class="th_sub">'.$this->lang['which_members'].' '.$this->js_mark_boxes('{NUM}', 'm_').'</td></tr>';
		$max = count($members);
        $output = str_replace('{NUM}', $max, $output);
		$rows = $max/3;
		settype($rows,'int');
		$done = array();
		for($i=0; $i<$rows; $i++) {
			$one = false;
			$two = false;
			$output .= "<tr class='".$core->switch_row_class()."'>";
			if(!in_array($i, $done)) {
				$output .= "<td width='33%'><input type='checkbox' name='member_id[]' id='m_".$i."' value='".$members[$i]['id']."' />".$members[$i]['name']."</td>";
				$one = true;
			}
			if(!in_array(($i+$rows), $done)) {
				$output .= "<td width='33%'><input type='checkbox' name='member_id[]' id='m_".($i+$rows)."' value='".$members[$i+$rows]['id']."' />".$members[$i+$rows]['name']."</td>";
				$two = true;
			}
			if(!in_array(($i+2*$rows), $done)) {
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

	protected function parse_members()
	{
		global $core;
		if(!$_POST['members']) {
			return false;
		}
		if($_POST['ranks']) {
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
		if($_POST['member_id']) {
			$members = array();
			$m2cr = array();
			$this->connect2olddb();
			$members2import = $this->get('member_id', 'int', true);
			$sql = "SELECT m.*, r.race_name, c.class_name FROM ".$this->old[1]."members m, ".$this->old[1]."races r, ".$this->old[1]."classes c WHERE r.race_id = m.member_race_id AND c.class_id = m.member_class_id AND member_id IN (".implode(',',$members2import).");";
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
				$m2cr[$row['member_id']]['class'] = $row['class_name'];
				$m2cr[$row['member_id']]['race'] = $row['race_name'];
			}
			$this->old[0]->free_result($result);
			$this->new[0]->query("TRUNCATE ".$this->new[1]."members;");
			$fields = "`".implode('`, `', array_keys(current($members)))."`";
			$sql = "INSERT INTO ".$this->new[1]."members (".$fields.") VALUES ";
			$sqls = array();
            include_once($this->path.'core/game.class.php');
            $game = new Game();
			foreach($members as $member) {
				$member['member_race_id'] = $game->get_id('races', $m2cr[$member['member_id']]['race']);
				$member['member_class_id'] = $game->get_id('classes', $m2cr[$member['member_id']]['class']);
				$sqls[] = "('".implode("', '", $member)."')";
			}
            unset($game);
			$this->new[0]->query($sql.implode(', ', $sqls).';');

			$members = array();
			$sql = "SELECT * FROM ".$this->old[1]."member_user WHERE member_id IN (".implode(',',$members2import).");";
			$result = $this->old[0]->query($sql);
			while ( $row = $this->old[0]->fetch_record($result) ) {
				$members[$row['member_id']] = ($this->old_db_data['replace_user'][$row['user_id']]) ? $this->old_db_data['replace_user'][$row['user_id']] : $row['user_id'];
			}
			$this->old[0]->free_result($result);
			$user_ids = array();
			$result = $this->new[0]->query("SELECT user_id FROM ".$this->new[1]."users;");
			while ( $row = $this->new[0]->fetch_record($result) ) {
				$user_ids[] = $row['user_id'];
			}
			$this->new[0]->free_result($result);
			$this->new[0]->query("TRUNCATE ".$this->new[1]."member_user;");
			$sql = "INSERT INTO ".$this->new[1]."member_user (member_id, user_id) VALUES ";
			$sqls = array();
			foreach($members as $mid => $uid) {
				if(in_array($uid, $user_ids)) {
					$sqls[] = "('".$mid."', '".$uid."')";
				}
			}
			$this->new[0]->query($sql.implode(', ', $sqls).';');
			unset($members);
		}
		if($_POST['special_members']) {
			$specials = $this->get('special_members', 'string', true);
			$ids = array();
			foreach($specials as $special_name) {
				if($special_name) {
					$this->new[0]->query("INSERT INTO ".$this->new[1]."members (member_name) VALUES ('".$special_name."');");
					$ids[] = $this->new[0]->insert_id();
				}
			}
			$core->config_set('special_members', serialize($ids));
		}
		return true;
	}

	protected function raids()
	{
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
        $output = '<table width="60%" cellpadding="1" cellspacing="1" align="center" class="task_table">';
        if(count($wrong_raids) > 0) {
        	$output .= "<tr><td colspan='2'>".$this->lang['raids_with_no_event']."</td></tr>";
        	foreach($wrong_raids as $raid_id => $name) {
        		$output .= "<tr><td width='50%'>".$this->lang['raid_id'].": ".$raid_id.", ".$this->lang['event_name'].": ".$name."</td>";
        		$output .= "<td>".$this->select_field('raid_event['.$raid_id.']', $events)."</td></tr>";
        	}
        } else {
        	$output .= "<tr><td width='100%'>".$this->lang['no_problems']."</td></tr>";
        }
		$output .= "<tr><th colspan='2' class='th_sub'><input type='submit' name='raids' value='".$this->lang['submit']."' class='mainoption'/></th></tr></table>";
		return $output;
	}

	protected function parse_raids()
	{
		if(!$_POST['raids']) {
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
				$raids[$row['raid_id']]['event_id'] = $_POST['raid_event'][$row['raid_id']];
			}
		}
		$this->old[0]->free_result($result);
		unset($this->step_data['import_data']['raids']);
		$fields = "`".implode('`, `', array_keys(current($raids)))."`";
        $this->new[0]->query("TRUNCATE ".$this->new[1]."raids;");
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

	protected function items()
	{
		global $core;
		$this->current_step = 'items';
		$items = array();
		$this->connect2olddb();
		$result = $this->old[0]->query("SELECT item_id, item_name, item_buyer, raid_id, item_date FROM ".$this->old[1]."items;");
		while ( $row = $this->old[0]->fetch_record($result) ) {
			$items[$row['item_id']]['name'] = $row['item_name'];
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
		function array_isearch($needle, $array) {
			foreach($array as $key => $val) {
				if(strlen($val) == strlen($needle) AND stripos($val, $needle) === 0) {
					return $key;
				}
			}
			return false;
		}
		foreach($items as $item_id => $item) {
			$key = array_isearch($item['buyer'], $members);
			if(!$key OR !$item['buyer']) {
				$no_member_items[$item_id] = $item;
			}
			if(!in_array($item['raid'], $raid_ids)) {
				$no_raid_items[$item_id] = $item;
			}
		}
        $output = '<table width="60%" cellpadding="1" cellspacing="1" align="center" class="task_table">';
		if(count($no_raid_items) == 0 AND count($no_member_items) == 0) {
            $output .= "<tr><td width='100%' class='th_sub'>".$this->lang['no_problems']."</td></tr>";
        } else {
        	if(count($no_raid_items) > 0) {
            	$output .= "<tr><td colspan='2' class='th_sub'>".$this->lang['items_without_raid']."</td></tr>";
            	foreach($no_raid_items as $item_id => $item) {
            		$output .= "<tr class='".$core->switch_row_class()."'><td width='50%'>".date($this->lang['date_format'], $item['date']).' '.$item['name'].' -> '.$item['buyer']."</td>";
            		$output .= "<td>".$this->select_field('item_raid['.$item_id.']', $raids)."</td></tr>";
            	}
        	}
        	if(count($no_member_items) > 0) {
            	$output .= "<tr><td colspan='2' class='th_sub'>".$this->lang['items_without_member']."</td></tr>";
            	foreach($no_member_items as $item_id => $item) {
            		$output .= "<tr class='".$core->switch_row_class()."'><td width='50%'>".date($this->lang['date_format'], $item['date']).' '.$item['name'].' -> '.$item['buyer']."</td>";
            		$output .= "<td>".$this->select_field('item_member['.$item_id.']', $members)."</td></tr>";
            	}
            }
        }
		$output .= "<tr><th colspan='2' class='th_sub'><input type='submit' name='items' value='".$this->lang['submit']."' class='mainoption' /></th></tr></table>";
		return $output;

	}

	protected function parse_items()
	{
		if(!$_POST['items']) {
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
		$member_ids = array();
		$result = $this->new[0]->query("SELECT member_id, member_name FROM ".$this->new[1]."members;");
		while ( $row = $this->new[0]->fetch_record($result) ) {
			$member_ids[$row['member_name']] = $row['member_id'];
		}
		$this->old[0]->free_result($result);
		$result = $this->old[0]->query("SELECT * FROM ".$this->old[1]."items;");
		while ( $row = $this->old[0]->fetch_record($result) ) {
           	if(in_array($row['item_id'], $_POST['item_raid'])) {
               	$row['raid_id'] = $_POST['item_raid'][$row['item_id']];
               	settype($row['raid_id'], 'int');
            }
            if(in_array($row['item_id'], array_keys($_POST['item_member']))) {
               	$row['member_id'] = $_POST['item_member'][$row['item_id']];
            } else {
            	$row['member_id'] = $member_ids[$row['item_buyer']];
            }
            settype($row['member_id'], 'int');
            unset($row['item_buyer']);
            unset($row['item_added_by']);
            unset($row['item_updated_by']);
            unset($row['item_ctrt_wowitemid']);
			foreach($row as $field => $value) {
				$items[$row['item_id']][$field] = $this->new[0]->escape($value);
			}
		}
		unset($this->step_data['import_data']['items']);
		$this->old[0]->free_result($result);
		$fields = "`".implode('`, `', array_keys(current($items)))."`, `itempool_id`";
		$this->new[0]->query("TRUNCATE ".$this->new[1]."items;");
		$sql = "INSERT INTO ".$this->new[1]."items (".$fields.") VALUES ";
		$sqls = array();
		foreach($items as $item) {
			$sqls[] = "('".implode("', '", $item)."', '".$raid2itempool[$item['raid_id']]."')";
		}
		$this->new[0]->query($sql.implode(', ', $sqls).';');
		unset($items);
		return true;
	}

	protected function adjustments()
	{
		global $core;
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
        $output = '<table width="60%" cellpadding="1" cellspacing="1" align="center" class="task_table">';
		if(count($wrong_member_adj) == 0 AND count($wrong_event_adj) == 0) {
            $output .= "<tr><td width='100%' class='th_sub'>".$this->lang['no_problems']."</td></tr>";
        } else {
        	if(count($wrong_event_adj) > 0) {
            	$output .= "<tr><td colspan='2' class='th_sub'>".$this->lang['adjs_without_event']."</td></tr>";
            	foreach($wrong_event_adj as $adj_id => $adj) {
            		$output .= "<tr class='".$core->switch_row_class()."'><td width='50%'>".date($this->lang['date_format'], $adj['date']).' '.$adj['event'].': '.$adj['member']." (".$adj['reason'].")</td>";
            		$output .= "<td>".$this->select_field('adj_event['.$adj_id.']', $events)."</td></tr>";
            	}
        	}
        	if(count($wrong_member_adj) > 0) {
            	$output .= "<tr><td colspan='2' class='th_sub'>".$this->lang['adjs_without_member']."</td></tr>";
            	foreach($wrong_member_adj as $adj_id => $adj) {
            		$output .= "<tr class='".$core->switch_row_class()."'><td width='50%'>".date($this->lang['date_format'], $adj['date']).' '.$adj['event'].': '.$adj['member']." (".$adj['reason'].")</td>";
            		$output .= "<td>".$this->select_field('adj_member['.$adj_id.']', $members)."</td></tr>";
            	}
            }
        }
		$output .= "<tr><th colspan='2' class='th_sub'><input type='submit' name='adjustments' value='".$this->lang['submit']."' class='mainoption' /></th></tr></table>";
		return $output;
	}

	protected function parse_adjustments()
	{
		if(!$_POST['adjustments']) {
			return false;
		}
		$adjs = array();
		$this->connect2olddb();
		$result = $this->old[0]->query("SELECT * FROM ".$this->old[1]."adjustments;");
		while ( $row = $this->old[0]->fetch_record($result) ) {
           	if(in_array($row['adjustment_id'], $_POST['adj_event'])) {
               	$row['event_id'] = $_POST['adj_event'][$row['adjustment_id']];
            } else {
            	$row['event_id'] = $this->step_data['import_data']['adjustments'][$row['adjustment_id']]['event_id'];
            }
            settype($row['event_id'], 'int');
            if(in_array($row['adjustment_id'], $_POST['adj_member'])) {
               	$row['member_id'] = $_POST['adj_member'][$row['adjustment_id']];
            } else {
            	$row['member_id'] = $this->step_data['import_data']['adjustments'][$row['adjustment_id']]['member_id'];
            }
            settype($row['member_id'], 'int');
            unset($row['member_name']);
            unset($row['raid_name']);
            unset($row['adjustment_added_by']);
            unset($row['adjustment_updated_by']);
			foreach($row as $field => $value) {
				$adjs[$row['adjustment_id']][$field] = $this->new[0]->escape($value);
			}
		}
		unset($this->step_data['import_data']['adjustments']);
		$this->old[0]->free_result($result);
		$this->new[0]->query("TRUNCATE ".$this->new[1]."adjustments;");
		$sql = "INSERT INTO ".$this->new[1]."adjustments (`".implode('`, `', array_keys(current($adjs)))."`) VALUES ";
		$sqls = array();
		foreach($adjs as $adj) {
			$sqls[] = "('".implode("', '", $adj)."')";
		}
		$this->new[0]->query($sql.implode(', ', $sqls).';');
		unset($adjs);
		return true;
	}

	protected function dkp_check()
	{
		global $core;
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
				$output = "<table width='60%' cellpadding='1' cellspacing='1' class='task_table'>";
				$output .= "<tr><td colspan='3' class='th_sub'>".$this->lang['member_with_diff']."</td></tr>";
				foreach($members_with_diff as $mem_id => $diff) {
					$output .= "<tr class='".$core->switch_row_class()."'><td width='50%'>".$members[$mem_id]."</td>";
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

	protected function parse_dkp_check()
	{
		if(!$_POST['dkp_check']) {
			return false;
		}
        $sql = "INSERT INTO ".$this->new[1]."adjustments (adjustment_value, adjustment_reason, member_id, adjustment_date, event_id) VALUES ";
        $sqls = array();
        $insert = false;
		foreach($_POST['dmember'] as $mem_id => $value) {
			settype($mem_id, 'int');
            $event_id = $_POST['dmem_ev'][$mem_id];
            settype($event_id, 'int');
			if($value) {
				$insert = true;
				$sqls[] = "('".$this->step_data['import_data'][$mem_id]."', '".$this->lang['mem_diff_adj_reason']."', '".$mem_id."', '".time()."', '".$event_id."')";
			}
		}
		if($insert) {
			$this->new[0]->query($sql.implode(', ', $sqls).';');
		}
		return true;
	}

	protected function plugins_portal()
	{
		global $core;
		$this->current_step = 'plugins_portal';
		$this->connect2olddb();
		$new_plugins = array();
		$result = $this->new[0]->query("SELECT plugin_code FROM ".$this->new[1]."plugins WHERE plugin_installed = '1';");
		while ( $row = $this->old[0]->fetch_record($result) ) {
			$new_plugins[] = $row['plugin_code'];
		}
		$this->new[0]->free_result($result);
		$plugins = array();
		$result = $this->old[0]->query("SELECT plugin_name, plugin_code, plugin_path FROM ".$this->old[1]."plugins WHERE plugin_installed = '1';");
		while ( $row = $this->old[0]->fetch_record($result) ) {
			if(is_file($this->path.'plugins/'.$row['plugin_path'].'/'.$row['plugin_code'].'_plugin_class.php') AND !in_array($row['plugin_code'], $new_plugins)) {
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
							document.getElementById(code+'_iframe').src = '".$this->path."admin/manage_plugins.php?mode=install&code='+code;
							document.getElementById(code+'_install').className = 'installed';
							document.getElementById(code+'_installed').className = 'install';
						}
						function uninstall_plugin(code) {
							document.getElementById(code+'_iframe').src = '".$this->path."admin/manage_plugins.php?mode=uninstall&code='+code;
							document.getElementById(code+'_install').className = 'install';
							document.getElementById(code+'_installed').className = 'installed';
						}
					</script>";
		$output .= "<table width='100%' cellpadding='1' cellspacing='1' class='task_table'><tr><td colspan='2' class='th_sub'>".$this->lang['which_plugins']."</td></tr>";
		foreach($plugins as $plugin) {
			$output .= "<tr><td width='200px'>".$plugin['name']."</td><td>";
			$output .= "<div id='".$plugin['code']."_install' class='install'><a onclick=\"javascript:install_plugin('".$plugin['code']."')\" class='plugin'>".$this->lang['install']."</a></div>";
			$output .= "<div id='".$plugin['code']."_installed' class='installed'>".$this->lang['installed']."&nbsp;<a onclick=\"javascript:uninstall_plugin('".$plugin['code']."')\" class='plugin'>".$this->lang['uninstall']."</a></div>";
			$output .= "<iframe id='".$plugin['code']."_iframe' style='display:none;' src='".$this->path."admin/manage_plugins.php'></iframe></td></tr>";
		}
		$portals = array();
		$result = $this->old[0]->query("SELECT path, plugin, name FROM ".$this->old[1]."portal WHERE enabled = '1';");
		while ( $row = $this->old[0]->fetch_record($result) ) {
			$path =($row['plugin']) ? 'plugins/'.$row['plugin'].'/portal/'.$row['path'].'.php' : 'portal/'.$row['path'].'/module.php';
			if(is_file($this->path.$path)) {
				$portals[] = array('name' => $row['name'], 'path' => $row['path']);
			}
		}
		if($portals) {
			$output .= "<tr><th colspan='2'>".$this->lang['which_portals']."</th></tr>";
		}
		foreach($portals as $portal) {
			$output .= "<tr class='".$core->switch_row_class()."'><td colspan='2'><input type='checkbox' name='portals[".$portal['path']."]' value='".$portal['plugin']."' />".$portal['name']."</td></tr>";
		}
		$output .= "<tr><th colspan='2' class='th_sub'><input type='submit' name='plugins_portal' value='".$this->lang['submit']."' class='mainoption' /></th></tr></table>";
		
		//store table-names before installed plugins
		$result = $this->new[0]->query("SHOW TABLES LIKE '".$this->new[1]."%';");
		$this->step_data['import_data']['pre_tables'] = array();
		while ( $row = $this->new[0]->fetch_record($result) ) {
			$this->step_data['import_data']['pre_tables'][] = str_replace($this->new[1], '',current($row));
		}
		$this->new[0]->free_result($result);
		return $output;
	}

	protected function parse_plugins_portal()
	{
		if(!$_POST['plugins_portal']) {
			return false;
		}
		$this->connect2olddb();
		$old_tables = array();
		$result = $this->new[0]->query("SHOW TABLES LIKE '".$this->new[1]."%';");
		while ( $row = $this->new[0]->fetch_record($result) ) {
			$cur = str_replace($this->new[1], '',current($row));
			if(!in_array($cur,$this->step_data['import_data']['pre_tables'])) {
				$old_tables[] = $cur;
			}
		}
		$this->new[0]->free_result($result);
		$tables = array();
		$result = $this->old[0]->query("SHOW TABLES LIKE '".$this->old[1]."%';");
			while ( $row = $this->old[0]->fetch_record($result) ) {
			$cur = str_replace($this->old[1], '',current($row));
			if(in_array($cur, $old_tables)) {
				$tables[] = $cur;
			}
		}
		foreach($tables as $table_name) {
			$this->table_by_name($table_name);
		}
		include_once($this->path.'maintenance/includes/tasks/import06/plugin_list.php');
		foreach($plugin_names as $code => $info) {
			if(in_array($info['table'], $tables)) {
				$result = $this->old[0]->query("SELECT config_name, config_value FROM ".$this->old[1].$info['table']."
												WHERE config_name = '".$info['fieldprefix']."inst_version' OR config_name ='".$info['fieldprefix']."inst_build';");
				while ( $row = $this->new[0]->fetch_record($result) ) {
					if($row['config_name'] == $info['fieldprefix']."inst_version") {
						$info['version'] = $row['config_value'];
					} else {
						$info['build'] = $row['config_value'];
					}
				}
				$this->new[0]->query("UPDATE ".$this->new[1]."plugins SET plugin_version = '".$info['version']."', plugin_build = '".$info['build']."' WHERE plugin_code = '".$code."';");
			}
		}
		if($_POST['portals']) {
			$tables = array();
			foreach($_POST['portals'] as $code => $plugin) {
				$path = $this->path.(($plugin != '') ? 'plugins/'.$plugin.'/portal/'.$code.'.php' : 'portal/'.$code.'/module.php');
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
		return ($_POST['no_import']) ? $this->lang['nothing_imported'] : $this->lang['import_end'];
	}
}
?>