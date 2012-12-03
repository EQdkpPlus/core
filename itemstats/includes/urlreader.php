<?php

// Attempts to read the specified url and returns it as a string.
function itemstats_read_url($url,$language="en")
{       
 	// Try cURL first. If that isn't available, check if we're allowed to
	// use fopen on URLs.  If that doesn't work, just die.
	if (function_exists('curl_init'))
	{
		$ch = @curl_init($url);
		@curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		@curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows;
		U;
		Windows NT 5.1;
		fr;
		rv:1.8.1.2) Gecko/20070219 Firefox/2.0.0.2");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept-Language: ".$language.",".$language."-".$language.";"));
		$html_data = curl_exec($ch);
		@curl_close($ch);
	}
	else if (ini_get('allow_url_fopen') == 1)
	{
                $opts = array (
                  'http'=>array (
                    'method' => 'GET',
                    'header' => "Accept-language: ".$language.",".$language."-".$language."\r\n"
                    )
                );

                $context = stream_context_create($opts);

                // Envoie une requête HTTP à www.example.com
                // avec des en-têtes supplémentaires
                $html_data = @file_get_contents($url, false, $context);
	}
	else
	{
        // Thanks to Aki Uusitalo
		$url_array = parse_url($url);

		$fp = fsockopen($url_array['host'], 80, $errno, $errstr, 5); 

		if (!fp)
        {
			die("cURL isn't installed, 'allow_url_fopen' isn't set and socket opening failed. Socket failed because: <br /><br /> $errstr ($errno)");

		}
        else
        {
			$out = "GET " . $url_array[path] . "?" . $url_array[query] ." HTTP/1.0\r\n";
			$out .= "Host: " . $url_array[host] . " \r\n";
			$out .= "Accept-language: ".$language.",".$language."-".$language."\r\n";
			$out .= "Connection: Close\r\n\r\n";

			fwrite($fp, $out);

			// Get rid of the HTTP headers
			while ($fp && !feof($fp))
            {
				$headerbuffer = fgets($fp, 1024);
				if (urlencode($headerbuffer) == "%0D%0A")
                {
                    // We've reached the end of the headers
					break;
				}
			}

			$html_data = '';
			// Read the raw data from the socket in 1kb chunks
			// Hopefully, it's just HTML.
			
			while (!feof($fp))
            {
				$html_data .= fgets($fp, 1024);
			}
			fclose($fp);
		}        
    }
	return $html_data;
}

?>