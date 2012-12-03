<?php
/******************************
 * EQdkp
 * Copyright 2002-2005
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * fix_negative.php
 *
 ******************************/

define('EQDKP_INC', true);
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');

$sql = "SELECT m.member_name, m.member_raidcount, count(ra.raid_id) AS cnt, sum(r.raid_value), m.member_earned 
	FROM " . RAIDS_TABLE . " r, " . RAID_ATTENDEES_TABLE . " ra, " . MEMBERS_TABLE . " m 
	WHERE ra.raid_id = r.raid_id 
	AND ra.member_name = m.member_name 
	GROUP BY m.member_name 
	HAVING m.member_raidcount <> cnt";

if ( !($members_result = $db->query($sql)) )
{
    message_die('Could not obtain requested information', '', __FILE__, __LINE__, $sql);
}

while ( $row = $db->fetch_record($members_result) )
{

	print "Member: " . $row['member_name'] . " has " . $row['cnt'] . " 
	       recorded raids in the raid_attendees table, but is only 
	       accounted for in " . $row['member_raidcount'] . " raids 
	       in him member table entry.<BR>";

	$sql = "UPDATE ". MEMBERS_TABLE ." SET member_raidcount = ".$row['cnt']." WHERE member_name = '".$row['member_name']."'";
	print $sql . "<BR><BR>";
	$db->query($sql);
}

$sql = "DROP TABLE IF EXISTS tempqw_fixearned";

if (!($return_code = $db->query($sql)))
{
    message_die('Could not run tha big command ', '', __FILE__, __LINE__, $sql);
}

$sql = "CREATE TABLE tempqw_fixearned (
member_name varchar(30) NOT NULL UNIQUE,
member_earned float(11,2) NOT NULL default '0.00',
member_raidcount smallint(5) NOT NULL DEFAULT 0,
PRIMARY KEY (member_name)
)TYPE=InnoDB";

if (!($return_code = $db->query($sql)))
{
    message_die('Could not run tha big command ', '', __FILE__, __LINE__, $sql);
}


$sql = "INSERT INTO tempqw_fixearned
SELECT m.member_name, sum(r.raid_value), count(ra.raid_id)
FROM " . MEMBERS_TABLE . " m, " . RAIDS_TABLE . " r, " . RAID_ATTENDEES_TABLE . " ra
WHERE ra.raid_id = r.raid_id AND ra.member_name = m.member_name
GROUP BY ra.member_name";

if (!($return_code = $db->query($sql)))
{
    message_die('Could not run tha big command ', '', __FILE__, __LINE__, $sql);
}


$sql = "UPDATE " . MEMBERS_TABLE . " m, tempqw_fixearned f
SET m.member_earned = f.member_earned
WHERE m.member_name = f.member_name";

if (!($return_code = $db->query($sql)))
{
    message_die('Could not run tha big command ', '', __FILE__, __LINE__, $sql);
}


$sql = "UPDATE " . MEMBERS_TABLE . " m, tempqw_fixearned f
SET m.member_raidcount = f.member_raidcount
WHERE m.member_name = f.member_name";

if (!($return_code = $db->query($sql)))
{
    message_die('Could not run tha big command ', '', __FILE__, __LINE__, $sql);
}



?>
