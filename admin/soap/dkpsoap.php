<?php
/******************************
 * EQdkp
 * Copyright 2002-2005
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * dkpsoap.php
 * begin: Tue April 19 2005
 *
 * $Id: dkpsoap.php 8 2006-05-08 17:15:20Z tsigo $
 *
 ******************************/

define('EQDKP_INC', true);
$eqdkp_root_path = './../../';
include_once($eqdkp_root_path . 'common.php');

//$debug = 1;

$server = new soap_server();
$server->configureWSDL('DKPService',$ns);
$server->wsdl->schemaTargetNamespace=$ns;

$server->register('GetMemberRP',
         array('user' => 'xsd:string', 'password' => 'xsd:string', 'member' => 'xds:string'),
         array('rp' => 'xsd:string'),
         $ns);

$server->register('FindEvent',
         array('user' => 'xsd:string', 'password' => 'xsd:string', 'name_regexp' => 'xsd:string'),
         array('match' => 'xsd:string'),
         $ns);

$server->register('GetLastRaid',
         array('user' => 'xsd:string','password' => 'xsd:string'),
         array('raid_id' => 'xsd:string'),
         $ns);

$server->register('AddMember',
         array('user' => 'xsd:string','password' => 'xsd:string', 
               'member' => 'xsd:string', 'class' => 'xsd:string', 'level' => 'xsd:string'),
	 array('return' => 'xsd:string'),
         $ns);

$server->register('AddRaid',
         array('user' => 'xsd:string','password' => 'xsd:string', 'event_name' => 'xsd:string'),
	 array('return' => 'xsd:string'),
         $ns);

$server->register('AddRaidVal',
         array('user' => 'xsd:string','password' => 'xsd:string', 'raid_id' => 'xsd:string', 'raid_value' => 'xds:string'),
	 array('return' => 'xsd:string'),
         $ns);

$server->register('ComputeRaidVal',
         array('user' => 'xsd:string','password' => 'xsd:string', 'raid_id' => 'xsd:string'),
	 array('raidvalue' => 'xsd:string'),
         $ns);

$server->register('AddRaidAttendee',
         array('user' => 'xsd:string','password' => 'xsd:string', 'raid_id' => 'xsd:string', 'member' => 'xsd:string'),
	 array('return' => 'xsd:string'),
         $ns);

$server->register('DelRaidAttendee',
         array('user' => 'xsd:string','password' => 'xsd:string', 'raid_id' => 'xsd:string', 'member' => 'xsd:string'),
	 array('return' => 'xsd:string'),
         $ns);

$server->register('DoLoot',
         array('user' => 'xsd:string',
		'password' => 'xsd:string', 
		'item' => 'xsd:string', 
		'value' => 'xsd:string', 
		'member' => 'xsd:string', 
		'raid_id' => 'xsd:string'),
	 array('return' => 'xsd:string'),
         $ns);


function GetMemberRP($user, $password, $member)
{

   global $db, $eqdkp;

   $auth = soap_validate($user,$password);	

   // Valid member?
   $sql = "SELECT count(*) FROM " . MEMBERS_TABLE . "
           WHERE member_name = '$buyer'";
   $isvalid = $db->query_first($sql);
   if ( !( isset($isvalid) || $isvalid > 0 ) ) {
    $status = "ENOMEMBER";
    return($status);
   }


   if ( $auth == 'R' || $auth == 'W' ) {

   $sql = "SELECT (member_earned + member_adjustment - member_spent) as member_current 
	   FROM " . MEMBERS_TABLE ."
	   WHERE member_name = '$member'";
   $dkp = $db->query_first($sql);

   if ( isset($dkp) ) { 
    // if set, return value (we try to return $status always, so set it)
    $status = $dkp;
    return($status); 
   } else { 
    // otherwise, we didn't find them - error out
    $status = "ENOTFOUND"; 
    return($status); 
   } } 
  
  else {
     // if auth isnt Read or Write, error out
     $status = "ENOAUTH";
     return($status);
 }
   

}

function AddRaidVal($user, $password, $raid_id, $raid_value)
{
   global $db, $eqdkp;

   $auth = soap_validate($user,$password);	

   if ($auth == 'W') {

    if (isset($raid_value) && isset($raid_id) ) {
	
	$sql = "SELECT raid_value FROM " . RAIDS_TABLE . " WHERE raid_id = '$raid_id'";
	$old_val = $db->query_first($sql);

     $sql = "UPDATE " . RAIDS_TABLE . " SET raid_value = '$raid_value' 
             WHERE raid_id = '$raid_id'";
     
     if ( !( $result = $db->query($sql) )) {
  	  $status = "ESQLERR";
     } else {
          $status = "OK";
     }

    // Add this value to everyone currently in attendance
    $sql = "SELECT member_name FROM ". RAID_ATTENDEES_TABLE ." WHERE raid_id = '$raid_id'";
    $result = $db->query($sql);

    while ( $row = $db->fetch_record($result) ) {

	$sql = "UPDATE ". MEMBERS_TABLE ." 
		SET member_earned = (member_earned + '$raid_value' - '$old_val')	
		WHERE member_name = '". $row['member_name'] ."'";
	$db->query($sql);
    }


    } else {

      $status = "EBADVALUES";

    } // close of isset loop

   } else {

     $status = "ENOAUTH";

   } // close auth loop


 return($status);


}

