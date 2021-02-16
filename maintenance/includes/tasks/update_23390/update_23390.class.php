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

class update_23390 extends sql_update_task {
	public $author			= 'GodMod';
	public $version			= '2.3.39.0'; //new plus-version
	public $ext_version		= '2.3.39'; //new plus-version
	public $name			= '2.3.39 Update';
	
	public function __construct(){
		parent::__construct();
		
		$this->langs = array(
				'english' => array(
						'update_23330'	=> 'EQdkp Plus 2.3.33 Update',
						'update_function' => 'Clear Repository Table',
				),
				'german' => array(
						'update_23330'	=> 'EQdkp Plus 2.3.33 Update',
						'update_function' => 'Leere Repository Tabelle'
				),
		);
	}
	
	public function update_function(){
	    $this->pdh->put('repository', 'reset');
	    $this->pdh->process_hook_queue();
	    
	    return true;
	}
	
}

?>