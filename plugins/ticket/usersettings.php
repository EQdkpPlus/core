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
// EQdkp required files/vars
define('EQDKP_INC', true);
define('PLUGIN', 'ticket');
$eqdkp_root_path = './../../';

include_once('config.php');

// Check if plugin is installed
if (!$pm->check(PLUGIN_INSTALLED, 'ticket')) { message_die('The Ticket plugin is not installed.'); }

// Get the plugin
$ticket = $pm->get_plugin('ticket');


//---------
global $table_prefix;
global $db, $eqdkp, $user, $tpl, $pm;
global $SID;

$user_set = $db->query_first('SELECT count(*) FROM ' . TK_USER_CONFIG .' WHERE user_id = ' . $user->data['user_id']);

// Save this shit
if ($_POST['issavebu']){
  // global config
  
      if($user_set != 0){
        $query = $db->build_query('UPDATE', array(
               'email' => $_POST['email'],
               'color' => $_POST['color'],
               ));
    	$worked=$db->query('UPDATE ' . TK_USER_CONFIG . ' SET ' . $query . ' WHERE user_id =' . $user->data['user_id']);
    	//if(!$worked){
		//return array("info" => "set_undeletion_failed", "info_id" => $session_id);
    	//}
       } else {
        $query = $db->build_query('INSERT', array(
               'user_id' =>  $user->data['user_id'],
               'email' => $_POST['email'],
               'color' => $_POST['color'],
               ));
    	$worked=$db->query('INSERT INTO ' . TK_USER_CONFIG . $query);
    	//if(!$worked){
		//return array("info" => "set_undeletion_failed", "info_id" => $session_id);
    	//}
       }
      if ($lala){
    echo($user->lang['is_conf_saved']);
	}
} 

//----------------

    //get Conf
             $sql = 'SELECT * FROM ' . TK_CONFIG_TABLE;
             $settings_result = $db->query($sql);
             while($roww = $db->fetch_record($settings_result)) {
             $conf[$roww['config_name']] = $roww['config_value'];
             }

if($user_set != 0){
             $sql = 'SELECT * FROM ' . TK_USER_CONFIG . ' WHERE user_id =' .  $user->data['user_id'];
             if (!($settings_result = $db->query($sql))) { message_die('Could not obtain configuration data: Config-Table', '', __FILE__, __LINE__, $sql); }
             while($roww = $db->fetch_record($settings_result)) {
                 $crow['email']=$roww['email'];
                 $crow['color']=$roww['color'];
             }
} else {
    $crow['email']=1;
    $crow['color']=$conf['ticket_default_user_color'];
}


//------------------------
//
// Populate the fields
//
$tpl->assign_vars(array(
            // Form vars
           'F_CONFIG'        => 'usersettings.php' . $SID,
           'EMAIL' => ( $crow['email'] == 1 ) ? ' checked="checked"' : '',
           'COLOR' => $crow['color'],

            // Form values
            'ROW_CLASS' => $eqdkp->switch_row_class(),

            // Language

            'L_SUBMIT'        => $user->lang['submit'],
            'L_RESET'         => $user->lang['reset'],
      
            'L_EMAIL' => $user->lang['ticket_email'],
            'L_EMAIL_NOTE' => $user->lang['ticket_email_note'],
            'L_COLOR' => $user->lang['ticket_color'],
      
            'L_GENERAL'       => $user->lang['ticket_settings_header'],
      
      )
		);
    
    $eqdkp->set_vars(array(
		'page_title'             => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': Settings',
		'template_path' 	       => $pm->get_data('ticket', 'template_path'),
		'template_file'          => 'settings.html',
		'display'                => true)
    );
    

?>
