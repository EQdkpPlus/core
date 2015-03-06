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

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

if(!class_exists('auth')) include_once(registry::get_const('root_path').'core/auth.class.php');

class auth_db extends auth {
	
	public $error = false;
	
	public function pdl_html_format_login($log_entry) {
		return $log_entry['args'][0];
	}
	
	/**
	* Attempt to log in a user
	*
	* @param $strUsername
	* @param $strPassword
	* @param $boolSetAutoLogin Save login in cookie?
	* @param $boolUseHash Use Hash for comparing
	* @return bool
	*/
	public function login($strUsername, $strPassword, $boolSetAutoLogin = false, $boolUseHash = false){
		if(!$this->pdl->type_known("login")) $this->pdl->register_type("login", false, array($this, 'pdl_html_format_login'), array(3,4));
		
		$arrStatus = false;
		$this->error = false;
		
		//Bridge-Login, only if using not a hash
		if ($this->config->get('cmsbridge_active') == 1 && $this->config->get('pk_maintenance_mode') != 1 && $boolUseHash == false){
			$this->pdl->log('login', 'Try Bridge Login');
			$arrStatus = $this->bridge->login($strUsername, $strPassword, $boolSetAutoLogin, false);
		}
		
		//Bridge Login failed, Specific Auth-Method Login
		if (!$arrStatus){
			$this->pdl->log('login', 'Bridge Login failed or Bridge not activated');
			//Login-Method Login like OpenID, Facebook, ...
			if ($this->in->get('lmethod') != ""){
				$this->pdl->log('login', 'Try Auth-Method Login '.$this->in->get('lmethod'));
				$arrAuthObject = $this->get_login_objects($this->in->get('lmethod'));
				if ($arrAuthObject) $arrStatus = $arrAuthObject->login($strUsername, $strPassword, $boolUseHash);
				if ($arrStatus) $this->pdl->log('login', 'Auth-Method Login '.$this->in->get('lmethod').' successful');
			}
			
			//Auth Login, because all other failed
			if (!$arrStatus){
				$this->pdl->log('login', 'Try EQdkp Plus Login');
				$objQuery = $this->db->prepare("SELECT user_id, username, user_password, user_email, user_active, failed_login_attempts, user_login_key
								FROM __users
								WHERE LOWER(username) =?")->execute(clean_username($strUsername));
				
				if($objQuery && $objQuery->numRows){		
					$row = $objQuery->fetchAssoc();
					list($strUserPassword, $strUserSalt) = explode(':', $row['user_password']);
					//If it's an old password without salt or there is a better algorythm
					$blnNeedsUpdate = ($this->checkIfHashNeedsUpdate($strUserPassword) || !$strUserSalt);
					if($blnNeedsUpdate){
					if (((int)$row['user_active'])){
						$this->pdl->log('login', 'EQDKP User needs update');
						if($this->checkPassword($strPassword, $row['user_password'], $boolUseHash)){
							
								$strNewSalt		= $this->generate_salt();
								$strNewPassword	= $this->encrypt_password($strPassword, $strNewSalt);
								
								$this->db->prepare("UPDATE  __users :p WHERE user_id=?")->set(array(
										'user_password' => $strNewPassword.':'.$strNewSalt,
								))->execute($row['user_id']);
																		
								$arrStatus = array(
									'status'			=> 1,
									'user_id'			=> (int)$row['user_id'],
									'password_hash'		=> $strNewPassword,
									'user_login_key'	=> $row['user_login_key'],
								);
							} else {
								$this->pdl->log('login', 'EQDKP Login failed: wrong password');
								$this->error = 'wrong_password';
							}
						} else {
							$this->error = 'user_inactive';
							if ($row['failed_login_attempts'] >= (int)$this->config->get('failed_logins_inactivity') ){
								$this->error = 'user_inactive_failed_logins';
							}
							$this->pdl->log('login', 'EQDKP Login failed: '.$this->error);
						}
						
					}else{
						$strLoginPassword = $this->checkPassword($strPassword, $row['user_password'], $boolUseHash, true);
						if ((int)$row['user_active']){
							if($strLoginPassword){
								$arrStatus = array(
									'status'	=> 1,
									'user_id'	=> (int)$row['user_id'],
									'password_hash'	=> $strLoginPassword,
									'user_login_key' => $row['user_login_key'],
								);
							} else {
								$this->error = 'wrong_password';
								$this->pdl->log('login', 'EQDKP Login failed: '.$this->error);
							}	
						} else {
							$this->error = 'user_inactive';
							if ($row['failed_login_attempts'] >= (int)$this->config->get('failed_logins_inactivity') ){
								$this->error = 'user_inactive_failed_logins';
							}
							$this->pdl->log('login', 'EQDKP Login failed: '.$this->error);
						}
						
					}
				} else {
					$this->error = 'wrong_username';
					$this->pdl->log('login', 'EQDKP Login failed: '.$this->error);
				}
			}

			//If Bridge is active, check if EQdkp User is allowed to login
			if ($arrStatus && $this->config->get('cmsbridge_active') == 1 && (int)$this->config->get('pk_maintenance_mode') != 1){
				
				$this->pdl->log('login', 'Check EQdkp Plus User against Bridge Groups');
				//Only CMS User are allowed to login
				if ((int)$this->config->get('cmsbridge_onlycmsuserlogin')){
					$this->pdl->log('login', 'Only CMS User are allowed to login');
					//check if user is Superadmin, if yes, login
					$blnIsSuperadmin = $this->check_group(2, false, (int)$arrStatus['user_id']);
					
					//try Bridge-Login without passwort
					if (!$blnIsSuperadmin){
						$this->pdl->log('login', 'User ist not Superadmin, check against Bridge Groups');
						$arrStatus = $this->bridge->login($this->pdh->get('user', 'name', array((int)$arrStatus['user_id'])), false, false, $boolUseHash, false, false);
					}

					//deny access if not Superadmin and not in the groups
					if (!$blnIsSuperadmin && !$arrStatus){
						$arrStatus = false;
					}
				} else {
					//Everyone is allowed to login
					$this->pdl->log('login', 'Checks complete, call Bridge SSO if needed');
					//Bridge-Login without password, for settings Single Sign On
					$this->bridge->login($this->pdh->get('user', 'name', array((int)$arrStatus['user_id'])), false, false, $boolUseHash, false, false);
				}
			

			}
		}
		
		//Auth Method After-Login - reading only
		$this->pdl->log('login', 'Possible Intercept by Auth Methods');
		$this->handle_login_functions("after_login", false, array($arrStatus, $strUsername, $strPassword, $boolUseHash, ((isset($arrStatus['autologin'])) ? $arrStatus['autologin'] : $boolSetAutoLogin)));
		
		if (!$arrStatus){
			$this->pdl->log('login', 'User login failed');
			
			$this->db->prepare("UPDATE __sessions SET session_failed_logins = session_failed_logins + 1 WHERE session_id=?")->execute($this->sid);

			$this->data['session_failed_logins']++;
			
			//Failed Login
			if ($this->config->get('pk_maintenance_mode') != 1){ //Only do this if not in MMode
				$userid = $this->pdh->get('user', 'userid', array($strUsername));
				if ($userid != ANONYMOUS && $this->pdh->get('user', 'active', array($userid))){
					$intFailedLogins = $this->pdh->get('user', 'failed_logins', array($userid));
					$intFailedLogins++;
					$this->pdh->put('user', 'update_failed_logins', array($userid, $intFailedLogins));

					//Set him inactive
					if ((int)$this->config->get('failed_logins_inactivity') > 0 && $intFailedLogins == (int)$this->config->get('failed_logins_inactivity')){
						$this->pdh->put('user', 'activate', array($userid, 0));
						
						//Write to admin-Log
						$this->logs->add('action_user_failed_logins', '', $userid, $strUsername, false, '', 1, $userid);
						
						//Send the User an Email with activation link
						$user_key = $this->pdh->put('user', 'create_new_activationkey', array($userid));
						
						// Email them their new key
						$email = registry::register('MyMailer');
						$bodyvars = array(
							'USERNAME'		=> $strUsername,
							'U_ACTIVATE'	=> $this->env->link.$this->controller_path_plain.'Register/Activate/?key=' . $user_key,
						);
						$email->SendMailFromAdmin($this->pdh->get('user', 'email', array($userid)), $this->lang('email_subject_activation_self'), 'user_activation_failed_logins.html', $bodyvars);
					}
					
				}
			}
			
		} else {
			$this->pdl->log('login', 'User successfull authenticated');
			$this->hooks->process('user_login_successful', array('auth_method' => 'db', 'user_id' => $arrStatus['user_id'], 'autologin' => ((isset($arrStatus['autologin'])) ? $arrStatus['autologin'] : $boolSetAutoLogin)));
			//User successfull authenticated - destroy old session and create a new one
			$this->db->prepare("UPDATE __users :p WHERE user_id=?")->set(array('failed_login_attempts' => 0))->execute($arrStatus['user_id']);

			$this->destroy();
			$this->create($arrStatus['user_id'], (isset($arrStatus['user_login_key']) ? $arrStatus['user_login_key'] : ''), ((isset($arrStatus['autologin'])) ? $arrStatus['autologin'] : $boolSetAutoLogin));
			return true;
		}
		return false;
	}
	
	
	/**
	* Autologin
	*
	* @param $arrCookieData The Data ot the Session-Cookies
	* @return bool
	*/
	public function autologin($arrCookieData){
		$intCookieUserID = (isset($arrCookieData['data']['user_id'])) ? intval($arrCookieData['data']['user_id']) : ANONYMOUS;
		$strCookieAutologinKey = (isset($arrCookieData['data']['auto_login_id'])) ? $arrCookieData['data']['auto_login_id'] : '';
		
		if (isset($intCookieUserID) && intval($intCookieUserID) > 0){
			
			$objQuery = $this->db->prepare("SELECT *
								FROM __users
								WHERE user_id = ?")->execute($intCookieUserID);
			
			if ($objQuery && $objQuery->numRows){
				$arrUserResult = $objQuery->fetchAssoc();
				if ($arrUserResult){
					if ($strCookieAutologinKey != "" && strlen($arrUserResult['user_login_key']) && $strCookieAutologinKey===$arrUserResult['user_login_key'] && (int)$arrUserResult['user_active']){
						$this->hooks->process('user_autologin_successful', array('auth_method' => 'db', 'user_data' => $arrUserResult));
						return $arrUserResult;
					}
				}	
			}			
			
		}
		
		return false;
	}
}
?>