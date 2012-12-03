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

class update_0703 extends sql_update_task {
  public $author = 'godmod';
  public $version = '0.7.0.3'; //new plus-version
  public $name = '0.7.0.3 Update';

  public function __construct() {
  	parent::__construct();
    $this->sqls = array(
      'task00' => "ALTER TABLE __users CHANGE `user_password` `user_password` VARCHAR(128) NOT NULL;",
      'task01' => "ALTER TABLE __sessions ADD `session_browser` VARCHAR(255) DEFAULT NULL NULL AFTER `session_ip`;",
			'task02' => "ALTER TABLE __users CHANGE `user_newpassword` `user_newpassword` VARCHAR(128) NOT NULL;",

    );
  }
}
?>