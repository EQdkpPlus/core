<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2009
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2010 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 * 
 * $Id$
 */

 //infos about: itemstats(settings)

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class Game
{
	private $data			= array();
	private $gameinfo		= false;
	private $games			= array();
	private $class_colors	= array('fetched' => false);
	private $objects		= array();
	private $game			= '';

	public $obj				= array();

	//fill data with gameinfos (classes, races, factions, filters, etc.)
	public function __construct($install=false) {
		global $core, $eqdkp_root_path, $user, $pdl;
		if(!$install) {
			$this->game = $core->config['default_game'];
			$this->init_gameclass();
			$pdl->register_type('game');
		}
	}

	public function __destruct()
	{
		unset($data);
		unset($gameinfo);
		unset($games);
		unset($class_colors);
	}
	
	private function init_gameclass() {
		global $eqdkp_root_path, $user;
		include_once($eqdkp_root_path.'games/'.$this->game.'/'.$this->game.'.class.php');
		$this->gameinfo = new $this->game;
		$this->gameinfo->lang = (in_array($user->lang_name, $this->gameinfo->langs)) ? $user->lang_name : $this->gameinfo->langs[0];
		foreach($this->gameinfo->get_types() as $type) {
			$this->data[$type] = $this->gameinfo->get($type);
			$this->gameinfo->flush($type);
		}
		$this->gameinfo->flush($this->gameinfo->lang, true);
		return true;
	}

	public function get_importers($filter, $file=false){
		global $eqdkp_root_path;
		if($filter){
			if($file){
				return $eqdkp_root_path.'games/'.$this->get_game().'/import/'.$this->gameinfo->importers[$filter];
			}else{
				return (isset($this->gameinfo->importers[$filter])) ? true : false;
			}
		}else{
			return $this->gameinfo->importers;
		}
	}

	public function get_importAuth($myperm, $import){
		global $user;
		return ($user->check_auth($myperm, false) && $this->get_importers($import)) ? true : false;
	}

	//redirects to get_RenderImages defined in the specific game class or to the default below
	public function get_RenderImages($class_id=-1, $race_id=-1, $member_name='', $member_xml=false,$realm='',$options=false)
	{
		if(method_exists($this->gameinfo, 'get_RenderImages')) {
			return $this->gameinfo->get_RenderImages($class_id, $race_id, $member_name, $member_xml, $realm, $options);
		} elseif(in_array('3dmodel', $this->gameinfo->icons)) {
			return $this->_get_RenderImages($class_id, $race_id, $member_name, $member_xml, $realm, $options);
		} else {
			return '';
		}
	}

	/**
	 * Return Renderimages of a given member.
	 *
	 * @param integer $class_id
	 * @param integer $race_id
	 * @param string $member_name
	 * @return html string
	*/
	private function _get_RenderImages($class_id=-1, $race_id=-1, $member_name='', $member_xml=false,$realm='',$option=false)
	{
		global $pdh, $eqdkp_root_path, $core;
		$ret_val = false ;

		$img_folder = $eqdkp_root_path.'games/'.$this->game."/3dmodel/" ;

		if ( (($race_id == -1) or ($class_id == -1)) and (strlen($member_name) >1) )
		{
			$id = $pdh->get('member', 'id', $member_name);
			$race_id = $pdh->get('member', 'class_id', $id);
			$class_id = $pdh->get('member', 'race_id', $id);
		}

		$imgs = array();
		$gender = '0';
		if (get_Gender($member_name)=='Female') { #here we need an update!
			$gender = '1';
		}

		$imgs[] = $img_folder.$class_id."_".$race_id."_".$gender.'.jpg';

		foreach($imgs as $value)
		{
			if(file_exists($value))
			{
				$ret_val .= '<img src='.$value.'> &nbsp;&nbsp;' ;
			}
		}
		return 	$ret_val ;
	}

	/**
	 * Returns ImageTag with class-icon
	 *
	 * @param int $class_id
	 * @param bool $big
	 * @param bool $pathonly
	 * @return html string
	 */
	private function decorate_classes($class_id, $big=false, $pathonly=false)
	{
		global $eqdkp_root_path;
		if($big AND !in_array('classes_big', $this->gameinfo->icons)) {
			$big = false;
		}
		$icon_path = $eqdkp_root_path.'games/'.$this->game.'/classes/'.$class_id.(($big) ? '_b.png' : '.png');
		if($pathonly) {
			return $icon_path;
		}
		return "<img src='".$icon_path."' />";
	}

	/**
	 * Returns ImageTag with race-icon
	 *
	 * @param int $race_id
	 * @param bool $gender  (true for female)
	 * @param bool $pathonly
	 * @return html string
	 */
	private function decorate_races($race_id, $gender=false, $pathonly=false)
	{
		global $eqdkp_root_path;
		$icon_path = $eqdkp_root_path.'games/'.$this->game.'/races/'.$race_id.(($gender) ? 'f.png' : '.png');
		if($pathonly) {
			return $icon_path;
		}
		return "<img src='".$icon_path."' />";
	}

	/**
	 * Returns ImageTag with rank-icon
	 *
	 * @param int $rank_id
	 * @param int $size
	 * @param bool $pathonly
	 * @return html string
	 */
	private function decorate_ranks($rank_id, $size=16, $pathonly=false)
	{
		global $eqdkp_root_path;
		$icon_path = $eqdkp_root_path.'games/'.$this->game.'/ranks/'.$rank_id.'.png';
		if($pathonly) {
			return $icon_path;
		}
		return "<img src='".$icon_path."' width='".$size."' height='".$size."' />";
	}

	/**
	 * Returns ImageTag with talent-icon
	 *
	 * @param int $class_id
	 * @param int $talent
	 * @param bool $pathonly
	 * @return html string
	 */
	private function decorate_talents($class_id, $talent=0, $pathonly=false)
	{
		global $eqdkp_root_path;
		$icon_path = $eqdkp_root_path.'games/'.$this->game.'/talents/'.$class_id.$talent.'.png';
		if($pathonly) {
			return $icon_path;
		}
		return "<img src='".$icon_path."' />";
	}

	/**
	 * Returns ImageTag with event-icon
	 *
	 * @param string $icon
	 * @param bool $size
	 * @param bool $pathonly
	 * @return html string
	 */
	private function decorate_events($icon, $size=16, $pathonly=false)
	{
		global $eqdkp_root_path;
		$icon_path = $eqdkp_root_path.'games/'.$this->game.'/events/'.$icon;
		if($pathonly) {
			return $icon_path;
		}
		return "<img src='".$icon_path."' width='".$size."' height='".$size."' />";;
	}

	/**
	 * returns all available games
	 *
	 * @return array
	 */
	public function get_games()
	{
		global $eqdkp_root_path;
		if(empty($this->games)) {
			if ( $dir = opendir($eqdkp_root_path . 'games/') ) {
				while ( $game_name = @readdir($dir) ) {
					$cwd = $eqdkp_root_path . 'games/'.$game_name.'/'.$game_name.'.class.php'; // regenerate the link to the game
					if((@is_file($cwd)) && valid_folder($game_name)) {  // check if valid
						$this->games[] = $game_name;  // add to array
					}
				}
			}
		}
		return $this->games;
	}
	
	/**
	 * @return string full game-name
	 */
	public function game_name($tgame) {
		global $eqdkp_root_path;
		$name = '';
		if($tgame == $this->game) {
			$name = $this->glang($tgame);
		} else {
			include($eqdkp_root_path.'games/'.$tgame.'/'.$tgame.'.class.php');
			$cgame = new $tgame();
			$cgame->lang = (in_array($user->lang_name, $this->gameinfo->langs)) ? $user->lang_name : $this->gameinfo->langs[0];
			$name = $cgame->glang($tgame);
			unset($cgame);
		}
		return ($name) ? $name : $tgame;
	}

	/**
	 * returns current game
	 *
	 * @return string
	 */
	public function get_game()
	{
		return $this->game;
	}

	/**
	 * checks if the game has the given $type
	 *
	 * @param string $type
	 * @return bool
	 */
	public function type_exists($type)
	{
		if(in_array($type, $this->gameinfo->get_types())) {
			return true;
		}
		return false;
	}

	/**
	 * checks if the game has the given $icon
	 *
	 * @param string $icon
	 * @return bool
	 */
	public function icon_exists($icon)
	{
		if($this->gameinfo->icons AND in_array($icon, $this->gameinfo->icons)) {
			return true;
		}
		return false;
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
		global $pdl;
		$lang_var = $this->gameinfo->glang($var, $lang);
		if($lang_var !== NULL) {
			return $lang_var;
		} else {
			$pdl->log('game', 'Language variable "'.$var.'" not found in language "'.(($lang) ? $lang : $this->gameinfo->lang).'".');
			return false;
		}
	}

	/**
	 * calls a special function of the <game>.class.php
	 *
	 * @param string $function
	 * @return mixed
	 */
	public function callFunc($function_name, $params)
	{
		global $pdl;
		if(method_exists($this->gameinfo, $function_name)) {
			return call_user_func_array(array($this->gameinfo, $function_name), $params);
		}
		$pdl->log('game', 'Function "'.$fuction_name.'" does not exists.');
		return NULL;
	}

	/**
	 * creates an object, which is located in the games-folder
	 *
	 * @param string $classname
	 * @param string $index, the index you want to store the instance of the object in the object-array
	 * @param bool $force_new force a new object if true, else return index
	 * @param bool $overwrite_index
	 * @param array $params arguments the class may want
	 * @return false or index in obj-array
	 */
	public function new_object($classname, $index, $force_new=false, $overwrite_index=false, $params=NULL)
	{
		global $pdl;
		if(in_array($classname, $this->gameinfo->objects)) {
			if($force_new OR !($key = array_search($classname, $this->objects))) {
				if($overwrite_index OR !array_key_exists($index, $this->objects)) {
					$this->obj[$index] = $this->gameinfo->load_object($classname, $params);
					$this->objects[$index] = $classname;
					return $index;
				} else {
					$pdl->log('game', 'Index "'.$index.'" already exists.');
					return false;
				}
			} else {
				return $key;
			}
		}
		$pdl->log('game', 'Object "'.$classname.'" not found.');
		return false;
	}

	/**
	 * Loads class_colors from database
	 *
	 * @param string $type
	 * @param string or array $lang
	 * @return array
	 */
	private function load_class_color()
	{
		global $db, $user, $pdl;
		if(!$this->class_colors['fetched']) {
			$sql = "SELECT class_id, color FROM __classcolors WHERE template = '".$user->style['style_id']."';";
			$result = $db->query($sql);
			while ( $row = $db->fetch_record($result) ) {
				$this->class_colors[$row['class_id']] = $row['color'];
			}
			if(!$result) {
				$pdl->log('game', 'Unable to load class-colors.');
			} else {
				$this->class_colors['fetched'] = true;
			}
			$db->free_result($result);
		}
	}

	/**
	 * Returns available languages for current game
	 * @return array
	 */
	public function get_available_langs() {
		return $this->gameinfo->langs;
	}

	/**
	 * Returns class_color_code
	 *
	 * @param int $class_id
	 * @return hexcolorcode
	 */
	public function get_class_color($class_id)
	{
		$this->load_class_color();
		return ($this->class_colors[$class_id]) ? $this->class_colors[$class_id] :  '';
	}

	/**
	 * Returns whole array (such as filters, realmlist)
	 *
	 * @param string $type
	 * @param string or array $lang
	 * @return array
	 */
	public function get($type, $lang=false)
	{
		global $pdl;
		if(!$lang) {
			$lang = $this->gameinfo->lang;
		}
		if($this->type_exists($type)) {
			if(!$this->data[$type][$lang]) {
				$this->data[$type] = $this->gameinfo->get($type, $lang);
				$this->gameinfo->flush($type);
			}
			return $this->data[$type][$lang];
		}
		$pdl->log('game', 'Type "'.$type.'" does not exists');
		return false;
	}

	/**
	 * Returns ID of the Name of $type, while $type = classes, races, etc.
	 *
	 * @param string $type
	 * @param string $name
	 * @param bool $searched
	 * @return int
	 */
	public function get_id($type, $name, $searched=false)
	{
		global $pdl;
		if(!$this->type_exists($type)) {
			$pdl->log('game', 'Type "'.$type.'" does not exists');
			return false;
		}
		foreach ($this->data[$type] as $lang => $ids) {
			$langs[] = $lang;
			foreach($ids as $id => $typ) {
				if($typ == $name) {
					return $id;
				}
			}
		}
		if(!$searched) {
			foreach ($this->gameinfo->langs as $lang) {
				if(!in_array($lang, $langs)) {
					$langs2search[] = $lang;
				}
			}
			$this->data[$type] = $this->gameinfo->get($type, $langs2search);
			$this->gameinfo->flush($type);
			return $this->get_id($type, $name, true);
		}
		$pdl->log('game', 'No match, while searching in "'.$type.'" for "'.$name.'".');
		return false;
	}

	/**
	 * Returns Name of the ID of $type, while $type = classes, races, etc., in optional $lang
	 *
	 * @param string $type
	 * @param int $id
	 * @param string $lang
	 * @return string
	 */
	public function get_name($type, $id, $lang=false)
	{
		global $pdl;
		if(!$lang OR !in_array($lang, $this->gameinfo->langs)) {
			$lang = $this->gameinfo->lang;
		}
		if(!$this->type_exists($type)) {
			$pdl->log('game', 'Type "'.$type.'" does not exists.');
			return false;
		}
		if(!$this->data[$type][$lang][$id]) {
			$this->data[$type] = $this->gameinfo->get($type, $lang);
			$this->gameinfo->flush($type);
		}
		if(!empty($this->data[$type][$lang][$id])) {
			return $this->data[$type][$lang][$id];
		} else {
			$pdl->log('game', 'ID "'.$id.'" does not exists for type "'.$type.'".');
			return false;
		}
	}

	/**
	 * Redirects to decoration functions, or falls back to simple name
	 *
	 * @param string $icon
	 * @param array $params
	 * @return html string
	 */
	public function decorate($icon, $params)
	{
		$params =  (is_array($params)) ? $params : array($params);
		if($this->icon_exists($icon)) {
			if(method_exists($this->gameinfo, 'decorate_'.$icon)) {
				return call_user_func_array(array($this->gameinfo, 'decorate_'.$icon), $params);
			} elseif(method_exists($this, 'decorate_'.$icon)) {
				return call_user_func_array(array($this, 'decorate_'.$icon), $params);
			} elseif($this->type_exists($icon)) {
				if(!$this->data[$icon]) {
					$this->data[$icon] = $this->gameinfo->get($type);
					$this->gameinfo->flush($type);
				}
				return $this->get_name($type, $params[0]);
			}
		}
		return '';
	}

	/**
	 * Returns Version of current Game
	 *
	 */
	public function gameVersion()
	{
		return $this->gameinfo->version;
	}

	/**
	 * Add the profile fields for selected game
	 *
	 * @param string $newgame
	 * @param string $lang
	 */
	public function AddProfileFields($game){
		global $pm, $db, $eqdkp_root_path;
		$game_file = $eqdkp_root_path.'games/'.$game.'/field_data.php';
		$db->query('TRUNCATE TABLE __member_profilefields');
		if(is_file($game_file)){
			include($game_file);
			// Insert the field names in database
			if(is_array($xml_fields)){
				foreach($xml_fields as $name=>$values){
					$db->query("INSERT INTO __member_profilefields :params", array(
						'name'			=> $name,
						'fieldtype'		=> $values['type'],
						'category'		=> $values['category'],
						'language'		=> $values['name'],
						'size'			=> (intval($values['size']) > 0) ? intval($values['size']) : '0',
						'options'		=> (isset($values['option'])) ? serialize($values['option']) : '',
						'visible'		=> (isset($values['visible'])) ? intval($values['visible']) : '0',
						'image'			=> (isset($values['image'])) ? $values['image'] : '',
						'undeletable'	=> (($values['undeletable']) ? '1' : '0'),
						'enabled'		=> 1
					));
				}
			}
		}
	}

	/**
	 * Does DB-Updates for Game Changing
	 *
	 * @param string $newgame
	 * @param string $lang
	 */
	public function ChangeGame($newgame, $lang)
	{
		global $itt, $db, $eqdkp_root_path, $core, $settings, $pdl;
		unset($this->gameinfo);
		$this->game = $newgame;
		$this->init_gameclass();
		if(!in_array($lang, $this->gameinfo->langs)) {
			$lang = $this->gameinfo->langs[0];
		}
		$install = (defined('EQDKP_INSTALLED') && EQDKP_INSTALLED) ? false : true;
		$info = $this->gameinfo->get_OnChangeInfos($install);
		
		$game_config = array(
			'default_game'		=> $newgame,
			'game_language'		=> $lang,
			'game_version'		=> $this->gameinfo->version
			
		);
		if(!is_object($itt)) {
			include_once($eqdkp_root_path.'infotooltip/infotooltip.class.php');
			$itt = new infotooltip($settings, $pdl, $db, false, $eqdkp_root_path);
		}
		//infotooltip-config changes
		$itt_config = array(
			'infotooltip_use' => 0
		);
		if(in_array($newgame, $itt->get_supported_games())) {
			$itt_config['infotooltip_use'] = 1;
			$parserlist = $itt->get_parserlist($newgame);
			ksort($parserlist);
			reset($parserlist);
			$itt_config['itt_prio1'] = current($parserlist);
			$itt_config['itt_prio2'] = next($parserlist);
			unset($parserlist);
			$langlist = $itt->get_supported_languages($newgame);
			$itt_config['itt_langprio1'] = (in_array('en', $langlist)) ? 'en' : ((in_array('de', $langlist)) ? 'de' : ((in_array('fr', $langlist)) ? 'fr' : current($langlist)));
			$itt_config['itt_langprio2'] = (in_array('de', $langlist)) ? 'de' : ((in_array('fr', $langlist)) ? 'fr' : ((next($langlist)) ? current($langlist) : prev($langlist)));
			$itt_config['itt_langprio3'] = (in_array('fr', $langlist)) ? 'fr' : ((next($langlist)) ? current($langlist) : prev($langlist));
			unset($langlist);
		}
		$core->config_set(array_merge($game_config, $info['config'], $itt_config, $itt->changed_prio1($itt_config['itt_prio1'])));

		$queries = $info['aq'];

		//classcolors
		if(is_array($info['class_color']) AND $db->query("TRUNCATE __classcolors")) {
			$styles = $db->query("SELECT style_id FROM __styles;");
			$style_ids = array();
			while ( $row = $db->fetch_record($styles) ) {
				$style_ids[] = $row['style_id'];
			}
			$db->free_result($styles);
			foreach ($info['class_color'] as $class_id => $color) {
				foreach($style_ids as $id) {
					$queries[] = "INSERT INTO __classcolors (`template`, `class_id`, `color`) VALUES ('".$id."', '".$class_id."', '".$color."');";
				}
			}
		}
		if(is_array($queries)) {
			foreach($queries as $sql) {
				$db->query($sql);
			}
		}
		$this->AddProfileFields($newgame);
	}
}
?>