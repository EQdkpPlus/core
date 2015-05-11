<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2015 EQdkp-Plus Developer Team
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU Affero General Public License as published
 *	by the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU Affero General Public License for more details.
 *
 *	You should have received a copy of the GNU Affero General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

define('EQDKP_INC', true);
$eqdkp_root_path = './../';
$noinit = true;
include($eqdkp_root_path.'/common.php');
#error_reporting(E_ALL);
ini_set('display_errors', 0);
registry::add_const('root_path', $eqdkp_root_path);

try {
	//init our db-class
	registry::add_const('lite_mode', true);
	registry::load_config();
	require($eqdkp_root_path.'libraries/dbal/dbal.class.php');
	if (registry::get_const('dbtype') == 'mysql') registry::add_const('dbtype', 'mysqli');
	require_once($eqdkp_root_path.'libraries/dbal/'.registry::get_const('dbtype').'.dbal.class.php');
	registry::$aliases['db'] = array('dbal_'.registry::get_const('dbtype'), array(array('open' => true)));
	#error_reporting(E_ALL);
	header('content-type: text/html; charset=UTF-8');
	include($eqdkp_root_path.'infotooltip/infotooltip.class.php');
	$itt	= registry::register('infotooltip');
	$itt->cors_headers();
	$in		= registry::register('input');
	
	if (!function_exists("get_chmod")){
		function get_chmod(){
			if(defined('CHMOD')) return CHMOD;
			return 0775;
		}
	}

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
		$direct				= ($in->exists('direct')) ? $in->get('direct', 0) : substr($in->get('data'), 0, 1);
	}elseif($in->exists('data')) {
		$direct				= ($in->exists('direct')) ? $in->get('direct', 0) : substr($in->get('data'), 0, 1);
		$data				= unserialize(base64_decode(substr($in->get('data'), 1)));
	} else {
		$direct				= $in->get('direct', 0);
		$data['lang']		= substr($in->get('lang'),0,2);
		$data['onlyicon']	= $in->get('onlyicon', 0);
		$data['game_id']	= $in->get('game_id');
		$data['name']		= base64_decode($in->get('name'));
		$data['noicon']		= $in->get('noicon', false);
		$data['update']		= $in->get('update', false);
		$data['data']		= array($in->get('server'), $in->get('cname'), $in->get('slot', ''));
	}
	
	if(!isset($data['update'])) $data['update'] = $in->get('update', false);
	if($in->exists('lang')) $data['lang'] = substr($in->get('lang'),0,2);

	//only for armory
	$data['server']			= (isset($data['server'])) ? $data['server'] : 0;
	$data['char_name']		= (isset($data['char_name'])) ? $data['char_name'] : 0;
	$data['name']			= html_entity_decode($data['name'], ENT_QUOTES, 'UTF-8');
	$item					= $itt->getitem($data['name'], $data['lang'], $data['game_id'], $data['update'], $data['data']);
	$item['icon']			= (isset($item['icon'])) ? $item['icon'] : '';
	$display_name			= (isset($item['name']) AND strlen($item['name']) > 1) ? $item['name'] : $data['name'];
	$iconpath				= (isset($item['params']) && isset($item['params']['path']) && !empty($item['params']['path'])) ? $item['params']['path'] : $itt->config['icon_path'];
	$iconext				= (isset($item['params']) && isset($item['params']['ext']) && !empty($item['params']['ext'])) ? $item['params']['ext'] : $itt->config['icon_ext'];
	if($in->get('update', false)) {
		$cssfile			= $eqdkp_root_path.'games/'.$itt->config['game'].'/infotooltip/'.$itt->config['game'].'.css';
		if(!is_file($cssfile)) $cssfile = $eqdkp_root_path.'infotooltip/includes/'.$itt->config['game'].'.css';
		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
		echo '<html><head>';
		if(is_file($cssfile)) echo '<title>Itemtooltip Update</title><link rel="stylesheet" href="'.$cssfile.'" type="text/css" /></head><body>';
	}
	if($direct) {
		$item['html']		= str_replace('{ITEM_ICON_LINK}', $iconpath.$item['icon'].$iconext, $item['html']);
		$item['html']		= str_replace('{ITEM_ICON_PATH}', $iconpath, $item['html']);
		$item['html']		= str_replace('{ITEM_ICON_EXT}', $iconpath, $item['html']);
		echo $item['html'];
	} else {
		if(isset($item['icon']) && !$data['noicon']) {
			if($data['onlyicon'] > 0) {
				$visible = '<img src="'.$iconpath.$item['icon'].$iconext.'" width="'.$data['onlyicon'].'" height="'.$data['onlyicon'].'" style="margin-top: 1px;" alt="icon" class="itt-icon"/>';
			} else {
				$visible = '<img src="'.$iconpath.$item['icon'].$iconext.'" width="16" height="16" style="margin-top: 1px;" alt="icon" class="itt-icon"/> '.$display_name;
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
} catch (DBALException $e){
	echo ($e->message);
}




?>