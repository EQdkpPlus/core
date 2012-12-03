<?php
/******************************
 * EQdkp ItemSpecials Plugin
 * (c) 2006 by WalleniuM [Simon Wallmann]
 * http://www.wallenium.de   
 * ------------------
 * setitems.php
 * Changed: November 28, 2006
 * 
 ******************************/

define('EQDKP_INC', true);
define('PLUGIN', 'itemspecials');
$eqdkp_root_path = '../../';
include_once($eqdkp_root_path . 'common.php');
include_once('./include/functions.php');
include_once('./include/itemstatsadditions.class.php');

$tier_temp = "";
$member_count = 0;
$time_start = ISgetmicrotime();

// the sort filter crap
$sort_order = array(
    0 => array('member_name', 'member_name desc'),
    1 => array('class_name', 'class_name desc'),
    2 => array('member_current desc', 'member_current'),
    3 => array('rank_name', 'rank_name desc')
);

$current_order = switch_order($sort_order);
$cur_hash = hash_filename("setitems.php");

// get the config
global $table_prefix;
if (!defined('IS_CONFIG_TABLE')) { define('IS_CONFIG_TABLE', $table_prefix . 'itemspecials_config'); }
if (!defined('IS_TOLLFREE_ITEMS')) { define('IS_TOLLFREE_ITEMS', $table_prefix . 'itemspecials_items'); }

$sql = 'SELECT * FROM ' . IS_CONFIG_TABLE;
if (!($settings_result = $db->query($sql))) { message_die($user->lang['is_sqlerror_config'], '', __FILE__, __LINE__, $sql); }
while($roww = $db->fetch_record($settings_result)) {
  $conf[$roww['config_name']] = $roww['config_value'];
}

require('include/data/'.$conf['locale'].'/set.php');

if ($conf['nonset_set'] == 1){
  if (!defined('IS_NONSET_TABLE')) { define('IS_NONSET_TABLE', $conf['nonsettable']); }
  if (!defined('IS_SET_TABLE')) { define('IS_SET_TABLE', $conf['settable']); }
  $NSitemtable  = IS_NONSET_TABLE; // NonSet Items
  $Sitemtable   = IS_SET_TABLE; // SetItems
} else{
  $NSitemtable  = ITEMS_TABLE; // NonSet Items
  $Sitemtable   = ITEMS_TABLE; // SetItems
}

// when class is empty:
if ($_GET['class'] == ""){
  $start_page = true;
} else {
  $start_page = false;
}

// Load itemstats if possible
if ($conf['itemstats'] == 1){
  include_once($eqdkp_root_path.'itemstats/eqdkp_itemstats.php');
  $isaddition = new ItemstatsAddition();
	$is_version = ($isaddition->GetItemstatsVersion()) ? true : false;
}

if (!$pm->check(PLUGIN_INSTALLED, 'itemspecials')) { message_die($user->lang['is_not_installed']); }

$user->check_auth('u_setitems_view');

// The Config for the Tier pages
  // this is the config for "all tiers on one page"
    $tier_crap = array("Tier1"  => $conf['set_show_t1'],
                       "Tier2"  => $conf['set_show_t2'],
                       "Tier3"  => $conf['set_show_t3'],
                       "TierAQ" => $conf['set_show_tAQ']);                  
    $tiers_onpage = 0;
    $tier = array('');

// repeat it for all tiers                                    
foreach( $tier_crap as $stupid_crap => $enabled )
{
  // check if the tier should be shown
    if( $enabled == 1 )
    {
      // if it is enabled, set the output.
        array_push($tier, $stupid_crap);    
        $tiers_onpage += $enabled;
    }
}

// The data arrays

$array_tier = "tier_names";
$tier_names = $$array_tier;
$class_array = "classname";
$classes = $$class_array;
$class_count = count($classes);

$classLanguage = GetClassLanguage(); // Get the locale of the Classnames!

// Debug Code
if($_GET['debug'] == 'true'){
	echo $classLanguage;
}
// End of Debug Code

