<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:	     	http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2009
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2009 sz3
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev$
 * 
 * $Id$
 */
 
if (!defined('EQDKP_INC'))
{
  die('Do not access this file directly.');
}

if (!class_exists('exchange_login')){
	class exchange_login{
       
		var $options = array();
		var $type = 'REST';
	 
		function post_login($params, $body){
			global $user, $core;
			$xml = simplexml_load_string($body);
			if ($xml && $xml->password && $xml->user){
				if ($user->login($xml->user, $xml->password, false, true)){
					$result =  '<response><sid>'.$user->sid.'</sid><end>'.(time()+$core->config['session_length']).'</end></response>';
					return $result;
				}
				
			}
			return '<response><sid></sid><end></end></response>';
		}
       
	}
}
?>
