<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		    http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2009
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2009 GodMod
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev$
 * 
 * $Id$
 */

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class Manage_Menus extends EQdkp_Admin
{
    
    function Manage_Menus()
    {
        global $db, $core, $user, $tpl, $pm, $in;
        global $SID;

        parent::eqdkp_admin();
 
        $this->assoc_buttons(array(
            'save' => array(
                'name'    => 'save',
                'process' => 'process_save',
                'check'   => 'a_config_man'),
			
            'form' => array(
                'name'    => '',
                'process' => 'display_form',
                'check'   => 'a_config_man'))
        );
				$this->assoc_params(array(
            'del_plink' => array(
                'name'    => 'mode',
                'process' => 'process_delete_plink',
								'value'		=> 'del_plink',
                'check'   => 'a_config_man'),		
        ));
       
    }


    // ---------------------------------------------------------
    // Process Save
    // ---------------------------------------------------------
    function process_save()
    {
        global $db, $core, $user, $tpl, $pm, $in, $plus, $pdh;
        global $SID;
				//Menu1
				$sort_ary = $in->getArray('sort1', 'string');
				$hide_ary = $in->getArray('hide1', 'int');
				
				foreach ($sort_ary as $key=>$value){
					$sort[$value] = array('sort'=> $key, 'hide' => $hide_ary[$value]);					
				}	

				$core->config_set('sort_menu1', serialize($sort));
				//Menu 2
				$sort_ary = $in->getArray('sort2', 'string');
				$hide_ary = $in->getArray('hide2', 'int');
				
				foreach ($sort_ary as $key=>$value){
					$sort2[$value] = array('sort'=> $key, 'hide' => $hide_ary[$value]);					
				}	

				$core->config_set('sort_menu2', serialize($sort2));

				//Menu 4
				$sort_ary = $in->getArray('sort4', 'string');
				$hide_ary = $in->getArray('hide4', 'int');
				
				foreach ($sort_ary as $key=>$value){
					$sort4[$value] = array('sort'=> $key, 'hide' => $hide_ary[$value]);					
				}	

				$core->config_set('sort_menu4', serialize($sort4));
				
				//Admin-Favs
				$favs = ($in->getArray('fav', 'string'));
				$core->config_set('admin_favs', serialize($favs));
				
				 // Additional Inserts
   		 	$pdh->put('links', 'save_links', array($in->getArray('linkname', 'string'), $in->getArray('linkurl', 'string'), $in->getArray('linksortid', 'int'), $in->getArray('linkwindow', 'int'), $in->getArray('link_menu', 'int'), $in->getArray('link_visibility', 'int')));
				$pdh->process_hook_queue();
		
				$core->config_set('pk_links', $in->get('pk_links', 0));
				$core->config_set('enable_admin_favs', $in->get('enable_admin_favs', 0));
				$message = array('title' => $user->lang['save_suc'], 'text' => $user->lang['pk_succ_saved'], 'color' => 'green');
				redirect('admin/manage_menus.php'.$SID.'&saved=true');
    }
		
		function process_delete_plink()
    {
        global $db, $core, $user, $tpl, $pm, $in, $plus;
        global $SID;
				
				if ($in->get('id') != "" && is_numeric($in->get('id'))){
					$db->query("DELETE FROM __plus_links WHERE link_id = '".$db->escape($in->get('id'))."'");
				}
				redirect('admin/manage_menus.php'.$SID);
		}

    // ---------------------------------------------------------
    // Display form
    // ---------------------------------------------------------
    function display_form($messages=false)
    {
        global $db, $core, $user, $tpl, $pm, $jquery, $html, $in;
        global $SID, $pcache, $pdh, $admin_menu;
			
			if ($in->get('saved') == 'true'){
				$core->message( $user->lang['pk_succ_saved'], $user->lang['save_suc'], 'green');
			}
			
			if($messages)
			{
				$pdh->process_hook_queue();
				$core->messages($messages);
			}
		
						// Menus
		$gen_menus = $core->gen_menus();
		$menus = array();

		foreach ( $gen_menus as $number => $array ){
			foreach ( $array as $menu ){
				if ($menu['editable'] !== false){
					$menus[$number][] = array($menu['link'], $menu['text']);
				}
			}
		}
		
		// The JavaScript Magic...
		$tpl->add_js('$(document).ready(function(){
			// Return a helper with preserved width of cells
			var fixHelper = function(e, ui) {
				ui.children().each(function() {
					$(this).width($(this).width());
				});
				return ui;
			};
		
			$("#sortable1 tbody, #sortable2 tbody").sortable({
				connectWith: \'.connectedSortable tbody\',
		
				helper: fixHelper,
				cancel: \'.not-sortable, tbody .not-sortable,.not-sortable tbody, .th_add, .td_add\',
				cursor: \'pointer\',
				update: function(event, ui){
									$("#sortable1 tr:odd").removeClass();
									$("#sortable1 tr:odd").addClass("row1");
									$("#sortable1 tr:even").addClass("row2");
									$("#sortable2 tr:odd").removeClass();
									$("#sortable2 tr:odd").addClass("row1");
									$("#sortable2 tr:even").addClass("row2");
								},
				 receive: function(event, ui){
						sender = $(ui.sender).attr("id");
						item = $(ui.item).attr("id");
						cb = "cb_" + item;
						if (sender == "show") {
							document.getElementById(cb).checked = true;
							document.getElementById(\'menu1_nohidden\').style.display = \'none\';
						}else {
							document.getElementById(cb).checked = false;
						}
		
									}
			}).disableSelection();
		
		});
		');
				$tpl->add_js('$(document).ready(function(){
			// Return a helper with preserved width of cells
			var fixHelper = function(e, ui) {
				ui.children().each(function() {
					$(this).width($(this).width());
				});
				return ui;
			};
		
			$("#sortable3 tbody, #sortable4 tbody").sortable({
				connectWith: \'.connectedSortable2 tbody\',
		
				helper: fixHelper,
				cursor: \'pointer\',
				cancel: \'.not-sortable, tbody .not-sortable,.not-sortable tbody, .th_add, .td_add\',
				update: function(event, ui){
									$("#sortable3 tr:odd").removeClass();
									$("#sortable3 tr:odd").addClass("row1");
									$("#sortable3 tr:even").addClass("row2");
									$("#sortable4 tr:odd").removeClass();
									$("#sortable4 tr:odd").addClass("row1");
									$("#sortable4 tr:even").addClass("row2");
								},
				 receive: function(event, ui){
						sender = $(ui.sender).attr("id");
						item = $(ui.item).attr("id");
						cb = "cb2_" + item;
						if (sender == "show2") {
							document.getElementById(cb).checked = true;
							document.getElementById(\'menu2_nohidden\').style.display = \'none\';
						}else {
							document.getElementById(cb).checked = false;
						}
		
									}
			}).disableSelection();
		
		});
												
		');				
			$tpl->add_js('$(document).ready(function(){
			// Return a helper with preserved width of cells
			var fixHelper = function(e, ui) {
				ui.children().each(function() {
					$(this).width($(this).width());
				});
				return ui;
			};
		
			$("#sortable9 tbody, #sortable10 tbody").sortable({
				connectWith: \'.connectedSortable5 tbody\',
		
				helper: fixHelper,
				cursor: \'pointer\',
				cancel: \'.not-sortable, tbody .not-sortable,.not-sortable tbody, .th_add, .td_add\',
				update: function(event, ui){
									$("#sortable9 tr:odd").removeClass();
									$("#sortable9 tr:odd").addClass("row1");
									$("#sortable9 tr:even").addClass("row2");
									$("#sortable10 tr:odd").removeClass();
									$("#sortable10 tr:odd").addClass("row1");
									$("#sortable10 tr:even").addClass("row2");
								},
				 receive: function(event, ui){
						sender = $(ui.sender).attr("id");
						item = $(ui.item).attr("id");
						cb = "cb4_" + item;
						if (sender == "show4") {
							document.getElementById(cb).checked = true;
							document.getElementById(\'menu4_nohidden\').style.display = \'none\';
						}else {
							document.getElementById(cb).checked = false;
						}
		
									}
			}).disableSelection();
		
		});
												
		');
				$tpl->add_js('$(document).ready(function(){
			// Return a helper with preserved width of cells
			var fixHelper = function(e, ui) {
				ui.children().each(function() {
					$(this).width($(this).width());
				});
				return ui;
			};
		
			$("#sortable5 tbody").sortable({
		
				helper: fixHelper,
				cursor: \'pointer\',
				cancel: \'.not-sortable, tbody .not-sortable,.not-sortable tbody, .not-sortable tbody input, .td_add\',
				update: function(event, ui){
									$("#sortable5 tr:odd").removeClass();
									$("#sortable5 tr:odd").addClass("row1");
									$("#sortable5 tr:even").addClass("row2");
								},
			}).disableSelection();
		
		});
		');
						$tpl->add_js('$(document).ready(function(){
			// Return a helper with preserved width of cells
			var fixHelper = function(e, ui) {
				ui.children().each(function() {
					
				});
				return ui;
			};
		
			$("#sortable6, #sortable7 div div").sortable({
				connectWith: \'.connectedSortable3\',
		
				helper: fixHelper,
				cursor: \'pointer\',
				cancel: \'.not-sortable, tbody .not-sortable,.not-sortable tbody, .th_add, .td_add\',

				 receive: function(event, ui){
					var classI = $(ui.item).attr("class");
					classI = classI.toString();				

					var pos = $(ui.item).parents().attr("class");
						if (pos.indexOf("receiver") != -1){
							var content = $(ui.item).html();
							var Oclass = $(ui.item).attr(\'class\');
							var Oid = $(ui.item).attr(\'id\');
							var Oicon = document.getElementById("icon_"+Oid).innerHTML;
							$(ui.item).html(\'<img src="\'+Oicon+\'"> \' +content + \'   <img src="../images/delete.png" onClick="removeThis(this.parentNode.id); 	$(this).parent().remove();" class="not-sortable" height="16" width="16">\');
							document.getElementById("cb_"+Oid).checked = true;
						}

					}
			}).disableSelection();

		
		});
												
		');
		//add additional links to the normal menus
		$links = $pdh->get('links', 'id_list');
		foreach ($links as $link){
			$link = $pdh->get('links', 'data', array($link));
			switch ($link['window'])
			{
				case '2':
				case '3':  $url = 'wrapper.php?id='.$link['id']; 
				break ;
				default: $url = $link['url'];
			}
			
			$menus['menu'.$link['menu']][] = array($url, $link['name']);
		
		}

				
				//Sorting-Data - Menu 1
				if (isset($core->config['sort_menu1'])){
					$sort_ary1 = unserialize(stripslashes($core->config['sort_menu1']));
					
					foreach ($menus['menu1'] as $key=>$menu){
						//Remove Session
						$link = preg_replace('#\?s\=([0-9A-Za-z]{1,32})?#', '', $menu[0]);
						$link = preg_replace('#\.php&#', '.php?', $link);
						if (isset($sort_ary1[$link]['sort'])){
							$tmp_menu1[$key] = $sort_ary1[$link]['sort'];
						} else {
							$tmp_menu1[$key] = 999999;
						}
					}
			
					array_multisort($tmp_menu1, SORT_ASC, SORT_NUMERIC, $menus['menu1']);
				}

				$hide1 = false;
				foreach ($menus['menu1'] as $row){
					$link = preg_replace('#\?s\=([0-9A-Za-z]{1,32})?#', '', $row[0]);
					$link = preg_replace('#\.php&#', '.php?', $link);
					$block = ($sort_ary1[$link]['hide'] == 1) ? 'menu1hide_row' : 'menu1_row';
					($sort_ary1[$link]['hide'] == 1) ? $hide1 = true : '';
					$tpl->assign_block_vars($block, array(
						'ROW_CLASS'		=> $core->switch_row_class(),
						'NAME'				=> $row[1],
						'LINK'				=> $link,
						'SORT'				=> $sort_ary1[$link]['sort'],
						'HIDE'				=> ($sort_ary1[$link]['hide'] == 1) ? 'checked' : '',
					));
				
				}
				
				//Sorting-Data - Menu 2
				if (isset($core->config['sort_menu2'])){
					$sort_ary2 = unserialize(stripslashes($core->config['sort_menu2']));
					
					foreach ($menus['menu2'] as $key=>$menu){
						//Remove Session
						$link = preg_replace('#\?s\=([0-9A-Za-z]{1,32})?#', '', $menu[0]);
						$link = preg_replace('#\.php&#', '.php?', $link);
						if (isset($sort_ary2[$link]['sort'])){
							$tmp_menu2[$key] = $sort_ary2[$link]['sort'];
						} else {
							$tmp_menu2[$key] = 999999;
						}
					}
			
					array_multisort($tmp_menu2, SORT_ASC, SORT_NUMERIC, $menus['menu2']);
				}

				$hide2 = false;
				foreach ($menus['menu2'] as $row){
					$link = preg_replace('#\?s\=([0-9A-Za-z]{1,32})?#', '', $row[0]);
					$link = preg_replace('#\.php&#', '.php?', $link);
					$block = ($sort_ary2[$link]['hide'] == 1) ? 'menu2hide_row' : 'menu2_row';
					($sort_ary2[$link]['hide'] == 1) ? $hide2 = true : '';
					$tpl->assign_block_vars($block, array(
						'ROW_CLASS'		=> $core->switch_row_class(),
						'NAME'				=> $row[1],
						'LINK'				=> $link,
						'SORT'				=> $sort_ary2[$link]['sort'],
						'HIDE'				=> ($sort_ary2[$link]['hide'] == 1) ? 'checked' : '',
					));
				
				}
				
				//Sorting-Data - Menu 4
				if (isset($core->config['sort_menu4'])){
					$sort_ary4 = unserialize(stripslashes($core->config['sort_menu4']));
					
					foreach ($menus['menu4'] as $key=>$menu){
						//Remove Session
						$link = preg_replace('#\?s\=([0-9A-Za-z]{1,32})?#', '', $menu[0]);
						$link = preg_replace('#\.php&#', '.php?', $link);
						if (isset($sort_ary4[$link]['sort'])){
							$tmp_menu4[$key] = $sort_ary4[$link]['sort'];
						} else {
							$tmp_menu4[$key] = 999999;
						}
					}
			
					array_multisort($tmp_menu4, SORT_ASC, SORT_NUMERIC, $menus['menu4']);
				}

				$hide4 = false;
				foreach ($menus['menu4'] as $row){
					$link = preg_replace('#\?s\=([0-9A-Za-z]{1,32})?#', '', $row[0]);
					$link = preg_replace('#\.php&#', '.php?', $link);
					$block = ($sort_ary4[$link]['hide'] == 1) ? 'menu4hide_row' : 'menu4_row';
					($sort_ary4[$link]['hide'] == 1) ? $hide4 = true : '';
					$tpl->assign_block_vars($block, array(
						'ROW_CLASS'		=> $core->switch_row_class(),
						'NAME'				=> $row[1],
						'LINK'				=> $link,
						'SORT'				=> $sort_ary4[$link]['sort'],
						'HIDE'				=> ($sort_ary4[$link]['hide'] == 1) ? 'checked' : '',
					));
				
				}
				
				
				//PLUS-LINKS

				// get the links stuff
				$customlink = array();
				 $result = $pdh->get('links', 'id_list');
			
				$a_linkMode= array(
					'0'				=> $user->lang['pk_set_link_type_self'],
					'1'				=> $user->lang['pk_set_link_type_link'],
					'2'				=> $user->lang['pk_set_link_type_iframe'],
					'3'				=> $user->lang['pk_set_link_type_D_iframe'],
				);
			
				$a_linkMenu= array(
					'0'				=> $user->lang['info_opt_ml_0'],
					'3'				=> $user->lang['menu_links'],
					'1'				=> $user->lang['menu_main'],
					'2'				=> $user->lang['menu_user'],				
					'4'				=> $user->lang['pk_set_link_type_menuH']
				);
				
				$a_linkVis= array(
					'0'				=> $user->lang['info_opt_vis_0'],
					'1'				=> $user->lang['info_opt_vis_1'],
					'2'				=> $user->lang['info_opt_vis_2'],
					'3'				=> $user->lang['info_opt_vis_3'],
				);
				
  foreach ($result as $link)
   {
			$row = $pdh->get('links', 'data', array($link));
		 $tpl->assign_block_vars('pluslink_row', array(
			'ROW_CLASS' => $core->switch_row_class(),
			'ID'	=>	$row['id'],
			'NAME'	=> $row['name'],
			'URL'		=> $row['url'],
			'WINDOW'	=> $html->DropDown('linkwindow['.$row['id'].']', $a_linkMode , $row['window']).$html->HelpTooltip($user->lang['pk_set_link_type_iframe_help']),
			'MENU'		=> $html->DropDown('link_menu['.$row['id'].']', $a_linkMenu , $row['menu']),
			'VIS'		=> $html->DropDown('link_visibility['.$row['id'].']', $a_linkVis , $row['visibility'])
			));

 		$max_id = ( $max_id < $row['id'] ) ? $row['id'] : $max_id;
   }
	 	$newid = ($max_id +1);
		$tpl->assign_block_vars('pluslink_row', array(
			'ROW_CLASS' => $core->switch_row_class(),
			'ID'	=>	$newid,
			'WINDOW'	=> $html->DropDown('linkwindow['.$newid.']', $a_linkMode , $row['link_window']).$html->HelpTooltip($user->lang['pk_set_link_type_iframe_help']),
			'MENU'		=> $html->DropDown('link_menu['.$newid.']', $a_linkMenu , $row['link_menu']),
			'VIS'		=> $html->DropDown('link_visibility['.$newid.']', $a_linkVis , $row['link_visibility'])
			));

			$image_path = '../images/admin/';
			unset($admin_menu['favorits']);
			if (isset($core->config['admin_favs'])){
			$favs_array = unserialize(stripslashes($core->config['admin_favs']));
			$compare_array = array();
			if (is_array($favs_array)){
				foreach ($favs_array as $fav){
						
						$items = explode('|', $fav);
						$adm = $admin_menu;
						foreach ($items as $item){			
							if (!is_numeric($item)){
								$ident = $adm[$item]['name'];
							}
							$adm = $adm[$item];
						}

						$link = preg_replace('#\?s\=([0-9A-Za-z]{1,32})?#', '', $adm['link']);
						$compare_array[] = $link;
						if ($adm['link']){
							$tpl->assign_block_vars('fav_row', array(
								'NAME' => $adm['text'],
								'ID'	=> $link,
								'ICON' => $image_path.$adm['icon'],
								'DATA'	=> $fav,
								'IDENT'	=> md5($ident),
								'GROUP'	=> $ident,	
							));
						$tpl->add_js('$(document).ready(function(){ document.getElementById("cb_'.$link.'").checked = true; })');
						}
						
					}
				}
			}

			
			// Header row
			if(is_array($admin_menu)){
				foreach($admin_menu as $k => $v){
						//Der große Block rundherum, einfach immer abfeuern
					$tpl->assign_block_vars('group_row', array(
							'ALPHA' => '', //irreleveat
						));
					// Restart next loop if the element isn't an array we can use
					if ( !is_array($v) ){continue;}
								$ident = md5($v['name']);
								$jquery->Collapse('container_'.$ident);
								$tpl->assign_block_vars('group_row.menu_row', array(
									'NAME' => '<img src="'.(($v['icon']) ? $image_path.$v['icon'] : $image_path.'plugin.png').'"> '.$v['name'],
									'GROUP'	=> $v['name'],
									'IDENT'	=> $ident,
								));

					// Generate the Menues
					if(is_array($v)){
						foreach ( $v as $k2 => $row ){
			      	$admnsubmenu = (($row['link'] && $row['text']) ? false : true);
							// Ignore the first element (header)
							if ( ($k2 == 'name' || $k2 == 'icon') &&  !$admnsubmenu){
								continue;
							}
							if($admnsubmenu){
								$tpl->assign_block_vars('group_row', array(
							'ALPHA' => '', //irreleveat
						));
								$ident = md5($row['name']);
								$jquery->Collapse('container_'.$ident);
								$tpl->assign_block_vars('group_row.menu_row', array(
									'NAME' => '<img src="'.(($row['icon']) ? $image_path.$row['icon'] : $image_path.'plugin.png').'"> '.$row['name'],
									'GROUP'	=> $row['name'],
									'IDENT'	=> $ident,
								));
								// Submenu
								if(!$row['link'] && !$row['text']){
									if(is_array($row)){
										foreach($row as $k3 => $row2){
											if ($k3 == 'name' || $k3 =='icon'){
												continue;
											}
											if ($row2['check'] == '' || $user->check_auth($row2['check'], false)){
												$link = preg_replace('#\?s\=([0-9A-Za-z]{1,32})?#', '', $row2['link']);
												if (!in_array($link, $compare_array)){
													$tpl->assign_block_vars('group_row.menu_row.item_row', array(
														'NAME' => $row2['text'],
														'ID'	=> $link,
														'ICON' => $image_path.$row2['icon'],
														'DATA'	=> $k.'|'.$k2.'|'.$k3,
													));
												}

											}
										}
									}
								}
							}else{
								if (($row['check'] == '' || $user->check_auth($row['check'], false)) && (!isset($row['check2']) || $row['check2'] == true)){
												$link = preg_replace('#\?s\=([0-9A-Za-z]{1,32})?#', '', $row['link']);
												if (!in_array($link, $compare_array)){
													$tpl->assign_block_vars('group_row.menu_row.item_row', array(
														'NAME' => $row['text'],
														'ID'	=> $link,
														'ICON' => $image_path.$row['icon'],
														'DATA'	=> $k.'|'.$k2,
													));
												}
								}
							}
						}
					}
				}
  		}



				$tpl->assign_vars(array(
					'JS_TAB'		=> $jquery->Tab_header('menu_tabs', true),
				 	'L_MANAGE_MENUS'	=> $user->lang['manage_menus'],
					'L_MENUS_INFO'	=> $user->lang['menus_info'],
					'L_LINKS_INFO'	=> $user->lang['links_info'],
					'L_MENU1'		=> $user->lang['info_opt_ml_1'],
					'L_MENU2'		=> $user->lang['info_opt_ml_2'],
					'L_MENU4'		=> $user->lang['info_opt_ml_3'],
					'L_LINKS'		=> $user->lang['pk_set_linkstable'],
					'L_INACTIVE'	=> $user->lang['inactive_entries'],
					'L_DRAGNDROP' => $user->lang['dragndrop'],
					'L_NAME'		=> $user->lang['menu_entry'],
					'L_LINK' => $user->lang['link'],
					'L_SAVE' => $user->lang['save'],
					'L_RESET' => $user->lang['reset'],
					'L_DELETE'	=> $user->lang['delete'],
					'S_NO_INACTIVE_1' => ($hide1) ? false : true,
					'S_NO_INACTIVE_2' => ($hide2) ? false : true,
					'S_NO_INACTIVE_4' => ($hide4) ? false : true,
					'L_NO_INACTIVE'	=> $user->lang['no_inactive_entries'],
					'L_LINKNAME'		=> $user->lang['pk_set_linkname'],
					'L_LINK_URL'		=> $user->lang['pk_set_linkurl'],
					'L_LINK_TYPE'		=> $user->lang['pk_set_link_type_header'],
					'L_LINK_MENU'		=> $user->lang['pk_set_link_type_menu'],
					'L_LINK_VIS'		=> $user->lang['info_opt_visibility'],
					'L_ADMIN_FAVS'	=> $user->lang['favorits_admin_menu'],
					'LINK_CB_HELP'	=> $html->HelpTooltip($user->lang['pk_help_links']),
					'LINK_CB_TEXT'	=> $user->lang['pk_set_links'],
					'LINK_CB'			=> ($core->config['pk_links'] == 1) ? 'checked' : '',
					'ADMIN_FAVS_CB'			=> ($core->config['enable_admin_favs'] == 1) ? 'checked' : '',
					'L_ADMIN_FAVS_INFO'	=> $user->lang['favorits_info'],
					'L_ENABLE_ADMIN_FAVS'	=> $user->lang['favorits_enable'],
					'L_FAVORITS'	=> $user->lang['favorits'],
					'S_NO_FAVS'	=> (count($favs_array) > 0) ? false : true,  
					'L_NO_FAVS'	=> $user->lang['no_favs_message'],
					'ACTION'		=> 'manage_menus.php'.$SID,
				));
				
        $core->set_vars(array(
            'page_title'    => $user->lang['manage_menus'],
            'template_file' => 'admin/manage_menus.html',
            'display'       => true)
        );
    }
}

$manage_menus = new Manage_Menus;
$manage_menus->process();
?>
