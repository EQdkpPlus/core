<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * eqdkp_plugins.php
 * Began: Sun Mar 15 2003
 *
 * $Id$
 *
 ******************************/

if ( !defined('EQDKP_INC') )
{
    header('HTTP/1.0 404 Not Found');
    exit;
}

// Plugin states
define('PLUGIN_INITIALIZED', 1);
define('PLUGIN_REGISTERED',  2);
define('PLUGIN_INSTALLED',   4);
define('PLUGIN_UNINSTALLED', 8);
define('PLUGIN_DISABLED', 	 16);
define('PLUGIN_ALL', PLUGIN_INITIALIZED | PLUGIN_REGISTERED | PLUGIN_INSTALLED | PLUGIN_UNINSTALLED | PLUGIN_DISABLED);

// SQL types
define('SQL_INSTALL',   1);
define('SQL_UNINSTALL', 2);

/**
* EQdkp Plugin Manager class
* Framework for all plugins
* Available to every page as $pm
*/

class EQdkp_Plugin_Manager
{
    var $registered  = array();             // Registered plugins       @var registered
    var $installed   = array();             // Installed plugin         @var installed
    var $uninstalled = array();             // Uninstalled plugins      @var uninstalled

    var $plugins     = array();             // Plugin objects           @var plugins

    var $errors      = array();             // Error messages           @var errors
    var $error_die   = true;                // Die on errors?           @var error_die
    var $debug       = false;               // Debug?                   @var debug

    var $valid_data_types = array('id', 'code', 'name', 'installed', 'contact', 'path', 'template_path', 'version');
    var $additional_valid_data_types = array('description', 'long_description', 'manuallink', 'homepage', 'imageurl', 'author');
    // ---------------------------------------------------------
    // Core methods
    // ---------------------------------------------------------

    /**
    * Constructor
    * Populate the registered, installed and uninstalled arrays, initializing
    *   any installed plugins
    *
    * @param    bool    $error_die Die on error messages?
    * @param    bool    $debug Output debug messages?
    */
    function eqdkp_plugin_manager($error_die = false, $debug = false)
    {
        global $db;

        $this->error_die = $error_die;
        $this->debug = ( $debug ) ? true : false;

        // Populate arrays of registered/installed/uninstalled plugins
        $sql = 'SELECT plugin_code, plugin_path, plugin_installed
                FROM ' . PLUGINS_TABLE . '
                ORDER BY plugin_name';
        $result = $db->query($sql);

        while ( $row = $db->fetch_record($result) )
        {
            $plugin_code = $row['plugin_code'];
            $plugin_path = $row['plugin_path'];

            // Add this plugin's code to our registered list
            $this->registered[$plugin_code] = $plugin_code;

            // Add the plugin code to either the installed or uninstalled list
            if ( $row['plugin_installed'] == 1 )
            {
                $this->installed[$plugin_code] = $plugin_code;

                // Attempt to initialize the installed plugin
                if ( !$this->initialize($plugin_code, $plugin_path) )
                {
                    $this->error_append('Plugin Manager Instantiation', $plugin_code .' is installed but could not be initialized.', true);
                }
            }
            else
            {
                $this->uninstalled[$plugin_code] = $plugin_code;
            }
        }
        $db->free_result($result);

        // Output any errors that may have occurred
        $this->error_check(true);
    }

    /**
    * Perform a check on $plugin_code
    *
    * @param    int     $check PLUGIN_INITIALIZED | PLUGIN_REGISTERED | PLUGIN_INSTALLED | PLUGIN_UNINSTALLED
    * @param    string  $plugin_code
    * @return bool
    */
    function check($check, $plugin_code)
    {
        switch ( $check )
        {
            case PLUGIN_INITIALIZED:
                $retval = ( is_null($plugin_code) ) ? false : isset($this->plugins[$plugin_code]);
                break;

            case PLUGIN_REGISTERED:
                $retval = ( is_null($plugin_code) ) ? false : isset($this->registered[$plugin_code]);
                break;

            case PLUGIN_INSTALLED:
                $retval = ( is_null($plugin_code) ) ? false : isset($this->installed[$plugin_code]);
                break;

            case PLUGIN_UNINSTALLED:
                $retval = ( is_null($plugin_code) ) ? false : isset($this->uninstalled[$plugin_code]);
                break;
            
            case PLUGIN_DISABLED:
            		$dsoutput = (isset($this->plugins[$plugin_code]) && isset($this->uninstalled[$plugin_code])) ? true : false;
                $retval = ( is_null($plugin_code) ) ? false : $dsoutput;
                break;

            default:
                $retval = false;
        }

        return $retval;
    }

