<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2015 EQdkp-Plus Developer Team
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU Affero General Public License as published
 *	by the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU Affero General Public License for more details.
 *
 *	You should have received a copy of the GNU Affero General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if ( !defined('EQDKP_INC') ){
  header('HTTP/1.0 404 Not Found');exit;
}

include_once(registry::get_const('root_path').'maintenance/includes/sql_update_task.class.php');

class update_230013 extends sql_update_task {
	public $author			= 'GodMod';
	public $version			= '2.3.0.13'; //new plus-version
	public $ext_version		= '2.3.0'; //new plus-version
	public $name			= 'Update 2.3.0.13';

	public function __construct(){
		parent::__construct();

// combined update 2.3.0.1 to 2.3.0.11
		$this->langs = array(
			'english' => array(
				'update_230013'	=> 'EQdkp Plus 2.3.0.13',
				1	=> 'Change events table',
				2	=> 'Change style table',
			),
			'german' => array(
				'update_230013'	=> 'EQdkp Plus 2.3.0.13',
				1	=> 'Ändere Events-Tabelle',
				2	=> 'Ändere Styles-Tabelle',
			),
		);

		// init SQL querys
		$this->sqls = array(
			1	=> "ALTER TABLE `__events` ADD COLUMN `default_itempool` INT(11) UNSIGNED NOT NULL DEFAULT '0';",
			2	=> "ALTER TABLE `__styles` ADD `editor_theme` VARCHAR(255) NULL DEFAULT 'lightgray';",
		);
	}

}

?>
