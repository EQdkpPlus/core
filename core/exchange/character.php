<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2016 EQdkp-Plus Developer Team
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

if (!defined('EQDKP_INC')){
	die('Do not access this file directly.');
}

if (!class_exists('exchange_character')){
	class exchange_character extends gen_class {
		public static $shortcuts = array('pex'=>'plus_exchange');
		public $options		= array();

		private function build_form() {
			// Static fields
			$static_fields = array(
					'name'	=> array(
							'type'		=> 'text',
							'lang'		=> 'name',
							'required'	=> true,
							'pattern'	=> '.{1,}',
							'readonly'	=> ($this->url_id > 0 && !$this->adminmode) ? true : false,
							'size'		=> 20
					),
			);


			$static_fields['status'] = array(
							'type'	=> 'radio',
							'lang'	=> 'member_active',
			);

			$maincharsel = $this->pdh->aget('member', 'name', 0, array($this->pdh->get('member', 'id_list', array(false,false,true,true))));
			if (!$this->url_id){
				asort($maincharsel);
				$maincharsel[0] = $this->user->lang('mainchar');
			} else {
				$maincharsel[$this->url_id] = $this->pdh->get('member', 'name', array($this->url_id));
				asort($maincharsel);
			}
			$static_fields['mainid']	= array(
					'type'			=> 'dropdown',
					'options'		=> $maincharsel,
					'lang'			=> 'mainchar',
			);
			$tmpranks		= $this->pdh->aget('rank', 'name', 0, array($this->pdh->get('rank', 'id_list')));
			$static_fields['rankid']	= array(
					'type'			=> 'dropdown',
					'options'		=> $tmpranks,
					'lang'			=> 'rank',
					'default'		=> $this->pdh->get('rank', 'default', array()),
			);

			$arrGameUniqueIDs = $this->game->get_char_unique_ids();
			if (!$arrGameUniqueIDs || !is_array($arrGameUniqueIDs)) $arrGameUniqueIDs = array();

			// Dynamic Fields
			$profilefields = $this->pdh->get('profile_fields', 'fields');
			foreach($profilefields as $fieldid => $fielddata) {
				$fieldname = $fielddata['name'];
				//Set Required for Unique Options
				if (in_array($fieldname, $arrGameUniqueIDs)) {
					$fielddata['required'] = true;
					$fielddata['default'] = $this->config->get($fieldname);
				}

				if($fielddata['type'] == 'imageuploader'){
					$fielddata['returnFormat'] = 'relative';
					$fielddata['imgup_type']	= 'user';
				}

				//Make Dropdowns etc. translatable
				if(count($fielddata['options']) > 0 && $fielddata['options_language'] != ""){
					if (strpos($fielddata['options_language'], 'lang:') === 0){
						$arrSplitted = explode(':', $fielddata['options_language']);
						$arrGlang = $this->game->glang($arrSplitted[1]);
						$arrLang = (isset($arrSplitted[2])) ? $arrGlang[$arrSplitted[2]] : $arrGlang;
					} else $arrLang = $this->game->get($fielddata['options_language']);

					foreach($fielddata['options'] as $key => $val){
						if(isset($arrLang[$key])){
							$fielddata['options'][$key] = $arrLang[$key];
						}
					}
				}

				$fielddata['type'] = ($fielddata['type'] == 'link') ? 'text' : $fielddata['type'];
				$tab = (!empty($fielddata['category']) && in_array($fielddata['category'], $categorynames)) ? $fielddata['category'] : 'character';
				$fielddata['type'] = ($fielddata['type'] === 'link') ? 'text' : $fielddata['type'];

				$static_fields[$fieldname] = $fielddata;
			}

			return $static_fields;
		}

		public function get_character(){
			$isAPITokenRequest = $this->pex->getIsApiTokenRequest();
			if ($isAPITokenRequest){
				return $this->build_form();
			} else {
				return $this->pex->error('access denied');
			}
		}


		public function post_character($params, $arrBody){
			$isAPITokenRequest = $this->pex->isApiWriteTokenRequest();
			if ($isAPITokenRequest){

				$blnTest = (isset($params['get']['test']) && $params['get']['test']) ? true : false;

				if (count($arrBody)){

					$data = array();

					$arrFields = $this->build_form();
					foreach($arrFields as $name => $val){

						if(isset($arrBody[$name])) $data[$name] = $arrBody[$name];

						if ($val['required']) {
							if(!isset($data[$name]) || !strlen($data[$name])) return $this->pex->error('required data missing', $name);
						}
					}

					//Check required values
					if (!isset($data['name']) || !strlen($data['name'])) return $this->pex->error('required data missing', 'name');

					if($blnTest) return array('test' => 'success');

					$intCharID = $this->pdh->put('member', 'addorupdate_member', array(0, $data, false));

					if (!$intCharID) return $this->pex->error('an error occured');

					// member connection to user
					if((int)$arrBody['user_id']){
						$this->pdh->put('member', 'update_connection', array($intCharID, (int)$arrBody['user_id']));
					}

					$this->pdh->process_hook_queue();

					return array('character_id' => $intCharID);
				}
				return $this->pex->error('malformed input');
			} else {
				return $this->pex->error('access denied');
			}
		}
	}
}
