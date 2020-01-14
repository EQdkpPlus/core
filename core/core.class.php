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

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class core extends gen_class {
		// General vars
		public $header_format	= 'full';			// Use a simple header?		@var
		public $page_title		= '';				// Page title				@var page_title
		public $template_file	= '';				// Template file to parse	@var template_file
		public $template_path	= '';				// Path to template_file	@var template_path
		public $description		= '';				// Description of the page, relevant for META-Tags
		public $page_path		= [];				// Page path, relevant for breadcrumb
		public $page_image		= '';				// Preview-Image, relevant for META-Tags
		public $body_class		= '';
		private $notifications	= false;			// Flag if notifications have been done
		private $cache = array();
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
					case 'error':
					case 'red':
						$kkind	= 'error';
						$ktitle	= ($title != '') ? $title : $this->user->lang('error');
						break;
					case 'hint':
					case 'warning':
					case 'orange':
						$kkind	= 'warning';
						$ktitle	= ($title != '') ? $title : $this->user->lang('warning');
						break;
					case 'success':
					case 'ok':
					case 'green':
						$kkind	= 'success';
						$ktitle	= ($title != '') ? $title : $this->user->lang('success');
						break;
					default:
						$kkind	= false;
						$ktitle	= $title;
				}
				$this->tpl->add_js("custom_message('".nl2br($this->jquery->sanitize($text))."', {headertxt:'".$this->jquery->sanitize($ktitle)."',icon:'".$kkind."', sticky:".(($showalways) ? 'true' : 3000).", parent:".(($parent) ? 'true' : 'false')."});");
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
				${$name}['parent'] = (isset(${$name}['parent'])) ? ${$name}['parent'] : false;
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
		public function global_warning($message, $icon='fa-exclamation-triangle ', $class='red', $dismissal=false) {
			$this->tpl->assign_block_vars(
				'global_warnings', array(
					'MESSAGE'	=> $message,
					'CLASS'		=> $class,
					'ICON'		=> 'fa '.$icon,
					'S_DISMISS'	=> $dismissal,
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
				if($val === false) $this->$var = false;
				if(empty($val)) return false;

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
			$this->notifications();
			$this->page_header();
			$this->page_tail();
		}

		private function page_header(){
			define('HEADER_INC', true);		// Define a variable so we know the header's been included

			//Redirect to Guildrules if user hasn't accepted them
			$intGuildrulesArticleID = $this->pdh->get('articles', 'resolve_alias', array('guildrules'));
			$blnGuildrules = ($intGuildrulesArticleID && $this->pdh->get('articles', 'published', array($intGuildrulesArticleID)));
			if ($this->user->is_signedin() && (int)$this->user->data['rules'] != 1 && $blnGuildrules){
				if(stripos($this->env->path, 'register') === false){
					redirect($this->controller_path_plain.'Register/'.$this->SID, false, false, false);
				}
			}

			//Redirect to Settings if user has not confirmed their email
			if($this->user->is_signedin() && (int)$this->user->data['user_email_confirmed'] < 1 && $this->config->get('cmsbridge_active') != 1){
				if(stripos($this->env->path, 'settings') === false && stripos($this->env->path, 'activate') === false){
					redirect($this->controller_path_plain.'Settings/'.$this->SID, false, false, false);
				}
				$this->core->message($this->user->lang('email_must_confirm_note'), '', 'hint');
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
			if (!$this->config->get('disable_xframe_header')){
				@header("X-Frame-Options: SAMEORIGIN");
			}

			// some style additions (header, background image..)
			$favicon = (isset($this->user->style['favicon_img'])) ? $this->user->style['favicon_img'] : '';
			switch(pathinfo($favicon, PATHINFO_EXTENSION)){
				case 'png': $favicon_type = 'image/png';
					break;
				case 'ico': $favicon_type = 'image/x-icon';
					break;
				default:
					$favicon = $this->server_path.'templates/'.$this->user->style['template_path'].'/images/favicon.png';
					$favicon_type = 'image/png';
			}

			$template_background_file = "";
			switch($this->user->style['background_type']){
				//Game
				case 1: $template_background_file = $this->server_path . 'games/' .$this->config->get('default_game') . '/template_background.jpg' ;
					break;

				//Own
				case 2:
					if ($this->user->style['background_img'] != ''){
						if (strpos($this->user->style['background_img'],'://') > 1){
							$template_background_file = $this->user->style['background_img'];
						} else {
							$template_background_file = $this->server_path .$this->user->style['background_img'];
						}
					}
					break;

				//Style
				default:
					if(is_file($this->root_path . 'templates/' . $this->user->style['template_path'] . '/images/template_background.png')){
						$template_background_file	= $this->server_path . 'templates/' . $this->user->style['template_path'] . '/images/template_background.png';
					} else {
						$template_background_file	= $this->server_path . 'templates/' . $this->user->style['template_path'] . '/images/template_background.jpg';
					}
			}
			if($template_background_file == ""){
				//Cannot find a background file, let's take the game specific
				$template_background_file = $this->server_path . 'games/' .$this->config->get('default_game') . '/template_background.jpg' ;
			}

			// add the template specific JS file
			$templatejs		= $this->root_path.'templates/'.$this->user->style['template_path'].'/'.$this->user->style['template_path'].'.js';
			if(is_file($templatejs)){
				$this->tpl->js_file($templatejs);
			}

			// add the custom JS file
			$customjs		= $this->root_path.'templates/'.$this->user->style['template_path'].'/custom.js';
			if(is_file($customjs)){
				$this->tpl->js_file($customjs);
			}

			if(strlen($this->config->get('global_js'))){
				$global_js = $this->config->get('global_js');
				$this->tpl->assign_var('FOOTER_CODE', $global_js);
			}

			//CSS
			$this->tpl->add_common_cssfiles();

			if ($this->config->get('pk_maintenance_mode') && $this->user->check_auth('a_', false)){
				$this->global_warning($this->user->lang('maintenance_mode_warn'), 'fa-cog');
			}

			if(defined('EQDKP_UPDATE') && EQDKP_UPDATE){
				$this->global_warning($this->user->lang('maintenance_mode_noauth_warn'), 'fa-cog');
			}

			$s_in_admin		= (((defined('IN_ADMIN') ) ? IN_ADMIN : false) && ($this->user->check_auth('a_', false)) ) ? true : false;

			// Add a javascript code to make some checkboxes clickable through table rows
			$this->tpl->add_js("$('.trcheckboxclick tr').click(function(event) {
						if (event.target.type !== 'checkbox') {
							$(':checkbox', this).trigger('click');
						}
					});

					$(function(){
						var search = $('#loginarea_search');
						original_val = search.val();
						search.focus(function(){
							if($(this).val()===original_val){
								$(this).val('');
							}
						})
						.blur(function(){
							if($(this).val()===''){
								$(this).val(original_val);
							}
						});

					});", 'static_docready');

			if(strlen($this->user->style['portal_width']) > 2 && (strpos($this->user->style['portal_width'], '%') == false)){
				$this->tpl->add_js("
					$(function(){
						if($( 'body' ).hasClass( 'fixed_width' )){
							if($( window ).width() < ".intval($this->user->style['portal_width'])."){
								$( 'body' ).removeClass('fixed_width');
							}
						}

					});", 'static_docready');
			}

			$this->tpl->add_js("
					$('.paginationPageSelector').on('click', function(){
						var base_url = $(this).parent().parent().data('base-url');
						var pages = $(this).parent().parent().data('pages');
						var per_page = $(this).parent().parent().data('per-page');
						var hint = '".$this->user->lang('pagination_goto_hint')."';
						hint = hint.replace(/PAGE/g, pages);

						$('<div></div>')
								.html('<fieldset class=\"settings mediumsettings\"><label>".$this->user->lang('pagination_goto').":</label><pre><input type=\"number\" size=\"10\" maxlength=\"30\" class=\"input\" id=\"goToPageInput\" value=\"'+pages+'\" /><br />'+hint+'</pre></fieldset>')
								.dialog({
								bgiframe: true,
								modal: true,
								height: 250,
								width: 350,
								title: '".$this->user->lang('pagination_goto')."',
								buttons: {
									Ok: function() {
										var page = $('#goToPageInput').val();
										if (page > 0 && page <= pages){
											var start = (page-1)*per_page;
											window.location = base_url+start;
										}
										$(this).dialog('close');
									},
								}
						});
					});

					$('.clickToReveal > a').on('click', function(){
						$(this).hide();
						 $(this).parent().find('div').show();
					});
					", 'docready');


			//Embedds
			$this->tpl->add_js("
			$('div.embed-consent').each(function(index) {
				var provider = $(this).find('.embed-consent-provider').html();
				if(JQisLocalStorageNameSupported()) {
					var consent = localStorage.getItem('embedd_consent_'+provider);
					if(consent){
						var embeddedContent = $(this).find('.embed-consent-content').html();
						var decoded = $('<div/>').html(embeddedContent).text();

						$(this).html(decoded);
						$(this).trigger('load');
					} else {
						//show message
						var message = '".$this->jquery->sanitize($this->user->lang('embedd_consent'))."';
						var button = '".$this->jquery->sanitize($this->user->lang('embedd_consent_ok'))."';
						message = message.replace(/{PROVIDER}/g, provider);
						button = '<br /><br /><a class=\"button\"><i class=\"fa fa-lg fa-play-circle\"></i> '+button+'</a>';

						$(this).find('.embed-consent-message').html(message+button);
					}
				}
			});
			", 'eop');

			$this->tpl->add_js('
				function show_embedded_content(obj){
					var parent = $(obj).parent().parent().parent();
					var embeddedContent = parent.find(".embed-consent-content").html();
					var provider = parent.find(".embed-consent-provider").html();
					var decoded = $("<div/>").html(embeddedContent).text();
					parent.html(decoded);
					$(parent).trigger("load");
					if(JQisLocalStorageNameSupported()) {
						localStorage.setItem("embedd_consent_"+provider, 1);
					}
				}
			', "static");

			//Lightbox Zoom-Image
			$this->tpl->add_js("
			$('a.lightbox,  a[rel=\"lightbox\"]').each(function(index) {
				var image = $(this).html();
				var image_obj = $(this).find('img');
				var image_parent = image_obj.parent();
				var image_string = image_parent.html();

				var fullimage = $(this).attr('href');
				var imagetitle = image_obj.attr('alt');
				$(this).attr('title', imagetitle);

				var image_style = $(this).children().attr('style');
				if (image_style) {
					if (image_style == \"display: block; margin-left: auto; margin-right: auto;\") image_style = image_style + \" text-align:center;\";
					$(this).attr('style', image_style);
				}
				var randomId = parseInt(Math.random() * 1000);
				var zoomIcon = '<div class=\"image_resized\" onmouseover=\"$(\'#imgresize_'+randomId+'\').show()\" onmouseout=\"$(\'#imgresize_' +randomId+'\').hide()\" style=\"display:inline-block;\"><div id=\"imgresize_'+randomId+'\" class=\"markImageResized\"><a title=\"'+imagetitle+'\" href=\"'+fullimage+'\" class=\"lightbox\"><span class=\"fa-stack fa-lg\"><i class=\"fa fa-square fa-stack-2x image_zoom\"></i><i class=\"fa fa-search-plus fa-stack-1x fa-inverse\"></i></span><\/a><\/div>'+image_string+'<\/div>';
				$(this).html(zoomIcon);
			});
			", 'static_docready');

			// global qtip
			$this->jquery->qtip(".coretip", "return $(this).attr('data-coretip');", array('contfunc'=>true, 'width'=>200));
			$this->jquery->qtip(".coretip-sticky", "return $(this).attr('data-coretip');", array('contfunc'=>true, 'width'=>200, 'sticky'=>true));
			$this->jquery->qtip(".coretip-large", "return $(this).attr('data-coretip');", array('contfunc'=>true, 'width'=>400));
			$this->jquery->qtip(".coretip-left", "return $(this).attr('data-coretip');", array('contfunc'=>true, 'width'=>280, 'my'	=> 'top right', 'at' => 'bottom right'));
			$this->jquery->qtip(".coretip-right", "return $(this).attr('data-coretip');", array('contfunc'=>true, 'width'=>280, 'my'	=> 'top left', 'at' => 'bottom left'));

			//Portal Output
			$intPortalLayout = ($this->portal_layout != NULL) ? $this->portal_layout : 1;
			$intPortalLayout = ($this->config->get('mobile_portallayout') && strlen($this->config->get('mobile_portallayout')) && $this->env->agent->mobile && registry::get_const('mobile_view')) ? $this->config->get('mobile_portallayout') : $intPortalLayout;
			$this->portal->module_output($intPortalLayout);

			//Registration Link
			$registerLink = '';
			if ( ! $this->user->is_signedin() && intval($this->config->get('enable_registration'))){
				//CMS register?
				if ($this->config->get('cmsbridge_active') == 1 && strlen($this->config->get('cmsbridge_reg_url'))){
					$registerLink = $this->createLink($this->handle_link($this->config->get('cmsbridge_reg_url'),$this->user->lang('menu_register'),$this->config->get('cmsbridge_embedded'),'BoardRegister', '', '', 'fa fa-user-plus fa-lg', ''), 'register');
				} else {
					$registerLink = $this->createLink(array('link' => $this->controller_path_plain.'Register' . $this->routing->getSeoExtension().$this->SID, 'text' => $this->user->lang('menu_register'), 'icon' => 'fa fa-user-plus fa-lg'), 'register');
				}
			}

			$arrPWresetLink = $this->handle_link($this->config->get('cmsbridge_pwreset_url'),$this->user->lang('lost_password'),$this->config->get('cmsbridge_embedded'),'LostPassword');
			$strAvatarImg = ($this->user->is_signedin() && $this->pdh->get('user', 'avatarimglink', array($this->user->id))) ? $this->pfh->FileLink($this->pdh->get('user', 'avatarimglink', array($this->user->id)), false, 'absolute') : $this->server_path.'images/global/avatar-default.svg';
			$strHeaderLogoPath	= "templates/".$this->user->style['template_path']."/images/";

			// the logo...
			if(is_file($this->pfh->FolderPath('','files').$this->config->get('custom_logo'))){
				$headerlogo	= $this->pfh->FolderPath('','files', 'absolute').$this->config->get('custom_logo');
			} else if(file_exists($this->root_path.$strHeaderLogoPath.'logo.svg')){
				$headerlogo	= $this->env->link.$strHeaderLogoPath.'logo.svg';
			} else $headerlogo = "";

			// Load the jQuery stuff
			$this->addCommonTemplateVars();
			$this->tpl->assign_vars(array(
				'PAGE_TITLE'				=> $this->pagetitle($this->page_title),
				'FAVICON'					=> $favicon,
				'FAVICON_TYPE'				=> $favicon_type,
				'TEMPLATE_BANNER'			=> ((isset($this->user->style['banner_img'])) ? $this->user->style['banner_img'] : ''),
				'HEADER_LOGO'				=> $headerlogo,
				'TEMPLATE_BACKGROUND'		=> $template_background_file,

				'USER_AVATAR'				=> $strAvatarImg,
				'AUTH_LOGIN_BUTTON'			=> (!$this->user->is_signedin()) ? implode(' ', $this->user->handle_login_functions('login_button')) : '',
				'S_NORMAL_HEADER'			=> ($this->header_format != 'simple') ? true : false,
				'S_NORMAL_FOOTER'			=> ($this->header_format != 'simple') ? true : false,
				'S_NO_HEADER_FOOTER'		=> ($this->header_format == 'none') ? true : false,
				'S_IN_ADMIN'				=> $s_in_admin,
				'S_SEARCH'					=> $this->user->check_auth('u_search', false),
				'FIRST_C'					=> true,
				'T_PORTAL_WIDTH'			=> $this->user->style['portal_width'],
				'T_COLUMN_LEFT_WIDTH'		=> $this->user->style['column_left_width'],
				'T_COLUMN_RIGHT_WIDTH'		=> $this->user->style['column_right_width'],
				'T_LOGO_POSITION'			=> $this->user->style['logo_position'],
				'T_BACKGROUND_TYPE'			=> $this->user->style['background_type'],
				'T_BACKGROUND_COLOR'		=> $this->user->style['body_background_color'],
				'T_BACKGROUND_POSITION'		=> ($this->user->style['background_pos'] == 'normal') ? 'scroll' : 'fixed',
				'T_MENU_BACKGROUND_COLOR'=> $this->user->style['menu_background_color'],
				'S_REGISTER'				=> (int)$this->config->get('enable_registration'),
				'U_LOGOUT'					=> $this->controller_path.'Login/Logout'.$this->routing->getSeoExtension().$this->SID.'&amp;link_hash='.$this->user->csrfGetToken("login_pageobjectlogout"),
					'U_CHARACTERS'				=> ($this->user->is_signedin() && !$this->config->get('disable_guild_features') && $this->user->check_auths(array('u_member_man', 'u_member_add', 'u_member_conn', 'u_member_conn_free','u_member_del'), 'OR', false)) ? $this->controller_path.'MyCharacters' . $this->routing->getSeoExtension().$this->SID : '',
				'U_REGISTER'				=> $registerLink,
				'MAIN_MENU'					=> $this->build_menu_ul($this->build_menu_array(false)),
				'MAIN_MENU_MOBILE'			=> $this->build_menu_ul($this->build_menu_array(false), 'mainmenu-mobile'),
				'BREADCRUMB'				=> $this->build_breadcrumb(),
				'PAGE_CLASS'				=> 'page-'.$this->clean_url($this->env->get_current_page(false)).' controller-'.registry::get_const('pageobject'),
				'BODY_CLASS'				=> $this->body_class,
				'TEMPLATE_CLASS'			=> str_replace(array('.html', '/'), array('', '_'), $this->template_path.$this->template_file),
				'BROWSER_CLASS'				=> (!registry::get_const('mobile_view')) ? str_replace(" mobile", "", $this->env->agent->class) : $this->env->agent->class,
				'S_SHOW_PWRESET_LINK'		=> ($this->config->get('cmsbridge_active') == 1 && !strlen($this->config->get('cmsbridge_pwreset_url'))) ? false : true,
				'U_PWRESET_LINK'			=> ($this->config->get('cmsbridge_active') == 1 && strlen($this->config->get('cmsbridge_pwreset_url'))) ? $this->createLink($arrPWresetLink) : '<a href="'.$this->controller_path."Login/LostPassword/".$this->SID."\">".$this->user->lang('lost_password').'</a>',
				'S_BRIDGE_INFO'				=> ($this->config->get('cmsbridge_active') ==1) ? true : false,
				'U_USER_PROFILE'			=> $this->routing->build('user', (isset($this->user->data['username']) ? sanitize($this->user->data['username']) : $this->user->lang('anonymous')), 'u'.$this->user->id),
				'HONEYPOT_VALUE'			=> $this->user->csrfGetToken("honeypot"),
				'S_REPONSIVE'				=> registry::get_const('mobile_view'),
				'CURRENT_PAGE'				=> sanitize($this->env->request),
				'S_STYLECHANGER'			=> (!intval($this->config->get('default_style_overwrite')) && count(register('pdh')->get('styles', 'styles', array(0, false))) > 1) ? true : false,
				'USER_IS_AWAY'				=> ($this->user->data['user_id'] > 0) ? $this->pdh->get('calendar_raids_attendees', 'user_awaymode', array($this->user->data['user_id'])) : false,
				'S_DISABLE_GUILD_FEATURES'	=> ($this->config->get('disable_guild_features') == 1) ? true : false,
			));

			if (isset($this->page_body) && $this->page_body == 'full'){
				$this->tpl->assign_vars(array(
					'S_PORTAL_LEFT'	=> false,
					'S_PORTAL_RIGHT'=> false,
					'PORTAL_MIDDLE' => '',
					'PORTAL_BOTTOM' => '',
				));
			}

			if (isset($this->page_body) && $this->page_body == 'full_width'){
				$this->tpl->assign_vars(array(
						'S_PORTAL_LEFT'	=> false,
						'S_PORTAL_RIGHT'=> false,
				));
			}

			// show the full head
			if ($this->header_format != 'simple'){
				// System Message if user has no assigned members
				if($this->pdh->get('member', 'connection_id', array($this->user->data['user_id'])) < 1 && ($this->user->is_signedin()) && ($this->user->data['hide_nochar_info'] != '1') && $this->user->check_auths(array('u_member_man', 'u_member_add', 'u_member_conn', 'u_member_del'), 'OR', false) && !$this->config->get('disable_guild_features')){
					$message = '<a href="'.$this->routing->build('mycharacters').'">'.$this->user->lang('no_connected_char').'</a>';
					$message .= '<br /><br /><a href="'.$this->routing->build('mycharacters').'&hide_info=true">'.$this->user->lang('no_connected_char_hide').'</a>';
					$this->message($message);
				}

				// System Message for admins if a requirement was not met
				if($this->user->check_auth('a_', false)){
					if($this->requirements->getCounts() > 0){
						$this->message('<a href="'.$this->server_path.'admin/index.php">'.$this->user->lang('requirements_warningmessage').'</a>', '', 'warning');
					}
				}
			}
			$this->tpl->add_js("var mmocms_header_type = '".$this->header_format."';", 'head_top');

			//Template Vars for Group Memberships
			$arrGroupmemberships = $this->acl->get_user_group_memberships($this->user->id);
			foreach($arrGroupmemberships as $groupID => $status){
				if ($status) $this->tpl->assign_var("S_AUTH_GROUP_".$groupID, true);
			}

			$this->mycharacters();

			if($this->user->is_signedin() && $this->user->check_auth('a_', false)){
				include_once($this->root_path.'core/admin_functions.class.php');
				$admin_functions = register('admin_functions');
				$admin_functions->setAdminTooltip();
			}

			if($this->hooks->isRegistered('coreclass_page_header')){
				$this->hooks->process('coreclass_page_header');
			}
		}

		public function addCommonTemplateVars(){
			$arrLanguages = $this->user->getAvailableLanguages(false, true);
			$this->tpl->assign_vars(array(
					'MAIN_TITLE'				=> $this->config->get('main_title'),
					'SUB_TITLE'					=> $this->config->get('sub_title'),
					'GUILD_TAG'					=> $this->config->get('guildtag'),
					'META_KEYWORDS'				=> ($this->config->get('meta_keywords') && strlen($this->config->get('meta_keywords'))) ? $this->config->get('meta_keywords') : $this->config->get('guildtag').', '.$this->config->get('default_game').((strlen($this->config->get('servername'))) ? ', '.$this->config->get('servername') : ''),
					'META_DESCRIPTION'			=> ($this->config->get('meta_description') && strlen($this->config->get('meta_description'))) ? $this->config->get('meta_description') : $this->config->get('guildtag'),
					'EQDKP_ROOT_PATH'			=> $this->server_path,
					'EQDKP_IMAGE_PATH'			=> $this->server_path.'images/',
					'EQDKP_CONTROLLER_PATH'		=> $this->controller_path,
					'TEMPLATE_PATH'				=> $this->server_path . 'templates/' . $this->user->style['template_path'],
					'USER_TIME'					=> $this->time->user_date($this->time->time, true, false, true, true, true),
					'USER_ID'					=> $this->user->id,
					'USER_NAME'					=> isset($this->user->data['username']) ? sanitize($this->user->data['username']) : $this->user->lang('anonymous'),
					'S_POINTS_DISABLED'			=> (!$this->config->get('enable_points')) ? true : false,
					'S_ADMIN'					=> $this->user->check_auth('a_', false),
					'SID'						=> ((isset($this->SID)) ? $this->SID : '?' . 's='),
					'GAME'						=> $this->config->get('default_game'),
					'GAME_LANGUAGE'				=> $this->config->get('game_language'),
					'S_LOGGED_IN'				=> ($this->user->is_signedin()) ? true : false,
					'CSRF_TOKEN'				=> '<input type="hidden" name="'.$this->user->csrfPostToken().'" value="'.$this->user->csrfPostToken().'"/>',
					'SEO_EXTENSION'				=> $this->routing->getSeoExtension(),
					'USER_LANGUAGE'				=> $this->user->lang_name,
					'USER_LANGUAGE_NAME'		=> $arrLanguages[$this->user->lang_name],
					'S_DKP_EASYMODE'				=> $this->config->get('dkp_easymode'),
			));
		}

		public function createLink($arrLinkData, $strCssClass = '', $blnHrefOnly=false){
			$target = '';
			if (isset($arrLinkData['target']) && strlen($arrLinkData['target'])){
				$target = ' target="'.$arrLinkData['target'].'"';
			}
			$icon = '';
			if (isset($arrLinkData['icon']) && strlen($arrLinkData['icon'])){
				$icon = '<i class="'.$arrLinkData['icon'].'"></i> ';
			}
			$strHref = ((isset($arrLinkData['plus_link']) && $arrLinkData['plus_link']==true && $arrLinkData['link']) ? $arrLinkData['link'] : $this->server_path . $arrLinkData['link']);
			if ($strHref == $this->server_path.'#') $strHref = "#";

			if ($blnHrefOnly) return $strHref;

			return '<a href="' . $strHref . '"'.$target.' class="'.$strCssClass.'" itemprop="url">' . $icon . '<span itemprop="name">'.$arrLinkData['text'] . '</span></a>';
		}

		//Returns all possible Menu Items
		public function menu_items($show_hidden = false){
			if(isset($this->cache['menu_items'.(($show_hidden) ? '_h' : '')])) return $this->cache['menu_items'.(($show_hidden) ? '_h' : '')];
			$arrItems = array(
				array('link' => $this->controller_path_plain.$this->SID,				'text' => $this->user->lang('home'), 'static' => 1, 'default_hide' => 1, 'hidden' => 1),
				array('link' => $this->controller_path_plain.'User'.$this->routing->getSeoExtension().$this->SID, 'text' => $this->user->lang('user_list'),'check' => 'u_userlist', 'static' => 1, 'default_hide' => 1, 'hidden' => 1),
			);

			//Articles & Categories
			$arrCategoryIDs = $this->pdh->sort($this->pdh->get('article_categories', 'id_list', array()), 'article_categories', 'sort_id', 'asc');
			foreach($arrCategoryIDs as $cid){
				if (!$this->pdh->get('article_categories', 'published', array($cid))) continue;

				if ($cid != 1) $arrItems[] = array('link' => $this->controller_path_plain.$this->pdh->get('article_categories', 'path', array($cid)), 'text' => $this->pdh->get('article_categories', 'name', array($cid)), 'category' => true, 'id' => $cid);
				$arrArticles = $this->pdh->get('articles', 'id_list', array($cid));
				foreach($arrArticles as $articleID){
					if (!$this->pdh->get('articles', 'published', array($articleID))) continue;
					$arrItems[] = array('link' => $this->controller_path_plain.$this->pdh->get('articles', 'path', array($articleID)), 'text' => $this->pdh->get('articles', 'title', array( $articleID)), 'article' => true, 'id' => $articleID);
				}
			}

			//Plugins
			if (is_object($this->pm)){
				$plugin_menu = $this->pm->get_menus('main');
				if(is_array($plugin_menu)){
					foreach($plugin_menu as $key=>$val){
						$plugin_menu[$key]['plugin'] = 1;
					}
				}
				$arrItems = (is_array($plugin_menu)) ? array_merge($arrItems, $plugin_menu) : $arrItems;
			}

			//Forum
			if (strlen($this->config->get('cmsbridge_url')) > 0 && $this->config->get('cmsbridge_active') == 1){
				$inlineforum = $this->handle_link($this->config->get('cmsbridge_url'), $this->user->lang('forum'), $this->config->get('cmsbridge_embedded'), 'Board');
				$arrItems[]	= $inlineforum;
			}

			//Plus Links
			$arrItems = array_merge($arrItems, $this->pdh->get('links', 'menu', array($show_hidden)));

			//Hooks
			if ($this->hooks->isRegistered('main_menu_items')){
				$arrHooks = $this->hooks->process('main_menu_items', array());
				foreach($arrHooks as $arrHookItems){
					if (is_array($arrHookItems)) $arrItems = array_merge($arrItems, $arrHookItems);
				}
			}
			$this->cache['menu_items'.(($show_hidden) ? '_h' : '')] = $arrItems;
			return $arrItems;
		}

		public function build_link_hash($arrLinkData){

			if (isset( $arrLinkData['category'])) {
				return md5('category'.$arrLinkData['id']);
			} elseif (isset( $arrLinkData['category']) ) {
				return md5('article'.$arrLinkData['id']);
			} elseif (isset($arrLinkData['id'])) {
				return md5('pluslink'.$arrLinkData['id']);
			} else {
				$toHash = $this->user->removeSIDfromString($arrLinkData['link']);
				$toHash = str_replace(array("index.php/", ".html", ".php"), "", $toHash);
				if (substr($toHash, -1) == "?") $toHash = substr($toHash, 0, -1);
				if (substr($toHash, -1) == "/") $toHash = substr($toHash, 0, -1);
				return md5($toHash);

			}
		}

		public function build_menu_array($show_hidden = true, $blnOneLevel = false){
			$arrItems = $this->menu_items($show_hidden);
			$arrSortation = $this->config->get('mainmenu');
			$arrExistingHidden = ($this->config->get('mainmenu_hidden')) ? json_decode($this->config->get('mainmenu_hidden'), true) :  array();

			$arrHashArray = array();

			foreach ($arrItems as $key => $item){
				$strHash = $this->build_link_hash($item);
				$arrItems[$key]['_hash'] = $this->build_link_hash($item);
				$arrItems[$key]['hidden'] = (isset($arrExistingHidden[$arrItems[$key]['_hash']])) ? 1 : 0;
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
				if (!isset($arrHashArray[$hash])) {
					$show = false;
				}
				unset($arrToDo[$hash]);
				if ($show) {
					if ($hidden) $arrHashArray[$hash]['hidden'] = 1;
					$arrHashArray[$hash]['depth'] = 0;
					$arrOut[$key] = $arrHashArray[$hash];
					$arrOutOneLevel[] = $arrHashArray[$hash];
				}
				//Second Level
				if (isset($item['_childs']) && is_array($item['_childs'])){
					$secondlevel_show = $show;

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
								$show = true;
							}
						}
						$show = $secondlevel_show;
					}
				}
			}

			foreach($arrToDo as $hash => $item){
				$item['hidden'] = (isset($item['article']) || isset($item['category']) || isset($item['default_hide'])) ? 1 : 0;
				$item['hidden'] = (isset($arrExistingHidden[$item['_hash']])) ? 1 : $item['hidden'];
				if (!$show_hidden && $item['hidden']) continue;
				$arrOut[] = $item;
				$arrOutOneLevel[] = $item;
			}

			$arrOut = $this->hooks->process("menu", $arrOut, true);
			$arrOutOneLevel = $this->hooks->process("menu_onelevel", $arrOutOneLevel, true);

			return ($blnOneLevel) ? $arrOutOneLevel: $arrOut;
		}

		/**
		 * Build a Menu
		 *
		 * @param $arrMenuItems			Array with menu data
		 * @param $strCssClass			Add CSS class
		 * @param $blnAdminMenu			Is Admin Menu
		 * @param $strImagePath			Image path for menu images
		 * @param $blnDefaultImage		Do not use a default image
		 */
		public function build_menu_ul($arrMenuItems, $strCssClass = '', $blnAdminMenu = false, $strImagePath = '', $blnDefaultImage = false){
			$strCssClass = (empty($strCssClass) && !$blnAdminMenu)? 'mainmenu' : (($blnAdminMenu && empty($strCssClass))? 'adminmenu' : $strCssClass);
			$html  = '<ul class="'.$strCssClass.'">';

			// Adminmenu
			if($blnAdminMenu){
				// Header row
				if(is_array($arrMenuItems)){
					foreach($arrMenuItems as $k => $v){
						// Restart next loop if the element isn't an array we can use
						if ( !is_array($v) ){continue;}

						$header_row = '<li><a href="#" class="sub-menu-arrow">'.$this->core->icon_font((isset($v['icon'])) ? $v['icon'] : ((isset($v['img']) ? $v['img'] : (($blnDefaultImage) ? 'fa-puzzle-piece' : ''))), 'fa-lg fa-fw', $strImagePath).' '.$v['name'].'</a>
											<ul class="sub-menu">';

						// Generate the Menues
						$sub_rows = '';
						if(is_array($v)){
							foreach ( $v as $k2 => $row ){
								$admnsubmenu = ((isset($row['link']) && $row['text']) ? false : true);
								// Ignore the first element (header)
								if ( ($k2 == 'name' || $k2 == 'icon') &&  !$admnsubmenu){
									continue;
								}

								// the extension submenues
								if($admnsubmenu) {
									// build the icons
									$icon = $this->core->icon_font((isset($row['icon'])) ? $row['icon'] : ((isset($row['img']) ? $row['img'] : (($blnDefaultImage) ? '' : 'fa-puzzle-piece'))), 'fa-lg fa-fw', $strImagePath);
									$plugin_header_row = '<li><a href="#" class="sub-menu-arrow">'.$icon.' '.((isset($row['name'])) ? $row['name'] : 'UNKNOWN').'</a>
														<ul class="sub-menu">';
									// Submenu
									$plugin_sub_row = '';
									if(!isset($row['link']) && !isset($row['text'])){
										if(is_array($row)){
											foreach($row as $k3 => $row2){
												if ($k3 == 'name' || $k3 =='icon'){
													continue;
												}

												if ($row2['check'] == '' || ((is_array($row2['check'])) ? $this->user->check_auths($row2['check'][1], $row2['check'][0], false) : $this->user->check_auth($row2['check'], false))){
													$subsub_icon = $this->core->icon_font((isset($row2['icon'])) ? $row2['icon'] : ((isset($row2['img']) ? $row2['img'] : (($blnDefaultImage) ? '' : ''))), 'fa-lg fa-fw', $strImagePath);
													$plugin_sub_row .= '<li><a href="'.$this->root_path.$row2['link'].'">';
													$plugin_sub_row .= $subsub_icon.' '.$row2['text'].'</a></li>';
												}
											}
										}
									}
									if(strlen($plugin_sub_row) > 0) $sub_rows .= $plugin_header_row.$plugin_sub_row.'</ul></li>';
								}else{
									if (($row['check'] == '' || ((is_array($row['check'])) ? $this->user->check_auths($row['check'][1], $row['check'][0], false) : $this->user->check_auth($row['check'], false))) && (!isset($row['check2']) || $row['check2'] == true)){
										$subicon	= $this->core->icon_font((isset($row['icon'])) ? $row['icon'] : ((isset($row['img']) ? $row['img'] : (($blnDefaultImage) ? '' : ''))), 'fa-lg fa-fw', $strImagePath);
										$sub_rows .= '<li><a href="'.$this->root_path.$row['link'].'">';
										$sub_rows .= $subicon.' '.$row['text'].'</a></li>';
									}
								}
							}
						}

						if(strlen($sub_rows)) $html .= $header_row.$sub_rows.'</ul></li>';
					}
				}
				$html .= '</ul>';
				return $html;

			// Mainmenu
			}else{
				foreach($arrMenuItems as $k => $v){
					if ( !is_array($v) ) continue;

					if (!isset($v['childs'])){
						if ( $this->check_url_for_permission($v)) {
							if($v['link'] === '#') continue;
							$class = $this->clean_url($v['link']);
							if (!strlen($class)) $class = "entry_".$this->clean_url($v['text']);
							$html .= '<li class="link_li_'.$class.'"><i class="link_i_'.$class.'"></i>'.$this->createLink($v, 'link_'.$class).'</li>';
						} else {
							continue;
						}

					} else {
						$tmpHtml = $subHtml = "";
						if ( $this->check_url_for_permission($v)) {
							$class = $this->clean_url($v['link']);
							if (!strlen($class)) $class = "entry_".$this->clean_url($v['text']);
							$tmpHtml .= '<li class="link_li_'.$class.'"><i class="link_i_'.$class.'"></i>'.$this->createLink($v, 'link_'.$class.' sub-menu-arrow').'<ul class="sub-menu">';
						} else {
							continue;
						}

						foreach($v['childs'] as $k2 => $v2){
							if (!isset($v2['childs'])){
								if ( $this->check_url_for_permission($v2)) {
									$class = $this->clean_url($v2['link']);
									if (!strlen($class)) $class = "entry_".$this->clean_url($v2['text']);
									$subHtml .= '<li class="link_li_'.$class.'"><i class="link_i_'.$class.'"></i>'.$this->createLink($v2, 'link_'.$class).'</li>';
								} else {
									continue;
								}
							} else {
								if ( $this->check_url_for_permission($v2)) {
									$class = $this->clean_url($v2['link']);
									if (!strlen($class)) $class = "entry_".$this->clean_url($v2['text']);
									$subHtml .= '<li class="link_li_'.$class.'"><i class="link_i_'.$class.'"></i>'.$this->createLink($v2, 'link_'.$class.' sub-menu-arrow').'<ul class="sub-menu">';
								} else {
									continue;
								}

								foreach($v2['childs'] as $k3 => $v3){
									if ( $this->check_url_for_permission($v3)) {
										$class = $this->clean_url($v3['link']);
										if (!strlen($class)) $class = "entry_".$this->clean_url($v3['text']);
										$subHtml .= '<li class="link_li_'.$class.'"><i class="link_i_'.$class.'"></i>'.$this->createLink($v3, 'link_'.$class).'</li>';
									} else {
										continue;
									}
								}

								$subHtml .= '</ul></li>';
							}

						}

						$tmpHtml .= $subHtml;
						$tmpHtml .= '</ul></li>';

						if(!(strlen($subHtml) === 0 && $v['link'] === '#')){
							$html .= $tmpHtml;
						}
					}

				}

				$html .= '</ul>';
				return str_replace("<ul></ul>", "", $html);
			}
		}

		/**
		 * Build the Breadcrumb
		 */
		public function build_breadcrumb(){
			if($this->page_path === false) return '';
			if(is_string($this->page_path)) return $this->page_path;
			if($this->page_path == [[]]) $this->page_path = [];

			$arrBreadcrumb = $this->page_path;

			$html = '<ul class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">';

			$html .= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a href="'.$this->controller_path.$this->SID.'" itemtype="http://schema.org/Thing" itemprop="item"><i class="fa fa-home"></i><span itemprop="name" style="display:none;">Home</span></a>
					<meta itemprop="position" content="1" /></li>';

			foreach($arrBreadcrumb as $key => $arrItem){
				$html .= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a href="'.$arrItem['url'].'" itemtype="http://schema.org/Thing" itemprop="item"><span itemprop="name">'.$arrItem['title'].'</span></a>
				<meta itemprop="position" content="'.($key+2).'" /></li>';
			}
			return $html.'</ul>';
		}

		/**
		 * Build a ButtonDropDown Menu
		 *
		 * Use in your $arrMenuItems var everytime ['type','text','perm'] for button, select, javascript,.. output text & permissions
		 * icon		[all]				(optional) supports png and fa-class icons
		 * append	[javascript, link]	(optional) and add your own html code to the default code
		 * js		[javascript]		(required) add your own onclick javascript code
		 * name		[button, select]	(required) name attribute for the primary submit
		 * options	[select]			(required) ex: array('name_of_input', array('opt_1', 'opt_2', ...))
		 *
		 * @param $strButtonText		Text of the button
		 * @param $arrMenuItems			Array with menu data ['type','text','perm',.. 'icon','append','name','js','options']
		 * @param $strCssClass			Add CSS class
		 * @param $strCssID				Add CSS id for checkbox listener
		 * @param $arrCheckBoxListener	Use checkbox listener to rename by count
		 * @return string html
		 */
		public function build_dropdown_menu($strButtonText, $arrMenuItems, $strCssClass = '', $strCssID = '', $arrCheckBoxListener = array(), $addClear=true){
			$strCssClass= (!empty($strCssClass))? $strCssClass : 'floatLeft';
			$strCssID	= (!empty($strCssID))? $strCssID : $hash = 'ddm_'.md5(serialize($arrMenuItems));
			$html		= '<div id="'.$strCssID.'" class="btn-ddm '.$strCssClass.'"><button onclick="return false;">'.$strButtonText.'</button><ul>';

			foreach($arrMenuItems as $key => $arrMenuItem){
				if($arrMenuItem['perm']){
					$arrMenuItem['icon'] = (isset($arrMenuItem['icon']) && !empty($arrMenuItem['icon']))? $this->icon_font($arrMenuItem['icon'], 'fa-lg').'&nbsp;' : '';
					if(!isset($arrMenuItem['type'])) $arrMenuItem['type'] = "";
					switch($arrMenuItem['type']){
						case 'javascript':
							$html .= '<li data-type="javascript"><a href="javascript:void(0);" onclick="'.$arrMenuItem['js'].'">'.$arrMenuItem['icon'].$arrMenuItem['text'].'</a>'.((isset($arrMenuItem['append'])) ? $arrMenuItem['append'] : '').'</li>';
							break;

						case 'button':
							$html .= '<li data-type="button"><a href="javascript:void(0);" onclick="$(this).next(\'button\').click();">'.$arrMenuItem['icon'].$arrMenuItem['text'].'</a><button name="'.$arrMenuItem['name'].'" type="submit" style="display:none;" /></li>';
							break;

						case 'select':
							$html .= '<li data-type="select"><a href="javascript:void(0);">'.$arrMenuItem['icon'].$arrMenuItem['text'].'</a><ul>';
							foreach($arrMenuItem['options'][1] as $option_value => $option_text){
								$html .= '<li data-value="'.$option_value.'"><a href="javascript:void(0);" onclick="$(this).parent().parent().next(\'input\').val(\''.$option_value.'\');$(this).parent().parent().next().next(\'button\').click();">'.$option_text.'</a></li>';
							}
							$html .= '</ul><input name="'.$arrMenuItem['options'][0].'" value="" type="hidden"><button name="'.$arrMenuItem['name'].'" type="submit" style="display:none;" /></li>';
							break;

						default: $html .= '<li data-type="link"><a href="'.$arrMenuItem['link'].'">'.$arrMenuItem['icon'].$arrMenuItem['text'].'</a>'.((isset($arrMenuItem['append'])) ? $arrMenuItem['append'] : '').'</li>';
					}
				}
			}
			$html .= '</ul></div>';

			if($addClear){
				$html .= '<div class="clear"></div>';
			}

			if(count($arrCheckBoxListener)){
				foreach($arrCheckBoxListener as $strCheckBox) {
					$this->tpl->add_js("
						$('".$strCheckBox."').on('change', function(){
							var count = 0;
							if ($('".$strCheckBox."').prop(\"multiple\")){
								$('".$strCheckBox." :selected').each(function(i, selected){
									count += 1;
								});
							} else {
								$('".$strCheckBox."').each(function(){
									if (this.checked){
										count += 1;
									}
								});
							}
							$('#".$strCssID.".btn-ddm > button').html((count > 0)? count + ' ".$strButtonText."' : '".$strButtonText."');
						});
					", 'docready');
				}
			}

			return str_replace("<ul></ul>", "", $html);
		}

		public function clean_url($strUrl){
			return preg_replace("/[^a-zA-Z0-9_]/","",utf8_strtolower($this->user->removeSIDfromString($strUrl)));
		}

		public function check_url_for_permission($arrLinkData){
			if(isset($arrLinkData['check'])){
				$blnAuthCheck = (is_array($arrLinkData['check'])) ? $this->user->check_auths($arrLinkData['check'][1], $arrLinkData['check'][0], false) : $this->user->check_auth($arrLinkData['check'], false);
			}

			if ( (empty($arrLinkData['check'])) || ($blnAuthCheck)) {
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

				if (isset($arrLinkData['article']) && $arrLinkData['article']){
					$arrPermission = $this->pdh->get('articles', 'user_permissions', array(intval($arrLinkData['id']), $this->user->id));
					if (!$arrPermission['read']) return false;
				}

				if (isset($arrLinkData['category']) && $arrLinkData['category']){
					$arrPermission = $this->pdh->get('article_categories', 'user_permissions', array(intval($arrLinkData['id']), $this->user->id));
					if (!$arrPermission['read']) return false;
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
				case '4':
				case '5':	{
							switch($wrapper_id){
								case 'Board':
								case 'LostPassword':
								case 'BoardRegister':
									$wrapperText = $wrapper_id; $wrapperID = false;
								break;
								default: $wrapperText = $text;$wrapperID = $wrapper_id;
							}

							$url = $this->routing->build("external", $wrapperText, $wrapperID, true, true);
						}
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

		//Everything that creates Notifications
		public function notifications(){
			if ($this->notifications) return;

			//Update Warnings
			if ($this->user->check_auths(array('a_extensions_man', 'a_maintenance'), 'or', false)){
				$objRepository = register("repository");
				if (count($objRepository->updates)){
					$arrUpdates = $objRepository->updates;
					if (isset($arrUpdates['pluskernel']) && $this->user->check_auth("a_maintenance", false)){
						$this->ntfy->add_persistent('eqdkp_core_update', $this->user->lang("pluskernel_new_version"), $this->server_path.'admin/manage_live_update.php'.$this->SID, 2, 'fa-cog');
						unset($arrUpdates['pluskernel']);
					}

					if (count($arrUpdates)){
						$text = "";
						foreach($arrUpdates as $id => $data){
							$text	.= "<br />".sprintf($this->user->lang('lib_pupd_updtxt_tt'), (($this->user->lang($data['plugin'])) ? $this->user->lang($data['plugin']) : $data['name']), $data['version'], $data['plugin'], $data['release']);
						}
						$this->ntfy->add_persistent('eqdkp_extensions_update', $this->user->lang("lib_pupd_intro").$text, $this->server_path.'admin/manage_extensions.php'.$this->SID, 2, 'fa-cogs');
					}
				}
			}

			//Check for unpublished articles
			$arrCategories = $this->pdh->get('article_categories', 'unpublished_articles_notify', array());
			if (count($arrCategories) > 0 && $this->user->check_auth('a_articles_man',false)){
				foreach($arrCategories as $intCategoryID => $intUnpublishedCount){
					$this->ntfy->add_persistent(
							'eqdkp_article_unpublished',
							sprintf($this->user->lang('notify_unpublished_articles'), $intUnpublishedCount, $this->pdh->get('article_categories', 'name', array($intCategoryID))),
							$this->server_path.'admin/manage_articles.php'.$this->SID.'&amp;c='.$intCategoryID,
							1,
							'fa-file'
					);
				}
			}

			//Admin Tasks
			$this->admin_tasks->createNotifications();


			//Do portal hook
			register('hooks')->process('portal', array($this->env->eqdkp_page));

			$this->notifications = true;
		}

		public function page_tail(){
			if ( !empty($this->template_path) ){
				$this->tpl->set_template($this->user->style['template_path'], '', $this->template_path);
			}

			$this->tpl->set_filenames(array(
				'body' => $this->template_file)
			);

			// Hiding the normal-footer-stuff, but show debug-info, since in normal usage debug mode is turned off, and for developing purposes debug-tabs help alot info if header is set to none..
			$commonDescription = ($this->config->get('meta_description') && strlen($this->config->get('meta_description'))) ? $this->config->get('meta_description') : $this->config->get('guildtag');

			$this->tpl->assign_vars(array(
				'META_DESCRIPTION'			=> (strlen($this->description)) ? $this->description : $commonDescription,
				'S_NORMAL_FOOTER' 			=> ($this->header_format != 'simple') ? true : false,
				'EQDKP_PLUS_COPYRIGHT'		=> $this->Copyright())
			);

			//Language Switcher
			$arrLanguages = $this->user->getAvailableLanguages(false, true);
			$url = (preg_replace('#\&lang\=([a-zA-Z]*)#', "", $this->env->request));
			foreach($arrLanguages as $strKey => $strLangname){
				$this->tpl->assign_block_vars('languageswitcher_row', array(
						'LANGNAME'	=> $strLangname,
						'LINK'		=> sanitize($url).((strpos($url, "?") === false) ? '?' : '&').'lang='.$strKey,
				));
			}

			//Call Social Plugins
			$default_img_link	= $this->env->buildlink()."templates/".$this->user->style['template_path']."/images/";
			$default_img_link_rel = $this->root_path."templates/".$this->user->style['template_path']."/images/";
			$image = ((is_file($this->pfh->FolderPath('logo','eqdkp').$this->config->get('custom_logo'))) ? $this->env->buildlink().$this->pfh->FolderPath('logo','eqdkp', true).$this->config->get('custom_logo') : ((file_exists($default_img_link_rel."logo.svg")) ? $default_img_link."logo.svg": $default_img_link."logo.png"));
			$image = ($this->image != '') ? $this->image : $image;

			$description = ($this->description != '') ? $this->description : (($this->config->get('meta_description') && strlen($this->config->get('meta_description'))) ? $this->config->get('meta_description') : $this->config->get('guildtag'));
			register('socialplugins')->callSocialPlugins($this->page_title, $description, $image);

			//Notifications
			$arrNotifications = $this->ntfy->createNotifications();
			$this->tpl->assign_vars(array(
				'NOTIFICATION_COUNT_RED'	=> $arrNotifications['count2'],
				'NOTIFICATION_COUNT_YELLOW' => $arrNotifications['count1'],
				'NOTIFICATION_COUNT_GREEN' 	=> $arrNotifications['count0'],
				'NOTIFICATION_COUNT_TOTAL'	=> $arrNotifications['count'],
				'NOTIFICATIONS'				=> $arrNotifications['html'],
			));

			if(DEBUG) {
				$this->user->objLanguage->output_unused();
				$log = $this->pdl->get_log();
				$this->tpl->assign_vars(array(
					'S_SHOW_DEBUG'			=> true,
					'S_SHOW_QUERIES'		=> true,
					'EQDKP_RENDERTIME'		=> pr('', 2),
					'EQDKP_QUERYCOUNT'		=> $this->db->query_count,
					'EQDKP_MEM_PEAK'		=> number_format(memory_get_peak_usage(true)/1024, 0, '.', ',').' kb',
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
						$debug_tabs .= '<table class="table fullwidth colorswitch scrollable-x">';
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

			//Add custom CSS file - as late as possible
			$css_custom = $this->root_path.'templates/'.$this->user->style['template_path'].'/custom.css';
			if(file_exists($css_custom)){
				$this->tpl->css_file($css_custom);
			}

			//Global CSS - Direct Output into template
			if(strlen($this->config->get('global_css'))){
				$this->tpl->add_css($this->config->get('global_css'), true);
			}

			//Add additonal Template Links
			$strAdditionalLinks = $this->user->style['additional_fields'];
			$arrAdditionalLinks = ($strAdditionalLinks != "") ? unserialize($strAdditionalLinks) : array();
			foreach($arrAdditionalLinks as $key => $val){
				$this->tpl->assign_var('LINK_'.strtoupper($key), $val);
			}

			if($this->hooks->isRegistered('coreclass_page_tail')){
				$this->hooks->process('coreclass_page_tail');
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
			return '<div class="copyright">
						'.((!defined('TMP_DISABLE_EQDKP_URL')) ? '<a href="'.EQDKP_ABOUT_URL.'" target="new">' : '').'EQDKP-PLUS '.((DEBUG > 3) ? '[FILE: '.VERSION_INT.', DB: '.$this->config->get('plus_version').']' : VERSION_EXT).' &copy; '.$this->time->date('Y', $this->time->time).' by EQdkp-Plus Team'.((!defined('TMP_DISABLE_EQDKP_URL')) ? '</a>' : '').'
						<!-- Style '.$this->user->style['style_name'].' '.$this->user->style['style_version'].' -->
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
			$dkp_name 		= '';

			if($this->config->get('main_title')){
				$main_title	= $this->config->get('main_title');
			}else {
				$main_title = sprintf($pt_prefix, $this->config->get('guildtag'), $dkp_name);
			}

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

			//Check if EQdkp Plus was moved and data folder has changed
			$this->pfh->check_cachefolder();

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


		public function icon_font($icon, $size="", $pathext=""){
			if(isset($icon) && pathinfo($icon, PATHINFO_EXTENSION) == 'png'){
				return '<img src="'.$pathext.$icon.'" alt="img" />';
			}elseif(isset($icon)){
				return '<i class="fa '.$icon.(($size)? ' '.$size : '').'"></i>';
			}else{
				return '';
			}
		}

		public function mycharacters(){
			if ($this->config->get('enable_points') && $this->user->id != ANONYMOUS){

				//get member ID from UserID
				$memberids = $this->pdh->get('member', 'connection_id', array($this->user->id));
				$multidkps	= $this->pdh->sort($this->pdh->get('multidkp', 'id_list'), 'multidkp', 'sortid');
				$preset		= $this->pdh->pre_process_preset('current', array(), 0);

				if(is_array($memberids) && count($memberids) > 0){
					// start the output
					foreach($memberids as $member_id) {
						if(!$this->config->get('show_twinks') && !$this->pdh->get('member', 'is_main', array($member_id))) {
							continue;
						}

						$member_class = $this->game->decorate_character($member_id).' '.$this->pdh->geth('member', 'memberlink', array($member_id, $this->routing->build('character',false,false,false), '', false, false, false, true));
						#$quickdkp .= '<tr><td colspan="2">'.$member_class.'</td></tr>';
						$i = 0;
						foreach($multidkps as $mdkpid) {
							$current = $this->pdh->geth($preset[0], $preset[1], $preset[2], array('%member_id%' => $member_id, '%dkp_id%' => $mdkpid, '%with_twink%' =>!$this->config->get('show_twinks')));

							$this->tpl->assign_block_vars('mychars_points', array(
								'CHARICON' => $this->game->decorate_character($member_id),
								'CHARNAME' => $this->pdh->geth('member', 'name', array($member_id)),
								'CHARLINK' => $this->pdh->get('member', 'memberlink', array($member_id, $this->routing->simpleBuild("character"), '', true)),
								'POOLNAME' => $this->pdh->get('multidkp', 'name', array($mdkpid)),
								'CURRENT'  => $current.' '.$this->config->get('dkp_name'),
								'IS_MAIN'  => ($this->pdh->get('member', 'is_main', array($member_id)) && ($i==0)),
								'ID'	   => md5($this->user->id.'m'.$member_id.'mdkp'.$mdkpid),
							));

							$this->tpl->assign_var("S_MYCHARS_POINTS", true);
							$i++;
						}

					}
				}
			}
		}

		public function cors_headers(){
			$strDomains = $this->config->get('access_control_header');
			$arrDomains = explode("\n", $strDomains);

			$arrAllowedDomains = array();
			
			header('Access-Control-Allow-Headers: Authorization, X-Custom-Authorization');

			foreach($arrDomains as $strDomain){
				$strDomain = trim(unsanitize($strDomain));
				if($strDomain === '*') {
					header('Access-Control-Allow-Origin: *');
					return;
				}

				$arrAllowedDomains[] = $strDomain; // http://mydomain.com
			}

			//Some generic domains
			$strDomain = $this->env->httpHost;
			$urlData = parse_url($strDomain);
			$hostData = explode('.', $urlData['host']);
			$hostData = array_reverse($hostData);
			if(count($hostData) > 1) $strDomain = $hostData[1].'.'.$hostData[0];
			else $strDomain = $hostData[0];
			$arrAllowedDomains[] = $strDomain;

			$incomingOrigin = array_key_exists('HTTP_ORIGIN', $_SERVER) ? $_SERVER['HTTP_ORIGIN'] : NULL;
			if($incomingOrigin === NULL) $incomingOrigin = array_key_exists('ORIGIN', $_SERVER) ? $_SERVER['ORIGIN'] : NULL;
			
			foreach($arrAllowedDomains as $strAllowedDomain){
				$arrDomainParts = parse_url($strAllowedDomain);
				if($arrDomainParts['host'] != ""){
					$pattern = '/^https?:\/\/([\w_-]+\.)*' . $arrDomainParts['host'] . '$/';

					$allow = preg_match($pattern, $incomingOrigin);
					if ($allow){
						header('Access-Control-Allow-Origin: '.filter_var($incomingOrigin, FILTER_SANITIZE_URL));
						return;
					}
				}
			}

		}
}
