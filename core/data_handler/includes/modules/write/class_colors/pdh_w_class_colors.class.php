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
	
		public function add_classcolor($template, $clsid='', $color=''){
			$color = (substr($color, 0, 1) == '#') ? $color : ((strlen($color)) ? '#'.$color : '');
			
			$objQuery = $this->db->prepare('INSERT INTO __classcolors :p')->set(array(
				'template'		=> $template,
				'class_id'		=> $clsid,
				'color'			=> $color,
			))->execute();
			if($objQuery){
				$this->pdh->enqueue_hook('classcolors_update');
				return $objQuery->insertId;
			}
			return false;
		}

		public function truncate_classcolor() {
			$this->pdh->enqueue_hook('classcolors_update');
			return $this->db->query("TRUNCATE __classcolors");
		}

		public function delete_classcolor($template) {
			$objQuery = $this->db->prepare("DELETE FROM __classcolors WHERE template=?")->execute($template);
			$this->pdh->enqueue_hook('classcolors_update');
		}
		
	}
}
?>