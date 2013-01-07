<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2011
 * Date:		$Date: 2012-11-30 20:35:42 +0100 (Fr, 30. Nov 2012) $
 * -----------------------------------------------------------------------
 * @author		$Author: hoofy_leon $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 12519 $
 *
 * $Id: update_107.class.php 12519 2012-11-30 19:35:42Z hoofy_leon $
 */

if ( !defined('EQDKP_INC') ){
  header('HTTP/1.0 404 Not Found');exit;
}

include_once(registry::get_const('root_path').'maintenance/includes/sql_update_task.class.php');

class update_108 extends sql_update_task {
	public $author		= 'Hoofy';
	public $version		= '1.0.8'; //new plus-version
	public $name		= '1.0 RC2 Update 1';
	
	public static function __shortcuts() {
		$shortcuts = array('time');
		return array_merge(parent::__shortcuts(), $shortcuts);
	}
	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_108'		=> 'EQdkp Plus 1.0 RC2 Update 1',
				'update_function'	=> 'Alter birthday field',
			),
			'german' => array(
				'update_108'		=> 'EQdkp Plus 1.0 RC2 Update 1',
				'update_function'	=> 'Verändere birthday Feld',
			),
		);
	}
	
	public function update_function() {
		$sql = "SELECT user_id, birthday FROM __users;";
		$query = $this->db->query($sql);
		$update = array();
		while ($row = $this->db->fetch_record($query)) {
			if(strpos($row['birthday'], '.') !== false) {
				list($d,$m,$y) = explode('.', $row['birthday']);
				$update[$row['user_id']] = $this->time->mktime(0,0,0,$m,$d,$y);
			} elseif(empty($row['birthday'])) {
				$update[$row['user_id']] = 0;
			}
		}
		foreach($update as $user_id => $birthday) {
			$sql = "UPDATE __users SET birthday = '".$birthday."' WHERE user_id = '".$user_id."';";
			$this->db->query($sql);
		}
		if(!$this->db->query("ALTER TABLE `__users`CHANGE COLUMN `birthday` `birthday` BIGINT(11) NULL DEFAULT '0';")) return false;
		return true;
	}
}

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_update_108', update_108::__shortcuts());
?>