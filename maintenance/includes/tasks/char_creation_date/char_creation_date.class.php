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

if ( !defined('EQDKP_INC') ){
  header('HTTP/1.0 404 Not Found');exit;
}

class char_creation_date extends task {
	public $author = 'Hoofy';
	public $version = '1.0.0';
	public $form_method = 'post';
	public $name = 'Character creation date';
	public $type = 'fix';

	public static function __shortcuts() {
		$shortcuts = array('pdh', 'db', 'time', 'config');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public function is_applicable() {
		$creation_dates = $this->pdh->aget('member', 'creation_date', 0, array($this->pdh->get('member', 'id_list')));
		if(in_array(0, $creation_dates)) return true;
		return false;
	}
	
	public function is_necessary() {
		return ($this->is_applicable() && $this->config->get('char_creation_date_update')) ? true : false;
	}
	
	public function get_form_content() {
		$creation_dates = $this->pdh->aget('member', 'creation_date', 0, array($this->pdh->get('member', 'id_list')));
		$members = array();
		foreach($creation_dates as $member_id => $date) {
			if(!$date) $members[] = $member_id;
		}
		$first_raids = $this->pdh->aget('member_dates', 'first_raid', 0, array($members, null, false));
		$first_items = $this->pdh->aget('member_dates', 'first_item_date', 0, array($members, null, false));
		$adjs4mem = $this->pdh->aget('adjustment', 'adjsofmember', 0, array($members));
		$adjs_date = $this->pdh->aget('adjustment', 'date', 0, array($this->pdh->get('adjustment', 'id_list')));
		$first_adjs = array();
		foreach($adjs4mem as $member_id => $adjs) {
			$first_adj = 99999999999;
			foreach($adjs as $id) {
				if($adjs_date[$id] < $first_adj) $first_adj = $adjs_date[$id];
			}
			$first_adjs[$member_id] = $first_adj;
		}
		foreach($members as $member_id) {
			if(!empty($first_raids[$member_id])) {
				if(!empty($first_items[$member_id])) {
					if(!empty($first_adjs[$member_id]))
						$date = min($first_raids[$member_id], $first_items[$member_id], $first_adjs[$member_id]);
					else $date = min($first_raids[$member_id], $first_items[$member_id]);
				} else $date = $first_raids[$member_id];
			} else $date = $this->time->time;
			$this->db->query("UPDATE __members SET member_creation_date = '".$date."' WHERE member_id = '".$member_id."';");
		}
		$this->config->del('char_creation_date_update');
		return $this->lang['fix_creation_date_done'];
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_char_creation_date', char_creation_date::__shortcuts());
?>