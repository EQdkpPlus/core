<?php
/******************************
 * EQdkp
 * Copyright 2002-2005
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * viewraid.php
 * Began: Thu December 19 2002
 *
 * $Id: viewraid.php 1257 2008-01-12 22:18:55Z osr-corgan $
 *
 ******************************/
 
define('EQDKP_INC', true);
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');

if($_POST['comment']){
  $comments->Save();
}
?>