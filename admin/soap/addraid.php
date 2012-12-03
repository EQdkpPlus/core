<?php
/******************************
 * EQdkp
 * Copyright 2002-2005
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * addraid.php
 * begin: Tue April 19 2005
 *
 * $Id: addraid.php 6 2006-05-08 17:11:35Z tsigo $
 *
 ******************************/

define('EQDKP_INC', true);
$eqdkp_root_path = './../../';
include_once($eqdkp_root_path . 'common.php');

if (isset($_GET['raid']) ) {

$event_name = $_GET['raid'];

} else {
die("Event name not defined<br>");
}

// Create the client instance
$client = new soapclient( $ns . 'admin/soap/dkpsoap.php');

// Call the SOAP method
$output = $client->call('AddRaid', array('user' => $soap_user, 'password' => $soap_password, 'event_name' => $event_name));

// Display the result
print_r($output);

echo '<h2>Request</h2>';
echo '<pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
echo '<h2>Response</h2>';
echo '<pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';
?>
