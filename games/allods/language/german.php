<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       01.07.2009
 * Date:        $Date: 2009-05-17 16:10:37 +0200 (So, 17 Mai 2009) $
 * -----------------------------------------------------------------------
 * @author      $Author: hoofy_leon $
 * @copyright   2006-2008 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev: 4885 $
 *
 * $Id: filter.php 4885 2009-05-17 14:10:37Z sz3 $
 */

if ( !defined('EQDKP_INC') )
{
    header('HTTP/1.0 404 Not Found');
    exit;
}

$german_array = array(
	'class' => array(
       	0 => 'Unbekannt',
        1 => 'Krieger',
        2 => 'Paladin',
        3 => 'Heiler',
        4 => 'Beschwörer',
        5 => 'Magier',
        6 => 'Behüter',
        7 => 'Psioniker',
        8 => 'Späher',
	),
    'race' => array(
       	'Unbekannt',
       	'Gibberlings',
		'Elfen',
		'Kanians',
		'Xadaganians',
		'Orks',
		'Arisen'
    ),
	'faction' => array(
		'Die Liga',
		'Das Imperium'
	),
	'lang' => array(
		'allods' => 'Allods Online',
		'plate' => 'Platte',
		'cloth' => 'Stoff',
		'leather' => 'Leder',
		'mail' => 'Schwere Rüstung',
	),
);

?>