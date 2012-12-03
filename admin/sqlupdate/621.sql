ALTER TABLE eqdkp_style_config ADD background_img VARCHAR( 255 ) NOT NULL , ADD css_file VARCHAR( 255 ) NOT NULL ;
DELETE FROM eqdkp_config WHERE config_name like 'plus_version';
INSERT INTO eqdkp_config VALUES ('plus_version', '0.6.2.1' );

