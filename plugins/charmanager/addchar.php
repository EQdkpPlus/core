<?php
/******************************
 * EQDKP PLUGIN: Charmanager
 * (c) 2006 by WalleniuM [Simon Wallmann]
 * http://www.wallenium.de   
 * ------------------
 * addchar.php
 * Changed: December 28, 2006
 * 
 ******************************/

define('EQDKP_INC', true);
define('PLUGIN', 'charmanager');
$eqdkp_root_path = './../../';
include_once($eqdkp_root_path . 'common.php');
include_once('include/usermanagement.class.php');
include_once('include/komptab.class.php');
global $table_prefix;

if (!defined('MEMBER_ADDITION_TABLE')) { define('MEMBER_ADDITION_TABLE', $table_prefix . 'member_additions'); }

$user->check_auth('u_charmanager_add');
$mode['edit']   = false;
$mode['update'] = false;

// Build the memberTools
$CharTools = new CharTools();
$uctabs = new kompTabs();

if (!$pm->check(PLUGIN_INSTALLED, 'charmanager')) { message_die($user->$lang['uc_not_installed']); }
if ($user->data['username']=="") { message_die($user->lang['uc_not_loggedin']); }
$raidplan = $pm->get_plugin('charmanager');

if ($_POST['member_name'] && $_POST['add']) {
  $info = $CharTools->addChar($_POST['member_name']);
}
if ($_GET['mode'] == 'edit' && $_GET['secrethash'] == 'blubber' && $_GET['editid'])
{
    	// Check for existing member name
	$sql = "SELECT m.*, ma.* FROM " . MEMBERS_TABLE ." m
	LEFT JOIN " . MEMBER_ADDITION_TABLE . " ma ON (ma.member_id=m.member_id) 
	WHERE m.member_id = '".$_GET['editid']."'";
	$result = $db->query($sql); 
  $member_data = $db->fetch_record($result);
	$mode['edit'] = true;
}
if( $_POST['memberid'] && $_POST['edit'] ){
  $mode['update'] = true; 
  $info = $CharTools->updateChar($_POST['memberid']);
  $sql = "SELECT * FROM " . MEMBERS_TABLE ." m
	LEFT JOIN " . MEMBER_ADDITION_TABLE . " ma ON (ma.member_id=m.member_id) 
  WHERE m.member_id = '".$_POST['memberid']."'";
	$result = $db->query($sql); 
  $member_data = $db->fetch_record($result);
	$mode['edit'] = true;
}


// Build member drop-down
$eq_classes = array();

        $sql = 'SELECT class_id, class_name, class_min_level, class_max_level FROM ' . CLASS_TABLE .' GROUP BY class_id';
        $result = $db->query($sql);

        while ( $row = $db->fetch_record($result) )
        {

	   if ( $row['class_min_level'] == '0' ) {
             $option = ( !empty($row['class_name']) ) ? stripslashes($row['class_name'])." Level (".$row['class_min_level']." - ".$row['class_max_level'].")" : '(None)';
           } else {
             $option = ( !empty($row['class_name']) ) ? stripslashes($row['class_name'])." Level ".$row['class_min_level']."+" : '(None)';
	   }

            $tpl->assign_block_vars('class_row', array(
                'VALUE' => $row['class_id'],
                'SELECT'  => ( $member_data['member_class_id'] == $row['class_id']) ? ' selected="selected"' : '',
		            'OPTION'   => $option )
		        );

            $eq_classes[] = $row[0];
        }

        $db->free_result($result);
        $eq_races = array();

        $sql = 'SELECT race_id, race_name FROM ' . RACE_TABLE .' GROUP BY race_name';
        $result = $db->query($sql);

        while ( $row = $db->fetch_record($result) )
        {
            $tpl->assign_block_vars('race_row', array(
                'VALUE' => $row['race_id'],
                'SELECT'  => ( $member_data['member_race_id'] == $row['race_id'] ) ? ' selected="selected"' : '',
                'OPTION'   => ( !empty($row['race_name']) ) ? stripslashes($row['race_name']) : '(None)')
		);
            $eq_races[] = $row[0];
        }
        $db->free_result($result);

if ($info){
  if ( $info[0]=='false' && $info[1] != '' && $info[2] != '') {
    $errormsg = $user->lang['uc_error_p1'].$info[1].$user->lang['uc_error_p2'].$info[2].$user->lang['uc_error_p3'];
  }elseif ($info[0]=='false' && $info[1] == '' && $info[2] == ''){
    $errormsg = $user->lang['uc_saved_not'];
  }
}

