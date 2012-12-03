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
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

$user->check_auth('a_');

echo("Checking for leading/trailing whitespaces on events. Please make sure to check your ctrt triggers by yourself!<br>");

//check event names
echo("Event table:<br>");
$e = array();
$te = array();
$sql = "SELECT event_name FROM __events";
$result = $db->query($sql);
while($row = $db->fetch_record($result)){
  $e[] = $row['event_name'];
  $te[] = trim($row['event_name']);
}

$ec = count($e);
for($i=0; $i < $ec; $i++){
  if(strlen($te[$i]) < strlen($e[$i])){
    //ok, we have some whitespaces in eventnames, that suxx.
    echo("Found:".$e[$i]."<br>");
    $sql2 = "UPDATE __events SET event_name = '".$te[$i]."' WHERE event_name = '".$e[$i]."'"; 
    $db->query($sql2);
  }
}

echo("Adjustment table:<br>");
$e = array();
$te = array();
$sql = "SELECT raid_name FROM __adjustments";
$result = $db->query($sql);
while($row = $db->fetch_record($result)){
  $e[] = $row['raid_name'];
  $te[] = trim($row['raid_name']);
}

$ec = count($e);
for($i=0; $i < $ec; $i++){
  if(strlen($te[$i]) < strlen($e[$i])){
    //ok, we have some whitespaces in eventnames, that suxx.
    echo("Found:".$e[$i]."<br>");
    $sql2 = "UPDATE __adjustments SET raid_name = '".$te[$i]."' WHERE raid_name = '".$e[$i]."'"; 
    $db->query($sql2);
  }
}

echo("Raid table:<br>");
$e = array();
$te = array();
$sql = "SELECT raid_name FROM __raids";
$result = $db->query($sql);
while($row = $db->fetch_record($result)){
  $e[] = $row['raid_name'];
  $te[] = trim($row['raid_name']);
}

$ec = count($e);
for($i=0; $i < $ec; $i++){
  if(strlen($te[$i]) < strlen($e[$i])){
    //ok, we have some whitespaces in eventnames, that suxx.
    echo("Found:".$e[$i]."<br>");
    $sql2 = "UPDATE __raids SET raid_name = '".$te[$i]."' WHERE raid_name = '".$e[$i]."'"; 
    $db->query($sql2);
  }
}

echo("MultiDKP2Event table:<br>");
$e = array();
$te = array();
$sql = "SELECT multidkp2event_eventname FROM __multidkp2event";
$result = $db->query($sql);
while($row = $db->fetch_record($result)){
  $e[] = $row['multidkp2event_eventname'];
  $te[] = trim($row['multidkp2event_eventname']);
}

$ec = count($e);
for($i=0; $i < $ec; $i++){
  if(strlen($te[$i]) < strlen($e[$i])){
    //ok, we have some whitespaces in eventnames, that suxx.
    echo("Found:".$e[$i]."<br>");
    $sql2 = "UPDATE __multidkp2event SET multidkp2event_eventname = '".$te[$i]."' WHERE multidkp2event_eventname = '".$e[$i]."'"; 
    $db->query($sql2);
  }
}

die("Done!");

?>
