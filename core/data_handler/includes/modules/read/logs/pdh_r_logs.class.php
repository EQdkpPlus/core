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
		private $index = array();
		private $logs = array();
		private $objPagination = null;
		private $last_logs = array();
		
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

		public function init(){
			$this->objPagination = register("cachePagination", array("logs", "log_id", "__logs", array(), 100));
			$this->objPagination->initIndex();
			$this->index = $this->objPagination->getIndex();
		}
		

		public function reset($ids=false){
			$this->objPagination = register("cachePagination", array("logs", "log_id", "__logs", array(), 100));
			return $this->objPagination->reset($ids);
		}

		public function get_plugins() {
			$objQuery = $this->db->query("SELECT DISTINCT log_plugin FROM __logs;");
			$arrPlugins = array();
			if($objQuery){
				while($row = $objQuery->fetchAssoc()){
					$arrPlugins[] = $row['log_plugin'];
				}
			}
			return $arrPlugins;
		}
		
		public function get_grouped_users(){
			$objQuery = $this->db->query("SELECT DISTINCT user_id FROM __logs;");
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
			$objQuery = $this->db->query("SELECT DISTINCT log_tag FROM __logs;");
			if($objQuery){
				while($row = $objQuery->fetchAssoc()){
					$arrTags[] = $row['log_tag'];
				}
			}
			
			return $arrTags;
		}
		
		public function get_filtered_id_list($plugin, $result, $ip, $sid, $tag, $user_id, $value, $date_from, $date_to, $recordid, $record){
			$strQuery = "SELECT log_id FROM __logs WHERE ";
			if ($plugin !== false) $strQuery .= " log_plugin= '".$this->db->escapeString($plugin). "' AND";
			if ($result !== false) $strQuery .= " log_result= ".$this->db->escapeString($result). " AND";
			if ($ip !== false) $strQuery .= " log_ipaddress LIKE ".$this->db->escapeString('%'.$ip.'%'). " AND";
			if ($sid !== false) $strQuery .= " log_sid LIKE ".$this->db->escapeString('%'.$sid.'%'). " AND";
			if ($tag !== false) $strQuery .= " log_tag = '".$this->db->escapeString($tag). "' AND";
			if ($user_id !== false) $strQuery .= " user_id =".$this->db->escapeString($user_id). " AND";
			if ($value !== false) $strQuery .= " log_value LIKE ".$this->db->escapeString("%".$value."%"). " AND";
			if ($date_from !== false) $strQuery .= " log_date > ".$this->db->escapeString($date_from). " AND";
			if ($date_to !== false) $strQuery .= " log_date < ".$this->db->escapeString($date_to)." AND";
			if ($recordid !== false) $strQuery .= " log_record_id = '".$this->db->escapeString($recordid). "' AND";
			if ($record !== false) $strQuery .= " log_record= '".$this->db->escapeString($record). "' AND";
			
			$strQuery .= " log_id > 0";
			
			
			$objQuery = $this->db->query($strQuery);
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
				$objQuery = $this->db->prepare("SELECT log_id FROM __logs WHERE log_id :in ORDER BY username ".$direction.";")->in($id_list)->execute();
			} else {
				$objQuery = $this->db->prepare("SELECT log_id FROM __logs WHERE log_id :in ORDER BY log_".$tag." ".$direction.";")->in($id_list)->execute();
			}
			$id_list = array();
			if($objQuery){
				while ( $row = $objQuery->fetchAssoc() ) {
					$id_list[] = $row['log_id'];
				}
			}
			return $id_list;
		}

		public function get_id_list() {
			return array_keys($this->index);
		}

		public function get_lastxlogs($amount=10) {
			if(!isset($this->last_logs[$amount])) {
				$this->last_logs[$amount] = array();
				
				$objQuery = $this->db->prepare("SELECT log_id FROM __logs ORDER BY log_date DESC")->limit($amount)->execute();
				if($objQuery){
					while ( $row = $objQuery->fetchAssoc() ) {
						$this->last_logs[$amount][] = $row['log_id'];
					}
				}	
			}
			return $this->last_logs[$amount];
		}

		public function get_tag($id) {
			return $this->objPagination->get($id, 'log_tag');
		}

		public function get_html_tag($id, $link_url=false, $link_suffix='') {	
			if(!$this->get_tag($id)) return "";
			$intFlag = (int)$this->objPagination->get($id, 'log_flag');		
			$flag = ($intFlag == 1) ? ' class="admin_icon"' : '';
			if(!$link_url) return ($flag) ? '<span'.$flag.'><span>'.$this->user->lang($this->get_tag($id), true, false).'</span></span>' : $this->user->lang($this->get_tag($id), true, false);
			$link = $link_url.$this->SID . '&amp;logid='.$id.$link_suffix;
			$link = '<a'.$flag.' title="'.(($intFlag == 1) ? $this->user->lang('admin_action') : '').'" href="'.$link.'"><span>';
			return $link.$this->user->lang($this->get_tag($id), true, false).'</span></a>';
		}

		public function get_date($id){
			return $this->objPagination->get($id, 'log_date');
		}

		public function get_html_date($id, $withtime=false){
			return $this->time->user_date($this->get_date($id), $withtime);
		}

		public function get_user($id){
			return $this->objPagination->get($id, 'username');
		}

		public function get_value($id){
			return $this->objPagination->get($id, 'log_value');
		}

		public function get_ipaddress($id){
			return $this->objPagination->get($id, 'log_ipaddress');
		}

		public function get_sid($id) {
			return $this->objPagination->get($id, 'log_sid');
		}

		public function get_result($id) {
			return $this->objPagination->get($id, 'log_result');
		}

		public function get_html_result($id){
			$res = $this->get_result($id);
			$color	= ($res) ? 'positive' : 'negative';
			$lang	= ($res) ? $this->user->lang('success') : $this->user->lang('error');
			return '<span class="'.$color.'">'.$lang.'</span>';
		}
		
		public function get_record($id) {
			return $this->objPagination->get($id, 'log_record');
		}
		
		public function get_recordid($id) {
			return $this->objPagination->get($id, 'log_record_id');
		}

		public function get_plugin($id){
			return $this->objPagination->get($id, 'log_plugin');
		}

		public function get_html_plugin($id){
			if(!$this->get_plugin($id)) return '';
			if ($this->user->lang($this->get_plugin($id), false, false)) return $this->user->lang($this->get_plugin($id));

			return ucfirst($this->get_plugin($id));
		}

		public function get_viewicon($id, $link='', $suffix='') {
			$link = $link.$this->SID . '&amp;logid='.$id.$suffix;
			return '<a href="'.$link.'"><i class="fa fa-search fa-lg"></i></a>';
		}
	}//end class
}//end if
?>