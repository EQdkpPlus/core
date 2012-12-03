<?php
/******************************
 * EQdkp
 * Copyright 2002-2007
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * LOTRO_german.php
 * Began: 31 May 2007
 *
 * $Id: WoW_german.php 62 2007-05-15 18:42:34Z osr-corgan $
 *
 ******************************/

if ( !defined('EQDKP_INC') )
{
    die('Hacking attempt');
}

class Manage_Game
{
 function do_it($db,$table_prefix,$install)
 {
   $queries = array(
   "DELETE FROM ". $table_prefix ."classes;",
   "UPDATE " . $table_prefix . "members SET member_level = 50 WHERE member_level >50;",
   "UPDATE " . $table_prefix . "style_config SET date_notime_long = 'F j, Y' WHERE style_id >0;",
   "UPDATE " . $table_prefix . "style_config SET date_notime_short = 'm/d/y' WHERE style_id >0;",
   "UPDATE " . $table_prefix . "style_config SET date_time = 'd.m.y h:ia T' WHERE style_id >0;",
   "ALTER TABLE " . $table_prefix . "members MODIFY member_level tinyint(2) NOT NULL default '50';",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (0, 'Unknown', 'Light Armour',0,70);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (1, 'Minstrel', 'Medium Armour ',0,50);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (2, 'Captain', 'Heavy Armour',0,50);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (3, 'Hunter', 'Medium Armour',0,50);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (4, 'Lore-master', 'Light Armour',0,50);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (5, 'Burglar', 'Medium Armour',0,50);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (6, 'Guardian', 'Heavy Armour',0,50);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (7, 'Champion', 'Heavy Armour',0,50);",
   "DELETE FROM ". $table_prefix ."races;",
   "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (0, 'Unknown');",
   "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (1, 'Man');",
   "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (2, 'Hobbit');",
   "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (3, 'Elf');",
   "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (4, 'Dwarf');",
   "DELETE FROM ". $table_prefix ."factions;",
   "INSERT INTO ". $table_prefix ."factions (faction_id, faction_name) VALUES (1, 'Normal');",
   "INSERT INTO ". $table_prefix ."factions (faction_id, faction_name) VALUES (2, 'MonsterPlay');",

   "UPDATE ". $table_prefix ."config SET config_value = 'LOTRO_english' WHERE config_name = 'default_game';",
   );
   foreach ( $queries as $sql )
   {
       $db->query($sql);
   }

   if (!$install)
   {
	 $redir = "admin/config.php";
	 redirect($redir);
	}
 }
}

?>
