<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2016 EQdkp-Plus Developer Team
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
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');

class controller extends gen_class {
	public static $shortcuts = array('social' => 'socialplugins');

	public function __construct() {
		registry::fix_server_path();
		
		$blnCheckPost = $this->user->checkCsrfPostToken($this->in->get($this->user->csrfPostToken()));
		$blnCheckPostOld = $this->user->checkCsrfPostToken($this->in->get($this->user->csrfPostToken(true)));

		if ($this->in->exists('delete') && ($blnCheckPost || $blnCheckPostOld || $this->user->checkCsrfGetToken($this->in->get('link_hash'), get_class($this).'delete'))){
			$this->delete();
		}
		if ($this->in->exists('unpublish') && ($blnCheckPost || $blnCheckPostOld || $this->user->checkCsrfGetToken($this->in->get('link_hash'), get_class($this).'unpublish'))){
			$this->unpublish();
		}
		if ($this->in->exists('publish') && ($blnCheckPost || $blnCheckPostOld || $this->user->checkCsrfGetToken($this->in->get('link_hash'), get_class($this).'publish'))){
			$this->publish();
		}
		if ($this->in->exists('savevote')){
			$this->saveRating();
		}

		$this->display();
	}

	public function delete(){
		$intArticleID = $this->in->get('aid', 0);
		$intCategoryID = $this->pdh->get('articles','category', array($intArticleID));
		if ($intCategoryID){
			$arrPermissions = $this->pdh->get('article_categories', 'user_permissions', array($intCategoryID, $this->user->id));
			if ($arrPermissions && $arrPermissions['delete']){
				$strArticleTitle = $this->pdh->get('articles', 'title', array($intArticleID));
				$this->pdh->put('articles', 'delete', array($intArticleID));
				$this->core->message($strArticleTitle, $this->user->lang('del_suc'), 'green');

				$this->pdh->process_hook_queue();
			}
		}
	}

	public function unpublish(){
		$intArticleID = $this->in->get('aid', 0);
		$intCategoryID = $this->pdh->get('articles','category', array($intArticleID));
		if ($intCategoryID){
			$arrPermissions = $this->pdh->get('article_categories', 'user_permissions', array($intCategoryID, $this->user->id));
			if ($arrPermissions && $arrPermissions['change_state']){
				$strArticleTitle = $this->pdh->get('articles', 'title', array($intArticleID));
				$this->pdh->put('articles', 'set_unpublished', array(array($intArticleID)));

				$this->core->message($strArticleTitle, $this->user->lang('article_unpublish_success'), 'green');
				$this->pdh->process_hook_queue();
			}
		}
	}

	public function publish(){
		$intArticleID = $this->in->get('aid', 0);
		$intCategoryID = $this->pdh->get('articles','category', array($intArticleID));
		if ($intCategoryID){
			$arrPermissions = $this->pdh->get('article_categories', 'user_permissions', array($intCategoryID, $this->user->id));
			if ($arrPermissions && $arrPermissions['change_state']){
				$strArticleTitle = $this->pdh->get('articles', 'title', array($intArticleID));
				$this->pdh->put('articles', 'set_published', array(array($intArticleID)));

				$this->core->message($strArticleTitle, $this->user->lang('article_publish_success'), 'green');
				$this->pdh->process_hook_queue();
			}
		}
	}

	private function filterPathArray($arrPath){
		foreach($arrPath as $key => $val){
			if(defined('URL_COMPMODE') && strpos($val, "s=") === 0) {
				unset($arrPath[$key]);
				continue;
			}
			
			$arrPath[$key] = str_replace(array(".html", ".php", "?s=", "?"), "", utf8_strtolower($arrPath[$key]));
		}

		return $arrPath;
	}

	public function saveRating(){
		$this->pdh->put('articles', 'vote', array($this->in->get('name'), $this->in->get('score')));
		$this->pdh->process_hook_queue();
		die('done');
	}

