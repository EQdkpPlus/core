<?php
/******************************
 * EQDKP PLUGIN: Charmanager
 * (c) 2006 by WalleniuM [Simon Wallmann]
 * http://www.kompsoft.de  
 * ------------------
 * picture.php
 * Changed: October 12, 2006
 * 
 ******************************/

$uc_upload_folder = './images/upload/';

define('EQDKP_INC', true);
define('PLUGIN', 'charmanager');
$eqdkp_root_path = './../../';
include_once($eqdkp_root_path . 'common.php');
global $table_prefix;

if (is_writable($uc_upload_folder)){
if ($_POST['save'] == ''){
echo ("
<form action='picture.php' method='post' id='form' enctype='multipart/form-data'>
	<p>
		<label for='upload'>".$user->lang['uc_load_pic']."</label> <input type='file' name='upload' id='upload' value='' size='40' />
	<br />".$user->lang['uc_allowed_types'].": jpg, gif, png, ".$user->lang['uc_max_resolution'].": 300x600 ".$user->lang['uc_pixel']."
	</p>
	<p>
		<input type='hidden' name='save' id='sendtype' value='true' />
		<input type='submit' id='submit' value='".$user->lang['uc_save_pic']."' /></p>
</form>
");
}
if ($_POST['save'] == 'true'){
		$saveinfo = iSave_upload();
	echo ("<script language='JavaScript' type='text/javascript'>
function savePicture()
{
		opener.document.post.member_pic.value='$saveinfo';
	  parent.close();
	  if(opener.document.getElementById('picbutton').style.visibility=='hidden')
 			opener.document.getElementById('picbutton').style.visibility='visible';
 			else opener.document.getElementById('picbutton').style.visibility='hidden';
 			
 		if(opener.document.getElementById('pictext').style.visibility=='hidden')
 			opener.document.getElementById('pictext').style.visibility='visible';
 			else opener.document.getElementById('pictext').style.visibility='hidden';
}
</script>");
echo "<body onload='savePicture()'>";
}
}else{
	echo $user->lang['uc_not_writable'];
}

function iSave_upload()
{
	global $uc_upload_folder;
	if(($_FILES['upload']['type'] == 'image/jpeg' || $_FILES['upload']['type'] == 'image/pjpeg' || $_FILES['upload']['type'] == 'image/gif' || $_FILES['upload']['type'] == 'image/png' || $_FILES['upload']['type'] == 'image/x-png')  && $_FILES['upload']['size'] <= 300 * 600)
	{
		$prefix = substr ( md5(uniqid(rand(),1)), 3, 10);
		$save_name = $prefix.$_FILES['upload']['name'];
		move_uploaded_file($_FILES['upload']['tmp_name'], $uc_upload_folder.$save_name);
		chmod($uc_upload_folder.$save_name, 0777);
		return $save_name;
	}

	return  false;
}
?>