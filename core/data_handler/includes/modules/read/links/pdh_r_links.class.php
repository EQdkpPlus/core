<?php
/*
* Project:		EQdkp-Plus
* License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
* Link:			http://creativecommons.org/licenses/by-nc-sa/3.0/
* -----------------------------------------------------------------------
* Began:		2009
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

if ( !class_exists( "pdh_r_links" ) ){
	class pdh_r_links extends pdh_r_generic{
		public static function __shortcuts() {
		$shortcuts = array('pdc', 'db', 'user', 'bbcode'=>'bbcode'	);
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public $links;

		public $hooks = array(
			'links',
		);

		public function reset(){
			$this->pdc->del('pdh_links_table');
			$this->links = NULL;
		}

		public function init(){
			// try to get from cache first
			$this->links = $this->pdc->get('pdh_links_table');
			if( $this->links !== NULL){
				return true;
			}

			$this->links = array();
			$sql = "SELECT
					link_id,
					link_name,
					link_url,
					link_window,
					link_menu,
					link_visibility,
					link_height
					FROM
					__links
					ORDER BY link_sortid;";
			$r_result = $this->db->query($sql);

			while( $row = $this->db->fetch_record($r_result) ){
				$this->links[$row['link_id']]['id']			= $row['link_id'];
				$this->links[$row['link_id']]['name']		= $row['link_name'];
				$this->links[$row['link_id']]['url']		= $row['link_url'];
				$this->links[$row['link_id']]['window']		= $row['link_window'];
				$this->links[$row['link_id']]['menu']		= $row['link_menu'];
				$this->links[$row['link_id']]['visibility']	= $row['link_visibility'];
				$this->links[$row['link_id']]['height']		= $row['link_height'];
			}
			$this->db->free_result($r_result);

			if($r_result) $this->pdc->put('pdh_links_table', $this->links, NULL);
			return true;
		}

		public function get_id_list(){
			return array_keys($this->links);
		}

		public function get_data ($id){
			return $this->links[$id];
		}

		public function get_name ($id){
			return $this->links[$id]['name'];
		}

		public function get_height ($id){
			return $this->links[$id]['height'];
		}

		public function get_menu($menu_id){
			$menu = array();

			if (is_array($this->links)){
				foreach ($this->links as $link){
					if ($this->handle_permission($link['visibility'])){
						$target = '';
						$extern = false;
						$url = $this->parse_links($link['url']);
						switch ($link['window']){
							case '0':  $target = '_top';
										if (strpos($url, '://') === false){
											$extern = false;
										} else {
											 $extern = true;
										}
								break ;
							case '1':  $target = '_blank';
									   if (strpos($url, '://') === false){
											$extern = false;
										} else {
											 $extern = true;
										}
								break ;
							case '2':
							case '3':
							case '4': $url = 'wrapper.php'.$this->SID.'&amp;id='.$link['id'];
								break ;
						}


						$menu[$link['menu']][] = array('link' => $url, 'target' => $target, 'text' =>  $link['name'], 'check' => '', 'plus_link' => $extern, 'id'=>'pluslink'.$link['id']);
					}
				}
				return (isset($menu[$menu_id]) && is_array($menu[$menu_id]) ? $menu[$menu_id] : array());
			} else {
				return array();
			}
		}

		private function handle_permission($visibility){
			$perm = false;

			switch ($visibility){
				//Public links
				case '0':  $perm = true;
					break;
				//Guests
				case '1':  $perm = (!$this->user->is_signedin()) ? true : false;
					break ;
				//Logged-Ins
				case '2':  $perm = ($this->user->is_signedin()) ? true : false;
					break ;
				//Admins
				case '3':  $perm = ($this->user->check_auth('a_', false)) ? true : false;
					break ;
			}
			return $perm;
		}

		private function parse_links($text){
			return $this->bbcode->parse_shorttags($text, array('server', 'user', 'guild'));
		}
	}//end class
}//end if
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_r_links', pdh_r_links::__shortcuts());
?>