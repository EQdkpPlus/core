<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2011
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 *
 * $Id$
 */

if ( !defined('EQDKP_INC') ){
  header('HTTP/1.0 404 Not Found');exit;
}

include_once(registry::get_const('root_path').'maintenance/includes/sql_update_task.class.php');

class update_1103 extends sql_update_task {
	public $author		= 'Wallenium';
	public $version		= '1.1.0.3'; //new plus-version
	public $name		= '1.1.0 Update 3';

	public static function __shortcuts() {
		$shortcuts = array('time');
		return array_merge(parent::__shortcuts(), $shortcuts);
	}
	
	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_1103'		=> 'EQdkp Plus 1.1.0 Update 3',
				 1 => 'Alter calendars table',
				 2 => 'Set main raid calendar to restricted, operators only',
				 3 => 'Add new Userraids raid calendar'
			),
			'german' => array(
				'update_1103'		=> 'EQdkp Plus 1.1.0 Update 3',
				1 => 'Erweitere calendars Tabelle',
				2 => 'Setze den Hauptraidkalender auf "Nur für Operatoren"',
				3 => 'Füge neuen Userraids Kalender hinzu'
			),
		);
		
		// init SQL querys
		  $this->sqls = array(
			  1 => "ALTER TABLE `__calendars` ADD COLUMN `restricted` TINYINT(1) NOT NULL DEFAULT '0';",
			  2 => "UPDATE `__calendars` SET `restricted` = '1' WHERE `id` = '1';",
			  3 => "INSERT INTO `__calendars` (`name`, `color`, `private`, `feed`, `system`, `type`, `restricted`) VALUES ('Userraids', '#0cb20f', '0', NULL, '1', '1', '0');"
		  );
	}


}
?>