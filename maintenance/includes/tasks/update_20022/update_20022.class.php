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

class update_20022 extends sql_update_task {
	public $author			= 'GodMod';
	public $version			= '2.0.0.22'; //new plus-version
	public $ext_version		= '2.0.0'; //new plus-version
	public $name			= '2.0.0 Update Beta5';
	
	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_20022'	=> 'EQdkp Plus 2.0 Update Beta5',
					1			=> 'Alter Styles Table',
					2			=> 'Alter Styles Table',
				),
			'german' => array(
				'update_20022'	=> 'EQdkp Plus 2.0 Update Beta5',
					1			=> 'Alter Styles Table',
					2			=> 'Alter Styles Table',
			),
		);
		
		// init SQL querys
		$this->sqls = array(
			1 => "ALTER TABLE `__styles` ADD COLUMN `background_pos` VARCHAR(20) NULL DEFAULT 'normal';",
			2 => "ALTER TABLE `__styles` ADD COLUMN `background_type` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0';",
		);
	}
}


?>