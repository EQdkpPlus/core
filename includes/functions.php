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

// -----------------------------------------
// Template helpers
// -----------------------------------------

/**
 * Keep a consistent page title across the entire application
 *
 * @param     string     $title            The dynamic part of the page title, appears before " - Guild Name DKP"
 * @return    string
 */
function page_title($title = '')
{
    global $eqdkp, $user;

    $retval = '';

    $section = ( defined('IN_ADMIN') ) ? $user->lang['admin_title_prefix'] : $user->lang['title_prefix'];
    $global_title = sprintf($section, $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']);

    $retval = ( $title != '' ) ? "{$title} - " : '';
    $retval .= $global_title;

    return sanitize($retval, TAG);
}
/**
 * Determines if a folder path is valid. Ignores .svn, CVS, cache, etc.
 *
 * @param     string     $path             Path to check
 * @return    boolean
 */
function valid_folder($path)
{
    $ignore = array('.', '..', '.svn', 'CVS', 'cache', 'install', 'index.html', '.htaccess');

    if (isset($path)) 
    {
	    if ( !is_file($path) && !is_link($path) && !in_array(basename($path), $ignore) )
    	{
        	return true;
    	}    	
    }

    return false;
}

define('ENT', 1); // Escape HTML entities
define('TAG', 2); // Strip HTML tags
/**
 * Create a CSS bar graph
 *
 * @param     int        $width            Width of the bar
 * @param     string     $text             Text to show
 * @return    string
 */
function create_bar($width, $text = '')
{
    if ( strstr($width, '%') )
    {
        $width = intval(str_replace('%', '', $width));
        if ( $width > 0 )
        {

            $width = ( intval($width) <= 100 ) ? $width . '%' : '100%';
        }
    }

    $width = ($width > 100) ? 100 : $width ;
    $text = ( $text == '' ) ? $width . '%' : $text;

    return "<div class=\"graph\"><strong class=\"bar\" style=\"width: {$width}%;\">{$text}</strong></div>\n";
}

/**
* Compute the md5 hash of the filename passed
*
* @param	string	$filename	EQdkp file
* @return	string	$hash		md5 hash of file
*/

function hash_filename($filename)
{
    global $eqdkp;

    if ( isset($filename) && $filename != "" )
    {

	$hash = md5_file($filename);

    }

    return($hash);
}

/**
* Validates SOAP users before the beginning of every transaction
* returns Success or Failure based on entries in the
* eqdkp_soap_auth table
*
* @param	string	$user	SOAP user trying to auth
* @param	string	$ip_address	user client ip
* @param	string	$password	user password
* @return	string
*/

function soap_validate($user,$password)
{
   global $db, $eqdkp;

   $user = addslashes($user);
   $crypt_pass = md5($password);

   $sql = "SELECT a.auth_id, a.auth_setting, u.username, u.user_password AS password
	   FROM " . AUTH_USERS_TABLE . " a, " . USERS_TABLE ." u
	   WHERE ( a.auth_id = 34 OR a.auth_id = 35)
	   AND u.user_password = '$crypt_pass'
	   AND u.username = '$user'
	   AND u.user_id = a.user_id
	   ORDER BY auth_id";

   $result = $db->query($sql);

 $status = "ENOAUTH";

 while ( $row = $db->fetch_record($result) )
 {

   //  If row, and if password and IP address match, let them in

	if ( ($row['auth_id'] == '34') && ($row['auth_setting'] == 'Y') && ($row['password'] == $crypt_pass) ) {
		$status = "R";
	}

	if ( ($row['auth_id'] == '35') && ($row['auth_setting'] == 'Y') && ($row['password'] == $crypt_pass) ) {
                $status = "W";
        }


 }


 return($status);

}

/**
* Obviously, this function cleans any data passed
* to it of special characters
*
* @param   string  $data   Any data we wnat cleaned
* @return  string
*/
function clean_data($data)
{
       return(htmlspecialchars(stripslashes($data)));
}


/**
* Checks if a POST field value exists;
* If it does, we use that one, otherwise we use the optional database field value,
* or return a null string if $db_row contains no data
*
* @param    string  $post_field POST field name
* @param    array   $db_row     Array of DB values
* @param    string  $db_field   DB field name
* @return   string
*/
function post_or_db($post_field, $db_row = array(), $db_field = '')
{
    if ( @sizeof($db_row) > 0 )
    {
        if ( $db_field == '' )
        {
            $db_field = $post_field;
        }

        $db_value = $db_row[$db_field];
    }
    else
    {
        $db_value = '';
    }

    return ( (isset($_POST[$post_field])) || (!empty($_POST[$post_field])) ) ? $_POST[$post_field] : $db_value;
}

/**
 * Outputs a message with debugging info if needed and ends output.
 * Clean replacement for die()
 *
 * @param     string     $text             Message text
 * @param     string     $title            Message title
 * @param     string     $file             File name
 * @param     int        $line             File line
 * @param     string     $sql              SQL code
 */
function message_die($text = '', $title = '', $file = '', $line = '', $sql = '')
{
    global $db, $tpl, $eqdkp, $user, $pm;
    global $gen_simple_header, $start_time, $eqdkp_root_path;

    $error_text = '';
    if ( (DEBUG == 1) && ($db->error_die) )
    {
        $sql_error = $db->error();

        $error_text = '';

        if ( $sql_error['message'] != '' )
        {
            $error_text .= '<b>SQL error:</b> ' . $sql_error['message'] . '<br />';
        }

        if ( $sql_error['code'] != '' )
        {
            $error_text .= '<b>SQL error code:</b> ' . $sql_error['code'] . '<br />';
        }

        if ( $sql != '' )
        {
            $error_text .= '<b>SQL:</b> ' . $sql . '<br />';
        }

        if ( ($line != '') && ($file != '') )
        {
            $error_text .= '<b>File:</b> ' . $file . '<br />';
            $error_text .= '<b>Line:</b> ' . $line . '<br />';
        }
    }

    // Add the debug info if we need it
    if ( (DEBUG == 1) && ($db->error_die) )
    {
        if ( $error_text != '' )
        {
            $text .= '<br /><br /><b>Debug Mode</b><br />' . $error_text;
        }
    }

    if ( !is_object($tpl) )
    {
        die($text);
    }

    $tpl->assign_vars(array(
        'MSG_TITLE'  => ( $title != '' ) ? $title : '&nbsp;',
        'MSG_TEXT'   => ( $text  != '' ) ? $text  : '&nbsp;')
    );

    if ( !defined('HEADER_INC') )
    {
        if ( (is_object($user)) && (is_object($eqdkp)) && (@is_array($eqdkp->config)) && (isset($user->lang['title_prefix'])) )
        {
            $page_title = sprintf($user->lang['title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']) . ': '
                . (( !empty($title) ) ? $title : ' Message');
        }
        else
        {
            $page_title = $user->lang['message_title'];
        }

        $eqdkp->set_vars(array(
            'gen_simple_header' => $gen_simple_header,
            'page_title'        => $page_title,
            'template_file'     => 'message.html')
        );

        $eqdkp->page_header();
    }
    $eqdkp->page_tail();
    exit;
}

/**
 * Returns the appropriate CSS class to use based on a number's range
 *
 * @param     string     $item             The number
 * @param     boolean    $percentage       Treat the number like a percentage?
 * @return    mixed                        CSS Class / false
*/
function color_item($item, $percentage = false)
{
    if ( !is_numeric($item) )
    {
        return false;
    }

    if ( !$percentage )
    {
        if ( $item < 0 )
        {
            $class = 'negative';
        }
        elseif ( $item > 0)
        {
            $class = 'positive';
        }
        else
        {
            $class = 'neutral';
        }
    }
    elseif ( $percentage )
    {
        if ( ($item >= 0) && ($item <= 34) )
        {
            $class = 'negative';
        }
        elseif ( ($item >= 35) && ($item <= 66) )
        {
            $class = 'neutral';
        }
        elseif ( ($item >= 67) && ($item <= 100) )
        {
            $class = 'positive';
        }
        else
        {
            $class = 'neutral';
        }
    }

    return $class;
}

/*
* Switches the sorting order of a supplied array
* The array is in the format [number][0/1] (0 = the default, 1 = the opposite)
* Returns an array containing the code to use in an SQL query and the code to
* use to pass the sort value through the URI.  URI is in the format
* (number).(0/1)
*
* Also contains checks to make sure the first element is not larger than the
* sort_order array and that the second selement is either 0 or 1
*
* @param $sort_order Sorting order array
* @return array SQL/URI information
*/
function switch_order($sort_order)
{
    $uri_order = ( isset($_GET[URI_ORDER]) ) ? $_GET[URI_ORDER] : '0.0';
    $uri_order = explode('.', $uri_order);
    $element1 = ( isset($uri_order[0]) ) ? $uri_order[0] : 0;
    $element2 = ( isset($uri_order[1]) ) ? $uri_order[1] : 0;

    $array_size = count($sort_order);
    if ( $element1 > $array_size - 1 )
    {
        $element1 = $array_size - 1;
    }
    if ( $element2 > 1 )
    {
        $element2 = 0;
    }

    for ( $i = 0; $i < $array_size; $i++ )
    {
        if ( $element1 == $i )
        {
            $uri_element2 = ( $element2 == 0 ) ? 1 : 0;
        }
        else
        {
            $uri_element2 = 0;
        }
        $current_order['uri'][$i] = $i . '.' . $uri_element2;
    }

    $current_order['uri']['current'] = $element1.'.'.$element2;
    $current_order['sql'] = $sort_order[$element1][$element2];

    return $current_order;
}

/**
 * Returns a string with a list of available pages
 *
 * @param     string     $base_url         The starting URL for each page link
 * @param     int        $num_items        The number of items we're paging through
 * @param     int        $per_page         How many items to display per page
 * @param     int        $start_item       Which number are we starting on
 * @param     string     $start_variable   In case you need to call your _GET var something other than 'start'
 * @return    string
 */
function generate_pagination($base_url, $num_items, $per_page, $start_item, $start_variable='start')
{
    global $user;

    $total_pages = ceil($num_items / $per_page);

    if ( ($total_pages == 1) || (!$num_items) )
    {
        return '';
    }

    $uri_symbol = ( strpos($base_url, '?') ) ? '&amp;' : '?';

    $on_page = floor($start_item / $per_page) + 1;

    //«»

    $pagination = '';
    $pagination = ( $on_page == 1 ) ? '<b>1</b>' : '<a href="'.$base_url . $uri_symbol . $start_variable.'='.( ($on_page - 2) * $per_page).'" title="'.$user->lang['previous_page'].'" class="copy">&lt;</a>&nbsp;&nbsp;<a href="'.$base_url.'" class="copy">1</a>';

    if ( $total_pages > 5 )
    {
        $start_count = min(max(1, $on_page - 6), $total_pages - 5);
        $end_count = max(min($total_pages, $on_page + 6), 5);

        $pagination .= ( $start_count > 1 ) ? ' ... ' : ' ';

        for ( $i = $start_count + 1; $i < $end_count; $i++ )
        {
            $pagination .= ($i == $on_page) ? '<b>'.$i.'</b> ' : '<a href="'.$base_url . $uri_symbol . $start_variable.'='.( ($i - 1) * $per_page).
                           '" title="'.$user->lang['page'].' '.$i.'" class="copy">'.$i.'</a>';
            if ( $i < $end_count - 1 )
            {
                $pagination .= ' ';
            }
        }

        $pagination .= ($end_count < $total_pages ) ? ' ... ' : ' ';
    }
    else
    {
        $pagination .= ' ';

        for ( $i = 2; $i < $total_pages; $i++ )
        {
            $pagination .= ($i == $on_page) ? '<b>'.$i.'</b> ' : '<a href="'.$base_url . $uri_symbol . $start_variable.'='.( ($i - 1) * $per_page).
                           '" title="'.$user->lang['page'].' '.$i.'" class="copy">'.$i.'</a> ';
            if ( $i < $total_pages )
            {
                $pagination .= ' ';
            }
        }
    }

    $pagination .= ( $on_page == $total_pages ) ? '<b>'.$total_pages.'</b>' : '<a href="'.$base_url . $uri_symbol . $start_variable.'='.(($total_pages - 1) * $per_page) . '" class="copy">'.$total_pages.'</a>&nbsp;&nbsp;<a href="'.$base_url.'&amp;'.$start_variable.'='.($on_page * $per_page).
                   '" title="'.$user->lang['next_page'].'" class="copy">&gt;</a>';

    return $pagination;
}

/**
 * Redirects the user to another page and exits cleanly
 *
 * @param     string     $url          URL to redirect to
 * @param     bool       $return       Whether to return the generated redirect url (true) or just redirect to the page (false)
 * @return    mixed                    null, else the parsed redirect url if return is true.
 */
function redirect($url, $return = false, $extern=false)
{
    global $db, $eqdkp, $user;

    if (!$extern) 
    {
	    $protocol = 'http://';
	    $server = preg_replace('/^\/?(.*?)\/?$/', '\1', trim($eqdkp->config['server_name']));
	    $port = ( $eqdkp->config['server_port'] != 80 ) ? ':' . trim($eqdkp->config['server_port']) . '/' : '/';
	    $script = preg_replace('/^\/?(.*?)\/?$/', '\1', trim($eqdkp->config['server_path']));
	
	    $url      = preg_replace('/^\/?(.*?)\/?$/', '\1', trim($url));
	    $url      = str_replace('&amp;', '&', $url);	
	    
	    #$location = $protocol . $server . $port . '/' . (!empty($script) ? $script . '/' : '') . $url;
    	$location = $protocol . $server . $port . (!empty($script) ? $script . '/' : '') . $url;
    }else 
    {
    	$location = $url ;
    }

    if( $return )
    {
        return $url;
    }

    

   /* if ( @preg_match('/Microsoft|WebSTAR|Xitami/', getenv('SERVER_SOFTWARE')) )
    {
        header('Refresh: 0; URL=' . $location);

        echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">';
        echo '<html>';
        echo '<head>';
        echo '<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">';
        echo '<meta http-equiv="refresh" content="0; url=' . str_replace('&', '&amp;', $location) .'">';
        echo '<title>Redirect</title>';
        echo '</head>';
        echo '<body>';
        echo '<div align="center">If your browser does not support meta redirection, please click <a href="' . str_replace('&', '&amp;', $location) . '">here</a> to be redirected</div>';
        echo '</body>';
        echo '</html>';

        exit;
    }*/

    if ( isset($db) )
    {
        $db->close_db();
    }

    //header('Location: ' . $location);
    echo "<script>window.location.href = '".$location."';</script>";
    exit;
}

/**
* Outputs a message asking the user if they're sure they want to delete something
*
* @param $confirm_text Confirm message
* @param $uri_parameter URI_RAID, URI_NAME, etc.
* @param $parameter_value Value of the parameter
* @param $action Form action
*/
function confirm_delete($confirm_text, $uri_parameter, $parameter_value, $action = '')
{
    global $db, $tpl, $eqdkp, $user, $pm;
    global $gen_simple_header, $eqdkp_root_path;

    if ( !defined('HEADER_INC') )
    {
        $eqdkp->set_vars(array(
            'page_title' => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']),
            'gen_simple_header' => $gen_simple_header,
            'template_file' => 'admin/confirm_delete.html')
        );

        $eqdkp->page_header();
    }

    $tpl->assign_vars(array(
        'F_CONFIRM_DELETE_ACTION' => ( !empty($action) ) ? $action : $_SERVER['PHP_SELF'],

        'URI_PARAMETER' => $uri_parameter,
        'PARAMETER_VALUE' => $parameter_value,

        'L_DELETE_CONFIRMATION' => $user->lang['delete_confirmation'],
        'L_CONFIRM_TEXT' => $confirm_text,
        'L_YES' => $user->lang['yes'],
        'L_NO' => $user->lang['no'])
    );

    $eqdkp->page_tail();

    exit;
}

/**
* Parses a news post containing BBCode and replaces the code with HTML
*
* @param $message Text message to parse
* @param $parse_quotes Whether or not to parse quote tags
*/
function news_parse(&$message, $parse_quotes = true)
{
    global $user, $eqdkp, $SID, $pm;
    global $eqdkp_hooks,$conf_plus;

    // Figure out which quote class to use
    $quote_class = ( $eqdkp->switch_row_class(false) == 'row1' ) ? '1' : '2';

    // Pad message with a space so we can match things at the start of the first line
    $message = ' ' . $message;
    news_make_clickable($message);
    $message = preg_replace("#(\\\){1,}(\"|\'|\&quot;|\&\#039)#", "\"", $message);

    $quote_open = '<table width="90%" border="0" cellspacing="0" cellpadding="3" align="center"><tr><td class="quote'.$quote_class.'"><b>'.$user->lang['quote'].':</b></td></tr><tr><td class="quote'.$quote_class.'">';
    $quote_close = '</td></tr></table>';

    // Patterns and replacements
    $patterns = array();
    $replacements = array();

    
    // [img]image_url[/img]
    $patterns[0] = "#\[img\](.*?)\[/img\]#si";
    if ($conf_plus['pk_air_enable']) 
    {
		$replacements[0] = "<a href=\"\\1\" rel=\"lytebox\" class=\"image-resize\"><img src=\"\\1\" alt=\"User-posted image\" /></a>";
	}
 	else 
 	{
    	$replacements[0] = "<img src=\"\\1\" alt=\"User-posted image\" />";
 	}	

    // [url]xxxx://www.example.com[/url]
    $patterns[1] = "#\[url\]([a-z]+?://){1}([a-z0-9\-\.,\?!%\*_\#:;~\\&$@\/=\+]+)\[/url\]#si";
    $replacements[1] = "<a href=\"\\1\\2\">\\1\\2</a>";

    // [url]www.example.com[/url]
    $patterns[2] = "#\[url\]([a-z0-9\-\.,\?!%\*_\#:;~\\&$@\/=\+]+)\[/url\]#si";
    $replacements[2] = "<a href=\"http://\\1\">\\1</a>";

    // [url=xxxx://www.example.com]Example[/url]
    $patterns[3] = "#\[url=([a-z]+?://){1}([a-z0-9\-\.,\?!%\*_\#:;~\\&$@\/=\+]+)\](.*?)\[/url\]#si";
    $replacements[3] = "<a href=\"\\1\\2\">\\3</a>";

    // [url=www.example.com]Example[/url]
    $patterns[4] = "#\[url=([a-z0-9\-\.,\?!%\*_\#:;~\\&$@\/=\+]+)\](.*?)\[/url\]#si";
    $replacements[4] = "<a href=\"http://\\1\">\\2</a>";

    // [url=mailto:user@example.com]E-Mail[/url]
    $patterns[5] = "#\[url=mailto:([a-zA-Z0-9]+[\.a-zA-Z0-9_-]*@[a-zA-Z0-9_-]+\.[a-zA-Z0-9_-]+)\](.*?)\[/url\]#si";
    $replacements[5] = "<a href=\"mailto:\\1\">\\2</a>";

    // [item]name[/item]
    //$patterns[6] = "#\[item\](.*?)\[/item\]#si";
    //$replacements[6] = "<iframe name=\"item_stats\" src=\"http://www.crusadersvalorous.org/items/index.php?linked=1&amp;news=true&amp;item=\\1\" width=\"450\" height=\"260\" scrolling=\"no\" frameborder=\"0\" marginheight=\"0\" marginwidth=\"0\"></iframe>";



    $count = sizeof($patterns);
    if ( @is_object($pm) )
    {
        $plugin_news = $pm->do_hooks('news_parse');
        foreach ( $plugin_news as $news_array )
        {
            foreach ( $news_array as $find_replace )
            {
                if ( (isset($find_replace['patterns'])) && (isset($find_replace['replacements'])) )
                {
                    $count++;
                    $patterns[$count] = $find_replace['patterns'];
                    $replacements[$count] = $find_replace['replacements'];
                }
            }
        }
    }

    $message = preg_replace($patterns, $replacements, $message);

    $message = str_replace('[b]', '<b>', $message);
    $message = str_replace('[/b]', '</b>', $message);

    $message = str_replace('[i]', '<i>', $message);
    $message = str_replace('[/i]', '</i>', $message);

    $message = str_replace('[u]', '<u>', $message);
    $message = str_replace('[/u]', '</u>', $message);

    $message = str_replace('[center]', '<center>', $message);
    $message = str_replace('[/center]', '</center>', $message);

    if ( $parse_quotes )
    {
        $message = str_replace('[quote]', $quote_open, $message);
        $message = str_replace('[/quote]', $quote_close, $message);
    }

    // Undo our pad
    $message = substr($message, 1);

    return $message;
}

/**
* Replace "magic URLs" of form http://xxx.example.com, www.example.com, user@example.com
*
* @param $message Message to parse
*/
function news_make_clickable(&$message)
{
    global $eqdkp;

    $patterns = array();
    $replacements = array();

    $server_protocol = 'https://';
    $server_port = ( $eqdkp->config['server_port'] != 80 ) ? ':' . trim($eqdkp->config['server_port']) . '/' : '/';

    $match = array();
    $replace = array();

    // relative urls for this board
    $match[] = '#' . $server_protocol . trim($eqdkp->config['server_name']) . $server_port . preg_replace('/^\/?(.*?)(\/)?$/', '\1', trim($eqdkp->config['server_path'])) . '/([^\t\n\r <"\']+)#i';
    $replace[] = '<!-- l --><a href="\1" target="_blank">\1</a><!-- l -->';

    // matches a xxxx://aaaaa.bbb.cccc. ...
    $match[] = '#(^|[\n ])([\w]+?://.*?[^\t\n\r<"]*)#ie';
    $replace[] = "'\\1<!-- m --><a href=\"\\2\" target=\"_blank\">' . ( ( strlen(str_replace(' ', '%20', '\\2')) > 55 ) ?substr(str_replace(' ', '%20', '\\2'), 0, 39) . ' ... ' . substr(str_replace(' ', '%20', '\\2'), -10) : str_replace(' ', '%20', '\\2') ) . '</a><!-- m -->'";

    // matches a "www.xxxx.yyyy[/zzzz]" kinda lazy URL thing
    $match[] = '#(^|[\n ])(www\.[\w\-]+\.[\w\-.\~]+(?:/[^\t\n\r<"]*)?)#ie';
    $replace[] = "'\\1<!-- w --><a href=\"http://\\2\" target=\"_blank\">' . ( ( strlen(str_replace(' ', '%20', '\\2')) > 55 ) ? substr(str_replace(' ', '%20', '\\2'), 0, 39) . ' ... ' . substr(str_replace(' ', '%20', '\\2'), -10) : str_replace(' ', '%20', '\\2') ) . '</a><!-- w -->'";

    // matches an email@domain type address at the start of a line, or after a space.
    $match[] = '#(^|[\n ])([a-z0-9\-_.]+?@[\w\-]+\.([\w\-\.]+\.)?[\w]+)#ie';
    $replace[] = "'\\1<!-- e --><a href=\"mailto:\\2\">' . ( ( strlen('\\2') > 55 ) ?substr('\\2', 0, 39) . ' ... ' . substr('\\2', -10) : '\\2' ) . '</a><!-- e -->'";

    $message = preg_replace($match, $replace, $message);
}

function stripmultslashes($string)
{
    $string = preg_replace("#(\\\){1,}(\"|\&quot;)#", '"', $string);
    $string = preg_replace("#(\\\){1,}(\'|\&\#039)#", "'", $string);

    return $string;
}

function sanitize_tags($data)
{
    if ( is_array($data) )
    {
        foreach ( $data as $k => $v )
        {
            $data[$k] = sanitize_tags($v);
        }
    }
    else
    {
        $data = str_replace('<', '&lt;', $data);
        $data = str_replace('>', '&gt;', $data);
    }

    return $data;
}

function undo_sanitize_tags($data)
{
    if ( is_array($data) )
    {
        foreach ( $data as $k => $v )
        {
            $data[$k] = undo_sanitize_tags($v);
        }
    }
    else
    {
        $data = str_replace('&lt;', '<', $data);
        $data = str_replace('&gt;', '>', $data);
    }

    return $data;
}

/**
* Applies htmlspecialchars to an array of data
*
* @deprec sanitize_tags
* @param $data
* @return array
*/
function htmlspecialchars_array($data)
{
    if ( is_array($data) )
    {
        foreach ( $data as $k => $v )
        {
            $data[$k] = ( is_array($v) ) ? htmlspecialchars_array($v) : htmlspecialchars($v);
        }
    }

    return $data;
}

function htmlspecialchars_remove($data)
{
    $find    = array('#&amp;#', '#&quot;#', '#&\#039;#', '#&lt;#', '#&gt;#');
    $replace = array('&', '"', '\'', '<', '>');

    $data = preg_replace($find, $replace, $data);

    return $data;
}

/**
* Highlight certain keywords in a SQL query
*
* @param $sql Query string
* @return string Highlighted string
*/
function sql_highlight($sql)
{
    global $table_prefix;

    // Make table names bold
    $sql = preg_replace('/' . $table_prefix .'(\S+?)([\s\.,]|$)/', '<b>' . $table_prefix . "\\1\\2</b>", $sql);

    // Non-passive keywords
    $red_keywords = array('/(INSERT INTO)/','/(UPDATE\s+)/','/(DELETE FROM\s+)/', '/(CREATE TABLE)/', '/(IF (NOT)? EXISTS)/',
                          '/(ALTER TABLE)/', '/(CHANGE)/');
    $red_replace = array_fill(0, sizeof($red_keywords), '<span class="negative">\\1</span>');
    $sql = preg_replace($red_keywords, $red_replace, $sql);

    // Passive keywords
    $green_keywords = array('/(SELECT)/','/(FROM)/','/(WHERE)/','/(LIMIT)/','/(ORDER BY)/','/(GROUP BY)/',
                            '/(\s+AND\s+)/','/(\s+OR\s+)/','/(BETWEEN)/','/(DESC)/','/(LEFT JOIN)/');

    $green_replace = array_fill(0, sizeof($green_keywords), '<span class="positive">\\1</span>');
    $sql = preg_replace($green_keywords, $green_replace, $sql);

    return $sql;
}

/**
 * Option Selected value method
 * Returns ' checked="checked"' for use in checkbox/radio <input> tags if $condition is true
 */
function option_selected($condition)
{
    return ( $condition ) ? ' selected="selected"' : '';
}

/**
 * Option Checked value method
 * Returns ' selected="selected"' for use in <option> tags if $condition is true
 */
function option_checked($condition)
{
    return ( $condition ) ? ' checked="checked"' : '';
}

?>
