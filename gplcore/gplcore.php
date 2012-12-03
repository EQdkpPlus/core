<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * eqdkp.php
 * begin: Sat December 21 2002
 *
 * $Id$
 *
 ******************************/

if ( !defined('EQDKP_INC') ){
    header('HTTP/1.0 404 Not Found');exit;
}

/**
* EQdkp admin page foundation
* Extended by admin page classes only
*/

class EQdkp_Admin
{
    // General vars
    var $buttons      = array();          // Submit buttons and their associated actions      @var buttons
    var $params       = array();          // GET parameters and their associated actions      @var params
    var $last_process = '';               // Last-called process                              @var last_process
    var $err_process  = 'display_form';   // Process to call when errors occur                @var err_process
    var $url_id       = 0;                // ID from _GET                                     @var url_id
    var $fv           = NULL;             // Form Validation object (not reference)           @var fv
    var $time         = 0;                // Current time                                     @var time

    // Delete confirmation vars
    var $confirm_text  = '';              // Message to display for confirmation              @var confirm_text
    var $script_name   = '';              // e.g., eqdkp.php                                  @var script_name
    var $uri_parameter = '';              // URI parameter                                    @var uri_parameter

    // Logging vars
    var $log_fields = array('log_id', 'log_date', 'log_type', 'log_action', 'log_ipaddress', 'log_sid', 'log_result', 'admin_id');
    var $log_values = array();            // Holds default log values                         @var log_values
    var $admin_user = '';                 // Username of admin                                @var admin_user

    function eqdkp_admin()
    {
        global $user;

        // Store our Form Validation object
        $this->fv = new Form_Validate;

        // Determine the script name based on PHP_SELF
        $this->script_name = preg_replace('#.+/(.+\.php)$#', '\1', $_SERVER['PHP_SELF']);

        $this->admin_user = ( $user->data['user_id'] != ANONYMOUS ) ? $user->data['username'] : '';
        $this->time = time();
    }

    /**
    * Build the $buttons array
    *
    * @param $buttons Array of button => name/process/auth_check values
    * @return bool
    */
    function assoc_buttons($buttons)
    {
        if ( !is_array($buttons) )
        {
            return false;
        }

        foreach ( $buttons as $code => $button )
        {
            $this->buttons[$code] = $button;
        }

        return true;
    }

    function assoc_params($params)
    {
        if ( !is_array($params) )
        {
            return false;
        }

        foreach ( $params as $code => $param )
        {
            $this->params[$code] = $param;
        }

        return true;
    }

    function process()
    {
        global $user;

        $errors_exist = false;
        $processed    = false;

        // Form has been submitted
        if ( @sizeof($_POST) > 0 )
        {
            // Sanitize our POST vars
            $_POST = sanitize_tags($_POST);

            // Check for errors
            $this->process_error_check();

            foreach ( $this->buttons as $code => $button )
            {
            	if ( isset($_POST[ $button['name'] ]) )
            	{
                		if ( isset($button['value']) ) {

											if ($_POST[ $button['name'] ] == $button['value']){
												$processed = true;
												if ( isset($button['check']) )
												{
														$user->check_auth($button['check']);
												}
												$this->last_process = $button['process'];
												$this->$button['process']();
											}
										
										} else {
											$processed = true;
											if ( isset($button['check']) )
											{
													$user->check_auth($button['check']);
											}
											$this->last_process = $button['process'];
											$this->$button['process']();
										}
                }
            }

            // Confirm is an automatic button option if confirm_delete is called
            if ( isset($_POST['confirm']) )
            {
                if ( method_exists($this, 'process_confirm') )
                {
                    $processed = true;
                    if ( isset($this->buttons['delete']['check']) )
                    {
                        $user->check_auth($this->buttons['delete']['check']);
                    }
                    $this->last_process = 'process_confirm';
                    $this->process_confirm();
                }
            }
            // Cancel is an automatic button option if confirm_delete is called
            elseif ( isset($_POST['cancel']) )
            {
                $processed = true;
                $this->last_process = 'process_cancel';
                $this->process_cancel();
            }
        }
        // No POST vars, check for GET vars and process as necessary
        foreach ( $this->params as $code => $param )
        {
            if ( isset($_GET[ $param['name'] ]) )
            {
                if ( isset($param['value']) )
                {
                    if ( $_GET[ $param['name'] ] == $param['value'] )
                    {
                        $this->process_error_check();
                        $processed = true;
                        if ( isset($param['check']) )
                        {
                            $user->check_auth($param['check']);
                        }
                        $this->last_process = $param['process'];
                        $this->$param['process']();
                    }
                }
                else
                {
                    $this->process_error_check();
                    $processed = true;
                    if ( isset($param['check']) )
                    {
                        $user->check_auth($param['check']);
                    }
                    $this->last_process = $param['process'];
                    $this->$param['process']();
                }
            }
        }

        // Nothing was processed
        if ( !$processed )
        {
            if ( (isset($this->buttons['form'])) && (is_array($this->buttons['form'])) )
            {
                if ( isset($this->buttons['form']['check']) )
                {
                    $user->check_auth($this->buttons['form']['check']);
                }
                $process = $this->buttons['form']['process'];
                $this->last_process = $process;
                $this->$process();
            }
            else
            {
                return false;
            }
        }
    }

