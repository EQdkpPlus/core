<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date:  $
 * -----------------------------------------------------------------------
 * @author      $Author:  $
 * @copyright   2006-2008 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev:  $
 * 
 * $Id:  $
 */

if ( !defined('EQDKP_INC') ){
    header('HTTP/1.0 404 Not Found');exit;
}

class RenderMember
{

	// Init the class
	function RenderMember()
	{
		global $eqdkp_root_path, $eqdkp;
	
		// Load the Class file
		$renderfile = $eqdkp_root_path.'games/'.$eqdkp->config['default_game'].'/rendermember.php';
		if(is_file($renderfile))
		{
		  include($renderfile);
		}       
	}
  
	function getRender($class_id=-1, $race_id=-1, $member_name='', $member_xml=false,$realm='',$options=false)
  	{
		$ret_val = '';
  		if (function_exists('get_RenderImages')) 
  	 	{
  	 		$ret_val = get_RenderImages($class_id, $race_id, $member_name, $member_xml,$realm,$options);
  	 	}
     	return $ret_val ;
	}
  
  
}

?>
