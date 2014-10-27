<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2009
 * Date:		$Date: 2014-07-20 19:43:25 +0200 (So, 20 Jul 2014) $
 * -----------------------------------------------------------------------
 * @author		$Author: wallenium $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 14491 $
 *
 * $Id: team_pageobject.class.php 14491 2014-07-20 17:43:25Z wallenium $
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
					
				$hptt				= $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%link_url%' => 'team.php', '%link_url_suffix%' => '', '%use_controller%' => true), 'g.'.$group);
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
		
		

		$this->set_vars(array(
			//'page_title'		=> $this->user->lang('team'),
			'template_file'		=> 'team.html',
			'display'			=> true)
		);
	}

}
?>