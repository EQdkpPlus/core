-- 
-- Table structure for table `eqdkp_dev_plus_config`
-- 

CREATE TABLE IF NOT EXISTS `eqdkp_dev_plus_config` (
  `config_name` varchar(255) NOT NULL default '',
  `config_value` varchar(255) default NULL,
  PRIMARY KEY  (`config_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `eqdkp_dev_plus_config`
-- 

INSERT INTO `eqdkp_dev_plus_config` (`config_name`, `config_value`) VALUES 
('pk_updatecheck', '1'),
('pk_windowtime', '10'),
('pk_multidkp', '1'),
('pk_leaderboard_solo', ''),
('pk_rank', '1'),
('pk_level', '1'),
('pk_lastloot', '1'),
('pk_attendanceAll', '1'),
('pk_links', '1'),
('pk_bosscount', '1'),
('pk_itemstats', '1'),
('pk_attendance90', '1'),
('pk_attendance60', '1'),
('pk_attendance30', '1'),
('pk_lastraid', '1'),

-- --------------------------------------------------------

-- 
-- Table structure for table `eqdkp_dev_plus_links`
-- 

CREATE TABLE IF NOT EXISTS `eqdkp_dev_plus_links` (
  `link_id` int(12) NOT NULL auto_increment,
  `link_url` varchar(255) NOT NULL default '',
  `link_name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`link_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

-- 
-- Dumping data for table `eqdkp_dev_plus_links`
-- 

INSERT INTO `eqdkp_dev_plus_links` (`link_id`, `link_url`, `link_name`) VALUES 
(4, 'http://www.blasc.de', 'BLASC'),
(5, 'http://www.thottbot.com/de', 'Thottbot'),
(6, 'http://wow.allakhazam.com/', 'Allakhazam');

CREATE TABLE IF NOT EXISTS `eqdkp_dev_plus_update` (
  `name` varchar(255) NOT NULL default '',
  `version` varchar(255) NOT NULL default '',
  `level` varchar(255) NOT NULL default '',
  `changelog` varchar(255) NOT NULL default '',
  `release` varchar(255) NOT NULL default '',
  `download` varchar(255) NOT NULL default '',
  `realname` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;