    // ---------------------------------------------------------
    // Initialize / Register / Install / Uninstall / Activate methods
    // ---------------------------------------------------------
		
		 /**
    * Attempt to initialize a plugin
    *
    * @param    string  $plugin_code Plugin to initialize
    * @param    string  $plugin_path Optional, directory where the plugin resides
    * @return   bool
    */
    function enable($plugin_code, $plugin_path = '')
   	{
   			global $db, $eqdkp_root_path;
   			if ( $this->check(PLUGIN_DISABLED, $plugin_code) )
        {
            return true;
        }
   			
		 		// Set the plugin as 'activated'
                $sql = 'UPDATE ' . PLUGINS_TABLE . "
                        SET plugin_installed='1'
                        WHERE plugin_code='" . $plugin_code . "'";
                $db->query($sql);
		    // If we get to here, there were no errors (since error_check didn't kill our output)
		    $title = 'Success';
        $text  = $plugin_name . ' enabled successfully.';
        message_die($text, $title);
		}
	
    /**
    * Attempt to initialize a plugin
    *
    * @param    string  $plugin_code Plugin to initialize
    * @param    string  $plugin_path Optional, directory where the plugin resides
    * @return   bool
    */
    function initialize($plugin_code, $plugin_path = '')
    {
        global $db, $eqdkp_root_path;

        // Prevent re-initializing the same plugin
        if ( $this->check(PLUGIN_INITIALIZED, $plugin_code) )
        {
            return false;
        }

        $plugin_path = ( !empty($plugin_path) ) ? $plugin_path : $plugin_code;
        $plugin_dir  = $eqdkp_root_path . 'plugins/' . $plugin_path . '/';

        if ( !is_dir($plugin_dir) )
        {
            // Couldn't find this plugin's directory
            //$this->error_append('Initialization', 'Directory "' . $plugin_dir . '" does not exist.', true);

            // Directory doesn't exist, we'll never get this plugin inititalized
            // Remove its entry from the database
            $sql = 'DELETE FROM ' . PLUGINS_TABLE . "
                    WHERE plugin_code = '" . $plugin_code . "'";
            $db->query($sql);
        }
        else
        {
            // Search for a file named <code>_plugin_class.php containing a class named <code>_Plugin_Class
            $plugin_class      = $plugin_code . '_Plugin_Class';
            $plugin_class_file = $plugin_dir . strtolower($plugin_class) . '.php';

            if ( !is_file($plugin_class_file) )
            {
                // Plugin's class file doesn't exist
                //$this->error_append('Initialization', 'File "' . $plugin_class_file . '" does not exist.', true);

                // Plugin class file doesn't exist, remove its entry from the database
                $sql = 'DELETE FROM ' . PLUGINS_TABLE . "
                        WHERE plugin_code = '" . $plugin_code . "'";
                $db->query($sql);
            }
            else
            {
                include_once($plugin_class_file);

                if ( !class_exists($plugin_class) )
                {
                    // Plugin's class definition does not exist
                    $this->error_append('Initialization', 'Class "' . $plugin_class . '" does not exist.', true);
                }
                else
                {
                    $plugin_object = new $plugin_class($this);

                    if ( !is_object($plugin_object) )
                    {
                        // Plugin's class failed to instantiate
                        $this->error_append('Initialization', 'Class "' . $plugin_class . '" failed to instantiate.', true);
                    }
                    else
                    {
                        $this->plugins[$plugin_code] = $plugin_object;
                    }
                } // class_exists
            } // is_file
        } // is_dir
        unset($plugin_object, $plugin_class, $plugin_class_file, $plugin_path, $plugin_dir);

        if ( !$this->check(PLUGIN_INITIALIZED, $plugin_code) )
        {
            // Initialization failed
            $this->error_append('Initialization', 'Plugin "' . $plugin_code . '" could not be initialized.', true);
        }
        $this->error_check(true);

        // If we get to here, there were no errors (since error_check didn't kill our output)
        return true;
    }

