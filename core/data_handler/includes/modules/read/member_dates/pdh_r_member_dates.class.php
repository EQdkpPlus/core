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

if ( !defined('EQDKP_INC') )
{
die('Do not access this file directly.');
}

if ( !class_exists( "pdh_r_member_dates" ) ) {
	class pdh_r_member_dates extends pdh_r_generic{
		public static function __shortcuts() {
		$shortcuts = array('pdc', 'pdh', 'time'	);
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public $default_lang = 'english';

		public $fl_raid_dates;
		public $fl_item_dates;

		public $hooks = array(
			'adjustment_update',
			'event_update',
			'item_update',
			'member_update',
			'raid_update',
			'multidkp_update'
		);

		public $presets = array(
			'mfirst_item_date'	=> array('first_item_date', array('%member_id%', '%dkp_id%', '%with_twink%'), array()),
			'mfirst_item_name'	=> array('first_item_name', array('%member_id%', '%dkp_id%', '%with_twink%'), array()),
			'mlast_item_date'	=> array('last_item_date', array('%member_id%', '%dkp_id%', '%with_twink%'), array()),
			'mlast_item_name'	=> array('last_item_name', array('%member_id%', '%dkp_id%', '%with_twink%'), array()),
			'first_raid'		=> array('first_raid', array('%member_id%', '%dkp_id%', '%with_twink%'), array()),
			'last_raid'			=> array('last_raid', array('%member_id%', '%dkp_id%', '%with_twink%'), array()),
		);

		public $detail_twink = array(
			'first_item_date'	=> 'summed_up',
			'first_item_name'	=> 'summed_up',
			'last_item_date'	=> 'summed_up',
			'last_item_name'	=> 'summed_up',
			'first_raid'		=> 'summed_up',
			'last_raid'			=> 'summed_up',
		);

		public function reset(){
			$this->pdc->del('pdh_fl_raid_dates');
			$this->pdc->del('pdh_fl_item_dates');
			$this->fl_item_dates = NULL;
			$this->fl_raid_dates = NULL;
		}

		public function init(){
			$this->init_raid_dates();
			$this->init_item_dates();
		}

		public function init_raid_dates(){
			//cached data not outdated?
			$this->fl_raid_dates = $this->pdc->get('pdh_fl_raid_dates');
			if($this->fl_raid_dates != null){
				return true;
			}

			$this->fl_raid_dates = array();
			$raid_ids = $this->pdh->get('raid', 'id_list');
			$main_ids = $this->pdh->aget('member', 'mainid', 0, array($this->pdh->get('member', 'id_list', array(false, false, false, false))));
			foreach($raid_ids as $raid_id){
				$date = $this->pdh->get('raid', 'date', array($raid_id));
				$attendees = $this->pdh->get('raid', 'raid_attendees', array($raid_id));
				$event_id = $this->pdh->get('raid', 'event', array($raid_id));
				$mdkpids = $this->pdh->get('multidkp', 'mdkpids4eventid', array($event_id));
				if(is_array($attendees)) {
					foreach($attendees as $attendee_id){
						if(!isset($this->fl_raid_dates['single'][$attendee_id]['total']['first_date']) || $date < $this->fl_raid_dates['single'][$attendee_id]['total']['first_date']) {
							$this->fl_raid_dates['single'][$attendee_id]['total']['first_date'] = $date;
						}
						if(!isset($this->fl_raid_dates['multi'][$main_ids[$attendee_id]]['total']['first_date']) || $date < $this->fl_raid_dates['multi'][$main_ids[$attendee_id]]['total']['first_date']) {
							$this->fl_raid_dates['multi'][$main_ids[$attendee_id]]['total']['first_date'] = $date;
						}
						if(!isset($this->fl_raid_dates['single'][$attendee_id]['total']['last_date']) || $date > $this->fl_raid_dates['single'][$attendee_id]['total']['last_date']) {
							$this->fl_raid_dates['single'][$attendee_id]['total']['last_date'] = $date;
						}
						if(!isset($this->fl_raid_dates['multi'][$main_ids[$attendee_id]]['total']['last_date']) || $date > $this->fl_raid_dates['multi'][$main_ids[$attendee_id]]['total']['last_date']) {
							$this->fl_raid_dates['multi'][$main_ids[$attendee_id]]['total']['last_date'] = $date;
						}
						if(!isset($this->fl_raid_dates['single'][$attendee_id]['event'][$event_id]['first_date']) || $date < $this->fl_raid_dates['single'][$attendee_id]['event'][$event_id]['first_date']) {
							$this->fl_raid_dates['single'][$attendee_id]['event'][$event_id]['first_date'] = $date;
						}
						if(!isset($this->fl_raid_dates['multi'][$main_ids[$attendee_id]]['event'][$event_id]['first_date']) || $date < $this->fl_raid_dates['multi'][$main_ids[$attendee_id]]['event'][$event_id]['first_date']) {
							$this->fl_raid_dates['multi'][$main_ids[$attendee_id]]['event'][$event_id]['first_date'] = $date;
						}
						if(!isset($this->fl_raid_dates['single'][$attendee_id]['event'][$event_id]['last_date']) || $date > $this->fl_raid_dates['single'][$attendee_id]['event'][$event_id]['last_date']) {
							$this->fl_raid_dates['single'][$attendee_id]['event'][$event_id]['last_date'] = $date;
						}
						if(!isset($this->fl_raid_dates['multi'][$main_ids[$attendee_id]]['event'][$event_id]['last_date']) || $date > $this->fl_raid_dates['multi'][$main_ids[$attendee_id]]['event'][$event_id]['last_date']) {
							$this->fl_raid_dates['multi'][$main_ids[$attendee_id]]['event'][$event_id]['last_date'] = $date;
						}
						foreach($mdkpids as $mdkp_id){
							if(!isset($this->fl_raid_dates['single'][$attendee_id]['mdkp'][$mdkp_id]['first_date']) || $date < $this->fl_raid_dates['single'][$attendee_id]['mdkp'][$mdkp_id]['first_date']) {
								$this->fl_raid_dates['single'][$attendee_id]['mdkp'][$mdkp_id]['first_date'] = $date;
							}
							if(!isset($this->fl_raid_dates['multi'][$main_ids[$attendee_id]]['mdkp'][$mdkp_id]['first_date']) || $date < $this->fl_raid_dates['multi'][$main_ids[$attendee_id]]['mdkp'][$mdkp_id]['first_date']) {
								$this->fl_raid_dates['multi'][$main_ids[$attendee_id]]['mdkp'][$mdkp_id]['first_date'] = $date;
							}
							if(!isset($this->fl_raid_dates['single'][$attendee_id]['mdkp'][$mdkp_id]['last_date']) || $date > $this->fl_raid_dates['single'][$attendee_id]['mdkp'][$mdkp_id]['last_date']) {
								$this->fl_raid_dates['single'][$attendee_id]['mdkp'][$mdkp_id]['last_date'] = $date;
							}
							if(!isset($this->fl_raid_dates['multi'][$main_ids[$attendee_id]]['mdkp'][$mdkp_id]['last_date']) || $date > $this->fl_raid_dates['multi'][$main_ids[$attendee_id]]['mdkp'][$mdkp_id]['last_date']) {
								$this->fl_raid_dates['multi'][$main_ids[$attendee_id]]['mdkp'][$mdkp_id]['last_date'] = $date;
							}
						}
					}
				}
			}
			$this->pdc->put('pdh_fl_raid_dates', $this->fl_raid_dates);
		}

		public function init_item_dates(){
			//cached data not outdated?
			$this->fl_item_dates = $this->pdc->get('pdh_fl_item_dates');
			if($this->fl_item_dates != null){
				return true;
			}
			//initialise table
			$this->fl_item_dates = array();

			$item_ids = $this->pdh->get('item', 'id_list');
			$main_ids = $this->pdh->aget('member', 'mainid', 0, array($this->pdh->get('member', 'id_list', array(false, false, false, false))));
			$itempools = $this->pdh->aget('multidkp', 'mdkpids4itempoolid', 0, array($this->pdh->get('itempool', 'id_list')));
			foreach($item_ids as $item_id){
				$member_id = $this->pdh->get('item', 'buyer', array($item_id));
				$itempool_id = $this->pdh->get('item', 'itempool_id', array($item_id));
				$item_date = $this->pdh->get('item', 'date', array($item_id));
				if(!isset($this->fl_item_dates['single'][$member_id]['total']['last']['date']) || $item_date > $this->fl_item_dates['single'][$member_id]['total']['last']['date']){
					$this->fl_item_dates['single'][$member_id]['total']['last']['date'] = $item_date;
					$this->fl_item_dates['single'][$member_id]['total']['last']['item_id'] = $item_id;
				}
				if(!isset($this->fl_item_dates['multi'][$main_ids[$member_id]]['total']['last']['date']) || $item_date > $this->fl_item_dates['multi'][$main_ids[$member_id]]['total']['last']['date']){
					$this->fl_item_dates['multi'][$main_ids[$member_id]]['total']['last']['date'] = $item_date;
					$this->fl_item_dates['multi'][$main_ids[$member_id]]['total']['last']['item_id'] = $item_id;
				}
				if(!isset($this->fl_item_dates['single'][$member_id]['itempool'][$itempool_id]['last']['date']) || $item_date > $this->fl_item_dates['single'][$member_id]['itempool'][$itempool_id]['last']['date']) {
					$this->fl_item_dates['single'][$member_id]['itempool'][$itempool_id]['last']['date'] = $item_date;
					$this->fl_item_dates['single'][$member_id]['itempool'][$itempool_id]['last']['item_id'] = $item_id;
				}
				if(!isset($this->fl_item_dates['multi'][$main_ids[$member_id]]['itempool'][$itempool_id]['last']['date']) || $item_date > $this->fl_item_dates['multi'][$main_ids[$member_id]]['itempool'][$itempool_id]['last']['date']) {
					$this->fl_item_dates['multi'][$main_ids[$member_id]]['itempool'][$itempool_id]['last']['date'] = $item_date;
					$this->fl_item_dates['multi'][$main_ids[$member_id]]['itempool'][$itempool_id]['last']['item_id'] = $item_id;
				}
				if(!isset($this->fl_item_dates['single'][$member_id]['total']['first']['date']) || $item_date < $this->fl_item_dates['single'][$member_id]['total']['first']['date']){
					$this->fl_item_dates['single'][$member_id]['total']['first']['date'] = $item_date;
					$this->fl_item_dates['single'][$member_id]['total']['first']['item_id'] = $item_id;
				}
				if(!isset($this->fl_item_dates['multi'][$main_ids[$member_id]]['total']['first']['date']) || $item_date < $this->fl_item_dates['multi'][$main_ids[$member_id]]['total']['first']['date']){
					$this->fl_item_dates['multi'][$main_ids[$member_id]]['total']['first']['date'] = $item_date;
					$this->fl_item_dates['multi'][$main_ids[$member_id]]['total']['first']['item_id'] = $item_id;
				}
				if(!isset($this->fl_item_dates['single'][$member_id]['itempool'][$itempool_id]['first']['date']) || $item_date < $this->fl_item_dates['single'][$member_id]['itempool'][$itempool_id]['first']['date']){
					$this->fl_item_dates['single'][$member_id]['itempool'][$itempool_id]['first']['date'] = $item_date;
					$this->fl_item_dates['single'][$member_id]['itempool'][$itempool_id]['first']['item_id'] = $item_id;
				}
				if(!isset($this->fl_item_dates['multi'][$main_ids[$member_id]]['itempool'][$itempool_id]['first']['date']) || $item_date < $this->fl_item_dates['multi'][$main_ids[$member_id]]['itempool'][$itempool_id]['first']['date']){
					$this->fl_item_dates['multi'][$main_ids[$member_id]]['itempool'][$itempool_id]['first']['date'] = $item_date;
					$this->fl_item_dates['multi'][$main_ids[$member_id]]['itempool'][$itempool_id]['first']['item_id'] = $item_id;
				}
				if(is_array($itempools[$itempool_id])) {
					foreach($itempools[$itempool_id] as $mdkp_id){
						if(!isset($this->fl_item_dates['single'][$member_id]['mdkp'][$mdkp_id]['last']['date']) || $item_date > $this->fl_item_dates['single'][$member_id]['mdkp'][$mdkp_id]['last']['date']){
							$this->fl_item_dates['single'][$member_id]['mdkp'][$mdkp_id]['last']['date'] = $item_date;
							$this->fl_item_dates['single'][$member_id]['mdkp'][$mdkp_id]['last']['item_id'] = $item_id;
							}
						if(!isset($this->fl_item_dates['multi'][$main_ids[$member_id]]['mdkp'][$mdkp_id]['last']['date']) || $item_date > $this->fl_item_dates['multi'][$main_ids[$member_id]]['mdkp'][$mdkp_id]['last']['date']){
							$this->fl_item_dates['multi'][$main_ids[$member_id]]['mdkp'][$mdkp_id]['last']['date'] = $item_date;
							$this->fl_item_dates['multi'][$main_ids[$member_id]]['mdkp'][$mdkp_id]['last']['item_id'] = $item_id;
						}
						if(!isset($this->fl_item_dates['single'][$member_id]['mdkp'][$mdkp_id]['first']['date']) || $item_date < $this->fl_item_dates['single'][$member_id]['mdkp'][$mdkp_id]['first']['date']){
							$this->fl_item_dates['single'][$member_id]['mdkp'][$mdkp_id]['first']['date'] = $item_date;
							$this->fl_item_dates['single'][$member_id]['mdkp'][$mdkp_id]['first']['item_id'] = $item_id;
						}
						if(!isset($this->fl_item_dates['multi'][$main_ids[$member_id]]['mdkp'][$mdkp_id]['first']['date']) || $item_date < $this->fl_item_dates['multi'][$main_ids[$member_id]]['mdkp'][$mdkp_id]['first']['date']){
							$this->fl_item_dates['multi'][$main_ids[$member_id]]['mdkp'][$mdkp_id]['first']['date'] = $item_date;
							$this->fl_item_dates['multi'][$main_ids[$member_id]]['mdkp'][$mdkp_id]['first']['item_id'] = $item_id;
						}
					}
				}
			}
			$this->pdc->put('pdh_fl_item_dates', $this->fl_item_dates);
		}

		public function get_first_raid($member_id, $mdkp_id=null, $with_twink=true){
			$with_twink = ($with_twink) ? 'multi' : 'single';
			if($mdkp_id == null){
				return (!isset($this->fl_raid_dates[$with_twink][$member_id]['total']['first_date'])) ? 0 : $this->fl_raid_dates[$with_twink][$member_id]['total']['first_date'];
			} else {
				return (!isset($this->fl_raid_dates[$with_twink][$member_id]['mdkp'][$mdkp_id]['first_date'])) ? 0 : $this->fl_raid_dates[$with_twink][$member_id]['mdkp'][$mdkp_id]['first_date'];
			}
		}

		public function get_html_first_raid($member_id, $mdkp_id=null, $with_twink=true){
			return $this->time->user_date($this->get_first_raid($member_id, $mdkp_id, $with_twink));
		}

		public function get_last_raid($member_id, $mdkp_id=null, $with_twink=true){
			$with_twink = ($with_twink) ? 'multi' : 'single';
			if($mdkp_id == null AND isset($this->fl_raid_dates[$with_twink][$member_id]['total']['last_date'])){
				return (!isset($this->fl_raid_dates[$with_twink][$member_id]['total']['last_date'])) ? 0 : $this->fl_raid_dates[$with_twink][$member_id]['total']['last_date'];
			} elseif($mdkp_id AND isset($this->fl_raid_dates[$with_twink][$member_id]['mdkp'][$mdkp_id]['last_date'])) {
				return (!isset($this->fl_raid_dates[$with_twink][$member_id]['mdkp'][$mdkp_id]['last_date'])) ? 0 : $this->fl_raid_dates[$with_twink][$member_id]['mdkp'][$mdkp_id]['last_date'];
			}
			return false;
		}

		public function get_html_last_raid($member_id, $mdkp_id=null, $with_twink=true){
			return $this->time->user_date($this->get_last_raid($member_id, $mdkp_id, $with_twink));
		}

		public function get_first_item_date($member_id, $mdkp_id=null, $with_twink=true){
			$with_twink = ($with_twink) ? 'multi' : 'single';
			if($mdkp_id == null AND isset($this->fl_item_dates[$with_twink][$member_id]['total']['first']['date'])){
				return (!isset($this->fl_item_dates[$with_twink][$member_id]['total']['first']['date'])) ? 0 : $this->fl_item_dates[$with_twink][$member_id]['total']['first']['date'];
			}elseif($mdkp_id AND isset($this->fl_item_dates[$with_twink][$member_id]['mdkp'][$mdkp_id]['first']['date'])) {
				return (!isset($this->fl_item_dates[$with_twink][$member_id]['mdkp'][$mdkp_id]['first']['date'])) ? 0 : $this->fl_item_dates[$with_twink][$member_id]['mdkp'][$mdkp_id]['first']['date'];
			}
			return false;
		}

		public function get_html_first_item_date($member_id, $mdkp_id=null, $with_twink=true){
			return $this->time->user_date($this->get_first_item_date($member_id, $mdkp_id, $with_twink));
		}

		public function get_last_item_date($member_id, $mdkp_id=null, $with_twink=true){
			$with_twink = ($with_twink) ? 'multi' : 'single';
			if($mdkp_id == null){
				return (isset($this->fl_item_dates[$with_twink][$member_id]['total']['last']['date'])) ? $this->fl_item_dates[$with_twink][$member_id]['total']['last']['date'] : 0;
			} else {
				return (isset($this->fl_item_dates[$with_twink][$member_id]['mdkp'][$mdkp_id]['last']['date'])) ? $this->fl_item_dates[$with_twink][$member_id]['mdkp'][$mdkp_id]['last']['date'] : 0;
			}
			return false;
		}
		
		public function get_last_item($member_id, $mdkp_id=null, $with_twink=true){
			$with_twink = ($with_twink) ? 'multi' : 'single';
			if($mdkp_id == null AND isset($this->fl_item_dates[$with_twink][$member_id]['total']['first']['item_id'])){
				return $this->fl_item_dates[$with_twink][$member_id]['total']['first']['item_id'];
			} elseif($mdkp_id AND isset($this->fl_item_dates[$with_twink][$member_id]['mdkp'][$mdkp_id]['first']['item_id'])) {
				return $this->fl_item_dates[$with_twink][$member_id]['mdkp'][$mdkp_id]['first']['item_id'];
			}
			return false;
		}

		public function get_html_last_item_date($member_id, $mdkp_id=null, $with_twink=true){
			return $this->time->user_date($this->get_last_item_date($member_id, $mdkp_id, $with_twink));
		}

		public function get_first_item_name($member_id, $mdkp_id=null, $with_twink=true){
			$with_twink = ($with_twink) ? 'multi' : 'single';
			if($mdkp_id == null AND isset($this->fl_item_dates[$with_twink][$member_id]['total']['first']['item_id'])){
				return $this->pdh->get('item', 'name', array($this->fl_item_dates[$with_twink][$member_id]['total']['first']['item_id']));
			} elseif($mdkp_id AND isset($this->fl_item_dates[$with_twink][$member_id]['mdkp'][$mdkp_id]['first']['item_id'])) {
				return $this->pdh->get('item', 'name', array($this->fl_item_dates[$with_twink][$member_id]['mdkp'][$mdkp_id]['first']['item_id']));
			}
			return false;
		}

		public function get_html_first_item_name($member_id, $mdkp_id=null, $with_twink=true){
			$with_twink = ($with_twink) ? 'multi' : 'single';
			infotooltip_js();
			if($mdkp_id == null AND isset($this->fl_item_dates[$with_twink][$member_id]['total']['first']['item_id'])){
				return $this->pdh->get('item', 'itt_itemname', array($this->fl_item_dates[$with_twink][$member_id]['total']['first']['item_id']));
			} elseif($mdkp_id AND isset($this->fl_item_dates[$with_twink][$member_id]['mdkp'][$mdkp_id]['first']['item_id'])) {
				return $this->pdh->get('item', 'itt_itemname', array($this->fl_item_dates[$with_twink][$member_id]['mdkp'][$mdkp_id]['first']['item_id']));
			}
			return false;
		}

		public function get_last_item_name($member_id, $mdkp_id=null, $with_twink=true){
			$with_twink = ($with_twink) ? 'multi' : 'single';
			if($mdkp_id == null AND isset($this->fl_item_dates[$with_twink][$member_id]['total']['last']['item_id'])){
				return $this->pdh->get('item', 'name', array($this->fl_item_dates[$with_twink][$member_id]['total']['last']['item_id']));
			} elseif (isset($this->fl_item_dates[$with_twink][$member_id]['mdkp'][$mdkp_id]['last']['item_id'])) {
				return $this->pdh->get('item', 'name', array($this->fl_item_dates[$with_twink][$member_id]['mdkp'][$mdkp_id]['last']['item_id']));
			}
			return false;
		}

		public function get_html_last_item_name($member_id, $mdkp_id=null, $with_twink=true){
			$with_twink = ($with_twink) ? 'multi' : 'single';
			infotooltip_js();
			if($mdkp_id == null AND isset($this->fl_item_dates[$with_twink][$member_id]['total']['last']['item_id'])){
				return $this->pdh->get('item', 'itt_itemname', array($this->fl_item_dates[$with_twink][$member_id]['total']['last']['item_id']));
			} elseif($mdkp_id AND isset($this->fl_item_dates[$with_twink][$member_id]['mdkp'][$mdkp_id]['last']['item_id'])) {
				return $this->pdh->get('item', 'itt_itemname', array($this->fl_item_dates[$with_twink][$member_id]['mdkp'][$mdkp_id]['last']['item_id']));
			}
			return false;
		}
	}//end class
}//end if
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_r_member_dates', pdh_r_member_dates::__shortcuts());
?>