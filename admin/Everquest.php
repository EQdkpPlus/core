<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * Everquest.php
 * Began: Fri May 13 2005
 *
 * $Id: Everquest.php 6 2006-05-08 17:11:35Z tsigo $
 *
 ******************************/

if ( !defined('EQDKP_INC') )
{
    die('Hacking attempt');
}



class Manage_Game extends EQdkp_Admin
{ 

 function do_it()
 {

  global $db, $eqdkp, $user;
  global $SID, $dbname, $table_prefix;

  parent::eqdkp_admin();

  $queries = array(

  "DELETE FROM ". $table_prefix ."classes;",
  "ALTER TABLE " . $table_prefix . "members MODIFY member_level tinyint(2) NOT NULL default '70';",
  "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (0, 'Unknown', 'Plate');",
  "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (1, 'Warrior', 'Plate');",
  "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (2, 'Rogue', 'Chain');",
  "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (3, 'Monk', 'Leather');",
  "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (4, 'Ranger', 'Chain');",
  "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (5, 'Paladin', 'Plate');",
  "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (6, 'Shadow Knight', 'Plate');",
  "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (7, 'Bard', 'Plate');",
  "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (8, 'Beastlord', 'Leather');",
  "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (9, 'Cleric', 'Plate');",
  "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (10, 'Druid', 'Leather');",
  "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (11, 'Shaman', 'Chain');",
  "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (12, 'Enchanter', 'Silk');",
  "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (13, 'Wizard', 'Silk');",
  "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (14, 'Necromancer', 'Silk');",
  "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (15, 'Magician', 'Silk');",
  "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (16, 'Berserker', 'Leather');",
  
  "DELETE FROM ". $table_prefix ."races;",
  
  "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (0, 'Unknown');",
  "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (1, 'Gnome');",
  "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (2, 'Human');",
  "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (3, 'Barbarian');",
  "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (4, 'Dwarf');",
  "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (5, 'High Elf');",
  "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (6, 'Dark Elf');",
  "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (7, 'Wood Elf');",
  "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (8, 'Half Elf');",
  "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (9, 'Vah Shir');",
  "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (10, 'Troll');",
  "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (11, 'Ogre');",
  "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (12, 'Frog');",
  "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (13, 'Iksar');",
  "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (14, 'Erudite');",
  "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (15, 'Halfling');",
  
  "DELETE FROM ". $table_prefix ."factions;",
  
  "INSERT INTO ". $table_prefix ."factions (faction_id, faction_name) VALUES (1, 'Good');",
  "INSERT INTO ". $table_prefix ."factions (faction_id, faction_name) VALUES (2, 'Evil');",

  
  );
  
  
  foreach ( $queries as $sql )
  {
      $db->query($sql);
  }
  
 $redir = "admin/config.php";
 redirect($redir);

 }

}


$manage = new Manage_Game;
$manage->do_it();


?>
