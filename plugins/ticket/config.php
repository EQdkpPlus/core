<?php
/******************************
 * EQdkp Ticket System
 * Copyright 2006 by Achaz
 * ------------------
 * config.php
 * Began: 16 Nov, 2006
 * Changed: 16 Nov, 2006
 * 
 ******************************/
//global $eqdkp_root_path;
include_once($eqdkp_root_path . 'common.php');
//include_once($eqdkp_root_path . '/plugins/ticket/includes/functions.php');

// Set table names
global $table_prefix;
if (!defined('TK_TICKETS_TABLE')) 	{ define('TK_TICKETS_TABLE', $table_prefix . 'ticket_tickets');}
if (!defined('TK_REPLIES_TABLE')) 	{ define('TK_REPLIES_TABLE', $table_prefix . 'ticket_replied');}
if (!defined('TK_CONFIG_TABLE')) 	{ define('TK_CONFIG_TABLE', 	$table_prefix . 'ticket_config');}
if (!defined('TK_ADMINEMAIL_TABLE')) 	{ define('TK_ADMINEMAIL_TABLE', $table_prefix . 'ticket_adminemail');}
if (!defined('TK_USER_CONFIG')) 	{ define('TK_USER_CONFIG', $table_prefix . 'ticket_userconfig');}
?>
