<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date: 2008-07-29 00:22:12 +0200 (Di, 29 Jul 2008) $
 * -----------------------------------------------------------------------
 * @author      $Author: osr-corgan $
 * @copyright   2006-2008 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev: 2449 $
 * 
 * $Id: vBulletin.bridge.php 2449 2008-07-28 22:22:12Z osr-corgan $
 */
 
if ( !defined('EQDKP_INC') )
{
    header('HTTP/1.0 404 Not Found');
    exit;
}
class User extends UserSkel
{
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
            $db_cms->sql_connect($cms_host,$cms_db,$cms_user,$cms_pass,false); // Dantenbankverbindung aufbauen zum CMS
   
            //Check if we can connect to the new sql. If not, deactivate the bridge, so the user isn't locked out.
			if ( !$db_cms->link_id )
			{
				$sql = "Update __plus_config SET config_value = '0' where config_name ='pk_bridge_cms_active'";
				$db->query($sql);
				message_die('CMS Bridge SQL Settings are wrong. CMS-Bridge deactivated. Login with your regular EQDKPlus Admin Login and check your database settings!');
			}
			
            $a_username = $username;
          	$grp_ids = explode(",",$cms_group);
  			
          	//Check if there is a member table with the given prefix
			$sql = "SHOW TABLES LIKE '".$cms_tableprefix."user'";
			if ( $db_cms->sql_numrows($db_cms->query($sql)))
			{
				//If there is a member table, get check f their is a user with the given username
				$sql = "SELECT userid, password, email, salt FROM ".$cms_tableprefix."user WHERE username='".strtolower($a_username)."'";
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
                $sql = "SELECT usergroupid, membergroupids FROM ".$cms_tableprefix."user WHERE userid='".$rut_row['user_id']."'";
                $remote_groups_table = $db_cms->query($sql);
                $memres = $db_cms->sql_fetchrowset($remote_groups_table);
                $memgrp = $memres['membergroupids'];
                array_push($memgrp, $memres['usergroupid']);
                $right_group = false;
                foreach($memgrp as $el)
                {
					if (in_array($el, $grp_ids))
					{
						$right_group = true;						
					}
				} 

                if ( (md5(md5($password).$rut_row['salt']) == $rut_row['password']))
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