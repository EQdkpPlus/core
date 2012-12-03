DROP TABLE IF EXISTS eqdkp_itemIDs;
CREATE TABLE eqdkp_itemIDs (itemID_id INT( 11 ) NOT NULL AUTO_INCREMENT , itemID_blizID INT( 11 ) NOT NULL , itemID_displayID INT( 11 ) NOT NULL , itemID_armorySlotID INT( 11 ) NOT NULL , itemID_wowheadSlotID INT( 11 ) NOT NULL, PRIMARY KEY  (itemID_id) ) ;
ALTER TABLE eqdkp_members DROP member_xml ;
ALTER TABLE eqdkp_members ADD member_xml BLOB ;


