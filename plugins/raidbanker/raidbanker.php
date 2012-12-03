<?php
/******************************
 * EQdkp RaidBanker Plugin
 * Copyright 2005 - 2006
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * raidbanker.php
 ******************************/
 
define('EQDKP_INC', true);
$eqdkp_root_path = './../../';
include_once('includes/functions.php');

// The f..stup...IE...Fix... i HATE it!! I HATE IT!!!!!!
 if ((ereg("MSIE 6\.0",$_SERVER['HTTP_USER_AGENT'])) 
 		or (ereg("MSIE 5\.5",$_SERVER['HTTP_USER_AGENT'])) 
 		or (ereg("MSIE 5\.",$_SERVER['HTTP_USER_AGENT'])) 
 		or (ereg("MSIE 4\.",$_SERVER['HTTP_USER_AGENT']))) {
 		$browser = 'MSIE'; 
 }else{
 		$browser = 'other';
 } 
// end of hack


// set the summ to nil
$rb_sum_gold = $rb_sum_silver = $rb_sum_copper = 0;

if (!$pm->check(PLUGIN_INSTALLED, 'raidbanker')) { message_die('The Raid Banker plugin is not installed.'); }
global $table_prefix;

$user->check_auth('u_raidbanker_view');
$pm->get_data('raidbanker', 'template_path');

//set local
setlocale (LC_TIME, $user->lang['rb_local_format']);

// get the banker data
$sql = "SELECT rb_char_name, rb_char_money, rb_date, rb_bank_mainchar, rb_bank_note from ".RB_CHARS_TABLE." ORDER BY rb_char_name";
if ( !($chars_result = $db->query($sql)) ){ message_die($user->lang['lang_couldnt_char'], '', __FILE__, __LINE__, $sql); }

// check if Itemstats are enabled
if ($conf['rb_itemstats'] == 1){
include_once($conf['rb_is_path']);
$item_stats = new ItemStats();
  if ($_GET["refresh"]!= "")
  {
	 $item_stats->updateItem(urlencode(urldecode($_GET["refresh"])));
  }
}

// the data language
if ($conf['rb_list_lang']){
  $filterthin = GetFilterValues($conf['rb_list_lang']);
}else{
  $filterthin = GetFilterValues('english');
}

$bankers_count = $db->num_rows($chars_result);
$i = 0;
while ( $char = $db->fetch_record($chars_result) )
{
      $rb_money[$i]     = $char['rb_char_money'];
      $i++;
    $tpl->assign_block_vars('chars_row', array(
        'ROW_CLASS'     => $eqdkp->switch_row_class(),
        'NAME'          => $char['rb_char_name'],
        'NOTE'          => $char['rb_bank_note'],
        'MAINCHAR'      => ( $char['rb_bank_mainchar'] != "" ) ? "(".$char['rb_bank_mainchar'].")" : '',
        'MAINCHAR2'     => ( $char['rb_bank_mainchar'] != "" ) ? "".$char['rb_bank_mainchar']."" : '',
        'SELECT'        => ( $_GET['banker'] == $char['rb_char_name']) ? "selected=selected" : "",
        'GOLD'          => round($char['rb_char_money']/10000),
        'SILVER'        => substr(round($char['rb_char_money']/100), -2),
        'COPPER'        => substr(round($char['rb_char_money']), -2),
        'UPDATE'        => strftime($user->lang['rb_date_format'], $char["rb_date"]),
        'BANKER_LINK'   => "raidbanker.php" . $SID . "&amp;" . "banker=" . $char['rb_char_name']
        )
    );
}
$db->free_result($chars_result);

foreach($filterthin['type'] as $type_key => $type_value) {
// filter infos
$tpl->assign_block_vars('type_filter', array(
        'name'        => $type_value,
        'select'      => ( $_GET['type'] == $type_value) ? "selected=selected" : ""
        )
    );
}

// sort by command:
if($_GET["sort"] == "type"){
    $sortby = "rb_item_type ASC";
  }elseif ($_GET["sort"] == "qty"){
    $sortby = "rb_item_amount ASC";
  }elseif ($_GET["sort"] == "name"){
    $sortby = "rb_item_name ASC";
  }elseif ($_GET["sort"] == "banker"){
    $sortby = "rb_char_name ASC";
  }elseif ($_GET["sort"] == "priority"){
    $sortby = "rb_status DESC";
  }
  
