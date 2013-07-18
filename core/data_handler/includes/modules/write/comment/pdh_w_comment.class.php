<?php
/*
* Project:		EQdkp-Plus
* License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
* Link:			http://creativecommons.org/licenses/by-nc-sa/3.0/
* -----------------------------------------------------------------------
* Began:		2010
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

if(!class_exists('pdh_w_comment')) {
	class pdh_w_comment extends pdh_w_generic {
		public static function __shortcuts() {
		$shortcuts = array('pdh', 'db', 'time', 'embedly'=>'embedly');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public function __construct() {
			parent::__construct();
		}

		public function insert($attach_id, $user_id, $comment, $page, $reply_to) {
			$strComment = xhtml_entity_decode($this->embedly->parseString($comment, 400));
			if($this->db->query("INSERT INTO __comments :params", array(
					'attach_id'		=> $attach_id,
					'date'			=> $this->time->time,
					'userid'		=> $user_id,
					'text'			=> str_replace("\n", "[br]", $strComment),
					'page'			=> $page,
					'reply_to'		=> $reply_to,
					)
				)) {
				$id = $this->db->insert_id();
				$this->pdh->enqueue_hook('comment_update', $id);
				return $id;
			}
			return false;
		}

		public function delete($id) {
			if(!$id) return false;
			$this->db->query("DELETE FROM __comments WHERE id='".$this->db->escape($id)."' OR reply_to='".$this->db->escape($id)."';");
			$this->pdh->enqueue_hook('comment_update', array($id));
			return true;
		}

		public function uninstall($page) {
			if(!$page) return false;
			$this->db->query("DELETE FROM __comments WHERE page='".$this->db->escape($page)."';");
			$this->pdh->enqueue_hook('comment_update');
			return true;
		}

		public function delete_all($attach_id) {
			if(!$attach_id) return false;
			$this->db->query("DELETE FROM __comments WHERE attach_id='".$this->db->escape($attach_id)."';");
			$this->pdh->enqueue_hook('comment_update');
			return true;
		}
		
		public function delete_page($page) {
			if(!$page) return false;
			$this->db->query("DELETE FROM __comments WHERE page='".$this->db->escape($page)."';");
			$this->pdh->enqueue_hook('comment_update');
			return true;
		}

		public function delete_attach_id($page, $attach_id){
			if(!$attach_id) return false;
			$this->db->query("DELETE FROM __comments WHERE page='".$this->db->escape($page)."' AND attach_id='".$this->db->escape($attach_id)."';");
			$this->pdh->enqueue_hook('comment_update');
			return true;
		}
	}
}
?>