// post or get
if ($_GET['editid']) {
	$mem_id_temp = $_GET['editid'];
}else{
	$mem_id_temp = $_POST['memberid'];
}

        $tpl->assign_vars(array(
            // Form vars
            'F_ADD_MEMBER'              => 'addchar.php' . $SID,

            'U_INFO_BOX'                => ( $_POST['member_name'] ) ? true : false,
            'U_SAVED_SUCC'              => ( $_POST['member_name'] && $info[0]=='true' ) ? true : false,
            'U_SAVED_NOT'               => ( $_POST['member_name'] && $info[0]=='false' ) ? true : false,

            // Language
            'L_ADD_MEMBER_TITLE'        => $user->lang['uc_add_member'],
            'L_EDIT_MEMBER_TITLE'       => $user->lang['uc_edit_member'],
            'L_INFO_BOX'                => $user->lang['uc_info_box'],
            'L_SAVED_SUCC'              => $user->lang['us_char added'],
            'L_UPDATED_SUCC'            => $user->lang['us_char_updated'],
            'L_SAVED_NOT'               => $errormsg,
            'L_NAME'                    => $user->lang['name'],
            'L_RACE'                    => $user->lang['race'],
            'L_CLASS'                   => $user->lang['class'],
            'L_LEVEL'                   => $user->lang['level'],
            'L_ADD_MEMBER'              => $user->lang['uc_add_char'],
            'L_EDIT_MEMBER'             => $user->lang['uc_save_char'],
            'L_ADD_PIC'									=> $user->lang['uc_add_pic'],
            'L_CHANGE_PIC'							=> $user->lang['uc_change_pic'],
            'L_SUCC_ADDED'							=> $user->lang['uc_pic_added'],
            'L_SUCC_CHANGED'						=> $user->lang['uc_pic_changed'],
            'L_RESET'                   => $user->lang['reset'],
            'L_OVERTAKE'                => $user->lang['overtake_char'],
            'L_CANCEL'                  => $user->lang['uc_button_cancel'],
            'L_GUILD'										=> $user->lang['uc_guild'],
            'L_SKILL'										=> $user->lang['uc_tab_skills'],
            
            'L_RESISTENCE'							=> $user->lang['uc_resitence'],
            'L_RESI_FIRE'								=> $user->lang['uc_res_fire'],
            'L_RESI_FROST'							=> $user->lang['uc_res_frost'],
            'L_RESI_ARCANE'							=> $user->lang['uc_res_arcane'],
            'L_RESI_NATURE'							=> $user->lang['uc_res_nature'],
            'L_RESI_SHADOW'							=> $user->lang['uc_res_shadow'],
            
            'L_VERSION'                 => $pm->get_data('charmanager', 'version'),
						
						// Profiler 
						'L_BUFFED'									=> $user->lang['uc_buffed'],
						'L_CTPROFILES'							=> $user->lang['uc_ctprofiles'],
						'L_ALLAKHAZAM'							=> $user->lang['uc_allakhazam'],
						'L_CURSE_PROFILER'					=> $user->lang['uc_curse_profiler'],
						'L_TALENTPLANER'						=> $user->lang['uc_talentplaner'],
						
						//tab shit
						'TAB_PANE_START'						=> $uctabs->startPane('uc_profilechar'),
						'TAB_PANE_END'							=> $uctabs->endPane(),
						'TAB_M1_START'							=> $uctabs->startTab($user->lang['uc_tab_Character'] , 'char1'),
						'TAB_MX_END'								=> $uctabs->endTab(),
						'TAB_M2_START'							=> $uctabs->startTab($user->lang['uc_tab_skills'] , 'char2'),
						'TAB_M3_START'							=> $uctabs->startTab($user->lang['uc_tab_profilers'] , 'char3'),
						
            // Javascript messages
            'MSG_NAME_EMPTY'            => $user->lang['fv_required_name'],
            'SHOW_CHECKBOX'             => ( $mode['edit'] == true ) ? false : true,
            'EDIT_BUTTONS'              => ( $mode['edit'] == true ) ? true : false,
            'WAS_UPDATE'                => ( $mode['update'] == true ) ? true : false,
            
            'UCV_MEMBER_ID'             => ( $mode['edit'] == true ) ? $mem_id_temp : '',
            'UCV_MEMBER_NAME'           => ( $mode['edit'] == true ) ? $member_data['member_name'] : '',
            'UCV_MEMBER_LEVEL'          => ( $mode['edit'] == true ) ? $member_data['member_level'] : '',
            'UCV_ICE'										=> ( $mode['edit'] == true ) ? $member_data['frr'] : '',
            'UCV_FIRE'									=> ( $mode['edit'] == true ) ? $member_data['fir'] : '',
            'UCV_ARCANE'								=> ( $mode['edit'] == true ) ? $member_data['ar'] : '',
            'UCV_NATURE'								=> ( $mode['edit'] == true ) ? $member_data['nr'] : '',
            'UCV_SHADOW'								=> ( $mode['edit'] == true ) ? $member_data['sr'] : '',
            'UCV_PICTURE'								=> ( $mode['edit'] == true ) ? $member_data['picture'] : '',
            'UCV_GENDER'								=> ( $mode['edit'] == true ) ? $member_data['gender'] : '',
            'UCV_GUILD'									=> ( $mode['edit'] == true ) ? $member_data['guild'] : '',
            
            'UCV_SKILL_1'								=> ( $mode['edit'] == true ) ? $member_data['skill_1'] : '',
            'UCV_SKILL_2'								=> ( $mode['edit'] == true ) ? $member_data['skill_2'] : '',
            'UCV_SKILL_3'								=> ( $mode['edit'] == true ) ? $member_data['skill_3'] : '',
            
            'UCV_SHOW_PIC'							=> ( $mode['edit'] == true ) ? true : false,
            'UCV_IS_PIC'								=> ( $member_data['picture'] != '' ) ? true : false,
            
            // Profiler Data
            'UCV_BUFFED'								=>  ( $mode['edit'] == true ) ?$member_data['blasc_id'] : '',
            'UCV_CTPROFILES'						=>  ( $mode['edit'] == true ) ?$member_data['ct_profile'] : '',
            'UCV_ALLAKHAZAM'						=>  ( $mode['edit'] == true ) ?$member_data['allakhazam'] : '',
            'UCV_CURSE_PROFILER'				=>  ( $mode['edit'] == true ) ?$member_data['curse_profiler'] : '',
            'UCV_TALENTPLANER'					=>  ( $mode['edit'] == true ) ?$member_data['talentplaner'] : '',
            )
        );

        $eqdkp->set_vars(array(
            'page_title'    => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['manage_members_title'],
            'template_file' => 'addchar.html',
            'template_path' => $pm->get_data('charmanager', 'template_path'),
            'display'       => true)
        );
?>