    function process_error_check()
    {
        // Check for errors
        if ( method_exists($this, 'error_check') )
        {
            $errors_exist = $this->error_check();

            // Errors exist, redisplay the form
            if ( $errors_exist )
            {
                $process = $this->err_process;
                $this->last_process = $process;
                $this->$process();
            }
        }
    }

    // ---------------------------------------------------------
    // Default process methods
    // ---------------------------------------------------------

    function process_delete()
    {
        global $SID;

        $this->script_name = ( strpos($this->script_name, '?s=') ) ? $this->script_name : $this->script_name . $SID;

        confirm_delete($this->confirm_text, $this->uri_parameter, $this->url_id, $this->script_name);
    }

    function process_cancel()
    {
        global $SID;

        if ( empty($this->script_name) )
        {
            message_die('Cannot redirect to an empty script name.');
        }

        if ( defined('PLUGIN') )
        {
            $script_path = 'plugins/' . PLUGIN . '/';
			if ( defined('IN_ADMIN') ) { $script_path = 'plugins/' . PLUGIN . '/admin/'; }
        }
        elseif ( defined('IN_ADMIN') )
        {
            $script_path = 'admin/';
        }
        else
        {
            $script_path = '';
        }

        if ( $this->url_id )
        {
            $redirect = $script_path . $this->script_name . $SID . '&' . $this->uri_parameter . '=' . $this->url_id;
        }
        else
        {
            $redirect = $script_path . $this->script_name . $SID;
        }

        redirect($redirect);
    }

    /**
    * Set object variables
    *
    * @var $var Var to set
    * @var $val Value for Var
    * @return bool
    */
    function set_vars($var, $val = '')
    {
        if ( is_array($var) )
        {
            foreach ( $var as $d_var => $d_val )
            {
                $this->set_vars($d_var, $d_val);
            }
        }
        else
        {
            if ( empty($val) )
            {
                return false;
            }

            $this->$var = $val;
        }

        //
        // Set url_id if it hasn't already been set
        if ( !$this->url_id )
        {
            $this->url_id = ( !empty($_REQUEST[$this->uri_parameter]) ) ? $_REQUEST[$this->uri_parameter] : 0;
        }

        return true;
    }

    /**
    * Takes two variables of the same type and compares them, marking in red
    * any items that the two don't have in common
    *
    * @param $value1 The first, or 'old' value
    * @param $value2 The second, or 'new' value
    * @param $return_var Which of the two to return
    */
    function find_difference($value1, $value2, $return_var = 2)
    {
        if ( ($return_var != 1) && ($return_var != 2) )
        {
            $return_var = 2;
        }

        if ( (is_array($value1)) && (is_array($value2)) )
        {
            foreach ( $value1 as $k => $v )
            {
                $v = preg_replace("#(\\\){1,}\'#", "'", $v);

                if ( !in_array($v, $value2) )
                {
                    $value1[$k] = '<span class="negative">'.$v.'</span>';
                }
            }
            foreach ( $value2 as $k => $v )
            {
                $v = preg_replace("#(\\\){1,}\'#", "'", $v);

                if ( !in_array($v, $value1) )
                {
                    $value2[$k] = '<span class="negative">'.$v.'</span>';
                }
            }
        }
        elseif ( (!is_array($value1)) && (!is_array($value2)) )
        {
            $value1 = preg_replace("#(\\\){1,}\'#", "'", $value1);
            $value2 = preg_replace("#(\\\){1,}\'#", "'", $value2);

            if ( $value1 != $value2 )
            {
                $value2 = '<span class="negative">'.$value2.'</span>';
            }

            $value2 = addslashes($value2);
        }

        $valueX = 'value'.$return_var;

        return ${$valueX};
    }

    public function admin_die(&$message, $link_list = array()){
			global $user, $tpl, $pm, $SID;
			$message = stripmultslashes($message);
			if ( (is_array($link_list)) && (sizeof($link_list) > 0) ){
				$message .= '<br /><br />' . $this->generate_link_list($link_list);
			}
			message_die($message);
    }

