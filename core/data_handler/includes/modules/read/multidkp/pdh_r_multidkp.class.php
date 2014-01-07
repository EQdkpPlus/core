<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2007
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
	die('Do not access this file directly.');
}

if ( !class_exists( "pdh_r_multidkp" ) ) {
	class pdh_r_multidkp extends pdh_r_generic{
		public static function __shortcuts() {
		$shortcuts = array('pdc', 'db'	);
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public $default_lang = 'english';
		public $multidkp;

		public $hooks = array(
			'multidkp_update',
			'event_update',
			'itempool_update',
		);

		public $presets = array(
			'mdkpname' => array('name', array('%dkp_id%'), array()),
		);

		public function reset(){
			$this->pdc->del('pdh_multidkp_table');
			$this->multidkp = NULL;
		}

		public function init(){
			$this->multidkp = $this->pdc->get('pdh_multidkp_table');
			if($this->multidkp !== NULL){
				return true;
			}
			$this->multidkp = array();

			//fetch multidkp2event data
			$me_data = array();
			$sql = "SELECT * FROM __multidkp2event;";
			$me_result = $this->db->query($sql);
			while($row = $this->db->fetch_record($me_result)){
				$me_data[$row['multidkp2event_multi_id']][] = $row['multidkp2event_event_id'];
				if($row['multidkp2event_no_attendance']) {
					$noatt_data[$row['multidkp2event_multi_id']][] = $row['multidkp2event_event_id'];
				}
			}
			$this->db->free_result($me_result);

			//fetch multidkp2itempool data
			$ip_sql = "SELECT multidkp2itempool_itempool_id, multidkp2itempool_multi_id FROM __multidkp2itempool;";
			$ip_result = $this->db->query($ip_sql);
			while ($row = $this->db->fetch_record($ip_result)){
				$ip_data[$row['multidkp2itempool_multi_id']][] = $row['multidkp2itempool_itempool_id'];
			}
			$this->db->free_result($ip_result);
			$m_result = $this->db->query("SELECT * FROM __multidkp;");
			while($row = $this->db->fetch_record($m_result)){
				$this->multidkp[$row['multidkp_id']]['name'] = $row['multidkp_name'];
				$this->multidkp[$row['multidkp_id']]['desc'] = $row['multidkp_desc'];
				$this->multidkp[$row['multidkp_id']]['events'] = (isset($me_data[$row['multidkp_id']])) ? $me_data[$row['multidkp_id']] : array();
				$this->multidkp[$row['multidkp_id']]['no_attendance'] = ((isset($noatt_data[$row['multidkp_id']])) ? $noatt_data[$row['multidkp_id']] : '');
				$this->multidkp[$row['multidkp_id']]['itempools'] = (isset($ip_data[$row['multidkp_id']])) ? $ip_data[$row['multidkp_id']] : array();
			}
			$this->db->free_result($m_result);
			if($m_result) $this->pdc->put('pdh_multidkp_table', $this->multidkp, null);
		}

		public function get_id_list(){
			return array_keys($this->multidkp);
		}

		public function get_event_ids($mdkp_id, $boolWithoutNoAttendance = false){
			$arrEvents = (isset($this->multidkp[$mdkp_id]['events'])) ? $this->multidkp[$mdkp_id]['events'] : array();
			$arrNoAttendance = (isset($this->multidkp[$mdkp_id]['no_attendance']) && is_array($this->multidkp[$mdkp_id]['no_attendance'])) ? $this->multidkp[$mdkp_id]['no_attendance'] : array();
			if ($boolWithoutNoAttendance){
				return array_diff($arrEvents, $arrNoAttendance);
			}
			return (isset($this->multidkp[$mdkp_id]['events'])) ? $this->multidkp[$mdkp_id]['events'] : array();
		}

		public function get_mdkpids4eventid($event_id, $ignore_no_att=true){
			$ids = array();
			foreach($this->multidkp as $mdkp_id => $mdkp){
				foreach($mdkp['events'] as $mevent_id){
					if($event_id == $mevent_id AND ($ignore_no_att OR !$mdkp['no_attendance'] OR !in_array($mevent_id, $mdkp['no_attendance']))){
						$ids[] = $mdkp_id;
						break;
					}
				}
			}
			return $ids;
		}

		public function get_itempool_ids($mdkp_id){
			return (isset($this->multidkp[$mdkp_id]['itempools'])) ? $this->multidkp[$mdkp_id]['itempools'] : '';
		}

		public function get_mdkpids4itempoolid($itempool_id){
			$ids = array();
			foreach($this->multidkp as $mdkp_id => $mdkp){
				foreach($mdkp['itempools'] as $mitempool_id){
					if($itempool_id == $mitempool_id){
						$ids[] = $mdkp_id;
					}
				}
			}
			return $ids;
		}

		public function get_multidkp_id($mdkp_name){
			foreach($this->multidkp as $mdkp_id => $mdkp){
				if($mdkp_name == $mdkp['name']){
					return $mdkp_id;
				}
			}
		}

		public function get_name($mdkp_id){
			return (isset($this->multidkp[$mdkp_id]['name'])) ? $this->multidkp[$mdkp_id]['name'] : '';
		}

		public function get_desc($mdkp_id){
			return (isset($this->multidkp[$mdkp_id]['desc'])) ? $this->multidkp[$mdkp_id]['desc'] : '';
		}

		public function get_no_attendance($mdkp_id) {
			return (isset($this->multidkp[$mdkp_id]['no_attendance']) && is_array($this->multidkp[$mdkp_id]['no_attendance'])) ? $this->multidkp[$mdkp_id]['no_attendance'] : array();
		}
	}//end class
}//end if
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_r_multidkp', pdh_r_multidkp::__shortcuts());
?>