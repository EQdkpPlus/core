<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		    http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2007
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2007-2008 sz3
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev$
 * 
 * $Id$
 */

// EQdkp required files/vars
define('EQDKP_INC', true);
define('IN_ADMIN', true);

$eqdkp_root_path = './../';
include_once ($eqdkp_root_path . 'common.php');

class ManagePageLayouts extends EQdkp_Admin{
	public function __construct(){
		global $core;
		parent::eqdkp_admin();
		$this->assoc_params(array(
			'edit' => array(
				'name'		=> 'edit',
				'process'	=> 'edit_layout',
				'check'		=> 'a_config_man'
      ),
      'delete' => array(
				'name'		=> 'delete',
				'process'	=> 'delete_layout',
				'check'		=> 'a_config_man'
      ),
			'del_pre' => array(
				'name'		=> 'del_pre',
				'process'	=> 'delete_preset',
				'check'		=> 'a_config_man'
      ),
			
		));

		$this->assoc_buttons(array(
			'add_layout' => array(
				'name'		=> 'add_layout',
				'process'	=> 'add_layout',
				'check'		=> 'a_config_man'
      ),
			'add_preset' => array(
				'name'		=> 'add_preset',
				'process'	=> 'add_preset',
				'check'		=> 'a_config_man'
      ),
			'save' => array(
				'name'		=> 'save',
				'process'	=> 'save_layout',
				'check'		=> 'a_config_man'
      ),
      'set_current_layout' => array(
				'name'		=> 'set_current_layout',
				'process'	=> 'set_current_layout',
				'check'		=> 'a_config_man'
      ),
      'form' => array(
          'name'      => '',
          'process'   => 'display_list',
          'check'     => 'a_config_man'
      )
		));
	}
	
	function save_layout(){
		global $pdh, $in, $pcache, $user, $core, $pdc;

    $layout_name = $in->get('filename', '');

    if(!$pdh->user_layout_exists($layout_name))
      message_die("User layout does not exist.");

    $layout_def = $pdh->get_eqdkp_layout($layout_name);
    $layout_def['data']['description'] = addslashes($in->get('description', 'No description given.'));
		$page_list = $pdh->get_page_list();

    //options
    $options = $in->getArray('params', 'string');
    foreach($options as $key => $value){
      $layout_def['options'][$key]['value'] = $value;
    }
    
    //substitutions
    $subs = $in->getArray('subs', 'string');
    foreach($subs as $key => $value){
      $layout_def['substitutions'][$key]['value'] = $value;
    }

    //leaderboard
    $layout_def['pages']['listmembers']['listmembers_leaderboard']['maxperclass'] = $in->get('lb_maxperclass', 5);
    $layout_def['pages']['listmembers']['listmembers_leaderboard']['maxperrow'] = $in->get('lb_maxperrow', 5);
    $layout_def['pages']['listmembers']['listmembers_leaderboard']['sort_direction'] = $in->get('lb_sortdir', 'asc');
    $layout_def['pages']['listmembers']['listmembers_leaderboard']['classes'] = $in->getArray('lb_classes', 'int', array_keys($pdh->get('game', 'array', array('classes'))));
    
    foreach($page_list as $page){
      $page_arr = array();
      $page_arr = $in->getArray($page, 'string', 5);

      if(empty($page_arr))
        message_die("missing page!");

      foreach($layout_def['pages'][$page] as $page_object => $options){
        if(substr($page_object,0,4) == 'hptt'){
          $layout_def['pages'][$page][$page_object]['show_numbers'] = ($page_arr[$page_object]['numbers']) ? true : false;
          $layout_def['pages'][$page][$page_object]['table_sort_dir'] = $page_arr[$page_object]['table_sort_dir'];
          $layout_def['pages'][$page][$page_object]['table_sort_col'] = 0;
          $layout_def['pages'][$page][$page_object]['table_presets'] = array();
          $def_sort_column_id = 0;
          foreach($page_arr[$page_object]['td_add'] as $preset => $empty){
            $td_add = isset($page_arr[$page_object]['td_add'][$preset]) ? stripslashes($page_arr[$page_object]['td_add'][$preset]) : "";
            $th_add = isset($page_arr[$page_object]['th_add'][$preset]) ? stripslashes($page_arr[$page_object]['th_add'][$preset]) : "";
            $sortable = isset($page_arr[$page_object]['sortable'][$preset]) ? true : false;
            $layout_def['pages'][$page][$page_object]['table_presets'][] = array('name' => $preset, 'sort' => $sortable, 'th_add' => $th_add, 'td_add' => $td_add);
            if($preset == $page_arr[$page_object]['default_sort']){
              $layout_def['pages'][$page][$page_object]['table_sort_col'] = $def_sort_column_id;
            }
            $def_sort_column_id++;
          }
        }
      }
    }

    $pdh->save_layout($layout_name, $layout_def);
    //did we change the current layout? => flush cache
    if($filename == $core->config['eqdkp_layout']){
      $pdc->flush();
      $pdh->init_eqdkp_layout($layout_name);
    }

		$messages[] = array('title' => $user->lang['save_suc'], 'text' => $user->lang['lm_save_suc'], 'color' => 'green');
		$this->display_list($messages);
  }
  
