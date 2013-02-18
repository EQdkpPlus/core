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
	$ignore = array('.', '..', '.svn', 'CVS', 'cache', 'install', 'index.html', '.htaccess', '_images', 'libraries.php');
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
	$ignore		= array('.', '..', '.svn', 'CVS', 'index.html', '.htaccess');
	if ( !isset($dir[$path]) || $nocache) {
		$dir[$path] = scandir($path);
	}
	foreach ($dir[$path] as $i=>$entry) {
		if (!in_array($entry, $ignore) && fnmatch($mask, $entry) ) {
			$sdir[] = ($strip) ? str_replace($strip, '', $entry) : $entry;
		}
	}
	return ($sdir);
}

/**
 * Rundet je nach Einstellungen im Eqdkp Plus Admin Menu die DKP Werte
 *
 * @param float $value
 * @return float
 */
function runden($value){
	$ret_val		= $value;
	$precision	= registry::register('config')->get('pk_round_precision');

	if (($precision < 0) or ($precision > 5) ){
		$precision = 2;
	}

	if (registry::register('config')->get('pk_round_activate') == "1"){
		$ret_val = round($value,$precision)	;
	} else {
		$ret_val = round($value, 5);
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
function redirect($url, $return=false, $extern=false){
	$out = (!$extern) ? registry::register('environment')->link.str_replace('&amp;', '&', $url) : registry::fetch('user')->removeSIDfromString($url);
	if ($return){
		return $out;
	}else{
		header('Location: ' . $out);
		exit;
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
	$vals = unserialize(registry::register('config')->get('pk_color_items'));
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
 * Resolve the User Browser
 *
 * @param string $member
 * @return string
 */
function resolve_browser($string){
	$string = sanitize($string);
	if( preg_match("/opera/i",$string)){
		return registry::register('html')->ToolTip($string, "<img src=\"".registry::get_const('root_path')."images/glyphs/browser/opera_icon.png\" alt=\"Opera\" />", '', array('name' => 'aibrowsers', 'my'	=> 'top right', 'at' => 'bottom right'));
	}else if( preg_match("/msie/i",$string)){
		return registry::register('html')->ToolTip($string, "<img src=\"".registry::get_const('root_path')."images/glyphs/browser/ie_icon.png\" alt=\"Internet Explorer\" />", '', array('name' => 'aibrowsers', 'my'	=> 'top right', 'at' => 'bottom right'));
	}else if( preg_match("/chrome/i", $string)){
		return registry::register('html')->ToolTip($string, "<img src=\"".registry::get_const('root_path')."images/glyphs/browser/chrome_icon.png\" alt=\"Google Chrome\" />", '', array('name' => 'aibrowsers', 'my'	=> 'top right', 'at' => 'bottom right'));
	}else if( preg_match("/konqueror/i",$string)){
		return registry::register('html')->ToolTip($string, "<img src=\"".registry::get_const('root_path')."images/glyphs/browser/konqueror_icon.png\" alt=\"Konqueror\" />", '', array('name' => 'aibrowsers', 'my'	=> 'top right', 'at' => 'bottom right'));
	}else if( preg_match("/safari/i",$string) ){
		return registry::register('html')->ToolTip($string, "<img src=\"".registry::get_const('root_path')."images/glyphs/browser/safari_icon.png\" alt=\"Safari\" />", '', array('name' => 'aibrowsers', 'my'	=> 'top right', 'at' => 'bottom right'));
	}else if( preg_match("/lynx/i",$string) ){
		return registry::register('html')->ToolTip($string, "Lynx");
	}else if( preg_match("/netscape6/i",$string) ){
		return registry::register('html')->ToolTip($string, "<img src=\"".registry::get_const('root_path')."images/glyphs/browser/netscape_icon.png\" alt=\"Netscape\" />", '', array('name' => 'aibrowsers', 'my'	=> 'top right', 'at' => 'bottom right'));
	}else if( preg_match("/mozilla/i",$string) ){
		return registry::register('html')->ToolTip($string, "<img src=\"".registry::get_const('root_path')."images/glyphs/browser/firefox_icon.png\" alt=\"Firefox\" />", '', array('name' => 'aibrowsers', 'my'	=> 'top right', 'at' => 'bottom right'));
	}else if( preg_match("/w3m/i",$string) ){
		return registry::register('html')->ToolTip($string, "w3m");
	}else{
		return registry::register('html')->ToolTip($string, "<img src=\"".registry::get_const('root_path')."images/glyphs/help_off.png\" alt=\"Help\" />", '', array('name' => 'aibrowsers', 'my'	=> 'top right', 'at' => 'bottom right'));
	}
}

/**
 * Resolve the EQDKP Page the user is surfing on..
 *
 * @param string $member
 * @return string
 */
function resolve_eqdkp_page($strPage){

	$matches = explode('&', $strPage);

	if (!empty($matches[0])){
		$file = $matches[0]; //The whole filename without the querys
		$folder = explode('/', $file); //Contains the folders

		switch($folder[0]){
			case 'admin' : $prefix = registry::fetch('user')->lang('menu_admin_panel').': ';
			break;
			case 'plugins' : $prefix = registry::fetch('user')->lang('pi_title').': '.((registry::fetch('user')->lang($folder[1])) ? registry::fetch('user')->lang($folder[1]) : ucfirst($folder[1]));
			break;
			case 'maintenance' : $prefix = registry::fetch('user')->lang('maintenance');
			break;
			default: $prefix = '';
		}

		$params = array();
		if (isset($matches[1])){
			foreach ($matches as $key => $value){
				if ($key == 0) continue;
				$par = explode('=', $value);
				$params[$par[0]] = (isset($par[1])) ? $par[1] : '';
			}
		}

		switch ($file){

			/***************** Admin *****************/
			case 'admin/info_database':
				$page = $page = '<a href="'.registry::get_const('root_path').'admin/info_database.php'.registry::get_const('SID').'">'.registry::fetch('user')->lang('title_mysqlinfo').'</a>';
			break;

			case 'admin/info_php':
				$page = $page = '<a href="'.registry::get_const('root_path').'admin/info_php.php'.registry::get_const('SID').'">PHP-Info</a>';
			break;

			case 'admin/index':
				$page = registry::fetch('user')->lang('viewing_admin_index');
			break;

			case 'admin/manage_adjustments':
				$page = $page = '<a href="'.registry::get_const('root_path').'admin/manage_adjustments.php'.registry::get_const('SID').'">'.registry::fetch('user')->lang('manadjs_title').'</a>';
			break;

			case 'admin/manage_auto_points':
				$page = $page = '<a href="'.registry::get_const('root_path').'admin/manage_auto_points.php'.registry::get_const('SID').'">'.registry::fetch('user')->lang('apa_manager').'</a>';
			break;

			case 'admin/manage_backup':
				$page = $page = '<a href="'.registry::get_const('root_path').'admin/manage_backup.php'.registry::get_const('SID').'">'.registry::fetch('user')->lang('backup').'</a>';
			break;

			case 'admin/manage_bridge':
				$page = $page = '<a href="'.registry::get_const('root_path').'admin/manage_bridge.php'.registry::get_const('SID').'">'.registry::fetch('user')->lang('manage_bridge').'</a>';
			break;

			case 'admin/manage_cache':
				$page = $page = '<a href="'.registry::get_const('root_path').'admin/manage_cache.php'.registry::get_const('SID').'">'.registry::fetch('user')->lang('pdc_manager').'</a>';
			break;

			case 'admin/manage_calendars':
				$page = $page = '<a href="'.registry::get_const('root_path').'admin/manage_calendars.php'.registry::get_const('SID').'">'.registry::fetch('user')->lang('manage_calendars').'</a>';
			break;

			case 'admin/manage_calevents':
				$page = $page = '<a href="'.registry::get_const('root_path').'admin/manage_calevents.php'.registry::get_const('SID').'">'.registry::fetch('user')->lang('manage_calevents').'</a>';
			break;

			case 'admin/manage_crons':
				$page = $page = '<a href="'.registry::get_const('root_path').'admin/manage_crons.php'.registry::get_const('SID').'">'.registry::fetch('user')->lang('manage_cronjobs').'</a>';
			break;

			case 'admin/manage_events':
				$page = $page = '<a href="'.registry::get_const('root_path').'admin/manage_events.php'.registry::get_const('SID').'">'.registry::fetch('user')->lang('manevents_title').'</a>';
			break;

			case 'admin/manage_extensions':
				$page = $page = '<a href="'.registry::get_const('root_path').'admin/manage_extensions.php'.registry::get_const('SID').'">'.registry::fetch('user')->lang('extensions').'</a>';
			break;

			case 'admin/manage_pages':
				$page = $page = '<a href="'.registry::get_const('root_path').'admin/manage_pages.php'.registry::get_const('SID').'">'.registry::fetch('user')->lang('info_manage_pages').'</a>';
			break;

			case 'admin/manage_itempools':
				$page = $page = '<a href="'.registry::get_const('root_path').'admin/manage_itempools.php'.registry::get_const('SID').'">'.registry::fetch('user')->lang('manitempool_title').'</a>';
			break;

			case 'admin/manage_items':
				$page = $page = '<a href="'.registry::get_const('root_path').'admin/manage_items.php'.registry::get_const('SID').'">'.registry::fetch('user')->lang('manitems_title').'</a>';
			break;

			case 'admin/manage_logs':
				$page = $page = '<a href="'.registry::get_const('root_path').'admin/manage_logs.php'.registry::get_const('SID').'">'.registry::fetch('user')->lang('viewlogs_title').'</a>';
			break;

			case 'admin/manage_maintenance_user':
				$page = $page = '<a href="'.registry::get_const('root_path').'admin/manage_maintenance_user.php'.registry::get_const('SID').'">'.registry::fetch('user')->lang('maintenanceuser_user').'</a>';
			break;

			case 'admin/manage_members':
				$page = $page = '<a href="'.registry::get_const('root_path').'admin/manage_members.php'.registry::get_const('SID').'">'.registry::fetch('user')->lang('manage_members_title').'</a>';
			break;

			case 'admin/manage_menus':
				$page = $page = '<a href="'.registry::get_const('root_path').'admin/manage_menus.php'.registry::get_const('SID').'">'.registry::fetch('user')->lang('manage_menus').'</a>';
			break;

			case 'admin/manage_multidkp':
				$page = $page = '<a href="'.registry::get_const('root_path').'admin/manage_multidkp.php'.registry::get_const('SID').'">'.registry::fetch('user')->lang('manmdkp_title').'</a>';
			break;

			case 'admin/manage_news':
				$page = $page = '<a href="'.registry::get_const('root_path').'admin/manage_news.php'.registry::get_const('SID').'">'.registry::fetch('user')->lang('listnews_title').'</a>';
			break;

			case 'admin/manage_news_categories':
				$page = $page = '<a href="'.registry::get_const('root_path').'admin/manage_news_categories.php'.registry::get_const('SID').'">'.registry::fetch('user')->lang('manage_newscategories').'</a>';
			break;

			case 'admin/manage_pagelayouts':
				$page = $page = '<a href="'.registry::get_const('root_path').'admin/manage_pagelayouts.php'.registry::get_const('SID').'">'.registry::fetch('user')->lang('lm_title').'</a>';
			break;

			case 'admin/manage_portal':
				$page = $page = '<a href="'.registry::get_const('root_path').'admin/manage_portal.php'.registry::get_const('SID').'">'.registry::fetch('user')->lang('portalplugin_management').'</a>';
			break;

			case 'admin/manage_portal':
				$page = $page = '<a href="'.registry::get_const('root_path').'admin/manage_portal.php'.registry::get_const('SID').'">'.registry::fetch('user')->lang('portalplugin_management').'</a>';
			break;

			case 'admin/manage_profilefields':
				$page = $page = '<a href="'.registry::get_const('root_path').'admin/manage_profilefields.php'.registry::get_const('SID').'">'.registry::fetch('user')->lang('manage_profilefields').'</a>';
			break;

			case 'admin/manage_raids':
				$page = $page = '<a href="'.registry::get_const('root_path').'admin/manage_raids.php'.registry::get_const('SID').'">'.registry::fetch('user')->lang('manraid_title').'</a>';
			break;

			case 'admin/manage_ranks':
				$page = $page = '<a href="'.registry::get_const('root_path').'admin/manage_ranks.php'.registry::get_const('SID').'">'.registry::fetch('user')->lang('manraid_title').'</a>';
			break;

			case 'admin/manage_reset':
				$page = $page = '<a href="'.registry::get_const('root_path').'admin/manage_reset.php'.registry::get_const('SID').'">'.registry::fetch('user')->lang('title_resetdkp').'</a>';
			break;

			case 'admin/manage_roles':
				$page = $page = '<a href="'.registry::get_const('root_path').'admin/manage_roles.php'.registry::get_const('SID').'">'.registry::fetch('user')->lang('rolemanager').'</a>';
			break;

			case 'admin/manage_settings':
				$page = $page = '<a href="'.registry::get_const('root_path').'admin/manage_settings.php'.registry::get_const('SID').'">'.registry::fetch('user')->lang('config_title').'</a>';
			break;

			case 'admin/manage_styles':
				$page = $page = '<a href="'.registry::get_const('root_path').'admin/manage_styles.php'.registry::get_const('SID').'">'.registry::fetch('user')->lang('styles_title').'</a>';
			break;

			case 'admin/manage_tasks':
				$page = $page = '<a href="'.registry::get_const('root_path').'admin/manage_tasks.php'.registry::get_const('SID').'">'.registry::fetch('user')->lang('uc_delete_manager').'</a>';
			break;

			case 'admin/manage_user_groups':
				$page = $page = '<a href="'.registry::get_const('root_path').'admin/manage_user_groups.php'.registry::get_const('SID').'">'.registry::fetch('user')->lang('manage_user_groups').'</a>';
			break;

			case 'admin/manage_users':
				$page = $page = '<a href="'.registry::get_const('root_path').'admin/manage_users.php'.registry::get_const('SID').'">'.registry::fetch('user')->lang('manage_users').'</a>';
			break;

			case 'admin/upload':
				$page = $page = '<a href="'.registry::get_const('root_path').'admin/manage_users.php'.registry::get_const('SID').'">'.registry::fetch('user')->lang('file_manager').'</a>';
			break;

			case 'admin/manage_massmail':
				$page = $page = '<a href="'.registry::get_const('root_path').'admin/manage_massmail.php'.registry::get_const('SID').'">'.registry::fetch('user')->lang('massmail_send').'</a>';
			break;

			/***************** calendar Folder *****************/
			case 'calendar/addevent':
				$page = registry::fetch('user')->lang('calendars_add_title');
			break;

			case 'calendar/viewcalraid':
				$eventid = isset($params['eventid']) ? (int)$params['eventid'] : 0;
				$page = registry::fetch('user')->lang('calendar').': <a href="'.registry::get_const('root_path').'calendar/viewcalraid.php'.registry::get_const('SID').'&eventid='.$eventid.'">'.registry::register('plus_datahandler')->get('calendar_events', 'name', array($eventid)).'</a>';
			break;

			/***************** Root Folder *****************/
			case 'calendar':
				$page = '<a href="'.registry::get_const('root_path').'calendar.php'.registry::get_const('SID').'">'.registry::fetch('user')->lang('calendars').'</a>';
			break;

			case 'search':
				$page =  registry::fetch('user')->lang('search');
			break;

			case 'characters':
				$page = registry::fetch('user')->lang('manage_members_titl');
			break;

			case 'api':
			case 'exchange':
				$page = registry::fetch('user')->lang('viewing_exchange');
			break;

			case 'listcharacters':
				$page = '<a href="'.registry::get_const('root_path').'listcharacters.php'.registry::get_const('SID').'">'.registry::fetch('user')->lang('listmembers_title').'</a>';
			break;

			case 'listevents':
				$page = '<a href="'.registry::get_const('root_path').'listitems.php'.registry::get_const('SID').'">'.registry::fetch('user')->lang('listing_events').'</a>';
			break;

			case 'listitems':
				$page = '<a href="'.registry::get_const('root_path').'listitems.php'.registry::get_const('SID').'">'.registry::fetch('user')->lang('listitems_title').'</a>';
			break;

			case 'listraids':
				$page = '<a href="'.registry::get_const('root_path').'listraids.php'.registry::get_const('SID').'">'.registry::fetch('user')->lang('listing_raids').'</a>';
			break;

			case 'listusers':
				$page = '<a href="'.registry::get_const('root_path').'listusers.php'.registry::get_const('SID').'">'.registry::fetch('user')->lang('user_list').'</a>';
			break;

			case 'login':
				$page = '<a href="'.registry::get_const('root_path').'login.php'.registry::get_const('SID').'">'.registry::fetch('user')->lang('login_title').'</a>';
			break;

			case 'pages':
				$pageid = isset($params['page']) ? $params['page'] : 0;
				if (is_numeric($pageid)){
					$page = '<a href="'.registry::get_const('root_path').'pages.php'.registry::get_const('SID').'&page='.$id.'">'.registry::register('plus_datahandler')->get('pages', 'title', array($pageid)).'</a>';
				} else {
					$id = registry::register('plus_datahandler')->get('pages', 'alias_to_page', array($pageid));
					$page = '<a href="'.registry::get_const('root_path').'pages.php'.registry::get_const('SID').'&page='.$id.'">'.registry::register('plus_datahandler')->get('pages', 'title', array($id)).'</a>';
				}

			break;

			case 'register':
				$page = '<a href="'.registry::get_const('root_path').'register.php'.registry::get_const('SID').'">'.registry::fetch('user')->lang('register_title').'</a>';
			break;

			case 'roster':
				$page = '<a href="'.registry::get_const('root_path').'roster.php'.registry::get_const('SID').'">'.registry::fetch('user')->lang('menu_roster').'</a>';
			break;

			case 'settings':
				$page = registry::fetch('user')->lang('settings_title');
			break;

			case 'shop':
				$page = '<a href="'.registry::get_const('root_path').'shop.php'.registry::get_const('SID').'">'.registry::fetch('user')->lang('guild_shop').'</a>';
			break;

			case 'viewcharacter':
				$page = registry::fetch('user')->lang('viewing_member') . ': ';
				if (isset($params['member_id'])){
					$member_id = $params['member_id'];
					$page .= registry::register('plus_datahandler')->get('member', 'html_memberlink', array($member_id, registry::get_const('root_path').'viewcharacter.php'));
				}
			break;

			case 'viewevent':
				$page = registry::fetch('user')->lang('viewing_event') . ': ';
				if (isset($params['event_id'])){
					$event_id = $params['event_id'];
					$event_name = registry::register('plus_datahandler')->get('event', 'name', array($event_id));
					$page .= '<a href="../viewevent.php' . registry::get_const('SID') . '&amp;event_id=' . $event_id . '" target="_top">' . $event_name . '</a>';
				}
			break;

			case 'viewitem':
				$page = registry::fetch('user')->lang('viewing_item') . ': ';
				if (isset($params['i'])){
					$item_id = $params['i'];
					$item_name = registry::register('plus_datahandler')->get('item', 'name', array($item_id));
					$page .= '<a href="../viewitem.php' . registry::get_const('SID') . '&amp;i=' . $item_id. '" target="_top">' . $item_name . '</a>';
				}
			break;

			case 'viewnews':
				$page = '<a href="'.registry::get_const('root_path').'viewnews.php'.registry::get_const('SID').'">'.registry::fetch('user')->lang('viewing_news').'</a>';
			break;

			case 'viewraid':
				$page = registry::fetch('user')->lang('viewing_raid') . ': ';
				if (isset($params['r'])){
					$raid_id = $params['r'];
					$page .= registry::register('plus_datahandler')->get('raid', 'html_raidlink', array($raid_id, registry::get_const('root_path').'viewraid.php'));
				}
			break;

			case 'wrapper':
				$page = 'Wrapper';
				if (isset($params['id'])){
					$link_id = $params['id'];
					if (is_numeric($link_id)){
						$page.=': <a href="'.registry::get_const('root_path').'wrapper.php'.registry::get_const('SID').'&amp;id='.$link_id.'">'.sanitize(registry::register('plus_datahandler')->get('links', 'name', array($link_id))).'</a>';
					}else{

						switch($link_id){
							case 'lp':
							case 'board':
								$page.=': <a href="'.registry::get_const('root_path').'wrapper.php'.registry::get_const('SID').'&amp;id=board">'.registry::fetch('user')->lang('forum').'</a>';
						}
					}
				}
			break;

			default: if ($prefix == '') $page = htmlspecialchars($strPage);
		}
	}
	return $prefix.((isset($page)) ? $page : '');
}

/**
 * Copyright notice
 *
* ACCORDING TO THE CREATIVE COMMONS LICENSE (Attribution-Noncommercial-Share Alike 3.0),
* YOU ARE NOT PERMITTED TO RUN EQDKP-PLUS WITHOUT THIS COPYRIGHT NOTICE.
* CHANGING, REMOVING OR OBSTRUCTING IT IS PROHIBITED BY LAW!
*/
function gen_htmlhead_copyright($text){
	return preg_replace('/(<head[^>]*>)/',
			"$1\n\t<!--\n\n"
			."\tThis website is powered by EQDKP-PLUS Gamers CMS :: Licensed under Creative Commons by-nc-sa 3.0\n"
			."\tCopyright © 2006-" . date('Y') . " by EQDKP-PLUS Dev Team :: Plugins are copyright of their authors\n"
			."\tVisit the project website at ".EQDKP_PROJECT_URL." for more information\n\n"
			."\t//-->",
			$text, 1);
}

/**
 * Sanitize an imput
 *
 * @param		string		$input				Input to sanitize
 * @return		string
 */
function sanitize($input){
	return filter_var($input, FILTER_SANITIZE_STRING);
}

/**
 * unsanatize the input
 *
 * @param		string		$input				Input to reverse
 * @return		string
 */
function unsanitize($input){
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
function generate_pagination($url, $items, $per_page, $start, $start_variable='start'){

		$uri_symbol = ( strpos($url, '?') ) ? '&amp;' : '?';
		//On what page we are?
		$recent_page = (int)floor($start / $per_page) + 1;
		//Calculate total pages
		$total_pages = ceil($items / $per_page);
		//Return if we don't have at least 2 Pages
		if (!$items || $total_pages  < 2){
			return '';
		}

		$base_url = $url . $uri_symbol . $start_variable;
		//First Page
		$pagination = '<div class="pagination">';
		if ($recent_page == 1){
			$pagination .= '<span class="pagination_activ">1</span>';
		} else {
			$pagination .= '<a href="'.$base_url.'='.( ($recent_page - 2) * $per_page).'" title="'.registry::fetch('user')->lang('previous_page').'"><img src="'.registry::get_const('root_path').'images/arrows/left_arrow.png" border="0" alt="left"/></a>&nbsp;&nbsp;<a href="'.$url.'" class="pagination">1</a>';
		}

		//If total-pages <= 4 show all page-links
		if ($total_pages <= 4){
				$pagination .= ' ';
				for ( $i = 2; $i < $total_pages; $i++ ){
					if ($i == $recent_page){
						$pagination .= '<span class="pagination_activ">'.$i.'</span> ';
					} else {
						$pagination .= '<a href="'.$base_url.'='.( ($i - 1) * $per_page).'" title="'.registry::fetch('user')->lang('page').' '.$i.'" class="pagination">'.$i.'</a> ';
					}
					$pagination .= ' ';
				}
		//Don't show all page-links
		} else {
			$start_count = min(max(1, $recent_page - 5), $total_pages - 4);
			$end_count = max(min($total_pages, $recent_page + 5), 4);

			$pagination .= ( $start_count > 1 ) ? ' ... ' : ' ';

			for ( $i = $start_count + 1; $i < $end_count; $i++ ){
				if ($i == $recent_page){
					$pagination .= '<span class="pagination_activ">'.$i.'</span> ';
				} else {
					$pagination .= '<a href="'.$base_url.'='.( ($i - 1) * $per_page).'" title="'.registry::fetch('user')->lang('page').' '.$i.'" class="pagination">'.$i.'</a> ';
				}
			}
			$pagination .= ($end_count < $total_pages ) ? '  ...  ' : ' ';
		} //close else


		//Last Page
		if ($recent_page == $total_pages){
			$pagination .= '<span class="pagination_activ">'.$recent_page.'</span>';
		} else {
			$pagination .= '<a href="'.$base_url.'='.(($total_pages - 1) * $per_page) . '" class="pagination" title="'.registry::fetch('user')->lang('page').' '.$total_pages.'">'.$total_pages.'</a>&nbsp;&nbsp;<a href="'.$base_url.'='.($recent_page * $per_page).'" title="'.registry::fetch('user')->lang('next_page').'"><img src="'.registry::get_const('root_path').'images/arrows/right_arrow.png" border="0" alt="right"/></a>';
		}

	$pagination .= '</div>';
	return $pagination;
}
/*
 * Add the necessary Javascript for infotooltip to the template
 * @return true
 */
function infotooltip_js() {
	static $added = 0;
	if(!$added AND registry::register('config')->get('infotooltip_use')) {
		registry::register('template')->js_file(registry::get_const('root_path').'infotooltip/includes/jquery.infotooltip.js');
		$js = "$('.infotooltip').infotooltips(); var cached_itts = new Array();";
			$js .= "$('.infotooltip').tooltip({
						content: function(response) {
							var direct = $(this).attr('title').substr(0,1);
							var mytitle = $(this).attr('title');
							if(direct == 1) {
								$(this).attr('title', '');
								return '';
							}
							if (cached_itts['t_'+$(this).attr('title')] != undefined){
								return cached_itts['t_'+$(this).attr('title')];
							} else {
								var bla = $.get('".registry::get_const('root_path')."infotooltip/infotooltip_feed.php?direct=1&data='+$(this).attr('title'), response);
								bla.success(function(data) {
									cached_itts['t_'+mytitle] = $.trim(data);
								});
								return '<img src=\"".registry::get_const('root_path')."images/global/tooltip_loading.gif\" alt=\"".registry::fetch('user')->lang('lib_loading')."\" />';
							}
						},
						open: function() {
							var tooltip = $(this).tooltip('widget');
							tooltip.removeClass('ui-tooltip ui-widget ui-corner-all ui-widget-content');
							tooltip.addClass('ui-infotooltip');
							$(document).mousemove(function(event) {
								tooltip.position({
									my: 'left center',
									at: 'right center',
									offset: '50 25',
									of: event
								});
							})
							// trigger once to override element-relative positioning
							.mousemove();
						},
						close: function() {
							$(document).unbind('mousemove');
						}
					});";
		registry::register('template')->add_js($js, 'docready');
		registry::register('template')->css_file(registry::get_const('root_path').'infotooltip/includes/'.registry::register('config')->get('default_game').'.css');
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
 * @int $direct: 0: tooltip as tooltip, 1: direct display of tooltip
 * @int $onlyicon: >0: icon-size and only icon is displayed
 * @string $in_span: if you like to display something else, except itemname before loading tooltip
 * return @string
 */
function infotooltip($name='', $game_id=0, $lang=false, $direct=0, $onlyicon=0, $noicon=false, $char_name='', $server=false, $in_span=false, $class_add='', $slot=''){
	$server = ($server) ? $server : registry::register('config')->get("uc_servername");
	$lang = ($lang) ? $lang : registry::fetch('user')->lang('XML_LANG');
	$id = uniqid();
	$data = array('name' => $name, 'game_id' => $game_id, 'onlyicon' => $onlyicon, 'noicon' => $noicon, 'lang' => $lang, 'server' => $server, 'char_name'=> $char_name, 'slot' => $slot);
	$data = serialize($data);
	$direct = ($direct) ? 1 : 0;
	$str = '<span class="infotooltip '.$class_add.'" id="span_'.$id.'" title="'.$direct.urlencode(base64_encode($data)).'">';
	return $str.(($in_span !== false) ? $in_span : $name).'</span>';
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
						content: function(response) {
							mytitle = $(this).attr('title');
							if (cached_charTT['t_'+$(this).attr('title')] != undefined){
								return cached_charTT['t_'+$(this).attr('title')];
							} else {
								var bla = $.get('".registry::get_const('root_path')."exchange.php?out=chartooltip&charid='+$(this).attr('title'), response);
								bla.success(function(data) {
									cached_charTT['t_'+mytitle] = $.trim(data);
								});
								return '<img src=\"".registry::get_const('root_path')."images/global/tooltip_loading.gif\" alt=\"".registry::fetch('user')->lang('lib_loading')."\" />';
							}
						},
						open: function() {
							var tooltip = $(this).tooltip('widget');
							tooltip.removeClass('ui-tooltip ui-widget ui-corner-all ui-widget-content');
							tooltip.addClass('ui-infotooltip');
							$(document).mousemove(function(event) {
								tooltip.position({
									my: 'left center',
									at: 'right center',
									offset: '50 25',
									of: event
								});
							})
							// trigger once to override element-relative positioning
							.mousemove();
						},
						close: function() {
							$(document).unbind('mousemove');
						}
					});";
		registry::register('template')->add_js($js, 'docready');
		registry::register('template')->css_file(registry::get_const('root_path').'games/'.registry::register('config')->get('default_game').'/chartooltip/chartooltip.css');
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
		'MSG_TITLE'		=> (strlen($title)) ? $title : '&nbsp;',
		'MSG_TEXT'		=> (strlen($text)) ? $text  : '&nbsp;',
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
		case 'access_denied':	$message_class = 'errorbox';
								$icon = 'icon_stop';
		break;

		case 'info':			$message_class = 'infobox';
								$icon = 'icon_info';
		break;

		case 'error':			$message_class = 'errorbox';
								$icon = 'icon_false';
		break;

		case 'ok':				$message_class = 'greenbox';
								$icon = 'icon_ok';
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
			registry::register('jquery')->Validate('login', array(array('name' => 'username', 'value'=> registry::fetch('user')->lang('fv_required_user')), array('name'=>'password', 'value'=>registry::fetch('user')->lang('fv_required_password'))));
			registry::register('template')->add_js('document.login.username.focus();', 'docready');

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
			$return = search_in_array($child, $v);
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

function get_cookie($name){
	$cookie_name = registry::register('config')->get('cookie_name') . '_' . $name;
	return ( isset($_COOKIE[$cookie_name]) ) ? $_COOKIE[$cookie_name] : '';
}

function set_cookie($name, $cookie_data, $cookie_time){
	//dont set cookies if we dont have a cookie-name or cookie-path
	$cname = register('config')->get('cookie_name');
	$cpath = register('config')->get('cookie_path');
	if(empty($cname) || empty($cpath)) return;
	setcookie( $cname . '_' . $name, $cookie_data, $cookie_time, $cpath, register('config')->get('cookie_domain'));
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

function clean_username($strUsername){
	$strUsername = utf8_strtolower($strUsername);
	return $strUsername;
}

function xhtml_entity_decode($string){
	$string = html_entity_decode($string,  ENT_QUOTES, 'UTF-8');
	return $string;
}

function random_string($hash = false, $length = 10){
	$chars = array('a','A','b','B','c','C','d','D','e','E','f','F','g','G','h','H','i','I','j','J',
					'k','K','l','L','m','M','n','N','o','O','p','P','q','Q','r','R','s','S','t','T',
					'u','U','v','V','w','W','x','X','y','Y','z','Z','1','2','3','4','5','6','7','8',
					'9','0');

	$max_chars = count($chars) - 1;
	srand( (double) microtime()*1000000);

	$rand_str = '';
	for($i = 0; $i < $length; $i++){
		$rand_str = ( $i == 0 ) ? $chars[rand(0, $max_chars)] : $rand_str . $chars[rand(0, $max_chars)];
	}
	return ( $hash ) ? md5($rand_str) : $rand_str;
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
		return da($content);
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
		$content = "String Lenght=0";
	}

	echo "<table border=0>\n";
	echo "<tr>\n";
	echo "<td bgcolor='#0080C0'>";
	echo "<B>" . $content . "</B>";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
}

/**
 * Debug Function
 * gibt ein Array in Tabbelarischer Form aus.
 *
 * @param Array $TheArray
 * @return mixed
 */
function da($TheArray){ // Note: the function is recursive
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
	echo "</table>\n";
}

function MagicQuotesFix(){
	if (get_magic_quotes_gpc()) {
		function stripslashes_gpc(&$value)
		{
			$value = stripslashes($value);
		}
		array_walk_recursive($_GET, 'stripslashes_gpc');
		array_walk_recursive($_POST, 'stripslashes_gpc');
		array_walk_recursive($_COOKIE, 'stripslashes_gpc');
		array_walk_recursive($_REQUEST, 'stripslashes_gpc');
	}
}

// NOT USED; DEPRECATED, to be removed after RC
/**
 * Remove $GLOBALS if register_globals is on
 */
function RunGlobalsFix(){
	if((bool)@ini_get('register_globals')){
		$superglobals = array($_ENV, $_GET, $_POST, $_COOKIE, $_FILES, $_SERVER);
		if( isset($_SESSION) ){
			array_unshift($superglobals, $_SESSION);
		}
		$knownglobals = array(
			// Known PHP Reserved globals and superglobals:
			'_ENV',			'HTTP_ENV_VARS',
			'_GET',			'HTTP_GET_VARS',
			'_POST',		'HTTP_POST_VARS',
			'_COOKIE',		'HTTP_COOKIE_VARS',
			'_FILES',		'HTTP_FILES_VARS',
			'_SERVER',		'HTTP_SERVER_VARS',
			'_SESSION',		'HTTP_SESSION_VARS',
			'_REQUEST',

			// Global variables used by this code snippet:
			'superglobals',
			'knownglobals',
			'superglobal',
			'global',
			'void'
		);
		foreach( $superglobals as $superglobal ){
			foreach( $superglobal as $global => $void ){
				if( !in_array($global, $knownglobals) ){
					unset($GLOBALS[$global]);
				}
			}
		} // end forach
	} // end if register_globals = on
}
?>