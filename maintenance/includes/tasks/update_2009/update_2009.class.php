<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2011
 * Date:		$Date: 2014-10-06 23:44:01 +0200 (Mo, 06 Okt 2014) $
 * -----------------------------------------------------------------------
 * @author		$Author: godmod $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 14643 $
 *
 * $Id: update_2000.class.php 14643 2014-10-06 21:44:01Z godmod $
 */

if ( !defined('EQDKP_INC') ){
  header('HTTP/1.0 404 Not Found');exit;
}

include_once(registry::get_const('root_path').'maintenance/includes/sql_update_task.class.php');

class update_2009 extends sql_update_task {
	public $author			= 'GodMod';
	public $version			= '2.0.0.9'; //new plus-version
	public $ext_version		= '2.0.0'; //new plus-version
	public $name			= '2.0.0 Update 1';
	
	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_2009'		=> 'EQdkp Plus 2.0 Update 1',
					1 => 'Alter Usergroup Table',
				),
			'german' => array(
				'update_2009'		=> 'EQdkp Plus 2.0 Update 1',
					1 => 'Erweitere Benutzergruppen-Tabelle',
			),
		);
		
		// init SQL querys
		$this->sqls = array(
			1 => "ALTER TABLE `__groups_user` ADD COLUMN `groups_user_team` TINYINT(1) NOT NULL DEFAULT '0';"
		);
	}
	
}


?>