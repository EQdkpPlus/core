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

$a_system['multidpk_fix']['name'] = "MultiDKP_Fix" ;
$a_system['multidpk_fix']['version'] = "0.4.4.2" ;
$a_system['multidpk_fix']['detail'] = "MultiDKP Database Fix" ;

$a_system['mod_game']['name'] = "Mod_Game" ;
$a_system['mod_game']['version'] = "0.5.0.4" ;
$a_system['mod_game']['detail'] = "Modular Game Option" ;

$a_system['rss']['name'] = "RSS" ;
$a_system['rss']['version'] = "0.5.0.5" ;
$a_system['rss']['detail'] = "RSS News Feed Reader" ;

$a_system['comments']['name'] = "Comments" ;
$a_system['comments']['version'] = "0.5.0.7" ;
$a_system['comments']['detail'] = "Comments System" ;

$a_system['advanced_news']['name'] = "Advanced_News" ;
$a_system['advanced_news']['version'] = "0.5.0.8" ;
$a_system['advanced_news']['detail'] = "Advanced News" ;

$a_system['511']['name'] = "511" ;
$a_system['511']['version'] = "0.5.1.1" ;
$a_system['511']['detail'] = "Plus Version 0.5.1.1" ;

$a_system['513']['name'] = "513" ;
$a_system['513']['version'] = "0.5.1.3" ;
$a_system['513']['detail'] = "Plus Version 0.5.1.3" ;

$a_system['classcolors']['name'] = "classcolor" ;
$a_system['classcolors']['version'] = "0.6.0.3" ;
$a_system['classcolors']['detail'] = "dynamic Class Color" ;

$a_system['modelviewer']['name'] = "modelviewer" ;
$a_system['modelviewer']['version'] = "0.6.0.4" ;
$a_system['modelviewer']['detail'] = "WoW 3D Modelviewer" ;

$a_system['portal']['name'] = "portal" ;
$a_system['portal']['version'] = "0.6.0.4" ;
$a_system['portal']['detail'] = "Portal Management" ;

$a_system['615']['name'] = "615" ;
$a_system['615']['version'] = "0.6.1.5" ;
$a_system['615']['detail'] = "Plus Version 0.6.1.5" ;

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

$sql = "describe ".MULTIDKP_TABLE;
$result = $db->query($sql);
$a_system['multidpk_fix']['state'] = 1;
while ($row = $db->fetch_record($result) )
{
	if (($row[0] == "multidkp_id") and  (!$row[1] == "int(11)"))
	{$a_system['multidpk_fix']['state'] = 0 ;}

	if (($row[0] == "multidkp_id") and  (!$row[5] == "auto_increment"))
	{$a_system['multidpk_fix']['state'] = 0 ;}
}

$sql = "describe ".MULTIDKP2EVENTS_TABLE;
$result = $db->query($sql);
while ($row = $db->fetch_record($result) )
{
	if (($row[0] == "multidkp2event_id") and  (!$row[1] == "int(11)"))
	{$a_system['multidpk_fix']['state'] = 0 ;}

	if (($row[0] == "multidkp2event_multi_id") and (!$row[1] == "int(11)"))
	{$a_system['multidpk_fix']['state'] = 0 ;}

	if (($row[0] == "multidkp2event_id") and  (!$row[5] == "auto_increment"))
	{$a_system['multidpk_fix']['state'] = 0 ;}
}


#Mod_game
$sql = 'select * FROM ' . CONFIG_TABLE . " where config_name like'%game_language'";
$result = $db->query($sql);
if (mysql_num_rows($result) > 0)
	{$a_system['mod_game']['state'] = 1;}
else{$a_system['mod_game']['state'] = 0;}


#RSS_TABLE
$sql = "SHOW TABLE STATUS LIKE '". PLUS_RSS_TABLE ."'";
$result = $db->query($sql);
if (mysql_num_rows($result) > 0)
	{$a_system['rss']['state'] = 1;}
else
{$a_system['rss']['state'] = 0;}


#Comments
$sql = "SHOW TABLE STATUS LIKE '__comments'";
$result = $db->query($sql);
if (mysql_num_rows($result) > 0)
	{$a_system['comments']['state'] = 1;}
