<?php
/******************************
 * EQDKP PLUGIN: PLUSkernel
 * (c) 2006 - 2007 by EQDKP Plus Dev Team
 * http://www.kompsoft.de   
 * ------------------
 * plus.functions.php
 * Changed: Mai 10, 2007 Corgan
 * 
 ******************************/

/**
* This is for the addons to identify the PLUS as a PLUS
*
* if(function_exists(isEQDKPPLUS)){
*	 echo "ICH BIN DEUTSCHLAND";
* }
* ECHO ROFL Walle. :D
*
*/
function isEQDKPPLUS(){
	$iamplus = true;
	return $iamplus;
}

function get_linked_raid_note($raid_note, $name2, $name3)
{
	global $boss_raid_note, $SID,$db , $eqdkp, $conf_plus,$user;
	
	$boss = stripslashes($raid_note);

	$sql = 'SELECT * FROM ' . BP_CONFIG_TABLE;
	if(($bossprogress_result = $db->query($sql))) 
	{ 
				
		while($roww = $db->fetch_record($bossprogress_result)) 
		{
			unset($bpname);
			$bossvalue = str_replace('pb_','',$roww['config_name']);
			if($bossvalue==$name3)
			{
				$bpname = explode(",",$roww['config_value']);
				foreach($bpname as $wert)
				{
					$searchnames .= $wert.',';
				}		
				$searchnames = htmlspecialchars($searchnames);
			}
			
			
			$valuearray = explode(",",$roww['config_value']) ;			
			foreach($valuearray as $wert)
			{$bossprogress[] = $wert;}	

			$bossprogress[] = $user->lang[$bossvalue]['short'] ;
			$bossprogress[] = $user->lang[$bossvalue]['long'] ;
			
		}
		if(in_array("'".$boss."'", $bossprogress) or in_array($boss, $bossprogress))
		{
			return '<a href="' . $eqdkp->config['server_path'] . 'viewboss.php'.$SID.'&amp;boss='.$raid_note.'&amp;name2='.$name2.'&amp;name3='.$name3.'&amp;search='.$searchnames .'">'.$raid_note.'</a>';
		}
	}
	
	// schauen ob boss bekannt ist
	//if(in_array($boss, $boss_raid_note) and $conf_plus['pk_bossloot'] == 1)
	//{
	//	return '<a href="' . $eqdkp->config['server_path'] . 'viewboss.php'.$SID.'&amp;boss='.$raid_note.'">'.$raid_note.'</a>';
	//}
	
	return $raid_note;
}

// return the Classname
function get_classnamebyID($classID)
{
	$_return = '' ;
    global $db, $eqdkp;
	$sql = "select class_name from ".CLASSES_TABLE ." where class_id = ".$classID ;
    $result = $db->query($sql);

 	while ( $row = $db->fetch_record($result) )
 	{
		$_return = $row[class_name];
	}
}

// return Classname if Membername is given
function get_classNamebyMemberName($Mname)
{

	$_return = '' ;
    global $db, $eqdkp;


	$sql = 'SELECT c.class_name
            FROM ( ' . MEMBERS_TABLE . ' m
            LEFT JOIN ' . CLASS_TABLE . " c
            ON m.member_class_id = c.class_id)
            WHERE m.member_name = '".$Mname."'";

    $result = $db->query($sql);

 	while ( $row = $db->fetch_record($result) )
 	{
		$_return = $row[class_name];
	}
return($_return) ;
}

