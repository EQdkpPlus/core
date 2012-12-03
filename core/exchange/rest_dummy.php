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

if (!class_exists('exchange_rest_dummy')){
	class exchange_rest_dummy{
       
		var $options = array();
		var $type = 'REST';
	 
		function post_rest_dummy($params, $body){
			global $user, $pdh;
			
			//return '<user_id>'.$user->data['user_id'].'</user_id>';
			
			//Get the User-ID from the SID
			$user_id = $user->check_session($params['get']['s']);
			if ($user_id != ANONYMOUS){
				//Real User
				//you can now use
				//$user->check_auth('permission', false, $user_id)
				return '<response><username>'.$pdh->get('user', 'name', array($user_id)).'</username></response>';
			} else {
				return '<response><username>GUEST</username></response>';		
			}


		}
       
	}
}
?>