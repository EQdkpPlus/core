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

if ( !class_exists( "pdh_r_suicide_kings" ) ) {
	class pdh_r_suicide_kings extends pdh_r_generic{
		public static function __shortcuts() {
		$shortcuts = array('pdc', 'pdh', 'user', 'html');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public $default_lang = 'english';
		public $sk_list;

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
			'sk_position_all'	=> array('position', array('%member_id%', '%ALL_IDS%', '%with_twink%'), array('%ALL_IDS%', true, true)),
			'sk_position'		=> array('position', array('%member_id%', '%dkp_id%', '%with_twink%'), array('%dkp_id%')),
		);

		public function reset(){
			$this->pdc->del('pdh_suicide_kings_table');
			$this->sk_list = NULL;
		}


		public function init(){
			//cached data not outdated?
			$this->sk_list = $this->pdc->get('pdh_suicide_kings_table');
			if($this->sk_list !== null){
				return true;
			}

			//base list for all mdkp pools
			$member_hash = array();
			$member_list = $this->pdh->get('member', 'id_list', array(false, false));
			$member_list = $this->pdh->sort($member_list, 'member', 'creation_date', 'asc');

			foreach($member_list as $member_id){
				$member_hash['single'][$member_id] = md5($this->pdh->get('member','name', array($member_id)));
				$intMainID = $this->pdh->get('member', 'mainid', array($member_id));
				$member_hash['multi'][$intMainID] = md5($this->pdh->get('member','name', array($intMainID)));
			}
			
			//With Twinks (mainchar only)
			foreach($this->pdh->get('multidkp',  'id_list', array()) as $mdkp_id){
				$tmp_memberarray = array('multi'=>$member_hash['multi'], 'single'=>$member_hash['single']);
				
				$sort_list_lastitemdate = array('multi'=>array(), 'single'=>array());
				$sort_list_lastitemid = array('multi'=>array(), 'single'=>array());
				$sort_list_member = array('multi'=>array(), 'single'=>array());
				$sort_list_lastraiddate = array('multi'=>array(), 'single'=>array());
				
				//---MULTI--------------------------------------------------------------------
				foreach($member_hash['multi'] as $member_id => $hash){
					//Get latest item date
					$latest_item = $this->pdh->get('member_dates', 'last_item_date', array($member_id, $mdkp_id, true));
					$last_raid =  $this->pdh->get('member_dates', 'last_raid', array($member_id, $mdkp_id, true));

					$sort_list_lastitemdate['multi'][$member_id] = ($latest_item) ? $this->pdh->get('member_dates', 'last_item_date', array($member_id, $mdkp_id, true)) : 0;
					$sort_list_lastitemid['multi'][$member_id] = ($latest_item) ? $this->pdh->get('member_dates', 'last_item', array($member_id, $mdkp_id, true)) : 0;
					$sort_list_lastraiddate['multi'][$member_id] = ($last_raid) ? $this->pdh->get('member_dates', 'last_raid', array($member_id, $mdkp_id, true)) : 0;
					
					$sort_list_member['multi'][] = $member_id;
					unset($tmp_memberarray['multi'][$member_id]);
				}

				array_multisort($sort_list_lastraiddate['multi'], SORT_DESC, $sort_list_lastitemdate['multi'], SORT_ASC, $sort_list_lastitemid['multi'], SORT_ASC, $sort_list_member['multi']);
				
				//Position for member with items
				$i = 1;
				foreach ($sort_list_member['multi'] as $member_id){
					$this->sk_list['multi'][$mdkp_id][$member_id] = $i++;
				}
				
				//---SINGLE--------------------------------------------------------------------
				foreach($member_hash['single'] as $member_id => $hash){
					//Get latest item date
					$latest_item = $this->pdh->get('member_dates', 'last_item_date', array($member_id, $mdkp_id, false));
					$last_raid =  $this->pdh->get('member_dates', 'last_raid', array($member_id, $mdkp_id, false));
					

					$sort_list_lastitemdate['single'][$member_id] = ($latest_item) ? $this->pdh->get('member_dates', 'last_item_date', array($member_id, $mdkp_id, false)): 0;
					$sort_list_lastitemid['single'][$member_id] = ($latest_item) ? $this->pdh->get('member_dates', 'last_item', array($member_id, $mdkp_id, false)): 0;
					$sort_list_lastraiddate['single'][$member_id] = ($latest_item) ? $this->pdh->get('member_dates', 'last_raid', array($member_id, $mdkp_id, false)): 0;
					
					$sort_list_member['single'][] = $member_id;
					unset($tmp_memberarray['single'][$member_id]);
					
				}
				array_multisort($sort_list_lastraiddate['single'], SORT_DESC, $sort_list_lastitemdate['single'], SORT_ASC, $sort_list_lastitemid['single'], SORT_ASC, $sort_list_member['single']);
				//Position for member with items
				$i = 1;
				foreach ($sort_list_member['single'] as $member_id){
					$this->sk_list['single'][$mdkp_id][$member_id] = $i++;
				}
			}

			$this->pdc->put('pdh_suicide_kings_table', $this->sk_list, null);
		}

		public function sort_item_list($a, $b){
			$compResult = $this->pdh->comp('item', 'date', 1, array($a), array($b));
			if ($compResult == 0){
				return ($a < $b) ? -1 : 1;
			}
			return $compResult;
		}

		public function get_position($member_id, $multidkp_id, $with_twink = true){
			$with_twink = ($with_twink) ? 'multi' : 'single';
			if ($with_twink == 'multi'){
				$member_id = ($this->pdh->get('member', 'is_main', array($member_id))) ? $member_id : $this->pdh->get('member', 'mainid', array($member_id));
			}
			return $this->sk_list[$with_twink][$multidkp_id][$member_id];
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
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_r_suicide_kings', pdh_r_suicide_kings::__shortcuts());
?>