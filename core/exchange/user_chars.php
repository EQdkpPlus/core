<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2009
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

if (!defined('EQDKP_INC')){
	die('Do not access this file directly.');
}

if (!class_exists('exchange_user_chars')){
	class exchange_user_chars extends gen_class{
		public static $shortcuts = array('user', 'pdh', 'pex'=>'plus_exchange');
		public $options		= array();

		public function get_user_chars($params, $body){
			if ($this->user->check_auth('u_calendar_view', false)){
				$userid = (intval($params['get']['userid']) > 0) ? intval($params['get']['userid']) : $this->user->data['user_id'];
				//UserChars
				$user_chars = $this->pdh->aget('member', 'name', 0, array($this->pdh->get('member', 'connection_id', array($userid))));
				$mainchar = $this->pdh->get('user', 'mainchar', array($userid));
				$arrRoles = array();
				if (is_array($user_chars)){
					foreach ($user_chars as $key=>$charname){
						$roles = $this->pdh->get('roles', 'memberroles', array($this->pdh->get('member', 'classid', array($key))));
						if (is_array($roles)){
							$arrRoles = array();
							foreach ($roles as $roleid => $rolename){
								$arrRoles['role:'.$roleid] = array(
									'id'	=> $roleid,
									'name'	=> $rolename,
									'default'	=> ((int)$this->pdh->get('member', 'defaultrole', array($key)) == $roleid) ? 1 : 0,
								);
							}
						}

						$arrUserChars['char:'.$key] = array(
							'id'	=> $key,
							'name'	=> unsanitize($charname),
							'main'	=> ($key == $mainchar) ? 1 : 0,
							'class'	=> $this->pdh->get('member', 'classid', array($key)),
							'classname'	=> $this->pdh->get('member', 'classname', array($key)),
							'race'		=> $this->pdh->get('member', 'raceid', array($key)),
							'racename'	=> $this->pdh->get('member', 'racename', array($key)),
							'roles'	=> $arrRoles,
						);
					}
				}
				$out['chars'] = $arrUserChars;
				return $out;
			} else {
				return $this->pex->error('access denied');
			}
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_exchange_user_chars', exchange_user_chars::$shortcuts);
?>