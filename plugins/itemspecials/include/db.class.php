<?php
/******************************
 * EQDKP PLUGIN: ItemSpecials
 * (c) 2006 by WalleniuM [Simon Wallmann]
 * http://www.kompsoft.de   
 * ------------------
 * db.class.php
 * Changed: November 03, 2006
 * 
 ******************************/

class ItemSpecialsDB
{
	// the "Save my Data to the Database" :D
  function UpdateConfig($dataarray, $confarray)
      {
        global $eqdkp_root_path, $user, $SID, $table_prefix, $db;
        foreach($dataarray as $key=>$value) {
        	if(!in_array($key, $confarray)){
        		$this->InsertConfig($key);
        	}
        	
          $sql = "UPDATE `".$table_prefix."itemspecials_config` SET config_value='".strip_tags(htmlspecialchars($value))."' WHERE config_name='".$key."';";
          $db->query($sql);
		    }
          return true;
      }
    
    function InsertConfig($name)
      {
        global $table_prefix, $db;
        	$sql = "INSERT INTO `" . $table_prefix . "itemspecials_config` ( `config_name` ) VALUES ('".$name."');";
        	$blubb = $db->query($sql);
      }
    
    function CheckDBFields($table, $field)
    {
    	global $table_prefix, $db;
    	$il = 1;
    	$output = array();
    	$sql = "SELECT ".$field." FROM `" . $table_prefix . $table . "`;";
      $blubb = $db->query($sql);
      while ( $blubber = $db->fetch_record($blubb) ){
      	$output[$il] = $blubber['config_name'];
      	$il++;
      }
      return $output;
    }
    
    // add the item strip function
		function stripName($string) { /* a-z, A-Z, 0-9 */
 			return(preg_replace("/[^A-Za-z\s]/",'', $string));
		}
  	
}// end of class
?>