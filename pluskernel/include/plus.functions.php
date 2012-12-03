<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       May 7, 2007
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
 * return the english name
 *
 * @param string $class
 * @return string
 */
function renameClasstoenglish($class)
{
	global $pconvertion;
	// Helper Class to be compatible with old versions..
	return $pconvertion->classname($class);
}

/**
 * return the english name 
 *
 * @param string $class
 * @return string
 */
function renameRacetoenglish($class)
{
	global $eqdkp;
	$_return = $class ;

	if($eqdkp->config['default_game'] == 'WoW')
	{
		switch ($class)
		{
			case "Gnom"       	: $_return = "Gnome";break;
			case "Mensch" 		: $_return = "Human";break;
			case "Zwerg"        : $_return = "Dwarf";break;
			case "Nachtelf"     : $_return = "Night Elf";break;
			case "Troll"       	: $_return = "Troll";break;
			case "Untoter"      : $_return = "Undead";break;
			case "Ork"     		: $_return = "Orc";break;
			case "Taure"      	: $_return = "Tauren";break;
			case "Draenei"     	: $_return = "Draenei";break;
			case "Blutelf"     	: $_return = "Blood Elf";break;
		
			case "Гном"       	: $_return = "Gnome";break;
			case "Человек" 		: $_return = "Human";break;
			case "Дварф"        : $_return = "Dwarf";break;
			case "Ночной эльф"  : $_return = "Night Elf";break;
			case "Троль"       	: $_return = "Troll";break;
			case "Нежить"       : $_return = "Undead";break;
			case "Орк"     		: $_return = "Orc";break;
			case "Таурен"      	: $_return = "Tauren";break;
			case "Дреней"     	: $_return = "Draenei";break;
			case "Кровавый эльф": $_return = "Blood Elf";break;			
		 }
	}
	elseif ($eqdkp->config['default_game'] == 'LOTRO') {
		switch ($class)
		{
			case "Mensch"   		: $_return = "Human";break;
			case "Hobbit"   		: $_return = "Hobbit";break;
			case "Elb"   			: $_return = "Elf";break;
			case "Zwerg"   			: $_return = "Dwarf";break;

		}

	}
	return($_return) ;
}

// return Classname if Membername is given
function get_classNamebyMemberName($Mname)
{
	$_return = '' ;
    global $db, $eqdkp;
  static $c2n = array();
  if(empty($c2n)){
	  $sql = 'SELECT c.class_name AS class_name,
                   m.member_name AS member_name 
            FROM ( __members m
            LEFT JOIN __classes c
            ON m.member_class_id = c.class_id)';
            //WHERE m.member_name = '".$Mname."'";
    $result = $db->query($sql);
    while($row = $db->fetch_record($result)){
      $c2n[$row['member_name']] = $row['class_name'];
    }
  }
  $_return = $c2n[$Mname];
	return $_return ;
}