    /**
    * Register a specific plugin, or all available plugins
    *
    * @param $s_plugin_code
    * @return bool
    */
    function register($s_plugin_code = '')
    {
        global $eqdkp_root_path;

        // s_plugin_code - as in static, this plugin_code doesn't change
        // d_plugin_code - as in dynamic, this will change with each iteration of readdir()

        // Prevent re-registering the same plugin
        if ( $this->check(PLUGIN_REGISTERED, $s_plugin_code) )
        {
            return true;
        }

        // Search for plugins and make sure they are registered
        if ( $dir = @opendir($eqdkp_root_path . 'plugins/') )
        {
            while ( $d_plugin_code = @readdir($dir) )
            {
                $cwd = $eqdkp_root_path . 'plugins/' . $d_plugin_code;
                if ( valid_folder($cwd) && substr($d_plugin_code, 0, 1) != '_' )
                {
                    // If $d_plugin_code is in our array of registered codes,
                    // continue with the next iteration of the while loop
                    if ( in_array($d_plugin_code, $this->registered) )
                    {
                        continue;
                    }
                    else
                    {
                        // If $s_plugin_code is defined, only register that plugin
                        // Otherwise, register all unregistered plugins
                        if ( (!empty($s_plugin_code)) && ($s_plugin_code != $d_plugin_code) )
                        {
                            // Break out of the loop if $s_plugin_code is defined
                            // and this directory doesn't belong to $s_plugin_code
                            continue;
                        }
                        else
                        {
                            if ( !$this->initialize($d_plugin_code) )
                            {
                                // Couldn't initialize
                                $this->error_append('Registeration', 'Plugin "' . $d_plugin_code . '" could not be initialized.', true);
                            }
                            else
                            {
                                // Get the plugin's object
                                $plugin_object = $this->get_plugin($d_plugin_code);

                                if ( !is_object($plugin_object) )
                                {
                                    // Object couldn't be instantiated
                                    $this->error_append('Registration', 'Plugin class for "' . $d_plugin_code . '" could not be initialized.', true);
                                }
                                else
                                {
                                    // Make the plugin register itself
                                    $plugin_object->register($d_plugin_code);

                                    // Update the plugin manager arrays
                                    $this->registered[$d_plugin_code] = $d_plugin_code;
                                    $this->uninstalled[$d_plugin_code] = $d_plugin_code;
                                }
                            }
                        }
                    }
                } // is directory
                unset($plugin_object, $d_plugin_code, $cwd);
            } // readdir
        } // opendir
	else {
	print "fopen didn't work.<br>";
	}

        unset($dir);

        // If a specific plugin was given, check to make sure it was registered
        if ( !empty($s_plugin_code) )
        {
            if ( !$this->check(PLUGIN_REGISTERED, $plugin_code) )
            {
                // Initialization failed
                $this->error_append('Registration', 'Plugin "' . $plugin_code . '" could not be registered.', true);
            }
        }
        $this->error_check(true);

        return true;
    }

    /**
    * Install a plugin
    *
    * @param $plugin_code
    * @return bool
    */
    function install($plugin_code)
    {
        // Prevent re-installing this plugin
        if ( $this->check(PLUGIN_INSTALLED, $plugin_code) )
        {
            return true;
        }

        // Initialize the plugin - initialize() prevents this if it needs to
        $this->initialize($plugin_code);

        // Get the plugin object
        $plugin_object = $this->get_plugin($plugin_code);
        if ( !is_object($plugin_object) )
        {
            $this->error_append('Instantiation', 'Plugin "' . $plugin_code . '" failed to create a valid object.', true);
        }
        else
        {
            // Run the install
            $sql_install_return_value = $plugin_object->run_sql(SQL_INSTALL);
            if ( is_array($sql_install_return_value) && !($sql_install_return_value[0]) )
            {
                $this->error_append('Installation', 'Plugin "' . $plugin_code . '" installation SQL failed.', true);
                $this->error_append('Installation', 'Error in SQL command: "' . $sql_install_return_value[1] . '".', true);
            }
            else
            {
                // Remove this item from our uninstalled array
                unset($this->uninstalled[$plugin_code]);

                // Add it to our installed array
                $this->installed[$plugin_code] = $plugin_code;
            }
        }

        if ( !$this->check(PLUGIN_INSTALLED, $plugin_code) )
        {
            $this->error_append('Installation', 'Plugin "' . $plugin_code . '" failed to install.', true);
        }

        $this->error_check(true);

        return true;
    }

    /**
    * Uninstall a plugin
    *
    * @param $plugin_code
    * @return bool
    */
    function uninstall($plugin_code)
    {
        if ( $this->check(PLUGIN_UNINSTALLED, $plugin_code) )
        {
            return true;
        }

        $this->initialize($plugin_code);

        // Get the plugin object
        $plugin_object = $this->get_plugin($plugin_code);
        if ( !is_object($plugin_object) )
        {
            $this->error_append('Instantiation', 'Plugin "' . $plugin_code . '" failed to create a valid object.', true);
        }
        else
        {
            // Run the uninstall
            if ( !$plugin_object->run_sql(SQL_UNINSTALL) )
            {
                $this->error_append('Uninstallation', 'Plugin "' . $plugin_code . '" uninstallation SQL failed.', true);
            }
            else
            {
                // Remove this item from our installed array
                unset($this->installed[$plugin_code]);

                // Add it to our uninstalled array
                $this->uninstalled[$plugin_code] = $plugin_code;
            }
        }

        if ( !$this->check(PLUGIN_UNINSTALLED, $plugin_code) )
        {
            $this->error_append('Uninstallation', 'Plugin "' . $plugin_code . '" failed to uninstall.', true);
        }

        $this->error_check(true);

        return true;
    }

