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

// Arrays with the Set Content
$setitems_Tier1 = array(
        'Warrior' => array(
                'Helm of Might',
                'Pauldrons of Might',
                'Breastplate of Might',
                'Legplates of Might',
                'Sabatons of Might',
                'Gauntlets of Might',
                'Bracers of Might',
                'Belt of Might',
        ),
        'Paladin' => array(
                'Lawbringer Helm',
                'Lawbringer Spaulders',
                'Lawbringer Chestguard',
                'Lawbringer Legplates',
                'Lawbringer Boots',
                'Lawbringer Gauntlets',
                'Lawbringer Bracers',
                'Lawbringer Belt',
                            
                
        ),
        'Shaman' => array(
                'Earthfury Helmet',
                'Earthfury Epaulets',
                'Earthfury Vestments',
                'Earthfury Legguards',
                'Earthfury Boots',
                'Earthfury Gauntlets',
                'Earthfury Bracers',
                'Earthfury Belt',     
        ),
        'Hunter' => array(
                'Giantstalker\'s Helmet',
                'Giantstalker\'s Epaulets',
                'Giantstalker\'s Breastplate',
                'Giantstalker\'s Leggings',
                'Giantstalker\'s Boots',
                'Giantstalker\'s Gloves',
                'Giantstalker\'s Bracers',
                'Giantstalker\'s Belt',
        ),
        'Warlock' => array(
                'Felheart Horns',
                'Felheart Shoulder Pads',
                'Felheart Robes',
                'Felheart Pants',
                'Felheart Slippers',
                'Felheart Gloves',
                'Felheart Bracers',
                'Felheart Belt',
        ),
        'Priest' => array(
                'Circlet of Prophecy',
                'Mantle of Prophecy',
                'Robes of Prophecy',
                'Pants of Prophecy',
                'Boots of Prophecy',
                'Gloves of Prophecy',
                'Vambraces of Prophecy',
                'Girdle of Prophecy',    
        ),
        'Mage' => array(
                'Arcanist Crown',
                'Arcanist Mantle',
                'Arcanist Robes',
                'Arcanist Leggings',
                'Arcanist Boots',
                'Arcanist Gloves',
                'Arcanist Bindings',
                'Arcanist Belt',
        ),
        'Rogue' => array(
                'Nightslayer Cover',
                'Nightslayer Shoulder Pads',
                'Nightslayer Chestpiece',
                'Nightslayer Pants',
                'Nightslayer Boots',
                'Nightslayer Gloves',
                'Nightslayer Bracelets',
                'Nightslayer Belt',
        ),
        'Druid' => array(
                'Cenarion Helm',
                'Cenarion Spaulders',
                'Cenarion Vestments',
                'Cenarion Leggings',
                'Cenarion Boots',
                'Cenarion Gloves',
                'Cenarion Bracers',
                'Cenarion Belt',
        )
);


$setitems_Tier2 = array(
        'Warrior' => array(
                'Helm of Wrath',
                'Pauldrons of Wrath',
                'Breastplate of Wrath',
                'Legplates of Wrath',
                'Sabatons of Wrath',
                'Gauntlets of Wrath',
                'Bracelets of Wrath',
                'Waistband of Wrath',


        ),
        'Paladin' => array(

                'Judgement Crown',
                'Judgement Spaulders',
                'Judgement Breastplate',
                'Judgement Legplates',
                'Judgement Sabatons',
                'Judgement Gauntlets',
                'Judgement Bindings',
                'Judgement Belt',
                
                
        ),
        'Shaman' => array(
                'Helmet of Ten Storms',
                'Epaulets of Ten Storms',
                'Breastplate of Ten Storms',
                'Legplates of Ten Storms',
                'Greaves of Ten Storms',
                'Gauntlets of Ten Storms',
                'Bracers of Ten Storms',
                'Belt of Ten Storms',
                
                
        ),
        'Hunter' => array(
                'Dragonstalker\'s Helm',
                'Dragonstalker\'s Spaulders',
                'Dragonstalker\'s Breastplate',
                'Dragonstalker\'s Legguards',
                'Dragonstalker\'s Greaves',
                'Dragonstalker\'s Gauntlets',
                'Dragonstalker\'s Bracers',
                'Dragonstalker\'s Belt',

        ),
        'Warlock' => array(

                'Nemesis Skullcap',
                'Nemesis Spaulders',
                'Nemesis Robes',
                'Nemesis Leggings',
                'Nemesis Boots',
                'Nemesis Gloves',
                'Nemesis Bracers',
                'Nemesis Belt',
        ),
        'Priest' => array(
                'Halo of Transcendence',
                'Pauldrons of Transcendence',
                'Robes of Transcendence',
                'Leggings of Transcendence',
                'Boots of Transcendence',
                'Handguards of Transcendence',
                'Bindings of Transcendence',
                'Belt of Transcendence',
        ),
        'Mage' => array(    
                'Netherwind Crown',
                'Netherwind Mantle',
                'Netherwind Robes',
                'Netherwind Pants',                        
                'Netherwind Boots',
                'Netherwind Gloves',
                'Netherwind Bindings',
                'Netherwind Belt',
        ),
        'Rogue' => array(
                'Bloodfang Hood',
                'Bloodfang Spaulders',
                'Bloodfang Chestpiece',
                'Bloodfang Pants',
                'Bloodfang Boots',
                'Bloodfang Gloves',
                'Bloodfang Bracers',
                'Bloodfang Belt',

        ),
        'Druid' => array(
                'Stormrage Cover',
                'Stormrage Pauldrons',
                'Stormrage Chestguard',
                'Stormrage Legguards',
                'Stormrage Boots',
                'Stormrage Handguards',
                'Stormrage Bracers',
                'Stormrage Belt',
        )
);

