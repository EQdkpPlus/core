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

if(!class_exists('pdh_w_profile_fields')) {
	class pdh_w_profile_fields extends pdh_w_generic {
		public static function __shortcuts() {
		$shortcuts = array('pdh', 'db', 'in'	);
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public function __construct() {
			parent::__construct();
		}

		public function enable_field($field_id){
			$query = $this->db->query('UPDATE __member_profilefields SET enabled="1" WHERE name = "'.$this->db->escape($field_id).'"');
			if ($query) {
				$this->pdh->enqueue_hook('game_update');
				return true;
			} else {
				return false;
			}
		}

		public function disable_field($field_id) {
			$query = $this->db->query('UPDATE __member_profilefields SET enabled="0" WHERE name = "'.$this->db->escape($field_id).'"');
			if ($query) {
				$this->pdh->enqueue_hook('game_update');
				return true;
			} else {
				return false;
			}
		}

		public function delete_fields($fields) {
			if (is_array($fields)) {
				foreach($fields as $value) {
					$query = $this->db->query('DELETE FROM __member_profilefields WHERE name = "'.$this->db->escape($value).'"');
				}
				$this->pdh->enqueue_hook('game_update');
				return true;
			}
			return false;
		}

		public function update_field($id) {
			$options = array();
			if ($this->in->get('type') == 'dropdown'){
				$in_options_id = $this->in->getArray('option_id', 'string');
				$in_options_lang = $this->in->getArray('option_lang', 'string');
				foreach ($in_options_id as $key=>$value){
					if ($value != "" && $in_options_lang[$key] != ""){
						$options[$value] = $in_options_lang[$key];
					}
				}
			}	
			$sql = $this->db->query("UPDATE __member_profilefields SET :params WHERE name=?", array(
				'fieldtype'		=> $this->in->get('type'),
				'category'		=> $this->in->get('category'),
				'language'		=> $this->in->get('language'),
				'size'			=> $this->in->get('size'),
				'image'			=> $this->in->get('image'),
				'visible'		=> '1',
				'options'		=> serialize($options),
			), $id);	
			if(!$sql) {
				return false;
			}
			$this->pdh->enqueue_hook('game_update');
			return true;
		}

		public function insert_field($data=array()){
			$name = preg_replace("/[^a-zA-Z0-9_]/","",utf8_strtolower((isset($data['lang'])) ? $data['lang'] : $this->in->get('language')));
			if (!$name || !strlen($name)) $data['name'] = ((isset($data['fieldtype'])) ? $data['fieldtype'] : $this->in->get('type')).'_'.rand();
			//End if a field with this name exists
			$fields = $this->pdh->get('profile_fields', 'fields');
			if ($fields[$name]){
				return false;
			}

			$options = array();
			if ($this->in->get('type') == 'dropdown'){
				$in_options_id = $this->in->getArray('option_id', 'string');
				$in_options_lang = $this->in->getArray('option_lang', 'string');

				foreach ($in_options_id as $key=>$value){
					if ($value != "" && $in_options_lang[$key] != ""){
						$options[$value] = $in_options_lang[$key];
					}
				}
			}
			$data = array(
				'name'			=> (isset($data['name'])) ? $data['name'] : $name,
				'fieldtype'		=> (isset($data['fieldtype'])) ? $data['fieldtype'] : $this->in->get('type'),
				'category'		=> (isset($data['category'])) ? $data['category'] : $this->in->get('category'),
				'language'		=> (isset($data['lang'])) ? $data['lang'] : $this->in->get('language'),
				'size'			=> (isset($data['size'])) ? intval($data['size']) : $this->in->get('size', 3),
				'options'		=> (isset($data['option'])) ? serialize($data['option']) : serialize($options),
				'visible'		=> '1',
				'image'			=> (isset($data['image'])) ? $data['image'] : $this->in->get('image'),
				'undeletable'	=> (($data['undeletable']) ? '1' : '0'),
				'enabled'		=> 1,
				'custom'		=> ($data['no_custom']) ? '0' : '1'
			);
			$sql = $this->db->query("INSERT INTO __member_profilefields :params", $data);

			if(!$sql) {
				return false;
			}
			$this->pdh->enqueue_hook('game_update');
			return true;
		}
		
		public function truncate_fields() {
			$this->db->query('TRUNCATE TABLE __member_profilefields');
			$this->pdh->enqueue_hook('game_update');
			return true;
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_w_profile_fields', pdh_w_profile_fields::__shortcuts());
?>