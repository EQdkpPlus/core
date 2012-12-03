<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:	     	http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2009
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2009 sz3
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev$
 * 
 * $Id$
 */

if ( !defined('EQDKP_INC') ){
  header('HTTP/1.0 404 Not Found');exit;
}

class acl extends acl_manager {
  private $user_permissions = array();
	
	//Inits the userpermissions, group-memberships and group-permissions of a user
  private function init_user_permissions($user_id){
  global $db;
    if(!isset($this->user_permissions[$user_id])){
      $this->user_permissions[$user_id] = array();
  		$this->user_group_memberships[$user_id] = array();
			$this->user_group_permissions[$user_id] = array();

			if ( $user_id != ANONYMOUS ){
				
				//First Step: get Group memberships
				$result =  $db->query("SELECT status, group_id FROM __groups_users WHERE user_id='".$user_id."'");				
				while ( $row = $db->fetch_record($result) ){
						$this->user_group_memberships[$user_id][$row['group_id']] = $row['status'];
				}
				$db->free_result($result);
				
				//If user is Superadmin, he has all permissions
				if (isset($this->user_group_memberships[$user_id][2])){
					foreach ($this->get_auth_defaults() as $value => $default){
						$this->user_group_permissions[$user_id][$value] = "Y";
					}
					//If not superadmin: get user- and grouppermissions
				} else {
					//User-Permissions
					$sql = "SELECT ao.auth_value, au.auth_setting
									FROM __auth_users au, __auth_options ao
									WHERE (au.auth_id = ao.auth_id)
									AND (au.user_id='".$user_id."')";
							
					$result = $db->query($sql);
					while ( $row = $db->fetch_record($result) ){
						$this->user_permissions[$user_id][$row['auth_value']] = $row['auth_setting'];
					}
				
					$db->free_result($result);	
	
					//Group-Permissions					
					$result =  $db->query("SELECT ga.auth_setting, ao.auth_value, gu.group_id, gu.status FROM __groups_users gu, __auth_groups ga, __auth_options ao WHERE gu.user_id='".$user_id."' AND ga.group_id = gu.group_id AND ga.auth_id = ao.auth_id");
					
					while ( $row = $db->fetch_record($result) ){
						if ($row['auth_setting'] == "Y"){
							
							$this->user_group_permissions[$user_id][$row['auth_value']] = $row['auth_setting'];
							$this->user_group_memberships[$user_id][$row['group_id']] = $row['status'];
						}
					}
					$db->free_result($result);
				
				}
	 		
			} else { //Permission for ANONYMOUS
			
				$result =  $db->query("SELECT ga.auth_setting, ao.auth_value FROM __auth_groups ga, __auth_options ao WHERE ga.auth_id = ao.auth_id AND ga.group_id = 1");
		  	while ( $row = $db->fetch_record($result) ){
			  	if ($row['auth_setting'] == "Y" && substr($row['auth_value'], 0, 2)!= "a_"){
						$this->user_group_permissions[$user_id][$row['auth_value']] = $row['auth_setting'];
						
			  	}
					$this->user_group_memberships[$user_id][1] = 1;
		  	}
		  	$db->free_result($result);  
	  	}	  
    }
  }

	//Checks if a user has the permission.
  public function check_auth($auth_value, $user_id, $groups = true){
	
		$this->init_user_permissions($user_id);
		$tmp_user_permissions = $this->user_permissions[$user_id];
		
		if ($groups == true){
			foreach($this->user_group_permissions[$user_id] as $key=>$elem){
				if ($elem == "Y"){
					$tmp_user_permissions[$key] = "Y";
				}
			}
		}

    // If auth_value ends with a '_' it's checking for any permissions of that type
    $exact = ( strrpos($auth_value, '_') == (strlen($auth_value) - 1) ) ? false : true;

    foreach ( $tmp_user_permissions as $value => $setting ){
      if ( $exact ){
        if ( ($value == $auth_value) && ($setting == 'Y') ){
          return true;
        }
      } else {
        if ( preg_match('/^('.$auth_value.'.+)$/', $value, $match) ){
          if ( $tmp_user_permissions[$match[1]] == 'Y' ){
            return true;
          }
        }
      }
    }
    return false;
  }
  
