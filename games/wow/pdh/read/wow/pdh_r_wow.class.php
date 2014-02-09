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
			'wow_profiler'			=> array('profilers', array('%member_id%'),			array()),
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
			return '<i class="adminicon"></i>&nbsp;'.$this->get_achievementpoints($member_id);
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
				$charicon = $this->server_path.'images/global/avatar-default.svg';
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

		public function get_profilers($member_id){
			$membername		= $this->pdh->get('member', 'name', array($member_id));
			$output			= '';
			$a_profilers	= array(
				1	=> array(
					'icon'	=> $this->server_path.'games/wow/profiles/profilers/askmrrobot.png',
					'name'	=> 'AskMrRobot.com',
					'url'	=> $this->game->obj['armory']->bnlink($membername, $this->config->get('uc_servername'), 'askmrrobot')
				)
			);
			
			
			if(is_array($a_profilers)){
				foreach($a_profilers as $v_profiler){
					$output	.= '<a href="'.$v_profiler['url'].'"><img src="'.$v_profiler['icon'].'" alt="'.$v_profiler['name'].'" width="20" /></a> '; 
				}
			}
			return $output;
			
		}

	} //end class
} //end if class not exists
?>