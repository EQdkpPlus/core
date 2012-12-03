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
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');

// Make our _GET and _POST vars into normal variables
// so we can process a login request through get or post
extract($_GET, EXTR_SKIP);
extract($_POST, EXTR_SKIP);

// If there's an external login without showing template stuff and so on...
if($external == 'yes')
{
  $auto_login = ( !empty($auto_login) ) ? true : false;
  if($user->login($username, $password, $auto_login))
  {
    die('OK');
  }
  else
  {
    die('Error');
  }
}

// Normal Output
if ( (isset($login)) || (isset($logout)) )
{
    if ( isset($login) && ($user->data['user_id'] <= 0) )
    {
        $redirect = ( isset($redirect) ) ? $redirect : 'index.php';

        $auto_login = ( !empty($auto_login) ) ? true : false;

        if ( !$user->login($username, $password, $auto_login) )
        {
            $tpl->assign_var('META', '<meta http-equiv="refresh" content="3;url=login.php' . $SID . '&amp;redirect=' . $redirect . '">');

            message_die($user->lang['invalid_login'], $user->lang['error']);
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
// Lost Password Form
//
$eqdkp->set_vars(array(
    'page_title'    => sprintf($user->lang['title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['login_title'],
    'template_file' => 'login.html')
);
if ( isset($lost_password) )
{
    $tpl->assign_vars(array(
        'S_LOGIN' => false,

        'L_GET_NEW_PASSWORD' => $user->lang['get_new_password'],
        'L_USERNAME'         => $user->lang['username'],
        'L_EMAIL'            => $user->lang['email'],
        'L_SUBMIT'           => $user->lang['submit'],
        'L_RESET'            => $user->lang['reset'])
    );

    $eqdkp->display();
}

//
// Login form
//
elseif ( $user->data['user_id'] <= 0 )
{
    

	$tpl->assign_vars(array(
        'S_LOGIN' => true,

        'L_LOGIN'             => ($conf_plus['pk_bridge_cms_active'] ==1) ? $user->lang['login_bridge_notice'] : $user->lang['login']  ,
        'L_USERNAME'          => $user->lang['username'],
        'L_PASSWORD'          => $user->lang['password'],
        'L_REMEMBER_PASSWORD' => $user->lang['remember_password'],

        'L_LOST_PASSWORD'     => $user->lang['lost_password'],

        'ONLOAD' => ' onload="javascript:document.post.username.focus()"')
    );

    $eqdkp->display();
}
else
{
    redirect('index.php'.$SID);
}
?>