<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2015 EQdkp-Plus Developer Team
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU Affero General Public License as published
 *	by the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU Affero General Public License for more details.
 *
 *	You should have received a copy of the GNU Affero General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if(!defined('EQDKP_INC')) {
	die('Do not access this file directly.');
}

if(!class_exists('pdh_w_profile_fields')) {
	class pdh_w_profile_fields extends pdh_w_generic {
	
		private $fields = array('name', 'type', 'category', 'lang', 'options_language', 'size', 'data', 'sort', 'image', 'undeletable', 'enabled', 'custom');

		public function enable_field($field_id){
			$objQuery = $this->db->prepare('UPDATE __member_profilefields :p WHERE name=?')->set(array(
				'enabled' => 1	
			))->execute($field_id);
			
			if ($objQuery) {
				$this->pdh->enqueue_hook('game_update');
				$this->pdh->enqueue_hook('member_update');
				return true;
			} else {
				return false;
			}
		}

		public function disable_field($field_id) {
			$objQuery = $this->db->prepare('UPDATE __member_profilefields :p WHERE name=?')->set(array(
					'enabled' => 0
			))->execute($field_id);
			
			if ($objQuery) {
				$this->pdh->enqueue_hook('game_update');
				$this->pdh->enqueue_hook('member_update');
				return true;
			} else {
				return false;
			}
		}

		public function delete_fields($fields) {
			if (is_array($fields)) {
				foreach($fields as $value) {
					$objQuery = $this->db->prepare('DELETE FROM __member_profilefields WHERE name=?')->execute($value);
				}
				$this->pdh->enqueue_hook('game_update');
				$this->pdh->enqueue_hook('member_update');
				return true;
			}
			return false;
		}

		public function update_field($id) {
			$field = $this->pdh->get('profile_fields', 'fields', array($id));
			$options = array();
			if ($this->in->get('type') == 'dropdown' || $this->in->get('type') == 'multiselect'){
				$in_options_id = $this->in->getArray('option_id', 'string');
				$in_options_lang = $this->in->getArray('option_lang', 'string');
				foreach ($in_options_id as $key=>$value){
					if ($value != "" && $in_options_lang[$key] != ""){
						$options[$value] = $in_options_lang[$key];
					}
				}
			}
			$field['data']['options'] = $options;
			
			$objQuery = $this->db->prepare('UPDATE __member_profilefields :p WHERE name=?')->set(array(
				'type'			=> $this->in->get('type'),
				'category'		=> $this->in->get('category'),
				'lang'			=> $this->in->get('language'),
				'options_language' => $this->in->get('options_language'),
				'size'			=> $this->in->get('size'),
				'image'			=> $this->in->get('image'),
				'sort'			=> $this->in->get('sort', 1),
				'data'			=> serialize($field['data']),
			))->execute($id);
				
			if(!$objQuery) {
				return false;
			}
			$this->pdh->enqueue_hook('game_update');
			$this->pdh->enqueue_hook('member_update');
			return true;
		}

		public function insert_field($data=array()){
			if(!isset($data['name'])) {
				$data['name'] = utf8_strtolower((isset($data['lang'])) ? $data['lang'] : $this->in->get('language'));			
			}

			$fields = $this->pdh->get('profile_fields', 'fields');
			if (isset($fields[$data['name']])){
				$data['name'] = $data['name'].'_'.unique_id();
			}

			$options = array();
			if ($this->in->get('type') == 'dropdown' || $this->in->get('type') == 'multiselect'){
				$in_options_id = $this->in->getArray('option_id', 'string');
				$in_options_lang = $this->in->getArray('option_lang', 'string');

				foreach ($in_options_id as $key=>$value){
					if ($value != "" && $in_options_lang[$key] != ""){
						$options[$value] = $in_options_lang[$key];
					}
				}
			}
			foreach($data as $key => $dat) {
				if(!in_array($key, $this->fields)) {
					$data['data'][$key] = $dat;
				}
			}
			$data = array(
				'name'			=> (isset($data['name'])) ? $data['name'] : $name,
				'type'			=> (isset($data['type'])) ? $data['type'] : $this->in->get('type'),
				'category'		=> (isset($data['category'])) ? $data['category'] : $this->in->get('category'),
				'lang'			=> (isset($data['lang'])) ? $data['lang'] : $this->in->get('language'),
				'options_language'=> (isset($data['options_lang'])) ? $data['options_lang'] : $this->in->get('options_language'),
				'size'			=> (isset($data['size'])) ? intval($data['size']) : $this->in->get('size', 3),
				'data'			=> (isset($data['data'])) ? serialize($data['data']) : serialize(array('options' => $options)),
				'sort'			=> (isset($data['sort'])) ? intval($data['sort']) : $this->in->get('sort', 1),
				'image'			=> (isset($data['image'])) ? $data['image'] : $this->in->get('image'),
				'undeletable'	=> (isset($data['undeletable']) && $data['undeletable']) ? '1' : '0',
				'enabled'		=> 1,
				'custom'		=> (isset($data['no_custom']) && $data['no_custom']) ? '0' : '1'
			);
			$objQuery = $this->db->prepare("INSERT INTO __member_profilefields :p")->set($data)->execute();

			if(!$objQuery) {
				return false;
			}
			$this->pdh->enqueue_hook('game_update');
			$this->pdh->enqueue_hook('member_update');
			return true;
		}
		
		public function truncate_fields() {
			$this->db->query('TRUNCATE TABLE __member_profilefields');
			$this->pdh->enqueue_hook('game_update');
			$this->pdh->enqueue_hook('member_update');
			return true;
		}
	}
}
?>