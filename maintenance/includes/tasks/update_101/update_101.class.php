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

class update_101 extends sql_update_task {
	public $author		= 'GodMod';
	public $version		= '1.0.1'; //new plus-version
	public $name		= '1.0 beta 2 Developer-Update';

	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_101'		=> 'EQdkp Plus 1.0 beta 2 Update',
				'task01'			=> 'Alter member_ranks table',
			),
			'german' => array(
				'update_101'		=> 'EQdkp Plus 1.0 beta 2 Update',
				'task01'			=> 'Erweitere member_ranks Tabelle',
			),
		);

		$this->sqls = array(
			'task01' => "ALTER TABLE `__member_ranks` ADD COLUMN `rank_sortid` smallint(5) unsigned NOT NULL DEFAULT 0",
		);
	}
}

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_update_101', update_101::__shortcuts());
?>