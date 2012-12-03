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

if ( !class_exists( "pdh_r_effective_dkp" ) ) {
	class pdh_r_effective_dkp extends pdh_r_generic{
		public static function __shortcuts() {
		$shortcuts = array('pdc', 'pdh', 'html','user');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public $default_lang = 'english';

		public $edkp;
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
			'effective_dkp_30'	=> array('effective_dkp', array('%member_id%', '%dkp_id%', 30, '%with_twink%'), array('%dkp_id%', 30, false, true)),
			'effective_dkp_all'	=> array('effective_dkp', array('%member_id%', '%ALL_IDS%', '%edkp_days%', '%with_twink%'), array('%ALL_IDS%', '%edkp_days%', true, true)),
			'effective_dkp'		=> array('effective_dkp', array('%member_id%', '%dkp_id%', '%edkp_days%', '%with_twink%'), array('%dkp_id%', '%edkp_days%', false, true)),
		);

		public $detail_twink = array(
			'effective_dkp' 	=> 'summed_up',
		);

		public function reset(){
			//we'll need the appropiate function
			$this->pdc->del_prefix('pdh_edkp');
			$this->edkp = NULL;
		}

		public function init(){}

		public function init_effective_dkp($period, $with_twink = true){
			//cached data not outdated?
			$this->edkp[$period] = $this->pdc->get('pdh_edkp_'.$period);
			if($this->edkp[$period] != null){
				return true;
			}

			$this->edkp[$period] = array();
			foreach($this->pdh->get('member', 'id_list', array(false, false)) as $member_id){
				foreach($this->pdh->get('multidkp',  'id_list') as $mdkp_id){
					$this->edkp[$period][$member_id][$mdkp_id] = $this->calculate_effective_dkp($member_id, $mdkp_id, $period, $with_twink);
				}
			}

			//cache it and let it expire at midnight
			$stm = 86400-((time()-mktime(0,0,0,1,1,1970))%86400);
			$this->pdc->put('pdh_edkp_'.$period, $this->edkp[$period], $stm);
		}

		public function calculate_effective_dkp($member_id, $multidkp_id, $time_period, $with_twink = true){
			$earned		= $this->pdh->get('points', 'earned', array($member_id, $multidkp_id, 0, $with_twink));
			$adjustment	= $this->pdh->get('points', 'adjustment', array($member_id, $multidkp_id, 0, $with_twink));
			$spent		= $this->pdh->get('points', 'spent', array($member_id, $multidkp_id, 0, 0, $with_twink));
			$attendance	= $this->pdh->get('member_attendance', 'attendance', array($member_id, $multidkp_id, $time_period, $with_twink));
			return ($earned+$adjustment-$spent)*$attendance;
		}

		public function get_effective_dkp($member_id, $multidkp_id, $time_period, $with_twink = true){
			if(!isset($this->edkp[$time_period])){
				$this->init_effective_dkp($time_period, $with_twink);
			}
			return runden($this->edkp[$time_period][$member_id][$multidkp_id]);
		}

		public function get_html_effective_dkp($member_id, $multidkp_id, $time_period, $with_twink = true){
			return '<span class="'.color_item($this->get_effective_dkp($member_id, $multidkp_id, $time_period)).'">'.$this->get_effective_dkp($member_id, $multidkp_id, $time_period).'</span>';
		}

		public function get_caption_effective_dkp($mdkp_id, $time_period){
			return sprintf($this->pdh->get_lang('effective_dkp', 'effective_dkp'), $time_period);
		}

		public function get_html_caption_effective_dkp($mdkpid, $time_period, $showname, $showtooltip){
			if($showname){
				$text	= $this->pdh->get('multidkp', 'name', array($mdkpid))."($time_period)";
			}else{
				$text	= sprintf($this->pdh->get_lang('effective_dkp', 'effective_dkp'), $time_period);
			}

			if($showtooltip){
				$tooltip	= $this->user->lang('events').": <br />";
				$events		= $this->pdh->get('multidkp', 'event_ids', array($mdkpid));
				if(is_array($events))
				foreach($events as $event_id){
					$tooltip	.= $this->pdh->get('event', 'name', array($event_id))."<br />";
				}
				$text	= $this->html->ToolTip($tooltip, $text);
			}

			return $text;
		}
	}//end class
}//end if
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_r_effective_dkp', pdh_r_effective_dkp::__shortcuts());
?>