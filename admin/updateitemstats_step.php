<?php
/******************************
* EQdkp
* Copyright 2002-2007
* Licensed under the GNU GPL.  See COPYING for full terms.
* ------------------
*
* some code from Charmanager by Wallenium
*
* Began: Juni 1 2007 Corgan
*
* $Id: updateitemstats_step.php 62 2007-05-15 18:42:34Z osr-corgan $
 ******************************/

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');
include_once($eqdkp_root_path . 'itemstats/config.php');
include_once($eqdkp_root_path . 'itemstats/eqdkp_itemstats.php');
$user->check_auth('a_item_upd');

$window_close_array = array('UCCacheWindow');
$_output = "
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=".$user->lang['ENCODING']." />
".$jqueryp->Dialog_close($window_close_array , false, false, true)."
</head>";

	$step = $_GET['step']	;
	$items = array();
  $ii = 0;

	if($step=='items')
	{
		$sql = "SELECT DISTINCT item_name FROM " . ITEMS_TABLE ." order by item_name" ;
	}

  if($step=='trade')
	{
			if ($pm->check(PLUGIN_INSTALLED, 'ts'))
			{
		  	if (!defined('RP_RECIPES_TABLE')) { define('RP_RECIPES_TABLE', $table_prefix . 'tradeskill_recipes'); }
				$sql = ' SELECT DISTINCT recipe_name as item_name  FROM ' . RP_RECIPES_TABLE . ' GROUP BY recipe_name ';
			}
	}

 	if($step=='bank')
	{
		if ($pm->check(PLUGIN_INSTALLED, 'raidbanker'))
		{
			if (!defined('RB_BANKS_TABLE')) { define('RB_BANKS_TABLE', $table_prefix . 'raidbanker_bank'); }
			$sql = ' SELECT  DISTINCT rb_item_name as item_name FROM ' . RB_BANKS_TABLE . ' as item_name GROUP BY rb_item_name ';
		}
	}

 	if($step=='bad')
	{
	 		$sql = 'SELECT DISTINCT item_name FROM ' . item_cache_table . ' WHERE item_icon LIKE "%INV_Misc_QuestionMark%"'  ;
	}

 	if($step=='all')
	{
	 		$sql = 'SELECT DISTINCT item_name FROM ' . item_cache_table . ' ' . ITEMS_TABLE   ;
	}

	if(isset($sql))
	{
		$result = $db->query($sql) ;
	  while($row = $db->fetch_record($result))
    {
      $items[$ii] = $row['item_name'];
      $ii++;
    }
    $db->free_result($result);
	}
	$_output .= '<span id="loadingtext" style="display:inline;"><table>';
	$_output .= '<table>';
	$_output .= '<tr><td><img src="'.$eqdkp_root_path.'images/glyphs/progress.gif" alt"Loading" />';
	$_output .= ' Update '.$ii.' Items </td></tr>';
	$_output .= '<tr><td><iframe src="updateitemstats_include.php?step='.$step.'&count='.$ii.'&actual=0" width="390" height="280" name="item_update" frameborder=0 scrolling="no"><td></tr>';
	$_output .= '</table></span>';
	$_output .= '</html>';

 echo $_output;

?>
