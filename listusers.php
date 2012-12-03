<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2006
 * Date:        $Date: 2008-12-02 11:54:02 +0100 (Di, 02 Dez 2008) $
 * -----------------------------------------------------------------------
 * @author      $Author: corgan $
 * @copyright   2006-2008 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev: 3293 $
 * 
 * $Id: roster.php  $
 */

define('EQDKP_INC', true);
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');
$user->check_auth('u_member_list');

$mode = ( isset($_GET['mode']) ) ? $in->get('mode') : false;
switch ( $mode )
{
    case 'send':
        $action = 'send';
        break;
    default:
        $action = 'display';
        break;
}


switch ( $action )
{    
    case 'display':
    	
	$is_user = ($user->data['user_id']>0) ? true : false ;
	$is_admin = $user->check_auth('a',false);
	$sql = 'SELECT * from __users';
	if ($result = $db->query($sql)) 
	{
		while ($row = $db->fetch_record($result) )
		{					
			$data = array();						
			$data['first_name'] = $row['first_name'];
			$data['last_name'] = $row['last_name'];
			$data['user_names'] = (($row['first_name'] <> '') || ($row['last_name'] <> '')) ? " - " .$row['first_name'] . ' ' . $row['last_name']  : '' ;
			$data['town'] = $row['town'];
			$data['state'] = $row['state'] ;
			$data['user_email'] = ($row['user_email'] <> '') ? '<a href=mailto:'.$row['user_email'].'><img title="'.$row['user_email'].'" src='.$eqdkp_root_path.'images/glyphs/email.png /> </a>' : '' ;
	        $data['icq'] = ($row['icq'] <> '') ? '<a href=http://wwp.icq.com/scripts/search.dll?to='.$row['icq'].' target=_blank><img title="'.$row['icq'].'" src="http://status.icq.com/online.gif?icq='.$row['icq'].'&img=5"/> </a>' : '' ;        
	        $data['skype'] = ($row['skype'] <> '') ? '<a href="skype:'.$row['skype'].'?add"> <img title="'.$row['skype'].'" src='.$eqdkp_root_path.'images/glyphs/skype.png> </a>' : '' ;
	        $data['msn'] = ($row['msn'] <> '') ? '<a href="http://members.msn.com/?mem='.$row['msn'].'" target=_blank> <img title="'.$row['msn'].'" src='.$eqdkp_root_path.'images/glyphs/msn.png></a> ' : '' ;
	        $data['irq'] = ($row['irq'] <> '') ? '<a href="irc://irc.de.quakenet.org/'.$row['irq'].'" > <img title="'.$row['irq'].'" src='.$eqdkp_root_path.'images/glyphs/irc.png> </a>' : '' ;
	        $data['phone'] = ($row['phone'] <> '') ? ' <img title="Phone '.$row['phone'].'" src='.$eqdkp_root_path.'images/glyphs/phone.png>'.$row['phone'] : '' ;
	        $data['cellphone'] = ($row['cellphone'] <> '') ? ' <img title="Cell Phone '.$row['cellphone'].'" src='.$eqdkp_root_path.'images/glyphs/phone_cell.png>'.$row['cellphone'] : '' ;
	        $data['birthday'] = $row['birthday'];
	            	
	    	//check the pricacy permissions. if we dont have the permission unset() the data array
	    	$privacy = unserialize($row['privacy_settings']) ;		    	
			switch ($privacy['priv_set']) 
			{
				case 0: // all
				    // do nothing... everything fine
				    break;
				case 1: // only user 
				    if (!$is_user) { unset($data);}
				    break;
				case 2: // only admins
				    if (!$is_admin) { unset($data);}
				    break;
				case 3: // nobody
				    unset($data);
				    break;
			}
			
			if ($is_admin)
			{
				// Cript Phonenumber even for Admins
				if ($privacy['priv_tel_cript']==1) 
				{
	        		$data['phone'] = ($row['phone'] <> '') ? ' <img title="Phone" src='.$eqdkp_root_path.'images/glyphs/phone.png> ******' : '' ;
	        		$data['cellphone'] = ($row['cellphone'] <> '') ? ' <img title="Cell Phone" src='.$eqdkp_root_path.'images/glyphs/phone_cell.png> *******' : '' ;			
				}			
			}else 
			{
				//Dont show phonnumber if the user dont want it
				if (!$privacy['priv_tel_all'] == 1 || !$is_user ) 
				{
					$data['phone'] = '';
					$data['cellphone'] = '';
				}
			}
			
			//Dont send SMS
			if (strlen($data['cellphone']) > 1 
				 && $privacy['priv_nosms'] <> 1
				 && $conf_plus['pk_sms_disable'] <> 1) 
			{		
				$data['user_checkbox'] = '<input type="checkbox" name="sendto[]" value="'.$row['cellphone'].';'.$row['username'].'" >';					
				$something_to_do = true;			
			}
							
			$users_array = array(
	          'USER_NAME'    		=> $row['username'],
	          'USER_COUNTRY' 		=> $row['country'],
	          'USER_FLAG' 			=> ($row['country'] <> '') ? '<img src='.$eqdkp_root_path.'images/flags/'.strtolower($row['country']).'.png>' : '' ,	          
	          'USER_FIRST_NAME' 	=> ($is_user) ? $data['first_name']: '',
	          'USER_LAST_NAME' 		=> ($is_user) ? $data['last_name']: '',
	          'USER_NAMES' 			=> $data['user_names'],
	          'USER_TOWN' 			=> $data['town'],
	          'USER_STATE' 			=> $data['state'],
	          'USER_MAIL' 			=> ($is_user) ? $data['user_email'] : '',
	          'USER_ICQ' 			=> ($is_user) ? $data['icq']: '',
	          'USER_SKYPE' 			=> ($is_user) ? $data['skype']: '',
	          'USER_MSN' 			=> ($is_user) ? $data['msn']: '',
	          'USER_IRC' 			=> ($is_user) ? $data['irq']: '',
	          'USER_PHONE' 			=> ($is_user) ? $data['phone']: '',
	          'USER_CELLPHONE' 		=> ($is_user) ? $data['cellphone']: '',
	          'USER_BIRTHDAY'		=> $data['birthday'],
	          'USER_BIRTHDAY'		=> $data['birthday'],
	          'USER_CHECKBOX'		=> $data['user_checkbox'],	          	        
				);
		   	$tpl->assign_block_vars('row_users', $users_array );												
		}
		
		if (strlen(($conf_plus['pk_sms_username'])) < 1 || strlen(($conf_plus['pk_sms_password']))) 
		{			
			$user->lang['sms_header'] = $user->lang['sms_info_account']." ".$user->lang['sms_info_account_link'] ;
			if ($_HMODE) {$user->lang['sms_header'] = $user->lang['sms_info_account']." ".$_HMODE_LINK;}															
		}		
		
		if (!$conf_plus['pk_sms_disable']==1) 
		{
			$tpl->assign_vars(array(
			'F_SMS_INFO' 	=> $user->lang['sms_info'],
			'F_SMS_HEADER' => $user->lang['sms_header'],
			'F_SEND_INFO' => $user->lang['sms_send_info'],
			'F_ACC_INFO' => $acc_info,
			'F_TEXTAREA' 	=> '<textarea name="text_area" cols="100" rows="5"></textarea>',
			));			
		}
		
		
		if($something_to_do) 
		{
			$tpl->assign_vars(array(
			'F_LISTUSERS' 	=> 'listusers.php'.$SID.'&amp;mode=send',
			'F_SUBMIT' 		=> '<input type="submit" name="submit" value="'.$user->lang['submit'].'" class="mainoption" />',
			));
			
		}
	}
    break;
    
    //
    // Send Data
    //
    case 'send':
    	    	
    	if ( isset($_POST['submit']) )
		{
			$user->check_auth('a');
			
			// TEMPORARY CODE - REMOVE IN 0.7!
			include_once($eqdkp_root_path . 'libraries/sms/sms.class.php');
			// END OF TEMPORARY CODE
			
			$sms = new sms();
			$sms->username = $conf_plus['pk_sms_username'];
			$sms->passwort = $conf_plus['pk_sms_password'];
			
    		$_POST = htmlspecialchars_array($_POST);
    		$sendto_string = (isset($_POST['sendto']))? $_POST['sendto'] : '';
   			$message = (isset($_POST['text_area']))? $_POST['text_area'] : '';
   			
    		if(($sendto_string <> '') && $message <> '' ) 
    		{
    			$sendto_string = implode("\n", $sendto_string);    			  
    			$return = $sms->sendSMS($sendto_string,$message,$user->data['username']);    			
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

$eqdkp->set_vars(array(
    'page_title'    => sprintf($user->lang['title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['user_list'],    
    'template_file' => 'listusers.html',
    'display'       => true)
);

?>