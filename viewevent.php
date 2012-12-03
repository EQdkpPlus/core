<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2010
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
 
define('EQDKP_INC', true);
$eqdkp_root_path = './';
include_once($eqdkp_root_path.'common.php');
require_once($eqdkp_root_path.'core/html_pdh_tag_table.class.php');
$user->check_auth('u_event_view');

$event_ids = $pdh->get('event', 'id_list', array());
$event_id = $in->get('event_id', 0);

//id necessary
if(in_array($event_id, $event_ids)) {
	$isort = $in->get('isort');
	$rsort = $in->get('rsort');

	$ipools = $pdh->get('event', 'itempools', array($event_id));
	
	$raid_hptt_settings = $pdh->get_page_settings('viewevent', 'hptt_viewevent_raidlist');
	$item_hptt_settings = $pdh->get_page_settings('viewevent', 'hptt_viewevent_itemlist');
	
	$raid_ids = $pdh->get('raid', 'raidids4eventid', array($event_id));
	$raid_hptt = new html_pdh_tag_table($raid_hptt_settings, $raid_ids, $raid_ids, array('%link_url%' => 'viewraid.php', '%link_url_suffix%' => ''), $event_id, 'rsort');

	$item_ids = $pdh->get('item', 'itemids4eventid', array($event_id));
	$item_hptt = new html_pdh_tag_table($item_hptt_settings, $item_ids, $item_ids, array('%link_url%' => 'viewitem.php', '%link_url_suffix%' => '', '%raid_link_url%' => 'viewraid.php', '%raid_link_url_suffix%' => '', '%itt_lang%' => false, '%itt_direct%' => 0, '%onlyicon%' => 0), $event_id, 'isort');
	
	infotooltip_js();
	$tpl->assign_vars(array(
		'RAID_LIST' => $raid_hptt->get_html_table($rsort, '&amp;event_id='.$event_id),
		'ITEM_LIST' => $item_hptt->get_html_table($isort, '&amp;event_id='.$event_id),
		'EVENT_ICON' => $game->decorate('events', array($pdh->get('event', 'icon', array($event_id)), 64)),
		'EVENT_NAME' => $pdh->get('event', 'name', array($event_id)),
		'MDKPPOOLS' => $pdh->geth('event', 'multidkppools', array($event_id)),
		'ITEMPOOLS' => $pdh->geth('event', 'itempools', array($event_id)),
		//language
		'L_RAIDS' => $user->lang['raids'],
		'L_ITEMS' => $user->lang['items'],
		'L_MDKPPOOLS' => $user->lang['belonging_mdkppools'],
		'L_ITEMPOOLS' => $user->lang['belonging_itempools'],
		'L_EVENT_NAME' => $user->lang['event_name']
	));
	
	$core->set_vars(array(
		'page_title'	=> sprintf($user->lang['viewevent_title'], stripslashes($event['event_name'])),
		'template_file'	=> 'viewevent.html',
		'display'		=> true)
	);
}else{
	message_die('Invalid event id.');
}
?>