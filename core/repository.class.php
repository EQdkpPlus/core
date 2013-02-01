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
		
		//EQdkp Plus Core Root Cert
		private $coreRootCert = "-----BEGIN CERTIFICATE-----
MIIB4DCCAUmgAwIBAgIBAzANBgkqhkiG9w0BAQUFADAtMRMwEQYDVQQKEwpFUWRr
cCBQbHVzMRYwFAYDVQQLEw1FUWRrcCBQbHVzIENBMB4XDTEzMDExMTA5NDAwMFoX
DTQyMTEwMzE1MzkwMFowLTETMBEGA1UEChMKRVFka3AgUGx1czEWMBQGA1UECxMN
RVFka3AgUGx1cyBDQTCBnzANBgkqhkiG9w0BAQEFAAOBjQAwgYkCgYEA4+ZMyE7S
gUQ5g1UqPMsF8Tw2mc256uaJFvsi48fL0yzu60B8uItjQ9Rn0Tkr4WZf6Magy7Qi
GBPNYerIc2tLHBvBkvlP67Z5IsZi9HxcLZxJujekeo+7sBqYrRxO2Q8jtiNXGUAV
lObItVHmHF0gAUB1H1gEOPh5iLUB49wAs8MCAwEAAaMQMA4wDAYDVR0TBAUwAwEB
/zANBgkqhkiG9w0BAQUFAAOBgQC1Ulxr6GZ5okmF3kFcN0n1y3Os9VAmnDDnRY2m
khKbaXWms2Ezys2QuqMINYYi+g3BbO2AgZDVZ30NMn0WUldSvtykpkPydq+tSBlD
jVY5Fd7pm4adLV4kkyFRH2sWXlLJdLj8HwEvqFM4W/gDPC1B0hdulfkeXB75aINR
uN0FRg==
-----END CERTIFICATE-----";
		
		//EQdkp Plus Packages Root Cert
		private $packagesRootCert = "-----BEGIN CERTIFICATE-----
