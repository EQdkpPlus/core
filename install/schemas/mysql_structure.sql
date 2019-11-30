#
# MySQL EQdkp Structure
#
# $Id$
#
# --------------------------------------------------------
### Configuration

DROP TABLE IF EXISTS __config;
CREATE TABLE `__config` (
	`config_name` varchar(120) COLLATE utf8_bin NOT NULL,
	`config_plugin` varchar(40) COLLATE utf8_bin NOT NULL DEFAULT 'core',
	`config_value` text COLLATE utf8_bin,
	PRIMARY KEY (`config_name`, `config_plugin`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

# --------------------------------------------------------
### Users and Permissions

DROP TABLE IF EXISTS __users;
CREATE TABLE `__users` (
	`user_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`username` varchar(30) COLLATE utf8_bin NOT NULL,
	`user_password` varchar(255) COLLATE utf8_bin NOT NULL,
	`user_email` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`user_login_key` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`user_rlimit` smallint(4) NOT NULL DEFAULT '100',
	`user_date_time` varchar(10) COLLATE utf8_bin DEFAULT NULL,
	`user_date_short` varchar(20) COLLATE utf8_bin DEFAULT NULL,
	`user_date_long` varchar(20) COLLATE utf8_bin DEFAULT NULL,
	`user_style` tinyint(4) DEFAULT NULL,
	`user_lang` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`user_timezone` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`user_email_confirmed` INT(2) NOT NULL DEFAULT '1',
	`user_email_confirmkey` varchar(32) COLLATE utf8_bin DEFAULT NULL,
	`user_temp_email` VARCHAR(255) NULL COLLATE 'utf8_bin',
	`user_lastvisit` int(11) NOT NULL DEFAULT '0',
	`user_lastpage` varchar(255) COLLATE utf8_bin DEFAULT '',
	`user_registered` int(11) unsigned NOT NULL DEFAULT '0',
	`user_active` tinyint(1) NOT NULL DEFAULT 1,
	`custom_fields` text COLLATE utf8_bin,
	`plugin_settings` text COLLATE utf8_bin,
	`privacy_settings` text COLLATE utf8_bin,
	`rules` tinyint(3) unsigned DEFAULT '0',
	`auth_account` text COLLATE utf8_bin,
	`failed_login_attempts` INT(3) NOT NULL DEFAULT '0',
	`exchange_key` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
	`hide_nochar_info` TINYINT(1) NULL DEFAULT '0',
	`notifications` TEXT NULL COLLATE 'utf8_bin',
	`awaymode_enabled` tinyint(1) NOT NULL DEFAULT 0,
	`awaymode_startdate` INT(11) NULL DEFAULT '0',
	`awaymode_enddate` INT(11) NULL DEFAULT '0',
	`awaymode_note` text COLLATE utf8_bin,
	PRIMARY KEY (`user_id`),
	UNIQUE KEY `username` (`username`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __auth_options;
CREATE TABLE `__auth_options` (
	`auth_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`auth_value` varchar(65) COLLATE utf8_bin NOT NULL,
	`auth_default` enum('N','Y') COLLATE utf8_bin NOT NULL DEFAULT 'N',
	PRIMARY KEY (`auth_id`),
	KEY `auth_value` (`auth_value`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __auth_users;
CREATE TABLE `__auth_users` (
	`user_id` int(11) unsigned NOT NULL,
	`auth_id` int(11) unsigned NOT NULL,
	`auth_setting` enum('N','Y') COLLATE utf8_bin NOT NULL DEFAULT 'N',
	KEY `auth_id` (`auth_id`),
	KEY `user_id` (`user_id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __auth_groups;
CREATE TABLE `__auth_groups` (
	`group_id` int(11) unsigned NOT NULL,
	`auth_id` int(11) unsigned NOT NULL,
	`auth_setting` enum('N','Y') COLLATE utf8_bin NOT NULL DEFAULT 'N',
	KEY `auth_id` (`auth_id`),
	KEY `group_id` (`group_id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __groups_user;
CREATE TABLE IF NOT EXISTS `__groups_user` (
	`groups_user_id` int(11) NOT NULL AUTO_INCREMENT,
	`groups_user_name` varchar(255) COLLATE utf8_bin NOT NULL,
	`groups_user_desc` varchar(255) COLLATE utf8_bin NOT NULL,
	`groups_user_deletable` tinyint(1) NOT NULL DEFAULT 0,
	`groups_user_default` tinyint(1) NOT NULL DEFAULT 0,
	`groups_user_hide` tinyint(1) NOT NULL DEFAULT 0,
	`groups_user_sortid` int(11) unsigned NOT NULL DEFAULT 0,
	`groups_user_team` tinyint(1) NOT NULL DEFAULT 0,
	PRIMARY KEY (`groups_user_id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __groups_users;
CREATE TABLE IF NOT EXISTS `__groups_users` (
	`group_id` int(11) NOT NULL,
	`user_id` int(11) NOT NULL,
	`grpleader` int(11) NOT NULL DEFAULT 0
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __sessions;
CREATE TABLE `__sessions` (
	`session_id` varchar(40) COLLATE utf8_bin NOT NULL,
	`session_user_id` int(11) NOT NULL DEFAULT '-1',
	`session_last_visit` int(11) NOT NULL DEFAULT '0',
	`session_start` int(11) NOT NULL,
	`session_current` int(11) NOT NULL,
	`session_page` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '0',
	`session_ip` varchar(50) COLLATE utf8_bin NOT NULL,
	`session_browser` TEXT COLLATE 'utf8_bin',
	`session_key` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
	`session_type` varchar(15) COLLATE utf8_bin NOT NULL DEFAULT '',
	`session_perm_id` int(11) NULL DEFAULT '-1',
	`session_failed_logins` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`session_vars` MEDIUMTEXT COLLATE 'utf8_bin' NULL,
	PRIMARY KEY (`session_id`),
	KEY `session_current` (`session_current`),
	KEY `session_start` (`session_start`),
	KEY `session_user_id` (`session_user_id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __member_user;
CREATE TABLE `__member_user` (
	`member_id` int(11) unsigned NOT NULL,
	`user_id` int(11) unsigned NOT NULL,
	KEY `member_id` (`member_id`),
	KEY `user_id` (`user_id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

# --------------------------------------------------------
### Basic DKP tables

DROP TABLE IF EXISTS __adjustments;
CREATE TABLE `__adjustments` (
	`adjustment_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	`adjustment_value` float(11,2) DEFAULT NULL,
	`adjustment_apa_value` TEXT NULL COLLATE 'utf8_bin',
	`adjustment_date` int(11) NOT NULL DEFAULT '0',
	`member_id` int(11) unsigned NOT NULL,
	`adjustment_reason` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`adjustment_added_by` varchar(30) COLLATE utf8_bin NOT NULL,
	`adjustment_updated_by` varchar(30) COLLATE utf8_bin DEFAULT NULL,
	`adjustment_group_key` varchar(32) COLLATE utf8_bin DEFAULT NULL,
	`event_id` varchar(255) COLLATE utf8_bin NOT NULL,
	`raid_id` mediumint(8) unsigned DEFAULT NULL,
	PRIMARY KEY (`adjustment_id`),
	INDEX `adjustment_value` (`adjustment_value`),
	INDEX `event_id` (`event_id`),
	INDEX `member_id` (`member_id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __events;
CREATE TABLE `__events` (
	`event_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`event_name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`event_value` float(6,2) DEFAULT NULL,
	`event_added_by` varchar(30) COLLATE utf8_bin NOT NULL,
	`event_updated_by` varchar(30) COLLATE utf8_bin DEFAULT NULL,
	`event_icon` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`default_itempool` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`event_show_profile` TINYINT(1) UNSIGNED NULL DEFAULT '1',
	PRIMARY KEY (`event_id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __items;
CREATE TABLE `__items` (
	`item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`item_name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`member_id` int(11) unsigned NOT NULL,
	`raid_id` mediumint(8) unsigned NOT NULL,
	`item_value` float(10,2) DEFAULT NULL,
	`item_apa_value` TEXT NULL COLLATE 'utf8_bin',
	`item_date` int(11) NOT NULL DEFAULT '0',
	`item_added_by` varchar(30) COLLATE utf8_bin NOT NULL,
	`item_updated_by` varchar(30) COLLATE utf8_bin DEFAULT NULL,
	`item_group_key` varchar(32) COLLATE utf8_bin DEFAULT NULL,
	`game_itemid` varchar(50) COLLATE utf8_bin DEFAULT NULL,
	`itempool_id` int(11) unsigned NOT NULL,
	`item_color` VARCHAR(255) COLLATE utf8_bin DEFAULT NULL,
	PRIMARY KEY (`item_id`),
	INDEX `member_id` (`member_id`),
	INDEX `item_value` (`item_value`),
	INDEX `itempool_id` (`itempool_id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __members;
CREATE TABLE `__members` (
	`member_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`member_name` varchar(30) COLLATE utf8_bin NOT NULL,
	`member_status` tinyint(1) NOT NULL DEFAULT 1,
	`member_rank_id` smallint(3) NOT NULL DEFAULT '0',
	`member_main_id` int(11) unsigned DEFAULT NULL,
	`member_creation_date` int(10) unsigned NOT NULL DEFAULT 0,
	`last_update` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`picture` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`notes` text COLLATE utf8_bin,
	`profiledata` text COLLATE utf8_bin NOT NULL,
	`requested_del` tinyint(1) NOT NULL DEFAULT 0,
	`require_confirm` tinyint(1) NOT NULL DEFAULT 0,
	`defaultrole` tinyint(2) NOT NULL DEFAULT '0',
	`points` TEXT NULL COLLATE 'utf8_bin',
	`points_apa` TEXT NULL COLLATE 'utf8_bin',
	PRIMARY KEY (`member_id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __member_points;
CREATE TABLE `__member_points` (
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`time` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`member_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`mdkp_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`current` FLOAT(11,2) NOT NULL DEFAULT '0.00',
	`earned` FLOAT(11,2) NOT NULL DEFAULT '0.00',
	`spent` FLOAT(11,2) NOT NULL DEFAULT '0.00',
	`adjustments` FLOAT(11,2) NOT NULL DEFAULT '0.00',
	`misc` TEXT NULL COLLATE 'utf8_bin',
	`type` VARCHAR(10) NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
)
DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __classcolors;
CREATE TABLE `__classcolors` (
	`template` int(3) NOT NULL,
	`class_id` int(2) NOT NULL,
	`color` varchar(7) COLLATE utf8_bin DEFAULT NULL,
	PRIMARY KEY (`class_id`, `template`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __member_ranks;
CREATE TABLE `__member_ranks` (
	`rank_id` int(11) unsigned NOT NULL,
	`rank_name` varchar(50) COLLATE utf8_bin NOT NULL,
	`rank_hide` tinyint(1) NOT NULL DEFAULT 0,
	`rank_prefix` varchar(75) COLLATE utf8_bin NOT NULL DEFAULT '',
	`rank_suffix` varchar(75) COLLATE utf8_bin NOT NULL DEFAULT '',
	`rank_sortid` int(11) unsigned NOT NULL DEFAULT 0,
	`rank_default`  tinyint(1) NOT NULL DEFAULT 0,
	`rank_icon` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	UNIQUE KEY `rank_id` (`rank_id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __member_profilefields;
CREATE TABLE `__member_profilefields` (
	`id` INT(11) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`type` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`category` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`lang` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`size` int(11) DEFAULT NULL,
	`image` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`sort` int(11) NOT NULL DEFAULT 1,
	`enabled` tinyint(1) NOT NULL DEFAULT 0,
	`undeletable` tinyint(1) NOT NULL DEFAULT 0,
	`custom` tinyint(1) NOT NULL DEFAULT 0,
	`data` text COLLATE utf8_bin,
	`options_language` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __roles;
CREATE TABLE __roles (
	`c_index` mediumint(5) unsigned NOT NULL auto_increment,
	`role_id` mediumint(8) unsigned NOT NULL,
	`role_name` varchar(50) DEFAULT NULL,
	`role_classes` varchar(255) DEFAULT NULL,
	`role_icon` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	PRIMARY KEY (`c_index`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __raids;
CREATE TABLE `__raids` (
	`raid_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	`event_id` int(11) unsigned NOT NULL,
	`raid_date` int(11) NOT NULL DEFAULT '0',
	`raid_note` text COLLATE utf8_bin,
	`raid_value` float(6,2) NOT NULL DEFAULT '0.00',
	`raid_apa_value` TEXT NULL COLLATE 'utf8_bin',
	`raid_added_by` varchar(30) COLLATE utf8_bin NOT NULL,
	`raid_updated_by` varchar(30) COLLATE utf8_bin DEFAULT NULL,
	`raid_additional_data` TEXT NULL DEFAULT NULL COLLATE 'utf8_bin',
	PRIMARY KEY (`raid_id`),
	INDEX `raid_value` (`raid_value`),
	INDEX `event_id` (`event_id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __raid_attendees;
CREATE TABLE `__raid_attendees` (
	`raid_id` mediumint(8) unsigned NOT NULL,
	`member_id` int(11) unsigned NOT NULL,
	KEY `raid_id` (`raid_id`),
	KEY `member_id` (`member_id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __groups_raid;
CREATE TABLE `__groups_raid` (
	`groups_raid_id` int(11) NOT NULL AUTO_INCREMENT,
	`groups_raid_name` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`groups_raid_color` varchar(10) COLLATE utf8_bin DEFAULT NULL,
	`groups_raid_desc` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`groups_raid_system` tinyint(1) NOT NULL DEFAULT '0',
	`groups_raid_default` tinyint(1) NOT NULL DEFAULT '0',
	`groups_raid_sortid` int(11) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`groups_raid_id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __groups_raid_members;
CREATE TABLE `__groups_raid_members` (
	`group_id` int(22) NOT NULL,
	`user_id` int(22) NOT NULL,
	`grpleader` int(1) NOT NULL DEFAULT '0'
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

# --------------------------------------------------------
### Multidkp & ItemPools

DROP TABLE IF EXISTS __multidkp ;
CREATE TABLE `__multidkp` (
	`multidkp_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`multidkp_name` varchar(255) COLLATE utf8_bin NOT NULL,
	`multidkp_desc` text COLLATE utf8_bin NOT NULL,
	`multidkp_sortid` int(11) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`multidkp_id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __multidkp2event ;
CREATE TABLE `__multidkp2event` (
	`multidkp2event_multi_id` int(11) NOT NULL,
	`multidkp2event_event_id` int(11) NOT NULL,
	`multidkp2event_no_attendance` int(1) NOT NULL DEFAULT 0,
	PRIMARY KEY (`multidkp2event_multi_id`, `multidkp2event_event_id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __itempool;
CREATE TABLE IF NOT EXISTS `__itempool` (
	`itempool_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`itempool_name` varchar(255) COLLATE utf8_bin NOT NULL,
	`itempool_desc` text COLLATE utf8_bin NOT NULL,
	PRIMARY KEY (`itempool_id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __multidkp2itempool;
CREATE TABLE `__multidkp2itempool` (
	`multidkp2itempool_itempool_id` int(11) unsigned NOT NULL,
	`multidkp2itempool_multi_id` int(11) unsigned NOT NULL,
	PRIMARY KEY (`multidkp2itempool_itempool_id`, `multidkp2itempool_multi_id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


# --------------------------------------------------------
### Logging

DROP TABLE IF EXISTS __logs;
CREATE TABLE `__logs` (
	`log_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`log_date` int(11) NOT NULL DEFAULT '0',
	`log_value` MEDIUMTEXT COLLATE utf8_bin NOT NULL,
	`log_ipaddress` varchar(40) COLLATE utf8_bin NOT NULL DEFAULT '',
	`log_sid` varchar(40) COLLATE utf8_bin NOT NULL DEFAULT '',
	`log_result` int(1) NOT NULL DEFAULT 1,
	`log_tag` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`log_plugin` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`log_flag` smallint(3) NOT NULL DEFAULT '0',
	`user_id` int(11) NOT NULL DEFAULT '0',
	`username` VARCHAR(30) COLLATE utf8_bin NOT NULL,
	`log_record` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`log_record_id` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`trace` TEXT NULL COLLATE 'utf8_bin',
	PRIMARY KEY (`log_id`),
	KEY `user_id` (`user_id`),
	KEY `log_tag` (`log_tag`),
	KEY `log_ipaddress` (`log_ipaddress`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

# --------------------------------------------------------
### Styles

DROP TABLE IF EXISTS __styles;
CREATE TABLE `__styles` (
	`style_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`style_name` VARCHAR(100) NOT NULL DEFAULT '' COLLATE 'utf8_bin',
	`style_version` VARCHAR(20) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`style_contact` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`style_author` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`enabled` ENUM('0','1') NOT NULL DEFAULT '0' COLLATE 'utf8_bin',
	`template_path` VARCHAR(30) NOT NULL DEFAULT 'default' COLLATE 'utf8_bin',
	`attendees_columns` ENUM('1','2','3','4','5','6','7','8','9','10') NOT NULL DEFAULT '6' COLLATE 'utf8_bin',
	`logo_position` VARCHAR(6) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`favicon_img` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`banner_img` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`background_img` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`column_right_width` VARCHAR(20) NULL DEFAULT '' COLLATE 'utf8_bin',
	`column_left_width` VARCHAR(20) NULL DEFAULT '' COLLATE 'utf8_bin',
	`portal_width` VARCHAR(20) NULL DEFAULT '' COLLATE 'utf8_bin',
	`background_pos` VARCHAR(20) NULL DEFAULT 'normal' COLLATE 'utf8_bin',
	`background_type` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
	`body_background_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`body_font_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`body_font_size` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`body_font_family` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`body_link_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`body_link_color_hover` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`body_link_decoration` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`container_background_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`container_border_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`content_background_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`content_font_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`content_font_color_headings` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`content_link_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`content_link_color_hover` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`content_border_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`content_accent_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`userarea_background_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`userarea_font_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`userarea_link_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`userarea_link_color_hover` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`table_th_background_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`table_th_font_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`table_tr_font_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`table_tr_background_color1` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`table_tr_background_color2` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`table_tr_background_color_hover` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`table_border_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`menu_background_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`menu_font_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`menu_item_background_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`menu_item_background_color_hover` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`menu_item_font_color_hover` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`sidebar_background_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`sidebar_font_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`sidebar_border_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`button_background_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`button_font_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`button_border_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`button_background_color_hover` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`button_font_color_hover` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`button_border_color_hover` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`input_background_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`input_border_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`input_font_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`input_background_color_active` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`input_border_color_active` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`input_font_color_active` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`content_contrast_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`content_contrast_background_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`content_contrast_border_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`content_positive_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`content_negative_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`content_neutral_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`content_highlight_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`misc_color1` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`misc_color2` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`misc_color3` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`misc_text1` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`misc_text2` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`misc_text3` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`additional_less` TEXT NULL COLLATE 'utf8_bin',
	`additional_fields` TEXT NULL COLLATE 'utf8_bin',
	`editor_theme` VARCHAR(255) NULL DEFAULT 'lightgray' COLLATE 'utf8_bin',
	PRIMARY KEY (`style_id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

# --------------------------------------------------------
### Plugins

DROP TABLE IF EXISTS __plugins;
CREATE TABLE `__plugins` (
	`code` varchar(20) COLLATE utf8_bin NOT NULL,
	`status` tinyint(2) NOT NULL DEFAULT 0,
	`version` varchar(20) COLLATE utf8_bin NOT NULL,
	PRIMARY KEY (`code`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

# --------------------------------------------------------
### Portal

DROP TABLE IF EXISTS __links ;
CREATE TABLE IF NOT EXISTS `__links` (
	`link_id` int(12) NOT NULL AUTO_INCREMENT,
	`link_url` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`link_name` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`link_window` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`link_menu` tinyint(4) NOT NULL DEFAULT '0',
	`link_sortid` int(11) NOT NULL DEFAULT '0',
	`link_visibility` TEXT COLLATE 'utf8_bin' NOT NULL,
	`link_height` int(12) NOT NULL DEFAULT '4024',
	PRIMARY KEY (`link_id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __comments;
CREATE TABLE IF NOT EXISTS `__comments` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`attach_id` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`userid` int(11) unsigned NOT NULL,
	`date` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`text` text COLLATE utf8_bin,
	`page` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`reply_to` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __portal;
CREATE TABLE IF NOT EXISTS `__portal` (
	`id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
	`path` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`contact` varchar(100) COLLATE utf8_bin DEFAULT NULL,
	`url` varchar(100) COLLATE utf8_bin DEFAULT NULL,
	`autor` varchar(100) COLLATE utf8_bin DEFAULT NULL,
	`version` varchar(20) COLLATE utf8_bin NOT NULL DEFAULT '',
	`plugin` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`visibility` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT 'a:1:{i:0;i:0;}',
	`collapsable` tinyint(1) NOT NULL DEFAULT 1,
	`child` tinyint(1) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __portal_blocks;
CREATE TABLE `__portal_blocks` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`wide_content` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __portal_layouts;
CREATE TABLE `__portal_layouts` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`blocks` TEXT COLLATE 'utf8_bin' NOT NULL,
	`modules` TEXT COLLATE 'utf8_bin' NOT NULL,
	`routes` TEXT COLLATE 'utf8_bin' NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


# --------------------------------------------------------
### Articles
DROP TABLE IF EXISTS __articles;
CREATE TABLE `__articles` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`title` TEXT NOT NULL COLLATE 'utf8_bin',
	`text` MEDIUMTEXT COLLATE 'utf8_bin' NOT NULL,
	`category` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`featured` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
	`comments` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
	`votes` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
	`published` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
	`show_from` VARCHAR(11) COLLATE utf8_bin NOT NULL DEFAULT '',
	`show_to` VARCHAR(11) COLLATE utf8_bin NOT NULL DEFAULT '',
	`user_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
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
	`last_edited_user` INT(11) NOT NULL DEFAULT '0',
	`page_objects` TEXT NULL DEFAULT NULL,
	`hide_header` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
	`index` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`undeletable` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __article_categories;
CREATE TABLE `__article_categories` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` TEXT NOT NULL COLLATE 'utf8_bin',
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
) DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


# --------------------------------------------------------
### Repository

DROP TABLE IF EXISTS __repository;
CREATE TABLE `__repository` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`plugin` varchar(255) COLLATE utf8_bin NOT NULL,
	`plugin_id` INT(1) NULL,
	`name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`date` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`author` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`description` text COLLATE utf8_bin,
	`version` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`version_ext` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`category` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`level` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`changelog` text COLLATE utf8_bin,
	`dep_php` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`dep_coreversion` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`rating` int(10) unsigned NOT NULL DEFAULT '0',
	`updated` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`bugtracker_url` TEXT COLLATE utf8_bin NULL,
	`tags` TEXT COLLATE utf8_bin NULL,
	PRIMARY KEY (`id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


# --------------------------------------------------------
### Calendar

DROP TABLE IF EXISTS __calendar_events;
CREATE TABLE `__calendar_events` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`cloneid` int(10) unsigned NOT NULL DEFAULT '0',
	`calendar_id` int(10) unsigned NOT NULL DEFAULT '0',
	`name` VARCHAR(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`creator` int(11) UNSIGNED NOT NULL DEFAULT '0',
	`timestamp_start` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`timestamp_end` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`allday` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`private` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`visible` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`closed` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`extension` text COLLATE utf8_bin,
	`notes` TEXT COLLATE utf8_bin,
	`repeating` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`timezone` varchar(255) DEFAULT NULL,
	PRIMARY KEY (`id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __calendars;
CREATE TABLE `__calendars` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`color` VARCHAR(10) COLLATE utf8_bin DEFAULT NULL,
	`private` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`feed` VARCHAR(255) COLLATE utf8_bin DEFAULT NULL,
	`system` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`type` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`restricted` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`affiliation` VARCHAR(30) COLLATE utf8_bin  NOT NULL DEFAULT 'user',
	`permissions` VARCHAR(255) COLLATE utf8_bin NOT NULL DEFAULT 'all',
	PRIMARY KEY (`id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __calendar_raid_guests;
CREATE TABLE `__calendar_raid_guests` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`calendar_events_id` int(10) unsigned NOT NULL DEFAULT '0',
	`name` VARCHAR(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`email` VARCHAR(255) COLLATE utf8_bin DEFAULT NULL,
	`note` VARCHAR(255) COLLATE utf8_bin DEFAULT NULL,
	`timestamp_signup` int(10) UNSIGNED NOT NULL DEFAULT '0',
	`raidgroup` int(10) unsigned NOT NULL DEFAULT '0',
	`class` int(10) UNSIGNED NOT NULL DEFAULT '0',
	`creator` int(10) UNSIGNED NOT NULL DEFAULT '0',
	`status` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
	`role` int(10) UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __calendar_raid_attendees;
CREATE TABLE `__calendar_raid_attendees` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`calendar_events_id` int(10) unsigned NOT NULL DEFAULT '0',
	`member_id` int(10) unsigned NOT NULL DEFAULT '0',
	`member_role` int(10) unsigned NOT NULL DEFAULT '0',
	`signup_status` VARCHAR(255) COLLATE utf8_bin DEFAULT NULL,
	`status_changedby` int(11) NOT NULL DEFAULT '0',
	`note` VARCHAR(255) COLLATE utf8_bin DEFAULT NULL,
	`timestamp_signup` int(10) unsigned NOT NULL DEFAULT '0',
	`timestamp_change` int(10) unsigned NOT NULL DEFAULT '0',
	`raidgroup` int(10) unsigned NOT NULL DEFAULT '0',
	`random_value` int(10) unsigned NOT NULL DEFAULT '0',
	`signedbyadmin` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE __calendar_raid_attendees ADD UNIQUE(calendar_events_id, member_id);

DROP TABLE IF EXISTS __calendar_raid_templates;
CREATE TABLE `__calendar_raid_templates` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) COLLATE utf8_bin DEFAULT NULL,
	`tpldata` text COLLATE utf8_bin,
	PRIMARY KEY (`id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __groups_raid_members;
CREATE TABLE `__groups_raid_members` (
	`group_id` int(22) NOT NULL,
	`member_id` int(22) NOT NULL,
	`grpleader` int(1) NOT NULL DEFAULT '0'
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __user_profilefields;
CREATE TABLE `__user_profilefields` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) NOT NULL COLLATE 'utf8_bin',
	`lang_var` VARCHAR(255) NOT NULL COLLATE 'utf8_bin',
	`type` VARCHAR(30) NOT NULL,
	`length` INT(10) UNSIGNED NOT NULL DEFAULT '30',
	`minlength` INT(10) UNSIGNED NOT NULL DEFAULT '1',
	`validation` TEXT NOT NULL COLLATE 'utf8_bin',
	`required` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`show_on_registration` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`enabled` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`sort_order` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`is_contact` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
	`contact_url` TEXT NULL COLLATE 'utf8_bin',
	`icon_or_image` TEXT NULL COLLATE 'utf8_bin',
	`bridge_field` TEXT NULL COLLATE 'utf8_bin',
	`options` TEXT NULL COLLATE 'utf8_bin',
	`editable` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
	`example` VARCHAR(255) NULL COLLATE 'utf8_bin',
	PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __notification_types;
CREATE TABLE `__notification_types` (
	`id` VARCHAR(50) NOT NULL COLLATE 'utf8_bin',
	`name` VARCHAR(50) NOT NULL COLLATE 'utf8_bin',
	`category` VARCHAR(50) NULL DEFAULT NULL,
	`prio` INT(11) NOT NULL DEFAULT '0',
	`default` VARCHAR(50) NOT NULL DEFAULT '0',
	`group` TINYINT(4) NOT NULL DEFAULT '0',
	`group_name` VARCHAR(80) NULL DEFAULT NULL,
	`group_at` INT(5) NOT NULL DEFAULT '0',
	`icon` VARCHAR(50) NULL DEFAULT NULL,
	`default_method` VARCHAR(50) NULL DEFAULT '0' COLLATE 'utf8_bin',
	PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __notifications;
CREATE TABLE `__notifications` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`type` VARCHAR(50) NOT NULL,
	`user_id` INT(11) NOT NULL,
	`time` INT(11) NOT NULL,
	`read` TINYINT(4) NOT NULL DEFAULT '0',
	`username` TEXT NULL,
	`dataset_id` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`link` TEXT NULL,
	`additional_data` TEXT NULL,
	PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __cronjobs;
CREATE TABLE `__cronjobs` (
	`id` VARCHAR(100) NOT NULL COLLATE 'utf8_bin',
	`start_time` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`repeat` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`repeat_type` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`repeat_interval` INT(11) UNSIGNED NOT NULL,
	`extern` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`ajax` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`delay` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`multiple` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`active` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`editable` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`path` VARCHAR(255) NOT NULL COLLATE 'utf8_bin',
	`params` TEXT NULL COLLATE 'utf8_bin',
	`description` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`last_run` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`next_run` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
