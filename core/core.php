<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2010
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2006-2010 EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev$
 * 
 * $Id$
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

	class mmocms_core
	{
		// General vars
		public $config			= array();			// Config values            @var config
		public $rowclass		= 'row1';			// Alternating row class    @var row_class
		public $header_format	= 'full';			// Use a simple header?     @var
		public $page_title		= '';				// Page title               @var page_title
		public $template_file	= '';				// Template file to parse   @var template_file
		public $template_path	= '';				// Path to template_file    @var template_path
		protected $timer_start	= 0;				// Page timer start         @var timer_start
		protected $timer_end	= 0;				// Page timer end           @var timer_end

		/**
		* Construct
		*/
		public function __construct($eqdkp_root_path = './'){
			global $jquery, $db, $settings, $_HMODE, $pcache;
			$this->timer_start			= microtime(true);		// Start a script timer if were debugging
			$this->root_path			= $eqdkp_root_path;
			$this->settings				= &$settings;
			$this->db					= $db;
			$this->hmode				= $_HMODE;
			$this->pcache				= $pcache;
			$this->config				= $this->settings->get_config();
		}
		
		/**
		* Set a config value
		*
		* @param     string     $config_name			Name of the config value
		* @param     string     $config_value		Value
		* @param     string     $plugin					if plugin, plugin name
		*/
		public function config_set($config_name, $config_value='', $plugin=''){
			$this->settings->set_config($config_name, $config_value, $plugin);
			if(!is_array($config_name)) {
				if($plugin) {
					$this->config[$config_name][$plugin] = $config_value;
				} else {
					$this->config[$config_name] = $config_value;
				}
			} else {
				$this->config = $this->settings->get_config();
			}
		}
		
		public function config_del($config_name, $plugin=''){
			$this->settings->del_config($config_name, $plugin);
		}
		

		/**
		* Add a global Message to all Pages
		*
		* @param     string     $text             Message text
		* @param     string     $title            Message title
		* @param     string     $kind             Color Theme: red/green/default
		*/
		public function message($text='', $title='', $kind='default'){
			global $jquery;
			$jquery->Growl($text, array('header' => $title,'sticky' => true,'theme'  => 'eqdkp-'.$kind));
		}

		/**
		* Add a global Messages to all Pages
		*
		* @param     string     $text             Message text
		* @param     string     $title            Message title
		* @param     string     $kind             Color Theme: red/green/default
		*/
		public function messages($messages){
			foreach($messages as $message){
				$name = (is_array($message)) ? 'message' : 'messages';
				$this->message(${$name}['text'], ${$name}['title'], ${$name}['color']);
				if(!is_array($message)){
					break;
				}
			}
		}
		
		/**
		* Add a global Messages to all Pages
		*
		* @param     string     $text             Message text
		* @param     string     $title            Message title
		* @param     string     $kind             Color Theme: red/green/default
		*/
		private function global_warning($message, $icon='images/false.png', $class='errortable') {
			global $tpl;
			$tpl->assign_block_vars(
				'global_warnings', array(
					'MESSAGE' => $message,
					'CLASS'		=> $class,
					'ICON'		=> $this->root_path.$icon,
				)
			);
		}
		
		public function switch_row_class($new=true){
			$rowclass = ($this->rowclass == 'row1') ? 'row2' : 'row1';
			if($new){
				$this->rowclass = $rowclass;
			}
			return $rowclass;
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

		public function page_header(){
			global $user, $tpl, $pm, $debug, $jquery, $SID, $html, $portal, $plus, $game, $pdh;

			// Define a variable so we know the header's been included
			define('HEADER_INC', true);

			// gzip content if enabled
			if ( $this->config['enable_gzip'] == '1' ){
				if ( (extension_loaded('zlib')) && (!headers_sent()) ){
					@ob_start('ob_gzhandler');
				}
			}

			$SID = ( isset($SID) ) ? $SID : '?' . 's=';

			// Send the HTTP headers
			@header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
			@header('Content-Type: text/html; charset=utf-8');

			// some style additions (header, background image..)
			$header_logo = (strpos($user->style['logo_path'],'://') > 1) ? $user->style['logo_path'] : $this->root_path . 'templates/' . $user->style['template_path'] ."/images/". $user->style['logo_path'];
			$template_background_file = $this->root_path . 'games/' .$this->config['default_game'] . '/template_background.jpg' ;
			if (!file_exists($template_background_file)){
				$template_background_file	= $this->root_path . 'templates/' . $user->style['template_path'] . '/images/template_background.jpg';
			}

			if (strpos($user->style['background_img'],'://') > 1){
				$template_background_file = $user->style['background_img'];
			}

			$tpl->assign_vars(array(
				'XML_LANG'					=> $user->lang['XML_LANG'],
				'PAGE_TITLE'				=> page_title($this->page_title),
				'MAIN_TITLE'				=> $this->config['main_title'],
				'SUB_TITLE'					=> $this->config['sub_title'],
				'EQDKP_ROOT_PATH'			=> $this->root_path,
				'IMAGE_PATH'				=> $this->root_path.'images/',
				'LOGO_PATH'					=> $user->style['logo_path'],
				'HEADER_LOGO'				=> $header_logo,
				'TEMPLATE_BACKGROUND'		=> $template_background_file,
				'TEMPLATE_PATH'				=> $this->root_path . 'templates/' . $user->style['template_path'],
				'TMPL_FOLDERNAME'			=> $user->style['template_path'],
			));

			// JavaScript Windows
			$jquery->Dialog('AboutPLUSDialog', $user->lang['pk_plus_about'], array('url'=>$this->root_path.'about.php', 'width'=>'680', 'height'=>'540'));
			$jquery->Dialog('fe_portalsettings', $user->lang['portalplugin_winname'], array('url'=>$this->root_path."admin/portalsettings.php?id='+moduleid+'", 'width'=>'660', 'height'=>'400', 'withid'=>'moduleid'));

			// CSS
			$tpl->css_file($this->root_path.'templates/eqdkpplus.css');
			$customtpl = $this->root_path.'templates/'.$user->style['template_path'].'/'.$user->style['template_path'].'.css';
			if(is_file($customtpl)){
				$tpl->css_file($customtpl);
			}

			// Header, Part 1
			if (!$user->check_auth('a_', false) && validate()){
				$g = strtolower($this->config['default_game']);
				$l = strtolower($user->data['user_lang']);
				$d = strtolower($this->config['default_lang']);

				if ($this->hmode){
					$tpl->js_file('http://ads.h1351109.stratoserver.net/delivery/spcjs.php?id=2&amp;target=_blank');
				}elseif($g == 'wow'){
					if (($l == 'german') || ($d == 'german')){
						$tpl->js_file('http://ads.allvatar.com/adserver/www/delivery/spcjs.php?id=13');
					}else {
						$tpl->js_file('http://ads.allvatar.com/adserver/www/delivery/spcjs.php?id=14');
					}
				}elseif($g=='lotro'){
					if (($l == 'german') || ($d == 'german')){
						$tpl->js_file('http://ads.allvatar.com/adserver/www/delivery/spcjs.php?id=15');
					}else {
						$tpl->js_file('http://ads.allvatar.com/adserver/www/delivery/spcjs.php?id=16');
					}
				}else{
					if (($l == 'german') || ($d == 'german')){
						$tpl->js_file('http://ads.allvatar.com/adserver/www/delivery/spcjs.php?id=11');
					}else {
						$tpl->js_file('http://ads.allvatar.com/adserver/www/delivery/spcjs.php?id=12');
					}
				}
			}

			$my_js = "
				var marked_row = new Array;
				function Init_RowClick(){
					var rows = document.getElementsByTagName('tr');
					for ( var i = 0; i < rows.length; i++ ) {
						// ... with the class 'odd' or 'even' ...
						if ( 'row1' != rows[i].className.substr(0,4) && 'row2' != rows[i].className.substr(0,4) ) {
							continue;
						}
						rows[i].onmousedown = function() {
							var unique_id;
							var checkbox;
		
							checkbox = this.getElementsByTagName( 'input' )[0];
							if ( checkbox && checkbox.type == 'checkbox' ) {
								unique_id = checkbox.name + checkbox.value;
							} else if ( this.id.length > 0 ) {
								unique_id = this.id;
							} else {
								return;
							}
		
							if ( typeof(marked_row[unique_id]) == 'undefined' || !marked_row[unique_id] ) {
								marked_row[unique_id] = true;
							} else {
								marked_row[unique_id] = false;
							}
		
							if ( marked_row[unique_id] ) {
								this.className += ' marked';
							} else {
								this.className = this.className.replace(' marked', '');
							}
		
							if ( checkbox && checkbox.disabled == false ) {
								checkbox.checked = marked_row[unique_id];
							}
						}
	
						// .. and checkbox clicks
						var checkbox = rows[i].getElementsByTagName('input')[0];
						if ( checkbox ) {
							checkbox.onclick = function() {
							// opera does not recognize return false;
								this.checked = ! this.checked;
							}
						}
					}
			}";
			$tpl->add_js($my_js);

			$s_in_admin				= ( defined('IN_ADMIN') ) ? IN_ADMIN : false;
			$s_in_usersettings		= ( defined('IN_USERSETTINGS') ) ? IN_USERSETTINGS : false;
			$s_in_admin				= ( ($s_in_admin) && ($user->check_auth('a_', false)) ) ? true : false;

			if ($this->config['pk_maintenance_mode'] && $user->check_auth('a_', false)){
				$this->global_warning($user->lang['maintenance_mode_warn']);
			}

			if (file_exists($this->root_path.'install') && EQDKP_INSTALLED && $user->check_auth('a_', false) && IN_ADMIN && !EQDKPPLUS_VERSION_BETA){
				$this->global_warning($user->lang['install_folder_warn']);
			}

			$tpl->assign_vars(array(
				'GAME_FOLDER'				=> $this->config['default_game'],
				'S_NORMAL_HEADER'			=> false,
				'S_NORMAL_FOOTER'			=> false,
				'S_ADMIN'					=> $user->check_auth('a_', false),
				'S_IN_ADMIN'				=> $s_in_admin,
				'S_IN_USERSETTINGS'			=> $s_in_usersettings,
				'L_PLSLOGO_ALT'				=> $user->lang['home_of_eqdkpplus'],

				'URI_ADJUSTMENT'			=> 'a',
				'URI_EVENT'					=> 'e',
				'URI_ITEM'					=> 'i',
				'URI_LOG'					=> 'logid',
				'URI_NAME'					=> 'name',
				'URI_NEWS'					=> 'n',
				'URI_ORDER'					=> 'o',
				'URI_PAGE'					=> 'p',
				'URI_RAID'					=> 'r',
				'URI_SESSION'				=> 's',
				'SID'						=> $SID,

				// Theme Settings
				'T_FONTFACE1'				=> $user->style['fontface1'],
				'T_FONTFACE2'				=> $user->style['fontface2'],
				'T_FONTFACE3'				=> $user->style['fontface3'],
				'T_FONTSIZE1'				=> $user->style['fontsize1'],
				'T_FONTSIZE2'				=> $user->style['fontsize2'],
				'T_FONTSIZE3'				=> $user->style['fontsize3'],
				'T_FONTCOLOR1'				=> $user->style['fontcolor1'],
				'T_FONTCOLOR2'				=> $user->style['fontcolor2'],
				'T_FONTCOLOR3'				=> $user->style['fontcolor3'],
				'T_FONTCOLOR_NEG'			=> $user->style['fontcolor_neg'],
				'T_FONTCOLOR_POS'			=> $user->style['fontcolor_pos'],
				'T_BODY_BACKGROUND'			=> $user->style['body_background'],
				'T_TABLE_BORDER_WIDTH'		=> $user->style['table_border_width'],
				'T_TABLE_BORDER_COLOR'		=> $user->style['table_border_color'],
				'T_TABLE_BORDER_STYLE'		=> $user->style['table_border_style'],
				'T_BODY_LINK'				=> $user->style['body_link'],
				'T_BODY_LINK_STYLE'			=> $user->style['body_link_style'],
				'T_BODY_HLINK'				=> $user->style['body_hlink'],
				'T_BODY_HLINK_STYLE'		=> $user->style['body_hlink_style'],
				'T_HEADER_LINK'				=> $user->style['header_link'],
				'T_HEADER_LINK_STYLE'		=> $user->style['header_link_style'],
				'T_HEADER_HLINK'			=> $user->style['header_hlink'],
				'T_HEADER_HLINK_STYLE'		=> $user->style['header_hlink_style'],
				'T_TH_COLOR1'				=> $user->style['th_color1'],
				'T_TR_COLOR1'				=> $user->style['tr_color1'],
				'T_TR_COLOR2'				=> $user->style['tr_color2'],
				'T_INPUT_BACKGROUND'		=> $user->style['input_color'],
				'T_INPUT_BORDER_WIDTH'		=> $user->style['input_border_width'],
				'T_INPUT_BORDER_COLOR'		=> $user->style['input_border_color'],
				'T_INPUT_BORDER_STYLE'		=> $user->style['input_border_style'],
			));

			// Class colours
			foreach($game->get('classes') as $class_id => $class_name) {
				$tpl->add_css('
					.class_'.$class_id.', .class_'.$class_id.':link, .class_'.$class_id.':visited, .class_'.$class_id.':active,
					.class_'.$class_id.':link:hover, td.class_'.$class_id.' a:hover, td.class_'.$class_id.' a:active,
					td.class_'.$class_id.', td.class_'.$class_id.' a:link, td.class_'.$class_id.' a:visited{
						text-decoration: none;
						color: '.$game->get_class_color($class_id).';
					}
				');
			}

			//Portal things
			$portal_url = (strlen($this->config['pk_contact_website']) > 1) ? $this->config['pk_contact_website'] : $this->root_path . 'viewnews.php';
			$tpl->assign_vars(array('PORTAL_URL' => $portal_url));

			if($this->config['pk_noDKP'] == 1){
				$tpl->assign_vars(array('PORTAL_DKP_URL'						=> $this->root_path . 'roster.php'));
				$tpl->assign_vars(array('PORTAL_DKP_SYSTEM_NAME'		=> 'Roster'));
			}else{
				$tpl->assign_vars(array('PORTAL_DKP_URL'						=> $this->root_path . 'listcharacters.php'));
				$tpl->assign_vars(array('PORTAL_DKP_SYSTEM_NAME'		=> 'DKP-System'));
			}

			//Switches for installed Plugins
			$plugins = $pm->get_plugins();
			foreach($plugins as $key=>$value){
				$tpl->assign_vars(array( 'S_PLUGIN_'.strtoupper($key).'_INST' => true));
			};

			// Menus
			$menus = $this->gen_menus();

			//All Menu-Links
			$menus['menu1'] = array_merge($menus['menu1'], $pdh->get('links', 'menu', array(1)));
			$menus['menu2'] = array_merge($menus['menu2'], $pdh->get('links', 'menu', array(2)));
			$menus['menu4'] = array_merge($menus['menu4'], $pdh->get('links', 'menu', array(4)));

			if ($this->config['pk_links']){
				$menus['menu3'] = $pdh->get('links', 'menu', array(3));
			}

			//Sorting Menu1
			if (isset($this->config['sort_menu1'])){
				//Sorting-Data
				$sort_ary1 = unserialize(stripslashes($this->config['sort_menu1']));

				foreach ($menus['menu1'] as $key=>$menu){
					//Remove Session
					$link = preg_replace('#\?s\=([0-9A-Za-z]{1,32})?#', '', $menu['link']);
					$link = preg_replace('#\.php&#', '.php?', $link);
					if (isset($sort_ary1[$link]['sort'])){
						$tmp_menu1[$key] = $sort_ary1[$link]['sort'];
					} else {
						$tmp_menu1[$key] = 999999;
					}
					if ($sort_ary1[$link]['hide']){
						unset($tmp_menu1[$key]);
						unset($menus['menu1'][$key]);
					}
				}
				array_multisort($tmp_menu1, SORT_ASC, SORT_NUMERIC, $menus['menu1']);
			}

			//Sorting Menu 2
			if (isset($this->config['sort_menu2'])){
				//Sorting-Data
				$sort_ary2 = unserialize(stripslashes($this->config['sort_menu2']));
			
				foreach ($menus['menu2'] as $key=>$menu){
					//Remove Session
					$link = preg_replace('#\?s\=([0-9A-Za-z]{1,32})?#', '', $menu['link']);
					$link = preg_replace('#\.php&#', '.php?', $link);
					if (isset($sort_ary2[$link]['sort'])){
						$tmp_menu2[$key] = $sort_ary2[$link]['sort'];
					} else {
						$tmp_menu2[$key] = 999999;
					}
					if ($sort_ary2[$link]['hide']){
						unset($tmp_menu2[$key]);
						unset($menus['menu2'][$key]);
					}
				}
				array_multisort($tmp_menu2, SORT_ASC, SORT_NUMERIC, $menus['menu2']);
			}

			//Sorting Menu4
			if (isset($this->config['sort_menu4'])){
				//Sorting-Data
				$sort_ary4 = unserialize(stripslashes($this->config['sort_menu4']));
			
				foreach ($menus['menu4'] as $key=>$menu){
					//Remove Session
					$link = preg_replace('#\?s\=([0-9A-Za-z]{1,32})?#', '', $menu['link']);
					$link = preg_replace('#\.php&#', '.php?', $link);
					if (isset($sort_ary4[$link]['sort'])){
						$tmp_menu4[$key] = $sort_ary4[$link]['sort'];
					} else {
						$tmp_menu4[$key] = 999999;
					}
					if ($sort_ary4[$link]['hide']){
						unset($tmp_menu4[$key]);
						unset($menus['menu4'][$key]);
					}
				}
				array_multisort($tmp_menu4, SORT_ASC, SORT_NUMERIC, $menus['menu4']);
			}

			foreach ( $menus as $number => $array ){
				if (is_array($array)){
					foreach ( $array as $menu ){
						// Don't display the link if they don't have permission to view it
						if ( (empty($menu['check'])) || ($user->check_auth($menu['check'], false)) ){
							$var		= 'main_' . $number;
							$varV		= 'main_' . $number."_V";
							$class	= "row".($bi+1) ;
							${$varV} .= '<tr class="'.$class.'" nowrap onmouseover="this.className=\'rowHover\';" onmouseout="this.className=\''.$class.'\';">
															<td nowrap>&nbsp;<img src="' .$this->root_path .'images/arrow.gif" alt="arrow"/> &nbsp;
															<a href="' . (($menu['plus_link']) ? $menu['link'] : $this->root_path . $menu['link']) . '" class="copy" target="'.(($menu['target']) ? $menu['target'] : '_top').'">' . $menu['text'] . '</a>
															</td></tr>';
								${$var} .= '<a href="' . (($menu['plus_link']) ? $menu['link'] : $this->root_path . $menu['link']) . '" class="copy" target="'.(($menu['target']) ? $menu['target'] : '_top').'">' . $menu['text'] . '</a> | ';
								$bi = 1-$bi;
								
								$tpl->assign_block_vars( 'main_'.$number.'_menu', array(
									'LINK' => (($menu['plus_link']) ? $menu['link'] : $this->root_path . $menu['link']),
									'TEXT'	=> $menu['text'],
									'TARGET'	=> (($menu['target']) ? $menu['target'] : '_top'),
									'CLASS'	=> $this->switch_row_class(),
							));
						}
					}
				}
			}

			$main_menu1 = preg_replace('# \| $#', '', $main_menu1);
			$main_menu2 = preg_replace('# \| $#', '', $main_menu2);
			$main_menu3 = preg_replace('# \| $#', '', $main_menu3);

			//collapsable
			$jquery->Collapse('main_menu1');
			$jquery->Collapse('main_menu2');
			$jquery->Collapse('main_menu3');

			if ($this->header_format != 'simple'){
				$tpl->assign_vars(array(
					'S_NORMAL_HEADER'		=> true,
					'S_NORMAL_FOOTER'		=> true,
					'S_LOGGED_IN'			=> ($user->data['user_id'] != ANONYMOUS) ? true : false,
					'L_MENU1_HEADLINE'		=> $user->lang['menu_eqdkp'],
					'L_MENU2_HEADLINE'		=> $user->lang['menu_user'],
					'L_MENU3_HEADLINE'		=> $user->lang['menu_links_short'],
					'MAIN_MENU1'			=> $main_menu1,
					'MAIN_MENU2'			=> $main_menu2,
					'MAIN_MENU3'			=> $main_menu3,
					'S_MAIN_MENU3'			=> $this->config['pk_links'],
					)
				);
			}
		}

		public function gen_menus(){
			global $user, $pm, $SID, $tpl, $pdh;

			// Menu 1
			$listmembers	= array(array('link' => 'listcharacters.php'.$SID, 'text' => $user->lang['menu_standings'], 'check' => 'u_member_list'));
			$roster			= array(array('link' => 'roster.php'.$SID, 'text' => $user->lang['menu_roster'], 'check' => 'u_member_list'));
			$listraids		= array(array('link' => 'listraids.php'.$SID, 'text' => $user->lang['menu_raids'], 'check' => 'u_raid_list'));
			$listevents		= array(array('link' => 'listevents.php'.$SID, 'text' => $user->lang['menu_events'], 'check' => 'u_event_list'));
			$listitems		= array(array('link' => 'listitems.php'.$SID, 'text' => $user->lang['menu_itemhist'], 'check' => 'u_item_list'));

			$main_menu1 = array(
				array('link' => 'viewnews.php'.$SID, 'text' => $user->lang['menu_news'], 'check' => ''),
				array('link' => 'listusers.php'.$SID, 'text' => $user->lang['user_list'], 	 'check' => 'u_userlist')
			);

			if(!$this->config['pk_noDKP'] == 1) {$main_menu1 = array_merge($main_menu1,$listmembers); }
			if(!$this->config['pk_noRoster'] == 1) {$main_menu1 = array_merge($main_menu1,$roster); }
			if(!$this->config['pk_noRaids'] == 1) {$main_menu1 = array_merge($main_menu1,$listraids); }
			if(!$this->config['pk_noEvents'] == 1) {$main_menu1 = array_merge($main_menu1,$listevents); }
			if(!$this->config['pk_noItemPrices'] == 1) {$main_menu1 = array_merge($main_menu1,$listitems); }

			//Infopages
			if (is_array($pdh->get('infopages', 'mainmenu_pages', array()))){
				$main_menu1 = array_merge($main_menu1,$pdh->get('infopages', 'mainmenu_pages', array()));
			}

			if (is_object($pm)){
				$main_menu1 = (is_array($pm->get_menus('main_menu1'))) ? array_merge($main_menu1, $pm->get_menus('main_menu1')) : $main_menu1;
			}

			// Menu 2
			$main_menu2 = array();
			if ( $user->data['user_id'] != ANONYMOUS ){
				$main_menu2[] = array('link' => 'settings.php' . $SID, 'text' => $user->lang['menu_settings']);
				$main_menu2[] = array('link' => 'characters.php' . $SID, 'text' => $user->lang['menu_members']);
			}else{
				if (!$this->config['pk_disable_reg'] == 1){
					//CMS register?
					$register_link = ($this->config['pk_bridge_cms_deac_reg']) ? 'wrapper.php?id=register' :  'register.php'. $SID ;
					if ($this->config['disable_registration'] == 0){
						$main_menu2[] = array('link' => $register_link , 'text' => $user->lang['menu_register']);
					}
				}
			}

			// Switch login/logout link
			$main_menu2[] = ($user->data['user_id'] != ANONYMOUS) ? array('link' => 'login.php' . $SID . '&amp;logout=true', 'text' => $user->lang['logout'] . ' [ ' . $user->data['username'] . ' ]') : array('link' => 'login.php' . $SID, 'text' => $user->lang['login']);
			if ($user->data['user_id'] != ANONYMOUS && $user->check_auth('a_', false)){
				$main_menu2[] = array('link' => 'admin/index.php' . $SID, 'text' => $user->lang['menu_admin_panel']);
			}

			//Infopages
			if (is_array($pdh->get('infopages', 'usermenu_pages', array()))){
				$main_menu2 = array_merge($main_menu2,$pdh->get('infopages', 'usermenu_pages', array()));
			}

			if (is_object($pm)){
				$main_menu2 = (is_array($pm->get_menus('main_menu2'))) ? array_merge($main_menu2, $pm->get_menus('main_menu2')) : $main_menu2;
			}

			//Menu4
			if (strlen($this->config['pk_bridge_cms_InlineUrl']) > 0){
				$inlineforum	= array(array('link' => 'wrapper.php?id=board', 'text' => $user->lang['forum'], 'check' => '', 'plus_link' => true))	;
				$main_menu1		= array_merge($main_menu1, $inlineforum) ;
				$main_menu4[] = $inlineform;
			}

			//Portal TPL Vars
			$portal_url		= (strlen($this->config['pk_contact_website']) > 1 ) ? $this->config['pk_contact_website'] : 'viewnews.php';
			$main_menu4[] = array('link' => $portal_url, 'text' => $user->lang['portal'], 'check' => '')	;

			if($this->config['pk_noDKP'] == 1){
				$tpl->assign_vars(array('PORTAL_DKP_URL' => $this->root_path . 'roster.php'));
				$tpl->assign_vars(array('PORTAL_DKP_SYSTEM_NAME' => $user->lang['menu_roster']));
				$main_menu4[] = array('link' => 'roster.php', 'text' => $user->lang['menu_roster'], 'check' => '')	;
			}else{
				$tpl->assign_vars(array('PORTAL_DKP_URL' => $this->root_path . 'listcharacters.php'));
				$tpl->assign_vars(array('PORTAL_DKP_SYSTEM_NAME' => sprintf($user->lang['dkp_system'], $this->config['dkp_name'])));
				$main_menu4[] = array('link' => 'listcharacters.php', 'text' => sprintf($user->lang['dkp_system'], $this->config['dkp_name']), 'check' => '')	;
			}

			// Raidplan Tab on top of page in some templates. maybe make this dynamically in a future version?
			if (isset($pm)){
				if ($pm->check(PLUGIN_INSTALLED, 'raidplan')){
					$main_menu4[] = array('link' => 'plugins/raidplan/listraids.php', 'text' => $user->lang['raidplan'], 'check' => '')	;
					$tpl->assign_vars(array(
						'IS_RP_INSTALLED'	=> true,
						'IS_RP_URL'			=> $this->root_path . 'plugins/raidplan/listraids.php',
						'RP_LINK_NAME'		=> $user->lang['raidplan']
					));
				}
			}

			// Infopages for Menu 4
			if (is_array($pdh->get('infopages', 'tab_pages', array()))){
				$main_menu4 = array_merge($main_menu4,$pdh->get('infopages', 'tab_pages', array()));
			}

			// Shop
			if(($user->lang_name == 'german') and  (!$user->data['custom_fields']['hide_shop']) ){
				$shop_link = array(array('link' => 'shop.php' ,  'text' => $user->lang['guild_shop'], 'check' => '', 'editable' => false));
				$main_menu2 = array_merge($main_menu2, $shop_link) ;
			}

			$menus = array(
				'menu1'	=> $main_menu1,
				'menu2'	=> $main_menu2,
				'menu4'	=> $main_menu4
			);
			return $menus;
		}

		public function page_tail(){
			global $user, $tpl, $pm, $html, $debug, $SID, $pdl, $plus, $jquery;

			if ( !empty($this->template_path) ){
				$tpl->set_template($user->style['style_code'], $user->style['template_path'], $this->template_path);
			}

			$tpl->set_filenames(array(
				'body' => $this->template_file)
			);

			// Hiding the copyright/debug info if header is set to none..
			if ($this->header_format != 'simple'){
				$tpl->assign_vars(array(
					'S_NORMAL_FOOTER' 			=> true,
					'EQDKP_PLUS_COPYRIGHT'		=> $this->Copyright())
				);

				if($debug){
					$this->timer_end = microtime(true);
					$log = $pdl->get_log();
					$tpl->assign_vars(array(
						'S_SHOW_DEBUG'			=> true,
						'S_SHOW_QUERIES'		=> true,
						'EQDKP_RENDERTIME'		=> substr($this->timer_end - $this->timer_start, 0, 5),
						'EQDKP_QUERYCOUNT'		=> $this->db->query_count,
						'EQDKP_MEM_PEAK'		=> number_format(memory_get_peak_usage()/1024, 0, '.', ',').' kb'
					));

					//debug tabs
					if(count($log) > 0){
						$jquery->Tab_header('plus_debug_tabs');
						$debug_tabs_header = "<div id='plus_debug_tabs'><ul>";
						$i = 1;
						$debug_tabs = "";

						foreach($log as $type => $log_entries){
							$debug_tabs_header .= "<li><a href='#error-".$i."'><span>".$type." (".count($log_entries).")"."</span></a></li>";
							$debug_tabs .= '<div id="error-'.$i.'">';
							$debug_tabs .= '<table width="99%" border="0" cellspacing="1" cellpadding="0">';
							foreach($log_entries as $log_entry){
								$debug_tabs .= '<tr class="'.$this->switch_row_class().'"><td>'.$pdl->html_format_log_entry($type, $log_entry).'</td></tr>';
							}
							$debug_tabs .= '</table>';
							$debug_tabs .= '</div>';
							$i++;
						}
					}
					$debug_tabs_header .= "</ul>";
					$debug_tabs .= '</div>';

					$tpl->assign_vars(array(
						'DEBUG_TABS' => $debug_tabs_header . $debug_tabs,
					));
				}else{
					$tpl->assign_vars(array(
						'S_SHOW_DEBUG'		=> false,
						'S_SHOW_QUERIES'	=> false)
					);
					}
			}else{
				$tpl->assign_vars(array(
					'S_NORMAL_FOOTER' => false)
				);
			}

			// Pass JS Code to template..
			if(!$tpl->tplout_set['js_code']){
				// JS in header...
				if(is_array($tpl->tpl_output['js_code'])){
					$imploded_jscode = implode("\n", $tpl->tpl_output['js_code']); 
					$tpl->assign_var('JS_CODE', (($debug) ? $imploded_jscode : JSMin::minify($imploded_jscode)));
					$tpl->tplout_set['js_code'] = true;
				}
	
				// JS on end of page
				if(is_array($tpl->tpl_output['js_code_eop'])){
					$imploded_jscodeeop = implode("\n", $tpl->tpl_output['js_code_eop']); 
					$tpl->assign_var('JS_CODE_EOP', (($debug) ? $imploded_jscodeeop : JSMin::minify($imploded_jscodeeop)));
					$tpl->tplout_set['js_code'] = true;
				}
				// JS on end of page
				if(is_array($tpl->tpl_output['js_code_eop2'])){
					$imploded_jscodeeop2 = implode("\n", $tpl->tpl_output['js_code_eop2']); 
					$tpl->assign_var('JS_CODE_EOP2', (($debug) ? $imploded_jscodeeop2 : JSMin::minify($imploded_jscodeeop2)));
					$tpl->tplout_set['js_code'] = true;
				}
			}

			// Pass CSS Code to template..
			if(!$tpl->tplout_set['css_code']){
				if(is_array($tpl->tpl_output['css_code'])){
					$imploded_css = implode("\n", $tpl->tpl_output['css_code']);
					$tpl->assign_var('CSS_CODE', (($debug) ? $imploded_css : Minify_CSS::minify($imploded_css)));
					$tpl->tplout_set['css_code'] = true;
				}
			}

			// Load the CSS Files..
			if(!$tpl->tplout_set['css_file']){
				if(is_array($tpl->tpl_output['css_file'])){
					#$tpl->assign_var('CSS_FILES', '<link rel="stylesheet" type="text/css" href="'.$this->root_path.'perform.php?type=css&rpth='.base64_encode(serialize($this->root_path)).'&css='.base64_encode(serialize($tpl->tpl_output['css_file'])).'" />');
						
					$tpl->assign_var('CSS_FILES', $plus->implode_wrapped("<link rel='stylesheet' href='", "' type='text/css' />", "\n", $tpl->tpl_output['css_file']));
					$tpl->tplout_set['css_file'] = true;
				}
			}
	
			// Load the JS Files..
			if(!$tpl->tplout_set['js_file']){
				if(is_array($tpl->tpl_output['js_file'])){

					$tpl->assign_var('JS_FILES', '<script type="text/javascript" src="'.$this->root_path.'perform.php?type=js&js='.base64_encode(serialize($tpl->tpl_output['js_file'])).'"></script>');
					//$tpl->assign_var('JS_FILES', $plus->implode_wrapped("<script type='text/javascript' src='", "'></script>", "\n", $tpl->tpl_output['js_file']));
				}
				$tpl->tplout_set['js_file'] = true;
			}

			// Pass RSS-Feeds to template..
			if(!$tpl->tplout_set['rss_feeds']){
				if(is_array($tpl->tpl_output['rss_feeds'])){
					foreach($tpl->tpl_output['rss_feeds'] as $feed){
						$feeds[] = '<link rel="alternate" type="'.$feed['type'].'" title="'.$feed['name'].'" href="'.$feed['url'].'" />'; 
					}
				}

				if(is_array($feeds)){
					$imploded_feeds = implode("\n", $feeds);
					$tpl->assign_var('RSS_FEEDS', $imploded_feeds);
					$tpl->tplout_set['rss_feeds'] = true;
				}
			}

			// Get rid of our template data
			if($this->header_format != 'none'){
				$tpl->display('header');
			}
			$tpl->display('body');
			if($this->header_format != 'none'){
				$tpl->display('footer');
			}

			//unset settings, they need to save config in db (before the db-connection shuts down)
			unset($this->settings);
			$tpl->destroy();
			exit;
		}

		public function BuildLink(){
			$script_name = preg_replace('/^\/?(.*?)\/?$/', '\1', trim($this->config['server_path']));
			$script_name = ( $script_name != '' ) ? $script_name . '/' : '';
			return $this->httpHost().'/'.$script_name;
		}

		protected function httpHost(){
			$protocol	= ($_SERVER['SSL_SESSION_ID'] || $_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ? 'https://' : 'http://';
			$xhost		= preg_replace('/[^A-Za-z0-9\.:-]/', '', $_SERVER['HTTP_X_FORWARDED_HOST']);
			$host		= $_SERVER['HTTP_HOST'];
			if (empty($host)){
				$host	 = $_SERVER['SERVER_NAME'];
				$host	.= ($_SERVER['SERVER_PORT'] != 80) ? ':' . $_SERVER['SERVER_PORT'] : '';
			}
			return $protocol.(!empty($xhost) ? $xhost . '/' : '').preg_replace('/[^A-Za-z0-9\.:-]/', '', $host);
		}

		public function Copyright(){
			global $core;
			$year = (date('Y', time())== '2006') ? date('Y', time()) :'2006 - '.date('Y', time());
			$copyright='<br/><center><span class="copy">
										<a onclick="javascript:AboutPLUSDialog();" style="cursor:pointer;" onmouseover="style.textDecoration=\'underline\';"
										   onmouseout="style.textDecoration=\'none\';"><img src='.$this->root_path.'images/info.png> Credits</a>
										<br />
										   <a href="http://www.eqdkp-plus.com" target="_new" class="copy">EQDKP Plus '.$core->config['plus_version'].'</a>
										   &copy; '.$year.' by <a href="http://www.eqdkp-plus.com" target="_new" class="copy">Eqdkp Plus Development Team</a>
											</span></center><br />';
			return $copyright;
		}
		
		public function random_string($hash = false){
			$chars = array('a','A','b','B','c','C','d','D','e','E','f','F','g','G','h','H','i','I','j','J',
							'k','K','l','L','m','M','n','N','o','O','p','P','q','Q','r','R','s','S','t','T',
							'u','U','v','V','w','W','x','X','y','Y','z','Z','1','2','3','4','5','6','7','8',
							'9','0');
	
			$max_chars = count($chars) - 1;
			srand( (double) microtime()*1000000);
	
			$rand_str = '';
			for($i = 0; $i < 10; $i++){
				$rand_str = ( $i == 0 ) ? $chars[rand(0, $max_chars)] : $rand_str . $chars[rand(0, $max_chars)];
			}
			return ( $hash ) ? md5($rand_str) : $rand_str;
		}
			
		private function check_requirements(){
			global $tpl, $user;
			$error_message = array();
				
			// Check if Safe Mode
			if($this->pcache->safe_mode){
				if($this->pcache->CheckWrite()){
					$error_message[] = $user->lang['pcache_safemode_error'];
				}
			}
		
			// check if Data Folder is writable
			if(is_array($this->pcache->errors)){
				foreach($this->pcache->errors as $cacheerrors){
					$error_message[] = $user->lang[$cacheerrors];
				}
			}
				
			// check php-Version
			if (phpversion() < REQUIRED_PHP_VERSION){
				$error_message[] = sprintf($user->lang['php_too_old'], phpversion(), REQUIRED_PHP_VERSION);
			}
				
			//check function spl_autoload_register
			if (!function_exists('spl_autoload_register')){			
				$error_message[] = $user->lang['spl_autoload_register_notavailable'];
			}
				
			if (count($error_message) > 0){
				$out = $user->lang['requirements_notfilled'];
				$out .= '<ul>';
				foreach ($error_message as $message){
					$out .= '<li>'.$message.'</li>';
				}
				$out .= '</ul>';
				$this->global_warning($out);
			}
		}
		
		public function checkAdminTasks(){
			global $db, $user, $pdh, $eqdkp_root_path;
			$iTaskCount = 0;
			if ($user->check_auth('a_members_man', false)){
				$arrConfirmMembers = $pdh->get('member', 'confirm_required');
				$iTaskCount += count($arrConfirmMembers);
				
				$arrDeleteMembers = $pdh->get('member', 'delete_requested');
				$iTaskCount += count($arrDeleteMembers);
			}
			if ($user->check_auth('a_users_man', false)){
				$arrInactiveUser = $pdh->get('user', 'inactive');
				$iTaskCount += count($arrInactiveUser);
			}
			
			if ($iTaskCount > 0){
				$this->message('<a href="'.$eqdkp_root_path.'admin/manage_tasks.php'.$SID.'">'.sprintf($user->lang['cm_todo_txt'],$iTaskCount).'</a>', $user->lang['cm_todo_head'], 'default');
			}
			
			
		}
		
	}
?>