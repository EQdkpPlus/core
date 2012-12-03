<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       11 June 2007
 * Date:        $Date:  $
 * -----------------------------------------------------------------------
 * @author      $Author:  $
 * @copyright   2006-2008 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev: 516 $
 * 
 * $Id:  $
 */

if ( !defined('EQDKP_INC') )
{
    die('Do not access this file directly.');
}

if ( !isset($eqdkp_root_path) )
{
    $eqdkp_root_path = '../../';
}

if( !is_file($eqdkp_root_path . 'config.php') )
{
	die('Error: could not locate configuration file.');
}

require_once($eqdkp_root_path . 'config.php');

define('DEBUG', $debug);

// User Levels
define('ANONYMOUS', -1);
define('USER',       0);

// User activation
define('USER_ACTIVATION_NONE',  0);
define('USER_ACTIVATION_SELF',  1);
define('USER_ACTIVATION_ADMIN', 2);

// URI Parameters
define('URI_ADJUSTMENT', 'a');
define('URI_EVENT',      'e');
define('URI_ITEM',       'i');
define('URI_LOG',        'l');
define('URI_NAME',       'name');
define('URI_NEWS',       'n');
define('URI_ORDER',      'o');
define('URI_PAGE',       'p');
define('URI_RAID',       'r');
define('URI_SESSION',    's');

// Database Table names
define('ADJUSTMENTS_TABLE',    $table_prefix . 'adjustments');
define('ADMINS_TABLE',         $table_prefix . 'admins');
define('AUTH_OPTIONS_TABLE',   $table_prefix . 'auth_options');
define('AUTH_USERS_TABLE',     $table_prefix . 'auth_users');
define('CONFIG_TABLE',         $table_prefix . 'config');
define('EVENTS_TABLE',         $table_prefix . 'events');
define('ITEMS_TABLE',          $table_prefix . 'items');
define('LOGS_TABLE',           $table_prefix . 'logs');
define('MEMBERS_TABLE',        $table_prefix . 'members');
define('MEMBER_RANKS_TABLE',   $table_prefix . 'member_ranks');
define('MEMBER_USER_TABLE',    $table_prefix . 'member_user');
define('NEWS_TABLE',           $table_prefix . 'news');
define('PLUGINS_TABLE',        $table_prefix . 'plugins');
define('RAID_ATTENDEES_TABLE', $table_prefix . 'raid_attendees');
define('RAIDS_TABLE',          $table_prefix . 'raids');
define('SESSIONS_TABLE',       $table_prefix . 'sessions');
define('STYLES_CONFIG_TABLE',  $table_prefix . 'style_config');
define('STYLES_TABLE',         $table_prefix . 'styles');
define('USERS_TABLE',          $table_prefix . 'users');
define('CLASS_TABLE',	       	 $table_prefix . 'classes');
define('RACE_TABLE',	         $table_prefix . 'races');
define('FACTION_TABLE',	       $table_prefix . 'factions');


//MultiDKP
define('MULTIDKP_TABLE',	       $table_prefix . 'multidkp');
define('MULTIDKP2EVENTS_TABLE',	 $table_prefix . 'multidkp2event');

//Pluskernel
define('PLUS_CONFIG_TABLE',	       $table_prefix . 'plus_config');
define('PLUS_LINKS_TABLE',	       $table_prefix . 'plus_links');
define('PLUS_UPDATE_TABLE',	       $table_prefix . 'plus_update');
define('PLUS_RSS_TABLE',	       $table_prefix . 'plus_rss');
define('CLASSCOLOR_TABLE',         $table_prefix . 'classcolors');
define('ITEMID_TABLE',         	   $table_prefix . 'itemIDs');


//Allvatar
define('ALLVATAR_GRP_TABLE',	     'gruppen');

