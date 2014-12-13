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

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class MySQL_Info extends page_generic{

	public function __construct(){
		$this->user->check_auth('a_config_man');
		$handler = array();
		parent::__construct(false, $handler);
		$this->process();
	}

	// ---------------------------------------------------------
	// Display form
	// ---------------------------------------------------------
	public function display(){
		//Set some default-values
		$table_count = 0;
		$table_size = 0;
		$index_size = 0;

		$arrTables = $this->db->listTables();

		foreach ($arrTables as $strTablename){
			$arrTableInfos = $this->db->fieldInformation($strTablename);
			
			$this->tpl->assign_block_vars('table_row', array(
				'TABLE_NAME'	=> $strTablename,
				'ROWS'			=> $arrTableInfos['rows'],
				'COLLATION'		=> $arrTableInfos['collation'],
				'ENGINE'		=> $arrTableInfos['engine'],
				'TABLE_SIZE'	=> $this->convert_db_size($arrTableInfos['data_length']),
				'INDEX_SIZE'	=> $this->convert_db_size($arrTableInfos['index_length']))
			);

			$index_size += $arrTableInfos['index_length'];
			$table_size += $arrTableInfos['data_length'];
			$table_count++;
		}

		$this->tpl->assign_vars(array(
			'NUM_TABLES'		=> sprintf($this->user->lang('num_tables'), $table_count),
			'TOTAL_TABLE_SIZE'	=> $this->convert_db_size($table_size),
			'TOTAL_INDEX_SIZE'	=> $this->convert_db_size($index_size),
			'TOTAL_SIZE'		=> $this->convert_db_size($table_size + $index_size),

			'DB_ENGINE'			=> $this->dbtype,
			'DB_NAME'			=> $this->dbname,
			'DB_PREFIX'			=> $this->table_prefix,
			'DB_VERSION'		=> 'Client ('.$this->db->client_version.')<br/>Server ('.$this->db->server_version.')',
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('title_mysqlinfo'),
			'template_file'		=> 'admin/info_database.html',
			'display'			=> true)
		);
	}

	// ---------------------------------------------------------
	// Process Helper
	// ---------------------------------------------------------
	private function convert_db_size($bytes){
		if ( $bytes <= 1024 ){
			return $bytes.' B';
		} elseif ( $bytes <= 1048576 ) {
			return (round($bytes/1024, 2)).' KB';
		} else {
			return (round($bytes/1048576, 2)).' MB';
		}
	}
}
registry::register('MySQL_Info');
?>