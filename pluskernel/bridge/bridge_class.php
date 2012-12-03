<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       11 June 2007
 * Date:        $Date:  $
 * -----------------------------------------------------------------------
 * @author      $Author:  $
 * @copyright   2006-2008 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev: 516 $
 * -----------------------------------------------------------------------
 * EQdkp Bridge Data Class
 * created first for www.allvatar.com
 * 
 * SVN $Id: bridge_class.php 40 2007-07-06 15:40:27Z knaak $
 */

define('EQDKP_INC', true);


class eqdkp_bridge
{
	// objects
	var $table_prefix = 'eqdkp_';


	/**
	 * Constructor...
	 *
	 * @param Database $this->br_db
	 * @return eqdkp_bridge
	 */
	function eqdkp_bridge()
	{

		global $dbtype, $dbhost, $dbname, $dbuser , $dbpass, $table_prefix, $sql_db;

		$this->table_prefix = $table_prefix;
		$this->br_db = new $sql_db();
		$this->br_db->sql_connect($dbhost, $dbname, $dbuser, $dbpass, false);
	}


	/**
	 * Einfacher Counter der die Summe der Items/Member/Users/Raids
	 * simple counter
	 *
	 * @return Array
	 */
	function get_eqdkp_status()
	{
		$data = array();

		// Get total items
		$sql = "SELECT count(item_id) FROM ".ITEMS_TABLE;
		$data['total_items']  = $this->br_db->query_first($sql);

		// Get total members
		$sql = "SELECT count(member_id) FROM ".MEMBERS_TABLE;
		$data['total_members'] = $this->br_db->query_first($sql);

		// Get total users
		$sql = "SELECT count(user_id) FROM ".USERS_TABLE;
		$data['total_users'] = $this->br_db->query_first($sql);

		// Get total Raids
		$sql = "SELECT count(raid_id) FROM ".RAIDS_TABLE;
		$data['total_raids'] = $this->br_db->query_first($sql);

		return $data;
	}

	/**
	 * Gibt den Status eines Members als Array zurück
	 * ob sich dieser schon für einen Raid/Termin im Eqdkp Raidplaner angemeldet hat
	 *
	 * ['status'] kann eine Zahl zwischen -2 und 3 sein
	 *
	 * ['status'] -2 = Fehler, bzw Raid nicht gefunden
	 * ['status'] -1 = Spieler hat sich noch garnicht gemeldet
	 * ['status']  0 = besätigt gemeldet
	 * ['status']  1 = angemeldet
	 * ['status']  2 = abgemeldet
	 * ['status']  3 = Zur Ersatzbank gemeldet
	 *
	 * ['note'] = Notiz die der Member beim Melden eingeben kann
	 *
	 * @param String $playername (Eqdkp Membername)
	 * @param Integer $raidid (Eqdkp Raidplaner ID)
	 * @return Array / 0 / -1 (Array wenn Eintrag gefunden wurde, 0 wenn sich Spieler noch nicht gemeldet hat, -1 bei Error)
	 */
	function get_is_player_signin_raid($playername, $raidid)
	{

		$a_return = array();
		$memberID = 0;

		//Eqdkp Member ID suchen
		$sql = "SELECT member_id
					  FROM ".MEMBERS_TABLE."
					  WHERE member_name= '".$playername;
		$memberID = $this->br_db->query_first($sql) ;

		if($memberID==0){$a_return['status'] = -2;}

		//Beteiligung an RaidID des Members suchen
		$sql = "SELECT attendees_subscribed,attendees_note,confirmed   FROM ".RP_ATTENDEES_TABLE."
					  WHERE raid_id=".$raidid."
					  AND member_id=".$memberID ;

		$result = $this->br_db->query($sql);
		if($row = $this->br_db->fetch_record($result))
		{
			$a_return['status'] 	= $row['attendees_subscribed'];
			$a_return['note'] 		= $row['attendees_note'];
		}
		else {$a_return['status'] = 0;}

		return $a_return;
	}


	/**
	 * Gibt die Anzahl der nächsten Raids zurück
	 *
	 * @return Integer
	 */
	function get_next_raids_count()
	{
		$ret_val = 0 ;

		$sql = "SELECT count(raid_id)
	  		    FROM ".RP_RAIDS_TABLE."
			    WHERE raid_date > ".time();

		$ret_val = $this->br_db->query_first($sql);

		return $ret_val;
	}



