<?php
/******************************
 * EQdkp Ticket System
 * Copyright 2006 by Achaz
 * ------------------
 * index.php
 * Began: 16 Nov, 2006
 * Changed: 29 Dez, 2006
 * 
 ******************************/
define('EQDKP_INC', true);
define('PLUGIN', 'ticket');
$eqdkp_root_path = './../../';
include_once('config.php');

$user->check_auth('u_ticket_submit');

$ticket = $pm->get_plugin(PLUGIN);

if (!$pm->check(PLUGIN_INSTALLED, 'ticket')) { message_die('The Ticket plugin is not installed.'); }
$ticketplug = $pm->get_plugin('ticket');

global $table_prefix;
global $db, $eqdkp, $user, $tpl, $pm;
global $SID;

if(isset($_POST['new_ticket_message'])) {$info = submit_ticket($_POST['new_ticket_message'],0); }
if(isset($_POST['session_id'])) {$info = submit_ticket($_POST['replyticket_message'],$_POST['session_id']); }
if(isset($_GET['delete'])) {$info = delete_ticket($_GET['delete']); }
if(isset($_POST['reply_id'])) {$info = mark_as_read($_POST['reply_id']); }

$sql = 'SELECT * FROM ' . TK_CONFIG_TABLE;
	if (!($settings_result = $db->query($sql))) { message_die('Could not obtain configuration data', '', __FILE__, __LINE__, $sql); }
	while($roww = $db->fetch_record($settings_result)) {
  		$conf[$roww['config_name']] = $roww['config_value'];
	}
 
//setting default color
$color=$conf['ticket_default_user_color'];
//Find user-specified color
            $user_set = $db->query_first('SELECT count(*) FROM ' . $table_prefix.'ticket_userconfig WHERE user_id = ' . $user->data['user_id']);
            if($user_set != 0){
                $sql = 'SELECT color FROM ' . TK_USER_CONFIG . ' WHERE user_id =' .  $user->data['user_id'];
                if (!($result= $db->query($sql))) { $color=$conf['ticket_default_user_color'];}
                else {
                    $usercolorarray = $db->fetch_record($result);
                    $color = $usercolorarray['color'];
                }
            }

function submit_ticket($message,$session_id)
{
	global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;
	
	$eqdkp_root_path = './../../';
	
    $message = nl2br($message);
    $message = news_parse($message);

    $query = $db->build_query('INSERT', array(
       	'user_id'	=> $user->data['user_id'],
        'message_date'	=> time(),
     	'message'	=> $message,
	'session_id'	=> $session_id,
		));

    $worked=$db->query('INSERT INTO ' . TK_TICKETS_TABLE . $query);
		if(!$worked){
			return array("info" => "ticket_failed", "info_id" => $user->data['user_id']);
		}
		
//set undeletion data - if a replyticket is submitted after an admin or a user deleted the ticket session, reset deletion to 0
    if($session_id > 0)
    {
	$query2 = $db->build_query('UPDATE', array('deletion' => '0'));
    	$worked2=$db->query('UPDATE ' . TK_TICKETS_TABLE . ' SET ' . $query2 . ' WHERE ticket_id =' . $session_id);
    	if(!$worked2){
		return array("info" => "set_undeletion_failed", "info_id" => $session_id);
    	}
    }	
	
    //get Conf
	$sql = 'SELECT * FROM ' . TK_CONFIG_TABLE;
	if (!($settings_result = $db->query($sql))) { message_die('Could not obtain configuration data', '', __FILE__, __LINE__, $sql); }
	while($roww = $db->fetch_record($settings_result)) {
  		$conf[$roww['config_name']] = $roww['config_value'];
	}
	
    if($conf['ticket_email'] == 1)
    {
    	$sql_email = 'SELECT * FROM ' . TK_ADMINEMAIL_TABLE;
	if (!($email_result = $db->query($sql_email))) { message_die('Could not obtain email data', '', __FILE__, __LINE__, $sql); }
	
      while($email_row = $db->fetch_record($email_result))
      {
      	$admin_email = $email_row['email_address'];
	
	include_once($eqdkp->root_path . 'includes/class_email.php');
        $email = new EMail;

        $headers = "From: " . $eqdkp->config['admin_email'] . "\nReturn-Path: " . $eqdkp->config['admin_email'] . "\r\n";

	//get Template-msg
	$lang = $eqdkp->config['default_lang'];
	$email->tpl_file = $eqdkp->root_path.'plugins/ticket/language/'.$lang.'/email/admin_ticket_notification.txt';//Build
	
	$fd = fopen($email->tpl_file, 'r');
	$email->msg .= fread($fd, filesize($email->tpl_file));
	fclose($fd);
	//echo($email->msg);
        
	$email->address(stripslashes($admin_email)); //get emailaddresses
	//echo($email->address);
        
	$email->subject(); // Grabbed from the template itself
	//echo($email->subject);
       
	$email->extra_headers($headers);
	//echo($email->extra_headers);

        $email->assign_vars(array( 
		'GUILDTAG'   => $eqdkp->config['guildtag'],
		'DKP_NAME'   => $eqdkp->config['dkp_name']
  		));
        $email->send();
	
	/*echo($email->address);
	echo($email->extra_headers);
	echo($email->subject);	
	echo($email->msg);*/

        $email->reset();
      }
    }
    	
    return array("info" => "ticket_sent", "info_id" => $user_id);
}


