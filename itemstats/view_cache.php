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

require_once(dirname(__FILE__) . '/itemstats.php');
require_once(dirname(__FILE__) . '/generic_itemstats.php');
require_once(dirname(__FILE__) . '/includes/sqlhelper.php');

function display_cache()
{
	global $usee107path;

	$text = "";
	$item_stats = new ItemStats();
	$sql = new SqlHelper(dbhost, dbname, dbuser, dbpass);
    if ($sql->connected == false)
        return (false);

	$result = $sql->query("SELECT item_name FROM " . item_cache_table . " ORDER BY item_name ASC");

	$text .= '<table border="1">';

	while ($item = $sql->fetch_record($result))
	{
		$item_name = $item['item_name'];
		if (isset($usee107path) && $usee107path == 1)
			$item_link = e_BASE . path_itemstats . 'updateitem.php?item=' . urlencode(urlencode($item_name));
		else
			$item_link = 'updateitem.php?item=' . urlencode(urlencode($item_name));

        if (function_exists('itemstats_parse'))
        {
		    if (isset($usee107path) && $usee107path == 1)
			    $html = itemstats_parse_one_item($item_name, e_BASE . path_itemstats);
		    else
			    $html = itemstats_parse_one_item($item_name);
        }
        else
            $html = $item_name;

		$text .= '<tr>';
		$text .= '	<td>';
		$text .= 		$html;
		$text .= '	</td>';
		$text .= '	<td>';
		$text .= "		&nbsp;&nbsp;<a href='" . $item_link . "'>Maj</a>&nbsp;&nbsp;";
		$text .= '	</td>';
		$text .= '<tr>';
	}
	$item['text_html'] = $text;
	$sql->close();
	return $item;
}

$myhtml = "";

if (!isset($noprinttext) || $noprinttext != 1)
	$myhtml .= "<!DOCTYPE html PUBLIC '-//W3C//DTD HTML 4.01 Frameset//FR'>
<html>
	<head>
		<link rel='stylesheet' href='./templates/itemstats.css' type='text/css'>
		<script type='text/javascript' src='./overlib/overlib.js'><!-- overLIB (c) Erik Bosrup --></script>
		<title>Itemstats :) --  Visualisation du cache</title>
		<meta http-equiv='content-type' content='text/html; charset=iso-8859-1'>
		<meta http-equiv='content-style-type' content='text/css'>
		<meta http-equiv='content-language' content='fr'>
		<meta name='description' content='Itemstats display popup (infobulle) of World of Warcraft objects in English, French, German for E107, SMF, PhpBB, EqDKP, DotClear, NukedKlan, PunBB, Xoops, Invision Power Board, IPB'>
		<meta name='keywords' content='World of Warcraft, Wow, Infobulle, Popup, Tooltip, Itemstats, Itemstats FR, Itemstats français, Wowdbu, Judgehype, Allakhazam, Thottbot, Blasc, Buffed, German, Français, Deutsch, English, French, E107, SMF, PhpBB, EqDKP, DotClear, NukedKlan, PunBB, Xoops, Invision Power Board, IPB'>
	</head>
	<body bgcolor='#d5d6d7'>";

$myhtml .= "<table>";

$myitem = display_cache();
if (isset($printutf8header) && $printutf8header == 1)
	$myhtml .= utf8_encode($myitem['text_html']);
else
	$myhtml .= $myitem['text_html'];


$myhtml .= "</table>";

if (!isset($noprinttext) || $noprinttext != 1)
{
    require_once("check_maj.php");
    require_once("version.php");
    $checkversion = check_maj();
	$myhtml .= "&nbsp;&nbsp;=> <a href='clear_cache.php'>Clear unfound objects</a>
    <br/><br/><i>Checking new version...</i><br/>
    $checkversion
    <br/><br/><a href='about.php'>About Itemstats :)</a>
	</body>
</html>";
}

if (!isset($noprinttext) || $noprinttext != 1)
	echo $myhtml;

?>