	/**
	 * Gibt ein Array mit den nächsten Raids einer Gruppe zurück
	 *
	 * 		['name'] 							= Name des Raids
	 *		['date'] 							= Datum des Raids als Unix Timestamp
	 *		['icon'] 							= Icon des Raids ohne Path | Die Icons im Format 64x64 liegen in "<img src=eqdkp/games/gamename/events/"..">" ;
	 *		['count_total_benoetigt'] 			= Anzahl der insgesamt benötigten Member
	 *		['count_signin_angemeldet '] 		= Anzahl der angemeldeten Member
	 *		['count_signedout_abgemeldet'] 		= Anzahl der abgemeldeten Member
	 *		['count_affirmed_bestaetigt'] 		= Anzhal der bestätigten Member
	 *		['count_fehlende_anmeldungen'] 		= Anzahl der fehlenden Anmeldungen
	 *
	 * @param integer $limit Limit der Anzeige
	 * @return Array or 0
	 */
	function get_next_raids($limit)
	{

		$a_return = 0;
		$count_signin = 0;

		//Suche alle anstehenden Raids

		$sql = "SELECT *
				FROM ".RP_RAIDS_TABLE."
				WHERE raid_date > ".time();

		$sql.= " ORDER BY `raid_date` ASC LIMIT ".$limit;

		$result = $this->br_db->query($sql);
		$num_results = $this->br_db->num_rows($result);
		$a_return = array();

		while ( $row = $this->br_db->fetch_record($result) )
		{
		  	// count the signed in members
		    $sql_count = "SELECT count(member_id)
		    			  FROM ".RP_ATTENDEES_TABLE."
		    			  WHERE attendees_subscribed=0 AND confirmed=0 AND raid_id=" . $row['raid_id'];
			$count_affirmed = $this->br_db->query_first($sql_count) ;

		  	// count the signed in members
		    $sql_count = "SELECT count(member_id)
		    			  FROM ".RP_ATTENDEES_TABLE."
		    			  WHERE attendees_subscribed=1
		    			  AND confirmed=0 AND raid_id=" . $row['raid_id'];
		    $count_signin = $this->br_db->query_first($sql_count) ;

		  	// count the confirmed members
		    $sql_count = "SELECT count(member_id)
		    			  FROM ".RP_ATTENDEES_TABLE."
		    			  WHERE attendees_subscribed=1
		    			  AND confirmed=1 AND raid_id=" . $row['raid_id'];
		    $count_confirmed = $this->br_db->query_first($sql_count) ;

		  	// count the signedout members
		    $sql_count = "SELECT count(member_id)
		    			  FROM ".RP_ATTENDEES_TABLE."
		    			  WHERE attendees_subscribed=2
		    			  AND confirmed=0 AND raid_id=" . $row['raid_id'];
		    $count_signedout = $this->br_db->query_first($sql_count) ;

		  	// count the total sum
		    $sql_count = "SELECT raid_attendees
		    			  FROM ".RP_RAIDS_TABLE."
		    			  WHERE raid_id='".$row['raid_id']."'";
		    $count_total = $this->br_db->query_first($sql_count) ;

			// get event icon from DB
			$sql = "SELECT event_icon
					FROM ".EVENTS_TABLE."
					WHERE event_name='".$row['raid_name']."'";
			$icon = $this->br_db->query_first($sql);

		    //Berechne fehlende Anmeldungen
    		$diffgest = $count_total - $count_signin ;

    		$_id = $row['raid_id'] ;
			$a_return[$_id]['name'] 							= stripslashes($row['raid_name']) ;
			$a_return[$_id]['date'] 							= $row['raid_date'] ;
			$a_return[$_id]['icon'] 							= $icon ;
			$a_return[$_id]['count_total_benoetigt'] 			= $count_total;
			$a_return[$_id]['count_signin_angemeldet'] 			= $count_signin;
			$a_return[$_id]['count_signedout_abgemeldet'] 		= $count_signedout ;
			$a_return[$_id]['count_affirmed_bestaetigt'] 		= $count_affirmed;
			$a_return[$_id]['count_fehlende_anmeldungen'] 		= $diffgest ;
		}
		return $a_return ;

	} # end function




	/**
	 *  Gibt die letzten Items zurück
  	 * ["name"]   - Itemname
  	 * ["looter"] - Käufer des Items
  	 * ["value"]  - DKP für die das Item verkauft wurde
	 *
	 * @param integer $limit limit der Anzeige
	 * @return Array or 0
	 */
	function get_last_items($limit=7,$raidID=-1)
	{

		$sql = "SELECT i.*, r.raid_name, r.raid_date, r.raid_note
		        FROM ".ITEMS_TABLE." i INNER JOIN ".RAIDS_TABLE." r
		        ON i.raid_id = r.raid_id ";

		if ($raidID > -1) {
			$sql.=" WHERE i.raid_id = ".$raidID;
		}

	    if ($limit > -1) {
	    	$sql.=" ORDER BY i.item_date DESC LIMIT ".$limit;
	    }

		$result = $this->br_db->query($sql);
		$items_data = array ();
		if($result)
		{
			while($row = $this->br_db->fetch_record($result))
			{
				$i++;
				$items_data[$i]['name'] = $row['item_name'] ;
				$items_data[$i]['id'] = $row['item_id'] ;
				$items_data[$i]['looter'] = $row['item_buyer'] ;
				$items_data[$i]['value'] = round($row['item_value']) ;
			}
			return  $items_data ;
		}
		return 0;
	} # end function get_last_items


	/**
	 *  Gibt die letzten Items eines Members zurück
  	 * ["name"]   - Itemname
  	 * ["value"]  - DKP für die das Item verkauft wurde
	 *
	 * @param integer $limit limit der Anzeige
	 * @return Array or 0
	 */
	function get_last_member_items($playername, $limit)
	{

		$sql = "SELECT i.*, r.raid_name, r.raid_date, r.raid_note
		        FROM ".ITEMS_TABLE." i INNER JOIN ".RAIDS_TABLE." r
		        ON i.raid_id = r.raid_id
		     	WHERE item_buyer = '".$playername."'
		    	LIMIT ".$limit;
		$result = $this->br_db->query($sql);
		$items_data = array ();
		if($result)
		{
			while($row = $this->br_db->fetch_record($result))
			{
				$i++;
				$items_data[$i]['name'] = $row['item_name'] ;
				$items_data[$i]['value'] = round($row['item_value']) ;
			}
			return  $items_data ;
		}
		return 0;
	} # end function get_last_items


