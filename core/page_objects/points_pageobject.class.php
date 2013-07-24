<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2007
 * Date:		$Date: 2013-03-25 12:12:44 +0100 (Mo, 25 Mrz 2013) $
 * -----------------------------------------------------------------------
 * @author		$Author: godmod $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 13246 $
 *
 * $Id: listcharacters.php 13246 2013-03-25 11:12:44Z godmod $
 */

class points_pageobject extends pageobject {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'pdh', 'game', 'config', 'core', 'routing');
		return array_merge(parent::__shortcuts(), $shortcuts);
	}

	public function __construct() {
		$handler = array();
		parent::__construct(false, $handler, array());
		$this->process();
	}

	public function display(){
		$sort = $this->in->get('sort');

		$show_inactive	= false;
		$show_hidden	= false;
		$show_twinks	= $this->config->get('pk_show_twinks');

		if($this->in->exists('show_inactive')){
			$show_inactive = true;
		}

		if($this->in->exists('show_hidden')){
			$show_hidden = true;
		}

		if($this->in->exists('show_twinks')){
			$show_twinks = true;
		}

		//redirect on member compare
		if($this->in->exists('compare_b') && $this->in->get('compare_b') == $this->user->lang('compare_members')){
			$sort_suffix = (isset($sort))? '&amp;sort='.$sort : '';
			if($this->in->exists('selected_ids')){
				$compare_link	= $this->routing->build('points', false, false, true, true).'&mdkpid='.$mdkpid.$sort_suffix.'&amp;filter=Member:'.implode(',', $this->in->getArray('selected_ids', 'int'));
				redirect($compare_link);
			}else{
				$compare_link	= $this->routing->build('points', false, false, true, true).'&mdkpid='.$mdkpid.$sort_suffix;
				redirect($compare_link);
			}
		}

		//DKP Id
		$mdkp_suffix = '';
		$detail_settings = $this->pdh->get_page_settings('listmembers', 'hptt_listmembers_memberlist_detail');
		if(!$this->in->exists('mdkpid') && isset($detail_settings['default_pool'])) {
			$mdkpid = $detail_settings['default_pool'];
		} else {
			$mdkpid = $this->in->get('mdkpid', 0);
		}
		if($mdkpid == 0){
			$hptt_page_settings = $this->pdh->get_page_settings('listmembers', 'hptt_listmembers_memberlist_overview');
		}else{
			$hptt_page_settings = $detail_settings;
			unset($detail_settings);
			$mdkp_suffix = $mdkpid;
		}

		//Filter
		$is_compare = false;
		if ($this->in->exists('filter')){
			$filter = $this->in->get('filter');
			if(strpos($filter, 'Member') !== false){
				$filter_array[] = array('name' => $this->user->lang('compare_members'), 'value' => $filter);
				$is_compare = true;
			}
		} else {
			$filter = 'none';
		}

		//Multidkp selection output
		$multilist = $this->pdh->get('multidkp', 'id_list', array());
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

		$filter_array = $this->game->get('filters');
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
		
		$arrToolbarItems = array(				
			array(
				'icon'	=> 'icon-plus',
				'js'	=> 'onclick="window.location=\''.$this->server_path."admin/manage_raids.php".$this->SID.'&upd=true\';"',
				'check' => 'a_raid_add',
				'title' => $this->user->lang('adding_raid'),
			),
			array(
				'icon'	=> 'icon-edit',
				'js'	=> 'onclick="window.location=\''.$this->server_path."admin/manage_members.php".$this->SID.'\';"',
				'check' => 'a_members_man',
				'title' => $this->user->lang('manage_members'),
			),
			array(
				'icon'	=> 'icon-list',
				'js'	=> 'onclick="window.location=\''.$this->server_path."admin/manage_raids.php".$this->SID.'\';"',
				'check' => 'a_raid_',
				'title' => $this->user->lang('manraid_title'),
			),
		);

		//footer stuff
		if($is_compare){
			$footer_text	= sprintf($this->user->lang('listmembers_compare_footcount'), count($view_list));
		}else{
			$footer_text	= sprintf($this->user->lang('listmembers_footcount'), count($view_list));
		}

		$hptt = $this->get_hptt($hptt_page_settings, $full_list, $view_list, array('%dkp_id%' => $mdkpid, '%link_url%' => $this->routing->build('character', false, false, false), '%link_url_suffix%' => '', '%with_twink%' => !intval($this->config->get('pk_show_twinks')), '%use_controller%' => true), $mdkp_suffix);
		$hptt->setPageRef($this->strPath);
		$myleaderboard			= registry::register('html_leaderboard');
		$leaderboard_settings	= $this->pdh->get_page_settings('listmembers', 'listmembers_leaderboard');
		$jqToolbar = $this->jquery->toolbar('listcharacters', $arrToolbarItems, array('position' => 'bottom'));
		$lb_id = $this->in->get('lb_mdkpid', $leaderboard_settings['default_pool']);
		$lb_id = ($this->in->get('lbc', 0)) ? $lb_id : $mdkpid;
		
		$this->tpl->assign_vars(array (
			'LEADERBOARD'				=> $myleaderboard->get_html_leaderboard($lb_id, $view_list, $leaderboard_settings),
			'POINTOUT'					=> $hptt->get_html_table($sort, $suffix, null, null, $footer_text),
			'BUTTON_NAME'				=> 'compare_b',
			'S_MANAGE_LINK'				=> ($this->user->check_auth('a_members_man', false)),
			'SHOW_INACTIVE_CHECKED'		=> ($show_inactive)?'checked="checked"':'',
			'SHOW_HIDDEN_RANKS_CHECKED'	=> ($show_hidden)?'checked="checked"':'',
			'SHOW_TWINKS_CHECKED'		=> ($show_twinks)?'checked="checked"':'',
			'S_SHOW_TWINKS'				=> !$this->config->get('pk_show_twinks'),
			'LISTCHARS_TOOLBAR'			=> $jqToolbar['id'],
			'MDKP_POOLNAME'				=> ($mdkpid > 0) ? $this->pdh->get('multidkp', 'name', array($mdkpid)) : '',
			'LBC_VALUE'					=> ($this->in->get('lbc', 0)),
		));

		$this->set_vars(array(
			'page_title'		=> $this->user->lang('listmembers_title'),
			'template_file'		=> 'listcharacters.html',
			'show_article_subheader' => false,
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