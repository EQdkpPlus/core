<?php
/******************************
 * EQdkp ItemSpecials Plugin
 * (c) 2006 - 2007 by WalleniuM [Simon Wallmann]
 * http://www.wallenium.de   
 * ------------------
 * itemspecials_plugin_class.php
 * Changed: January 10, 2007
 * 
 ******************************/
 
if ( !defined('EQDKP_INC') )
{
    die('You cannot access this file directly.');
}

class itemspecials_Plugin_Class extends EQdkp_Plugin
{
		// TEMPORARY FIX
		// set this one to true, if your links look like
		// http://www.dkpurl.tld/http://www.dkpurl.tld/plugins/itemspecials....
		var $is3_fallback = false;
	
    function itemspecials_plugin_class($pm)
    {
        global $eqdkp_root_path, $user, $SID, $table_prefix;

        // Call our parent's constructor
        $this->eqdkp_plugin($pm);

        // Get language pack
        $this->pm->get_language_pack('itemspecials');

        // Data for this plugin
		$this->add_data(array(
			'name'			    => 'Item Specials',
			'code'			    => 'itemspecials',
			'path'			    => 'itemspecials',
			'contact'		    => 'webmaster@wallenium.de',
			'template_path'	=> 'plugins/itemspecials/templates/',
			'version'		    => '3.0.2')
        );

        // Register our permissions
        $this->add_permission('924', 'a_itemspecials_conf',   'N', $user->lang['is_itemspecials_conf']);
        $this->add_permission('925', 'a_itemspecials_plugins','N', $user->lang['is_itemspecials_plugins']);
        $this->add_permission('921', 'u_setitems_view',       'Y', $user->lang['is_set_view']);
        $this->add_permission('922', 'u_specialitems_view',   'Y', $user->lang['is_special_view']);
        $this->add_permission('923', 'u_setright_view',       'Y', $user->lang['is_setright_view']);
        $this->add_permission('926', 'u_items_add',       		'Y', $user->lang['is_items_add']);
          
        // Add Menus
		    $this->add_menu('main_menu1', $this->gen_main_menu1());
		    $this->add_menu('admin_menu', $this->gen_admin_menu());
		    $this->add_menu('settings', $this->gen_settings_menu());
		    
		    // Define installation
        // -----------------------------------------------------
          $sql = "CREATE TABLE IF NOT EXISTS " . $table_prefix . "itemspecials_config (
				          `config_name` varchar(255) NOT NULL default '',
                  `config_value` varchar(255) default NULL,
                  PRIMARY KEY  (`config_name`)
		              )";
		      $this->add_sql(SQL_INSTALL, $sql);
		      
