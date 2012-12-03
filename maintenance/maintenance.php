<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * login.php
 * Began: Sat December 21 2002
 *
 * $Id$
 *
 ******************************/

define('EQDKP_INC', true);
$eqdkp_root_path = './../';
require_once('./common_lite.php');
require_once('./includes/template_wrap.class.php');

$tpl = new Template_Wrap('maintenance', 'maintenance.html', 'maintenance_message.html');
$tpl->page_header();
// Make our _GET and _POST vars into normal variables
// so we can process a login request through get or post
extract($_GET, EXTR_SKIP);
extract($_POST, EXTR_SKIP);
// Normal Output
if ( (isset($login)) || (isset($logout)) )
{
    if ( isset($login) && ($user->data['user_id'] <= 0) )
    {
        $redirect = ( isset($redirect) ) ? $redirect : 'index.php';

        $auto_login = ( !empty($auto_login) ) ? true : false;

        if ( !$user->login($username, $password, $auto_login) )
        {
            //echo("invalid user");
            $redirect = 'maintenance.php';
            $tpl->assign_var('META', '<meta http-equiv="refresh" content="3;url=maintenance.php' . $SID . '&amp;redirect=' . $redirect . '">');
            $tpl->message_die($user->lang['invalid_login_warning']);
        }
    }
    elseif ( $user->data['user_id'] != ANONYMOUS )
    {
        $user->destroy();
    }

    $redirect_url = ( isset($redirect) ) ? preg_replace('#^.*?redirect=(.+?)&(.+?)$#', '\\1' . $SID . '&\\2', $redirect) : 'index.php';
    redirect($redirect_url);
}

//
// Login form
//
if ( !$user->check_auth('a_maintenance', false)){
	$tpl->assign_vars(array(
        'S_LOGIN' => true,

        'L_LOGIN'             => $user->lang['login'],
        'L_USERNAME'          => $user->lang['username'],
        'L_PASSWORD'          => $user->lang['password'],
        'L_REMEMBER_PASSWORD' => $user->lang['remember_password'],
        'L_MAINTENANCE_MESSAGE' => $user->lang['maintenance_message'],
				'S_HIDE_BREADCRUMP'		=> true,
				'S_HIDE_DEBUG'				=> true,
				'S_MMODE_ACTIVE'			=> true,
				'L_ADMIN_LOGIN'				=> $user->lang['admin_login'],
				'REASON'							=> ($core->config['pk_maintenance_message'] != "") ? $user->lang['reason'].sanitize($core->config['pk_maintenance_message']) : '',

        'ONLOAD' => ' onload="javascript:document.post.username.focus()"')
    );

}else{
  $redirect_url = ( isset($redirect) ) ? preg_replace('#^.*?redirect=(.+?)&(.+?)$#', '\\1' . $SID . '&\\2', $redirect) : 'index.php';
  redirect($redirect_url);
}


$tpl->page_tail();
?>