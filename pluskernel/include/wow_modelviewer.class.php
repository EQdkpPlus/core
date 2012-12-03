<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       13.06.2007
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2006-2008 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev$
 * -----------------------------------------------------------------------
 * Thanxs to Painis for the help |visit:  http://www.extinction.cc/
 * Thanxs to Wallenium for the armory import class
 *
 * This file can only be used on www.allvatar.com and in the EQDKP Plus!!
 * If you want to use this class in your project, plz send a mail to me
 * corgan@eqdkp-plus.com
 * -----------------------------------------------------------------------
 * $Id$
 */

if ( !defined('EQDKP_INC') )
{
    header('HTTP/1.0 404 Not Found');exit;
}

/**

 */
class wow_modelviewer
{
	
	var	$hostURLS = array(0=> "http://static.wowhead.com/modelviewer/", 1=> "http://i.thottbot.com/f/");
	var	$hostFilesS = array(0=> "ModelView.swf", 1=> "modelviewer.swf?3");
	var $flashSizeS = array(0=> 'width="350" height="450"', 1=> 'width="350" height="250"' );

	
	var	$host 	 	  = "http://static.wowhead.com/modelviewer/";
	var	$hostefile 	  = "ModelView.swf";

	
	var $wowhead_weaponIDs 			= array(-1,13,14,15,17,21,22,23,25);	
	var $wowhead_itemIDs 			= array(-1,1,3,4,5,6,7,8,9,10,16,19,20);	
	var $wowhead_ignoreIDs 			= array(-1,0,2,11,12,18,24,27,28,29,30);	

	var $armory_SlotsIDs 			= array(-2,-1,0,2,3,4,5,6,7,8,9,14,15,16,18);	
	var $armory_ignoreSlotIDs 		= array(-2,-1,1,10,11,12,13,17);	
	var $armory_ignoreSlotIDs_bow 	= array(-2,-1,1,10,11,12,13,15,16);	

	var $race 						= array('dummy','human','orc','dwarf','nightelf','scourge','tauren','gnome','troll','dummy','bloodelf','draenei') ;
	var $gender 					= array('male','female');
	var $wowhead_modelIDs 			= array(4,8,32,128,1,1024,16,2,64);
	
	var $textSlots					= array(0=>'helm',1=>'hals',2=>'schultern',3=>'hemd',4=>'brust',5=>'gürtel',6=>'hose',7=>'schuhe',8=>'armsch',
											9=>'handsch',10=>'ring1',11=>'ring2',12=>'trinket1',13=>'trinket2' ,14=>'umhang',15=>'mainhand',16=>'offhand / schild',
											17=>'relikt / bogen',18=>'tabard');

