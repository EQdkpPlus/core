<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2009
 * Date:		$Date: 2011-11-29 17:10:46 +0100 (Di, 29 Nov 2011) $
 * -----------------------------------------------------------------------
 * @author		$Author: Godmod $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 11470 $
 * 
 * $Id: events.php 11470 2011-11-29 16:10:46Z Godmod $
 */

if (!defined('EQDKP_INC')){
	die('Do not access this file directly.');
}

if (!class_exists('exchange_events')){
	class exchange_events extends gen_class {
		public static $shortcuts = array('user', 'config', 'pex'=>'plus_exchange', 'pdh', 'time', 'env' => 'environment');
		public $options		= array();
		

		public function get_events($params, $body){
			if ($this->user->check_auth('u_event_view', false)){
				$arrEvents = $this->pdh->get('event', 'id_list');
				$out = array();
				foreach($arrEvents as $eventid){
					$out['event:'.$eventid] = array(
						'id'	=> $eventid,
						'name'	=> $this->pdh->get('event', 'name', array($eventid)),
						'value'	=> $this->pdh->get('event', 'value', array($eventid)),
						'icon'  => str_replace("{ROOT_PATH}", $this->env->link, $this->pdh->get('event', 'icon', array($eventid, true, false, true))),
					);
					$arrMultidkpPools = $this->pdh->get('event', 'multidkppools', array($eventid));
					foreach($arrMultidkpPools as $mdkp){
						$arrPools['multidkp_pool:'.$mdkp] = array(
							'id'				=> $mdkp,
							'name'				=> $this->pdh->get('multidkp', 'name', array($mdkp)),
							'desc'				=> $this->pdh->get('multidkp', 'desc', array($mdkp)),
						);
					}
					$out['event:'.$eventid]['multidkp_pools'] = $arrPools;
					
					$arrItempoolsForEvent = $this->pdh->get('event', 'itempools', array($eventid));
					foreach($arrItempoolsForEvent as $itempoolid){
						$arrItempools['itempool:'.$itempoolid] = array(
							'id'				=> $itempoolid,
							'name'				=> $this->pdh->get('itempool', 'name', array($itempoolid)),
							'desc'				=> $this->pdh->get('itempool', 'desc', array($itempoolid)),
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
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_exchange_events', exchange_events::$shortcuts);
?>