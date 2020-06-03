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

$rpexport_plugin['csv_points.class.php'] = array(
	'name'			=> 'CSV & DKP',
	'function'		=> 'CSVpointexport',
	'contact'		=> 'webmaster@wallenium.de',
	'version'		=> '2.0.0');

if(!function_exists('CSVpointexport')){
	function CSVpointexport($raid_id, $raid_groups){
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

		$detail_settings = registry::register('plus_datahandler')->get_page_settings('listmembers', 'hptt_listmembers_memberlist_detail');
		$intDefaultMDKP = $detail_settings['default_pool'];

		$eventId = registry::register('plus_datahandler')->get('calendar_events', 'raid_eventid', array($raid_id));
		$arrMultiDkpIDs = registry::register('plus_datahandler')->get('event', 'multidkppools', array($eventId));
		//Because the event can be with different MultiDKPIDs, take the first one
		$mdkp = (is_array($arrMultiDkpIDs)) ? $arrMultiDkpIDs[0] : $intDefaultMDKP;

		$a_json_d	= array();
		$a_json_a	= array();

		$arrPoints = $arrMember = array();
		foreach($attendees as $id_attendees=>$d_attendees){
			$arrPoints[] = (isset($arrPresets['current'])) ? registry::register('plus_datahandler')->get($arrPresets['current'][0], $arrPresets['current'][1], $arrPresets['current'][2], array('%dkp_id%' => $mdkp, '%member_id%' => $id_attendees, '%with_twink%' => (intval(registry::register('config')->get('show_twinks'))) ? 0 : 1)) : 0;
			$arrMember[] = array(
				'id'			=> $id_attendees,
				'name'			=> unsanitize(registry::register('plus_datahandler')->get('member', 'name', array($id_attendees))),
				'status'		=> $d_attendees['signup_status'],
				'guest'			=> false,
				'group'			=> $d_attendees['raidgroup'],
				'point'			=> (isset($arrPresets['current'])) ? registry::register('plus_datahandler')->get($arrPresets['current'][0], $arrPresets['current'][1], $arrPresets['current'][2], array('%dkp_id%' => $mdkp, '%member_id%' => $id_attendees, '%with_twink%' => (intval(registry::register('config')->get('show_twinks'))) ? 0 : 1)) : 0,
			);
		}

		array_multisort($arrPoints, SORT_NUMERIC, SORT_DESC, $arrMember);
		foreach($arrMember as $arrData){
			$a_json_d[] = array(
				'name'		=> $arrData['name'],
				'status'	=> $arrData['status'],
				'guest'		=> $arrData['guest'],
				'point'		=> $arrData['point'],
				'group'		=> $arrData['group'],
			);
		}

		array_multisort($arrPoints, SORT_NUMERIC, SORT_ASC, $arrMember);
		foreach($arrMember as $arrData){
			$a_json_a[] = array(
				'name'		=> $arrData['name'],
				'status'	=> $arrData['status'],
				'guest'		=> $arrData['guest'],
				'point'		=> $arrData['point'],
				'group'		=> $arrData['group'],
			);
		}

		foreach($guests as $guestsdata){
			$a_json_d[]	= $a_json_a[] = array(
				'name'		=> $guestsdata['name'],
				'status'	=> false,
				'guest'		=> true,
				'point'		=> 0,
				'group'		=> $guestsdata['raidgroup']
			);
		}

		$json_asc	= json_encode($a_json_a);
		$json_desc	= json_encode($a_json_d);
		unset($a_json);

		registry::register('template')->add_js('
			genOutput()
			$("input[type=\'checkbox\'], #ip_seperator, #dd_sorting, #raidgroup").on(\'change\', function (){
				genOutput()
			});
		', "docready");

		registry::register('template')->add_js('
		function genOutput(){
			var json_asc		= '.$json_asc.';
			var json_desc		= '.$json_desc.';
			var attendee_data = ($("#dd_sorting").val() != "asc") ? json_asc : json_desc;
			var data = [];

			ip_seperator	= ($("#ip_seperator").val() != "") ? $("#ip_seperator").val() : ",";
			cb_guests		= ($("#cb_guests").prop("checked")) ? true : false;
			cb_confirmed	= ($("#cb_confirmed").prop("checked")) ? true : false;
			cb_signedin		= ($("#cb_signedin").prop("checked")) ? true : false;
			cb_backup		= ($("#cb_backup").prop("checked")) ? true : false;

			$.each(attendee_data, function(i, item) {
				if((cb_guests && item.guest == true) || (cb_confirmed && !item.guest && item.status == 0) || (cb_signedin && item.status == 1) || (cb_backup && item.status == 3)){
					console.log($("#raidgroup").val());
					if($("#raidgroup").val() == "0" || (item.group > 0 && item.group == $("#raidgroup").val())){
						data.push(item.name + " " + item.point);
					}
				}
			});
			$("#attendeeout").html(data.join(ip_seperator));
		}
			');
			$text  = "<dt><label>".registry::fetch('user')->lang('raidevent_export_seperator')."</label></dt>
						<dd>
							<input type='text' name='seperator' id='ip_seperator' value=',' size='4' />
						</dd>
					</dl><dl>";
			$text .= "<dt><label>".registry::fetch('user')->lang('raidevent_export_sorting')."</label></dt>
						<dd>
							<select name='sorting' id='dd_sorting'>
								<option value='desc'>ASC</option>
								<option value='asc'>DESC</option>
							</select>
						</dd>
					</dl><dl>";
			$text .= "<dt><label>".registry::fetch('user')->lang('raidevent_export_raidgroup')."</label></dt>
						<dd>
							".(new hdropdown('raidgroup', array('options' => $raid_groups, 'value' => 0, 'id' => 'raidgroup')))->output()."
						</dd>
					</dl><dl>";
		$text .= "<input type='checkbox' checked='checked' name='confirmed' id='cb_confirmed' value='true'> ".registry::fetch('user')->lang(array('raidevent_raid_status', 0));
		$text .= "<input type='checkbox' checked='checked' name='guests' id='cb_guests' value='true'> ".registry::fetch('user')->lang('raidevent_raid_guests');
		$text .= "<input type='checkbox' checked='checked' name='signedin' id='cb_signedin' value='true'> ".registry::fetch('user')->lang(array('raidevent_raid_status', 1));
		$text .= "<input type='checkbox' name='backup' id='cb_backup' value='true'> ".registry::fetch('user')->lang(array('raidevent_raid_status', 3));
		$text .= ' | '.registry::fetch('user')->lang('raidevent_export_sorting')." ";
		$text .= "<br/>";
		$text .= "<textarea name='group".mt_rand()."' id='attendeeout' cols='60' rows='10' onfocus='this.select()' readonly='readonly'>";
		$text .= "</textarea>";

		$text .= '<br/>'.registry::fetch('user')->lang('rp_copypaste_ig')."</b>";
		return $text;
	}
}
