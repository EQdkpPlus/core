<?php 
if ( !defined('EQDKP_INC') ){
  header('HTTP/1.0 404 Not Found');exit;
}

if ( !class_exists( "maintenanceuser_crontask" ) ) {
  class maintenanceuser_crontask extends crontask{
		
		public function __construct(){
			$this->defaults['description'] = 'Deleting Maintenance-user';
			$this->defaults['delay'] = false;
    }
		
  	public function run(){
  		global $core, $user, $tpl, $table_prefix, $pcache, $SID, $html, $pdh, $acl, $db, $in;
  		$muser = unserialize(stripslashes($core->config['maintenance_user']));
  		$db->query("DELETE FROM __users WHERE user_id = ".$muser['user_id']);
  
  		$pdh->put('user_groups_users', 'delete_user_from_group', array($muser['user_id'], 2));
  		$core->config_set('maintenance_user', '');				
  	}
  }
}

?>