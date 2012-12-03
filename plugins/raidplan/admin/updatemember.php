<?php
/******************************
 * EQdkp Raid Planner
 * Copyright 2005 by A.Stranger
 * ------------------
 * updatemember.php
 * Began: Tue September 20 2005
 * Changed: Thu September 26 2005
 * 
 ******************************/
define('EQDKP_INC', true);
define('IN_ADMIN', true);
define('PLUGIN', 'raidplan');
$eqdkp_root_path = './../../../';
include_once($eqdkp_root_path . 'common.php');

// Check if plugin is installed
if (!$pm->check(PLUGIN_INSTALLED, 'raidplan')) { message_die('The Raid Planer plugin is not installed.'); }

// Check user permission
$user->check_auth('a_raidplan_update');

// Get the plugin
$raidplan = $pm->get_plugin(PLUGIN);

// Set table names
global $table_prefix;
if (!defined('RP_RAIDS_TABLE')) { define('RP_RAIDS_TABLE', $table_prefix . 'raidplan_raids'); }
if (!defined('RP_CLASSES_TABLE')) { define('RP_CLASSES_TABLE', $table_prefix . 'raidplan_raid_classes'); }
if (!defined('RP_ATTENDEES_TABLE')) { define('RP_ATTENDEES_TABLE', $table_prefix . 'raidplan_raid_attendees'); }

class RPToggleMember extends EQdkp_Admin
{
	function RPToggleMember()
	{
        global $db, $eqdkp, $user, $tpl, $pm;
        global $eqdkp_root_path, $SID;

		$success_message = "";

		if (isset($_GET['name']) && isset($_GET['r']) && isset($_GET['mode']))
		{
			$link_list = array(
				$user->lang['add_raid']		=> 'addraid.php' . $SID,
				$user->lang['update_raid']	=> 'addraid.php' . $SID . '&amp;r=' . $_GET['r'],
				$user->lang['list_raids']	=> 'index.php' . $SID);

			$sql = "SELECT attendees.confirmed, attendees.attendees_random, attendees.member_id as member_id, raid_id
				FROM " . RP_ATTENDEES_TABLE . " as attendees, " . MEMBERS_TABLE. " as members
				WHERE raid_id='" . $_GET['r'] . "'
				AND member_name='" . $_GET['name'] . "'
				AND attendees.member_id=members.member_id";

			if (!($result = $db->query($sql))) { message_die('Could not obtain attendees information', '', __FILE__, __LINE__, $sql); }
			if (!$row = $db->fetch_record($result)) { message_die($user->lang['error_invalid_raid_provided']); }
			$db->free_result($result);

			$confirmed = ($row['confirmed'] == '1') ? '0' : '1';
			$random = ($row['attendees_random'] < 0) ? $row['attendees_random'] * -1 : $row['attendees_random'];

			$mode = stripslashes($_GET['mode']);
			switch($mode)
			{
				case 'confirm':
					$sql = "UPDATE " . RP_ATTENDEES_TABLE . "
						SET confirmed='" . $confirmed . "'
						WHERE member_id='" . $row['member_id'] . "'
						AND raid_id='" . $row['raid_id']  . "'";
					$success_message = sprintf($user->lang['rp_admin_update_confimation_status'], $_GET['name']);
					break;
				case 'unlock':
					$sql = "UPDATE " . RP_ATTENDEES_TABLE . "
						SET attendees_random=" . $random . "
						WHERE member_id='" . $row['member_id'] . "'
						AND raid_id='" . $row['raid_id']  . "'";
					$success_message = sprintf($user->lang['rp_admin_unlock_member'], $_GET['name']);
					break;
				default:
					message_die($user->lang['error_invalid_mode_provided']);
					break;
			}
			$db->query($sql);

			//$success_message = "Member " . $_GET['name'] . " successful updated.<br />";
			//$this->admin_die($success_message, $link_list);
		}
		
		else
		{
			if (!isset($_GET['r']))		{ message_die($user->lang['error_invalid_raid_provided']); }
			if (!isset($_GET['name']))	{ message_die($user->lang['error_invalid_name_provided']); }
			if (!isset($_GET['mode']))	{ message_die($user->lang['rp_error_invalid_mode_provided']); }
		}
		redirect('plugins/'.PLUGIN.'/admin/addraid.php' . $SID . '&r=' . $_GET['r']);
	}
}
$myToggle = new RPToggleMember();
?>
