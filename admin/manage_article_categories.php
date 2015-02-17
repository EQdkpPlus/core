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

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class Manage_Article_Categories extends page_generic {

	public function __construct(){
		$this->user->check_auths(array('a_articles_man', 'a_article_categories_man'), 'OR');
		$handler = array(
			'save' 		=> array('process' => 'save', 'csrf' => true),
			'update'	=> array('process' => 'update', 'csrf' => true),
			'checkalias'=> array('process' => 'ajax_checkalias'),
			'calcperm'	=> array('process' => 'ajax_calculate_permission'),
			'c'			=> array('process' => 'edit'),
		);
		parent::__construct(false, $handler, array('article_categories', 'name'), null, 'selected_ids[]');
		$this->process();
	}
	
	public function ajax_checkalias(){
		$strAlias = $this->in->get('alias');
		$intCID = $this->in->get('c', 0);
		
		$blnResult = $this->pdh->get('article_categories', 'check_alias', array($strAlias, true));
		if (!$blnResult && $this->pdh->get('article_categories', 'alias', array($intCID)) === $strAlias) $blnResult = true;
		if (is_numeric($strAlias)) $blnResult = false;
		
		header('content-type: text/html; charset=UTF-8');
		if ($blnResult){
			echo 'true';
		} else {
			echo 'false';
		}
		exit;
	}
	
	public function ajax_calculate_permission(){
		$intCID = $this->in->get('c', 0);
		$strPermission = $this->in->get('perm');
		$strPermissionValue = $this->in->get('value', 0);
		$intGroupID = $this->in->get('gid', 0);
		$intParentID = $this->in->get('parent', 0);
		$blnResult = $this->pdh->get('article_categories', 'calculated_permissions', array($intCID, $strPermission, $intGroupID, $strPermissionValue, $intParentID));
	
		header('content-type: text/html; charset=UTF-8');
		if ($blnResult){
			echo '<span class="positive">'.$this->user->lang('allowed').'</span>';
		} else {
			echo '<span class="negative">'.$this->user->lang('disallowed').'</span>';
		}
		exit;
	}
	
	public function update(){
		$this->user->check_auth('a_article_categories_man');
		
		$id = $this->in->get('c', 0);
		$strName = $this->in->get('name');
		$strDescription = $this->in->get('description', '', 'raw');
		$strAlias = $this->in->get('alias');
		$intPublished = $this->in->get('published', 0);
		$intPortalLayout = $this->in->get('portal_layout', 0);
		$intArticlePerPage = $this->in->get('per_page', 25);
		$intParentCategory = (($id == 1) ? 0 : $this->in->get('parent',0));
		$intListType = $this->in->get('list_type', 0);
		$intShowChilds = $this->in->get('show_childs', 0);
		$arrAggregation = $this->in->getArray('aggregation', 'int');
		$intFeaturedOnly = $this->in->get('featured_only', 0);
		$intSocialButtons = $this->in->get('show_ssb', 0);
		$intArticlePublishedState = $this->in->get('article_published_state', 0);
		$arrPermissions = $this->in->getArray('perm', 'int');
		$intNotifyUnpublishedArticles = $this->in->get('notify_unpublished', 0);
		$intHideHeader = $this->in->get('hide_header', 0);
		$intSortationType = $this->in->get('sortation_type', 0);
		$intFeaturedOntop = $this->in->get('featured_ontop', 0);
		$intHideOnRSS = $this->in->get('hide_on_rss', 0);
		
		if ($strName == "" ) {
			$this->core->message($this->user->lang('name'), $this->user->lang('missing_values'), 'red');
			$this->edit();
			return;
		}
		
		if ($id){
			$blnResult = $this->pdh->put('article_categories', 'update', array($id, $strName, $strDescription, $strAlias, $intPublished, $intPortalLayout, $intArticlePerPage, $intParentCategory, $intListType, $intShowChilds, $arrAggregation, $intFeaturedOnly, $intSocialButtons, $intArticlePublishedState, $arrPermissions, $intNotifyUnpublishedArticles, $intHideHeader, $intSortationType, $intFeaturedOntop, $intHideOnRSS));
		} else {
			$blnResult = $this->pdh->put('article_categories', 'add', array($strName, $strDescription, $strAlias, $intPublished, $intPortalLayout, $intArticlePerPage, $intParentCategory, $intListType, $intShowChilds, $arrAggregation, $intFeaturedOnly, $intSocialButtons, $intArticlePublishedState, $arrPermissions, $intNotifyUnpublishedArticles, $intHideHeader, $intSortationType, $intFeaturedOntop, $intHideOnRSS));
		}
		
		if ($blnResult){
			$this->pdh->process_hook_queue();
			$this->core->message($this->user->lang('success_create_article_category'), $this->user->lang('success'), 'green');
		} else {
			$this->core->message($this->user->lang('error_create_article_category'), $this->user->lang('error'), 'red');
		}
		
		$this->display();
	}
	
	public function save(){
		$this->user->check_auth('a_article_categories_man');
		
		$arrSortables = $this->in->getArray('sortCategories', 'int');
		$arrSortablesFlipped = array_flip($arrSortables);
	
		$arrPublished = $this->in->getArray('published', 'int');
		foreach($arrPublished as $key => $val){
			$this->pdh->put('article_categories', 'update_sortandpublished', array($key, (int)$arrSortablesFlipped[$key], (int)$val));
		}
		$this->pdh->process_hook_queue();
		$this->core->message($this->user->lang('pk_succ_saved'), $this->user->lang('success'), 'green');
	}
	
	public function delete(){
		$this->user->check_auth('a_article_categories_man');
		
		$retu = array();
		if(count($this->in->getArray('selected_ids', 'int')) > 0) {
			foreach($this->in->getArray('selected_ids','int') as $id) {
				//Dont Delete System Category
				if ($id == 1) continue; 
				$pos[] = stripslashes($this->pdh->get('article_categories', 'name', array($id)));
				$retu[$id] = $this->pdh->put('article_categories', 'delete', array($id));
				$this->pdh->put('articles', 'delete_category', array($id));
			}
		}

		if(!empty($pos)) {
			$messages[] = array('title' => $this->user->lang('del_suc'), 'text' => implode(', ', $pos), 'color' => 'green');
			$this->core->messages($messages);
		}
		
		$this->pdh->process_hook_queue();
	}
	
	public function edit(){
		$this->user->check_auth('a_article_categories_man');
		
		$id = $this->in->get('c', 0);
		
		$arrPermissionDropdown = array(
			-1 => $this->user->lang('inherited'),
			1 => $this->user->lang('allowed'),
			0 => $this->user->lang('disallowed')
		);
		
		$arrPortalLayouts = $this->pdh->aget('portal_layouts', 'name', 0, array($this->pdh->get('portal_layouts', 'id_list')));
				
		$arrGroups = $this->pdh->get('user_groups', 'id_list', array());		
		$arrPermissions = $this->pdh->get('article_categories', 'permissions', array($id));
		foreach($arrGroups as $gid){
			$this->tpl->assign_block_vars('group_row', array(
				'ID' 		=> $gid,
				'NAME' 		=> $this->pdh->get('user_groups', 'name', array($gid)),
				'DD_CREATE' =>new hdropdown('perm[cre]['.$gid.']', array('options' => $arrPermissionDropdown, 'value' => (isset($arrPermissions['cre'][$gid]) ? $arrPermissions['cre'][$gid] : -1), 'js' => 'onchange="calculate_permission(\'cre\', '.$gid.', this)"')),
				'DD_UPDATE' => new hdropdown('perm[upd]['.$gid.']', array('options' => $arrPermissionDropdown, 'value' => (isset($arrPermissions['upd'][$gid]) ? $arrPermissions['upd'][$gid] : -1), 'js' => 'onchange="calculate_permission(\'upd\', '.$gid.', this)"')),
				'DD_DELETE' => new hdropdown('perm[del]['.$gid.']', array('options' => $arrPermissionDropdown, 'value' => (isset($arrPermissions['del'][$gid]) ? $arrPermissions['del'][$gid] : -1), 'js' => 'onchange="calculate_permission(\'del\', '.$gid.', this)"')),
				'DD_READ' 	=> new hdropdown('perm[rea]['.$gid.']', array('options' => $arrPermissionDropdown, 'value' => (isset($arrPermissions['rea'][$gid]) ? $arrPermissions['rea'][$gid] : -1), 'js' => 'onchange="calculate_permission(\'rea\', '.$gid.', this)"')),
				'DD_CHANGE_STATE' => new hdropdown('perm[chs]['.$gid.']', array('options' => $arrPermissionDropdown, 'value' => (isset($arrPermissions['chs'][$gid]) ? $arrPermissions['chs'][$gid] : -1), 'js' => 'onchange="calculate_permission(\'chs\', '.$gid.', this)"')),
				'CALC_CREATE' 		=> $this->pdh->get('article_categories', 'calculated_permissions', array((($id) ? $id : 1), 'cre', $gid)) ? '<span class="positive">'.$this->user->lang('allowed').'</span>' : '<span class="negative">'.$this->user->lang('disallowed').'</span>',
				'CALC_UPDATE' 		=> $this->pdh->get('article_categories', 'calculated_permissions', array((($id) ? $id : 1), 'upd', $gid)) ? '<span class="positive">'.$this->user->lang('allowed').'</span>' : '<span class="negative">'.$this->user->lang('disallowed').'</span>',
				'CALC_DELETE' 		=> $this->pdh->get('article_categories', 'calculated_permissions', array((($id) ? $id : 1), 'del', $gid)) ? '<span class="positive">'.$this->user->lang('allowed').'</span>' : '<span class="negative">'.$this->user->lang('disallowed').'</span>',
				'CALC_READ' 		=> $this->pdh->get('article_categories', 'calculated_permissions', array((($id) ? $id : 1), 'rea', $gid)) ? '<span class="positive">'.$this->user->lang('allowed').'</span>' : '<span class="negative">'.$this->user->lang('disallowed').'</span>',
				'CALC_CHANGE_STATE' => $this->pdh->get('article_categories', 'calculated_permissions', array((($id) ? $id : 1), 'chs', $gid)) ? '<span class="positive">'.$this->user->lang('allowed').'</span>' : '<span class="negative">'.$this->user->lang('disallowed').'</span>',
			));
		}
		
		
		$this->jquery->Tab_header('article_category-tabs');
		$this->jquery->Tab_header('category-permission-tabs');
		$editor = register('tinyMCE');
		$editor->editor_normal(array(
			'relative_urls'	=> false,
			'link_list'		=> true,
			'readmore'		=> false,
		));
		
		$arrCategoryIDs = $this->pdh->sort($this->pdh->get('article_categories', 'id_list', array()), 'article_categories', 'sort_id', 'asc');
		foreach($arrCategoryIDs as $cid){
			$arrCategories[$cid] = $this->pdh->get('article_categories', 'name_prefix', array($cid)).$this->pdh->get('article_categories', 'name', array($cid));
		}
		$arrAggregation = $arrCategories;
		unset($arrAggregation[0]);
		if ($id){
			unset($arrCategories[$id]);
			$this->tpl->assign_vars(array(
				'DESCRIPTION' =>  $this->pdh->get('article_categories', 'description', array($id)),
				'NAME' 		=> $this->pdh->get('article_categories', 'name', array($id)),
				'ALIAS'		=> $this->pdh->get('article_categories', 'alias', array($id)),
				'PER_PAGE'	=> $this->pdh->get('article_categories', 'per_page', array($id)),
				'DD_PORTAL_LAYOUT' => new hdropdown('portal_layout', array('options' => $arrPortalLayouts, 'value' => $this->pdh->get('article_categories', 'portal_layout', array($id)))),
				'R_PUBLISHED'	=> new hradio('published', array('value' => ($this->pdh->get('article_categories', 'published', array($id))))),	
				'DD_PARENT' => new hdropdown('parent', array('js' => 'onchange="renew_all_permissions();"', 'options' => $arrCategories, 'value' => $this->pdh->get('article_categories', 'parent', array($id)))),
				'DD_LIST_TYPE' => new hdropdown('list_type', array('options' => array(1 => $this->user->lang('list_type_full'), 2 => $this->user->lang('list_type_headline'), 3 => $this->user->lang('list_type_teaser')), 'value' => $this->pdh->get('article_categories', 'list_type', array($id)))),
				'R_SHOW_CHILDS' => new hradio('show_childs', array('value' => ($this->pdh->get('article_categories', 'show_childs', array($id))))),	
				'MS_AGGREGATION' => $this->jquery->MultiSelect('aggregation', $arrAggregation, $this->pdh->get('article_categories', 'aggregation', array($id))),
				'R_FEATURED_ONLY' => new hradio('featured_only', array('value' => ($this->pdh->get('article_categories', 'featured_only', array($id))))),

				'R_SHOW_SSB' => new hradio('show_ssb', array('value' => ($this->pdh->get('article_categories', 'social_share_buttons', array($id))))),
				'R_FEATURED_ONTOP' => new hradio('featured_ontop', array('value' => ($this->pdh->get('article_categories', 'featured_ontop', array($id))))),
				'R_HIDE_ON_RSS' => new hradio('hide_on_rss', array('value' => ($this->pdh->get('article_categories', 'hide_on_rss', array($id))))),
				'R_NOTIFY_UNPUBLISHED' => new hradio('notify_unpublished', array('value' => ($this->pdh->get('article_categories', 'notify_on_onpublished_articles', array($id))))),
				'R_HIDE_HEADER' => new hradio('hide_header', array('value' => ($this->pdh->get('article_categories', 'hide_header', array($id))))),
					
				'R_PUBLISHED_STATE' => new hradio('article_published_state', array('options' => array(0 => $this->user->lang('not_published'), 1 => $this->user->lang('published')), 'value' => $this->pdh->get('article_categories', 'article_published_state', array($id)))),
				'DD_SORTATION_TYPE' => new hdropdown('sortation_type', array('options' => $this->user->lang('sortation_types'), 'value' => $this->pdh->get('article_categories', 'sortation_type', array($id)))),
			));
			
		} else {
			
			$this->tpl->assign_vars(array(
				'PER_PAGE' => 25,
				'DD_PORTAL_LAYOUT' => new hdropdown('portal_layout', array('options' => $arrPortalLayouts, 'value' => 1)),
				'R_PUBLISHED'	=> new hradio('published', array('value' => 1)),
				'R_SHOW_CHILDS' => new hradio('show_childs', array('value' => 1)),
				'DD_PARENT' => new hdropdown('parent', array('js' => 'onchange="renew_all_permissions();"', 'options' => $arrCategories, 'value' => 0)),
				'DD_LIST_TYPE' => new hdropdown('list_type', array('options' => array(1 => $this->user->lang('list_type_full'), 2 => $this->user->lang('list_type_headline'), 3 => $this->user->lang('list_type_teaser')))),
				'MS_AGGREGATION' => $this->jquery->MultiSelect('aggregation', $arrAggregation, array()),
				'R_PUBLISHED_STATE' => new hradio('article_published_state', array('options' => array(0 => $this->user->lang('not_published'), 1 => $this->user->lang('published')), 'value' => 1)),
				'DD_SORTATION_TYPE' => new hdropdown('sortation_type', array('options' => array($this->user->lang('sortation_types')), 'value' => $this->pdh->get('article_categories', 'sortation_type', array($id)))),
				'R_FEATURED_ONLY' => new hradio('featured_only', array('value' => 0)),
				'R_SHOW_SSB' => new hradio('show_ssb', array('value' => 0)),
				'R_FEATURED_ONTOP' => new hradio('featured_ontop', array('value' => 0)),
				'R_HIDE_ON_RSS' => new hradio('hide_on_rss', array('value' => 0)),
				'R_NOTIFY_UNPUBLISHED' => new hradio('notify_unpublished', array('value' => 0)),
				'R_HIDE_HEADER' => new hradio('hide_header', array('value' => 0)),
			));
		}
		$this->tpl->assign_vars(array(
			'CID' => $id,
		));
		$this->core->set_vars(array(
			'page_title'		=> (($id) ? $this->user->lang('manage_article_categories').': '.$this->pdh->get('article_categories', 'name', array($id)) : $this->user->lang('add_article_category')),
			'template_file'		=> 'admin/manage_article_categories_edit.html',
			'display'			=> true)
		);
	}

	// ---------------------------------------------------------
	// Display form
	// ---------------------------------------------------------
	public function display() {
		$blnHasEditPermission = $this->user->check_auth('a_article_categories_man', false);
		
		if($blnHasEditPermission){
			$this->tpl->add_js("
				$(\"#article_categories-table tbody\").sortable({
					cancel: '.not-sortable, input, tr th.footer, th',
					cursor: 'pointer',
				});
			", "docready");
			
			$this->jquery->qtip('.articles-link', $this->user->lang('link_to_articles'));
		
			$view_list = $this->pdh->get('article_categories', 'id_list', array());
			$hptt_page_settings = $this->pdh->get_page_settings('admin_manage_article_categories', 'hptt_admin_manage_article_categories_categorylist');
			
			$hptt = $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%link_url%' => 'manage_article_categories.php', '%link_url_suffix%' => '&amp;upd=true'));
			$page_suffix = '&amp;start='.$this->in->get('start', 0);
			$sort_suffix = '?sort='.$this->in->get('sort');
			
			$item_count = count($view_list);
			
			$this->confirm_delete($this->user->lang('confirm_delete_article_category'));
	
			$this->tpl->assign_vars(array(
				'CATEGORY_LIST'		=> $hptt->get_html_table($this->in->get('sort'), $page_suffix,null,1,null,false, array('article_categories', 'checkbox_check')),
				'HPTT_COLUMN_COUNT'	=> $hptt->get_column_count())
			);
		} else {
			$this->jquery->qtip('.articles-link', $this->user->lang('link_to_articles'));
			
			$view_list = $this->pdh->get('article_categories', 'id_list', array());
			$hptt_page_settings = array(
				'name'				=> 'hptt_admin_manage_article_categories_categorylist',
				'table_main_sub'	=> '%category_id%',
				'table_subs'		=> array('%category_id%', '%article_id%'),
				'page_ref'			=> 'manage_article_categories.php',
				'show_numbers'		=> false,
				'show_select_boxes'	=> false,
				'selectboxes_checkall'=> false,
				'show_detail_twink'	=> false,
				'table_sort_dir'	=> 'asc',
				'table_sort_col'	=> 0,
				'table_presets'		=> array(
						array('name' => 'category_sortable',	'sort' => true, 'th_add' => 'width="20" class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
						array('name' => 'category_article_count','sort' => true, 'th_add' => 'width="20" class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
						array('name' => 'category_name',		'sort' => true, 'th_add' => '', 'td_add' => ''),
						array('name' => 'category_alias',		'sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
						array('name' => 'category_portallayout','sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
				),
			);
				
			$hptt = $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%link_url%' => 'manage_article_categories.php', '%link_url_suffix%' => '&amp;upd=true'), 'noperm');
			$page_suffix = '&amp;start='.$this->in->get('start', 0);
			$sort_suffix = '?sort='.$this->in->get('sort');
				
			$item_count = count($view_list);
			$this->tpl->assign_vars(array(
					'CATEGORY_LIST'		=> $hptt->get_html_table($this->in->get('sort'), $page_suffix,null,1,null,false, array('article_categories', 'checkbox_check')),
					'HPTT_COLUMN_COUNT'	=> $hptt->get_column_count(),
					'S_NO_PERMISSION'	=> true,
			)
			);
		}

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('manage_article_categories'),
			'template_file'		=> 'admin/manage_article_categories.html',
			'display'			=> true)
		);
	}
	
}
registry::register('Manage_Article_Categories');
?>