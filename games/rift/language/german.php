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
$german_array = array(
	'classes' => array(
		0 => 'Unbekannt',
		1 => 'Krieger',
		2 => 'Schurke',
		3 => 'Kleriker',
		4 => 'Magier',
	),
	'races' => array(
		'Unknown',
		'Mathosianer',
		'Hochelfen',
		'Zwerg',
		'Bahmi',
		'Eth',
		'Kelari',
	),
	'factions' => array(
		'default',
		'Wächter',
		'Skeptiker',
	),
	'roles' => array(
		1 => array(3,4),
		2 => array(1,2,3),
		3 => array(1,2,3,4),
		4 => array(2,3,4),
	),
	'lang' => array(
		'rift' => 'RIFT',
		'plate'	=> 'Platte',
		'heavy' => 'Kette',
		'medium' => 'Leder',
		'light' => 'Stoff',
		'role1' => 'Heiler',
		'role2' => 'Tank',
		'role3' => 'Damage Dealer',
		'role4' => 'Supporter',
		'import_ranks'	=> 'Ränge importieren',
		'guild_xml'	=> 'Gilden-XML',
		'uc_import_forw' => 'Importieren',
		'uc_import_guild'				=> 'Gilde importieren/aktualisieren',
		'uc_import_guild_help'			=> 'Importiere/aktualisiere alle Mitglieder einer Gilde aus dem Gilden-XML',
		'guild_xml_lang' => 'Sprache des Gilden-XMLs',
		'uc_gimp_header_fnsh'	=> 'Die Aktualisierung der Charaktere wurde beendet. Das Fenster kann nun geschlossen werden.',
		'import_status_true'	=> 'Importiert/Aktualisiert',
		'import_status_false'	=> 'Fehler',
		'guild_xml_error'		=> 'Das Gilden-XML hat ein nicht gültiges Format.',
		'uc_delete_chars_onimport'		=> 'Charaktere im System löschen, die nicht mehr in der Gilde sind',
	),
);
?>