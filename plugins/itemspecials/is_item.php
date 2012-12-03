<?php
/******************************
 * EQdkp ItemSpecials Plugin
 * (c) 2006 by WalleniuM [Simon Wallmann]
 * http://www.wallenium.de   
 * ------------------
 * is_item.php
 * Changed: Wed July 12, 2006
 * 
 ******************************/

define('EQDKP_INC', true);
$eqdkp_root_path = '../../';
include_once($eqdkp_root_path . 'common.php');
include_once($eqdkp_root_path . 'itemstats/eqdkp_itemstats.php');

$user->check_auth('u_item_view');

if (isset($_GET[URI_ITEM]))
{

    // We want to view items by name and not id, so get the name
    $item_name = urldecode($_GET[URI_ITEM]);
    if ( empty($item_name) ){
        message_die($user->lang['error_invalid_item_provided']);
    }
		$output  = "<link REL=StyleSheet HREF='../../itemstats/templates/itemstats.css' TYPE='text/css' MEDIA=screen />";
		$output .= "<center>".itemstats_get_html($item_name)."</center>";
		echo $output;
}
else
{
    message_die($user->lang['error_invalid_item_provided']);
}
?>
