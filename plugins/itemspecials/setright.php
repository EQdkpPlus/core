<?php
/******************************
 * EQdkp ItemSpecials Plugin
 * (c) 2006 - 2007 by WalleniuM [Simon Wallmann]
 * http://www.wallenium.de   
 * ------------------
 * setright.php
 * Changed: January 10, 2007
 * 
 ******************************/
 
define('EQDKP_INC', true);
define('PLUGIN', 'itemspecials');
$eqdkp_root_path = '../../';
include_once($eqdkp_root_path . 'common.php');
include_once('include/additional_config.php');
include_once('include/functions.php');

$time_start = ISgetmicrotime();

global $table_prefix;
if (!defined('IS_CONFIG_TABLE')) { define('IS_CONFIG_TABLE', $table_prefix . 'itemspecials_config'); }
if (!defined('IS_PLUGIN_TABLE')) { define('IS_PLUGIN_TABLE', $table_prefix . 'itemspecials_plugins'); }

$sql = 'SELECT * FROM ' . IS_CONFIG_TABLE;
if (!($settings_result = $db->query($sql))) { message_die($user->lang['is_sqlerror_config'], '', __FILE__, __LINE__, $sql); }
while($roww = $db->fetch_record($settings_result)) {
  $conf[$roww['config_name']] = $roww['config_value'];
}

include_once('include/data/'.$conf['locale'].'/set.php');

// Plugin Code
$sql = "SELECT * FROM " . IS_PLUGIN_TABLE. " WHERE plugin_installed='1' LIMIT 1";
if (!($plugg_result = $db->query($sql))) { message_die($user->lang['is_sqlerror_plugin'], '', __FILE__, __LINE__, $sql); }
  $plugg = $db->fetch_record($plugg_result);
  $plugin_name = 'plugins/'.$plugg['plugin_path'].'.php';
  if ($plugg['plugin_path']){
    include($plugin_name);
  }

$classLanguage = GetClassLanguage(); // Get the locale of the Classnames!

if (!function_exists(CalculateSetRight)){
  message_die($user->lang['is_no_plugins_inst']);
}

if ($conf['nonset_set'] == 1){
  if (!defined('IS_NONSET_TABLE')) { define('IS_NONSET_TABLE', $conf['nonsettable']); }
  if (!defined('IS_SET_TABLE')) { define('IS_SET_TABLE', $conf['settable']); }
  $NSitemtable  = IS_NONSET_TABLE; // NonSet Items
  $Sitemtable   = IS_SET_TABLE; // SetItems
} else{
  $NSitemtable  = ITEMS_TABLE; // NonSet Items
  $Sitemtable   = ITEMS_TABLE; // SetItems
}

// get the stupid tiers
$array_name1 = "setitems_Tier1";
$array_name2 = "setitems_Tier2";
$array_name3 = "setitems_Tier3";
//build up the array
$kacknoob = array_merge_recursive($$array_name1, $$array_name2, $$array_name3);

if (!$pm->check(PLUGIN_INSTALLED, 'itemspecials')) { message_die($user->lang['is_not_installed']); }

$user->check_auth('u_setright_view');

if ($conf['itemstats'] == true){
  include_once($eqdkp_root_path.'itemstats/eqdkp_itemstats.php');
}

$sort_order = array(
    0 => array('member_name', 'member_name desc'),
    1 => array('member_level desc', 'member_level'),
    2 => array('member_class', 'member_class desc'),
    3 => array('rank_name', 'rank_name desc'),
    4 => array('class_armor_type', 'class_armor_type desc'),
);

$current_order = switch_order($sort_order);

