<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2016 EQdkp-Plus Developer Team
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

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path.'common.php');

class Manage_Members extends page_generic {

	public function __construct(){
		$this->user->check_auth('a_members_man');
		$handler = array(
			'search' =>	array('process' => 'search', 'csrf' => false),
			'submit_search' =>	array('process' => 'process_search', 'csrf' => true),
			'del' => array('process' => 'member_del', 'csrf'=>true),
			'del_history_entries' => array('process' => 'delete_history_items', 'csrf'=>true),
			'mstatus' => array('process' => 'member_status', 'csrf'=>true),
			'rankc' => array('process' => 'member_ranks', 'csrf'=>true),
			'groupc' => array('process' => 'member_raidgroups', 'csrf'=>true),
			'defrolechange'	=> array('process' => 'ajax_defaultrole', 'csrf'=>true),
			'setinactive' => array('process' => 'process_set_inactive', 'csrf'=>true),
			'setactive' => array('process' => 'process_set_active', 'csrf'=>true),
			'member' => array('process' => 'display_member_history'),
		);
		parent::__construct(false, $handler, array('member', 'name'), null, 'selected_ids[]');
		$this->process();
	}

	public function ajax_defaultrole(){
		$this->pdh->put('member', 'change_defaultrole', array($this->in->get('defrolechange_memberid', 0), $this->in->get('defrolechange', 0)));
		$this->pdh->process_hook_queue();
		echo($this->user->lang('uc_savedmsg_roles'));
		exit;
	}

	public function process_set_active(){
		$intMemberID = $this->in->get('setactive', 0);
		$this->pdh->put('member', 'change_status', array($intMemberID, 1));

		$messages[] = array('title' => $this->user->lang('save_suc'), 'text' => $this->user->lang('mems_status_change').$this->pdh->get('member', 'name', array($intMemberID)), 'color' => 'green');
		$this->display($messages);
	}

	public function process_set_inactive(){
		$intMemberID = $this->in->get('setinactive', 0);
		$this->pdh->put('member', 'change_status', array($intMemberID, 0));

		$messages[] = array('title' => $this->user->lang('save_suc'), 'text' => $this->user->lang('mems_status_change').$this->pdh->get('member', 'name', array($intMemberID)), 'color' => 'green');
		$this->display($messages);
	}

	public function delete_history_items(){
		$arrItems = $this->in->getArray('item_ids', 'int');
		$arrRaids = $this->in->getArray('raid_ids', 'int');
		$arrAdjustments = $this->in->getArray('adj_ids', 'int');

		$intMember = $this->in->get('member', 0);

		$intDelCount = 0;

		//Raids
		if(count($arrRaids) > 0 && $this->user->check_auth('a_raid_del', false)) {
			if($this->in->get('delete_raids', 0)){
				foreach($arrRaids as $raidid) {
					//delete everything connected to the raid
					//adjustments first
					$adj_ids = $this->pdh->get('adjustment', 'adjsofraid', array($raidid));
					$adj_del = array(true);
					foreach($adj_ids as $id) {
						$adj_del[] = $this->pdh->put('adjustment', 'delete_adjustment', array($id));
					}
					//raid itself now
					$raid_del = $this->pdh->put('raid', 'delete_raid', array($raidid));
					if($raid_del) {
						$intDelCount++;
					}
				}
			} else {
				foreach($arrRaids as $raidid) {
					//delete raid attendance of the member
					$raid_del = $this->pdh->put('raid', 'delete_raid_attendance', array($raidid, $intMember));
					if($raid_del) {
						$intDelCount++;
					}
				}
			}
		}

		//Items
		if(count($arrItems) > 0 && $this->user->check_auth('a_item_del', false)) {
			foreach($arrItems as $itemid) {
				$item_del = $this->pdh->put('item', 'delete_item', array($itemid));
				if($item_del) $intDelCount++;
			}
		}

		//Adjustments
		if(count($arrAdjustments) > 0 && $this->user->check_auth('a_indivadj_del', false)) {
			foreach($arrAdjustments as $adjid) {
				$item_del = $this->pdh->put('adjustment', 'delete_adjustment', array($adjid));
				if($item_del) $intDelCount++;
			}
		}

		$this->pdh->process_hook_queue();
		if($intDelCount > 0) $this->core->message($this->user->lang('deleted').': '.$intDelCount, $this->user->lang('del_suc'), 'green');
		$this->display_member_history();
	}

