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
$user->check_auth('u_userlist');

$mode = ( isset($_GET['mode']) ) ? $in->get('mode') : false;
$mode = ($in->get('u') != "") ? 'view' : $mode;
include_once($eqdkp_root_path.'core/country_states.php');

switch ( $mode )
{
    case 'send':
        $action = 'send';
        break;
				
		case 'view':
				$action = 'view';
				break;
				
    default:
        $action = 'display';
        break;
}


switch ( $action )
{
   
	 case 'view':
	$username = '';
	$is_user = ($user->data['user_id']>0) ? true : false ;
	$is_admin = $user->check_auth('a',false);
	$sql = "SELECT * from __users WHERE user_id='".$db->escape($in->get('u'))."'";
	if ($result = $db->query($sql))
	{
		while ($row = $db->fetch_record($result) )
		{
			$privacy = unserialize($row['privacy_settings']) ;
			$custom = unserialize($row['custom_fields']) ;
			
			
			$username = sanitize($row['username']);
			$data = array();
			$data['first_name'] = $row['first_name'];
			$data['last_name'] = $row['last_name'];
			$data['user_names'] = (($row['first_name'] <> '') || ($row['last_name'] <> '')) ? " - " .$row['first_name'] . ' ' . $row['last_name']  : '' ;
			$data['town'] = $row['town'];
			$data['state'] = $row['state'] ;
			//Contact
			$data['user_email'] = ($row['user_email'] <> '') ? '<a href=mailto:'.sanitize($row['user_email']).'><img title="'.$row['user_email'].'" src='.$eqdkp_root_path.'images/glyphs/email.png /> '.sanitize($row['user_email']).'</a>' : '' ;
	    $data['icq'] = ($row['icq'] <> '') ? '<a href="http://www.icq.com/people/'.$row['icq'].'" target=_blank><img title="'.$row['icq'].'" src="http://status.icq.com/online.gif?icq='.$row['icq'].'&img=5"/> '.sanitize($row['icq']).'</a>' : '' ;
	    $data['skype'] = ($row['skype'] <> '') ? '<a href="skype:'.$row['skype'].'?add"> <img title="'.$row['skype'].'" src='.$eqdkp_root_path.'images/glyphs/skype.png> '.sanitize($row['skype']).'</a>' : '' ;
	    $data['msn'] = ($row['msn'] <> '') ? '<a href="http://members.msn.com/?mem='.$row['msn'].'" target=_blank> <img title="'.$row['msn'].'" src='.$eqdkp_root_path.'images/glyphs/msn.png> '.sanitize($row['msn']).'</a> ' : '' ;
			if ($row['irq'] != ""){
				$irc_parts = explode('@',$row['irq']);
				$data['irq'] = '<a href="irc://'.(($irc_parts[1]) ? $irc_parts[1] : 'irc.de.quakenet.org').'/'.str_replace('#', '', $irc_parts[0]).'" > <img title="'.$row['irq'].'" src='.$eqdkp_root_path.'images/glyphs/irc.png> '.$row['irq'].'</a>';
			}
			
			$data['twitter'] = ($custom['twitter'] <> '') ? '<a href="http://twitter.com/'.$custom['twitter'].'" target="_blank"><img src="'.$eqdkp_root_path.'images/twitter_icon_16.png"> '.sanitize($custom['twitter']).'</a>' : '' ;
			
			$data['facebook'] = ($custom['facebook'] <> '') ? '<a href="http://facebook.com/'.((is_numeric($custom['facebook'])) ? 'profile.php?id='.$custom['facebook'] : $custom['facebook']).'" target="_blank"><img src="'.$eqdkp_root_path.'images/facebook_icon_16.png"> '.sanitize($custom['facebook']).'</a>' : '' ;
		
	    //Phone
			$data['phone'] = ($row['phone'] <> '') ? ' <img title="Phone '.$row['phone'].'" src='.$eqdkp_root_path.'images/glyphs/phone.png>'.$row['phone'] : '' ;
	    $data['cellphone'] = ($row['cellphone'] <> '') ? ' <img title="Cell Phone '.$row['cellphone'].'" src='.$eqdkp_root_path.'images/glyphs/phone_cell.png>'.$row['cellphone'] : '' ;
	    
			$data['birthday'] = ($privacy['priv_bday'] == 1) ? $row['birthday'].' ('.age($row['birthday']).')': age($row['birthday']);
			$data['user_avatar'] = ($custom['user_avatar'] != "") ? $pcache->FilePath('user_avatars/'.$custom['user_avatar'], 'eqdkp') : $eqdkp_root_path.'images/no_pic.png';

	    //check the pricacy permissions. if we dont have the permission unset() the data array
			switch ($privacy['priv_set'])
			{
				case 0: // all
				    // do nothing... everything fine
				    break;
				case 1: // only user
				    if (!$is_user) { unset($data['user_email']); unset($data['icq']); unset($data['skype']); unset($data['msn']); unset($data['irq']);unset($data['twitter']); unset($data['facebook']);}
				    break;
				case 2: // only admins
				    if (!$is_admin) { unset($data['user_email']); unset($data['icq']); unset($data['skype']); unset($data['msn']); unset($data['irq']);unset($data['twitter']); unset($data['facebook']);}
				    break;
			}
			
			//check the pricacy permissions. if we dont have the permission unset() the data array
			switch ($privacy['priv_phone'])
			{
				case 0: // all
				    // do nothing... everything fine
				    break;
				case 1: // only user
				    if (!$is_user) { unset($data['phone']); unset($data['cellphone']);}
				    break;
				case 2: // only admins
				    if (!$is_admin) { unset($data['phone']); unset($data['cellphone']);}
				    break;
				case 3: // nobody
				    unset($data['phone']); unset($data['cellphone']);
				    break;
			}
				
			$member_list = $pdh->get('member_connection', 'connection', array($in->get('u')));
			if (is_array($member_list)){
				foreach ($member_list as $member){
					$mid = $member['member_id'];
					$rank = 

					$member_array = array(
						'ROW_CLASS'	=> $core->switch_row_class(),
						'NAME'	=> '<a href="'.$eqdkp_root_path.'viewcharacter.php?member_id='.$mid.'">'.sanitize($member['member_name'])."</a>",
						'LEVEL'	=> sanitize($member['member_level']),
						'CLASS' => $game->decorate('classes', array($member['member_class_id'])).' '.$game->get_name('classes', $member['member_class_id']),
						'RACE' => $game->decorate('races', array($member['member_race_id'])).' '.$game->get_name('races', $member['member_race_id']),
						'RANK'	=> sanitize($pdh->get('member', 'rankname', array($mid))),
						'TYPE'	=> sanitize($pdh->get('member', 'twink', array($mid))),
					
					);
															 
					$tpl->assign_block_vars('char_row', $member_array );
				}
			}
			
				$comm_settings = array(
					'attach_id' => $in->get('u'), 
					'page'      => 'userview',
					'auth'      => 'a_users_comment_w',
					'userauth'	=> 'a_users_comment_w',
				);	  
				$pcomments->SetVars($comm_settings);
				
				if ($user->check_auth('u_users_comment_r', false) || $user->check_auth('a_users_comment_w', false)){
					$tpl->assign_vars(array(
						'COMMENTS'            => $pcomments->Show(),
						'ENABLE_COMMENTS'			=> true,
					));
				}
			
			$users_array = array(
						'S_VIEW'				=> true,							 
						'USER_NAME'    		=> sanitize($row['username']),
	          'USER_COUNTRY' 		=> ucfirst(strtolower($country_array[$row['country']])),
	          'USER_FLAG' 			=> ($row['country'] <> '') ? '<img src='.$eqdkp_root_path.'images/flags/'.strtolower($row['country']).'.png>' : '' ,
	          'USER_FIRST_NAME' => sanitize($data['first_name']),
	          'USER_LAST_NAME' 	=> ($is_user) ? sanitize($data['last_name']): '',
	          'USER_NAMES' 			=> sanitize($data['user_names']),
	          'USER_TOWN' 			=> sanitize($data['town']),
	          'USER_STATE' 			=> sanitize($data['state']),
	          'USER_MAIL' 			=> $data['user_email'],
	          'USER_ICQ' 				=> $data['icq'],
	          'USER_SKYPE' 			=> $data['skype'],
	          'USER_MSN' 				=> $data['msn'],
	          'USER_IRC' 				=> $data['irq'],
	          'USER_PHONE' 			=> $data['phone'],
	          'USER_CELLPHONE' 	=> $data['cellphone'],
	          'USER_BIRTHDAY'		=> sanitize( $data['birthday']),
	          'USER_CHECKBOX'		=> sanitize( $data['user_checkbox']),
						'USER_REGISTERED'	=> date($user->style['date_time'], $row['user_registered']),
						'USER_CHAR_FC'		=> sprintf($user->lang['listmembers_footcount'], count($member_list)),
						'USER_IMAGE'			=> sanitize($data['user_avatar']),
						'USER_HARDWARE'		=> sanitize($custom['hardware']),
						'USER_WORK'				=> sanitize($custom['work']),
						'USER_INTERESTS'	=> sanitize($custom['interests']),
						'USER_TWITTER' 		=> $data['twitter'],
						'USER_FACEBOOK' 	=> $data['facebook'],
						'ADMIN_EDIT'			=> ($user->check_auth('a_users_man', false)) ? '<a href="'.$eqdkp_root_path.'admin/manage_users.php'.$SID.'&u='.sanitize($row['user_id']).'" title="'.$user->lang['manage_users'].'"><img src="'.$eqdkp_root_path.'images/global/edit.png"></a>' : '',
												
						'L_CHARS'					=> $user->lang['chars'],
						'L_IMAGE'					=> $user->lang['user_image'],
						'L_PROFILE'				=> $user->lang['adduser_addinfos'],
						'L_CONTACT'				=> $user->lang['user_contact'],
						'L_NAME'					=> $user->lang['name'],
						'L_AGE'						=> $user->lang['age'],
						'L_TOWN'					=> $user->lang['adduser_town'],
						'L_COUNTRY'				=> $user->lang['adduser_country'],
						'L_REGISTERED'		=> $user->lang['registered_at'],
						'L_EMAIL'					=> $user->lang['email'],
						'L_PHONE'					=> $user->lang['adduser_phone'],
						'L_CELLPHONE'			=> $user->lang['adduser_cellphone'],
						'L_WORK'					=> $user->lang['user_work'],
						'L_HARDWARE'			=> $user->lang['user_hardware'],
						'L_INTERESTS'			=> $user->lang['user_interests'],
						'L_USER'					=> $user->lang['user'],
						'L_USERLIST'			=> $user->lang['user_list'],
						'L_COMMENTS'			=> $user->lang['user_comments'],
						
						'L_LEVEL'			=> $user->lang['level'],
						'L_CLASS'			=> $user->lang['class'],
						'L_RACE'			=> $user->lang['race'],
						'L_RANK'			=> $user->lang['rank'],
						'L_TYPE'			=> $user->lang['type'],
						
				);
		   	$tpl->assign_vars($users_array );
		}}
	 
	 break;
	 
	 
	case 'display':

	$is_user = ($user->data['user_id']>0) ? true : false ;
	$is_admin = $user->check_auth('a',false);
	$username = '';
	
	$usergroups = $pdh->get('user_groups', 'id_list', array(true));
	
	$special_user = unserialize(stripslashes($core->config['special_user']));
	
	unset($usergroups[0]); //Guestgroup
	
	foreach ($usergroups as $group){
		$user_in_group = $pdh->get('user_groups_users', 'user_list', array($group));

		if (is_array($user_in_group) && count($user_in_group) > 0){

			$tpl->assign_block_vars('group_row', array(
				'NAME'	=> $pdh->get('user_groups', 'name', array($group)),
				'ID'		=> $group,
				'FC'		=> sprintf($user->lang['user_group_footcount'], count($user_in_group)),
			));
		if (is_array($user_in_group)){
		foreach ($user_in_group as $usr){
			
			if (!in_array($usr, $special_user)){
			
			$row = $pdh->get('user', 'data', array($usr));
			$privacy = unserialize($row['privacy_settings']) ;
			$custom = unserialize($row['custom_fields']) ;

			$data = array();
			
			$data['first_name'] = $row['first_name'];
			$data['last_name'] = $row['last_name'];
			$data['user_names'] = (($row['first_name'] <> '') || ($row['last_name'] <> '')) ? $row['first_name'] . ' ' . $row['last_name']  : '' ;
			//Contact
			$data['user_email'] = ($row['user_email'] <> '') ? '<a href=mailto:'.sanitize($row['user_email']).'>'.$html->Tooltip($user->lang['email'].': '.$row['user_email'], '<img src='.$eqdkp_root_path.'images/glyphs/email.png />').'</a>' : '' ;
			
			$data['icq'] = ($row['icq'] <> '') ? '<a href="http://www.icq.com/people/'.$row['icq'].'" target=_blank>
			'.$html->Tooltip('ICQ: '.$row['icq'], '<img src="http://status.icq.com/online.gif?icq='.$row['icq'].'&img=5"/>').'</a>' : '' ;
	    $data['skype'] = ($row['skype'] <> '') ? '<a href="skype:'.$row['skype'].'?add">'.$html->Tooltip('Skype: '.$row['skype'], '<img src='.$eqdkp_root_path.'images/glyphs/skype.png>').'</a>' : '' ;
			
	    $data['msn'] = ($row['msn'] <> '') ? '<a href="http://members.msn.com/?mem='.$row['msn'].'" target=_blank>'.$html->Tooltip('MSN: '.$row['msn'], '<img src='.$eqdkp_root_path.'images/glyphs/msn.png>').'</a> ' : '' ;
			if ($row['irq'] != ""){
				$irc_parts = explode('@',$row['irq']);
				$data['irq'] = '<a href="irc://'.(($irc_parts[1]) ? $irc_parts[1] : 'irc.de.quakenet.org').'/'.str_replace('#', '', $irc_parts[0]).'" > '.$html->Tooltip('IRC: '.$row['irq'], '<img src='.$eqdkp_root_path.'images/glyphs/irc.png>').'</a>';
			}
	   			
			$data['twitter'] = ($custom['twitter'] <> '') ? '<a href="http://twitter.com/'.$custom['twitter'].'" target="_blank">'.$html->Tooltip('Twitter: '.$custom['twitter'], '<img src="'.$eqdkp_root_path.'images/twitter_icon_16.png">').'</a>' : '' ;
			
			$data['facebook'] = ($custom['facebook'] <> '') ? '<a href="http://facebook.com/'.((is_numeric($custom['facebook'])) ? 'profile.php?id='.$custom['facebook'] : $custom['facebook']).'" target="_blank">'.$html->Tooltip('Facebook: '.$custom['facebook'], '<img src="'.$eqdkp_root_path.'images/facebook_icon_16.png">').'</a>' : '' ;
	
	    //Phone
	    $data['cellphone'] = ($row['cellphone'] <> '') ? $html->Tooltip($user->lang['adduser_cellphone'].': '.$row['cellphone'], '<img src='.$eqdkp_root_path.'images/glyphs/phone_cell.png>') : '' ;
	    
	    //check the pricacy permissions. if we dont have the permission unset() the data array
			switch ($privacy['priv_set'])
			{
				case 0: // all
				    // do nothing... everything fine
				    break;
				case 1: // only user
				    if (!$is_user) { unset($data['user_email']); unset($data['icq']); unset($data['skype']); unset($data['msn']); unset($data['irq']);unset($data['facebook']);unset($data['twitter']);}
				    break;
				case 2: // only admins
				    if (!$is_admin) { unset($data['user_email']); unset($data['icq']); unset($data['skype']); unset($data['msn']); unset($data['irq']);unset($data['facebook']);unset($data['twitter']);}
				break;
			}
			
			//check the pricacy permissions. if we dont have the permission unset() the data array
			switch ($privacy['priv_phone'])
			{
				case 0: // all
				    // do nothing... everything fine
				    break;
				case 1: // only user
				    if (!$is_user) { unset($data['cellphone']);}
				    break;
				case 2: // only admins
				    if (!$is_admin) {unset($data['cellphone']);}
				    break;
				case 3: // nobody
				   unset($data['cellphone']);
				    break;
			}

			//Dont send SMS
			if (strlen($row['cellphone']) > 1
				 && $privacy['priv_nosms'] <> 1
				 && $core->config['pk_sms_disable'] <> 1 && $user->check_auth('a_sms_send', false))
			{
				$data['user_checkbox'] = '<input type="checkbox" name="sendto['.$row['user_id'].']" value="'.base64_encode($row['cellphone'].';'.$row['username']).'" />';
				$something_to_do = true;
			}

			$users_array = array(
						'USER_ID'					=> sanitize($row['user_id']),							 
	          'USER_NAME'    		=> sanitize($row['username']),
	          'USER_COUNTRY' 		=> sanitize($row['country']),
	          'USER_FLAG' 			=> ($row['country'] <> '') ? '<img src='.$eqdkp_root_path.'images/flags/'.strtolower($row['country']).'.png title="'.ucfirst(strtolower($country_array[$row['country']])).'">' : '' ,
	          'USER_FIRST_NAME' => sanitize($data['first_name']),
	          'USER_LAST_NAME' 	=> ($is_user) ? sanitize($data['last_name']): '',
	          'USER_NAMES' 			=> sanitize($data['user_names']),
	          'USER_STATE' 			=> sanitize($data['state']),
	          'USER_MAIL' 			=> $data['user_email'],
						'USER_ICQ' 				=> $data['icq'],
	          'USER_SKYPE' 			=> $data['skype'],
	          'USER_MSN' 				=> $data['msn'],
	          'USER_IRC' 				=> $data['irq'],
						'USER_TWITTER' 		=> $data['twitter'],
						'USER_FACEBOOK' 	=> $data['facebook'],
	          'USER_CELLPHONE' 	=> $data['cellphone'],
	          'USER_CHECKBOX'		=> $data['user_checkbox'],
						'USER_REGISTERED'		=> ($row['user_registered'] > 0) ? date($user->style['date_time'], $row['user_registered']) : date($user->style['date_time'], $core->config['eqdkp_start']),
						'USER_LINK_TITLE'	=> sprintf($user->lang['user_more_information'], sanitize($row['username'])),
						'USER_TOWN'				=> sanitize($row['town']),
						'ROW_CLASS'				=> $core->switch_row_class(),
				);
		   	$tpl->assign_block_vars('group_row.row_users', $users_array );
			
		} //if not special user
		} //foreach user
		} //is is_array
		}
	} //foreach user-group
	
		
		$tpl->assign_vars(array(
			'L_USERNAME' 	=> $user->lang['username'],
			'L_EMAIL' 	=> $user->lang['email'],
			'L_NAME' 	=> $user->lang['name'],
			'L_REGISTERED' 	=> $user->lang['registered_at'],
			'L_TOWN' 	=> $user->lang['adduser_town'],
			));
		
		
		if (strlen(($core->config['pk_sms_username'])) < 1 || strlen(($core->config['pk_sms_password'])))
		{
			$sms_info = $user->lang['sms_info_account']." ".$user->lang['sms_info_account_link'] ;
			if ($_HMODE) {$sms_info = $user->lang['sms_info_account']." ".$_HMODE_LINK;}
		}

		if (!$core->config['pk_sms_disable']==1 && $user->check_auth('a_sms_send', false))
		{
			$tpl->assign_vars(array(
			'F_SMS'				=>  true,
			'F_SMS_INFO' 	=> $user->lang['sms_info'],
			'F_SMS_HEADER' => $user->lang['sms_header'],
			'F_SEND_INFO' => $user->lang['sms_send_info'],
			'F_ACC_INFO' => $sms_info,
			'F_TEXTAREA' 	=> '<textarea name="text_area"rows="5" onkeyup="count_chars()" style="width:550px;"></textarea>',
			'F_LISTUSERS' 	=> 'listusers.php'.$SID.'&amp;mode=send',
			'F_SUBMIT' 		=> '<div style="width:550px;"><span style="float:left;"><input type="submit" name="submit" value="'.$user->lang['submit'].'" class="mainoption bi_cellphone" /></span><span style="float:right;"><span id="counter">0</span> '.$user->lang['sms_chars'].'</div>',
			
			));
		}

		
    break;

    //
    // Send Data
    //
    case 'send':

    	if ( isset($_POST['submit']) )
		{
			$user->check_auth('a_sms_send');

			$sms = new sms();
			$sms->username = $core->config['pk_sms_username'];
			$sms->passwort = $core->config['pk_sms_password'];

    		$_POST = htmlspecialchars_array($_POST);
    		$sendto_string = (isset($_POST['sendto']))? $_POST['sendto'] : '';
   			$message = (isset($_POST['text_area']))? $_POST['text_area'] : '';
				
    		if(($sendto_string <> '') && $message <> '' )
    		{
    			foreach($sendto_string as $elem){
						$send_to_string[] = base64_decode($elem);
					}
					
					$send_to_string = implode("\n", $send_to_string);
    			$return = $sms->sendSMS($send_to_string,$message,$user->data['username']);
    		}

    		$notice_img[0] = "<img src=".$eqdkp_root_path."/images/ok.png>";
    		$notice_img[1] = "<img src=".$eqdkp_root_path."/images/false.png>";

    		switch($return['status'])
    		{
    			case '-1' : $notice = $return['msg']; $rs = 0; break ;
    			case '-2' : $notice = $user->lang['sms_error_fopen']." ".$return['msg']; $rs = 1; break;
    			case '100' : $notice = $user->lang['sms_success'];	$rs = 0;  break;
    			case '150' : $notice = $user->lang['sms_error_badpw']; $rs = 1;	break;
    			case '159' : $notice = $user->lang['sms_error_159']; $rs = 1;	break;
    			case '160' : $notice = $user->lang['sms_error_160']; $rs = 1;	break;
    			case '200' : $notice = $user->lang['sms_error_200']; $rs = 1;	break;
    			case '254' : $notice = $user->lang['sms_error_254']; $rs = 1; break;
    			default: $notice = $user->lang['sms_error']; $rs = 1; break;
    		}

    		$tpl->assign_vars(array(
			'F_NOTICE' 		=> $notice,
			'F_NOTICE_IMG' 	=> $notice_img[$rs],
			'SENDTO' 		=> 'TRUE',
			));

		}
    	break;

}

$core->set_vars(array(
    'page_title'    => ($mode == 'view') ? $user->lang['user'].': '.$username : $user->lang['user_list'],
    'template_file' => 'listusers.html',
    'display'       => true)
);

  function age($age) {
		$age = time() - strtotime($age);
		$age = date("Y", $age) - date("Y", strtotime("1970-01-01"));    
		return $age;
	}

?>