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
				$withMemberItems = (isset($params['get']['exclude_memberitems']) && $params['get']['exclude_memberitems'] == 'true') ? false : true;
				return $myexp->export($withMemberItems);
			} else {
				return $this->pex->error('access denied');
			}
		}
	}
}
?>