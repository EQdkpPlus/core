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
		public static function __shortcuts() {
		$shortcuts = array('pdh', 'db', 'config'	);
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public function __construct() {
			parent::__construct();
		}

		public function update_status($id, $status=1){
			$this->db->query('UPDATE __styles SET :params WHERE style_id="'.$this->db->escape($id).'"', array(
				'enabled'			=> $this->db->escape($status),
			));
			$this->pdh->enqueue_hook('styles_update');
		}

		public function update_version($version, $style_id){
			$this->db->query('UPDATE __styles SET :params WHERE style_id="'.$this->db->escape($style_id).'"', array(
				'style_version'		=> $this->db->escape($version),
			));
			$this->pdh->enqueue_hook('styles_update');
		}

		public function delete_style($styleid){
			$this->db->query("DELETE FROM __styles WHERE style_id='".$this->db->escape($styleid)."'");
			$this->db->query("UPDATE __users SET :params WHERE user_style='".$this->db->escape($styleid)."'", array(
				'user_style' => $this->db->escape($this->config->get('default_style')),
			));
			$this->pdh->enqueue_hook('styles_update');
		}
		
		public function insert_styleparams($style){
			$this->db->query("INSERT INTO __styles :params", array(
				'style_name'	=> $style,
				'template_path'	=> $style,
				'enabled'	=> '1',
				'use_db_vars'	=> 1,
			));
			$this->pdh->enqueue_hook('styles_update');
		}
		
		public function add_style($data){
			$this->db->query('INSERT INTO __styles :params', $data);
			$this->pdh->enqueue_hook('styles_update');
			return $this->db->insert_id();
		}
		
		public function update_style($styleid ,$data){
			$this->db->query('UPDATE __styles SET :params WHERE style_id="'.$this->db->escape($styleid).'"', $data);
			$this->pdh->enqueue_hook('styles_update');
			return $styleid;
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_w_styles', pdh_w_styles::__shortcuts());
?>