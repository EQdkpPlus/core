<?php

if ( !defined('EQDKP_INC') )
{
    die('You cannot access this file directly.');
}

class ticket_plugin_class extends EQdkp_Plugin
{
    function ticket_plugin_class($pm)
    {
        global $eqdkp_root_path, $user, $SID, $eqdkp;

        $this->eqdkp_plugin($pm);
        $this->pm->get_language_pack('ticket');

        $this->add_data(array(
            'name'          => 'Ticket - Conversation',
            'code'          => 'ticket',
            'path'          => 'ticket',
            'contact'       => 'Achaz@lionforge.de',
            'template_path' => 'plugins/ticket/templates/',
            'version'       => '0.06')
        );


         // Register our permissions
             $this->add_permission('1100', 'a_ticket_admin', 'N', $user->lang['ticket_admin']);
             $this->add_permission('1101', 'u_ticket_submit', 'N', $user->lang['ticket_submit']);


        // Define (un)installation
        // -----------------------------------------------------
        // Thanks to Raidplanner for this code.
		$steps=5;
		for ($i = 1; $i <= $steps; $i++)
		{
			$this->add_sql(SQL_INSTALL, $this->create_ticket_tables("step".$i."b"));
		    $this->add_sql(SQL_UNINSTALL, $this->create_ticket_tables("step".$i."a"));
		}
        //Fill config table
	$this->InsertIntoConfTable('ticket_email', '1');
	$this->InsertIntoConfTable('ticket_admin', '1');
	$this->InsertIntoConfTable('ticket_admincolor', '#CC0000');
    $this->InsertIntoConfTable('ticket_default_user_color', '#FF0000');
	$this->InsertIntoEmailTable('1', $eqdkp->config['admin_email']);
 
        //generate menus
      $this->add_menu('main_menu2', $this->gen_main_menu());
        $this->add_menu('settings', $this->gen_settings_menu());
        //generate admin menu
             $this->add_menu('admin_menu', $this->gen_admin_menu());

    }

