<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       18 October 2009
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2006-2008 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev$
 *
 * $Id$
 */

define('EQDKP_INC', true);
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');

$myOut = '';
if($in->get('out') != ''){
	switch ($in->get('out'))
	{
		case 'raidplan':				$myOut = BuildMyLink('rss.xml',			'raidplan');break;
		case 'news':					$myOut = BuildMyLink('last_news.xml',	'eqdkp');	break;
		case 'items':					$myOut = BuildMyLink('last_items.xml',	'eqdkp');	break;
		case 'raids':					$myOut = BuildMyLink('last_raids.xml',	'eqdkp');	break;
		case 'member':					$myOut = BuildMyLink('member.xml',	'eqdkp');break;
		case 'shoutbox':				$myOut = BuildMyLink('shoutbox.xml',	'shoutbox');break;
		case 'data':
			include_once($eqdkp_root_path . 'pluskernel/include/data_export.class.php');
			$myexp = new content_export();
			die($myexp->export());
		break;
		case 'eqdkpstatus':
			
			if (isset($conf_plus['pk_servername']) && strlen($conf_plus['pk_servername']))
			{
		      	// build array by exploding
	      		$realmnames		= explode(',', $conf_plus['rs_realm']);
	      		include($eqdkp_root_path.'portal/realmstatus/'.$eqdkp->config['default_game'].'/status.php');
	      		if (is_array($urls)) 
	      		{
	      			foreach ($urls as $key => $value)
	      			{
	      				$status .= "<realmStatusUrl_$key> $value </realmStatusUrl_$key>";
	      			}
	      		}	      		
	    	}
			
$myOut = '<?xml version="1.0" encoding="ISO-8859-1"?>
<rss version="2.0">
	<channel>
	    <title>'.$eqdkp->config['guildtag'].'</title>
        <description> '.$eqdkp->config['dkp_name'].' </description>
        <link>'.$pcache->BuildLink().'</link>
		<guildtag>'.$eqdkp->config['guildtag'].'</guildtag>
		<dkpname>'.$eqdkp->config['dkp_name'].'</dkpname>
		<version>'.EQDKPPLUS_VERSION.'</version>
		<game>'.$eqdkp->config['default_game'].'</game>
		<game_version>'.$eqdkp->config['game_version'].'</game_version>
		<realm>'.$conf_plus['pk_servername'].'</realm>
		<realm_region>'.$conf_plus['pk_server_region'].'</realm_region>
		'.$status.'
	</channel>
</rss>
';
			die($myOut);
		break;
		case 'serverstatus':
				if (isset($conf_plus['pk_servername']) && strlen($conf_plus['pk_servername']))
			{
		      	// build array by exploding
	      		$realmnames		= explode(',', $conf_plus['rs_realm']);
	      		include($eqdkp_root_path.'portal/realmstatus/'.$eqdkp->config['default_game'].'/status.php');
	      		if (is_array($urls)) 
	      		{
	      			foreach ($urls as $key => $value)
	      			{
	      				$status .= "<realmStatusUrl_$key> $value </realmStatusUrl_$key>";
	      			}
	      		}	      		
	    	}
	    		    
			$myOut = '<?xml version="1.0" encoding="UTF-8"?>
								<serverstatus>
									<servername>'.$conf_plus['rs_realm'].'</servername>
									'.$status.'
									<lastcheck>'.time().'</lastcheck>
								</serverstatus>';
			die($myOut);
		break;
		
		case 'wsdl':
			$myOut = "<?xml version ='1.0' encoding ='UTF-8' ?> 
<definitions name='TestServer' 
  xmlns:tns='".$pcache->BuildLink()."exchange.php?out=wsdl' 
  xmlns:soap='http://schemas.xmlsoap.org/wsdl/soap/' 
  xmlns:xsd='http://www.w3.org/2001/XMLSchema' 
  xmlns:soapenc='http://schemas.xmlsoap.org/soap/encoding/' 
  xmlns:wsdl='http://schemas.xmlsoap.org/wsdl/' 
  xmlns='http://schemas.xmlsoap.org/wsdl/'> 

<message name='loginRequest'> 
  <part name='username' type='xsd:string'/>
	<part name='password' type='xsd:string'/> 
</message> 
<message name='loginResponse'> 
  <part name='Result' type='xsd:int'/> 
</message>
<message name='logoutRequest'> 
  <part name='sid' type='xsd:string'/>
</message> 
<message name='logoutResponse'> 
  <part name='Result' type='xsd:bool'/> 
</message>
<message name='raidanmeldungRequest'> 
  <part name='raid_id' type='xsd:int'/>
	<part name='member_id' type='xsd:int'/>
	<part name='status' type='xsd:int'/>
	<part name='sid' type='xsd:string'/> 
	<part name='note' type='xsd:string'/>
	<part name='role' type='xsd:string'/>
</message> 
<message name='raidanmeldungResponse'> 
  <part name='Result' type='xsd:string'/> 
</message>
<message name='statusRequest'> 
  <part name='raid_id' type='xsd:int'/>
	<part name='sid' type='xsd:string'/> 
</message> 
<message name='statusResponse'> 
  <part name='Result' type='xsd:string'/> 
</message>   
<message name='memberRequest'> 
  <part name='username' type='xsd:string'/>
	<part name='sid' type='xsd:string'/> 
</message> 
<message name='memberResponse'> 
  <part name='Result' type='xsd:string'/> 
</message>

<portType name='TestServerPortType'> 
  <operation name='login'> 
    <input message='tns:loginRequest'/> 
    <output message='tns:loginResponse'/> 
  </operation>
	<operation name='logout'> 
    <input message='tns:logoutRequest'/> 
    <output message='tns:logoutResponse'/> 
  </operation>  
	<operation name='raidanmeldung'> 
    <input message='tns:raidanmeldungRequest'/> 
    <output message='tns:raidanmeldungResponse'/> 
  </operation>
	<operation name='members'> 
    <input message='tns:memberRequest'/> 
    <output message='tns:memberResponse'/> 
  </operation> 
	<operation name='status'> 
    <input message='tns:statusRequest'/> 
    <output message='tns:statusResponse'/> 
  </operation> 
</portType> 

<binding name='TestServerBinding' type='tns:TestServerPortType'> 
  <soap:binding style='rpc' transport='http://schemas.xmlsoap.org/soap/http'/> 
  <operation name='login'> 
    <soap:operation soapAction='urn:xmethodsTestServer#login'/> 
    <input> 
      <soap:body use='encoded' namespace='urn:xmethodsTestServer' encodingStyle='http://schemas.xmlsoap.org/soap/encoding/'/> 
    </input> 
    <output> 
      <soap:body use='encoded' namespace='urn:xmethodsTestServer' encodingStyle='http://schemas.xmlsoap.org/soap/encoding/'/> 
    </output> 
  </operation>
	<operation name='logout'> 
    <soap:operation soapAction='urn:xmethodsTestServer#logout'/> 
    <input> 
      <soap:body use='encoded' namespace='urn:xmethodsTestServer' encodingStyle='http://schemas.xmlsoap.org/soap/encoding/'/> 
    </input> 
    <output> 
      <soap:body use='encoded' namespace='urn:xmethodsTestServer' encodingStyle='http://schemas.xmlsoap.org/soap/encoding/'/> 
    </output> 
  </operation>
	  <operation name='raidanmeldung'> 
    <soap:operation soapAction='urn:xmethodsTestServer#raidanmeldung'/> 
    <input> 
      <soap:body use='encoded' namespace='urn:xmethodsTestServer' encodingStyle='http://schemas.xmlsoap.org/soap/encoding/'/> 
    </input> 
    <output> 
      <soap:body use='encoded' namespace='urn:xmethodsTestServer' encodingStyle='http://schemas.xmlsoap.org/soap/encoding/'/> 
    </output> 
  </operation>
	<operation name='members'> 
    <soap:operation soapAction='urn:xmethodsTestServer#members'/> 
    <input> 
      <soap:body use='encoded' namespace='urn:xmethodsTestServer' encodingStyle='http://schemas.xmlsoap.org/soap/encoding/'/> 
    </input> 
    <output> 
      <soap:body use='encoded' namespace='urn:xmethodsTestServer' encodingStyle='http://schemas.xmlsoap.org/soap/encoding/'/> 
    </output> 
  </operation>
	<operation name='status'> 
    <soap:operation soapAction='urn:xmethodsTestServer#status'/> 
    <input> 
      <soap:body use='encoded' namespace='urn:xmethodsTestServer' encodingStyle='http://schemas.xmlsoap.org/soap/encoding/'/> 
    </input> 
    <output> 
      <soap:body use='encoded' namespace='urn:xmethodsTestServer' encodingStyle='http://schemas.xmlsoap.org/soap/encoding/'/> 
    </output> 
  </operation>  
</binding> 

<service name='TestServerService'> 
  <port name='TestServerPort' binding='TestServerBinding'> 
    <soap:address location='".$pcache->BuildLink()."exchange.php?out=app'/> 
  </port> 
</service> 
</definitions>";
			die($myOut);
		break;
		case 'app':
			//The SOAP-Things
				$server = new SoapServer(NULL, array('uri' => $pcache->buildlink(), 'exceptions'	=> 0));                
				$server->addFunction('login');
				$server->addFunction('raidanmeldung');
				$server->addFunction('members');
				$server->addFunction('status');
				$server->handle();
				die();
		break;
		
		
	}
	
	if($myOut){
		ob_end_clean();
		ob_start();
		if(!readfile($myOut)){
			die('no_data');	
		}
	}else{
		die('no_file');	
	}
}else{
	die('no_selection');	
}

