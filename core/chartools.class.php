<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2006
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

class CharTools
{
  
  /**
  * Check if Server is Online
  **/
  function CheckUptime($sitename){
    if(function_exists('fsockopen')){
      $fp = fsockopen($sitename, 80, $errno, $errstr, 10);
      $upout = ($fp) ? 'Online' : 'Offline';
    }else{
      $upout = 'n/a';
    }
    return $upout;
  }
  
  /**
  * Generate the dynamic Fields...
  **/
  function generateField($confvars, $name, $value){
  	global $html, $game, $jquery;
		
		$confvars['name'] = $name;
		$confvars['value'] = $confvars['selected'] = $value;
  	
		switch($confvars['fieldtype']){		
  		// Dropdown - load glang for dropdown-options
  		case 'dropdown':
  			$drpdownfields = array();
				foreach($confvars['options'] as $namee=>$valuee){
					$drpdownfields[$namee] =  ($game->glang($valuee)) ? $game->glang($valuee) : $valuee;
				}
			$confvars['options'] = $drpdownfields;
  		break;
  	}
		
		$ccfield = $html->widget($confvars);
  	return $ccfield;
  }
  
  /**
  * Update Character Connection
  **/
	function updateConnection($member_id, $user_id = 0){
    global $db, $user, $config, $pdh;
    $user_id = ($user_id == 0) ? $user->data['user_id'] : $user_id;
		
		// Users -> Members associations
    $sql = 'DELETE FROM __member_user
            WHERE user_id = ' . $user_id;
    $db->query($sql);

    if (is_array($member_id) && count($member_id) > 0){
      $sql = 'INSERT INTO __member_user
              (member_id, user_id)
              VALUES ';
      $query = array();
      foreach ( $member_id as $memberid ){
        $query[] = '(' . $memberid . ', ' . $user_id . ')';
      }

      $sql .= implode(', ', $query);
      $db->query($sql);
			$myupdate = true;
    }else{
      $myupdate = false;
    }
		$pdh->enqueue_hook('update_connection');
		$pdh->process_hook_queue();
    return $myupdate;
	}
	
	/**
  * Delete the Character
  **/
	function DeleteChar($member_id){
		global $db, $pdh;
	
		$pdh->put('member', 'delete_member', array($member_id));
		$pdh->process_hook_queue();
		return true;
	}
	
	/**
  * Mark the char for deletion for an admin
  **/
	function SuspendChar($member_id){
		global $db, $pdh;
		$db->query("UPDATE __members SET member_status='0' WHERE member_id=".$member_id);
		$db->query("UPDATE __members SET requested_del='1' WHERE member_id=".$member_id);
		
		$pdh->enqueue_hook('member_update');
		$pdh->process_hook_queue();
	}
	
	/**
  * Rewoke a char (un-delete!)
  **/
	function RewokeChar($member_id){
		global $db, $pdh;
		$db->query("UPDATE __members SET member_status='1' WHERE member_id=".$member_id);
		$db->query("UPDATE __members SET requested_del='0' WHERE member_id=".$member_id);
		
		$pdh->enqueue_hook('member_update');
		$pdh->process_hook_queue();
	}
	
	/**
  * Confirm a Char
  **/
	function ConfirmChar($member_id){
		global $db, $pdh;
		$db->query("UPDATE __members SET :params WHERE member_id='".$db->escape($member_id)."'", array(
			'member_status'		=> '1',
			'requested_del'		=> '0',
			'require_confirm'	=> '0',
		));
		
		$pdh->enqueue_hook('member_update');
		$pdh->process_hook_queue();
	}
	
	/**
  * Confirm all chars
  **/
	function ConfirmAllChars(){
		global $db, $pdh;
		$confirm = $pdh->get('member', 'confirm_required');
		if (is_array($confirm)){
			foreach ($confirm as $member){
				$this->ConfirmChar($member);
			}
		}
	}
	
	/**
  * Delete all chars
  **/
	function DeleteAllChars(){
		global $db, $pdh;
		$deletion = $pdh->get('member', 'delete_requested');
		foreach($deletion as $member){
			$this->DeleteChar($member);
		}
	}
	
	/**
  * Take the Character
  **/
  function TakeOverChar($membername){
		global $db, $user;
    $sql = "SELECT member_id FROM __members WHERE member_name = '".$db->sql_escape($membername)."'";
    $member_id = $db->query_first($sql);
    $sql = 'INSERT INTO __member_user
            (member_id, user_id)
            VALUES ('.$member_id.', '.$user->data['user_id'].')';
    $db->query($sql);
  }
  
