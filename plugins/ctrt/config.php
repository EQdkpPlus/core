<?php
/******************************
 * EQdkp CT_RaidTracker Import
 * Copyright 2005
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * config.php
 ******************************/

$ctrt_set_SplitRaidsbyBoss = false;									// add a raid for each boss. (if false, only one Raid will be insert)
																										// Splittet die Raid auf, so das für jeden Boss ein einzelner Raid eingetragen wird. #by Corgan

$ctrt_settings['MinItemQuality'] = 3;         			// Sets the minimum Item Quality of Items to get parsed (4 = Epic, 3 = Rare, 2 = uncommon)
$ctrt_settings['IgnoredLooter'] = "disenchanted";		// Set a "Looter" which should be ignored
$ctrt_settings['AddLootDkpValuesCheckbox'] = false;	// Here you can set the default status of the "Add Item value/attendees" Checkbox
$ctrt_settings['ConvertNames'] = false;							// Setting it to true will convert e.g. "Âvâtâr" to "Avatar".
$ctrt_settings['LootNoteEventTriggerCheck'] = false;// Will Check for Event Triggers in the Loot Notes (e.g. if you have events called "MC (Lucifron), MC (Magmadar), ..." and only want to log one raid.
$ctrt_settings['NewMemberDefaultRank'] = "";	 			// Here you can set the default Rank of new Members (e.g. Member).
$ctrt_settings['AttendanceFilter'] = 2;				 			// Sets the type of attendance filtering
																										// 0 = None ( if a person was in the raid they are added to all events )
																										// 1 = Loot Time ( if a person was in the raid when the loot attached to an event was picked up they are added to the event )
																										// 2 = Boss Kill Time ( if a person was in the raid when a boss attached to an event was killed they are addedto the event )
$ctrt_settings['SkipRaidsWithEmptyNote'] = true;		// This will skip any raids which have an empty Raid Note
$ctrt_settings['DefaultDKPCost'] = false;						// The cost of items with no dkp value
$ctrt_settings['StartingDKP'] = "0";								// When creating a new member, if this is > 0, it will add an adjustment to the member as starting dkp
$ctrt_settings['CreateStartRaid'] = false;					// Set this to ture to create a Starting Raid when using boss kill time.
$ctrt_settings['StartRaidDKP'] = "";								// Default starting Raid DKP.
		
// Here you can set a list of item IDs which should be ignored
#$ctrt_settings['IgnoreItems'][] = 18704;							// Mature Blue Dragon Sinew (Azuregos)
$ctrt_settings['IgnoreItems'][] = 19017;								// Essence of the Firelord (Ragnaros)
$ctrt_settings['IgnoreItems'][] = 18562;								// Elementium Ore (BwL)
$ctrt_settings['IgnoreItems'][] = 20383;								// Head of the Broodlord Lashlayer (BwL)
$ctrt_settings['IgnoreItems'][] = 20725;								// Nexus Crystal

// Here you can add item IDs which should be always added (even if the quality is below $ctrt_set_MinItemQuality)
#$ctrt_settings['AlwaysAddItems'][] = 17966;					  // Onyxia Hide Backpack

// Zul Gurub Coins
$ctrt_settings['AlwaysAddItems'][] = 19707;
$ctrt_settings['AlwaysAddItems'][] = 19708;
$ctrt_settings['AlwaysAddItems'][] = 19709;
$ctrt_settings['AlwaysAddItems'][] = 19710;
$ctrt_settings['AlwaysAddItems'][] = 19711;
$ctrt_settings['AlwaysAddItems'][] = 19712;
$ctrt_settings['AlwaysAddItems'][] = 19713;
$ctrt_settings['AlwaysAddItems'][] = 19714;
$ctrt_settings['AlwaysAddItems'][] = 19715;

// Zul Gurub Bijous
$ctrt_settings['AlwaysAddItems'][] = 19698;
$ctrt_settings['AlwaysAddItems'][] = 19699;
$ctrt_settings['AlwaysAddItems'][] = 19700;
$ctrt_settings['AlwaysAddItems'][] = 19701;
$ctrt_settings['AlwaysAddItems'][] = 19702;
$ctrt_settings['AlwaysAddItems'][] = 19703;
$ctrt_settings['AlwaysAddItems'][] = 19704;
$ctrt_settings['AlwaysAddItems'][] = 19705;
$ctrt_settings['AlwaysAddItems'][] = 19706;

