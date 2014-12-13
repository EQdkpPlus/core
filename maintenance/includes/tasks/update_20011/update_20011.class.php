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

class update_20011 extends sql_update_task {
	public $author			= 'GodMod';
	public $version			= '2.0.0.11'; //new plus-version
	public $ext_version		= '2.0.0'; //new plus-version
	public $name			= '2.0.0 Update 3';
	
	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_20011'		=> 'EQdkp Plus 2.0 Update 3',
					1 => 'Create User Profilefield Table',
					2 => 'Insert Profilefield',
					3 => 'Insert Profilefield',
					4 => 'Insert Profilefield',
					5 => 'Insert Profilefield',
					6 => 'Insert Profilefield',
					7 => 'Insert Profilefield',
					8 => 'Insert Profilefield',
					9 => 'Insert Profilefield',
					10 => 'Insert Profilefield',
					11 => 'Insert Profilefield',
					12 => 'Insert Profilefield',
					13 => 'Insert Profilefield',
					14 => 'Insert Profilefield',
					15 => 'Remove Userfields',
				),
			'german' => array(
				'update_20011'		=> 'EQdkp Plus 2.0 Update 3',
					1 => 'Erstelle Benutzerprofilfeld Tabelle',
					2 => 'Füge Profilfeld ein',
					3 => 'Füge Profilfeld ein',
					4 => 'Füge Profilfeld ein',
					5 => 'Füge Profilfeld ein',
					6 => 'Füge Profilfeld ein',
					7 => 'Füge Profilfeld ein',
					8 => 'Füge Profilfeld ein',
					9 => 'Füge Profilfeld ein',
					10 => 'Füge Profilfeld ein',
					11 => 'Füge Profilfeld ein',
					12 => 'Füge Profilfeld ein',
					13 => 'Füge Profilfeld ein',
					14 => 'Füge Profilfeld ein',
					15 => 'Entferne Spalten aus Benutzertabelle',
			),
		);
		
		// init SQL querys
		$this->sqls = array(
			1 => "CREATE TABLE `__user_profilefields` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) NOT NULL,
	`lang_var` VARCHAR(255) NOT NULL,
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
	PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_bin;",
				2 => "INSERT INTO `__user_profilefields` (`id`, `name`, `lang_var`, `type`, `length`, `minlength`, `validation`, `required`, `show_on_registration`, `enabled`, `sort_order`, `is_contact`, `contact_url`, `icon_or_image`, `bridge_field`, `options`, `editable`) VALUES (1, 'location', 'userpf_location', 'text', 100, 2, '', 0, 0, 1, 2, 0, '', 'fa-map-marker', '', 'a:1:{s:7:\"options\";a:0:{}}', 1);",
				3 =>"INSERT INTO `__user_profilefields` (`id`, `name`, `lang_var`, `type`, `length`, `minlength`, `validation`, `required`, `show_on_registration`, `enabled`, `sort_order`, `is_contact`, `contact_url`, `icon_or_image`, `bridge_field`, `options`, `editable`) VALUES (2, 'website', 'userpf_website', 'link', 255, 10, '', 0, 0, 1, 12, 1, '%s', 'fa-globe', NULL, NULL, 1);",
				4 =>"INSERT INTO `__user_profilefields` (`id`, `name`, `lang_var`, `type`, `length`, `minlength`, `validation`, `required`, `show_on_registration`, `enabled`, `sort_order`, `is_contact`, `contact_url`, `icon_or_image`, `bridge_field`, `options`, `editable`) VALUES (3, 'interests', 'userpf_interests', 'textarea', 500, 2, '', 0, 0, 1, 3, 0, NULL, NULL, NULL, NULL, 1);",
				5 =>"INSERT INTO `__user_profilefields` (`id`, `name`, `lang_var`, `type`, `length`, `minlength`, `validation`, `required`, `show_on_registration`, `enabled`, `sort_order`, `is_contact`, `contact_url`, `icon_or_image`, `bridge_field`, `options`, `editable`) VALUES (4, 'occupation', 'userpf_occupation', 'textarea', 500, 2, '', 0, 0, 1, 4, 0, NULL, NULL, NULL, NULL, 1);",
				6 =>"INSERT INTO `__user_profilefields` (`id`, `name`, `lang_var`, `type`, `length`, `minlength`, `validation`, `required`, `show_on_registration`, `enabled`, `sort_order`, `is_contact`, `contact_url`, `icon_or_image`, `bridge_field`, `options`, `editable`) VALUES (5, 'facebook', 'userpf_facebook', 'link', 50, 5, '[\\w.]+', 0, 0, 1, 5, 1, 'https://facebook.com/%s/', 'fa-facebook-square', NULL, NULL, 1);",
				7 =>"INSERT INTO `__user_profilefields` (`id`, `name`, `lang_var`, `type`, `length`, `minlength`, `validation`, `required`, `show_on_registration`, `enabled`, `sort_order`, `is_contact`, `contact_url`, `icon_or_image`, `bridge_field`, `options`, `editable`) VALUES (6, 'twitter', 'userpf_twitter', 'link', 15, 1, '[\\w_]+', 0, 0, 1, 6, 1, 'https://twitter.com/%s', 'fa-twitter-square', NULL, NULL, 1);",
				8 =>"INSERT INTO `__user_profilefields` (`id`, `name`, `lang_var`, `type`, `length`, `minlength`, `validation`, `required`, `show_on_registration`, `enabled`, `sort_order`, `is_contact`, `contact_url`, `icon_or_image`, `bridge_field`, `options`, `editable`) VALUES (7, 'skype', 'userpf_skype', 'link', 32, 1, '[a-zA-Z][\\w\\.,\\-_]+', 0, 0, 1, 7, 1, 'skype:%s?userinfo', 'fa-skype', '', 'a:1:{s:7:\"options\";a:0:{}}', 1);",
				9 =>"INSERT INTO `__user_profilefields` (`id`, `name`, `lang_var`, `type`, `length`, `minlength`, `validation`, `required`, `show_on_registration`, `enabled`, `sort_order`, `is_contact`, `contact_url`, `icon_or_image`, `bridge_field`, `options`, `editable`) VALUES (8, 'youtube', 'userpf_youtube', 'link', 60, 6, '[a-zA-Z][\\w\\.,\\-_]+', 0, 0, 1, 8, 1, 'http://youtube.com/user/%s', 'fa-youtube', NULL, NULL, 1);",
				10 =>"INSERT INTO `__user_profilefields` (`id`, `name`, `lang_var`, `type`, `length`, `minlength`, `validation`, `required`, `show_on_registration`, `enabled`, `sort_order`, `is_contact`, `contact_url`, `icon_or_image`, `bridge_field`, `options`, `editable`) VALUES (9, 'googleplus', 'userpf_googleplus', 'link', 255, 3, '[\\w]+', 0, 0, 1, 9, 1, 'http://plus.google.com/%s', 'fa-google-plus-square', NULL, NULL, 1);",
				11 =>"INSERT INTO `__user_profilefields` (`id`, `name`, `lang_var`, `type`, `length`, `minlength`, `validation`, `required`, `show_on_registration`, `enabled`, `sort_order`, `is_contact`, `contact_url`, `icon_or_image`, `bridge_field`, `options`, `editable`) VALUES (10, 'icq', 'userpf_icq', 'link', 15, 3, '[0-9]+', 0, 0, 1, 10, 1, 'https://www.icq.com/people/%s/', '', '', 'a:1:{s:7:\"options\";a:0:{}}', 1);",
				12 =>"INSERT INTO `__user_profilefields` (`id`, `name`, `lang_var`, `type`, `length`, `minlength`, `validation`, `required`, `show_on_registration`, `enabled`, `sort_order`, `is_contact`, `contact_url`, `icon_or_image`, `bridge_field`, `options`, `editable`) VALUES (11, 'mobile', 'userpf_mobile', 'text', 30, 6, '[0-9\\+\\/\\-]+', 0, 0, 1, 11, 1, '%s', 'fa-mobile-phone', NULL, NULL, 1);",
				13 =>"INSERT INTO `__user_profilefields` (`id`, `name`, `lang_var`, `type`, `length`, `minlength`, `validation`, `required`, `show_on_registration`, `enabled`, `sort_order`, `is_contact`, `contact_url`, `icon_or_image`, `bridge_field`, `options`, `editable`) VALUES (12, 'name', 'userpf_name', 'text', 50, 1, '', 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, 1);",
				14 =>"INSERT INTO `__user_profilefields` (`id`, `name`, `lang_var`, `type`, `length`, `minlength`, `validation`, `required`, `show_on_registration`, `enabled`, `sort_order`, `is_contact`, `contact_url`, `icon_or_image`, `bridge_field`, `options`, `editable`) VALUES (13, 'lastname', 'userpf_lastname', 'text', 50, 1, '', 0, 0, 1, 1, 0, NULL, NULL, NULL, NULL, 1);",
		15 => "ALTER TABLE `__users`
	DROP COLUMN `first_name`,
	DROP COLUMN `last_name`,
	DROP COLUMN `town`,
	DROP COLUMN `state`,
	DROP COLUMN `ZIP_code`,
	DROP COLUMN `phone`,
	DROP COLUMN `cellphone`,
	DROP COLUMN `address`,
	DROP COLUMN `icq`,
	DROP COLUMN `skype`,
	DROP COLUMN `irq`;",
		);
	}
	
}


?>