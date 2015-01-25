<?php 
define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../../../../../';
include_once($eqdkp_root_path . 'common.php');

$user = register('user');
$in = register('input');

if ($user->is_signedin()){
	$pdh = register('pdh');
	$arrOut = array();
	$arrUsernames = $pdh->aget('user', 'name', 0, array($pdh->get('user', 'id_list')));
	foreach($arrUsernames as $strUsername){
		$arrOut[] = array('name' => $strUsername);
	}
	echo json_encode($arrOut);
	die();
}
echo json_encode(array());
die();
?>