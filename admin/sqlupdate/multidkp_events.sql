DROP TABLE IF EXISTS eqdkp_multidkp2event ;
CREATE TABLE eqdkp_multidkp2event (
`multidkp2event_id` TINYINT( 5 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`multidkp2event_multi_id` TINYINT( 5 ) NOT NULL ,
`multidkp2event_eventname` VARCHAR( 255 ) NOT NULL 
) TYPE = MYISAM ;
