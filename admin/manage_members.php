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

class Manage_Members extends EQdkp_Admin
{
	private $simple_head = 'full';

	function Manage_Members()
	{
		global $core, $in;

		parent::eqdkp_admin();

		$this->simple_head = ($in->get('simple_head', false)) ? 'simple' : 'full';

		$this->assoc_params(array(
			'm_add_update' => array(
				'name' => 'mupd',
				'process' => 'member_upd',
				'check' => 'a_members_man')
			)
		);

		$this->assoc_buttons(array(
			'm_save' => array(
				'name' => 'msave',
				'process' => 'member_save',
				'check' => 'a_members_man'),
			'm_del' => array(
				'name' => 'mdel',
				'process' => 'member_del',
				'check' => 'a_members_man'),
			'm_stat' => array(
				'name' => 'mstatus',
				'process' => 'member_status',
				'check' => 'a_members_man'),
			'rank_c' => array(
				'name' => 'rankc',
				'process' => 'member_ranks',
				'check' => 'a_members_man'),
			'confirm' => array(
				'name' => 'confirm',
				'process' => 'member_del',
				'check' => 'a_members_man'),
			'm_history' => array(
				'name' => 'mhistory',
				'process' => 'do_member_trans',
				'check' => 'a_members_man'),
			'form' => array(
				'name' => '',
				'process' => 'display_form',
				'check' => 'a_members_man')
			)
		);
	}

	function member_save()
	{
		global $pdh, $user, $in;
		$member = $this->get_post();
		$member_id = $in->get('member_id', 0);
		if($member_id)
		{
			$success = $pdh->put('member', 'update_member', array($member_id, $member['name'], $member['level'], $member['raceid'], $member['classid'], $member['rankid'], $member['mainid'], $member['active']));
		}
		else
		{
			$success = $pdh->put('member', 'add_member', array($member['name'], $member['level'], $member['raceid'], $member['classid'], $member['rankid'], $member['mainid'], $member['active']));
		}
		if($success)
		{
			$message = array(
				'title' => $user->lang['save_suc'],
				'text'	=> $member['name'],
				'color' => 'green'
			);
		}
		else
		{
			$message = array(
				'title' => $user->lang['save_nosuc'],
				'text'	=> $member['name'],
				'color'	=> 'red'
			);
		}
		$this->display_form($message);
	}

	function member_del()
	{
		global $user, $pdh, $in;
		$member_ids = $in->getArray('selected_ids', 'int');
		if($member_id = $in->get('member_id', 0))
		{
			$member_ids[] = $member_id;
		}
		if(!isset($_POST['confirm']) AND $member_ids)
		{
			$names = implode(', ', $pdh->aget('member', 'name', 0, array(0 => $member_ids)));
			$member_ids = implode(',', $member_ids);
			confirm_delete($user->lang['confirm_delete_members'].':<br />'.$names, 'member_ids', $member_ids);
		}
		elseif(isset($_POST['confirm']) AND $_POST['confirm'] == $user->lang['yes'])
		{
			$ids = explode(',', $in->get('member_ids',''));
			foreach($ids as $id)
			{
				//delete member
				$success[intval($id)] = $pdh->put('member', 'delete_member', array(intval($id)));
			}
			foreach($success as $id => $suc)
			{
				if($suc)
				{
					$pos[] = $pdh->get('member', 'name', array($id));
				}
				else
				{
					$neg[] = $pdh->get('member', 'name', array($id));
				}
			}
			if($neg)
			{
				$messages[] = array('title' => $user->lang['del_nosuc'], 'text' => $user->lang['mems_no_del'].implode(', ', $neg), 'color' => 'red');
			}
			if($pos)
			{
				$messages[] = array('title' => $user->lang['del_suc'], 'text' => $user->lang['mems_del'].implode(', ', $pos), 'color' => 'green');
			}
			$this->display_form($messages);
		}
		else
		{
			$this->display_form();
		}
	}

	function do_member_trans()
	{
		global $pdh, $user, $in;
		if($transid = $in->get('transid',0) AND $member_id = $in->get('member_id',0) AND $transid != $member_id)
		{
			if($pdh->put('member', 'trans_member', array($member_id, $transid)))
			{
				$message = array(
					'title' => $user->lang['save_suc'],
					'text'	=> sprintf($user->lang['mem_history_trans'], $pdh->get('member', 'name', array($member_id)), $pdh->get('member', 'name', array(transid))),
					'color' => 'green');
			}
		}
		if(!$message)
		{
			$message = array(
				'title' => $user->lang['save_nosuc'],
				'text'	=> $user->lang['no_mem_history'],
				'color'	=> 'red');
		}
		$this->display_form($message);
	}

