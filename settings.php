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
 * @copyright	2006-2010 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 * 
 * $Id$
 */

define('EQDKP_INC', true);
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');
define('IN_USERSETTINGS', true);

$fv = new Form_Validate;

$logo_upload = new AjaxImageUpload;

$_auioptions = array(
	'filesize'  => '2048576',  // 1 MB
	'maxheight' => '500',
	'maxwidth'  => '500'
);


$mode = ( isset($_GET['mode']) ) ? $in->get('mode') : false;

if ( $user->data['user_id'] == ANONYMOUS )
{
    header('Location: login.php'.$SID);
		die();
}

if($in->get('performupload') != '')
{
	$logo_upload->PerformUpload('user_avatar', 'eqdkp', 'user_avatars',$_auioptions);
	die();
}

if($in->get('deleteavatar') == 'true')
{
	$result = $db->query_first("SELECT custom_fields FROM __users WHERE user_id = '".$db->escape($user->data['user_id'])."'");
	$custom = unserialize($result);
	$pcache->Delete($pcache->FilePath('user_avatars/'.$custom['user_avatar'], 'eqdkp'));
	unset($custom['user_avatar']);
	$db->query("UPDATE __users SET custom_fields = '".$db->escape(serialize($custom))."' WHERE user_id='".$db->escape($user->data['user_id'])."'");
	redirect('settings.php'.$SID);
}

$action = 'account_settings';

if ( isset($_POST['submit']) )
{
    $_POST = htmlspecialchars_array($_POST);

    $action = 'update';

    // Error-check the form
    $change_username = false;
    if ( $_POST['username'] != $user->data['username'] )
    {
		// They changed the username. See if it's already registered
        $sql = "SELECT user_id
                FROM __users
                WHERE username='".$_POST['username']."'";
        if ( $db->num_rows($db->query($sql)) > 0 )
        {
            $fv->errors['username'] = $user->lang['fv_already_registered_username'];
        }
        $change_username = true;
    }

    $change_password = false;
    if ( (!empty($_POST['new_user_password1'])) || (!empty($_POST['new_user_password2'])) )
    {
        $fv->matching_passwords('new_user_password1', 'new_user_password2', $user->lang['fv_match_password']);
        $change_password = true;
    }

    // If they changed their username or password, we have to confirm
    // their current password
    if ( ($change_username) || ($change_password) )
    {
        $sql = "SELECT user_id
                FROM __users
                WHERE user_id='".$user->data['user_id']."'
                AND user_password='".$user->encrypt_password($_POST['user_password'], $user->data['user_salt']).':'.$user->data['user_salt']."'";
        if ( $db->num_rows($db->query($sql)) == 0 )
        {
            $fv->errors['user_password'] = $user->lang['incorrect_password'];
        }
    }

    $fv->is_number(array(
        'user_alimit' => $user->lang['fv_number'],
        'user_elimit' => $user->lang['fv_number'],
        'user_ilimit' => $user->lang['fv_number'],
        'user_nlimit' => $user->lang['fv_number'],
        'user_rlimit' => $user->lang['fv_number'])
    );

    $fv->is_within_range('user_alimit', 1, 9999);
    $fv->is_within_range('user_elimit', 1, 9999);
    $fv->is_within_range('user_ilimit', 1, 9999);
    $fv->is_within_range('user_nlimit', 1, 9999);
    $fv->is_within_range('user_rlimit', 1, 9999);
	
		$fv->is_email_address('user_email', $user->lang['fv_invalid_email']);
    
    $fv->is_filled('first_name', $user->lang['fv_required']);
    $fv->is_filled('gender', $user->lang['fv_required']);
    $fv->is_filled('country', $user->lang['fv_required']);

    if ( $fv->is_error() )
    {
			$action = 'account_settings';
			$user->data['user_alimit']	= $in->get('user_alimit');
			$user->data['user_elimit']	= $in->get('user_elimit');
			$user->data['user_ilimit']	= $in->get('user_ilimit');
			$user->data['user_nlimit']	= $in->get('user_nlimit');
			$user->data['user_rlimit']	= $in->get('user_rlimit');
			$user->data['user_email']		= $in->get('user_email');
			
			$user->data['first_name']		= $in->get('first_name');
			$user->data['gender']				= $in->get('gender');
			$user->data['country']			= $in->get('country');
    }
}