// Here You can Set all the Raid notes which should be handled as a own raid everytime
$ctrt_settings['OwnRaids'][] = "Random Drop";				// e.g. Random Drops are normaly added to an own Raid (Not all Random Drops to one Raid)

// Here you can set the triggers for the eqDKP Event (CT_RaidRracker Raid note will be parsed (Loot Notes only when $ctrt_set_LootNoteEventTriggerCheck is set))
$ctrt_settings['EventTriggers']["Molten Core"] 				= "Molten Core";
$ctrt_settings['EventTriggers']["MC"] 								= "Molten Core";
$ctrt_settings['EventTriggers']["Blackwing Lair"] 		= "Blackwing Lair";
$ctrt_settings['EventTriggers']["Blackwinglair"] 			= "Blackwing Lair";
$ctrt_settings['EventTriggers']["BWL"] 								= "Blackwing Lair";
$ctrt_settings['EventTriggers']["Onyxia"] 						= "Onyxia";
$ctrt_settings['EventTriggers']["Azuregos"] 					= "Worldbosse";
$ctrt_settings['EventTriggers']["Kazzak"] 						= "Worldbosse";
$ctrt_settings['EventTriggers']["Worldboss"] 					= "Worldbosse";
$ctrt_settings['EventTriggers']["Emeriss"] 						= "Worldbosse";
$ctrt_settings['EventTriggers']["Ysondre"] 						= "Worldbosse";
$ctrt_settings['EventTriggers']["Taerar"] 						= "Worldbosse";
$ctrt_settings['EventTriggers']["Lethon"] 						= "Worldbosse";
$ctrt_settings['EventTriggers']["Zul"] 								= "Zul Gurub";
$ctrt_settings['EventTriggers']["ZG"] 								= "Zul Gurub";
$ctrt_settings['EventTriggers']["Ahn\'Qiraj Temple"] 	= "Ahn Qiraj";
$ctrt_settings['EventTriggers']["Ahn\'Qiraj Ruins"]		= "Ahn Qiraj";
$ctrt_settings['EventTriggers']["Naxxramas"] 					= "Naxxramas";