//--------------DISPLAY TICKETS AND REPLYS BY USER-------------------------
//--function for the user concerning his tickets

//Deletes ticket with ticket_id and all replys to this ticket and submits action to -->log<--!
function delete_ticket($ticket_id)
{
    	global $db,$user;
	
	$sql_deletion = 'SELECT deletion FROM ' . TK_TICKETS_TABLE .' WHERE ticket_id=' . $ticket_id .'';
	if (!($deletion_result = $db->query($sql_deletion))) { message_die('Could not obtain deletion information', '', __FILE__, __LINE__, $sql); }
	$deletioncounter = $db->fetch_record($deletion_result);

	$delnumber=$deletioncounter['deletion'];

    if($delnumber == 2) //that means admin already registered for deletion
    {
      $sql_ticket="DELETE FROM ". TK_TICKETS_TABLE . " WHERE ticket_id=" . $ticket_id;
	  if (!($ticket_result = $db->query($sql_ticket))) {
		message_die('Could not delete ticket', '', __FILE__, __LINE__, $sql); 
	  } else {
		$sql_reply="DELETE FROM ". TK_REPLIES_TABLE . " WHERE ticket_id=" . $ticket_id;
		if (!($reply_result = $db->query($sql_reply))) { 
			message_die('Could not delete replies', '', __FILE__, __LINE__, $sql); 
		}
        //dont forget replytickets and the answers to reply tickets:
        $sql_get_tickets="SELECT ticket_id FROM ". TK_TICKETS_TABLE . " WHERE session_id=" . $ticket_id;
        if (!($get_tickets_result = $db->query($sql_get_tickets))) {
    		message_die('Could not get ticket ids for marking ', '', __FILE__, __LINE__, $sql);
	    }
        while ( $tickets_id_row = $db->fetch_record($get_tickets_result) )
        {
            $sql_del_reply="DELETE FROM ". TK_REPLIES_TABLE . " WHERE ticket_id=" . $tickets_id_row['ticket_id'];
            if (!($reply_del_result = $db->query($sql_del_reply))) {
			message_die('Could not delete replies to replytickets', '', __FILE__, __LINE__, $sql);
		    }
            $sql_del_replyticket="DELETE FROM ". TK_TICKETS_TABLE . " WHERE ticket_id=" . $tickets_id_row['ticket_id'];
            if (!($replyticket_del_result = $db->query($sql_del_replyticket))) {
			message_die('Could not delete replytickets', '', __FILE__, __LINE__, $sql);
		    }
        }
        //---------
		return array("info" => "delete success", "info_id" => $ticket_id);
	  }
    } else {
	//register for deletion instead
       	$sql_ticket="UPDATE ". TK_TICKETS_TABLE . " SET deletion='1' WHERE ticket_id=" . $ticket_id;
        if (!($ticket_result = $db->query($sql_ticket))) {
    		message_die('Could not set ticket for deletion ', '', __FILE__, __LINE__, $sql);
	    }
        //set answers to deleted deleted tickets to read
		$sql_reply="UPDATE ". TK_REPLIES_TABLE . " SET userviewed='1' WHERE ticket_id=" . $ticket_id;
		if (!($reply_result = $db->query($sql_reply))) { 
			message_die('Could mark replies as read', '', __FILE__, __LINE__, $sql); 
		}
                       //dont forget the answers to reply tickets:
        $sql_get_tickets="SELECT ticket_id FROM ". TK_TICKETS_TABLE . " WHERE session_id=" . $ticket_id;
        if (!($get_tickets_result = $db->query($sql_get_tickets))) {
    		message_die('Could not get ticket ids for marking ', '', __FILE__, __LINE__, $sql);
	    }
        while ( $tickets_id_row = $db->fetch_record($get_tickets_result) )
        {
            $sql_reply2="UPDATE ". TK_REPLIES_TABLE . " SET userviewed='1' WHERE ticket_id=" . $tickets_id_row['ticket_id'];
            if (!($reply_result2 = $db->query($sql_reply2))) {
			message_die('Could not mark replies as read', '', __FILE__, __LINE__, $sql);
		    }
        }
                     //---------
	    return array("info" => "Ticket marked for deletion", "info_id" => $ticket_id);
   }
}

