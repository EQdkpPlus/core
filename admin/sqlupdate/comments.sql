CREATE TABLE IF NOT EXISTS eqdkp_comments (id int(11) unsigned NOT NULL auto_increment, attach_id int(11) unsigned NOT NULL, userid int(11) unsigned NOT NULL, date VARCHAR(255) NULL, text text NULL, page VARCHAR(255) NULL, PRIMARY KEY  (id)) TYPE = MYISAM ;
ALTER TABLE eqdkp_comments CHANGE `attach_id` `attach_id` VARCHAR( 255 ) NULL DEFAULT NULL ;

