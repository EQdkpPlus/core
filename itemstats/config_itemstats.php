<?php
/*
+---------------------------------------------------------------+
|       Itemstats FR Core
|       http://itemstats.free.fr
|
|		modded by Corgan for EQdkp Plus
|		$id:
+---------------------------------------------------------------+
*/


// Order search
if($conf_plus['pk_is_prio_first'] && $conf_plus['pk_is_prio_second']
 && $conf_plus['pk_is_prio_third'] && $conf_plus['pk_is_prio_fourth'])
{
	$GLOBALS["prio"][] = $conf_plus['pk_is_prio_first'];
	$GLOBALS["prio"][] = $conf_plus['pk_is_prio_second'];
	$GLOBALS["prio"][] = $conf_plus['pk_is_prio_third'];
	$GLOBALS["prio"][] = $conf_plus['pk_is_prio_fourth'];
}
else
{
	$GLOBALS["prio"][] = 'allakhazam';
	$GLOBALS["prio"][] = 'buffed';
	$GLOBALS["prio"][] = 'thottbot';
	$GLOBALS["prio"][] = 'judgehype';
	$GLOBALS["prio"][] = 'wowdbu';
}

if (($conf_plus['pk_is_prio_first'] == 'wowhead') or ($conf_plus['pk_is_prio_first'] == 'amory'))
{
	unset($GLOBALS["prio"]);
	define('tooltip_css', 'wowhead.css');
	$GLOBALS["prio"][] = $conf_plus['pk_is_prio_first'];
}
else
{
	define('tooltip_css', 'itemstats.css');
}


// Allakhazam languages search
// Actually it search in this order : fr, en, de, es, ko, zh
if($conf_plus['pk_is_itemlanguage_alla'])
{
	$GLOBALS["allakhazam_lang"][] = $conf_plus['pk_is_itemlanguage_alla'];
}
else
{
	$GLOBALS["allakhazam_lang"][] = 'enUS';
	$GLOBALS["allakhazam_lang"][] = 'deDE';
	$GLOBALS["allakhazam_lang"][] = 'frFR';
	$GLOBALS["allakhazam_lang"][] = 'esES';
	$GLOBALS["allakhazam_lang"][] = 'koKR';
	$GLOBALS["allakhazam_lang"][] = 'zhCN';
	$GLOBALS["allakhazam_lang"][] = 'zhTW';
}


// Language default for Item's Id when not specified
// Example : [item]17182[/item] will choose this language
// It can be : 'en', 'fr', 'de'
if($conf_plus['pk_is_itemlanguage'])
{
	define('item_lang_default', $conf_plus['pk_is_itemlanguage']);
}
else
{
	define('item_lang_default', 'fr');
}


// The path for custom item, it's based on Itemstats directory path.
if($conf_plus['pk_is_patch_cache'])
{
	define('path_cache', $conf_plus['pk_is_patch_cache']);
}
else
{
	define('path_cache', './xml_cache/');
}

// Choose the comportement of Itemstats :
// - true : If the object is not on the cache, Itemstats will search it on data website (Allakhazam, etc.)
// - false : The object is displayed only if it is on cache, otherwise it stays grey and you have to click one time on it (to search the object and fill the cache)

if($conf_plus['pk_is_autosearch'])
{
	define('automatic_search', $conf_plus['pk_is_autosearch']);
}
else
{
	define('automatic_search', false);
}

// Choose the integration mode
// - normal : Use the normal method, it scans the text and inject the tooltips directly in the HTML code
// - script : Use alternative method, it scans the text and put <script> tag (that asks the tooltip by the navigator)
if($conf_plus['pk_is_integration_mode'])
{
	define('integration_mode', $conf_plus['pk_is_integration_mode']);
}
else
{
	define('integration_mode', 'normal');
}

// Choose the tooltip displayer
// - overlib : Overlib (big tooltip library with very good compatibility) ~20ko+
// - light : Light Tooltip (very light tooltip script that works) ~3ko
if($conf_plus['pk_is_tooltip_js'])
{
	define('tooltip_js', $conf_plus['pk_is_tooltip_js']);
}
else
{
	define('tooltip_js', 'overlib');
}

// Activate or not the DEBUG MODE (more information if there is a problem)
// It can be : 'true' or 'false'
if($conf_plus['pk_itemstats_debug'])
{
	define('debug_mode', $conf_plus['pk_itemstats_debug']);
}
else
{
	define('debug_mode', false);
}


//[en]
// Sockets images path (only for Allakhazam objects)
// Example : http://wow.allakhazam.com/images/
if($conf_plus['pk_is_path_sockets_image'])
{
	define('path_sockets_image', $conf_plus['pk_is_path_sockets_image']);
}
else
{
	define('path_sockets_image', 'http://wow.allakhazam.com/images/');
}

// Display the text "itemstats.free.fr" in the tooltips
// It can be : 'true' or 'false'
define('displayitemstatslink', false);
?>