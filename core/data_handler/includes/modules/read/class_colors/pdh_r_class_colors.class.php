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
		public static function __shortcuts() {
		$shortcuts = array('pdc', 'db'	);
		return array_merge(parent::$shortcuts, $shortcuts);
	}

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
			$result = $this->db->query("SELECT * FROM __classcolors");
			while($row = $this->db->fetch_record($result)){
				$this->class_colors[$row['template']][$row['class_id']]	= $row['color'];
			}
			$this->pdc->put('pdh_classcolors_table', $this->class_colors, null);
			$this->db->free_result($result);
		}

		public function get_class_colors($templateid){
			return ($templateid) ? ((isset($this->class_colors[$templateid])) ? $this->class_colors[$templateid] : '') : $this->class_colors;
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_r_class_colors', pdh_r_class_colors::__shortcuts());
?>
