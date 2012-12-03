<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2010
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2006-2010 EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev$
 *
 * $Id$
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

// -----------------------------------------
// Version Data
// -----------------------------------------
define('REQUIRED_PHP_VERSION',	'5.2.0');
define('EQDKPPLUS_VERSION_BETA', TRUE);

if (isset($svn_rev)) {
	define('SVN_REV', $svn_rev);
}

/**
 * Determines if a folder path is valid. Ignores .svn, CVS, cache, etc.
 *
 * @param     string     $path             Path to check
 * @return    boolean
 */
function valid_folder($path){
	$ignore = array('.', '..', '.svn', 'CVS', 'cache', 'install', 'index.html', '.htaccess', '_images');
	if (isset($path)){
		if (!is_file($path) && !is_link($path) && !in_array(basename($path), $ignore)){
			return true;
		}
	}
	return false;
}

/**
* Strip multiple slashes
*
* @param $string	String input
*/
function stripmultslashes($string){
	$string = preg_replace("#(\\\){1,}(\"|\&quot;)#", '"', $string);
	$string = preg_replace("#(\\\){1,}(\'|\&\#039)#", "'", $string);
	return $string;
}

/**
* Undo Sanatize Tags
*
* @param $data	Data
*/
function undo_sanitize_tags($data){
	if ( is_array($data) ){
		foreach ( $data as $k => $v ){
			$data[$k] = undo_sanitize_tags($v);
		}
	}else{
		$data = str_replace('&lt;', '<', $data);
		$data = str_replace('&gt;', '>', $data);
	}
	return $data;
}

/**
* Applies htmlspecialchars to an array of data
*
* @deprec sanitize_tags
* @param $data
* @return array
*/
function htmlspecialchars_array($data){
	if ( is_array($data) ){
		foreach ( $data as $k => $v ){
			$data[$k] = ( is_array($v) ) ? htmlspecialchars_array($v) : htmlspecialchars($v);
		}
	}
	return $data;
}

/**
 * Personal ob_start function which uses gzip if enabled
 * This prevents the whitepage bugs/ wrong encodings if gzip is enabled
 */
function My_ob_start(){
	global $core;
	if ( $core->config['enable_gzip'] == '1' ){
		if ( (extension_loaded('zlib')) && (!headers_sent()) ){
			@ob_start('ob_gzhandler');
		}
	}else{
		@ob_start();
	}
}

/**
 * Rundet je nach Einstellungen im Eqdkp Plus Admin Menu die DKP Werte
 *
 * @param float $value
 * @return float
 */
