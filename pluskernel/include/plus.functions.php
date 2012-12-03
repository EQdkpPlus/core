<?php
/******************************
 * EQDKP PLUGIN: PLUSkernel
 * (c) 2007 by EQDKP Plus Dev Team
 * file written by Stefan Knaak
 * http://www.eqdkp-plus.com
 * ------------------
 * plus.functions.php
 * Start: 2006
 * $Id$
 ******************************/

if ( !defined('EQDKP_INC') ){
    header('HTTP/1.0 404 Not Found');exit;
}

function isEQDKPPLUS()
{
	$iamplus = true;
	return $iamplus;
}

/**
 * Rundet je nach Einstellungen im Eqdkp Plus Admin Menu die DKP Werte
 *
 * @param float $value
 * @return float
 */
function runden($value){
	global $conf_plus, $eqdkp;

	$ret_val = $value;
	$precision = $conf_plus['pk_round_precision'];

	if (($precision < 0) or ($precision > 5) )
	{
		$precision = 2;
	}

	if ($conf_plus['pk_round_activate'] == "1")
	{
		$ret_val = round($value,$precision)	;
	}

	return $ret_val;
}

/**
 * return the english name if the game language is german
 *
 * @param string $class
 * @return string
 */
function renameClasstoenglish($class)
{
	global $eqdkp;
	$_return = $class ;

	if($eqdkp->config['default_game'] == 'WoW')
	{
		switch ($class)
		{
			case "Druide"       : $_return = "Druid";break;
			case "Hexenmeister" : $_return = "Warlock";break;
			case "Jäger"        : $_return = "Hunter";break;
			case "Krieger"      : $_return = "Warrior";break;
			case "Magier"       : $_return = "Mage";break;
			case "Paladin"      : $_return = "Paladin";break;
			case "Priester"     : $_return = "Priest";break;
			case "Schurke"      : $_return = "Rogue";break;
			case "Schamane"     : $_return = "Shaman";break;
		 }
	}
	elseif ($eqdkp->config['default_game'] == 'LOTRO') {
		switch ($class)
		{
			case "Schurke"   		: $_return = "Burglar";break;
			case "Hauptmann"        : $_return = "Captain";break;
			case "Waffenmeister"    : $_return = "Champion";break;
			case "Wächter"          : $_return = "Guardian";break;
			case "Jäger"            : $_return = "Hunter";break;
			case "Kundiger"         : $_return = "Lore-master";break;
			case "Barde"        	: $_return = "Minstrel";break;
		}

	}
	return($_return) ;
}

// return Classname if Membername is given
function get_classNamebyMemberName($Mname)
{
	$_return = '' ;
    global $db, $eqdkp;

	$sql = 'SELECT c.class_name
            FROM ( ' . MEMBERS_TABLE . ' m
            LEFT JOIN ' . CLASS_TABLE . " c
            ON m.member_class_id = c.class_id)
            WHERE m.member_name = '".$Mname."'";

    $_return = $db->query_first($sql);
	return $_return ;
}

// Return Class with link to Listmembers with Filter = class
function get_RankIcon($rank)
{
	global $eqdkp,$conf_plus, $game_icons,$eqdkp_root_path;
	$ret_val = $rank ;

	if($game_icons['rank'])
	{
	   $rank_img = $eqdkp_root_path.'games/'.$eqdkp->config['default_game'].'/rank/'.strtolower($rank).'.gif';
	   if(file_exists($rank_img) && ($conf_plus['pk_rank_icon'] == 1))
	   {$ret_val = '<img src='.$rank_img.' alt="Rank='.$row['rank_name'].'">';}
	}

	return $ret_val;
}

// Return Race with link to Listmembers with Filter = class
function get_RaceIcon($raceid, $membername="")
{
	global $eqdkp, $game_icons, $html,$eqdkp_root_path;
	$ret_val = '' ;

	if($game_icons['races'])
	{
	    $gender = '';
		if (strtolower(get_Gender($membername))=='female') {
	   	$gender = 'f';
	   }

	   $race_img = $eqdkp_root_path.'games/'.$eqdkp->config['default_game'].'/races/'.strtolower($raceid).$gender.'.gif';

	   if(file_exists($race_img))
	   {
	   	$ret_val = '<img src='.$race_img.'>';
	   }
  }

	return $ret_val;
}

// Return Class with link to Listmembers with Filter = class
function get_ClassIcon($class="")
{
	global $eqdkp, $game_icons, $html,$eqdkp_root_path;
	$ret_val = '' ;

	if($game_icons['class'])
	{
	   $class_img = $eqdkp_root_path.'games/'.$eqdkp->config['default_game'].'/class/'.ucfirst(renameClasstoenglish($class)).'.gif';

	   if(file_exists($class_img))
	   {
	   	$ret_val = '<img src='.$class_img.'>';
	   }
  }

	return $ret_val;
}

