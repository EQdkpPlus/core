<?php
define('EQDKP_INC', true);
$eqdkp_root_path = './../../';

include_once ($eqdkp_root_path . 'common.php');

register('user')->check_auth('a_files_man');

?>

<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>File Browser</title>

		<!-- jQuery and jQuery UI (REQUIRED) -->
		<link rel="stylesheet" type="text/css" media="screen" href="css/jquery-ui.css">
		<script type="text/javascript" src="js/jquery.min.js"></script>
		<script type="text/javascript" src="js/jquery-ui.min.js"></script>

		<!-- elFinder CSS (REQUIRED) -->
		<link rel="stylesheet" type="text/css" media="screen" href="css/elfinder.min.css">
		<link rel="stylesheet" type="text/css" media="screen" href="css/theme.css">

		<!-- elFinder JS (REQUIRED) -->
		<script type="text/javascript" src="js/elfinder.min.js"></script>
		
		<?php 
			if (register('in')->get('editor') == 'tiny') {
		?>
		<script type="text/javascript" src="<?php echo $eqdkp_root_path; ?>libraries/tinyMCE/tiny_mce/tiny_mce_popup.js"></script>

		<script type="text/javascript">
		  var FileBrowserDialogue = {
			init: function() {
			  // Here goes your code for setting your custom things onLoad.
			},
			mySubmit: function (URL) {
			  var win = tinyMCEPopup.getWindowArg('window');
			  if (typeof(win) == 'undefined'){
				insertFile(URL.url);
				return;
			  }

			  // pass selected file path to TinyMCE
			  win.document.getElementById(tinyMCEPopup.getWindowArg('input')).value = URL.url;

			  // are we an image browser?
			  if (typeof(win.ImageDialog) != 'undefined') {
				// update image dimensions
				if (win.ImageDialog.getImageData) {
				  win.ImageDialog.getImageData();
				}
				// update preview if necessary
				if (win.ImageDialog.showPreviewImage) {
				  win.ImageDialog.showPreviewImage(URL.url);
				}
			  }

			  // close popup window
			 tinyMCEPopup.close();
			}
		  }

		  tinyMCEPopup.onInit.add(FileBrowserDialogue.init, FileBrowserDialogue);
		  
	function insertFile(name)	{
		var image = false;
		try {
			if (is_image(name)){			
				image = true;
			} else {
				
				image = false;
			}

		} catch(e) {
			alert("Error");
		}
		
		if (image){
			output = '<img src="'+name+'" alt="Image" />';
		} else {
			output = '<a href="'+name+'">'+name+'</a>';
		}
		
		tinyMCEPopup.editor.execCommand('mceInsertContent', false, output);
		tinyMCEPopup.editor.execCommand('mceRepaint');
		tinyMCEPopup.resizeToInnerSize();
		tinyMCEPopup.close();
	}
	
	function is_image(file_name) {
	  // Die erlaubten Dateiendungen
	  var image_extensions = new Array('jpg', 'jpeg','gif','png');

	  // Dateiendung der Datei
	  var extension = file_name.split('.');
	  extension = extension[extension.length - 1];
	  extension = extension.toLowerCase();
	  for (var k in image_extensions) {
		if (image_extensions[k] == extension) return true;
	  }
	  return false;
	}

		  
		  </script>
		<?php } ?>

		<!-- elFinder initialization (REQUIRED) -->
		<script type="text/javascript" charset="utf-8">
			var target = '<?php echo register('input')->get('field'); ?>';
			var myCommands = elFinder.prototype._options.commands;
			var disabled = ['extract', 'archive','mkfile','help','edit'];
			$.each(disabled, function(i, cmd) {
				(idx = $.inArray(cmd, myCommands)) !== -1 && myCommands.splice(idx,1);
			});
		
			$().ready(function() {
				var elf = $('#elfinder').elfinder({
					url : 'php/connector.admin.php',  // connector URL (REQUIRED)
					// lang: 'ru',             // language (OPTIONAL)
					<?php if (register('in')->get('type') == 'image') echo 'onlyMimes: ["image/jpeg","image/png","image/gif"],';?>
					commands : myCommands,
					getFileCallback: function(url) { // editor callback
						<?php if (register('in')->get('editor') == 'tiny') {?>
						FileBrowserDialogue.mySubmit(url); // pass selected file path to TinyMCE 
						<?php } else {?>
						//alert(url); // pass selected file path to TinyMCE
						parent.$('#'+target).val(url.url);
						parent.$('#image_'+target+' .previewimage').attr("src", url.url);
						parent.$('#image_'+target+' .previewurl').attr("href", url.url);
						parent.$(".ui-dialog-content").dialog("close");
						<?php } ?>
					  }
				}).elfinder('instance');
			});
		</script>
	</head>
	<body>

		<!-- Element where elFinder will be created (REQUIRED) -->
		<div id="elfinder"></div>

	</body>
</html>
