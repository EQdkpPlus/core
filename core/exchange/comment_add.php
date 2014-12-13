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

if (!defined('EQDKP_INC')){
	die('Do not access this file directly.');
}

if (!class_exists('exchange_comment_add')){
	class exchange_comment_add extends gen_class {
		public static $shortcuts = array('pex'=>'plus_exchange');
		public $options		= array();

		public function post_comment_add($params, $body){
			 // be sure user is logged in
			if ($this->user->is_signedin()){
				 $xml = simplexml_load_string($body);
				 if ($xml && strlen($xml->comment)){
					//Check for page and attachid
					if (!$xml->page || !$xml->attachid) return $this->pex->error('page or attachid is missing');
					
					$intReplyTo = ((int)$xml->reply_to) ? (int)$xml->reply_to : 0;
					
					$this->pdh->put('comment', 'insert', array((string)$xml->attachid, $this->user->id, (string)strip_tags($xml->comment), (string)$xml->page, $intReplyTo));
					
					$this->pdh->process_hook_queue();
					return array('status'	=> 1);
				 } else {
					return $this->pex->error('comment is missing');
				 }
			
			} else {
				return $this->pex->error('access denied');
			}
		}
	}
}
?>