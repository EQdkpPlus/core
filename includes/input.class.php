<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * functions.php
 * begin: Tue December 17 2002
 *
 * $Id$
 *
 ******************************/

if ( !defined('EQDKP_INC') )
{
    header('HTTP/1.0 404 Not Found');
    exit;
}

class Input
{
    /**
     * Stores input variables after any cleaning's been done to them, to
     * prevent overhead of multiple $in->string('var') calls, for example.
     * 
     * @var array
     * @access private
     */
    var $_cache = array();
    
    /**
     * Determines whether or not to cache fetched values.
     *
     * @var bool
     * @access private
     */
    var $_caching = true;
    
    /**
     * Get an input variable from a superglobal. POST > SESSION > GET
     *
     * @param string $key Input key
     * @param string $default Default variable to return if $key is not set
     * @return mixed
     * @access private
     */
    function _get($key, $default = null)
    {
        $retval = $default;
        
        if ( isset($_GET[$key]) )
        {
            $retval = $_GET[$key];
        }
        
        if ( isset($_POST[$key]) )
        {
            $retval = $_POST[$key];
        }
        elseif ( isset($_SESSION[$key]) )
        {
            // NOTE: This elseif is intentional. We don't want session data overwriting post.
            $retval = $_SESSION[$key];
        }
        
        if ( isset($_COOKIE[$key]) )
        {
            $retval = $_COOKIE[$key];
        }
        
        return $retval;
    }

    /**
     * A shortcut method to request an input variable. Calls the appropriate
     * type-specifc method based on the variable type of $default
     * 
     * Note that our most-used, and default type, is a string.
     * <code>
     * // Return a cleaned integer value for the input variable 'id', or 0 if not set
     * $in->get('id', 0);
     * 
     * // Return true if the input variable 'id' is set, or false if not set
     * $in->get('id', false);
     * 
     * // Return a cleaned string value for the input variable 'name', or '' if not set
     * $in->get('name');
     * </code>
     * 
     * @param string $key Input key
     * @param mixed $default Default variable to return if $key is not set. This also determines the type of data cleaning performed.
     * @return mixed
     */
    function get($key, $default = '')
    {
        $type = gettype($default);
        
        if ( method_exists($this, $type) )
        {
            return $this->$type($key, $default);
        }
        else
        {
            trigger_error("Input accessor method for variables of type <b>{$type}</b> with an input key of <b>{$key}</b> doesn't exist", E_USER_NOTICE);
            return $this->_get($key, $default);
        }
    }
    
    /**
     * Clean and fetch an input variable that is an array, for example an array
     * of checkbox IDs. Depending on $type, the appropriate cleaning method will be
     * called on each element.
     * 
     * @param string $key Input key
     * @param string $type String-based variable type ('int', 'string', etc.)
     * @param string $max_depth Maximum depth to recurse to
     * @return array
     */
    function getArray($key, $type, $max_depth = 10)
    {
        $retval = array();
        
        if ( isset($this->_cache[$key]) )
        {
            $retval = $this->_cache[$key];
        }
        else
        {
            $input  = $this->_get($key, $retval);
            $retval = $this->_recurseClean($input, $type, $max_depth);
            
            if ( $this->_caching && count($retval) > 0 )
            {
                $this->_cache[$key] = $retval;
            }
        }
        
        return $retval;
    }
    
    /**
     * Checks if $key exists as an input value.
     *
     * @param string $key Input key
     * @return boolean
     */
    function exists($key)
    {
        $retval = false;
        
        $value = $this->_get($key);
        if ( !is_null($value) )
        {
            $retval = true;
        }
        
        return $retval;
    }
    
    // ----------------------------------------------------
    // Data type methods
    // ----------------------------------------------------
    
    /**
     * Note that this method is special in that it doesn't actually return the
     * value of the input, rather the result of isset() on the input key.
     *
     * @param string $key Input key
     * @param null $default not used
     * @return boolean true if the key exists, false if not
     */
    function boolean($key, $default = 'ignored')
    {
        return $this->exists($key);
    }
    
    /**
     * Alias to {@link float}, see {@link http://us2.php.net/manual/en/function.gettype.php}
     * 
     * @see float
     */
    function double($key, $default = 0.00)
    {
        return $this->float($key, $default);
    }
    
    /**
     * Fetch an input variable as a float, or return $default
     *
     * @param string $key Input key
     * @param float $default The default variable to return if $key is not set
     * @return float Variable cleaned by {@link _cleanFloat}, or $default
     */
    function float($key, $default = 0.00)
    {
        if ( isset($this->_cache[$key]) )
        {
            $retval = $this->_cache[$key];
        }
        else
        {
            $retval = $this->_cleanFloat($this->_get($key, $default));
            
            if ( $this->_caching && $retval != $default )
            {
                $this->_cache[$key] = $retval;
            }
        }
        
        return $retval;
    }
    
