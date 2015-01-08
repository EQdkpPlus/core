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

if (!defined('EQDKP_INC')) {
	die('You cannot access this file directly.');
}

$system_def = array(
	'base_layout' => 'normal',
	
	'data' => array(
		'description' => registry::fetch('user')->lang('lm_layout_normal'),
		'point_direction' => 'asc',
	),
	
	'aliases' => array(
		'earned' => 'earned',
		'spent' => 'spent',
		'adjustment' => 'adjustment',
		'current' => 'current',
		'current_all' => 'all_current',
		'rvalue' => 'rvalue',
		'ivalue' => 'ivalue',
	),
	
	'defaults' => array(
		'ival' => 1,
		'rval' => 1,
	),
	
	'options' => array(
	),
	
	'substitutions' => array(
	),
	
	'pages' => array(
	
	
	
	
		'listraids' => array(
			'hptt_listraids_raidlist' => array(
				'name' => 'hptt_listraids_raidlist',
				'table_main_sub' => '%raid_id%',
				'table_subs' => array('%raid_id%', '%link_url%', '%link_url_suffix%'),
				'page_ref' => 'listraids.php',
				'show_numbers' => false,
				'show_select_boxes' => false,
				'show_detail_twink' => false,
				'table_sort_col' => 0,
				'table_sort_dir' => 'desc',
				'table_presets' => array(
					array('name' => 'rdate', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'rlink', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'rnote', 'sort' => true, 'th_add' => 'width="50%" class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
					array('name' => 'rattcount', 'sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
					array('name' => 'ritemcount', 'sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
					array('name' => 'rvalue', 'sort' => true, 'th_add' => '', 'td_add' => ''),
				),
			),
		),//end listraids

		'viewmember' => array(
			'hptt_viewmember_points' => array(
				'name' => 'hptt_viewmember_points',
				'table_main_sub' => '%dkp_id%',
				'table_subs' => array('%dkp_id%', '%member_id%', '%with_twink%'),
				'page_ref' => 'viewcharacter.php',
				'show_numbers' => false,
				'show_select_boxes' => false,
				'show_detail_twink' => true,
				'table_presets' => array(
					array('name' => 'mdkpname', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'earned', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'spent', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'adjustment', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'current', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'attendance_30', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'attendance_60', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'attendance_90', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'attendance_lt', 'sort' => true, 'th_add' => '', 'td_add' => ''),
				),
			),
			'hptt_viewmember_memberlist' => array(
				'name' => 'hptt_viewmember_memberlist',
				'table_main_sub' => '%member_id%',
				'table_subs' => array('%member_id%', '%with_twink%'),
				'page_ref' => 'viewcharacter.php',
				'show_numbers' => false,
				'show_select_boxes' => false,
				'show_detail_twink' => false,
				'table_sort_col' => 0,
				'table_sort_dir' => 'asc',
				'table_presets' => array(
					array('name' => 'mlink', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'mlevel', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'mrank', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'mactive', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'mcname', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'mtwink', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'current_all', 'sort' => true, 'th_add' => '', 'td_add' => ''),
				),
			),

			'hptt_viewmember_raidlist' => array(
				'name' => 'hptt_viewmember_raidlist',
				'table_main_sub' => '%raid_id%',
				'table_subs' => array('%raid_id%', '%link_url%', '%link_url_suffix%'),
				'page_ref' => 'viewcharacter.php',
				'show_numbers' => false,
				'show_select_boxes' => false,
				'show_detail_twink' => false,
				'table_sort_col' => 0,
				'table_sort_dir' => 'desc',
				'table_presets' => array(
					array('name' => 'rdate', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'rlink', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'rnote', 'sort' => true, 'th_add' => 'width="70%"', 'td_add' => 'nowrap="nowrap"'),
					array('name' => 'rvalue', 'sort' => true, 'th_add' => '', 'td_add' => ''),
				),
			),

			'hptt_viewmember_adjlist' => array(
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
					array('name' => 'adj_date', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'adj_reason', 'sort' => true, 'th_add' => 'width="70%"', 'td_add' => ''),
					array('name' => 'adj_value', 'sort' => true, 'th_add' => '', 'td_add' => 'nowrap="nowrap"'),
				),
			),

			'hptt_viewmember_itemlist' => array(
				'name' => 'hptt_viewmember_itemlist',
				'table_main_sub' => '%item_id%',
				'table_subs' => array('%item_id%', '%link_url%', '%link_url_suffix%', '%raid_link_url%', '%raid_link_url_suffix%', '%itt_lang%', '%itt_direct%', '%onlyicon%', '%noicon%'),
				'page_ref' => 'viewcharacter.php',
				'show_numbers' => false,
				'show_select_boxes' => false,
				'show_detail_twink' => false,
				'table_sort_col' => 0,
				'table_sort_dir' => 'desc',
				'table_presets' => array(
					array('name' => 'idate', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'ibuyername', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'ilink_itt', 'sort' => true, 'th_add' => '', 'td_add' => 'style="height:21px;"'),
					array('name' => 'iraidlink', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'ipoolname', 'sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
					array('name' => 'ivalue', 'sort' => true, 'th_add' => '', 'td_add' => ''),
				),
			),

			'hptt_viewmember_eventatt' => array(
				'name' => 'hptt_viewmember_eventatt',
				'table_main_sub' => '%event_id%',
				'table_subs' => array('%event_id%', '%member_id%', '%link_url%', '%link_url_suffix%'),
				'page_ref' => 'viewcharacter.php',
				'show_numbers' => false,
				'show_select_boxes' => false,
				'show_detail_twink' => false,
				'table_sort_col' => 0,
				'table_sort_dir' => 'desc',
				'table_presets' => array(
					array('name' => 'eicon', 'sort' => false, 'th_add' => '', 'td_add' => ''),
					array('name' => 'elink', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'event_attendance', 'sort' => true, 'th_add' => '', 'td_add' => 'width="80%"'),
				),
			),
		),//end viewmember

		'listitems' => array(
			'hptt_listitems_itemlist' => array(
				'name' => 'hptt_listitems_itemlist',
				'table_main_sub' => '%item_id%',
				'table_subs' => array('%item_id%', '%link_url%', '%link_url_suffix%', '%raid_link_url%', '%raid_link_url_suffix%', '%itt_lang%', '%itt_direct%', '%onlyicon%', '%noicon%'),
				'page_ref' => 'listitems.php',
				'show_numbers' => false,
				'show_select_boxes' => false,
				'show_detail_twink' => false,
				'table_sort_col' => 0,
				'table_sort_dir' => 'desc',
				'table_presets' => array(
					array('name' => 'idate', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'ibuyername', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'ilink_itt', 'sort' => true, 'th_add' => '', 'td_add' => 'style="height:21px;"'),
					array('name' => 'iraidlink', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'ipoolname', 'sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
					array('name' => 'ivalue', 'sort' => true, 'th_add' => '', 'td_add' => ''),
				),
			),
		),//end listitems

		'viewitem' => array(
			'hptt_viewitem_buyerslist' => array(
				'name' => 'hptt_viewitem_buyerslist',
				'table_main_sub' => '%item_id%',
				'table_subs' => array('%item_id%', '%raid_link_url%', '%raid_link_url_suffix%'),
				'page_ref' => 'viewitem.php',
				'show_numbers' => false,
				'show_select_boxes' => false,
				'show_detail_twink' => false,
				'table_sort_col' => 0,
				'table_sort_dir' => 'desc',
				'table_presets' => array(
					array('name' => 'idate', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'ibuyername', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'iraidlink', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'ipoolname', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'ivalue', 'sort' => true, 'th_add' => '', 'td_add' => ''),
				),
			),
		),
	
		'listevents' => array(
			'hptt_listevents_eventlist' => array(
				'name' => 'hptt_listevents_eventlist',
				'table_main_sub' => '%event_id%',
				'table_subs' => array('%event_id%', '%link_url%', '%link_url_suffix%'),
				'page_ref' => 'listevents.php',
				'show_numbers' => false,
				'show_select_boxes' => false,
				'show_detail_twink' => false,
				'table_sort_col' => 0,
				'table_sort_dir' => 'asc',
				'table_presets' => array(
					array('name' => 'elink', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'emdkps', 'sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
					array('name' => 'eipools', 'sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
					array('name' => 'evalue', 'sort' => true, 'th_add' => '', 'td_add' => ''),
				),
			),
		),//end listevents
		
		
		'viewevent' => array(
			'hptt_viewevent_raidlist' => array(
				'name' => 'hptt_viewevent_raidlist',
				'table_main_sub' => '%raid_id%',
				'table_subs' => array('%raid_id%', '%link_url%', '%link_url_suffix%'),
				'page_ref' => 'viewevent.php',
				'show_numbers' => false,
				'show_select_boxes' => false,
				'show_detail_twink' => false,
				'table_sort_col' => 0,
				'table_sort_dir' => 'desc',
				'table_presets' => array(
					array('name' => 'rdate', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'rlink', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'rnote', 'sort' => true, 'th_add' => 'width="70%" class="hiddenSmartphone"', 'td_add' => 'class="nowrap hiddenSmartphone"'),
					array('name' => 'rvalue', 'sort' => true, 'th_add' => '', 'td_add' => ''),
				),
			),
			'hptt_viewevent_itemlist' => array(
				'name' => 'hptt_viewevent_itemlist',
				'table_main_sub' => '%item_id%',
				'table_subs' => array('%item_id%', '%link_url%', '%link_url_suffix%', '%raid_link_url%', '%raid_link_url_suffix%', '%itt_lang%', '%itt_direct%', '%onlyicon%', '%noicon%'),
				'page_ref' => 'viewevent.php',
				'show_numbers' => false,
				'show_select_boxes' => false,
				'show_detail_twink' => false,
				'table_sort_col' => 0,
				'table_sort_dir' => 'desc',
				'table_presets' => array(
					array('name' => 'idate', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'ibuyername', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'ilink_itt', 'sort' => true, 'th_add' => '', 'td_add' => 'style="height:21px;"'),
					array('name' => 'iraidlink', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'ipoolname', 'sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
					array('name' => 'ivalue', 'sort' => true, 'th_add' => '', 'td_add' => ''),
				),
			),
		),//end viewevent
		
		'listusers' => array(
			'hptt_listusers_userlist' => array(
				'name' => 'hptt_listraids_raidlist',
				'table_main_sub' => '%user_id%',
				'table_subs' => array('%user_id%', '%member_id%','%link_url%', '%link_url_suffix%', '%use_controller%'),
				'page_ref' => 'listusers.php',
				'show_numbers' => false,
				'show_select_boxes' => false,
				'show_detail_twink' => false,
				'table_sort_col' => 3,
				'table_sort_dir' => 'asc',
				'table_presets' => array(
					array('name' => 'useronlinestatus', 'sort' => false, 'th_add' => '', 'td_add' => 'width="10" nowrap="nowrap"'),
					array('name' => 'useravatar', 'sort' => false, 'th_add' => '', 'td_add' => 'nowrap="nowrap"'),
					array('name' => 'username', 'sort' => true, 'th_add' => '', 'td_add' => 'nowrap="nowrap"'),
					array('name' => 'useremail', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'usercountry', 'sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
					array('name' => 'usergroups', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'userregdate', 'sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' =>  'class="hiddenSmartphone"'),
					array('name' => 'usercharnumber', 'sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),				
				),
			),
		),
			
		'userprofile' => array(
				'hptt_userprofile_memberlist_overview' => array(
						'name' => 'hptt_userprofile_memberlist_overview',
						'table_main_sub' => '%member_id%',
						'table_subs' => array('%member_id%', '%link_url%', '%link_url_suffix%', '%with_twink%'),
						'page_ref' => $this->strPath,
						'show_numbers' => false,
						'show_select_boxes' => false,
						'show_detail_twink' => false,
						'perm_detail_twink' => true,
						'table_sort_col' => 0,
						'table_sort_dir' => 'asc',
						'table_presets' => array(
							array('name' => 'mlink_decorated', 'sort' => true, 'th_add' => '', 'td_add' => ''),
							array('name' => 'mlevel', 'sort' => true, 'th_add' => '', 'td_add' => ''),
							array('name' => 'mrank', 'sort' => true, 'th_add' => '', 'td_add' => ''),
							array('name' => 'mtwink', 'sort' => true, 'th_add' => '', 'td_add' => ''),
							array('name' => 'current_all', 'sort' => true, 'th_add' => '', 'td_add' => ''),
							array('name' => 'attendance_30_all', 'sort' => true, 'th_add' => '', 'td_add' => ''),
							array('name' => 'attendance_lt_all', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					),
				),			
		),
			
			'teamlist' => array(
					'hptt_team_list' => array(
							'name' => 'hptt_team_list',
							'table_main_sub' => '%user_id%',
							'table_subs' => array('%user_id%', '%member_id%','%link_url%', '%link_url_suffix%', '%use_controller%'),
							'page_ref' => 'listusers.php',
							'show_numbers' => false,
							'show_select_boxes' => false,
							'show_detail_twink' => false,
							'table_sort_col' => 2,
							'table_sort_dir' => 'asc',
							'table_presets' => array(
									array('name' => 'useronlinestatus', 'sort' => false, 'th_add' => '', 'td_add' => 'width="10" nowrap="nowrap"'),
									array('name' => 'useravatar', 'sort' => false, 'th_add' => '', 'td_add' => 'nowrap="nowrap"'),
									array('name' => 'username', 'sort' => true, 'th_add' => '', 'td_add' => 'nowrap="nowrap"'),
									array('name' => 'useremail', 'sort' => true, 'th_add' => '', 'td_add' => 'nowrap="nowrap"'),
									array('name' => 'usercountry', 'sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
									array('name' => 'userregdate', 'sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' =>  'class="hiddenSmartphone"'),
							),
					),
			),
	
		'listmembers' => array(
			'listmembers_leaderboard' => array(
				'maxpercolumn' => 5,
				'maxperrow' => 5,
				'sort_direction' => 'desc',
				'column_type' => 'classid',
				'columns' => array_keys(registry::register('game')->get_primary_classes(array('id_0'))),
				'default_pool'	=> 1,
			),
			'hptt_listmembers_memberlist_overview' => array(
				'name' => 'hptt_listmembers_memberlist_overview',
				'table_main_sub' => '%member_id%',
				'table_subs' => array('%member_id%', '%link_url%', '%link_url_suffix%', '%with_twink%', '%dkp_id%'),
				'page_ref' => 'listcharacters.php',
				'show_numbers' => true,
				'show_select_boxes' => true,
				'show_detail_twink' => true,
				'table_sort_col' => 0,
				'table_sort_dir' => 'asc',
				'table_presets' => array(
					array('name' => 'mlink', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'mlevel', 'sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
					array('name' => 'mrank', 'sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
					array('name' => 'mactive', 'sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
					array('name' => 'mcname', 'sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
					array('name' => 'mtwink', 'sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
					array('name' => 'current_all', 'sort' => true, 'th_add' => '', 'td_add' => ''),
				),
			),
			'hptt_listmembers_memberlist_detail' => array(
				'name' => 'hptt_listmembers_memberlist_detail',
				'table_main_sub' => '%member_id%',
				'table_subs' => array('%member_id%', '%dkp_id%', '%link_url%', '%link_url_suffix%', '%with_twink%'),
				'page_ref' => 'listcharacters.php',
				'show_numbers' => true,
				'show_select_boxes' => true,
				'show_detail_twink' => true,
				'table_sort_col' => 0,
				'table_sort_dir' => 'asc',
				'table_presets' => array(
					array('name' => 'mlink', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'mtwink', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'earned', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'spent', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'adjustment', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'current', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'last_raid', 'sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
					array('name' => 'attendance_30', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'attendance_lt', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'mlast_item_name', 'sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
					array('name' => 'mlast_item_date', 'sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
				),
			),
		),//end listmembers

		'calendar' => array(
			'hptt_calendar_raidlist'	=> array(
				'name'					=> 'hptt_calendar_raidlist', 
				'table_main_sub'		=> '%calevent_id%',
				'table_subs'			=> array('%member_id%'),
				'page_ref'				=> 'calendar/index.php',
				'show_numbers'			=> false,
				'show_select_boxes'		=> true,
				'selectboxes_checkall'	=> true,
				'show_detail_twink'		=> false,
				'table_sort_col' => 1,
				'table_sort_dir' => 'asc',
				'table_presets' => array(
					array('name' => 'calevents_weekday', 'sort' => false, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
					array('name' => 'calevents_date', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'calevents_start_time', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'calevents_end_time', 'sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
					array('name' => 'calevents_raid_event', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'calevents_note', 'sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
					array('name' => 'raidattendees_status', 'sort' => false, 'th_add' => '', 'td_add' => 'align="center"'),
					array('name' => 'calevents_detailslink', 'sort' => false, 'th_add' => '', 'td_add' => 'align="center"'),
				),
			),
		),
		
		'manage_characters' => array(
			'hptt_manage_characters' => array(
				'name'				=> 'hptt_manage_characters',
				'table_main_sub'	=> '%member_id%',
				'table_subs'		=> array('%member_id%', '%link_url%', '%link_url_suffix%'),
				'page_ref'			=> 'characters.php',
				'show_numbers'		=> true,
				'show_select_boxes'	=> false,
				'show_detail_twink'	=> false,
				'table_sort_dir' => 'asc',
				'table_sort_col' => 1,
				'table_presets'		=> array(
					array('name' => 'cmainchar','sort' => false, 'th_add' => 'width="20"', 'td_add' => ''),
					array('name' => 'mlink_decorated',	'sort' => true, 'th_add' => 'width="100%"', 'td_add' => ''),
					array('name' => 'mrank',	'sort' => true, 'th_add' => 'width="100" class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
					array('name' => 'mlevel',	'sort' => true, 'th_add' => 'width="40" class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
					array('name' => 'cdefrole',	'sort' => false, 'th_add' => 'width="70"', 'td_add' => ''),
					array('name' => 'charmenu',	'sort' => false, 'th_add' => 'width="40"', 'td_add' => ''),
				),
			),
		),//end manage characters
		
		'roster' => array(
			'hptt_roster' => array(
				'name'				=> 'roster',
				'table_main_sub'	=> '%member_id%',
				'table_subs'		=> array('%member_id%'),
				'page_ref'			=> 'roster.php',
				'show_numbers'		=> false,
				'show_select_boxes'	=> false,
				'show_detail_twink'	=> false,
				'table_sort_dir' 	=> 'asc',
				'table_sort_col' 	=> 0,
				'table_presets'		=> array(
					array('name' => 'mlink',	'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'mrank',	'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'mlevel',	'sort' => true, 'th_add' => '', 'td_add' => ''),
				),
			),
		),//end roster

		'admin_manage_members' => array(
			'hptt_admin_manage_members_memberlist' => array(
				'name' => 'hptt_admin_manage_members_memberlist',
				'table_main_sub' => '%member_id%',
				'table_subs' => array('%member_id%', '%link_url%', '%link_url_suffix%'),
				'page_ref' => 'manage_members.php',
				'show_numbers' => true,
				'show_select_boxes' => true,
				'selectboxes_checkall'=>true,
				'show_detail_twink' => false,
				'table_sort_col' => 1,
				'table_sort_dir' => 'asc',
				'table_presets' => array(
					array('name' => 'medit', 'sort' => false, 'th_add' => 'width="20"', 'td_add' => ''),
					array('name' => 'mname', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'mrank', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'mcname', 'sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
					array('name' => 'mlevel', 'sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
					array('name' => 'mmainname', 'sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
					array('name' => 'mactive', 'sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
				),
			),
		),//end manage members

		'admin_manage_items' => array(
			'hptt_admin_manage_items_itemlist' => array(
				'name' => 'hptt_admin_manage_items_itemlist',
				'table_main_sub' => '%item_id%',
				'table_subs' => array('%item_id%', '%link_url%', '%link_url_suffix%', '%raid_link_url%', '%raid_link_url_suffix%', '%itt_lang%', '%itt_direct%', '%onlyicon%', '%noicon%'),
				'page_ref' => 'manage_items.php',
				'show_numbers' => true,
				'show_select_boxes' => true,
				'selectboxes_checkall'=>true,
				'show_detail_twink' => false,
				'table_sort_dir' => 'desc',
				'table_sort_col' => 1,
				'table_presets' => array(
					array('name' => 'itemsedit', 'sort' => false, 'th_add' => '', 'td_add' => ''),
					array('name' => 'idate', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'ilink_itt', 'sort' => true, 'th_add' => '', 'td_add' => 'style="height:21px;"'),
					array('name' => 'ibuyers', 'sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
					array('name' => 'iraididlink', 'sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
					array('name' => 'ivalue', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'ipoolname', 'sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
				),
			),
		),//end manage items

		'admin_manage_adjustments' => array(
			'hptt_admin_manage_adjustments_adjlist' => array(
				'name' => 'hptt_admin_manage_adjustments_adjlist',
				'table_main_sub' => '%adjustment_id%',
				'table_subs' => array('%adjustment_id%', '%link_url%', '%link_url_suffix%'),
				'page_ref' => 'manage_adjustments.php',
				'show_numbers' => true,
				'show_select_boxes' => true,
				'selectboxes_checkall'=>true,
				'show_detail_twink' => false,
				'table_sort_dir' => 'desc',
				'table_sort_col' => 1,
				'table_presets' => array(
					array('name' => 'adjedit', 'sort' => false, 'th_add' => '', 'td_add' => ''),
					array('name' => 'adj_date', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'adj_reason_link', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'adj_event', 'sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
					array('name' => 'adj_members', 'sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
					array('name' => 'adj_value', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'adj_raid', 'sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
				),
			),
		),//end manage adjustments

		'admin_manage_events' => array(
			'hptt_admin_manage_events_eventlist' => array(
				'name' => 'hptt_admin_manage_events_eventlist',
				'table_main_sub' => '%event_id%',
				'table_subs' => array('%event_id%', '%link_url%', '%link_url_suffix%'),
				'page_ref' => 'manage_events.php',
				'show_numbers' => true,
				'show_select_boxes' => false,
				'show_detail_twink' => false,
				'table_sort_dir' => 'desc',
				'table_sort_col' => 0,
				'table_presets' => array(
					array('name' => 'eventedit', 'sort' => false, 'th_add' => '', 'td_add' => ''),
					array('name' => 'eicon', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'elink', 'sort' => true, 'th_add' => 'width="90%"', 'td_add' => ''),
					array('name' => 'evalue', 'sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
				),
			),
		),//end manage events

		'admin_manage_raids' => array(
			'hptt_admin_manage_raids_raidlist' => array(
				'name' => 'hptt_admin_manage_raids_raidlist',
				'table_main_sub' => '%raid_id%',
				'table_subs' => array('%raid_id%', '%link_url%', '%link_url_suffix%'),
				'page_ref' => 'manage_raids.php',
				'show_numbers' => false,
				'show_select_boxes' => true,
				'selectboxes_checkall'=>true,
				'show_detail_twink' => false,
				'table_sort_col' => 1,
				'table_sort_dir' => 'desc',
				'table_presets' => array(
					array('name' => 'raidedit', 'sort' => false, 'th_add' => '', 'td_add' => ''),
					array('name' => 'rdate', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'rlink', 'sort' => true, 'th_add' => '', 'td_add' => 'nowrap="nowrap"'),
					array('name' => 'rnote', 'sort' => true, 'th_add' => 'width="50%" class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
					array('name' => 'rattcount', 'sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
					array('name' => 'ritemcount', 'sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
					array('name' => 'rvalue', 'sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
				),
			),
		),//end manage raids

		'admin_manage_news' => array(
			'hptt_admin_manage_news' => array(
				'name'				=> 'hptt_admin_manage_news',
				'table_main_sub'	=> '%news_id%',
				'table_subs'		=> array('%news_id%'),
				'page_ref'			=> 'manage_news.php',
				'show_numbers'		=> true,
				'show_select_boxes'	=> true,
				'selectboxes_checkall'=>true,
				'show_detail_twink'	=> false,
				'table_sort_dir' => 'desc',
				'table_sort_col' => 1,
				'table_presets'		=> array(
					array('name' => 'nedit',		'sort' => false, 'th_add' => 'width="16"', 'td_add' => ''),
					array('name' => 'ndate',		'sort' => true, 'th_add' => 'width="150"', 'td_add' => 'nowrap="nowrap"'),
					array('name' => 'nheadline',	'sort' => true, 'th_add' => 'width="80%"', 'td_add' => ''),
					array('name' => 'nstart',		'sort' => true, 'th_add' => 'width="100"', 'td_add' => ''),
					array('name' => 'nstop',		'sort' => true, 'th_add' => 'width="100"', 'td_add' => ''),
					array('name' => 'nusername',	'sort' => true, 'th_add' => 'width="100"', 'td_add' => ''),
					array('name' => 'ncategory',	'sort' => true, 'th_add' => 'width="10%"', 'td_add' => ''),
				),
			),
		),//end manage news

		'admin_manage_logs' => array(
			'hptt_managelogs_actions' => array(
				'name'				=> 'hptt_managelogs_actions',
				'table_main_sub'	=> '%log_id%',
				'table_subs'		=> array('%log_id%', '%link_url%', '%link_url_suffix%'),
				'page_ref'			=> 'manage_logs.php',
				'show_numbers'		=> true,
				'show_select_boxes'	=> true,
				'selectboxes_checkall'=>true,
				'show_detail_twink'	=> false,
				'table_sort_dir'	=> 'desc',
				'table_sort_col'	=> 0,
				'table_presets'		=> array(
					array('name' => 'logdatetime',	'sort' => true, 'th_add' => '', 'td_add' => 'class="nowrap desktopOnly"'),
					array('name' => 'logtype',		'sort' => true, 'th_add' => 'width="50%"', 'td_add' => ''),
					array('name' => 'logrecordid',	'sort' => true, 'th_add' => 'width="20%" class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
					array('name' => 'logrecord',	'sort' => true, 'th_add' => 'width="30%"', 'td_add' => ''),
					array('name' => 'logplugin',	'sort' => true, 'th_add' => 'width="100" class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
					array('name' => 'loguser',		'sort' => true, 'th_add' => 'width="100" class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
					array('name' => 'logipaddress',	'sort' => true, 'th_add' => 'width="70" class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
					array('name' => 'logresult',	'sort' => true, 'th_add' => 'width="70" class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
				),
			),
		),//end manage logs

		'admin_index' => array(
			'hptt_latest_logs' => array(
				'name'				=> 'hptt_latest_logs',
				'table_main_sub'	=> '%log_id%',
				'table_subs'		=> array('%log_id%', '%link_url%', '%link_url_suffix%'),
				'page_ref'			=> 'index.php',
				'show_numbers'		=> false,
				'show_select_boxes'	=> false,
				'show_detail_twink'	=> false,
				'table_sort_dir'	=> 'desc',
				'table_sort_col'	=> 0,
				'table_presets'		=> array(
					array('name' => 'viewlog',		'sort' => false, 'th_add' => 'width="30"', 'td_add' => ''),
					array('name' => 'logdate',		'sort' => false, 'th_add' => 'width="150"', 'td_add' => ''),
					array('name' => 'logtype',		'sort' => false, 'th_add' => 'width="100%"', 'td_add' => ''),
					array('name' => 'logplugin',	'sort' => false, 'th_add' => 'width="100" class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
					array('name' => 'loguser',		'sort' => false, 'th_add' => 'width="100"', 'td_add' => ''),
					array('name' => 'logresult',	'sort' => false, 'th_add' => 'width="70" class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
				),
			),
		), //end admin_index
		
		'admin_manage_roles' => array(
			'hptt_manageroles_actions' => array(
				'name'				=> 'hptt_manageroles_actions',
				'table_main_sub'	=> '%role_id%',
				'table_subs'		=> array('%role_id%'),
				'page_ref'			=> 'manage_roles.php',
				'show_numbers'		=> false,
				'show_select_boxes'	=> true,
				'show_detail_twink'	=> false,
				'table_sort_dir'	=> 'asc',
				'table_sort_col'	=> 0,
				'table_presets'		=> array(
					array('name' => 'roleid',		'sort' => true, 'th_add' => 'width="20"', 'td_add' => ''),
					array('name' => 'roleedit',		'sort' => false, 'th_add' => 'width="20"', 'td_add' => ''),
					array('name' => 'rolename',		'sort' => true, 'th_add' => 'width="30%"', 'td_add' => ''),
					array('name' => 'roleclasses',	'sort' => false, 'th_add' => 'width="70%"', 'td_add' => ''),
				),
			),
		),//end manage roles
		
		'admin_manage_calevents' => array(
			'hptt_managecalevents_actions' => array(
				'name'				=> 'hptt_managecaleventss_actions',
				'table_main_sub'	=> '%calevent_id%',
				'table_subs'		=> array('%calevent_id%'),
				'page_ref'			=> 'manage_calevents.php',
				'show_numbers'		=> false,
				'show_select_boxes'	=> true,
				'selectboxes_checkall'=>true,
				'show_detail_twink'	=> false,
				'table_sort_dir'	=> 'desc',
				'table_sort_col'	=> 1,
				'table_presets'		=> array(
					array('name' => 'calevents_edit',		'sort' => false, 'th_add' => 'width="20"', 'td_add' => 'width="20"'),
					array('name' => 'calevents_date',		'sort' => true, 'th_add' => 'width="14%"', 'td_add' => 'width="14%"'),
					array('name' => 'calevents_duration',	'sort' => true, 'th_add' => 'width="6%" class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
					array('name' => 'calevents_name',		'sort' => true, 'th_add' => 'width="40%"', 'td_add' => ''),
					array('name' => 'calevents_creator',	'sort' => true, 'th_add' => 'width="20%" class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
					array('name' => 'calevents_calendar',	'sort' => true, 'th_add' => 'width="20%" class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
				),
			),
		),//end manage calendar events
		
		'admin_manage_article_categories' => array(
			'hptt_admin_manage_article_categories_categorylist' => array(
				'name'				=> 'hptt_admin_manage_article_categories_categorylist',
				'table_main_sub'	=> '%category_id%',
				'table_subs'		=> array('%category_id%', '%article_id%'),
				'page_ref'			=> 'manage_article_categories.php',
				'show_numbers'		=> false,
				'show_select_boxes'	=> true,
				'selectboxes_checkall'=>true,
				'show_detail_twink'	=> false,
				'table_sort_dir'	=> 'asc',
				'table_sort_col'	=> 0,
				'table_presets'		=> array(
					array('name' => 'category_sortable',	'sort' => true, 'th_add' => 'width="20" class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
					array('name' => 'category_editicon',	'sort' => false, 'th_add' => 'width="20"', 'td_add' => ''),
					array('name' => 'category_published',	'sort' => true, 'th_add' => 'width="20"', 'td_add' => ''),
					array('name' => 'category_article_count','sort' => true, 'th_add' => 'width="20" class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),				
					array('name' => 'category_name',		'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'category_alias',		'sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
					array('name' => 'category_portallayout','sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
				),
			),
			
		),

		'admin_manage_articles' => array(
			'hptt_admin_manage_articles_list' => array(
				'name'				=> 'hptt_admin_manage_articles_list',
				'table_main_sub'	=> '%article_id%',
				'table_subs'		=> array('%article_id%', '%category_id%'),
				'page_ref'			=> 'manage_articles.php',
				'show_numbers'		=> false,
				'show_select_boxes'	=> true,
				'selectboxes_checkall'=>true,
				'show_detail_twink'	=> false,
				'table_sort_dir'	=> 'desc',
				'table_sort_col'	=> 7,
				'table_presets'		=> array(
					array('name' => 'article_editicon',	'sort' => false, 'th_add' => 'width="20"', 'td_add' => ''),
					array('name' => 'article_published',	'sort' => true, 'th_add' => 'width="20"', 'td_add' => ''),
					array('name' => 'article_featured', 'sort' => true, 'th_add' => 'width="20"', 'td_add' => ''),
					array('name' => 'article_index_cb', 'sort' => true, 'th_add' => 'width="20"', 'td_add' => ''),
					array('name' => 'article_title','sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'article_alias','sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
					array('name' => 'article_user',		'sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
					array('name' => 'article_date',		'sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
					array('name' => 'article_last_edited',		'sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
				),
			),
			
		),

	),//pages end
);
?>