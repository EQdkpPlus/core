<?php

/*
 * Name      = TeamSpeak Viewer
 * Version   = 2.0
 * Datum     = 01.02.2006
 * Datei     = TSV_Config.php
 * * * * * * * * * * * * * * * * * * */

class tss2info {

	var $TS_Version = "2.0";

	////// TeamSpeak Einstellungen ///////////////////////////
	var $sitetitle       = "titel ausgeben"; // SeitenTitle und Scriptversion
	var $serverAddress   = "000.000.000.000"; // Hier die TeamSpeak IP Adresse eintragen !!wichtig!! (Beispiel: 192.168.7.1)
	var $serverQueryPort = "51234"; // TeamSpeak QueryPort.. Schau in die server.ini von TeamSpeak (Standard 51234)
	var $serverUDPPort   = "50006"; // UDP Port für Teamspeak der auch hinter der IP Adresse genutzt wird (Standard 8767)
	var $serverPasswort  = "password"; // Serverpasswort das bei Serversettings eingestellt wird (wenn kein Passwort erteilt, dann leer lassen)

	////// Erweiterte Einstellungen //////////////////////////
	var $tabellenbreite    = "120"; // Mindestbreite der Teamspeaktabelle (die einbindung mit einem IFRAME sollte 20px mehr betragen)
	var $alternativer_nick = "Guest"; // Alternativer Gastname

	////// Aktivieren & Deaktivieren /////////////////////////
	// 1 = aktiviert
	// 0 = deaktiviert
	//var $TS_subchannel_ausgabe     = 0;   // Sollen die Subchannels angezeigt werden? FUNKTION NOCH NICHT INTEGRIERT!!!
	var $TS_channelflags_ausgabe   = 1;   // Sollen die Channelrechte angezeigt werden? (R,M,S,P etc.)
	var $TS_userstatus_ausgabe     = 1;   // Soll der Status des Players angezeigt werden? (U,R,SA etc.)
	var $TS_channel_anzeigen       = 1;   // Sollen die Channel angezeigt werden? (0 = nur Playerausgabe)
	var $TS_leerchannel_anzeigen   = 0;   // Sollen die leeren Channel angezeigt werden?
	var $TS_title_anzeigen         = 1;   // Soll der Title �ber den Channels sichtbar sein?
	var $TS_overlib_mouseover      = 0;   // Soll der Mouseover Effekt vorhanden sein?
	var $TS_refresh                = 1;   // Refreshen generell erlauben (inkl. Refreshlink)
	var $TS_autorefresh            = 1;   // Autorefresh erlauben oder nicht
	var $TS_autorefresh_zeit       = 120;  // Zeit in Sekunden angeben (Funktioniert nur, wenn autorefresh aktiviert wurde)
	var $TS_blendtrans             = 1;   // �berblendeffekt an oder aus?
	var $joinable				   = 0;

	// Debugmodus
	// (u.a. kann damit die Channelid angezeigt werden sollte sie gebraucht werden bei dem verstecken einzelner Channels)
	var $TS_debug_modus            = 0;   // Debugmodus zur Fehlersuche an oder aus? (es werden s�mtliche Variablen ausgegeben)
	var $TS_hide_channels          = array(); // Welche Channels sollen versteckt werden?
	// Beispiel: array(CHANNELID,CHANNELID,CHANNELID,CHANNELID)

	////// PHPKIT Einstellungen //////////////////////////////
	var $phpkit_config     = "../admin/config/config.php"; // Ort ab dem TeamSpeak Viewer Ordner wo die config.php des PHPKIT's liegt.
	var $phpkit_gast_nick  = "PHPKIT_GastNick"; // Name des Gastes der durch das PHPKIT connectet
	var $include_phpkit    = 0; // 1 = aktiv ; 0 = inaktiv

	//internal
	var $socket;

	// external
	var $serverStatus = "offline";
	var $playerList = array();
	var $channelList = array();
	var $rpath = '';

	// opens a connection to the teamspeak server
	function getSocket($host, $port, $errno, $errstr, $timeout)
	{
	  unset($socket);
	  $attempts = 1;
	  while($attempts <= 1 and !$socket) {
		$attempts++;
	    $socket = @fsockopen($host, $port, $errno, $errstr, $timeout);
	    $this->errno = $errno;
	    $this->errstr = $errstr;
	    if($socket and fread($socket, 4) == "[TS]") {
	      fgets($socket, 128);
	      return $socket;
		}
	  }// end while
	  return false;
	}// end function getSocket(...)

	// sends a query to the teamspeak server
	function sendQuery($socket, $query) {
	  fputs($socket, $query."\n");
	}

	// answer OK?
	function getOK($socket) {
	  $result = fread($socket, 2);
	  fgets($socket, 128);
	  return($result == "OK");
	}

	// closes the connection to the teamspeak server
	function closeSocket($socket) {
	  fputs($socket, "quit");
	  fclose($socket);
	}

	// retrieves the next argument in a tabulator-separated string (PHP scanf function bug workaround)
	function getNext($evalString) {
	  $pos = strpos($evalString, "\t");
	  if(is_integer($pos)) {
	    return substr($evalString, 0, $pos);
	  } else {
	    return $evalString;
	  }
	}

	// removes the first argument in a tabulator-separated string (PHP scanf function bug workaround)
	function chopNext($evalString) {
	  $pos = strpos($evalString, "\t");
	  if(is_integer($pos)) {
	    return substr($evalString, $pos + 1);
	  } else {
	    return "";
	  }
	}