	/**
	 * Constructor
	 *
	 * @return wow_modelviewer
	 */
	function wow_modelviewer()	
	{
	
	}
	
	
	/**
	 * return the 3D Model
	 * $member_xml contains the saved XML data
	 * if no xml data found in the database we try to catch them from the armory
	 *
	 * @param string $member_name
	 * @param base64_encode(gzcompress(serialize(blob))) $member_xml
	 * @return string
	 */
	function wow_charviewer($member_name, $member_xml, $options)	
	{		
		$ret_val = false;
		global $conf_plus,$eqdkp_root_path, $db ;
		include $eqdkp_root_path.'/pluskernel/include/armory.class.details.php';
		include $eqdkp_root_path.'/pluskernel/include/wowhead.class.items.php';
				
		$member_xml=  unserialize(gzuncompress(base64_decode($member_xml)));
		$member_xml['xml'] = simplexml_load_string($member_xml['xml']);				
		$xml = $member_xml['xml'];		
		$this->host = $this->hostURLS[intval($options['flash'])];
		$this->hostefile = $this->hostFilesS[intval($options['flash'])];
		$this->flashSize = $this->flashSizeS[intval($options['flash'])];
		

		$updatetime = $member_xml['timestamp'] + (24*60*60);		
		//update once a day
		

		if( ($member_xml['xml'] == false) or ( time() > $updatetime ) )
		{
			$armory = new ArmoryCharLoader("Ã¢Ã®Ã¢");					
			$armory_url = $armory->GetArmoryData($member_name,$conf_plus['pk_servername'] ,$conf_plus['pk_server_region'], 'de');	

			
			if (isset($armory_url) and ($armory_url <> false)) 
			{		
				$xml = simplexml_load_string($armory_url);	
			}
			
			if (is_object($xml)) 
			{
				$saveXML['timestamp']= time();
				$saveXML['xml']=$armory_url;	
				
				$sql = "UPDATE ".MEMBERS_TABLE. " SET member_xml='".base64_encode(gzcompress(serialize($saveXML)))."' WHERE member_name='".$member_name."'" ;				
				$db->query($sql);
			}
										
		}
		
		
		//Parse the Chrakter XML
		if (isset($xml) and ($xml <> false) ) 
		{						
			foreach ($xml->xpath('//characterInfo') as $characterInfo)
			{
				// Char Info
				$class_id=$characterInfo->character['classId'];
				$gender_id=$characterInfo->character['genderId'];
				$race_id=$characterInfo->character['raceId'];
			}				
			
			//parse though the Items
			foreach($xml->xpath('//item') as $item)
			{								
					if( ($class_id=="3")) // 3 = for hunter we dont show the main/offhand. we only display the bow
					{
						if(!array_search($item[slot],$this->armory_ignoreSlotIDs_bow))
						{
							if ($options['speedy'])  //speedydragon doesnt need the displayID from wowhead
							{
								$equip.=$item[id].":";
							}else 
							{
								$equip.=$this->get_wowhead_info($item[id],false,$item[slot]);	
							}							
						}
					}else 
					{
						if(!array_search($item[slot],$this->armory_ignoreSlotIDs))
						{
							if ($options['speedy'])  //speedydragon doesnt need the displayID from wowhead
							{
								$equip.=$item[id].":";								
								
							}else 
							{
								$equip.=$this->get_wowhead_info($item[id],false,$item[slot]);	
							}							
						}						
					}										
								
			} #end foreach
			
			if ($options['speedy']) 
			{				
				$ret_val = $this->speedydragonJava($race_id,$gender_id,substr($equip,0,strlen($equip)-1))	;	
			}else 
			{
				$ret_val = $this->create_char_output($race_id,$gender_id,$equip)	;						
			}

		}	#end if xml			
		

		return $ret_val;
	}# end function
	
	
	
	/**
	 * Return the displayID from wowhead/database wich we need to show up in the flash
	 * if plain is set, than only returns the displayId.  
	 * if plain is false the function returns the slotnumber and the displayID
	 * 
	 * @param integer $item_id blizzardID
	 * @param boolean $plain 
	 * @param integer $armorySlotID
	 * @return unknown
	 */
	function get_wowhead_info($item_id,$plain=false,$armorySlotID=-2)
	{
		global $db ;
		
		//lokk in the database if we allready have this items
		$sql = "SELECT * FROM ".ITEMID_TABLE." WHERE itemID_blizID=".$item_id;
		$item = $db->fetch_record($db->query($sql));
		
		//if we dont found the item in the database we have to ask wowhead for the displayID
		if(!($item['itemID_displayID'] > 0) and! ($item['itemID_wowheadSlotID'] > 0) ) 
		{
			$wowhead = new wowheadLoader("Ã¢Ã®Ã¢");
			$url = $wowhead->GetwowheadData($item_id);
			$xml = simplexml_load_string($url);
			
			if (is_object($xml)) 
			{
				foreach ($xml->xpath('//item') as $wow_head_item)
				{				
					$item['itemID_wowheadSlotID'] = $wow_head_item->inventorySlot['id'];
					$item['itemID_displayID'] = $wow_head_item->icon['displayId'];	
					
					$sql = "INSERT INTO ".ITEMID_TABLE." (itemID_blizID, itemID_displayID, itemID_armorySlotID ,itemID_wowheadSlotID)
												values  (".$item_id.",".$item['itemID_displayID'].",".$armorySlotID.",".$item['itemID_wowheadSlotID'].")";
					$db->query($sql);
							
				}	
			}				
		}
		
		//Do only if we have a item, wich can by displayed. ignore rings/trinkets
		if(!array_search($item['itemID_wowheadSlotID'],$this->wowhead_ignoreIDs)) 					  	
		{
			// Weapons only without chars			
			if ($plain) 
			{										
				$found_weapon = array_search($item['itemID_wowheadSlotID'],$this->wowhead_weaponIDs);
				//if found weaponslot, just return the weaponId
				if($found_weapon)  
				{
					return $item['itemID_displayID']; // show only weapon	
				}
				//fallback
				return $item['itemID_wowheadSlotID'].",".$item['itemID_displayID'].",";	
				
			}else // normal equipment with slotID
			{
				//fix the main/offhand 
				$wowhead_slot = $item['itemID_wowheadSlotID'];						
				switch ($armorySlotID)
				{							
					case 14: $wowhead_slot = 22; break;	
					case 15: $wowhead_slot = 21; break;								
					case 16: $wowhead_slot = 22; break;															
				}
				return $wowhead_slot.",".$item['itemID_displayID'].",";	
			}									
		}
	
	}#end function
	

