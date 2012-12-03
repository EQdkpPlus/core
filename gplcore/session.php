<?php
if ( !defined('EQDKP_INC') )
{
    header('HTTP/1.0 404 Not Found');
    exit;
}

/**
* Session class
*
* Manages sessions; Based on phpBB
*/
class Session
{
    var $sid          = 0;                      // Session ID       @var sid
    var $data         = array();                // Data array       @var data
    var $browser      = '';                     // User agent       @var browser
    var $ip_address   = 0;                      // User IP          @var ip_address
    var $current_page = '';                     // EQdkp Page       @var current_page
    var $request_page = '';  
		 
    function start()
    {
        global $SID, $db, $core;

        $current_time = time();

        $this->ip_address   = ( !empty($_SERVER['REMOTE_ADDR']) )     ? $_SERVER['REMOTE_ADDR']     : $REMOTE_ADDR;
        $this->browser      = ( !empty($_SERVER['HTTP_USER_AGENT']) ) ? $_SERVER['HTTP_USER_AGENT'] : $_ENV['HTTP_USER_AGENT'];
        $this->request_page = ( !empty($_SERVER['REQUEST_URI']) )     ? $_SERVER['REQUEST_URI']     : $_SERVER['SCRIPT_NAME'] . (( isset($_SERVER['QUERY_STRING']) ) ? '?' . $_SERVER['QUERY_STRING'] : '');
				$this->request_page	= str_replace($core->config['server_path'], '', $this->request_page);
        $this->current_page = preg_replace('#^.*?/?.*?/?([a-z\_\-]+?)\.php\?s=.*?(&.*)?$#', '\1\2', $this->request_page);

        // Check for cookie'd session data
        $cookie_data         = array();
        $cookie_data['sid']  = get_cookie('sid');
        $cookie_data['data'] = get_cookie('data');
        $cookie_data['data'] = ( !empty($cookie_data['data']) ) ? unserialize(stripslashes($cookie_data['data'])) : $cookie_data['data'];

        if ( $cookie_data['data'] != '') {
            $session_data = $cookie_data['data'];
            $this->sid    = (isset($cookie_data['sid'])) ? $cookie_data['sid'] : '';
            $SID = '?s=';
        } else {
            $session_data = array();
            $this->sid    = ( isset($_GET['s']) ) ? $_GET['s'] : '';
            $SID = '?s=' . $this->sid;
        }

        if ( (!empty($this->sid)) || ((isset($_GET['s'])) && ($this->sid == $_GET['s'])) )
        {
            $sql = "SELECT u.*, s.*
                    FROM __sessions s, __users u
                    WHERE s.session_id = '" . $this->sid . "'
                    AND u.user_id = s.session_user_id";
            $result = $db->query($sql);

            $this->data = $db->fetch_record($result);
            $db->free_result($result);
						list($this->data['user_password_clean'], $this->data['user_salt']) = explode(':', $this->data['user_password']);

            // Did the session exist in the DB?
            if ( isset($this->data['user_id']) )
            {
                // Validate IP length
                $s_ip = implode('.', array_slice(explode('.', $this->data['session_ip']), 0, 4));
                $u_ip = implode('.', array_slice(explode('.', $this->ip_address),         0, 4));

                if ( $u_ip == $s_ip )
                {
                    // Only update session DB a minute or so after last update or if page changes
                    if ( ($current_time - $this->data['session_current'] > 60) || ($this->data['session_page'] != $this->current_page) )
                    {
                        $sql = "UPDATE __sessions
                                SET session_current = '" . $current_time . "',
                                    session_page = '" . $db->escape($this->current_page) . "'
                                WHERE session_id = '" . $this->sid . "'";
                        $db->query($sql);
                    }
                    return true;
                }
            }
        }

        // If we reach here then no (valid) session exists.  So we'll create a new one,
        // using the cookie user_id if available to pull basic user prefs.
        // Prevent security vulnerability
        if ( (isset($session_data['auto_login_id'])) && (is_bool($session_data['auto_login_id'])) )
        {
           die('Invalid session data.');
        }

        $auto_login = ( @isset($session_data['auto_login_id']) ) ? $session_data['auto_login_id'] : '';
        $user_id    = ( @isset($session_data['user_id']) )       ? intval($session_data['user_id']) : ANONYMOUS;

        return $this->create($user_id, $auto_login);
    }

