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

class update_2006 extends sql_update_task {
	public $author			= 'Hoofy';
	public $version			= '2.0.0.6'; //new plus-version
	public $ext_version		= '2.0.0'; //new plus-version
	public $name			= 'Update profile-fields table and member-table';
	
	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_2006'		=> 'Update profile-fields table',
				1 => 'Rename visible to sort in profilefields table',
				2 => 'Remove race, class and level column from member table',
			),
		);
		
		// init SQL querys
		$this->sqls = array(
			1 	=> 'ALTER TABLE __member_profilefields CHANGE `visible` `sort` SMALLINT( 2 ) UNSIGNED NULL DEFAULT \'1\';',
			2	=> 'ALTER TABLE __members DROP `member_level`, DROP `member_race_id`, DROP `member_class_id`;'
		);
	}
}
?>