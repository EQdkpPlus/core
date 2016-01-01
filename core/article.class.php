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

if(!defined('EQDKP_INC')){
	die('Do not access this file directly.');
}

if(!class_exists('article')){
	class article extends gen_class {

		private $_cache = array();

		public function gallery($strFolder, $intSortation, $strPath, $intPageNumber  = 0){
			$strFolder = str_replace("*+*+*", "/", $strFolder);
			$strOrigFolder = $strFolder;
			//Subfolder navigation
			if ($this->in->get('gf') != "" && $this->in->get('gsf') != ""){
				if (base64_decode($this->in->get('gf')) == $strOrigFolder) $strFolder = base64_decode($this->in->get('gsf'));
			}


			$contentFolder = $this->pfh->FolderPath($strFolder, 'files');
			$contentFolderSP = $this->pfh->FolderPath($strFolder, 'files', 'serverpath');

			$dataFolder = $this->pfh->FolderPath('system', 'files', 'plain');
			$blnIsSafe = isFilelinkInFolder($contentFolder, $dataFolder);
			if (!$blnIsSafe) return "";

			$arrFiles = sdir($contentFolder);
			$arrDirs = $arrImages = $arrImagesDate = array();
			foreach($arrFiles as $key => $val){
				if (is_dir($contentFolder.$val)){
					$arrDirs[] = $val;
				} else {
					$extension = strtolower(pathinfo($val, PATHINFO_EXTENSION));
					if (in_array($extension, array('jpg', 'png', 'gif', 'jpeg'))){
						$arrImages[$val] = pathinfo($val, PATHINFO_FILENAME);
						$arrImageDimensions[$val] = getimagesize($contentFolder.$val);
						if ($intSortation == 2 || $intSortation == 3) $arrImagesDate[$val] = filemtime($contentFolder.$val);
					}
				}
			}

			switch($intSortation){
				case 1: natcasesort($arrImages);
						$arrImages = array_reverse($arrImages);

				break;
				case 2: asort($arrImagesDate); $arrImages = $arrImagesDate;
				break;

				case 3: arsort($arrImagesDate); $arrImages = $arrImagesDate;
				break;

				default: natcasesort($arrImages);
			}

			$strOut = '<ul class="image-gallery">';
			$strLink = $strPath.(($intPageNumber > 1) ? '&page='.$intPageNumber : '');

			if($this->in->exists('gsf') && $this->in->get('gsf') != ''){
				$arrPath = array_filter(explode('/', $strFolder));
				array_pop($arrPath);
				$strFolderUp = implode('/', $arrPath);
				if ($strFolderUp == $strOrigFolder) {
					$strFolderUp = '';
				} else {
					$strFolderUp = base64_encode($strFolderUp);
				}
				$strOut .='<li class="folderup"><a href="'.$strLink.'&gf='.base64_encode($strOrigFolder).'&gsf='.$strFolderUp.'"><i class="fa fa-level-up fa-flip-horizontal"></i><br/>'.$this->user->lang('back').'</a></li>';
			}

			natcasesort($arrDirs);
			foreach($arrDirs as $foldername){
				$strOut .= '<li class="folder"><a href="'.$strLink.'&gf='.base64_encode($strOrigFolder).'&gsf='.base64_encode($strFolder.'/'.$foldername).'"><i class="fa fa-folder"></i><br/>'.sanitize($foldername).'</a></li>';
			}

			$strThumbFolder = $this->pfh->FolderPath('system/thumbs', 'files');
			$strThumbFolderSP = $this->pfh->FolderPath('system/thumbs', 'files', 'serverpath');

			foreach($arrImages as $key => $val){
				//Check for thumbnail
				$strThumbname = "thumb_".pathinfo($key, PATHINFO_FILENAME)."-150x150.".pathinfo($key, PATHINFO_EXTENSION);
				$strThumbnail = "";
				if (is_file($strThumbFolder.$strThumbname)){
					$strThumbnail = $strThumbFolderSP.$strThumbname;
				} else {
					//Create thumbnail
					$this->pfh->thumbnail($contentFolder.$key, $strThumbFolder, $strThumbname, 150);
					if (is_file($strThumbFolder.$strThumbname)){
						$strThumbnail = $strThumbFolderSP.$strThumbname;
					}
				}

				if($strThumbnail != ""){
					$strOut .= '<li class="image"><a href="'.$contentFolderSP.$key.'" class="lightbox_'.md5($strFolder).'" rel="'.md5($strFolder).'" title="'.sanitize($key).'; '.$arrImageDimensions[$key][0].'x'.$arrImageDimensions[$key][1].' px"><img src="'.$strThumbnail.'" alt="Image" /></a></li>';
				}

			}

			$strOut .= "</ul><div class=\"clear\"></div>";

			$this->jquery->lightbox(md5($strFolder), array('slideshow' => true, 'transition' => "elastic", 'slideshowSpeed' => 4500, 'slideshowAuto' => false));

			return $strOut;
		}

		public function raidloot($intRaidID, $blnWithChars=false){
			//Get Raid-Infos:
			$intEventID = $this->pdh->get('raid', 'event', array($intRaidID));
			if ($intEventID){
				if(isset($this->_cache['raidloot']) && isset($this->_cache['raidloot'][$intRaidID])){
					return $this->_cache['raidloot'][$intRaidID];
				}
				
				$strOut = '<div class="raidloot"><h3>'.$this->user->lang('loot').' '.$this->pdh->get('event', 'html_icon', array($intEventID)).$this->pdh->get('raid', 'html_raidlink', array($intRaidID, register('routing')->simpleBuild('raids'), '', true));
				$strRaidNote = $this->pdh->get('raid', 'html_note', array($intRaidID));
				if ($strRaidNote != "") $strOut .= ' ('.$strRaidNote.')';
				$strOut .= ', '.$this->pdh->get('raid', 'html_date', array($intRaidID)).'</h3>';

				//Get Items from the Raid
				$arrItemlist = $this->pdh->get('item', 'itemsofraid', array($intRaidID));
				infotooltip_js();

				if (count($arrItemlist)){
					foreach($arrItemlist as $item){
						$buyer = $this->pdh->get('item', 'buyer', array($item));
						$strOut .=  $this->pdh->get('item', 'link_itt', array($item, register('routing')->simpleBuild('items'), '', false, false, false, false, false, true)). ' - '.$this->pdh->geth('member', 'memberlink_decorated', array($buyer, register('routing')->simpleBuild('character'), '', true)).
						', '.round($this->pdh->get('item', 'value', array($item))).' '.$this->config->get('dkp_name').'<br />';
					}
				}

				if ($blnWithChars){
					$attendees_ids = $this->pdh->get('raid', 'raid_attendees', array($intRaidID));
					if (count($attendees_ids)){
						$strOut .= '<br /><h3>'.$this->user->lang('attendees').'</h3>';

						foreach($attendees_ids as $intAttendee){
							$strOut.= $this->pdh->get('member', 'memberlink_decorated', array($intAttendee, $this->routing->simpleBuild('character'), '', true)).'<br/>';
						}
					}
				}

				$strOut = $strOut.'</div>';
				
				if(!isset($this->_cache['raidloot'])) $this->_cache['raidloot'] = array();
				$this->_cache['raidloot'][$intRaidID] = $strOut;
				
				return $strOut;
			}
			return '';
		}

		public function buildCalendarevent($intEventID){
			if(isset($this->_cache['calendarevent']) && isset($this->_cache['calendarevent'][$intEventID])){
				return $this->_cache['calendarevent'][$intEventID];
			}
			
			$out = '<div class="articleCalendarEventBox table noMobileTransform">';

			$eventextension	= $this->pdh->get('calendar_events', 'extension', array($intEventID));
			if(!$eventextension) return false;
			
			$raidclosed		= ($this->pdh->get('calendar_events', 'raidstatus', array($intEventID)) == '1') ? true : false;

			if($eventextension['calendarmode'] == 'raid') {
				$eventdata	= $this->pdh->get('calendar_events', 'data', array($intEventID));

				// Build the Deadline
				$deadlinedate	= $eventdata['timestamp_start']-($eventdata['extension']['deadlinedate'] * 3600);
				if(date('j', $deadlinedate) == date('j', $eventdata['timestamp_start'])){
					$deadlinetime	= $this->time->user_date($deadlinedate, false, true);
				}else{
					$deadlinetime	= $this->time->user_date($deadlinedate, true);
				}

				$data = array(
						'NAME'				=> $this->pdh->get('event', 'name', array($eventdata['extension']['raid_eventid'])),
						'DATE_DAY'				=> $this->time->date('d', $eventdata['timestamp_start']),
						'DATE_MONTH'			=> $this->time->date('F', $eventdata['timestamp_start']),
						'DATE_YEAR'				=> $this->time->date('Y', $eventdata['timestamp_start']),
						'DATE_FULL'				=> $this->time->user_date($eventdata['timestamp_start']).', '.$this->time->user_date($eventdata['timestamp_start'], false, true).' - '.$this->time->user_date($eventdata['timestamp_end'], false, true),
						'RAIDTIME_START'		=> $this->time->user_date($eventdata['timestamp_start'], false, true),
						'RAIDTIME_END'			=> $this->time->user_date($eventdata['timestamp_end'], false, true),
						'RAIDTIME_DEADLINE'		=> $deadlinetime,
						'CALENDAR'				=> $this->pdh->get('calendars', 'name', array($eventdata['calendar_id'])),
						'RAIDICON'				=> $this->pdh->get('event', 'html_icon', array($eventdata['extension']['raid_eventid'], 32)),
						'RAIDNOTE'				=> ($eventdata['notes']) ? $this->bbcode->toHTML(nl2br($eventdata['notes'])) : '',
						'LINK'					=> $this->routing->build('calendarevent', $this->pdh->get('calendar_events', 'name', array($intEventID)), $intEventID),
				);

				$out .= '<div class="tr raid '.(($raidclosed) ? 'closed' : 'open').'"><div class="bigDateContainer td">';
				$out .= $data['RAIDICON'];
				$out .= '<div class="middleDateTime">'.$data['DATE_DAY'].'</div>';
				$out .= '<div class="articleMonth">'.$data['DATE_MONTH'].'</div>';
				$out .= '<div class="middleDateTime">'.$data['RAIDTIME_START'].'</div>';
				$out .= '</div>';

				$out .= '<div class="articleCalendarEventBoxContent td">';
				$closedIcon = ($raidclosed) ? '<i class="fa fa-lg fa-lock"></i> ' : '';
				$out .= '<h2>'.$closedIcon.'<a href="'.$data['LINK'].'">'.$data['NAME'].'</a></h2>';
				$out .= '<div class="eventdata-details">
			<div class="eventdata-details-date"><i class="fa fa-lg fa-calendar-o"></i> '.$data['DATE_FULL'].'</div>';
				$out .= '<div class="eventdata-details-deadline"><i class="fa fa-calendar-times-o fa-lg" title="{L_raidevent_raidleader}"></i> '.$this->user->lang('calendar_deadline').' '.$data['RAIDTIME_DEADLINE'].' </div>';
				$out .='<div class="eventdata-details-calendar"><i class="fa fa-calendar fa-lg"></i> '.$data['CALENDAR'].'</div>';

				//Attendees
				// Build the Attendee Array
				$attendees = array();
				$attendees_raw = $this->pdh->get('calendar_raids_attendees', 'attendees', array($intEventID));
				if(is_array($attendees_raw)){
					foreach($attendees_raw as $attendeeid=>$attendeerow){
						if($attendeeid > 0){
							$attendees[$attendeerow['signup_status']][$attendeeid] = $attendeerow;
						}
					}
				}

				// Build the guest array
				$guests = '';
				if($this->config->get('calendar_raid_guests') > 0){
					$guestarray = $this->pdh->get('calendar_raids_guests', 'members', array($intEventID));
					if(is_array($guestarray)){
						foreach($guestarray as $guest_row){
							$guests[] = $guest_row['name'];
						}
					}
				}
				// get the status counts
				$raidcal_status = $this->config->get('calendar_raid_status');
				$raidstatus = array();
				if(is_array($raidcal_status)){
					foreach($raidcal_status as $raidcalstat_id){
						if($raidcalstat_id != 4){
							$raidstatus[$raidcalstat_id]	= $this->user->lang(array('raidevent_raid_status', $raidcalstat_id));
						}
					}
				}

				$counts = '';
				foreach($raidstatus as $statusid=>$statusname){
					$counts[$statusid]  = ((isset($attendees[$statusid])) ? count($attendees[$statusid]) : 0);
				}
				$guest_count	= (is_array($guests)) ? count($guests) : 0;
				if(isset($counts[0])){
					$counts[0]		= $counts[0] + $guest_count;
				}
				$signinstatus = $this->pdh->get('calendar_raids_attendees', 'html_status', array($intEventID, $this->user->data['user_id']));

				if (is_array($counts)){
					foreach($counts as $countid=>$countdata){#
						$out .= '<span class="status'.$countid.' nextevent_statusrow coretip" data-coretip="'.$raidstatus[$countid].'">'.$this->pdh->get('calendar_raids_attendees', 'status_flag', array($countid)).' '.$countdata.'</span>';
					}
				}

				if($signinstatus && $signinstatus != ""){
					$out .= ' &bull; <i class="fa fa-lg fa-user coretip" data-coretip="'.$this->user->data['username'].'"></i> '.$signinstatus;
				}

				$out .='</div>';
				$out .= '</div></div>';
			} else {
				$eventdata	= $this->pdh->get('calendar_events', 'data', array($intEventID));

				$blnIsPrivate = ($eventdata['private'] == 1) ? true : false;
				if($blnIsPrivate) return false;

				if($eventdata['allday'] == 1){
					$full_date = $this->time->user_date($eventdata['timestamp_start']).', '.$this->user->lang('calendar_allday');
				}elseif($this->time->date('d', $eventdata['timestamp_start']) == $this->time->date('d', $eventdata['timestamp_end'])){
					//Samstag, 31.12.2015, 15 - 17 Uhr
					$full_date = $this->time->user_date($eventdata['timestamp_start']).', '.$this->time->user_date($eventdata['timestamp_start'], false, true);
				}else{
					$full_date = $this->time->user_date($eventdata['timestamp_start'], true, false).' - '.$this->time->user_date($eventdata['timestamp_end'], true, false);
				}

				$data = array(
						'NAME'				=> $this->pdh->get('calendar_events', 'name', array($intEventID)),
						'DATE_DAY'			=> $this->time->date('d', $eventdata['timestamp_start']),
						'DATE_MONTH'		=> $this->time->date('F', $eventdata['timestamp_start']),
						'DATE_YEAR'			=> $this->time->date('Y', $eventdata['timestamp_start']),
						'DATE_TIME'			=> ($eventdata['allday'] == 1) ? '' : $this->time->user_date($eventdata['timestamp_start'], false, true),
						'DATE_FULL'			=> $full_date,
						'LOCATION'			=> (isset($eventdata['extension']['location'])) ? $eventdata['extension']['location'] : false,
						'CALENDAR'			=> $this->pdh->get('calendars', 'name', array($eventdata['calendar_id'])),
						'LINK'				=> $this->routing->build('calendarevent', $this->pdh->get('calendar_events', 'name', array($intEventID)), $intEventID),
				);

				$out .= '<div class="tr event"><div class="bigDateContainer td">';
				$out .= '<div class="bigDateNumber">'.$data['DATE_DAY'].'</div>';
				$out .= '<div class="articleMonth">'.$data['DATE_MONTH'].'</div>';
				$out .= '<div class="middleDateTime">'.$data['DATE_TIME'].'</div>';
				$out .= '</div>';

				$out .= '<div class="articleCalendarEventBoxContent td">';
				$out .= '<h2><a href="'.$data['LINK'].'">'.$data['NAME'].'</a></h2>';
				$out .= '<div class="eventdata-details">
			<div class="eventdata-details-date"><i class="fa fa-lg fa-calendar-o"></i> '.$data['DATE_FULL'].'</div>';
				if($data['LOCATION']){
					$out .= '<div class="eventdata-details-location"><i class="fa fa-lg fa-map-marker"></i> '.$data['LOCATION'].'</div>';
				}
				$out .='<div class="eventdata-details-calendar"><i class="fa fa-calendar fa-lg"></i> '.$data['CALENDAR'].'</div>';
				//Attendees
				$event_attendees		= (isset($eventdata['extension']['attendance']) && count($eventdata['extension']['attendance']) > 0) ? $eventdata['extension']['attendance'] : array();
				$userstatus	  = array('attendance' => 0, 'maybe' => 0, 'decline' => 0);
				$statusofuser = array();
				foreach($event_attendees as $attuserid=>$attstatus){
					switch($attstatus){
						case 1:		$attendancestatus = 'attendance'; break;
						case 2:		$attendancestatus = 'maybe'; break;
						case 3:		$attendancestatus = 'decline'; break;
					}
					$statusofuser[$attuserid] = $attstatus;
					if(!isset($userstatus[$attendancestatus])) $userstatus[$attendancestatus] = 0;
					$userstatus[$attendancestatus]++;
				}

				$out .='<div><i class="fa fa-lg fa-users green coretip" data-coretip="'.$this->user->lang('calendar_eventdetails_confirmations').'"></i> '.$userstatus['attendance'].((isset($statusofuser[$this->user->id]) && $statusofuser[$this->user->id] == 1) ? ' <i class="fa fa-lg fa-user coretip" data-coretip="'.$this->user->data['username'].'"></i>' : '').'
					&bull; <i class="fa fa-lg fa-users orange coretip" data-coretip="'.$this->user->lang('calendar_eventdetails_maybes').'"></i> '.$userstatus['maybe'].((isset($statusofuser[$this->user->id]) && $statusofuser[$this->user->id] == 2) ? '<i class="fa fa-lg fa-user coretip" data-coretip="'.$this->user->data['username'].'"></i>' : '').'
					&bull; <i class="fa fa-lg fa-users red coretip" data-coretip="'.$this->user->lang('calendar_eventdetails_declines').'"></i> '.$userstatus['decline'].((isset($statusofuser[$this->user->id]) && $statusofuser[$this->user->id] == 3) ? '<i class="fa fa-lg fa-user coretip" data-coretip="'.$this->user->data['username'].'"></i>' : '').
							'</div>';

				$out .='</div>';
				$out .= '</div></div>';

			}


			$out .= '</div>';

			if(!isset($this->_cache['calendarevent'])) $this->_cache['calendarevent'] = array();
				
			$this->_cache['calendarevent'][$intEventID] = $out;

			return $out;
		}

	}
}
?>
