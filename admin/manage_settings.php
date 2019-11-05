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
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class admin_settings extends page_generic {
	public static $shortcuts = array(
			'itt' => 'infotooltip',
			'social' => 'socialplugins',
			'email'=>'MyMailer',
			'form'	=> array('form', array('core_settings'))
		);

	public function __construct(){
		$this->user->check_auth('a_config_man');

		$handler = array(
			'ajax'	=> array(
				array('process' => 'ajax_gamelanguage',	'value' => 'games'),
				array('process' => 'ajax_mailtest',	'value' => 'mailtest'),
			),
			'dellogo' => array('process' => 'delete_logo'),
		);
		parent::__construct(false, $handler, array(), null, '');
		$this->process();
	}

	public function delete_logo(){
		$this->pfh->Delete( $this->pfh->FolderPath('','files').$this->config->get('custom_logo'));
		$this->config->set("custom_logo", "");
	}

	public function ajax_mailtest(){
		$adminmail	= register('encrypt')->decrypt($this->config->get('admin_email'));

		$options = array(
				'template_type'		=> 'input',
		);

		//Set E-Mail-Options
		$this->email->SetOptions($options);

		$blnResult = $this->email->SendMail($adminmail, $adminmail, "Testmail", "This is a test mail to check your mail settings.");

		if($blnResult){
			echo $this->user->lang('test_mail_ok');
		} else {
			echo $this->user->lang('test_mail_fail').'<br /><br />';
			echo nl2br($this->email->getLatestErrorMessage());
		}

		exit;
	}

	public function ajax_gamelanguage() {
		$options = array(
			'options_only'	=> true,
			'tolang'		=> true,
			'no_key'		=> true,
			'format'		=> 'ucfirst',
			'options' 		=> sdir($this->root_path . 'games/'.$this->in->get('requestid').'/language/', '*.php', '.php'),
			'value'			=> $this->config->get('game_language'),
		);
		echo (new hdropdown('dummy', $options))->output();
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

		$a_startday = array(
			'sunday'	=> $this->user->lang(array('time_daynames', 6)),
			'monday'	=> $this->user->lang(array('time_daynames', 0)),
		);

		$a_calraid_status = array(
			0					=> $this->user->lang(array('raidevent_raid_status', 0)),
			1					=> $this->user->lang(array('raidevent_raid_status', 1)),
			2					=> $this->user->lang(array('raidevent_raid_status', 2)),
			3					=> $this->user->lang(array('raidevent_raid_status', 3)),
			4					=> $this->user->lang(array('raidevent_raid_status', 4))
		);

		$a_calraid_status2 = array(
			0					=> $this->user->lang(array('raidevent_raid_status', 0)),
			1					=> $this->user->lang(array('raidevent_raid_status', 1)),
		);

		$a_calraid_nsfilter = array(
			'twinks'	=> 'raidevent_raid_nsf_twink',
			'inactive'=> 'raidevent_raid_nsf_inctv',
			'hidden'	=> 'raidevent_raid_nsf_hiddn',
			'special'	=> 'raidevent_raid_nsf_special',
		);

		$a_debug_mode = array(
			'0'				=> 'core_sett_f_debug_type0',
			'1'				=> 'core_sett_f_debug_type1',
			'2'				=> 'core_sett_f_debug_type2',
			'3'				=> 'core_sett_f_debug_type3',
			'4'				=> 'core_sett_f_debug_type4',
		);

		$accact_array = array(
			'0'				=> 'none',
			'1'				=> 'user',
			'2'				=> 'admin',
		);

		$mail_array = array(
			'mail'		=> 'lib_email_mail',
			'sendmail'=> 'lib_email_sendmail',
			'smtp'		=> 'lib_email_smtp',
		);

		$smtp_connection_methods = array(
			''				=> 'none',
			'ssl'			=> 'SSL/TLS',
			'tls'			=> 'STARTTLS'
		);

		$a_calendar_addevmode = array(
			'event'		=> 'calendar_mode_event',
			'raid'		=> 'calendar_mode_raid'
		);

		$a_calendar_guestmodes = array(
			0					=> 'raidevent_guests_enable_none',
			1					=> 'raidevent_guests_enable_ops',
			2					=> 'raidevent_guests_enable_both',
		);

		$mobile_template_array = array("" => $this->user->lang('default_setting'));
		foreach($this->pdh->get('styles', 'styles', array(0, false)) as $styleid=>$row){
			$mobile_template_array[$styleid] = $row['style_name'];
		}

		$mobile_portallayout_array = array("" => $this->user->lang('default_setting'));
		foreach($this->pdh->get('portal_layouts', 'id_list') as $layoutid){
			$mobile_portallayout_array[$layoutid] = $this->pdh->get('portal_layouts', 'name', array($layoutid));
		}

		$mobile_pagelayout_array = array("" => $this->user->lang('default_setting'));
		foreach($this->pdh->get_layout_list() as $key => $val){
			$mobile_pagelayout_array[$val] = $val;
		}

		$a_groups = $this->pdh->aget('raid_groups', 'name', false, array($this->pdh->get('raid_groups', 'id_list')));

		// Startpage
		$arrMenuItems = $this->core->build_menu_array(true, true);
		$startpage_array = array();
		foreach($arrMenuItems as $page){
			$link = $this->user->removeSIDfromString($page['link']);
			if ($link != "" && $link != "#" && $link != "index.php"){
				if (isset($page['category'])){
					$strAlias = $this->pdh->get('article_categories', 'alias', array($page['id']));
					if(!isset($startpage_array[$strAlias])) $startpage_array[$this->pdh->get('article_categories', 'alias', array($page['id']))] = $this->pdh->get('article_categories', 'name_prefix', array($page['id'])).$this->pdh->get('article_categories', 'name', array($page['id']));
				} elseif(isset($page['article'])){
					$catid = $this->pdh->get('articles', 'category', array($page['id']));
					$startpage_array[$this->pdh->get('articles', 'alias', array($page['id']))] = $this->pdh->get('article_categories', 'name_prefix', array($catid)).' -> '.$this->pdh->get('articles', 'title', array($page['id']));
				} elseif(!isset($page['pluslink'])) {
					$startpage_array[$link] = $page['text'].' ('.$link.')';
				}
			}
		}

		// Build language array
		$language_array = $locale_array = $lang = array();
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
		foreach ($arrSocialPlugins as $key){
			$arrSocialFields['sp_'.$key] = array(
				'type' => 'radio',
			);
		}
		$arrSocialButtons = $this->social->getSocialButtons();
		foreach ($arrSocialButtons as $key){
			$arrSocialFields['sp_'.$key] = array(
				'type' => 'radio',
			);
		}

		// ---------------------------------------------------------
		// Member Array
		// ---------------------------------------------------------
		$members = $this->pdh->aget('member', 'name', 0, array($this->pdh->get('member', 'id_list', array(false, false, false))));
		asort($members);


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

		// ---------------------------------------------------------
		// Output to the page
		// ---------------------------------------------------------
		$this->jquery->Dialog('template_preview', $this->user->lang('template_preview'), array('url'=>$this->root_path."viewnews.php".$this->SID."&amp;style='+ $(\"select[name='user_style'] option:selected\").val()+'", 'width'=>'750', 'height'=>'520', 'modal'=>true));

		// initialize form class
		$this->form->lang_prefix = 'core_sett_';
		$this->form->use_tabs = true;
		$this->form->use_fieldsets = true;

		// define standard data for settings
		$settingsdata = array(
			'global' => array(
				'global' => array(
					'main_title'	=> array(
						'type'		=> 'text',
						'size'		=> 40
					),
					'sub_title'		=> array(
						'type'		=> 'text',
						'size'		=> 40
					),
					'notify_updates_email' => array(
							'type'		=> 'radio',
					),
					'disable_guild_features' => array(
							'type'		=> 'radio',
					),
					'debug'	=> array(
						'type'		=> 'dropdown',
						'tolang'	=> true,
						'options'	=> $a_debug_mode,
					)
				),
				'meta'	=> array(
					'meta_keywords' => array(
						'type'		=> 'text',
						'size'		=> 40
					),
					'meta_description' => array(
						'type'		=> 'text',
						'size'		=> 40
					),
				),
				'js' => array(
					'global_js'	=> array(
							'type'			=> 'textarea',
							'cols'			=> 80,
							'rows'			=> 5,
							'codeinput'		=> true,
					),
					'global_css'=> array(
							'type'			=> 'textarea',
							'cols'			=> 80,
							'rows'			=> 5,
							'codeinput'		=> true,
					),
				),
			),
			'system'	=> array(
				'globalsettings'	=> array(
					'default_locale'	=> array(
						'type'			=> 'dropdown',
						'options'		=> $locale_array,
					),
					'server_path'	=> array(
						'type'		=> 'text',
						'size'		=> 50,
					),
					'enable_gzip'	=> array(
						'type'		=> 'radio',
						'default'	=> 0
					),
				),
				'auth'			=> array(
					'auth_method'	=> array(
						'type'		=> 'dropdown',
						'options'	=> $this->user->get_available_authmethods(),
						'default'	=> 'db',

					),
				),
				'cookie'			=> array(
					'cookie_domain'	=> array(
						'type'		=> 'text',
						'size'		=> 25,
					),
					'cookie_name'	=> array(
						'type'		=> 'text',
						'size'		=> 25,
					),
					'cookie_path'	=> array(
						'type'		=> 'text',
						'size'		=> 25,
					),
				),
				'email'				=> array(
					'lib_email_method'	=> array(
						'type'			=> 'dropdown',
						'tolang'		=> true,
						'options'		=> $mail_array,
						'text_after' => '<button onclick="testmail()" type="button"><i class="fa fa-lg fa-envelope-o"></i> '.$this->user->lang('test_mail').'</button>',
						'dependency'	=> array(
							'sendmail' => array('lib_email_sendmail_path'),
							'smtp' => array('lib_email_smtp_host', 'lib_email_smtp_port', 'lib_email_smtp_connmethod', 'lib_email_smtp_auth', 'lib_email_smtp_user', 'lib_email_smtp_pw')
						)
					),
					'admin_email'	=> array(
						'type'		=> 'email',
						'size'		=> 30,
						'encrypt'	=> true,
					),
					'lib_email_sender_name'	=> array(
						'type'		=> 'text',
						'size'		=> 30
					),
					'lib_email_sendmail_path'	=> array(
						'type'			=> 'text',
						'size'			=> 30,
					),
					'lib_email_smtp_host'	=> array(
						'type'			=> 'text',
						'size'			=> 30,
					),
					'lib_email_smtp_port'	=> array(
						'type'			=> 'text',
						'size'			=> 5,
						'default'		=> 25,
					),
					'lib_email_smtp_connmethod'	=> array(
						'type'			=> 'dropdown',
						'options'		=> $smtp_connection_methods,
					),
					'lib_email_smtp_auth'	=> array(
						'type'			=> 'radio',
					),
					'lib_email_smtp_user'	=> array(
						'type'			=> 'text',
						'size'			=> 30,
					),
					'lib_email_smtp_pw'	=> array(
						'type'			=> 'password',
						'required'		=> false,
						'pattern'		=> '',
						'size'			=> 30,
						'set_value'		=> true,
					),
					'lib_email_signature'	=> array(
						'type'			=> 'radio',
						'dependency'	=> array(1 => array('lib_email_signature_value')),
					),
					'lib_email_signature_value'	=> array(
						'type'			=> 'textarea',
						'default'		=> $signature,
						'cols'			=> 80,
						'rows'			=> 5,
					),
				),
				'recaptcha'		=> array(
					'enable_captcha'	=> array(
						'type'		=> 'radio',
					),
					'recaptcha_type' => array(
						'type' 		=> 'radio',
						'options'	=> array(
							'v2' => 'V2 - Checkbox',
							'invisible' => 'V2 - Invisible',
						),
						'default' => 'v2',
					),
					'lib_recaptcha_okey'	=> array(
						'type'		=> 'text',
						'size'		=> 30
					),
					'lib_recaptcha_pkey'	=> array(
						'type'		=> 'text',
						'size'		=> 30
					)
				),
				'date'		=> array(
					'timezone'	=> array(
						'type'		=> 'dropdown',
						'options'	=> $this->time->timezones,
					),
					'date_startday'	=> array(
						'type'		=> 'dropdown',
						'options'	=> $a_startday,
					),
					'default_date_time'	=> array(
						'type'		=> 'text',
						'size'		=> 10,
						'default'	=> $this->user->lang('style_time')
					),
					'default_date_short'	=> array(
						'type'		=> 'text',
						'size'		=> 20,
						'default'	=> $this->user->lang('style_date_short')
					),
					'default_date_long'	=> array(
						'type'		=> 'text',
						'size'		=> 20,
						'default'	=> $this->user->lang('style_date_long')
					),
					'default_jsdate_time'	=> array(
						'type'		=> 'text',
						'size'		=> 20,
						'default'	=> $this->user->lang('style_jstime')
					),
					'default_jsdate_nrml'	=> array(
						'type'		=> 'text',
						'size'		=> 20,
						'default'	=> $this->user->lang('style_jsdate_nrml')
					),
					'default_jsdate_short'	=> array(
						'type'		=> 'text',
						'size'		=> 20,
						'default'	=> $this->user->lang('style_jsdate_short')
					)
				)
			),
			'user'	=> array(
				'user'	=> array(
					'default_lang'	=> array(
						'type'		=> 'dropdown',
						'options'	=> $language_array,
					),
					'account_activation'	=> array(
						'type'		=> 'radio',
						'tolang'	=> true,
						'options'	=> $accact_array,
						'default'	=> 0
					),
					'failed_logins_inactivity'	=> array(
						'type'		=> 'text',
						'size'		=> 5,
						'default'	=> 5,
					),
					'enable_registration'	=> array(
						'type'		=> 'radio',
					),
					'stopforumspam_use' => array(
							'type'		=> 'radio',
							'dependency' => array(1=>array('stopforumspam_action')),
					),
					'stopforumspam_action' => array(
							'type' => 'radio',
							'options' => array('deny' => $this->user->lang('stopforumspam_action_deny'), 'disable' => $this->user->lang('stopforumspam_action_disable'))
					),
					'enable_username_change'	=> array(
						'type'		=> 'radio',
					),
					'default_style_overwrite'	=> array(
						'type'		=> 'radio',
					),
					'special_user'	=> array(
						'type'			=> 'multiselect',
						'options'		=> $this->pdh->aget('user', 'name', 0, array($this->pdh->get('user', 'id_list', array(false)))),
						'datatype'		=> 'int'
					),
					'password_length' => array(
						'default'	=> 8,
						'type'		=> 'spinner',
						'min'		=> 6,
					),
					'banned_emails' => array(
						'type'			=> 'textarea',
						'cols'			=> 80,
						'rows'			=> 5,
					),
				),
					'login'			=> array(
							'login_method'	=> array(
									'type'		=> 'multiselect',
									'options'	=> $this->user->get_available_loginmethods(),
									'tolang'	=> true,
							),
							'login_fastregister'	=> array(
									'type'		=> 'radio',
							),
					),
				'avatar' => array(
					'avatar_allowed' => array(
						'type' => 'multiselect',
						'default' => array('eqdkp'),
						'options' => array(),
					),
					'avatar_default' => array(
						'type' => 'radio',
						'default' => 'eqdkp',
						'options' => array(),
					),
				),
			),
			'points' => array(
				'points' => array(
						'enable_points'=> array(
								'type'		=> 'radio',
						),
						'dkp_name'		=> array(
								'type'		=> 'text',
								'size'		=> 5,
						),

						'round_activate'	=> array(
								'type'		=> 'radio',
								'default'	=> 0,
								'dependency' => array(1=>array('round_precision')),
						),
						'round_precision'	=> array(
								'type'		=> 'text',
								'size'		=> 2,
								'default'	=> 0
						),
						'enable_leaderboard'=> array(
								'type'		=> 'radio',
						),
						'color_items'	=> array(
								'type'		=> 'slider',
								'label'		=> $this->user->lang('core_sett_f_color_items'),
								'min'		=> 0,
								'max'		=> 100,
								'width'		=> '300px'
						),
						'dkp_easymode'=> array(
								'type'		=> 'radio',
						),
				),
			),
			'chars'		=> array(
				'chars'		=> array(
					'class_color'	=> array(
						'type'		=> 'radio',
					),
					'special_members'	=> array(
						'type'		=> 'multiselect',
						'options'		=> $members,
					),
					'show_twinks'	=> array(
						'type'		=> 'radio',
						'dependency'=> array(0 => array('detail_twink')),
					),
					'detail_twink'	=> array(
						'type'		=> 'radio',
					),
					'hide_inactive'	=> array(
						'type'		=> 'radio'
					),
					'inactive_period'	=> array(
						'type'		=> 'text',
						'size'		=> 5,
						'default'	=> 0
					),
					'auto_set_active' => array(
						'type'		=> 'radio',
						'default'	=> 1,
					)
				)
			),
			'calendar'	=> array(
				'calendar'	=> array(
					'calendar_addevent_mode'	=> array(
						'type'			=> 'dropdown',
						'options'		=> $a_calendar_addevmode,
						'tolang'		=> true
					),
					'calendar_show_birthday'	=> array(
						'type'			=> 'radio',
					)
				),
				'raids'		=> array(
					/*'calendar_raid_enabled'	=> array(
						'type'	=> 'radio',
						'dependency'	=> array(1 => array('calendar_raid_guests', 'calendar_raid_random', 'calendar_raid_status', 'calendar_raid_nsfilter', 'calendar_addraid_deadline', 'calendar_addraid_duration', 'calendar_addraid_use_def_start', 'calendar_repeat_crondays', 'calendar_raid_confirm_raidgroupchars', 'calendar_raid_add_raidgroupchars', 'calendar_raidleader_autoinvite', 'calendar_raid_notsigned_classsort', 'calendar_raid_coloredclassnames', 'calendar_raid_shownotsigned', 'calendar_raid_allowstatuschange', 'calendar_raid_statuschange_status')),
					),*/
					'calendar_raid_guests'	=> array(
						'type'			=> 'dropdown',
						'options'		=> $a_calendar_guestmodes,
						'tolang'		=> true
					),
					'calendar_raid_random'	=> array(
						'type'			=> 'radio',
					),
					'calendar_raid_status'	=> array(
						'type'			=> 'multiselect',
						'options'		=> $a_calraid_status,
						'datatype'		=> 'int'
					),
					'calendar_raid_nsfilter'	=> array(
						'type'			=> 'multiselect',
						'options'		=> $a_calraid_nsfilter,
						'tolang'		=> true
					),
					'calendar_addraid_deadline'	=> array(
						'type'			=> 'spinner',
						'size'			=> 5,
						'default'		=> 1
					),
					'calendar_addraid_duration'	=> array(
						'type'			=> 'spinner',
						'size'			=> 5,
						'min'			=> 10,
						'step'			=> 10,
						'default'		=> 120
					),
					'calendar_addraid_use_def_start'	=> array(
						'type'			=> 'radio',
						'dependency'	=> array(1 => array('calendar_addraid_def_starttime')),
					),
					'calendar_addraid_def_starttime'	=> array(
						'type'			=> 'timepicker',
						'default'		=> '20:00'
					),
					'calendar_repeat_crondays'	=> array(
						'type'			=> 'spinner',
						'size'			=> 5,
						'min'			=> 5,
						'step'			=> 5,
						'default'		=> 40
					),
					'calendar_raid_confirm_raidgroupchars'	=> array(
						'type'			=> 'multiselect',
						'options'		=> $a_groups,
						'datatype'		=> 'int',
					),
					'calendar_raid_add_raidgroupchars'	=> array(
						'type'			=> 'multiselect',
						'options'		=> $a_groups,
						'datatype'		=> 'int',
					),
					'calendar_raidleader_autoinvite'	=> array(
						'type'			=> 'radio',
					),
					'calendar_raid_notsigned_classsort'	=> array(
						'type'			=> 'radio',
					),
					'calendar_raid_attendees_classsort'	=> array(
						'type'			=> 'radio',
					),
					'calendar_raid_coloredclassnames'	=> array(
						'type'			=> 'radio',
					),
					'calendar_raid_shownotsigned'	=> array(
						'type'			=> 'radio',
					),
					'calendar_raid_allowstatuschange'	=> array(
						'type'			=> 'radio',
					),
					'calendar_raid_statuschange_status'	=> array(
						'type'			=> 'dropdown',
						'options'		=> $a_calraid_status2,
						'datatype'		=> 'int'
					),
				),
			),
			'game'		=> array(
				'game'	=> array(
					'default_game'	=> array(
						'type'		=> 'dropdown',
						'options'	=> $games,
						'default'	=> 'dummy',
						'ajax_reload' => array('game_language', 'manage_settings.php'.$this->SID.'&ajax=games'),
					),
					'game_language'	=> array(
						'type'		=> 'dropdown',
						'options'	=> array('--------'),
					),
					'guildtag'		=> array(
						'type'		=> 'text',
						'size'			=> 35
					),
				)
			),
			'portal'	=> array(
				'portal'	=> array(
					'start_page'	=> array(
						'type'		=> 'singleselect',
						'options'	=> $startpage_array,
						'filter'	=> true,
					),
					'disable_xframe_header' => array(
						'type'	=> 'radio',
					),
					'access_control_header' => array(
						'type'	=> 'textarea',
						'cols'	=> 80,
						'rows'	=> 5,
					),
					'enable_comments'	=> array(
							'type'		=> 'radio',
					),
				),
				'mobile' => array(
					'mobile_template' => array(
						'type'		=> 'dropdown',
						'options'	=> $mobile_template_array,
						'default'	=> "",
					),
					'mobile_portallayout' => array(
						'type'		=> 'dropdown',
						'options'	=> $mobile_portallayout_array,
						'default'	=> "",
					),
					'mobile_pagelayout' => array(
						'type'		=> 'dropdown',
						'options'	=> $mobile_pagelayout_array,
						'default'	=> -1,
					),
				),
				'article'		=> array(
					'enable_embedly'	=> array(
						'type'	=> 'radio',
					),
					'embedly_gdpr'	=> array(
							'type'	=> 'radio',
					),
					'articleimage_size' => array(
						'type'		=> 'spinner',
						'size'		=> 3,
						'default'	=> 120,
						'step'		=> 1,
						'min'		=> 32,
					),
					'thumbnail_defaultsize' => array(
						'type'		=> 'spinner',
						'size'		=> 3,
						'default'	=> 500,
						'step'		=> 25,
						'min'		=> 25
					),
				),
				'seo'	=> array(
					'seo_remove_index'	=> array(
						'type'		=> 'radio',
					),
					'seo_extension'	=> array(
						'type'		=> 'dropdown',
						'options'	=> array("/", ".html", ".php"),
					),
				),
				'social_sharing' => $arrSocialFields,
			),
			'layout'	=> array(
				'layout'	=> array(
					'custom_logo'	=> array(
						'type'			=> 'imageuploader',
						'imgpath'		=> $this->pfh->FolderPath('','files'),
						//'noimgfile'		=> "templates/".$this->user->style['template_path']."/images/logo.png",
						'returnFormat'	=> 'in_data',
						'deletelink'	=> $this->root_path.'admin/manage_settings.php'.$this->SID.'&dellogo=true',
					),
					'itemhistory_dia'	=> array(
						'type'		=> 'radio',
					),
				),
				'default'	=> array(
					'default_alimit'	=> array(
						'type'		=> 'spinner',
						'min'			=> 10,
						'step'		=> 10,
					),
					'default_elimit'	=> array(
						'type'		=> 'spinner',
						'min'			=> 10,
						'step'		=> 10,
					),
					'default_ilimit'	=> array(
						'type'		=> 'spinner',
						'min'			=> 10,
						'step'		=> 10,
					),
					'default_nlimit'	=> array(
						'type'		=> 'spinner',
						'min'			=> 0,
					),
					'default_rlimit'	=> array(
						'type'		=> 'spinner',
						'min'			=> 10,
						'step'		=> 10,
					),
				)
			),
			'itemtooltip'	=> array(),	// placeholder for sorting..
		);
		if($this->config->get('default_game') == 'dummy'){
			$settingsdata['game']['game']['default_game']['text_after'] = '<button onclick="window.location=\'manage_extensions.php'.$this->SID.'#fragment-7\'" type="button"><i class="fa fa-lg fa-download"></i> '.$this->user->lang('install_other_games').'</button>';
		}

		if($this->config->get('disable_guild_features')) {
			unset($settingsdata['itemtooltip']);
			unset($settingsdata['calendar']['raids']);
			unset($settingsdata['calendar']['calendar']['calendar_addevent_mode']);
			unset($settingsdata['chars']);
			unset($settingsdata['points']);
			unset($settingsdata['game']);
		}

		//Avatar Provider
		$arrAllAvatarProviders = array();
		foreach($this->user->getAvatarProviders() as $key => $val){
			$arrAllAvatarProviders[$key] = $val['name'];
		}
		$settingsdata['user']['avatar']['avatar_allowed']['options'] = $arrAllAvatarProviders;
		$settingsdata['user']['avatar']['avatar_default']['options'] = $arrAllAvatarProviders;
		$this->form->add_tabs($settingsdata);

		// add some additional fields
		// ItemTooltip Inject
		$fields = array(
				'infotooltip_use'	=> array(
						'type'		=> 'radio',
				),
		);
		if(!$this->config->get('disable_guild_features')) $this->form->add_fields($fields, 'itemtooltip' ,'itemtooltip');

		if(count($this->itt->get_parserlist()) && !$this->config->get('disable_guild_features')){
			$fields = array(
				'itt_debug'	=> array(
						'type'		=> 'radio',
				),
				'itt_trash'	=> array(
						'type'		=> 'direct',
						'text'		=> '<input type="submit" name="itt_reset" value="'.$this->user->lang('itt_reset').'" class="mainoption bi_reset" />',
				),
			);
			$this->form->add_fields($fields, 'itemtooltip' ,'itemtooltip');

			$itt_parserlist	= $this->itt->get_parserlist();

			$fields	= array(
				'itt_prio1'	=> array(
					'type'		=> 'dropdown',
					'name'			=> 'itt_prio1',
					'options'		=> $itt_parserlist
				),
				'itt_prio2'	=> array(
					'type'		=> 'dropdown',
					'name'			=> 'itt_prio2',
					'options'		=> $itt_parserlist
				),
			);
			$this->form->add_fields($fields, 'priorities' ,'itemtooltip');

			// TODO: rework this, it overwrites settings of previous parsers (in ittsettdata), mb use ajax-dd
			$ittsettdata = $this->itt->get_extra_settings();
			if(is_array($ittsettdata)){
				$this->form->add_fields($ittsettdata, 'ittdbsettings', 'itemtooltip');
				//add button to reload defaults
				$this->form->add_field('itt_force_default', array(
					'type'		=> 'direct',
					'text'		=> '<input type="submit" name="itt_force_default" value="'.$this->user->lang('core_sett_f_itt_force_default').'" class="mainoption bi_reset" />',
				), 'ittdbsettings', 'itemtooltip');
			}

			$itt_langlist	= $this->itt->get_supported_languages();
			$fields = array();
			for($i=1; $i<=3; $i++){
				$fields['itt_langprio'.$i]	= array(
					'type'	=> 'dropdown',
					'options'	=> $itt_langlist,
				);
			}

			$fields['itt_overwrite_lang']	= array(
					'type'		=> 'radio',
			);
			$this->form->add_fields($fields, 'ittlanguages', 'itemtooltip');

			//check if user wanted to reset itt-cache
			if($this->in->get('itt_reset', false)) {
				$this->itt->reset_cache();
				$this->core->message($this->user->lang('itt_reset_success'), $this->user->lang('success'), 'green');
			}

			//check if user wanted to reload defaults$
			if($this->in->get('itt_force_default', '') != ''){
				$this->config->set($this->itt->changed_prio1($this->in->get('default_game', 'dummy'), $this->in->get('itt_prio1')));
				$this->core->message($this->user->lang('itt_default_success'), $this->user->lang('success'), 'green');
			}
		}
		//Own Tooltips

		$fields = array(
			'infotooltip_own_enabled'	=> array(
				'type'		=> 'radio',
				'dependency'	=> array(1 => array('infotooltip_own_script', 'infotooltip_own_link')),
			),
			'infotooltip_own_script'	=> array(
				'type'			=> 'textarea',
				'cols'			=> 80,
				'rows'			=> 5,
				'codeinput'		=> true,
			),
			'infotooltip_own_link'	=> array(
				'type'			=> 'textarea',
				'cols'			=> 80,
				'rows'			=> 2,
				'codeinput'		=> true,
			),
		);
		if(!$this->config->get('disable_guild_features')) $this->form->add_fields($fields, 'ittownscripts', 'itemtooltip');

		// Importer API Key Wizzard
		$apikey_config		= $this->game->get_importers('apikey');
		if(($this->game->get_importAuth('a_members_man', 'guild_import') || $this->game->get_importAuth('a_members_man', 'char_mupdate')) && $apikey_config){
			if($apikey_config['status'] == 'required' || $apikey_config['status'] == 'optional'){
				if(isset($apikey_config['steps']) && is_array($apikey_config['steps']) && count($apikey_config['steps']) > 0){
					$appisetts	= array();
					foreach($apikey_config['steps'] as $title=>$val){
						$appisetts[$this->game->glang($title)]	= $this->game->glang($val);
					}

					// now, let us add the API-Key-Field to the last element of the array
					if(isset($apikey_config['version']) && isset($apikey_config['form']) &&  $apikey_config['version'] > 1 && is_array($apikey_config['form'])){
						$apikeyform = '';
						$apikey_set	= false;
						foreach($apikey_config['form'] as $fieldname=>$fieldcontent){
							if($this->config->get($fieldname) != '') { $apikey_set = true; }
							$value = ($this->in->exists($fieldname)) ? $this->in->get($fieldname) : $this->config->get($fieldname);
							$apikeyform	.= '<br/>'.$this->game->glang($fieldname).': '.$this->form->field($fieldname, array_merge($fieldcontent, array('value'=>$value)));
						}
					}

					end($appisetts);
					$key				= key($appisetts);
					reset($appisetts);
					$appisetts[$key]	= str_replace('{APIKEY_FORM}', $apikeyform, $appisetts[$key]);

					$this->form->add_field('settings_apikey', array(
						'type'		=> 'accordion',
						'options'	=> $appisetts,
						'active'	=> (($apikey_set) ? (count($appisetts)-1) : 0),
					), 'importer', 'game');
				}
			}
		}

		// The importer settings
		if($this->game->get_importAuth('a_members_man', 'guild_import')){
			if(($this->game->get_importers('guild_imp_rsn') && $this->config->get('servername') == '') || $this->game->get_apikeyfield_requiered_and_empty()){
				$gimport_out = '<input type="button" name="add" value="'.$this->user->lang('uc_bttn_import').'" disabled="disabled" />';
			}else{
				$gimport_out = '<input type="button" name="add" value="'.$this->user->lang('uc_bttn_import').'" class="mainoption" onclick="javascript:GuildImport()" />';
			}
			$this->form->add_field('uc_import_guild', array(
				'lang'	=> 'uc_import_guild',
				'type'	=> 'direct',
				'text'	=> $gimport_out,
			), 'importer', 'game');
		}

		if($this->game->get_importAuth('a_members_man', 'char_mupdate')){
			if(($this->game->get_importers('guild_imp_rsn') && $this->config->get('servername') == '')  || $this->game->get_apikeyfield_requiered_and_empty()){
				$cupdate_out = '<input type="button" name="add" value="'.$this->user->lang('uc_bttn_update').'" disabled="disabled" />';
			}else{
				$cupdate_out = '<input type="button" name="add" value="'.$this->user->lang('uc_bttn_update').'" class="mainoption" onclick="javascript:MassUpdateChars()" />';
			}
			$cupdate_out .= ' ['.(($this->config->get('uc_profileimported')) ? $this->user->lang('uc_last_updated').': '.date($this->user->style['date_time'], $this->config->get('uc_profileimported')) : $this->user->lang('uc_never_updated')).']';
			$this->form->add_field('uc_update_all', array(
				'lang'	=> 'uc_update_all',
				'type'	=> 'direct',
				'text'	=> $cupdate_out,
			), 'importer', 'game');
		}

		// Importer cache reset button
		if(($this->game->get_importAuth('a_members_man', 'guild_import') || $this->game->get_importAuth('a_members_man', 'char_mupdate')) && $this->game->get_importers('import_data_cache')){
			$this->form->add_field('uc_importer_cache', array(
				'lang'	=> 'uc_importer_cache',
				'type'	=> 'direct',
				'text'	=> '<input type="button" name="add" value="'.$this->user->lang('uc_bttn_resetcache').'" class="mainoption" onclick="javascript:ClearImportCache()" />',
			), 'importer', 'game');
		}

		// merge the game admin array to the existing one
		$settingsdata_admin = $this->game->admin_settings();
		if(is_array($settingsdata_admin) && !empty($settingsdata_admin)){
			$this->form->add_fields($settingsdata_admin, 'gamesettings', 'game');
		}

		//Merge authmethod-settings
		if ($authmethodSettings = $this->user->get_authmethod_settings()){
			$this->form->add_fields($authmethodSettings, 'auth', 'system');
		}

		//Merge loginmethod-settings
		if ($arrLoginmethodSettings = $this->user->get_loginmethod_settings()){
			$this->form->add_fields($arrLoginmethodSettings, 'login', 'user');
		}

		//Notifications
		$this->form->add_fields($this->ntfy->getNotificationMethodsAdminSettings(), 'notifications', 'user');


		// Inject Plugin Settings
		// PLACEHOLDER.. maybe we will do that one day...

		// save the setting
		if ($this->in->exists('save_plus') && $this->checkCSRF('display') && !$this->settings_saved){

			$save_array = $this->form->return_values($this->config->get_config());
			//check for changed game
			$game_changed = false;

			// add the API key save code
			if(isset($apikey_config['version']) && isset($apikey_config['form']) &&  $apikey_config['version'] > 1 && is_array($apikey_config['form'])){
				$apikeyform = '';
				foreach($apikey_config['form'] as $fieldname=>$fieldcontent){
					$save_array[$fieldname]	= trim($this->in->get($fieldname, ''));
				}
			}else{
				$save_array['game_importer_apikey']	= trim($this->in->get('game_importer_apikey', ''));
			}

			// default game
			if (($this->in->get('default_game', 'dummy') != $this->config->get('default_game')) || ($this->in->get('game_language') != $this->config->get('game_language'))){
				$game_changed = true;
			}
			//check for changed itt 1.prio and load defaults if so
			if($this->config->get('itt_prio1') != $this->in->get('itt_prio1', '')) {
				$save_array = array_merge($save_array, $this->itt->changed_prio1($this->in->get('default_game', 'dummy'), $this->in->get('itt_prio1')));
			}

			//check for changed disable points
			if((int)$this->config->get('enable_points') != $this->in->get('enable_points', 1)){
				if ($this->in->get('enable_points', 1) == 0) {$this->config->set('eqdkp_layout', "nopoints");} else $this->config->set('eqdkp_layout', "normal");
			}

			// Save the settings array
			$this->config->set($save_array);

			// Since ChangeGame alters Config it has to be executed after config-save
			if($game_changed) {
				$strDefaultGame = $this->in->get('default_game', 'dummy');
				if($strDefaultGame == "") $strDefaultGame = 'dummy';

				$this->game->installGame($strDefaultGame, $this->in->get('game_language'), $this->in->get('overwrite-game', 0));
				if($this->config->get('disable_guild_features')) $this->config->set('default_game', 'dummy');
				$this->pdc->flush();
				$objStyles = register('styles');
				$objStyles->delete_cache(false);
				$this->form->reset_fields();
				$this->settings_saved = true;
				$itt_parserlist	= $this->itt->get_parserlist($this->in->get('default_game', 'dummy'));
				$this->config->set($this->itt->changed_prio1($this->in->get('default_game', 'dummy'), key($itt_parserlist)));
				$this->display();
				redirect('admin/manage_settings.php'.$this->SID);		// we need to reload cause of the per-game settings
			}

			//clear cache now
			$this->pdc->flush();

			// The Saved-Message
			$this->core->message($this->user->lang('pk_succ_saved'), $this->user->lang('pk_save_title'), 'green');
		}

		//Hint for ReCaptcha
		if($this->config->get('enable_captcha') == 1 && !strlen($this->config->get('lib_recaptcha_pkey') ))
			$this->core->message($this->user->lang('recaptcha_nokeys_hint'), "ReCaptcha", 'red');

		// Output the form, pass values in
		$this->form->output($this->config->get_config());

		$this->tpl->assign_vars(array(
			'SET_GAME_LANG'	=> $this->config->get('game_language'),
		));

		$this->core->set_vars([
			'page_title'		=> $this->user->lang('config_title'),
			'template_file'		=> 'admin/manage_settings.html',
			'page_path'			=> [
				['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
				['title'=>$this->user->lang('config_title'), 'url'=>' '],
			],
			'display'			=> true
		]);
	}
}
registry::register('admin_settings');