# BEGIN of Classes Hack
if ($conf['locale'] != "en" || $classLanguage != 'english'){
// German Classnames in DB
  if(!isset($_GET['class'])) {
        $class = convert_Classname($classes[0], $classLanguage, 'to');
  } else {
        if(in_array($_GET['class'], convert_class_Array($classes, $class_count, $classLanguage))) {
                $class = $db->escape($_GET['class']);
        } else {
                $class = convert_Classname($classes[0], $classLanguage, 'to');
        }
  }
} else {
// ENGLISH Classnames in DB
  if(!isset($_GET['class'])) {
        $class = $classes[0];
  } else {
        if(in_array($_GET['class'], $classes)) {
                $class = $db->escape($_GET['class']);
        } else {
                $class = $classes[0];
        }
  }
}

  if (strtolower($user->lang['is_user_language']) != 'english' && $classLanguage != 'english'){
    foreach($classes as $cl)
        $tpl->assign_block_vars('CLASSES', array(
                'name'      => convert_Classname($cl, $classLanguage, 'to'),
                'linkname'  => convert_Classname($cl, $classLanguage, 'to'),
                'SELECTED'  => ( convert_Classname($_GET['class'], $classLanguage, 'from') == convert_Classname($cl, $classLanguage, 'from')) ? "selected=selected" : "",
                'SP_SELECT' => ( $_GET['class'] == "") ? "selected=selected" : "",
                'img'       => convert_Classname($cl, $classLanguage, 'from'))
        );
    } elseif (strtolower($user->lang['is_user_language']) != "english" && $classLanguage == 'english'){
      foreach($classes as $cl)
        $tpl->assign_block_vars('CLASSES', array(
                'name'      => convert_Classname($cl, $classLanguage, 'to'),
                'linkname'  => convert_Classname($cl, $classLanguage, 'from'),
                'SELECTED'  => ( convert_Classname($_GET['class'], $classLanguage, 'from') == convert_Classname($cl, $classLanguage, 'from')) ? "selected=selected" : "",
                'SP_SELECT' => ( $_GET['class'] == "") ? "selected=selected" : "",
                'img'       => convert_Classname($cl, $classLanguage, 'from'))
        );
      } elseif (strtolower($user->lang['is_user_language']) == 'english' && $conf['locale'] != "en" && $classLanguage != 'english'){
      foreach($classes as $cl)
        $tpl->assign_block_vars('CLASSES', array(
                'name'      => $cl,
                'linkname'  => convert_Classname($cl,$classLanguage, 'to'),
                'SELECTED'  => ( convert_Classname($_GET['class'], $classLanguage, 'from')) == convert_Classname($cl, $classLanguage, 'from') ? "selected=selected" : "",
                'SP_SELECT' => ( $_GET['class'] == "") ? "selected=selected" : "",
                'img'       => convert_Classname($cl, $classLanguage, 'from'))
        );
      } else {
      foreach($classes as $cl)
        $tpl->assign_block_vars('CLASSES', array(
                'name'      => $cl,
                'linkname'  => $cl,
                'SELECTED'  => ( $_GET['class'] == $cl) ? "selected=selected" : "",
                'SP_SELECT' => ( $_GET['class'] == "") ? "selected=selected" : "",
                'img'       => convert_Classname($_GET['class'], $classLanguage, 'from'))
        );
      }
# END of Classes hack
      
