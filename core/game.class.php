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
 * @copyright	2006-2011 EQdkp-Plus Developer Team
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

class game extends gen_class {
	public static $shortcuts = array('itt' => 'infotooltip');

	private $data			= array();
	private $games			= array();
	private $class_colors	= array('fetched' => false);
	private $objects		= array();
	private $game			= '';
	private $lang_name		= '';
	private $installer		= false;
	private $deficon_path	= array(
								'roles'		=> 'images/roles/',
								'events'	=> 'images/events/',
							);
	public $obj				= array();

	//fill data with gameinfos (classes, races, factions, filters, etc.)
	public function __construct($installer=false, $lang_name=''){
		if(!$installer){
			$this->lang_name = $this->user->lang_name;
			$this->game = $this->config->get('default_game');
			$this->init_gameclass();
			$this->pdl->register_type('game');
		}
		if($installer) {
			$this->installer = true;
			$this->lang_name = $lang_name;
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
			$this->pdl->log('game', 'Tried to initialize undefined game \''.$this->game.'\', default to game \'wow\'.');
			$this->game = 'wow';
			$this->config->set('default_game', 'wow');
			return $this->init_gameclass();
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
		if ($this->type_exists('chartooltip')){
			$tt = $this->gameinfo()->chartooltip($intCharID);
			return str_replace($this->root_path, register('env')->link, $tt);
		}
		return '';
	}

	/**
	 * Returns per game ImageTag for classes
	 *
	 * @param int $class_id
	 * @param bool $big
	 * @param bool $pathonly
	 * @return html string
	 */
	private function decorate_classes($class_id, $big=false, $member_id=null, $pathonly=false){
		if($big AND !$this->icon_exists('classes_big')) {
			$big = false;
		}
	
		if(is_file($this->root_path.'games/'.$this->game.'/classes/'.$class_id.(($big) ? '_b.png' : '.png'))){
			$icon_path = $this->server_path.'games/'.$this->game.'/classes/'.$class_id.(($big) ? '_b.png' : '.png');
			return ($pathonly) ? $icon_path : '<img src="'.$icon_path.'" alt="class '.$class_id.'" class="'.(($big) ? $this->game.'_classicon_big' : $this->game.'_classicon classicon').'" title="'.$this->get_name('classes', $class_id).'" />';
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
	private function decorate_races($race_id,  $gender=false, $member_id=null, $pathonly=false){
		if ($gender == "Female" && is_file($this->root_path.'games/'.$this->game.'/races/'.$race_id.'f.png')){
			$icon_path = $this->root_path.'games/'.$this->game.'/races/'.$race_id.'f.png';
		} else {
			$icon_path = $this->root_path.'games/'.$this->game.'/races/'.$race_id.'.png';
		}

		if(is_file($icon_path)){
			$icon_path = str_replace($this->root_path, $this->server_path, $icon_path);
			return ($pathonly) ? $icon_path : "<img src='".$icon_path."' alt='race ".$race_id."' class=\"".$this->game."_raceicon raceicon\" title=\"".$this->get_name('races', $race_id)."\" />";
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
	private function decorate_ranks($rank_id,  $size=16, $member_id=null, $pathonly=false){		
		$strRankIcon = $this->pdh->get('rank', 'icon', array($rank_id));
		
		if(strlen($strRankIcon) && is_file($this->root_path.'games/'.$this->game.'/ranks/'.$strRankIcon)){
			$icon_path = $this->server_path.'games/'.$this->game.'/ranks/'.$strRankIcon;
			return ($pathonly) ? $icon_path : "<img src='".$icon_path."' width='".$size."' height='".$size."' alt='rank ".$rank_id."' class=\"".$this->game."_rankicon\" />";
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
	private function decorate_talents($class_id, $talent=0, $member_id=null, $pathonly=false){
		if(is_file($this->root_path.'games/'.$this->game.'/talents/'.$class_id.$talent.'.png')){
			$icon_path = $this->server_path.'games/'.$this->game.'/talents/'.$class_id.$talent.'.png';
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
	private function decorate_events($event_id, $size=16, $pathonly=false){		
		if(is_file($this->root_path.'games/'.$this->game.'/events/'.$this->pdh->get('event', 'icon', array($event_id)))){
			$icon_path = $this->server_path.'games/'.$this->game.'/events/'.$this->pdh->get('event', 'icon', array($event_id));
			return ($pathonly) ? $icon_path : "<img src='".$icon_path."' width='".$size."' height='".$size."' alt='' class=\"".$this->game."_eventicon eventicon\"/>";
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
	private function decorate_def_events($event_id, $size=16, $pathonly=false, $alt=''){
		if(is_file($this->root_path.$this->deficon_path['events'].$this->pdh->get('event', 'icon', array($event_id)))){
			$icon_path = $this->server_path.$this->deficon_path['events'].$this->pdh->get('event', 'icon', array($event_id));
			return ($pathonly) ? $icon_path : "<img src='".$icon_path."' width='".$size."' height='".$size."' alt='".$alt."' class=\"".$this->game."_eventicon eventicon\"/>";
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
	private function decorate_roles($role_id, $size=20, $member_id=null, $pathonly=false){
		if(is_file($this->root_path.'games/'.$this->game.'/roles/'.$role_id.'.png')){
			$icon_path = $this->server_path.'games/'.$this->game.'/roles/'.$role_id.'.png';
			return ($pathonly) ? $icon_path : "<img src='".$icon_path."' width='".$size."' height='".$size."' alt='' class=\"".$this->game."_roleicon roleicon\"/>";
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
	private function decorate_def_roles($role_id, $size=20, $pathonly=false){
		if(is_file($this->root_path.$this->deficon_path['roles'].$role_id.'.png')){
			$icon_path = $this->server_path.$this->deficon_path['roles'].$role_id.'.png';
			return ($pathonly) ? $icon_path : "<img src='".$icon_path."' width='".$size."' height='".$size."' alt='' class=\"".$this->game."_roleicon roleicon\"/>";
		}
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


	/**
	 * returns all available games
	 *
	 * @return array
	 */
	public function get_games(){
		if(empty($this->games)) {
			if ( $dir = opendir($this->root_path . 'games/') ) {
				while ( $game_name = @readdir($dir) ) {
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
	 * checks if the game has the given $icon
	 *
	 * @param string $icon
	 * @return bool
	 */
	public function icon_exists($icon){
		if(empty($this->gameinfo()->icons_checked[$icon]))
			$this->gameinfo()->check_icons($icon);
		if($this->gameinfo()->icons AND in_array($icon, $this->gameinfo()->icons)) {
			return true;
		}
		return false;
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
			
			if (count($this->class_colors[$style_id]) != count($this->get($this->get_primary_classes(), 'id_0'))){
				$arrGameColors = $this->gameinfo()->get_class_colors();
				foreach($this->get($this->get_primary_classes(), 'id_0') as $key => $val){
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
			switch($filter) {
				case 'id_0': 
					unset($data[0]); break;

				default: break;
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
	 * Returns whole array for the primary class (necessary in e.g. calendar, role assignment)
	 *
	 * @param array $filter, possible values ('id_0')
	 * @param string $lang
	 * @return array
	 */
	public function get_primary_classes($blnReturnName = false) {
		$class_dep = $this->gameinfo()->get_class_dependencies();
		foreach($class_dep as $class) {
			if(isset($class['primary']) && $class['primary']) {
				return ($blnReturnName) ? $class['name'] : $class['type'];
			}
		}
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
	 * Returns an array with all possible dependent classes
	 *
	 * @param string	$parent:	Fieldname of the parent class
	 * @param string 	$child:		Fieldname of child class
	 * @param string 	$selection:	ID of the parent-class
	 * @return array
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
	 * Returns the class-dependencies as an associative array
	 *
	 * @return array
	 */
	public function get_assoc_classes() {
		/*
		 *	a fully linear dependency for all classes with flag roster is assumed, ordering has to be correct beforehand
		 *	means, e.g. faction => race => class => talent
		 */
		$class_dep = $this->gameinfo()->get_class_dependencies();
		// create a name 2 type array
		$name2type = array();
		$todisplay = array();
		foreach($class_dep as $class) {
			$name2type[$class['name']] = $class['type'];
			if(isset($class['roster']) && $class['roster']) $todisplay[] = $class['type'];
		}
		// get all single dependencies
		$relevant_deps = array();
		foreach($class_dep as $class) {
			if($class['parent'] && isset($name2type[key($class['parent'])])) {
				$relevant_deps[$name2type[key($class['parent'])]] = $class['type'];
				$child_ids[$name2type[key($class['parent'])]] = current($class['parent']);
			}
		}
		// find first "level"
		pd($relevant_deps);
		pd($child_ids);
		pd($name2type);
		$first = key($relevant_deps);
		while($key = array_search($first, $relevant_deps)) {
			$first = $key;
		}
		// build associative array
		pd($first);
		return $this->build_assoc_array($first, $relevant_deps, $child_ids);
	}
	
	private function build_assoc_array($type, $dep_order, $child_ids, $parent_id=false) {
		$assoc_array = array();
		$data = $this->get($type);
		foreach($data as $id => $name) {
			if(isset($dep_order[$type])) {
				if($parent_id !== false) {
					$true_ids = $child_ids[array_search($type, $dep_order)][$parent_id];
					if(!in_array($id, $true_ids))
						continue;
				}
				$assoc_array[$id] = $this->build_assoc_array($dep_order[$type], $dep_order, $child_ids, $id);
			// last level reached
			} else $assoc_array = $child_ids[$type][$parent_id];
		}
		return $assoc_array;
	}
	
	/**
	 * Returns a data-array containing all admin-selected class stuff
	 *
	 * @return array
	 */
	public function get_admin_classdata() {
		$class_dep = $this->gameinfo()->get_class_dependencies();
		$data = array();
		foreach($class_dep as $class) {
			if(isset($class['admin']) && $class['admin']) {
				$data[$class['name']] = $this->config->get($class['name']);
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
			pd(debug_backtrace());
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
	public function decorate($type, $params){
		$params =  (is_array($params)) ? $params : array($params);
		if($this->icon_exists($type)) {
			if(method_exists($this->gameinfo(), 'decorate_'.$type)) {
				return call_user_func_array(array($this->gameinfo(), 'decorate_'.$type), $params);
			} elseif(method_exists($this, 'decorate_'.$type)) {
				return call_user_func_array(array($this, 'decorate_'.$type), $params);
			}		
		// there are no game specific icons, check if there are default ones
		}elseif($this->default_icons($type)){
			return call_user_func_array(array($this, 'decorate_def_'.$type), $params);
		} elseif($this->type_exists($type)) {
			if(!$this->data[$type]) {
				$this->data[$type] = $this->gameinfo()->get($type);
				$this->gameinfo()->flush($type);
			}
			return $this->get_name($type, $params[0]);
		}
		return '';
	}
	
	/**
	 * Creates all Icons of classes/subclasses to decorate a character
	 *
	 * @param int 	$char_id
	 * @return html string
	 */
	public function decorate_character($char_id) {
		$class_dep = $this->gameinfo()->get_class_dependencies();
		$decor = '';
		foreach($class_dep as $class) {
			if(isset($class['decorate']) && $class['decorate'])
				$decor .= ' '.$this->decorate($class['type'], array($this->pdh->get('member', 'profile_field', array($char_id, $class['name']))));
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
	
	

	/**
	 * Add the profile fields for selected game
	 *
	 * @param string $newgame
	 * @param string $lang
	 */
	public function AddProfileFields() {
		$this->pdh->put('profile_fields', 'truncate_fields');
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
				);
			}
			foreach($class_deps[$class['name']] as $child => $type) {
				$field['ajax_reload']['multiple'][] = array(array($child), '%URL%&ajax=true&child='.$child.'&parent='.$class['name']);
			}
			$this->pdh->put('profile_fields', 'insert_field', array($field));
		}
		// Insert the field names in database
		$xml_fields = $this->gameinfo()->profilefields();
		if(is_array($xml_fields)){
			foreach($xml_fields as $name=>$values) {
				$values['name'] = $name;
				// move the static profilefields behind the class-stuff
				$values['sort'] = $values['sort'] + $z;
				$this->pdh->put('profile_fields', 'insert_field', array($values));
			}
		}
	}
	
	/**
	 * initializes default-roles
	 */
	public function load_default_roles() {
		$this->pdh->put('roles', 'truncate_role');
		if($this->type_exists('roles')) {
			$roles = $this->get('roles');
			foreach($roles as $roleid => $classes){
				$this->pdh->put('roles', 'insert_role', array($roleid, $this->glang('role'.$roleid), implode('|', $classes)));
			}
		} else {
			if($this->installer) $role_lang = array($this->lang['role_healer'], $this->lang['role_tank'], $this->lang['role_range'], $this->lang['role_melee']);
			else $role_lang = array($this->user->lang('role_healer'), $this->user->lang('role_tank'), $this->user->lang('role_range'), $this->user->lang('role_melee'));
			$this->pdh->put('roles', 'insert_role', array(1, $role_lang[0]));
			$this->pdh->put('roles', 'insert_role', array(2, $role_lang[1]));
			$this->pdh->put('roles', 'insert_role', array(3, $role_lang[2]));
			$this->pdh->put('roles', 'insert_role', array(4, $role_lang[3]));
		}
		$this->pdh->process_hook_queue();
	}	

	/**
	 * Does DB-Updates for Game Changing
	 *
	 * @param string $newgame
	 * @param string $lang
	 */
	public function ChangeGame($newgame, $lang){
		$this->game = $newgame;
		$this->init_gameclass();
		if(!in_array($lang, $this->gameinfo()->langs)) {
			$lang = $this->gameinfo()->langs[0];
		}
		$this->gameinfo()->lang = $lang;
		$install = (defined('EQDKP_INSTALLED') && EQDKP_INSTALLED) ? false : true;
		$info = $this->gameinfo()->get_OnChangeInfos($install);
		
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

		$queries = $info['aq'];

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
		$this->AddProfileFields();
		
		//roles
		$this->load_default_roles();
		if (!$install) {$this->tpl->parse_cssfile();}
		$this->pdh->process_hook_queue();
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
		public $icons_checked = false;

		public function __construct(){
			$this->path = $this->root_path.'games/'.$this->this_game.'/';
			$this->scan_languages();
		}
		
		public function __get($name) {
			if(in_array($name, $this->types)) {
				$this->$name = array();
			}
			return parent::__get($name);
		}
		
		protected function scan_languages() {
			$languages = sdir($this->path.'language/', '*.php', '.php');
			foreach($languages as $language) {
				if(!in_array($language, $this->langs)) {
					$this->langs[] = $language;
				}
			}
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
		 * Check if an icon exists
		 *
		 * @param string $icon
		 * @return string
		 */
		public function check_icons($icon){
			if(is_dir($this->path.$icon)) {
				$this->icons_checked[$icon] = true;
				$this->icons[] = $icon;
			}
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

		public abstract function get_OnChangeInfos($install=false);
		protected abstract function load_filters($langs);
		
		public function get_class_colors(){
			if (isset($this->class_colors)) return $this->class_colors;
			return false;
		}
	}
}
?>