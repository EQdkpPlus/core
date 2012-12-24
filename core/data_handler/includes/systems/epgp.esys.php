<?php
/*
* Project:		EQdkp-Plus
* License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
* Link:			http://creativecommons.org/licenses/by-nc-sa/3.0/
* -----------------------------------------------------------------------
* Began:		2007
* Date:			$Date$
* -----------------------------------------------------------------------
* @author		$Author$
* @copyright	2006-2011 EQdkp-Plus Developer Team
* @link			http://eqdkp-plus.com
* @package		eqdkpplus
* @version		$Rev$
*
* $Id$
*/

if (!defined('EQDKP_INC')) {
	die('You cannot access this file directly.');
}

$system_def = array(
	'base_layout' => 'epgp',
	
	'data' => array(
		'description' => registry::fetch('user')->lang('lm_layout_epgp'),
		'point_direction' => 'asc',
	),

	'aliases' => array(
		'earned' => 'ep',
		'spent' => 'gp',
		'adjustment' => 'adjustment',
		'current' => 'epgp',
		'current_all' => 'epgp_all',
		'rvalue' => 'rvalue',
		'ivalue' => 'ivalue'
	),

	'defaults' => array(
		'ival' => 1,
		'rval' => 1,
	),

	'options' => array(
	  'base_points'	=> array(
		'lang'	=> 'Base points',
		'name'	=> 'base_points',
		'type'	=> 'int',
		'size'	=> 5,
		'value' => 0
	  ),
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
					array('name' => 'rlink', 'sort' => true, 'th_add' => '', 'td_add' => 'nowrap="nowrap"'),
					array('name' => 'rnote', 'sort' => true, 'th_add' => 'width="50%"', 'td_add' => 'nowrap="nowrap"'),
					array('name' => 'rattcount', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'ritemcount', 'sort' => true, 'th_add' => '', 'td_add' => ''),
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
					array('name' => 'mrace', 'sort' => true, 'th_add' => '', 'td_add' => ''),
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
				'table_subs' => array('%adjustment_id%'),
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
					array('name' => 'ipoolname', 'sort' => true, 'th_add' => '', 'td_add' => ''),
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
					array('name' => 'ipoolname', 'sort' => true, 'th_add' => '', 'td_add' => ''),
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
					array('name' => 'emdkps', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'eipools', 'sort' => true, 'th_add' => '', 'td_add' => ''),
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
					array('name' => 'rnote', 'sort' => true, 'th_add' => 'width="70%"', 'td_add' => 'class="nowrap"'),
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
					array('name' => 'ipoolname', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'ivalue', 'sort' => true, 'th_add' => '', 'td_add' => ''),
				),
			),
		),//end viewevent
	
	
		'listmembers' => array(
			'listmembers_leaderboard' => array(
				'maxpercolumn' => 5,
				'maxperrow' => 5,
				'sort_direction' => 'desc',
				'column_type' => 'classid',
				'columns' => array_keys(registry::register('game')->get('classes', 'id_0')),
				'default_pool'	=> 1,
			),
			'hptt_listmembers_memberlist_overview' => array(
				'name' => 'hptt_listmembers_memberlist_overview',
				'table_main_sub' => '%member_id%',
				'table_subs' => array('%member_id%', '%link_url%', '%link_url_suffix%', '%with_twink%'),
				'page_ref' => 'listcharacters.php',
				'show_numbers' => true,
				'show_select_boxes' => true,
				'show_detail_twink' => true,
				'table_sort_col' => 0,
				'table_sort_dir' => 'asc',
				'table_presets' => array(
					array('name' => 'mlink', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'mlevel', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'mrace', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'mrank', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'mactive', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'mcname', 'sort' => true, 'th_add' => '', 'td_add' => 'class="nowrap"'),
					array('name' => 'mtwink', 'sort' => true, 'th_add' => '', 'td_add' => ''),
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
				'table_sort_col' => 4,
				'table_sort_dir' => 'desc',
				'table_presets' => array(
					array('name' => 'mlink', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					//array('name' => 'mlevel', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					//array('name' => 'mrace', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					//array('name' => 'mrank', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					//array('name' => 'mactive', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					//array('name' => 'mcname', 'sort' => true, 'th_add' => '', 'td_add' => 'class="nowrap"'),
					array('name' => 'mtwink', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'earned', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'spent', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					//array('name' => 'adjustment', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'current', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					//array('name' => 'first_raid', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'last_raid', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'attendance_30', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					//array('name' => 'attendance_60', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					//array('name' => 'attendance_90', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'attendance_lt', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					//array('name' => 'mfirst_item_name', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					//array('name' => 'mfirst_item_date', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'mlast_item_name', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'mlast_item_date', 'sort' => true, 'th_add' => '', 'td_add' => ''),
				),
			),
		),//end listmembers

		'calendar' => array(
			'hptt_calendar_raidlist'	=> array(
				'name'					=> 'hptt_calendar_raidlist', 
				'table_main_sub'		=> '%calevent_id%',
				'table_subs'			=> array('%member_id%'),
				'page_ref'				=> 'calendar.php',
				'show_numbers'			=> false,
				'show_select_boxes'		=> true,
				'selectboxes_checkall'	=> true,
				'show_detail_twink'		=> false,
				'table_sort_col' => 0,
				'table_sort_dir' => 'asc',
				'table_presets' => array(
					array('name' => 'calevents_weekday', 'sort' => false, 'th_add' => '', 'td_add' => ''),
					array('name' => 'calevents_date', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'calevents_start_time', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'calevents_end_time', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'calevents_raid_event', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'calevents_note', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'raidattendees_status', 'sort' => false, 'th_add' => '', 'td_add' => 'align="center"'),
					array('name' => 'calevents_detailslink', 'sort' => false, 'th_add' => '', 'td_add' => 'align="center"'),
				),
			),
		),
		
		'manage_characters' => array(
			'hptt_manage_characters' => array(
				'name'				=> 'hptt_manage_characters',
				'table_main_sub'	=> '%member_id%',
				'table_subs'		=> array('%member_id%'),
				'page_ref'			=> 'characters.php',
				'show_numbers'		=> true,
				'show_select_boxes'	=> false,
				'show_detail_twink'	=> false,
				'table_sort_dir' => 'asc',
				'table_sort_col' => 1,
				'table_presets'		=> array(
					array('name' => 'cmainchar','sort' => false, 'th_add' => 'width="20"', 'td_add' => ''),
					array('name' => 'mlink_decorated',	'sort' => true, 'th_add' => 'width="100%"', 'td_add' => ''),
					array('name' => 'mrank',	'sort' => true, 'th_add' => 'width="100"', 'td_add' => ''),
					array('name' => 'mlevel',	'sort' => true, 'th_add' => 'width="40"', 'td_add' => ''),
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
					array('name' => 'mrace', 'sort' => true, 'th_add' => '', 'td_add' => ''),
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
					array('name' => 'mcname', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'mrace', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'mlevel', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'mmainname', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'mactive', 'sort' => true, 'th_add' => '', 'td_add' => ''),
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
					array('name' => 'ibuyers', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'iraididlink', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'ivalue', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'ipoolname', 'sort' => true, 'th_add' => '', 'td_add' => ''),
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
					array('name' => 'adj_event', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'adj_members', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'adj_value', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'adj_raid', 'sort' => true, 'th_add' => '', 'td_add' => ''),
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
					array('name' => 'evalue', 'sort' => true, 'th_add' => '', 'td_add' => ''),
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
				'show_select_boxes' => false,
				'show_detail_twink' => false,
				'table_sort_col' => 1,
				'table_sort_dir' => 'desc',
				'table_presets' => array(
					array('name' => 'raidedit', 'sort' => false, 'th_add' => '', 'td_add' => ''),
					array('name' => 'rdate', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'rlink', 'sort' => true, 'th_add' => '', 'td_add' => 'nowrap="nowrap"'),
					array('name' => 'rnote', 'sort' => true, 'th_add' => 'width="50%"', 'td_add' => ''),
					array('name' => 'rattcount', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'ritemcount', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'rvalue', 'sort' => true, 'th_add' => '', 'td_add' => ''),
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
				'show_select_boxes'	=> false,
				'show_detail_twink'	=> false,
				'table_sort_dir'	=> 'desc',
				'table_sort_col'	=> 0,
				'table_presets'		=> array(
					array('name' => 'logdate',		'sort' => true, 'th_add' => 'width="150"', 'td_add' => ''),
					array('name' => 'logtype',		'sort' => true, 'th_add' => 'width="100%"', 'td_add' => 'style="height:22px;"'),
					array('name' => 'logplugin',	'sort' => true, 'th_add' => 'width="100"', 'td_add' => ''),
					array('name' => 'loguser',		'sort' => true, 'th_add' => 'width="100"', 'td_add' => ''),
					array('name' => 'logipaddress',	'sort' => true, 'th_add' => 'width="70"', 'td_add' => ''),
					array('name' => 'logresult',	'sort' => true, 'th_add' => 'width="70"', 'td_add' => ''),
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
					array('name' => 'logtype',		'sort' => false, 'th_add' => 'width="100%"', 'td_add' => 'style="height:22px;"'),
					array('name' => 'logplugin',	'sort' => false, 'th_add' => 'width="100"', 'td_add' => ''),
					array('name' => 'loguser',		'sort' => false, 'th_add' => 'width="100"', 'td_add' => ''),
					array('name' => 'logresult',	'sort' => false, 'th_add' => 'width="70"', 'td_add' => ''),
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
					array('name' => 'calevents_edit',		'sort' => false, 'th_add' => 'width="20"', 'td_add' => ''),
					array('name' => 'calevents_date',		'sort' => true, 'th_add' => 'width="14%"', 'td_add' => ''),
					array('name' => 'calevents_duration',	'sort' => true, 'th_add' => 'width="6%"', 'td_add' => ''),
					array('name' => 'calevents_name',		'sort' => true, 'th_add' => 'width="40%"', 'td_add' => ''),
					array('name' => 'calevents_creator',	'sort' => true, 'th_add' => 'width="20%"', 'td_add' => ''),
					array('name' => 'calevents_calendar',	'sort' => true, 'th_add' => 'width="20%"', 'td_add' => ''),
				),
			),
		),//end manage calendar events

	),//pages end
);
?>