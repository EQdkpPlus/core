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
	
	$caleventids	= $pdh->sort($pdh->get('calendar_events', 'id_list', array(false, register('time')->time - (86400*7), register('time')->time + (86400*14), false)), 'calendar_events', 'date', 'asc');
	foreach($caleventids as $id)
	{
		if($pdh->get('calendar_events', 'private', array($id))) continue;
		$raids[$id] = register('time')->user_date($pdh->get('calendar_events', 'time_start', array($id)), true).', '.$pdh->get('calendar_events', 'name', array($id)).' ('.$pdh->get('calendar_events', 'calendar', array($id)).')';
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