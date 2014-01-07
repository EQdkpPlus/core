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

if ( !defined('EQDKP_INC') ){
	die('Do not access this file directly.');
}

if ( !class_exists( "pdh_r_styles" ) ) {
	class pdh_r_styles extends pdh_r_generic{
		public static function __shortcuts() {
		$shortcuts = array('pdc', 'db', 'pdh'	);
		return array_merge(parent::$shortcuts, $shortcuts);
	}

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

			$pff_result = $this->db->query("SELECT * FROM __styles ORDER BY enabled DESC, style_name");
			while ( $drow = $this->db->fetch_record($pff_result) ){

				$this->styles[$drow['style_id']] = array(
					'style_id'				=> $drow['style_id'],
					'style_name'			=> $drow['style_name'],
					'style_version'			=> $drow['style_version'],
					'style_contact'			=> $drow['style_contact'],
					'style_author'			=> $drow['style_author'],
					'enabled'				=> $drow['enabled'],
					'template_path'			=> $drow['template_path'],
					'body_background'		=> $drow['body_background'],
					'body_link'				=> $drow['body_link'],
					'body_link_style'		=> $drow['body_link_style'],
					'body_hlink'			=> $drow['body_hlink'],
					'body_hlink_style'		=> $drow['body_hlink_style'],
					'header_link'			=> $drow['header_link'],
					'header_link_style'		=> $drow['header_link_style'],
					'header_hlink'			=> $drow['header_hlink'],
					'header_hlink_style'	=> $drow['header_hlink_style'],
					'tr_color1'				=> $drow['tr_color1'],
					'tr_color2'				=> $drow['tr_color2'],
					'th_color1'				=> $drow['th_color1'],
					'fontface1'				=> $drow['fontface1'],
					'fontface2'				=> $drow['fontface2'],
					'fontface3'				=> $drow['fontface3'],
					'fontsize1'				=> $drow['fontsize1'],
					'fontsize2'				=> $drow['fontsize2'],
					'fontsize3'				=> $drow['fontsize3'],
					'fontcolor1'			=> $drow['fontcolor1'],
					'fontcolor2'			=> $drow['fontcolor2'],
					'fontcolor3'			=> $drow['fontcolor3'],
					'fontcolor_neg'			=> $drow['fontcolor_neg'],
					'fontcolor_pos'			=> $drow['fontcolor_pos'],
					'table_border_width'	=> $drow['table_border_width'],
					'table_border_color'	=> $drow['table_border_color'],
					'table_border_style'	=> $drow['table_border_style'],
					'input_color'			=> $drow['input_color'],
					'input_border_width'	=> $drow['input_border_width'],
					'input_border_color'	=> $drow['input_border_color'],
					'input_border_style'	=> $drow['input_border_style'],
					'attendees_columns'		=> $drow['attendees_columns'],
					'logo_position'			=> $drow['logo_position'],
					'background_img'		=> $drow['background_img'],
					'css_file'				=> $drow['css_file'],
					'use_db_vars'			=> $drow['use_db_vars'],
					'portal_width'		=> $drow['portal_width'],
					'column_right_width'	=> $drow['column_right_width'],
					'column_left_width'		=> $drow['column_left_width'],
					'users'					=> $this->pdh->get('user','stylecount', array($drow['style_id']))
				);
			}
			$this->db->free_result($pff_result);
			if($pff_result) $this->pdc->put('pdh_styles_table', $this->styles, null);
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
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_r_styles', pdh_r_styles::__shortcuts());
?>