  	/**
	 * Gibt die letzten Raids zurück
	 * ["raid_name"] - Name des Events
	 * ["raid_date"] - Datum
	 * ["raid_note"] - Raidnotiz mit z.b. Bosskills
	 * ["raid_icon"] - Icon - Die Icons im Format 64x64 liegen in "<img src=/eqdkp/games/gamename/events/"..">" ;
  	 *
  	 * @param Integer $limit
  	 * @return Array or 0
  	 */
  	function get_last_Group_Raids($limit=5)
	{
		$this->br_db = $this->br_db;
		$sql = 	"SELECT raid_id, raid_name, raid_date, raid_note
		         FROM ".RAIDS_TABLE ;

		$sql .= " ORDER BY raid_date DESC
		   		  LIMIT ".$limit ;

		$result = $this->br_db->query($sql);
		$items_data = array ();
		if($result)
		{
			while($row = $this->br_db->fetch_record($result))
			{
				$sql = "SELECT event_icon from ".EVENTS_TABLE." WHERE event_name='".$row['raid_name']."'" ;
				$eventicon = $this->br_db->query_first($sql);
				$i++;
				$raid_data[$i]['raid_id'] = $row['raid_id'] ;
				$raid_data[$i]['raid_name'] = $row['raid_name'] ;
				$raid_data[$i]['raid_date'] = $row['raid_date'] ;
				$raid_data[$i]['raid_note'] = $row['raid_note'] ;
				$raid_data[$i]['raid_icon'] = $eventicon ;

			}
			return  $raid_data ;
		}
		return 0;
	} # end function get_last_Raids

  	/**
	 * Gibt die letzten Raids eines Members zurück
	 * ["raid_name"] - Name des Events
	 * ["raid_date"] - Datum
	 * ["raid_note"] - Raidnotiz mit z.b. Bosskills
	 * ["raid_icon"] - Icon - Die Icons im Format 64x64
  	 *
  	 * @param String $membername
  	 * @param Integer $limit
  	 * @return Array or 0
  	 */
  	function get_last_Member_Raids($membername , $limit)
	{
		$this->br_db = $this->br_db;

		$sql = 'SELECT r.raid_id, r.raid_name, r.raid_date, r.raid_note, r.raid_value
	            FROM ' . RAIDS_TABLE . ' r, ' . RAID_ATTENDEES_TABLE . " ra
	            WHERE (ra.raid_id = r.raid_id)
	            AND (ra.member_name='" . $membername . "')
	            ORDER BY r.raid_date DESC
	            LIMIT " .  $limit ;

		$result = $this->br_db->query($sql);
		$raid_data = array ();
		if($result)
		{
			while($row = $this->br_db->fetch_record($result))
			{
				$sql = "SELECT event_icon from ".EVENTS_TABLE." WHERE event_name='".$row['raid_name'];
				$eventicon = $this->br_db->query_first($sql);
				$i++;
				$raid_data[$i]['raid_name'] = $row['raid_name'] ;
				$raid_data[$i]['raid_date'] = $row['raid_date'] ;
				$raid_data[$i]['raid_note'] = $row['raid_note'] ;
				$raid_data[$i]['raid_icon'] = $eventicon ;
			}
			return  $raid_data ;
		}
		return 0;
	} # end function get_last_Raids


  /**
   *  Gibt ein Array mit DKP Informationen zurück
   * ["allraids"] - Anzahl der Raids
   * ["total_points"] - Punkte gesamt
   * ["total_items"]  - Items gesamt
   * ["total_member"] - Members gesamt
   *
   * @return Array or 0
   */
  function get_GroupDKPInfo()
	{
		$data = array() ;

		// Get total Raids
		$sql ="select count(raid_id) as alle from ".RAIDS_TABLE;
		$data['allraids'] = $this->br_db->query_first($sql);

		$sql = "SELECT member_earned,member_spent,member_adjustment FROM ".MEMBERS_TABLE;
		$member_results = $this->br_db->query($sql) ;
		while($row = $this->br_db->fetch_record($member_results))
		{
			$player_dkps = ($row['member_earned'] - $row['member_spent']) + $row['member_adjustment'];
			$total_points+=$player_dkps;
		}
		$data['total_points'] = $total_points;

		// Get total items
		$sql= "SELECT item_id FROM ".ITEMS_TABLE;
		$data['total_items'] = $this->br_db->num_rows($this->br_db->query($sql));

		// Get total members
		$sql = "SELECT member_id FROM ".MEMBERS_TABLE;
		$data['total_member'] = $this->br_db->num_rows($this->br_db->query($sql));

	 return $data;
	}


