<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date: 2008-12-24 16:37:34 +0100 (Mi, 24 Dez 2008) $
 * -----------------------------------------------------------------------
 * @author      $Author: $
 * @copyright   2006-2008 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev: 3522 $
 * 
 * $Id: e107.bridge.php 3522 2008-12-24 15:37:34Z ghoschdi $Id$
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
            $db_cms = new $sql_db();
            $db_cms->sql_connect($cms_host,$cms_db,$cms_user,$cms_pass,false); // Dantenbankverbindung aufbauen zum CMS
            if ( !$db_cms->link_id )
                    {
                    message_die('Could not connect to the database.');
                    }
            $a_username = $username;
            $group_ids = $cms_group;
            $sql = "SELECT user_id, username, user_password, user_email FROM `".$db->dbname."`.__users WHERE username='" . $a_username . "'";
            $local_users_table = $db->query($sql); // Gucken ob ein lokaler Nutzer mit dem Usernamen exisitert
            $sql = "SHOW TABLE STATUS FROM `".$db_cms->dbname."` LIKE '".$cms_tableprefix."users'";
            if ($res = $db_cms->query($sql))
            {
               if ( $row = $db_cms->fetch_record($res) )
            	{
            $sql = "SELECT user_id, user_password, user_email FROM `".$db_cms->dbname."`.".$cms_tableprefix."users WHERE user_name='" . $a_username . "'";

            $remote_users_table = $db_cms->query($sql); // Gucken ob in der CMS datenbank ein nutzer mit dem Namen existiert
			$rut_row = $db_cms->fetch_record($remote_users_table);
            	}
            }
            if ( $rut_row )
            {
                $db_cms->free_result($remote_users_table);
                $db_cms->free_result($remote_groups_table);
					
                $sql = "SELECT user_groups, user_status  FROM `".$db_cms->dbname."`.".$cms_tableprefix."users WHERE user_id='" . $rut_row['user_id'] . "'";
                $remote_groups_table = $db_cms->query($sql); // Berechtigungen aus der CMS Datenbank auslesen ob der Nutzer auf das eqdkp zugreifen darf
                $rgt_row = $db->fetch_record($remote_groups_table);
                $grps = explode('.' , $rgt_row['user_groups']);
                if (in_array($group_ids, $grps)) {
                	$right_group = true;
                	}
                if ( (md5(md5($password)) == $rut_row['user_password']) && $rgt_row['user_status'] == "0" && $right_group) // Gucken ob das eingegebene Paswort mit dem CMS passwort übereinstimmt (das Passwort wird mit dem salt aus der datenbank natürlich gesalzen) und ob er berechtigt ist zuzugreifen
                {

                    if ($lut_row = $db->fetch_record($local_users_table))
                    {
                        $sql = "UPDATE `".$db->dbname."`.__users SET user_password='" . md5($password) ."',user_email='".$rut_row['user_email']."' WHERE username='" . $a_username . "'";
                        $db->query($sql);   // Aktualisieren der Lokalen Informationen
                        $userid = $lut_row['user_id'];
                    }
                    else
                    {
                          $sql = "INSERT into `".$db->dbname."`.__users SET username='".$a_username."', user_password='".md5($password)."', user_email='".$rut_row['user_email']."', user_active='1', user_style='".$eqdkp->config['default_style']."', user_lang='".$eqdkp->config['default_lang']."'";
                        $db->query($sql); // Einfügen der ausgelesen Informationen in die Lokale Datenbank
                        $userid = $db->insert_id();
                        $sql = "SELECT auth_id, auth_default FROM `".$db->dbname."`.__auth_options ORDER BY auth_id";
                        $result = $db->query($sql); // Auslesen der Standardberechtigungen in der Lokalen Datenbank
                        while ( $row = $db->fetch_record($result) )
                        {
                            $sql = "INSERT INTO `".$db->dbname."`.__auth_users (user_id, auth_id, auth_setting) VALUES ('" . $userid . "','" . $row['auth_id'] . "','" . $row['auth_default'] . "')";
                            $db->query($sql);             // Standardrechte Setzen
                        }
                        $db->free_result($result);
                    }
                    $db->free_result($local_users_table);
					$user_pass = md5($password);
					$cookie_value = $rut_row['user_id'].".".$user_pass;
					$cookie_exp =( !empty($auto_login) )  ? time() + 3600 * 24 * 30 : time() + 3600 * 3;
					header("P3P: CP='NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM'");
					setcookie($cms_tableprefix."user", $cookie_value, $cookie_exp, "/", "", "0");
                } else {
                    return false;
                }
            }
             return UserSkel::login($username,$password,$auto_login);
        }
}
?>