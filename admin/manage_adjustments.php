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

class ManageAdjs extends EQdkp_Admin
{
	function ManageAdjs ()
	{
		global $core;

		parent::eqdkp_admin();

		$this->assoc_params(array(
			'update' => array(
				'name'		=> 'update',
				'process'	=> 'edit_adj',
				'check'		=> 'a_indivadj_upd')
			)
		);

		$this->assoc_buttons(array(
			'save' => array(
				'name'		=> 'save',
				'process'	=> 'save_adj',
				'check'		=> 'a_indivadj_add'),
			'del' => array(
				'name'		=> 'delete',
				'process'	=> 'del_adj',
				'check'		=> 'a_indivadj_del'),
            'form' => array(
                'name'      => '',
                'process'   => 'display_form',
                'check'     => 'a_indivadj_')
			)
		);
	}

	function save_adj()
	{
		global $user, $pdh, $in;

		$adj = $this->get_post();
		if($in->get('selected_ids',''))
		{
			$retu = $pdh->put('adjustment', 'update_adjustment', array($in->get('selected_ids','',true), $adj['value'], $adj['reason'], $adj['members'], $adj['event'], $adj['raid_id'], $adj['date']));
		}
		else
		{
			$retu = $pdh->put('adjustment', 'add_adjustment', array($adj['value'], $adj['reason'], $adj['members'], $adj['event'], $adj['raid_id'], $adj['date']));
		}
		if($retu)
		{
			$message = array('title' => $user->lang['save_suc'], 'text' => $adj['reason'], 'color' => 'green');
		}
		else
		{
			$message = array('title' => $user->lang['save_no_suc'], 'text' => $adj['reason'], 'color' => 'red');
		}
		$this->display_form($message);
	}

	function del_adj()
	{
		global $user, $pdh, $in;

		$ids = array();
		if(!is_array($_POST['selected_ids']))
		{
			$ids = $pdh->get('adjustment', 'ids_of_group_key', array($in->get('selected_ids','',true)));
		}
		else
		{
			foreach($in->getArray('selected_ids','int') as $s_id)
			{
				$new_ids = $pdh->get('adjustment', 'ids_of_group_key', array($pdh->get('adjustment', 'group_key', array($s_id))));
				$ids = array_merge($ids, $new_ids);
			}
		}
		$retu = array();
		foreach($ids as $id)
		{
			$retu[$id] = $pdh->put('adjustment', 'delete_adjustment', array($id));
		}
		foreach($retu as $id => $suc)
		{
			if($suc)
			{
				$pos[] = stripslashes($pdh->get('adjustment', 'reason', array($id)));
			}
			else
			{
				$neg[] = stripslashes($pdh->get('adjustment', 'reason', array($id)));
			}
		}
		if($pos)
		{
			$messages[] = array('title' => $user->lang['del_suc'], 'text' => implode(', ', $pos), 'color' => 'green');
		}
		if($neg)
		{
			$messages[] = array('title' => $user->lang['del_no_suc'], 'text' => implode(', ', $neg), 'color' => 'red');
		}
		$this->display_form($messages);
	}

	function edit_adj($message=false)
	{
		global $core, $user, $tpl, $pdh, $SID, $jquery, $html, $in;

        //fetch members for select
        $members = $pdh->aget('member', 'name', 0, array($pdh->get('member', 'id_list')));

        //fetch raids for select
        $raids = array(0 => '');
        $raidids = $pdh->get('raid', 'id_list');
        foreach($raidids as $id)
        {
            $raids[$id] = '#ID:'.$id.' - '.$pdh->get('event', 'name', array($pdh->get('raid', 'event', array($id)))).' '.date('d.m.y', $pdh->get('raid', 'date', array($id)));
        }

        //fetch events for select
        $events = array();
        $event_ids = $pdh->get('event', 'id_list');
        foreach($event_ids as $id)
        {
        	$events[$id] = $pdh->get('event', 'name', array($id));
        }
		if($message)
		{
			$core->messages($message);
			$adj = $this->get_post(true);
		}
        elseif($in->get('a',0))
        {
        	$grp_key = $pdh->get('adjustment', 'group_key', array($in->get('a',0)));
        	$ids = $pdh->get('adjustment', 'ids_of_group_key', array($grp_key));
        	foreach($ids as $id)
        	{
        		$adj['members'][] = $pdh->get('adjustment', 'member', array($id));
        	}
        	$adj['reason'] = $pdh->get('adjustment', 'reason', array($id));
        	$adj['value'] = $pdh->get('adjustment', 'value', array($id));
        	$adj['date'] = $pdh->get('adjustment', 'date', array($id));
        	$adj['raid_id'] = $pdh->get('adjustment', 'raid_id', array($id));
        	$adj['event'] = $pdh->get('adjustment', 'event', array($id));
        }
				
				//fetch adjustment-reasons
				$adjustment_reasons = $pdh->aget('adjustment', 'reason', 0, array($pdh->get('adjustment', 'id_list')));
				$jquery->Autocomplete('reason', array_unique($adjustment_reasons));
				
        $tpl->assign_vars(array(
        	'GRP_KEY'	=> $grp_key,
        	'REASON'	=> $adj['reason'],
        	'RAID' 		=> $html->DropDown('raid_id', $raids, $adj['raid_id']),
        	'MEMBERS'	=> $jquery->MultiSelect('members', $members, $adj['members'], 200, 350),
        	'DATE'		=> $jquery->calendar('date', date('d.m.Y', $adj['date'])),
        	'VALUE'		=> $adj['value'],
        	'HOUR'		=> date('H', $adj['date']),
        	'MIN'		=> date('i', $adj['date']),
        	'SEC'		=> date('s', $adj['date']),
        	'EVENT'		=> $html->DropDown('event', $events, $adj['event']),
        	//language
        	'L_REASON'	=> $user->lang['reason'],
        	'L_DATE'	=> $user->lang['date'],
        	'L_MEMBERS'	=> $user->lang['members'],
        	'L_VALUE'	=> $user->lang['value'],
        	'L_TIME'	=> $user->lang['time'],
        	'L_RAID'	=> $user->lang['raid'],
        	'L_EDIT_ADJ' => $user->lang['editing_adj'],
        	'L_SAVE'	=> $user->lang['save'],
        	'L_DEL'		=> $user->lang['delete'],
					'L_CANCEL'		=> $user->lang['cancel'],
        	'L_EVENT'	=> $user->lang['event'])
        );
				

        $core->set_vars(array(
            'page_title'    => $user->lang['manadjs_title'],
            'template_file' => 'admin/manage_adjustments_edit.html',
            'display'       => true)
        );
	}

