<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2016 EQdkp-Plus Developer Team
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

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

//Shortcut function for registry::get_const
function get_const($value){
	return registry::get_const($value);
}

//Shortcut function for registry::register
function register($value, $params=array()){
	return registry::fetch($value, $params);
}

/**
 * Determines if a folder path is valid. Ignores .svn, CVS, cache, etc.
 *
 * @param		string $path		Path to check
 * @return		boolean
 */
function valid_folder($path){
	$ignore = array('.', '..', '.svn', 'CVS', 'cache', 'install', 'index.html', '.htaccess', '_images', 'libraries.php', '.tmb', '.quarantine');
	if (isset($path)){
		if (!in_array(basename($path), $ignore) && !is_file($path) && !is_link($path)){
			return true;
		}
	}
	return false;
}

/**
 * Get all dirs/files in a folder which fits the mask, optional strip the file extension
 *
 * @param 		string $path			Path for output
 * @param 		string $mask			'*' for all files, '*.php' for allphp files
 * @param 		string $strip			Strip file extension, p.e. '.php'
 * @param 		string $nocache			To cache or not to cache.. thats the question
 * @return		boolean
 */
function sdir( $path='.', $mask='*', $strip='', $nocache=0 ){
	static $dir	= array(); // cache result in memory
	if(!is_dir($path)) return array();
	$sdir = array();
	$ignore		= array('.', '..', '.svn', 'CVS', 'index.html', '.htaccess', '.tmb', '.quarantine');
	if ( (!isset($dir[$path]) || $nocache)) {
		$dir[$path] = scandir($path);
	}
	foreach ($dir[$path] as $i=>$entry) {
		if (!in_array($entry, $ignore) && fnmatch($mask, $entry) ) {
			$sdir[] = ($strip) ? str_replace($strip, '', $entry) : $entry;
		}
	}
	return ($sdir);
}


function includeLibraries($path, $mask){
	$arrFiles = sdir($path, $mask);
	foreach($arrFiles as $file){
		include($path.'/'.$file);
	}
}


/**
 * Rundet je nach Einstellungen im Eqdkp Plus Admin Menu die DKP Werte
 *
 * @param float $value
 * @return float
 */
function runden($value){
	$ret_val		= $value;
	$precision	= (int)registry::register('config')->get('round_precision');

	if($precision < 0) $precision = 0;
	if($precision > 5) $precision = 5;

	if ((int)registry::register('config')->get('round_activate')){
		$ret_val = round($value,$precision)	;
		$ret_val = number_format($ret_val, $precision, '.', '');
	} else {
		$ret_val = number_format(round($value, 2), 2, '.', '');
	}
	return $ret_val;
}

/**
 * returns comparison result
 * @param string $version1
 * @param string $version2
 * @return int
 */
function compareVersion($version1, $version2){
	$version1 = strtolower($version1);
	$version2 = strtolower($version2);
	return version_compare($version1, $version2);
}

/**
 * returns sorted ids				example: sorting by name
 * @param array $tosort				array($id => array('name' => name))
 * @param array $order				array(0, 0)
 * @param array $sort_order			array(0 => 'name')
 * @return array
 */
function get_sortedids($tosort, $order, $sort_order){
	if(!is_array($tosort) || count($tosort) < 1) return array();
	$sorts = array();
	foreach($tosort as $id => $detail){
		$sorts[$id] = $detail[$sort_order[$order[0]]];
	}
	if($order[1]){
		arsort($sorts);
	}else{
		asort($sorts);
	}
	foreach($sorts as $id => $detail){
		$sortids[] = $id;
	}
	return $sortids;
}

/**
 * Redirects the user to another page and exits cleanly
 *
 * @param		string		$url			URL to redirect to
 * @param		bool		$return			Whether to return the generated redirect url (true) or just redirect to the page (false)
 * @param		bool		$extern			Is it an external link (other server) or an internal link?
 * @return		mixed						null, else the parsed redirect url if return is true.
 */
function redirect($url='', $return=false, $extern=false, $blnShowPage=true, $strContent=""){
	if($url == "") $url = registry::get_const('controller_path_plain');

	$out = (!$extern) ? (registry::register('environment')->link.str_replace('&amp;', '&', $url)) : registry::fetch('user')->removeSIDfromString($url);

	if ($return){
		return $out;
	}else{

		if($strContent && $strContent != ""){
			$intRedirectTime = 5;
		} else {
			//Do not send Referer when redirecting to external pages
			if($extern){
				header('Referrer-Policy: no-referrer');
			}

			header('Location: ' . $out);
			$intRedirectTime = 3;
		}

		if(defined('USER_INITIALIZED') && $blnShowPage) {
			registry::register('template')->add_meta('<meta http-equiv="refresh" content="'.$intRedirectTime.';URL='.$out.'" />');

			registry::register('template')->assign_vars(array(
				'MSG_CLASS'		=> 'blue',
				'MSG_ICON'		=> 'fa-refresh',
				'MSG_TITLE'		=> registry::register('user')->lang('redirection'),
				'MSG_TEXT'		=> '<br/><a href="'.$out.'">'.registry::register('user')->lang('redirection_info')."</a>".$strContent,
				'S_MESSAGE'		=> true,
			));

			registry::register('core')->set_vars(array(
				'header_format'		=> registry::register('core')->header_format,
				'page_title'		=> registry::register('user')->lang('redirection'),
				'template_file'		=> 'message.html'
			));
			registry::register('core')->generate_page();
		}
	}
}

/**
 * Returns the appropriate CSS class to use based on a number's range
 *
 * @param		string		$item			The number
 * @param		boolean		$percentage		Treat the number like a percentage?
 * @return		mixed						CSS Class / false
*/
function color_item($item, $percentage = false){
	if (!is_numeric($item)){
		return false;
	}
	$class		= 'neutral';
	$vals = registry::register('config')->get('color_items');
	$max_val	= ($vals[1]) ? $vals[1] : 67;
	$min_val	= ($vals[0]) ? $vals[0] : 34;

	if (!$percentage){
		if($item < 0){
			$class = 'negative';
		}elseif($item > 0){
			$class = 'positive';
		}
	}else{
		if($item >= 0 && $item <= $min_val){
			$class = 'negative';
		}elseif ($item >= $max_val && $item <= 100){
			$class = 'positive';
		}
	}
	return $class;
}

