<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2007
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
include_once ($eqdkp_root_path . 'common.php');
require_once($eqdkp_root_path.'core/html_leaderboard.class.php');

class listcharacters extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'pdh', 'game', 'config', 'core');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public function __construct() {
		$handler = array();
		$this->user->check_auth('u_member_view');
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
			$sort_suffix = (isset($sort))? '&amp;sort='.$sort : '';
			if($this->in->exists('selected_ids')){
				$compare_link	= $this->root_path.'listcharacters.php'.$this->SID.'&mdkpid='.$mdkpid.$sort_suffix.'&amp;filter=Member:'.implode(',', $this->in->getArray('selected_ids', 'int'));
				redirect($compare_link);
			}else{
				$compare_link	= $this->root_path.'listcharacters.php'.$this->SID.'&mdkpid='.$mdkpid.$sort_suffix;
				redirect($compare_link);
			}
		}

		//redirect on member compare
		if ( $this->in->exists('manage_b')){
			$manage_link = './admin/manage_members.php'.$this->SID;
			redirect($manage_link);
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
		$hptt = $this->get_hptt($hptt_page_settings, $full_list, $view_list, array('%dkp_id%' => $mdkpid, '%link_url%' => 'viewcharacter.php', '%link_url_suffix%' => '', '%with_twink%' => !intval($this->config->get('pk_show_twinks'))), $mdkp_suffix);
		$myleaderboard			= registry::register('html_leaderboard');
		$leaderboard_settings	= $this->pdh->get_page_settings('listmembers', 'listmembers_leaderboard');
		$this->tpl->assign_vars(array (
			'LEADERBOARD'				=> $myleaderboard->get_html_leaderboard($this->in->get('lb_mdkpid', $leaderboard_settings['default_pool']), $view_list, $leaderboard_settings),
			'POINTOUT'					=> $hptt->get_html_table($sort, $suffix, null, null, $footer_text),
			'BUTTON_NAME'				=> 'compare_b',
			'MANAGE_LINK'				=> ($this->user->check_auth('a_members_man', false)) ? '<input type="submit" name="manage_b" value="" alt="'.$this->user->lang('manage_members').'" title="'.$this->user->lang('manage_members').'" class="bi_manage novalue" />' : '',
			'SHOW_INACTIVE_CHECKED'		=> ($show_inactive)?'checked="checked"':'',
			'SHOW_HIDDEN_RANKS_CHECKED'	=> ($show_hidden)?'checked="checked"':'',
			'SHOW_TWINKS_CHECKED'		=> ($show_twinks)?'checked="checked"':'',
			'S_SHOW_TWINKS'				=> !$this->config->get('pk_show_twinks'),
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('listmembers_title'),
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
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_listcharacters', listcharacters::__shortcuts());
registry::register('listcharacters');
?>