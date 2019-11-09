<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2016 EQdkp-Plus Developer Team
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

class update_2100 extends sql_update_task {
	public $author			= 'GodMod';
	public $version			= '2.1.0.7'; //new plus-version
	public $ext_version		= '2.1.0'; //new plus-version
	public $name			= '2.1.0 Update';

	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_2100'	=> 'EQdkp Plus 2.1.0 Update',
				'update_function'=> 'Perform some important updates',
					1			=> 'Alter calendar events table add timezone',
					2			=> 'Rename "none" in repeating field to 0',
					3			=> 'Rename "day" in repeating field to 1',
					4			=> 'Rename "week" in repeating field to 7',
					5			=> 'Rename "twoweek" in repeating field to 14',
					6			=> 'Change repeating field in calendar events table to INT',
					7			=> 'Alter portallayout table',
					8			=> 'Add field for away mode to user table',
					9			=> 'Add field for away mode to user table',
					10			=> 'Add field for away mode to user table',
					11			=> 'Add field for away mode to user table',
					12			=> 'Change column format',
					13			=> 'Change column format',
				),
			'german' => array(
				'update_2100'	=> 'EQdkp Plus 2.1.0 Update',
				'update_function'=> 'Führe einige wichtige Updates durch',
					1			=> 'Füge die Zeitzone zur Kalender-Events-Tabelle hinzu',
					2			=> 'Bennene den Inhalt des Wiederholungsfeldes von "none" in 0',
					3			=> 'Bennene den Inhalt des Wiederholungsfeldes von "day" in 1',
					4			=> 'Bennene den Inhalt des Wiederholungsfeldes von "week" in 7',
					5			=> 'Bennene den Inhalt des Wiederholungsfeldes von "twoweek" in 14',
					6			=> 'Ändere das Wiederholungsfeldes von VARCHAR zu INT',
					7			=> 'Erweitere Portallayout Tabelle',
					8			=> 'Füge Feld für Abwesenheitsmodus in die Benutzertabelle ein',
					9			=> 'Füge Feld für Abwesenheitsmodus in die Benutzertabelle ein',
					10			=> 'Füge Feld für Abwesenheitsmodus in die Benutzertabelle ein',
					11			=> 'Füge Feld für Abwesenheitsmodus in die Benutzertabelle ein',
					12			=> 'Ändere Spaltenformat',
					13			=> 'Ändere Spaltenformat',
			),
		);

		// init SQL querys
		$this->sqls = array(
			1	=> "ALTER TABLE `__calendar_events` ADD COLUMN `timezone` VARCHAR(255) NULL;",
			2	=> "UPDATE `__calendar_events` SET `repeating` = '0' WHERE `repeating` = 'none';",
			3	=> "UPDATE `__calendar_events` SET `repeating` = '7' WHERE `repeating` = 'week';",
			4	=> "UPDATE `__calendar_events` SET `repeating` = '14' WHERE `repeating` = 'twoweeks';",
			5	=> "UPDATE `__calendar_events` SET `repeating` = '1' WHERE `repeating` = 'day';",
			6	=> "ALTER TABLE `__calendar_events` CHANGE `repeating` `repeating` INT(10) NULL DEFAULT 0;",
			7	=> "ALTER TABLE `__portal_layouts` ADD COLUMN `routes` TEXT NULL DEFAULT NULL COLLATE 'utf8_bin';",
			8	=> "ALTER TABLE `__users` ADD COLUMN `awaymode_enabled` tinyint(1) NOT NULL DEFAULT 0;",
			9	=> "ALTER TABLE `__users` ADD COLUMN `awaymode_startdate` INT(11) NULL DEFAULT '0';",
			10	=> "ALTER TABLE `__users` ADD COLUMN `awaymode_enddate` INT(11) NULL DEFAULT '0';",
			11	=> "ALTER TABLE `__users` ADD COLUMN `awaymode_note` text COLLATE utf8_bin;",
			12	=> "ALTER TABLE `__article_categories` CHANGE COLUMN `name` `name` TEXT NOT NULL COLLATE 'utf8_bin';",
			13	=> "ALTER TABLE `__articles` CHANGE COLUMN `title` `title` TEXT NOT NULL COLLATE 'utf8_bin'",
		);
	}

	public function update_function(){
		// fetch all events and set timezones
		$caleventids	= $this->pdh->get('calendar_events', 'id_list');
		if(is_array($caleventids) && count($caleventids) > 0){
			foreach($caleventids as $calid){
				$creator	= $this->pdh->get('calendar_events', 'creatorid', array($calid));
				$creator_tz	= $this->pdh->get('user', 'timezone', array($creator));
				$this->pdh->put('calendar_events', 'update_timezone', array($calid, $creator_tz));
			}
		}

		// 2.1.0 Update 1
		$this->ntfy->addNotificationType('calendarevent_new','notification_calendarevent_new', 'calendarevent', 0, 1, 0, '', 0, 'fa-calendar');

		$arrUsers = $this->pdh->get('user', 'id_list', array());
		foreach($arrUsers as $intUserID){
			$arrNotificationSettings = $this->pdh->get('user', 'notification_settings', array($intUserID));
			if(!isset($arrNotificationSettings['ntfy_comment_new_article'])) continue;

			$arrNotificationSettings['ntfy_comment_new_article_categories'] = $arrNotificationSettings['ntfy_comment_new_article'];
			$arrNotificationSettings['ntfy_comment_new_article'] = 1;

			$this->pdh->put('user', 'update_user', array($intUserID, array('notifications' => serialize($arrNotificationSettings)), false, false));
		}

		// update the js date settings
		$this->config->set('default_jsdate_nrml', $this->user->lang('style_jsdate_nrml'));
		$this->config->set('default_jsdate_short', $this->user->lang('style_jsdate_short'));

		// 2.1.0 Update 2
		$this->db->query('ALTER TABLE `__styles`
	DROP COLUMN `body_background`,
	DROP COLUMN `body_link`,
	DROP COLUMN `body_link_style`,
	DROP COLUMN `body_hlink`,
	DROP COLUMN `body_hlink_style`,
	DROP COLUMN `header_link`,
	DROP COLUMN `header_link_style`,
	DROP COLUMN `header_hlink`,
	DROP COLUMN `header_hlink_style`,
	DROP COLUMN `tr_color1`,
	DROP COLUMN `tr_color2`,
	DROP COLUMN `th_color1`,
	DROP COLUMN `fontface1`,
	DROP COLUMN `fontface2`,
	DROP COLUMN `fontface3`,
	DROP COLUMN `fontsize1`,
	DROP COLUMN `fontsize2`,
	DROP COLUMN `fontsize3`,
	DROP COLUMN `fontcolor1`,
	DROP COLUMN `fontcolor2`,
	DROP COLUMN `fontcolor3`,
	DROP COLUMN `fontcolor_neg`,
	DROP COLUMN `fontcolor_pos`,
	DROP COLUMN `table_border_width`,
	DROP COLUMN `table_border_color`,
	DROP COLUMN `table_border_style`,
	DROP COLUMN `input_color`,
	DROP COLUMN `input_border_width`,
	DROP COLUMN `input_border_color`,
	DROP COLUMN `input_border_style`,
	DROP COLUMN `css_file`,
	DROP COLUMN `use_db_vars`;');


		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `body_background_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `body_font_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `body_font_size` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `body_font_family` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `body_link_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `body_link_color_hover` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `body_link_decoration` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `container_background_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `container_border_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `content_background_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `content_font_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `content_font_color_headings` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `content_link_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `content_link_color_hover` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `content_border_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `content_accent_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `userarea_background_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `userarea_font_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `userarea_link_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `userarea_link_color_hover` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `table_th_background_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `table_th_font_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `table_tr_font_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `table_tr_background_color1` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `table_tr_background_color2` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `table_tr_background_color_hover` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `table_border_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `menu_background_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `menu_font_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `menu_item_background_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `menu_item_background_color_hover` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `menu_item_font_color_hover` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `sidebar_background_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `sidebar_font_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `sidebar_border_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `button_background_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `button_font_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `button_border_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `button_background_color_hover` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `button_font_color_hover` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `button_border_color_hover` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `input_background_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `input_border_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `input_font_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `input_background_color_active` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `input_border_color_active` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `input_font_color_active` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");

		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `content_contrast_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `content_contrast_background_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `content_contrast_border_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `content_positive_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `content_negative_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `content_neutral_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `misc_color1` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `misc_color2` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `misc_color3` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");

		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `misc_text1` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `misc_text2` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `misc_text3` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `additional_less` TEXT COLLATE 'utf8_bin' NULL DEFAULT NULL");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `content_highlight_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");

		$arrSet = array(
				'style_version' => '0.2.0',
				'body_background_color' => 'rgb(46, 120, 176)',
				'body_font_color' => 'rgb(255, 255, 255)',
				'body_font_size' => '13',
				'body_font_family' => 'Tahoma,Arial,Verdana,sans-serif',
				'body_link_color' => 'rgb(254, 254, 254)',
				'body_link_color_hover' => 'rgb(255, 255, 255)',
				'body_link_decoration' => 'none',
				'container_background_color' => 'rgb(224, 224, 224)',
				'container_border_color' => 'rgb(56, 56, 56)',
				'content_background_color' => 'rgb(245, 245, 245)',
				'content_font_color' => 'rgb(0, 0, 0)',
				'content_font_color_headings' => 'rgb(0, 0, 0)',
				'content_link_color' => 'rgb(78, 127, 168)',
				'content_link_color_hover' => 'rgb(0, 0, 0)',
				'content_border_color' => 'rgb(204, 204, 204)',
				'content_accent_color' => 'rgb(234, 234, 234)',
				'userarea_background_color' => 'rgb(69, 72, 77)',
				'userarea_font_color' => 'rgb(217, 215, 215)',
				'userarea_link_color' => 'rgb(217, 215, 215)',
				'userarea_link_color_hover' => 'rgb(255, 255, 255)',
				'table_th_background_color' => 'rgb(232, 232, 232)',
				'table_th_font_color' => 'rgb(0, 0, 0)',
				'table_tr_font_color' => '',
				'table_tr_background_color1' => 'rgb(249, 249, 249)',
				'table_tr_background_color2' => 'rgb(245, 245, 245)',
				'table_tr_background_color_hover' => 'rgb(232, 232, 232)',
				'table_border_color' => 'rgb(221, 221, 221)',
				'menu_background_color' => 'rgb(69, 72, 77)',
				'menu_font_color' => 'rgb(217, 215, 215)',
				'menu_item_background_color' => 'rgba(255, 255, 255, 0)',
				'menu_item_background_color_hover' => 'rgb(0, 0, 0)',
				'menu_item_font_color_hover' => 'rgb(217, 215, 215)',
				'sidebar_background_color' => 'rgb(234, 234, 234)',
				'sidebar_font_color' => 'rgb(0, 0, 0)',
				'sidebar_border_color' => 'rgb(204, 204, 204)',
				'button_background_color' => 'rgb(0, 173, 238)',
				'button_font_color' => 'rgb(228, 245, 252)',
				'button_border_color' => 'rgb(0, 118, 163)',
				'button_background_color_hover' => 'rgb(0, 149, 204)',
				'button_font_color_hover' => 'rgb(217, 238, 247)',
				'button_border_color_hover' => 'rgb(0, 118, 163)',
				'input_background_color' => 'rgb(255, 255, 255)',
				'input_border_color' => 'rgb(204, 204, 204)',
				'input_font_color' => 'rgb(85, 85, 85)',
				'input_background_color_active' => 'rgb(255, 255, 255)',
				'input_border_color_active' => 'rgba(82, 168, 236, 0.8)',
				'input_font_color_active' => 'rgb(85, 85, 85)',
				'content_contrast_color' => 'rgb(0, 0, 0)',
				'content_contrast_border_color' => 'rgb(204, 204, 204)',
				'misc_color1' => 'rgb(78, 127, 168)',
				'misc_color2' => 'rgb(255, 255, 255)',
				'misc_color3' => '',
				'content_contrast_background_color' => 'rgb(252, 253, 254)',
				'content_positive_color' => 'rgb(51, 204, 51)',
				'content_negative_color' => 'rgb(226, 59, 48)',
				'content_neutral_color' => 'rgb(153, 153, 153)',
				'misc_text1' => '',
				'misc_text2' => '',
				'misc_text3' => '',
				'additional_less' => '@styleCommentContainerBackgroundColor: #fff;
@styleCommentContainerBorderColor: #ccc;
@styleCommentAuthorColor: #9f9f9f;
@stylePaginationBorderColor: #ddd;
@stylePaginationBackgroundColor: #fff;
@stylePaginationActiveBackgroundColor: #F7F7F9;
@stylePaginationActiveColor: #999;
@stylePaginationActiveHoverBackgroundColor: #F7F7F9;
@stylePaginationActiveHoverColor: #000;
@styleArticleSitemapBorderColor: #ddd;
@styleArticleSitemapBackgroundColor: #fff;
@styleArticleSitemapActiveBackgroundColor: #F7F7F9;
@styleArticleSitemapActiveColor: #999999;
@styleArticleSitemapHoverColor: #000;
				',
				'content_highlight_color' => 'rgb(78, 127, 168)',
		);

		$this->db->prepare("UPDATE __styles :p WHERE template_path='eqdkp_modern' ")->set($arrSet)->execute();

		$this->pdh->enqueue_hook('styles_update');

		//Reset Template Cache
		$objStyles = register('styles');
		$objStyles->deleteStyleCache('eqdkp_modern');

		// 2.1.0 Update 8
		$this->ntfy->addNotificationType('calendarevent_invitation','notification_calendarevent_invitation', 'calendarevent', 0, 1, 0, '', 0, 'fa-envelope');
		$this->config->set('calendar_raidleader_autoinvite', 1);

		//Reset Repository
		$this->pdh->put('repository', 'reset', array());

		$this->pdh->process_hook_queue();
		return true;
	}
}
