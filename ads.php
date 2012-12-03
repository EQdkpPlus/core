<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * viewmember.php
 * Began: Thu December 19 2002
 *
 * $Id$
 *
 ******************************/

define('EQDKP_INC', true);
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');

    $tpl->assign_vars(array(
        'HEADER'			=> $user->lang['ads_header'],	
        'TEXT' 				=> $user->lang['ads_text']	
        ));

$eqdkp->set_vars(array(
    'page_title'    => sprintf($user->lang['title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']),
    'template_file' => 'ads.html',
    'display'       => true)
);        

?>