/**
 * Returns coloured member names
 *
 * @return string
 */
function get_coloured_names($norm, $pos=array(), $neg=array()){
	$mems = array();
	if(is_array($neg)){
		foreach($neg as $member_id){
			$mems[] = "<span class='negative'>".register('pdh')->get('member', 'name', array($member_id))."</span>";
		}
	}
	if(is_array($norm)){
		foreach($norm as $member_id){
			$mems[] = register('pdh')->get('member', 'name', array($member_id));
		}
	}
	if(is_array($pos)){
		foreach($pos as $member_id){
			$mems[] = "<span class='positive'>".register('pdh')->get('member', 'name', array($member_id))."</span>";
		}
	}
	asort($mems);
	return implode(', ', $mems);
}

/**
 * Copyright notice
 *
* ACCORDING TO THE AGPL LICENSE,
* YOU ARE NOT PERMITTED TO RUN EQDKP-PLUS WITHOUT THIS COPYRIGHT NOTICE.
* CHANGING, REMOVING OR OBSTRUCTING IT IS PROHIBITED BY LAW!
*/
function gen_htmlhead_copyright($text){

	return preg_replace('/(<head[^er>]*>)/',
			"$1\n\t<!--\n\n"
			."\tThis website is powered by EQDKP-PLUS Gamers CMS :: Licensed under AGPL v3.0\n"
			."\tCopyright © 2006-" . date('Y') . " by EQDKP-PLUS Dev Team :: Plugins are copyright of their authors\n"
			."\tVisit the project website at ".EQDKP_PROJECT_URL." for more information\n\n"
			."\t//-->",
			$text);
}

/**
 * Sanitize an imput
 *
 * @param		string		$input				Input to sanitize
 * @return		string
 */
function sanitize($input){
	if (is_array($input)){
		return array_map("sanitize", $input);
	}

	return filter_var($input, FILTER_SANITIZE_STRING);
}

/**
 * unsanatize the input
 *
 * @param		string		$input				Input to reverse
 * @return		string
 */
function unsanitize($input){
	if (is_array($input)){
		return array_map("unsanitize", $input);
	}

	$input = str_replace("&#34;", "&quot;", $input);
	return htmlspecialchars_decode($input, ENT_QUOTES);
}

/**
 * Returns a string with a list of available pages
 *
 * @param		string		$url				The starting URL for each page link
 * @param		int			$items				The number of items we're paging through
 * @param		int			$per_page			How many items to display per page
 * @param		int			$start_item			Which number are we starting on
 * @param		string		$start_variable		In case you need to call your _GET var something other than 'start'
 * @return		string
 */
function generate_pagination($url, $items, $per_page, $start, $start_variable='start', $offset=0){

		$uri_symbol = ( strpos($url, '?') !== false) ? '&amp;' : '?';

		//On what page we are?
		$recent_page = ($start == 0) ? 1 : (int)floor($start / $per_page) + 1;
		//Calculate total pages
		$total_pages = ($items == 0) ? 1 : ceil($items / $per_page);
		//Return if we don't have at least 2 Pages
		if (!$items || $total_pages  < 2){
			return '';
		}

		$base_url = $url . $uri_symbol . $start_variable;
		//First Page
		$pagination = '<div class="pagination"><ul data-pages="'.$total_pages.'" data-base-url="'.$base_url.'=" data-per-page="'.$per_page.'">';
		if ($recent_page == 1){
			$pagination .= '<li class="active"><a href="#">1</a></li>';
		} else {
			$pagination .= '<li class="arrow-left"><a href="'.$base_url.'='.(( ($recent_page - 2) * $per_page) + $offset).'" title="'.registry::fetch('user')->lang('previous_page').'"><i class="fa fa-angle-double-left"></i></a></li><li><a href="'.$url.'" class="pagination">1</a></li>';
		}

		//If total-pages <= 4 show all page-links
		if ($total_pages <= 4){
				$pagination .= ' ';
				for ( $i = 2; $i < $total_pages; $i++ ){
					if ($i == $recent_page){
						$pagination .= '<li class="active"><a href="#">'.$i.'</a></li> ';
					} else {
						$pagination .= '<li><a href="'.$base_url.'='.(( ($i - 1) * $per_page) +$offset).'" title="'.registry::fetch('user')->lang('page').' '.$i.'" class="pagination">'.$i.'</a></li>';
					}
					$pagination .= '';
				}
		//Don't show all page-links
		} else {
			$start_count = min(max(1, $recent_page - 5), $total_pages - 4);
			$end_count = max(min($total_pages, $recent_page + 5), 4);

			$pagination .= ( $start_count > 1 ) ? '<li><a class="paginationPageSelector hand">...</a></li>' : '';

			for ( $i = $start_count + 1; $i < $end_count; $i++ ){
				if ($i == $recent_page){
					$pagination .= '<li class="active"><a href="#">'.$i.'</a></li> ';
				} else {
					$pagination .= '<li><a href="'.$base_url.'='.( (($i - 1) * $per_page)+$offset).'" title="'.registry::fetch('user')->lang('page').' '.$i.'" class="pagination">'.$i.'</a></li>';
				}
			}
			$pagination .= ($end_count < $total_pages ) ? '<li><a class="paginationPageSelector hand">...</a></li>' : '';
		} //close else


		//Last Page
		if ($recent_page == $total_pages){
			$pagination .= '<li class="active"><a href="#">'.$recent_page.'</a></li>';
		} else {
			$pagination .= '<li><a href="'.$base_url.'='.((($total_pages - 1) * $per_page)+$offset) . '" class="pagination" title="'.registry::fetch('user')->lang('page').' '.$total_pages.'">'.$total_pages.'</a></li><li class="arrow-right"><a href="'.$base_url.'='.(($recent_page * $per_page)+$offset).'" title="'.registry::fetch('user')->lang('next_page').'"><i class="fa fa-angle-double-right"></i></a></li>';
		}

	$pagination .= '</ul><div class="clear"></div></div>';
	return $pagination;
}
/*
 * Add the necessary Javascript for infotooltip to the template
 * @return true
 */
