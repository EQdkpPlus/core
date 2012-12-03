<?php
/******************************
 * EQdkp RaidBanker Plugin
 * Copyright 2005 - 2006
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * config.php
 ******************************/

// EQdkp required files/vars
define('EQDKP_INC', true);
define('IN_ADMIN', true);
define('PLUGIN', 'raidbanker');
$eqdkp_root_path = './../../../';
include_once($eqdkp_root_path . 'plugins/raidbanker/includes/functions.php');
if ($conf['rb_itemstats'] == 1){
  include_once("../".$conf['rb_is_path']);
}


$rb = $pm->get_plugin('raidbanker');

if ( !$pm->check(PLUGIN_INSTALLED, 'raidbanker') )
{
    message_die('The Raid Banker plugin is not installed.');
}

class RaidBanker_Import extends EQdkp_Admin
{


    function RaidBanker_Import()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;

        parent::eqdkp_admin();

        $this->assoc_buttons(
        	array(
				'parse' => array('name' => 'doParse','process' => 'process_parse','check'   => 'a_raidbanker_import'),
				'insert' => array('name' => 'doInsert','process' => 'process_insert','check'   => 'a_raidbanker_import'),
				'form' => array('name' => '','process' => 'display_form', 'check' => 'a_raidbanker_import')
        	));
    }


	function process_parse()
	{
		global $db, $eqdkp, $user, $tpl, $pm;
		global $SID;

		$log = trim($_POST['log']);

		$items = split("\n", $log);
		$num = 0;
		$charName = "";
		$charGold = 0;
		$charSilver = 0;
		$charCopper = 0;
		foreach($items as $row)
		{
			$item = split("/", $row);
			if ($num == 0)
			{
				$charName = $item[0];
				$charMoneylength = strlen($item[1]);
				if ($charMoneylength == 4){
          $charGold = 0; 
          $charSilver = substr($item[1], -4, -2);
          $charCopper = substr($item[1], -2 );   }
        elseif ($charMoneylength == 3){
				  $charGold = 0;
				  $charSilver = (substr($item[1], 0, 1)-1);
          $charCopper = substr($item[1], -2 );	}
				elseif ($charMoneylength == 2){
				  $charGold = 0;
				  $charSilver = 0;
          $charCopper = substr($item[1], -2 );	}
				elseif ($charMoneylength == 0 or $charMoneylength == 1){
				  $charGold = 0;
				  $charSilver = 0;	
          $charCopper = 0;  }
        else {
          $charGold = substr($item[1], 0, strlen($item[1])-4);
				  $charSilver = substr($item[1], -4, -2);
          $charCopper = substr($item[1], -2 );
        }
        $charMoney = $charCopper + ($charSilver*100) + ($charGold*10000);
			}
			else
			{
				$tpl->assign_block_vars(
					'items_row',
					array(
						'NAME' => stripslashes($item[0]),
						'NAME2'=> GetItemName($item[4], $item[0]),
						'AMOUNT' => $item[1],
						'QUALITY' => $item[2],
						'TYPE' => $item[3],
						'ITEMID' => $item[4],
						'ROW_CLASS' => $eqdkp->switch_row_class(),
						'NUMBER' => $num
					)
				);
			}
			$num++;
		}

		$tpl->assign_vars(array(
		            'S_STEP1'		     => false,
		            'L_CHARNAME'	   => $charName,
		            'L_CHARGOLD'     => round($charMoney/10000),
        				'L_CHARSILVER'   => substr(round($charMoney/100), -2),
        				'L_CHARCOPPER'   => substr(round($charMoney), -2),
		            'L_CHARMONEY'    => $charMoney,
		            'Lang_g'         => $user->lang['lang_g'],
			          'Lang_s'         => $user->lang['lang_s'],
			          'Lang_c'         => $user->lang['lang_c'],
		            'L_BANK_INSERT'	 => $user->lang['lang_update_data'],
		            'L_FOUND_ITEMS'	 => $user->lang['lang_found_log'],
		            'Lang_QTY'       => $user->lang['rb_Bank_QTY'],
                'Lang_Type'      => $user->lang['rb_Bank_Type'],
                'Lang_Name'      => $user->lang['rb_Item_Name'],
                'Lang_ItemID'    => $user->lang['lang_itemid'],
			          'lang_skip'      => $user->lang['lang_skip']
		            )
        );

        $eqdkp->set_vars(array(
			'page_title'        => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['rb_step2_pagetitel'],
			'template_path' 	=> $pm->get_data('raidbanker', 'template_path'),
			'template_file'     => 'admin/import.html',
			'display'           => true,
			)
        );
    }

	function process_insert()
	{

		global $db, $eqdkp, $user, $tpl, $pm;
		global $SID;

		$charName = $_POST["charName"];

		$db->query("DELETE FROM ".RB_CHARS_TABLE." WHERE rb_char_name = '".mysql_escape_string($_POST["charName"])."'");
		$db->query("DELETE FROM ".RB_BANK_TABLE." WHERE rb_char_name = '".mysql_escape_string($_POST["charName"])."'");
		$tpl->assign_vars(array(
						'L_ACT_PERF' => $user->lang['Lang_actions_performed'],
						'L_USER_LINK'=> $user->lang['rb_user_link'],
						'F_LINK'     => 'importbankdata.php' . $SID
					)
				);
    $tpl->assign_block_vars(
			'logs_row',
			array(
				'DES' => $user->lang['lang_cleared_data']." ".$charName,
				'ROW_CLASS' => $eqdkp->switch_row_class()
			)
		);


	$db->query("INSERT INTO ".RB_CHARS_TABLE." (rb_char_name, rb_char_money, rb_date) VALUES('".mysql_escape_string($_POST["charName"])."',".$_POST["charMoney"]."," .time().")");
		$tpl->assign_vars(array(
						'L_ACT_PERF' => $user->lang['Lang_actions_performed'],
						'L_USER_LINK'=> $user->lang['rb_user_link'],
						'F_LINK'     => 'importbankdata.php' . $SID
					)
				);
    $tpl->assign_block_vars(
			'logs_row',
			array(
				'DES' => $user->lang['lang_added_data']." ".$charName,
				'ROW_CLASS' => $eqdkp->switch_row_class()
			)
		);
  if ($rb_set_itemstats){
		$item_stats = new ItemStats();
		}

		foreach($_POST["item"] as $item)
		{
			if(!isset($item["skip"]))
			{
				$sql  = "INSERT INTO ".RB_BANK_TABLE." (rb_char_name, rb_item_name, rb_item_rarity, rb_item_type, rb_item_amount, rb_item_id) VALUES ";
				$sql .= "(\"".$_POST["charName"]."\",";
				if ($rb_set_itemstats){
				  $sql .= "\"".$item_stats->getItemName(stripslashes($item["name"]),false)."\",";
				}else{
          $sql .= "\"".$item["name"]."\",";
        }
				$sql .= "".$item["quality"].",";
				$sql .= "'".$item["type"]."',";
				$sql .= "".$item["amount"].",";
				$sql .= "".$item["itemid"].")";


				$db->query($sql);
				$tpl->assign_vars(array(
						'L_ACT_PERF' => $user->lang['Lang_actions_performed'],
						'L_USER_LINK'=> $user->lang['rb_user_link'],
						'F_LINK'     => 'importbankdata.php' . $SID
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
						'F_LINK'     => 'importbankdata.php' . $SID
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
			//
    // Logging
    //
    $log_action = array(
            'header'        => '{L_ACTION_RB_IMPORTED}',
            'id'            => $db->insert_id(),
            '{L_EVENT}'     => $item["name"],
            '{L_UPDATED_BY}'=> $this->admin_user);
     $this->log_insert(array(
                'log_type'   => $log_action['header'],
                'log_action' => $log_action)
            );
		}

	echo '<script LANGUAGE="JavaScript">
    top.location.href=\'./editbankdata.php\'
</script>';

		$eqdkp->set_vars(array(
			'page_title'        => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['rb_step2_pagetitel'],
			'template_path' 	=> $pm->get_data('raidbanker', 'template_path'),
			'template_file'     => 'admin/import_result.html',
			'display'           => true,
			)
		);


	}

	function display_form()
	{
		global $db, $eqdkp, $user, $tpl, $pm;
		global $SID;

		$tpl->assign_vars(array(
			'F_PARSE_LOG'    => 'importbankdata.php' . $SID,
			'S_STEP1'        => true,
			'L_PASTE_LOG'    => $user->lang['rb_step1_th'],
			'L_PARSE_LOG'    => $user->lang['rb_step1_button_parselog'],
			'Char_Data'      => $user->lang['Character_Data'],
			'Lang_with'      => $user->lang['lang_with'],
			'Lang_g'         => $user->lang['lang_g'],
			'Lang_s'         => $user->lang['lang_s'],
			'Lang_c'         => $user->lang['lang_c'],
			'Bank_Type'      => $user->lang['lang_c']
			)
		);

		$eqdkp->set_vars(array(
			'page_title'        => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['rb_step1_pagetitle'],
			'template_path' 	=> $pm->get_data('raidbanker', 'template_path'),
			'template_file'     => 'admin/import.html',
			'display'           => true,
			)
		);
	}
}

$RB_Import = new RaidBanker_Import();
$RB_Import->process();
?>