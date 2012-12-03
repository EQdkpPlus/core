<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * Everquest2.php
 * Began: Fri May 13 2005
 *
 * $Id: Everquest2.php 6 2006-05-08 17:11:35Z tsigo $
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
   "UPDATE " . $table_prefix . "members SET member_level = 50 WHERE member_level > 50;",
   "ALTER TABLE " . $table_prefix . "members MODIFY member_level tinyint(2) NOT NULL default '50';",
   "DELETE FROM ". $table_prefix ."classes;",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (0, 'Unknown', 'Heavy',1,99);",

   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (1, 'Fighter', 'Heavy',1,9);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (2, 'Scout', 'Medium',1,9);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (3, 'Mage', 'VeryLight',1,9);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (4, 'Priest', 'Heavy',1,9);",

   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (5, 'Warrior', 'Heavy',10,19);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (6, 'Crusader', 'Heavy',10,19);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (7, 'Brawler', 'Light',10,19);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (8, 'Bruiser', 'Light',20,99);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (9, 'Monk', 'Light',20,99);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (10, 'Berserker', 'Heavy',20,99);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (11, 'Guardian', 'Heavy',20,99);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (12, 'Paladin', 'Heavy',20,99);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (13, 'Shadowknight', 'Heavy',20,99);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (14, 'Enchanter', 'VeryLight',10,19);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (15, 'Sorcerer', 'VeryLight',10,19);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (16, 'Summoner', 'VeryLight',10,19);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (17, 'Illusionist', 'VeryLight',20,99);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (18, 'Coercer', 'VeryLight',20,99);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (19, 'Wizard', 'VeryLight',20,99);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (20, 'Warlock', 'VeryLight',20,99);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (21, 'Necromancer', 'VeryLight',20,99);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (22, 'Conjuror', 'VeryLight',20,99);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (23, 'Cleric', 'Heavy',10,19);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (24, 'Druid', 'Light',10,19);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (25, 'Shaman', 'Medium',10,19);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (26, 'Templar', 'Heavy',20,99);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (27, 'Inquisitor', 'Heavy',20,99);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (28, 'Warden', 'Light',20,99);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (29, 'Fury', 'Light',20,99);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (30, 'Defiler', 'Medium',20,99);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (31, 'Mystic', 'Medium',20,99);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (32, 'Rogue', 'Medium',10,19);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (33, 'Bard', 'Medium',10,19);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (34, 'Predator', 'Medium',10,19);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (35, 'Swashbuckler', 'Medium',20,99);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (36, 'Brigand', 'Medium',20,99);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (37, 'Dirge', 'Medium',20,99);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (38, 'Troubador', 'Medium',20,99);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (39, 'Assassin', 'Medium',20,99);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (40, 'Ranger', 'Medium',20,99);",
   
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (41, 'Craftsmen', 'Heavy',1,99);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (42, 'Scholar', 'Heavy',1,99);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (43, 'Outfitter', 'Heavy',1,99);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (44, 'Provisioner', 'Heavy',1,99);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (45, 'Woodworker', 'Heavy',1,99);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (46, 'Carpenter', 'Heavy',1,99);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (47, 'Armorer', 'Heavy',1,99);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (48, 'Weaponsmith', 'Heavy',1,99);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (49, 'Tailor', 'Heavy',1,99);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (50, 'Jeweler', 'Heavy',1,99);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (51, 'Sage', 'Heavy',1,99);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (52, 'Alchemist', 'Heavy',1,99);",
   
   
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
   "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (9, 'Kerra');",
   "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (10, 'Troll');",
   "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (11, 'Ogre');",
   "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (12, 'Frog');",
   "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (13, 'Erudite');",
   "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (14, 'Iksar');",
   "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (15, 'Ratonga');",
   "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (16, 'Halfling');",
   
   "DELETE FROM ". $table_prefix ."factions;",
   
   "INSERT INTO ". $table_prefix ."factions (faction_id, faction_name) VALUES (1, 'Good');",
   "INSERT INTO ". $table_prefix ."factions (faction_id, faction_name) VALUES (2, 'Evil');",
   "INSERT INTO ". $table_prefix ."factions (faction_id, faction_name) VALUES (3, 'Neutral');",


   
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
