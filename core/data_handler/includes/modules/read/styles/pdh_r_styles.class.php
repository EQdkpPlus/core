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

if ( !class_exists( "pdh_r_styles" ) ) {
	class pdh_r_styles extends pdh_r_generic{

		public $default_lang = 'english';
		public $styles;

		public $hooks = array(
			'styles_update'
		);

		public function reset(){
			$this->pdc->del('pdh_styles_table');
			$this->styles = NULL;
		}

		public function init(){
			// disable for now until styles.php is fully converted
			$this->styles	= $this->pdc->get('pdh_styles_table');
			if($this->styles !== NULL){
				return true;
			}

			$objQuery = $this->db->query("SELECT * FROM __styles ORDER BY enabled DESC, style_name");
			if($objQuery){
				while($drow = $objQuery->fetchAssoc()){
					$this->styles[$drow['style_id']] = array(
						'style_id'					=> (int)$drow['style_id'],
						'style_name'				=> $drow['style_name'],
						'style_version'				=> $drow['style_version'],
						'style_contact'				=> $drow['style_contact'],
						'style_author'				=> $drow['style_author'],
						'enabled'					=> $drow['enabled'],
						'template_path'				=> $drow['template_path'],
						'attendees_columns'			=> $drow['attendees_columns'],
						'logo_position'				=> $drow['logo_position'],
						'favicon_img'				=> $drow['favicon_img'],
						'banner_img'				=> $drow['banner_img'],
						'background_img'			=> $drow['background_img'],
						'column_right_width'		=> $drow['column_right_width'],
						'column_left_width'			=> $drow['column_left_width'],
						'portal_width'				=> $drow['portal_width'],
						'background_pos'			=> $drow['background_pos'],
						'background_type'			=> (int)$drow['background_type'],
						'body_background_color'		=> $drow['body_background_color'],
						'body_font_color'			=> $drow['body_font_color'],
						'body_font_size'			=> $drow['body_font_size'],
						'body_font_family'			=> $drow['body_font_family'],
						'body_link_color'			=> $drow['body_link_color'],
						'body_link_color_hover'		=> $drow['body_link_color_hover'],
						'body_link_decoration'		=> $drow['body_link_decoration'],
						'container_background_color'=> $drow['container_background_color'],
						'container_border_color'	=> $drow['container_border_color'],
						'content_background_color'	=> $drow['content_background_color'],
						'content_font_color'		=> $drow['content_font_color'],
						'content_font_color_headings'=> $drow['content_font_color_headings'],
						'content_link_color'		=> $drow['content_link_color'],
						'content_link_color_hover'	=> $drow['content_link_color_hover'],
						'content_border_color'		=> $drow['content_border_color'],
						'content_accent_color'		=> $drow['content_accent_color'],
						'userarea_background_color'	=> $drow['userarea_background_color'],
						'userarea_font_color'		=> $drow['userarea_font_color'],
						'userarea_link_color'		=> $drow['userarea_link_color'],
						'userarea_link_color_hover'	=> $drow['userarea_link_color_hover'],
						'table_th_background_color'	=> $drow['table_th_background_color'],
						'table_th_font_color'		=> $drow['table_th_font_color'],
						'table_tr_font_color'		=> $drow['table_tr_font_color'],
						'table_tr_background_color1'=> $drow['table_tr_background_color1'],
						'table_tr_background_color2'=> $drow['table_tr_background_color2'],
						'table_tr_background_color_hover'=> $drow['table_tr_background_color_hover'],
						'table_border_color'		=> $drow['table_border_color'],
						'menu_background_color'		=> $drow['menu_background_color'],
						'menu_font_color'			=> $drow['menu_font_color'],
						'menu_item_background_color'=> $drow['menu_item_background_color'],
						'menu_item_background_color_hover'=> $drow['menu_item_background_color_hover'],
						'menu_item_font_color_hover'=> $drow['menu_item_font_color_hover'],
						'sidebar_background_color'	=> $drow['sidebar_background_color'],
						'sidebar_font_color'		=> $drow['sidebar_font_color'],
						'sidebar_border_color'		=> $drow['sidebar_border_color'],
						'button_background_color'	=> $drow['button_background_color'],
						'button_font_color'			=> $drow['button_font_color'],
						'button_border_color'		=> $drow['button_border_color'],
						'button_background_color_hover'	=> $drow['button_background_color_hover'],
						'button_font_color_hover'	=> $drow['button_font_color_hover'],
						'button_border_color_hover'	=> $drow['button_border_color_hover'],
						'input_background_color'	=> $drow['input_background_color'],
						'input_border_color'		=> $drow['input_border_color'],
						'input_font_color'			=> $drow['input_font_color'],
						'input_background_color_active'	=> $drow['input_background_color_active'],
						'input_border_color_active'	=> $drow['input_border_color_active'],
						'input_font_color_active'	=> $drow['input_font_color_active'],
						'content_contrast_color'	=> $drow['content_contrast_color'],
						'content_contrast_background_color'=> $drow['content_contrast_background_color'],
						'content_contrast_border_color'=> $drow['content_contrast_border_color'],
						'content_positive_color'	=> $drow['content_positive_color'],
						'content_negative_color'	=> $drow['content_negative_color'],
						'content_neutral_color'		=> $drow['content_neutral_color'],
						'content_highlight_color'	=> $drow['content_highlight_color'],
						'misc_color1' 				=> $drow['misc_color1'],
						'misc_color2' 				=> $drow['misc_color2'],
						'misc_color3' 				=> $drow['misc_color3'],
						'misc_text1' 				=> $drow['misc_text1'],
						'misc_text2' 				=> $drow['misc_text2'],
						'misc_text3' 				=> $drow['misc_text3'],
						'additional_less' 			=> $drow['additional_less'],
						'users'						=> $this->pdh->get('user','stylecount', array($drow['style_id'])),
						'additional_fields'			=> ($drow['additional_fields'] != "") ? unserialize_noclasses($drow['additional_fields']) : array(),
						'editor_theme'				=> $drow['editor_theme'],
					);
				}

				$this->pdc->put('pdh_styles_table', $this->styles, null);
			}
		}

		public function get_styles($styleid=0, $all=true){
			if($all){
				return ($styleid > 0) ? $this->styles[$styleid] : $this->styles;
			}else{
				$tmp_out = array();
				foreach($this->styles as $stid=>$styleset){
					if($styleset['enabled'] == '1'){
						$tmp_out[$stid] = $styleset;
					}
				}
				return ($styleid > 0) ? $tmp_out[$styleid] : $tmp_out;
			}
		}

		public function get_templatename($styleid){
			return $this->styles[$styleid]['style_name'];
		}

		public function get_templatepath($styleid){
			return $this->styles[$styleid]['template_path'];
		}

		public function get_version($styleid){
			return $this->styles[$styleid]['style_version'];
		}

		public function get_id_list(){
			return array_keys($this->styles);
		}
	}//end class
}//end if
