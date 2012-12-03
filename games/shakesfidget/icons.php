<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date: 2009-01-14 12:12:58 +0100 (Mi, 14 Jan 2009) $
 * -----------------------------------------------------------------------
 * @author      $Author: wallenium $
 * @copyright   2006-2008 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev: 3575 $
 * 
 * $Id: icons.php 3575 2009-01-14 11:12:58Z wallenium $
 */
 
if ( !defined('EQDKP_INC') )
{
    header('HTTP/1.0 404 Not Found');
    exit;
}

$game_icons = array(
  '3dmodel'     => false,
  'class'       => true,
  'class_b'     => true,
  'event'       => false,
  'races'       => true,
  'rank'        => false,
  'filter'	=> false
);

?>
