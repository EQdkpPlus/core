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

if (!class_exists('exchange_logout')){
	class exchange_logout{
       
		var $options = array();
		var $type = 'REST';
	 
		function post_logout($params, $body){
			global $user;
			
			if ($xml && $xml->sid){	
				$user->destroy_sid($xml->sid);
			}
			
			return '<response><result>true</result></response>';
		}
       
	}
}
?>