	public function member_del(){
		$member_ids = $this->in->getArray('selected_ids', 'int');
		if($member_id = $this->in->get('member_id', 0)){
			$member_ids[] = $member_id;
		}

		$pos = $neg = array();
		foreach($member_ids as $id){
			//delete member
			$membername = $this->pdh->get('member', 'name', array(intval($id)));
			if($this->pdh->put('member', 'delete_member', array(intval($id)))){
				$pos[] = $membername;
			}else{
				$neg[] = $membername;
			}
		}
		if(count($neg)){
			$messages[]	= array('title' => $this->user->lang('del_nosuc'), 'text' => $this->user->lang('mems_no_del').implode(', ', $neg), 'color' => 'red');
		}
		if(count($pos)){
			$messages[]	= array('title' => $this->user->lang('del_suc'), 'text' => $this->user->lang('mems_del').implode(', ', $pos), 'color' => 'green');
		}
		$this->display($messages);
	}

	public function member_ranks(){
		$sucs = array();
		if($member_ids = $this->in->getArray('selected_ids','int')){
			foreach($member_ids as $id){
				$sucs[$id] = $this->pdh->put('member', 'change_rank', array($id, $this->in->get('rank',0)));
			}
		}
		foreach($sucs as $id => $suc){
			if($suc){
				$pos[] = $this->pdh->get('member', 'name', array($id));
			}else{
				$neg[] = $this->pdh->get('member', 'name', array($id));
			}
		}
		if($neg){
			$messages[] = array('title' => $this->user->lang('save_nosuc'), 'text' => $this->user->lang('mems_no_rank_change').implode(', ', $neg), 'color' => 'red');
		}
		if($pos){
			$messages[] = array('title' => $this->user->lang('save_suc'), 'text' => $this->user->lang('mems_rank_change').implode(', ', $pos), 'color' => 'green');
		}
		$this->display($messages);
	}
	
	public function member_raidgroups(){
		$this->user->check_auth('a_raidgroups_man');
		$intGroupID = $this->in->get('groups',0);
		
		$sucs = array();
		if($member_ids = $this->in->getArray('selected_ids','int')){			
			foreach($member_ids as $id){
				$sucs[$id] = $this->pdh->put('raid_groups_members', 'add_member_to_group', array($id, $intGroupID));
			}
		}
		foreach($sucs as $id => $suc){
			if($suc){
				$pos[] = $this->pdh->get('member', 'name', array($id));
			}
		}
		
		if($pos){
			$messages[] = array('title' => $this->user->lang('save_suc'), 'text' => $this->user->lang('mems_raidgroup_change').implode(', ', $pos), 'color' => 'green');
		}
		$this->display($messages);
	}

	public function member_status(){
		$sucs = array();
		if($member_ids = $this->in->getArray('selected_ids','int')){
			foreach($member_ids as $id){
				$status = ($this->pdh->get('member', 'active', array($id))) ? 0 : 1;
				$sucs[$id] = $this->pdh->put('member', 'change_status', array($id, $status));
			}
		}
		foreach($sucs as $id => $suc){
			if($suc){
				$pos[] = $this->pdh->get('member', 'name', array($id));
			}else{
				$neg[] = $this->pdh->get('member', 'name', array($id));
			}
		}
		if($neg){
			$messages[] = array('title' => $this->user->lang('save_nosuc'), 'text' => $this->user->lang('mems_no_status_change').implode(', ', $neg), 'color' => 'red');
		}
		if($pos){
			$messages[] = array('title' => $this->user->lang('save_suc'), 'text' => $this->user->lang('mems_status_change').implode(', ', $pos), 'color' => 'green');
		}
		$this->display($messages);
	}

