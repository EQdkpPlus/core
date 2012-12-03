<?php
/******************************
 * EQdkp ItemSpecials Plugin
 * (c) 2006 by WalleniuM [Simon Wallmann]
 * http://www.wallenium.de   
 * ------------------
 * special.php
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

$trinket_items = array(
        'Paladin'      => 'Rollen des Blendenden Lichts',
        'Shaman'       => 'Kristall der Naturverbundenheit',
			  'Warrior'      => 'Lebensspendender Edelstein',
			  'Rogue'        => 'Gifttotem',
			  'Hunter'       => 'Edelstein der arkanen Kraft',
			  'Warlock'      => 'Das schwarze Buch',
			  'Druid'        => 'Rune der Metamorphose',
			  'Mage'         => 'Edelstein des Gedankensprungs',
			  'Priest'       => 'Aegis der Bewahrung'
);

$aqbook_items = array(
        'Paladin'      => array('Buchband: Segen der Macht VII','Buchband: Segen der Weisheit VI','Buchband: Heiliges Licht IX'),
        'Shaman'       => array('Schrifttafel des Totems der luftgleichen Anmut III','Schrifttafel der Welle der Heilung X','Schrifttafel des Totems der Erdstärke V'),
			  'Warrior'      => array('Handbuch des Schlachtrufs VII','Handbuch des heldenhaften Stoßes IX','Handbuch der Rache VI'),
			  'Rogue'        => array('Leitfaden des Meuchelns IX','Leitfaden des tödlichen Gifts V','Leitfaden der Finte V'),
			  'Hunter'       => array('Lehrbuch: Aspekt des Falken VII','Lehrbuch: Mehrfachschuss V','Lehrbuch: Schlangenbiss IX'),
			  'Warlock'      => array('Zauberfoliant der Verderbnis VII','Zauberfoliant des Feuerbrandes VIII','Zauberfoliant des Schattenblitzes X'),
			  'Druid'        => array('Buch der Heilenden Berührung XI','Buch der Verjüngung XI','Buch des Sternenfeuers VII'),
			  'Mage'         => array('Foliant des Frostblitzes XI','Foliant des Feuerballs XII','Foliant der Arkanen Geschosse VIII'),
			  'Priest'       => array('Kodex der großen Heilung V','Kodex des Gebets der Heilung V','Kodex der Erneuerung X')
);

$mount_items = array(
        'Blue'         => 'Blauer Qirajiresonanzkristall',
        'Yellow'       => 'Gelber Qirajiresonanzkristall',
			  'Green'        => 'Grüner Qirajiresonanzkristall',
			  'Red'          => 'Roter Qirajiresonanzkristall',
			  'Black'        => 'Schwarzer Qirajiresonanzkristall'
);

$atiesh_items = array(
        'Bruchstück von Atiesh',
        'Grundstab von Atiesh',
			  'Stabkopf von Atiesh',
			  'Stabfuß von Atiesh',
			  'Atiesh, Hohestab des Wächters'
);
?>