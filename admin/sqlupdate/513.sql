DELETE FROM eqdkp_config WHERE config_name = 'default_game_overwrite';
INSERT INTO eqdkp_config (config_name, config_value) VALUES ('default_game_overwrite', '0');
DELETE FROM eqdkp_config WHERE config_name = 'plus_version';
INSERT INTO eqdkp_config (config_name, config_value) VALUES ('plus_version', '0.5.1.3');

