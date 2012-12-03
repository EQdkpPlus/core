<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2009
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 * 
 * $Id$
 */

if ( !defined('EQDKP_INC') ){
    header('HTTP/1.0 404 Not Found');exit;
} 

$wow_bosses = array (
	'ruby_sanctum_10' => array(  
	  '4821' => 0, #Siege über Halion (Rubinsanktum, 10 Spieler)
	),
	'ruby_sanctum_10_hm' => array(
	  '4822' => 0, #Siege über Halion (Heroisches Rubinsanktum, 10 Spieler)
	),
	'ruby_sanctum_25' => array(
		'4820' => 0, #Siege über Halion (Rubinsanktum, 25 Spieler
	),
	'ruby_sanctum_25_hm' => array(
		'4823' => 0, #Siege über Halion (Heroisches Rubinsanktum, 25 Spieler)
	),
  'icecrown_10' => array(
		'4639' => 0, #Siege über Lord Mark'gar (Eiskrone, 10 Spieler)
		'4643' => 0, #Siege über Lady Todeswisper (Eiskrone, 10 Spieler)
		'4644' => 0, #Luftschiffkämpfe gewonnen (Eiskrone, 10 Spieler)
		'4645' => 0, #Siege über den Todesbringer (Eiskrone, 10 Spieler)
		'4646' => 0, #Siege über Fauldarm (Eiskrone, 10 Spieler)
		'4647' => 0, #Siege über Modermiene (Eiskrone, 10 Spieler)
		'4648' => 0, #Siege über den Rat der Blutprinzen (Eiskrone, 10 Spieler)
		'4649' => 0, #Valithria Traumwandler gerettet (Eiskrone, 10 Spieler)
		'4650' => 0, #Siege über Professor Seuchenmord (Eiskrone, 10 Spieler)
		'4651' => 0, #Siege über Blutkönigin Lana'thel (Eiskrone, 10 Spieler)
		'4652' => 0, #Siege über Sindragosa (Eiskrone, 10 Spieler)
		'4653' => 0, #Siege über den Lichkönig (Eiskrone, 10 Spieler)
  ),

  'icecrown_10_hm' => array(
		'4640' => 0, #Siege über Lord Mark'gar (Heroische Eiskrone, 10 Spieler)
		'4654' => 0, #Siege über Lady Todeswisper (Heroische Eiskrone, 10 Spieler)
		'4659' => 0, #Luftschiffkämpfe gewonnen (Heroische Eiskrone, 10 Spieler)
		'4662' => 0, #Siege über den Todesbringer (Heroische Eiskrone, 10 Spieler)
		'4665' => 0, #Siege über Fauldarm (Heroische Eiskrone, 10 Spieler)
		'4647' => 0, #Siege über Modermiene (Eiskrone, 10 Spieler)
		'4671' => 0, #Siege über den Rat der Blutprinzen (Heroische Eiskrone, 10 Spieler)
		'4674' => 0, #Valithria Traumwandler gerettet (Heroische Eiskrone, 10 Spieler)
		'4677' => 0, #Siege über Professor Seuchenmord (Heroische Eiskrone, 10 Spieler)
		'4680' => 0, #Siege über Blutkönigin Lana'thel (Heroische Eiskrone, 10 Spieler)
		'4684' => 0, #Siege über Sindragosa (Eiskrone, 10 Spieler)
		'4686' => 0, #Siege über den Lichkönig (Heroische Eiskrone, 10 Spieler)
    ),
    
  'icecrown_25' => array(
	'4641' => 0, #Siege über Lord Mark'gar (Eiskrone, 25 Spieler)
	'4655' => 0, #Siege über Lady Todeswisper (Eiskrone, 25 Spieler)
	'4660' => 0, #Luftschiffkämpfe gewonnen (Eiskrone, 25 Spieler)
	'4663' => 0, #Siege über den Todesbringer (Eiskrone, 25 Spieler)
	'4666' => 0, #Siege über Fauldarm (Eiskrone, 25 Spieler)
	'4669' => 0, #Siege über Modermiene (Eiskrone, 25 Spieler)
	'4672' => 0, #Siege über den Rat der Blutprinzen (Eiskrone, 25 Spieler)
	'4675' => 0, #Valithria Traumwandler gerettet (Eiskrone, 25 Spieler)
	'4678' => 0, #Siege über Professor Seuchenmord (Eiskrone, 25 Spieler)
	'4681' => 0, #Siege über Blutkönigin Lana'thel (Eiskrone, 25 Spieler)
	'4683' => 0, #Siege über Sindragosa (Eiskrone, 25 Spieler)
	'4687' => 0, #Siege über den Lichkönig (Eiskrone, 25 Spieler)    
  ),

  'icecrown_25_hm' => array(
	'4642' => 0, #Siege über Lord Mark'gar (Heroische Eiskrone, 25 Spieler)
	'4656' => 0, #Siege über Lady Todeswisper (Heroische Eiskrone, 25 Spieler)
	'4661' => 0, #Luftschiffkämpfe gewonnen (Heroische Eiskrone, 25 Spieler)
	'4664' => 0, #Siege über den Todesbringer (Heroische Eiskrone, 25 Spieler)
	'4667' => 0, #Siege über Fauldarm (Heroische Eiskrone, 25 Spieler)
	'4670' => 0, #Siege über Modermiene (Heroische Eiskrone, 25 Spieler)
	'4673' => 0, #Siege über den Rat der Blutprinzen (Heroische Eiskrone, 25 Spieler)
	'4676' => 0, #Valithria Traumwandler gerettet (Heroische Eiskrone, 25 Spieler)
	'4679' => 0, #Siege über Professor Seuchenmord (Heroische Eiskrone, 25 Spieler)
	'4682' => 0, #Siege über Blutkönigin Lana'thel (Heroische Eiskrone, 25 Spieler)
	'4685' => 0, #Siege über Sindragosa (Heroische Eiskrone, 25 Spieler)
	'4688' => 0, #Siege über den Lichkönig (Heroische Eiskrone, 25 Spieler)
  ),  

  'onyxia' => array(
    '1098' => 0, #Siege über Onyxia (Onyxias Hort)
  ),

  'totc_10' => array(
	'4028' => 0, #Siege über die Monster von Nordend (Prüfung des Kreuzfahrers 10 Spieler)
	'4032' => 0, #Siege über Lord Jaraxxus (Prüfung des Kreuzfahrers, 10 Spieler)
	'4036' => 0, #Siege über die Champions der Fraktionen (Prüfung des Kreuzfahrers 10 Spieler)
	'4040' => 0, #Siege über die Zwillingsval'kyr (Prüfung des Kreuzfahrers, 10 Spieler)
	'4044' => 0, #Anzahl der Abschlüsse der Prüfung des Kreuzfahrers (10 Spieler)
  ),
  
  'totc_10_hm' => array(
	'4030' => 0, #Siege über die Monster von Nordend (Prüfung des Obersten Kreuzfahrers 10 Spieler)
	'4033' => 0, #Siege über Lord Jaraxxus (Prüfung des Obersten Kreuzfahrers, 10 Spieler)
	'4037' => 0, #Siege über die Champions der Fraktionen (Prüfung des Obersten Kreuzfahrers 10 Spieler)
	'4041' => 0, #Siege über die Zwillingsval'kyr (Prüfung des Obersten Kreuzfahrers, 10 Spieler)
	'4045' => 0, #Anzahl der Abschlüsse der Prüfung des Obersten Kreuzfahrers (10 Spieler)
  ),
  
  'totc_25' => array(
	'4031' => 0, #Siege über die Monster von Nordend (Prüfung des Kreuzfahrers 25 Spieler)
	'4034' => 0, #Siege über Lord Jaraxxus (Prüfung des Kreuzfahrers, 25 Spieler)
	'4038' => 0, #Siege über die Champions der Fraktionen (Prüfung des Kreuzfahrers 25 Spieler)
	'4042' => 0, #Siege über die Zwillingsval'kyr (Prüfung des Kreuzfahrers, 25 Spieler)
	'4046' => 0, #Anzahl der Abschlüsse der Prüfung des Kreuzfahrers (25 Spieler)
  ),
  
  'totc_25_hm' => array(
	'4029' => 0, #Siege über die Monster von Nordend (Prüfung des Obersten Kreuzfahrers 25 Spieler)
	'4035' => 0, #Siege über Lord Jaraxxus (Prüfung des Obersten Kreuzfahrers, 25 Spieler)
	'4039' => 0, #Siege über die Champions der Fraktionen (Prüfung des Obersten Kreuzfahrers 25 Spieler)
	'4043' => 0, #Siege über die Zwillingsval'kyr (Prüfung des Obersten Kreuzfahrers, 25 Spieler)
	'4047' => 0, #Anzahl der Abschlüsse der Prüfung des Obersten Kreuzfahrers (25 Spieler)
  ),

  'ulduar_10' => array (
	'2856' => 0, #Siege über den Flammenleviathan (Ulduar, 10 Spieler)
	'2857' => 0, #Siege über Klingenschuppe (Ulduar, 10 Spieler)
	'2858' => 0, #Siege über Ignis, Meister des Eisenwerks (Ulduar, 10 Spieler)
	'2859' => 0, #Siege über XT-002 Dekonstruktor (Ulduar, 10 Spieler)
	'2860' => 0, #Siege über die Versammlung des Eisens (Ulduar, 10 Spieler)
	'2861' => 0, #Siege über Kologarn (Ulduar, 10 Spieler)
	'2868' => 0, #Siege über Auriaya (Ulduar, 10 Spieler)
	'2862' => 0, #Siege über Hodir (Ulduar 10 Spieler)
	'2863' => 0, #Siege über Thorim (Ulduar 10 Spieler)
	'2864' => 0, #Siege über Freya (Ulduar 10 Spieler)
	'2865' => 0, #Siege über Mimiron (Ulduar 10 Spieler)
	'2866' => 0, #Siege über General Vezax (Ulduar, 10 Spieler)
	'2869' => 0, #Siege über Yogg-Saron (Ulduar, 10 Spieler)
	'2867' => 0, #Siege über Algalon den Beobachter (Ulduar 10 Spieler)
  ),
    
  'ulduar_25' => array (
	'2872' => 0, #Siege über den Flammenleviathan (Ulduar, 25 Spieler)
	'2873' => 0, #Siege über Klingenschuppe (Ulduar, 25 Spieler)
	'2874' => 0, #Siege über Ignis, Meister des Eisenwerks (Ulduar, 25 Spieler)
	'2884' => 0, #Siege über XT-002 Dekonstruktor (Ulduar, 25 Spieler)
	'2885' => 0, #Siege über die Versammlung des Eisens (Ulduar, 25 Spieler)
	'2875' => 0, #Siege über Kologarn (Ulduar, 25 Spieler)
	'2882' => 0, #Siege über Auriaya (Ulduar, 25 Spieler)
	'3256' => 0, #Siege über Hodir (Ulduar 25 Spieler)
	'3257' => 0, #Siege über Thorim (Ulduar 25 Spieler)
	'3258' => 0, #Siege über Freya (Ulduar 25 Spieler)
	'2879' => 0, #Siege über Mimiron (Ulduar 25 Spieler)
	'2880' => 0, #Siege über General Vezax (Ulduar, 25 Spieler)
	'2883' => 0, #Siege über Yogg-Saron (Ulduar, 25 Spieler)
	'2881' => 0, #Siege über Algalon den Beobachter (Ulduar 25 Spieler)
  ),
    
  'naxx_10' => array (
	'1361' => 0, #Siege über Anub'Rekhan (Naxxramas, 10 Spieler)
	'1372' => 0, #Siege über Gluth (Naxxramas, 10 Spieler)
	'1366' => 0, #Siege über Gothik den Ernter (Naxxramas, 10 Spieler)
	'1362' => 0, #Siege über Großwitwe Faerlina (Naxxramas, 10 Spieler)
	'1371' => 0, #Siege über Grobbulus (Naxxramas, 10 Spieler)
	'1369' => 0, #Siege über Heigan den Unreinen (Naxxramas, 10 Spieler)
	'1375' => 0, #Siege über die Vier Reiter (Naxxramas, 10 Spieler)
	'1374' => 0, #Siege über Instrukteur Razuvious (Naxxramas, 10 Spieler)
	'1370' => 0, #Siege über Loatheb (Naxxramas, 10 Spieler)
	'1363' => 0, #Siege über Maexxna (Naxxramas, 10 Spieler)
	'1365' => 0, #Siege über Noth den Seuchenfürst (Naxxramas, 10 Spieler)
	'1364' => 0, #Siege über Flickwerk (Naxxramas, 10 Spieler)
	'1373' => 0, #Siege über Thaddius (Naxxramas, 10 Spieler)
	'1376' => 0, #Siege über Saphiron (Naxxramas, 10 Spieler)
	'1377' => 0, #Siege über Kel'Thuzad (Naxxramas, 10 Spieler)		
	),
	
  'naxx_25' => array (
	'1368' => 0, #Siege über Anub'Rekhan (Naxxramas, 25 Spieler)
	'1378' => 0, #Siege über Gluth (Naxxramas, 25 Spieler)
	'1379' => 0, #Siege über Gothik den Ernter (Naxxramas, 25 Spieler)
	'1380' => 0, #Siege über Großwitwe Faerlina (Naxxramas, 25 Spieler)
	'1381' => 0, #Siege über Grobbulus (Naxxramas, 25 Spieler)
	'1382' => 0, #Siege über Heigan den Unreinen (Naxxramas, 25 Spieler)
	'1383' => 0, #Siege über die Vier Reiter (Naxxramas, 25 Spieler)
	'1384' => 0, #Siege über Instrukteur Razuvious (Naxxramas, 25 Spieler)
	'1385' => 0, #Siege über Loatheb (Naxxramas, 25 Spieler)
	'1386' => 0, #Siege über Maexxna (Naxxramas, 25 Spieler)
	'1387' => 0, #Siege über Noth den Seuchenfürst (Naxxramas, 25 Spieler)
	'1367' => 0, #Siege über Flickwerk (Naxxramas, 25 Spieler)
	'1388' => 0, #Siege über Thaddius (Naxxramas, 25 Spieler)
	'1389' => 0, #Siege über Saphiron (Naxxramas, 25 Spieler)
	'1390' => 0, #Siege über Kel'Thuzad (Naxxramas, 25 Spieler)		
	),
	
	
	'vault_of_archavon_10' => array(
    '1753' => 0, #Siege über Archavon den Steinwächter (Tausendwinter, 10 Spieler)
    '2870' => 0, #Siege über Emalon den Sturmwächter (Tausendwinter, 10 Spieler
    '4074' => 0, #Siege über Koralon den Flammenwächter (Tausendwinter, 10 Spieler)
    '4657' => 0, #Siege über Toravon den Eiswächter (Tausendwinter, 10 Spieler)
  ),
  
	'vault_of_archavon_25' => array(
    '1754' => 0, #Siege über Archavon den Steinwächter (Tausendwinter, 25 Spieler)
    '3236' => 0, #Siege über Emalon den Sturmwächter (Tausendwinter, 25 Spieler)
    '4075' => 0, #Siege über Koralon den Flammenwächter (Tausendwinter, 25 Spieler)
    '4658' => 0, #Siege über Toravon den Eiswächter (Tausendwinter, 25 Spieler)
  ),
  
	'eye_of_eternity_10' => array(
    	'1391' => 0, #Siege über Malygos (10 Spieler)
  ),
  
	'eye_of_eternity_25' => array(
    	'1394' => 0, #Siege über Malygos (25 Spieler)
  ),
  
  'obsidian_sanctum_10' => array(
    '1392' => 0, #Siege über Sartharion (Kammer der Aspekte, 10 Spieler)
  ),
  
  'obsidian_sanctum_25' => array(
    '1393' => 0, #Siege über Sartharion (Kammer der Aspekte, 25 Spieler)
  ),
  
  'burning_crusade' => array(
    '1068' => 0, #Siege über Keli'dan den Zerstörer (Blutkessel)
	'1069' => 0, #Siege über Nexusprinz Shaffar (Managruft)
	'1070' => 0, #Siege über den Epochenjäger (Flucht von Durnholde)
	'1071' => 0, #Siege über Quagmirran (Sklavenunterkünfte)
	'1072' => 0, #Siege über die Schattenmutter (Tiefensumpf)
	'1073' => 0, #Siege über Exarch Maladaar (Auchenaikrypta)
	'1074' => 0, #Siege über Klauenkönig Ikiss (Sethekkhallen)
	'1075' => 0, #Siege über Murmur (Schattenlabyrinth)
	'1076' => 0, #Siege über Aeonus (Öffnung des Dunklen Portals)
	'1077' => 0, #Siege über Kriegsherr Kalithresh (Dampfkessel)
	'1078' => 0, #Siege über Kargath Messerfaust (Zerschmetterte Hallen)
	'1079' => 0, #Siege über Pathaleon den Kalkulator (Die Mechanar)
	'1080' => 0, #Siege über Warpzweig (Die Botanika)
	'1081' => 0, #Siege über Herold Horizontiss (Die Arkatraz)
	'1082' => 0, #Siege über Kael'thas Sonnenwanderer (Terrasse der Magister)
	'1083' => 0, #Siege über Prinz Malchezaar (Karazhan)
	'1084' => 0, #Siege über Zul'jin (Zul'Aman)
	'1085' => 0, #Siege über Gruul (Gruuls Unterschlupf)
	'1086' => 0, #Siege über Magtheridon (Magtheridons Kammer)
	'1087' => 0, #Siege über Lady Vashj (Höhle des Schlangenschreins)
	'1088' => 0, #Siege über Kael'thas Sonnenwanderer (Festung der Stürme)
	'1089' => 0, #Siege über Illidan Sturmgrimm (Der Schwarze Tempel)
	'1090' => 0, #Siege über Kil'jaeden (Sonnenbrunnenplateau)
  ),
  
  'classic' => array(
    '1091' => 0, #Siege über Edwin van Cleef (Todesminen)
	'1092' => 0, #Siege über Erzmagier Arugal (Burg Schattenfang)
	'1093' => 0, #Siege über Kommandant Mograine (Scharlachrotes Kloster)
	'1094' => 0, #Siege über Häuptling Ukorz Sandskalp (Zul'Farrak)
	'1095' => 0, #Siege über Imperator Dagran Thaurissan (Schwarzfelstiefen)
	'1097' => 0, #Siege über Baron Totenschwur (Stratholme)
	'1096' => 0, #Siege über General Drakkisath (Schwarzfelsspitze)
	'1102' => 0, #Siege über Hakkar (Zul'Gurub)
	'1098' => 0, #Siege über Onyxia (Onyxias Hort)
	'1099' => 0, #Siege über Ragnaros (Geschmolzener Kern)
	'1100' => 0, #Siege über Nefarian (Pechschwingenhort)
	'1101' => 0, #Siege über C'Thun (Tempel von Ahn'Qiraj)
  ),
  
  
  
); 


$wow_instances = array ('ruby_sanctum_10','ruby_sanctum_25', 'icecrown_10', 'icecrown_25' , 'onyxia', 
						'totc_10', 'totc_25', 'ulduar_10' , 'ulduar_25', 'naxx_10', 'naxx_25' , 'vault_of_archavon_10',
						'eye_of_eternity_10' , 'eye_of_eternity_25' , 'obsidian_sanctum_10', 'obsidian_sanctum_25' , 
						'burning_crusade' , 'classic');


?>