// return Rankname
function get_RankNamebyMemberName($Mname)
{
	$_return = '' ;
    global $db, $eqdkp;

	$sql = 'SELECT r.rank_name
            FROM ( ' . MEMBERS_TABLE . ' m
            LEFT JOIN ' . MEMBER_RANKS_TABLE . " r
            ON m.member_rank_id = r.rank_id)
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
		if (($conf_plus['pk_rank_icon'] == 1))
		{
			$rank_img = $eqdkp_root_path.'games/'.$eqdkp->config['default_game'].'/rank/'.strtolower($rank).'.gif';
		   	if(file_exists($rank_img))
		   	{
		   		return '<img src='.$rank_img.' alt="Rank='.$row['rank_name'].'">';
		   	}

		   	$rank_img = $eqdkp_root_path.'games/'.$eqdkp->config['default_game'].'/rank/'.strtolower($rank).'.jpg';
		   	if(file_exists($rank_img))
		   	{
		   		return '<img src='.$rank_img.' alt="Rank='.$row['rank_name'].'">';
		   	}

		   	$rank_img = $eqdkp_root_path.'games/'.$eqdkp->config['default_game'].'/rank/'.strtolower($rank).'.png';
		   	if(file_exists($rank_img))
		   	{
		   		return '<img src='.$rank_img.' alt="Rank='.$row['rank_name'].'">';
		   	}
		}
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
		if(strtolower(get_Gender($membername))=='female')
		{
	   		$gender = 'f';
	   	}
	   	
	   $allowed_formats = array('.gif','.png');
	   foreach ($allowed_formats as $value)
	   {
	   		$race_img = $eqdkp_root_path.'games/'.$eqdkp->config['default_game'].'/races/'.strtolower($raceid).$gender.$value;	   	
	   		
	   		if(file_exists($race_img))
	   		{
	   			return '<img src='.$race_img.'>';
	   		}else
	   		{
	   			$race_img = $eqdkp_root_path.'games/'.$eqdkp->config['default_game'].'/races/'.strtolower($raceid).$value;
	   			if(file_exists($race_img))
	   			{
	   				return '<img src='.$race_img.'>';
	   			}
	   		}	   		
	   }

  }
}

// Return only the class img icon
function get_ClassIcon($class="",$classID=-1, $big=false)
{
	global $eqdkp, $game_icons, $html,$eqdkp_root_path;
	$ret_val = '' ;
	
	$allowed_formats = array('.gif','.png');
	
	if($game_icons['class'])
	{
		foreach ($allowed_formats as $value)
	    {   		
	    	$class_img = $eqdkp_root_path.'games/'.$eqdkp->config['default_game'].'/class/'.$classID;	
	    	if ($big) {
	    		$class_img .= '_b';	
	    	}else {
	    		$size = " height=18 width=18";
	    	}
	    		    	
	    	$class_img = $class_img.$value;
	    	
	    	if(file_exists($class_img))
	   		{
	   			$ret_val = '<img src='.$class_img.' '.$size.' >';	   			
	   			return $ret_val;
	   		}else // noID
	   		{	    
	   			$class_img = $eqdkp_root_path.'games/'.$eqdkp->config['default_game'].'/class/'. str_replace(' ','',ucfirst(strtolower(renameClasstoenglish($class)))).$value;	
	   		   	if(file_exists($class_img))
	   			{
	   				$ret_val = '<img src='.$class_img.' height=18 width=18 >';
		   			return $ret_val;
		   		}
	   			
	   		}	
  		}
	}	
} // end function

/**
 * Gibt NUR das Klassenicon mit Link auf Listmembers?filter=class zurьck
 * uses in Listmembers.php
 *
 * @param string $class
 * @return string
 */
function get_classImgListmembers($class, $classID=-1)
{
	global $eqdkp,  $game_icons,$eqdkp_root_path ;
	$ret_val = '' ;

	if($game_icons['class'])
	{
		$img = get_ClassIcon($class,$classID) ;			
		$ret_val = "<a class=".get_classColorChecked($class)."  href='".$eqdkp->config['server_path']."listmembers.php?s=&filter=".$class."'>".$img."</a>";
	}	

	return($ret_val) ;
}


/**
 * Return Classicon + Class Text mit Link zur Listmembers?filter=class
 *
 * @param String $class Klassenname
 * @return String Classicon + Class Text mit Link zur Listmembers?filter=class
 */