$cur_hash = hash_filename("setright.php");

    $s_compare = false;

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


        } elseif ( $temp_filter == "none" ) {

            $temp_filter = "";
            $query_by_armor = 0;
            $query_by_class = 0;

       } else {

            $query_by_class = 1;
            $query_by_armor = 0;
            $id = $temp_filter;
       }

    }

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

    // Build SQL query based on GET options
    $sql = 'SELECT m.member_id, m.member_name, m.member_level, m.member_status,
                   (m.member_earned-m.member_spent+m.member_adjustment) AS member_current, m.member_earned,
                   r.rank_name, r.rank_hide, r.rank_prefix, r.rank_suffix,
                   c.class_name AS member_class,
                   c.class_armor_type AS armor_type,
                   c.class_min_level AS min_level,
                   c.class_max_level AS max_level
            FROM ' . MEMBERS_TABLE . ' m, ' . MEMBER_RANKS_TABLE . ' r, ' . CLASS_TABLE . ' c
            WHERE c.class_id = m.member_class_id
            AND (m.member_rank_id = r.rank_id)';
            
            if ($conf['hidden_groups'] == 1){
          $sql .= " AND r.rank_hide = '0' ";
        }
        if ($conf['hide_inactives'] == 1){
          $sql .= "AND m.member_status != 1 ";
        }
            
    if ( !empty($_GET['rank']) )
    {
        $sql .= " AND r.rank_name='" . urldecode($_GET['rank']) . "'";
    }

    if ( $query_by_class == '1' )
    {
        //$sql .= " AND m.member_class_id =  $id";
        $sql .= " AND c.class_name =  '$id'";

    }

    if ( $query_by_armor == '1' )
    {
        $sql .= " AND c.class_armor_type =  '". ucwords(strtolower($temp_filter))."'";
    }

    $sql .= ' ORDER BY '.$current_order['sql'];

    if ( !($members_result = $db->query($sql)) )
    {
        message_die('Could not obtain member information', '', __FILE__, __LINE__, $sql);
    }

    // *********************************************
    // PLUGIN API
    // collect all static  Data for the IS PLUGIN-API

      $raidcount_sql = "SELECT COUNT(*) AS anzahl FROM ".RAIDS_TABLE;
      $raidcount = $db->query_first($raidcount_sql);
      
      $class_array = "classname";
      foreach ($$class_array as $value) {
        $cct = 'SELECT COUNT(member_name), c.class_name AS member_class, 
                FROM ' . MEMBERS_TABLE . ' m, ' . CLASS_TABLE . ' c
                WHERE c.class_id = m.member_class_id AND class_name='.$value;
        $membersperclass[$value] = $db->query_first($cct);
      }
      
      // The Count per Raid... thats a bit tricky...
      $raidid_sql = "SELECT MAX(raid_id) AS anzahl FROM ".RAIDS_TABLE;
      $raidid = $db->query_first($raidid_sql);
      
      for ($i = 1; $i <= $raidid; $i++) {
        $countperraid_sql = "SELECT COUNT(*) AS anzahl FROM ".RAID_ATTENDEES_TABLE." WHERE raid_id=".$i;
        $countperraid[$i] = $db->query_first($countperraid_sql);
      }

    // END OF PLUGIN API *************************
    
    
    while ( $row = $db->fetch_record($members_result) )
    {
    // *********************************************
    // PLUGIN API
    // collect all  Data per Member for the IS PLUGIN-API
      if ($conf['nonset_set'] == 1){
        $itemcount_ns_sql = "SELECT COUNT(*) AS anzahl FROM `".$NSitemtable."` WHERE item_buyer='".$row['member_name']."'";
        $itemcount_s_sql = "SELECT COUNT(*) AS anzahl FROM `".$Sitemtable."` WHERE item_buyer='".$row['member_name']."'";
        $itemcount_ns = $db->query_first($itemcount_ns_sql);
        $itemcount_s = $db->query_first($itemcount_s_sql);
        $itemcount = $itemcount_ns + $itemcount_s;
      } else{
        $itemcount_sql = "SELECT COUNT(*) AS anzahl FROM `".$NSitemtable."` WHERE item_buyer='".$row['member_name']."'";
        $itemcount = $db->query_first($itemcount_sql);
      }
    // END OF PLUGIN API *************************
      
        // Figure out the rank search URL based on show and filter
        $u_rank_search  = 'setright.php' . $SID . '&amp;rank=' . urlencode($row['rank_name']);
        $u_rank_search .= ( ($eqdkp->config['hide_inactive'] == 1) && (!$show_all) ) ? '&amp;show=' : '&amp;show=all';
        $u_rank_search .= ( $filter != 'none' ) ? '&amp;filter=' . $filter : '';


        if ( member_display($row) )
        {
            $member_count++;
            $setvals = getStats($row['member_name'], $row['member_class']);
            // *********************************************
            // PLUGIN API
            $calc_data = array(
              'raidcount'       => $setvals[0],
              'itemcount'       => $setvals[1],
              'membername'      => $row['member_name'],
              'class'           => $row['member_class'],
              'member_id'       => $row['member_id'],
              'raid_total'      => $raidcount,
              'dkp_total'       => $row['member_earned'],
              'current_dkp'     => $row['member_current'],
              'countperclass'   => $membersperclass,
              'itemsumm'        => $itemcount,
              'countperraid'    => $countperraid,
            );
            // END OF PLUGIN API *************************
            
            if (function_exists(CalculateSetRight)){
              $calculation = CalculateSetRight($calc_data, $kacknoob); // insert Plugin-function
            } else{ $calculation = 0; }
            
            $priv_output[$member_count] = array(
                'ROW_CLASS'     => $eqdkp->switch_row_class(),
                'ID'            => $row['member_id'],
                'COUNT'         => $member_count,
                'NAME'          => $row['rank_prefix'] . (( $row['member_status'] == '0' ) ? '<i>' . $row['member_name'] . '</i>' : $row['member_name']) . $row['rank_suffix'],
                'RANK'          => ( !empty($row['rank_name']) ) ? (( $row['rank_hide'] == '1' ) ? '<i>' . '<a href="'.$u_rank_search.'">' . stripslashes($row['rank_name']) . '</a>' . '</i>'  : '<a href="'.$u_rank_search.'">' . stripslashes($row['rank_name']) . '</a>') : '&nbsp;',
                'LEVEL'         => ( $row['member_level'] > 0 ) ? $row['member_level'] : '&nbsp;',
                'CLASS'         => ( !empty($row['member_class']) ) ? $row['member_class'] : '&nbsp;',
                'CLASSIMG'      => ( !empty($row['member_class']) ) ? convert_Classname($row['member_class'], $classLanguage, 'from') : '&nbsp;',
                'ARMOR'         => ( !empty($row['armor_type']) ) ? $row['armor_type'] : '&nbsp;',
                'RAIDS'         => $setvals[0],
                'SETITEMS'      => ($setvals[1]== 0 ? 1 : $setvals[1]),
                'GREED'         => $calculation,
                'U_VIEW_MEMBER' => $eqdkp_root_path.'viewmember.php' . $SID . '&amp;' . URI_NAME . '='.$row['member_name']
            );
            $u_rank_search = '';
        }
    }
    if ($priv_output){
      foreach($priv_output as $values)
      {
        $values['ROW_CLASS'] = $eqdkp->switch_row_class();
        $tpl->assign_block_vars('members_row', $values);
      }
    }else{
        $values['ROW_CLASS'] = $eqdkp->switch_row_class();
        $tpl->assign_block_vars('members_row', '');
    }

    $uri_addon  = ''; // Added to the end of the sort links
    $uri_addon .= '&amp;filter=' . urlencode($filter);
    $uri_addon .= ( isset($_GET['show']) ) ? '&amp;show=' . $_GET['show'] : '';

    if ( ($eqdkp->config['hide_inactive'] == 1) && (!$show_all) )
    {
        $footcount_text = sprintf($user->lang['listmembers_active_footcount'], $member_count,
                                  '<a href="setright.php' . $SID . '&amp;' . URI_ORDER . '=' . $current_order['uri']['current'] . '&amp;show=all" class="rowfoot">');
    }
    else
    {
        $footcount_text = sprintf($user->lang['listmembers_footcount'], $member_count);
    }
    $db->free_result($members_result);

