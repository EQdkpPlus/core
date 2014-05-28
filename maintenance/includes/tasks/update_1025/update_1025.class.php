<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2011
 * Date:		$Date: 2013-01-12 23:08:06 +0100 (Sa, 12. Jan 2013) $
 * -----------------------------------------------------------------------
 * @author		$Author: wallenium $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 12818 $
 *
 * $Id: update_1013.class.php 12818 2013-01-12 22:08:06Z wallenium $
 */

if ( !defined('EQDKP_INC') ){
  header('HTTP/1.0 404 Not Found');exit;
}

include_once(registry::get_const('root_path').'maintenance/includes/sql_update_task.class.php');

class update_1025 extends sql_update_task {
	public $author		= 'GodMod';
	public $version		= '1.0.25'; //new plus-version
	public $name		= '1.0 Update 1';
	
	public static function __shortcuts() {
		$shortcuts = array('time');
		return array_merge(parent::__shortcuts(), $shortcuts);
	}
	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_1025'		=> 'EQdkp Plus 1.0.8',
				'update_function'	=> 'Changed Usernames',
			),
			'german' => array(
				'update_1025'		=> 'EQdkp Plus 1.0.8',
				'update_function'	=> 'Anpassung Benutzernamen',
			),
		);
	
	}
	
	public function update_function() {
		$sql = "SELECT username, user_id FROM __users;";
		$query = $this->db->query($sql);

		while ($row = $this->db->fetch_record($query)) {
			$username = str_replace(array("'", '"'), array('&#39;', '&#34;'), $row['username']);
			$this->db->query("UPDATE __users SET :params WHERE user_id=?", array(
				'username' => $username,
			), $row['user_id']);
		}
		return true;
	}
}

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_update_1025', update_1025::__shortcuts());
?>