<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2002
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

// If there's an external login without showing template stuff and so on...
if($in->get('external', '') == 'yes')
{
  $auto_login = ( $in->exists('auto_login') ) ? true : false;
  if($user->login($in->get('username'), $in->get('password'), $auto_login))
  {
    die('OK');
  }
  else
  {
    die('Error');
  }
}

// Normal Output
if ( $in->exists('login') || $in->exists('logout') )
{
    if ( $in->exists('login') && ($user->data['user_id'] <= 0) )
    {
       $redirect = ( $in->exists('redirect') ) ? $in->get('redirect') : 'index.php';

       $auto_login = ( $in->exists('auto_login') ) ? true : false;

        if ( !$user->login($in->get('username'), $in->get('password'), $auto_login) )
        {
            $tpl->assign_var('META', '<meta http-equiv="refresh" content="3;url=login.php' . $SID . '&amp;redirect=' . $redirect . '">');

            message_die($user->lang['invalid_login'], $user->lang['error']);
        }
    }
    elseif ( $user->data['user_id'] != ANONYMOUS )
    {
        $user->destroy();
    }

    $redirect_url = ( $in->exists('redirect') ) ? preg_replace('#^.*?redirect=(.+?)&(.+?)$#', '\\1' . $SID . '&\\2', $in->get('redirect')) : 'index.php';
    redirect($redirect_url);
}

//
// Lost Password Form
//
$core->set_vars(array(
    'page_title'    => $user->lang['login_title'],
    'template_file' => 'login.html')
);
if ( $in->exists('lost_password') )
{	
		$jquery->Validate('lost_password', array(
				array('name' => 'username', 'value' => $user->lang['jqfv_required_user']), 
				array('name' => 'user_email', 'value' => $user->lang['jqfv_required_email'])
		));
		$jquery->ResetValidate('lost_password');
		
		$tpl->add_js('$(document).ready(function() { document.lost_password.username.focus()}) ');
    $tpl->assign_vars(array(
        'S_LOGIN' => false,
				
				'F_ACTION'						=> 'lostpassword',
        'L_GET_NEW_PASSWORD' => $user->lang['get_new_password'],
        'L_USERNAME'         => $user->lang['username'],
        'L_EMAIL'            => $user->lang['email'],
				'L_EMAIL_NOTE'       => $html->HelpTooltip($user->lang['lost_password_email_info']),
        'L_SUBMIT'           => $user->lang['submit'],
        'L_RESET'            => $user->lang['reset'])
    );

    $core->generate_page();
}

//
//	 Resend Activation Mail
//

elseif( $in->exists('resend_activation_mail') ){
		$jquery->Validate('lost_password', array(
				array('name' => 'username', 'value'=> $user->lang['jqfv_required_user']), 
				array('name'=>'user_email', 'value'=>$user->lang['jqfv_required_email'])
		));
		$jquery->ResetValidate('lost_password');
		
		$tpl->add_js('$(document).ready(function() { document.lost_password.username.focus()}) ');
    $tpl->assign_vars(array(
        'S_LOGIN' => false,
				
				'F_ACTION'						=> 'resend_validation',
        'L_GET_NEW_PASSWORD' => $user->lang['get_new_activation_mail'],
        'L_USERNAME'         => $user->lang['username'],
        'L_EMAIL'            => $user->lang['email'],
				'L_EMAIL_NOTE'       => $html->HelpTooltip($user->lang['validation_email_info']),
        'L_SUBMIT'           => $user->lang['submit'],
        'L_RESET'            => $user->lang['reset'])
    );

    $core->generate_page();

}


//
// Login form
//
elseif ( $user->data['user_id'] <= 0 )
{
  $jquery->Validate('login', array(array('name' => 'username', 'value'=> $user->lang['jqfv_required_user']), array('name'=>'password', 'value'=>$user->lang['jqfv_required_password'])));
	$tpl->add_js('$(document).ready(function() { document.login.username.focus()}) ');
	$tpl->assign_vars(array(
        'S_LOGIN' => true,
        'L_LOGIN'             => $user->lang['login']  ,
				'S_BRIDGE_INFO'				=>  ($core->config['pk_bridge_cms_deac_reg'] ==1) ? true : false,
				'L_BRIDGE_INFO'				=> $user->lang['login_bridge_notice'],
        'L_USERNAME'          => $user->lang['username'],
        'L_PASSWORD'          => $user->lang['password'],
        'L_REMEMBER_PASSWORD' => $user->lang['remember_password'],
				'L_RESEND_ACTIVATION' => $user->lang['get_new_activation_mail'],
				'S_USER_ACTIVATION'		=> ($core->config['account_activation'] == 1) ? true : false,
				
				'REDIRECT'						=> ( isset($redirect) ) ? '<input type="hidden" name="redirect" value="'.sanitize($redirect).'">' : '',
        'L_LOST_PASSWORD'     => $user->lang['lost_password'],

      )
    );

    $core->generate_page();
}
else
{
    redirect('index.php'.$SID);
}
?>