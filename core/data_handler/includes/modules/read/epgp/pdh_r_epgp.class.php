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

if ( !class_exists( "pdh_r_epgp" ) ) {
	class pdh_r_epgp extends pdh_r_generic{
		public static function __shortcuts() {
		$shortcuts = array('pdc', 'pdh', 'html', 'user');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public $default_lang = 'english';
		public $epgp;

		public $hooks = array(
			'adjustment_update',
			'event_update',
			'item_update',
			'member_update',
			'raid_update',
			'multidkp_update',
		);

		public $presets = array(
			'ep'		=> array('ep', array('%member_id%', '%dkp_id%', 1, '%with_twink%'), array('%dkp_id%')),
			'gp'		=> array('gp', array('%member_id%', '%dkp_id%', 1, '%with_twink%'), array('%dkp_id%')),
			'epgp'		=> array('epgp', array('%member_id%', '%dkp_id%', 1, '%with_twink%'), array('%dkp_id%')),
			'epgp_all'	=> array('epgp', array('%member_id%', '%ALL_IDS%', 1, '%with_twink%'),	array('%ALL_IDS%', true, true)),
		);

		public $detail_twink = array(
			'ep' 			=> 'summed_up',
			'gp' 			=> 'summed_up',
			'epgp' 			=> 'summed_up',
		);

		public function reset(){
			$this->pdc->del('pdh_epgp_table');
			$this->epgp = NULL;
		}

		public function init(){
		}

		public function init_epgp($with_twink=true){
			//cached data not outdated?
			$this->epgp = $this->pdc->get('pdh_epgp_table');
			if($this->epgp !== NULL){
				return true;
			}
			$this->epgp = array();
			foreach($this->pdh->get('member', 'id_list', array(false, false)) as $member_id){
				foreach($this->pdh->get('multidkp',  'id_list', array()) as $mdkp_id){
					$this->epgp[$member_id][$mdkp_id]['ep'] = $this->calculate_ep($member_id, $mdkp_id, $with_twink);
					$this->epgp[$member_id][$mdkp_id]['epgp'] = $this->calculate_epgp($member_id, $mdkp_id, $with_twink);
				}
			}
			$this->pdc->put('pdh_epgp_table', $this->epgp, null);
		}

		public function calculate_epgp($member_id, $multidkp_id, $with_twink){
			$ep		= $this->get_ep($member_id, $multidkp_id, false, $with_twink);
			$gp		= $this->get_gp($member_id, $multidkp_id, false, $with_twink);
			$bp 	= intval($this->pdh->get_layout_config('base_points'));
			return $epgp = (($gp + $bp) == 0)? $ep : ($ep/($gp + $bp));
		}

		public function calculate_ep($member_id, $multidkp_id, $with_twink=true){
			return $this->pdh->get('points', 'earned', array($member_id, $multidkp_id, 0, $with_twink)) + $this->pdh->get('points', 'adjustment', array($member_id, $multidkp_id, 0, $with_twink));
		}

		public function get_ep($member_id, $multidkp_id, $round = true, $with_twink=true){
			if(!isset($this->epgp[$member_id])){
				$this->init_epgp($with_twink);
			}

			$ep = $this->epgp[$member_id][$multidkp_id]['ep'];
			return ($round == true) ? runden($ep) : $ep;
		}

		public function get_html_ep($member_id, $multidkp_id){
			return '<span class="positive">'.$this->get_ep($member_id, $multidkp_id).'</span>';
		}

		public function get_gp($member_id, $multidkp_id, $round = true, $with_twink=true){
			$gp = $this->pdh->get('points', 'spent', array($member_id, $multidkp_id, 0, 0, $with_twink));
			return ($round == true) ? runden($gp) : $gp;
		}

		public function get_html_gp($member_id, $multidkp_id){
			return '<span class="negative">'.$this->get_gp($member_id, $multidkp_id).'</span>';
		}

		public function get_epgp($member_id, $multidkp_id, $round = true, $with_twink=true){
			if(!isset($this->epgp[$member_id])){
				$this->init_epgp($with_twink);
			}
			$epgp = $this->epgp[$member_id][$multidkp_id]['epgp'];
			return ($round == true) ? runden($epgp) : $epgp;
		}

		public function get_html_epgp($member_id, $multidkp_id){
			return '<span class="'.color_item($this->get_epgp($member_id, $multidkp_id)).'">'.$this->get_epgp($member_id, $multidkp_id).'</span>';
		}

		public function get_html_caption_epgp($mdkpid, $showname = false, $showtooltip = false){
			if($showname){
				$text = $this->pdh->get('multidkp', 'name', array($mdkpid));
			}else{
				$text = 'PR';
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
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_r_epgp', pdh_r_epgp::__shortcuts());
?>