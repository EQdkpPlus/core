<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2013
 * Date:		$Date: 2013-04-24 10:23:19 +0200 (Mi, 24 Apr 2013) $
 * -----------------------------------------------------------------------
 * @author		$Author: godmod $
 * @copyright	2006-2013 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 13337 $
 * 
 * $Id: super_registry.class.php 13337 2013-04-24 08:23:19Z godmod $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

include_once(registry::get_const('root_path').'core/html/html.aclass.php');

class htooltip extends html {
	public static $shortcuts = array('in');

	protected static $type = 'none';
	
	public $name = '';
	public $content = '';
	public $text = '';
	public $usediv = false;
	private $contfunc = true;
	private $all_opts = array('contfunc', 'name', 'my', 'at', 'classes', 'width');
	
	public function __construct($text, $content, $options=array()) {
		$this->text = $text;
		$this->content = $content;
		foreach($options as $key => $option) {
			$this->$key = $option;
		}
	}
	
	public function __toString() {
		if(empty($this->name)) $this->name = uniqid();
		if(empty($this->id)) $this->id = uniqid();
		$options = array();
		foreach($this->all_opts as $opt) $options[$opt] = $this->$opt;
		$this->jquery->qtip('.'.$name, 'return $(".'.$name.'_c", this).html();', $options);
		if(isset($this->usediv) && $this->usediv){
			return '<div class="'.$this->name.'" id="'.$this->id.'"><div class="'.$this->name.'_c" style="display:none;">'.$this->content.'</div>'.$this->text.'</div>';
		}else{
			return '<span class="'.$this->name.'" id="'.$this->id.'"><span class="'.$this->name.'_c" style="display:none;">'.$this->content.'</span>'.$this->text.'</span>';
		}
		return ;
	}
	
	public function inpval() {
		return '';
	}
}
?>