function infotooltip_js() {
	static $added = 0;
	if(!$added AND registry::register('config')->get('infotooltip_use')) {
		$blnUseOwnTooltips = register('config')->get('infotooltip_own_enabled');
		if($blnUseOwnTooltips){
			registry::register('template')->add_meta(register('config')->get('infotooltip_own_script'));
		} else {
			registry::register('template')->js_file(registry::get_const('server_path').'infotooltip/jquery.infotooltip.js');
			$js = "$('.infotooltip').infotooltips(); var cached_itts = new Array();";
				$js .= "$('.infotooltip-tt').tooltip({
							track: true,
							content: function(response) {
								var direct = $(this).attr('title').substr(0,1);
								var mytitle = $(this).attr('title');
								if(direct == '1') {
									$(this).attr('title', '');
									return '';
								}
								if (mytitle == ''){
									return;
								}

								if (cached_itts['t_'+$(this).attr('title')] != undefined){
									return cached_itts['t_'+$(this).attr('title')];
								} else {
									var bla = $.get('".registry::get_const('server_path')."infotooltip/infotooltip_feed.php".registry::get_const('SID')."&direct=1&data='+$(this).attr('title'), response);
									bla.done(function(data) {
										cached_itts['t_'+mytitle] = $.trim(data);
									});
									return '<i class=\"fa fa-spinner fa-spin fa-lg\"></i> ".registry::fetch('user')->lang('lib_loading')."';
								}
							},
							classes: {
								\"ui-tooltip\": \"ui-infotooltip\"
							}
						});";
			registry::register('template')->add_js($js, 'docready');
			registry::register('template')->css_file(registry::get_const('server_path').'games/'.registry::register('config')->get('default_game').'/infotooltip/'.registry::register('config')->get('default_game').'.css');
		}
	}
	$added = 1;
	return true;
}

/*
 * Direct-Display of itt-Items
 * @string $span_id: unique element id for javascript
 * @string $name: name of the item
 * @int $game_id: ingame-item-id
 * @string $lang: display language
 * @int $direct: 0: tooltip as tooltip, 1: direct display of tooltip, 2: direct display + force update
 * @int $onlyicon: >0: icon-size and only icon is displayed
 * @string $in_span: if you like to display something else, except itemname before loading tooltip
 * @int $
 * return @string
 */
function infotooltip($name='', $game_id='', $lang=false, $direct=0, $onlyicon=0, $noicon=false, $data=array(), $in_span=false, $class_add='', $withColorForIconOnly=false){
	$blnUseOwnTooltips	= register('config')->get('infotooltip_own_enabled');
	$data						= (is_array($data)) ? $data : array();
	if($blnUseOwnTooltips){
		$strLink = register('config')->get('infotooltip_own_link');
		$strLink = str_replace(array('{ITEMID}', '{ITEMNAME}', '{ITEMLINK}'), array($game_id, $name, 'data-eqdkplink=""'), $strLink);

		return $strLink;
	} else {
		if(!isset($data['server']) || empty($data['server'])) $data['server'] = registry::register('config')->get("servername");
		$lang = ($lang) ? $lang : registry::fetch('user')->lang('XML_LANG');
		if(register('config')->get('itt_overwrite_lang')) $lang = register('config')->get('itt_langprio1');

		if($withColorForIconOnly) $data['withcolorforicon'] = true;

		$cachedname = register('infotooltip')->getcacheditem($name, $lang, $game_id, $onlyicon, $noicon, $data);
		$id = unique_id();
		$data = array('name' => $name, 'game_id' => $game_id, 'onlyicon' => $onlyicon, 'noicon' => $noicon, 'lang' => $lang, 'data' => $data);
		if($direct > 1) $data['update'] = true;
		$data = serialize($data);
		$direct = ($direct) ? 1 : 0;
		if($cachedname && $cachedname != "" && !$direct){
			$str = '<span class="infotooltip-tt '.$class_add.'" id="span_'.$id.'" title="'.$direct.urlencode(base64_encode($data)).'">'.$cachedname;
			return $str.'</span>';
		} else {
			$str = '<span class="infotooltip infotooltip-tt '.$class_add.'" id="span_'.$id.'" title="'.$direct.urlencode(base64_encode($data)).'">';
		}

		return $str.(($in_span !== false && strlen($in_span)) ? $in_span : $name).'</span>';
	}
}

/*
 * Add the necessary Javascript for infotooltip to the template
 * @return true
 */
function chartooltip_js() {
	static $charTTadded = 0;
	if(!$charTTadded && registry::register('game')->type_exists('chartooltip')) {
		$js = "var cached_charTT = new Array();";
			$js .= "$('.chartooltip').tooltip({
						track: true,
						content: function(response) {
							mytitle = $(this).attr('title');
							if (cached_charTT['t_'+$(this).attr('title')] != undefined){
								return cached_charTT['t_'+$(this).attr('title')];
							} else {
								var bla = $.get('".registry::get_const('server_path')."exchange.php".registry::get_const('SID')."&out=chartooltip&charid='+$(this).attr('title'), response);
								bla.done(function(data) {
									cached_charTT['t_'+mytitle] = $.trim(data);
								});
								return '<i class=\"fa fa-spinner fa-spin fa-lg\"></i> ".registry::fetch('user')->lang('lib_loading')."';
							}
						},
						tooltipClass: \"ui-infotooltip\",
					});";
		registry::register('template')->add_js($js, 'docready');
		registry::register('template')->css_file(registry::get_const('server_path').'games/'.registry::register('config')->get('default_game').'/chartooltip/chartooltip.css');
	}
	$charTTadded = 1;
	return true;
}

/**
 * Outputs a message with debugging info if needed and ends output.
 * Clean replacement for die()
 *
 * @param		string		$text			Message text
 * @param		string		$title			Message title
 * @param		string		$file			File name
 * @param		int			$line			File line
 * @param		string		$sql			SQL code
 * @param		array		$buttons		Buttons
 */
