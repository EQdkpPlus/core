<!DOCTYPE html PUBLIC '-//W3C//DTD HTML 4.01 Frameset//FR'>
<html>
	<head>
		<title>Itemstats :) --  A propos de Itemstats !</title>
		<meta http-equiv='content-type' content='text/html; charset=iso-8859-1'>
		<meta http-equiv='content-style-type' content='text/css'>
		<meta http-equiv='content-language' content='fr'>
		<meta name='description' content='Itemstats display popup (infobulle) of World of Warcraft objects in English, French, German for E107, SMF, PhpBB, EqDKP, DotClear, NukedKlan, PunBB, Xoops, Invision Power Board, IPB'>
		<meta name='keywords' content='World of Warcraft, Wow, Infobulle, Popup, Tooltip, Itemstats, Itemstats FR, Itemstats français, Wowdbu, Judgehype, Allakhazam, Thottbot, Blasc, Buffed, German, Français, Deutsch, English, French, E107, SMF, PhpBB, EqDKP, DotClear, NukedKlan, PunBB, Xoops, Invision Power Board, IPB'>
	</head>
	<body bgcolor='#d5d6d7'>
<?php
/*
+---------------------------------------------------------------+
|       Itemstats FR Core
|
|       Yahourt
|       http://itemstats.free.fr
|       itemstats@free.fr
|
|       Thorkal
|       EU Elune / Horde
|       www.elune-imperium.com
+---------------------------------------------------------------+
*/

require_once("version.php");
require_once("check_maj.php");

echo '<b>Itemstats VF</b> by Yahourt<br/>';
echo '<a href="http://itemstats.free.fr">http://itemstats.free.fr</a><br/>';
echo '<a href="mailto:itemstats@free.fr">itemstats@free.fr</a><br/>';
echo '<b>Itemstats Core</b> version ' . $itemstats_core_version . '<br/>';

echo '<br/><i>Checking new version...</i><br/>';
$checkversion = check_maj();
echo $checkversion;

?>
    </body>
</html>