    // ---------------------------------------------------------
    // Plugin methods
    // ---------------------------------------------------------

    /**
    * Return $plugin_code's associated object
    *
    * @param $plugin_code
    * @return mixed Object / false
    */
    function get_plugin($plugin_code)
    {
        return ( @is_object($this->plugins[$plugin_code]) ) ? $this->plugins[$plugin_code] : false;
    }

    /**
    * Get multiple plugins
    *
    * @param $filter Type of plugins to get (can be |'d together using the PLUGIN_x constants)
    * @return array Associative array of plugin objects as code => object
    */
    function get_plugins($filter = PLUGIN_INSTALLED)
    {
        $retval = array();
        $unset_array = array();

        // Get initialized plugins
        if ( $filter & PLUGIN_INITIALIZED )
        {
            $retval = array_merge($retval, $this->plugins);
        }

        // Get installed plugins
        if ( $filter & PLUGIN_INSTALLED )
        {
            foreach ( $this->installed as $plugin_code )
            {
                // Initialize the plugin if it's not already
                if ( !$this->check(PLUGIN_INITIALIZED, $plugin_code) )
                {
                    $this->initialize($plugin_code);
                }

                $retval[$plugin_code] = $this->get_plugin($plugin_code);
            }
        }

        // Get uninstalled plugins
        if ( $filter & PLUGIN_UNINSTALLED )
        {
            foreach ( $this->uninstalled as $plugin_code )
            {
                if ( !$this->check(PLUGIN_INITIALIZED, $plugin_code) )
                {
                    $this->initialize($plugin_code);
                    $unset_array[] = $plugin_code;
                }

                $retval[$plugin_code] = $this->get_plugin($plugin_code);
            }
        }

        // Unset any plugins that may have been initialized just for this purpose
        foreach ( $unset_array as $plugin_code )
        {
            unset($this->plugins[$plugin_code]);
        }

        return $retval;
    }

    /**
    * Get a plugin's language file
    *
    * @param $plugin_code Either a specific plugin, or 'all'
    * @return bool
    */
    function get_language_pack($plugin_code = 'all')
    {
        global $eqdkp_root_path, $user;

        // Recursive if we're getting the language packs for every plugin
        if ( $plugin_code == 'all' )
        {
            foreach ( $this->installed as $plugin_code )
            {
                $this->get_language_pack($plugin_code);
            }
        }
        else
        {
            $lang_file = $eqdkp_root_path . 'plugins/' . $plugin_code . '/language/' . $user->lang_name . '/lang_main.php';

            if ( file_exists($lang_file) )
            {
                include_once($lang_file);

                $user->lang = ( @is_array($lang) ) ? array_merge($user->lang, $lang) : $user->lang;
		        //Add english language fallback code | sz3
            }else{
                $lang_file = $eqdkp_root_path . 'plugins/' . $plugin_code . '/language/english/lang_main.php';
                if ( file_exists($lang_file) ){
                    include_once($lang_file);
                    $user->lang = ( @is_array($lang) ) ? array_merge($user->lang, $lang) : $user->lang;
		            }//end of addition
           }
        }
        return true;
    }

    // ---------------------------------------------------------
    // Hook methods
    // ---------------------------------------------------------

    /**
    * Add plugins' permission box arrays to an existing permissions array
    * Modifies the array by reference
    *
    * @param $cbox_array Array we're modifying
    * @return bool
    */
    function generate_permission_boxes(&$cbox_array)
    {
        global $user;

        foreach ( $this->get_plugins(PLUGIN_INSTALLED) as $plugin_code => $plugin_object )
        {
            if ( $plugin_object->is_permissions() )
            {
                $cbox_array = array_merge($cbox_array, $plugin_object->permission_boxes());
            }
        }

        return true;
    }

