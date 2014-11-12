<?php
define('EQDKP_INC', true);
$eqdkp_root_path = './../../';

include_once ($eqdkp_root_path . 'common.php');

if(!registry::fetch('user')->is_signedin()){
	echo('You have no permission to see this page as you are not logged in'); exit;
}

?>

<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>elFinder 2.0</title>

		<!-- jQuery and jQuery UI (REQUIRED) -->
		<link rel="stylesheet" type="text/css" media="screen" href="css/jquery-ui.css">
		<script type="text/javascript" src="js/jquery.min.js"></script>
		<script type="text/javascript" src="js/jquery-ui.min.js"></script>

		<!-- elFinder CSS (REQUIRED) -->
		<link rel="stylesheet" type="text/css" media="screen" href="css/elfinder.min.css">
		<link rel="stylesheet" type="text/css" media="screen" href="css/theme.css">

		<!-- elFinder JS (REQUIRED) -->
		<script type="text/javascript" src="js/elfinder.min.js"></script>

		<!-- elFinder initialization (REQUIRED) -->
		<script type="text/javascript" charset="utf-8">
			var target = '<?php echo register('input')->get('field'); ?>';
			var myCommands = elFinder.prototype._options.commands;
			var disabled = ['extract', 'archive','mkdir', 'mkfile','help','rename','download','edit'];
			$.each(disabled, function(i, cmd) {
				(idx = $.inArray(cmd, myCommands)) !== -1 && myCommands.splice(idx,1);
			});
		
			$().ready(function() {
				var elf = $('#elfinder').elfinder({
					url : 'php/connector.useravatars.php<?php echo registry::get_const('SID');?>',  // connector URL (REQUIRED)
					// lang: 'ru',             // language (OPTIONAL)
					onlyMimes: ["image/jpeg","image/png","image/gif"],
					commands : myCommands,
					getFileCallback: function(url) { // editor callback
						//alert(url); // pass selected file path to TinyMCE
						parent.$('#'+target).val(url.url);
						parent.$('#image_'+target+' .previewimage').attr("src", url.url);
						parent.$('#image_'+target+' .previewurl').attr("href", url.url);
						parent.$(".ui-dialog-content").dialog("close");
						jQuery.FrameDialog.closeDialog();
					  }
				}).elfinder('instance');
			});
		</script>
		<style>
		.elfinder-navbar {
			display: none !important;
		}
		</style>
	</head>
	<body>

		<!-- Element where elFinder will be created (REQUIRED) -->
		<div id="elfinder"></div>

	</body>
</html>
