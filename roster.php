<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2006
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
include_once($eqdkp_root_path . 'common.php');

class roster extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'pdh', 'game', 'config', 'core', 'html');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public function __construct() {
		$handler = array();
		$this->user->check_auth('u_roster_list');
		parent::__construct(false, $handler, array());
		$this->process();
	}

	public function display(){
		// The Multigame Roster..
		$rosterfolder = $this->root_path.'games/'.$this->game->get_game().'/roster/';
		$skip_inactive = ((int)$this->config->get('hide_inactive') == 1) ? true : false;
		$skip_twinks = ((int)$this->config->get('pk_show_twinks') == 1) ? false : !(int)$this->config->get('roster_show_twinks');
		
		//Init chartooltip
		chartooltip_js();
		
		if(is_file($rosterfolder.'roster_additions.php')){
			include($rosterfolder.'roster_additions.php');		// include a game specific file
		}else{	//if we dont find the addional roster site, user the default layout
			
			$hptt_page_settings = $this->pdh->get_page_settings('roster', 'hptt_roster');
			
			if ($this->config->get('roster_classorrole') == 'role'){
					$members = $this->pdh->aget('member', 'defaultrole', 0, array($this->pdh->get('member', 'id_list', array($skip_inactive, true, true, $skip_twinks))));
					$arrRoleMembers = array();
					foreach ($members as $memberid => $defaultroleid){
						if ((int)$defaultroleid == 0){
							$arrAvailableRoles = array_keys($this->pdh->get('roles', 'memberroles', array($this->pdh->get('member', 'classid', array($memberid)))));
							if (isset($arrAvailableRoles[0])) $arrRoleMembers[$arrAvailableRoles[0]][] = $memberid;
						} else {
							$arrRoleMembers[$defaultroleid][] = $memberid;
						}
					}
					
					foreach ($this->pdh->aget('roles', 'name', 0, array($this->pdh->get('roles', 'id_list', array()))) as $key => $value){
						if ($key == 0) continue;

						$hptt = $this->get_hptt($hptt_page_settings, $arrRoleMembers[$key], $arrRoleMembers[$key], array('%link_url%' => 'viewcharacter.php', '%link_url_suffix%' => '', '%with_twink%' => $skip_twinks), 'role_'.$key);
						
						$this->tpl->assign_block_vars('class_row', array(
							'CLASS_NAME'	=> $value,
							'CLASS_ICONS'	=> $this->game->decorate('roles', array($key)),
							'MEMBER_LIST'	=> $hptt->get_html_table($this->in->get('sort')),
						));
					}
				
			} else {
				$members = $this->pdh->aget('member', 'classid', 0, array($this->pdh->get('member', 'id_list', array($skip_inactive, true, true, $skip_twinks))));
				$arrClassMembers = array();
				foreach ($members as $memberid => $classid){
					$arrClassMembers[$classid][] = $memberid;
				}

				foreach ($this->game->get('classes') as $key => $value){
					if ($key == 0) continue;
					if(empty($arrClassMembers[$key])) $arrClassMembers[$key] = array();

					$hptt = $this->get_hptt($hptt_page_settings, $arrClassMembers[$key], $arrClassMembers[$key], array('%link_url%' => 'viewcharacter.php', '%link_url_suffix%' => '', '%with_twink%' => $skip_twinks), 'class_'.$key);
					
					$this->tpl->assign_block_vars('class_row', array(
						'CLASS_NAME'	=> $value,
						'CLASS_ID'		=> $key ,
						'CLASS_ICONS'	=> $this->game->decorate('classes', array($key, true)),
						'MEMBER_LIST'	=> $hptt->get_html_table($this->in->get('sort')),
					));
				}
			
			}
		}

		$this->tpl->assign_vars(array(
			'ROSTER_OUTPUT'		=> ((is_file($rosterfolder.'roster_view.html')) ? $rosterfolder.'roster_view.html' : 'roster_view.html')
		));

		$this->core->set_vars(array(
			'page_title'	=> $this->user->lang('menu_roster'),
			'template_file'	=> 'roster.html',
			'display'		=> true)
		);
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_roster', roster::__shortcuts());
registry::register('roster');
?>