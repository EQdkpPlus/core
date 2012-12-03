<?php
/******************************
 * EQDKP PLUGIN: Charmanager
 * (c) 2006 by WalleniuM [Simon Wallmann]
 * http://www.wallenium.de   
 * ------------------
 * charmanager.php
 * Changed: Thu October 24, 2006
 * 
 ******************************/
 if ($pm->check(PLUGIN_INSTALLED, 'ts')){
if (!defined('RP_TRADESKILL_TABLE')) { define('RP_TRADESKILL_TABLE', $table_prefix . 'tradeskills'); }
if (!defined('RP_RECIPES_TABLE')) { define('RP_RECIPES_TABLE', $table_prefix . 'tradeskill_recipes'); }
if (!defined('RP_USERS_TABLE')) { define('RP_USERS_TABLE', $table_prefix . 'tradeskill_users'); }
if (!defined('RP_TUSERS_TABLE')) { define('RP_TUSERS_TABLE', $table_prefix . 'user_tradeskills'); }

	$tradeid_sql = 'SELECT ut.ps, rp.trade_name, rp.trade_icon
        				FROM ' . RP_TUSERS_TABLE . ' ut
        				LEFT JOIN ' . RP_TRADESKILL_TABLE . ' rp ON (ut.trade_id=rp.trade_id)
								WHERE ut.member_id = ' . $member['member_id'] . '
								AND ut.ps > 0
								ORDER BY ut.member_id';
	$tsext_result = $db->query($tradeid_sql);
	
	while ( $tsext_row = $db->fetch_record($tsext_resul) )
	{ 
		$tpl->assign_block_vars('ts_ext', array(
            'NAME'    => $tsext_row['trade_name'],
            'ICON'		=> '<img src="'.$tsext_row['trade_icon'].'" title="'.$tsext_row['trade_name'].'" border="0" height="32" width="32">'
    ));
   } // end of while
}
 ?>