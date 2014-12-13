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
if (!class_exists("zip")) {
	class zip extends gen_class {
		private $zipfile = false;
		private $objZip = false;
		private $files = array();

		public function __construct($zipfile){
			$this->zipfile = $zipfile;
			if (class_exists("ZipArchive")){
				$this->objZip = new ZipArchive;
				if (file_exists($this->zipfile)){
					$blnOpen = $this->objZip->open($this->zipfile);
					if (!$blnOpen){
						$this->objZip = false;
					}
				}
			} else {
				$this->objZip = false;
			}
		}
		
		public function __destruct(){
			if ($this->objZip){
				@$this->objZip->close();
			}
		}
		
		public function close(){
			$this->__destruct();
		}
		
		//Add files that will be written to the archiv when calling create()
		public function add($mixFiles, $strRemovePath = false, $strAddPath = false){

			if (is_array($mixFiles)){
				foreach ($mixFiles as $file){
					$localfilename = $file;
					if ($strRemovePath && strlen($strRemovePath) && strpos($file, $strRemovePath) === 0){
						$localfilename = substr($file, strlen($strRemovePath));
					}
					if ($strAddPath && strlen($strAddPath)){
						$localfilename = $strAddPath.$localfilename;
					}
					$this->files['add'][$localfilename] = $file;
					if (isset($this->files['delete'][$localfilename])) unset($this->files['delete'][$localfilename]);
				}

			} elseif (strlen($mixFiles)) {	
				if (is_dir($mixFiles)){
					$mixFiles = (substr($mixFiles, -1) != '/') ? $mixFiles.'/' : $mixFiles;
					
					$d = dir($mixFiles);

					while (FALSE !== ($entry = $d->read())){
						if ($entry == '.' || $entry == '..'){
							continue;
						}

						$Entry = $mixFiles . $entry;
						if (is_dir( $Entry )){
							$this->add($Entry, $strRemovePath, $strAddPath);
							continue;
						}
						
						$localfilename = $Entry;
						if ($strRemovePath && strlen($strRemovePath) && strpos($Entry, $strRemovePath) === 0){
							$localfilename = substr($Entry, strlen($strRemovePath));
						}
						if ($strAddPath && strlen($strAddPath)){
							$localfilename = $strAddPath.$localfilename;
						}
						$this->files['add'][$localfilename] = $Entry;
						if (isset($this->files['delete'][$localfilename])) unset($this->files['delete'][$localfilename]);
					}
					
				} else {
			
					$localfilename = $mixFiles;
					if ($strRemovePath && strlen($strRemovePath) && strpos($mixFiles, $strRemovePath) === 0){
						$localfilename = substr($mixFiles, strlen($strRemovePath));
					}
					if ($strAddPath && strlen($strAddPath)){
						$localfilename = $strAddPath.$localfilename;
					}
					$this->files['add'][$localfilename] = $mixFiles;
					if (isset($this->files['delete'][$localfilename])) unset($this->files['delete'][$localfilename]);
				}
			} else {
				return false;
			}
			return true;
		}
		
		//Delete Files from Filelist, nur for deleting files from an existing archive!
		public function delete($strPath){
			//Directory
			if (substr($strPath, -1) == '/'){
				foreach($this->files as $key => $value){
					if (strpos($key, $strPath) === 0){
						unset($this->files['add'][$key]);
						$this->files['delete'][$key] = $key;
					}
				}
				
			} else {
				//File
				if (isset($this->files['add'][$strPath])){
					unset($this->files['add'][$strPath]);
					$this->files['delete'][$strPath] = $strPath;
					return true;
				}
				return false;
			}
			
			
			return false;
		}
		
		//Call create, when you have finished adding and deleting files. Archive will be created in tmp-Folder and moved to the right folder
		public function create(){
			//existing archive
			if ($this->objZip && $this->objZip->numFiles > 0){
				$tmpExisting = $this->pfh->FilePath(md5(generateRandomBytes()).'.zip', 'tmp');
				//Move archive to temp folder
				$this->pfh->copy($this->zipfile, $tmpExisting);
				
				
				//open existing zip
				$objZip = new ZipArchive;
				$resZip = $objZip->open($tmpExisting);
				if ($resZip){
					if (is_array($this->files['add'])){
						foreach ($this->files['add'] as $key => $value){
							if(is_file($value)){
								$blnResult = $objZip->addFile($value, $key);
								if (!$blnResult) return false;
							}
						}
					}
					if (is_array($this->files['delete'])){
						foreach ($this->files['delete'] as $key => $value){
							$blnResult = $objZip->deleteName($value, $key);
							//if (!$blnResult) return false;
						}
					}
					
					$this->objZip->close();
					$objZip->close();
					$this->pfh->FileMove($tmpExisting, $this->zipfile);
					return true;
				} else {
					return false;
				}
				
			} else {
				$strTempArchiv = $this->pfh->FilePath(md5(generateRandomBytes()).'.zip', 'tmp');
				//Create new archive
				$blnOpen = $this->objZip->open($strTempArchiv, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE);
				if ($blnOpen){
					foreach ($this->files['add'] as $key => $value){
						if (is_file($value)){
							$blnResult = $this->objZip->addFile($value, $key);
							if (!$blnResult) return false;
						}
					}
					$this->objZip->close();
					$this->pfh->FileMove($strTempArchiv, $this->zipfile);
					return true;
				} else {
					$this->objZip = false;
					return false;
				}
			}
		}
		
		public function extract($strTargetFolder, $arrFiles = false){
			$strTargetFolder = (substr( $strTargetFolder, -1 ) != '/') ? $strTargetFolder.'/' : $strTargetFolder;
		
			if ($this->objZip){
				for ( $i=0; $i < $this->objZip->numFiles; $i++ ) {
					$entry = $this->objZip->getNameIndex($i);
					
					if ($arrFiles && is_array($arrFiles)){
						if (!in_array($entry, $arrFiles)) continue;
					}

						//Directory
						if ( substr( $entry, -1 ) == '/' ) {
							$this->pfh->CheckCreateFolder($strTargetFolder.$entry);
						} else {
							//File
							$contents = '';
							$fp = $this->objZip->getStream($entry);
							if(!$fp) return false;

							while (!feof($fp)) {
								$contents .= fread($fp, 2);
							}

							fclose($fp);
							$this->pfh->CheckCreateFolder(pathinfo($strTargetFolder.$entry, PATHINFO_DIRNAME));
							$this->pfh->CheckCreateFile($strTargetFolder.$entry);
							$this->pfh->putContent($strTargetFolder.$entry, $contents);
						}
						
				}
				return true;
			}
			return false;
		}
	
	}
}
?>