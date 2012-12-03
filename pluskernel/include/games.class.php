<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
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
  function GameManagerPlus($table_prefix, $db){
    $this->table_prefix = $table_prefix;
    $this->dbclass      = $db;
  }

  function ChangeGame($gamename, $classes, $races, $factions, $classcolors, $maxlevel, $addquerries=false,$lang="de"){
    // remove the old stuff
    $queries = array(
  	 "TRUNCATE TABLE ". $this->table_prefix ."classes;",
  	 "TRUNCATE TABLE ". $this->table_prefix ."races;",
  	 "TRUNCATE TABLE ". $this->table_prefix ."factions;"
    );

    // the class array
    $i = 0;
    foreach($classes as $clsses)
    {
    	$_classid = $i;
     	if (isset($clsses[4])) {
     		$_classid = $clsses[4];
     		}
  	 	array_push($queries, "INSERT INTO ". $this->table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (".$_classid.", '".$clsses[0]."', '".$clsses[1]."', ".$clsses[2].", ".$clsses[3].");");
  	 	$i++;
    }

    // the races array
    $i = 0;
    foreach($races as $rname)
    {
  	 array_push($queries, "INSERT INTO ". $this->table_prefix ."races (race_id, race_name) VALUES (".$i.", '".$rname."');");
  	 $i++;
    }

  	// the factions array
    $i = 1;
    foreach($factions as $fname)
    {
  	 array_push($queries, "INSERT INTO ". $this->table_prefix ."factions (faction_id, faction_name) VALUES (".$i.", '".$fname."');");
  	 $i++;
    }

    // Max. Level Thing:
    if(is_int($maxlevel) >0)
    {
        array_push($queries, "ALTER TABLE " . $this->table_prefix . "members MODIFY member_level tinyint(2) NOT NULL default '".$maxlevel."';");
        array_push($queries, "UPDATE " . $this->table_prefix . "members SET member_level = ".$maxlevel." WHERE member_level >".$maxlevel.";");
    }

    // handle addition stuff..
   	if (is_array($addquerries))
   	{
        foreach ($addquerries as $_sql )
    	{
     		array_push($queries, $_sql);
    	}

   	}

  	array_push($queries, "UPDATE ". $this->table_prefix ."config SET config_value = '".$gamename."' WHERE config_name = 'default_game';");
	  array_push($queries, "UPDATE ". $this->table_prefix ."config SET config_value = '".$lang."' WHERE config_name = 'game_language';");

    foreach ( $queries as $sql )
    {
	     $this->dbclass->query($sql);
    }

    // insert the class color thing into all templates:
    $this->ClassColorFlush();     // Flush the old data
    $stysql = "SELECT style_id FROM ".$this->table_prefix."style_config";
    $styresult = $this->dbclass->query($stysql);
      while ($styrow = $this->dbclass->fetch_record($styresult)){
        $this->ClassColorManagement($styrow['style_id'], $classcolors);
      }
    return;
  }

  function ClassColorFlush()
  {
      $this->dbclass->query('DELETE FROM '. $this->table_prefix .'classcolors');
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
          $this->dbclass->query('INSERT INTO '. $this->table_prefix .'classcolors'. $query);
    }
  }

}

?>