function ComputeRaidVal($user, $password, $raid_id)
{
   global $db, $eqdkp;

   $auth = soap_validate($user,$password);	

  if ( !(isset($raid_id)) ) {
	$status = "EBADPARM";
	return($status);
  }

   if ($auth == 'W') {

	// count members
	$sql = "SELECT count(*) FROM " . RAID_ATTENDEES_TABLE ."
		WHERE raid_id = '$raid_id'";
	
	if ( !($member_count = $db->query_first($sql)  )) {
  	  	$status = "ESQLERR";
	        return($status);
     	} 

	if ( ($member_count < 1) || (!(isset($member_count)))  ) {
  	  	$status = "EBADCOUNT";
	        return($status);
     	} 
	
	// sum value of all loot
	$sql = "SELECT sum(item_value) FROM " . ITEMS_TABLE . "
		WHERE raid_id = '$raid_id'";

	if (!($item_value = $db->query_first($sql))) {
  	  	$status = "ESQLERR";
	        return($status);
     	} 
	
	// value divided by member count = value
	// NOTICE!!!
	//
	// Raid value is a whole number only. Minimum value is 1.
	// All raids will get assigned 1RP if there was loot.
	// Otherwise, no loot = no RP.
	// member_count = 1? return item_value

        if ( ($item_value > 0) && ($member_count > 1) ) {

        $raid_value = floor( round( .5 + ($item_value/$member_count) ) );

        } elseif ($member_count = 1) {

        $raid_value = $item_value;

        } else {

        $raid_value = 0;

        }

	// update raid_value

	if ( (isset($raid_value)) && (isset($raid_id)) && (isset($member_count)) ) {

		$sql = "SELECT raid_value FROM " . RAIDS_TABLE . " WHERE raid_id = '$raid_id'";
		$old_val = $db->query_first($sql);

	        $sql = "UPDATE " . RAIDS_TABLE . " SET raid_value = '$raid_value' 
       		         WHERE raid_id = '$raid_id'";
     
       	 	if ( !( $result = $db->query($sql) )) {
  			     $status = "ESQLERR";
		             return($status);
       	 	} else {
       			     $status = $raid_value;
       	 	}

	  	    // Add this value to everyone currently in attendance
		    $sql = "SELECT member_name FROM ". RAID_ATTENDEES_TABLE ." WHERE raid_id = '$raid_id'";
		    $result = $db->query($sql);

		    while ( $row = $db->fetch_record($result) ) {
		
		        $sql = "UPDATE ". MEMBERS_TABLE ."
		                SET member_earned = (member_earned + '$raid_value' - '$old_val')
		                WHERE member_name = '". $row['member_name'] ."'";
		        $db->query($sql);
		    }



	} else {
	
	$status="ESQLFAIL";
        return($status);
	
	}

   } else {

     $status = "ENOAUTH";
     return($status);

   } // close auth loop

 return($status);

}


