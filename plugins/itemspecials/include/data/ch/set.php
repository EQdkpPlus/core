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
				'力量头盔',
				'力量肩铠',
				'力量胸甲',
				'力量护腕',
				'力量护手',
				'力量腰带',
				'力量腿铠',
				'力量马靴'	
        ),
        'Paladin' => array(
				'秩序之源头盔',
				'秩序之源肩铠',
				'秩序之源胸甲',
				'秩序之源护腕',
				'秩序之源护手',
				'秩序之源腰带',
				'秩序之源腿铠',
				'秩序之源战靴'
                            
                
        ),
        'Shaman' => array(
				'大地之怒头盔',
				'大地之怒肩饰',
				'大地之怒外衣',
				'大地之怒护腕',
				'大地之怒护手',
				'大地之怒腰带',
				'大地之怒腿甲',
				'大地之怒长靴'
        ),
        'Hunter' => array(
				'巨人追踪者头盔',
				'巨人追踪者肩饰',
				'巨人追踪者胸甲',
				'巨人追踪者护腕',
				'巨人追踪者手套',
				'巨人追踪者腰带',
				'巨人追踪者护腿',
				'巨人追踪者长靴'
        ),
        'Warlock' => array(
				'恶魔之心角饰',
				'恶魔之心护肩',
				'恶魔之心长袍',
				'恶魔之心护腕',
				'恶魔之心手套',
				'恶魔之心腰带',
				'恶魔之心短裤',
				'恶魔之心便鞋'
        ),
        'Priest' => array(
				'预言头饰',
				'预言衬肩',
				'预言法袍',
				'预言臂甲',
				'预言手套',
				'预言束带',
				'预言短裤',
				'预言之靴'
        ),
        'Mage' => array(
				'奥术师头冠',
				'奥术师衬肩',
				'奥术师长袍',
				'奥术师护腕',
				'奥术师手套',
				'奥术师腰带',
				'奥术师护腿',
				'奥术师便鞋'
        ),
        'Rogue' => array(
				'夜幕杀手头巾',
				'夜幕杀手护肩',
				'夜幕杀手胸甲',
				'夜幕杀手护腕',
				'夜幕杀手手套',
				'夜幕杀手腰带',
				'夜幕杀手短裤',
				'夜幕杀手长靴'
        ),
        'Druid' => array(
				'塞纳里奥头盔',
				'塞纳里奥肩甲',
				'塞纳里奥胸甲',
				'塞纳里奥护腕',
				'塞纳里奥手套',
				'塞纳里奥腰带',
				'塞纳里奥护腿',
				'塞纳里奥长靴'
        )
);


$setitems_Tier2 = array(
        'Warrior' => array(
				'愤怒头盔',
				'愤怒肩铠',
				'愤怒胸甲',
				'愤怒护腕',
				'愤怒护手',
				'愤怒腰带',
				'愤怒腿铠',
				'愤怒马靴'


        ),
        'Paladin' => array(

				'审判头冠',
				'审判肩铠',
				'审判胸甲',
				'审判束腕',
				'审判护手',
				'审判腰带',
				'审判腿铠',
				'审判马靴'
                
                
        ),
        'Shaman' => array(
				'无尽风暴头盔',
				'无尽风暴肩饰',
				'无尽风暴胸甲',
				'无尽风暴护腕',
				'无尽风暴护手',
				'无尽风暴腰带',	
				'无尽风暴护腿',
				'无尽风暴胫甲'
                
                
        ),
        'Hunter' => array(
				'巨龙追踪者头盔',
				'巨龙追踪者肩甲',
				'巨龙追踪者胸甲',
				'巨龙追踪者护腕',
				'巨龙追踪者护手',
				'巨龙追踪者腰带',
				'巨龙追踪者腿甲',
				'巨龙追踪者胫甲'

        ),
        'Warlock' => array(

				'复仇骨帽',
				'复仇肩铠',
				'复仇法袍',
				'复仇护腕',
				'复仇手套',
				'复仇腰带',
				'复仇护腿',
				'复仇战靴'
        ),
        'Priest' => array(
				'卓越之环',
				'卓越肩铠',
				'卓越法袍',
				'卓越束腕',
				'卓越护手',
				'卓越腰带',
				'卓越护腿',
				'卓越长靴'
        ),
        'Mage' => array(    
				'灵风头冠',
				'灵风衬肩',
				'灵风长袍',
				'灵风束腕',
				'灵风手套',
				'灵风腰带',
				'灵风短裤',
				'灵风长靴'
        ),
        'Rogue' => array(
				'血牙头巾',
				'血牙肩甲',
				'血牙胸甲',
				'血牙护腕',
				'血牙手套',
				'血牙腰带',
				'血牙短裤',
				'血牙长靴'

        ),
        'Druid' => array(
				'怒风头巾',
				'怒风肩甲',
				'怒风胸甲',
				'怒风护腕',
				'怒风护手',
				'怒风腰带',
				'怒风腿甲',
				'怒风长靴'
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