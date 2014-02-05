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

if(!class_exists('pdh_w_styles')) {
	class pdh_w_styles extends pdh_w_generic {

		public function update_status($styleid, $status=1){
			$objQuery = $this->db->prepare("UPDATE __styles :p WHERE style_id=?")->set(array(
					'enabled'	=> $status
			))->execute($styleid);
			if(!$objQuery) return false;
			$this->pdh->enqueue_hook('styles_update');
			return true;
		}

		public function update_version($version, $styleid){
			$objQuery = $this->db->prepare("UPDATE __styles :p WHERE style_id=?")->set(array(
					'style_version'	=> $version
			))->execute($style_id);
			if(!$objQuery) return false;
			$this->pdh->enqueue_hook('styles_update');
			return true;
		}

		public function delete_style($styleid){
			$this->db->prepare("DELETE FROM __styles WHERE style_id=?")->execute($style_id);

			$objQuery = $this->db->prepare("UPDATE __users :p WHERE user_style=?")->set(array(
					'user_style' => $this->config->get('default_style'),
			))->execute($styleid);

			$this->pdh->enqueue_hook('styles_update');
			return true;
		}
		
		public function insert_styleparams($style){
			$objQuery = $this->db->prepare("INSERT INTO __styles :p")->set(array(
				'style_name'	=> $style,
				'template_path'	=> $style,
				'enabled'		=> 1,
				'use_db_vars'	=> 1,	
			))->execute();

			if ($objQuery){
				$this->pdh->enqueue_hook('styles_update');
				return $objQuery->insertId;
			}
			
			return false;
		}
		
		public function add_style($data){
			$objQuery = $this->db->prepare("INSERT INTO __styles :p")->set($data)->execute();
			if ($objQuery){
				$this->pdh->enqueue_hook('styles_update');
				return $objQuery->insertId;
			}
			
			return false;
		}
		
		public function update_style($styleid ,$data){
			$objQuery = $this->db->prepare("UPDATE __styles :p WHERE style_id=?")->set($data)->execute($styleid);
			$this->pdh->enqueue_hook('styles_update');
			return $styleid;
		}
	}
}
?>