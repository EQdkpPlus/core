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

if (!class_exists('exchange_logout')){
	class exchange_logout extends gen_class{
		public static $shortcuts = array('user', 'pex'=>'plus_exchange');

		public function post_logout($params, $body){
			$xml = simplexml_load_string($body);
			if ($xml && $xml->sid){
				$this->user->sid = $xml->sid;
				$this->user->destroy();
				return array('result' => 1);
			}
			$this->pex->error('no sid given');
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_exchange_logout', exchange_logout::$shortcuts);
?>