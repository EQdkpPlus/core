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
	
	protected $fields2change	= array(
			'__styles'			=> array('field' => array(
					'body_background',
					'body_link',
					'body_hlink',
					'header_link',
					'header_hlink',
					'tr_color1',
					'tr_color2',
					'th_color1',
					'fontcolor1',
					'fontcolor2',
					'fontcolor3',
					'fontcolor_neg',
					'fontcolor_pos',
					'table_border_color',
					'input_color',
					'input_border_color',
			), 'id' => 'style_id')
	);
	
	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_20022'	=> 'EQdkp Plus 2.0 Update Beta5',
					1			=> 'Alter Styles Table',
					2			=> 'Alter Styles Table',
					3			=> 'Alter Session Table',
				),
			'german' => array(
				'update_20022'	=> 'EQdkp Plus 2.0 Update Beta5',
					1			=> 'Alter Styles Table',
					2			=> 'Alter Styles Table',
					3			=> 'Alter Session Table',
			),
		);
		
		// init SQL querys
		$this->sqls = array(
			1 => "ALTER TABLE `__styles` ADD COLUMN `background_pos` VARCHAR(20) NULL DEFAULT 'normal';",
			2 => "ALTER TABLE `__styles` ADD COLUMN `background_type` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0';",
			3 => "ALTER TABLE `__sessions` ADD COLUMN `session_vars` MEDIUMTEXT COLLATE 'utf8_bin' NULL;"
		);
	}
	
	public function update_function(){
		$this->update_colors();
		
		//Delete global key because it does not work with recaptcha v2
		if($this->config->get('lib_recaptcha_okey') == '6LdKQMUSAAAAAOFATjZq_IyMruO1jxQL-rSVNF-g'){
			$this->config->set('lib_recaptcha_okey', '');
			$this->config->set('lib_recaptcha_pkey', '');
		}
		
		return true;
	}
	
	private function update_colors(){
		//Update Colors
		foreach($this->fields2change as $dbtable=>$dbfields){
			foreach($dbfields['field'] as $dbfieldvalue){
				// now, lets change the values
				$sql	= 'SELECT '.$dbfieldvalue.' as mycolorvalue, '.$dbfields['id'].' as mycolorid FROM '.$dbtable.';';
				$query = $this->db->query($sql);
				$update = array();
				if ($query){
					while ($row = $query->fetchAssoc()) {
						if(trim($row['mycolorvalue']) != ''){
							// check if the # is already in the value
							if (preg_match('/^#[a-f0-9]{6}$/i', $row['mycolorvalue'])) {
								continue;
							}else if (preg_match('/^[a-f0-9]{6}$/i', $row['mycolorvalue'])) {
								$sql = "UPDATE ".$dbtable." SET ".$dbfieldvalue." = '#".$row['mycolorvalue']."' WHERE ".$dbfields['id']." = '".$row['mycolorid']."';";
								$this->db->query($sql);
							}
						}
					}
				}
			}
		}
	}
}


?>