function message_die($text = '', $title = '', $type = 'normal', $login_form = false, $debug_file = '', $debug_line = '', $debug_sql = '', $button = ''){

	//Output if template-class is not available
	if ( !is_object(register('template')) ){
		echo($error_message);
		exit;
	}

	register('template')->assign_vars(array(
		'MSG_TITLE'		=> (strlen($title)) ? $title : '',
		'MSG_TEXT'		=> (strlen($text)) ? $text  : '',
	));

	//Buttons
	if (is_array($button)){

		registry::register('template')->assign_vars(array(
			'S_BUTTON'		=> true,
			'BU_FORM'		=> ($button['form_action'] != "") ? '<form action="'.$button['form_action'].'" method="post" name="post">' : '',
			'BU_FORM_END'	=> ($button['form_action'] != "") ? '</form>' : '',
			'BU_VALUE'		=> $button['value'],
			'BU_ONCLICK'	=> ($button['onclick'] != "") ? ' onclick="'.$button['onclick'].'"' : '',
		));
	}

	//Page-Header
	if (function_exists('page_title')){
		$page_title = page_title(((strlen($title)) ? $title : 'Message'));
	} elseif (is_object(registry::fetch('user'))) {
		$page_title = registry::fetch('user')->lang('message_title');
	} else {
		$page_title = 'Message';
	}

	//Switch rounded boxes and icons
	switch($type){
		case 'access_denied':	$message_class = 'red';
								$icon = 'fa-minus-circle';
		break;

		case 'info':			$message_class = 'blue';
								$icon = 'fa-info-circle ';
		break;

		case 'error':			$message_class = 'red';
								$icon = 'fa-exclamation-triangle';
		break;

		case 'ok':				$message_class = 'green';
								$icon = 'fa-check';
		break;

	}

	if ($type != 'normal'){
		registry::register('template')->assign_vars(array(
			'MSG_CLASS'		=> (strlen($message_class)) ? $message_class : '',
			'MSG_ICON'		=> (strlen($icon)) ? $icon  : '',
			'S_MESSAGE'		=> true,
		));
	}


	//Login-Form
	if ($login_form){
		if (!registry::fetch('user')->is_signedin()){
			registry::register('template')->add_js('$("#username");', 'docready');

			$redirect = registry::register('environment')->eqdkp_request_page;
			$redirect = registry::fetch('user')->removeSIDfromString($redirect);

			registry::register('template')->assign_vars(array(
				'S_LOGIN'				=> true,
				'S_BRIDGE_INFO'			=> (registry::register('config')->get('cmsbridge_active') == 1) ? true : false,
				'S_USER_ACTIVATION'		=> (registry::register('config')->get('account_activation') == 1) ? true : false,
				'REDIRECT'				=> ( isset($redirect) ) ? '<input type="hidden" name="redirect" value="'.base64_encode($redirect).'" />' : '',
			));
		}

	}

	registry::register('core')->set_vars(array(
		'header_format'		=> registry::register('core')->header_format,
		'page_title'		=> $page_title,
		'template_file'		=> 'message.html'
	));
	registry::register('core')->generate_page();
}

function isValidURL($url){
	return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
}



// returns brightness value from 0 to 255
function get_brightness($hex) {
	$hex			= str_replace('#', '', $hex);
	$color_red		= hexdec(substr($hex, 0, 2));
	$color_green	= hexdec(substr($hex, 2, 2));
	$color_blue		= hexdec(substr($hex, 4, 2));
	return (($color_red * 299) + ($color_green * 587) + ($color_blue * 114)) / 1000;
}

// "Extend" recursively array $a with array $b values (no deletion in $a, just added and updated values)
function array_extend($a, $b){
	foreach($b as $k=>$v){
		if(is_array($v)){
			if(!isset($a[$k])){
				$a[$k] = $v;
			}else{
				$a[$k] = array_extend($a[$k], $v);
			}
		}else{
			$a[$k] = $v;
		}
	}
	return $a;
}

function search_in_array($child, $haystack, $strict=false, $key='') {
	foreach ($haystack as $k => $v){
		if(is_array($v)){
			$return = search_in_array($child, $v, $strict, $key);
			if(is_array($return)){
				return array($k => $return);
			}
		}else{
			if ((!$strict AND $v == $child) OR ($strict AND $v === $child)){
				if (($key == '') OR ($key != '' AND $k == $key)){
					return array($k => $child);		// got a match, stack it & return it
				}
			}
		}
	}

	return false;			// nothing found
}

function arraykey_for_array($keyArray, $haystack){
	foreach ($keyArray as $k => $v){
		$result = $haystack[$k];
		if (is_array($v)){
			$result = arraykey_for_array($v, $result);
			return $result;
		} else {
			return $haystack;
		}
	}
	return false;
}

// in_array for multiple search items
function multi_array_search($array, $search){
	// Create the result array
	$result = array();

	// Iterate over each array element
	foreach ($array as $key => $value){
		// Iterate over each search condition
		foreach ($search as $k => $v){
			// If the array element does not meet the search condition then continue to the next element
			if (!isset($value[$k]) || $value[$k] != $v){
				continue 2;
			}
		}
		// Add the array element's key to the result array
		$result[] = $key;
	}
	// Return the result array
	return $result;
}

function countWhere($input = array(), $operator = '==', $value = null, $key = null, $i=0){
	$supported_ops	= array('<','>','<=', '>=','==', '!=', '===');
	$operator		= !in_array($operator, $supported_ops) ? '==' : $operator;

	if(is_array($input)){
		array_walk_recursive($input, 'compare_value', array('operator' => $operator, 'value' => $value, 'key' => $key, 'count' => &$i));
	}
	return $i;
}

function compare_value($item, $key, $settings){
	if($settings['key'] != null)
		if($key != $settings['key'])
		return;

	switch($settings['operator']){
		case '<':
			if($item < $settings['value'])
				$settings['count']++;
			break;
		case '>':
			if($item > $settings['value'])
				$settings['count']++;
			break;
		case '<=':
			if($item <= $settings['value'])
				$settings['count']++;
			break;
		case '>=':
			if($item >= $settings['value'])
				$settings['count']++;
			break;
		case '==':
			if($item == $settings['value'])
				$settings['count']++;
			break;
		case '!=':
			if($item != $settings['value'])
				$settings['count']++;
			break;
		case '===':
			if($item === $settings['value'])
				$settings['count']++;
			break;
	}
}

