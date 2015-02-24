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

class team_pageobject extends pageobject {
	public function __construct() {
		$handler = array();

		parent::__construct(false, $handler, array());
		$this->process();
	}

	public function display(){
		$usergroups		= $this->pdh->get('user_groups', 'team_groups', array());
		$arrSorted = $this->pdh->sort($usergroups, 'user_groups', 'sortid');
	
		$special_user	= unserialize(stripslashes($this->config->get('special_user')));
		$special_user = (!$special_user) ? array() : $special_user;
		unset($usergroups[0]); //Guestgroup
		
		foreach ($usergroups as $group){
			$user_in_group = $this->pdh->get('user_groups_users', 'user_list', array($group));

			//Build Group Count
			$usercount = 0;
			$teamArray = array();
			foreach ($user_in_group as $usr){
				if (!in_array($usr, $special_user)){
					$usercount++;
					$teamArray[] = $usr;
				}
			}

			
			if (is_array($user_in_group) && count($teamArray) > 0){
				$user_count = count($teamArray);
				$view_list = $teamArray;

				//Output
				$hptt_page_settings	= $this->pdh->get_page_settings('teamlist', 'hptt_team_list');
					
				$hptt				= $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%link_url%' => 'team.php', '%link_url_suffix%' => '', '%use_controller%' => true), 'g.'.$group.'.u'.$this->user->id);
				$hptt->setPageRef($this->strPath);
				
				//footer
				$footer_text		= sprintf($this->user->lang('user_group_footcount'), $user_count);

				$this->tpl->assign_block_vars('group_row', array (
						'NAME'				=> $this->pdh->get('user_groups', 'name', array($group)),
						'ID'				=> $group,
						'PAGE_OUT'			=> $hptt->get_html_table($sort, $pagination_suffix),
				));
				
			}
		}
	
		$this->jquery->Dialog('usermailer', $this->user->lang('adduser_send_mail'), array('url'=>$this->server_path."email.php".$this->SID."&user='+userid+'", 'width'=>'660', 'height'=>'450', 'withid'=>'userid'));
		$this->tpl->add_meta('<link rel="canonical" href="'.$this->env->link.$this->routing->build('Team', false, false, false, true).'" />');
		$this->set_vars(array(
			//'page_title'		=> $this->user->lang('team'),
			'template_file'		=> 'team.html',
			'display'			=> true)
		);
	}

}
?>