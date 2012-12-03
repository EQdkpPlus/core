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

if(!class_exists('pdh_w_repository')) {
	class pdh_w_repository extends pdh_w_generic {
		public static function __shortcuts() {
		$shortcuts = array('pdh', 'db'	);
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public function __construct() {
			parent::__construct();
		}

		public function insert($arrData){
			$this->db->query("INSERT INTO __repository :params", $arrData);
			$this->pdh->enqueue_hook('repository_update');
		}

		public function reset() {
			$this->db->query("TRUNCATE TABLE __repository;");
			$this->pdh->enqueue_hook('repository_update');
		}
		
		public function setUpdateTime($time){
			$this->db->query("UPDATE __repository SET :params", array(
				'updated' => $time,
			));
			$this->pdh->enqueue_hook('repository_update');
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_w_repository', pdh_w_repository::__shortcuts());
?>