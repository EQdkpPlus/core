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

if (!class_exists('exchange_login')){
	class exchange_login extends gen_class{
		public static $shortcuts = array('user','config', 'pex'=>'plus_exchange');

		public function post_login($params, $body){
			$xml = simplexml_load_string($body);
			if ($xml && $xml->password && $xml->user){
				if ($this->user->login($xml->user, $xml->password, false, true)){
					$result =  array(
						'sid'	=> $this->user->sid,
						'end'	=> time()+$this->config->get('session_length'),
					);
					return $result;
				}
			}
			return $this->pex->error('access denied');
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_exchange_login', exchange_login::$shortcuts);
?>