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

if(!class_exists('admin_functions')) {
class admin_functions extends gen_class {
	public static $shortcuts = array('puf' => 'urlfetcher');

	public function resolve_ip($strIP){
		$out = false;
		if(strlen($strIP)){
			$return = $this->puf->fetch("http://www.geoplugin.net/php.gp?ip=".$this->in->get('ip_resolve'));
			if ($return){
				$unserialized = @unserialize($return);
				if ($unserialized){
					$out = array(
						'city' 			=> $unserialized['geoplugin_city'],
						'regionName'	=> $unserialized['geoplugin_regionName'],
						'countryName'	=> $unserialized['geoplugin_countryName'],
					);

					if (!strlen($out['countryName'])) $out = false; 
				}
			}
		}
		
		return $out;
	}
	
	/**
	 * Resolve the User Browser
	 *
	 * @param string $member
	 * @return string
	 */
	function resolve_browser($string){
		$string = sanitize($string);
		if( preg_match("/opera/i",$string)){
			return "<div class=\"coretip-left browser-icon opera\" data-coretip=\"".$string."\">".inline_svg($this->root_path."images/browser/opera.svg")."</div>";
		}else if( preg_match("/msie/i",$string)){
			return "<div class=\"coretip-left browser-icon ie\" data-coretip=\"".$string."\">".inline_svg($this->root_path."images/browser/ie.svg")."</div>";	
		}else if( preg_match("/chrome/i", $string)){
			return "<div class=\"coretip-left browser-icon chrome\" data-coretip=\"".$string."\">".inline_svg($this->root_path."images/browser/chrome.svg")."</div>";
		}else if( preg_match("/konqueror/i",$string)){
			return "<div class=\"coretip-left browser-icon konqueror\" data-coretip=\"".$string."\">".inline_svg($this->root_path."images/browser/konqueror.svg")."</div>";
		}else if( preg_match("/safari/i",$string) ){
			return "<div class=\"coretip-left browser-icon safari\" data-coretip=\"".$string."\">".inline_svg($this->root_path."images/browser/safari.svg")."</div>";
		}else if( preg_match("/lynx/i",$string) ){
			return "<span class=\"coretip-left\" data-coretip=\"".$string."\">Lynx</span>";
		}else if( preg_match("/mozilla/i",$string) ){
			return "<div class=\"coretip-left browser-icon firefox\" data-coretip=\"".$string."\">".inline_svg($this->root_path."images/browser/firefox.svg")."</div>";
		}else if( preg_match("/w3m/i",$string) ){
			return "<span class=\"coretip-left\" data-coretip=\"".$string."\">w3m</span>";
		}else{
			return "<i class=\"fa fa-question-circle fa-lg fa-fw coretip-left\" data-coretip=\"".$string."\"></i>";
		}
	}
	
	/**
	 * Resolve the EQDKP Page the user is surfing on..
	 *
	 * @param string $member
	 * @return string
	 */
	function resolve_eqdkp_page($strPage){

		$matches = explode('&', $strPage);
		$strPath = $matches[0];

		if (strlen($strPath)){
			$strQuery = (isset($matches[1])) ? $matches[1] : "";
			$arrQuery = array();
			parse_str($strQuery, $arrQuery);
			$arrFolder = explode('/', $strPath);
			$strOut = "";
			
			//Prefixes for Admin, Plugins, Maintenance
			switch($arrFolder[0]){
				case 'admin' : $strPrefix = registry::fetch('user')->lang('menu_admin_panel').': ';
				break;
				case 'plugins' : $strPrefix = registry::fetch('user')->lang('pi_title').': '; $strOut = ((registry::fetch('user')->lang($arrFolder[1])) ? registry::fetch('user')->lang($arrFolder[1]) : ucfirst($arrFolder[1]));
				break;
				case 'maintenance' : $strPrefix = registry::fetch('user')->lang('maintenance'); $strOut = " ";
				break;
				case 'portal': $strPrefix = registry::fetch('user')->lang('portal').': '; $strOut = ((registry::fetch('user')->lang($arrFolder[1])) ? registry::fetch('user')->lang($arrFolder[1]) : ucfirst($arrFolder[1]));
				break;
				default: $strPrefix = '';
			}
			
			//Resolve Admin Pages
			if ($arrFolder[0] == "admin"){
				//First, some admin pages without menu entry
				switch($strPath){
					case 'admin/info_php':
						$strOut = '<a href="'.$this->root_path.'admin/info_php.php'.$this->SID.'">PHP-Info</a>';
					break;
					
					case 'admin/manage_articles':
						$strOut = '<a href="'.$this->root_path.'admin/manage_articles.php'.$this->SID.'&amp;'.$strQuery.'">'.$this->user->lang('manage_articles').'</a>';
					break;
					
					case 'admin/manage_styles':
						$strOut = '<a href="'.$this->root_path.'admin/manage_styles.php'.$this->SID.'&amp;'.$strQuery.'">'.$this->user->lang('styles_title').'</a>';
					break;
					
					case 'admin':
					case 'admin/index':
						$strOut = registry::fetch('user')->lang('menu_admin_panel');
						$strPrefix = "";
					break;
				}
				
				//Now check if there is an menu entry
				if($strOut == ""){
					$admin_menu = $this->adminmenu(false);
					$result = search_in_array($strPath.".php".$this->SID, $admin_menu);
					if ($result){
						$arrMenuEntry = arraykey_for_array($result, $admin_menu);
						if ($arrMenuEntry) $strOut = '<a href="'.$this->root_path.$arrMenuEntry['link'].'">'.$arrMenuEntry['text'].'</a>';
					}				
				}
			}
			
			//Resolve Frontend Page
			if ($strOut == "" && $strPrefix == ""){
				$intArticleID = $intCategoryID = 0;
				
				$arrPath = array_reverse($arrFolder);

				//Suche Alias in Artikeln
				$intArticleID = $this->pdh->get('articles', 'resolve_alias', array(str_replace(".html", "", utf8_strtolower($arrPath[0]))));
				if (!$intArticleID){
					//Suche Alias in Kategorien
					$intCategoryID = $this->pdh->get('article_categories', 'resolve_alias', array(str_replace(".html", "", utf8_strtolower($arrPath[0]))));
					
					//Suche in Artikeln mit nächstem Index, denn könnte ein dynamischer Systemartikel sein
					if (!$intCategoryID && isset($arrPath[1])) {					
						$intArticleID = $this->pdh->get('articles', 'resolve_alias', array(str_replace(".html", "", utf8_strtolower($arrPath[1]))));
					}
				}

				if ($intArticleID){
					$strOut = $this->user->lang('article').': <a href="'.$this->controller_path.$this->pdh->get('articles', 'path', array($intArticleID)).'">'.$this->pdh->get('articles', 'title', array($intArticleID)).'</a>';
				} elseif($intCategoryID) {
					$strOut = $this->user->lang('category').': <a href="'.$this->server_path.$this->pdh->get('article_categories', 'path', array($intCategoryID)).'">'.$this->pdh->get('article_categories', 'name', array($intCategoryID)).'</a>';
				} elseif (register('routing')->staticRoute($arrPath[0]) || register('routing')->staticRoute($arrPath[1])) {
					$strPageObject = register('routing')->staticRoute($arrPath[0]);
					if (!$strPageObject) {			
						$strPageObject = register('routing')->staticRoute($arrPath[1]);
					}
					
					if ($strPageObject){

						$strID = str_replace("-", "", strrchr(str_replace(".html", "", $arrPath[0]), "-"));
						$arrMatches = array();
						$myVar = false;
						preg_match_all('/[a-z]+|[0-9]+/', $strID, $arrMatches, PREG_PATTERN_ORDER);
						if (isset($arrMatches[0]) && count($arrMatches[0])){
							if (count($arrMatches[0]) == 2){
								$myVar = $arrMatches[0][1];
							}
						}
						if (strlen($strID) && count($arrMatches[0]) != 2) $myVar = $strID;
						
						switch($strPageObject){
							case 'settings': $strOut = registry::fetch('user')->lang('settings_title');
								break;
							case 'login': $strOut = registry::fetch('user')->lang('login_title');
								break;
							case 'mycharacters': $strOut = registry::fetch('user')->lang('manage_members_titl');
								break;
							case 'search': $strOut = registry::fetch('user')->lang('search');
								break;
							case 'register': $strOut = registry::fetch('user')->lang('register_title');
								break;
							//TODO Add Title
							case 'addcharacter': $strOut = '';
								break;
							case 'editarticle': $strOut = $this->user->lang('manage_articles');
								if (isset($arrQuery['aid']) && $arrQuery['aid']) $strOut .= ': <a href="'.$this->controller_path.$this->pdh->get('articles', 'path', array($arrQuery['aid'])).'">'.$this->pdh->get('articles', 'title', array($arrQuery['aid'])).'</a>';
								break;
							case 'user': $strOut = $this->user->lang('user');
								if ($myVar) $strOut .= ': <a href="'.$this->server_path.sanitize($strPage).'">'.$this->pdh->get('user', 'name', array($myVar)).'</a>';
								break;
							case 'usergroup': $strOut = $this->user->lang('usergroup');
								if ($myVar) $strOut .= ': <a href="'.$this->server_path.sanitize($strPage).'">'.$this->pdh->get('user_groups', 'name', array((int)$myVar)).'</a>';
								break;
							case 'rss': $strOut = "RSS";
								break;
							case 'wrapper': {
								if($arrFolder[1] == "board" || $arrFolder[1] == "boardregister" || $arrFolder[1] == "lostpassword") {
									$strOut = '<a href="'.$this->routing->build('External', 'Board').'">'.$this->user->lang('forum').'</a>';
								} elseif($myVar) {
									$strOut = $this->user->lang('viewing_wrapper').': <a href="'.$this->routing->build('External', $this->pdh->get('links', 'name', array(intval($myVar))), intval($myVar)).'">'.$this->pdh->get('links', 'name', array(intval($myVar))).'</a>';
								} else {
									$strOut = $this->user->lang('viewing_wrapper');
								}								
							}
								break;
							case 'tag': $strOut .= $this->user->lang('tag').': <a href="'.$this->routing->build('tag', sanitize($arrFolder[1])).'">'.sanitize($arrFolder[1]).'</a>';
								break;
						}
					}		
				} else {
					//Some special frontend pages
					switch($strPath){
						case "api":
						case "exchange": $strOut = registry::fetch('user')->lang('viewing_exchange');
						break;
					}
				}
				
			}
		}
		
		if ($strOut == '') $strOut = '<span style="font-style:italic;">'.$this->user->lang('unknown').'</span>';
		return $strPrefix.$strOut;
	}
	
	public function adminmenu($blnShowBadges = true, $coreUpdates="", $extensionUpdates=""){
		$admin_menu = array(
			'members' => array(
				'icon'	=> 'fa-user fa-lg fa-fw',
				'name'	=> $this->user->lang('chars'),
				1		=> array('link' => 'admin/manage_members.php'.$this->SID,			'text' => $this->user->lang('manage_members'),	'check' => 'a_members_man',	'icon'	=> 'fa-user fa-lg fa-fw'),
				2		=> array('link' => 'admin/manage_items.php'.$this->SID,			'text' => $this->user->lang('manitems_title'),	'check' => 'a_item_',		'icon' => 'fa-gift fa-lg fa-fw'),
				3		=> array('link' => 'admin/manage_adjustments.php'.$this->SID,		'text' => $this->user->lang('manadjs_title'),		'check' => 'a_indivadj_',	'icon' => 'fa-tag fa-lg fa-fw'),
				4		=> array('link' => 'admin/manage_ranks.php'.$this->SID,			'text' => $this->user->lang('manrank_title'),		'check' => 'a_members_man',	'icon' => 'fa-flag fa-lg fa-fw'),
				5		=> array('link' => 'admin/manage_profilefields.php'.$this->SID,	'text' => $this->user->lang('manage_pf_menue'),	'check' => 'a_config_man',	'icon' => 'fa-sitemap fa-lg fa-fw'),
				6		=> array('link' => 'admin/manage_roles.php'.$this->SID,			'text' => $this->user->lang('rolemanager'),		'check' => 'a_config_man',	'icon' => 'fa-beer fa-lg fa-fw'),
				7		=> array('link' => 'admin/manage_auto_points.php'.$this->SID,		'text' => $this->user->lang('manage_auto_points'),'check' => 'a_config_man',	'icon' => 'fa-magic fa-lg fa-fw'),
			),
			'users' => array(
				'icon'	=> 'fa-group fa-lg fa-fw',
				'name'	=> $this->user->lang('users'),
				1		=> array('link' => 'admin/manage_users.php'.$this->SID,			'text' => $this->user->lang('manage_users'),		'check' => 'a_users_man',	'icon' => 'fa-user fa-lg fa-fw'),
				2		=> array('link' => 'admin/manage_user_groups.php'.$this->SID,		'text' => $this->user->lang('manage_user_groups'),'check' => array('OR', array('a_usergroups_man', 'a_usergroups_grpleader')),	'icon' => 'fa-group fa-lg fa-fw'),
				3		=> array('link' => 'admin/manage_user_profilefields.php'.$this->SID,	'text' => $this->user->lang('manage_userpf'),	'check' => 'a_users_profilefields',	'icon' => 'fa-sitemap fa-lg fa-fw'),
				4		=> array('link' => 'admin/manage_maintenance_user.php'.$this->SID,'text' => $this->user->lang('maintenanceuser_user'),'check' => 'a_maintenance','icon' => 'fa-user-md fa-lg fa-fw'),
				5		=> array('link' => 'admin/manage_massmail.php'.$this->SID,'text' => $this->user->lang('massmail'),'check' => 'a_users_massmail','icon' => 'fa fa-envelope fa-lg fa-fw'),
			),
			'extensions' => array(
				'name'	=> $this->user->lang('extensions').(($blnShowBadges) ? $extensionUpdates : ''),
				'icon' => 'fa-cogs fa-lg fa-fw',
				1		=> array('link' => 'admin/manage_extensions.php'.$this->SID,		'text' => $this->user->lang('extension_repo'),'check' => 'a_config_man',	'icon' => 'fa-cogs fa-lg fa-fw'),
			),
			'portal'	=> array(
				'icon'	=> 'fa-home fa-lg fa-fw',
				'name'	=> $this->user->lang('portal'),
				1		=> array('link' => 'admin/manage_portal.php'.$this->SID,			'text' => $this->user->lang('portalmanager'),		'check' => 'a_config_man',	'icon' => 'fa-columns fa-lg fa-fw'),
				2		=> array('link' => 'admin/manage_article_categories.php'.$this->SID,'text' => $this->user->lang('manage_articles'),		'check' => array('OR', array('a_articles_man', 'a_article_categories_man')),	'icon' => 'fa-file-text fa-lg fa-fw'),
				3		=> array('link' => 'admin/manage_pagelayouts.php'.$this->SID,		'text' => $this->user->lang('page_manager'),		'check' => 'a_config_man',	'icon' => 'fa-table fa-lg fa-fw'),
				4		=> array('link' => 'admin/manage_menus.php'.$this->SID,				'text' => $this->user->lang('manage_menus'),		'check' => 'a_config_man',	'icon' => 'fa-list fa-lg fa-fw'),
				
			),
			'raids'	=> array(
				'icon'	=> 'fa-trophy fa-lg fa-fw',
				'name'	=> $this->user->lang('raids'),
				1		=> array('link' => 'admin/manage_raids.php'.$this->SID,			'text' => $this->user->lang('manage_raids'),		'check' => 'a_raid_add',	'icon' => 'fa-trophy fa-lg fa-fw'),
				2		=> array('link' => 'admin/manage_events.php'.$this->SID,			'text' => $this->user->lang('manevents_title'),	'check' => 'a_event_upd',	'icon' => 'fa-key fa-lg fa-fw'),
				3		=> array('link' => 'admin/manage_multidkp.php'.$this->SID,		'text' => $this->user->lang('manmdkp_title'),		'check' => 'a_event_upd',	'icon' => 'fa-gavel fa-lg fa-fw'),
				4		=> array('link' => 'admin/manage_itempools.php'.$this->SID,		'text' => $this->user->lang('manitempools_title'),'check' => 'a_event_upd',	'icon' => 'fa-tags fa-lg fa-fw'),
				5		=> array('link' => 'admin/manage_raid_groups.php'.$this->SID,		'text' => $this->user->lang('manage_raid_groups'),'check' => array('OR', array('a_raidgroups_man', 'a_raidgroups_grpleader')),	'icon' => 'fa-users fa-lg fa-fw'),
				6		=> array('link' => 'admin/manage_export.php'.$this->SID,		'text' => $this->user->lang('manexport_title'),'check' => 'a_',	'icon' => 'fa-share-square-o fa-lg fa-fw'),
			),
			'calendar'	=> array(
				'icon'	=> 'fa-calendar fa-lg fa-fw',
				'name'	=> $this->user->lang('calendars'),
				1		=> array('link' => 'admin/manage_calendars.php'.$this->SID,		'text' => $this->user->lang('manage_calendars'),	'check' => 'a_calendars_man',	'icon' => 'fa-calendar fa-lg fa-fw'),
				2		=> array('link' => 'admin/manage_calevents.php'.$this->SID,		'text' => $this->user->lang('manage_calevents'),	'check' => 'a_cal_event_man',	'icon' => 'fa-clock-o fa-lg fa-fw'),
			),
			'general' => array(
				'icon'	=> 'fa-wrench fa-lg fa-fw',
				'name'	=> $this->user->lang('general_admin'),
				1		=> array('link' => 'admin/manage_settings.php'.$this->SID,		'text' => $this->user->lang('configuration'),		'check' => 'a_config_man',	'icon' => 'fa-wrench fa-lg fa-fw'),
				2		=> array('link' => 'admin/manage_logs.php'.$this->SID,			'text' => $this->user->lang('view_logs'),			'check' => 'a_logs_view',	'icon' => 'fa-book fa-lg fa-fw'),
				3		=> array('link' => 'admin/manage_tasks.php'.$this->SID,			'text' => $this->user->lang('mantasks_title'),		'check' => array('OR', array('a_users_man', 'a_members_man')),	'icon' => 'fa-tasks fa-lg fa-fw'),
				4		=> array('link' => 'admin/manage_bridge.php'.$this->SID,		'text' => $this->user->lang('manage_bridge'),	'check' => 'a_config_man',	'icon' => 'fa-link fa-lg fa-fw'),
				5		=> array('link' => 'admin/manage_crons.php'.$this->SID,			'text' => $this->user->lang('manage_cronjobs'),		'check' => 'a_config_man',	'icon' => 'fa-clock-o fa-lg fa-fw'),
				6		=> array('link' => 'admin/manage_media.php'.$this->SID,			'text' => $this->user->lang('manage_media'),		'check' => 'a_files_man',	'icon' => 'fa-picture-o fa-lg fa-fw'),
			),
			'maintenance' => array(
				'icon'	=> 'fa-cog fa-lg fa-fw',
				'name'	=> $this->user->lang('menu_maintenance').(($blnShowBadges) ? $coreUpdates : ''),
				1		=> array('link' => 'maintenance/'.$this->SID,		'text' => $this->user->lang('maintenance'),		'check' => 'a_maintenance',	'icon' => 'fa-cog fa-lg fa-fw'),
				2		=> array('link' => 'admin/manage_live_update.php'.$this->SID,		'text' => $this->user->lang('liveupdate'),		'check' => 'a_maintenance',	'icon' => 'fa fa-refresh fa-lg fa-fw'),
				3		=> array('link' => 'admin/manage_backup.php'.$this->SID,			'text' => $this->user->lang('backup'),			'check' => 'a_backup',		'icon' => 'fa-floppy-o fa-lg fa-fw'),
				4		=> array('link' => 'admin/manage_reset.php'.$this->SID,			'text' => $this->user->lang('reset'),				'check' => 'a_config_man',	'icon' => 'fa-retweet fa-lg fa-fw'),
				5		=> array('link' => 'admin/manage_cache.php'.$this->SID,			'text' => $this->user->lang('pdc_manager'),		'check' => 'a_config_man',	'icon' => 'fa-briefcase fa-lg fa-fw'),
				6		=> array('link' => 'admin/info_database.php'.$this->SID,			'text' => $this->user->lang('mysql_info'),		'check' => 'a_config_man',	'icon' => 'fa-database fa-lg fa-fw'),				
			),
		);

		// Now get plugin hooks for the menu
		$admin_menu = (is_array($this->pm->get_menus('admin'))) ? array_merge_recursive($admin_menu, array('extensions'=>$this->pm->get_menus('admin'))) : $admin_menu;

		//Now get the admin-favorits
		$favs_array = array();
		if($this->config->get('admin_favs')) {
			$favs_array = $this->config->get('admin_favs');
		}
		$admin_menu['favorits']['icon'] = 'fa-star fa-lg fa-fw';
		$admin_menu['favorits']['name'] = $this->user->lang('favorits');
		//Style Management
		$admin_menu['favorits'][1] = array(
			'link'	=> 'admin/manage_extensions.php'.$this->SID.'&tab=1',
			'text'	=> $this->user->lang('styles_title'),
			'check'	=> 'a_extensions_man',
			'icon'	=> 'fa-paint-brush fa-lg fa-fw',
		);
			
		$i = 2;
		if (is_array($favs_array) && count($favs_array) > 0){
			foreach ($favs_array as $fav){
				$items = explode('|', $fav);
				$adm = $admin_menu;
				foreach ($items as $item){
					$latest = $adm;
					$adm = (isset($adm[$item])) ? $adm[$item] : false;
				}
				if (isset($adm['link'])){
					$admin_menu['favorits'][$i] = array(
						'link'	=> $adm['link'],
						'text'	=> $adm['text'].((count($items) == 3) ? ' ('.$latest['name'].')': ''),
						'check'	=> $adm['check'],
						'icon'	=> $adm['icon'],
					);
				}
				$i++;
			}
		} else { //If there are no links, point to the favorits-management
			$admin_menu['favorits'][2] = array(
				'link'	=> 'admin/manage_menus.php'.$this->SID.'&tab=1',
				'text'	=> $this->user->lang('manage_menus'),
				'check'	=> 'a_config_man',
				'icon'	=> 'fa-list fa-lg fa-fw',
			);
		}
		
		return $admin_menu;
	}
}
}
?>
