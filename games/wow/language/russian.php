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
$russian_array = array(
	'class' => array(
	   	0 => 'Íåèçâåñòíî',
	  	1 => 'Todesritter',
	   	2 => 'Äðóèä',
	  	3 => 'Îõîòíèê',
	   	4 => 'Ìàã',
	   	5 => 'Ïàëàäèí',
	    6 => 'Æðåö',
	    7 => 'Ðàçáîéíèê',
	    8 => 'Øàìàí',
	    9 => 'Êîëäóí',
	   10 => 'Âîèí'
	),
	'race' => array(
        	'Íåèçâåñòíî',
        	'Ãíîì',
        	'×åëîâåê',
        	'Äâàðô',
        	'Íî÷íîé ýëüô',
        	'Òðîëü',
        	'Íåæèòü',
        	'Îðê',
        	'Òàóðåí',
        	'Äðåíåé',
        	'Êðîâàâûé ýëüô'
    ),
    'faction' => array('Àëüÿíñ', 'Îðäà'),
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
            1   => array('Blood','Frost','Unholy'),
            2     => array('Áàëàíñ','Äèêîñòü','Âîññòàíîâëåíèå'),
            3    => array('Ïîâåëèòåëü Çâåðåé','Ñòðåëüáà','Âûæèâàíèå'),
            4      => array('Àðêàí','Îãîíü','Õîëîä'),
			5   => array('Ñâåò','Çàùèòà','Âîçìåçäèå'),
            6    => array('Äèñöèïëèíà','Ñâåò','Òåíü'),
			7     => array('Óáèéñòâî','Áèòâà','Òîíêîñòü'),
            8    => array('Ñòèõèÿ','Çà÷àðîâàíèå','Âîññòàíîâëåíèå'),
			9   => array('Áåäñòâèå','Äåìîíîëîãèÿ','Ðàçðóøåíèå'),
           10   => array('Îðóæèå','ßðîñòü','Çàùèòà'),
		),
	),
	'realmlist' => array(),
);
?>