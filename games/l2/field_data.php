<?php

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

// Category 'character' is a fix one! All others are created dynamically!

$xml_fields = array(

	'classpath'	=> array(
		'type'			=> 'dropdown',
		'category'		=> 'character',
		'name'			=> 'uc_class_path',
		'options'		=> array('Fighter' => 'uc_path_1', 'Soldier' => 'uc_path_2', 'Mystic' => 'uc_path_3', 'Rogue' => 'uc_path_4', 'Scout' => 'uc_path_5', 'Assassin' => 'uc_path_6', 'Warder' => 'uc_path_7', 'Cleric' => 'uc_path_8', 'Knight' => 'uc_path_9', 'Shaman' => 'uc_path_10', 'Oracle' => 'uc_path_11', 'Scavenger' => 'uc_path_12', 'Wizard' => 'uc_path_13', 'Warrior' => 'uc_path_14', 'Artisan' => 'uc_path_15', 'Raider' => 'uc_path_16', 'Monk' => 'uc_path_17', 'Trooper' => 'uc_path_18', 'Hawkeye' => 'uc_path_19', 'Silver Ranger' => 'uc_path_20', 'Phantom Ranger' => 'uc_path_21', 'Arbalester' => 'uc_path_22', 'Prophet' => 'uc_path_23', 'Sword Singer' => 'uc_path_24', 'Bladedancer' => 'uc_path_25', 'Overlord' => 'uc_path_26', 'Warcryer' => 'uc_path_27', 'Inspector' => 'uc_path_28', 'Bishop' => 'uc_path_29', 'Elder' => 'uc_path_30', 'Paladin' => 'uc_path_31', 'Dark Avenger' => 'uc_path_32', 'Temple Knight' => 'uc_path_33', 'Shillien Knight' => 'uc_path_34', 'Treasure Hunter' => 'uc_path_35', 'Plains Walker' => 'uc_path_36', 'Abyss Walker' => 'uc_path_37', 'Bounty Hunter' => 'uc_path_38', 'Warlock' => 'uc_path_39', 'Elemental Summoner' => 'uc_path_40', 'Phantom Summoner' => 'uc_path_41', 'Warlord' => 'uc_path_42', 'Gladiator' => 'uc_path_43', 'Warsmith' => 'uc_path_44', 'Destroyer' => 'uc_path_45', 'Tyrant' => 'uc_path_46', 'Berserker' => 'uc_path_47', 'Sorcerer' => 'uc_path_48', 'Necromancer' => 'uc_path_49', 'Spellsinger' => 'uc_path_50', 'Spellhowler' => 'uc_path_51', 'Soul Breaker' => 'uc_path_52', 'Sagitarrius' => 'uc_path_53', 'Moonlight Sentinel' => 'uc_path_54', 'Ghost Sentinel' => 'uc_path_55', 'Trickster' => 'uc_path_56', 'Hierophant' => 'uc_path_57', 'Sword Muse' => 'uc_path_58', 'Spectral Dancer' => 'uc_path_59', 'Dominator' => 'uc_path_60', 'Doom Cryer' => 'uc_path_61', 'Judicator' => 'uc_path_62', 'Cardinal' => 'uc_path_63', 'Eva&acute;s Saint' => 'uc_path_64', 'Shillien Saint' => 'uc_path_65', 'Phoenix Knight' => 'uc_path_66', 'Hell Knight' => 'uc_path_67', 'Eva&acute;s Templar' => 'uc_path_68', 'Shillien Templar' => 'uc_path_69', 'Adventurer' => 'uc_path_70', 'Wind Rider' => 'uc_path_71', 'Ghost Hunter' => 'uc_path_72', 'Fortune Seeker' => 'uc_path_73', 'Arcana Lord' => 'uc_path_74', 'Elemental Master' => 'uc_path_75', 'Spectral Master' => 'uc_path_76', 'Dreadnought' => 'uc_path_77', 'Duelist' => 'uc_path_78', 'Maestro' => 'uc_path_79', 'Titan' => 'uc_path_80', 'Grand Khavatari' => 'uc_path_81', 'Doombringer' => 'uc_path_82', 'Archmage' => 'uc_path_83', 'Soultaker' => 'uc_path_84', 'Mysic Muse' => 'uc_path_85', 'Storm Screamer' => 'uc_path_86', 'Soul Hound' => 'uc_path_87', 'Yul Archer' => 'uc_path_88', 'Iss Enchanter' => 'uc_path_89', 'Aeore Healer' => 'uc_path_90', 'Sigel Knight' => 'uc_path_91', 'Othell Rogue' => 'uc_path_92', 'Wynn Summoner' => 'uc_path_93', 'Tyrr Warrior' => 'uc_path_94', 'Feoh Wizard' => 'uc_path_95'),
		'undeletable'	=> true,
		'visible'		=> true,
	),

	'gender'	=> array(
		'type'			=> 'dropdown',
		'category'		=> 'character',
		'name'			=> 'uc_gender',
		'options'		=> array('Male' => 'uc_male', 'Female' => 'uc_female'),
		'undeletable'	=> true,
		'visible'		=> true
	),
	'guild'	=> array(
		'type'			=> 'text',
		'category'		=> 'character',
		'name'			=> 'uc_guild',
		'size'			=> 40,
		'undeletable'	=> true,
		'visible'		=> true	
	),

);
?>