    /**
	* Get SQL to create the ts tables
	*
	* @return string Table creation SQL
	*/
	function create_ticket_tables($step)
	{
		global $table_prefix, $eqdkp;
		$sql = "";

		switch ($step)
		{
			case "step1a":
				$sql = "DROP TABLE IF EXISTS " . $table_prefix . "ticket_tickets";
				break;
			case "step1b":
				$sql = "CREATE TABLE IF NOT EXISTS " . $table_prefix . "ticket_tickets (
				    ticket_id int(11) NOT NULL auto_increment,
				    session_id int(11) DEFAULT '0',
				    user_id int(11) NOT NULL,
                    		    message_date int(11),
				    message text NOT NULL,
                    		    replied ENUM( '0','1') DEFAULT '0',
				    firstreplydate int(11) NOT NULL,
				    deletion int(5) DEFAULT '0',
				    PRIMARY KEY  (ticket_id))";
				break;
			case "step2a":
				$sql = "DROP TABLE IF EXISTS " . $table_prefix . "ticket_replied";
				break;
			case "step2b":
				$sql = "CREATE TABLE IF NOT EXISTS " . $table_prefix . "ticket_replied (
					reply_id int(11) NOT NULL auto_increment,
					replied_by_id int(11) NOT NULL,
                    reply_date int(11),
					ticket_id int(11) NOT NULL,
					ticketsuser_id int(11) NOT NULL,
					message text NOT NULL,
					userviewed ENUM( '0','1') DEFAULT '0',
					userviewdate int(11) NOT NULL,
					PRIMARY KEY  (reply_id))";
				break;
			case "step3a":
				$sql = "DROP TABLE IF EXISTS " . $table_prefix . "ticket_config";
				break;
			case "step3b":
				$sql = "CREATE TABLE IF NOT EXISTS " . $table_prefix . "ticket_config (
					config_name varchar(255) NOT NULL default '',
					config_value varchar(255) default NULL,
					PRIMARY KEY (config_name))";
				break;	
			case "step4a":
				$sql = "DROP TABLE IF EXISTS " . $table_prefix . "ticket_adminemail";
				break;
			case "step4b":
				$sql = "CREATE TABLE IF NOT EXISTS " . $table_prefix . "ticket_adminemail (
					email_id int(11) NOT NULL auto_increment,
					user_id int(11) NOT NULL,
					email_address varchar(100) NULL default 'NULL',
					PRIMARY KEY (email_id))";
				break;	
           case "step5a":
				$sql = "DROP TABLE IF EXISTS " . $table_prefix . "ticket_userconfig";
				break;
			case "step5b":
				$sql = "CREATE TABLE IF NOT EXISTS " . $table_prefix . "ticket_userconfig (
					user_id int(11) NOT NULL,
					email ENUM( '0','1') DEFAULT '1',
					color varchar(7) default 'NULL',
					PRIMARY KEY (user_id))";
				break;
			}
		return $sql;
	}

    function gen_main_menu()
    {
        if ( $this->pm->check(PLUGIN_INSTALLED, 'ticket') )
        {
            global $table_prefix;
            global $db, $eqdkp, $user, $tpl, $pm;
            global $SID;

             //get Config for Default colors
             $sql = 'SELECT * FROM ' . $table_prefix.'ticket_config';
             $settings_result = $db->query($sql);
             while($roww = $db->fetch_record($settings_result)) {
//             $usercolor = $roww['config_value'];
             $conf[$roww['config_name']] = $roww['config_value'];
             }
             $usercolor=$conf['ticket_default_user_color'];
             
             //Find user-specified color
            $user_set = $db->query_first('SELECT count(*) FROM ' . $table_prefix.'ticket_userconfig WHERE user_id = ' . $user->data['user_id']);
            if($user_set != 0){
                $sql = 'SELECT color FROM ' . $table_prefix . 'ticket_userconfig WHERE user_id =' .  $user->data['user_id'];
                if (!($result= $db->query($sql))) { $usercolor=$conf['ticket_default_user_color'];}
                else {
                    $usercolorarray = $db->fetch_record($result);
                    $usercolor = $usercolorarray['color'];
                }
            }

            $user_replies_new = $db->query_first('SELECT count(*) FROM ' . $table_prefix .'ticket_replied  WHERE ticketsuser_id = ' . $user->data['user_id']. " AND userviewed = '0'");

            //new button
            $admintext = '';
            $pretext = '';
            $posttext = '';

            if($user->check_auth('a_ticket_admin',false)){

                $admin_new_tickets = $db->query_first('SELECT count(*) FROM ' . $table_prefix ."ticket_tickets WHERE replied = '0'");
                if($admin_new_tickets != 0){
                                $admintext .= '('.$admin_new_tickets.') <span style="color:'.$conf['ticket_admincolor'].'"><b>Admin: '.$user->lang['ticket_open'].'</b></span>';
                } else {
                                $admintext .= 'Admin: '.$user->lang['ticket'];
                }
            }

            if($user_replies_new !=0){
                $pretext .= '<span style="color:'.$usercolor.'"><b>';
                $posttext .= '</b></span> ('.$user_replies_new.')';
            }

            $main_menu = array(
                array('link' => 'plugins/' . $this->get_data('path') . '/index.php'  . $SID, 'text' => ''.$pretext.$user->lang['ticket'].$posttext.'', 'check' => 'u_ticket_submit'),
                array('link' => 'plugins/' . $this->get_data('path') . '/admin/adminconverse.php'  . $SID, 'text' => $admintext, 'check' => 'a_ticket_admin')
            );

            return $main_menu;
        }
        return;
    }

    function gen_settings_menu()
    {
        if ( $this->pm->check(PLUGIN_INSTALLED, 'ticket') )
        {
            global $db, $user, $SID;

        $settings_menu = array(
            $user->lang['ticket'] => array(
                0 => '<a href="plugins/ticket/usersettings.php' . $SID . '">' . $user->lang['ticket_usersettings'] . '</a>',
            )
        );

            return $settings_menu;
        }
        return;
    }

   /**
    	* Generate ts admin menu
    	*
    	* @return array
    	*/
    	
//thx to Raidplanner
    function gen_admin_menu()
    {


	if ($this->pm->check(PLUGIN_INSTALLED, 'ticket') )
	{
		global $db, $user, $user, $SID, $eqdkp;
          	$dkpurl = "http://". trim($eqdkp->config['server_name'], "\\/"). "/" .trim($eqdkp->config['server_path'], "\\/"). "/";
			$admin_menu = array(
				'ticket' => array(
					0 => $user->lang['ticket'],
					1 => array(
						'link' => $dkpurl . 'plugins/ticket/admin/adminsettings.php' . $SID,
						'text' => $user->lang['ticket_adminsettings'],
						'check' => 'a_ticket_admin'),
					2 => array(
						'link' => $dkpurl . 'plugins/ticket/admin/adminconverse.php' . $SID,
						'text' => $user->lang['ticket_admin_converse'],
						'check' => 'a_ticket_admin'),
				)
			);
		return $admin_menu;
	}
	return;
    }


    function InsertIntoConfTable($fieldname,$insertvalue) // Edit by Achaz -- thx to Raidplanner
    {
	        global $eqdkp_root_path, $user, $SID, $table_prefix;
	                    $sql = "INSERT INTO " . $table_prefix . "ticket_config VALUES ('".$fieldname."', '".$insertvalue."');";
	                    $this->add_sql(SQL_INSTALL, $sql);
    }

    function InsertIntoEmailTable($admin_id,$email) // Edit by Achaz -- thx to Raidplanner
    {
	        global $eqdkp_root_path, $user, $SID, $table_prefix;
	                    $sql = "INSERT INTO " . $table_prefix . "ticket_adminemail VALUES ('','".$admin_id."', '".$email."');";
	                    $this->add_sql(SQL_INSTALL, $sql);
    }
}     
?>
