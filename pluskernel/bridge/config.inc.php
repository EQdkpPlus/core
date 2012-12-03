<?php
/**
 * EQDKP Forums/CMS Bridge
 * @author Mike "RedPepper" Becker
 *
 * $Id$
 */
$cms = array( // all supported cms systems.
"vbulletin" => "vBulletin.bridge.php",
"phpbb3"    => "phpbb3.bridge.php",
"phpbb2"    => "phpbb2.bridge.php",
"joomla"    => "joomla.bridge.php",
"e107"      => "e107.bridge.php",
"wbb3"      => "wbb3.bridge.php"
);

if(!is_null($conf_plus['pk_bridge_cms_sel']))
{
    $cms_sel = $conf_plus['pk_bridge_cms_sel']; // selected cms
}

$cms_host = $dbhost;
$cms_user = $dbuser;
$cms_pass = $dbpass;
$cms_db = $dbname;

//If the CMS is installed in a different Database
if ($conf_plus['pk_bridge_cms_otherDB']==1)
{
	$cms_host = $conf_plus['pk_bridge_cms_host']; // host
	$cms_user = $conf_plus['pk_bridge_cms_user']; // user
	$cms_pass = $conf_plus['pk_bridge_cms_pass']; // password
	$cms_db = $conf_plus['pk_bridge_cms_db']; // databasename
}

$cms_tableprefix = $conf_plus['pk_bridge_cms_tableprefix']; // Prefix
$cms_group = $conf_plus['pk_bridge_cms_group']; // cms group
?>