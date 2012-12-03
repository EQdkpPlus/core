<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2011
 * Date:		$Date: 2011-07-14 23:32:30 +0200 (Do, 14. Jul 2011) $
 * -----------------------------------------------------------------------
 * @author		$Author: wallenium $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 10679 $
 *
 * $Id: update_07067.class.php 10679 2011-07-14 21:32:30Z wallenium $
 */

if ( !defined('EQDKP_INC') ){
  header('HTTP/1.0 404 Not Found');exit;
}

include_once(registry::get_const('root_path').'maintenance/includes/sql_update_task.class.php');

class update_07067 extends sql_update_task {
	public $author		= 'GodMod';
	public $version		= '0.7.0.67'; //new plus-version
	public $name		= '0.7.0.67 Developer-Update';

	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_07067'		=> 'eqDKP Plus 0.7.0.67 Developer-Update package',
				'task01'			=> 'Alter session table',
			),
			'german' => array(
				'update_07067'		=> 'eqDKP Plus 0.7.0.67 Developer-Update package',
				'task01'			=> 'Alter sesion table',
			),
		);

		$this->sqls = array(
			'task01' => "ALTER TABLE `__sessions` CHANGE COLUMN `session_id` `session_id` VARCHAR(40) NOT NULL COLLATE 'utf8_bin';",
		);
	}
}

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_update_07067', update_07067::__shortcuts());
?>