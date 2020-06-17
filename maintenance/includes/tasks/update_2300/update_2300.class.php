<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2015 EQdkp-Plus Developer Team
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU Affero General Public License as published
 *	by the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU Affero General Public License for more details.
 *
 *	You should have received a copy of the GNU Affero General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if ( !defined('EQDKP_INC') ){
  header('HTTP/1.0 404 Not Found');exit;
}

include_once(registry::get_const('root_path').'maintenance/includes/sql_update_task.class.php');

class update_2300 extends sql_update_task {
	public $author			= 'Wallenium';
	public $version		= '2.3.0.0'; //new plus-version
	public $ext_version	= '2.3.0'; //new plus-version
	public $name			= '2.3.0 Update';

	public function __construct(){
		parent::__construct();

// combined update 2.3.0.1 to 2.3.0.11
		$this->langs = array(
			'english' => array(
				'update_2300'	=> 'EQdkp Plus 2.3.0 Update',
				1	=> 'Add icons field to roles table',
				2	=> 'Change article table',
				3	=> 'Change calendar_raid_attendees table',
				4	=> 'Change log table',
				5	=> 'Delete notifcation Type',
				6	=> 'Add Notification Type',
				7	=> 'Alter Style Table',
				8	=> 'Alter Style Table',
				9	=> 'Alter Style Table',
				10	=> 'Change user table',
				11	=> 'Change user table',
				12	=> 'Change user table',
				13	=> 'Add roles field to raid guests table',
				14	=> 'Alter table raids',
				15	=> 'Alter table items',
				16	=> 'Alter table adjustments',
				17	=> 'Alter table members',
				18	=> 'Alter table members',
				19	=> 'Create table member_points',
				20	=> 'Create table member_points',
				21	=> 'Add permission',
				22	=> 'Add permission',
				23	=> 'Add permission',
				24	=> 'Add permission',
				25	=> 'Add permission',
				26	=> 'Add permission',
				27	=> 'Add permission',
				28	=> 'Add permission',
				29	=> 'Add permission',
				30	=> 'Add Cronjob Table',
				31 => 'Add permissions field to calendar table',
				32 => 'Add permissions field to calendar table',
				33 => 'Add Steam field',
				34 => 'Add Discord Field',
				35 => 'Drop some unused columns',
				36 => 'Alter Style Table',
				37 => 'Alter Events Table',
				'update_function' => 'Add Cronjobs to the Databse',
				38 => "Alter user profilfeld table",
				39 => 'Add Steam field',
				40 => 'Add Discord field',
				41 => 'Add userprofile field',
				42 => 'Add userprofile field',
				43 => 'Add userprofile field',
				44	=> 'Add Indexes for Raids',
				45	=> 'Add Indexes for Items',
				46	=> 'Add Indexes for Adjustments',
				47	=> 'Extend Log Table',
			),
			'german' => array(
				'update_2300'	=> 'EQdkp Plus 2.3.0 Update',
				1	=> 'Füge Icons-Feld zur Rollentabelle hinzu',
				2	=> 'Ändere Article-Tabelle',
				3	=> 'Ändere calendar_raid_attendees-Tabelle',
				4	=> 'Ändere Logs-Tabelle',
				5	=> 'Entferne Benachrichtigungstyp',
				6	=> 'Füge Benachrichtigungstyp hinzu',
				7	=> 'Erweitere Style Tabelle',
				8	=> 'Erweitere Style Tabelle',
				9	=> 'Erweitere Style Tabelle',
				10	=> 'Ändere Benutzer-Tabelle',
				11	=> 'Ändere Benutzer-Tabelle',
				12	=> 'Ändere Benutzer-Tabelle',
				13	=> 'Füge Rollenfeld zur Raid-Gästetabelle',
				14	=> 'Erweitere Tabelle raids',
				15	=> 'Erweitere Tabelle items',
				16	=> 'Erweitere Tabelle adjustments',
				17	=> 'Erweitere Tabelle members',
				18	=> 'Erweitere Tabelle members',
				19	=> 'Erstelle Tabelle member_points',
				20	=> 'Erstelle Tabelle member_points',
				21	=> 'Füge Berechtigung hinzu',
				22	=> 'Füge Berechtigung hinzu',
				23	=> 'Füge Berechtigung hinzu',
				24	=> 'Füge Berechtigung hinzu',
				25	=> 'Füge Berechtigung hinzu',
				26	=> 'Füge Berechtigung hinzu',
				27	=> 'Füge Berechtigung hinzu',
				28	=> 'Füge Berechtigung hinzu',
				29	=> 'Füge Berechtigung hinzu',
				30	=> 'Erstelle Cronjob Tabelle',
				31 => 'Füge Berechtigungen Feld zu Kalendertabelle hinzu',
				32 => 'Füge Berechtigungen Feld zu Kalendertabelle hinzu',
				35 => 'Entferne nicht mehr genutzte Spalten',
				36 => 'Erweitere Style Tabelle',
				37 => 'Erweitere Events Tabelle',
				'update_function' => 'Übertrage Cronjobs in die Datenbank',
				38 => "Erweitere Benutzerprofilfeldtabelle",
				39 => 'Füge Steam Benutzerprofilfeld hinzu',
				40 => 'Füge Discord Benutzerprofilfeld hinzu',
				41 => 'Füge Benutzerprofilfeld hinzu',
				42 => 'Füge Benutzerprofilfeld hinzu',
				43 => 'Füge Benutzerprofilfeld hinzu',
				44	=> 'Füge Indexes für Raids hinzu',
				45	=> 'Füge Indexes für Items hinzu',
				46	=> 'Füge Indexes für Adjustments hinzu',
				47	=> 'Erweitere Log-Tabelle',
			),
		);

		// init SQL querys
		$this->sqls = array(
			1	=> "ALTER TABLE `__roles` ADD COLUMN `role_icon` varchar(255) COLLATE utf8_bin DEFAULT NULL;",
			2	=> "ALTER TABLE `__articles` CHANGE COLUMN `last_edited_user` `last_edited_user` INT(11) NOT NULL DEFAULT '0';",
			3	=> "ALTER TABLE `__calendar_raid_attendees` CHANGE COLUMN `status_changedby` `status_changedby` INT(11) NOT NULL DEFAULT '0';",
			4	=> "ALTER TABLE `__logs` CHANGE COLUMN `log_value` `log_value` MEDIUMTEXT NOT NULL COLLATE 'utf8_bin';",
			5	=> "DELETE FROM `__notification_types` WHERE `id` = 'eqdkp_user_new_registered';",
			6	=> "INSERT INTO `__notification_types` (`id`, `name`, `category`, `prio`, `default`, `group`, `group_name`, `group_at`, `icon`) VALUES ('eqdkp_user_new_registered', 'notification_user_new_registered', 'user', 0, '0', 0, NULL, 0, 'fa-user-plus');",
			7	=> "ALTER TABLE `__styles` ADD COLUMN `additional_fields` TEXT NULL COLLATE 'utf8_bin';",
			8	=> "ALTER TABLE `__styles` ADD `favicon_img` VARCHAR(255) NULL DEFAULT NULL AFTER `logo_position`;",
			9	=> "ALTER TABLE `__styles` ADD `banner_img` VARCHAR(255) NULL DEFAULT NULL AFTER `favicon_img`;",
			10	=> "ALTER TABLE `__users` CHANGE COLUMN `user_key` `user_email_confirmkey` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8_bin';",
			11	=> "ALTER TABLE `__users` ADD COLUMN `user_email_confirmed` INT(2) NOT NULL DEFAULT '1';",
			12	=> "ALTER TABLE `__users` ADD COLUMN `user_temp_email` VARCHAR(255) NULL COLLATE 'utf8_bin'",
			13	=> "ALTER TABLE `__calendar_raid_guests` ADD `role` int(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `status`;",
			14	=> "ALTER TABLE `__raids` ADD COLUMN `raid_apa_value` TEXT NULL COLLATE 'utf8_bin'",
			15	=> "ALTER TABLE `__items` ADD COLUMN `item_apa_value` TEXT NULL COLLATE 'utf8_bin'",
			16	=> "ALTER TABLE `__adjustments` ADD COLUMN `adjustment_apa_value` TEXT NULL COLLATE 'utf8_bin'",
			17	=> "ALTER TABLE `__members` ADD COLUMN `points` TEXT NULL COLLATE 'utf8_bin'",
			18	=> "ALTER TABLE `__members` ADD COLUMN `points_apa` TEXT NULL COLLATE 'utf8_bin'",
			19	=> "DROP TABLE IF EXISTS __member_points;",
			20 => "CREATE TABLE `__member_points` (
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
			) DEFAULT CHARSET=utf8 COLLATE=utf8_bin;",
			21	=> "INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_roles_man','N');",
			22	=> "INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_member_profilefields_man','N');",
			23	=> "INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_apa_man','N');",
			24	=> "INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_tables_man','N');",
			25	=> "INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_notifications_man','N');",
			26	=> "INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_menues_man','N');",
			27	=> "INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_cronjobs_man','N');",
			28	=> "INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_bridge_man','N');",
			29	=> "INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_cache_man','N');",
			30	=> "CREATE TABLE `__cronjobs` (
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
			) DEFAULT CHARSET=utf8 COLLATE=utf8_bin;",
			31	=> "ALTER TABLE `__calendars` ADD `permissions` VARCHAR(255) COLLATE utf8_bin NOT NULL DEFAULT 'all' AFTER `affiliation`;",
			32 => "UPDATE __calendars SET `permissions` = 'all';",
			35 => "ALTER TABLE `__users`
	DROP COLUMN `user_alimit`,
	DROP COLUMN `user_climit`,
	DROP COLUMN `user_elimit`,
	DROP COLUMN `user_ilimit`,
	DROP COLUMN `user_nlimit`,DROP COLUMN `country`,DROP COLUMN `birthday`,DROP COLUMN `gender`;",
			36	=> "ALTER TABLE `__styles` ADD `editor_theme` VARCHAR(255) NULL DEFAULT 'lightgray';",
			37 => "ALTER TABLE `__events` ADD COLUMN `default_itempool` INT(11) UNSIGNED NOT NULL DEFAULT '0';",
			38 => "ALTER TABLE `__user_profilefields` ADD COLUMN `example` VARCHAR(255) NULL COLLATE 'utf8_bin'",
			39 => "INSERT INTO `__user_profilefields` (`name`, `lang_var`, `type`, `length`, `minlength`, `validation`, `required`, `show_on_registration`, `enabled`, `sort_order`, `is_contact`, `contact_url`, `icon_or_image`, `bridge_field`, `options`, `editable`, `example`) VALUES ('Steam', 'userpf_steam', 'link', 30, 2, '[\\w_]+', 0, 0, 1, 14, 1, 'https://steamcommunity.com/id/%s/', 'fa-steam-square', NULL, NULL, 1, 'zaflu');",
			40 => "INSERT INTO `__user_profilefields` (`name`, `lang_var`, `type`, `length`, `minlength`, `validation`, `required`, `show_on_registration`, `enabled`, `sort_order`, `is_contact`, `contact_url`, `icon_or_image`, `bridge_field`, `options`, `editable`, `example`) VALUES ('Discord', '', 'text', 30, 2, '[\\w\\s_]+#[0-9]{4}', 0, 0, 1, 15, 1, '', '', NULL, NULL, 1, 'GodMod#1234');",
			41 => "INSERT INTO `__user_profilefields` (`name`, `lang_var`, `type`, `length`, `minlength`, `validation`, `required`, `show_on_registration`, `enabled`, `sort_order`, `is_contact`, `contact_url`, `icon_or_image`, `bridge_field`, `options`, `editable`, `example`) VALUES ('birthday', 'userpf_birthday', 'birthday', 50, 0, '', 0, 0, 1, 1, 0, NULL, NULL, NULL, NULL, 1, NULL);",
			42 => "INSERT INTO `__user_profilefields` (`name`, `lang_var`, `type`, `length`, `minlength`, `validation`, `required`, `show_on_registration`, `enabled`, `sort_order`, `is_contact`, `contact_url`, `icon_or_image`, `bridge_field`, `options`, `editable`, `example`) VALUES ('country', 'userpf_country', 'country', 50, 0, '', 0, 0, 1, 1, 0, NULL, NULL, NULL, NULL, 1, NULL);",
			43 => "INSERT INTO `__user_profilefields` (`name`, `lang_var`, `type`, `length`, `minlength`, `validation`, `required`, `show_on_registration`, `enabled`, `sort_order`, `is_contact`, `contact_url`, `icon_or_image`, `bridge_field`, `options`, `editable`, `example`) VALUES ('gender', 'userpf_gender', 'gender', 50, 0, '', 0, 0, 1, 1, 0, '', '', NULL, 'a:1:{s:7:\"options\";a:3:{s:1:\"m\";s:8:\"gender_m\";s:1:\"f\";s:8:\"gender_f\";s:1:\"n\";s:8:\"gender_n\";}}', 1, '');",
			44	=> "ALTER TABLE `__adjustments`
	ADD INDEX `adjustment_value` (`adjustment_value`),
	ADD INDEX `event_id` (`event_id`),
	ADD INDEX `member_id` (`member_id`);",
			45	=> "ALTER TABLE `__raids`
	ADD INDEX `raid_value` (`raid_value`),
	ADD INDEX `event_id` (`event_id`);",
			46	=> "ALTER TABLE `__items`
	ADD INDEX `member_id` (`member_id`),
	ADD INDEX `item_value` (`item_value`),
	ADD INDEX `itempool_id` (`itempool_id`);",
			47 => "ALTER TABLE `__logs`
	ADD COLUMN `trace` TEXT NULL COLLATE 'utf8_bin' AFTER `log_record_id`;",
		);
	}

	public function update_function(){
		//Set new configs
		$this->config->set('avatar_default', 'eqdkp');
		$this->config->set('avatar_allowed', array('eqdkp', 'gravatar'));
		$this->config->set('auto_set_active', 1);
		$this->config->set('notify_updates_email', 1);

		$this->config->set('build_pointcache', 'true');

		$this->update_cronjobs();

		$this->update_defaultstyle();

		return true;
	}

	private function update_cronjobs(){
		$crontab_file = $this->pfh->FolderPath('timekeeper', 'eqdkp').'crontab.php';
		$result = @file_get_contents($crontab_file);
		if($result !== false){
			$this->db->prepare("TRUNCATE __cronjobs")->execute();
			$crontab = unserialize_noclasses($result);
			foreach($crontab as $key => $val){
				$this->pdh->put('cronjobs', 'add', array($key, $val['start_time'], $val['repeat'], $val['repeat_type'], $val['repeat_interval'], $val['extern'], $val['ajax'],$val['delay'], $val['multiple'], $val['active'], $val['editable'], $val['path'], $val['params'], $val['description']));
				$this->pdh->put('cronjobs', 'setLastAndNextRun', array($key, $val['last_run'],$val['next_run']));
			}

			$this->pdh->process_hook_queue();
			return true;
		}

		return false;
	}

	private function update_defaultstyle(){
		$arrTemplates = $this->pdh->get('styles', 'styles');
		$blnHasClean = false;
		foreach($arrTemplates as $row){
			if($row['template_path'] == "eqdkp_clean"){
				$blnHasClean = true;
				$intCleanId = $row['style_id'];
				break;
			}
		}

		if(!$blnHasClean){
			$objQuery =	$this->db->query("INSERT INTO `__styles` (`style_name`, `style_version`, `style_contact`, `style_author`, `enabled`, `template_path`, `attendees_columns`, `logo_position`, `favicon_img`, `banner_img`, `background_img`, `column_right_width`, `column_left_width`, `portal_width`, `background_pos`, `background_type`, `body_background_color`, `body_font_color`, `body_font_size`, `body_font_family`, `body_link_color`, `body_link_color_hover`, `body_link_decoration`, `container_background_color`, `container_border_color`, `content_background_color`, `content_font_color`, `content_font_color_headings`, `content_link_color`, `content_link_color_hover`, `content_border_color`, `content_accent_color`, `userarea_background_color`, `userarea_font_color`, `userarea_link_color`, `userarea_link_color_hover`, `table_th_background_color`, `table_th_font_color`, `table_tr_font_color`, `table_tr_background_color1`, `table_tr_background_color2`, `table_tr_background_color_hover`, `table_border_color`, `menu_background_color`, `menu_font_color`, `menu_item_background_color`, `menu_item_background_color_hover`, `menu_item_font_color_hover`, `sidebar_background_color`, `sidebar_font_color`, `sidebar_border_color`, `button_background_color`, `button_font_color`, `button_border_color`, `button_background_color_hover`, `button_font_color_hover`, `button_border_color_hover`, `input_background_color`, `input_border_color`, `input_font_color`, `input_background_color_active`, `input_border_color_active`, `input_font_color_active`, `content_contrast_color`, `content_contrast_background_color`, `content_contrast_border_color`, `content_positive_color`, `content_negative_color`, `content_neutral_color`, `content_highlight_color`, `misc_color1`, `misc_color2`, `misc_color3`, `misc_text1`, `misc_text2`, `misc_text3`, `additional_less`, `additional_fields`, `editor_theme`) VALUES ('EQdkp Clean', '2.3.1', '', 'GodMod', '1', 'eqdkp_clean', '6', 'left', '', '@eqdkpTemplateImagePath/background-head.svg', '', '0px', '0px', '0px', 'normal', 0, '#f5f5f5', '#ffffff', '14', '\"Open Sans\",Arial,Helvetica,sans-serif', '#4e7fa8', '#000000', 'none', '#f3f3f3', 'rgb(56, 56, 56)', '#f5f5f5', 'rgb(0, 0, 0)', '#1D2F3a', '#4e7fa8', 'rgb(0, 0, 0)', 'rgb(204, 204, 204)', 'rgb(234, 234, 234)', '#16334f', '#d9d7d7', 'rgb(217, 215, 215)', 'rgb(255, 255, 255)', '#efefef', '#000000', '', '#f9f9f9', 'rgb(245, 245, 245)', '#e7eff8', '#e0e0e0', '#1a5188', '#ffffff', 'rgba(255, 255, 255, 0)', '#2e78b0', 'rgb(255, 255, 255)', '#eaeaea', 'rgb(0, 0, 0)', '#eaeaea', '#1a5188', '#ffffff', '#1a5188', '#2e78b0', '#ffffff', '#2e78b0', 'rgb(255, 255, 255)', 'rgb(204, 204, 204)', 'rgb(85, 85, 85)', 'rgb(255, 255, 255)', 'rgba(82, 168, 236, 0.8)', 'rgb(85, 85, 85)', 'rgb(0, 0, 0)', '#eaeaea', '#eaeaea', 'rgb(51, 204, 51)', 'rgb(226, 59, 48)', 'rgb(153, 153, 153)', '#125190', '', 'rgb(255, 255, 255)', '', '', '', '', '@eqdkpHeaderFontColor: #fff;\n@styleCommentContainerBackgroundColor: #fff;\n@styleCommentContainerBorderColor: #ccc;\n@styleCommentAuthorColor: #9f9f9f;\n@stylePaginationBorderColor: #ddd;\n@stylePaginationBackgroundColor: #fff;\n@stylePaginationActiveBackgroundColor: #1a5188;\n@stylePaginationActiveColor: #fff;\n@stylePaginationActiveHoverBackgroundColor: #1a5188;\n@stylePaginationActiveHoverColor: #fff;\n@styleArticleSitemapBorderColor: #ddd;\n@styleArticleSitemapBackgroundColor: #fff;\n@styleArticleSitemapActiveBackgroundColor: #1a5188;\n@styleArticleSitemapActiveColor: #fff;\n@styleArticleSitemapHoverColor: #fff;\n@styleBorderRadius:0px;\n@styleShadowColor: #17252d;\n@stylePortalModulesBorderRadius:0px;\n@stylePortalModulesBorderColor:transparent;\n@stylePortalModulesColor:@eqdkpContentFontColor;\n@stylePortalModulesBackgroundColor:@eqdkpContentBackgroundColor;\n@styleContentFooter: #0c0c0c;\n@styleBooleanGreen:#89bf8b;\n@styleBooleanRed:#e89795;\n@styleTableThBorderColor: #4e7fa8;\n@styleAvatarBorderRadius: 50%;\n@styleFeaturedColor: #ffd700;\n@styleBannerBackgroundColorFrom: #2e78b0;\n@styleBannerBackgroundColorTo: #193759;\n@styleBubbleBorderRadius: 9px;', 'a:0:{}', 'lightgray');");
			$intCleanId = $objQuery->insertId;

			$this->pdh->enqueue_hook('styles_update');

			$this->pdh->process_hook_queue();
		}

		$arrTemplatesFor23 = array('eqdkp_diablo', 'eqdkp_neon', 'eqdkp_bluesky', 'eqdkp_luna', 'eqdkp_legion', 'eqdkp_swtor', 'eqdkp_bs', 'eqdkp_modern', 'eqdkp_clean');

		$intDefaultStyle = $this->config->get('default_style');

		$arrDefaultStyle = $this->pdh->get('styles', 'styles', array($intDefaultStyle));
		$strPath = $arrDefaultStyle['template_path'];

		if(!in_array($strPath, $arrTemplatesFor23)){
			$this->config->set('default_style', $intCleanId);
		}

		return true;
	}
}