    /**
    * Call hooks for a specific page
    *
    * @param $page
    * @return mixed Result of <plugin_object>::do_hook()
    */
    function do_hooks($page)
    {
        global $eqdkp_root_path;

        $retval = array();

        $request = ( isset($_SERVER['REQUEST_URI']) ) ? $_SERVER['REQUEST_URI'] : $_SERVER['SCRIPT_NAME'] . (( isset($_SERVER['QUERY_STRING']) ) ? '?' . $_SERVER['QUERY_STRING'] : '');

        // If we've been handed a raw URL string, shorten it appropriately
        if ( $request == $page )
        {
            $slash_count = substr_count($eqdkp_root_path, '/');
            $url = split('/', $request);
            $url = array_slice($url, -$count);
            $page = join('/', $url);
        }

        foreach ( $this->get_plugins() as $plugin_code => $plugin_object )
        {
            $retval[$plugin_code] = $plugin_object->do_hook($page);
        }

        return $retval;
    }

    /**
    * Return log actions
    *
    * @return array
    */
    function get_log_actions()
    {
        $retval = array();

        foreach ( $this->get_plugins() as $plugin_code => $plugin_object )
        {
            foreach ( $plugin_object->get_log_actions() as $action_type => $lang_string )
            {
                $retval[$action_type] = $lang_string;
            }
        }

        return $retval;
    }

    /**
    * Generate a menu
    *
    * @param $menu_name
    * @deprec Array format removes the need for this
    * @return string Menu
    */
    function generate_menu($menu_name)
    {
        $menu_array = array();

        foreach ( $this->get_plugins(PLUGIN_INSTALLED) as $plugin_code => $plugin_object )
        {
            $menu_array = array_merge($menu_array, $plugin_object->get_menu($menu_name));
        }

        $menu_string = '';

        foreach ( $menu_array as $plugin_code => $menu )
        {
            if ( ($menu != '') && ($this->check(PLUGIN_INSTALLED, $plugin_code)) )
            {
                $menu_string .= ' | ' . $menu;
            }
        }

        return $menu_string;
    }

    /**
    * Get a menu array
    *
    * @param $menu_name
    * @return array
    */
    function get_menus($menu_name = 'admin_menu')
    {
        $menu_array = array();

        foreach ( $this->get_plugins(PLUGIN_INSTALLED) as $plugin_code => $plugin_object )
        {
            $plugin_array = $plugin_object->get_menu($menu_name);
            $menu_array = (is_array($plugin_array[$plugin_code])) ? array_merge($menu_array, $plugin_array[$plugin_code]) : $menu_array;
        }

        return $menu_array;
    }

    // ---------------------------------------------------------
    // Error methods
    // ---------------------------------------------------------

    /**
    * Add an error message to the error buffer
    *
    * @param $location Part of the class the error occurred in
    * @param $error Error message
    * @param $force Add this message regardless of $this->debug?
    * @return bool
    */
    function error_append($location, $error, $force = false)
    {
        if ( ($this->debug) || ($force) )
        {
            $this->errors[] = '<b>' . $location . ':</b> ' . $error;

            return true;
        }

        return false;
    }

    /**
    * Check to see if errors exist in the queue
    *
    * @return bool
    */
    function is_error()
    {
        return ( sizeof($this->errors) > 0 ) ? true : false;
    }

    /**
    * Output error messages if we need to
    *
    * @param $die Can override $this->error_die
    * @return bool
    */
    function error_check($die = false)
    {
        if ( ($this->is_error()) && (($this->error_die) || ($die)) )
        {
            $message = '<b>The following error(s) occurred:</b><br />';
            foreach ( $this->errors as $error )
            {
                $message .= $error . '<br />';
            }
            message_die("$message");
        }
        else
        {
            // Wipe the error buffer
            $this->errors = array();

            return false;
        }
    }

    // ---------------------------------------------------------
    // Data methods
    // ---------------------------------------------------------

    /**
    * Add plugin data to $plugin_code
    *
    * @param $plugin_code
    * @param $type Data type (may be a relational array of type => data)
    * @param $data Data value
    * @return bool
    */
    function add_data($plugin_code, $type, $data = '')
    {
        if ( $this->check(PLUGIN_INITIALIZED, $plugin_code) )
        {
            if ( is_array($type) )
            {
                foreach ( $type as $i_type => $data )
                {
                    $this->add_data($i_type, $data);
                }
            }
            else
            {
                if ( in_array($type, $this->valid_data_types) )
                {
                    $plugin_object = $this->plugins[$plugin_code];

                    if ( is_object($plugin_object) )
                    {
                        $plugin_object->add_data($type, $data);
                    }

                    return true;
                }
            }
        }
        $this->error_append('Add Data', 'Invalid data type ("' . $type . '").');

        return false;
    }

