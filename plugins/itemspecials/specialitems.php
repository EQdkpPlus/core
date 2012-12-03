<?php
/******************************
 * EQdkp ItemSpecials Plugin
 * (c) 2006 - 2007 by WalleniuM [Simon Wallmann]
 * http://www.wallenium.de   
 * ------------------
 * specialitems.php
 * Changed: January 10, 2007
 * 
 ******************************/

define('EQDKP_INC', true);
define('PLUGIN', 'itemspecials');
$eqdkp_root_path = './../../';
include_once($eqdkp_root_path . 'common.php');
include_once('./include/functions.php');
include_once('./include/itemstatsadditions.class.php');

$time_start = ISgetmicrotime();

// Get the config
global $table_prefix;
if (!defined('IS_CONFIG_TABLE')) { define('IS_CONFIG_TABLE', $table_prefix . 'itemspecials_config'); }
if (!defined('IS_CUSTOM_TABLE')) { define('IS_CUSTOM_TABLE', $table_prefix . 'itemspecials_custom'); }
if (!defined('IS_TOLLFREE_ITEMS')) { define('IS_TOLLFREE_ITEMS', $table_prefix . 'itemspecials_items'); }

$sql = 'SELECT * FROM ' . IS_CONFIG_TABLE;
if (!($settings_result = $db->query($sql))) { message_die($user->lang['is_sqlerror_config'], '', __FILE__, __LINE__, $sql); }
while($roww = $db->fetch_record($settings_result)) {
  $conf[$roww['config_name']] = $roww['config_value'];
}

include_once('include/data/'.$conf['locale'].'/special.php');

$classLanguage = GetClassLanguage(); // Get the locale of the Classnames!

$sql = "SELECT * FROM ".IS_CUSTOM_TABLE." WHERE `set` = 'itemshow' ORDER BY `order` ASC";
if (!($custom_result = $db->query($sql))) { message_die($user->lang['is_sqlerror_sitem'], '', __FILE__, __LINE__, $sql); }
while($customrow = $db->fetch_record($custom_result)) {
  $custom[$customrow['item_name']] = $customrow['item_name'];
}

// This is for the set/nonset items
if ($conf['nonset_set'] == 1){
  if (!defined('IS_NONSET_TABLE')) { define('IS_NONSET_TABLE', $conf['nonsettable']); }
  if (!defined('IS_SET_TABLE')) { define('IS_SET_TABLE', $conf['settable']); }
  $NSitemtable  = IS_NONSET_TABLE; // NonSet Items
  $Sitemtable   = IS_SET_TABLE; // SetItems
} else{
  $NSitemtable  = ITEMS_TABLE; // NonSet Items
  $Sitemtable   = ITEMS_TABLE; // SetItems
}

// Load Itemstats if possible
if ($conf['itemstats'] == true){
  include_once($eqdkp_root_path.'itemstats/eqdkp_itemstats.php');
  $isaddition = new ItemstatsAddition();
	$is_version = ($isaddition->GetItemstatsVersion()) ? true : false;
}

if (!$pm->check(PLUGIN_INSTALLED, 'itemspecials')) { message_die($user->lang['is_not_installed']); }

$user->check_auth('u_specialitems_view');

//set the data arrays
$trinketspecial_name = "trinket_items";
$mountspecial_name = "mount_items";
$bookspecial_name = "aqbook_items";
$atieshspecial_name = "atiesh_items";
if (is_array($custom) and isset($custom)){
  $specialitems = $custom;
}else{
  message_die($user->lang['is_no_specialitems']);
}
$trinketitems = $$trinketspecial_name;
$aqmountitems = $$mountspecial_name;
$aqbookitems = $$bookspecial_name;
$atieshitems = $$atieshspecial_name;

$sort_order = array(
    0 => array('member_name', 'member_name desc'),
    1 => array('member_class', 'member_class desc'),
    2 => array('member_current desc', 'member_current'),
    3 => array('rank_name', 'rank_name desc'),
    4 => array('class_armor_type', 'class_armor_type desc'),
    5 => array('member_earned desc', 'member_earned'),
);

