<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2006-2008 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev$
 * 
 * $Id$
 */
 
if ( !defined('EQDKP_INC') )
{
    header('HTTP/1.0 404 Not Found');
    exit;
}
class User extends UserSkel {


    /**
     * Passwort Checker from phpBB 3.0.0
     *  used due some Major Werid Changes handling the  password of phpBB 3.0.0
     *
     * @param string $password
     * @param string $hash
     * @return boolean
     */
 function phpbb_check_hash($password, $hash)
{
	$itoa64 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	if (strlen($hash) == 34)
	{
		return ($this->_hash_crypt_private($password, $hash, $itoa64) === $hash) ? true : false;
	}
	return (md5($password) === $hash) ? true : false;
}

/**
 * Hash Algorithm from phpBB 3.0.0
 * used due some Major Werid Changes handling the password of phpBB 3.0.0
 *
 * @param string $password
 * @param string $setting
 * @param string_type $itoa64
 * @return string
 */
function _hash_crypt_private($password, $setting, &$itoa64)
{
	$output = '*';

	// Check for correct hash
	if (substr($setting, 0, 3) != '$H$')
	{
		return $output;
	}

	$count_log2 = strpos($itoa64, $setting[3]);
	if ($count_log2 < 7 || $count_log2 > 30)
	{
		return $output;
	}
	$count = 1 << $count_log2;
	$salt = substr($setting, 4, 8);
	if (strlen($salt) != 8)
	{
		return $output;
	}

	/**
	* We're kind of forced to use MD5 here since it's the only
	* cryptographic primitive available in all versions of PHP
	* currently in use.  To implement our own low-level crypto
	* in PHP would result in much worse performance and
	* consequently in lower iteration counts and hashes that are
	* quicker to crack (by non-PHP code).
	*/
	if (PHP_VERSION >= 5)
	{
		$hash = md5($salt . $password, true);
		do
		{
			$hash = md5($hash . $password, true);
		}
		while (--$count);
	}
	else
	{
		$hash = pack('H*', md5($salt . $password));
		do
		{
			$hash = pack('H*', md5($hash . $password));
		}
		while (--$count);
	}

	$output = substr($setting, 0, 12);
	$output .= $this->_hash_encode64($hash, 16, $itoa64);

	return $output;
}


/**
 * Encoding 64-Bit Hash Algorithm from phpBB 3.0.0
 * used due some Major Werid Changes handling the password of phpBB 3.0.0
 *
 * @param string $input
 * @param integer $count
 * @param string $itoa64
 * @return string
 */
function _hash_encode64($input, $count, &$itoa64)
{
	$output = '';
	$i = 0;
	do
	{
		$value = ord($input[$i++]);
		$output .= $itoa64[$value & 0x3f];

		if ($i < $count)
		{
			$value |= ord($input[$i]) << 8;
		}

		$output .= $itoa64[($value >> 6) & 0x3f];

		if ($i++ >= $count)
		{
			break;
		}

		if ($i < $count)
		{
			$value |= ord($input[$i]) << 16;
		}

		$output .= $itoa64[($value >> 12) & 0x3f];

		if ($i++ >= $count)
		{
			break;
		}

		$output .= $itoa64[($value >> 18) & 0x3f];
	}
	while ($i < $count);

	return $output;
}

function login($username, $password, $auto_login)
   {
		global $eqdkp, $db, $cms_host, $cms_db, $cms_user, $cms_pass, $cms_tableprefix, $cms_group, $sql_db;
        
		//Create new database connection
		$db_cms = new $sql_db();
		$db_cms->sql_connect($cms_host,$cms_db,$cms_user,$cms_pass,false);
            
			//Check if we can connect to the new sql. If not, deactivate the bridge, so the user isn't locked out.
			if ( !$db_cms->link_id )
			{
				$sql = "Update __plus_config SET config_value = '0' where config_name ='pk_bridge_cms_active'";
				$db->query($sql);
				message_die('CMS Bridge SQL Settings are wrong. CMS-Bridge deactivated. Login with your regular EQDKPlus Admin Login and check your database settings!');
			}
			
			//Store the username and group id
			$a_username = $username;
			$grp_ids = explode(",",$cms_group);
                        
			//Check if there is a member table with the given prefix
			$sql = "SHOW TABLES LIKE '".$cms_tableprefix."users'";
			if ( $db_cms->sql_numrows($db_cms->query($sql)))
			{
				//If there is a member table, get check f their is a user with the given username
				$sql = "SELECT user_id, user_password, user_email, user_inactive_reason FROM ".$cms_tableprefix."users WHERE username_clean='".strtolower($a_username)."'";
				$remote_users_table = $db_cms->query($sql);
				$rut_row = $db_cms->sql_fetchrow($remote_users_table);
			}
			else 
			{
				$sql = "Update __plus_config SET config_value = '0' where config_name ='pk_bridge_cms_active'";
                $db->query($sql);
				message_die('Either the Bridge table prefix is wrong or you connected to the wrong database. CMS-Bridge deactivated. Login with your regular EQDKPlus Admin Login and check your settings!');
			}
			
            //If we found a member with the given username, check all necessary data
			if ( $rut_row )
			{
                //Build an array of all groups of which the user is member
                $sql = "SELECT group_id FROM ".$cms_tableprefix."user_group WHERE user_id='".$rut_row['user_id']."' AND user_pending ='0'";
                $remote_groups_table = $db_cms->query($sql);
                $memgrp = $db_cms->sql_fetchrowset($remote_groups_table);
                $right_group = false;
                foreach($memgrp as $el)
                {
					if (in_array($el['group_id'], $grp_ids))
					{
						$right_group = true;						
					}
				} 
				//Preparation of phpbb3's password and salting ;)
				$passcheck = $this->phpbb_check_hash($password,$rut_row['user_password']);
				
				//Check all inputs against the data in the cms table
				if (  $passcheck && $rut_row['user_inactive_reason'] == '0' && $right_group )
                {
                	//Prepare userdata to be insert into MySQL
					$mail = $db->escape($rut_row['user_email']);
					$pass = $db->escape(md5($password));
					$user = $db->escape($a_username);
                	
					//Check if the user already exists in the eqdkp
					$sql = "SELECT user_id, username, user_password, user_email FROM __users WHERE username='$a_username'";
					$local_users_table = $db->query($sql); 
            		
					if ($lut_row = $db->sql_fetchrow($local_users_table))
					{
						//If we already have a user, update his settings
						$sql = "UPDATE __users SET user_password='$pass', user_email='$mail' WHERE username='$user'";
						$db->query($sql);
						$userid = $lut_row['user_id'];
					}
					else
					{
						//If we need a new user, create him and insert the default data plus the cms data
						$sql = "INSERT into __users SET username='$user', user_password='$pass', user_email='$mail', user_active='1', user_alimit='".$eqdkp->config['default_alimit']."', user_elimit='".$eqdkp->config['default_elimit']."', user_ilimit='".$eqdkp->config['default_ilimit']."', user_nlimit='".$eqdkp->config['default_nlimit']."', user_rlimit='".$eqdkp->config['default_rlimit']."', user_style='".$eqdkp->config['default_style']."', user_lang='".$eqdkp->config['default_lang']."'";
						$db->query($sql);
						$userid = $db->insert_id();
                        
						//Give him the default permissions
						$sql = "SELECT auth_id, auth_default FROM __auth_options ORDER BY auth_id";
						$result = $db->query($sql);
						while ( $row = $db->sql_fetchrow($result) )
						{
							$sql = "INSERT INTO __auth_users (user_id, auth_id, auth_setting) VALUES ('" . $userid . "','" . $row['auth_id'] . "','" . $row['auth_default'] . "')";
							$db->query($sql);
						}
						$db->free_result($result);
					}
					//Clean all MySQL Garbage for performance, set the autologin and create a session
					$db->free_result($local_users_table);
					$db_cms->free_result($remote_users_table);
					$db_cms->free_result($remote_groups_table);
					$auto_login = ( !empty($auto_login) ) ? md5($password) : '';
					return $this->create($userid, $auto_login, true);
				}				
			}
			//Close MySQL connection and jump to EQDKPlus login
			return UserSkel::login($username,$password,$auto_login);
			$db_cms->close_db();
	}
}
?>