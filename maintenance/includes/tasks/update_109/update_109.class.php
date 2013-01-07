<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2011
 * Date:		$Date: 2012-11-30 20:35:42 +0100 (Fr, 30. Nov 2012) $
 * -----------------------------------------------------------------------
 * @author		$Author: hoofy_leon $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 12519 $
 *
 * $Id: update_107.class.php 12519 2012-11-30 19:35:42Z hoofy_leon $
 */

if ( !defined('EQDKP_INC') ){
  header('HTTP/1.0 404 Not Found');exit;
}

include_once(registry::get_const('root_path').'maintenance/includes/sql_update_task.class.php');

class update_109 extends sql_update_task {
	public $author		= 'GodMod';
	public $version		= '1.0.9'; //new plus-version
	public $name		= '1.0 RC2 Update 2';
	
	public static function __shortcuts() {
		$shortcuts = array('time');
		return array_merge(parent::__shortcuts(), $shortcuts);
	}
	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_109'		=> 'EQdkp Plus 1.0 RC2 Update 2',
				'task01'			=> 'Alter repository table',
				'task02'			=> 'Alter session table',
			),
			'german' => array(
				'update_109'		=> 'EQdkp Plus 1.0 RC2 Update 2',
				'task01'			=> 'Alter repository table',
				'task02'			=> 'Alter session table',
			),
		);
	
		$this->sqls = array(
				'task01' => "ALTER TABLE `__repository`ADD COLUMN `version_ext` VARCHAR(255) NULL DEFAULT NULL;",
				'task02' => "ALTER TABLE `__sessions` CHANGE COLUMN `session_browser` `session_browser` TEXT NULL COLLATE 'utf8_bin';",
		);
	
	}
}

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_update_109', update_109::__shortcuts());
?>