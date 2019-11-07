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

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class ilch_bridge extends bridge_generic {

	public static $name = "ilch 1.1";

	public $data = array(
			'user'	=> array(
				'table'	=> 'user',
				'id'	=> 'id',
				'name'	=> 'name',
				'where'	=> 'name',
				'password' => 'pass',
				'email'	=> 'email',
				'salt'	=> '',
				'QUERY'	=> '',
			),
			'groups' => array(
				'table'	=> 'grundrechte', //without prefix
				'id'	=> 'id',
				'name'	=> 'name',
				'QUERY'	=> '',
			),
			'user_group' => array(
				'table'	=> 'user',
				'group'	=> 'recht',
				'user'	=> 'id',
				'QUERY'	=> '',
			),
	);

	//Needed function
	public function check_password($password, $hash, $strSalt = '', $strUsername = "", $arrUserdata=array()){
		$pwCrypt = new Ilch_PwCrypt();
		return $pwCrypt->checkPasswd($password, $hash);
	}

}



/**
 * From the ilch-Project
 * http://ilch.de
 * License: GNU GENERAL PUBLIC LICENSE
 */
class Ilch_PwCrypt
{
	const LETTERS = 1;    //0001
	const NUMBERS = 2;    //0010
	const ALPHA_NUM = 3;    //0011
	const URL_CHARACTERS = 4;   //0100
	const FOR_URL = 7;    //0111
	const SPECIAL_CHARACTERS = 8; //1000
	//Konstanten für die Verschlüsselung
	const MD5 = '1';
	const BLOWFISH_OLD = '2a';
	const BLOWFISH = '2y';
	const BLOWFISH_FALSE = '2x';
	const SHA256 = '5';
	const SHA512 = '6';

	private $hashAlgorithm = self::SHA256;

	/**
	 * PwCrypt::checkHashStrength wird immer false zurückliefern, wenn dieser Wert true ist
	 *
	 * @var boolean
	 */
	private $dontCheckHashStrength = false;

	/**
	 * @param string $lvl Gibt den zu verwendenden Hashalgorithmus an (Klassenkonstante)
	 */
	public function __construct($lvl = '')
	{
		if (!empty($lvl)) {
			$this->hashAlgorithm = $lvl;
		}

		// wenn 2x oder 2y gewählt, aber nicht verfügbar, nutze 2a
		if (version_compare(PHP_VERSION, '5.3.7', '<')
				&& in_array($this->hashAlgorithm, array(self::BLOWFISH, self::BLOWFISH_FALSE))
				) {
					$this->hashAlgorithm = self::BLOWFISH_OLD;
				}

				// Prüfen welche Hash Funktionen Verfügbar sind. Ab 5.3.2 werden alle mitgeliefert
				if (version_compare(PHP_VERSION, '5.3.2', '<')) {
					if ($this->hashAlgorithm === self::SHA512 && (!defined('CRYPT_SHA512') || CRYPT_SHA512 !== 1)) {
						$this->hashAlgoriathm = self::SHA256; // Wenn SHA512 nicht verfügbar, versuche SHA256
					}
					if ($this->hashAlgorithm === self::SHA256 && (!defined('CRYPT_SHA256') || CRYPT_SHA256 !== 1)) {
						$this->hashAlgorithm = self::BLOWFISH_OLD; // Wenn SHA256 nicht verfügbar, versuche BLOWFISH
					}
					if ($this->hashAlgorithm === self::BLOWFISH_OLD && (!defined('CRYPT_BLOWFISH') || CRYPT_BLOWFISH !== 1)) {
						$this->hashAlgorithm = self::MD5; // Wenn BLOWFISH nicht verfügbar, nutze MD5
					}
				}

				/* Wenn 2a oder 2x gewählt, aber 2y verfügbar: nutze trotzdem 2y, da dies sicherer ist; */
				if (version_compare(PHP_VERSION, '5.3.7', '>=')
						&& in_array($this->hashAlgorithm, array(self::BLOWFISH_OLD, self::BLOWFISH_FALSE))
						) {
							$this->hashAlgorithm = self::BLOWFISH;
						}
	}

	/**
	 * Erstellt eine zufällige Zeichenkette
	 *
	 * @param integer $size Länge der Zeichenkette
	 * @param integer $chars Angabe welche Zeichen für die Zeichenkette verwendet werden
	 * @return string
	 */
	public static function getRndString($size = 20, $chars = self::LETTERS)
	{
		if ($chars & self::LETTERS) {
			$pool = 'abcdefghijklmnopqrstuvwxyz';
			$pool .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		}

		if ($chars & self::NUMBERS) {
			$pool .='0123456789';
		}

		//in einer URL nicht reservierte Zeichen
		if ($chars & (self::URL_CHARACTERS | self::SPECIAL_CHARACTERS)) {
			$pool .= '-_.~';
		}

		//restiliche Sonderzeichen
		if ($chars & self::SPECIAL_CHARACTERS) {
			$pool .= '!#$%&()*+,/:;=?@[]';
		}

		$pool = str_shuffle($pool);
		$pool_size = strlen($pool);
		$string = '';
		for ($i = 0; $i < $size; $i++) {
			//TODO: Zufallszahlen aus /dev/random bzw /dev/urandom wenn verfügbar
			$string .= $pool[mt_rand(0, $pool_size - 1)];
		}
		return $string;
	}