$setitems_TierAQ = array(
        'Warrior' => array(
                'Vek\'nilash\'s Circlet'      => 'Conqueror\'s Crown',
                'Qiraji Bindings of Command'  => 'Conqueror\'s Spaulders',
                'Carapace of the Old God'     => 'Conqueror\'s Breastplate',
                'Ouro\'s Intact Hide'         => 'Conqueror\'s Legguards',
                'Conqueror\'s Greaves'  			=> 'Conqueror\'s Greaves',
        ),
        'Paladin' => array(
                'Vek\'lor\'s Diadem'          => 'Avenger\'s Crown',
                'Qiraji Bindings of Dominance'=> 'Avenger\'s Pauldrons',
                'Carapace of the Old God'     => 'Avenger\'s Breastplate',
                'Skin of the Great Sandworm'  => 'Avenger\'s Legguards',
                'Avenger\'s Greaves'					=> 'Avenger\'s Greaves',
        ),
        'Shaman' => array(
                'Vek\'lor\'s Diadem'          => 'Stormcaller\'s Diadem',
                'Qiraji Bindings of Dominance'=> 'Stormcaller\'s Pauldrons',
                'Carapace of the Old God'     => 'Stormcaller\'s Hauberk',
                'Skin of the Great Sandworm'  => 'Stormcaller\'s Leggings',      
                'Stormcaller\'s Footguards'		=> 'Stormcaller\'s Footguards',
        ),
        'Hunter' => array(
                'Vek\'lor\'s Diadem'          => 'Striker\'s Diadem',
                'Qiraji Bindings of Command'  => 'Striker\'s Pauldrons',
                'Carapace of the Old God'     => 'Striker\'s Hauberk',
                'Skin of the Great Sandworm'  => 'Striker\'s Leggings',
                'Striker\'s Footguards'  			=> 'Striker\'s Footguards',
        ),
        'Warlock' => array(
                'Vek\'nilash\'s Circlet'      => 'Doomcaller\'s Circlet',
                'Qiraji Bindings of Dominance'=> 'Doomcaller\'s Mantle',
                'Husk of the Old God'         => 'Doomcaller\'s Robes',
                'Skin of the Great Sandworm'  => 'Doomcaller\'s Trousers',
                'Doomcaller\'s Footwraps'			=> 'Doomcaller\'s Footwraps',
        ),
        'Priest' => array(
                'Vek\'nilash\'s Circlet'      => 'Tiara of the Oracle',
                'Qiraji Bindings of Command'  => 'Mantle of the Oracle',
                'Husk of the Old God'         => 'Vestments of the Oracle',
                'Ouro\'s Intact Hide'         => 'Trousers of the Oracle',
                'Footwraps of the Oracle'  		=> 'Footwraps of the Oracle',
        ),
        'Mage' => array(
                'Vek\'nilash\'s Circlet'      => 'Enigma Circlet',
                'Qiraji Bindings of Dominance'=> 'Enigma Shoulderpads',
                'Husk of the Old God'         => 'Enigma Robes',
                'Ouro\'s Intact Hide'         => 'Enigma Leggings',
                'Enigma Boots'								=> 'Enigma Boots',                                
        ),
        'Rogue' => array(
                'Vek\'lor\'s Diadem'          => 'Deathdealer\'s Helm',
                'Qiraji Bindings of Command'  => 'Deathdealer\'s Spaulders',
                'Carapace of the Old God'     => 'Deathdealer\'s Vest',
                'Ouro\'s Intact Hide'         => 'Deathdealer\'s Leggings',
                'Deathdealer\'s Boots'  			=> 'Deathdealer\'s Boots',
        ),
        'Druid' => array(
                'Vek\'lor\'s Diadem'          => 'Genesis Helm',
                'Qiraji Bindings of Dominance'=> 'Genesis Shoulderpads',
                'Husk of the Old God'         => 'Genesis Vest',
                'Skin of the Great Sandworm'  => 'Genesis Trousers',
                'Genesis Boots'								=> 'Genesis Boots',
        )
);


