<?php
/******************************
 * EQdkp
 * Copyright 2002-2005
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * getlastraid.php
 * begin: Tue April 19 2005
 *
 * $Id$
 *
 ******************************/

define('EQDKP_INC', true);
$eqdkp_root_path = './../../';
include_once($eqdkp_root_path . 'common.php');


// Create the client instance
$client = new soapclient($ns . 'admin/soap/dkpsoap.php');

// Call the SOAP method
$output = $client->call('GetLastRaid', array('user' => $soap_user, 'password' => $soap_password));

// Display the result
print_r($output);

echo '<h2>Request</h2>';
echo '<pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
echo '<h2>Response</h2>';
echo '<pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';
?>