  /**
  * Update the Profile fields
  **/
  function updateChar($memberid='', $membername='', $dataarray = '', $isImport=false){
		global $db, $user, $cmapi, $pdh, $in;
		
		// Import or $_POST?
    $impvar         = ($dataarray && is_array($dataarray)) ? $dataarray : $_POST;
		
    // Make sure that each member's name is properly capitalized
    $membername     = ($membername) ? $membername : $impvar['member_name'];
    $member_name    = $membername;
    if(!$isImport){
		//$member_name    = strtolower(preg_replace('/[[:space:]]/i', ' ', $membername));
		$member_name    = mb_ucfirst($member_name);
    }
    
    // Check for existing member name
    $mySQLsentence  = ($memberid) ? "member_id='".$db->sql_escape($memberid)."'" : "member_name='".$db->sql_escape($member_name)."'";
    $member_id      = $db->query_first("SELECT member_id FROM __members WHERE ".$mySQLsentence);
    $isaddmember    = ( isset($member_id) && $member_id > 0 ) ? false : true;
		
		// Get the XML Field structure
		$sql = 'SELECT * FROM __member_profilefields';
		$result = $db->query($sql);
		$myXML = array();
		while($drow = $db->fetch_record($result)){
			$myXML[$drow['name']] = $impvar[$drow['name']];
		}

		// Save the whole thing..
		if($isaddmember){
			$save_array = array(
									$member_name,
									(($impvar['member_level']) ? $impvar['member_level'] : 0),
									$impvar['member_race_id'],
									$impvar['member_class_id'],
									(($user->config['uc_defaultrank']) ? $user->config['uc_defaultrank'] : 0),
									'',
									'1',
									$this->ValueorNull(htmlspecialchars($impvar['notes'], ENT_QUOTES)),
									$cmapi->xmltools->Array2Database($myXML),
									$impvar['last_update'],
									((!$isImport) ? $impvar['member_pic'] : '')
								);
			$memberid = $pdh->put('member', 'add_member', $save_array);
    }else{
    	$save_array = array(
    							$member_id,
    							$name='',
    							((isset($impvar['member_level'])) ? $impvar['member_level'] : 0),
    							$impvar['member_race_id'],
									$impvar['member_class_id'],
    							(($user->config['uc_defaultrank']) ? $user->config['uc_defaultrank'] : 0),
    							$mainid='',
    							'',
    							$this->ValueorNull(htmlspecialchars($impvar['notes'], ENT_QUOTES)),
    							$cmapi->xmltools->Array2Database($myXML),
    							$impvar['last_update'],
    						);
    	$memberid = $pdh->put('member', 'update_member', $save_array);
    }
    $pdh->enqueue_hook('member_update');
		$pdh->process_hook_queue();
    if($memberid){
      $failure_message = array('true','','');
    }else{
      $failure_message = array('false','','');
    }
    
    // Take the cake... oh.. its a char.. :D.. maybe a female char IN a cake? :D
    if ($in->get('overtakeuser')){
	      $this->TakeOverChar($member_name);
    }
    
    return $failure_message;
	} // end of update
  
  /**
  * Value or NULL Helper Class
  **/
  function ValueorNull($inp){
  	return ($input) ? $input : (($type == 'int') ? 0 : '');
  }
}// end of class

class cmAPI
{
  var $categories   = array();
  var $dynfields    = array();
  var $config       = array();
	
	function __construct(){
    global $eqdkp_root_path, $user, $db, $game, $CharTools, $pdh;
    
    $this->xmltools   = new XMLtools();
    $this->CharTools  = $CharTools;
    $this->profiles		= $this->FetchProfiles();
    $this->dynfields	= $pdh->get('profile_fields', 'fields');
		$this->categories	= $pdh->get('profile_fields', 'categories');
  }
	
	/*************************************
	 * PRIVATE FUNCTIONS
	 ************************************/  
	
	//Fetch the Profiles out of the Database
	private function FetchProfiles(){
    global $db, $table_prefix, $user;
    $sql = "SELECT * FROM __members";
    $result = $db->query($sql);
    $profiledata = array();
		while ($memdata = $db->fetch_record($result)){
	   	$myarr_part1 = array(
        'mem_notes'   => array(
                            'name'      => $user->lang['note'],
                            'data'      => $memdata['notes'],
                            'category'  => 'notes',
                          ),
      );
      
      $myarr_part2 = array();
      foreach($this->xmltools->Database2Array($memdata['profiledata']) as $mmdata=>$value){
        $myarr_part2[$mmdata] = array(
                            'name'      => $this->dynfields[$mmdata]['language'],
                            'data'      => (is_array($value)) ? '' : $value,
                            'category'  => $this->dynfields[$mmdata]['category'],
                            'list'      => $this->dynfields[$mmdata]['list'],
                            'image'     => 'games/'.$this->myGame.'/images/'.$this->dynfields[$mmdata]['category'].'/'.$this->dynfields[$mmdata]['image']
                          );
      }
      $profiledata[$memdata['member_id']] = array_merge($myarr_part1, $myarr_part2);
		}
		$db->free_result($result);
		return $profiledata;
  }
	
	/*************************************
	 * NON PRIVATE FUNCTIONS
	 ************************************/   	
	// Get the Member Profile for a member ID
  function MemberProfile($member_id){
    return $this->profiles[$member_id];
  }
  
  // Get all Profiles
  function MemberProfiles(){
    return $this->profiles;
  }
  
  // Get the Dynamic Fields Array
  function GetDynFields(){
    return $this->dynfields;
  }
	
	// Get Tabulator Categories
	function GetCategories(){
    return $this->categories;
  }
	
	// Get the Charmanager Configuration Data
	function GetConfig(){
    return $this->config;
  }
  
  // Is the Charmanager installed?
  function isInstalled(){
    return $this->Installed;
  }
  
  function Game(){
    return $this->myGame;
  }
}
?>