<?php
/******************************
 * EQdkp
 * Copyright 2002-2005
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * viewraid.php
 * Began: Thu December 19 2002
 *
 * $Id$
 *
 ******************************/

define('EQDKP_INC', true);
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');
infotooltip_js();

$user->check_auth('u_raid_view');

if ( $raid_id = $in->get('r', 0) ){
    if(!in_array($raid_id, $pdh->get('raid', 'id_list')))
      message_die($user->lang['error_invalid_raid_provided']);

    //
    // Attendees
    //
		$attendees_ids = $pdh->get('raid', 'raid_attendees', array($raid_id));
    foreach($attendees_ids as $attendee_id){
      $attendees[$attendee_id] = addslashes($pdh->get('member', 'name', array($attendee_id)));
    }
    $attendee_copy = $attendees;

    // Get each attendee's rank
    foreach($attendees as $attendee_id => $attendee_name){
        $ranks[ $attendee_name ] = array(
            'prefix' => $pdh->get('rank', 'prefix', array($pdh->get('member', 'rankid', array($attendee_id)))),
            'suffix' => $pdh->get('rank', 'suffix', array($pdh->get('member', 'rankid', array($attendee_id)))),
        );
    }

    if ( @sizeof($attendees) > 0 ){
        // First get rid of duplicates and resort them just in case,
        // so we're sure they're displayed correctly
        $attendees = array_unique($attendees);
        sort($attendees);
        reset($attendees);
        $rows = ceil(sizeof($attendees) / $user->style['attendees_columns']);

        // First loop: iterate through the rows
        // Second loop: iterate through the columns as defined in template_config,
        // then "add" an array to $block_vars that contains the column definitions,
        // then assign the block vars.
        // Prevents one column from being assigned and the rest of the columns for
        // that row being blank
        for ( $i = 0; $i < $rows; $i++ )
        {
            $block_vars = array();
            for ( $j = 0; $j < $user->style['attendees_columns']; $j++ )
            {
                $offset = ($i + ($rows * $j));
                $attendee = ( isset($attendees_ids[$offset]) ) ? $attendees_ids[$offset] : '';

                if ( $attendee != '' )
                {
                    $block_vars += array(
                        'COLUMN'.$j.'_NAME' => $pdh->get('member', 'html_memberlink', array($attendee, 'viewcharacter.php'))
                    );

                }
                else
                {
                    $block_vars += array(
                        'COLUMN'.$j.'_NAME' => ''
                    );
                }

                // Are we showing this column?
                $s_column = 's_column'.$j;
                ${$s_column} = true;
            }
            $tpl->assign_block_vars('attendees_row', $block_vars);
        }
        $column_width = floor(100 / $user->style['attendees_columns']);
    }else{
        message_die('Could not get raid attendee information.','Critical Error');
    }

    //
    // Drops
    //
    $loot_dist = array();
    $items = $pdh->get('item', 'itemsofraid', array($raid_id));
    foreach($items as $item_id){
      $buyer_id = $pdh->get('item', 'buyer', array($item_id));

      $class_name = $pdh->get('member', 'classname', array($buyer_id));
  		$class_id = $pdh->get('member', 'classid', array($buyer_id));

      if(isset($loot_dist[$class_id])){
        $loot_dist[$class_id]['value']++;
      }else{
        $loot_dist[$class_id] = array('value' => 1, 'name' => $class_name, 'color' => $game->get_class_color($class_id) );
      } 

        $tpl->assign_block_vars('items_row', array(
            'ROW_CLASS'    => $core->switch_row_class(),
            'BUYER'        => $pdh->get('member', 'html_memberlink', array($buyer_id, 'viewcharacter.php')),
            'ITEM'         => $pdh->get('item', 'link_itt', array($item_id, 'viewitem.php')),
            'VALUE'        => runden($pdh->get('item', 'value', array($item_id))))
        );
    }

    ksort($loot_dist);

    //
    // Class distribution
    //
    $class_dist = array();
    $total_attendee_count = sizeof($attendee_copy);
    foreach($attendee_copy as $member_id => $member_name){
  		$member_class = $pdh->get('member', 'classname', array($member_id));
  		$member_class_id = $pdh->get('member', 'classid', array($member_id));

      if ( $member_name != '' ){
          $html_prefix = ( isset($ranks[$member_name]) ) ? $ranks[$member_name]['prefix'] : '';
          $html_suffix = ( isset($ranks[$member_name]) ) ? $ranks[$member_name]['suffix'] : '';

          $class_dist[$member_class_id]['names'] .= " " . $html_prefix . $member_name . $html_suffix .",";
    	    $class_dist[$member_class_id]['count']++;
      }
    }

    unset($ranks);

    #Class distribution
    $chartarray = array();
    foreach ( $class_dist as $class_id => $details ){
  		$percentage =  ( $total_attendee_count > 0 ) ? round(($details['count'] / $total_attendee_count) * 100) : 0;  
      $class = $game->get_name('classes', $class_id);
  		$chartarray[] = array('value' => $percentage, 'name' => $class." (".$class_dist[$class_id]['count']." - ".$percentage."%)", 'color' => $game->get_class_color($class_id) );
  		
      $tpl->assign_block_vars('class_row', array(
          'CLASS'     => $game->decorate('classes', $class_id).' <span class="class_'.$class_id.'">'.$class.'</span>',
          'BAR'       => $jquery->ProgressBar('bar_'.$class, $percentage, $percentage.'%'),
          'ATTENDEES' => $class_dist[$class_id]['names']
        )
      );
    }

    $chartoptions['border'] = '0.0';
		$chartoptions['background'] = 'transparent';

    unset($eq_classes);

    // Comment System
    $comm_settings = array('attach_id'=>$raid_id, 'page'=>'raids');
  	$pcomments->SetVars($comm_settings);
  	$COMMENT = ($core->config['pk_enable_comments'] == 1) ? $pcomments->Show() : '';

    $vpre = $pdh->pre_process_preset('rvalue', array(), 0);
    $vpre[2][0] = $raid_id;
    
    $tpl->assign_vars(array(
        'L_MEMBERS_PRESENT_AT' => sprintf($user->lang['members_present_at'],
                                          $time->date($user->style['date_notime_long'],
                                               $pdh->get('raid', 'date', array($raid_id))),
                                          $time->date($user->style['time'],
                                               $pdh->get('raid', 'date', array($raid_id))
                                          )),
        'L_ADDED_BY'           => $user->lang['added_by'],
        'L_UPDATED_BY'         => $user->lang['updated_by'],
        'L_NOTE'               => $user->lang['note'],
        'L_VALUE'              => $user->lang['value'],
        'L_DROPS'              => $user->lang['drops'],
        'L_BUYER'              => $user->lang['buyer'],
        'L_ITEM'               => $user->lang['item'],
        'L_SPENT'              => $user->lang['spent'],
        'L_ATTENDEES'          => $user->lang['attendees'],
        'L_CLASS_DISTRIBUTION' => $user->lang['class_distribution'],
        'L_LOOT_DISTRIBUTION'  => $user->lang['loot_distribution'],
        'L_CLASS'              => $user->lang['class'],
        'L_PERCENT'            => $user->lang['percent'],
        'L_RANK_DISTRIBUTION'  => $user->lang['rank_distribution'],
        'L_RANK'               => $user->lang['rank'],

        'EVENT_ICON'          => $game->decorate('events', array($pdh->get('event', 'icon', array($pdh->get('raid', 'event', array($raid_id)))), 40)),
        'EVENT_NAME'		      => stripslashes($pdh->get('raid', 'event_name', array($raid_id))),
        'COMMENT' 			      => $COMMENT ,

        'S_COLUMN0' => ( isset($s_column0) ) ? true : false,
        'S_COLUMN1' => ( isset($s_column1) ) ? true : false,
        'S_COLUMN2' => ( isset($s_column2) ) ? true : false,
        'S_COLUMN3' => ( isset($s_column3) ) ? true : false,
        'S_COLUMN4' => ( isset($s_column4) ) ? true : false,
        'S_COLUMN5' => ( isset($s_column5) ) ? true : false,
        'S_COLUMN6' => ( isset($s_column6) ) ? true : false,
        'S_COLUMN7' => ( isset($s_column7) ) ? true : false,
        'S_COLUMN8' => ( isset($s_column8) ) ? true : false,
        'S_COLUMN9' => ( isset($s_column9) ) ? true : false,

        'COLUMN_WIDTH' => ( isset($column_width) ) ? $column_width : 0,
        'COLSPAN'      => $user->style['attendees_columns'],

        'RAID_ADDED_BY'       => ( $pdh->get('raid', 'added_by', array($raid_id)) != '' ) ? stripslashes($pdh->get('raid', 'added_by', array($raid_id))) : 'N/A',
        'RAID_UPDATED_BY'     => ( $pdh->get('raid', 'updated_by', array($raid_id)) != '' ) ? stripslashes($pdh->get('raid', 'updated_by', array($raid_id))) : 'N/A',
        'RAID_NOTE'           => ( $pdh->get('raid', 'note', array($raid_id)) != '' ) ? stripslashes($pdh->get('raid', 'note', array($raid_id))) : '&nbsp;',
        'DKP_NAME'            => $core->config['dkp_name'],
        'RAID_VALUE'          => $pdh->geth($vpre[0], $vpre[1], $vpre[2]),//runden($pdh->get('raid', 'value', array($raid_id))),
        'ATTENDEES_FOOTCOUNT' => sprintf($user->lang['viewraid_attendees_footcount'], sizeof($attendees)),
        'ITEM_FOOTCOUNT'      => sprintf($user->lang['viewraid_drops_footcount'], sizeof($items)),
        'CLASS_PERCENT_CHART' => $jquery->PieChart('class_dist', $chartarray, '', $chartoptions, 2),
        'LOOT_PERCENT_CHART'  => $jquery->PieChart('loot_dist', $loot_dist, '', $chartoptions, 2),
        
    ));

    $core->set_vars(array(
        'page_title'    => $user->lang['viewraid_title'],
        'template_file' => 'viewraid.html',
        'display'       => true)
    );
}
else{
    message_die($user->lang['error_invalid_raid_provided']);
}
?>