	public function display_member_history(){
		$intMemberID = $this->in->get('member', 0);
		$strMembername = $this->pdh->get('member', 'name', array($intMemberID));

		$withTwinksDKP = false;

		// Raids
		$view_list			= $this->pdh->get('raid', 'raidids4memberid', array($intMemberID));
		$hptt_page_settings	= $this->pdh->get_page_settings('admin_manage_raids', 'hptt_admin_manage_raids_raidlist');
		$hptt_page_settings['selectbox_name'] = 'raid_ids';
		$hptt				= $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%link_url%' => 'manage_raids.php', '%link_url_suffix%' => '&amp;upd=true', '%with_twink%' => false, '%use_controller%' => false), 'history_'.$intMemberID, 'rsort');
		$hptt->setPageRef($this->root_path.'admin/manage_members.php');
		$this->tpl->assign_vars(array (
				'RAID_OUT'			=> $hptt->get_html_table($this->in->get('rsort', ''), $this->vc_build_url('rsort'), $this->in->get('rstart', 0), $this->user->data['user_rlimit']),
				'RAID_PAGINATION'	=> generate_pagination($this->vc_build_url('rstart', true), count($view_list), $this->user->data['user_rlimit'], $this->in->get('rstart', 0), 'rstart')
		));

		// Item History
		infotooltip_js();
		$view_list			= $this->pdh->get('item', 'itemids4memberid', array($intMemberID));
		$hptt_page_settings	= $this->pdh->get_page_settings('admin_manage_items', 'hptt_admin_manage_items_itemlist');
		$hptt_page_settings['selectbox_name'] = 'item_ids';
		$hptt				= $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%link_url%' => 'manage_items.php', '%link_url_suffix%' => '&amp;upd=true', '%raid_link_url%' => 'manage_raids.php', '%raid_link_url_suffix%' => '&amp;upd=true', '%itt_lang%' => false, '%itt_direct%' => 0, '%onlyicon%' => 0, '%noicon%' => 0), 'history_'.$intMemberID, 'isort');
		$hptt->setPageRef($this->root_path.'admin/manage_members.php');
		$this->tpl->assign_vars(array (
				'ITEM_OUT'			=> $hptt->get_html_table($this->in->get('isort', ''), $this->vc_build_url('isort'), $this->in->get('istart', 0), $this->user->data['user_rlimit']),
				'ITEM_PAGINATION'	=> generate_pagination($this->vc_build_url('istart', true), count($view_list), $this->user->data['user_rlimit'], $this->in->get('istart', 0), 'istart')
		));

		// Individual Adjustment History
		$view_list = $this->pdh->get('adjustment', 'adjsofmember', array($intMemberID));
		$hptt_page_settings = $this->pdh->get_page_settings('admin_manage_adjustments', 'hptt_admin_manage_adjustments_adjlist');
		$hptt_page_settings['selectbox_name'] = 'adj_ids';
		$hptt = $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%link_url%' => 'manage_adjustments.php', '%link_url_suffix%' => '&amp;upd=true', '%raid_link_url%' => 'manage_raids.php', '%raid_link_url_suffix%' => '&amp;upd=true'), 'history_'.$intMemberID, 'asort');
		$hptt->setPageRef($this->root_path.'admin/manage_members.php');
		$this->tpl->assign_vars(array (
				'ADJUSTMENT_OUT' 		=> $hptt->get_html_table($this->in->get('asort', ''), $this->vc_build_url('asort'), $this->in->get('astart', 0), $this->user->data['user_rlimit']),
				'ADJUSTMENT_PAGINATION'	=> generate_pagination($this->vc_build_url('astart', true), count($view_list), $this->user->data['user_rlimit'], $this->in->get('astart', 0), 'astart')
		));

		$this->jquery->Tab_header('profile_information', true);

		$this->jquery->Dialog('delete_warning', '', array('custom_js'=>"$('#del_history_entries_btm').click();", 'height' => 300, 'message' => $this->user->lang('confirm_delete_member_history').'<br /><br /><label><input type="checkbox" onclick="change_raid_setting(this.checked)" />'.$this->user->lang('confirm_delete_member_history_raids').'</label>'), 'confirm');

		$this->tpl->assign_vars(array(
				'MEMBER_NAME' => $strMembername,
				'MEMBER_ID'		=> $intMemberID,
		));

		$this->core->set_vars([
				'page_title'		=> $this->user->lang('manage_members_title').': '.$strMembername,
				'template_file'		=> 'admin/manage_members_history.html',
				'header_format'		=> $this->simple_head,
				'page_path'			=> [
					['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
					['title'=>$this->user->lang('manage_members'), 'url'=>$this->root_path.'admin/manage_members.php'.$this->SID],
					['title'=>$strMembername, 'url'=>' '],
				],
				'display'			=> true
		]);
	}

	//Url building
	private function vc_build_url($exclude='', $with_base=false) {
		$base_url = $this->root_path.'admin/manage_members.php'.$this->SID;
		$url_params = array(
				'member'	=> $this->in->get('member', 0),
				'asort'		=> $this->in->get('asort', ''),
				'isort'		=> $this->in->get('isort', ''),
				'rsort'		=> $this->in->get('rsort', ''),
				'istart'	=> $this->in->get('istart', 0),
				'rstart'	=> $this->in->get('rstart', 0),
		);
		$url = ($with_base) ? $base_url : '';
		foreach($url_params as $key => $par) {
			if($key != $exclude && !empty($par)) $url .= '&amp;'.$key.'='.$par;
		}
		return $url;
	}

	public function search(){

		$arrClasses = $this->game->get_primary_classes(array('id_0'));
		array_unshift($arrClasses, "");
		
		$ranks			= $this->pdh->aget('rank', 'name', 0, array($this->pdh->get('rank', 'id_list', array())));
		$ranks[""] = "";
		
		$this->tpl->assign_vars(array(
				'SPINNER_CHAR_COUNT' => (new hspinner('charcount'))->output(),
				'DATEPICKER_BEFORE'	=> (new hdatepicker('date_before', array('value' => false)))->output(),
				'DATEPICKER_AFTER'	=> (new hdatepicker('date_after', array('value' => false)))->output(),
				'DD_CLASS'			=> (new hdropdown('class', array('options' => $arrClasses)))->output(),
				'DD_RANKS'			=> (new hdropdown('rank', array('options' => $ranks)))->output(),
		));


		$arrUsers = $this->pdh->aget('user', 'name', 0, array($this->pdh->get('user', 'id_list', array(false))));

		$arrMembers = $this->pdh->aget('member', 'name', 0, array($this->pdh->get('member', 'id_list', array(false,true,false))));


		$this->jquery->Autocomplete('name', $arrUsers);
		$this->jquery->Autocomplete('charname', $arrMembers);

		$this->core->set_vars([
				'page_title'		=> $this->user->lang('manage_members_search'),
				'template_file'		=> 'admin/manage_members_search.html',
				'page_path'			=> [
						['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
						['title'=>$this->user->lang('manage_members'), 'url'=> $this->root_path.'admin/manage_members.php'.$this->SID],
						['title'=>$this->user->lang('manage_members_search'), 'url'=> ''],
				],
				'display'			=> true
		]);

	}

	public function process_search(){
		//I will process each search, and merge the found user array later
		$arrUserIDs = $this->pdh->get('member', 'id_list', array(false, false, false));
		$arrResults = array(
				'name' => false,
				'date_before' => false,
				'date_after' => false,
				'active' => false,
				'inactive' => false,
				'twinkname' => false,
				'class' => false,
				'rank' => false,
		);

		//Username
		$strSearchName = utf8_strtolower($this->in->get('name'));
		if($strSearchName != ""){
			$arrResults['name'] = array();
			foreach($arrUserIDs as $intUserID){
				$strMembername = $this->pdh->get('member', 'name', array($intUserID));

				if(stripos($strMembername, $strSearchName) !== false) {
					$arrResults['name'][] = $intUserID;
				}

			}
		}

		//Date before
		$strBeforeDate = $this->in->get('date_before');
		if($strBeforeDate){
			$arrResults['date_before'] = array();
			$intTime = $this->time->fromformat($strBeforeDate, 0);

			foreach($arrUserIDs as $intUserID){
				$intRegDate = $this->pdh->get('member', 'creation_date', array($intUserID));

				if($intRegDate < $intTime) $arrResults['date_before'][] = $intUserID;
			}
		}


		//Date after
		$strAfterDate = $this->in->get('date_after');
		if($strAfterDate){
			$arrResults['date_after'] = array();
			$intTime = $this->time->fromformat($strAfterDate, 0);

			foreach($arrUserIDs as $intUserID){
				$intRegDate = $this->pdh->get('member', 'creation_date', array($intUserID));

				if($intRegDate > $intTime) $arrResults['date_after'][] = $intUserID;
			}
		}

		//Charname
		$strCharname = utf8_strtolower($this->in->get('twinkname'));
		$arrChars = $this->pdh->get('member', 'id_list');
		if($strCharname != ""){
			$arrResults['twinkname'] = array();
			foreach($arrChars as $intCharID){
				$strMyCharname = $this->pdh->get('member', 'name', array($intCharID));

				if(stripos($strMyCharname, $strCharname) !== false) {
					//Find Main Char
					$intOwner = $this->pdh->get('member', 'mainid', array($intCharID));
					if($intOwner === false) $arrResults['twinkname'][] = $intCharID;
					if($intOwner > 0) $arrResults['twinkname'][] = $intOwner;
				}
			}
		}


		//Locked
		$arrStatus = $this->in->getArray('status');

		if(in_array('active',$arrStatus ) ){
			$arrResults['active'] = array();
			foreach($arrUserIDs as $intUserID){
				$intActive = $this->pdh->get('member', 'active', array($intUserID));

				if($intActive) $arrResults['active'][] = $intUserID;
			}
		}

		if(in_array('inactive',$arrStatus ) ){
			$arrResults['inactive'] = array();
			foreach($arrUserIDs as $intUserID){
				$intActive = $this->pdh->get('member', 'active', array($intUserID));
				if(!$intActive) $arrResults['inactive'][] = $intUserID;
			}
		}

		//Class
		$intClass = $this->in->get('class', 0);
		if($intClass){
			$arrResults['class'] = array();

			foreach($arrUserIDs as $intUserID){
				$intCharClass = $this->pdh->get('member', 'classid', array($intUserID));

				if($intCharClass == $intClass) $arrResults['class'][] = $intUserID;
			}
		}
		
		//Rank
		$intRank = $this->in->get('rank', 0);
		if($intRank){
			$arrResults['rank'] = array();
			
			foreach($arrUserIDs as $intUserID){
				if($intUserID == 3) {
					$intCharRank = $this->pdh->get('member', 'rankid', array($intUserID));
				}
				
				$intCharRank = $this->pdh->get('member', 'rankid', array($intUserID));
				
				if($intCharRank == $intRank) $arrResults['rank'][] = $intUserID;
			}
		}


		//Now combine the search results
		$intFalseCount = 0;
		$arrOutResult = $arrUserIDs;
		foreach($arrResults as $key => $val){
			if($val === false) {
				$intFalseCount++;
			} else {
				$arrOutResult = array_intersect($arrOutResult, $val);
			}
		}

		$this->display(false, $arrOutResult);
	}

	public function display($messages=false, $arrChars=false){
		if($messages){
			$this->pdh->process_hook_queue();
			$this->core->messages($messages);
		}

		if($arrChars !== false){
			$blnIsSearch = true;
			$view_list = $arrChars;
		} else {
			$blnIsSearch = false;
			$view_list		= $this->pdh->get('member', 'id_list', array(false, false, false));
		}

		$hptt_page_settings = $this->pdh->get_page_settings('admin_manage_members', 'hptt_admin_manage_members_memberlist');
		$cache_suffix 	= ($blnIsSearch) ? random_string(32) : '';
		$hptt			= $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%link_url%' => 'manage_members.php', '%link_url_suffix%' => '&mupd=true'), $cache_suffix);
		$ranks			= $this->pdh->aget('rank', 'name', 0, array($this->pdh->get('rank', 'id_list', array())));
		asort($ranks);
		$page_suffix	= '&amp;start='.$this->in->get('start', 0);
		$sort_suffix	= '?sort='.$this->in->get('sort');

		//footer
		$character_count	= count($view_list);

		$onclose_url = "window.location.href = '".$this->server_path."admin/manage_members.php".$this->SID."';";
		$this->jquery->Dialog('EditChar', $this->user->lang('uc_edit_char'), array('withid'=>'editid', 'url'=> $this->controller_path.'AddCharacter/'.$this->SID."&adminmode=1&editid='+editid+'", 'width'=>'750', 'height'=>'700', 'onclosejs'=>$onclose_url));
		$this->jquery->Dialog('AddChar', $this->user->lang('uc_add_char'), array('url'=> $this->controller_path.'AddCharacter/'.$this->SID.'&adminmode=1', 'width'=>'750', 'height'=>'700', 'onclosejs'=>$onclose_url));
		$this->confirm_delete($this->user->lang('confirm_delete_members'));

		$this->tpl->add_js("
			$('.cdefroledd').on('change', function(){
				$.post('manage_members.php".$this->SID."&link_hash=".$this->CSRFGetToken('defrolechange')."', { defrolechange: $(this).val(), defrolechange_memberid: $(this).attr('name').replace('defaultrole_', '') },
					function(data){
						system_message(data, 'success');
					});
			});
		", 'docready');
		
		$arrRaidgroups = $this->pdh->aget('raid_groups', 'name', false, array($this->pdh->get('raid_groups', 'id_list')));
	
		$arrMenuItems = array(
			0 => array(
				'type'	=> 'javascript',
				'icon'	=> 'fa-trash-o',
				'text'	=> $this->user->lang('delete'),
				'perm'	=> true,
				'name'	=> 'mdel',
				'js'	=> "$('#del_members').click();",
				'append'=> '<input name="mdel" onclick="delete_warning();" id="del_members" class="mainoption bi_delete" type="button" style="display:none;" />',
			),
			1 => array(
				'type'	=> 'button',
				'icon'	=> 'fa-level-down',
				'text'	=> $this->user->lang('mass_stat_change'),
				'perm'	=> true,
				'name'	=> 'mstatus',
			),
			2 => array(
				'type'	=> 'select',
				'icon'	=> 'fa-level-down',
				'text'	=> $this->user->lang('mass_rank_change'),
				'perm'	=> true,
				'name'	=> 'rankc',
				'options' => array('rank', $ranks),
			),
			3 => array(
				'type'	=> 'select',
				'icon'	=> 'fa-users',
				'text'	=> $this->user->lang('mass_raidgroup_change'),
				'perm'	=> true,
				'name'	=> 'groupc',
				'options' => array('groups', $arrRaidgroups),
			),
		);

		$intCharsPerPage = ($blnIsSearch) ? PHP_INT_MAX : $this->user->data['user_rlimit'];

		$this->tpl->assign_vars(array(
			'SID'				=> $this->SID,
			'MEMBER_LIST'		=> $hptt->get_html_table($this->in->get('sort'), $page_suffix, $this->in->get('start', 0), $intCharsPerPage, false),
			'PAGINATION'		=> generate_pagination('manage_members.php'.$sort_suffix, $character_count, $intCharsPerPage, $this->in->get('start', 0)),
			'HPTT_COLUMN_COUNT'	=> $hptt->get_column_count(),
			'MEMBER_COUNT'		=> $character_count,
			'S_IS_SEARCH'		=> $blnIsSearch,
			'HPTT_ADMIN_LINK'	=> ($this->user->check_auth('a_tables_man', false)) ? '<a href="'.$this->server_path.'admin/manage_pagelayouts.php'.$this->SID.'&edit=true&layout='.$this->config->get('eqdkp_layout').'#page-'.md5('admin_manage_members').'" title="'.$this->user->lang('edit_table').'"><i class="fa fa-pencil floatRight"></i></a>' : false,
			'BUTTON_MENU'		=> $this->core->build_dropdown_menu($this->user->lang('selected_chars').'...', $arrMenuItems, '', 'manage_members_menu', array("input[name=\"selected_ids[]\"]")),
		));

		$this->core->set_vars([
			'page_title'		=> $this->user->lang('manage_members_title'),
			'template_file'		=> 'admin/manage_members.html',
			'header_format'		=> $this->simple_head,
			'page_path'			=> [
				['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
				['title'=>$this->user->lang('manage_members'), 'url'=>' '],
			],
			'display'			=> true
		]);
	}
}
registry::register('Manage_Members');
