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

if ( !class_exists( "game_crontask" ) ) {
	class game_crontask extends crontask {
		
		public $options = array();

		public function __construct(){
			$this->defaults['repeat']		= true;
			$this->defaults['repeat_type']	= 'daily';
			$this->defaults['editable']		= true;
			$this->defaults['delay']		= true;
			$this->defaults['ajax']			= true;
			$this->defaults['description']	= 'Game-specific Update Tasks';
			
			$this->options = $this->game->cronjobOptions();
		}
		
		public function run(){
			$crons		= $this->timekeeper->list_crons();
			$params		= $crons['game']['params'];
			$this->game->cronjob($params);
		}
	}
}
?>