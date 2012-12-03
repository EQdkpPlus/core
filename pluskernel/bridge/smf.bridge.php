<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date:  $
 * -----------------------------------------------------------------------
 * @author      $Author:  $
 * @copyright   2006-2008 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev: 516 $
 * 
 * $Id:  $
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
            $sql = "SELECT user_id, username, user_password, user_email FROM `".$db->dbname."`." . USERS_TABLE . " WHERE username='" . $a_username . "'";
            $local_users_table = $db->query($sql); // Gucken ob ein lokaler Nutzer mit dem Usernamen exisitert

            $sql = "SHOW TABLE STATUS FROM `".$db_cms->dbname."` LIKE '".$cms_tableprefix."members'";
            if ($res = $db_cms->query($sql))
            {
                 if ( $row = $db_cms->fetch_record($res) )
            	{
            
            $sql = "SELECT user_id, username, user_password, user_email FROM `".$db->dbname."`." . USERS_TABLE . " WHERE username='" . $a_username . "'";
            $local_users_table = $db->query($sql); // Gucken ob ein lokaler Nutzer mit dem Usernamen exisitert

            $sql = "SELECT ID_MEMBER, passwd , emailAddress , passwordSalt   FROM `".$db_cms->dbname."`.".$cms_tableprefix."members WHERE memberName='" . $a_username . "'";

            $remote_users_table = $db_cms->query($sql); // Gucken ob in der CMS datenbank ein nutzer mit dem Namen existiert
            $rut_row = $db_cms->fetch_record($remote_users_table);
            	}
            }
            if ( $rut_row )
            {
                $db_cms->free_result($remote_users_table);
                $db_cms->free_result($remote_groups_table);
                $grp_ids = explode(",",$group_ids);
                $sql = "SELECT is_activated, additionalGroups, ID_GROUP  FROM `".$db_cms->dbname."`.".$cms_tableprefix."members WHERE ID_MEMBER='" . $rut_row['ID_MEMBER'] . "'";
                $remote_groups_table = $db_cms->query($sql); // Berechtigungen aus der CMS Datenbank auslesen ob der Nutzer auf das eqdkp zugreifen darf
                $rgt_row = $db->fetch_record($remote_groups_table);
                $memgrp = array($rgt_row['ID_GROUP']);
                if($rgt_row['additionalGroups'] != "") {
                        $addGroups = explode(",",$rgt_row['additionalGroups']);
                        $memgrp = array_merge($addGroups,$memgrp);
                }
                $right_group = false;
                foreach($grp_ids as $el) {
                        if (in_array($el, $memgrp)) {
                            $right_group = true;
                        }
                    }
                if ((sha1(strtolower($username) . htmlspecialchars_decode(stripslashes($password))) == $rut_row['passwd']) && ($rgt_row['is_activated'] == "1") && $right_group) // Gucken ob das eingegebene Paswort mit dem CMS passwort übereinstimmt (das Passwort wird mit dem salt aus der datenbank natürlich geszen) und ob er berechtigt ist zuzugreifen
                {
                    if ($lut_row = $db->fetch_record($local_users_table))
                    {
                        $sql = "UPDATE `".$db->dbname."`." . USERS_TABLE . " SET user_password='" . md5($password) ."',user_email='".$rut_row['emailAddress']."' WHERE username='" . $a_username . "'";
                        $db->query($sql);   // Aktualisieren der Lokalen Informationen
                        $userid = $lut_row['user_id'];
                    }
                    else
                    {
                          $sql = "INSERT into `".$db->dbname."`." . USERS_TABLE . " SET username='".$a_username."', user_password='".md5($password)."', user_email='".$rut_row['emailAddress']."', user_active='1', user_style='".$eqdkp->config['default_style']."', user_lang='".$eqdkp->config['default_lang']."'";
                        $db->query($sql); // Einfügen der ausgelesen Informationen in die Lokale Datenbank
                        $userid = $db->insert_id();
                        $sql = "SELECT auth_id, auth_default FROM `".$db->dbname."`." . AUTH_OPTIONS_TABLE . " ORDER BY auth_id";
                        $result = $db->query($sql); // Auslesen der Standardberechtigungen in der Lokalen Datenbank
                        while ( $row = $db->fetch_record($result) )
                        {
                            $sql = "INSERT INTO `".$db->dbname."`." . AUTH_USERS_TABLE . " (user_id, auth_id, auth_setting) VALUES ('" . $userid . "','" . $row['auth_id'] . "','" . $row['auth_default'] . "')";
                            $db->query($sql);             // Standardrechte Setzen
                        }
                        $db->free_result($result);
                    }
                    $db->free_result($local_users_table);
                    if ($auto_login == true) {
                    	$auto_login = ( !empty($auto_login) ) ? md5($password) : '';
                    } else {
                    	$auto_login = md5($password);
                    }
                    $db_cms->close_db();
                    return $this->create($userid, $auto_login, true); // Session erzeugen zum einloggen
                } else {
                    return false;
                }
            }
            return UserSkel::login($username,$password,$auto_login);
        }
}
?>