/**
 * Gibt NUR das Klassenicon mit Link auf Listmembers?filter=class zurück
 * uses in Listmembers.php
 *
 * @param string $class
 * @return string
 */
function get_classImgListmembers($class)
{
	global $eqdkp,  $game_icons,$eqdkp_root_path ;
	$ret_val = '' ;

	if($game_icons['class'])
	{
		$class_img = $eqdkp_root_path.'games/'.$eqdkp->config['default_game'].'/class/'.ucfirst(renameClasstoenglish($class)).'.gif';
		if(file_exists($class_img))
	   	{
			$img= "<img width=18 height=18 src='".$class_img."' alt='".$class."_icon'>";
			$ret_val = "<a class=".renameClasstoenglish($class)."  href='".$eqdkp->config['server_path']."listmembers.php?s=&filter=".$class."'>".$img."</a>";
	   	}
	}

	return($ret_val) ;
}

/**
 * Return Classicon + Class Text mit Link zur Listmembers?filter=class
 *
 * @param String $class Klassenname
 * @return String Classicon + Class Text mit Link zur Listmembers?filter=class
 */
function get_classNameImgListmembers($class)
{
	global $eqdkp,$game_icons,$eqdkp_root_path;
	$_return = $class ;

	if($game_icons['class'])
	{
		$class_img = $eqdkp_root_path.'games/'.$eqdkp->config['default_game'].'/class/'.ucfirst(renameClasstoenglish($class)).'.gif';
		if(file_exists($class_img))
	   	{
			$img= "<img width=18 height=18 src='".$class_img."' alt='".$class."_icon'>";
			$_return = "<a class=".renameClasstoenglish($class)."  href='".$eqdkp->config['server_path']."listmembers.php?s=&filter=".$class."'>".$img."&nbsp;$class</a>";
	   	}
	}

	return($_return) ;
}

/**
 * Return colored Name with link to viewmember
 * used in viewraid.php, viewitem.php
 *
 * @param string $name / Membername
 * @return string Classicon + Membername mit Link zum Member
 */
function get_classNameImgViewmembers($name)
{
	global $eqdkp, $game_icons,$eqdkp_root_path;

	$class = get_classNamebyMemberName($name);
	$_return = $name ;

	if($game_icons['class'])
	{
		$class_img = $eqdkp_root_path.'games/'.$eqdkp->config['default_game'].'/class/'.ucfirst(renameClasstoenglish($class)).'.gif';
		if(file_exists($class_img))
	   	{
			$img= "<img width=18 height=18 src='".$class_img."' alt='".$class."_icon'>";
			$_return = "<a class=".renameClasstoenglish($class)." href='".$eqdkp_root_path."viewmember.php?s=&name=".$name."'>".$img."&nbsp;".$name."</a>";
	   	}
	}
	return($_return) ;
}

/**
 * return only name in class colur with Link zo member
 *  z.b. viewnews.php, viewraid.php
 *
 * @param string $name / Membername
 * @return String / Membername in Klassenfarbe mit Link auf Member
 */
function get_coloredLinkedName($name)
{
	global $eqdkp;

	$class = get_classNamebyMemberName($name);
	$_return = $name ;

	if($eqdkp->config['default_game'] == 'WoW')
	{
		$_return = "<a class=".renameClasstoenglish($class)."  href='".$eqdkp_root_path."viewmember.php?s=&name=".$name."'>".$name."</a>";
	}else
	{
		$_return = "<a href='".$eqdkp_root_path."viewmember.php?s=&name=".$name."'>".$name."</a>";

	}

	return($_return) ;
}

/**
 * return the <img> code with the event icon
 * the output size can bei define with size
 * default is 16, but you can define up to 64
 *
 * @param string $event
 * @param integer $size
 * @return html string
 */
function getEventIcon($event, $size='16')
{
	global $db, $eqdkp, $game_icons,$eqdkp_root_path;

	$ret_val = '' ;

	if($game_icons['event'])
	{
	   $img_path = $eqdkp_root_path.'games/'.$eqdkp->config['default_game'].'/events/' ;
   	   $sql = 'SELECT event_icon
       		   FROM ' . EVENTS_TABLE . "
               WHERE event_name='" . $event . "'";
   	   $icon = $db->query_first($sql);

   	   if(file_exists($img_path.$icon))
	   {
			return "<img height='".$size."' width='".$size."'  src='".$img_path.$icon."'> " ;
	   }

	}
} #end function



/**
 * Return Renderimages of a given member.
 *
 * @param integer $class_id
 * @param integer $race_id
 * @param string $member_name
 * @return html string
 */
