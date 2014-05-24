<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2010
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
	header('HTTP/1.0 404 Not Found');exit;
}

class content_export extends gen_class {
	public static $shortcuts = array('config', 'pdh', 'time');
	
	private $timestamp;
	private $date_created;
	private $presets = array();
	
	public function __construct(){
		$this->timestamp		= $this->time->time;
		$this->date_created		= date("d.m.y G:i:s");
		$this->presets = array(
			array('name' => 'earned', 'sort' => true, 'th_add' => '', 'td_add' => ''),
			array('name' => 'spent', 'sort' => true, 'th_add' => '', 'td_add' => ''),
			array('name' => 'adjustment', 'sort' => true, 'th_add' => '', 'td_add' => ''),
			array('name' => 'current', 'sort' => true, 'th_add' => '', 'td_add' => ''),
		);
	}
	
	public function export($withMemberItems = true, $withMemberAdjustments = false, $blnExcludeHTML = false){
		$arrPresets = array();
		foreach ($this->presets as $preset){
			$pre = $this->pdh->pre_process_preset($preset['name'], $preset);
				if(empty($pre))
					continue;
					
			$arrPresets[$pre[0]['name']] = $pre[0];
		}
	
		$out = array();
		
		$out['eqdkp'] = array(
			'name'				=> unsanitize($this->config->get('guildtag')),
			'guild'				=> unsanitize($this->config->get('guildtag')),
			'dkp_name'			=> $this->config->get('dkp_name'),
			'version'			=> $this->config->get('plus_version'),
			'layout'			=> $this->config->get('eqdkp_layout'),
			'base_layout'		=> $this->pdh->get_eqdkp_base_layout($this->config->get('eqdkp_layout')),
		);
		$out['game'] = array(
			'name'				=> $this->config->get('default_game'),
			'version'			=> $this->config->get('game_version'),
			'language'			=> $this->config->get('game_language'),
			'server_name'		=> unsanitize($this->config->get('uc_servername')),
			'server_loc'		=> $this->config->get('uc_server_loc'),
		);				
		$out['info'] = array(
			'with_twink'		=> (intval($this->config->get('pk_show_twinks'))) ? 0 : 1,
			'date'				=> $this->date_created,
			'timestamp'			=> $this->timestamp,
			'total_players'		=> count($this->pdh->get('member', 'id_list')),
			'total_items'		=> count($this->pdh->get('item', 'id_list')),
		);

		$mdkps = $this->pdh->get('multidkp', 'id_list');
		
		//Alle Member
		$total_points = 0;
		$members = $this->pdh->sort($this->pdh->get('member', 'id_list'), 'member', 'name');
		if (is_array($members) && count($members) > 0) {
			foreach ($members as $member){		
				$points = array();
				foreach ($mdkps as $mdkp){				
					$points['multidkp_points:'.$mdkp] = array(
						'multidkp_id'	=> $mdkp,
						'points_current' => (isset($arrPresets['current'])) ? $this->pdh->get($arrPresets['current'][0], $arrPresets['current'][1], $arrPresets['current'][2], array('%dkp_id%' => $mdkp, '%member_id%' => $member, '%with_twink%' => false)) : false,
						'points_current_with_twink' => (isset($arrPresets['current'])) ? $this->pdh->get($arrPresets['current'][0], $arrPresets['current'][1], $arrPresets['current'][2], array('%dkp_id%' => $mdkp, '%member_id%' => $member, '%with_twink%' => true)) : false,
						'points_earned'	=> (isset($arrPresets['earned'])) ? $this->pdh->get($arrPresets['earned'][0], $arrPresets['earned'][1], $arrPresets['earned'][2], array('%dkp_id%' => $mdkp, '%member_id%' => $member, '%with_twink%' => false)) : false,
						'points_earned_with_twink' => (isset($arrPresets['earned'])) ? $this->pdh->get($arrPresets['earned'][0], $arrPresets['earned'][1], $arrPresets['earned'][2], array('%dkp_id%' => $mdkp, '%member_id%' => $member, '%with_twink%' => true)) : false,
						'points_spent'	=> (isset($arrPresets['spent'])) ? $this->pdh->get($arrPresets['spent'][0], $arrPresets['spent'][1], $arrPresets['spent'][2], array('%dkp_id%' => $mdkp, '%member_id%' => $member, '%with_twink%' => false)) : false,
						'points_spent_with_twink'	=> (isset($arrPresets['spent'])) ? $this->pdh->get($arrPresets['spent'][0], $arrPresets['spent'][1], $arrPresets['spent'][2], array('%dkp_id%' => $mdkp, '%member_id%' => $member, '%with_twink%' => true)) : false,
						'points_adjustment'	=> (isset($arrPresets['adjustment'])) ? $this->pdh->get($arrPresets['adjustment'][0], $arrPresets['adjustment'][1], $arrPresets['adjustment'][2], array('%dkp_id%' => $mdkp, '%member_id%' => $member, '%with_twink%' => false)) : false,
						'points_adjustment_with_twink'	=> (isset($arrPresets['adjustment'])) ? $this->pdh->get($arrPresets['adjustment'][0], $arrPresets['adjustment'][1], $arrPresets['adjustment'][2], array('%dkp_id%' => $mdkp, '%member_id%' => $member, '%with_twink%' => true)) : false,
						
					);
					if (!$blnExcludeHTML){
						$points['multidkp_points:'.$mdkp]['points_current_html'] 			= (isset($arrPresets['current'])) ? $this->pdh->geth($arrPresets['current'][0], $arrPresets['current'][1], $arrPresets['current'][2], array('%dkp_id%' => $mdkp, '%member_id%' => $member, '%with_twink%' => false)) : false;
						$points['multidkp_points:'.$mdkp]['points_current_with_twink_html'] = (isset($arrPresets['current'])) ? $this->pdh->geth($arrPresets['current'][0], $arrPresets['current'][1], $arrPresets['current'][2], array('%dkp_id%' => $mdkp, '%member_id%' => $member, '%with_twink%' => true)) : false;
						$points['multidkp_points:'.$mdkp]['points_earned_html']				= (isset($arrPresets['earned'])) ? $this->pdh->geth($arrPresets['earned'][0], $arrPresets['earned'][1], $arrPresets['earned'][2], array('%dkp_id%' => $mdkp, '%member_id%' => $member, '%with_twink%' => false)) : false;
						$points['multidkp_points:'.$mdkp]['points_earned_with_twink_html']	= (isset($arrPresets['earned'])) ? $this->pdh->geth($arrPresets['earned'][0], $arrPresets['earned'][1], $arrPresets['earned'][2], array('%dkp_id%' => $mdkp, '%member_id%' => $member, '%with_twink%' => true)) : false;
						$points['multidkp_points:'.$mdkp]['points_spent_html']				= (isset($arrPresets['spent'])) ? $this->pdh->geth($arrPresets['spent'][0], $arrPresets['spent'][1], $arrPresets['spent'][2], array('%dkp_id%' => $mdkp, '%member_id%' => $member, '%with_twink%' => false)) : false;
						$points['multidkp_points:'.$mdkp]['points_spent_with_twink_html']	= (isset($arrPresets['spent'])) ? $this->pdh->geth($arrPresets['spent'][0], $arrPresets['spent'][1], $arrPresets['spent'][2], array('%dkp_id%' => $mdkp, '%member_id%' => $member, '%with_twink%' => true)) : false;
						$points['multidkp_points:'.$mdkp]['points_adjustment_html']			= (isset($arrPresets['adjustment'])) ? $this->pdh->geth($arrPresets['adjustment'][0], $arrPresets['adjustment'][1], $arrPresets['adjustment'][2], array('%dkp_id%' => $mdkp, '%member_id%' => $member, '%with_twink%' => false)) : false;
						$points['multidkp_points:'.$mdkp]['points_adjustment_with_twink_html']	= (isset($arrPresets['adjustment'])) ? $this->pdh->geth($arrPresets['adjustment'][0], $arrPresets['adjustment'][1], $arrPresets['adjustment'][2], array('%dkp_id%' => $mdkp, '%member_id%' => $member, '%with_twink%' => true)) : false;
					
					}
				}

				$items = array();
				if ($withMemberItems){
					$item_list = $this->pdh->get('item', 'itemids4memberid', array($member));
					foreach ($item_list as $item_id){
							$game_id = $this->pdh->get('item', 'game_itemid', array($item_id));
							$items['item:'.$item_id] = array(
								'game_id'		=> ($game_id) ? $game_id : 0,
								'name'			=> $this->pdh->get('item', 'name', array($item_id)),
								'value'			=> $this->pdh->get('item', 'value', array($item_id)),
								'itempool_id'	=> $this->pdh->get('item', 'itempool_id', array($item_id)),
							);
					}
				}
				
				$out['players']['player:'.$member] = array(
					'id'			=> $member,
					'name'			=> unsanitize($this->pdh->get('member', 'name', array($member))),
					'active'		=> $this->pdh->get('member', 'active', array($member)),
					'hidden'		=> $this->pdh->get('member', 'is_hidden', array($member)),
					'main_id'		=> $this->pdh->get('member', 'mainid', array($member)),
					'main_name'		=> unsanitize($this->pdh->get('member', 'mainname', array($member))),

					'class_id'		=> $this->pdh->get('member', 'classid', array($member)),
					'class_name'	=> $this->pdh->get('member', 'classname', array($member)),
					'race_id'		=> $this->pdh->get('member', 'raceid', array($member)),
					'race_name'		=> $this->pdh->get('member', 'racename', array($member)),

					'points'		=> $points,
					'items'			=> $items,
				);
				
				if ($withMemberAdjustments){
					$adjustments = array();
					$adj_list = $this->pdh->get('adjustment', 'adjsofmember', array($member));
					$i = 0;
					foreach($adj_list as $adj_id){
						$adjustments['adjustment:'.$adj_id] = array(
							'reason'	=> $this->pdh->get('adjustment', 'reason', array($adj_id)),
							'value'		=> $this->pdh->get('adjustment', 'value', array($adj_id)),
							'timestamp' => $this->pdh->get('adjustment', 'date', array($adj_id)),
							'event_id'	=> $this->pdh->get('adjustment', 'event', array($adj_id)),
						);
						$i++;
					}
					$out['players']['player:'.$member]['adjustments'] = $adjustments;
				}
			}
		} else {
			$out['players'] = '';
		}
	
		//Alle MultiDKP-Konten
		if (is_array($mdkps) && count($mdkps) > 0) {
			foreach ($mdkps as $mdkp){
				$event_ids		= $this->pdh->get('multidkp', 'event_ids', array($mdkp));
				$itempool_ids	= $this->pdh->get('multidkp', 'itempool_ids', array($mdkp));
				foreach ($itempool_ids as $pool){
					$itempools['itempool_id:'.$pool] = $pool;
				}

				$events = array();
				foreach ($event_ids as $event){
					$events['event:'.$event] = array(
						'id'	=> $event,
						'name'	=> $this->pdh->get('event', 'name', array($event)),
						'value'	=> $this->pdh->get('event', 'value', array($event))
					);
				}

				$out['multidkp_pools']['multidkp_pool:'.$mdkp] = array(
					'id'				=> $mdkp,
					'name'				=> unsanitize($this->pdh->get('multidkp', 'name', array($mdkp))),
					'desc'				=> unsanitize($this->pdh->get('multidkp', 'desc', array($mdkp))),
					'events'			=> $events,
					'mdkp_itempools'	=> $itempools,
				);
			}
		} else {
			$out['multidkp_pools'] = '';
		}
		
		//Alle Itempools
		$itempools = $this->pdh->get('itempool', 'id_list');
		if (is_array($itempools) && count($itempools) > 0) {
			foreach ($itempools as $itempool){
				$out['itempools']['itempool:'.$itempool] = array(
					'id'		=> $itempool,
					'name'		=> unsanitize($this->pdh->get('itempool', 'name', array($itempool))),
					'desc'		=> unsanitize($this->pdh->get('itempool', 'desc', array($itempool))),
				);
			}
		} else {
			$out['itempools'] = '';
		}
		
		return $out;
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_content_export', content_export::$shortcuts);
?>