ALTER TABLE eqdkp_users ADD first_name VARCHAR( 255 ) NOT NULL, ADD last_name VARCHAR( 255 ) NOT NULL, ADD country VARCHAR( 255 ) NOT NULL , ADD town VARCHAR( 250 ) NOT NULL , ADD state VARCHAR( 250 ) NOT NULL , ADD ZIP_code INT NOT NULL , ADD phone VARCHAR( 255 ) NOT NULL , ADD cellphone VARCHAR( 255 ) NOT NULL , ADD address TEXT NOT NULL , ADD allvatar_nick VARCHAR( 255 ) NOT NULL , ADD icq VARCHAR( 255 ) NOT NULL , ADD skype VARCHAR( 255 ) NOT NULL , ADD msn VARCHAR( 255 ) NOT NULL , ADD irq VARCHAR( 255 ) NOT NULL , ADD gender VARCHAR( 255 ) NOT NULL , ADD birthday VARCHAR( 255 ) NOT NULL ;
ALTER TABLE eqdkp_plus_links ADD link_menu TINYINT NOT NULL DEFAULT 0 ;
ALTER TABLE eqdkp_news ADD news_flags TINYINT NOT NULL DEFAULT 0 ;
DELETE FROM eqdkp_config WHERE config_name like 'plus_version';
INSERT INTO eqdkp_config VALUES ('plus_version', '0.6.2.3' );





