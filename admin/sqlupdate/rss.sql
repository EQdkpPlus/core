DROP TABLE IF EXISTS eqdkp_plus_rss;
CREATE TABLE eqdkp_plus_rss (id int(11) NOT NULL auto_increment, updated text NOT NULL,  rss text NOT NULL,  game text NOT NULL,  PRIMARY KEY  (id) ) ;
INSERT INTO eqdkp_plus_config VALUES ('pk_showRss', '');
INSERT INTO eqdkp_plus_config VALUES ('pk_Rss_Style', '1');
INSERT INTO eqdkp_plus_config VALUES ('pk_Rss_count', '10');
