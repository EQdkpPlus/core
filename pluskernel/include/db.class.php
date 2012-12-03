<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2006
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

class dbPlus
{
	// Plugin Version
	var $PlusUpdateClassVersion 	= '1.0.1';
	var $PlusUpdateClassBuild		= '020320071937';

	function UpdateConfig($fieldname,$insertvalue, $confarray = false, $striptag=true)
      {
        global $table_prefix, $db;
        // check if the config setting is avialable, if not
        // get it in the database
        if(!in_array(strtolower($fieldname), $confarray)){
        	$this->InsertConfig($fieldname);
        }
        // insert the new setting value
        $myinsertvalue = ($striptag) ? htmlspecialchars(strip_tags($insertvalue)) : htmlentities(htmlspecialchars($insertvalue));
        $sql = "UPDATE `" . $table_prefix . "plus_config` SET config_value='".$myinsertvalue."' WHERE config_name='".$fieldname."';";
        if ($db->query($sql)){
          return true;
        } else {
          return false;
        }
      }

    function InsertConfig($name)
      {
        global $table_prefix, $db;
        	$sql = "INSERT INTO `" . $table_prefix . "plus_config` ( `config_name` ) VALUES ('".$name."');";
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
      	$output[$il] = strtolower($blubber[$field]);
      	$il++;
      }
      return $output;
    }

  	function UpdateUpdateCache($fieldname1, $pluginname, $insertvalue)
      {
        global $table_prefix, $db;
        	$sql = "UPDATE `" . $table_prefix . "plus_update` SET `".$fieldname1."`='".strip_tags(htmlspecialchars($insertvalue))."' WHERE `name`='".$pluginname."';";
        	$blubb = $db->query($sql);
      	if ($blubb){
          return true;
        } else {
          return false;
        }
      }

   function InsertUpdateCache($pluginname)
      {
        global $table_prefix, $db;
        	$sql = "INSERT INTO `" . $table_prefix . "plus_update` ( `name` ) VALUES ('".$pluginname."');";
        	$blubb = $db->query($sql);
      	if ($blubb){
          return true;
        } else {
          return false;
        }
      }

  function InitConfig()
  {
		global $table_prefix, $db;
		// get the config
		$sqlplus = 'SELECT * FROM `' . $table_prefix . 'plus_config`';
		if (($settings_result = $db->query($sqlplus)))
		{
			while($roww = $db->fetch_record($settings_result))
			{
				$conf[$roww['config_name']] = $roww['config_value'];
			}
			return $conf;
		}
	}

	function ProcessLinks($p_linkname, $p_linkurl, $p_linkwindow, $p_link_menu)
	{
		global $db, $table_prefix;
		foreach ( $p_linkname as $link_id => $link_name )
    	{
	        $sql = "DELETE FROM ".PLUS_LINKS_TABLE." WHERE link_id='".$link_id."'";
	        $db->query($sql);

	        if ( $link_name == '' )
	        {
	        }else
	        {
	            $link_name = ( isset($p_linkname[$link_id]) ) ? $p_linkname[$link_id] : '';
	            $link_url  = ( isset($p_linkurl[$link_id]) ) ? $p_linkurl[$link_id] : '';
	            $link_window = ( isset($p_linkwindow[$link_id]) ) ? $p_linkwindow[$link_id] : '';
	            $link_menu = ( isset($p_link_menu[$link_id]) ) ? $p_link_menu[$link_id] : 0;

	            $link_name 		= undo_sanitize_tags(stripslashes($link_name));
	            $link_url 		= undo_sanitize_tags(stripslashes($link_url));
	            $link_window	= undo_sanitize_tags(stripslashes($link_window));
	            $link_menu		= undo_sanitize_tags(stripslashes($link_menu));

	            $query = $db->build_query('INSERT', array(
	                'link_id'     => $link_id,
	                'link_name'   => $link_name,
	                'link_url' 		=> $link_url,
	                'link_window' => $link_window,
	                'link_menu' => $link_menu)
	            );
	            $db->query("INSERT INTO ".PLUS_LINKS_TABLE." ".$query);
	         }
    	}
	}


	function ProcesspRecruitment($p_classes)
	{

		global $db, $table_prefix,$eqdkp;

		if (is_array($p_classes))
		{
	        $sql = "DELETE FROM ".PLUS_CONFIG_TABLE." WHERE config_name like '%pk_recruitment_class%'";
	        $db->query($sql);


			foreach ( $p_classes as $class_id => $class_count )
	    	{

				if (is_array($class_count)) // wow skillungsarray
				{
					foreach ($class_count as $skilcount => $skillvalue )
					{
			    		$skilcount = intval(undo_sanitize_tags(stripslashes($skilcount)));
			    		$skillvalue = intval(undo_sanitize_tags(stripslashes($skillvalue)));
			        	$config_name = 'pk_recruitment_class['.$class_id.']['.$skilcount.']';

			            $query = $db->build_query('INSERT', array(
			                'config_name'     => $config_name,
			                'config_value'   => $skillvalue)
			            );
			            $db->query("INSERT INTO ".PLUS_CONFIG_TABLE." ".$query);
					}
				}else //normal
				{
			    		$class_id = intval(undo_sanitize_tags(stripslashes($class_id)));
			    		$class_count = intval(undo_sanitize_tags(stripslashes($class_count)));

			        	$config_name = 'pk_recruitment_class['.$class_id.']';
			            $query = $db->build_query('INSERT', array(
			                'config_name'     => $config_name,
			                'config_value'   => $class_count)
			            );
			            $db->query("INSERT INTO ".PLUS_CONFIG_TABLE." ".$query);
				}
	    	}
		}


	}# end function


}// end of class
?>