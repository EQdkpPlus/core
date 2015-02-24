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

class usergroup_pageobject extends pageobject {
	public static $shortcuts = array('crypt'=>'encrypt');

	public function __construct() {
		$handler = array();
		$this->user->check_auth('u_userlist');
		parent::__construct(false, $handler, array());
		if(empty($this->url_id)){
			redirect($this->controller_path_plain.'User/'.$this->SID);
		}
		$this->process();
	}
	
	public function display(){
		$groupID = $this->url_id;
		if ($this->pdh->get('user_groups', 'hide', array($groupID))) redirect($this->controller_path_plain.'User/'.$this->SID);
		
		//Sort
		$sort			= $this->in->get('sort');
		$sort_suffix	= '&amp;sort='.$sort.'&amp;g='.$groupID;

		$start				= $this->in->get('start', 0);
		$pagination_suffix	= ($start) ? '&amp;start='.$start.'&amp;g='.$groupID : '';
		
		$arrUsers = $this->pdh->get('user_groups_users', 'user_list', array($groupID));
		$view_list = $view_list_grpleader = array();
		foreach($arrUsers as $user_id){
			if ($this->pdh->get('user_groups_users', 'is_grpleader', array($user_id, $groupID))){
				$view_list_grpleader[] = $user_id;
			} else {
				$view_list[] = $user_id;
			}
		}
		
		//Output
		$hptt_page_settings	= $this->pdh->get_page_settings('listusers', 'hptt_listusers_userlist');
			
		$hptt				= $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%link_url%' => $this->routing->simpleBuild('user'), '%link_url_suffix%' => '', '%use_controller%' => true), $groupID);
		$hptt->setPageRef($this->strPath);
		
		//footer
		$user_count			= count($view_list);
		$footer_text		= sprintf($this->user->lang('listusers_footcount'), $user_count, $this->user->data['user_rlimit']);

		
		$this->tpl->add_meta('<link rel="canonical" href="'.$this->env->link.$this->routing->build('Usergroup', $this->pdh->get('user_groups', 'name', array($groupID)), $groupID, false, true).'" />');
		
		$this->tpl->assign_vars(array (
			'PAGE_OUT'			=> $hptt->get_html_table($sort, $pagination_suffix, $start, $this->user->data['user_rlimit'], $footer_text),
			'USER_PAGINATION'	=> generate_pagination($this->strPath.$this->SID.$sort_suffix, $user_count, $this->user->data['user_rlimit'], $start),
			'GROUPNAME'			=> $this->pdh->get('user_groups', 'name', array($groupID)),
			'S_DISPLAY_GROUP'	=> true,
		));
		
		
		
		$hptt_grpleader		= $this->get_hptt($hptt_page_settings, $view_list_grpleader, $view_list_grpleader, array('%link_url%' => $this->routing->simpleBuild('user'), '%link_url_suffix%' => '', '%use_controller%' => true), $groupID.'_grpleader');
		$hptt_grpleader->setPageRef($this->strPath);
		//footer
		$user_count			= count($view_list_grpleader);
		$footer_text		= sprintf($this->user->lang('listusers_footcount'), $user_count, $this->user->data['user_rlimit']);

		$this->tpl->assign_vars(array (
			'PAGE_OUT_GRPLEADER'=> $hptt_grpleader->get_html_table($sort, $pagination_suffix),
		));	

		$this->jquery->Dialog('usermailer', $this->user->lang('adduser_send_mail'), array('url'=>$this->server_path."email.php".$this->SID."&user='+userid+'", 'width'=>'660', 'height'=>'450', 'withid'=>'userid'));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('user_list'),
			'template_file'		=> 'listusers.html',
			'display'			=> true)
		);
	}
}
?>