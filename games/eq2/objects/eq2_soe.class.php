<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2011
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 * 
 * $Id$
 *
 * Based on the new battlenet API, see documentation: http://blizzard.github.com/api-wow-docs/
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class eq2_soe {

	protected $apiurl		= 'http://data.soe.com/s:eqdkpplus/json/get/eq2/';
	public $imgurl			= 'http://data.soe.com/s:eqdkpplus/img/eq2/';
	private $chariconUpdates = 0;
	private $chardataUpdates = 0;
	
	protected $convert		= array(
		'classes' => array(
			40 => 1,//Assassin
			4 => 2,//Berserker
			34 => 3,//Brigand
			7 => 4,//Bruiser
			27 => 5,//Coercer
			29 => 6,//Conjuror
			20 => 7,//Defiler
			37 => 8,//Dirge
			17 => 9,//Fury
			3 => 10,//Guardian
			26 => 11,//Illusionist
			14 => 12,//Inquisitor
			6 => 13,//Monk
			19 => 14,//Mystic
			30 => 15,//Necromancer
			10 => 16,//Paladin
			39 => 17,//Ranger
			9 => 18,//Shadowknight
			33 => 19,//Swashbuckler
			13 => 20,//Templar
			36 => 21,//Troubador
			16 => 22,//Warden
			24 => 23,//Warlock
			23 => 24,//Wizard
			42 => 25,//Beastlord
		),
		'races' => array(
			18 => 1, //Sarnak
			5 => 2, //Gnome
			9 => 3, //Human
			0 => 4, //Barbarian
			2 => 5, //Dwarf
			8 => 6, //High Elf
			1 => 7, //Dark Elf
			15 => 8, //Wood Elf
			6 => 9, //Half Elf
			11 => 10, //Kerran
			14 => 11, //Troll
			12 => 12, //Ogre
			4 => 13, //Froglok
			3 => 14, //Erudite
			10 => 15, //Iksar
			13 => 16, //Ratonga
			7 => 17, //Halfling
			17 => 18, //Arasai
			16 => 19, //Fae
			19 => 20, //Freeblood
		),
	);
	
	private $converts		= array();
	
	private $_config		= array(
		'maxChariconUpdates'	=> 10,
		'maxChardataUpdates'	=> 10,
		'caching'				=> true,
		'caching_time'			=> 24,
	);

	/**
	* Initialize the Class
	* 
	* @param $serverloc		Location of Server
	* @param $locale		The Language of the data
	* @return bool
	*/
	public function __construct(){
		
	}
	
	public function __get($name) {
		if(class_exists('registry')) {
			if($name == 'pfh') return registry::register('file_handler');
			if($name == 'puf') return registry::register('urlfetcher');
		}
		return null;
	}

	/**
	* Fetch guild information
	* 
	* @param $user		Character Name
	* @param $realm		Realm Name
	* @param $force		Force the cache to update?
	* @return bol
	*/
	public function guild($guild, $realm='', $force=false){
		$guildid = $this->getGuildID($guild, $realm);
		if (is_array($guildid )) return $guildid;
		$url	= $this->apiurl.'character/?guild.id='.$guildid.'&c:limit=999&c:show=name,guild.status,type,dbid,guild.rank,guild.joined,guild.name';
		if(!$json	= $this->get_CachedData('guilddata_'.$guildid.$realm, $force)){
			$json	= $this->read_url($url);
			$this->set_CachedData($json, 'guilddata_'.$guildid.$realm);
		}
		$chardata	= json_decode($json, true);
		$errorchk	= $this->CheckIfError($chardata);
		return (!$errorchk) ? $chardata: $errorchk;
	}
	
	public function getGuildID($guild, $realm, $force=false){
		$guild = rawurlencode($guild);
		$realm = rawurlencode($realm);
		$url	= $this->apiurl.'guild/?name='.$guild.'&world='.$realm;
		if(!$json	= $this->get_CachedData('guildid_'.$guild.$realm, $force)){
			$json	= $this->read_url($url);
			$this->set_CachedData($json, 'guildid_'.$guild.$realm);
		}
		$chardata	= json_decode($json, true);
		$errorchk	= $this->CheckIfError($chardata);
		return (!$errorchk) ? $chardata['guild_list'][0]['id']: $errorchk;
	}
	
	/**
	* Fetch character information
	* 
	* @param $user		Character Name
	* @param $realm		Realm Name
	* @param $force		Force the cache to update?
	* @return bol
	*/
	public function character($user, $realm, $force=false){
		$user	= rawurlencode($user);
		$realm = rawurlencode($realm);
		$url	= $this->apiurl.'character/?name.first='.$user.'&locationdata.world='.$realm.'&c:resolve=factions(name),appearanceslots(displayname,iconid),equipmentslots(displayname,iconid),achievements(name),statistics';
		$json	= $this->get_CachedData('chardata_'.$user.$realm, $force);
		if(!$json && ($this->chardataUpdates < $this->_config['maxChardataUpdates'])){
			$json	= $this->read_url($url);
			$this->set_CachedData($json, 'chardata_'.$user.$realm);
		}
		$chardata	= json_decode($json, true);
		$errorchk	= $this->CheckIfError($chardata);
		return (!$errorchk) ? $chardata: $errorchk;
	}
	
	/**
	* Create full character Icon Link
	* 
	* @param $thumb		Thumbinformation returned by battlenet JSON feed
	* @return string
	*/
	public function characterIcon($charid, $forceUpdateAll = false){
		$cached_img	= str_replace('/', '_', 'image_character_'.$charid.'.png');
		$img_charicon	= $this->get_CachedData($cached_img, false, true);
		if(!$img_charicon && ($forceUpdateAll || ($this->chariconUpdates < $this->_config['maxChariconUpdates']))){
			$this->set_CachedData($this->read_url($this->imgurl.'character/'.$charid.'/headshot'), $cached_img, true);
			$img_charicon	= $this->get_CachedData($cached_img, false, true);			
			$this->chariconUpdates++;
		}
		if(!$img_charicon){
			$img_charicon	= $this->get_CachedData($cached_img, false, true, true);
			if(filesize($img_charicon) < 900){
				$img_charicon = '';
			}
		}
		
		return $img_charicon;
	}
	

	/**
	* Check if the JSON is an error result
	* 
	* @param $data		XML Data of Char
	* @return error code
	*/
	protected function CheckIfError($data){
		$error_code = (intval($data['returned']) == 0) ? 'no data returned' : false;
		$reason	= (intval($data['returned']) == 0) ? 'no data returned' : false;
		if(!$data || (intval($data['returned']) == 0)){
			return array('status'=> 'no data returned','reason'=>'no data returned');
		}else{
			return false;
		}
	}

	/**
	* Convert from Armory ID to EQDKP Id or reverse
	* 
	* @param $name			name/id to convert
	* @param $type			int/string?
	* @param $cat			category (classes, races, months)
	* @param $ssw			if set, convert from eqdkp id to armory id
	* @return string/int output
	*/
	public function ConvertID($name, $type, $cat, $ssw=''){
		if($ssw){
			if(!is_array($this->converts[$cat])){
				$this->converts[$cat] = array_flip($this->convert[$cat]);
			}
			return ($type == 'int') ? $this->converts[$cat][(int) $name] : $this->converts[$cat][$name];
		}else{
			return ($type == 'int') ? $this->convert[$cat][(int) $name] : $this->convert[$cat][$name];
		}
	}

	/**
	* Write JSON to Cache
	* 
	* @param	$json		XML string
	* @param	$filename	filename of the cache file
	* @return --
	*/
	protected function set_CachedData($json, $filename, $binary=false){
		if($this->_config['caching']){
			$cachinglink = $this->binaryORdata($filename, $binary);
			if(is_object($this->pfh)){
				$this->pfh->putContent($this->pfh->FolderPath('eq2', 'cache', false).$cachinglink, $json);
			}else{
				file_put_contents('data/'.$cachinglink, $json);
			}
		}
	}

	/**
	* get the cached JSON if not outdated & available
	* 
	* @param	$filename	filename of the cache file
	* @param	$force		force an update of the cached json file
	* @return --
	*/
	protected function get_CachedData($filename, $force=false, $binary=false, $returniffalse=false){
		if(!$this->_config['caching']){return false;}
		$data_ctrl = false;
		$rfilename	= (is_object($this->pfh)) ? $this->pfh->FolderPath('eq2', 'cache').$this->binaryORdata($filename, $binary) : 'data/'.$this->binaryORdata($filename, $binary);
		if(is_file($rfilename)){
			$data_ctrl	= (!$force && (filemtime($rfilename)+(3600*$this->_config['caching_time'])) > time()) ? true : false;
		}
		return ($data_ctrl || $returniffalse) ? (($binary) ? $rfilename : @file_get_contents($rfilename)) : false;
	}

	/**
	* delete the cached data
	* 
	* @return --
	*/
	public function DeleteCache(){
		if(!$this->_config['caching']){return false;}
		$rfoldername	= (is_object($this->pfh)) ? $this->pfh->FolderPath('eq2', 'cache') : 'data/';
		return $this->pfh->Delete($rfoldername);
	}

	/**
	* check if binary files or json/data
	* 
	* @param	$input	the input
	* @param	$binary	true/false
	* @return --
	*/
	protected function binaryORdata($input, $binary=false){
		return ($binary) ? $input : 'data_'.md5($input);
	}

	/**
	* Fetch the Data from URL
	* 
	* @param $url URL to Download
	* @return json
	*/
	protected function read_url($url) {
		if(!is_object($this->puf)) {
			global $eqdkp_root_path;
			include_once($eqdkp_root_path.'core/urlfetcher.class.php');
			$this->puf = new urlfetcher();
		}
		return $this->puf->fetch($url);
	}

	/**
	* Check if an error occured
	* 
	* @return error
	*/
	public function CheckError(){
		return ($this->error) ? $this->error : false;
	}
}
?>