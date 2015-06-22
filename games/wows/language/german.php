<?php
/*	Project:	EQdkp-Plus
 *	Package:	World of Warships game package
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2015 EQdkp-Plus Developer Team
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU Affero General Public License as published
 *	by the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU Affero General Public License for more details.
 *
 *	You should have received a copy of the GNU Affero General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}
$german_array =  array(
	'races' => array(
		0 	=> 'Wähle Land',   			
		1	=> 'Vereinigte Staaten',	
		2	=> 'Japan',					
		3	=> 'Deutschland',			
		4	=> 'Groß Britanien',			
		5	=> 'Sowjetunion',			
//		6	=> 'Italien',	coming soon
	),
	'roles' => array(
		1	=> 'Zerstörer',
		2	=> 'Kreuzer',
		3	=> 'Schlachtschiff',
		4	=> 'Flugzeugträger',
	),
	'classes' => array(
		0	=> 'Wähle Schiffsklasse',
		1	=> 'Zerstörer',
		2	=> 'Kreuzer',
		3	=> 'Schlachtschiff',
		4	=> 'Flugzeugträger',
		5	=> 'Premium',
	),

/* Schiffsnummer zusammengesetzt:   Rasse : Premium : Klasse : Stufe */

	'ships' => array(
		0			=> 'Wähle Schiff',
		// Zerstörer
		//US
		10100		=>	'',
		10101		=>	'',
		10102		=>	'II Sampson',
		10103		=>	'III Wickes',
		10104		=>	'IV Clemson',
		10105		=>	'V Nicholas',
		10105		=>	'VI Farragut',
		10107		=>	'VII Mahan',
		10108		=>	'VIII Benson',
		10109		=>	'IX Fletcher',
		10110		=>	'X Gearing',
		//Japan
		20100		=>	'',
		20101		=>	'',
		20102		=>	'II Umikaze',
		20103		=>	'III Wakatake',
		20104		=>	'IV Isokaze',
		20105		=>	'V Minekaze',
		20106		=>	'VI Mutsuki',
		20107		=>	'VII Hatsuharu',
		20108		=>	'VIII Fubuki',
		20109		=>	'IX Kagero',
		20110		=>	'X Shimakaze',

		//PremiumSchiffe
		51105	=>	'V Gremjaschtschi',
		
	//Kreuzer
		//US
		10200		=>	'',
		10201		=>	'I Erie',
		10202		=>	'II Chester',
		10203		=>	'III St. Louis',
		10204		=>	'IV Phoenix',
		10205		=>	'V Omaha',
		10206		=>	'VI Cleveland',
		10207		=>	'VII Pensacola',
		10208		=>	'VIII New Orleans',
		10209		=>	'IX Baltimore',
		10210		=>	'X Des Moines',
		//Japan
		20200		=>	'',
		20201		=>	'I Katori',
		20202		=>	'II Chikuma',
		20203		=>	'III Tenryu',
		20204		=>	'IV Kuma',
		20205		=>	'V Furutaka',
		20206		=>	'VI Aoba',
		20207		=>	'VII Myoko',
		20208		=>	'VIII Mogami',
		20209		=>	'IX Ibuki',
		20210		=>	'X Zao',

		//PremiumSchiffe
		11202		=>	'II Albany',
		11207		=>	'VII Atlanta',
		21208		=>	'VIII Katikami',
		51203		=>	'III Aurora',
		51205		=>	'V Murmansk',
		
	//Schalchtschiffe
		//US
		10300		=>	'',
		10301		=>	'',
		10302		=>	'',
		10303		=>	'III South Carolina',
		10304		=>	'IV Wyoming',
		10305		=>	'V New York',
		10306		=>	'VI New Mexico',
		10307		=>	'VII Colorado',
		10308		=>	'VIII North Carolina',
		10309		=>	'IX Iowa',
		10310		=>	'X Montana',
		//Japan
		20300		=>	'',
		20301		=>	'',
		20302		=>	'',
		20303		=>	'III Kawachi',
		20304		=>	'IV Myogi',
		20305		=>	'V Kongo',
		20306		=>	'VI Fuso',
		20307		=>	'VII Nagato',
		20308		=>	'VIII Amagi',
		20309		=>	'IX Izumo',
		20310		=>	'X Yamato',

		//PremiumSchiffe
		41306		=>	'VI Warspite',

	//Flugzeugträger
		//US
		10400		=>	'',
		10401		=>	'',
		10402		=>	'',
		10403		=>	'',
		10404		=>	'IV Langley',
		10405		=>	'V Bogue',
		10406		=>	'VI Independence',
		10407		=>	'VII Ranger',
		10408		=>	'VIII Lexington',
		10409		=>	'IX Essex',
		10410		=>	'',
		//Japan
		20400		=>	'',
		20401		=>	'',
		20402		=>	'',
		20403		=>	'',
		20404		=>	'IV Hosho',
		20405		=>	'V Zuiho',
		20406		=>	'VI Ryujo',
		20407		=>	'VII Shokaku',
		20408		=>	'',
		20409		=>	'',
		20410		=>	'',

	),
	'usdestroyer' => array(
		// Zerstörer
		//US
		0		=>	'',
		1		=>	'',
		2		=>	'II Sampson',
		3		=>	'III Wickes',
		4		=>	'IV Clemson',
		5		=>	'V Nicholas',
		5		=>	'VI Farragut',
		7		=>	'VII Mahan',
		8		=>	'VIII Benson',
		9		=>	'IX Fletcher',
		10		=>	'X Gearing',
		),
	'uscruisers' => array(
		0		=>	'',
		1		=>	'I Erie',
		2		=>	'II Chester',
		3		=>	'III St. Louis',
		4		=>	'IV Phoenix',
		5		=>	'V Omaha',
		6		=>	'VI Cleveland',
		7		=>	'VII Pensacola',
		8		=>	'VIII New Orleans',
		9		=>	'IX Baltimore',
		10		=>	'X Des Moines',
		),
	'usbattleship'	=> array(
		0		=>	'',
		1		=>	'',
		2		=>	'',
		3		=>	'III South Carolina',
		4		=>	'IV Wyoming',
		5		=>	'V New York',
		6		=>	'VI New Mexico',
		7		=>	'VII Colorado',
		8		=>	'VIII North Carolina',
		9		=>	'IX Iowa',
		10		=>	'X Montana',
		),
	'uscarrier'	=>array(
		0		=>	'',
		1		=>	'',
		2		=>	'',
		3		=>	'',
		4		=>	'IV Langley',
		5		=>	'V Bogue',
		6		=>	'VI Independence',
		7		=>	'VII Ranger',
		8		=>	'VIII Lexington',
		9		=>	'IX Essex',
		10		=>	'',
		),
	'jpndestroyer'	=> array(
		0		=>	'',
		1		=>	'',
		2		=>	'II Umikaze',
		3		=>	'III Wakatake',
		4		=>	'IV Isokaze',
		5		=>	'V Minekaze',
		6		=>	'VI Mutsuki',
		7		=>	'VII Hatsuharu',
		8		=>	'VIII Fubuki',
		9		=>	'IX Kagero',
		10		=>	'X Shimakaze',
		),
	'jpncruiser'	=> array(
		0		=>	'',
		1		=>	'I Katori',
		2		=>	'II Chikuma',
		3		=>	'III Tenryu',
		4		=>	'IV Kuma',
		5		=>	'V Furutaka',
		6		=>	'VI Aoba',
		7		=>	'VII Myoko',
		8		=>	'VIII Mogami',
		9		=>	'IX Ibuki',
		10		=>	'X Zao',
		),
	'jpnbattleship'	=>array(
		0		=>	'',
		1		=>	'',
		2		=>	'',
		3		=>	'III Kawachi',
		4		=>	'IV Myogi',
		5		=>	'V Kongo',
		6		=>	'VI Fuso',
		7		=>	'VII Nagato',
		8		=>	'VIII Amagi',
		9		=>	'IX Izumo',
		10		=>	'X Yamato',
		),
	'jpncarrier'	=>array(
		0		=>	'',
		1		=>	'',
		2		=>	'',
		3		=>	'',
		4		=>	'IV Hosho',
		5		=>	'V Zuiho',
		6		=>	'VI Ryujo',
		7		=>	'VII Shokaku',
		8		=>	'',
		9		=>	'',
		10		=>	'',
		),
	
	
	'lang' => array(
		'wows'			=> 'World of Warships',
		'uc_cat_usa'	=> 'USA',
		'uc_cat_jpn'	=> 'JAPAN',
		'uc_usdestroyer'	=> 'Zerstörer',
		'uc_uscruiser'		=> 'Kreuzer',
		'uc_battleship'		=> 'Schlachtschiff',
		'uc_carrier'		=> 'Flugzeugträger',

		//Profilkram
		'uc_race'	=> 'Bevorzugtes Land',
		'uc_class'	=> 'Bevorzugte Schiffsklasse',
	),
);

?>