	/**
	 * Help function. return the name of the race/gender from the arrays
	 *
	 * @param integer $race_id
	 * @param  integer $gender_id
	 * @return string
	 */
	function get_model($race_id, $gender_id)
	{
		return $this->race[intval($race_id)].$this->gender[intval($gender_id)];
	}
		
	/**
	 * Create the 3D Charakter Model
	 *
	 * @param integer $race_id
	 * @param integer $gender_id
	 * @param string $equip
	 * @return string
	 */
	function create_char_output($race_id,$gender_id,$equip)
	{
		$ret_val = '<object 
					id="head" '.$this->flashSize.'
					type="application/x-shockwave-flash" 
					data="'.$this->host.$this->hostefile.'">
					<param name="quality" value="high"/>
					<param name="allowscriptaccess" value="always"/>
					<param name="menu" value="false"/>				
					<param value="transparent" name="wmode">
					<param name="flashvars" value="model='.$this->get_model($race_id, $gender_id).'&modelType=16
					&contentPath='.$this->host.'&blur=1&equipList='.substr($equip, 0, -1).'"/>
					<param name="movie" value="'.$this->host.$this->hostefile.'">
					</object>
					<br>							
					';
			
		return $ret_val ;			
	}
	
	/**
	 * Shows the WoWhead/Thot Item Model in Flash
	 *
	 * @param Integer $itemID BlizzardItemID
	 * @return string 
	 */
	function wow_itemviewer($itemID)
	{
		global $conf_plus,$eqdkp_root_path ;
		include $eqdkp_root_path.'/pluskernel/include/armory.class.details.php';
		include $eqdkp_root_path.'/pluskernel/include/wowhead.class.items.php';
				 	
		$wowheadInfos = $this->get_wowhead_info($itemID,true) ;	 	
		$object = '';
			
		//must different. normale equip must show with an 3dchar
		if (strpos($wowheadInfos,',')) 
		{ 
			$modelType = '16' ;
			$object ='<object id="head" width="350" height="300" 
					  type="application/x-shockwave-flash" 
					  data="'.$this->host.$this->hostefile.'"
					  style="visibility: visible;">
				<param name="quality" value="high"/>
				<param name="allowscriptaccess" value="always"/>
				<param name="menu" value="false"/>
				<param value="transparent" name="wmode">
				<param name="flashvars" value="model=bloodelffemale&modelType='.$modelType.'&contentPath='.$this->host.'&equipList='.substr($wowheadInfos, 0, -1).'"/>
				<param name="movie" value="'.$this->host.$this->hostefile.'">
				</object>';
			 		
		}elseif (isset($wowheadInfos))  // display weapons stand alone
		{ 
			
			$object = '<object id="head" width="350" height="300" 
						type="application/x-shockwave-flash" 
						data="'.$this->host.$this->hostefile.'"
						style="visibility: visible;">
				<param name="quality" value="high"/>
				<param name="allowscriptaccess" value="always"/>
				<param name="menu" value="false"/>
				<param value="transparent" name="wmode">
				<param name="flashvars" value="model='.$wowheadInfos.'&modelType=1&contentPath='.$this->host.'"/>
				<param name="movie" value="'.$this->host.$this->hostefile.'">
				</object>'	;
			
		}			 
		
		return $object;		
	} #end funktion
	
	/**
	 * Returns a NPC
	 *
	 * @param integer $model
	 * @return string
	 */
	function wow_npcviewer($model)
	{
		#$model = 21135;
		#$ret_val = $wow_modelviewer->wow_npcviewer(21135);
		
		$object = '
				<object id="head" width="300" height="400" 
					type="application/x-shockwave-flash" 
					data="'.$this->host.$this->hostefile.'"
					style="visibility: visible;">
				<param name="quality" value="high"/>
				<param name="allowscriptaccess" value="always"/>
				<param name="menu" value="false"/>
				<param value="transparent" name="wmode">
				<param name="flashvars" value="model='.$model.'&modelType=8&contentPath='.$this->host.'"/>
				<param name="movie" value="'.$this->host.$this->hostefile.'">
				</object>
				';
		return $object ;
	}
	
	
	