    /**
     * Fetch an input variable as an integer, or return $default
     *
     * @param string $key Input key
     * @param int $default The default variable to return if $key is not set
     * @return int Variable cleaned by {@link _cleanInt}, or $default
     */
    function int($key, $default = 0)
    {
        if ( isset($this->_cache[$key]) )
        {
            $retval = $this->_cache[$key];
        }
        else
        {
            $retval = $this->_cleanInt($this->_get($key, $default));
            
            if ( $this->_caching && $retval != $default )
            {
                $this->_cache[$key] = $retval;
            }
        }
        
        return $retval;
    }
    
    /**
     * Alias to {@link int}
     * 
     * @see int
     */
    function integer($key, $default = 0)
    {
        return $this->int($key, $default);
    }
    
    /**
     * Fetch an input variable as a MD5 or SHA1 hash string, or return $default
     *
     * @param string $key Input key
     * @param string $default The default variable to return if $key is not set
     * @return string Variable cleaned by {@link _cleanHash}, or $default
     */
    function hash($key, $default = '')
    {
        if ( isset($this->_cache[$key]) )
        {
            $retval = $this->_cache[$key];
        }
        else
        {
            $retval = $this->_cleanHash($this->string($key, $default));
            
            if ( $this->_caching && $retval != $default )
            {
                $this->_cache[$key] = $retval;
            }
        }
        
        return $retval;
    }
    
    /**
     * Fetch an input variable as a string, or return $default
     *
     * @param string $key Input key
     * @param string $default The default variable to return if $key is not set
     * @return string Variable cleaned by {@link _cleanString}, or $default
     */
    function string($key, $default = '')
    {
        if ( isset($this->_cache[$key]) )
        {
            $retval = $this->_cache[$key];
        }
        else
        {
            $retval = $this->_cleanString($this->_get($key, $default));
            
            if ( $this->_caching && $retval != $default )
            {
                $this->_cache[$key] = $retval;
            }
        }
        
        return $retval;
    }
    
    /**
     * Fail-safe method to prevent a user passing a null type as a default value
     * to {@link get}, which may produce unexpected results.
     *
     * @param string $key Input key
     * @param null $default Ignored
     * @return void
     * @ignore
     */
    function NULL($key, $default = null)
    {
        trigger_error("Tried to get a null variable type for <b>{$key}</b>", E_USER_NOTICE);
    }
    
    // ----------------------------------------------------
    // Data cleaning methods
    // ----------------------------------------------------
    
    /**
     * Perform float-specific cleaning on an input variable.
     *
     * @param mixed $value Value to be cleaned
     * @return float
     * @access private
     */
    function _cleanFloat($value)
    {
        $value = floatval($value);
        
        return $value;
    }
    
    /**
     * Perform integer-specific cleaning on an input variable.
     *
     * @param mixed $value Value to be cleaned
     * @return int
     * @access private
     */
    function _cleanInt($value)
    {
        $value = intval($value);
        
        return $value;
    }
    
    /**
     * Perform MD5- or SHA1-specific cleaning on an input variable.
     *
     * @param mixed $value Value to be cleaned
     * @return string
     * @access private
     */
    function _cleanHash($value)
    {
        $value = substr(preg_replace('/[^0-9A-Za-z]/', '', $this->_cleanString($value)), 0, 40);
        
        return $value;
    }
    
    /**
     * Perform string-specific cleaning on an input variable. Forces strval(),
     * urldecode(), and stripslashes() as needed.
     *
     * @param mixed $value Value to be cleaned
     * @return string
     * @access private
     */
    function _cleanString($value)
    {
        $value = strval($value);
        $value = urldecode($value);
        $value = ( get_magic_quotes_gpc() ) ? stripslashes($value) : $value;
        
        return $value;
    }
    
    /**
     * Recursively clean an array, stopping if the number of iterations is higher
     * than $max_depth. This prevents a malicious user from submitting an array
     * with an unusually high number of dimensions, potentially overloading
     * the server.
     * 
     * @param $array Array to clean
     * @param $type The type of data in each element
     * @param $max_depth The maximum number of iterations to run
     * @param $cur_depth The current number of iterations run
     * @access private
     */
    function _recurseClean($array, $type, $max_depth, $cur_depth = 0)
    {
        $cleaner = '_clean' . ucwords(strtolower($type));
        
        if ( !is_array($array) )
        {
            return $this->$cleaner($array);
        }
        
        if ( $cur_depth >= $max_depth )
        {
            return $array;
        }
        
        foreach ( $array as $k => $v )
        {
            if ( is_array($v) )
            {
                $array[$k] = $this->_recurseClean($v, $type, $max_depth, $cur_depth++);
            }
            else
            {
                $array[$k] = $this->$cleaner($v);
            }
        }
        
        return $array;
    }
}
?>