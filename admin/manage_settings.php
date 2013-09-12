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
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 *
 * $Id$
 */

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = '../';
include_once($eqdkp_root_path . 'common.php');

class mmocms_settings extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'pdh', 'jquery', 'game', 'core', 'config', 'html', 'db', 'pfh', 'pdc', 'pdl', 'env', 'itt' => 'infotooltip', 'social' => 'socialplugins');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public function __construct(){
		$this->user->check_auth('a_config_man');

		$handler = array(
			'ajax'	=> array(
				array('process' => 'ajax_gamelanguage',	'value' => 'games'),
			),
		);
		parent::__construct(false, $handler, array(), null, '');
		$this->process();
	}

	public function ajax_gamelanguage() {
		echo($this->jquery->dd_create_ajax(sdir($this->root_path . 'games/'.$this->in->get('requestid').'/language/', '*.php', '.php'), array('format'=>'ucfirst','noid'=>true,'selected'=>$this->config->get('game_language'))));
		exit;
	}

	public function display(){
		// Build the default game array
		$games = array();
		foreach($this->game->get_games() as $sgame){
			$games[$sgame]		= $this->game->game_name($sgame);
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

		$a_startday = array(
			'sunday'	=> $this->user->lang(array('time_daynames', 6)),
			'monday'	=> $this->user->lang(array('time_daynames', 0)),
		);

		$a_calraid_status = array(
			0	=> $this->user->lang(array('raidevent_raid_status', 0)),
			1	=> $this->user->lang(array('raidevent_raid_status', 1)),
			2	=> $this->user->lang(array('raidevent_raid_status', 2)),
			3	=> $this->user->lang(array('raidevent_raid_status', 3)),
			4	=> $this->user->lang(array('raidevent_raid_status', 4))
		);

		$a_calraid_nsfilter = array(
			'twinks'	=> 'raidevent_raid_nsf_twink',
			'inactive'	=> 'raidevent_raid_nsf_inctv',
			'hidden'	=> 'raidevent_raid_nsf_hiddn',
			'special'	=> 'raidevent_raid_nsf_special',
		);

		$a_debug_mode = array(
			'0'			=> 'pk_set_debug_type0',
			'1'			=> 'pk_set_debug_type1',
			'2'			=> 'pk_set_debug_type2',
			'3'			=> 'pk_set_debug_type3',
			//'4'			=> 'pk_set_debug_type4',
		);

		$a_modelviewer = array(
			'0'			=> 'WoWHead',
			'1'			=> 'Thottbot',
			'2'			=> 'SpeedyDragon'
		);

		$accact_array = array(
			'0'			=> 'none',
			'1'			=> 'user',
			'2'			=> 'admin',
		);

		$portal_positions = array(
			'right'		=> 'portalplugin_right',
			'middle'	=> 'portalplugin_middle',
			'bottom'	=> 'portalplugin_bottom',
		);

		$mail_array = array(
			'mail'		=> 'lib_email_mail',
			'sendmail'	=> 'lib_email_sendmail',
			'smtp'		=> 'lib_email_smtp',
		);

		$stmp_connection_methods = array(
			''	=> 'none',
			'ssl'	=> 'SSL/TLS',
			'tls'	=> 'STARTTLS'
		);

		$a_calendar_addevmode = array(
			'event'		=> 'calendar_mode_event',
			'raid'		=> 'calendar_mode_raid'
		);

		$a_groups = $this->pdh->aget('user_groups', 'name', 0, array($this->pdh->get('user_groups', 'id_list')));

		// Startpage
		$menus		= $this->core->gen_menus();
		$pages		= array_merge($menus['menu1'], $menus['menu2']);

		unset($menus);
		if(is_array($this->pdh->get('pages', 'startpage_list', array()))){
			// Add Pages to startpage array
			$pages = array_merge_recursive( $pages, $this->pdh->get('pages', 'startpage_list', array()));
		}
		foreach($pages as $page){
			$link = preg_replace('#\?s\=([0-9A-Za-z]{1,40})?#', '', $page['link']);
			$link = preg_replace('#\.php&amp;#', '.php?', $link);
			$link = preg_replace('#\.php&#', '.php?', $link);
			$text = ( isset($this->user->data['username']) ) ? str_replace($this->user->data['username'], $this->user->lang('username'), $page['text']) : $page['text'];

			if($link != 'login.php?logout=true'){
				$startpage_array[$link] = $text;
			}
			unset($link, $text);
		}

		// Build language array
		if($dir = @opendir($this->core->root_path . 'language/')){
			while ( $file = @readdir($dir) ){
				if ((!is_file($this->core->root_path . 'language/' . $file)) && (!is_link($this->core->root_path . 'language/' . $file)) && valid_folder($file)){
					include($this->core->root_path.'language/'.$file.'/lang_main.php');
					$lang_name_tp = (($lang['ISO_LANG_NAME']) ? $lang['ISO_LANG_NAME'].' ('.$lang['ISO_LANG_SHORT'].')' : ucfirst($file));
					$language_array[$file]					= $lang_name_tp;
					$locale_array[$lang['ISO_LANG_SHORT']]	= $lang_name_tp;
				}
			}
		}

		//Social Plugins
		$arrSocialPlugins = $this->social->getSocialPlugins();
		$arrSocialFields = array();
		foreach ($arrSocialPlugins as $key => $value){
			$arrSocialFields['sp_'.$key] = array(
				'fieldtype' => 'checkbox',
				'name' 		=> 'sp_'.$key,
			);
		}
		$arrSocialButtons = $this->social->getSocialButtons();
		foreach ($arrSocialButtons as $key => $value){
			$arrSocialFields['sp_'.$key] = array(
				'fieldtype' => 'checkbox',
				'name' 		=> 'sp_'.$key,
			);
		}

		// ---------------------------------------------------------
		// Member Array
		// ---------------------------------------------------------
		$members = $this->pdh->aget('member', 'name', 0, array($this->pdh->get('member', 'id_list', array(false, false, false))));
		asort($members);

		// ---------------------------------------------------------
		// Portal position
		// ---------------------------------------------------------
		$selected_portal_pos = unserialize(stripslashes($this->config->get('pk_permanent_portal')));

		// ---------------------------------------------------------
		// Default email signature
		// ---------------------------------------------------------
		$signature  = "--\n";
		$signature .= $this->user->lang('lib_signature_defaultval');
		$signature .= ' '.$this->config->get('guildtag');
		$signature .= "\nEQdkp Plus: ";
		$signature .= $this->env->link;

		// Bit of jQuery..
		if($this->game->get_importAuth('a_members_man', 'char_mupdate')){
			$this->jquery->Dialog('MassUpdateChars', $this->user->lang('uc_import_adm_update'), array('url'=>$this->game->get_importers('char_mupdate', true), 'width'=>'600', 'height'=>'450', 'onclose'=>$this->env->link.'admin/manage_settings.php'));
		}
		if($this->game->get_importAuth('a_members_man', 'guild_import')){
			$this->jquery->Dialog('GuildImport', $this->user->lang('uc_import_guild_wh'), array('url'=>$this->game->get_importers('guild_import', true), 'width'=>'600', 'height'=>'450', 'onclose'=>$this->env->link.'admin/manage_settings.php'));
		}
		if(($this->game->get_importAuth('a_members_man', 'char_mupdate') || $this->game->get_importAuth('a_members_man', 'guild_import')) && $this->game->get_importers('import_data_cache')){
			$this->jquery->Dialog('ClearImportCache', $this->user->lang('uc_importer_cache'), array('url'=>$this->game->get_importers('import_reseturl', true), 'width'=>'400', 'height'=>'250', 'onclose'=>$this->env->link.'admin/manage_settings.php'));
		}
		$this->jquery->spinner('#inactive_period, #pk_round_precision, #inactive_period, #default_nlimit, #calendar_raid_classbreak, #calendar_addraid_deadline, #failed_logins_inactivity', array('multiselector'=>true));
		$this->jquery->spinner('#default_alimit, #default_climit, #default_ilimit, #default_rlimit, #default_elimit, #calendar_addraid_duration, #calendar_repeat_crondays', array('step'=>10, 'multiselector'=>true));
		$this->jquery->spinner('pk_newsloot_limit', array('step'=>10, 'max'=>25, 'steps'=>5, 'min'=>0));

		// ---------------------------------------------------------
		// Output to the page
		// ---------------------------------------------------------
		$this->jquery->Tab_header('plus_sett_tabs', true);
		$this->jquery->Dialog('template_preview', $this->user->lang('template_preview'), array('url'=>$this->root_path."viewnews.php".$this->SID."&amp;style='+ $(\"select[name='user_style'] option:selected\").val()+'", 'width'=>'750', 'height'=>'520', 'modal'=>true));
		$game_array = $this->jquery->dd_ajax_request('default_game', 'game_language', $games, array('--------'), $this->config->get('default_game'), 'manage_settings.php'.$this->SID.'&ajax=games');
		$settingsdata = array(
			'global' => array(
				'global' => array(
					'pk_updatecheck'=> array(
						'fieldtype'		=> 'checkbox',
						'name'			=> 'pk_updatecheck',
						'not4hmode'		=> true,
					),
					'guildtag'		=> array(
						'fieldtype'		=> 'text',
						'name'			=> 'guildtag',
						'size'			=> 35
					),
					'main_title'	=> array(
						'fieldtype'		=> 'text',
						'name'			=> 'main_title',
						'size'			=> 40
					),
					'sub_title'		=> array(
						'fieldtype'		=> 'text',
						'name'			=> 'sub_title',
						'size'			=> 40
					),
					'dkp_name'		=> array(
						'fieldtype'		=> 'text',
						'name'			=> 'dkp_name',
						'size'			=> 5
					),
					'pk_color_items'=> array(
						'fieldtype'		=> 'slider',
						'name'			=> 'pk_color_items',
						'label'			=> $this->user->lang('pk_color_items'),
						'min'			=> 0,
						'max'			=> 100,
						'width'			=> '300px',
						'format'		=> 'range',
						'serialized'	=> true,
						'datatype'		=> 'int'
					),
					'pk_enable_comments'	=> array(
						'fieldtype'		=> 'checkbox',
						'name'			=> 'pk_enable_comments',
					),
					'pk_round_activate'	=> array(
						'fieldtype'		=> 'checkbox',
						'name'			=> 'pk_round_activate',
						'default'		=> 0
					),
					'pk_round_precision'	=> array(
						'fieldtype'		=> 'text',
						'name'			=> 'pk_round_precision',
						'size'			=> 2,
						'id'			=> 'pk_round_precision',
						'class'			=> '',
						'default'		=> 0
					),
					'pk_debug'	=> array(
						'fieldtype'		=> 'dropdown',
						'name'			=> 'pk_debug',
						'options'		=> $a_debug_mode,
					)
				),
				'meta'	=> array(
					'pk_meta_keywords' => array(
						'fieldtype'		=> 'text',
						'name'			=> 'pk_meta_keywords',
						'size'			=> 40
					),
					'pk_meta_description' => array(
						'fieldtype'		=> 'text',
						'name'			=> 'pk_meta_description',
						'size'			=> 40
					),
				),
				'disclaimer'	=> array(
					'pk_disclaimer_show'	=> array(
						'fieldtype'		=> 'checkbox',
						'name'			=> 'pk_disclaimer_show',
					),
					'pk_disclaimer_name'		=> array(
						'fieldtype'		=> 'text',
						'name'			=> 'pk_disclaimer_name',
						'size'			=> 40,
						'dependency'	=> 'pk_disclaimer_show',
					),
					'pk_disclaimer_address'	=> array(
						'fieldtype'		=> 'textarea',
						'name'			=> 'pk_disclaimer_address',
						'cols'			=> 50,
						'rows'			=> 4,
						'dependency'	=> 'pk_disclaimer_show',
					),
					'pk_disclaimer_email'		=> array(
						'fieldtype'		=> 'text',
						'name'			=> 'pk_disclaimer_email',
						'size'			=> 40,
						'dependency'	=> 'pk_disclaimer_show',
					),
					'pk_disclaimer_irc'		=> array(
						'fieldtype'		=> 'text',
						'name'			=> 'pk_disclaimer_irc',
						'size'			=> 40,
						'dependency'	=> 'pk_disclaimer_show',
					),
					'pk_disclaimer_messenger'		=> array(
						'fieldtype'		=> 'text',
						'name'			=> 'pk_disclaimer_messenger',
						'size'			=> 40,
						'dependency'	=> 'pk_disclaimer_show',
					),
					'pk_disclaimer_custom'		=> array(
						'fieldtype'		=> 'text',
						'name'			=> 'pk_disclaimer_custom',
						'size'			=> 50,
						'dependency'	=> 'pk_disclaimer_show',
					)
				)
			),
			'system'	=> array(
				'globalsettings'	=> array(
					'default_locale'	=> array(
						'fieldtype'		=> 'dropdown',
						'name'			=> 'default_locale',
						'options'		=> $locale_array,
						'no_lang'		=> true,
					),
					'server_path'	=> array(
						'fieldtype'		=> 'text',
						'name'			=> 'server_path',
						'size'			=> 50,
						'not4hmode'		=> true
					),
					'enable_gzip'	=> array(
						'fieldtype'		=> 'checkbox',
						'name'			=> 'enable_gzip',
						'not4hmode'		=> true,
						'default'		=> 0
					),
					'upload_allowed_extensions'	=> array(
						'fieldtype'		=> 'text',
						'name'			=> 'upload_allowed_extensions',
						'size'			=> 50
					)
				),
				'auth'				=> array(
					'auth_method'	=> array(
						'fieldtype'		=> 'dropdown',
						'name'			=> 'auth_method',
						'options'		=> $this->user->get_available_authmethods(),
						'default'		=> 'db',
					),


				),
				'login'				=> array(
					'login_method'	=> array(
						'fieldtype'		=> 'jq_multiselect',
						'name'			=> 'login_method',
						'options'		=> $this->user->get_available_loginmethods(),
						'serialized'	=> true,
						'default'		=> '',
					),
				),
				'cookie'			=> array(
					'cookie_domain'	=> array(
						'fieldtype'		=> 'text',
						'name'			=> 'cookie_domain',
						'size'			=> 25,
						'not4hmode'		=> true
					),
					'cookie_name'	=> array(
						'fieldtype'		=> 'text',
						'name'			=> 'cookie_name',
						'size'			=> 25,
						'not4hmode'		=> true
					),
					'cookie_path'	=> array(
						'fieldtype'		=> 'text',
						'name'			=> 'cookie_path',
						'size'			=> 25,
						'not4hmode'		=> true
					),
				),
				'email'				=> array(
					'lib_email_method'	=> array(
						'fieldtype'		=> 'dropdown',
						'name'			=> 'lib_email_method',
						'options'		=> $mail_array,
					),
					'admin_email'	=> array(
						'fieldtype'		=> 'text',
						'name'			=> 'admin_email',
						'size'			=> 30,
						'encrypt'		=> true,
					),
					'lib_email_sender_name'	=> array(
						'fieldtype'		=> 'text',
						'name'			=> 'lib_email_sender_name',
						'size'			=> 30
					),
					'lib_email_sendmail_path'	=> array(
						'fieldtype'		=> 'text',
						'name'			=> 'lib_email_sendmail_path',
						'size'			=> 30,
						'dependency'	=> array('lib_email_method', 'sendmail'),
					),
					'lib_email_smtp_host'	=> array(
						'fieldtype'		=> 'text',
						'name'			=> 'lib_email_smtp_host',
						'size'			=> 30,
						'dependency'	=> array('lib_email_method', 'smtp'),
					),
					'lib_email_smtp_port'	=> array(
						'fieldtype'		=> 'text',
						'name'			=> 'lib_email_smtp_port',
						'size'			=> 5,
						'default'		=> 25,
						'dependency'	=> array('lib_email_method', 'smtp'),
					),
					'lib_email_smtp_connmethod'	=> array(
						'fieldtype'		=> 'dropdown',
						'name'			=> 'lib_email_smtp_connmethod',
						'options'		=> $stmp_connection_methods,
						'dependency'	=> array('lib_email_method', 'smtp'),
					),
					'lib_email_smtp_auth'	=> array(
						'fieldtype'		=> 'checkbox',
						'name'			=> 'lib_email_smtp_auth',
						'dependency'	=> array('lib_email_method', 'smtp'),
					),
					'lib_email_smtp_user'	=> array(
						'fieldtype'		=> 'text',
						'name'			=> 'lib_email_smtp_user',
						'size'			=> 30,
						'dependency'	=> array('lib_email_method', 'smtp'),
					),
					'lib_email_smtp_pw'	=> array(
						'fieldtype'		=> 'password',
						'name'			=> 'lib_email_smtp_pw',
						'size'			=> 30,
						'dependency'	=> array('lib_email_method', 'smtp'),
					),
					'lib_email_signature'	=> array(
						'fieldtype'		=> 'checkbox',
						'name'			=> 'lib_email_signature',
						'default'		=> 0
					),
					'lib_email_signature_value'	=> array(
						'fieldtype'		=> 'textarea',
						'name'			=> 'lib_email_signature_value',
						'default'		=> $signature,
						'cols'			=> 80,
						'rows'			=> 5,
						'dependency'	=> 'lib_email_signature',
					),
				),
				'recaptcha'			=> array(
					'lib_recaptcha_okey'	=> array(
						'fieldtype'		=> 'text',
						'name'			=> 'lib_recaptcha_okey',
						'size'			=> 30
					),
					'lib_recaptcha_pkey'	=> array(
						'fieldtype'		=> 'text',
						'name'			=> 'lib_recaptcha_pkey',
						'size'			=> 30
					)
				),
				'date'				=> array(
					'timezone'	=> array(
						'fieldtype'		=> 'dropdown',
						'name'			=> 'timezone',
						'options'		=> $this->time->timezones,
					),
					'pk_date_startday'	=> array(
						'fieldtype'		=> 'dropdown',
						'name'			=> 'pk_date_startday',
						'options'		=> $a_startday,
						'no_lang'		=> true,
					),
					'default_date_time'	=> array(
						'fieldtype'		=> 'text',
						'name'			=> 'default_date_time',
						'size'			=> 10,
						'default'		=> $this->user->lang('style_time')
					),
					'default_date_short'	=> array(
						'fieldtype'		=> 'text',
						'name'			=> 'default_date_short',
						'size'			=> 20,
						'default'		=> $this->user->lang('style_date_short')
					),
					'default_date_long'	=> array(
						'fieldtype'		=> 'text',
						'name'			=> 'default_date_long',
						'size'			=> 20,
						'default'		=> $this->user->lang('style_date_long')
					)
				)
			),
			'user'		=> array(
				'user'	=> array(
					'default_lang'	=> array(
						'fieldtype'		=> 'dropdown',
						'name'			=> 'default_lang',
						'options'		=> $language_array,
						'no_lang'		=> true,
					),
					'account_activation'	=> array(
						'fieldtype'		=> 'radio',
						'name'			=> 'account_activation',
						'options'		=> $accact_array,
						'default'		=> 0
					),
					'failed_logins_inactivity'	=> array(
						'fieldtype'		=> 'text',
						'name'			=> 'failed_logins_inactivity',
						'size'			=> 5,
						'id'			=> 'failed_logins_inactivity',
						'class'			=> '',
						'default'		=> 5,
					),
					'disable_registration'	=> array(
						'fieldtype'		=> 'checkbox',
						'name'			=> 'disable_registration',
					),
					'pk_enable_captcha'	=> array(
						'fieldtype'		=> 'checkbox',
						'name'			=> 'pk_enable_captcha',
					),
					'pk_disable_username_change'	=> array(
						'fieldtype'		=> 'checkbox',
						'name'			=> 'pk_disable_username_change',
					),
					'default_style_overwrite'	=> array(
						'fieldtype'		=> 'checkbox',
						'name'			=> 'default_style_overwrite',
					),
					'special_user'	=> array(
						'fieldtype'		=> 'jq_multiselect',
						'name'			=> 'special_user',
						'options'		=> $this->pdh->aget('user', 'name', 0, array($this->pdh->get('user', 'id_list'))),
						'serialized'	=> true,
						'datatype'		=> 'int',
						'no_lang'		=> true
					),
				)
			),
			'chars'		=> array(
				'chars'		=> array(
					'pk_class_color'	=> array(
						'fieldtype'		=> 'checkbox',
						'name'			=> 'pk_class_color',
					),
					'special_members'	=> array(
						'fieldtype'		=> 'jq_multiselect',
						'name'			=> 'special_members',
						'options'		=> $members,
						'serialized'	=> true,
						'datatype'		=> 'int',
						'no_lang'		=> true
					),
					'pk_show_twinks'	=> array(
						'fieldtype'		=> 'checkbox',
						'name'			=> 'pk_show_twinks',
						'default'		=> 0
					),
					'pk_detail_twink'	=> array(
						'fieldtype'		=> 'checkbox',
						'name'			=> 'pk_detail_twink',
						'default'		=> 0
					),
					'hide_inactive'	=> array(
						'fieldtype'		=> 'checkbox',
						'name'			=> 'hide_inactive',
						'default'		=> 0
					),
					'inactive_period'	=> array(
						'fieldtype'		=> 'text',
						'name'			=> 'inactive_period',
						'size'			=> 5,
						'id'			=> 'inactive_period',
						'class'			=> '',
						'default'		=> 0
					)
				)
			),
			'calendar'	=> array(
				'calendar'	=> array(
					'calendar_addevent_mode'	=> array(
						'fieldtype'		=> 'dropdown',
						'name'			=> 'calendar_addevent_mode',
						'options'		=> $a_calendar_addevmode,
					)
				),
				'raids'		=> array(
					'calendar_raid_guests'	=> array(
						'fieldtype'		=> 'checkbox',
						'name'			=> 'calendar_raid_guests',
					),
					'calendar_raid_random'	=> array(
						'fieldtype'		=> 'checkbox',
						'name'			=> 'calendar_raid_random',
					),
					'calendar_raid_classbreak'	=> array(
						'fieldtype'		=> 'text',
						'name'			=> 'calendar_raid_classbreak',
						'size'			=> 4,
						'id'			=> 'calendar_raid_classbreak',
						'class'			=> ''
					),
					'calendar_raid_status'	=> array(
						'fieldtype'		=> 'jq_multiselect',
						'name'			=> 'calendar_raid_status',
						'options'		=> $a_calraid_status,
						'serialized'	=> true,
						'datatype'		=> 'int',
						'no_lang'		=> true
					),
					'calendar_raid_nsfilter'	=> array(
						'fieldtype'		=> 'jq_multiselect',
						'name'			=> 'calendar_raid_nsfilter',
						'options'		=> $a_calraid_nsfilter,
						'serialized'	=> true,
						'datatype'		=> 'string'
					),
					'calendar_addraid_deadline'	=> array(
						'fieldtype'		=> 'text',
						'name'			=> 'calendar_addraid_deadline',
						'size'			=> 5,
						'id'			=> 'calendar_addraid_deadline',
						'class'			=> '',
						'default'		=> '1'
					),
					'calendar_addraid_duration'	=> array(
						'fieldtype'		=> 'text',
						'name'			=> 'calendar_addraid_duration',
						'size'			=> 5,
						'id'			=> 'calendar_addraid_duration',
						'class'			=> '',
						'default'		=> '120'
					),
					'calendar_addraid_use_def_start'	=> array(
						'fieldtype'		=> 'checkbox',
						'name'			=> 'calendar_addraid_use_def_start',
					),
					'calendar_addraid_def_starttime'	=> array(
						'fieldtype'		=> 'timepicker',
						'name'			=> 'calendar_addraid_def_starttime',
						'dependency'	=> 'calendar_addraid_use_def_start',
						'default'		=> '20:00'
					),
					'calendar_repeat_crondays'	=> array(
						'fieldtype'		=> 'text',
						'name'			=> 'calendar_repeat_crondays',
						'size'			=> 5,
						'id'			=> 'calendar_repeat_crondays',
						'class'			=> '',
						'default'		=> '40'
					),
					'calendar_raid_autoconfirm'	=> array(
						'fieldtype'		=> 'jq_multiselect',
						'name'			=> 'calendar_raid_autoconfirm',
						'options'		=> $a_groups,
						'serialized'	=> true,
						'datatype'		=> 'int',
						'no_lang'		=> true
					),
					'calendar_raid_autocaddchars'	=> array(
						'fieldtype'		=> 'jq_multiselect',
						'name'			=> 'calendar_raid_autocaddchars',
						'options'		=> $a_groups,
						'serialized'	=> true,
						'datatype'		=> 'int',
						'no_lang'		=> true
					),
					'calendar_raid_shownotes'	=> array(
						'fieldtype'		=> 'jq_multiselect',
						'name'			=> 'calendar_raid_shownotes',
						'options'		=> $a_groups,
						'serialized'	=> true,
						'datatype'		=> 'int',
						'no_lang'		=> true
					),
					'calendar_raid_notsigned_classsort'	=> array(
						'fieldtype'		=> 'checkbox',
						'name'			=> 'calendar_raid_notsigned_classsort',
					),
					'calendar_raid_coloredclassnames'	=> array(
						'fieldtype'		=> 'checkbox',
						'name'			=> 'calendar_raid_coloredclassnames',
					),
					'calendar_raid_shownotsigned'	=> array(
						'fieldtype'		=> 'checkbox',
						'name'			=> 'calendar_raid_shownotsigned',
					),
					'calendar_raid_allowstatuschange'	=> array(
						'fieldtype'		=> 'checkbox',
						'name'			=> 'calendar_raid_allowstatuschange',
					),
				),
				'calendar_mails'	=> array(
					'calendar_email_statuschange'	=> array(
						'fieldtype'		=> 'checkbox',
						'name'			=> 'calendar_email_statuschange',
					),
					'calendar_email_newraid'	=> array(
						'fieldtype'		=> 'checkbox',
						'name'			=> 'calendar_email_newraid',
					),
					'calendar_email_openclose'	=> array(
						'fieldtype'		=> 'checkbox',
						'name'			=> 'calendar_email_openclose',
					),
				)
			),
			'game'		=> array(
				'game'	=> array(
					'pk_defaultgame'	=> array(
						'fieldtype'		=> 'direct',
						'name'			=> 'pk_defaultgame',
						'direct'		=> $game_array[0],
					),
					'pk_defaultgamelang'	=> array(
						'fieldtype'		=> 'direct',
						'name'			=> 'pk_defaultgamelang',
						'direct'		=> $game_array[1],
					)
				)
			),
			'portal'	=> array(
				'portal'	=> array(
					'start_page'	=> array(
						'fieldtype'		=> 'dropdown',
						'name'			=> 'start_page',
						'options'		=> $startpage_array,
					),
					'pk_permanent_portal'	=> array(
						'fieldtype'		=> 'jq_multiselect',
						'name'			=> 'pk_permanent_portal',
						'options'		=> $portal_positions,
						'selected'		=> $selected_portal_pos,
						'serialized'	=> true,
						'datatype'		=> 'string'
					),
					'pk_portal_website'	=> array(
						'fieldtype'		=> 'text',
						'name'			=> 'pk_portal_website',
						'size'			=> 40
					),
					'eqdkpm_shownote'	=> array(
						'fieldtype'		=> 'checkbox',
						'name'			=> 'eqdkpm_shownote',
					),
				),
				'news'		=> array(
					'enable_newscategories'	=> array(
						'fieldtype'		=> 'checkbox',
						'name'			=> 'enable_newscategories',
					),
					'disable_embedly'	=> array(
						'fieldtype'		=> 'checkbox',
						'name'			=> 'disable_embedly',
					),
					'thumbnail_defaultsize' => array(
						'fieldtype'		=> 'int',
						'name'			=> 'thumbnail_defaultsize',
						'size'			=> 3,
						'default'		=> 500,
					),
				),

				'social_sharing' => $arrSocialFields,
			),
			'layout'	=> array(
				'layout'	=> array(
					'custom_logo'	=> array(
						'fieldtype'	=> 'imageuploader',
						'name'		=> 'custom_logo',
						'imgpath'	=> $this->pfh->FolderPath('logo','eqdkp'),
						'options'	=> array(
							'noimgfile'	=> "templates/".$this->user->style['template_path']."/images/logo.png"
						),
					),
					'pk_itemhistory_dia'	=> array(
						'fieldtype'		=> 'checkbox',
						'name'			=> 'pk_itemhistory_dia',
					),
				),
				'default'	=> array(
					'default_alimit'	=> array(
						'fieldtype'		=> 'text',
						'name'			=> 'default_alimit',
						'size'			=> 5,
						'id'			=> 'default_alimit',
						'class'			=> '',
						'default'		=> 0
					),
					'default_elimit'	=> array(
						'fieldtype'		=> 'text',
						'name'			=> 'default_elimit',
						'size'			=> 5,
						'id'			=> 'default_elimit',
						'class'			=> '',
						'default'		=> 0
					),
					'default_ilimit'	=> array(
						'fieldtype'		=> 'text',
						'name'			=> 'default_ilimit',
						'size'			=> 5,
						'id'			=> 'default_ilimit',
						'class'			=> '',
						'default'		=> 0
					),
					'default_nlimit'	=> array(
						'fieldtype'		=> 'text',
						'name'			=> 'default_nlimit',
						'size'			=> 5,
						'id'			=> 'default_nlimit',
						'class'			=> '',
						'default'		=> 0
					),
					'default_rlimit'	=> array(
						'fieldtype'		=> 'text',
						'name'			=> 'default_rlimit',
						'size'			=> 5,
						'id'			=> 'default_rlimit',
						'class'			=> '',
						'default'		=> 0
					),
					'pk_newsloot_limit'	=> array(
						'fieldtype'		=> 'text',
						'name'			=> 'pk_newsloot_limit',
						'size'			=> 5,
						'id'			=> 'pk_newsloot_limit',
						'class'			=> '',
						'default'		=> '0'
					)
				)
			),
			'itemtooltip'	=> array(),	// placeholder for sorting..
			'sms'		=> array(
				'sms'	=> array(
					'pk_sms_enable'	=> array(
						'fieldtype'		=> 'checkbox',
						'name'			=> 'pk_sms_enable',
					),
					'pk_sms_username'	=> array(
						'fieldtype'		=> 'text',
						'name'			=> 'pk_sms_username',
						'size'			=> 40,
						'dependency'	=> 'pk_sms_enable',
					),
					'pk_sms_password'	=> array(
						'fieldtype'		=> 'password',
						'name'			=> 'pk_sms_password',
						'size'			=> 40,
						'dependency'	=> 'pk_sms_enable',
					)
				)
			),
		);

		// ItemTooltip Inject
		if(in_array($this->game->get_game(), $this->itt->get_supported_games())){
			$settingsdata['itemtooltip']['itemtooltip'] = array(
				'infotooltip_use'	=> array(
					'fieldtype'		=> 'checkbox',
					'name'			=> 'infotooltip_use',
				),
				'itt_debug'	=> array(
					'fieldtype'		=> 'checkbox',
					'name'			=> 'itt_debug',
				),
				'itt_trash'	=> array(
					'name'			=> 'itt_trash',
					'fieldtype'		=> 'direct',
					'direct'		=> '<input type="submit" name="itt_reset" value="'.$this->user->lang('pk_itt_reset').'" class="mainoption bi_reset" />',
				),
			);

			$itt_parserlist	= $this->itt->get_parserlist();
			$settingsdata['itemtooltip']['priorities']	= array(
				'itt_prio1'	=> array(
					'fieldtype'		=> 'dropdown',
					'name'			=> 'itt_prio1',
					'options'		=> $itt_parserlist
				),
				'itt_prio2'	=> array(
					'fieldtype'		=> 'dropdown',
					'name'			=> 'itt_prio2',
					'options'		=> $itt_parserlist
				),
			);

			$ittsettdata = $this->itt->get_extra_settings();
			if(is_array($ittsettdata)){
				foreach($ittsettdata as $confvars){
					$ittsett_value = ($this->config->get($confvars['name'])) ? $this->config->get($confvars['name']) : $confvars['default'];
					$settingsdata['itemtooltip']['ittdbsettings'][$confvars['name']]	= $confvars;
				}
				//add button to reload defaults
				$settingsdata['itemtooltip']['ittdbsettings']['itt_force_default'] = array(
					'name'			=> 'pk_itt_force_default',
					'fieldtype'		=> 'direct',
					'direct'		=> '<input type="submit" name="itt_force_default" value="'.$this->user->lang('pk_itt_force_default').'" class="mainoption bi_reset" />',
				);
			}

			$itt_langlist	= $this->itt->get_supported_languages();
			for($i=1; $i<=3; $i++){
				$settingsdata['itemtooltip']['ittlanguages']['itt_langprio'.$i]	= array(
					'fieldtype'	=> 'dropdown',
					'name'		=> 'itt_langprio'.$i,
					'options'	=> $itt_langlist,
					'no_lang'	=> true
				);
			}

			//check if user wanted to reset itt-cache
			if($this->in->get('itt_reset', false)) {
				$this->itt->reset_cache();
				$this->core->message($this->user->lang('itt_reset_success'), $this->user->lang('success'), 'green');
			}

			//check if user wanted to reload defaults$
			if($this->in->get('itt_force_default', '') != ''){
				$this->config->set($this->itt->changed_prio1($this->in->get('itt_prio1')));
				$this->core->message($this->user->lang('itt_default_success'), $this->user->lang('success'), 'green');
			}
		}

		// The importer settings
		if($this->game->get_importAuth('a_members_man', 'guild_import')){
			if($this->game->get_importers('guild_imp_rsn') && $this->config->get('uc_servername') == ''){
				$gimport_out = '<input type="button" name="add" value="'.$this->user->lang('uc_bttn_import').'" disabled="disabled" />';
			}else{
				$gimport_out = '<input type="button" name="add" value="'.$this->user->lang('uc_bttn_import').'" class="mainoption" onclick="javascript:GuildImport()" />';
			}
			$settingsdata['game']['importer']['uc_import_guild'] = array(
				'name'			=> 'uc_import_guild',
				'fieldtype'		=> 'direct',
				'direct'		=> $gimport_out,
			);
		}

		if($this->game->get_importAuth('a_members_man', 'char_mupdate')){
			if($this->game->get_importers('guild_imp_rsn') && $this->config->get('uc_servername') == ''){
				$cupdate_out = '<input type="button" name="add" value="'.$this->user->lang('uc_bttn_update').'" disabled="disabled" />';
			}else{
				$cupdate_out = '<input type="button" name="add" value="'.$this->user->lang('uc_bttn_update').'" class="mainoption" onclick="javascript:MassUpdateChars()" />';
			}
			$cupdate_out .= ' ['.(($this->config->get('uc_profileimported')) ? $this->user->lang('uc_last_updated').': '.date($this->user->style['date_time'], $this->config->get('uc_profileimported')) : $this->user->lang('uc_never_updated')).']';
			$settingsdata['game']['importer']['uc_update_all'] = array(
				'name'			=> 'uc_update_all',
				'fieldtype'		=> 'direct',
				'direct'		=> $cupdate_out,
			);
		}

		// Importer cache reset button
		if(($this->game->get_importAuth('a_members_man', 'guild_import') || $this->game->get_importAuth('a_members_man', 'char_mupdate')) && $this->game->get_importers('import_data_cache')){
			$settingsdata['game']['importer']['uc_importer_cache'] = array(
				'name'			=> 'uc_importer_cache',
				'fieldtype'		=> 'direct',
				'direct'		=> '<input type="button" name="add" value="'.$this->user->lang('uc_bttn_resetcache').'" class="mainoption" onclick="javascript:ClearImportCache()" />',
			);
		}

		// merge the game admin array to the existing one
		$myprofiledata = $this->root_path.'games/'.$this->game->get_game().'/admin_data.php';
		if(is_file($myprofiledata)){
			require_once($myprofiledata);
			if(is_array($settingsdata_admin)){
				$settingsdata = array_merge_recursive($settingsdata, $settingsdata_admin);
			}
		}

		//Merge authmethod-settings
		if ($this->user->get_authmethod_settings()){
			$settingsdata = array_merge_recursive($settingsdata, $this->user->get_authmethod_settings());
		}

		//Merge loginmethod-settings
		if ($arrLoginmethodSettings = $this->user->get_loginmethod_settings()){
			$settingsdata = array_merge_recursive($settingsdata, $arrLoginmethodSettings);
		}

		// Inject Plugin Settings
		// PLACEHOLDER.. maybe we will do that one day...

		// save the setting
		if ($this->in->get('save_plus') && $this->checkCSRF('display')){

			foreach($settingsdata as $tabname=>$fieldsetdata){
				foreach($fieldsetdata as $fieldsetname=>$fielddata){
					foreach($fielddata as $name=>$confvars){
						if(isset($confvars['serialized'])){
							$tmp_get	= serialize($this->in->getArray($confvars['name'], $confvars['datatype']));
						}else{
							$tmp_get	= $this->html->widget_return($confvars);
						}
						if(isset($confvars['edecode'])){
							$tmp_get	= html_entity_decode($tmp_get);
						}
						$save_array[$confvars['name']] = $tmp_get;
					}
				}
			}
			//check for changed game
			$game_changed = false;
			if (($this->in->get('default_game') != $this->config->get('default_game')) || ($this->in->get('game_language') != $this->config->get('game_language'))){
				$game_changed = true;
			}
			//check for changed itt 1.prio and load defaults if so
			if($this->config->get('itt_prio1') != $this->in->get('itt_prio1', '')) {
				$save_array = array_merge($save_array, $this->itt->changed_prio1($this->in->get('itt_prio1')));
			}

			// Save the settings array
			$this->config->set($save_array);

			// Since ChangeGame alters Config it has to be executed after config-save
			if($game_changed) {
				$this->game->ChangeGame($this->in->get('default_game'), $this->in->get('game_language'));
				$this->pdc->flush();
				redirect('admin/manage_settings.php'.$this->SID);		// we need to reload cause of the per-game settings
			}

			//clear cache now
			$this->pdc->flush();

			// The Saved-Message
			$this->core->message($this->user->lang('pk_succ_saved'), $this->user->lang('pk_save_title'), 'green');
		}

		// Output
		foreach($settingsdata as $tabname=>$fieldsetdata){
			$this->tpl->assign_block_vars('tabs', array(
				'NAME'	=> $this->user->lang('pk_tab_'.$tabname),
				'ID'	=> $tabname
				)
			);

			foreach($fieldsetdata as $fieldsetname=>$fielddata){
				$this->tpl->assign_block_vars('tabs.fieldset', array(
					'NAME'		=> ($this->user->lang('pk_tab_fs_'.$fieldsetname, false, false)) ? $this->user->lang('pk_tab_fs_'.$fieldsetname) : $this->game->glang('pk_tab_fs_'.$fieldsetname),
					'INFO'		=> ($this->user->lang('pk_tab_info_'.$fieldsetname, false, false)) ? $this->user->lang('pk_tab_info_'.$fieldsetname) : $this->game->glang('pk_tab_info_'.$fieldsetname),
				));

				foreach($fielddata as $name=>$confvars){
					// continue if hmode == true
					if(($this->hmode && $confvars['not4hmode']) || (isset($confvars['disabled']) && $confvars['disabled']===true)){
						continue;
					}
					$no_lang	= (isset($confvars['no_lang'])) ? true : false;
					$selection	= ($this->config->get($confvars['name'])) ? $this->config->get($confvars['name']) : ((isset($confvars['default'])) ? $confvars['default'] : '');
					$this->tpl->assign_block_vars('tabs.fieldset.field', array(
						'NAME'		=> ($this->user->lang($confvars['name'], false, false)) ? $this->user->lang($confvars['name']) : (($this->game->glang($confvars['name'])) ? $this->game->glang($confvars['name']) : $confvars['name']),
						'HELP'		=> ($this->user->lang($confvars['name'].'_help', false, false)) ? $this->user->lang($confvars['name'].'_help') : (($this->game->glang($confvars['name'].'_help')) ? $this->game->glang($confvars['name'].'_help') : ''),
						'FIELD'		=> $this->html->generateField($confvars, $name, $selection, $no_lang),
					));
				}
			}
		}

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('config_title'),
			'template_file'		=> 'admin/manage_settings.html',
			'display'			=> true)
		);
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_mmocms_settings', mmocms_settings::__shortcuts());
registry::register('mmocms_settings');
?>