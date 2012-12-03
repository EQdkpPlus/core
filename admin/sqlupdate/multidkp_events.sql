DROP TABLE IF EXISTS eqdkp_multidkp2event ;
CREATE TABLE eqdkp_multidkp2event (
`multidkp2event_id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`multidkp2event_multi_id` INT( 11 ) NOT NULL ,
`multidkp2event_eventname` VARCHAR( 255 ) NOT NULL
) TYPE = MYISAM ;