if($ctrt_set_SplitRaidsbyBoss)
{
	// Here you can set the triggers for the eqDKP Raid Note (CT_RaidRracker Raid note and the Loots Notes will be parsed (Loot Notes will override the Raid Note))
	
	// Zul'Gurub
	$ctrt_settings['RaidNoteTriggers']["Bloodlord Mandokir"]    = "Bloodlord Mandokir";
	$ctrt_settings['RaidNoteTriggers']["Gahz\'ranka"]         	= "Gahz ranka";
	$ctrt_settings['RaidNoteTriggers']["Gri\'lek"]        = "Gri lek";
	$ctrt_settings['RaidNoteTriggers']["Hakkar"]        = "Hakkar";
	$ctrt_settings['RaidNoteTriggers']["Hazza\'rah"]        = "Hazza rah";
	$ctrt_settings['RaidNoteTriggers']["High Priest Thekal"]    = "High Priest Thekal";
	$ctrt_settings['RaidNoteTriggers']["High Priest Venoxis"]    = "High Priest Venoxis";
	$ctrt_settings['RaidNoteTriggers']["High Priestess Arlokk"]    = "High Priestess Arlokk";
	$ctrt_settings['RaidNoteTriggers']["High Priestess Jeklik"]    = "High Priestess Jeklik";
	$ctrt_settings['RaidNoteTriggers']["High Priestess Mar\'li"]    = "High Priestess Mar li";
	$ctrt_settings['RaidNoteTriggers']["Jin\'do the Hexxer"]    = "Jin do the Hexxer";
	$ctrt_settings['RaidNoteTriggers']["Ohgan"]        = "Ohgan";
	$ctrt_settings['RaidNoteTriggers']["Renataki"]        = "Renataki";
	$ctrt_settings['RaidNoteTriggers']["Shade of Jin\'do"]    = "Shade of Jin do";
	$ctrt_settings['RaidNoteTriggers']["Wushoolay"]        = "Wushoolay";
	
	// AQ20
	$ctrt_settings['RaidNoteTriggers']["Kurinnaxx"]        = "Kurinnaxx"; 
	$ctrt_settings['RaidNoteTriggers']["General Rajaxx"]    = "General Rajaxx";
	$ctrt_settings['RaidNoteTriggers']["Moam"]        = "Moam";
	$ctrt_settings['RaidNoteTriggers']["Buru the Gorger"]    = "Buru the Gorger";
	$ctrt_settings['RaidNoteTriggers']["Rajaxx"]        = "General Rajaxx";
	$ctrt_settings['RaidNoteTriggers']["Ayamiss the Hunter"]    = "Ayamiss the Hunter";
	$ctrt_settings['RaidNoteTriggers']["Buru"]        = "Buru the Gorger";
	$ctrt_settings['RaidNoteTriggers']["Ayamiss"]        = "Ayamiss the Hunter";
	$ctrt_settings['RaidNoteTriggers']["Ossirian the Unscarred"]    = "Ossirian the Unscarred";
	$ctrt_settings['RaidNoteTriggers']["Ossirian"]        = "Ossirian the Unscarred";
	
	// AQ40
	$ctrt_settings['RaidNoteTriggers']["The Prophet Skeram"] = "The Prophet Skeram";
	$ctrt_settings['RaidNoteTriggers']["Fankriss the Unyielding"] = "Fankriss the Unyielding";
	$ctrt_settings['RaidNoteTriggers']["Battleguard Sartura"] = "Battleguard Sartura";
	$ctrt_settings['RaidNoteTriggers']["Princess Huhuran"] = "Princess Huhuran";
	$ctrt_settings['RaidNoteTriggers']["Twin Emperors"] = "Twin Emperors";
	$ctrt_settings['RaidNoteTriggers']["Emperor Vek'nilash"] = "Twin Emperors";
	$ctrt_settings['RaidNoteTriggers']["Emperor Vek'lor"] = "Twin Emperors";
	$ctrt_settings['RaidNoteTriggers']["C\'Thun"] = "CThun";
	$ctrt_settings['RaidNoteTriggers']["Vem"] = "Bug Trio";
	$ctrt_settings['RaidNoteTriggers']["Ouro"] = "Ouro";
	$ctrt_settings['RaidNoteTriggers']["Princess Yauj"] = "Bug Trio";
	$ctrt_settings['RaidNoteTriggers']["Lord Kri"] = "Bug Trio";
	$ctrt_settings['RaidNoteTriggers']["Viscidus"] = "Viscidus";
	
	// Outdoor Bosses
	$ctrt_settings['RaidNoteTriggers']["Azuregos"] 				= "Azuregos";
	$ctrt_settings['RaidNoteTriggers']["Kazzak"] 					= "Kazzak";
	$ctrt_settings['RaidNoteTriggers']["Onyxia"] 					= "Onyxia";
	$ctrt_settings['RaidNoteTriggers']["Emeriss"] 				= "Emeriss";
	$ctrt_settings['RaidNoteTriggers']["Ysondre"] 				= "Ysondre";
	$ctrt_settings['RaidNoteTriggers']["Taerar"] 					= "Taerar";
	$ctrt_settings['RaidNoteTriggers']["Lethon"] 					= "Lethon";
	
	// Onyxia's Lair
	$ctrt_settings['RaidNoteTriggers']["Onyxia"]         = "Onyxia";
	
	// Molten Core
	$ctrt_settings['RaidNoteTriggers']["Lucifron"]				= "Lucifron";
	$ctrt_settings['RaidNoteTriggers']["Magmadar"]				= "Magmadar";
	$ctrt_settings['RaidNoteTriggers']["Gehennas"] 				= "Gehennas";
	$ctrt_settings['RaidNoteTriggers']["Garr"] 						= "Garr";
	$ctrt_settings['RaidNoteTriggers']["Geddon"] 					= "Baron Geddon";
	$ctrt_settings['RaidNoteTriggers']["Shazzrah"] 				= "Shazzrah";
	$ctrt_settings['RaidNoteTriggers']["Sulfuron"] 				= "Sulfuron";
	$ctrt_settings['RaidNoteTriggers']["Golemagg"] 				= "Golemagg";
	$ctrt_settings['RaidNoteTriggers']["Majordomo"] 			= "Majordomo";
	$ctrt_settings['RaidNoteTriggers']["Ragnaros"] 				= "Ragnaros";
	
	// Blackwing Lair
	$ctrt_settings['RaidNoteTriggers']["Razorgore"] 			= "Razorgore";
	$ctrt_settings['RaidNoteTriggers']["Vaelastrasz"] 		= "Vaelastrasz";
	$ctrt_settings['RaidNoteTriggers']["Broodlord"] 			= "Broodlord";
	$ctrt_settings['RaidNoteTriggers']["Lashlayer"] 			= "Broodlord";
	$ctrt_settings['RaidNoteTriggers']["Firemaw"] 				= "Firemaw";
	$ctrt_settings['RaidNoteTriggers']["Ebonroc"] 				= "Ebonroc";
	$ctrt_settings['RaidNoteTriggers']["Flamegor"] 				= "Flamegor";
	$ctrt_settings['RaidNoteTriggers']["Chromaggus"] 			= "Chromaggus";
	$ctrt_settings['RaidNoteTriggers']["Nefarius"] 				= "Lord Nefarius";
	$ctrt_settings['RaidNoteTriggers']["Nefarian"] 				= "Lord Nefarius";
	
	// Naxxramas
	$ctrt_settings['RaidNoteTriggers']["Patchwerk"]								= "Patchwerk";
	$ctrt_settings['RaidNoteTriggers']["Grobbulus"]								= "Grobbulus";
	$ctrt_settings['RaidNoteTriggers']["Gluth"]										= "Gluth";
	$ctrt_settings['RaidNoteTriggers']["Thaddius"]								= "Thaddius";
	$ctrt_settings['RaidNoteTriggers']["Instructor Razuvious"]		= "Instructor Razuvious";
	$ctrt_settings['RaidNoteTriggers']["Gothik the Harvester"]		= "Gothik the Harvester";
	$ctrt_settings['RaidNoteTriggers']["Highlord Mograine"]				= "Highlord Mograine";
	$ctrt_settings['RaidNoteTriggers']["Thane Korthazz"]					= "Thane Korthazz";
	$ctrt_settings['RaidNoteTriggers']["Lady Blameux"]						= "Lady Blameux";
	$ctrt_settings['RaidNoteTriggers']["Sir Zeliek"]							= "Sir Zeliek";
	$ctrt_settings['RaidNoteTriggers']["Noth The Plaguebringer"]	= "Noth The Plaguebringer";
	$ctrt_settings['RaidNoteTriggers']["Heigan the Unclean"]			= "Heigan the Unclean";
	$ctrt_settings['RaidNoteTriggers']["Loatheb"]									= "Loatheb";
	$ctrt_settings['RaidNoteTriggers']["Anub\'Rekhan"]						= "Anub Rekhan";
	$ctrt_settings['RaidNoteTriggers']["Grand Widow Faerlina"]		= "Grand Widow Faerlina";
	$ctrt_settings['RaidNoteTriggers']["Maexxna"]									= "Maexxna";
	$ctrt_settings['RaidNoteTriggers']["Sapphiron"]								= "Sapphiron";
	$ctrt_settings['RaidNoteTriggers']["Kel\'Thuzad"]							= "Kel Thuzad";
	
	// Other
	$ctrt_settings['RaidNoteTriggers']["Random"]									= "Random Drop";
	$ctrt_settings['RaidNoteTriggers']["Trash mob"]			 					= "Random Drop";
}	
else
{
	$ctrt_set_RaidNoteTriggers["nosplit"] 					= "nosplit";
}	

// Here you can set Player aliases, if one of the players is in the attende List it will be replaced (e.g. if a Twink of the Mainchar helps out, but the Mainchar should get the DKP Points)
$ctrt_settings['PlayerAliases']["bank"]		     = "disenchanted"; // will hide banked items
#$ctrt_set_PlayerAliases["Twink1ofMainChar1"]	= "MainChar1";
#$ctrt_set_PlayerAliases["Twink2ofMainChar1"]	= "MainChar1";
?>