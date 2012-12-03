<?php



// Attempts to read the specified url and returns it as a string.

function itemstats_read_url($url)

{

	// Try cURL first. If that isn't available, check if we're allowed to
	// use fopen on URLs.  If that doesnt work, just die.

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
		die("cURL isn't installed and 'allow_url_fopen' isn't set.");
	}
	return $html_data;
}



?>