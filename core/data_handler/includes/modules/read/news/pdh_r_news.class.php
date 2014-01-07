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

if ( !defined('EQDKP_INC') ){
	die('Do not access this file directly.');
}

if ( !class_exists( "pdh_r_news" ) ) {
	class pdh_r_news extends pdh_r_generic{
		public static function __shortcuts() {
		$shortcuts = array('pdc', 'db', 'pdh', 'user', 'time'	);
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public $default_lang = 'english';
		public $news;

		public $hooks = array(
			'news_update'
		);

		public $presets = array(
			'ncheckbox'		=> array('ncheckbox',	array('%news_id%'),	array()),
			'nedit'			=> array('edit',		array('%news_id%', '%edit_url%'),	array()),
			'nheadline'		=> array('headline',	array('%news_id%'),	array()),
			'ndate'			=> array('date',		array('%news_id%'),	array()),
			'nusername'		=> array('username',	array('%news_id%'),	array()),
			'ncategory'		=> array('category',	array('%news_id%'),	array()),
			'nstart'		=> array('newsstart',	array('%news_id%'),	array()),
			'nstop'			=> array('newsstop',	array('%news_id%'),	array()),
		);

		public function reset(){
			$this->pdc->del('pdh_news_table');
			$this->news = NULL;
		}

		public function init(){
			$this->news			= $this->pdc->get('pdh_news_table');
			if($this->news !== NULL){
				return true;
			}

			$pff_result = $this->db->query("SELECT * FROM __news ORDER BY news_flags DESC, news_date DESC");
			while ($drow = $this->db->fetch_record($pff_result) ){
				$this->news[$drow['news_id']] = array(
					'news_id'			=> $drow['news_id'],
					'news_headline'		=> $drow['news_headline'],
					'news_message'		=> $drow['news_message'],
					'news_date'			=> $drow['news_date'],
					'user_id'			=> $drow['user_id'],
					'username'			=> $this->pdh->get('user', 'name', array($drow['user_id'])),
					'showRaids_id'		=> $drow['showRaids_id'],
					'extended_message'	=> $drow['extended_message'],
					'nocomments'		=> $drow['nocomments'],
					'news_permissions'	=> $drow['news_permissions'],
					'news_flags'		=> $drow['news_flags'],
					'news_category_id'	=> $drow['news_category'],
					'news_category'		=> $this->pdh->get('news_categories', 'name', array($drow['news_category'])),
					'news_start'		=> $drow['news_start'],
					'news_stop'			=> $drow['news_stop'],
				);
			}
				$this->db->free_result($pff_result);
				if($pff_result) $this->pdc->put('pdh_news_table', $this->news, null);
		}

		public function get_id_list(){
			if(is_array($this->news)){
				return array_keys($this->news);
			}else{
				return array();
			}
		}

		public function get_edit($id, $url){
			return '<a href="'.$url.'&amp;n='.$id.'"><img src="'.$this->root_path.'images/glyphs/edit.png" alt="Edit" width="16" height="16" border="0" /></a>';
		}

		public function get_news($id='', $cat_id=''){
			if($cat_id) {
				$re = array();
				if($id) {
					if($this->news[$id]['news_category_id'] == $cat_id) {
						$re[$id] = $this->news[$id];
					}
				} else {
					foreach($this->news as $nid => $news) {
						if($news['news_category_id'] == $cat_id) {
							$re[$nid] = $this->news[$nid];
						}
					}
				}
			} else {
				$re = ($id) ? $this->news[$id] : $this->news;
			}
			return $re;
		}

		public function get_news_ids4cat($cat_id) {
			$re = array();
			foreach($this->news as $nid => $news) {
				if($news['news_category_id'] == $cat_id) {
					$re[] = $nid;
				}
			}
			return $re;
		}

		public function get_search($value) {
			$news_ids = array();
			if (is_array($this->news)){
				foreach($this->news as $id => $news) {
					if(stripos($news['news_headline'], $value) !== false OR stripos($news['news_message'], $value) !== false OR stripos($news['extended_message'], $value) !== false) {
						$news_ids[] = array(
							'id'	=> $this->get_html_date($id),
							'name'	=> $news['news_headline'],
							'link'	=> $this->root_path.'viewnews.php'.$this->SID.'&amp;id='.$id,
						);
					}
				}
			}
			return $news_ids;
		}

		public function get_username($id){
			return $this->news[$id]['username'];
		}

		public function get_userid($id){
			return $this->news[$id]['user_id'];
		}

		public function get_message($id){
			return $this->news[$id]['news_message'];
		}

		public function get_category_id($id) {
			return $this->news[$id]['news_category_id'];
		}

		public function get_category($id){
			return $this->news[$id]['news_category'];
		}

		public function get_flags($id){
			return $this->news[$id]['news_flags'];
		}

		public function get_permissions($id){
			return $this->news[$id]['news_permissions'];
		}

		public function get_showRaidsid($id){
			return $this->news[$id]['showRaids_id'];
		}

		public function get_nocomments($id){
			return $this->news[$id]['nocomments'];
		}

		public function get_extendedmessage($id){
			return $this->news[$id]['extended_message'];
		}

		public function get_date($id){
			return $this->news[$id]['news_date'];
		}

		public function get_html_date($id){
			return $this->time->user_date($this->news[$id]['news_date'], true);
		}

		public function get_user($id){
			return $this->news[$id]['user_id'];
		}

		public function get_headline($id){
			return $this->news[$id]['news_headline'];
		}

		public function get_newsstart($id, $plain=false){
			return ($plain) ? $this->news[$id]['news_start'] : (($this->news[$id]['news_start']) ? $this->time->user_date($this->news[$id]['news_start'], true) : '');
		}

		public function get_newsstop($id, $plain=false){
			return ($plain) ? $this->news[$id]['news_stop'] :  (($this->news[$id]['news_stop']) ? $this->time->user_date($this->news[$id]['news_stop'], true) : '');
		}

		public function get_has_permission($id){
			$news_perm = (isset($this->news[$id]['news_permissions'])) ? (int)$this->news[$id]['news_permissions'] : '';
			switch ($news_perm){
				case 0: return true;
				case 1: if ($this->user->is_signedin() ) return true;
				case 2: if ($this->user->check_auth('a_', false) ) return true;
			}
			return false;
		}


	}//end class
}//end if
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_r_news', pdh_r_news::__shortcuts());
?>