    /**
    * Get plugin data for $plugin_code
    * @param $plugin_code
    * @param $type Data type
    */
    function get_data($plugin_code, $type)
    {
        if ( $this->check(PLUGIN_INITIALIZED, $plugin_code) )
        {
            if ( in_array($type, $this->valid_data_types) )
            {
                $plugin_object = $this->plugins[$plugin_code];

                if ( is_object($plugin_object) )
                {
                    return $plugin_object->get_data($type);
                }
            }
        }
        $this->error_append('Get Data', 'Invalid data type ("' . $type . '").');

        return false;
    }
    
        /**
    * Get plugin data for $plugin_code
    * @param $plugin_code
    * @param $type Data type
    */
    function get_additional_data($plugin_code, $type)
    {
        if ( $this->check(PLUGIN_INITIALIZED, $plugin_code) )
        {
            if ( in_array($type, $this->additional_valid_data_types) )
            {
                $plugin_object = $this->plugins[$plugin_code];

                if ( is_object($plugin_object) )
                {
                    return $plugin_object->get_additional_data($type);
                }
            }
        }
        $this->error_append('Get Additional Data', 'Invalid data type ("' . $type . '").');

        return false;
    }
}

/**
* EQdkp Plugin class
* Framework for individual classes
* Is used only as an extension to a plugin's actual class
*/

class EQdkp_Plugin
{
    var $pm;                                // Plugin manager       @var pm

    var $data          = array();           // Plugin data          @var data
    var $permissions   = array();           // Permission data      @var permissions

    var $install_sql   = array();           // Install queries      @var install_sql
    var $uninstall_sql = array();           // Uninstall queries    @var uninstall_sql

    var $hooks         = array();           // Hook calls           @var hooks
    var $menus         = array();           // Menu definitions     @var menus
    var $log_actions   = array();           // Log actions          @var log_actions

    // ---------------------------------------------------------
    // Core methods
    // ---------------------------------------------------------

    /**
    * Constructor
    * Stores a reference to the EQdkp Plugin Manager object
    *
    * @param $pm Plugin Manager object
    */
    function eqdkp_plugin($pm = '')
    {
        if ( !empty($pm) )
        {
            $this->pm = &$pm;
        }
    }

    // ---------------------------------------------------------
    // Install / Uninstall methods
    // ---------------------------------------------------------

    /**
    * Add an SQL query to the appropriate array
    *
    * @param $type SQL_INSTALL | SQL_UNINSTALL
    * @param $sql Query string
    * @param $order Step
    */
    function add_sql($type, $sql, $order = '')
    {
        switch ( $type )
        {
            case SQL_INSTALL:
                $order = ( $order == '' ) ? (count($this->install_sql) + 2) : $order;
                $this->install_sql[$order] = $sql;
                break;

            case SQL_UNINSTALL:
                $order = ( $order == '' ) ? (count($this->uninstall_sql) + 3) : $order;
                $this->uninstall_sql[$order] = $sql;
                break;
        }
    }

    /**
    * Run an SQL query from the appropriate array
    *
    * @param $type SQL_INSTALL | SQL_UNINSTALL
    * @return bool
    */
    function run_sql($type)
    {
        global $db;

        $permissions = $this->get_permissions();
        ksort($permissions);

        switch ( $type )
        {
            case SQL_INSTALL:
                // Check if we need to add permissions
                if ( count($permissions) > 0 )
                {
                    $sql = 'INSERT INTO ' . AUTH_OPTIONS_TABLE . '
                            (auth_id, auth_value, auth_default)
                            VALUES ';

                    foreach ( $permissions as $auth_id => $permission )
                    {
                        $sql .= "('" . $auth_id . "','" . $permission['auth_value'] . "','" . $permission['auth_default'] . "'), ";
                    }
                    $sql = preg_replace('/, $/', '', $sql);
                    $this->add_sql(SQL_INSTALL, $sql, 0);
                }

                // Set the plugin as 'installed'
                $sql = 'UPDATE ' . PLUGINS_TABLE . "
                        SET plugin_installed='1'
                        WHERE plugin_code='" . $this->get_data('code') . "'";
                $this->add_sql(SQL_INSTALL, $sql, 1);

                ksort($this->install_sql);
                foreach ( $this->install_sql as $sql )
                {
                    if ( !$db->query($sql) )
                    {
                        // SQL_DB has its own error handling
                        return array(0 => false, 1 => $sql);
                    }
                }
                break;

            case SQL_UNINSTALL:
                // Check if we need to remove permissions
                if ( count($permissions) > 0 )
                {
                    $in_clause = '';
                    foreach ( $permissions as $auth_id => $permission )
                    {
                        $in_clause .= $auth_id . ',';
                    }
                    $in_clause = preg_replace('/,$/', '', $in_clause);

                    // Auth Options
                    $sql = 'DELETE FROM ' . AUTH_OPTIONS_TABLE . '
                            WHERE auth_id IN(' . $in_clause . ')';
                    $this->add_sql(SQL_UNINSTALL, $sql, 0);

                    // Auth Users
                    $sql = 'DELETE FROM ' . AUTH_USERS_TABLE . '
                            WHERE auth_id IN(' . $in_clause . ')';
                    $this->add_sql(SQL_UNINSTALL, $sql, 1);
                }

                // Set the plugin as 'uninstalled'
                $sql = 'UPDATE ' . PLUGINS_TABLE . "
                        SET plugin_installed='0'
                        WHERE plugin_code='" . $this->get_data('code') . "'";
                $this->add_sql(SQL_UNINSTALL, $sql, 2);

                ksort($this->uninstall_sql);
                foreach ( $this->uninstall_sql as $sql )
                {
                    if ( !$db->query($sql) )
                    {
                        return false;
                    }
                }
                break;

            default:
                $this->pm->error_append('Run SQL', 'EQdkp_Plugin::run_sql() called without a valid type specified.');
        }

        $this->pm->error_check(true);

        return true;
    }

