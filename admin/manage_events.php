<?php
/*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2006
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2006-2008 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev$
 *
 * $Id$
 */

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path.'common.php');

class Manage_Events extends EQdkp_Admin
{
	private $simple_head = 'full';
	
	function Manage_Events()
	{
		global $core, $in;

		parent::eqdkp_admin();
		
		$this->simple_head = ($in->get('simple_head', false)) ? 'simple' : 'full';

		$this->assoc_params(array(
			'upd'	=> array(
				'name' => 'update',
				'process' => 'update_event',
				'check' => 'a_event_add')
			)
		);

		$this->assoc_buttons(array(
			'save' => array(
				'name' => 'save',
				'process' => 'save_event',
				'check' => 'a_event_add'),
			'del' => array(
				'name' => 'delete',
				'process' => 'delete_event',
				'check' => 'a_event_del'),
			'confirm' => array(
				'name' => 'confirm',
				'process' => 'delete_event',
				'check' => 'a_event_del'),
			'form' => array(
				'name' => '',
				'process' => 'display_form',
				'check' => 'a_event_upd')
			)
		);
	}

	function save_event()
	{
		global $user, $pdh;
		$event = $this->get_post();
		if($event)
		{
			if($event['id'])
			{
				$retu = $pdh->put('event', 'update_event', array($event['id'], $event['name'], $event['value'], $event['icon']));
			}
			else
			{
				$retu = $pdh->put('event', 'add_event', array($event['name'], $event['value'], $event['icon']));
			}
			if($retu)
			{				
				$message = array('title' => $user->lang['save_suc'], 'text' => $event['name'], 'color' => 'green');
			}
			else
			{
				$message = array('title' => $user->lang['save_nosuc'], 'text' => $event['name'], 'color' => 'red');
			}
		}
		$this->display_form($message);
	}

	function delete_event()
	{
		global $user, $pdh, $in;
		$event_id = $in->get('event_id',0);
		if($event_id AND !isset($_POST['confirm']))
		{
			$raids = $pdh->get('raid', 'raidids4eventid', array($event_id));

			confirm_delete(sprintf($user->lang['confirm_delete_event'], $in->get('name',''), count($raids)), 'event_id', $event_id);
		}
		elseif($event_id AND $_POST['confirm'] == $user->lang['yes'])
		{
			if($pdh->put('event', 'delete_event', array($event_id)))
			{
				$message = array('title' => $user->lang['del_no_suc'], 'text' => $pdh->get('event', 'name', $event_id), 'color' => 'red');
			}
			else
			{
				$message = array('title' => $user->lang['del_suc'], 'text' => $pdh->get('event', 'name', $event_id), 'color' => 'green');
			}
		}
		$this->display_form($message);
	}

	function update_event($message=false)
	{
		global $core, $user, $tpl, $pdh, $eqdkp_root_path, $in;

		$event = array('id' => $in->get('event_id',0), 'value' => '0.00');
		if($message)
		{
			$core->messages($message);
			$event = $this->get_post(true);
		}
		elseif($event['id'])
		{
			$event['name'] = $pdh->get('event', 'name', array($event['id']));
			$event['icon'] = $pdh->get('event', 'icon', array($event['id']));
			$event['value'] = $pdh->get('event', 'value', array($event['id']));
		}

		//get icons
		$events_folder = $eqdkp_root_path.'games/'.$core->config['default_game'].'/events';
		$handle = opendir($events_folder);
		$ignorefiles = array('.', '..', '.svn', 'index.html');
		$icons = array();
		while(($file = readdir($handle)) !== false)
		{
			if(!in_array($file, $ignorefiles))
			{
				$icons[] = $file;
			}
		}
		closedir($handle);

		$num = count($icons);
		$fields = (ceil($num/6))*6;
		for($i=0; $i<$fields; $i++)
		{
			$tpl->assign_block_vars('files_row', array());
			$b = $i+6;
			for(; $i<$b; $i++)
			{
				$icon = $icons[$i];
				$tpl->assign_block_vars('files_row.fields', array(
					'NAME'		=> $icon,
					'CHECKED'	=> ($icon == $event['icon']) ? ' checked="checked"' : '',
					'IMAGE'		=> "<img src='".$eqdkp_root_path."games/".$core->config["default_game"]."/events/".$icon."' alt='".$icon."' width='48px' />",
					'ROWCLASS'	=> $core->switch_row_class(),
					'CHECKBOX'	=> ($i < $num) ? true : false)
				);
			}
		}
		$tpl->assign_vars(array(
			'S_UPD'			=> ($event['id']) ? TRUE : FALSE,
			'EVENT_ID'		=> $event['id'],
			'NAME'			=> $event['name'],
			'VALUE'			=> $event['value'],
			//language
			'L_ADD_EVENT'	=> $user->lang['addevent_title'],
			'L_SAVE'		=> $user->lang['save'],
			'L_DELETE'		=> $user->lang['delete'],
			'L_SELECT_ICON'	=> $user->lang['event_icon_header'],
			'L_DKP_VALUE'	=> $user->lang['value'],
			'L_NAME'		=> $user->lang['name'])
		);
		$core->set_vars(array(
            'page_title'    => $user->lang['addevent_title'],
            'template_file' => 'admin/manage_events_add.html',
            'header_format' => $this->simple_head,
            'display'       => true)
        );
	}

