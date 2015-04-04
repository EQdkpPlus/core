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

class raids_pageobject extends pageobject {

	public function __construct() {
		$handler = array('r' => array('process' => 'display_raid'));
		parent::__construct(false, $handler, array());
		$this->process();
	}
	
	public function display_raid(){
		infotooltip_js();
		$raid_id = $this->in->get('r', 0);
	
		if ( $raid_id ){
			if(!in_array($raid_id, $this->pdh->get('raid', 'id_list')))
				message_die($this->user->lang('error_invalid_raid_provided'));
	
			// Attendees
			$attendees_ids = $this->pdh->get('raid', 'raid_attendees', array($raid_id));
			$attendee_copy = $attendees = array();
			foreach($attendees_ids as $attendee_id){
				$attendees[$attendee_id] = sanitize(($this->pdh->get('member', 'name', array($attendee_id))));
			}
			$attendee_copy = $attendees;
	
			// Get each attendee's rank
			foreach($attendees as $attendee_id => $attendee_name){
				$ranks[ $attendee_name ] = array(
						'prefix'	=> $this->pdh->get('rank', 'prefix', array($this->pdh->get('member', 'rankid', array($attendee_id)))),
						'suffix'	=> $this->pdh->get('rank', 'suffix', array($this->pdh->get('member', 'rankid', array($attendee_id)))),
				);
			}
	
			if ( count($attendees) > 0 ){
				// First get rid of duplicates and resort them just in case,
				// so we're sure they're displayed correctly
				$attendees = array_unique($attendees);
				sort($attendees);
				reset($attendees);
				$rows = ceil(sizeof($attendees) / $this->user->style['attendees_columns']);
	
				// First loop: iterate through the rows
				// Second loop: iterate through the columns as defined in template_config,
				// then "add" an array to $block_vars that contains the column definitions,
				// then assign the block vars.
				// Prevents one column from being assigned and the rest of the columns for
				// that row being blank
				for ( $i = 0; $i < $rows; $i++ ){
					$block_vars		= array();
					for ( $j = 0; $j < $this->user->style['attendees_columns']; $j++ ){
						$offset		= ($i + ($rows * $j));
						$attendee	= ( isset($attendees_ids[$offset]) ) ? $attendees_ids[$offset] : '';
	
						if($attendee != ''){
							$block_vars += array(
									'COLUMN'.$j.'_NAME' => $this->pdh->get('member', 'html_memberlink', array($attendee, $this->routing->simpleBuild('character'), '', false, false, true, true))
							);
	
						}else{
							$block_vars += array(
									'COLUMN'.$j.'_NAME' => ''
							);
						}
	
						// Are we showing this column?
						$s_column = 's_column'.$j;
						${$s_column} = true;
					}
					$this->tpl->assign_block_vars('attendees_row', $block_vars);
				}
				$column_width = floor(100 / $this->user->style['attendees_columns']);
			}else{
				message_die('Could not get raid attendee information.','Critical Error');
			}
	
			// Drops
			$loot_dist	= array();
			$items		= $this->pdh->get('item', 'itemsofraid', array($raid_id));
			$chartcolorsLootdisti = array();
			foreach($items as $item_id){
				$buyer_id	= (int)$this->pdh->get('item', 'buyer', array($item_id));
				$class_name	= $this->pdh->get('member', 'classname', array($buyer_id));
				$class_id	= (int)$this->pdh->get('member', 'classid', array($buyer_id));
	
				if(isset($loot_dist[$class_id])){
					$loot_dist[$class_id]['value']++;
				}else{
					$loot_dist[$class_id]	= array('value' => 1, 'name' => $class_name);
					$tmp_classcolor			= $this->game->get_class_color($class_id);
					$chartcolorsLootdisti[$class_id] = ($tmp_classcolor != '') ? $tmp_classcolor : 'gray';
				}
	
				$this->tpl->assign_block_vars('items_row', array(
						'BUYER'			=> $this->pdh->get('member', 'html_memberlink', array($buyer_id, $this->routing->simpleBuild('character'), '', false, false, true, true)),
						'ITEM'			=> $this->pdh->get('item', 'link_itt', array($item_id, $this->routing->simpleBuild('items'), '', false,false,false,false,false,true)),
						'VALUE'			=> runden($this->pdh->get('item', 'value', array($item_id))))
				);
			}
			ksort($loot_dist);
			ksort($chartcolorsLootdisti);
	
			// Class distribution
			$class_dist = array();
			$total_attendee_count = sizeof($attendee_copy);
			foreach($attendee_copy as $member_id => $member_name){
				$member_class		= $this->pdh->get('member', 'classname', array($member_id));
				$member_class_id	= $this->pdh->get('member', 'classid', array($member_id));
				if($member_name != ''){
					$html_prefix	= ( isset($ranks[$member_name]) ) ? $ranks[$member_name]['prefix'] : '';
					$html_suffix	= ( isset($ranks[$member_name]) ) ? $ranks[$member_name]['suffix'] : '';
	
					if(isset($class_dist[$member_class_id]['names']) && isset($class_dist[$member_class_id]['count'])){
						$class_dist[$member_class_id]['names'] .= ", " . $html_prefix . $member_name . $html_suffix ;
						$class_dist[$member_class_id]['count']++;
					}else{
						$class_dist[$member_class_id] = array(
								'names'	=> $html_prefix . $member_name . $html_suffix,
								'count'	=> 1
						);
					}
				}
			}
	
			unset($ranks);
	
			#Class distribution
			$chartarray = array();
			$chartcolors = array();
			foreach ( $class_dist as $class_id => $details ){
				$percentage		= ($total_attendee_count > 0) ? round(($details['count'] / $total_attendee_count) * 100) : 0;
				$class			= $this->game->get_name('primary', $class_id);
				$chartarray[]	= array('value' => $percentage, 'name' => (($class) ? $class : $this->user->lang('unknown'))." (".$class_dist[$class_id]['count']." - ".$percentage."%)");
				$chartcolors[]	= (strlen($this->game->get_class_color($class_id))) ? $this->game->get_class_color($class_id) : "gray";
	
				$this->tpl->assign_block_vars('class_row', array(
						'CLASS'			=> $this->game->decorate('primary', $class_id).' <span class="class_'.$class_id.'">'.(($class_id > 0) ? $class : $this->user->lang('unknown')).'</span>',
						'BAR'			=> $this->jquery->progressbar('bar_'.md5($class), $percentage, array('text' => '%percentage%')),
						'ATTENDEES'		=> $class_dist[$class_id]['names']
				));
			}
	
			$chartoptions	= array(
					'border'		=> '0.0',
					'piemargin'		=> 2,
					'datalabels'	=> true,
					'legend'		=> true,
					'background'	=> 'rgba(255, 255, 255, 0.1)'
			);
			$chartoptionsLootDistri = $chartoptions;
			if ($this->game->get_class_color(1) != ''){
				$chartoptions['color_array']			= $chartcolors;
				$chartoptionsLootDistri['color_array']	= $chartcolorsLootdisti;
			}
			unset($eq_classes);
	
			$vpre = $this->pdh->pre_process_preset('rvalue', array(), 0);
			$vpre[2][0] = $raid_id;
				
			//Items
			$arrItemListSettings = array(
					'name' => 'hptt_viewmember_itemlist',
					'table_main_sub' => '%item_id%',
					'table_subs' => array('%item_id%', '%link_url%', '%link_url_suffix%', '%raid_link_url%', '%raid_link_url_suffix%', '%itt_lang%', '%itt_direct%', '%onlyicon%', '%noicon%'),
					'page_ref' => 'viewcharacter.php',
					'show_numbers' => false,
					'show_select_boxes' => false,
					'show_detail_twink' => false,
					'table_sort_col' => 0,
					'table_sort_dir' => 'asc',
					'table_presets' => array(
							array('name' => 'ibuyerlink', 'sort' => true, 'th_add' => '', 'td_add' => ''),
							array('name' => 'ilink_itt', 'sort' => true, 'th_add' => '', 'td_add' => 'style="height:21px;"'),
							array('name' => 'ipoolname', 'sort' => true, 'th_add' => '', 'td_add' => ''),
							array('name' => 'idroprate', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					),
			);
			if (!$this->config->get('disable_points')) $arrItemListSettings['table_presets'][] = array('name' => 'ivalue', 'sort' => true, 'th_add' => '', 'td_add' => '');
			$hptt_page_settings	= $arrItemListSettings;
			$hptt				= $this->get_hptt($hptt_page_settings, $items, $items, array('%link_url%' => $this->routing->simpleBuild('items'), '%link_url_suffix%' => '', '%itt_lang%' => false, '%itt_direct%' => 0, '%onlyicon%' => 0, '%noicon%' => 0, '%raid_link_url%' => $this->routing->simpleBuild('raids'), '%raid_link_url_suffix%' => '', '%use_controller%' => true, '%member_link_url_suffix%' => '','%member_link_url%' => $this->routing->simpleBuild('character')), 'raid_'.$this->url_id, 'isort');
			$hptt->setPageRef($this->strPath);
			$this->tpl->assign_vars(array (
					'ITEM_OUT'			=> $hptt->get_html_table($this->in->get('isort'), '', null, false, sprintf($this->user->lang('viewitem_footcount'), count($items))),
			));
	
			//Adjustments
			if (!$this->config->get('disable_points')){
				$arrAdjListSettings = array(
						'name' => 'hptt_viewmember_adjlist',
						'table_main_sub' => '%adjustment_id%',
						'table_subs' => array('%adjustment_id%', '%raid_link_url%', '%raid_link_url_suffix%'),
						'page_ref' => 'viewcharacter.php',
						'show_numbers' => false,
						'show_select_boxes' => false,
						'show_detail_twink' => false,
						'table_sort_col' => 0,
						'table_sort_dir' => 'desc',
						'table_presets' => array(
								array('name' => 'adj_reason', 'sort' => true, 'th_add' => '', 'td_add' => ''),
								array('name' => 'adj_members', 'sort' => true, 'th_add' => 'width="70%"', 'td_add' => ''),
								array('name' => 'adj_value', 'sort' => true, 'th_add' => '', 'td_add' => 'nowrap="nowrap"'),
						),
				);
				$arrAdjustments = $this->pdh->get('adjustment', 'adjsofraid', array($raid_id, true));

				$hptt_page_settings = $arrAdjListSettings;
				$hptt = $this->get_hptt($hptt_page_settings, $arrAdjustments, $arrAdjustments, array('%raid_link_url%' => $this->routing->simpleBuild('raids'), '%raid_link_url_suffix%' => '', '%use_controller%' => true), 'raid_'.$this->url_id, 'asort');
				$hptt->setPageRef($this->strPath);
				$this->tpl->assign_vars(array (
						'ADJUSTMENT_OUT' 		=> $hptt->get_html_table($this->in->get('asort', ''),''),
						'S_ADJUSTMENTS'			=> count($arrAdjustments),
				));
			}
	
			$this->tpl->assign_vars(array(
					'L_MEMBERS_PRESENT_AT'	=> sprintf($this->user->lang('members_present_at'),
							$this->time->user_date($this->pdh->get('raid', 'date', array($raid_id)), false, false, true),
							$this->time->user_date($this->pdh->get('raid', 'date', array($raid_id)), false, true)
					),
	
					'EVENT_ICON'			=> $this->game->decorate('events', $this->pdh->get('raid', 'event', array($raid_id)), array(), 40),
					'EVENT_NAME'			=> stripslashes($this->pdh->get('raid', 'event_name', array($raid_id))),
	
					'S_COLUMN0'				=> ( isset($s_column0) ) ? true : false,
					'S_COLUMN1'				=> ( isset($s_column1) ) ? true : false,
					'S_COLUMN2'				=> ( isset($s_column2) ) ? true : false,
					'S_COLUMN3'				=> ( isset($s_column3) ) ? true : false,
					'S_COLUMN4'				=> ( isset($s_column4) ) ? true : false,
					'S_COLUMN5'				=> ( isset($s_column5) ) ? true : false,
					'S_COLUMN6'				=> ( isset($s_column6) ) ? true : false,
					'S_COLUMN7'				=> ( isset($s_column7) ) ? true : false,
					'S_COLUMN8'				=> ( isset($s_column8) ) ? true : false,
					'S_COLUMN9'				=> ( isset($s_column9) ) ? true : false,
	
					'COLUMN_WIDTH'			=> ( isset($column_width) ) ? $column_width : 0,
					'COLSPAN'				=> $this->user->style['attendees_columns'],
	
					'RAID_ADDED_BY'			=> ( $this->pdh->get('raid', 'added_by', array($raid_id)) != '' ) ? stripslashes($this->pdh->get('raid', 'added_by', array($raid_id))) : 'N/A',
					'RAID_UPDATED_BY'		=> ( $this->pdh->get('raid', 'updated_by', array($raid_id)) != '' ) ? stripslashes($this->pdh->get('raid', 'updated_by', array($raid_id))) : 'N/A',
					'S_RAID_UPDATED'		=> (strlen($this->pdh->get('raid', 'updated_by', array($raid_id)))),
					'RAID_NOTE'				=> ( $this->pdh->get('raid', 'note', array($raid_id)) != '' ) ? sanitize($this->pdh->get('raid', 'note', array($raid_id))) : '&nbsp;',
					'DKP_NAME'				=> $this->config->get('dkp_name'),
					'RAID_VALUE'			=> $this->pdh->geth($vpre[0], $vpre[1], $vpre[2]),//runden($this->pdh->get('raid', 'value', array($raid_id))),
					'ATTENDEES_FOOTCOUNT'	=> sprintf($this->user->lang('viewraid_attendees_footcount'), sizeof($attendees)),
					'ITEM_FOOTCOUNT'		=> sprintf($this->user->lang('viewitem_footcount'), sizeof($items)),
					'CLASS_PERCENT_CHART'	=> $this->jquery->charts('pie', 'class_dist', $chartarray, $chartoptions),
					'LOOT_PERCENT_CHART'	=> (count($loot_dist) > 0) ? $this->jquery->charts('pie', 'loot_dist', $loot_dist, $chartoptionsLootDistri) : '',
					'RAID_DATE'				=> $this->time->user_date($this->pdh->get('raid', 'date', array($raid_id)), true, false, true),
					'U_RAIDLIST'			=> $this->routing->build('raids'),
					'RAID_ID'				=> $raid_id,
					'S_ADDITIONAL_DATA'		=> strlen($this->pdh->get('raid', 'additional_data', array($raid_id))) ? true : false,
					'RAID_ADDITIONAL_DATA'	=> $this->pdh->geth('raid', 'additional_data', array($raid_id)),
					'S_PERM_RAID_ADMIN'		=> $this->user->check_auth('a_raid_upd', false),
			));
			if($this->user->check_auth('a_raid_upd', false)){
				$this->jquery->dialog('editRaid', $this->user->lang('raidevent_raid_edit'), array('url' => $this->server_path."admin/manage_raids.php".$this->SID."&r=".$raid_id."&upd=true&simple_head=simple",'width' => 920, 'height' => 740, 'onclose'=> $this->env->link.$this->controller_path_plain.$this->page_path.$this->SID));
			}

			chartooltip_js();
				
			$this->set_vars(array(
					'page_title' 		=> $this->pdh->get('raid', 'event_name', array($raid_id)).', '.$this->time->user_date($this->pdh->get('raid', 'date', array($raid_id))),
					'template_file'		=> 'viewraid.html',
					'display'			=> true)
			);
		} else {
			redirect($this->routing->build('raids',false,false,true,true));
		}
	}

	public function display(){
		if ($this->in->get('from') && $this->in->get('to')){
			if(!$this->in->exists('timestamps')) {
				$date1 = $this->time->fromformat($this->in->get('from'));
				$date2 = $this->time->fromformat($this->in->get('to'));
				$date2 += 86400; // Includes raids/items ON that day
			} else {
				$date1 = $this->in->get('from');
				$date2 = $this->in->get('to');
			}
			$date_suffix	= '&amp;timestamps=1&amp;from='.$date1.'&amp;to='.$date2;
			$view_list		= $this->pdh->get('raid', 'raididsindateinterval', array($date1, $date2));
			$date2			-= 86400; // Shows THAT day
			
			
			//Create a Summary
			$arrRaidstatsSettings = array(
					'name' => 'hptt_viewmember_itemlist',
					'table_main_sub' => '%member_id%',
					'table_subs' => array('%member_id%', '%link_url%', '%link_url_suffix%', '%raid_link_url%', '%raid_link_url_suffix%', '%itt_lang%', '%itt_direct%', '%onlyicon%', '%noicon%', '%from%', '%to%'),
					'page_ref' => 'viewcharacter.php',
					'show_numbers' => false,
					'show_select_boxes' => false,
					'show_detail_twink' => false,
					'table_sort_col' => 0,
					'table_sort_dir' => 'asc',
					'table_presets' => array(
						array('name' => 'mlink', 'sort' => true, 'th_add' => '', 'td_add' => ''),
						array('name' => 'mactive', 'sort' => true, 'th_add' => '', 'td_add' => ''),
						array('name' => 'mtwink', 'sort' => true, 'th_add' => '', 'td_add' => ''),
						array('name' => 'attendance_fromto_all', 'sort' => true, 'th_add' => '', 'td_add' => ''),
						array('name' => 'dattendance_fromto_all', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					),
			);
			
			$show_twinks = $this->config->get('show_twinks');
			$statsuffix = $date_suffix;
			if($this->in->exists('show_twinks')){
				$show_twinks = true;
				$statsuffix .= '&amp;show_twinks=1';
			}

			$arrMemberlist	= $this->pdh->get('member', 'id_list', array(true, true, true, !($show_twinks)));

			$hptt= $this->get_hptt($arrRaidstatsSettings, $arrMemberlist, $arrMemberlist, array('%link_url%' => $this->routing->simpleBuild('raids'), '%link_url_suffix%' => '', '%use_controller%' => true, '%from%'=> $date1, '%to%' => $date2, '%with_twink%' => !$show_twinks), md5($date1.'.'.$date2.'.'.$show_twinks), 'statsort');
			$hptt->setPageRef($this->strPath);
			
			//footer
			$footer_text	= sprintf($this->user->lang('listmembers_footcount'), count($arrMemberlist));
			//$sort = $this->in->get('statsort');
			//$suffix = (strlen($sort))? '&amp;statsort='.$sort : '';
			
			$this->tpl->assign_vars(array (
				'RAIDSTATS_OUT' 		=> $hptt->get_html_table($sort, $statsuffix, null, null, $footer_text),
				'S_RAIDSTATS'			=> true,
				'SHOW_TWINKS_CHECKED'	=> ($show_twinks)?'checked="checked"':'',
				'S_SHOW_TWINKS'			=> !$this->config->get('show_twinks'),
			));
			
		}else{
			$view_list		= $this->pdh->get('raid', 'id_list');
			$date1			= time()-(30*86400);
			$date2			= time();
			$date_suffix	= '';
		}

		//Sort
		$sort			= $this->in->get('sort');
		$sort_suffix	= '&amp;sort='.$sort;

		$start				= $this->in->get('start', 0);
		$pagination_suffix	= ($start) ? '&amp;start='.$start : '';

		//Output
		$hptt_page_settings	= $this->pdh->get_page_settings('listraids', 'hptt_listraids_raidlist');
		$hptt				= $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%link_url%' => $this->routing->simpleBuild('raids'), '%link_url_suffix%' => '', '%use_controller%' => true));
		$hptt->setPageRef($this->strPath);

		//footer
		$raid_count			= count($view_list);
		$footer_text		= sprintf($this->user->lang('listraids_footcount'), $raid_count ,$this->user->data['user_rlimit']);

		$this->tpl->assign_vars(array (
			'PAGE_OUT'			=> $hptt->get_html_table($sort, $pagination_suffix.$date_suffix, $start, $this->user->data['user_rlimit'], $footer_text),
			'RAID_PAGINATION'	=> generate_pagination($this->strPath.$this->SID.$sort_suffix.$date_suffix, $raid_count, $this->user->data['user_rlimit'], $start),

			// Date Picker
			'DATEPICK_DATE_FROM'		=> $this->jquery->Calendar('from', $this->time->user_date($date1, false, false, false, function_exists('date_create_from_format'))),
			'DATEPICK_DATE_TO'			=> $this->jquery->Calendar('to', $this->time->user_date($date2, false, false, false, function_exists('date_create_from_format')))
		));
		
		$this->jquery->Collapse('#toggleRaidsummary', true);

		$this->set_vars(array(
			'template_file'		=> 'listraids.html',
			'display'			=> true
		));
	}
}
?>