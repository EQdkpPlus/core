<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2009
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
$eqdkp_root_path = './';
define('NO_MMODE_REDIRECT', true);

include_once($eqdkp_root_path . 'common.php');

$myOut = '';

// the extensions for the image files
$mime_types = array(
	'png'	=> 'image/png',
	'jpe'	=> 'image/jpeg',
	'jpeg'	=> 'image/jpeg',
	'jpg'	=> 'image/jpeg',
	'gif'	=> 'image/gif',
);

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

		case 'imageupload':

			if(!registry::fetch('user')->is_signedin()){
				echo('You have no permission to see this page as you are not logged in');exit;
			}

			$fname			= $_FILES['uploadfile']['name'];
			$fsize			= $_FILES['uploadfile']['size'];
			$ftmpname		= $_FILES['uploadfile']['tmp_name'];

			// Check if file is selected
			if(isset($fsize) && $fsize == 0 || !$fsize){
				echo json_encode(array(
					'error'	=> registry::fetch('user')->lang('imageuploader_e_empty'),
				));
				exit;
			}

			list($imgwidth, $imgheight, $imgtype, $imgattr) = getimagesize($ftmpname);
			$maxwidth		= (registry::register('input')->get('imgwidth', 0) > 0) ? registry::register('input')->get('imgwidth', 0) : 9000;
			$maxheight		= (registry::register('input')->get('imgheight', 0) > 0) ? registry::register('input')->get('imgheight', 0) : 9000;
			$maxfsize		= (registry::register('input')->get('filesize', 0) > 0) ? registry::register('input')->get('filesize', 0) : 2000000;

			// check the filesize
			if($fsize < $maxfsize){
				if (!$imgwidth && !$imgheight){
					echo json_encode(array(
						'error'	=> registry::fetch('user')->lang('imageuploader_e_noimg'),
					));
					exit;
				}

				// Check the image dimensions
				if ($imgwidth > $maxwidth || $imgheight > $maxheight){
					echo json_encode(array(
						'error'	=> sprintf(registry::fetch('user')->lang('imageuploader_e_fsize'), $maxwidth, $maxheight),
					));
					exit;
				}
				$fileEnding		= pathinfo($fname, PATHINFO_EXTENSION);
				
				// get the mine....
				if(function_exists('finfo_open') && function_exists('finfo_file') && function_exists('finfo_close')){
					$finfo			= finfo_open(FILEINFO_MIME);
					$mime			= finfo_file($finfo, $ftmpname);
					finfo_close($finfo);
				}elseif(function_exists('mime_content_type')){
					$mime			= mime_content_type( $ftmpname );
				}else{
					// try to get the extension... not really secure...

					if(!$fileEnding){
						echo json_encode(array(
							'error'	=> 'Your image seems to have no file extension. This is not allowed for security reasons',
						));
						exit;
					}
					
					if (array_key_exists($fileEnding, $mime_types)) {
						$mime			= $mime_types[$fileEnding];
					}
					if(!$mime){
						echo json_encode(array(
							'error'	=> 'We could not get the proper mime code for the image. You tried to upload "*.'.$fileEnding.'", this mime type is not allowed.',
						));
						exit;
					}
				}

				// Sometimes (PHP-5.3?) content-type contains charset definition, remove it as "charset=binary" is useless
				$mime = array_shift(preg_split('/[; ]/', $mime));

				switch ($mime) {
					case 'image/jpeg':
					case 'image/pjpeg':
						if (strtolower($fileEnding) != 'jpg' && strtolower($fileEnding) != 'jpeg'){
							echo json_encode(array(
								'error'	=> registry::fetch('user')->lang('imageuploader_e_mime'),
							));
							exit;
						}
					break;
					case 'image/gif':
						if (strtolower($fileEnding) != 'gif'){
							echo json_encode(array(
								'error'	=> registry::fetch('user')->lang('imageuploader_e_mime'),
							));
							exit;
						}
					break;
					case 'image/png':
						if (strtolower($fileEnding) != 'png'){
							echo json_encode(array(
								'error'	=> registry::fetch('user')->lang('imageuploader_e_mime'),
							));
							exit;
						}
					break;
					default:
						echo json_encode(array(
							'error'	=> sprintf(registry::fetch('user')->lang('imageuploader_e_wrongtype'), 'jpg, gif, png'),
						));
						exit;
				}

				$filename_out	= time().'__'.basename($fname);
				registry::register('file_handler')->FileMove($ftmpname, registry::register('file_handler')->FolderPath('imageupload', 'eqdkp').$filename_out, true);
				echo json_encode(array(
					'file'	=> $filename_out,
					'size'	=> $fsize
				));
				exit;
			} else {
				echo json_encode(array(
					'error'	=> sprintf(registry::fetch('user')->lang('imageuploader_e_filesize'), round(($maxfsize/1024), 2)),
				));
				exit;
			}
		break;

		case 'imageupload_del':

			// check if the user is logged in
			if(!registry::fetch('user')->is_signedin()){
				echo('You have no permission to see this page as you are not logged in');exit;
			}

			// set the file name
			$tmp_filename	= registry::register('encrypt')->decrypt(registry::register('input')->get('data', ''));

			// now check if the input file type is right
			$fileEnding		= pathinfo($tmp_filename, PATHINFO_EXTENSION);
			if (array_key_exists($fileEnding, $mime_types)) {
				echo('You tried to delete a file with an extension which is not allowed.... Bad guy! Do not try to hack this page...');exit;
			}

			// check if the path is ok...
			if (isFilelinkInFolder($tmp_filename, 'data')) {
				echo('Only actions within the data folder are allowed.');exit;
			}

			if($tmp_filename != ''){
				registry::register('file_handler')->Delete($tmp_filename);
			}
		break;

		case 'comments':
			if(!registry::fetch('user')->is_signedin()){
				echo('You have no permission to see this page as you are not logged in');exit;
			}
			if(registry::register('input')->get('deleteid', 0)){
				registry::register('comments')->Delete(registry::register('input')->get('page'), registry::register('input')->get('rpath'));
			}elseif(registry::register('input')->get('comment', '', 'htmlescape')){
				registry::register('comments')->Save();
			}else{
				echo registry::register('comments')->Content(registry::register('input')->get('attach_id', 0), registry::register('input')->get('page'), registry::register('input')->get('rpath'), true);
			}
			exit;
		break;

		case 'xsd': $myOut = $eqdkp_root_path.'core/xsd/data_export.xsd';
			break;
				
		case 'xml':
				
				if (registry::register('input')->get('data', '') != ''){
					$encrypt = registry::register('encrypt');
					$data = unserialize($encrypt->decrypt(registry::register('input')->get('data')));
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
				'calendar'=>'u_calendar_view'
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
						$caleventids	= registry::register('plus_datahandler')->get('calendar_events', 'id_list', array(true, registry::register('timekeeper')->time));
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
								$e->setProperty('dtstart',        array("timestamp" => registry::register('plus_datahandler')->get('calendar_events', 'time_start', array($calid)), "tz" => registry::register('config')->get('timezone')));
								$e->setProperty('dtend',		array("timestamp" => registry::register('plus_datahandler')->get('calendar_events', 'time_end', array($calid)), "tz" => registry::register('config')->get('timezone')));
								$e->setProperty('summary',		registry::register('plus_datahandler')->get('calendar_events', 'name', array($calid)));
								$e->setProperty('description',	$description_data);
								//$e->setProperty('comment',		'This is a comment');
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
			echo registry::register('game')->chartooltip(registry::register('input')->get('charid', 0));
			exit;
		break;
	}
	


	if(is_file($myOut)){
			ob_end_clean();
			ob_start();
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