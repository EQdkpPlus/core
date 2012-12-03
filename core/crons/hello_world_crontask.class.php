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

if ( !defined('EQDKP_INC') ){
  header('HTTP/1.0 404 Not Found');exit;
}

if ( !class_exists( "hello_world_crontask" ) ) {
  class hello_world_crontask extends crontask{ 
    public function __construct(){
      $this->defaults['params'] = array("world");
      $this->defaults['active'] = true;     
			//$this->defaults['repeat'] = true;
			$this->defaults['repeat_interval'] = 2;
			$this->defaults['repeat_type'] = 'minutely';
			$this->defaults['multiple'] = true;
			$this->defaults['ajax'] = false;
			$this->defaults['delay'] = false;
			$this->defaults['editable'] = true;
    }
    
    public function run($name){
      global $core;
      $core->message("Hello $name");
    }
  }
}
?>