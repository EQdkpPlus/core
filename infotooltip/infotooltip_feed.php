<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2010
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 * 
 * $Id$
 */

define('EQDKP_INC', true);
$eqdkp_root_path = './../';
$noinit = true;
include($eqdkp_root_path.'/common.php');
registry::add_const('root_path', $eqdkp_root_path);
//init our db-class
registry::load_config();
require($eqdkp_root_path.'core/dbal/dbal.php');
require_once($eqdkp_root_path.'core/dbal/'.registry::get_const('dbtype').'.php');
registry::$aliases['db'] = array('dbal_'.registry::get_const('dbtype'), array(array('open' => true)));
error_reporting(E_ALL);

header('content-type: text/html; charset=UTF-8');
include($eqdkp_root_path.'infotooltip/infotooltip.class.php');

$itt = registry::register('infotooltip');
$in = registry::register('input');
registry::$aliases['bridge'] = 'bridge_generic';

/* Itemfetching
 * Parameters accepted:
 * 	- name => item_name OR item_ID if game_id = true
 *	- lang => language of item (short version, e.g. 'en', 'de')
 *	- game_id => see name
 */

if($in->exists('jsondata')) {
	$data				= array();
	$data['name']		= $in->get('name');
	$data['game_id']	= $in->get('game_id');
	#var_dump($data);die();
	$direct = ($in->exists('direct')) ? $in->get('direct', 0) : substr($in->get('data'), 0, 1);
}elseif($in->exists('data')) {
	$direct = ($in->exists('direct')) ? $in->get('direct', 0) : substr($in->get('data'), 0, 1);
	$data = unserialize(base64_decode(substr($in->get('data'), 1)));
} else {
	$direct = $in->get('direct', 0);
	$data['lang'] = substr($in->get('lang'),0,2);
	$data['onlyicon'] = $in->get('onlyicon', 0);
	$data['game_id'] = $in->get('game_id');
	$data['name'] = base64_decode($in->get('name'));
	$data['server'] = $in->get('server');
	$data['char_name'] = $in->get('cname');
	$data['noicon'] = $in->get('noicon', false);
	$data['slot'] = $in->get('slot', '');
}

//only for armory
$data['server'] = (isset($data['server'])) ? $data['server'] : 0;
$data['char_name'] = (isset($data['char_name'])) ? $data['char_name'] : 0;
$data['name'] = html_entity_decode($data['name'], ENT_QUOTES, 'UTF-8');
$item = $itt->getitem($data['name'], $data['lang'], $data['game_id'], $in->get('update', false), array($data['server'], $data['char_name'], $data['slot']));
$item['icon'] = (isset($item['icon'])) ? $item['icon'] : '';
$display_name = (isset($item['name']) AND strlen($item['name']) > 1) ? $item['name'] : $data['name'];
if($in->get('update', false)) {
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
	echo '<html><head>';
	echo '<title>Itemtooltip Update</title><link rel="stylesheet" href="'.$eqdkp_root_path.'infotooltip/includes/'.$itt->config['game'].'.css" type="text/css" /></head><body>';
}
if($direct) {
	echo str_replace('{ITEM_ICON_LINK}', $itt->config['icon_path'].$item['icon'].$itt->config['icon_ext'], $item['html']);
} else {
	if(isset($item['icon']) && !$data['noicon']) {
		if($data['onlyicon'] > 0) {
			$visible = '<img src="'.$itt->config['icon_path'].$item['icon'].$itt->config['icon_ext'].'" width="'.$data['onlyicon'].'" height="'.$data['onlyicon'].'" style="margin-top: 1px;" alt="icon" />';
		} else {
			$visible = '<img src="'.$itt->config['icon_path'].$item['icon'].$itt->config['icon_ext'].'" width="16" height="16" style="margin-top: 1px;" alt="icon" /> '.$display_name;
		}
	} else {
		$visible = $display_name;
	}
	if(!isset($item['html']) OR !$item['html']) {
		echo $data['name'];
	} elseif(isset($item['color'])) {
		if (substr($item['color'], 0, 1) == "#"){
			echo '<span style="color:'.$item['color'].'">'.$visible.'</span>';
		} else {
			echo '<span class="'.$item['color'].'">'.$visible.'</span>';
		}
	} else {
		echo $visible;
	}
}
if($in->get('update', false)) {
	echo "</body></html>";
}
?>