	function add_preset(){
		global $pdh, $in, $pcache, $user, $core, $pdc, $SID, $xmltools;
    $new_preset_xml = unsanitize($_POST['new_preset_xml']);
		if ($new_preset_xml != ""){
      $newxml = new SimpleXMLElement($new_preset_xml);
      $arr = $xmltools->simplexml2array($newxml, true);
      $changed = false;
      $user_presets = $pdh->get_user_presets();
      $user_presets_lang = $pdh->get_user_preset_lang();
      
      foreach($arr as $preset_name => $options){
        $ps[0] = $options['module'];
        $ps[1] = $options['tag'];
        $cpars = array();
        if(is_array($options['cpar'])){
          foreach($options['cpar'] as $parameter){
            $cpars[] = $parameter;
          }
        }
        $dpars = array();
        if(is_array($options['dpar'])){
          foreach($options['dpar'] as $parameter){
            $dpars[] = $parameter;
          }
        }
        $ps[2] = $cpars;
        $ps[3] = $dpars;
        $user_presets[$preset_name] = $ps;
        
        $user_presets_lang[$preset_name] = $options['lang'];
        $changed = true;
      }
		  
      if($changed){
  			$pdh->save_user_presets($user_presets, $user_presets_lang);
  			$pdc->flush();
  			//redirect('admin/manage_pagelayouts.php'.$SID);
      }
		}
		$this->display_list(false, '1');
	}
	
	function delete_preset(){
		global $pdh, $in, $pcache, $user, $core, $pdc;
    $delete_preset = $in->get('del_pre', '');
    if($delete_preset != ''){
      $user_presets = $pdh->get_user_presets();
      $user_presets_lang = $pdh->get_user_preset_lang();
      
      if(array_key_exists($delete_preset, $user_presets)){
        unset($user_presets[$delete_preset]);
        unset($user_presets_lang[$delete_preset]);
        $pdh->save_user_presets($user_presets, $user_presets_lang);
        $pdc->flush();
      }
    }
		$this->display_list(false, '1');
	}
	
	
  function set_current_layout(){
  global $core, $pdh, $in, $pdc, $user;

    $new_layout = $in->get('current_layout', '');
    $layouts = $pdh->get_layout_list(true, true);
    $current_layout = $core->config['eqdkp_layout'];
    
    if(in_array($new_layout, $layouts) && ($new_layout != $current_layout) ){
      $core->config_set('eqdkp_layout', $new_layout);
      $pdc->flush();
    }
		$messages[] = array('title' => $user->lang['save_suc'], 'text' =>  $user->lang['save_suc'], 'color' => 'green');
    // Check if the layout has changed
    $this->display_list($messages);  
  }
  
