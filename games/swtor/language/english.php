<?php
/*	Project:	EQdkp-Plus
 *	Package:	Star Wars - The Old Republic game package
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
$english_array =  array(
	'factions' => array(
		''			=> 'choose faction',
		'republic'	=> 'Republic',
		'imperial'	=> 'Empire'
	),
	'classes' => array(
		0	=> 'choose race',

		#republic
		1	=> 'Vanguard',
		2	=> 'Commando',
		3	=> 'Scoundrel',
		4	=> 'Gunslinger',
		5	=> 'Sage',
		6	=> 'Shadow',
		7	=> 'Sentinel',
		8	=> 'Guardian',

		#imperium
		9	=> 'Powertech',
		10	=> 'Mercenary',
		11	=> 'Operative',
		12	=> 'Sniper',
		13	=> 'Sorcerer',
		14	=> 'Assassin',
		15	=> 'Marauder',
		16	=> 'Juggernaut',
	),
	'races' => array(
		0	=> 'Unknown',
		1	=> 'Human',
		2	=> 'Rattataki',
		3	=> 'Twi\'lek',
		4	=> 'Chiss',
		5	=> 'Sith Pureblood',
		6	=> 'Miraluka',
		7	=> 'Mirialan',
		8	=> 'Zabrak',
		9	=> 'Cyborg',
		10	=> 'Cathar',
	),
		'skills'=> array(
		0 	=> 'Shield Specialist', //Frontkaempfer
		1 	=> 'Tactics',			//Frontkaempfer
		2 	=> 'Assault Specialist',//Kommando
		3 	=> 'Combat Medic', 		//Kommando
		4 	=> 'Gunnery',			//Kommando
		5 	=> 'Seer',				//Gelehrter
		6 	=> 'Telekinetics',		//Gelehrter
		7 	=> 'Balance',			//Gelehrter
		8 	=> 'Kinetic Combat',	//Schatten
		9 	=> 'Infiltration',		//Schatten
		10 	=> 'Watchman',			//Wächter
		11 	=> 'Combat',			//Wächter
		12 	=> 'Concentration',		//Wächter
		13 	=> 'Defence',			//Hüter
		14 	=> 'Vigilance',			//Hüter
		15 	=> 'Sawbones',			//Schurke
		16 	=> 'Scrapper',			//Schurke
		17 	=> 'Dirty Fighting',	//Revolverheld
		18 	=> 'Sharpshooter',		//Revolverheld
		19 	=> 'Saboteur',			//Revolverheld
		20  => 'Choose Class',		//platzhalter
		21  => 'Bodyguard',			//Soeldner
		22  => 'Arsenal',			//Soeldner
		23  => 'Pyrotech',			//Powertech
		24  => 'Shield Tech',		//Powertech
		25  => 'Advance Prototype',	//Powertech
		26  => 'Annihilation',		//Marodeur
		27  => 'Carnage',			//Marodeur
		28  => 'Fury',				//Marodeur
		29  => 'Immortal',			//Juggernaut
		30  => 'Corruption',		//Hexer
		31  => 'Lightning',			//Hexer
		32  => 'Madness',			//Hexer
		33  => 'Darkness',			//Attentaeter
		34  => 'Deception',			//Attentaeter
		35  => 'Marksmanship',		//Scharfschuetze
		36  => 'Engineering',		//Scharfschuetze
		37  => 'Lethality',			//Saboteur
		38  => 'Medicine',			//Saboteur
		39  => 'Concealment',		//Saboteur
		40  => 'Vengeance',			//Juggernaut
		41	=> 'Rage',				//juggernaut
		42	=> 'Focus',				//Hüter
		43	=> 'Serenity',			//Schatten
		44	=> 'Hatred',			//Attentäter
		45	=> 'Ruffian',			//Schurke
		46	=> 'Virulence',			//Scharfschuetze
		47	=> 'Innovative Ordnance', //Söldner
		48 	=> 'Plasmatech', 		//Frontkaempfer
		
		
	),

	'roles' => array(
		1	=> 'Healer',
		2	=> 'Tank',
		3	=> 'DPS',
		4	=> 'PVP',
	),
			'professions' => array(
		'0'								=> 'Unknown',
		'biochem'						=> 'Biochem', //
		'cybertech'						=> 'Cybertech', //
		'artifice'						=> 'Artifice', //
		'armormech'						=> 'Armormech',
		'armstech'						=> 'Armstech',
		'synthweaving'					=> 'Synthweaving', //
		'bioanalysis'					=> 'Bioanalysis', //
		'scavenging'					=> 'Scavenging', //
		'archaeolgy'					=> 'Archaeolgy', //
		'diplomacy'						=> 'Diplomacy', //
		'underworldtrading'				=> 'Underworldtrading', //
		'slicing'						=> 'Slicing', //
		'investigation'					=> 'Investigation', //
		'treasurehunting'				=> 'Treasurehunting', //
	),

	'lang' => array(
		'swtor'						=> 'Star Wars: The Old Republic',
		//Reputation
		'uc_cat_reputation'			=> 'Reputation',
		'reputation'				=> 'Reputation',
		'head_reputation_perc'		=> 'Value',
		'head_reputation_name'		=> 'Faction',
		'tab_reputation'			=> 'Reputation',
		'ruflevel'					=> 'Level',
		'repuname0'					=> 'Without',
		'repuname1'					=> 'Outsider',
		'repuname2'					=> 'Newcomer',
		'repuname3'					=> 'Friend',
		'repuname4'					=> 'Hero',
		'repuname5'					=> 'Champion',
		'repuname6'					=> 'Legend',

		'ruf1'	=> 'T.H.O.R.N.',
		'ruf2'	=> 'The Gree-Enclave',
		'ruf3'	=> 'The Contraband Resale Corporation',
		'ruf4'	=> 'The Dread Executioners',
		'ruf5'	=> 'The Voss',
		'ruf6'	=> 'Binary Star Realty',
		'ruf7'	=> 'Galactic Solutions Industries',
		'ruf8'	=> 'Imperial Guard on Belsavis - Republic Fifth Assault Battalion',
		'ruf9'	=> 'Makeb Imperial Forces - Citizens of Makeb',
		'ruf10'	=> 'Imperial Forward Command',
		'ruf11'	=> 'Bounty Brokers Associations',
		'ruf12'	=> 'Bounty Supply Company',
		'ruf13'	=> 'Ordnance Acquisition Corps - The Adjudicators',
		'ruf14'	=> 'Imperial First Mobile Fleet',
		'ruf15'	=> 'Interplanetary Compenent Exchange',
		'ruf16'	=> 'Strike Team Oricon',
		'ruf17' => 'Republikanic Hyperspace Armada',
		'ruf18'	=> 'Coalition Forces on Yavin 4',
		'ruf19'	=> 'Freebooters Trade Union',
		'ruf20'	=> 'People of Rishi',
		
		//Admin Settings
		'core_sett_fs_gamesettings'	=> 'SWToR Settings',
		'uc_one_faction'			=> 'Restrict the class selection to a specific faction?',
		'uc_faction'				=> 'Faction',
		'uc_faction_help'			=> 'The classes of the opposing faction cannot be selected anymore.',

		// Profile information
		'uc_gender'					=> 'Gender',
		'uc_male'					=> 'Male',
		'uc_female'					=> 'Female',
		'uc_guild'					=> 'Guild',
		'uc_race'					=> 'Race',
		'uc_class'					=> 'Class',
		'uc_skill'		  			=> 'Discipline',
		'uc_servername'				=> 'Realm',
		'uc_prof1_value'			=> 'Tier',
		'uc_prof1_name'				=> 'Regular occupation',
		'uc_prof2_value'			=> 'Tier',
		'uc_prof2_name'				=> 'Secondary occupation',
		'uc_prof3_value'			=> 'Tier',
		'uc_prof3_name'				=> 'Secondary occupation',
		'uc_prof_professions'		=> 'Profession',
		'uc_level'					=> 'Level',
		
		//Operation
		
		'sm_ewigekammer'					=> 'The Eternity Vault (Story)',
		'hc_ewigekammer'					=> 'The Eternity Vault (Hard)',
		'nm_ewigekammer'					=> 'The Eternity Vault (Nightmare)',
		'sm_karaggaspalast'					=> 'Karaggas Palace (Story)',
		'hc_karaggaspalast'					=> 'Karaggas Palace (Hard)',
		'nm_karaggaspalast'					=> 'Karaggas Palace (Nightmare)',
		'sm_explosivkonflikt'				=> 'Explosiv Conflict (Story)',
		'hc_explosivkonflikt'				=> 'Explosiv Conflict (Hard)',
		'nm_explosivkonflikt'				=> 'Explosiv Conflict (Nightmare)',
		'sm_abschaum'						=> 'Scum and Villainy (Story)',
		'hc_abschaum'						=> 'Scum and Villainy (Hard)',
		'nm_abschaum'						=> 'Scum and Villainy (Nightmare)',
		'sm_schrecken'						=> 'Terror from Beyond (Story)',
		'hc_schrecken'						=> 'Terror from Beyond (Hard)',
		'nm_schrecken'						=> 'Terror from Beyond (Nightmare)',
		'sm_s_festung'						=> 'Dread Fortress (Story)',
		'hc_s_festung'						=> 'Dread Fortress (Hard)',
		'nm_s_festung'						=> 'Dread Fortress (Nightmare)',
		'sm_s_palast'						=> 'Dread Palace (Story)',
		'hc_s_palast'						=> 'Dread Palace (Hard)',
		'nm_s_palast'						=> 'Dread Palace (Nightmare)',
		'sm_tbh'							=> 'Golden Fury (Story)',
		'hc_tbh'							=> 'Golden Fury (Hard)',
		'nm_tbh'							=> 'Golden Fury (Nightmare)',
		'sm_wueter'							=> 'The Ravagers (Story)',
		'hc_wueter'							=> 'The Ravagers (Schwer)',
		'nm_wueter'							=> 'The Ravagers (Alptraum)',
		'sm_tempel'							=> 'Tempel of Sacrifice (Story)',
		'hc_tempel'							=> 'Tempel of Sacrifice (Schwer)',
		'nm_tempel'							=> 'Tempel of Sacrifice(Alptraum)',

		//realms
	),
		'realmlist' => array(
		'T3-M4','Darth Nihilus',
		'Tomb of Freedon Nadd',
		'Jar\'Kai Sword',
		'The Progenitor',
		'Vanjervalis Chain',
		'Battle Meditation',
		'Mantle of the Force',
		'The Red Eclipse',
		'The Bastion',
		'Begeren Colony',
		'The Harbinger',
		'The Shadowlands',
		'Jung Ma',
		'The Ebon Hawk',
		'Prophecy of the Five',
		'Jedi Covenant'),
);

?>
