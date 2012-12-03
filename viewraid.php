<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2002
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

define('EQDKP_INC', true);
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');
infotooltip_js();

class viewraid extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'pdh', 'jquery', 'game', 'config', 'core', 'time', 'comments'	=> 'comments');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public function __construct() {
		$handler = array();
		$this->user->check_auth('u_raid_view');
		parent::__construct(false, $handler, array(), null, '', 'r');
		$this->process();
	}

	public function display(){
		if ( $raid_id = $this->in->get('r', 0) ){
			if(!in_array($raid_id, $this->pdh->get('raid', 'id_list')))
			message_die($this->user->lang('error_invalid_raid_provided'));

			// Attendees
			$attendees_ids = $this->pdh->get('raid', 'raid_attendees', array($raid_id));
			foreach($attendees_ids as $attendee_id){
				$attendees[$attendee_id] = addslashes($this->pdh->get('member', 'name', array($attendee_id)));
			}
			$attendee_copy = $attendees;

			// Get each attendee's rank
			foreach($attendees as $attendee_id => $attendee_name){
				$ranks[ $attendee_name ] = array(
					'prefix'	=> $this->pdh->get('rank', 'prefix', array($this->pdh->get('member', 'rankid', array($attendee_id)))),
					'suffix'	=> $this->pdh->get('rank', 'suffix', array($this->pdh->get('member', 'rankid', array($attendee_id)))),
				);
			}

			if ( @sizeof($attendees) > 0 ){
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
								'COLUMN'.$j.'_NAME' => $this->pdh->get('member', 'html_memberlink', array($attendee, 'viewcharacter.php'))
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
				$buyer_id = $this->pdh->get('item', 'buyer', array($item_id));

				$class_name	= $this->pdh->get('member', 'classname', array($buyer_id));
				$class_id	= $this->pdh->get('member', 'classid', array($buyer_id));

				if(isset($loot_dist[$class_id])){
					$loot_dist[$class_id]['value']++;
				}else{
					$loot_dist[$class_id] = array('value' => 1, 'name' => $class_name);
					$chartcolorsLootdisti[$class_id] = $this->game->get_class_color($class_id);
				}

				$this->tpl->assign_block_vars('items_row', array(
					'BUYER'			=> $this->pdh->get('member', 'html_memberlink', array($buyer_id, 'viewcharacter.php')),
					'ITEM'			=> $this->pdh->get('item', 'link_itt', array($item_id, 'viewitem.php')),
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
						$class_dist[$member_class_id]['names'] .= " " . $html_prefix . $member_name . $html_suffix .",";
						$class_dist[$member_class_id]['count']++;
					}else{
						$class_dist[$member_class_id] = array(
							'names'	=> $html_prefix . $member_name . $html_suffix .",",
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
				$class			= $this->game->get_name('classes', $class_id);
				$chartarray[]	= array('value' => $percentage, 'name' => $class." (".$class_dist[$class_id]['count']." - ".$percentage."%)");
				$chartcolors[] = $this->game->get_class_color($class_id);

				$this->tpl->assign_block_vars('class_row', array(
					'CLASS'			=> $this->game->decorate('classes', $class_id).' <span class="class_'.$class_id.'">'.$class.'</span>',
					'BAR'			=> $this->jquery->ProgressBar('bar_'.$class, $percentage, $percentage.'%'),
					'ATTENDEES'		=> $class_dist[$class_id]['names']
				));
			}

			$chartoptions['border'] = '0.0';
			$chartoptions['background'] = 'transparent';
			$chartoptionsLootDistri = $chartoptions;		
			if ($this->game->get_class_color(1) != ''){
				$chartoptions['color_array'] = $chartcolors;
				$chartoptionsLootDistri['color_array'] = $chartcolorsLootdisti;
			}
			
			unset($eq_classes);

			// Comment System
			$comm_settings = array('attach_id'=>$raid_id, 'page'=>'raids');
			$this->comments->SetVars($comm_settings);
			$COMMENT = ($this->config->get('pk_enable_comments') == 1) ? $this->comments->Show() : '';

			$vpre = $this->pdh->pre_process_preset('rvalue', array(), 0);
			$vpre[2][0] = $raid_id;

			$this->tpl->assign_vars(array(
				'L_MEMBERS_PRESENT_AT'	=> sprintf($this->user->lang('members_present_at'),
					$this->time->user_date($this->pdh->get('raid', 'date', array($raid_id)), false, false, true),
					$this->time->user_date($this->pdh->get('raid', 'date', array($raid_id)), false, true)
				),

				'EVENT_ICON'			=> $this->game->decorate('events', array($this->pdh->get('raid', 'event', array($raid_id)), 40)),
				'EVENT_NAME'			=> stripslashes($this->pdh->get('raid', 'event_name', array($raid_id))),
				'COMMENT'				=> $COMMENT,

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
				'RAID_NOTE'				=> ( $this->pdh->get('raid', 'note', array($raid_id)) != '' ) ? stripslashes($this->pdh->get('raid', 'note', array($raid_id))) : '&nbsp;',
				'DKP_NAME'				=> $this->config->get('dkp_name'),
				'RAID_VALUE'			=> $this->pdh->geth($vpre[0], $vpre[1], $vpre[2]),//runden($this->pdh->get('raid', 'value', array($raid_id))),
				'ATTENDEES_FOOTCOUNT'	=> sprintf($this->user->lang('viewraid_attendees_footcount'), sizeof($attendees)),
				'ITEM_FOOTCOUNT'		=> sprintf($this->user->lang('viewraid_drops_footcount'), sizeof($items)),
				'CLASS_PERCENT_CHART'	=> $this->jquery->PieChart('class_dist', $chartarray, '', $chartoptions, 2, true, true),
				'LOOT_PERCENT_CHART'	=> (count($loot_dist) > 0) ? $this->jquery->PieChart('loot_dist', $loot_dist, '', $chartoptionsLootDistri, 2) : '',
			));

			$this->core->set_vars(array(
				'page_title'		=> $this->user->lang('viewraid_title'),
				'template_file'		=> 'viewraid.html',
				'display'			=> true)
			);
		} else {
			message_die($this->user->lang('error_invalid_raid_provided'));
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_viewraid', viewraid::__shortcuts());
registry::register('viewraid');
?>