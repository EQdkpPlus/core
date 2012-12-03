<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2009
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

if ( !defined('EQDKP_INC') ){
  header('HTTP/1.0 404 Not Found');exit;
}

require_once(registry::get_const('root_path') . 'maintenance/includes/task.aclass.php');

class sql_update_task extends task {
	public static function __shortcuts() {
		$shortcuts = array('config', 'in', 'user', 'db');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public $sqls = array();
	public $plugin_path = '';

	public function is_necessary() {
		$version = $this->config->get('plus_version');
		if($this->plugin_path) {
			$data = $this->db->fetch_record($this->db->query("SELECT version, status FROM __plugins WHERE code = '".$this->plugin_path."';"));
			if($data['status'] != 1) return false;
			$version = $data['version'];
		}
		if(compareVersion($version, $this->version) == -1 AND $version) {
			return true;
		}
		return false;
	}

	public function is_applicable() {
		return true;
	}

	public function get_form_content() {
		include_once($this->root_path.'/maintenance/includes/sql_update.class.php');
		$sql_update = registry::register('sql_update', array(array(get_class($this), $this->lang[get_class($this)], $this->version, $this->plugin_path, $this->name), $this->in->get('update_all', false)));
		return $sql_update->a_get_form_content();
	}

	public function get_sqls() {
		return $this->sqls;
	}

	public function init_lang() {
		if(isset($this->langs[$this->user->data['user_lang']])) {
			$lang = $this->langs[$this->user->data['user_lang']];
		} elseif(isset($this->langs[$this->default_lang])) {
			$lang = $this->langs[$this->default_lang];
		}
		if(!isset($lang)) {
			parent::init_lang();
		} else {
			$this->lang = $lang;
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_sql_update_task', sql_update_task::__shortcuts());
?>