	function wow_create_modelviewer($type,$model=false,$race_id=-1,$gender_id=-1,$equip=-1)
	{
		#$model = 21135;
		#$ret_val = $wow_modelviewer->wow_npcviewer(21135);
		
		$object = '
				<object id="head" width="300" height="400" 
					type="application/x-shockwave-flash" 
					data="'.$this->host.$this->hostefile.'"
					style="visibility: visible;">
				<param name="quality" value="high"/>
				<param name="allowscriptaccess" value="always"/>
				<param name="menu" value="false"/>
				<param value="transparent" name="wmode">
				<param name="flashvars" 
					value="model='.$model.'
					&
					modelType=8
					&
					contentPath='.$this->host.
				'"/>
				<param name="movie" value="'.$this->host.$this->hostefile.'">
				</object>
				';
		return $object ;
	}
	
	function speedydragonJava($race_id,$gender_id,$equip)
	{
		global $user ;
		$id = 1000000 + ($race_id*10)+$gender_id;		
		$object = '
					<APPLET id="modelviewer" height="300" archive="http://www.speedydragon.de/modelviewer/omv.jar,http://www.speedydragon.de/modelviewer/jpct.jar" width="600" code="firehead.omv.applet.OnlineModelViewer.class">
						<PARAM value="'.$user->style['tr_color1'].'" name="bgcolor" /> 
					    <PARAM value="dressroom" name="type" /> 
					    <PARAM value="'.$id.'" name="id" /> 
					    <PARAM value="'.$equip.'" name="items" /> 
					    <PARAM value="true" name="stripped" />
					</APPLET>		
				';
		return $object ;
	}
	
	
	
	
}

	#
	# WoWHead SlotIds 
	
	#EquipmentIDs
	# 1,3,4,5,6,7,8,9,10,16,19,20
	
	#WeapomIds
	# 13,14,15,17,21,22,23,25
	
	#ignoreIDs
	#2,11,12,18,24,16,27,28,29,30	
	
	# 1 = Kopf - head
	# 2 = Hals - neck
	# 3 = Schultern - shoulder
	# 4 = Hemd - 
	# 5 = Brust - chest
	# 6 = Taille - 
	# 7 = Hose - legs
	# 8 = schuhe - foot
	# 9 = Handgelenke - wraist
	# 10 = Hände - hand
	# 11 = Ringe - ring
	# 12 = Schmuck - trinket
	# 13 = Einhand - Dolch, Axt, Schwert (1h) - onehand
	# 14 = Schild - shild
	# 15 = Distanz	(Armbrust, Bogen, Zauberstab) - range
	# 16 = Rücken (Umhang) - back
	# 17 = Zweihändig (Stab, Stange, 2h Schwerte, angel) - twohand
	# 18 = 
	# 19 = Wappenrock - tabbard
	# 20 = robe - robe
	# 21 = rechte hand (Kriegsgleve von Azzinoth) (1h) - right hand
	# 22 = Fastwaffe bze. linke hand (1h) - fist
	# 23 = Nebendhand-Schildhand  () - offhand
	# 24 = 
	# 25 = Geworfen (Wurfwaffe) - throw
	# 26 = 
	# 27 = 
	# 28 = Relikt
	# 29 = 
	# 30 = 
		
	###########################
	
	#armory SlotIds IDs
	
	#armoryItemSlot 
	#0,2,3,4,5,6,7,8,9,14,15,16,18
	
	#ignore Slots
	#1,10,11,12,13,17
	
	#ignore Slots by class=3
	#1,10,11,12,13,	
	
	/*
	"0": helm
	"1": hals
	"2": schultern
	"3": hemd
	"4": brust
	"5": gürtel
	"6": hose
	"7": schuhe
	"8": armsch.
	"9": handsch
	"10": ring1
	"11": ring2
	"12": trinket1
	"13": trinket2 
	"14": umhang
	"15": mainhand
	"16": offhand / schild				
	"17": relikt / bogen !
	"18": tabard			

	###########################
	
	wowhead model id
		
	SHOULDER: = 4;
	NPC: = 8;
	HUMAN: = 32;
	ARMOR: = 128;
	ITEM: = 1;
	ATTACH: = 1024;
	CHAR: = 16;
	HELM: = 2;
	OBJECT: = 64;

	
	
	*/	
?>