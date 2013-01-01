#
# MySQL EQdkp Structure
#
# $Id$
#
# --------------------------------------------------------
### Configuration

DROP TABLE IF EXISTS __backup_cnf;
CREATE TABLE `__backup_cnf` (
	`config_name` varchar(255) COLLATE utf8_bin NOT NULL,
	`config_plugin` varchar(25) COLLATE utf8_bin NOT NULL DEFAULT 'core',
	`config_value` text COLLATE utf8_bin,
	PRIMARY KEY (`config_name`, `config_plugin`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

# --------------------------------------------------------
### Users and Permissions

DROP TABLE IF EXISTS __users;
CREATE TABLE `__users` (
	`user_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
	`username` varchar(30) COLLATE utf8_bin NOT NULL,
	`user_password` varchar(255) COLLATE utf8_bin NOT NULL,
	`user_email` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`user_login_key` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`user_alimit` smallint(4) NOT NULL DEFAULT '100',
	`user_climit` smallint(4) NOT NULL DEFAULT '100',
	`user_elimit` smallint(4) NOT NULL DEFAULT '100',
	`user_ilimit` smallint(4) NOT NULL DEFAULT '100',
	`user_nlimit` smallint(2) NOT NULL DEFAULT '10',
	`user_rlimit` smallint(4) NOT NULL DEFAULT '100',
	`user_date_time` varchar(10) COLLATE utf8_bin DEFAULT NULL,
	`user_date_short` varchar(20) COLLATE utf8_bin DEFAULT NULL,
	`user_date_long` varchar(20) COLLATE utf8_bin DEFAULT NULL,
	`user_style` tinyint(4) DEFAULT NULL,
	`user_lang` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`user_timezone` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`user_key` varchar(32) COLLATE utf8_bin DEFAULT NULL,
	`user_lastvisit` int(11) NOT NULL DEFAULT '0',
	`user_lastpage` varchar(255) COLLATE utf8_bin DEFAULT '',
	`user_registered` int(11) unsigned NOT NULL DEFAULT '0',
	`user_active` tinyint(1) NOT NULL DEFAULT 1,
	`custom_fields` text COLLATE utf8_bin,
	`plugin_settings` text COLLATE utf8_bin,
	`first_name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`last_name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`country` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`town` varchar(250) COLLATE utf8_bin DEFAULT NULL,
	`state` varchar(250) COLLATE utf8_bin DEFAULT NULL,
	`ZIP_code` int(11) DEFAULT NULL,
	`phone` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`cellphone` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`address` text COLLATE utf8_bin,
	`allvatar_nick` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`icq` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`skype` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`msn` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`irq` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`gender` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`birthday` BIGINT(10) NULL DEFAULT '0',
	`privacy_settings` text COLLATE utf8_bin,
	`rules` tinyint(3) unsigned DEFAULT '0',
	`auth_account` text COLLATE utf8_bin,
	`failed_login_attempts` INT(3) NOT NULL DEFAULT '0',
	`exchange_key` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
	`api_key` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`hide_nochar_info` TINYINT(1) NULL DEFAULT '0',
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
	`user_id` smallint(5) unsigned NOT NULL,
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
	PRIMARY KEY (`groups_user_id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __groups_users;
CREATE TABLE IF NOT EXISTS `__groups_users` (
	`group_id` int(22) NOT NULL,
	`user_id` int(22) NOT NULL
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __sessions;
CREATE TABLE `__sessions` (
	`session_id` varchar(40) COLLATE utf8_bin NOT NULL,
	`session_user_id` smallint(5) NOT NULL DEFAULT '-1',
	`session_last_visit` int(11) NOT NULL DEFAULT '0',
	`session_start` int(11) NOT NULL,
	`session_current` int(11) NOT NULL,
	`session_page` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '0',
	`session_ip` varchar(50) COLLATE utf8_bin NOT NULL,
	`session_browser` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`session_key` varchar(15) COLLATE utf8_bin NOT NULL DEFAULT '',
	`session_type` varchar(15) COLLATE utf8_bin NOT NULL DEFAULT '',
	`session_perm_id` smallint(5) NULL DEFAULT '-1',
	PRIMARY KEY (`session_id`),
	KEY `session_current` (`session_current`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __member_user;
CREATE TABLE `__member_user` (
	`member_id` smallint(5) unsigned NOT NULL,
	`user_id` smallint(5) unsigned NOT NULL,
	KEY `member_id` (`member_id`),
	KEY `user_id` (`user_id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

# --------------------------------------------------------
### Basic DKP tables

DROP TABLE IF EXISTS __adjustments;
CREATE TABLE `__adjustments` (
	`adjustment_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	`adjustment_value` float(11,2) DEFAULT NULL,
	`adjustment_date` int(11) NOT NULL DEFAULT '0',
	`member_id` smallint(5) unsigned NOT NULL,
	`adjustment_reason` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`adjustment_added_by` varchar(30) COLLATE utf8_bin NOT NULL,
	`adjustment_updated_by` varchar(30) COLLATE utf8_bin DEFAULT NULL,
	`adjustment_group_key` varchar(32) COLLATE utf8_bin DEFAULT NULL,
	`event_id` varchar(255) COLLATE utf8_bin NOT NULL,
	`raid_id` mediumint(8) unsigned DEFAULT NULL,
	PRIMARY KEY (`adjustment_id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __events;
CREATE TABLE `__events` (
	`event_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
	`event_name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`event_value` float(6,2) DEFAULT NULL,
	`event_added_by` varchar(30) COLLATE utf8_bin NOT NULL,
	`event_updated_by` varchar(30) COLLATE utf8_bin DEFAULT NULL,
	`event_icon` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	PRIMARY KEY (`event_id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __items;
CREATE TABLE `__items` (
	`item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`item_name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`member_id` smallint(5) unsigned NOT NULL,
	`raid_id` mediumint(8) unsigned NOT NULL,
	`item_value` float(10,2) DEFAULT NULL,
	`item_date` int(11) NOT NULL DEFAULT '0',
	`item_added_by` varchar(30) COLLATE utf8_bin NOT NULL,
	`item_updated_by` varchar(30) COLLATE utf8_bin DEFAULT NULL,
	`item_group_key` varchar(32) COLLATE utf8_bin DEFAULT NULL,
	`game_itemid` int(10) unsigned DEFAULT NULL,
	`itempool_id` int(11) unsigned NOT NULL,
	`item_color` VARCHAR(255) COLLATE utf8_bin DEFAULT NULL,
	PRIMARY KEY (`item_id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __members;
CREATE TABLE `__members` (
	`member_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
	`member_name` varchar(30) COLLATE utf8_bin NOT NULL,
	`member_status` tinyint(1) NOT NULL DEFAULT 1,
	`member_level` smallint(3) DEFAULT NULL,
	`member_race_id` smallint(3) unsigned NOT NULL DEFAULT '0',
	`member_class_id` smallint(3) unsigned NOT NULL DEFAULT '0',
	`member_rank_id` smallint(3) NOT NULL DEFAULT '0',
	`member_main_id` smallint(5) unsigned DEFAULT NULL,
	`member_creation_date` int(10) unsigned NOT NULL DEFAULT 0,
	`last_update` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`picture` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`notes` text COLLATE utf8_bin,
	`profiledata` text COLLATE utf8_bin NOT NULL,
	`requested_del` tinyint(1) NOT NULL DEFAULT 0,
	`require_confirm` tinyint(1) NOT NULL DEFAULT 0,
	`defaultrole` tinyint(2) NOT NULL DEFAULT '0',
	PRIMARY KEY (`member_id`),
	UNIQUE KEY `member_name` (`member_name`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __classcolors;
CREATE TABLE `__classcolors` (
	`template` int(3) NOT NULL,
	`class_id` int(2) NOT NULL,
	`color` varchar(7) COLLATE utf8_bin DEFAULT NULL,
	PRIMARY KEY (`class_id`, `template`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __member_ranks;
CREATE TABLE `__member_ranks` (
	`rank_id` smallint(5) unsigned NOT NULL,
	`rank_name` varchar(50) COLLATE utf8_bin NOT NULL,
	`rank_hide` tinyint(1) NOT NULL DEFAULT 0,
	`rank_prefix` varchar(75) COLLATE utf8_bin NOT NULL DEFAULT '',
	`rank_suffix` varchar(75) COLLATE utf8_bin NOT NULL DEFAULT '',
	`rank_sortid` smallint(5) unsigned NOT NULL DEFAULT 0,
	UNIQUE KEY `rank_id` (`rank_id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __member_profilefields;
CREATE TABLE `__member_profilefields` (
	`name` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`fieldtype` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`category` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`language` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`size` smallint(5) DEFAULT NULL,
	`image` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`visible` tinyint(1) NOT NULL DEFAULT 0,
	`enabled` tinyint(1) NOT NULL DEFAULT 0,
	`undeletable` tinyint(1) NOT NULL DEFAULT 0,
	`custom` tinyint(1) NOT NULL DEFAULT 0,
	`options` text COLLATE utf8_bin,
	PRIMARY KEY (`name`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __roles;
CREATE TABLE __roles (
	`c_index` mediumint(5) unsigned NOT NULL auto_increment,
	`role_id` mediumint(8) unsigned NOT NULL,
	`role_name` varchar(50) DEFAULT NULL,
	`role_classes` varchar(255) DEFAULT NULL,
	PRIMARY KEY (`c_index`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __raids;
CREATE TABLE `__raids` (
	`raid_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	`event_id` smallint(5) unsigned NOT NULL,
	`raid_date` int(11) NOT NULL DEFAULT '0',
	`raid_note` text COLLATE utf8_bin,
	`raid_value` float(6,2) NOT NULL DEFAULT '0.00',
	`raid_added_by` varchar(30) COLLATE utf8_bin NOT NULL,
	`raid_updated_by` varchar(30) COLLATE utf8_bin DEFAULT NULL,
	PRIMARY KEY (`raid_id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __raid_attendees;
CREATE TABLE `__raid_attendees` (
	`raid_id` mediumint(8) unsigned NOT NULL,
	`member_id` smallint(5) unsigned NOT NULL,
	KEY `raid_id` (`raid_id`),
	KEY `member_id` (`member_id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

# --------------------------------------------------------
### Multidkp & ItemPools

DROP TABLE IF EXISTS __multidkp ;
CREATE TABLE `__multidkp` (
	`multidkp_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`multidkp_name` varchar(255) COLLATE utf8_bin NOT NULL,
	`multidkp_desc` text COLLATE utf8_bin NOT NULL,
	PRIMARY KEY (`multidkp_id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __multidkp2event ;
CREATE TABLE `__multidkp2event` (
	`multidkp2event_multi_id` int(11) NOT NULL,
	`multidkp2event_event_id` smallint(5) NOT NULL,
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
### News

DROP TABLE IF EXISTS __news;
CREATE TABLE `__news` (
	`news_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
	`news_headline` varchar(255) COLLATE utf8_bin NOT NULL,
	`news_message` text COLLATE utf8_bin NOT NULL,
	`news_date` int(11) NOT NULL,
	`user_id` smallint(5) unsigned NOT NULL,
	`showRaids_id` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`extended_message` text COLLATE utf8_bin NOT NULL,
	`nocomments` tinyint(4) NOT NULL,
	`news_permissions` tinyint(4) NOT NULL,
	`news_flags` tinyint(4) NOT NULL DEFAULT '0',
	`news_category` int(10) unsigned DEFAULT '1',
	`news_start` varchar(11) COLLATE utf8_bin NOT NULL DEFAULT '',
	`news_stop` varchar(11) COLLATE utf8_bin NOT NULL DEFAULT '',
	PRIMARY KEY (`news_id`),
	KEY `user_id` (`user_id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __news_categories;
CREATE TABLE `__news_categories` (
	`category_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`category_name` text COLLATE utf8_bin,
	`category_icon` text COLLATE utf8_bin,
	`category_color` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	PRIMARY KEY (`category_id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

# --------------------------------------------------------
### Logging

DROP TABLE IF EXISTS __logs;
CREATE TABLE `__logs` (
	`log_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`log_date` int(11) NOT NULL DEFAULT '0',
	`log_value` text COLLATE utf8_bin NOT NULL,
	`log_ipaddress` varchar(40) COLLATE utf8_bin NOT NULL DEFAULT '',
	`log_sid` varchar(40) COLLATE utf8_bin NOT NULL DEFAULT '',
	`log_result` int(1) NOT NULL DEFAULT 1,
	`log_tag` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`log_plugin` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`log_flag` smallint(3) NOT NULL DEFAULT '0',
	`user_id` smallint(5) NOT NULL DEFAULT '0',
	`username` VARCHAR(30) COLLATE utf8_bin NOT NULL,
	PRIMARY KEY (`log_id`),
	KEY `user_id` (`user_id`),
	KEY `log_tag` (`log_tag`),
	KEY `log_ipaddress` (`log_ipaddress`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

# --------------------------------------------------------
### Styles

DROP TABLE IF EXISTS __styles;
CREATE TABLE `__styles` (
	`style_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
	`style_name` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
	`style_version` varchar(7) COLLATE utf8_bin DEFAULT NULL,
	`style_contact` varchar(100) COLLATE utf8_bin DEFAULT NULL,
	`style_author` varchar(100) COLLATE utf8_bin DEFAULT NULL,
	`enabled` enum('0','1') COLLATE utf8_bin NOT NULL DEFAULT '0',
	`template_path` varchar(30) COLLATE utf8_bin NOT NULL DEFAULT 'default',
	`body_background` varchar(6) COLLATE utf8_bin DEFAULT NULL,
	`body_link` varchar(6) COLLATE utf8_bin DEFAULT NULL,
	`body_link_style` varchar(30) COLLATE utf8_bin DEFAULT NULL,
	`body_hlink` varchar(6) COLLATE utf8_bin DEFAULT NULL,
	`body_hlink_style` varchar(30) COLLATE utf8_bin DEFAULT NULL,
	`header_link` varchar(6) COLLATE utf8_bin DEFAULT NULL,
	`header_link_style` varchar(30) COLLATE utf8_bin DEFAULT NULL,
	`header_hlink` varchar(6) COLLATE utf8_bin DEFAULT NULL,
	`header_hlink_style` varchar(30) COLLATE utf8_bin DEFAULT NULL,
	`tr_color1` varchar(6) COLLATE utf8_bin DEFAULT NULL,
	`tr_color2` varchar(6) COLLATE utf8_bin DEFAULT NULL,
	`th_color1` varchar(6) COLLATE utf8_bin DEFAULT NULL,
	`fontface1` varchar(60) COLLATE utf8_bin DEFAULT NULL,
	`fontface2` varchar(60) COLLATE utf8_bin DEFAULT NULL,
	`fontface3` varchar(60) COLLATE utf8_bin DEFAULT NULL,
	`fontsize1` tinyint(4) DEFAULT NULL,
	`fontsize2` tinyint(4) DEFAULT NULL,
	`fontsize3` tinyint(4) DEFAULT NULL,
	`fontcolor1` varchar(6) COLLATE utf8_bin DEFAULT NULL,
	`fontcolor2` varchar(6) COLLATE utf8_bin DEFAULT NULL,
	`fontcolor3` varchar(6) COLLATE utf8_bin DEFAULT NULL,
	`fontcolor_neg` varchar(6) COLLATE utf8_bin DEFAULT NULL,
	`fontcolor_pos` varchar(6) COLLATE utf8_bin DEFAULT NULL,
	`table_border_width` tinyint(3) DEFAULT NULL,
	`table_border_color` varchar(6) COLLATE utf8_bin DEFAULT NULL,
	`table_border_style` varchar(30) COLLATE utf8_bin DEFAULT NULL,
	`input_color` varchar(6) COLLATE utf8_bin DEFAULT NULL,
	`input_border_width` tinyint(3) DEFAULT NULL,
	`input_border_color` varchar(6) COLLATE utf8_bin DEFAULT NULL,
	`input_border_style` varchar(30) COLLATE utf8_bin DEFAULT NULL,
	`attendees_columns` enum('1','2','3','4','5','6','7','8','9','10') COLLATE utf8_bin NOT NULL DEFAULT '6',
	`logo_position` VARCHAR(6) NULL DEFAULT NULL,
	`background_img` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`css_file` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`use_db_vars` tinyint(1) unsigned DEFAULT NULL,
	`column_right_width` VARCHAR(20) COLLATE utf8_bin NULL DEFAULT '',
	`column_left_width` VARCHAR(20) COLLATE utf8_bin NULL DEFAULT '',
	`portal_width` VARCHAR(20) COLLATE utf8_bin NULL DEFAULT '',
	PRIMARY KEY (`style_id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

# --------------------------------------------------------
### Plugins

DROP TABLE IF EXISTS __plugins;
CREATE TABLE `__plugins` (
	`code` varchar(20) COLLATE utf8_bin NOT NULL,
	`status` tinyint(2) NOT NULL DEFAULT 0,
	`version` varchar(7) COLLATE utf8_bin NOT NULL,
	PRIMARY KEY (`code`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


DROP TABLE IF EXISTS __itemIDs;
CREATE TABLE `__itemIDs` (
	`itemID_id` int(11) NOT NULL AUTO_INCREMENT,
	`itemID_blizID` int(11) NOT NULL,
	`itemID_displayID` int(11) NOT NULL,
	`itemID_armorySlotID` int(11) NOT NULL,
	`itemID_wowheadSlotID` int(11) NOT NULL,
	PRIMARY KEY (`itemID_id`)
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
	`link_visibility` tinyint(3) unsigned NOT NULL DEFAULT '0',
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
	PRIMARY KEY (`id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __portal;
CREATE TABLE IF NOT EXISTS `__portal` (
	`id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
	`enabled` tinyint(1) NOT NULL DEFAULT 0,
	`settings` tinyint(1) NOT NULL DEFAULT 0,
	`path` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`contact` varchar(100) COLLATE utf8_bin DEFAULT NULL,
	`url` varchar(100) COLLATE utf8_bin DEFAULT NULL,
	`autor` varchar(100) COLLATE utf8_bin DEFAULT NULL,
	`version` varchar(7) COLLATE utf8_bin NOT NULL DEFAULT '',
	`position` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '0',
	`number` mediumint(8) DEFAULT NULL,
	`plugin` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`visibility` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT 'a:1:{i:0;i:0;}',
	`collapsable` tinyint(1) NOT NULL DEFAULT 1,
	`child` tinyint(1) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __pages;
CREATE TABLE IF NOT EXISTS `__pages` (
	`page_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	`page_title` varchar(255) COLLATE utf8_bin NOT NULL,
	`page_content` text COLLATE utf8_bin,
	`page_visibility` text COLLATE utf8_bin NOT NULL,
	`page_menu_link` varchar(255) COLLATE utf8_bin NOT NULL,
	`page_edit_user` smallint(5) NOT NULL,
	`page_edit_date` int(11) NOT NULL,
	`page_alias` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`page_comments` tinyint(4) DEFAULT '0',
	`page_voting` tinyint(4) DEFAULT '0',
	`page_votes` int(10) DEFAULT '0',
	`page_ratingpoints` int(10) DEFAULT '0',
	`page_voters` text COLLATE utf8_bin,
	`page_rating` int(10) DEFAULT '0',
	PRIMARY KEY (`page_id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

# --------------------------------------------------------
### Repository

DROP TABLE IF EXISTS __repository;
CREATE TABLE `__repository` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`plugin` varchar(255) COLLATE utf8_bin NOT NULL,
	`name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`date` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`author` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`shortdesc` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`version` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`category` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`level` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`changelog` text COLLATE utf8_bin,
	`build` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`dep_coreversion` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`rating` int(10) unsigned NOT NULL DEFAULT '0',
	`updated` varchar(255) COLLATE utf8_bin DEFAULT NULL,
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
	`creator` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
	`timestamp_start` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`timestamp_end` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`allday` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`private` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`visible` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`closed` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`extension` text COLLATE utf8_bin,
	`notes` TEXT COLLATE utf8_bin,
	`repeating` varchar(255) DEFAULT NULL,
	PRIMARY KEY (`id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __calendars;
CREATE TABLE `__calendars` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`color` VARCHAR(20) COLLATE utf8_bin DEFAULT NULL,
	`private` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`feed` VARCHAR(255) COLLATE utf8_bin DEFAULT NULL,
	`system` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`type` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __calendar_raid_guests;
CREATE TABLE `__calendar_raid_guests` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`calendar_events_id` int(10) unsigned NOT NULL DEFAULT '0',
	`name` VARCHAR(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`note` VARCHAR(255) COLLATE utf8_bin DEFAULT NULL,
	`timestamp_signup` int(10) UNSIGNED NOT NULL DEFAULT '0',
	`raidgroup` VARCHAR(255) COLLATE utf8_bin DEFAULT NULL,
	`class` int(10) UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __calendar_raid_attendees;
CREATE TABLE `__calendar_raid_attendees` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`calendar_events_id` int(10) unsigned NOT NULL DEFAULT '0',
	`member_id` int(10) unsigned NOT NULL DEFAULT '0',
	`member_role` int(10) unsigned NOT NULL DEFAULT '0',
	`signup_status` VARCHAR(255) COLLATE utf8_bin DEFAULT NULL,
	`status_changedby` int(10) unsigned NOT NULL DEFAULT '0',
	`note` VARCHAR(255) COLLATE utf8_bin DEFAULT NULL,
	`timestamp_signup` int(10) unsigned NOT NULL DEFAULT '0',
	`timestamp_change` int(10) unsigned NOT NULL DEFAULT '0',
	`raidgroup` int(10) unsigned NOT NULL DEFAULT '0',
	`random_value` int(10) unsigned NOT NULL DEFAULT '0',
	`signedbyadmin` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS __calendar_raid_templates;
CREATE TABLE `__calendar_raid_templates` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) COLLATE utf8_bin DEFAULT NULL,
	`tpldata` text COLLATE utf8_bin,
	PRIMARY KEY (`id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;