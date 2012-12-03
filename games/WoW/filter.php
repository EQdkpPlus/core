<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date: 2008-07-29 00:22:12 +0200 (Di, 29 Jul 2008) $
 * -----------------------------------------------------------------------
 * @author      $Author: osr-corgan $
 * @copyright   2006-2008 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev: 2449 $
 * 
 * $Id: icons.php 2449 2008-07-28 22:22:12Z osr-corgan $
 */
 
if ( !defined('EQDKP_INC') )
{
    header('HTTP/1.0 404 Not Found');
    exit;
}

class GameFilter
{
  function addFilter($filter)
  {

  		$filter_list = array();
  	
	 	#Pool1: Rogue, Shaman, Paladin  = 1,12,6,7
		###############################################
	    $a_filterIds = array();
	    $a_filterIds[] = '(class_id=5)';
	    $a_filterIds[] = 'or (class_id=13)';
	    $a_filterIds[] = 'or (class_id=2)';
	    $a_filterIds[] = 'or (class_id=8)';
	   	$a_filterIds[] = 'or (class_id=9)';
	    $filter_list['pool1'] = $this->assing_filter_rows($a_filterIds,'filter_row',$filter,'pool1','[Tier4+5 Pool1]');

	 	#Pool2: Warrior, Priest, Druid = 1,12,6,7
		###############################################
		$a_filterIds = array();
	    $a_filterIds[] = '(class_id=1)';
	    $a_filterIds[] = 'or (class_id=12)';
	    $a_filterIds[] = 'or (class_id=6)';
	    $a_filterIds[] = 'or (class_id=7)';
	    $filter_list['pool2'] = $this->assing_filter_rows($a_filterIds,'filter_row',$filter,'pool2','[Tier4+5 Pool2]');

		#Pool3: hunter, mage,  = 3,4,11,10
		###############################################
		$a_filterIds = array();
	    $a_filterIds[] = '(class_id=3)';
	    $a_filterIds[] = 'or (class_id=4)';
	    $a_filterIds[] = 'or (class_id=11)';
	    $a_filterIds[] = 'or (class_id=10)';
	     $filter_list['pool3'] = $this->assing_filter_rows($a_filterIds,'filter_row',$filter,'pool3','[Tier4+5 Pool3]');

		#Pool4: Tier6+7 warrior, hunter, shaman
		###############################################
		$a_filterIds = array();
	    $a_filterIds[] = '(class_id=3)';
	    $a_filterIds[] = 'or (class_id=4)';
	    $a_filterIds[] = 'or (class_id=1)';
	    $a_filterIds[] = 'or (class_id=12)';
	    $a_filterIds[] = 'or (class_id=8)';
	    $a_filterIds[] = 'or (class_id=9)';
	     $filter_list['pool4'] = $this->assing_filter_rows($a_filterIds,'filter_row',$filter,'pool4','[Tier6+7 Pool1]');

		#Pool5: Tier6+7 rogue, mage, druid
		###############################################
		$a_filterIds = array();
	    $a_filterIds[] = '(class_id=2)';
	    $a_filterIds[] = 'or (class_id=11)';
	    $a_filterIds[] = 'or (class_id=7)';
	    $a_filterIds[] = 'or (class_id=20)';
	     $filter_list['pool5'] = $this->assing_filter_rows($a_filterIds,'filter_row',$filter,'pool5','[Tier6+7 Pool2]');

		#Pool6: Tier6+7 Paladin, priest, warlock
		###############################################
		$a_filterIds = array();
	    $a_filterIds[] = '(class_id=5)';
	    $a_filterIds[] = 'or (class_id=13)';
	    $a_filterIds[] = 'or (class_id=6)';
	    $a_filterIds[] = 'or (class_id=10)';
	    $filter_list['pool6'] = $this->assing_filter_rows($a_filterIds,'filter_row',$filter,'pool6','[Tier6+7 Pool3]');	  
   
	    return $filter_list; 
	    
  }
  
function assing_filter_rows($a_filterIds, $to_assingVar='', $filter='',$value='', $name)
{
	global $db,$tpl;

	$sql = 'SELECT class_name, class_id, class_min_level, class_max_level FROM ' . CLASS_TABLE .'';
	$implodestring = implode(' ',$a_filterIds);
	$sql .= ' WHERE '. $implodestring ;
  	$result = $db->query($sql);

  	if($result)
	{
	  $poo1 = array();
	  while ( $row = $db->fetch_record($result) )
	  {

	  	$poo1[] = $row['class_name'];}
		$poo1_classes = $name.' ';

	  	foreach(array_unique($poo1) as $key)
	  	{
	  		$poo1_classes .= $key.", ";
	  	}

  		$tpl->assign_block_vars($to_assingVar, array(
  		  'VALUE' => $value,
	      'SELECTED' => ( strtolower($filter) == strtolower($value) ) ? ' selected="selected"' : '',
	      'OPTION'   => ( !empty($poo1_classes) ) ? stripslashes($poo1_classes) : '(None)' )
	      );
    }
    return $implodestring ;
}  
  
}
  

?>
