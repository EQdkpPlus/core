<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2009
 * Date:		$Date: 2011-11-29 17:10:46 +0100 (Di, 29 Nov 2011) $
 * -----------------------------------------------------------------------
 * @author		$Author: Godmod $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 11470 $
 * 
 * $Id: add_raid.php 11470 2011-11-29 16:10:46Z Godmod $
 */

if (!defined('EQDKP_INC')){
	die('Do not access this file directly.');
}

if (!class_exists('exchange_add_raid')){
	class exchange_add_raid extends gen_class {
		public static $shortcuts = array('user', 'config', 'pex'=>'plus_exchange', 'pdh', 'time');
		public $options		= array();
		
		/*
		* Required: Raid Date (Format: Y-m-d H:i)
		* Required: Raid Attendees (Member-IDs)
		* Required: EventID
		* Optional: Raid-Note
		* Required: Raid-Value (DKP) 
		*
		* Returns: Status 0 on error, Status 1 and inserted Raid-ID on succes
		*/
		public function post_add_raid($params, $body){
			if ($this->user->check_auth('a_raid_add', false)){
				$xml = simplexml_load_string($body);
				if ($xml){
					//Check required values
					if (!isset($xml->raid_date) && !strlen($xml->raid_date)) return $this->pex->error('required data missing');
					if (!isset($xml->raid_attendees) && !is_object($xml->raid_attendees->member)) return $this->pex->error('required data missing');
					if (!isset($xml->raid_value) && !strlen($xml->raid_value)) return $this->pex->error('required data missing');
					if (!isset($xml->raid_event_id) && !strlen($xml->raid_event_id)) return $this->pex->error('required data missing');
					
					$intRaidDate = $this->time->fromformat($xml->raid_date, "Y-m-d H:i");
					$arrRaidAttendees = array();
					
					$arrMemberIDList = $this->pdh->get('member', 'id_list', array());
					foreach($xml->raid_attendees->member as $objMemberID){
						if (in_array(intval($objMemberID), $arrMemberIDList)) $arrRaidAttendees[] = intval($objMemberID);
					}
					if(count($arrRaidAttendees) == 0) return $this->pex->error('required data missing');
					
					$fltRaidValue = (float)$xml->raid_value;
					$arrEventIDList = $this->pdh->get('event', 'id_list');
					$intRaidEventID = intval($xml->raid_event_id);
					if (!in_array($intRaidEventID, $arrEventIDList)) return $this->pex->error('required data missing');
					
					$strRaidNote = filter_var((string)$xml->raid_note, FILTER_SANITIZE_STRING);				
				
					$raid_upd = $this->pdh->put('raid', 'add_raid', array($intRaidDate, $arrRaidAttendees, $intRaidEventID, $strRaidNote, $fltRaidValue));
					if (!$raid_upd) return $this->pex->error('an error occured');
					$this->pdh->process_hook_queue();
					
					return array('raid_id' => $raid_upd);
				}
				return $this->pex->error('malformed xml');
			} else {
				return $this->pex->error('access denied');
			}
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_exchange_add_raid', exchange_add_raid::$shortcuts);
?>