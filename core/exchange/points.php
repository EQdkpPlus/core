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

if (!class_exists('exchange_points')){
	class exchange_points extends gen_class{
		public static $shortcuts = array('pex'=>'plus_exchange');
		public $options		= array();

		public function get_points($params, $body){
			if($this->user->check_pageobjects(array('points'), 'AND', false))
			{
				include_once($this->root_path . 'core/data_export.class.php');
				$myexp = new content_export();
				
				//MemberData
				$arrBooleansMemberdata = array();
				if(isset($params['get']['memberdata']) && strlen($params['get']['memberdata'])){
					//Available Memberdata
					$arrMemberdata = array('items', 'adjustments');
					
				
					if(strpos($params['get']['memberdata'], ',')){
						$arrData = explode(",", $params['get']['memberdata']);
					} else {
						$arrData = array($params['get']['memberdata']);
					}
					foreach($arrData as $strData){
						if (in_array($strData, $arrMemberdata)){
							$arrBooleansMemberdata[$strData] = true;
						}
					}
				}
				
				//IncludeHTML
				$blnIncludeHTML = (isset($params['get']['include_html']) && $params['get']['include_html'] == 'true') ? true : false;
				
				//Filter
				$filter = $filterid = false;
				if (isset($params['get']['filter']) && in_array($params['get']['filter'], array('user', 'character')) && isset($params['get']['filterid'])){
					$filter = $params['get']['filter'];
					$filterid = intval($params['get']['filterid']);
				}
				
				return $myexp->export((isset($arrBooleansMemberdata['items']) && $arrBooleansMemberdata['items']), (isset($arrBooleansMemberdata['adjustments']) && $arrBooleansMemberdata['adjustments']), $filter, $filterid, $blnIncludeHTML);
			} else {
				return $this->pex->error('access denied');
			}
		}
	}
}
?>