	// strips the quotes around a string
	function stripQuotes($evalString) {
	  if(strpos($evalString, '"') == 0) $evalString = substr($evalString, 1, strlen($evalString) - 1);
	  if(strrpos($evalString, '"') == strlen($evalString) - 1) $evalString = substr($evalString, 0, strlen($evalString) - 1);
	  return htmlentities($evalString);
	}

	// returns the codec name
	function getVerboseCodec($codec) {
	  if($codec == 0) {
	    $codec = "CELP 5.1 Kbit";
	  } elseif($codec == 1) {
	    $codec = "CELP 6.3 Kbit";
	  } elseif($codec == 2) {
	    $codec = "GSM 14.8 Kbit";
	  } elseif($codec == 3) {
	    $codec = "GSM 16.4 Kbit";
	  } elseif($codec == 4) {
	    $codec = "CELP Windows 5.2 Kbit";
	  } elseif($codec == 5) {
	    $codec = "Speex 3.4 Kbit";
	  } elseif($codec == 6) {
	    $codec = "Speex 5.2 Kbit";
	  } elseif($codec == 7) {
	    $codec = "Speex 7.2 Kbit";
	  } elseif($codec == 8) {
	    $codec = "Speex 9.3 Kbit";
	  } elseif($codec == 9) {
	    $codec = "Speex 12.3 Kbit";
	  } elseif($codec == 10) {
	    $codec = "Speex 16.3 Kbit";
	  } elseif($codec == 11) {
	    $codec = "Speex 19.5 Kbit";
	  } elseif($codec == 12) {
	    $codec = "Speex 25.9 Kbit";
	  } else {
	    $codec = "unknown (".$codec.")";
	  }
	  return $codec;
	}