	public function display(){
		$blnIsStartpage = false;
		$strPath = $this->env->path;
		$arrPath = array_filter(explode('/', $strPath));
		$arrPath = array_reverse($arrPath);
		$arrPath = $this->filterPathArray($arrPath);
		
		//Required, otherwise the Routing of Plugin URLS wont work.
		register('pm');

		if (count($arrPath) == 0 || str_ireplace('index.php', '', $strPath) === $this->config->get('server_path')){
			$blnIsStartpage = true;
			//Get Start Page
			if ($this->config->get('start_page') != ""){
				$strPath = str_ireplace('index.php', '', $this->config->get('start_page'));
			} else {
				$strPath = "news";
			}
			$arrPath = array_filter(explode('/', $strPath));
			$arrPath = array_reverse($arrPath);
			$arrPath = $this->filterPathArray($arrPath);
		}
		registry::add_const('patharray', $arrPath);
		$intArticleID = $intCategoryID = $strSpecificID = 0;

		//Search for static Routes first
		$strPageObject = false;
		foreach($arrPath as $intPathPart => $strPathPart){
			if (register('routing')->staticRoute($strPathPart)){
				if ($intPathPart == 0){

					$strPageObject = register('routing')->staticRoute($strPathPart);
					registry::add_const('page_path', $strPath);
					registry::add_const('page', $strPath);
				} else {

					//Static Page Object
					$strPageObject = register('routing')->staticRoute($arrPath[$intPathPart]);

					if ($strPageObject){
						//Zerlege .html
						$strID = str_replace("-", "", strrchr($arrPath[0], "-"));

						$arrMatches = array();
						preg_match_all('/[a-z]+|[0-9]+/', $strID, $arrMatches, PREG_PATTERN_ORDER);
						if (isset($arrMatches[0]) && count($arrMatches[0])){
							if (count($arrMatches[0]) == 2){
								if(is_numeric($arrMatches[0][1])) $arrMatches[0][1] = intval($arrMatches[0][1]);
								$this->in->inject($arrMatches[0][0], $arrMatches[0][1]);
							}
						}
						if (strlen($strID)) {
							if(is_numeric($strID)) $strID = intval($strID);
							registry::add_const('url_id', $strID);
						} elseif (strlen($arrPath[0])){
							registry::add_const('url_id', $arrPath[0]);
							$this->in->inject(utf8_strtolower($arrPath[0]), 'injected');
						}
						registry::add_const('page', str_replace('/'.$arrPath[0], '', $strPath));
						registry::add_const('page_path', $strPath);
						registry::add_const('speaking_name', str_replace('-'.$strID, '', $arrPath[0]));
						break;
					}
				}
			}
		}

		if ($strPageObject){
			registry::add_const('pageobject', $strPageObject);
			$this->core->set_vars('portal_layout', 1);
			$objPage = $this->routing->getPageObject($strPageObject);
			if ($objPage){
				$arrVars = $objPage->get_vars();
				$this->core->set_vars(array(
						'page_title'		=> $arrVars['page_title'],
						'template_file'		=> $arrVars['template_file'],
				        'description'		=> isset($arrVars['description']) ? $arrVars['description'] : "",
						'display'			=> true)
				);
			} else {
				redirect();
			}
		} else {
			//Search for Articles and Categories

			//Suche Alias in Artikeln
			$intArticleID = ($this->in->exists('a')) ? $this->in->get('a', 0) : $this->pdh->get('articles', 'resolve_alias', array($arrPath[0]));

			if (!$intArticleID){
				//Suche Alias in Kategorien
				$intCategoryID = ($this->in->exists('c')) ? $this->in->get('c', 0) : $this->pdh->get('article_categories', 'resolve_alias', array($arrPath[0]));
				//Is there an index-Article in this Category?
				if ($intCategoryID){
					$intIndexArticle = $this->pdh->get('article_categories', 'index_article', array($intCategoryID));
					if ($intIndexArticle) {
						$intArticleID = $intIndexArticle;
						$intCategoryID = false;
					}
				}

				//Suche in Artikeln mit nächstem Index, denn könnte ein dynamischer Systemartikel sein
				if (!$intArticleID && !$intCategoryID && isset($arrPath[1])) {

					$intArticleID = $this->pdh->get('articles', 'resolve_alias', array($arrPath[1]));
					if ($intArticleID){
						//Zerlege .html
						$strID = str_replace("-", "", strrchr($arrPath[0], "-"));
						$arrMatches = array();
						preg_match_all('/[a-z]+|[0-9]+/', $strID, $arrMatches, PREG_PATTERN_ORDER);
						if (isset($arrMatches[0]) && count($arrMatches[0])){
							if (count($arrMatches[0]) == 2){
								if(is_numeric($arrMatches[0][1])) $arrMatches[0][1] = intval($arrMatches[0][1]);
								$this->in->inject($arrMatches[0][0], $arrMatches[0][1]);
							}
						}
						if (strlen($strID)) {
							if(is_numeric($strID)) $strID = intval($strID);
							registry::add_const('url_id', $strID);
							$strSpecificID = $strID;
						} elseif (strlen($arrPath[0])){
							$this->in->inject(utf8_strtolower($arrPath[0]), 'injected');
							registry::add_const('url_id', $arrPath[0]);
							$strSpecificID = $arrPath[0];
						}
					} else {
						$intCategoryID = $this->pdh->get('article_categories', 'resolve_alias', array($arrPath[1]));
						if ($intCategoryID){
							$intIndexArticle = $this->pdh->get('article_categories', 'index_article', array($intCategoryID));
							if ($intIndexArticle) {
								$intArticleID = $intIndexArticle;
								$intCategoryID = false;

								//Zerlege .html
								$strID = str_replace("-", "", strrchr($arrPath[0], "-"));
								$arrMatches = array();
								preg_match_all('/[a-z]+|[0-9]+/', $strID, $arrMatches, PREG_PATTERN_ORDER);

								if (isset($arrMatches[0]) && count($arrMatches[0])){
									if (count($arrMatches[0]) == 2){
										if(is_numeric($arrMatches[0][1])) $arrMatches[0][1] = intval($arrMatches[0][1]);
										$this->in->inject($arrMatches[0][0], $arrMatches[0][1]);
									}
								}
								if (strlen($strID)) {
									if(is_numeric($strID)) $strID = intval($strID);
									registry::add_const('url_id', $strID);
									$strSpecificID = $strID;
								} elseif (strlen($arrPath[0])){
									$this->in->inject(utf8_strtolower($arrPath[0]), 'injected');
									registry::add_const('url_id', $arrPath[0]);
									$strSpecificID = $arrPath[0];
								}

							}
						}
					}

				}
			}

			//Display Article
			if ($intArticleID){
				register('article')->showArticle($intArticleID, $strSpecificID, $blnIsStartpage, $strPath, $arrPath);
			} elseif ($intCategoryID){
				//Display Category
				register('article')->showCategory($intCategoryID, $strSpecificID, $blnIsStartpage);
			}
		}

		if(!$this->env->is_ajax) message_die($this->user->lang('article_not_found'));
	}
	
}
registry::register('controller');
?>