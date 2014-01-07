<?php
/*
* Project:		EQdkp-Plus
* License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
* Link:			http://creativecommons.org/licenses/by-nc-sa/3.0/
* -----------------------------------------------------------------------
* Began:		2009
* Date:			$Date$
* -----------------------------------------------------------------------
* @author		$Author$
* @copyright	2006-2011 EQdkp-Plus Developer Team
* @link			http://eqdkp-plus.com
* @package		eqdkpplus
* @version		$Rev$
*
* $Id$
*/

if ( !defined('EQDKP_INC') ){
	die('Do not access this file directly.');
}

if ( !class_exists( "pdh_r_rank" ) ) {
	class pdh_r_rank extends pdh_r_generic{
		public static function __shortcuts() {
		$shortcuts = array('pdc', 'db',	'game');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public $default_lang = 'english';
		public $ranks;

		public $hooks = array(
			'adjustment_update',
			'event_update',
			'item_update',
			'member_update',
			'raid_update',
			'rank_update'
		);

		public function reset(){
			$this->pdc->del('pdh_member_ranks');
			$this->ranks = NULL;
		}

		public function init(){
			$this->ranks = $this->pdc->get('pdh_member_ranks');
			if($this->ranks !== NULL) return true;

			$r_result = $this->db->query("SELECT * FROM __member_ranks ORDER BY rank_sortid ASC;");
			while($r_row = $this->db->fetch_record($r_result)){
				$this->ranks[$r_row['rank_id']]['rank_id']	= $r_row['rank_id'];
				$this->ranks[$r_row['rank_id']]['prefix']	= $r_row['rank_prefix'];
				$this->ranks[$r_row['rank_id']]['suffix']	= $r_row['rank_suffix'];
				$this->ranks[$r_row['rank_id']]['name']		= $r_row['rank_name'];
				$this->ranks[$r_row['rank_id']]['hide']		= $r_row['rank_hide'];
				$this->ranks[$r_row['rank_id']]['sortid']	= $r_row['rank_sortid'];
			}
			if (!isset($this->ranks[0])) $this->ranks[0] = array('rank_id' => 0,	'prefix' => '',	'suffix' => '',	'name' => '', 'hide' => 0, 'sortid' => 0);
			$this->db->free_result($r_result);
			if($r_result) $this->pdc->put('pdh_member_ranks', $this->ranks);
		}

		public function get_id($name) {
			if(!empty($this->ranks)) {
				foreach($this->ranks as $id => $rank) {
					if($rank['name'] == $name) return $id;
				}
			}
			return false;
		}

		public function get_ranks(){
			return $this->ranks;
		}

		public function get_id_list(){
			return array_keys($this->ranks);
		}

		public function get_name($rank_id){
			return $this->ranks[$rank_id]['name'];
		}

		public function get_html_name($rank_id){
			return $this->game->decorate('ranks', array($rank_id)).$this->ranks[$rank_id]['name'];
		}

		public function get_rank_image($rank_id){
			$rankimage = (is_file("images/ranks/'.$rank_id.'.png")) ? "images/ranks/'.$this->ranks[$rank_id]['name'].'" : "images/roles/unknown.png";
			return '<img src="'.$rankimage.'" alt="rank image" width="20"/>';
		}

		public function get_prefix($rank_id){
			return $this->ranks[$rank_id]['prefix'];
		}

		public function get_suffix($rank_id){
			return $this->ranks[$rank_id]['suffix'];
		}

		public function get_is_hidden($rank_id){
			return $this->ranks[$rank_id]['hide'];
		}
		
		public function get_sortid($rank_id){
			return $this->ranks[$rank_id]['sortid'];
		}
		
		public function get_default(){
			$arrIDs = $this->get_id_list();
			return ((isset($arrIDs[0])) ? $arrIDs[0] : 0);
		}
		
	}//end class
}//end if
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_r_rank', pdh_r_rank::__shortcuts());
?>