// this is to make the fmatch working for windows php less 5.3
if(!function_exists('fnmatch')) {
	function fnmatch($pattern, $string) {
		return @preg_match('/^' . strtr(addcslashes($pattern, '/\\.+^$(){}=!<>|'),array('*' => '.*', '?' => '.?')) . '$/i', $string);
	}
}

function set_cookie($name, $cookie_data, $cookie_time, $blnHttpOnly=true){
	//dont set cookies if we dont have a cookie-name or cookie-path
	$cname = register('config')->get('cookie_name');
	$cpath = register('config')->get('cookie_path');
	if(empty($cname) || empty($cpath)) return;
	setcookie( $cname . '_' . $name, $cookie_data, $cookie_time, $cpath, register('config')->get('cookie_domain'), register('env')->ssl, $blnHttpOnly);
}

//A workaround because strtolower() does not support UTF8
function utf8_strtolower($string){
	if (function_exists('mb_strtolower')){
		$string = mb_strtolower($string,'UTF-8');
	} else {
		 $convert_to = array(
			"a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u",
			"v", "w", "x", "y", "z", "à", "á", "â", "ã", "ä", "å", "æ", "ç", "è", "é", "ê", "ë", "ì", "í", "î", "ï",
			"ð", "ñ", "ò", "ó", "ô", "õ", "ö", "ø", "ù", "ú", "û", "ü", "ý", "а", "б", "в", "г", "д", "е", "ё", "ж",
			"з", "и", "й", "к", "л", "м", "н", "о", "п", "р", "с", "т", "у", "ф", "х", "ц", "ч", "ш", "щ", "ъ", "ы",
			"ь", "э", "ю", "я"
		  );
		  $convert_from = array(
			"A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U",
			"V", "W", "X", "Y", "Z", "À", "Á", "Â", "Ã", "Ä", "Å", "Æ", "Ç", "È", "É", "Ê", "Ë", "Ì", "Í", "Î", "Ï",
			"Ð", "Ñ", "Ò", "Ó", "Ô", "Õ", "Ö", "Ø", "Ù", "Ú", "Û", "Ü", "Ý", "А", "Б", "В", "Г", "Д", "Е", "Ё", "Ж",
			"З", "И", "Й", "К", "Л", "М", "Н", "О", "П", "Р", "С", "Т", "У", "Ф", "Х", "Ц", "Ч", "Ш", "Щ", "Ъ", "Ъ",
			"Ь", "Э", "Ю", "Я"
		  );

		$string = str_replace($convert_from, $convert_to, $string);
	}


	return $string;
}

function utf8_strtoupper($string){
	if (function_exists('mb_strtoupper')){
		$string = mb_strtoupper($string,'UTF-8');
	} else {
		$convert_from = array(
				"a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u",
				"v", "w", "x", "y", "z", "à", "á", "â", "ã", "ä", "å", "æ", "ç", "è", "é", "ê", "ë", "ì", "í", "î", "ï",
				"ð", "ñ", "ò", "ó", "ô", "õ", "ö", "ø", "ù", "ú", "û", "ü", "ý", "а", "б", "в", "г", "д", "е", "ё", "ж",
				"з", "и", "й", "к", "л", "м", "н", "о", "п", "р", "с", "т", "у", "ф", "х", "ц", "ч", "ш", "щ", "ъ", "ы",
				"ь", "э", "ю", "я"
		);
		$convert_to = array(
				"A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U",
				"V", "W", "X", "Y", "Z", "À", "Á", "Â", "Ã", "Ä", "Å", "Æ", "Ç", "È", "É", "Ê", "Ë", "Ì", "Í", "Î", "Ï",
				"Ð", "Ñ", "Ò", "Ó", "Ô", "Õ", "Ö", "Ø", "Ù", "Ú", "Û", "Ü", "Ý", "А", "Б", "В", "Г", "Д", "Е", "Ё", "Ж",
				"З", "И", "Й", "К", "Л", "М", "Н", "О", "П", "Р", "С", "Т", "У", "Ф", "Х", "Ц", "Ч", "Ш", "Щ", "Ъ", "Ъ",
				"Ь", "Э", "Ю", "Я"
		);
		$string = str_replace($convert_from, $convert_to, $string);
	}
	return $string;
}

function utf8_substr($str, $s=0, $l=1){
	if(function_exists('mb_substr')){
		return mb_substr($str, $s, $l, 'UTF-8');
	} else {
		return substr($str, $s, $l);
	}
}


function is_utf8($str){
	if (function_exists("mb_detect_encoding")){
		if(mb_detect_encoding($str, 'UTF-8, ISO-8859-1') === 'UTF-8'){
			return true;
		} else {
			return false;
		}
	}

	$strlen = strlen($str);
	for($i=0; $i<$strlen; $i++){
		$ord = ord($str[$i]);

		if($ord < 0x80) continue; // 0bbbbbbb
		elseif(($ord&0xE0)===0xC0 && $ord>0xC1) $n = 1; // 110bbbbb (exkl C0-C1)
		elseif(($ord&0xF0)===0xE0) $n = 2; // 1110bbbb
		elseif(($ord&0xF8)===0xF0 && $ord<0xF5) $n = 3; // 11110bbb (exkl F5-FF)
		else return false; // ungültiges UTF-8-Zeichen
		for($c=0; $c<$n; $c++) // $n Folgebytes? // 10bbbbbb
			if(++$i===$strlen || (ord($str[$i])&0xC0)!==0x80)
				return false; // ungültiges UTF-8-Zeichen
	}
	return true; // kein ungültiges UTF-8-Zeichen gefunden
}

function utf8_ucfirst($str) {
	if(function_exists('mb_strtoupper')){
		$fc = mb_strtoupper(mb_substr($str, 0, 1));
		return $fc.mb_substr($str, 1);
	} else {
		return ucfirst($str);
	}
}

function clean_username($strUsername){
	$strUsername = utf8_strtolower($strUsername);
	return $strUsername;
}

