<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2016 EQdkp-Plus Developer Team
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

if(!defined('EQDKP_INC')) {
	die('Do not access this file directly.');
}

if(!class_exists('pdh_w_comment')) {
	class pdh_w_comment extends pdh_w_generic {

		public function insert($attach_id, $user_id, $strComment, $page, $reply_to) {
			if($this->config->get('enable_embedly')) $strComment = $this->embedly->parseString($strComment, 400, false);

			//EmojiOne
			$strComment = register('myemojione')->textToShortcode($strComment);

			$objQuery = $this->db->prepare("INSERT INTO __comments :p")->set(array(
					'attach_id'		=> $attach_id,
					'date'			=> $this->time->time,
					'userid'		=> $user_id,
					'text'			=> str_replace("\n", "[br]", $strComment),
					'page'			=> $page,
					'reply_to'		=> $reply_to,
				))->execute();

			if($objQuery){
				$id = $objQuery->insertId;
				$this->pdh->enqueue_hook('comment_update', $id);

				if($this->hooks->isRegistered('comments_added')){
					$this->hooks->process('comments_added', array('id' => $id, 'data' => array()));
				}

				return $id;
			}
			return false;
		}

		public function update($intCommentId, $strComment){
			if($this->config->get('enable_embedly')) $strComment = $this->embedly->parseString($strComment, 400, false);

			//EmojiOne
			$strComment = register('myemojione')->textToShortcode($strComment);

			$objQuery = $this->db->prepare("UPDATE __comments :p WHERE id=?")->set(array(
					'text'			=> str_replace("\n", "[br]", $strComment),
			))->execute($intCommentId);

			if($objQuery){
				if($this->hooks->isRegistered('comments_updated')){
					$this->hooks->process('comments_updated', array('id' => $intCommentId, 'data' => str_replace("\n", "[br]", $strComment)));
				}

				return true;
			}
			return false;
		}

		public function delete($id) {
			if(!$id) return false;
			$objQuery = $this->db->prepare("DELETE FROM __comments WHERE id=? OR reply_to=?")->execute($id, $id);
			$this->pdh->enqueue_hook('comment_update', array($id));

			if($this->hooks->isRegistered('comments_deleted')){
				$this->hooks->process('comments_deleted', array('id' => $id, 'data' => array()));
			}

			return true;
		}

		public function uninstall($page) {
			if(!$page) return false;
			$objQuery = $this->db->prepare("DELETE FROM __comments WHERE page=?")->execute($page);

			if($this->hooks->isRegistered('comments_deleted')){
				$this->hooks->process('comments_deleted', array('page' => $page, 'data' => array()));
			}

			$this->pdh->enqueue_hook('comment_update');
			return true;
		}

		public function delete_all($attach_id) {
			if(!$attach_id) return false;
			$objQuery = $this->db->prepare("DELETE FROM __comments WHERE attach_id=?")->execute($attach_id);

			if($this->hooks->isRegistered('comments_deleted')){
				$this->hooks->process('comments_deleted', array('attach_id' => $attach_id, 'data' => array()));
			}

			$this->pdh->enqueue_hook('comment_update');
			return true;
		}

		public function delete_page($page) {
			if(!$page) return false;
			$objQuery = $this->db->prepare("DELETE FROM __comments WHERE page=?")->execute($page);

			if($this->hooks->isRegistered('comments_deleted')){
				$this->hooks->process('comments_deleted', array('page' => $page, 'data' => array()));
			}

			$this->pdh->enqueue_hook('comment_update');
			return true;
		}

		public function delete_attach_id($page, $attach_id){
			if(!$attach_id) return false;
			$objQuery = $this->db->prepare("DELETE FROM __comments WHERE page=? AND attach_id=?")->execute($page, $attach_id);

			if($this->hooks->isRegistered('comments_deleted')){
				$this->hooks->process('comments_deleted', array('page' => $page, 'attach_id' => $attach_id, 'data' => array()));
			}

			$this->pdh->enqueue_hook('comment_update');
			return true;
		}
	}
}
