<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2016 EQdkp-Plus Developer Team
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
	die('Do not access this file directly.');
}

if ( !class_exists( "apa_hardcap_current" ) ) {
	class apa_hardcap_current extends apa_type_generic {
		public static $shortcuts = array('apa'=>'auto_point_adjustments');

		protected $ext_options = array(
			'upper_cap'	=> array(
				'type'		=> 'spinner',
				'step'		=> 10,
				'size'		=> 5,
				'default'	=> 100
			),
			'lower_cap' => array(
				'type'		=> 'spinner',
				'step'		=> 10,
				'size'		=> 5,
				'default'	=> -100
			),
		);

		private $modules_affected = array('current_hardcap');

		private $cached_data = array();

		public function __construct() {
			$this->options = array_merge($this->options, $this->ext_options);
		}

		public function update_point_cap($apa_id) {

		}

		public function modules_affected($apa_id) {
			return $this->modules_affected;
		}

		public function get_last_run($date, $apa_id) { return; }
		public function get_next_run($apa_id) { return 0; }

		public function get_value($apa_id, $cache_date, $module, $dkp_id, $data, $refdate, $debug=false) {
			$value = $data['val'];
			$lower_cap = $this->apa->get_data('lower_cap', $apa_id);
			$upper_cap = $this->apa->get_data('upper_cap', $apa_id);

			if($lower_cap != "" && ($value < $lower_cap)){
				return array($lower_cap, false, 0);
			}

			if($upper_cap != "" && ($value > $upper_cap)){
				return array($upper_cap, false, 0);
			}

			return array($value, false, 0);
		}

		public function recalculate($apa_id){
			return true;
		}
	}//end class
}//end if