$current_order = switch_order($sort_order);
$cur_hash = hash_filename("specialitems.php");

foreach($specialitems as $key=>$item) {
  if ($conf['itemstats'] == 1 && $conf['si_only_crosses'] == 0){
        $specialitems[$key] = array(
                'name'  => $item,
                'item_icon'      => $isaddition->itemstats_decorate_Icon(stripslashes($item), "middle", $is_version)
        );
  } else {
        $specialitems[$key] = array(
                'name'  => $item,
                'item_icon'      => html_entity_decode($conf['is_replace'])
        );
  }
}

$sql = "SELECT i.item_id, i.item_name, i.item_buyer
        FROM (" . $NSitemtable . " i INNER JOIN " . MEMBERS_TABLE . " m
        ON i.item_buyer = m.member_name)
        UNION SELECT ti.item_id, ti.item_name, ti.item_buyer
        FROM (".IS_TOLLFREE_ITEMS." ti
        INNER JOIN " . MEMBERS_TABLE . " m
        ON ti.item_buyer = m.member_name)
        ORDER BY item_name ASC;";

$itemres = $db->query($sql);
if(!$itemres)
        message_die($user->lang['is_item_info'], "", "", "");
        $zz = 0;
        while($row = $db->fetch_record($itemres)) {
        				$itemnametemporary = ($conf['itemstats'] == 1 && $conf['is_correctmode']) ? $isaddition->itemstats_format_name($row['item_name']) : $row['item_name'];
                $member_items[$row['item_buyer']][$itemnametemporary] = true;
                $member_items[trim($row['item_name'])]['item_id'] = $row['item_id'];
                if ($conf['si_itemstatus_show'] == 1){
                  $countitemsblubb[$zz] = $row['item_name'];
                  $zz++;
                }
        }
        
