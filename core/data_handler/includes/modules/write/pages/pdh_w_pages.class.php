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

if ( !defined('EQDKP_INC') ){
	die('Do not access this file directly.');
}

if ( !class_exists( "pdh_w_pages" ) ) {
	class pdh_w_pages extends pdh_w_generic {
		public static function __shortcuts() {
		$shortcuts = array('pdh', 'db', 'user', 'bbcode'=>'bbcode', 'embedly'=>'embedly'	);
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public function __construct(){
			parent::__construct();
		}

		public function add($title, $content, $alias='', $menu_link=0, $visibility=array(2), $comments=0, $voting=0) {
			if(!$title OR !$content) return false;
			$content = $this->bbcode->replace_shorttags($content);
			$content = $this->embedly->parseString($content);

			if($this->db->query("INSERT INTO __pages :params ", array(
				"page_title"		=> $title,
				"page_alias"		=> $alias,
				"page_content"		=> $content,
				"page_menu_link"	=> $menu_link,
				"page_edit_user"	=> $this->user->data['user_id'],
				"page_visibility"	=> serialize($visibility),
				"page_edit_date"	=> time(),
				"page_comments"		=> $comments,
				"page_voting"		=> $voting))) {
				$id = $this->db->insert_id();
				$this->pdh->enqueue_hook('pages', array($id));
				return $id;
			}
			return false;
		}

		public function update($id, $title='', $content='', $alias='', $menu_link=99, $visibility=false, $comments=false, $voting=false) {
			if(!$id) return false;
			$old = array(
				'title'				=> $this->pdh->get('pages', 'title', array($id)),
				'alias'				=> $this->pdh->get('pages', 'alias', array($id)),
				'content'			=> $this->pdh->get('pages', 'content', array($id)),
				'menu_link'			=> $this->pdh->get('pages', 'menu_link', array($id)),
				'visibility'		=> $this->pdh->get('pages', 'visibility', array($id)),
				'comments'			=> $this->pdh->get('pages', 'comments', array($id)),
				'voting'			=> $this->pdh->get('pages', 'voting', array($id))
			);

			$content = $this->bbcode->replace_shorttags($content);
			$content = $this->embedly->parseString($content);

			if($this->db->query("UPDATE __pages SET :params WHERE page_id = '".$this->db->escape($id)."'", array(
				"page_title"		=> ($title != '') ? $title : $old['title'],
				"page_alias"		=> ($alias != '') ? $alias : $old['alias'],
				"page_content"		=> ($content != '') ? $content : $old['content'],
				"page_menu_link"	=> ($menu_link != '') ? $menu_link : $old['menu_link'],
				"page_edit_user"	=> $this->user->data['user_id'],
				"page_visibility"	=> serialize((($visibility != false) ? $visibility : $old['visibility'])),
				"page_edit_date"	=> time(),
				"page_comments"		=> ($comments === false) ? $old['comments'] : (($comments) ? 1 : 0),
				"page_voting"		=> ($votings === false) ? $old['voting'] : (($voting) ? 1: 0)))) {
				$this->pdh->enqueue_hook('pages', array($id));
				return true;
			}
		}

		public function delete($id) {
			if(!$id) return false;
			if($this->db->query("DELETE FROM __pages WHERE page_id = '".$this->db->escape($id)."';")) {
				$this->delete_comments($id);
				$this->pdh->enqueue_hook('pages', array($id));
				return true;
			}
			return false;
		}

		public function delete_comments($id) {
			if(!$id) return false;
			if($this->db->query("DELETE FROM __comments WHERE page='pages' AND attach_id='".$this->db->escape($id)."'")) {
				$this->pdh->enqueue_hook('pages', array($id));
				return true;
			}
			return false;
		}

		public function reset_voting($id) {
			if(!$id) return false;
			if($this->db->query("UPDATE __pages SET :params WHERE page_id = '".$this->db->escape($id)."'",
							array('page_ratingpoints' => 0, 'page_votes' => 0, 'page_rating' => 0, 'page_voters' => ""))) {
				$this->pdh->enqueue_hook('pages', array($id));
				return true;
			}
			return false;
		}
	}//end class
}//end if
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_w_pages', pdh_w_pages::__shortcuts());
?>