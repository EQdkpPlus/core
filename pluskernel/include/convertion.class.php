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
 * @version     $Rev: 516 $
 * 
 * $Id:  $
 */

if ( !defined('EQDKP_INC') ){
    header('HTTP/1.0 404 Not Found');exit;
}

class PlusConvertions
{

  // Init the class
  function PlusConvertions()
  {
    global $eqdkp_root_path, $eqdkp;
    
    // Load the Class file
    $classfiles = $eqdkp_root_path.'games/'.$eqdkp->config['default_game'].'/convertion.php';
    if(is_file($classfiles))
    {
      include($classfiles);
    }
    
    // Fill the Lang to EN array
    if(is_array($classconvert_array))
    {
		foreach($classconvert_array as $langclasses)
		{
        	$this->LANGtoEN = (is_array($this->LANGtoEN)) ? array_merge($langclasses, $this->LANGtoEN) : $langclasses;
      	}
    }
    
    // Fill the EN to LANG array   
    
    #d($classconvert_array);
    #$this->ENtoLANG = (is_array($classconvert_array)) ? array_flip($classconvert_array) : array();    
    #d($this->ENtoLANG);    
    #@Wallenium KA wofür das ist. Jedenfalls funktioniert es nicht, da in $classconvert_array zwei mehrdimensionale Arrays enthalten sind.
    
  }
  
  // Class name to EN
  function classname($classname, $tolanguage='')
  {
  	
  	
    if($tolanguage)
    {
      $tmpclassname = $this->ENtoLANG[$tolanguage][$classname];
    }else
    {
      $tmpclassname = $this->LANGtoEN[$classname];
    }
    
    return ($tmpclassname) ? $tmpclassname : $classname;
  }
}

?>
