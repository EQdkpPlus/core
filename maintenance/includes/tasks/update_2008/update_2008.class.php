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

include_once(registry::get_const('root_path').'maintenance/includes/sql_update_task.class.php');

class update_2008 extends sql_update_task {
	public $author			= 'GodMod';
	public $version			= '2.0.0.8'; //new plus-version
	public $ext_version		= '2.0.0'; //new plus-version
	public $name			= 'Update Usernames';
	
	public static function __shortcuts() {
		$shortcuts = array('time');
		return array_merge(parent::__shortcuts(), $shortcuts);
	}
	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_2008'		=> 'EQdkp Plus 2.0.0',
				'update_function'	=> 'Changed Usernames',
			),
			'german' => array(
				'update_2008'		=> 'EQdkp Plus 2.0.0',
				'update_function'	=> 'Anpassung Benutzernamen',
			),
		);
	
	}
	
	public function update_function() {
		$sql = "SELECT username, user_id FROM __users;";
		$query = $this->db->query($sql);

		while ($row = $query->fetchAssoc()) {
			$username = str_replace(array("'", '"'), array('&#39;', '&#34;'), $row['username']);
			$this->db->prepare("UPDATE __users :p WHERE user_id=?")->set( array(
				'username' => $username,
			))->execute($row['user_id']);
		}
		return true;
	}
}

?>