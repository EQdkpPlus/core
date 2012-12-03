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

global $table_prefix;
$eqdkp_root_path = './../../../';
include_once($eqdkp_root_path . 'plugins/raidbanker/includes/functions.php');
if ($conf['rb_itemstats'] == 1){
  include_once("../".$conf['rb_is_path']);
}

// Check user permission
$user->check_auth('a_raidbanker_acl');
$rb = $pm->get_plugin('raidbanker');
if ( !$pm->check(PLUGIN_INSTALLED, 'raidbanker') )
{
    message_die('The Raid Banker plugin is not installed.');
}

class RaidBanker_AddACL extends EQdkp_Admin
{


    function RaidBanker_AddACL()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;

        parent::eqdkp_admin();

        $this->assoc_buttons(
        	array(
				'insert' => array('name' => 'doInsert','process' => 'process_insert','check'   => 'a_raidbanker_import'),
				'form' => array('name' => '','process' => 'display_form', 'check' => 'a_raidbanker_import'),
        'delete' => array('name' => 'doDelete','process' => 'process_delete','check'   => 'a_raidbanker_import'),
        'add' => array('name' => 'doAdd','process' => 'process_add','check'   => 'a_raidbanker_import'),
          ));
    }

	function process_insert()
	{
		global $db, $eqdkp, $user, $tpl, $pm, $conf;
		global $SID;
		$sql = "SELECT * from ".RB_BANK_TABLE." WHERE rb_bank_id=".$_POST['item_name']."";
		 $result = $db->query($sql);
		 $item = $db->fetch_record($result);
		 
		 // automtaic item adjustment
				  if ($_POST['dkp_value'] != 0 && $_POST['action'] == "got" && $conf['rb_auto_adjustment']){
				    $group_key = $this->gen_group_key(time(), stripslashes($user->lang['rb_adjust_reason']), -($_POST['amount']*$_POST['dkp_value']));
				    $this->add_new_adjustment($_POST['members'], $group_key, $item['rb_item_name']);
				    $temp_insertid = $db->insert_id();
          } else {
            $temp_insertid = 0;
          } // adjustment end
		 
		 // do the count adjustments
		 if ($_POST['action'] == "got"){
      $rv_vzcount = '-';
     }else{
      $rv_vzcount = '+';
     }
		 $sql = 'UPDATE ' . RB_BANK_TABLE . '
                SET rb_item_amount = rb_item_amount '.$rv_vzcount.' ' . $db->escape($_POST['amount']) . "
                WHERE rb_item_name='" . $item['rb_item_name'] . "'";
        $db->query($sql);
        unset($sql);
		 
		 // the acl action
		 $totalmoney = $_POST["copper_value"] + ($_POST["silver_value"]*100) + ($_POST["gold_value"]*10000);
		 $sql = "INSERT INTO ".RB_BANK_REL_TABLE.
            " (rb_item_name, rb_char_name, rb_qty, rb_action, rb_item_dkp, rb_char_money, rb_adjust_id) VALUES ('".
            $item['rb_item_name']."','".mysql_escape_string($_POST['members'])."','".$_POST['amount']."','".$_POST['action']."','".$_POST['dkp_value']."','".$totalmoney."','".$temp_insertid."' )";
						$db->query($sql);
		    $tpl->assign_vars(array(
						'L_ACT_PERF' => $user->lang['Lang_actions_performed'],
						'L_USER_LINK'=> $user->lang['rb_user_link'],
						'F_LINK'     => 'acl.php' . $SID
					)
				);
				
        $tpl->assign_block_vars(
			'logs_row',
			array(
				'DES' => $user->lang['lang_added_data']." ".$_POST['members'],
				'ROW_CLASS' => $eqdkp->switch_row_class()
			)
		);
		
		//
    // Logging
    //
    $log_action = array(
            'header'        => '{L_ACTION_RBACL_ADDED}',
            'id'            => $db->insert_id(),
            '{L_EVENT}'     => $item['rb_item_name'],
            '{L_ATTENDEES}' => $_POST['members'],
            '{L_NOTE}'      => '',
            '{L_VALUE}'     => $_POST['action'],
            '{L_ADDED_BY}'  => $this->admin_user);
     $this->log_insert(array(
                'log_type'   => $log_action['header'],
                'log_action' => $log_action)
            );

		$eqdkp->set_vars(array(
			'page_title'        => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['rb_step2_pagetitel'],
			'template_path' 	=> $pm->get_data('raidbanker', 'template_path'),
			'template_file'     => 'admin/import_result.html',
			'display'           => true,
			)
		);
	}

	function process_add()
	{
		global $db, $eqdkp, $user, $tpl, $pm;
		global $SID;
   
   $sql = "SELECT * from ".MEMBERS_TABLE." ORDER BY member_name";
    if (!($result = $db->query($sql))) { message_die('Could not obtain class information', '', __FILE__, __LINE__, $sql); }
		    while ($row = $db->fetch_record($result))
		    {
		        $tpl->assign_block_vars('members_row', array(
				      'NAME'	   => $row['member_name']
		           )
              );
		    }
		$sql = "SELECT * from ".RB_BANK_TABLE." GROUP BY rb_item_name ORDER BY rb_item_name";
    if (!($result = $db->query($sql))) { message_die('Could not obtain class information', '', __FILE__, __LINE__, $sql); }
		    while ($row = $db->fetch_record($result))
		    {
		        $tpl->assign_block_vars('bank_row', array(
				      'NAME'	   => $row['rb_item_name'],
				      'ID'       => $row['rb_bank_id']
		           )
              );
		    }
   
   $tpl->assign_vars(array(
      'IS_ADD'           => true,
			'F_ADD_ACL'        => 'acl.php' . $SID,
			'ROW_CLASS'        => $eqdkp->switch_row_class(),
			'L_ADD_ACL'        => $user->lang['rb_add_acl'],
			'L_RB_ACT'	       => $user->lang['rb_acl_action'],
			'L_AC_SPENT'	     => $user->lang['rb_ac_spent'],
		  'L_AC_GOT'	       => $user->lang['rb_ac_got'],
		  'L_ITEMNAME'	     => $user->lang['rb_item_name'],
		  'L_ACL_SAVE'       => $user->lang['rb_acl_save'],
		  'L_CHAR_DATA'      => $user->lang['rb_char_data'],
		  'L_BANK_QTY'       => $user->lang['rb_Bank_QTY'], 
		  'L_ITEMCOST_MON'   => $user->lang['itemcost_money'],
		  'L_ITEMCOST_DKP'   => $user->lang['itemcost_dkp']
			)
		);
   
   	$eqdkp->set_vars(array(
			'page_title'        => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['rb_step1_pagetitle'],
			'template_path' 	=> $pm->get_data('raidbanker', 'template_path'),
			'template_file'     => 'admin/acl.html',
			'display'           => true,
			)
		);
	}

	function process_delete()
	{
		global $db, $eqdkp, $user, $tpl, $pm, $conf;
		global $SID;
   
   foreach ($_POST['delete'] as $delete_id){
        //get the adjustment ID
        $sql = "SELECT rb_adjust_id, rb_char_name, rb_action from ".RB_BANK_REL_TABLE." WHERE rb_bank_rel_id='" . $delete_id . "'";
        $result = $db->query($sql);
        $adjustment_id = $db->fetch_record($result);
        // get the adjustment content
        $sql = "SELECT adjustment_value from ".ADJUSTMENTS_TABLE." WHERE adjustment_id= '".$adjustment_id['rb_adjust_id']."'";
        $result = $db->query($sql);
        $adjustment_value = $db->fetch_record($result);
        if ($adjustment_id['rb_action'] == "got" && $conf['rb_auto_adjustment'] == 1 && $adjustment_value['adjustment_value']){
        // Remove the adjustment value from members
        $sql = 'UPDATE ' . MEMBERS_TABLE . '
                SET member_adjustment = member_adjustment - ' . stripslashes($adjustment_value['adjustment_value']) . '
                WHERE member_name="'.$adjustment_id['rb_char_name'].'"';
        $db->query($sql);
        // Remove the adjustment value from adjustments table
        $sql = "DELETE FROM ".ADJUSTMENTS_TABLE." WHERE adjustment_id= '".$adjustment_id['rb_adjust_id']."'";
        $db->query($sql);
      }
    $db->query('DELETE FROM ' . RB_BANK_REL_TABLE . " WHERE rb_bank_rel_id='" . $delete_id . "'");
    unset($sql);
    $db->free_result($result);
   }
   
   //
    // Logging
    //
    $log_action = array(
            'header'        => '{L_ACTION_RBACL_DEL}',
            'id'            => $db->insert_id(),
            '{L_EVENT}'     => $delete_id,
            '{L_VALUE}'     => 'deleted',
            '{L_ADDED_BY}'  => $this->admin_user);
     $this->log_insert(array(
                'log_type'   => $log_action['header'],
                'log_action' => $log_action)
            );
   
   $tpl->assign_vars(array(
						'L_ACT_PERF' => $user->lang['Lang_actions_performed'],
						'L_USER_LINK'=> $user->lang['rb_user_link'],
						'F_LINK'     => 'acl.php' . $SID
					)
				);
   
   	$tpl->assign_block_vars(
					'logs_row',
					array(
						'DES' => $user->lang['lang_deleting_item']." ".stripslashes($_POST["name"]),
						'ROW_CLASS' => $eqdkp->switch_row_class()
					)
				);
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

$sql = "SELECT * from ".RB_BANK_REL_TABLE." ORDER BY rb_bank_rel_id";
    $result = $db->query($sql);
		    while ($row = $db->fetch_record($result))
		    {
		        $tpl->assign_block_vars('acl_row', array(
				      'NAME'	     => $row['rb_char_name'],
				      'ID'         => $row['rb_bank_rel_id'],
				      'QTY'	       => $row['rb_qty'],
				      'ACTION'	   => ( $row['rb_action'] == "got") ? $user->lang['rb_ac_got'] : $user->lang['rb_ac_spent'],
				      'ITEMID'	   => $row['rb_item_id'],
				      'ITEM_NAME'  => $row['rb_item_name'],
				      'ROW_CLASS'  => $eqdkp->switch_row_class(),
		           )
              );
		    }

		$tpl->assign_vars(array(
			'F_ADD_ACL'        => 'acl.php' . $SID,
			'IS_ADD'           => false,
			'ROW_CLASS'        => $eqdkp->switch_row_class(),
			'L_LIST_ACL'       => $user->lang['rb_list_acl'],
			'L_ACL_ADD'        => $user->lang['rb_add_acl'],
			'L_ITEM_NAME'      => $user->lang['rb_item_name'],
			'L_AMOUNT'         => $user->lang['lang_amount'],
			'L_CHARNAME'       => $user->lang['rb_char_name'],
			'L_ID'             => $user->lang['rb_id'],
			'L_RB_ACT'	       => $user->lang['rb_acl_action'],
			'L_DELETE'         => $user->lang['rb_delete']
			)
		);

		$eqdkp->set_vars(array(
			'page_title'        => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['rb_step1_pagetitle'],
			'template_path' 	=> $pm->get_data('raidbanker', 'template_path'),
			'template_file'     => 'admin/acl.html',
			'display'           => true,
			)
		);
	}
	
	function add_new_adjustment($member_name, $group_key, $item_name)
    {
        global $db, $user;
        
        //
        // Add the adjustment to the member
        //
        $sql = 'UPDATE ' . MEMBERS_TABLE . '
                SET member_adjustment = member_adjustment + ' . $db->escape(-($_POST['amount']*$_POST['dkp_value'])) . "
                WHERE member_name='" . $member_name . "'";
        $db->query($sql);
        unset($sql);
        
        //
        // Add the adjustment to the database
        //
        $query = $db->build_query('INSERT', array(
            'adjustment_value'     => -($_POST['amount']*$_POST['dkp_value']),
            'adjustment_date'      => time(),
            'member_name'          => $member_name,
            'adjustment_reason'    => $item_name.' '.$user->lang['rb_adjust_reason'],
            'adjustment_group_key' => $group_key,
            'adjustment_added_by'  => $this->admin_user)
        );
        $db->query('INSERT INTO ' . ADJUSTMENTS_TABLE . $query);
    }
    
}

$RB_Import = new RaidBanker_AddACL();
$RB_Import->process();
?>
