<?php 
define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../../../../../';
include_once($eqdkp_root_path . 'common.php');

$pfh = register('file_handler');
$puf = register('urlfetcher');
$user = register('user');

$imgfolder = $pfh->FolderPath('system', 'files');

function myScandir(&$parentDir,$actual_dir){
	global $pfh;
	
        $scanDir = scandir($actual_dir);
        for ($i=0;$i<count($scanDir);$i++){
                if (!valid_folder($scanDir[$i]) || $scanDir[$i] == "thumbs") {
                        continue;
                }
                if (is_file($actual_dir.'/'.$scanDir[$i])){
                      
                } elseif (is_dir($actual_dir.'/'.$scanDir[$i])){
                        $dir =  $scanDir[$i];
                        $parentDir[$dir]= str_replace($pfh->FolderPath('system', 'files'), "system", "$actual_dir*+*+*$dir");
                        myScandir( $parentDir , "$actual_dir/$dir" );
                }
        }
        return true;
}

if ($user->is_signedin()){
	$parentDir = array('system' => 'system');
	myScandir($parentDir, $imgfolder);
	
	$outArray = array();
	foreach($parentDir as $key => $value){
		$outArray[] = array(
			'folder' => $value,
			'name'	=> $key,
		);
	}
	echo json_encode(array('status' => 'ok', 'folders' => $outArray));
	die();
}
echo json_encode(array('status' => 'error', 'folders' => array()));
die();
?>