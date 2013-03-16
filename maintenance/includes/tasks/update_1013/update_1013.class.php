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
 * $Id: update_1013.class.php 12818 2013-01-12 22:08:06Z wallenium $
 */

if ( !defined('EQDKP_INC') ){
  header('HTTP/1.0 404 Not Found');exit;
}

include_once(registry::get_const('root_path').'maintenance/includes/sql_update_task.class.php');

class update_1013 extends sql_update_task {
	public $author		= 'GodMod';
	public $version		= '1.0.13'; //new plus-version
	public $name		= '1.0 RC5';
	
	public static function __shortcuts() {
		$shortcuts = array('time');
		return array_merge(parent::__shortcuts(), $shortcuts);
	}
	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_1013'		=> 'EQdkp Plus 1.0 RC5 Update 2',
				'task01'			=> 'Alter item table',
			),
			'german' => array(
				'update_1013'		=> 'EQdkp Plus 1.0 RC5 Update 2',
				'task01'			=> 'Alter item table',
			),
		);
	
		$this->sqls = array(
			'task01' => "ALTER TABLE `__items` CHANGE COLUMN `game_itemid` `game_itemid` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8_bin';",
		);
	
	}
}

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_update_1013', update_1013::__shortcuts());
?>