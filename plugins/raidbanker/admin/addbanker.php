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

$user->check_auth('a_raidbanker_import');
$rb = $pm->get_plugin('raidbanker');

if ( !$pm->check(PLUGIN_INSTALLED, 'raidbanker') )
{
    message_die('The Raid Banker plugin is not installed.');
}

class RaidBanker_AddBanker extends EQdkp_Admin
{


    function RaidBanker_AddBanker()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;

        parent::eqdkp_admin();

        $this->assoc_buttons(
        	array(
				'insert' => array('name' => 'doInsert','process' => 'process_insert','check'   => 'a_raidbanker_import'),
				'form' => array('name' => '','process' => 'display_form', 'check' => 'a_raidbanker_import')
        	));
    }

	function process_insert()
	{
		global $db, $eqdkp, $user, $tpl, $pm;
		global $SID;
    $charName = $_POST["charName"];
						$db->query("DELETE FROM ".RB_CHARS_TABLE." WHERE rb_char_name = '".mysql_escape_string($_POST["charName"])."'");
						$totalmoney = $_POST["charCopper"] + ($_POST["charSilver"]*100) + ($_POST["charGold"]*10000);
						$db->query("INSERT INTO ".RB_CHARS_TABLE." (rb_char_name, rb_char_money, rb_date) VALUES('".mysql_escape_string($_POST["charName"])."',".$totalmoney."," .time().")");
		    echo '<script LANGUAGE="JavaScript">
    top.location.href=\'./editbankdata.php\'
</script>';
	}

	function display_form()
	{
		global $db, $eqdkp, $user, $tpl, $pm;
		global $SID;

		$tpl->assign_vars(array(
			'F_ADD_BANKER'     => 'addbanker.php' . $SID,
			'ROW_CLASS'        => $eqdkp->switch_row_class(),
			'L_ADD_BANKER'     => $user->lang['rb_add_banker_l'],
			'L_BANKER_INSERT'	 => $user->lang['rb_insert_banker'],
			'L_GOLD'	         => $user->lang['lang_gold'],
		  'L_SILVER'	       => $user->lang['lang_silver'],
		  'L_COPPER'	       => $user->lang['lang_copper'],
		  'L_BANKER'         => $user->lang['rb_banker'],
		  'L_CLOSE'          => $user->lang['rb_close'],
			)
		);

		$eqdkp->set_vars(array(
			'page_title'        => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['rb_step1_pagetitle'],
			'template_path' 	=> $pm->get_data('raidbanker', 'template_path'),
			'template_file'     => 'admin/addbanker.html',
			'display'           => true,
			)
		);
	}
}

$RB_Import = new RaidBanker_AddBanker();
$RB_Import->process();
?>
