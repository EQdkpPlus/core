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

if ( !class_exists( "pointcap_crontask" ) ) {
	class pointcap_crontask extends crontask {
		public static $shortcuts = array('apa' => 'auto_point_adjustments');

		public function __construct(){
			$this->defaults['repeat']		= true;
			$this->defaults['repeat_type']	= 'daily';
			$this->defaults['repeat_interval']	= 1;
			$this->defaults['editable']		= false;
			$this->defaults['description']	= 'Cap points of characters';
		}

		public function run() {
			$cron = $this->timekeeper->list_crons('pointcap');
			$apa_ids = $this->apa->get_apa_idsbytype('cap_current');
			foreach($apa_ids as $apa_id) {
				$this->apa->get_apa_type('cap_current')->update_point_cap($apa_id);
			}
			$this->pdh->process_hook_queue();
		}
	}
}
?>