//marks the replys to the ticket with ticket_id as read (no longer shown bold - admin knows when read)
function mark_as_read($reply_id)
{
	global $db, $eqdkp, $user, $tpl, $pm;

	$sql = 'SELECT userviewed FROM ' . TK_REPLIES_TABLE .' WHERE reply_id=' . $reply_id .'';
	if (!($mark_result = $db->query($sql))) { message_die('Could not obtain mark information', '', __FILE__, __LINE__, $sql); }
	$marked = $db->fetch_record($mark_result);

	$newmark=($marked['userviewed']+1)%2;
	
    	$worked=$db->query('UPDATE ' . TK_REPLIES_TABLE . " SET userviewed ='".$newmark."' WHERE reply_id =" . $reply_id);
	if(!$worked){
		return array("info" => "mark_failed", "info_id" => $reply_id);
	}
	return array("info" => "mark_set", "info_id" => $reply_id);
}
//--

$tickets_total = $db->query_first('SELECT count(*) FROM ' . TK_TICKETS_TABLE . ' WHERE user_id = ' . $user->data['user_id']);

if($tickets_total >0)
{
    $ticket_sql = 'SELECT ticket_id, session_id, message_date, message, replied, deletion
        FROM ' . TK_TICKETS_TABLE . '
		WHERE user_id = ' .$user->data['user_id']. '
        ORDER BY message_date DESC';
        
    if (!($ticket_result = $db->query($ticket_sql))) { message_die('Could not obtain ticket information', '', __FILE__, __LINE__, $trade_sql); }
    
    while ( $ticket_row = $db->fetch_record($ticket_result) )
    {
      if($ticket_row['deletion']!=1 && $ticket_row['session_id']==0)
      {
        $tpl->assign_block_vars('ticket_row', array(
        'MESSAGE_DATE' => date($user->style['date_time'],$ticket_row['message_date']),
        'MESSAGE_BODY' => $ticket_row['message'],                                            //bbcode scannen?
        'DELETE' => 'index.php' . $SID .'&delete=' . $ticket_row['ticket_id'],
	    'TICKET_ID' => $ticket_row['ticket_id'],
        'S_REPLIES' => ($ticket_row['replied'] != 0) ? true : false ,
	    'F_SUBMIT_REPLYTICKET' => 'index.php' . $SID,
	    'S_REPLYTICKET' => ($_GET['replyticket'] == $ticket_row['ticket_id']) ? true : false,
	    'REPLYTICKET' => 'index.php' . $SID . '&replyticket=' . $ticket_row['ticket_id'],
        ));
    
    	if($ticket_row['replied'] != 0)
    	{
        	$replies_sql = 'SELECT reply_id, replied_by_id, reply_date, ticket_id, ticketsuser_id, message, userviewed
                     FROM ' . TK_REPLIES_TABLE . '
                     WHERE ticket_id = ' .$ticket_row['ticket_id']. '
                     AND ticketsuser_id = ' . $user->data['user_id'] . '
                     ORDER BY reply_date ASC';

        	if (!($reply_result = $db->query($replies_sql))) { message_die('Could not obtain reply information', '', __FILE__, __LINE__, $trade_sql); }

        	while ( $reply_row = $db->fetch_record($reply_result) )
        	{
			$sql_adminname='SELECT username FROM ' . USERS_TABLE . ' WHERE user_id='.$reply_row['replied_by_id'];
			if (!($adminname_result = $db->query($sql_adminname))) { message_die('Could not obtain adminname', '', __FILE__, __LINE__, $sql_adminname); }
			$adminname = $db->fetch_record($adminname_result);
			
              		$tpl->assign_block_vars('ticket_row.reply_row', array(
           			'ROW_CLASS' => $eqdkp->switch_row_class(),
              		//'FROM_ADMIN' => $reply_row['replied_by_id'],        //todo get admin user name
                    'FROM_ADMIN' => ($reply_row['userviewed'] != 0) ? $adminname['username']:'<span style="color:'.$color.'"><b>'.$adminname['username'].'</b></span>',
              		'REPLY_DATE' => ($reply_row['userviewed'] != 0) ? date($user->style['date_time'],$reply_row['reply_date']) : '<span style="color:'.$color.'"><b>'. date($user->style['date_time'],$reply_row['reply_date']).'</b></span>',
              		'REPLY_BODY' => $reply_row['message'],                
              		//-------
              		'REPLY_ID' => $reply_row['reply_id'],
              		'READ' => ($reply_row['userviewed'] != 0) ? 'checked' : '',
              		'F_MARKREAD' => 'index.php' . $SID,
              		'S_NOTREAD' => ($reply_row['userviewed'] != 0) ? false : true , //sets date and admin name in bold
              		));
        	}
    	}
	
	//CHECK FOR REPLYTICKETS
	$replytickets_total = $db->query_first('SELECT count(*) FROM ' . TK_TICKETS_TABLE . ' WHERE user_id = ' . $user->data['user_id'] .' AND session_id=' . $ticket_row['ticket_id']);
	
	if($replytickets_total > 0)
	{
	
	$replyticket_sql = 'SELECT ticket_id, session_id, message_date, message, replied, deletion 
        	FROM ' . TK_TICKETS_TABLE . ' 
		    WHERE user_id = ' .$user->data['user_id']. '
		    AND session_id = ' .$ticket_row['ticket_id'].'
        	ORDER BY message_date ASC';

    	if (!($replyticket_result = $db->query($replyticket_sql))) { message_die('Could not obtain replyticket information', '', __FILE__, __LINE__, $trade_sql); }
	
	while ( $replyticket_row = $db->fetch_record($replyticket_result) )
	{
		$tpl->assign_block_vars('ticket_row.replyticket_row', array(
		'ROW_CLASS' => $eqdkp->switch_row_class(),
        	'MESSAGE_DATE' => date($user->style['date_time'],$replyticket_row['message_date']),
        	'MESSAGE_BODY' => $replyticket_row['message'],                                            //bbcode scannen?
        	//'DELETE' => 'index.php' . $SID .'&delete=' . $ticket_row['ticket_id'],
		    'TICKET_ID' => $replyticket_row['ticket_id'],
        	'S_REPLIES' => ($replyticket_row['replied'] != 0) ? true : false ,
		    //'F_SUBMIT_REPLYTICKET' => 'index.php' . $SID,
		    //'S_REPLYTICKET' => ($_GET['replyticket'] == $ticket_row['ticket_id']) ? true : false,
	        //'REPLYTICKET' => 'index.php' . $SID . '&replyticket=' . $ticket_row['ticket_id'],
        	));
    
    		if($replyticket_row['replied'] != 0)
		{
        		$replies_sql = 'SELECT reply_id, replied_by_id, reply_date, ticket_id, ticketsuser_id, message, userviewed
                     		FROM ' . TK_REPLIES_TABLE . '
		                    WHERE ticket_id = ' .$replyticket_row['ticket_id']. '
                     		AND ticketsuser_id = ' . $user->data['user_id'] . '
                     		ORDER BY reply_date ASC';

        		if (!($reply_result = $db->query($replies_sql))) { message_die('Could not obtain reply information', '', __FILE__, __LINE__, $trade_sql); }

        	     while ( $reply_row = $db->fetch_record($reply_result) )
		     {
			$sql_adminname='SELECT username FROM ' . USERS_TABLE . ' WHERE user_id='.$reply_row['replied_by_id'];
			if (!($adminname_result = $db->query($sql_adminname))) { message_die('Could not obtain adminname', '', __FILE__, __LINE__, $sql_adminname); }
			$adminname = $db->fetch_record($adminname_result);
			
              		$tpl->assign_block_vars('ticket_row.replyticket_row.reply_row', array(
                    'ROW_CLASS' => $eqdkp->switch_row_class(),
              		//'FROM_ADMIN' => $reply_row['replied_by_id'],        //todo get admin user name
                    'FROM_ADMIN' => ($reply_row['userviewed'] != 0) ? $adminname['username']:'<span style="color:'.$color.'"><b>'.$adminname['username'].'</b></span>',
              		'REPLY_DATE' => ($reply_row['userviewed'] != 0) ? date($user->style['date_time'],$reply_row['reply_date']) : '<span style="color:'.$color.'"><b>'. date($user->style['date_time'],$reply_row['reply_date']).'</b></span>',
			        //'FROM_ADMIN' => $adminname['username'],
              		//'REPLY_DATE' => date($user->style['date_time'],$reply_row['reply_date']),
              		'REPLY_BODY' => $reply_row['message'],                
              		//-------
              		'REPLY_ID' => $reply_row['reply_id'],
              		'READ' => ($reply_row['userviewed'] != 0) ? 'checked' : '',
              		'F_MARKREAD' => 'index.php' . $SID,
              		'S_NOTREAD' => ($reply_row['userviewed'] != 0) ? false : true , //sets date and admin name in bold
              		));
		      }
		}
	}
	}
      }
    }
}

