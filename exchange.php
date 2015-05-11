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
$eqdkp_root_path = './';
define('NO_MMODE_REDIRECT', true);

include_once($eqdkp_root_path . 'common.php');

$myOut = '';

if (registry::register('config')->get('pk_maintenance_mode')){
	if (registry::register('input')->get('format') == 'json'){
		$myOut = json_encode(array('status' => 0, 'error' => 'maintenance'));
	} else {
		$myOut = '<?xml version="1.0" encoding="UTF-8"?><response><status>0</status><error>maintenance</error></response>';
	}
	echo($myOut);
	exit;
}

if(registry::register('input')->get('out') != ''){
	
	switch (registry::register('input')->get('out')){
	
		case 'comments':
			if(registry::register('input')->get('deleteid', 0)){
				registry::register('comments')->Delete(registry::register('input')->get('page'), registry::register('input')->get('replies', 0));
			}elseif(registry::register('input')->get('comment', '', 'htmlescape')){
				registry::register('comments')->Save();
			}else{
				echo registry::register('comments')->Content(registry::register('input')->get('attach_id'), registry::register('input')->get('page'), registry::register('input')->get('replies', 0));
			}
			exit;
		break;

		case 'xsd': $myOut = $eqdkp_root_path.'core/xsd/data_export.xsd';
			break;
				
		case 'xml':
				
				if (registry::register('input')->get('data', '') != ''){
					$encrypt = registry::register('encrypt');
					$data = unserialize($encrypt->decrypt(rawurldecode(registry::register('input')->get('data'))));
					if ($data){
						$userid = registry::fetch('user')->getUserIDfromExchangeKey(registry::register('input')->get('key', ''));
						foreach($data['perms'] as $perm){
							if (!registry::fetch('user')->check_auth($perm, false, $userid)){
								$myOut = '<?xml version="1.0" encoding="UTF-8"?><response><status>0</status><error>access denied</error></response>';
								echo($myOut);
								exit;
							}
						}
						$myOut = registry::register('file_handler')->FileLink('rss/'.$data['url'], 'eqdkp', 'relative');
					}
					
				}
				
		break;

		// generate an ical feed
		case 'icalfeed':
			// the permissions for the single modules
			$permissions	= array(
				'calendar'	=> 'u_calendar_view'
			);
			$modulename		= registry::register('input')->get('module', '');

			// check for permission
			$userid = registry::fetch('user')->getUserIDfromExchangeKey(registry::register('input')->get('key', ''));
			if (isset($permissions[$modulename]) && registry::fetch('user')->check_auth($permissions[$modulename], false, $userid)){
				require($eqdkp_root_path.'libraries/icalcreator/iCalcreator.class.php');
				$v = new vcalendar;
				$v->setConfig('unique_id',		registry::register('config')->get('server_name'));
				$v->setProperty('x-wr-calname',	sprintf(registry::fetch('user')->lang('icalfeed_name'), registry::register('config')->get('guildtag')));
				$v->setProperty('X-WR-CALDESC',	registry::fetch('user')->lang('icalfeed_description'));
				// set the timezone - required by some clients
				$timezone 	= registry::register('config')->get('timezone');
				$v->setProperty( "X-WR-TIMEZONE", $timezone);
				iCalUtilityFunctions::createTimezone( $v, $timezone, array( "X-LIC-LOCATION" => $timezone));

				switch($modulename){
					case 'calendar':
						$eventtypes		= registry::register('input')->get('type', 'raids');
						switch($eventtypes){
							case 'raids':
								$eventsfilter = true;
							break;
							case 'all':
								$eventsfilter = false;
							break;
							case 'appointments':
								$eventsfilter = 'appointments';
							break;
						}
						$caleventids	= registry::register('plus_datahandler')->get('calendar_events', 'id_list', array($eventsfilter, registry::register('timehandler')->adddays(registry::register('timehandler')->time, -30)));

						if(is_array($caleventids) && count($caleventids) > 0){
							foreach($caleventids as $calid){

								// the attendee stuff
								$raidcal_status = unserialize(registry::register('config')->get('calendar_raid_status'));
								$raidstatus = array();
								if(is_array($raidcal_status)){
									foreach($raidcal_status as $raidcalstat_id){
										if($raidcalstat_id != 4){
											$raidstatus[$raidcalstat_id]	= registry::fetch('user')->lang(array('raidevent_raid_status', $raidcalstat_id));
										}
									}
								}

								// Build the Attendee Array
								$attendees = array();
								$attendees_raw = registry::register('plus_datahandler')->get('calendar_raids_attendees', 'attendees', array($calid));
								if(is_array($attendees_raw)){
									foreach($attendees_raw as $attendeeid=>$attendeerow){
										$attendees[$attendeerow['signup_status']][$attendeeid] = registry::register('plus_datahandler')->get('member', 'name', array($attendeeid));
									}
								}

								// Build the guest array
								if(registry::register('config')->get('calendar_raid_guests') == 1){
									$guestarray = registry::register('plus_datahandler')->get('calendar_raids_guests', 'members', array($calid));
									if(is_array($guestarray)){
										foreach($guestarray as $guest_row){
											$attendees[0][] = $guest_row['name'];
										}
									}
								}

								// get the status counts
								$counts = '';
								foreach($raidstatus as $statusid=>$statusname){
									$counts[$statusid]  = ((isset($attendees[$statusid])) ? count($attendees[$statusid]) : 0);
								}

								// build the description data
								$description_data	 = registry::register('plus_datahandler')->get('calendar_events', 'notes', array($calid));
								$description_data	.= (!empty($description_data)) ? '\n\n' : '';
								foreach($counts as $countid=>$countdata){
									$description_data .= $raidstatus[$countid].' ('.$countdata.'): '.((isset($attendees[$countid]) && count($attendees[$countid]) > 0) ? implode(', ',$attendees[$countid]) : '--').'\n';
								}

								// generate the ical output
								$e = new vevent;
								$e->setProperty('dtstart',		array("timestamp" => registry::register('plus_datahandler')->get('calendar_events', 'time_start', array($calid)).'Z'));
								$e->setProperty('dtend',		array("timestamp" => registry::register('plus_datahandler')->get('calendar_events', 'time_end', array($calid)).'Z'));
								$e->setProperty('summary',		registry::register('plus_datahandler')->get('calendar_events', 'name', array($calid)));
								$e->setProperty('description',	$description_data);
								$e->setProperty('class',		'PUBLIC');
								$e->setProperty('categories',	'PERSONAL');
								$v->setComponent($e);
							}
						}
					break;
				}

				// Save or Output the ICS File..(for future usage, not used atm)
				if($icsfile == true){
					$v->setConfig('filename', $icsfile);
					$v->saveCalendar();
				}else{
					header('Content-type: text/calendar; charset=utf-8;');
					header('Content-Disposition: attachment; filename=raidevents.ics');
					header('Cache-Control: max-age=10');
					echo $v->createCalendar();
					die();
				}
			}else{
				die('Permission denied');
			}
		break;

		case 'chartooltip':
			header('content-type: text/html; charset=UTF-8');
			echo registry::register('game')->chartooltip(registry::register('input')->get('charid', 0));
			exit;
		break;
		
		case 'portal':
			header('content-type: text/html; charset=UTF-8');
			registry::register('core')->cors_headers();
			echo registry::register('portal')->get_module_external(registry::register('input')->get('id', 0));
			exit;
		break;
		
		case 'socialcounts':
			header('Content-type: application/json; charset=utf-8');
			echo registry::register('socialplugins')->getSocialButtonCount(rawurldecode(registry::register('input')->get('url')), registry::register('input')->get('target'));
			exit;
		break;
		
		case 'styles':
			header('content-type: text/html; charset=UTF-8');
			$out = '<table class="table fullwidth colorswitch hoverrows">';
			$intCurrentStyle = register('user')->style['style_id'];
			foreach(register('pdh')->get('styles', 'styles', array(0, false)) as $styleid=>$row){
					$plugin_code = $row['template_path'];
					if (file_exists(registry::get_const('root_path').'templates/'.$plugin_code.'/screenshot.png' )){
						$screenshot = '<img src="'.registry::get_const('server_path').'templates/'.$plugin_code.'/screenshot.png" style="max-width:200px;" alt="" />';
					} elseif(file_exists(registry::get_const('root_path').'templates/'.$plugin_code.'/screenshot.jpg' )){
						$screenshot = '<img src="'.registry::get_const('server_path').'templates/'.$plugin_code.'/screenshot.jpg" style="max-width:200px;" alt="" />';
					} else $screenshot = "<img src='".registry::get_const('server_path')."images/global/default-image.svg' />";
					if($styleid == $intCurrentStyle){
						$current = " <i class='fa fa-check-circle fa-lg'></i>";
					} else $current = "";
					$url = filter_var($_SERVER['HTTP_REFERER'], FILTER_SANITIZE_STRING);
					$link = sanitize(preg_replace('#style\=([0-9]*)#', "", $url)).((strpos($url, "?") === false) ? '?' : '&').'style='.$styleid;
					
					$out .= '<tr><td width="10"><a href="'.$link.'">'.$screenshot.'</a></td><td><a href="'.$link.'">'.$row['style_name'].$current.'</a></td></tr>';
			}
			$out .= '</table>';
			echo $out;
			exit;
		break;
	}
	


	if(is_file($myOut)){
			ob_end_clean();
			ob_start();
			header('content-type: text/html; charset=UTF-8');
			$outdata = file_get_contents($myOut);
			echo((isset($outdata)) ? $outdata : '<?xml version="1.0" encoding="UTF-8"?><response><status>0</status><error>no data</error></response>');
	}else{
		echo '<?xml version="1.0" encoding="UTF-8"?><response><status>0</status><error>no file</error></response>';
	}
	exit;
}else{
	echo '<?xml version="1.0" encoding="UTF-8"?><response><status>0</status><error>no selection</error></response>';
	exit;
}

?>