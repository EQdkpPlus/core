<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * Vanguard-SoH.php
 * Began: Tues Jan 9 2007
 *
 * $Id$
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

   "UPDATE " . $table_prefix . "members SET member_level = 50 WHERE member_level > 50;",

   "ALTER TABLE " . $table_prefix . "members MODIFY member_level tinyint(2) NOT NULL default '50';",

   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (0, 'Unknown', 'Unknown',0,50);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (1, 'Bard', 'Leather',0,50);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (2, 'Berserker', 'Leather',0,50);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (3, 'Blood Mage', 'Cloth',0,50);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (4, 'Cleric', 'Plate',0,50);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (5, 'Disciple', 'Cloth',0,50);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (6, 'Dread Knight', 'Plate',0,50);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (7, 'Druid', 'Leather',0,50);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (8, 'Inquisitor', 'Plate',0,50);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (9, 'Monk', 'Cloth',0,50);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (10, 'Necromancer', 'Cloth',0,50);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (11, 'Paladin', 'Plate',0,50);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (12, 'Psionicist', 'Cloth',0,50);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (13, 'Ranger', 'Chain',0,50);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (14, 'Rogue', 'Chain',0,50);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (15, 'Shaman', 'Chain',0,50);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (16, 'Sorcerer', 'Cloth',0,50);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (17, 'Warrior', 'Plate',0,50);",

      
   "DELETE FROM ". $table_prefix ."factions;",
   "INSERT INTO ". $table_prefix ."factions (faction_id, faction_name) VALUES (0, 'Unknown');",
   "INSERT INTO ". $table_prefix ."factions (faction_id, faction_name) VALUES (1, 'Good');",
   "INSERT INTO ". $table_prefix ."factions (faction_id, faction_name) VALUES (2, 'Evil');",
   "INSERT INTO ". $table_prefix ."factions (faction_id, faction_name) VALUES (3, 'Neutral');",

   "DELETE FROM ". $table_prefix ."races;",
   "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (0, 'Unknown');",
   "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (1, 'Barbarian');",
   "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (2, 'Dark Elf');",
   "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (3, 'Dwarf');",
   "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (4, 'Elf');",
   "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (5, 'Giant');",
   "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (6, 'Gnome');",
   "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (7, 'Goblin');",
   "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (8, 'Half-Elf');",
   "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (9, 'Halfling');",
   "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (10, 'High Elf');",
   "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (11, 'Human');",
   "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (12, 'Kojani');",
   "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (13, 'Kurashasa');",
   "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (14, 'Lesser Giant');",
   "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (15, 'Mordebi');",
   "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (16, 'Orc');",
   "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (17, 'Qaliathari');",
   "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (18, 'Raki');",
   "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (19, 'Thestran');",
   "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (20, 'Varanjar');",
   "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (21, 'Varathari');",
   "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (22, 'Vulmane');",
   "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (23, 'Wood Elf');",


   "UPDATE ". $table_prefix ."config SET config_value = 'Vanguard-SoH' WHERE config_name = 'default_game';",

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
