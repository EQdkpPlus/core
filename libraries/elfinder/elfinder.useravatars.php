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
		<meta charset="utf-8">
		<title>Useravatars</title>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=2" />

		<!-- Section CSS -->
		<!-- jQuery UI (REQUIRED) -->
		<link rel="stylesheet" type="text/css" href="jquery/jquery-ui.css">

		<!-- elFinder CSS (REQUIRED) -->
		<link rel="stylesheet" type="text/css" href="css/elfinder.min.css">
		<link rel="stylesheet" type="text/css" href="css/theme.css">

		<!-- Section JavaScript -->
		<!-- jQuery and jQuery UI (REQUIRED) -->
		<!--[if lt IE 9]>
		<script src="jquery/jquery.min.js"></script>
		<![endif]-->
		<!--[if gte IE 9]><!-->
		<script src="jquery/jquery.min.js"></script>
		<!--<![endif]-->
		<script src="jquery/jquery-ui.min.js"></script>

		<!-- elFinder JS (REQUIRED) -->
		<script src="js/elfinder.min.js"></script>

		<!-- Extra contents editors (OPTIONAL) -->
		<script src="js/extras/editors.default.min.js"></script>

		<!-- GoogleDocs Quicklook plugin for GoogleDrive Volume (OPTIONAL) -->
		<!--<script src="js/extras/quicklook.googledocs.js"></script>-->

		<!-- elFinder initialization (REQUIRED) -->
		<script type="text/javascript" charset="utf-8">
			// Documentation for client options:
			// https://github.com/Studio-42/elFinder/wiki/Client-configuration-options
			$(document).ready(function() {
				var target = '<?php echo register('input')->get('field'); ?>';
				var myCommands = elFinder.prototype._options.commands;
				var disabled = ['extract', 'archive','mkdir', 'mkfile','help','rename','download','edit'];
				$.each(disabled, function(i, cmd) {
					(idx = $.inArray(cmd, myCommands)) !== -1 && myCommands.splice(idx,1);
				});
				
				$('#elfinder').elfinder(
					// 1st Arg - options
					{
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
					},
					// 2nd Arg - before boot up function
					function(fm, extraObj) {
						// `init` event callback function
						fm.bind('init', function() {
							// Optional for Japanese decoder "extras/encoding-japanese.min"
							delete fm.options.rawStringDecoder;
							if (fm.lang === 'jp') {
								fm.loadScript(
									[ fm.baseUrl + 'js/extras/encoding-japanese.min.js' ],
									function() {
										if (window.Encoding && Encoding.convert) {
											fm.options.rawStringDecoder = function(s) {
												return Encoding.convert(s,{to:'UNICODE',type:'string'});
											};
										}
									},
									{ loadType: 'tag' }
								);
							}
						});
					}
				);
			});
		</script>
	</head>
	<body>
			<style>
		.elfinder-navbar {
			display: none !important;
		}
		</style>
		<!-- Element where elFinder will be created (REQUIRED) -->
		<div id="elfinder"></div>

	</body>
</html>