function get_RenderImages($class_id=-1, $race_id=-1, $member_name='')
{
	global $db, $eqdkp, $user, $conf_plus,$eqdkp_root_path;

	$ret_val = "" ;
	$img_folder = $eqdkp_root_path.'games/'.$eqdkp->config['default_game']."/3dmodel/" ;


	if ( (($race_id == -1) or ($class_id == -1)) and (strlen($member_name) >1) )
	{
		$sql = "SELECT member_class_id, member_race_id from ".MEMBERS_TABLE. " WHERE member_name ='".$member_name."'" ;
		$result = $db->query($sql);
		$row = $db->fetch_record($result);
		$race_id = $row['member_race_id'];
		$class_id = $row['member_class_id'];
	}


	$imgs = array();

	$gender = 'm';
	if (get_Gender($member_name)=='Female') {
		$gender = 'w';
	}

	$imgs[] = $img_folder.$class_id.$race_id.'4'.$gender.'.png' ; //T4
	$imgs[] = $img_folder.$class_id.$race_id.'5'.$gender.'.png' ; //T5
	$imgs[] = $img_folder.$class_id.$race_id.'6'.$gender.'.png' ; //T6

	foreach($imgs as $value)
	{
		 if(file_exists($value))
		 {
		 	$ret_val .= '<img src='.$value.'> &nbsp;&nbsp;' ;
		 }
	}

	return 	$ret_val ;

}# end function

/**
 * Return the gender of a given member
 * need the Plugin Charmanager installed
 *
 * @param string $member
 * @return string
 */
function get_Gender($member)
{
	global $table_prefix , $pm,$db;

	$ret = '';
	if ($pm->check(PLUGIN_INSTALLED, 'charmanager'))
	{
		$sql = 'SELECT member_id FROM '.$table_prefix.'members '.
				"WHERE member_name='".$member."'";
		$ID = $db->query_first($sql);

		$sql = 'SELECT gender FROM '.$table_prefix.'member_additions '.
				'WHERE member_id='.$ID;
		$ret = $db->query_first($sql);

	}
	return $ret ;
}


/**
 * Return the WoW Talent Text with an Tooltip
 * Stefan Knaak 08/07
 *
 * @param String $class (Class has to be in ucword(englisch))
 * @param Integer $skill1
 * @param Integer $skill2
 * @param Integer $skill3
 * @param String $member Membername
 * @param Date $last_update
 * @return String
 */
function get_wow_talent_spec($class, $skill1, $skill2, $skill3, $member, $last_update)
{
	global $user, $conf_plus, $html, $eqdkp,$game_icons;

	if(!$game_icons['3dmodel'])
	{return;}

	// return empty string if no skill is given
 	if ( ($skill1 == 0) and ($skill2 == 0) and ($skill3 == 0)  ) {
 		return "";
 	}

 	if (strtolower($class) == 'unknown') {
 		return "";
 	}

	 // set default return value
	 $ret_val =  $skill1 ."/". $skill2 ."/". $skill3 ;
	 $spec = -1 ;

	 //define the array to sort the skills
	 $a_skill = array('0'=>$skill1 , '1'=>$skill2, '2'=>$skill3);

	 //sort the Arry to get the highest skill
	 asort($a_skill);

	 //now we have an sorted array, sorted by the highest skill
	 //go through the array and get the highest number
	 foreach ( $a_skill as $key => $row)
	 {$spec_number = $key;}

	 //spec= skill in text get from the language vars
	 $spec =  $user->lang['talents'][$class][$spec_number] ;

	 // If no 41 Talent is given, i think its a hybrid
	 if ( ($skill1 < 40) and ($skill2 < 40) and ($skill3 < 40)  )
	 {$spec =	$user->lang['Hybrid'] ;}

	 //USA or Europe Server for the Amory Link
	 if($conf_plus['pk_server_region'] =="eu")
	 {
		$armoryurl = "http://armory.wow-europe.com";
	 }else{
		$armoryurl = "http://armory.worldofwarcraft.com";
	 }
	 $menulink = $armoryurl.'/character-talents.xml?r='.stripslashes(rawurlencode(utf8_encode($conf_plus['pk_servername']))).
	 			 '&n='.stripslashes(rawurlencode(utf8_encode($member)));

	 $img = $eqdkp_root_path."games/WoW/talents/".strtolower($class).$spec_number.".png" ;
 	 $icon = "<img src='".$img."' alt='talent_icon'>";

	 //define the Tooltip
	  $tooltip  = "<table>";
	  $tooltip .= "<tr>
	  					<td><span class=itemdesc>".$icon.$ret_val." - ".$spec."</span></td>
	  			  </tr>";
	  $tooltip .= "<tr>
	  			  <td>".
		  			  $user->lang['talents'][$class][0]." - ".
		  			  $user->lang['talents'][$class][1]." - ".
		  			  $user->lang['talents'][$class][2]."
	  				</td></tr>";
	$tooltip  .= "<tr>
					<tdcolspan=3><span class=credits>Amory ".$user->lang['updated'].": ".$last_update."</span></td>
				  </tr>";
	$tooltip  .= "</table>";

	 // define the return value with the link + tooltip if an spec was found
	 if ($spec <> -1)
	 {
	 	$ret_val = "<a href=".$menulink." target=_blank>".$icon.$html->ToolTip($tooltip,$spec)."</a>";
	 }
	 else {
	 	$ret_val = "<a href=".$menulink." target=_blank>".$ret_val."</a>";
	 }

	 //und ab dafür :D
	 return $ret_val ;
}

