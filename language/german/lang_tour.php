<?php
/*	Project:	EQdkp-Plus
 *	Package:	Language File
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

$lang = array(

	//Navigation
	'navi'	=> '<ul><li><a href="?tour=next"><b>Mit dem nächsten Schritt der Tour fortfahren</b></a></li><li><a href="?tour=reload">Schritt erneut anzeigen</a></li><li><a href="?tour=cancel">Tour beenden</a></li></ul>',
	'navi_title'	=> 'EQdkp Plus Tour',
	'steps'	=> 'Schritt %d von %d',
	
	//Step 0 - Start
	'step_0'	=> 'Herzlich Willkommen zur EQdkp Plus Tour!<br /><br />Diese Tour führt dich durch die wichtigen Funktionen dieses CMS & DKP-System. <br /><ul><li>Einstellungen</li><li>Plugins</li><li>Portalmodule</li><li>Benutzer verwalten</li><li>Raids verwalten</li><li>Tabellen & Punktesystem</li><li>Artikel verwalten</li><li>Sicherung</li></ul>',
	'step_0_title'	=> 'Start',
	
	//Step1	=> Settings
	'step_1'	=> 'Auf dieser Seite kannst du diverse Einstellungen rund um dein EQdkp Plus tätigen. Du findest hier Einstellungen wie z.B.<ul><li>Layout-Einstellungen wie Seitenname</li><li>Spiel-Einstellungen wie Gildenname, Servername,...</li><li>Kontaktinformationen</li><li>Email-Einstellungen</li><li>Registrierungs-Einstellungen (z.B. CAPTCHA)</li><li>Itemstats-Einstellungen</li></ul>',
	'step_1_title'	=> 'Einstellungen',
	
	//Step2 - Plugins
	'step_2'	=> 'Plugins sind Erweiterungen, die die Funktionalität deines EQdkp Plus ausbauen. Um ein Plugin zu installieren, klicke einfach auf "Installieren".<br /><br />Empfohlene Plugins:<ul><li>Raidlogimport: importiere Raidlogs aus Ingame-Addons</li></ul>',
	'step_2_title'	=> 'Plugins',
	
	//Step3 - Portalmodule
	'step_3'	=> 'Mit Portalmodulen kannst du dir verschiedenste Sachen in deinem Portal anzeigen, z.B. :<ul><li>Teamspeak oder andere Voiceserver</li><li>aktuelle Fightlogs</li><li>nächste Geburtstage</li><li>Wetter</li></ul>uvm. <br /> Im Tab "Positionierung" kannst du die Portalmodule dahin schieben, wo du sie gerne haben willst.<br /><br />Du kannst auch eigene Blöcke anlegen, und diese in deinem Template positionieren.',
	'step_3_title'	=> 'Portalmodule',
	
	//Step4 - Manage User
	'step_4'	=> 'Hier kannst du Benutzer verwalten, z.B. freischalten wenn einer nach der Registrierung noch inaktiv ist.<br /><br />Damit sich ein Benutzer für einen Raid anmelden kann oder DKP-Punkte bekommen kann, musst du ihm hier einen Charakter zuweisen.<br /><br />Auch kannst du hier die Berechtigungen eines Benutzers verwalten, d.h. ihm Rechte zuweisen, welche Aufgaben er im EQdkp Plus ausführen darf.<br />Dies kann entweder geschehen, indem du den Benutzer in Benutzergruppen unterbringst, oder ihm individuelle Rechte zuweist.<br /><br />Welche Rechte ein Gast hat, kannst du über die Benutzergruppe "Gäste" festlegen.<br ><br >Mehr Informationen zum Thema Berechtigungen findest du in <a href="'.EQDKP_WIKI_URL.'/Benutzergruppen" target="_blank" style="color:#000;">diesem Wiki-Artikel</a>',
	'step_4_title'	=> 'Benutzer verwalten',
	
	//Step5 - Manage Raids
	'step_5'	=> 'Um Charakteren DKP-Punkte zuzuweisen, verwendest du in der Regel Raids. In einem Raid gibst du das Datum, das Event, den DKP-Wert und natürlich die Teilnehmer des Raids an, also die Charaktere, denen die DKP-Punkte gutgeschrieben werden sollen.<br />Desweiteren kannst du hier auch die Items an die Charaktere vergeben und ihnen die entsprechenden DKP-Punkte wieder abziehen. Auch individuelle Korrekturen sind möglich.<br /><br />Wenn du ein Ingame-Addon benutzt, um den Raid aufzuzeichen, ist eher das Plugin "Raidlogimport" deine richtige Anlaufstelle, da dieses Plugin die Raiderstellung übernimmt.<br /><br />Mehr Informationen zum Thema "Punktevergabe" findest du in <a href="'.EQDKP_WIKI_URL.'/How_to_Benutzung_EQdkp_Plus" target="_blank" style="color:#000;">diesem Wiki-Artikel</a>',
	'step_5_title'	=> 'Raids verwalten',
	
	//Step6 - Manage Layout
	'step_6'	=> 'Da jeder andere Ansprüche hat, wie sein EQdkp Plus aussehen soll oder welches DKP-Punkte-System genutzt wird, kannst du hier auswählen, wie das EQdkp Plus aussehen soll.<br /><br />Neben vorgefertigen Layouts wie "normal", "EPGP" oder "Suicide Kings" kannst du auch selbst dein eigenes Layout zusammenstellen.<br /><br />In jedem Tab kannst du für die jeweilige Seite festlegen, welche Spalten angezeigt werden sollen.',
	'step_6_title'	=> 'Tabellen & Punktesysteme verwalten',
	
	//Step7 - Manage articles
	'step_7'	=> 'Mit Artikel kannst du vieles realisieren: News, Gildeninfos oder auch Blogs.<br >Mit einem umfangreichen Editor und dem Medien-Manager bleiben beim Erstellen der Artikel keine Wünsche offen.<br ><br >Doch nicht nur das - du kannst hier auch Gildenregeln erstellen, die jeder Benutzer bei der Registrierung bestätigen muss.',
	'step_7_title'	=> 'Artikel verwalten',

	//Step8 - Backup
	'step_8'	=> 'Jeder kennt das Problem - der PC geht kaputt, und die Sicherung wurde vergessen.<br /><br />Dies kann nicht mehr vorkommen!<br /><br />Nicht nur, dass man nun auch Backups wiederherstellen kann, sondern es ist auch möglich, automatisiert Backups erstellen zu lassen. Die Optionen hierfür findet ihr unter dem Menüpunkt "Zeitgesteuerte Aufgaben verwalten" <br /><br />Mehr Informationen zum Thema "Sicherung" findest du in <a href="'.EQDKP_WIKI_URL.'/Sicherung" target="_blank" style="color:#000;">diesem Wiki-Artikel</a>',
	'step_8_title'	=> 'Sicherung',
	
	//Step9 - End
	'step_9'	=> 'Vielen Dank, dass du diese Tour durch das EQdkp Plus mitgemacht hast.<br /><br />Solltest du noch Fragen haben, kannst du <ul><li>in unserer <a href="'.EQDKP_WIKI_URL.'" style="color:#000">Wiki</a> nachlesen</li><li>oder in unserem <a href="'.EQDKP_BOARD_URL.'" style="color:#000">Forum</a>.</li></ul> Du kannst diese Tour jederzeit wiederholen, in dem du auf der Startseite des Adminbereiches in den Tab "Support" gehst.<br /><br />Viel Spaß wünscht das gesamte EQdkp Plus Team',
	'step_9_title'	=> 'Ende',


);

?>