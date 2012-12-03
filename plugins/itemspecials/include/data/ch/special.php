<?php
/******************************
 * EQdkp ItemSpecials Plugin
 * (c) 2006 by WalleniuM [Simon Wallmann]
 * http://www.wallenium.de   
 * ------------------
 * setitems.data.php
 * Changed: November 17, 2006
 * 
 ******************************/
 
if ( !defined('EQDKP_INC') )
{
    die('Do not access this file directly.');
}

$classname = array(
        'Warrior',
        'Shaman',
        'Paladin',
        'Hunter',
        'Warlock',
        'Priest',
        'Mage',
        'Rogue',
        'Druid');

$aqbook_items = array(
        'Paladin'      => array('Libram: Blessing of Might VII','Libram: Blessing of Wisdom VI','Libram: Holy Light IX'),
        'Shaman'       => array('Tablet of Grace of Air Totem III','Tablet of Healing Wave X','Tablet of Strength of Earth Totem V'),
			  'Warrior'      => array('Manual of Battle Shout VII','Manual of Heroic Strike IX','Manual of Revenge VI'),
			  'Rogue'        => array('Handbook of Backstab IX','Handbook of Deadly Poison V','Handbook of Feint V'),
			  'Hunter'       => array('Guide: Aspect of the Hawk VII','Guide: Multi-Shot V','Guide: Serpent Sting IX'),
			  'Warlock'      => array('Grimoire of Corruption VII','Grimoire of Immolate VIII','Grimoire of Shadow Bolt X'),
        'Druid'        => array('Book of Healing Touch XI','Book of Rejuvenation XI','Book of Starfire VII'),
			  'Mage'         => array('Tome of Frostbolt XI','Tome of Fireball XII','Tome of Arcane Missiles VIII'),
			  'Priest'       => array('Codex of Greater Heal V','Codex of Prayer of Healing V','Codex of Renew X')
);

$trinket_items = array(
        'Paladin'      => 'Scrolls of Blinding Light',
        'Shaman'       => 'Natural Alignment Crystal',
			  'Warrior'      => 'Lifegiving Gem',
			  'Rogue'        => 'Venomous Totem',
			  'Hunter'       => 'Arcane Infused Gem',
			  'Warlock'      => 'The Black Book',
			  'Druid'        => 'Rune of Metamorphosis',
			  'Mage'         => 'Mind Quickening Gem',
			  'Priest'       => 'Aegis of Preservation'
);

$mount_items = array(
        'Blue'         => 'Blue Qiraji Resonating Crystal',
        'Yellow'       => 'Yellow Qiraji Resonating Crystal',
			  'Green'        => 'Green Qiraji Resonating Crystal',
			  'Red'          => 'Red Qiraji Resonating Crystal',
			  'Black'        => 'Black Qiraji Resonating Crystal'
);

$atiesh_items = array(
        'Splinter of Atiesh',
        'Frame of Atiesh',
			  'Staff Head of Atiesh',
			  'Base of Atiesh',
			  'Atiesh, Greatstaff of the Guardian'
);
?>