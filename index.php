<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * index.php
 * Began: Mon December 23 2002
 * 
 * $Id$
 * 
 ******************************/

define('EQDKP_INC', true);
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');

$mode = ( isset($_GET['mode']) ) ? $_GET['mode'] : 'redirect';

switch ( $mode )
{
    case 'redirect':
        // Fall through
    default:
        if ( isset($eqdkp->config['start_page']) )
        {
            $start_page = preg_replace('#\?' . URI_SESSION . '=([A-Za-z0-9]{32})?#', $SID, $eqdkp->config['start_page']);
            redirect($start_page);
        }
        else
        {
            redirect('viewnews.php' . $SID);
        }
        
        break;
}

// Thassit
?>
