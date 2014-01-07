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

if ( !defined('EQDKP_INC') ){
	die('Do not access this file directly.');
}

if ( !class_exists( "pdh_r_member" ) ) {
	class pdh_r_member extends pdh_r_generic{
		public static function __shortcuts() {
			$shortcuts = array('pdc', 'db', 'pdh', 'game', 'user', 'html', 'config', 'jquery', 'xmltools'=>'xmltools', 'time');
			return array_merge(parent::$shortcuts, $shortcuts);
		}

		public $default_lang	= 'english';
		public $data			= array();
		public $cmfields		= array();
		public $preset_lang		= array();

		public $hooks = array(
			'adjustment_update',
			'event_update',
			'item_update',
			'member_update',
			'raid_update',
			'user',
			'update_connection',
			'roles_update'
		);

		public $presets = array(
			'mname'			=> array('name', array('%member_id%'), array()),
			'medit'			=> array('editbutton', array('%member_id%'), array()),
			'mlink'			=> array('memberlink', array('%member_id%', '%link_url%', '%link_url_suffix%', true, true), array()),
			'mlevel'		=> array('level', array('%member_id%'), array()),
			'mrace'			=> array('racename', array('%member_id%'), array()),
			'mrank'			=> array('rankname', array('%member_id%'), array()),
			'mrank_sortid'	=> array('rankname_sortid', array('%member_id%'), array()),
			'mrankimg'		=> array('rankimage', array('%member_id%'), array()),
			'mactive'		=> array('active', array('%member_id%'), array()),
			'mcname'		=> array('classname', array('%member_id%',true), array(true)),
			'mtwink'		=> array('twink', array('%member_id%', true), array(true)),
			'mmainname'		=> array('mainname', array('%member_id%'), array(true)),
			'muser'			=> array('user', array('%member_id%'), array()),
			'picture'		=> array('picture', array('%member_id%'), array(true)),
			'note'			=> array('note', array('%member_id%'), array(true)),
			'last_update'	=> array('last_update', array('%member_id%'), array(true)),
			'charmenu'		=> array('member_menu',	array('%member_id%'),	array()),
			'charname'		=> array('name_decorated',	array('%member_id%'),	array()),
			'cmainchar'		=> array('mainchar_radio',	array('%member_id%'),	array()),
			'cdefrole'		=> array('char_defrole',	array('%member_id%'),	array()),
			'mlink_decorated'=> array('memberlink_decorated', array('%member_id%', '%link_url%', '%link_url_suffix%'), array()),
		);

		public $detail_twink = array(
			'memberlink'	=> 'lang:summed_up',
			'level'			=> false,
			'racename'		=> false,
			'rankname'		=> false,
			'active'		=> false,
			'classname'		=> false,
			'twink'			=> false,
		);

		public function init_presets(){
			//generate presets
			$this->cmfields = $this->pdh->get('profile_fields', 'fieldlist');
			if(is_array($this->cmfields)) {
				foreach($this->cmfields as $mmdata){
					$this->presets['profile_'.$mmdata] = array('profile_field', array('%member_id%', $mmdata), array($mmdata));
					$this->preset_lang['profile_'.$mmdata] = 'Profil-'.$this->pdh->get('profile_fields', 'language', array($mmdata));;
				}
			}
		}

		public function reset(){
			$this->pdc->del('pdh_members_table');
			$this->pdc->del('pdh_member_connections_table');
			$this->data = NULL;
			$this->member_connections = NULL;
		}

		public function init(){

			// Check if a preset is loaded...
			if(count($this->cmfields) < 1){
				$this->cmfields = $this->pdh->get('profile_fields', 'fieldlist');
				if (is_array($this->cmfields)) {
					foreach($this->cmfields as $mmdata){
						$this->presets['profile_'.$mmdata] = array('profile_field', array('%member_id%', $mmdata), array($mmdata));
					}
				}
			}

			//cached data not outdated?
			$this->data 				= $this->pdc->get('pdh_members_table');
			$this->member_connections	= $this->pdc->get('pdh_member_connections_table');
			if($this->data !== NULL && $this->member_connections !== NULL){
				return true;
			}

			//connection data
			$conn_sql =		"SELECT m.member_id, mu.user_id
							FROM __members m
							LEFT JOIN __member_user mu ON mu.member_id = m.member_id
							WHERE (m.requested_del != '1' OR m.requested_del IS NULL)
							ORDER BY m.member_main_id;";
			$conn_result = $this->db->query($conn_sql);

			$this->member_connections = array(array());
			$this->member_user = array();
			while ($drow = $this->db->fetch_record($conn_result) ){
				$this->member_connections[$drow['user_id']][] = $drow['member_id'];
				$this->member_user[$drow['member_id']] = $drow['user_id'];
			}
			$this->db->free_result($conn_result);

			// The free to take members..
			$free_sql =		"SELECT m.member_id, mu.user_id
							FROM __members m
							LEFT JOIN __member_user mu ON m.member_id = mu.member_id
							WHERE mu.user_id IS NULL
							AND (m.requested_del != '1' OR m.requested_del IS NULL)
							ORDER BY m.member_main_id;";
			$free_result = $this->db->query($free_sql);
			while ($drow = $this->db->fetch_record($free_result) ){
				$this->member_connections[0][] = $drow['member_id'];
				$this->member_user[$drow['member_id']] = 0;
			}
			$this->db->free_result($free_result);
			if($free_sql) $this->pdc->put('pdh_member_connections_table',		$this->member_connections,	null);

			// basic member data
			$bmd_sql = "SELECT
						member_id,
						member_name AS name,
						member_level AS level,
						member_status AS status,
						member_class_id AS class_id,
						member_race_id AS race_id,
						member_rank_id AS rank_id,
						member_main_id AS main_id,
						member_creation_date AS creation_date,
						picture,
						notes,
						last_update,
						profiledata,
						requested_del,
						require_confirm,
						defaultrole
						FROM __members;";
			$bmd_result = $this->db->query($bmd_sql);

			while( $bmd_row = $this->db->fetch_record($bmd_result) ){
				if(!isset($this->data[$bmd_row['member_id']]['name'])){
					$this->data[$bmd_row['member_id']]['name']				= $bmd_row['name'];
					$this->data[$bmd_row['member_id']]['class_id']			= $bmd_row['class_id'];
					$this->data[$bmd_row['member_id']]['class_name']		= $this->get_classname($bmd_row['member_id']);
					$this->data[$bmd_row['member_id']]['race_id']			= $bmd_row['race_id'];
					$this->data[$bmd_row['member_id']]['race_name']			= $this->get_racename($bmd_row['member_id']);
					$this->data[$bmd_row['member_id']]['rank_id']			= $bmd_row['rank_id'];
					$this->data[$bmd_row['member_id']]['status']			= $bmd_row['status'];
					$this->data[$bmd_row['member_id']]['level']				= $bmd_row['level'];
					$this->data[$bmd_row['member_id']]['main_id']			= ($bmd_row['main_id'] > 0)? $bmd_row['main_id'] : $bmd_row['member_id'];
					$this->data[$bmd_row['member_id']]['creation_date']		= $bmd_row['creation_date'];
					$this->data[$bmd_row['member_id']]['picture']			= $bmd_row['picture'];
					$this->data[$bmd_row['member_id']]['notes']				= $bmd_row['notes'];
					$this->data[$bmd_row['member_id']]['last_update']		= $bmd_row['last_update'];
					$this->data[$bmd_row['member_id']]['requested_del']		= $bmd_row['requested_del'];
					$this->data[$bmd_row['member_id']]['require_confirm']	= $bmd_row['require_confirm'];
					$this->data[$bmd_row['member_id']]['defaultrole']		= $bmd_row['defaultrole'];
					$this->data[$bmd_row['member_id']]['profiledata']		= $bmd_row['profiledata'];
					$this->data[$bmd_row['member_id']]['user']				= isset($this->member_user[$bmd_row['member_id']]) ? $this->member_user[$bmd_row['member_id']] : 0;
					if(is_array($this->cmfields)){
						$my_data = $this->xmltools->Database2Array($bmd_row['profiledata']);
						foreach($this->cmfields as $mmdata){
							$this->data[$bmd_row['member_id']][$mmdata] = (isset($my_data[$mmdata]) && !is_array($my_data[$mmdata])) ? $my_data[$mmdata] : '';
						}
					}
				}
			}
			$this->db->free_result($bmd_result);
			if($bmd_result) $this->pdc->put('pdh_members_table', $this->data, null);
		}

		public function get_id_list($skip_inactive=false, $skip_hidden=false, $skip_special = true, $skip_twinks=false){
			$members = array();
			$special_members = ($this->config->get('special_members') && unserialize($this->config->get('special_members'))) ? unserialize($this->config->get('special_members')) : array();
			if(is_array($this->data)){
				foreach (array_keys($this->data) as $member_id){
					//special members like disenchanted or banked
					if(!$skip_special || !in_array($member_id, $special_members)){
						//check if we filter hidden ranks
						if(!($skip_hidden) || !$this->pdh->get('rank', 'is_hidden', array($this->data[$member_id]['rank_id']))){
							//and now we filter inactive
							if(!$skip_inactive || $this->data[$member_id]['status']){
								//filter twinks
								if(!$skip_twinks || $this->data[$member_id]['main_id'] == $member_id) {
									$members[] = $member_id;
								}
							}
						}
					}
				}
			}
			return $members;
		}

		public function get_connection_id($userid){
			if(isset($this->member_connections[$userid])) return $this->member_connections[$userid];
			return false;
		}

		public function get_userid($memberid) {
			if(is_array($memberid)){
				$ArrOut	= array();
				foreach($memberid as $memid){
					foreach($this->member_connections as $userid => $data){
						foreach($data as $member_id){
							if($memid == $member_id){
								$ArrOut[]	= $userid;
							}
						}
					}
				}
				return (count($ArrOut) > 0) ? $ArrOut : false;
			}else{
				foreach($this->member_connections as $userid => $data){
					foreach($data as $member_id){
						if($memberid == $member_id) return $userid;
					}
				}
			}
			return false;
		}

		public function get_freechars($userid){
			if($userid > 0 && isset($this->member_connections[$userid])){
				return $this->pdh->maget('member', array('name', 'userid'), 0, array(array_merge($this->member_connections[0], $this->member_connections[$userid])));
			}else{
				return $this->pdh->maget('member', array('name', 'userid'), 0, array($this->member_connections[0]));
			}
		}

		public function get_profile_field($member_id, $profile_field){
			return $this->data[$member_id][$profile_field];
		}
		
		public function get_html_profile_field($member_id, $profile_field){
			$arrField = $this->pdh->get('profile_fields', 'fields', array($profile_field));
			$strMemberValue = $this->get_profile_field($member_id, $profile_field);
			$out = $strMemberValue;
			switch($arrField['fieldtype']){
				case 'int':
				case 'text': {
					if ($arrField['image'] != "" && is_file($this->root_path.'games/'.$this->config->get('default_game').'/profiles/'.$arrField['image'])){
						$out = '<img src="'.$this->root_path.'games/'.$this->config->get('default_game').'/profiles/'.$arrField['image'].'" alt="'.$out.'" /> '.$out;
					}
				}
				break;
				
				case 'dropdown':				
					if ($arrField['image'] != "" && is_dir($this->root_path.'games/'.$this->config->get('default_game').'/profiles/'.$arrField['image']) && is_file($this->root_path.'games/'.$this->config->get('default_game').'/profiles/'.$arrField['image'].'/'.$out.'.png')){
						$out = '<img src="'.$this->root_path.'games/'.$this->config->get('default_game').'/profiles/'.$arrField['image'].'/'.$out.'.png" alt="'.$out.'" />';
					}
				break;
			}
			return $out;
		}

		public function get_rankid($member_id){
			return $this->data[$member_id]['rank_id'];
		}

		public function get_rankname($member_id){
			return $this->pdh->get('rank', 'name', array($this->data[$member_id]['rank_id']));
		}

		public function get_html_rankname($member_id){
			return $this->pdh->geth('rank', 'name', array($this->data[$member_id]['rank_id']));
		}
		
		public function get_rankname_sortid($member_id){
			return $this->get_rankname($member_id);
		}
		
		public function comp_rankname_sortid($params1, $params2){		
			$val1 = $this->pdh->get('rank', 'sortid', array($this->get_rankid($params1[0])));
			$val2 = $this->pdh->get('rank', 'sortid', array($this->get_rankid($params2[0])));
			if ($val1 > $val2) {
				return 1;
			} elseif ($val1 < $val2) {
				return -1;
			}
			return 0;
		}

		public function get_rankimage($member_id){
			return $this->pdh->get('rank', 'rank_image', array($this->data[$member_id]['rank_id']));
		}

		public function get_name($member_id, $rank_prefix = false, $rank_suffix = false){
			if($member_id > 0){
				$name  = ($rank_prefix) ? $this->pdh->get('rank', 'prefix', array($this->data[$member_id]['rank_id'])) : '';
				if(isset($this->data[$member_id]['name'])){
					$name .= $this->data[$member_id]['name'];
				}
				$name .= ($rank_suffix) ? $this->pdh->get('rank', 'suffix', array($this->data[$member_id]['rank_id'])) : '';
				return $name;
			}else{
				return '';
			}
		}

		public function get_html_name($member_id, $rank_prefix = false, $rank_suffix = false) {
			if($this->config->get('pk_class_color')){
				return "<span class='class_".$this->get_classid($member_id)."'>".$this->get_name($member_id,$rank_prefix,$rank_suffix)."</span>";
			}else{
				return $this->get_name($member_id,$rank_prefix,$rank_suffix);
			}
		}

		public function get_id($member_name){
			if (is_array($this->data)){
				foreach($this->data as $mid => $detail){
					if($detail['name'] == $member_name){
						return $mid;
					}
				}
			}
			return false;
		}

		public function get_pfields(){
			return $this->cmfields;
		}

		public function get_level($member_id){
			return $this->data[$member_id]['level'];
		}

		public function get_array($member_id){
			return $this->data[$member_id];
		}

		public function get_fullarray(){
			return $this->data;
		}

		public function get_names($skip_inactive=false, $skip_hidden=false, $skip_special = true, $skip_twinks=false){
			return array_values($this->pdh->aget('member', 'name', 0, array($this->get_id_list($skip_inactive, $skip_hidden, $skip_special, $skip_twinks))));
		}

		public function get_classname($member_id){
			return $this->game->get_name('classes', $this->get_classid($member_id));
		}

		public function get_html_classname($member_id){
			return $this->game->decorate('classes', $this->get_classid($member_id))."<span class='class_".$this->get_classid($member_id)."'>".$this->get_classname($member_id)."</span>";
		}

		public function get_classid($member_id){
			return ((isset($this->data[$member_id]['class_id'])) ? $this->data[$member_id]['class_id'] : 0);
		}

		// seems stupid, but is used by maget to add the memberid to the array
		public function get_memberid($member_id){
			return $member_id;
		}

		public function get_html_classid($member_id){
			return $this->game->decorate('classes', $this->get_classid($member_id));
		}

		public function get_note($member_id){
			return ($member_id > 0 && isset($this->data[$member_id]['note'])) ? $this->data[$member_id]['note'] : '';
		}

		public function get_last_update($member_id){
			return $this->data[$member_id]['last_update'];
		}
		
		public function get_html_last_update($member_id){
			return $this->time->user_date($this->data[$member_id]['last_update'], true);
		}

		public function get_creation_date($member_id){
			return $this->data[$member_id]['creation_date'];
		}

		public function get_picture($member_id){
			return $this->data[$member_id]['picture'];
		}

		public function get_raceid($member_id){
			return $this->data[$member_id]['race_id'];
		}

		public function get_racename($member_id){
			return $this->game->get_name('races', $this->get_raceid($member_id));
		}

		public function get_profiledata($member_id){
			return $this->data[$member_id]['profiledata'];
		}

		public function get_defaultrole($member_id){
			return $this->data[$member_id]['defaultrole'];
		}

		public function get_html_racename($member_id){
			$gender = (isset($this->data[$member_id]['gender'])) ? $this->data[$member_id]['gender'] : 'Male';
			return $this->game->decorate('races', array($this->get_raceid($member_id),$gender)).' <span class="racename">'.$this->get_racename($member_id).'</span>';
		}

		public function get_gender($member_id){
			return (isset($this->data[$member_id]['gender'])) ? $this->data[$member_id]['gender'] : false;
		}

		public function get_active($member_id){
			return $this->data[$member_id]['status'];
		}

		public function get_html_active($member_id){
			if($this->get_active($member_id) == 0){
				return '<img src="'.$this->root_path.'images/glyphs/status_red.gif" alt="i" />';
			}else{
				return '<img src="'.$this->root_path.'images/glyphs/status_green.gif" alt="a" />';
			}
		}

		public function get_is_hidden($member_id){
			return $this->pdh->get('rank', 'is_hidden', array($this->data[$member_id]['rank_id']));
		}

		public function get_mainid($member_id){
			return (isset($this->data[$member_id]) ? $this->data[$member_id]['main_id'] : false);
		}

		public function get_mainchar($userid){
			if(isset($this->member_connections[$userid][0])){
				return $this->data[$this->member_connections[$userid][0]]['main_id'];
			}
			return false;
		}

		public function get_mainname($member_id){
			return $this->data[$this->data[$member_id]['main_id']]['name'];
		}

		public function get_is_main($member_id){
			if(!($member_id == '') && ($this->get_mainid($member_id)==$member_id))
				return true;
			return false;
		}

		public function get_name_decorated($memberid){
			$output =	' '.$this->game->decorate('classes', array($this->pdh->get('member', 'classid', array($memberid)))).
						$this->game->decorate('races', array($this->pdh->get('member', 'raceid', array($memberid)),$this->pdh->get('member', 'gender', array($memberid)))).
						' '.$this->get_html_name($memberid);
			return $output;
		}

		public function comp_name_decorated($params1, $params2){
			return ($this->pdh->get('member', 'name', array($params1[0])) < $this->pdh->get('member', 'name', array($params2[0]))) ? -1 : 1;
		}

		public function get_memberlink_decorated($member_id, $base_url, $url_suffix = ''){
			return '<a href="'.$this->get_memberlink($member_id, $base_url, $url_suffix).'">'.$this->get_name_decorated($member_id).'</a>';
		}
		
		public function comp_memberlink_decorated($params1, $params2){
			return ($this->pdh->get('member', 'name', array($params1[0])) < $this->pdh->get('member', 'name', array($params2[0]))) ? -1 : 1;
		}

		public function get_member_menu($memberid){
			// Action Menu
			$cm_actions= array(
				0 => array(
					'name'		=> $this->user->lang('uc_edit_char'),
					'link'		=> "javascript:EditChar('".$memberid."')",
					'img'		=> 'edit.png',
					'perm'		=> $this->user->check_auth('u_member_view', false),
				),
				1 => array(
					'name'		=> $this->user->lang('uc_delete_char'),
					'link'		=> "javascript:DeleteChar('".$memberid."')",
					'img'		=> 'delete.png',
					'perm'		=> $this->user->check_auth('u_member_del', false),
				),
				2 => array(
					'name'		=> $this->game->glang('uc_updat_armory'),
					'link'		=> "javascript:UpdateChar('".$memberid."')",
					'img'		=> 'update.png',
					'perm'		=> $this->game->get_importAuth('u_member_view', 'char_update'),
				),
			);
			return $this->jquery->DropDownMenu('actionmenu'.$memberid, $cm_actions, 'images/global','<img src="images/global/edit.png" alt="edit"/>');
		}

		public function get_other_members($member_id){
			$twinks = array();
			foreach($this->data as $id => $details){
			if($details['main_id'] == $this->get_mainid($member_id) && $id != $member_id)
				$twinks[] = $id;
			}
			return $twinks;
		}

		public function get_mainchar_radio($member_id){
			return $this->html->RadioBox('mainchar', array($member_id=>''), (($this->get_is_main($member_id)) ? $member_id : 0), 'input cmainradio');
		}

		public function get_char_defrole($member_id){
			$defaultrole = $this->get_defaultrole($member_id);
			$roles_array = $this->pdh->get('roles', 'memberroles', array($this->pdh->get('member', 'classid', array($member_id)), true));
			return $this->html->DropDown('defaultrole_'.$member_id, $roles_array, $defaultrole, '', '', 'input cdefroledd');
		}

		public function get_twink($member_id){
			if ($this->get_is_main($member_id)){
				return 'Main';
			}else{
				return 'Twink';
			}
		}

		public function get_html_twink($member_id){
			if ($this->get_is_main($member_id)){
				$main_id = $member_id;
				$text = 'Main';
			}else{
				$main_id = $this->get_mainid($member_id);
				$text = 'Twink';
			}
			$twinks = $this->get_other_members($main_id);
			$htmllist = "Main: ".$this->get_name($main_id)."<br />";
			foreach($twinks as $twinkid){
				$htmllist .= $this->get_name($twinkid)."<br />";
			}
			return $this->html->ToolTip($htmllist, $text);
		}

		public function comp_twink($params1, $params2){
			if($this->get_is_main($params1[0]) == $this->get_is_main($params2[0])){
				if($this->get_name($params1[0]) == $this->get_name($params2[0]))
					return 0;
				else return ($this->get_name($params1[0]) < $this->get_name($params2[0])) ? -1 : 1;
			}
			return $this->get_is_main($params1[0]) ? -1 : 1;
		}

		public function get_memberlink($member_id, $base_url, $url_suffix = ''){
			return $base_url.$this->SID . '&amp;member_id='.$member_id.$url_suffix;
		}

		public function get_html_memberlink($member_id, $base_url, $url_suffix = '', $rank_prefix = false, $rank_suffix = false, $chartooltip=false){
			$ctt = '';
			if ($chartooltip) {
				chartooltip_js();
				$ctt = ' class="chartooltip" title="'.$member_id.'"';
			}
			return '<a href="'.$this->get_memberlink($member_id, $base_url, $url_suffix).'"'.$ctt.'>'.$this->get_html_name($member_id, $rank_prefix, $rank_suffix).'</a>';
		}

		public function comp_memberlink($params1, $params2){
			return ($this->get_name($params1[0]) < $this->get_name($params2[0])) ? -1  : 1 ;
		}

		public function get_editbutton($id){
			return '<span onclick="EditChar('.$id.')" class="hand"><img src="'.$this->root_path.'images/glyphs/edit.png" alt="Edit" width="16" height="16" border="0" /></span>';
		}

		public function get_delete_requested(){
			$chars = array();
			if(is_array($this->data)){
				foreach($this->data as $id => $details){
					if($details['requested_del'] == '1')
						$chars[]	= $id;
				}
			}
			return $chars;
		}

		public function get_confirm_required(){
			$chars = array();
			if(is_array($this->data)){
				foreach($this->data as $id => $details){
					if($details['require_confirm'] == '1')
					$chars[]	= $id;
				}
			}
			return $chars;
		}

		public function get_user($memberid){
			return $this->data[$memberid]['user'];
		}

		public function get_html_user($memberid){
			return $this->pdh->get('user', 'name', array($this->get_user($memberid)));
		}
		
		public function comp_user($params1, $params2) {
			return strcasecmp($this->pdh->get('user', 'name', array($this->get_user($params1[0]))), $this->pdh->get('user', 'name', array($this->get_user($params2[0]))));
		}

		public function get_search($search_value) {
			$arrSearchResults = array();
			if (is_array($this->data)){
				foreach($this->data as $id => $value) {
					if(stripos($value['name'], $search_value) !== false OR
					stripos($value['class_name'], $search_value) !== false OR
					stripos($value['race_name'], $search_value) !== false OR
					stripos($this->get_rankname($id), $search_value) !== false) {

						$arrSearchResults[] = array(
							'id'	=> $this->game->decorate('classes', array($this->pdh->get('member', 'classid', array($id)))).$this->game->decorate('races', array($this->pdh->get('member', 'raceid', array($id)), $this->get_gender($id))),
							'name'	=> $this->get_html_name($id),
							'link'	=> $this->root_path.'viewcharacter.php'.$this->SID.'&amp;member_id='.$id,
						);
					}
				}
			}
			return $arrSearchResults;
		}

		public function get_html_caption_profile_field($params){
			return $this->pdh->get('profile_fields', 'language', array($params));
		}
	}//end class
}//end if
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_r_member', pdh_r_member::__shortcuts());
?>