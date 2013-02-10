<?php
/*
* Project:		EQdkp-Plus
* License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
* Link:			http://creativecommons.org/licenses/by-nc-sa/3.0/
* -----------------------------------------------------------------------
* Began:		2007
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

if(!class_exists('pdh_w_rank')) {
	class pdh_w_rank extends pdh_w_generic {
		public static function __shortcuts() {
		$shortcuts = array('pdh', 'db'	);
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public function __construct() {
			parent::__construct();
		}

		public function add_rank($id, $name, $hide=0, $prefix='', $suffix='', $sortid=0) {
			$arrSet = array(
				'rank_id'	=> $id,
				'rank_name' => $name,
				'rank_hide' => $hide,
				'rank_prefix' => $prefix,
				'rank_suffix' => $suffix,
				'rank_sortid' => $sortid,
			);
			if(!$this->db->query("INSERT INTO __member_ranks :params", $arrSet)) {
				return false;
			}
			$this->pdh->enqueue_hook('rank_update', array($id));
			return $id;
		}

		public function update_rank($id, $name='', $hide='', $prefix='', $suffix='', $sortid=0) {
			$old['name'] = $this->pdh->get('rank', 'name', array($id));
			$old['hide'] = $this->pdh->get('rank', 'is_hidden', array($id));
			$old['prefix'] = $this->pdh->get('rank', 'prefix', array($id));
			$old['suffix'] = $this->pdh->get('rank', 'suffix', array($id));
			$old['sortid'] = $this->pdh->get('rank', 'sortid', array($id));
			$changes = false;
			foreach($old as $varname => $value) {
				if(${$varname} != $value) {
					$changes = true;
				}
			}
			if($changes) {
				$arrSet = array(
					'rank_name' => $name,
					'rank_hide' => $hide,
					'rank_prefix' => $prefix,
					'rank_suffix' => $suffix,
					'rank_sortid' => $sortid,
				);
				if(!$this->db->query("UPDATE __member_ranks SET :params WHERE rank_id=?", $arrSet, $id)) {
					return false;
				}
			}
			$this->pdh->enqueue_hook('rank_update', array($id));
			return true;
		}

		public function delete_rank($id) {
			if($this->db->query("DELETE FROM __member_ranks WHERE rank_id = ?;", false, $id)) {
				$this->pdh->enqueue_hook('rank_update', array($id));
				return true;
			}
			return false;
		}
		
		public function truncate(){
			if($this->db->query("TRUNCATE __member_ranks;")) {
				$this->pdh->enqueue_hook('rank_update');
				return true;
			}
			return false;
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_w_rank', pdh_w_rank::__shortcuts());
?>