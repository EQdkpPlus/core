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

class update_23100 extends sql_update_task {
	public $author			= 'GodMod';
	public $version			= '2.3.10.0'; //new plus-version
	public $ext_version		= '2.3.10'; //new plus-version
	public $name			= '2.3.10 Update';

	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_23100'	=> 'EQdkp Plus 2.3.10 Update',
				'update_function' => 'Clean up chars',
			),
			'german' => array(
				'update_23100'	=> 'EQdkp Plus 2.3.10 Update',
				'update_function' => 'Räume Charaktere auf',
			),
		);

		// init SQL querys
		$this->sqls = array(
		);
	}

	public function update_function(){
		//Remove doubled ownerships
		$objQuery = $this->db->query("SELECT member_id,COUNT(*) as count FROM __member_user GROUP BY member_id HAVING count > 1");
		if($objQuery){
			while ( $row = $objQuery->fetchAssoc() ) {
				$intChar = $row['member_id'];
				$this->db->prepare("DELETE FROM __member_user WHERE member_id=?")->execute($intChar);

				$this->pdh->put('member', 'change_mainid', array($intChar, $intChar));
			}
		}

		$this->pdh->enqueue_hook('member_update');
		$this->pdh->enqueue_hook('user_update');
		$this->pdh->process_hook_queue();

		//Check mainchar
		$arrChars = $this->pdh->get('member', 'id_list', array(false, false, false, false));
		foreach($arrChars as $intCharID){
			$intUser = $this->pdh->get('member', 'user', array($intCharID));
			if($intUser){
				$userchars = $this->pdh->get('member', 'connection_id', array($intUser));
				$intMainChar = $this->pdh->get('member', 'mainid', array($intCharID));
				if(!in_array($intMainChar, $userchars)){
					//Out of band
					$this->pdh->put('member', 'change_mainid', array($intCharID, $intCharID));
				}

				//Überprüfe Ringabhängigkeit von Mainchars
				$this->pdh->process_hook_queue();
				if ($intCharID != $intMainChar){
					if (($this->pdh->get('member', 'mainid', array($intMainChar)) == $intCharID) && ($this->pdh->get('member', 'mainid', array($intCharID)) == $intMainChar)){
						$this->change_mainid($intMainChar, $intMainChar);
					}
				}
			}
		}

		$this->pdh->process_hook_queue();

		return true;
	}

}
