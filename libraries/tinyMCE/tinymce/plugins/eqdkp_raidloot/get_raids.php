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
	$raids = array();
	$raidids = $pdh->sort($pdh->get('raid', 'id_list'), 'raid', 'date', 'desc');
	foreach($raidids as $id)
	{
		$raids[$id] = register('time')->user_date($pdh->get('raid', 'date', array($id)), true).', '.$pdh->get('event', 'name', array($pdh->get('raid', 'event', array($id)))).' (#'.$id.')';
	}
	
	$raids = array_slice($raids, 0, 20, true);
	
	foreach($raids as $key => $value){
		$outArray[] = array(
			'id' 	=> $key,
			'name'	=> $value,
		);
	}
	echo json_encode(array('status' => 'ok', 'raids' => $outArray));
	die();
}
echo json_encode(array('status' => 'error', 'raids' => array()));
die();
?>