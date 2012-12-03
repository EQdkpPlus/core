<?php
/******************************
 * EQDKP PLUGIN: PLUSkernel
 * (c) 2007 by EQDKP Plus Dev Team
 * originally written by S.Wallmann
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
 * gibt immer den englischen Namen der Klasse zurück
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
			case "Unknown"      : $_return = "Default";break;
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
			case "Kundiger"         : $_return = "Lore-Master";break;
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
	global $eqdkp,$conf_plus, $game_icons;
	$ret_val = $rank ;

  // TODO: change it to in-folder ini file
	if($game_icons['rank'])
	{
	   $rank_img = './games/'.$eqdkp->config['default_game'].'/rank/'.strtolower($rank).'.gif';
	   if(file_exists($rank_img) && ($conf_plus['pk_rank_icon'] == 1))
	   {$ret_val = '<img src='.$rank_img.' alt="Rank='.$row['rank_name'].'">';}
	}

	return $ret_val;
}


// Return Class with link to Listmembers with Filter = class
function get_RaceIcon($raceid, $membername="")
{
	global $eqdkp, $game_icons, $html;
	$ret_val = '' ;

	if($game_icons['races'])
	{
	    $gender = '';
		if (strtolower($html->get_Gender($membername))=='female') {
	   	$gender = 'f';
	   }

	   $race_img = './games/'.$eqdkp->config['default_game'].'/races/'.strtolower($raceid).$gender.'.gif';
	   if(file_exists($race_img))
	   {
	   	$ret_val = '<img src='.$race_img.'>';
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

	global $eqdkp, $lotro_classes, $wow_classes;
	$_return = false ;

	if( $eqdkp->config['default_game'] == 'WoW' && array_search($class,$wow_classes)>0 )
	{
		$img= "<img width=18 height=18 src='".$eqdkp->config['server_path']."./games/".$eqdkp->config['default_game']."/class/".ucfirst(strtolower(renameClasstoenglish($class))).".gif' alt=''>";
		$_return = "<a class=".renameClasstoenglish($class)."  href='".$eqdkp->config['server_path']."listmembers.php?s=&filter=".$class."'>".$img."</a>";
	}
	elseif (($eqdkp->config['default_game'] == 'LOTRO')
			 && array_search(strtolower($class),$lotro_classes)>0)
	{
		$img= "<img width=18 height=18 src='".$eqdkp->config['server_path']."./games/".$eqdkp->config['default_game']."/class/".strtolower(renameClasstoenglish($class)).".gif' alt=''>";
		$_return = "<a href='".$eqdkp->config['server_path']."listmembers.php?s=&filter=".$class."'>".$img."</a>";
	}
	return($_return) ;
}

/**
 * Return Classicon + Class Text mit Link zur Listmembers?filter=class
 *
 * @param String $class Klassenname
 * @return String Classicon + Class Text mit Link zur Listmembers?filter=class
 */