function xhtml_entity_decode($string){
	$string = html_entity_decode($string,  ENT_QUOTES, 'UTF-8');
	return $string;
}

function randomID() {
	return sha1(microtime() . uniqid(mt_rand(), true));
}

function generateRandomBytes($length = 16) {
	try {
		if (function_exists('random_bytes')) {
			$bytes = random_bytes($length);
			if ($bytes === false) throw new Exception('Cannot generate a secure stream of bytes.');

			return $bytes;
		}

		$bytes = openssl_random_pseudo_bytes($length, $s);
		if (!$s) throw new Exception('Cannot generate a secure stream of bytes.');

		return $bytes;
	} catch (\Exception $e) {
		throw new Exception('Cannot generate a secure stream of bytes.', $e);
	}
}

function random_integer($min, $max) {
	try {
		$range = $max - $min;

		if (function_exists('random_int')) {
			return random_int($min, $max);
		}

		$log = log($range, 2);
		$bytes = (int) ($log / 8) + 1; // length in bytes
		$bits = (int) $log + 1; // length in bits
		$filter = (int) (1 << $bits) - 1; // set all lower bits to 1
		do {
			$rnd = hexdec(bin2hex(generateRandomBytes($bytes)));
			$rnd = $rnd & $filter; // discard irrelevant bits
		}
		while ($rnd > $range);

		return $min + $rnd;


	}
	catch (Exception $e) {
		// Backwards compatibility: This function never did throw.
		return mt_rand($min, $max);
	}
}


function random_string($length = 20){
	if($length === false) $length = 20;

	$binLength = ceil($length / 2);

	$string = bin2hex(generateRandomBytes($binLength));
	return $string;
}


function get_absolute_path($path) {
	$strMyDirectorySeperator = "/";
	$path = str_replace(array('/', '\\'), $strMyDirectorySeperator, $path);
	$parts = array_filter(explode($strMyDirectorySeperator, $path), 'strlen');
	$absolutes = array();
	foreach ($parts as $part) {
		if ('.' == $part) continue;
		if ('..' == $part) {
			array_pop($absolutes);
		} else {
			$absolutes[] = $part;
		}
	}
	return implode($strMyDirectorySeperator, $absolutes);
}

function clean_rootpath($strPath){
	return preg_replace('/[^\.\/]/', '', $strPath);
}

function cut_text($strText, $max = 200, $blnAddPoints = true){
	$v = $strText;
	if (strlen($strText) > $max) {
		$v = substr($v.' ' , 0 , $max + 1);
		$v = substr($v , 0 , strrpos ($v , ' '));
		if ($blnAddPoints) $v .= '...';
	}
	return $v;
}

/**
* Truncates text.
*
* Cuts a string to the length of $length and replaces the last characters
* with the ending if the text is longer than length.
*
* @param string $text String to truncate.
* @param integer $length Length of returned string, including ellipsis.
* @param string $ending Ending to be appended to the trimmed string.
* @param boolean $exact If false, $text will not be cut mid-word
* @param boolean $considerHtml If true, HTML tags would be handled correctly
* @return string Trimmed string.
*/
function truncate($text, $length = 100, $ending = '…', $exact = true, $considerHtml = false) {
	if ($considerHtml) {
		// if the plain text is shorter than the maximum length, return the whole text
		if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
		return $text;
	}

	// splits all html-tags to scanable lines
	preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);

	$total_length = strlen($ending);
	$open_tags = array();
	$truncate = '';

	foreach ($lines as $line_matchings) {
		// if there is any html-tag in this line, handle it and add it (uncounted) to the output
		if (!empty($line_matchings[1])) {
			// if it's an “empty element'' with or without xhtml-conform closing slash (f.e.)
			if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
				// do nothing
				// if tag is a closing tag (f.e. )
			} else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
				// delete tag from $open_tags list
				$pos = array_search($tag_matchings[1], $open_tags);
				if ($pos !== false) {
					unset($open_tags[$pos]);
				}
				// if tag is an opening tag (f.e. )
			} else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
				// add tag to the beginning of $open_tags list
				array_unshift($open_tags, strtolower($tag_matchings[1]));
			}
			// add html-tag to $truncate'd text
			$truncate .= $line_matchings[1];
		}

		// calculate the length of the plain text part of the line; handle entities as one character
		$content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
		if ($total_length+$content_length > $length) {
			// the number of characters which are left
			$left = $length - $total_length;
			$entities_length = 0;
			// search for html entities
			if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
			// calculate the real length of all entities in the legal range
				foreach ($entities[0] as $entity) {
					if ($entity[1]+1-$entities_length <= $left) {
						$left--;
						$entities_length += strlen($entity[0]);
					} else {
						// no more characters left
						break;
					}
				}
			}
			$truncate .= substr($line_matchings[2], 0, $left+$entities_length);
			// maximum lenght is reached, so get off the loop
			break;
		} else {
			$truncate .= $line_matchings[2];
			$total_length += $content_length;
		}

		// if the maximum length is reached, get off the loop
		if($total_length >= $length) {
			break;
		}
	}
	} else {
		if (strlen($text) <= $length) {
			return $text;
		} else {
			$truncate = substr($text, 0, $length - strlen($ending));
		}
	}

	// if the words shouldn't be cut in the middle...
	if (!$exact) {
		// ...search the last occurance of a space...
		$spacepos = strrpos($truncate, ' ');
		if (isset($spacepos)) {
			// ...and cut the text in this position
			$truncate = substr($truncate, 0, $spacepos);
		}
	}

	// add the defined ending to the text
	$truncate .= $ending;

	if($considerHtml) {
		// close all unclosed html-tags
		foreach ($open_tags as $tag) {
			$truncate .= '</'.$tag.'>';
		}
	}

	return $truncate;
}

function get_first_image($strHTML, $blnGetFullImage = false){
	if (class_exists("DOMDocument")){
		$dom = new DOMDocument();
		$dom->loadHTML('<html><body>'.$strHTML.'</body></html>');
		$images = $dom->getElementsByTagName('img');
		 foreach ($images as $image) {
			$src = $image->getAttribute('src');
			if ($src && strlen($src)){
				if ($blnGetFullImage && strpos($src, 'eqdkp/news/thumb/')){
					$src = str_replace('eqdkp/news/thumb/', 'eqdkp/news/', $src);
				}

				if (strpos($src, '/') === 0){
					return register('env')->httpHost.$src;
				} else {
					return $src;
				}
			}
		}
	}
	return '';
}

