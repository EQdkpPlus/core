<?php
/******************************
 * EQDKP PLUGIN: PLUSkernel
 * (c) 2006 - 2007 by WalleniuM
 * http://www.kompsoft.de
 * ------------------
 * 2007 Stefan Knaak
 *
 * php.class.php
 * started: May 7, 2007
 * $Id:$
 ******************************/

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