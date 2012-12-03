DROP TABLE IF EXISTS eqdkp_plus_update;
CREATE TABLE IF NOT EXISTS eqdkp_plus_update (
  `name` varchar(255) NOT NULL default '',
  `version` varchar(255) NOT NULL default '',
  `level` varchar(255) NOT NULL default '',
  `changelog` varchar(255) NOT NULL default '',
  `release` varchar(255) NOT NULL default '',
  `download` varchar(255) NOT NULL default '',
  `realname` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`name`)
) TYPE=MyISAM ;