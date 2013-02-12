<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2011
 * Date:		$Date: 2013-02-05 01:13:41 +0100 (Di, 05 Feb 2013) $
 * -----------------------------------------------------------------------
 * @author		$Author: wallenium $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 13003 $
 *
 * $Id: update_1102.class.php 13003 2013-02-05 00:13:41Z wallenium $
 */

if ( !defined('EQDKP_INC') ){
  header('HTTP/1.0 404 Not Found');exit;
}

include_once(registry::get_const('root_path').'maintenance/includes/sql_update_task.class.php');

class update_1102 extends sql_update_task {
	public $author		= 'GodMod';
	public $version		= '1.1.0.2'; //new plus-version
	public $name		= '1.1.0 Update 2';

	public static function __shortcuts() {
		$shortcuts = array('time');
		return array_merge(parent::__shortcuts(), $shortcuts);
	}
	
	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_1102'		=> 'EQdkp Plus 1.1.0 Update 2',
				 1 => 'Add new permission',
				 2 => 'Add new permission',
				 3 => 'Alter groups_users table'
			),
			'german' => array(
				'update_1102'		=> 'EQdkp Plus 1.1.0 Update 2',
				1 => 'Füge neue Berechtigung hinzu',
				2 => 'Füge neue Berechtigung hinzu',
				3 => 'Erweitere groups_users Tabelle'
			),
		);
		
		// init SQL querys
		  $this->sqls = array(
			  1 => "INSERT INTO `__auth_options` (`auth_value`, `auth_default`) VALUES ('a_usergroups_man', 'N');",
			  2 => "INSERT INTO `__auth_options` (`auth_value`, `auth_default`) VALUES ('a_usergroups_grpleader', 'N');",
			  3 => "ALTER TABLE `__groups_users` ADD COLUMN `grpleader` INT(1) NOT NULL DEFAULT '0';"
		  );
	}


}
?>