    function create(&$user_id, &$auto_login, $set_auto_login = false)
    {
        global $SID, $db, $core;

        $session_data = array();
        $current_time = time();

        // Remove old sessions and update user information if necessary.
        if ( $current_time - $core->config['session_cleanup'] > $core->config['session_last_cleanup'] )
        {
            $this->cleanup($current_time);
        }

        // Grab user data
         $sql = "SELECT u.*, s.session_current
                FROM (`".$db->dbname."`.__users u
                LEFT JOIN `".$db->dbname."`.__sessions s
                ON s.session_user_id = u.user_id)
                WHERE u.user_id = '" . $user_id . "'
                ORDER BY s.session_current DESC";


        $result = $db->query($sql);
        $this->data = $db->fetch_record($result);
        $db->free_result($result);
        // Check auto login request to see if it's valid
				list($this->data['user_password_clean'], $this->data['user_salt']) = explode(':', $this->data['user_password']);
				
        if ( empty($this->data) || ($this->data['user_password_clean'] != $auto_login && !$set_auto_login) || !$this->data['user_active'])
        {
            $auto_login = '';
            $this->data['user_id'] = $user_id = ANONYMOUS;
        }

        // Grab the last visit if there's an existing session
        $this->data['session_last_visit'] = ( !empty($this->data['session_current']) ) ? $this->data['session_current'] : (( !empty($this->data['user_lastvisit']) ) ? $this->data['user_lastvisit'] : time());

        // Create or update the session
        $query = $db->build_query('UPDATE', array(
            'session_user_id'    => $user_id,
            'session_last_visit' => $this->data['session_last_visit'],
            'session_start'      => $current_time,
            'session_current'    => $current_time,
						'session_browser'		 => $this->browser,
            'session_page'       => $db->escape($this->current_page))
        );
        $sql = 'UPDATE __sessions SET ' . $query . " WHERE session_id='" . $this->sid . "'";
        if ( ($this->sid == '') || (!$db->query($sql)) || (!$db->affected_rows()) )
        {
            $this->sid = md5(uniqid($this->ip_address));

            $query = $db->build_query('INSERT', array(
                'session_id'         => $this->sid,
                'session_user_id'    => $user_id,
                'session_last_visit' => $this->data['session_last_visit'],
                'session_start'      => $current_time,
                'session_current'    => $current_time,
                'session_ip'         => $this->ip_address,
								'session_browser'		 => $this->browser,
                'session_page'       => $db->escape($this->current_page))
            );
            $db->query('INSERT INTO `'.$db->dbname.'`.__sessions' . $query);
        }

        $this->data['session_id'] = $this->sid;

        $session_data['auto_login_id'] = ( ($auto_login) && ($user_id != ANONYMOUS) )? $auto_login : '';
        $session_data['user_id'] = $user_id;

        set_cookie('data', serialize($session_data), $current_time + 31536000);
        set_cookie('sid', $this->sid, 0);
        $SID = '?s=' . (( !isset($_COOKIE['sid']) ) ? $this->sid : '');
        return true;
    }

    function destroy()
    {
        global $SID, $db, $core;

        $current_time = time();

        set_cookie('data', '0', -1);
        set_cookie('sid',  '0', -1);
        $SID = '?s=';

        // Delete existing session
        $sql = "UPDATE __users
                SET user_lastvisit='" . intval($this->data['session_current']) . "'
                WHERE user_id='" . $this->data['user_id'] . "'";
        $db->query($sql);

        $sql = "DELETE FROM __sessions
                WHERE session_id='" . $this->sid . "'
                AND session_user_id='" . $this->data['user_id'] . "'";
        $db->query($sql);

        $this->sid = '';

        return true;
    }
		
		function destroy_sid($sid){
				global $SID, $db, $core, $user;
					
					$sql = "DELETE FROM __sessions
									WHERE session_id='" . $db->escape($sid) . "'
									AND session_ip='" . $db->escape($this->ip_address) . "'";
					$db->query($sql);
					$this->destroy();
				
				return true;
		}


