<?php
 /*
 * Project:     EQdkp Plus Patcher
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		    http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2007-2009 sz3
 * @link        http://eqdkp-plus.com
 * @package     plus patcher
 * @version     $Rev$
 *
 * $Id$
 */

if ( !defined('EQDKP_INC') ){
  header('HTTP/1.0 404 Not Found');exit;
}

include_once($eqdkp_root_path.'maintenance/includes/sql_update_task.class.php');

class update_0702 extends sql_update_task {
  public $author = 'godmod';
  public $version = '0.7.0.2'; //new plus-version
  public $name = '0.7.0.2 Update';

  public function __construct() {
  	parent::__construct();
    $this->sqls = array(
      'task00' => "ALTER TABLE __plus_links ADD `link_sortid` INT UNSIGNED DEFAULT '0' NULL AFTER `link_menu` ;",
      'task01' => "INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_maintenance','N');",
      'task02' => "INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_reset','N');",
      'task03' => "INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_logs_del','N');",
      'task04' => "INSERT INTO __auth_options (auth_value, auth_default) VALUES ('u_userlist','N');",
      'task05' => "DELETE FROM __auth_options WHERE auth_value = 'a_turnin_add'",
      'task06' => "CREATE TABLE IF NOT EXISTS __auth_groups (
                    `group_id` smallint(5) unsigned NOT NULL,
                    `auth_id` smallint(3) unsigned NOT NULL,
                    `auth_setting` enum('N','Y') NOT NULL DEFAULT 'N',
                    KEY `auth_id` (`auth_id`),
                    KEY `group_id` (`group_id`)
                  )",
     'task07' => "DROP TABLE IF EXISTS __groups_user",
     'task08' => "CREATE TABLE IF NOT EXISTS __groups_user (
                    `groups_user_id` int(11) NOT NULL AUTO_INCREMENT,
                    `groups_user_name` varchar(255) NOT NULL,
                    `groups_user_desc` varchar(255) NOT NULL,
                    `groups_user_deletable` enum('0','1') NOT NULL DEFAULT '0',
                    `groups_user_default` enum('0','1') NOT NULL DEFAULT '0',
                    PRIMARY KEY (`groups_user_id`)
                  )",
      'task09' => "INSERT INTO __groups_user (`groups_user_id`, `groups_user_name`, `groups_user_desc`, `groups_user_deletable`, `groups_user_default`) VALUES
                   (1,'Gast','Gästegruppe','0', '0'),
                   (3,'Administratoren','Administratoren haben keine Rechte, die das System zerstören können','0', '0'),
                   (2,'Super-Administratoren','Super-Admins besitzen alle Rechte','0', '0'),
                   (4,'Mitglieder','','0', '1')
                 ",
      'task10' => "CREATE TABLE IF NOT EXISTS __groups_users (
                    `group_id` int(22) NOT NULL,
                    `user_id` int(22) NOT NULL,
                    `status` int(2) NOT NULL
                  )",
      'task11' => "ALTER TABLE __auth_options CHANGE `auth_id` `auth_id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT ",
      'task12' => "INSERT INTO __groups_users (group_id, user_id, status) VALUES (2,1,0)",
      'task13' => "CREATE TABLE IF NOT EXISTS __news_categories (
                    `category_id` int(10) NOT NULL AUTO_INCREMENT,
                    `category_name` text,
                    `category_icon` text,
                    `category_color` varchar(255) DEFAULT NULL,
                    PRIMARY KEY (`category_id`)
                    )",
      'task14' => "INSERT INTO __news_categories (`category_id`, `category_name`) VALUES ('1','Default');",
      'task15' => "ALTER TABLE __news ADD `news_category` INT UNSIGNED DEFAULT '1' NULL AFTER `news_flags` ;",
			'task16' => "INSERT INTO __config (config_name, config_value) VALUES ('pk_enable_captcha', 0);",
    );
  }
}
?>