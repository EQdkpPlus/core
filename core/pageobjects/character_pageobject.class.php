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

define('ITEMSTATS', true);

class character_pageobject extends pageobject {

	public function __construct() {
		$handler = array();
		parent::__construct(false, $handler, array());

		$this->process();
	}

	public function display(){
		if (!$this->url_id){
			$strReferer = $this->env->get_referer();
			if (stripos($strReferer, "ref=mc")!==0){
				redirect($this->routing->build('MyCharacters',false,false,true,true));
			} else {
				if ($this->config->get('disable_points'))
					redirect($this->routing->build('roster',false,false,true,true));
				else
					redirect($this->routing->build('points',false,false,true,true));
			}
		}
				
		$member_name	= $this->pdh->get('member', 'name', array($this->url_id));

		if($member_name == ''){
			message_die($this->user->lang('error_invalid_name_provided'));
		}
		
		$withTwinksDKP = ($this->in->exists('with_twinks')) ? $this->in->get('with_twinks') : !$this->config->get('show_twinks');

		// Raid Attendance
		$view_list			= $this->pdh->get('raid', 'raidids4memberid', array($this->url_id));
		$hptt_page_settings	= $this->pdh->get_page_settings('viewmember', 'hptt_viewmember_raidlist');
		$hptt				= $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%link_url%' => $this->routing->simpleBuild('raids'), '%link_url_suffix%' => '', '%with_twink%' => false, '%use_controller%' => true), $this->url_id, 'rsort');
		$hptt->setPageRef($this->strPath);
		$this->tpl->assign_vars(array (
			'RAID_OUT'			=> $hptt->get_html_table($this->in->get('rsort', ''), $this->vc_build_url('rsort'), $this->in->get('rstart', 0), $this->user->data['user_rlimit']),
			'RAID_PAGINATION'	=> generate_pagination($this->vc_build_url('rstart', true), count($view_list), $this->user->data['user_rlimit'], $this->in->get('rstart', 0), 'rstart')
		));

		// Item History
		infotooltip_js();
		$view_list			= $this->pdh->get('item', 'itemids4memberid', array($this->url_id));
		$hptt_page_settings	= $this->pdh->get_page_settings('viewmember', 'hptt_viewmember_itemlist');
		$hptt				= $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%link_url%' => $this->routing->simpleBuild('items'), '%link_url_suffix%' => '', '%itt_lang%' => false, '%itt_direct%' => 0, '%onlyicon%' => 0, '%noicon%' => 0, '%raid_link_url%' => $this->routing->simpleBuild('raids'), '%raid_link_url_suffix%' => '', '%use_controller%' => true), $this->url_id, 'isort');
		$hptt->setPageRef($this->strPath);
		$this->tpl->assign_vars(array (
			'ITEM_OUT'			=> $hptt->get_html_table($this->in->get('isort', ''), $this->vc_build_url('isort'), $this->in->get('istart', 0), $this->user->data['user_ilimit']),
			'ITEM_PAGINATION'	=> generate_pagination($this->vc_build_url('istart', true), count($view_list), $this->user->data['user_ilimit'], $this->in->get('istart', 0), 'istart')
		));

		// Individual Adjustment History
		$view_list = $this->pdh->get('adjustment', 'adjsofmember', array($this->url_id));
		$hptt_page_settings = $this->pdh->get_page_settings('viewmember', 'hptt_viewmember_adjlist');
		$hptt = $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%raid_link_url%' => $this->routing->simpleBuild('raids'), '%raid_link_url_suffix%' => '', '%use_controller%' => true), $this->url_id, 'asort');
		$hptt->setPageRef($this->strPath);
		$this->tpl->assign_vars(array (
			'ADJUSTMENT_OUT' => $hptt->get_html_table($this->in->get('asort', ''), $this->vc_build_url('asort'), $this->in->get('astart', 0), $this->user->data['user_alimit']),
			'ADJUSTMENT_PAGINATION'	=> generate_pagination($this->vc_build_url('astart', true), count($view_list), $this->user->data['user_alimit'], $this->in->get('astart', 0), 'astart')
		));

		//Event-Attendance
		$view_list = $this->pdh->get('event', 'id_list');
		$hptt_page_settings = $this->pdh->get_page_settings('viewmember', 'hptt_viewmember_eventatt');
		$hptt = $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%member_id%' => $this->url_id, '%link_url%' => $this->routing->simpleBuild('events'), '%link_url_suffix%' => '', '%with_twinks%' => false, '%use_controller%' => true), $this->url_id, 'esort');
		$hptt->setPageRef($this->strPath);
		$this->tpl->assign_vars(array (
			'EVENT_ATT_OUT' => $hptt->get_html_table($this->in->get('esort', ''), $this->vc_build_url('esort')),
		));

		// Load member Data to an array
		$member			= $this->pdh->get('member', 'array', array($this->url_id));
		$last_update	= $this->time->user_date((($member['last_update']) ? $member['last_update'] : 0), true);

		// load profile files in the game folder if available
		$profilefolder		= $this->root_path.'games/'.$this->game->get_game().'/profiles/';
		$profile_tplfile	= 'profile_view.html';
		$profile_owntpl		= false;
		if(is_file($profilefolder.'profile_additions.php') || is_file($profilefolder.'profile_view.html')){
			if(is_file($profilefolder.'profile_additions.php')){
				include($profilefolder.'profile_additions.php');
			}
			if(is_file($profilefolder.'profile_view.html')){
				$profile_tplfile	= $profilefolder.'profile_view.html';
			}
			$profile_owntpl		= true;
		}

		$this->comments->SetVars(array(
			'ntfy_user'		=> array($this->pdh->get('member', 'user', (array($this->url_id)))),
		));

		$this->jquery->Tab_header('profile_information', true);
		
		//Member DKP
		$view_list = $this->pdh->get('multidkp', 'id_list');
		$hptt_page_settings = $this->pdh->get_page_settings('viewmember', 'hptt_viewmember_points');
		if (!$withTwinksDKP) $hptt_page_settings['show_detail_twink'] = false;
		$hptt = $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%member_id%' => $this->url_id, '%with_twink%' => $withTwinksDKP, '%use_controller%' => true), $this->url_id.'.'.$withTwinksDKP, 'msort');
		$hptt->setPageRef($this->strPath);
		
		$profile_out = array(
			'PROFILE_OUTPUT'		=> $profile_tplfile,
			'COMMENT'				=> ($this->config->get('enable_comments') == 1) ? $this->comments->Show() : '',
			'LAST_UPDATE'			=> $last_update,
			'MEMBER_POINTS'			=> $hptt->get_html_table($this->in->get('msort', 0), $this->vc_build_url('msort')),
			'L_DKP_NAME'			=> (!$this->config->get('disable_points')) ? $this->config->get('dkp_name')." ".$this->user->lang('information') : $this->user->lang('tab_attendance'),
			'U_VIEW_MEMBER'			=> $this->vc_build_url('', true).'&amp;',

			// common data
			'DATA_GUILDTAG'			=> $this->config->get('guildtag'),
			'CHARDATA_NAME'			=> $member_name,
			'S_CHARDATA_PICTURE'	=> ($this->pdh->get('member', 'picture', array($this->url_id)) != "") ? true : false,
			'CHARDATA_PICTURE'		=> $this->pdh->geth('member', 'picture', array($this->url_id)),
			'DATA_CLASSNAME'		=> $this->pdh->get('member', 'classname', array($this->url_id)),
			'NOTES'					=> (isset($member['notes']) && $member['notes'] != '') ? $member['notes'] : $this->user->lang('no_notes'),
			'S_WITH_TWINK_DKP'		=> ($withTwinksDKP) ? true : false,

			// images
			'IMG_CLASSICON'			=> $this->game->decorate('primary', $member[$this->game->get_primary_class(true)], $this->pdh->get('member', 'profiledata', array($this->url_id))),
			'IMG_FULLYDECORATED'	=> $this->game->decorate_character($this->url_id, $char_id),
		);

		// Add the game-specific Fields...
		foreach($member as $profile_id=>$profile_value){
			$profile_out['DATA_ID_'.strtoupper($profile_id)]	= $profile_value;
			$profile_out['DATA_NAME_'.strtoupper($profile_id)]	= $this->pdh->geth('member', 'profile_field', array($this->url_id, $profile_id, true));
			$profile_out['DATA_'.strtoupper($profile_id)]		= $this->pdh->geth('member', 'profile_field', array($this->url_id, $profile_id));

			$profile_out['L_'.strtoupper($profile_id)]			= $this->game->glang($profile_id);
		}
		
		// the profile fields
		if(!$profile_owntpl){
			$pfields	= $this->pdh->get('profile_fields', 'fields');

			$category	= array();
			$this->jquery->Tab_header('profile_field_data', true);
			if(is_array($pfields) && count($pfields) > 0){
				foreach($pfields as $pfname=>$pfoption){
					// only relevant data!
					$category[$pfoption['category']][$pfname]	= $pfoption;
				}
				foreach($category as $catname=>$catvalues){
					$this->tpl->assign_block_vars('cat_data', array(
						'NAME'		=> ($this->game->glang('uc_cat_'.$catname)) ? $this->game->glang('uc_cat_'.$catname) : $this->user->lang('uc_cat_'.$catname),
						'ID'		=> 'id_'.$catname
					));
					
					foreach($catvalues as $pfname=>$pfoption){
						if($pfoption['category'] == $catname && $pfoption['enabled'] == '1' && $pfoption['type'] != 'hidden'){
							$this->tpl->assign_block_vars('cat_data.pfield_data', array(
								'NAME'		=> $this->pdh->get('member', 'html_caption_profile_field', array($pfname)),
								'VALUE'		=> $this->pdh->get('member', 'html_profile_field', array($this->url_id, $pfname))
							));
						}
					}
				}
				$this->tpl->assign_var('S_PFIELDS', true);
			} else {
				$this->tpl->assign_var('S_PFIELDS', false);
			}
		}else{
			$pfields	= $this->pdh->get('profile_fields', 'fields');
			$custfields	= false;
			foreach($pfields as $pfname=>$pfoption){
				// only relevant data!
				if($pfoption['custom'] == '1' && $pfoption['enabled'] == '1' && $pfoption['sort'] != 0){
					$custfields = true;
					$this->tpl->assign_block_vars('pfield_custom', array(
						'NAME'		=> $this->pdh->get('member', 'html_caption_profile_field', array($pfname)),
						'VALUE'		=> $this->pdh->get('member', 'html_profile_field', array($this->url_id, $pfname))
					));
				}
			}
			$profile_out['CUSTOM_FIELDS']	= ($custfields) ? true : false;
		}

		// Start the Output
		$this->tpl->assign_vars($profile_out);
		
		
		
		
		
		
		//=============================================
		//Point History: Alpha
		
		
		
		
		
		//=============================================

		$this->set_vars(array(
			'page_title'		=> $member_name,
			'template_file'		=> 'viewcharacter.html',
			'display'			=> true
		));
	}

	//Url building
	public function vc_build_url($exclude='', $with_base=false) {
		$base_url = $this->strPath.$this->SID;
		$url_params = array(
			'member_id'	=> $this->in->get('member_id', 0),
			'asort'		=> $this->in->get('asort', ''),
			'esort'		=> $this->in->get('esort', ''),
			'isort'		=> $this->in->get('isort', ''),
			'msort'		=> $this->in->get('msort', ''),
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
}
?>