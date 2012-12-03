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
	global $in, $db;
    $uri_order = $in->get('o', 0.0);
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

?>