    /**
    * Provide feedback to the plugins.php script
    *
    * @param $type SQL_INSTALL | SQL_UNINSTALL
    * @param $show_error Show error message(s)
    */
    function message($type, $show_error = true)
    {
        global $db, $tpl, $eqdkp, $user;
        global $gen_simple_header, $start_time, $eqdkp_root_path;

        //Generic install statement
        $plugin_name = $this->get_data('name');

        switch ( $type )
        {
            case SQL_INSTALL:
                $install_uninstall = 'install';
                $installed_uninstalled = 'installed';
                break;

            case SQL_UNINSTALL:
                $install_uninstall = 'uninstall';
                $installed_uninstalled = 'uninstalled';
                break;

            // Fail-safe, this shouldn't ever happen
            default:
                $install_uninstall = 'message';
                $installed_uninstalled = 'called message()';
                break;
        }

        if ( ($this->pm->is_error()) && ($show_error) )
        {
            $title = 'Error';
            $text  = 'Errors were detected during the ' . $install_uninstall . ' process: ' . $this->pm->get_errors();
            $text .= $plugin_name . ' may not have ' . $installed_uninstalled . ' correctly.';
        }
        else
        {
            $title = 'Success';
            $text  = $plugin_name . ' ' . $installed_uninstalled . ' successfully.';
        }

        // Use regular die if $tpl is invalid or message_die is undefined
        if ( (!is_object($tpl)) || (!function_exists('message_die')) )
        {
            die($text);
        }
        else
        {
            message_die($text, $title);
        }
    }

    // ---------------------------------------------------------
    // Registration methods
    // ---------------------------------------------------------

    /**
    * Register a plugin in the database
    *
    * @param $plugin_code
    * @return bool
    */
    function register($plugin_code)
    {
        global $db;

        $sql = 'SELECT plugin_installed
                FROM ' . PLUGINS_TABLE . "
                WHERE plugin_code='" . $plugin_code . "'";
        $registered = $db->query_first($sql);
        unset($sql);

        if ( ($registered != '0') && ($registered != '1') )
        {
            $plugin_path    = $this->get_data('path');
            $plugin_name    = $this->get_data('name');
            $plugin_contact = $this->get_data('contact');
            $plugin_version = $this->get_data('version');

            $query = $db->build_query('INSERT', array(
                'plugin_name'      => $plugin_name,
                'plugin_code'      => $plugin_code,
                'plugin_installed' => '0',
                'plugin_path'      => $plugin_path,
                'plugin_contact'   => $plugin_contact,
                'plugin_version'   => $plugin_version)
            );

            if ( !$db->query('INSERT INTO ' . PLUGINS_TABLE . $query) )
            {
                return false;
            }
            unset($query, $plugin_path, $plugin_name, $plugin_contact, $plugin_version);
        }

        return true;
    }

    // ---------------------------------------------------------
    // Permission methods
    // ---------------------------------------------------------

    /**
    * Add permission options for this plugin
    *
    * @param $auth_id
    * @param $auth_value
    * @param $auth_default
    * @param $text Text describing this permission (checkbox)
    */
    function add_permission($auth_id, $auth_value, $auth_default, $text)
    {
        $this->permissions[$auth_id] = array(
            'auth_id'      => $auth_id,
            'auth_value'   => $auth_value,
            'auth_default' => $auth_default,
            'text'         => $text);
    }

