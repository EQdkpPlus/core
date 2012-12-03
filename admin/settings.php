<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2006
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2010 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 * 
 * $Id$
 */

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = '../';
$data_saved = false;
include_once($eqdkp_root_path . 'common.php');

// Check user permission
$user->check_auth('a_config_man');

	// Itemtooltip init
	if(!is_object($itt)) {
		include($eqdkp_root_path.'infotooltip/infotooltip.class.php');
		$itt = new infotooltip($settings, $pdl, $db, false, $eqdkp_root_path);
	}

	// Image Upload
	$logo_upload = new AjaxImageUpload;
	if($in->get('performupload') == 'true'){
		$logo_upload->PerformUpload('logo_path', 'eqdkp', 'logo', array(
			'filesize'	=> '2048576',
			'maxheight'	=> '600',
			'maxwidth'	=> '600'
		));
		die();
	}
	if (strpos($user->style['logo_path'],'://') > 1){
		$preview_image = $user->style['logo_path'];
	}else{
		$preview_image = $eqdkp_root_path."templates/".$user->style['template_path']."/images/".$user->style['logo_path'] ;
	}

	// ---------------------------------------------------------
	// SAVE SETINGS
	// ---------------------------------------------------------
	
	// Save it!
	if ($in->get('save_plus')){
				
		// Slider Values
		$slider_color_itm = $in->getArray('pk_color_items', 'int');
		
		// Whitelist
		$save_whitelist = array(
		
			// Global Tab
			'pk_updatecheck'=>array('nohmode'=>true), 'guildtag', 'parsetags', 'main_title', 'sub_title', 'dkp_name', 'pk_min_percvalue'=>array('value'=>$slider_color_itm[0]),
			'pk_max_percvalue'=>array('value'=>$slider_color_itm[1]), 'pk_enable_comments', 'pk_round_activate'=>array('default'=>0),
			'pk_round_precision'=>array('default'=>0), 'pk_debug', 'logo_path',
			
			// System
			'default_locale', 'server_path'=>array('nohmode'=>true), 'enable_gzip'=>array('nohmode'=>true,'default'=>0),
			'upload_allowed_extensions', 'cookie_domain'=>array('nohmode'=>true), 'cookie_name'=>array('nohmode'=>true),
			'cookie_path'=>array('nohmode'=>true), 'session_length'=>array('default'=>0), 'lib_email_method', 'admin_email', 'lib_email_sender_name',
			'lib_email_sendmail_path', 'lib_email_smtp_host', 'lib_email_smtp_auth', 'lib_email_smtp_user', 'lib_email_smtp_pw',
			'lib_email_signature'=>array('default'=>0), 'lib_email_signature_value', 'lib_recaptcha_okey', 'lib_recaptcha_pkey','timezone',
			'default_date_short'=>array('default'=>$user->lang['style_date_short']), 'default_date_long'=>array('default'=>$user->lang['style_date_long']),
			'default_date_time'=>array('default'=>$user->lang['style_time']),
			
			// User
			'default_lang', 'account_activation'=>array('default'=>0), 'disable_registration', 'pk_enable_captcha', 'pk_disable_username_change',
			'default_style_overwrite',
			
			// Chars
			'pk_class_color', 'special_members'=>array('value'=>serialize($in->getArray('special_members', 'int'))), 'pk_show_twinks'=>array('default'=>0),
			'hide_inactive'=>array('default'=>0), 'inactive_period'=>array('default'=>0), 'pk_detail_twink'=>array('default'=>0),
			
			// Contact
			'pk_contact_name', 'pk_contact_email', 'pk_contact_website', 'pk_contact_irc', 'pk_contact_admin_messenger', 'pk_contact_custominfos',
			
			// Game
			'default_game', 'game_language',
			
			// Portal
			'pk_permanent_portal'=>array('value'=>serialize($in->getArray('pk_permanent_portal', 'string'))),'pk_lightbox_enabled','pk_air_max_resize_width',
			'pk_show_newsarchive'=>array('default'=>0), 'pk_newsarchive_position'=>array('default'=>'left'), 'enable_newscategories', 'start_page',
			
			// Layout
			'pk_itemhistory_dia', 'pk_enable_3ditem', 'pk_hide_shop'=>array('nohmode'=>true,'default'=>0), 'default_alimit'=>array('default'=>0),
			'default_elimit'=>array('default'=>0), 'default_ilimit'=>array('default'=>0), 'default_nlimit'=>array('default'=>0),
			'default_rlimit'=>array('default'=>0), 'pk_newsloot_limit', 'pk_noRaids', 'pk_noEvents', 'pk_noItemPrices','pk_noDKP', 'pk_noRoster', 

			//Infotooltip
			'infotooltip_use', 'itt_debug', 'itt_autosearch', 'itt_useitemlist', 'itt_default_icon', 'itt_icon_ext', 'itt_icon_loc', 'itt_prio1',
			'itt_prio2', 'itt_prio3', 'itt_prio4', 'itt_langprio1', 'itt_langprio2', 'itt_langprio3',
			
			// SMS
			'pk_sms_disable', 'pk_sms_username', 'pk_sms_password',
		);

		// Perform the save
		$save_array = array();
		foreach($save_whitelist as $csetname=>$csetoptions){
			$skip = ($_HMODE && (is_array($csetoptions) && $csetoptions['nohmode'] == true)) ? true : false;
			if(!$skip){
				if(is_array($csetoptions)){
					$save_array[$csetname]		= (isset($csetoptions['value'])) ? $csetoptions['value'] : $in->get($csetname, $csetoptions['default']);
				}else{
					$save_array[$csetoptions]	= $in->get($csetoptions);
				}
			}
		}
		
		// Dynamic per Game Settings - Saving
		$myprofiledata = $eqdkp_root_path.'games/'.$game->get_game().'/admin_data.php';
		if(is_file($myprofiledata)){
			require($myprofiledata);
			if(is_array($config_fields)){
				foreach($config_fields['data'] as $confvars){
					$save_array[$confvars['name']] = ($confvars['edecode']) ? html_entity_decode($in->get($confvars['name']) ,ENT_QUOTES,"UTF-8") : $in->get($confvars['name']);
				}
			}
		}
		
		// Dynamic ItemTooltip Saving
		$ittsettdata = $itt->get_extra_settings();
		if(is_array($ittsettdata)){
			foreach($ittsettdata as $confvars){
				$save_array[$confvars['name']] = ($confvars['edecode']) ? html_entity_decode($in->get($confvars['name']) ,ENT_QUOTES,"UTF-8") : $in->get($confvars['name']);
			}
		}
		
		// Save new logo
		if ($in->get('logo_path') <> $user->style['logo_path'] ){
			$newpath = $core->BuildLink().$pcache->FileLink($in->get('logo_path'),'eqdkp','logo') ;
			$sql = "UPDATE __styles SET logo_path = '".$newpath."' ";
			$result = $db->query($sql);
		}
		
		// Trash the thumb folder if width is changed
		if($core->config['pk_air_max_resize_width'] != $in->get('pk_air_max_resize_width')){
			$pcache->Delete($pcache->FolderPath('news/thumb', 'eqdkp', false));
		}
		
		// Perform the game-change-things...
		$game_changed = false;
		if (($in->get('default_game') != $core->config['default_game']) || ($in->get('game_language') != $core->config['game_language'])){
			$game_changed = true;
		}
		
		// Prio1 of infotooltip changed?
		if ($in->get('itt_prio1') != $core->config['itt_prio1']) {
			$save_array = array_merge($save_array, $itt->changed_prio1($in->get('itt_prio1')));
		}
		
		// Save the settings array
		$core->config_set($save_array);
		
		// Since ChangeGame alters Config it has to be executed after config-save
		if($game_changed) {
			$game->ChangeGame($in->get('default_game'), $in->get('game_language'));
		}
		
		//clear cache now
		$pdc->flush();
		
		// The Saved-Message
		$core->message($user->lang['pk_succ_saved'], $user->lang['pk_save_title'], 'green');
	}
	
	// ---------------------------------------------------------
	// Ajax
	// ---------------------------------------------------------
	
	// Build the default game array
	$langfiles = $games = array();
	foreach($game->get_games() as $sgame){
		$games[$sgame] = $game->game_name($sgame);
		$langfiles[$sgame] = sdir($eqdkp_root_path . 'games/'.$sgame.'/language/', '*.php', '.php');
	}

	// check for the ajax...
	if($in->get('ajax') == 'games'){
		echo $jquery->dd_create_ajax($langfiles[$in->get('requestid')], array('format'=>'ucfirst'));die();
	}
	
	// ---------------------------------------------------------
	// Build the Dropdown Arrays
	// ---------------------------------------------------------
	$newsloot_limit = array(
		'all'		=> 0,
		'5'			=> 5,
		'10'		=> 10,
		'15'		=> 15,
		'20'		=> 20
	);
	
	$a_debug_mode= array(
		'0'			=> $user->lang['pk_set_debug_type0'],
		'1'			=> $user->lang['pk_set_debug_type1'],
		'2'			=> $user->lang['pk_set_debug_type2'],
		'3'			=> $user->lang['pk_set_debug_type3'],
	);
	
	$a_modelviewer = array(
		'0'			=> 'WoWHead',
		'1'			=> 'Thottbot',
		'2'			=> 'SpeedyDragon'
	);
	
	$accact_array = array(
		'0'			=> $user->lang['none'],
		'1'			=> $user->lang['user'],
		'2'			=> $user->lang['admin'],
	);
	
	$portal_positions = array(
		'right'		=> $user->lang['portalplugin_right'],
		'middle'	=> $user->lang['portalplugin_middle'],
		'bottom'	=> $user->lang['portalplugin_bottom'],
	);
	
	$a_newsarchive_position = array(
		'right'		=> $user->lang['portalplugin_right'],
		'left'		=> $user->lang['portalplugin_left'],
		'bottom'	=> $user->lang['portalplugin_bottom'],
		'middle'	=> $user->lang['portalplugin_middle'],
	);
	
	$mail_array = array(
		'mail'      => $user->lang['lib_email_mail'],
		'sendmail'  => $user->lang['lib_email_sendmail'],
		'smtp'      => $user->lang['lib_email_smtp'],
	);
	
	// Startpage
	$menus		= $core->gen_menus();
	$pages		= array_merge($menus['menu1'], $menus['menu2']);
	$pages[]	= array('link'	=> 'news_archive.php', 'text' => $user->lang['newsarchive_title'], 'check' => '');
	unset($menus);
	if(is_array($pdh->get('infopages', 'startpage_list', array()))){
		// Add Infopages to startpage array
		$pages = array_merge_recursive( $pages, $pdh->get('infopages', 'startpage_list', array()));
	}
	foreach($pages as $page){
		$link = preg_replace('#\?s\=([0-9A-Za-z]{1,32})?#', '', $page['link']);
		$link = preg_replace('#\.php&amp;#', '.php?', $link);
		$link = preg_replace('#\.php&#', '.php?', $link);
		$text = ( isset($user->data['username']) ) ? str_replace($user->data['username'], $user->lang['username'], $page['text']) : $page['text'];
	
		if($link != 'login.php?logout=true'){
			$startpage_array[$link] = $text;
		}
		unset($link, $text);
	}
	
	// Build language array
	if($dir = @opendir($core->root_path . 'language/')){
		while ( $file = @readdir($dir) ){
			if ((!is_file($core->root_path . 'language/' . $file)) && (!is_link($core->root_path . 'language/' . $file)) && valid_folder($file)){
				include($core->root_path.'language/'.$file.'/lang_main.php');
				$lang_name_tp = (($lang['ISO_LANG_NAME']) ? $lang['ISO_LANG_NAME'].' ('.$lang['ISO_LANG_SHORT'].')' : ucfirst($file));
				$language_array[$file]					= $lang_name_tp;
				$locale_array[$lang['ISO_LANG_SHORT']]	= $lang_name_tp;
			}
		}
	}
	
	// ---------------------------------------------------------
	// Dynamic per game settings
	// ---------------------------------------------------------
	$myprofiledata = $eqdkp_root_path.'games/'.$game->get_game().'/admin_data.php';
	if(is_file($myprofiledata)){
		require_once($myprofiledata);
		if(is_array($config_fields)){
			foreach($config_fields['data'] as $confvars){
				$select_value = ($core->config[$confvars['name']]) ? $core->config[$confvars['name']] : $confvars['default'];
				if($ccfield = $CharTools->generateField($confvars, $confvars['name'], $select_value)){
					$tpl->assign_block_vars('config_row', array(
						'NAME'		=> $game->glang($confvars['language']),
						'FIELD'		=> $ccfield,
						'HELP'		=> $game->glang($confvars['language'].'_help')
					));
				}
			}
		}
	}
	
	// ---------------------------------------------------------
	// Dynamic itemTooltip settings
	// ---------------------------------------------------------
	$ittsettdata = $itt->get_extra_settings();
	if(is_array($ittsettdata)){
		foreach($ittsettdata as $confvars){
			$ittsett_value = ($core->config[$confvars['name']]) ? $core->config[$confvars['name']] : $confvars['default'];
			if($ccfield = $CharTools->generateField($confvars, $confvars['name'], $ittsett_value)){
				$tpl->assign_block_vars('itt_extrasett_row', array(
					'NAME'		=> $user->lang[$confvars['language']],
					'FIELD'		=> $ccfield,
					'HELP'		=> $user->lang[$confvars['language'].'_help']
				));
			}
		}
	}
	
	$itt_langlist	= $itt->get_supported_languages();
	for($i=1; $i<=3; $i++) {
		$tpl->assign_block_vars('itt_lang_row', array(
			'NAME'		=> sprintf($user->lang['pk_itt_langprio'], $i),
			'FIELD'		=> $html->widget(array('fieldtype'=>'dropdown','name'=>'itt_langprio'.$i,'options'=>$itt_langlist,'selected'=>$core->config['itt_langprio'.$i])),
			'HELP'		=> $user->lang['pk_itt_help_langprio']
		));
	}
	
	//check if user wanted to reset itt-cache
	if($in->get('itt_reset', false)) {
		$itt->reset_cache();
	}
	$itt_parserlist	= $itt->get_parserlist();
	
	// ---------------------------------------------------------
	// Member Array
	// ---------------------------------------------------------
	$members = $pdh->aget('member', 'name', 0, array($pdh->get('member', 'id_list', array(false, false, false))));
	asort($members);
	
	// ---------------------------------------------------------
	// Portal position
	// ---------------------------------------------------------
	$selected_portal_pos = unserialize(stripslashes($core->config['pk_permanent_portal']));
	
	// ---------------------------------------------------------
	// Default email signature
	// ---------------------------------------------------------
	$signature  = "--\n";
	$signature .= $user->lang['nl_signature_value'];
	$signature .= $core->config['guildtag'];
	$signature .= "\nEQdkp Plus: ";
	$signature .= $core->BuildLink();

	// Bit of jQuery..
	if($game->get_importAuth('a_members_man', 'char_mupdate')){
		$jquery->Dialog('MassUpdateChars', $user->lang['uc_import_adm_update'], array('url'=>$game->get_importers('char_mupdate', true), 'width'=>'500', 'height'=>'130', 'onclose'=>'settings.php'));
	}
	if($game->get_importAuth('a_members_man', 'guild_import')){
		$jquery->Dialog('GuildImport', $user->lang['uc_import_guild_wh'], array('url'=>$game->get_importers('guild_import', true), 'width'=>'470', 'height'=>'340', 'onclose'=>'settings.php'));
	}
	
	// ---------------------------------------------------------
	// Output to the page
	// ---------------------------------------------------------
	$jquery->Tab_header('plus_sett_tabs', true);
	$game_array = $jquery->dd_ajax_request('default_game', 'game_language', $games, array('--------'), $core->config['default_game'], $core->config['game_language'], 'settings.php?ajax=games');
	$tpl->assign_vars(array(
		'FORM_ACTION'			=> $_SERVER["PHP_SELF"],
		'SAVE_BUTTON'			=> $html->Button('save_plus', $user->lang['save']),
		'RESET_BUTTON'			=> $html->Button('reset', $user->lang['reset'], 'reset'),
		'L_ITEMSTATS_INFO'		=> $user->lang['pk_is_info'],

		// Tab names
		'L_TAB_GLOBAL'			=> $user->lang['pk_tab_global'],
		'L_TAB_SYSTEM'			=> $user->lang['pk_set_system'],
		'L_TAB_LAYOUT'			=> $user->lang['pk_set_layout'],
		'L_TAB_PORTAL'			=> $user->lang['pk_set_portal'],
		'L_TAB_USER'			=> $user->lang['pk_set_user'],
		'L_TAB_CONTACT'			=> $user->lang['pk_set_contact'],
		'L_TAB_LINKS'			=> $user->lang['pk_tab_links'],
		'L_TAB_NEWS'			=> $user->lang['pk_set_news_tab'],
		'L_TAB_ITEMSTATS'		=> $user->lang['pk_tab_itemstats'],
		'L_TAB_SMS'				=> $user->lang['pk_set_sms_tab'],
		'L_TAB_GAME'			=> $user->lang['pk_set_game_tab'],
		'L_TAB_CHARS'			=> $user->lang['pk_set_chars'],

		// GLOBALS
		'GLOBAL_NO_HMODE'		=> (!$_HMODE) ? true : false,

		// Tab 1 - GLOBAL
		'HEAD_GLOBAL'			=> $user->lang['pk_set_globaltable'],
		
		'C_UPDATECHECK'			=> $html->widget(array('fieldtype'=>'checkbox','name'=>'pk_updatecheck','selected'=>$core->config['pk_updatecheck'])),
		'L_UPDATECHECK'			=> $user->lang['pk_set_Updatecheck'],
		'H_UPDATECHECK'			=> $user->lang['pk_help_autowarning'],
		
		'T_GUILDTAG'			=> $html->widget(array('fieldtype'=>'text','name'=>'guildtag','value'=>$core->config['guildtag'],'size'=>'35')),
		'L_GUILDTAG'			=> $user->lang['guildtag_note'],
		'H_GUILDTAG'			=> $user->lang['guildtag_note'],

		'TA_PARSETAGS'			=> $html->widget(array('fieldtype'=>'textarea','name'=>'parsetags','rows'=>'4','size'=>'35','value'=>$core->config['main_title'])),
		'L_PARSETAGS'			=> $user->lang['parsetags'],
		'H_PARSETAGS'			=> $user->lang['parsetags_note'],

		'T_MAIN_TITLE'			=> $html->widget(array('fieldtype'=>'text','name'=>'main_title','value'=>$core->config['main_title'],'size'=>'40')),
		'L_MAIN_TITLE'			=> $user->lang['site_name'],
		'H_MAIN_TITLE'			=> $user->lang['main_title_note'],
		
		'T_SUB_TITLE'			=> $html->widget(array('fieldtype'=>'text','name'=>'sub_title','value'=>$core->config['sub_title'],'size'=>'40')),
		'L_SUB_TITLE'			=> $user->lang['site_description'],
		'H_SUB_TITLE'			=> $user->lang['sub_title_note'],
		
		'T_DKP_NAME'			=> $html->widget(array('fieldtype'=>'text','name'=>'dkp_name','value'=>$core->config['dkp_name'],'size'=>'5')),
		'L_DKP_NAME'			=> $user->lang['point_name'],
		'H_DKP_NAME'			=> $user->lang['point_name_note'],
		
		'T_LOGO_PATH'			=> $html->widget(array('fieldtype'=>'hidden','name'=>'logo_path','value'=>$user->style['logo_path'],'size'=>'40')),
		'A_IMAGE_UPLOAD'		=> $logo_upload->Show('logo_path', 'settings.php?performupload=true', $preview_image,false),
		'L_IMAGE_UPLOAD'		=> $user->lang['dkp_logoimg_note'],		
				
		'C_COMMENTS'			=> $html->widget(array('fieldtype'=>'checkbox','name'=>'pk_enable_comments','selected'=>$core->config['pk_enable_comments'])),
		'L_COMMENTS'			=> $user->lang['pk_set_comments_enable'],
		'H_COMMENTS'			=> $user->lang['pk_hel_pcomments_enable'],
		
		'S_COLOR_ITEMS'			=>$jquery->Slider('pk_color_items', array('label' => $user->lang['pk_color_items'], 'values' => array((($core->config['pk_min_percvalue'] > 0) ? $core->config['pk_min_percvalue'] : 34), (($core->config['pk_max_percvalue']) ? $core->config['pk_max_percvalue'] : 67)), 'min'=>0, 'max'=>100, 'width'=> '300px'), 'range'),
		'L_COLOR_ITEMS'			=> $user->lang['pk_color_items'],
		'H_COLOR_ITEMS'			=> $user->lang['pk_color_items_help'],
		
		'C_ROUND_ACTIVATE'		=> $html->widget(array('fieldtype'=>'checkbox','name'=>'pk_round_activate','selected'=>$core->config['pk_round_activate'])),
		'L_ROUND_ACTIVATE'		=> $user->lang['pk_set_round_activate'],
		'H_ROUND_ACTIVATE'		=> $user->lang['pk_help_round_activate'],

		'T_ROUND_PRECISION'		=> $html->widget(array('fieldtype'=>'text','name'=>'pk_round_precision','value'=>$core->config['pk_round_precision'],'size'=>'2')),
		'L_ROUND_PRECISION'		=> $user->lang['pk_set_round_precision'],
		'H_ROUND_PRECISION'		=> $user->lang['pk_help_round_precision'],
		
		'D_DEBUGMODE'			=> $html->widget(array('fieldtype'=>'dropdown','name'=>'pk_debug','options'=>$a_debug_mode,'selected'=>$core->config['pk_debug'])),
		'L_DEBUGMODE'			=> $user->lang['pk_set_debug'],
		'H_DEBUGMODE'			=> $user->lang['pk_help_debug'],

		// Tab 2 - SYSTEM
		'HEAD_DEFAULT_SYSTEM'	=> $user->lang['pk_set_defaults'],

		'D_DEFAULT_LOCALE'		=> $html->widget(array('fieldtype'=>'dropdown','name'=>'default_locale','options'=>$locale_array,'selected'=>$core->config['default_locale'])),
		'L_DEFAULT_LOCALE'		=> $user->lang['default_locale'],
		'H_DEFAULT_LOCALE'		=> $user->lang['default_locale_note'],

		'T_SERVER_PATH'			=> $html->widget(array('fieldtype'=>'text','name'=>'server_path','value'=>$core->config['server_path'],'size'=>'50')),
		'L_SERVER_PATH'			=> $user->lang['script_path'],
		'H_SERVER_PATH'			=> $user->lang['script_path_note'],

		'C_GZIP'				=> $html->widget(array('fieldtype'=>'checkbox','name'=>'enable_gzip','selected'=>$core->config['enable_gzip'])),
		'L_GZIP'				=> $user->lang['enable_gzip'],
		'H_GZIP'				=> $user->lang['enable_gzip_note'],

		'T_ALLOWED_EXTENSIONS'	=> $html->widget(array('fieldtype'=>'text','name'=>'upload_allowed_extensions','value'=>$core->config['upload_allowed_extensions'],'size'=>'50')),
		'L_ALLOWED_EXTENSIONS'	=> $user->lang['upload_extensions'],
		'H_ALLOWED_EXTENSIONS'	=> $user->lang['upload_extensions_help'],

		'HEAD_COOKIE_SETTINGS'	=> $user->lang['cookie_settings'],
		
		'T_COOKIE_DOMAIN'		=> $html->widget(array('fieldtype'=>'text','name'=>'cookie_domain','value'=>$core->config['cookie_domain'],'size'=>'25')),
		'L_COOKIE_DOMAIN'		=> $user->lang['cookie_domain'],
		'H_COOKIE_DOMAIN'		=> $user->lang['cookie_domain_note'],

		'T_COOKIE_NAME'			=> $html->widget(array('fieldtype'=>'text','name'=>'cookie_name','value'=>$core->config['cookie_name'],'size'=>'25')),
		'L_COOKIE_NAME'			=> $user->lang['cookie_name'],
		'H_COOKIE_NAME'			=> $user->lang['cookie_name_note'],

		'T_COOKIE_PATH'			=> $html->widget(array('fieldtype'=>'text','name'=>'cookie_path','value'=>$core->config['cookie_path'],'size'=>'25')),
		'L_COOKIE_PATH'			=> $user->lang['cookie_path'],
		'H_COOKIE_PATH'			=> $user->lang['cookie_path_note'],

		'T_SESSION_LENGTH'		=> $html->widget(array('fieldtype'=>'text','name'=>'session_length','value'=>$core->config['session_length'],'size'=>'7')),
		'L_SESSION_LENGTH'		=> $user->lang['session_length'],
		'H_SESSION_LENGTH'		=> $user->lang['session_length_note'],
		
		'HEAD_EMAIL'			=> $user->lang['pk_set_email_header'],
		
		'D_EMAIL_METHOD'		=> $html->widget(array('fieldtype'=>'dropdown','name'=>'lib_email_method','options'=>$mail_array,'selected'=>$core->config['default_game'])),
		'L_EMAIL_METHOD'		=> $user->lang['lib_email_method'],
		'H_EMAIL_METHOD'		=> $user->lang['lib_email_method_help'],
	
		'T_ADMIN_EMAIL'			=> $html->widget(array('fieldtype'=>'text','name'=>'admin_email','value'=>$core->config['admin_email'],'size'=>'30')),
		'L_ADMIN_EMAIL'			=> $user->lang['admin_email'],
		'H_ADMIN_EMAIL'			=> $user->lang['admin_email_note'],

		'T_EMAIL_SENDER_NAME'	=> $html->widget(array('fieldtype'=>'text','name'=>'lib_email_sender_name','value'=>$core->config['lib_email_sender_name'],'size'=>'30')),
		'L_EMAIL_SENDER_NAME'	=> $user->lang['lib_email_sender_name'],
		'H_EMAIL_SENDER_NAME'	=> $user->lang['lib_email_sender_name_help'],

		'T_EMAIL_SENDER_PATH'	=> $html->widget(array('fieldtype'=>'text','name'=>'lib_email_sendmail_path','value'=>$core->config['lib_email_sendmail_path'],'size'=>'30')),
		'L_EMAIL_SENDER_PATH'	=> $user->lang['lib_email_sendmail_path'],
		'H_EMAIL_SENDER_PATH'	=> $user->lang['lib_email_sendmail_path_help'],

		'T_EMAIL_SENDER_HOST'	=> $html->widget(array('fieldtype'=>'text','name'=>'lib_email_smtp_host','value'=>$core->config['lib_email_smtp_host'],'size'=>'30')),
		'L_EMAIL_SENDER_HOST'	=> $user->lang['lib_email_smtp_host'],
		'H_EMAIL_SENDER_HOST'	=> $user->lang['lib_email_smtp_host_help'],

		'C_EMAIL_SMTP_AUTH'		=> $html->widget(array('fieldtype'=>'checkbox','name'=>'lib_email_smtp_auth','selected'=>$core->config['lib_email_smtp_auth'])),
		'L_EMAIL_SMTP_AUTH'		=> $user->lang['lib_email_smtp_auth'],
		'H_EMAIL_SMTP_AUTH'		=> $user->lang['lib_email_smtp_auth_help'],
	
		'T_EMAIL_SMTP_USER'		=> $html->widget(array('fieldtype'=>'text','name'=>'lib_email_smtp_user','value'=>$core->config['lib_email_smtp_user'],'size'=>'30')),
		'L_EMAIL_SMTP_USER'		=> $user->lang['lib_email_smtp_user'],
		'H_EMAIL_SMTP_USER'		=> $user->lang['lib_email_smtp_user_help'],

		'T_EMAIL_SMTP_PASSWORD'	=> $html->widget(array('fieldtype'=>'text','name'=>'lib_email_smtp_pw','value'=>$core->config['lib_email_smtp_pw'],'size'=>'30')),
		'L_EMAIL_SMTP_PASSWORD'	=> $user->lang['lib_email_smtp_password'],
		'H_EMAIL_SMTP_PASSWORD'	=> $user->lang['lib_email_smtp_password_help'],

		'C_EMAIL_SIGNATURE'		=> $html->widget(array('fieldtype'=>'checkbox','name'=>'lib_email_signature','selected'=>$core->config['lib_email_signature'])),
		'L_EMAIL_SIGNATURE'		=> $user->lang['lib_email_signature'],
		'H_EMAIL_SIGNATURE'		=> $user->lang['lib_email_signature_help'],

		'TA_EMAIL_SIGNATURE_V'	=> $html->widget(array('fieldtype'=>'textarea','name'=>'lib_email_signature_value','rows'=>'5','size'=>'80','value'=>(($core->config['lib_email_signature_value'] == "") ? $signature : stripcslashes($core->config['lib_email_signature_value'])))),
		'L_EMAIL_SIGNATURE_V'	=> $user->lang['lib_email_signature_value'],
		'H_EMAIL_SIGNATURE_V'	=> $user->lang['lib_email_signature_value_help'],
		
		'HEAD_CAPTCHA'			=> $user->lang['pk_set_recaptcha_header'],

		'T_RECAPTCHA_OKEY'		=> $html->widget(array('fieldtype'=>'text','name'=>'lib_recaptcha_okey','value'=>$core->config['lib_recaptcha_okey'],'size'=>'30')),
		'L_RECAPTCHA_OKEY'		=> $user->lang['lib_recaptcha_okey'],
		'H_RECAPTCHA_OKEY'		=> $user->lang['lib_recaptcha_okey_help'],

		'T_RECAPTCHA_PKEY'		=> $html->widget(array('fieldtype'=>'text','name'=>'lib_recaptcha_pkey','value'=>$core->config['lib_recaptcha_pkey'],'size'=>'30')),
		'L_RECAPTCHA_PKEY'		=> $user->lang['lib_recaptcha_pkey'],
		'H_RECAPTCHA_PKEY'		=> $user->lang['lib_recaptcha_pkey_help'],
		
		'HEAD_DEFAULT_DATESETT'	=> $user->lang['pi_date'],
		
		'D_TIMEZONE'			=> $html->widget(array('fieldtype'=>'dropdown','name'=>'timezone','options'=>$time->timezones,'selected'=>$core->config['timezone'])),
		'L_TIMEZONE'			=> $user->lang['timezone'],
		'H_TIMEZONE'			=> $user->lang['timezone_note'],

		'T_DEFAULT_DATETIME'	=> $html->widget(array('fieldtype'=>'text','name'=>'default_date_time','value'=>$core->config['default_date_time'],'size'=>'10')),
		'L_DEFAULT_DATETIME'	=> $user->lang['adduser_date_time'],
		'H_DEFAULT_DATETIME'	=> $user->lang['adduser_date_note_nolink'],

		'T_DEFAULT_DATESHORT'	=> $html->widget(array('fieldtype'=>'text','name'=>'default_date_short','value'=>$core->config['default_date_short'],'size'=>'20')),
		'L_DEFAULT_DATESHORT'	=> $user->lang['adduser_date_short'],
		'H_DEFAULT_DATESHORT'	=> $user->lang['adduser_date_note_nolink'],

		'T_DEFAULT_DATELONG'	=> $html->widget(array('fieldtype'=>'text','name'=>'default_date_long','value'=>$core->config['default_date_long'],'size'=>'20')),
		'L_DEFAULT_DATELONG'	=> $user->lang['adduser_date_long'],
		'H_DEFAULT_DATELONG'	=> $user->lang['adduser_date_note_nolink'],
		
		// Tab 3 - User
		'HEAD_USER_SETTINGS'	=> $user->lang['user_settings'],
		
		'D_DEFAULT_LANGUAGE'	=> $html->widget(array('fieldtype'=>'dropdown','name'=>'default_lang','options'=>$language_array,'selected'=>$core->config['default_lang'])),
		'L_DEFAULT_LANGUAGE'	=> $user->lang['pk_deflanguage'],
		'H_DEFAULT_LANGUAGE'	=> $user->lang['default_lang_note'],
		
		'R_ACCOUNT_ACTIVATION'	=> $html->widget(array('fieldtype'=>'radio','name'=>'account_activation','options'=>$accact_array,'selected'=>$core->config['account_activation'])),
		'L_ACCOUNT_ACTIVATION'	=> $user->lang['enable_account_activation'],
		'H_ACCOUNT_ACTIVATION'	=> $user->lang['account_activation_note'],

		'C_REGISTRATION_ACC'	=> $html->widget(array('fieldtype'=>'checkbox','name'=>'disable_registration','selected'=>$core->config['disable_registration'])),
		'L_REGISTRATION_ACC'	=> $user->lang['disable_account_registration'],
		'H_REGISTRATION_ACC'	=> $user->lang['disable_account_registration_note'],

		'C_CAPTCHA'				=> $html->widget(array('fieldtype'=>'checkbox','name'=>'pk_enable_captcha','selected'=>$core->config['pk_enable_captcha'])),
		'L_CAPTCHA'				=> $user->lang['enable_captcha'],
		'H_CAPTCHA'				=> $user->lang['enable_captcha_help'],

		'C_USERNAME_CHANGE'		=> $html->widget(array('fieldtype'=>'checkbox','name'=>'pk_disable_username_change','selected'=>$core->config['pk_disable_username_change'])),
		'L_USERNAME_CHANGE'		=> $user->lang['pk_disable_username_change'],
		'H_USERNAME_CHANGE'		=> $user->lang['pk_disable_username_change_help'],

		'C_DEFAULT_GAME_OR'		=> $html->widget(array('fieldtype'=>'checkbox','name'=>'default_style_overwrite','selected'=>$core->config['default_style_overwrite'])),
		'L_DEFAULT_GAME_OR'		=> $user->lang['default_style_overwrite'],
		'H_DEFAULT_GAME_OR'		=> $user->lang['default_style_overwrite_note'],
		
		// Tab 4 - CHARS
		'HEAD_CHARS'			=> $user->lang['pk_set_chars_settings'],
		
		'C_CLASSCOLOR'			=> $html->widget(array('fieldtype'=>'checkbox','name'=>'pk_class_color','selected'=>$core->config['pk_class_color'])),
		'L_CLASSCOLOR'			=> $user->lang['pk_set_ClassColor'],
		'H_CLASSCOLOR'			=> $user->lang['pk_help_colorclassnames'],
		
		'M_SPECIALMEMBER'		=> $jquery->MultiSelect('special_members', $members, unserialize($core->config['special_members'])),
		'L_SPECIALMEMBER'		=> $user->lang['special_members'],
		'H_SPECIALMEMBER'		=> $user->lang['special_members_help'],

		'C_SHOW_TWINKS'			=> $html->widget(array('fieldtype'=>'checkbox','name'=>'pk_show_twinks','selected'=>$core->config['pk_show_twinks'])),
		'L_SHOW_TWINKS'			=> $user->lang['pk_show_twinks'],
		'H_SHOW_TWINKS'			=> $user->lang['pk_help_show_twinks'],
		
		'C_DETAIL_TWINK'		=> $html->widget(array('fieldtype'=>'checkbox','name'=>'pk_detail_twink','selected'=>$core->config['pk_detail_twink'])),
		'L_DETAIL_TWINK'		=> $user->lang['pk_detail_twink'],
		'H_DETAIL_TWINK'		=> $user->lang['pk_help_detail_twink'],
		
		'C_HIDE_INACTIVE'		=> $html->widget(array('fieldtype'=>'checkbox','name'=>'hide_inactive','selected'=>$core->config['hide_inactive'])),
		'L_HIDE_INACTIVE'		=> $user->lang['hide_inactive'],
		'H_HIDE_INACTIVE'		=> $user->lang['hide_inactive_note'],
		
		'T_INACTIVE_PERIOD'		=> $html->widget(array('fieldtype'=>'text','name'=>'inactive_period','value'=>$core->config['inactive_period'],'size'=>'5')),
		'L_INACTIVE_PERIOD'		=> $user->lang['hide_inactive'],
		'H_INACTIVE_PERIOD'		=> $user->lang['hide_inactive_note'],
		
		// Tab 5 - CONTACT
		'HEAD_CONTACT'			=> $user->lang['pk_contact'],
		
		'T_CONTACT_NAME'		=> $html->widget(array('fieldtype'=>'text','name'=>'pk_contact_name','value'=>htmlspecialchars($core->config['pk_contact_name'],ENT_QUOTES),'size'=>'40')),
		'L_CONTACT_NAME'		=> $user->lang['pk_contact_name'],
		'H_CONTACT_NAME'		=> $user->lang['pk_contact_name_help'],

		'T_CONTACT_EMAIL'		=> $html->widget(array('fieldtype'=>'text','name'=>'pk_contact_email','value'=>htmlspecialchars($core->config['pk_contact_email'],ENT_QUOTES),'size'=>'40')),
		'L_CONTACT_EMAIL'		=> $user->lang['pk_contact_email'],
		'H_CONTACT_EMAIL'		=> $user->lang['pk_contact_email_help'],

		'T_CONTACT_WEBSITE'		=> $html->widget(array('fieldtype'=>'text','name'=>'pk_contact_website','value'=>htmlspecialchars($core->config['pk_contact_website'],ENT_QUOTES),'size'=>'40')),
		'L_CONTACT_WEBSITE'		=> $user->lang['pk_contact_website'],
		'H_CONTACT_WEBSITE'		=> $user->lang['pk_contact_website_help'],

		'T_CONTACT_IRC'			=> $html->widget(array('fieldtype'=>'text','name'=>'pk_contact_irc','value'=>htmlspecialchars($core->config['pk_contact_irc'],ENT_QUOTES),'size'=>'40')),
		'L_CONTACT_IRC'			=> $user->lang['pk_contact_irc'],
		'H_CONTACT_IRC'			=> $user->lang['pk_contact_irc_help'],

		'T_CONTACT_MESSENGER'	=> $html->widget(array('fieldtype'=>'text','name'=>'pk_contact_admin_messenger','value'=>htmlspecialchars($core->config['pk_contact_admin_messenger'],ENT_QUOTES),'size'=>'40')),
		'L_CONTACT_MESSENGER'	=> $user->lang['pk_contact_admin_messenger'],
		'H_CONTACT_MESSENGER'	=> $user->lang['pk_contact_admin_messenger_help'],

		'T_CONTACT_CINFO'		=> $html->widget(array('fieldtype'=>'text','name'=>'pk_contact_custominfos','value'=>htmlspecialchars($core->config['pk_contact_custominfos'],ENT_QUOTES),'size'=>'50')),
		'L_CONTACT_CINFO'		=> $user->lang['pk_contact_custominfos'],
		'H_CONTACT_CINFO'		=> $user->lang['pk_contact_custominfos_help'],
		
		// Tab 6 - GAME
		'HEAD_GAME_SELECTION'	=> $user->lang['game_settings_head'],
		
		'D_DEFAULT_GAME'		=> $game_array[0],
		'L_DEFAULT_GAME'		=> $user->lang['pk_set_defaultgame'],
		'H_DEFAULT_GAME'		=> $user->lang['default_game_note'],

		'D_GAME_LANGUAGE'		=> $game_array[1],
		'L_GAME_LANGUAGE'		=> $user->lang['pk_set_gamelanguage'],
		'H_GAME_LANGUAGE'		=> $user->lang['game_language_note'],
		
		'HEAD_GAME_SETTINGS'	=> $game->glang($config_fields['tabname']),
		
		// Game importers
		'HEAD_GAME_IMPORTERS'	=> $game->glang('importer_head_txt'),
		
		'ENABLED_MASSUPDATE'	=> $game->get_importAuth('a_members_man', 'char_mupdate'),
		'ENABLED_GUILDIMPORT'	=> $game->get_importAuth('a_members_man', 'guild_import'),
		'DISABLE_IU_BUTTONS'	=> ($core->config['uc_servername'] == '' && $game->get_importers('guild_imp_rsn')) ? true : false,
		
		'L_GAME_MASSUPDATE'		=> $game->glang('uc_update_all'),
		'H_GAME_MASSUPDATE'		=> $game->glang('uc_update_all_help'),
		'LU_GAME_MASSUPDATE'	=> ($core->config['uc_profileimported']) ? $user->lang['uc_last_updated'].': '.date($user->style['date_time'], $core->config['uc_profileimported']) : $user->lang['uc_never_updated'],
		'B_GAME_MASSUPDATE'		=> $user->lang['uc_bttn_update'],
		
		'L_GAME_GUILDIMPORT'	=> $game->glang('uc_import_guild'),
		'H_GAME_GUILDIMPORT'	=> $game->glang('uc_import_guild_help'),
		'B_GAME_GUILDIMPORT'	=> $user->lang['uc_bttn_import'],
		
		// Tab 7 - PORTAL
		'HEAD_PORTAL'			=> $user->lang['pk_set_portal_head'],
		
		'D_STARTPAGE'			=> $html->widget(array('fieldtype'=>'dropdown','name'=>'start_page','options'=>$startpage_array,'selected'=>$core->config['start_page'])),
		'L_STARTPAGE'			=> $user->lang['default_page'],
		'H_STARTPAGE'			=> $user->lang['start_page_note'],

		'M_PERMANENT_PORTAL'	=> $jquery->MultiSelect('pk_permanent_portal', $portal_positions, $selected_portal_pos, 80, 300),
		'L_PERMANENT_PORTAL'	=> $user->lang['pk_permanent_portal'],
		'H_PERMANENT_PORTAL'	=> $user->lang['pk_permanent_portal'],
		
		'HEAD_NEWS'				=> $user->lang['pk_set_news_settings'],
		
		'C_LIGHTBOX'			=> $html->widget(array('fieldtype'=>'checkbox','name'=>'pk_lightbox_enabled','selected'=>$core->config['pk_lightbox_enabled'])),
		'L_LIGHTBOX'			=> $user->lang['pk_lightbox_enabled'],
		'H_LIGHTBOX'			=> $user->lang['pk_lightbox_enabled_help'],

		'T_LB_MAXRESIZEWIDTH'	=> $html->widget(array('fieldtype'=>'text','name'=>'pk_air_max_resize_width','value'=>$core->config['pk_air_max_resize_width'],'size'=>'4')),
		'L_LB_MAXRESIZEWIDTH'	=> $user->lang['pk_air_max_post_img_resize_width'],
		'H_LB_MAXRESIZEWIDTH'	=> $user->lang['pk_air_max_post_img_resize_width_help'],

		'C_SHOW_NEWSARCHIVE'	=> $html->widget(array('fieldtype'=>'checkbox','name'=>'pk_show_newsarchive','selected'=>$core->config['pk_show_newsarchive'])),
		'L_SHOW_NEWSARCHIVE'	=> $user->lang['pk_enable_newsarchive'],
		'H_SHOW_NEWSARCHIVE'	=> $user->lang['pk_enable_newsarchive_help'],

		'D_NARCHIVE_POSITION'	=> $html->widget(array('fieldtype'=>'dropdown','name'=>'pk_newsarchive_position','options'=>$a_newsarchive_position,'selected'=>$core->config['pk_newsarchive_position'])),
		'L_NARCHIVE_POSITION'	=> $user->lang['pk_newsarchive_position'],
		'H_NARCHIVE_POSITION'	=> $user->lang['pk_newsarchive_position_help'],

		'C_NEWSCATEGORIES'		=> $html->widget(array('fieldtype'=>'checkbox','name'=>'enable_newscategories','selected'=>$core->config['enable_newscategories'])),
		'L_NEWSCATEGORIES'		=> $user->lang['enable_newscategories'],
		'H_NEWSCATEGORIES'		=> $user->lang['enable_newscategories_help'],
		
		// Tab 8 - LAYOUT
		'HEAD_LAYOUT'			=> $user->lang['pk_set_layout'],
		
		'C_DIAGRAMMS'			=> $html->widget(array('fieldtype'=>'checkbox','name'=>'pk_itemhistory_dia','selected'=>$core->config['pk_itemhistory_dia'])),
		'L_DIAGRAMMS'			=> $user->lang['pk_set_itemhistory_dia'],
		'H_DIAGRAMMS'			=> $user->lang['pk_help_itemhistory_dia'],

		'C_3DITEM'				=> $html->widget(array('fieldtype'=>'checkbox','name'=>'pk_enable_3ditem','selected'=>$core->config['pk_enable_3ditem'])),
		'L_3DITEM'				=> $user->lang['pk_set_dis_3ditem'],
		'H_3DITEM'				=> $user->lang['pk_help_dis_3item'],
		
		'C_SHOP'				=> $html->widget(array('fieldtype'=>'checkbox','name'=>'pk_hide_shop','selected'=>$core->config['pk_hide_shop'])),
		'L_SHOP'				=> $user->lang['pk_hide_shop'],
		'H_SHOP'				=> $user->lang['pk_hide_shop_note'],
				
		'HEAD_DEFAULT_SETTINGS'	=> $user->lang['default_settings'],
		
		'T_DEFAULT_ALIMIT'		=> $html->widget(array('fieldtype'=>'text','name'=>'default_alimit','value'=>$core->config['default_alimit'],'size'=>'5')),
		'L_DEFAULT_ALIMIT'		=> $user->lang['adjustments_per_page'],
		'H_DEFAULT_ALIMIT'		=> $user->lang['default_alimit_note'],

		'T_DEFAULT_ELIMIT'		=> $html->widget(array('fieldtype'=>'text','name'=>'default_elimit','value'=>$core->config['default_elimit'],'size'=>'5')),
		'L_DEFAULT_ELIMIT'		=> $user->lang['events_per_page'],
		'H_DEFAULT_ELIMIT'		=> $user->lang['default_elimit_note'],

		'T_DEFAULT_ILIMIT'		=> $html->widget(array('fieldtype'=>'text','name'=>'default_ilimit','value'=>$core->config['default_ilimit'],'size'=>'5')),
		'L_DEFAULT_ILIMIT'		=> $user->lang['items_per_page'],
		'H_DEFAULT_ILIMIT'		=> $user->lang['default_ilimit_note'],

		'T_DEFAULT_NLIMIT'		=> $html->widget(array('fieldtype'=>'text','name'=>'default_nlimit','value'=>$core->config['default_nlimit'],'size'=>'5')),
		'L_DEFAULT_NLIMIT'		=> $user->lang['news_per_page'],
		'H_DEFAULT_NLIMIT'		=> $user->lang['default_nlimit_note'],

		'T_DEFAULT_RLIMIT'		=> $html->widget(array('fieldtype'=>'text','name'=>'default_rlimit','value'=>$core->config['default_rlimit'],'size'=>'5')),
		'L_DEFAULT_RLIMIT'		=> $user->lang['raids_per_page'],
		'H_DEFAULT_RLIMIT'		=> $user->lang['default_rlimit_note'],
		
		'D_NEWSLOOT_LIMIT'		=> $html->widget(array('fieldtype'=>'dropdown','name'=>'pk_newsloot_limit','options'=>$newsloot_limit,'selected'=>$core->config['pk_newsloot_limit'])),
		'L_NEWSLOOT_LIMIT'		=> $user->lang['pk_set_newsloot_limit'],
		'H_NEWSLOOT_LIMIT'		=> $user->lang['pk_help_newsloot_limit'],
		
		'C_NORAIDS'				=> $html->widget(array('fieldtype'=>'checkbox','name'=>'pk_noRaids','selected'=>$core->config['pk_noRaids'])),
		'L_NORAIDS'				=> $user->lang['pk_set_noRaids'],
		'H_NORAIDS'				=> $user->lang['pk_help_noRaids'],

		'C_EVENTS'				=> $html->widget(array('fieldtype'=>'checkbox','name'=>'pk_noEvents','selected'=>$core->config['pk_noEvents'])),
		'L_EVENTS'				=> $user->lang['pk_set_noEvents'],
		'H_EVENTS'				=> $user->lang['pk_help_noEvents'],

		'C_ITEMPRICES'			=> $html->widget(array('fieldtype'=>'checkbox','name'=>'pk_noItemPrices','selected'=>$core->config['pk_noItemPrices'])),
		'L_ITEMPRICES'			=> $user->lang['pk_set_noItemPrices'],
		'H_ITEMPRICES'			=> $user->lang['pk_help_noItemPrices'],
		
		// Roster things
		'HEAD_ROSTER'		=> $user->lang['menu_roster'],
		
		'C_NODKP'				=> $html->widget(array('fieldtype'=>'checkbox','name'=>'pk_noDKP','selected'=>$core->config['pk_noDKP'])),
		'L_NODKP'				=> $user->lang['pk_set_noDKP'],
		'H_NODKP'				=> $user->lang['pk_help_noDKP'],

		'C_ROSTER'				=> $html->widget(array('fieldtype'=>'checkbox','name'=>'pk_noRoster','selected'=>$core->config['pk_noRoster'])),
		'L_ROSTER'				=> $user->lang['pk_set_noRoster'],
		'H_ROSTER'				=> $user->lang['pk_help_noRoster'],
		
		// Tab 9 - ITEMSTATS
		'HEAD_ITEMTOOLTIP'		=> $user->lang['pk_set_itemtooltip_name'],
		'L_ITEMTOOLTIP_NA'		=> $user->lang['pk_itt_not_avail'],
		'ITEMTOOLTIP_AVAIL'		=> (in_array($game->get_game(), $itt->get_supported_games())) ? true : false,
		'ITT_RESET_BUTTON'		=> $user->lang['pk_itt_reset'],
		
		'C_USE_ITEMTOOLTIP'		=> $html->widget(array('fieldtype'=>'checkbox','name'=>'infotooltip_use','selected'=>$core->config['infotooltip_use'])),
		'L_USE_ITEMTOOLTIP'		=> $user->lang['pk_set_itemtooltip'],
		'H_USE_ITEMTOOLTIP'		=> $user->lang['pk_help_itemtooltip'],

		'C_ITEMTOOLTIP_DEBUG'	=> $html->widget(array('fieldtype'=>'checkbox','name'=>'itt_debug','selected'=>$core->config['itt_debug'])),
		'L_ITEMTOOLTIP_DEBUG'	=> $user->lang['pk_set_itemtooltip_debug'],
		'H_ITEMTOOLTIP_DEBUG'	=> $user->lang['pk_help_itemtooltip_debug'],
		
		'HEAD_ITEMPRIORITY'		=> $user->lang['pk_is_set_prio'],

		'D_ITT_PRIO1'			=> $html->widget(array('fieldtype'=>'dropdown','name'=>'itt_prio1','options'=>$itt_parserlist,'selected'=>$core->config['itt_prio1'])),
		'L_ITT_PRIO1'			=> sprintf($user->lang['pk_itt_prio'], 1),
		'H_ITT_PRIO1'			=> $user->lang['pk_is_help_prio'],

		'D_ITT_PRIO2'			=> $html->widget(array('fieldtype'=>'dropdown','name'=>'itt_prio2','options'=>$itt_parserlist,'selected'=>$core->config['itt_prio2'])),
		'L_ITT_PRIO2'			=> sprintf($user->lang['pk_itt_prio'], 2),
		'H_ITT_PRIO2'			=> $user->lang['pk_is_help_prio'],

		'HEAD_ITEMLANGPRIORITY'	=> $user->lang['pk_itt_set_langprio'],
		'HEAD_ITEMDBSPECIFY'	=> $user->lang['pk_itt_database_specific'],
		
		// Tab 10 - SMS
		'HEAD_SMS'				=> $user->lang['pk_set_sms_header'],
		
		'C_SMS_DISABLE'			=> $html->widget(array('fieldtype'=>'checkbox','name'=>'pk_sms_disable','selected'=>$core->config['pk_sms_disable'])),
		'L_SMS_DISABLE'			=> $user->lang['pk_set_sms_deactivate'],
		'H_SMS_DISABLE'			=> $user->lang['pk_set_sms_deactivate_help'],

		'T_SMS_USERNAME'		=> $html->widget(array('fieldtype'=>'text','name'=>'pk_sms_username','value'=>$core->config['pk_sms_username'],'size'=>'15')),
		'L_SMS_USERNAME'		=> $user->lang['pk_set_sms_username'],
		'H_SMS_USERNAME'		=> $user->lang['pk_set_sms_username_help'],

		'T_SMS_PASSWORD'		=> $html->widget(array('fieldtype'=>'text','name'=>'pk_sms_password','value'=>$core->config['pk_sms_password'],'size'=>'15')),
		'L_SMS_PASSWORD'		=> $user->lang['pk_set_sms_pass'],
		'H_SMS_PASSWORD'		=> $user->lang['pk_set_sms_pass_help'],

		'FOOTER_SMS'			=> ($_HMODE) ? $user->lang['pk_set_sms_info_temp'].$_HMODE_LINK : $user->lang['pk_set_sms_info_temp'].$user->lang['sms_info_account_link']
	));

	$core->set_vars(array(
		'page_title'		=> $user->lang['config_title'],
		'template_file'		=> 'admin/settings.html',
		'display'			=> true)
	);
?>