function get_chmod(){
	if(defined('CHMOD')) return CHMOD;
	return 0775;
}

function br2nl( $input ) {
	return preg_replace('/<br(\s+)?\/?>/i', "\n", $input);
}

function human_filesize($bytes, $dec = 2)
{
	$size   = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
	$factor = floor((strlen($bytes) - 1) / 3);

	return sprintf("%.{$dec}f", $bytes / pow(1024, $factor)) . ' '.@$size[$factor];
}


//Checks if an filelink is in an given folder. Set strict true if FileLink should not be in subfolder
function isFilelinkInFolder($strFilelink, $strFolder, $blnStrict=false){
	$strPath = pathinfo($strFilelink, PATHINFO_DIRNAME);
	if (substr($strFilelink, -1) == "/"){
		$strPath = $strPath . "/" .pathinfo($strFilelink, PATHINFO_BASENAME);
	}

	$strAbsolutePath = get_absolute_path($strPath);
	if (substr($strFolder, -1) == "/"){
		$strFolder = substr($strFolder, 0, -1);
	}
	$strFolder = str_replace(registry::get_const('root_path'), '', $strFolder);

	if($blnStrict){
		if ($strAbsolutePath === $strFolder) return true;
	} else {
		if (strpos($strAbsolutePath, $strFolder) === 0) return true;
	}
	return false;
}

// Debug functions
function pd($var='backtrace', $die=false) {
	if(!is_object(registry::register('plus_debug_logger'))) var_dump($var);
	registry::register('plus_debug_logger')->debug($var, $die);
}
function pr($info='', $ret1=false, $ret2=false) {
	if(!is_object(registry::register('plus_debug_logger'))) return false;
	return registry::register('plus_debug_logger')->runtime($info, $ret1, $ret2);
}
function pf($var) {
	if(!is_object(registry::register('plus_debug_logger'))) return false;
	return registry::register('plus_debug_logger')->format_var($var);
}
/**
 * var_dump array
 *
 * @param array $array
 */
function da_($array){
	echo "<pre>";
	var_dump($array);
	echo "</pre>";
}

/**
* Debug Function
* wenn inhalt ein array ist, wird da() aufgerufen
*
* @param mixed $content
* @return mixed
*/
function d($content="-" ){
	if(is_array($content)){
		return da($content, true);
	}
	if (is_object($content)) {
		echo "<pre>";
		var_dump($content);
		echo "</pre>";
	}
	if (is_bool($content)) {
		if($content == true){
			$content = "Bool - True";
		}else{
			$content = "Bool - false";
		}
	}
	if (strlen($content) ==0) {
		$content = "String Length=0";
	}
	echo "<table border=0>\n";
	echo "<tr>\n";
	echo "<td bgcolor='#0080C0'>";
	echo "<B>" . $content . "</B>";
	echo "</td>\n";
	echo "</tr>\n";

	$arrBacktrace = debug_backtrace();
	if($arrBacktrace && isset($arrBacktrace[0])){
		echo "<tr><td  bgcolor='#efefef'>Debug called in ".$arrBacktrace[0]['file']." line ".$arrBacktrace[0]['line']."</td></tr>";
	}

	echo "</table>\n";
}

/**
 * Debug Function
 * gibt ein Array in Tabbelarischer Form aus.
 *
 * @param Array $TheArray
 * @return mixed
 */
function da($TheArray, $blnPostDebugLine=false){ // Note: the function is recursive
	if(!is_array($TheArray)){
		return "no array";
	}
	echo "<table border=0>\n";
	$Keys = array_keys( $TheArray );
	foreach( $Keys as $OneKey ){
		echo "<tr>\n";
		echo "<td bgcolor='#727450'>";
		echo "<B>" . $OneKey . "</B>";
		echo "</td>\n";
		echo "<td bgcolor='#C4C2A6'>";
		if ( is_array($TheArray[$OneKey]) ){
			da($TheArray[$OneKey]);
		}else{
			echo $TheArray[$OneKey];
		}
		echo "</td>\n";
		echo "</tr>\n";
	}
	
	if($blnPostDebugLine){
		$arrBacktrace = debug_backtrace();
		if($arrBacktrace && isset($arrBacktrace[1])){
			echo "<tr><td  bgcolor='#efefef' colspan=\"2\">Debug called in ".$arrBacktrace[1]['file']." line ".$arrBacktrace[1]['line']."</td></tr>";
		}
	}
	
	echo "</table>\n";
}

/*
 * Fix for uniqid()
 * Since it returns the same ID when called multiple times per microsecond
 *
 * @param string $prefix
 * @param boolean $more_entropy
 * @return string
 */
function unique_id($prefix='', $more_entropy=false) {
	$id = uniqid($prefix, $more_entropy);
	usleep(1);
	return $id;
}

function inline_svg($strFile){
	$strContent = "";
	if (is_file($strFile)){
		$strContent = file_get_contents($strFile);
	}
	return $strContent;
}

/**
 * Check value to find if it was serialized.
 *
 * If $data is not an string, then returned value will always be false.
 * Serialized data is always a string.
 * Wordpress!
 * http://jetpack.wp-a2z.org/oik_api/is_serialized/
 */
