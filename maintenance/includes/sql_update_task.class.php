<?php
 /*
 * Project:     EQdkp Plus Patcher
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		    http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2009
 * Date:        $Date: 2010-01-17 15:42:17 +0100 (So, 17 Jan 2010) $
 * -----------------------------------------------------------------------
 * @author      $Author: hoofy_leon $
 * @copyright   2009 sz3
 * @link        http://www.eqdkp-plus.com
 * @package     plus patcher
 * @version     $Rev: 7013 $
 *
 * $Id: sql_update.class.php 7013 2010-01-17 14:42:17Z hoofy_leon $
 */

if ( !defined('EQDKP_INC') ){
  header('HTTP/1.0 404 Not Found');exit;
}

if ( !class_exists( "task" ) ) {
  require_once($eqdkp_root_path . 'maintenance/includes/task.aclass.php');
}

class sql_update_task extends task {
	public $sqls = array();
	public $plugin_path = '';

	public function is_necessary() {
		global $core, $db;
		$version = $core->config['plus_version'];
        if($this->plugin_path) {
        	$version = $db->query_first("SELECT plugin_version FROM __plugins WHERE plugin_path = '".$this->plugin_path."';");
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
		global $eqdkp_root_path, $in;
		include_once($eqdkp_root_path.'/maintenance/includes/sql_update.class.php');
		$sql_update = new sql_update(array(get_class($this), $this->lang[get_class($this)], $this->version, $this->plugin_path, $this->name), $in->get('update_all', false));
		return $sql_update->a_get_form_content();
	}

	public function get_sqls() {
		return $this->sqls;
	}

	public function init_lang() {
		global $user;
		if(isset($this->langs[$user->data['user_lang']])) {
			$lang = $this->langs[$user->data['user_lang']];
		} else {
			$lang = $this->langs[$this->default_lang];
		}
		if(!$lang) {
			parent::init_lang();
		} else {
			$this->lang = $lang;
		}
	}
}
?>