	function member_ranks()
	{
		global $pdh, $user, $in;
		$sucs = array();
		if($member_ids = $in->getArray('selected_ids','int'))
		{
			foreach($member_ids as $id)
			{
				$sucs[$id] = $pdh->put('member', 'update_member', array($id, '', '', '', '', $in->get('rank',0)));
			}
		}
		foreach($sucs as $id => $suc)
		{
			if($suc)
			{
				$pos[] = $pdh->get('member', 'name', array($id));
			}
			else
			{
				$neg[] = $pdh->get('member', 'name', array($id));
			}
        }
		if($neg)
		{
			$messages[] = array('title' => $user->lang['save_nosuc'], 'text' => $user->lang['mems_no_rank_change'].implode(', ', $neg), 'color' => 'red');
		}
		if($pos)
		{
			$messages[] = array('title' => $user->lang['save_suc'], 'text' => $user->lang['mems_rank_change'].implode(', ', $pos), 'color' => 'green');
		}
		$this->display_form($messages);

	}

	function member_status()
	{
		global $pdh, $user, $in;
		$sucs = array();
		if($member_ids = $in->getArray('selected_ids','int'))
		{
			foreach($member_ids as $id)
			{
				$status = ($pdh->get('member', 'active', array($id))) ? 0 : 1;
				$sucs[$id] = $pdh->put('member', 'update_member', array($id, '', '', '', '', '', '', $status));
			}
		}
		foreach($sucs as $id => $suc)
		{
			if($suc)
			{
				$pos[] = $pdh->get('member', 'name', array($id));
			}
			else
			{
				$neg[] = $pdh->get('member', 'name', array($id));
			}
        }
		if($neg)
		{
			$messages[] = array('title' => $user->lang['save_nosuc'], 'text' => $user->lang['mems_no_status_change'].implode(', ', $neg), 'color' => 'red');
		}
		if($pos)
		{
			$messages[] = array('title' => $user->lang['save_suc'], 'text' => $user->lang['mems_status_change'].implode(', ', $pos), 'color' => 'green');
		}
		$this->display_form($messages);
	}

    function member_upd($message=false)
    {
        global $user, $core, $tpl, $SID, $pdh, $html, $in, $game;

        $member_id = $in->get('member_id',0);
        if($message)
        {
        	$core->messages($message);
        	$member = $this->get_post(true);
        }
        else
        {
	        $member = array(
	        	'name' => '',
	        	'rankid' => 1,
	        	'raceid' => $game->get_id('races', 'Unknown'),
	        	'classid' => $game->get_id('classes', 'Unknown'),
	        	'level' => '',
	        	'mainid' => 0,
	        	'active' => 1
	        );
	        if($member_id)
	        {
	        	$member['name'] = $pdh->get('member', 'name', array($member_id));
	        	$member['classid'] = $pdh->get('member', 'classid', array($member_id));
	        	$member['raceid'] = $pdh->get('member', 'raceid', array($member_id));
	        	$member['rankid'] = $pdh->get('member', 'rankid', array($member_id));
	        	$member['mainid'] = $pdh->get('member', 'mainid', array($member_id));
	        	$member['level'] = $pdh->get('member', 'level', array($member_id));
	        	$member['active'] = $pdh->get('member', 'active', array($member_id));
	        }
	    }

        //fetch ranks, members
        $tofetch = array('ranks' => 'rank', 'members' => 'member');
        foreach($tofetch as $arrayname => $modul)
        {
        	${$arrayname} = $pdh->aget($modul, 'name', 0, array($pdh->get($modul, 'id_list')));
        }
        unset($ranks[0]);
        asort($members);
		$members = array_merge(array(0 => $user->lang['mainchar']), $members);
        $tpl->assign_vars(array(
        	'S_RACE'		=> $game->type_exists('races'),
        	'SID'			=> $SID,
        	'S_UPD'			=> ($member_id) ? true : false,
        	'MEMBERS'		=> $html->DropDown('transid', $members, 0),
        	//member-data
        	'ID'			=> $member_id,
			'NAME' 			=> $member['name'],
			'MAINCHAR'		=> $html->DropDown('mainid', $members, $member['mainid']),
			'LVL'			=> $member['level'],
			'RANK'			=> $html->DropDown('rankid', $ranks, $member['rankid']),
			'STATUS'		=> $member['active'],
			'CLASS'			=> $html->DropDown('classid', $game->get('classes'), $member['classid']),
			'RACE'			=> $html->DropDown('raceid', $game->get('races'), $member['raceid']),
        	//language
        	'L_DEL'			=> $user->lang['delete'],
        	'L_RACE'		=> $user->lang['race'],
        	'L_CLASS'		=> $user->lang['class'],
        	'L_ACTIVE'		=> $user->lang['member_active'],
        	'L_RANK'		=> $user->lang['rank'],
        	'L_LVL'			=> $user->lang['level'],
        	'L_MAINCHAR'	=> $user->lang['mainchar'],
        	'L_NAME'		=> $user->lang['name'],
        	'L_MEMBER'		=> ($member_id) ? $user->lang['uc_edit_char'] : $user->lang['uc_add_char'],
        	'L_HISTORY'		=> $user->lang['member_history'],
        	'L_DOIT'		=> $user->lang['doit'],
					'L_CANCEL'	=> $user->lang['cancel'],
					'MAINCHAR_HELP'	=> $html->HelpTooltip($user->lang['mainchar_help']),
        	'L_ADDUPD'		=> $user->lang['save'])
        );

		$core->set_vars(array(
            'page_title'    => $user->lang['manage_members_title'],
            'template_file' => 'admin/manage_members_edit.html',
            'header_format' => $this->simple_head,
            'display'       => true)
        );
    }

