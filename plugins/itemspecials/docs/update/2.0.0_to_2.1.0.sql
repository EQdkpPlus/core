-- 
-- Tabellenstruktur für Tabelle `eqdkp_itemspecials_items`
-- 

CREATE TABLE `eqdkp_itemspecials_items` (
  `item_id` mediumint(8) unsigned NOT NULL auto_increment,
  `item_name` varchar(255) default NULL,
  `item_buyer` varchar(50) default NULL,
  PRIMARY KEY  (`item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;