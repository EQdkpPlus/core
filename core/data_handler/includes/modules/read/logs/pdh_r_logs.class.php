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
		$shortcuts = array('pdc', 'db2', 'user', 'pdh', 'time');
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
			'logdatetime'	=> array('date',		array('%log_id%', true), array()),
			'logtype'		=> array('tag',			array('%log_id%', '%link_url%', '%link_url_suffix%'), array()),
			'logplugin'		=> array('plugin',		array('%log_id%'), array()),
			'loguser'		=> array('user',		array('%log_id%'), array()),
			'logipaddress'	=> array('ipaddress',	array('%log_id%'), array()),
			'logresult'		=> array('result',		array('%log_id%'), array()),
			'logrecord'		=> array('record',		array('%log_id%'), array()),
			'logrecordid'	=> array('recordid',	array('%log_id%'), array()),
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
					$objQuery = $this->db2->query("SELECT log_id, log_plugin FROM __logs;");
					if($objQuery){
						while($row = $objQuery->fetchAssoc()){
							$this->logs['ids'][$row['log_id']] = $row['log_plugin'];
							$this->logs['plugins'][$row['log_plugin']] = $row['log_plugin'];
						}
					}
				}
				$this->pdc->put('pdh_logs_table', $this->logs, null);
				return true;
			}
			if(!isset($this->logs['ids'][$id])) return false;
			//load logs in 100er chunks
			$cache_name = $this->cache_id($id);

			$cache_result = $this->pdc->get('pdh_logs_table_'.$cache_name);
			if(empty($cache_result)) {
				$objQuery = $this->db2->prepare("SELECT * FROM __logs WHERE log_id >= ? AND log_id < ?;")->execute($cache_name*$this->cache_number, ($cache_name+1)*$this->cache_number);
				if($objQuery){
					while($drow = $objQuery->fetchAssoc()){
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
							'log_record'		=> $drow['log_record'],
							'log_recordid'		=> $drow['log_record_id'],
						);
					}
				}
	
				$this->pdc->put('pdh_logs_table_'.$cache_name, $cache_result, null);
			}
			if(!is_array($this->data)) $this->data = array();
			if(is_array($cache_result)) $this->data += $cache_result;
			return true;
		}

		public function get_plugins() {
			return $this->logs['plugins'];
		}
		
		public function get_grouped_users(){
			$objQuery = $this->db2->query("SELECT DISTINCT user_id FROM __logs;");
			$arrUsers = array();
			if($objQuery){
				while($row = $objQuery->fetchAssoc()){
					$arrUsers[] = $row['user_id'];
				}
			}

			return $arrUsers;
		}
		
		public function get_grouped_tags(){
			$arrTags = array();
			$objQuery = $this->db2->query("SELECT DISTINCT log_tag FROM __logs;");
			if($objQuery){
				while($row = $objQuery->fetchAssoc()){
					$arrTags[] = $row['log_tag'];
				}
			}
			
			return $arrTags;
		}
		
		public function get_filtered_id_list($plugin, $result, $ip, $sid, $tag, $user_id, $value, $date_from, $date_to, $recordid, $record){
			$strQuery = "SELECT log_id FROM __logs WHERE ";
			if ($plugin !== false) $strQuery .= " log_plugin= '".$this->db2->escapeString($plugin). "' AND";
			if ($result !== false) $strQuery .= " log_result= ".$this->db2->escapeString($result). " AND";
			if ($ip !== false) $strQuery .= " log_ipaddress LIKE '%".$this->db2->escapeString($ip). "%' AND";
			if ($sid !== false) $strQuery .= " log_sid LIKE '%".$this->db2->escapeString($sid). "%' AND";
			if ($tag !== false) $strQuery .= " log_tag = '".$this->db2->escapeString($tag). "' AND";
			if ($user_id !== false) $strQuery .= " user_id =".$this->db2->escapeString($user_id). " AND";
			if ($value !== false) $strQuery .= " log_value LIKE '%".$this->db2->escapeString($value). "%' AND";
			if ($date_from !== false) $strQuery .= " log_date > ".$this->db2->escapeString($date_from). " AND";
			if ($date_to !== false) $strQuery .= " log_date < ".$this->db2->escapeString($date_to)." AND";
			if ($recordid !== false) $strQuery .= " log_record_id = '".$this->db2->escapeString($recordid). "' AND";
			if ($record !== false) $strQuery .= " log_record= '".$this->db2->escapeString($record). "' AND";
			
			$strQuery .= " log_id > 0";
			
			
			$objQuery = $this->db2->query($strQuery);
			$arrIDs = array();
			if($objQuery){
				while($row = $objQuery->fetchAssoc()){
					$arrIDs[] = $row['log_id'];
				}
			}
			
			return $arrIDs;
		}

		public function sort($id_list, $tag, $direction = 'asc', $params = array( ), $id_position = 0) {
			if(empty($id_list)) return array();
			if(!method_exists($this, 'get_'.$tag) || $tag == 'viewicon' || $tag == 'value' || $tag == 'id_list' || $tag == 'lastxlogs') return $id_list;

			$direction = ($direction == 'asc') ? 'ASC' : 'DESC';
						if($tag == 'user') { 
				$objQuery = $this->db2->prepare("SELECT log_id FROM __logs WHERE log_id :in ORDER BY username ".$direction.";")->in($id_list)->execute();
			} else {
				$objQuery = $this->db2->prepare("SELECT log_id FROM __logs WHERE log_id :in ORDER BY log_".$tag." ".$direction.";")->in($id_list)->execute();
			}
			$id_list = array();
			if($objQuery){
				while ( $row = $objQuery->fetchAssoc() ) {
					$id_list[] = $row['log_id'];
				}
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
				
				$objQuery = $this->db2->prepare("SELECT log_id FROM __logs ORDER BY log_date DESC")->limit($amount)->execute();
				if($objQuery){
					while ( $row = $objQuery->fetchAssoc() ) {
						$this->last_logs[$amount][] = $row['log_id'];
					}
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
			return $this->time->user_date($this->data[$id]['log_date'], $withtime);
		}

		public function get_user($id){
			if(!isset($this->data[$id]) && isset($this->logs['ids'][$id])) $this->init($id);
			return $this->data[$id]['username'];
		}

		public function get_value($id){
			if(!isset($this->logs['ids'][$id])) return false;
			if(!isset($this->data[$id])) $this->init($id);
			if(!isset($this->data[$id]['log_value'])) {
				$arrResult = $this->db2->query("SELECT log_value FROM __logs WHERE log_id = '".intval($id)."';", true);		
				$this->data[$id]['log_value'] = $arrResult['log_value'];
			}
			return $this->data[$id]['log_value'];
		}

		public function get_ipaddress($id){
			if(!isset($this->data[$id]) && isset($this->logs['ids'][$id])) $this->init($id);
			return $this->data[$id]['log_ipaddress'];
		}

		public function get_sid($id) {
			if(!isset($this->data[$id])) return false;
			if(!isset($this->data[$id]['log_sid'])){
				$arrResult = $this->db2->query("SELECT log_sid FROM __logs WHERE log_id = '".intval($id)."';", true);
				$this->data[$id]['log_sid'] = $arrResult['log_sid'];
			}
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
		
		public function get_record($id) {
			if(!isset($this->data[$id]) && isset($this->logs['ids'][$id])) $this->init($id);
			return $this->data[$id]['log_record'];
		}
		
		public function get_recordid($id) {
			if(!isset($this->data[$id]) && isset($this->logs['ids'][$id])) $this->init($id);
			return $this->data[$id]['log_recordid'];
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
?>