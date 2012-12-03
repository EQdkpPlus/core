<?php
/*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2006
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2006-2008 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev$
 * 
 * $Id$
 */

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = '../';
include_once($eqdkp_root_path . 'common.php');

// Check user permission
$user->check_auth('a_config_man');

if ($in->get('save')){
	$portal->EnablePortalModules();
	$core->message( $user->lang['pk_succ_saved'], $user->lang['save_suc'],'green');
}

// Install the Plugins if required
$portal_module = $portal->InstallIfRequired();

$sql = 'SELECT * FROM __portal';
$tpl->add_js('$(document).ready(function(){
														 
		
					$("#left1, #left2, #middle, #right, #bottom").sortable({
					connectWith: \'.connectedSortable\',
					cancel: \'.not-sortable, tbody .not-sortable,.not-sortable tbody, .th_add, .td_add\',
					cursor: \'pointer\',
				 	start: function(event, ui){
						var classI = $(ui.item).attr("class");
						classI = classI.toString();
						
						if (classI.indexOf("Pleft1") == -1){
							$("#left1").addClass("red");
						} else {
							$("#left1").addClass("green");
						};
						if (classI.indexOf("Pleft2") == -1){
							$("#left2").addClass("red");
						} else {
							$("#left2").addClass("green");
						};
						if (classI.indexOf("Pmiddle") == -1){
							$("#middle").addClass("red");
						} else {
							$("#middle").addClass("green");
						};
						if (classI.indexOf("Pright") == -1){
							$("#right").addClass("red");
						} else {
							$("#right").addClass("green");
						};
						if (classI.indexOf("Pbottom") == -1){
							$("#bottom").addClass("red");
						} else {
							$("#bottom").addClass("green");
						};
					},
					stop: function(event, ui){
						$("#left1, #left2, #middle, #right, #bottom").removeClass("red");
						$("#left1, #left2, #middle, #right, #bottom").removeClass("green");
					},
					
				 receive: function(event, ui){
					var classI = $(ui.item).attr("class");
					classI = classI.toString();				
					
					var pos = $(ui.item).parents().attr("id");
					if (pos == "left1" && classI.indexOf("Pleft1") == -1){$(ui.sender).sortable(\'cancel\');};
					if (pos == "left2" && classI.indexOf("Pleft2") == -1){$(ui.sender).sortable(\'cancel\');};
					if (pos == "middle" && classI.indexOf("Pmiddle") == -1){$(ui.sender).sortable(\'cancel\');};
					if (pos == "right" && classI.indexOf("Pright") == -1){$(ui.sender).sortable(\'cancel\');};
					if (pos == "bottom" && classI.indexOf("Pbottom") == -1){$(ui.sender).sortable(\'cancel\');};
					
					var id = $(ui.item).attr("id");
					$("#block_"+id).val(pos);
					}
			}).disableSelection();
		
		});
		');

// Filter me!
$filter_sql = '';
if($in->get('position') > 0 && $in->get('position') != '100'){
	$filter_sql[] = "position='".$portal->positions[$in->get('position')-1]."'";	
}
if($in->get('enabled') > 0 && $in->get('enabled') != '100'){
	$filter_sql[] = "enabled='".($in->get('enabled')-1)."'";	
}
if($in->get('visibility') > 0 && $in->get('visibility') != '100'){
	$filter_sql[] = "visibility='".($in->get('visibility')-1)."'";	
}

if(is_array($filter_sql)){
	$myii = 0;
	foreach($filter_sql as $filtersql){
		$sqlwhereorand = ($myii === 0) ? ' WHERE' : ' AND';
		$sql .= $sqlwhereorand.' '.$filtersql;
		$myii++;
	}	
}

// Order me!
$sql .=' ORDER BY number ASC';
if ($members_result = $db->query($sql)) 
{
	while ( $row = $db->fetch_record($members_result) )
	{
		$portalinfos	= $portal_module[$row['path']];
		$portal_contact = (strpos($portalinfos['contact'], '@')=== false) ? $portalinfos['contact'] : 'mailto:'.$portalinfos['contact'];
		$user->lang		= array_merge($user->lang, $pluslang->PortalLanguage(array($row['path'] => $row['plugin'])));
		$pmodule_desc	= ($user->lang[$row['path'].'_desc']) ? $user->lang[$row['path'].'_desc'] : $portalinfos['description'];
		$pmodule_name	= ($user->lang[$row['path'].'_name']) ? $user->lang[$row['path'].'_name'] : $portalinfos['name'];
		
		// User Rights
		$drpdwn_rights = array(
			'0'   => $user->lang['portal_rights0'],
			'1'   => $user->lang['portal_rights1'],
			'2'   => $user->lang['portal_rights2']
		);

		$class = '';
		foreach ($portalinfos['positions'] as $key=>$value){
			$class .= 'P'.$value.' ';
		};

		$tpl->assign_block_vars($row['position'].'_row', array(
			'NAME'			=> $pmodule_name,
			'ENABLED'		=> ($row['enabled'] == '0') ? 'style="visibility:hidden; position:absolute; "' : '',
			'CLASS'			=> $class,
			'ID'			=> $row['id'],
			'POS'			=> $row['position'],
		));
		
		$tpl->assign_block_vars('plugins_row', array(
			'ROW_CLASS'     => $core->switch_row_class(),
			'ID'            => $row['id'],
			'S_SETTINGS'    => ($portalinfos['settings'] == '1') ? true : false,
			'S_PLUGIN'      => ($row['plugin']) ? true : false,
			'PLUGIN'		=> ($row['plugin']) ? $user->lang[$row['plugin']] : '',
			'NUMBER'        => $row['number'],
			'NAME'          => $html->ToolTip($pmodule_desc, $pmodule_name),
			'CONTACT'       => '<a href="'.$portal_contact.'">'.$portalinfos['author'].'</a>',
			'VERSION'       => $portalinfos['version'],
			'ENABLED'       => $html->CheckBox('enabled['.$row['id'].']', false, $row['enabled'], $row['id'], '', 'onclick="if(this.checked == false){hide_item('.$row['id'].');} else {show_item('.$row['id'].');};"'),
			'RIGHTS'        => $html->DropDown('rights['.$row['id'].']', $drpdwn_rights , $row['visibility']),
			'COLLAPSABLE'   => $html->CheckBox('collapsable['.$row['id'].']', false, $row['collapsable']),
		));
	}
}

// Filter:
$filter_position[100] = $user->lang['portalplugin_filter1_all'];
foreach($portal->positions as $myPosID=>$myPos){
	$filter_position[$myPosID+1] = $user->lang['portalplugin_'.$myPos];
}

$filter_enabled = array(
	100 => $user->lang['portalplugin_filter2_all'],
	1	=> $user->lang['portalplugin_disabled'],
	2	=> $user->lang['portalplugin_enabled'],
);

$filter_rights = array(
	100 => $user->lang['portalplugin_filter3_all'],
	1   => $user->lang['portal_rights0'],
	2	=> $user->lang['portal_rights1'],
	3   => $user->lang['portal_rights2']
);

// Jquery
$jquery->Dialog('Settings', $user->lang['portalplugin_winname'], array('url'=>$eqdkp_root_path."admin/portalsettings.php?id='+moduleid+'", 'width'=>'660', 'height'=>'400', 'withid'=>'moduleid'));
$jquery->Tab_header('portal_tabs', true);

// assign the vars
$tpl->assign_vars(array(
    'F_PLUGINS'             => 'manage_portal.php' . $SID,
    
    'POS_FILTER'			=> $html->DropDown('position', $filter_position , $in->get('position'),'', 'onchange="javascript:form.submit();"'),
    'ENABLED_FILTER'		=> $html->DropDown('enabled', $filter_enabled , $in->get('enabled'),'','onchange="javascript:form.submit();"'),
    'PERM_FILTER'			=> $html->DropDown('visibility', $filter_rights , $in->get('visibility'),'','onchange="javascript:form.submit();"'),
    
    'L_NAME'                => $user->lang['portalplugin_name'],
    'L_VERSION_N'           => $user->lang['portalplugin_version'],
    'L_CONTACT'             => $user->lang['portalplugin_contact'],
    'L_POSITION'            => $user->lang['portalplugin_order'],
    'L_RIGHTS'              => $user->lang['portalplugin_rights'],
    'L_COLLAPSE'            => $user->lang['portal_collapsable'],
    'L_ORIENTATION'         => $user->lang['portalplugin_orientation'],
    'L_ENABLED'             => $user->lang['portalplugin_enabled'],
    'L_SAVE'                => $user->lang['save'],
    'L_EDIT'                => $user->lang['edit'],
    'L_SETTINGS'            => $user->lang['portalplugin_settings'],
    'L_MORE'                => $user->lang['more_moduls'],
    'L_FILTER'				=> $user->lang['portalplugin_filter'],
	'L_LEFT1'				=> $user->lang['portalplugin_left1'],
	'L_LEFT2'				=> $user->lang['portalplugin_left2'],
	'L_MIDDLE'				=> $user->lang['portalplugin_middle'],
	'L_RIGHT'				=> $user->lang['portalplugin_right'],
	'L_BOTTOM'				=> $user->lang['portalplugin_bottom'],
	'L_NEWS'				=> $user->lang['news'],
	'L_MAINMENU'			=> $user->lang['info_opt_ml_1'],
	'L_OVERVIEW'			=> $user->lang['portal_overview'],
	'L_POSITIONING'			=> $user->lang['portal_positioning'],
	'L_DRAGNDROP_INFO'		=> $user->lang['portal_dragndrop_info'],
	'L_RESET'				=> $user->lang['reset'],
    )
);

$core->set_vars(array(
    'page_title'    => $user->lang['portalplugin_management'],
    'template_file' => 'admin/manage_portal.html',
    'display'       => true)
);
?>