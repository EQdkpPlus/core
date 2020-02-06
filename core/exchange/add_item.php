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
		public function post_add_item($params, $arrBody){
			$isAPITokenRequest = $this->pex->isApiWriteTokenRequest();

			if ($isAPITokenRequest || $this->user->check_auth('a_item_add', false)){
				$blnTest = (isset($params['get']['test']) && $params['get']['test']) ? true : false;

				if (count($arrBody)){
					//Check required values
					if (!isset($arrBody['item_date']) || !strlen($arrBody['item_date'])) return $this->pex->error('required data missing', 'item_date');
					if (!isset($arrBody['item_name']) || !strlen($arrBody['item_name'])) return $this->pex->error('required data missing', 'item_name');
					if (!isset($arrBody['item_buyers']) || !count($arrBody['item_buyers']['member'])) return $this->pex->error('required data missing', 'item_buyers');
					if (!isset($arrBody['item_raid_id']) || !strlen($arrBody['item_raid_id'])) return $this->pex->error('required data missing', 'item_raid_id');
					if (!isset($arrBody['item_value']) || !strlen($arrBody['item_value'])) return $this->pex->error('required data missing', 'item_value');
					if (!isset($arrBody['item_itempool_id']) || !strlen($arrBody['item_itempool_id'])) {
						$arrItempoolIDList =  $this->pdh->get('itempool', 'id_list');
						if(count($arrItempoolIDList) > 1){
							return $this->pex->error('required data missing', 'item_itempool_id');
						} else {
							$arrBody['item_itempool_id'] = $arrItempoolIDList[0];
						}
					}

					//Item Date
					$intItemDate = $this->time->fromformat($arrBody['item_date'], "Y-m-d H:i");

					//Item Buyers
					$arrItemBuyers = array();
					$arrMemberIDList = $this->pdh->get('member', 'id_list', array());

					if(is_array($arrBody['item_buyers']['member'])){
						foreach($arrBody['item_buyers']['member'] as $objMemberID){
							if (in_array(intval($objMemberID), $arrMemberIDList)) $arrItemBuyers[] = intval($objMemberID);
						}
					} else {
						$objMemberID = intval($arrBody['item_buyers']['member']);
						if (in_array(intval($objMemberID), $arrMemberIDList)) $arrItemBuyers[] = intval($objMemberID);
					}

					if(count($arrItemBuyers) == 0) return $this->pex->error('required data missing', 'no member found');

					//Item Value
					$fltItemValue = (float)$arrBody['item_value'];

					//Item Name
					$strItemName = filter_var((string)$arrBody['item_name'], FILTER_SANITIZE_STRING);

					//Item Raid ID
					$arrRaidIDList = $this->pdh->get('raid', 'id_list');
					$intRaidID = intval($arrBody['item_raid_id']);
					if (!in_array($intRaidID, $arrRaidIDList)) return $this->pex->error('required data missing', 'raid does not exist');

					//Item Itempool ID
					$arrItempoolIDList =  $this->pdh->get('itempool', 'id_list');
					$intItempoolID = intval($arrBody['item_itempool_id']);
					if (!in_array($intItempoolID, $arrItempoolIDList)) return $this->pex->error('required data missing', 'itempool does not exist');

					//Item Ingame ID
					$intIngameID = (isset($arrBody['item_game_id'])) ? filter_var((string)$arrBody['item_game_id'], FILTER_SANITIZE_STRING) : '';

					if($blnTest) return array('test' => 'success');

					$mixItemID = $this->pdh->put('item', 'add_item', array($strItemName, $arrItemBuyers, $intRaidID, $intIngameID, $fltItemValue, $intItempoolID, $intItemDate));
					if (!$mixItemID) return $this->pex->error('an error occured');
					$this->pdh->process_hook_queue();

					return array('item_id' => $mixItemID);
				}
				return $this->pex->error('malformed input');
			} else {
				return $this->pex->error('access denied');
			}
		}
	}
}