    /**
    * Check if any permissions are set
    *
    * @return bool
    */
    function is_permissions()
    {
        return ( count($this->permissions) ) ? true : false;
    }

    /**
    * Return permission array for this object
    *
    * @return array
    */
    function get_permissions()
    {
        return $this->permissions;
    }

    /**
    * Add this object's permission options in the form of an
    * array to an existing permission boxes array
    *
    * @return array
    */
    function permission_boxes()
    {
        global $user;

        $cbox_array = array();

        foreach ( $this->permissions as $auth_id => $permissions )
        {
            // All plugin permissions are italic, a_* should be bold/italic
            $text = '<i>' . (( preg_match('/^a_/', $permissions['auth_value']) ) ? '<b>' . $permissions['text'] . '</b>' : $permissions['text']) . '</i>';

            // Look for $user->lang['<code>_plugin'] - otherwise just use $user->lang['<code>']
            $code = $this->get_data('code');
            $cbox_group = ( isset($user->lang[$code . '_plugin']) ) ? $user->lang[$code . '_plugin'] : $user->lang[$code];
            $cbox_array[$cbox_group][] = array(
                'CBNAME'    => $permissions['auth_value'],
                'CBCHECKED' => $permissions['auth_id'],
                'TEXT'      => $text);
        }

        return $cbox_array;
    }

    // ---------------------------------------------------------
    // Log methods
    // ---------------------------------------------------------

    /**
    * Add a log action
    *
    * @var $action_type
    * @var $action_text
    */
    function add_log_action($action_type, $action_text)
    {
        $this->log_actions[$action_type] = $action_text;
    }

    /**
    * Get log actions
    *
    * @return array
    */
    function get_log_actions()
    {
        return $this->log_actions;
    }

    // ---------------------------------------------------------
    // Menu methods
    // ---------------------------------------------------------

    /**
    * Add a link to a menu
    *
    * @param $menu_name
    * @param $menu_item
    */
    function add_menu($menu_name, $menu_item)
    {
        $this->menus[$menu_name][ $this->get_data('code') ] = $menu_item;
    }

    /**
    * Return a menu definition
    *
    * @param $menu_name
    */
    function get_menu($menu_name)
    {
        return ( isset($this->menus[$menu_name]) ) ? $this->menus[$menu_name] : false;
    }

    // ---------------------------------------------------------
    // Hook methods
    // ---------------------------------------------------------

    /**
    * Add a hook definition
    *
    * @param $page
    * @param $hook_name
    */
    function add_hook($page, $hook_name)
    {
        $this->hooks[$page][] = $hook_name;
    }

    /**
    * Perform a hook call
    *
    * @param $s_page Page with the hook
    * @return array
    */
    function do_hook($s_page)
    {
        $retval = array();

        foreach ( $this->hooks as $d_page => $page_hooks )
        {
            if ( $s_page == $d_page )
            {
                foreach ( $page_hooks as $hook )
                {
                    if ( method_exists($this, $hook) )
                    {
                        $retval[$hook] = $this->$hook();
                    }
                }
            }
        }

        return $retval;
    }

    // ---------------------------------------------------------
    // Data methods
    // ---------------------------------------------------------

    /**
    * Add plugin data to this object
    *
    * @param $type Data type (may be a relational array of type => data)
    * @param $data Data value
    * @return bool
    */
    function add_data($type, $data = '')
    {
        if ( is_array($type) )
        {
            foreach ( $type as $i_type => $data )
            {
                $this->add_data($i_type, $data);
            }
        }
        else
        {
            if ( in_array($type, $this->pm->valid_data_types) )
            {
                $this->data[$type] = $data;

                return true;
            }
            else
            {
                message_die('Invalid data type ("' . $type . '").', 'Data error');

                return false;
            }
        }
    }

    /**
    * Get plugin data for this object
    *
    * @param $type Data type
    */
    function get_data($type)
    {
        if ( in_array($type, $this->pm->valid_data_types) )
        {
            return ( isset($this->data[$type]) ) ? $this->data[$type] : false;
        }
        else
        {
            message_die('Invalid data type ("' . $type . '").', 'Data error');

            return false;
        }
    }
    
    /**
    * Get additional plugin data for this object
    *
    * @param $type Data type
    */
    function get_additional_data($type)
    {
        if ( in_array($type, $this->pm->additional_valid_data_types) )
        {
            return ( isset($this->additional_data[$type]) ) ? $this->additional_data[$type] : false;
        }
        else
        {
            message_die('Invalid additional data type ("' . $type . '").', 'Data error');

            return false;
        }
    }
}
?>
