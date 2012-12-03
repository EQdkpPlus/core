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

if (!class_exists('exchange_members')){
	class exchange_members {
       
		var $options = array();
		var $type = 'REST';
	 
		function post_members($params, $body){
			global $user, $pdh, $xmltools, $db, $game;
			if ($user->check_auth('u_member_list', false) && $user->check_auth('u_member_view', false)){
				$xml = simplexml_load_string($body);
				
				if ($xml && $xml->user){

					$query = $db->query("SELECT user_id FROM __users WHERE username='".$db->escape($xml->user)."'");
					$result = $db->fetch_record($query);
					if ($result['user_id']){			
						$member_list = $pdh->get('member_connection', 'connection', array($result['user_id']));
						$out = '';
						if (is_array($member_list)){
							foreach ($member_list as $member){
								$out .= '<member>';
								
								$out .= '<id>'.$member['member_id'].'</id>';
								$out .= '<name>'.$member['member_name'].'</name>';
								$out .= '<level>'.$member['member_level'].'</level>';
								$out .= '<type>'.$pdh->get('member', 'twink', array($member['member_id'])).'</type>';								
								
								$out .= '</member>';
							}

							return '<response><members>'.$out.'</members></response>';
						}
					}
				}
				return '<response><members></members></response>';
			} else {
			
				return '<response><error>Access denied</error></response>';
			}
			
		}
       
	}
}
?>