//--------------------------------------------------------------

        $tpl->assign_vars(array(
	         //ifs
	        'S_NOREPLYOPEN' => (!isset($_GET['replyticket']) ) ? true : false,
            'COLOR' => $color,
	
            // Form vars
            'F_SUBMIT_TICKET' => 'index.php' . $SID,
            //'USER_ID'    => $user->data['user_id'],

            // Form values
            //'MESSAGE'  => stripmultslashes($this->news['news_message']),

            // Language (General)
            'L_MESSAGE_BODY'   => $user->lang['tk_message_body'],
            'L_SUBMIT_TICKET'       => $user->lang['tk_submit_ticket'],
            'L_SUBMIT_REPLYTICKET'  => $user->lang['tk_submit_replyticket'],
            'L_RESET'          => $user->lang['tk_reset'],
            'L_UPDATE_TICKET'    => $user->lang['tk_update_ticket'],
            'L_DELETE_TICKET'    => $user->lang['tk_delete_ticket'],
	        'L_REPLYTICKET' => $user->lang['tk_replyticket'],
            'L_DELETE' => $user->lang['tk_delete'],
            'L_READ' => $user->lang['tk_read'],
            'L_MESSAGE_DATE' => $user->lang['tk_date'],
            'L_SUBMIT_TICKET' => $user->lang['tk_submit_ticket'],
            'L_FROM_ADMIN' => $user->lang['tk_from_admin'],
            'L_REPLY_DATE' => $user->lang['tk_date'],
            'L_REPLY_BODY'   => $user->lang['tk_message_body'],

            // Language (Help messages)
            'L_B_HELP' => $user->lang['b_help'],
            'L_I_HELP' => $user->lang['i_help'],
            'L_U_HELP' => $user->lang['u_help'],
            'L_Q_HELP' => $user->lang['q_help'],
            'L_C_HELP' => $user->lang['c_help'],
            'L_P_HELP' => $user->lang['p_help'],
            'L_W_HELP' => $user->lang['w_help'],

            // Form validation
            //'FV_MESSAGE'  => $this->fv->generate_error('news_message'),

            // Javascript messages
            'MSG_MESSAGE_EMPTY'  => $user->lang['tk_fv_required_message'],

            // Buttons
            'S_SUBMIT' => ( !isset($_GET['ticket_id']) ) ? true : false)
        );

$eqdkp->set_vars(array(
            'page_title'    => sprintf($user->lang['title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['ticket'],
            'template_file' => 'submit.html',
	        'template_path' => $pm->get_data('ticket', 'template_path'),
            'display'       => true)
        );
?>