	//Returns all groups the user is in
  public function get_user_group_memberships($user_id=0){
    global $db;
    if(!isset($this->user_permissions[$user_id])){
			$this->init_user_permissions($user_id);
    }
		return $this->user_group_memberships[$user_id];
	}
	
	//Checks if a user is in a special group
  public function check_group($group_id, $user_id){
    global $db;
    if(!isset($this->user_permissions[$user_id])){
			$this->init_user_permissions($user_id);
    }
		if (isset($this->user_group_memberships[$user_id][$group_id])){
			return true;
		} else {
			return false;
		}
	}

} //Close class


class acl_manager {
  private $auth_defaults = array();
  private $auth_ids = array();
  private $user_permissions = array();
  private $group_permissions = array();
  
	//Permissions that are only for a superadmin
	private $superadmin_only_permissions = array(
		'a_maintenance'	=> 'a_maintenance',
		'a_logs_del'		=> 'a_logs_del',
		'a_backup'			=> 'a_backup',
		'a_reset'				=> 'a_reset',
		'a_files_man'		=> 'a_files_man',
	);			
	
	//Returns the default permissions
  public function get_auth_defaults($force_requery = false){
  	global $db;
    if(empty($this->auth_defaults) || $force_requery){
      $sql = 'SELECT auth_id, auth_value, auth_default
              FROM __auth_options
              ORDER BY auth_id';
      $result = $db->query($sql);
      while ( $row = $db->fetch_record($result) ) {
          $this->auth_defaults[ $row['auth_value'] ] = $row['auth_default'];
          $this->auth_ids[$row['auth_value']] = $row['auth_id'];
      }
      $db->free_result($result);
    }
    return $this->auth_defaults;
  }
  
	//Return the auth_value for an auth_id
  public function get_auth_id($auth_value){
    if(isset($this->auth_ids[$auth_value])){
      return $this->auth_ids[$auth_value];
    }else{
      $this->get_auth_defaults(true);
      if(isset($this->auth_ids[$auth_value])){
        return $this->auth_ids[$auth_value];
      } 
    }
    return null;
  }
  
	//Return all permissions of a User
  public function get_user_permissions($user_id=0, $groups=true){
    global $db;
    if(!isset($this->user_permissions[$user_id])){
		$this->init_user_permissions($user_id);
    }
	$tmp_user_permissions = $this->user_permissions[$user_id];
	
	if ($groups == true){
		foreach($this->user_group_permissions[$user_id] as $key=>$elem){
			if ($elem == "Y"){
				$tmp_user_permissions[$key] = "Y";
			}
		}
	}
    return $tmp_user_permissions;
  }
  
	//Returns all permissions of a Group
  public function get_group_permissions($group_id=0, $force_requery=false){

    global $db;
		
    if(!isset($this->group_permissions[$group_id]) || $force_requery){
		$this->group_permissions[$group_id] = array();
		//Grant Super-Admins all rights
		if ($group_id == 2){
			$defaults = $this->get_auth_defaults();
			foreach ($defaults as $value => $elem){
				$this->group_permissions[$group_id][$value] = "Y";
			}
		
		} else {

			$sql = "SELECT ao.auth_value, ag.auth_setting
        			FROM __auth_groups ag, __auth_options ao
          		WHERE (ag.auth_id = ao.auth_id) 
            	AND (ag.group_id='".$group_id."')";
				$result = $db->query($sql);

				while ( $row = $db->fetch_record($result) ){
					if ($row['auth_setting'] == 'Y'){
						$this->group_permissions[$group_id][$row['auth_value']] = $row['auth_setting'];
					}
				}
				 $db->free_result($result);
				
		}
	}
    return $this->group_permissions[$group_id];
  }
  
	
  public function update_auth_option($auth_value, $auth_default){
  global $db;
    $sql = "SELECT * FROM __auth_options
            WHERE auth_value='$auth_value'";
    if ( $db->num_rows($db->query($sql)) > 0 ){
      $sql = "UPDATE __auth_users
              SET auth_setting='$auth_default'
                WHERE auth_value='$auth_value'";
    }else{
      $sql = "INSERT INTO __auth_options
              (auth_value, auth_default)
              VALUES ('$auth_value', '$auth_default')";    
    }
    $db->query($sql);
  }
  
