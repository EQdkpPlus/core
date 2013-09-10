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

if(!defined('EQDKP_INC')) {
	die('Do not access this file directly.');
}

if(!class_exists('pdh_w_logs')) {
	class pdh_w_logs extends pdh_w_generic {
		public static function __shortcuts() {
		$shortcuts = array('pdh', 'db', 'user', 'env');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public function __construct() {
			parent::__construct();
		}

		public function clean_log($timestamp){
			$log_date = time()-($timestamp*24*60*60);
			$objQuery = $this->db->prepare('DELETE FROM __logs WHERE log_date < ?')->execute($log_date);
			$this->pdh->enqueue_hook('logs_update');
			return $objQuery->affectedRows;
		}
		
		public function delete_ids($arrIDs){
			$this->db->prepare("DELETE FROM __logs WHERE log_id :in")->in($arrIDs)->execute();
			
			$this->pdh->enqueue_hook('logs_update');
			return count($arrIDs);
		}

		public function truncate_log(){
			$arrResult = $this->db->query("SELECT count(*) as count FROM __logs", true);
			$count = $arrResult['count'];
			
			$this->db->query("TRUNCATE TABLE __logs");
			$this->pdh->enqueue_hook('logs_update');
			return intval($count);
		}

		public function delete_log($log_id){
			$objQuery = $this->db->prepare("DELETE FROM __logs WHERE log_id=?")->execute($log_id);
			if ($objQuery) {
				$this->pdh->enqueue_hook('logs_update', array($log_id));
				return $objQuery->affectedRows;
			}
			return false;
		}

		public function add_log($tag, $value, $recordid=0, $record='',$admin_action=true, $plugin='', $result=1, $userid = false) {
			$userid = ($userid) ? $userid : $this->user->id;
			
			$objQuery = $this->db->prepare('INSERT INTO __logs :p')->set(array(
				'log_value'			=> serialize($value),
				'log_result'		=> $result,
				'log_tag'			=> $tag,
				'log_date'			=> time(),
				'log_ipaddress'		=> $this->env->ip,
				'log_sid'			=> $this->user->sid,
				'user_id'			=> $userid,
				'username'			=> $this->pdh->get('user', 'name', array($userid)),
				'log_plugin'		=> $plugin,
				'log_flag'			=> ($admin_action) ? 1 : 0,
				'log_record'		=> $record,
				'log_record_id'		=> $recordid,
			))->execute();
			
			if ($objQuery){
				$id = $objQuery->insertId;
				$this->pdh->enqueue_hook('logs_update', array($id));
			}
			
		}
	}
}
?>