	function getInfo() {

		// establish connection to teamspeak server
		$this->socket = $this->getSocket($this->serverAddress, $this->serverQueryPort, $errno, $errstr, 0.3);
		if($this->socket == false) {
		  if($this->TS_debug_modus == 1) {
		    $htmlOutPut .= 'Server (Port '.$this->serverQueryPort.') momentan nicht erreichbar.';
		    die();
		  } else {
		    return;
		  }
		} else {
		  $this->serverStatus = "online";

		  // select the one and only running server on port 8767
		  $this->sendQuery($this->socket, "sel ".$this->serverUDPPort);

		  // retrieve answer "OK"
		  if(!$this->getOK($this->socket)) {
		    if($this->TS_debug_modus == 1) {
		      $htmlOutPut .= 'Server reagiert nicht. UDP ('.$this->serverUDPPort.') Port fehlerhaft oder gesperrt';
		      die();
		    } else {
		      return;
		    }
		  }

		  // retrieve player list
		  $this->sendQuery($this->socket,"pl");

		  // read player info
		  $this->playerList = array();
		  do {
		    $playerinfo = fscanf($this->socket, "%s %d %d %d %d %d %d %d %d %d %d %d %d %s %[^\t]");
		    list($playerid, $channelid, $receivedpackets, $receivedbytes, $sentpackets, $sentbytes, $paketlost, $pingtime, $totaltime, $idletime, $privileg, $userstatus, $attribute, $s, $playername) = $playerinfo;
		    if($playerid != "OK") {
		      $this->playerList[$playerid] = array(
		      "playerid" => $playerid,
		      "channelid" => $channelid,
		      "receivedpackets" => $receivedpackets,
		      "receivedbytes" => $receivedbytes,
		      "sentpackets" => $sentpackets,
		      "sentbytes" => $sentbytes,
		      "paketlost" => $paketlost / 100,
		      "pingtime" => $pingtime,
		      "totaltime" => $totaltime,
		      "idletime" => $idletime,
		      "privileg" => $privileg,
		      "userstatus" => $userstatus,
		      "attribute" => $attribute,
		      "s" => $this->stripQuotes($s),
		      "playername" => $this->stripQuotes($playername));
		    }// end if
		  } while($playerid != "OK");


		  // retrieve channel list
		  $this->sendQuery($this->socket,"cl");

		  // read channel info
		  $this->channelList = array();
		  do {
		    $channelinfo = "";
		    do {
		      $input = fread($this->socket, 1);
		      if($input != "\n" && $input != "\r") $channelinfo .= $input;
		    } while($input != "\n");
		    $channelid         = $this->getNext($channelinfo); $channelinfo = $this->chopNext($channelinfo);
		    $channelcodec      = $this->getNext($channelinfo); $channelinfo = $this->chopNext($channelinfo);
		    $channelparent     = $this->getNext($channelinfo); $channelinfo = $this->chopNext($channelinfo);
		    $channelorder      = $this->getNext($channelinfo); $channelinfo = $this->chopNext($channelinfo);
		    $channelmaxplayers = $this->getNext($channelinfo); $channelinfo = $this->chopNext($channelinfo);
		    $channelname       = $this->getNext($channelinfo); $channelinfo = $this->chopNext($channelinfo);
		    $channelflags      = $this->getNext($channelinfo); $channelinfo = $this->chopNext($channelinfo);
		    $channelpasswort   = $this->getNext($channelinfo); $channelinfo = $this->chopNext($channelinfo);
		    $channeltopic      = $this->getNext($channelinfo);

		    if($channelid != "OK") {

			  // determine number of players in channel
		      $playercount = 0;
		      foreach($this->playerList as $playerInfo) {
		        if($playerInfo['channelid'] == $channelid) $playercount++;
		      }// end foreach

		      $this->channelList[$channelid] = array(
		      "channelid"             => $channelid,
		      "channelcodec"          => $this->getVerboseCodec($channelcodec),
		      "channelparent"         => $channelparent,
		      "channelorder"          => $channelorder,
		      "channelmaxplayers"     => $channelmaxplayers,
		      "channelname"           => $this->stripQuotes($channelname),
		      "channelflags"          => $channelflags,
		      "channelpasswort"       => $channelpasswort,
		      "channeltopic"          => $this->stripQuotes($channeltopic),
		      "channelcurrentplayers" => $playercount);
		    }// end if
		  } while($channelid != "OK");

		  // close connection to teamspeak server
		  $this->closeSocket($this->socket);

		  }



	//////////////////////////////////////////////////////////


			  $tsv_username = $this->alternativer_nick;

				$tsv_array_1 = array(" ","-","(",")","[","]","{","}","&"); // Das wird gesucht..
				$tsv_array_2 = array("_","_","","","","","","",""); // ..und ersetzt mit diesem
				$tsv_count = count($tsv_array_1);
				for($x=0;$x<$tsv_count;$x++){
				  $tsv_username = trim(str_replace($tsv_array_1[$x],$tsv_array_2[$x],$tsv_username));
				}
				$tsv_username = trim($tsv_username);

				$counter = 0;
				$channelcounter = count($tss2info->channelList) - 1;

				unset($s1);
				unset($s2);
				unset($v);
				$s1 = array();
				$s2 = array();
				foreach($this->channelList as $v) $s1[] = $v['channelorder'];    // Sortierung nach Order
				foreach($this->channelList as $v) $s2[] = $v['channelname'];    // Wenn Order gleich Sortierung nach Name
				array_multisort($s1, SORT_ASC, $s2, SORT_ASC, $this->channelList); // ASC = auf-, DESC = absteigend

				$tss2info_channellist = $this->channelList;
				for($i=0;$i<count($tss2info_channellist);$i++) {
				  if(intval($tss2info_channellist[$i]['channelparent']) > 0 AND intval($tss2info_channellist[$i]['channelcurrentplayers']) > 0) {
				    $subchannels[$tss2info_channellist[$i]['channelparent']] = 1;
				  }
				}

				//---> ChannelList <---\\ Anfang
				foreach($this->channelList as $channelInfo) {

				  if($channelInfo['channelid'] != "id" AND !in_array($channelInfo['channelid'],$this->TS_hide_channels)) {

				    if($channelInfo['channelparent'] < "0") {

				      //---> Channelanzeigen <---\\ Anfang
				      if($this->TS_channel_anzeigen == 1 AND ($this->TS_leerchannel_anzeigen == 1 OR ($this->TS_leerchannel_anzeigen == 0 AND (trim($channelInfo['channelcurrentplayers']) > 0 OR (intval($subchannels[$channelInfo['channelid']]) == 1))))) {

				        //---> Mouseover <---\\ Anfang
				        $channel_mouseover1 = "Join als: ".$tsv_username." | Channelname: ".$channelInfo['channelname']." | Topic: ".$channelInfo['channeltopic']." | Maximale User: ".$channelInfo['channelmaxplayers']." | Derzeitige User: ".$channelInfo['channelcurrentplayers']." | Codec: ".$channelInfo['channelcodec']."";
				        $channel_mouseover2 = "Kein Joinen möglich | Channelname: ".$channelInfo['channelname']." | Topic: ".$channelInfo['channeltopic']." | Maximale User: ".$channelInfo['channelmaxplayers']." | Derzeitige User: ".$channelInfo['channelcurrentplayers']." | Codec: ".$channelInfo['channelcodec']."";
				        $channel_mouseover3 = "<b>Join als:</b> ".$tsv_username."<br><br><b>Channelname:</b><br>".$channelInfo['channelname']."<br><br><b>Topic:</b><br>".$channelInfo['channeltopic']."<br><br><b>Maximale User:</b> ".$channelInfo['channelmaxplayers']."<br><b>Derzeitige User:</b> ".$channelInfo['channelcurrentplayers']."<br><br><b>Codec:</b><br>".$channelInfo['channelcodec']."";
				        $channel_mouseover4 = "<b>Kein Joinen möglich</b><br><br><b>Channelname:</b><br>".$channelInfo['channelname']."<br><br><b>Topic:</b><br>".$channelInfo['channeltopic']."<br><br><b>Maximale User:</b> ".$channelInfo['channelmaxplayers']."<br><b>Derzeitige User:</b> ".$channelInfo['channelcurrentplayers']."<br><br><b>Codec:</b><br>".$channelInfo['channelcodec']."";
				        //---> Mouseover <---\\ Ende

				        if($channelInfo['channelpasswort'] == "0") {
				        	$channel_mouseover3 = "title=\"".$channel_mouseover1."\"";


				        	if ($this->joinable == 1){
								$channellink = "<a class=\"channellink\" href=\"teamspeak://".$this->serverAddress.":".$this->serverUDPPort."/?channel=".rawurlencode($channelInfo['channelname'])."?password=".$this->serverPasswort."?nickname=".rawurlencode($tsv_username)."\" ".$channel_mouseover3.">".$channelInfo['channelname']."</a>";
				        	} else {
				        		$channellink = "<span>".$channelInfo['channelname']."</span>";
				        	}


				        } else {
				          $channel_mouseover4 = "title=\"".$channel_mouseover2."\"";
				          $channellink = "<span ".$channel_mouseover4.">".$channelInfo['channelname']."</span>";
				        }



				        //---> Passwortabfrage <---\\ Anfang
				        //---> Passwortabfrage <---\\ Ende

				        //---> Channelflags <---\\ Anfang
				        if($this->TS_channelflags_ausgabe == 1) $channellink .= ' ('.$this->TS_channelflags($channelInfo['channelflags']).')';
				        //---> Channelflags <---\\ Ende

				        //---> Channel <---\\ Anfang
				        $ts_viewer_ausgabe .= '
				        <tr>
				          <td valign="top">
				            <table border="0" width="100%" cellpadding="0" cellspacing="0">
				              <tr nowrap>
				                <td class="channel" width="25" valign="top" nowrap>
				                	<img width="5" height="13" src="'.$this->rpath.'pluskernel/include/TeamSpeakViewer/images/blank.gif" border="0" alt="">
				                	<img src="'.$this->rpath.'pluskernel/include/TeamSpeakViewer/images/channel.gif" width="20" height="13" border="0" alt="">
				                </td>
				                <td class="channel" width="100%" valign="top" nowrap>&nbsp;'.$channellink.'</td>';

				        //---> Debug Modus <---\\ Anfang
				        if($this->TS_debug_modus == 1)
				        {
				          $ts_viewer_ausgabe .= "\n   <td class=\"player\" width=\"1500\" valign=\"top\" nowrap>&nbsp;&nbsp;<b>channelid:</b> ".$channelInfo['channelid']."&nbsp;&nbsp;<b>channelcodec:</b> ".$channelInfo['channelcodec']."&nbsp;&nbsp;<b>channelparent:</b> ".$channelInfo['channelparent']."&nbsp;&nbsp;<b>channelorder:</b> ".$channelInfo['channelorder']."&nbsp;&nbsp;<b>channelmaxplayers:</b> ".$channelInfo['channelmaxplayers']."&nbsp;&nbsp;<b>channelname:</b> ".$channelInfo['channelname']."&nbsp;&nbsp;<b>channelflags:</b> ".$channelInfo['channelflags']."&nbsp;&nbsp;<b>channelpasswort:</b> ".$channelInfo['channelpasswort']."&nbsp;&nbsp;<b>channeltopic:</b> ".$channelInfo['channeltopic']."&nbsp;&nbsp;<b>channelcurrentplayers:</b> ".$channelInfo['channelcurrentplayers']."</td>";
				        }
				        //---> Debug Modus <---\\ Ende

				        $ts_viewer_ausgabe .= '
				              </tr>
				            </table>
				          </td>
				        </tr>';
				        //---> Channel <---\\ Ende
				      }
				      //---> Channelanzeigen <---\\ Ende

				      $counter_player = 0; // Playercounter beginnen

				      //---> Player Sortierung <---\\ Anfang
				      unset($s1);
				      unset($s2);
				      unset($v);
				      $s1 = array();
				      $s2 = array();
				      foreach($this->playerList as $v) $s1[] = $v['userstatus'];    // Sortierung nach Order
				      foreach($this->playerList as $v) $s2[] = $v['playername'];    // Wenn Order gleich Sortierung nach Name
				      array_multisort($s1, SORT_DESC, $s2, SORT_ASC, $this->playerList); // ASC = auf-, DESC = absteigend
				      //---> Player Sortierung <---\\ Ende

				      //---> PlayerList <---\\ Anfang
				      foreach($this->playerList as $playerInfo) {
				        if($playerInfo['channelid'] == $channelInfo['channelid']) {
				          $playercounter1 = $counter_player+1;
				          $playercounter2 = $channelInfo['channelcurrentplayers'];
				          $player_mouse_over1 = "".$playerInfo['playername']." | Online seit: ".$this->TS_totaltime($playerInfo['totaltime'])." | Idle seit: ".$this->TS_idletime($playerInfo['idletime'])." | Ping: ".$playerInfo['pingtime']." ms";
				          $player_mouse_over2 = "<b>".$playerInfo['playername']."</b><br><br><b>Online seit:</b><br>".$this->TS_totaltime($playerInfo['totaltime'])."<br><br><b>Idle seit:</b><br>".$this->TS_idletime($playerInfo['idletime'])."<br><br><b>Ping:</b> ".$playerInfo['pingtime']." ms";
				          if($this->TS_overlib_mouseover == 1) $player_mouse_over = "style=\"cursor: help;\" onmouseover=\"return overlib('".str_replace("'","\'",$player_mouse_over2)."', WIDTH, 150);\" onmouseout=\"return nd();\"";
				          else $player_mouse_over = "title=\"".$player_mouse_over1."\"";

				          //---> Player <---\\ Anfang
				          $ts_viewer_ausgabe .= '
				        <tr>
				          <td>
				            <table border="0" width="100%" cellpadding="0" cellspacing="0">
				              <tr>';
				              unset($userstatus);
				              if($this->TS_userstatus_ausgabe == 1) $userstatus = ' ('.$this->TS_userstatus($playerInfo['userstatus']).$this->TS_privileg($playerInfo['privileg'],$playerInfo['attribute']).')';
				              if($this->TS_channel_anzeigen == 1) {
				              $ts_viewer_ausgabe .= '
				                <td width="40" nowrap>
				                	<img width="5" height="16" src="'.$this->rpath.'pluskernel/include/TeamSpeakViewer/images/blank.gif" border="0" alt=""><img src="'.$this->rpath.'pluskernel/include/TeamSpeakViewer/images/blank.gif" width="15" height="16" border="0" alt=""><img src="'.$this->rpath.'pluskernel/include/TeamSpeakViewer/images/'.$this->TS_attribute($playerInfo['attribute']).'" width="20" height="16" border="0" alt=""></td>';
				              } else {
				              $player_without_channel[] = $playerInfo;
				              $ts_viewer_ausgabe .= '
				                <td width="20" nowrap><img src="'.$this->rpath.'pluskernel/include/TeamSpeakViewer/images/'.$this->TS_attribute($playerInfo['attribute']).'" width="20" height="16" border="0" alt=""></td>';
				              }
				              $ts_viewer_ausgabe .= '
				                <td class="player" width="100%">&nbsp;<span '.$player_mouse_over.'>'.$playerInfo['playername'].$userstatus.'</span></td>
				              </tr>
				            </table>
				          </td>
				        </tr>';
				          //---> Player <---\\ Ende

				          $counter_player++; // Playercounter hochz�hlen

				        }
				      }
				      //---> PlayerList <---\\ Ende

				      //---> Subchannel Sortierung <---\\ Anfang
				      unset($s1);
				      unset($s2);
				      $s1 = array();
				      $s2 = array();
				      foreach($this->channelList as $v) $s1[] = $v['channelorder'];    // Sortierung nach Order
				      foreach($this->channelList as $v) $s2[] = $v['channelname'];    // Wenn Order gleich Sortierung nach Name
				      array_multisort($s1, SORT_ASC, $s2, SORT_ASC, $this->channelList); // ASC = auf-, DESC = absteigend
				      unset($v);
				      //---> Subchannel Sortierung <---\\ Ende

				      //---> SubchannelList <---\\ Anfang
				      foreach($this->channelList as $subchannelInfo) {
				        if($subchannelInfo['channelparent'] == $channelInfo['channelid'] AND !in_array($subchannelInfo['channelid'],$this->TS_hide_channels) AND ($this->TS_leerchannel_anzeigen == 1 OR ($this->TS_leerchannel_anzeigen == 0 AND trim($subchannelInfo['channelcurrentplayers']) > 0))) {
				          if($this->TS_channel_anzeigen == 1) {
				            $subchannel_mouseover1 = "Join als: ".$tsv_username." | Channelname: ".$subchannelInfo['channelname']." | Subchannel von: ".$channelInfo['channelname']." | Topic: ".$subchannelInfo['channeltopic']." | Maximale User: ".$subchannelInfo['channelmaxplayers']." | Derzeitige User: ".$subchannelInfo['channelcurrentplayers']." | Codec: ".$subchannelInfo['channelcodec']."";
				            $subchannel_mouseover2 = "Kein Joinen m�glich | Channelname: ".$subchannelInfo['channelname']." | Subchannel von: ".$channelInfo['channelname']." | Topic: ".$subchannelInfo['channeltopic']." | Maximale User: ".$subchannelInfo['channelmaxplayers']." | Derzeitige User: ".$subchannelInfo['channelcurrentplayers']." | Codec: ".$subchannelInfo['channelcodec']."";
				            $subchannel_mouseover3 = "<b>Join als:</b> ".$tsv_username."<br><br><b>Channelname:</b><br>".$subchannelInfo['channelname']."<br><b>Subchannel von:</b><br>".$channelInfo['channelname']."<br><br><b>Topic:</b><br>".$subchannelInfo['channeltopic']."<br><br><b>Maximale User:</b> ".$subchannelInfo['channelmaxplayers']."<br><b>Derzeitige User:</b> ".$subchannelInfo['channelcurrentplayers']."<br><br><b>Codec:</b><br>".$subchannelInfo['channelcodec']."";
				            $subchannel_mouseover4 = "<b>Kein Joinen m�glich</b><br><br><b>Channelname:</b><br>".$subchannelInfo['channelname']."<br><b>Subchannel von:</b><br>".$channelInfo['channelname']."<br><br><b>Topic:</b><br>".$subchannelInfo['channeltopic']."<br><br><b>Maximale User:</b> ".$subchannelInfo['channelmaxplayers']."<br><b>Derzeitige User:</b> ".$subchannelInfo['channelcurrentplayers']."<br><br><b>Codec:</b><br>".$subchannelInfo['channelcodec']."";


				            if($channelInfo['channelpasswort'] == "0") {
				              $subchannel_mouseover3 = "title=\"".$channel_mouseover1."\"";


				              if ($this->joinable == 1){
									$subchannellink = "<a class=\"channellink\" href=\"teamspeak://".$this->serverAddress.":".$this->serverUDPPort."/?channel=".rawurlencode($subchannelInfo['channelname'])."?password=".$this->serverPasswort."?nickname=".rawurlencode($tsv_username)."\" ".$subchannel_mouseover3.">".$subchannelInfo['channelname']."</a>";
				              } else {
				              		$subchannellink = "<span>".$subchannelInfo['channelname']."</span>";
				              }



				            } else {
				              $subchannel_mouseover4 = "title=\"".$channel_mouseover2."\"";
				              $subchannellink = "<span ".$subchannel_mouseover4.">".$subchannelInfo['channelname']."</span>";
				            }



				            //---> Channel <---\\ Anfang
				            $ts_viewer_ausgabe .= '
				        <tr>
				          <td valign="top">
				            <table border="0" width="100%" cellpadding="0" cellspacing="0">
				              <tr>
				                <td class="channel" width="40" valign="top" nowrap><img width="5" height="13" src="'.$this->rpath.'pluskernel/include/TeamSpeakViewer/images/blank.gif" border="0" alt=""><img width="15" height="13" src="'.$this->rpath.'pluskernel/include/TeamSpeakViewer/images/blank.gif" border="0" alt=""><img src="'.$this->rpath.'pluskernel/include/TeamSpeakViewer/images/channel.gif" width="20" height="13" border="0" alt=""></td>
				                <td class="channel" width="100%" valign="top" nowrap>&nbsp;'.$subchannellink.'</td>';
				            //---> Debug Modus <---\\ Anfang
				            if($this->TS_debug_modus == 1) {
				              $ts_viewer_ausgabe .= "\n                <td class=\"player\" width=\"1500\" valign=\"top\" nowrap>&nbsp;&nbsp;<b>channelid:</b> ".$subchannelInfo['channelid']."&nbsp;&nbsp;<b>channelcodec:</b> ".$subchannelInfo['channelcodec']."&nbsp;&nbsp;<b>channelparent:</b> ".$subchannelInfo['channelparent']."&nbsp;&nbsp;<b>channelorder:</b> ".$subchannelInfo['channelorder']."&nbsp;&nbsp;<b>channelmaxplayers:</b> ".$subchannelInfo['channelmaxplayers']."&nbsp;&nbsp;<b>channelname:</b> ".$subchannelInfo['channelname']."&nbsp;&nbsp;<b>channelflags:</b> ".$subchannelInfo['channelflags']."&nbsp;&nbsp;<b>channelpasswort:</b> ".$subchannelInfo['channelpasswort']."&nbsp;&nbsp;<b>channeltopic:</b> ".$subchannelInfo['channeltopic']."&nbsp;&nbsp;<b>channelcurrentplayers:</b> ".$subchannelInfo['channelcurrentplayers']."</td>";
				            }
				            //---> Debug Modus <---\\ Ende
				            $ts_viewer_ausgabe .= '
				              </tr>
				            </table>
				          </td>
				        </tr>';
				            //---> Channel <---\\ Ende
				          }
				          $counter_player = 0;

				          //---> Sortierung <---\\ Anfang
				          unset($s1);
				          unset($s2);
				          unset($v);
				          $s1 = array();
				          $s2 = array();
				          foreach($this->playerList as $v) $s1[] = $v['userstatus'];    // Sortierung nach Order
				          foreach($this->playerList as $v) $s2[] = $v['playername'];    // Wenn Order gleich Sortierung nach Name
				          array_multisort($s1, SORT_DESC, $s2, SORT_ASC, $this->playerList); // ASC = auf-, DESC = absteigend
				          //---> Sortierung <---\\ Ende

				          //---> SubPlayerList <---\\ Anfang
				          foreach($this->playerList as $playerInfo) {
				            if($playerInfo['channelid'] == $subchannelInfo['channelid'] && $subchannelInfo['channelparent'] == $channelInfo['channelid']) {
				              $playercounter1 = $counter_player+1;
				              $playercounter2 = $subchannelInfo['channelcurrentplayers'];
				              $player_mouse_over1 = "".$playerInfo['playername']." | Online seit: ".$this->TS_totaltime($playerInfo['totaltime'])." | Idle seit: ".$this->TS_idletime($playerInfo['idletime'])." | Ping: ".$playerInfo['pingtime']." ms";
				              $player_mouse_over2 = "<b>".$playerInfo['playername']."</b><br><br><b>Online seit:</b><br>".$this->TS_totaltime($playerInfo['totaltime'])."<br><br><b>Idle seit:</b><br>".$this->TS_idletime($playerInfo['idletime'])."<br><br><b>Ping:</b> ".$playerInfo['pingtime']." ms";
				              if($this->TS_overlib_mouseover == 1) $player_mouse_over = "style=\"cursor: help;\" onmouseover=\"return overlib('".str_replace("'","\'",$player_mouse_over2)."', WIDTH, 150);\" onmouseout=\"return nd();\"";
				              else $player_mouse_over = "title=\"".$player_mouse_over1."\"";
				              //---> SubPlayer <---\\ Anfang
				              $ts_viewer_ausgabe .= '
				        <tr>
				          <td>
				            <table border="0" width="100%" cellpadding="0" cellspacing="0">
				              <tr>';
				              unset($subuserstatus);
				              if($this->TS_userstatus_ausgabe == 1) $subuserstatus = ' ('.$this->TS_userstatus($playerInfo['userstatus']).$this->TS_privileg($playerInfo['privileg'],$playerInfo['attribute']).')';
				              if($this->TS_channel_anzeigen == 1) {
				              $ts_viewer_ausgabe .= '
				                <td class="player" width="55" nowrap><img width="5" height="16" src="'.$this->rpath.'pluskernel/include/TeamSpeakViewer/images/blank.gif" border="0" alt=""><img src="'.$this->rpath.'pluskernel/include/TeamSpeakViewer/images/blank.gif" width="15" height="16" border="0" alt=""><img src="'.$this->rpath.'pluskernel/include/TeamSpeakViewer/images/blank.gif" width="15" height="16" border="0" alt=""><img src="'.$this->rpath.'pluskernel/include/TeamSpeakViewer/images/'.$this->TS_attribute($playerInfo['attribute']).'" width="20" height="16" border="0" alt=""></td>';
				              } else {
				              $player_without_channel[] = $playerInfo;
				              $ts_viewer_ausgabe .= '
				                <td class="player" width="20" nowrap><img src="'.$this->rpath.'pluskernel/include/TeamSpeakViewer/images/'.$this->TS_attribute($playerInfo['attribute']).'" width="20" height="16" border="0" alt=""></td>';
				              }
				              $ts_viewer_ausgabe .= '
				                <td class="player" width="100%">&nbsp;<span '.$player_mouse_over.'>'.$playerInfo['playername'].$subuserstatus.'</span></td>
				              </tr>
				            </table>
				          </td>
				        </tr>';
				              //---> SubPlayer <---\\ Ende
				              $counter_player++; // Playercounter hochz�hlen
				            }
				          }
				          //---> SubPlayerList <---\\ Ende

				        }
				      }
				      //---> SubchannelList <---\\ Ende

				      $counter++; // Channelcounter hochz�hlen

				    }
				  }
				  $counter++; // Channelcounter hochz�hlen
				}
				//---> ChannelList <---\\ Ende




				if($counter == 0) {
				  $ts_viewer_ausgabe .= '
				        <tr>
				          <td>
				            <table border="0" width="100%" cellpadding="0" cellspacing="0">
				              <tr>
				                <td class="offline" width="110" align="center"><font class="heads"><b>Offline</b></font></td>
				              </tr>
				            </table>
				          </td>
				        </tr>';
				}

			if(is_array($player_without_channel)) {
			  unset($ts_viewer_ausgabe);

			  //---> Sortierung <---\\ Anfang
			  unset($s1);
			  unset($s2);
			  unset($v);
			  $s1 = array();
			  $s2 = array();
			  foreach($player_without_channel as $v) $s1[] = $v['userstatus'];    // Sortierung nach Order
			  foreach($player_without_channel as $v) $s2[] = $v['playername'];    // Wenn Order gleich Sortierung nach Name
			  array_multisort($s1, SORT_DESC, $s2, SORT_ASC, $player_without_channel); // ASC = auf-, DESC = absteigend
			  //---> Sortierung <---\\ Ende

			  //---> PlayerList <---\\ Anfang
			  foreach($player_without_channel as $playerInfo) {
			    $player_mouse_over1 = "".$playerInfo['playername']." | Online seit: ".$this->TS_totaltime($playerInfo['totaltime'])." | Idle seit: ".$this->TS_idletime($playerInfo['idletime'])." | Ping: ".$playerInfo['pingtime']." ms";
			    $player_mouse_over2 = "<b>".$playerInfo['playername']."</b><br><br><b>Online seit:</b><br>".$this->TS_totaltime($playerInfo['totaltime'])."<br><br><b>Idle seit:</b><br>".$this->TS_idletime($playerInfo['idletime'])."<br><br><b>Ping:</b> ".$playerInfo['pingtime']." ms";
			    if($this->TS_overlib_mouseover == 1) $player_mouse_over = "style=\"cursor: help;\" onmouseover=\"return overlib('".str_replace("'","\'",$player_mouse_over2)."', WIDTH, 150);\" onmouseout=\"return nd();\"";
			    else $player_mouse_over = "title=\"".$player_mouse_over1."\"";
			    unset($userstatus);
			    if($this->TS_userstatus_ausgabe == 1) $userstatus = ' ('.$this->TS_userstatus($playerInfo['userstatus']).$this->TS_privileg($playerInfo['privileg'],$playerInfo['attribute']).')';
			    $ts_viewer_ausgabe .= '
			        <tr>
			          <td>
			            <table border="0" width="100%" cellpadding="0" cellspacing="0">
			              <tr>
			                <td class="player" width="20" nowrap><img src="'.$this->rpath.'pluskernel/include/TeamSpeakViewer/images/'.$this->TS_attribute($playerInfo['attribute']).'" width="20" height="16" border="0" alt=""></td>
			                <td class="player" width="100%">&nbsp;<span '.$player_mouse_over.'>'.$playerInfo['playername'].$userstatus.'</span></td>
			              </tr>
			            </table>
			          </td>
			        </tr>';
			  }
			}


			if($this->TS_overlib_mouseover == 1) {
			  $htmlOutPut .= '
			<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>';
			}
			$htmlOutPut .= '
			<table border="0" width="100%" cellpadding="1" cellspacing="4">
			  <tr>
			    <td class="odd" align="left" valign="top" width="'.$this->tabellenbreite.'" nowrap>
			      <table border="0" width="100%" cellpadding="0" cellspacing="2">';

			if($this->TS_title_anzeigen == 1) {
			  $htmlOutPut .= '
			        <tr>
			          <td>
			            <table border="0" width="100%" cellpadding="0" cellspacing="0">
			              <tr>
			                <td class="teamspeak" width="33" nowrap><img src="'.$this->rpath.'pluskernel/include/TeamSpeakViewer/images/teamspeak.gif" width="33"height="18" border="0" alt=""></td><td class="teamspeak" width="100%">'.$this->sitetitle.'</td>
			              </tr>
			            </table>
			          </td>
			        </tr>';
			}
			$htmlOutPut .= ''.$ts_viewer_ausgabe.'
			      </table>
			    </td>
			  </tr>
			</table>';

			return $htmlOutPut;



	}

