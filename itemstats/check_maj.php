<?php
/*
+---------------------------------------------------------------+
|       Itemstats FR Core by Yahourt
|		modded by Corgan for EQdkp Plus
|		Add WoWHead & Armory Support
|
|		$Id:$
+---------------------------------------------------------------+
*/

require_once("includes/urlreader.php");

function check_maj()
{
	global $itemstats_core_version;
	global $itemstats_e107plugin_version;

	$text = '';
    $addr = addslashes($_SERVER['SERVER_NAME']);
    if ($addr)
	    $data = itemstats_read_url('http://itemstats.free.fr/version.php?server=' . $addr);
    if ($data == '')
        $data = itemstats_read_url('http://itemstats.free.fr/version.php');
	if ($data == '')
		$text .= 'Site does not respond !<br/>';
	else
	{
		$data = str_replace("\r", "", $data);
		$version = explode("\n", $data);
		if (isset($itemstats_core_version))
		{
			$current_core_version = explode('.', $itemstats_core_version);
			$new_core_version = explode('.', $version[0]);
			if ($current_core_version[0] < $new_core_version[0])
				$text .= '<b>Itemstats Core</b> : New version is available !<br/>';
			else if ($current_core_version[1] < $new_core_version[1])
				$text .= '<b>Itemstats Core</b> : Critically update is available !<br/>';
			else if ($current_core_version[2] < $new_core_version[2])
				$text .= '<b>Itemstats Core</b> : Major update is available !<br/>';
			else if ($current_core_version[3] < $new_core_version[3])
				$text .= '<b>Itemstats Core</b> : Minor update is available !<br/>';
			else
				$text .= '<b>Itemstats Core</b> : Up to date !<br/>';
		}
		if (isset($itemstats_e107plugin_version))
		{
			$current_e107plugin_version = explode('.', $itemstats_e107plugin_version);
			$new_e107plugin_version = explode('.', $version[1]);
			if ($current_e107plugin_version[0] < $new_e107plugin_version[0])
				$text .= '<b>Itemstats e107 plugin</b> : New version is available !<br/>';
			else if ($current_e107plugin_version[1] < $new_e107plugin_version[1])
				$text .= '<b>Itemstats e107 plugin</b> : Critically update is available !<br/>';
			else if ($current_e107plugin_version[2] < $new_e107plugin_version[2])
				$text .= '<b>Itemstats e107 plugin</b> : Major update is available !<br/>';
			else if ($current_e107plugin_version[3] < $new_e107plugin_version[3])
				$text .= '<b>Itemstats e107 plugin</b> : Minor update is available !<br/>';
			else
				$text .= '<b>Itemstats e107 plugin</b> : Up to date !<br/>';
		}
	}
	if ($text == '')
		$text .= '<b>You are up to date !</b><br/>';
	return $text;
}

?>