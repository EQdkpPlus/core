#
# MySQL EQdkp Structure
#
# $Id$
#
# --------------------------------------------------------
### Configuration

DROP TABLE IF EXISTS eqdkp_backup_cnf;
CREATE TABLE `eqdkp_backup_cnf` (
	`config_name` varchar(255) COLLATE utf8_bin NOT NULL,
	`config_plugin` varchar(255) COLLATE utf8_bin,
	`config_value` text COLLATE utf8_bin,
	PRIMARY KEY (`config_name`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

# --------------------------------------------------------
### Users and Permissions

DROP TABLE IF EXISTS eqdkp_users;
CREATE TABLE `eqdkp_users` (
	`user_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
	`username` varchar(30) COLLATE utf8_bin NOT NULL,
	`user_password` varchar(128) COLLATE utf8_bin NOT NULL,
	`user_email` varchar(100) COLLATE utf8_bin DEFAULT NULL,
	`user_alimit` smallint(4) NOT NULL DEFAULT '100',
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
	`user_lastpage` varchar(100) COLLATE utf8_bin DEFAULT '',
	`user_registered` int(11) unsigned NOT NULL DEFAULT '0',
	`user_active` enum('0','1') COLLATE utf8_bin NOT NULL DEFAULT '1',
	`user_newpassword` varchar(128) COLLATE utf8_bin NOT NULL,
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
	`birthday` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`privacy_settings` blob,
	`rules` tinyint(3) unsigned DEFAULT '0',
	PRIMARY KEY (`user_id`),
	UNIQUE KEY `username` (`username`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS eqdkp_auth_options;
CREATE TABLE `eqdkp_auth_options` (
	`auth_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`auth_value` varchar(65) COLLATE utf8_bin NOT NULL,
	`auth_default` enum('N','Y') COLLATE utf8_bin NOT NULL DEFAULT 'N',
	PRIMARY KEY (`auth_id`),
	KEY `auth_value` (`auth_value`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS eqdkp_auth_users;
CREATE TABLE `eqdkp_auth_users` (
	`user_id` smallint(5) unsigned NOT NULL,
	`auth_id` int(11) unsigned NOT NULL,
	`auth_setting` enum('N','Y') COLLATE utf8_bin NOT NULL DEFAULT 'N',
	KEY `auth_id` (`auth_id`),
	KEY `user_id` (`user_id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS eqdkp_auth_groups;
CREATE TABLE `eqdkp_auth_groups` (
	`group_id` int(11) unsigned NOT NULL,
	`auth_id` int(11) unsigned NOT NULL,
	`auth_setting` enum('N','Y') COLLATE utf8_bin NOT NULL DEFAULT 'N',
	KEY `auth_id` (`auth_id`),
	KEY `group_id` (`group_id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS eqdkp_groups_user;
CREATE TABLE IF NOT EXISTS `eqdkp_groups_user` (
	`groups_user_id` int(11) NOT NULL AUTO_INCREMENT,
	`groups_user_name` varchar(255) COLLATE utf8_bin NOT NULL,
	`groups_user_desc` varchar(255) COLLATE utf8_bin NOT NULL,
	`groups_user_deletable` enum('0','1') COLLATE utf8_bin NOT NULL DEFAULT '0',
	`groups_user_default` enum('0','1') COLLATE utf8_bin NOT NULL DEFAULT '0',
	`groups_user_hide` enum('0','1') COLLATE utf8_bin NOT NULL DEFAULT '0',
	PRIMARY KEY (`groups_user_id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS eqdkp_groups_users;
CREATE TABLE IF NOT EXISTS `eqdkp_groups_users` (
	`group_id` int(22) NOT NULL,
	`user_id` int(22) NOT NULL,
	`status` int(2) NOT NULL
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS eqdkp_sessions;
CREATE TABLE `eqdkp_sessions` (
	`session_id` varchar(32) COLLATE utf8_bin NOT NULL,
	`session_user_id` smallint(5) NOT NULL DEFAULT '-1',
	`session_last_visit` int(11) NOT NULL DEFAULT '0',
	`session_start` int(11) NOT NULL,
	`session_current` int(11) NOT NULL,
	`session_page` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '0',
	`session_ip` varchar(15) COLLATE utf8_bin NOT NULL,
	`session_browser` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	PRIMARY KEY (`session_id`),
	KEY `session_current` (`session_current`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS eqdkp_member_user;
CREATE TABLE `eqdkp_member_user` (
	`member_id` smallint(5) unsigned NOT NULL,
	`user_id` smallint(5) unsigned NOT NULL,
	KEY `member_id` (`member_id`),
	KEY `user_id` (`user_id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

# --------------------------------------------------------
### Basic DKP tables

DROP TABLE IF EXISTS eqdkp_adjustments;
CREATE TABLE `eqdkp_adjustments` (
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

DROP TABLE IF EXISTS eqdkp_events;
CREATE TABLE `eqdkp_events` (
	`event_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
	`event_name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`event_value` float(6,2) DEFAULT NULL,
	`event_added_by` varchar(30) COLLATE utf8_bin NOT NULL,
	`event_updated_by` varchar(30) COLLATE utf8_bin DEFAULT NULL,
	`event_icon` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	PRIMARY KEY (`event_id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS eqdkp_items;
CREATE TABLE `eqdkp_items` (
	`item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`item_name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`member_id` smallint(5) unsigned NOT NULL,
	`raid_id` mediumint(8) unsigned NOT NULL,
	`item_value` float(6,2) DEFAULT NULL,
	`item_date` int(11) NOT NULL DEFAULT '0',
	`item_added_by` varchar(30) COLLATE utf8_bin NOT NULL,
	`item_updated_by` varchar(30) COLLATE utf8_bin DEFAULT NULL,
	`item_group_key` varchar(32) COLLATE utf8_bin DEFAULT NULL,
	`game_itemid` int(10) unsigned DEFAULT NULL,
	`itempool_id` int(11) unsigned NOT NULL,
	`item_color` VARCHAR(20) COLLATE utf8_bin DEFAULT NULL,
	PRIMARY KEY (`item_id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS eqdkp_members;
CREATE TABLE `eqdkp_members` (
	`member_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
	`member_name` varchar(30) COLLATE utf8_bin NOT NULL,
	`member_status` enum('0','1') COLLATE utf8_bin NOT NULL DEFAULT '1',
	`member_level` smallint(3) DEFAULT NULL,
	`member_race_id` smallint(3) unsigned NOT NULL DEFAULT '0',
	`member_class_id` smallint(3) unsigned NOT NULL DEFAULT '0',
	`member_rank_id` smallint(3) NOT NULL DEFAULT '0',
	`member_xml` blob,
	`member_main_id` smallint(5) unsigned DEFAULT NULL,
	`last_update` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`picture` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`notes` text COLLATE utf8_bin,
	`profiledata` blob NOT NULL,
	`requested_del` enum('0','1') COLLATE utf8_bin NOT NULL,
	`require_confirm` enum('0','1') COLLATE utf8_bin NOT NULL,
	PRIMARY KEY (`member_id`),
	UNIQUE KEY `member_name` (`member_name`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS eqdkp_classcolors;
CREATE TABLE `eqdkp_classcolors` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`template` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`class_id` tinyint(2) DEFAULT NULL,
	`color` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	PRIMARY KEY (`id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS eqdkp_member_ranks;
CREATE TABLE `eqdkp_member_ranks` (
	`rank_id` smallint(5) unsigned NOT NULL,
	`rank_name` varchar(50) COLLATE utf8_bin NOT NULL,
	`rank_hide` enum('0','1') COLLATE utf8_bin NOT NULL DEFAULT '0',
	`rank_prefix` varchar(75) COLLATE utf8_bin NOT NULL DEFAULT '',
	`rank_suffix` varchar(75) COLLATE utf8_bin NOT NULL DEFAULT '',
	UNIQUE KEY `rank_id` (`rank_id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS eqdkp_member_profilefields;
CREATE TABLE `eqdkp_member_profilefields` (
	`name` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`fieldtype` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`category` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`language` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`size` smallint(5) DEFAULT NULL,
	`image` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`visible` enum('0','1') COLLATE utf8_bin NOT NULL DEFAULT '0',
	`enabled` enum('0','1') COLLATE utf8_bin NOT NULL DEFAULT '0',
	`undeletable` enum('0','1') COLLATE utf8_bin NOT NULL DEFAULT '0',
	`custom` enum('0','1') COLLATE utf8_bin NOT NULL DEFAULT '0',
	`options` blob,
	PRIMARY KEY (`name`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS eqdkp_raids;
CREATE TABLE `eqdkp_raids` (
	`raid_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	`event_id` smallint(5) unsigned NOT NULL,
	`raid_date` int(11) NOT NULL DEFAULT '0',
	`raid_note` text COLLATE utf8_bin,
	`raid_value` float(6,2) NOT NULL DEFAULT '0.00',
	`raid_added_by` varchar(30) COLLATE utf8_bin NOT NULL,
	`raid_updated_by` varchar(30) COLLATE utf8_bin DEFAULT NULL,
	PRIMARY KEY (`raid_id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS eqdkp_raid_attendees;
CREATE TABLE `eqdkp_raid_attendees` (
	`raid_id` mediumint(8) unsigned NOT NULL,
	`member_id` smallint(5) unsigned NOT NULL,
	KEY `raid_id` (`raid_id`),
	KEY `member_id` (`member_id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

# --------------------------------------------------------
### Multidkp & ItemPools

DROP TABLE IF EXISTS eqdkp_multidkp ;
CREATE TABLE `eqdkp_multidkp` (
	`multidkp_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`multidkp_name` varchar(255) COLLATE utf8_bin NOT NULL,
	`multidkp_desc` text COLLATE utf8_bin NOT NULL,
	PRIMARY KEY (`multidkp_id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS eqdkp_multidkp2event ;
CREATE TABLE `eqdkp_multidkp2event` (
	`multidkp2event_id` int(11) NOT NULL AUTO_INCREMENT,
	`multidkp2event_multi_id` int(11) NOT NULL,
	`multidkp2event_event_id` smallint(5) NOT NULL,
	PRIMARY KEY (`multidkp2event_id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS eqdkp_itempool;
CREATE TABLE IF NOT EXISTS `eqdkp_itempool` (
	`itempool_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`itempool_name` varchar(255) COLLATE utf8_bin NOT NULL,
	`itempool_desc` text COLLATE utf8_bin NOT NULL,
	PRIMARY KEY (`itempool_id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS eqdkp_multidkp2itempool;
CREATE TABLE `eqdkp_multidkp2itempool` (
	`multidkp2itempool_id` int(11) NOT NULL AUTO_INCREMENT,
	`multidkp2itempool_itempool_id` int(11) unsigned NOT NULL,
	`multidkp2itempool_multi_id` int(11) unsigned NOT NULL,
	PRIMARY KEY (`multidkp2itempool_id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

# --------------------------------------------------------
### News

DROP TABLE IF EXISTS eqdkp_news;
CREATE TABLE `eqdkp_news` (
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

DROP TABLE IF EXISTS eqdkp_news_categories;
CREATE TABLE `eqdkp_news_categories` (
	`category_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`category_name` text COLLATE utf8_bin,
	`category_icon` text COLLATE utf8_bin,
	`category_color` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	PRIMARY KEY (`category_id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

# --------------------------------------------------------
### Logging

DROP TABLE IF EXISTS eqdkp_logs;
CREATE TABLE `eqdkp_logs` (
	`log_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`log_date` int(11) NOT NULL DEFAULT '0',
	`log_value` text COLLATE utf8_bin NOT NULL,
	`log_ipaddress` varchar(15) COLLATE utf8_bin NOT NULL DEFAULT '',
	`log_sid` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
	`log_result` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`log_tag` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`log_plugin` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`log_flag` smallint(3) NOT NULL DEFAULT '0',
	`user_id` smallint(5) NOT NULL DEFAULT '0',
	PRIMARY KEY (`log_id`),
	KEY `user_id` (`user_id`),
	KEY `log_tag` (`log_tag`),
	KEY `log_ipaddress` (`log_ipaddress`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

# --------------------------------------------------------
### Styles

DROP TABLE IF EXISTS eqdkp_styles;
CREATE TABLE `eqdkp_styles` (
	`style_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
	`style_name` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
	`style_code` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
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
	`logo_path` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT 'logo.gif',
	`background_img` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`css_file` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`use_db_vars` tinyint(1) unsigned DEFAULT NULL,
	PRIMARY KEY (`style_id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

# --------------------------------------------------------
### Plugins

DROP TABLE IF EXISTS eqdkp_plugins;
CREATE TABLE `eqdkp_plugins` (
	`plugin_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
	`plugin_name` varchar(50) COLLATE utf8_bin NOT NULL,
	`plugin_code` varchar(20) COLLATE utf8_bin NOT NULL,
	`plugin_installed` enum('0','1') COLLATE utf8_bin NOT NULL DEFAULT '0',
	`plugin_path` varchar(255) COLLATE utf8_bin NOT NULL,
	`plugin_contact` varchar(100) COLLATE utf8_bin DEFAULT NULL,
	`plugin_version` varchar(7) COLLATE utf8_bin NOT NULL,
	`plugin_build` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
	PRIMARY KEY (`plugin_id`),
	KEY `plugin_code` (`plugin_code`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

# --------------------------------------------------------
### Item Cache

CREATE TABLE IF NOT EXISTS `item_cache` (
	`item_name` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
	`item_link` varchar(100) COLLATE utf8_bin DEFAULT NULL,
	`item_color` varchar(20) COLLATE utf8_bin NOT NULL DEFAULT '',
	`item_icon` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
	`item_html` text COLLATE utf8_bin NOT NULL,
	UNIQUE KEY `item_name` (`item_name`),
	FULLTEXT KEY `item_html` (`item_html`)
)	 TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS eqdkp_itemIDs;
CREATE TABLE `eqdkp_itemIDs` (
	`itemID_id` int(11) NOT NULL AUTO_INCREMENT,
	`itemID_blizID` int(11) NOT NULL,
	`itemID_displayID` int(11) NOT NULL,
	`itemID_armorySlotID` int(11) NOT NULL,
	`itemID_wowheadSlotID` int(11) NOT NULL,
	PRIMARY KEY (`itemID_id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

# --------------------------------------------------------
### Portal

DROP TABLE IF EXISTS eqdkp_plus_links ;
CREATE TABLE IF NOT EXISTS `eqdkp_plus_links` (
	`link_id` int(12) NOT NULL AUTO_INCREMENT,
	`link_url` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`link_name` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`link_window` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`link_menu` tinyint(4) NOT NULL DEFAULT '0',
	`link_sortid` int(11) NOT NULL DEFAULT '0',
	`link_visibility` tinyint(3) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`link_id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS eqdkp_comments;
CREATE TABLE IF NOT EXISTS `eqdkp_comments` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`attach_id` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`userid` int(11) unsigned NOT NULL,
	`date` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`text` text COLLATE utf8_bin,
	`page` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	PRIMARY KEY (`id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS eqdkp_portal;
CREATE TABLE IF NOT EXISTS `eqdkp_portal` (
	`id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
	`enabled` enum('0','1') COLLATE utf8_bin NOT NULL DEFAULT '0',
	`settings` enum('0','1') COLLATE utf8_bin NOT NULL DEFAULT '0',
	`path` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`contact` varchar(100) COLLATE utf8_bin DEFAULT NULL,
	`url` varchar(100) COLLATE utf8_bin DEFAULT NULL,
	`autor` varchar(100) COLLATE utf8_bin DEFAULT NULL,
	`version` varchar(7) COLLATE utf8_bin NOT NULL DEFAULT '',
	`position` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '0',
	`number` mediumint(8) DEFAULT NULL,
	`plugin` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`visibility` enum('0','1','2') COLLATE utf8_bin NOT NULL DEFAULT '0',
	`collapsable` enum('0','1') COLLATE utf8_bin NOT NULL DEFAULT '1',
	PRIMARY KEY (`id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS eqdkp_pages;
CREATE TABLE IF NOT EXISTS `eqdkp_pages` (
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

DROP TABLE IF EXISTS eqdkp_repository;
CREATE TABLE `eqdkp_repository` (
	`id` varchar(255) COLLATE utf8_bin NOT NULL,
	`plugin` varchar(255) COLLATE utf8_bin NOT NULL,
	`name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`date` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`author` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`description` text COLLATE utf8_bin,
	`shortdesc` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`title` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`version` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`category` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`level` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`changelog` text COLLATE utf8_bin,
	`download` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`filesize` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`build` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	`updated` varchar(255) COLLATE utf8_bin DEFAULT NULL,
	PRIMARY KEY (`id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


# --------------------------------------------------------
### Others (should be removed & added to module...)

DROP TABLE IF EXISTS eqdkp_module_mmonews;
CREATE TABLE IF NOT EXISTS `eqdkp_module_mmonews` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`updated` int(11) NOT NULL DEFAULT '0',
	`rss` text COLLATE utf8_bin NOT NULL,
	`game` text COLLATE utf8_bin NOT NULL,
	PRIMARY KEY (`id`)
)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

