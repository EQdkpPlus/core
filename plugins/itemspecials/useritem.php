<?php
/******************************
 * EQdkp ItemSpecials Plugin
 * (c) 2006 by WalleniuM [Simon Wallmann]
 * http://www.wallenium.de   
 * ------------------
 * useritem.php
 * Changed: November 15, 2006
 * 
 ******************************/

// EQdkp required files/vars
define('EQDKP_INC', true);
define('PLUGIN', 'itemspecials');
include_once('include/functions.php');
$eqdkp_root_path = './../../';
include_once($eqdkp_root_path . 'common.php');

global $table_prefix, $db;
if (!defined('IS_CONFIG_TABLE')) { define('IS_CONFIG_TABLE', $table_prefix . 'itemspecials_config'); }
if (!defined('IS_TOLLFREE_ITEMS')) { define('IS_TOLLFREE_ITEMS', $table_prefix . 'itemspecials_items'); }

// Save the tollfree item addition
if (isset($_POST['item_name'])){
    $sql = "INSERT INTO ".IS_TOLLFREE_ITEMS." (`item_name`, `item_buyer`) VALUES ('".$_POST['item_name']."', '".$_POST['item_buyer']."');";
    if($db->query($sql)){   
    	echo '<script LANGUAGE="JavaScript">
    top.location.href=\'./useritem.php\'
</script>';
    }
}

// Delete the tollfree item addition
if (isset($_POST['delete_id'])){
    $sql = "DELETE FROM ".IS_TOLLFREE_ITEMS." WHERE item_id='".$_POST['delete_id']."';";
    if($db->query($sql)){   
    	echo '<script LANGUAGE="JavaScript">
    				top.location.href=\'./useritem.php\'
						</script>';
    }
}

$sql = 'SELECT * FROM ' . IS_CONFIG_TABLE;
if (!($settings_result = $db->query($sql))) { message_die('Could not obtain configuration data', '', __FILE__, __LINE__, $sql); }
while($roww = $db->fetch_record($settings_result)) {
  $conf[$roww['config_name']] = $roww['config_value'];
}

// Load itemstats if possible
if ($conf['itemstats'] == 1){
  include_once($eqdkp_root_path.'itemstats/eqdkp_itemstats.php');
}

if (!$pm->check(PLUGIN_INSTALLED, 'itemspecials')) { message_die('The Itemspecials plugin is not installed.'); }
if ($user->data['username']=="") { message_die('You are not logged in.'); }
// Check user permission
$user->check_auth('u_items_add');
$rb = $pm->get_plugin('itemspecials');

$sort_order = array(
    0 => array('item_buyer', 'item_buyer desc'),
    1 => array('item_name', 'item_name desc'),
);

$current_order = switch_order($sort_order);

$total_items = $db->query_first('SELECT count(*) FROM ' . IS_TOLLFREE_ITEMS);
$start = ( isset($_GET['start']) ) ? $_GET['start'] : 0;

$sql = 'SELECT ist.item_id, ist.item_name, ist.item_buyer, m.member_name
        FROM ' . IS_TOLLFREE_ITEMS . ' ist, ' . MEMBERS_TABLE . ' m
        LEFT JOIN (' . MEMBER_USER_TABLE . ' mu)       
        ON m.member_id = mu.member_id
        WHERE mu.user_id = '.$user->data['user_id'].'
        AND ist.item_buyer=m.member_name
       	ORDER BY m.member_name';

$listitems_footcount = sprintf($user->lang['listpurchased_footcount'], $total_items, $user->data['user_ilimit']);
$pagination = generate_pagination('useritem.php'.$SID.'&amp;o='.$current_order['uri']['current'], $total_items, $user->data['user_ilimit'], $start);

if ( !($items_result = $db->query($sql)) )
{
    message_die('Could not obtain item information', 'Database error', __FILE__, __LINE__, $sql);
}

while ( $item = $db->fetch_record($items_result) )
{
    $tpl->assign_block_vars('items_row', array(
        'ROW_CLASS' 		=> $eqdkp->switch_row_class(),
	      'ID'						=> $item['item_id'],
	      'BUYER' 				=> ( !empty($item['item_buyer']) ) ? $item['item_buyer'] : '&lt;<i>Not Found</i>&gt;',
        'U_VIEW_BUYER' 	=> ( !empty($item['item_buyer']) ) ? '../viewmember.php'.$SID.'&amp;' . URI_NAME . '='.$item['item_buyer'] : '',
        'NAME' 					=> ($conf['itemstats'] == 1) ? itemstats_decorate_name(stripslashes($item['item_name'])) : $item['item_name'],
        )
    );
}
$db->free_result($items_result);

$tpl->assign_vars(array(
    'L_BUYER' 					=> $user->lang['is_owner'],
    'L_ITEM' 						=> $user->lang['item'],
    'L_BUTTON_ADDITEM'	=> $user->lang['is_additem'],
    'L_BUTTON_I_ADD'		=> $user->lang['is_additem'],
    'L_BUTTON_DELETE'		=> $user->lang['is_delbutton'],
    'L_MEMBER_SETITEMS'	=> $user->lang['is_user_setitems'],
    'L_MEMBER_SETINFO'	=> $user->lang['is_user_setinfo'],

    'O_BUYER' => $current_order['uri'][0],
    'O_NAME' => $current_order['uri'][1],

    'U_LIST_ITEMS' => 'useritem.php'.$SID.'&amp;',

    'START' => $start,
    'S_HISTORY' => true,
    'L_VERSION'	=> $pm->get_data('itemspecials', 'version'),
    'L_ABOUT_HEADER'	=> $user->lang['is_dialog_header'],
    'LISTITEMS_FOOTCOUNT' => $listitems_footcount,
    'ITEM_PAGINATION' => $pagination)
);

$eqdkp->set_vars(array(
	    'page_title'             => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['is_conf_pagetitle'],
			'template_path' 	       => $pm->get_data('itemspecials', 'template_path'),
			'template_file'          => 'useritems.html',
			'display'                => true)
    );
?>