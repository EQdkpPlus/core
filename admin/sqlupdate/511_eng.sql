DELETE FROM eqdkp_config WHERE config_name = 'plus_version';
DELETE FROM eqdkp_config WHERE config_name = 'default_game';
DELETE FROM eqdkp_config WHERE config_name = 'game_language';
DELETE FROM eqdkp_plus_config WHERE config_name = 'pk_debug';
DELETE FROM eqdkp_plus_config WHERE config_name = 'pk_itemstats_debug';
INSERT INTO eqdkp_config (config_name, config_value) VALUES ('plus_version', '0.5.1.1');
INSERT INTO eqdkp_config (config_name, config_value) VALUES ('default_game', 'WoW');
INSERT INTO eqdkp_config (config_name, config_value) VALUES ('game_language', 'en');
INSERT INTO eqdkp_plus_config (config_name, config_value) VALUES ('pk_debug', '0');
INSERT INTO eqdkp_plus_config (config_name, config_value) VALUES ('pk_itemstats_debug', '0');

