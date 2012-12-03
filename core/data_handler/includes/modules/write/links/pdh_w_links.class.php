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

		public function save_links($p_linkname, $p_linkurl, $p_linkwindow, $p_link_visibility, $p_link_height){
			$arrReturn = array();
			foreach ( $p_linkname as $link_id => $link_name ){

				//Insert a new link
				if (strpos($link_id, 'new') === 0){
					if (strlen($p_linkurl[$link_id]) && strlen($link_name)){
						//get menu
						$menuid = substr($link_id,-1);
						$this->db->query("INSERT INTO __links :params", array(
							'link_name'			=> $link_name,
							'link_url'			=> $p_linkurl[$link_id],
							'link_window'		=> ( isset($p_linkwindow[$link_id]) ) ? $p_linkwindow[$link_id] : 0,
							'link_menu'			=> $menuid,
							'link_visibility'	=> ( isset($p_link_visibility[$link_id]) ) ? $p_link_visibility[$link_id] : 0,
							'link_height'		=> ( isset($p_link_height[$link_id]) && strlen($p_link_height[$link_id])) ? $p_link_height[$link_id] : 4024,
						));
						$arrReturn[$link_id] = $this->db->insert_id();
					}				
				} elseif (strlen($p_linkurl[$link_id]) && strlen($link_name)) {
					//Update an existing link
					$link_name			= ( isset($p_linkname[$link_id]) ) ? $p_linkname[$link_id] : '';
					$link_url			= ( isset($p_linkurl[$link_id]) ) ? $p_linkurl[$link_id] : '';
					$link_window		= ( isset($p_linkwindow[$link_id]) ) ? $p_linkwindow[$link_id] : 0;
					$link_visibility	= ( isset($p_link_visibility[$link_id]) ) ? $p_link_visibility[$link_id] : 0;
					$link_height		= ( isset($p_link_height[$link_id]) ) ? $p_link_height[$link_id] : 4024;
					
					$arrLink = $this->pdh->get('links', 'data', array($link_id));
					if ($arrLink['name'] != $link_name OR $arrLink['url'] != $link_url OR (int)$arrLink['window'] != (int)$link_window OR (int)$arrLink['visibility'] != (int)$link_visibility OR (int)$arrLink['height'] != (int)$link_height){
						$this->db->query("UPDATE __links SET :params WHERE link_id=?", array(
							'link_name'			=> $link_name,
							'link_url'			=> $link_url,
							'link_window'		=> $link_window,
							'link_visibility'	=> $link_visibility,
							'link_height'		=> $link_height,
						), $link_id);
					}
				} else {
					$this->delete_link($link_id);
				}	
			}
			$this->pdh->enqueue_hook('links');
			return $arrReturn;
		}

		public function delete_link($id){
			$this->db->query("DELETE FROM __links WHERE link_id = '".$this->db->escape($id)."'");
			$this->pdh->enqueue_hook('links', array($id));
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_w_links', pdh_w_links::__shortcuts());
?>