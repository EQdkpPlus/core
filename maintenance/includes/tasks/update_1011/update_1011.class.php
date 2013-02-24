<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2011
 * Date:		$Date: 2013-01-12 23:08:06 +0100 (Sa, 12. Jan 2013) $
 * -----------------------------------------------------------------------
 * @author		$Author: wallenium $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 12818 $
 *
 * $Id: update_1011.class.php 12818 2013-01-12 22:08:06Z wallenium $
 */

if ( !defined('EQDKP_INC') ){
  header('HTTP/1.0 404 Not Found');exit;
}

include_once(registry::get_const('root_path').'maintenance/includes/sql_update_task.class.php');

class update_1011 extends sql_update_task {
	public $author		= 'GodMod';
	public $version		= '1.0.11'; //new plus-version
	public $name		= '1.0 RC4';
	
	public static function __shortcuts() {
		$shortcuts = array('time');
		return array_merge(parent::__shortcuts(), $shortcuts);
	}
	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_1011'		=> 'EQdkp Plus 1.0 RC4 Update 1',
				'task01'			=> 'Alter repository table',
			),
			'german' => array(
				'update_1011'		=> 'EQdkp Plus 1.0 RC4 Update 1',
				'task01'			=> 'Alter repository table',
			),
		);
	
		$this->sqls = array(
			'task01' => "ALTER TABLE `__repository` CHANGE COLUMN `build` `dep_php` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_bin';",
		);
	
	}
}

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_update_1011', update_1011::__shortcuts());
?>