		      $sql = "CREATE TABLE IF NOT EXISTS `" . $table_prefix . "itemspecials_plugins` (
                  `plugin_id` mediumint(8) unsigned NOT NULL auto_increment,
                  `plugin_name` varchar(50) NOT NULL default '',
                  `plugin_installed` enum('0','1') NOT NULL default '0',
                  `plugin_path` varchar(255) NOT NULL default '',
                  `plugin_contact` varchar(100) default NULL,
                  `plugin_version` varchar(7) NOT NULL default '',
                  PRIMARY KEY  (`plugin_id`)
                  ) ;";
          $this->add_sql(SQL_INSTALL, $sql);
          
          $sql = "CREATE TABLE IF NOT EXISTS `" . $table_prefix . "itemspecials_items` (
                  `item_id` mediumint(8) unsigned NOT NULL auto_increment,
  								`item_name` varchar(255) default NULL,
  								`item_buyer` varchar(50) default NULL,
                  PRIMARY KEY  (`item_id`)
                  ) ;";
          $this->add_sql(SQL_INSTALL, $sql);
          
          $sql = "INSERT INTO `" . $table_prefix . "itemspecials_plugins` (`plugin_name`, `plugin_installed`, `plugin_path`, `plugin_contact`, `plugin_version`)  VALUES ('Normal Calculation', '1', 'setright_plugin1', 'webmaster@wallenium.de', '1.0.0');";
		      $this->add_sql(SQL_INSTALL, $sql);

		      $sql = "CREATE TABLE IF NOT EXISTS " . $table_prefix . "itemspecials_custom (
				          `set` varchar(50) NOT NULL default '',
                  `custom_name` varchar(255) NOT NULL default '',
                  `item_name` varchar(255) NOT NULL default '',
                  `order` int(9) NOT NULL default '0',
                  PRIMARY KEY  (`set`,`custom_name`)
		              ) ENGINE = MYISAM";
		      $this->add_sql(SQL_INSTALL, $sql);

          // standard Specialitems
          $ajax_array = array(
    				'Heart of Hakkar'   => 'Heart of Hakkar',
    				'Head of Onyxia'   => 'Head of Onyxia',
    				'Onyxia Hide Backpack'   => 'Onyxia Hide Backpack',
    				'Head of Nefarian'   => 'Head of Nefarian',
    				'Head of the Broodlord Lashlayer'   => 'Head of the Broodlord Lashlayer',
    				'Head of Ossirian the Unscarred'   => 'Head of Ossirian the Unscarred',
    				'Mature Black Dragon Sinew'   => 'Mature Black Dragon Sinew',
    				'Mature Blue Dragon Sinew'   => 'Mature Blue Dragon Sinew',
    				'Ancient Petrified Leaf'   => 'Ancient Petrified Leaf',
    				'The Eye of Divinity'  => 'The Eye of Divinity',
    				'The Eye of Shadow'  => 'The Eye of Shadow',
    				'Onyxia Scale Cloak'  => 'Onyxia Scale Cloak',
    				'Eye of CThun'  => "Eye of C\'Thun",
    				'Entry to Naxxramas'  => 'Entry to Naxxramas',
    				'Panther Hide Sack'  => 'Panther Hide Sack'
    			);
          $this->InsertAjax($ajax_array);
		      
		// global config
	  $this->InsertIntoTable('is_exec_time', '1');
	  $this->InsertIntoTable('is_updatecheck', '1');
		$this->InsertIntoTable('locale', 'de');
		$this->InsertIntoTable('race', 'Al');
		$this->InsertIntoTable('nonsettable', 'eqdkp_items');
		$this->InsertIntoTable('settable', 'eqdkp2_items');
		$this->InsertIntoTable('imgwidth', '22px');
		$this->InsertIntoTable('imgheight', '22px');
		$this->InsertIntoTable('hide_inactives', '1');
		$this->InsertIntoTable('colouredcls', '1');
		$this->InsertIntoTable('itemstats', '1');
		$this->InsertIntoTable('is_replace', '<font color=red>x</font>');
		
		// specialitems config
		$this->InsertIntoTable('header_images', '1');   // show Header Images instead of txt
		$this->InsertIntoTable('download_cache', '0');     // are all tier images downloaded?
		$this->InsertIntoTable('si_class', '1');        // show Class
		$this->InsertIntoTable('si_cls_icon', '1');     // show Class Icon
		
		//setitems config
		$this->InsertIntoTable('set_show_t1', '1');     // show Tier 1 Set
		$this->InsertIntoTable('set_show_t2', '1');     // show Tier 2 Set
		$this->InsertIntoTable('set_show_t3', '1');     // show Tier 3 Se
		$this->InsertIntoTable('set_show_index', '1');  // show Overview index
		$this->InsertIntoTable('set_drpdwn_cls', '1');  // show Dropdown Class list
		
		// set progress one page
		$this->InsertIntoTable('set_onePage', '1');     // show Tiers on one page
		$this->InsertIntoTable('set_op_rank', '1');        // show Rank
		$this->InsertIntoTable('set_op_points', '1');      // show current Points
		$this->InsertIntoTable('set_op_total', '1');      // show total Points
		$this->InsertIntoTable('set_op_class', '1');       // show Class
		$this->InsertIntoTable('set_op_cls_icon', '1');    // show Class Icon
		
		//setright config
		$this->InsertIntoTable('sr_rank', '1');         // show Rank
		$this->InsertIntoTable('sr_points', '1');       // show Points
		$this->InsertIntoTable('sr_class', '1');        // show Class
		$this->InsertIntoTable('sr_cls_icon', '1');     // show Class Icon
		
		// Add the Itemstats addtion for the NAXX thing
		$this->WriteISNaxxKey();
		
		// Uninstall
		$this->add_sql(SQL_UNINSTALL, "DROP TABLE IF EXISTS " . $table_prefix . "itemspecials_config");
		$this->add_sql(SQL_UNINSTALL, "DROP TABLE IF EXISTS " . $table_prefix . "itemspecials_plugins");
		$this->add_sql(SQL_UNINSTALL, "DROP TABLE IF EXISTS " . $table_prefix . "itemspecials_custom");
		$this->add_sql(SQL_UNINSTALL, "DROP TABLE IF EXISTS " . $table_prefix . "itemspecials_items");
		    
    }

    function gen_main_menu1()
	    {
			global $user, $SID;

	        if ($this->pm->check(PLUGIN_INSTALLED, 'itemspecials'))
	        {
	            global $db, $user, $eqdkp;
	            
              $main_menu1 = array(
	                array(

						'link' => 'plugins/itemspecials/specialitems.php' . $SID,
						'text' => $user->lang['is_usermenu_Specialitems'],
						'check' => 'u_specialitems_view'
					     ),
				        array(
						'link' => 'plugins/itemspecials/setitems.php' . $SID,
						'text' => $user->lang['is_usermenu_Setitems'],
						'check' => 'u_setitems_view'
	             ),
                  array(
						'link' => 'plugins/itemspecials/setright.php' . $SID,
						'text' => $user->lang['is_usermenu_setright'],
						'check' => 'u_setright_view')
	             );

	            return $main_menu1;
	        }
	        return;
    }
    
        function gen_admin_menu()
    {
        if ( $this->pm->check(PLUGIN_INSTALLED, 'itemspecials') )
        {
            global $db, $user, $SID, $eqdkp_root_path, $eqdkp;
            
            if($this->is3_fallback == true){
            	$dkpurl = $eqdkp_root_path;
            }else{
            	$dkpurl = "http://". trim($eqdkp->config['server_name'], "\\/"). "/" .trim($eqdkp->config['server_path'], "\\/"). "/";
						}

            $admin_menu = array(
				'itemspecials' => array(
					0 => $user->lang['is_adminmenu_itemspecials'],
					1 => array(
						'link' => $dkpurl . 'plugins/itemspecials/admin/settings.php' . $SID,
						'text' => $user->lang['is_itemspecials_conf'],
						'check' => 'a_itemspecials_conf'),
					2 => array(
						'link' => $dkpurl . 'plugins/itemspecials/admin/tollfreeitems.php' . $SID,
						'text' => $user->lang['is_itemspecials_additem'],
						'check' => 'a_itemspecials_conf'),
					3 => array(
						'link' => $dkpurl . 'plugins/itemspecials/admin/plugins.php' . $SID,
						'text' => $user->lang['is_itemspecials_plugins'],
						'check' => 'a_itemspecials_plugins')
				)
			);

            return $admin_menu;
        }
        return;
    }

		function gen_settings_menu()
    {
    	global $db, $user, $SID, $eqdkp;
        if ( $this->pm->check(PLUGIN_INSTALLED, 'itemspecials') &&  $user->check_auth('u_items_add', false))
        {
            if($this->is3_fallback == true){
            	$dkpurl = $eqdkp_root_path;
            }else{
            	$dkpurl = "http://". trim($eqdkp->config['server_name'], "\\/"). "/" .trim($eqdkp->config['server_path'], "\\/"). "/";
						}
        $settings_menu = array(
            $user->lang['itemspecials'] => array(
                0 => '<a href="'. $dkpurl . 'plugins/itemspecials/useritem.php' . $SID . '">' . $user->lang['is_useradd_items'] . '</a>',
                )
        );
            return $settings_menu;
        }
        return;
    }

      function WriteISNaxxKey()
      {
        global $eqdkp_root_path, $dbhost, $dbname, $dbuser, $dbpass, $eqdkp;
        if($this->is3_fallback == true){
            	$dkpurl = $eqdkp_root_path;
            }else{
            	$dkpurl = "http://". trim($eqdkp->config['server_name'], "\\/"). "/" .trim($eqdkp->config['server_path'], "\\/"). "/";
						}
        $filetocheck = $dkpurl."itemstats/config.php";
        if (file_exists($filetocheck))
        {
          // Check if sb is using corgans shit itemstats... if not, include the
          // stupid itemstatsconfig, if yes, let it be.
          if (item_cache_table == "item_cache_table")
          {
            include_once($filetocheck);
          }
          // Install all the additional (fictious) Itemstats Items in the cache
          //checks if the stupid itemstats cache table is installed
            $itemstats_sql = "CREATE TABLE IF NOT EXISTS `item_cache` (
                              `item_name` varchar(100) NOT NULL default '',
                              `item_link` varchar(100) default NULL,
                              `item_color` varchar(20) NOT NULL default '',
                              `item_icon` varchar(50) NOT NULL default '',
                              `item_html` text NOT NULL,
                              UNIQUE KEY `item_name` (`item_name`),
                              FULLTEXT KEY `item_html` (`item_html`)
                              ) ENGINE = MYISAM";
            $this->add_sql(SQL_INSTALL, $itemstats_sql);
            // GERMAN Item
            $del_sql = "DELETE FROM " . item_cache_table . " WHERE item_name = 'Zugang zu Naxxramas'";
            $this->add_sql(SQL_INSTALL, $del_sql);
            $sql = "INSERT INTO `".item_cache_table."` VALUES ('Zugang zu Naxxramas', '', 'orangename', 'INV_Misc_Key_04', '<table cellpadding=\'0\' border=\'0\' class=\'borderless\'><tr><td valign=\'top\'>\r\n<img class=\'itemicon\' src=\'{ITEM_ICON_LINK}\'></td><td><div class=\'wowitemt\' style=\'display:block\'><div>\r\n<span class=\'orangename\'>Zugang zu Naxxramas</span><br /></div>\r\n<div class=\'tooldiv\'><span class=\'itemdesc\'>&quot;Diesem Spieler ist der Zutritt zu Naxxramas gestattet.&quot;</span><br />\r\n</div></div></td></tr></table>');";
		        $this->add_sql(SQL_INSTALL, $sql);
            // French Item
            $del_sql = "DELETE FROM " . item_cache_table . " WHERE item_name = 'Accès à Naxxramas'";
            $this->add_sql(SQL_INSTALL, $del_sql);
						$sql = "INSERT INTO `".item_cache_table."` VALUES ('Accès à Naxxramas', '', 'orangename', 'INV_Misc_Key_04', '<table cellpadding=\'0\' border=\'0\' class=\'borderless\'><tr><td valign=\'top\'>\r\n<img class=\'itemicon\' src=\'{ITEM_ICON_LINK}\'></td><td><div class=\'wowitemt\' style=\'display:block\'><div>\r\n<span class=\'orangename\'>Accès à Naxxramas</span><br /></div>\r\n<div class=\'tooldiv\'><span class=\'itemdesc\'>&quot;Ce joueur est autorisé à entrer dans Naxxramas.&quot;</span><br />\r\n</div></div></td></tr></table>');";
						$this->add_sql(SQL_INSTALL, $sql);
		        // ENGLISH Item
            $del_sql = "DELETE FROM " . item_cache_table . " WHERE item_name = 'Entry to Naxxramas'";
            $this->add_sql(SQL_INSTALL, $del_sql);
            $sql = "INSERT INTO `".item_cache_table."` VALUES ('Entry to Naxxramas', '', 'orangename', 'INV_Misc_Key_04', '<table cellpadding=\'0\' border=\'0\' class=\'borderless\'><tr><td valign=\'top\'>\r\n<img class=\'itemicon\' src=\'{ITEM_ICON_LINK}\'></td><td><div class=\'wowitemt\' style=\'display:block\'><div>\r\n<span class=\'orangename\'>Entry to Naxxramas</span><br /></div>\r\n<div class=\'tooldiv\'><span class=\'itemdesc\'>&quot;This player is allowed to enter Naxxramas.&quot;</span><br />\r\n</div></div></td></tr></table>');";
		        $this->add_sql(SQL_INSTALL, $sql);
        } 
      }

      function InsertIntoTable($fieldname,$insertvalue)
      {
        global $eqdkp_root_path, $user, $SID, $table_prefix;
		    $sql = "INSERT INTO " . $table_prefix . "itemspecials_config VALUES ('".$fieldname."', '".$insertvalue."');";
		    $this->add_sql(SQL_INSTALL, $sql);
      }
      
      function InsertAjax($dataarray)
      {
        global $eqdkp_root_path, $user, $SID, $table_prefix;
        foreach($dataarray as $key=>$value) {
		    $sql = "INSERT INTO " . $table_prefix . "itemspecials_custom VALUES ('itempool', '".$key."', '".$value."', 0);";
		    $this->add_sql(SQL_INSTALL, $sql);
		    }
      }
}
?>