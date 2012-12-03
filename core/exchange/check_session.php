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

if (!class_exists('exchange_check_session')){
	class exchange_check_session extends gen_class {
		public static $shortcuts = array('user');

		public function post_check_session($params, $body){
			$xml = simplexml_load_string($body);
			$status = 0;
			if ($xml && $xml->sid){
				$result = $this->user->check_session($xml->sid);
				if ($result != ANONYMOUS){
					$status = 1;
				} else {
					$status = 0;
				}
			}
			return array('valid' => $result);
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_exchange_check_session', exchange_check_session::$shortcuts);
?>