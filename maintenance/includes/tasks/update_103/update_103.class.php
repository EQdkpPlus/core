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

class update_103 extends sql_update_task {
	public $author		= 'GodMod';
	public $version		= '1.0.3'; //new plus-version
	public $name		= '1.0.3 Update';

	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_103'		=> 'EQdkp Plus 1.0.3 Update',
				'task01'			=> 'Alter Logs Table',
			),
			'german' => array(
				'update_103'		=> 'EQdkp Plus 1.0.3 Update',
				'task01'			=> 'Verändere Logs-Tabelle',
			),
		);

		$this->sqls = array(
			'task01' => "ALTER TABLE `__logs` CHANGE COLUMN `log_ipaddress` `log_ipaddress` VARCHAR(40) NOT NULL DEFAULT '' COLLATE 'utf8_bin';",
		);
	}
}

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_update_103', update_103::__shortcuts());
?>