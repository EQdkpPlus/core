<?php
/******************************
 * EQdkp ItemSpecials Plugin
 * (c) 2006 by WalleniuM [Simon Wallmann]
 * http://www.wallenium.de   
 * ------------------
 * setitems.php
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
                'Helm der Macht',
                'Schulterstücke der Macht',
                'Brustplatte der Macht',
                'Beinplatten der Macht',
                'Sabatons der Macht',
                'Stulpen der Macht',
                'Armschienen der Macht',
                'Gürtel der Macht',

        ),
        'Paladin' => array(
                'Helm der Gerechtigkeit',
                'Schiftung der Gerechtigkeit',
                'Brustschutz der Gerechtigkeit',
                'Beinplatten der Gerechtigkeit',
                'Stiefel der Gerechtigkeit',
                'Stulpen der Gerechtigkeit',
                'Armschienen der Gerechtigkeit',
                'Gürtel der Gerechtigkeit',

        ),
         'Shaman' => array(
                'Helm der Erdenwut',
                'Schulterklappen der Erdenwut',
                'Gewand der Erdenwut',
                'Beinschützer der Erdenwut',
                'Stiefel der Erdenwut',
                'Stulpen der Erdenwut',
                'Armschienen der Erdenwut',
                'Gürtel der Erdenwut',
 
        ),
        'Hunter' => array(
                'Helm des Riesenjägers',
                'Schulterklappen des Riesenjägers',
                'Brustplatte des Riesenjägers',
                'Gamaschen des Riesenjägers',
                'Stiefel des Riesenjägers',
                'Handschuhe des Riesenjägers',
                'Armschienen des Riesenjägers',
                'Gürtel des Riesenjägers',

        ),
        'Warlock' => array(
                'Teufelsherzhörner',
                'Teufelsherzschulterpolster',
                'Teufelsherzroben',
                'Teufelsherzhose',
                'Teufelsherzschuhe',
                'Teufelsherzhandschuhe',
                'Teufelsherzarmschienen',
                'Teufelsherzgürtel',

        ),
        'Priest' => array(
                'Reif der Prophezeiung',
                'Mantel der Prophezeiung',
                'Roben der Prophezeiung',
                'Hose der Prophezeiung',
                'Stiefel der Prophezeiung',
                'Handschuhe der Prophezeiung',
                'Unterarmschienen der Prophezeiung',
                'Gurt der Prophezeiung',

        ),
        'Mage' => array(
                'Krone des Arkanisten',
                'Mantel des Arkanisten',
                'Roben des Arkanisten',
                'Gamaschen des Arkanisten',
                'Stiefel des Arkanisten',
                'Handschuhe des Arkanisten',
                'Bindungen des Arkanisten',
                'Gürtel des Arkanisten',

                
        ),
        'Rogue' => array(
                'Kopfschutz des Nachtmeuchlers',
                'Schulterklappen des Nachtmeuchlers',
                'Brustharnisch des Nachtmeuchlers',
                'Hose des Nachtmeuchlers',
                'Stiefel des Nachtmeuchlers',
                'Handschuhe des Nachtmeuchlers',
                'Armreifen des Nachtmeuchlers',
                'Gürtel des Nachtmeuchlers',
 
        ),
        'Druid' => array(
                'Helm des Cenarius',
                'Schiftung des Cenarius',
                'Gewand des Cenarius',
                'Gamaschen des Cenarius',
                'Stiefel des Cenarius',
                'Handschuhe des Cenarius',
                'Armschienen des Cenarius',
                'Gürtel des Cenarius',

        )
);
$setitems_Tier2 = array(
        'Warrior' => array(
                'Helm des Zorns',
                'Schulterstücke des Zorns',
                'Brustplatte des Zorns',
                'Beinplatten des Zorns',  
                'Sabatons des Zorns',
                'Stulpen des Zorns',
                'Armreifen des Zorns',
                'Gürtelbund des Zorns',
        ),
        'Paladin' => array(
                'Krone des Richturteils',
                'Schiftung des Richturteils',
                'Brustplatte des Richturteils',
                'Beinplatten des Richturteils',
                'Sabatons des Richturteils',
                'Stulpen des Richturteils',
                'Bindungen des Richturteils',
                'Gürtel des Richturteils',  
        ),
        'Shaman' => array(
                'Helm der zehn Stürme',
                'Schulterklappen der zehn Stürme',
                'Brustplatte der zehn Stürme',
                'Beinplatten der zehn Stürme',
                'Schienbeinschützer der zehn Stürme',
                'Stulpen der zehn Stürme',
                'Armschienen der zehn Stürme',
                'Gürtel der zehn Stürme',  
        ),
        'Hunter' => array(
                'Helm des Drachenjägers',
                'Schiftung des Drachenjägers',
                'Brustplatte des Drachenjägers',
                'Beinschützer des Drachenjägers',
                'Schienbeinschützer des Drachenjägers',
                'Stulpen des Drachenjägers',
                'Armschienen des Drachenjägers',
                'Gürtel des Drachenjägers',
        ),
        'Warlock' => array(
                'Schädelkappe der Nemesis',
                'Schiftung der Nemesis',
                'Roben der Nemesis',
                'Gamaschen der Nemesis',
                'Stiefel der Nemesis',
                'Handschuhe der Nemesis',
                'Armschienen der Nemesis',
                'Gürtel der Nemesis',
        ),
        'Priest' => array(
                'Heiligenschein der Erhabenheit',
                'Schulterstücke der Erhabenheit',
                'Roben der Erhabenheit',
                'Gamaschen der Erhabenheit',
                'Stiefel der Erhabenheit',
                'Handschützer der Erhabenheit',
                'Bindungen der Erhabenheit',
                'Gürtel der Erhabenheit',
        ),
        'Mage' => array(
                'Krone des Netherwinds',
                'Mantel des Netherwinds',
                'Roben des Netherwinds',
                'Hose des Netherwinds',
                'Stiefel des Netherwinds',
                'Handschuhe des Netherwinds',
                'Bindungen des Netherwinds',
                'Gürtel des Netherwinds',
                
        ),
        'Rogue' => array(
                'Blutfangkapuze',
                'Blutfangschiftung',
                'Blutfangbrustharnisch',
                'Blutfanghose',
                'Blutfangstiefel',
                'Blutfanghandschuhe',
                'Blutfangarmschienen',
                'Blutfanggürtel',
        ),
        'Druid' => array(
                'Sturmgrimms Bedeckung',
                'Sturmgrimms Schulterstücke',
                'Sturmgrimms Brustschutz',
                'Sturmgrimms Beinschützer',
                'Sturmgrimms Stiefel',
                'Sturmgrimms Handschützer',
                'Sturmgrimms Armschienen',
                'Sturmgrimms Gürtel',
        )
);

$setitems_TierAQ = array(
        'Warrior' => array(
                "Vek\'nilashs Reif"           => 'Krone des Eroberers',
                'Befehlsbindungen der Qiraji' => 'Schiftung des Eroberers',
                'Knochenpanzer des alten Gottes'=> 'Brustplatte des Eroberers',
		            'Ouros intakte Haut'          => 'Beinschützer des Eroberers',
                'Schienbeinschützer des Eroberers' => 'Schienbeinschützer des Eroberers',
),
        'Paladin' => array(
                'Vek\'lors Diadem'            => 'Krone des Rächers',
                'Dominanzbindungen der Qiraji'=> 'Schulterstücke des Rächers',
                'Knochenpanzer des alten Gottes'=> 'Brustplatte des Rächers',
                'Haut des großen Sandwurms'   => 'Beinschützer des Rächers',
                'Schienbeinschützer des Rächers'=> 'Schienbeinschützer des Rächers',
        ),

        'Shaman' => array(
                'Vek\'lors Diadem'            => 'Diadem des Sturmrufers',
                'Dominanzbindungen der Qiraji'=> 'Schulterstücke des Sturmrufers',
                'Knochenpanzer des alten Gottes'=> 'Halsberge des Sturmrufers',
                'Haut des großen Sandwurms'   => 'Gamaschen des Sturmrufers',      
                'Fußschützer des Sturmrufers'=> 'Fußschützer des Sturmrufers',
        ),
        'Hunter' => array(
                'Vek\'lors Diadem'            => 'Diadem des Hetzers',
                'Befehlsbindungen der Qiraji' => 'Schulterstücke des Hetzers',
                'Knochenpanzer des alten Gottes'=> 'Halsberge des Hetzers',
                'Haut des großen Sandwurms'   => 'Gamaschen des Hetzers',
                'Fußschützer des Hetzers' 		=> 'Fußschützer des Hetzers',
        ),
        'Warlock' => array(
                'Vek\'nilashs Reif'           => 'Reif des Verdammnisrufers',
                'Dominanzbindungen der Qiraji'=> 'Mantel des Verdammnisrufers',
                'Hülle des alten Gottes'      => 'Roben des Verdammnisrufers',
                'Haut des großen Sandwurms'   => 'Beinkleider des Verdammnisrufers',
                'Fußlappen des Verdammnisrufers'=> 'Fußlappen des Verdammnisrufers',
        ),
        'Priest' => array(
                'Vek\'nilashs Reif'           => 'Tiara des Orakels',
                'Befehlsbindungen der Qiraji' => 'Mantel des Orakels',
                'Hülle des alten Gottes'      => 'Tunika des Orakels',
                'Ouros intakte Haut'          => 'Beinkleider des Orakels',
                'Fußlappen des Orakels' 			=> 'Fußlappen des Orakels',

        ),
        'Mage' => array(
                'Vek\'nilashs Reif'           => 'Reif des Mysteriums',
                'Dominanzbindungen der Qiraji'=> 'Schulterpolster des Mysteriums',
                'Hülle des alten Gottes'      => 'Roben des Mysteriums',
                'Ouros intakte Haut'          => 'Gamaschen des Mysteriums',
                'Stiefel des Mysteriums'			=> 'Stiefel des Mysteriums',                                
        ),
        'Rogue' => array(
                'Vek\'lors Diadem'            => 'Helm des Todesboten',
                'Befehlsbindungen der Qiraji' => 'Schiftung des Todesboten',
                'Knochenpanzer des alten Gottes'=> 'Weste des Todesboten',
                'Ouros intakte Haut'          => 'Gamaschen des Todesboten',
                'Stiefel des Todesboten' 			=> 'Stiefel des Todesboten',
        ),
        'Druid' => array(
                'Vek\'lors Diadem'            => 'Helm der Genesis',
                'Dominanzbindungen der Qiraji'=> 'Schulterpolster der Genesis',
                'Hülle des alten Gottes'      => 'Weste der Genesis',
                'Haut des großen Sandwurms'   => 'Beinkleider der Genesis',
                'Stiefel der Genesis'					=> 'Stiefel der Genesis',
        )
);

$setitems_Tier3 = array(
        'Warrior' => array(
                'Entweihter Helm'             => 'Helm des Schreckenspanzers',
                'Entweihte Schulterstücke'    => 'Schulterstücke des Schreckenspanzers',
                'Entweihte Brustplatte'       => 'Brustplatte des Schreckenspanzers',
                'Entweihte Beinplatten'       => 'Beinplatten des Schreckenspanzers',
                'Entweihte Sabatons'          => 'Sabatons des Schreckenspanzers',
                'Entweihte Stulpen'           => 'Stulpen des Schreckenspanzers',
                'Entweihte Armschienen'       => 'Armschienen des Schreckenspanzers',
                'Entweihter Taillenschutz'    => 'Taillenschutz des Schreckenspanzers',
                'Ring des Schreckenspanzers'  => 'Ring des Schreckenspanzers',
        ),
        'Paladin' => array(
                'Entweihtes Kopfstück'        => 'Kopfstück der Erlösung',
                'Entweihte Schiftung'         => 'Schiftung der Erlösung',
                'Entweihte Tunika'            => 'Tunika der Erlösung',
                'Entweihte Beinschützer'      => 'Beinschützer der Erlösung',
                'Entweihte Stiefel'           => 'Stiefel der Erlösung',
                'Entweihte Handschützer'      => 'Handschützer der Erlösung',
                'Entweihter Handgelenksschutz'=> 'Handgelenksschutz der Erlösung',
                'Entweihter Gurt'             => 'Gurt der Erlösung',
                'Ring der Erlösung'           => 'Ring der Erlösung',
        ),
        'Shaman' => array(
                'Entweihtes Kopfstück'        => 'Kopfstück des Erdspalters',
                'Entweihte Schiftung'         => 'Schiftung des Erdspalters',
                'Entweihte Tunika'            => 'Tunika des Erdspalters',
                'Entweihte Beinschützer'      => 'Beinschützer des Erdspalters',
                'Entweihte Stiefel'           => 'Stiefel des Erdspalters',
                'Entweihte Handschützer'      => 'Handschützer des Erdspalters',
                'Entweihter Handgelenksschutz'=> 'Handgelenksschutz des Erdspalters',
                'Entweihter Gurt'             => 'Gurt des Erdspalters',
                'Ring des Erdspalters'        => 'Ring des Erdspalters',
        ),
        'Hunter' => array(
                'Entweihtes Kopfstück'        => 'Kopfstück des Gruftpirschers',
                'Entweihte Schiftung'         => 'Schiftung des Gruftpirschers',
                'Entweihte Tunika'            => 'Tunika des Gruftpirschers',
                'Entweihte Beinschützer'      => 'Beinschützer des Gruftpirschers',
                'Entweihte Stiefel'           => 'Stiefel des Gruftpirschers',
                'Entweihte Handschützer'      => 'Handschützer des Gruftpirschers',
                'Entweihter Handgelenksschutz'=> 'Handgelenksschutz des Gruftpirschers',
                'Entweihter Gurt'             => 'Gurt des Gruftpirschers',
                'Ring des Gruftpirschers'     => 'Ring des Gruftpirschers',
        ),
        'Warlock' => array(
                'Entweihter Reif'             => 'Reif des verseuchten Herzens',
                'Entweihte Schulterpolster'   => 'Schulterpolster des verseuchten Herzens',
                'Entweihte Robe'              => 'Robe des verseuchten Herzens',
                'Entweihte Gamaschen'         => 'Gamaschen des verseuchten Herzens',
                'Entweihte Sandalen'          => 'Sandalen des verseuchten Herzens',
                'Entweihte Handschuhe'        => 'Handschuhe des verseuchten Herzens',
                'Entweihte Bindungen'         => 'Bindungen des verseuchten Herzens',
                'Entweihter Gürtel'           => 'Gürtel des verseuchten Herzens',
                'Ring des verseuchten Herzens'=> 'Ring des verseuchten Herzens',

        ),
        'Priest' => array(
                'Entweihter Reif'             => 'Reif des Glaubens',
                'Entweihte Schulterpolster'   => 'Schulterpolster des Glaubens',
                'Entweihte Robe'              => 'Robe des Glaubens',
                'Entweihte Gamaschen'         => 'Gamaschen des Glaubens',
                'Entweihte Sandalen'          => 'Sandalen des Glaubens',
                'Entweihte Handschuhe'        => 'Handschuhe des Glaubens',
                'Entweihte Bindungen'         => 'Bindungen des Glaubens',
                'Entweihter Gürtel'           => 'Gürtel des Glaubens',
                'Ring des Glaubens'           => 'Ring des Glaubens',

        ),
        'Mage' => array(
                'Entweihter Reif'             => 'Frostfeuerreif',
                'Entweihte Schulterpolster'   => 'Frostfeuerschulterpolster',
                'Entweihte Robe'              => 'Frostfeuerrobe',
                'Entweihte Gamaschen'         => 'Frostfeuergamaschen',
                'Entweihte Sandalen'          => 'Frostfeuersandalen',
                'Entweihte Handschuhe'        => 'Frostfeuerhandschuhe',
                'Entweihte Bindungen'         => 'Frostfeuerbindungen',
                'Entweihter Gürtel'           => 'Frostfeuergürtel',
                'Frostfeuerring'              => 'Frostfeuerring',
                
        ),
        'Rogue' => array(
                'Entweihter Helm'             => 'Helm der Knochensense',
                'Entweihte Schulterstücke'    => 'Schulterstücke der Knochensense',
                'Entweihte Brustplatte'       => 'Brustplatte der Knochensense',
                'Entweihte Beinplatten'       => 'Beinplatten der Knochensense',
                'Entweihte Sabatons'          => 'Sabatons der Knochensense',
                'Entweihte Stulpen'           => 'Stulpen der Knochensense',
                'Entweihte Armschienen'       => 'Armschienen der Knochensense',
                'Entweihter Taillenschutz'    => 'Taillenschutz der Knochensense',
                'Ring der Knochensense'       => 'Ring der Knochensense',
        ),
        'Druid' => array(
                'Entweihtes Kopfstück'        => 'Kopfstück des Traumwandlers',
                'Entweihte Schiftung'         => 'Schiftung des Traumwandlers',
                'Entweihte Tunika'            => 'Tunika des Traumwandlers',
                'Entweihte Beinschützer'      => 'Beinschützer des Traumwandlers',
                'Entweihte Stiefel'           => 'Stiefel des Traumwandlers',
                'Entweihte Handschützer'      => 'Handschützer des Traumwandlers',
                'Entweihter Handgelenksschutz'=> 'Handgelenksschutz des Traumwandlers',
                'Entweihter Gurt'             => 'Gurt des Traumwandlers',
                'Ring des Traumwandlers'      => 'Ring des Traumwandlers',
        )
);

$tier_names = array(
        'Warrior' => array(
                'Tier1' => 'Schlachtrüstung der Macht',
                'Tier2' => 'Schlachtrüstung des Zorns',
                'TierAQ' => 'Schlachtrüstung des Eroberers',
                'Tier3' => 'Schlachtrüstung des Schreckenspanzers',
        ),
        'Paladin' => array(
                'Tier1' => 'Rüstung der Gerechtigkeit',
                'Tier2' => 'Rüstung des Richturteils',
                'TierAQ' => 'Schlachtrüstung des Rächers',
                'Tier3' => 'Rüstung der Erlösung',
        ),
        'Hunter' => array(
                'Tier1' => 'Rüstung des Riesenjägers',
                'Tier2' => 'Rüstung des Drachenjägers',
                'TierAQ' => 'Gewand des Hetzers',
                'Tier3' => 'Rüstung des Gruftpirschers',
        ),
        'Warlock' => array(
                'Tier1' => 'Teufelsherzroben',
                'Tier2' => 'Roben der Nemesis',
                'TierAQ' => 'Roben des Verdammnisrufers',
                'Tier3' => 'Roben des verseuchten Herzens',
        ),
        'Priest' => array(
                'Tier1' => 'Gewänder der Prophezeiung',
                'Tier2' => 'Gewänder der Erhabenheit',
                'TierAQ' => 'Gewänder des Orakels',
                'Tier3' => 'Gewänder des Glaubens',
        ),
        'Mage' => array(
                'Tier1' => 'Ornat des Arkanisten',
                'Tier2' => 'Ornat des Netherwinds',
                'TierAQ' => 'Gewänder des Mysteriums',
                'Tier3' => 'Frostfeuerornat',
        ),
        'Rogue' => array(
                'Tier1' => 'Der Nachtmeuchler',
                'Tier2' => 'Blutfangrüstung',
                'TierAQ' => 'Umarmung des Todesboten',
                'Tier3' => 'Rüstung der Knochensense',
        ),
        'Shaman' => array(
                'Tier1' => 'Die Wut der Erde',
                'Tier2' => 'Die Zehn Stürme',
                'TierAQ' => 'Gewand des Sturmrufers',
                'Tier3' => 'Der Erdspalter',
        ),
        'Druid' => array(
                'Tier1' => 'Gewänder des Cenarius',
                'Tier2' => 'Gewänder des Stormrage',
                'TierAQ' => 'Beinkleider der Genesis',
                'Tier3' => 'Gewandung des Traumwandlers',
        )
);
?>