  /**
   *  Gibt ein Array mit Informationen zu dem Member zurück
   *
   * ['current_dkp'] = aktuelle DKP
   * ['count_raids'] = teilgenommene Raids
   * ['total_items'] = anzahl der gekauften Items
   *
   * @param String $member_name (Name des Members)
   * @return Array or 0
   */
  function get_memberInfo($member_name)
	{	$data = array() ;

		//Member DKP
		$sql = "SELECT member_earned + member_adjustment - member_spent as dkp
				FROM ".MEMBERS_TABLE."
				WHERE member_name = '".$member_name."'";
		$data['current_dkp'] = $this->br_db->query_first($sql);

		// Raid Count
		$sql = 'SELECT count(r.raid_id)
	            FROM ' . RAIDS_TABLE . ' r, ' . RAID_ATTENDEES_TABLE . " ra
	            WHERE (ra.raid_id = r.raid_id)
	            AND (ra.member_name='" . $member_name . "')";

		$data['count_raids'] = $this->br_db->query_first($sql);

		// Get total items
		$sql= "SELECT item_id
			   FROM ".ITEMS_TABLE."
			   WHERE item_buyer='".$member_name."'" ;

		$data['total_items'] = $this->br_db->num_rows($this->br_db->query($sql));

	 return $data;
	}

	/**
	 *  Gibt die KlassenID zurück, 0 wenn nichts gefunden wurde
	 *	Klassenname kann deutsch oder english sein
	 *
	 * @param string $classname
	 * @return integer
	 */
	function get_ClassID_from_ClassName($classname)
	{

	  	$sql = "SELECT class_id from ".CLASS_TABLE." WHERE class_name='".$this->translate_Classname_into_German($classname)."'" ;
	  	$classID = $this->br_db->query_first($sql);

	  	if(isset($classID))
	  	{return $classID;}

	  	return 0;
  	}


	/**
	 *  Gibt die RassenID zurück, 0 wenn nichts gefunden wurde.
	 *  Klassenname kann deutsch oder english sein
	 *
	 * @param String $racename
	 * @return Integer RaceID
	 */
	function get_RaceID_from_RaceName($racename)
	{

	  	$sql = "SELECT race_id from ".RACE_TABLE." WHERE race_name ='".$this->translate_RaceName_into_German($racename)."'" ;
	  	$raceID = $this->br_db->query_first($sql);

	  	if(isset($raceID))
	  	{return $raceID;}

	  	return 0;
  	}



  /**
   *  Prüft ob es den User schon gibt
   *  Wenn ID gefunden wurde = true
   *  liefert false, wenn der Username mit dieser Gruppennummer nicht in der DB gefunden wurde
   *
   * @param String $user_name (Name des Users)
   * @return Boolean
   */
  function check_is_user($user_name)
	{
		$user_name = ucwords($user_name);
		$sql = "SELECT user_id FROM " . USERS_TABLE ." WHERE username = '".$db->sql_escape($user_name)."'";
		$user_id = $this->br_db->query_first($sql);
		if (isset($user_id) ){
			return true ;}
		else{
			return false;}
	}# end function

  /**
   *  Prüft ob es den Member schon gibt
   *  Wenn ID gefunden wurde = true
   *  liefert false, wenn der Membernamen mit dieser Gruppennummer nicht in der DB gefunden wurde
   *
   * @param String $member_name (Name des Members)
   * @return Boolean
   */
  function check_is_member($member_name)
	{
	 	$member_name = ucwords($member_name);
		$sql = "SELECT member_id FROM " . MEMBERS_TABLE ." WHERE member_name = '".$member_name."'";
		$member_id = $this->br_db->query_first($sql);

		if(isset($member_id) )
		{
			return true ;}
		else{
			return false;}
	}


	/**
	 * gibt  deutschen Klassennamen aus
	 *
	 * @param String $classname
	 * @return String
	 */
	function translate_Classname_into_German($classname)
	{
		switch ($classname)
		{
			case "Druid"        : return "Druide";break;
			case "Warlock"      : return "Hexenmeister";break;
			case "Hunter"       : return "Jäger";break;
			case "Warrior"      : return "Krieger";break;
			case "Mage"         : return "Magier";break;
			case "Paladin"      : return "Paladin";break;
			case "Priest"       : return "Priester";break;
			case "Rogue"        : return "Schurke";break;
			case "Shaman"       : return "Schamane";break;
		 }
		 return $classname;
	}


	/**
	 * gibt  deutschen Rassennamen aus
	 *
	 * @param String $RaceName
	 * @return String
	 */
	function translate_RaceName_into_German($RaceName)
	{
		switch ($RaceName)
		{
			case "Gnome"        : return "Gnom";break;
			case "Human"      	: return "Mensch";break;
			case "Dwarf"        : return "Zwerg";break;
			case "Night Elf"    : return "Nachtelf";break;
			case "Troll"      	: return "Troll";break;
			case "Undead"       : return "Untoter";break;
			case "Orc"       	: return "Ork";break;
			case "Tauren"       : return "Taure";break;
			case "Draenei"      : return "Draenei";break;
			case "Blood Elf"    : return "Blutelf";break;
		 }
		 return $RaceName;
	}

	# Admin Funtions
	##################