switch ( $action )
{
    //
    // Process the update
    //
    case 'update':
        // Errors have been checked at this point, build the query
        // User settings
        $query_ary = array();
        if ( $change_username )
        {
            $query_ary['username'] = $_POST['username'];
        }
        if ( $change_password )
        {
            $new_salt = $user->generate_salt();
						$query_ary['user_password'] = $user->encrypt_password($_POST['new_user_password1'], $new_salt).':'.$new_salt;
        }

        $query_ary['user_email']				= stripslashes($in->get('user_email'));
        $query_ary['user_alimit'] 			= $in->get('user_alimit');
        $query_ary['user_elimit'] 			= $in->get('user_elimit');
        $query_ary['user_ilimit'] 			= $in->get('user_ilimit');
        $query_ary['user_nlimit'] 			= $in->get('user_nlimit');
        $query_ary['user_rlimit'] 			= $in->get('user_rlimit');
        $query_ary['user_lang'] 				= $in->get('user_lang');
        $query_ary['user_style'] 				= $in->get('user_style');
        $query_ary['user_timezone']			= $in->get('user_timezone');
        
        $query_ary['first_name'] 				= $in->get('first_name');
        $query_ary['last_name']					= $in->get('last_name');
        $query_ary['country']						= $in->get('country');
        $query_ary['town']							= $in->get('town');
        $query_ary['state']							= $in->get('state');
        $query_ary['ZIP_code']					= $in->get('ZIP_code', 0);
        $query_ary['phone']							= $in->get('phone');
        $query_ary['cellphone']					= $in->get('cellphone');
        $query_ary['address']						= $in->get('address');
        $query_ary['allvatar_nick']			= $in->get('allvatar_nick');
        $query_ary['icq']								= $in->get('icq');
        $query_ary['skype']							= $in->get('skype');
        $query_ary['msn']								= $in->get('msn');
        $query_ary['irq']								= $in->get('irq');
        $query_ary['gender']						= $in->get('gender');
        $query_ary['birthday']					= $in->get('birthday');
				$query_ary['user_date_time']		= $in->get('user_date_time', $user->lang['style_time']);
				$query_ary['user_date_short']		= $in->get('user_date_short', $user->lang['style_date_short']);
				$query_ary['user_date_long']		= $in->get('user_date_long', $user->lang['style_date_long']);
				
				$privArray = array();
        $privArray['priv_set']					= $in->get('priv_set');
       	$privArray['priv_phone']				= $in->get('priv_phone');
        $privArray['priv_nosms']				= $in->get('priv_nosms');
				$privArray['priv_bday']					= $in->get('priv_bday');
				$privArray['priv_gallery']			= $in->get('priv_gallery');
        $query_ary['privacy_settings']	= serialize($privArray);
				
				$customArray = array();
				$customArray['user_avatar'] 		= $in->get('user_avatar');
				$customArray['work']						= $in->get('work');
				$customArray['interests'] 			= $in->get('interests');
				$customArray['hardware'] 				= $in->get('hardware');
				$customArray['facebook'] 				= $in->get('facebook');		
				$customArray['twitter'] 				= $in->get('twitter');	
				$customArray['hide_shop'] 			= $in->get('hide_shop');	
				$customArray['hide_mini_games'] = $in->get('hide_mini_games');		
        $query_ary['custom_fields'] 		= serialize($customArray);
				
				$plugin_settings = array();
				if (is_array($pm->get_menus('settings'))){
					foreach ($pm->get_menus('settings') as $plugin => $values){
						foreach ($values as $key=>$setting){
							$name = $setting['name'];
							$setting['name'] = $plugin.':'.$setting['name'];
							$plugin_settings[$plugin][$name] = $html->widget_return($setting);
						}
					
					}
				}

				$query_ary['plugin_settings']	= serialize($plugin_settings);
				
				
				
        $query = $db->build_query('UPDATE', $query_ary);
        $sql = 'UPDATE __users SET ' . $query . " WHERE user_id = '" . $db->escape($user->data['user_id']) . "'";

        if ( !($result = $db->query($sql)) )
        {
					message_die('Could not update user information', '', __FILE__, __LINE__, $sql);
        }



        redirect('settings.php'.$SID.'&save=true');

        break;
    //
    // Display the account settings form
    //
    case 'account_settings':
				$privacy = $user->data['privacy_settings'];
				$custom = $user->data['custom_fields'];
				
				//Privacy - Phone numbers
        $priv_phone_array = array(
						'0'=>$user->lang['user_priv_all'],
						'1'=>$user->lang['user_priv_user'],
						'2'=>$user->lang['user_priv_admin'],
						'3'=>$user->lang['user_priv_no']
				);
				
				$priv_set_array = array(
						'0'=>$user->lang['user_priv_all'],
						'1'=>$user->lang['user_priv_user'],
						'2'=>$user->lang['user_priv_admin']
				);
				
				$gender_array = array(
						'0'=> "---",
						'1'=> $user->lang['adduser_gender_m'],
						'2'=> $user->lang['adduser_gender_f']
				);
				
				$cfile = $eqdkp_root_path.'core/country_states.php';
        if (file_exists($cfile)){
					include_once($cfile);
        }
        
        $language_array = array();
        if($dir = @opendir($eqdkp_root_path . 'language/')){
					while($file = @readdir($dir)){
						if((!is_file($eqdkp_root_path . 'language/' . $file)) && (!is_link($eqdkp_root_path . 'language/' . $file)) && valid_folder($file)){
							$language_array[$file] = ucfirst($file);
						}
					}
        }
				
				$style_array = array();
        $result = $db->query("SELECT style_id, style_name FROM __styles WHERE enabled = '1' ORDER BY style_id DESC");
        while ($row = $db->fetch_record($result)){
        	$style_array[$row['style_id']] = $row['style_name'];
        }
        $db->free_result($result);
				
				$image = ($custom['user_avatar'] != '') ? $pcache->FilePath('user_avatars/'.$custom['user_avatar'], 'eqdkp') : '';
		
				if ($in->get('save') == 'true'){$core->message( $user->lang['update_settings_success'],$user->lang['save_suc'], 'green');}
        $jquery->Tab_header('usersettings_tabs');
        $tpl->assign_vars(array(
            'F_SETTINGS'									=> 'settings.php'.$SID.'&amp;mode=account',

            'S_CURRENT_PASSWORD'					=> true,
            'S_NEW_PASSWORD'							=> true,
            'S_SETTING_ADMIN'							=> false,
            'S_MU_TABLE'									=> false,
            
            'DD_LANGUAGE'									=> $html->DropDown('user_lang', $language_array, $user->data['user_lang']),
            'DD_STYLES'										=> $html->DropDown('user_style',  $style_array, $user->data['user_style']),
            'DD_TIMEZONES'								=> $html->DropDown('user_timezone', $time->timezones, $user->data['user_timezone']),
            'DD_COUNTRIES'								=> $html->DropDown('country',  $country_array, $user->data['country']),
            'DD_GENDER'										=> $html->DropDown('gender',  $gender_array, $user->data['gender']),
						'PRIV_SET_DROPDOWN'						=> $html->DropDown('priv_set',  $priv_set_array, $privacy['priv_set']),
						'PRIV_PHONE_DROPDOWN'					=> $html->DropDown('priv_phone',  $priv_phone_array, $privacy['priv_phone']),
						'IMAGE_UPLOAD'								=> '<input type="hidden" name="user_avatar" id="user_avatar" value="'.$custom['user_avatar'].'">'.$logo_upload->Show('user_avatar', 'settings.php?performupload=true', $image, false),
						'S_IMAGE'											=> ($image != "") ? true: false,
						'S_USERNAME_DISABLED'					=> ($core->config['pk_disable_username_change'] == 1) ? true : false,
						'HELP_USERNAME'								=> ($core->config['pk_disable_username_change'] == 1) ?  $html->HelpTooltip($user->lang['register_help_disabled_username']) : '',
						'IRC_HELP'										=> $html->HelpTooltip($user->lang['register_help_irc']),

            'L_REGISTRATION_INFORMATION'	=> $user->lang['registration_information'],
            'L_REQUIRED_FIELD_NOTE'				=> $user->lang['required_field_note'],
            'L_USERNAME'									=> $user->lang['username'],
            'L_EMAIL_ADDRESS'							=> $user->lang['email_address'],
            'L_CURRENT_PASSWORD'					=> $user->lang['current_password'],
            'L_CURRENT_PASSWORD_NOTE'			=> $user->lang['current_password_note'],
            'L_NEW_PASSWORD'							=> $user->lang['new_password'],
            'L_NEW_PASSWORD_NOTE'					=> $user->lang['new_password_note'],
            'L_CONFIRM_PASSWORD'					=> $user->lang['confirm_password'],
            'L_CONFIRM_PASSWORD_NOTE'			=> $user->lang['confirm_password_note'],
            'L_PREFERENCES'								=> $user->lang['view_options'],
            'L_ADJUSTMENTS_PER_PAGE'			=> $user->lang['adjustments_per_page'],
            'L_EVENTS_PER_PAGE'						=> $user->lang['events_per_page'],
            'L_ITEMS_PER_PAGE'						=> $user->lang['items_per_page'],
            'L_NEWS_PER_PAGE'							=> $user->lang['news_per_page'],
            'L_RAIDS_PER_PAGE'						=> $user->lang['raids_per_page'],
            'L_LANGUAGE'									=> $user->lang['language'],
            'L_STYLE'											=> $user->lang['style'],
            'L_PREVIEW'										=> $user->lang['preview'],
            'L_SUBMIT'										=> $user->lang['submit'],
            'L_RESET'											=> $user->lang['reset'],
            'L_DELETE'										=> $user->lang['delete'],
            'L_TIMEZONES'									=> $user->lang['user_timezones'],
                        
            'L_ADDUSER_FIRST_NAME'				=> $user->lang['adduser_first_name'],
            'L_ADDUSER_LAST_NAME'					=> $user->lang['adduser_last_name'],
            'L_ADDINFOS'									=> $user->lang['adduser_addinfos'],
            'L_ADDUSER_COUNTRY'						=> $user->lang['adduser_country'],
            'L_ADDUSER_TOWN'							=> $user->lang['adduser_town'],
            'L_ADDUSER_STATE'							=> $user->lang['adduser_state'],
            'L_ADDUSER_ZIP_CODE'					=> $user->lang['adduser_ZIP_code'],
            'L_ADDUSER_PHONE'							=> $user->lang['adduser_phone'],
            'L_ADDUSER_CELLPHONE'					=> $user->lang['adduser_cellphone'],
            'L_ADDUSER_FONEINFO'					=> $html->HelpTooltip($user->lang['adduser_foneinfo']),
						'L_ADDUSER_FONEINFO2'					=> $html->HelpTooltip($user->lang['adduser_cellinfo']),
            'L_ADDUSER_ADDRESS'						=> $user->lang['adduser_address'],
            'L_ADDUSER_ALLVATAR_NICK'			=> $user->lang['adduser_allvatar_nick'],
            'L_ADDUSER_ICQ'								=> $user->lang['adduser_icq'],
            'L_ADDUSER_SKYPE'							=> $user->lang['adduser_skype'],
            'L_ADDUSER_MSN'								=> $user->lang['adduser_msn'],
            'L_ADDUSER_IRQ'								=> $user->lang['adduser_irq'],
            'L_ADDUSER_GENDER'						=> $user->lang['adduser_gender'],          
            'L_ADDUSER_GENDER_M'					=> $user->lang['adduser_gender'],
            'L_ADDUSER_GENDER_F'					=> $user->lang['adduser_gender'],            
            'L_ADDUSER_BIRTHDAY'					=> $user->lang['adduser_birthday'],
						'L_HARDWARE'									=> $user->lang['user_hardware'],
						'L_WORK'											=> $user->lang['user_work'],
						'L_INTERESTS'									=> $user->lang['user_interests'],
            'L_USER_IMAGE'								=> $user->lang['user_image'],
						'L_ADDUSER_TWITTER'						=> $user->lang['adduser_twitter'],
						'L_ADDUSER_FACEBOOK'					=> $user->lang['adduser_facebook'],
						'L_DATE_TIME'									=> $user->lang['adduser_date_time'],
						'L_DATE_SHORT'								=> $user->lang['adduser_date_short'],
						'L_DATE_LONG'									=> $user->lang['adduser_date_long'],
						'L_DATE_NOTE'									=> $user->lang['adduser_date_note'],
						
            'USERNAME'										=> $user->data['username'],
            'USER_EMAIL'									=> $user->data['user_email'],
            'USER_ALIMIT'									=> $user->data['user_alimit'],
            'USER_ELIMIT'									=> $user->data['user_elimit'],
            'USER_ILIMIT'									=> $user->data['user_ilimit'],
            'USER_NLIMIT'									=> $user->data['user_nlimit'],
            'USER_RLIMIT'									=> $user->data['user_rlimit'],
						'USER_DATE_TIME'							=> $user->data['user_date_time'],
						'USER_DATE_SHORT'							=> $user->data['user_date_short'],
						'USER_DATE_LONG'							=> $user->data['user_date_long'],
						
						'L_USER_PRIV'									=> $user->lang['user_priv'],
            'L_USER_PRIV_SET'							=> $user->lang['user_priv_set'],
            'L_USER_PRIV_SET_GLOBAL'			=> $user->lang['user_priv_set_global'],
						'L_USER_PRIV_BDAY'						=> $user->lang['user_priv_bday'],
            'L_USER_PRIV_TEL_ALL'					=> $user->lang['user_priv_tel_all'],
            'L_USER_PRIV_TEL_SMS'					=> $user->lang['user_priv_tel_sms'],
            'L_USER_PRIV_GALLERY'					=> $user->lang['user_priv_gallery'],
						'L_HIDE_SHOP'									=> $user->lang['adduser_hide_shop'],
						'L_HIDE_MINI_GAMES'						=> $user->lang['adduser_hide_mini_games'],
						'L_MISC'											=> $user->lang['adduser_misc'],
						
            'FIRST_NAME'									=> stripslashes($user->data['first_name']),
            'LAST_NAME'										=> stripslashes($user->data['last_name']),
            'COUNTRY'											=> $user->data['country'],
            'TOWN'												=> stripslashes($user->data['town']),
            'STATE'												=> stripslashes($user->data['state']),
            'ZIP_CODE'										=> stripslashes($user->data['ZIP_code']),
            'PHONE'												=> stripslashes($user->data['phone']),
            'CELLPHONE'										=> stripslashes($user->data['cellphone']),
            'ADDRESS'											=> stripslashes($user->data['address']),
            'ALLVATAR_NICK'								=> stripslashes($user->data['allvatar_nick']),
            'ICQ'													=> stripslashes($user->data['icq']),
            'SKYPE'												=> stripslashes($user->data['skype']),
            'MSN'													=> stripslashes($user->data['msn']),
            'IRQ'													=> stripslashes($user->data['irq']),
            'GENDER'											=> stripslashes($user->data['gender']),
						'USER_APP_KEY'								=> stripslashes($user->data['app_key']),
						'USER_APP_USE'								=> ($user->data['app_use'] == '1') ? 'checked' : '',
            'BIRTHDAY'										=> $jquery->Calendar('birthday', stripslashes($user->data['birthday']), '', array('change_fields'=>true, 'year_range'=>'-80:+0')),
						'HARDWARE'										=> $custom['hardware'],
						'WORK'												=> $custom['work'],
						'INTERESTS'										=> $custom['interests'],
						'FACEBOOK'										=> $custom['facebook'],
						'TWITTER'											=> $custom['twitter'],
						'HIDE_SHOP'										=> ($custom['hide_shop']==1) ? 'checked' : '' ,
						'HIDE_MINI_GAMES'							=> ($custom['hide_mini_games']==1) ? 'checked' : '' ,
						'IMAGE_DELETE'								=> 'settings.php'.$SID.'&deleteavatar=true',
            
            'REGISTRATION_ERROR_CLASS'		=> ($fv->generate_error('username') || $fv->generate_error('user_password') || $fv->generate_error('new_user_password1') || $fv->generate_error('user_email')) ? ' class="negative"' : '',
						'FV_USERNAME'									=> $fv->generate_error('username'),
            'FV_PASSWORD'									=> $fv->generate_error('user_password'),
            'FV_NEW_PASSWORD'							=> $fv->generate_error('new_user_password1'),
						'FV_USER_EMAIL'								=> $fv->generate_error('user_email'),
						 
						'PREFERENCES_ERROR_CLASS'			=> ($fv->generate_error('user_alimit') || $fv->generate_error('user_elimit') || $fv->generate_error('user_ilimit') || $fv->generate_error('user_nlimit') || $fv->generate_error('user_rlimit')) ? ' class="negative"' : '',
            'FV_USER_ALIMIT'							=> $fv->generate_error('user_alimit'),
            'FV_USER_ELIMIT'							=> $fv->generate_error('user_elimit'),
            'FV_USER_ILIMIT'							=> $fv->generate_error('user_ilimit'),
            'FV_USER_NLIMIT'							=> $fv->generate_error('user_nlimit'),
            'FV_USER_RLIMIT'							=> $fv->generate_error('user_rlimit'),
						
            'ADDINFOS_ERROR_CLASS'				=> ($fv->generate_error('first_name') || $fv->generate_error('gender') || $fv->generate_error('country')) ? ' class="negative"' : '',					
            'FV_FIRST_NAME'								=> $fv->generate_error('first_name'),
            'FV_GENDER'										=> $fv->generate_error('gender'),
            'FV_COUNTRY'									=> $fv->generate_error('country'),
						
						'PRIV_TEL_ALL'								=> ($privacy['priv_tel_all'] ==1) ? 'checked' : '' ,
            'PRIV_TEL_CRIPT'							=> ($privacy['priv_tel_cript']==1) ? 'checked' : '' ,
            'PRIV_NOSMS'									=> ($privacy['priv_nosms']==1) ? 'checked' : '' ,
						'PRIV_GALLERY'								=> ($privacy['priv_gallery']==1) ? 'checked' : '' ,
            'PRIV_BDAY'										=> ($privacy['priv_bday']==1) ? 'checked' : '' ,
            
            )
        );
				
				
				//Generate Plugin-Tabs
				if (is_array($pm->get_menus('settings'))){
					foreach ($pm->get_menus('settings') as $plugin => $values){
						$name = ($values['name']) ? $values['name'] : $user->lang[$plugin];
						$icon = ($values['icon']) ? $values['icon'] : $eqdkp_root_path.'images/admin/plugin.png';
						unset($values['name'], $values['icon']);
						
						$tpl->assign_block_vars('plugin_settings_row', array(
							'KEY'	=> $plugin,
							'PLUGIN'	=> $name,
							'ICON'	=> $icon,
						));
						$tpl->assign_block_vars('plugin_usersettings_div', array(
							'KEY'	=> $plugin,
							'PLUGIN'	=> $name,
						));

						foreach ($values as $key=>$setting){
							$helpstring =	($user->lang[$setting['help']]) ? $user->lang[$setting['help']] : $setting['help'];	    
							$help = (isset($setting['help'])) ? " ".$html->HelpTooltip($helpstring) : '';
							$setting['value']	= $setting['selected'] = $user->data['plugin_settings'][$plugin][$setting['name']];
							$setting['name'] = $plugin.'['.$setting['name'].']';
							
							$tpl->assign_block_vars('plugin_usersettings_div.plugin_usersettings', array(
								'NAME'	=> $user->lang[$setting['language']],
								'FIELD'	=> $html->widget($setting),
								'HELP'	=> $help,
								'S_TH'	=> ($setting['type'] == 'tablehead') ? true : false,
							));
						}				
					}	
				}
				
				
				$core->set_vars(array(
            'page_title'    => $user->lang['settings_title'],
            'template_file' => 'settings.html',
            'display'       => true)
        );

        break;
    
}
?>