// Return Class with link to Listmembers with Filter = class
function get_classNameImgListmembers($class)
{

global $eqdkp;


	$_return = $class ;
	switch ($class)
	{
		case "Druid"        : $_return = "<a class=Druid  href='".$eqdkp->config['server_path']."listmembers.php?s=&filter=Druid'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Druid.gif' alt=''>&nbsp;Druid</a>";break;
		case "Warlock"      : $_return = "<a class=Warlock href='".$eqdkp->config['server_path']."listmembers.php?s=&filter=Warlock'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Warlock.gif' alt=''>&nbsp;Warlock</a>";break;
		case "Hunter"       : $_return = "<a class=Hunter  href='".$eqdkp->config['server_path']."listmembers.php?s=&filter=Hunter'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Hunter.gif' alt=''>&nbsp;Hunter</a>";break;
		case "Warrior"      : $_return = "<a class=Warrior href='".$eqdkp->config['server_path']."listmembers.php?s=&filter=Warrior'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Warrior.gif' alt=''>&nbsp;Warrior</a>";break;
		case "Mage"         : $_return = "<a class=Mage  href='".$eqdkp->config['server_path']."listmembers.php?s=&filter=Mage'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Mage.gif' alt=''>&nbsp;Mage</a>";break;
		case "Paladin"      : $_return = "<a class=Paladin href='".$eqdkp->config['server_path']."listmembers.php?s=&filter=Paladin'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Paladin.gif' alt=''>&nbsp;Paladin</a>";break;
		case "Priest"       : $_return = "<a class=Priest  href='".$eqdkp->config['server_path']."listmembers.php?s=&filter=Priest'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Priest.gif' alt=''>&nbsp;Priest</a>";break;
		case "Rogue"        : $_return = "<a class=Rogue  href='".$eqdkp->config['server_path']."listmembers.php?s=&filter=Rogue'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Rogue.gif' alt=''>&nbsp;Rogue</a>";break;
		case "Shaman"       : $_return = "<a class=Shaman  href='".$eqdkp->config['server_path']."listmembers.php?s=&filter=Shaman'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Shaman.gif' alt=''> Shaman</a>";break;
		case "Default"      : $_return = "<img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Unknown.gif' alt=''>&nbsp;Unknown";break;

		case "Druide"       : $_return = "<a class=Druid  href='".$eqdkp->config['server_path']."listmembers.php?s=&filter=Druide'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Druid.gif' alt=''>&nbsp;Druide</a>";break;
		case "Hexenmeister" : $_return = "<a class=Warlock href='".$eqdkp->config['server_path']."listmembers.php?s=&filter=Hexenmeister'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Warlock.gif' alt=''>&nbsp;Hexenmeister</a>";break;
		case "Jäger"        : $_return = "<a class=Hunter  href='".$eqdkp->config['server_path']."listmembers.php?s=&filter=Jäger'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Hunter.gif' alt=''>&nbsp;Jäger</a>";break;
		case "Krieger"      : $_return = "<a class=Warrior href='".$eqdkp->config['server_path']."listmembers.php?s=&filter=Krieger'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Warrior.gif' alt=''>&nbsp;Krieger</a>";break;
		case "Magier"       : $_return = "<a class=Mage  href='".$eqdkp->config['server_path']."listmembers.php?s=&filter=Magier'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Mage.gif' alt=''>&nbsp;Magier</a>";break;
		case "Paladin"      : $_return = "<a class=Paladin href='".$eqdkp->config['server_path']."listmembers.php?s=&filter=Paladin'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Paladin.gif' alt=''>&nbsp;Paladin</a>";break;
		case "Priester"     : $_return = "<a class=Priest  href='".$eqdkp->config['server_path']."listmembers.php?s=&filter=Priester'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Priest.gif' alt=''>&nbsp;Priester</a>";break;
		case "Schurke"      : $_return = "<a class=Rogue  href='".$eqdkp->config['server_path']."listmembers.php?s=&filter=Schurke'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Rogue.gif' alt=''>&nbsp;Schurke</a>";break;
		case "Schamane"     : $_return = "<a class=Shaman  href='".$eqdkp->config['server_path']."listmembers.php?s=&filter=Shaman'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Shaman.gif' alt=''> Schamane</a>";break;
		case "Unknown"      : $_return = "<img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Unknown.gif' alt=''>&nbsp;Unknown";break;
	 }
return($_return) ;
}



