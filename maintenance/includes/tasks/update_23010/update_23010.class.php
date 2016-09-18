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

class update_23010 extends sql_update_task {
	public $author			= 'GodMod';
	public $version			= '2.3.0.10.0'; //new plus-version
	public $ext_version		= '2.3.0.10'; //new plus-version
	public $name			= 'Update 2.3.0.10';

	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_23010'	=> 'EQdkp Plus 2.3.0.10 Update',
					'update_function'	=> 'Save Crontab into Database',
					1			=> 'Add Cronjob Table',

				),
			'german' => array(
				'update_23010'	=> 'EQdkp Plus 2.3.0.10 Update',
					'update_function'	=> 'Save Crontab into Database',
					1			=> 'Add Cronjob Table',
			),
		);

		$this->sqls = array(
			1 => "CREATE TABLE `__cronjobs` (
				`id` VARCHAR(100) NOT NULL COLLATE 'utf8_bin',
				`start_time` INT(11) UNSIGNED NOT NULL DEFAULT '0',
				`repeat` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
				`repeat_type` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8_bin',
				`repeat_interval` INT(11) UNSIGNED NOT NULL,
				`extern` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
				`ajax` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
				`delay` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
				`multiple` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
				`active` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
				`editable` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
				`path` VARCHAR(255) NOT NULL COLLATE 'utf8_bin',
				`params` TEXT NULL COLLATE 'utf8_bin',
				`description` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_bin',
				`last_run` INT(11) UNSIGNED NOT NULL DEFAULT '0',
				`next_run` INT(11) UNSIGNED NOT NULL DEFAULT '0',
				PRIMARY KEY (`id`)
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin;",	
				);

	}
	
	public function update_function(){
		$crontab_file = $this->pfh->FolderPath('timekeeper', 'eqdkp').'crontab.php';
		$result = @file_get_contents($crontab_file);
		if($result !== false){
			$this->db->prepare("TRUNCATE __cronjobs")->execute();
			$crontab = unserialize($result);
			foreach($crontab as $key => $val){
				$this->pdh->put('cronjobs', 'add', array($key, $val['start_time'], $val['repeat'], $val['repeat_type'], $val['repeat_interval'], $val['extern'], $val['ajax'],$val['delay'], $val['multiple'], $val['active'], $val['editable'], $val['path'], $val['params'], $val['description']));
				$this->pdh->put('cronjobs', 'setLastAndNextRun', array($key, $val['last_run'],$val['next_run']));
			}
			
			$this->pdh->process_hook_queue();
			return true;
		}
		
		return false;
	}

}


?>