  function delete_layout(){
  global $pdh, $in, $user, $pcache, $core;
    if($in->get('layout') != ''){
      $layout = $in->get('layout', '');
      $user_layouts = $pdh->get_layout_list(false, true);  
			$current_layout = $core->config['eqdkp_layout'];
			
      if(in_array($layout, $user_layouts) && $layout != $current_layout){
				$storage_folder  = $pcache->FolderPath('layouts', 'eqdkp');
				if (file_exists($storage_folder.$layout.'.esys.php')){
					$pcache->Delete($storage_folder.$layout.'.esys.php');
				}
        $messages[] = array('title' => $user->lang['del_suc'], 'text' => $user->lang['lm_del_suc'], 'color' => 'green');
      }else{
        $messages[] = array('title' => $user->lang['del_no_suc'], 'text' => $user->lang['lm_del_error'], 'color' => 'red');      
      }
    }
   	$this->display_list($messages);  
  }
  
  function add_layout(){
    global $pdh, $in, $user;

    if($in->get('new_layout_name', '') != ""){
      $layout = $in->get('new_layout_name', '');
      $layouts = $pdh->get_layout_list(true, true);
      
      if($pdh->layout_exists($layout)){
        $messages[] = array('title' => $user->lang['add_no_suc'], 'text' => $user->lang['lm_layout_exists'], 'color' => 'red');
				$this->display_list($messages);
      }else{
        //Create new layout
        $source_layout = $in->get('new_layout_source');
        $layout_desc = $in->get('new_layout_desc');
        $sl = $pdh->get_eqdkp_layout($source_layout);
        $sl['data']['description'] = $layout_desc;
        $pdh->save_layout($layout, $sl);
				$this->edit_layout(false, $layout);
      } 
    } else {
    	$this->display_list($messages);
		}
  }
  
  function display_list($message = false, $tab = '0'){
  global $core, $pdh, $tpl, $user, $jquery, $eqdkp_root_path, $pcache, $html;
  	if($message){
  		$core->messages($message);
  	}
    
    $current_layout = $core->config['eqdkp_layout'];
		
    foreach($pdh->get_layout_list(true, false) as $layout){

      $tpl->assign_block_vars('layouts_row', array(
          'NAME'     => $layout,
          'DESC'    => $pdh->get_eqdkp_layout_description($layout),
          'IS_CURRENT' => ($layout == $current_layout) ? 'checked="checked"' : '',
          'ROW_CLASS' => $core->switch_row_class(),
        )
      );
    }
    
    foreach($pdh->get_layout_list(false, true) as $layout){
      $tpl->assign_block_vars('user_layouts_row', array(
          'NAME'     => $layout,
          'DESC'    => $pdh->get_eqdkp_layout_description($layout),
          'IS_CURRENT' => ($layout == $current_layout) ? 'checked="checked"' : '',
          'ROW_CLASS' => $core->switch_row_class(),
        )
      );
    }
    
    foreach($pdh->get_layout_list(true, true) as $layout) {
			$layout_options[$layout] = $layout;
    }
		
		$user_presets = $pdh->get_user_presets();
		if (is_array($user_presets)){
			foreach($user_presets as $key=>$value){
				$tpl->assign_block_vars('preset_row', array (
					'NAME' => $key,
					'MODULE' => $value[0],
					'TAG' => $value[1],
					'APARAM'	=> implode(', ', $value[2]),
					'DPARAM'	=> implode(', ', $value[3]),
					'ROW_CLASS'	=> $core->switch_row_class(),
				));
			}
		}
    
		
    $tpl->assign_vars(array (
  	  'F_PAGE_MANAGER'       => 'manage_pagelayouts.php' . $SID,
			'LAYOUT_DROPDOWN'				=> $html->DropDown('new_layout_source', $layout_options, $core->config['eqdkp_layout']),
			
  	  'L_SET_CURRENT_LAYOUT' => $user->lang['lm_set_current_layout'],
      'L_ADD_LAYOUT'         => $user->lang['lm_add_layout'],
			'L_LM_TITLE'					=> $user->lang['lm_title'],
			'L_DEFAULT_LAYOUTS'		=> $user->lang['lm_default_layouts'],
			'L_USER_LAYOUTS'		=> $user->lang['lm_user_layouts'],
			'L_NAME'	=> $user->lang['name'],
			'L_LM_INFO'	=> $user->lang['lm_info'],
			'L_DESC'	=> $user->lang['description'],
			'L_ACTION'	=> $user->lang['action'],
			'L_EDIT'	=> $user->lang['edit'],
			'L_DELETE'	=> $user->lang['delete'],
			'L_SET_CURRENT_LAYOUT'	=> $user->lang['lm_make_current'],
			'L_ADD_NEW_LAYOUT'	=> $user->lang['lm_new_layout'],
			'L_SOURCE_LAYOUT'	=> $user->lang['lm_source_layout'],
			'L_ADD_LAYOUT' => $user->lang['lm_create_layout'],
			'L_MANAGE'		=>$user->lang['lm_manage_layouts'],
			'L_ADVANCED'	=> $user->lang['lm_manage_advanced'],
			'JS_LM_TABS'	=> $jquery->Tab_header('lm_tabs'),
			'L_LM_WARNING'	=> $user->lang['lm_warning'],
			'L_USER_PRESETS'	=> $user->lang['lm_user_presets'],
			'L_ADD_NEW_PRESET'	=> $user->lang['lm_add_preset'],
      'L_MODULE'	=> $user->lang['lm_module'],
			'L_TAG'	=> $user->lang['lm_tag'],
			'L_DPARAM'	=> $user->lang['lm_dparam'],
      'L_APARAM'	=> $user->lang['lm_aparam'],
      'L_PRESET_XML'	=> $user->lang['lm_up_xml'],

    ));
		
    $jquery->Tab_Select('lm_tabs', $tab);	
    
		$core->set_vars(array (
      	'page_title'    => $user->lang['lm_title'],
      	'template_file' => 'admin/manage_pagelayouts.html',
        'display'       => true
    	)
    );
  }
  
