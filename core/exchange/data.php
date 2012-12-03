<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2009
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

if (!class_exists('exchange_data')){
	class exchange_data extends gen_class{
		public static $shortcuts = array('user', 'pex'=>'plus_exchange', 'config');
		public $options		= array();

		public function get_data($params, $body){
			$out['eqdkp'] = array(
				'name'				=> $this->config->get('guildtag'),
				'guild'				=> $this->config->get('guildtag'),
				'dkp_name'			=> $this->config->get('dkp_name'),
				'forum_url'			=> $this->config->get('cmsbridge_url'),
				'language'			=> $this->config->get('default_lang'),
			);
			$out['game'] = array(
				'name'				=> $this->config->get('default_game'),
				'version'			=> $this->config->get('game_version'),
				'language'			=> $this->config->get('game_language'),
				'server_name'		=> $this->config->get('uc_servername'),
				'server_loc'		=> $this->config->get('uc_server_loc'),
			);
			return $out;
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_exchange_data', exchange_data::$shortcuts);
?>