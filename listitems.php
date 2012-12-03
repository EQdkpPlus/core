<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2007
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2010 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 * 
 * $Id$
 */

// EQdkp required files/vars
define('EQDKP_INC', true);

$eqdkp_root_path = './';
include_once ($eqdkp_root_path . 'common.php');

// Check user permission
$user->check_auth('u_item_list');

require_once($eqdkp_root_path.'core/html_pdh_tag_table.class.php');

$user->lang['manage_items'] = "Manage items";

//Sort
if (isset($_GET['sort'])){
  $sort = $in->get('sort');
}else{
  $sort = '0|desc';
}
$sort_suffix = '&amp;sort='.$sort;

//redirect on member management
if ( isset($_GET['manage_b']) && ($_GET['manage_b'] == $user->lang['manage_items']) ){
  $manage_link = './admin/listitems.php';
  redirect($manage_link);
}

$start = 0;
if($in->get('start')){
  $start = $in->get('start', 0);
  $pagination_suffix = '&amp;start='.$start;
}

if($in->get('search')){
  $mySearch = $in->get('search');
  $searchType = ($in->get('search_type')) ? $in->get('search_type') : 'itemname';
}

//Output
$view_list = $pdh->get('item', 'id_list');
$filtered_list = filter($view_list, $searchType, $mySearch);

$item_count = ((count($filtered_list) > 0) ? count($filtered_list) : count($view_list));
$footer_text = sprintf($user->lang['listitems_footcount'], $item_count ,$user->data['user_ilimit']);

//init infotooltip
infotooltip_js();
$hptt_page_settings = $pdh->get_page_settings('listitems', 'hptt_listitems_itemlist');
$hptt = new html_pdh_tag_table($hptt_page_settings, $view_list, $filtered_list, array('%link_url%' => 'viewitem.php', '%link_url_suffix%' => '', '%raid_link_url%' => 'viewraid.php', '%raid_link_url_suffix%' => '', '%itt_lang%' => false, '%itt_direct%' => 0, '%onlyicon%' => 0));
$tpl->assign_vars(
  array (
  	'F_ITEMS'         => 'listitems.php'.$SID,
    'SEARCH_LINK'     => '<input type="image" src="'.$eqdkp_root_path.'images/glyphs/view.png" name="search_b" width="16" height="16" value="1" alt="'.$user->lang['Itemsearch_searchby'].'" title="'.$user->lang['Itemsearch_searchby'].'" align="absmiddle">',
    'MANAGE_LINK'     => ($user->check_auth('a_item_', false)) ? '<a href="admin/manage_items.php'.$SID.'" title="'.$user->lang['manage_items'].'"><img src="'.$eqdkp_root_path.'images/glyphs/edit.png" alt="'.$user->lang['manage_items'].'"></a>' : '',
		'PAGE_OUT'        => $hptt->get_html_table($sort, $pagination_suffix, $start, $user->data['user_ilimit'], $footer_text),
		'ITEM_PAGINATION' => generate_pagination('listitems.php'.$SID.$sort_suffix, $item_count, $user->data['user_ilimit'], $start),
  	'L_SEARCHBY'      => $user->lang['Itemsearch_searchby'] ,
  	'L_ITEMM'         => $user->lang['Itemsearch_item'] ,
  	'L_BUYERR'        => $user->lang['Itemsearch_buyer'] ,
  	'L_RAIDD'         => $user->lang['Itemsearch_raid'] ,
  )
);

$core->set_vars(
  array(
    'page_title'    => $user->lang['listitems_title'],
    'template_file' => 'listitems.html',
    'display'       => true
  )
);

// Search Helper
function filter($view_list, $searchType, $mySearch ){
global $pdh;
  if(!$mySearch){
    return $view_list;
  }

  $filtered_list = array();
  $filter_type = '';
  switch($searchType){
    case 'itemname':  $filter_type = 'name';				break;
    case 'buyer':			$filter_type = 'buyer_name';	break;
    case 'raidname':  $filter_type = 'raid_name';		break;
  }
  
  // Set the search array
  if($filter_type){
  	foreach($view_list as $item_id){
			if(preg_match("/".$mySearch."/i", $pdh->get('item', $filter_type, array($item_id)))){
				$filtered_list[] = $item_id;
			}
		}
  }
  
  // Return to page
  return $filtered_list;
}
?>