  /**
   * Verarbeitet das sql Array
   * Wenn alle SQL Anweisungen erfolgreich waren, wird ein True zurück gegeben
   * Bei einer fehlerhaften Anweisung wir diese als Array zurück gegeben
   *
   * @param Array $sql
   * @return Boolean or Array
   */
  function sql_prozess($sql)
	{
		$table_prefix = $this->table_prefix;

    	$sql_count = count($sql);
		if($sql_count<0)
		{return;}

		$i = 0;
	  	while ( $i < $sql_count )
	  	{
				if (isset($sql[$i]) && $sql[$i] != "")
				{
					$sql[$i] = preg_replace('#eqdkp\_(\S+?)([\s\.,]|$)#', $table_prefix . '\\1\\2', $sql[$i]);

					if ( !($this->br_db->query($sql[$i]) )){
						$error_log[] = $sql[$i];}
	  		}
	  		$i++;
	  	}# end while
	  	unset($sql);

	  	if(is_array($error_log))
	  	{return $error_log;}
	  	else{return true;}
	}# end function


  /**
   * Legt eine neues Eqdkp an
   * Config Variablen und Plus Config Vars werden gesetzt
   *
   * @param String $grp_name (Gruppenname)
   * @param String $adminemail (eMail Addresse des Admins)
   * @return Boolean or Array
   */
  function new_eqdkp($grp_name,$adminemail)
	{

		$grp_name = ucwords($grp_name);

    $sql = array();
    ### default config vars für jede eqdkp gruppe
		$sql[] = "INSERT INTO eqdkp_config (config_name, config_value) VALUES ('default_lang' ,'german')";
		$sql[] = "INSERT INTO eqdkp_config (config_name, config_value) VALUES ('default_game' ,'WoW_german')";
		$sql[] = "INSERT INTO eqdkp_config (config_name, config_value) VALUES ('default_style' ,'2')";
		$sql[] = "INSERT INTO eqdkp_config (config_name, config_value) VALUES ('default_alimit' ,'100')";
		$sql[] = "INSERT INTO eqdkp_config (config_name, config_value) VALUES ('default_elimit' ,'100')";
		$sql[] = "INSERT INTO eqdkp_config (config_name, config_value) VALUES ('default_ilimit' ,'100')";
		$sql[] = "INSERT INTO eqdkp_config (config_name, config_value) VALUES ('default_nlimit' ,'10')";
		$sql[] = "INSERT INTO eqdkp_config (config_name, config_value) VALUES ('default_rlimit' ,'100')";
		$sql[] = "INSERT INTO eqdkp_config (config_name, config_value) VALUES ('guildtag', '')";
		$sql[] = "INSERT INTO eqdkp_config (config_name, config_value) VALUES ('parsetags', '')";
		$sql[] = "INSERT INTO eqdkp_config (config_name, config_value) VALUES ('dkp_name' ,'DKP')";
		$sql[] = "INSERT INTO eqdkp_config (config_name, config_value) VALUES ('hide_inactive' ,'0')";
		$sql[] = "INSERT INTO eqdkp_config (config_name, config_value) VALUES ('inactive_period', '99')";
		$sql[] = "INSERT INTO eqdkp_config (config_name, config_value) VALUES ('active_point_adj' ,'0.00')";
		$sql[] = "INSERT INTO eqdkp_config (config_name, config_value) VALUES ('inactive_point_adj' ,'0.00')";
		$sql[] = "INSERT INTO eqdkp_config (config_name, config_value) VALUES ('main_title', '".$grp_name."')";
		$sql[] = "INSERT INTO eqdkp_config (config_name, config_value) VALUES ('sub_title' ,'')";
		$sql[] = "INSERT INTO eqdkp_config (config_name, config_value) VALUES ('start_page', 'viewnews.php')";
		$sql[] = "INSERT INTO eqdkp_config (config_name, config_value) VALUES ('cookie_domain', '')";
		$sql[] = "INSERT INTO eqdkp_config (config_name, config_value) VALUES ('cookie_name', 'eqdkp')";
		$sql[] = "INSERT INTO eqdkp_config (config_name, config_value) VALUES ('cookie_path' ,'/')";
		$sql[] = "INSERT INTO eqdkp_config (config_name, config_value) VALUES ('session_length', '3600')";
		$sql[] = "INSERT INTO eqdkp_config (config_name, config_value) VALUES ('session_cleanup' ,'0')";
		$sql[] = "INSERT INTO eqdkp_config (config_name, config_value) VALUES ('session_last_cleanup', '0')";
		$sql[] = "INSERT INTO eqdkp_config (config_name, config_value) VALUES ('server_name', 'allvatar.com')";
		$sql[] = "INSERT INTO eqdkp_config (config_name, config_value) VALUES ('server_path', '/eqdkp/')";
		$sql[] = "INSERT INTO eqdkp_config (config_name, config_value) VALUES ('server_port' ,'80')";
		$sql[] = "INSERT INTO eqdkp_config (config_name, config_value) VALUES ('enable_gzip', '1')";
		$sql[] = "INSERT INTO eqdkp_config (config_name, config_value) VALUES ('admin_email' ,'".$adminemail."')";
		$sql[] = "INSERT INTO eqdkp_config (config_name, config_value) VALUES ('account_activation', '1')";
		$sql[] = "INSERT INTO eqdkp_config (config_name, config_value) VALUES ('eqdkp_start', UNIX_TIMESTAMP())";
		$sql[] = "INSERT INTO eqdkp_config (config_name, config_value) VALUES ('default_locale' ,'de_DE')";

		### Member ranks
		$sql[] = "INSERT INTO eqdkp_member_ranks (rank_name) VALUES ('-')";
		$sql[] = "INSERT INTO eqdkp_member_ranks (rank_name) VALUES ('Member')";

		### A = Admin / U = User
		$sql[] = "INSERT INTO eqdkp_auth_options (auth_id,auth_value, auth_default) VALUES ('1' , 'a_event_add','N')";
		$sql[] = "INSERT INTO eqdkp_auth_options (auth_id,auth_value, auth_default) VALUES ('2' , 'a_event_upd','N')";
		$sql[] = "INSERT INTO eqdkp_auth_options (auth_id,auth_value, auth_default) VALUES ('3' , 'a_event_del','N')";
		$sql[] = "INSERT INTO eqdkp_auth_options (auth_id,auth_value, auth_default) VALUES ('4' , 'a_groupadj_add','N')";
		$sql[] = "INSERT INTO eqdkp_auth_options (auth_id,auth_value, auth_default) VALUES ('5' , 'a_groupadj_upd','N')";
		$sql[] = "INSERT INTO eqdkp_auth_options (auth_id,auth_value, auth_default) VALUES ('6' , 'a_groupadj_del','N')";
		$sql[] = "INSERT INTO eqdkp_auth_options (auth_id,auth_value, auth_default) VALUES ('7' , 'a_indivadj_add','N')";
		$sql[] = "INSERT INTO eqdkp_auth_options (auth_id,auth_value, auth_default) VALUES ('8' , 'a_indivadj_upd','N')";
		$sql[] = "INSERT INTO eqdkp_auth_options (auth_id,auth_value, auth_default) VALUES ('9' , 'a_indivadj_del','N')";
		$sql[] = "INSERT INTO eqdkp_auth_options (auth_id,auth_value, auth_default) VALUES ('10' , 'a_item_add','N')";
		$sql[] = "INSERT INTO eqdkp_auth_options (auth_id,auth_value, auth_default) VALUES ('11' , 'a_item_upd','N')";
		$sql[] = "INSERT INTO eqdkp_auth_options (auth_id,auth_value, auth_default) VALUES ('12' , 'a_item_del','N')";
		$sql[] = "INSERT INTO eqdkp_auth_options (auth_id,auth_value, auth_default) VALUES ('13' , 'a_news_add','N')";
		$sql[] = "INSERT INTO eqdkp_auth_options (auth_id,auth_value, auth_default) VALUES ('14' , 'a_news_upd','N')";
		$sql[] = "INSERT INTO eqdkp_auth_options (auth_id,auth_value, auth_default) VALUES ('15' , 'a_news_del','N')";
		$sql[] = "INSERT INTO eqdkp_auth_options (auth_id,auth_value, auth_default) VALUES ('16' , 'a_raid_add','N')";
		$sql[] = "INSERT INTO eqdkp_auth_options (auth_id,auth_value, auth_default) VALUES ('17' , 'a_raid_upd','N')";
		$sql[] = "INSERT INTO eqdkp_auth_options (auth_id,auth_value, auth_default) VALUES ('18' , 'a_raid_del','N')";
		$sql[] = "INSERT INTO eqdkp_auth_options (auth_id,auth_value, auth_default) VALUES ('19' , 'a_turnin_add','N')";
		$sql[] = "INSERT INTO eqdkp_auth_options (auth_id,auth_value, auth_default) VALUES ('20' , 'a_config_man','N')";
		$sql[] = "INSERT INTO eqdkp_auth_options (auth_id,auth_value, auth_default) VALUES ('21' , 'a_members_man','N')";
		$sql[] = "INSERT INTO eqdkp_auth_options (auth_id,auth_value, auth_default) VALUES ('22' , 'a_users_man','N')";
		$sql[] = "INSERT INTO eqdkp_auth_options (auth_id,auth_value, auth_default) VALUES ('23' , 'a_logs_view','N')";
		$sql[] = "INSERT INTO eqdkp_auth_options (auth_id,auth_value, auth_default) VALUES ('24' , 'u_event_list','Y')";
		$sql[] = "INSERT INTO eqdkp_auth_options (auth_id,auth_value, auth_default) VALUES ('25' , 'u_event_view','Y')";
		$sql[] = "INSERT INTO eqdkp_auth_options (auth_id,auth_value, auth_default) VALUES ('26' , 'u_item_list','Y')";
		$sql[] = "INSERT INTO eqdkp_auth_options (auth_id,auth_value, auth_default) VALUES ('27' , 'u_item_view','Y')";
		$sql[] = "INSERT INTO eqdkp_auth_options (auth_id,auth_value, auth_default) VALUES ('28' , 'u_member_list','Y')";
		$sql[] = "INSERT INTO eqdkp_auth_options (auth_id,auth_value, auth_default) VALUES ('29' , 'u_member_view','Y')";
		$sql[] = "INSERT INTO eqdkp_auth_options (auth_id,auth_value, auth_default) VALUES ('30' , 'u_raid_list','Y')";
		$sql[] = "INSERT INTO eqdkp_auth_options (auth_id,auth_value, auth_default) VALUES ('31' , 'u_raid_view','Y')";
		$sql[] = "INSERT INTO eqdkp_auth_options (auth_id,auth_value, auth_default) VALUES ('32' , 'a_plugins_man','N')";
		$sql[] = "INSERT INTO eqdkp_auth_options (auth_id,auth_value, auth_default) VALUES ('33' , 'a_styles_man','N')";
		$sql[] = "INSERT INTO eqdkp_auth_options (auth_id,auth_value, auth_default) VALUES ('36' , 'a_lua_import','N')";

		### Plus Config
		$sql[] = "INSERT INTO eqdkp_plus_config (config_name,  config_value) VALUES ('pk_updatecheck', '0')";
		$sql[] = "INSERT INTO eqdkp_plus_config (config_name,  config_value) VALUES ('pk_windowtime' ,'10')";
		$sql[] = "INSERT INTO eqdkp_plus_config (config_name,  config_value) VALUES ('pk_multidkp', '0')";
		$sql[] = "INSERT INTO eqdkp_plus_config (config_name,  config_value) VALUES ('pk_rank' ,'0')";
		$sql[] = "INSERT INTO eqdkp_plus_config (config_name,  config_value) VALUES ('pk_level' ,'1')";
		$sql[] = "INSERT INTO eqdkp_plus_config (config_name,  config_value) VALUES ('pk_lastloot', '0')";
		$sql[] = "INSERT INTO eqdkp_plus_config (config_name,  config_value) VALUES ('pk_attendanceAll', '1')";
		$sql[] = "INSERT INTO eqdkp_plus_config (config_name,  config_value) VALUES ('pk_links', '0')";
		$sql[] = "INSERT INTO eqdkp_plus_config (config_name,  config_value) VALUES ('pk_bosscount', '0')";
		$sql[] = "INSERT INTO eqdkp_plus_config (config_name,  config_value) VALUES ('pk_itemstats' ,'1')";
		$sql[] = "INSERT INTO eqdkp_plus_config (config_name,  config_value) VALUES ('pk_is_icon_loc' , '')";
		$sql[] = "INSERT INTO eqdkp_plus_config (config_name,  config_value) VALUES ('pk_is_icon_ext' , '.png')";
		$sql[] = "INSERT INTO eqdkp_plus_config (config_name,  config_value) VALUES ('pk_attendance90' , '0')";
		$sql[] = "INSERT INTO eqdkp_plus_config (config_name,  config_value) VALUES ('pk_attendance60' , '0')";
		$sql[] = "INSERT INTO eqdkp_plus_config (config_name,  config_value) VALUES ('pk_attendance30' , '1')";
		$sql[] = "INSERT INTO eqdkp_plus_config (config_name,  config_value) VALUES ('pk_lastraid' , '0')";
	    $sql[] = "INSERT INTO eqdkp_plus_config (config_name, config_value) VALUES ('pk_class_color' , '1')";
	    $sql[] = "INSERT INTO eqdkp_plus_config (config_name, config_value) VALUES ('pk_leaderboard' , '1')";


		### Plus Links
		$sql[] = "INSERT INTO eqdkp_plus_links (link_url, link_name, link_window) VALUES ('http://www.allvatar.de','Allvatar Guilding Community', '1')";


		return $this->sql_prozess($sql);
	}


