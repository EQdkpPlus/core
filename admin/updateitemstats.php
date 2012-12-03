<?php
/******************************
* EQdkp
* Copyright 2002-2003
* Licensed under the GNU GPL.  See COPYING for full terms.
* ------------------
* updateitems.php
* Began: Wed October 12 2005
*
* $Id$
*
******************************/

define('EQDKP_INC', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');
include_once($eqdkp_root_path . 'itemstats/config.php');
include_once($eqdkp_root_path . 'itemstats/eqdkp_itemstats.php');


$user->check_auth('a_');

if($_GET['delete'] == 'yes')
{
	$footcount = 0 ;
	//get all itemnames and store in array
	// Itemcache
	$sql = 'SELECT item_name FROM ' . item_cache_table . ' GROUP BY item_name';
	$items_result = $db->query($sql) ;
	while ( $item = $db->fetch_record($items_result) )
	{
		$items[] = $item[item_name];
	}
	$db->free_result($items_result);

	// Itemstable
	$sql = 'SELECT item_name FROM ' . ITEMS_TABLE . ' GROUP BY item_name';
	$items_result = $db->query($sql) ;
	while ( $item = $db->fetch_record($items_result) )
	{
		$items[] = $item[item_name];
	}
	$db->free_result($items_result);

	//clear itemcache
	$sql2 = 'TRUNCATE table ' . item_cache_table ;
	$result2 = $db->query($sql2) ;
	$db->free_result($result2);

	$sql = "INSERT INTO `".item_cache_table."` VALUES ('Zugang zu Naxxramas', '', 'orangename', 'INV_Misc_Key_04', '<table cellpadding=\'0\' border=\'0\' class=\'borderless\'><tr><td valign=\'top\'>\r\n<img class=\'itemicon\' src=\'{ITEM_ICON_LINK}\'></td><td><div class=\'wowitemt\' style=\'display:block\'><div>\r\n<span class=\'orangename\'>Zugang zu Naxxramas</span><br /></div>\r\n<div class=\'tooldiv\'><span class=\'itemdesc\'>&quot;Diesem Spieler ist der Zutritt zu Naxxramas gestattet.&quot;</span><br />\r\n</div></div></td></tr></table>');";
	$result = $db->query($sql) ;
	$db->free_result($result);
	$sql = "INSERT INTO `".item_cache_table."` VALUES ('Entry to Naxxramas', '', 'orangename', 'INV_Misc_Key_04', '<table cellpadding=\'0\' border=\'0\' class=\'borderless\'><tr><td valign=\'top\'>\r\n<img class=\'itemicon\' src=\'{ITEM_ICON_LINK}\'></td><td><div class=\'wowitemt\' style=\'display:block\'><div>\r\n<span class=\'orangename\'>Entry to Naxxramas</span><br /></div>\r\n<div class=\'tooldiv\'><span class=\'itemdesc\'>&quot;This player is allowed to enter Naxxramas.&quot;</span><br />\r\n</div></div></td></tr></table>');";
	$result = $db->query($sql) ;
	$db->free_result($result);

	if(is_array($items))
	{
		$items = array_unique($items);		
		$footcount = count($items) ;		

		foreach ($items as $value)
		{
		   $tpl->assign_block_vars('items_row', array(
		   			'ROW_CLASS' => $eqdkp->switch_row_class(),
		   			'NAME' => itemstats_decorate_name(stripslashes($value)).'<br>'.itemstats_get_html(stripslashes($value))
		   			)
			);
		}
	}
	else
	{
	 $tpl->assign_block_vars('items_row', array(
	   			'ROW_CLASS' => $eqdkp->switch_row_class(),
	   			'NAME' => '<br><br>'. $user->lang['updi_nothing_found'].'<br><br>' ));   	
	}	
	
	$tpl->assign_vars(array(
		'SHOW'	 => true ,
		'L_ITEM' => $user->lang['item'],		
		'FOOTCOUNT' =>  $footcount.$user->lang['updi_footcount'],
		)
	);

}
else if($_GET['show'] == 'yes')
{
	$footcount = 0 ;
	//get all itemnames and store in array
	// Itemcache
	$sql = 'SELECT item_name FROM ' . item_cache_table . ' GROUP BY item_name';
	$items_result = $db->query($sql) ;
	while ( $item = $db->fetch_record($items_result) )
	{
		$items[] = $item[item_name];
	}
	$db->free_result($items_result);

	// Itemstable
	$sql = 'SELECT item_name FROM ' . ITEMS_TABLE . ' GROUP BY item_name';
	$items_result = $db->query($sql) ;
	while ( $item = $db->fetch_record($items_result) )
	{
		$items[] = $item[item_name];
	}
	$db->free_result($items_result);
	
	if(is_array($items))
	{
		$items = array_unique($items);		
		$footcount = count($items) ;		

		foreach ($items as $value)
		{
		   $tpl->assign_block_vars('items_row', array(
		   			'ROW_CLASS' => $eqdkp->switch_row_class(),
		   			'NAME' => itemstats_decorate_name(stripslashes($value)).'<br>'.itemstats_get_html(stripslashes($value))
		   			)
			);
		}
	}
	else
	{
	 $tpl->assign_block_vars('items_row', array(
	   			'ROW_CLASS' => $eqdkp->switch_row_class(),
	   			'NAME' => '<br><br>'. $user->lang['updi_nothing_found'].'<br><br>' ));   	
	}	
	
	$tpl->assign_vars(array(
		'SHOW'	 => true ,
		'L_ITEM' => $user->lang['item'],		
		'FOOTCOUNT' =>  $footcount." ".$user->lang['item'],
		)
	);
}
else if($_GET['refreshbad'] == 'yes')
{

	$footcount = 0 ;
	$sql = 'SELECT item_name FROM ' . item_cache_table . ' WHERE item_icon LIKE "%INV_Misc_QuestionMark%"'  ; 
	$items_result = $db->query($sql) ;


	while ( $item = $db->fetch_record($items_result) )
	{
		$items[] = $item[item_name];
	}
	$db->free_result($items_result);
	
	if(is_array($items))
	{
		$items = array_unique($items);		
		$footcount = count($items) ;		
	
		foreach ($items as $value)
		{   
			$sql = "DELETE FROM " . item_cache_table . " WHERE item_name = '".$value."' LIMIT 1";
			$db->query($sql);	
	   
		  $tpl->assign_block_vars('items_row', array(
	   			'ROW_CLASS' => $eqdkp->switch_row_class(),
	   			'NAME' => itemstats_decorate_name(stripslashes($value)).'<br>'.itemstats_get_html(stripslashes($value))));   			
		}
	}	
	else
	{
		  $tpl->assign_block_vars('items_row', array(
	   			'ROW_CLASS' => $eqdkp->switch_row_class(),
	   			'NAME' => '<br><br>No Bad items found<br><br>' ));   			
	}

	$tpl->assign_vars(array(
		'SHOW'	 => true ,
		'L_ITEM' => $user->lang['item'],		
		'FOOTCOUNT' =>  $footcount.$user->lang['updi_footcount'],
		)
	);
}
else if($_GET['tradeskill'] == 'yes')
{
	if ($pm->check(PLUGIN_INSTALLED, 'ts')) 
	{ 
		global $table_prefix;
		$footcount = 0 ;
		
		if (!defined('RP_RECIPES_TABLE')) { define('RP_RECIPES_TABLE', $table_prefix . 'tradeskill_recipes'); }
		
		$sql = ' SELECT recipe_name FROM ' . RP_RECIPES_TABLE . ' GROUP BY recipe_name ';
		$items_result = $db->query($sql) ;
		
		while ( $item = $db->fetch_record($items_result) )
		{
			$items[] = $item[recipe_name];
		}		

		if(is_array($items))
		{
			$items = array_unique($items);		
			$footcount = count($items) ;
			foreach ($items as $value)
			{
				   
				$sql = "DELETE FROM " . item_cache_table . " WHERE item_name = '".$value."' LIMIT 1";
				$db->query($sql);	
		   
			  $tpl->assign_block_vars('items_row', array(
		   			'ROW_CLASS' => $eqdkp->switch_row_class(),
		   			'NAME' => itemstats_decorate_name(stripslashes($value)).'<br>'.itemstats_get_html(stripslashes($value))));   			
			}
		}	
		else
		{
		  $tpl->assign_block_vars('items_row', array(
	   			'ROW_CLASS' => $eqdkp->switch_row_class(),
	   			'NAME' => '<br><br>'. $user->lang['updi_nothing_found'].'<br><br>' ));   			
		}	
	}	# if installed
	
	$tpl->assign_vars(array(
		'SHOW'	 => true ,
		'L_ITEM' => $user->lang['item'],		
		'FOOTCOUNT' =>  $footcount.$user->lang['updi_footcount'],
		)
	);
	
}
else if($_GET['raidbanker'] == 'yes')
{
	if ($pm->check(PLUGIN_INSTALLED, 'raidbanker')) 
	{ 
		//
	}	
	
}
else
{
	# do nothing :D
}

	$itemstats_count 			= $db->query_first('SELECT count(*) FROM ' . item_cache_table) ;
	$items_count 					= $db->query_first('SELECT distinct(count(item_name)) FROM ' . ITEMS_TABLE) ;
	$itemstats_bad_count 	= $db->query_first('SELECT count(*) FROM ' . item_cache_table. ' WHERE item_icon LIKE "%INV_Misc_QuestionMark%"')  ; 
	
	
	  if(function_exists('curl_version'))
    {
    	$their_curl =	curl_version();
    	if(isset($their_curl['version']))
    	{
    		$curl =  '<span class="positive">('.$their_curl['version'].')'.$user->lang['updi_curl_ok'].'</span>' ;
    	}
    	else
    	{
    		$curl='<span class="negative">'.$user->lang['updi_curl_bad'].'</span>'; 
    	}
    }
    else
    {
     $curl='<span class="negative">'.$user->lang['updi_curl_bad'].'</span>'; 
    }
    
    if(function_exists('fopen'))
    {
    	$fopen = '<span class="positive">'.$user->lang['updi_fopen_ok'].'</span>' ; 
    }	
    else
    {
      $fopen = '<span class="negative">'.$user->lang['updi_fopen_bad'].'</span>' ; 
    }
    
    $file = "/itemstats/includes_de/debug.txt" ;
    if(file_exists("..".$file))
    {
     $debugfile = "<a href='".$eqdkp->config['server_path'].$file."' >Download</a>" ;
     if (is_writeable("..".$file)) 
     {
     	$debugfile_w =  '<span class="positive">'.$user->lang['updi_writeable_ok'].'</span>' ; 
     }
     else
     {
     	$debugfile_w =  '<span class="negative">'.$user->lang['updi_writeable_no'].'</span>' ;
     }	
    }
    else
    {
    	$debugfile = $user->lang['updi_notfound'] ;
    }
    
    if($conf_plus['pk_itemstats_debug']==1)
		{
			$debug_state =  '<span class="positive">'.$user->lang['updi_active'].'</span>' ;
			
		}
		else
		{
			$debug_state = '<span class="negative">'.$user->lang['updi_inactive'].'</span>' ;
		}	
		
		$web_db_state = ping();	
    

$tpl->assign_vars(array(
		'LISTITEMS_FOOTCOUNT' => 'Itemstats update Tool by Corgan',
		'UPDI_HEADER'										=> $user->lang['updi_header'] ,
		'UPDI_HEADER2'									=> $user->lang['updi_header2'] ,
		'UPDI_ACTION'										=> $user->lang['updi_action'] ,
		'UPDI_HELP'											=> $user->lang['updi_help'],
		'UPDI_SHOW_ALL'									=> $user->lang['updi_show_all'] ,
		'UPDI_ITEMSCOUNT'								=> $user->lang['updi_itemscount'],
		'UPDI_BADITEMSCOUNT'						=> $user->lang['updi_baditemscount'] ,
		'UPDI_REFRESH_ALL'							=> $user->lang['updi_refresh_all'],
		'UPDI_REFRESH_BAD'							=> $user->lang['updi_refresh_bad'],
		'UPDI_REFRESH_RAIDBANK'					=> $user->lang['updi_refresh_raidbank'] ,
		'UPDI_REFRESH_TRADESKILL'				=> $user->lang['updi_refresh_tradeskill'],
		'UPDI_HELP_SHOW_ALL'						=> $user->lang['updi_help_show_all'] ,
		'UPDI_HELP_REFRESH_ALL'					=> $user->lang['updi_help_refresh_all'],
		'UPDI_HELP_REFRESH_BAD'					=> $user->lang['updi_help_refresh_bad'],
		'UPDI_HELP_REFRESH_RAIDBANK'		=> $user->lang['updi_help_refresh_raidbank'],
		'UPDI_HELP_REFRESH_TRADESKILL'	=> $user->lang['updi_help_refresh_Tradeskill'],
		'UPDI_ITEMS'										=> $user->lang['updi_items'],
		'UPDI_ITEMS_DUPLICATE'					=> $user->lang['updi_items_duplicate'],
	
		'ALL_COUNT' 										=> $itemstats_count, 
		'BAD_COUNT' 										=> $itemstats_bad_count,
		'ITEMS'													=> $items_count,
		'CURL'													=> $curl,
		'FOPEN'													=> $fopen,
		'DEBUG_FILE'										=> $debugfile_w.$debugfile ,
		'DEBUG_STATE'										=> $debug_state ,
		'WEB_DB_STATE'									=> $web_db_state 

		));


$eqdkp->set_vars(array(
    'page_title'    => sprintf($user->lang['title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']),
    'template_file' => 'admin/updateitemstats.html',
    'display'       => true)
);

function ping()
{
	global $eqdkp ;
	
	if($eqdkp->config['default_game'] == 'WoW_german')
	{
		$host='www.buffed.de';
	}
	else
	{
		$host='wow.allakhazam.com'; 
	}
	
	if(!function_exists('fsockopen'))
  {
  	return '<a href=http://'.$host.' target=_blank>'.$host.'</a> (fsockopen off)' ;
  }	
	  
	    
  $port=80;
  $numpings=10;
  for ($x=0;$x<$numpings;$x++)
      {
          $starttime=microtime();
          $socket=@fsockopen($host,$port);
          $endtime=microtime();
          if ($socket!=false)
              {
                  fclose($socket);
                  list($msec,$sec)=explode(" ",$starttime);
                  $starttime=(float)$msec+(float)$sec;
                  list($msec,$sec)=explode(" ",$endtime);
                  $endtime=(float)$msec+(float)$sec;
                  $pingtime=($endtime-$starttime)*1000;
              }
          else
              {
                  $pingtime=-1;
              }
          if ($pingtime!=-1)
              {
                  return '<span class="positive"><a href=http://'.$host.' target=_blank>'.$host.'</a> ('.$port.') Ping: '.round($pingtime,2).' ms </span>' ;
              }
          else
              {
                  return  '<span class="negative"> Port '.$port.' could not be reached on <a href=http://'.$host.' target=_blank>'.$host.'</a> </span>' ;
              }
          flush();
      }
}

?>
