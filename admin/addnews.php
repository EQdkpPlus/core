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
    var $news     = array();            // Holds news data if 'n' is set              		@var news
    var $old_news = array();            // Holds news data from before POST                 @var old_news

    function add_news()
    {
        global $db, $core, $user, $tpl, $pm, $time;
        global $SID;

        parent::eqdkp_admin();

        $this->news = array(
            'news_headline' => post_or_db('news_headline'),
            'news_message'  => post_or_db('news_message'),
            'showRaid_id'  => post_or_db('showRaid_id'),
       			'extended_message'  => post_or_db('extended_message'),
      		 	'nocomments'  => post_or_db('nocomments'),
      			'news_permissions'  => post_or_db('news_permissions'),
      			'news_flags' => post_or_db('news_flags', $row),
	  				'news_category'=> post_or_db('news_category'),
						'news_date'	=> post_or_db('news_date'),
						'news_start'	=> post_or_db('news_start'),
						'news_stop'	=> post_or_db('news_stop'),
        );

        // Vars used to confirm deletion
        $this->set_vars(array(
            'uri_parameter' => 'n',)
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
            'form' => array(
                'name'    => '',
                'process' => 'display_form',
                'check'   => 'a_news_'))
        );
				
				$this->assoc_params(array(
            'delete' => array(
                'name'    => 'delete',
                'process' => 'process_delete',
                'check'   => 'a_news_del'),		
        ));

        // Build the news array
        // ---------------------------------------------------------
        if ( $this->url_id )
        {
            $sql = "SELECT news_headline, news_message, showRaids_id, extended_message, nocomments, news_permissions, news_flags, news_category, news_date, news_stop, news_start 
                    FROM __news
                    WHERE news_id='" . $this->url_id . "'";
            $result = $db->query($sql);
            if ( !$row = $db->fetch_record($result) )
            {
                message_die($user->lang['error_invalid_news_provided']);
            }
            $db->free_result($result);

            $this->time = $time->time;
            $this->news = array(
                'news_headline' => post_or_db('news_headline', $row),
                'news_message'  => post_or_db('news_message', $row),
                'news_showRaids_id'  => post_or_db('showRaids_id', $row),
                'extended_message'  => post_or_db('extended_message', $row),
                'nocomments'  => post_or_db('nocomments', $row),
                'news_permissions'  => post_or_db('news_permissions', $row),
                'news_flags' => post_or_db('news_flags', $row),
								'news_category' => post_or_db('news_category', $row),
								'news_date'	=> post_or_db('news_date', $row),
								'news_start'	=> post_or_db('news_start', $row),
								'news_stop'	=> post_or_db('news_stop', $row),
            );
        }
    }

    function error_check()
    {
        global $user, $in;
				if ($in->get('delete') == ""){
					$this->fv->is_filled(array(
							'news_headline' => $user->lang['fv_required_headline'],
							'news_message'  => $user->lang['fv_required_message'])
					);
				}

        return $this->fv->is_error();
    }
    
    
    // ---------------------------------------------------------
    // Process Add
    // ---------------------------------------------------------
    function process_add()
    {
        global $db, $core, $user, $tpl, $pm, $time;
        global $SID, $in, $logs;

        // Insert the news

      	if(is_array($_POST['raid_id']))
      	{
      		$raids_id = implode(",", $_POST['raid_id']) ;
      	}
        if ($in->get('news_date') != ""){
					  $date1 = @explode('.', $in->get('news_date'));
						$d = $date1[0];
						$mo = $date1[1];
						$y  = $date1[2];
						$h = $in->get('news_date_time_hour', 0);
						$m = $in->get('news_date_time_min', 0);
						$date = $time->mktime($h, $m, 0, $mo, $d, $y);
				} else {
					$date = $this->time;
				}
				if ($in->get('news_start') != ""){
					  $date1 = @explode('.', $in->get('news_start'));
						$d = $date1[0];
						$mo = $date1[1];
						$y  = $date1[2];
						$h = $in->get('news_start_time_hour', 0);
						$m = $in->get('news_start_time_min', 0);
						$news_from = $time->mktime($h, $m, 0, $mo, $d, $y);
				} else {
						$news_from = '';
				};
				if ($in->get('news_stop') != ""){
					  $date1 = @explode('.', $in->get('news_stop'));
						$d = $date1[0];
						$mo = $date1[1];
						$y  = $date1[2];
						$h = $in->get('news_stop_time_hour', 0);
						$m = $in->get('news_stop_time_min', 0);
						$news_to = $time->mktime($h, $m, 0, $mo, $d, $y);
				} else {
					$news_to = '';
				};
				
        $query = $db->build_query('INSERT', array(
            'news_headline'     => $in->get('news_headline'),
            'news_message'      => $in->get('news_message'),
            'user_id'           => $user->data['user_id'],
            'showRaids_id'      => $raids_id,
            'extended_message'  => $in->get('news_message_ext', ''),
            'nocomments'        => $in->get('nocomments', 0),
            'news_permissions'  => $in->get('news_permissions', 0),
            'news_flags'        => $in->get('news_flags', 0),
						'news_category'			=> $in->get('category', 1),
						'news_date'					=> $date,
						'news_start'				=> $news_from,
						'news_stop'					=> $news_to,
          )
        );
        $db->query('INSERT INTO __news' . $query);
        $this_news_id = $db->insert_id();

        //
        // Logging
        //
        $log_action = array(
            'header'           => '{L_ACTION_NEWS_ADDED}',
            'id'               => $this_news_id,
            '{L_HEADLINE}'     => sanitize($in->get('news_headline')),
            '{L_MESSAGE_BODY}' => nl2br(sanitize($in->get('news_message').'<br />'.$in->get('news_message_ext', ''))),
            '{L_ADDED_BY}'     => $this->admin_user);
				$logs->add( $log_action['header'], $log_action);
        
				//
        // Success message
        //

				if ($in->get('ref') == "fe"){
					$tpl->add_js("parent.window.location.href = '../viewnews.php';");
				} else {
					$tpl->add_js("parent.window.location.href = 'manage_news.php';");
				}
				$this->display_form();
    }

    // ---------------------------------------------------------
    // Process Update
    // ---------------------------------------------------------
    function process_update()
    {
        global $db, $core, $user, $tpl, $pm;
        global $SID, $in, $logs, $time;

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
				if ($in->get('news_date') != ""){
					  $date1 = @explode('.', $in->get('news_date'));
						$d = $date1[0];
						$mo = $date1[1];
						$y  = $date1[2];
						$h = $in->get('news_date_time_hour', 0);
						$m = $in->get('news_date_time_min', 0);
						$date = $time->mktime($h, $m, 0, $mo, $d, $y);
				} else {
					$date = $this->time;
				}
				if ($in->get('news_start') != ""){
					  $date1 = @explode('.', $in->get('news_start'));
						$d = $date1[0];
						$mo = $date1[1];
						$y  = $date1[2];
						$h = $in->get('news_start_time_hour', 0);
						$m = $in->get('news_start_time_min', 0);
						$news_from = $time->mktime($h, $m, 0, $mo, $d, $y);
				} else {
						$news_from = '';
				};
				if ($in->get('news_stop') != ""){
					  $date1 = @explode('.', $in->get('news_stop'));
						$d = $date1[0];
						$mo = $date1[1];
						$y  = $date1[2];
						$h = $in->get('news_stop_time_hour', 0);
						$m = $in->get('news_stop_time_min', 0);
						$news_to = $time->mktime($h, $m, 0, $mo, $d, $y);
				} else {
						$news_to = '';
				};
				

        {
            $query = $db->build_query('UPDATE', array(
                'news_headline'     => $in->get('news_headline'),
                'news_message'      => $in->get('news_message'),
                'extended_message'  => $in->get('news_message_ext', ''),
                'nocomments'        => $in->get('nocomments', 0),					
                'news_permissions'  => $in->get('news_permissions', 0),
                'showRaids_id'      => $raids_id,
								'news_flags'        => $in->get('news_flags', 0),
								'news_category'			=> $in->get('category', 1),
								'news_date'					=> $date,
								'news_start'				=> $news_from,
								'news_stop'					=> $news_to,
                )
            );
        }
        $db->query('UPDATE __news SET ' . $query . " WHERE news_id='" . $this->url_id . "'");

        //
        // Logging
        //
        $log_action = array(
            'header'              => '{L_ACTION_NEWS_UPDATED}',
            'ID'                  => $this->url_id,
            '{L_HEADLINE_BEFORE}' => $this->old_news['news_headline'],
            '{L_MESSAGE_BEFORE}'  => nl2br($this->old_news['news_message'].'<br />'.$this->old_news['extended_message']),
            '{L_HEADLINE_AFTER}'  => $this->find_difference($this->old_news['news_headline'], $in->get('news_headline')),
            '{L_MESSAGE_AFTER}'   => nl2br($in->get('news_message').'<br />'.$in->get('news_message_ext', '')),
            '{L_UPDATED_BY}'      => $this->admin_user);
				
				$logs->add( $log_action['header'], $log_action);
        
				if ($in->get('ref') == "fe"){
					$tpl->add_js("parent.window.location.href = '../viewnews.php';");
				} else {
					$tpl->add_js("parent.window.location.href = 'manage_news.php';");
				}
				$this->display_form();
    }

    // ---------------------------------------------------------
    // Process Delete (confirmed)
    // ---------------------------------------------------------
    function process_delete()
    {
        global $db, $core, $user, $tpl, $pm, $in;
        global $SID, $logs;

        //
        // Get the old data
        //
        $this->get_old_data();

        //
        // Remove the news entry
        //
        $sql = "DELETE FROM __news WHERE news_id='" . $this->url_id . "'";
        $db->query($sql);

        //
        // Logging
        //
        $log_action = array(
            'header'           => '{L_ACTION_NEWS_DELETED}',
            'id'               => $this->url_id,
            '{L_HEADLINE}'     => $this->old_news['news_headline'],
            '{L_MESSAGE_BODY}' => nl2br($this->old_news['news_message']));
        $logs->add( $log_action['header'], $log_action);

        //
        // Success message
        //
        if ($in->get('ref') == "fe"){
					$tpl->add_js("parent.window.location.href = '../viewnews.php';");
				} else {
					$tpl->add_js("parent.window.location.href = 'manage_news.php';");
				}
				$this->display_form();
    }

    // ---------------------------------------------------------
    // Process helper methods
    // ---------------------------------------------------------
    function get_old_data()
    {
        global $db;

        $sql = "SELECT news_headline, news_message, showRaids_id, extended_message, news_permissions, nocomments, news_flags 
                FROM __news
                WHERE news_id='" . $db->escape($this->url_id) . "'";
        $result = $db->query($sql);
        while ( $row = $db->fetch_record($result) )
        {
            $this->old_news = array(
                'news_headline' 		=> $row['news_headline'],
                'news_message'  		=> $row['news_message'],
                'extended_message'  => $row['extended_message'],
                'nocomments'  			=> $row['nocomments'],
                'news_permissions'  => $row['news_permissions'],
                'news_showRaids_id' => $row['showRaids_id'],
                'news_flags'        => $row['news_flags'],
								'category'					=> $row['news_category'],
								
            );
        }
        $db->free_result($result);
    }

    // ---------------------------------------------------------
    // Display form
    // ---------------------------------------------------------
    function display_form()
    {
        global $db, $core, $user, $tpl, $pm, $jquery, $html;
        global $SID, $in, $pdh, $time;

		##########################################################
     //
		// Build raid drop-down
		//
		// Show all raids?
		
		$show_all = ( $in->get('show') == 'all') ? true : false;
		$show_all = ($this->news['news_showRaids_id'] != "") ? true : $show_all;
		
		if (!$show_all){
			$two_weeks = $time->mktime(0, 0, 0, $time->date('m', $this->time), $time->date('d', $this->time)-90, $time->date('y', $this->time));
			$raidlist = $pdh->get('raid', 'raididsindateinterval', array($two_weeks, $time->time));
		} else {
			$raidlist = $pdh->get('raid','id_list', array());
		}
		$raidlist = array_reverse($raidlist);
		$raid_ids = explode(",",$this->news['news_showRaids_id']);
		
		foreach ( $raidlist as $key=>$row)
		{
			$raids[$row] = $time->date($user->style['date_notime_short'], $pdh->get('raid', 'date', array($row))) . ' - ' . stripslashes($pdh->get('raid', 'event_name', array($row)));
			if (@in_array($row,$raid_ids)){
				$raid_selected[] = $row;
			}


		}

        ##########################################################
		
		// Build category drop-down
		$result = $db->query("SELECT category_id, category_name FROM __news_categories");
		while ( $row = $db->fetch_record($result) )
		{
			$categories[$row['category_id']] = $row['category_name'];
		}
        $db->free_result($result);
        
				
				$permission_dropdown = array(
					0	=>  $user->lang['news_permissions_all'],
					1	=>  $user->lang['news_permissions_guest'],
					2	=>  $user->lang['news_permissions_member'],
					
				);

				$jquery->Dialog('delete_warning', '', array('url'=>"addnews.php?n=".$this->url_id."&delete=true&ref=".$in->get('ref'), 'message'=>$user->lang['confirm_delete_news']), 'confirm');
				$jquery->Collapse('extended');
				$jquery->Collapse('message');
				
				//Do Hooks - BBcode-Notes
				if (is_array($pm->hooks['addnews_bbcodeinfo'])){
					foreach ($pm->hooks['addnews_bbcodeinfo'] as $plugin_code => $function){
						$notes = $pm->do_hook('addnews_bbcodeinfo', $plugin_code, array());
						if (is_array($notes)){
							foreach ($notes as $note){
								$tpl->assign_block_vars('bbcode_notes', array(
									'ROW_CLASS'	=> $core->switch_row_class(),
									'STARTTAG'	=> $note['starttag'],
									'ENDTAG'		=> $note['endtag'],
									'TAG'				=> $note['tag'],
									'NOTE'			=> $note['note'],
								));
							}
						}
						
					}
				}

        $tpl->assign_vars(array(
            // Form vars
            'F_ADD_NEWS' 		=> 'addnews.php' . $SID,
            'NEWS_ID'    		=> $this->url_id,
            'S_UPDATE'   		=> ( $this->url_id ) ? true : false,
						'REF'						=> $in->get('ref'),
						'RAID_DROPDOWN'	=> $jquery->MultiSelect('raid_id', $raids, $raid_selected, 200, 350),

            // Form values
            'HEADLINE' 			=> sanitize($this->news['news_headline']),
            'MESSAGE'  			=> sanitize($this->news['news_message']),
						'EXT_MESSAGE'		=> sanitize($this->news['extended_message']),

            'NOCOMMENTS_CHECKED'  => ($this->news['nocomments'] == 1) ? 'checked' : '' ,
						'PERMISSION_DROPDOWN'	=> $html->DropDown('news_permissions', $permission_dropdown, $this->news['news_permissions'] ),
						'JS_TABS'							=> $jquery->Tab_header('news_tabs'),
            'STICKY_CHECKED' 			=> ($this->news['news_flags'] == 1) ? 'checked' : '',
						'DATE_PICKER'					=> $jquery->Calendar('news_date', (($this->news['news_date']) ? $time->date('d.m.Y', $this->news['news_date']) :  $time->date('d.m.Y', $time->time))),				
						'DATE_TO_PICKER'			=> $jquery->Calendar('news_stop', (($this->news['news_stop']) ? $time->date('d.m.Y', $this->news['news_stop']) : '')),
						'DATE_FROM_PICKER'		=> $jquery->Calendar('news_start', (($this->news['news_start']) ? $time->date('d.m.Y', $this->news['news_start']) : '')),
						
						'DATE_TIME'						=> $jquery->timePicker('news_date_time', 
																			(($this->news['news_date']) ? $time->date('G', $this->news['news_date']) : $time->date('G', $time->time)), 
																			((($this->news['news_date']) ? ceil($time->date('i', $this->news['news_date']) / 5) * 5 : ceil($time->date('i', $time->time) / 5) * 5 ))
																		),					
						'DATE_TO_TIME'	=> $jquery->timePicker('news_stop_time', 
																		(($this->news['news_stop']) ? $time->date('G', (int)$this->news['news_stop']) : 0), 
																		(($this->news['news_stop']) ? $time->date('i', (int)$this->news['news_stop']) : 0)
															),
						'DATE_FROM_TIME'	=> $jquery->timePicker('news_start_time', 
																		(($this->news['news_start']) ? $time->date('G', (int)$this->news['news_start']) : 0),
																		(($this->news['news_start']) ? $time->date('i', (int)$this->news['news_start']) : 0)
															),
						
            // Language (General)
						'L_EXTENDED'				=> $user->lang['message_extended'],
						'L_MESSAGE'					=> $user->lang['message_body'],
						'L_SHOW_ALL'				=> $user->lang['additem_raidid_showall_note'],
            'L_HEADLINE'       	=> $user->lang['headline'],
            'L_MESSAGE_BODY'   	=> $user->lang['message_body'],
            'L_MESSAGE_SHOW_LOOT_RAID' => $user->lang['message_show_loot_raid'],
						'L_SELECT_RAIDS' 		=> $user->lang['message_select_raids'],
            'L_ADD_NEWS'       	=> ($this->url_id) ? $user->lang['manage_news'] : $user->lang['add_news'],
            'L_RESET'          	=> $user->lang['reset'],
            'L_UPDATE_NEWS'    	=> $user->lang['update_news'],
            'L_DELETE_NEWS'    	=> $user->lang['delete_news'],
						'L_BBCODES'					=> $user->lang['bbcode'],
						'L_BBCODES_NOTE'		=> $user->lang['bbcode_note'],
						'L_EXPLANATION'			=> $user->lang['explanation'],
						'L_DATE'						=> $user->lang['news_date'],
						'L_UPDATE_DATE'			=> $user->lang['update_date_to'],
						'L_DATE_TO'					=> $user->lang['show_news_to'],
						'L_DATE_FROM'				=> $user->lang['show_news_from'],
						'L_DATE_TO_HELP'		=> $html->HelpTooltip($user->lang['show_news_to_help']),
						'L_DATE_FROM_HELP'	=> $html->HelpTooltip($user->lang['show_news_from_help']),
						'ACTUELL_DATE'			=> $time->date('d.m.Y', $time->time),
						'ACTUELL_HOUR'			=> $time->date('G', $time->time),
						'ACTUELL_MIN'				=> ceil($time->date('i', $time->time) / 5) * 5,
						
						'BBCODE_ITEM'				=> $user->lang['bbcode_item'],
						'BBCODE_ITEM_NOTE'	=> $user->lang['bbcode_item_note'],
						'BBCODE_VIDEO'			=> $user->lang['bbcode_video'],
						'BBCODE_VIDEO_NOTE'	=> $user->lang['bbcode_video_note'],						

             'L_NEWS_NOCOMMENTS'	=> $user->lang['news_nocomments'],
             'L_READMORE' 				=> $user->lang['news_readmore_button'],
             'L_READMORE_HLP' 		=> $user->lang['news_readmore_button_help'],
             'L_NEWS_MSG' 				=> $user->lang['news_message'],
             'L_PERMISSIONS' 			=> $user->lang['news_permissions'],
						 'L_SETTINGS'					=> $user->lang['settings'],
						 
			 			'L_NEWS_CATEGORY' 		=> $user->lang['select_newscategories'],
			 			'S_NEWS_CATEGORIES'		=> ($core->config['enable_newscategories'] == 1) ? true : false,
			 
            'NEWS_CATEGORY_DROPDOWN'	=> $html->DropDown('category', $categories, $this->news['news_category'], '', '', 'input'),
            'L_STICKY_NEWS' 			=> $user->lang['news_sticky'],
            // WYSIWYG Editors
            'WYSIWYG_SHORT' 			=> $jquery->wysiwyg('shortinput'),
            'WYSIWYG_EXT' 				=> $jquery->wysiwyg('news_message_ext'),
            // Language (Help messages)
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

        $core->set_vars(array(
            'page_title'    => $user->lang['addnews_title'],
            'template_file' => 'admin/addnews.html',
						'header_format'	=> ($in->get('ref') == 'fe') ? 'simple' : 'full',
    				'display'       => true,
			));
    }
}

$add_news = new Add_News;
$add_news->process();
?>
