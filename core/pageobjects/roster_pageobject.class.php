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

class roster_pageobject extends pageobject {

	public function __construct() {
		$handler = array();
		parent::__construct(false, $handler, array());
		$this->process();
	}
	
	private $hptt_page_settings = false;
	private $skip_twinks = true;
	private $skip_hidden = true;
	private $skip_inactive = true;

	public function display(){
		// The Multigame Roster..
		$rosterfolder = $this->root_path.'games/'.$this->game->get_game().'/roster/';
		$this->skip_inactive = ((int)$this->config->get('hide_inactive') == 1) ? true : false;
		$this->skip_twinks = ((int)$this->config->get('show_twinks') == 1) ? false : !(int)$this->config->get('roster_show_twinks');
		$this->skip_hidden = !((int)$this->config->get('roster_show_hidden'));
		
		//Init chartooltip
		chartooltip_js();
		
		if(is_file($rosterfolder.'roster_additions.php')){
			include($rosterfolder.'roster_additions.php');		// include a game specific file
		}else{	//if we dont find the addional roster site, user the default layout
			
			$this->hptt_page_settings = $this->pdh->get_page_settings('roster', 'hptt_roster');
			
			if ($this->config->get('roster_classorrole') == 'role'){
					$members = $this->pdh->aget('member', 'defaultrole', 0, array($this->pdh->get('member', 'id_list', array($this->skip_inactive, $this->skip_hidden, true, $this->skip_twinks))));
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

						$hptt = $this->get_hptt($this->hptt_page_settings, $arrRoleMembers[$key], $arrRoleMembers[$key], array('%link_url%' => $this->routing->simpleBuild('character'), '%link_url_suffix%' => '', '%with_twink%' => $this->skip_twinks, '%use_controller%' => true), 'role_'.$key);
						$hptt->setPageRef($this->strPath);
						$this->tpl->assign_block_vars('class_row', array(
							'CLASS_NAME'	=> $value,
							'CLASS_ICONS'	=> $this->game->decorate('roles', $key, array(), 48),
							'MEMBER_LIST'	=> $hptt->get_html_table($this->in->get('sort')),
						));
					}
				
			} else {
				$arrMembers = $this->pdh->get('member', 'id_list', array($this->skip_inactive, $this->skip_hidden, true, $this->skip_twinks));
				
				$rosterClasses = $this->game->get_roster_classes();

				$arrRosterMembers = array();
				foreach($arrMembers as $memberid){
					$string = "";
					foreach($rosterClasses['todisplay'] as $key => $val){
						$string .= $this->pdh->get('member', 'profile_field', array($memberid, $this->game->get_name_for_type($val)))."_";
					}
						
					$arrRosterMembers[$string][] = $memberid;
				}
				
				$this->build_class_block($rosterClasses['data'], $rosterClasses['todisplay'], $arrRosterMembers);
		
			}
		}

		$this->set_vars(array(
			'template_file'	=> ((is_file($rosterfolder.'roster_view.html')) ? $rosterfolder.'roster_view.html' : 'roster_view.html'),
			'display'		=> true,
			'show_article_subheader' => false,
		));

	}
	
	private function build_class_block($arrData, $arrToDisplay, $arrRosterMembers, $level = 0, $string = ""){
		foreach ($arrData as $key => $val) {
			//Chang Key to Integer
			$key = intval($key);
			
			if (is_array($val)){
				
				$this->tpl->assign_block_vars('class_row', array(
						'CLASS_NAME'	=> $this->game->get_name($arrToDisplay[$level], $key),
						'CLASS_ICONS'	=> $this->game->decorate($arrToDisplay[$level], $key, array(), 48),
						'CLASS_ID'		=> $key,
						'CLASS_LEVEL'	=> $level+1,
						'ENDLEVEL'		=> false,
						'IS_PRIMARY'	=> ($arrToDisplay[$level] == $this->game->get_primary_class()),
				));
				
				$this->build_class_block($val, $arrToDisplay, $arrRosterMembers, $level+1, $string.$key.'_');
				
			} else {
				if ($val == 0) continue;
				$arrMemb = isset($arrRosterMembers[$string.$val.'_']) ? $arrRosterMembers[$string.$val.'_'] : array();
				
				$hptt = $this->get_hptt($this->hptt_page_settings, $arrMemb, $arrMemb, array('%link_url%' => $this->routing->simpleBuild('character'), '%link_url_suffix%' => '', '%with_twink%' => $this->skip_twinks, '%use_controller%' => true), 'class_'.$key);
				$hptt->setPageRef($this->strPath);
				
				$this->tpl->assign_block_vars('class_row', array(
						'CLASS_NAME'	=> $this->game->get_name($arrToDisplay[$level], $val),
						'CLASS_ICONS'	=> $this->game->decorate($arrToDisplay[$level], $val, array(), 48),
						'CLASS_ID'		=> $val,
						'CLASS_LEVEL'	=> $level+1,
						'IS_PRIMARY'	=> ($arrToDisplay[$level] == $this->game->get_primary_class()),
						'ENDLEVEL'		=> true,
						'MEMBER_LIST'	=> $hptt->get_html_table($this->in->get('sort')),
				));
			}
		}
	}
}
?>