function get_classNameImgListmembers($class,$classID=-1)
{
	global $eqdkp,$game_icons,$eqdkp_root_path;
	$_return = $class ;

	if($game_icons['class'])
	{
		$img = get_ClassIcon($class,$classID) ;			
		$_return = "<a class=".get_classColorChecked($class)."  href='".$eqdkp_root_path."listmembers.php?s=&filter=".$class."'>".$img."&nbsp;$class</a>";
	}else{
  	$_return = "<a class=".get_classColorChecked($class)."  href='".$eqdkp_root_path."listmembers.php?s=&filter=".$class."'>$class</a>";
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
function get_classNameImgViewmembers($membername,$clasname='',$clasID=-1)
{
	global $eqdkp, $game_icons,$eqdkp_root_path;

  //fix for non existent users (disenchanted)
  if($clasname == NULL && $clasID == NULL){
    return $membername;
  }

	if ($clasname=='') 
	{
		$clasname = get_classNamebyMemberName($membername);		
	}
	
	if($game_icons['class'])
	{
		$img= get_ClassIcon($clasname,$clasID);
		$_return = "<a class=".get_classColorChecked($clasname)." href='".$eqdkp_root_path."viewmember.php?s=&name=".$membername."'>".$img."&nbsp;".$membername."</a>";
	}else{
    $_return = "<a class=".get_classColorChecked($clasname)." href='".$eqdkp_root_path."viewmember.php?s=&name=".$membername."'>".$membername."</a>";
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
	global $eqdkp,$eqdkp_root_path;

	$class = get_classNamebyMemberName($name);
	$_return = $name ;

	if($eqdkp->config['default_game'] == 'WoW')
	{
		$_return = "<a class=".get_classColorChecked($class)."  href='".$eqdkp_root_path."viewmember.php?s=&name=".$name."'>".$name."</a>";
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
  static $en2ei = array();
	if($game_icons['event'])
	{
       if(empty($en2ei)){
     	   $sql = 'SELECT event_icon, event_name
         		   FROM __events'; 
         $result = $db->query($sql);
          while($row = $db->fetch_record($result)){
            $en2ei[$row['event_name']] = $row['event_icon'];
          }
       }
       $icon = $en2ei[$event];
       $img_path = $eqdkp_root_path.'games/'.$eqdkp->config['default_game'].'/events/' ; 
   	   if(file_exists($img_path.$icon))
	   {
	   		if($size <> 'org')
			{
				$_size = "height='".$size."' width='".$size."'";
			}

	   		return "<img ". $_size." src='".$img_path.$icon."'> " ;
	   }

	}
} #end function

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
	static $gender_member = array();

	$ret = '';
	if ($pm->check(PLUGIN_INSTALLED, 'charmanager') and isset($member))
	{		
		if (empty($gender_member)) 
		{
			$sql =' SELECT m.member_id, m.member_name, ma.gender
					FROM '.$table_prefix.'member_additions ma 
			   		INNER JOIN '.$table_prefix.'members m 
			   		ON ma.member_id = m.member_id';		
			
			$result = $db->query($sql);
			while($row = $db->fetch_record($result))
			{
	      		$gender_member[$row['member_name']] = $row['gender'];
	    	}
			
		}
	    	
		$ret = $gender_member[$member];
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
function get_wow_talent_spec($class, $skill1, $skill2, $skill3, $member, $last_update, $light_mode=false)
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

	 $img = $eqdkp_root_path."games/WoW/talents/".str_replace(' ','',strtolower($class)).$spec_number.".png" ;
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
					<tdcolspan=3><span class=credits>Armory ".$user->lang['updated'].": ".$last_update."</span></td>
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
	 
	 if ($light_mode) 
	 {
	 	$ret_val = array();
	 	$ret_val['icon'] = $html->ToolTip($tooltip,$icon) ;
	 	$ret_val['spec'] = $spec;
	 }

	 //und ab dafьr :D
	 return $ret_val ;
}

function get_class_color_code($class)
{
	global $eqdkp ;
	$_return = null;

	if($eqdkp->config['default_game'] == 'WoW')
	{		
		$class = trim(str_replace(' ','',strtolower(renameClasstoenglish($class))));
		switch ($class)
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
			case "deathknight"  : $_return = "#C41F3B";break;			
		 }
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

	global $conf_plus, $eqdkp, $html,$user,$pm,$eqdkp_root_path;

	$img = false;
	$class = renameClasstoenglish($class);

	if ($conf_plus['pk_servername'] && $conf_plus['pk_server_region'] && $charname && $class && strtolower($eqdkp->config['default_game'])=='wow')
	{
		$img  = "http://sig.allvatar.com/signatur/sig.php";
		$img .= "?n=".rawurlencode(utf8_encode($charname));
		$img .= "&r=".rawurlencode(utf8_encode($conf_plus['pk_servername']));
		$img .= "&x=".$conf_plus['pk_server_region'];
		$img .= "&c=". str_replace(" ",'',$class);

		//BossCounter
		$bl_file = $eqdkp_root_path."/plugins/bosssuite/mods/plus_get_sig_data.php" ;
		if ( ($pm->check(PLUGIN_INSTALLED, 'bosssuite')) && (file_exists($bl_file)) )
		{
			@include_once($bl_file);
			$data = @plus_get_sig_data();
		}

		if (isset($data))
		{
			$img .= "&k=".$data['kara']['zk'];
			$img .= "&g=".$data['gruuls']['zk'];
			$img .= "&m=".$data['maglair']['zk'];
			$img .= "&s=".$data['serpent']['zk'];
			$img .= "&e=".$data['eye']['zk'];
			$img .= "&h=".$data['hyjal']['zk'];
			$img .= "&b=".$data['temple']['zk'];
			$img .= "&z=".$data['za']['zk'];
			$img .= "&sw=".$data['sunwell']['zk'];
			
			$img .= "&nax10=".$data['naxx_10']['zk'];
			$img .= "&arch10=".$data['vault_of_archavon_10']['zk'];
			$img .= "&maly10=".$data['eye_of_eternity_10']['zk'];
			$img .= "&sart10=".$data['obsidian_sanctum_10']['zk'];
			
			$img .= "&nax25=".$data['naxx_25']['zk'];
			$img .= "&arch25=".$data['vault_of_archavon_25']['zk'];
			$img .= "&maly25=".$data['eye_of_eternity_25']['zk'];
			$img .= "&sart25=".$data['obsidian_sanctum_25']['zk'];		
			
		}

		if ($eqdkp->config['default_lang'] <> 'german')
		{
			$img .= "&lang=eng";
		}

		$url  = "http://www.allvatar.com/signatur/wow/index.php";
		$url .= (isUTF8($charname)) ? "?charname=".rawurlencode($charname) :  "?charname=".rawurlencode(utf8_encode($charname));
		$url .= (isUTF8($charname)) ? "&realm=".rawurlencode($conf_plus['pk_servername']) : "&realm=".rawurlencode(utf8_encode($conf_plus['pk_servername']));
		$url .= "&region=".$conf_plus['pk_server_region'];
		$url .= "&lan=".$eqdkp->config['default_lang'];
		$url = $url;

		$img = "<a href=".$url." target=_blank><img src='".$img."'></a>";
		$img = $html->ToolTip($user->lang['sig_conf'],$img);
	}
	return $img;

}

function create_shop_link($nick,$class_name, $race_nam, $gilde, $realm, $level )
{

	global $user ,$eqdkp_root_path, $eqdkp ;
	$gender = 'm';
	if (get_Gender($nick)=='Female') 
	{
		$gender = 'w';
	}

	$class_name = strtolower(renameClasstoenglish($class_name));
	$race_nam  = strtolower(renameRacetoenglish($race_nam));

	$motivid['warrior_troll'] = 6693;
	$motivid['warrior_night elf'] = 6695;
	$motivid['warrior_undead'] = 6694;
	$motivid['warrior_orc'] = 6691;
	$motivid['warrior_dwarf'] = 6688;
	$motivid['warrior_draenei'] = 6687;
	$motivid['warrior_tauren'] = 6692;
	$motivid['warrior_gnome'] = 6689;
	$motivid['warlock_gnome'] = 6684;
	$motivid['warlock_undead'] = 6686;
	$motivid['warlock_human'] = 6685;
	$motivid['warlock_blood elf'] = 6683;
	$motivid['warrior_human'] = 6690;
	$motivid['shaman_orc'] = 6680;
	$motivid['shaman_troll'] = 6682;
	$motivid['shaman_draenei'] = 6678;
	$motivid['shaman_tauren'] = 6681;
	$motivid['hunter_troll'] = 6653;
	$motivid['hunter_night elf'] = 6650;
	$motivid['hunter_dwarf'] = 6649;
	$motivid['hunter_orc'] = 6651;
	$motivid['hunter_blood elf'] = 6648;
	$motivid['hunter_tauren'] = 6652;
	$motivid['mage_human'] = 6657;
	$motivid['mage_draenei'] = 6655;
	$motivid['mage_blood elf'] = 6654;
	$motivid['mage_gnome'] = 6656;
	$motivid['mage_undead'] = 6659;
	$motivid['mage_troll'] = 6658;
	$motivid['priest_blood elf'] = 6663;
	$motivid['priest_human'] = 6666;
	$motivid['priest_draenei'] = 6664;
	$motivid['priest_dwarf'] = 6665;
	$motivid['priest_night elf'] = 6667;
	$motivid['priest_troll'] = 6668;
	$motivid['priest_undead'] = 6669;
	$motivid['paladin_dwarf'] = 6661;
	$motivid['paladin_blood elf'] = 6660;
	$motivid['paladin_human'] = 6662;
	$motivid['rogue_troll'] = 6676;
	$motivid['rogue_human'] = 6673;
	$motivid['rogue_night elf'] = 6674;
	$motivid['rogue_orc'] = 6675;
	$motivid['rogue_undead'] = 6677;
	$motivid['rogue_gnome'] = 6672;
	$motivid['rogue_blood elf'] = 6670;
	$motivid['rogue_dwarf'] = 6671;
	$motivid['druid_tauren'] = 6647;
	$motivid['druid_night elf'] = 6646;

	$motivid_fem['druid_night elf']=8074;
	$motivid_fem['druid_tauren']=8075;
	$motivid_fem['hunter_blood elf']=8076;
	$motivid_fem['hunter_dwarf']=8077;
	$motivid_fem['hunter_night elf']=848785;
	$motivid_fem['hunter_orc']=8079;
	$motivid_fem['hunter_tauren']=8080;
	$motivid_fem['hunter_troll']=8081;
	$motivid_fem['mage_blood elf']=8082;
	$motivid_fem['mage_draenei']=8083;
	$motivid_fem['mage_troll']=8084;
	$motivid_fem['mage_gnome']=8085;
	$motivid_fem['mage_human']=8086;
	$motivid_fem['mage_undead']=8087;
	$motivid_fem['paladin_blood elf']=8088;
	$motivid_fem['paladin_dwarf']=8089;
	$motivid_fem['paladin_human']=8090;
	$motivid_fem['priest_blood elf']=8091;
	$motivid_fem['priest_draenei']=8092;
	$motivid_fem['priest_dwarf']=8093;
	$motivid_fem['priest_human']=8094;
	$motivid_fem['priest_night elf']=8095;
	$motivid_fem['priest_troll']=8096;
	$motivid_fem['priest_undead']=8097;
	$motivid_fem['rouge_blood elf']=8098;
	$motivid_fem['rouge_troll']=8099;
	$motivid_fem['rouge_dwarf']=8100;
	$motivid_fem['rouge_gnome']=8101;
	$motivid_fem['rouge_human']=8102;
	$motivid_fem['rouge_night elf']=8103;
	$motivid_fem['rouge_orc']=8104;
	$motivid_fem['rouge_undead']=8105;
	$motivid_fem['shaman_draenei']=8106;
	$motivid_fem['shaman_orc']=8107;
	$motivid_fem['shaman_tauren']=8108;
	$motivid_fem['shaman_troll']=8109;
	$motivid_fem['warlock_blood elf']=8110;
	$motivid_fem['warlock_gnome']=8111;
	$motivid_fem['warlock_human']=8112;
	$motivid_fem['warlock_undead']=8113;
	$motivid_fem['warrior_dranei']=8114;
	$motivid_fem['warrior_troll']=8115;
	$motivid_fem['warrior_dwarf']=8116;
	$motivid_fem['warrior_gnome']=8117;
	$motivid_fem['warrior_human']=8118;
	$motivid_fem['warrior_night elf']=8119;
	$motivid_fem['warrior_orc']=8120;
	$motivid_fem['warrior_tauren']=8121;
	$motivid_fem['warrior_undead']=8122;


	 if ($gender == 'w')  $motivid = $motivid_fem[$class_name.'_'.$race_nam];
	 else $motivid = $motivid[$class_name.'_'.$race_nam];
	 
	 if (!strtolower($eqdkp->config['default_game'])=='wow' ) 
	 {
	 	$motivid = -1 ;
	 }

	$nick = (strlen($nick)>1) ? $nick : $user->lang['your_name'] ;
	if ($nick && $nick != '') $link_ext .= '&shrModText_char='.urlencode(strtoupper($nick));
	else  $link_ext .= '&shrModText_char=';

	$level = ($level > 0) ? $level : 70 ;
	$link_ext .= '&shrModText_level='.$level;

	$realm = (strlen($realm)>1) ? $realm : $user->lang['your_server'] ;
	if ($realm && $realm != '') $link_ext .= '&shrModText_server='.urlencode(strtoupper($realm));
	else $link_ext .= '&shrModText_server=';

	$gilde = (strlen($gilde)>1) ? $gilde : $user->lang['your_guild'] ;
	if ($gilde && $gilde != '') $link_ext .= '&shrModText_guild='.urlencode(strtoupper($gilde));
	else $link_ext .= '&shrModText_guild=';

	$motivid = ($motivid > 0) ? $motivid : 6662 ;
	$link_ext .= '&shrModMotive_outline='.$motivid;

	if (strlen($nick)<1)
	{
		$shoplink = 'http://shirt-druck.shirtinator.net/myShop/produkte/?SISID=63036';
	}else
	{
		$shoplink = 'http://shirt-druck.shirtinator.net/myShop/produkte/?SISID=63036'.$link_ext;
	}

	return $shoplink;

}

function createShirtBox($member)
{
	global $eqdkp_root_path,$user,$eqdkp ;

	if (($user->data['user_lang'] == 'german') or ($eqdkp->config['default_lang'] == 'german'))
	{
		$out = "<table>
					<tr>
						<td><a href='".$eqdkp_root_path."shop.php?id=".urlencode($member)."'> <img src=".$eqdkp_root_path."pluskernel/images/shirt.png> </a></td><td valign=top><a href='".$eqdkp_root_path."shop.php?id=".urlencode($member)."'> ".$user->lang['shirt_ad1']." </a></td>
					</tr>
				</table> ";

		return $out ;
	}
}

// join an Array copyed from walles RP class
function join_array($glue, $pieces, $dimension = 0)
{
	$rtn = array();
	foreach($pieces as $key => $value)
	{
		if(isset($value[$dimension]))
		{
			$rtn[] = $value;
		}
	}
	return join($glue, $rtn);
}

function get_classColorChecked($class)
{
	global $conf_plus;		

	if ($conf_plus['pk_class_color'] == 1) 
	{		
		return trim(str_replace(' ','',renameClasstoenglish($class)));
	}else
	{
		return "none" ;
	}
}


	/**
 * Returns <kbd>true</kbd> if the string or array of string is encoded in UTF8.
 *
 * Example of use. If you want to know if a file is saved in UTF8 format :
 * <code> $array = file('one file.txt');
 * $isUTF8 = isUTF8($array);
 * if (!$isUTF8) --> we need to apply utf8_encode() to be in UTF8
 * else --> we are in UTF8 :)
 * @param mixed A string, or an array from a file() function.
 * @return boolean
 */
function isUTF8($string)
{
	if (is_array($string))
	{
		$enc = implode('', $string);
		return @!((ord($enc[0]) != 239) && (ord($enc[1]) != 187) && (ord($enc[2]) != 191));
	}else
	{
		return (utf8_encode(utf8_decode($string)) == $string);
	}
}

function isStyleInstalled($styleID)
{
	global $db;

	$sql = "SELECT style_id from ".STYLES_TABLE." WHERE style_id=".$styleID;
	$found = $db->query_first($sql);
	if ($found=$styleID) {
		return true;
	}
}

function check_auth_admin($user_id=0)
{

	if ($user_id == 0) return false;

	global $db;

    $sql = "
	SELECT u.*, o.*
	FROM ".AUTH_USERS_TABLE." u
	LEFT JOIN ".AUTH_OPTIONS_TABLE." o ON u.auth_id = o.auth_id
	WHERE u.user_id='$user_id' AND u.auth_setting='Y' AND LEFT(o.auth_value,2) = 'a_'
	GROUP BY (u.auth_id);
    ";
    $db->query($sql);

    if ( $db->num_rows() > 0) return true;
    else return false;
}

function Raidcount()
{
	global $db ;
	$sql = "SELECT count(raid_id) from ".RAIDS_TABLE ;
	$count = $db->query_first($sql);

	if ($count > 1 )
	{
		return	true ;
	}
}

function validate()
{
	global $eqdkp_root_path ;
	
	$keyfile_dat = $eqdkp_root_path.'/key.dat' ;
	$keyfile_php = $eqdkp_root_path.'/key.php' ;
	$return = true;
	
	if(file_exists($keyfile_dat) ) 
	{
		$handle = @fopen($keyfile_dat,"r");
		$keystring = @fread($handle, filesize($keyfile_dat));
	}
	elseif (file_exists($keyfile_php) )
	{
		include_once($keyfile_php);
	}
				
	if (strlen($keystring) > 1) 
	{
		$keystring = @base64_decode($keystring) ;
		$keystring = @gzuncompress($keystring) ;
		$keystring = @unserialize($keystring);	
		$_data = $keystring ;
	}		

	if (is_array($_data)) 
	{
		$_info = " | Type:".$_data['type']." | User:".$_data['kndNr'];

		switch ($_data['type'])
		{
			case 0: $return = (substr(EQDKPPLUS_VERSION,0,3) > $_data['version_allowed']) ? true : false ;	 break;	 //check server & version - 10
			case 1: $return = false ; break;	 //>50
			case 2: $return = false ; break;	 //>100
			case 3: $return = false ; break;	 //>100
			case 4: $return = false ; break;	 //>dev
			case 5: $return = false ; break;	 //>beta
		}
	}		

	//$this->left = "<img src=".$eqdkp_root_path."/images/premium.png>";
	
	
	return $return;
}

// get MultiDKP Data from eqdkp_multidkp
function get_multi_pools(){
global $db;
  static $mp = array();
  if(empty($mp)){
    $sql = 'SELECT multidkp_id, multidkp_name FROM __multidkp';
    if ( !($multi_results = $db->query($sql)) ){
      message_die('Could not obtain MultiDKP information', '', __FILE__, __LINE__, $sql);
    }
    while ( $a_multi = $db->fetch_record($multi_results) ){
      $mp[] = $a_multi;
    }
  }
  return $mp;
}

//get event names for dkp pool id
function get_events_for_poolid($poolid){
global $db;
  static $e4p = array();
  $sql_events = 'SELECT  multidkp2event_multi_id, multidkp2event_eventname FROM __multidkp2event';
  if(empty($e4p)){
    if ( !($multi2event_results = $db->query($sql_events)) ){
    	message_die('Could not obtain MultiDKP -> Event information ', '', __FILE__, __LINE__, $sql_events);
    }
    while ( $a_multi = $db->fetch_record($multi2event_results) )
  	{ // gehe alle Events durch, die einem Konto zugewiesen wurden
      $e4p[$a_multi['multidkp2event_multi_id']][] = $a_multi;					
    }
  }
  return $e4p[$poolid];
}

?>
