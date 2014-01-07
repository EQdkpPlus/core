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

if ( !defined('EQDKP_INC') ){
	die('Do not access this file directly.');
}

if ( !class_exists( "pdh_r_calendars" ) ) {
	class pdh_r_calendars extends pdh_r_generic{
		public static function __shortcuts() {
		$shortcuts = array('pdc', 'db'	);
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public $default_lang = 'english';
		public $calendars;

		public $hooks = array(
			'calendar_update',
		);

		public function reset(){
			$this->pdc->del('pdh_calendars_table');
			$this->calendars = NULL;
		}

		public function init(){
			//cached data not outdated?
			$this->calendars	= $this->pdc->get('pdh_calendars_table');
			if($this->calendars !== NULL){
				return true;
			}

			$query = $this->db->query("SELECT * FROM __calendars");
			while ( $row = $this->db->fetch_record($query) ){
				$this->calendars[$row['id']] = array(
					'id'						=> $row['id'],
					'name'						=> $row['name'],
					'color'						=> $row['color'],
					'private'					=> $row['private'],
					'feed'						=> $row['feed'],
					'system'					=> $row['system'],
					'type'						=> $row['type'],
				);
			}
			$this->db->free_result($query);
			if($query) $this->pdc->put('pdh_calendars_table', $this->calendars, null);
		}

		//1 = raid, 2=event 3=feed
		public function get_idlist($filter=false, $idfilter=false){
			if($filter){
				$out = array();
				foreach($this->calendars as $id=>$cals){
					// continue if the idfilter is false or if the id is in the id filter
					if(!$idfilter || (is_array($idfilter) && in_array($id, $idfilter))){
						if($filter == 'feed'){
							if($cals['type'] == '3'){
								$out[] = $id;
							}
						#}elseif($filter == 'free2add'){
						#	if(!$cals['system'] && $cals['type'] != '3'){
						#		$out[] = $id;
						#	}
						}else{
							if($cals['type'] != '3'){
								$out[] = $id;
							}
						}
					}
				}
				return $out;
			}else{
				return (isset($this->calendars)) ? array_keys($this->calendars) : array();
			}
		}

		public function get_is_deletable($id){
			return 	($this->calendars[$id]['system'] > 0) ? false : true;
		}

		public function get_data($id=false){
			return ($id > 0) ? $this->calendars[$id] : $this->calendars;
		}

		public function get_name($id){
			return 	$this->calendars[$id]['name'];
		}

		public function get_color($id){
			return 	$this->calendars[$id]['color'];
		}

		public function get_private($id){
			return 	$this->calendars[$id]['private'];
		}

		public function get_feed($id){
			return 	$this->calendars[$id]['feed'];
		}

		public function get_type($id){
			return (isset($this->calendars[$id])) ? $this->calendars[$id]['type'] : '';
		}
	}//end class
}//end if
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_r_calendars', pdh_r_calendars::__shortcuts());
?>