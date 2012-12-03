<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * common.php
 * Began: Tue December 17 2002
 *
 * $Id: common.php 910 2007-11-19 19:39:42Z wallenium $
 *
 ******************************/

if ( !defined('EQDKP_INC') )
{
    die('Do not access this file directly.');
}

define('EQDKPPLUS_VERSION', '0.5.0.4');
if (isset($svn_rev)) {
	define('SVN_REV', $svn_rev);
}




?>
