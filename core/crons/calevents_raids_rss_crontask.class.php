<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2011
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

if ( !class_exists( "calevents_raids_rss_crontask" ) ) {
	class calevents_raids_rss_crontask extends crontask {
		public static $shortcuts = array('pfh', 'time', 'pdh', 'user', 'config', 'env');

		public function __construct(){
			$this->defaults['active']			= true;
			$this->defaults['repeat']			= true;
			$this->defaults['repeat_type']		= 'hourly';
			$this->defaults['repeat_interval']	= 2;
			$this->defaults['ajax']				= true;
			$this->defaults['description']		= 'Creating Calendar Raids RSS-Feed';
		}

		public function run(){
			$this->pfh->secure_folder('rss', 'eqdkp');
			require($this->root_path.'core/feed.class.php');
			$rssfile			= $this->pfh->FilePath('rss/calendar_raids.xml', 'eqdkp', 'relative');
			$feed				= registry::register('feed');
			$feed->feedfile		= $this->pfh->FileLink('rss/calendar_raids.xml', 'eqdkp', 'absolute');
			$feed->link			= $this->env->link;
			$feed->title		= "Calendar Raids";
			$feed->description	= $this->config->get('main_title')." EQdkp-Plus - Calendar Raids";
			$feed->published	= $this->time->time;
			$feed->language		= 'EN-EN';

			// Load the raidplan pdh data
			$intNumber = 15;
			
			$arrRaidIDlist = $this->pdh->get('calendar_events', 'id_list', array(true, $this->time->time));
			$arrRaidIDlist = $this->pdh->sort($arrRaidIDlist, 'calendar_events', 'date', 'asc');
			if (is_array($arrRaidIDlist)) {
				$arrRaidIDlist = array_slice($arrRaidIDlist, 0, $intNumber);
			}

			if (is_array($arrRaidIDlist)){
				foreach ($arrRaidIDlist as $intRaidID){
					$eventextension	= $this->pdh->get('calendar_events', 'extension', array($intRaidID));
					$raidmode		= ((int)$this->pdh->get('calendar_events', 'calendartype', array($intRaidID)) == 1) ? true : false;
					$eventcolor		= $this->pdh->get('calendars', 'color', $this->pdh->get('calendar_events', 'calendar_id', array($intRaidID)));

					// fetch the attendees
					$attendees_raw = $this->pdh->get('calendar_raids_attendees', 'attendees', array($intRaidID));
					$attendees = array();
					if(is_array($attendees_raw)){
						foreach($attendees_raw as $attendeeid=>$attendeerow){
							$attendees[$attendeerow['signup_status']][$attendeeid] = $attendeerow;
						}
					}

					// fetch per raid data
					$raidcal_status = unserialize($this->config->get('calendar_raid_status'));
					$rstatusdata = array();
					if(is_array($raidcal_status)){
						foreach($raidcal_status as $raidcalstat_id){
							if($raidcalstat_id != 4){
								$actcount  = ((isset($attendees[$raidcalstat_id])) ? count($attendees[$raidcalstat_id]) : 0);
								$rstatusdata['status'.$raidcalstat_id] = array(
									'id'	=> $raidcalstat_id,
									'count'	=> $actcount,
								);
							}
						}
					}
					$rstatusdata['required'] = ((isset($eventextension['attendee_count'])) ? $eventextension['attendee_count'] : 0);

					$memberdata = $this->pdh->get('calendar_raids_attendees', 'myattendees', array($intRaidID, $this->user->data['user_id']));
					if($memberdata['member_id'] > 0){
						$memberstatus = $this->pdh->get('calendar_raids_attendees', 'status', array($intRaidID, $memberdata['member_id']));
					} else {
						$memberstatus = -1;
					}
					
					$placesfree = $rstatusdata['required'] - $rstatusdata['status0']['count'];
					$eventdata	= $this->pdh->get('calendar_events', 'data', array($intRaidID));
					
					$rssitem = registry::register('feeditems');
					$rssitem->title			= $this->time->date('Y-m-d H:i', $this->pdh->get('calendar_events', 'time_start', array($intRaidID))).': '.$this->pdh->get('calendar_events', 'name', array($intRaidID));
					$rssitem->description	= sprintf($this->user->lang('calendar_rss_itemdesc'),$placesfree,$this->time->date('Y-m-d H:i', $eventdata['timestamp_start']-($eventdata['extension']['deadlinedate'] * 3600)));
					$rssitem->link			= $feed->link.'calendar/viewcalraid.php?eventid='.$intRaidID;
					$rssitem->published		= $this->pdh->get('calendar_events', 'time_start', array($intRaidID));
					$rssitem->author		= ($eventextension['raidleader'] > 0) ? implode(', ', $this->pdh->aget('member', 'name', 0, array($eventextension['raidleader']))) : '';
					$rssitem->source		= $feed->link;

					$feed->addItem($rssitem);
				}
			}	
			$feed->save($rssfile);
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_calevents_raids_rss_crontask', calevents_raids_rss_crontask::$shortcuts);
?>