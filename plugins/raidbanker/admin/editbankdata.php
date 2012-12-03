<?php
/******************************
 * EQdkp RaidBanker Plugin
 * Copyright 2005 - 2006
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * editbankdata.php
 ******************************/

// EQdkp required files/vars
define('EQDKP_INC', true);
define('IN_ADMIN', true);
define('PLUGIN', 'raidbanker');
$eqdkp_root_path = './../../../';
global $table_prefix;
include_once('../includes/functions.php');

// Check user permission
$user->check_auth('a_raidbanker_update');
$rb = $pm->get_plugin('raidbanker');

if ( !$pm->check(PLUGIN_INSTALLED, 'raidbanker') )
{
    message_die('The Raid Banker plugin is not installed.');
}

    if (isset($_POST["MakeMeHappy"])){
      process_insert();
    }

        // delete banker
    if ((isset($_GET["what"])) && ($_GET["what"] == "delete") && isset($_GET['name'])){
		  // Delete banker
		    $db->query('DELETE FROM ' . RB_CHARS_TABLE . " WHERE rb_char_name='" . $_GET['name'] . "'");
		  // Delete content of banker
		    $db->query('DELETE FROM ' . RB_BANK_TABLE . " WHERE rb_char_name ='" . $_GET['name'] . "'");
      // Success message
        $success_message = $user->lang['admin_delete_bank_success'];
        //$this->admin_die($success_message);
    }
    
    // edit the banker
    if ((isset($_GET["what"])) && ($_GET["what"] == "manage") && isset($_GET['name'])){
      $success_message = "";
        $num = 0; // count the items
        
      $sql = "SELECT sum(rbb.rb_item_amount) as qty, rbb.rb_item_name, rbb.rb_item_rarity, rbb.rb_item_type, rbb.rb_item_amount,
            rbb.rb_char_name, rbb.rb_item_id, rba.rb_char_money, rba.rb_item_dkp, rba.rb_status
            FROM (" .RB_BANK_TABLE. " rbb LEFT JOIN " .RB_ACTIONS_TABLE." rba 
            ON rbb.rb_item_name = rba.rb_item_name)
            WHERE rbb.rb_char_name = '".$_GET['name']."' 
      GROUP BY rbb.rb_item_name, rbb.rb_item_type, rbb.rb_char_name ORDER BY rbb.rb_item_rarity DESC, 
      rbb.rb_item_name ASC, rbb.rb_char_name ASC";
        $result = $db->query($sql);
		    while ($row = $db->fetch_record($result))
		    {
		    
		        $tpl->assign_block_vars('items_row', array(
				      'NAME'	        => $row['rb_item_name'],
				      'ROW_CLASS' => $eqdkp->switch_row_class(),
				      'ITEMID'        => $row['rb_item_id'],
              'TYPE'	        => $row['rb_item_type'],
              'AMOUNT'        => $row['qty'],
              'QUALITY'       => $row['rb_item_rarity'],
              'SELECT0'       => ( $row['rb_status'] == "0" ) ? "selected=selected" :"",
              'SELECT1'       => ( $row['rb_status'] == "1" ) ? "selected=selected" :"",
              'SELECT2'       => ( $row['rb_status'] == "2" ) ? "selected=selected" :"",
              'SELECT3'       => ( $row['rb_status'] == "3" ) ? "selected=selected" :"",
              'SELECT4'       => ( $row['rb_status'] == "4" ) ? "selected=selected" :"",
              'GOLD_VALUE'    => round($row['rb_char_money']/10000),
              'SILVER_VALUE'  => substr(round($row['rb_char_money']/100), -2),
              'COPPER_VALUE'  => substr(round($row['rb_char_money']), -2),
              'DKP_VALUE'     => $row['rb_item_dkp'],
              'NUMBER'        => $num)
              );
            $num++;
		    }
		    $db->free_result($result);

  // the char addition Data
    $sql = "SELECT rb_bank_mainchar, rb_bank_note from ".RB_CHARS_TABLE." WHERE rb_char_name ='" . $_GET['name'] . "' LIMIT 1";
    $chars_result = $db->query($sql);
    $char = $db->fetch_record($chars_result);
    $db->free_result($chars_result);

    $tpl->assign_vars(array(
			'RB_IS_UPDATE'           => true,
			'I_NOTE'                 => $char['rb_bank_note'],
			'I_MAINCHAR'             => $char['rb_bank_mainchar'],
			
      'Bank_Items'             => $user->lang['rb_Bank_Items'],
      'Lang_QTY'               => $user->lang['rb_Bank_QTY'],
      'Lang_Type'              => $user->lang['rb_Bank_Type'],
      'lang_quality'           => $user->lang['rb_Bank_Quality'],
      'Lang_Name'              => $user->lang['rb_Item_Name'],
      'Lang_ItemID'            => $user->lang['lang_itemid'],
      'lang_delete'            => $user->lang['rb_delete'],
      'L_BANK_UPDATE'	         => $user->lang['lang_update_data'],
      'CHARNAME'               => $_GET['name'],
      'L_MONEY_VALUE'          => $user->lang['rb_money_val'],
      'L_DKP_VALUE'            => $user->lang['rb_dkp_val'],
      'L_PRIO_0'               => $user->lang['rb_prio_0'],
      'L_PRIO_1'               => $user->lang['rb_prio_1'],
      'L_PRIO_2'               => $user->lang['rb_prio_2'],
      'L_PRIO_3'               => $user->lang['rb_prio_3'],
      'L_PRIO_4'               => $user->lang['rb_prio_4'],
      'L_STATUS'               => $user->lang['rb_priority'],
      'L_MAINCHAR'             => $user->lang['rb_mainchar'],
      'L_NOTE'                 => $user->lang['rb_note'],
      'L_FOUND_ITEMS'	         => $user->lang['lang_found_log'])
		);
    
    $eqdkp->set_vars(array(
	    'page_title'             => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['rb_step1_pagetitle'],
			'template_path' 	       => $pm->get_data('raidbanker', 'template_path'),
			'template_file'          => 'admin/update.html',
			'display'                => true)
    );
    
    }
    
    
    
    function process_insert()
	{

		global $db, $eqdkp, $user, $tpl, $pm;
		global $SID;

		$charName = $_POST["charName"];

		$db->query("DELETE FROM ".RB_BANK_TABLE." WHERE rb_char_name = '".mysql_escape_string($_POST["charName"])."'");
		$tpl->assign_vars(array(
						'L_ACT_PERF' => $user->lang['Lang_actions_performed'],
						'L_USER_LINK'=> $user->lang['rb_user_link'],
						'F_LINK'     => 'editbankdata.php' . $SID
					)
				);
    $tpl->assign_block_vars(
			'logs_row',
			array(
				'DES' => $user->lang['lang_cleared_data']." ".$charName,
				'ROW_CLASS' => $eqdkp->switch_row_class()
			)
		);

  if ($conf['rb_itemstats'] == 1){
		$item_stats = new ItemStats();
		}

    // save the bank mainchar & note
      $sql = "UPDATE ".RB_CHARS_TABLE." SET rb_bank_note='".$_POST["note"]."', rb_bank_mainchar='".$_POST["mainchar"]."' WHERE rb_char_name='".$_POST["charName"]."';";
			$db->query($sql);

		foreach($_POST["item"] as $item)
		{
			if(!isset($item["delete"]))
			{
			if ($conf['rb_itemstats'] == 1){
				  $name_of_item = "'".$item_stats->getItemName(stripslashes($item["name"]),false)."',";
				}else{
          $name_of_item = "'".$item["name"]."',";
        }
			
				$sql  = "INSERT INTO ".RB_BANK_TABLE." (rb_char_name, rb_item_name, rb_item_rarity, rb_item_type, rb_item_amount, rb_item_id) VALUES ";
				$sql .= "('".$_POST["charName"]."',";
				$sql .= "'".$item["name"]."',";
				$sql .= "".$item["quality"].",";
				$sql .= "'".$item["type"]."',";
				$sql .= "".$item["amount"].",";
				$sql .= "".$item["itemid"].")";
				$db->query($sql);
				if ($item["status"] or $item["gold_value"] or $item["dkp_value"]){
				//delete the old crap if there's one:
				$sql  = "DELETE FROM ".RB_ACTIONS_TABLE." WHERE rb_item_name = '".$item["name"]."' LIMIT 1;";
				$db->query($sql);
				// calculate the copper value :)
				$totalmoney = $item["copper_value"] + ($item["silver_value"]*100) + ($item["gold_value"]*10000);				
				// Add the special things to the database				
				$dkptemvalue = ( $item["dkp_value"] ) ? $item["dkp_value"] : 0;
				$moneytemp = ( $totalmoney ) ? $totalmoney : 0;
				$sql  = "INSERT INTO ".RB_ACTIONS_TABLE." (rb_item_name, rb_status, rb_char_money, rb_item_dkp) VALUES ";
				$sql .= "('".$item["name"]."',";
				$sql .= "'".$item["status"]."',";
				$sql .= "".$moneytemp.",";
				$sql .= "".$dkptemvalue.")";
				$db->query($sql);
				}
				
				$tpl->assign_vars(array(
						'L_ACT_PERF' => $user->lang['Lang_actions_performed'],
						'L_USER_LINK'=> $user->lang['rb_user_link'],
						'F_LINK'     => 'editbankdata.php' . $SID
					)
				);
				$tpl->assign_block_vars(
					'logs_row',
					array(
						'DES' => $user->lang['lang_adding_item']." ".stripslashes($item["name"]),
						'ROW_CLASS' => $eqdkp->switch_row_class()
					)
				);
			}
			else
			{
			$tpl->assign_vars(array(
						'L_ACT_PERF' => $user->lang['Lang_actions_performed'],
						'L_USER_LINK'=> $user->lang['rb_user_link'],
						'F_LINK'     => 'editbankdata.php' . $SID
					)
				);
				$tpl->assign_block_vars(
					'logs_row',
					array(
						'DES' => $user->lang['lang_skipped_items']." ".$item["name"],
						'ROW_CLASS' => $eqdkp->switch_row_class()
					)
				);
			}
		} // foreach end

		$eqdkp->set_vars(array(
			'page_title'        => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['rb_step2_pagetitel'],
			'template_path' 	=> $pm->get_data('raidbanker', 'template_path'),
			'template_file'     => 'admin/import_result.html',
			'display'           => true,
			)
		);


	} // end insert

