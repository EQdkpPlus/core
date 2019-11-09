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

class update_23200 extends sql_update_task {
	public $author			= 'GodMod';
	public $version			= '2.3.20.0'; //new plus-version
	public $ext_version		= '2.3.20'; //new plus-version
	public $name			= '2.3.20 Update';

	public function __construct(){
		parent::__construct();

		$this->langs = array(
				'english' => array(
						'update_23200'	=> 'EQdkp Plus 2.3.20 Update',
						'update_function' => 'Try to enable Easymode',
				),
				'german' => array(
						'update_23200'	=> 'EQdkp Plus 2.3.20 Update',
						'update_function' => 'Versuche den Easymode zu aktivieren',
				),
		);

	}

	public function update_function(){
		//Are there events in multiple pools?

		$objQuery = $this->db->prepare("SELECT COUNT(*) as count FROM __multidkp2event GROUP BY multidkp2event_event_id;")->execute();

		if($objQuery){
			while($row = $objQuery->fetchAssoc()){
				if(intval($row['count']) > 1){
					echo "raus";
					return true;
				}
			}
		}

		//We are still here, so enable the Easy mode
		$this->config->set('dkp_easymode', 1);

		//Checke den Itempool für jeden MDKP Pool
		$mdkp_ids = $this->pdh->get('multidkp', 'id_list');

		foreach($mdkp_ids as $id)
		{
			$ip_ids = $this->pdh->get('multidkp', 'itempool_ids', $id);

			if(count($ip_ids) === 0){
				$name = $this->pdh->get('multidkp', 'name', $id);
				//Create one and connect it
				$itempoolID = $this->pdh->put('itempool', 'add_itempool', array($name, 'Auto generated for '.$name));

				if($itempoolID){
					$retu = ($this->db->prepare("INSERT INTO __multidkp2itempool :p")->set(array(
							'multidkp2itempool_multi_id' => $id,
							'multidkp2itempool_itempool_id' => $itempoolID))->execute()) ? true : false;
				}
			}
		}

		$this->pdh->enqueue_hook('itempool_update');
		$this->pdh->enqueue_hook('multidkp_update');

		return true;
	}

}
