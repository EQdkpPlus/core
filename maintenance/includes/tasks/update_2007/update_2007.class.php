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
 * @copyright	2007-2011 EQdkp-Plus Developer Team
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

class update_2007 extends sql_update_task {
	public $author			= 'GodMod';
	public $version			= '2.0.0.7'; //new plus-version
	public $ext_version		= '2.0.0'; //new plus-version
	public $name			= 'Update multidkp table';
	
	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_2007'		=> 'Update multidkp table table',
				1 => 'Update multidkp table table',
			),
		);
		
		// init SQL querys
		$this->sqls = array(
			1 	=> "ALTER TABLE `__multidkp` ADD COLUMN `multidkp_sortid` INT(11) UNSIGNED NULL DEFAULT '0' AFTER `multidkp_desc`;",
		);
	}
}
?>