if ($start_page == true && $conf['set_show_index'] == 1){
$tier_total = array('', 8, 8, 9, 5);
// startpage
$tiers_summ = 4;
$tier_ov = array('','Tier1','Tier2', 'Tier3', 'TierAQ');
$tpl->assign_vars(array(
        'STARTPAGE'       => true,
        'ITEMS_TOTAL_T1'        => $tier_total[1],
        'ITEMS_TOTAL_T2'        => $tier_total[2],
        'ITEMS_TOTAL_T3'        => $tier_total[3],
        'ITEMS_TOTAL_AQ'        => $tier_total[4])
        );
        
$sql = "SELECT i.item_id, i.item_name, i.item_buyer
        FROM ((" . $Sitemtable . " i
        INNER JOIN " . MEMBERS_TABLE . " m
        ON i.item_buyer = m.member_name)
        INNER JOIN " . CLASS_TABLE . " c
        ON c.class_id = m.member_class_id)
        UNION SELECT ti.item_id, ti.item_name, ti.item_buyer
        FROM (".IS_TOLLFREE_ITEMS." ti
        INNER JOIN " . MEMBERS_TABLE . " m
        ON ti.item_buyer = m.member_name)
        ORDER BY item_name ASC;";

$itemres = $db->query($sql);
if(!$itemres)
        message_die($user->lang['is_item_info'], "", "", "");

while($row_item = $db->fetch_record($itemres)) {
                $member_items[$row_item['item_buyer']][$row_item['item_name']] = true;
                $member_items[$row_item['item_name']]['item_id'] = $row_item['item_id'];
        }
$sp_sql = "SELECT m.*, (m.member_earned-m.member_spent+m.member_adjustment) AS member_current, member_name, 
		   member_status, r.rank_name, r.rank_hide, r.rank_prefix, r.rank_suffix,
		   c.class_name
        FROM (" . MEMBERS_TABLE . " m INNER JOIN " . CLASS_TABLE . " c
        ON m.member_class_id = c.class_id)
        INNER JOIN ". MEMBER_RANKS_TABLE ." r 
        on m.member_rank_id = r.rank_id ";
    if($conf['hide_inactives'] == 1){
      $sp_sql .= "And m.member_status != 1 ";
    }
    if($conf['hidden_groups'] == 1){
      $sp_sql .= "AND r.rank_hide = '0'";
    }
      $sp_sql .= "ORDER BY ".$current_order['sql'];
$sp_result = $db->query($sp_sql);

while ( $sp_row = $db->fetch_record($sp_result) )
{        
$member_count++;
  for ($i = 1; $i <= $tiers_summ; $i++) {
    $tier_sp[$i] = "";
    $array_name = "setitems_".$tier_ov[$i];
    $items = $$array_name;
    $items = $items[convert_Classname($sp_row['class_name'], $classLanguage, 'from')];

if ($items){
  foreach($items as $key=>$item){
  if (!is_numeric($key)){
    $items[$key] = array('name'  => $key);
  }else{
    $items[$key] = array('name'  => $item);
  }
}
         foreach($items as $key => $item) {
                        if(isset($member_items[$sp_row['member_name']][$item['name']]) && $member_items[$sp_row['member_name']][$item['name']] != 0) {
                          $tier_sp[$i]++;
                        }
        }
        }
        $completed[$i] = round(($tier_sp[$i] / $tier_total[$i]) * 100);
      }
      $temptiersumm = array(
       '1'    => $temptiersumm[1] + $tier_sp[1],
       '2'    => $temptiersumm[2] + $tier_sp[2],
       '3'    => $temptiersumm[3] + $tier_sp[3],
       '4'    => $temptiersumm[4] + $tier_sp[4]);
      
        $footcount_text = sprintf($user->lang['listmembers_footcount'], $member_count);
          $tpl->assign_block_vars('SP_MEMBERS', array(
                        'link'                  => $eqdkp_root_path . "viewmember.php" . $SID . "&amp;name=" . $sp_row['member_name'],
                        'name'                  => $sp_row['member_name'],
                        'ITEMS_COUNT_T1'        => ( !empty($tier_sp[1]) ) ? $tier_sp[1] : 0,
                        'BAR_T1'                => $completed[1]."%",
                        'ITEMS_COUNT_T2'        => ( !empty($tier_sp[2]) ) ? $tier_sp[2] : 0,
                        'BAR_T2'                => $completed[2]."%",
                        'ITEMS_COUNT_T3'        => ( !empty($tier_sp[3]) ) ? $tier_sp[3] : 0,
                        'BAR_T3'                => $completed[3]."%",
                        'ITEMS_COUNT_AQ'        => ( !empty($tier_sp[4]) ) ? $tier_sp[4] : 0,
                        'BAR_AQ'                => $completed[4]."%",
                        'ROW_CLASS'             => $eqdkp->switch_row_class(),
                        'RANK'                  => ( !empty($sp_row['rank_name']) ) ? (( $sp_row['rank_hide'] == '1' ) ? '<i>' . '<a href="'.$u_rank_search.'">' . stripslashes($sp_row['rank_name']) . '</a>' . '</i>'  : '<a href="'.$u_rank_search.'">' . stripslashes($sp_row['rank_name']) . '</a>') : '&nbsp;',
                        'CLASS'                 => ( !empty($sp_row['class_name']) ) ? $sp_row['class_name'] : '&nbsp;',
	        	            'CLASSIMG'              => ( !empty($sp_row['class_name']) ) ? convert_Classname($sp_row['class_name'], $classLanguage, 'from') : '&nbsp;',
	        	            'C_CURRENT'             => color_item($sp_row['member_current']),
                        'C_TOTAL'               => color_item($sp_row['member_earned']),
                        'TOTAL'                 => $sp_row['member_earned'],
                        'CURRENT'               => $sp_row['member_current'])
                );
        }
}else{      
for ($i = 1; $i <= $tiers_onpage; $i++) {
$array_name = "setitems_".$tier[$i];
$items = $$array_name;
$items = $items[convert_Classname($class, $classLanguage, 'from')];

foreach($items as $key=>$item)
// get the stupid icons...

if ($conf['itemstats'] == 1){
        $items[$key] = array(
                'name'          => (!is_numeric($key)) ? $key : $item,
                'realname'			=> $item,
                'item_icon'     => $isaddition->itemstats_decorate_Icon($item, "middle", $is_version),
        );
} else {
 $items[$key] = array(
                'name'          => $item,
                'realname'			=> $item,
                'item_icon'     => html_entity_decode($conf['is_replace'])
        );
}

$sql = "SELECT i.item_id, i.item_name, i.item_buyer
        FROM (" . $Sitemtable . " i
        INNER JOIN " . MEMBERS_TABLE . " m
        ON i.item_buyer = m.member_name)
        INNER JOIN " . CLASS_TABLE . " c
        ON c.class_id = m.member_class_id
        WHERE c.class_name = '" . $class . "'
        UNION SELECT ti.item_id, ti.item_name, ti.item_buyer
        FROM (".IS_TOLLFREE_ITEMS." ti
        INNER JOIN " . MEMBERS_TABLE . " m
        ON ti.item_buyer = m.member_name)
        ORDER BY item_name ASC;";

$itemres = $db->query($sql);
if(!$itemres)
        message_die($user->lang['is_item_info'], "", "", "");

$sql = "SELECT m.*, (m.member_earned-m.member_spent+m.member_adjustment) AS member_current, member_name, 
		   member_status, r.rank_name, r.rank_hide, r.rank_prefix, r.rank_suffix,
		   c.class_name
        FROM (" . MEMBERS_TABLE . " m INNER JOIN " . CLASS_TABLE . " c
        ON m.member_class_id = c.class_id)
        INNER JOIN ". MEMBER_RANKS_TABLE ." r 
        on m.member_rank_id = r.rank_id
        WHERE c.class_name = '" . $class . "' ";
    if($conf['hide_inactives'] == 1){
      $sql .= "And m.member_status != 1 ";
    }
    if($conf['hidden_groups'] == 1){
      $sql .= "AND r.rank_hide = '0'";
    }
      $sql .= "ORDER BY member_name;";

$classres = $db->query($sql);

if(!$classres)
        message_die($user->lang['is_member_info'],"", "", "");
        
    $tpl->assign_block_vars('TIERS_PAGE', array(
            'TIER'            => $tier[$i],
            'SHOW_RING'       => ( $tier[$i] == "Tier3") ? "<td align='center'>".$user->lang['is_ring']."</td>" : '',
            'SHOW_BELT'				=> ( $tier[$i] != "TierAQ") ? "<td align='center'>".$user->lang['is_Belt']."</td>" : '',
            'SHOW_WRIST'			=> ( $tier[$i] != "TierAQ") ? "<td align='center'>".$user->lang['is_Wrist']."</td>" : '',
            'SHOW_HANDS'			=> ( $tier[$i] != "TierAQ") ? "<td align='center'>".$user->lang['is_Hands']."</td>" : '',
            'SHOW_BOOTS'      => "<td align='center'>".$user->lang['is_Boots']."</td>",
            'SHOW_HEAD'       => "<td align='center'>".$user->lang['is_Head']."</td>",
            'SHOW_SHOULDERS'  => "<td align='center'>".$user->lang['is_Shoulders']."</td>",
            'SHOW_CHEST'      => "<td align='center'>".$user->lang['is_Chest']."</td>",
            'SHOW_LEGS'       => "<td align='center'>".$user->lang['is_Legs']."</td>",
            'NO'              => $i,
            'TIER_NAME'       => $tier_names[convert_Classname($class, $classLanguage, 'from')][$tier[$i]])
                );

        $tpl->assign_var('MEMBER_ITEM', true);
        while($row = $db->fetch_record($itemres)) {
        				$itemnametemporary = ($conf['itemstats'] == 1 && $conf['is_correctmode']) ? $isaddition->itemstats_format_name($row['item_name']) : $row['item_name'];
                $member_items[$row['item_buyer']][$itemnametemporary] = true;
                $member_items[$row['item_name']]['item_id'] = $row['item_id'];
        }
        while($row = $db->fetch_record($classres)) {
                $iconhave = array();
                foreach($items as $key => $item) {

                        if(isset($member_items[$row['member_name']][$item['name']]) && $member_items[$row['member_name']][$item['name']] === true) {
                                $iconhave[] = array(
                                        'item_image'    => $item['item_icon'],
                                				'id'						=> urlencode($item['realname']),
                                				'id2'						=> $member_items[$item['name']]['item_id'],
                                );
                        } else {
                                $iconhave[] = array(
                                        'item_image'    => '',
                                        'id'						=> '',
                                        'id2'						=> ''
                                );
                        }
                }
                $tpl->assign_block_vars('TIERS_PAGE.MEMBERS', array(
                        'link'                  => $eqdkp_root_path . "viewmember.php" . $SID . "&amp;name=" . $row['member_name'],
                        'name'                  => $row['member_name'],
                        'MEMBERITEMS_NEED.'     => $iconhave,
                        'ROW_CLASS'             => $eqdkp->switch_row_class(),
                        'RANK'                  => ( !empty($row['rank_name']) ) ? (( $row['rank_hide'] == '1' ) ? '<i>' . '<a href="'.$u_rank_search.'">' . stripslashes($row['rank_name']) . '</a>' . '</i>'  : '<a href="'.$u_rank_search.'">' . stripslashes($row['rank_name']) . '</a>') : '&nbsp;',
                        'CLASS'                 => ( !empty($row['class_name']) ) ? $row['class_name'] : '&nbsp;',
	        	            'CLASSIMG'              => ( !empty($row['class_name']) ) ? convert_Classname($row['class_name'], $classLanguage, 'from') : '&nbsp;',
	        	            'C_CURRENT'             => color_item($row['member_current']),
                        'C_TOTAL'               => color_item($row['member_earned']),
                        'TOTAL'                 => $row['member_earned'],
                        'CURRENT'               => $row['member_current'])
                );
        }
} // for end
} //if startpage end