function runden($value){
	global $core;
	$ret_val		= $value;
	$precision	= $core->config['pk_round_precision'];

	if (($precision < 0) or ($precision > 5) ){
		$precision = 2;
	}

	if ($core->config['pk_round_activate'] == "1"){
		$ret_val = round($value,$precision)	;
	}
	return $ret_val;
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
function da( $TheArray ){ // Note: the function is recursive
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

/**
* Sanatize Tags
*
* @param $data	Data
*/
function sanitize_tags($data){
	if ( is_array($data) ){
		foreach ( $data as $k => $v ){
			$data[$k] = sanitize_tags($v);
		}
	}else{
		$data = str_replace('<', '&lt;', $data);
		$data = str_replace('>', '&gt;', $data);
	}
	return $data;
}

function join_array($glue, $pieces, $dimension = 0){
	$rtn = array();
	foreach($pieces as $key => $value){
		if(isset($value[$dimension])){
			$rtn[] = $value;
		}
	}
	return join($glue, $rtn);
}

/**
 * Returns <kbd>true</kbd> if the string or array of string is encoded in UTF8.
 *
 * Example of use. If you want to know if a file is saved in UTF8 format :
 * <code> $array = file('one file.txt');
 * $isUTF8 = isUTF8($array);
 * if (!$isUTF8) --> we need to apply utf8_encode() to be in UTF8
 * else --> we are in UTF8 :)
 * @param mixed A string, or an array from a file() function.
 * @return boolean
 */
function isUTF8($string){
	if (is_array($string)){
		$enc = implode('', $string);
		return @!((ord($enc[0]) != 239) && (ord($enc[1]) != 187) && (ord($enc[2]) != 191));
	}else{
		return (utf8_encode(utf8_decode($string)) == $string);
	}
}

function validate(){
	global $eqdkp_root_path, $core;

	$keyfile_dat = $eqdkp_root_path.'/key.dat' ;
	$keyfile_php = $eqdkp_root_path.'/key.php' ;
	$return = true;

	if(file_exists($keyfile_dat) ){
		$handle = @fopen($keyfile_dat,"r");
		$keystring = @fread($handle, filesize($keyfile_dat));
	}elseif (file_exists($keyfile_php) ){
		include_once($keyfile_php);
	}

	if (strlen($keystring) > 1){
		$keystring = @base64_decode($keystring) ;
		$keystring = @gzuncompress($keystring) ;
		$keystring = @unserialize($keystring);
		$_data = $keystring ;
	}

	if (is_array($_data)){
		$_info = " | Type:".$_data['type']." | User:".$_data['kndNr'];

		switch ($_data['type']){
			case 0: $return = (substr($core->config['plus_version'],0,3) > $_data['version_allowed']) ? true : false ;	 break;	 //check server & version - 10
			case 1: $return = false ; break;	 //>50
			case 2: $return = false ; break;	 //>100
			case 3: $return = false ; break;	 //>100
			case 4: $return = false ; break;	 //>dev
			case 5: $return = false ; break;	 //>beta
		}
	}
	return $return;
}

/**
 * returns coloured names
 * @param array $neg
 * @param array $norm
 * @param array $pos
 * @return string
 */
function get_coloured_member_names($norm, $pos=array(), $neg=array()){
	global $pdh;
	$mems = array();
	if(is_array($neg)){
		foreach($neg as $member_id){
			$mems[] = "<span class='negative'>".$pdh->get('member', 'name', array($member_id))."</span>";
		}
	}
	if(is_array($norm)){
		foreach($norm as $member_id){
			$mems[] = $pdh->get('member', 'name', array($member_id));
		}
	}
	if(is_array($pos)){
		foreach($pos as $member_id){
			$mems[] = "<span class='positive'>".$pdh->get('member', 'name', array($member_id))."</span>";
		}
	}
	asort($mems);
	return implode(', ', $mems);
}

/**
 * returns comparison result
 * @param string $version1
 * @param string $version2
 * @return int
 */
function compareVersion($version1, $version2){
  $result = 0;
  $match1 = explode('.', $version1);
  $match2 = explode('.', $version2);
  $int1 = sprintf( '%d%02d%02d%02d', $match1[0], $match1[1], intval($match1[2]),intval($match1[3]));
  $int2 = sprintf( '%d%02d%02d%02d', $match2[0], $match2[1], intval($match2[2]), intval($match2[3]) );

  if($int1 < $int2){ $result = -1;}
  if($int1 > $int2){ $result = 1;}
  return $result;
}

function RunGlobalsFix(){
	if( (bool)@ini_get('register_globals') ){
		$superglobals = array($_ENV, $_GET, $_POST, $_COOKIE, $_FILES, $_SERVER);
		if( isset($_SESSION) ){
			array_unshift($superglobals, $_SESSION);
		}
		$knownglobals = array(
			// Known PHP Reserved globals and superglobals:
			'_ENV',       'HTTP_ENV_VARS',
			'_GET',       'HTTP_GET_VARS',
			'_POST',    	'HTTP_POST_VARS',
			'_COOKIE',    'HTTP_COOKIE_VARS',
			'_FILES',    	'HTTP_FILES_VARS',
			'_SERVER',    'HTTP_SERVER_VARS',
			'_SESSION',   'HTTP_SESSION_VARS',
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

/**
 * returns sorted ids			example: sorting by name
 * @param array $tosort				array($id => array('name' => name))
 * @param array $order				array(0, 0)
 * @param array $sort_order			array(0 => 'name')
 * @return array
 */
function get_sortedids($tosort, $order, $sort_order){
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
 * @param     string     $url          URL to redirect to
 * @param     bool       $return       Whether to return the generated redirect url (true) or just redirect to the page (false)
 * @param			bool			 $extern			 Is it an external link (other server) or an internal link?
 * @return    mixed                    null, else the parsed redirect url if return is true.
 */
function redirect($url, $return=false, $extern=false){
	global $core;
	$out = ((!$extern) ? $core->BuildLink(). str_replace('&amp;', '&', $url) : $url);
	if ($return){
		return $out;
	}else{
		header('Location: ' . $out);exit;
	}
}

/**
 * Keep a consistent page title across the entire application
 *
 * @param     string     $title            The dynamic part of the page title, appears before " - Guild Name DKP"
 * @return    string
 */
function page_title($title = ''){
	global $core, $user;
	$pt_prefix		= (defined('IN_ADMIN')) ? $user->lang['admin_title_prefix'] : $user->lang['title_prefix'];
	$main_title		= sprintf($pt_prefix, $core->config['guildtag'], $core->config['dkp_name']);
	return sanitize((( $title != '' ) ? $title.' - ' : '').$main_title, TAG);
}

/**
 * Returns the appropriate CSS class to use based on a number's range
 *
 * @param     string     $item             The number
 * @param     boolean    $percentage       Treat the number like a percentage?
 * @return    mixed                        CSS Class / false
*/
function color_item($item, $percentage = false){
	global $core;
	if (!is_numeric($item)){
		return false;
	}
	$class		= 'neutral';
	$max_val	= ($core->config['pk_max_percvalue']) ? $core->config['pk_max_percvalue'] : 67;
	$min_val	= ($core->config['pk_min_percvalue']) ? $core->config['pk_min_percvalue'] : 34;

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
 * Resolve the User Browser
 *
 * @param string $member
 * @return string
 */
function resolve_browser($string){
	global $eqdkp_root_path, $jquery, $html;

	if( preg_match("/opera/i",$string))
	{
	return $html->html_tooltip($string, "<img src=\"".$eqdkp_root_path."images/glyphs/browser/opera_icon.png\">");
	}
	else if( preg_match("/msie/i",$string) )
	{
	return $html->html_tooltip($string, "<img src=\"".$eqdkp_root_path."images/glyphs/browser/ie_icon.png\">");
	}
	else if( preg_match("/chrome/i", $string) )
	{
	return $html->html_tooltip($string, "<img src=\"".$eqdkp_root_path."images/glyphs/browser/chrome_icon.png\">");
	}
	else if( preg_match("/konqueror/i",$string) )
	{
	return $html->html_tooltip($string, "<img src=\"".$eqdkp_root_path."images/glyphs/browser/konqueror_icon.png\">");
	}
	else if( preg_match("/safari/i",$string) )
	{
	return $html->html_tooltip($string, "<img src=\"".$eqdkp_root_path."images/glyphs/browser/safari_icon.png\">");
	}
	else if( preg_match("/lynx/i",$string) )
	{
	return $html->html_tooltip($string, "Lynx");
	}
	else if( preg_match("/netscape6/i",$string) )
	{
	return $html->html_tooltip($string, "<img src=\"".$eqdkp_root_path."images/glyphs/browser/netscape_icon.png\">");
	}
	else if( preg_match("/mozilla/i",$string) )
	{
	return $html->html_tooltip($string, "<img src=\"".$eqdkp_root_path."images/glyphs/browser/firefox_icon.png\">");
	}
	else if( preg_match("/w3m/i",$string) )
	{
	return $html->html_tooltip($string, "w3m");
	}
	else
	{
		return $html->html_tooltip($string, "<img src=\"".$eqdkp_root_path."images/glyphs/help_off.png\">");
	}
}

/**
 * Resolve the EQDKP Page the user is surfing on..
 *
 * @param string $member
 * @return string
 */
function resolve_eqdkp_page($page){
	global $db, $core, $user, $SID, $pdh, $eqdkp_root_path;

	$matches = explode('&', $page);

	if (!empty($matches[0])){
		$filename = pathinfo($matches[0]);
		switch ($filename['filename']){
			/***************** Admin *****************/
			case 'addadj':
				$page = $user->lang['adding_groupadj'];
				if ( (!empty($matches[1])) && (preg_match('/^a=([0-9]{1,})/', $matches[1], $adjustment_id)) ){
					$page  = $user->lang['editing_groupadj'] . ': ';
					$page .= '<a href="addadj.php' . $SID . '&amp;a=' . $adjustment_id[1] . '">' . $adjustment_id[1] . '</a>';
				}
			break;
			case 'additem':
				$page = $user->lang['adding_item'];
				if ( (!empty($matches[1])) && (preg_match('/^i=([0-9]{1,})/', $matches[1], $item_id)) ){
					$item_name = $pdh->get('item', 'name', array($item_id[1]));
					$page  = $user->lang['editing_item'] . ': ';
					$page .= '<a href="additem.php' . $SID . '&amp;i=' . $item_id[1] . '">' . $item_name . '</a>';
				}
			break;
			case 'addnews':
				$page = $user->lang['adding_news'];
				if ( (!empty($matches[1])) && (preg_match('/^n=([0-9]{1,})/', $matches[1], $news_id)) ){
					$news_name = get_news_name($news_id[1]);
					$page  = $user->lang['editing_item'] . ': ';
					$page .= '<a href="addnews.php' . $SID . '&amp;n=' . $news_id[1] . '">' . $news_name . '</a>';
				}
			break;
			case 'addraid':
				$page = $user->lang['adding_raid'];
				if ( (!empty($matches[1])) && (preg_match('/^r=([0-9]{1,})/', $matches[1], $raid_id)) ){
					$raid_name = $pdh->get('raid', 'event_name', array($raid_id[1]));
					$page  = $user->lang['editing_raid'] . ': ';
					$page .= '<a href="addraid.php' . $SID . '&amp;r=' . $raid_id[1] . '">' . $raid_name . '</a>';
				}
			break;
			case 'config':
				$page = $user->lang['managing_config'];
			break;
			case 'index':
				$page = $user->lang['viewing_admin_index'];
			break;
			case 'logs':
				$page = $user->lang['viewing_logs'];
			break;
			case 'manage_members':
				$page = $user->lang['managing_members'];
			break;
			case 'manage_users':
				$page = $user->lang['managing_users'];
			break;
			case 'mysql_info':
				$page = $user->lang['viewing_mysql_info'];
			break;
			case 'plugins':
				$page = $user->lang['managing_plugins'];
			break;
			case 'styles':
				$page = $user->lang['managing_styles'];
			break;

			/***************** Listing *****************/
			case 'listevents':
				$page = $user->lang['listing_events'];
			break;
			case 'listitems':
				if ( (empty($matches[1])) || ($matches[1] == 'values') ){
					$page = $user->lang['listing_itemvals'];
				}else{
					$page = $user->lang['listing_itemhist'];
				}
			break;
			case 'listmembers':
				$page = $user->lang['listing_members'];
			break;
			case 'listraids':
				$page = $user->lang['listing_raids'];
			break;

			/***************** Viewing *****************/
			case 'viewevent':
				$page = $user->lang['viewing_event'] . ': ';
				if (!empty($matches[1])){
					preg_match('/^e=([0-9]{1,})/', $matches[1], $event_id);
					$event_name = $pdh->get('event', 'name', array($event_id[1]));
					$page .= '<a href="../viewevent.php' . $SID . '&amp;e=' . $event_id[1] . '" target="_top">' . $event_name . '</a>';
				}
			break;
			case 'viewitem':
				$page = $user->lang['viewing_item'] . ': ';
				if (!empty($matches[1])){
					preg_match('/^i=([0-9]{1,})/', $matches[1], $item_id);
					$item_name = $pdh->get('item', 'name', array($item_id[1]));
					$page .= '<a href="../viewitem.php' . $SID . '&amp;i=' . $item_id[1] . '" target="_top">' . $item_name . '</a>';
				}
			break;
			case 'viewnews':
				$page = $user->lang['viewing_news'];
			break;
			case 'viewcharacter':
				$page = $user->lang['viewing_member'] . ': ';
				if (!empty($matches[1])){
					preg_match('/^name=([A-Za-z]{1,})/', $matches[1], $member_name);
					$page .= '<a href="../viewcharacter.php' . $SID . '&amp;name=' . $member_name[1] . '" target="_top">' . $member_name[1] . '</a>';
				}
			break;
			case 'viewraid':
				$page = $user->lang['viewing_raid'] . ': ';
				if (!empty($matches[1])){
					preg_match('/^r=([0-9]{1,})/', $matches[1], $raid_id);
					$raid_name = $pdh->get('raid', 'name', array($raid_id[1]));
					$page .= '<a href="../viewraid.php' . $SID . '&amp;r=' . $raid_id[1] . '" target="_top">' . $raid_name . '</a>';
				}
			break;
			case 'maintenance':
				$page = '<a href="'.$eqdkp_root_path.'maintenance/task_manager.php">'.$user->lang['maintenance'].'</a>';
			break;
			case 'exchange':
				$page = $user->lang['viewing_exchange'];
			break;
			case 'wrapper':
				$page = 'Wrapper';
				if (!empty($matches[0])){
					preg_match('/id=([A-Za-z0-9]{1,})/', $matches[0], $link_id);
					if ($link_id[1]){
						if (is_numeric($link_id[1])){
							$page.=': <a href="'.$eqdkp_root_path.'wrapper.php?id='.$link_id[1].'">'.sanitize($pdh->get('links', 'name', array($link_id[1]))).'</a>';
						}else{

							switch($link_id[1]){
								case 'lp':
								case 'board':
									$page.=': <a href="'.$eqdkp_root_path.'wrapper.php?id=board">'.$user->lang['forum'].'</a>';
							}
						}
					}
					$page .= '<a href="../viewraid.php' . $SID . '&amp;r=' . $raid_id[1] . '" target="_top">' . $raid_name . '</a>';
				}
			break;


		}
	}
	return $page;
}

/**
 * Resolve the saved eqdkp log entries
 *
 * @param string $member
 * @return string
 */
function resolve_logs($row){
	global $user, $logs, $pdh;
	eval($row['log_value']);
	$row['username'] = $pdh->get('user', 'name', array($row['user_id']));
	switch ( $row['log_tag'] ){
		case '{L_ACTION_EVENT_ADDED}':
			$logline = sprintf($user->lang['vlog_event_added'],      $row['username'], $log_action['{L_NAME}'], $log_action['{L_VALUE}']);
		break;
		case '{L_ACTION_EVENT_UPDATED}':
			$logline = sprintf($user->lang['vlog_event_updated'],    $row['username'], $log_action['{L_NAME_BEFORE}']);
		break;
		case '{L_ACTION_EVENT_DELETED}':
			$logline = sprintf($user->lang['vlog_event_deleted'],    $row['username'], $log_action['{L_NAME}']);
		break;
		case '{L_ACTION_GROUPADJ_ADDED}':
			$logline = sprintf($user->lang['vlog_groupadj_added'],   $row['username'], $log_action['{L_ADJUSTMENT}']);
		break;
		case '{L_ACTION_GROUPADJ_UPDATED}':
			$logline = sprintf($user->lang['vlog_groupadj_updated'], $row['username'], $log_action['{L_ADJUSTMENT_BEFORE}']);
		break;
		case '{L_ACTION_GROUPADJ_DELETED}':
			$logline = sprintf($user->lang['vlog_groupadj_deleted'], $row['username'], $log_action['{L_ADJUSTMENT}']);
		break;
		case '{L_ACTION_HISTORY_TRANSFER}':
			$logline = sprintf($user->lang['vlog_history_transfer'], $row['username'], $log_action['{L_FROM}'], $log_action['{L_TO}']);
		break;
		case '{L_ACTION_INDIVADJ_ADDED}':
			$logline = sprintf($user->lang['vlog_indivadj_added'],   $row['username'], $log_action['{L_ADJUSTMENT}'], count(explode(', ', $log_action['{L_MEMBERS}'])));
		break;
		case '{L_ACTION_INDIVADJ_UPDATED}':
			$logline = sprintf($user->lang['vlog_indivadj_updated'], $row['username'], $log_action['{L_ADJUSTMENT_BEFORE}'], $log_action['{L_MEMBERS_BEFORE}']);
		break;
		case '{L_ACTION_INDIVADJ_DELETED}':
			$logline = sprintf($user->lang['vlog_indivadj_deleted'], $row['username'], $log_action['{L_ADJUSTMENT}'], $log_action['{L_MEMBERS}']);
		break;
		case '{L_ACTION_ITEM_ADDED}':
			$logline = sprintf($user->lang['vlog_item_added'],       $row['username'], $log_action['{L_NAME}'], count(explode(', ', $log_action['{L_BUYERS}'])), $log_action['{L_VALUE}']);
		break;
		case '{L_ACTION_ITEM_UPDATED}':
			$logline = sprintf($user->lang['vlog_item_updated'],     $row['username'], $log_action['{L_NAME_BEFORE}'], count(explode(', ', $log_action['{L_BUYERS_BEFORE}'])));
		break;
		case '{L_ACTION_ITEM_DELETED}':
			$logline = sprintf($user->lang['vlog_item_deleted'],     $row['username'], $log_action['{L_NAME}'], count(explode(', ', $log_action['{L_BUYERS}'])));
		break;
		case '{L_ACTION_MEMBER_ADDED}':
			$logline = sprintf($user->lang['vlog_member_added'],     $row['username'], $log_action['{L_NAME}']);
		break;
		case '{L_ACTION_MEMBER_UPDATED}':
			$logline = sprintf($user->lang['vlog_member_updated'],   $row['username'], $log_action['{L_NAME_BEFORE}']);
		break;
		case '{L_ACTION_MEMBER_DELETED}':
			$logline = sprintf($user->lang['vlog_member_deleted'],   $row['username'], $log_action['{L_NAME}']);
		break;
		case '{L_ACTION_NEWS_ADDED}':
			$logline = sprintf($user->lang['vlog_news_added'],       $row['username'], $log_action['{L_HEADLINE}']);
		break;
		case '{L_ACTION_NEWS_UPDATED}':
			$logline = sprintf($user->lang['vlog_news_updated'],     $row['username'], $log_action['{L_HEADLINE_BEFORE}']);
		break;
		case '{L_ACTION_NEWS_DELETED}':
			$logline = sprintf($user->lang['vlog_news_deleted'],     $row['username'], $log_action['{L_HEADLINE}']);
		break;
		case '{L_ACTION_RAID_ADDED}':
			$logline = sprintf($user->lang['vlog_raid_added'],       $row['username'], $log_action['{L_EVENT}']);
		break;
		case '{L_ACTION_RAID_UPDATED}':
			$logline = sprintf($user->lang['vlog_raid_updated'],     $row['username'], $log_action['{L_EVENT_BEFORE}']);
		break;
		case '{L_ACTION_RAID_DELETED}':
			$logline = sprintf($user->lang['vlog_raid_deleted'],     $row['username'], $log_action['{L_EVENT}']);
		break;
		case '{L_ACTION_LOGS_DELETED}':
			$logline = sprintf($user->lang['vlog_logs_deleted'],       $row['username'], $log_action['{L_HEADLINE}']);
		break;
		case '{L_ACTION_USER_ADDED}':
			$logline = sprintf($user->lang['vlog_user_added'],     $row['username'], $log_action['{L_USER}']);
		break;
		case '{L_ACTION_USER_UPDATED}':
			$logline = sprintf($user->lang['vlog_user_updated'],   $row['username'], $log_action['{L_USER}']);
		break;
		case '{L_ACTION_USER_DELETED}':
			$logline = sprintf($user->lang['vlog_user_deleted'],   $row['username'], $log_action['{L_USER}']);
		break;
		case '{L_ACTION_MULTIDKP_ADDED}':
			$logline = sprintf($user->lang['vlog_multidkp_added'],     $row['username'], $log_action['{L_MULTINAME}']);
		break;
		case '{L_ACTION_MULTIDKP_UPDATED}':
			$logline = sprintf($user->lang['vlog_multidkp_updated'],   $row['username'], $log_action['{L_MULTINAME}']);
		break;
		case '{L_ACTION_MULTIDKP_DELETED}':
			$logline = sprintf($user->lang['vlog_multidkp_deleted'],   $row['username'], $log_action['{L_MULTINAME}']);
		break;
		default: $logline = $logs->lang_replace($row['log_tag']);
	}
	unset($log_action);
	return $logline;
}

/**
 * Checks if a POST or a database field value exists;
 * Use the POST field value or if not available use the DB field!
 *
 * @param    string  $post_field POST field name
 * @param    array   $db_row     Array of DB values
 * @param    string  $db_field   DB field name
 * @return   string
 */
function post_or_db($fieldname, $data_row = array(), $data_field = ''){
	global $in;
	// Check if there's a database row..
	if (@sizeof($data_row) > 0 ){
		if ( $data_field == '' ){
			$data_field = $fieldname;
		}
		$database_value = $data_row[$data_field];
	}else{
		$database_value = '';
	}
	return ($in->get($fieldname)) ? $in->get($fieldname) : $database_value;
}

/**
 * Sanitize an imput
 *
 * @param     string     $input            Input to sanitize
 * @return    string
 */
function sanitize($input){
	return filter_var($input, FILTER_SANITIZE_STRING);
}

/**
 * unsanatize the input
 *
 * @param     string     $input            Input to reverse
 * @return    string
 */
function unsanitize($input){
	return htmlspecialchars_decode($input, ENT_QUOTES);
}

/**
 * Returns a string with a list of available pages
 *
 * @param     string     $url         			The starting URL for each page link
 * @param     int        $items        			The number of items we're paging through
 * @param     int        $per_page         How many items to display per page
 * @param     int        $start_item       Which number are we starting on
 * @param     string     $start_variable   In case you need to call your _GET var something other than 'start'
 * @return    string
 */
function generate_pagination($url, $items, $per_page, $start, $start_variable='start'){
	global $eqdkp_root_path, $user;

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
			$pagination .= '<a href="'.$base_url.'='.( ($recent_page - 2) * $per_page).'" title="'.$user->lang['previous_page'].'"><img src="'.$eqdkp_root_path.'images/arrows/left_arrow.png" border="0"></a>&nbsp;&nbsp;<a href="'.$url.'" class="pagination">1</a>';
		}

		//If total-pages < 4 show all page-links
		if ($total_pages < 4){
				$pagination .= ' ';
				for ( $i = 2; $i < $total_pages; $i++ ){
					if ($i == $recent_page){
						$pagination .= '<span class="pagination_activ">'.$i.'</span> ';
					} else {
						$pagination .= '<a href="'.$base_url.'='.( ($i - 1) * $per_page).'" title="'.$user->lang['page'].' '.$i.'" class="pagination">'.$i.'</a> ';
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
					$pagination .= '<a href="'.$base_url.'='.( ($i - 1) * $per_page).'" title="'.$user->lang['page'].' '.$i.'" class="pagination">'.$i.'</a> ';
				}
			}
			$pagination .= ($end_count < $total_pages ) ? '  ...  ' : ' ';
		} //close else


		//Last Page
		if ($recent_page == $total_pages){
			$pagination .= '<span class="pagination_activ">'.$recent_page.'</span>';
		} else {
			$pagination .= '<a href="'.$base_url.'='.(($total_pages - 1) * $per_page) . '" class="pagination" title="'.$user->lang['page'].' '.$total_pages.'">'.$total_pages.'</a>&nbsp;&nbsp;<a href="'.$base_url.'='.($recent_page * $per_page).'" title="'.$user->lang['next_page'].'"><img src="'.$eqdkp_root_path.'images/arrows/right_arrow.png" border="0"></a>';
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
    global $tpl, $eqdkp_root_path, $core, $user;
	if(!$added AND $core->config['infotooltip_use']) {
		$tpl->js_file($eqdkp_root_path.'infotooltip/includes/jquery.infotooltip.js');
		$js = "$(document).ready(function(){
			$('.infotooltip').infotooltips();";
		$js .= "$('.infotooltip').tooltip({
						tooltipClass: 'ui-tt-transparent-tooltip',
						content: function(response) {
							if($(this).attr('direct') == 1) {
								$(this).attr('title', '');
								return '';
							}
							$.get('".$eqdkp_root_path."infotooltip/infotooltip_feed.php?direct=1&name='+$(this).attr('title')+'&lang='+$(this).attr('lang')+'&game_id='+$(this).attr('game_id')+'&server='+$(this).attr('server')+'&cname='+$(this).attr('cname')+'&slotid='+$(this).attr('slotid'), response);
							return '".$user->lang['lib_loading']."';
						},
						open: function() {
							var tooltip = $(this).tooltip('widget');
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
		$tpl->add_js($js."});");
		$tpl->css_file($eqdkp_root_path.'infotooltip/includes/'.$core->config['default_game'].'.css');
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
function infotooltip($name='', $game_id=0, $lang=false, $direct=0, $onlyicon=0, $char_name='', $server=false, $slot=false, $in_span=false) {
	global $core, $user;
	$server = ($server) ? $server : $core->config["uc_servername"];
	$lang = ($lang) ? $lang : $user->lang['XML_LANG'];
	$str = '<span class="infotooltip" id="span_'.uniqid().'" use_game_id="'.(($game_id > 0) ? 1 : 0).'" game_id="'.$game_id.'" direct="'.$direct;
	$str .= '" onlyicon="'.$onlyicon.'" lang="'.$lang.'" server="'.$server.'" cname="'.$char_name.'" slotid="'.$slot.'" title="'.base64_encode($name).'">';
	return $str.(($in_span) ? $in_span : $name).'</span>';
}

/**
 * Outputs a message with debugging info if needed and ends output.
 * Clean replacement for die()
 *
 * @param     string     $text             Message text
 * @param     string     $title            Message title
 * @param     string     $file             File name
 * @param     int        $line             File line
 * @param     string     $sql              SQL code
 * @param     array      $buttons          Buttons
 */
function message_die($text = '', $title = '', $debug_file = '', $debug_line = '', $debug_sql = '', $buttons = '', $login_form = false){
	global $db, $tpl, $core, $user, $pm, $eqdkp_root_path, $jquery;

	$error_message = "";
	$error_sql = $db->error();

	if (($core->config['pk_debug'] == '1' || $core->config['pk_debug'] == '3') && $db->error_die && $error_sql['code'] > 0){
		//SQL-Error
		$error_message .= (strlen($error_sql['message'])) ? '<b>SQL-Error:</b> '.$error_sql['message'].'<br />' : '';
		//Error-Code
		$error_message .= (strlen($error_sql['code'])) ? '<b>SQL-Error Code:</b> '.$error_sql['code'].'<br />' : '';
		//Now the given parameters
		$error_message .= (strlen($debug_sql)) ? '<b>SQL:</b> '.$debug_sql.'<br />' : '';

		if (strlen($debug_file) && strlen($debug_line)){
			$error_message .= '<b>File:</b> '.$debug_file.'<br />';
			$error_message .= '<b>Line:</b> '.$debug_line.'<br />';
		}

		if (strlen($error_message)){
			$text .= '<br /><br /><b>Debug Mode</b><br />'.$error_message;
		}

	}

	//Output if template-class is not available
	if ( !is_object($tpl) ){
		die($error_message);
	}

	$tpl->assign_vars(array(
		'MSG_TITLE'  => (strlen($title)) ? $title : '&nbsp;',
		'MSG_TEXT'   => (strlen($text)) ? $text  : '&nbsp;',
	));

	//Buttons
	if (is_array($buttons)){

		$tpl->assign_vars(array(
			'S_BUTTON'		=> true,
			'BU_FORM'		=> ($button['form_action'] != "") ? '<form action="'.$button['form_action'].'" method="post" name="post">' : '',
			'BU_FORM_END'	=> ($button['form_action'] != "") ? '</form>' : '',
			'BU_VALUE'		=> $button['value'],
			'BU_ONCLICK'	=> ($button['onclick'] != "") ? ' onClick="'.$button['onclick'].'"' : '',
		));
	}

	//Page-Header
	if (function_exists('page_title')){
		$page_title = page_title(((strlen($title)) ? $title : 'Message'));
	} elseif (is_object($user)) {
		$page_title = $user->lang['message_title'];
	} else {
		$page_title = 'Message';
	}

	$core->set_vars(array(
		'header_format' 		=> $core->header_format,
		'page_title'        => $page_title,
		'template_file'     => 'message.html')
	);

	//Login-Form
	if ($login_form){
		if ($user->data['user_id'] == ANONYMOUS){
			$jquery->Validate('login', array(array('name' => 'username', 'value'=> $user->lang['jqfv_required_user']), array('name'=>'password', 'value'=>$user->lang['jqfv_required_password'])));
			$tpl->add_js('$(document).ready(function() { document.login.username.focus()}) ');

			$redirect = $user->request_page;

			$tpl->assign_vars(array(
						'S_LOGIN' => true,
						'L_LOGIN'             => $user->lang['login']  ,
						'S_BRIDGE_INFO'				=>  ($core->config['pk_bridge_cms_deac_reg'] ==1) ? true : false,
						'L_BRIDGE_INFO'				=> $user->lang['login_bridge_notice'],
						'L_USERNAME'          => $user->lang['username'],
						'L_PASSWORD'          => $user->lang['password'],
						'L_REMEMBER_PASSWORD' => $user->lang['remember_password'],
						'L_RESEND_ACTIVATION' => $user->lang['get_new_activation_mail'],
						'S_USER_ACTIVATION'		=> ($core->config['account_activation'] == 1) ? true : false,

						'REDIRECT'						=> ( isset($redirect) ) ? '<input type="hidden" name="redirect" value="'.sanitize($redirect).'">' : '',
						'L_LOST_PASSWORD'     => $user->lang['lost_password'],

				));
		}

	}

	$core->page_header();
	$core->page_tail();
	exit;
}

/**
* Outputs a message asking the user if they're sure they want to delete something
*
* @param $confirm_text Confirm message
* @param $uri_parameter URI_RAID, 'name', etc.
* @param $parameter_value Value of the parameter
* @param $form_action Form action
*/
function confirm_delete($confirm_text, $uri_parameter, $parameter_value, $form_action = ''){
	global $db, $core, $eqdkp_root_path, $tpl, $user;

	if (!defined('HEADER_INC')){
		$core->set_vars(array(
			'header_format'			=> $core->header_format,
			'page_title'			=> (strlen($core->page_title)) ? $core->page_title : $user->lang['delete_confirmation'],
			'template_file'			=> 'admin/confirm_delete.html',
		));
		$core->page_header();
	}

	$tpl->assign_vars(array(
		//Form
		'F_CONFIRM_ACTION'			=> (strlen($form_action) ) ? $form_action : $_SERVER['PHP_SELF'],
		//Params
		'URI_PARAMETER'				=> sanitize($uri_parameter),
		'PARAMETER_VALUE'			=> sanitize($parameter_value),
		//Language
		'L_BUTTON_YES'				=> $user->lang['yes'],
		'L_BUTTON_NO'				=> $user->lang['no'],
		'L_DELETE_CONFIRMATION'		=> $user->lang['delete_confirmation'],
		'L_CONFIRM_TEXT'			=> $confirm_text,
	));

	$core->page_tail();
	exit;
}

function createBar($value, $max, $widht, $text, $color=false )
{
	global $eqdkp_root_path;
	$proz = ($max>0) ? ($value / $max) * 100 : 0 ;

	if ($color)
	{
		$html = "
			<div class='plus_bar_$color' style='width: ".$widht."px;'><p>$max</p></div>";
	}else{
	$html = "<div class='plus_bar-container' style='width: ".$widht."px;'>
				<img class='ieimg' src='images/pixel.gif' width='1' height='1'>
				<b style='width: ".round($proz)."%;'></b>
				<span style='width: ".$widht."px;'>".$text." ".$value."/".$max."</span>
			</div>";
	}
	return $html;
}

function createMultiBar($value_array, $width)
{
	if (is_array($value_array))
	{
		$out = '<div class="plus_multibar" style="width: '.$width.'px;">';
		foreach ($value_array as $bar)
		{
			$proz = ($bar['max'] > 0) ? ($bar['value'] / $bar['max']) * 100 : 0 ;
			$color = ($bar['color']) ? 'plus_multibar_'.$bar['max'] : 'plus_multibar-container';
			$out .= '<div class="plus_multibar-container" style="width: '.$width.'px;">
						<img class="ieimg" src="images/pixel.gif" height="1" width="1">
						<b style="width: '.round($proz).'%;"></b>
						<span style="width: '.$width.'px;">'.$bar['text'].' '.$bar['value'].'/'.$bar['max'].'</span>
					</div>';
		}
		$out .= '</div>';
		return $out;
	}
}

function sdir( $path='.', $mask='*', $strip='', $nocache=0 ){
	static $dir = array(); // cache result in memory
	if ( !isset($dir[$path]) || $nocache) {
		$dir[$path] = scandir($path);
	}
	foreach ($dir[$path] as $i=>$entry) {
		if ($entry!='.' && $entry!='..' && fnmatch($mask, $entry) ) {
			$sdir[] = ($strip) ? str_replace($strip, '', $entry) : $entry;
		}
	}
	return ($sdir);
}

// this is to make the fmatch working for windows php less 5.3
if(!function_exists('fnmatch')) {
	function fnmatch($pattern, $string) {
		return @preg_match('/^' . strtr(addcslashes($pattern, '/\\.+^$(){}=!<>|'),array('*' => '.*', '?' => '.?')) . '$/i', $string);
	}
}
if (!function_exists('mb_ucfirst')) {
	function mb_ucfirst($string, $e='utf-8'){
		if(function_exists('mb_substr') && function_exists('mb_strtoupper')){
			return mb_strtoupper(mb_substr($string, 0, 1, $e), $e) . mb_substr($string, 1, intval(mb_strlen($string, $e)), $e);
		}else{
			return ucfirst($string);
		}
	}
}
//////////////////////////////////////////////////////////////////////
// TO BE REWORKED OR REMOVED
//////////////////////////////////////////////////////////////////////

function Raidcount(){
	global $db ;
	$sql = "SELECT count(raid_id) from __raids";
	$count = $db->query_first($sql);

	if ($count > 1 ){
		return	true ;
	}
}

function decode_unicode($string) {
	$string = str_replace('\u00df', 'ß', $string);
	$string = str_replace('\u00e4', 'ä', $string);
	$string = str_replace('\u00fc', 'ü', $string);
	$string = str_replace('\u00f6', 'ö', $string);
	$string = str_replace('\u00c4', 'Ä', $string);
	$string = str_replace('\u00dc', 'Ü', $string);
	$string = str_replace('\u00d6', 'Ö', $string);
	return $string;
}

function get_cookie($name){
		global $core;

		$cookie_name = $core->config['cookie_name'] . '_' . $name;
		return ( isset($_COOKIE[$cookie_name]) ) ? $_COOKIE[$cookie_name] : '';
}

function set_cookie($name, $cookie_data, $cookie_time){
		global $core;

		setcookie($core->config['cookie_name'] . '_' . $name, $cookie_data, $cookie_time, $core->config['cookie_path'], $core->config['cookie_domain']);
}
?>