// Return colored Name with link to viewmembers
function get_classNameImgViewmembers($name)
{
	global $eqdkp;
	
	$class = get_classNamebyMemberName($name);
	$_return = $name ;
	
	switch ($class)
	{
		case "Druid"        : $_return = "<a class=Druid  href='".$eqdkp->config['server_path']."viewmember.php?s=&name=".$name."'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Druid.gif' alt=''>&nbsp;".$name."</a>";break;
		case "Warlock"      : $_return = "<a class=Warlock href='".$eqdkp->config['server_path']."viewmember.php?s=&name=".$name."'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Warlock.gif' alt=''>&nbsp;".$name."</a>";break;
		case "Hunter"       : $_return = "<a class=Hunter  href='".$eqdkp->config['server_path']."viewmember.php?s=&name=".$name."'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Hunter.gif' alt=''>&nbsp;".$name."</a>";break;
		case "Warrior"      : $_return = "<a class=Warrior href='".$eqdkp->config['server_path']."viewmember.php?s=&name=".$name."'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Warrior.gif' alt=''>&nbsp;".$name."</a>";break;
		case "Mage"         : $_return = "<a class=Mage  href='".$eqdkp->config['server_path']."viewmember.php?s=&name=".$name."'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Mage.gif' alt=''>&nbsp;".$name."</a>";break;
		case "Paladin"      : $_return = "<a class=Paladin href='".$eqdkp->config['server_path']."viewmember.php?s=&name=".$name."'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Paladin.gif' alt=''>&nbsp;".$name."</a>";break;
		case "Priest"       : $_return = "<a class=Priest  href='".$eqdkp->config['server_path']."viewmember.php?s=&name=".$name."'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Priest.gif' alt=''>&nbsp;".$name."</a>";break;
		case "Rogue"        : $_return = "<a class=Rogue  href='".$eqdkp->config['server_path']."viewmember.php?s=&name=".$name."'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Rogue.gif' alt=''>&nbsp;".$name."</a>";break;
		case "Shaman"       : $_return = "<a class=Shaman  href='".$eqdkp->config['server_path']."viewmember.php?s=&name=".$name."'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Shaman.gif' alt=''> ".$name."</a>";break;
		
		case "Druide"       : $_return = "<a class=Druid 		href='".$eqdkp->config['server_path']."viewmember.php?s=&name=".$name."'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Druid.gif' alt=''>&nbsp;".$name."</a>";break;
		case "Hexenmeister" : $_return = "<a class=Warlock 	href='".$eqdkp->config['server_path']."viewmember.php?s=&name=".$name."'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Warlock.gif' alt=''>&nbsp;".$name."</a>";break;
		case "Jäger"        : $_return = "<a class=Hunter 	href='".$eqdkp->config['server_path']."viewmember.php?s=&name=".$name."'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Hunter.gif' alt=''>&nbsp;".$name."</a>";break;
		case "Krieger"      : $_return = "<a class=Warrior 	href='".$eqdkp->config['server_path']."viewmember.php?s=&name=".$name."'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Warrior.gif' alt=''>&nbsp;".$name."</a>";break;
		case "Magier"       : $_return = "<a class=Mage 		href='".$eqdkp->config['server_path']."viewmember.php?s=&name=".$name."'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Mage.gif' alt=''>&nbsp;".$name."</a>";break;
		case "Paladin"      : $_return = "<a class=Paladin 	href='".$eqdkp->config['server_path']."viewmember.php?s=&name=".$name."'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Paladin.gif' alt=''>&nbsp;".$name."</a>";break;
		case "Priester"     : $_return = "<a class=Priest 	href='".$eqdkp->config['server_path']."viewmember.php?s=&name=".$name."'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Priest.gif' alt=''>&nbsp;".$name."</a>";break;
		case "Schurke"      : $_return = "<a class=Rogue 		href='".$eqdkp->config['server_path']."viewmember.php?s=&name=".$name."'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Rogue.gif' alt=''>&nbsp;".$name."</a>";break;
		case "Schamane"     : $_return = "<a class=Shaman 	href='".$eqdkp->config['server_path']."viewmember.php?s=&name=".$name."'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Shaman.gif' alt=''>".$name."</a>";break;
		
	 }
return($_return) ;
}

