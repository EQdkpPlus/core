TRUNCATE TABLE eqdkp_adjustments;
TRUNCATE TABLE eqdkp_items;
TRUNCATE TABLE eqdkp_raids;
TRUNCATE TABLE eqdkp_raid_attendees;
UPDATE eqdkp_members SET member_earned = '0', member_spent = '0', member_adjustment = '0', member_firstraid = '', member_lastraid = '', member_raidcount = '0' ;

