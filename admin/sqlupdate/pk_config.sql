DROP TABLE IF EXISTS eqdkp_plus_config;
CREATE TABLE IF NOT EXISTS eqdkp_plus_config (
  `config_name` varchar(255) NOT NULL default '',
  `config_value` varchar(255) default NULL,
  PRIMARY KEY  (`config_name`)
) TYPE=MyISAM ;

INSERT INTO eqdkp_plus_config (config_name, config_value) VALUES ('pk_updatecheck', '1');
INSERT INTO eqdkp_plus_config (config_name, config_value) VALUES ('pk_windowtime', '10');
INSERT INTO eqdkp_plus_config (config_name, config_value) VALUES ('pk_multidkp', '1');
INSERT INTO eqdkp_plus_config (config_name, config_value) VALUES ('pk_rank', '1');
INSERT INTO eqdkp_plus_config (config_name, config_value) VALUES ('pk_level', '1');
INSERT INTO eqdkp_plus_config (config_name, config_value) VALUES ('pk_lastloot', '1');
INSERT INTO eqdkp_plus_config (config_name, config_value) VALUES ('pk_attendanceAll', '1');
INSERT INTO eqdkp_plus_config (config_name, config_value) VALUES ('pk_links', '1');
INSERT INTO eqdkp_plus_config (config_name, config_value) VALUES ('pk_bosscount', '1');
INSERT INTO eqdkp_plus_config (config_name, config_value) VALUES ('pk_itemstats', '1');
INSERT INTO eqdkp_plus_config (config_name, config_value) VALUES ('pk_is_icon_loc', 'http://www.buffed.de/images/wow/32/');
INSERT INTO eqdkp_plus_config (config_name, config_value) VALUES ('pk_is_icon_ext', '.png');
INSERT INTO eqdkp_plus_config (config_name, config_value) VALUES ('pk_attendance90', '1');
INSERT INTO eqdkp_plus_config (config_name, config_value) VALUES ('pk_attendance60', '1');
INSERT INTO eqdkp_plus_config (config_name, config_value) VALUES ('pk_attendance30', '1');
INSERT INTO eqdkp_plus_config (config_name, config_value) VALUES ('pk_lastraid', '1');
