<?php
 /*
 * Project:     EQdkp TwinkIt (v0.7 eqdkp plus sandbox test)
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		    http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2007
 * Date:        $Date: 2009-02-26 16:28:10 +0100 (Do, 26 Feb 2009) $
 * -----------------------------------------------------------------------
 * @author      $Author: sz3 $
 * @copyright   2007-2008 sz3
 * @link        http://eqdkp-plus.com
 * @package     twinkit
 * @version     $Rev: 4008 $
 * 
 * $Id: manage_alts.php 4008 2009-02-26 15:28:10Z sz3 $
 */

// EQdkp required files/vars
define('EQDKP_INC', true);
define('IN_ADMIN', true);

$eqdkp_root_path = './../';
include_once ($eqdkp_root_path . 'common.php');

// Check user permission
$user->check_auth('a_members_man');

$members = $pdh->get('member', 'id_list', array());
$valid = true;

// Saving
if ($_POST['twink_save_button']){
  
  //create dummy array to check if there are invalid mains set
  $dummy_array = array();  

  foreach($members as $memberid){
    $dummy_array[$memberid] = intval($_POST[$memberid.'_mainid']);
  }

  foreach($dummy_array as $memberid => $mainid){
    if($dummy_array[$mainid] != $mainid){
      $name = $pdh->get('member', 'name',array($memberid));
      System_Message(sprintf($user->lang['alt_main_is_alt'], $name), $user->lang['alt_message_title'], "red");
      $valid = false;
    }
  }
  if($valid){
    //first update mains
    foreach($members as $memberid){
      if($dummy_array[$memberid] == $memberid){
        $pdh->put('member', 'update_member', array($memberid, '', '', '', '', '', intval($_POST[$memberid.'_mainid'])));
      }
    }
    //then update alts
    foreach($members as $memberid){
      if($dummy_array[$memberid] != $memberid){
        $pdh->put('member', 'update_member', array($memberid, '', '', '', '', '', intval($_POST[$memberid.'_mainid'])));
      }
    }
    $pdh->process_hook_queue();  
    System_Message($user->lang['alt_update_successful'], $user->lang['alt_message_title'], "green");
  }else{
    System_Message($user->lang['alt_update_unsuccessful'], $user->lang['alt_message_title'], "red");
  } 
}

$memberout = '<tr><th>Member</th><th>Main</th></tr>';
foreach($members as $memberid){
  $memberout .= '<tr class="row1"><td>'.$pdh->get('member', 'name',array($memberid)).'</td>';
  $memberout .= '<td><select name="'.$memberid.'_mainid" class="input">';
  foreach($members as $inner_memberid){
    $memberout .= '<option value="'.$inner_memberid.'"';
    $c_main_id = ($valid)?$pdh->get('member', 'mainid', array($memberid)) : intval($_POST[$memberid.'_mainid']);  
    if ( $c_main_id == $inner_memberid ){
      $memberout .= ' selected="selected">';
    }else{
      $memberout .= '>';
    }
    $memberout .= $pdh->get('member', 'name',array($inner_memberid)).'</option>';
  }
  $memberout .= '</select></td></tr>';
}

$tpl->assign_vars(array (
	'F_CONFIG' => 'settings.php' . $SID,
  'MEMBEROUT' => $memberout,
));

$eqdkp->set_vars(array(
    'page_title' => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['pdc_manager'],
    'template_file' => 'admin/manage_alts.html',
    'display' => true
  )
);
?>
