DROP TABLE IF EXISTS eqdkp_plus_links ;
CREATE TABLE IF NOT EXISTS eqdkp_plus_links (
  `link_id` int(12) NOT NULL auto_increment,
  `link_url` varchar(255) NOT NULL default '',
  `link_name` varchar(255) NOT NULL default '',
  `link_window` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`link_id`)
) TYPE=MyISAM ;

INSERT INTO eqdkp_plus_links (link_id, link_url, link_name, link_window) VALUES (1, 'http://www.eqdkp-plus.com', 'EQDKP-Plus', '1');
INSERT INTO eqdkp_plus_links (link_id, link_url, link_name, link_window) VALUES (2, 'http://allvatar.com/', 'Allvatar Free TS Server', '2');