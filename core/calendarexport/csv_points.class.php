<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2010
 * Date:		$Date: 2014-05-12 18:20:50 +0200 (Mo, 12. Mai 2014) $
 * -----------------------------------------------------------------------
 * @author		$Author: godmod $ geändert Tom
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 14203 $
 * 
 * $Id: csv.class.php 14203 2014-05-12 16:20:50Z godmod $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

$rpexport_plugin['csv_points.class.php'] = array(
	'name'			=> 'CSV + DKP',
	'function'		=> 'CSVpointexport',
	'contact'		=> 'webmaster@wallenium.de',
	'version'		=> '1.0.0');

if(!function_exists('CSVpointexport')){
	function CSVpointexport($raid_id){
		$presets = array(
			array('name'	=> 'earned', 'sort' => true, 'th_add' => '', 'td_add' => ''),
			array('name'	=> 'spent', 'sort' => true, 'th_add' => '', 'td_add' => ''),
			array('name'	=> 'adjustment', 'sort' => true, 'th_add' => '', 'td_add' => ''),
			array('name'	=> 'current', 'sort' => true, 'th_add' => '', 'td_add' => ''),
		);

		$arrPresets = array();
		foreach ($presets as $preset){
			$pre = registry::register('plus_datahandler')->pre_process_preset($preset['name'], $preset);
				if(empty($pre))
					continue;
			$arrPresets[$pre[0]['name']] = $pre[0];
		}

		$attendees	= registry::register('plus_datahandler')->get('calendar_raids_attendees', 'attendees', array($raid_id));
		$guests		= registry::register('plus_datahandler')->get('calendar_raids_guests', 'members', array($raid_id));
		$mdkp		= 1;
		$a_json		= array();
		
		$arrPoints = $arrMember = array();
		foreach($attendees as $id_attendees=>$d_attendees){
			$arrPoints[] = (isset($arrPresets['current'])) ? registry::register('plus_datahandler')->get($arrPresets['current'][0], $arrPresets['current'][1], $arrPresets['current'][2], array('%dkp_id%' => $mdkp, '%member_id%' => $id_attendees, '%with_twink%' => false)) : 0;
			$arrMember[] = array(
				'id'			=> $id_attendees,
				'name'			=> unsanitize(registry::register('plus_datahandler')->get('member', 'name', array($id_attendees))),
				'status'		=> $d_attendees['signup_status'],
				'guest'			=> false,
				'point'			=> (isset($arrPresets['current'])) ? registry::register('plus_datahandler')->get($arrPresets['current'][0], $arrPresets['current'][1], $arrPresets['current'][2], array('%dkp_id%' => $mdkp, '%member_id%' => $id_attendees, '%with_twink%' => false)) : 0,
			);
		}

		array_multisort($arrPoints, SORT_NUMERIC, SORT_DESC, $arrMember);
		foreach($arrMember as $arrData){
			$a_json[] = array(
				'name'		=> $arrData['name'],
				'status'	=> $arrData['status'],
				'guest'		=> $arrData['guest'],
				'point'		=> $arrData['point'],
			);
		}

		foreach($guests as $guestsdata){
			$a_json[]	= array(
				'name'		=> $guestsdata['name'],
				'status'	=> false,
				'guest'		=> true,
				'point'		=> 0
			);
		}
		
		$json = json_encode($a_json);
		unset($a_json);

		registry::register('template')->add_js('
			genOutput()
			$("input[type=\'checkbox\']").change(function (){
				genOutput()
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
					output += item.name + " " + item.point + ", ";
				}
			});
			$("#attendeeout").html(output.substring(0, output.length-2));
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