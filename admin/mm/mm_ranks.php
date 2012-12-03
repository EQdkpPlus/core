<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * mm_ranks.php
 * Began: Fri February 14 2003
 *
 * $Id$
 *
 ******************************/

// This script handles editing membership ranks
if ( !defined('EQDKP_INC') ){
    header('HTTP/1.0 404 Not Found');exit;
}

class MM_Ranks extends EQdkp_Admin
{
    function mm_ranks()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;

        parent::eqdkp_admin();

        $this->assoc_buttons(array(
            'submit' => array(
                'name'    => 'submit',
                'process' => 'process_submit',
                'check'   => 'a_members_man'),
            'form' => array(
                'name'    => '',
                'process' => 'display_form',
                'check'   => 'a_members_man'))
        );
    }

    function error_check()
    {
        return $this->fv->is_error();
    }

    // ---------------------------------------------------------
    // Process submit
    // ---------------------------------------------------------
    function process_submit()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;

        foreach ( $_POST['ranks'] as $rank_id => $rank_name )
        {
            $sql = 'DELETE FROM ' . MEMBER_RANKS_TABLE . "
                    WHERE rank_id='" . $rank_id . "'";
            $db->query($sql);

            // If the rank's been removed, NULL the member_rank for users that have it
            if ( $rank_name == '' )
            {
                $sql = 'UPDATE ' . MEMBERS_TABLE . "
                        SET member_rank_id = NULL
                        WHERE member_rank_id='" . $rank_id . "'";
                $db->query($sql);
            }
            // Otherwise re-add the rank to the table
            else
            {
                $rank_prefix = ( isset($_POST['prefix'][$rank_id]) ) ? $_POST['prefix'][$rank_id] : '';
                $rank_suffix = ( isset($_POST['suffix'][$rank_id]) ) ? $_POST['suffix'][$rank_id] : '';

                $rank_prefix = undo_sanitize_tags(stripslashes($rank_prefix));
                $rank_suffix = undo_sanitize_tags(stripslashes($rank_suffix));

                $query = $db->build_query('INSERT', array(
                    'rank_id'     => $rank_id,
                    'rank_name'   => $rank_name,
                    'rank_hide'   => ( isset($_POST['hide'][$rank_id]) ) ? '1' : '0',
                    'rank_prefix' => $rank_prefix,
                    'rank_suffix' => $rank_suffix)
                );
                $db->query('INSERT INTO ' . MEMBER_RANKS_TABLE . $query);
            }
        }

        header('Location: manage_members.php' . $SID . '&mode=ranks');
    }

    // ---------------------------------------------------------
    // Display form
    // ---------------------------------------------------------
    function display_form()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;

        //
        // Populate the fields
        //
        $max_id = 0;
        $sql = 'SELECT rank_id, rank_name, rank_hide, rank_prefix, rank_suffix
                FROM ' . MEMBER_RANKS_TABLE . '
                WHERE rank_id > 0
                ORDER BY rank_id';
        $result = $db->query($sql);
        while ( $row = $db->fetch_record($result) )
        {
            $prefix = htmlspecialchars($row['rank_prefix']);
            $suffix = htmlspecialchars($row['rank_suffix']);

            $tpl->assign_block_vars('ranks_row', array(
                'ROW_CLASS'    => $eqdkp->switch_row_class(),
                'RANK_ID'      => $row['rank_id'],
                'RANK_NAME'    => stripslashes($row['rank_name']),
                'RANK_PREFIX'  => stripslashes($prefix),
                'RANK_SUFFIX'  => stripslashes($suffix),
                'HIDE_CHECKED' => ( $row['rank_hide'] == '1' ) ? 'checked="checked"' : '')
            );
            $max_id = ( $max_id < $row['rank_id'] ) ? $row['rank_id'] : $max_id;
        }
        $db->free_result($result);

        $tpl->assign_vars(array(
            // Form vars
            'F_EDIT_RANKS' => 'manage_members.php' . $SID . '&amp;mode=ranks',

            // Form values
            'ROW_CLASS' => $eqdkp->switch_row_class(),
            'RANK_ID'   => ($max_id + 1),

            // Language
            'L_EDIT_RANKS_TITLE' => $user->lang['edit_ranks'],
            'L_TITLE'            => $user->lang['title'],
            'L_HIDE'             => $user->lang['hide'],
            'L_LIST_PREFIX'      => $user->lang['list_prefix'],
            'L_LIST_SUFFIX'      => $user->lang['list_suffix'],
            'L_EDIT_RANKS'       => $user->lang['edit_ranks'],
            'L_RESET'            => $user->lang['reset'])
        );

        $eqdkp->set_vars(array(
            'page_title'    => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['manage_members_title'],
            'template_file' => 'admin/mm_ranks.html',
            'display'       => true)
        );
    }
}
?>