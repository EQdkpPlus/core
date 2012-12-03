<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2010
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2006-2010 EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev$
 * 
 * $Id$
 */

if ( !defined('EQDKP_INC') ){
  header('HTTP/1.0 404 Not Found');exit;
}

class content_export
{
	var $data_out				= "";
	
	public function __construct(){
		$this->timestamp			= time();
		$this->date_created		= date("d.m.y G:i:s");
	}
	
	public function export(){
		global $db, $pm, $core, $game, $eqdkp_root_path, $pdh, $xmltools;
		
		$out = array();		
		
		$out['eqdkp'] = array(
			'name'			=> $core->config['guildtag'],
			'guild'			=> $core->config['parsetags'],
			'dkp_name'	=> $core->config['dkp_name'],
			
			'version'		=> $core->config['plus_version'],
			'layout'		=> $core->config['eqdkp_layout'],
		);
		$out['game'] = array(
			'name'			=> $core->config['default_game'],
			'version'		=> $core->config['game_version'],
			'language'	=> $core->config['game_language'],
			'server_name'=> $core->config['uc_servername'],
			'server_loc'=> $core->config['uc_server_loc'],
		);				
		$out['info'] = array(
			'date'			=> $this->date_created,			
			'timestamp'	=> $this->timestamp,
			'total_players'	=> count($pdh->get('member', 'id_list')),
			'total_items'		=> count($pdh->get('item', 'id_list')),
			'total_points' 	=> 0,
		);

		$mdkps = $pdh->get('multidkp', 'id_list');
		
		//Alle Member
		$total_points = 0;
		$members = $pdh->get('member', 'id_list');
		foreach ($members as $member){		
			$points = array();
			foreach ($mdkps as $mdkp){
				$points['multidkp_points:'.$mdkp] = array(
					'multidkp_id'	=> $mdkp,
					'points_current'	=> $pdh->get('points', 'current', array($member, $mdkp)),
					'points_earned'	=> $pdh->get('points', 'earned', array($member, $mdkp)),
					'points_spent'	=> $pdh->get('points', 'spent', array($member, $mdkp)),
				);
				$total_points += $pdh->get('points', 'current', array($member, $mdkp)); 
			}
			
			$items = array();
			$item_list = $pdh->get('item', 'itemids4memberid', array($member));
			foreach ($item_list as $item_id){
					$game_id = $pdh->get('item', 'game_itemid', array($item_id));
					$items['item:'.$item_id] = array(
						'game_id'	=> ($game_id) ? $game_id : 0,
						'name'		=> $pdh->get('item', 'name', array($item_id)),
						'value'		=> $pdh->get('item', 'value', array($item_id)),
						'itempool_id'	=> $pdh->get('item', 'itempool_id', array($item_id)),
					);
			}
			
			$out['players']['player:'.$member] = array(
				'id'				=> $member,
				'name'			=> $pdh->get('member', 'name', array($member)),
				'active'		=> $pdh->get('member', 'active', array($member)),
				'hidden'		=> $pdh->get('member', 'is_hidden', array($member)),
				'main_id'		=> $pdh->get('member', 'mainid', array($member)),
				'main_name'	=> $pdh->get('member', 'mainname', array($member)),
				
				'class_id'	=> $pdh->get('member', 'classid', array($member)),
				'class_name'=> $pdh->get('member', 'classname', array($member)),
				'race_id'		=> $pdh->get('member', 'raceid', array($member)),
				'race_name'	=> $pdh->get('member', 'racename', array($member)),
				
				'points'		=> $points,
				'items'			=> $items,
			);
		}
		
		$out['info']['total_points'] = $total_points;
		
		//Alle MultiDKP-Konten

		foreach ($mdkps as $mdkp){
			$event_ids = $pdh->get('multidkp', 'event_ids', array($mdkp));
			$itempool_ids = $pdh->get('multidkp', 'itempool_ids', array($mdkp));
			foreach ($itempool_ids as $pool){
				$itempools['itempool_id:'.$pool] = $pool;
			}
			
			$events = array();
			
			foreach ($event_ids as $event){
				$events['event:'.$event] = array(
					'id'	=> $event,
					'name'	=> $pdh->get('event', 'name', array($event)),
					'value'	=> $pdh->get('event', 'value', array($event))
				);
			}
			
			$out['multidkp_pools']['multidkp_pool:'.$mdkp] = array(
				'id'		=> $mdkp,
				'name'	=> $pdh->get('multidkp', 'name', array($mdkp)),
				'desc'	=> $pdh->get('multidkp', 'desc', array($mdkp)),
				'events' => $events,
				'mdkp_itempools'	=> $itempools,
			);
		}
		
		//Alle Itempools
		$itempools = $pdh->get('itempool', 'id_list');
		foreach ($itempools as $itempool){
			$out['itempools']['itempool:'.$itempool] = array(
				'id'		=> $itempool,
				'name'	=> $pdh->get('itempool', 'name', array($itempool)),
				'desc'	=> $pdh->get('itempool', 'desc', array($itempool)),
			);
		}
				
		$xml_array = $xmltools->array2simplexml($out);
		
		$dom = dom_import_simplexml($xml_array)->ownerDocument;
		$dom->formatOutput = true;
		$dom->encoding='utf-8';
		$string = $dom->saveXML(); 
		return $string;
	}
	
}
?>