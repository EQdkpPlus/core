DROP TABLE IF EXISTS eqdkp_portal;
CREATE TABLE IF NOT EXISTS eqdkp_portal (id mediumint(8) unsigned NOT NULL auto_increment, name varchar(50) NOT NULL default '', enabled enum('0','1') NOT NULL default '0', settings enum('0','1') NOT NULL default '0', path varchar(255) NOT NULL default '', contact varchar(100) default NULL, url varchar(100) default NULL, autor varchar(100) default NULL, version varchar(7) NOT NULL default '', position varchar(255) NOT NULL default '0', number mediumint(8), plugin varchar(255) NOT NULL default '', PRIMARY KEY  (id));
INSERT INTO eqdkp_portal VALUES (1, 'DKPInfo Module', '1', '0', 'dkpinfo', 'corgan@eqdkp-plus.com', NULL, NULL, '1.0.0', 'left2', 0, '');
INSERT INTO eqdkp_portal VALUES (2, 'Hello World Module', '0', '1', 'helloworld', 'corgan@eqdkp-plus.com', NULL, NULL, '1.0.0', 'right', 6, '');
INSERT INTO eqdkp_portal VALUES (3, 'LastItems Module', '1', '1', 'lastitems', 'corgan@eqdkp-plus.com', NULL, NULL, '1.0.0', 'right', 2, '');
INSERT INTO eqdkp_portal VALUES (4, 'LastRaids Module', '1', '1', 'lastraids', 'corgan@eqdkp-plus.com', NULL, NULL, '1.0.0', 'right', 3, '');
INSERT INTO eqdkp_portal VALUES (5, 'QuickDKP Module', '1', '0', 'quickdkp', 'corgan@eqdkp-plus.com', NULL, NULL, '1.0.0', 'left1', 2, '');
INSERT INTO eqdkp_portal VALUES (6, 'RankImage Module', '0', '1', 'rankimage', 'corgan@eqdkp-plus.com', NULL, NULL, '1.0.0', 'right', 4, '');
INSERT INTO eqdkp_portal VALUES (7, 'Recruitment Module', '0', '0', 'recruitment', 'corgan@eqdkp-plus.com', NULL, NULL, '1.0.0', 'left1', 1, '');
INSERT INTO eqdkp_portal VALUES (8, 'Teamspeak Module', '0', '1', 'teamspeak', 'corgan@eqdkp-plus.com', NULL, NULL, '1.0.0', 'right', 5, '');
INSERT INTO eqdkp_portal VALUES (9, 'nextRaids Module', '0', '1', 'nextraids', 'corgan@eqdkp-plus.com', NULL, NULL, '1.0.0', 'right', 1, 'raidplan');

