<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2010
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2006-2011 EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev$
 * 
 * $Id$
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
	'step_0'	=> 'Herzlich Willkommen zur EQdkp Plus Tour!<br /><br />Diese Tour führt dich durch die wichtigen Funktionen dieses CMS & DKP-System. <br /><ul><li>Einstellungen</li><li>Plugins</li><li>Portalmodule</li><li>Benutzer verwalten</li><li>Raids verwalten</li><li>Layout/DKP-System</li><li>Eigene CMS-Seiten erstellen</li><li>Sicherung</li></ul>',
	'step_0_title'	=> 'Start',
	
	//Step1	=> Settings
	'step_1'	=> 'Auf dieser Seite kannst du diverse Einstellungen rund um dein EQdkp Plus tätigen. Du findest hier Einstellungen wie z.B.<ul><li>Layout-Einstellungen wie Seitenname</li><li>Spiel-Einstellungen wie Gildenname, Servername,...</li><li>Kontaktinformationen</li><li>Email-Einstellungen</li><li>Registrierungs-Einstellungen (z.B. CAPTCHA)</li><li>Itemstats-Einstellungen</li></ul>',
	'step_1_title'	=> 'Einstellungen',
	
	//Step2 - Plugins
	'step_2'	=> 'Plugins sind Erweiterungen, die die Funktionalität deines EQdkp Plus ausbauen. Um ein Plugin zu installieren, klicke einfach auf "Installieren".<br /><br />Empfohlene Plugins:<ul><li>Raidlogimport: importiere Raidlogs aus Ingame-Addons</li></ul>',
	'step_2_title'	=> 'Plugins',
	
	//Step3 - Portalmodule
	'step_3'	=> 'Mit Portalmodulen kannst du dir verschiedenste Sachen in deinem Portal anzeigen, z.B. :<ul><li>Teamspeak oder andere Voiceserver</li><li>aktuelle Fightlogs</li><li>nächste Geburtstage</li><li>Wetter</li></ul>uvm. <br /> Im Tab "Positionierung" kannst du die Portalmodule dahin schieben, wo du sie gerne haben willst.<br /><br />In den Einstellungen aus Step1 kannst du auch festlegen, welche Portalspalten auf allen Spalten zu sehen sind. Normalerweise werden alle Portalspalten nur auf der Indexseite angezeigt.',
	'step_3_title'	=> 'Portalmodule',
	
		//Step4 - Manage User
	'step_4'	=> 'Hier kannst du Benutzer verwalten, z.B. freischalten wenn einer nach der Registrierung noch inaktiv ist.<br /><br />Damit sich ein Benutzer für einen Raid anmelden kann oder DKP-Punkte bekommen kann, musst du ihm hier einen Charakter zuweisen.<br /><br />Auch kannst du hier die Berechtigungen eines Benutzers verwalten, d.h. ihm Rechte zuweisen, welche Aufgaben er im EQdkp Plus ausführen darf.<br />Dies kann entweder geschehen, indem du den Benutzer in Benutzergruppen unterbringst, oder ihm individuelle Rechte zuweist.<br /><br />Welche Rechte ein Gast hat, kannst du über die Benutzergruppe "Gäste" festlegen.<br ><br >Mehr Informationen zum Thema Berechtigungen findest du in <a href="'.EQDKP_WIKI_URL.'/de/index.php/Benutzergruppen" target="_blank" style="color:#000;">diesem Wiki-Artikel</a>',
	'step_4_title'	=> 'Benutzer verwalten',
	
	//Step5 - Manage Raids
	'step_5'	=> 'Um Charakteren DKP-Punkte zuzuweisen, verwendest du in der Regel Raids. In einem Raid gibst du das Datum, das Event, den DKP-Wert und natürlich die Teilnehmer des Raids an, also die Charaktere, denen die DKP-Punkte gutgeschrieben werden sollen.<br />Desweiteren kannst du hier auch die Items an die Charaktere vergeben und ihnen die entsprechenden DKP-Punkte wieder abziehen. Auch individuelle Korrekturen sind möglich.<br /><br />Wenn du ein Ingame-Addon benutzt, um den Raid aufzuzeichen, ist eher das Plugin "Raidlogimport" deine richtige Anlaufstelle, da dieses Plugin die Raiderstellung übernimmt.<br /><br />Mehr Informationen zum Thema "Raids" findest du in <a href="'.EQDKP_WIKI_URL.'/de/index.php/Ein_Beispielraid" target="_blank" style="color:#000;">diesem Wiki-Artikel</a>',
	'step_5_title'	=> 'Raids verwalten',
	
		//Step6 - Manage Layout
	'step_6'	=> 'Da jeder andere Ansprüche hat, wie sein EQdkp Plus aussehen soll oder welches DKP-Punkte-System genutzt wird, kannst du hier auswählen, wie das EQdkp Plus aussehen soll.<br /><br />Neben vorgefertigen Layouts wie "normal", "EPGP" oder "Suicide Kings" kannst du auch selbst dein eigenes Layout zusammenstellen.<br /><br />In jedem Tab kannst du für die jeweilige Seite festlegen, welche Spalten angezeigt werden sollen.',
	'step_6_title'	=> 'Layout verwalten',
	
	//Step7 - Manage pages
	'step_7'	=> 'Natürlich gehört zu einem CMS, dass man eigene Seiten erstellen kann.<br >Mit einem umfangreichen Editor und dem Upload-Manager bleiben beim Erstellen der eigenen Seiten keine Wünsche offen.<br ><br >Doch nicht nur das - du kannst hier auch Gildenregeln erstellen, die jeder Benutzer bei der Registrierung bestätigen muss.<br /><br />Mehr Informationen zum Thema "Eigene CMS-Seiten erstellen" findest du in <a href="'.EQDKP_WIKI_URL.'/de/index.php/Infoseiten" target="_blank" style="color:#000;">diesem Wiki-Artikel</a>',
	'step_7_title'	=> 'eigene CMS-Seiten erstellen',

	//Step8 - Backup
	'step_8'	=> 'Jeder kennt das Problem - der PC geht kaputt, und die Sicherung wurde vergessen.<br /><br />Dies kann nicht mehr vorkommen!<br /><br />Nicht nur, dass man nun auch Backups wiederherstellen kann, sondern es ist auch möglich, automatisiert Backups erstellen zu lassen. Die Optionen hierfür findet ihr unter dem Menüpunkt "Zeitgesteuerte Aufgaben verwalten" <br /><br />Mehr Informationen zum Thema "Sicherung" findest du in <a href="'.EQDKP_WIKI_URL.'/de/index.php/Sicherung" target="_blank" style="color:#000;">diesem Wiki-Artikel</a>',
	'step_8_title'	=> 'Sicherung',
	
	//Step9 - End
	'step_9'	=> 'Vielen Dank, dass du diese Tour durch das EQdkp Plus mitgemacht hast.<br /><br />Solltest du noch Fragen haben, kannst du <ul><li>in unserer <a href="'.EQDKP_WIKI_URL.'" style="color:#000">Wiki</a> nachlesen</li><li>oder in unserem <a href="'.EQDKP_BOARD_URL.'" style="color:#000">Forum</a>.</li></ul> Du kannst diese Tour jederzeit wiederholen, in dem du auf der Startseite des Adminbereiches in den Tab "Support" gehst.<br /><br />Viel Spaß wünscht das gesamte EQdkp Plus Team',
	'step_9_title'	=> 'Ende',


);

?>