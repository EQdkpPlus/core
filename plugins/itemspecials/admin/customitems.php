<?php
/******************************
 * EQdkp ItemSpecials Plugin
 * (c) 2006 by WalleniuM [Simon Wallmann]
 * http://www.wallenium.de   
 * ------------------
 * settings.php
 * Changed: Thu July 13, 2006
 * 
 ******************************/

// EQdkp required files/vars
define('EQDKP_INC', true);
define('IN_ADMIN', true);
define('PLUGIN', 'itemspecials');
include_once('../include/functions.php');

$eqdkp_root_path = './../../../';
include_once($eqdkp_root_path . 'common.php');

// Check user permission
$user->check_auth('a_itemspecials_conf');
$rb = $pm->get_plugin('itemspecials');

if ( !$pm->check(PLUGIN_INSTALLED, 'itemspecials') )
{
    message_die('The ItemSpecials plugin is not installed.');
}
if (!defined('IS_CUSTOM_TABLE')) { define('IS_CUSTOM_TABLE', $table_prefix . 'itemspecials_custom'); }

$sql ="SELECT * FROM ".IS_CUSTOM_TABLE;
$custom_result = $db->query($sql);
while($customrow = $db->fetch_record($custom_result)) {
  $tpl->assign_block_vars('items_row', array(
              'NAME'      => $customrow['custom_name']
              )
          );
}
$tpl->assign_vars(array(
      'F_CUSTOM'        => 'settings.php' . $SID,
      'SHOW_ADD'        => false,
      
      'L_ADD_ITEM'      => $user->lang['is_add_item'],
      'L_CUSTOM_ITEMS'  => $user->lang['is_custom_items'],
      'L_ITEM_NAME'     => $user->lang['is_item_name'],
      'L_B_ADDITEM'     => $user->lang['is_add_item-b'],
      'L_B_DELITEM'     => $user->lang['is_del_item_b'],
      )
);

   $eqdkp->set_vars(array(
	    'page_title'             => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['is_conf_pagetitle'],
			'template_path' 	       => $pm->get_data('itemspecials', 'template_path'),
			'template_file'          => 'admin/customitems.html',
			'display'                => true)
    );

?>
