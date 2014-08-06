<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2011
 * Date:		$Date: 2013-01-12 23:08:06 +0100 (Sa, 12 Jan 2013) $
 * -----------------------------------------------------------------------
 * @author		$Author: wallenium $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 12818 $
 *
 * $Id: char_creation_date.class.php 12818 2013-01-12 22:08:06Z wallenium $
 */

if ( !defined('EQDKP_INC') ){
  header('HTTP/1.0 404 Not Found');exit;
}

class clean_raid_attendees extends task {
	public $author = 'Hoofy';
	public $version = '1.0.0';
	public $form_method = 'post';
	public $name = 'Clean raid attendees table';
	public $type = 'fix';

	public static function __shortcuts() {
		$shortcuts = array('pdh', 'db', 'time', 'config');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public function is_applicable() {
		return true;
	}
	
	public function is_necessary() {
		return false;
	}
	
	public function get_form_content() {
		$this->db->query("DELETE ra FROM __raid_attendees ra LEFT JOIN __raids r ON r.raid_id = ra.raid_id WHERE r.raid_id IS NULL");
		$no_rows = $this->db->affected_rows();
		if(!$no_rows) $no_rows = 0;
		return sprintf($this->lang['fix_clean_raid_attendees_done'],$no_rows);
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_char_creation_date', char_creation_date::__shortcuts());
?>