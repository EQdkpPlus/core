DROP TABLE IF EXISTS eqdkp_multidkp ;
CREATE TABLE eqdkp_multidkp 
(
`multidkp_id` TINYINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`multidkp_name` VARCHAR( 255 ) NOT NULL ,
`multidkp_disc` TEXT NOT NULL 
) TYPE = MYISAM ;

