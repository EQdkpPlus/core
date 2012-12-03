<?php
 /*
 * Project:     EQdkp-Plus Infotooltips
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		    http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2009
 * Date:        $Date: 2009-10-28 18:08:57 +0100 (Wed, 28 Oct 2009) $
 * -----------------------------------------------------------------------
 * @author      $Author: hoofy_leon $
 * @copyright   2009-2010 hoofy_leon
 * @link        http://eqdkp-plus.com
 * @package     infotooltip
 * @version     $Rev: 6294 $
 *
 * $Id: $
 */

define('EQDKP_INC', true);
error_reporting(E_ERROR);
header('content-type: text/html; charset=UTF-8');
$eqdkp_root_path = './../';
include($eqdkp_root_path.'/core/input.class.php');
include($eqdkp_root_path.'infotooltip/infotooltip.class.php');

$itt = new infotooltip();
$in = new Input();
$time = microtime(true);

/* Itemfetching
 * Parameters accepted:
 * 	- name => item_name OR item_ID if game_id = true
 *	- lang => language of item (short version, e.g. 'en', 'de')
 *	- game_id => see name
 */
$name_id = ($in->get('game_id')) ? $in->get('game_id') : base64_decode($in->get('name'));
$name_id = html_entity_decode($name_id, ENT_QUOTES, 'UTF-8');
//only for armory
$server = ($in->get('server', 'undefined') == 'undefined') ? 0 : $in->get('server');
$cname = ($in->get('cname', 'undefined') == 'undefined') ? 0 : $in->get('cname');
$slot = ($in->get('slotid', 'undefined') == 'undefined') ? 0 : $in->get('slotid');
$item = $itt->getitem($name_id, substr($in->get('lang'),0,2), $in->get('game_id', false), $in->get('update', false), array($server, $cname, $slot));
$display_name = (strlen($item['name']) > 1) ? $item['name'] : base64_decode($in->get('name'));
if($in->get('update', false)) {
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
	echo '<head><link rel="stylesheet" href="'.$eqdkp_root_path.'infotooltip/includes/'.$itt->config['game'].'.css" type="text/css" /></head><body>';
}
if($in->get('direct', 0)) {
	echo str_replace('{ITEM_ICON_LINK}', $itt->config['icon_path'].$item['icon'].$itt->config['icon_ext'], $item['html']);
} else {
	if($in->get('onlyicon', 0) > 0) {
		$visible = '<img src="'.$itt->config['icon_path'].$item['icon'].$itt->config['icon_ext'].'" width="'.$in->get('onlyicon').'" height="'.$in->get('onlyicon').'" style="margin-top: 1px;">';
	} else {
		$visible = '<img src="'.$itt->config['icon_path'].$item['icon'].$itt->config['icon_ext'].'" width="16" height="16" style="margin-top: 1px;"> '.$display_name;
	}
	if(!$item['html']) {
		echo base64_decode($in->get('name'));
	} else {
		echo '<span class="'.$item['color'].'">'.$visible.'</span>';
#echo (microtime(true)-$time);
	}
}
if($in->get('update', false)) {
	echo "</body>";
}

?>