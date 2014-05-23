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