//
// Normal member display
//
    $member_count = 0;

    // Figure out what data we're comparing from member to member
    // in order to rank them
    $sort_index = explode('.', $current_order['uri']['current']);
    $previous_source = preg_replace('/( (asc|desc))?/i', '', $sort_order[$sort_index[0]][$sort_index[1]]);

    $show_all = ( (!empty($_GET['show'])) && ($_GET['show'] == 'all') ) ? true : false;

    //
    // Filtering
    //

    $filter = ( isset($_GET['filter']) ) ? urldecode($_GET['filter']) : 'none';
    $filter = ( preg_match('#\-{1,}#', $filter) ) ? 'none' : $filter;


    // Grab class_id

    if ( isset($_GET['filter']) ) {
	     $temp_filter = $_GET['filter'];
       // Just because filter is set doesn't mean its valid - clear it if its set to none
       if ( preg_match('/ARMOR_/', $temp_filter) ) {
	          $temp_filter = preg_replace('/ARMOR_/', '', $temp_filter);
	          $query_by_armor = 1;
            $query_by_class = 0;
            $id = $temp_filter;
      }elseif ( $temp_filter == "none" ) {
            $temp_filter = "";
	          $query_by_armor = 0;
            $query_by_class = 0;
      } else {
            $query_by_class = 1;
            $query_by_armor = 0;
            $id = $temp_filter;
       } // end armor preg
} // end if filter

    $tpl->assign_block_vars('filter_row', array(
        'VALUE'    => strtolower("None"),
        'SELECTED' => ( $filter == strtolower("None") ) ? ' selected="selected"' : '',
        'OPTION'   => str_replace('_', ' ', "None"))
    );

	// Add in the cute ---- line, filter on None if some idiot selects it
    $tpl->assign_block_vars('filter_row', array(
        'VALUE'    => strtolower("None"),
        'SELECTED' => ( $filter == strtolower("NULL") ) ? ' selected="selected"' : '',
        'OPTION'   => str_replace('_', ' ', "--------"))
    );

	// Grab generic armor information
	$sql = 'SELECT class_armor_type FROM ' . CLASS_TABLE .'';
	$sql .= ' GROUP BY class_armor_type';
	$result = $db->query($sql);

        while ( $row = $db->fetch_record($result) )
        {
          $tpl->assign_block_vars('filter_row', array(
              'VALUE'    => "ARMOR_" . strtolower($row['class_armor_type']),
              'SELECTED' => ( $temp_filter == strtolower($row['class_armor_type']) ) ? ' selected="selected"' : '',
              'OPTION'   => str_replace('_', ' ', $row['class_armor_type']))
          );
        }

	// Add in the cute ---- line, filter on None if some idiot selects it
    $tpl->assign_block_vars('filter_row', array(
        'VALUE'    => strtolower("None"),
        'SELECTED' => ( $filter == strtolower("NULL") ) ? ' selected="selected"' : '',
        'OPTION'   => str_replace('_', ' ', "--------"))
    );

	// Moved the class/race/faction information to the database
        $sql = 'SELECT class_name, class_id, class_min_level, class_max_level FROM ' . CLASS_TABLE .'';
        $sql .= ' GROUP BY class_name';
        $result = $db->query($sql);

        while ( $row = $db->fetch_record($result) )
        {
           $tpl->assign_block_vars('filter_row', array(
                'VALUE' => $row['class_name'],
                'SELECTED' => ( strtolower($filter) == strtolower($row['class_name']) ) ? ' selected="selected"' : '',
                'OPTION'   => ( !empty($row['class_name']) ) ? stripslashes($row['class_name']) : '(None)' )
                );
        }
        $db->free_result($result);
	// end database move of race/class/faction

    // Build SQL query based on GET options &other data
        $sql = 'SELECT m.*, (m.member_earned-m.member_spent+m.member_adjustment) AS member_current,
		            member_status, r.rank_name, r.rank_hide, r.rank_prefix, r.rank_suffix,
								c.class_name AS member_class, c.class_armor_type AS armor_type,
		            c.class_min_level AS min_level, c.class_max_level AS max_level';
	
    $sql .=' FROM ' . MEMBERS_TABLE . ' m, ' . MEMBER_RANKS_TABLE . ' r, ' . CLASS_TABLE . ' c';
    if (!empty($_GET['itemname'])){ 
    		 $sql .= ', '.$NSitemtable.' i LEFT JOIN '.IS_TOLLFREE_ITEMS.' ci on (ci.item_buyer = i.item_buyer)'; 
    } 
	       $sql .= ' WHERE (c.class_id = m.member_class_id)';
	       $sql .= ' AND (m.member_rank_id = r.rank_id)';   
   
   if (!empty($_GET['itemname']))
    {
        $sql .= " AND (i.item_buyer = m.member_name)";
        $sql .= " AND ((ci.item_name =  '". urldecode($_GET['itemname'])."') OR (i.item_name =  '". urldecode($_GET['itemname'])."'))";
    }
   
    if ( $query_by_class == '1' )
    {
        $sql .= " AND c.class_name =  '$id'";
    
    if ($conf['hidden_groups'] == 1){
          $sql .= " AND r.rank_hide = '0'";
        }
    if ($conf['hide_inactives'] == 1){
          $sql .= " And member_status != 1";
        }
    }

    if ( $query_by_armor == '1' )
    {
        $sql .= " AND c.class_armor_type =  '". ucwords(strtolower($temp_filter))."'";
    }
     if ( !empty($_GET['rank']) )
    {
        $sql .= " AND r.rank_name='" . urldecode($_GET['rank']) . "'";
    }
    if (!empty($_GET['itemname']))
    {
     		$sql .= " GROUP BY i.item_buyer";
    }
        $sql .= ' ORDER BY '.$current_order['sql'];
 //message_die($sql);
    if ( !($members_result = $db->query($sql)) )
    {
        message_die('Could not obtain member information', '', __FILE__, __LINE__, $sql);
    }
    
    while ( $row = $db->fetch_record($members_result) )
    {
            $iconhave = array();
                foreach($specialitems as $key => $item) {
                        if(isset($member_items[$row['member_name']][stripslashes($item['name'])]) && $member_items[$row['member_name']][stripslashes($item['name'])] === true) {
                                
                                $iconhave[$item['name']] = array(
                                        'item_image'    => $item['item_icon'],
                                        'link'          => $eqdkp_root_path."viewitem.php" . $SID . "&amp;i=" . $member_items[$item['name']]['item_id']
                                );
                        } else {
                                $iconhave[$item['name']] = array(
                                        'item_image'    => '',
                                        'link'          => ''
                                );
                        }
                }
        // Figure out the rank search URL based on show and filter
        $u_rank_search  = 'specialitems.php' . $SID . '&amp;rank=' . urlencode($row['rank_name']);
        $u_rank_search .= ( ($eqdkp->config['hide_inactive'] == 1) && (!$show_all) ) ? '&amp;show=' : '&amp;show=all';
        $u_rank_search .= ( $filter != 'none' ) ? '&amp;filter=' . $filter : '';

        if ( member_display($row) )
        {
            $member_count++;
            
// ************************************************************************
// Special Items (non dynamic)
// ************************************************************************
      
	// BWL Trinket
    $st_loot = false;
      if ($conf['si_bwltrinket'] == 1){
			     if($member_items[$row['member_name']][stripslashes($trinketitems[convert_Classname($row['member_class'], $classLanguage, 'from')])] === true){
			     	$st_loot = true;
			    }
			  }
			    
  // ATIESH, the stupid staff :D
      if ($conf['si_atiesh'] == 1){
      $atieshtemp1 = $atieshtemp2 = $atieshtemp3 = $atieshtemp4 = 0;
			     // build an array which  part you have
			     $atiesh1_sql = 'SELECT count(*) AS atieshcount
			            FROM ' . $Sitemtable . "
			            WHERE item_name='".$atieshitems[0]."'
			            and item_buyer='".$row['member_name']."'";
			    if($member_items[$row['member_name']][stripslashes($atieshitems[1])] === true){
			     	$atieshtemp2 = 1 ;
			 		}
			 		if($member_items[$row['member_name']][stripslashes($atieshitems[2])] === true){
			     	$atieshtemp3 = 1 ;
			 		}
			 		if($member_items[$row['member_name']][stripslashes($atieshitems[3])] === true){
			     	$atieshtemp4 = 1 ;
			 		}
					$atiesh1 = $db->query_first($atiesh1_sql);
			    $atieshtemp1 = ($atiesh1 >= 1) ? $atiesh1 : 0 ;
			    $atieshcounts = array($atieshtemp1, $atieshtemp2, $atieshtemp3, $atieshtemp4);
			}else{
            $atiesh_loot = 0;
      }
   // AQ Books
      if ($conf['si_aqbook'] == 1){
      $booktemp1 = $booktemp2 = $booktemp3 = "";
      $book_loot = 0;
       if($member_items[$row['member_name']][stripslashes($aqbookitems[convert_Classname($row['member_class'], $classLanguage, 'from')][0])] === true){
			     	$booktemp1 = $aqbookitems[convert_Classname($row['member_class'], $classLanguage, 'from')][0];
			 			$book_loot++;
			 }
			 if($member_items[$row['member_name']][stripslashes($aqbookitems[convert_Classname($row['member_class'], $classLanguage, 'from')][1])] === true){
			     	$booktemp2 = $aqbookitems[convert_Classname($row['member_class'], $classLanguage, 'from')][1];
			 			$book_loot++;
			 }
			 if($member_items[$row['member_name']][stripslashes($aqbookitems[convert_Classname($row['member_class'], $classLanguage, 'from')][2])] === true){
			     	$booktemp3 = $aqbookitems[convert_Classname($row['member_class'], $classLanguage, 'from')][2];
			 			$book_loot++;
			 }
			    $booknames = array($booktemp1, $booktemp2, $booktemp3);
			}else{
            $book_loot = 0;
      }
    // AQ Mount
			if ($conf['si_aqmount'] == 1){
			$aqmount_name = "";
			$aq_mount = 0;
			 if($member_items[$row['member_name']][stripslashes($aqmountitems['Blue'])] === true){
			     	$aqmount_name = "Blue";
			 			$aq_mount++;
			 }
			 if($member_items[$row['member_name']][stripslashes($aqmountitems['Yellow'])] === true){
			     	$aqmount_name = "Yellow";
			 			$aq_mount++;
			 }
			 if($member_items[$row['member_name']][stripslashes($aqmountitems['Green'])] === true){
			     	$aqmount_name = "Green";
			 			$aq_mount++;
			 }
			 if($member_items[$row['member_name']][stripslashes($aqmountitems['Black'])] === true){
			     	$aqmount_name = "Black";
			 			$aq_mount++;
			 }
			 if($member_items[$row['member_name']][stripslashes($aqmountitems['Red'])] === true){
			     	$aqmount_name = "Red";
			 			$aq_mount++;
			 }
			} else {
            $aq_mount = 0;
      }
      
// ************************************************************************
// Output
// ************************************************************************
			$trinketimg = "" ;
			$atiesh_roundthing = ( @array_sum($atieshcounts)) ? array_sum($atieshcounts): 0;
			if ($conf['itemstats'] == 1 && $conf['si_only_crosses'] == 0){
			 $trinketimg = $isaddition->itemstats_decorate_Icon(stripslashes($trinketitems[convert_Classname($row['member_class'], $classLanguage, 'from')]), 'middle' , $is_version) ;
       $aqmountimg = $isaddition->itemstats_decorate_Icon(stripslashes($aqmountitems[$aqmount_name]),'middle' , $is_version) ;
       $book_tooltip = "<a href='JavaScript:void(0)' onMouseOver=\"overlib('<b>".$user->lang['is_header_tt_book']."</b><br />".$booknames[0]."<br />".$booknames[1]."<br />".$booknames[2]."');\" onMouseOut='nd();'>(".$book_loot."/3)</a> ";
			 $atiesh_tooltip = ($atiesh_roundthing != 0 ) ? "<a href='JavaScript:void(0)' onMouseOver=\"overlib('<b>".$user->lang['is_header_tt_atiesh']."</b><br />".$atieshitems[0]." (".$atieshcounts[0]."/40)<br />".$atieshitems[1]." (".$atieshcounts[1]."/1)<br />".$atieshitems[2]." (".$atieshcounts[2]."/1)<br />".$atieshitems[3]." (".$atieshcounts[3]."/1)');\" onMouseOut='nd();'>(".$atiesh_roundthing."/43)</a>" : "";
			}else{
       $aqmountimg = html_entity_decode($conf['is_replace']);
       $trinketimg = $aqmountimg;
       $book_tooltip = "(".$book_loot."/3)";
       $atiesh_tooltip = ($atiesh_roundthing != 0 ) ? "(".$atiesh_roundthing."/43)" : '';
      }
      
      $tpl->assign_block_vars('members_row', array(
                'ROW_CLASS'       => $eqdkp->switch_row_class(),
                'ID'              => $row['member_id'],
                'COUNT'           => $member_count,
                'RANK'            => ( !empty($row['rank_name']) ) ? (( $row['rank_hide'] == '1' ) ? '<i>' . '<a href="'.$u_rank_search.'">' . stripslashes($row['rank_name']) . '</a>' . '</i>'  : '<a href="'.$u_rank_search.'">' . stripslashes($row['rank_name']) . '</a>') : '&nbsp;',
                'CURRENT'         => $row['member_current'],
                'C_CURRENT'       => color_item($row['member_current']),
                'C_TOTAL'         => color_item($row['member_earned']),
                'TOTAL'           => $row['member_earned'],
                'NAME'            => $row['rank_prefix'] . (( $row['member_status'] == '0' ) ? '<i>' . $row['member_name'] . '</i>' : $row['member_name']),
                'LEVEL'           => ( $row['member_level'] > 0 ) ? $row['member_level'] : '&nbsp;',
                'CLASS'           => ( !empty($row['member_class']) ) ? $row['member_class'] : '&nbsp;',
	        	    'CLASSIMG'        => ( !empty($row['member_class']) ) ? convert_Classname($row['member_class'], $classLanguage, 'from') : '&nbsp;',
                'ARMOR'		        => ( !empty($row['armor_type']) ) ? $row['armor_type'] : '&nbsp;',
                'U_VIEW_MEMBER'   => $eqdkp_root_path.'viewmember.php' . $SID . '&amp;' . URI_NAME . '='.$row['member_name'],
				        
				        'ST'              => (( $st_loot == true) ?  $trinketimg : '&nbsp;'),
				        'AQMOUNT'         => (( $aq_mount >= '1' ) ? $aqmountimg : '&nbsp;'),
                'AQ_BOOK'         => (( $book_loot != '0') ? $book_tooltip : '&nbsp;'),
                'ATIESH'         => (( $atiesh_loot['atieshcount'] != '0') ? $atiesh_tooltip : '&nbsp;')
              ));
      
            if (is_array($custom) and isset($custom)){
              foreach ($custom as $key => $value) {
              // Write data to template
                 $tpl->assign_block_vars('members_row.custom_items', array(
						              'IMAGE'         => ( $iconhave[$value]['link'] != "" ) ? $iconhave[$value]['item_image'] : "",
					                 )
                 );
              }
            }  
            
            $u_rank_search = '';
        }
    }

    $uri_addon  = ''; // Added to the end of the sort links
    $uri_addon .= '&amp;filter=' . urlencode($filter);
    $uri_addon .= ( isset($_GET['show']) ) ? '&amp;show=' . $_GET['show'] : '';

    if ( ($eqdkp->config['hide_inactive'] == 1) && (!$show_all) )
    {
        $footcount_text = sprintf($user->lang['listmembers_active_footcount'], $member_count,
                                  '<a href="specialitems.php' . $SID . '&amp;' . URI_ORDER . '=' . $current_order['uri']['current'] . '&amp;show=all" class="rowfoot">');
    }
    else
    {
        $footcount_text = sprintf($user->lang['listmembers_footcount'], $member_count);
    }
    $db->free_result($members_result);

 // Experimental code
 if ($conf['si_itemstatus_show'] == 1){
  $customthingy = array_count_values($countitemsblubb);
            if (is_array($custom) and isset($custom)){
              foreach ($custom as $key => $value) {
              // fetch the data
              // Write data to template
					       $tpl->assign_block_vars('dyn_counts', array(
						              'ITEMS'         => ( isset($customthingy[$value]) ) ? $customthingy[$value] : 0,
					                'MEMBER'        => $member_count)
                 );
              }
            }     
  } // end

