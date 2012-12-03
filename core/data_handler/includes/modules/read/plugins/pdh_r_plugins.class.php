<?php
/*
* Project:		EQdkp-Plus
* License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
* Link:			http://creativecommons.org/licenses/by-nc-sa/3.0/
* -----------------------------------------------------------------------
* Began:		2010
* Date:			$Date$
* -----------------------------------------------------------------------
* @author		$Author$
* @copyright	2006-2011 EQdkp-Plus Developer Team
* @link			http://eqdkp-plus.com
* @package		eqdkpplus
* @version		$Rev$
*
* $Id$
*/

if ( !defined('EQDKP_INC') ){
	die('Do not access this file directly.');
}

if ( !class_exists( "pdh_r_plugins" ) ) {
	class pdh_r_plugins extends pdh_r_generic{
		public static function __shortcuts() {
		$shortcuts = array('pdc', 'db'	);
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public $default_lang = 'english';
		public $plugins;

		public $hooks = array(
			'plugins_update'
		);

		public function reset(){
			$this->pdc->del('pdh_plugins_table');
			$this->plugins = NULL;
		}

		public function init(){
			$this->plugins	= $this->pdc->get('pdh_plugins_table');
			if($this->plugins !== NULL){
				return true;
			}
			$result = $this->db->query("SELECT * FROM __plugins ORDER BY code");
			while($drow = $this->db->fetch_record($result) ){
				$this->plugins[$drow['code']] = array(
					'code'		=> $drow['code'],
					'version'	=> $drow['version'],
					'status'	=> $drow['status']
				);
			}
			$this->db->free_result($result);
			$this->pdc->put('pdh_plugins_table', $this->plugins, null);
		}

		public function get_id_list() {
			return array_keys($this->plugins);
		}

		public function get_data($plugin_code='', $field=''){
			return ($plugin_code) ? (($field) ? $this->plugins[$plugin_code][$field] : $this->plugins[$plugin_code]) : $this->plugins;
		}
	}//end class
}//end if
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_r_plugins', pdh_r_plugins::__shortcuts());
?>