  function edit_layout($messages = false, $name = false){
  global $core, $tpl, $pdh, $jquery, $html, $user, $in, $game;
    // The JavaScript Magic...
    $tpl->add_js('$(document).ready(function(){
    	// Return a helper with preserved width of cells
    	var fixHelper = function(e, ui) {
    		ui.children().each(function() {
    			$(this).width($(this).width());
    		});
    		return ui;
    	};
    
    	$(".sortingtable tbody").sortable({
    		helper: fixHelper,
    		cancel: \'.not-sortable, .th_add, .td_add\',
    		update: function(){
    							$(".sortingtable tr:odd").removeClass();
    							$(".sortingtable tr:odd").addClass("row1");
      						$(".sortingtable tr:even").addClass("row2");
    						}
    	}).disableSelection();
        	
    	// Delete the row
    	$(".delRow").btnDelRow(function(roww){
    		var mydelid		= jQuery.trim($(".delete_id", roww).text());
    		var mypreset	= jQuery.trim($(".presetname", roww).text());
    		$("#dp"+mydelid).removeAttr("disabled");
    		$("#dp"+mydelid).append("<option value=\'"+mypreset+"\'>"+mypreset+"</option>");
    	});	
    
    	$(".alternativeRow").btnAddRow({oddRowCSS:"row1",evenRowCSS:"row2"}, function(row){
    			if(row){
    				var prefix		= jQuery.trim($(".prefix_id", row).text());
    				var id				= jQuery.trim($(".delete_id", row).text());
    				var selected	= jQuery.trim($("#dp"+id).val());
						var name			= jQuery.trim($("#dp"+id+ " :selected").text());

    				// The name of the field
    				$(".presetname", row).empty();
    				$(".presetname", row).append(name);
    					
    				// The value fields
    				$(".sortable" ,row).attr("name", prefix+"[sortable]["+selected+"]");
    				$(".default_sort" ,row).attr({"value": selected});
    				$(".td_add" ,row).attr("name", prefix+"[td_add]["+selected+"]");
    				$(".th_add" ,row).attr("name", prefix+"[th_add]["+selected+"]");
    				
    				// Remove the option in select
    				$("#dp"+id+" option[value="+selected+"]").remove();
    				
    				// Disable if no selection available
    				if($("#dp"+id+" option").length == 0){
    					$("#dp"+id).attr("disabled","disabled");
    					$("#button_"+id).attr("disabled","disabled");
    				}
    			};
    		});
    	
    	$(".sortingtable tr:odd").addClass("row1");
      $(".sortingtable tr:even").addClass("row2");
    });
    ');
    
