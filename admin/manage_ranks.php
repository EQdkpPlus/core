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

class Manage_Ranks extends EQdkp_Admin
{
	function Manage_Ranks()
	{
		global $core;

		parent::eqdkp_admin();

		$this->assoc_buttons(array(
			'r_save' => array(
				'name' => 'save',
				'process' => 'rank_save',
				'check' => 'a_members_man'),
			'r_del' => array(
				'name' => 'del',
				'process' => 'rank_del',
				'check' => 'a_members_man'),
			'confirm' => array(
				'name' => 'confirm',
				'process' => 'rank_del',
				'check' => 'a_members_man'),
			'form' => array(
				'name' => '',
				'process' => 'display_form',
				'check' => 'a_members_man')
			)
		);
	}

	function rank_save()
	{
		global $user, $pdh, $in;
		$noranks = false;
		$retu = array();
		$ranks = $this->get_post();
		if($ranks)
		{
			$id_list = $pdh->get('rank', 'id_list');
			foreach($ranks as $rank)
			{
				$func = (in_array($rank['id'], $id_list)) ? 'update_rank' : 'add_rank';
				$retu[] = $pdh->put('rank', $func, array($rank['id'], $rank['name'], $rank['hide'], $rank['prefix'], $rank['suffix']));
				$names[] = $rank['name'];
			}
			if(in_array(false, $retu))
			{
				$message = array('title' => $user->lang['save_no_suc'], 'text' => implode(', ', $names), 'color' => 'red');
			}
			elseif(in_array(true, $retu))
			{
				$message = array('title' => $user->lang['save_suc'], 'text' => implode(', ', $names), 'color' => 'green');
			}
		}
		else
		{
			$message = array('title' => '', 'text' => $user->lang['no_ranks_selected'], 'color' => 'grey');
		}
		$this->display_form($message);
	}

	function rank_del()
	{
		global $user, $pdh, $in;
		$noranks = false;
		if($in->exists('ranks') AND !isset($_POST['confirm']))
		{
			foreach($this->get_post() as $rank)
			{
			  if(isset($rank['id']) && $rank['selected'] != "")
			  {
				$rank_ids[] = $rank['id'];
				$names[] = $rank['name'];
			  }
			  else
			  {
			  	$noranks = true;
			  }
			}
			confirm_delete($user->lang['confirm_delete_ranks'].'<br />'.implode(', ', $names), 'rank_ids', implode(',', $rank_ids));
		}
		elseif($_POST['rank_ids'] AND $_POST['confirm'] == $user->lang['yes'])
		{
			$rankids = explode(',', $in->get('rank_ids',''));
			foreach($rankids as $id)
			{
                $names[] = $pdh->get('rank', 'name', ($id));
				$retu[] = $pdh->put('rank', 'delete_rank', array($id));
			}
			if(in_array(false, $retu))
			{
				$message = array('title' => $user->lang['del_no_suc'], 'text' => implode(', ', $names), 'color' => 'red');
			}
			else
			{
				$message = array('title' => $user->lang['del_suc'], 'text' => implode(', ', $names), 'color' => 'green');
			}
		}
		if($noranks)
		{
			$message = array('title' => '', 'text' => $user->lang['no_ranks_selected'], 'color' => 'grey');
		}
		$this->display_form($message);
	}

	function display_form($messages=false)
	{
		global $core, $user, $tpl, $pdh, $SID, $in;
		
		if($messages)
		{
			$pdh->process_hook_queue();
			$core->messages($messages);
		}

		$new_id = 0;
		$order = $in->get('order','0.0');
		$red = 'RED'.str_replace('.', '', $order);
		$ranks = $pdh->aget('rank', 'name', 0, array($pdh->get('rank', 'id_list')));
		unset($ranks[0]);
		if($order == '0.0')
		{
			arsort($ranks);
		}
		else
		{
			asort($ranks);
		}
		$key = 0;
		$new_id = 1;
		ksort($ranks);
		foreach($ranks as $id => $name)
		{
			$tpl->assign_block_vars('ranks', array(
				'KEY'	=> $key,
				'ID'	=> $id,
				'NAME'	=> $name,
				'HIDE'	=> ($pdh->get('rank', 'is_hidden', array($id))) ? 'checked="checked"' : '',
				'PREFIX' => $pdh->get('rank', 'prefix', array($id)),
				'SUFFIX' => $pdh->get('rank', 'suffix', array($id)),
				'ROW_CLASS' => $core->switch_row_class())
			);
			$key++;
			$new_id = ($new_id == $id) ? $id+1 : $new_id;
		}
		$tpl->assign_vars(array(
			'ACTION' 	=> 'manage_ranks.php'.$SID,
			$red 		=> '_red',
			'SID'		=> $SID,
			'ID'		=> $new_id,
			'KEY'		=> $key,
			'ROW_CLASS' => $core->switch_row_class(),
			//Language
			'L_RANKS'	=> $user->lang['edit_ranks'],
			'L_NAME'	=> $user->lang['name'],
			'L_HIDE'	=> $user->lang['hide'],
			'L_PREFIX'	=> $user->lang['list_prefix'],
			'L_SUFFIX'	=> $user->lang['list_suffix'],
			'L_SAVE'	=> $user->lang['save'],
			'L_DEL'		=> $user->lang['del_selected_ranks']
			)
		);
		
		$tpl->add_js('function select_all_chechboxes(area_id){
				
	var area = document.getElementById(area_id);
	var checkboxes = area.getElementsByTagName(\'input\');

	for (var i=0; i<checkboxes.length; i++){
		checkboxes[i].checked = true;

	}
	
}
			
function deselect_all_chechboxes(area_id){

	var area = document.getElementById(area_id);

	var checkboxes = area.getElementsByTagName(\'input\');

	for (var i=0; i<checkboxes.length; i++){
		checkboxes[i].checked = false;

	}
		
}');
		$core->set_vars(array(
            'page_title'    => $user->lang['manrank_title'],
            'template_file' => 'admin/manage_ranks.html',
            'display'       => true)
        );
	}

	function get_post()
	{
		global $in;
		$ranks = array();
		if($in->exists('ranks', 'string'))
		{
			foreach($in->getArray('ranks', 'string') as $key => $rank)
			{
				if(isset($rank['id']) AND $rank['id'] AND !empty($rank['name']))
				{
					$ranks[] = array(
						'selected'	=> $in->get('ranks:'.$key.':selected'),
						'id'	=> $in->get('ranks:'.$key.':id',0),
						'name'	=> $in->get('ranks:'.$key.':name',''),
						'hide'	=> $in->get('ranks:'.$key.':hide',0),
						'prefix' => $in->get('ranks:'.$key.':prefix',''),
						'suffix' => $in->get('ranks:'.$key.':suffix','')
					);
				}
			}
			return $ranks;
		}
		return false;
	}
}
$manranks = new Manage_Ranks;
$manranks->process();
?>