    /**
    * Returns a bulleted list of links to display after an admin event
    * has been completed
    *
    * @param $links Array of links
    * @return string Link list
    */
    function generate_link_list($links)
    {
        $link_list = '<ul>';

        if ( is_array($links) )
        {
            foreach ( $links as $k => $v )
            {
                $link_list .= '<li><a href="'.$v.'">'.$k.'</a></li>';
            }
        }
        $link_list .= '</ul>';

        return $link_list;
    }

    function gen_group_key($time, $parts)
    {
    	$time = htmlspecialchars(stripslashes($time));
    	$time = substr(md5($time), 0, 10);
    	foreach($parts as $key => $part)
    	{
    		$parts[$key] = htmlspecialchars(stripslashes($part));
    		$parts[$key] = substr(md5($parts[$key]), 0, 11);
    	}
    	$group_key = $time.implode('', $parts);
        $group_key = md5(uniqid($group_key));

        return $group_key;
    }
}

/**
* Form Validate Class
* Validates various elements of a form and types of data
* Available through admin extensions as fv
*/
class Form_Validate
{
    var $errors = array();          // Error messages       @var errors

    /**
    * Constructor
    *
    * Initiates the error list
    */
    function form_validate()
    {
        $this->_reset_error_list();
    }

    /**
    * Resets the error list
    *
    * @access private
    */
    function _reset_error_list()
    {
        $this->errors = array();
    }

    /**
    * Returns the array of errors
    *
    * @return array Errors
    */
    function get_errors()
    {
        return $this->errors;
    }

    /**
    * Checks if errors exist
    *
    * @return bool
    */
    function is_error()
    {
        if ( @sizeof($this->errors) > 0 )
        {
            return true;
        }

        return false;
    }

    /**
    * Returns a string with the appropriate error message
    *
    * @param $field Field to generate an error for
    * @return string Error string
    */
    function generate_error($field)
    {
				global $eqdkp_root_path;
        if ( $field != '' )
        {
            if ( !empty($this->errors[$field]) )
            {
                $error = '<br /><img src="'.$eqdkp_root_path . 'images/error.png"
                          align="middle" alt="Error" />&nbsp;<b>'.
                          $this->errors[$field].'</b>';
                return $error;
            }
            else
            {
                return '';
            }
        }
        else
        {
            return '';
        }
    }

    /**
    * Returns the value of a variable in _POST or _GET
    *
    * @access private
    * @param $field_name Field name
    * @param $from post/get
    * @return mixed Value of the field_name
    */
    function _get_value($field_name, $from = 'post')
    {
        if ( $from == 'post' )
        {
            return ( isset($_POST[$field_name]) ) ? $_POST[$field_name] : false;
        }
        elseif ( $from == 'get' )
        {
            return ( isset($_GET[$field_name]) ) ? $_GET[$field_name] : false;
        }
    }

    // Begin validator methods
    // Note: The validation methods can accept arrays for the $field param
    // in this form: $field['fieldname'] = "Error message";
    // and the validation will be performed on each key/val pair.
    // If an array if used for validation, the method will always return true

    /**
    * Checks if a field is filled out
    *
    * @param $field Field name to check
    * @param $message Error message to insert
    * @return bool
    */
    function is_filled($field, $message = '')
    {

				if ( is_array($field) )
        {
						foreach ( $field as $k => $v )
            {
                $this->is_filled($k, $v);
            }
            return true;
        }
        else
        {
						$value = $this->_get_value($field);
            if ( trim($value) == '' )
            {
                $this->errors[$field] = $message;
                return false;
            }
            return true;
        }
				
    }

    /**
    * Checks if a field is numeric
    *
    * @param $field Field name to check
    * @param $message Error message to insert
    * @return bool
    */
    function is_number($field, $message = '')
    {
        if ( is_array($field) )
        {
            foreach ( $field as $k => $v )
            {
                $this->is_number($k, $v);
            }
            return true;
        }
        else
        {
            $value = str_replace(' ','', $this->_get_value($field));
            if ( !is_numeric($value) )
            {
                $this->errors[$field] = $message;
                return false;
            }
            return true;
        }
    }

    /**
    * Checks if a field is alphabetic
    *
    * @param $field Field name to check
    * @param $message Error message to insert
    * @return bool
    */
    function is_alpha($field, $message = '')
    {
        if ( is_array($field) )
        {
            foreach ( $field as $k => $v )
            {
                $this->is_alpha($k, $v);
            }
            return true;
        }
        else
        {
            $value = $this->_get_value($field);
            #if ( !preg_match("/^[[:alpha:][:space:]]+$/", $value) )
            # corgan
            if ( preg_match("/^[\"'-]+$/", $value) )
            {
                $this->errors[$field] = $message;
                return false;
            }
            return true;
        }
    }

