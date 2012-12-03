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

class update_0709 extends sql_update_task {
  public $author = 'godmod';
  public $version = '0.7.0.9'; //new plus-version
  public $name = '0.7.0.9 Update';

  public function __construct(){
  	parent::__construct();

    $this->sqls = array(
				'task01'	=> "DROP TABLE __style_config",
				'task02'	=> "ALTER TABLE __styles ADD `use_db_vars` TINYINT(1) UNSIGNED NULL",
				'task03'	=> "ALTER TABLE __styles ADD `css_file` VARCHAR(255) NULL",
				'task04'	=> "ALTER TABLE __styles ADD `background_img` VARCHAR(255) NULL",
				'task05'	=> "ALTER TABLE __styles ADD `logo_path` VARCHAR(255) DEFAULT 'logo.gif' NOT NULL",				
				'task06'	=> "ALTER TABLE __styles ADD `attendees_columns` ENUM('1','2','3','4','5','6','7','8','9','10') DEFAULT '6' NOT NULL",
				
    );
  }
}
?>
