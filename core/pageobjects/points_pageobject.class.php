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

class points_pageobject extends pageobject {

	public function __construct() {
		$handler = array();
		parent::__construct(false, $handler, array());
		$this->process();
	}

	public function display(){
		$sort = $this->in->get('sort');

		$show_inactive	= false;
		$show_hidden	= false;
		$show_twinks	= $this->config->get('show_twinks');
		$sort_suffix = (isset($sort))? '&amp;sort='.$sort : '';

		if($this->in->exists('show_inactive')){
			$show_inactive = true;
			$sort_suffix = '&amp;show_inactive=1';
		}

		if($this->in->exists('show_hidden')){
			$show_hidden = true;
			$sort_suffix = '&amp;show_hidden=1';
		}

		if($this->in->exists('show_twinks')){
			$show_twinks = true;
			$sort_suffix = '&amp;show_twinks=1';
		}
		
		//DKP Id
		$mdkp_suffix = '';
		$detail_settings = $this->pdh->get_page_settings('listmembers', 'hptt_listmembers_memberlist_detail');
		if(!$this->in->exists('mdkpid') && isset($detail_settings['default_pool'])) {
			$mdkpid = $detail_settings['default_pool'];
		} else {
			$mdkpid = $this->in->get('mdkpid', 0);
		}

		//redirect on member compare
		if($this->in->exists('compare_b') && $this->in->get('compare_b') == $this->user->lang('compare_members')){
			if($this->in->exists('selected_ids')){
				$compare_link	= $this->routing->build('points', false, false, true, true).'&mdkpid='.$mdkpid.$sort_suffix.'&amp;filter=Member:'.implode(',', $this->in->getArray('selected_ids', 'int'));
				redirect($compare_link);
			}else{
				$compare_link	= $this->routing->build('points', false, false, true, true).'&mdkpid='.$mdkpid.$sort_suffix;
				redirect($compare_link);
			}
		}
	
		
		//Multidkp selection output
		$multilist = $this->pdh->get('multidkp', 'id_list', array());
		
		if($mdkpid == 0){
			$hptt_page_settings = $this->pdh->get_page_settings('listmembers', 'hptt_listmembers_memberlist_overview');
			$defaultPoolOverview = (isset($detail_settings['default_pool_ov'])) ? $detail_settings['default_pool_ov'] : $multilist[0];
		}else{
			$hptt_page_settings = $detail_settings;
			unset($detail_settings);
			$mdkp_suffix = $mdkpid;
		}

		//Filter
		$is_compare = false;
		$filter_array = array();
		if ($this->in->exists('filter')){
			$filter = $this->in->get('filter');
			if(strpos($filter, 'Member') !== false){
				$filter_array[] = array('name' => $this->user->lang('compare_members'), 'value' => $filter);
				$is_compare = true;
			}
		} else {
			$filter = 'none';
		}


		$this->tpl->assign_block_vars('mdkpid_row', array (
			'VALUE'		=> 0,
			'SELECTED'	=> ($mdkpid === 0) ? ' selected="selected"' : '',
			'OPTION'	=> $this->user->lang('overview'),
		));
		if(!empty($multilist)){
			foreach ($multilist as $id) {
				$this->tpl->assign_block_vars('mdkpid_row', array (
					'VALUE'		=> $id,
					'SELECTED'	=> ($mdkpid == $id) ? ' selected="selected"' : '',
					'OPTION'	=> $this->pdh->get('multidkp', 'name', array($id))
				));
			}
		}

		$filter_array = array_merge($filter_array, $this->game->get('filters'));
		if(is_array($filter_array)) {
			foreach($filter_array as $details){
				$this->tpl->assign_block_vars('filter_row', array(
					'VALUE'		=> $details['value'],
					'SELECTED'	=> ( ($filter != 'none') && ($filter == $details['value']) ) ? ' selected="selected"' : '',
					'OPTION'	=> $details['name']
				));
			}
		}

		//Output
		$full_list	= $this->pdh->get('member', 'id_list', array(false, false, false));
		$view_list	= $this->filter_view_list($filter, $this->pdh->get('member', 'id_list', array(!$show_inactive, !$show_hidden, true, !$show_twinks)));


		//Create our suffix
		$suffix		 = '';
		$suffix		.= ($mdkpid > 0)		? '&amp;mdkpid='.$mdkpid	: '';
		$suffix		.= ($filter != 'none')	? '&amp;filter='.$filter	: '';
		$suffix		.= ($show_inactive)		? '&amp;show_inactive=1'	: '';
		$suffix		.= ($show_hidden)		? '&amp;show_hidden=1'		: '';
		$suffix		.= ($show_twinks)		? '&amp;show_twinks=1'		: '';

		//footer stuff
		if($is_compare){
			$footer_text	= sprintf($this->user->lang('listmembers_compare_footcount'), count($view_list));
		}else{
			$footer_text	= sprintf($this->user->lang('listmembers_footcount'), count($view_list));
		}

		$hptt = $this->get_hptt($hptt_page_settings, $full_list, $view_list, array('%dkp_id%' => (($mdkpid == 0) ? $defaultPoolOverview : $mdkpid), '%link_url%' => $this->routing->simpleBuild('character'), '%link_url_suffix%' => '', '%with_twink%' => !intval($this->config->get('show_twinks')), '%use_controller%' => true), $mdkp_suffix);
		$hptt->setPageRef($this->strPath);
		
		if((int)$this->config->get('enable_leaderboard')){
			$leaderboard_settings	= $this->pdh->get_page_settings('listmembers', 'listmembers_leaderboard');
			$lb_id = $this->in->get('lb_mdkpid', $leaderboard_settings['default_pool']);
			$lb_id = ($this->in->get('lbc', 0)) ? $lb_id : $mdkpid;
			if (!$this->config->get('disable_points')){
				$myleaderboard			= registry::register('html_leaderboard');
				$this->tpl->assign_vars(array (
						'LEADERBOARD'				=> $myleaderboard->get_html_leaderboard($lb_id, $view_list, $leaderboard_settings),
				));
			}
		}
		
		$this->tpl->assign_vars(array (
			'POINTOUT'					=> $hptt->get_html_table($sort, $suffix, null, null, $footer_text),
			'BUTTON_NAME'				=> 'compare_b',
			'S_MANAGE_LINK'				=> ($this->user->check_auth('a_members_man', false)),
			'SHOW_INACTIVE_CHECKED'		=> ($show_inactive)?'checked="checked"':'',
			'SHOW_HIDDEN_RANKS_CHECKED'	=> ($show_hidden)?'checked="checked"':'',
			'SHOW_TWINKS_CHECKED'		=> ($show_twinks)?'checked="checked"':'',
			'S_SHOW_TWINKS'				=> !$this->config->get('show_twinks'),
			'MDKP_POOLNAME'				=> ($mdkpid > 0) ? $this->pdh->get('multidkp', 'name', array($mdkpid)) : '',
			'LBC_VALUE'					=> ($this->in->get('lbc', 0)),
		));

		$this->set_vars(array(
			'page_title'		=> ($mdkpid > 0) ? $this->pdh->get('multidkp', 'name', array($mdkpid)) : '',
			'template_file'		=> 'listcharacters.html',
			'display'			=> true
		));
	}

	private function filter_view_list($filter_string, $view_list){
		if($filter_string != '' && $filter_string != 'none'){
			list($filter, $params) = explode(":", $filter_string);

			switch (strtolower($filter)){
				case	'none':	break;
				case	'class':
					$classids = explode(',',$params);
					if(is_array($classids) && !empty($classids)){
						foreach($view_list as $index => $memberid){
							if(in_array($this->pdh->get('member', 'classid', array($memberid)), $classids))
							$temp[]	=$memberid;
						}
						$view_list = $temp;
					}
					break;
				case 'member':
					$memberids = explode(',',$params);
					if(is_array($memberids) && !empty($memberids))
					$view_list = array_intersect($view_list, $memberids);
					break;
			}
		}
		return $view_list;
	}
}

?>