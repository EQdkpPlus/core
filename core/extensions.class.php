<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2010
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2006-2010 EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev$
 * 
 * $Id$
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}
 
if (!class_exists("Extensions")) {
  class Extensions {
  
    var $package_feed = 'http://rss.eqdkp-plus.com/';
    var $cachetime    = 7200;   // Search for new
    var $debug        = '';
    var $maxfilezize	= 5000000;
    var $whitelist		= array(
    											'application/zip',
    											'application/x-zip'
    										);
    function __construct(){
    	global $user;
			$this->categories		= array(
													1	=> $user->lang['pi_category_1'],
													2	=> $user->lang['pi_category_2'],
													3	=> $user->lang['pi_category_3'],
													4	=> $user->lang['pi_category_4'],
													7	=> $user->lang['pi_category_7'],
												);
    }
    
    public function Categories(){
    	return $this->categories;
    }
    
    // Install a plugin
    public function Install($fieldname, $foldername){
      $tempname   = $_FILES[$fieldname]['tmp_name'];
      $name       = $_FILES[$fieldname]['name'];
      $type       = $_FILES[$fieldname]['type'];
      $size       = $_FILES[$fieldname]['size'];
      
      if(!$tempname){
        $err[] = $user->lang['plugin_error_prefix'].": ".$user->lang['plugin_upload_error1'];
      }elseif(in_array($_FILES[$fieldname]['type'], $this->whitelist)) {
        $err[] = $user->lang['plugin_error_prefix'].": ".$user->lang['plugin_upload_error2'];
      }elseif($_FILES[$fieldname]['size'] > $this->maxfilezize) {
        $err[] = $user->lang['plugin_error_prefix'].": ".$user->lang['plugin_upload_error3'];
      }
    
      if(empty($err)) {
        $this->UnZip($tempname, $foldername);
        return true;
      }else{
        return $err;
      }
    }
    
    /**
    * Download the Package & version List
    *
    * @return array with version info
    */
    private function DownloadPackageList(){
      global $db, $core;
      $myid = 0;
      // Load the List from RSS Feed
      foreach($this->categories as $cid=>$cname){
	      $xml = simplexml_load_file($this->package_feed.'feed'.$cid.'.xml');
	      $rss = $xml->channel;
	      
	      if($rss && $rss->title != 'ACCESS DENIED'){
	        foreach ($rss->item as $item){
	          if(in_array($item->enclosure['type'], $this->whitelist)){    // Only zip files
	            // Flush Database entry
	            $db->query("DELETE FROM __repository WHERE `plugin` = '".$item->plugin."';");
	
	            // Save in Database for later...
	            $db->query("INSERT INTO __repository :params", array(
	                    'id'          => $myid++,
	    		            'plugin'      => $item->plugin,
	    		            'name'        => $item->realname,
	    		            'date'        => strtotime($item->pubDate),
	                    'author'      => $item->author,
	                    'description' => $item->description,
	                    'shortdesc'   => $item->shortdesc,
	                    'title'       => $item->title,
	                    'version'     => $item->version,
	                    'category'    => $item->category,
	                    'level'       => $item->level,
	                    'changelog'   => htmlentities($item->changelog),
	                    'download'    => $item->enclosure['url'],
	                    'filesize'    => $item->enclosure['length'],
	    		            'build'       => $item->build,
	    		            'updated'     => time(),
	    		          ));
	          }
	        } // end foreach rss item
	        $core->config_set('onlinerepo_updated', time());
	        $core->config_set('onlinerepo_blacklisted', 'false');
	      }else{
	      	// set the blacklist_bit...
	      	$core->config_set('onlinerepo_blacklisted', 'true');
	      } // end rss if
    	} // end foreach category...
    }
    
    /**
    * Check if we need to download a new Packagelist
    *
    * @return array with version info
    */
    private function CheckforPackages(){
      global $db, $conf;

  		$sql = "SELECT updated FROM __repository";
  		$result = $db->query($sql);
  		$row = $db->fetch_record($result);
          
      // The Data is Outdated, load the new ones to the DB
			if((time() - $row['updated']) > $this->cachetime){
        $this->DownloadPackageList();
        $this->debug .= 'Load from Net';
			}
    }
    
    /**
    * Build the List with all package information
    *
    * @return array with version info
    */
    public function FetchPackageList($sort='', $pluglist=false){
      global $db;
      
      $this->CheckforPackages();    // Check if there's need to update the Package DB
      
      // Build the Output
      if($sort){
        $sql = "SELECT * FROM __repository WHERE category='".$sort."';";
      }else{
        $sql = "SELECT * FROM __repository";
      }
  		$result = $db->query($sql);
  		while($row = $db->fetch_record($result)){
  		  if($pluglist){
          $plist[$row['plugin']] = array(
              'version'     => $row['version'],
              'build'       => $row['build'],
              'date'        => $row['date'],
              'level'       => $row['level'],
          );
        }else{
          $plist[] = array(
              'download'    => $row['download'],
              'title'       => $row['title'],
              'plugin'      => $row['plugin'],
              'description' => $row['description'],
              'date'        => $row['date'],
              'author'      => $row['author'],
              'version'     => $row['version'],
              'changelog'   => $row['changelog'],
              'lastupdate'  => $row['updated'],
              'shortdesc'   => $row['shortdesc'],
              'category'    => $row['category'],
              'filesize'    => $row['filesize'],
              'build'       => $row['build'],
              'level'       => $row['level'],
                      );
        }
      }
      return $plist;
    }
    
    public function ShowStatus($msg){
      if(is_array($msg)){
        foreach($msg as $error) {
            echo "$error<br>";
        } 
      }
    }
    
    public function UnZip($file, $destination){
      $archive = new PclZip($file);
      $my_extract = $archive->extract(PCLZIP_OPT_PATH, $destination);
      if($my_extract == 0) {
        return "Error : ".$archive->errorInfo(true);
      }else{
        return $my_extract;
      }
    }
    
    public function UploadPackage($fieldname, $move=false){
    	global $pcache, $user;
      $tempname   = $_FILES[$fieldname]['tmp_name'];
      $name       = $_FILES[$fieldname]['name'];
      $upload_id	= md5(time());

      if(!$tempname){
        $err[] = $user->lang['plugin_error_prefix'].": ".$user->lang['plugin_upload_error1'];
      /*}elseif(in_array($_FILES[$fieldname]['type'], $this->whitelist)) {
        $err[] = $user->lang['plugin_error_prefix'].": ".$user->lang['plugin_upload_error2'];*/
      }elseif($_FILES[$fieldname]['size'] > $this->maxfilezize) {
        $err[] = $user->lang['plugin_error_prefix'].": ".$user->lang['plugin_upload_error3'];
      }

      if(empty($err)) {
        $my_files = $this->UnZip($tempname, $pcache->FolderPath('plugincache/'.$upload_id, 'cache'));
        if($move){
        	$perform_move = $this->MoveDownload($pcache->FolderPath('plugincache/'.$upload_id, 'cache'), $my_files[0]['filename']) ;
        	switch ($perform_move){
          	case 'no_xml':				echo $user->lang['plugin_package_error1'];	break;
          	case 'invalid_type':	echo $user->lang['plugin_package_error2'];	break;
        	}
        	$pcache->Delete($pcache->FolderPath('plugincache/'.$upload_id, 'cache'), true);
        }
        return true;
      }else{
        return $err;
      }
    }
    
    public function DownloadPackage($id){
      global $db, $eqdkp_root_path, $pcache;
      $sql = "SELECT download FROM __repository WHERE plugin='".$id."';";
      $result = $db->query($sql);
  		$row = $db->fetch_record($result);
  		
      $urlreader  = new urlreader();
      $upload_id	= md5(time());
      
      // download and unzip
      $this->writeFile($urlreader->GetURL($row['download']), $pcache->FolderPath('plugincache/'.$upload_id, 'cache').'/'.$id.'.zip');
      $this->UnZip($pcache->FolderPath('plugincache/'.$upload_id, 'cache').'/'.$id.'.zip', $pcache->FolderPath('plugincache/'.$upload_id, 'cache'));
      $pcache->Delete($pcache->FolderPath('plugincache/'.$upload_id, 'cache').'/'.$id.'.zip');
      
      // move to final destination
      $this->MoveDownload($pcache->FolderPath('plugincache/'.$upload_id, 'cache'), $pcache->FolderPath('plugincache/'.$upload_id, 'cache').'/'.$id);
      $pcache->Delete($pcache->FolderPath('plugincache/'.$upload_id, 'cache'), true);
    }
    
    public function MoveDownload($source, $zipsource){
      global $eqdkp_root_path;
      if(!$zipsource){
      	$zipsource = $source;	
      }
      if (file_exists($zipsource.'/package.xml')) {
        // read the XML description file...
        $xml = simplexml_load_file($zipsource.'/package.xml');
        
        // check the type...
        switch ($xml['type']){
          case 'plugin':    $target = $eqdkp_root_path.'plugins';   break;
          case 'game':      $target = $eqdkp_root_path.'games';     break;
          case 'template':	$target = $eqdkp_root_path.'templates';	break;
          case 'libraries':	$target = $eqdkp_root_path.'libraries'; break;
          case 'portal':		$target = $eqdkp_root_path.'portal';    break;
        }
        if($target){
	        $target = $target.'/'.$xml->foldername;
	        $this->full_copy($source, $target);
        }else{
        	return 'invalid_type';	
        }
      }else{
        return 'no_xml';
      }
    }
    
    // Copy one dir over another
    private function full_copy($source, $target){
      if (is_dir($source)){
        @mkdir($target);
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
          copy($Entry, $target . '/' . $entry);
        }
        $d->close();
      }else{
        copy($source, $target);
      }
    }
    
    // Remove a dir with files in it
    public function deltree($f) {
      global $pcache;
			if (is_dir($f)) {
        foreach(glob($f.'/*') as $sf) {
          if (is_dir($sf) && !is_link($sf)) {
            deltree($sf);
          } else {
            $pcache->Delete($sf);
          } 
        } 
      }
      rmdir($f);
    }
    
    public function writeFile($data, $name){
      $handle = fopen($name, 'w');
      fwrite($handle, $data);
      fclose($handle);
    }
    
  } 
}
