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

class update_0705 extends sql_update_task {
  public $author = 'godmod';
  public $version = '0.7.0.5'; //new plus-version
  public $name = '0.7.0.5 Update';

  public function __construct(){
  	parent::__construct();

    $this->sqls = array(
				'task01'	=> "ALTER TABLE __users ADD `user_registered` INT(11) UNSIGNED DEFAULT '0' NOT NULL AFTER `user_lastpage` ;",
				'tast02'	=> "ALTER TABLE __users ADD `custom_fields` BLOB NULL AFTER `privacy_settings` ;",
				'task03'	=> "ALTER TABLE __plus_links ADD `link_visibility` TINYINT UNSIGNED DEFAULT '0' NOT NULL AFTER `link_sortid` ;",
				'task04'	=> "INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_users_comment_w','N');",
				'task05'	=> "INSERT INTO __auth_options (auth_value, auth_default) VALUES ('u_users_comment_r','N');",
				'task06'	=> "INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_sms_send','N');",
    );
  }
}
?>