// return only name in class colur
function get_coloredLinkedName($name)
{
	global $eqdkp;
	
	$class = get_classNamebyMemberName($name);
	$_return = $name ;
	
	switch ($class)
	{
		case "Druid"        : $_return = "<a class=Druid  href='".$eqdkp->config['server_path']."viewmember.php?s=&name=".$name."'>&nbsp;".$name."</a>";break;
		case "Warlock"      : $_return = "<a class=Warlock href='".$eqdkp->config['server_path']."viewmember.php?s=&name=".$name."'>&nbsp;".$name."</a>";break;
		case "Hunter"       : $_return = "<a class=Hunter  href='".$eqdkp->config['server_path']."viewmember.php?s=&name=".$name."'>&nbsp;".$name."</a>";break;
		case "Warrior"      : $_return = "<a class=Warrior href='".$eqdkp->config['server_path']."viewmember.php?s=&name=".$name."'>&nbsp;".$name."</a>";break;
		case "Mage"         : $_return = "<a class=Mage  href='".$eqdkp->config['server_path']."viewmember.php?s=&name=".$name."'>&nbsp;".$name."</a>";break;
		case "Paladin"      : $_return = "<a class=Paladin href='".$eqdkp->config['server_path']."viewmember.php?s=&name=".$name."'>&nbsp;".$name."</a>";break;
		case "Priest"       : $_return = "<a class=Priest  href='".$eqdkp->config['server_path']."viewmember.php?s=&name=".$name."'>&nbsp;".$name."</a>";break;
		case "Rogue"        : $_return = "<a class=Rogue  href='".$eqdkp->config['server_path']."viewmember.php?s=&name=".$name."'>&nbsp;".$name."</a>";break;
		case "Shaman"       : $_return = "<a class=Shaman  href='".$eqdkp->config['server_path']."viewmember.php?s=&name=".$name."'>&nbsp;".$name."</a>";break;
		
		case "Druide"       : $_return = "<a class=Druid  href='".$eqdkp->config['server_path']."viewmember.php?s=&name=".$name."'>&nbsp;".$name."</a>";break;
		case "Hexenmeister" : $_return = "<a class=Warlock href='".$eqdkp->config['server_path']."viewmember.php?s=&name=".$name."'>&nbsp;".$name."</a>";break;
		case "Jäger"        : $_return = "<a class=Hunter  href='".$eqdkp->config['server_path']."viewmember.php?s=&name=".$name."'>&nbsp;".$name."</a>";break;
		case "Krieger"      : $_return = "<a class=Warrior href='".$eqdkp->config['server_path']."viewmember.php?s=&name=".$name."'>&nbsp;".$name."</a>";break;
		case "Magier"       : $_return = "<a class=Mage  href='".$eqdkp->config['server_path']."viewmember.php?s=&name=".$name."'>&nbsp;".$name."</a>";break;
		case "Paladin"      : $_return = "<a class=Paladin href='".$eqdkp->config['server_path']."viewmember.php?s=&name=".$name."'>&nbsp;".$name."</a>";break;
		case "Priester"     : $_return = "<a class=Priest  href='".$eqdkp->config['server_path']."viewmember.php?s=&name=".$name."'>&nbsp;".$name."</a>";break;
		case "Schurke"      : $_return = "<a class=Rogue  href='".$eqdkp->config['server_path']."viewmember.php?s=&name=".$name."'>&nbsp;".$name."</a>";break;		
		case "Schamane"     : $_return = "<a class=Shaman  href='".$eqdkp->config['server_path']."viewmember.php?s=&name=".$name."'>&nbsp;".$name."</a>";break;	
	 }
return($_return) ;
}

