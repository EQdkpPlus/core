ALTER TABLE item_cache DROP INDEX item_name;
ALTER TABLE item_cache ADD INDEX (item_name, item_id);
DELETE FROM eqdkp_config WHERE config_name like 'plus_version';
INSERT INTO eqdkp_config VALUES ('plus_version', '0.6.3.3' );