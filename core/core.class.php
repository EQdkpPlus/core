<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2010
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 *
 * $Id$
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class core extends gen_class {
	public static $shortcuts = array('pfh', 'jquery', 'time', 'pdh', 'pm', 'pdl', 'tpl', 'user','db', 'config', 'timekeeper', 'env', 'in', 'portal', 'ntfy');

		// General vars
		public $header_format	= 'full';			// Use a simple header?		@var
		public $page_title		= '';				// Page title				@var page_title
		public $template_file	= '';				// Template file to parse	@var template_file
		public $template_path	= '';				// Path to template_file	@var template_path
		public $description		= '';				// Description of the page, relevant for META-Tags
		public $page_image		= '';				// Preview-Image, relevant for META-Tags

		/**
		* Construct
		*/
		public function __construct(){
			$this->header_format		= (isset($_GET['simple_head'])) ? 'simple' : 'full';
		}

		/**
		* Set a config value
		*
		* @param		string		$config_name		Name of the config value
		* @param		string		$config_value		Value
		* @param		string		$plugin				if plugin, plugin name
		*/
		public function config_set($config_name, $config_value='', $plugin=''){
			$this->pdl->deprecated('config_set');
			return $this->config->set($config_name, $config_value, $plugin);
		}

		public function config_del($config_name, $plugin=''){
			$this->pdl->deprecated('config_del');
			return $this->config->del($config_name, $plugin);
		}

		public function config($name, $plugin=''){
			$this->pdl->deprecated('config');
			return $this->config->get($name, $plugin);
		}

		/**
		* Add a Message to all Pages
		*
		* @param		string		$text				Message text
		* @param		string		$title				Message title
		* @param		string		$kind				Color Theme: red/green/default
		*/
		public function message($text='', $title='', $kind='default', $showalways=true, $parent=false){
			if($showalways || (!$showalways && $this->header_format != 'simple')){
				switch($kind){
					case 'red': $kkind = 'error';break;
					case 'green': $kkind = 'success';break;
					default: $kkind = 'default';
				}
				$this->jquery->notify($text, array('header' => $title,'expires' => false, 'custom'=>true,'theme'  => $kkind, 'parent' => $parent));
			}
		}

		/**
		* Add a Messages to all Pages
		*
		* @param		string		$text				Message text
		* @param		string		$title				Message title
		* @param		string		$kind				Color Theme: red/green/default
		*/
		public function messages($messages){
			foreach($messages as $message){
				$name = (is_array($message)) ? 'message' : 'messages';
				${$name}['showalways'] = (isset(${$name}['showalways'])) ? ${$name}['showalways'] : true;
				${$name}['parent'] = (isset(${$name}['parent'])) ? ${$name}['parent'] : true;
				$this->message(${$name}['text'], ${$name}['title'], ${$name}['color'], ${$name}['showalways'], ${$name}['parent']);
				if(!is_array($message)){
					break;
				}
			}
		}

		/**
		* Add a Messages to all Pages
		*
		* @param		string		$text				Message text
		* @param		string		$title				Message title
		* @param		string		$kind				Color Theme: red/green/default
		*/
		public function global_warning($message, $icon='icon_false', $class='errorbox') {
			$this->tpl->assign_block_vars(
				'global_warnings', array(
					'MESSAGE'	=> $message,
					'CLASS'		=> $class,
					'ICON'		=> $icon,
				)
			);
		}

		/**
		* Set object variables
		*
		* @var $var Var to set
		* @var $val Value for Var
		* @return bool
		*/
		public function set_vars($var, $val = '', $append=false){
			if(is_array($var)){
				foreach ( $var as $d_var => $d_val ){
					$this->set_vars($d_var, $d_val);
				}
			}else{
				if (empty($val) ){
					return false;
				}
				if (($var == 'display') && ($val === true)){
					$this->generate_page();
				}else{
					if ( $append ){
						if ( is_array($this->$var) ){
							$this->{$var}[] = $val;
						}elseif ( is_string($this->$var) ){
							$this->$var .= $val;
						}else{
							$this->$var = $val;
						}
					}else{
						$this->$var = $val;
					}
				}
			}
			return true;
		}

		public function generate_page(){
			$this->check_requirements();
			$this->page_header();
			$this->page_tail();
		}

		private function page_header(){
			define('HEADER_INC', true);		// Define a variable so we know the header's been included
			
			if ($this->user->is_signedin() && (int)$this->user->data['rules'] != 1){
				if ($this->env->request_page != 'register.php'){
					redirect('register.php');
				}
			}
			
			// Check if gzip is enabled & send the HTTP headers
			if ( $this->config->get('enable_gzip') == '1' ){
				if ( (extension_loaded('zlib')) && (!headers_sent()) ){
					@ob_start('ob_gzhandler');
				}
			}
			@header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
			@header('Content-Type: text/html; charset=utf-8');
			//Disable Browser Cache
			@header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
			@header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Datum in der Vergangenheit

			// some style additions (header, background image..)
			if ($this->user->style['background_img'] != ''){
				if (strpos($this->user->style['background_img'],'://') > 1){
					$template_background_file = $this->user->style['background_img'];
				} else {
					$template_background_file = $this->root_path .$this->user->style['background_img'];
				}
			} else {
				$template_background_file = $this->root_path . 'games/' .$this->config->get('default_game') . '/template_background.jpg' ;

				if (!file_exists($template_background_file)){
					$template_background_file	= $this->root_path . 'templates/' . $this->user->style['template_path'] . '/images/template_background.jpg';
				}
			}

			// Load a template specific template css out of template folder
			$css_theme		= $this->root_path.'templates/'.$this->user->style['template_path'].'/'.$this->user->style['template_path'].'.css';
			$global_css		= $this->root_path.'templates/eqdkpplus.css';
			$customcss		= $this->root_path.'templates/'.$this->user->style['template_path'].'/custom.css';
			$customjs		= $this->root_path.'templates/'.$this->user->style['template_path'].'/custom.js';

			$storage_folder = $this->pfh->FolderPath('templates/'.$this->user->style['template_path'], 'eqdkp');

			// add the custom JS file
			if(is_file($customjs)){
				$this->tpl->js_file($customjs);
			}
			

			if (!is_file($storage_folder.'main.css')){
				$this->tpl->parse_cssfile();
				$this->tpl->css_file($storage_folder.'main.css');
			} elseif (is_file($storage_folder.'main.css')){
				//Renew file?
				if ($this->timekeeper->get('tpl_cache_'.$this->user->style['template_path'], 'main.css') < @filemtime($css_theme) ||
					$this->timekeeper->get('tpl_cache_'.$this->user->style['template_path'], 'main.css') < @filemtime($global_css) || (is_file($customcss) && $this->timekeeper->get('tpl_cache_'.$this->user->style['template_path'], 'main.css') < @filemtime($customcss))){
					$this->tpl->parse_cssfile();
				}
				$this->tpl->css_file($storage_folder.'main.css');
			} elseif(is_file($css_theme)){
				$this->tpl->css_file($global_css);
				$this->tpl->css_file($css_theme);
			}
			if ($this->config->get('pk_maintenance_mode') && $this->user->check_auth('a_', false)){
				$this->global_warning($this->user->lang('maintenance_mode_warn'), 'icon_maintenance');
			}

			$s_in_admin		= (((defined('IN_ADMIN') ) ? IN_ADMIN : false) && ($this->user->check_auth('a_', false)) ) ? true : false;
			if (file_exists($this->root_path.'install') && EQDKP_INSTALLED && $this->user->check_auth('a_', false) && $s_in_admin && !VERSION_WIP){
				$this->global_warning($this->user->lang('install_folder_warn'));
			}

			//Registration Link
			if ($this->config->get('cmsbridge_active') == 1 && strlen($this->config->get('cmsbridge_reg_url'))){
				$arrLinkData = $this->handle_link($this->config->get('cmsbridge_reg_url'),$this->user->lang('menu_register'),$this->config->get('cmsbridge_embedded'),'register');
				$register_link = (isset($arrLinkData['plus_link'])) ? $arrLinkData['link'] : $this->root_path.$arrLinkData['link'];
			} else {
				$register_link = $this->root_path.'register.php'.$this->SID;
			}

			// Add a javascript code to make some checkboxes clickable through table rows
			$this->tpl->add_js("$('.trcheckboxclick tr').click(function(event) {
						if (event.target.type !== 'checkbox') {
							$(':checkbox', this).trigger('click');
						}
					});", 'docready');


			$this->tpl->add_js('$(function(){
				var $search = $("#loginarea_search");
				original_val = $search.val();
				$search.focus(function(){
					if($(this).val()===original_val){
						$(this).val("");
					}
				})
				.blur(function(){
					if($(this).val()===""){
						$(this).val(original_val);
					}
				});
			});

			', 'docready');

			//Lightbox Zoom-Image
			$this->tpl->add_js("
			$('a.lightbox').each(function(index) {
				var image = $(this).html();
				var fullimage = $(this).attr('href');
				var image_style = $(this).children().attr('style');
				if (image_style) $(this).attr('style', image_style);
				var randomId = parseInt(Math.random() * 1000);
				var zoomIcon = '<div class=\"image_resized\" onmouseover=\"$(\'#imgresize_'+randomId+'\').show()\" onmouseout=\"$(\'#imgresize_' +randomId+'\').hide()\" style=\"display:inline-block;\"><div id=\"imgresize_'+randomId+'\" class=\"markImageResized\"><a href=\"'+fullimage+'\" class=\"lightbox\"><img src=\"'+mmocms_root_path+'images/global/zoom.png\" alt=\"Resized\"/><\/a><\/div><a href=\"'+fullimage+'\" class=\"lightbox\">'+image+'<\/a><\/div>';
				$(this).html(zoomIcon);
			});
			", 'docready');

			//Mobile Browser detection
			if ((int)$this->config->get('eqdkpm_shownote')){
			$this->tpl->add_js("function detectMobileBrowser() {
				if (document.cookie.indexOf(\"eqdkpm_redirect=false\") < 0) {
					if(screen.width < 500 || navigator.userAgent.match(/Android/i) || navigator.userAgent.match(/webOS/i) || navigator.userAgent.match(/iPhone/i) || navigator.userAgent.match(/iPod/i)) {
						setEQdkpmCookies();
						if (confirm(\"".$this->user->lang('eqdkpm_redirectnote').".\"))
							window.location = mmocms_root_path + \"m/\";
					}
				}
			}

			function setEQdkpmCookies() {
				var date = new Date();
				var days = 14;
				date.setTime(date.getTime()+(days*24*60*60*1000));
				var expires = \"; expires=\"+ date.toGMTString();
				document.cookie = \"eqdkpm_redirect=false\" + expires;
			}
			detectMobileBrowser();", 'docready');
			}

			// global qtip
			$this->jquery->qtip(".coretip", "return $(this).attr('data-coretip');", array('contfunc'=>true, 'width'=>200));

			//Portal Output
			$this->portal->module_output();
			
			//Registration Link
			$registerLink = '';
			if ( ! $this->user->is_signedin() && intval($this->config->get('disable_registration')) != 1){
				//CMS register?
				if ($this->config->get('cmsbridge_active') == 1 && strlen($this->config->get('cmsbridge_reg_url'))){
					$registerLink = $this->createLink($this->handle_link($this->config->get('cmsbridge_reg_url'),$this->user->lang('menu_register'),$this->config->get('cmsbridge_embedded'),'register', '', '', 'icon-check'));
				} else {
					$registerLink = $this->createLink(array('link' => 'register.php' . $this->SID, 'text' => $this->user->lang('menu_register'), 'icon' => 'icon-check'));
				}
			}
			
			$arrPWresetLink = $this->handle_link($this->config->get('cmsbridge_pwreset_url'),$this->user->lang('lost_password'),$this->config->get('cmsbridge_embedded'),'pwreset');

			// Load the jQuery stuff
			$this->tpl->assign_vars(array(
				'PAGE_TITLE'				=> $this->pagetitle($this->page_title),
				'MAIN_TITLE'				=> $this->config->get('main_title'),
				'SUB_TITLE'					=> $this->config->get('sub_title'),
				'GUILD_TAG'					=> $this->config->get('guildtag'),
				'META_KEYWORDS'				=> ($this->config->get('pk_meta_keywords') && strlen($this->config->get('pk_meta_keywords'))) ? $this->config->get('pk_meta_keywords') : $this->config->get('guildtag').', '.$this->config->get('default_game').((strlen($this->config->get('uc_servername'))) ? ', '.$this->config->get('uc_servername') : ''),
				'META_DESCRIPTION'			=> ($this->config->get('pk_meta_description') && strlen($this->config->get('pk_meta_description'))) ? $this->config->get('pk_meta_description') : $this->config->get('guildtag'),
				'EQDKP_ROOT_PATH'			=> $this->root_path,
				'EQDKP_IMAGE_PATH'			=> $this->root_path.'images/',
				'HEADER_LOGO'				=> (is_file($this->pfh->FolderPath('','files').$this->config->get('custom_logo'))) ? $this->pfh->FolderPath('','files').$this->config->get('custom_logo') : $this->root_path."templates/".$this->user->style['template_path']."/images/logo.png",
				'TEMPLATE_BACKGROUND'		=> $template_background_file,
				'TEMPLATE_PATH'				=> $this->root_path . 'templates/' . $this->user->style['template_path'],
				'USER_TIME'					=> $this->time->user_date($this->time->time, true, false, true, true, true),
				'USER_NAME'					=> isset($this->user->data['username']) ? sanitize($this->user->data['username']) : $this->user->lang('anonymous'),
				'AUTH_LOGIN_BUTTON'			=> (!$this->user->is_signedin()) ? implode(' ', $this->user->handle_login_functions('login_button')) : '',

				'S_NORMAL_HEADER'			=> ($this->header_format != 'simple') ? true : false,
				'S_NORMAL_FOOTER'			=> ($this->header_format != 'simple') ? true : false,
				'S_NO_HEADER_FOOTER'		=> ($this->header_format == 'none') ? true : false,
				'S_ADMIN'					=> $this->user->check_auth('a_', false),
				'S_IN_ADMIN'				=> $s_in_admin,
				'S_SEARCH'					=> $this->user->check_auth('u_search', false),
				'SID'						=> ((isset($SID)) ? $SID : '?' . 's='),
				'S_LOGGED_IN'				=> ($this->user->is_signedin()) ? true : false,
				'FIRST_C'					=> true,
				'T_PORTAL_WIDTH'			=> $this->user->style['portal_width'],
				'T_COLUMN_LEFT_WIDTH'		=> $this->user->style['column_left_width'],
				'T_COLUMN_RIGHT_WIDTH'		=> $this->user->style['column_right_width'],
				'T_LOGO_POSITION'			=> $this->user->style['logo_position'],
				'S_REGISTER'				=> !(int)$this->config->get('disable_registration'),
				'REGISTER_LINK'				=> $register_link,
				'CSRF_TOKEN'				=> '<input type="hidden" name="'.$this->user->csrfPostToken().'" value="'.$this->user->csrfPostToken().'"/>',
				'CSRF_LOGOUT_TOKEN'			=> $this->user->csrfGetToken("loginlogout"),
				'U_CHARACTERS'				=> ($this->user->is_signedin() && $this->user->check_auths(array('u_member_man', 'u_member_add', 'u_member_conn', 'u_member_del'), 'OR', false)) ? $this->root_path.'characters.php' . $this->SID : '',
				'U_REGISTER'				=> $registerLink,
				'MAIN_MENU'					=> $this->build_menu_ul(),
				'PAGE_CLASS'				=> 'page-'.$this->clean_url($this->env->get_current_page(false)),
				'S_SHOW_PWRESET_LINK'		=> ($this->config->get('cmsbridge_active') == 1 && !strlen($this->config->get('cmsbridge_pwreset_url'))) ? false : true,
				'U_PWRESET_LINK'			=> ($this->config->get('cmsbridge_active') == 1 && strlen($this->config->get('cmsbridge_pwreset_url'))) ? $this->createLink($arrPWresetLink) : '<a href="'.$this->root_path."login.php".$this->SID."&amp;mode=lostpassword\">".$this->user->lang('lost_password').'</a>',	
			));
						
			if (isset($this->page_body) && $this->page_body == 'full'){
				$this->tpl->assign_vars(array(
					'FIRST_C'	=> false,
					'THIRD_C'	=> false,
				));
			}

			// show the full head
			if ($this->header_format != 'simple'){
				// System Message if user has no assigned members
				if($this->pdh->get('member', 'connection_id', array($this->user->data['user_id'])) < 1 && ($this->user->is_signedin()) && ($this->user->data['hide_nochar_info'] != '1')){
					$message = '<a href="'.$this->root_path.'characters.php'.$this->SID.'">'.$this->user->lang('no_connected_char').'</a>';
					$message .= '<br /><br /><a href="'.$this->root_path.'characters.php'.$this->SID.'&hide_info=true">'.$this->user->lang('no_connected_char_hide').'</a>';
					$this->message($message);
				}
			}
			
			//Do portal hook
			register('hooks')->process('portal', array($this->env->eqdkp_page));
		}
		
		public function createLink($arrLinkData, $strCssClass = ''){
			$target = '';
			if (isset($arrLinkData['target']) && strlen($arrLinkData['target'])){
				$target = ' target="'.$arrLinkData['target'].'"';
			}
			$icon = '';
			if (isset($arrLinkData['icon']) && strlen($arrLinkData['icon'])){
				$icon = '<i class="'.$arrLinkData['icon'].'"></i>';
			}
			return '<a href="' . ((isset($arrLinkData['plus_link']) && $arrLinkData['plus_link']==true) ? $arrLinkData['link'] : $this->root_path . $arrLinkData['link']) . '"'.$target.' class="'.$strCssClass.'">' . $icon . $arrLinkData['text'] . '</a>';
		}
		
		public function menu_items(){
			$arrItems = array(
				array('link' => 'index.php'.$this->SID,				'text' => $this->user->lang('home')),
				array('link' => 'listcharacters.php'.$this->SID,	'text' => $this->user->lang('menu_standings'),	'check' => 'u_member_view'),
				array('link' => 'roster.php'.$this->SID,			'text' => $this->user->lang('menu_roster'),		'check' => 'u_roster_list'),
				array('link' => 'listraids.php'.$this->SID,			'text' => $this->user->lang('menu_raids'),		'check' => 'u_raid_view'),
				array('link' => 'listevents.php'.$this->SID,		'text' => $this->user->lang('menu_events'),		'check' => 'u_event_view'),
				array('link' => 'listitems.php'.$this->SID,			'text' => $this->user->lang('menu_itemhist'),	'check' => 'u_item_view'),
				array('link' => 'viewnews.php'.$this->SID,			'text' => $this->user->lang('menu_news'),		'check' => 'u_news_view'),
				array('link' => 'calendar/index.php'.$this->SID,	'text' => $this->user->lang('menu_calendar'),	'check' => 'u_calendar_view'),
				array('link' => 'listusers.php'.$this->SID,			'text' => $this->user->lang('user_list'),		'check' => 'u_userlist'),
			);
			
			//Pages
			if (is_array($this->pdh->get('pages', 'mainmenu_pages', array()))){
				$arrItems = array_merge($arrItems,$this->pdh->get('pages', 'mainmenu_pages', array()));
			}
			
			//Plugins
			if (is_object($this->pm)){
				$plugin_menu = $this->pm->get_menus('main_menu1');
				$arrItems = (is_array($plugin_menu)) ? array_merge($arrItems, $plugin_menu) : $arrItems;
			}
			
			//Forum
			if (strlen($this->config->get('cmsbridge_url')) > 0 && $this->config->get('cmsbridge_active') == 1){
				$inlineforum = $this->handle_link($this->config->get('cmsbridge_url'), $this->user->lang('forum'), $this->config->get('cmsbridge_embedded'), 'board');
				$arrItems[]	= $inlineforum;
			}
			
			//Plus Links
			$arrItems = array_merge($arrItems, $this->pdh->get('links', 'menu', array()));
			
			return $arrItems;
		}
		
		public function build_link_hash($arrLinkData){
			return md5($this->user->removeSIDfromString($arrLinkData['link']).((isset($arrLinkData['id'])) ? $arrLinkData['id'] : ''));
		}
		
		public function build_menu_array($show_hidden = true, $blnOneLevel = false){
			$arrItems = $this->menu_items();
			$arrSortation = unserialize($this->config->get('mainmenu'));

			foreach ($arrItems as $key => $item){
				$strHash = $this->build_link_hash($item);
				$arrItems[$key]['_hash'] = $this->build_link_hash($item);
				$arrItems[$key]['hidden'] = 0;
				$arrHashArray[$strHash] = $arrItems[$key];
			}
			$arrOut = array();
			$arrOutOneLevel = array();
			$arrToDo = $arrHashArray;

			foreach($arrSortation as $key => $item){
				$show = true;
				$hidden = $item['item']['hidden'];
				if ($hidden && !$show_hidden) $show = false;
				$hash = $item['item']['hash'];
				if (!isset($arrHashArray[$hash])) $show = false;
				unset($arrToDo[$hash]);
				if ($show) {
					if ($hidden) $arrHashArray[$hash]['hidden'] = 1;
					$arrHashArray[$hash]['depth'] = 0;
					$arrOut[$key] = $arrHashArray[$hash];
					$arrOutOneLevel[] = $arrHashArray[$hash];
				}
				//Second Level
				if (isset($item['_childs']) && is_array($item['_childs'])){
					foreach($item['_childs'] as $key2 => $item2){
						$hidden = $item2['item']['hidden'];
						if ($hidden && !$show_hidden) $show = false;
						$hash = $item2['item']['hash'];
						if (!isset($arrHashArray[$hash])) $show = false;
						unset($arrToDo[$hash]);
						if ($show) {
							if ($hidden) $arrHashArray[$hash]['hidden'] = 1;
							$arrHashArray[$hash]['depth'] = 1;
							$arrOut[$key]['childs'][$key2] = $arrHashArray[$hash];
							$arrOutOneLevel[] = $arrHashArray[$hash];
						}
						//Third Level
						if (isset($item2['_childs']) && is_array($item2['_childs'])){
							foreach($item2['_childs'] as $key3 => $item3){
								$hidden = $item3['hidden'];
								if ($hidden && !$show_hidden) $show = false;
								$hash = $item3['hash'];
								if (!isset($arrHashArray[$hash])) $show = false;
								unset($arrToDo[$hash]);
								if ($show) {
									if ($hidden) $arrHashArray[$hash]['hidden'] = 1;
									$arrHashArray[$hash]['depth'] = 2;
									$arrOut[$key]['childs'][$key2]['childs'][$key3] = $arrHashArray[$hash];
									$arrOutOneLevel[] = $arrHashArray[$hash];
								}	
							}
							$show = true;
						}
					}
					$show = true;
				}
			}
			
			foreach($arrToDo as $hash => $item){
				$item['hidden'] = 0;
				$arrOut[] = $item;
				$arrOutOneLevel[] = $item;
			}

			return ($blnOneLevel) ? $arrOutOneLevel: $arrOut;
		}
		
		public function build_menu_select($show_hidden = true){
			
		}
		
		public function build_menu_ul(){
			$arrItems = $this->build_menu_array(false);
			
			$html  = '<ul class="mainmenu">';
			
			foreach($arrItems as $k => $v){
				if ( !is_array($v) )continue;
				
				if (!isset($v['childs'])){
					if ( $this->check_url_for_permission($v)) {
						$class = $this->clean_url($v['link']);
						$html .= '<li class="link_li_'.$class.'"><i class="link_i_'.$class.'"></i>'.$this->createLink($v, 'link_'.$class).'</li>';
					} else {
						continue;
					}
					
				} else {
					if ( $this->check_url_for_permission($v)) {
						$class = $this->clean_url($v['link']);
						$html .= '<li class="link_li_'.$class.'"><i class="link_i_'.$class.'"></i>'.$this->createLink($v, 'link_'.$class).'<ul>';
					} else {
						continue;
					}
					
					foreach($v['childs'] as $k2 => $v2){
						if (!isset($v2['childs'])){
							if ( $this->check_url_for_permission($v2)) {
								$class = $this->clean_url($v2['link']);
								$html .= '<li class="link_li_'.$class.'"><i class="link_i_'.$class.'"></i>'.$this->createLink($v2, 'link_'.$class).'</li>';
							} else {
								continue;
							}
						} else {
							if ( $this->check_url_for_permission($v2)) {							
								$class = $this->clean_url($v2['link']);
								$html .= '<li class="link_li_'.$class.'"><i class="link_i_'.$class.'"></i>'.$this->createLink($v2, 'link_'.$class).'<ul>';
							} else {
								continue;
							}
							
							foreach($v2['childs'] as $k3 => $v3){
								if ( $this->check_url_for_permission($v3)) {	
									$class = $this->clean_url($v3['link']);
									$html .= '<li class="link_li_'.$class.'"><i class="link_i_'.$class.'"></i>'.$this->createLink($v3, 'link_'.$class).'</li>';
								} else {
									continue;
								}
							}
							
							$html .= '</ul></li>';
						}
					
					}
					
					$html .= '</ul></li>';
				}
				
			}
			
			$html .= '</ul>';
			return $html;
		}
		
		public function clean_url($strUrl){
			return preg_replace("/[^a-zA-Z0-9_]/","",utf8_strtolower($this->user->removeSIDfromString($strUrl)));
		}
		
		public function check_url_for_permission($arrLinkData){
			if ( (empty($arrLinkData['check'])) || ($this->user->check_auth($arrLinkData['check'], false))) {
				if (isset($arrLinkData['signedin'])){
					$perm = true;
					switch ($arrLinkData['signedin']){
						case 0: if ($this->user->is_signedin()) $perm = false;
						break;
						case 1: if (!$this->user->is_signedin()) $perm = false;
						break;
					}
					if (!$perm) return false;
				}
				
				return true;
			}
			return false;
		}

		public function handle_link($url, $text,$window,$wrapper_id = '', $check = '', $editable = true, $icon = ''){
			$arrData = array();
			switch ($window){
				case '0':  $arrData['plus_link'] = true;
					break ;
				case '1':  $arrData['target'] = '_blank';
							$arrData['plus_link'] = true;
					break ;
				case '2':
				case '3':
				case '4':  $url = 'wrapper.php'.$this->SID.'&amp;id='.$wrapper_id;
					break ;
			}
			$arrData['link'] = $url;
			$arrData['check'] = $check;
			$arrData['text'] = $text;
			$arrData['icon'] = $icon;
			$arrData['id'] = $wrapper_id;
			if (!$editable) $arrData['editable'] = false;
			return $arrData;
		}

		public function page_tail(){
			if ( !empty($this->template_path) ){
				$this->tpl->set_template($this->user->style['template_path'], '', $this->template_path);
			}

			$this->tpl->set_filenames(array(
				'body' => $this->template_file)
			);

			// Hiding the normal-footer-stuff, but show debug-info, since in normal usage debug mode is turned off, and for developing purposes debug-tabs help alot info if header is set to none..
			$this->tpl->assign_vars(array(
				'S_NORMAL_FOOTER' 			=> ($this->header_format != 'simple') ? true : false,
				'EQDKP_PLUS_COPYRIGHT'		=> $this->Copyright())
			);

			//Call Social Plugins
			$image = ((is_file($this->pfh->FolderPath('logo','eqdkp').$this->config->get('custom_logo'))) ? $this->env->buildlink().$this->pfh->FolderPath('logo','eqdkp', true).$this->config->get('custom_logo') : $this->env->buildlink()."templates/".$this->user->style['template_path']."/images/logo.png");
			$image = ($this->image != '') ? $this->image : $image;
			$description = ($this->description != '') ? $this->description : (($this->config->get('pk_meta_description') && strlen($this->config->get('pk_meta_description'))) ? $this->config->get('pk_meta_description') : $this->config->get('guildtag'));
			register('socialplugins')->callSocialPlugins($this->page_title, $description, $image);
			$this->checkAdminTasks();
			
			//Notifications
			$arrNotificationGreen	= register('ntfy')->get('green');
			$arrNotificationRed		= register('ntfy')->get('red');
			$arrNotificationYellow	= register('ntfy')->get('yellow');
			$this->tpl->assign_vars(array(
				'NOTIFICATION_COUNT_RED'	=> $arrNotificationRed['count'],
				'NOTIFICATION_COUNT_YELLOW' => $arrNotificationYellow['count'],
				'NOTIFICATION_COUNT_GREEN' 	=> $arrNotificationGreen['count'],
				'NOTIFICATION_RED'			=> $arrNotificationRed['html'],
				'NOTIFICATION_YELLOW'		=> $arrNotificationYellow['html'],
				'NOTIFICATION_GREEN'		=> $arrNotificationGreen['html'],
				'NOTIFICATION_COUNT_TOTAL'	=> $arrNotificationRed['count'] + $arrNotificationYellow['count'] + $arrNotificationGreen['count'],
			));

			if(DEBUG) {
				$this->user->output_unused();
				$log = $this->pdl->get_log();
				$this->tpl->assign_vars(array(
					'S_SHOW_DEBUG'			=> true,
					'S_SHOW_QUERIES'		=> true,
					'EQDKP_RENDERTIME'		=> pr('', 2),
					'EQDKP_QUERYCOUNT'		=> $this->db->query_count,
					'EQDKP_MEM_PEAK'		=> number_format(memory_get_peak_usage()/1024, 0, '.', ',').' kb'
				));

				//debug tabs
				if(count($log) > 0) {
					$this->jquery->Tab_header('plus_debug_tabs');
					$debug_tabs_header = "<div id='plus_debug_tabs'><ul>";
					$i = 1;
					$debug_tabs = "";
					foreach($log as $type => $log_entries) {
						$debug_tabs_header .= "<li><a href='#error-".$i."'><span>".$type." (".count($log_entries).")"."</span></a></li>";
						$debug_tabs .= '<div id="error-'.$i.'">';
						$debug_tabs .= '<table width="99%" border="0" cellspacing="1" cellpadding="0" class="colorswitch">';
						foreach($log_entries as $log_entry){
							$debug_tabs .= '<tr><td>'.$this->pdl->html_format_log_entry($type, $log_entry).'</td></tr>';
						}
						$debug_tabs .= '</table>';
						$debug_tabs .= '</div>';
						$i++;
					}
					$debug_tabs_header .= "</ul>";
					$debug_tabs .= '</div>';
					$this->tpl->assign_vars(array(
						'DEBUG_TABS' => $debug_tabs_header . $debug_tabs,
					));
				}

			} else {
				$this->tpl->assign_vars(array(
					'S_SHOW_DEBUG'		=> false,
					'S_SHOW_QUERIES'	=> false)
				);
			}
			$this->tpl->display();
		}

		public function array_intersect_split($needle, $haystack) {
			if(!is_array($haystack) || !is_array($needle)) return false;
			$new_arr = $haystack;
			foreach($haystack as $key => $value) {
				if(array_search($value, $needle) !== false) {
					unset($new_arr[$key]);
				}
			}
			return $new_arr;
		}

		public function Copyright(){
			$disclaimer_txt = '';
			if($this->config->get('pk_disclaimer_show')){
				$array_contactdetails = array(
					'name'		=> $this->config->get('pk_disclaimer_name'),
					'address'	=> $this->config->get('pk_disclaimer_address'),
					'email'		=> $this->config->get('pk_disclaimer_email'),
					'messenger'	=> $this->config->get('pk_disclaimer_messenger'),
					'irc'		=> $this->config->get('pk_disclaimer_irc'),
					'custom'	=> $this->config->get('pk_disclaimer_custom'),
				);

				$static_disclaimer = '
					<div id="dialog_disclaimer" title="'.$this->user->lang('disclaimer_win_title').'">

							<fieldset class="settings">
								<legend>'.$this->user->lang('disclaimer_c_title').'</legend>';
				foreach($array_contactdetails as $stname=>$stvalue){
					if(trim($stvalue) != ''){
						$static_disclaimer .= '<dl>
										<dt><label>'.$this->user->lang('disclaimer_c_'.$stname).'</label></dt>
										<dd>'.$stvalue.'</dd>
									</dl>';
					}
				}
				$static_disclaimer .= '</fieldset>';
				$disclaimerfile = $this->root_path.'language/'.$this->user->data['user_lang'].'/disclaimer.php' ;
				if (file_exists($disclaimerfile)){
					include_once($disclaimerfile);
					$static_disclaimer .= '<fieldset class="settings">
								<legend>'.$this->user->lang('disclaimer_c_disclaimer').'</legend>
								<div style="text-align:left;">'.$disclaimer.'</div>
						</fieldset>';
				}
				$static_disclaimer .= '
					</div>';
				$this->tpl->staticHTML($static_disclaimer);
				$this->tpl->add_js('$("#impressum").click(function(){ $("#dialog_disclaimer").dialog({height: 400, width: 600, modal: true }); });', 'docready');
				$disclaimer_txt = '<div id="impressum" class="hand">'.$this->user->lang('disclaimer').'</div>';
			}
			return $disclaimer_txt.'<div class="copyright">
						<a href="'.EQDKP_ABOUT_URL.'" target="new">EQDKP-PLUS '.((DEBUG > 3) ? '[FILE: '.VERSION_INT.', DB: '.$this->config->get('plus_version').']' : VERSION_EXT).' &copy; '.$this->time->date('Y', $this->time->time).' by EQdkp-Plus Team</a>
					</div>';
		}

		/**
		 * Keep a consistent page title across the entire application
		 *
		 * @param		string		$title			The dynamic part of the page title, appears before " - Guild Name DKP"
		 * @return		string
		 */
		private function pagetitle($title = ''){
			$pt_prefix		= (defined('IN_ADMIN')) ? $this->user->lang('admin_title_prefix') : $this->user->lang('title_prefix');
			$main_title		= sprintf($pt_prefix, $this->config->get('guildtag'), $this->config->get('dkp_name'));
			return sanitize((( $title != '' ) ? $title.' - ' : '').$main_title);
		}

		private function check_requirements(){
			$error_message = array();

			// check if Data Folder is writable
			if(count($this->pfh->get_errors()) > 0){
				foreach($this->pfh->get_errors() as $cacheerrors){
					$error_message[] = $this->user->lang($cacheerrors);
				}
			}

			// check php-Version
			if (phpversion() < VERSION_PHP_RQ){
				$error_message[] = sprintf($this->user->lang('php_too_old'), phpversion(), VERSION_PHP_RQ);
			}

			if (count($error_message) > 0){
				$out = $this->user->lang('requirements_notfilled');
				$out .= '<ul>';
				foreach ($error_message as $message){
					$out .= '<li>'.$message.'</li>';
				}
				$out .= '</ul>';
				$this->global_warning($out);
			}
		}

		public function checkAdminTasks(){
			$iTaskCount = 0;
			if ($this->user->check_auth('a_members_man', false)){
				$arrConfirmMembers = $this->pdh->get('member', 'confirm_required');
				$arrConfirmMemberCount = count($arrConfirmMembers);
				$iTaskCount += $arrConfirmMemberCount;
				if ($arrConfirmMemberCount) $this->ntfy->add('yellow', $this->user->lang('manage_members'), sprintf($this->user->lang('notification_char_confirm_required'), $arrConfirmMemberCount), $this->root_path.'admin/manage_tasks.php'.$this->SID, $arrConfirmMemberCount);

				$arrDeleteMembers = $this->pdh->get('member', 'delete_requested');
				$intDeleteMemberCount = count($arrDeleteMembers);
				$iTaskCount += $intDeleteMemberCount;
				if ($intDeleteMemberCount) $this->ntfy->add('yellow', $this->user->lang('manage_members'), sprintf($this->user->lang('notification_char_delete_requested'), $intDeleteMemberCount),  $this->root_path.'admin/manage_tasks.php'.$this->SID, $intDeleteMemberCount);
			}
			if ($this->user->check_auth('a_users_man', false) && $this->config->get('account_activation') == 2){
				$arrInactiveUser = $this->pdh->get('user', 'inactive');
				$intInactiveUserCount = count($arrInactiveUser);
				$iTaskCount += $intInactiveUserCount;
				if ($intInactiveUserCount) $this->ntfy->add('yellow', $this->user->lang('manage_users'), sprintf($this->user->lang('notification_user_enable'), $intInactiveUserCount),  $this->root_path.'admin/manage_tasks.php'.$this->SID, $intInactiveUserCount);
			}

			if ($iTaskCount > 0){
				//$this->message('<a href="'.$this->root_path.'admin/manage_tasks.php'.$this->SID.'">'.sprintf($this->user->lang('cm_todo_txt'),$iTaskCount).'</a>', $this->user->lang('cm_todo_head'), 'default', false);
			}

			return $iTaskCount;
		}
}
?>