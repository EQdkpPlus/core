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

		$a_json	= array();
		foreach($attendees as $id_attendees=>$d_attendees){
			$a_json[]	= array(
				'name'		=> registry::register('plus_datahandler')->get('member', 'name', array($id_attendees)),
				'status'	=> $d_attendees['signup_status'],
				'guest'		=> false
			);
		}
		foreach($guests as $guestsdata){
			$a_json[]	= array(
				'name'		=> $guestsdata['name'],
				'status'	=> false,
				'guest'		=> true
			);
		}
		$json = json_encode($a_json);
		unset($a_json);

		registry::register('template')->add_js('
			genOutput()
			$("input[type=\'checkbox\']").change(function (){
				if ($(this).is(":checked")){
					genOutput()
				}
			});
		', "docready");

		registry::register('template')->add_js('
		function genOutput(){
			var attendee_data = '.$json.';
			output = "";

			cb_guests		= ($("#cb_guests").attr("checked")) ? true : false;
			cb_confirmed	= ($("#cb_confirmed").attr("checked")) ? true : false;
			cb_signedin		= ($("#cb_signedin").attr("checked")) ? true : false;
			cb_backup		= ($("#cb_backup").attr("checked")) ? true : false;

			$.each(attendee_data, function(i, item) {
				if((cb_guests && item.guest == true) || (cb_confirmed && !item.guest && item.status == 0) || (cb_signedin && item.status == 1) || (cb_backup && item.status == 3)){
					output += "/inv " + item.name + "\n";
				}
			});
			$("#attendeeout").html(output);
		}
			');

		$text  = "<input type='checkbox' checked='checked' name='confirmed' id='cb_confirmed' value='true'> ".registry::fetch('user')->lang(array('raidevent_raid_status', 0));
		$text .= "<input type='checkbox' checked='checked' name='guests' id='cb_guests' value='true'> ".registry::fetch('user')->lang('raidevent_raid_guests');
		$text .= "<input type='checkbox' checked='checked' name='signedin' id='cb_signedin' value='true'> ".registry::fetch('user')->lang(array('raidevent_raid_status', 1));
		$text .= "<input type='checkbox' name='backup' id='cb_backup' value='true'> ".registry::fetch('user')->lang(array('raidevent_raid_status', 3));
		$text .= "<br/>";
		$text .= "<textarea name='group".rand()."' id='attendeeout' cols='60' rows='10' onfocus='this.select()' readonly='readonly'>";
		$text .= "</textarea>";

		$text .= '<br/>'.registry::fetch('user')->lang('rp_copypaste_ig')."</b>";
		return $text;
	}
}
?>