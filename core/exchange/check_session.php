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

if (!class_exists('exchange_check_session')){
	class exchange_check_session{
       
		var $options = array();
		var $type = 'REST';
	 
		function post_check_session($params, $body){
			global $user;
			
			$xml = simplexml_load_string($body);
			if ($xml && $xml->sid){
				$result = $user->check_session($xml->sid);
			}
			
			return '<response><user_id>'.$result.'</user_id></response>';
		}
       
	}
}
?>
