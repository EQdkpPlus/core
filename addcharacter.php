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
 * @copyright	2006-2010 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 * 
 * $Id$
 */

define('EQDKP_INC', true);
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');
$mode['edit'] = $mode['update'] = false;

// Permissions
if (!$user->check_auth('u_member_man', false) && !$user->check_auth('u_member_add', false)) {
	message_die($user->lang['uc_no_prmissions']);
}
if ($user->data['username']==""){
  message_die($user->lang['uc_not_loggedin']);
}

// File Upload..
$cmupload = new AjaxImageUpload;
if($in->get('performupload') == 'true'){
  $cmupload->PerformUpload('member_pic', 'charmanager', 'upload');
  die();
}

// save the data
if ($in->get('member_name') != ''){
  // Save the new Char...
  if($in->get('add') != ''){
    $info = $CharTools->updateChar('', sanitize($in->get('member_name')));
    $tpl->add_js("parent.window.location.href = 'characters.php';");
  }else{
    $info = $CharTools->updateChar($in->get('memberid',0));
    $tpl->add_js("parent.window.location.href = 'characters.php';");
  }
  
}

// Fill with data?
if ($in->get('editid', 0) > 0){
  $mode['edit']   = true;
  $MyMemberID     = $in->get('editid', 0);
}

// Read the Data
if($MyMemberID > 0){
  $member_data = $pdh->get('member', 'array', array($MyMemberID));
}

$customNoPic    = (is_file('games/'.$conf['real_gamename'].'/images/no_pic.png')) ? 'games/'.$conf['real_gamename'].'/images/no_pic.png' : 'images/no_pic.png';
$myCoolPicture  = ( $mode['edit'] == true && $member_data['picture'])  ? $pcache->FolderPath('upload','charmanager').$member_data['picture'] : $customNoPic;
$myCoolPicture2 = ( $mode['edit'] == true && $member_data['picture'])  ? $member_data['picture'] : '';
      
// Dynamic Fields
$jquery->Tab_header('addchar_tab');
foreach($cmapi->GetCategories() as $catname){
	if($catname != 'character'){
		$tpl->assign_block_vars('cmrow', array(
			'NAME'  => $game->glang('uc_cat_'.$catname),
			'ID'    => $catname
			)
		);
	}
    
	foreach($cmapi->GetDynFields() as $name=>$confvars){
		if($confvars['category'] == $catname){			
			$ccfield = $CharTools->generateField($confvars, $name, $member_data[$name]);
          
			if($ccfield && $confvars['visible']){
				$dynwhereto = ($confvars['category'] == 'character') ? 'character_row' : 'cmrow.tabs';
				$tpl->assign_block_vars($dynwhereto, array(
					'NAME'      => $confvars['language'],
					'FIELD'     => $ccfield,
           )
				);
			}
		}
	}
}

$tpl->assign_vars(array(
            'TEMPLATENAME'              => $user->style['template_path'],
            'F_ADD_MEMBER'              => 'addcharacter.php' . $SID,
            'U_IS_EDIT'							    => ($mode['edit'] == true) ? true : false,
            'U_ISNOT_EDIT'              => ($mode['edit'] == true) ? false : true,
						
						'DRPWN_CLASS' 						  => $html->DropDown('member_class_id', $game->get('classes'), $member_data['class_id'], '', '', 'input'),
						'DRPWN_RACE' 						    => $html->DropDown('member_race_id', $game->get('races'), $member_data['race_id'], '', '', 'input'),
						
            // Language
            'L_ADD_MEMBER_TITLE'        => $user->lang['uc_add_member'],
            'L_EDIT_MEMBER_TITLE'       => $user->lang['uc_edit_member'],
            'L_INFO_BOX'                => $user->lang['uc_info_box'],
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
            'L_CANCEL'                  => $user->lang['cancel'],
            'L_GUILD'										=> $user->lang['uc_guild'],
            'L_NOTES'                   => $user->lang['uc_notes'],
						
            // Javascript messages
            'MSG_NAME_EMPTY'            => $user->lang['fv_required_name'],
            'USER_CAN_CONNECT'					=> ($user->check_auth('u_member_conn', false)) ? true : false,
            
            // Picture upload
            'UCV_PICTURE'               => $cmupload->Show('member_pic', 'addcharacter.php?performupload=true', $myCoolPicture, false),
            'UCV_PICTURE_NAME'          => $myCoolPicture2,
            
            'UCV_IS_WOW'								=> ($conf['real_gamename'] == 'wow') ? true : false,
            'UCV_MEMBER_ID'             => $CharTools->ValueorNull($MyMemberID),
            'UCV_MEMBER_NAME'           => $CharTools->ValueorNull($member_data['name']),
            'UCV_MEMBER_LEVEL'          => $CharTools->ValueorNull($member_data['level']),
            'UCV_GENDER'								=> $CharTools->ValueorNull($member_data['gender']),
            'UCV_GUILD'									=> $CharTools->ValueorNull($member_data['guild']),
            'UCV_NOTES'                 => $CharTools->ValueorNull(stripslashes($member_data['notes'])),
            
            'TAB_CHARS'									=> $user->lang['uc_tab_Character'],
            'TAB_NOTES'									=> $user->lang['uc_tab_notes'],
  )
);

  $core->set_vars(array(
    'page_title'        => $user->lang['manage_members_title'],
    'template_file'     => 'addcharacter.html',
    'header_format'			=> 'simple',
    'display'           => true)
  );
?>
