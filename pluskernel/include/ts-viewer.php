<?php

define('EQDKP_INC', true);
//dependent on your folder change to rootpath"
$eqdkp_root_path = '../../';

//include the settings and class files
include($eqdkp_root_path.'pluskernel/bridge/bridge_common.php');
include_once($eqdkp_root_path . 'includes/db/mysql.php');
include_once($eqdkp_root_path . 'pluskernel/include/db.class.php');
include_once($eqdkp_root_path . 'pluskernel/include/TeamSpeakViewer/TS_Viewer.class.php');

	$db 		= new SQL_DB($dbhost, $dbname, $dbuser, $dbpass, false);
	$plusdb     = new dbPlus();
	$conf_plus  = $plusdb->InitConfig();

	$tss2info 	= new tss2info;
	$tss2info->sitetitle = $conf_plus['pk_ts_title'];
	$tss2info->serverAddress = $conf_plus['pk_ts_serverAddress'];
	$tss2info->serverQueryPort = $conf_plus['pk_ts_serverQueryPort'];
	$tss2info->serverUDPPort = $conf_plus['pk_ts_serverUDPPort'];
	$tss2info->serverPasswort = $conf_plus['pk_ts_serverPasswort'];


	$tss2info->TS_channelflags_ausgabe   = $conf_plus['pk_ts_channelflags'];
	$tss2info->TS_userstatus_ausgabe     = $conf_plus['pk_ts_userstatus'];
	$tss2info->TS_channel_anzeigen       = $conf_plus['pk_ts_showchannel'];
	$tss2info->TS_leerchannel_anzeigen   = $conf_plus['pk_ts_showEmptychannel'];
	$tss2info->TS_overlib_mouseover      = $conf_plus['pk_ts_overlib_mouseover'];

	# $conf_plus['pk_ts_joinableMember']
	$tss2info->joinable				   	 = $conf_plus['pk_ts_joinable'];




	$htmlout 	= $tss2info->getInfo();
	$url 		= $eqdkp_root_path.'pluskernel/include/ts.php';
	?>

	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
	<html>
		<head>
			<title>Teamspeakserver</title>
			<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
			<meta http-equiv="content-language" content="de">
			<meta http-equiv="refresh" content="190; URL=<?php echo $url; ?>">
			<link rel="stylesheet" type="text/css" href="TeamSpeakViewer/stylesheet.css">
		</head>
		<body>
		<?php echo $htmlout; ?>
		</body>
	</html>

	<?php







?>
