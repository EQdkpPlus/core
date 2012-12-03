<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       01.07.2009
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

if ( !defined('EQDKP_INC') )
{
    header('HTTP/1.0 404 Not Found');
    exit;
}

if(!class_exists('wow')) {
  class wow
  {
  	private $this_game = 'wow';
  	private $types = array('classes', 'races', 'factions', 'filters', 'realmlist');  		// which information are stored?
	private $classes = array();																//
	private $races = array();																// for each type there must be the according var
	private $factions = array();															// and the according function: load_$type
	private $filters = array();																//
	private $realmlist = array();															//
	public  $objects = array('wow_modelviewer');											// eventually there are some objects (php-classes) in this game
    public  $langs = array('english', 'german', 'french', 'russian');						// in which languages do we have information?
    public  $icons = array('classes', 'classes_big', 'races', 'ranks', 'events', 'talents', '3dmodel');	// which icons do we have?
	public	$importers = array(
				'char_import'	=> 'u_import.php',				// filename of the character import
				'char_update'	=> 'u_import.php',				// filename of the character update, member_id (POST) is passed
				'char_mupdate'	=> 'a_import.php?step=1',		// filename of the "update all characters" aka mass update
				'guild_import'	=> 'a_guild_import.php',		// filename of the guild import
				'guild_imp_rsn'	=> true							// Guild import & Mass update requires server name
			);

	private $glang = array();
    private $lang_file = array();
    private $path = '';
	public  $lang = false;
	public  $version = '4.1';
	
	public $maxItemlevel = '277';

	public function __construct()
	{
		global $eqdkp_root_path;
		$this->path = $eqdkp_root_path.'games/'.$this->this_game.'/';
  	}

	/**
	 * Returns information about type in appropriate langs (if not default lang)
	 *
	 * @param string $type
	 * @param array $add_lang
	 * @return array
	 */
	public function get($type, $add_lang=array())
	{
		//valid type?
		if(!in_array($type, $this->types)) {
			return false;
		}
		if(!is_array($add_lang)) {
			$add_lang = array($add_lang);
		}
		$langs = (!empty($add_lang)) ? $add_lang : array($this->lang);
		//type already loaded?
		if(!$this->$type) {
			call_user_func_array(array($this, 'load_'.$type), array($langs));
		}
		return $this->$type;
	}

	/**
	 * Returns array of types as strings
	 *
	 * @return array
	 */
	public function get_types()
	{
		return $this->types;
	}

	/**
	 * deletes data holding variables
	 *
	 * @param string $type
	 * @param bool $lang_file
	 * @return bool
	 */
	public function flush($type, $lang_file=false)
	{
		//type is the language here
		if($lang_file) {
	  		unset($this->lang_file[$type]);
	  		return true;
		}
		if(!in_array($type, $this->types)) {
			return false;
		}
		if($this->$type) {
			unset($this->$type);
		}
        return true;
	}

	/**
	 * load an object
	 *
	 * @param string $classname
	 * @return object
	 */
	public function load_object($classname, $params=NULL)
	{
		$path = $this->path.'objects/'.$classname.'.class.php';
		if(file_exists($path)) {
			include_once($path);
			return new $classname($params);
		}
		return false;
	}

	/**
	 * Loads language data from file into class
	 *
	 * @param string $lang
	 */
	private function load_lang_file($lang)
	{
		if(!$this->lang_file[$lang]) {
			include($this->path.'language/'.$lang.'.php');
			$this->lang_file[$lang] = ${$lang.'_array'};
		}
	}

	/**
	 * Initialises Gamelanguage
	 *
	 * @param string $lang
	 */
	private function load_glang($lang)
	{
        $this->load_lang_file($lang);
        $this->glang[$lang] = $this->lang_file[$lang]['lang'];
	}

	/**
	 * Returns language var
	 *
	 * @param string $var
	 * @param string $lang
	 * @return string
	 */
	public function glang($var, $lang=false)
	{
		if(!$lang) {
			$lang = $this->lang;
		}
		if(!$this->glang[$lang]) {
			$this->load_glang($lang);
		}
		if(!$this->glang[$lang]) {
			return $this->glang($var);
		}
		return $this->glang[$lang][$var];
	}

	/**
	 * Returns Information to change the game
	 *
	 * @param bool $install
	 * @return array
	 */
	public function get_OnChangeInfos($install=false)
	{
		global $itt;

	    //classcolors
	    $info['class_color'] = array(
          1 => '#C41F3B',
          2 => '#FF7C0A',
          3 => '#AAD372',
          4 => '#68CCEF',
          5 => '#F48CBA',
          6 => '#FFFFFF',
          7 => '#FFF468',
          8 => '#1a3caa',
          9 => '#9382C9',
         10 => '#C69B6D',
        );
		
		//config-values
		$info['config'] = array();

	    //lets do some tweak on the templates dependent on the game
	    $info['aq'] = array(
			"UPDATE __styles SET logo_path='/logo/logo_wow.gif' WHERE style_id = 14  ;",
			"UPDATE __styles SET logo_path='logo_wow.gif' WHERE style_id = 15  ;",
			"UPDATE __styles SET logo_path='logo_wow.gif' WHERE style_id = 16  ;",
			"UPDATE __styles SET logo_path='bc_header3.gif' WHERE (style_id > 16) and (style_id < 30)  ;",
			"UPDATE __styles SET logo_path='/logo/logo_wow.gif' WHERE style_id = 30  ;",
			"UPDATE __styles SET logo_path='bc_header3.gif' WHERE style_id = 31  ;",
			"UPDATE __styles SET logo_path='bc_header3.gif' WHERE style_id = 32  ;",
			"UPDATE __styles SET logo_path='/logo/logo_wow.gif' WHERE style_id = 33  ;",
			"UPDATE __styles SET logo_path='wowlogo3.png' WHERE style_id = 35  ;",
	    );
			
	    //Do this SQL Query NOT if the Eqdkp is installed -> only @ the first install
	    if($install)
	    {
			array_push($info['aq'], "INSERT INTO __events VALUES (1, 'Ulduar (10)', 0.00, 'default', NULL, 'ulduar3.png'); ");
			array_push($info['aq'], "INSERT INTO __events VALUES (2, 'Ulduar (25)', 0.00, 'default', NULL, 'ulduar4.png'); ");
			array_push($info['aq'], "INSERT INTO __events VALUES (3, 'Naxxramas (10)', 0.00, 'default', NULL, 'Icon-Naxxramas10.gif'); ");
			array_push($info['aq'], "INSERT INTO __events VALUES (4, 'Naxxramas (25)', 0.00, 'default', NULL, 'Icon-Naxxramas25.gif'); ");
			array_push($info['aq'], "INSERT INTO __events VALUES (5, 'Malygos (10)', 0.00, 'default', NULL, 'eye_10.gif'); ");
			array_push($info['aq'], "INSERT INTO __events VALUES (6, 'Malygos (25)', 0.00, 'default', NULL, 'eye_25.gif'); ");
			array_push($info['aq'], "INSERT INTO __events VALUES (7, 'Sartharion (10)', 0.00, 'default', NULL, 'wotlk-raid-obsidian_sanctum_10.gif'); ");
			array_push($info['aq'], "INSERT INTO __events VALUES (8, 'Sartharion (25)', 0.00, 'default', NULL, 'wotlk-raid-obsidian_sanctum_25.gif'); ");
			//default links
			array_push($info['aq'], "INSERT INTO __plus_links (`link_url`, `link_name`, `link_window`, `link_menu`) VALUES ('http://eu.wowarmory.com', 'Armory', 1, 0);");
			array_push($info['aq'], "INSERT INTO __plus_links (`link_url`, `link_name`, `link_window`, `link_menu`) VALUES ('http://www.wow-europe.com', 'WoW-Europe', 1, 0);");
		}
		return $info;
	}

	/**
	 * Initialises classes
	 *
	 * @param array $langs
	 */
	private function load_classes($langs)
	{
        foreach($langs as $lang) {
        	$this->load_lang_file($lang);
        	$this->classes[$lang] = $this->lang_file[$lang]['class'];
        }
	}

	/**
	 * Initialises races
	 *
	 * @param array $langs
	 */
	private function load_races($langs)
	{
        foreach($langs as $lang) {
        	$this->load_lang_file($lang);
        	$this->races[$lang] = $this->lang_file[$lang]['race'];
        }
	}

	/**
	 * Initialises factions
	 *
	 * @param array $langs
	 */
	private function load_factions($langs)
	{
        foreach($langs as $lang) {
        	$this->load_lang_file($lang);
        	$this->factions[$lang] = $this->lang_file[$lang]['faction'];
        }
	}

	/**
	 * Initialises filters
	 *
	 * @param array $langs
	 */
	private function load_filters($langs)
	{
		global $user;
        if(!$this->classes) {
            $this->load_classes($langs);
        }
        foreach($langs as $lang) {
			$names = $this->classes[$this->lang];
    		$this->filters[$lang] = array(
				array('name' => '-----------', 'value' => false),
    			array('name' => $names[0], 'value' => 'class:0'),
		    	array('name' => $names[1], 'value' => 'class:1'),
		    	array('name' => $names[2], 'value' => 'class:2'),
		    	array('name' => $names[3], 'value' => 'class:3'),
			    array('name' => $names[4], 'value' => 'class:4'),
			    array('name' => $names[5], 'value' => 'class:5'),
			    array('name' => $names[6], 'value' => 'class:6'),
			    array('name' => $names[7], 'value' => 'class:7'),
			    array('name' => $names[8], 'value' => 'class:8'),
			    array('name' => $names[9], 'value' => 'class:9'),
			    array('name' => $names[10], 'value' => 'class:10'),
		    	array('name' => '-----------', 'value' => false),
			    array('name' => $this->glang('plate', $lang), 'value' => 'class:1,5,10'),
			    array('name' => $this->glang('mail', $lang), 'value' => 'class:3,8'),
			    array('name' => $this->glang('leather', $lang), 'value' => 'class:2,7'),
			    array('name' => $this->glang('cloth', $lang), 'value' => 'class:4,6,9'),
			    array('name' => '-----------', 'value' => false),
			    array('name' => $this->glang('tier45', $lang).$names[3].', '.$names[4].', '.$names[9], 'value' => 'class:3,4,9'),
			    array('name' => $this->glang('tier45', $lang).$names[5].', '.$names[7].', '.$names[8], 'value' => 'class:5,7,8'),
			    array('name' => $this->glang('tier45', $lang).$names[2].', '.$names[6].', '.$names[10], 'value' => 'class:2,6,10'),
			    array('name' => '-----------', 'value' => false),
			    array('name' => $this->glang('tier6', $lang).$names[3].', '.$names[10].', '.$names[8], 'value' => 'class:3,8,10'),
			    array('name' => $this->glang('tier6', $lang).$names[5].', '.$names[6].', '.$names[9], 'value' => 'class:5,6,9'),
			    array('name' => $this->glang('tier6', $lang).$names[2].', '.$names[4].', '.$names[7], 'value' => 'class:2,4,7'),
			    array('name' => '-----------', 'value' => false),
			    array('name' => $this->glang('tier78', $lang).$names[3].', '.$names[10].', '.$names[8], 'value' => 'class:3,8,10'),
			    array('name' => $this->glang('tier78', $lang).$names[5].', '.$names[6].', '.$names[9], 'value' => 'class:5,6,9'),
			    array('name' => $this->glang('tier78', $lang).$names[1].', '.$names[2].', '.$names[4].', '.$names[7], 'value' => 'class:1,2,4,7'),
			);
		}
	}

	private function load_realmlist($langs)
	{
		foreach($langs as $lang) {
			$this->load_lang_file($lang);
			$this->realmlist[$lang] = $this->lang_file[$lang]['realmlist'];
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
	function get_wow_talent_spec($classid, $skill1, $skill2, $skill3, $member, $last_update, $light_mode=false){
		global $user, $html, $core, $eqdkp_root_path;

		// return empty string if no skill is given
	 	if ( ($skill1 == 0) and ($skill2 == 0) and ($skill3 == 0)  ) {
	 		return "";
	 	}

	 	if ($classid == '0') {
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
		 $seclang = $this->glang('talents');
		 $spec =  $seclang[$classid][$spec_number];

		 // If no 41 Talent is given, i think its a hybrid
		 if ( ($skill1 < 40) and ($skill2 < 40) and ($skill3 < 40)  )
		 {$spec =	$this->glang('Hybrid');}

		 //USA or Europe Server for the Amory Link
		 if($core->config['pk_server_region'] =="eu"){
			$armoryurl = "http://armory.wow-europe.com";
		 }else{
			$armoryurl = "http://armory.worldofwarcraft.com";
			 }
		 $menulink = $armoryurl.'/character-talents.xml?r='.stripslashes(rawurlencode($core->config['pk_servername'])).
		 			 '&n='.stripslashes(rawurlencode($member));

		 $img = $this->path."talents/".$classid.$spec_number.".png" ;
	 	 $icon = "<img src='".$img."' alt='talent_icon'>";

		 //define the Tooltip
		  $tooltip  = "<table>";
		  $tooltip .= "<tr>
		  					<td><span class=itemdesc>".$icon.$ret_val." - ".$spec."</span></td>
		  			  </tr>";
		  $tooltip .= "<tr>
		  			  <td>".
			  			  $seclang[$classid][0]." - ".
			  			  $seclang[$classid][1]." - ".
			  			  $seclang[$classid][2]."
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

		 //und ab dafür :D
		 return $ret_val ;
	}

	public function showAllvatarWoW_Signatur($charname, $class)
	{
		global $core, $html,$user,$pm,$eqdkp_root_path, $game;

		$img = false;
		$class = $game->get_name('class', $class, 'english');//renameClasstoenglish($class);

		if ($core->config['pk_servername'] && $core->config['pk_server_region'] && $charname && $class && strtolower($core->config['default_game'])=='wow')
		{
			$img  = "http://sig.allvatar.com/signatur/sig.php";
			$img .= "?n=".rawurlencode($charname);
			$img .= "&r=".rawurlencode($core->config['pk_servername']);
			$img .= "&x=".$core->config['pk_server_region'];
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

			if ($core->config['default_lang'] <> 'german')
			{
				$img .= "&lang=eng";
			}

			$url  = "http://www.allvatar.com/signatur/wow/index.php";
			$url .= "?charname=".rawurlencode($charname);
			$url .= "&realm=".rawurlencode($core->config['pk_servername']);
			$url .= "&region=".$core->config['pk_server_region'];
			$url .= "&lan=".$core->config['default_lang'];

			$img = "<a href=".$url." target=_blank><img src='".$img."'></a>";
			$img = $html->ToolTip($user->lang['sig_conf'],$img);
		}
		return $img;
	}

  	function armory_charviewer($member_name, $realm_name, $region)
	{
		$region = (strtolower($region)=='us') ? 'www' : $region ;
		$ret_val = "<iframe src=\"http://$region.wowarmory.com/character-model-embed.xml?r=$realm_name&cn=$member_name&rhtml=true\" 
					scrolling='no' height='444' width='321' frameborder='0'></iframe>";
		return $ret_val ;
	}
	
	function default_slot($slotID,$icons_size=64)
	{
		return "<img src='games/wow/profiles/slots/$slotID.png' height='$icons_size' width='$icons_size'>";
	}
	
	/**
	 * Return an array(left,right,button) with the wow char icons
	 *
	 * @param array $data
	 * @param string $member_name
	 * @return array
	 */
	function ShowItem($data, $member_name, $glyphs=false)
	{
		//armory slot ID
		$item_array[left_slots] 	= array('0'=>'0','1'=>1,'2'=>2,'14'=>14,'4'=>4,'3'=>3,'18'=>18,'8'=>8);		
		$item_array[right_slots] 	= array('9'=>9,'5'=>5,'6'=>6,'7'=>7,'10'=>10,'11'=>11,'12'=>12,'13'=>13);
		$item_array[bottom_slots] 	= array('15'=>15,'16'=>16,'17'=>17);
		$icons_size					= 53;
		$i = 0;
		
		//go through the member items
		foreach ($data as $item) 
		{			
			foreach ($item_array[left_slots] as $key => $slot) 
			{
				if ($slot == $item['@attributes']['slot']) 
				{
						$item_array[left_slots][$slot] = infotooltip('', $item['@attributes']['id'], false, 0, $icons_size, $member_name, false, $slot ,  $in_span=false) ;
				}
			}

			foreach ($item_array[right_slots] as $slot) 
			{
				if ($slot == $item['@attributes']['slot']) 
				{
					$item_array[right_slots][$slot] = infotooltip('', $item['@attributes']['id'], false, 0, $icons_size, $member_name, false, $slot ,  $in_span=false) ;
				}
			}

			foreach ($item_array[bottom_slots] as $slot) 
			{
				if ($slot == $item['@attributes']['slot']) 
				{
					$item_array[bottom_slots][$slot]= infotooltip('', $item['@attributes']['id'], false, 0, $icons_size, $member_name, false, $slot ,  $in_span=false) ;
				}
			}
			
			//Itemlevel avg/min/max
			if ($item['@attributes']['level'] > 10)			 
			{
				$i++;
				$itemlevel[avg] += $item['@attributes']['level'] ;
 
				$itemlevel[min][val] = 999;
				if ($item['@attributes']['level'] < $itemlevel[min][val]) 
				{
					$itemlevel[min][val] = $item['@attributes']['level'] ;					
					$itemlevel[min][item] = infotooltip('', $item['@attributes']['id'], false, 0, 18, $member_name, false, $item['@attributes']['slot'] ,  $in_span=false) ;					
				}else{
					$itemlevel[min][val] =  $itemlevel[min][val];
				}
				
				if ($item['@attributes']['level'] > $itemlevel[max][val]) 
				{
					$itemlevel[max][val] = $item['@attributes']['level'] ;									
					$itemlevel[max][item] = infotooltip('', $item['@attributes']['id'], false, 0, 18, $member_name, false, $item['@attributes']['slot'] ,  $in_span=false) ;					
				}else{
					$itemlevel[max][val] =  $itemlevel[max][val];
				}															
			}			
		}
				
		$avgItemlevel = ($i>0) ? round($itemlevel[avg] / $i) : 0 ;		
		$itemlevel[avg] = $avgItemlevel ;
		$itemlevel[avgBar] =  createBar( $avgItemlevel,$this->maxItemlevel,110,"")  ;		
		$item_array['itemlevel'] = $itemlevel ;		 
		
		//default icons, if no itemstats found		
		foreach ($item_array[left_slots] as $key => $slot) 
		{
			if (strlen($slot) < 3) 
			{
				$item_array[left_slots][$key] = $this->default_slot($slot,$icons_size);
			}
		}
		foreach ($item_array[right_slots] as $key => $slot) 
		{
			if (strlen($slot) < 3) 
			{
				$item_array[right_slots][$key] = $this->default_slot($slot,$icons_size);
			}
		}
		foreach ($item_array[bottom_slots] as $key => $slot) 
		{
			if (strlen($slot) < 3) 
			{
				$item_array[bottom_slots][$key] = $this->default_slot($slot,$icons_size);
			}
		}
		
		
		return $this->format_char_Icons($item_array,$glyphs);			
	}
	
	function format_char_Icons($item_array,$glyphs)
	{
		
		$output_left = "<ul id='wow_icons_left'>";
		foreach ($item_array[left_slots] as $slots) 
		{			
			$output_left .= "<li>$slots </li>";
		}
		$output_left .= "</ul>";		
		
		$output_right = "<ul id='wow_icons_right'>";		
		foreach ($item_array[right_slots] as $slots) 
		{
			$output_right .= "<li>$slots </li>";
		}
		$output_right .= "</ul>";				
		
		$output_bottom = "<ul id='wow_icons_bottom'>";

		//glyphs
		$formatet_glyphs = $this->showGlyphs($glyphs);
		foreach ($formatet_glyphs['minor'] as $minor) 
		{
			$output_bottom .= "<li>$minor </li>";
		}
		$output_bottom .= '&nbsp;&nbsp;&nbsp;&nbsp;';
		
		foreach ($item_array[bottom_slots] as $slots) 
		{
			$output_bottom .= "<li>$slots </li>";
		}
		
		$output_bottom .= '&nbsp;&nbsp;&nbsp;&nbsp;';
		foreach ($formatet_glyphs['mayor'] as $mayor) 
		{
			$output_bottom .= "<li>$mayor </li>";
		}
		
		
		$output_bottom .= "</ul>";				
		
		$out[left] = $output_left;
		$out[right] = $output_right;
		$out[bottom] = $output_bottom;
		$out[itemlevel] = $item_array[itemlevel];
		
		return $out;			
	}
	
	function showGlyphs($data)
	{
		#d($data);
		if (is_array($data))
		{
			foreach ($data as $value) 
			{
				if ($value['@attributes'][type] == 'minor') 
				{
					$output['minor'][] = infotooltip($value['@attributes'][name], 0, false, 0, 32, $member_name, false, false ,  $in_span=" ");
				}else{
					$output['mayor'][] = infotooltip($value['@attributes'][name], 0, false, 0, 32, $member_name, false, false ,  $in_span=" ");	
				}
			}
			return $output;			
		}
	}
	
	function spec($data)
	{		
		if (is_array($data)) 
		{
			foreach ($data as $value) 
			{
				if ($value['@attributes'][active] == '1') 
				{
					$output[active] = $value['@attributes'] ;
				}else{
					$output[non_active] = $value['@attributes'] ;
				}
			}
			return $output ;
		}
	}
	
	function profs($data)
	{
		if (is_array($data)) 
		{
			foreach ($data as $key => $value) 
			{
	            $akt = (int)$value['@attributes']['value'];
	            $max = (int)$value['@attributes']['max'];
	            
	            if($akt>$max)
	            {
	              $max = $akt;
	            }
	            
	            $output[$key]['name'] = $value['@attributes'][name];
				$output[$key]['img'] = "<img src=games/wow/profiles/professions/".$value['@attributes'][key]."-sm.png>" ;				
				$output[$key]['container'] = createBar( $akt,$max,110,"") ;
			}
			return $output ;
		}		
	}
	
	function charInfos($data)
	{				
		global $game, $core;
		$ret['base'] = array($game->glang('strength')    	=> $data['baseStats']['strength']['@attributes']['effective'],
                         	$game->glang('agility')   		=> $data['baseStats']['agility']['@attributes']['effective'],
                         	$game->glang('stamina')   		=> $data['baseStats']['stamina']['@attributes']['effective'],
                         	$game->glang('intellect')   	=> $data['baseStats']['intellect']['@attributes']['effective'],
                         	$game->glang('spirit')   		=> $data['baseStats']['spirit']['@attributes']['effective'],
                         	$game->glang('armor')   		=> $data['baseStats']['armor']['@attributes']['effective']);
	
		$ret['melee'] = array($game->glang('mainHandDamage')=> $data['melee']['mainHandDamage']['@attributes']['min']   ." - ".$data['melee']['mainHandDamage']['@attributes']['max'],
                            $game->glang('mainHandSpeed')	=> $data['melee']['mainHandSpeed']['@attributes']['value'],
                            $game->glang('power')    		=> $data['melee']['power']['@attributes']['effective'],
                            $game->glang('hitRating')    	=> $data['melee']['hitRating']['@attributes']['value'],
                            $game->glang('critChance')    	=> $data['melee']['critChance']['@attributes']['percent'],
                            $game->glang('expertise')    	=> $data['melee']['expertise']['@attributes']['value'])    ;
         
		$ret['range'] = array($game->glang('weaponSkill')   => $data['ranged']['weaponSkill']['@attributes']['value'],
                            $game->glang('damage')    		=> $data['ranged']['damage']['@attributes']['min']." - ".$data['ranged']['damage']['@attributes']['max'],
                            $game->glang('speed')    		=> $data['ranged']['speed']['@attributes']['value'],
                            $game->glang('power')    		=> $data['ranged']['power']['@attributes']['effective'],
                            $game->glang('hitRating')    	=> $data['ranged']['hitRating']['@attributes']['value'],
                            $game->glang('critChance')    	=> $data['ranged']['critChance']['@attributes']['percent'])    ;
		
     	$ret['spell'] = array($game->glang('bonusDamage')   => $data['spell']['bonusDamage']['holy']['@attributes']['value'],
                            $game->glang('bonusHealing')  	=> $data['spell']['bonusHealing']['@attributes']['value'],
                            $game->glang('hitRating')    	=> $data['spell']['hitRating']['@attributes']['value'],
                            $game->glang('critChance')    	=> $data['spell']['critChance']['holy']['@attributes']['percent'],
                            $game->glang('penetration')   	=> $data['spell']['penetration']['@attributes']['value'],
                            $game->glang('manaRegen')    	=> $data['spell']['manaRegen']['@attributes']['casting']." / ". $data['spell']['manaRegen']['@attributes']['notCasting'],
                            $game->glang('hasteRating')   	=> $data['spell']['hasteRating']['@attributes']['hastePercent']);
     		
     	$ret['defenses'] = array($game->glang('armor')      => $data['defenses']['armor']['@attributes']['effective'],
                            $game->glang('defense')    		=> (Int)$data['defenses']['defense']['@attributes']['plusDefense']+(Int) $data['defenses']['defense']['@attributes']['value'],
                            $game->glang('dodge')    		=> $data['defenses']['dodge']['@attributes']['percent'],
                            $game->glang('parry')    		=> $data['defenses']['parry']['@attributes']['percent'],
                            $game->glang('block')    		=> $data['defenses']['block']['@attributes']['percent'],
                            $game->glang('resilience') 		=> $data['defenses']['resilience']['@attributes']['value'])    ;

        $ret['resistances'] = array('arcane'  => $data['resistances']['arcane']['@attributes']['value'],                           
                           'fire'    		=> $data['resistances']['fire']['@attributes']['value'],
                            'frost'    		=> $data['resistances']['frost']['@attributes']['value'],
                            'nature' 		=> $data['resistances']['nature']['@attributes']['value'],
                            'shadow' 		=> $data['resistances']['shadow']['@attributes']['value'])    ;

                            
		foreach ($ret as $info_grp_name => $info_grp)
		{
			$output['html'] .= "<div id='$info_grp_name'><table width=100% cellspacing=0 cellpadding=2>";
			foreach ($info_grp as $info_name => $info)
			{
				if ($info_grp_name == 'resistances') 
				{
					$output['html'] .= "<tr class=".$core->switch_row_class().">
										<td>".$game->glang($info_name)."</td><td BACKGROUND='games/wow/profiles/resistence/".$info_name."_resistance.gif' width='24px' height='27px'><span class='resists'><center>$info</center></span></td></tr>";	
				}else {
					$output['html'] .= "<tr class=".$core->switch_row_class().">
										<td width='170'>$info_name</td><td>$info</td></tr>";	
				}								
			}
			$output['html'] .= "</table></div>";
		}
		 
		$output['header'] = array( 'base' 		=> $game->glang('base'),
								'melee' 		=> $game->glang('melee'),
								'range' 		=> $game->glang('range'),
								'spell' 		=> $game->glang('spell'),
								'defenses' 		=> $game->glang('defenses'),
								'resistances' 	=> $game->glang('resistances'),
								'all' 			=> $game->glang('all'));
		
		return $output;
		
	}
	
	function achievements($data,$armory_links)
	{
			global $game, $pm, $member_id, $eqdkp_root_path;
		
	       $oa =  array( 'earned'     => (int)$data[c]['@attributes'][earned],
	                     'total'      => (int)$data[c]['@attributes'][total],);

	       
	       if ($pm->check(PLUGIN_INSTALLED, 'achievements')) 
			{
				$achievements_link['top'] = "<a href=".$eqdkp_root_path."plugins/achievements/member.php?id=$member_id>";  
			}else {
				$achievements_link['top'] = "<a href=".$armory_links['achievements']." target=_blank>";
			}
			$achievements_link['bottom'] = "</a>";	

	       //total
	       $proz = ($oa['earned'] / $oa['total']) * 100; 	       
	       $container  =$achievements_link['top']."<table width='100%' border='0' cellspacing='1' cellpadding='2'>
	       				<tr>
	       					<th align='center' colspan='3' nowrap='nowrap'>".$game->glang('achievements')."</th> 
	       				</tr>
	       				<tr class='row2'>
	       					<td class='row2' align='center' colspan='3' >
							".createBar($oa['earned'],$oa['total'],350,$game->glang('total').":")."
	       					</td>
	       				</tr>";
	       $achi = 0;
	       
	      
	       foreach ($data[category] as $category )
	       {  
	         $ov[$achi] =  array( 'earned'    => (int)$category[c]['@attributes']['earned'],
	                     'total'              => (int)$category[c]['@attributes']['total'],
	                     'points'             => (int)$category[c]['@attributes']['points'],
	                     'name'               => $category['@attributes']['name'],
	                     'totalPoints'        => (int)$category[c]['@attributes']['totalPoints'] );  
	        $achi++;  
	       }
	       
	       
	        $container  .= "<tr class='row2'>";	        
	      	$row="row2";
	       	for ($runi=0;$runi<sizeof($ov);$runi++)
	       	{	          
          		if($runi==3 || $runi==6){
            		$container  .= "</tr><tr class='row2'>";
          		}

          		$proz = ( $ov[$runi]['earned'] / $ov[$runi]['total']) * 100;  
          		$container  .= "<td align='center' >".$ov[$runi]['name']."<br>
          					   	".createBar( $ov[$runi]['earned'],$ov[$runi]['total'],116,"")."	
          						</td>";       
	       }
	       $container  .= "</tr></table>".$achievements_link['bottom'];	    	       
	       return $container;
	}
	
	function achievement_list($data)
	{
		global $game, $core, $html, $user, $eqdkp_root_path ;
		
		#d($data[achievements][summary][achievement]);
		if (is_array($data[achievements][summary][achievement])) 
		{
			$container  ="<table width='100%' border='0' cellspacing='1' cellpadding='2'>
	       					<tr>
	       						<th  align='center' colspan='4' nowrap='nowrap'>".$game->glang('last5achievements')."</th> 
	       					</tr>";	
	       				
			foreach ($data[achievements][summary][achievement] as $achievement) 
			{
				$id = $achievement['@attributes'][id] ;
				$icon = "http://wowdata.buffed.de/img/icons/wow/32/".sanitize($achievement['@attributes'][icon]).".png" ;
				$ttext = "<b>".$achievement['@attributes'][title]." </b><br><br>".$achievement['@attributes'][desc];
				$link_acmp = $eqdkp_root_path.'plugins/achievements/view_achievement.php?id='.$id ;
				$link_wowhead = 'http://www.wowhead.com/?achievement='.$id ;
				
				$container  .= "<tr class=".$core->switch_row_class().">
								<td width='24'><a href='$link_wowhead' target=_blank><img src='$icon' height='22' widht='22'></a></td>
								<td><a href='$link_acmp'>".$html->ToolTip($ttext , $achievement['@attributes'][title], '', $icon,null) ."</a></td>
								<td width='24'>".$achievement['@attributes'][points]."</td>								
								<td width='90' nowrap='nowrap'>".date($user->style['date_time'], strtotime($achievement['@attributes'][dateCompleted]))."</td>
								
								</tr>";
			}
			
			$container  .= "</table>";
		}
		return $container;
	}
	
	function basicBars($data,$color)
	{		
		$bars['health'] = createBar(-1,$data[characterBars][health]['@attributes'][effective],150,$data[characterBars][health]['@attributes'][effective],'green');
		$bars['secondBar'] = createBar(-1,$data[characterBars][health]['@attributes'][effective],150,$data[characterBars][secondBar]['@attributes'][effective],$color);
		return $bars ; 
	}
	
	function armoryLinks($user,$server,$loc,$locale)
	{
		global $armory;

	  	$wowurl['profil'] = $armory->Link($loc, $user, $server, 'char', '');
	  	$wowurl['talents1'] = $armory->Link($loc, $user, $server, 'talent1', '');
	  	$wowurl['talents2'] = $armory->Link($loc, $user, $server, 'talent2', '');
	  	$wowurl['reputation'] = $armory->Link($loc, $user, $server, 'reputation', '');
	  	$wowurl['achievements'] = $armory->Link($loc, $user, $server, 'achievements', '');
	  	$wowurl['statistics'] = $armory->Link($loc, $user, $server, 'statistics', '');
	  	$wowurl['character-feed'] = $armory->Link($loc, $user, $server, 'character-feed', '');
	  	$wowurl['character-feed-atom'] = $armory->Link($loc, $user, $server, 'character-feed-atom', '');

	  	return $wowurl;
	}
	
  	function CharRssInfos($data)
	{
		global $armory, $user, $html,$game, $core;
				
		$parseArray = $game->glang('rssKeys');
		
		if (is_array($data[entry])) 
		{
			$container  ="<table width='100%' border='0' cellspacing='1' cellpadding='2'>
	       					<tr>
	       						<th  align='center' colspan='4' nowrap='nowrap'>".$game->glang('charRssFeed')."</th> 
	       					</tr>";	
			
			foreach ($data[entry] as $value) 
			{
				$firstStr = trim(substr($value[title],0, strpos($value[title],'[')));
				$item = trim(substr($value[title],strpos($value[title],'[')+1,strpos($value[title],']') - strpos($value[title],'[') -1));
				$lastStr = trim(substr($value[title],strpos($value[title],']') ,strlen($value[title])));
				$type = $parseArray[$firstStr];
				
				
				switch ($type){
					case 'bosskill':
						 $text = $value[title];
						 $icon = $this->path.'/profiles/feed_icon_bosskill.png';	
						 break;
					case 'featofstrength':
						 $text = $value[title];
						 $icon = $this->path.'/profiles/feed_icon_achievement.png';	
						 break;
					case 'item':
						 $text = $firstStr." ".infotooltip($item, 0, false, 0, 0, false, false, false ,  $in_span=" ");
						 $icon = infotooltip($item, 0, false, 0, 22, false, false, false ,  $in_span=" ");	
						 break;
					case 'achievement':
						 $text = $value[title];
						 $icon = $this->path.'/profiles/feed_icon_achievement.png';								
						 break;
					case 'bosskill':
						 $text = $value[title];
						 $icon = $this->path.'/profiles/feed_icon_bosskill.png';
					default :
						 $text = $value[title];
						 $icon = $this->path.'/profiles/feed_icon_bosskill.png';						
						 break;						 
					}		
				
				#d($value[title]);
				
				$container  .= "<tr style='height:28;' class=".$core->switch_row_class().">
									<td widht='20' height=28 align='center'> <img src=".$icon." </img></td>
									<td>".$text."</td>
									<td width='120' align='center'>".date($user->style['date_time'], strtotime($value[published]))."</td>
								";
			}
			$container  .="</table>";
		}

	  	return $container;
	}

	
	function bosskills($data)
	{
		global $core, $html;
		$rawData = $this->GetBossKillRawData($data);
		#d($rawData);
		if (is_array($rawData['zones'])) 
		{
			$zone_html = '<table border="0" width="75%" cellspacing="0" cellpadding="2" >';			 
			foreach ($rawData['zones'] as $zone) 
			{
				#d($zone_data);
				$zone_data = $this->CountArray($rawData[bosskilldata][$zone]);
				$zone_data_hm = $this->CountArray($rawData[bosskilldata][$zone.'_hm'],'heroic');
				$zone_bar = ($zone_data_hm) ? createMultiBar(array($zone_data,$zone_data_hm),300)
											: createBar($zone_data['value'],$zone_data['max'],300, '');
				$runs = ($zone_data['highest'] > 0) ? $zone_data['highest'].' runs' : '0 runs'  ;											
				$heroic_runs = ($zone_data_hm['highest'] > 0) ? ' | '. $zone_data_hm['highest'].' heroic' : '' ;				
				$bossDetails_hm = "";
				
				if (is_array($rawData[bosskilldata][$zone])) 
				{
					$bossDetails = "<td>normal<br><br>";
					foreach ($rawData[bosskilldata][$zone] as $bossID => $bossKillCount) 
					{
						$bossKillCount = ($bossKillCount > 0) ? $bossKillCount : 0 ;
						$font = ($bossKillCount > 0) ? "#ffffff" : "#808080" ;
						$bossDetails .= "<font color='$font'> <b>".$bossKillCount . "</b> ".$this->clearBossName($rawData[bossnames][$bossID]) ."</font> <br>" ; 
					}
					$bossDetails .= "</td>";
				}
															
				if (is_array($rawData[bosskilldata][$zone.'_hm'])) 
				{
					$bossDetails_hm = "<td>heroic<br><br>";
					foreach ($rawData[bosskilldata][$zone.'_hm'] as $bossID => $bossKillCount) 
					{
						$bossKillCount = ($bossKillCount > 0) ? $bossKillCount : 0 ;
						$font = ($bossKillCount > 0) ? 'white' : 'grey' ;
						$bossDetails_hm .= "<font color='$font'><b>".$bossKillCount . "</b> ".$this->clearBossName($rawData[bossnames][$bossID]) ."</font> <br>" ;
					}
					$bossDetails_hm .= "</td>";
				}
				$ttext = "<table cellspacing='20'><tr>$bossDetails $bossDetails_hm </tr></table>";
				

				$zone_html .= '	<tr class="'.$core->switch_row_class().'">
		     						<td width="470">	  	                						                      					                         						
	        							<table border="0" width="400">
	       									<tr> 
	       										<td width="70">
	              									<img src="games/wow/events/'.$zone.'.png" width="40" height="40" ></img>              									 
	              								</td>
	              								<td align="center" nowrap="nowrap">
	                								<b>'.$this->glang($zone).'</b>
	                								<hr height="1"> '.$runs.' '.$heroic_runs.' 
	             								</td>
	             							</tr>  
	        							</table>	                    						
		     						</td>
		     						<td width="250">
		     						'.$html->ToolTip($ttext , $zone_bar).'                   
		     						</td>

		     					</tr>';				
				
			}
			$zone_html .= '</table>';
		}
					
		$bosskillData['instance_quantity'] = $rawData['instance_quantity'];		
		$bosskillData['html'] = $zone_html;
						
		return $bosskillData; 
	}
	
	function GetBossKillRawData($data)
	{
		
		include_once $this->path.'/raid/bosses.php';		
		$dataArray['instance_quantity'] = array(
									'5' => $data[statistic][0]['@attributes'][quantity],
									'10' => $data[statistic][9]['@attributes'][quantity],
									'25' => $data[statistic][12]['@attributes'][quantity] 
									);

		$bossNames = array();									
		foreach ($data[category] as $dungeons) 
		{			
			$i=0;
			foreach ($dungeons as $bosses) 
			{
				if ($i == 0) 
				{
					//instance name in given language 
				}elseif($i == 1)
				{
					//each boss per instance
					foreach ($bosses as $boss) 
					{
						$bossNames[$boss['@attributes']['id']] = $boss['@attributes']['name'];
						#$str = "  '".$boss['@attributes']['id']."' => 0, #".$boss['@attributes']['name'] ;
						#d($str);
											
						//data from bosses.php
						foreach ($wow_bosses as $boss_key => $a_wow_boss) 
						{
							foreach ($a_wow_boss as $key => $value) 
							{
								if ($key == $boss['@attributes']['id']) 
								{
									$wow_bosses[$boss_key][$key] = $boss['@attributes']['quantity'];
								}								
							}
						}						
					}
				}				
				$i++;
			}			
		}
		
		$dataArray['bosskilldata'] = $wow_bosses ;
		$dataArray['zones'] = $wow_instances ;
		$dataArray['bossnames'] = $bossNames ;		
		return $dataArray;		
	}
	
	
  	function CountArray($array, $text=false)
	{
		if (is_array($array)) 
		{			
			$return['max'] = count($array);
			$return['highest'] = max($array);
			$i = 0;
			foreach ($array as $value) 
			{
				if ($value > 0) 
				{
					$i++;
					$return['sum'] += $value;
				}			
			}

			$return['value'] = $i; 
			$return['text'] = ($text) ? $text : false ;
			return $return;
		}
		else{
			return false;
		}
	}

	function clearBossName($bossString)
	{
		$filterThis = $this->glang('bossNameFilter');
		$bossname = str_replace($filterThis,'',$bossString);
		$bossname = substr($bossname,0,strpos($bossname,'('));
		#d($bossname);
		return $bossname;
	}

	
	
  }#class
}
?>