    function cleanup(&$current_time)
    {
        global $db, $core;

        // Get expired sessions, only most recent for each user
        $sql = 'SELECT session_user_id, session_page, MAX(session_current) AS recent_time
                FROM __sessions
                WHERE session_current < ' . ($current_time - $core->config['session_length']) . '
                GROUP BY session_user_id, session_page';
        $result = $db->query($sql);

        $del_user_id  = '';
        $del_sessions = 0;
        if ( $row = $db->fetch_record($result) )
        {
            do
            {
                if ( intval($row['session_user_id']) != ANONYMOUS )
                {
                    $sql = "UPDATE __users
                            SET user_lastvisit='" . $row['recent_time'] . "', user_lastpage='" . $db->escape($row['session_page']) . "'
                            WHERE user_id = '" . $row['session_user_id'] . "'";
                    $db->query($sql);
                }

                $del_user_id .= ( ($del_user_id != '') ? ', ' : '') . " '" . $row['session_user_id'] . "'";
                $del_sessions++;
            }
            while ( $row = $db->fetch_record($result) );
        }

        if ( $del_user_id != '' )
        {
            // Delete expired sessions
            $sql = "DELETE FROM __sessions
                    WHERE session_user_id IN ($del_user_id)
                    AND session_current < " . ($current_time - $core->config['session_length']);
            $db->query($sql);
        }

        if ( $del_sessions < 5 )
        {
            // Less than 5 sessions, update gc timer
            // Otherwise we want cleanup called again to delete other sessions
            $core->config_set(array('session_last_cleanup'=>$current_time));
        }
    }


}

/**
* User Class
*
* Stores user/global preferences
* and language data
*/

class UserSkel extends Session
{
    var $lang      = array();               // Loaded language pack     @var lang
    var $lang_name = '';                    // Pack name (ie 'English') @var lang_name
    var $lang_path = '';                    // Language path            @var lang_path
    var $style     = array();               // Style data               @var style