function is_serialized( $data, $strict = true ) {
	// if it isn't a string, it isn't serialized.
	if ( ! is_string( $data ) ) {
		return false;
	}
	$data = trim( $data );
 	if ( 'N;' == $data ) {
		return true;
	}
	if ( strlen( $data ) < 4 ) {
		return false;
	}
	if ( ':' !== $data[1] ) {
		return false;
	}
	if ( $strict ) {
		$lastc = substr( $data, -1 );
		if ( ';' !== $lastc && '}' !== $lastc ) {
			return false;
		}
	} else {
		$semicolon = strpos( $data, ';' );
		$brace     = strpos( $data, '}' );
		// Either ; or } must exist.
		if ( false === $semicolon && false === $brace )
			return false;
		// But neither must be in the first X characters.
		if ( false !== $semicolon && $semicolon < 3 )
			return false;
		if ( false !== $brace && $brace < 4 )
			return false;
	}
	$token = $data[0];
	switch ( $token ) {
		case 's' :
			if ( $strict ) {
				if ( '"' !== substr( $data, -2, 1 ) ) {
					return false;
				}
			} elseif ( false === strpos( $data, '"' ) ) {
				return false;
			}
			// or else fall through
		case 'a' :
		case 'O' :
			return (bool) preg_match( "/^{$token}:[0-9]+:/s", $data );
		case 'b' :
		case 'i' :
		case 'd' :
			$end = $strict ? '$' : '';
			return (bool) preg_match( "/^{$token}:[0-9.E-]+;$end/", $data );
	}
	return false;
}

function is_serialized0($strValue){
	$data = @unserialize($strValue);
	if ($strValue === 'b:0;' || $data !== false) {
		return true;
	} else {
		return false;
	}
}

function full_copy($source, $target){
	if (is_dir($source)){
		register('pfh')->CheckCreateFolder($target);
		$d = dir($source);

		while (FALSE !== ($entry = $d->read())){
			if ($entry == '.' || $entry == '..'){
				continue;
			}

			$Entry = $source . '/' . $entry;
			if (is_dir( $Entry )){
				full_copy($Entry, $target . '/' . $entry);
				continue;
			}
			register('pfh')->copy($Entry, $target . '/' . $entry);
			if (!is_file($target . '/' . $entry)) return false;
		}
		$d->close();
	} else {
		register('pfh')->copy($source, $target);
		if (!is_file($target)) return false;
	}

	return true;
}

function implode_r($glue, $pieces){
	$out = "";
	foreach ($pieces as $piece) {
		if (is_array ($piece)) $out .= implode_r ($glue, $piece); // recurse
		else $out .= $glue.$piece;
	}
	return $out;
 }


 function hyphenize($string) {
 	$dict = array(
 			"I'm"      => "I am",
 			"thier"    => "their",
 			// Add your own replacements here
 	);
 	return strtolower(
 			preg_replace(
 					array( '#[\\s-]+#', '#[^A-Za-z0-9\. -]+#' ),
 					array( '-', '' ),
 					// the full cleanString() can be downloaded from http://www.unexpectedit.com/php/php-clean-string-of-utf8-chars-convert-to-similar-ascii-char
 					cleanString(urldecode($string))
 					)
 			);
 }

 function cleanString($text) {
 	$utf8 = array(
 			'/[áàâãªä]/u'   =>   'a',
 			'/[ÁÀÂÃÄ]/u'    =>   'A',
 			'/[ÍÌÎÏ]/u'     =>   'I',
 			'/[íìîï]/u'     =>   'i',
 			'/[éèêë]/u'     =>   'e',
 			'/[ÉÈÊË]/u'     =>   'E',
 			'/[óòôõºö]/u'   =>   'o',
 			'/[ÓÒÔÕÖ]/u'    =>   'O',
 			'/[úùûü]/u'     =>   'u',
 			'/[ÚÙÛÜ]/u'     =>   'U',
 			'/ç/'           =>   'c',
 			'/Ç/'           =>   'C',
 			'/ñ/'           =>   'n',
 			'/Ñ/'           =>   'N',
 			'/–/'           =>   '-', // UTF-8 hyphen to "normal" hyphen
 			'/[’‘‹›‚]/u'    =>   ' ', // Literally a single quote
 			'/[“”«»„]/u'    =>   ' ', // Double quote
 			'/ /'           =>   ' ', // nonbreaking space (equiv. to 0x160)
 	);
 	return preg_replace(array_keys($utf8), array_values($utf8), $text);
 }

/**
 * Anonymizes an IP address
 * @param string $ip IP-address
 * @param number $byteCount How much bytes should by anomymized
 * @return string anonymized ip address
 */
function anonymize_ipaddress($ip, $byteCount=1){

 	$binaryIp = @inet_pton($ip);
 	if(!$binaryIp) $binaryIp = "\x00\x00\x00\x00";

 	$strlen = function_exists('mb_orig_strlen') ? 'mb_orig_strlen' : 'strlen';
 	if($strlen($binaryIp) == 4){
 		//ipv4
 		$i = strlen($binaryIp);
 		if ($byteCount > $i) {
 			$byteCount = $i;
 		}

 		while ($byteCount-- > 0) {
 			$binaryIp[--$i] = chr(0);
 		}

 		$ipStr = @inet_ntop($binaryIp);
 		if(!$ipStr) $ipStr = "0.0.0.0";
 		return $ipStr;
 	} else {
 		//ipv6
 		if (substr_compare($binaryIp, "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\xff\xff", 0, 12) === 0
 				|| substr_compare($binaryIp, "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", 0, 12) === 0) {

 					$i = strlen($binaryIp);
 					if ($byteCount > $i) {
 						$byteCount = $i;
 					}

 					while ($byteCount-- > 0) {
 						$binaryIp[--$i] = chr(0);
 					}

 					$ipStr = @inet_ntop($binaryIp);
 					if(!$ipStr) $ipStr = "0.0.0.0";
 					return $ipStr;
 				}

 				$masks = array(
 						'ffff:ffff:ffff:ffff:ffff:ffff:ffff:ffff',
 						'ffff:ffff:ffff:ffff::',
 						'ffff:ffff:ffff:0000::',
 						'ffff:ff00:0000:0000::'
 				);

 				$binaryIp = $binaryIp & pack('a16', inet_pton($masks[$byteCount]));
 				$ipStr = @inet_ntop($binaryIp);
 				if(!$ipStr) $ipStr = "0.0.0.0";
 				return $ipStr;
 	}
 }




/* Workarounds because php does not allow arrays in Constants < 5.6 */
function get_attr_blacklist(){
	global $ATTR_BLACKLIST;
	return $ATTR_BLACKLIST;
}

function get_tag_blacklist(){
	global $TAG_BLACKLIST;
	return $TAG_BLACKLIST;
}