MIIB4DCCAUmgAwIBAgIBBDANBgkqhkiG9w0BAQUFADAtMRMwEQYDVQQKEwpFUWRr
cCBQbHVzMRYwFAYDVQQLEw1FUWRrcCBQbHVzIENBMB4XDTEzMDExMTA5NDEwMFoX
DTE0MDExMTA5NDEwMFowLTETMBEGA1UEChMKRVFka3AgUGx1czEWMBQGA1UECxMN
RVFka3AgUGx1cyBDQTCBnzANBgkqhkiG9w0BAQEFAAOBjQAwgYkCgYEAr+WDcCmz
IwSZ8MzRqubhZwgnzRapiofJ+MMWk1GoLvJo8MnPQa+CQYK9UZGLnZxJTAtywsHS
FVtwgb/4gd8RbQ3kkrD47QexSMd8auzSDfpXx+knRQMdUmxVIxTDvw9be0XFWrkf
91GeJUcU+l1Txym5t8IrstgHMA8QhBeRa68CAwEAAaMQMA4wDAYDVR0TBAUwAwEB
/zANBgkqhkiG9w0BAQUFAAOBgQBID5kZh0SjBX16Np1x4vFC6JSsDPGF7S5vZtZu
ryZAs5qB971q7IiPjSXYlKC4AErLW6EM5CfimlCSQQBc2UFBIG8gpohIDsomsWP+
aBesoxBasYqnZZz0J9pS+ISQ15/OD0NVlx4jgtVlvQ5bjm9eZ8MmsEWGHH1LQUDF
HTP69g==
-----END CERTIFICATE-----";

		
		public $cachetime		= 86400; //1 day
		public $categories		= array(1,2,3,7,8,11);
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
				11	=> $this->user->lang('pi_category_11'),
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
							if (!in_array((int)$ext->category, $this->categories)) {
								continue;
							}
							
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
								'updated'			=> $this->time->time,
								'rating'			=> (int)round((float)$ext->rating),
								'dep_coreversion'	=> $ext->dep_coreversion,
								'version_ext'		=> ($ext->version_ext) ? $ext->version_ext : $ext->version,
								'dep_php'			=> ($ext->dep_php) ? $ext->dep_php : '',
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
		public function verifyPackage($src, $hash, $signature, $type="core",$blnDeleteIfWrong = true, $blnAgain = false){
			if (file_exists($src) && $signature != "" && $hash != ""){
				$arrIntermCerts = $this->getIntermediateCerts();
				$arrVerified = array();

				foreach($arrIntermCerts as $cert){
					if ($this->verifyIntermediateCert($cert, $type)) $arrVerified[] = $cert;
				}
				$strFileHash = sha1_file($src);
				
				include_once('libraries/phpseclib/X509.php');
				include_once('libraries/phpseclib/RSA.php');
				$x509 = new File_X509();
		
				foreach ($arrVerified as $intermCert){
					//Check, if $hash is valid
					$cert = $x509->loadX509($intermCert);
					$pkey = $x509->getPublicKey()->getPublicKey();
					$rsa = new Crypt_RSA();
					
					$rsa->setSignatureMode(CRYPT_RSA_SIGNATURE_PKCS1);
					$rsa->loadKey($pkey);
					$blnVerified = $rsa->verify($hash, base64_decode($signature));
					
					//If hashes are eqal, it's a valid package
					if ($blnVerified && ($strFileHash === $hash)) {
						return true;
					}
				}
				
				//We are still here, package not valid
				
				//load new intermediate Cert
				$this->loadIntermediateCert();
				//do the thing again
				if (!$blnAgain){
					$blnResult = $this->verifyPackage($src, $hash, $signature, $type, $blnDeleteIfWrong, true);
					return $blnResult;
				}
			}
			return false;
		}
		
		//Get local Intermediate Cert
		private function getIntermediateCerts(){
			if (is_file($this->pfh->FilePath('eqdkp_interm_cert.crt', 'eqdkp/certs', false))){
				$strCerts = file_get_contents($this->pfh->FilePath('eqdkp_interm_cert.crt', 'eqdkp/certs'));
				return $this->parseIntermediateCerts($strCerts);
			} else {
				$blnResult = $this->loadIntermediateCert();
				if ($blnResult){
					$strCerts = file_get_contents($this->pfh->FilePath('eqdkp_interm_cert.crt', 'eqdkp/certs'));
					return $this->parseIntermediateCerts($strCerts);
				}
			}
			return false;
		}
		
		private function parseIntermediateCerts($strCerts){
			$count = preg_match_all('#//(.*?)(-----BEGIN CERTIFICATE-----)(.*?)(-----END CERTIFICATE-----)#s', $strCerts, $arr, PREG_PATTERN_ORDER);
			$arrCerts = array();
			if ($count > 0){
				for($i=0; $i < $count; $i++){
					$cert = "-----BEGIN CERTIFICATE-----\n".trim($arr[3][$i])."\n-----END CERTIFICATE-----";
					$arrCerts[$arr[1][$i]] = $cert;
				}
			}
			return $arrCerts;
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

			if ($response){
				$this->pfh->Delete('crl.txt', 'eqdkp/certs');
				$this->pfh->CheckCreateFolder('certs', 'eqdkp');
				$this->pfh->CheckCreateFile('crl.txt', 'eqdkp/certs');
				$this->pfh->putContent($this->pfh->FilePath('crl.txt', 'eqdkp/certs'), $response);
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
		private function verifyIntermediateCert($intermCert, $type="core"){
			//Root Cert revoked?
			if ($this->checkIfRevoked($this->coreRootCert) || $this->checkIfRevoked($this->packagesRootCert)) {
				$this->config->set('rootcert_revoked', 1);
				return false; 
			}
			
			//Intermediate Cert revoked?
			if ($this->checkIfRevoked($intermCert)) { return false; }
			
			$rootCert = ($type == 'core') ? $this->coreRootCert : $this->packagesRootCert;
			
			include_once($this->root_path.'libraries/phpseclib/X509.php');

			$x509 = new File_X509();
			$x509->loadCA($rootCert); // see signer.crt
			$cert = $x509->loadX509($intermCert); // see google.crt
			if (!$x509->validateSignature(FILE_X509_VALIDATE_SIGNATURE_BY_CA)) return false;

			if (!$x509->validateDate()) return false;			
			
			return true;
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
								
								//Languages
								case 11: 	$arrLanguages = $arrLanguageVersions = array();
											// Build language array
											if($dir = @opendir($this->root_path . 'language/')){
												while ( $file = @readdir($dir) ){
													if ((!is_file($this->root_path . 'language/' . $file)) && (!is_link($this->root_path . 'language/' . $file)) && valid_folder($file)){
														include($this->root_path.'language/'.$file.'/lang_main.php');
														$lang_name_tp = (($lang['ISO_LANG_NAME']) ? $lang['ISO_LANG_NAME'].' ('.$lang['ISO_LANG_SHORT'].')' : ucfirst($file));
														$arrLanguages[$file]		= $lang_name_tp;
														$arrLanguageVersions[$file] = $lang['LANG_VERSION'];
													}
												}
											}
								
			
											if(isset($arrLanguages[$value['plugin']])){
												$blnUpdateAvailable = (compareVersion(trim($value['version']),$arrLanguageVersions[$value['plugin']])==1);
												if ($blnUpdateAvailable) $recent_version = $arrLanguageVersions[$value['plugin']];
											}
								break;
							}

							if ($blnUpdateAvailable){
								$pluginscheck[$value['plugin']] = array(
											'plugin'			=> $value['plugin'],
											'name'				=> $value['name'],
											'version'			=> $value['version_ext'],
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
