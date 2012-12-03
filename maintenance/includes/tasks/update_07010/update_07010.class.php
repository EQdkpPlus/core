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

class update_07010 extends sql_update_task {
  public $author = 'wallenium';
  public $version = '0.7.0.10'; //new plus-version
  public $name = '0.7.0.10 Update';

  public function __construct(){
  	parent::__construct();
  	
  	$this->langs = array(
  		'english' => array(
  			'update_07010' => 'eqDKP Plus 0.7.0.10 update package',
  			'task01' => 'Drop outdated lists field in profile fields table',
			'task02' => 'Add undeletable field to profile fields table',
		),
		'german' => array(
			'update_07010' => 'eqDKP Plus 0.7.0.10 Update Paket',
			'task01' => 'Entferne veraltetes lists aus der profile fields Tabelle',
			'task02' => 'Füge undeletable Feld zur profile fields Tabelle hinzu',
		),
	);

    $this->sqls = array(
				'task01'	=> "ALTER TABLE __member_profilefields DROP `list`",
				'task02'	=> "ALTER TABLE __member_profilefields ADD `undeletable` enum('0','1') NOT NULL default '0' AFTER `enabled`",
    );
  }
}
?>