$setitems_Tier3 = array(
        'Warrior' => array(
                'Desecrated Helmet'           => 'Dreadnaught Helmet',
                'Desecrated Pauldrons'        => 'Dreadnaught Pauldrons',
                'Desecrated Breastplate'      => 'Dreadnaught Breastplate',
                'Desecrated Legplates'        => 'Dreadnaught Legplates',
                'Desecrated Sabatons'         => 'Dreadnaught Sabatons',
                'Desecrated Gauntlets'        => 'Dreadnaught Gauntlets',
                'Desecrated Bracers'          => 'Dreadnaught Bracers',
                'Desecrated Waistguard'       => 'Dreadnaught Waistguard',
                'Ring of the Dreadnaught'     => 'Ring of the Dreadnaught',
        ),
        'Paladin' => array(
                'Desecrated Headpiece'        => 'Redemption Headpiece',
                'Desecrated Spaulders'        => 'Redemption Spaulders',
                'Desecrated Tunic'            => 'Redemption Tunic',
                'Desecrated Legguards'        => 'Redemption Legguards',
                'Desecrated Boots'            => 'Redemption Boots',
                'Desecrated Handguards'       => 'Redemption Handguards',
                'Desecrated Wristguards'      => 'Redemption Wristguards',
                'Desecrated Girdle'           => 'Redemption Girdle',
                'Ring of Redemption'          => 'Ring of Redemption',
        ),
        'Shaman' => array(
                'Desecrated Headpiece'        => 'Earthshatter Headpiece',
                'Desecrated Spaulders'        => 'Earthshatter Spaulders',
                'Desecrated Tunic'            => 'Earthshatter Tunic',
                'Desecrated Legguards'        => 'Earthshatter Legguards',
                'Desecrated Boots'            => 'Earthshatter Boots',
                'Desecrated Handguards'       => 'Earthshatter Handguards',
                'Desecrated Wristguards'      => 'Earthshatter Wristguards',
                'Desecrated Girdle'           => 'Earthshatter Girdle',
                'Ring of the Earthshatter'    => 'Ring of the Earthshatter',
        ),
        'Hunter' => array(
                'Desecrated Headpiece'        => 'Cryptstalker Headpiece',
                'Desecrated Spaulders'        => 'Cryptstalker Spaulders',
                'Desecrated Tunic'            => 'Cryptstalker Tunic',
                'Desecrated Legguards'        => 'Cryptstalker Legguards',
                'Desecrated Boots'            => 'Cryptstalker Boots',
                'Desecrated Handguards'       => 'Cryptstalker Handguards',
                'Desecrated Wristguards'      => 'Cryptstalker Wristguards',
                'Desecrated Girdle'           => 'Cryptstalker Girdle',
                'Ring of the Cryptstalker'    => 'Ring of the Cryptstalker',
        ),
        'Warlock' => array(
                'Desecrated Circlet'          => 'Plagueheart Circlet',
                'Desecrated Shoulderpads'     => 'Plagueheart Shoulderpads',
                'Desecrated Robe'             => 'Plagueheart Robes',
                'Desecrated Leggings'         => 'Plagueheart Leggings',
                'Desecrated Sandals'          => 'Plagueheart Sandals',
                'Desecrated Gloves'           => 'Plagueheart Gloves',
                'Desecrated Bindings'         => 'Plagueheart Bindings',
                'Desecrated Belt'             => 'Plagueheart Belt',
                'Plagueheart Ring'            => 'Plagueheart Ring',
        ),
        'Priest' => array(
                'Desecrated Circlet'          => 'Circlet of Faith',
                'Desecrated Shoulderpads'     => 'Shoulderpads of Faith',
                'Desecrated Robe'             => 'Robe of Faith',
                'Desecrated Leggings'         => 'Leggings of Faith',
                'Desecrated Sandals'          => 'Sandals of Faith',
                'Desecrated Gloves'           => 'Gloves of Faith',
                'Desecrated Bindings'         => 'Bindings of Faith',
                'Desecrated Belt'             => 'Belt of Faith',
                'Ring of Faith'               => 'Ring of Faith',
        ),
        'Mage' => array(
                'Desecrated Circlet'          => 'Frostfire Circlet',
                'Desecrated Shoulderpads'     => 'Frostfire Shoulderpads',
                'Desecrated Robe'             => 'Frostfire Robe',
                'Desecrated Leggings'         => 'Frostfire Leggings',
                'Desecrated Sandals'          => 'Frostfire Sandals',
                'Desecrated Gloves'           => 'Frostfire Gloves',
                'Desecrated Bindings'         => 'Frostfire Bindings',
                'Desecrated Belt'             => 'Frostfire Belt',
                'Frostfire Ring'              => 'Frostfire Ring',
        ),
        'Rogue' => array(
                'Desecrated Helmet'           => 'Bonescythe Helmet',
                'Desecrated Pauldrons'        => 'Bonescythe Pauldrons',
                'Desecrated Breastplate'      => 'Bonescythe Breastplate',
                'Desecrated Legplates'        => 'Bonescythe Legplates',
                'Desecrated Sabatons'         => 'Bonescythe Sabatons',
                'Desecrated Gauntlets'        => 'Bonescythe Gauntlets',
                'Desecrated Bracers'          => 'Bonescythe Bracers',
                'Desecrated Waistguard'       => 'Bonescythe Waistguard',
                'Bonescythe Ring'             => 'Bonescythe Ring',
        ),
        'Druid' => array(
                'Desecrated Headpiece'        => 'Dreamwalker Headpiece',
                'Desecrated Spaulders'        => 'Dreamwalker Spaulders',
                'Desecrated Tunic'            => 'Dreamwalker Tunic',
                'Desecrated Legguards'        => 'Dreamwalker Legguards',
                'Desecrated Boots'            => 'Dreamwalker Boots',
                'Desecrated Handguards'       => 'Dreamwalker Handguards',
                'Desecrated Wristguards'      => 'Dreamwalker Wristguards',
                'Desecrated Girdle'           => 'Dreamwalker Girdle',
                'Ring of the Dreamwalker'     => 'Ring of the Dreamwalker',
        )
);