	  function TS_channelflags($channelflags) {
	    if(preg_match("/^(1|3|5|7|9|11|13)$/",$channelflags)) $TS_channelflags = "U"; // Unregistriert
	    if(preg_match("/^(0|2|4|6|8|10|12|14|16|18|24|26)$/",$channelflags)) $TS_channelflags .= "R"; // Registriert
	    if(preg_match("/^(2|3|6|7|10|11|14|15|18|26)$/",$channelflags)) $TS_channelflags .= "M"; // Moderiert
	    if(preg_match("/^(4|6|7|12|13|14)$/",$channelflags)) $TS_channelflags .= "P"; // Passwort
	    if(preg_match("/^(8|9|10|11|12|13|14|15|24|26)$/",$channelflags)) $TS_channelflags .= "S"; // Subchannels
	    if(preg_match("/^(16|18|24|26)$/",$channelflags)) $TS_channelflags .= "D"; // Default
	    //---> Variablen �bergabe
	    return $TS_channelflags;
	  }

	  function TS_attribute($attribute) {
	    if(preg_match("/^(0|4)$/",$attribute)) $TS_attribute = "player.gif"; // Player (gr�n)
	    if(preg_match("/^(1|5)$/",$attribute)) $TS_attribute = "commander.gif"; // ChannelComander (rot)
	    if(preg_match("/^(16|17|20|21)$/",$attribute)) $TS_attribute = "micro.gif"; // Micro aus
	    if(preg_match("/^(32|33|36|37|48|49|52|53)$/",$attribute)) $TS_attribute = "speakers.gif"; // Headset aus
	    if(preg_match("/^(8|9|12|13|24|25|28|29|40|41|42|44|45|56|57|60|61)$/",$attribute)) $TS_attribute = "away.gif"; // Abwesend
	    if(preg_match("/^(6|14|22|38|46|54|62)$/",$attribute)) $TS_attribute = "request.gif"; // Request Voice
	    if($attribute >= "64") $TS_attribute = "record.gif"; // Aufnehmen
	    return $TS_attribute;
	  }

