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
	die('Do not access this file directly.');
}

if ( !class_exists( "pdh_r_article_categories" ) ) {
	class pdh_r_article_categories extends pdh_r_generic{

		public $default_lang = 'english';
		public $categories;
		public $sortation;
		private $arrTempPermissions = array();

		public $hooks = array(
			'article_categories_update',
			'article_updates',
		);
				
		public $presets = array(
			'category_sortable' => array('sort_id', array('%category_id%'), array()),
			'category_editicon' => array('editicon', array('%category_id%'), array()),
			'category_published' => array('published', array('%category_id%'), array()),
			'category_article_count' => array('article_count', array('%category_id%'), array()),
			'category_name' => array('name', array('%category_id%'), array()),
			'category_alias' => array('alias', array('%category_id%'), array()),
			'category_portallayout' => array('portal_layout', array('%category_id%'), array()),
		);

		public function reset(){
			$this->pdc->del('pdh_article_categories_table');
			$this->categories = NULL;
			$this->sortation = NULL;
			$this->alias = NULL;
		}

		public function init(){
			$this->categories	= $this->pdc->get('pdh_article_categories_table');
			$this->sortation	= $this->pdc->get('pdh_article_categories_sortation');
			$this->alias		= $this->pdc->get('pdh_article_categories_alias');
			if($this->categories !== NULL){
				return true;
			}
			
			$objQuery = $this->db->query("SELECT * FROM __article_categories ORDER BY sort_id ASC");
			if($objQuery){
				while($drow = $objQuery->fetchAssoc()){
					$this->categories[intval($drow['id'])] = array(
						'id'				=> intval($drow['id']),
						'name'				=> $drow['name'],
						'alias'				=> utf8_strtolower($drow['alias']),
						'description'		=> $drow['description'],
						'per_page'			=> intval($drow['per_page']),
						'permissions'		=> $drow['permissions'],
						'published'			=> intval($drow['published']),
						'parent'			=> intval($drow['parent']),
						'sort_id'			=> intval($drow['sort_id']),
						'list_type'			=> intval($drow['list_type']),
						'aggregation'		=> $drow['aggregation'],
						'featured_only'		=> intval($drow['featured_only']),
						'social_share_buttons'=> intval($drow['social_share_buttons']),
						'portal_layout'		=> intval($drow['portal_layout']),
						'show_childs'		=> intval($drow['show_childs']),
						'article_published_state' => intval($drow['article_published_state']),
						'notify_on_onpublished_articles' => intval($drow['notify_on_onpublished_articles']),
						'hide_header'		=> intval($drow['hide_header']),
						'sortation_type'	=> intval($drow['sortation_type']),
						'featured_ontop'	=> intval($drow['featured_ontop']),
						'hide_on_rss'		=> intval($drow['hide_on_rss']),
					);
					$this->alias[utf8_strtolower($drow['alias'])] = intval($drow['id']);
				}
				
				$this->sortation = $this->get_sortation();
				
				$this->pdc->put('pdh_article_categories_table', $this->categories, null);
				$this->pdc->put('pdh_article_categories_sortation', $this->sortation, null);
				$this->pdc->put('pdh_article_categories_alias', $this->alias, null);
			}
	
		}

		public function get_id_list($blnPublishedOnly = false) {
			if ($this->categories == NULL) return array();
			
			if ($blnPublishedOnly){
				$arrOut = array();
				foreach($this->categories as $intCategoryID => $arrCat){
					if ($this->get_published($intCategoryID)) $arrOut[] = $intCategoryID;
				}
				return $arrOut;
			} else return array_keys($this->categories);
		}
		
		//Get all published article IDs
		public function get_published_id_list($intCategoryID, $intUserID = false, $forRSS=false, $blnFeaturedOnly=NULL, $isAdmin=false){
			if($blnFeaturedOnly === NULL) $blnFeaturedOnly = $this->get_featured_only($intCategoryID);
			if (!$this->get_published($intCategoryID)) return array();
			if ($intUserID === false) $intUserID = $this->user->id;
			$arrOut = array();
			//Get articles from all aggregation categorys
			$arrAggregation = $this->get_aggregation($intCategoryID);
			if (is_array($arrAggregation) && count($arrAggregation) > 0){
				foreach($arrAggregation as $intCatID){
					//Check published cat
					if (!$this->get_published($intCatID)) continue;
					//Check if category should be hidden on RSS
					if($forRSS && $this->get_hide_on_rss($intCatID)) continue;
					
					//Check cat permission
					$arrPermissions = $this->get_user_permissions($intCatID, $intUserID);
					if (!$arrPermissions['read']) continue;
					
					//Foreach Article
					$arrArticles = $this->pdh->get('articles', 'id_list', array($intCatID));
					foreach($arrArticles as $intArticleID){
						//Check published
						if (!$this->pdh->get('articles', 'published', array($intArticleID)) && !$isAdmin) continue;
						//Check featured
						if ($blnFeaturedOnly && !$this->pdh->get('articles', 'featured', array($intArticleID))) continue;
						//Check start from/to
						if (($this->pdh->get('articles', 'show_from', array($intArticleID)) != "" && $this->pdh->get('articles', 'show_from', array($intArticleID)) > $this->time->time) || ($this->pdh->get('articles', 'show_to', array($intArticleID)) != "" && $this->pdh->get('articles', 'show_to', array($intArticleID)) < $this->time->time)) continue;
						
						$arrOut[] = $intArticleID;
					}	
				}
			}
			$arrOut = array_unique($arrOut);
			
			return $arrOut;
		}
		
		public function get_data($intCategoryID){
			if (isset($this->categories[$intCategoryID])){
				return $this->categories[$intCategoryID];
			}
			return false;
		}

		public function get_name($intCategoryID){
			if (isset($this->categories[$intCategoryID])){
				return $this->categories[$intCategoryID]['name'];
			}
			return false;
		}
		
		public function get_alias($intCategoryID){
			if (isset($this->categories[$intCategoryID])){
				return $this->categories[$intCategoryID]['alias'];
			}
			return false;
		}
		
		public function get_description($intCategoryID){
			if (isset($this->categories[$intCategoryID])){
				return $this->categories[$intCategoryID]['description'];
			}
			return false;
		}
		
		public function get_per_page($intCategoryID){
			if (isset($this->categories[$intCategoryID])){
				return $this->categories[$intCategoryID]['per_page'];
			}
			return false;
		}
		
		public function get_permissions($intCategoryID){
			if (isset($this->categories[$intCategoryID])){
				return unserialize($this->categories[$intCategoryID]['permissions']);
			}
			return false;
		}
		
		public function get_published($intCategoryID){
			if (isset($this->categories[$intCategoryID])){
				return $this->categories[$intCategoryID]['published'];
			}
			return false;
		}
		
		public function get_parent($intCategoryID){
			if (isset($this->categories[$intCategoryID])){
				return $this->categories[$intCategoryID]['parent'];
			}
			return false;
		}
		
		public function get_sort_id($intCategoryID){
			if (isset($this->categories[$intCategoryID])){
				return $this->sortation[$intCategoryID];
				//return $this->categories[$intCategoryID]['sort_id'];
			}
			return false;
		}
		
		public function get_html_sort_id($intCategoryID){
			return '<span class="ui-icon ui-icon-arrowthick-2-n-s" title="'.$this->user->lang('dragndrop').'"></span><input type="hidden" name="sortCategories[]" value="'.$intCategoryID.'"/>';
		}
		
		public function get_list_type($intCategoryID){
			if (isset($this->categories[$intCategoryID])){
				return $this->categories[$intCategoryID]['list_type'];
			}
			return false;
		}
		
		public function get_aggregation($intCategoryID){
			if (isset($this->categories[$intCategoryID])){
				return unserialize($this->categories[$intCategoryID]['aggregation']);
			}
			return false;
		}
		
		public function get_featured_only($intCategoryID){
			if (isset($this->categories[$intCategoryID])){
				return $this->categories[$intCategoryID]['featured_only'];
			}
			return false;
		}
		
		public function get_social_share_buttons($intCategoryID){
			if (isset($this->categories[$intCategoryID])){
				return $this->categories[$intCategoryID]['social_share_buttons'];
			}
			return false;
		}
		
		public function get_portal_layout($intCategoryID){
			if (isset($this->categories[$intCategoryID])){
				return $this->categories[$intCategoryID]['portal_layout'];
			}
			return false;
		}
		
		public function get_html_portal_layout($intCategoryID){
			if ($this->get_portal_layout($intCategoryID)) {
				return $this->pdh->get('portal_layouts', 'name', array($this->get_portal_layout($intCategoryID)));
			} else return '';
		}
		
		public function get_used_portallayout_number($intPortalLayoutID){
			$intCount = 0;
			$intPortalLayoutID = intval($intPortalLayoutID);
			foreach($this->categories as $key => $value){
				if ($value['portal_layout'] === $intPortalLayoutID) $intCount++;
			}
			return $intCount;
		}
		
		public function get_show_childs($intCategoryID){
			if (isset($this->categories[$intCategoryID])){
				return $this->categories[$intCategoryID]['show_childs'];
			}
			return false;
		}
		
		public function get_article_published_state($intCategoryID){
			if (isset($this->categories[$intCategoryID])){
				return $this->categories[$intCategoryID]['article_published_state'];
			}
			return false;
		}
		
		public function get_notify_on_onpublished_articles($intCategoryID){
			if (isset($this->categories[$intCategoryID])){
				return $this->categories[$intCategoryID]['notify_on_onpublished_articles'];
			}
			return false;
		}
		
		public function get_hide_header($intCategoryID){
			if (isset($this->categories[$intCategoryID])){
				return $this->categories[$intCategoryID]['hide_header'];
			}
			return false;
		}
		
		public function get_hide_on_rss($intCategoryID){
			if (isset($this->categories[$intCategoryID])){
				return $this->categories[$intCategoryID]['hide_on_rss'];
			}
			return false;
		}
		
		public function get_sortation_type($intCategoryID){
			if (isset($this->categories[$intCategoryID])){
				return $this->categories[$intCategoryID]['sortation_type'];
			}
			return false;
		}
		
		public function get_featured_ontop($intCategoryID){
			if (isset($this->categories[$intCategoryID])){
				return $this->categories[$intCategoryID]['featured_ontop'];
			}
			return false;
		}
				
		public function get_editicon($intCategoryID){
			return '<a href="'.$this->root_path.'admin/manage_article_categories.php'.$this->SID.'&c='.$intCategoryID.'"><i class="fa fa-pencil fa-lg" title="'.$this->user->lang('edit').'"></i></a>';
		}
		
		public function get_html_published($intCategoryID){
			if ($this->get_published($intCategoryID)){
				$strImage = '<div><div class="eye eyeToggleTrigger"></div><input type="hidden" class="published_cb" name="published['.$intCategoryID.']" value="1"/></div>';
			} else {
				$strImage = '<div><div class="eye-gray eyeToggleTrigger"></div><input type="hidden" class="published_cb" name="published['.$intCategoryID.']" value="0"/></div>';
			}
			
			if ($intCategoryID == 1) $strImage = '<div><div class="eye"></div><input type="hidden" class="published_cb" name="published['.$intCategoryID.']" value="1"/></div>';
			return $strImage;
		}
		
		public function get_article_count($intCategoryID){
			$arrArticles = $this->pdh->get('articles', 'id_list', array($intCategoryID));
			if (is_array($arrArticles)) return count($arrArticles);
			return 0;
		}
		
		public function get_html_article_count($intCategoryID){
			return '<a href="'.$this->root_path.'admin/manage_articles.php'.$this->SID.'&c='.$intCategoryID.'">'.$this->get_article_count($intCategoryID).'</a>';
		}
		
		public function get_html_name($intCategoryID){
			return $this->get_name_prefix($intCategoryID).'<a href="'.$this->root_path.'admin/manage_articles.php'.$this->SID.'&c='.$intCategoryID.'" class="articles-link">'.$this->get_name($intCategoryID).'</a>';
		}
		
		public function get_check_alias($strAlias, $blnCheckArticles=false){
			$strAlias = utf8_strtolower($strAlias);
		
			foreach ($this->categories as $key => $val){
				if ($this->get_alias($key) == $strAlias) return false;
			}
			
			//Check static routes
			$arrRoutes = register('routing')->getRoutes();
			if (isset($arrRoutes[$strAlias])) return false;
			
			//No Category uses this alias, check articles
			if ($blnCheckArticles){
				$blnResult = $this->pdh->get('articles', 'check_alias', array($strAlias, false));
				return $blnResult;
			}
			return true;
		}
		
		public function get_calculated_permissions($intCategoryID, $strPermission, $intUsergroupID, $myPermission=false, $intParentID=false, $intCall = 0){
			$arrPermissions = $this->get_permissions($intCategoryID);
			$myPermission = ($myPermission !== false && $intCall == 0) ? $myPermission : ((isset($arrPermissions[$strPermission][$intUsergroupID])) ? $arrPermissions[$strPermission][$intUsergroupID] : ((isset($arrPermissions[$strPermission][1])) ? $arrPermissions[$strPermission][1] : -1));
			
			if ($strPermission == 'rea'){
				switch($myPermission){
					case -1:
					case 1: 
							//Do we have a parent?
							$result = $myPermission;
							if ($intCategoryID == 0){
								$result = $this->get_calculated_permissions($intParentID, $strPermission, $intUsergroupID, $myPermission,  false, $intCall+1);
							} else {
								if ($intParentID !== false){
									$result = $this->get_calculated_permissions($intParentID, $strPermission, $intUsergroupID, $myPermission,  false, $intCall+1);
								} else {
									if ($this->get_parent($intCategoryID)) $result = $this->get_calculated_permissions($this->get_parent($intCategoryID), $strPermission, $intUsergroupID, $myPermission,  false, $intCall+1);
								}
							}
							if($intCall != 0) return $result;
							if ($result == -1){
								switch($myPermission){
									case 0:
									case -1: return 0;
									case 1: return 1;
								}
							}
							return $result;
					break;
					default: return 0;
				}
				
				
			} else {
				switch($myPermission){
					case 0:
					case 1: return $myPermission;
					case -1: //Do we have a parent?
							$result = $myPermission;
							if ($intCategoryID == 0){
								$result = $this->get_calculated_permissions($intParentID, $strPermission, $intUsergroupID, $myPermission, false, $intCall+1);
							} else {
								if ($intParentID !== false){
									$result = $this->get_calculated_permissions($intParentID, $strPermission, $intUsergroupID, $myPermission, false, $intCall+1);			
								} else {
									if ($this->get_parent($intCategoryID)) $result = $this->get_calculated_permissions($this->get_parent($intCategoryID), $strPermission, $intUsergroupID, $myPermission, false, $intCall+1);
								}
							}
							if($intCall != 0) return $result;
							if ($result == -1) return 0;
							return $result;
				}
				
			}
			return 0;
		}
		
		public function get_sortation(){
			$myChildArray = array();
			$myRootArray  = array();
			foreach($this->categories as $key => $val){
				if ($val['parent']) {
					$myChildArray[$val['parent']][] = $key;
				} else {
					$myRootArray[$key] = $key;
				}
			}
			
			$outArray = array();
			foreach($myRootArray as $key => $val){
				$outArray[] = $key;
				$this->add_array($key, $outArray, $myChildArray);
			}
			
			return array_flip($outArray);
		}
		
		public function get_all_childs($intCategoryID){
			$arrCategories = $this->get_sortation();
			$arrOut = array();
			$blnStart = false;
			$myPreset = false;
			foreach($arrCategories as $catid => $key){
				if ($catid == $intCategoryID) {
					$blnStart = true;
					$myPreset = $this->get_name_prefix($catid);
					$arrOut[] = $catid;
				} elseif ($blnStart){
					if ($myPreset === $this->get_name_prefix($catid)) break;
					$arrOut[] = $catid;
				}				
			}
			
			return $arrOut;
		}
		
		public function add_array($key, &$arrOut, $arrChildArray){
			if (isset($arrChildArray[$key])){
				foreach($arrChildArray[$key] as $val){
					$arrOut[] = $val;
					$this->add_array($val, $arrOut, $arrChildArray);
				}
			}
		}
		
		public function get_parent_count($intCategoryID, $intCount=0){
			if ($this->get_parent($intCategoryID)){
				$intCount = $this->get_parent_count($this->get_parent($intCategoryID), $intCount+1);
			}
			return $intCount;
		}
		
		public function get_name_prefix($intCategoryID){
			$intParentCount = $this->get_parent_count($intCategoryID);
			$strOut = '';
			for($i=0; $i < $intParentCount; $i++){
				$strOut .= '-- ';
			}
			return $strOut;
		}
		
		public function get_resolve_alias($strAlias){
			$strAlias = utf8_strtolower($strAlias);
			
			if (isset($this->alias[$strAlias])){
				return $this->alias[$strAlias];
			}
			return false;
		}
		
		public function get_path($intCategoryID){			
			$strPath = "";
			$strPath = $this->add_path($intCategoryID);
			
			if(substr($strPath, -1) == "/") $strPath = substr($strPath, 0, -1);
			$strPath .= $this->routing->getSeoExtension();
			
			return $strPath.(($this->SID == "?s=") ? '?' : $this->SID);
		}
		
		public function get_permalink($intCategoryID){
			return $this->env->link.'index.php?c='.(int)$intCategoryID;
		}
		
		private function add_path($intCategoryID, $strPath=''){
			$strAlias = ucfirst($this->get_alias($intCategoryID));
			if ($strAlias != '' && $strAlias != 'system' && $strAlias != 'System'){
				$strPath = $strAlias.'/'.$strPath;
			}
			if ($this->get_parent($intCategoryID)){
				$strPath = $this->add_path($this->get_parent($intCategoryID), $strPath);
			}
			
			return $strPath;
		}
		
		public function get_user_permissions($intCategoryID, $intUserID){
			$arrUsergroupMemberships = $this->acl->get_user_group_memberships($intUserID);
			
			if (isset($this->arrTempPermissions[$intCategoryID]) && isset($this->arrTempPermissions[$intCategoryID][$intUserID])){
				return $this->arrTempPermissions[$intCategoryID][$intUserID];
			} else {			
				$arrPermissions = array('read' => false, 'create' => false, 'delete' => false, 'update' => false, 'change_state' => false);
				foreach($arrUsergroupMemberships as $intGroupID => $intStatus){
					$blnReadPerm = $this->pdh->get('article_categories', 'calculated_permissions', array($intCategoryID, 'rea', $intGroupID));
					if ($blnReadPerm) $arrPermissions['read'] = true;
					$blnCreatePerm = $this->pdh->get('article_categories', 'calculated_permissions', array($intCategoryID, 'cre', $intGroupID));
					if ($blnCreatePerm) $arrPermissions['create'] = true;
					$blnUpdatePerm = $this->pdh->get('article_categories', 'calculated_permissions', array($intCategoryID, 'upd', $intGroupID));
					if ($blnUpdatePerm) $arrPermissions['update'] = true;
					$blnDeletePerm = $this->pdh->get('article_categories', 'calculated_permissions', array($intCategoryID, 'del', $intGroupID));
					if ($blnDeletePerm) $arrPermissions['delete'] = true;
					$blnChangeStatePerm = $this->pdh->get('article_categories', 'calculated_permissions', array($intCategoryID, 'chs', $intGroupID));
					if ($blnChangeStatePerm) $arrPermissions['change_state'] = true;		
				}
				$this->arrTempPermissions[$intCategoryID][$intUserID] = $arrPermissions;
				return $arrPermissions;
			}
		}
		
		public function get_breadcrumb($intCategoryID){
			if ($intCategoryID == 1) return "";
			$strBreadcrumb = ($this->get_parent($intCategoryID)) ? $this->add_breadcrumb($this->get_parent($intCategoryID)) : '';

			$strBreadcrumb .=  '<li class="current"><a href="'.$this->controller_path.$this->get_path($intCategoryID).'">'.$this->get_name($intCategoryID).'</a></li>';
			return $strBreadcrumb;
		}
		
		private function add_breadcrumb($intCategoryID, $strBreadcrumb=''){
			if ($intCategoryID == 1) return $strBreadcrumb;
			$strName = $this->pdh->get('article_categories', 'name', array($intCategoryID));
			$strPath = $this->pdh->get('article_categories', 'path', array($intCategoryID));
			$strBreadcrumb = '<li><a href="'.$this->controller_path.$strPath.'">'.$strName.'</a></li>'.$strBreadcrumb;
			
			if ($this->pdh->get('article_categories', 'parent', array($intCategoryID))){
				$strBreadcrumb = $this->add_breadcrumb($this->pdh->get('article_categories', 'parent', array($intCategoryID)), $strBreadcrumb);
			}
			
			return $strBreadcrumb;
		}
		
		public function get_childs($intCategoryID){
			$arrChilds = array();
			foreach($this->categories as $catID => $val){
				if ($this->get_parent($catID) === $intCategoryID){
					$arrChilds[] = $catID;
				}
			}
			return $arrChilds;
		}
		
		public function get_index_article($intCategoryID){
			$arrArticles = $this->pdh->get('articles', 'id_list', array($intCategoryID));
			foreach($arrArticles as $intArticleID){
				if ($this->pdh->get('articles', 'published', array($intArticleID))){
					if($this->pdh->get('articles', 'index', array($intArticleID))) return $intArticleID;
				}
			}
			return false;
		}
		
		public function get_unpublished_articles_notify(){
			$arrOut = array();
			foreach($this->categories as $intCategoryID => $val){
				if (!$val['notify_on_onpublished_articles']) continue;
				$arrArticleIDs = $this->pdh->get('articles', 'id_list', array($intCategoryID));
				foreach($arrArticleIDs as $intArticleID){
					if (!$this->pdh->get('articles', 'published', array($intArticleID))){
						if (!isset($arrOut[$intCategoryID])) $arrOut[$intCategoryID] = 0;
						$arrOut[$intCategoryID]++;
					}
				}
			}
			return $arrOut;
		}
		
		public function get_checkbox_check($intCategoryID){
			if ($intCategoryID == 1) return false;
			return true;
		}
		
		public function get_search($search_value) {
			$arrSearchResults = array();
			if (is_array($this->categories)){
				foreach($this->categories as $id => $value) {
					if (!$this->get_published($id)) continue;			
					$arrPermissions = $this->get_user_permissions($id, $this->user->id);
					if(!$arrPermissions['read']) continue;
				
					if(stripos($this->get_name($id), $search_value) !== false OR stripos($this->get_description($id), $search_value) !== false ) {

						$arrSearchResults[] = array(
							'id'	=> $this->get_article_count($id),
							'name'	=> $this->get_name($id),
							'link'	=> $this->controller_path.$this->get_path($id),
						);
					}
				}
			}
			return $arrSearchResults;
		}

	}//end class
}//end if
?>