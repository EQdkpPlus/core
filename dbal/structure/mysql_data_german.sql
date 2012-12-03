-- phpMyAdmin SQL Dump
-- version 2.6.0-beta3
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Erstellungszeit: 28. Juli 2005 um 01:28
-- Server Version: 4.0.18
-- PHP-Version: 4.3.4
-- 
-- Datenbank: `db0`
-- 
-- Use this for EQdkp 1.3.0 if you are German and using EQ2.
-- The nice WoW folks actually made a install script :-)
-- 

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `eqdkp_classes`
-- 

DROP TABLE IF EXISTS `eqdkp_classes`;
CREATE TABLE IF NOT EXISTS `eqdkp_classes` (
  `c_index` smallint(3) unsigned NOT NULL auto_increment,
  `class_id` smallint(3) unsigned NOT NULL default '0',
  `class_name` varchar(50) NOT NULL default '',
  `class_min_level` smallint(3) NOT NULL default '0',
  `class_max_level` smallint(3) NOT NULL default '999',
  `class_armor_type` varchar(50) NOT NULL default '',
  `class_hide` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`c_index`)
) TYPE=MyISAM;

-- 
-- Daten für Tabelle `eqdkp_classes`
-- 

INSERT INTO `eqdkp_classes` (`c_index`, `class_id`, `class_name`, `class_min_level`, `class_max_level`, `class_armor_type`, `class_hide`) VALUES (18, 0, 'Unknown', 1, 99, 'Heavy', '0'),
(19, 1, 'Kämpfer', 1, 9, 'Heavy', '0'),
(20, 2, 'Kundschafter', 1, 9, 'Medium', '0'),
(21, 3, 'Magier', 1, 9, 'VeryLight', '0'),
(22, 4, 'Priester', 1, 9, 'Heavy', '0'),
(23, 5, 'Krieger', 10, 19, 'Heavy', '0'),
(24, 6, 'Kreuzritter', 10, 19, 'Heavy', '0'),
(25, 7, 'Schläger', 10, 19, 'Light', '0'),
(26, 8, 'Raufbold', 20, 99, 'Light', '0'),
(27, 9, 'Mönch', 20, 99, 'Light', '0'),
(28, 10, 'Berserker', 20, 99, 'Heavy', '0'),
(29, 11, 'Wächter', 20, 99, 'Heavy', '0'),
(30, 12, 'Paladin', 20, 99, 'Heavy', '0'),
(31, 13, 'Schattenritter', 20, 99, 'Heavy', '0'),
(32, 14, 'Thaumaturge', 10, 19, 'VeryLight', '0'),
(33, 15, 'Hexer', 10, 19, 'VeryLight', '0'),
(34, 16, 'Beschwörer', 10, 19, 'VeryLight', '0'),
(35, 17, 'Illusionist', 20, 99, 'VeryLight', '0'),
(36, 18, 'Erzwinger', 20, 99, 'VeryLight', '0'),
(37, 19, 'Zauberer', 20, 99, 'VeryLight', '0'),
(38, 20, 'Hexenmeister', 20, 99, 'VeryLight', '0'),
(39, 21, 'Nekromant', 20, 99, 'VeryLight', '0'),
(40, 22, 'Elementalist', 20, 99, 'VeryLight', '0'),
(41, 23, 'Kleriker', 10, 19, 'Heavy', '0'),
(42, 24, 'Druide', 10, 19, 'Light', '0'),
(43, 25, 'Schamane', 10, 19, 'Medium', '0'),
(44, 26, 'Templer', 20, 99, 'Heavy', '0'),
(45, 27, 'Inquisitor', 20, 99, 'Heavy', '0'),
(46, 28, 'Wärter', 20, 99, 'Light', '0'),
(47, 29, 'Furie', 20, 99, 'Light', '0'),
(48, 30, 'Schänder', 20, 99, 'Medium', '0'),
(49, 31, 'Mystiker', 20, 99, 'Medium', '0'),
(50, 32, 'Dieb', 10, 19, 'Medium', '0'),
(51, 33, 'Barde', 10, 19, 'Medium', '0'),
(52, 34, 'Raubtier', 10, 19, 'Medium', '0'),
(53, 35, 'Abenteurer', 20, 99, 'Medium', '0'),
(54, 36, 'Brigand', 20, 99, 'Medium', '0'),
(55, 37, 'Klagesänger', 20, 99, 'Medium', '0'),
(56, 38, 'Troubadour', 20, 99, 'Medium', '0'),
(57, 39, 'Assassine', 20, 99, 'Medium', '0'),
(58, 40, 'Waldläufer', 20, 99, 'Medium', '0'),
(59, 41, 'Craftsmen', 1, 99, 'Heavy', '0'),
(60, 42, 'Scholar', 1, 99, 'Heavy', '0'),
(61, 43, 'Outfitter', 1, 99, 'Heavy', '0'),
(62, 44, 'Provisioner', 1, 99, 'Heavy', '0'),
(63, 45, 'Woodworker', 1, 99, 'Heavy', '0'),
(64, 46, 'Carpenter', 1, 99, 'Heavy', '0'),
(65, 47, 'Armorer', 1, 99, 'Heavy', '0'),
(66, 48, 'Weaponsmith', 1, 99, 'Heavy', '0'),
(67, 49, 'Tailor', 1, 99, 'Heavy', '0'),
(68, 50, 'Jeweler', 1, 99, 'Heavy', '0'),
(69, 51, 'Sage', 1, 99, 'Heavy', '0'),
(70, 52, 'Alchemist', 1, 99, 'Heavy', '0');

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `eqdkp_races`
-- 

DROP TABLE IF EXISTS `eqdkp_races`;
CREATE TABLE IF NOT EXISTS `eqdkp_races` (
  `race_id` smallint(3) unsigned NOT NULL default '0',
  `race_name` varchar(50) NOT NULL default '',
  `race_faction_id` smallint(3) NOT NULL default '0',
  `race_hide` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`race_id`),
  UNIQUE KEY `race_id` (`race_id`)
) TYPE=MyISAM;

-- 
-- Daten für Tabelle `eqdkp2_races`
-- 

INSERT INTO `eqdkp_races` (`race_id`, `race_name`, `race_faction_id`, `race_hide`) VALUES (0, 'Unknown', 0, '0'),
(1, 'Gnom', 0, '0'),
(2, 'Mensch', 0, '0'),
(3, 'Barbar', 0, '0'),
(4, 'Zwerg', 0, '0'),
(5, 'Hochelf', 0, '0'),
(6, 'Dunkelelf', 0, '0'),
(7, 'Waldelf', 0, '0'),
(8, 'Halbelf', 0, '0'),
(9, 'Kerraner', 0, '0'),
(10, 'Troll', 0, '0'),
(11, 'Oger', 0, '0'),
(12, 'Froschlog', 0, '0'),
(13, 'Erudit', 0, '0'),
(14, 'Iksar', 0, '0'),
(15, 'Ratonga', 0, '0'),
(16, 'Halbling', 0, '0');

