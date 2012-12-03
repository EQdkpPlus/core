<?php
/*
* Project:		EQdkp-Plus
* License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
* Link:			http://creativecommons.org/licenses/by-nc-sa/3.0/
* -----------------------------------------------------------------------
* Began:		2009
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

if(!class_exists('pdh_w_user_groups')) {
	class pdh_w_user_groups extends pdh_w_generic{
		public static function __shortcuts() {
		$shortcuts = array('pdh', 'db'	);
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public function __construct() {
			parent::__construct();
		}

		public function add_grp($id, $name, $desc='', $standard=0, $hide=0, $deletable=1) {
			
			$arrSet = array(
				'groups_user_id' 	=> $id,
				'groups_user_name' => $name,
				'groups_user_desc' => $desc,
				'groups_user_deletable' => $deletable,
				'groups_user_default' => $standard,
				'groups_user_hide' => $hide,
			);
			
			if(!$this->db->query("INSERT INTO __groups_user :params", $arrSet)) {
				return false;
			}
			$this->pdh->enqueue_hook('user_groups_update');
			return true;
		}

		public function update_grp($id, $name='', $desc='', $standard=0, $hide=0) {
			$old = array();
			$old['name']		= $this->pdh->get('user_groups', 'name', array($id));
			$old['desc']		= $this->pdh->get('user_groups', 'desc', array($id));
			$old['standard']	= (int)$this->pdh->get('user_groups', 'standard', array($id));
			$old['hide']		= (int)$this->pdh->get('user_groups', 'hide', array($id));
			$changes = false;
			
			foreach($old as $varname => $value) {
				if(${$varname} === '') {
					${$varname} = $value;
				} else {
					if(${$varname} != $value) {
						$changes = true;
					}
				}
			}

			if ($changes) {
				$arrSet = array(
					'groups_user_name' => $name,
					'groups_user_desc' => $desc,
					'groups_user_default' => $standard,
					'groups_user_hide' => $hide,
				);

				if(!$this->db->query("UPDATE __groups_user SET :params WHERE groups_user_id=?", $arrSet, $id)) {
					return false;
				}
			}
			$this->pdh->enqueue_hook('user_groups_update');
			return true;
		}

		public function delete_grp($id) {
			if ($id == $this->pdh->get('user_groups', 'standard_group', array())) {
				return false;
			} else {
				if($this->db->query("DELETE FROM __groups_user WHERE (groups_user_id = ".$this->db->escape($id)." AND groups_user_deletable != '0' AND groups_user_default != '1');")) {
					$this->pdh->put('user_groups_users', 'delete_all_user_from_group', $id);
					$this->db->query("DELETE FROM __auth_groups WHERE group_id = '".$this->db->escape($id)."'");
					$this->pdh->enqueue_hook('user_groups_update');
					return true;
				}
			}
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_w_user_groups', pdh_w_user_groups::__shortcuts());
?>