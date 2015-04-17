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

if (!defined('EQDKP_INC')){
	die('Do not access this file directly.');
}

if (!class_exists('exchange_events')){
	class exchange_events extends gen_class {
		public static $shortcuts = array('user', 'config', 'pex'=>'plus_exchange', 'pdh', 'time', 'env' => 'environment');
		public $options		= array();
		

		public function get_events($params, $body){
			if($this->user->check_pageobjects(array('events'), 'AND', false)){
				$arrEvents = $this->pdh->get('event', 'id_list');
				$out = array();
				foreach($arrEvents as $eventid){
					$out['event:'.$eventid] = array(
						'id'	=> $eventid,
						'name'	=> unsanitize($this->pdh->get('event', 'name', array($eventid))),
						'value'	=> $this->pdh->get('event', 'value', array($eventid)),
						'icon'  => $this->env->link.$this->pdh->get('event', 'icon', array($eventid, true)),
					);
					$arrMultidkpPools = $this->pdh->get('event', 'multidkppools', array($eventid));
					foreach($arrMultidkpPools as $mdkp){
						$arrPools['multidkp_pool:'.$mdkp] = array(
							'id'				=> $mdkp,
							'name'				=> unsanitize($this->pdh->get('multidkp', 'name', array($mdkp))),
							'desc'				=> unsanitize($this->pdh->get('multidkp', 'desc', array($mdkp))),
						);
					}
					$out['event:'.$eventid]['multidkp_pools'] = $arrPools;
					
					$arrItempoolsForEvent = $this->pdh->get('event', 'itempools', array($eventid));
					foreach($arrItempoolsForEvent as $itempoolid){
						$arrItempools['itempool:'.$itempoolid] = array(
							'id'				=> $itempoolid,
							'name'				=> unsanitize($this->pdh->get('itempool', 'name', array($itempoolid))),
							'desc'				=> unsanitize($this->pdh->get('itempool', 'desc', array($itempoolid))),
						);
					}
					$out['event:'.$eventid]['itempools'] = $arrItempools;
				}
				return $out;
			} else {
				return $this->pex->error('access denied');
			}
		}
	}
}
?>