		$layout_name = ($name) ? $name : $in->get('layout');

    if(!$pdh->user_layout_exists($layout_name))
      message_die("User layout does not exist.");
		$pdh->auto_update_layout($layout_name);
		
    $layout_def = $pdh->get_eqdkp_layout($layout_name);

    //Tabs
    $jquery->Tab_header('plus_pm_pages_tabs');
    //Get all defined pages from current system file                
    $pages = $pdh->get_page_list();
		
    $page_id = 1;
    $page_object_id = 1;
    
    $page_tabs = '';

		$table_sort_dirs['asc'] = $user->lang['lm_sort_asc'];
		$table_sort_dirs['desc'] = $user->lang['lm_sort_desc'];
		
    //general options
    if (is_array($layout_def['options']) && !empty($layout_def['options'])){
			foreach ($layout_def['options'] as $key=>$value){
				$value['name'] = 'params['.$value['name'].']';
				$tpl->assign_block_vars('param_row', array(
					'NAME'	=> $value['lang'],
					'FIELD'	=> $html->widget($value),
				));
			}
		}

    //substitutions
    if (is_array($layout_def['substitutions']) && !empty($layout_def['substitutions'])){
			foreach ($layout_def['substitutions'] as $key=>$value){
				$value['name'] = 'subs['.$value['name'].']';
				$tpl->assign_block_vars('subs_row', array(
					'NAME'	=> $value['lang'],
					'FIELD'	=> $html->widget($value),
				));
			}
		}
		
    //iterate through all pages
    foreach($pages as $page){
      $page_tabs .= "<li><a href='#page-".$page_id."'><span>".$user->lang['lm_page_'.$page]."</span></a></li>";
      
      //get page settings
      $page_settings = $layout_def['pages'][$page];
      
      $tpl->assign_block_vars('page_row', array(
          'ID' => $page_id,
					'S_LEADERBORD'	=> ($page == 'listmembers') ? true : false,
        )
      );
      
			//Leaderbord-Settings
			if ($page == 'listmembers'){
				$tpl->assign_vars(array(
					'LB_MAXPERCLASS'	=> '<input type="text" size="4" name="lb_maxperclass" value="'.$page_settings['listmembers_leaderboard']['maxperclass'].'" class="input">',
					'LB_MAXPERROW'	=> '<input type="text" size="4" name="lb_maxperrow" value="'.$page_settings['listmembers_leaderboard']['maxperrow'].'" class="input">',
					'LB_SORTDIR'	=> $html->DropDown(lb_sortdir, $table_sort_dirs, $page_settings['listmembers_leaderboard']['sort_direction'], '', '', 'input', ''),
				));
				foreach($page_settings['listmembers_leaderboard']['classes'] as $class){
					$tpl->assign_block_vars('page_row.class_row', array(
						'HIDDEN'		=> '<input type="hidden" name="lb_classes['.$class.']" value="'.$class.'">',
						'NAME'	=>	$game->decorate('classes', array($class)).' '.$game->get_name('classes', $class), 
					));
				} 
			}
			
      //iterate through defined objects
      foreach($page_settings as $page_object => $options){
        //for now only show html pdh tag tables (hptt)
        if(substr($page_object,0,4) == 'hptt'){     
          $potential_presets = array_keys($pdh->get_preset_list($options['table_main_sub'], $options['table_subs'], array_keys($layout_def['subs'])));
          $pps = array();
          foreach($potential_presets as $id => $pset){
            $pps[$pset] = ($pdh->get_preset_description($pset)) ? $pdh->get_preset_description($pset) : $pset; 
          }
          
          foreach($options['table_presets'] as $column_id => $column_options){
            $preset = $column_options['name'];       
            unset($pps[$preset]);
          }
					
					if (count($pps) == 0){
						$tpl->add_js('$(document).ready(function(){ $("#dp'.$page_object_id.'").attr("disabled","disabled"); $("#button_'.$page_object_id.'").attr("disabled","disabled"); })');
					}
					
          $tpl->assign_block_vars('page_row.page_object_row', array(
              'TABLE_TITLE' => $user->lang['lm_'.$page_object],
              'ID' => $page_object_id,
              'NAME' => $page_object,
              'DROPDOWN' => $html->DropDown('dp'.$page_object_id, $pps, '', '', '', 'input', ''),
              'PREFIX' => $page.'['.$page_object.']',
							'NUMBERS'	=> $html->CheckBox($page.'['.$page_object.'][numbers]', '', $options['show_numbers']),
							'TABLE_SORT_DIR'	=> $html->DropDown($page.'['.$page_object.'][table_sort_dir]', $table_sort_dirs, $options['table_sort_dir'], '', '', 'input', ''),
            )
          );
          
          foreach($options['table_presets'] as $column_id => $column_options){
            $preset = $column_options['name'];       
    
            $tpl->assign_block_vars('page_row.page_object_row.preset_row', array(
                'NAME'     => ($pdh->get_preset_description($preset)) ? $pdh->get_preset_description($preset) : $preset,
                'SORTABLE' => $html->CheckBox($page.'['.$page_object.'][sortable]['.$preset.']', '', $column_options['sort'], '1', true, 'sortable'),
                'CODE'     => $preset,
                'DEFAULT_SORT'  => ($options['table_sort_col'] == $column_id) ? 'checked="checked"' : '',
                'TH_ADD'   => sanitize($column_options['th_add']),
                'TD_ADD'   => sanitize($column_options['td_add']),
                'ID' => $page_object_id,
              )
            );
          }
          
          $page_object_id++;
        }
      }
      $page_id++;
    }
    
