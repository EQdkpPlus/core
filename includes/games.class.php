<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * DAoC.php
 * Began: 20.11.2007
 *
 * $Id:  $
 *
 ******************************/

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

  function ChangeGame($gamename, $classes, $races, $factions, $maxlevel, $addquerries=false,$lang="de")
  {
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
    if(isset($maxlevel)){
        array_push($queries, "ALTER TABLE " . $this->table_prefix . "members MODIFY member_level tinyint(2) NOT NULL default '".$maxlevel."';");
        array_push($queries, "UPDATE " . $this->table_prefix . "members SET member_level = ".$maxlevel." WHERE member_level >".$maxlevel.";");
    }

    // handle addition stuff.. mayxbe usefull at some time...
    if($addquerries && is_array($addquerries))
    {
      array_push($queries, $addquerries);
    }

  	array_push($queries, "UPDATE ". $this->table_prefix ."config SET config_value = '".$gamename."' WHERE config_name = 'default_game';");
	array_push($queries, "UPDATE ". $this->table_prefix ."config SET config_value = '".$lang."' WHERE config_name = 'game_language';");

    foreach ( $queries as $sql )
    {
      $this->dbclass->query($sql);
    }
    return;
  }
}

?>
