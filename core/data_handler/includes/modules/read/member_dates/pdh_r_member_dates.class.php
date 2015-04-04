<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2015 EQdkp-Plus Developer Team
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU Affero General Public License as published
 *	by the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU Affero General Public License for more details.
 *
 *	You should have received a copy of the GNU Affero General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if ( !defined('EQDKP_INC') )
{
die('Do not access this file directly.');
}

if ( !class_exists( "pdh_r_member_dates" ) ) {
	class pdh_r_member_dates extends pdh_r_generic{

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
			$member_list = $this->pdh->get('member', 'id_list', array(false, false, false));
			foreach($raid_ids as $raid_id){
				$date = $this->pdh->get('raid', 'date', array($raid_id));
				$attendees = $this->pdh->get('raid', 'raid_attendees', array($raid_id));
				$event_id = $this->pdh->get('raid', 'event', array($raid_id));
				$mdkpids = $this->pdh->get('multidkp', 'mdkpids4eventid', array($event_id));
				if(is_array($attendees)) {
					foreach($attendees as $attendee_id){
						if(!in_array($attendee_id, $member_list)) continue;
						
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
				$member_list = $this->pdh->get('member', 'id_list', array(false, false, false));
				if(!in_array($member_id, $member_list)) continue;	
				
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
				if(isset($itempools[$itempool_id]) && is_array($itempools[$itempool_id])) {
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
				return (!isset($this->fl_raid_dates[$with_twink][$member_id]['total']['last_date'])) ? 2147483647 : $this->fl_raid_dates[$with_twink][$member_id]['total']['last_date'];
			} elseif($mdkp_id AND isset($this->fl_raid_dates[$with_twink][$member_id]['mdkp'][$mdkp_id]['last_date'])) {
				return (!isset($this->fl_raid_dates[$with_twink][$member_id]['mdkp'][$mdkp_id]['last_date'])) ? 2147483647 : $this->fl_raid_dates[$with_twink][$member_id]['mdkp'][$mdkp_id]['last_date'];
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
?>