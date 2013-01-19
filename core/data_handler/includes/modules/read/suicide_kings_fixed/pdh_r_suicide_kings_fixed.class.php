<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2007
 * Date:		$Date: 2013-01-09 20:51:38 +0100 (Mi, 09. Jan 2013) $
 * -----------------------------------------------------------------------
 * @author		$Author: godmod $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 12785 $
 *
 * $Id: pdh_r_suicide_kings_bottom.class.php 12785 2013-01-09 19:51:38Z godmod $
 */

if ( !defined('EQDKP_INC') ){
	die('Do not access this file directly.');
}

if ( !class_exists( "pdh_r_suicide_kings_fixed" ) ) {
	class pdh_r_suicide_kings_fixed extends pdh_r_generic{
		public static function __shortcuts() {
			$shortcuts = array('pdc', 'pdh', 'user', 'html');
			return array_merge(parent::$shortcuts, $shortcuts);
		}

		public $default_lang = 'english';
		private $sk_list = array();

		public $hooks = array(
			'adjustment_update',
			'event_update',
			'item_update',
			'member_update',
			'raid_update',
			'multidkp_update',
			'itempool_update'
		);

		public $presets = array(
			'sk_fixed_position_all'	=> array('position', array('%member_id%', '%ALL_IDS%', '%with_twink%'), array('%ALL_IDS%', true, true)),
			'sk_fixed_position'		=> array('position', array('%member_id%', '%dkp_id%', '%with_twink%'), array('%dkp_id%')),
		);

		public function reset(){
			$this->pdc->del('pdh_suicide_kings_fixed_table');
			$this->sk_list = NULL;
		}

		public function init(){
			//cached data not outdated?
			$this->sk_list = $this->pdc->get('pdh_suicide_kings_fixed_table');
			if($this->sk_list !== null){
				return true;
			}

			//base list for all mdkp pools
			$member_list = $this->pdh->get('member', 'id_list');
			$member2main = $this->pdh->aget('member', 'mainid', 0, array($member_list));
			$main2member = $this->pdh->aget('member', 'other_members', 0, array(array_unique($member2main)));
			$member_list = $this->pdh->sort($member_list, 'member', 'creation_date', 'asc');
			$main_list = array();
			foreach($member_list as $key => $member_id) {
				$main_list[$member2main[$member_id]] = $key;
			}
			$member_list = array_flip($member_list);
			
			// mdkp2event list
			$mdkplist = $this->pdh->aget('multidkp', 'event_ids', 0, array($this->pdh->get('multidkp',  'id_list', array())));
			// raid-event list sorted by date
			$raid_ids = $this->pdh->sort($this->pdh->get('raid', 'id_list'), 'raid', 'date', 'asc');
			$raidlist = $this->pdh->maget(array('raid', 'raid', 'item'), array('event', 'raid_attendees', 'itemsofraid'), 0, array($raid_ids));
			
			foreach($mdkplist as $mdkp_id => $events) {
				// initialise list
				if(!isset($this->sk_list['multi'][$mdkp_id])) $this->sk_list['multi'][$mdkp_id] = $main_list;
				if(!isset($this->sk_list['single'][$mdkp_id])) $this->sk_list['single'][$mdkp_id] = $member_list;
				// iterate through raids
				foreach($raidlist as $raid_id => $raid) {
					if(!in_array($raid['event'], $events)) continue;
					$temp_list = array();
					$redistribute = array();
					foreach($this->sk_list['single'][$mdkp_id] as $member_id => $posi) {
						if(!in_array($member_id, $raid['raid_attendees'])) continue;
						$temp_list['single'][] = $member_id;
						$redistribute['single'][] = $posi;
					}
					foreach($this->sk_list['multi'][$mdkp_id] as $main_id => $posi) {
						if(!in_array($main_id, $raid['raid_attendees'])) continue;
						if(!empty($main2member[$main_id])) {
							$cont = true;
							foreach($main2member[$main_id] as $member_id) {
								if(in_array($member_id, $raid['raid_attendees'])) {
									$cont = false;
									break;
								}
							}
							if($cont) continue;
						}
						$temp_list['multi'][] = $main_id;
						$redistribute['multi'][] = $posi;
					}
					pd($temp_list);
					$items = $this->pdh->aget('item', 'buyer', 0, array($this->pdh->sort($raid['itemsofraid'], 'item', 'date', 'asc')));
					foreach($items as $memberid) {
						$key = array_search($memberid, $temp_list['single']);
						unset($temp_list['single'][$key]);
						$temp_list['single'][] = $memberid;
						$key = array_search($member2main[$memberid], $temp_list['multi']);
						unset($temp_list['multi'][$key]);
						$temp_list['multi'][] = $member2main[$memberid];
					}
					$temp_list['single'] = array_values($temp_list['single']);
					foreach($temp_list['single'] as $key => $member_id) {
						$this->sk_list['single'][$mdkp_id][$member_id] = $redistribute['single'][$key];
					}
					$temp_list['multi'] = array_values($temp_list['multi']);
					foreach($temp_list['multi'] as $key => $member_id) {
						$this->sk_list['multi'][$mdkp_id][$member_id] = $redistribute['multi'][$key];
					}
				}
			}

			$this->pdc->put('pdh_suicide_kings_fixed_table', $this->sk_list, null);
		}

		public function get_position($member_id, $multidkp_id, $with_twink = true){
			$with_twink = ($with_twink) ? 'multi' : 'single';
			if ($with_twink == 'multi'){
				$member_id = ($this->pdh->get('member', 'is_main', array($member_id))) ? $member_id : $this->pdh->get('member', 'mainid', array($member_id));
			}
			return $this->sk_list[$with_twink][$multidkp_id][$member_id];
		}
		
		public function get_html_position($member_id, $multidkp_id, $with_twink=true) {
			return $this->get_position($member_id, $multidkp_id, $with_twink)+1;
		}

		public function get_html_caption_position($mdkpid, $showname = false, $showtooltip = false, $tt_options = array()){
			if($showname){
				$text = $this->pdh->get('multidkp', 'name', array($mdkpid));
			}else{
				$text = $this->pdh->get_lang('points', 'current');
			}

			if($showtooltip){
				$tooltip = $this->user->lang('events').": <br />";
				$events = $this->pdh->get('multidkp', 'event_ids', array($mdkpid));
				if(is_array($events)) foreach($events as $event_id) $tooltip .= $this->pdh->get('event', 'name', array($event_id))."<br />";
				$text = $this->html->ToolTip($tooltip, $text, '', $tt_options);
			}
			return $text;
		}

	}//end class
}//end if
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_r_suicide_kings_fixed', pdh_r_suicide_kings_fixed::__shortcuts());
?>