    /**
    * Sets up user language and style settings
    *
    * @param $lang_set Language to set
    * @param $style Style ID to set
    */
    function setup($lang_set = false, $style = false)
    {
        global $db, $core, $eqdkp_root_path, $tpl;
				
				if (!$lang_set) {
					// Set up language array
					if ( (isset($this->data['user_id'])) && ($this->data['user_id'] != ANONYMOUS) && (!empty($this->data['user_lang'])) )
					{
							$this->lang_name = ( file_exists($eqdkp_root_path . 'language/' . $this->data['user_lang']) ) ? $this->data['user_lang'] : $core->config['default_lang'];
							$this->lang_path = $eqdkp_root_path . 'language/' . $this->lang_name . '/';
					}
					else
					{
							$this->lang_name = $core->config['default_lang'];
							$this->lang_path = $eqdkp_root_path . 'language/' . $this->lang_name . '/';
					}
				} else {
					$this->lang_name = ( file_exists($eqdkp_root_path . 'language/' . $lang_set) ) ? $lang_set : $core->config['default_lang'];
						$this->lang_path = $eqdkp_root_path . 'language/' . $this->lang_name . '/';
						$this->data['user_lang'] = ( file_exists($eqdkp_root_path . 'language/' . $lang_set) ) ? $lang_set : $this->data['user_lang'];
				}
				
				//Unserialize custom-fields and privacy-settings
				$this->data['privacy'] = unserialize($this->data['privacy_settings']);
				$this->data['custom_fields'] = unserialize($this->data['custom_fields']);	
				$this->data['plugin_settings'] = unserialize($this->data['plugin_settings']);	
        include($this->lang_path . 'lang_main.php');
        if ( defined('IN_ADMIN') )
        {
            include($this->lang_path . 'lang_admin.php');
        }

        $this->lang = &$lang;

        // Set up style
        //$style = ( $style ) ? $style : ( ($this->data['user_id'] != ANONYMOUS) ? $this->data['user_style'] : $core->config['default_style']);
        //$style = ($core->config['default_style_overwrite'] == '1') ? $core->config['default_style'] : $style ;
        if($style && !is_numeric($style)){
						$style = $db->query_first("SELECT style_id FROM __styles WHERE style_name = '".$db->escape($style)."'");
				}

        if(!$style){

					
          if($core->config['default_style_overwrite'] == '1'){
            $style = $core->config['default_style'];
          }else{
            $style = ($this->data['user_id'] != ANONYMOUS) ? $this->data['user_style'] : $core->config['default_style'];
          }
        }
        
        $sql = 'SELECT *
                FROM __styles
                WHERE style_id='.$style;
        $result = $db->query($sql);

        if ( !($this->style = $db->fetch_record($result)) )
        {
            // If we STILL can't get style information, go back to the default
            // Fail-safe in case someone (ahem) forgets to add style config settings

            // NOTE: This was mostly only an issue during development before the
            // manage_styles panel was developed, but can remain here as a fail-safe
            $sql = 'SELECT *
                    FROM __styles
                    WHERE style_id='.$core->config['default_style'];
            $result = $db->query($sql);
            $this->style = $db->fetch_record($result);
        }
        #$user->style['date_notime_long']


        if ($this->lang_name=='german')
        {
        	setlocale(LC_ALL, 'de_DE@euro', 'de_DE', 'deu_deu');
        	
        }
				$this->style['date_notime_long'] = ($this->data['user_date_long']) ? $this->data['user_date_long'] : $this->lang['style_date_long'];
				$this->style['date_notime_short'] = ($this->data['user_date_short']) ? $this->data['user_date_short'] : $this->lang['style_date_short'];
				$this->style['time'] = ($this->data['user_date_time']) ? $this->data['user_date_time'] : $this->lang['style_time'];
				$this->style['date_time']			= $this->style['date_notime_short'].' '.$this->style['time'];
				$this->style['date']		= 'l, '.$this->style['date_notime_long'];
				$this->style['date_short']  = 'D '.$this->style['date_notime_short'].' '.$this->style['time'];

        $tpl->set_template($this->style['style_code'], $this->style['template_path']);

        // Default the limits if the user's anonymous
        if ( $this->data['user_id'] == ANONYMOUS )
        {
            $this->data['user_alimit'] = $core->config['default_alimit'];
            $this->data['user_elimit'] = $core->config['default_elimit'];
            $this->data['user_ilimit'] = $core->config['default_ilimit'];
            $this->data['user_nlimit'] = $core->config['default_nlimit'];
            $this->data['user_rlimit'] = $core->config['default_rlimit'];
						$this->style['date_notime_long'] = ($core->config['default_date_long']) ? $core->config['default_date_long'] : $this->lang['style_date_long'];
						$this->style['date_notime_short'] = ($core->config['default_date_short']) ? $core->config['default_date_short'] : $this->lang['style_date_short'];
						$this->style['time'] 			= ($core->config['default_date_time']) ? $core->config['default_date_time'] : $this->lang['style_time'];
						
						$this->style['date_time']	= $this->style['date_notime_short'].' '.$this->style['time'];
						$this->style['date']			= 'l, '.$this->style['date_notime_long'];
						$this->style['date_short']= 'D '.$this->style['date_notime_short'].' '.$this->style['time'];
						
						$this->data['custom_fields']['hide_shop'] = $core->config['pk_hide_shop'];
        }
        
        return;
    }
		
		/**
    * Checks if the eqdkp runs in the easy mode...
    *
    * @param $die					If they don't have permission, exit with message_die or just return false?
    * @return bool
    */
		function check_hostmode($die = true){
			global $_HMODE;
			if(!$_HMODE){
        return true;
      }else{
        return ( $die ) ? message_die($this->lang['noauth_hostmode'], $this->lang['noauth_default_title'], '', '', '', false, true) : false;
      }
		}
		
