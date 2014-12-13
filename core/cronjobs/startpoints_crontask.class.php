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

if ( !class_exists( "startpoints_crontask" ) ) {
	class startpoints_crontask extends crontask {
		public static $shortcuts = array('apa' => 'auto_point_adjustments');

		public function __construct(){
			$this->defaults['repeat']		= true;
			$this->defaults['repeat_type']	= 'daily';
			$this->defaults['editable']		= false;
			$this->defaults['description']	= 'Give startpoints to characters';
		}

		public function run() {
			$cron = $this->timekeeper->list_crons('startpoints');
			$apa_ids = $this->apa->get_apa_idsbytype('startpoints');
			foreach($apa_ids as $apa_id) {
				$this->apa->get_apa_type('startpoints')->update_startdkp($apa_id, $cron['last_run']);
			}
			$this->pdh->process_hook_queue();
		}
	}
}
?>