    $tpl->assign_vars(array (
      	'F_PAGE_MANAGER'       => 'manage_pagelayouts.php' . $SID,
        'PAGE_TAB_LIST'        => $page_tabs,
        'L_SYSTEM_DESCRIPTION' => $user->lang['lm_system_description'],
        'L_SAVE'               => $user->lang['save'],
        'L_LM_PRESETNAME'      => $user->lang['lm_column_preset'],
        'L_LM_SORTABLE'        => $user->lang['lm_column_sortable'],
        'L_LM_DEFAULT_SORT'    => $user->lang['lm_column_default_sort'],
        'L_LM_TH_ADD'          => $user->lang['lm_column_th_add'],
        'L_LM_TD_ADD'          => $user->lang['lm_column_td_add'],
        'L_ADD_ROW'            => $user->lang['lm_add_row'],
				'L_DELETE_ROW'         => $user->lang['lm_delete_row'],
        'L_TABLE_SETTINGS'     => $user->lang['lm_table_settings'],
        'L_TABLE_COLUMNS'      => $user->lang['lm_table_columns'],
        'L_LM_TITLE'           => $user->lang['lm_title'],
				'L_CANCEL'           	=> $user->lang['cancel'],
				'L_NUMBERS'           => $user->lang['lm_show_numbers'],
				'L_SORT_DIR'           => $user->lang['lm_sort_direction'],
				'L_DRAGNDROP'					=> $user->lang['dragndrop'],
				'L_LEADERBORD_SETTINGS'	=> $user->lang['lm_leaderbord_settings'],
				'L_MAX_PER_CLASS'			=> $user->lang['lm_lb_maxperclass'],
				'L_MAX_PER_ROW'				=> $user->lang['lm_lb_maxperrow'],
				'L_CLASS_SORT'				=> $user->lang['lm_lb_class_sort'],
				'L_DESCRIPTION'				=> $user->lang['description'],
				
				'FILENAME'						=> $layout_name,
				'DESCRIPTION'					=> $pdh->get_eqdkp_layout_description($layout_name),
      )
    ); 
    
    $core->set_vars(array (
      	'page_title'    => $user->lang['lm_title'],
      	'template_file' => 'admin/manage_pagelayouts_edit.html',
        'display'       => true
    	)
    );
  }
}

$manpagelayouts = new ManagePageLayouts();
$manpagelayouts->process();
?>
