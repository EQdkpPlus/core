<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2006-2008 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev$
 * 
 * $Id$
 */

if ( !defined('EQDKP_INC') ){
    header('HTTP/1.0 404 Not Found');exit;
}

/**
 * Return Renderimages of a given member.
 *
 * @param integer $class_id
 * @param integer $race_id
 * @param string $member_name
 * @return html string
 */
function get_RenderImages($class_id=-1, $race_id=-1, $member_name='', $member_xml=false,$realm='')
{
	global $db, $eqdkp, $user, $conf_plus,$eqdkp_root_path;
	$ret_val = false ;

	$img_folder = $eqdkp_root_path.'games/'.$eqdkp->config['default_game']."/3dmodel/" ;

	if ( (($race_id == -1) or ($class_id == -1)) and (strlen($member_name) >1) )
	{
		$sql = "SELECT member_class_id, member_race_id from __members WHERE member_name ='".$member_name."'" ;
		$result = $db->query($sql);
		$row = $db->fetch_record($result);
		$race_id = $row['member_race_id'];
		$class_id = $row['member_class_id'];
	}


	$imgs = array();

	$gender = '0';
	if (get_Gender($member_name)=='Female') {
		$gender = '1';
	}

	$imgs[] = $img_folder.$class_id."_".$race_id."_".$gender.'.jpg' ; //T4
	

	foreach($imgs as $value)
	{
		 if(file_exists($value))
		 {
		 	$ret_val .= '<img src='.$value.'> &nbsp;&nbsp;' ;
		 }
	}

	
	return 	$ret_val ;

}# end function

?>