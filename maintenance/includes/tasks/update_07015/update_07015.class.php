<?php
 /*
 * Project:     EQdkp Plus Patcher
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		    http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date: 2010-05-27 13:31:17 +0200 (Do, 27. Mai 2010) $
 * -----------------------------------------------------------------------
 * @author      $Author: wallenium $
 * @copyright   2007-2009 sz3
 * @link        http://eqdkp-plus.com
 * @package     plus patcher
 * @version     $Rev: 7900 $
 *
 * $Id: update_07012.class.php 7900 2010-05-27 11:31:17Z wallenium $
 */

if ( !defined('EQDKP_INC') ){
  header('HTTP/1.0 404 Not Found');exit;
}

include_once($eqdkp_root_path.'maintenance/includes/sql_update_task.class.php');

class update_07015 extends sql_update_task {
  public $author = 'GodMod';
  public $version = '0.7.0.15'; //new plus-version
  public $name = '0.7.0.15 Developer-Update';

  public function __construct(){
  	parent::__construct();
  	
  	$this->langs = array(
  		'english' => array(
  			'update_07015' => 'eqDKP Plus 0.7.0.15 Developer-Update package',
  			'task01' => 'Alter user-Table',
				'task02' => 'Alter user-Table',
				'task03' => 'create oauth-Table',
		),
		'german' => array(
			'update_07015' => 'eqDKP Plus 0.7.0.15 Developer-Update Paket',
			  'task01' => 'Erweitere user-Tabelle',
				'task02' => 'Erweitere user-Tabelle',
				'task03' => 'Erstelle oauth-Tabelle',
		),
	);

    $this->sqls = array(
				'task01'	=> "ALTER TABLE __users ADD `app_key` varchar(250) COLLATE utf8_bin DEFAULT NULL",
				'task02'	=> "ALTER TABLE __users ADD `app_use` tinyint(3) unsigned DEFAULT '0'",
				'task03'	=> "CREATE TABLE IF NOT EXISTS __oauth (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `token` text COLLATE utf8_bin,
  `secret` text COLLATE utf8_bin,
  `usergroups` text COLLATE utf8_bin,
  `time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=48 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;",
    );
  }
}
?>