  /**
   *  Legt neuen Eqdkp User und dessen Rechte an
   *
   * @param Boolean $is_admin ($is_admin =1 {user bekommt admin rechte}, bei <>1 nur user rechte)
   * @param String $user_name (Name des Users)
   * @param String $user_pw (Password des Users muss md5('password') sein)
   * @param String $user_email (eMail Addresse des Users)
   * @return Boolean or Array
   */
  function new_user($is_admin, $user_name, $user_pw, $user_email)
	{
		if($this->check_is_user($user_name))
		{return false;}

		$maxid = $this->br_db->query_first('SELECT max(user_id) from '. USERS_TABLE) +1;
		$sql[] = "INSERT INTO eqdkp_users (user_id,  username, user_password, user_email ,user_style, user_lang, user_active)
								   VALUES (".$maxid.", '".$user_name."','".$user_pw."', '".$user_email."' ,2,'german','1')";

		# Admin user bekommt alle Rechte
		if($is_admin==1)
		{
      // alle Rechte des users auf Y setzen = Admin
	  	for($i=1 ; $i<36; $i++)
	  	{
            $sql[] = 'INSERT INTO ' . AUTH_USERS_TABLE . "
                       (user_id,  auth_id, auth_setting)
                       VALUES ('" . $maxid. "','" . $i . "','Y')";
      }
 		}
 		else # Userrechte. alle Rechte der Gruppe durchgehen und die Rechte anhand der defaults setzen
 		{
      // Insert their permissions into the table
      $_sql = 'SELECT auth_id, auth_default
              FROM ' . AUTH_OPTIONS_TABLE . '
              ORDER BY auth_id';
      $result = $this->br_db->query($_sql);
      while ( $row = $this->br_db->fetch_record($result) )
      {
          $sql[] = 'INSERT INTO ' . AUTH_USERS_TABLE . "
                     (user_id, auth_id, auth_setting)
                     VALUES ('" . $maxid . "','" . $row['auth_id'] . "','" . $row['auth_default'] . "')";
          $this->br_db->query($au_sql);
      }# end while
		}# end user rechte if
		return $this->sql_prozess($sql);
	} # end user function


