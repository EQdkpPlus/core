<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2013
 * Date:		$Date: 2012-08-04 23:04:36 +0200 (Sa, 04 Aug 2012) $
 * -----------------------------------------------------------------------
 * @author		$Author: hoofy_leon $
 * @copyright	2006-2013 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 11892 $
 * 
 * $Id: gen_class.class.php 11892 2012-08-04 21:04:36Z hoofy_leon $
 */
 
if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class form extends gen_class {
	public static $shortcuts = array();
	// public static $singleton = false;
	
	public static $form_id = '';
	
	public $use_tabs = false;
	public $use_fieldsets = false;
	public $use_dependency = false;
	
	public $lang_prefix = '';
	
	private $field_array = array();
	
	public function __construct($form_id) {
		self::$form_id = $form_id;
	}
	
	public function add_tab($tab, $fieldsets=array(), $fields=array()) {
	}
	
	public function add_tabs($fieldarray) {
	}
	
	public function add_fieldset($fieldset, $fields=array()) {
	}
	
	public function add_fieldsets($fieldarray, $tab='') {
	}
	
	public function add_field($name, $options, $fieldset='', $tab='') {
	}
	
	public function add_fields($fieldarray, $fieldset='', $tab='') {
	}
	
	public function output($values=array()) {
		foreach($this->field_array as $tabname => $fieldsetdata){
			$this->tpl->assign_block_vars('tabs', array(
				'NAME'	=> $this->user->lang($this->lang_prefix.'tab_'.$tabname),
				'ID'	=> $tabname
				)
			);

			foreach($fieldsetdata as $fieldsetname=>$fielddata){
				$lang = $this->lang_prefix.'fs_'.$fieldsetname;
				$info = $this->lang_prefix.'info_'.$fieldsetname;
				$this->tpl->assign_block_vars('tabs.fieldsets', array(
					'NAME'		=> ($this->user->lang($lang, false, false)) ? $this->user->lang($lang) : $this->game->glang($lang),
					'INFO'		=> ($this->user->lang($info, false, false)) ? $this->user->lang($info) : $this->game->glang($info),
				));

				foreach($fielddata as $name=>$data){
					// TODO: rework 'disabled' and 'default'
					$lang = $this->lang_prefix.$name;
					$help = $lang.'_help';
					$this->tpl->assign_block_vars('tabs.fieldsets.field', array(
						'NAME'		=> ($this->user->lang($lang, false, false)) ? $this->user->lang($lang) : (($this->game->glang($lang)) ? $this->game->glang($lang) : $name),
						'HELP'		=> ($this->user->lang($help, false, false)) ? $this->user->lang($help) : (($this->game->glang($help)) ? $this->game->glang($help) : ''),
						'FIELD'		=> register('h'.$data['type'], array($name, $options))
					));
				}
			}
		}
	}
	
	public function return_values() {
	}
}
?>