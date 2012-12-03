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
			$sql = 'DELETE FROM __logs WHERE log_date < '.$log_date;
			$this->db->query($sql);
			$this->pdh->enqueue_hook('logs_update');
			return $this->db->affected_rows();
		}

		public function truncate_log(){
			$this->db->query("TRUNCATE TABLE __logs");
			$this->pdh->enqueue_hook('logs_update');
			return $this->db->affected_rows();
		}

		public function delete_log($log_id){
			$this->db->query("DELETE FROM __logs WHERE log_id = ".$this->db->escape($log_id));
			$this->pdh->enqueue_hook('logs_update', array($log_id));
			return $this->db->affected_rows();
		}

		public function add_log($tag, $value, $admin_action=true, $plugin='', $result=1, $userid = false) {
			$userid = ($userid) ? $userid : $this->user->id;
			
			$this->db->query('INSERT INTO __logs :params', array(
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
			));
			$this->pdh->enqueue_hook('logs_update', array($this->db->insert_id()));
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_w_logs', pdh_w_logs::__shortcuts());
?>