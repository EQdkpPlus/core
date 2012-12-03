<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		    http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2010
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2010 sz3
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev$
 *
 * $Id$
 */

if ( !defined('EQDKP_INC') )
{
     die('Do not access this file directly.');
}

if ( !class_exists( "auto_point_adjustments" ) ) {
  class auto_point_adjustments{
    private $apa_tab;
    private $apa_folder;
    private $apa_tab_file =  '.apatab';

    private $dummy_apa_tab = array(
      1234 => array(
        'pool' => 0,
        'event' => 0,
        'type' => 'startpoints',
        'reason' => "allgemeine startdkp",
        'options' => array(
          'mainsonly' => true,
          'value' => 100,
        )
      ),
      2345 => array(
        'pool' => 0,
        'event' => 0,
        'type' => 'pointcap',
        'reason' => "punktecap",
        'options' => array(
          'value' => 1000,
        ),
      ),
      3456 => array(
        'pool' => 0,
        'event' => 0,
        'type' => 'timedecay',
        'reason' => "weekly decay",
        'options' => array(
          'value' => 1000,
          'percentaged' => false,
          'round_decimals' => 2,
          'range' => array(0, 1000),
          'startdate' => 0,
          'interval_time' => 86400,
          'exclude_members' => array(),
        ),
      ),
      4567 => array(
        'pool' => 0,
        'event' => 0,
        'type' => 'inactivity',
        'reason' => "inactivity",
        'options' => array(
          'value' => 1000,
          'percentaged' => false,
          'round_decimals' => 2,
          'restore' => true,
          'range' => array(0, 1000),
          'inactivity_time' => 86400,
          'exclude_members' => array(),
        )
      ),
    );

    public function __construct(){
    global $pcache;
      $this->apa_tab_file  = $pcache->FolderPath('apa', 'eqdkp').'.apatab';
      $this->load_apa_tab();
    }
    
    private function load_apa_tab(){
      $result = @file_get_contents($this->apa_tab_file);
      if($result !== false){
        $this->apa_tab = unserialize($result);
      }else{
        //$this->save_apa_tab(array());

        //dummy tab for now
        $this->save_apa_tab($this->dummy_apa_tab);
        $this->load_apa_tab();
      }
    }
    
    private function save_apa_tab($apa_tab){
      global $pcache;
      $pcache->putContent(serialize($apa_tab), $this->apa_tab_file);
    }

    public function add_apa($pool, $event, $type, $reason = '', $options = array()){
      //generate unique id
      $unique_id = uniqid();
      
      $apa['pool'] = $pool;
      $apa['event'] = $event;
      $apa['type'] = $type;
      $apa['reason'] = $reason;
      $apa['options'] = $options;
      //add apa to apatab
      $this->apa_tab[$unique_id] = $apa;
      //save apatab
      $this->save_apa_tab($this->apa_tab);
      //run apa?
    }
    
    public function update_apa($apa_id, $pool, $event, $type, $reason, $options = array()){
      $apa['pool'] = $pool;
      $apa['event'] = $event;
      $apa['type'] = $type;
      $apa['reason'] = $reason;
      $apa['options'] = $options;
      //add apa to apatab
      $this->apa_tab[$apa_id] = $apa;
      //save apatab
      $this->save_apa_tab($this->apa_tab);
      //run apa?
    }
    
    public function del_apa($apa_id){
      //remove apa adjustments
      //remove apa from apatab
      unset($this->apa_tab[$apa_id]);
      //save apatab
      $this->save_apa_tab($this->apa_tab);
    }

    
    public function list_apas(){
      return $this->apa_tab;
    }
    
    public function get_apa_edit_form($apa_id){
      if(!isset($this->apa_tab[$apa_id]))
        return null;
        
      $type = "apa_".$this->apa_tab[$apa_id]['type'];

      $apa_object = new $type();
      $apa_object->set_values($this->apa_tab[$apa_id]);
      return $apa_object->get_options();
    }
    
    public function get_apa_add_form($apa_type){
      $class = "apa_".$apa_type;
      if(!class_exists($class))
        return null;

      $apa_object = new $class();
      return $apa_object->get_options();
		}

  }//end class
}//end if


if( !class_exists( "apa_type" ) ) {
  class apa_type{
    protected $options = array(
      'value' => array(
        'name' => 'value',
        'type' => 'int',
        'size' => 5,
        'value' => 0,
      )
    );
    
    protected $ext_options = array();

    public function set_values($apa){
	    $this->options = array_merge($this->options, $this->ext_options);
      foreach($apa['options'] as $key => $value){
        if(isset($this->options[$key])){
          $this->options[$key]['value'] = $value;
        }
      }
      return $this->options;
    }
    
    public function get_options(){
      return $this->options;
    }
  }
}

if ( !class_exists( "apa_pointcap" ) ) {
  class apa_pointcap extends apa_type{
  }//end class
}//end if

if ( !class_exists( "apa_startpoints" ) ) {
  class apa_startpoints extends apa_type{
    protected $ext_options = array(
      'mainsonly' => array(
        'name' => 'mainsonly',
        'type' => 'boolean',
        'value' => true,
      )
    );
  }//end class
}//end if

if ( !class_exists( "apa_timedecay" ) ) {
  class apa_timedecay extends apa_type{
  }//end class
}//end if

if ( !class_exists( "apa_attendancedecay" ) ) {
  class apa_attendancedecay extends apa_type{
  }//end class
}//end if

?>