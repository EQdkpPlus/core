<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * DAoC.php
 * Began: Fri May 13 2005
 *
 * $Id: index.php 2206 2008-06-16 17:13:40Z osr-corgan $
 *
 ******************************/

if ( !defined('EQDKP_INC') )
{
    header('HTTP/1.0 404 Not Found');
    exit;
}

// Convert the Classnames to english
$classconvert_array = array(
  'german'  => array(
      "Schurke"           => "Burglar",
      "Hauptmann"         => "Captain",
      "Waffenmeister"     => "Champion",
      "Wächter"           => "Guardian",
      "Jäger"             => "Hunter",
      "Kundiger"          => "Lore-master",
      "Barde"             => "Minstrel",
      "Hüter"             => "Warden",
      "Runenbewahrer"     => "Runekeeper",
  ),
);

?>
