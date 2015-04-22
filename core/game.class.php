<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2015 EQdkp-Plus Developer Team
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU Affero General Public License as published
 *	by the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU Affero General Public License for more details.
 *
 *	You should have received a copy of the GNU Affero General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

 //infos about: itemstats(settings)

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class game extends gen_class {
	public static $shortcuts = array('itt' => 'infotooltip');
	
	protected $apiLevel		= 20;

	private $data			= array();
	private $games			= array();
	private $class_colors	= array('fetched' => false);
	private $objects		= array();
	private $game			= '';
	private $lang_name		= '';
	private $installer		= false;
	public $import_apikey	= false;
	private $deficon_path	= array(
								'roles'		=> 'images/roles/',
								'events'	=> 'images/events/',
							);
	public $obj				= array();
	private	$blnClassUpdateChecked = false;

	//fill data with gameinfos (classes, races, factions, filters, etc.)
	public function __construct($installer=false, $lang_name=''){
		if(!$installer){
			$this->lang_name		= $this->user->lang_name;
			$this->game				= $this->config->get('default_game');
			if($this->config->get('game_importer_apikey')){
				$this->import_apikey	= $this->config->get('game_importer_apikey');
			}
			$this->init_gameclass();
			$this->pdl->register_type('game');
		}
		if($installer){
			$this->installer	= true;
			$this->lang_name	= $lang_name;
			$this->game			= $this->config->get('default_game');
		}
	}

	/**
	 * Returns the gameobject
	 *
	 * @return object
	 */
	private function gameinfo() {
		return registry::register($this->game);
	}

	/**
	 * Init the game class
	 *
	 * @return string
	 */
	private function init_gameclass(){
		include_once($this->root_path.'games/'.$this->game.'/'.$this->game.'.class.php');
		if(!class_exists($this->game)) {
			$this->game = 'dummy';
			include_once($this->root_path.'games/'.$this->game.'/'.$this->game.'.class.php');
			$this->pdl->log('game', 'Tried to initialize undefined game \''.$this->game.'\', default to game \''.$this->game.'\'.');
			return $this->init_gameclass();
		} else {
			//Check API Level
			$strClassname = $this->game;
			$intAPILevel = $strClassname::getApiLevel();
			if (!$intAPILevel || $intAPILevel < $this->apiLevel-2){
				//Default to dummy, API is deprecated
				$this->game = 'dummy';
				include_once($this->root_path.'games/'.$this->game.'/'.$this->game.'.class.php');
				$this->pdl->log('game', 'The Game API Level of the Game \''.$this->game.'\' is too old ('.$intAPILevel.' vs. '.$this->apiLevel.'), defaulted to dummy game.');
				$this->config->set('default_game', $this->game);
			} elseif ($intAPILevel < $this->apiLevel) {
				$this->pdl->log('game', 'The Game API Level of the Game \''.$this->game.'\' should be updated ('.$intAPILevel.' vs. '.$this->apiLevel.')');
			}
		}
		
		// check for selected language
		if(!in_array($this->lang_name, $this->gameinfo()->langs)) {
			// file in selected language available but not listed, add it to the list
			if(file_exists($this->root_path.'games/'.$this->game.'/language/'.$this->lang_name.'.php')) $this->gameinfo()->langs[] = $this->lang_name;
			// file for selected language not available, revert to default language
			else $this->lang_name = $this->gameinfo()->langs[0];
		}
		$this->gameinfo()->lang = $this->lang_name;
		foreach($this->gameinfo()->get_types() as $type) {
			$this->data[$type] = $this->gameinfo()->get($type);
			$this->gameinfo()->flush($type);
		}
		$this->gameinfo()->flush($this->gameinfo()->lang, true);

		return true;
	}

	/**
	 * Returns global Character Tooltip
	 *
	 * @param int $intCharID
	 * @return string
	 */
	public function chartooltip($intCharID){
		$tt = "";
		
		if ($this->type_exists('chartooltip')){
			$tt = $this->gameinfo()->chartooltip($intCharID);		
		}
		
		if ($this->hooks->isRegistered('game_chartooltip')){
			$hook_tt = $this->hooks->process('game_chartooltip', array('tooltip' => $tt, 'char_id' => $intCharID), true);
			$tt = $hook_tt['tooltip'];
		}
		
		return str_replace($this->root_path, register('env')->link, $tt);
	}

	/**
	 * Returns per game ImageTag for classes
	 *
	 * @param int $class_id
	 * @param bool $big
	 * @param bool $pathonly
	 * @return html string
	 */
	private function decorate_classes($class_id, $profile=array(), $size=16, $pathonly=false){	
		if(is_file($this->root_path.'games/'.$this->game.'/icons/classes/'.$class_id.'.png')){
			$icon_path = $this->server_path.'games/'.$this->game.'/icons/classes/'.$class_id.'.png';
			return ($pathonly) ? $icon_path : '<img src="'.$icon_path.'" height="'.$size.'"  alt="class '.$class_id.'" class="'.$this->game.'_classicon classicon'.'" title="'.$this->get_name('classes', $class_id).'" />';
		}
		return false;
	}

	/**
	 * Returns per game ImageTag for races
	 *
	 * @param int $race_id
	 * @param bool $gender  (true for female)
	 * @param bool $pathonly
	 * @return html string
	 */
	private function decorate_races($race_id, $profile=array(), $size=16, $pathonly=false){
		$gender = (isset($profile['gender'])) ? $profile['gender'] : '';
		if (strtolower($gender) == "female" && is_file($this->root_path.'games/'.$this->game.'/icons/races/'.$race_id.'f.png')){
			$icon_path = $this->root_path.'games/'.$this->game.'/icons/races/'.$race_id.'f.png';
		} else {
			$icon_path = $this->root_path.'games/'.$this->game.'/icons/races/'.$race_id.'.png';
		}

		if(is_file($icon_path)){
			$icon_path = str_replace($this->root_path, $this->server_path, $icon_path);
			return ($pathonly) ? $icon_path : "<img src='".$icon_path."' height='".$size."' alt='race ".$race_id."' class=\"".$this->game."_raceicon raceicon\" title=\"".$this->get_name('races', $race_id)."\" />";
		}
		return false;
	}

	/**
	 * Returns per game ImageTag for ranks
	 *
	 * @param int $rank_id
	 * @param int $size
	 * @param bool $pathonly
	 * @return html string
	 */
	private function decorate_ranks($rank_id, $profile=array(), $size=16, $pathonly=false){	
		$strRankIcon = $this->pdh->get('rank', 'icon', array($rank_id));
		
		if(strlen($strRankIcon) && is_file($this->root_path.'games/'.$this->game.'/icons/ranks/'.$strRankIcon)){
			$icon_path = $this->server_path.'games/'.$this->game.'/icons/ranks/'.$strRankIcon;
			return ($pathonly) ? $icon_path : "<img src='".$icon_path."' height='".$size."' alt='rank ".$rank_id."' class=\"".$this->game."_rankicon rankicon\" />";
		}
	}

	/**
	 * Returns per game ImageTag for talents
	 *
	 * @param int $class_id
	 * @param int $talent
	 * @param bool $pathonly
	 * @return html string
	 */
	private function decorate_talents($talent_id, $profile=array(), $size=16, $pathonly=false){	
		if(is_file($this->root_path.'games/'.$this->game.'/icons/talents/'.$talent_id.'.png')){
			$icon_path = $this->server_path.'games/'.$this->game.'/icons/talents/'.$talent_id.'.png';
			return ($pathonly) ? $icon_path : "<img src='".$icon_path."' alt='' class=\"".$this->game."_talenticon talenticon\" />";
		}
	}

	/**
	 * Returns per game ImageTag for events
	 *
	 * @param string $icon
	 * @param bool $size
	 * @param bool $pathonly
	 * @return html string
	 */
	private function decorate_events($event_id, $profile=array(), $size=20, $pathonly=false){			
		if(is_file($this->pfh->FolderPath("event_icons", "files").$this->pdh->get('event', 'icon', array($event_id)))){			
			$icon_path = $this->pfh->FolderPath("event_icons", "files", "serverpath").$this->pdh->get('event', 'icon', array($event_id));
			return ($pathonly) ? $icon_path : "<img src='".$icon_path."' height='".$size."' alt='eventicon".$event_id."' class=\"".$this->game."_eventicon eventicon\"/>";
		}elseif(is_file($this->root_path.'games/'.$this->game.'/icons/events/'.$this->pdh->get('event', 'icon', array($event_id)))){
			$icon_path = $this->server_path.'games/'.$this->game.'/icons/events/'.$this->pdh->get('event', 'icon', array($event_id));
			return ($pathonly) ? $icon_path : "<img src='".$icon_path."' height='".$size."' alt='eventicon".$event_id."' class=\"".$this->game."_eventicon eventicon\"/>";
		}
	}
	
	/**
	 * Returns default ImageTag for events
	 *
	 * @param string $icon
	 * @param bool $size
	 * @param bool $pathonly
	 * @return html string
	 */
	private function decorate_def_events($event_id, $profile=array(), $size=20, $pathonly=false){	
		if(is_file($this->root_path.$this->deficon_path['events'].$this->pdh->get('event', 'icon', array($event_id)))){
			$icon_path = $this->server_path.$this->deficon_path['events'].$this->pdh->get('event', 'icon', array($event_id));
			return ($pathonly) ? $icon_path : "<img src='".$icon_path."' height='".$size."' alt='eventicon".$event_id."' class=\"".$this->game."_eventicon eventicon\"/>";
		}
	}
	
	/**
	 * Returns per game ImageTag for roles
	 *
	 * @param int $role_id
	 * @param int $class_id
	 * @param int $size
	 * @param bool $pathonly
	 * @return html string
	 */
	private function decorate_roles($role_id, $profile=array(), $size=20, $pathonly=false){	
		if(is_file($this->root_path.'games/'.$this->game.'/icons/roles/'.$role_id.'.png')){
			$icon_path = $this->server_path.'games/'.$this->game.'/icons/roles/'.$role_id.'.png';
			return ($pathonly) ? $icon_path : "<img src='".$icon_path."' height='".$size."' alt='' class=\"".$this->game."_roleicon roleicon\"/>";
		}
		return false;
	}

	/**
	 * Returns default ImageTag for roles
	 *
	 * @param int $role_id
	 * @param int $class_id
	 * @param int $size
	 * @param bool $pathonly
	 * @return html string
	 */
	private function decorate_def_roles($role_id, $profile=array(), $size=20, $pathonly=false){	
		if(is_file($this->root_path.$this->deficon_path['roles'].$role_id.'.png')){
			$icon_path = $this->server_path.$this->deficon_path['roles'].$role_id.'.png';
			return ($pathonly) ? $icon_path : "<img src='".$icon_path."' height='".$size."' alt='' class=\"".$this->game."_roleicon roleicon\"/>";
		}
	}

	/**
	 * Returns image for generic type
	 *
	 * @param int $class_id
	 * @param bool $big
	 * @param bool $pathonly
	 * @return html string
	 */
	private function decorate_generic($type, $id, $profile=array(), $size=16, $pathonly=false){
		if(is_file($this->root_path.'games/'.$this->game.'/icons/'.$type.'/'.$id.'.png')){
			$icon_path = $this->server_path.'games/'.$this->game.'/icons/'.$type.'/'.$id.'.png';
			return ($pathonly) ? $icon_path : '<img src="'.$icon_path.'" height="'.$size.'"  alt="'.$type.' '.$id.'" class="'.$this->game.'_'.$type.'icon gameicon '.$type.'icon" title="'.$this->get_name($type, $id).'" />';
		}
		return false;
	}
	
	/**
	 * get unique ID settings of Game. If set, chars will be identified by charname AND the given Profilesettings
	 */
	public function get_char_unique_ids(){
		return (isset($this->gameinfo()->character_unique_ids)) ? ($this->gameinfo()->character_unique_ids) : false;
	}

	/**
	 * get available importers (p.e. wow armory)
	 *
	 * @param int $filter
	 * @param bool $file
	 * @return array
	 */
	public function get_importers($filter, $file=false){
		if($filter){
			if($file){
				return $this->server_path.'games/'.$this->get_game().'/import/'.$this->gameinfo()->importers[$filter];
			}else{
				return (isset($this->gameinfo()->importers[$filter])) ? ($this->gameinfo()->importers[$filter]) : false;
			}
		}else{
			return $this->gameinfo()->importers;
		}
	}

	public function get_import_apikey(){
		return $this->import_apikey;
	}

	/**
	 * Check the auth for importers
	 *
	 * @param str $myperm
	 * @param str $import
	 * @return bool
	 */
	public function get_importAuth($myperm, $import){
		return ($this->user->check_auth($myperm, false) && $this->get_importers($import)) ? true : false;
	}

	public function get_require_apikey(){
		$setting_apikey		= $this->config->get('game_importer_apikey');
		$apikey_config		= $this->get_importers('apikey');
		return ($apikey_config['status'] == 'required' && empty($setting_apikey)) ? true : false;
	}

	/**
	 * returns all available games
	 *
	 * @return array
	 */
	public function get_games(){
		if(empty($this->games)) {
			if ( $dir = opendir($this->root_path . 'games/') ) {
				while ( $game_name = @readdir($dir)) {
					if (!valid_folder($game_name)) continue;
 					$cwd = $this->root_path . 'games/'.$game_name.'/'.$game_name.'.class.php';	// regenerate the link to the game
					if(valid_folder($game_name) && (@is_file($cwd))){							// check if valid
						$this->games[] = $game_name;											// add to array
					}
				}
			}
		}
		return $this->games;
	}
	
	/**
	 * returns an array containing versions of all games
	 */
	public function get_versions() {
		$versions = array();
		foreach($this->get_games() as $gme) {
			if(!class_exists($gme)) include_once($this->root_path . 'games/'.$gme.'/'.$gme.'.class.php');
			$object = registry::register($gme);
			$versions[$gme] = $object->version;
			unset($object);
		}
		return $versions;
	}
	
	/**
	 * returns an array containing versions of all games
	 */
	public function get_authors() {
		$authors = array();
		foreach($this->get_games() as $gme) {
			if(!class_exists($gme)) include_once($this->root_path . 'games/'.$gme.'/'.$gme.'.class.php');
			$object = registry::register($gme);
			$authors[$gme] = $object->author;
			unset($object);
		}
		return $authors;
	}

	/**
	 * @return string full game-name
	 */
	public function game_name($tgame) {
		$name = '';
		if($tgame == $this->game) {
			$name = $this->glang($tgame);
		}else{
			include_once($this->root_path.'games/'.$tgame.'/'.$tgame.'.class.php');
			$cgame			= registry::register($tgame);
			$cgame->lang	= (in_array($this->lang_name, $cgame->langs)) ? $this->lang_name : $cgame->langs[0];
			$name			= $cgame->glang($tgame);
			unset($cgame);
		}
		return ($name) ? $name : $tgame;
	}

	/**
	 * returns current game
	 *
	 * @return string
	 */
	public function get_game(){
		return $this->game;
	}

	/**
	 * checks if the game has the given $type
	 *
	 * @param string $type
	 * @return bool
	 */
	public function type_exists($type){
		if(in_array($type, $this->gameinfo()->get_types())) {
			return true;
		}
		return false;
	}

	/**
	 * checks if the game provides an icon for a given $type
	 *
	 * @param string $type
	 * @return bool
	 */
	public function icon_exists($type) {
		return in_array($type, $this->gameinfo()->icons); 
	}
	
	/**
	 * checks if there are default icons
	 *
	 * @param string $icon
	 * @return bool
	 */
	public function default_icons($icon){
		return isset($this->deficon_path[$icon]) ? $this->deficon_path[$icon] : false;
	}
	
	/**
	 * Returns language var
	 *
	 * @param string $var
	 * @param string $lang
	 * @return string
	 */
	public function glang($var, $return_key=false, $lang=false, $exists=false){
		$lang_var = $this->gameinfo()->glang($var, $return_key, $lang);
		if($lang_var !== NULL) {
			return $lang_var;
		} else {
			if($exists) return false;
			$this->pdl->log('game', 'Language variable "'.$var.'" not found in language "'.(($lang) ? $lang : $this->gameinfo()->lang).'".');
			return false;
		}
	}

	/**
	 * calls a special function of the <game>.class.php
	 *
	 * @param string $function
	 * @return mixed
	 */
	public function callFunc($function_name, $params=array()){
		if(method_exists($this->gameinfo(), $function_name)) {
			return call_user_func_array(array($this->gameinfo(), $function_name), $params);
		}
		$this->pdl->log('game', 'Function "'.$function_name.'" does not exists.');
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
	public function new_object($classname, $index, $params=NULL, $force_new=false, $overwrite_index=false){
		if(in_array($classname, $this->gameinfo()->objects)) {
			if($force_new OR !($key = array_search($classname, $this->objects))) {
				if($overwrite_index OR !array_key_exists($index, $this->objects)) {
					$this->obj[$index] = $this->gameinfo()->load_object($classname, $params);
					$this->objects[$index] = $classname;
					return $index;
				} else {
					$this->pdl->log('game', 'Index "'.$index.'" already exists.');
					return false;
				}
			} else {
				return $key;
			}
		}
		$this->pdl->log('game', 'Object "'.$classname.'" not found.');
		return false;
	}

	/**
	 * Loads class_colors from database
	 *
	 * @param string $type
	 * @param string or array $lang
	 * @return array
	 */
	private function load_class_color($style_id = false){
		$style_id = (!$style_id) ? $this->user->style['style_id'] : $style_id;
	
		if(!isset($this->class_colors[$style_id]['fetched']) || $this->class_colors[$style_id]['fetched'] == false) {
			$this->class_colors[$style_id] = $this->pdh->get('class_colors', 'class_colors', array($style_id));
			$classes = $this->get_primary_classes(array('id_0'));
			if (count($this->class_colors[$style_id]) != count($classes)){
				$arrGameColors = $this->gameinfo()->get_class_colors();
				foreach($classes as $key => $val){
					if (!isset($this->class_colors[$style_id][$key]) && isset($arrGameColors[$key])){
						$this->pdh->put('class_colors', 'add_classcolor', array($style_id, $key, $arrGameColors[$key]));
					}
				}
			}
						
			if(!is_array($this->class_colors[$style_id])) {
				$this->pdl->log('game', 'Unable to load class-colors.');
			} else {
				$this->class_colors[$style_id]['fetched'] = true;
			}
		}
	}

	/**
	 * Returns available languages for current game
	 * @return array
	 */
	public function get_available_langs() {
		return $this->gameinfo()->langs;
	}

	/**
	 * Returns class_color_code
	 *
	 * @param int $class_id
	 * @return hexcolorcode
	 */
	public function get_class_color($class_id, $style_id = false){
		$style_id = (!$style_id) ? $this->user->style['style_id'] : $style_id;
		$this->load_class_color($style_id);
		return (isset($this->class_colors[$style_id][$class_id])) ? $this->class_colors[$style_id][$class_id] :  '';
	}

	/**
	 * Filter data
	 *
	 * @param string $data
	 * @param array $filter, possible values ('id_0')
	 * @return array
	 */
	private function filter($filters, $data) {
		foreach($filters as $filter) {
			if(is_array($filter)) {
				foreach($data as $id => $dat) {
					if(!in_array($id, $filter)) {
						unset($data[$id]);
					}
				}
			} else {
				switch($filter) {
					case 'id_0': 
						unset($data[0]); break;

					default: break;
				}
			}
		}
		return $data;
	}

	/**
	 * Returns whole array (such as filters, realmlist)
	 *
	 * @param string $type
	 * @param array $filter, possible values ('id_0')
	 * @param string $lang
	 * @return array
	 */
	public function get($type, $filter=array(), $lang=false){
		if($type == 'primary') $type = $this->get_primary_class();
		if(!$lang) $lang = $this->gameinfo()->lang;
		if(!is_array($filter) AND $filter) $filter = array($filter);
		if($this->type_exists($type)) {
			if(!isset($this->data[$type][$lang])) {
				$this->data[$type] = $this->gameinfo()->get($type, $lang);
				$this->gameinfo()->flush($type);
			}
			if(isset($this->data[$type][$lang])) {
				$to_return = $this->data[$type][$lang];
				if(count($filter) > 0) {
					$to_return = $this->filter($filter, $to_return);
				}
				return $to_return;
			}
		}
		$this->pdl->log('game', 'Type "'.$type.'" does not exists');
		return false;
	}
	
	/**
	 * Returns name or type for the primary class (necessary in e.g. calendar, role assignment)
	 *
	 * @param 	boolean		$returnName:	return name (true) or type (false)
	 * @return 	string
	 */
	public function get_primary_class($returnName = false) {
		if(!$this->blnClassUpdateChecked){
			$this->autoUpdateClassProfileFields();
			$this->blnClassUpdateChecked = true;
		}
		
		$class_dep = $this->gameinfo()->get_class_dependencies();
		foreach($class_dep as $class) {
			if(isset($class['primary']) && $class['primary']) {
				#pd($class);
				return ($returnName) ? $class['name'] : $class['type'];
			}
		}
	}	
	
	/**
	 * Returns selectable data (respecting e.g. faction selection of admin) for the primary class
	 *
	 * @param 	string 	$selection:	ID of the parent-class
	 * @param 	array 	$filter:	possible values ('id_0')
	 * @return 	string
	 */
	public function get_primary_classes($filter=array(), $lang=false) {
		$admin_data = $this->get_admin_classdata(true);
		$primary = $this->get_primary_class();
		$todisplay = array_keys($admin_data);
		$todisplay[] = $primary;
		
		$ids = $this->get_assoc_classes($todisplay, $filter, $lang);
		$ids = $ids['data'];
		reset($todisplay);
		$type = current($todisplay);
		while($type != $primary) {
			if(!isset($ids[$admin_data[$type]])) {
				return $this->get($primary, $filter, $lang);
			}
			$ids = $ids[$admin_data[$type]];
			$type = next($todisplay);
		}
		return $this->get($primary, array($ids), $lang);
	}
	
	/**
	 * Returns the type for the name, e.g. "classes" for name "class"
	 * 
	 * @param string $strName
	 * @return string
	 */
	public function get_type_for_name($strName){
		$class_dep = $this->gameinfo()->get_class_dependencies();
		foreach($class_dep as $class) {
			if ($class['name'] == $strName) return $class['type'];
		}
		return false;
	}
	
	/**
	 * Returns the name for the type, e.g. "class" for name "classes"
	 *
	 * @param string $strName
	 * @return string
	 */
	public function get_name_for_type($strType){
		$class_dep = $this->gameinfo()->get_class_dependencies();
		foreach($class_dep as $class) {
			if ($class['type'] == $strType) return $class['name'];
		}
		return false;
	}
	
	/**
	 * Returns an array with all possible dependent classes
	 *
	 * @param 	string	$parent:	Fieldname of the parent class
	 * @param 	string 	$child:		Fieldname of child class
	 * @param 	string 	$selection:	ID of the parent-class
	 * @param 	array 	$filter:	possible values ('id_0')
	 * @param 	string	$lang
	 * @return 	array
	 */
	public function get_dep_classes($parent, $child, $selection, $filter=array(), $lang=false) {
		$class_dep = $this->gameinfo()->get_class_dependencies();
		foreach($class_dep as $class) {
			if($class['name'] == $child) {
				break;
			}
		}
		$class['parent'][$parent][$selection];
		$all_data = $this->get($class['type'], $filter, $lang);
		if(empty($class['parent'][$parent][$selection]) || $class['parent'][$parent][$selection] == 'all') {
			return $all_data;
		}
		foreach($all_data as $id => $name) {
			if(!in_array($id, $class['parent'][$parent][$selection])) {
				unset($all_data[$id]);
			}
		}
		return $all_data;
	}
	
	/**
	 * Returns the class-dependencies as an associative array for the roster
	 *
	 * @param 	array	$todisplay:		array containing the types which shall be contained in the associative array
	 * @param 	array 	$filter:	possible values ('id_0')
	 * @param 	string	$lang
	 * @return 	array
	 */
	public function get_roster_classes($filter=array(), $lang=false) {
		$class_dep = $this->gameinfo()->get_class_dependencies();
		// gather all classes for roster
		$todisplay = array();
		foreach($class_dep as $class) {
			if(isset($class['roster']) && $class['roster']) $todisplay[] = $class['type'];
		}
		return $this->get_assoc_classes($todisplay, $filter, $lang);
	}
	
	/**
	 * Returns the class-dependencies as an associative array for the recruitment module
	 *
	 * @param 	array	$todisplay:		array containing the types which shall be contained in the associative array
	 * @param 	array 	$filter:	possible values ('id_0')
	 * @param 	string	$lang
	 * @return 	array
	 */
	public function get_recruitment_classes($filter=array(), $lang=false) {
		$class_dep = $this->gameinfo()->get_class_dependencies();
		// gather all classes for roster
		$todisplay = array();
		foreach($class_dep as $class) {
			if(isset($class['recruitment']) && $class['recruitment']) $todisplay[] = $class['type'];
		}
		return $this->get_assoc_classes($todisplay, $filter, $lang);
	}
	
	/**
	 * Returns the class-dependencies as an associative array
	 *
	 * @param 	array	$todisplay:		array containing the types which shall be contained in the associative array
	 * @param 	array 	$filter:	possible values ('id_0')
	 * @param 	string	$lang
	 * @return 	array
	 */
	public function get_assoc_classes($todisplay, $filter=array(), $lang=false, $blnWithAdminData=true) {
		/*
		 *	a fully linear dependency for all classes with flag roster is assumed, ordering has to be correct beforehand
		 *	means, e.g. faction => race => class => talent
		 */
		$class_dep = $this->gameinfo()->get_class_dependencies();
		$admin_data = ($blnWithAdminData) ? $this->get_admin_classdata(true) : array();
		
		// create a name 2 type array
		$name2type = array();
		foreach($class_dep as $class) {
			$name2type[$class['name']] = $class['type'];
		}
		// get all single dependencies
		$relevant_deps = array();
		$child_ids = array();
		foreach($class_dep as $class) {
			if($class['parent'] && isset($name2type[key($class['parent'])])) {
				$relevant_deps[$name2type[key($class['parent'])]] = $class['type'];
				$child_ids[$name2type[key($class['parent'])]] = current($class['parent']);
			}
		}
		
		if (count($relevant_deps) === 0){
			return array(
				'todisplay'	=> $todisplay,
				'data'		=> array_keys($this->get($todisplay[0], $filter, $lang)),
			);
		}
		
		// build associative array
		return array(
			'todisplay'	=> $todisplay,
			'data'		=> $this->build_assoc_array(key($relevant_deps), $relevant_deps, $child_ids, $todisplay, $filter, $lang, false, $admin_data)
		);
	}
	
	private function build_assoc_array($type, $dep_order, $child_ids, $todisplay, $filter=array(), $lang=false, $parent_id=false, $admin_data=array()) {
		//echo "build assoc array ".$type."<br />";
		
		$assoc_array = array();
		$data = $this->get($type, $filter, $lang);

		foreach($data as $id => $name) {
			//echo "foreach ".$type." id ".$id."<br />";
			if($id === "" || $id === "_select") continue;
			
			if (isset($admin_data[$type])){
				if ($id !== $admin_data[$type] && strlen($admin_data[$type])){
					//echo "Admin data and not in ".$type."<br />";
					continue;
				}
			}
			
			// filter out ids not allowed
			if($parent_id !== false) {
				//echo "parent_id filter out ".$type."<br />";
				$true_ids = $child_ids[array_search($type, $dep_order)][$parent_id];
				if(!((!is_array($true_ids) && $true_ids == 'all') || in_array($id, $true_ids)))
					continue;
			}
			// last "level" reached
			if($dep_order[$type] == end($todisplay)) {
				//echo "last level ".$type." id ".$id." end ".end($todisplay)."<br />";
				if($child_ids[$type][$id] == 'all') {
					$child_ids[$type][$id] = array_keys($this->get($dep_order[$type], $filter, $lang));
				}
				if(in_array($type, $todisplay)) {
					$assoc_array[$id.'_'] = $child_ids[$type][$id];
				} else {
					$assoc_array = array_unique(array_merge($assoc_array, $child_ids[$type][$id]));
				}
			} else {
				//echo "not level ".$type." id ".$id." end ".end($todisplay)."<br />";
				if(in_array($type, $todisplay)) {
					$assoc_array[$id.'_'] = $this->build_assoc_array($dep_order[$type], $dep_order, $child_ids, $todisplay, $filter, $lang, $id, $admin_data);
				} else {

					if(in_array($dep_order[$type], $todisplay)) {
						//d($assoc_array);
						$assoc_array = $assoc_array + $this->build_assoc_array($dep_order[$type], $dep_order, $child_ids, $todisplay, $filter, $lang, $id, $admin_data);
						//echo "Vereinigung ".$type." mit ".$dep_order[$type]."id ".$id." end ".end($todisplay)."<br />";
					} else {
						//echo "Merge ".$type." mit ".$dep_order[$type]."id ".$id." end ".end($todisplay)."<br />";
		
						$assoc_array = array_merge($assoc_array, $this->build_assoc_array($dep_order[$type], $dep_order, $child_ids, $todisplay, $filter, $lang, $id, $admin_data));
						if(!is_array(current($assoc_array))) $assoc_array = array_unique($assoc_array);
						//echo "Merge ".$type." mit ".$dep_order[$type]."id ".$id." end ".end($todisplay)."<br />";
						
						//d($assoc_array);
					}
				}
			}
		}

		return $assoc_array;
	}

	
	/**
	 * Returns a data-array containing all admin-selected class stuff
	 *
	 * @return array
	 */
	public function get_admin_classdata($type_key=false) {
		$class_dep = $this->gameinfo()->get_class_dependencies();
		$data = array();
		foreach($class_dep as $class) {
			if(isset($class['admin']) && $class['admin']) {
				$data[$class[($type_key ? 'type' : 'name')]] = $this->config->get($class['name']);
			}
		}
		return $data;
	}

	/**
	 * Returns ID of the Name of $type, while $type = classes, races, etc.
	 *
	 * @param string $type
	 * @param string $name
	 * @param bool $searched
	 * @return int
	 */
	public function get_id($type, $name, $searched=false){
		if($type == 'primary') $type = $this->get_primary_class();
		if(!$this->type_exists($type)) {
			$this->pdl->log('game', 'Type "'.$type.'" does not exists');
			return false;
		}
		$name = trim($name);
		foreach ($this->data[$type] as $lang => $ids) {
			$langs[] = $lang;
			foreach($ids as $id => $typ) {
				if(strcasecmp($typ, $name) === 0) {
					return $id;
				}
			}
		}
		if(!$searched) {
			foreach ($this->gameinfo()->langs as $lang) {
				if(!in_array($lang, $langs)) {
					$langs2search[] = $lang;
				}
			}
			$this->data[$type] = $this->gameinfo()->get($type, $langs2search);
			$this->gameinfo()->flush($type);
			return $this->get_id($type, $name, true);
		}
		$this->pdl->log('game', 'No match, while searching in "'.$type.'" for "'.$name.'".');
		return 0;
	}

	/**
	 * Returns Name of the ID of $type, while $type = classes, races, etc., in optional $lang
	 *
	 * @param string $type
	 * @param int $id
	 * @param string $lang
	 * @return string
	 */
	public function get_name($type, $id, $lang=false){
		if($type == 'primary') $type = $this->get_primary_class();
		if(!$lang OR !in_array($lang, $this->gameinfo()->langs)) {
			$lang = $this->gameinfo()->lang;
		}
		if(!$this->type_exists($type)) {
			$this->pdl->log('game', 'Type "'.$type.'" does not exists.');
			return false;
		}
		if(!isset($this->data[$type][$lang][$id])) {
			$this->data[$type] = $this->gameinfo()->get($type, $lang);
			$this->gameinfo()->flush($type);
		}
		if(!empty($this->data[$type][$lang][$id])) {
			return $this->data[$type][$lang][$id];
		} else {
			$this->pdl->log('game', 'ID "'.$id.'" does not exists for type "'.$type.'".');
			return false;
		}
	}

	/**
	 * Redirects to decoration functions, or falls back to simple name
	 *
	 * @param string $type
	 * @param array $params
	 * @return html string
	 */
	public function decorate($type, $id, $profile=array(), $size=16, $pathonly=false, $allow_empty=true){
		if($type == 'primary') $type = $this->get_primary_class();
		if($this->icon_exists($type)) {
			if(method_exists($this->gameinfo(), 'decorate_'.$type)) {
				return call_user_func_array(array($this->gameinfo(), 'decorate_'.$type), array($id, $profile, $size, $pathonly));
			} elseif(method_exists($this, 'decorate_'.$type)) {
				return call_user_func_array(array($this, 'decorate_'.$type), array($id, $profile, $size, $pathonly));
			} else {
				return $this->decorate_generic($type, $id, $profile, $size, $pathonly);
			}
		// there are no game specific icons, check if there are default ones
		}elseif($this->default_icons($type)){
			return call_user_func_array(array($this, 'decorate_def_'.$type), array($id, $profile, $size, $pathonly));
		} elseif($this->type_exists($type) && !$allow_empty) {
			if(!$this->data[$type]) {
				$this->data[$type] = $this->gameinfo()->get($type);
				$this->gameinfo()->flush($type);
			}
			return $this->get_name($type, $id);
		}
		return '';
	}
	
	/**
	 * Creates all Icons of classes/subclasses to decorate a character
	 *
	 * @param int 	$char_id
	 * @return html string
	 */
	public function decorate_character($char_id, $size=20) {
		$class_dep = $this->gameinfo()->get_class_dependencies();
		$decor = '';
		foreach($class_dep as $class) {
			if(isset($class['decorate']) && $class['decorate']) {
				$fields = $this->pdh->get('member', 'profiledata', array($char_id));
				if(isset($fields[$class['name']])){
					$decor .= ' '.$this->decorate($class['type'], $fields[$class['name']], $fields, $size);
				}
			}
		}
		return $decor;
	}

	/**
	 * Returns Version of current Game
	 *
	 */
	public function gameVersion(){
		return $this->gameinfo()->version;
	}
	
	private function autoUpdateClassProfileFields(){
		// add fields for classes/subclasses etc
		$class_data = $this->gameinfo()->get_class_dependencies();
		
		$strClassdataHash = md5(serialize($class_data));
		if($strClassdataHash === $this->config->get('game_classdata_hash')){
			return;
		}
		
		// build an array with parent-name , child-name => child-type
		$class_deps = array();
		foreach($class_data as $class) {
			if(!is_array($class['parent'])) continue;
			foreach($class['parent'] as $parent => $ids) {
				$class_deps[$parent][$class['name']] = $class['type'];
			}
		}
		$z = 0;
		foreach($class_data as $class) {
			if($class['admin']) {
				// make it a hidden field
				$field = array(
						'name'			=> $class['name'],
						'type'			=> 'hidden',
						'lang'			=> 'uc_'.$class['name'],
						'undeletable'	=> 1,
						'category'		=> 'character',
						'no_custom'		=> true,
						'options_language'=> $class['type'],
				);
			} else {
				$z++;
				$field = array(
						'name'			=> $class['name'],
						'type'			=> 'dropdown',
						'lang'			=> 'uc_'.$class['name'],
						'category'		=> 'character',
						'undeletable'	=> 1,
						'options'		=> array('-----'),
						'sort' 			=> $z,
						'no_custom'		=> true,
						'options_language'=> $class['type'],
				);
			}
			if(isset($class_deps[$class['name']])) {
				foreach($class_deps[$class['name']] as $child => $type) {
					$field['ajax_reload']['multiple'][] = array(array($child), '%URL%&ajax=true&child='.$child.'&parent='.$class['name']);
				}
			}
			foreach($class_data as $iclass) {
				if($iclass['name'] == $class['name'])
					$field['options'] = $this->get($iclass['type']);
			}
			
			$this->pdh->put('profile_fields', 'delete_fields', array(array($field['name'])));
			$this->pdh->process_hook_queue();
			$this->pdh->put('profile_fields', 'insert_field', array($field));
			$this->pdh->process_hook_queue();
			
			$this->config->set('game_classdata_hash', $strClassdataHash);
		}
	}
	

	/**
	 * Add the profile fields for selected game
	 *
	 * @param string $newgame
	 * @param string $lang
	 */
	public function AddProfileFields() {
		$this->pdh->put('profile_fields', 'truncate_fields');
		$this->pdh->process_hook_queue();
		// add fields for classes/subclasses etc
		$class_data = $this->gameinfo()->get_class_dependencies();
		// build an array with parent-name , child-name => child-type
		$class_deps = array();
		foreach($class_data as $class) {
			if(!is_array($class['parent'])) continue;
			foreach($class['parent'] as $parent => $ids) {
				$class_deps[$parent][$class['name']] = $class['type'];
			}
		}
		$z = 0;
		foreach($class_data as $class) {
			if($class['admin']) {
				// make it a hidden field
				$field = array(
					'name'			=> $class['name'],
					'type'			=> 'hidden',
					'lang'			=> 'uc_'.$class['name'],
					'undeletable'	=> 1,
					'category'		=> 'character',
					'no_custom'		=> true,
					'options_language'=> $class['type'],
				);
			} else {
				$z++;
				$field = array(
					'name'			=> $class['name'],
					'type'			=> 'dropdown',
					'lang'			=> 'uc_'.$class['name'],
					'category'		=> 'character',
					'undeletable'	=> 1,
					'options'		=> array('-----'),
					'sort' 			=> $z,
					'no_custom'		=> true,
					'options_language'=> $class['type'],
				);
			}
			if(isset($class_deps[$class['name']])) {
				foreach($class_deps[$class['name']] as $child => $type) {
					$field['ajax_reload']['multiple'][] = array(array($child), '%URL%&ajax=true&child='.$child.'&parent='.$class['name']);
				}
			}
			foreach($class_data as $iclass) {
				if($iclass['name'] == $class['name'])
					$field['options'] = $this->get($iclass['type']);
			}
			$this->pdh->put('profile_fields', 'insert_field', array($field));
		}
		// Insert the field names in database
		$xml_fields = $this->gameinfo()->profilefields();
		if(is_array($xml_fields)){
			foreach($xml_fields as $name=>$values) {
				$values['no_custom']	= true;
				$values['name']			= $name;
				// move the static profilefields behind the class-stuff
				$values['sort']			= $values['sort'] + $z;
				$this->pdh->put('profile_fields', 'insert_field', array($values));
			}
		}
		
		//Reset Cache
		$this->pdh->process_hook_queue();
	}
	
	/**
	 * initializes default-roles
	 */
	public function load_default_roles() {
		$this->pdh->put('roles', 'truncate_role');
		if($this->type_exists('roles')) {
			$roles = $this->gameinfo()->default_roles;
			foreach($roles as $roleid => $classes){
				$this->pdh->put('roles', 'insert_role', array($roleid, $this->get_name('roles', $roleid), implode('|', $classes)));
			}
		} else {
			if($this->installer) $role_lang = array($this->lang['role_healer'], $this->lang['role_tank'], $this->lang['role_range'], $this->lang['role_melee']);
			else $role_lang = array($this->user->lang('role_healer'), $this->user->lang('role_tank'), $this->user->lang('role_range'), $this->user->lang('role_melee'));
			$this->pdh->put('roles', 'insert_role', array(1, $role_lang[0]));
			$this->pdh->put('roles', 'insert_role', array(2, $role_lang[1]));
			$this->pdh->put('roles', 'insert_role', array(3, $role_lang[2]));
			$this->pdh->put('roles', 'insert_role', array(4, $role_lang[3]));
		}
		$this->load_default_classroles();
		$this->pdh->process_hook_queue();
	}	

	// load the default role for each class if available in game file
	public function load_default_classroles() {
		if($this->type_exists('classrole')) {
			$roles = $this->gameinfo()->default_classrole;
			if(is_array($roles) && count($roles)){
				$this->config->set('roles_defaultclasses', json_encode($roles));
			}
		}
	}

	public function installGame($newgame, $lang){
		//Uninstall old game
		$this->uninstallGame();
		
		$this->game = $newgame;
		if ((int)$this->config->get('update_first_game_inst') !== 1){
			//Reset some data
			$this->resetEvents();
			$this->resetItempools();
			$this->resetMultiDKPPools();
			
			//Add Default Pools - There is always an itempool with ID 1
			$this->addMultiDKPPool("Default", "Default MultiDKPPool", array(), array(1));
		}
		
		//Reset Ranks
		$this->resetRanks();
		//Reset Raidgroups
		$this->resetRaidgroups();

		//Install new game
		$this->init_gameclass();
		
		if(!in_array($lang, $this->gameinfo()->langs)) {
			$lang = $this->gameinfo()->langs[0];
		}
		
		$this->gameinfo()->lang = $lang;
		$install = (defined('EQDKP_INSTALLED') && EQDKP_INSTALLED) ? false : true;
		
		$info = $this->gameinfo()->install($install);
		
		$game_config = array(
				'default_game'		=> $newgame,
				'game_language'		=> $lang,
				'game_version'		=> $this->gameinfo()->version
		);
		
		//infotooltip-config changes
		$itt_config = array(
				'infotooltip_use' => 0
		);
		
		$parserlist = $this->itt->get_parserlist($newgame);
		if(count($parserlist)) {
			$itt_config['infotooltip_use'] = 1;
			ksort($parserlist);
			reset($parserlist);
			$itt_config['itt_prio1'] = current($parserlist);
			$itt_config['itt_prio2'] = next($parserlist);
			unset($parserlist);
			$langlist = $this->itt->get_supported_languages($newgame);
			$itt_config['itt_langprio1'] = (in_array('en', $langlist)) ? 'en' : ((in_array('de', $langlist)) ? 'de' : ((in_array('fr', $langlist)) ? 'fr' : current($langlist)));
			$itt_config['itt_langprio2'] = (in_array('de', $langlist)) ? 'de' : ((in_array('fr', $langlist)) ? 'fr' : ((next($langlist)) ? current($langlist) : prev($langlist)));
			$itt_config['itt_langprio3'] = (in_array('fr', $langlist)) ? 'fr' : ((next($langlist)) ? current($langlist) : prev($langlist));
			unset($langlist);
		}
		$this->config->set(array_merge($game_config, ((is_array($info['config'])) ? $info['config'] : array()), $itt_config, $this->itt->changed_prio1($newgame, $itt_config['itt_prio1'])));
		
		$queries = $info['queries'];
		
		//classcolors
		if(is_array($this->gameinfo()->get_class_colors()) && $this->pdh->put('class_colors', 'truncate_classcolor', array())) {
			$style_ids = array();
			$style_ids = $this->pdh->get('styles', 'id_list');
			foreach ($this->gameinfo()->get_class_colors() as $class_id => $color) {
				foreach($style_ids as $id) {
					$this->pdh->put('class_colors', 'add_classcolor', array($id, $class_id, $color));
				}
			}
		}
		if(is_array($queries)) {
			foreach($queries as $sql) {
				$this->db->query($sql);
			}
		}
		//Add Profilefields
		$this->AddProfileFields();
		
		//roles
		$this->load_default_roles();
		
		$this->config->del('update_first_game_inst');
		
		//Reset PDH Cache
		$this->pdh->process_hook_queue();
	}
	
	/**
	 * Uninstalls recent game
	 */
	private function uninstallGame(){
		if (defined('EQDKP_INSTALLED')){
			$this->gameinfo()->uninstall();
		}
	}
	
	//Delets all events
	public function resetEvents(){
		$this->pdh->put("event", "reset", array());
		$this->pdh->process_hook_queue();
	}
		
	public function addEvent($strName, $intValue, $strIcon){
		return $this->pdh->put("event", "add_event", array($strName, $intValue, $strIcon));
	}
	
	//Deletes all itempools execept the one with ID 1
	public function resetItempools(){
		$this->pdh->put("itempool", "reset", array());
		$this->pdh->process_hook_queue();
	}
	
	public function addItempool($strName, $strDescription){
		return $this->pdh->put("itempool", "add_itempool", array($strName, $strDescription));
	}
	
	public function addLink($strName, $strURL){
		return $this->pdh->put("links", "add", array($strName, $strURL));
	}
	
	public function removeLink($strName){
		$this->pdh->put("links", "deleteByName", array($strName));
		$this->pdh->process_hook_queue();
	}
	
	//Deletes all MultiDKP Pools. Default one will be created on game install.
	public function resetMultiDKPPools(){
		$this->pdh->put("multidkp", "reset", array());
		$this->pdh->process_hook_queue();
	}
	
	public function addMultiDKPPool($strName, $strDescription, $arrEventIDs, $arrItempoolIDs){
		return $this->pdh->put("multidkp", "add_multidkp", array($strName, $strDescription, $arrEventIDs, $arrItempoolIDs));
	}
	//Updates the Default MultiDKP Pools
	public function updateDefaultMultiDKPPool($strName, $strDescription, $arrEventIDs){
		return $this->pdh->put("multidkp", "update_multidkp", array(1, $strName, $strDescription, $arrEventIDs, array(1), array()));
	}
	
	//Deletes all ranks
	public function resetRanks(){
		$this->pdh->put("rank", "truncate", array());
		$this->pdh->process_hook_queue();
	}
	
	public function addRank($intID, $strName, $blnDefault=false, $strIcon=''){
		return $this->pdh->put("rank", "add_rank", array($intID, $strName, false, '', '', $intID+1, $blnDefault, $strIcon));
	}
	
	//Delete all Raidgroups except the Default Raidgroup with ID 1
	public function resetRaidgroups(){
		$this->pdh->put('raid_groups', 'reset', array());
		$this->pdh->process_hook_queue();
	}
	
	public function addRaidgroup($name, $color, $desc='', $standard=0, $sortid=0, $system=0){
		$this->pdh->put('raid_groups', 'add', array($name, $color, $desc, $standard, $sortid, $system));
	}
	
	
	/**
	 * Will be executed from the Game Cronjob
	 *
	 * @return string
	 */
	public function cronjob($params){
		return $this->gameinfo()->cronjob($params);
	}
	
	/**
	 * Options for the Game Cronjob
	 *
	 * @return string
	 */
	public function cronjobOptions(){
		return $this->gameinfo()->cronjobOptions();
	}
		
	/**
	 * additional fields for admin-settings
	 *
	 * @return array
	 */
	public function admin_settings() {
		$fields = $this->gameinfo()->admin_settings();
		$class_deps = $this->gameinfo()->get_class_dependencies();
		foreach($class_deps as $class) {
			if(isset($class['admin']) && $class['admin']) {
				$fields[$class['name']] = array(
					'type'		=> 'dropdown',
					'lang'		=> 'uc_'.$class['name'],
					'options'	=> $this->get($class['type']),
				);
			}
		}
		return $fields;
	}
}

if(!class_exists('game_generic')) {
	abstract class game_generic extends gen_class {
		private $icons_checked = false;
		public $icons = array();
		public $character_unique_ids = false;
		public $author = "";
		public $version = "";

		public function __construct(){
			$this->path = $this->root_path.'games/'.$this->this_game.'/';
			$this->scan_languages();
			$this->scan_icons();
		}
		
		public function __get($name) {
			if(in_array($name, $this->types)) {
				$this->$name = array();
			}
			return parent::__get($name);
		}
		
		public static function getApiLevel(){
			return (isset(static::$apiLevel)) ? static::$apiLevel : 0;
		}
		
		protected function scan_languages() {
			$languages = sdir($this->path.'language/', '*.php', '.php');
			foreach($languages as $language) {
				if(!in_array($language, $this->langs)) {
					$this->langs[] = $language;
				}
			}
		}
				
		public function get_dependency($dependency) {
			if(isset($this->dependencies[$dependency])) return $this->dependencies[$dependency];
			return false;
		}
		
		/**
		 * scan all available icons
		 *
		 */
		protected function scan_icons(){
			if($this->icons_checked) return true;
			$this->icons_checked = true;
			$this->icons = sdir($this->path.'icons/');
		}

		/**
		 * Returns information about type in appropriate langs (if not default lang)
		 *
		 * @param string $type
		 * @param array $add_lang
		 * @return array
		 */
		public function get($type, $add_lang=array()){
			//valid type?
			if(!in_array($type, $this->types)) {
				return false;
			}
			if(!is_array($add_lang)) {
				$add_lang = array($add_lang);
			}
			$langs = (!empty($add_lang)) ? $add_lang : array($this->lang);
			//type already loaded?
			if(empty($this->$type)) {
				if(method_exists($this, 'load_'.$type)) {
					call_user_func_array(array($this, 'load_'.$type), array($langs));
				} else {
					call_user_func_array(array($this, 'load_type'), array($type, $langs));
				}
			}
			return $this->$type;
		}
		
		protected function load_type($type, $langs){
			foreach($langs as $lang) {
				$this->load_lang_file($lang);
				if(!isset($this->$type)) $this->$type = array();
				if (isset($this->lang_file[$lang][$type])) $this->{$type}[$lang] = $this->lang_file[$lang][$type];
			}
		}

		/**
		 * Returns array of types as strings
		 *
		 * @return array
		 */
		public function get_types(){
			return $this->types;
		}

		/**
		 * deletes data holding variables
		 *
		 * @param string $type
		 * @param bool $lang_file
		 * @return bool
		 */
		public function flush($type, $lang_file=false){
			//type is the language here
			if($lang_file) {
				unset($this->lang_file[$type]);
				return true;
			}
			if(!in_array($type, $this->types)) {
				return false;
			}
			if($this->$type) {
				$this->$type = array();
			}
			return true;
		}

		/**
		 * load an object
		 *
		 * @param string $classname
		 * @return object
		 */
		public function load_object($classname, $params=NULL){
			$path = $this->path.'objects/'.$classname.'.class.php';
			if(file_exists($path)) {
				include_once($path);
				if(!in_array($classname, $this->no_reg_obj)) {
					return registry::register($classname, $params);
				} else {
					$ref = new ReflectionClass($classname);
					return $ref->newInstanceArgs($params);
				}
			}
			return false;
		}

		/**
		 * Loads language data from file into class
		 *
		 * @param string $lang
		 */
		protected function load_lang_file($lang){
			$language_file = $this->path.'language/'.$lang.'.php';
			if(!isset($this->lang_file[$lang]) && is_file($language_file)) {
				include($language_file);
				$this->lang_file[$lang] = ${$lang.'_array'};
			}
		}

		/**
		 * Initialises Gamelanguage
		 *
		 * @param string $lang
		 */
		protected function load_glang($lang){
			$this->load_lang_file($lang);
			if(isset($this->lang_file[$lang]['lang'])){
				$this->glang[$lang] = $this->lang_file[$lang]['lang'];
			}
		}

		/**
		 * Returns language var
		 *
		 * @param string $var
		 * @param string $lang
		 * @return string
		 */
		public function glang($var, $return_key=false, $lang=false, $secondtry=false){
			if(!$var OR is_array($var)) return false;
			if(!$lang) {
				$lang = $this->lang;
			}
			if(!isset($this->glang[$lang])) {
				$this->load_glang($lang);
			}
			if(!isset($this->glang[$lang]) AND !$secondtry) {
				return $this->glang($var, $return_key, false, true);
			}
			if(isset($this->glang[$lang][$var])) {
				return $this->glang[$lang][$var];
			}
			if($return_key) return $var;
			return false;
		}
	
		/**
		 * Default profilefields
		 *
		 * @return array
		 */
		public function profilefields() {
			return array();
		}

		/**
		 * Returns profile fields
		 *
		 * @return string
		 */
		public function changeprofilefields(){
			return array();
		}
		
		/**
		 * Will be executed from the Game Cronjob
		 *
		 * @return string
		 */
		public function cronjob($params = array()){
			return false;
		}
		
		/**
		 * Options for the Game Cronjob
		 *
		 * @return string
		 */
		public function cronjobOptions(){
			return false;
		}
		
		/**
		 * additional fields for admin-settings
		 *
		 * @return array
		 */
		public function admin_settings() {
			return array();
		}
		
		/**
		 * class-dependency array
		 *
		 * @return array
		 */
		public function get_class_dependencies() {
			return $this->class_dependencies;
		}
		
		public abstract function install($blnEQdkpInstall=false);
		
		public function uninstall(){
			return false;
		}
		
		protected abstract function load_filters($langs);
		
		public function get_class_colors(){
			if (isset($this->class_colors)) return $this->class_colors;
			return false;
		}
	}
}
?>