<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2015 EQdkp-Plus Developer Team
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU Affero General Public License as published
 *	by the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU Affero General Public License for more details.
 *
 *	You should have received a copy of the GNU Affero General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
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