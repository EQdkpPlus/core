<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2011
 * Date:        $Date: 2011-01-26 01:06:10 +0100 (Wed, 26 Jan 2011) $
 * -----------------------------------------------------------------------
 * @author      $Author: wallenium $
 * @copyright   2006-2008 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev: 9627 $
 * 
 * $Id: convertion.php 9627 2011-01-26 00:06:10Z wallenium $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

// Convert the Classnames to english
$classconvert_array = array(
	'german'  => array(
		'Sturmtruppe'		=> 'Trooper',
		'Schmuggler'		=> 'Smuggler',
		'Jedi Konsular'		=> 'Jedi Consular',
		'Jedi Ritter'		=> 'Jedi Knight',
		'Kopfgeldjäger'		=> 'Bounty Hunter',
		'Imperialer Agent'	=> 'Imperial Agent',
		'Sith Inquisitor'	=> 'Sith Inquisitor',
		'Sith Krieger'		=> 'Sith Warrior',
	)
);

?>