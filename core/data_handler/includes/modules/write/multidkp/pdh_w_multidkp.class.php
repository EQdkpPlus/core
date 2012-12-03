<?php
/*
* Project:		EQdkp-Plus
* License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
* Link:			http://creativecommons.org/licenses/by-nc-sa/3.0/
* -----------------------------------------------------------------------
* Began:		2007
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

if(!class_exists('pdh_w_multidkp')) {
	class pdh_w_multidkp extends pdh_w_generic {
		public static function __shortcuts() {
		$shortcuts = array('pdh', 'db'	);
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public function __construct() {
			parent::__construct();
		}

		public function add_multidkp($name, $desc, $events, $itempools, $no_atts) {
			$arrSet = array(
				'multidkp_name' => $name,
				'multidkp_desc' => $desc,
			);
			if($this->db->query("INSERT INTO __multidkp :params", $arrSet)) {
				$id = $this->db->insert_id();
				$retu = array(true);
				foreach($events as $event_id) {
					$s_no_att = (in_array($event_id, $no_atts)) ? 1 : 0;
					$arrSet = array(
						'multidkp2event_multi_id' => $id,
						'multidkp2event_event_id' => $event_id,
						'multidkp2event_no_attendance' => $s_no_att,
					);
					$retu[] = ($this->db->query("INSERT INTO __multidkp2event :params", $arrSet)) ? true : false;
				}
				foreach ($no_atts as $event_id){
					if (in_array($event_id, $events)) continue;
					$arrSet = array(
						'multidkp2event_multi_id' => $id,
						'multidkp2event_event_id' => $event_id,
						'multidkp2event_no_attendance' => 1,
					);
					$retu[] = ($this->db->query("INSERT INTO __multidkp2event :params", $arrSet)) ? true : false;	
				}
				
				
				foreach($itempools as $itempool_id) {
					$retu[] = ($this->db->query("INSERT INTO __multidkp2itempool :params", array('multidkp2itempool_multi_id' =>$id, 'multidkp2itempool_itempool_id'=>$itempool_id))) ? true : false;
				}
				if(!in_array(false, $retu)) {
					$this->pdh->enqueue_hook('multidkp_update',array($id));
					return $id;
				}
			}
			return false;
		}

		public function update_multidkp($id, $name, $desc, $events, $itempools, $no_atts) {
			$old_events = $this->pdh->get('multidkp', 'event_ids', array($id));
			$old_itempools = $this->pdh->get('multidkp', 'itempool_ids', array($id));
			$old_no_atts = $this->pdh->get('multidkp', 'no_attendance', array($id));

			$arrSet = array(
				'multidkp_name' => $name,
				'multidkp_desc' => $desc,
			);
			if($this->db->query("UPDATE __multidkp SET :params WHERE multidkp_id=?", $arrSet, $id)) {
				$all_events = array_merge($events, $old_events);
				foreach($all_events as $event_id){
					if(in_array($event_id, $old_events) AND in_array($event_id, $events)) {
						if(in_array($event_id, $no_atts) AND !in_array($event_id, $old_no_atts)) {
							$sql = "UPDATE __multidkp2event SET multidkp2event_no_attendance = '1' WHERE multidkp2event_multi_id = '".$this->db->escape($id)."' AND multidkp2event_event_id = '".$this->db->escape($event_id)."';";
							$retu[] = $this->db->query($sql);
						} elseif(!in_array($event_id, $no_atts) AND in_array($event_id, $old_no_atts)) {
							$sql = "UPDATE __multidkp2event SET multidkp2event_no_attendance = '0' WHERE multidkp2event_multi_id = '".$this->db->escape($id)."' AND multidkp2event_event_id = '".$this->db->escape($event_id)."';";
							$retu[] = $this->db->query($sql);
						}
					} elseif(!in_array($event_id, $old_events)) {
						$s_no_att = (in_array($event_id, $no_atts)) ? 1 : 0;
						$sql = "INSERT INTO __multidkp2event (multidkp2event_multi_id, multidkp2event_event_id, multidkp2event_no_attendance)
								VALUES ('".$this->db->escape($id)."', '".$this->db->escape($event_id)."', '".$this->db->escape($s_no_att)."');";
						$retu[] = $this->db->query($sql);
					} elseif(!in_array($event_id, $events)) {
						$sql = "DELETE FROM __multidkp2event WHERE multidkp2event_multi_id = '".$this->db->escape($id)."' AND multidkp2event_event_id = '".$this->db->escape($event_id)."';";
						$retu[] = $this->db->query($sql);
					}
				}
				$all_itempools = (is_array($old_itempools)) ? array_unique(array_merge($itempools, $old_itempools)) : $itempools;
				$retu = array(true);
				foreach($all_itempools as $itempool_id) {
					if(!$old_itempools OR !in_array($itempool_id, $old_itempools)) {
						$sql = "INSERT INTO __multidkp2itempool (multidkp2itempool_multi_id, multidkp2itempool_itempool_id)
								VALUES ('".$this->db->escape($id)."', '".$this->db->escape($itempool_id)."');";
						$retu[] = $this->db->query($sql);
					}elseif(!in_array($itempool_id, $itempools)) {
						$sql = "DELETE FROM __multidkp2itempool WHERE multidkp2itempool_multi_id = '".$this->db->escape($id)."' AND multidkp2itempool_itempool_id = '".$this->db->escape($itempool_id)."';";
						$retu[] = $this->db->query($sql);
					}
				}
				if(!in_array(false, $retu)) {
					$this->pdh->enqueue_hook('multidkp_update', array($id));
					return true;
				}
			}
			return false;
		}

		public function delete_multidkp($id) {
			$sql = "DELETE FROM __multidkp WHERE multidkp_id = '".$this->db->escape($id)."';";
			if($this->db->query($sql)) {
				$sql = "DELETE FROM __multidkp2event WHERE multidkp2event_multi_id = '".$this->db->escape($id)."';";
				$retu[] = ($this->db->query($sql)) ? true : false;
				$sql = "DELETE FROM __multidkp2itempool WHERE multidkp2itempool_multi_id = '".$this->db->escape($id)."';";
				$retu[] = ($this->db->query($sql)) ? true : false;
				if(!in_array(false, $retu)) {
					$this->pdh->enqueue_hook('multidkp_update', array($id));
					return true;
				}
			}
			return false;
		}
		
		public function add_multidkp2event($event_id, $mdkps) {
			if(!is_array($mdkps) || count($mdkps) < 1) return true;
			$this->db->query("DELETE FROM __multidkp2event WHERE multidkp2event_event_id = ?", false, $event_id);
			$sql = "INSERT INTO __multidkp2event (`multidkp2event_event_id`, `multidkp2event_multi_id`) VALUES ";
			$sqls = array();
			foreach($mdkps as $mdkp_id) {
				$sqls[] = "('".$this->db->escape($event_id)."', '".$this->db->escape($mdkp_id)."')";
			}
			if($this->db->query($sql.implode(', ', $sqls).';')) {
				$this->pdh->enqueue_hook('multidkp_update');
				return true;
			}
			return false;
		}
		
		public function reset() {
			$this->db->query("TRUNCATE TABLE __multidkp;");
			$this->pdh->enqueue_hook('multidkp_update');
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_w_multidkp', pdh_w_multidkp::__shortcuts());
?>