function get_classImgListmembers($class)
{

global $eqdkp;
	$_return = $class ;
	switch ($class)
	{
		case "Druid"        : $_return = "<a class=Druid  href='".$eqdkp->config['server_path']."listmembers.php?s=&filter=Druid'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Druid.gif' alt=''></a>";break;
		case "Warlock"      : $_return = "<a class=Warlock href='".$eqdkp->config['server_path']."listmembers.php?s=&filter=Warlock'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Warlock.gif' alt=''></a>";break;
		case "Hunter"       : $_return = "<a class=Hunter  href='".$eqdkp->config['server_path']."listmembers.php?s=&filter=Hunter'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Hunter.gif' alt=''></a>";break;
		case "Warrior"      : $_return = "<a class=Warrior href='".$eqdkp->config['server_path']."listmembers.php?s=&filter=Warrior'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Warrior.gif' alt=''></a>";break;
		case "Mage"         : $_return = "<a class=Mage  href='".$eqdkp->config['server_path']."listmembers.php?s=&filter=Mage'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Mage.gif' alt=''></a>";break;
		case "Paladin"      : $_return = "<a class=Paladin href='".$eqdkp->config['server_path']."listmembers.php?s=&filter=Paladin'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Paladin.gif' alt=''></a>";break;
		case "Priest"       : $_return = "<a class=Priest  href='".$eqdkp->config['server_path']."listmembers.php?s=&filter=Priest'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Priest.gif' alt=''></a>";break;
		case "Rogue"        : $_return = "<a class=Rogue  href='".$eqdkp->config['server_path']."listmembers.php?s=&filter=Rogue'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Rogue.gif' alt=''></a>";break;
		case "Shaman"       : $_return = "<a class=Shaman  href='".$eqdkp->config['server_path']."listmembers.php?s=&filter=Shaman'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Shaman.gif' alt=''></a>";break;
		case "Default"      : $_return = "<img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Unknown.gif' alt=''>&nbsp;Unknown";break;

		case "Druide"       : $_return = "<a class=Druid  href='".$eqdkp->config['server_path']."listmembers.php?s=&filter=Druide'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Druid.gif' alt=''></a>";break;
		case "Hexenmeister" : $_return = "<a class=Warlock href='".$eqdkp->config['server_path']."listmembers.php?s=&filter=Hexenmeister'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Warlock.gif' alt=''></a>";break;
		case "Jäger"        : $_return = "<a class=Hunter  href='".$eqdkp->config['server_path']."listmembers.php?s=&filter=Jäger'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Hunter.gif' alt=''></a>";break;
		case "Krieger"      : $_return = "<a class=Warrior href='".$eqdkp->config['server_path']."listmembers.php?s=&filter=Krieger'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Warrior.gif' alt=''></a>";break;
		case "Magier"       : $_return = "<a class=Mage  href='".$eqdkp->config['server_path']."listmembers.php?s=&filter=Magier'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Mage.gif' alt=''></a>";break;
		case "Paladin"      : $_return = "<a class=Paladin href='".$eqdkp->config['server_path']."listmembers.php?s=&filter=Paladin'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Paladin.gif' alt=''></a>";break;
		case "Priester"     : $_return = "<a class=Priest  href='".$eqdkp->config['server_path']."listmembers.php?s=&filter=Priester'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Priest.gif' alt=''></a>";break;
		case "Schurke"      : $_return = "<a class=Rogue  href='".$eqdkp->config['server_path']."listmembers.php?s=&filter=Schurke'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Rogue.gif' alt=''></a>";break;
		case "Schamane"     : $_return = "<a class=Shaman  href='".$eqdkp->config['server_path']."listmembers.php?s=&filter=Shaman'><img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Shaman.gif' alt=''></a>";break;
		case "Unknown"      : $_return = "<img width=18 height=18 src='".$eqdkp->config['server_path']."./images/class/Unknown.gif' alt=''>&nbsp;Unknown";break;
	 }
return($_return) ;
}

function renameClasstoenglish($class)
{
global $eqdkp;
	$_return = $class ;
	switch ($class)
	{
		case "Druide"       : $_return = "Druid";break;
		case "Hexenmeister" : $_return = "Warlock";break;
		case "Jäger"        : $_return = "Hunter";break;
		case "Krieger"      : $_return = "Warrior";break;
		case "Magier"       : $_return = "Mage";break;
		case "Paladin"      : $_return = "Paladin";break;
		case "Priester"     : $_return = "Priest";break;
		case "Schurke"      : $_return = "Rogue";break;
		case "Schamane"     : $_return = "Shaman";break;
		case "Unknown"      : $_return = "Default";break;

		case "Druid"        : $_return = "Druid";break;
		case "Warlock"    	: $_return = "Warlock";break;
		case "Hunter"       : $_return = "Hunter";break;
		case "Warrior"      : $_return = "Warrior";break;
		case "Mage"         : $_return = "Mage";break;
		case "Paladin"      : $_return = "Paladin";break;
		case "Priest"       : $_return = "Priest";break;
		case "Rogue"        : $_return = "Rogue";break;
		case "Shaman"       : $_return = "Shaman";break;
		case "Default"   	  : $_return = "Default";break;
		
	 }
return($_return) ;
}