    /**
    * Checks if a user has permission to do ($auth_value)
    *
    * @param $auth_value	Permission we want to check
    * @param $die					If they don't have permission, exit with message_die or just return false?
    * @param $user_id			If set, checks $user_id's permission instead of $this->data['user_id']
	  * @param $groups			Groups
    * @return bool
    */
    function check_auth($auth_value, $die = true, $user_id = 0, $groups = true){
    global $acl;
      if($user_id == 0){
        $user_id = $this->data['user_id'];
      }

      $auth_result = $acl->check_auth($auth_value, $user_id, $groups);
      
      if($auth_result){
        return true;
      }else{
        $index = ( isset($this->lang['noauth_'.$auth_value]) ) ? 'noauth_'.$auth_value : 'noauth_default_title';
        return ( $die ) ? message_die($this->lang[$index], $this->lang['noauth_default_title'], '', '', '', false, true) : false;
      }
    }
		
		/**
    * Checks if a user is a member of the group
    *
    * @param $group_id		Group we want to check
    * @param $die					If the user is not member of the group, exit with message_die or just return false?
    * @param $user_id			If set, checks $user_id's permission instead of $this->data['user_id']
    * @return bool
    */
		function check_group($group_id, $die = true, $user_id = 0){
    	global $acl;
      if($user_id == 0){
        $user_id = $this->data['user_id'];
      }

      $result = $acl->check_group($group_id, $user_id);
      
      if($result){
        return true;
      }else{
        $index = ( isset($this->lang['noauth_'.$auth_value]) ) ? 'noauth_'.$auth_value : 'noauth_default_title';
        return ( $die ) ? message_die($this->lang[$index], $this->lang['noauth_default_title'], '', '', '', false, true) : false;
      }
    }
		


    /**
    * Attempt to log in a user
    *
    * @param $username
    * @param $password
    * @param $auto_login Save login in cookie?
    * @return bool
    */
   function login($username, $password, $auto_login, $use_hash = false)
    {
        global $user, $db, $core;

        $sql = "SELECT user_id, username, user_password, user_email, user_active
                FROM __users
                WHERE username='" . $db->escape($username) . "'";

        $result = $db->query($sql);
        $row = $db->fetch_record($result);
         if ( $row )
        {
            $db->free_result($result);
						
						list($user_password, $user_salt) = explode(':', $row['user_password']);
						
						if (!$user_salt) {

							if (md5($password) == $user_password && ($row['user_active'])){
								$new_salt = $this->generate_salt();						
								$new_password = $this->encrypt_password($password, $new_salt);
								$db->query("UPDATE `".$db->dbname."`.__users SET user_password='".$new_password.':'.$new_salt."' WHERE username='".$db->escape($username)."'");
								$auto_login = ( !empty($auto_login) ) ? $new_password : '';
								return $this->create($row['user_id'], $auto_login, true);
								
							} else {
								return false;
							}
							
						}else {	
							$login_password = ($use_hash) ? $password : $this->encrypt_password($password, $user_salt);
		
							if ( $login_password == $user_password && ($row['user_active']) )
							{
									$auto_login = ( !empty($auto_login) ) ? $this->encrypt_password($password, $user_salt) : '';
	
									return $this->create($row['user_id'], $auto_login, true);
							}
						}
				
            
        }

        return false;
    }
		
		
		/**
    * Checks if a session is valid and returns the user_id
    *
    * @param $sid						Session-ID
    * @return $user_id			Returns the User-ID
    */
		function check_session($sid){
			global $user, $acl, $db;
			$sql = "SELECT u.*, s.*
												FROM __sessions s, __users  u
												WHERE s.session_id = '".$db->escape($sid)."'
												AND u.user_id = s.session_user_id";
			$result = $db->query($sql);
		
			$data = $db->fetch_record($result);

			$db->free_result($result);

			// Did the session exist in the DB?
			if ( isset($data['user_id']) )
			{				
					// Validate IP
					if ( $data['session_ip'] == $this->ip_address )
					{
							return $data['user_id'];
					}
			}
			
			return ANONYMOUS;
		}

    /**
     * Static function to abstract password encryption
     *
     * @param string $string String to encrypt
     * @param string $salt Salt value; not yet in use
     * @return string
     * @static
     */
    function encrypt_password($string, $salt = '')
    {
        return hash('sha256', $salt . $string);
    }
		
		function generate_salt()
		{
			return substr(md5(uniqid('', true)), 0, 23);		 
		}
}
?>
