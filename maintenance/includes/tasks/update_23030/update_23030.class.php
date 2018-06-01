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

if ( !defined('EQDKP_INC') ){
  header('HTTP/1.0 404 Not Found');exit;
}

include_once(registry::get_const('root_path').'maintenance/includes/sql_update_task.class.php');

class update_23030 extends sql_update_task {
	public $author			= 'GodMod';
	public $version			= '2.3.0.30'; //new plus-version
	public $ext_version		= '2.3.0'; //new plus-version
	public $name			= '2.3.0 RC11';

	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_23030'	=> 'EQdkp Plus 2.3.0 RC11',
				'update_function'	=> 'Update default style',
			),
			'german' => array(
				'update_23030'	=> 'EQdkp Plus 2.3.0 RC11',
				'update_function'	=> 'Aktualisiere Standard-Style',
			),
		);

	}
	
	public function update_function(){
		$arrTemplates = $this->pdh->get('styles', 'styles');
		$blnHasClean = false;
		foreach($arrTemplates as $row){
			if($row['template_path'] == "eqdkp_clean"){
				$blnHasClean = true;
				$intCleanId = $row['style_id'];
				break;
			}
		}
		
		if(!$blnHasClean){
			$objQuery =	$this->db->query("INSERT INTO `__styles` (`style_name`, `style_version`, `style_contact`, `style_author`, `enabled`, `template_path`, `attendees_columns`, `logo_position`, `favicon_img`, `banner_img`, `background_img`, `column_right_width`, `column_left_width`, `portal_width`, `background_pos`, `background_type`, `body_background_color`, `body_font_color`, `body_font_size`, `body_font_family`, `body_link_color`, `body_link_color_hover`, `body_link_decoration`, `container_background_color`, `container_border_color`, `content_background_color`, `content_font_color`, `content_font_color_headings`, `content_link_color`, `content_link_color_hover`, `content_border_color`, `content_accent_color`, `userarea_background_color`, `userarea_font_color`, `userarea_link_color`, `userarea_link_color_hover`, `table_th_background_color`, `table_th_font_color`, `table_tr_font_color`, `table_tr_background_color1`, `table_tr_background_color2`, `table_tr_background_color_hover`, `table_border_color`, `menu_background_color`, `menu_font_color`, `menu_item_background_color`, `menu_item_background_color_hover`, `menu_item_font_color_hover`, `sidebar_background_color`, `sidebar_font_color`, `sidebar_border_color`, `button_background_color`, `button_font_color`, `button_border_color`, `button_background_color_hover`, `button_font_color_hover`, `button_border_color_hover`, `input_background_color`, `input_border_color`, `input_font_color`, `input_background_color_active`, `input_border_color_active`, `input_font_color_active`, `content_contrast_color`, `content_contrast_background_color`, `content_contrast_border_color`, `content_positive_color`, `content_negative_color`, `content_neutral_color`, `content_highlight_color`, `misc_color1`, `misc_color2`, `misc_color3`, `misc_text1`, `misc_text2`, `misc_text3`, `additional_less`, `additional_fields`, `editor_theme`) VALUES ('EQdkp Clean', '2.3.1', '', 'GodMod', '1', 'eqdkp_clean', '6', 'left', '', '@eqdkpTemplateImagePath/background-head.svg', '', '0px', '0px', '0px', 'normal', 0, '#f5f5f5', '#ffffff', '14', '\"Open Sans\",Arial,Helvetica,sans-serif', '#4e7fa8', '#000000', 'none', '#f3f3f3', 'rgb(56, 56, 56)', '#f5f5f5', 'rgb(0, 0, 0)', '#1D2F3a', '#4e7fa8', 'rgb(0, 0, 0)', 'rgb(204, 204, 204)', 'rgb(234, 234, 234)', '#16334f', '#d9d7d7', 'rgb(217, 215, 215)', 'rgb(255, 255, 255)', '#efefef', '#000000', '', '#f9f9f9', 'rgb(245, 245, 245)', '#e7eff8', '#e0e0e0', '#1a5188', '#ffffff', 'rgba(255, 255, 255, 0)', '#2e78b0', 'rgb(255, 255, 255)', '#eaeaea', 'rgb(0, 0, 0)', '#eaeaea', '#1a5188', '#ffffff', '#1a5188', '#2e78b0', '#ffffff', '#2e78b0', 'rgb(255, 255, 255)', 'rgb(204, 204, 204)', 'rgb(85, 85, 85)', 'rgb(255, 255, 255)', 'rgba(82, 168, 236, 0.8)', 'rgb(85, 85, 85)', 'rgb(0, 0, 0)', '#eaeaea', '#eaeaea', 'rgb(51, 204, 51)', 'rgb(226, 59, 48)', 'rgb(153, 153, 153)', '#125190', '', 'rgb(255, 255, 255)', '', '', '', '', '@eqdkpHeaderFontColor: #fff;\n@styleCommentContainerBackgroundColor: #fff;\n@styleCommentContainerBorderColor: #ccc;\n@styleCommentAuthorColor: #9f9f9f;\n@stylePaginationBorderColor: #ddd;\n@stylePaginationBackgroundColor: #fff;\n@stylePaginationActiveBackgroundColor: #1a5188;\n@stylePaginationActiveColor: #fff;\n@stylePaginationActiveHoverBackgroundColor: #1a5188;\n@stylePaginationActiveHoverColor: #fff;\n@styleArticleSitemapBorderColor: #ddd;\n@styleArticleSitemapBackgroundColor: #fff;\n@styleArticleSitemapActiveBackgroundColor: #1a5188;\n@styleArticleSitemapActiveColor: #fff;\n@styleArticleSitemapHoverColor: #fff;\n@styleBorderRadius:0px;\n@styleShadowColor: #17252d;\n@stylePortalModulesBorderRadius:0px;\n@stylePortalModulesBorderColor:transparent;\n@stylePortalModulesColor:@eqdkpContentFontColor;\n@stylePortalModulesBackgroundColor:@eqdkpContentBackgroundColor;\n@styleContentFooter: #0c0c0c;\n@styleBooleanGreen:#89bf8b;\n@styleBooleanRed:#e89795;\n@styleTableThBorderColor: #4e7fa8;\n@styleAvatarBorderRadius: 50%;\n@styleFeaturedColor: #ffd700;\n@styleBannerBackgroundColorFrom: #2e78b0;\n@styleBannerBackgroundColorTo: #193759;\n@styleBubbleBorderRadius: 9px;', 'a:0:{}', 'lightgray');");
			$intCleanId = $objQuery->insertId;
			
			$this->pdh->enqueue_hook('styles_update');
			
			$this->pdh->process_hook_queue();
		}
		
		$arrTemplatesFor23 = array('eqdkp_diablo', 'eqdkp_neon', 'eqdkp_bluesky', 'eqdkp_luna', 'eqdkp_legion', 'eqdkp_swtor', 'eqdkp_bs', 'eqdkp_modern', 'eqdkp_clean');
		
		$intDefaultStyle = $this->config->get('default_style');
		
		$arrDefaultStyle = $this->pdh->get('styles', 'styles', array($intDefaultStyle));
		$strPath = $arrDefaultStyle['template_path'];
		
		if(!in_array($strPath, $arrTemplatesFor23)){
			$this->config->set('default_style', $intCleanId);
		}
		
		return true;
	}

}

?>