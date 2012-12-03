<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2009
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

if (!defined('EQDKP_INC')){
	die('Do not access this file directly.');
}

if (!class_exists('exchange_comment_add')){
	class exchange_comment_add extends gen_class {
		public static $shortcuts = array('user', 'config', 'pex'=>'plus_exchange', 'pdh', 'time');
		public $options		= array();

		public function post_comment_add($params, $body){
			 // be sure user is logged in
			if ($this->user->is_signedin()){
				 $xml = simplexml_load_string($body);
				 if ($xml && strlen($xml->comment)){
					//Check for page and attachid
					if (!$xml->page || !$xml->attachid) return $this->pex->error('page or attachid is missing');
					
					$this->pdh->put('comment', 'insert', array($xml->attachid, $this->user->id, strip_tags($xml->comment), $xml->page));
					
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
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_exchange_comment_add', exchange_comment_add::$shortcuts);
?>