function BuildMyLink($xml, $plugin){
	global $pcache;
	if($pcache->FileExists($xml, $plugin)){
		return $pcache->BuildLink().$pcache->FileLink($xml, $plugin, false);
	}else{
		return '';
	}
}

/**
* User-Login
* 
* @param $username	The Username
* @param $password	The Password
*/
function login($username, $password){
	global $user, $pm;
	
	if ($pm->check(PLUGIN_INSTALLED, 'raidplan')){
			//Login, return 0 if bad login
		if($user->login($username, $password, false))
		{
			return $user->data['session_id'];
		} else {
			return 0;
		}
	
	} else {
		return 'error: Raidplan not installed';
	}	

}

/**
* User-Logout
* 
* @param $sid Session-ID
*/
function logout($sid){
	global $db;
	$user_id = session_check($sid);
	if ($user_id){
		$sql = 'DELETE FROM ' . SESSIONS_TABLE . " WHERE session_id='" . $db->escape($sid) ." AND session_user_id='" . $db->escape($user_id) . "'";
  	$db->query($sql);
		return true;
	} else {
		return false;
	}
}


/**
* Session-Check
* 
* @param $sid	The Session-ID
*/
function session_check($sid){
	global $db;

	$ip_address   = ( !empty($_SERVER['REMOTE_ADDR']) )     ? $_SERVER['REMOTE_ADDR']     : $REMOTE_ADDR;
	$current_page = 'Iphone-App';
	 
	$sql = 'SELECT u.*, s.*
                    FROM ' . SESSIONS_TABLE . ' s, ' . USERS_TABLE . " u
                    WHERE s.session_id = '".$db->sql_escape($sid)."'
                    AND u.user_id = s.session_user_id";
  $result = $db->query($sql);

	$data = $db->fetch_record($result);
	$db->free_result($result);

	// Did the session exist in the DB?
	if ( isset($data['user_id']) )
	{
		
			// Validate IP length
			$s_ip = implode('.', array_slice(explode('.', $data['session_ip']), 0, 4));
			$u_ip = implode('.', array_slice(explode('.', $ip_address),         0, 4));

			if ( $u_ip == $s_ip )
			{
					return $data['user_id'];
			}
	}
	return false;
}

