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

if ( !class_exists( "pdh_r_logs" ) ) {
	class pdh_r_logs extends pdh_r_generic{
		public static function __shortcuts() {
		$shortcuts = array('pdc', 'db', 'user', 'pdh', 'time');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public $default_lang = 'english';
		private $data = array();
		private $logs = array('ids'=>array(), 'plugins'=>array());

		public $hooks = array(
			'logs_update'
		);

		public $presets = array(
			'logdate'		=> array('date',		array('%log_id%'), array()),
			'logtype'		=> array('tag',			array('%log_id%', '%link_url%', '%link_url_suffix%'), array()),
			'logplugin'		=> array('plugin',		array('%log_id%'), array()),
			'loguser'		=> array('user',		array('%log_id%'), array()),
			'logipaddress'	=> array('ipaddress',	array('%log_id%'), array()),
			'logresult'		=> array('result',		array('%log_id%'), array()),
			'viewlog'		=> array('viewicon',	array('%log_id%', '%link_url%', '%link_url_suffix%'), array()),
		);

		private $cache_number = 100;
		private $last_logs = array();

		private function cache_id($id) {
			return ($id-($id%$this->cache_number))/$this->cache_number;
		}

		public function reset($ids=false){
			if($ids === false) $this->pdc->del_prefix('pdh_logs_table');
			else {
				$this->pdc->del('pdh_logs_table');
				if(!is_array($ids)) $ids = array($ids);
				foreach($ids as $id) {
					if(!is_numeric($id)) return $this->reset();
					$this->pdc->del('pdh_logs_table_'.$this->cache_id($id));
				}
			}
			$this->logs = NULL;
			$this->data = NULL;
			return true;
		}

		public function init($id=false){
			if($id === false) {
				$this->logs = $this->pdc->get('pdh_logs_table');
				if(empty($this->logs)) {
					$id_res = $this->db->query("SELECT log_id, log_plugin FROM __logs;");
					while ( $row = $this->db->fetch_row($id_res) ) {
						$this->logs['ids'][$row['log_id']] = $row['log_plugin'];
						$this->logs['plugins'][$row['log_plugin']] = $row['log_plugin'];
					}
					$this->db->free_result($id_res);
				}
				$this->pdc->put('pdh_logs_table', $this->logs, null);
				return true;
			}
			if(!isset($this->logs['ids'][$id])) return false;
			//load logs in 100er chunks
			$cache_name = $this->cache_id($id);

			$cache_result = $this->pdc->get('pdh_logs_table_'.$cache_name);
			if(empty($cache_result)) {
				$pff_result = $this->db->query("SELECT * FROM __logs WHERE log_id >= '".$cache_name*$this->cache_number."' AND log_id < '".($cache_name+1)*$this->cache_number."';");
				while($drow = $this->db->fetch_row($pff_result) ){
					$cache_result[$drow['log_id']] = array(
						'log_id'			=> $drow['log_id'],
						'log_date'			=> $drow['log_date'],
						'log_ipaddress'		=> $drow['log_ipaddress'],
						'log_result'		=> $drow['log_result'],
						'log_tag'			=> $drow['log_tag'],
						'log_flag'			=> $drow['log_flag'],
						'log_plugin'		=> $drow['log_plugin'],
						'user_id'			=> $drow['user_id'],
						'username'			=> $drow['username'],
					);
				}
				$this->db->free_result($pff_result);
				$this->pdc->put('pdh_logs_table_'.$cache_name, $cache_result, null);
			}
			if(!is_array($this->data)) $this->data = array();
			if(is_array($cache_result)) $this->data += $cache_result;
			return true;
		}

		public function get_plugins() {
			return $this->logs['plugins'];
		}

		public function sort($id_list, $tag, $direction = 'asc', $params = array( ), $id_position = 0) {
			if(empty($id_list)) return array();
			if(!method_exists($this, 'get_'.$tag) || $tag == 'viewicon' || $tag == 'value' || $tag == 'id_list' || $tag == 'lastxlogs') return $id_list;
			if($tag == 'user') { return $id_list; }
			$direction = ($direction == 'asc') ? 'ASC' : 'DESC';
			$result = $this->db->query("SELECT log_id FROM __logs WHERE log_id IN ('".implode("', '", $id_list)."') ORDER BY log_".$tag." ".$direction.";");
			$id_list = array();
			while ( $row = $this->db->fetch_row($result) ) {
				$id_list[] = $row['log_id'];
			}
			return $id_list;
		}

		public function get_id_list($plugin = false) {
			if ($plugin && isset($this->logs['plugins'][$plugin])) {
				$out = array();
				foreach($this->logs['ids'] as $id => $plug) {
					if($plug == $plugin) $out[] = $id;
				}
				return $out;
			}
			return array_keys($this->logs['ids']);
		}

		public function get_lastxlogs($amount=10) {
			if(!isset($this->last_logs[$amount])) {
				$this->last_logs[$amount] = array();
				$result = $this->db->query("SELECT log_id FROM __logs ORDER BY log_date DESC LIMIT 0, ".$this->db->escape($amount).";");
				while ( $row = $this->db->fetch_row($result) ) {
					$this->last_logs[$amount][] = $row['log_id'];
				}
			}
			return $this->last_logs[$amount];
		}

		public function get_tag($id) {
			if(!isset($this->data[$id]) && isset($this->logs['ids'][$id])) $this->init($id);
			return $this->data[$id]['log_tag'];
		}

		public function get_html_tag($id, $link_url=false, $link_suffix='') {
			if(!isset($this->data[$id]) && isset($this->logs['ids'][$id])) $this->init($id);
			$flag = ($this->data[$id]['log_flag'] == 1) ? ' class="admin_icon"' : '';
			if(!$link_url) return ($flag) ? '<span'.$flag.'><span>'.$this->user->lang($this->get_tag($id), true, false).'</span></span>' : $this->user->lang($this->get_tag($id), true, false);
			$link = $link_url.$this->SID . '&amp;logid='.$id.$link_suffix;
			$link = '<a'.$flag.' title="'.(($this->data[$id]['log_flag'] == 1) ? $this->user->lang('admin_action') : '').'" href="'.$link.'"><span>';
			return $link.$this->user->lang($this->get_tag($id), true, false).'</span></a>';
		}

		public function get_date($id){
			if(!isset($this->data[$id]) && isset($this->logs['ids'][$id])) $this->init($id);
			return $this->data[$id]['log_date'];
		}

		public function get_html_date($id, $withtime=false){
			if(!isset($this->data[$id]) && isset($this->logs['ids'][$id])) $this->init($id);
			return $this->time->user_date($this->data[$id]['log_date']);
		}

		public function get_user($id){
			if(!isset($this->data[$id]) && isset($this->logs['ids'][$id])) $this->init($id);
			return $this->data[$id]['username'];
		}

		public function get_value($id){
			if(!isset($this->logs['ids'][$id])) return false;
			if(!isset($this->data[$id])) $this->init($id);
			if(!isset($this->data[$id]['log_value'])) $this->data[$id]['log_value'] = $this->db->query_first("SELECT log_value FROM __logs WHERE log_id = '".$id."';");
			return $this->data[$id]['log_value'];
		}

		public function get_ipaddress($id){
			if(!isset($this->data[$id]) && isset($this->logs['ids'][$id])) $this->init($id);
			return $this->data[$id]['log_ipaddress'];
		}

		public function get_sid($id) {
			if(!isset($this->data[$id])) return false;
			if(!isset($this->data[$id]['log_sid'])) $this->data[$id]['log_sid'] = $this->db->query_first("SELECT log_sid FROM __logs WHERE log_id = '".$id."';");
			return $this->data[$id]['log_sid'];
		}

		public function get_result($id) {
			if(!isset($this->data[$id]) && isset($this->logs['ids'][$id])) $this->init($id);
			return $this->data[$id]['log_result'];
		}

		public function get_html_result($id){
			$res = $this->get_result($id);
			$color	= ($res) ? 'positive' : 'negative';
			$lang	= ($res) ? $this->user->lang('success') : $this->user->lang('error');
			return '<span class="'.$color.'">'.$lang.'</span>';
		}

		public function get_plugin($id){
			if(!isset($this->logs['ids'][$id])) return false;
			return $this->logs['ids'][$id];
		}

		public function get_html_plugin($id){
			if(!isset($this->logs['ids'][$id])) return '';
			if ($this->user->lang($this->logs['ids'][$id], false, false)) return $this->user->lang($this->logs['ids'][$id]);

			return ucfirst($this->logs['ids'][$id]);
		}

		public function get_viewicon($id, $link='', $suffix='') {
			$link = $link.$this->SID . '&amp;logid='.$id.$suffix;
			return '<a href="'.$link.'"><img src="'.$this->root_path.'images/glyphs/view.png" alt="v" /></a>';
		}
	}//end class
}//end if
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_r_logs', pdh_r_logs::__shortcuts());
?>