// SQL Construct
    $sql = "SELECT sum(rbb.rb_item_amount) as qty, rbb.rb_item_name, rbb.rb_item_type, rbb.rb_item_amount,
            rbb.rb_char_name, rbb.rb_item_id, rba.rb_char_money, rba.rb_item_dkp, rba.rb_status
            FROM (" .RB_BANK_TABLE. " rbb LEFT JOIN " .RB_ACTIONS_TABLE." rba 
            ON rbb.rb_item_name = rba.rb_item_name)";
    if ($_GET["banker"] or $_GET['type'] or $_GET['priority']){
      $sql .= " WHERE";
    }
    if ($_GET["banker"]){
      $sql .= " rbb.rb_char_name = '".addslashes($_GET["banker"])."'";
    }
    if ($_GET["banker"] and $_GET['type']){
      $sql .= " AND ";
    }
    if ($_GET['type']){
      $sql .= " rbb.rb_item_type = '".addslashes($_GET['type'])."'";
    }
    if (($_GET['type'] and $_GET['priority']) or ($_GET['banker'] and $_GET['priority'])){
      $sql .= " AND ";
    }
    if($_GET['priority']){
      $sql .= " rba.rb_status = '".addslashes($_GET['priority'])."'";
    }
    if ($_GET["banker"]){
      $sql .= " GROUP BY rbb.rb_item_name, rbb.rb_item_type, rbb.rb_char_name";
    }else{
    	if ($conf['rb_oldstyle'] == 1){
    		$sql .= " GROUP BY rbb.rb_item_name, rbb.rb_item_type, rbb.rb_char_name";
    	}else{
     		$sql .= " GROUP BY rbb.rb_item_name";
    	}
    }
    if ($sortby){
      $sql .= " ORDER BY $sortby, rbb.rb_char_name ASC";
    } else {
      $sql .= " ORDER BY rbb.rb_item_rarity DESC, rbb.rb_item_name ASC, rbb.rb_char_name ASC";
    }
if ( !($items_result = $db->query($sql)) )
{
    message_die($user->lang['lang_couldnt_info'], '', __FILE__, __LINE__, $sql);
}