   /**
   *  Gibt einem Eqdkp User alle Standardadmin Rechte
   *
   * @param Boolean $is_admin ($is_admin =1 {user bekommt admin rechte}, bei <>1 nur user rechte)
   * @param String $user_name (Name des Users)
   * @return Boolean or Array
   */
   function set_userAdmin($member_name)
	{
	  	$member_name = ucwords($member_name);
	  	$_sql = "SELECT member_id FROM " . MEMBERS_TABLE ." WHERE member_name = '".$member_name."'";
	  	$member_id = $this->br_db->query_first($_sql);


	  	if ($member_id >=1 )
	  	{
			$_sql = "SELECT user_id FROM " . MEMBER_USER_TABLE ." WHERE member_id = ".$member_id ;
			$user_id = $this->br_db->query_first($_sql);

			if ($user_id >= 1)
			{
				$sql[] = 'UPDATE ' . AUTH_USERS_TABLE . " SET auth_setting = 'Y' WHERE user_id = ".$user_id;
			}
	  	}


		return  $this->sql_prozess($sql);
	}



  /**
   * Legt neuen Eqdkp Member an
   *
   * @param String $member_name (Membername)
   * @param Integer $member_level (Member Level zwischen 0 und 70)
   * @param String $member_race (Rasse als Name)
   * @param String $member_class (Klasse als Name)
   * @return Boolean or Array
   */
  function new_member($member_name, $member_level, $member_race, $member_class)
	{

		if($this->check_is_member($member_name))
		{return false;}

		$member_classid = $this->get_ClassID_from_ClassName($member_class);
		$member_raceid = $this->get_RaceID_from_RaceName($member_race);

		#Suche Member RankID in Members Rank Table
		$_sql = "SELECT rank_id FROM " . MEMBER_RANKS_TABLE ." WHERE rank_name = 'Member'";
		$member_rankid = $this->br_db->query_first($_sql);

		#Member anlegen
		$sql[] = "INSERT INTO eqdkp_members (member_name, member_earned, member_spent, member_adjustment, member_firstraid, member_lastraid ,member_raidcount, member_level, member_race_id, member_class_id, member_rank_id)
				  VALUES
				  ('".$member_name."', 0,0,0,0,0,0,'".$member_level."','".$member_raceid."','".$member_classid."','".$member_rankid."')";
		return $this->sql_prozess($sql);
	}# end function


