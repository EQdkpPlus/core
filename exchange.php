<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       18 October 2009
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2006-2008 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev$
 *
 * $Id$
 */

define('EQDKP_INC', true);
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');

$myOut = '';
switch ($in->get('out')){
	case 'raidplan':	$myOut = BuildMyLink('rss.xml', 'raidplan');break;
	case 'news':			$myOut = BuildMyLink('last_news.xml', 'eqdkp');break;
	case 'items':			$myOut = BuildMyLink('last_items.xml', 'eqdkp');break;
	case 'raids':			$myOut = BuildMyLink('last_raids.xml', 'eqdkp');break;
	case 'shoutbox':	$myOut = BuildMyLink('shoutbox.xml', 'shoutbox');break;
}

if($myOut){
	readfile($myOut);	
}

function BuildMyLink($xml, $plugin){
	global $pcache;
	if($pcache->FileExists($xml, $plugin)){
		return $pcache->BuildLink().$pcache->FileLink($xml, $plugin, false);
	}else{
		return '';
	}
}


?>