while ( $item = $db->fetch_record($items_result) )
{

  if ($conf['rb_itemstats'] == 1){
	   $link = $item_stats->getItemLink(stripslashes($item['rb_item_name']));
	   $name = $item_stats->getItemName(urldecode(urlencode($item['rb_item_name'])),$conf['rb_is_cache']);
	$target = "target=\"_blank\"";
	if (trim($link) == "")
	{
		$link = "raidbanker.php" . $SID . "&amp;". "refresh=" . $name;
		$target = "";
	}
	} else {
	$name = $item['rb_item_name'];
  }
  $itemidrb = $item['rb_item_id'];
	
    if ($conf['rb_itemstats'] == 1){
      $rb_dec_name = itemstats_decorate_name($name);
    } else {
      $rb_dec_name = $name;
    }
    
    $sortthing = "raidbanker.php".$SID."&amp;"."sort=";
    $prio_name = array(
      0   => $user->lang['rb_prio_0'],
      1   => $user->lang['rb_prio_1'],
      2   => $user->lang['rb_prio_2'],
      3   => $user->lang['rb_prio_3'],
      4   => $user->lang['rb_prio_4']
    );
    
    $tpl->assign_block_vars('items_row', array(
        'ROW_CLASS'    => $eqdkp->switch_row_class(),
        'QTY'          => $item['qty'],
        'PRIO'         => ( $item['rb_status'] != "" ) ? $item['rb_status'] : 0,
        'GES_COUNT'    => $item['rb_item_amount'],
        'PRIONAME'     => ( $item['rb_status'] != "" ) ? $prio_name[$item['rb_status']] : $prio_name[0],
        'DKP_VALUE'    => ( $item['rb_item_dkp'] != "" ) ? $item['rb_item_dkp'] : 0,
        'GOLD_VALUE'   => ( $item['rb_char_money'] != "") ? round($item['rb_char_money']/10000) : 0,
        'SILVER_VALUE' => ( $item['rb_char_money'] != "") ? substr(round($item['rb_char_money']/100), -2) : 0,
        'COPPER_VALUE' => ( $item['rb_char_money'] != "") ? substr(round($item['rb_char_money']), -2) : 0,
        'NAME'         => $rb_dec_name,
        'REALNAME'     => $item['rb_item_name'],
        'LINK'         => $link,
        'TYPE'         => $item["rb_item_type"],
        'BANKER'       => ($_GET['banker'] || $conf['rb_oldstyle'] == 1) ? $item["rb_char_name"] : '--',
        'TARGET'       => $target,
        )
    );
  if ($conf['rb_show_tooltip'] == 1){
    	$sql_got = "SELECT * FROM ". RB_BANK_REL_TABLE ." WHERE rb_item_name='". addslashes($item['rb_item_name']) ."' AND rb_action = 'got'";
    $result_got = $db->query($sql_got);
    		
		$sql_spent = "SELECT * FROM ". RB_BANK_REL_TABLE ." WHERE rb_item_name='". addslashes($item['rb_item_name']) ."' AND rb_action = 'spent'";
		$result_spent = $db->query($sql_spent);	
    // start the item lists
	while($data_got = $db->fetch_record($result_got))
		{
		  if ($data_got['rb_item_dkp'] > 0){
        $monvalue = $data_got['rb_item_dkp']." ".$user->lang['rb_dkp'];
      }elseif ($data_got['rb_char_money'] > 0 ){
        $monvalue =  round($data_got['rb_char_money']/10000)."<b>".$user->lang['lang_g']."</b> ".substr(round($data_got['rb_char_money']/100), -2)."<b>".$user->lang['lang_s']."</b> ".substr(round($data_got['rb_char_money']), -2)."<b>".$user->lang['lang_c']."</b>";
      }
			$tpl->assign_block_vars('items_row.items_got', array(
				'NAME'		    => $data_got['rb_char_name'],
				'NOTHING'     => ( $data_got['rb_char_name'] == "" ) ? true : false,
				'QTY'         => $data_got['rb_qty'],
				'MONEY'       => $monvalue
        ));	
		}
	while($data_spent = $db->fetch_record($result_spent))
		{
		 if ($data_spent['rb_item_money'] == "0" or $data_spent['rb_item_money'] == ""){
        $monvalue = $data_spent['rb_item_dkp']." ".$user->lang['rb_dkp'];
      }elseif ($data_spent['rb_item_dkp'] == "0" or $data_spent['rb_item_dkp'] == ""){
        $monvalue =  round($data_spent['rb_char_money']/10000)."<b>".$user->lang['lang_g']."</b> ".substr(round($data_spent['rb_char_money']/100), -2)."<b>".$user->lang['lang_s']."</b> ".substr(round($data_spent['rb_char_money']), -2)."<b>".$user->lang['lang_c']."</b>";
      }
			$tpl->assign_block_vars('items_row.items_spent', array(
				'NAME'		    => $data_spent['rb_char_name'],
				'QTY'         => $data_spent['rb_qty'],
				'MONEY'       => $monvalue
        ));	
		}
		} // end of tooltip if
} // end of while


// Free the whole shit
$db->free_result($items_result);

    // Calculate the total money
    $rb_summ_all = @array_sum($rb_money);
    $rb_sum_copper =  round($rb_summ_all);
    $rb_sum_silver =  round($rb_summ_all/100);
    $rb_sum_gold =  round($rb_summ_all/10000);      

