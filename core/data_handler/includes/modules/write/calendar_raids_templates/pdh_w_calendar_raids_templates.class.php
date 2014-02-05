<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2010
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

if(!defined('EQDKP_INC')){
	die('Do not access this file directly.');
}

if(!class_exists('pdh_w_calendar_raids_templates')){
	class pdh_w_calendar_raids_templates extends pdh_w_generic{

		public function reset() {
			$this->db->query("TRUNCATE TABLE __calendar_raid_templates;");
			$this->pdh->enqueue_hook('calendar_templates_update');
		}

		public function save_template($name, $tpldata){
			$templateid = $this->pdh->get('calendar_raids_templates', 'idbyname', array($name));
			if($templateid > 0){
				$this->delete_template($templateid);
			}
			$objQuery = $this->db->prepare("INSERT INTO __calendar_raid_templates :p")->set(array(
				'name'			=> $name,
				'tpldata'		=> json_encode($tpldata),
			))->execute();
			
			if ($objQuery){
				$id = $objQuery->insertId;
				$this->pdh->enqueue_hook('calendar_templates_update', array($id));
				return $id;
			}
			
			return false;		
		}

		public function delete_template($templateid){
			$objQuery = $this->db->prepare("DELETE FROM __calendar_raid_templates WHERE id=?;")->execute($templateid);
			
			if($objQuery){
				$this->pdh->enqueue_hook('calendar_templates_update', array($templateid));
				return true;
			}
			return false;
		}
	}
}
?>