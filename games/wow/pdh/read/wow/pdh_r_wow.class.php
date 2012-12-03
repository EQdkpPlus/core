<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2011
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 * 
 * $Id$
 */

if (!defined('EQDKP_INC')){
	die('Do not access this file directly.');
}

/*+----------------------------------------------------------------------------
  | pdh_r_wow
  +--------------------------------------------------------------------------*/
if (!class_exists('pdh_r_wow')) {
	class pdh_r_wow extends pdh_r_generic {
		public static $shortcuts = array('core', 'game', 'pdh', 'config');

		/**
		* Data array loaded by initialize
		*/
		private $data;
		private $guilddata;

		/**
		* Hook array
		*/
		public $hooks = array(
			'member_update',
		);

		/**
		* Presets array
		*/
		public $presets = array(
			'wow_charicon'			=> array('charicon', array('%member_id%'),			array()),
			'wow_achievementpoints'	=> array('achievementpoints',array('%member_id%'),	array()),
			'wow_gearlevel'			=> array('averageItemLevelEquipped',array('%member_id%'),	array()),
		);

		/**
		* Constructor
		*/
		public function __construct(){
		}
	
		public function reset(){
		}

		/**
		* init
		*
		* @returns boolean
		*/
		public function init(){
			$this->data = array();
			$this->game->new_object('bnet_armory', 'armory', array($this->config->get('uc_server_loc'), $this->config->get('uc_data_lang')));
			$this->guilddata = $this->game->obj['armory']->guild($this->config->get('guildtag'), $this->config->get('uc_servername'));
			$guildMembers = array();
			if (is_array($this->guilddata['members'])){
				foreach($this->guilddata['members'] as $member){
					 $this->data[$member['character']['name']] = $member;
				}
			}
			return true;
		}

		public function get_achievementpoints($member_id){
			$membername = $this->pdh->get('member', 'name', array($member_id));
			if (isset($this->data[$membername])){
				return $this->data[$membername]['character']['achievementPoints'];
			}
			
			$charinfo = $this->game->obj['armory']->character($membername, $this->config->get('uc_servername'));
			if (isset($charinfo['achievementPoints'])){
				return $charinfo['achievementPoints'];
			}
			
			return 0;
		}

		public function get_html_achievementpoints($member_id){
			return '<img src="'.$this->root_path.'games/wow/profiles/achievements.png" alt="Achievement-Points"/>&nbsp;'.$this->get_achievementpoints($member_id);
		}

		public function get_charicon($member_id){
			$membername = $this->pdh->get('member', 'name', array($member_id));
			if (isset($this->data[$membername])){
				return $this->game->obj['armory']->characterIcon($this->data[$membername]['character']);
			}

			$charinfo = $this->game->obj['armory']->character($membername, $this->config->get('uc_servername'));
			if (isset($charinfo['thumbnail'])){
				return $this->game->obj['armory']->characterIcon($charinfo);
			}
			return '';
		}

		public function get_html_charicon($member_id){
			$charicon = $this->get_charicon($member_id);
			if ($charicon == '') {
				$charicon = $this->root_path.'images/no_pic.png';
			}
			return '<img src="'.$charicon.'" alt="Char-Icon" height="48" />';
		}
		
		public function get_averageItemLevelEquipped($member_id){
			$membername = $this->pdh->get('member', 'name', array($member_id));
			$charinfo = $this->game->obj['armory']->character($membername, $this->config->get('uc_servername'));
			if (isset($charinfo['items']['averageItemLevelEquipped'])){
				return $charinfo['items']['averageItemLevelEquipped'];
			}
			
			return '';
		}
		
		
		
	} //end class
} //end if class not exists
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_r_wow', pdh_r_wow::$shortcuts);
?>