	function display_form($messages=false)
	{
		global $core, $user, $tpl, $pdh, $SID, $in, $eqdkp_root_path;

		if($messages)
		{
			$pdh->process_hook_queue();
			$core->messages($messages);
		}
		include($eqdkp_root_path.'core/html_pdh_tag_table.class.php');
		$view_list = $pdh->aget('adjustment', 'group_key', 0, array($pdh->get('adjustment', 'id_list', array())));
		$view_list = array_flip($view_list);
		$hptt_page_settings = $pdh->get_page_settings('admin_manage_adjustments', 'hptt_admin_manage_adjustments_adjlist');
		$hptt = new html_pdh_tag_table($hptt_page_settings, $view_list, $view_list, array('%link_url%' => 'manage_adjustments.php', '%link_url_suffix%' => '&update=true'));
		$page_suffix = '&amp;start='.$in->get('start', 0);
		$sort_suffix = '?sort='.$in->get('sort', '0|desc');
    
    //footer
		$adj_count = count($view_list);
		$footer_text = sprintf($user->lang['listadj_footcount'], $adj_count ,$user->data['user_alimit']);
		
		$tpl->assign_vars(array(
			'SID'			=> $SID,
			'ADJ_LIST'		=> $hptt->get_html_table($in->get('sort','0|desc'), $page_suffix, $in->get('start', 0), $user->data['user_alimit'], $footer_text),
			'PAGINATION' 	=> generate_pagination('manage_adjustments.php'.$sort_suffix, $adj_count, $user->data['user_alimit'], $in->get('start', 0)),
			//language
			'L_ADJS'		=> $user->lang['manadjs_title'],
			'L_DATE'		=> $user->lang['date'],
			'L_REASON'		=> $user->lang['reason'],
			'L_RAID'		=> $user->lang['raid'],
			'L_MEMBERS'		=> $user->lang['members'],
			'L_VALUE'		=> $user->lang['value'],
			'L_MASS_DEL'	=> $user->lang['delete_selected_adjs'],
			'L_ADD_ADJ'		=> $user->lang['add_adjustment'],
			'HPTT_COLUMN_COUNT'	=> $hptt->get_column_count(),
			'L_EVENT'		=> $user->lang['event'])
		);

		$core->set_vars(array(
            'page_title'    => $user->lang['manadjs_title'],
            'template_file' => 'admin/manage_adjustments.html',
            'display'       => true)
        );
    }

    function get_post($norefresh=false)
    {
    	global $user, $in;
    	$adj['reason'] = $in->get('reason','');
        foreach($in->getArray('members','int') as $member)
        {
            $adj['members'][] = (int) $member;
        }
    	if(!$adj['reason'])
    	{
    		$missing[] = $user->lang['reason'];
    	}
    	if(empty($adj['members']))
    	{
    		$missing[] = $user->lang['members'];
    	}
    	if($missing AND !$norefresh)
    	{
    		$this->edit_adj(array('title' => $user->lang['missing_values'], 'text' => implode(', ',$missing), 'color' => 'red'));
    	}
        $adj['value'] = $in->get('value',0.0);
        list($day, $mon, $year) = explode('.', $in->get('date','1.1.1970'));
        $adj['date'] = mktime($in->get('hour',0), $in->get('min',0), $in->get('sec',0), $mon, $day, $year);
        $adj['raid_id'] = $in->get('raid_id',0);
        $adj['event'] = $in->get('event',0);
        return $adj;
    }
}

$manadjs = new ManageAdjs;
$manadjs->process();
?>