// Dynamic Header
if (is_array($custom) and isset($custom)){
              foreach ($custom as $key => $value) {
              // Write data to template
              $imglink = 'specialitems.php'.$SID.'&itemname='.urlencode($value);
              if ($conf['itemstats'] == 1 ){
                $customheader = $isaddition->itemstats_get_header_Icon(stripslashes($value), "middle", addslashes($value), $imglink);
              }else{
                $customheader = '<a href="'.$imglink.'" >'.$value.'</a>';
              }
					$tpl->assign_block_vars('custom_header', array(
						'HEADER'    => $customheader
						)
					);
              }
            }    

// the old crap Headers
if ($conf['itemstats'] == 1 ){
$lang_sitems = array(
  'Trinket'   => $isaddition->itemstats_get_header_Icon($trinketitems['Mage'], "middle",$user->lang['is_Trinket']), 
  'AQ_Mount'  => $isaddition->itemstats_get_header_Icon($aqmountitems['Blue'], "middle",$user->lang['is_aqmount']),
  'AQ_Book'   => $isaddition->itemstats_get_header_Icon($aqbookitems['Mage'][1], "middle",$user->lang['is_aqbook']),
  'Atiesh'    => $isaddition->itemstats_get_header_Icon($atieshitems[4], "middle",$atieshitems[4]),
  );
}else{
  $lang_sitems['Trinket']   = $user->lang['is_Trinket']; 
  $lang_sitems['AQ_Mount']  = $user->lang['is_aqmount'];
  $lang_sitems['AQ_Book']   = $user->lang['is_aqbook'];
  $lang_sitems['Atiesh']    = $user->lang['is_atiesh'];
}

