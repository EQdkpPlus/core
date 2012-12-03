<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       01.07.2009
 * Date:        $Date: 2009-05-17 16:10:37 +0200 (So, 17 Mai 2009) $
 * -----------------------------------------------------------------------
 * @author      $Author: hoofy_leon $
 * @copyright   2006-2008 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev: 4885 $
 *
 * $Id: filter.php 4885 2009-05-17 14:10:37Z sz3 $
 */

if ( !defined('EQDKP_INC') )
{
    header('HTTP/1.0 404 Not Found');
    exit;
}

if(!class_exists('daoc')) {
  class daoc
  {
  	private $this_game = 'daoc';
  	private $types = array('classes', 'races', 'factions', 'filters');
	private $classes = array();
	private $races = array();
	private $factions = array();
	private $filters = array();
    public  $langs = array('english');

	private $glang = array();
	private $lang_file = array();
	private $path = '';
	public  $lang = false;
	public  $version = '2.0';


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
		global $core;
		if(!$lang) {
			$lang = $this->lang;
		}
		if(!$this->glang[$lang]) {
			$this->load_glang($lang);
		}
		return $this->glang[$lang][$var];
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
			$this->filters[$lang][] = array('name' => '-----------', 'value' => false);
        	foreach($names as $id => $name) {
            	$this->filters[$lang][] = array('name' => $name, 'value' => 'class:'.$id);
        	}
		}
	}

	public function get_OnChangeInfos($install=false)
	{
	    $info['aq'] = array(
    		"UPDATE __styles SET logo_path='logo_plus.gif' WHERE logo_path='bc_header3.gif' ;",
    		"UPDATE __styles SET logo_path='/logo/logo_plus.gif' WHERE logo_path='/logo/logo_wow.gif' ;",
    		"UPDATE __styles SET logo_path='logo_plus.gif' WHERE logo_path='logo_wow.gif' ;" ,
	    );

	    //Do this SQL Query NOT if the Eqdkp is installed -> only @ the first install
	    if($install)
	    {
    		array_push($info['aq'], "UPDATE __config SET config_value = 32 WHERE config_name='default_style' ;");
    		array_push($info['aq'], "UPDATE __users SET user_style = '32' ;");
		}
		return $info;
	}
  }#class
}
?>