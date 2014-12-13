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

if ( !defined('EQDKP_INC') ){
  header('HTTP/1.0 404 Not Found');exit;
}

require_once(registry::get_const('root_path') . 'maintenance/includes/task.aclass.php');

class sql_update_task extends task {

	public $sqls = array();
	public $plugin_path = '';

	public function is_necessary() {
		$version = $this->config->get('plus_version');
		if($this->plugin_path) {
			$objQuery = $this->db->prepare("SELECT version, status FROM __plugins WHERE code =?")->execute($this->plugin_path);
			if ($objQuery){
				$data = $objQuery->fetchAssoc();
				if($data['status'] != 1) return false;
				$version = $data['version'];
			} else $version = false;
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
		$sql_update = registry::register('sql_update', array(array(get_class($this), $this->lang[get_class($this)], $this->version, $this->plugin_path, $this->name, $this->ext_version), $this->in->get('update_all', false)));
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
?>