<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2011
 * Date:		$Date: 2013-03-03 18:38:23 +0100 (So, 03 Mrz 2013) $
 * -----------------------------------------------------------------------
 * @author		$Author: wallenium $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 13156 $
 *
 * $Id: update_1105.class.php 13156 2013-03-03 17:38:23Z wallenium $
 */

if ( !defined('EQDKP_INC') ){
  header('HTTP/1.0 404 Not Found');exit;
}

include_once(registry::get_const('root_path').'maintenance/includes/sql_update_task.class.php');

class update_1107 extends sql_update_task {
	public $author		= 'GodMod';
	public $version		= '1.1.0.7'; //new plus-version
	public $name		= '1.1.0 Update 7';

	public static function __shortcuts() {
		$shortcuts = array('time');
		return array_merge(parent::__shortcuts(), $shortcuts);
	}
	
	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_1107'		=> 'EQdkp Plus 1.1.0 Update 7',
				 1 => 'Alter comments table',

			),
			'german' => array(
				'update_1107'		=> 'EQdkp Plus 1.1.0 Update 7',
					 1 => 'Alter comments table',

			),
		);
		
		// init SQL querys
		  $this->sqls = array(
			1 => "ALTER TABLE `__comments` ADD COLUMN `reply_to` INT(11) UNSIGNED NOT NULL DEFAULT '0';",
			);
	}
	


}
?>