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

class update_2102 extends sql_update_task {
	public $author			= 'GodMod';
	public $version			= '2.1.0.2'; //new plus-version
	public $ext_version		= '2.1.0'; //new plus-version
	public $name			= '2.1.0 Update 3 Alpha 1';
	
	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_2102'	=> 'EQdkp Plus 2.1.0 Update 3',
				'update_function'=> 'Perform some Update for Styles',
				),
			'german' => array(
				'update_2102'	=> 'EQdkp Plus 2.1.0 Update 3',
				'update_function'=> 'Führe einige Updates für Styles aus',
			),
		);
		
		// init SQL querys
		$this->sqls = array();
	}
	
	public function update_function(){
		$this->db->query('ALTER TABLE `__styles`
	DROP COLUMN `body_background`,
	DROP COLUMN `body_link`,
	DROP COLUMN `body_link_style`,
	DROP COLUMN `body_hlink`,
	DROP COLUMN `body_hlink_style`,
	DROP COLUMN `header_link`,
	DROP COLUMN `header_link_style`,
	DROP COLUMN `header_hlink`,
	DROP COLUMN `header_hlink_style`,
	DROP COLUMN `tr_color1`,
	DROP COLUMN `tr_color2`,
	DROP COLUMN `th_color1`,
	DROP COLUMN `fontface1`,
	DROP COLUMN `fontface2`,
	DROP COLUMN `fontface3`,
	DROP COLUMN `fontsize1`,
	DROP COLUMN `fontsize2`,
	DROP COLUMN `fontsize3`,
	DROP COLUMN `fontcolor1`,
	DROP COLUMN `fontcolor2`,
	DROP COLUMN `fontcolor3`,
	DROP COLUMN `fontcolor_neg`,
	DROP COLUMN `fontcolor_pos`,
	DROP COLUMN `table_border_width`,
	DROP COLUMN `table_border_color`,
	DROP COLUMN `table_border_style`,
	DROP COLUMN `input_color`,
	DROP COLUMN `input_border_width`,
	DROP COLUMN `input_border_color`,
	DROP COLUMN `input_border_style`,
	DROP COLUMN `css_file`,
	DROP COLUMN `use_db_vars`;');


		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `body_background_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `body_font_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `body_font_size` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `body_font_family` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `body_link_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `body_link_color_hover` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `body_link_decoration` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `container_background_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `container_border_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `content_background_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `content_font_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `content_font_color_headings` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `content_link_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `content_link_color_hover` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `content_border_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `content_accent_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `userarea_background_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `userarea_font_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `userarea_link_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `userarea_link_color_hover` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `table_th_background_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `table_th_font_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `table_tr_font_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `table_tr_background_color1` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `table_tr_background_color2` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `table_tr_background_color_hover` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `table_border_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `menu_background_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `menu_font_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `menu_item_background_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `menu_item_background_color_hover` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `menu_item_font_color_hover` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `sidebar_background_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `sidebar_font_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `sidebar_border_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `button_background_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `button_font_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `button_border_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `button_background_color_hover` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `button_font_color_hover` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `button_border_color_hover` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `input_background_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `input_border_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `input_font_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `input_background_color_active` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `input_border_color_active` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `input_font_color_active` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");

		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `content_contrast_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `content_contrast_background_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `content_contrast_border_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `content_positive_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `content_negative_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `content_neutral_color` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `misc_color1` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `misc_color2` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		$this->db->query("ALTER TABLE `__styles` ADD COLUMN `misc_color3` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_bin'");
		
		$arrSet = array(
				'style_version' => '0.2.0',
				'body_background_color' => 'rgb(46, 120, 176)', 
				'body_font_color' => 'rgb(255, 255, 255)', 
				'body_font_size' => '13', 
				'body_font_family' => 'Tahoma,Arial,Verdana,sans-serif', 
				'body_link_color' => 'rgb(254, 254, 254)', 
				'body_link_color_hover' => 'rgb(255, 255, 255)', 
				'body_link_decoration' => 'none', 
				'container_background_color' => 'rgb(224, 224, 224)', 
				'container_border_color' => 'rgb(56, 56, 56)', 
				'content_background_color' => 'rgb(245, 245, 245)', 
				'content_font_color' => 'rgb(0, 0, 0)', 
				'content_font_color_headings' => 'rgb(0, 0, 0)', 
				'content_link_color' => 'rgb(78, 127, 168)', 
				'content_link_color_hover' => 'rgb(0, 0, 0)', 
				'content_border_color' => 'rgb(204, 204, 204)', 
				'content_accent_color' => 'rgb(234, 234, 234)', 
				'content_contrast_color' => 'rgb(0, 0, 0)', 
				'content_contrast_background_color' => 'rgb(252, 253, 254)', 
				'content_contrast_border_color' => 'rgb(204, 204, 204)', 
				'content_positive_color' => 'rgb(51, 204, 51)', 
				'content_negative_color' => 'rgb(226, 59, 48)', 
				'content_neutral_color' => 'rgb(153, 153, 153)', 
				'userarea_background_color' => 'rgb(69, 72, 77)', 
				'userarea_font_color' => 'rgb(217, 215, 215)', 
				'userarea_link_color' => 'rgb(217, 215, 215)', 
				'userarea_link_color_hover' => 'rgb(255, 255, 255)', 
				'table_th_background_color' => 'rgb(232, 232, 232)', 
				'table_th_font_color' => 'rgb(0, 0, 0)', 
				'table_tr_font_color' => '', 
				'table_tr_background_color1' => 'rgb(249, 249, 249)', 
				'table_tr_background_color2' => 'rgb(245, 245, 245)', 
				'table_tr_background_color_hover' => 'rgb(232, 232, 232)', 
				'table_border_color' => 'rgb(221, 221, 221)', 
				'menu_background_color' => 'rgb(69, 72, 77)', 
				'menu_font_color' => 'rgb(217, 215, 215)', 
				'menu_item_background_color' => 'rgba(255, 255, 255, 0)', 
				'menu_item_background_color_hover' => 'rgb(0, 0, 0)', 
				'menu_item_font_color_hover' => 'rgb(217, 215, 215)', 
				'sidebar_background_color' => 'rgb(234, 234, 234)', 
				'sidebar_font_color' => 'rgb(0, 0, 0)', 
				'sidebar_border_color' => 'rgb(204, 204, 204)', 
				'button_background_color' => 'rgb(0, 173, 238)', 
				'button_font_color' => 'rgb(228, 245, 252)', 
				'button_border_color' => 'rgb(0, 118, 163)', 
				'button_background_color_hover' => 'rgb(0, 149, 204)', 
				'button_font_color_hover' => 'rgb(217, 238, 247)', 
				'button_border_color_hover' => 'rgb(0, 118, 163)', 
				'input_background_color' => 'rgb(255, 255, 255)', 
				'input_border_color' => 'rgb(204, 204, 204)', 
				'input_font_color' => 'rgb(85, 85, 85)', 
				'input_background_color_active' => 'rgb(255, 255, 255)', 
				'input_border_color_active' => 'rgba(82, 168, 236, 0.8)', 
				'input_font_color_active' => 'rgb(85, 85, 85)', 
				'misc_color1' => '', 
				'misc_color2' => '', 
				'misc_color3' => '', 		
		);
		
		
		$this->db->prepare("UPDATE __styles :p WHERE template_path='eqdkp_modern' ")->set($arrSet)->execute();
		return true;
	}
}


?>