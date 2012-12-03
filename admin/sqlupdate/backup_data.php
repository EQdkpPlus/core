<?php

if ( !defined('EQDKP_INC') )
{
     die('Do not access this file directly.');
}


        global $db, $eqdkp, $user;



$a_system['newsloot']['name'] = "Newsloot" ;
$a_system['newsloot']['version'] = "0.3" ;
$a_system['newsloot']['detail'] = "Table column" ;

$a_system['pk_config']['name'] = "Plus Kernel" ;
$a_system['pk_config']['version'] = "0.4" ;
$a_system['pk_config']['detail'] = "Config Table" ;

$a_system['pk_links']['name'] = "Plus Kernel Links" ;
$a_system['pk_links']['version'] = "0.4" ;
$a_system['pk_links']['detail'] = "Links Table" ;

$a_system['pk_update']['name'] = "Plus Kernel Update" ;
$a_system['pk_update']['version'] = "0.4" ;
$a_system['pk_update']['detail'] = "Update Table" ;

$a_system['multidkp']['name'] = "MultiDKP" ;
$a_system['multidkp']['version'] = "0.4" ;
$a_system['multidkp']['detail'] = "MultiDKP Table" ;

$a_system['multidkp_events']['name'] = "MultiDKP" ;
$a_system['multidkp_events']['version'] = "0.4" ;
$a_system['multidkp_events']['detail'] = "MultiDKP2Events Table" ;

$a_system['adjustment']['name'] = "MultiDKP" ;
$a_system['adjustment']['version'] = "0.4" ;
$a_system['adjustment']['detail'] = "Adjustment column" ;

$a_system['event_icon']['name'] = "EventIcon" ;
$a_system['event_icon']['version'] = "0.4" ;
$a_system['event_icon']['detail'] = "Icon column" ;

$a_system['item_id']['name'] = "ItemID" ;
$a_system['item_id']['version'] = "0.4" ;
$a_system['item_id']['detail'] = "Item ID" ;

$a_system['classcolor_style']['name'] = "ClassColor" ;
$a_system['classcolor_style']['version'] = "0.41" ;
$a_system['classcolor_style']['detail'] = "Class Color" ;

$a_system['multidpk_fix']['name'] = "MultiDKP_Fix" ;
$a_system['multidpk_fix']['version'] = "0.4.4.2" ;
$a_system['multidpk_fix']['detail'] = "MultiDKP Database Fix" ;

#newsloot spalte
$sql = 'show columns
        FROM ' . NEWS_TABLE . "
        like 'showRaids_id'";
$result = $db->query($sql);
if (mysql_num_rows($result) > 0)
	{$a_system['newsloot']['state'] = 1;}
else{$a_system['newsloot']['state'] = 0;}

				#Plus Kernel Config
$sql = "SHOW TABLE STATUS LIKE '". PLUS_CONFIG_TABLE ."'";
$result = $db->query($sql);
if (mysql_num_rows($result) > 0)
	{$a_system['pk_config']['state'] = 1;}
else
{$a_system['pk_config']['state'] = 0;}

#Plus Kernel Links
$sql = "SHOW TABLE STATUS LIKE '". PLUS_LINKS_TABLE ."'";
$result = $db->query($sql);
if (mysql_num_rows($result) > 0)
	{$a_system['pk_links']['state'] = 1;}
else
{$a_system['pk_links']['state'] = 0;}

#Plus Kernel Update
$sql = "SHOW TABLE STATUS LIKE '". PLUS_UPDATE_TABLE ."'";
$result = $db->query($sql);
if (mysql_num_rows($result) > 0)
	{$a_system['pk_update']['state'] = 1;}
else
{$a_system['pk_update']['state'] = 0;}

#MULTIDKP_TABLE
$sql = "SHOW TABLE STATUS LIKE '". MULTIDKP_TABLE ."'";
$result = $db->query($sql);
if (mysql_num_rows($result) > 0)
	{$a_system['multidkp']['state'] = 1;}
else
{$a_system['multidkp']['state'] = 0;}

#MULTIDKP2EVENTS_TABLE
$sql = "SHOW TABLE STATUS LIKE '". MULTIDKP2EVENTS_TABLE ."'";
$result = $db->query($sql);
if (mysql_num_rows($result) > 0)
	{$a_system['multidkp_events']['state'] = 1;}
else
{$a_system['multidkp_events']['state'] = 0;}