/**
* Raidanmeldung
* 
* @param $raid_id	Raid-ID
* @param $member_id
* @param $status - Ob Angemeldet, Abgemeldet, Ersatzbank
*	1 = anmelden, 2 = abmelden, 3 = Ersatzbank, 10 = Update
* @param $sid	Session-ID
* @param $note	Notiz des Members zu seiner Anmeldung
* @param $role die Rolle des Members
*/
function raidanmeldung($raid_id, $member_id, $status = 1, $sid = 0, $note = '', $role = ''){
	global $SID, $db, $eqdkp, $user, $tpl, $pm, $conf, $rpclass, $stime, $rpconvert, $khrml, $logmanager, $eqdkp_root_path, $table_prefix;
	$user_id = session_check($sid);
	if ($user_id != false && $user_id > 0 && $user->check_auth('u_raidplan_view', false, $user_id)){
		
		//Raidanmeldung
		//===============================================================
		$statusID 					= $status;	
		
		require($eqdkp_root_path.'plugins/raidplan/includes/common.php');
		

		$sql = 'SELECT raid_id, raid_name, raid_link, raid_distribution, raid_date_added, raid_date_change, raid_date, raid_date_subscription, raid_date_finish, raid_date_invite, raid_note, raid_leader, raid_value, raid_added_by, raid_updated_by, raid_closed, raid_attendees
				    FROM ' . RP_RAIDS_TABLE . "
				    WHERE raid_id='" . (int) $raid_id . "'";
		$result = $db->query($sql);
		if (!$row = $db->fetch_record($result)) { return($user->lang['error_invalid_raid_provided']); }
		$db->free_result($result);

		$raid = array(
			'raid_added_by'						=> post_or_db('raid_added_by', $row),
			'raid_updated_by'         => post_or_db('raid_updated_by', $row),
			'raid_name'								=> post_or_db('raid_name', $row),
			'raid_date'								=> post_or_db('raid_date', $row),
			'raid_date_added'					=> post_or_db('raid_date_added', $row),
			'raid_date_subscription'	=> post_or_db('raid_date_subscription', $row),
			'raid_date_invite'				=> post_or_db('raid_date_invite', $row),
			'raid_date_finish'				=> post_or_db('raid_date_finish', $row),
			'raid_date_change'        => post_or_db('raid_date_change', $row),
			'raid_note'								=> post_or_db('raid_note', $row),
			'raid_leader'							=> post_or_db('raid_leader', $row),
			'raid_value'							=> post_or_db('raid_value', $row),
			'raid_link'								=> post_or_db('raid_link', $row),
			'raid_closed'             => post_or_db('raid_closed', $row),
			'raid_attendees'					=> post_or_db('raid_attendees', $row),
			'raid_subscribed'					=> false,
			'raid_distribution'       => post_or_db('raid_distribution', $row),
		);

		if (!$raid['raid_value']) { $raid['raid_value'] = $rpclass->get_raid_value($raid['raid_name']); }
   
    // Build the Wildcard Array
    if($conf['rp_wildcard'] == 1){
      $rpevent_name = addslashes($rpclass->IdToEventName($raid_id));
      $wcsql = "SELECT wildcard, date, member_name
                FROM " . RP_WILDCARD_TABLE . "
                WHERE event='".$rpevent_name."';";
      $wcresult = $db->query($wcsql);
      
      while ($wcrow = $db->fetch_record($wcresult)){
        $wildcards[$wcrow['member_name']] = array(
                'wcexpire'   => $wcrow['date'],
                'wildcard'   => $wcrow['wildcard']
          );
      }
      $db->free_result($wcresult);
    }
		
		//
		// Get members
		//
		if ($member_id > 0){		  
			$sql =  "SELECT users.user_id, members.member_id, ranks.rank_id, members.member_name, classes.class_name, classes.class_id, attendees.raid_id, attendees.role, attendees.attendees_subscribed, attendees.attendees_random, attendees.attendees_note
							FROM (" . MEMBERS_TABLE . " as members, " . MEMBER_USER_TABLE . " as users, " . CLASS_TABLE . " as classes, ".MEMBER_RANKS_TABLE." as ranks) 
							LEFT JOIN " . RP_ATTENDEES_TABLE . " as attendees
							ON (members.member_id=attendees.member_id AND attendees.raid_id=" . (int) $raid_id . ")";
			if($raid['raid_distribution'] == 1){
        $sql .= "WHERE members.member_class_id=classes.class_id";
      }else{
        $sql .= "WHERE members.member_class_id=classes.class_id";
			}
			$sql .= " AND members.member_id=users.member_id
							AND members.member_rank_id = ranks.rank_id
							AND users.user_id=" . $user_id ."
							ORDER BY attendees.attendees_random DESC";
			$result = $db->query($sql);
			while ($row = $db->fetch_record($result)){        
				$members[$row['member_id']] = array(
					'id'					=> $row['member_id'],
					'name'				=> $row['member_name'],
					'class_name'	=> $row['class_name'],
					'class_id'    => $row['class_id'],
					'subscribed'	=> $row['attendees_subscribed'],
					'random'			=> $row['attendees_random'],
					'rank'				=> $row['rank_id'],
					'role'        => $row['role'],
					'note'				=> stripslashes($row['attendees_note'])
        );
				if($row['attendees_random']>0) { $raid['raid_subscribed'] = true; }
			}
			$db->free_result($result);
		}
		
			$sql = "SELECT members.*, class_name, class_id, member_user.user_id, ranks.rank_name, ranks.rank_prefix, ranks.rank_suffix, ranks.rank_hide, attendees.role
	            FROM (" . MEMBERS_TABLE . " as members, " . MEMBER_USER_TABLE . " as member_user, " . CLASS_TABLE . " as classes, " . USERS_TABLE . " as users, " . MEMBER_RANKS_TABLE . " as ranks)
	            LEFT JOIN " . RP_ATTENDEES_TABLE . " as attendees	ON (members.member_id=attendees.member_id AND attendees.raid_id=" . (int) $raid_id . ")
	      			WHERE members.member_id=member_user.member_id
	      			AND classes.class_id=members.member_class_id
	      			AND member_user.user_id=users.user_id
	      			AND members.member_rank_id = ranks.rank_id";
	    if($eqdkp->config['hide_inactive'] == 1){
			  $sql .= " AND members.member_status='1'";
			}
			$sql .= " ORDER BY members.member_name";
			$result = $db->query($sql);
			while ($row = $db->fetch_record($result)){
        
        // Fill the members array
				$member[$row['member_id']] = array(
	        'id'            => $row['member_id'],
	        'name'          => $row['member_name'],
					'class_name'		=> $row['class_name'],
					'class_id'      => $row['class_id'],
					'status'				=> $row['attendees_subscribed'],
					'user_id'				=> $row['user_id'],
					'random'				=> $row['attendees_random'],
					'rank'					=> $row['rank_name'],
					'rank_prefix'		=> $row['rank_prefix'],
					'rank_suffix'		=> $row['rank_suffix'],
					'level'         => $row['member_level'],
					'rank_hide'     => $row['rank_hide'],
					'note'					=> $row['attendees_note'],
					'role'          => $row['role'],
					'signup_time'  	=> $stime->DoDate($conf['timeformats']['short'],$row['attendees_signup_time']),
					'change_time'  	=> $stime->DoDate($conf['timeformats']['medium'],$row['attendees_change_time']),
					'wildcard'			=> $wildcards[$row['member_name']]['wildcard'],
	        'member_status' => $row['member_status'],
	      );
	      
			}
      $db->free_result($result);
      	

		
		foreach ($members as $membercheck){
			
      //auto confirm part
      if($membercheck['id'] == $member_id){
        if($statusID == 1){
          $statusID = (($membercheck['rank'] == $conf['rp_rank_team'] && $conf['rp_enable_team'] == 1) || ($membercheck['rank'] == $conf['rp_rank'] && $conf['rp_enabl_officr'] == 1 && $conf['rp_disabl_cl_ac'] != 1))? 0 : 1;
        }
      } 

  		// If the attendee should be updated
      if (!is_null($membercheck['subscribed'])){
  			$pre_member_id  = $membercheck['id'];
  			$statusID       = ($statusID == '10') ? $membercheck['subscribed'] : $statusID;
				$subscribed_member_id 		=  $membercheck['id'];
				$signin_member_random 		=  $membercheck['random'];
  		}
		}
		
			$deadline_reached = true;
			if ($raid['raid_date_subscription'] > $stime->DoTime()){     // If sign in date is NOT expired
				$deadline_reached = false;
				if ($subscribed_member_id){     // If the user already signed in
					if ($signin_member_random > 0){
					 $val_subscription = false;    // the member is signed in
					 $val_update       = true;     // update the entry
					}else{
					 $val_subscription = false;
					 $val_update       = false;
					}
				}else{
				  $val_subscription = true;
					$val_update       = false;
				} 
		  }else{
		    // the user is subsribed && should be able to change the data until xx minutes before the raid
		    $minutes2midnight = ($conf['rp_chsigndvalue']) ? ($conf['rp_chsigndvalue']*60) : (60*30);
		  	if ($subscribed_member_id && $signin_member_random > 0 && ($raid['raid_date_invite']-$minutes2midnight) > $stime->DoTime()){
          $val_subscription = false;
					$val_update       = true;
					$val_delete       = ($conf['rp_changesigned'] == 1) ? true : false;
					$deadline_reached = false;
				}
		  }  
		
		
		if ($statusID < 4 && !$val_subscription){
			return "Anmeldung nicht mehr moeglich (vlt. schon angemeldet)";
		}
		if ($statusID == 10 && !$val_update){
			return "Update nicht mehr moeglich";
		}
		
		// Delete old Wildcard if Member is confirmed for that event
		if($statusID == 0){
		  $rpclass->DeleteOldWildcard($member_id, $raid_id);
    }

		// Insert the 
		if ($pre_member_id > 0){
			// Member subscribed earlier - Change Status
			$query = $db->build_query('UPDATE', array(
				'member_id'							=> stripslashes($member_id),
				'attendees_subscribed'	=> $statusID,
				'attendees_change_time' => $stime->DoTime(),
				'role'                  => ($raid['raid_distribution'] == 1) ? stripslashes($role) : '',
				'attendees_note'        => $khrml->CleanInput($note)
      ));
			$db->query('UPDATE ' . RP_ATTENDEES_TABLE . ' SET ' . $query . "
				          WHERE raid_id='" . stripslashes($raid_id) . "'
				          AND member_id='" . $pre_member_id . "'");
		}else{		
			// This is the first subscription with this member
      srand((double)microtime()*1000000);
      $rand_value = rand(1,100);
			$query = $db->build_query('INSERT', array(
				'raid_id'								=> stripslashes($raid_id),
				'member_id'     				=> stripslashes($member_id),
				'attendees_subscribed'	=> $statusID,
				'attendees_signup_time' => $stime->DoTime(),
				'attendees_change_time' => $stime->DoTime(),
				'attendees_random'			=> $rand_value,
				'role'                  => ($raid['raid_distribution'] == 1) ? stripslashes($role) : '',
				'attendees_note'        => $khrml->CleanInput($note)
      ));
			$db->query('INSERT INTO ' . RP_ATTENDEES_TABLE . $query);			
		}
		
		// LogManager entry
		$myrole = ($raid['raid_distribution'] == 1) ? stripslashes($role) : '';
		$status2name = array('{L_LOG_STATUS_0}','{L_LOG_STATUS_1}','{L_LOG_STATUS_2}','{L_LOG_STATUS_3}');
		$rplog_action = array(
        'header'          => '{L_LOG_CHANGED_STATUS}',
        '{L_LOG_MEMBER}'  => $rpclass->MemberID2Name(stripslashes($member_id)),
        '{L_LOG_STATUS}'  => $status2name[$statusID],
        '{L_LOG_ROLE}'    => $myrole,
        '{L_LOG_RANDOM}'  => ($rand_value) ? $rand_value : $membercheck['random'],
    );
		$logmanager->AddEntry(stripslashes($raid_id), '{L_LOG_CHANGED_STATUS}', $rplog_action);
	
		
		
		//=================================================================

		//$db->query("DELETE FROM __sessions WHERE session_id = '".$db->escape($user->data['session_id'])."'");
		return "Du wurdest fuer den Raid ".$raid_id." auf den Status ".$status." gesetzt.";

	} else {
		//Fehler: nicht authentifiziert: keine SID vorhanden
		//$db->query("DELETE FROM __sessions WHERE session_id = '".$db->escape($user->data['session_id'])."'");
		return "error: Authentifizierung fehlgeschlagen";
	}

}

/**
* Members - gibt die Charaktere eines Users als String zurück
* 
* @param $username
*/
function members($username, $sid){
	global $db;
	if (session_check($sid)){
		$query = $db->query("SELECT m.member_id, m.member_name FROM __members m, __member_user mu, __users u WHERE u.username ='".$username."' AND u.user_id = mu.user_id AND m.member_id = mu.member_id AND m.member_status = '1'");
		while ($row = $db->fetch_record($query)) {
			$members = sanitize($row['member_id']).';'.sanitize($row['member_name']).'|';
		};
		return $members;
	} else {
		return "error: Authentifizierung fehlgeschlagen"; 
	}
}

/**
* Status - gibt den Anmeldestatus eines Members für einen Raid zurück
* 
* @param $raid_id - ID des Raids
* @param $sid - Session-ID
*/
function status($raid_id, $sid){
	global $db;
	$user_id = session_check($sid);
	if ($user_id){
			$sql2 = 'SELECT member_id	FROM __member_user WHERE user_id = '. $db->escape($user_id) .'';
			$result2 = $db->query($sql2);
			$member_ids = array();
	
			//get all memberIDs
			while ( $row2 = $db->fetch_record($result2) ){
				$member_ids[] = $row2[member_id]  ;
			}
			if(is_array($member_ids)){
				$sql = "SELECT attendees_subscribed, attendees_note, attendees_signup_time FROM __raidplan_raid_attendees WHERE raid_id=".$db->escape($raid_id)." AND member_id in ('".join_array("', '", $member_ids)."')";
				$result = $db->query($sql);
				
				//found some raids
				$row = $db->fetch_record($result);
				if($row){
					if ($row['attendees_signup_time']){     //only if the user has allready signed on
						$own_status['status'] = $row['attendees_subscribed'];
						$own_status['note'] 	= $row['attendees_note'];
						return $own_status['status'];
					} else {
						return 'none';
					}
				}
			}
		
	} else {
		return "error: Authentifizierung fehlgeschlagen"; 
	}
}

?>