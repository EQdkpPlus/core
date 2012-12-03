<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:        http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       20.11.2007
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
    header('HTTP/1.0 404 Not Found');
    exit;
}

class GameManagerPlus
{
  function GameManagerPlus(){
    global $db;
    $this->dbclass  = $db;
  }

  function ChangeGame($gamename, $myconfig, $lang="de"){
    // remove the old stuff
    $queries = array(
  	 "TRUNCATE TABLE __classes;",
  	 "TRUNCATE TABLE __races;",
  	 "TRUNCATE TABLE __factions;"
    );
    
    // the class array
    $i = 0;
    foreach($myconfig['classes'] as $clsses)
    {
    	$_classid = $i;
     	if (isset($clsses[4])) {
     		$_classid = $clsses[4];
     		}
  	 	array_push($queries, "INSERT INTO __classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (".$_classid.", '".$clsses[0]."', '".$clsses[1]."', ".$clsses[2].", ".$clsses[3].");");
  	 	$i++;
    }

    // the races array
    $i = 0;
    foreach($myconfig['races'] as $rname)
    {
  	 array_push($queries, "INSERT INTO __races (race_id, race_name) VALUES (".$i.", '".$rname."');");
  	 $i++;
    }

  	// the factions array
    $i = 1;
    foreach($myconfig['factions'] as $fname)
    {
  	 array_push($queries, "INSERT INTO __factions (faction_id, faction_name) VALUES (".$i.", '".$fname."');");
  	 $i++;
    }

    // Max. Level Thing:
    if(is_int($myconfig['max_level']) >0)
    {
        array_push($queries, "ALTER TABLE __members MODIFY member_level tinyint(2) NOT NULL default '".$myconfig['max_level']."';");
        array_push($queries, "UPDATE __members SET member_level = ".$myconfig['max_level']." WHERE member_level >".$myconfig['max_level'].";");
    }

    // handle addition stuff..
   	if (is_array($myconfig['add_sql']))
   	{
        foreach ($myconfig['add_sql'] as $_sql )
    	{
     		array_push($queries, $_sql);
    	}

   	}

  	array_push($queries, "UPDATE __config SET config_value = '".$gamename."' WHERE config_name = 'default_game';");
	  array_push($queries, "UPDATE __config SET config_value = '".$lang."' WHERE config_name = 'game_language';");
	  array_push($queries, "DELETE FROM __config WHERE config_name = 'game_version';");
	  array_push($queries, "INSERT INTO __config (config_name, config_value) VALUES  ('game_version', '".$myconfig['version']."');");

    foreach ( $queries as $sql )
    {
	     $this->dbclass->query($sql);
    }

    // insert the class color thing into all templates:
    if(is_array($myconfig['class_colors'])){
      $this->ClassColorFlush();     // Flush the old data
      $stysql = "SELECT style_id FROM __style_config";
      $styresult = $this->dbclass->query($stysql);
      while ($styrow = $this->dbclass->fetch_record($styresult)){
        $this->ClassColorManagement($styrow['style_id'], $myconfig['class_colors']);
      }
    }
    return;
  }

  function ClassColorFlush()
  {
      $this->dbclass->query('DELETE FROM __classcolors');
  }

  function ClassColorManagement($template, $clsarray)
  {
  	foreach($clsarray as $name=>$value)
  	{
  		$name = trim(str_replace(' ','',$name));
  		$cname = strtolower($name);  		
        $query = $this->dbclass->build_query('INSERT', array(
              'template'    => $template,
              'name'        => 'classc_'.$cname,
              'class'       => $name,
              'color'       => $value
              ));
          $this->dbclass->query('INSERT INTO __classcolors'. $query);
    }
  }

}

?>