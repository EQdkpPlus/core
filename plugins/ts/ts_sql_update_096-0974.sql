DROP TABLE IF EXISTS eqdkp_tradeskill_config;
CREATE TABLE eqdkp_tradeskill_config (config_name varchar(255) NOT NULL default '', config_value varchar(255) default NULL, PRIMARY KEY (config_name));

INSERT INTO eqdkp_tradeskill_config VALUES ('ts_restrict_professions', '1');
INSERT INTO eqdkp_tradeskill_config VALUES ('ts_use_infosite', 'buffed');
INSERT INTO eqdkp_tradeskill_config VALUES ('ts_single_show', '');

INSERT INTO eqdkp_auth_options VALUES ('602', 'u_ts_confirm', 'Y');
INSERT INTO eqdkp_auth_options VALUES ('603', 'a_ts_admin', 'N');

ALTER TABLE `eqdkp_tradeskills` ADD `inuse` ENUM( '0', '1' ) DEFAULT '1' NOT NULL AFTER `trade_name` ;
INSERT INTO eqdkp_tradeskills VALUES (7, 'http://wow.allakhazam.com/images/icons/INV_Misc_Food_15.png', 'Kochen',0);
INSERT INTO eqdkp_tradeskills VALUES (8, 'http://wow.allakhazam.com/images/icons/INV_Misc_QuestionMark.png', 'Juwelenschleifen','0');