	  function TS_userstatus($userstatus) {
	    if(preg_match("/^0$/",$userstatus)) $TS_userstatus = "U"; //
	    if(preg_match("/^4$/",$userstatus)) $TS_userstatus .= "R"; //
	    if(preg_match("/^5$/",$userstatus)) $TS_userstatus .= "R SA"; //
	    //---> Variablen �bergabe
	    return $TS_userstatus;
	  }

	  function TS_privileg($privileg, $attribute) {
	    if(preg_match("/^(1|3|5|7|9|11|13|15|17|19|21|23|25|27|29|31)$/",$privileg)) $TS_privileg = " CA"; // Channeladmin
	    if(preg_match("/^(8|9|10|11|12|13|14|15|24|25|26|27|28|29|30|31)$/",$privileg)) $TS_privileg .= " AO"; // AutoOperator
	    if(preg_match("/^(16|17|18|19|20|21|22|23|24|25|26|27|28|29|30|31)$/",$privileg)) $TS_privileg .= " AV"; // AutoVoice
	    if(preg_match("/^(2|3|6|7|10|11|14|15|18|19|22|23|26|27|30|31)$/",$privileg)) $TS_privileg .= " O"; // Operator
	    if(preg_match("/^(4|5|6|7|12|13|14|15|20|21|22|23|28|29|30|31)$/",$privileg)) $TS_privileg .= " V"; // Voice
	    //---> Privilegien Request Voice und Record hinzugef�gt
	    if(preg_match("/^(6|14|22|38|46|54|62)$/",$attribute)) $TS_privileg = " WV"; // RequestVoice
	    if($attribute >= "64") $TS_privileg .= " Rec"; // Record
	    //---> Variablen �bergabe
	    return $TS_privileg;
	  }

