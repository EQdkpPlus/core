DELETE FROM eqdkp_plus_config WHERE config_name like 'pk_showRss';
DELETE FROM eqdkp_plus_config WHERE config_name like 'pk_air_enable';
DELETE FROM eqdkp_plus_config WHERE config_name like 'pk_Rss_checkURL';
DELETE FROM eqdkp_config WHERE config_name like 'plus_version';
INSERT INTO eqdkp_plus_config VALUES ('pk_showRss', '');
INSERT INTO eqdkp_plus_config VALUES ('pk_air_enable', '1');
INSERT INTO eqdkp_plus_config VALUES ('pk_Rss_checkURL', '1');
INSERT INTO eqdkp_config VALUES ('plus_version', '0.6.1.6' );
ALTER TABLE eqdkp_plus_config CHANGE `config_value` `config_value` TEXT  


