<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2002
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
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');

//AJAX
if(registry::fetch('in')->get('ajax', 0) === 1){
	if($_POST['username']){
		if(registry::fetch('in')->exists('olduser') && registry::fetch('in')->get('olduser') === $_POST['username']){
			echo 'true';
		}else{
			echo registry::fetch('pdh')->get('user', 'check_username', array(registry::fetch('in')->get('username')));
		}
	}
	if($_POST['email_address']){
		if(registry::fetch('in')->exists('oldmail') && registry::fetch('in')->get('oldmail') === $_POST['email_address']){
			echo 'true';
		}else{
			echo registry::fetch('pdh')->get('user', 'check_email', array(registry::fetch('in')->get('email_address')));
		}
	}
	exit;
}


class user_settings extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'pdh', 'jquery', 'config', 'core', 'html', 'pm', 'time', 'pfh', 'bridge');
		return array_merge(parent::$shortcuts, $shortcuts);
	}
	private $logo_upload = false;

	public function __construct() {
		if (!$this->user->is_signedin()){
			redirect('login.php'.$this->SID);
		}

		$handler = array(
			'newexchangekey' => array('process' => 'renew_exchangekey', 'csrf' => true),
			'submit' => array('process' => 'update', 'csrf' => true),
			'mode' => array(
				array('process' => 'delete_authaccount', 'value' => 'delauthacc', 'csrf' => true),
				array('process' => 'add_authaccount', 'value' => 'addauthacc'),
				array('process' => 'delete_avatar', 'value' => 'deleteavatar',  'csrf' => true),
			),

		);
		parent::__construct(false, $handler);

		$this->process();
	}

	public function renew_exchangekey(){
		$app_key = $this->pdh->put('user', 'create_new_exchangekey', array($this->user->id));
		$this->user->data['exchange_key'] = $app_key;
		if ($app_key) $this->core->message($this->user->lang('user_create_new appkey_success'), $this->user->lang('success'), 'green');
	}

	public function delete_avatar() {
		$this->pdh->put('user', 'delete_avatar', array($this->user->data['user_id']));
		$this->pdh->process_hook_queue();
		unset($this->user->data['custom_fields']['user_avatar']);
	}

	public function delete_authaccount() {
		$strMethod = $this->in->get('lmethod');
		$this->pdh->put('user', 'delete_authaccount', array($this->user->id, $strMethod));
		$this->pdh->process_hook_queue();
		unset($this->user->data['auth_account'][$strMethod]);
		$this->display();
	}

	public function add_authaccount() {
		$strMethod = $this->in->get('lmethod');
		$account = $this->user->handle_login_functions('get_account', $strMethod);
		if ($strMethod && !is_array($account) && $this->pdh->get('user', 'check_auth_account', array($account))){
			$this->pdh->put('user', 'add_authaccount', array($this->user->id, $account, $strMethod));
			$this->pdh->process_hook_queue();
			$this->user->data['auth_account'][$strMethod] = $account;
		} else {
			$this->core->message($this->user->lang('auth_connect_account_error'), $this->user->lang('error'), 'red');
		}
		$this->display();
	}

	public function update() {
		// Error-check the form
		$change_username = ( $this->in->get('username') != $this->user->data['username'] ) ? true : false;
		$change_password = ( $this->in->get('new_password') != '' || $this->in->get('confirm_password') != '') ? true : false;
		$change_email = ( $this->in->get('email_address')  != $this->user->data['user_email']) ? true : false;

		//Check username
		if ($change_username && $this->pdh->get('user', 'check_username', array($this->in->get('username'))) == 'false'){
			$this->core->message($this->user->lang('fv_username_alreadyuse'), $this->user->lang('error'), 'red');
			$this->display();
			return;
		}

		//Check email
		if ($change_email){
			if ($this->pdh->get('user', 'check_email', array($this->in->get('email_address'))) == 'false'){
				$this->core->message($this->user->lang('fv_email_alreadyuse'), $this->user->lang('error'), 'red');
				$this->display();
				return;
			} elseif ( !preg_match("/^([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)+/",$this->in->get('email_address')) ){
					$this->core->message($this->user->lang('fv_invalid_email'), $this->user->lang('error'), 'red');
					$this->display();
					return;
			}
		}
		
		//Check matching new passwords
		if($change_password) {
			if($this->in->get('new_password') != $this->in->get('confirm_password')) {
				$this->core->message($this->user->lang('fv_required_password_repeat'), $this->user->lang('error'), 'red');
				$this->display();
				return;
			}
		}
		
		// If they changed their username or password, we have to confirm their current password
		if ( ($change_username) || ($change_password) || ($change_email)){
			if (!$this->user->checkPassword($this->in->get('current_password'), $this->user->data['user_password'])){
				$this->core->message($this->user->lang('incorrect_password'), $this->user->lang('error'), 'red');
				$this->display();
				return;
			}
		}

		// Errors have been checked at this point, build the query
		$blnResult = $this->pdh->put('user', 'update_user_settings', array($this->user->data['user_id'], $this->get_settingsdata()));
		$this->pdh->process_hook_queue();
		//Only redirect if saving was successfull so we can grad an error message
		if ($blnResult) redirect('settings.php'.$this->SID.'&amp;save=true');
		return;
	}

	public function get_settingsdata() {
		$settingsdata = array();

		//Privacy - Phone numbers
		$priv_phone_array = array(
			'0'=>'user_priv_all',
			'1'=>'user_priv_user',
			'2'=>'user_priv_admin',
			'3'=>'user_priv_no'
		);

		$priv_set_array = array(
			'0'=>'user_priv_all',
			'1'=>'user_priv_user',
			'2'=>'user_priv_admin'
		);

		$gender_array = array(
			'0'=> "---",
			'1'=> 'adduser_gender_m',
			'2'=> 'adduser_gender_f',
		);

		$cfile = $this->root_path.'core/country_states.php';
		if (file_exists($cfile)){
			include($cfile);
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

		$style_array = array();
		foreach($this->pdh->get('styles', 'styles', array(0, false)) as $styleid=>$row){
			$style_array[$styleid] = $row['style_name'];
		}

		$auth_options = $this->user->get_loginmethod_options();
		
		// hack the birthday format, to be sure there is a 4 digit year in it
		$birthday_format = $this->user->style['date_notime_short'];
		if(stripos($birthday_format, 'y') === false) $birthday_format .= 'Y';
		$birthday_format = str_replace('y', 'Y', $birthday_format);
		
		$settingsdata = array(
			'registration_information'	=> array(
				'adduser_tab_registration_information'	=> array(
					'username'	=> array(
						'fieldtype'	=> 'text',
						'name'	=> 'username',
						'help'	=> ($this->config->get('pk_disable_username_change') == 1) ? $this->user->lang('register_help_disabled_username') : '',
						'readonly'	=> ($this->config->get('pk_disable_username_change') == 1) ? true : false,
						'text'	=> '<img id="tick_username" src="'.$this->root_path.'images/register/tick.png" style="display:none;" width="16" height="16" alt="" />',
						'size'		=> 40,
						'required'	=> true,
						'id'	=> 'username',
					),
					'user_email' => array(
						'fieldtype'	=> 'text',
						'name'		=> 'email_address',
						'size'		=> 40,
						'required'	=> true,
						'id'		=> 'useremail',
					),
					'user_password'	=> array(
						'fieldtype'	=> 'password',
						'name'		=> 'current_password',
						'size'		=> 40,
						'help'		=> 'current_password_note',
						'no_value'	=> true,
						'id'		=> 'oldpassword',
						'required'	=> true,
					),
					'new_user_password1' => array(
						'fieldtype'	=> 'password',
						'name'		=> 'new_password',
						'size'		=> 40,
						'id'		=> 'password1',
					),
					'new_user_password2' => array(
						'fieldtype'	=> 'password',
						'name'		=> 'confirm_password',
						'size'		=> 40,
						'help'		=> 'confirm_password_note',
						'id'		=> 'password2',
					),

				),
			),
			'profile'	=> array(
				'adduser_tab_profile'	=> array(
					'first_name'	=> array(
						'fieldtype'	=> 'text',
						'name'		=> 'adduser_first_name',
						'size'		=> 40,
						'id'		=> 'first_name',
					),
					'last_name'	=> array(
						'fieldtype'	=> 'text',
						'name'		=> 'adduser_last_name',
						'size'		=> 40,
					),
					'gender' => array(
						'fieldtype'	=> 'dropdown',
						'name'		=> 'adduser_gender',
						'options'	=> $gender_array,
						'id'		=> 'gender',
					),
					'country' => array(
						'fieldtype'	=> 'dropdown',
						'name'		=> 'adduser_country',
						'options'	=> $country_array,
						'no_lang'	=> true,
						'id'		=> 'country',
					),
					'state' => array(
						'fieldtype'	=> 'text',
						'name'		=> 'adduser_state',
						'options'	=> $country_array,
						'no_lang'	=> true,
					),
					'ZIP_code'	=> array(
						'fieldtype'	=> 'int',
						'size'	=> 5,
						'name'	=> 'adduser_ZIP_code'
					),
					'town'	=> array(
						'fieldtype'	=> 'text',
						'name'	=> 'adduser_town',
						'size'	=> 40,
					),
					'phone'	=> array(
						'fieldtype'	=> 'text',
						'name'	=> 'adduser_phone',
						'size'	=> 40,
						'help'	=> 'adduser_foneinfo',
					),
					'cellphone'	=> array(
						'fieldtype'	=> 'text',
						'name'	=> 'adduser_cellphone',
						'size'	=> 40,
						'help'	=> 'adduser_cellinfo',
					),
					'icq'	=> array(
						'fieldtype'	=> 'text',
						'name'	=> 'adduser_icq',
						'size'	=> 40,
					),
					'skype'	=> array(
						'fieldtype'	=> 'text',
						'name'	=> 'adduser_skype',
						'size'	=> 40,
					),
					'msn'=> array(
						'fieldtype'	=> 'text',
						'name'	=> 'adduser_msn',
						'size'	=> 40,
					),
					'irq'	=> array(
						'fieldtype'	=> 'text',
						'name'	=> 'adduser_irq',
						'size'	=> 40,
						'help'	=> 'register_help_irc',
					),
					'twitter'	=> array(
						'fieldtype'	=> 'text',
						'name'	=> 'adduser_twitter',
						'size'	=> 40,
					),
					'facebook'	=> array(
						'fieldtype'	=> 'text',
						'name'	=> 'adduser_facebook',
						'size'	=> 40,
					),
					'birthday'	=> array(
						'fieldtype'	=> 'datepicker',
						'name'	=> 'adduser_birthday',
						'allow_empty' => true,
						'options' => array(
							'year_range' => '-80:+0',
							'change_fields' => true,
							'format' => $birthday_format
						),
					),
					'user_avatar'	=> array(
						'fieldtype'	=> 'imageuploader',
						'name'		=> 'user_image',
						'imgpath'	=> $this->pfh->FolderPath('user_avatars','eqdkp'),
						'options'	=> array('deletelink'	=> 'settings.php'.$this->SID.'&mode=deleteavatar&link_hash='.$this->CSRFGetToken('mode')),
					),
					'work'	=> array(
						'fieldtype'	=> 'text',
						'name'	=> 'user_work',
						'size'	=> 40,
					),
					'interests'	=> array(
						'fieldtype'	=> 'text',
						'name'	=> 'user_interests',
						'size'	=> 40,
					),
					'hardware'	=> array(
						'fieldtype'	=> 'text',
						'name'	=> 'user_hardware',
						'size'	=> 40,
					),

				),
				'user_priv'	=> array(
					'priv_no_boardemails'	=> array(
						'fieldtype'	=> 'checkbox',
						'default'	=> 0,
						'name'		=> 'user_priv_no_boardemails',
					),
					'priv_set'	=> array(
						'fieldtype'	=> 'dropdown',
						'name'	=> 'user_priv_set_global',
						'options'	=> $priv_set_array,
						'value'	=> '1',
					),
					'priv_phone'	=> array(
						'fieldtype'	=> 'dropdown',
						'name'	=> 'user_priv_tel_all',
						'options'	=> $priv_phone_array,
						'value'	=> '1',
					),
					'priv_nosms'	=> array(
						'fieldtype'	=> 'checkbox',
						'default'	=> 0,
						'name'		=> 'user_priv_tel_sms',
					),
					'priv_bday'	=> array(
						'fieldtype'	=> 'checkbox',
						'default'	=> 0,
						'name'		=> 'user_priv_bday',
					),
					'exchange_key' => array(
						'fieldtype' => 'plaintext',
						'text'		=> $this->user->data['exchange_key'].'<br /><input type="submit" value="'.$this->user->lang('user_create_new appkey').'" name="newexchangekey" class="bi_reload" />',
						'name'		=> 'user_app_key'
					),

				),
			),
			'view_options'		=> array(
				'adduser_tab_view_options'	=> array(
					'user_alimit'	=> array(
						'fieldtype'	=> 'spinner',
						'name'	=> 'adjustments_per_page',
						'size'	=> 5,
						'step'	=> 10,
						'id'	=> 'user_alimit'
					),
					'user_climit'	=> array(
						'fieldtype'	=> 'spinner',
						'name'	=> 'characters_per_page',
						'size'	=> 5,
						'step' => 10,
						'id'	=> 'user_climit'
					),
					'user_elimit'	=> array(
						'fieldtype'	=> 'spinner',
						'name'	=> 'events_per_page',
						'size'	=> 5,
						'step' => 10,
						'id'	=> 'user_elimit'
					),
					'user_ilimit'	=> array(
						'fieldtype'	=> 'spinner',
						'name'	=> 'items_per_page',
						'size'	=> 5,
						'step' => 10,
						'id'	=> 'user_ilimit'
					),
					'user_rlimit'	=> array(
						'fieldtype'	=> 'spinner',
						'name'	=> 'raids_per_page',
						'size'	=> 5,
						'step' => 10,
						'id'	=> 'user_rlimit'
					),
					'user_nlimit'	=> array(
						'fieldtype'	=> 'spinner',
						'name'	=> 'news_per_page',
						'size'	=> 5,
						'id'	=> 'user_nlimit'
					),
					'user_lang'	=> array(
						'fieldtype'	=> 'dropdown',
						'name'	=> 'language',
						'options'	=> $language_array,
						'no_lang' => true
					),
					'user_timezone'	=> array(
						'fieldtype'	=> 'dropdown',
						'name'		=> 'user_timezones',
						'options'	=> $this->time->timezones,
						'value'		=> $this->config->get('timezone'),
					),

					'user_style'	=> array(
						'fieldtype'	=> 'dropdown',
						'name'		=> 'style',
						'options'	=> $style_array,
						'text'		=> ' (<a href="javascript:template_preview()">'.$this->user->lang('preview').'</a>)',
						'no_lang'	=> true,
					),
					'user_date_time'	=> array(
						'fieldtype'	=> 'text',
						'name'	=> 'adduser_date_time',
						'size'	=> 40,
						'help'	=> 'adduser_date_note',
					),
					'user_date_short'	=> array(
						'fieldtype'	=> 'text',
						'name'	=> 'adduser_date_short',
						'size'	=> 40,
						'help'	=> 'adduser_date_note',
					),
					'user_date_long'	=> array(
						'fieldtype'	=> 'text',
						'name'	=> 'adduser_date_long',
						'size'	=> 40,
						'help'	=> 'adduser_date_note',
					),
				),
			),
		);

		//Merge auth-accounts
		foreach($auth_options as $method => $options){
			if (isset($options['connect_accounts']) && $options['connect_accounts']){
				if (isset($this->user->data['auth_account'][$method]) && strlen($this->user->data['auth_account'][$method])){
					$auth_array['registration_information']['auth_accounts'] = array(
						'auth_account_'.$method	=> array(
							'name'	=> ($this->user->lang('login_'.$method)) ? $this->user->lang('login_'.$method) : ucfirst($method),
							'text'	=> $this->user->data['auth_account'][$method].' <a href="settings.php'.$this->SID.'&amp;mode=delauthacc&amp;lmethod='.$method.'&amp;link_hash='.$this->CSRFGetToken('mode').'"><img src="'.$this->root_path.'images/global/delete.png" alt="Delete" /></a>',
							'help'	=> 'auth_accounts_help',
						),
					);
				} else {
					$auth_array['registration_information']['auth_accounts']  = array(
						'auth_account_'.$method	=> array(
							'name'	=> ($this->user->lang('login_'.$method)) ? $this->user->lang('login_'.$method) : ucfirst($method),
							'text'	=> $this->user->handle_login_functions('account_button', $method),
							'help'	=> 'auth_accounts_help',
						),
					);
				}
				$settingsdata = array_merge_recursive($settingsdata, $auth_array);
			}
		}
		return $settingsdata;
	}

	public function display() {
		if ($this->in->exists('save')){
			$this->core->message( $this->user->lang('update_settings_success'),$this->user->lang('save_suc'), 'green');
		}
		
		$settingsdata = $this->get_settingsdata();
		$userdata = array_merge($this->user->data, $this->user->data['privacy_settings'], $this->user->data['custom_fields']);
		
		//Deactive Profilefields synced by Bridge
		$synced_fields = array();
		if ($this->config->get('cmsbridge_active') == 1){
			$synced_fields = array('user_email','username','user_password', 'new_user_password1', 'new_user_password2');

			if ($this->bridge->get_sync_fields()){;
				$synced_fields = array_merge($synced_fields, $this->bridge->get_sync_fields());
			}
		}

		// Output
		foreach($settingsdata as $tabname=>$fieldsetdata){
			$this->tpl->assign_block_vars('tabs', array(
				'NAME'	=> $this->user->lang('adduser_tab_'.$tabname),
				'ID'	=> $tabname)
			);

			foreach($fieldsetdata as $fieldsetname=>$fielddata){

				$this->tpl->assign_block_vars('tabs.fieldset', array(
					'NAME'		=> $this->user->lang($fieldsetname),
					'INFO'		=> ''
				));

				foreach($fielddata as $name=>$confvars){
					$help = '';
					if (in_array($name, $synced_fields) && (int)$this->config->get('cmsbridge_disable_sync') != 1){
						$confvars['readonly'] = true;
						$help = $this->user->lang('adduser_bridge_note');
 					}
					$no_lang = (isset($confvars['no_lang'])) ? true : false;
					$confvars['value'] = (isset($confvars['no_value'])) ? '' : ((isset($userdata[$name]) && $userdata[$name] != "") ? $userdata[$name] : ((isset($confvars['value'])) ? $confvars['value'] : ''));
					$this->tpl->assign_block_vars('tabs.fieldset.field', array(
						'NAME'		=> ((isset($confvars['required'])) ? '* ' : '').(($this->user->lang($confvars['name'])) ? $this->user->lang($confvars['name']) : $confvars['name']),
						'HELP'		=> ((!empty($confvars['help'])) ? $this->user->lang($confvars['help']) : '').' '.$help,
						'FIELD'		=> $this->html->widget($confvars),
						'TEXT'		=> isset($confvars['text']) ? $confvars['text'] : '',
					));
				}
			}
		}

		$this->jquery->Tab_header('usersettings_tabs', true);
		$this->jquery->spinner('#user_alimit, #user_climit, #user_ilimit, #user_rlimit, #user_elimit', array('step'=>10, 'multiselector'=>true));
		$this->jquery->spinner('user_nlimit');
		$this->jquery->Dialog('template_preview', $this->user->lang('template_preview'), array('url'=>$this->root_path."viewnews.php".$this->SID."&style='+ $(\"select[name='user_style'] option:selected\").val()+'", 'width'=>'750', 'height'=>'520', 'modal'=>true));
		$this->tpl->assign_vars(array(
			'S_CURRENT_PASSWORD'			=> true,
			'S_NEW_PASSWORD'				=> true,
			'S_SETTING_ADMIN'				=> false,
			'S_MU_TABLE'					=> false,
			'USERNAME'						=> $this->user->data['username'],

			// Validation
			'AJAXEXTENSION_USER'			=> '&olduser='.$this->user->data['username'],
			'AJAXEXTENSION_MAIL'			=> '&oldmail='.urlencode($this->user->data['user_email']),
		));

		//Generate Plugin-Tabs
		if (is_array($this->pm->get_menus('settings'))){
			foreach ($this->pm->get_menus('settings') as $plugin => $values){
				$name = ($values['name']) ? $values['name'] : $this->user->lang($plugin);
				$icon = ($values['icon']) ? $values['icon'] : $this->root_path.'images/admin/plugin.png';
				unset($values['name'], $values['icon']);
				$this->tpl->assign_block_vars('plugin_settings_row', array(
					'KEY'		=> $plugin,
					'PLUGIN'	=> $name,
					'ICON'		=> $icon,
				));
				$this->tpl->assign_block_vars('plugin_usersettings_div', array(
					'KEY'		=> $plugin,
					'PLUGIN'	=> $name,
				));

				foreach ($values as $key=>$setting){
					$helpstring = ($this->user->lang(@$setting['help'])) ? $this->user->lang(@$setting['help']) : @$setting['help'];
					$help = (isset($setting['help'])) ? " ".$helpstring : '';
					$setting['value']	= $setting['selected'] = @$this->user->data['plugin_settings'][$plugin][$setting['name']];
					$setting['name'] = $plugin.'['.((isset($setting['name'])) ? $setting['name'] : '').']';
					$setting['plugin'] = $plugin;
					$this->tpl->assign_block_vars('plugin_usersettings_div.plugin_usersettings', array(
						'NAME'	=> $this->user->lang($setting['language']),
						'FIELD'	=> $this->html->widget($setting),
						'HELP'	=> $help,
						'S_TH'	=> ($setting['type'] == 'tablehead') ? true : false,
					));
				}
			}
		}

		$this->core->set_vars(array(
			'page_title'	=> $this->user->lang('settings_title'),
			'template_file'	=> 'settings.html',
			'display'		=> true)
		);
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_user_settings', user_settings::__shortcuts());
registry::register('user_settings');
?>