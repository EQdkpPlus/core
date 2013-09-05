<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2011
 * Date:		$Date: 2013-03-03 18:38:23 +0100 (So, 03 Mrz 2013) $
 * -----------------------------------------------------------------------
 * @author		$Author: wallenium $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 13156 $
 *
 * $Id: update_1105.class.php 13156 2013-03-03 17:38:23Z wallenium $
 */

if ( !defined('EQDKP_INC') ){
  header('HTTP/1.0 404 Not Found');exit;
}

include_once(registry::get_const('root_path').'maintenance/includes/sql_update_task.class.php');

class update_200 extends sql_update_task {
	public $author		= 'GodMod';
	public $version		= '2.0.0'; //new plus-version
	public $name		= '2.0.0 Migration from 1.x';

	public static function __shortcuts() {
		$shortcuts = array('time', 'config', 'routing', 'pdc');
		return array_merge(parent::__shortcuts(), $shortcuts);
	}
	
	protected $fields2change	= array(
			'__calendars'		=> array('field' => array('color'), 'id' => 'id'),
			'__styles'			=> array('field' => array(
					'body_background',
					'body_link',
					'body_hlink',
					'header_link',
					'header_hlink',
					'tr_color1',
					'tr_color2',
					'th_color1',
					'fontcolor1',
					'fontcolor2',
					'fontcolor3',
					'fontcolor_neg',
					'fontcolor_pos',
					'table_border_color',
					'input_color',
					'input_border_color',
			), 'id' => 'style_id')
	);
	
	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_200'		=> 'EQdkp Plus 2.0 Migrate from 1.x',
				1 => 'Alter session-table, change session-key',
				2 => 'Alter user-table, remove allvatar_nick',
				3 => 'Alter groups_user-table',
				4 => 'Alter ranks table',
				5  => 'Alter logs table',
				6  => 'Alter styles table',
				7  => 'Alter links table',
				8  => 'Alter comments table',
				9  => 'Alter portal table',
				10  => 'Create portal_blocks table',
				11  => 'Create portal_layouts table',
				12 => 'Create articles table',
				13 => 'Create article_categories table',
				14 => 'Alter calendar table',
				15 => 'Insert auth_option',
				16 => 'Insert auth_option',
				17 => 'Insert auth_option',
				18 => 'Insert auth_option',
				19 => 'Update auth_option',
				20 => 'Insert eqdkp_modern style',
				21 => 'Update calendars',
				22 => 'Insert Userraid calendar',
				24 => 'Insert Link',
				25 => 'Insert Link',
				26 => 'Insert Link',
				27 => 'Insert Portal Layout',
				28 => 'Insert Article Category',
				29 => 'Insert Article Category',
				30 => 'Insert Article Category',
				31 => 'Insert Article Category',
				32 => 'Insert Article Category',
				33 => 'Insert Article Category',
				34 => 'Insert Article',
				35 => 'Insert Article',
				36 => 'Insert Article',
				37 => 'Insert Article',
				38 => 'Insert Article',
				39 => 'Insert Article',
				40 => 'Insert Article',
				41 => 'Insert Article',
				42 => 'Insert Article',
				43 => 'Insert Article',
				44 => 'Insert Article',
				45 => 'Insert Article Category',
				46 => 'Alter groups_user table',
				47 => 'Update Link Visbilities',
				48 => 'Alter member_profilefields table',
				'update_function' => 'Set Settings, Migrate News and Pages, Update Colors',
			),
			'german' => array(
				'update_200'		=> 'EQdkp Plus 2.0 Migration von 1.x',
				1 => 'Alter session-table, change session-key',
				2 => 'Alter user-table, remove allvatar_nick',
				3 => 'Alter groups_user-table',
				4 => 'Alter ranks table',
				5  => 'Alter logs table',
				6  => 'Alter styles table',
				7  => 'Alter links table',
				8  => 'Alter comments table',
				9  => 'Alter portal table',
				10  => 'Create portal_blocks table',
				11  => 'Create portal_layouts table',
				12 => 'Create articles table',
				13 => 'Create article_categories table',
				14 => 'Alter calendar table',
				15 => 'Insert auth_option',
				16 => 'Insert auth_option',
				17 => 'Insert auth_option',
				18 => 'Insert auth_option',
				19 => 'Update auth_option',
				20 => 'Insert eqdkp_modern style',
				21 => 'Update calendars',
				22 => 'Insert Userraid calendar',
				24 => 'Insert Link',
				25 => 'Insert Link',
				26 => 'Insert Link',
				27 => 'Insert Portal Layout',
				28 => 'Insert Article Category',
				29 => 'Insert Article Category',
				30 => 'Insert Article Category',
				31 => 'Insert Article Category',
				32 => 'Insert Article Category',
				33 => 'Insert Article Category',
				34 => 'Insert Article',
				35 => 'Insert Article',
				36 => 'Insert Article',
				37 => 'Insert Article',
				38 => 'Insert Article',
				39 => 'Insert Article',
				40 => 'Insert Article',
				41 => 'Insert Article',
				42 => 'Insert Article',
				43 => 'Insert Article',
				44 => 'Insert Article',
				45 => 'Insert Article Category',
				46 => 'Alter groups_user table',
				47 => 'Update Link Visbilities',
				48 => 'Alter member_profilefields table',
				'update_function' => 'Set Settings, Migrate News and Pages, Update Colors',
			),
		);
		
		// init SQL querys
		$this->sqls = array(
			1 => "ALTER TABLE `__sessions` CHANGE COLUMN `session_key` `session_key` VARCHAR(50) NOT NULL DEFAULT '' COLLATE 'utf8_bin' AFTER `session_browser`;",
			2 => "ALTER TABLE `__users` DROP COLUMN `allvatar_nick`;",
			3 => "ALTER TABLE `__groups_users`
	CHANGE COLUMN `group_id` `group_id` INT(11) NOT NULL FIRST,
	CHANGE COLUMN `user_id` `user_id` INT(11) NOT NULL AFTER `group_id`,
	ADD COLUMN `grpleader` INT(11) NOT NULL DEFAULT '0' AFTER `user_id`;",
			4 => "ALTER TABLE `__member_ranks`
	ADD COLUMN `rank_default` TINYINT(1) NOT NULL DEFAULT '0' AFTER `rank_sortid`,
	ADD COLUMN `rank_icon` VARCHAR(255) NULL DEFAULT '' COLLATE 'utf8_bin' AFTER `rank_default`;",
			5 => "ALTER TABLE `__logs`
	ADD COLUMN `log_record` VARCHAR(255) NOT NULL DEFAULT '' COLLATE 'utf8_bin' AFTER `log_flag`,
	ADD COLUMN `log_record_id` VARCHAR(255) NOT NULL DEFAULT '' COLLATE 'utf8_bin' AFTER `log_record`;",
			6 => "ALTER TABLE `__styles`
	CHANGE COLUMN `body_background` `body_background` VARCHAR(10) NULL DEFAULT NULL COLLATE 'utf8_bin' AFTER `template_path`,
	CHANGE COLUMN `body_link` `body_link` VARCHAR(10) NULL DEFAULT NULL COLLATE 'utf8_bin' AFTER `body_background`,
	CHANGE COLUMN `body_hlink` `body_hlink` VARCHAR(10) NULL DEFAULT NULL COLLATE 'utf8_bin' AFTER `body_link_style`,
	CHANGE COLUMN `header_link` `header_link` VARCHAR(10) NULL DEFAULT NULL COLLATE 'utf8_bin' AFTER `body_hlink_style`,
	CHANGE COLUMN `header_hlink` `header_hlink` VARCHAR(10) NULL DEFAULT NULL COLLATE 'utf8_bin' AFTER `header_link_style`,
	CHANGE COLUMN `tr_color1` `tr_color1` VARCHAR(10) NULL DEFAULT NULL COLLATE 'utf8_bin' AFTER `header_hlink_style`,
	CHANGE COLUMN `tr_color2` `tr_color2` VARCHAR(10) NULL DEFAULT NULL COLLATE 'utf8_bin' AFTER `tr_color1`,
	CHANGE COLUMN `th_color1` `th_color1` VARCHAR(10) NULL DEFAULT NULL COLLATE 'utf8_bin' AFTER `tr_color2`,
	CHANGE COLUMN `fontcolor1` `fontcolor1` VARCHAR(10) NULL DEFAULT NULL COLLATE 'utf8_bin' AFTER `fontsize3`,
	CHANGE COLUMN `fontcolor2` `fontcolor2` VARCHAR(10) NULL DEFAULT NULL COLLATE 'utf8_bin' AFTER `fontcolor1`,
	CHANGE COLUMN `fontcolor3` `fontcolor3` VARCHAR(10) NULL DEFAULT NULL COLLATE 'utf8_bin' AFTER `fontcolor2`,
	CHANGE COLUMN `fontcolor_neg` `fontcolor_neg` VARCHAR(10) NULL DEFAULT NULL COLLATE 'utf8_bin' AFTER `fontcolor3`,
	CHANGE COLUMN `fontcolor_pos` `fontcolor_pos` VARCHAR(10) NULL DEFAULT NULL COLLATE 'utf8_bin' AFTER `fontcolor_neg`,
	CHANGE COLUMN `table_border_color` `table_border_color` VARCHAR(10) NULL DEFAULT NULL COLLATE 'utf8_bin' AFTER `table_border_width`,
	CHANGE COLUMN `input_color` `input_color` VARCHAR(10) NULL DEFAULT NULL COLLATE 'utf8_bin' AFTER `table_border_style`,
	CHANGE COLUMN `input_border_color` `input_border_color` VARCHAR(10) NULL DEFAULT NULL COLLATE 'utf8_bin' AFTER `input_border_width`;",
			7 => "ALTER TABLE `__links` CHANGE COLUMN `link_visibility` `link_visibility` TEXT NOT NULL COLLATE 'utf8_bin' AFTER `link_sortid`;",
			8 => "ALTER TABLE `__comments` ADD COLUMN `reply_to` INT UNSIGNED NOT NULL DEFAULT '0' AFTER `page`;",
			9 => "ALTER TABLE `__portal`
	DROP COLUMN `enabled`,
	DROP COLUMN `settings`,
	DROP COLUMN `position`,
	DROP COLUMN `number`;",
			10 => "CREATE TABLE `__portal_blocks` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`wide_content` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_bin;",
			11 => "CREATE TABLE `__portal_layouts` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`blocks` TEXT COLLATE 'utf8_bin' NOT NULL,
	`modules` TEXT COLLATE 'utf8_bin' NOT NULL,
	PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_bin;",
			12 => "CREATE TABLE `__articles` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`text` TEXT COLLATE 'utf8_bin' NOT NULL,
	`category` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`featured` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
	`comments` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
	`votes` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
	`published` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
	`show_from` VARCHAR(11) COLLATE utf8_bin NOT NULL DEFAULT '',
	`show_to` VARCHAR(11) COLLATE utf8_bin NOT NULL DEFAULT '',
	`user_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`date` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`previewimage` TEXT COLLATE 'utf8_bin' NOT NULL,
	`alias` VARCHAR(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`hits` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`sort_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`tags` TEXT COLLATE 'utf8_bin' NOT NULL,
	`votes_count` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`votes_sum` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`votes_users` TEXT COLLATE 'utf8_bin' NULL DEFAULT NULL,
	`last_edited` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`last_edited_user` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`page_objects` TEXT NULL DEFAULT NULL,
	`hide_header` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_bin;",
			13 => "CREATE TABLE `__article_categories` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`alias` VARCHAR(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`portal_layout` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`description` TEXT COLLATE utf8_bin NOT NULL,
	`per_page` INT(3) UNSIGNED NOT NULL DEFAULT '25',
	`permissions` TEXT COLLATE utf8_bin NOT NULL,
	`published` TINYINT(4) UNSIGNED NOT NULL DEFAULT '0',
	`parent` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`sort_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`list_type` INT(3) UNSIGNED NOT NULL DEFAULT '1',
	`aggregation` TEXT COLLATE utf8_bin NOT NULL,
	`featured_only` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`notify_on_onpublished_articles` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`social_share_buttons` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`show_childs` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
	`article_published_state` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
	`hide_header` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`sortation_type` INT(3) UNSIGNED NOT NULL DEFAULT '1',
	`featured_ontop` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`hide_on_rss` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_bin;",
			14 => "ALTER TABLE `__calendars` ADD COLUMN `restricted` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `type`;",
			15 => "INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_usergroups_man','N');",
			16 => "INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_usergroups_grpleader','N');",
			17 => "INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_articles_man','N');",
			18 => "INSERT INTO __auth_options (auth_value, auth_default) VALUES ('u_files_man','Y');",
			19 => "UPDATE `__auth_options` SET `auth_default`='Y' WHERE `auth_value`='u_userlist';",
			20 => "INSERT INTO `__styles` (`style_name`, `style_version`, `style_contact`, `style_author`, `enabled`, `template_path`, `body_background`, `body_link`, `body_link_style`, `body_hlink`, `body_hlink_style`, `header_link`, `header_link_style`, `header_hlink`, `header_hlink_style`, `tr_color1`, `tr_color2`, `th_color1`, `fontface1`, `fontface2`, `fontface3`, `fontsize1`, `fontsize2`, `fontsize3`, `fontcolor1`, `fontcolor2`, `fontcolor3`, `fontcolor_neg`, `fontcolor_pos`, `table_border_width`, `table_border_color`, `table_border_style`, `input_color`, `input_border_width`, `input_border_color`, `input_border_style`, `attendees_columns`, `logo_position`, `background_img`, `css_file`, `use_db_vars`, `column_right_width`, `column_left_width`, `portal_width`) VALUES ('EQdkp Modern', '0.0.1', '', 'GodMod', '1', 'eqdkp_modern', '#2B577C', '#C5E5FF', 'none', '#EEEEEE', 'none', '#FFFFFF', 'none', '#C3E5FF', 'none', '#14293B', '#1D3953', '#2B577C', 'Verdana, Arial, Helvetica, sans-serif', 'Verdana, Arial, Helvetica, sans-serif', 'Verdana, Arial, Helvetica, sans-serif', 10, 11, 12, '#EEEEEE', '#C3E5FF', '#000000', '#FF0000', '#008800', 1, '#999999', 'solid', '#EEEEEE', 1, '#2B577C', 'solid', '6', 'left', '', '', 1, '0px', '0px', '0px');",
			21 => "UPDATE `__calendars` SET `restricted` = '1' WHERE `id` = '1';",
			22 => "INSERT INTO `__calendars` (`name`, `color`, `private`, `feed`, `system`, `type`, `restricted`) VALUES ('Userraids', '#0cb20f', '0', NULL, '1', '1', '0');",			
			24 => "INSERT INTO `__links` (`link_url`, `link_name`, `link_window`, `link_menu`, `link_sortid`, `link_visibility`, `link_height`) VALUES ('#', 'Guild', '0', 0, 0, '[&#34;0&#34;]', 4024);",
			25 => "INSERT INTO `__links` (`link_url`, `link_name`, `link_window`, `link_menu`, `link_sortid`, `link_visibility`, `link_height`) VALUES ('#', 'Links', '0', 0, 0, '[&#34;0&#34;]', 4024);",
			26 => "INSERT INTO `__links` (`link_url`, `link_name`, `link_window`, `link_menu`, `link_sortid`, `link_visibility`, `link_height`) VALUES ('#', 'DKP-System', '0', 0, 0, '[&#34;0&#34;]', 4024);",
			27 => 'INSERT INTO `__portal_layouts` (`id`, `name`, `blocks`, `modules`) VALUES (1, \'Standard\', \'a:4:{i:0;s:4:"left";i:1;s:6:"middle";i:2;s:6:"bottom";i:3;s:5:"right";}\', \'a:0:{}\');',
			28 => 'INSERT INTO `__article_categories` (`id`, `name`, `alias`, `portal_layout`, `description`, `per_page`, `permissions`, `published`, `parent`, `sort_id`, `list_type`, `aggregation`, `featured_only`, `notify_on_onpublished_articles`, `social_share_buttons`, `show_childs`, `article_published_state`, `hide_header`, `sortation_type`, `featured_ontop`, `hide_on_rss`) VALUES (1, \'System\', \'system\', 1, \'\', 25, \'a:5:{s:3:"rea";a:6:{i:2;s:1:"1";i:3;s:1:"1";i:4;s:1:"1";i:5;s:1:"1";i:6;s:1:"1";i:1;s:1:"1";}s:3:"cre";a:6:{i:2;s:1:"1";i:3;s:1:"1";i:4;s:2:"-1";i:5;s:2:"-1";i:6;s:1:"0";i:1;s:2:"-1";}s:3:"upd";a:6:{i:2;s:1:"1";i:3;s:1:"1";i:4;s:2:"-1";i:5;s:2:"-1";i:6;s:1:"0";i:1;s:2:"-1";}s:3:"del";a:6:{i:2;s:1:"1";i:3;s:1:"1";i:4;s:2:"-1";i:5;s:2:"-1";i:6;s:1:"0";i:1;s:2:"-1";}s:3:"chs";a:6:{i:2;s:1:"1";i:3;s:1:"1";i:4;s:2:"-1";i:5;s:2:"-1";i:6;s:1:"0";i:1;s:2:"-1";}}\', 1, 0, 99999999, 1, \'a:0:{}\', 0, 0, 0, 0, 1, 0, 1, 0, 1);
			',
			29 => 'INSERT INTO `__article_categories` (`id`, `name`, `alias`, `portal_layout`, `description`, `per_page`, `permissions`, `published`, `parent`, `sort_id`, `list_type`, `aggregation`, `featured_only`, `notify_on_onpublished_articles`, `social_share_buttons`, `show_childs`, `article_published_state`, `hide_header`, `sortation_type`, `featured_ontop`, `hide_on_rss`) VALUES (2, \'News\', \'news\', 1, \'\', 15, \'a:5:{s:3:"rea";a:6:{i:2;s:2:"-1";i:3;s:2:"-1";i:4;s:2:"-1";i:5;s:2:"-1";i:6;s:2:"-1";i:1;s:2:"-1";}s:3:"cre";a:6:{i:2;s:2:"-1";i:3;s:2:"-1";i:4;s:2:"-1";i:5;s:2:"-1";i:6;s:1:"1";i:1;s:2:"-1";}s:3:"upd";a:6:{i:2;s:2:"-1";i:3;s:2:"-1";i:4;s:2:"-1";i:5;s:2:"-1";i:6;s:1:"1";i:1;s:2:"-1";}s:3:"del";a:6:{i:2;s:2:"-1";i:3;s:2:"-1";i:4;s:2:"-1";i:5;s:2:"-1";i:6;s:1:"1";i:1;s:2:"-1";}s:3:"chs";a:6:{i:2;s:2:"-1";i:3;s:2:"-1";i:4;s:2:"-1";i:5;s:2:"-1";i:6;s:1:"1";i:1;s:2:"-1";}}\', 1, 1, 99999999, 1, \'a:1:{i:0;s:1:"2";}\', 0, 0, 0, 0, 1, 1, 1, 0, 0);
			',
			30 => 'INSERT INTO `__article_categories` (`id`, `name`, `alias`, `portal_layout`, `description`, `per_page`, `permissions`, `published`, `parent`, `sort_id`, `list_type`, `aggregation`, `featured_only`, `notify_on_onpublished_articles`, `social_share_buttons`, `show_childs`, `article_published_state`, `hide_header`, `sortation_type`, `featured_ontop`, `hide_on_rss`) VALUES (3, \'Events\', \'events\', 1, \'\', 25, \'a:5:{s:3:"rea";a:6:{i:2;s:2:"-1";i:3;s:2:"-1";i:4;s:2:"-1";i:5;s:2:"-1";i:6;s:2:"-1";i:1;s:2:"-1";}s:3:"cre";a:6:{i:2;s:2:"-1";i:3;s:2:"-1";i:4;s:2:"-1";i:5;s:2:"-1";i:6;s:2:"-1";i:1;s:2:"-1";}s:3:"upd";a:6:{i:2;s:2:"-1";i:3;s:2:"-1";i:4;s:2:"-1";i:5;s:2:"-1";i:6;s:2:"-1";i:1;s:2:"-1";}s:3:"del";a:6:{i:2;s:2:"-1";i:3;s:2:"-1";i:4;s:2:"-1";i:5;s:2:"-1";i:6;s:2:"-1";i:1;s:2:"-1";}s:3:"chs";a:6:{i:2;s:2:"-1";i:3;s:2:"-1";i:4;s:2:"-1";i:5;s:2:"-1";i:6;s:2:"-1";i:1;s:2:"-1";}}\', 1, 1, 99999999, 1, \'a:1:{i:0;i:21;}\', 0, 0, 0, 0, 1, 1, 0, 0, 1);
			',
			31 => 'INSERT INTO `__article_categories` (`id`, `name`, `alias`, `portal_layout`, `description`, `per_page`, `permissions`, `published`, `parent`, `sort_id`, `list_type`, `aggregation`, `featured_only`, `notify_on_onpublished_articles`, `social_share_buttons`, `show_childs`, `article_published_state`, `hide_header`, `sortation_type`, `featured_ontop`, `hide_on_rss`) VALUES (4, \'Items\', \'items\', 1, \'\', 25, \'a:5:{s:3:"rea";a:6:{i:2;s:2:"-1";i:3;s:2:"-1";i:4;s:2:"-1";i:5;s:2:"-1";i:6;s:2:"-1";i:1;s:2:"-1";}s:3:"cre";a:6:{i:2;s:2:"-1";i:3;s:2:"-1";i:4;s:2:"-1";i:5;s:2:"-1";i:6;s:2:"-1";i:1;s:2:"-1";}s:3:"upd";a:6:{i:2;s:2:"-1";i:3;s:2:"-1";i:4;s:2:"-1";i:5;s:2:"-1";i:6;s:2:"-1";i:1;s:2:"-1";}s:3:"del";a:6:{i:2;s:2:"-1";i:3;s:2:"-1";i:4;s:2:"-1";i:5;s:2:"-1";i:6;s:2:"-1";i:1;s:2:"-1";}s:3:"chs";a:6:{i:2;s:2:"-1";i:3;s:2:"-1";i:4;s:2:"-1";i:5;s:2:"-1";i:6;s:2:"-1";i:1;s:2:"-1";}}\', 1, 1, 99999999, 1, \'a:1:{i:0;i:20;}\', 0, 0, 0, 0, 1, 1, 0, 0, 1);
			',
			32 => 'INSERT INTO `__article_categories` (`id`, `name`, `alias`, `portal_layout`, `description`, `per_page`, `permissions`, `published`, `parent`, `sort_id`, `list_type`, `aggregation`, `featured_only`, `notify_on_onpublished_articles`, `social_share_buttons`, `show_childs`, `article_published_state`, `hide_header`, `sortation_type`, `featured_ontop`, `hide_on_rss`) VALUES (5, \'Raids\', \'raids\', 1, \'\', 25, \'a:5:{s:3:"rea";a:6:{i:2;s:2:"-1";i:3;s:2:"-1";i:4;s:2:"-1";i:5;s:2:"-1";i:6;s:2:"-1";i:1;s:2:"-1";}s:3:"cre";a:6:{i:2;s:2:"-1";i:3;s:2:"-1";i:4;s:2:"-1";i:5;s:2:"-1";i:6;s:2:"-1";i:1;s:2:"-1";}s:3:"upd";a:6:{i:2;s:2:"-1";i:3;s:2:"-1";i:4;s:2:"-1";i:5;s:2:"-1";i:6;s:2:"-1";i:1;s:2:"-1";}s:3:"del";a:6:{i:2;s:2:"-1";i:3;s:2:"-1";i:4;s:2:"-1";i:5;s:2:"-1";i:6;s:2:"-1";i:1;s:2:"-1";}s:3:"chs";a:6:{i:2;s:2:"-1";i:3;s:2:"-1";i:4;s:2:"-1";i:5;s:2:"-1";i:6;s:2:"-1";i:1;s:2:"-1";}}\', 1, 1, 99999999, 1, \'a:1:{i:0;i:19;}\', 0, 0, 0, 0, 1, 0, 0, 0, 1);
			',
			33 => 'INSERT INTO `__article_categories` (`id`, `name`, `alias`, `portal_layout`, `description`, `per_page`, `permissions`, `published`, `parent`, `sort_id`, `list_type`, `aggregation`, `featured_only`, `notify_on_onpublished_articles`, `social_share_buttons`, `show_childs`, `article_published_state`, `hide_header`, `sortation_type`, `featured_ontop`, `hide_on_rss`) VALUES (6, \'Calendar\', \'calendar\', 1, \'\', 25, \'a:5:{s:3:"rea";a:6:{i:1;s:2:"-1";i:2;s:2:"-1";i:3;s:2:"-1";i:4;s:2:"-1";i:5;s:2:"-1";i:6;s:2:"-1";}s:3:"cre";a:6:{i:1;s:2:"-1";i:2;s:2:"-1";i:3;s:2:"-1";i:4;s:2:"-1";i:5;s:2:"-1";i:6;s:2:"-1";}s:3:"upd";a:6:{i:1;s:2:"-1";i:2;s:2:"-1";i:3;s:2:"-1";i:4;s:2:"-1";i:5;s:2:"-1";i:6;s:2:"-1";}s:3:"del";a:6:{i:1;s:2:"-1";i:2;s:2:"-1";i:3;s:2:"-1";i:4;s:2:"-1";i:5;s:2:"-1";i:6;s:2:"-1";}s:3:"chs";a:6:{i:1;s:2:"-1";i:2;s:2:"-1";i:3;s:2:"-1";i:4;s:2:"-1";i:5;s:2:"-1";i:6;s:2:"-1";}}\', 1, 1, 99999999, 1, \'a:1:{i:0;i:6;}\', 0, 0, 0, 0, 1, 1, 1, 0, 1);
			',
			34 => "INSERT INTO `__articles` (`id`, `title`, `text`, `category`, `featured`, `comments`, `votes`, `published`, `show_from`, `show_to`, `user_id`, `date`, `previewimage`, `alias`, `hits`, `sort_id`, `tags`, `votes_count`, `votes_sum`, `votes_users`, `last_edited`, `last_edited_user`, `page_objects`, `hide_header`) VALUES (2, 'Raid', '&lt;p class=&quot;system-article&quot; title=&quot;raid&quot;&gt;raid&lt;/p&gt;\r\n&lt;p&gt; &lt;/p&gt;', 5, 0, 0, 0, 1, '', '', 1, 1375968840, '', 'raid', 0, 0, 'a:1:{i:0;s:0:\"\";}', 0, 0, '', 1375968892, 1, 'a:1:{i:0;s:4:\"raid\";}', 1);
			",
			35 => "INSERT INTO `__articles` (`id`, `title`, `text`, `category`, `featured`, `comments`, `votes`, `published`, `show_from`, `show_to`, `user_id`, `date`, `previewimage`, `alias`, `hits`, `sort_id`, `tags`, `votes_count`, `votes_sum`, `votes_users`, `last_edited`, `last_edited_user`, `page_objects`, `hide_header`) VALUES (3, 'Item', '&lt;p class=&quot;system-article&quot; title=&quot;item&quot;&gt;item&lt;/p&gt;\r\n&lt;p&gt; &lt;/p&gt;', 4, 0, 0, 0, 1, '', '', 1, 1375968900, '', 'item', 0, 0, 'a:1:{i:0;s:0:\"\";}', 0, 0, '', 1375968921, 1, 'a:1:{i:0;s:4:\"item\";}', 1);
			",
			36 => "INSERT INTO `__articles` (`id`, `title`, `text`, `category`, `featured`, `comments`, `votes`, `published`, `show_from`, `show_to`, `user_id`, `date`, `previewimage`, `alias`, `hits`, `sort_id`, `tags`, `votes_count`, `votes_sum`, `votes_users`, `last_edited`, `last_edited_user`, `page_objects`, `hide_header`) VALUES (4, 'Event', '&lt;p class=&quot;system-article&quot; title=&quot;event&quot;&gt;event&lt;/p&gt;\r\n&lt;p&gt; &lt;/p&gt;', 3, 0, 0, 0, 1, '', '', 1, 1375969140, '', 'event', 0, 0, 'a:1:{i:0;s:0:\"\";}', 0, 0, '', 1375969201, 1, 'a:1:{i:0;s:5:\"event\";}', 1);
			",
			37 => "INSERT INTO `__articles` (`id`, `title`, `text`, `category`, `featured`, `comments`, `votes`, `published`, `show_from`, `show_to`, `user_id`, `date`, `previewimage`, `alias`, `hits`, `sort_id`, `tags`, `votes_count`, `votes_sum`, `votes_users`, `last_edited`, `last_edited_user`, `page_objects`, `hide_header`) VALUES (5, 'Character', '&lt;p class=&quot;system-article&quot; title=&quot;character&quot;&gt;character&lt;/p&gt;\r\n&lt;p&gt; &lt;/p&gt;', 1, 0, 0, 0, 1, '', '', 1, 1375969320, '', 'character', 0, 0, 'a:1:{i:0;s:0:\"\";}', 0, 0, '', 1375969361, 1, 'a:1:{i:0;s:9:\"character\";}', 1);
			",
			38 => "INSERT INTO `__articles` (`id`, `title`, `text`, `category`, `featured`, `comments`, `votes`, `published`, `show_from`, `show_to`, `user_id`, `date`, `previewimage`, `alias`, `hits`, `sort_id`, `tags`, `votes_count`, `votes_sum`, `votes_users`, `last_edited`, `last_edited_user`, `page_objects`, `hide_header`) VALUES (6, 'Roster', '&lt;p class=&quot;system-article&quot; title=&quot;roster&quot;&gt;roster&lt;/p&gt;\r\n&lt;p&gt; &lt;/p&gt;', 1, 0, 0, 0, 1, '', '', 1, 1375969740, '', 'roster', 0, 0, 'a:1:{i:0;s:0:\"\";}', 0, 0, '', 1375969757, 1, 'a:1:{i:0;s:6:\"roster\";}', 1);
			",
			39 => "INSERT INTO `__articles` (`id`, `title`, `text`, `category`, `featured`, `comments`, `votes`, `published`, `show_from`, `show_to`, `user_id`, `date`, `previewimage`, `alias`, `hits`, `sort_id`, `tags`, `votes_count`, `votes_sum`, `votes_users`, `last_edited`, `last_edited_user`, `page_objects`, `hide_header`) VALUES (7, 'Events', '&lt;p class=&quot;system-article&quot; title=&quot;events&quot;&gt;events&lt;/p&gt;\r\n&lt;p&gt; &lt;/p&gt;', 1, 0, 0, 0, 1, '', '', 1, 1375969800, '', 'events', 0, 0, 'a:1:{i:0;s:0:\"\";}', 0, 0, '', 1375969867, 1, 'a:1:{i:0;s:6:\"events\";}', 1);
			",
			40 => "INSERT INTO `__articles` (`id`, `title`, `text`, `category`, `featured`, `comments`, `votes`, `published`, `show_from`, `show_to`, `user_id`, `date`, `previewimage`, `alias`, `hits`, `sort_id`, `tags`, `votes_count`, `votes_sum`, `votes_users`, `last_edited`, `last_edited_user`, `page_objects`, `hide_header`) VALUES (8, 'Items', '&lt;p class=&quot;system-article&quot; title=&quot;items&quot;&gt;items&lt;/p&gt;\r\n&lt;p&gt; &lt;/p&gt;', 1, 0, 0, 0, 1, '', '', 1, 1375969860, '', 'items', 0, 0, 'a:1:{i:0;s:0:\"\";}', 0, 0, '', 1375969890, 1, 'a:1:{i:0;s:5:\"items\";}', 1);
			",
			41 => "INSERT INTO `__articles` (`id`, `title`, `text`, `category`, `featured`, `comments`, `votes`, `published`, `show_from`, `show_to`, `user_id`, `date`, `previewimage`, `alias`, `hits`, `sort_id`, `tags`, `votes_count`, `votes_sum`, `votes_users`, `last_edited`, `last_edited_user`, `page_objects`, `hide_header`) VALUES (9, 'Points', '&lt;p class=&quot;system-article&quot; title=&quot;points&quot;&gt;points&lt;/p&gt;\r\n&lt;p&gt; &lt;/p&gt;', 1, 0, 0, 0, 1, '', '', 1, 1375969860, '', 'points', 0, 0, 'a:1:{i:0;s:0:\"\";}', 0, 0, '', 1375969920, 1, 'a:1:{i:0;s:6:\"points\";}', 1);
			",
			42 => "INSERT INTO `__articles` (`id`, `title`, `text`, `category`, `featured`, `comments`, `votes`, `published`, `show_from`, `show_to`, `user_id`, `date`, `previewimage`, `alias`, `hits`, `sort_id`, `tags`, `votes_count`, `votes_sum`, `votes_users`, `last_edited`, `last_edited_user`, `page_objects`, `hide_header`) VALUES (10, 'Raids', '&lt;p class=&quot;system-article&quot; title=&quot;raids&quot;&gt;raids&lt;/p&gt;\r\n&lt;p&gt; &lt;/p&gt;', 1, 0, 0, 0, 1, '', '', 1, 1375969920, '', 'raids', 0, 0, 'a:1:{i:0;s:0:\"\";}', 0, 0, '', 1375969956, 1, 'a:1:{i:0;s:5:\"raids\";}', 1);
			",
			43 => "INSERT INTO `__articles` (`id`, `title`, `text`, `category`, `featured`, `comments`, `votes`, `published`, `show_from`, `show_to`, `user_id`, `date`, `previewimage`, `alias`, `hits`, `sort_id`, `tags`, `votes_count`, `votes_sum`, `votes_users`, `last_edited`, `last_edited_user`, `page_objects`, `hide_header`) VALUES (12, 'Calendarevent', '&lt;p class=&quot;system-article&quot; title=&quot;calendarevent&quot;&gt;calendarevent&lt;/p&gt;\r\n&lt;p&gt; &lt;/p&gt;', 6, 0, 1, 0, 1, '', '', 1, 1376132580, '', 'calendarevent', 0, 0, 'a:1:{i:0;s:0:\"\";}', 0, 0, '', 1376132677, 1, 'a:1:{i:0;s:13:\"calendarevent\";}', 1);
			",
			44 => "INSERT INTO `__articles` (`id`, `title`, `text`, `category`, `featured`, `comments`, `votes`, `published`, `show_from`, `show_to`, `user_id`, `date`, `previewimage`, `alias`, `hits`, `sort_id`, `tags`, `votes_count`, `votes_sum`, `votes_users`, `last_edited`, `last_edited_user`, `page_objects`, `hide_header`) VALUES (13, 'Calendar', '&lt;p class=&quot;system-article&quot; title=&quot;calendar&quot;&gt;calendar&lt;/p&gt;\r\n&lt;p&gt; &lt;/p&gt;', 1, 0, 0, 0, 1, '', '', 1, 1376132580, '', 'calendar', 0, 0, 'a:1:{i:0;s:0:\"\";}', 0, 0, '', 1376132650, 1, 'a:1:{i:0;s:8:\"calendar\";}', 1);
			",
			45 => 'INSERT INTO `__article_categories` (`id`, `name`, `alias`, `portal_layout`, `description`, `per_page`, `permissions`, `published`, `parent`, `sort_id`, `list_type`, `aggregation`, `featured_only`, `notify_on_onpublished_articles`, `social_share_buttons`, `show_childs`, `article_published_state`, `hide_header`, `sortation_type`, `featured_ontop`, `hide_on_rss`) VALUES (7, \'Pages\', \'pages\', 1, \'\', 25, \'a:5:{s:3:"rea";a:6:{i:1;s:2:"-1";i:2;s:2:"-1";i:3;s:2:"-1";i:4;s:2:"-1";i:5;s:2:"-1";i:6;s:2:"-1";}s:3:"cre";a:6:{i:1;s:2:"-1";i:2;s:2:"-1";i:3;s:2:"-1";i:4;s:2:"-1";i:5;s:2:"-1";i:6;s:2:"-1";}s:3:"upd";a:6:{i:1;s:2:"-1";i:2;s:2:"-1";i:3;s:2:"-1";i:4;s:2:"-1";i:5;s:2:"-1";i:6;s:2:"-1";}s:3:"del";a:6:{i:1;s:2:"-1";i:2;s:2:"-1";i:3;s:2:"-1";i:4;s:2:"-1";i:5;s:2:"-1";i:6;s:2:"-1";}s:3:"chs";a:6:{i:1;s:2:"-1";i:2;s:2:"-1";i:3;s:2:"-1";i:4;s:2:"-1";i:5;s:2:"-1";i:6;s:2:"-1";}}\', 1, 1, 99999999, 1, \'a:1:{i:0;i:7;}\', 0, 0, 0, 0, 1, 0, 1, 0, 0);
			',
			46 => "ALTER TABLE `__groups_user` ADD COLUMN `groups_user_sortid` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0' AFTER `groups_user_hide`;",
			47 => "UPDATE __links SET link_visibility='[&#34;0&#34;]';",
			48 => "ALTER TABLE `__member_profilefields` ADD COLUMN `options_language` VARCHAR(255) NULL AFTER `options`;",
		);

	}
	 
	//Settings, Migrate News and Infopages, Update Colors
	//After that, rename the tables to obselete
	public function update_function(){
		//Set Default Settings
		$this->config->set( 'start_page' , 'news');
		$arrQuery = $this->db->query_first("SELECT style_id FROM __styles WHERE template_path='eqdkp_modern'");
		$this->config->set('default_style', (int)$arrQuery);
		$this->config->set('default_style_overwrite', 1);
		$this->config->set('mainmenu', 'a:6:{i:0;a:1:{s:4:"item";a:2:{s:4:"hash";s:32:"828e0013b8f3bc1bb22b4f57172b019d";s:6:"hidden";s:1:"0";}}i:1;a:1:{s:4:"item";a:2:{s:4:"hash";s:32:"e2672c7758bc5f8bb38ddb4b60fa530c";s:6:"hidden";s:1:"0";}}i:2;a:2:{s:4:"item";a:2:{s:4:"hash";s:32:"92f04bcfb72b27949ee68f52a412acac";s:6:"hidden";s:1:"0";}s:7:"_childs";a:1:{i:0;a:1:{s:4:"item";a:2:{s:4:"hash";s:32:"7809b1008f1d915120b3b549ca033e1f";s:6:"hidden";s:1:"0";}}}}i:3;a:2:{s:4:"item";a:2:{s:4:"hash";s:32:"ca65b9cf176197c365f17035270cc9f1";s:6:"hidden";s:1:"0";}s:7:"_childs";a:4:{i:1;a:1:{s:4:"item";a:2:{s:4:"hash";s:32:"0e6acee4fa4635f2c25acbf0bad6c445";s:6:"hidden";s:1:"0";}}i:2;a:1:{s:4:"item";a:2:{s:4:"hash";s:32:"53433bf03b32b055f789428e95454cec";s:6:"hidden";s:1:"0";}}i:3;a:1:{s:4:"item";a:2:{s:4:"hash";s:32:"c1ec6e24e3276e17e3edcb08655d9181";s:6:"hidden";s:1:"0";}}i:4;a:1:{s:4:"item";a:2:{s:4:"hash";s:32:"65d93e089c21a737b601f81e70921b8b";s:6:"hidden";s:1:"0";}}}}i:4;a:1:{s:4:"item";a:2:{s:4:"hash";s:32:"fd613a0f87638ad1372d9b06bad29cb3";s:6:"hidden";s:1:"0";}}i:5;a:2:{s:4:"item";a:2:{s:4:"hash";s:32:"ebc90e9afa50f8383d4f93ce9944b8dd";s:6:"hidden";s:1:"0";}s:7:"_childs";a:1:{i:5;a:1:{s:4:"item";a:2:{s:4:"hash";s:32:"276753faf0f1a394d24bea5fa54a4e6b";s:6:"hidden";s:1:"0";}}}}}');
		
		
		//Migrate News		
		$sql = "SELECT * FROM __news";
		$query = $this->db->query($sql);
		while ($row = $this->db->fetch_record($query)) {
			$message = $row['news_message'];
			if ($row['extended_message'] != ""){
				$message .= '<hr id="system-readmore" />';
				$message .= $row['extended_message'];
			}	
			
			$this->db->query("INSERT INTO __articles :params", array(
					'title' 			=> $row['news_headline'],
					'text'				=> $message,
					'category'			=> 2,
					'featured'			=> 0,
					'comments'			=> !intval($row['nocomments']),
					'votes'				=> 0,
					'published'			=> 1,
					'show_from'			=> $row['news_start'],
					'show_to'			=> $row['news_stop'],
					'user_id'			=> $row['user_id'],
					'date'				=> $row['news_date'],
					'previewimage'		=> "",
					'alias'				=> $this->routing->clean($row['news_headline'].''.$row['news_id']),
					'hits'				=> 0,
					'sort_id'			=> 0,
					'tags'				=> serialize(array()),
					'votes_count'		=> 0,
					'votes_sum'			=> 0,
					'last_edited'		=> $row['news_date'],
					'last_edited_user'	=> $row['user_id'],
					'page_objects'		=> serialize(array()),
					'hide_header'		=> 0,					
			));
		}
		$prefix = registry::get_const("table_prefix");
		
		$this->db->query("RENAME TABLE `__news` TO `!OBSOLETE_".$prefix."news`;");
		$this->db->query("RENAME TABLE `__news_categories` TO `!OBSOLETE_".$prefix."news_categories`;");
		
		//Migrate Infopages
		$sql = "SELECT * FROM __pages";
		$query = $this->db->query($sql);
		while ($row = $this->db->fetch_record($query)) {
							
			$this->db->query("INSERT INTO __articles :params", array(
					'title' 			=> $row['page_title'],
					'text'				=> $row['page_content'],
					'category'			=> 7,
					'featured'			=> 0,
					'comments'			=> intval($row['page_comments']),
					'votes'				=> intval($row['page_voting']),
					'published'			=> 1,
					'show_from'			=> '',
					'show_to'			=> '',
					'user_id'			=> $row['page_edit_user'],
					'date'				=> $row['page_edit_date'],
					'previewimage'		=> "",
					'alias'				=> $this->routing->clean($row['page_title'].''.$row['page_id']),
					'hits'				=> 0,
					'sort_id'			=> 0,
					'tags'				=> serialize(array()),
					'votes_count'		=> 0,
					'votes_sum'			=> 0,
					'last_edited'		=> $row['page_edit_date'],
					'last_edited_user'	=> $row['page_edit_user'],
					'page_objects'		=> serialize(array()),
					'hide_header'		=> 0,
			));
		}
		$this->db->query("RENAME TABLE `__pages` TO `!OBSOLETE_".$prefix."pages`;");
			
		//Update Colors
		$this->update_colors();
		
		//Clear cache
		$this->pdc->flush();
		
		return true;
	}
	
	private function update_colors(){
		//Update Colors
		foreach($this->fields2change as $dbtable=>$dbfields){
			foreach($dbfields['field'] as $dbfieldvalue){
				// now, lets change the values
				$sql	= 'SELECT '.$dbfieldvalue.' as mycolorvalue, '.$dbfields['id'].' as mycolorid FROM '.$dbtable.';';
				$query = $this->db->query($sql);
				$update = array();
				while ($row = $this->db->fetch_record($query)) {
					if(trim($row['mycolorvalue']) != ''){
						// check if the # is already in the value
						if (preg_match('/^#[a-f0-9]{6}$/i', $row['mycolorvalue'])) {
							continue;
						}else if (preg_match('/^[a-f0-9]{6}$/i', $row['mycolorvalue'])) {
							$sql = "UPDATE ".$dbtable." SET ".$dbfieldvalue." = '#".$row['mycolorvalue']."' WHERE ".$dbfields['id']." = '".$row['mycolorid']."';";
							$this->db->query($sql);
						}
					}
				}
			}
		}
	}

}
?>