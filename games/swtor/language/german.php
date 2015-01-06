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
$german_array = array(
	'factions' => array(
		'' => 'Wähle Fraktion', //use "" or "_select" as key for selection entries - nothing else!
		'republic'	=> 'Republik',
		'imperial'	=> 'Imperium'
	),
	'classes' => array(
		0	=> 'Wähle Rasse',

		#republic
		1	=> 'Frontkämpfer',
		2	=> 'Kommando',
		3	=> 'Schurke',
		4	=> 'Revolverheld',
		5	=> 'Gelehrter',
		6	=> 'Schatten',
		7	=> 'Wächter',
		8	=> 'Hüter',

		#imperium
		9	=> 'Powertech',
		10	=> 'Söldner',
		11	=> 'Saboteur',
		12	=> 'Scharfschütze',
		13	=> 'Hexer',
		14	=> 'Attentäter',
		15	=> 'Marodeur',
		16	=> 'Juggernaut',
	),
	'races' => array(
		0	=> 'Unbekannt',
		1	=> 'Mensch',
		2	=> 'Rattataki',
		3	=> 'Twi\'lek',
		4	=> 'Chiss',
		5	=> 'Reinblut Sith',
		6	=> 'Miraluka',
		7	=> 'Mirialan',
		8	=> 'Zabrak',
		9	=> 'Cyborg',
		10	=> 'Cathar',
	),
	'skills'=> array(
		0 	=> 'Schildspezialist', 	//Frontkaempfer
		1 	=> 'Taktiker',			//Frontkaempfer
		2 	=> 'Angriffsspezialist',//Kommando
		3 	=> 'Gefechtssanitäter', //Kommando
		4 	=> 'Artillerist',		//Kommando
		5 	=> 'Seher',				//Gelehrter
		6 	=> 'Telekinese',		//Gelehrter
		7 	=> 'Gleichgewicht',		//Gelehrter
		8 	=> 'Kinetikkampf',		//Schatten
		9 	=> 'Infiltration',		//Schatten
		10 	=> 'Wachmann',			//Wächter
		11 	=> 'Kampf',				//Wächter
		12 	=> 'Konzentration',		//Wächter
		13 	=> 'Verteidigung',		//Hüter
		14 	=> 'Wachsamkeit',		//Hüter
		15 	=> 'Knochenflicker',	//Schurke
		16 	=> 'Schläger',			//Schurke
		17 	=> 'Fieser Kämpfer',	//Revolverheld
		18 	=> 'Meisterschütze',	//Revolverheld
		19 	=> 'Sabotage',			//Revolverheld
		20  => 'Wähle Klasse',		//platzhalter
		21  => 'Leibwache',			//Soeldner
		22  => 'Arsenal',			//Soeldner
		23  => 'Pyrotech',			//Powertech
		24  => 'Schildtechnologie',	//Powertech
		25  => 'Spezialprototyp',	//Powertech
		26  => 'Vernichtung',		//Marodeur
		27  => 'Blutbad',			//Marodeur
		28  => 'Raserei',			//Marodeur
		29  => 'Unstreblich',		//Juggernaut
		30  => 'Korrumpierung',		//Hexer
		31  => 'Blitzschlag',		//Hexer
		32  => 'Wahnsinn',			//Hexer
		33  => 'Dunkelheit',		//Attentaeter
		34  => 'Täuschung',			//Attentaeter
		35  => 'Treffsicherheit',	//Scharfschuetze
		36  => 'Ingenieur',			//Scharfschuetze
		37  => 'Tödlichkeit',		//Saboteur
		38  => 'Medizin',			//Saboteur
		39  => 'Verborgenheit',		//Saboteur
		40  => 'Vergeltung',		//Juggernaut
		41	=> 'Wut',				//juggernaut
		42	=> 'Fokus',				//Hüter
		43	=> 'Gelassenheit',		//Schatten
		44	=> 'Hass',				//Attentäter
		45	=> 'Grobian',			//Schurke
		46	=> 'Giftigkeit',		//Scharfschuetze
		47	=> 'Innovative Bewaffnung', //Söldner
		48 	=> 'Plasmatech', 		//Frontkaempfer
		
		
	),
	'roles' => array(
		1	=> 'Heiler',
		2	=> 'Tank',
		3	=> 'Schaden',
		4	=> 'PVP',
	),
	'professions' => array(
		'0'								=> 'Unbekannt',
		'biochem'						=> 'Biochemie', //
		'cybertech'						=> 'Cybertech', //
		'artifice'						=> 'Kunstfertigkeit', //
		'armormech'						=> 'Rüstungsbau',
		'armstech'						=> 'Waffenbau',
		'synthweaving'					=> 'Synth-Fertigung', //
		'bioanalysis'					=> 'Bioanalyse', //
		'scavenging'					=> 'Plündern', //
		'archaeolgy'					=> 'Archäologie', //
		'diplomacy'						=> 'Diplomatie', //
		'underworldtrading'				=> 'Unterwelthandel', //
		'slicing'						=> 'Hacken', //
		'investigation'					=> 'Ermittlung', //
		'treasurehunting'				=> 'Schatzsuche', //
	),

	'lang' => array(
		'swtor'						=> 'Star Wars: The Old Republic',
		//Reputation
		'uc_cat_reputation'			=> 'Ruf',
		'reputation'				=> 'Ruf',
		'head_reputation_perc'		=> 'Wert',
		'head_reputation_name'		=> 'Fraktion',
		'tab_reputation'			=> 'Ruf',
		'ruflevel'					=> 'Stufe',
		'repuname0'					=> 'Ohne',
		'repuname1'					=> 'Fremndling',
		'repuname2'					=> 'Neuling',
		'repuname3'					=> 'Freund',
		'repuname4'					=> 'Held',
		'repuname5'					=> 'Streiter',
		'repuname6'					=> 'Legende',

		'ruf1'	=> 'D.H.O.R.N.',
		'ruf2'	=> 'Die Gree-Enklave',
		'ruf3'	=> 'Die Schmugglerwaren-Wiederverkaufsgesellschaft',
		'ruf4'	=> 'Die Schreckenshenker',
		'ruf5'	=> 'Die Voss',
		'ruf6'	=> 'Doppelstern-Immobilien',
		'ruf7'	=> 'Galactic Solutions Industries',
		'ruf8'	=> 'Imperiale Garde auf Belsavis',
		'ruf9'	=> 'Imperiale Streitkräfte auf Makeb',
		'ruf10'	=> 'Imperiales Frontkommando',
		'ruf11'	=> 'Kopfgeld-Vermittlungsgesellschaft',
		'ruf12'	=> 'Kopfgeld-Versorgungsunternehmen',
		'ruf13'	=> 'Waffensicherheitskorps',
		'ruf14'	=> '1. imperiale Flotte',
		'ruf15'	=> 'Interplanetare Komponentenbörse',
		'ruf16'	=> 'Stoßtrupp Oricon', 
		'ruf17' => 'Republikanische Hyperraum-Armada', 
		'ruf18'	=> 'Coalition Forces on Yavin 4', 		//deu name fehlt
		'ruf19'	=> 'Freebooters Trade Union', 			//deu name fehlt
		'ruf20'	=> 'People of Rishi', 					//deu name fehlt

		//Admin Settings
		'core_sett_fs_gamesettings'	=> 'SWToR Einstellungen',
		'uc_one_faction'			=> 'Klassenauswahl auf bestimmte Fraktion einschränken?',
		'uc_faction'				=> 'Fraktion',
		'uc_faction_help'			=> 'Die Klassen der gegnerischen Fraktion können nicht mehr ausgewählt werden.',

		// Profile information
		'uc_gender'					=> 'Geschlecht',
		'uc_male'					=> 'Männlich',
		'uc_female'					=> 'Weiblich',
		'uc_guild'					=> 'Gilde',
		'uc_race'					=> 'Rasse',
		'uc_class'					=> 'Klasse',
		'uc_skill'		  			=> 'Skillung',
		'uc_servername'				=> 'Realm',
		'uc_prof1_value'			=> 'Stufe',
		'uc_prof1_name'				=> 'Hauptberuf',
		'uc_prof2_value'			=> 'Stufe',
		'uc_prof2_name'				=> 'Sekundärberuf',
		'uc_prof3_value'			=> 'Stufe',
		'uc_prof3_name'				=> 'Sekundärberuf',
		'uc_prof_professions'		=> 'Berufe',
		'uc_level'					=> 'Stufe',
		
		//Operation
		'sm_ewigekammer'					=> 'Ewige Kammer (Story)',
		'hc_ewigekammer'					=> 'Ewige Kammer (Schwer)',
		'nm_ewigekammer'					=> 'Ewige Kammer (Alptraum)',
		'sm_karaggaspalast'					=> 'Karaggas Palast (Story)',
		'hc_karaggaspalast'					=> 'Karaggas Palast (Schwer)',
		'nm_karaggaspalast'					=> 'Karaggas Palast (Alptraum)',
		'sm_explosivkonflikt'				=> 'Explosiv Konflikt (Story)',
		'hc_explosivkonflikt'				=> 'Explosiv Konflikt (Schwer)',
		'nm_explosivkonflikt'				=> 'Explosiv Konflikt (Alptraum)',
		'sm_abschaum'						=> 'Abschaum und Verkommenheit (Story)',
		'hc_abschaum'						=> 'Abschaum und Verkommenheit (Schwer)',
		'nm_abschaum'						=> 'Abschaum und Verkommenheit (Alptraum)',
		'sm_schrecken'						=> 'Schrecken aus der Tiefe (Story)',
		'hc_schrecken'						=> 'Schrecken aus der Tiefe (Schwer)',
		'nm_schrecken'						=> 'Schrecken aus der Tiefe (Alptraum)',
		'sm_s_festung'						=> 'Schreckensfestung (Story)',
		'hc_s_festung'						=> 'Schreckensfestung (Schwer)',
		'nm_s_festung'						=> 'Schreckensfestung (Alptraum)',
		'sm_s_palast'						=> 'Schreckenspalast (Story)',
		'hc_s_palast'						=> 'Schreckenspalast (Schwer)',
		'nm_s_palast'						=> 'Schreckenspalast (Alptraum)',
		'sm_tbh'							=> 'Toborro\'s Hof (Story)',
		'hc_tbh'							=> 'Toborro\'s Hof (Schwer)',
		'nm_tbh'							=> 'Toborro\'s Hof (Alptraum)',
		'sm_wueter'							=> 'Die Wüter (Story)',
		'hc_wueter'							=> 'Die Wüter (Schwer)',
		'nm_wueter'							=> 'Die Wüter (Alptraum)',
		'sm_tempel'							=> 'Tempel des Opfers (Story)',
		'hc_tempel'							=> 'Tempel des Opfers (Schwer)',
		'nm_tempel'							=> 'Tempel des Opfers (Alptraum)',

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