function AddRaid($user, $password, $event_name)
{
	global $db, $eqdkp;

        $current_time = time();
        $clean_event_name = str_replace("'","", $event_name);

   	$auth = soap_validate($user,$password);

     if ( $auth == 'W' ) {

	// get event value
        $raid_value = $db->query_first('SELECT event_value FROM ' . EVENTS_TABLE . " 
					WHERE event_name='" . addslashes($clean_event_name) . "'");


	// If there was no event, create one with a zero value
        if ((  ! (isset($raid_value)) || empty($raid_value)   )) {

          $raid_value = '0.00';

          $query = $db->build_query('INSERT', array(
              'event_name'     => ($clean_event_name),
              'event_value'    => $raid_value,
              'event_added_by' => $user)
          );

          if ( !($db->query('INSERT INTO ' . EVENTS_TABLE . $query)) ) {

	    die("SQL Error - Aborting on event insert - contact thundarr@gmail.com");

	  }

          $this_event_id = $db->insert_id();


	// Else, if there was a event, zero out its value
	// since we never want a event value used
        } else {

          $raid_value = '0.00';

          if ( !($db->query("UPDATE " . EVENTS_TABLE . " SET event_value = '0.00' WHERE event_name = '" . addslashes($clean_event_name) . "'")) ) {

            die("SQL Error - Aborting on event zeroing - contact thundarr@gmail.com");

          }

	}

          $query = $db->build_query('INSERT', array(
                'raid_name'     => $clean_event_name,
                'raid_date'     => $current_time,
                'raid_value'    => $raid_value,
                'raid_added_by' => $user)
          );

	  if ( ! ($db->query('INSERT INTO ' . RAIDS_TABLE . $query) )) {

	    die("SQL Error - Aborting on raid insert - contact thundarr@gmail.com");

	  }

          $this_raid_id = $db->insert_id();

	  $status = $this_raid_id;
	  return($status);

	} else {
	
	  $status = "EBADAUTH";
	  return($status);
	
	}

}

function AddRaidAttendee($user, $password, $raid_id, $member)
{

        global $db, $eqdkp;
        $current_time = time();

        $auth = soap_validate($user,$password);

        $inactive_time = mktime(0, 0, 0, date('m'), date('d')-$eqdkp->config['inactive_period'], date('Y'));
	
	$sql = "SELECT member_id FROM " . MEMBERS_TABLE . " 
		WHERE member_name = '$member'";
	$isvalid = $db->query_first($sql);

	if ( (isset($isvalid) && ($isvalid > 0) ) ) { 

         if ( $auth == 'W' ) {

	   $sql = "SELECT count(*) FROM " . RAID_ATTENDEES_TABLE . "
		   WHERE raid_id = '$raid_id' 
		   AND member_name = '$member'";
	   $duplicate = $db->query_first($sql);
	
	   if ( $duplicate > 0 ) {
		$status = "EDUPLICATE";
		return($status);
	   }

           $sql = "INSERT INTO " . RAID_ATTENDEES_TABLE . " (raid_id, member_name)
                   VALUES ('$raid_id', '$member')";
           $db->query($sql);

	   $sql = "UPDATE " . MEMBERS_TABLE . " m, " . RAIDS_TABLE . " r 
		   SET m.member_lastraid = r.raid_date 
		   WHERE m.member_name = '$member' 
		   AND r.raid_id = '$raid_id'"; 
	   $db->query($sql);

	   $sql = "UPDATE " . MEMBERS_TABLE . " SET member_raidcount = (member_raidcount + 1) WHERE member_name = '$member'";
	   $db->query($sql);
 
           // Active -> Inactive
           $db->query('UPDATE ' . MEMBERS_TABLE . " SET member_status='0' WHERE (member_lastraid < " .  $inactive_time . ") AND (member_status='1')");

           // Inactive -> Active
           $db->query('UPDATE ' . MEMBERS_TABLE . " SET member_status='1' WHERE (member_lastraid >= " . $inactive_time . ") AND (member_status='0')");

 	   $status = "OK";
           return($status);
 	
         } else {
 
           $status = "EBADAUTH";
           return($status);
 
         } 

        } else {
	
	$status = "ENOMEMBER";
	return($status);
	
	}
      
	

}

function AddMember($user, $password, $member, $class, $level)
{
     global $db, $eqdkp;

     $auth = soap_validate($user, $password);
	
     if ($auth == 'W') {

	 // first, insert the name and level :: race_id Unknown is always zero
	 $sql = "INSERT INTO " . MEMBERS_TABLE . " (member_name, member_level)
		 VALUES ('$member', '$level')";

	 if (!($db->query($sql))) {
	    $status = "ESQLFAIL1";
	    return($status);
	 }
	 

	 // now update class_id
	 $sql = "UPDATE " . MEMBERS_TABLE . " m, " . CLASS_TABLE . " c
		 SET m.member_class_id = c.class_id
		 WHERE c.class_name = '$class'
		 AND m.member_name = '$member'
		 AND '$level' >= c.class_min_level
		 AND '$level' <= c.class_max_level";

	 if (!($db->query($sql))) {
	    $status = "ESQLFAIL2";
	    return($status);
	 }

	$status = "OK";
	return($status);

     } else {

	$status = "ENOAUTH";
	return($status);
     }
	
	

}

function GetLastRaid($user, $password)
{

        global $db, $eqdkp;

        $auth = soap_validate($user,$password);

        if ( $auth == 'R' || $auth == 'W' ) {

	  $sql = "SELECT raid_id, raid_name FROM " . RAIDS_TABLE . "
	       	  ORDER BY raid_id DESC LIMIT 1";
	  $last_raid_id = $db->query_first($sql);

	  $status = $last_raid_id;
	  return($status);

        } else {

       	 $status = "EBADAUTH";
       	 return($status);

        }
}

function DelRaidAttendee($user, $password, $raid_id, $member)
{

        global $db, $eqdkp;

        $auth = soap_validate($user,$password);

	// Valid member?
        $sql = "SELECT count(*) FROM " . MEMBERS_TABLE . "
                WHERE member_name = '$member'";
        $isvalid = $db->query_first($sql);
        if ( !( isset($isvalid) || $isvalid > 0 ) ) {
         $status = "ENOMEMBER";
         return($status);
	}

	// Valid raid id?
        $sql = "SELECT count(*) FROM " . RAIDS_TABLE . "
                WHERE raid_id = '$raid_id'";
        $israid = $db->query_first($sql);
        if ( !( isset($israid) || $israid > 0 ) ) {
	 $status = "ENORAID";
	 return($status);
	}

         if ( $auth == 'W' ) {

           $sql = "SELECT count(*) FROM " . RAID_ATTENDEES_TABLE . "
                   WHERE raid_id = '$raid_id'
                   AND member_name = '$member'";
           $duplicate = $db->query_first($sql);

           if ( ($duplicate == 0) || (!(isset($duplicate))) ) {
                $status = "ENOEXIST";
                return($status);
           }

           $sql = "DELETE FROM " . RAID_ATTENDEES_TABLE . " WHERE raid_id = '$raid_id' AND member_name = '$member'";
           $db->query($sql);

	   $sql = "SELECT max(raid_id) FROM " . RAID_ATTENDEES_TABLE . " WHERE member_name = '$member'";
	   $last_raid = $db->query_first($sql);
	
	   $sql = "UPDATE " . MEMBERS_TABLE . " m, " . RAIDS_TABLE . " r
		   SET m.member_lastraid = r.raid_date
		   WHERE raid_id = '$last_raid'
		   AND m.member_name = '$member'";
           $db->query($sql);

	   $sql = "UPDATE " . MEMBERS_TABLE . " SET member_raidcount = (member_raidcount - 1)";
	   $db->query($sql);


           $status = "OK";
           return($status);


        } else {

        $status = "ENOMEMBER";
        return($status);

        }



}

function DoLoot($user, $password, $item, $value, $buyer, $raid_id)
{

	global $db, $eqdkp;

	// Taken from includes/eqdkp.php
        // Normalize data
        $part1 = htmlspecialchars(stripslashes($item));
        $part2 = htmlspecialchars(stripslashes($current_time));
        $part3 = htmlspecialchars(stripslashes($raid_id));

        // Get the first 10-11 digits of each md5 hash
        $part1 = substr(md5($part1), 0, 10);
        $part2 = substr(md5($part2), 0, 11);
        $part3 = substr(md5($part3), 0, 11);

        // Group the hashes together and create a new hash based on uniqid()
        $group_key = $part1 . $part2 . $part3;
        $group_key = md5(uniqid($group_key));


        $current_time = time();
	$itemss = stripslashes($item);

	// auth 
	$auth = soap_validate($user,$password);

	// Valid raid id?
        $sql = "SELECT count(*) FROM " . RAIDS_TABLE . "
                WHERE raid_id = '$raid_id'";
        $israid = $db->query_first($sql);
        if ( !( isset($israid) || $israid > 0 ) ) {
	 $status = "ENORAID";
	 return($status);
	}

	// Valid member?
        $sql = "SELECT count(*) FROM " . MEMBERS_TABLE . "
                WHERE member_name = '$buyer'";
        $isvalid = $db->query_first($sql);
        if ( !( isset($isvalid) || $isvalid > 0 ) ) {
         $status = "ENOMEMBER";
         return($status);
	}


	if ( $auth == 'W' ) {

	  // insert items
	  $sql = "INSERT INTO ". ITEMS_TABLE ." 
		(item_name, item_buyer, raid_id, item_value, item_date, item_group_key, item_added_by) 
		VALUES 
		('$itemss', '$buyer', '$raid_id', '$value', '$current_time', '$group_key', '$user')";
          $db->query($sql);
          
          // fix their spent RP 
          $sql = "UPDATE " . MEMBERS_TABLE . " 
	  	  SET member_spent = member_spent + " . $value . " 
		  WHERE member_name =  '$buyer'";
          $db->query($sql);
	
	  $status = "OK";
	  return($status);
	
        } else {	
	 // I dont have write permission; error out
         $status = "ENOAUTH";
         return($status);
	}



}

function FindEvent($user, $password, $name_regexp)
{
	global $db, $eqdkp;
	$match = "";

	$auth = soap_validate($user,$password);	

   	if ( $auth == 'R' || $auth == 'W' ) {
	
	  $sql = "SELECT event_name FROM eqdkp_events WHERE (event_name REGEXP '$name_regexp')";
	  $result = $db->query($sql);
 
	  while ( $row = $db->fetch_record($result) ) {
	   $match .= $row['event_name'] ."::";
	  } 
	return($match);
	} else {
        // if auth isnt Read or Write, error out
        $status = "ENOAUTH";
        return($status);
        }
	
}

$server->service($HTTP_RAW_POST_DATA);
?>
