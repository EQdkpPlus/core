<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * manage_members.php
 * Began: Sun January 5 2003
 * 
 * $Id$
 * 
 ******************************/

// Notice: Since 'Manage Members' function as a whole handles a lot of form and 
// processing code, this script will serve only as a framework for other processing
// scripts (found in the mm directory)
 
define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class Manage_Members extends EQdkp_Admin
{
    function manage_members()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;
        
        parent::eqdkp_admin();
        
        $this->assoc_buttons(array(
            'form' => array(
                'name'    => '',
                'process' => 'display_menu',
                'check'   => 'a_members_man'))
        );
        
        $this->assoc_params(array(
            'transfer' => array(
                'name'    => 'mode',
                'value'   => 'transfer',
                'process' => 'mm_transfer',
                'check'   => 'a_members_man'),
            'addmember' => array(
                'name'    => 'mode',
                'value'   => 'addmember',
                'process' => 'mm_addmember',
                'check'   => 'a_members_man'),
            'list' => array(
                'name'    => 'mode',
                'value'   => 'list',
                'process' => 'mm_listmembers',
                'check'   => 'a_members_man'),
            'ranks' => array(
                'name'    => 'mode',
                'value'   => 'ranks',
                'process' => 'mm_ranks',
                'check'   => 'a_members_man'))
        );
    }
    
    function error_check()
    {
        return $this->fv->is_error();
    }
    
    // ---------------------------------------------------------
    // Display menu
    // ---------------------------------------------------------
    function display_menu()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;
        
        $tpl->assign_vars(array(
            'L_MANAGE_MEMBERS'        => $user->lang['manage_members'],
            'L_ADD_MEMBER'            => $user->lang['add_member'],
            'L_LIST_EDIT_DEL_MEMBERS' => $user->lang['list_edit_del_member'],
            'L_EDIT_RANKS'            => $user->lang['edit_ranks'],
            'L_TRANSFER_HISTORY'      => $user->lang['transfer_history'],
            
            'U_ADD_MEMBER'       => 'manage_members.php' . $SID . '&amp;mode=addmember',
            'U_LIST_MEMBERS'     => 'manage_members.php' . $SID . '&amp;mode=list',
            'U_EDIT_RANKS'       => 'manage_members.php' . $SID . '&amp;mode=ranks',
            'U_TRANSFER_HISTORY' => 'manage_members.php' . $SID . '&amp;mode=transfer')
        );
        
        $eqdkp->set_vars(array(
            'page_title'    => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['manage_members_title'],
            'template_file' => 'admin/mm_menu.html',
            'display'       => true)
        );
    }
    
    // ---------------------------------------------------------
    // Transfer history
    // ---------------------------------------------------------
    function mm_transfer()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;
        
        include('mm/mm_transfer.php');
        $mm_extension = new MM_Transfer;
        $mm_extension->process();
    }
    
    // ---------------------------------------------------------
    // Add member
    // ---------------------------------------------------------
    function mm_addmember()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;
        
        include('mm/mm_addmember.php');
        $mm_extension = new MM_Addmember;
        $mm_extension->process();
    }
    
    // ---------------------------------------------------------
    // List members
    // ---------------------------------------------------------
    function mm_listmembers()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;
        
        include('mm/mm_listmembers.php');
        $mm_extension = new MM_Listmembers;
        $mm_extension->process();        
    }
    
    // ---------------------------------------------------------
    // Ranks
    // ---------------------------------------------------------
    function mm_ranks()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;
        
        include('mm/mm_ranks.php');
        $mm_extension = new MM_Ranks;
        $mm_extension->process();
    }
}

$manage_members = new Manage_Members;
$manage_members->process();
?>