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

//tbody not allowed withoud thead, 

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class Manage_Menus extends page_generic {

	public function __construct(){
		$this->user->check_auth('a_config_man');
		$handler = array(
			'save' => array('process' => 'save','csrf'=>true),
		);
		parent::__construct(false, $handler);
		$this->process();
	}

	// ---------------------------------------------------------
	// Process Save
	// ---------------------------------------------------------
	public function save() {
	
		//Plus links deletion
		$strPlusDelete = $this->in->get('del_pluslinks');
		if($strPlusDelete != "0"){
			$arrPlusLinks = explode(',', $strPlusDelete);
			$arrPlusLinks = array_unique($arrPlusLinks);
			
			foreach($arrPlusLinks as $pid){
				$pid = intval($pid);
				if($pid === 0) continue;
				
				$this->pdh->put('links', 'delete_link', array($pid));
			}
		}
		
		//MENU
		$json = $this->in->get('serialized', '', 'noencquotes');
		$arrItems = $this->in->getArray('mainmenu', 'string');
		
		$decoded = json_decode($json, true);
		$arrSorted = array();
		if ($decoded){
			$intFirstLevel = -1;
			$intSecondLevel = -1;
			foreach($decoded as $item){
				if ((int)$item['item_id']){
					$hash = $arrItems[$item['item_id']]['id'];
					if ($arrItems[$item['item_id']]['type'] == 'pluslink'){
						//New plus links
						if ($hash == 'new'){
							$data = $arrItems[$item['item_id']];
							$pid = $this->pdh->put('links', 'add', array($data['name'], $data['url'],$data['window'],$data['visibility'],$data['windowsize']));
							if (!$pid) continue;
							$link = $this->core->handle_link($data['url'],$data['name'], $data['window'], 'pluslink'.$pid);
							$hash = $this->core->build_link_hash($link);
						} else {
							//Update existing plus link
							$data = $arrItems[$item['item_id']];
							$pid = $this->pdh->put('links', 'update', array($data['specialid'], $data['name'], $data['url'],$data['window'],$data['visibility'],$data['windowsize']));
							if (!$pid) continue;
							$link = $this->core->handle_link($data['url'],$data['name'], $data['window'], 'pluslink'.$data['specialid']);
							$hash = $this->core->build_link_hash($link);
						}
					}
					
					$hidden = $arrItems[$item['item_id']]['hidden'];
					switch((int)$item['depth']){
						case 1: 	$intFirstLevel++;									
									$arrSorted[$intFirstLevel]['item'] = array('hash' => $hash, 'hidden' => $hidden);
						break;
						case 2:		$intSecondLevel++;
									$arrSorted[$intFirstLevel]['_childs'][$intSecondLevel]['item'] = array('hash' => $hash, 'hidden' => $hidden);
						break;
						case 3:		$arrSorted[$intFirstLevel]['_childs'][$intSecondLevel]['_childs'][] = array('hash' => $hash, 'hidden' => $hidden);
						break;
					}
					
				}
			}

			$this->config->set('mainmenu', serialize($arrSorted));
			$this->pdh->process_hook_queue();			
		}
				
		//Admin Favs
		$favs = ($this->in->getArray('fav', 'string'));
		$this->config->set('admin_favs', serialize($favs));
		
		redirect('admin/manage_menus.php'.$this->SID.'&status=saved');
	}

	// ---------------------------------------------------------
	// Display form
	// ---------------------------------------------------------
	public function display($messages=false){
		if($messages){
			$this->pdh->process_hook_queue();
			$this->core->messages($messages);
		}
		
		if ($this->in->get('status') == 'saved'){
			$this->core->message( $this->user->lang('pk_succ_saved'), $this->user->lang('save_suc'), 'green');
		}

		// Menus
		$arrOl = $this->build_menu_ol();
		$strMenuOl = $arrOl[0];
		$intMaxID = $arrOl[1];

		// The JavaScript Magic...
		$this->tpl->add_js('
			// Return a helper with preserved width of cells
			var fixHelper = function(e, ui) {
				ui.children().each(function() {
					$(this).width($(this).width());
				});
				return ui;
			};

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
							$(ui.item).html(content + \'  <i class="fa fa-trash-o fa-lg not-sortable hand" onclick="removeThis(this.parentNode.id); 	$(this).parent().remove();"></i>\');
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
				regex = new RegExp(\'(<img class="delete".*?>)\',\'gi\');
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
		
		$a_linkMode= array(
			'0'				=> $this->user->lang('pk_set_link_type_self'),
			'1'				=> $this->user->lang('pk_set_link_type_link'),
			'2'				=> $this->user->lang('pk_set_link_type_iframe'),
			'4'				=> $this->user->lang('pk_set_link_type_D_iframe_womenues'),
			'5'				=> $this->user->lang('pk_set_link_type_D_iframe_woblocks'),
		);
					
		$a_linkVis= array(
			'0'				=> $this->user->lang('info_opt_vis_0'),
			'1'				=> $this->user->lang('info_opt_vis_1'),
			'2'				=> $this->user->lang('info_opt_vis_2'),
			'3'				=> $this->user->lang('info_opt_vis_3'),
		);
	

			$image_path = '../images/admin/';
			include_once($this->root_path.'core/admin_functions.class.php');
			
			$admin_menu = register('admin_functions')->adminmenu(false);
			unset($admin_menu['favorits']);
			$compare_array = array();
			$favs_array = array();
			if ($this->config->get('admin_favs')){
				$favs_array = $this->config->get('admin_favs');
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
									'ICON' => $this->core->icon_font((isset($adm['icon'])) ? $adm['icon'] : ((isset($adm['img']) ? $adm['img'] : (($nodefimage) ? '' : 'fa-puzzle-piece'))), 'fa-lg', $image_path),
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
						'NAME' => $this->core->icon_font((isset($v['icon'])) ? $v['icon'] : ((isset($v['img']) ? $v['img'] : (($nodefimage) ? '' : 'fa-puzzle-piece'))), 'fa-lg', $image_path).' '.$v['name'],
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
									'NAME' => $this->core->icon_font(((isset($row['icon'])) ? $row['icon'] : 'fa-puzzle-piece'), 'fa-lg', $image_path).' '.$row['name'],
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
													'NAME'	=> $row2['text'],
													'ID'	=> 'l'.md5($link),
													'ICON'	=> $this->core->icon_font((isset($row2['icon'])) ? $row2['icon'] : ((isset($row2['img']) ? $row2['img'] : (($nodefimage) ? '' : 'fa-puzzle-piece'))), 'fa-lg', $image_path),
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
										'NAME'	=> $row['text'],
										'ID'	=> 'l'.md5($link),
										'ICON'	=> $this->core->icon_font((isset($row['icon'])) ? $row['icon'] : ((isset($row['img']) ? $row['img'] : (($nodefimage) ? '' : 'fa-puzzle-piece'))), 'fa-lg', $image_path),
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
		
		$drpdwn_rights = $this->pdh->aget('user_groups', 'name', 0, array($this->pdh->get('user_groups', 'id_list')));
		$drpdwn_rights[0] = $this->user->lang('cl_all');
		ksort($drpdwn_rights);
		
		$arrLinkTypes = array('internal' => $this->user->lang('link_type_internal'), 'external' => $this->user->lang('link_type_external'));
		
		$arrLinkCategories = $this->build_link_categories();
		if (count($arrLinkCategories)) $arrLinkTypes = array_merge($arrLinkTypes, $arrLinkCategories);
		
		$this->tpl->assign_vars(array(		
			'CSRF_MODE_TOKEN'		=> $this->CSRFGetToken('mode'),
			'S_NO_FAVS'				=> (count($favs_array) > 0) ? false : true,
			'DD_LINK_WINDOW'		=> new hdropdown('editlink-window', array('options' => $a_linkMode, 'class' => 'editlink-window')),
			'MS_LINK_VISIBILITY'	=> $this->jquery->MultiSelect("editlink-visibility", $drpdwn_rights, 0),
			'DD_LINK_VISIBILITY'	=> new hdropdown('editlink-visibility', array('options' => $a_linkVis, 'class' => 'editlink-visibility')),
			'DD_LINK_TYPE'			=> new hdropdown('link_type', array('options' => $arrLinkTypes, 'class' => 'link_type')),
			'MENU_OL'				=> $strMenuOl,
			'NEW_ID'				=> ++$intMaxID,
			'DD_ARTICLES'			=> new hdropdown('editlink-article', array('options' => $this->build_article_dropdown(), 'class' => 'editlink-article')),
		));
				
		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('manage_menus'),
			'template_file'		=> 'admin/manage_menus.html',
			'display'			=> true)
		);
	}
	
	private function build_article_dropdown(){
		$arrItems = $this->core->build_menu_array(true, true);
		$arrOut[''] = "";
		foreach($arrItems as $k => $v){
			if ( !is_array($v) )continue;

			if (!isset($v['childs'])){
				if (!$this->check_for_hidden_article($v) && (isset($v['article']) || isset($v['category']))) {
					if (isset($v['category'])){
						$arrOut[$v['_hash']] = $this->pdh->get('article_categories', 'name_prefix', array($v['id'])).$this->pdh->get('article_categories', 'name', array($v['id']));
					} else {
						$catid = $this->pdh->get('articles', 'category', array($v['id']));
						$arrOut[$v['_hash']] = $this->pdh->get('article_categories', 'name_prefix', array($catid)).' -> '.$this->pdh->get('articles', 'title', array($v['id']));
					}
				}
				if(!$this->check_for_hidden_article($v) && (isset($v['static']))){
					$arrOut[$v['_hash']] = $v['text'];
				}
				
			}
		}
		return $arrOut;
	}
	
	private function build_menu_ol(){
		$arrItems = $this->core->build_menu_array(true);
		
		$html  = '<ol class="sortable">';
		$id = 0;
		foreach($arrItems as $k => $v){
			if ( !is_array($v) )continue;
			$id++;
			
			if (!isset($v['childs'])){
				if (!$this->check_for_hidden_article($v, $id)) continue;
				$html .= '<li id="list_'.$id.'">'.$this->create_li($v, $id).'</li>';
				
			} else {
				$html .= '<li id="list_'.$id.'">'.$this->create_li($v, $id).'<ol>';
				
				foreach($v['childs'] as $k2 => $v2){
					$id++;
					if (!isset($v2['childs'])){
						if (!$this->check_for_hidden_article($v2, $id)) continue;
						$html .= '<li id="list_'.$id.'">'.$this->create_li($v2, $id).'</li>';
					} else {
						$html .= '<li id="list_'.$id.'">'.$this->create_li($v2, $id).'<ol>';
						
						foreach($v2['childs'] as $k3 => $v3){
							$id++;
							if (!$this->check_for_hidden_article($v3, $id)) continue;
							$html .= '<li id="list_'.$id.'">'.$this->create_li($v3, $id).'</li>';
							
						}
						
						$html .= '</ol></li>';
					}
					
				}
				
				$html .= '</ol></li>';
			}
		}

		$html .= '</ol>';
		
		return array($html, $id);
	}
	
	private function check_for_hidden_article($arrLink){
		if ((int)$arrLink['hidden'] && (isset($arrLink['article']) || isset($arrLink['category']) || isset($arrLink['default_hide']) || isset($arrLink['static']))) return false;
		return true;
	}
	
	private function build_link_categories(){
		$arrItems = $this->core->build_menu_array(true);
		
		$arrCategories = array();
		$arrOptions = array();
		
		foreach($arrItems as $k => $v){
			if (isset($v['link_category'])) {
				if (!isset($arrCategories[md5($v['link_category'])])){
					$arrCategories[md5($v['link_category'])] = $this->user->lang($v['link_category']);
				}
				$strHash = $this->core->build_link_hash($v);
				$arrOptions[md5($v['link_category'])][$strHash] =  $v['text'];
			}
		}
		
		foreach($arrCategories as $strCategoryID => $strCategoryName){
			$this->tpl->assign_block_vars('link_type_row', array(
					'ID'	=> $strCategoryID,
					'NAME'	=> $strCategoryName,
					'DD'	=> new hdropdown('links_'.$strCategoryID, array('options' => $arrOptions[$strCategoryID])),
			));
		}
		return $arrCategories;
	}
	
	private function create_li($arrLink, $id){
		$hash = $arrLink['_hash'];
		$blnPluslink = (isset($arrLink['id']) && strpos($arrLink['id'], "pluslink") === 0);

		$html = '
			<div data-linkid="'.$id.'">
			<span class="ui-icon ui-icon-arrowthick-2-n-s" title="'.$this->user->lang('dragndrop').'" style="display:inline-block;"></span>&nbsp;
			<span class="link-hide '.(((int)$arrLink['hidden']) ? 'eye-gray' : 'eye').'" '.(( (isset($arrLink['article']) || isset($arrLink['category']) || isset($arrLink['static']))) ? 'style="display:none;"' : '').'></span>&nbsp;';
			if ($blnPluslink){
				$plinkid = intval(str_replace("pluslink", "", $arrLink['id']));
				$arrPluslinkData = $this->pdh->get('links', 'data', array($plinkid));
				$html .= '<i class="fa fa-pencil"></i>&nbsp;<a href="javascript:void(0);" class="edit-menulink-trigger">'.$arrLink['text'].' ('.$arrLink['link'].')</a>
					<i class="fa fa-trash-o fa-lg hand" onclick="delete_plink('.$plinkid.', this)" title="'.$this->user->lang("delete").'"></i>
					<input type="hidden" value="'.$arrPluslinkData['url'].'"  name="mainmenu['.$id.'][url]" class="link-url">
					<input type="hidden" value="'.$arrPluslinkData['name'].'"  name="mainmenu['.$id.'][name]" class="link-name">
					<input type="hidden" value="'.$arrPluslinkData['window'].'"  name="mainmenu['.$id.'][window]" class="link-window">
					<input type="hidden" value="'.$arrPluslinkData['height'].'"  name="mainmenu['.$id.'][windowsize]" class="link-windowsize">
					<input type="hidden" value=\''.$arrPluslinkData['visibility'].'\'  name="mainmenu['.$id.'][visibility]" class="link-visibility">
					<input type="hidden" value="'.$plinkid.'"  name="mainmenu['.$id.'][specialid]" class="link-specialid">
				';
			} else {
				$html .= ''.$arrLink['text'].' ('.$this->user->removeSIDfromString($arrLink['link']).') <i class="fa fa-trash-o fa-lg hand" title="'.$this->user->lang('delete').'" onclick="softdelete_row(this);"></i>';
			}	
			$html .= '
			<input type="hidden" value="'.(($blnPluslink) ? 'pluslink' : 'normal').'"  name="mainmenu['.$id.'][type]" class="link-type">			
			<input type="hidden" value="'.(((int)$arrLink['hidden']) ? 1 : 0).'"  name="mainmenu['.$id.'][hidden]" class="link-hidden">
			<input type="hidden" value="'.$hash.'"  name="mainmenu['.$id.'][id]" class="link-id">
			</div>
		';
		return $html;
	}
}
registry::register('Manage_Menus');
?>