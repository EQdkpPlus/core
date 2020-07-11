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

if ( !defined('EQDKP_INC') ){
	die('Do not access this file directly.');
}

if ( !class_exists( "pdh_r_user_profilefields" ) ) {
	class pdh_r_user_profilefields extends pdh_r_generic{
		public static function __shortcuts() {
		$shortcuts = array();
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public $default_lang = 'english';
	public $user_profilefields = null;
	private $arrFieldByNameCache = array();

	public $hooks = array(
		'user_profilefields_update',
	);

	public $presets = array(
		'user_profilefields_id' => array('id', array('%intFieldID%'), array()),
		'user_profilefields_name' => array('name', array('%intFieldID%'), array()),
		'user_profilefields_type' => array('type', array('%intFieldID%'), array()),
		'user_profilefields_length' => array('length', array('%intFieldID%'), array()),
		'user_profilefields_minlength' => array('minlength', array('%intFieldID%'), array()),
		'user_profilefields_validation' => array('validation', array('%intFieldID%'), array()),
		'user_profilefields_required' => array('required', array('%intFieldID%'), array()),
		'user_profilefields_show_on_registration' => array('show_on_registration', array('%intFieldID%'), array()),
		'user_profilefields_enabled' => array('enabled', array('%intFieldID%'), array()),
		'user_profilefields_sort_order' => array('sort_order', array('%intFieldID%'), array()),
		'user_profilefields_is_contact' => array('is_contact', array('%intFieldID%'), array()),
		'user_profilefields_contact_url' => array('contact_url', array('%intFieldID%'), array()),
		'user_profilefields_icon_or_image' => array('icon_or_image', array('%intFieldID%'), array()),
		'user_profilefields_bridge_field' => array('bridge_field', array('%intFieldID%'), array()),
	);

	public function reset(){
			$this->pdc->del('pdh_user_profilefields_table');

			$this->user_profilefields = NULL;
			$this->arrFieldByNameCache = array();
	}

	public function init(){
			$this->user_profilefields	= $this->pdc->get('pdh_user_profilefields_table');

			if($this->user_profilefields !== NULL){
				return true;
			}

			$objQuery = $this->db->query('SELECT * FROM __user_profilefields ORDER BY sort_order ASC');
			if($objQuery){
				while($drow = $objQuery->fetchAssoc()){

					$this->user_profilefields[(int)$drow['id']] = array(
						'id'					=> (int)$drow['id'],
						'name'					=> $drow['name'],
						'type'					=> $drow['type'],
						'length'				=> (int)$drow['length'],
						'minlength'				=> (int)$drow['minlength'],
						'validation'			=> $drow['validation'],
						'required'				=> (int)$drow['required'],
						'show_on_registration'	=> (int)$drow['show_on_registration'],
						'enabled'				=> (int)$drow['enabled'],
						'sort_order'			=> (int)$drow['sort_order'],
						'is_contact'			=> (int)$drow['is_contact'],
						'contact_url'			=> $drow['contact_url'],
						'icon_or_image'			=> $drow['icon_or_image'],
						'bridge_field'			=> $drow['bridge_field'],
						'options'				=> $drow['options'],
						'lang_var'				=> $drow['lang_var'],
						'editable'				=> (int)$drow['editable'],
						'example'				=> $drow['example'],
					);
				}
				$this->pdc->put('pdh_user_profilefields_table', $this->user_profilefields, null);
			}

		}	//end init function

		/**
		 * @return multitype: List of all IDs
		 */
		public function get_id_list(){
			if ($this->user_profilefields === null) return array();
			return array_keys($this->user_profilefields);
		}

		public function get_fields(){
			if ($this->user_profilefields === null) return array();
			return $this->user_profilefields;
		}

		/**
		 * Get all data of Element with $strID
		 * @return multitype: Array with all data
		 */
		public function get_data($intFieldID){
			if (isset($this->user_profilefields[$intFieldID])){
				return $this->user_profilefields[$intFieldID];
			}
			return false;
		}

		/**
		 * Returns id for $intFieldID
		 * @param integer $intFieldID
		 * @return multitype id
		 */
		 public function get_id($intFieldID){
			if (isset($this->user_profilefields[$intFieldID])){
				return $this->user_profilefields[$intFieldID]['id'];
			}
			return false;
		}

		/**
		 * Returns name for $intFieldID
		 * @param integer $intFieldID
		 * @return multitype name
		 */
		 public function get_name($intFieldID){
			if (isset($this->user_profilefields[$intFieldID])){
				return $this->user_profilefields[$intFieldID]['name'];
			}
			return false;
		}

		public function get_html_name($intFieldID){
			$strLangVar = $this->get_lang_var($intFieldID);
			if ($strLangVar && strlen($strLangVar) && strlen($this->user->lang($strLangVar))) return $this->user->lang($strLangVar);
			return $this->user->multilangValue($this->get_name($intFieldID));
		}

		/**
		 * Returns type for $intFieldID
		 * @param integer $intFieldID
		 * @return multitype type
		 */
		 public function get_type($intFieldID){
			if (isset($this->user_profilefields[$intFieldID])){
				return $this->user_profilefields[$intFieldID]['type'];
			}
			return false;
		}

		/**
		 * Returns length for $intFieldID
		 * @param integer $intFieldID
		 * @return multitype length
		 */
		 public function get_length($intFieldID){
			if (isset($this->user_profilefields[$intFieldID])){
				return $this->user_profilefields[$intFieldID]['length'];
			}
			return false;
		}

		/**
		 * Returns minlength for $intFieldID
		 * @param integer $intFieldID
		 * @return multitype minlength
		 */
		 public function get_minlength($intFieldID){
			if (isset($this->user_profilefields[$intFieldID])){
				return $this->user_profilefields[$intFieldID]['minlength'];
			}
			return false;
		}

		/**
		 * Returns validation for $intFieldID
		 * @param integer $intFieldID
		 * @return multitype validation
		 */
		 public function get_validation($intFieldID){
			if (isset($this->user_profilefields[$intFieldID])){
				return $this->user_profilefields[$intFieldID]['validation'];
			}
			return false;
		}

		/**
		 * Returns required for $intFieldID
		 * @param integer $intFieldID
		 * @return multitype required
		 */
		 public function get_required($intFieldID){
			if (isset($this->user_profilefields[$intFieldID])){
				return $this->user_profilefields[$intFieldID]['required'];
			}
			return false;
		}

		/**
		 * Returns show_on_registration for $intFieldID
		 * @param integer $intFieldID
		 * @return multitype show_on_registration
		 */
		 public function get_show_on_registration($intFieldID){
			if (isset($this->user_profilefields[$intFieldID])){
				return $this->user_profilefields[$intFieldID]['show_on_registration'];
			}
			return false;
		}

		/**
		 * Returns enabled for $intFieldID
		 * @param integer $intFieldID
		 * @return multitype enabled
		 */
		 public function get_enabled($intFieldID){
			if (isset($this->user_profilefields[$intFieldID])){
				return $this->user_profilefields[$intFieldID]['enabled'];
			}
			return false;
		}

		/**
		 * Returns sort_order for $intFieldID
		 * @param integer $intFieldID
		 * @return multitype sort_order
		 */
		 public function get_sort_order($intFieldID){
			if (isset($this->user_profilefields[$intFieldID])){
				return $this->user_profilefields[$intFieldID]['sort_order'];
			}
			return false;
		}

		/**
		 * Returns is_contact for $intFieldID
		 * @param integer $intFieldID
		 * @return multitype is_contact
		 */
		 public function get_is_contact($intFieldID){
			if (isset($this->user_profilefields[$intFieldID])){
				return $this->user_profilefields[$intFieldID]['is_contact'];
			}
			return false;
		}

		/**
		 * Returns contact_url for $intFieldID
		 * @param integer $intFieldID
		 * @return multitype contact_url
		 */
		 public function get_contact_url($intFieldID){
			if (isset($this->user_profilefields[$intFieldID])){
				return $this->user_profilefields[$intFieldID]['contact_url'];
			}
			return false;
		}

		/**
		 * Returns icon_or_image for $intFieldID
		 * @param integer $intFieldID
		 * @return multitype icon_or_image
		 */
		 public function get_icon_or_image($intFieldID){
			if (isset($this->user_profilefields[$intFieldID])){
				return $this->user_profilefields[$intFieldID]['icon_or_image'];
			}
			return false;
		}

		/**
		 * Returns bridge_field for $intFieldID
		 * @param integer $intFieldID
		 * @return multitype bridge_field
		 */
		 public function get_bridge_field($intFieldID){
			if (isset($this->user_profilefields[$intFieldID])){
				return $this->user_profilefields[$intFieldID]['bridge_field'];
			}
			return false;
		}

		public function get_options($intFieldID){
			if (isset($this->user_profilefields[$intFieldID])){
				return unserialize_noclasses($this->user_profilefields[$intFieldID]['options']);
			}
			return false;
		}

		public function get_lang_var($intFieldID){
			if (isset($this->user_profilefields[$intFieldID])){
				return $this->user_profilefields[$intFieldID]['lang_var'];
			}
			return false;
		}

		public function get_editable($intFieldID){
			if (isset($this->user_profilefields[$intFieldID])){
				return $this->user_profilefields[$intFieldID]['editable'];
			}
			return false;
		}

		public function get_field_by_name($strName){
			$strNameToLower = utf8_strtolower($strName);
			if(isset($this->arrFieldByNameCache[$strNameToLower])) return $this->arrFieldByNameCache[$strNameToLower];

			foreach($this->user_profilefields as $intFieldID => $arrValue){

				if(is_serialized($arrValue['name'])){
					$arrNames = unserialize_noclasses($arrValue['name']);
					foreach($arrNames as $lang => $val){
						if (utf8_strtolower($val) === $strNameToLower){
							$this->arrFieldByNameCache[$strNameToLower] = $intFieldID;
							return $intFieldID;
						}
					}
				} else {
					if (utf8_strtolower($arrValue['name']) === $strNameToLower){
						$this->arrFieldByNameCache[$strNameToLower] = $intFieldID;
						return $intFieldID;
					}
				}
			}
			return false;
		}

		public function get_registration_fields($blnIDsOnly=false){
			$fields = $fieldids = array();

			$arrIDList = $this->get_id_list();
			foreach($arrIDList as $intFieldID){
				if ($this->get_show_on_registration($intFieldID) && $this->get_enabled($intFieldID)){
					$fields['userprofile_'.$intFieldID] = $this->get_create_field($intFieldID);
					$fieldids[] = $intFieldID;
				}
			}

			return ($blnIDsOnly) ? $fieldids : $fields;
		}

		public function get_usersettings_fields($blnIDsOnly=false){
			$fields = $fieldids = array();

			$arrIDList = $this->get_id_list();
			foreach($arrIDList as $intFieldID){
				if ($this->get_enabled($intFieldID) && !$this->get_is_contact($intFieldID)){
					$fields['userprofile_'.$intFieldID] = $this->get_create_field($intFieldID);
					$fieldids[] = $intFieldID;
				}
			}

			return ($blnIDsOnly) ? $fieldids : $fields;
		}

		public function get_contact_fields($blnIDsOnly=false){
			$fields = $fieldids = array();

			$arrIDList = $this->get_id_list();
			foreach($arrIDList as $intFieldID){
				if ($this->get_enabled($intFieldID) && $this->get_is_contact($intFieldID)){
					$fields['userprofile_'.$intFieldID] = $this->get_create_field($intFieldID);
					$fieldids[] = $intFieldID;
				}
			}
			return ($blnIDsOnly) ? $fieldids : $fields;
		}

		public function get_create_field($intFieldID){
			$options = $this->get_options($intFieldID);
			if ($options && isset($options['options'])){
				$arrOptions = $options['options'];
			} else $arrOptions = array();

			$strType = $this->get_type($intFieldID);
			if ($strType == 'link') $strType = "text";

			$myField =  array(
				'type'		=> $strType,
				'lang'		=> $this->get_html_name($intFieldID),
				'required'	=> ($this->get_required($intFieldID)) ? true : false,
				'options'	=> $arrOptions,
				'dir_help'	=> ($this->get_example($intFieldID)) ? $this->get_example($intFieldID) : '',
			);

			$strPattern = $this->get_validation($intFieldID);
			if ($strPattern != "") $myField['pattern'] = $strPattern;

			if($strType == 'birthday'){
				$myField['type'] = 'datepicker';
				$birthday_format = register('user')->style['date_notime_short'];
				if(stripos($birthday_format, 'y') === false) $birthday_format .= 'Y';
				$birthday_format = str_replace('y', 'Y', $birthday_format);

				if (!$this->get_required($intFieldID)) $myField['allow_empty'] = true;
				$myField['year_range'] = '-80:+0';
				$myField['change_fields'] = true;
				$myField['format'] = $birthday_format;
			}

			if ($strType == 'text' || $strType == 'textarea'){
				if ($this->get_length($intFieldID) > 0) $myField['maxlength'] = $this->get_length($intFieldID);
				if ($this->get_minlength($intFieldID) > 0) $myField['minlength'] = $this->get_minlength($intFieldID);
			}

			if ($strType == 'text'){
				$myField['size'] = 40;
			}

			if ($strType == 'textarea'){
				$myField['cols'] = 40;
			}

			if($strType == 'gender'){
				$myField['type'] = 'radio';
				$myField['tolang'] = true;
			}

			if($strType == 'imageuploader'){
				$myField['returnFormat'] = 'relative';
				$myField['imgup_type']	= 'user';
			}

			if($strType == 'bbcode'){
				$myField['type'] = 'bbcodeeditor';
			}

			return $myField;
		}

		/**
		 * Returns example for $intFieldID
		 * @param integer $intFieldID
		 * @return multitype example
		 */
		public function get_example($intFieldID){
			if (isset($this->user_profilefields[$intFieldID])){
				return $this->user_profilefields[$intFieldID]['example'];
			}
			return false;
		}

		public function get_by_type($strType){
			$fields = array();
			$arrIDList = $this->get_id_list();
			foreach($arrIDList as $intFieldID){
				$strMyType = $this->get_type($intFieldID);
				if($strType == $strMyType){
					return $intFieldID;
				}
			}
			return false;
		}

		public function get_bridge_mapping(){
			$fields = array();
			$arrIDList = $this->get_id_list();
			foreach($arrIDList as $intFieldID){
				if ($this->get_enabled($intFieldID) && $this->get_bridge_field($intFieldID) != ""){
					$fields[$this->get_bridge_field($intFieldID)] = $intFieldID;
				}
			}

			return $fields;
		}

		public function get_display_field($intFieldID, $intUserID){
			$strUserValue = $this->pdh->get('user', 'custom_fields', array($intUserID, 'userprofile_'.$intFieldID));
			if ($strUserValue == "" || is_array($strUserValue)) return "";

			$strType = $this->get_type($intFieldID);
			if ($this->get_is_contact($intFieldID)){
				$strFormat = $this->get_contact_url($intFieldID);
				if ($strFormat == "") $strFormat = "%s";
				return sprintf($strFormat, $strUserValue);
			} else {
				switch($strType){
					case 'text':
					case 'int':
					case 'link':
					case 'country':
					case 'datepicker':
					case 'birthday':
					case 'imageuploader':
					case 'bbcode':
						return $strUserValue;
					case 'dropdown':
					case 'radio':
						$arrOptions = $this->get_options($intFieldID);
						if (!in_array($strUserValue, array_keys($arrOptions['options']))) return '';
						return $arrOptions['options'][$strUserValue];

					case 'multiselect':
						$arrOut = array();
						$arrUserValue = $strUserValue;
						$arrOptions = $this->get_options($intFieldID);
						foreach($arrUserValue as $strMemberVal) {
							//Check if Value is in dropdown options
							if (!in_array($strMemberVal, array_keys($arrOptions['options']))) return '';

							$arrOut[] = $arrOptions['options'][$strMemberVal];
						}

						$out = implode(', ', $arrOut);
						return $out;
				}

			}
		}

		public function get_html_display_field($intFieldID, $intUserID, $blnWithIcon=true){
			if ($blnWithIcon && $this->get_icon_or_image($intFieldID) != ""){
				$strIcon = $this->core->icon_font($this->get_icon_or_image($intFieldID), 'fa-lg', $this->server_path.'images/').' ';
			} else $strIcon = "";

			$strUserValue = $this->pdh->get('user', 'custom_fields', array($intUserID, 'userprofile_'.$intFieldID));
			if ($strUserValue == "" || (is_array($strUserValue) && count($strUserValue) == 0)) return "";

			$strType = $this->get_type($intFieldID);


			if ($this->get_is_contact($intFieldID)){
				$strFormat = $this->get_contact_url($intFieldID);
				if ($strFormat == "") $strFormat = "%s";
				$strFormattedString = sprintf($strFormat, $strUserValue);
			} else $strFormattedString = $strUserValue;

			switch($strType){
				case 'text':
				case 'int':
					return $strIcon.$strUserValue;
				case 'bbcode':
					return $this->bbcode->toHTML($strUserValue);
				case 'imageuploader':
					if (strlen($strUserValue)){
						$out = str_replace($this->root_path, $this->server_path, $strUserValue);
					}

					return '<img src="'.$out.'" style="max-width:95%;" class="user-profilefield-'.$intFieldID.' user-additional-image" alt="" loading="lazy"/>';

				case 'link':
					return '<a href="'.$strFormattedString.'" rel="nofollow">'.$strIcon.$strUserValue.'</a>';
				case 'country':
					return '<img src="'.$this->server_path.'images/flags/'.strtolower($strUserValue).'.svg" alt="'.$strUserValue.'" class="coretip" loading="lazy"/>';
				case 'dropdown':
					$arrOptions = $this->get_options($intFieldID);
					if (!in_array($strUserValue, array_keys($arrOptions['options']))) return '';
					return $strIcon.$arrOptions['options'][$strUserValue];

				case 'multiselect':
					$arrOut = array();
					$arrUserValue = $strUserValue;
					$arrOptions = $this->get_options($intFieldID);
					foreach($arrUserValue as $strMemberVal) {
						//Check if Value is in dropdown options
						if (!in_array($strMemberVal, array_keys($arrOptions['options']))) return '';

						$arrOut[] = $arrOptions['options'][$strMemberVal];
					}
					$out = implode(', ', $arrOut);
					return $strIcon.$out;

				case 'gender':
					switch($strUserValue){
						case 'f' : $strGender = $this->user->lang('gender_f');
						break;
						case 'm' : $strGender = $this->user->lang('gender_m');
						break;
						case 'n' : $strGender = $this->user->lang('gender_n');
						break;
						default: $strGender = "";
					}
					return $strGender;

				case 'birthday':
					$intBirthday = intval($strUserValue);
					$age = ($this->time->age($intBirthday) !== 0) ? $this->time->age($intBirthday) : '';
					$val = "";
					if (strlen($age)) {
						$val = ($this->pdh->get('user', 'check_privacy', array($intUserID, 'priv_bday'))) ? $this->time->user_date($intBirthday).' ('.$age.')': $age;
					}
					return $val;
			}

		}

	}//end class
}//end if
