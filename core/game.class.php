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
	public static $shortcuts = array('config', 'user', 'pdl', 'pdh', 'tpl', 'db', 'itt' => 'infotooltip',
	);

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
	 * Returns information of the selected game
	 *
	 * @return string
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
		$this->gameinfo()->lang = (in_array($this->lang_name, $this->gameinfo()->langs)) ? $this->lang_name : $this->gameinfo()->langs[0];
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
	private function decorate_classes($class_id, $big=false, $pathonly=false){
		if($big AND !$this->icon_exists('classes_big')) {
			$big = false;
		}
		$icon_path = $this->root_path.'games/'.$this->game.'/classes/'.$class_id.(($big) ? '_b.png' : '.png');
		if(is_file($icon_path)){
			return ($pathonly) ? $icon_path : "<img src='".$icon_path."' alt='class ".$class_id."' />";
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
	private function decorate_races($race_id, $gender=false, $pathonly=false){
		if ($gender == "Female" && is_file($this->root_path.'games/'.$this->game.'/races/'.$race_id.'f.png')){
			$icon_path = $this->root_path.'games/'.$this->game.'/races/'.$race_id.'f.png';
		} else {
			$icon_path = $this->root_path.'games/'.$this->game.'/races/'.$race_id.'.png';
		}

		if(is_file($icon_path)){
			return ($pathonly) ? $icon_path : "<img src='".$icon_path."' alt='race ".$race_id."' />";
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
	private function decorate_ranks($rank_id, $size=16, $pathonly=false){
		$icon_path = $this->root_path.'games/'.$this->game.'/ranks/'.$rank_id.'.png';
		if(is_file($icon_path)){
			return ($pathonly) ? $icon_path : "<img src='".$icon_path."' width='".$size."' height='".$size."' alt='rank ".$rank_id."' />";
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
	private function decorate_talents($class_id, $talent=0, $pathonly=false){
		$icon_path = $this->root_path.'games/'.$this->game.'/talents/'.$class_id.$talent.'.png';
		if(is_file($icon_path)){
			return ($pathonly) ? $icon_path : "<img src='".$icon_path."' alt=''/>";
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
		$icon_path = $this->root_path.'games/'.$this->game.'/events/'.$this->pdh->get('event', 'icon', array($event_id));
		if(is_file($icon_path)){
			return ($pathonly) ? $icon_path : "<img src='".$icon_path."' width='".$size."' height='".$size."' alt=''/>";
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
		$icon_path = $this->root_path.$this->deficon_path['events'].$this->pdh->get('event', 'icon', array($event_id));
		if(is_file($icon_path)){
			return ($pathonly) ? $icon_path : "<img src='".$icon_path."' width='".$size."' height='".$size."' alt='".$alt."'/>";
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
	private function decorate_roles($role_id, $size=20, $pathonly=false){
		$icon_path = $this->root_path.'games/'.$this->game.'/roles/'.$role_id.'.png';
		if(is_file($icon_path)){
			return ($pathonly) ? $icon_path : "<img src='".$icon_path."' width='".$size."' height='".$size."' alt=''/>";
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
		$icon_path = $this->root_path.$this->deficon_path['roles'].$role_id.'.png';
		if(is_file($icon_path)){
			return ($pathonly) ? $icon_path : "<img src='".$icon_path."' width='".$size."' height='".$size."' alt=''/>";
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
				return $this->root_path.'games/'.$this->get_game().'/import/'.$this->gameinfo()->importers[$filter];
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
			} elseif($this->type_exists($type)) {
				if(!$this->data[$type]) {
					$this->data[$type] = $this->gameinfo()->get($type);
					$this->gameinfo()->flush($type);
				}
				return $this->get_name($type, $params[0]);
			}
		
		// there are no game specific icons, check if there are default ones
		}elseif($this->default_icons($type)){
			return call_user_func_array(array($this, 'decorate_def_'.$type), $params);
		}
		return '';
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
		$xml_fields = $this->gameinfo()->get_profilefields();
		$this->pdh->put('profile_fields', 'truncate_fields');
		// Insert the field names in database
		if(is_array($xml_fields)){
			foreach($xml_fields as $name=>$values) {
				$this->pdh->put('profile_fields', 'insert_field', array(array(
					'name'			=> $name,
					'fieldtype'		=> $values['type'],
					'category'		=> $values['category'],
					'lang'			=> $values['name'],
					'size'			=> (isset($values['size'])) ? intval($values['size']) : '0',
					'option'		=> (isset($values['options']) && is_array($values['options'])) ? $values['options'] : '',
					'visible'		=> (isset($values['visible'])) ? intval($values['visible']) : '0',
					'image'			=> (isset($values['image'])) ? $values['image'] : '',
					'undeletable'	=> (($values['undeletable']) ? '1' : '0'),
					'enabled'		=> 1,
					'no_custom'		=> true
				)));
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
		if(in_array($newgame, $this->itt->get_supported_games())) {
			$itt_config['infotooltip_use'] = 1;
			$parserlist = $this->itt->get_parserlist($newgame);
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
		$this->config->set(array_merge($game_config, ((is_array($info['config'])) ? $info['config'] : array()), $itt_config, $this->itt->changed_prio1($itt_config['itt_prio1'])));

		$queries = $info['aq'];

		//classcolors
		if(is_array($info['class_color']) && $this->pdh->put('class_colors', 'truncate_classcolor', array())) {
			$style_ids = array();
			$style_ids = $this->pdh->get('styles', 'id_list');
			foreach ($info['class_color'] as $class_id => $color) {
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
}

if(!class_exists('game_generic')) {
	abstract class game_generic extends gen_class {
		public $icons_checked = false;

		public function __construct(){
			$this->path = $this->root_path.'games/'.$this->this_game.'/';
		}
		
		public function __get($name) {
			if(in_array($name, $this->types)) {
				$this->$name = array();
			}
			return parent::__get($name);
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
		 * Returns profile fields
		 *
		 * @return string
		 */
		public function get_profilefields(){
			$game_file = $this->path.'field_data.php';
			if(is_file($game_file)){
				include($game_file);
			}
			return (isset($xml_fields) && is_array($xml_fields)) ? $xml_fields : array();
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

		public abstract function get_OnChangeInfos($install=false);
		protected abstract function load_filters($langs);
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_game', game::$shortcuts);
?>