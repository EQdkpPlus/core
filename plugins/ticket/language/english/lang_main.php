<?php
//::///////////////////////////////////////////////
//::
//:: EQDKP PLUGIN: Language File (German)
//:: © 2006 ACHAZ
//:: Contact:  Achaz (Achaz@lionforge.de)
//::
//::
//:://////////////////////////////////////////////
//::
//:: File: lang_main.php (language script)
//:: Created on: 16. Nov 2006
//:: Last Changed: 00. Nov 2006
//::
//:://////////////////////////////////////////////

#Main 
$lang['ticket'] = "Ticket";
$lang['ticket_open'] = "open tickets";
$lang['ticket_usersettings'] = "Settings";
$lang['ticket_adminsettings'] = "Administration Settings";
$lang['ticket_admin_converse'] = "Answer tickets";

#permissions
$lang['ticket_admin'] = "Administration";
$lang['ticket_submit'] = "Submit tickets";

#index.php und mehr als eine Datei
$lang['tk_message_body'] = "Messagebody";
$lang['tk_submit_ticket'] = "Submi ticket";
$lang['tk_reset'] = "Reset";
$lang['tk_update_ticket'] = "Update ticket";
$lang['tk_delete_ticket'] = "delte ticket";
$lang['tk_replyticket'] = "Answer submitting a new ticket";
$lang['ticket_settings_header'] = "Settings";
$lang['tk_delete'] = "Delete";
$lang['tk_read'] = "Read";
$lang['tk_date'] = "Date";
$lang['tk_submit_ticket'] = "Submit ticket";
$lang['tk_submit_replyticket'] = "Submit reply ticket";

#usersettings.php
$lang['ticket_email'] = "Email notification";
$lang['ticket_email_note'] = "Email notifications are only sent if the server is set to do so. Please check your email address in the general settings.";
$lang['ticket_color'] = "Color of unread answers";

#adminconverse.php
$lang['helptextdel'] = "Tickets shown here are deleted by an administrator. If a user deletes one also it is removed from the database. If a user answers to a deleted ticket the mark of deletion will be removed so that the ticket will show up as not deleted for the administrators."; //sowas wie <i> ist Userdeleted und wie deleten funktioniert
$lang['helptext'] = "Tickets shown in italics are deleted by the user. If you delete them they are permanently removed from the databse. If you answer to such a ticket the mark of deletion will be removed and the user can see the ticket and the answers again";
$lang['showdeleted'] = "Show deleted tickets";
$lang['hidedeleted'] = "Show tickets";
$lang['tk_fv_required_message'] = "Error - check the ticket text";
$lang['tk_replytoticket'] = "Answer to a ticket";
$lang['tk_from_user'] = "From user";
$lang['tk_from_admin'] = "From admin";
$lang['tk_submit_st_reply'] = "Send message to user";
$lang['tk_submit_st_reply_button'] = "Submit";
$lang['tk_to_user'] = "To user";
$lang['admin-sends-message'] = "This is an automatically generated ticket. An admin has a message for you, which you can find in the reply to this ticket.";
$lang['tk_usernameerror'] = "Username unknown";
$lang['tk_submit'] = "Submit";
$lang['tk_replyheader'] = "Answer Tickets or Send Message to User";
$lang['tk_submit_reply'] = "Submit Reply";
$lang['tk_undelete'] = "Undelete ticket";

#adminSettings
$lang['edit_admin_emails'] = "Edit the email addresses of the admins";
$lang['submit_edited_emails'] = "Submit";
//$lang['reset'] = "Zurücksetzen";
$lang['ticket_email_general'] = "Use email notifications";
$lang['ticket_email_general_note'] = "This is the general setting for all notifications";
$lang['ticket_email_admin']= "Use email notification for admins";
$lang['ticket_email_admincolor'] = "Color setting for unreplied tickets (admin)";
$lang['ticket_default_user_color'] = "Standard color setting for unread answers to tickets";

#HTML

// Help lines
$lang['b_help'] = 'bold: [b]text[/b] (alt+b)';
$lang['i_help'] = 'italic: [i]text[/i] (alt+i)';
$lang['u_help'] = 'underlined: [u]text[/u] (alt+u)';
$lang['q_help'] = 'quote: [quote]text[/quote] (alt+q)';
$lang['c_help'] = 'centered: [center]text[/center] (alt+c)';
$lang['p_help'] = 'image: [img]http://image_url[/img] (alt+p)';
$lang['w_help'] = 'URL: [url]http://url[/url] or [url=http://url]URL text[/url]  (alt+w)';

?>
