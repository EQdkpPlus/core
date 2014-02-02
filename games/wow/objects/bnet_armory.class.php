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

/*********** TODO ************ 
- testing of the header sending & API KEY
******************************/

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class bnet_armory {

	private $version		= '5.2.1';
	private $build			= '$Rev$';
	private $chariconUpdates = 0;
	private $chardataUpdates = 0;
	const apiurl			= 'http://{region}.battle.net/api/';
	const staticrenderurl	= 'http://{region}.battle.net/static-render/';
	const tabardrenderurl	= 'http://{region}.battle.net/wow/static/images/guild/tabards/';

	private $_config		= array(
		'serverloc'				=> 'us',
		'locale'				=> 'en',
		'caching'				=> true,
		'caching_time'			=> 24,
		'apiUrl'				=> '',
		'apiRenderUrl'			=> '',
		'apiTabardRenderUrl'	=> '',
		'apiKeyPrivate'			=> '',
		'apiKeyPublic'			=> '',
		'maxChariconUpdates'	=> 10,
		'maxChardataUpdates'	=> 10,
	);

	protected $convert		= array(
		'classes' => array(
			1		=> '10',	// warrior
			2		=> '5',		// paladin
			3		=> '3',		// hunter
			4		=> '7',		// rogue
			5		=> '6',		// priest
			6		=> '1',		// DK
			7		=> '8',		// shaman
			8		=> '4',		// mage
			9		=> '9',		// warlock
			11		=> '2',		// druid
			10		=> '11',	//monk
		),
		'races' => array(
			'1'		=> 2,		// human
			'2'		=> 7,		// orc
			'3'		=> 3,		// dwarf
			'4'		=> 4,		// night elf
			'5'		=> 6,		// undead
			'6'		=> 8,		// tauren
			'7'		=> 1,		// gnome
			'8'		=> 5,		// troll
			'9'		=> 12,		// Goblin
			'10'	=> 10,		// blood elf
			'11'	=> 9,		// draenei
			'22'	=> 11,		// Worgen
			'24'	=> 13,		// Pandaren neutral
			'25'	=> 13,		// Pandaren alliance
			'26'	=> 13,		// Pandaren horde
		),
		'gender' => array(
			'0'		=> 'Male',
			'1'		=> 'Female',
		),
		'talent'	=> array(
			0	=> array(
				'spell_deathknight_bloodpresence',			// DK
				'spell_nature_starfall',					// Druid
				'ability_hunter_bestialdiscipline',			// Hunter
				'spell_holy_magicalsentry',					// Mage
				'spell_holy_holybolt',						// Paladin
				'spell_holy_powerwordshield',				// Priest
				'ability_rogue_eviscerate',					// Rogue
				'spell_nature_lightning',					// Shaman
				'spell_shadow_deathcoil',					// Warlock
				'ability_warrior_savageblow',				// Warrior
				'spell_monk_brewmaster_spec',				// Monk
			),
			1	=> array(
				'spell_deathknight_frostpresence',			// DK
				'ability_druid_catform',					// Druid
				'ability_hunter_focusedaim',				// Hunter
				'spell_fire_firebolt02',					// Mage
				'ability_paladin_shieldofthetemplar',		// Paladin
				'spell_holy_guardianspirit',				// Priest
				'ability_backstab',							// Rogue
				'spell_shaman_improvedstormstrike',			// Shaman
				'spell_shadow_metamorphosis',				// Warlock
				'ability_warrior_innerrage',				// Warrior
				'spell_monk_mistweaver_spec',				// Monk
			),
			2	=> array(
				'spell_deathknight_unholypresence',			// DK
				'ability_racial_bearform',					// Druid
				'ability_hunter_camouflage',				// Hunter
				'spell_frost_frostbolt02',					// Mage
				'spell_holy_auraoflight',					// Paladin
				'spell_shadow_shadowwordpain',				// Priest
				'ability_stealth',							// Rogue
				'spell_nature_magicimmunity',				// Shaman
				'spell_shadow_rainoffire',					// Warlock
				'ability_warrior_defensivestance',			// Warrior
				'spell_monk_windwalker_spec',				// Monk
			),
			3	=> array('spell_nature_healingtouch')		// Druid
		),
	);

	private $serverlocs		= array(
		'eu'	=> 'EU',
		'us'	=> 'US',
		'kr'	=> 'KR',
		'tw'	=> 'TW',
	);
	private $converts		= array();

	/**
	* Initialize the Class
	* 
	* @param $serverloc		Location of Server
	* @param $locale		The Language of the data
	* @return bool
	*/
	public function __construct($serverloc='us', $locale='en_EN', $apikeys=false){
		$this->_config['serverloc']	= ($serverloc != '') ? $serverloc : 'en_EN';
		$this->_config['locale']	= $locale;
		$this->setApiUrl($this->_config['serverloc']);
		if(isset($apikeys['apiKeyPrivate']) && isset($apikeys['apiKeyPublic'])){
			$this->_config['apiKeyPrivate']	= $apikeys['apiKeyPrivate'];
			$this->_config['apiKeyPublic']	= $apikeys['apiKeyPublic'];
		}
	}
	
	public function __get($name) {
		if(class_exists('registry')) {
			if($name == 'pfh') return registry::register('file_handler');
			if($name == 'puf') return registry::register('urlfetcher');
		}
		return null;
	}

	/**
	* Set some settings
	* 
	* @param $setting	Which language to import
	* @return bool
	*/
	public function setSettings($setting){
		if(isset($setting['loc'])){
			$this->_config['serverloc']	= $setting['loc'];
			$this->setApiUrl($this->_config['serverloc']);
		}
		if(isset($setting['locale'])){
			$this->_config['locale']	= $setting['locale'];
		}
		if(isset($setting['caching_time'])){
			$this->_config['caching_time']	= $setting['caching_time'];
		}
		if(isset($setting['caching'])){
			$this->_config['caching']	= $setting['caching'];
		}
		if(isset($setting['apiKeyPrivate']) && isset($setting['apiKeyPublic'])){
			$this->_config['apiKeyPrivate']	= $setting['apiKeyPrivate'];
			$this->_config['apiKeyPublic']	= $setting['apiKeyPublic'];
		}
	}

	public function getServerLoc(){
		return $this->serverlocs;
	}

	public function getVersion(){
		return $this->version.((preg_match('/\d+/', $this->build, $match))? '#'.$match[0] : '');
	}

	/**
	* Generate Link to Armory
	* 
	* @param $user			Name of the User
	* @param $server		Name of the WoW Server
	* @param $mode			Which page to open? (char, talent, statistics, reputation, guild, achievements)
	* @param $guild			Name of the guild
	* @return string		output
	*/
	public function bnlink($user, $server, $mode='char', $guild='', $talents=array()){
		$linkprfx	= str_replace('/api', '/wow', $this->_config['apiUrl']);
		switch ($mode) {
			case 'char':
				return $linkprfx.sprintf('character/%s/%s/simple', $this->ConvertInput($server, true, true), $this->ConvertInput($user));break;
			case 'talent':
				return $linkprfx.sprintf('character/%s/%s/simple#talents', $this->ConvertInput($server, true, true), $this->ConvertInput($user));break;
			case 'statistics':
				return $linkprfx.sprintf('character/%s/%s/statistic', $this->ConvertInput($server, true, true), $this->ConvertInput($user));break;
			case 'profession':
				return $linkprfx.sprintf('character/%s/%s/profession/', $this->ConvertInput($server, true, true), $this->ConvertInput($user));break;
			case 'reputation':
				return $linkprfx.sprintf('character/%s/%s/reputation', $this->ConvertInput($server, true, true), $this->ConvertInput($user));break;
			case 'pvp':
				return $linkprfx.sprintf('character/%s/%s/pvp', $this->ConvertInput($server, true, true), $this->ConvertInput($user));break;
			case 'achievements':
				return $linkprfx.sprintf('character/%s/%s/achievement', $this->ConvertInput($server, true, true), $this->ConvertInput($user));break;
			case 'character-feed':
				return $linkprfx.sprintf('character/%s/%s/feed', $this->ConvertInput($server, true, true), $this->ConvertInput($user));break;
			case 'talent-calculator':
				return $linkprfx.sprintf('tool/talent-calculator#d%s!%s!%s', $talents['calcSpec'], $talents['calcTalent'], $talents['calcGlyph']);break;
			case 'guild':
				return $linkprfx.sprintf('guild/%s/%s/roster', $this->ConvertInput($server, true, true), $this->ConvertInput($guild));break;
			case 'guild-achievements':
				return $linkprfx.sprintf('guild/%s/%s/achievement', $this->ConvertInput($server, true, true), $this->ConvertInput($guild));break;
			case 'askmrrobot':
			return sprintf('http://www.askmrrobot.com/wow/gear/%s/%s/%s', $this->_config['serverloc'], $this->ConvertInput($server, true, true), $this->ConvertInput($user));break;
		}
	}

	/**
	* Return an array with all links for one char
	* 
	* @param $user			Name of the User
	* @param $server		Name of the WoW Server
	* @return string		output
	*/
	public function a_bnlinks($user, $server, $guild=false){
		return array(
			'profil'				=> $this->bnlink($user, $server, 'char'),
			'talents'				=> $this->bnlink($user, $server, 'talent'),
			'profession'			=> $this->bnlink($user, $server, 'profession'),
			'reputation'			=> $this->bnlink($user, $server, 'reputation'),
			'pvp'					=> $this->bnlink($user, $server, 'pvp'),
			'achievements'			=> $this->bnlink($user, $server, 'achievements'),
			'statistics'			=> $this->bnlink($user, $server, 'statistics'),
			'character-feed'		=> $this->bnlink($user, $server, 'character-feed'),
			'guild'					=> $this->bnlink($user, $server, 'guild', $guild),

			// external ones
			'askmrrobot'			=> $this->bnlink($user, $server, 'askmrrobot'),
		);
	}

	/**
	* Fetch character information
	* 
	* @param $user		Character Name
	* @param $realm		Realm Name
	* @param $force		Force the cache to update
	* @param $params	array, optional fields such as reputation, appearance, mounts, pets, hunterPets,petSlots, quests, pvp
	* @return bol
	*/
	public function character($user, $realm, $force=false, $params=array()){
		$realm		= $this->ConvertInput($this->cleanServername($realm));
		$user		= $this->ConvertInput($user);
		$basicparam	= array('guild', 'stats', 'feed', 'talents', 'items', 'titles', 'professions', 'achievements', 'progression');
		$usedparams = array_merge($basicparam, $params);
		$force		= (count($params) > 1 && $force == false) ? true : $force;
		$wowurl		= $this->_config['apiUrl'].sprintf('wow/character/%s/%s?locale=%s&fields='.implode(',', $usedparams), $realm, $user, $this->_config['locale']);
		$json		= $this->get_CachedData('chardata_'.$user.$realm, $force);		
		if(!$json && ($this->chardataUpdates < $this->_config['maxChardataUpdates'])){
			$json	= $this->read_url($wowurl);
			$this->set_CachedData($json, 'chardata_'.$user.$realm);
			$this->chardataUpdates++;
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
	public function characterIcon($chardata, $forceUpdateAll = false){
		$cached_img	= str_replace('/', '_', 'image_character_'.$this->_config['serverloc'].'_'.$chardata['thumbnail']);
		$img_charicon	= $this->get_CachedData($cached_img, false, true);
		if(!$img_charicon && ($forceUpdateAll || ($this->chariconUpdates < $this->_config['maxChariconUpdates']))){
			$this->set_CachedData($this->read_url($this->_config['apiRenderUrl'].sprintf('%s/%s', $this->_config['serverloc'], $chardata['thumbnail'])), $cached_img, true);
			$img_charicon	= $this->get_CachedData($cached_img, false, true);

			// this is due to an api bug and may be removed some day, thumbs are always set and could be 404!
			if(filesize($img_charicon) < 400){
				$linkprfx	= str_replace('/api', '/wow/static/images/2d/avatar/', $this->_config['apiUrl']);
				$this->set_CachedData($this->read_url($linkprfx.sprintf('%s-%s.jpg', $chardata['race'], $chardata['gender'])), $cached_img, true);
			}
			$this->chariconUpdates++;
		}
		
		if (!$img_charicon){
			$img_charicon	= $this->get_CachedData($cached_img, false, true, true);
			if(filesize($img_charicon) < 400){
				$img_charicon = '';
			}
		}
		
		return $img_charicon;
	}

	public function characterIconSimple($race, $gender='0'){
		return sprintf('http://eu.battle.net/wow/static/images/2d/profilemain/race/%s-%s.jpg', $race, $gender);
	}

	/**
	* Create full character Image Link
	* 
	* @param $thumb		Thumbinformation returned by battlenet JSON feed
	* @param $type		Image tyoe, big or inset
	* @return string
	*/
	public function characterImage($chardata, $type='big', $forceUpdateAll = false){
		switch($type){
			case 'big':		$dtype_ending = 'profilemain'; break;
			case 'inset':	$dtype_ending = 'inset'; break;
			default: $dtype_ending = 'profilemain';
		}
		$imgfile = str_replace('avatar.jpg', $dtype_ending.'.jpg', $chardata['thumbnail']);
		$cached_img	= str_replace('/', '_', 'image_big_character_'.$this->_config['serverloc'].'_'.$imgfile);
		$img_charicon	= $this->get_CachedData($cached_img, false, true);
		if(!$img_charicon || $forceUpdateAll){
			$this->set_CachedData($this->read_url($this->_config['apiRenderUrl'].sprintf('%s/%s', $this->_config['serverloc'], $imgfile)), $cached_img, true);
			$img_charicon	= $this->get_CachedData($cached_img, false, true);
		}
		return $img_charicon;
	}

	public function talentIcon($name){
		return 'http://'.$this->_config['serverloc'].'.media.blizzard.com/wow/icons/36/'.$name.'.jpg';
	}

	public function selectedTitle($titles, $cleantitle=false){
		if(is_array($titles)){
			foreach($titles as $titledata){
				if(isset($titledata['selected']) && $titledata['selected'] == '1'){
					if($cleantitle){
						$temp_data = str_replace('%s, ', '', $titledata['name']);
						$temp_data = str_replace(' %s', '', $temp_data);
						$temp_data = str_replace('%s ', '', $temp_data);
						$temp_data = str_replace('%s', '', $temp_data);
						return $temp_data;
					}else{
						return $titledata['name'];
					}
				}
			}
		}
	}

	/**
	* Fetch guild information
	* 
	* @param $user		Character Name
	* @param $realm		Realm Name
	* @param $force		Force the cache to update?
	* @return bol
	*/
	public function guild($guild, $realm, $force=false){
		$realm	= $this->ConvertInput($this->cleanServername($realm));
		$guild	= $this->ConvertInput($guild);
		$wowurl	= $this->_config['apiUrl'].sprintf('wow/guild/%s/%s?locale=%s&fields=members,achievements,news,challenge', $realm, $guild, $this->_config['locale']);
		if(!$json	= $this->get_CachedData('guilddata_'.$guild.$realm, $force)){
			$json	= $this->read_url($wowurl);
			$this->set_CachedData($json, 'guilddata_'.$guild.$realm);
		}
		$chardata	= json_decode($json, true);
		$errorchk	= $this->CheckIfError($chardata);
		return (!$errorchk) ? $chardata: $errorchk;
	}

	/**
	* Generate guild tabard & save in cache
	* 
	* @param $emblemdata	emblem data array of battle.net api
	* @param $faction		name of the faction
	* @param $guild			name of the guild
	* @param $imgwidth		width of the image
	* @return bol
	*/
	public function guildTabard($emblemdata, $faction, $guild, $imgwidth=215){
		$cached_img	= sprintf('image_tabard_%s_w%s.png', utf8_decode(strtolower(str_replace(' ', '', $guild))), $imgwidth);
		if(!$imgfile = $this->get_CachedData($cached_img, false, true)){
			if(!function_exists('imagecreatefrompng') || version_compare(PHP_VERSION, "5.3.0", '<')){
				return $this->root_path.sprintf('games/wow/guild/tabard_%s.png', (($faction == 0) ? 'alliance' : 'horde'));
			}
			$imgfile	= $this->get_CachedData($cached_img, false, true, true);

			// set the URL of the required image parts
			$img_emblem		= $this->_config['apiTabardRenderUrl'].sprintf('emblem_%02s', $emblemdata['icon']) .'.png';
			$img_border		= $this->_config['apiTabardRenderUrl']."border_".(($emblemdata['border'] == '-1') ? sprintf("%02s", $emblemdata['border']) : '00').".png";
			$img_ring		= $this->_config['apiTabardRenderUrl'].sprintf('ring-%s', (($faction == 0) ? 'alliance' : 'horde')) .'.png';
			$img_background	= $this->_config['apiTabardRenderUrl'].'bg_00.png';
			$img_shadow		= $this->_config['apiTabardRenderUrl'].'shadow_00.png';
			$img_overlay	= $this->_config['apiTabardRenderUrl'].'overlay_00.png';
			$img_hooks		= $this->_config['apiTabardRenderUrl'].'hooks.png';

			// set the image size (max width 215px) & generate the guild tabard image
			$img_resampled	= false;
			if ($imgwidth > 1 && $imgwidth < 215){
				$img_resampled	= true;
				$imgheight		= ($imgwidth/215)*230;
				$img_tabard		= imagecreatetruecolor($imgwidth, $imgheight);
				$tranparency	= imagecolorallocatealpha($img_tabard, 0, 0, 0, 127);
				imagefill($img_tabard, 0, 0, $tranparency);
				imagesavealpha($img_tabard,true);
				imagealphablending($img_tabard, true);
			}

			// generate the output image
			$img_genoutput	= imagecreatetruecolor(215, 230);
			imagesavealpha($img_genoutput,true);
			imagealphablending($img_genoutput, true);
			$tranparency	= imagecolorallocatealpha($img_genoutput, 0, 0, 0, 127);
			imagefill($img_genoutput, 0, 0, $tranparency);

			// generate the ring
			$ring			= imagecreatefrompng($img_ring);
			$ring_size		= getimagesize($img_ring);
			$emblem_image	= imagecreatefrompng($img_emblem);
			$emblem_size	= getimagesize($img_emblem);
			if($this->checkImageLayerEffect()){
				imagelayereffect($emblem_image, IMG_EFFECT_OVERLAY);
			}
			$tmp_emblemcolor= preg_replace('/^ff/i','',$emblemdata['iconColor']);
			$emblemcolor	= array(hexdec(substr($tmp_emblemcolor,0,2)), hexdec(substr($tmp_emblemcolor,2,2)), hexdec(substr($tmp_emblemcolor,4,2)));
			if($this->checkImageLayerEffect()){
				imagefilledrectangle($emblem_image,0,0,$emblem_size[0],$emblem_size[1],imagecolorallocate($emblem_image, $emblemcolor[0], $emblemcolor[1], $emblemcolor[2]));
			}else{
				$this->imageColorize($emblem_image, $emblemcolor[0], $emblemcolor[1], $emblemcolor[2]);
			}

			// generate the border
			$border			= imagecreatefrompng($img_border);
			$border_size	= getimagesize($img_border);
			if($this->checkImageLayerEffect()){
				imagelayereffect($border, IMG_EFFECT_OVERLAY);
			}
			$tmp_bcolor		= preg_replace('/^ff/i','',$emblemdata['borderColor']);
			$bordercolor	= array(hexdec(substr($tmp_bcolor,0,2)), hexdec(substr($tmp_bcolor,2,2)), hexdec(substr($tmp_bcolor,4,2)));
			if($this->checkImageLayerEffect()){
				imagefilledrectangle($border,0,0,$border_size[0]+100,$border_size[0]+100,imagecolorallocate($border, $bordercolor[0], $bordercolor[1], $bordercolor[2]));
			}else{
				$this->imageColorize($border, $bordercolor[0], $bordercolor[1], $bordercolor[2]);
			}

			// generate the background
			$shadow			= imagecreatefrompng($img_shadow);
			$bg				= imagecreatefrompng($img_background);
			$bg_size		= getimagesize($img_background);
			if($this->checkImageLayerEffect()){
				imagelayereffect($bg, IMG_EFFECT_OVERLAY);
			}
			$tmp_bgcolor	= preg_replace('/^ff/i','',$emblemdata['backgroundColor']);
			$bgcolor		= array(hexdec(substr($tmp_bgcolor,0,2)), hexdec(substr($tmp_bgcolor,2,2)), hexdec(substr($tmp_bgcolor,4,2)));
			if($this->checkImageLayerEffect()){
				imagefilledrectangle($bg,0,0,$bg_size[0]+100,$bg_size[0]+100,imagecolorallocate($bg, $bgcolor[0], $bgcolor[1], $bgcolor[2]));
			}else{
				$this->imageColorize($bg, $bgcolor[0], $bgcolor[1], $bgcolor[2]);
			}

			// put it together...
			imagecopy($img_genoutput,$ring,0,0,0,0, $ring_size[0],$ring_size[1]);
			$size			= getimagesize($img_shadow);
			imagecopy($img_genoutput,$shadow,20,23,0,0, $size[0],$size[1]);
			imagecopy($img_genoutput,$bg,20,23,0,0, $bg_size[0],$bg_size[1]);
			imagecopy($img_genoutput,$emblem_image,37,53,0,0, $emblem_size[0],$emblem_size[1]);
			imagecopy($img_genoutput,$border,32,38,0,0, $border_size[0],$border_size[1]);
			$size			= getimagesize($img_overlay);
			imagecopy($img_genoutput,imagecreatefrompng($img_overlay),20,25,0,0, $size[0],$size[1]);
			$size			= getimagesize($img_hooks);
			imagecopy($img_genoutput,imagecreatefrompng($img_hooks),18,23,0,0, $size[0],$size[1]);

			// check if the image is the same size as the image file parts, if not, resample the image
			if ($img_resampled){
				imagecopyresampled($img_tabard, $img_genoutput, 0, 0, 0, 0, $imgwidth, $imgheight, 215, 230);
			}else{
				$img_tabard = $img_genoutput;
			}
			
			$strTmpFolder = (is_object($this->pfh)) ? $this->pfh->FolderPath('tmp', '').$cached_img : $imgfile;
			
			//Create PNG
			imagepng($img_tabard,$strTmpFolder);
			
			//Move from tmp-Folder to right folder
			if (is_object($this->pfh)){
				$this->pfh->FileMove($strTmpFolder, $imgfile);
			}
		}
		return $imgfile;
	}

	/**
	* Fetch realm information
	* 
	* @param $realm		Realm Name
	* @param $force		Force the cache to update?
	* @return bol
	*/
	public function realm($realms, $force=false){
		$wowurl = $this->_config['apiUrl'].sprintf('wow/realm/status?locale=%s&realms=%s', $this->_config['locale'], $realms = ((is_array($realms)) ? implode(",",$realms) : ''));
		if(!$json	= $this->get_CachedData('realmdata_'.str_replace(",", "", $realms), $force)){
			$json	= $this->read_url($wowurl);
			$this->set_CachedData($json, 'realmdata_'.str_replace(",", "", $realms));
		}
		$realmdata	= json_decode($json, true);
		$errorchk	= $this->CheckIfError($realmdata);
		return (!$errorchk) ? $realmdata: $errorchk;
	}

	/**
	* Fetch pvpteam information
	* 
	* @param $teamsize	TeamSize = "2v2" | "3v3" | "5v5" | "rbg"
	* @param $force		Force the cache to update?
	* @return bol
	*/
	public function pvpteam($teamsize, $force=false){
		switch($teamname){
			case '2v2':	$teamsize = '2v2'; break;
			case '3v3':	$teamsize = '3v3'; break;
			case '5v5':	$teamsize = '5v5'; break;
			case 'rbg':	$teamsize = 'rbg'; break;
			default: $teamsize = '2v2';
		}
		$wowurl = $this->_config['apiUrl'].sprintf('wow/leaderboard/%s?locale=%s', $this->ConvertInput($realm), $teamsize, $this->ConvertInput($teamname), $this->_config['locale']);
		if(!$json	= $this->get_CachedData('pvpdata_'.$guild.$teamname.$teamsize, $force)){
			$json	= $this->read_url($wowurl);
			$this->set_CachedData($json, 'pvpdata_'.$guild.$teamname.$teamsize);
		}
		$pvpdata	= json_decode($json, true);
		$errorchk	= $this->CheckIfError($pvpdata);
		return (!$errorchk) ? $pvpdata: $errorchk;
	}

	/**
	* Fetch item information
	* 
	* @param $itemid	battlenet Item ID
	* @param $force		Force the cache to update?
	* @return bol
	*/
	public function item($itemid, $force=false){
		$wowurl = $this->_config['apiUrl'].sprintf('wow/item/%s?locale=%s', $itemid, $this->_config['locale']);
		if(!$json	= $this->get_CachedData('itemdata_'.$itemid, $force)){
			$json	= $this->read_url($wowurl);
			$this->set_CachedData($json, 'itemdata_'.$itemid);
		}
		$itemdata	= json_decode($json, true);
		$errorchk	= $this->CheckIfError($itemdata);
		return (!$errorchk) ? $itemdata: $errorchk;
	}
	
	/**
	* Fetch achievement information
	* 
	* @param $achievementid		battlenet Achievement ID
	* @param $force				Force the cache to update?
	* @return bol
	*/
	public function achievement($achievementid, $force=false){
		$wowurl = $this->_config['apiUrl'].sprintf('wow/achievement/%s?locale=%s', $achievementid, $this->_config['locale']);
		if(!$json	= $this->get_CachedData('achievementdata_'.$achievementid, $force)){
			$json	= $this->read_url($wowurl);
			$this->set_CachedData($json, 'achievementdata_'.$achievementid);
		}
		$achievementdata	= json_decode($json, true);
		$errorchk	= $this->CheckIfError($achievementdata);
		return (!$errorchk) ? $achievementdata : $errorchk;
	}


	/**
	* Fetch quest information
	* 
	* @param $questid	battlenet quest ID
	* @param $force		Force the cache to update?
	* @return bol
	*/
	public function quest($questid, $force=false){
		$wowurl = $this->_config['apiUrl'].sprintf('wow/quest/%s?locale=%s', $questid, $this->_config['locale']);
		if(!$json	= $this->get_CachedData('questdatadata_'.$questid, $force)){
			$json	= $this->read_url($wowurl);
			$this->set_CachedData($json, 'questdatadata_'.$questid);
		}
		$questdata	= json_decode($json, true);
		$errorchk	= $this->CheckIfError($questdata);
		return (!$errorchk) ? $questdata : $errorchk;
	}

	/**
	* Fetch recipe information
	* 
	* @param $questid	battlenet quest ID
	* @param $force		Force the cache to update?
	* @return bol
	*/
	public function recipe($recipeid, $force=false){
		$wowurl = $this->_config['apiUrl'].sprintf('wow/recipe/%s?locale=%s', $recipeid, $this->_config['locale']);
		if(!$json	= $this->get_CachedData('recipedatadata_'.$recipeid, $force)){
			$json	= $this->read_url($wowurl);
			$this->set_CachedData($json, 'recipedatadata_'.$recipeid);
		}
		$recipe	= json_decode($json, true);
		$errorchk	= $this->CheckIfError($recipe);
		return (!$errorchk) ? $recipe : $errorchk;
	}

	/**
	* Fetch spell information
	* 
	* @param $questid	battlenet quest ID
	* @param $force		Force the cache to update?
	* @return bol
	*/
	public function spell($spellid, $force=false){
		$wowurl = $this->_config['apiUrl'].sprintf('wow/spell/%s?locale=%s', $spellid, $this->_config['locale']);
		if(!$json	= $this->get_CachedData('spelldatadata_'.$spellid, $force)){
			$json	= $this->read_url($wowurl);
			$this->set_CachedData($json, 'spelldatadata_'.$spellid);
		}
		$spell		= json_decode($json, true);
		$errorchk	= $this->CheckIfError($spell);
		return (!$errorchk) ? $spell : $errorchk;
	}

	/**
	* Fetch challenge mode information
	* 
	* @param $realm		battlenet realm
	* @param $force		Force the cache to update?
	* @return bol
	*/
	public function challenge($realm, $force=false){
		$wowurl = $this->_config['apiUrl'].sprintf('wow/challenge/%s?locale=%s', $this->ConvertInput($realm), $this->_config['locale']);
		if(!$json	= $this->get_CachedData('challengedatadata_'.$realm, $force)){
			$json	= $this->read_url($wowurl);
			$this->set_CachedData($json, 'challengedatadata_'.$realm);
		}
		$challengedata	= json_decode($json, true);
		$errorchk		= $this->CheckIfError($challengedata);
		return (!$errorchk) ? $challengedata : $errorchk;
	}

	/**
	* Fetch challenge mode information
	* 
	* @param $abilityid	Ability ID
	* @param $force		Force the cache to update?
	* @return bol
	*/
	public function battlepet($abilityid, $force=false){
		$wowurl = $this->_config['apiUrl'].sprintf('wow/battlePet/ability/%s?locale=%s', $abilityid, $this->_config['locale']);
		if(!$json	= $this->get_CachedData('battlepetdatadata_'.$abilityid, $force)){
			$json	= $this->read_url($wowurl);
			$this->set_CachedData($json, 'battlepetdatadata_'.$abilityid);
		}
		$battlepet	= json_decode($json, true);
		$errorchk	= $this->CheckIfError($battlepet);
		return (!$errorchk) ? $battlepet : $errorchk;
	}

	/**
	* This API resource provides a per-realm list of recently generated auction house data dumps.
	* 
	* @param $abilityid	Ability ID
	* @param $force		Force the cache to update?
	* @return bol
	*/
	public function auction($realm, $force=false){
		$wowurl = $this->_config['apiUrl'].sprintf('api/wow/auction/data/%s?locale=%s', $this->ConvertInput($realm), $this->_config['locale']);
		if(!$json	= $this->get_CachedData('auctiondatadata_'.$realm, $force)){
			$json	= $this->read_url($wowurl);
			$this->set_CachedData($json, 'auctiondatadata_'.$realm);
		}
		$auction	= json_decode($json, true);
		$errorchk	= $this->CheckIfError($auction);
		return (!$errorchk) ? $auction : $errorchk;
	}

	// DATA RESOURCES
	public function getdata($type='character', $sub_type='achievements', $force=false){
		$wowurl	= $this->_config['apiUrl'].sprintf('wow/data/'.$type.'/'.$sub_type.'?locale=%s', $this->_config['locale']);
		if(!$json	= $this->get_CachedData('data_'.$type.'_'.$sub_type, $force)){
			$json	= $this->read_url($wowurl);
			$this->set_CachedData($json, 'data_'.$type.'_'.$sub_type);
		}
		$chardata	= json_decode($json, true);
		$errorchk	= $this->CheckIfError($chardata);
		return (!$errorchk) ? $chardata: $errorchk;
	}
	
	/**
	 * Returns Category for Achievement-ID, usefull for Armory-Links
	 *
	 * @int 	$intAchievID					Armory Achievement-ID
	 * @array 	$arrAchievementData	Difference  Achievement-Data, e.g. from Armory Resources
	 * @return 	formatted String				10588 or 10589:92
	 */
	function getCategoryForAchievement($intAchievID, $arrAchievementData){
		foreach($arrAchievementData['achievements'] as $arrAchievs){
			$intCatID = $arrAchievs['id'];
			foreach ($arrAchievs['achievements'] as $arrAchievs2){
				if ((int)$arrAchievs2['id'] == $intAchievID) return $intCatID;
			}
			
			if (isset($arrAchievs['categories'])){
				foreach ($arrAchievs['categories'] as $arrCatAchievs2){
					$intNewCatID = $intCatID . ':'. $arrCatAchievs2['id'];
					foreach ($arrCatAchievs2['achievements'] as $arrCatAchievs3){
						if ((int)$arrCatAchievs3['id'] == $intAchievID) return $intNewCatID;
					}
				}
			}
		}
	}

	/**
	* Check if the JSON is an error result
	* 
	* @param $data		XML Data of Char
	* @return error code
	*/
	protected function CheckIfError($data){
		$status	= (isset($data['status'])) ? $data['status'] : false;
		$reason	= (isset($data['reason'])) ? $data['reason'] : false;
		$error = '';
		if($status){
			return array('status'=>$status,'reason'=>$reason);
		}elseif(is_array($data) && count($data) == 0){
			return array('status'=>'nok','reason'=>'Battle.net API returned an empty array.');
		}
		return false;
	}

	/**
	* Clean the Servername if taken from Database
	* 
	* @return string output
	*/
	public function cleanServername($server){
		return html_entity_decode($server,ENT_QUOTES,"UTF-8");
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
	public function ConvertID($name, $type, $cat, $ssw=false){
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
	* Convert talent from icon to id
	* 
	* @param $name			name/id to convert
	* @return string/int output
	*/
	public function ConvertTalent($name){
		return key(search_in_array($name, $this->convert['talent']));
	}

	/**
	* Prepare a string for beeing sent to armory
	* 
	* @param $input 
	* @return string output
	*/
	public function ConvertInput($input, $removeslash=false, $removespace=false){
		// new servername convention: mal'ganis = malganis
		$input = ($removespace) ? str_replace(" ", "-", $input) : $input;
		return ($removeslash) ? stripslashes(str_replace("'", "", $input)) : stripslashes(rawurlencode($input));
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
			$cachinglink = $this->binaryORdata($this->clean_name($filename), $binary);
			if(is_object($this->pfh)){
				$this->pfh->putContent($this->pfh->FolderPath('armory', 'cache', false).$cachinglink, $json);
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
		$rfilename	= (is_object($this->pfh)) ? $this->pfh->FolderPath('armory', 'cache').$this->binaryORdata($this->clean_name($filename), $binary) : 'data/'.$this->binaryORdata($this->clean_name($filename), $binary);
		if(is_file($rfilename)){
			$data_ctrl	= (!$force && (filemtime($rfilename)+(3600*$this->_config['caching_time'])) > time()) ? true : false;
		}
		return ($data_ctrl || $returniffalse) ? (($binary) ? $rfilename : @file_get_contents($rfilename)) : false;
	}

	/**
	* strip all non conform chars out of the filename
	* 
	* @param	$name	name of the file to be cleaned
	* @return --
	*/
	protected function clean_name($name){
		return preg_replace('/[^a-zA-Z0-9_ \.]/s', '_', $name);
	}

	/**
	* delete the cached data
	* 
	* @return --
	*/
	public function DeleteCache(){
		if(!$this->_config['caching']){return false;}
		$rfoldername	= (is_object($this->pfh)) ? $this->pfh->FolderPath('armory', 'cache') : 'data/';
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
		return ($binary) ? $input : 'data_'.$this->_config['locale'].md5($input);
	}

	/**
	* set the API Url
	* 
	* @param	$serverloc	the location of the server
	* @return --
	*/
	protected function setApiUrl($serverloc){
		$this->_config['apiUrl']				= str_replace('{region}', $serverloc, self::apiurl);
		$this->_config['apiRenderUrl']			= str_replace('{region}', $serverloc, self::staticrenderurl);
		$this->_config['apiTabardRenderUrl']	= str_replace('{region}', $serverloc, self::tabardrenderurl);
	}

	/**
	* Fetch the Data from URL
	* 
	* @param $url URL to Download
	* @return json
	*/
	protected function read_url($url) {
		$apikeyhead = (isset($this->_config['apiKeyPrivate']) && isset($this->_config['apiKeyPublic']) && $this->_config['apiKeyPrivate'] != '' && $this->_config['apiKeyPublic'] != '') ? $this->gen_api_header($url) : '';
		if(!is_object($this->puf)) {
			global $eqdkp_root_path;
			include_once($eqdkp_root_path.'core/urlfetcher.class.php');
			$this->puf = new urlfetcher();
		}
		return $this->puf->fetch($url, $apikeyhead);
	}

	private function gen_api_header($url){
		$date = date(DATE_RFC2822);
		$headers = array(
			'Date: '. $date,
			'Authorization: BNET '. $this->_config['apiKeyPublic'] .':'. base64_encode(hash_hmac('sha1', "GET\n{$date}\n{$url}\n", $this->_config['apiKeyPrivate'], true))
		);
		return $headers;
	}

	/**
	* Check if an error occured
	* 
	* @return error
	*/
	public function CheckError(){
		return ($this->error) ? $this->error : false;
	}

	/**
	* Check if the function imagelayereffect for GD is available
	* 
	* @return true/false
	*/
	private function checkImageLayerEffect(){
		$gdInfo		= gd_info();
		if (function_exists('imagelayereffect') && strpos($gdInfo['GD Version'], 'bundled')) {
			return true;
		}
	}

	/**
	* Loop over image, and colorize non transparent pixels.
	*
	* Simulates the following two lines:  (see above)
	*   imagelayereffect($image, IMG_EFFECT_OVERLAY);
	*   imagefilledrectangle($image,0,0,$emblem_size[0],$emblem_size[1],imagecolorallocatealpha($image, $color_r, $color_g, $color_b,0));
	*
	* Source: http://drupal.org/node/1154970
	*
	* @param Image $image
	* @param int $r_overlay
	* @param int $g_overlay
	* @param int $b_overlay
	*/
	private function imageColorize(&$image, $r_overlay, $g_overlay, $b_overlay) {
		$height		= imagesy($image);
		$width		= imagesx($image);

		for($y=0;$y<$height;$y++) {
			for($x=0;$x<$width;$x++) {
				$rgb		= imagecolorat($image, $x, $y);
				$alpha		= ($rgb >> 24) & 0xFF;
				$r_source	= ($rgb >> 16) & 0xFF;
				$g_source	= ($rgb >> 8) & 0xFF;
				$b_source	= $rgb & 0xFF;

				// Tweak this number if overlay looks weird.  (0 = Fully transparent, 127 = No transparancy)
				if ($alpha < 50) {
					if($r_source <= 128) {
						$final_r = (2 * $r_source * $r_overlay) / 256;
					}else{
						$final_r = 255 - (((255 - (2 * ($r_source - 128))) * (255 - $r_overlay)) / 256);
					}
					if($g_source <= 128) {
						$final_g = (2 * $g_source * $g_overlay) / 256;
					}else{
						$final_g = 255 - (((255 - (2 * ($g_source - 128))) * (255 - $g_overlay)) / 256);
					}
					if ($b_source <= 128) {
						$final_b = (2 * $b_source * $b_overlay) / 256;
					}else{
						$final_b = 255 - (((255 - (2 * ($b_source - 128))) * (255 - $b_overlay)) / 256);
					}
					$final_colour = imagecolorallocate($image, $final_r, $final_g, $final_b);
					imagesetpixel($image, $x, $y, $final_colour);
				}
			}
		}
	}
}
?>