	/**
	 * Verknüpft einen Eqdkp Member mit einem User
	 * Wird z.b. für den Raidplaner gebraucht
	 *
	 * @param String $user_name (Name des Users)
	 * @param String $member_name (Name des Members)
	 * @return Boolean or Array
	 */
	function new_member2user($user_name,$member_name)
	{
		$user_name = ucwords($user_name);
		$_sql = "SELECT user_id FROM " . USERS_TABLE ." WHERE username = '".$db->sql_escape($user_name)."'";
		$user_id = $this->br_db->query_first($_sql);

		$member_name = ucwords($member_name);
		$_sql = "SELECT member_id FROM " . MEMBERS_TABLE ." WHERE member_name = '".$member_name."'";
		$member_id = $this->br_db->query_first($_sql);

		$sql[] = "INSERT INTO ".MEMBER_USER_TABLE." (`member_id` , `user_id`) VALUES ('".$member_id."', '".$user_id."');";
		return $this->sql_prozess($sql);
	}# end function

	# Admin Delete Functions
	#########################

  /**
   * Löscht einen Member und alles seine Daten aus der Eqdkp DB
   *
   * @param Integer $member_name
   * @param String $user_name
   * @return Boolean or Array
   */
  function delete_member($member_name)
	{
	    // Delete attendance
	    $sql[] = 'DELETE FROM ' . RAID_ATTENDEES_TABLE . "
	              WHERE member_name='" . $member_name . "'";

	    // Delete items
	    $sql[] = 'DELETE FROM ' . ITEMS_TABLE . "
	              WHERE item_buyer='" . $member_name . "'";

	    // Delete adjustments
	    $sql[] = 'DELETE FROM ' . ADJUSTMENTS_TABLE . "
	              WHERE member_name='" . $member_name . "'";

	    // Delete member
	    $sql[] = 'DELETE FROM ' . MEMBERS_TABLE . "
	              WHERE member_name='" . $member_name . "'";

	   	return $this->sql_prozess($sql);
	}


  /**
   * Löscht User und deren Rechte aus der Eqdkp DB.
   *
   * @param String $user_name
   * @return Boolean or Array
   */
  function delete_user($user_name)
	{
	    // Delete attendance
	    $sql[] = 'DELETE FROM ' . USERS_TABLE . "
	              WHERE username='" . $user_name . "'";

	  	$_sql = "SELECT user_id from ".USERS_TABLE." WHERE username='".$db->sql_escape($user_name)."'" ;
	  	$user_id = $this->br_db->query_first($_sql);
	  	if(isset($user_id))
	  	{
	    	$sql[] = 'DELETE FROM ' . AUTH_USERS_TABLE . "
	                  WHERE user_id=" . $user_id ;

	    }
	   	return $this->sql_prozess($sql);
	}


} # end class


?>
