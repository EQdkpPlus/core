<?php

$strFiles = file_get_contents('Less.php.combine');
$arrFiles = explode("\n", $strFiles);

file_put_contents("!Less.php", "<?php\n\n");

$arrDone = array();

foreach($arrFiles as $val){
	if(trim($val) == "") continue;
	
	if(is_dir($val)){
		$string = scan_directory($val);
		file_put_contents("!Less.php", str_replace("<?php", "", $string), FILE_APPEND);
	} else {
		if(in_array($val, $arrDone)){
			continue;
		} else {
			$arrDone[] = $val;
		}
		
		$string = "/* ".$val." */ \n".file_get_contents($val);
		file_put_contents("!Less.php", str_replace("<?php", "", $string), FILE_APPEND);
	}
}

function scan_directory($strDir){
	global $arrDone;
	
	$arrFiles = scandir($strDir);
	$string = "";
	var_dump($strDir);
	
	foreach($arrFiles as $val){
		//var_dump($val);
		
		if($val == "." || $val == ".." || $val == "" || $val == "/") continue;
		
		if(is_dir($strDir.'/'.$val)){
			$string .= scan_directory($strDir.'/'.$val);
		} else {
			if(in_array($strDir.'/'.$val, $arrDone)){
				continue;
			} else {
				$arrDone[] = $strDir.'/'.$val;
			}
			
			var_dump($strDir.'/'.$val);
			$string .= "/* ".$strDir.'/'.$val." */ \n".file_get_contents($strDir.'/'.$val);
		}
	}
	
	return $string;
}