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

if (!class_exists('exchange_get_salt')){
	class exchange_get_salt{
       
		var $options = array();
		var $type = 'REST';
	 
		function post_get_salt($params, $body){
			global $user, $db;
			
			$xml = simplexml_load_string($body);		
			if ($xml && $xml->user){

				$query = $db->query("SELECT user_password FROM __users WHERE username='".$db->escape($xml->user)."'");
				while ($row = $db->fetch_record($query)){
					list($user_password, $user_salt) = explode(':', $row['user_password']);
					$result = $user_salt;
				}
				
			}
			
			return '<response><salt>'.base64_encode($result).'</salt></response>';
		}
       
	}
}
?>