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

if (!defined('EQDKP_INC')){
	die('Do not access this file directly.');
}

if (!class_exists('exchange_add_item')){
	class exchange_add_item extends gen_class {
		public static $shortcuts = array('pex'=>'plus_exchange');
		public $options		= array();
		
		/*
		* Required: Item Date (Format: Y-m-d H:i)
		* Required: Item Name
		* Required: Buyer IDs (EQdkp Character ID)
		* Required: Raid ID
		* Required: Item Value
		* Required: Itempool ID
		* Optional: Item Ingame ID
		*
		* Returns: Status 0 on error, Status 1 and inserted Item-ID on succes
		*/
		public function post_add_item($params, $body){
			if ($this->user->check_auth('a_item_add', false)){
				$xml = simplexml_load_string($body);
				if ($xml){
					//Check required values
					if (!isset($xml->item_date) && !strlen($xml->item_date)) return $this->pex->error('required data missing');
					if (!isset($xml->item_name) && !strlen($xml->item_name)) return $this->pex->error('required data missing');
					if (!isset($xml->item_buyers) && !is_object($xml->item_buyers->member)) return $this->pex->error('required data missing');
					if (!isset($xml->item_raid_id) && !strlen($xml->item_raid_id)) return $this->pex->error('required data missing');
					if (!isset($xml->item_value) && !strlen($xml->item_value)) return $this->pex->error('required data missing');
					if (!isset($xml->item_itempool_id) && !strlen($xml->item_itempool_id)) return $this->pex->error('required data missing');
					
					//Item Date
					$intItemDate = $this->time->fromformat($xml->item_date, "Y-m-d H:i");
					
					//Item Buyers
					$arrItemBuyers = array();
					$arrMemberIDList = $this->pdh->get('member', 'id_list', array());
					foreach($xml->item_buyers->member as $objMemberID){
						if (in_array(intval($objMemberID), $arrMemberIDList)) $arrItemBuyers[] = intval($objMemberID);
					}
					if(count($arrItemBuyers) == 0) return $this->pex->error('required data missing');
					
					//Item Value
					$fltItemValue = (float)$xml->item_value;
					
					//Item Name
					$strItemName = filter_var((string)$xml->item_name, FILTER_SANITIZE_STRING);	
					
					//Item Raid ID
					$arrRaidIDList = $this->pdh->get('raid', 'id_list');
					$intRaidID = intval($xml->item_raid_id);
					if (!in_array($intRaidID, $arrRaidIDList)) return $this->pex->error('required data missing');
					
					//Item Itempool ID
					$arrItempoolIDList =  $this->pdh->get('itempool', 'id_list');
					$intItempoolID = intval($xml->item_itempool_id);
					if (!in_array($intItempoolID, $arrItempoolIDList)) return $this->pex->error('required data missing');
					
					//Item Ingame ID
					$intIngameID = (isset($xml->item_game_id)) ? filter_var((string)$xml->item_game_id, FILTER_SANITIZE_STRING) : '';	
					
					$mixItemID = $this->pdh->put('item', 'add_item', array($strItemName, $arrItemBuyers, $intRaidID, $intIngameID, $fltItemValue, $intItempoolID, $intItemDate));
					if (!$mixItemID) return $this->pex->error('an error occured');
					$this->pdh->process_hook_queue();
					
					return array('item_id' => $mixItemID);
				}
				return $this->pex->error('malformed xml');
			} else {
				return $this->pex->error('access denied');
			}
		}
	}
}
?>