	/**
	 * Prüft, ob der übergebene Hash, im crypt Format ist
	 *
	 * @param mixed $hash
	 * @return boolean
	 */
	public static function isCryptHash($hash)
	{
		return (preg_match('/^\$([156]|2[axy])\$/', $hash) === 1);
	}

	/**
	 * Wenn der übergebene Hash einen schwächeren Algorithmus verwendet (kleinere Zahl) wird true zurück geliefert
	 * (schwächere Hashs werden an andere Stelle (user_pw_check()) mit neuem Algorithmus gespeichert)
	 *
	 * @param string $hash
	 * @return boolean
	 */
	public function checkHashStrength($hash)
	{
		$matches = array();
		if ($this->dontCheckHashStrength) {
			return false;
		}
		if (!self::isCryptHash($hash)) {
			return true;
		}
		if (preg_match('/^\$([1256])([axy])?\$/', $hash, $matches) === 1) {
			$hashAlgoNumber = $matches[1];
			$hashAlgoLetter = isset($matches[2]) ? $matches[2] : '';
			if (preg_match('/^([1256])([axy])?$/', $this->hashAlgorithm, $matches) === 1) {
				if ($matches[1] > $hashAlgoNumber) {
					return true;
				} elseif ($matches[1] === '2' && $hashAlgoNumber === '2' && $matches[2] > $hashAlgoLetter) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Gibt den Code der gewählten/genutzen Hashmethode zurück (Crypt Konstante)
	 *
	 * @return string
	 */
	public function getHashAlgorithm()
	{
		return $this->hashAlgorithm;
	}

	/**
	 * Erstellt ein Hash für das übergebene Passwort
	 *
	 * @param string $passwd Klartextpasswort
	 * @param string $salt Salt für den Hashalgorithus
	 * @param integer $rounds Anzahl der Runden für den verwendeten Hashalgorithmus
	 * @return string Hash des Passwortes (Ausgabe von crypt())
	 */
	public function cryptPasswd($passwd, $salt = '', $rounds = 0)
	{
		$salt_string = '';
		switch ($this->hashAlgorithm) {
			case self::SHA512:
			case self::SHA256:
				$salt = (empty($salt) ? self::getRndString(16, self::LETTERS | self::NUMBERS) : $salt);
				if ($rounds < 1000 || $rounds > 999999999) {
					$rounds = mt_rand(2000, 10000);
				}
				$salt_string = '$' . $this->hashAlgorithm . '$rounds=' . $rounds . '$' . $salt . '$';
				break;
			case self::BLOWFISH:
			case self::BLOWFISH_OLD:
				$salt = (empty($salt) ? self::getRndString(22, self::LETTERS | self::NUMBERS) : $salt);
				if ($rounds < 4 || $rounds > 31) {
					$rounds = mt_rand(6, 10);
				}
				$salt_string = '$' . $this->hashAlgorithm . '$' . str_pad($rounds, 2, '0', STR_PAD_LEFT) . '$' . $salt . '$';
				break;
			case self::MD5:
				$salt = (empty($salt) ? self::getRndString(12, self::LETTERS | self::NUMBERS) : $salt);
				$salt_string = '$' . $this->hashAlgorithm . '$' . $salt . '$';
				break;
			default:
				return false;
		}
		$crypted_pw = crypt($passwd, $salt_string);
		if (strlen($crypted_pw) < 13) {
			return false;
		}
		return $crypted_pw;
	}

	/**
	 * Prüft, ob das Klartextpasswort dem Hash "entspricht"
	 *
	 * @param mixed $passwd Klartextpasswort
	 * @param mixed $crypted_passwd Hash des Passwortes (aus der Datenbank)
	 * @param boolean $backup wenn Check fehlschlägt und das alte passwort mit BLOWFISH_OLD verschlüsselt wurde,
	 *      werden beide Varianten noch einmal explizit geprüft, wenn verfügbar. Nur nach Transfer der Datenbank verwenden,
	 *      da dies ein Sicherheitsrisiko darstellen kann
	 * @return boolean
	 */
	public function checkPasswd($passwd, $crypted_passwd, $backup = false)
	{
		if (empty($crypted_passwd)) {
			return false;
		}
		if (self::isCryptHash($crypted_passwd)) {
			$new_chrypt_pw = crypt($passwd, $crypted_passwd);
			if (strlen($new_chrypt_pw) < 13) {
				return false;
			}
		} else {
			$new_chrypt_pw = md5($passwd);
		}
		if ($new_chrypt_pw == $crypted_passwd) {
			return true;
		} else {
			if ($backup == true
					&& version_compare(PHP_VERSION, '5.3.7', '>=')
					&& substr($crypted_passwd, 0, 4) == '$2a$'
					) {
						$password_x = '$2x$' . substr($crypted_passwd, 4);
						$password_y = '$2y$' . substr($crypted_passwd, 4);
						$password_neu_x = crypt($passwd, $password_x);
						$password_neu_y = crypt($passwd, $password_y);
						if ($password_neu_x === $password_x || $password_neu_y === $password_y) {
							return true;
						}
					}
		}
		return false;
	}
}
