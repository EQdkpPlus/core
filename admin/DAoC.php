<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * DAoC.php
 * Began: Fri May 13 2005
 *
 * $Id: DAoC.php 6 2006-05-08 17:11:35Z tsigo $
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
"INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (0, 'Unknown', 'Unknown');",
"INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (1, 'Animist', 'Plate');",
"INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (2, 'Armsman', 'Plate');",
"INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (3, 'Bainshee', 'Plate');",
"INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (4, 'Bard', 'Plate');",
"INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (5, 'Berserker', 'Plate');",
"INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (6, 'Blademaster', 'Plate');",
"INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (7, 'Bonedancer', 'Plate');",
"INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (8, 'Cabalist', 'Plate');",
"INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (9, 'Champion', 'Plate');",
"INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (10, 'Cleric', 'Plate');",
"INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (11, 'Druid', 'Plate');",
"INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (12, 'Eldritch', 'Plate');",
"INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (13, 'Enchanter', 'Plate');",
"INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (14, 'Friar', 'Plate');",
"INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (15, 'Healer', 'Plate');",
"INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (16, 'Heretic', 'Plate');",
"INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (17, 'Hero', 'Plate');",
"INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (18, 'Hunter', 'Plate');",
"INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (19, 'Infiltrator', 'Plate');",
"INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (20, 'Mentalist', 'Plate');",
"INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (21, 'Mercenary', 'Plate');",
"INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (22, 'Minstrel', 'Plate');",
"INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (23, 'Necromancer', 'Plate');",
"INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (24, 'Nightshade', 'Plate');",
"INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (25, 'Paladin', 'Plate');",
"INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (26, 'Ranger', 'Plate');",
"INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (27, 'Reaver', 'Plate');",
"INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (28, 'Runemaster', 'Plate');",
"INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (29, 'Savage', 'Plate');",
"INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (30, 'Scout', 'Plate');",
"INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (31, 'Shadowblade', 'Plate');",
"INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (32, 'Shaman', 'Plate');",
"INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (33, 'Skald', 'Plate');",
"INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (34, 'Sorcerer', 'Plate');",
"INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (35, 'Spiritmaster', 'Plate');",
"INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (36, 'Thane', 'Plate');",
"INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (37, 'Theurgist', 'Plate');",
"INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (38, 'Valewalker', 'Plate');",
"INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (39, 'Valkyrie', 'Plate');",
"INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (40, 'Vampiir', 'Plate');",
"INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (41, 'Warden', 'Plate');",
"INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (42, 'Warlock', 'Plate');",
"INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (43, 'Warrior', 'Plate');",
"INSERT INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (44, 'Wizard', 'Plate');",

"DELETE FROM ". $table_prefix ."races;",

"INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (0, 'Unknown');",
"INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (1, 'Valonian');",
"INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (2, 'Briton');",
"INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (3, 'Half Ogre');",
"INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (4, 'Highlander');",
"INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (5, 'Inconnu');",
"INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (6, 'Saracen');",
"INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (7, 'Celt');",
"INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (8, 'Elf');",
"INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (9, 'Firbolg');",
"INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (10, 'Lurikeen');",
"INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (11, 'Shar');",
"INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (12, 'Sylvan');",
"INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (13, 'Dwarf');",
"INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (14, 'Frostalf');",
"INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (15, 'Kobold');",
"INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (16, 'Norse');",
"INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (17, 'Troll');",
"INSERT INTO ". $table_prefix ."races (race_id, race_name) VALUES (18, 'Valkyn');",

"DELETE FROM ". $table_prefix ."factions;",

"INSERT INTO ". $table_prefix ."factions (faction_id, faction_name) VALUES (1, 'Albion');",
"INSERT INTO ". $table_prefix ."factions (faction_id, faction_name) VALUES (2, 'Hibernia');",
"INSERT INTO ". $table_prefix ."factions (faction_id, faction_name) VALUES (3, 'Midgard');",

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

