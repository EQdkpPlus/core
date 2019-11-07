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

if (!defined('EQDKP_INC')){
	die('Do not access this file directly.');
}

if (!class_exists('exchange_raids')) {
    class exchange_raids extends gen_class {
        public static $shortcuts = array('user', 'config', 'pex'=>'plus_exchange', 'pdh', 'time', 'env' => 'environment');
        public $options		= array();

        public function get_raids($params, $arrBody) {
		    $isAPITokenRequest = $this->pex->getIsApiTokenRequest();

		    if($isAPITokenRequest || $this->user->check_pageobjects(array('raids'), 'AND', false)){
		    	$raidlist = $this->pdh->get('raid', 'id_list');
		    	$raidlist = $this->pdh->sort($raidlist, 'raid', 'date', 'desc');
		    	$intNumber = (intval($params['get']['number']) > 0) ?  intval($params['get']['number']) : false;
		    	$intStart = (intval($params['get']['number']) > 0) ?  intval($params['get']['start']) : 0;

		    	if($intNumber !== false){
		    		$raidlist = $this->pdh->limit($raidlist, $intStart, $intNumber);
		    	}

		    	$out = array();
		    	foreach ($raidlist as $key => $raid_id){
		    		$date_raw	= $this->pdh->get('raid', 'date', array($raid_id));
		    		$date_info	= $this->pdh->get('raid', 'date', array($raid_id));
		    		$date_info	= date("Y-m-d H:i:s", $date_raw);
		    		$added_by	= $this->pdh->get('raid', 'added_by', array($raid_id));
		    		$event_name	= unsanitize($this->pdh->get('raid', 'event_name', array($raid_id)));
		    		$event_id	= unsanitize($this->pdh->get('raid', 'event', array($raid_id)));
		    		$raid_note	= unsanitize($this->pdh->get('raid', 'note', array($raid_id)));
		    		$added_by_name	= unsanitize($this->pdh->get('user', 'name', array($added_by)));
		    		$raid_value	= $this->pdh->get('raid', 'value', array($raid_id));
				$raid_attendees	= $this->pdh->get('raid', 'raid_attendees', array($raid_id));

		    		$out['raid:'.$raid_id] = array(
		    				'id'			=> $raid_id,
		    				'date'			=> $date_info,
		    				'date_timestamp'	=> $date_raw,
		    				'note'			=> $raid_note,
		    				'event_id'		=> $event_id,
		    				'event_name'		=> $event_name,
		    				'added_by_id'		=> 0,
		    				'added_by_name'		=> $added_by,
		    				'value'			=> runden($raid_value),
		    				'raid_attendees'	=> $raid_attendees
		    		);
		    	}

		    	return $out;
		    } else {
		        return $this->pex->error('access denied');
		    }
        }
    }
}
