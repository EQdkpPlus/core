<?php 
define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../../../../../';
include_once($eqdkp_root_path . 'common.php');

$pfh = register('file_handler');
$puf = register('urlfetcher');
$thumbfolder	= $pfh->FolderPath('system/thumbs', 'files');
$imgfolder  	= $pfh->FolderPath('system', 'files');

// Download the Image to eqdkp
function DownloadImage($img){
	global $thumbfolder,$imgfolder,$pfh,$puf;
					
		//If its an dynamic image...
		$extension_array = array('jpg', 'png', 'gif', 'jpeg');
		$path_parts = pathinfo($img);
		if (!in_array(strtolower($path_parts['extension']), $extension_array)){
			return false;
		}
		
		// Load it...
		$tmp_name = md5(generateRandomBytes());
		$pfh->CheckCreateFile($imgfolder.$tmp_name);
		$pfh->putContent($imgfolder.$tmp_name, $puf->fetch($img)); 
		$i = getimagesize($imgfolder.$tmp_name);
		
		// Image is no image, lets remove it
		if (!$i) {
			$pfh->Delete($imgfolder.$tmp_name);
			return false;
		}
		
		$myFileName = $imgfolder.substr(md5(generateRandomBytes()), 0,8).'_'.$path_parts['filename'].'.'.$path_parts['extension'];
		$pfh->rename($imgfolder.$tmp_name, $myFileName);
		return $myFileName;
}

$user = register('user');
$in = register('input');

if ($user->is_signedin()){
	if (strlen($in->get('img'))){
		$strImageSource = $in->get('img');
		if (strpos($strImageSource, '/') === 0){
			$link = register('env')->link;
			$arrURL = parse_url($link);
			
			$strImageSource = $arrURL['scheme'].'://'.$arrURL['host'].$strImageSource;
			
		} 
	
		$image = DownloadImage($strImageSource);

		if ($image){
			$path_parts = pathinfo($image);
			$pfh->thumbnail($image, $thumbfolder, $path_parts['filename'].'.'.$path_parts['extension'], $in->get('width', 400));
			$img_src = $pfh->FileLink('system/'.$path_parts['filename'].'.'.$path_parts['extension'], 'files', 'absolute');

			if (is_file($thumbfolder.$path_parts['filename'].'.'.$path_parts['extension'])){
				$thumb_src = $pfh->FileLink('system/thumbs/'.$path_parts['filename'].'.'.$path_parts['extension'], 'files', 'absolute');
				
				echo json_encode(array('status' => 'ok', 'thumb' => $thumb_src, 'image' => $img_src));
				die();

			} else {
				echo json_encode(array('status' => 'ok', 'thumb' => $img_src, 'image' => $img_src));
				die();
			}
		
		}
		
	}
}
echo json_encode(array('status' => 'error', 'thumb' => '', 'image' => ''));
die();
?>