	function display_form($messages=false)
	{
		global $user, $core, $tpl, $SID, $eqdkp_root_path, $pdh, $html, $in, $game;

		if($messages)
		{
			$pdh->process_hook_queue();
			$core->messages($messages);
		}
		include_once($eqdkp_root_path.'core/html_pdh_tag_table.class.php');
		$view_list = $pdh->get('member', 'id_list', array(false, false, false));
		$hptt_page_settings = $pdh->get_page_settings('admin_manage_members', 'hptt_admin_manage_members_memberlist');
    $hptt = new html_pdh_tag_table($hptt_page_settings, $view_list, $view_list, array('%link_url%' => 'manage_members.php', '%link_url_suffix%' => '&mupd=true'));
		$ranks = $pdh->aget('rank', 'name', 0, array($pdh->get('rank', 'id_list', array())));
		asort($ranks);
    $page_suffix = '&amp;start='.$in->get('start', 0);
    $sort_suffix = '?sort='.$in->get('sort', '0|desc');

		//footer
		$character_count = count($view_list);
		$footer_text = sprintf($user->lang['listmembers_footcount'], $character_count);

		$tpl->assign_vars(array(
            'SID'       => $SID,
            'S_RACE'    => $game->type_exists('races'),
            'MEMBER_LIST' => $hptt->get_html_table($in->get('sort',''), $page_suffix, $in->get('start', 0), $user->data['user_climit'], $footer_text),
            'RANK_SEL' => $html->DropDown('rank', $ranks, ''),
            'PAGINATION' => generate_pagination('manage_members.php'.$sort_suffix, $character_count, $user->data['user_climit'], $in->get('start', 0)),
						'HPTT_COLUMN_COUNT'	=> $hptt->get_column_count(),
            //language
            'L_MEMBERS' => $user->lang['chars'],
			'L_MASS_DEL' => $user->lang['delete_selected_members'],
			'L_ADD_MEMBER' => $user->lang['add_member'],
			'L_MASS_STAT' => $user->lang['mass_stat_change'],
			'L_MASS_RANK' => $user->lang['mass_rank_change'])
		);

		$core->set_vars(array(
            'page_title'    => $user->lang['manage_members_title'],
            'template_file' => 'admin/manage_members.html',
            'header_format' => $this->simple_head,
            'display'       => true)
        );
    }

    function get_post($norefresh=false)
    {
    	global $user, $in;
    	$member['name'] = $in->get('name','');
    	if(empty($member['name']) AND !$norefresh)
    	{
    		$this->member_upd(array('title' => $user->lang['missing_values'], 'text' => $user->lang['name'], 'color' => 'red'));
    	}
	    $member['classid'] = $in->get('classid',0);
	    $member['raceid'] = $in->get('raceid',0);
	    $member['rankid'] = $in->get('rankid',0);
	    $member['mainid'] = $in->get('mainid',0);
	    $member['level'] = $in->get('level',0);
	    $member['active'] = $in->get('status',1);
	    return $member;
	}
}
$manage_mems = new Manage_Members;
$manage_mems->process();
?>