$time_end = ISgetmicrotime();

$tpl->assign_vars(array(
    'F_MEMBERS' => 'setright.php'.$SID,
    'V_SID'     => str_replace('?' . URI_SESSION . '=', '', $SID),

    'L_FILTER'        => $user->lang['filter'],
    'L_NAME'          => $user->lang['name'],
    'L_RANK'          => $user->lang['rank'],
    'L_LEVEL'         => $user->lang['level'],
    'L_CLASS'         => $user->lang['class'],
    'L_ARMOR'         => $user->lang['armor'],
    'L_SETITEMS'      => $user->lang['is_count_setitems'],
    'L_RAIDSCOUNT'    => $user->lang['is_count_raids'],
    'L_GREED'         => $user->lang['is_tab_setright'],
    'L_VERSION'       => $pm->get_data('itemspecials', 'version'),

    'SHOW_EXEC_TIME'  => ( $conf['is_exec_time'] == 1) ? true : false,
    'SHOW_USR_LEVEL'  => ( $conf['sr_points'] == 1) ? true : false,
    'SHOW_USR_RANK'   => ( $conf['sr_rank'] == 1) ? true : false,
    'SHOW_USR_CLASS'  => ( $conf['sr_class'] == 1) ? true : false,
    'SHOW_CLSS_IMG'   => ( $conf['sr_cls_icon'] == 1) ? true : false,

    'O_NAME'       => $current_order['uri'][0],
    'O_LEVEL'      => $current_order['uri'][1],
    'O_CLASS'      => $current_order['uri'][2],
    'O_RANK'       => $current_order['uri'][3],
    'O_ARMOR'      => $current_order['uri'][4],

    'URI_ADDON'      => $uri_addon,
    'U_LIST_MEMBERS' => 'setright.php' . $SID . '&amp;',
    'S_NOTMM'   => true,
    
    'L_CLOSE'         => $user->lang['is_button_close'],
    'L_AJAX_LOADING'  => $user->lang['is_loading'],
    'L_ABOUT_HEADER'	=> $user->lang['is_dialog_header'],
    'L_EXEC_TIME'     => $user->lang['is_exec_time'],
    'EXEC_TIME'       => round(($time_end - $time_start),2) . " ".$user->lang['is_seconds'],

    'LISTMEMBERS_FOOTCOUNT' => ( isset($_GET['compare']) ) ? sprintf($footcount_text, sizeof($compare_ids)) : $footcount_text)
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
    'page_title'    => sprintf($user->lang['title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['is_title_itemrights'],
    'template_path' 	  => $pm->get_data('itemspecials', 'template_path'),
    'template_file' => 'setright.html',
    'extra_css'		=> $extra_css,
    'display'       => true)
);

?>