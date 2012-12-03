<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2010
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2006-2010 EQdkp-Plus Developer Team
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
class mmocms_language
{
  var $DefaultLang = 'english';

	public function __construct()	{
    global $user, $core;
    $this->portallang = array();
    // Set up language array
		if ((isset($user->data['user_id'])) && ($user->data['user_id'] != ANONYMOUS) && (!empty($user->data['user_lang']))) {
    	$this->langname = $user->data['user_lang'];
		}else{
			$this->langname = $core->config['default_lang'];
		}
	}
	
	public function PortalLanguage($portalplugins){
    global $eqdkp_root_path;

    $portallang = is_array($portallang) ? $portallang : array();
    if (is_array($portalplugins)) 
    {
	    foreach($portalplugins as $portalplugin=>$isplugin)
	    {
        if(!$isplugin){
  	      $modulelang = $eqdkp_root_path.'portal/'.$portalplugin.'/language/'.$this->langname.'.php';
  	      if(is_file($modulelang))
  	      {
  	        include($modulelang);
						$portallang = array_merge($portallang, $lang);
  	      }else
  	      {
  	      	$inc_path = $eqdkp_root_path.'portal/'.$portalplugin.'/language/'.$this->DefaultLang.'.php' ;
  	      	if (file_exists($inc_path)) 
  	      	{
  	      		include($inc_path);
							$portallang = array_merge($portallang, $lang);   	
  	      	}	        
  	      }
	      }
	    }    	
    }

    return $portallang;
  }
}	
?>