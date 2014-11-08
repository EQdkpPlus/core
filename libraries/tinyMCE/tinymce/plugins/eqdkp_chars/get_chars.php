<?php 
define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../../../../../';
include_once($eqdkp_root_path . 'common.php');

$pfh = register('file_handler');
$puf = register('urlfetcher');
$user = register('user');

if ($user->is_signedin()){
	$pdh = register('pdh');
	$outArray = array();
	$members = $pdh->aget('member', 'name', 0, array($pdh->sort($pdh->get('member', 'id_list', array(false,true,false)), 'member', 'name', 'asc')));
	
	foreach($members as $key => $value){
		$outArray[] = array(
			'id' 	=> $key,
			'name'	=> unsanitize($value),
		);
	}
	echo json_encode(array('status' => 'ok', 'chars' => $outArray));
	die();
}
echo json_encode(array('status' => 'error', 'chars' => array()));
die();
?>