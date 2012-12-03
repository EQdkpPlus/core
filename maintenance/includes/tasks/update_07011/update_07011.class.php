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

class update_07011 extends sql_update_task {
  public $author = 'hoofy';
  public $version = '0.7.0.11'; //new plus-version
  public $name = '0.7.0.11 Update';

  public function __construct(){
  	parent::__construct();
  	
  	$this->langs = array(
  		'english' => array(
  			'update_07011' => 'eqDKP Plus 0.7.0.11 update package',
  			'task01' => 'Add Column for item-color',
		),
		'german' => array(
			'update_07011' => 'eqDKP Plus 0.7.0.11 Update Paket',
			'task01' => 'Spalte für Itemfarbe hinzugefügt.',
		),
	);

    $this->sqls = array(
				'task01'	=> "ALTER TABLE __items ADD `item_color` VARCHAR(20) DEFAULT NULL AFTER `itempool_id`;",
    );
  }
}
?>