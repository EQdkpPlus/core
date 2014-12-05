<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2011
 * Date:		$Date: 2014-10-06 23:44:01 +0200 (Mo, 06 Okt 2014) $
 * -----------------------------------------------------------------------
 * @author		$Author: godmod $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 14643 $
 *
 * $Id: update_2000.class.php 14643 2014-10-06 21:44:01Z godmod $
 */

if ( !defined('EQDKP_INC') ){
  header('HTTP/1.0 404 Not Found');exit;
}

include_once(registry::get_const('root_path').'maintenance/includes/sql_update_task.class.php');

class update_20012 extends sql_update_task {
	public $author			= 'GodMod';
	public $version			= '2.0.0.12'; //new plus-version
	public $ext_version		= '2.0.0'; //new plus-version
	public $name			= '2.0.0 Update 4';
	
	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_20012'		=> 'EQdkp Plus 2.0 Update 4',
					1 => 'Create Notification Types Table',
					2 => 'Create Notification Table',
					3 => 'Insert Notification Type',
					4 => 'Insert Notification Type',
					5 => 'Insert Notification Type',
					6 => 'Insert Notification Type',
					7 => 'Insert Notification Type',
					8 => 'Insert Notification Type',
					9 => 'Insert Notification Type',
					10 => 'Insert Notification Type',
					11 => 'Insert Notification Type',
					12 => 'Insert Notification Type',
					13 => 'Insert Notification Type',
					14 => 'Insert Notification Type',
				),
			'german' => array(
				'update_20012'		=> 'EQdkp Plus 2.0 Update 4',
					1 => 'Erstelle Notification Types Tabelle',
					2 => 'Erstelle Notification Tabelle',
					3 => 'Füge Notification Types ein',
					4 => 'Füge Notification Types ein',
					5 => 'Füge Notification Types ein',
					6 => 'Füge Notification Types ein',
					7 => 'Füge Notification Types ein',
					8 => 'Füge Notification Types ein',
					9 => 'Füge Notification Types ein',
					10 => 'Füge Notification Types ein',
					11 => 'Füge Notification Types ein',
					12 => 'Füge Notification Types ein',
					13 => 'Füge Notification Types ein',
					14 => 'Füge Notification Types ein',
			),
		);
		
		// init SQL querys
		$this->sqls = array(
			1 => "CREATE TABLE `__notification_types` (
	`id` VARCHAR(50) NOT NULL,
	`name` VARCHAR(50) NOT NULL,
	`category` VARCHAR(50) NULL DEFAULT NULL,
	`prio` INT(11) NOT NULL DEFAULT '0',
	`default` TINYINT(4) NOT NULL DEFAULT '0',
	`group` TINYINT(4) NOT NULL DEFAULT '0',
	`group_name` VARCHAR(80) NULL DEFAULT NULL,
	`group_at` INT(5) NOT NULL DEFAULT '0',
	`icon` VARCHAR(50) NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_bin;",
				2 => "CREATE TABLE `__notifications` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`type` VARCHAR(50) NOT NULL,
	`user_id` INT(11) NOT NULL,
	`time` INT(11) NOT NULL,
	`read` TINYINT(4) NOT NULL DEFAULT '0',
	`username` TEXT NULL,
	`dataset_id` VARCHAR(255) NULL DEFAULT NULL,
	`link` TEXT NULL,
	`additional_data` TEXT NULL,
	PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_bin;",
			
				3 => "INSERT INTO `__notification_types` (`id`, `name`, `category`, `prio`, `default`, `group`, `group_name`, `group_at`, `icon`) VALUES ('calendarevent_char_statuschange', 'notification_calendarevent_statuschange', 'calendarevent', 1, 0, 1, 'notification_calendarevent_statuschange_grouped', 3, 'fa-refresh');",
				4 => "INSERT INTO `__notification_types` (`id`, `name`, `category`, `prio`, `default`, `group`, `group_name`, `group_at`, `icon`) VALUES ('calendarevent_mod_groupchange', 'notification_calendarevent_mod_groupchange', 'calendarevent', 0, 1, 0, 'notification_calendarevent_mod_groupchange_grouped', 3, 'fa-users');",
				5 => "INSERT INTO `__notification_types` (`id`, `name`, `category`, `prio`, `default`, `group`, `group_name`, `group_at`, `icon`) VALUES ('calendarevent_mod_statuschange', 'notification_calendarevent_mod_statuschange', 'calendarevent', 0, 1, 0, 'notification_calendarevent_mod_statuschange_grouped', 3, 'fa-refresh');",
				6 => "INSERT INTO `__notification_types` (`id`, `name`, `category`, `prio`, `default`, `group`, `group_name`, `group_at`, `icon`) VALUES ('comment_new_article', 'notification_newcomment_article', 'articles', 0, 1, 1, 'notification_newcomment_article_grouped', 3, 'fa-comment');",
				7 => "INSERT INTO `__notification_types` (`id`, `name`, `category`, `prio`, `default`, `group`, `group_name`, `group_at`, `icon`) VALUES ('comment_new_userwall', 'notification_newcomment_userwall', 'userwall', 0, 1, 1, 'notification_newcomment_userwall_grouped', 3, 'fa-comment');",
				8 => "INSERT INTO `__notification_types` (`id`, `name`, `category`, `prio`, `default`, `group`, `group_name`, `group_at`, `icon`) VALUES ('comment_new_userwall_response', 'notification_newcomment_userwall_response', 'userwall', 0, 1, 1, 'notification_newcomment_userwall_response_grouped', 3, 'fa-comments-o');",
				9 => "INSERT INTO `__notification_types` (`id`, `name`, `category`, `prio`, `default`, `group`, `group_name`, `group_at`, `icon`) VALUES ('eqdkp_article_unpublished', 'notifaction_article_unpublished', 'articles', 1, 1, 0, NULL, 0, 'fa-file');",
				10 => "INSERT INTO `__notification_types` (`id`, `name`, `category`, `prio`, `default`, `group`, `group_name`, `group_at`, `icon`) VALUES ('eqdkp_char_confirm_required', 'notification_char_confirm_required', 'chars', 1, 1, 0, NULL, 0, 'fa-check-square-o');",
				11 => "INSERT INTO `__notification_types` (`id`, `name`, `category`, `prio`, `default`, `group`, `group_name`, `group_at`, `icon`) VALUES ('eqdkp_char_delete_requested', 'notification_char_delete_requested', 'chars', 1, 1, 0, NULL, 0, 'fa-trash');",
				12 => "INSERT INTO `__notification_types` (`id`, `name`, `category`, `prio`, `default`, `group`, `group_name`, `group_at`, `icon`) VALUES ('eqdkp_user_enable_requested', 'notification_user_enable_requested', 'user', 1, 1, 1, 'notification_user_enable_requested_grouped', 3, 'fa-user');",
				13 => "INSERT INTO `__notification_types` (`id`, `name`, `category`, `prio`, `default`, `group`, `group_name`, `group_at`, `icon`) VALUES ('calenderevent_closed', 'notification_calendarevent_closed', 'calendarevent', 0, 1, 0, '', 3, 'fa-lock');",
				14 => "INSERT INTO `__notification_types` (`id`, `name`, `category`, `prio`, `default`, `group`, `group_name`, `group_at`, `icon`) VALUES ('calenderevent_opened', 'notification_calendarevent_opened', 'calendarevent', 0, 1, 0, '', 3, 'fa-unlock');",
				
				
		);
	}
	
}


?>