	function display_form($messages=false)
	{
		global $core, $user, $tpl, $pdh, $SID, $eqdkp_root_path, $in;

		if($messages)
		{
			$pdh->process_hook_queue();
			$core->messages($messages);
		}
		$event_ids = $pdh->get('event', 'id_list');

		//Sort
		if (isset($_GET['sort'])){
		  $sort = $in->get('sort');
		}else{
		  $sort = '0|desc';
		}
		$sort_suffix = '&amp;sort='.$sort;

		$start = 0;
		if(isset($_GET['start'])){
		  $start = $in->get('start', 0);
		  $pagination_suffix = '&amp;start='.$start;
		}

		include_once($eqdkp_root_path.'core/html_pdh_tag_table.class.php');
		$hptt_page_settings = $pdh->get_page_settings('admin_manage_events', 'hptt_admin_manage_events_eventlist');
    $hptt = new html_pdh_tag_table($hptt_page_settings, $event_ids, $event_ids, array('%link_url%' => 'manage_events.php', '%link_url_suffix%' => '&update=true'));

		//footer
		$event_count = count($event_ids);
		$footer_text = sprintf($user->lang['listevents_footcount'], $event_count ,$user->data['user_elimit']);

		$tpl->assign_vars(array(
			'ACTION' 	=> 'manage_events.php'.$SID,
			'EVENTS_LIST' => $hptt->get_html_table($in->get('sort',''), $pagination_suffix, $start, $user->data['user_elimit'], $footer_text),
			'EVENT_PAGINATION' => generate_pagination('manage_events.php'.$SID.$sort_suffix, $event_count, $user->data['user_elimit'], $start),
			
			//Language
			'L_EVENTS'	=> $user->lang['manevents_title'],
			'L_ADD_EVENT' => $user->lang['addevent_title'],
			'HPTT_COLUMN_COUNT'	=> $hptt->get_column_count(),
			)
		);

		$core->set_vars(array(
            'page_title'    => $user->lang['manevents_title'],
            'template_file' => 'admin/manage_events.html',
            'header_format' => $this->simple_head,
            'display'       => true)
        );
	}

    function get_post($norefresh=false)
    {
    	global $user, $in;
    	$event['name'] = $in->get('name','');
    	if(empty($event['name']) AND !$norefresh)
    	{
    		$this->update_event(array('title' => $user->lang['missing_values'], 'text' => $user->lang['name'], 'color' => 'red'));
    	}
    	if(empty($_POST['buyers']))
    	{
    		$missing[] = $user->lang['buyers'];
    	}
        $event['value'] = $in->get('value',0.0);
        $event['icon'] = $in->get('icon','');
        $event['id'] = $in->get('event_id',0);
        return $event;
    }
}
$manevents = new Manage_Events;
$manevents->process();
?>