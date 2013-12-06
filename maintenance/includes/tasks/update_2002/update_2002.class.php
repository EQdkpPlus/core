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

class update_2002 extends sql_update_task {
	public $author		= 'WalleniuM';
	public $version		= '2.0.0.2'; //new plus-version
	public $ext_version	= '2.0.0';
	public $name		= '2.0.0 Add raid groups tables';

	public static function __shortcuts() {
		$shortcuts = array('time', 'config', 'routing', 'pdc', 'db');
		return array_merge(parent::__shortcuts(), $shortcuts);
	}

	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_2002'		=> 'EQdkp Plus 2.0 raidgroup update',
				1 => 'Add raid group table',
				2 => 'Add raid group user table',
				3 => 'Add default raid group',
			),
			'german' => array(
				'update_2002'		=> 'EQdkp Plus 2.0 Raidgruppen Update',
				1 => 'Add raid group table',
				2 => 'Add raid group user table',
				3 => 'Add default raid group',
			),
		);
		
		// init SQL querys
		$this->sqls = array(
			1 => "CREATE TABLE `__groups_raid` (
					`groups_raid_id` int(11) NOT NULL AUTO_INCREMENT,
					`groups_raid_name` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
					`groups_raid_desc` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
					`groups_raid_deletable` tinyint(1) NOT NULL DEFAULT '0',
					`groups_raid_default` tinyint(1) NOT NULL DEFAULT '0',
					`groups_raid_sortid` smallint(5) unsigned NOT NULL DEFAULT '0',
					PRIMARY KEY (`groups_raid_id`)
				)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;",
			2 => "CREATE TABLE `__groups_raid_users` (
					`group_id` int(22) NOT NULL,
					`user_id` int(22) NOT NULL,
					`grpleader` int(1) NOT NULL DEFAULT '0'
				)	DEFAULT CHARSET=utf8 COLLATE=utf8_bin;",
			3 => "INSERT INTO `__groups_raid` (`groups_raid_id`, `groups_raid_name`, `groups_raid_desc`, `groups_raid_deletable`, `groups_raid_default`, `groups_raid_sortid`)
VALUES (1, 'Default','',0,1,1);"
		);

	}

}
?>