else
{$a_system['comments']['state'] = 0;}

$sql = "describe __comments";
$result = $db->query($sql);
while ($row = $db->fetch_record($result) )
{
	if (($row['Field'] == "attach_id") and  ($row['Type'] == "int(11) unsigned"))
	{$a_system['comments']['state'] = 0 ;}
}


#Advanced_News
$sql = 'show columns
        FROM ' . NEWS_TABLE . "
        like 'news_permissions'";
$result = $db->query($sql);
if (mysql_num_rows($result) > 0)
	{$a_system['advanced_news']['state'] = 1;}
else{$a_system['advanced_news']['state'] = 0;}

$a_system['511']['state'] = 0 ;
$sql = "SELECT config_value from ". CONFIG_TABLE ."
        WHERE config_name = 'plus_version'" ;
$result = $db->query($sql);
while ($row = $db->fetch_record($result) )
{
	$v1 = intval(str_replace('.','',$row['config_value']));
	$v2 = intval(str_replace('.','','0.5.1.1'));

	if ($v1 >= $v2)
	{
		$a_system['511']['state'] = 1 ;
	}
}

$a_system['513']['state'] = 0 ;
$sql = "SELECT config_value from ". CONFIG_TABLE ."
        WHERE config_name = 'plus_version'" ;
$result = $db->query($sql);
while ($row = $db->fetch_record($result) )
{
	$v1 = intval(str_replace('.','',$row['config_value']));
	$v2 = intval(str_replace('.','','0.5.1.3'));

	if ($v1 >= $v2)
	{
		$a_system['513']['state'] = 1 ;
	}
}


$sql = "SHOW TABLE STATUS LIKE '". CLASSCOLOR_TABLE ."'";
$result = $db->query($sql);
if (mysql_num_rows($result) > 0)
	{$a_system['classcolors']['state'] = 1;}
else
{$a_system['classcolors']['state'] = 0;}

#Modelviewer
$sql = "SHOW TABLE STATUS LIKE '". ITEMID_TABLE ."'";
$result = $db->query($sql);
if (mysql_num_rows($result) > 0)
	{$a_system['modelviewer']['state'] = 1;}
else
{$a_system['modelviewer']['state'] = 0;}

#Portal
$sql = "SHOW TABLE STATUS LIKE '__portal'";
$result = $db->query($sql);
if (mysql_num_rows($result) > 0)
	{$a_system['portal']['state'] = 1;}
else
{$a_system['portal']['state'] = 0;}


$a_system['615']['state'] = 0 ;
$sql = "SELECT config_value from ". CONFIG_TABLE ."
        WHERE config_name = 'plus_version'" ;
$result = $db->query($sql);
while ($row = $db->fetch_record($result) )
{
	$v1 = intval(str_replace('.','',$row['config_value']));
	$v2 = intval(str_replace('.','','0.6.1.5'));

	if ($v1 >= $v2)
	{
		$a_system['615']['state'] = 1 ;
	}
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
$a_styles['m9wow3eq']['version'] = '0.5' ;
$a_styles['luna_wotlk']['version'] = '0.6' ;
$a_styles['m9wotlk']['version'] = '0.6' ;


foreach($a_styles as $key => $value)
{ 
	$template_name = '';
	$sql = 'select template_path  
	        FROM ' . STYLES_TABLE . "
  			where style_name like'%".$key."'";
  	$result = $db->query($sql);

	//Found template in DB
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
						
			if ($key == 'm9wow3eq') 
			{
				$sql = 'select logo_path  
	        			FROM ' . STYLES_CONFIG_TABLE . "
  						where style_id =35";	
				$logopath = $db->query_first($sql);	

				if ($logopath == '/logo/wow_logo.gif') 
				{
					$a_styles[$key]['state'] = 0;	
				}
				
			}
	}
	else // dont found template in DB, check the files
	{
		$a_styles[$key]['state'] = 0;
		$template_file = str_replace('_Vert','V','../templates/'.$key.'/page_header.html');
		if(file_exists($template_file))
		{
			$a_styles[$key]['filestate']=1;
		}
		else
		{
			$a_styles[$key]['filestate']=2;
		}
		
		
			
	}

}# end foreach styles


?>
