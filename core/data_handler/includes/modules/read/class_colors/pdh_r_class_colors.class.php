<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2007
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

if(!defined('EQDKP_INC'))
{
	die('Do not access this file directly.');
}

if(!class_exists('pdh_r_class_colors')){
	class pdh_r_class_colors extends pdh_r_generic{

		public $default_lang = 'english';

		public $class_colors = array();

		public $hooks = array(
			'classcolors_update'
		);

		public $presets = array();

		public function reset(){
			$this->pdc->del('pdh_classcolors_table');
			$this->class_colors = NULL;
		}

		public function init(){
			$this->class_colors	= $this->pdc->get('pdh_classcolors_table');
			if($this->class_colors !== NULL){
				return true;
			}

			$this->class_colors = array();
			
			$objQuery = $this->db->query("SELECT * FROM __classcolors");
			if($objQuery){
				while($row = $objQuery->fetchAssoc()){
					$this->class_colors[$row['template']][$row['class_id']]	= $row['color'];
				}
				$this->pdc->put('pdh_classcolors_table', $this->class_colors, null);
			}

		}

		public function get_class_colors($templateid){
			return ($templateid) ? ((isset($this->class_colors[$templateid])) ? $this->class_colors[$templateid] : '') : $this->class_colors;
		}
	}
}
?>
