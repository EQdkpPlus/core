ALTER TABLE eqdkp_raids CHANGE raid_note raid_note TEXT ;
DELETE FROM eqdkp_auth_options WHERE auth_id = '36';
INSERT INTO eqdkp_auth_options (auth_id, auth_value, auth_default) VALUES ('36', 'a_backup', 'N');
ALTER TABLE eqdkp_plus_rss CHANGE updated updated int(11) NOT NULL DEFAULT '0';
DELETE FROM eqdkp_config WHERE config_name like 'plus_version';
INSERT INTO eqdkp_config VALUES ('plus_version', '0.6.2.4' );