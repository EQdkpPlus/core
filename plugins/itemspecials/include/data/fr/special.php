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

$trinket_items = array(
				'Paladin' 		=> 'Parchemin de lumière aveuglante',
				'Shaman' 			=> 'Cristal d\'alignement sur la nature',
				'Warrior' 		=> 'Gemme donneuse de vie',
				'Rogue' 			=> 'Totem venimeux',
				'Hunter' 			=> 'Gemme imprégnée d\'arcanes',
				'Warlock' 		=> 'Le Livre noir',
				'Druid' 			=> 'Rune de transformation',
				'Mage' 				=> 'Gemme de vivacité d\'esprit',
				'Priest' 			=> 'Egide de préservation'
);

$aqbook_items = array(
			'Paladin' 			=> array('Libram : Bénédiction de puissance VII','Libram : Bénédiction de sagesse VI','Libram : Lumière sacrée IX'),
			'Shaman' 				=> array('Tablette de Totem de grâce aérienne III','Tablette de Vague de soins X','Tablette de Totem de force de la terre V'),
			'Warrior' 			=> array('Manuel de Cri de guerre VII','Manuel de Frappe héroïque IX','Manuel de Vengeance VI'),
			'Rogue' 				=> array('Recueil : Attaque sournoise IX','Recueil : Poison mortel V','Recueil : Feinte V'),
			'Hunter' 				=> array('Guide : Aspect du faucon VII','Guide : Flèches multiples V','Guide : Morsure du serpent IX'),
			'Warlock' 			=> array('Grimoire de Corruption VII','Grimoire d\'immolation VIII','Grimoire de Trait de l\'ombre X'),
			'Druid' 				=> array('Livre de Récupération XI','Livre de Toucher guérisseur XI','Livre de Feu stellaire VII'),
			'Mage' 					=> array('Tome d\'Eclair de givre XI','Tome de Boule de feu XII','Tome de Projectile des arcanes VIII'),
			'Priest' 				=> array('Codex de Soins supérieurs V','Codex de Prière de soins V','Codex de Rénovation X')
);

$mount_items = array(
				'Blue' 				=> 'Cristal de résonance Qiraji Bleu',
				'Yellow'			=> 'Cristal de résonance Qiraji jaune',
				'Green' 			=> 'Cristal de résonance Qiraji vert',
				'Red' 				=> 'Cristal de résonance Qiraji rouge',
				'Black' 			=> 'Cristal de résonance Qiraji noir'
);

$atiesh_items = array(
        'Eclat d\'Atiesh',
        'Esprit d\'Atiesh',
			  'Toque d\'Atiesh',
			  'Base d\'Atiesh',
			  'Atiesh, Grand bâton du gardien'
);
?>