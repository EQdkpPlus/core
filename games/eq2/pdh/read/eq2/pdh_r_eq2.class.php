<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2011
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

/*+----------------------------------------------------------------------------
  | pdh_r_eq2
  +--------------------------------------------------------------------------*/
if (!class_exists('pdh_r_eq2')) {
	class pdh_r_eq2 extends pdh_r_generic {
		public static $shortcuts = array('core', 'game', 'pdh', 'config');

		/**
		* Hook array
		*/
		public $hooks = array(
			'member_update',
		);

		/**
		* Presets array
		*/
		public $presets = array(
			'eq2_charicon'			=> array('charicon', array('%member_id%'), array()),
		);

		/**
		* Constructor
		*/
		public function __construct(){
		}
	
		public function reset(){
		}

		/**
		* init
		*
		* @returns boolean
		*/
		public function init(){
			$this->game->new_object('eq2_soe', 'soe', array());
			return true;
		}

		public function get_charicon($member_id){
			$picture_id = $this->pdh->get('member', 'picture', array($member_id));
			if ($picture_id){
				return $this->game->obj['soe']->characterIcon($picture_id);
			}
			return '';
		}

		public function get_html_charicon($member_id){
			$charicon = $this->get_charicon($member_id);
			if ($charicon == '') {
				$charicon = $this->root_path.'images/no_pic.png';
			}
			return '<img src="'.$charicon.'" alt="Char-Icon" height="48" />';
		}
	} //end class
} //end if class not exists
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_r_eq2', pdh_r_eq2::$shortcuts);
?>