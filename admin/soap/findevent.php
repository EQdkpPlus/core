<?php
/******************************
 * EQdkp
 * Copyright 2002-2005
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * getrp.php
 * begin: Tue April 19 2005
 *
 * $Id$
 *
 ******************************/

define('EQDKP_INC', true);
$eqdkp_root_path = './../../';
include_once($eqdkp_root_path . 'common.php');

if (isset($_GET['event']) ) {

$event_name = $_GET['event'];

} else {
die("member not defined<br>");
}

// Create the client instance
$client = new soapclient( $ns . 'admin/soap/dkpsoap.php');

// Call the SOAP method
$output = $client->call('FindEvent', array('user' => $soap_user, 'password' => $soap_password, 'name_regexp' => $event_name));

// Display the result
print_r($output);

echo '<h2>Request</h2>';
echo '<pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
echo '<h2>Response</h2>';
echo '<pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';
?>
