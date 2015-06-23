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

if (!class_exists("styles")){
	class styles extends gen_class {
		private $update_styles = array();
		private $arrIgnoreFolder = array(
			'maintenance',
			'install',
			'base_template',
		);

		private $allowed_colors = array(
			'attendees_columns',
			'logo_position',
			'background_img',
			'background_pos',
			'background_type',
			'column_left_width',
			'column_right_width',
			'portal_width',
				
			'body_background_color',
			'body_font_color',
			'body_font_size',
			'body_font_family',
			'body_link_color',
			'body_link_color_hover',
			'body_link_decoration',
			'container_background_color',
			'container_border_color',
			'content_background_color',
			'content_font_color',
			'content_font_color_headings',
			'content_link_color',
			'content_link_color_hover',
			'content_border_color',
			'content_accent_color',
			'content_contrast_color',	
			'content_contrast_background_color',
			'content_contrast_border_color',
			'content_highlight_color',
			'content_positive_color',
			'content_negative_color',
			'content_neutral_color',
			'userarea_background_color',
			'userarea_font_color',
			'userarea_link_color',
			'userarea_link_color_hover',
			'table_th_background_color',
			'table_th_font_color',
			'table_tr_font_color',
			'table_tr_background_color1',
			'table_tr_background_color2',
			'table_tr_background_color_hover',
			'table_border_color',
			'menu_background_color',
			'menu_font_color',
			'menu_item_background_color',
			'menu_item_background_color_hover',
			'menu_item_font_color_hover',
			'sidebar_background_color',
			'sidebar_font_color',
			'sidebar_border_color',
			'button_background_color',
			'button_font_color',
			'button_border_color',
			'button_background_color_hover',
			'button_font_color_hover',
			'button_border_color_hover',
			'input_background_color',
			'input_border_color',
			'input_font_color',
			'input_background_color_active',
			'input_border_color_active',
			'input_font_color_active',
			'misc_color1',
			'misc_color2',
			'misc_color3',
			'misc_text1',
			'misc_text2',
			'misc_text3',
			'additional_less',
		);

		public $allowed_extensions = array(
			'htm',
			'html',
			'css',
			'js',
			'tpl',
		);

		//Disable Style - user can't use it
		public function enable($styleid){
			$styleid = intval($styleid);
			$this->pdh->put('styles', 'update_status', array($styleid, '1'));
			$this->pdh->process_hook_queue();
			$this->core->message(sprintf($this->user->lang('enable_style_suc'), $this->pdh->get('styles', 'templatename', array($styleid))), $this->user->lang('success'), 'green');
		}

		//Enable Style
		public function disable($styleid){
			$styleid = intval($styleid);
			$this->pdh->put('styles', 'update_status', array($styleid, '0'));
			$this->pdh->process_hook_queue();
			$this->core->message(sprintf($this->user->lang('disable_style_suc'), $this->pdh->get('styles', 'templatename', array($styleid))), $this->user->lang('success'), 'green');
		}

		//Set Default Style - expects $this->in-> params
		public function default_style(){
			if (is_numeric($this->in->get('standard_style'))){
				$this->config->set('default_style', $this->in->get('standard_style'));
				if ($this->in->get('override') == 1){
					$this->pdh->put('user', 'update_userstyle', array($this->in->get('standard_style')));
				}
				$this->core->message(sprintf($this->user->lang('default_style_suc'), $this->pdh->get('styles', 'templatename', array($this->in->get('styleid'), '1'))), $this->user->lang('success'), 'green');
				$this->pdh->process_hook_queue();
			}
		}

		public function reset($styleid, $updateColors = true, $deleteChangedFiles = true, $update = false){
			$style = $this->pdh->get('styles', 'styles', array($styleid));

			$installer_file = $this->root_path."templates/".$style['template_path']."/package.xml";
			if (file_exists($installer_file)){

					//Get the install instructions
					$xml = simplexml_load_file($installer_file);
					if ($xml){
						$data = array(
							'style_name'	=> (string)$xml->name,
							'style_version'	=> (string)$xml->version,
							'style_author'	=> (string)$xml->author,
							'style_contact'	=> (string)$xml->authorEmail,
							'template_path'	=>($xml->settings->template_path) ? (string)$xml->settings->template_path : $style['template_path'],
							'enabled'		=> '1'
						);

						$data_array = array();

						if ($updateColors){
							$settings_file = $this->root_path."templates/".$style['template_path']."/settings.xml";
							if (file_exists($settings_file)){
								$settings_xml = simplexml_load_file($settings_file);
								if ($settings_xml){
									foreach($this->allowed_colors as $color){
										$data_array[$color] = (string)$settings_xml->$color;
									}
									
									$data		= array_merge($data, $data_array);
								
									$style_id	= $this->pdh->put('styles', 'update_style', array($styleid,$data));
									
									if (isset($settings_xml->classcolors) && $style_id > 0){
										$this->ClassColorManagement($style_id, $settings_xml->classcolors, true);
									}
								}
							} else {
								$this->pdh->put('class_colors', 'delete_classcolor', array($styleid));
							}
							
						} else {
							$this->pdh->put('styles', 'update_version', array((string)$xml->version, $styleid));
						}
					}
					
					if (!$update){
						$this->core->message(sprintf($this->user->lang('style_reset_success'), $style['style_name']), $this->user->lang('success'), 'green');
					} else {
						$this->core->message(sprintf($this->user->lang('update_style_suc'), $style['style_name']), $this->user->lang('success'), 'green');
					}

			} else {
				$this->core->message($this->user->lang('style_installfile_not_found'), $this->user->lang('error'), 'red');
			}

			//Delete edited template files
			if ($deleteChangedFiles){
				$storage_folder  = $this->pfh->FolderPath('templates/'.$style['template_path'], 'eqdkp');
				$this->pfh->Delete($storage_folder);
			}

			//Delete Cache
			$this->helperDeleteCompleteCache();
		}

		public function install($stylename){
			if (!strlen($stylename)) return false;

			//Get the styles
			$styles = $this->pdh->aget('styles', 'templatename', 0, array($this->pdh->get('styles', 'id_list')));

			$installer_file = $this->root_path."templates/".$stylename."/package.xml";
			if (file_exists($installer_file)){

				//Get the install instructions
				$xml = simplexml_load_file($installer_file);
				if ($xml){
					$data = array(
						'style_name'	=> (string)$xml->name,
						'style_version'	=> (string)$xml->version,
						'style_author'	=> (string)$xml->author,
						'style_contact'	=> (string)$xml->authorEmail,
						'template_path'	=>($xml->folder) ? (string)$xml->folder : $stylename,
						'enabled'				=> '1'
					);
					if (!in_array($data['style_name'], $styles)){
						$data_array = array();
						$blnClassColors = false;
						
						$settings_file = $this->root_path."templates/".$stylename."/settings.xml";
						if (file_exists($settings_file)){
							$settings_xml = simplexml_load_file($settings_file);
							if ($settings_xml){
								foreach($this->allowed_colors as $color){
									$data_array[$color] = (string)$settings_xml->$color;
								}
								$data		= array_merge($data, $data_array);
								
								if (isset($settings_xml->classcolors)) $blnClassColors = true;
							}
							
							
						}
		
						$style_id	= $this->pdh->put('styles', 'add_style', array($data));
						
						if ($blnClassColors && $style_id > 0){
							$this->ClassColorManagement($style_id, $settings_xml->classcolors, false);
						} else {
							$arrClassColorsDefaultStyle = $this->pdh->get('class_colors', 'class_colors', array((int)$this->config->get('default_style')));
							$classdata = $this->game->get_primary_classes();
							foreach($classdata as $class_id => $class_name){
								if (!isset($arrClassColorsDefaultStyle[$class_id])) continue;
								$color = $arrClassColorsDefaultStyle[$class_id];
								$this->pdh->put('class_colors', 'add_classcolor', array($style_id, $class_id, $color));
							}
						}
						$this->core->message( sprintf($this->user->lang('install_style_suc'), $stylename), $this->user->lang('success'), 'green');
					} else {
						$this->core->message( sprintf($this->user->lang('install_style_nosuc'), $stylename), $this->user->lang('error'), 'red');
					}
				}
			} else {
				$this->pdh->put('styles', 'insert_styleparams', array($stylename));
				$this->core->message( sprintf($this->user->lang('install_style_suc'), $stylename), $this->user->lang('success'), 'green');
			}
		}

		public function uninstall($styleid){
			if ($styleid == $this->config->get('default_style')){
				$this->core->message( $this->user->lang('admin_delete_style_error_defaultstyle'), $this->user->lang('error'), 'red');
			}else{
				$style = $this->pdh->get('styles', 'styles', array($styleid));
				$this->pdh->put('styles', 'delete_style', array($styleid));
				$this->pdh->process_hook_queue();
				$storage_folder = $this->pfh->FolderPath('templates/'.$style['template_path'], 'eqdkp');

				if (file_exists($storage_folder)){$this->pfh->Delete($storage_folder);}
				$this->core->message( $this->user->lang('admin_delete_style_success'), $this->user->lang('success'), 'green');
			}
		}
		
		public function remove($stylename){
			$stylename = preg_replace("/[^a-zA-Z0-9-_]/", "", $stylename);
			if($stylename == "") return false;
			$this->pfh->Delete($this->root_path.'templates/'.$stylename.'/');
		}

		public function export($styleid){
			$styleid = intval($styleid);

			$data	= $this->pdh->get('styles', 'styles', array($styleid));
			
			if ($data){
				$template_path = $data['template_path'];
				$style_version = $data['style_version'];
			
				//Create here the package.xml

				$fot ='<?xml version="1.0" encoding="utf-8"?>
<install type="template" version="'.$this->config->get('plus_version').'">
	<name>'.$data['style_name'].'</name>
	<author>'.$data['style_author'].'</author>
	<authorEmail>'.$data['style_contact'].'</authorEmail>
	<authorUrl>'.EQDKP_PROJECT_URL.'</authorUrl>
	<creationDate>'.$this->time->RFC3339($this->time->time).'</creationDate>
	<copyright>'.$data['style_author'].'</copyright>
	<license>CC</license>
	<version>'.$data['style_version'].'</version>
	<description></description>
	<folder>'.$template_path.'</folder>
</install>';

				$storage_folder  = $this->pfh->FolderPath('templates/'.$template_path, 'eqdkp');

				$this->pfh->putContent($storage_folder.'package.xml', $fot);
				
				$fot ='<?xml version="1.0" encoding="utf-8"?>
<settings styleversion="'.$data['style_version'].'">	
	<template_path>'.$data['template_path'].'</template_path>'."\n";
				unset($data['style_id']);
				unset($data['template_path']);
				unset($data['style_name']);
				unset($data['style_version']);
				unset($data['style_author']);
				unset($data['style_contact']);
				unset($data['enabled']);
				unset($data['users']);

				foreach ($data as $key=>$value){
					$fot .= "	<$key>$value</$key>\n";
				}

				$fot.='	<classcolors>'."\n";

				if ($data){
					foreach($this->pdh->get('class_colors', 'class_colors', array($styleid)) as $tclassid=>$tcolor){
						$fot .= "		<cc_$tclassid>$tcolor</cc_$tclassid>\n";
					}
				}

				$fot.= '	</classcolors>'."\n";
				$fot.='</settings>';
				
				$this->pfh->putContent($storage_folder.'settings.xml', $fot);


				$file = $this->pfh->FolderPath('templates', 'eqdkp').$template_path.'_'.$style_version.'.zip';
				$archive = registry::register('zip', array($file));
				$template_root_path = $this->root_path."templates/".$template_path."/";

				//Create the archive
				$archive->add($template_root_path, $this->root_path."templates/");
				$archive->delete($template_path.'/package.xml');
				$archive->delete($template_path.'/settings.xml');
				$archive->add($this->pfh->FolderPath('templates/'.$template_path, 'eqdkp'), $this->pfh->FolderPath('templates', 'eqdkp'));
				$archive->add($storage_folder.'package.xml', $storage_folder);

				$result = $archive->create();

				if (file_exists($file)){
					header('Content-Type: application/octet-stream');
					header('Content-Length: '.$this->pfh->FileSize($file));
					header('Content-Disposition: attachment; filename="'.sanitize($template_path.'_'.$style_version.'.zip').'"');
					header('Content-Transfer-Encoding: binary');
					readfile($file);
					exit;
				}
			} else {
				$this->core->message($this->user->lang('error'), $this->user->lang('error'),'red');
			}
		}

		public function exportChangedFiles($styleid){
			$data	= $this->pdh->get('styles', 'styles', array($styleid));
			$arrChangedFiles = $this->getChangedFiles($data['template_path']);
			$file = $this->pfh->FolderPath('templates', 'eqdkp').$data['template_path'].'_changed_files.zip';
			$archive = registry::register('zip', array($file));
			$archive->add(array_keys($arrChangedFiles), $this->pfh->FolderPath('templates', 'eqdkp'));
			$result = $archive->create();

			if (file_exists($file)){
				header('Content-Type: application/octet-stream');
				header('Content-Length: '.$this->pfh->FileSize($file));
				header('Content-Disposition: attachment; filename="'.$data['template_path'].'_changed_files.zip"');
				header('Content-Transfer-Encoding: binary');
				readfile($file);
				exit;
			}

		}

		public function update($styleid){
			if (!is_numeric($styleid)){
				$arrStylesList = $this->pdh->aget('styles', 'templatepath', 0, array($this->pdh->get('styles', 'id_list')), false);
				$styleid = array_search($styleid, $arrStylesList);
				if ($styleid === false) return false;
			}
			$data	= $this->pdh->get('styles', 'styles', array($styleid));

			$arrChangedFiles = $this->getChangedFiles($data['template_path']);

			foreach ($arrChangedFiles as $key => $file){
				$this->tpl->assign_block_vars('changed_files_row', array(
					'FILE'	=> $file,
					'ENCODED_FILENAME' => base64_encode($file),
				));
			}
			
			$this->jquery->Dialog('diffviewer', $this->user->lang('liveupdate_show_differences'), array('url'=>$this->root_path.'admin/manage_styles.php'.$this->SID.'&styleid='.$styleid.'&diff=\'+file+\'', 'withid'=>'file', 'height'=> '700', 'width'=>'900'));
			
			$this->tpl->assign_vars(array(
				'S_LOCAL_UPDATE'	=> true,
				'S_CHANGED_FILES'	=> (count($arrChangedFiles) > 0) ? true : false,
				'TEMPLATE_ID'		=> $styleid,
			));

			$this->core->set_vars(array(
				'page_title'		=> $this->user->lang('styles_title'),
				'template_file'		=> 'admin/manage_styles.html',
				'display'			=> true)
			);
		}

		public function process_update($styleid){
			$updateColors = ((int)$this->in->get('colors', 0) == 1);
			$deleteChangedFiles = ((int)$this->in->get('template', 0) == 1);

			$this->reset($styleid, $updateColors, $deleteChangedFiles, true);
		}

		public function delete_cache(){
			$this->helperDeleteCompleteCache();
			$this->core->message($this->user->lang('delete_template_cache_success'), $this->user->lang('success'), 'green');
		}

		public function getLocalStyleUpdates(){
			foreach($this->pdh->get('styles', 'styles', array()) as $row){
				$tpl_index = $this->root_path . 'templates/' . $row['template_path'].'/package.xml';
				if (file_exists($tpl_index)){
						$xml = simplexml_load_file($tpl_index);

						$result = compareVersion($xml->version, $row['style_version']);
						if ($result == 1){
							$this->update_styles[$row['template_path']] = array(
								'plugin'			=> $row['template_path'],
								'name'				=> $row['style_name'],
								'version'			=> $xml->version,
								'recent_version'	=> $row['style_version'],
								'changelog'			=> '',
								'level'				=> '',
								'release'			=> $this->time->user_date($xml->creationDate),
							);
						}
				}
			}
			if (count($this->update_styles) >0){
				return $this->update_styles;
			}
			return array();
		}

		public function getUninstalledStyles(){
			$available_templates = array();

			foreach ($this->pdh->get('styles', 'styles') as $row){
				$templates[$row['template_path']] =  $row['template_path'];
			}

			if ( $dir = @opendir($this->root_path . 'templates/') ){
				while ($file = @readdir($dir)){
					if ( (!is_file($this->root_path . 'templates/' . $file)) && (!is_link($this->root_path . 'templates/' . $file)) && valid_folder($file) && !in_array(strtolower($file), $this->arrIgnoreFolder)){
						$tpl_index = $this->root_path . 'templates/' . $file.'/package.xml';

						//iterate through installed templates and check if the template path is installed
						if (!in_array($file, $templates)){
							$install_xml;
							if (file_exists($tpl_index)){
								//Get the install instructions
								$install_xml = simplexml_load_file($tpl_index);
								$available_templates[$file] = $install_xml;
							} else {
								$available_templates[$file] = $file;
							}

						}
					}
				}
			}

			return $available_templates;
		}


		public function ClassColorManagement($template, $xml=false, $with_delete=true) {
			if($with_delete) $this->pdh->put('class_colors', 'delete_classcolor', array($template));
			$classdata = $this->game->get_primary_classes();
			foreach($classdata as $class_id => $class_name){
				$color = (is_object($xml)) ? (string)$xml->{'cc_'.$class_id} : $this->in->get('classc_'.$class_id);
				$this->pdh->put('class_colors', 'add_classcolor', array($template, $class_id, $color));
			}
		}
		
		public function scan_templates($templatepath, $orig_templatepath=false, $remove_templatepath = true){
			$files = array();
			if (!$orig_templatepath) $orig_templatepath = $templatepath;
			
			if ( $dir = @opendir($templatepath) ){
				while ($file = @readdir($dir)){
					if (is_dir($templatepath.'/'.$file) && valid_folder($templatepath.'/'.$file)){
						$files_rec = $this->scan_templates($templatepath.'/'.$file, $templatepath, $remove_templatepath);
						$files = array_merge($files, $files_rec);
					} else {
						$ext = pathinfo($file, PATHINFO_EXTENSION);
						if ($file != "index.php" && $file != "index.html" && $file != 'user_additions.css' && $file != 'jquery_tmpl.css' && in_array($ext, $this->allowed_extensions)){
							$filepath = ($remove_templatepath) ? str_replace($orig_templatepath, '', $templatepath.'/'.$file) : $file;
							$filepath = (substr($filepath, 0, 1) === '/') ? substr($filepath, 1) : $filepath;
							$files[$filepath] = $file;
						}
					}
				}
			}
			return $files;
		}

		//----------------------------------------------------------------------------------------------
		//Helper Functions

		private function getChangedFiles($templatepath){
			$files = array();
			if ( $dir = @opendir($this->pfh->FolderPath('templates/'.$templatepath, 'eqdkp')) ){
				while ($file = @readdir($dir)){
					$ext = pathinfo($file, PATHINFO_EXTENSION);
					if (!is_dir($file) && $file != "index.php" && $file != "index.html" && in_array($ext, $this->allowed_extensions)){
						if (strpos($file, "combined") === 0 || strpos($file, "dev_") === 0) continue;
						$files[$this->pfh->FolderPath('templates/'.$templatepath, 'eqdkp').$file] = $file;
					}
				}
			}
			if ( $dir = @opendir($this->pfh->FolderPath('templates/'.$templatepath.'/admin', 'eqdkp')) ){
				while ($file = @readdir($dir)){
					$ext = pathinfo($file, PATHINFO_EXTENSION);
					if (!is_dir($file) && $file != "index.php" && $file != "index.html" && in_array($ext, $this->allowed_extensions)){
						$files[$this->pfh->FolderPath('templates/'.$templatepath, 'eqdkp').'admin/'.$file] = 'admin/'.$file;
					}
				}
			}
			return $files;
		}

		private function helperDeleteCompleteCache(){
			$this->tpl->delete_cache();

			//Also delete the main.css-files from the styles
			$storage_folder  = $this->pfh->FolderPath('templates', 'eqdkp');

			if ( $dir = @opendir($storage_folder) ){
				while ($file = @readdir($dir)){					
					//Delete the Combined Files
					if(is_dir($storage_folder.$file)){
						$arrDir = sdir($storage_folder.$file, 'combined_*');
						foreach($arrDir as $file){
							$this->pfh->Delete('templates/'.$templatepath.'/'.$file, 'eqdkp');
						}
					}
				}
			}
			
			
		}
		
		public function deleteStyleCache($templatepath){
			//Delete the Combined Files
			$arrDir = sdir($storage_folder = $this->pfh->FolderPath('templates', 'eqdkp').$templatepath, 'combined_*');
			foreach($arrDir as $file){
				$this->pfh->Delete('templates/'.$templatepath.'/'.$file, 'eqdkp');
			}
			
			$this->tpl->delete_cache($templatepath);
		}
		
		
		public function styleOptions(){
			$arrOptions = array(
				'body' => array(
					'body_background_color' => 'color',
					'body_font_color'		=> 'color',
					'body_font_size'		=> 'size',
					'body_font_family'		=> 'font-family',
					'body_link_color'		=> 'color',
					'body_link_color_hover' => 'color',
					'body_link_decoration'	=> 'decoration',	
				),
				'container' => array(
					'container_background_color' => 'color',
					'container_border_color' => 'color',	
				),
				'content' => array(
					'content_background_color' => 'color',
					'content_font_color' => 'color',
					'content_font_color_headings' => 'color',
					'content_link_color' => 'color',
					'content_link_color_hover' => 'color',
					'content_border_color' => 'color',
					'content_accent_color' => 'color',	
					'content_contrast_color'=> 'color',
					'content_contrast_background_color'=> 'color',
					'content_contrast_border_color'=> 'color',
					'content_highlight_color'=> 'color',
					'content_positive_color'=> 'color',
					'content_negative_color'=> 'color',
					'content_neutral_color'=> 'color',
				),
				'userarea' => array(
					'userarea_background_color' => 'color',
					'userarea_font_color' => 'color',
					'userarea_link_color' => 'color',
					'userarea_link_color_hover' => 'color',	
				),
				'table' => array(
					'table_th_background_color' => 'color',
					'table_th_font_color' => 'color',
					'table_tr_font_color' => 'color',
					'table_tr_background_color1' => 'color',
					'table_tr_background_color2' => 'color',
					'table_tr_background_color_hover' => 'color',
					'table_border_color' => 'color',
				),
				'menu' => array(
					'menu_background_color' => 'color',
					'menu_font_color'		=> 'color',
					'menu_item_background_color' => 'color',
					'menu_item_background_color_hover' => 'color',
					'menu_item_font_color_hover' => 'color',	
				),
				'sidebar' => array(
					'sidebar_background_color' => 'color',
					'sidebar_font_color'		=> 'color',
					'sidebar_border_color'		=> 'color',	
				),
				'button' => array(
					'button_background_color' => 'color',
					'button_font_color'			=> 'color',
					'button_border_color'		=> 'color',
					'button_background_color_hover' => 'color',
					'button_font_color_hover'	=> 'color',
					'button_border_color_hover'	=> 'color',	
				),
				'input' => array(
					'input_background_color' => 'color',
					'input_border_color'	=> 'color',
					'input_font_color'		=> 'color',
					'input_background_color_active' => 'color',
					'input_border_color_active' => 'color',
					'input_font_color_active' => 'color',
				),
				'misc' => array(
					'misc_color1' => 'color',
					'misc_color2' => 'color',
					'misc_color3' => 'color',
					'misc_text1' => 'font-family',
					'misc_text2' => 'font-family',
					'misc_text3' => 'font-family',
					'additional_less' => 'text',
				)
			);
			
			return $arrOptions;
		}

		
		public function convertNameToLessVar($strName){
			$strName = preg_replace_callback(
					"/(?:^|_)(.?)/",
					function($m) { return strtoupper($m[1]); },
					$strName
			);

			$strName = 'eqdkp'.$strName;
			return $strName;
		}
	}
	
}
?>