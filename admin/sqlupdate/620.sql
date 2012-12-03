 ALTER TABLE eqdkp_portal ADD visibility ENUM( '0', '1', '2' ) NOT NULL DEFAULT '0';
 ALTER TABLE eqdkp_portal ADD collapsable ENUM( '0', '1' ) NOT NULL DEFAULT '1'; 
 DELETE FROM eqdkp_config WHERE config_name like 'plus_version';
 INSERT INTO eqdkp_config VALUES ('plus_version', '0.6.2.0' );


