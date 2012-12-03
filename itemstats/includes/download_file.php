<?php

/*
 * download_file
 * description: downloads a binary file
 *
 */

// downloads a binary file using curl/fopen/fsockopen
function download_file($file_source, $file_target)
{       
	if (function_exists('curl_init')) 
	{
		// curl available
		
		if (($ch = curl_init($file_source)) === FALSE) { return false; }
		if (($fp = fopen($file_target, 'wb')) === FALSE) { return false; }
		
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		
		// Begin transfer
		if (!curl_exec($ch)) { 
			curl_close($ch);
			fclose($fp);
			return false; 
		};
		curl_close($ch);
		fclose($fp);
		return true;

	} else if (ini_get('allow_url_fopen') == 1) {
		// curl unavailable, fopen is

		$file_source = str_replace(' ', '%20', html_entity_decode($file_source)); // fix url format
		if (file_exists($file_target)) { chmod($file_target, 0777); } // add write permission
		
		// Begin transfer
		if (($rh = fopen($file_source, 'rb')) === FALSE) { return false; } // fopen() handles
		if (($wh = fopen($file_target, 'wb')) === FALSE) { return false; } // error messages.
		while (!feof($rh))
		{
			// unable to write to file, possibly because the harddrive has filled up
			if (fwrite($wh, fread($rh, 1024)) === FALSE) { 
				fclose($rh); 
				fclose($wh); 
				return false; 
			}
		}
		
		// Finished without errors
		fclose($rh);
		fclose($wh);
		return true;
	} else {
		// curl and fopen unavailable, try fsockopen

		$url_array = parse_url($file_source);
		$fp = fsockopen($url_array['host'], 80, $errno, $errstr, 5); 

		if (!fp) {
			die("cURL isn't installed, 'allow_url_fopen' isn't set and socket opening failed. Socket failed because: <br /><br /> $errstr ($errno)");
		} else {
			if (file_exists($file_target)) { chmod($file_target, 0777); } // add write permission
			if (($wh = fopen($file_target, 'wb')) === FALSE) { return false; }

			// send the request for the file
			$out = "GET " . $url_array[path] . "?" . $url_array[query] ." HTTP/1.0\r\n";
			$out .= "Host: " . $url_array[host] . " \r\n";
			$out .= "Connection: Close\r\n\r\n";
			fwrite($fp, $out);

			// Begin transfer
			while (!feof($fp)) {
        			// unable to write to file, possibly because the harddrive has filled up
				if (fwrite($wh, fgets($fp, 128)) === FALSE) { 
					fclose($fp); 
					fclose($wh); 
					return false; 
				}
    			}
			
			// Finished without errors
			fclose($fp);
			fclose($wh);
			return true;
		}
	}
	return false;
}

?>