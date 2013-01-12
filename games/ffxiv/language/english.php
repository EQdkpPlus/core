<?php

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}
$english_array = array(
	'classes' => array(
		0 => 'Unknown',
		1 => 'Thaumaturge',
		2 => 'Marauder',
		3 => 'Lancer',
		4 => 'Pugilist',
		5 => 'Gladiator',
		6 => 'Archer',
		7 => 'Conjurer',
		8 => 'Botanist',
		9 => 'Fisher',
		10 => 'Miner',
		11 => 'Alchemist',
		12 => 'Armorer',
		13 => 'Blacksmith',
		14 => 'Carpenter',
		15 => 'Culinarian',
		16 => 'Leatherworker',
		17 => 'Goldsmith',
		18 => 'Weaver',
	),
	'races' => array(
		'Unknown',
		'Elezen',
		'Roegadyn',
		'Hyur',
		'Miqote',
		'Lalafell',
	),
	'factions' => array(
		'Gridania',
		'Limsa Lominsa',
		'Uldah',
	),
	'lang' => array(
		'ffxiv' => 'Final Fantasy XIV',
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