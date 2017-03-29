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

class update_230014 extends sql_update_task {
	public $author			= 'GodMod';
	public $version			= '2.3.0.14'; //new plus-version
	public $ext_version		= '2.3.0'; //new plus-version
	public $name			= 'Update 2.3.0.14';

	public function __construct(){
		parent::__construct();

		// They are all included in Update 2300 - This update must be deleted before release
		$this->langs = array(
			'english' => array(
				'update_230014'	=> 'EQdkp Plus 2.3.0.14 (Remove before Release)',
				1	=> 'Change table',
				2	=> 'Add Profilefield',
				3	=> 'Add Profilefield',
				4	=> 'Add Profilefield',
			),
			'german' => array(
				'update_230014'	=> 'EQdkp Plus 2.3.0.14 (Remove before Release)',
				1	=> 'Ändere Events-Tabelle',
				2	=> 'Erstelle Profilfeld',
				3	=> 'Erstelle Profilfeld',
				4	=> 'Erstelle Profilfeld',
			),
		);

		// init SQL querys
		//TODO: They are all included in Update 2300 - This update must be deleted before release
		$this->sqls = array(
			1	=> "ALTER TABLE `__user_profilefields` ADD COLUMN `example` VARCHAR(255) NULL COLLATE 'utf8_bin'",
			2	=> "INSERT INTO `__user_profilefields` (`name`, `lang_var`, `type`, `length`, `minlength`, `validation`, `required`, `show_on_registration`, `enabled`, `sort_order`, `is_contact`, `contact_url`, `icon_or_image`, `bridge_field`, `options`, `editable`, `example`) VALUES ('birthday', 'userpf_birthday', 'birthday', 50, 0, '', 0, 0, 1, 1, 0, NULL, NULL, NULL, NULL, 1, NULL);",
			3	=> "INSERT INTO `__user_profilefields` (`name`, `lang_var`, `type`, `length`, `minlength`, `validation`, `required`, `show_on_registration`, `enabled`, `sort_order`, `is_contact`, `contact_url`, `icon_or_image`, `bridge_field`, `options`, `editable`, `example`) VALUES ('country', 'userpf_country', 'country', 50, 0, '', 0, 0, 1, 1, 0, NULL, NULL, NULL, NULL, 1, NULL);",
			4	=> "INSERT INTO `__user_profilefields` (`name`, `lang_var`, `type`, `length`, `minlength`, `validation`, `required`, `show_on_registration`, `enabled`, `sort_order`, `is_contact`, `contact_url`, `icon_or_image`, `bridge_field`, `options`, `editable`, `example`) VALUES ('gender', 'userpf_gender', 'gender', 50, 0, '', 0, 0, 1, 1, 0, '', '', NULL, 'a:1:{s:7:\"options\";a:3:{s:1:\"m\";s:8:\"gender_m\";s:1:\"f\";s:8:\"gender_f\";s:1:\"n\";s:8:\"gender_n\";}}', 1, '');",	
		);
	}

}

?>
