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

if ( !defined('EQDKP_INC') )
{
    header('HTTP/1.0 404 Not Found');exit;
}
class PlusLanguage
{
  var $DefaultLang = 'english';

	function PlusLanguage()	{
    global $user, $eqdkp;
    $this->portallang = array();
    // Set up language array
		if ((isset($user->data['user_id'])) && ($user->data['user_id'] != ANONYMOUS) && (!empty($user->data['user_lang']))) {
    	$this->langname = $user->data['user_lang'];
		}else{
			$this->langname = $eqdkp->config['default_lang'];
		}
	}
	
	function PortalLanguage($portalplugins){
    global $eqdkp_root_path, $plang;
    $plang = is_array($plang) ? $plang : array();
    if (is_array($portalplugins)) 
    {
	    foreach($portalplugins as $portalplugin=>$isplugin)
	    {
        if(!$isplugin){
  	      $modulelang = $eqdkp_root_path.'portal/'.$portalplugin.'/language/'.$this->langname.'.php';
  	      if(is_file($modulelang))
  	      {
  	        include($modulelang);
  	      }else
  	      {
  	      	$inc_path = $eqdkp_root_path.'portal/'.$portalplugin.'/language/'.$this->DefaultLang.'.php' ;
  	      	if (file_exists($inc_path)) 
  	      	{
  	      		include($inc_path);   	
  	      	}	        
  	      }
	      }
	    }    	
    }
    return $plang;
  }
	
	function NormalLanguage(){
    global $eqdkp_root_path, $plang;
    $plang          = is_array($plang) ? $plang : array();
		$pluslang_file  = $eqdkp_root_path.'pluskernel/language/'.$this->langname.'/lang_main.php';
		
    // Check if the file is there, if not use the fallback to english...
    if(is_file($pluslang_file)){
		  include($pluslang_file);
		}else{
      $include($eqdkp_root_path.'pluskernel/language/'.$this->DefaultLang.'/lang_main.php');
    }
    return $plang;
  }
}	
?>