$time_end = ISgetmicrotime();

$tpl->assign_vars(array(
    'F_MEMBERS'       => 'specialitems.php'.$SID,

    'L_FILTER'        => $user->lang['filter'],
    'L_NAME'          => $user->lang['name'],
    'L_CLASS'         => $user->lang['class'],
    'L_RANK'          => $user->lang['rank'],
    'L_CURRENT'       => $user->lang['current'],
    'L_TOTAL'         => $user->lang['header_total'],
    'L_VERSION'       => $pm->get_data('itemspecials', 'version'),
    
    'L_TRINKET'       => $lang_sitems['Trinket'],
    'L_AQMOUNT'       => $lang_sitems['AQ_Mount'],
    'L_AQBOOK'        => $lang_sitems['AQ_Book'],
    'L_ATIESH'				=> $lang_sitems['Atiesh'],

    'O_NAME'          => $current_order['uri'][0],
    'O_CLASS'         => $current_order['uri'][1],
    'O_RANK'          => $current_order['uri'][3],
    'O_CURRENT'       => $current_order['uri'][2],
    'O_TOTAL'         => $current_order['uri'][5],

    'URI_ADDON'       => $uri_addon,
    'U_LIST_MEMBERS'  => 'specialitems.php' . $SID . '&amp;',
    'ITEMSTATS_TRUE'  => ( $conf['itemstats'] == 1) ? true : false,
    'ICON_WIDTH'      => $conf['imgwidth'],
    'ICON_HEIGHT'     => $conf['imgheight'],

    'S_NOTMM'         => true,
    
    'SHOW_FOOTER_STAT'=> ( $conf['si_itemstatus_show'] == 1) ? true : false,
    'SHOW_EXEC_TIME'  => ( $conf['is_exec_time'] == 1) ? true : false,
    'SHOW_CURR_DKP'   => ( $conf['si_points'] == 1) ? true : false,
    'SHOW_TOTAL_DKP'  => ( $conf['si_total'] == 1) ? true : false,
    'SHOW_USR_RANK'   => ( $conf['si_rank'] == 1) ? true : false,
    'SHOW_USR_CLASS'  => ( $conf['si_class'] == 1) ? true : false,
    'SHOW_CLSS_IMG'   => ( $conf['si_cls_icon'] == 1) ? true : false,
    'SHOW_TRINKET'    => ( $conf['si_bwltrinket'] == 1) ? true : false,
    'SHOW_AQ_MOUNT'   => ( $conf['si_aqmount'] == 1) ? true : false,
    'SHOW_AQ_BOOK'    => ( $conf['si_aqbook'] == 1) ? true : false,
    'SHOW_ATIESH'	    => ( $conf['si_atiesh'] == 1) ? true : false,
    'L_CLOSE'         => $user->lang['is_button_close'],
    'L_AJAX_LOADING'  => $user->lang['is_loading'],
    'L_ABOUT_HEADER'	=> $user->lang['is_dialog_header'],
    'L_EXEC_TIME'     => $user->lang['is_exec_time'],
    'EXEC_TIME'       => round(($time_end - $time_start),2) . " ".$user->lang['is_seconds'],
    'LISTMEMBERS_FOOTCOUNT' => $footcount_text)
);

if ($conf['colouredcls'] == 1)
		{
			$extra_css = "";
			$extra_css_file = $eqdkp_root_path . $pm->get_data('itemspecials', 'template_path') . $user->style['template_path'] . "/stylesheet.css";
			
			if (file_exists($extra_css_file))
			{
				$filehandle = fopen($extra_css_file, "r");
				while (!feof($filehandle)) {
					$extra_css .= fgets($filehandle);
				}
				fclose ($filehandle);
			}
		}

$eqdkp->set_vars(array(
    'page_title'    => sprintf($user->lang['title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['is_title_specialitems'],
    'template_path' 	  => $pm->get_data('itemspecials', 'template_path'),
    'template_file' => 'specialitems.html',
    'extra_css'		=> $extra_css,
    'display'       => true)
);

?>