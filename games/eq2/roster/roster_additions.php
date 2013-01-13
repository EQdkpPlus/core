<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2010
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

$hptt_page_settings = $this->pdh->get_page_settings('roster', 'hptt_roster');
$hptt_page_settings['table_sort_col'] += 1;
$table_presets = $hptt_page_settings['table_presets'];
array_unshift($table_presets, array('name' => 'eq2_charicon', 'sort' => false, 'th_add' => 'width="52"', 'td_add' => ''));
$hptt_page_settings['table_presets'] = $table_presets;

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

		$hptt = $this->get_hptt($hptt_page_settings, $arrRoleMembers[$key], $arrRoleMembers[$key], array('%link_url%' => 'viewcharacter.php', '%link_url_suffix%' => '', '%with_twink%' => !intval($this->config->get('pk_show_twinks'))), 'role_'.$key);
		
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

		$hptt = $this->get_hptt($hptt_page_settings, $arrClassMembers[$key], $arrClassMembers[$key], array('%link_url%' => 'viewcharacter.php', '%link_url_suffix%' => '', '%with_twink%' => !intval($this->config->get('pk_show_twinks'))), 'class_'.$key);
		
		$this->tpl->assign_block_vars('class_row', array(
			'CLASS_NAME'	=> $value,
			'CLASS_ID'		=> $key ,
			'CLASS_ICONS'	=> $this->game->decorate('classes', array($key, true)),
			'MEMBER_LIST'	=> $hptt->get_html_table($this->in->get('sort')),
		));
	}
}
?>