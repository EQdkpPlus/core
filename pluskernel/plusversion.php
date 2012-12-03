<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * common.php
 * Began: Tue December 17 2002
 *
 * $Id$
 *
 ******************************/

if ( !defined('EQDKP_INC') )
{
    die('Do not access this file directly.');
}

define('EQDKPPLUS_VERSION', '0.5.1.2');
define('EQDKPPLUS_VERSION_BETA', TRUE);
if (isset($svn_rev)) {
	define('SVN_REV', $svn_rev);
}




?>
