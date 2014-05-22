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
		public static $shortcuts = array('user', 'pex'=>'plus_exchange');
		public $options		= array();

		public function get_points($params, $body){
			if ($this->user->check_auth('u_event_view', false) && $this->user->check_auth('u_member_view', false) && $this->user->check_auth('u_item_view', false)){
				include_once($eqdkp_root_path . 'core/data_export.class.php');
				$myexp = new content_export();
				$withMemberItems = (isset($params['get']['exclude_memberitems']) && $params['get']['exclude_memberitems'] == 'true') ? false : true;
				$blnExcludeHTML = (isset($params['get']['exclude_html']) && $params['get']['exclude_html'] == 'true') ? true : false;

				return $myexp->export($withMemberItems, $blnExcludeHTML);
			} else {
				return $this->pex->error('access denied');
			}
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_exchange_points', exchange_points::$shortcuts);
?>