<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2016 EQdkp-Plus Developer Team
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU Affero General Public License as published
 *	by the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU Affero General Public License for more details.
 *
 *	You should have received a copy of the GNU Affero General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

$url = '';
if (isset($_GET['url'])) $url = htmlspecialchars(str_replace(';', '%3B', trim(urldecode($_GET['url']))));
if (empty($url)) exit;
// check url
$testURL = preg_replace('/[^a-z0-9:\/]+/', '', strtolower($url));
if (strpos($testURL, 'script:') !== false || !preg_match('~^https?://~', $testURL)) {
	exit;
}

$strKey = $_GET['key'];
$url = filter_var($url, FILTER_SANITIZE_URL);

include_once 'config.php';
$strCompareKey = substr(sha1($encryptionKey.'|'.urldecode($_GET['url'])), 0, 12);
if($strKey !== $strCompareKey) exit;

 ?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
	<title>Dereferrer</title>
	<meta http-equiv="refresh" content="0;URL=<?php echo $url; ?>">
</head>
<body>
	<p><a href="<?php echo $url; ?>"><?php echo $url; ?></a></p>
</body>
</html>