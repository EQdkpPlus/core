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

if(!class_exists('pdh_w_class_colors')) {
	class pdh_w_class_colors extends pdh_w_generic {
		public static function __shortcuts() {
		$shortcuts = array('pdh', 'db'	);
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public function __construct() {
			parent::__construct();
		}

		public function add_classcolor($template, $clsid='', $color=''){
			$color = (substr($color, 0, 1) == '#') ? $color : ((strlen($color)) ? '#'.$color : '');
			$result = $this->db->query('INSERT INTO __classcolors :params', array(
				'template'		=> $template,
				'class_id'		=> $clsid,
				'color'			=> $color,
			));
			$this->pdh->enqueue_hook('classcolors_update');
			return $this->db->insert_id();
		}

		public function truncate_classcolor() {
			$this->pdh->enqueue_hook('classcolors_update');
			return $this->db->query("TRUNCATE __classcolors");
		}

		public function delete_classcolor($template) {
			$this->db->query("DELETE FROM __classcolors WHERE template=?", false, $template);
			$this->pdh->enqueue_hook('classcolors_update');
		}
		
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_w_class_colors', pdh_w_class_colors::__shortcuts());
?>