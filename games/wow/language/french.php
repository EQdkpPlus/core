<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       01.07.2009
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2006-2008 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev$
 *
 * $Id$
 */

if ( !defined('EQDKP_INC') )
{
    header('HTTP/1.0 404 Not Found');
    exit;
}
$french_array = array(
	'class' => array(
		0 => 'Inconnue',
	    1 => 'Chevalier de la mort',
	    2 => 'Druide',
	    3 => 'Chasseur',
	    4 => 'Mage',
	    5 => 'Paladin',
	    6 => 'Prêtre',
	    7 => 'Voleur',
	    8 => 'Chaman',
	    9 => 'Démoniste',
	   10 => 'Guerrier',
	),
	'race' => array(
        	'Inconnue',
        	'Gnome',
        	'Humain',
        	'Nain',
        	'Elfe de la nuit',
        	'Troll',
        	'Mort-vivant',
        	'Orc',
        	'Tauren',
        	'Draeneï',
        	'Elfe de sang'
    ),
	'faction' => array('Alliance', 'Horde'),
	'lang' => array(
		'wow' => 'World of Warcraft',
		'plate' => 'Plate',
		'cloth' => 'Cloth',
		'leather' => 'Leather',
		'mail' => 'Mail',
		'tier45' => 'Tier4/5: ',
		'tier6' => 'Tier6: ',
		'tier78' => 'Tier7/8: ',
		'talents' => array(
            1   => array('Sang','Givre','Impie'),
            2   => array('Équilibre','Combat farouche','Restauration'),
            3   => array('Maîtrise des bêtes','Précision','Survie'),
            4   => array('Arcanes','Feu','Givre'),
			5   => array('Sacré','Protection','Vindicte'),
            6   => array('Discipline','Sacré','Ombre'),
			7   => array('Assassinat','Combat','Finesse'),
            8   => array('Élémentaire','Amélioration','Restauration'),
			9  	=> array('Affliction','Démonologie','Destruction'),
           10   => array('Armes','Fureur','Protection'),
        ),
    ),
    'realmlist' => array(
        "Arak-arahm",
        "Arathi",
        "Archimonde",
        "Chants éternels",
        "Cho'gall",
        "Confrérie du Thorium",
        "Conseil des Ombres",
        "Culte de la Rive noire",
        "Dalaran",
        "Drek'Thar",
        "Eitrigg",
        "Eldre'Thalas",
        "Elune",
        "Garona",
        "Hyjal",
        "Illidan",
        "Kael'thas",
        "Khaz Modan",
        "Kirin Tor",
        "Krasus",
        "La Croisade écarlate",
        "Les Clairvoyants",
        "Les Sentinelles",
        "Marécage de Zangar",
        "Medivh",
        "Naxxramas",
        "Ner'zhul",
        "Rashgarroth",
        "Sargeras",
        "Sinstralis",
        "Suramar",
        "Temple noir",
        "Throk'Feroth",
        "Uldaman",
        "Varimathras",
        "Vol'jin",
        "Ysondre",
    ),
);
?>