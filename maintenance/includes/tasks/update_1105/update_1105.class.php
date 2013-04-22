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

class update_1105 extends sql_update_task {
	public $author		= 'GodMod';
	public $version		= '1.1.0.5'; //new plus-version
	public $name		= '1.1.0 Update 5';

	public static function __shortcuts() {
		$shortcuts = array('time');
		return array_merge(parent::__shortcuts(), $shortcuts);
	}
	
	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_1105'		=> 'EQdkp Plus 1.1.0 Update 5',
				 1 => 'Add portal_block Table',
				 2 => 'Add portal_layout Table',
				 3 => 'Add articles Table',
				 4 => 'Add article_categories Table',
				 5 => 'Populate portal_layout Table',
				 6 => 'Populate article_categories Table',
				 7 => 'Populate article_categories Table',
				 8 => 'Populate articles Table',
				 'update_function' => 'Set Start Page',
			),
			'german' => array(
				'update_1105'		=> 'EQdkp Plus 1.1.0 Update 5',
				 1 => 'Add portal_block Table',
				 2 => 'Add portal_layout Table',
				 3 => 'Add articles Table',
				 4 => 'Add article_categories Table',
				 5 => 'Populate portal_layout Table',
				 6 => 'Populate article_categories Table',
				 7 => 'Populate article_categories Table',
				 8 => 'Populate articles Table',
				 'update_function' => 'Set Start Page',
			),
		);
		
		// init SQL querys
		  $this->sqls = array(
			1 => "CREATE TABLE `__portal_blocks` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`wide_content` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_bin;",
			2 => "CREATE TABLE `__portal_layouts` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`blocks` TEXT NOT NULL,
	`modules` TEXT NOT NULL,
	PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_bin;",
			3 => "CREATE TABLE `__articles` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`text` TEXT NOT NULL,
	`category` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`featured` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
	`comments` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
	`votes` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
	`published` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
	`show_from` VARCHAR(11) COLLATE utf8_bin NOT NULL DEFAULT '',
	`show_to` VARCHAR(11) COLLATE utf8_bin NOT NULL DEFAULT '',
	`user_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`date` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`previewimage` TEXT NOT NULL,
	`alias` VARCHAR(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`hits` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`sort_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`tags` TEXT NOT NULL,
	`votes_count` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`votes_sum` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`votes_users` TEXT NOT NULL,
	`last_edited` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`last_edited_user` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`system` VARCHAR(255) COLLATE utf8_bin NULL DEFAULT '',
	PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_bin;",
			4 => "CREATE TABLE `__article_categories` (
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
	PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_bin;",
			5 => "INSERT INTO `__portal_layouts` (`id`, `name`, `blocks`, `modules`) VALUES (1, 'Standard', 'a:4:{i:0;s:4:\"left\";i:1;s:6:\"middle\";i:2;s:6:\"bottom\";i:3;s:5:\"right\";}', 'a:0:{}');",
			6 => "INSERT INTO `__article_categories` (`id`, `name`, `alias`, `portal_layout`, `description`, `per_page`, `permissions`, `published`, `parent`, `sort_id`, `list_type`, `aggregation`, `featured_only`, `notify_on_onpublished_articles`, `social_share_buttons`, `show_childs`, `article_published_state`, `hide_header`, `sortation_type`, `featured_ontop`) VALUES (1, 'System', 'system', 1, '', 25, 'a:5:{s:3:\"rea\";a:6:{i:2;s:1:\"1\";i:3;s:1:\"1\";i:4;s:1:\"1\";i:5;s:1:\"1\";i:6;s:1:\"1\";i:1;s:1:\"1\";}s:3:\"cre\";a:6:{i:2;s:1:\"1\";i:3;s:1:\"1\";i:4;s:2:\"-1\";i:5;s:2:\"-1\";i:6;s:1:\"0\";i:1;s:2:\"-1\";}s:3:\"upd\";a:6:{i:2;s:1:\"1\";i:3;s:1:\"1\";i:4;s:2:\"-1\";i:5;s:2:\"-1\";i:6;s:1:\"0\";i:1;s:2:\"-1\";}s:3:\"del\";a:6:{i:2;s:1:\"1\";i:3;s:1:\"1\";i:4;s:2:\"-1\";i:5;s:2:\"-1\";i:6;s:1:\"0\";i:1;s:2:\"-1\";}s:3:\"chs\";a:6:{i:2;s:1:\"1\";i:3;s:1:\"1\";i:4;s:2:\"-1\";i:5;s:2:\"-1\";i:6;s:1:\"0\";i:1;s:2:\"-1\";}}', 1, 0, 99999999, 1, 'a:0:{}', 0, 0, 0, 0, 1, 0, 1, 0);",
			7 => "INSERT INTO `__article_categories` (`id`, `name`, `alias`, `portal_layout`, `description`, `per_page`, `permissions`, `published`, `parent`, `sort_id`, `list_type`, `aggregation`, `featured_only`, `notify_on_onpublished_articles`, `social_share_buttons`, `show_childs`, `article_published_state`, `hide_header`, `sortation_type`, `featured_ontop`) VALUES (2, 'News', 'news', 1, '', 15, 'a:5:{s:3:\"rea\";a:6:{i:2;s:2:\"-1\";i:3;s:2:\"-1\";i:4;s:2:\"-1\";i:5;s:2:\"-1\";i:6;s:2:\"-1\";i:1;s:2:\"-1\";}s:3:\"cre\";a:6:{i:2;s:2:\"-1\";i:3;s:2:\"-1\";i:4;s:2:\"-1\";i:5;s:2:\"-1\";i:6;s:1:\"1\";i:1;s:2:\"-1\";}s:3:\"upd\";a:6:{i:2;s:2:\"-1\";i:3;s:2:\"-1\";i:4;s:2:\"-1\";i:5;s:2:\"-1\";i:6;s:1:\"1\";i:1;s:2:\"-1\";}s:3:\"del\";a:6:{i:2;s:2:\"-1\";i:3;s:2:\"-1\";i:4;s:2:\"-1\";i:5;s:2:\"-1\";i:6;s:1:\"1\";i:1;s:2:\"-1\";}s:3:\"chs\";a:6:{i:2;s:2:\"-1\";i:3;s:2:\"-1\";i:4;s:2:\"-1\";i:5;s:2:\"-1\";i:6;s:1:\"1\";i:1;s:2:\"-1\";}}', 1, 1, 99999999, 1, 'a:1:{i:0;s:1:\"2\";}', 0, 0, 0, 0, 1, 1, 1, 0);",	  
			8 => "INSERT INTO `__articles` (`id`, `title`, `text`, `category`, `featured`, `comments`, `votes`, `published`, `show_from`, `show_to`, `user_id`, `date`, `previewimage`, `alias`, `hits`, `sort_id`, `tags`, `votes_count`, `votes_sum`, `votes_users`, `last_edited`, `last_edited_user`, `system`) VALUES 
		(1, 'Welcome to EQdkp Plus', 'Welcome to EQdkp Plus!', 2, 1, 1, 0, 1, '', '', 1, ".time().", '', 'welcome', 0, 0, 'a:1:{i:0;s:0:\"\";}', 0, 0, '', ".time().", 1, '');",
		  );
	}
	
	public function update_function() {
		register('config')->set( 'start_page' , 'news');
		return true;
	}


}
?>