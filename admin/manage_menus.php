<?php
/*
* Project:		EQdkp-Plus
* License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
* Link:			http://creativecommons.org/licenses/by-nc-sa/3.0/
* -----------------------------------------------------------------------
* Began:		2009
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

//tbody not allowed withoud thead, 

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class Manage_Menus extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'pdh', 'jquery', 'core', 'config', 'html', 'admin_index'=>'admin_index');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public function __construct(){
		$this->user->check_auth('a_config_man');
		$handler = array(
			'save' => array('process' => 'save','csrf'=>true),
			'mode' => array('process' => 'delete_plink', 'csrf'=>true)
		);
		parent::__construct(false, $handler);
		$this->process();
	}

	// ---------------------------------------------------------
	// Process Save
	// ---------------------------------------------------------
	public function save() {
		//Save Plus-Links
		$arrNewLinks = $this->pdh->put('links', 'save_links', array($arrLinknames = $this->in->getArray('linkname', 'string'), $arrLinksurl = $this->in->getArray('linkurl', 'string'), $arrLinkwindows = $this->in->getArray('linkwindow', 'int'), $arrVis = $this->in->getArray('link_visibility', 'int'), $arrHeight = $this->in->getArray('link_height', 'int')));
		$this->pdh->process_hook_queue();

		//Menus
		foreach(array(1,2,3,4) as $menuid){
			$sort_ary = $this->in->getArray('sort'.$menuid, 'string');
			$hide_ary = $this->in->getArray('hide'.$menuid, 'int');
			$i = 0;
			$sort = array();
			foreach ($sort_ary as $key=>$value){
				if ($value === 'new'){
					if (isset($arrNewLinks['new'.$menuid])){
						$linkdata = $this->pdh->get('links', 'data', array($arrNewLinks['new'.$menuid]));
						switch ($linkdata['window'])
						{
							case '2':
							case '3':
							case '4':  $url = 'wrapper.php?id='.$linkdata['id']; 
							break ;
							default: $url = $linkdata['url'];
						}
						$url = $this->user->removeSIDfromString($url).'pluslink'.$linkdata['id'];
					}
				} elseif (strpos($value, 'pluslink_') === 0){
					$linkid = (int)substr($value, 9);
					$linkdata = $this->pdh->get('links', 'data', array($linkid));
					switch ($linkdata['window'])
					{
						case '2':
						case '3':
						case '4':  $url = 'wrapper.php?id='.$linkdata['id']; 
						break ;
						default: $url = $linkdata['url'];
					}
					$url = $this->user->removeSIDfromString($url).'pluslink'.$linkdata['id'];

				} else {
					$url = $value;
				}
				$hidekey = (strpos($value, 'pluslink_') === 0) ? $value : md5($value);
				$sort[md5($url)] = array('sort'=> $i, 'hide' => $hide_ary[$hidekey]);
				$i++;
			}
			$this->config->set('sort_menu'.$menuid, serialize($sort));
		}
		
		//Admin Favs
		$favs = ($this->in->getArray('fav', 'string'));
		$this->config->set('admin_favs', serialize($favs));

		$message = array('title' => $this->user->lang('save_suc'), 'text' => $this->user->lang('pk_succ_saved'), 'color' => 'green');
		$this->core->message( $this->user->lang('pk_succ_saved'), $this->user->lang('save_suc'), 'green');

		redirect('admin/manage_menus.php'.$this->SID);
	}

	public function delete_plink() {
		if ($this->in->get('id', 0) > 0){
			$this->pdh->put('links', 'delete_link', $this->in->get('id', 0));
			$this->pdh->process_hook_queue();
		}
		redirect('admin/manage_menus.php'.$this->SID);
	}

	// ---------------------------------------------------------
	// Display form
	// ---------------------------------------------------------
	public function display($messages=false){
		if($messages){
			$this->pdh->process_hook_queue();
			$this->core->messages($messages);
		}

		// Menus
		$gen_menus = $this->core->gen_menus();

		$menus = array();

		foreach ( $gen_menus as $number => $array ){
			foreach ( $array as $menu ){
				if (!isset($menu['editable'])){
					$menus[$number][] = array($menu['link'], $menu['text']);
				}
			}
		}

		// The JavaScript Magic...
		$this->tpl->add_js('
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
				receive: function(event, ui){
					sender = $(ui.sender).attr("id");
					itemi = $(ui.item).attr("id");
					cb = "cb_" + itemi;
					if (sender == "show") {
						$("#"+cb).attr("checked", "checked");
					}else {
						$("#"+cb).removeAttr("checked");
					}
				}
			});

			$("#sortable3 tbody, #sortable4 tbody").sortable({
				connectWith: \'.connectedSortable2 tbody\',
				helper: fixHelper,
				cursor: \'pointer\',
				cancel: \'.not-sortable, tbody .not-sortable,.not-sortable tbody, .th_add, .td_add\',
				receive: function(event, ui){
					sender = $(ui.sender).attr("id");
					itemi = $(ui.item).attr("id");
					cb = "cb2_" + itemi;
					if (sender == "show2") {
						$("#"+cb).attr("checked", "checked");
					}else {
						$("#"+cb).removeAttr("checked");
					}
				}
			});
			
			$("#sortable5 tbody, #sortable6 tbody").sortable({
				connectWith: \'.connectedSortable3 tbody\',
				helper: fixHelper,
				cursor: \'pointer\',
				cancel: \'.not-sortable, tbody .not-sortable,.not-sortable tbody, .th_add, .td_add\',
				receive: function(event, ui){
					sender = $(ui.sender).attr("id");
					itemi = $(ui.item).attr("id");
					cb = "cb3_" + itemi;
					if (sender == "show3") {
						$("#"+cb).attr("checked", "checked");
					}else {
						$("#"+cb).removeAttr("checked");
					}
				}
			});

			$("#sortable7 tbody, #sortable8 tbody").sortable({
				connectWith: \'.connectedSortable4 tbody\',
				helper: fixHelper,
				cursor: \'pointer\',
				cancel: \'.not-sortable, tbody .not-sortable,.not-sortable tbody, .th_add, .td_add\',
				receive: function(event, ui){
					sender = $(ui.sender).attr("id");
					itemi = $(ui.item).attr("id");
					cb = "cb4_" + itemi;
					if (sender == "show4") {
						$("#"+cb).attr("checked", "checked");
					}else {
						$("#"+cb).removeAttr("checked");
					}
				}
			});

			$("#sortable9, #sortable10 div div ul").sortable({
				connectWith: \'.connectedSortable5\',
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
							$(ui.item).html(\'<img src="\'+Oicon+\'" alt="" /> \' +content + \'   <img src="../images/global/delete.png" onclick="removeThis(this.parentNode.id); 	$(this).parent().remove();" class="not-sortable" height="16" width="16" alt="" />\');
							document.getElementById("cb_"+Oid).checked = true;
						}

					}
			}).disableSelection();
			
			$(".equalHeights").equalHeights();
			
		', 'docready');

		$this->tpl->add_js('
			function removeThis(test){
				document.getElementById("cb_"+test).checked = false;
				var name = document.getElementById(test).innerHTML;
				var clas = document.getElementById(test).className;
				regex = new RegExp(\'(<img.*?>)\',\'gi\');
				do {
					found = false;
					if (regex.exec(name)) {
						found = true;
						name = name.replace(RegExp.$1, \'\');
					}
				} while (found != false)
				$("#t"+clas+" ul").append(\'<li class="\'+clas+\'" id="\'+test+\'">\'+name+\'</li>\');
				document.getElementById("cb_"+test).checked = false;
			}
		');
		//add additional links to the normal menus
		$links = $this->pdh->get('links', 'id_list');
		foreach ($links as $link){
			$link = $this->pdh->get('links', 'data', array($link));
			switch ($link['window'])
			{
				case '2':
				case '3':
				case '4':  $url = 'wrapper.php?id='.$link['id']; 
				break ;
				default: $url = $link['url'];
			}
			
			$menus['menu'.$link['menu']][] = array($url, $link['name'], 'plus_link_id'	=> $link['id'], 'id'=>"pluslink".$link['id']);
		}
		
		$a_linkMode= array(
			'0'				=> $this->user->lang('pk_set_link_type_self'),
			'1'				=> $this->user->lang('pk_set_link_type_link'),
			'2'				=> $this->user->lang('pk_set_link_type_iframe'),
			'4'				=> $this->user->lang('pk_set_link_type_D_iframe_womenues'),
		);
					
		$a_linkVis= array(
			'0'				=> $this->user->lang('info_opt_vis_0'),
			'1'				=> $this->user->lang('info_opt_vis_1'),
			'2'				=> $this->user->lang('info_opt_vis_2'),
			'3'				=> $this->user->lang('info_opt_vis_3'),
		);

		foreach (array(1,2,3,4) as $menuid){
				//Sorting-Data
				$tmp_menu = array();
				$sort_ary = array();
				if ($this->config->get('sort_menu'.$menuid)){
					$sort_ary = unserialize(stripslashes($this->config->get('sort_menu'.$menuid)));
					if(isset($menus['menu'.$menuid]) && is_array($menus['menu'.$menuid])){

						foreach ($menus['menu'.$menuid] as $key=>$menu){
							//Remove Session
							$link = $this->user->removeSIDfromString($menu[0]);
							$linkhash = md5($link.((isset($menu['id'])) ? $menu['id'] : ''));
							if (isset($sort_ary[$linkhash]['sort'])){
								$tmp_menu[$key] = $sort_ary[$linkhash]['sort'];
							} else {
								$tmp_menu[$key] = 999999;
							}
						}
						array_multisort($tmp_menu, SORT_ASC, SORT_NUMERIC, $menus['menu'.$menuid]);
					}
				}
				$this->tpl->assign_vars(array(
					'DD_LINK_WINDOW'.$menuid	=> $this->html->DropDown('linkwindow[new'.$menuid.']', $a_linkMode , '', '', '', 'input th_add'),
					'DD_LINK_VIS'.$menuid		=> $this->html->DropDown('link_visibility[new'.$menuid.']', $a_linkVis , '', '', '', 'input th_add'),
				));
				
				if(isset($menus['menu'.$menuid]) && is_array($menus['menu'.$menuid])){
					foreach ($menus['menu'.$menuid] as $row){
						$link = $this->user->removeSIDfromString($row[0]);
						$linkhash = md5($link.((isset($row['id'])) ? $row['id'] : ''));
						$block = (isset($sort_ary[$linkhash]['hide']) && $sort_ary[$linkhash]['hide'] == 1) ? 'menu'.$menuid.'hide_row' : 'menu'.$menuid.'_row';
						$vars = array(
							'NAME'				=> $row[1],
							'LINK'				=> $link,
							'LINK_HASH'			=> md5($link),
							'ID'				=> 'm'.md5('menu'.$menuid.$linkhash),
							'SORT'				=> (isset($sort_ary[$linkhash]['sort'])) ? $sort_ary[$linkhash]['sort'] : '',
							'HIDE'				=> (isset($sort_ary[$linkhash]['hide']) && $sort_ary[$linkhash]['hide'] == 1) ? 'checked="checked"' : '',
						);
						if (isset($row['plus_link_id'])){
							
							$arrLink = $this->pdh->get('links', 'data', array((int)$row['plus_link_id']));
							$vars['S_PLUSLINK'] = true;
							$vars['PLUSLINK_ID'] = $arrLink['id'];
							$vars['PLUSLINK_NAME'] = $arrLink['name'];
							$vars['PLUSLINK_URL'] = $arrLink['url'];
							$vars['PLUSLINK_HEIGHT'] = $arrLink['height'];
							$vars['PLUSLINK_WINDOW'] = $this->html->DropDown('linkwindow['.$arrLink['id'].']', $a_linkMode , $arrLink['window'], '', '', 'input th_add', 'linkwindow'.$arrLink['id']);
							$vars['PLUSLINK_VIS'] = $this->html->DropDown('link_visibility['.$arrLink['id'].']', $a_linkVis , $arrLink['visibility'], '', '', 'input th_add', 'link_visibility'.$arrLink['id']);
						}
						
						$this->tpl->assign_block_vars($block, $vars);
					}
				}
		
		}
	

			$image_path = '../images/admin/';
			$admin_menu = $this->admin_index->adminmenu(false);
			unset($admin_menu['favorits']);
			$compare_array = array();
			$favs_array = array();
			if ($this->config->get('admin_favs')){
				$favs_array = unserialize(stripslashes($this->config->get('admin_favs')));
				$no_favs = true;
					if (is_array($favs_array)){
						foreach ($favs_array as $fav_key => $fav){
							$items = explode('|', $fav);
							$adm = $admin_menu;
							foreach ($items as $item){
								$latest = $adm;
								$adm = $adm[$item];
							}

							$link = preg_replace('#\?s\=([0-9A-Za-z]{1,32})?#', '', $adm['link']);

							$compare_array[] = $link;
							if ($adm['link']){
								$this->tpl->assign_block_vars('fav_row', array(
									'NAME' => $adm['text'],
									'ID'	=> 'fav_'.$fav_key,
									'ICON' => $image_path.$adm['icon'],
									'DATA'	=> $fav,
									'IDENT'	=> 'i'.md5($latest['name']),
									'GROUP'	=> $latest['name'],	
								));
								$this->tpl->add_js('document.getElementById("cb_fav_'.$fav_key.'").checked = true;', 'docready');
								$no_favs = false;
							}
								
						}
					}
			}

			// Header row
			if(is_array($admin_menu)){
				foreach($admin_menu as $k => $v){
					//Der große Block rundherum, einfach immer abfeuern
					$this->tpl->assign_block_vars('group_row', array(
						'ALPHA' => '', //irreleveat
					));

					// Restart next loop if the element isn't an array we can use
					if ( !is_array($v) ){continue;}
					
					$ident = 'i'.md5($v['name']);
					$this->jquery->Collapse('#container_'.$ident);
					$this->tpl->assign_block_vars('group_row.menu_row', array(
						'NAME' => '<img src="'.((isset($v['icon'])) ? $image_path.$v['icon'] : $image_path.'plugin.png').'" alt="" /> '.$v['name'],
						'GROUP'	=> $v['name'],
						'IDENT'	=> $ident,
					));

					// Generate the Menues
					if(is_array($v)){
						foreach ( $v as $k2 => $row ){

							$admnsubmenu = ((isset($row['link']) && isset($row['text'])) ? false : true);
							// Ignore the first element (header)
							if ( ($k2 == 'name' || $k2 == 'icon')){
								continue;
							}
							if($admnsubmenu){
								$this->tpl->assign_block_vars('group_row', array(
									'ALPHA' => '', //irreleveat
								));
								$ident = 'i'.md5($row['name']);
								$this->jquery->Collapse('#container_'.$ident);
								$this->tpl->assign_block_vars('group_row.menu_row', array(
									'NAME' => '<img src="'.((isset($row['icon'])) ? $image_path.$row['icon'] : $image_path.'plugin.png').'" alt="" /> '.$row['name'],
									'GROUP'	=> $row['name'],
									'IDENT'	=> $ident,
								));
								// Submenu
								if(!isset($row['link']) && !isset($row['text'])){
									if(is_array($row)){
										foreach($row as $k3 => $row2){
											if ($k3 == 'name' || $k3 =='icon'){
												continue;
											}

											$link = preg_replace('#\?s\=([0-9A-Za-z]{1,32})?#', '', $row2['link']);
											if (!in_array($link, $compare_array)){
												$this->tpl->assign_block_vars('group_row.menu_row.item_row', array(
													'NAME' => $row2['text'],
													'ID'	=> 'l'.md5($link),
													'ICON' => $image_path.$row2['icon'],
													'DATA'	=> $k.'|'.$k2.'|'.$k3,
												));
											}
										}
									}
								}
							}else{

								$link = preg_replace('#\?s\=([0-9A-Za-z]{1,32})?#', '', $row['link']);
								if (!in_array($link, $compare_array)){
									$this->tpl->assign_block_vars('group_row.menu_row.item_row', array(
										'NAME' => $row['text'],
										'ID'	=> 'l'.md5($link),
										'ICON' => $image_path.$row['icon'],
										'DATA'	=> $k.'|'.$k2,
									));
								}

							}
						}
					}
				}
		}
				$this->jquery->Tab_header('menu_tabs', true);
				if ($this->in->exists('tab')){
					$this->jquery->Tab_Select('menu_tabs', $this->in->get('tab',0));
				}
				$this->tpl->assign_vars(array(				
					'LINK_CB_HELP'			=> $this->html->ToolTip($this->user->lang('pk_help_links'), '<img src="'.$this->root_path.'images/global/info.png" alt="" />'),
					'CSRF_MODE_TOKEN'		=> $this->CSRFGetToken('mode'),
					'S_NO_FAVS'				=> (count($favs_array) > 0) ? false : true,  
				));
				
		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('manage_menus'),
			'template_file'		=> 'admin/manage_menus.html',
			'display'			=> true)
		);
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_Manage_Menus', Manage_Menus::__shortcuts());
registry::register('Manage_Menus');
?>