// the rest of the stupid IE hack:
if ($browser == 'MSIE'){
	$icon_info = 'images/ie/info.gif';
	$icon_bubble = 'images/ie/bubble.gif';
	$icon_prio2 = 'gif';
	$icon_prio1 = 'ie/';
}else{
	$icon_info = 'images/info.png';
	$icon_bubble = 'images/bubble.png';
	$icon_prio2 = 'png';
	$icon_prio1 = '';
}
// end of thing

  $tpl->assign_vars(array(
      'ICON_PRIO2'						 => $icon_prio2,
      'ICON_PRIO1'						 => $icon_prio1,
      'ICON_INFO'							 => $icon_info,
      'ICON_BUBBLE'						 => $icon_bubble, 
      'U_RAIDBANKER'           => $eqdkp_root_path . "plugins/raidbanker/raidbanker.php" . $SID,
      'Bank_Items'             => $user->lang['rb_Bank_Items'],
      'Lang_Banker'            => $user->lang['rb_Banker'],
      'Lang_QTY'               => $user->lang['rb_Bank_QTY'],
      'Lang_Type'              => $user->lang['rb_Bank_Type'],
      'Lang_Name'              => $user->lang['rb_Item_Name'],
      'Lang_Update'            => $user->lang['rb_Update'],
      'L_ALL_BANKERS'          => $user->lang['rb_AllBankers'],
      'L_TOT_BANKERS'          => $user->lang['rb_TotBankers'],
      'L_RB_NA'                => $user->lang['rb_not_avail'],
      'L_ALL_SELECTED'         => $user->lang['rb_all_Banker'],
      'L_PRIO'                 => $user->lang['rb_priority'],
      'L_SPENT_BY'             => $user->lang['rb_char_spent'],
			'L_GOT_BY'               => $user->lang['rb_char_got'],
			'L_TOTAL_AMOUNT'         => $user->lang['rb_total_amount'],
			'L_DKP_VALUE'            => $user->lang['rb_dkp_value'],
			'L_GOLD_VALUE'           => $user->lang['rb_gold_value'],
      'L_VERSION'              => $pm->get_data('raidbanker', 'version'),
      'L_FILTER_PRIO'          => $user->lang['rb_filter_prio'],
      'L_PRIO0'                => $user->lang['rb_prio_0'],
      'L_PRIO1'                => $user->lang['rb_prio_1'],
      'L_PRIO2'                => $user->lang['rb_prio_2'],
      'L_PRIO3'                => $user->lang['rb_prio_3'],
      'L_PRIO4'                => $user->lang['rb_prio_4'],
      'L_COPPER'               => $user->lang['lang_c'],
      'L_SILVER'               => $user->lang['lang_s'],
      'L_GOLD'                 => $user->lang['lang_g'],
      'L_MAINCHAR'             => $user->lang['rb_mainchar_out'],
      'L_NOTE'                 => $user->lang['rb_note_out'],
      'L_AJAX_LOADING'         => $user->lang['rb_loading'],
      'L_CLOSE'                => $user->lang['rb_close'],
    	'L_ABOUT_HEADER'				 => $user->lang['rb_dialog_header'],
       
      'L_FILTER_TYPE'          => $user->lang['rb_filter_type'],
      'L_FILTER_BANK'          => $user->lang['rb_filter_banker'],
      'filter_select_no'       => ( $_GET['type'] == "") ? "selected=selected" : "",
      'chars_SELECT_NO'        => ( $_GET['banker'] == "") ? "selected=selected" : "",
      
      'SHOW_NO_BANKERS'        => ( $conf['rb_no_bankers'] == 1 ) ? true : false,
      'SHOW_NO_MONEY'          => ( $conf['rb_show_money'] == 1 ) ? true : false,
      'SHOW_INFO_TOOLTIP'      => ( $conf['rb_show_tooltip'] == 1 ) ? true : false,
      'RB_GOLD'                => $rb_sum_gold,
      'RB_SILVER'              => substr($rb_sum_silver, -2),
      'RB_COPPER'              => substr($rb_sum_copper, -2),
      
      'QTY_LINK'               => $sortthing . "qty",
      'TYPE_LINK'              => $sortthing . "type",
      'NAME_LINK'              => $sortthing . "name",
      'BANKER_LINK'            => $sortthing . "banker",
      'PRIO_LINK'              => $sortthing . "priority",
      
      'S_PRIORITY0'            => ( $_GET['priority'] == 0) ? "selected=selected" : "",
      'S_PRIORITY1'            => ( $_GET['priority'] == 1) ? "selected=selected" : "",
      'S_PRIORITY2'            => ( $_GET['priority'] == 2) ? "selected=selected" : "",
      'S_PRIORITY3'            => ( $_GET['priority'] == 3) ? "selected=selected" : "",
      'S_PRIORITY4'            => ( $_GET['priority'] == 4) ? "selected=selected" : ""
	     )
  );
  
  $eqdkp->set_vars(array(
	   'page_title'        => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['raidbanker_title'],
	   'template_path' 	   => $pm->get_data('raidbanker', 'template_path'),
	   'template_file'     => 'bank.html',
	   'display'           => true,
	   )
  );
?>