function get_class_color_code($class)
{
	$_return = null;
	switch (strtolower(renameClasstoenglish($class)))
	{
		case "druid"        : $_return = "#FF7C0A";break;
		case "warlock" 		: $_return = "#9382C9";break;
		case "hunter"       : $_return = "#AAD372";break;
		case "warrior"      : $_return = "#C69B6D";break;
		case "mage"         : $_return = "#68CCEF";break;
		case "paladin"      : $_return = "#F48CBA";break;
		case "priest"       : $_return = "#FFFFFF";break;
		case "rogue"        : $_return = "#FFF468";break;
		case "shaman"       : $_return = "#1a3caa";break;
	 }
	return $_return;
}

/**
 * var_dump array
 *
 * @param array $array
 */
function da_($array)
{
	echo "<pre>";
	var_dump($array);
	echo "</pre>";
}

/**
* Debug Function
* wenn inhalt ein array ist, wird da() aufgerufen
*
* @param mixed $content
* @return mixed
*/
function d( $content="-" )
{ // Note: the function is recursive

	if(is_array($content))
	{return da($content); }

	if (is_object($content)) {
		echo "<pre>";
		var_dump($content);
		echo "</pre>";
	}

	if (is_bool($content)) {
		if($content == true)
		{
			$content = "Bool - True";
		}
		else {
	   	   $content = "Bool - false";
		}
	}

	if (strlen($content) ==0) {
		$content = "String Lenght=0";
	}

echo "<table border=0>\n";
echo "<tr>\n";
echo "<td bgcolor='#0080C0'>";
echo "<B>" . $content . "</B>";
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";
}

 /**
  * Debug Function
  * gibt ein Array in Tabbelarischer Form aus.
  *
  * @param Array $TheArray
  * @return mixed
  */
 function da( $TheArray )
  { // Note: the function is recursive

  	if(!is_array($TheArray))
  	{return "no array";}

    echo "<table border=0>\n";

    $Keys = array_keys( $TheArray );
    foreach( $Keys as $OneKey )
    {
      echo "<tr>\n";

      echo "<td bgcolor='#727450'>";
      echo "<B>" . $OneKey . "</B>";
      echo "</td>\n";

      echo "<td bgcolor='#C4C2A6'>";
        if ( is_array($TheArray[$OneKey]) )
          da($TheArray[$OneKey]);
        else
          echo $TheArray[$OneKey];
      echo "</td>\n";

      echo "</tr>\n";
    }
    echo "</table>\n";
  }


 if ( !function_exists('htmlspecialchars_decode') )
{
    /**
     * PHP4 Workaround if not exist htmlspecialchars_decode function create it.
     *
     * @param string $text
     * @return string
     */
    function htmlspecialchars_decode($text)
    {
        return strtr($text, array_flip(get_html_translation_table(HTML_SPECIALCHARS)));
    }
}

function clear_root_path()
{
	global $eqdkp,$eqdkp_root_path;
	$ret_val = null ;

	$ret_val = $eqdkp->config['server_path'];
	$ret_val = str_replace('//','/',$ret_val);
	$ret_val = trim($ret_val);

	if ($ret_val[strlen($ret_val)-1] <> '/') {
		$ret_val .= '/';
	}

	return $ret_val;
}

function showAllvatarWoW_Signatur($charname, $class)
{

	global $conf_plus, $eqdkp;


	$img = false;
	$class = renameClasstoenglish($class);

	if ($conf_plus['pk_servername'] && $conf_plus['pk_server_region'] && $charname && $class && strtolower($eqdkp->config['default_game'])=='wow')
	{
		$url  = "http://sig.allvatar.com/signatur/sig.php";
		$url .= "?n=".$charname;
		$url .= "&r=".$conf_plus['pk_servername'];
		$url .= "&x=".$conf_plus['pk_server_region'];
		$url .= "&c=".$class;

		if ($eqdkp->config['default_lang'] <> 'german')
		{
			$url .= "&lang=eng";
		}


		$img = "<a href='http://www.allvatar.com/signatur/'><img src='".$url."'></a>";
	}
	return $img;

}

?>