//Plugin CTRT
if (!defined('CTRT_CONFIG_TABLE'))    			{ define('CTRT_CONFIG_TABLE', ($table_prefix . 'ctrt_config')); }
if (!defined('CTRT_ALIASES_TABLE'))   			{ define('CTRT_ALIASES_TABLE', ($table_prefix . 'ctrt_aliases')); }
if (!defined('CTRT_EVENT_TRIGGERS_TABLE'))		{ define('CTRT_EVENT_TRIGGERS_TABLE', ($table_prefix . 'ctrt_event_triggers')); }
if (!defined('CTRT_RAID_NOTE_TRIGGERS_TABLE'))	{ define('CTRT_RAID_NOTE_TRIGGERS_TABLE', ($table_prefix . 'ctrt_raid_note_triggers')); }
if (!defined('CTRT_OWN_RAIDS_TABLE'))			{ define('CTRT_OWN_RAIDS_TABLE', ($table_prefix . 'ctrt_own_raids')); }
if (!defined('CTRT_ADD_ITEMS_TABLE'))			{ define('CTRT_ADD_ITEMS_TABLE', ($table_prefix . 'ctrt_add_items')); }
if (!defined('CTRT_IGNORE_ITEMS_TABLE'))		{ define('CTRT_IGNORE_ITEMS_TABLE', ($table_prefix . 'ctrt_ignore_items')); }

//Plugin Raidplaner
if (!defined('RP_RAIDS_TABLE')) 				{ define('RP_RAIDS_TABLE', 			$table_prefix . 'raidplan_raids');}
if (!defined('RP_CLASSES_TABLE')) 				{ define('RP_CLASSES_TABLE', 		$table_prefix . 'raidplan_raid_classes');}
if (!defined('RP_ATTENDEES_TABLE')) 			{ define('RP_ATTENDEES_TABLE', 		$table_prefix . 'raidplan_raid_attendees');}
if (!defined('RP_WILDCARD_TABLE')) 				{ define('RP_WILDCARD_TABLE', 		$table_prefix . 'raidplan_wildcards');}
if (!defined('RP_CLASS_DIST_TABLE'))			{ define('RP_CLASS_DIST_TABLE', 	$table_prefix . 'raidplan_classes'); }

if (!defined('RP_RAID_TEMP_TABLE'))				{ define('RP_RAID_TEMP_TABLE', 		$table_prefix . 'raidplan_raidtemplate'); }
if (!defined('RP_CLASS_TEMP_TABLE'))			{ define('RP_CLASS_TEMP_TABLE', 	$table_prefix . 'raidplan_template_classes'); }
if (!defined('RP_REPEAT_TABLE'))				{ define('RP_REPEAT_TABLE', 		$table_prefix . 'raidplan_repeat'); }

//Plugin CharManager
if (!defined('MEMBER_ADDITION_TABLE')) { define('MEMBER_ADDITION_TABLE', $table_prefix . 'member_additions'); }

// Auth Options
define('A_EVENT_ADD',    1);
define('A_EVENT_UPD',    2);
define('A_EVENT_DEL',    3);
define('A_GROUPADJ_ADD', 4);
define('A_GROUPADJ_UPD', 5);
define('A_GROUPADJ_DEL', 6);
define('A_INDIVADJ_ADD', 7);
define('A_INDIVADJ_UPD', 8);
define('A_INDIVADJ_DEL', 9);
define('A_ITEM_ADD',    10);
define('A_ITEM_UPD',    11);
define('A_ITEM_DEL',    12);
define('A_NEWS_ADD',    13);
define('A_NEWS_UPD',    14);
define('A_NEWS_DEL',    15);
define('A_RAID_ADD',    16);
define('A_RAID_UPD',    17);
define('A_RAID_DEL',    18);
define('A_TURNIN_ADD',  19);
define('A_CONFIG_MAN',  20);
define('A_MEMBERS_MAN', 21);
define('A_USERS_MAN',   22);
define('A_LOGS_VIEW',   23);
define('U_EVENT_LIST',  24);
define('U_EVENT_VIEW',  25);
define('U_ITEM_LIST',   26);
define('U_ITEM_VIEW',   27);
define('U_MEMBER_LIST', 28);
define('U_MEMBER_VIEW', 29);
define('U_RAID_LIST',   30);
define('U_RAID_VIEW',   31);
define('A_PLUGINS_MAN', 32);
define('A_STYLES_MAN',  33);
define('A_SOAP_READ',   34);
define('A_SOAP_WRITE',  35);
define('A_BACKUP',      36);


?>