#adjusment spalte
$sql = 'show columns
        FROM ' . ADJUSTMENTS_TABLE . "
        like 'raid_name'";
$result = $db->query($sql);
if (mysql_num_rows($result) > 0)
	{$a_system['adjustment']['state'] = 1;}
else{$a_system['adjustment']['state'] = 0;}

#Event Icon Spalte
$sql = 'show columns
        FROM ' . EVENTS_TABLE . "
        like 'event_icon'";
$result = $db->query($sql);
if (mysql_num_rows($result) > 0)
	{$a_system['event_icon']['state'] = 1;}
else{$a_system['event_icon']['state'] = 0;}

#Game ItemID spalte
$sql = 'show columns
        FROM ' . ITEMS_TABLE . "
        like 'game_itemid'";
$result = $db->query($sql);
if (mysql_num_rows($result) > 0)
	{$a_system['item_id']['state'] = 1;}
else{$a_system['item_id']['state'] = 0;}

#classcolor_style
$sql = 'show columns
        FROM ' . STYLES_TABLE . "
        like 'classfontcolor_Warrior'";
$result = $db->query($sql);
if (mysql_num_rows($result) > 0)
	{$a_system['classcolor_style']['state'] = 1;}
else{$a_system['classcolor_style']['state'] = 0;}

$sql = "describe ".MULTIDKP_TABLE;
$result = $db->query($sql);
while ($row = $db->fetch_record($result) )
{
	if (($row[0] == "multidkp_id") and  ($row[1] == "tinyint(3) unsigned"))
	{$a_system['multidpk_fix']['state'] = 0 ;}
	else {$a_system['multidpk_fix']['state'] = 1 ;}
}

$sql = "describe ".MULTIDKP2EVENTS_TABLE;
$result = $db->query($sql);
while ($row = $db->fetch_record($result) )
{
	if (($row[0] == "multidkp2event_id") and  ($row[1] == "tinyint(5)"))
	{$a_system['multidpk_fix']['state'] = 0 ;}
	else {$a_system['multidpk_fix']['state'] = 1 ;}
}


##########

$a_styles['wow_Vert']['version'] = '0.3';
$a_styles['wow_style']['version'] = '0.3';
$a_styles['wow_style_Vert']['version']= '0.3';
$a_styles['WoWMoonclaw01']['version'] = '0.3';
$a_styles['WoWMoonclaw01_Vert']['version'] = '0.3';
$a_styles['WoWMaevahEmpire']['version'] = '0.3';
$a_styles['WoWMaevahEmpire_Vert']['version'] = '0.3';
$a_styles['dkpUA_Vert']['version'] = '0.3';
$a_styles['EQCPS_Vert']['version'] = '0.3';
$a_styles['Collab_Vert']['version'] = '0.3';
$a_styles['Blueish_Vert']['version'] = '0.3';
$a_styles['Penguin_Vert']['version'] = '0.3';
$a_styles['Default_Vert']['version'] = '0.3';
$a_styles['EQdkp VB2_Vert']['version'] = '0.3';
$a_styles['subSilver_Vert']['version'] = '0.3';
$a_styles['EQdkp VB2_Vert']['version'] = '0.3';
$a_styles['Old_School_Vert']['version'] = '0.3';
$a_styles['EQdkp Items_Vert']['version'] = '0.3';
$a_styles['aallix Silver_Vert']['version'] = '0.3';
$a_styles['EQdkp Invision_Vert']['version'] = '0.3';
#$a_styles['wow3theme']['version'] = '0.4' ;

foreach($a_styles as $key => $value)
{ $template_name = '';
	$sql = 'select template_path
	        FROM ' . STYLES_TABLE . "
  				where style_name like'%".$key."'";
  $result = $db->query($sql);

	if (mysql_num_rows($result) > 0)
	{
			$a_styles[$key]['state'] = 1;
			$template_name = $db->query_first($sql);
			$template_file = '../templates/'.$template_name.'/page_header.html';
			if(file_exists($template_file))
			{
				$a_styles[$key]['filestate']=1;
			}
			else
			{
				$a_styles[$key]['filestate']=0;
			}
	}
	else
	{
		$a_styles[$key]['state'] = 0;
		$template_file = str_replace('_Vert','V','../templates/'.$key.'/page_header.html');
		if(file_exists($template_file))
			{$a_styles[$key]['filestate']=1;}
			else
			{$a_styles[$key]['filestate']=2;}
	}

}# end foreach styles


?>
