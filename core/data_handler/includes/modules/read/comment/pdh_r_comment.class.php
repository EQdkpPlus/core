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

if(!defined('EQDKP_INC'))
{
	die('Do not access this file directly.');
}

if(!class_exists('pdh_r_comment')){
	class pdh_r_comment extends pdh_r_generic{

		public $default_lang = 'english';

		private $comments = array();
		private $count = array();

		public $hooks = array(
			'comment_update',
			'user_update'
		);

		public $presets = array(
		);


		public function reset(){
			$this->pdc->del('pdh_comments_table');
			$this->comments = NULL;
			$this->count = NULL;
		}

		public function init(){
			//cached data not outdated?
			$this->comments = $this->pdc->get('pdh_comments_table');
			if($this->comments !== NULL){
				return true;
			}

			$this->comments = array();
			
			$objQuery = $this->db->query("SELECT com.*, u.username FROM __comments com, __users u WHERE com.userid = u.user_id ORDER BY com.date DESC;");
			if($objQuery){
				while($row = $objQuery->fetchAssoc()){
					$this->comments[$row['id']] = $row;
				}
				
				$this->pdc->put('pdh_comments_table', $this->comments, null);
			}
		}

		public function get_comments() {
			return $this->comments;
		}

		public function get_id_list() {
			return array_keys($this->comments);
		}

		public function get_userid($id) {
			return (isset($this->comments[$id]['userid'])) ? $this->comments[$id]['userid'] : -1;
		}

		public function get_filtered_list($page, $attach_id=false) {
			$comments = array();
			$replies = array();
			foreach($this->comments as $id => $comment) {
				if($comment['page'] != $page) continue;
				if($attach_id > 0 AND $comment['attach_id'] != $attach_id) continue;
				if ((int)$comment['reply_to'] > 0){
					if (isset($replies[(int)$comment['reply_to']])){
						$replies[(int)$comment['reply_to']][] = $comment;
					} else {
						$replies[(int)$comment['reply_to']] = array();
						$replies[(int)$comment['reply_to']][] = $comment;
					}
				} else {
					$arrReplies = isset($replies[(int)$id]) ? $replies[(int)$id] : array();
					$comment['replies'] = array_reverse($arrReplies);
					$comments[(int)$id] = $comment;
				}		
			}
			return $comments;
		}

		public function get_count($page, $attach_id) {
			if(empty($this->count[$page])) {
				foreach($this->comments as $id => $comment) {
					if($page != $comment['page']) continue;
					if(!isset($this->count[$comment['page']][$comment['attach_id']])){
						$this->count[$comment['page']][$comment['attach_id']] = 0;
					}
					$this->count[$comment['page']][$comment['attach_id']]++;
				}
			}
			return (isset($this->count[$page][$attach_id])) ? $this->count[$page][$attach_id] : 0;
		}
	}
}
?>