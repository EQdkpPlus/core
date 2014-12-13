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

if (!class_exists('exchange_data')){
	class exchange_data extends gen_class{
		public static $shortcuts = array('pex'=>'plus_exchange');
		public $options		= array();

		public function get_data($params, $body){
			$out['eqdkp'] = array(
				'name'				=> unsanitize($this->config->get('guildtag')),
				'guild'				=> unsanitize($this->config->get('guildtag')),
				'dkp_name'			=> $this->config->get('dkp_name'),
				'forum_url'			=> $this->config->get('cmsbridge_url'),
				'language'			=> $this->config->get('default_lang'),
			);
			$out['game'] = array(
				'name'				=> $this->config->get('default_game'),
				'version'			=> $this->config->get('game_version'),
				'language'			=> $this->config->get('game_language'),
				'server_name'		=> unsanitize($this->config->get('servername')),
				'server_loc'		=> $this->config->get('uc_server_loc'),
			);
			return $out;
		}
	}
}
?>