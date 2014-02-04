<?php
define('EQDKP_INC', true);
$eqdkp_root_path = './../../';

include_once ($eqdkp_root_path . 'common.php');

$blnIsAdmin = register('user')->check_auth('a_files_man', false);
$blnIsUser = register('user')->is_signedin() && register('user')->check_auth('u_files_man', false);

$strType = register('in')->get('type', '');

if (!$blnIsUser) die('Access denied.');

if (!$blnIsAdmin) $strType = 'image';

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

		<script type="text/javascript">
			var editor, tinymce, tinyMCE, parentWindow;
			var type = '<?php echo register('in')->get('type'); ?>';
			var field_name = '<?php echo register('in')->get('field'); ?>';
			
		  var FileBrowserDialogue = {
			init: function() {
				parentWindow = (!window.frameElement && window.dialogArguments) || opener || parent || top;
			  	tinymce = parentWindow.tinymce;
				tinyMCE = parentWindow.tinyMCE;
				editor = tinymce.EditorManager.activeEditor;
				console.log(editor);
			},
			mySubmit: function (URL) {
				
				//Comes from an popup
				if (field_name != ''){
					parentWindow.$('#'+field_name).val(URL.url);
				} else {
					//Insert into Editor Field
					insertFile(URL.url);
				}

				var window_count = editor.windowManager.windows.length;
				editor.windowManager.windows[window_count-1].close();
			}
		  }

		 FileBrowserDialogue.init();
		  
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
			var index = name.lastIndexOf("/") + 1;
			var filename = name.substr(index);
			output = '<a href="'+name+'">'+filename+'</a>';
		}
		
		editor.insertContent(output);
		
		editor.execCommand('mceRepaint');
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
					url : 'php/connector.php<?php echo registry::get_const('SID').'&sf='.register('input')->get('sf'); ?>',  // connector URL (REQUIRED)
					// lang: 'ru',             // language (OPTIONAL)
					<?php if ($strType == 'image') echo 'onlyMimes: ["image/jpeg","image/png","image/gif"],';?>
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
