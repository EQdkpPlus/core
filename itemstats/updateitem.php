<?php
/*
+---------------------------------------------------------------+
|       Itemstats FR Core by Yahourt
|		modded by Corgan for EQdkp Plus
|		Add WoWHead & Armory Support
|
|		$Id$
+---------------------------------------------------------------+

*/

define('EQDKP_INC', true);
$eqdkp_root_path = '../';
include_once($eqdkp_root_path . 'common.php');


include_once(dirname(__FILE__) . '/config.php');
include_once(dirname(__FILE__) . '/itemstats.php');


if (!defined('ITEMSTATS_DEBUG'))
{
	// Date in the past
	header("Expires: Mon, 05 Sep 1985 16:31:00 GMT");

	// always modified
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

	// HTTP/1.1
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);

	// HTTP/1.0
	header("Pragma: no-cache");

	// Redirect via an HTML form for PITA webservers
	if (@preg_match('/Microsoft|WebSTAR|Xitami/', getenv('SERVER_SOFTWARE')))
	{
        if (isset($_SERVER['HTTP_REFERER']))
        {
		    header('Refresh: 0; URL=' . $_SERVER['HTTP_REFERER']);
        }
		echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"><html><head><meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"><meta http-equiv="refresh" content="0; url=' . $_SERVER['HTTP_REFERER'] . '"><title>Redirect</title></head><body><div align="center">If your browser does not support meta redirection please click <a href="' . $_SERVER['HTTP_REFERER'] . '">HERE</a> to be redirected</div></body></html>';
	}
	else
	{
		// Behave as per HTTP/1.1 spec for others
        if (isset($_SERVER['HTTP_REFERER']))
        {
		    header('Location: ' . $_SERVER['HTTP_REFERER']);
        }
	}
}

if ($in->exists('item'))
{
    $item = $in->get('item', '');
    if(validate_item($item)){    
      $item_stats = new ItemStats(false, 1);
      if ($item_stats->connected == true)
      {
      	$item_stats->item_cache->deleteItemFromCache($item);
      	$item_stats->updateItem($item);
      }
    }
}

function validate_item($item){
  $bad_content = array(
    '\\',
    '/',
    '@',
    ';',
    '>',
    '<',
    '%',
    '#',
    '?'
  );
  
  $is_valid = true;
  foreach($bad_content as $bad_word){
    if(strpos($item, $bad_word) > 0){
      $is_valid = false;
      break;
    }        
  }
  
  //it might still be an url, we can't prohibit dots
  if(preg_match('/\S\.\S/', $item) > 0)
    $is_valid = false;
   
  return $is_valid;
}
?>