// return the Class Color Code
function getClassHtmlColorLinkCode($classname)
{

	$_return = '';
	switch ($classname)
	{
		case "Druid"        : $_return = " class=Druid  '";break;
		case "Warlock"      : $_return = " class=Warlock '";break;
		case "Hunter"       : $_return = " class=Hunter  '";break;
		case "Warrior"      : $_return = " class=Warrior '";break;
		case "Mage"         : $_return = " class=Mage  '";break;
		case "Paladin"      : $_return = " class=Paladin '";break;
		case "Priest"       : $_return = " class=Priest  '";break;
		case "Rogue"        : $_return = " class=Rogue  '";break;
		case "Shaman"       : $_return = " class=Shaman  '";break;

		case "Druide"       : $_return = " class=Druid  '";break;
		case "Hexenmeister" : $_return = " class=Warlock '";break;
		case "Jäger"        : $_return = " class=Hunter  '";break;
		case "Krieger"      : $_return = " class=Warrior '";break;
		case "Magier"       : $_return = " class=Mage  '";break;
		case "Paladin"      : $_return = " class=Paladin '";break;
		case "Priester"     : $_return = " class=Priest  '";break;
		case "Schurke"      : $_return = " class=Rogue  '";break;
		case "Schamane"     : $_return = " class=Shaman  '";break;
	 }
return($_return) ;
}

// return only the Class image
function getClassImg($classname)
{
	global $eqdkp ;
	$_return = '' ;
	
	switch ($classname)
	{
		case "Druid"        : $_return = "<img width=18 height=18 src='".$eqdkp->config['server_path']."/images/class/Druid.gif' alt=''>";break;
		case "Warlock"    	: $_return = "<img width=18 height=18 src='".$eqdkp->config['server_path']."/images/class/Warlock.gif' alt=''>";break;
		case "Hunter"       : $_return = "<img width=18 height=18 src='".$eqdkp->config['server_path']."/images/class/Hunter.gif' alt=''>";break;
		case "Warrior"      : $_return = "<img width=18 height=18 src='".$eqdkp->config['server_path']."/images/class/Warrior.gif' alt=''>";break;
		case "Mage"         : $_return = "<img width=18 height=18 src='".$eqdkp->config['server_path']."/images/class/Mage.gif' alt=''>";break;
		case "Paladin"      : $_return = "<img width=18 height=18 src='".$eqdkp->config['server_path']."/images/class/Paladin.gif' alt=''>";break;
		case "Priest"       : $_return = "<img width=18 height=18 src='".$eqdkp->config['server_path']."/images/class/Priest.gif' alt=''>";break;
		case "Rogue"        : $_return = "<img width=18 height=18 src='".$eqdkp->config['server_path']."/images/class/Rogue.gif' alt=''>";break;
		case "Shaman"       : $_return = "<img width=18 height=18 src='".$eqdkp->config['server_path']."/images/class/Shaman.gif' alt=''>";break;
		case "Default"      : $_return = "<img width=18 height=18 src='".$eqdkp->config['server_path']."/images/class/Unknown.gif' alt=''>";break;

		case "Druide"       : $_return = "<img width=18 height=18 src='".$eqdkp->config['server_path']."/images/class/Druid.gif' alt=''>";break;
		case "Hexenmeister" : $_return = "<img width=18 height=18 src='".$eqdkp->config['server_path']."/images/class/Warlock.gif' alt=''>";break;
		case "Jäger"        : $_return = "<img width=18 height=18 src='".$eqdkp->config['server_path']."/images/class/Hunter.gif' alt=''>";break;
		case "Krieger"      : $_return = "<img width=18 height=18 src='".$eqdkp->config['server_path']."/images/class/Warrior.gif' alt=''>";break;
		case "Magier"       : $_return = "<img width=18 height=18 src='".$eqdkp->config['server_path']."/images/class/Mage.gif' alt=''>";break;
		case "Paladin"      : $_return = "<img width=18 height=18 src='".$eqdkp->config['server_path']."/images/class/Paladin.gif' alt=''>";break;
		case "Priester"     : $_return = "<img width=18 height=18 src='".$eqdkp->config['server_path']."/images/class/Priest.gif' alt=''>";break;
		case "Schurke"      : $_return = "<img width=18 height=18 src='".$eqdkp->config['server_path']."/images/class/Rogue.gif' alt=''>";break;
		case "Schamane"     : $_return = "<img width=18 height=18 src='".$eqdkp->config['server_path']."/images/class/Shaman.gif' alt=''>";break;
		case "Unknown"      : $_return = "<img width=18 height=18 src='".$eqdkp->config['server_path']."/images/class/Unknown.gif' alt=''>";break;
	 }
return($_return) ;
}

?>