    /**
    * Checks if a field is a valid hexadecimal color code (#FFFFFF)
    *
    * @param $field Field name to check
    * @param $message Error message to insert
    * @return bool
    */
    function is_hex_code($field, $message = '')
    {
        if ( is_array($field) )
        {
            foreach ( $field as $k => $v )
            {
                $this->is_hex_code($k, $v);
            }
            return true;
        }
        else
        {
            $value = $this->_get_value($field);
            if ( !preg_match("/(#)?[0-9A-Fa-f]{6}$/", $value) )
            {
                $this->errors[$field] = $message;
                return false;
            }
            return true;
        }
    }

    /**
    * Checks if a field is within a minimum and maximum range
    * NOTE: Will NOT accept an array of fields
    *
    * @param $field Field name to check
    * @param $min Minimum value
    * @param $max Maximum value
    * @param $message Error message to insert
    * @return bool
    */
    function is_within_range($field, $min, $max, $message = '')
    {
        $value = $this->_get_value($field);
        if ( (!is_numeric($value)) || ($value < $min) || ($value > $max) )
        {
            $this->errors[$field] = $message;
            return false;
        }
        return true;
    }

   /**
    * Checks if a date string is valid
    * From: http://www.smartwebby.com/PHP/datevalidation.asp
    *
    * @param $field Field name to check
    * @param $message Error message to insert
    * @return bool
    */
    function is_valid_date($field, $message = '')
    {
		$strdate = $this->_get_value($field);

		//Check whether the string is empty
		if($strdate === ''){
			$this->errors[$field] = $message;
	        return false;
		}

		//Check the length of the entered Date value
		if((strlen($strdate)<10)OR(strlen($strdate)>10)){
			$this->errors[$field] = $message;
	        return false;
		}

		//The entered value is checked for proper Date format
		if((substr_count($strdate,"."))<>2){
			$this->errors[$field] = $message;
	        return false;
		}

		$pos=strpos($strdate,".");
		$date=substr($strdate,0,($pos));
		$result=ereg("^[0-9]+$",$date,$trashed);

		if(!($result)){
			$this->errors[$field] = $message;
	        return false;
		}

		if(($date<=0)OR($date>31)){
			$this->errors[$field] = $message;
	        return false;
		}

		// Check month
		$month=substr($strdate,($pos+1),($pos));
		if(($month<=0)OR($month>12)){
			$this->errors[$field] = $message;
	        return false;
		}

		$result=ereg("^[0-9]+$",$month,$trashed);

		if(!($result)){
			$this->errors[$field] = $message;
	        return false;
		}

		// Check year
		$year=substr($strdate,($pos+4),strlen($strdate));
		$result=ereg("^[0-9]+$",$year,$trashed);

		if(!($result)){
			$this->errors[$field] = $message;
	        return false;
		}

		if(($year<1900)OR($year>2200)){
			$this->errors[$field] = $message;
	        return false;
		}

        return true;
    }

    /**
    * Checks if a field has a valid e-mail address pattern
    *
    * @param $field Field name to check
    * @param $message Error message to insert
    * @return bool
    */
    function is_email_address($field, $message = '')
    {
        if ( is_array($field) )
        {
            foreach ( $field as $k => $v )
            {
                $this->is_email_address($k, $v);
            }
            return true;
        }
        else
        {
            $value = $this->_get_value($field);
            if ( !preg_match("/^([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)+/", $value) )
            {
                $this->errors[$field] = $message;
                return false;
            }
            return true;
        }
    }

    /**
    *  Checks if a field has a valid IP address pattern
    *
    * @param $field Field name to check
    * @param $message Error message to insert
    * @return bool
    */
    function is_ip_address($field, $message = '')
    {
        if ( is_array($field) )
        {
            foreach ( $field as $k => $v )
            {
                $this->is_ip_address($k, $v);
            }
            return true;
        }
        else
        {
            $value = $this->_get_value($field);
            if ( !preg_match("/([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})/", $value) )
            {
                $this->errors[$field] = $v;
                return false;
            }
            return true;
        }
    }

    /**
    * Checks if two fields match eachother exactly
    * Used to verify the password/confirm password fields
    *
    * @param $field Field name to check
    * @param $message Error message to insert
    * @return bool
    */
    function matching_passwords($field1, $field2, $message = '')
    {
        $value1 = $this->_get_value($field1);
        $value2 = $this->_get_value($field2);

        if ( md5($value1) != md5($value2) )
        {
            $this->errors[$field1] = $message;
            return false;
        }
        return true;
    }
		
		
		function matching_emails($field1, $field2, $message = ''){
			 $value1 = $this->_get_value($field1);
       $value2 = $this->_get_value($field2);
			 
			 if ($value1 != $value2){
			 		$this->errors[$field1] = $message;
					return false;			 
			 }
				return true;
		}
}
?>