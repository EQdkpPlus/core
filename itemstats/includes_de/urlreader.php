<?php

// Attempts to read the specified url and returns it as a string.
function itemstats_read_url($url)
{
	// Try cURL first. If that isnt available, check if we're allowed to
	// use fopen on URLs.  If that doesn't work, just die.
	if (function_exists('curl_init'))
	{
		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		#curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		if (!(ini_get("safe_mode") || ini_get("open_basedir"))) {
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$html_data = curl_exec($ch);

		curl_close($ch);
	}
	else if (ini_get('allow_url_fopen') == 1)
	{
		$html_data = file_get_contents($url);
	}
	else
	{
		die("Die PHP Funktionen <a href=http://php3.de/manual/de/ref.curl.php>cURL</a> und <a href=http://de3.php.net/manual/de/function.fopen.php> fopen </a> stehen auf Deinem Webspace NICHT zur Verf&uuml;gung ! <br>
			Wende Dich bitte an Deinen Webhoster, damit dieser die Funktionen zur Verf&uuml;gung stellt. Alternativ k&ouml;nnt ihr eine vorhandene
			<a href='http://forums.eqdkp.com/index.php?showtopic=5074'>Itemcache.sql</a> benutzen.");
	}

	return $html_data;
}

?>