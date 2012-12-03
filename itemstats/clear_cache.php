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

// Date in the past
header("Expires: Mon, 05 Sep 1985 16:31:00 GMT");

// always modified
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

// HTTP/1.1
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);

// HTTP/1.0
header("Pragma: no-cache");

// Redirect via an HTML form for PITA webservers
if (@preg_match('/Microsoft|WebSTAR|Xitami/', getenv('SERVER_SOFTWARE')))
{
    if (isset($_SERVER['HTTP_REFERER']))
		header('Refresh: 0; URL=' . $_SERVER['HTTP_REFERER']);
	echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"><html><head><meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"><meta http-equiv="refresh" content="0; url=' . $_SERVER['HTTP_REFERER'] . '"><title>Redirect</title></head><body><div align="center">If your browser does not support meta redirection please click <a href="' . $_SERVER['HTTP_REFERER'] . '">HERE</a> to be redirected</div></body></html>';
}
else
{
	// Behave as per HTTP/1.1 spec for others
    if (isset($_SERVER['HTTP_REFERER']))
		header('Location: ' . $_SERVER['HTTP_REFERER']);
}

function clear_cache()
{
	$sql = new SqlHelper(dbhost, dbname, dbuser, dbpass);
    if ($sql->connected == false)
        return (false);

	$result = $sql->query("DELETE FROM " . item_cache_table . " WHERE item_icon='" . DEFAULT_ICON . "' AND item_id='0'");
	$sql->close();
	return (true);
}

clear_cache();



?>