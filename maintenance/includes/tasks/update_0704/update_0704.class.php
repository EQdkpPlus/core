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

class update_0704 extends sql_update_task {
  public $author = 'godmod';
  public $version = '0.7.0.4'; //new plus-version
  public $name = '0.7.0.4 Update';

  public function __construct(){
  	parent::__construct();

    $this->sqls = array(
      'task00' => "CREATE TABLE __pages (
	`page_id` mediumint(8) unsigned NOT NULL auto_increment,
	`page_title` varchar(255) NOT NULL,
	`page_alias` varchar(255) DEFAULT NULL NULL,
	`page_content` text,
	`page_ise` enum('0','1') NOT NULL default '0',
	`page_ace` enum('0','1') NOT NULL default '0',
	`page_visibility` enum('0','1', '2') NOT NULL default '0',
	`page_menu_link` varchar(255) NOT NULL,
	`page_edit_user` smallint(5) NOT NULL,
	`page_edit_date` int(11) NOT NULL,

	PRIMARY KEY  (`page_id`)
);",
      'task01' => "INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_infopages_man','N');",
			'task02' => "INSERT INTO __auth_options (auth_value, auth_default) VALUES ('u_infopages_view','Y');",
      'task03' => "CREATE TABLE __repository (
					id varchar(255) NOT NULL,
					plugin varchar(255) NOT NULL,
					name varchar(255) DEFAULT NULL,
					date varchar(255) DEFAULT NULL,
					author varchar(255) DEFAULT NULL,
					description text DEFAULT NULL,
					shortdesc varchar(255) DEFAULT NULL,
					title varchar(255) DEFAULT NULL,
					version varchar(255) DEFAULT NULL,
					category varchar(255) DEFAULT NULL,
					level varchar(255) DEFAULT NULL,
					changelog text DEFAULT NULL,
					download varchar(255) DEFAULT NULL,
					filesize varchar(255) DEFAULT NULL,
					build varchar( 255 ) DEFAULT NULL,
					updated varchar(255) DEFAULT NULL,
					PRIMARY KEY  (id)
				);",
				'task04' => "DROP TABLE IF EXISTS eqdkp_logs;",
				'task05' => "CREATE TABLE __logs (
                  `log_id` int(11) unsigned NOT NULL auto_increment,
                  `log_date` int(11) NOT NULL default '0',
                  `log_value` text NOT NULL default '',
                  `log_ipaddress` varchar(15) NOT NULL default '',
                  `log_sid` varchar(32) NOT NULL default '',
                  `log_result` varchar(255) NOT NULL default '',
                  `log_tag` varchar(255) NOT NULL default '',
                  `log_plugin` varchar(255) NOT NULL default '',
									`log_flag` smallint(3) NOT NULL default '0',
                  `user_id` smallint(5) NOT NULL default '0',
                PRIMARY KEY  (`log_id`),
                KEY `user_id` (`user_id`),
                KEY `log_tag` (`log_tag`),
                KEY `log_ipaddress` (`log_ipaddress`)
              ) TYPE=MyISAM;",

    );
  }
}
?>