  public function del_auth_option($auth_value){
  global $db;
    $sql = "DELETE FROM __auth_options
              WHERE auth_value='".$auth_value."'";
    $db->query($sql);
  }
  
  public function update_user_permissions($permission_array, $user_id=0){
    global $db;
	if ($user_id == 0){$user_id = $user->data['user_id'];}

    $perm_ids = implode("', '", array_keys($permission_array));
    $sql = "DELETE FROM __auth_users WHERE user_id='".$user_id."' AND auth_id IN ('".$perm_ids."')";
	$db->query($sql);
                          
    $sql = "INSERT INTO __auth_users (user_id, auth_id, auth_setting) VALUES ";
                  foreach ($permission_array as $auth_id => $permission) {
                $sql .= "('{$user_id}','{$auth_id}','{$permission}'), ";
                  }
                  $sql = preg_replace('/, $/', '', $sql);		  
	$db->query($sql);

  }
   
	//Returns the permissions that are only for the superadmin
	public function get_superadmin_only_permissions(){
		return $this->superadmin_only_permissions;
	}
	
	public function get_permission_boxes(){
    	global $user, $pm, $db, $pdh, $eqdkp_root_path;
		  $group_permissions = array(
			// Events
			$user->lang['events'] => array(
				array('CBNAME' => 'a_event_add',  'TEXT' => '<img src="'.$eqdkp_root_path.'images/admin/updates.png" alt="'.$user->lang['admin_right_icon_title'].'" title="'.$user->lang['admin_right_icon_title'].'"><b>' . $user->lang['add'] . '</b>'),
				array('CBNAME' => 'a_event_upd',  'TEXT' => '<img src="'.$eqdkp_root_path.'images/admin/updates.png" alt="'.$user->lang['admin_right_icon_title'].'" title="'.$user->lang['admin_right_icon_title'].'"><b>' . $user->lang['update'] . '</b>'),
				array('CBNAME' => 'a_event_del',  'TEXT' => '<img src="'.$eqdkp_root_path.'images/admin/updates.png" alt="'.$user->lang['admin_right_icon_title'].'" title="'.$user->lang['admin_right_icon_title'].'"><b>' . $user->lang['delete'] . '</b>'),
				array('CBNAME' => 'u_event_list', 'TEXT' => $user->lang['list']),
				array('CBNAME' => 'u_event_view', 'TEXT' => $user->lang['view'])
			),
			// Individual adjustments
			$user->lang['individual_adjustments'] => array(
				array('CBNAME' => 'a_indivadj_add', 'TEXT' => '<img src="'.$eqdkp_root_path.'images/admin/updates.png" alt="'.$user->lang['admin_right_icon_title'].'" title="'.$user->lang['admin_right_icon_title'].'"><b>' . $user->lang['add'] . '</b>'),
				array('CBNAME' => 'a_indivadj_upd', 'TEXT' => '<img src="'.$eqdkp_root_path.'images/admin/updates.png" alt="'.$user->lang['admin_right_icon_title'].'" title="'.$user->lang['admin_right_icon_title'].'"><b>' . $user->lang['update'] . '</b>'),
				array('CBNAME' => 'a_indivadj_del', 'TEXT' => '<img src="'.$eqdkp_root_path.'images/admin/updates.png" alt="'.$user->lang['admin_right_icon_title'].'" title="'.$user->lang['admin_right_icon_title'].'"><b>' . $user->lang['delete'] . '</b>')
			),
			// Items
			$user->lang['items'] => array(
				array('CBNAME' => 'a_item_add',  'TEXT' => '<img src="'.$eqdkp_root_path.'images/admin/updates.png" alt="'.$user->lang['admin_right_icon_title'].'" title="'.$user->lang['admin_right_icon_title'].'"><b>' . $user->lang['add'] . '</b>'),
				array('CBNAME' => 'a_item_upd',  'TEXT' => '<img src="'.$eqdkp_root_path.'images/admin/updates.png" alt="'.$user->lang['admin_right_icon_title'].'" title="'.$user->lang['admin_right_icon_title'].'"><b>' . $user->lang['update'] . '</b>'),
				array('CBNAME' => 'a_item_del',  'TEXT' => '<img src="'.$eqdkp_root_path.'images/admin/updates.png" alt="'.$user->lang['admin_right_icon_title'].'" title="'.$user->lang['admin_right_icon_title'].'"><b>' . $user->lang['delete'] . '</b>'),
				array('CBNAME' => 'u_item_list', 'TEXT' => $user->lang['list']),
				array('CBNAME' => 'u_item_view', 'TEXT' => $user->lang['view'])
			),
			// News
			$user->lang['news'] => array(
				array('CBNAME' => 'a_news_add', 'TEXT' => '<img src="'.$eqdkp_root_path.'images/admin/updates.png" alt="'.$user->lang['admin_right_icon_title'].'" title="'.$user->lang['admin_right_icon_title'].'"><b>' . $user->lang['add'] . '</b>'),
				array('CBNAME' => 'a_news_upd', 'TEXT' => '<img src="'.$eqdkp_root_path.'images/admin/updates.png" alt="'.$user->lang['admin_right_icon_title'].'" title="'.$user->lang['admin_right_icon_title'].'"><b>' . $user->lang['update'] . '</b>'),
				array('CBNAME' => 'a_news_del', 'TEXT' => '<img src="'.$eqdkp_root_path.'images/admin/updates.png" alt="'.$user->lang['admin_right_icon_title'].'" title="'.$user->lang['admin_right_icon_title'].'"><b>' . $user->lang['delete'] . '</b>')
			),
			// Raids
			$user->lang['raids'] => array(
				array('CBNAME' => 'a_raid_add',  'TEXT' => '<img src="'.$eqdkp_root_path.'images/admin/updates.png" alt="'.$user->lang['admin_right_icon_title'].'" title="'.$user->lang['admin_right_icon_title'].'"><b>' . $user->lang['add'] . '</b>'),
				array('CBNAME' => 'a_raid_upd',  'TEXT' => '<img src="'.$eqdkp_root_path.'images/admin/updates.png" alt="'.$user->lang['admin_right_icon_title'].'" title="'.$user->lang['admin_right_icon_title'].'"><b>' . $user->lang['update'] . '</b>'),
				array('CBNAME' => 'a_raid_del',  'TEXT' => '<img src="'.$eqdkp_root_path.'images/admin/updates.png" alt="'.$user->lang['admin_right_icon_title'].'" title="'.$user->lang['admin_right_icon_title'].'"><b>' . $user->lang['delete'] . '</b>'),
				array('CBNAME' => 'u_raid_list', 'TEXT' => $user->lang['list']),
				array('CBNAME' => 'u_raid_view', 'TEXT' => $user->lang['view'])
			),

			// Members
			$user->lang['chars'] => array(
				array('CBNAME' => 'a_members_man', 'TEXT' => '<img src="'.$eqdkp_root_path.'images/admin/updates.png" alt="'.$user->lang['admin_right_icon_title'].'" title="'.$user->lang['admin_right_icon_title'].'"><b>' . $user->lang['manage'] . '</b>'),
				array('CBNAME' => 'u_member_list', 'TEXT' => $user->lang['list']),
				array('CBNAME' => 'u_member_view', 'TEXT' => $user->lang['view']),
				
				array('CBNAME' => 'u_member_man',	 'TEXT' => $user->lang['charsmanage']),
				array('CBNAME' => 'u_member_add',  'TEXT' => $user->lang['uc_add_char']),
				array('CBNAME' => 'u_member_conn', 'TEXT' => $user->lang['charconnect']),
				array('CBNAME' => 'u_member_del',  'TEXT' => $user->lang['charsdelete']),
			),
			// Manage
			$user->lang['manage'] => array(
				array('CBNAME' => 'a_config_man',  'TEXT' => '<img src="'.$eqdkp_root_path.'images/admin/updates.png" alt="'.$user->lang['admin_right_icon_title'].'" title="'.$user->lang['admin_right_icon_title'].'"><b>' . $user->lang['configuration'] . '</b>'),
				array('CBNAME' => 'a_plugins_man', 'TEXT' => '<img src="'.$eqdkp_root_path.'images/admin/updates.png" alt="'.$user->lang['admin_right_icon_title'].'" title="'.$user->lang['admin_right_icon_title'].'"><b>' . $user->lang['plugins'] . '</b>'),
				array('CBNAME' => 'a_styles_man',  'TEXT' => '<img src="'.$eqdkp_root_path.'images/admin/updates.png" alt="'.$user->lang['admin_right_icon_title'].'" title="'.$user->lang['admin_right_icon_title'].'"><b>' . $user->lang['styles'] . '</b>'),
				array('CBNAME' => 'a_reset',   'TEXT' => '<img src="'.$eqdkp_root_path.'images/admin/updates.png" alt="'.$user->lang['admin_right_icon_title'].'" title="'.$user->lang['admin_right_icon_title'].'"><b>' . $user->lang['reset'] . '</b>'),
				array('CBNAME' => 'a_maintenance',   'TEXT' => '<img src="'.$eqdkp_root_path.'images/admin/updates.png" alt="'.$user->lang['admin_right_icon_title'].'" title="'.$user->lang['admin_right_icon_title'].'"><b>' . $user->lang['maintenance'] . '</b>'),
				array('CBNAME' => 'a_files_man',   'TEXT' => '<img src="'.$eqdkp_root_path.'images/admin/updates.png" alt="'.$user->lang['manage_files'].'" title="'.$user->lang['admin_right_icon_title'].'"><b>' . $user->lang['manage_files'] . '</b>')
			),
			$user->lang['user'] => array(			
				array('CBNAME' => 'a_users_man',   'TEXT' => '<img src="'.$eqdkp_root_path.'images/admin/updates.png" alt="'.$user->lang['admin_right_icon_title'].'" title="'.$user->lang['admin_right_icon_title'].'"><b>' . $user->lang['manage'] . '</b>'),
				array('CBNAME' => 'a_users_comment_w',   'TEXT' => '<img src="'.$eqdkp_root_path.'images/admin/updates.png" alt="'.$user->lang['admin_right_icon_title'].'" title="'.$user->lang['admin_right_icon_title'].'"><b>' . $user->lang['comments_write'] . '</b>'),
				array('CBNAME' => 'u_users_comment_r',   'TEXT' => $user->lang['comments_read']),
				array('CBNAME' => 'u_userlist',   'TEXT' => $user->lang['view']),				
			),
			
			// Logs
			$user->lang['logs'] => array(
				array('CBNAME' => 'a_logs_view', 'TEXT' => '<img src="'.$eqdkp_root_path.'images/admin/updates.png" alt="'.$user->lang['admin_right_icon_title'].'" title="'.$user->lang['admin_right_icon_title'].'"><b>' . $user->lang['view'] . '</b>'),
				array('CBNAME' => 'a_logs_del', 'TEXT' => '<img src="'.$eqdkp_root_path.'images/admin/updates.png" alt="'.$user->lang['admin_right_icon_title'].'" title="'.$user->lang['admin_right_icon_title'].'"><b>' . $user->lang['delete'] . '</b>')
			),
			// Backup Database
			$user->lang['backup'] => array(
				array('CBNAME' => 'a_backup', 'TEXT' => '<img src="'.$eqdkp_root_path.'images/admin/updates.png" alt="'.$user->lang['admin_right_icon_title'].'" title="'.$user->lang['admin_right_icon_title'].'"><b>' . $user->lang['backup_database'] . '</b>')
			),
			// Infopages
			$user->lang['info'] => array(
				array('CBNAME' => 'a_infopages_man', 'TEXT' => '<img src="'.$eqdkp_root_path.'images/admin/updates.png" alt="'.$user->lang['admin_right_icon_title'].'" title="'.$user->lang['admin_right_icon_title'].'"><b>' . $user->lang['manage'] . '</b>'),
				array('CBNAME' => 'u_infopages_view',   'TEXT' => $user->lang['view']),
			),
			 // SMS
			$user->lang['sms_perm'] => array(
					array('CBNAME' => 'a_sms_send', 'TEXT' => '<img src="'.$eqdkp_root_path.'images/admin/updates.png" alt="'.$user->lang['admin_right_icon_title'].'" title="'.$user->lang['admin_right_icon_title'].'"><b>' . $user->lang['sms_perm2'] . '</b>'),
			),
		  );
		  
		  return $group_permissions;
		}

}    

?>
