<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * addnews.php
 * Began: Wed December 25 2002
 *
 * $Id$
 *
 ******************************/

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class Add_News extends EQdkp_Admin
{
    var $news     = array();            // Holds news data if URI_NEWS is set               @var news
    var $old_news = array();            // Holds news data from before POST                 @var old_news

    function add_news()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;

        parent::eqdkp_admin();

        $this->news = array(
            'news_headline' => post_or_db('news_headline'),
            'news_message'  => post_or_db('news_message'),
            'showRaid_id'  => post_or_db('showRaid_id')
        );

        // Vars used to confirm deletion
        $this->set_vars(array(
            'confirm_text'  => $user->lang['confirm_delete_news'],
            'uri_parameter' => URI_NEWS)
        );

        $this->assoc_buttons(array(
            'add' => array(
                'name'    => 'add',
                'process' => 'process_add',
                'check'   => 'a_news_add'),
            'update' => array(
                'name'    => 'update',
                'process' => 'process_update',
                'check'   => 'a_news_upd'),
            'delete' => array(
                'name'    => 'delete',
                'process' => 'process_delete',
                'check'   => 'a_news_del'),
            'form' => array(
                'name'    => '',
                'process' => 'display_form',
                'check'   => 'a_news_'))
        );

        // Build the news array
        // ---------------------------------------------------------
        if ( $this->url_id )
        {
            $sql = 'SELECT news_headline, news_message, showRaids_id
                    FROM ' . NEWS_TABLE . "
                    WHERE news_id='" . $this->url_id . "'";
            $result = $db->query($sql);
            if ( !$row = $db->fetch_record($result) )
            {
                message_die($user->lang['error_invalid_news_provided']);
            }
            $db->free_result($result);

            $this->time = time();
            $this->news = array(
                'news_headline' => post_or_db('news_headline', $row),
                'news_message'  => post_or_db('news_message', $row),
                'news_showRaids_id'  => post_or_db('showRaids_id', $row)
            );
        }
    }

    function error_check()
    {
        global $user;

        $this->fv->is_filled(array(
            'news_headline' => $user->lang['fv_required_headline'],
            'news_message'  => $user->lang['fv_required_message'])
        );

        return $this->fv->is_error();
    }

    // ---------------------------------------------------------
    // Process Add
    // ---------------------------------------------------------
    function process_add()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;

        // Insert the news

      	if(is_array($_POST['raid_id']))
      	{
      		$raids_id = implode(",", $_POST['raid_id']) ;
      	}

        $query = $db->build_query('INSERT', array(
            'news_headline' => stripslashes($_POST['news_headline']),
            'news_message'  => stripslashes($_POST['news_message']),
            'news_date'     => $this->time,
            'user_id'       => $user->data['user_id'],
            'showRaids_id'   => stripslashes($raids_id)
            )
        );
        $db->query('INSERT INTO ' . NEWS_TABLE . $query);
        $this_news_id = $db->insert_id();

        //
        // Logging
        //
        $log_action = array(
            'header'           => '{L_ACTION_NEWS_ADDED}',
            'id'               => $this_news_id,
            '{L_HEADLINE}'     => $_POST['news_headline'],
            '{L_MESSAGE_BODY}' => nl2br($_POST['news_message']),
            '{L_ADDED_BY}'     => $this->admin_user);
        	$this->log_insert(array(
            'log_type'   => $log_action['header'],
            'log_action' => $log_action)
        );

        //
        // Success message
        //
        $success_message = $user->lang['admin_add_news_success'];
        $link_list = array(
            $user->lang['add_news']  => 'addnews.php' . $SID,
            $user->lang['list_news'] => 'listnews.php' . $SID);
        $this->admin_die($success_message, $link_list);
    }

    // ---------------------------------------------------------
    // Process Update
    // ---------------------------------------------------------
    function process_update()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;

        //
        // Get the old data
        //
        $this->get_old_data();

        //
        // Update the news table
        //

        if(is_array($_POST['raid_id']))
      	{
        	$raids_id = implode(",", $_POST['raid_id']) ;
        }

        if ( isset($_POST['update_date']) )
        {
            $query = $db->build_query('UPDATE', array(
                'news_headline' => stripslashes($_POST['news_headline']),
                'news_message'  => stripslashes($_POST['news_message']),
                'news_date'     => $this->time,
                'showRaids_id'   => $raids_id
                )
            );
        }
        else
        {
            $query = $db->build_query('UPDATE', array(
                'news_headline' => stripslashes($_POST['news_headline']),
                'news_message'  => stripslashes($_POST['news_message']),
                'showRaids_id'   => $raids_id
                )
            );
        }
        $db->query('UPDATE ' . NEWS_TABLE . ' SET ' . $query . " WHERE news_id='" . $this->url_id . "'");

        //
        // Logging
        //
        $log_action = array(
            'header'              => '{L_ACTION_NEWS_UPDATED}',
            'id'                  => $this->url_id,
            '{L_HEADLINE_BEFORE}' => $this->old_news['news_headline'],
            '{L_MESSAGE_BEFORE}'  => nl2br($this->old_news['news_message']),
            '{L_HEADLINE_AFTER}'  => $this->find_difference($this->old_news['news_headline'], $_POST['news_headline']),
            '{L_MESSAGE_AFTER}'   => nl2br($_POST['news_message']),
            '{L_UPDATED_BY}'      => $this->admin_user);
        $this->log_insert(array(
            'log_type'   => $log_action['header'],
            'log_action' => $log_action)
        );

        //
        // Success message
        //
        $success_message = $user->lang['admin_update_news_success'];
        $link_list = array(
            $user->lang['add_news']  => 'addnews.php' . $SID,
            $user->lang['list_news'] => 'listnews.php' . $SID);
        $this->admin_die($success_message, $link_list);
    }

    // ---------------------------------------------------------
    // Process Delete (confirmed)
    // ---------------------------------------------------------
    function process_confirm()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;

        //
        // Get the old data
        //
        $this->get_old_data();

        //
        // Remove the news entry
        //
        $sql = 'DELETE FROM ' . NEWS_TABLE . "
                WHERE news_id='" . $this->url_id . "'";
        $db->query($sql);

        //
        // Logging
        //
        $log_action = array(
            'header'           => '{L_ACTION_NEWS_DELETED}',
            'id'               => $this->url_id,
            '{L_HEADLINE}'     => $this->old_news['news_headline'],
            '{L_MESSAGE_BODY}' => nl2br($this->old_news['news_message']));
        $this->log_insert(array(
            'log_type'   => $log_action['header'],
            'log_action' => $log_action)
        );

        //
        // Success message
        //
        $success_message = $user->lang['admin_delete_news_success'];
        $link_list = array(
            $user->lang['add_news']  => 'addnews.php' . $SID,
            $user->lang['list_news'] => 'listnews.php' . $SID);
        $this->admin_die($success_message, $link_list);
    }

    // ---------------------------------------------------------
    // Process helper methods
    // ---------------------------------------------------------
    function get_old_data()
    {
        global $db;

        $sql = 'SELECT news_headline, news_message, showRaids_id
                FROM ' . NEWS_TABLE . "
                WHERE news_id='" . $this->url_id . "'";
        $result = $db->query($sql);
        while ( $row = $db->fetch_record($result) )
        {
            $this->old_news = array(
                'news_headline' => addslashes($row['news_headline']),
                'news_message'  => addslashes($row['news_message']),
                'news_showRaids_id'  => $row['showRaids_id']
            );
        }
        $db->free_result($result);
    }

    // ---------------------------------------------------------
    // Display form
    // ---------------------------------------------------------
    function display_form()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;

		##########################################################
        //
		// Build raid drop-down
		//
		// Show all raids?
		$show_all = ( (!empty($_GET['show'])) && ($_GET['show'] == 'all') ) ? true : false;

		// Make two_weeks two weeks before the date the item was purchased
		$two_weeks = mktime(0, 0, 0, date('m', $this->time), date('d', $this->time)-90, date('y', $this->time));

		$sql_where_clause = ( $show_all ) ? '' : ' WHERE (raid_date >= ' . $two_weeks . ')';
		$sql = 'SELECT raid_id, raid_name, raid_date
				FROM ' . RAIDS_TABLE .
				$sql_where_clause . '
				ORDER BY raid_date DESC';
		$result = $db->query($sql);


		$raid_ids = explode(",",$this->news['news_showRaids_id']);
		while ( $row = $db->fetch_record($result) )
		{

			$selected = ( @in_array($row['raid_id'],$raid_ids) ) ? ' selected="selected"' : '';

			$tpl->assign_block_vars('raids_row', array(
				'VALUE'    => $row['raid_id'],
				'SELECTED' => $selected,
				'OPTION'   => date($user->style['date_notime_short'], $row['raid_date']) . ' - ' . stripslashes($row['raid_name']))
														);
		}
        $db->free_result($result);
        ##########################################################


        $tpl->assign_vars(array(
            // Form vars
            'F_ADD_NEWS' => 'addnews.php' . $SID,
            'NEWS_ID'    => $this->url_id,
            'S_UPDATE'   => ( $this->url_id ) ? true : false,

            // Form values
            'HEADLINE' => stripslashes(htmlspecialchars($this->news['news_headline'])),
            'MESSAGE'  => stripmultslashes($this->news['news_message']),

            // Language (General)
            'L_HEADLINE'       => $user->lang['headline'],
            'L_MESSAGE_BODY'   => $user->lang['message_body'],
            'L_MESSAGE_SHOW_LOOT_RAID' => $user->lang['message_show_loot_raid'],
            'L_ADD_NEWS'       => $user->lang['add_news'],
            'L_RESET'          => $user->lang['reset'],
            'L_UPDATE_NEWS'    => $user->lang['update_news'],
            'L_DELETE_NEWS'    => $user->lang['delete_news'],
            'L_UPDATE_DATE_TO' => sprintf($user->lang['update_date_to'], date('m/d/y h:ia T', time())),
             'L_VIDEO_HELP' => $user->lang['News_vid_help'],

            // Language (Help messages)
            'L_B_HELP' => $user->lang['b_help'],
            'L_I_HELP' => $user->lang['i_help'],
            'L_U_HELP' => $user->lang['u_help'],
            'L_Q_HELP' => $user->lang['q_help'],
            'L_C_HELP' => $user->lang['c_help'],
            'L_P_HELP' => $user->lang['p_help'],
            'L_W_HELP' => $user->lang['w_help'],

            'L_IT_HELP' => $user->lang['it_help'],
            'L_II_HELP' => $user->lang['ii_help'],

            // Form validation
            'FV_HEADLINE' => $this->fv->generate_error('news_headline'),
            'FV_MESSAGE'  => $this->fv->generate_error('news_message'),

            // Javascript messages
            'MSG_HEADLINE_EMPTY' => $user->lang['fv_required_headline'],
            'MSG_MESSAGE_EMPTY'  => $user->lang['fv_required_message'],

            // Buttons
            'S_ADD' => ( !$this->url_id ) ? true : false)
        );

        $eqdkp->set_vars(array(
            'page_title'    => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['addnews_title'],
            'template_file' => 'admin/addnews.html',
            'display'       => true)
        );
    }
}

$add_news = new Add_News;
$add_news->process();
?>
