<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        2010-10-12
 * -----------------------------------------------------------------------
 * @author      Mindcrime <mindcrime@gab-clan.org>
 * @copyright   2010 Mindcrime - Remy Wetzels
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     
 * 
 * Bridge for ClanSphere CMS: http://www.csphere.eu/
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
   * Attempt to log in a user
   *
   * @param $username
   * @param $password
   * @param $auto_login Save login in cookie?
   * @return bool
   */
   function login($username, $password, $auto_login)
   {
 		global $eqdkp, $db, $cms_host, $cms_db, $cms_user, $cms_pass, $cms_tableprefix, $cms_group,$sql_db;
        
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
				//If there is a member table, check if there is a user with the given username
				$sql = "SELECT users_id, users_delete, users_active, access_id, users_pwd, users_email FROM ".$cms_tableprefix."users WHERE users_nick='$a_username' AND users_delete = 0";
				$remote_users_table = $db_cms->query($sql);
				$rut_row = $db_cms->sql_fetchrow($remote_users_table);
		}
		else 
		{
			$sql = "Update __plus_config SET config_value = '0' where config_name ='pk_bridge_cms_active'";
               $db->query($sql);
			message_die('CMS Bridge table prefix is wrong. CMS-Bridge deactivated. Login with your regular EQDKPlus Admin Login and check your prefix settings!');
		}
		
    // If we found a member with the given username, check all necessary data
		if ( is_array($rut_row) )
		{
			// Get the access_id of the group
			$memgrp = $rut_row['access_id'];
			
			// Check if the users in in one of the allowed groups (for ClanSphere access_id's)
			$right_group = false;
			if (in_array($memgrp, $grp_ids))
			{
				$right_group = true;
			}
			
			// ClanSphere can have either SHA1 or MD5
               
      //Check all inputs against the data in the cms table
      if ( (md5($password) == $rut_row['users_pwd'] || sha1($password) == $rut_row['users_pwd']) && $rut_row['users_active'] == "1" && $right_group) // Check if password matches and the users is active
      {
       	//Prepare userdata to be insert into MySQL
				$mail = $db->escape($rut_row['users_email']);
				$pass = $db->escape(md5($password));
				$user = $db->escape($a_username);  
				
				//Check if the user already exists in the eqdkp
				$sql = "SELECT user_id, username, user_password, user_email FROM __users WHERE username='$a_username'";
				$local_users_table = $db->query($sql); 
				
				if ($lut_row = $db->sql_fetchrow($local_users_table))
				{
					// If we already have a user, update his settings
					$sql = "UPDATE __users SET user_password='$pass',user_email='$mail' WHERE username='$user'";
					$db->query($sql);
					$userid = $lut_row['user_id'];
				}
				else
				{
					// If we need a new user, create him and insert the default data plus the cms data
					$sql = "INSERT into __users SET username='$user', user_password='$pass', user_email='$mail', user_active='1', user_alimit='".$eqdkp->config['default_alimit']."', user_elimit='".$eqdkp->config['default_elimit']."', user_ilimit='".$eqdkp->config['default_ilimit']."', user_nlimit='".$eqdkp->config['default_nlimit']."', user_rlimit='".$eqdkp->config['default_rlimit']."', user_style='".$eqdkp->config['default_style']."', user_lang='".$eqdkp->config['default_lang']."'";
					$db->query($sql);
					$userid = $db->insert_id();
                       
					// Give him the default permissions
					$sql = "SELECT auth_id, auth_default FROM __auth_options ORDER BY auth_id";
					$result = $db->query($sql);
					while ( $row = $db->sql_fetchrow($result) )
					{
						$sql = "INSERT INTO __auth_users (user_id, auth_id, auth_setting) VALUES ('" . $userid . "','" . $row['auth_id'] . "','" . $row['auth_default'] . "')";
						$db->query($sql);
					}
					$db->free_result($result);
				}
				// Clean all MySQL Garbage for performance, set the autologin and create a session
				$db->free_result($local_users_table);
				$db_cms->free_result($remote_users_table);
				$auto_login = ( !empty($auto_login) ) ? md5($password) : '';
				return $this->create($userid, $auto_login, true);
			} 
			else
			{
				// User doesn't have the right permissions or entered wrong data. Reject him
				return false;
			}
		}
		// Close MySQL connection and jump to EQDKPlus login
		$db_cms->close_db();
		return UserSkel::login($username,$password,$auto_login);
	}
} // class User
?>
