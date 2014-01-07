<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2011
 * Date:		$Date: 2013-12-08 11:06:58 +0100 (So, 08 Dez 2013) $
 * -----------------------------------------------------------------------
 * @author		$Author: godmod $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 13783 $
 *
 * $Id: update_2003.class.php 13783 2013-12-08 10:06:58Z godmod $
 */

if ( !defined('EQDKP_INC') ){
  header('HTTP/1.0 404 Not Found');exit;
}

include_once(registry::get_const('root_path').'maintenance/includes/sql_update_task.class.php');

class update_2003 extends sql_update_task {
	public $author		= 'GodMod';
	public $version		= '2.0.0.3'; //new plus-version
	public $ext_version	= '2.0.0';
	public $name		= '2.0.0 Change Repo Column';

	public static function __shortcuts() {
		$shortcuts = array('time', 'config', 'routing', 'pdc', 'db');
		return array_merge(parent::__shortcuts(), $shortcuts);
	}

	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_2003'		=> 'EQdkp Plus 2.0 Repo update',
				1 => 'Rename shortdesc column',
			),
			'german' => array(
				'update_2003'		=> 'EQdkp Plus 2.0 Repo Update',
				1 => 'Rename shortdesc column',
			),
		);
		
		// init SQL querys
		$this->sqls = array(
			1 => "ALTER TABLE `__repository`
	CHANGE COLUMN `shortdesc` `description` TEXT NULL COLLATE 'utf8_bin';",
	
		);

	}

}
?>