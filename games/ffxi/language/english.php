<?php

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}
$english_array = array(
	'classes' => array(
		0 => 'Unknown',
		1 => 'Bard',
		2 => 'Beastmaster',
		3 => 'Blackmage',
		4 => 'Bluemage',
		5 => 'Corsair',
		6 => 'Dancer',
		7 => 'Dark Knight',
		8 => 'Dragoon',
		9 => 'Monk',
		10 => 'Ninja',
		11 => 'Paladin',
		12 => 'Puppetmaster',
		13 => 'Ranger',
		14 => 'Redmage',
		15 => 'Samurai',
		16 => 'Scholar',
		17 => 'Summoner',
		18 => 'Thief',
		19 => 'Warrior',
		20 => 'Whitemage',
	),
	'races' => array(
		'Unknown',
		'Elvaan',
		'Galka',
		'Hume',
		'Mithra',
		'Tarutaru',		
	),
	'factions' => array(
		'Bastok',
		'SanDoria',
		'Windurst',
	),
	'lang' => array(
		'ffxi' => 'Final Fantasy XI',
		'tank' => 'Tank',
		'support' => 'Support',
		'damage_dealer' => 'Damage Dealer',
		
		// Profile information
		'uc_gender'						=> 'Gender',
		'uc_male'						=> 'Male',
		'uc_female'						=> 'Female',
		'uc_guild'						=> 'Guild',

	),
);
?>