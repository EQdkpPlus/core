<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2010
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 *
 * $Id$
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

if (!class_exists("repository")) {
	class repository extends gen_class {
		public static $shortcuts = array('user', 'config', 'time', 'pdh', 'pfh', 'jquery', 'tpl', 'game', 'pm',
			'puf'	=> 'urlfetcher', 'objStyles'=> 'styles'
		);
		
		//Dummy URL, should be a secure connection
		private $RepoEndpoint	= "";
		
		//EQdkp Plus Root Cert
		private $rootCert = "-----BEGIN CERTIFICATE-----
MIIB7jCCAVegAwIBAgIBATANBgkqhkiG9w0BAQUFADAtMRMwEQYDVQQKEwpFUWRr
cCBQbHVzMRYwFAYDVQQLEw1FUWRrcCBQbHVzIENBMB4XDTEyMTEwMzE1MzkwMFoX
DTQyMTEwMzE1MzkwMFowLTETMBEGA1UEChMKRVFka3AgUGx1czEWMBQGA1UECxMN
RVFka3AgUGx1cyBDQTCBnzANBgkqhkiG9w0BAQEFAAOBjQAwgYkCgYEAxRPRiRKf
YMm9tdR3wRDdxZ0VdcsbPA6JHtoOdYb0UMzehfJkwdGWjYDIi4188QPOah8IaCjz
/25yh1GLVvs7vUJhJfm6zSEMKM9KFmptuAB2nVWj7vJ8N71WupfqGfhlV/ptm7NY
SgF1u5Iec3BfF36is4GhV+WpaUQXAjk6H2ECAwEAAaMeMBwwDAYDVR0TBAUwAwEB
/zAMBgNVHQ8EBQMDB/+AMA0GCSqGSIb3DQEBBQUAA4GBAHijJJt0JmvLoBTrQQpO
xy4/WbV2cHoGPCkzrSqFsTUVkm4FtvryXwhu1f7LmtEB0kubdjfm0sVsiYqIcSve
GZ4AWKOmtvP/FnLoGgCrvZ/awt/sCM1giZ9z3fDRmW2fu2OAR10UV33c7fQJsUap
E8Cl00yOHHLzxkEzjPqv8GpC
-----END CERTIFICATE-----";
		
		public $cachetime		= 86400; //1 day
		public $categories		= array(1,2,3,7,8);
		public $update_count	= 0;
		private $extensions 	= array();
		public $updates = array();
		private $plusversion, $user_auth, $host_mode, $new_version = false;		

		//Constructor
		public function __construct(){
			$this->RepoEndpoint		= EQDKP_REPO_URL."repository.php?function=";
			$this->plusversion		= VERSION_INT;
			$this->user_auth		= $this->user->check_auth('a_', false);
			$this->host_mode		= $this->user->check_hostmode(false);
			$this->updates			= $this->BuildUpdateArray();
			//reduce caching time if it's a tester
			if ($this->getChannel() != "stable"){
				$this->cachetime = 3600 * 3; //3 hours
			}
		}
		
		public function getChannel(){
			if (defined('REPO_CHANNEL')){
				switch (REPO_CHANNEL){
					case 'alpha': return "alpha";
					case 'beta' : return "beta";
				}
			}
			return "stable";
		}
		
		private function getChannelURL(){
			if (defined('REPO_CHANNEL')){
				switch (REPO_CHANNEL){
					case 'alpha': return "&channel=alpha";
					case 'beta' : return "&channel=beta";
				}
			}
			return "";
		}
		
		
		//Check for all required php functions
		public function checkRequirements(){
			if (function_exists('openssl_verify') && (int)$this->config->get('rootcert_revoked') != 1) return true;
			
			return false;
		}

		//Init Lifeupdate
		public function InitLifeupdate($new_version){
			$this->new_version = $new_version;
		}

		//Get Categories for Displaying
		public function DisplayCategories(){
			return  array(
				1	=> $this->user->lang('pi_category_1'),
				2	=> $this->user->lang('pi_category_2'),
				3	=> $this->user->lang('pi_category_3'),
				7	=> $this->user->lang('pi_category_7'),
			);
		}

		/**
		* Check if we need to download a new Packagelist
		*
		*/
		public function CheckforPackages($force = false){
			$lastupdate = (int) $this->pdh->get('repository', 'lastupdate');
			// The Data is Outdated, load the new ones to the DB
			if($force || (($this->time->time - $lastupdate) > $this->cachetime)){
				$this->fetchExtensionList();
				$this->loadRevokeList();
			}
		}

		/**
		* Build the List with all package information
		*
		* @return array with version info
		*/
		public function getExtensionList(){
			$this->CheckforPackages();		// Check if there's need to update the Package DB
			$plist = $this->pdh->get('repository', 'repository');
			return $plist;
		}

		// truncate the repo database folder
		public function ResetExtensionList(){
			$this->pdh->put('repository', 'reset');
			$this->pdh->process_hook_queue();
		}

		// fetch the extension list and save to database
		private function fetchExtensionList(){
			$response = $this->puf->fetch($this->RepoEndpoint.'extension_list'.$this->getChannelURL(), "", 1);
			if ($response){
				$this->ResetExtensionList();		
				$arrJson = json_decode($response);
				if ($arrJson && (int)$arrJson->status == 1){
					$extensions = $arrJson->extensions;
					if(is_object($extensions)){
						foreach ($extensions as $ext){
							if (!isset($this->categories[(int)$ext->category])) continue;
							$this->pdh->put('repository', 'insert', array(array(
								'plugin'			=> $ext->plugin,
								'name'				=> $ext->name,
								'date'				=> $ext->releasedate,
								'author'			=> $ext->author,
								'shortdesc'			=> $ext->shortdesc,
								'version'			=> $ext->version,
								'category'			=> $ext->category,
								'level'				=> $ext->level,
								'changelog'			=> $ext->changelog,
								'build'				=> $ext->build,
								'updated'			=> $this->time->time,
								'rating'			=> (int)round((float)$ext->rating),
								'dep_coreversion'	=> $ext->dep_coreversion,
							)));
						}
					}
					$this->pdh->process_hook_queue();
					return true;
				}
			} else {
				//If EQdkp Plus Server could not be reached, try again next day
				$this->pdh->put('repository', 'setUpdateTime', array($this->time->time));	
				$plist = $this->pdh->get('repository', 'repository');
				if ($plist == null){
					$this->pdh->put('repository', 'insert', array(array(
								'plugin'			=> 'dummy',
								'name'				=> 'dummy',
					)));
				}
				$this->pdh->process_hook_queue();				
			}
			return false;
		}

		// generate download link for extension
		public function getExtensionDownloadLink($intCategory, $strExtensionName){
			$response = $this->puf->fetch($this->RepoEndpoint.'download_link&category='.intval($intCategory).'&name='.$strExtensionName, "", 1);
			$arrJson = json_decode($response);
			if ($arrJson && (int)$arrJson->status == 1 && strlen((string)$arrJson->link)){
				return array('link' => (string)$arrJson->link, 'hash' => (string)$arrJson->hash, 'signature' => (string)$arrJson->signature);
			} else {
				return false;
			}
		}

		// generate download link for core update
		public function getCoreUpdateDownloadLink(){
			$response = $this->puf->fetch($this->RepoEndpoint.'core_update&old='.$this->plusversion.'&new='.$this->new_version.$this->getChannelURL(), "", 1);
			$arrJson = json_decode($response);
			if ($arrJson && (int)$arrJson->status == 1 && strlen((string)$arrJson->link)){
				return array('link' => (string)$arrJson->link, 'hash' => (string)$arrJson->hash, 'signature' => (string)$arrJson->signature);
			} else {
				return false;
			}
		}

		// download package
		public function downloadPackage($src, $dest, $filename){
			$file = $this->puf->fetch($src, "", 1, 200);
			if ($file) {
				$this->pfh->Delete($dest.$filename);
				$this->pfh->CheckCreateFolder($dest);
				$this->pfh->CheckCreateFile($dest.$filename);
				$this->pfh->putContent($dest.$filename, $file);
				return true;
			}
			return false;
		}
		
		// verify package
		public function verifyPackage($src, $hash, $signature, $blnDeleteIfWrong = true, $blnAgain = false){
			if (file_exists($src) && $signature != "" && $hash != "" && $this->rootCert != ""){
				$intermCert = $this->getIntermediateCert();
				
				//Verify Intermediate Cert
				if ($this->verifyIntermediateCert($intermCert)){
					//Check, if $hash is valid
					$arrPublicKey = openssl_pkey_get_details(openssl_pkey_get_public($intermCert));
					$blnVerified = openssl_verify($hash, base64_decode($signature), $arrPublicKey['key']);

					$strFileHash = sha1_file($src);
					
					if (!$blnVerified) {
						//cert error or file error?
						if ($strFileHash === $hash){
							//load new intermediate Cert
							$this->loadIntermediateCert();
							//do the thing again
							if (!$blnAgain){
								$blnResult = $this->verifyPackage($src, $hash, $signature, $blnDeleteIfWrong, true);
								return $blnResult;
							}
						}
					}	
					
					//If hashes are eqal, it's a valid package
					if ($blnVerified && ($strFileHash === $hash)) {
						return true;
					} else {
						//Delete the file because it's a bad file
						if ($blnDeleteIfWrong) $this->pfh->Delete($src);
					}

				} else {
					//Load cert from server, because it has been revoked or is not valid anymore
					$this->loadIntermediateCert();
					//do the thing again
					if (!$blnAgain){
						$blnResult = $this->verifyPackage($src, $hash, $signature, $blnDeleteIfWrong, true);
						return $blnResult;
					}
					
					if ($blnDeleteIfWrong) $this->pfh->Delete($src);
					return false;
				}	
			}
			return false;
		}
		
		//Get local Intermediate Cert
		private function getIntermediateCert(){
			if (is_file($this->pfh->FilePath('eqdkp_interm_cert.crt', 'eqdkp/certs', false))){
				return file_get_contents($this->pfh->FilePath('eqdkp_interm_cert.crt', 'eqdkp/certs'));
			} else {
				$blnResult = $this->loadIntermediateCert();
				if ($blnResult){
					return file_get_contents($this->pfh->FilePath('eqdkp_interm_cert.crt', 'eqdkp/certs'));
				}
			}
			return false;
		}
		
		//Download the Intermediate Cert from our server
		private function loadIntermediateCert(){

			$response = $this->puf->fetch($this->RepoEndpoint.'interm_cert', "", 1);
			$arrJson = json_decode($response);

			if ($arrJson && (int)$arrJson->status == 1 && strlen((string)$arrJson->cert)){
				$this->pfh->Delete('eqdkp_interm_cert.crt', 'eqdkp/certs');
				$this->pfh->CheckCreateFolder('certs', 'eqdkp');
				$this->pfh->CheckCreateFile('eqdkp_interm_cert.crt', 'eqdkp/certs');
				$this->pfh->putContent($this->pfh->FilePath('eqdkp_interm_cert.crt', 'eqdkp/certs'), (string)$arrJson->cert);
				return true;
			} else {
				return false;
			}
		}
		
		//Download Revoke List from our server
		private function loadRevokeList(){

			$response = $this->puf->fetch(EQDKP_CRL_URL, "", 1);
			$arrJson = json_decode($response);

			if ($arrJson && (int)$arrJson->status == 1 && strlen((string)$arrJson->list)){
				$this->pfh->Delete('crl.txt', 'eqdkp/certs');
				$this->pfh->CheckCreateFolder('certs', 'eqdkp');
				$this->pfh->CheckCreateFile('crl.txt', 'eqdkp/certs');
				$this->pfh->putContent($this->pfh->FilePath('crl.txt', 'eqdkp/certs'), (string)$arrJson->list);
				return true;
			} else {
				return false;
			}
		}
		
		//Parses Revoke List and returns an array with all revoked certs
		private function getRevokeList(){
			$strRevokeList = false;
			if (is_file($this->pfh->FilePath('crl.txt', 'eqdkp/certs', false))){
				$strRevokeList = file_get_contents($this->pfh->FilePath('crl.txt', 'eqdkp/certs'));
			} else {
				$blnResult = $this->loadRevokeList();
				if ($blnResult){
					$strRevokeList = file_get_contents($this->pfh->FilePath('crl.txt', 'eqdkp/certs'));
				}
			}
			
			if ($strRevokeList && strlen($strRevokeList)){
				$arrRevokeList = array();
				$convert = explode("\n", $strRevokeList);
				for ($i=0;$i<count($convert);$i++) {	
					$arrRevokeList[] = strtolower(str_replace(':', '', $convert[$i]));
				}
				return $arrRevokeList;
			}
			
			return false;
		}
		
		//Checks if an cert is revoked. Returns true when revoked, otherwise false
		private function checkIfRevoked($cert){
			$arrRevokeList = $this->getRevokeList();
			if (is_array($arrRevokeList)){
				$certData = $this->pemToDer($cert);
				$strFingerprint = sha1($certData);
				foreach ($arrRevokeList as $print){
					if ($print == $strFingerprint) return true;
				}
			}
			return false;
		}
		
		//Check if Intermediate Cert is valid
		private function verifyIntermediateCert($intermCert){
			//Root Cert revoked?
			if ($this->checkIfRevoked($this->rootCert)) {
				$this->config->set('rootcert_revoked', 1);
				return false; 
			}
			
			//Intermediate Cert revoked?
			if ($this->checkIfRevoked($intermCert)) { return false; }

			// Convert the cert to der for feeding to extractSignature.
			$certDer = $this->pemToDer($intermCert);
			if (!is_string($certDer)) { return false; }
			
			// Grab the encrypted signature from the der encoded cert.
			$encryptedSig = $this->extractSignature($certDer);
			if (!is_string($encryptedSig)) { return false; }
    
			// Extract the public key from the ca cert, which is what has
			// been used to encrypt the signature in the cert.
			$pubKey = openssl_pkey_get_public($this->rootCert);
			if ($pubKey === false) {
				return false;
			}
			// Attempt to decrypt the encrypted signature using the CA's public
			// key, returning the decrypted signature in $decryptedSig.  If
			// it can't be decrypted, this ca was not used to sign it for sure...
			$rc = openssl_public_decrypt($encryptedSig,$decryptedSig,$pubKey);
			if ($rc === false) { return false; }
			// We now have the decrypted signature, which is der encoded
			// asn1 data containing the signature algorithm and signature hash.
			// Now we need what was originally hashed by the issuer, which is
			// the original DER encoded certificate without the issuer and
			// signature information.
			$origCert = $this->stripSignerAsn($certDer);
			if ($origCert === false) {
				return false;
			}
			// Get the oid of the signature hash algorithm, which is required
			// to generate our own hash of the original cert.  This hash is
			// what will be compared to the issuers hash.
			$oid = $this->getSignatureAlgorithmOid($decryptedSig);
			if ($oid === false) {
				return false;
			}
			switch($oid) {
				//case '1.2.840.113549.2.2':     $algo = 'md2';    break;
				//case '1.2.840.113549.2.4':     $algo = 'md4';    break;
				case '1.2.840.113549.2.5':     $certHash = md5($origCert);    break;
				//case '1.3.14.3.2.18':          $algo = 'sha';    break;
				case '1.3.14.3.2.26':          $certHash = sha1($origCert);    break;
				//case '2.16.840.1.101.3.4.2.1': $algo = 'sha256'; break;
				//case '2.16.840.1.101.3.4.2.2': $algo = 'sha384'; break;
				//case '2.16.840.1.101.3.4.2.3': $algo = 'sha512'; break;
				default:
					return false;
				break;
			}
			
			// Get the issuer generated hash from the decrypted signature.
			$decryptedHash = $this->getSignatureHash($decryptedSig);
			// Ok, hash the original unsigned cert with the same algorithm
			// and if it matches $decryptedHash we have a winner.
			//$certHash = hash($algo,$origCert);
			$blnResult = ($decryptedHash === $certHash);
			
			//Check timestamp
			if ($blnResult){
				$arrCert = openssl_x509_parse($intermCert);
				if ($arrCert){
					$intValidFrom = (int)$arrCert['validFrom_time_t'];
					$intValidTo = (int)$arrCert['validTo_time_t'];
					if (time() > $intValidFrom && time() < $intValidTo) return true;
				}
			}
			
			return false;
		}
		
		
		// unarchive package
		public function unpackPackage($file, $dest){
			$archive = registry::register('zip', array($file));
			$my_extract = $archive->extract($dest);
			if(!$my_extract) {
				$this->pfh->Delete($file);
				return false;
			} else{
				return true;
			}
		}

		// amount ofupdates for extensions available
		public function UpdateCount(){
			return (isset($this->updates['pluskernel'])) ? ($this->update_count-1) : $this->update_count;
		}

		// check if there are updates available
		public function UpdatesAvailable($pcore=false){
			if($pcore){
				return (isset($this->updates['pluskernel'])) ? true : false;
			}else{
				return ($this->UpdateCount() > 0) ? true : false;
			}
		}
		
		/**
		 * Extract signature from der encoded cert.
		 * Expects x509 der encoded certificate consisting of a section container
		 * containing 2 sections and a bitstream.  The bitstream contains the
		 * original encrypted signature, encrypted by the public key of the issuing
		 * signer.
		 * @param string $der
		 * @return string on success
		 * @return bool false on failures
		 */
		function extractSignature($der=false) {
			if (strlen($der) < 5) { return false; }
			// skip container sequence
			$der = substr($der,4);
			// now burn through two sequences and the return the final bitstream
			while(strlen($der) > 1) {
				$class = ord($der[0]);
				$classHex = dechex($class);
				switch($class) {
					// BITSTREAM
					case 0x03:
						$len = ord($der[1]);
						$bytes = 0;
						if ($len & 0x80) {
							$bytes = $len & 0x0f;
							$len = 0;
							for ($i = 0; $i < $bytes; $i++) {
								$len = ($len << 8) | ord($der[$i + 2]);
								}
							}
						return substr($der,3 + $bytes, $len);
					break;
					// SEQUENCE
					case 0x30:
						$len = ord($der[1]);
						$bytes = 0;
						if ($len & 0x80) {
							$bytes = $len & 0x0f;
							$len = 0;
							for($i = 0; $i < $bytes; $i++) {
								$len = ($len << 8) | ord($der[$i + 2]);
								}
							}
						$contents = substr($der, 2 + $bytes, $len);
						$der = substr($der,2 + $bytes + $len);
					break;
					default:
						return false;
					break;
					}
			}
			return false;
		}

		/**
		 * Get signature algorithm oid from der encoded signature data.
		 * Expects decrypted signature data from a certificate in der format.
		 * This ASN1 data should contain the following structure:
		 * SEQUENCE
		 *    SEQUENCE
		 *       OID    (signature algorithm)
		 *       NULL
		 * OCTET STRING (signature hash)
		 * @return bool false on failures
		 * @return string oid
		 */
		function getSignatureAlgorithmOid($der=null) {
			// Validate this is the der we need...
			if (!is_string($der) or strlen($der) < 5) { return false; }
			$bit_seq1 = 0;
			$bit_seq2 = 2;
			$bit_oid  = 4;
			if (ord($der[$bit_seq1]) !== 0x30) {
				die('Invalid DER passed to getSignatureAlgorithmOid()');
				}
			if (ord($der[$bit_seq2]) !== 0x30) {
				die('Invalid DER passed to getSignatureAlgorithmOid()');
				}
			if (ord($der[$bit_oid]) !== 0x06) {
				die('Invalid DER passed to getSignatureAlgorithmOid');
				}
			// strip out what we don't need and get the oid
			$der = substr($der,$bit_oid);
			// Get the oid
			$len = ord($der[1]);
			$bytes = 0;
			if ($len & 0x80) {
				$bytes = $len & 0x0f;
				$len = 0;
				for ($i = 0; $i < $bytes; $i++) {
					$len = ($len << 8) | ord($der[$i + 2]);
					}
				}
			$oid_data = substr($der, 2 + $bytes, $len);
			// Unpack the OID
			$oid  = floor(ord($oid_data[0]) / 40);
			$oid .= '.' . ord($oid_data[0]) % 40;
			$value = 0;
			$i = 1;
			while ($i < strlen($oid_data)) {
				$value = $value << 7;
				$value = $value | (ord($oid_data[$i]) & 0x7f);
				if (!(ord($oid_data[$i]) & 0x80)) {
					$oid .= '.' . $value;
					$value = 0;
					}
				$i++;
				}
			return $oid;
		}

		/**
		 * Get signature hash from der encoded signature data.
		 * Expects decrypted signature data from a certificate in der format.
		 * This ASN1 data should contain the following structure:
		 * SEQUENCE
		 *    SEQUENCE
		 *       OID    (signature algorithm)
		 *       NULL
		 * OCTET STRING (signature hash)
		 * @return bool false on failures
		 * @return string hash
		 */
		function getSignatureHash($der=null) {
			// Validate this is the der we need...
			if (!is_string($der) or strlen($der) < 5) { return false; }
			if (ord($der[0]) !== 0x30) {
				die('Invalid DER passed to getSignatureHash()');
				}
			// strip out the container sequence
			$der = substr($der,2);
			if (ord($der[0]) !== 0x30) {
				die('Invalid DER passed to getSignatureHash()');
				}
			// Get the length of the first sequence so we can strip it out.
			$len = ord($der[1]);
			$bytes = 0;
			if ($len & 0x80) {
				$bytes = $len & 0x0f;
				$len = 0;
				for ($i = 0; $i < $bytes; $i++) {
					$len = ($len << 8) | ord($der[$i + 2]);
					}
				}
			$der = substr($der, 2 + $bytes + $len);
			// Now we should have an octet string
			if (ord($der[0]) !== 0x04) {
				die('Invalid DER passed to getSignatureHash()');
				}
			$len = ord($der[1]);
			$bytes = 0;
			if ($len & 0x80) {
				$bytes = $len & 0x0f;
				$len = 0;
				for ($i = 0; $i < $bytes; $i++) {
					$len = ($len << 8) | ord($der[$i + 2]);
					}
				}
			return bin2hex(substr($der, 2 + $bytes, $len));
		}

		/**
		* Convert pem encoded certificate to DER encoding
		* @return string $derEncoded on success
		* @return bool false on failures
		*/
		function pemToDer($pem=null) {
			if (!is_string($pem)) { return false; }
			$cert_split = preg_split('/(-----((BEGIN)|(END)) CERTIFICATE-----)/',$pem);
			if (!isset($cert_split[1])) { return false; }
			return base64_decode($cert_split[1]);
		}

		/**
		 * Obtain der cert with issuer and signature sections stripped.
		 * @param string $der - der encoded certificate
		 * @return string $der on success
		 * @return bool false on failures.
		 */
		private function stripSignerAsn($der=null) {
			if (!is_string($der) or strlen($der) < 8) { return false; }
			$bit = 4;
			$len   = ord($der[($bit + 1)]);
			$bytes = 0;
			if ($len & 0x80) {
				$bytes = $len & 0x0f;
				$len   = 0;
				for($i = 0; $i < $bytes; $i++) {
					$len = ($len << 8) | ord($der[$bit + $i + 2]);
					}
				}
			return substr($der,4,$len + 4);
		}

		/************************************
		 * HELPER FUNCTIONS
		 ***********************************/

		// Build the update badge
		public function UpdateBadge($id, $pcore=false){
			if(!$this->UpdatesAvailable($pcore)){ return ''; }
			$this->jquery->qtip('#'.$id, (($pcore) ? '<div class=\"updchk_tt_info\">'.((defined('USE_REPO')) ? $this->user->lang('lib_pupd_core_intro') : $this->user->lang('lib_pupd_core_intro2')).'</div>' : $this->TooltipContent()));
			$this->tpl->add_js('$("#'.$id.'").click(function(){ window.location = "'.($this->root_path.'admin/'.(($pcore) ? 'manage_live_update.php' : 'manage_extensions.php')).'"; });', 'docready');
			return '<span class="update_available" id="'.$id.'">'.(($pcore) ? '!' : $this->UpdateCount()).'</span>';
		}

		// Build the Array with the available Updates
		public function BuildUpdateArray($only_installed = true){
			$pluginscheck = array();
			if (!$only_installed){
				$arrUninstalledStyles = $this->objStyles->getUninstalledStyles();
			}

			$arrExtensions = $this->getExtensionList();
			if(is_array($arrExtensions)){
				foreach ($arrExtensions as $categoryid => $categorycontent){
					if (is_array($categorycontent)){
						foreach ($categorycontent as $id => $value){

							$blnUpdateAvailable = false;

							switch((int)$value['category']){
								//Plugins
								case 1 : $status = ($only_installed) ? PLUGIN_INSTALLED : PLUGIN_REGISTERED;
										$blnUpdateAvailable = (($this->pm->check($value['plugin'], $status) && !$this->pm->check($value['plugin'], PLUGIN_DISABLED)) && (compareVersion(trim($value['version']),$this->pm->get_data($value['plugin'], 'version'))==1)) || ($value['plugin'] == 'pluskernel' && compareVersion(trim($value['version']), $this->plusversion)==1);
										 if ($blnUpdateAvailable) $recent_version = ($value['plugin'] != 'pluskernel') ? $this->pm->get_data($value['plugin'], 'version') : $this->plusversion;
								break;
								//Templates
								case 2 : $arrStylesList = $this->pdh->aget('styles', 'templatepath', 0, array($this->pdh->get('styles', 'id_list')), false);
										if (in_array($value['plugin'], $arrStylesList)){
											$styleid = array_search($value['plugin'], $arrStylesList);
											$blnUpdateAvailable = (compareVersion(trim($value['version']),$this->pdh->get('styles', 'version', array($styleid)))==1);
											if ($blnUpdateAvailable) $recent_version = $this->pdh->get('styles', 'version', array($styleid));
										}
										if (!$only_installed){
											if (isset($arrUninstalledStyles[$value['plugin']])){
												$blnUpdateAvailable = (compareVersion(trim($value['version']),$arrUninstalledStyles[$value['plugin']]->version) == 1);
												if ($blnUpdateAvailable) $recent_version = $arrUninstalledStyles[$value['plugin']]->version;
											}
										}


								break;
								//Portal modules
								case 3 : $arrPortalList = $this->pdh->aget('portal', 'path', 0, array($this->pdh->get('portal', 'id_list')), false);
										if (in_array($value['plugin'], $arrPortalList)){
											//Module belongs to an plugin
											$moduleid = array_search($value['plugin'], $arrPortalList);
											if (strlen($this->pdh->get('portal', 'plugin', array($moduleid)))) break;
											if ($only_installed){
												$status = (int)$this->pdh->get('portal', 'enabled', array($moduleid));
												if ($status != 1) break;
											}

											if ($blnUpdateAvailable) $recent_version = $this->pdh->get('portal', 'version', array($moduleid));
										}

								break;

								//Games
								case 7 : if ($only_installed){
											if ($value['plugin'] == $this->config->get('default_game')){
												$blnUpdateAvailable = (compareVersion(trim($value['version']),$this->game->gameVersion())==1);
												if ($blnUpdateAvailable) $recent_version = $this->game->gameVersion();
											}
										} else {
											$arrGames = $this->game->get_versions();
											if (isset($arrGames[$value['plugin']])){
												$blnUpdateAvailable = (compareVersion(trim($value['version']),$arrGames[$value['plugin']])==1);
												if ($blnUpdateAvailable) $recent_version = $arrGames[$value['plugin']];
											}
										}
								break;
							}

							if ($blnUpdateAvailable){
								$pluginscheck[$value['plugin']] = array(
											'plugin'			=> $value['plugin'],
											'name'				=> $value['name'],
											'version'			=> $value['version'],
											'recent_version'	=> $recent_version,
											'changelog'			=> $value['changelog'],
											'level'				=> $value['level'],
											'release'			=> $this->time->user_date($value['date']),
								);
								$this->update_count++;
							}
						}
					}
				}
			}

			//local Style Updates
			$arrStyleUpdates = $this->objStyles->getLocalStyleUpdates();
			if (count($arrStyleUpdates) > 0) {
				$pluginscheck = array_merge($pluginscheck, $this->objStyles->getLocalStyleUpdates());
				$this->update_count += count($arrStyleUpdates);
			}

			return $pluginscheck;
		}

		// Build the tooltip for the extension updates badge
		private function TooltipContent(){
			$out_htm  = '<div class=\"updchk_tt_info\">'.$this->user->lang('lib_pupd_intro').'</div><br/>';
			if($this->user_auth && is_array($this->updates) && ($this->update_count > 0)){
				foreach($this->updates as $data){
					if ($data['plugin'] == 'pluskernel') continue;
					$sentence	= sprintf($this->user->lang('lib_pupd_updtxt_tt'), (($this->user->lang($data['plugin'])) ? $this->user->lang($data['plugin']) : $data['name']), $data['version'], $data['plugin'], $data['release']);
					$out_htm  .= '<div class=\"updchk_tt_uv\">'.$sentence.'</div>';
				}
			}
			return $out_htm;
		}

		//Helper for Reading package.xml
		public function getFilelistFromPackageFile($packageFile, $type = false){;
			$content = file_get_contents($packageFile);
			$arrChanged = array();
			if ($content){
				$xml = simplexml_load_string($content);
				if ($xml){
					foreach ($xml->file as $file){
						if (!$type){
							$arrChanged[] = array(
								'name'	=> (string)$file->attributes()->name,
								'type'	=> (string)$file->attributes()->type,
								'md5'	=> (string)$file->attributes()->md5,
								'md5_old'	=> (string)$file->attributes()->md5_old,
							);
						} elseif ((string)$file->attributes()->type == $type) {
							$arrChanged[] = array(
								'name'	=> (string)$file->attributes()->name,
								'type'	=> (string)$file->attributes()->type,
								'md5'	=> (string)$file->attributes()->md5,
								'md5_old'=> (string)$file->attributes()->md5_old,
							);
						}
					} 
					return $arrChanged;
				}
			}
			return false;
		}

		// Copy one dir over another
		public function full_copy($source, $target){
			if (is_dir($source)){
				$this->pfh->CheckCreateFolder($target);
				$d = dir($source);

				while (FALSE !== ($entry = $d->read())){
					if ($entry == '.' || $entry == '..'){
						continue;
					}

					$Entry = $source . '/' . $entry;
					if (is_dir( $Entry )){
						$this->full_copy($Entry, $target . '/' . $entry);
						continue;
					}
					$this->pfh->copy($Entry, $target . '/' . $entry);
					if (!is_file($target . '/' . $entry)) return false;
				}
				$d->close();
			} else {
				$this->pfh->copy($source, $target);
				if (!is_file($target)) return false;
			}

			return true;
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_repository', repository::$shortcuts);
?>
