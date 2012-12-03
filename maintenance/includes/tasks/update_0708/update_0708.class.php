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

class update_0708 extends sql_update_task {
  public $author = 'godmod';
  public $version = '0.7.0.8'; //new plus-version
  public $name = '0.7.0.8 Update';

  public function __construct(){
  	parent::__construct();

    $this->sqls = array(
				'task01'	=> "ALTER TABLE __styles ADD `style_code` VARCHAR(50) DEFAULT '' NOT NULL",
				'task02'	=> "ALTER TABLE __styles ADD `style_version` VARCHAR(7) DEFAULT '' NULL",
				'task03'	=> "ALTER TABLE __styles ADD `style_contact` VARCHAR(100) DEFAULT '' NULL",
				'task04'	=> "ALTER TABLE __styles ADD `style_author` VARCHAR(100) DEFAULT '' NULL",
				'task05'	=> "ALTER TABLE __styles ADD `enabled` enum('0','1') NOT NULL default '0'",
				
				'task06'	=> "UPDATE __styles SET style_code ='luna_wotlk' WHERE style_name='luna_wotlk'",
				
    );
  }
}
?>
