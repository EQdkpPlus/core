<?php


if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}
$german_array = array(
	'classes' => array(
		0 => 'Unknown',
		1 => 'Magier',
		2 => 'Kundschafter',
		3 => 'Krieger',
	),
	'races' => array(
		'Menschen',
		'Elfen',
		'Zwerge',
		'Gnome',
		'Orks',
		'Dunkelelfen',
		'Goblins',
		'Dämonen'
	),
	'factions' => array(
		'Member',
	),
	'lang' => array(
		'shakesfidget' => 'Shakes & Fidget',
		
		// Profile information
		'uc_gender'						=> 'Geschlecht',
		'uc_male'						=> 'Männlich',
		'uc_female'						=> 'Weiblich',
		'uc_guild'						=> 'Gilde',
	),
);
?>