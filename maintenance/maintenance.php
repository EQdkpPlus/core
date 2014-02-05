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
define('MAINTENANCE_MODE',1);
$eqdkp_root_path = './../';
$lite = true;
define('DEBUG', 3);
require_once($eqdkp_root_path.'common.php');

class maintenance_display extends gen_class {
	public static $shortcuts = array(
		'core'	=> array('core', array('maintenance', 'maintenance.html', 'maintenance_message.html')),
	);

	public function __construct() {
		$this->core->page_header();
		// Normal Output
		if($this->in->exists('login') || $this->in->exists('logout')){

			if ( $this->in->exists('login') && ($this->user->data['user_id'] <= 0) ){
				$redirect	= ( $this->in->exists('redirect') ) ? $this->in->get('redirect') : 'index.php'.$this->SID;
				$auto_login	= ( $this->in->get('autologin') ) ? true : false;

				if ( !$this->user->login($this->in->get('username'), $this->in->get('password'), $auto_login) ){
					$this->tpl->assign_vars(array(
						'S_LOGIN_ERROR' => true,
						'L_LOGIN_WARNING' => $this->user->lang('invalid_login_warning')
					));
				}
				
				if ($this->in->exists('redirect')){
					$redirect_url = preg_replace('#^.*?redirect=(.+?)&(.+?)$#', '\\1' . $this->SID . '&\\2', base64_decode($this->in->get('redirect')));
					if (strpos($redirect_url, '?') === false) {
						$redirect_url = $redirect_url.$this->SID;
					} else {
						$redirect_url = str_replace("?&", $this->SID.'&', $redirect_url);
					}
					redirect($redirect_url);
				}

			}elseif ( $this->user->is_signedin() ){
				$this->user->logout();
			}

			if($this->in->get('splash')) redirect('maintenance/task_manager.php'.$this->SID.'&splash=true');
		}

		// Login form
		if(!$this->user->check_auth('a_maintenance', false)){
			$this->tpl->assign_vars(array(
				'S_LOGIN'					=> true,

				'L_LOGIN'					=> $this->user->lang('login'),
				'L_USERNAME'				=> $this->user->lang('username'),
				'L_PASSWORD'				=> $this->user->lang('password'),
				'L_REMEMBER_PASSWORD'		=> $this->user->lang('remember_password'),
				'L_MAINTENANCE_MESSAGE'		=> $this->user->lang('maintenance_message'),
				'S_HIDE_BREADCRUMP'			=> true,
				'S_HIDE_DEBUG'				=> true,
				'S_MMODE_ACTIVE'			=> true,
				'L_ADMIN_LOGIN'				=> $this->user->lang('admin_login'),
				'REASON'					=> ($this->config->get('pk_maintenance_message') != "") ? $this->user->lang('reason').$this->config->get('pk_maintenance_message') : '',

				'ONLOAD'					=> ' onload="javascript:document.post.username.focus()"',
				'SPLASH'					=> $this->in->get('splash'))
			);

		}else{
			if($this->in->get('splash')) redirect('maintenance/task_manager.php'.$this->SID.'&splash=true');
			$redirect_url = ( $this->in->exists('redirect') ) ? preg_replace('#^.*?redirect=(.+?)&(.+?)$#', '\\1' . $this->SID . '&\\2', $this->in->get('redirect')) : 'index.php'.$this->SID;
			redirect($redirect_url);
		}
		$this->core->page_tail();
	}
}
registry::register('maintenance_display');
?>