$uri_addon .= '&amp;filter=none';
$headerimgcls = convert_Classname($_GET['class'], $classLanguage, 'from');
if ($start_page == true && $conf['set_show_index'] == 0){
 $headerimgcls = 'Warrior';
}

if ($start_page == true && $conf['set_show_index'] == 1){
$t1percent = ($temptiersumm[1]) ? round(($temptiersumm[1]/($member_count*8))*100) : 0;
$t2percent = ($temptiersumm[2]) ? round(($temptiersumm[2]/($member_count*8))*100) : 0;
$t3percent = ($temptiersumm[3]) ? round(($temptiersumm[3]/($member_count*9))*100) : 0;
$taqpercent = ($temptiersumm[4]) ? round(($temptiersumm[4]/($member_count*5))*100) : 0;
}


$time_end = ISgetmicrotime();
$tpl->assign_vars(array(
        'CLASSNAME'       => $class,
        'ITEMLINK'				=> $eqdkp_root_path . "viewitem.php" . $SID . "&i=",
        'CLASSWIDTH'      => round(100 / count($classes),0) . "%",
        'U_SET_ITEMS'     => $eqdkp_root_path . "plugins/itemspecials/setitems.php" . $SID,
        'ROW_CLASS'       => $eqdkp->switch_row_class(),
        
        //tier global percentage
        'T1_GLOBAL'       => $temptiersumm[1],
        'T1_GL_PERCENT'   => $t1percent,
        'T2_GLOBAL'       => $temptiersumm[2],
        'T2_GL_PERCENT'   => $t2percent,
        'T3_GLOBAL'       => $temptiersumm[3],
        'T3_GL_PERCENT'   => $t3percent,
        'TAQ_GLOBAL'      => $temptiersumm[4],
        'TAQ_GL_PERCENT'  => $taqpercent,
        
        'SHOW_EXEC_TIME'  => ( $conf['is_exec_time'] == 1) ? true : false,
        'SHOW_CURR_DKP'   => ( $conf['set_points'] == 1) ? true : false,
        'SHOW_TOTAL_DKP'  => ( $conf['set_total'] == 1) ? true : false,
        'SHOW_USR_RANK'   => ( $conf['set_rank'] == 1) ? true : false,
        'SHOW_USR_CLASS'  => ( $conf['set_class'] == 1) ? true : false,
        'SHOW_CLSS_IMG'   => ( $conf['set_cls_icon'] == 1) ? true : false,
        
        'SHOW_OP_CURR_DKP'=> ( $conf['set_op_points'] == 1) ? true : false,
        'SHOW_OP_TOT_DKP' => ( $conf['set_op_total'] == 1) ? true : false,
        'SHOW_OP_USR_RANK'=> ( $conf['set_op_rank'] == 1) ? true : false,
        'SHOW_OP_USR_CLS' => ( $conf['set_op_class'] == 1) ? true : false,
        'SHOW_OP_CLS_IMG' => ( $conf['set_op_cls_icon'] == 1) ? true : false,        
        
        'SHOW_TIER_ONE'   => ( $conf['set_show_t1'] == 1) ? true : false,
        'SHOW_TIER_TWO'   => ( $conf['set_show_t2'] == 1) ? true : false,
        'SHOW_TIER_THREE' => ( $conf['set_show_t3'] == 1) ? true : false,
        'SHOW_TIER_AQ'    => ( $conf['set_show_tAQ'] == 1) ? true : false,
        'SHOW_STARTPAGE'  => ( $conf['set_show_index'] == 1) ? true : false,
        'CLASS_DROPDOWN'  => ( $conf['set_drpdwn_cls'] == 1 ) ? true : false,
        'ITEMSTATS_TRUE'  => ( $conf['itemstats'] == 1) ? true : false,
        'OLDST_ITMLINKS'	=> ( $conf['set_oldLink'] == 1) ? true : false,

        'ICON_WIDTH'      => $conf['imgwidth'],
        'ICON_HEIGHT'     => $conf['imgheight'],
        
        'O_NAME'          => $current_order['uri'][0],
        'O_CLASS'         => $current_order['uri'][1],
        'O_RANK'          => $current_order['uri'][3],
        'O_CURRENT'       => $current_order['uri'][2],
        'U_LIST_MEMBERS'  => 'setitems.php' . $SID . '&amp;',
        'URI_ADDON'       => $uri_addon,
        'IS_LANGUAGE'     => ( strtolower($user->lang['is_user_language']) == "german" ) ? "de" : "en",
        
        'IS_CLASS_IMG'    => $headerimgcls,
        'HEADER_CLS_TXT'	=> ( $headerimgcls != "" ) ? $user->lang['is_header_names'][$headerimgcls]  : $user->lang['is_usermenu_Setitems'],
        'SP_SELECTED'     => ( $_GET['class'] == "" ) ? 'selected=selected' : "",  
        'STARTP_SELECTED' => ( $_GET['tier'] == "") ? "selected=selected" : "",
        'TIER1_SELECTED'  => ( $_GET['tier'] == "Tier1") ? "selected=selected" : "",
        'TIER2_SELECTED'  => ( $_GET['tier'] == "Tier2") ? "selected=selected" : "",
        'TIER3_SELECTED'  => ( $_GET['tier'] == "Tier3") ? "selected=selected" : "",
        'TIERAQ_SELECTED' => ( $_GET['tier'] == "TierAQ") ? "selected=selected" : "",
        
        'LANG_TIER'       => $user->lang['is_Set'],
        'LANG_TIER1'      => $user->lang['is_tier1'],
        'LANG_TIER2'      => $user->lang['is_tier2'],
        'LANG_TIER3'      => $user->lang['is_tier3'],
        'LANG_TIERAQ'     => $user->lang['is_tieraq'],
        'LANG_HOME'       => $user->lang['is_home'],
        'L_CLASS'         => $user->lang['class'],
        'L_RANK'          => $user->lang['rank'],
        'L_CURRENT'       => $user->lang['current'],
        'L_TOTAL'         => $user->lang['header_total'],
        'L_VERSION'       => $pm->get_data('itemspecials', 'version'),
        'L_NAME'          => $user->lang['name'],
        'L_EXEC_TIME'     => $user->lang['is_exec_time'],
        'L_SUMM'          => $user->lang['is_summ'],
        
        'L_CLOSE'         => $user->lang['is_button_close'],
        'L_AJAX_LOADING'  => $user->lang['is_loading'],
        'L_ITEM_INFO'			=> $user->lang['is_item_status'],
        'L_ABOUT_HEADER'	=> $user->lang['is_dialog_header'],
        'EXEC_TIME'       => round(($time_end - $time_start),2) . " ".$user->lang['is_seconds'],
        'FOOTCOUNT'       => $footcount_text
        )
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
        'page_title'    => sprintf($user->lang['title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['is_title_setitems'],
        'template_path' 	  => $pm->get_data('itemspecials', 'template_path'),
        'template_file' => 'setitems.html',
        'extra_css'		=> $extra_css,
        'display'       => true)
);
?>