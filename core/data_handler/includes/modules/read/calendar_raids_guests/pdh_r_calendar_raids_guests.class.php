<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2010
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

if (!class_exists('pdh_r_calendar_raids_guests')){
	class pdh_r_calendar_raids_guests extends pdh_r_generic{
		public static function __shortcuts() {
		$shortcuts = array('pdc', 'db'	);
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		private $guests;
		public $hooks = array(
			'guests_update',
		);

		/**
		* Constructor
		*/
		public function __construct(){
		}

		/**
		* reset
		*/
		public function reset(){
			$this->pdc->del('pdh_calendar_raids_table.guests');
			$this->pdc->del_prefix('plugin.guests');
			$this->guests = NULL;
		}

		/**
		* init
		*
		* @returns boolean
		*/
		public function init(){
			// try to get from cache first
			$this->guests		= $this->pdc->get('pdh_calendar_raids_table.guests');
			if($this->guests !== NULL){
				return true;
			}

			// empty array as default
			$this->guests	= array();
			$myresult		= $this->db->query('SELECT * FROM __calendar_raid_guests;');
			while ($row = $this->db->fetch_record($myresult)){
				$this->guests[$row['calendar_events_id']][$row['id']] = array(
					'name'				=> $row['name'],
					'note'				=> $row['note'],
					'timestamp_signup'	=> $row['timestamp_signup'],
					'raidgroup'			=> $row['raidgroup'],
					'class'				=> $row['class'],
				);
			}

			if($myresult) $this->pdc->put('pdh_calendar_raids_table.guests', $this->guests, NULL);
			return true;
		}

		public function get_members($id=''){
			$output = ($id) ? ((isset($this->guests[$id])) ? $this->guests[$id] : '') : $this->guests;
			return (is_array($output)) ? $output : array();
		}

		public function get_guest($id){
			foreach($this->guests as $gdata){
				if(is_array($gdata[$id])){
						return $gdata[$id];
				}
			}
		}

		public function get_class($id){
			return $this->guests[$id]['class'];
		}

		public function get_note($id){
			return $this->guests[$id]['note'];
		}

		public function get_group($id){
			return $this->guests[$id]['group'];
		}

		// not working. must have a look another day
		public function get_count($raidid){
			if(isset($this->guests[$id]) && is_array($this->guests[$id])){
				/*foreach($this->guests[$id]){

				}*/
				return count($this->guests[$id]);
			}else{
				return 0;
			}
		}

	} //end class
} //end if class not exists

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_r_calendar_raids_guests', pdh_r_calendar_raids_guests::__shortcuts());
?>