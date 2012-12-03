<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * class_email.php
 * Began: Sat January 4 2003
 * 
 * $Id$
 * 
 ******************************/

if ( !defined('EQDKP_INC') )
{
    header('HTTP/1.0 404 Not Found');
    exit;
}
 
/**
* EMail class
* Templating e-mail class inspired by phpBB
*/
class EMail
{
    /**
    * Template file
    * @var tpl_file
    */
    var $tpl_file;
    
    /**
    * EMail message
    * @var msg
    */
    var $msg;

    /**
    * @var subject
    * @var extra headers
    * @var address
    */
    var $subject, $extra_headers, $address;
    
    /**
    * Constructor
    * 
    * Make vars null
    */
    function EMail()
    {
        $this->tpl_file = null;
        $this->address = null;
        $this->msg = '';
    }
    
    /**
    * Set vars to empty strings
    */
    function reset()
    {
        $this->tpl_file = '';
        $this->address = '';
        $this->msg = '';
        $this->vars = array();
    }
    
    /**
    * Assign email address to send to
    * 
    * @param $address
    */
    function address($address)
    {
        $this->address = $address;
    }
    
    /**
    * Assign subject line
    * 
    * @param $subject
    */
    function subject($subject='')
    {
        $this->subject = $subject;
    }
    
    /**
    * Assign extra headers
    * 
    * @param $headers
    */
    function extra_headers($headers)
    {
        $this->extra_headers = $headers;
    }
    
    /**
    * Set the template to use
    * 
    * @param $template Filename
    * @param $lang Language to use
    * @return bool
    */
    function set_template($template, $lang='')
    {
        global $eqdkp, $eqdkp_root_path;
        
        $lang = ( $lang == '' ) ? $eqdkp->config['default_lang'] : $lang;
        $lang = preg_replace('/[^\w]+/', '', $lang);
        
        $this->tpl_file = $eqdkp_root_path . 'language/' . $lang . '/email/' . $template . '.txt';

        if ( !file_exists($this->tpl_file) )
        {
            message_die('Could not find email template file ' . $template);
        }

        if ( !$this->load_msg() )
        {
            message_die('Could not load email template file ' . $template);
        }

        return true;
    }
    
    /**
    * Load the message file
    * 
    * @return bool
    */
    function load_msg()
    {
        if ( $this->tpl_file == null )
        {
            message_die('No template file set');
        }

        if ( !($fd = fopen($this->tpl_file, 'r')) )
        {
            message_die('Failed opening template file');
        }

        $this->msg .= fread($fd, filesize($this->tpl_file));
        fclose($fd);

        return true;
    }
    
    /**
    * Assign template vars
    * 
    * @param $vars Array of variables
    */
    function assign_vars($vars)
    {
        $this->vars = ( empty($this->vars) ) ? $vars : $this->vars . $vars;
    }
    
    /**
    * Parse email; Replace vars with their values, find subject/charset
    * 
    * @return bool
    */
    function parse_email()
    {
        @reset($this->vars);
        while (list($key, $val) = @each($this->vars))
        {
            $$key = $val;
        }

        // Escape all quotes, else the eval will fail.
        $this->msg = str_replace ("'", "\'", $this->msg);
        $this->msg = preg_replace('#\{([a-z0-9\-_]*?)\}#is', "' . $\\1 . '", $this->msg);

        eval("\$this->msg = '$this->msg';");

        //
        // We now try and pull a subject from the email body ... if it exists,
        // do this here because the subject may contain a variable
        //
        $match = array();
        preg_match("/^(Subject:(.*?)[\r\n]+?)?(Charset:(.*?)[\r\n]+?)?(.*?)$/is", $this->msg, $match);

        $this->msg = ( isset($match[5]) ) ? trim($match[5]) : '';
        $this->subject = ( $this->subject != '' ) ? $this->subject : trim($match[2]);
        $this->encoding = ( trim($match[4]) != '' ) ? trim($match[4]) : 'iso-8859-1';

        return true;
    }
    
    /**
    * mail() the email
    * 
    * @return bool
    */
    function send()
    {
        if ( $this->address == null )
        {
            message_die('No email address set');
        }

        if ( !$this->parse_email() )
        {
            return false;
        }

        // Add date and encoding type
        $universal_extra = "MIME-Version: 1.0\nContent-type: text/plain; charset=" . $this->encoding . "\nContent-transfer-encoding: 8bit\nDate: " . gmdate('D, d M Y H:i:s', time()) . " UT\n";
        $this->extra_headers = $universal_extra . $this->extra_headers;

        $result = @mail($this->address, $this->subject, $this->msg, $this->extra_headers);

        if ( !$result )
        {
            message_die('Failed sending email');
        }

        return true;
    }
}
?>