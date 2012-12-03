<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * WoW.php
 * Began: Fri May 13 2005
 *
 * $Id$
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
   "UPDATE " . $table_prefix . "members SET member_level = 70 WHERE member_level >70;",
   "UPDATE " . $table_prefix . "style_config SET date_notime_long = 'j F, Y' WHERE style_id >0;",
   "UPDATE " . $table_prefix . "style_config SET date_notime_short = 'd.m.y' WHERE style_id >0;",
   "UPDATE " . $table_prefix . "style_config SET date_time = 'd.m.y h:i' WHERE style_id >0;",
   "ALTER TABLE " . $table_prefix . "members MODIFY member_level tinyint(2) NOT NULL default '70';",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (0, 'Unknown', 'Platte',0,70);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (1, 'Krieger', 'Platte',0,70);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (2, 'Schurke', 'Leder',0,70);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (3, 'Jäger', 'Leder',0,39);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (4, 'Jäger', 'Schwere Rüstung',40,70);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (5, 'Paladin', 'Platte',0,70);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (6, 'Priester', 'Stoff',0,70);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (7, 'Druide', 'Leder',0,70);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (8, 'Schamane', 'Leder',0,39);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (9, 'Schamane', 'Schwere Rüstung',40,70);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (10, 'Hexenmeister', 'Stoff',0,70);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (11, 'Magier', 'Stoff',0,70);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (12, 'Krieger', 'Platte',40,70);",
   "INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type, class_min_level, class_max_level) VALUES (13, 'Paladin', 'Platte',40,70);",
   "DELETE FROM ". $table_prefix ."races;",
   "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (0, 'Unknown');",
   "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (1, 'Gnom');",
   "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (2, 'Mensch');",
   "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (3, 'Zwerg');",
   "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (4, 'Nachtelf');",
   "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (5, 'Troll');",
   "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (6, 'Untoter');",
   "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (7, 'Ork');",
   "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (8, 'Taure');",
   "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (9, 'Draenei');",
   "INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (10,'Blutelf');",
   "DELETE FROM ". $table_prefix ."factions;",
   "INSERT INTO ". $table_prefix ."factions (faction_id, faction_name) VALUES (1, 'Allianz');",
   "INSERT INTO ". $table_prefix ."factions (faction_id, faction_name) VALUES (2, 'Horde');",
   "UPDATE ". $table_prefix ."config SET config_value = 'WoW_german' WHERE config_name = 'default_game';",
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
