<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2015 EQdkp-Plus Developer Team
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

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class password extends gen_class {
	private $itoa64;
	private $iteration_count_log2;
	private $random_state;

	public function __construct()
	{
		$this->itoa64 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

		$this->iteration_count_log2 = 10; // Must between 4 and 31

		$this->random_state = microtime();
		if (function_exists('getmypid'))
			$this->random_state .= getmypid();
	}


	public function hash($strPassword, $strSalt = '', $strMethod = ''){
		$strHashedPassword = $this->prehash($strPassword, $strSalt);
		if ($strMethod == '') $strMethod = $this->getBestHashMethod();
		switch ($strMethod){
			case "blowfish": return $this->hash_blowfish($strHashedPassword);
				break;

			case "ext_des": return $this->hash_ext_des($strHashedPassword);
				break;

			case "sha512":	return $this->hash_sha512($strHashedPassword);
				break;
		}

		return false;
	}

	public function prehash($strPassword, $strSalt=''){
		return hash('sha512', $strSalt.$strPassword);
	}

	public function checkPassword($strPassword, $strStoredHash, $blnUseHash=false, $blnReturnHash=false){
		list($strStoredHash, $strSalt) = explode(':', $strStoredHash);

		$strHashedPassword = (!$blnUseHash) ? $this->prehash($strPassword, $strSalt) : $strPassword;
		$strMethod = $this->getHashMethod($strStoredHash);
		$strHash = false;

		switch ($strMethod){
			case "ext_des":
			case "blowfish": $strHash = crypt($strHashedPassword, $strStoredHash);
				break;

			case "sha512":	$strHash = $this->crypt_private($strHashedPassword, $strStoredHash);
				break;

			case "plain_md5": $strHash = md5($strPassword);
				break;

			case "salted_sha512": $strHash = hash('sha512', $strSalt.$strPassword);
		}
		
		//Prevent Timing attacks
		for ($i = 0; $i < strlen($strHash); $i++) {
            $status |= (ord($strHash[$i]) ^ ord($strStoredHash[$i]));
        }

        $blnCompareStatus = ($status === 0);
		
		if ($blnReturnHash){
				return ($strHash &&  $blnCompareStatus) ? $strHash : false;
		}
		return ($strHash &&  $blnCompareStatus);
	}



	private function getBestHashMethod(){
		if (CRYPT_BLOWFISH == 1) return "blowfish";
		if (CRYPT_EXT_DES == 1) return "ext_des";
		return "sha512";
	}

	private function getHashMethod($strHash){
		if (substr($strHash, 0, 4) == '$2a$' && strlen($strHash) == 60) return "blowfish";
		if (substr($strHash, 0, 1) == '_' && strlen($strHash) == 20) return "ext_des";
		if (substr($strHash, 0, 3) == '$S$' && strlen($strHash) == 98) return "sha512";
		if (strlen($strHash) == 32) return "plain_md5";
		if (strlen($strHash) == 128) return "salted_sha512";

		return false;
	}

	public function getIterationCount($strHash){
		$strMethod = $this->getHashMethod($strHash);
		switch ($strMethod){
			case "blowfish":return intval(substr($strHash, 4,2));
				break;

			case "sha512":	return intval($count_log2 = strpos($this->itoa64, $strHash[3]));
				break;
		}

		return false;
	}


	public function checkIfHashNeedsUpdate($strHash){
		$strMethod = $this->getHashMethod($strHash);
		$strBestMethod = $this->getBestHashMethod();
		$intItcount = $this->getIterationCount($strHash);
		if ($strMethod && (($strMethod !== $strBestMethod) || ($intItcount && ($intItcount < $this->iteration_count_log2)))){
			return true;
		}
		return false;
	}

	public function get_random_bytes($count)
	{
		$output = '';
		if (is_readable('/dev/urandom') &&
		    ($fh = @fopen('/dev/urandom', 'rb'))) {
			$output = fread($fh, $count);
			fclose($fh);
		}

		if (strlen($output) < $count) {
			$output = '';
			for ($i = 0; $i < $count; $i += 16) {
				$this->random_state =
				    md5(microtime() . $this->random_state);
				$output .=
				    pack('H*', md5($this->random_state));
			}
			$output = substr($output, 0, $count);
		}

		return $output;
	}

	function encode64($input, $count)
	{
		$output = '';
		$i = 0;
		do {
			$value = ord($input[$i++]);
			$output .= $this->itoa64[$value & 0x3f];
			if ($i < $count)
				$value |= ord($input[$i]) << 8;
			$output .= $this->itoa64[($value >> 6) & 0x3f];
			if ($i++ >= $count)
				break;
			if ($i < $count)
				$value |= ord($input[$i]) << 16;
			$output .= $this->itoa64[($value >> 12) & 0x3f];
			if ($i++ >= $count)
				break;
			$output .= $this->itoa64[($value >> 18) & 0x3f];
		} while ($i < $count);

		return $output;
	}

	private function gensalt_private($input)
	{
		$output = '$S$';
		$output .= $this->itoa64[min($this->iteration_count_log2 +
			((PHP_VERSION >= '5') ? 5 : 3), 30)];
		$output .= $this->encode64($input, 6);

		return $output;
	}

	private function crypt_private($password, $setting)
	{
		$output = '*0';
		if (substr($setting, 0, 2) == $output)
			$output = '*1';

		$id = substr($setting, 0, 3);
		if ($id != '$S$')
			return $output;

		$count_log2 = strpos($this->itoa64, $setting[3]);
		if ($count_log2 < 7 || $count_log2 > 30)
			return $output;

		$count = 1 << $count_log2;

		$salt = substr($setting, 4, 8);
		if (strlen($salt) != 8)
			return $output;

		$hash = hash('sha512', $salt . $password, TRUE);
		do {
			$hash = hash('sha512', $hash . $password, TRUE);
		} while (--$count);
		$len = strlen($hash);
		$output = substr($setting, 0, 12);
		$output .= $this->encode64($hash, $len);

		return $output;
	}

	private function gensalt_extended($input)
	{
		$count_log2 = min($this->iteration_count_log2 + 8, 24);
		# This should be odd to not reveal weak DES keys, and the
		# maximum valid value is (2**24 - 1) which is odd anyway.
		$count = (1 << $count_log2) - 1;

		$output = '_';
		$output .= $this->itoa64[$count & 0x3f];
		$output .= $this->itoa64[($count >> 6) & 0x3f];
		$output .= $this->itoa64[($count >> 12) & 0x3f];
		$output .= $this->itoa64[($count >> 18) & 0x3f];

		$output .= $this->encode64($input, 3);

		return $output;
	}

	private function gensalt_blowfish($input)
	{
		# This one needs to use a different order of characters and a
		# different encoding scheme from the one in encode64() above.
		# We care because the last character in our encoded string will
		# only represent 2 bits.  While two known implementations of
		# bcrypt will happily accept and correct a salt string which
		# has the 4 unused bits set to non-zero, we do not want to take
		# chances and we also do not want to waste an additional byte
		# of entropy.
		$itoa64 = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

		$output = '$2a$';
		$output .= chr(ord('0') + $this->iteration_count_log2 / 10);
		$output .= chr(ord('0') + $this->iteration_count_log2 % 10);
		$output .= '$';

		$i = 0;
		do {
			$c1 = ord($input[$i++]);
			$output .= $itoa64[$c1 >> 2];
			$c1 = ($c1 & 0x03) << 4;
			if ($i >= 16) {
				$output .= $itoa64[$c1];
				break;
			}

			$c2 = ord($input[$i++]);
			$c1 |= $c2 >> 4;
			$output .= $itoa64[$c1];
			$c1 = ($c2 & 0x0f) << 2;

			$c2 = ord($input[$i++]);
			$c1 |= $c2 >> 6;
			$output .= $itoa64[$c1];
			$output .= $itoa64[$c2 & 0x3f];
		} while (1);

		return $output;
	}

	private function hash_blowfish($password){
		$random = '';

		if (CRYPT_BLOWFISH == 1) {
			$random = $this->get_random_bytes(16);
			$hash =
			    crypt($password, $this->gensalt_blowfish($random));
			if (strlen($hash) == 60)
				return $hash;
		}

		return false;
	}

	private function hash_ext_des($password){
		$random = '';

		if (CRYPT_EXT_DES == 1) {
			if (strlen($random) < 3)
				$random = $this->get_random_bytes(3);
			$hash =
			    crypt($password, $this->gensalt_extended($random));
			if (strlen($hash) == 20)
				return $hash;
		}

		return false;
	}

	private function hash_sha512($password){
		$random = '';

		$random = $this->get_random_bytes(6);
		$hash =
		    $this->crypt_private($password,
		    $this->gensalt_private($random));

		if (strlen($hash) == 98)
			return $hash;

		return false;
	}

}
?>