$tier_names = array(
        'Warrior' => array(
                'Tier1' => 'Battlegear of Might',
                'Tier2' => 'Battlegear of Wrath',
                'TierAQ' => 'Conqueror\'s Battlegear',
                'Tier3' => 'Dreadnaught\'s Battlegear',
        ),
        'Paladin' => array(
                'Tier1' => 'Lawbringer Armor',
                'Tier2' => 'Judgement Armor',
                'TierAQ' => 'Avenger\'s Battlegear',
                'Tier3' => 'Redemption Armor',
        ),
        'Hunter' => array(
                'Tier1' => 'Giantstalker Armor',
                'Tier2' => 'Dragonstalker Armor',
                'TierAQ' => 'Striker\'s Garb',
                'Tier3' => 'Cryptstalker Armor',
        ),
        'Warlock' => array(
                'Tier1' => 'Felheart Raiment',
                'Tier2' => 'Nemesis Raiment',
                'TierAQ' => 'Doomcaller\'s Attire',
                'Tier3' => 'Plagueheart Raiment',
        ),
        'Priest' => array(
                'Tier1' => 'Vestments of Prophecy',
                'Tier2' => 'Vestments of Transcendence',
                'TierAQ' => 'Garments of the Oracle',
                'Tier3' => 'Vestments of Faith',
        ),
        'Mage' => array(
                'Tier1' => 'Arcanist Regalia',
                'Tier2' => 'Netherwind Regalia',
                'TierAQ' => 'Enigma Vestments',
                'Tier3' => 'Frostfire Regalia',
        ),
        'Rogue' => array(
                'Tier1' => 'Nightslayer Armor',
                'Tier2' => 'Bloodfang Armor',
                'TierAQ' => 'Deathdealer\'s Embrace',
                'Tier3' => 'Bonescythe Armor',
        ),
        'Shaman' => array(
                'Tier1' => 'The Earthfury',
                'Tier2' => 'The Ten Storms',
                'TierAQ' => 'Stormcaller\'s Garb',
                'Tier3' => 'The Earthshatterer',
        ),
        'Druid' => array(
                'Tier1' => 'Cenarion Raiment',
                'Tier2' => 'Stormrage Raiment',
                'TierAQ' => 'Genesis Raiment',
                'Tier3' => 'Dreamwalker Raiment',
        )
);

?>