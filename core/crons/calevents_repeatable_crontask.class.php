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

if(!class_exists("calevents_repeatable_crontask")){
	class calevents_repeatable_crontask extends crontask{
		public static $shortcuts = array('time', 'config', 'pdh');
		
		public function __construct(){
			$this->defaults['active']			= true;
			$this->defaults['repeat']			= true;
			$this->defaults['repeat_type']		= 'daily';
			$this->defaults['repeat_interval']	= 1;
			$this->defaults['ajax']				= true;
			$this->defaults['description']		= 'Creating repeatable events';
		}

		public function run(){
			// fetch the raid ids of the repeatable raids
			$repeatable_events = $this->pdh->get('calendar_events', 'repeatable_events');
			if(is_array($repeatable_events) && count($repeatable_events) > 0){
				foreach($repeatable_events as $parentid=>$a_childid){

					// the ID of the parent event have to be higher 0
					if($parentid > 0){
						$eventsdata			= $this->pdh->get('calendar_events', 'data', array($parentid));
						$start_timestamp	= $this->pdh->get('calendar_events', 'time_start', array($parentid));
						$end_timestamp		= $this->pdh->get('calendar_events', 'time_end', array($parentid));
						$eventduration		= $end_timestamp - $start_timestamp;
						$end_cronjob		= $this->time->time+((($this->config->get('calendar_repeat_crondays') > 0) ? $this->config->get('calendar_repeat_crondays') : 40) * 86400);

						switch($this->pdh->get('calendar_events', 'repeating', array($parentid))){
							case 'day':			$rptbl_period = 86400; break;
							case 'week':		$rptbl_period = 604800; break;
							case 'twoweeks':	$rptbl_period = 1209600; break;
							default:			$rptbl_period = 604800; break;
						}

						// if the calendar id is < 1, continue
						if($eventsdata['calendar_id'] < 1){
							continue;
						}

						// get the highest id of the clones and check if we have to add a new one
						$max_childid	= max($a_childid);
						$date_max_child	= $this->pdh->get('calendar_events', 'time_start', array($max_childid));
						$date_event_add	= $date_max_child+$rptbl_period;
						// summertime handling
						$date_event_add = $this->handle_summertime($date_event_add, $date_max_child);

						// add an event if needed
						while($date_event_add < $end_cronjob){
							$clone_starttimestamp	= $date_event_add;
							$clone_endtimestamp		= $date_event_add + $eventduration;

							$a_data = array(
								$eventsdata['calendar_id'],
								$eventsdata['name'],
								$eventsdata['creator'],
								$clone_starttimestamp,
								$clone_endtimestamp,
								$eventsdata['repeating'],
								$eventsdata['notes'],
								$eventsdata['allday']
							);
							if($eventsdata['extension']['calendarmode'] == 'raid'){
								array_push($a_data, array(
									'raid_eventid'		=> $eventsdata['extension']['raid_eventid'],
									'calendarmode'		=> $eventsdata['extension']['calendarmode'],
									'raid_value'		=> $eventsdata['extension']['raid_value'],
									'deadlinedate'		=> $eventsdata['extension']['deadlinedate'],
									'raidmode'			=> $eventsdata['extension']['raidmode'],
									'raidleader'		=> $eventsdata['extension']['raidleader'],
									'distribution'		=> $eventsdata['extension']['distribution'],
									'attendee_count'	=> $eventsdata['extension']['attendee_count'],
									'created_on'		=> $this->time->time,
									));
								}else{
								array_push($a_data, false);
							}
							array_push($a_data, $parentid);
							$clonedraidid = $this->pdh->put('calendar_events', 'add_cevent', $a_data);
							$this->pdh->put('calendar_events', 'auto_addchars', array($eventsdata['extension']['raidmode'], $clonedraidid));

							// set the date for the next event and handle summertime stuff
							$date_event_add = $this->handle_summertime($date_event_add+$rptbl_period, $date_event_add);
						}
					}
				} // end of foreach
			}
			$this->pdh->process_hook_queue();
		}
		
		// handle sumertime
		private function handle_summertime($toadd, $before) {
			// first raid from winter to summer needs -3600;
			if($this->time->date('I', $toadd, false) == '1' && $this->time->date('I', $before, false) == '0') $toadd -= 3600;
			//  first raid from summer to winter needs +3600
			elseif($this->time->date('I', $toadd, false) == '0' && $this->time->date('I', $before, false) == '1') $toadd += 3600;
			return $toadd;
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_calevents_repeatable_crontask', calevents_repeatable_crontask::$shortcuts);
?>