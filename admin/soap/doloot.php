<?php
/******************************
 * EQdkp
 * Copyright 2002-2005
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * doloot.php
 * begin: Tue April 19 2005
 *
 * $Id$
 *
 ******************************/

define('EQDKP_INC', true);
$eqdkp_root_path = './../../';
include_once($eqdkp_root_path . 'common.php');

if (isset($_GET['item_name']) ) { $item_name = $_GET['item_name']; } else { die("item_name not defined<br>"); }
if (isset($_GET['value']) ) { $value = $_GET['value']; } else { die("value not defined<br>"); }
if (isset($_GET['buyer']) ) { $buyer = $_GET['buyer']; } else { die("buyer not defined<br>"); }
if (isset($_GET['raid_id']) ) { $raid_id = $_GET['raid_id']; } else { die("raid_id not defined<br>"); }


// Create the client instance
$client = new soapclient( $ns .  'admin/soap/dkpsoap.php');

// Call the SOAP method
$output = $client->call('DoLoot', array('user' => $soap_user, 'password' => $soap_password, 
					'item' => $item_name, 'value' => $value, 
					'buyer' => $buyer, 'raid_id' => $raid_id));

// Display the result
print_r($output);

echo '<h2>Request</h2>';
echo '<pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
echo '<h2>Response</h2>';
echo '<pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';
?>
