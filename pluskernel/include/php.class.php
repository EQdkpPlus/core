<?php
/******************************
 * EQDKP PLUGIN: PLUSkernel
 * (c) 2007 by EQDKP Plus Dev Team
 * originally written by S.Knaak
 * http://www.eqdkp-plus.com
 * ------------------
 * php.class.php
 * started: May 7, 2007
 * $Id$
 ******************************/

if ( !defined('EQDKP_INC') ){
    header('HTTP/1.0 404 Not Found');exit;
}

class phpAdditions EXTENDS EQdkp_Plugin
{

	function get_php_setting($val, $colour=0, $yn=1) {
		$r =  (ini_get($val) == '1' ? 1 : 0);

		if ($colour) {
			if ($yn) {
				$r = $r ? '<span style="color: green;">ON</span>' : '<span style="color: red;">OFF</span>';
			} else {
				$r = $r ? '<span style="color: red;">ON</span>' : '<span style="color: green;">OFF</span>';
			}

			return $r;
		} else {
			return $r ? 'ON' : 'OFF';
		}
	}

	function get_curl_setting($colour=0)
	{
		$r =  (function_exists('curl_version') ? 1 : 0);
		if ($colour) {
				$r = $r ? '<span style="color: green;">ON</span> ('.curl_version().')' : '<span style="color: red;">OFF</span>';
			return $r;
		} else {
			return $r ? 'ON' : 'OFF';
		}
	}

	function check_PHP_Function($_function,$colour=0)
	{
		$r =  (function_exists($_function) ? 1 : 0);
		if ($colour) {
				$r = $r ? '<span style="color: green;">ON</span>' : '<span style="color: red;">OFF</span>';
			return $r;
		} else {
			return $r ? 'ON' : 'OFF';
		}
	}


}
?>
