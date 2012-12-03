<?php
/******************************
 * EQdkp
 * Copyright 2002-2005
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * viewraid.php
 * Began: Thu December 19 2002
 *
 * $Id$
 *
 ******************************/
 
define('EQDKP_INC', true);
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');

// Prevent Guests to write comments...
if(!$user->data['username']){
  die('No User Name!');
}

if($_GET['deleteid']){
  $pcomments->Delete($_GET['page'], $_GET['rpath']);
}elseif($_POST['comment']){
  $pcomments->Save();
}else{
  echo $pcomments->Content($_POST['attach_id'], $_POST['page'], $_POST['rpath'], true, $_POST['lang_prefix']);
}
?>