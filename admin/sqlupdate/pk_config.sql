DROP TABLE IF EXISTS eqdkp_plus_config;
CREATE TABLE IF NOT EXISTS eqdkp_plus_config (
  `config_name` varchar(255) NOT NULL default '',
  `config_value` varchar(255) default NULL,
  PRIMARY KEY  (`config_name`)
) TYPE=MyISAM ;

INSERT INTO eqdkp_plus_config VALUES ('pk_updatecheck', '1');
INSERT INTO eqdkp_plus_config VALUES ('pk_windowtime', '10');
INSERT INTO eqdkp_plus_config VALUES ('pk_links', '1');
INSERT INTO eqdkp_plus_config VALUES ('pk_bosscount', '1');
INSERT INTO eqdkp_plus_config VALUES ('pk_itemstats', '1');
INSERT INTO eqdkp_plus_config VALUES ('pk_is_icon_loc', 'http://www.buffed.de/images/wow/32/');
INSERT INTO eqdkp_plus_config VALUES ('pk_is_icon_ext', '.png');
INSERT INTO eqdkp_plus_config VALUES ('pk_attendance90', '1');
INSERT INTO eqdkp_plus_config VALUES ('pk_lastraid', '1');
INSERT INTO eqdkp_plus_config VALUES ('pk_class_color', '1');
INSERT INTO eqdkp_plus_config VALUES ('pk_quickdkp', '1');
INSERT INTO eqdkp_plus_config VALUES ('pk_newsloot_limit', 'all');
INSERT INTO eqdkp_plus_config VALUES ('pk_multiTooltip', '1');
INSERT INTO eqdkp_plus_config VALUES ('pk_multiSmarttip', '1');
INSERT INTO eqdkp_plus_config VALUES ('pk_leaderboard', '1');
INSERT INTO eqdkp_plus_config VALUES ('pk_rank_icon', '1');
INSERT INTO eqdkp_plus_config VALUES ('pk_showclasscolumn', '1');
INSERT INTO eqdkp_plus_config VALUES ('pk_show_skill', '1');
INSERT INTO eqdkp_plus_config VALUES ('pk_is_prio_first', 'armory');
INSERT INTO eqdkp_plus_config VALUES ('pk_is_prio_second', 'wowhead');
INSERT INTO eqdkp_plus_config VALUES ('pk_is_prio_third', 'allakhazam');
INSERT INTO eqdkp_plus_config VALUES ('pk_is_prio_fourth', 'allakhazam');
INSERT INTO eqdkp_plus_config VALUES ('pk_is_itemlanguage', 'de');
INSERT INTO eqdkp_plus_config VALUES ('pk_is_itemlanguage_alla', 'deDE');
INSERT INTO eqdkp_plus_config VALUES ('pk_is_autosearch', '1');
INSERT INTO eqdkp_plus_config VALUES ('pk_showRss', '');
INSERT INTO eqdkp_plus_config VALUES ('pk_Rss_Style', '1');
INSERT INTO eqdkp_plus_config VALUES ('pk_Rss_count', '10');

