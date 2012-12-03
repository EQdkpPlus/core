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

$english_array =  array(
	'class' => array(
		0	=> 'Unknown',
		1	=> 'Engineering',
		2	=> 'Science',
		3	=> 'Tactical',
	),
  
	'race' => array(
    	//Federation
			'Unknown',
		  'Human',
			'Vulcan',
			'Bajoran',
			'Bolian',
			'Benzite ',			
			'Betazoid',
			'Andorian',
			'Saurian',	
			'Trill',
			'Tellarite',
			'Ferengi',
			'Pakled',
			//Klingon Empire
			'Orion',
			'Gorn',
			'Nausicaan',
			'Lethean',
			//Shared
			'Klingon',
			'Liberated Borg',
			'Alien'
    ),		
	'faction' => array(
		'Federation',
		'Klingon Empire',
	),
	'lang' => array(
		'sto' => 'Star Trek Online',
	),
);

?>