class RaidBanker_Update extends EQdkp_Admin
{
      var $banker = array();
function RaidBanker_Update()
	{

        global $db, $eqdkp, $user, $tpl, $pm, $rb_date_format;
		    global $SID;
		    
		    parent::eqdkp_admin();
            
        $success_message = "";
        
         $sql = "SELECT rb_char_name, rb_char_money, rb_date from ".RB_CHARS_TABLE." ORDER BY rb_char_name";

        $result = $db->query($sql);
		    while ($row = $db->fetch_record($result))
		    {
		        $tpl->assign_block_vars('chars_row', array(
				      'NAME'	=> $row['rb_char_name'],
				      'ROW_CLASS' => $eqdkp->switch_row_class(),
              'UPDATE'	=> strftime($user->lang['rb_date_format'], $row["rb_date"]),
              'DELETE' => ( $user->check_auth('a_raidbanker_update', false) ) ? '(<a href="editbankdata.php'.$SID.'&amp;what=delete&amp;name='.$row['rb_char_name'] . '">' . $user->lang['rb_delete'] . '</a>)' : '',
              'MANAGE' => ( $user->check_auth('a_raidbanker_update', false) ) ? '(<a href="editbankdata.php'.$SID.'&amp;what=manage&amp;name='.$row['rb_char_name'] . '">' . $user->lang['rb_edit'] . '</a>)' : '',
              )
              );
		    }
		    $db->free_result($result);

    $tpl->assign_vars(array(
			'F_EDIT_BANK'            => 'editbankdata.php' . $SID,
			'RB_IS_UPDATE'           => false,
      'Lang_Update'            => $user->lang['rb_Update'],
      'Lang_Banker'            => $user->lang['rb_Banker'],
      'Lang_Edit'              => $user->lang['rb_edit'],
      'Lang_Delete'            => $user->lang['rb_delete'],
      'L_CLOSE'                => $user->lang['rb_close'],
      'L_ADD'                  => $user->lang['rb_add'],
      'L_BANKER'               => $user->lang['rb_banker'],
      'L_IMPORT'               => $user->lang['rb_import'],
      'L_ADDBANKER'    				 => $user->lang['rb_add_banker_l'],
      'L_ADD_ITEM_HEAD'			 	 => $user->lang['rb_add_item'],
      'L_IMPORT_LOG_HEAD'			 => $user->lang["rb_step1_pagetitle"],
      'RB_PERM_ADD'            => ( $user->check_auth('a_raidbanker_import', false) ) ? true : false
      )
		);
    
    $eqdkp->set_vars(array(
	    'page_title'             => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['rb_edit_pagetitle'],
			'template_path' 	       => $pm->get_data('raidbanker', 'template_path'),
			'template_file'          => 'admin/update.html',
			'display'                => true)
    );


  }  // end main
  
}

    if (!isset($_GET["what"])){
      $RB_Update = new RaidBanker_Update();
      $RB_Update->process();
    }
?>