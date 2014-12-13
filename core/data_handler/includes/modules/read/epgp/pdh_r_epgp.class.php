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

if ( !defined('EQDKP_INC') ){
	die('Do not access this file directly.');
}

if ( !class_exists( "pdh_r_epgp" ) ) {
	class pdh_r_epgp extends pdh_r_generic{
	
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
			//cached data not outdated?
			$this->epgp = $this->pdc->get('pdh_epgp_table');
			if($this->epgp !== NULL){
				return true;
			}
			$this->epgp = array();
			foreach($this->pdh->get('member', 'id_list', array(false, false)) as $member_id){
				foreach($this->pdh->get('multidkp',  'id_list', array()) as $mdkp_id){
					$this->epgp['multi'][$member_id][$mdkp_id]['ep'] = $this->calculate_ep($member_id, $mdkp_id, true);
					$this->epgp['multi'][$member_id][$mdkp_id]['epgp'] = $this->calculate_epgp($member_id, $mdkp_id, true);
					$this->epgp['single'][$member_id][$mdkp_id]['ep'] = $this->calculate_ep($member_id, $mdkp_id, false);
					$this->epgp['single'][$member_id][$mdkp_id]['epgp'] = $this->calculate_epgp($member_id, $mdkp_id, false);
				}
			}
			$this->pdc->put('pdh_epgp_table', $this->epgp, null);
		}
		
		public function calculate_epgp($member_id, $multidkp_id, $with_twink){
			$ep		= $this->get_ep($member_id, $multidkp_id, false, $with_twink);
			$gp		= $this->get_gp($member_id, $multidkp_id, false, $with_twink);
			$bp 	= intval($this->pdh->get_layout_config('base_points'));
			$min_ep = intval($this->pdh->get_layout_config('min_ep'));
			$epgp	= (($gp + $bp) == 0) ? $ep : ($ep/($gp + $bp));
			
			return (($min_ep > 0) && ($ep < $min_ep)) ? 0 : $epgp;
		}

		public function calculate_ep($member_id, $multidkp_id, $with_twink=true){
			return $this->pdh->get('points', 'earned', array($member_id, $multidkp_id, 0, $with_twink)) + $this->pdh->get('points', 'adjustment', array($member_id, $multidkp_id, 0, $with_twink));
		}

		public function get_ep($member_id, $multidkp_id, $round = true, $with_twink=true){
			$single = ($with_twink) ? 'multi' : 'single';
			$ep = $this->epgp[$single][$member_id][$multidkp_id]['ep'];
			return ($round == true) ? runden($ep) : $ep;
		}

		public function get_html_ep($member_id, $multidkp_id, $round = true, $with_twink=true){
			return '<span class="positive">'.$this->get_ep($member_id, $multidkp_id, $round, $with_twink).'</span>';
		}

		public function get_gp($member_id, $multidkp_id, $round = true, $with_twink=true){
			$gp = $this->pdh->get('points', 'spent', array($member_id, $multidkp_id, 0, 0, $with_twink));
			return ($round == true) ? runden($gp) : $gp;
		}

		public function get_html_gp($member_id, $multidkp_id, $round = true, $with_twink=true){
			return '<span class="negative">'.$this->get_gp($member_id, $multidkp_id, $round, $with_twink).'</span>';
		}

		public function get_epgp($member_id, $multidkp_id, $round = true, $with_twink=true){
			$single = ($with_twink) ? 'multi' : 'single';
			$epgp = $this->epgp[$single][$member_id][$multidkp_id]['epgp'];
			return ($round == true) ? runden($epgp) : $epgp;
		}

		public function get_html_epgp($member_id, $multidkp_id, $round = true, $with_twink=true){
			return '<span class="'.color_item($this->get_epgp($member_id, $multidkp_id, $round, $with_twink)).'">'.$this->get_epgp($member_id, $multidkp_id, $round, $with_twink).'</span>';
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
				$text	= '<span class="coretip" data-coretip="'.$tooltip.'">'.$text.'</span>';
			}

			return $text;
		}
	}//end class
}//end if
?>