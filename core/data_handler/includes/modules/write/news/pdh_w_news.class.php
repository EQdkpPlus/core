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

if(!class_exists('pdh_w_news')) {
	class pdh_w_news extends pdh_w_generic {
		public static function __shortcuts() {
		$shortcuts = array('pdh', 'db', 'config', 'bbcode'=>'bbcode', 'embedly'=>'embedly'	);
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public function __construct() {
			parent::__construct();
		}

		public function insert_news($news_headline='', $news_message='', $user_id='', $showRaids_id='', $nocomments='', $news_permissions='', $news_flags='', $news_category='', $news_date='', $news_start='', $news_stop=''){
			$news_message = $this->bbcode->replace_shorttags($news_message);
			if ((int)$this->config->get('disable_embedly') != 1){
				$news_message	= $this->embedly->parseString($news_message);
			}

			$newsmessage	= explode("{{readmore}}", $news_message);

			$this->db->query('INSERT INTO __news :params', array(
				'news_headline'			=> $news_headline,
				'news_message'			=> $newsmessage[0],
				'extended_message'		=> (isset($newsmessage[1])) ? $newsmessage[1] : '',
				'user_id'				=> $user_id,
				'showRaids_id'			=> $showRaids_id,
				'nocomments'			=> $nocomments,
				'news_permissions'		=> $news_permissions,
				'news_flags'			=> $news_flags,
				'news_category'			=> $news_category,
				'news_date'				=> $news_date,
				'news_start'			=> $news_start,
				'news_stop'				=> $news_stop,
			));
			$news_id = $this->db->insert_id();
			$this->pdh->enqueue_hook('news_update', array($news_id));

			// Logging
			$log_action = array(
				'id'					=> $news_id,
				'{L_HEADLINE}'			=> sanitize($news_headline),
				'{L_MESSAGE_BODY}'		=> nl2br(sanitize($newsmessage[0].'<br />'.(isset($newsmessage[1])) ? $newsmessage[1] : '')),
				'{L_ADDED_BY}'			=> $this->admin_user
			);
			$this->log_insert('action_news_added', $log_action);
			return $news_id;
		}

		public function update_news($id, $news_headline='', $news_message='', $user_id='', $showRaids_id='', $nocomments='', $news_permissions='', $news_flags='', $news_category='', $news_date='', $news_start='', $news_stop=''){
			$news_message = $this->bbcode->replace_shorttags($news_message);
			if ((int)$this->config->get('disable_embedly') != 1){
				$news_message	= $this->embedly->parseString($news_message);
			}
			$newsmessage	= explode("{{readmore}}", $news_message);
			$showRaids_id = ($showRaids_id == '') ? NULL : $showRaids_id;
			$old['news_headline']		= $this->pdh->get('news', 'headline', array($id));
			$old['news_message']		= $this->pdh->get('news', 'message', array($id));
			$old['extended_message']	= $this->pdh->get('news', 'extendedmessage', array($id));
			$old['user_id']				= $this->pdh->get('news', 'userid', array($id));
			$old['showRaids_id']		= $this->pdh->get('news', 'showRaidsid', array($id));
			$old['nocomments']			= $this->pdh->get('news', 'nocomments', array($id));
			$old['news_permissions']	= $this->pdh->get('news', 'permissions', array($id));
			$old['news_flags']			= $this->pdh->get('news', 'flags', array($id));
			$old['news_category']		= $this->pdh->get('news', 'category', array($id));
			$old['news_date']			= $this->pdh->get('news', 'date', array($id, true));
			$old['news_start']			= $this->pdh->get('news', 'newsstart', array($id, true));
			$old['news_stop']			= $this->pdh->get('news', 'newsstop', array($id, true));
			$changes = false;
			foreach($old as $varname => $value){
				if(${$varname} === ''){
					${$varname} = $value;
				}else{
					if(${$varname} != $value){
						$changes = true;
					}
				}
			}

			$old['extended_message']	= $this->pdh->get('news', 'extendedmessage', array($id));
			if($extended_message === false) {
				$extended_message = $old['extended_message'];
			} else $changes = true;

			if($changes) {
				$this->db->query("UPDATE __news SET :params WHERE news_id=?", array(
					'news_headline'			=> $news_headline,
					'news_message'			=> $newsmessage[0],
					'extended_message'		=> (isset($newsmessage[1])) ? $newsmessage[1] : '',
					'user_id'				=> $user_id,
					'showRaids_id'			=> $showRaids_id,
					'nocomments'			=> $nocomments,
					'news_permissions'		=> $news_permissions,
					'news_flags'			=> $news_flags,
					'news_category'			=> $news_category,
					'news_date'				=> $news_date,
					'news_start'			=> $news_start,
					'news_stop'				=> $news_stop,
				), $id);
			}
			$this->pdh->enqueue_hook('news_update', array($id));

			// Logging
			$log_action = array(
				'ID'					=> $id,
				'{L_HEADLINE_BEFORE}'	=> sanitize($old['news_headline']),
				'{L_MESSAGE_BEFORE}'	=> nl2br(sanitize($old['news_message'].'<br />'.$old['extended_message'])),
				'{L_HEADLINE_AFTER}'	=> sanitize($news_headline),
				'{L_MESSAGE_AFTER}'		=> nl2br(sanitize($news_message.'<br />'.$extended_message)),
				'{L_UPDATED_BY}'		=> $this->admin_user
			);

			$this->log_insert('action_news_updated', $log_action);
			return $id;
		}

		public function delete_news($id, $multiple=false) {
			if(!$multiple) $id = array($id);
			foreach($id as $news_id){
				$old_headers[$news_id] = $this->pdh->get('news', 'headline', array($news_id));;
			}

			$this->db->query('DELETE FROM __news WHERE news_id IN ('.implode(', ', $id).')');
			$this->pdh->enqueue_hook('news_update');

			// Logging
			foreach($news_ids as $news_id){
				$log_action = array(
					'id'			=> $news_id,
					'{L_HEADLINE}'	=> $old_headers[$news_id],
				);
				$this->log_insert('action_news_deleted', $log_action);
			}
		}

		public function reset() {
			$this->db->query("TRUNCATE TABLE __news;");
			$this->pdh->enqueue_hook('news_update');
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_w_news', pdh_w_news::__shortcuts());
?>