<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * php_info.php
 * Began: Sat April 5 2003
 *
 * $Id$
 *
 ******************************/

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');
$user->check_auth('a_');

ob_start();
phpinfo();
$pinfo = ob_get_contents();
ob_end_clean();
$pinfo = trim($pinfo);

preg_match_all('%^.*<body>(.*)</body>.*$%ms', $pinfo, $output);

$output = $output[1][0];
$output = preg_replace('#<table[^>]+>#i', '<table>', $output);
$output = str_replace('class="e"', 'class="row1"', $output);
$output = str_replace('class="v"', 'class="row2"', $output);
$output = str_replace('class="h"', '', $output);
$output = str_replace('<hr />', '', $output);

$tpl->assign_vars(array(
		'PHP_INFO'    			=> $output,
		'L_PHP_INFO'			=> $user->lang['adminc_server'],
));
				
$core->set_vars(array(
            'page_title'    => 'PHP-Info',
            'template_file' => 'admin/php_info.html',
            'display'       => true)
        );
?>
