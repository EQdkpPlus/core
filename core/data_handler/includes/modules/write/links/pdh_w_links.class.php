<?php
/*
* Project:		EQdkp-Plus
* License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
* Link:			http://creativecommons.org/licenses/by-nc-sa/3.0/
* -----------------------------------------------------------------------
* Began:		2007
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

if(!defined('EQDKP_INC')) {
	die('Do not access this file directly.');
}

if(!class_exists('pdh_w_links')) {
	class pdh_w_links extends pdh_w_generic {
		public static function __shortcuts() {
		$shortcuts = array('pdh', 'db'	);
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public function __construct() {
			parent::__construct();
		}
		
		public function add($name, $url, $window=0, $visibility=0, $height=4024){
			if (strlen($name)){
				$this->db->query("INSERT INTO __links :params", array(
					'link_name'			=> $name,
					'link_url'			=> $url,
					'link_window'		=> $window,
					'link_visibility'	=> $visibility,
					'link_height'		=> $height,
				));
				$this->pdh->enqueue_hook('links');
				return $this->db->insert_id();
			}
			return false;
		}
		
		public function update($id, $name, $url, $window, $visibility, $height, $force = false){
			$data = $this->pdh->get('links', 'data', array($id));
		
			if ($force OR $data['name'] != $name OR $data['url'] != $url OR (int)$data['window'] != (int)$window OR (int)$data['visibility'] != (int)$visibility OR (int)$data['height'] != (int)$height){
				$blnResult = $this->db->query("UPDATE __links SET :params WHERE link_id=?", array(
					'link_name'			=> $name,
					'link_url'			=> $url,
					'link_window'		=> $window,
					'link_visibility'	=> $visibility,
					'link_height'		=> $height,
				), $id);
				$this->pdh->enqueue_hook('links');
				if (!$blnResult) return false;
			}
			return true;
		}

		public function delete_link($id){
			$this->db->query("DELETE FROM __links WHERE link_id = '".$this->db->escape($id)."'");
			$this->pdh->enqueue_hook('links', array($id));
		}
	}
}
?>