function get_classNameImgListmembers($class)
{
	global $eqdkp, $lotro_classes, $wow_classes;
	$_return = $class ;

	if($eqdkp->config['default_game'] == 'WoW' && array_search(strtolower($class),$wow_classes)>0 )
	{
		$img= "<img width=18 height=18 src='".$eqdkp->config['server_path']."./games/".$eqdkp->config['default_game']."/class/".ucfirst(strtolower(renameClasstoenglish($class))).".gif' alt=''>";
		$_return = "<a class=".renameClasstoenglish($class)."  href='".$eqdkp->config['server_path']."listmembers.php?s=&filter=".$class."'>".$img."&nbsp;$class</a>";
	}
	elseif ($eqdkp->config['default_game'] == 'LOTRO' && array_search(strtolower($class),$lotro_classes)>0)
	{
		$img = "<img width=18 height=18 src='".$eqdkp->config['server_path']."./games/".$eqdkp->config['default_game']."/class/".strtolower(renameClasstoenglish($class)).".gif' alt=''>";
		$_return = "<a href='".$eqdkp->config['server_path']."listmembers.php?s=&filter=".$class."'>".$img."&nbsp;".$class."</a>";
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
	global $eqdkp, $lotro_classes;

	$class = get_classNamebyMemberName($name);
	$_return = $name ;

	if($eqdkp->config['default_game'] == 'WoW')
	{
		$img= "<img width=18 height=18 src='".$eqdkp->config['server_path']."./games/".$eqdkp->config['default_game']."/class/".ucfirst(strtolower(renameClasstoenglish($class))).".gif' alt=''>";
		$_return = "<a class=".renameClasstoenglish($class)." href='".$eqdkp->config['server_path']."viewmember.php?s=&name=".$name."'>".$img."&nbsp;".$name."</a>";
	}
	elseif (($eqdkp->config['default_game'] == 'LOTRO') && array_search(strtolower($class),$lotro_classes)>0)
	{
		$img= "<img width=18 height=18 src='".$eqdkp->config['server_path']."./games/".$eqdkp->config['default_game']."/class/".strtolower(renameClasstoenglish($class)).".gif' alt=''>";
		$_return = "<a href='".$eqdkp->config['server_path']."viewmember.php?s=&name=".$name."'>".$img."&nbsp;".$name."</a>";
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
		$_return = "<a class=".renameClasstoenglish($class)."  href='".$eqdkp->config['server_path']."viewmember.php?s=&name=".$name."'>".$name."</a>";
	}
	return($_return) ;
}

function getDKPInfo()
{
	global $eqdkp , $user , $tpl;

    $total_points = 0;
		$sqlabfrage ="select count(*) as alle from ".RAIDS_TABLE.";";
		$result = mysql_query("$sqlabfrage");
		$data = mysql_fetch_object($result);
		$allraids = $data->alle;

		$member_results = mysql_query("SELECT * FROM ".MEMBERS_TABLE.";") or die(mysql_error());
		while($row = mysql_fetch_array($member_results, MYSQL_ASSOC))
		{
			$player_dkps = ($row['member_earned'] - $row['member_spent']) + $row['member_adjustment'];
			$total_points+=$player_dkps;
		}

		$total_pointsset = $total_points;

		// Get total items
		$item_results = mysql_query("SELECT * FROM ".ITEMS_TABLE.";") or die(mysql_error());
		$total_items = mysql_num_rows($item_results);
		$setitems = $total_items ;

		// Get total players
		$member_results = mysql_query("SELECT * FROM ".MEMBERS_TABLE.";") or die(mysql_error());
		$total_players = mysql_num_rows($member_results);


		$DKPInfo = '
					<tr><th colspan=2 class="smalltitle" align="center">DKP Infos</th></tr>
					<tr><td class="row1">'.$user->lang['bosscount_raids'].'</td><td class="row1">'. $allraids. '</td></tr>
					<tr><td class="row2">'.$user->lang['bosscount_player'].'</td><td class="row2">'. $total_players. '</td></tr>
					<tr><td class="row1">'.$user->lang['bosscount_items'].'</td><td class="row1">'. $total_items. '</td></tr>
					';

		$tpl->assign_var('DKP_INFO',$DKPInfo);
}

if ( !function_exists('htmlspecialchars_decode') )
{
    function htmlspecialchars_decode($text)
    {
        return strtr($text, array_flip(get_html_translation_table(HTML_SPECIALCHARS)));
    }
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

 global $user, $conf_plus, $html, $eqdkp;


 // return empty string if no skill is given
 if ( ($skill1 == 0) and ($skill2 == 0) and ($skill3 == 0)  ) {
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

 $icon = $img= "<img src='".$eqdkp->config['server_path']."./games/WoW/talents/".strtolower($class).$spec_number.".png' alt=''>";

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







/*


elseif ($eqdkp->config['default_game'] == 'LOTRO') {
		switch ($class)
		{
			case "Burglar"      	: $_return = "";
			case "Captain"      	: $_return = "";
			case "Champion"     	: $_return = "";
			case "Guardian" 		: $_return = "";
			case "Hunter"       	: $_return = "";
			case "Lore-Master"  	: $_return = "";
			case "Minstrel"	    	: $_return = "";

			case "Schurke"   	    : $_return = "";
			case "Hauptmann"        : $_return = "";
			case "Waffenmeister"    : $_return = "";
			case "Wächter"          : $_return = "";
			case "Jäger"            : $_return = "";
			case "Kundiger"         : $_return = "";
			case "Barde"        	: $_return = "";
		}

	}

			case "Druide"       : $_return = "Druid";break;
			case "Hexenmeister" : $_return = "Warlock";break;
			case "Jäger"        : $_return = "Hunter";break;
			case "Krieger"      : $_return = "Warrior";break;
			case "Magier"       : $_return = "Mage";break;
			case "Paladin"      : $_return = "Paladin";break;
			case "Priester"     : $_return = "Priest";break;
			case "Schurke"      : $_return = "Rogue";break;
			case "Schamane"     : $_return = "Shaman";break;
			case "Unknown"      : $_return = "Default";break;

*/

?>
