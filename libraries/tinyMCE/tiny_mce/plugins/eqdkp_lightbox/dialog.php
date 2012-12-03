<?php 
define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../../../../../';
include_once($eqdkp_root_path . 'common.php');

// Download the Image to eqdkp
function DownloadImage($img){
		$pfh = register('file_handler');
		$puf = register('urlfetcher');
		
		$thumbfolder	= $pfh->FolderPath('news/thumb', 'eqdkp');
		$imgfolder  	= $pfh->FolderPath('news', 'eqdkp');
		
		//If its an dynamic image...
		$extension_array = array('jpg', 'png', 'gif', 'jpeg');
		$path_parts = pathinfo($img);
		if (!in_array(strtolower($path_parts['extension']), $extension_array)){
			return false;
		}
		
		// Load it...
		$tmp_name = md5(rand());
		$pfh->CheckCreateFile($imgfolder.$tmp_name);
		$pfh->putContent($imgfolder.$tmp_name, $puf->fetch($img)); 
		$i = getimagesize($imgfolder.$tmp_name);
		
		// Image is no image, lets remove it
		if (!$i) {
			$pfh->Delete($imgfolder.$tmp_name);
			return false;
		}
		
		$myFileName = $imgfolder.substr(md5(rand()), 0,8).'_'.$path_parts['filename'].'.'.$path_parts['extension'];
		$pfh->rename($imgfolder.$tmp_name, $myFileName);
		return $myFileName;
}

$user = register('user');
$in = register('input');
$pfh = register('file_handler');

if ($user->check_auth('a_news_add', false) || $user->check_auth('a_news_upd', false) || $user->check_auth('a_pages_man', false)){

	if ($in->exists('submit')){
		if (strlen($in->get('src'))){
			$image = DownloadImage($in->get('src'));
			if ($image){
				$path_parts = pathinfo($image);
				$pfh->thumbnail($image, $pfh->FolderPath('news/thumb', 'eqdkp'), $path_parts['filename'].'.'.$path_parts['extension'], $in->get('width', 400));
				$img_src = $pfh->FileLink('news/'.$path_parts['filename'].'.'.$path_parts['extension'], 'eqdkp', 'absolute');

				if (is_file($pfh->FolderPath('news/thumb', 'eqdkp').$path_parts['filename'].'.'.$path_parts['extension'])){
					$thumb_src = $pfh->FileLink('news/thumb/'.$path_parts['filename'].'.'.$path_parts['extension'], 'eqdkp', 'absolute');
					
					$js_code = "var output = '<a href=\"".$img_src."\" rel=\"lightbox\"><img src=\"".$thumb_src."\" alt=\"".$in->get('alt', 'Image')."\" /></a>';
					
					tinyMCEPopup.editor.execCommand('mceInsertContent', false, output);
					tinyMCEPopup.close();
					";
				} else {
					$js_code = "
					var output = '<a href=\"".$img_src."\" rel=\"lightbox\"><img src=\"".$img_src."\" alt=\"".$in->get('alt', 'Image')."\" /></a>';
					tinyMCEPopup.editor.execCommand('mceInsertContent', false, output);
					tinyMCEPopup.close();
					";
				}
			
			}
			
		}
	}



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>{#eqdkp_lightbox_dlg.title}</title>
	<script type="text/javascript" src="../../tiny_mce_popup.js?v={tinymce_version}"></script>
	<script type="text/javascript" src="js/dialog.js?v={tinymce_version}"></script>
</head>
<script>
<?php 
 if (isset($js_code)){
	echo $js_code;
 }
?>
</script>
<body>

<form action="">
	<fieldset>
						<legend>{#eqdkp_lightbox_dlg.general}</legend>

						<table role="presentation" class="properties">
							<tr>
								<td class="column1"><label id="srclabel" for="src">{#eqdkp_lightbox_dlg.src}</label></td>
								<td colspan="2"><table role="presentation" border="0" cellspacing="0" cellpadding="0">
									<tr> 
										<td><input name="src" type="text" id="src" value="" class="mceFocus" onchange="ImageDialog.showPreviewImage(this.value);" aria-required="true" /></td> 
										<td id="srcbrowsercontainer">&nbsp;</td>
									</tr>
								</table></td>
							</tr>
							<tr> 
								<td class="column1"><label id="altlabel" for="alt">{#eqdkp_lightbox_dlg.alt}</label></td> 
								<td colspan="2"><input id="alt" name="alt" type="text" value="" /></td> 
							</tr> 
							<tr> 
								<td class="column1"><label id="titlelabel" for="title">{#eqdkp_lightbox_dlg.width}</label></td> 
								<td colspan="2"><input id="width" name="width" type="text" value="<?php echo ((register('config')->get('thumbnail_defaultsize')) ?  register('config')->get('thumbnail_defaultsize') : 500); ?>" size="5"/>px</td> 
							</tr>
						</table>
				</fieldset>
				<div class="mceActionPanel">
					<div style="float: left"><input type="submit" id="insert" name="submit" value="{#insert}" /></div>
					<div style="float: right"><input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="tinyMCEPopup.close();" /></div>
				</div>
</form>

</body>
</html>
<?php } else {?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>{#eqdkp_lightbox_dlg.title}</title>
	<script type="text/javascript" src="../../tiny_mce_popup.js?v={tinymce_version}"></script>
	<script type="text/javascript" src="js/dialog.js?v={tinymce_version}"></script>
</head>
<body>
Access denied.
</body>
</html>
<?php } ?>