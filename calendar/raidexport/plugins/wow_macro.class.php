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

$rpexport_plugin['wow_macro.class.php'] = array(
	'name'			=> 'WoW Macro',
	'function'		=> 'WoWMacroexport',
	'contact'		=> 'webmaster@wallenium.de',
	'version'		=> '2.0.0');

if(!function_exists('WoWMacroexport')){
	function WoWMacroexport($raid_id){
		$attendees	= registry::register('plus_datahandler')->get('calendar_raids_attendees', 'attendees', array($raid_id));
		$guests		= registry::register('plus_datahandler')->get('calendar_raids_guests', 'members', array($raid_id));
	
		$text .= "<textarea name='group".rand()."' cols='60' rows='10' onfocus='this.select()' readonly='readonly'>";
		if(is_array($attendees)){
			foreach($attendees as $attendeeid=>$data){
				$text .= "/i ".registry::register('plus_datahandler')->get('member', 'name', array($attendeeid))."\n";
			}
		}
		if(is_array($guests)){
			foreach($guests as $guestid=>$guestsdata){
				$text .= "/i ".$guestsdata['name']."\n";
			}
		}
	
		$text .= "</textarea>";
	
		$text .= '<br/>'.registry::fetch('user')->lang('rp_copypaste_ig')."</b>";
		return $text;
	}
}
?>