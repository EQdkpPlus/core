<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * styles.php
 * Began: Thu January 16 2003
 *
 * $Id$
 *
 ******************************/

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class Manage_Styles extends EQdkp_Admin
{
    var $style = array();
		var $allowed_colors = array(
			'attendees_columns',
			'logo_path',
			'background_img',
			'css_file',
			'use_db_vars',
			
			'body_background',
			'body_link',
			'body_link_style',
			'body_hlink',
			'body_hlink_style',
			'header_link',
			'header_link_style',
			'header_hlink',
			'header_hlink_style',
			'tr_color1',
			'tr_color2',
			'th_color1',
			'fontface1',
			'fontface2',
			'fontface3',
			'fontsize1',
			'fontsize2',
			'fontsize3',
			'fontcolor1',
			'fontcolor2',
			'fontcolor3',
			'fontcolor_neg',
			'fontcolor_pos',
			'table_border_width',
			'table_border_color',
			'table_border_style',
			'input_color',
			'input_border_width',
			'input_border_color',
			'input_border_style',
		);
		var $allowed_extensions = array(
			'htm',
			'html',
			'css',
			'js'
		);
		
    function manage_styles()
    {
        global $db, $core, $user, $tpl, $pm;
        global $SID;

        parent::eqdkp_admin();

        // Variables
        $defaults = array(
            'attendees_columns' => 8,
            'logo_path'         => 'logo.gif',
						'use_db_vars'				=> true,
            );

        $this->style = array(
            'style_name'         => post_or_db('style_name'),
						'style_version'      => post_or_db('style_version'),
						'style_author'       => post_or_db('style_author'),
						'style_contact'      => post_or_db('style_contact'),
						'style_code'      	 => post_or_db('style_code'),
            'template_path'      => post_or_db('template_path'),
            'body_background'    => post_or_db('body_background'),
            'body_link'          => post_or_db('body_link'),
            'body_link_style'    => post_or_db('body_link_style'),
            'body_hlink'         => post_or_db('body_hlink'),
            'body_hlink_style'   => post_or_db('body_hlink_style'),
            'header_link'        => post_or_db('header_link'),
            'header_link_style'  => post_or_db('header_link_style'),
            'header_hlink'       => post_or_db('header_hlink'),
            'header_hlink_style' => post_or_db('header_hlink_style'),
            'tr_color1'          => post_or_db('tr_color1'),
            'tr_color2'          => post_or_db('tr_color2'),
            'th_color1'          => post_or_db('th_color1'),
            'fontface1'          => post_or_db('fontface1'),
            'fontface2'          => post_or_db('fontface2'),
            'fontface3'          => post_or_db('fontface3'),
            'fontsize1'          => post_or_db('fontsize1'),
            'fontsize2'          => post_or_db('fontsize2'),
            'fontsize3'          => post_or_db('fontsize3'),
            'fontcolor1'         => post_or_db('fontcolor1'),
            'fontcolor2'         => post_or_db('fontcolor2'),
            'fontcolor3'         => post_or_db('fontcolor3'),
            'fontcolor_neg'      => post_or_db('fontcolor_neg'),
            'fontcolor_pos'      => post_or_db('fontcolor_pos'),
            'table_border_width' => post_or_db('table_border_width'),
            'table_border_color' => post_or_db('table_border_color'),
            'table_border_style' => post_or_db('table_border_style'),
            'input_color'        => post_or_db('input_color'),
            'input_border_width' => post_or_db('input_border_width'),
            'input_border_color' => post_or_db('input_border_color'),
            'input_border_style' => post_or_db('input_border_style'),
            'attendees_columns'  => post_or_db('attendees_columns', $defaults),
            'logo_path'          => post_or_db('logo_path', $defaults),
            'background_img'     => post_or_db('background_img', $defaults),
            'css_file'           => post_or_db('css_file', $defaults),
        		'use_db_vars'		 => post_or_db('use_db_vars', $defaults)
        );

        // Vars used to confirm deletion
        $this->set_vars(array(
            'confirm_text'  => $user->lang['confirm_delete_style'],
            'uri_parameter' => 'styleid')
        );

        $this->assoc_buttons(array(
            'add' => array(
                'name'    => 'add',
                'process' => 'process_add',
                'check'   => 'a_styles_man'),
            'update' => array(
                'name'    => 'update',
                'process' => 'process_update',
                'check'   => 'a_styles_man'),
						'version_update' => array(
                'name'    => 'version_update',
                'process' => 'process_version_update',
                'check'   => 'a_styles_man'),
            'delete' => array(
                'name'    => 'delete',
                'process' => 'process_delete',
                'check'   => 'a_styles_man'),
						 'edit_template' => array(
                'name'    => 'template_edit_button',
                'process' => 'process_edit_template',
                'check'   => 'a_styles_man'),
            'form' => array(
                'name'    => '',
                'process' => 'display_list',
                'check'   => 'a_styles_man'))
        );

        $this->assoc_params(array(
            'create' => array(
                'name'    => 'mode',
                'value'   => 'create',
                'process' => 'display_form',
                'check'   => 'a_styles_man'),
						'cache' => array(
                'name'    => 'cache',
                'value'   => 'reset',
                'process' => 'process_delete_cache',
                'check'   => 'a_styles_man'),
						'enable' => array(
                'name'    => 'enable',
                'value'   => 'true',
                'process' => 'process_enable',
                'check'   => 'a_styles_man'),
						'disable' => array(
                'name'    => 'disable',
                'value'   => 'true',
                'process' => 'process_disable',
                'check'   => 'a_styles_man'),
						'default' => array(
                'name'    => 'default',
                'value'   => 'true',
                'process' => 'process_default',
                'check'   => 'a_styles_man'),
						'install' => array(
                'name'    => 'install',
								'value'   => 'true',
                'process' => 'process_install',
                'check'   => 'a_styles_man'),
						'delete' => array(
                'name'    => 'delete',
								'value'   => 'true',
                'process' => 'process_delete',
                'check'   => 'a_styles_man'),
						'export' => array(
                'name'    => 'export',
								'value'   => 'true',
                'process' => 'process_export',
                'check'   => 'a_styles_man'),
            'edit' => array(
                'name'    => 'edit',
								'value'   => 'true',
                'process' => 'display_form',
                'check'   => 'a_styles_man'))
        );

        // Build the style array
        // ---------------------------------------------------------
        if ( $this->url_id )
        {
            // Class Color Selection
            $sql = "SELECT * FROM __classcolors WHERE template='" . $this->url_id . "'";
          	$result = $db->query($sql);
          	while ($row = $db->fetch_record($result)){
                $this->classcolor[$row['class']] = $row['color'];
            }
            $db->free_result($result);

            $sql = "SELECT *
                    FROM __styles
                    WHERE style_id='" . $this->url_id . "'";
            $result = $db->query($sql);
            if ( !$row = $db->fetch_record($result) )
            {
                message_die($user->lang['error_invalid_style']);
            }
            $db->free_result($result);

            $this->style = array(
                'style_name'         => post_or_db('style_name', $row),
								'style_version'      => post_or_db('style_version', $row),
								'style_author'       => post_or_db('style_author', $row),
								'style_contact'      => post_or_db('style_contact', $row),
								'style_code'      	 => post_or_db('style_code', $row),
                'template_path'      => post_or_db('template_path', $row),
                'body_background'    => post_or_db('body_background', $row),
                'body_link'          => post_or_db('body_link', $row),
                'body_link_style'    => post_or_db('body_link_style', $row),
                'body_hlink'         => post_or_db('body_hlink', $row),
                'body_hlink_style'   => post_or_db('body_hlink_style', $row),
                'header_link'        => post_or_db('header_link', $row),
                'header_link_style'  => post_or_db('header_link_style', $row),
                'header_hlink'       => post_or_db('header_hlink', $row),
                'header_hlink_style' => post_or_db('header_hlink_style', $row),
                'tr_color1'          => post_or_db('tr_color1', $row),
                'tr_color2'          => post_or_db('tr_color2', $row),
                'th_color1'          => post_or_db('th_color1', $row),
                'fontface1'          => post_or_db('fontface1', $row),
                'fontface2'          => post_or_db('fontface2', $row),
                'fontface3'          => post_or_db('fontface3', $row),
                'fontsize1'          => post_or_db('fontsize1', $row),
                'fontsize2'          => post_or_db('fontsize2', $row),
                'fontsize3'          => post_or_db('fontsize3', $row),
                'fontcolor1'         => post_or_db('fontcolor1', $row),
                'fontcolor2'         => post_or_db('fontcolor2', $row),
                'fontcolor3'         => post_or_db('fontcolor3', $row),
                'fontcolor_neg'      => post_or_db('fontcolor_neg', $row),
                'fontcolor_pos'      => post_or_db('fontcolor_pos', $row),
                'table_border_width' => post_or_db('table_border_width', $row),
                'table_border_color' => post_or_db('table_border_color', $row),
                'table_border_style' => post_or_db('table_border_style', $row),
                'input_color'        => post_or_db('input_color', $row),
                'input_border_width' => post_or_db('input_border_width', $row),
                'input_border_color' => post_or_db('input_border_color', $row),
                'input_border_style' => post_or_db('input_border_style', $row),
                'attendees_columns'  => post_or_db('attendees_columns', $row),
                'logo_path'          => post_or_db('logo_path', $row),
            		'background_img'     => post_or_db('background_img', $row),
            		'css_file'           => post_or_db('css_file', $row),
                'use_db_vars'				 =>  post_or_db('use_db_vars', $row)
            );
        }
    }

    function error_check()
    {
        return false;
    }

		function process_edit_template(){
			global $db, $in, $core, $user, $pcache, $tpl;
			
			if ($in->get('template_dd') != "" && $in->get('template_edit') != ""){				
					$admin_folder = (substr($in->get('template_dd'), 0, 6) == 'admin/') ? '/admin' : '';
					$filename = str_replace('admin/', '', $in->get('template_dd'));
					$storage_folder  = $pcache->FolderPath('templates/'.$this->style['style_code'].$admin_folder, 'eqdkp'); 
					$fp = fopen($storage_folder.$filename, "w");
					$result = fwrite($fp, html_entity_decode($in->get('template_edit', '', 'htmlescape')));
					fclose($fp); 

				if (!$result){
					$core->message( $user->lang['edit_template_nosuc'], $user->lang['save_nosuc'], 'red');
				} else {
					$core->message( $user->lang['edit_template_suc'], $user->lang['save_suc'], 'green');
					$tpl->delete_cache($this->style['template_path']);
				}
			}
			
			$this->display_form();
		}
		
		function process_enable(){
			global $db, $in, $user, $core;
			if (is_numeric($in->get('styleid'))){
				$db->query("UPDATE __styles SET enabled='1' WHERE style_id ='".$db->escape($in->get('styleid'))."'");
				$query = $db->query("SELECT style_name FROM __styles WHERE style_id ='".$db->escape($in->get('styleid'))."'");
				$data = $db->fetch_record($query);
				$core->message( sprintf($user->lang['enable_style_suc'], $data['style_name']), $user->lang['success'], 'green');
			}	
			
			$this->display_list();
		}
		
		function process_disable(){
			global $db, $in, $user, $core;
			if (is_numeric($in->get('styleid'))){
				$db->query("UPDATE __styles SET enabled='0' WHERE style_id ='".$db->escape($in->get('styleid'))."'");
				$query = $db->query("SELECT style_name FROM __styles WHERE style_id ='".$db->escape($in->get('styleid'))."'");
				$data = $db->fetch_record($query);
				$core->message( sprintf($user->lang['disable_style_suc'], $data['style_name']), $user->lang['success'], 'green');
			}	
			
			$this->display_list();
		}
		
		function process_default(){
			global $db, $in, $core, $user;
			if (is_numeric($in->get('standard_style'))){
				$core->config_set('default_style', $in->get('standard_style'));
				
				$query = $db->query("SELECT style_name FROM __styles WHERE style_id ='".$db->escape($in->get('standard_style'))."'");
				$data = $db->fetch_record($query);
				
				if ($in->get('override') == 1){
					$db->query("UPDATE __users SET user_style='".$in->get('standard_style')."'");
				}
				$core->message( sprintf($user->lang['default_style_suc'], $data['style_name']), $user->lang['success'], 'green');
			}
			
			$this->display_list();
		}
		
		function process_delete_cache(){
			global $core, $pcache, $user;
			
			$pcache->Delete($pcache->FolderPath('template', 'cache'));
			$core->message($user->lang['delete_template_cache_success'], $user->lang['success'], 'green');
			
			$this->display_list();
		}
		
		function process_install(){
			global $db, $in, $core, $eqdkp_root_path, $user, $game;
			if ($in->get('style') != ""){
				//Get the styles
				$stylename = $in->get('style');
				$style_query = $db->query("SELECT style_code FROM __styles");
				while ($row = $db->fetch_record($style_query)){
					$styles[] = $row['style_code'];
				}

				if (!in_array($in->get('style'), $styles)){
					$installer_file = $eqdkp_root_path."templates/".$in->get('style')."/package.xml";
					if (file_exists($installer_file)){
						
						//Get the install instructions
						$xml = simplexml_load_file($installer_file);
						if ($xml){

							$data = array(
								'style_name'	=> $xml->name,
								'style_code'	=> $stylename,
								'style_version'	=> $xml->version,
								'style_author'	=> $xml->author,
								'style_contact'	=> $xml->authorEmail,
								'template_path'	=>($xml->settings->template_path) ? $xml->settings->template_path : $stylename,
							);
							if (!in_array($data['style_name'], $styles)){
						
								$data_array = array();
								
								foreach($this->allowed_colors as $color){
									$data_array[$color] = $xml->settings->$color;
								}
								

								$data = array_merge($data, $data_array);
								$query = $db->build_query('INSERT', $data);
								$db->query('INSERT INTO __styles' . $query);
								$style_id = $db->insert_id();
								
								
								if (isset($xml->classcolors) && $style_id > 0){
									foreach($game->get('classes') as $class_id => $class_name){
										$xmlclass_id = 'cc_'.$class_id;
										$this->ClassColorManagement($style_id, $class_id, $xml->classcolors->$xmlclass_id);
									}
								}
													
								$core->message( sprintf($user->lang['install_style_suc'], $style_data[$stylename]['name']), $user->lang['success'], 'green');
			
							} else {
								$core->message( sprintf($user->lang['install_style_nosuc'], $in->get('style')), $user->lang['error'], 'red');
							}
						
						
						
						}
							
					} else {
						$db->query("INSERT INTO __styles :params", array(
							'style_name'	=> $in->get('style'),
							'style_code'	=> $in->get('style'),
							'template_path'	=> $in->get('style'),
						));
						
						$core->message( sprintf($user->lang['install_style_suc'], $in->get('style')), $user->lang['success'], 'green');
					}
				} else {
					$core->message( sprintf($user->lang['install_style_nosuc'], $in->get('style')), $user->lang['error'], 'red');
				}
				
			}
			
			$this->display_list();
		}
		
		function process_export(){
			global $in, $db, $eqdkp_root_path,$pcache, $core;
			if ($in->get('styleid') > 0){
				$query = $db->query("SELECT * FROM __styles WHERE style_id='".$db->escape($in->get('styleid'))."'");
				$data = $db->fetch_record($query);
				if ($data){
				
				//Create here the package.xml

					$fot ='<?xml version="1.0" encoding="utf-8"?>
					<install type="template" version="'.$core->config['plus_version'].'">
						<name>'.$data['style_name'].'</name>
						<author>'.$data['style_author'].'</author>
						<authorEmail>'.$data['style_contact'].'</authorEmail>
						<authorUrl>http://www.eqdkp-plus.com</authorUrl>
						<creationDate>19-DEC-2009</creationDate>
						<copyright>'.$data['style_author'].'</copyright>
						<license>CC</license>
						<version>'.$data['style_version'].'</version>
						<description></description>
					
					<settings>
						<template_path>'.$data['template_path'].'</template_path>';
						
					$style_code = ($data['style_code']) ? $data['style_code'] : $data['style_name'];
					unset($data['style_code']);
					unset($data['style_id']);
					unset($data['template_path']);
					unset($data['style_name']);
					unset($data['style_version']);
					unset($data['style_author']);
					unset($data['style_contact']);
					unset($data['enabled']);
					
					foreach ($data as $key=>$value){
						$fot .= "<$key>$value</$key>\n";
					}	
					$fot.='</settings>'."\n\n";
					
					$fot.='<classcolors>'."\n";
					
					$query = $db->query("SELECT class_id, color FROM __classcolors WHERE template='".$db->escape($in->get('styleid'))."'");
					if ($data){
						while ($row = $db->fetch_record($query)){
							$key = $row['class_id'];
							$value = $row['color'];
							$fot .= "<cc_$key>$value</cc_$key>\n";
						}
					}
					
					$fot.='</classcolors>'."\n\n";
					$fot.='</install>';

					$storage_folder  = $pcache->FolderPath('templates/'.$style_code, 'eqdkp');  	
					
					$fp = fopen($storage_folder.'package.xml', "w");
					fwrite($fp, $fot);
					fclose($fp);
		
					$file = $pcache->FolderPath('templates', 'eqdkp').$style_code.'_'.$this->style['style_version'].'.zip';
					$archive = new PclZip($file);
					$template_path = $eqdkp_root_path."templates/".$this->style['template_path']."/";
					
					//Create the archive
					$result = $archive->create($template_path, PCLZIP_OPT_REMOVE_PATH, $eqdkp_root_path."templates/");
					if ($style_code != $this->style['template_path']){
						$archive->add($eqdkp_root_path."templates/".$style_code."/", PCLZIP_OPT_REMOVE_PATH, $eqdkp_root_path."templates/");
					}
					$archive->delete(PCLZIP_OPT_BY_NAME, $style_code.'/package.xml');
					$archive->add($pcache->FolderPath('templates/'.$style_code, 'eqdkp'), PCLZIP_OPT_REMOVE_PATH, $pcache->FolderPath('templates', 'eqdkp'));
					if (file_exists($file)){
							header('Content-Type: application/octet-stream');
							header('Content-Length: '.filesize($file));
							header('Content-Disposition: attachment; filename="'.sanitize($style_code.'_'.$this->style['style_version'].'.zip').'"');
							header('Content-Transfer-Encoding: binary');
			
							readfile($file);
							die();
					}
					
					
				} else {
					$core->message($user->lang['error'], $user->lang['error'],'red');
				}
				
			}
			$this->display_list();
		}
		
    // ---------------------------------------------------------
    // Process Add
    // ---------------------------------------------------------
    function process_add()
    {
        global $db, $core, $user, $tpl, $pm;
        global $SID,$eqdkp_root_path, $game, $in;

				$style_code = preg_replace("/[^a-zA-Z0-9]/","",strtolower($in->get('style_name')));
				if ($in->get('style_name') == ""){
					$core->message($user->lang['style_name'], $user->lang['missing_values'], 'red');
					$this->display_form();			
				} else {
				
					$query = $db->query("SELECT * FROM __styles WHERE style_code ='".$db->escape($style_code)."' OR style_name='".$db->escape($style_name)."'");
					if ($db->affected_rows() > 0){
						$core->message($user->lang['create_style_nosuc'], $user->lang['error'], 'red');
						$this->display_form();
					} else {
	
						$query = $db->build_query('INSERT', array(
							'style_name'         => $in->get('style_name'),
							'style_code'					=> $in->get('style_code'),
							'style_version'				=> $in->get('style_version'),
							'style_author'				=> $in->get('style_author'),
							'style_contact'				=> $in->get('style_contact'),
											
							'template_path'      => $in->get('template_path'),
							'body_background'    => $in->get('body_background'),
							'body_link'          => $in->get('body_link'),
							'body_link_style'    => $in->get('body_link_style'),
							'body_hlink'         => $in->get('body_hlink'),
							'body_hlink_style'   => $in->get('body_hlink_style'),
							'header_link'        => $in->get('header_link'),
							'header_link_style'  => $in->get('header_link_style'),
							'header_hlink'       => $in->get('header_hlink'),
							'header_hlink_style' => $in->get('header_hlink_style'),
							'tr_color1'          => $in->get('tr_color1'),
							'tr_color2'          => $in->get('tr_color2'),
							'th_color1'          => $in->get('th_color1'),
							'fontface1'          => $in->get('fontface1'),
							'fontface2'          => $in->get('fontface2'),
							'fontface3'          => $in->get('fontface3'),
							'fontsize1'          => $in->get('fontsize1', NULL),
							'fontsize2'          => $in->get('fontsize2', NULL),
							'fontsize3'          => $in->get('fontsize3', NULL),
							'fontcolor1'         => $in->get('fontcolor1'),
							'fontcolor2'         => $in->get('fontcolor2'),
							'fontcolor3'         => $in->get('fontcolor3'),
							'fontcolor_neg'      => $in->get('fontcolor_neg'),
							'fontcolor_pos'      => $in->get('fontcolor_pos'),
							'table_border_width' => $in->get('table_border_width', NULL),
							'table_border_color' => $in->get('table_border_color'),
							'table_border_style' => $in->get('table_border_style'),
							'input_color'        => $in->get('input_color'),
							'input_border_width' => $in->get('input_border_width', NULL),
							'input_border_color' => $in->get('input_border_color'),
							'input_border_style' => $in->get('input_border_style'),
							
							'attendees_columns' => $in->get('attendees_columns'),
							'logo_path'         => $in->get('logo_path'),
							'background_img'    => $in->get('background_img'),
							'css_file'          => $in->get('css_file'),
							'use_db_vars'				=> $in->get('use_db_vars', 1),
							'enabled'						=> '1',
							));
						$result = $db->query('INSERT INTO __styles' . $query);
	
						// add the class colors to the array:
						$this->ClassColorDelete($style_id);
						foreach($game->get('classes') as $class_id => $class_name){
							$this->ClassColorManagement($style_id, $class_id, $_POST['classc_'.$class_id]);
						}
					
						$core->message($user->lang['admin_add_style_success'], $user->lang['success'], 'green');
						$this->display_list();
					}
				}
    }

    // ---------------------------------------------------------
    // Process Update
    // ---------------------------------------------------------
    function process_update()
    {
        global $db, $core, $user, $tpl, $pm;
        global $SID, $game;

        extract($_POST);

        // add the class colors to the database
        $this->ClassColorDelete($this->url_id);
        foreach($game->get('classes') as $class_id => $classname){
          $this->ClassColorManagement($this->url_id, $class_id, $_POST['classc_'.$class_id]);
        }

        $query = $db->build_query('UPDATE', array(
            'style_name'         => $style_name,
            'template_path'      => $template_path,
            'body_background'    => $body_background,
            'body_link'          => $body_link,
            'body_link_style'    => $body_link_style,
            'body_hlink'         => $body_hlink,
            'body_hlink_style'   => $body_hlink_style,
            'header_link'        => $header_link,
            'header_link_style'  => $header_link_style,
            'header_hlink'       => $header_hlink,
            'header_hlink_style' => $header_hlink_style,
            'tr_color1'          => $tr_color1,
            'tr_color2'          => $tr_color2,
            'th_color1'          => $th_color1,
            'fontface1'          => $fontface1,
            'fontface2'          => $fontface2,
            'fontface3'          => $fontface3,
            'fontsize1'          => $fontsize1,
            'fontsize2'          => $fontsize2,
            'fontsize3'          => $fontsize3,
            'fontcolor1'         => $fontcolor1,
            'fontcolor2'         => $fontcolor2,
            'fontcolor3'         => $fontcolor3,
            'fontcolor_neg'      => $fontcolor_neg,
            'fontcolor_pos'      => $fontcolor_pos,
            'table_border_width' => $table_border_width,
            'table_border_color' => $table_border_color,
            'table_border_style' => $table_border_style,
            'input_color'        => $input_color,
            'input_border_width' => $input_border_width,
            'input_border_color' => $input_border_color,
            'input_border_style' => $input_border_style,
						
						'attendees_columns' => $attendees_columns,
            'logo_path'         => $logo_path,
          	'background_img'    => $background_img,
           	'css_file'          => $css_file,
            'use_db_vars'				=> $use_db_vars
						
            ));
        $db->query('UPDATE __styles SET ' . $query . " WHERE style_id='" . $this->url_id . "'");

				$core->message( $user->lang['admin_update_style_success'], $user->lang['success'], 'green');
				$this->display_list();
    }

    // ---------------------------------------------------------
    // Process Delete (confirmed)
    // ---------------------------------------------------------
    function process_confirm(){
			global $db, $core, $user, $tpl, $pm, $SID, $user, $pcache;
			if ($this->url_id == $core->config['default_style']){
				$core->message( $user->lang['admin_delete_style_error_defaultstyle'], $user->lang['error'], 'red');
			}else{
				$db->query("DELETE FROM __styles WHERE style_id='" . $this->url_id . "'");
				$db->query("UPDATE __users SET user_style = '".$db->escape($core->config['default_style'])."' WHERE user_style = '".$db->escape($this->url_id)."'");
				$storage_folder = $pcache->FolderPath('templates/'.$this->style['template_path'], 'eqdkp'); 
				if (file_exists($storage_folder)){$pcache->Delete($storage_folder);}
				
				$core->message( $user->lang['admin_delete_style_success'], $user->lang['success'], 'green');
			}
			$this->display_list();
    }

		function process_version_update(){
			global $db, $core, $user, $tpl, $pm, $SID, $jquery, $eqdkp_root_path, $html, $pcache, $in, $game;
			$style_code = $in->get('style_code');
			if ($style_code != ""){
				$tpl_index = $core->root_path . 'templates/' . $style_code.'/package.xml';
				if (file_exists($tpl_index)){
						$xml = simplexml_load_file($tpl_index);
						if ($xml){
							//Templates
							if ($in->get('template', 0) == 1){					
								$storage_folder = $pcache->FolderPath('templates/'.$this->style['template_path'].'/admin', 'eqdkp'); 
								if (file_exists($storage_folder)){$pcache->Delete($storage_folder);}
								$storage_folder = $pcache->FolderPath('templates/'.$this->style['template_path'], 'eqdkp'); 
								if (file_exists($storage_folder)){$pcache->Delete($storage_folder);}
							}
						
							//Colors
							if ($in->get('colors', 0) == 0){
								if (isset($xml->version)){
									$db->query('UPDATE __styles SET style_version = "'.$db->escape($xml->version).'" WHERE style_code = "'.$db->escape($style_code).'"');
									$core->message( sprintf($user->lang['update_style_suc'], $style_code), $user->lang['success'], 'green');
								}
							} else {
								
								$data = array(
									'style_version'	=> $xml->version,
									'style_author'	=> $xml->author,
									'style_contact'	=> $xml->authorEmail,
									'template_path'	=>($xml->settings->template_path) ? $xml->settings->template_path : $stylename,
								);
							
									$data_array = array();
									foreach($this->allowed_colors as $color){
											$data_array[$color] = $xml->settings->$color;
									}
									$data = array_merge($data, $data_array);
									$query = $db->build_query('UPDATE', $data);
									$db->query('UPDATE __styles SET ' . $query.' WHERE style_code ="'.$db->escape($style_code).'"');
									
									$style_data = $db->query('SELECT style_id FROM __styles WHERE style_code ="'.$db->escape($style_code).'"');
									$style_data =	$db->fetch_record($style_data);
									$style_id = $style_data['style_id'];
									
									if (isset($xml->classcolors) && $style_id > 0){
										$this->ClassColorDelete($style_id);
										foreach($game->get('classes') as $class_id => $class_name){
											$xmlclassid = 'cc_'.$class_id;
											$this->ClassColorManagement($style_id, $class_id, $xml->classcolors->$xmlclassid);
										}
									}
									
									$core->message( sprintf($user->lang['update_style_suc'], $style_code), $user->lang['success'], 'green');
								
							
							}
						
						
						} //end if xml
			
				} //end if file exists
			
			} //end if style-code
			$this->display_list();
		} //close function
		
    // ---------------------------------------------------------
    // Display
    // ---------------------------------------------------------
    function display_list(){
			global $db, $core, $user, $tpl, $pm, $SID, $jquery, $eqdkp_root_path, $html, $pcache;
			
			$default_style = $core->config['default_style'];
			
			$sql = 'SELECT *, count(u.user_id) AS users
                FROM (__styles s
                LEFT JOIN __users u
                ON u.user_style = s.style_id)
                GROUP BY s.style_id
                ORDER BY s.enabled DESC, s.style_name';
			$result = $db->query($sql);
			while ( $row = $db->fetch_record($result) ){
				$templates[$row['style_code']] =  $row['style_code'];
				$template_array[] = $row;
			}

			
			$i = 0;
			//Get all styles that are not installed
			if ( $dir = @opendir($core->root_path . 'templates/') ){
				while ($file = @readdir($dir)){
					if ( (!is_file($core->root_path . 'templates/' . $file)) && (!is_link($core->root_path . 'templates/' . $file)) && valid_folder($file) && strtolower($file) != "maintenance" && strtolower($file) != "_tooltips"){
						$tpl_index = $core->root_path . 'templates/' . $file.'/package.xml';
						$available_templates[] = $file;
						//iterate through installed templates and check if the template path is installed
						if (!in_array($file, $templates)){
							if (file_exists($tpl_index)){
								//Get the install instructions
								$install_xml = simplexml_load_file($tpl_index);
							}
							$i ++;
							$tpl->assign_block_vars('install_row', array(
								'NAME'    	=> ($install_xml->name) ? $install_xml->name : stripslashes($file), 
								'VERSION'		=> $install_xml->version,
								'AUTHOR'		=> ($install_xml->authorEmail != "") ? '<a href="mailto:'.$install_xml->authorEmail.'">'.$install_xml->author.'</a>': $install_xml->author,
								'ROW_CLASS' => $core->switch_row_class(),
								'U_INSTALL'	=> 'styles.php' . $SID . '&amp;install=true&amp;style=' . $file,
							));
						}						
					}
				}
			}
			
			$updates = false;		
			foreach($template_array as $row){
				$templates[$row['style_code']] =  $row['style_code'];
				$screenshot = '';
				if (file_exists($eqdkp_root_path.'templates/'.$row['style_code'].'/screenshot.png' )){
					$screenshot = '<img src=\''.$eqdkp_root_path.'templates/'.$row['style_code'].'/screenshot.png\' style=\'max-width:300px;\'><br />';
				} elseif(file_exists($eqdkp_root_path.'templates/'.$row['style_code'].'/screenshot.jpg' )){
					$screenshot = '<img src=\''.$eqdkp_root_path.'templates/'.$row['style_code'].'/screenshot.jpg\' style=\'max-width:300px;\'><br />';
				}
				
				//The Update-Check
				if (file_exists($core->root_path . 'templates/' . $row['style_code'].'/package.xml')){
					$xml = simplexml_load_file($core->root_path . 'templates/' . $row['style_code'].'/package.xml');
					$return = compareVersion($xml->version, $row['style_version']);
					if ($return == 1){
						$updates = true;
						$files = array();
						
						//First: Show DB-Update
						
						//Second: Show conflicted templates

							if ( $dir = @opendir($pcache->FolderPath('templates/'.$row['style_code'], 'eqdkp')) ){
								while ($file = @readdir($dir)){
										$ext = pathinfo($file, PATHINFO_EXTENSION);
										if (!is_dir($file) && $file != "ads.html" && $file != "index.php" && in_array($ext, $this->allowed_extensions)){
											$files[$file] = $file;			
										}
								}
							}
							if ( $dir = @opendir($pcache->FolderPath('templates/'.$row['style_code'].'/admin', 'eqdkp')) ){
								while ($file = @readdir($dir)){
										$ext = pathinfo($file, PATHINFO_EXTENSION);
										if (!is_dir($file) && $file != "ads.html" && $file != "index.php" && in_array($ext, $this->allowed_extensions)){
											$files['admin/'.$file] = 'admin/'.$file;			
										}
								}
							}
							$output = '<form action="styles.php'.$SID.'" method="post">
  <h2>'.$row['style_name'].'</h2>
  '.$user->lang['style_update_selection1'].'
  <ul>
  <li>'.$user->lang['color_settings'].':</li>
  <ul>
  <label><input type="radio" name="colors" value="1" checked> '.$user->lang['style_update_selection2'].'</label><br>
  <label><input type="radio" name="colors" value="0" > '.$user->lang['style_update_selection3'].'</label>
	</ul>';
  if (count($files) > 0){
		$output .='<li>'.sprintf($user->lang['style_update_selection4'], count($files), '$(\'#files_'.$row['style_code'].'\').toggle();').'</li>
		<ul>
		<label><input type="radio" name="template" value="1" checked> '.$user->lang['style_update_selection5'].'</label><br>
		<label><input type="radio" name="template" value="0"> '.$user->lang['style_update_selection6'].'</label>
		</ul>
		<div id="files_'.$row['style_code'].'" style="display:none;"><li>'.$user->lang['style_update_selection7'].'</li><ul>';
		foreach ($files as $key=>$value){
			$output .= '<li>'.$key.'</li>';
		}
		$output .= '</ul></div>';
	}
	$output .= '</ul>
	<input type="hidden" name="style_code" value="'.$row['style_code'].'"> 
  <input type="submit" value="Update ausführen" class="mainoption bi_ok" name="version_update">
  </form>';
					
					$update_list[$row['style_name']] = $output;	

					}
					
				}

				$tpl->assign_block_vars('styles_row', array(
                'ROW_CLASS'    		=> $core->switch_row_class(),
								'ID'							=> $row['style_id'],
                'U_EDIT_STYLE' 		=> 'styles.php' . $SID . '&amp;edit=true&amp;styleid=' . $row['style_id'],
								'U_DELETE_STYLE' 	=> 'styles.php' . $SID . '&amp;delete=true&amp;styleid=' . $row['style_id'],
								'U_DOWNLOAD_STYLE' => 'styles.php' . $SID . '&amp;export=true&amp;styleid=' . $row['style_id'],
								'ENABLE_ICON'			=> ($row['enabled'] == '1') ? 'green' : 'red',
								'ENABLE_ICON_INFO' => ($row['enabled'] == '1') ? $user->lang['style_enabled_info'] : $user->lang['style_disabled_info'],
								'L_ENABLE'					=> ($row['enabled'] == '1') ? $user->lang['deactivate'] : $user->lang['activate'],
								'ENABLE'					=> ($row['enabled'] == '1') ? 'disable' : 'enable',
								'U_ENABLE'				=> ($row['enabled'] == '1') ? 'styles.php' . $SID . '&amp;disable=true&amp;styleid=' . $row['style_id'] : 'styles.php' . $SID . '&amp;enable=true&amp;styleid=' . $row['style_id'],
								'S_DEFAULT'				=> ($row['style_id'] == $default_style) ? true : false,
								'S_DEACTIVATED'		=> ($row['enabled'] != '1') ? true : false,
								'STANDARD'				=> ($row['style_id'] == $default_style) ? 'checked="checked"' : '',
								'VERSION'					=> $row['style_version'],
								'AUTHOR'					=> ($row['style_contact'] != "") ? '<a href="mailto:'.$row['style_contact'].'">'.$row['style_author'].'</a>': $row['style_author'],
                'NAME'         		=> $html->html_tooltip($screenshot, $row['style_name']),
								'CODE'						=> $row['style_code'],
                'TEMPLATE'     		=> ((!in_array($row['template_path'], $available_templates)) ? '<img src="'.$eqdkp_root_path.'images/error.png" title="'.$user->lang['template_not_exists_warning'].'" height="18"> ' : '').$row['template_path'],
                'USERS'        		=> $row['users'],
                'U_PREVIEW'    		=> 'styles.php' . $SID . '&amp;style=' . $row['style_id'])
				);
			}
			$db->free_result($result);
			

			$tpl->assign_vars(array(
						'S_NO_STYLES_TO_INSTALL' =>	($i == 0) ? true : false,
						'S_UPDATES'							=> $updates,
						'L_NO_STYLES_TO_INSTALL'	=> $user->lang['no_styles_to_install'],
            'L_NAME'     				=> $user->lang['name'],
            'L_TEMPLATE' 				=> $user->lang['template'],
            'L_USERS'    				=> $user->lang['users'],
            'L_PREVIEW'  				=> $user->lang['preview'],
            'L_MORE' 	 					=> $user->lang['more_template'],
						'L_INSTALL'					=> $user->lang['install'],
						'L_INSTALL_STYLES'	=> $user->lang['install_templates'],
						'L_ENABLE'					=> $user->lang['activate'],
						'L_UPDATES'					=> $user->lang['style_updates_available'],
						'L_UPDATE_INFO'			=> $user->lang['style_updates_info'],
						'L_MANAGE'					=> $user->lang['managing_styles'],
						'L_ACTION'					=> $user->lang['action'],
						'L_EDIT'						=> $user->lang['edit_style'],
						'L_DELETE'					=> $user->lang['delete_style'],
						'L_DOWNLOAD'				=> $user->lang['download_style'],
						'L_AUTHOR'					=> $user->lang['pi_author'],
						'L_VERSION'					=> $user->lang['pk_version'],
						'L_MAKE_DEFAULT'		=> $user->lang['make_default_style'],
						'L_RESET_CACHE'			=> $user->lang['delete_template_cache'],
						'L_STYLE_CODE'			=> $user->lang['style_code'],
						'JS_UPDATE_ACCORDION'=> $jquery->Accordion('updates', $update_list),
						'JS_DEFAULT_INFO'		=> $jquery->dialog('style_default_info', '', array('message' => $user->lang['style_default_info'].'<br /><br /><label><input type="radio" name="override" value="0" onChange="change_override(1);">'.$user->lang['yes'].'</label>  <label><input type="radio" name="override" value="1" checked="checked" onChange="change_override(0);">'.$user->lang['no'].'</label>', 'custom_js' => 'submit_form();'), 'confirm'),
						'JS_STYLE_TABS'		=> ($updates) ? $jquery->Tab_header('style_tabs') : '',
						'JS_SELECT_TABS'		=> ($updates) ? $jquery->Tab_Select('style_tabs', 1) : '',
            )
			);
			$core->set_vars(array(
            'page_title'    => $user->lang['styles_title'],
            'template_file' => 'admin/styles.html',
            'display'       => true)
			);
    }

    function display_form(){
			global $db, $core, $user, $tpl, $pm, $jquery, $SID, $game, $html, $in, $pcache;
			
			//get installed templates
				if ( $dir = @opendir($core->root_path . 'templates/') ){
				while ($file = @readdir($dir)){
					if ( (!is_file($core->root_path . 'templates/' . $file)) && (!is_link($core->root_path . 'templates/' . $file)) && valid_folder($file) && strtolower($file) != "maintenance" && strtolower($file) != "_tooltips"){
						$template_dropdown[$file] = $file;					
					}
				}
			}


			$text_decoration = array(
            'none' => 'none',
            'underline' => 'underline',
            'overline' => 'overline',
            'line-through' => 'line-through',
            'blink' => 'blink');
			$border_style = array(
            'none' => 'none',
            'hidden' => 'hidden',
            'dotted' => 'dotted',
            'dashed' => 'dashed',
            'solid' => 'solid',
            'double' => 'double',
            'groove' => 'groove',
            'ridge' => 'ridge',
            'inset' => 'inset',
            'outset' => 'outset');
			
			// Attendee columns
			for ($i = 1; $i < 11; $i++){
   			$attendee_colums[$i] = $i;
			}
				
        // Class Colors
        foreach($game->get('classes') as $class_id => $class_name){
	  			$tpl->assign_block_vars('classes', array(
						'NAME'     => $class_name,
						'CPICKER'  => $jquery->colorpicker('classc_'.$class_id, $game->get_class_color($class_id) ),
					));
        }
				
			//Get templates
			if ( $dir = @opendir($core->root_path . 'templates/'.$this->style['template_path']) ){
				$files[9] =  $user->lang['frontend'];
				while ($file = @readdir($dir)){
						$ext = pathinfo($file, PATHINFO_EXTENSION);
						if (!is_dir($file) && $file != "ads.html" && $file != "package.xml" && in_array($ext, $this->allowed_extensions)){
							$files[$file] = $file;			
						}
				}
			}
			//Get Admin-Templates
			if ( $dir = @opendir($core->root_path . 'templates/'.$this->style['template_path'].'/admin') ){
				$files[99] = ' ';
				$files[999] = $user->lang['backend'];
				while ($file = @readdir($dir)){
						$ext = pathinfo($file, PATHINFO_EXTENSION);
						if (!is_dir($file) && $file != "admin" && $file != "images" && $file != "ads.html" && in_array($ext, $this->allowed_extensions)){
							$files['admin/'.$file] = $file;			
						}
				}
			}
			
			//Read an spezific template-file to edit
			if ($in->get('template') != "" && !is_numeric($in->get('template') != "")){
				if (file_exists($pcache->FolderPath('templates/'.$this->style['style_code'], 'eqdkp').$in->get('template'))){
					$filename = $pcache->FolderPath('templates/'.$this->style['style_code'], 'eqdkp').$in->get('template');
				} elseif (file_exists($core->root_path . 'templates/'.$this->style['style_code'].'/'.$in->get('template'))){
					$filename = $core->root_path . 'templates/'.$this->style['style_code'].'/'.$in->get('template');
				} else {
					$filename = $core->root_path . 'templates/'.$this->style['template_path'].'/'.$in->get('template');
				}
				
				if (file_exists($filename)){
					$handle =  fopen($filename, "r");
					$contents = fread ($handle, filesize ($filename));
					fclose ($handle);
					$select_tab = 3;
				}
			}
				
        $tpl->assign_vars(array(
            // Form vars
            'F_ADD_STYLE' => 'styles.php' . $SID,
            'STYLE_ID'    => $this->url_id,
						'DD_EDIT_TEMPLTES'		=> $html->DropDown('template_dd', $files, $in->get('template'), '', 'onChange="this.form.template.value=this.value;this.form.action =\'styles.php'.$SID.'&amp;edit=true&amp;styleid=' . $this->url_id.'\'; this.form.submit();"', 'input'),
						'TEMPLATE_CONTENT'		=> $jquery->CodeEditor('template_edit', htmlentities($contents), 'html_js'),
						'S_USE_DBVARS'	=> ($this->style['use_db_vars']) ? true : false,
						

            // Form Values
            'STYLE_NAME'					=> $this->style['style_name'],
						'STYLE_CODE'					=> $this->style['style_code'],
						'STYLE_AUTHOR'				=> $this->style['style_author'],
						'STYLE_CONTACT'				=> $this->style['style_contact'],
						'STYLE_VERSION'				=> $this->style['style_version'],
            'FONTFACE1'						=> $this->style['fontface1'],
            'FONTFACE2'						=> $this->style['fontface2'],
            'FONTFACE3'						=> $this->style['fontface3'],
            'FONTSIZE1'						=> $this->style['fontsize1'],
            'FONTSIZE2'						=> $this->style['fontsize2'],
            'FONTSIZE3'						=> $this->style['fontsize3'],

            'TABLE_BORDER_WIDTH'	=> $this->style['table_border_width'],
            'TABLE_BORDER_STYLE'	=> $this->style['table_border_style'],
            'INPUT_BORDER_WIDTH'	=> $this->style['input_border_width'],
            'INPUT_BORDER_STYLE'	=> $this->style['input_border_style'],
            'STYLE_LOGO_PATH'			=> $this->style['logo_path'],
            'BACKGROUND_IMG'			=> $this->style['background_img'],
						'CSS_FILE'						=> $this->style['css_file'],
						'DD_LINK_STYLE'				=> $html->DropDown('body_link_style', $text_decoration, $this->style['body_link_style'], '', '', 'input'),
						'DD_HLINK_STYLE'			=> $html->DropDown('body_hlink_style', $text_decoration, $this->style['body_hlink_style'], '', '', 'input'),
						'DD_HEAD_LINK_STYLE'	=> $html->DropDown('header_link_style', $text_decoration, $this->style['header_link_style'], '', '', 'input'),
						'DD_HEAD_HLINK_STYLE'	=> $html->DropDown('header_hlink_style', $text_decoration, $this->style['header_hlink_style'], '', '', 'input'),
						'DD_TABLE_BORDERSTYLE'=> $html->DropDown('table_border_style', $border_style, $this->style['table_border_style'], '', '', 'input'),
						'DD_INPUT_BORDERSTYLE'=> $html->DropDown('input_border_style', $border_style, $this->style['input_border_style'], '', '', 'input'),
						'DD_ATTENDEE_COLUMNS'	=> $html->DropDown('attendees_columns', $attendee_colums, $this->style['attendees_columns'], '', '', 'input'),
						'DD_TEMPLATES'				=> $html->DropDown('template_path', $template_dropdown, $this->style['template_path'], '', '', 'input'),

						// Color pickers
						'CP_BODY_BG'					=> $jquery->colorpicker('body_background', $this->style['body_background']),
						'CP_FONTCOLOR1'				=> $jquery->colorpicker('fontcolor1', $this->style['fontcolor1']),
						'CP_FONTCOLOR2'				=> $jquery->colorpicker('fontcolor2', $this->style['fontcolor2']),
						'CP_FONTCOLOR3'				=> $jquery->colorpicker('fontcolor3', $this->style['fontcolor3']),
						'CP_FONTCOLOR_NEG'		=> $jquery->colorpicker('fontcolor_neg', $this->style['fontcolor_neg']),
						'CP_FONTCOLOR_POS'		=> $jquery->colorpicker('fontcolor_pos', $this->style['fontcolor_pos']),
						'CP_BODY_LINK'				=> $jquery->colorpicker('body_link', $this->style['body_link']),
						'CP_BODY_HLINK'				=> $jquery->colorpicker('body_hlink', $this->style['body_hlink']),
						'CP_HEADER_LINK'			=> $jquery->colorpicker('header_link', $this->style['header_link']),
						'CP_HEADER_HLINK'			=> $jquery->colorpicker('header_hlink', $this->style['header_hlink']),
						
						'CP_TR_COLOR1'				=> $jquery->colorpicker('tr_color1', $this->style['tr_color1']),
						'CP_TR_COLOR2'				=> $jquery->colorpicker('tr_color2', $this->style['tr_color2']),
						'CP_TH_COLOR1'				=> $jquery->colorpicker('th_color1', $this->style['th_color1']),
						'CP_TABLE_BORDER'			=> $jquery->colorpicker('table_border_color', $this->style['table_border_color']),
						
						'CP_INPUT_COLOR'			=> $jquery->colorpicker('input_color', $this->style['input_color']),
						'CP_INPUT_BORDER'			=> $jquery->colorpicker('input_border_color', $this->style['input_border_color']),
						
						// Language
            'L_STYLE_SETTINGS'         => $user->lang['style_settings'],
            'L_STYLE_NAME'             => $user->lang['style_name'],
						'L_STYLE_CODE'             => $user->lang['style_code'],
            'L_TEMPLATE'               => $user->lang['template_files'],
            'L_ELEMENT'                => $user->lang['element'],
            'L_VALUE'                  => $user->lang['value'],
            'L_BACKGROUND_COLOR'       => $user->lang['background_color'],
            'L_FONTFACE1'              => $user->lang['fontface1'],
            'L_FONTFACE1_NOTE'         => $user->lang['fontface1_note'],
            'L_FONTFACE2'              => $user->lang['fontface2'],
            'L_FONTFACE2_NOTE'         => $user->lang['fontface2_note'],
            'L_FONTFACE3'              => $user->lang['fontface3'],
            'L_FONTFACE3_NOTE'         => $user->lang['fontface3_note'],
            'L_FONTSIZE1'              => $user->lang['fontsize1'],
            'L_FONTSIZE1_NOTE'         => $user->lang['fontsize1_note'],
            'L_FONTSIZE2'              => $user->lang['fontsize2'],
            'L_FONTSIZE2_NOTE'         => $user->lang['fontsize2_note'],
            'L_FONTSIZE3'              => $user->lang['fontsize3'],
            'L_FONTSIZE3_NOTE'         => $user->lang['fontsize3_note'],
            'L_FONTCOLOR1'             => $user->lang['fontcolor1'],
            'L_FONTCOLOR1_NOTE'        => $user->lang['fontcolor1_note'],
            'L_FONTCOLOR2'             => $user->lang['fontcolor2'],
            'L_FONTCOLOR2_NOTE'        => $user->lang['fontcolor2_note'],
            'L_FONTCOLOR3'             => $user->lang['fontcolor3'],
            'L_FONTCOLOR3_NOTE'        => $user->lang['fontcolor3_note'],
            'L_FONTCOLOR_NEG'          => $user->lang['fontcolor_neg'],
            'L_FONTCOLOR_NEG_NOTE'     => $user->lang['fontcolor_neg_note'],
            'L_FONTCOLOR_POS'          => $user->lang['fontcolor_pos'],
            'L_FONTCOLOR_POS_NOTE'     => $user->lang['fontcolor_pos_note'],
            'L_BODY_LINK'              => $user->lang['body_link'],
            'L_BODY_LINK_STYLE'        => $user->lang['body_link_style'],
            'L_BODY_HLINK'             => $user->lang['body_hlink'],
            'L_BODY_HLINK_STYLE'       => $user->lang['body_hlink_style'],
            'L_HEADER_LINK'            => $user->lang['header_link'],
            'L_HEADER_LINK_STYLE'      => $user->lang['header_link_style'],
            'L_HEADER_HLINK'           => $user->lang['header_hlink'],
            'L_HEADER_HLINK_STYLE'     => $user->lang['header_hlink_style'],
            'L_TR_COLOR1'              => $user->lang['tr_color1'],
            'L_TR_COLOR2'              => $user->lang['tr_color2'],
            'L_TH_COLOR1'              => $user->lang['th_color1'],
            'L_TABLE_BORDER_WIDTH'     => $user->lang['table_border_width'],
            'L_TABLE_BORDER_COLOR'     => $user->lang['table_border_color'],
            'L_TABLE_BORDER_STYLE'     => $user->lang['table_border_style'],
            'L_INPUT_COLOR'            => $user->lang['input_color'],
            'L_INPUT_BORDER_WIDTH'     => $user->lang['input_border_width'],
            'L_INPUT_BORDER_COLOR'     => $user->lang['input_border_color'],
            'L_INPUT_BORDER_STYLE'     => $user->lang['input_border_style'],
            'L_STYLE_CONFIGURATION'    => $user->lang['style_configuration'],
            'L_STYLE_DATE_NOTE'        => $user->lang['style_date_note'],
            'L_ATTENDEES_COLUMNS'      => $user->lang['attendees_columns'],
            'L_ATTENDEES_COLUMNS_NOTE' => $user->lang['attendees_columns_note'],
            'L_LOGO_PATH'              => $user->lang['logo_path'],
            'L_LOGO_PATH_NOTE'         => $user->lang['logo_path_note'],
            'L_ADD_STYLE'              => $user->lang['add_style'],
            'L_RESET'                  => $user->lang['reset'],
            'L_UPDATE_STYLE'           => $user->lang['update_style'],
            'L_DELETE_STYLE'           => $user->lang['delete_style'],
            'L_CLASS_COLORS'           => $user->lang['class_colors'],
            'L_MORE' 				   				 => $user->lang['more_template'],
            'L_BACKGROUND_IMG' 		   	 => $user->lang['background_image'],
            'L_CSS_FILE' 			    		 => $user->lang['css_file'],
						'L_COLORS'								=> $user->lang['color_settings'],
						'L_TEMPLATES'							=> $user->lang['edit_templates'],
						'L_CANCEL'								=> $user->lang['cancel'],
						'L_TEMPLATE_WARNING'			=> sprintf($user->lang['template_warning'], $pcache->FileLink('templates', 'eqdkp').'/'.$this->style['style_code']),
						'L_SELECT_TEMPLATE'				=> $user->lang['select_template'],
						'L_SAVE'									=> $user->lang['save'],
						'L_AUTHOR'								=> $user->lang['pi_author'],
						'L_VERSION'								=> $user->lang['pk_version'],
						'L_CONTACT'								=> $user->lang['contact'],
						'JS_STYLE_TABS'						=> $jquery->Tab_header('style_tabs'),
						'JS_TAB_SELECT'						=> ($select_tab > 0) ? $jquery->Tab_Select('style_tabs', $select_tab) : '',

            // Buttons
            'S_ADD' 									 => ( !$this->url_id ) ? true : false)
        );
        $core->set_vars(array(
            'page_title'    => $user->lang['styles_title'],
            'template_file' => 'admin/styles_add.html',
            'display'       => true)
        );
    }

    function ClassColorDelete($template){
      global $db;
      $db->query("DELETE FROM __classcolors WHERE template='".$template."'");
    }

    function ClassColorManagement($template, $id, $color){
      global $db;
      $query = $db->build_query('INSERT', array(
            'template'    => $template,
            'class_id'    => $id,
            'color'       => $color,
            ));
        $db->query('INSERT INTO __classcolors' . $query);
    }
}

$manage_styles = new Manage_Styles;
$manage_styles->process();
?>