	  function TS_totaltime($totaltime) {
	    if($totaltime < 60 ) {
	      $playertotaltime = strftime("%S Sekunden", $totaltime);
	    } else {
	      if ($totaltime >= 3600 ) {
	        $playertotaltime = strftime("%H:%M:%S Stunden", $totaltime - 3600);
	      } else {
	        $playertotaltime = strftime("%M:%S Minuten", $totaltime);
	      }
	    }
	    return $playertotaltime;
	  }

	  function TS_idletime($idletime) {
	    if ($idletime < 60 ) {
	      $playeridletime = strftime("%S Sekunden", $idletime);
	    } else {
	      if ($idletime >= 3600 ) {
	        $playeridletime = strftime("%H:%M:%S Stunden", $idletime - 3600);
	      } else {
	        $playeridletime = strftime("%M:%S Minuten", $idletime);
	      }
	    }
	    return $playeridletime;
	  }




}

/*
	$tss2info = new tss2info;
		$tss2info->sitetitle = 'titel hier ausgeben';
		$tss2info->serverAddress = '81.169.177.137';
		$tss2info->serverQueryPort = '51234';
		$tss2info->serverUDPPort = '50006';
		$tss2info->serverPasswort = 'password';
	echo $tss2info->getInfo();
	*/


?>
