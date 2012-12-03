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

/**
 * Return Renderimages of a given member.
 *
 * @param integer $class_id
 * @param integer $race_id
 * @param string $member_name
 * @return html string
 */
function get_RenderImages($class_id=-1, $race_id=-1, $member_name='', $member_xml=false,$realm='',$options=false)
{
	global $db, $eqdkp, $user, $conf_plus,$eqdkp_root_path;
	$ret_val = false ;

	if( version_compare(phpversion(), "5.0.0", ">=")  and (strlen($realm)>1 ) and (!$conf_plus['pk_disable_3dmember']==1) )	 
	{					
		include $eqdkp_root_path.'/pluskernel/include/wow_modelviewer.class.php';	
		$wow_modelviewer = new wow_modelviewer();			
		$ret_val = $wow_modelviewer->wow_charviewer($member_name,$member_xml,$options);	
		
		if ($ret_val) 
		{
			$wowhead  = "<a class='copy' href=viewmember.php?s=&name=$member_name&model=flash1> WoWHead Flash </a>";
			$thot  = "<a class='copy' href=viewmember.php?s=&name=$member_name&model=flash2> Thotbot Flash </a>";
			$java   = "<a href=viewmember.php?s=&name=$member_name&model=Java> SpeedyDragon Java </a>";		
			$ret_val =	"<table border=0><tr><td>$ret_val</td></tr><tr><td align=center> $wowhead | $thot |  $java  </td></tr></table>";	
		}
		
	}
	
	if(!$ret_val) 
	{
		$img_folder = $eqdkp_root_path.'games/'.$eqdkp->config['default_game']."/3dmodel/" ;
	
		if ( (($race_id == -1) or ($class_id == -1)) and (strlen($member_name) >1) )
		{
			$sql = "SELECT member_class_id, member_race_id from ".MEMBERS_TABLE. " WHERE member_name ='".$member_name."'" ;
			$result = $db->query($sql);
			$row = $db->fetch_record($result);
			$race_id = $row['member_race_id'];
			$class_id = $row['member_class_id'];
		}
	
	
		$imgs = array();
	
		$gender = 'm';
		if (get_Gender($member_name)=='Female') {
			$gender = 'w';
		}
	
		$imgs[] = $img_folder.$class_id.$race_id.'4'.$gender.'.png' ; //T4
		$imgs[] = $img_folder.$member_name.'.png' ; //Member
		$imgs[] = $img_folder.$class_id.$race_id.'5'.$gender.'.png' ; //T5
		$imgs[] = $img_folder.$class_id.$race_id.'6'.$gender.'.png' ; //T6
		
		foreach($imgs as $value)
		{
			 if(file_exists($value))
			 {
			 	$ret_val .= '<img src='.$value.'> &nbsp;&nbsp;' ;
			 }
		}
	}
	
	return 	$ret_val ;

}# end function

?>
