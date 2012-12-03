<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:				http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2009
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2006-2009 EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev$
 * 
 * $Id$
 */

	define('EQDKP_INC', true);
	define('IN_ADMIN', true);
	$eqdkp_root_path = './../';
	include_once($eqdkp_root_path . 'common.php');
	include($eqdkp_root_path.'core/extensions.class.php');
	$online_repo = false;
	
	$user->check_auth('a_plugins_man');
	$user->check_hostmode();
	$pmanager   = new Extensions();

	$mode = ( isset($_GET['mode']) ) ? $_GET['mode'] : 'list';
	switch ( $mode ){
		case 'reset_repocache':
        $db->query('TRUNCATE TABLE __repository');
        break;
    case 'reset_blacklist':
        $core->config_set('onlinerepo_blacklisted', 'false');
        $core->config_set('onlinerepo_updated', '');
        break;
    case 'upload':
				$installit = $pmanager->UploadPackage('file', true);
  			$pmanager->ShowStatus($installit);
  			redirect('admin/extensions.php');
    		break;
    
    case 'download':
    		if($in->get('package')){
				  $pmanager->DownloadPackage($in->get('package'));
				}
    		break;
	}
	
	// The online Repo...
	if($online_repo){
		$myfilter = ($in->get('cat_filter', 0) > 0) ? $in->get('cat_filter', 0) : 1;
		$plist = $pmanager->FetchPackageList($myfilter);
		echo $pmanager->debug;
		if(is_array($plist)){
			foreach($plist as $values){
				$plugin_inst	= $pm->check(PLUGIN_INSTALLED, $values['plugin']);
				$version_comp	= ($pm->get_data($values['plugin'], 'version')) ? compareVersion($values['version'], $pm->get_data($values['plugin'], 'version')) : '';
				$instupd_icon = '';
				if($plugin_inst && ($version_comp == 0 || $version_comp == -1) && $version_comp != ''){
					$instupd_icon   = '<img src="../images/installed.png" alt="'.$user->lang['pi_installed'].'" title="'.$user->lang['pi_installed'].'">';
				}elseif($plugin_inst && $version_comp == 1){
					$instupd_icon   = '<img src="../images/update.png" alt="'.$user->lang['pi_update'].'" title="'.$user->lang['pi_update'].'">';
				}
	            
				$tpl->assign_block_vars('online_row', array(
					'LINK'          => 'extensions.php?s=&mode=download&package='.$values['plugin'],
					'PLUGIN'        => $values['plugin'],
					'VERSION'       => $values['version'],
					'AUTHOR'        => $values['author'],
					'DESCRIPTION'   => ($values['description']) ? $html->ToolTip($values['description'], $html->toggleIcons($values['description'],'info.png','info_off.png','images/',$user->lang['description'],false)) : false,
					'CHANGELOG'     => ($values['changelog']) ? $html->ToolTip($values['changelog'], $html->toggleIcons($values['description'],'changelog.png','changelog_off.png','images/',$user->lang['description'],false)) : false,
					'SHORTDESC'     => $values['shortdesc'],
					'DATE'          => date('d.m.Y', $values['date']),
					'INST_UPD'      => ($instupd_icon) ? $instupd_icon.' ('.$pm->get_data($values['plugin'], 'version').')' : '',
					'ROW_CLASS'     => $core->switch_row_class(),
				));
			}
		}
	}
	
	// Tabs..
	$jquery->Tab_header('plus_plugins_tab', true);
	if($in->get('cat_filter', 0) > 0){
		$jquery->Tab_Select('admininfos_tabs', '1');
	}

	$last_update = ($core->config['onlinerepo_updated'] > 0) ? date('d.m.Y H:i', $core->config['onlinerepo_updated']) : '--';
	$tpl->assign_vars(array(
		'F_MANAGE'						=> 'extensions.php?s=&mode=upload',
		'F_FILTER'						=> 'extensions.php?s=',
		'UPD_LINK'						=> 'extensions.php?s=&mode=reset_repocache',
		'REPO_AVAILABLE'			=> ($core->config['onlinerepo_blacklisted'] == 'true') ? false : true,
		'REPO_ON'							=> $online_repo,
		'FILTER_CAT'					=> $html->DropDown('cat_filter', $pmanager->Categories(), $in->get('cat_filter', 0), '', 'onchange="javascript:form.submit();"', 'input'),
		
		// Tabs
		'L_TAB_INSTALL'				=> $user->lang['pi_manualupload'],
		'L_TAB_REPO'					=> $user->lang['pi_onlinerepo'],
		
		'L_MANUALUPLOAD_INFO'	=> $user->lang['pi_manualupload_info'],
		'L_MANUALUPLOAD'			=> $user->lang['pi_manualupload'],
		'L_CHOOSE_FILE'				=> $user->lang['pi_choose_file'],
		'B_UPLOAD'						=> $user->lang['pi_upload_button'],
		'L_ONLINEREPO'    		=> $user->lang['pi_onlinerepo'],
		              
		'L_VERSION'						=> $user->lang['pi_version'],
		'L_REPOBLOCKED_TXT'		=> $user->lang['pi_repoblocked'],
		'L_AUTHOR'						=> $user->lang['pi_author'],
		'L_DATE'							=> $user->lang['pi_date'],
		'L_SHORTDESC'					=> $user->lang['pi_shortdec'],
		'L_PLUGIN'						=> $user->lang['pi_title'],
		'L_DESCRIPTION'				=> $user->lang['pi_description'],
		'L_DOWNLOAD'					=> $user->lang['pi_download'],
		'L_ACTION'						=> $user->lang['pi_action'],
		'L_CATEGORY'					=> $user->lang['pi_category'],
		'L_INSTALLED'					=> $user->lang['pi_installed'],
		'L_UPDATE'						=> $user->lang['pi_update'],
		'L_UPDATE_NOW'				=> $user->lang['pi_updnow'],
		'L_LAST_UPDATED'			=> $user->lang['pi_lastupdate'].': '.$last_update,
	));

	$core->set_vars(array(
		'page_title'    		=> $user->lang['manage_extensions'],
		'template_file' 		=> 'admin/extensions.html',
		'display'       		=> true)
	);
?>

