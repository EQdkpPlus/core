<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2011
 * Date:		$Date: 2013-01-29 17:35:08 +0100 (Di, 29 Jan 2013) $
 * -----------------------------------------------------------------------
 * @author		$Author: wallenium $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 12937 $
 * 
 * $Id: exports.php 12937 2013-01-29 16:35:08Z wallenium $
 */

class calendareventexport_pageobject extends pageobject {

	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'core', 'html');
		return array_merge(parent::__shortcuts(), $shortcuts);
	}

	public function __construct() {
		$handler = array();
		$this->user->check_pageobject('calendar');
		parent::__construct(false, $handler, array());
		$this->process();
	}

	public function display(){
		$menu_structure	= $this->generateMenuStructure();
		if($this->in->get('eventid', 0)){
			$eventid		= $this->in->get('eventid', 0);
			$output			= $this->in->get('output');

			$flipstruc = array_flip($menu_structure);
			if($output && in_array($output, $flipstruc)){
				$this->tpl->assign_var('EXPORT_OUTPUT', $output($eventid));
			}else{
				$this->tpl->assign_var('EXPORT_OUTPUT', $this->user->lang('raidevent_raid_export_indx'));
			}

			$this->tpl->add_js('$("#exportdropdown").change(function(){ window.location.href = "'.$this->strPath.$this->SID.'&eventid='.$eventid.'&output="+$(this).val();});', "docready");

			$this->tpl->assign_vars(array(
				'DROPDOWN'			=> $this->html->DropDown('link', $menu_structure, $output, '', '', 'input', 'exportdropdown'),
				'MACROEXPORT'		=> ($output == 'WoWMacroexport') ? true : false,
				'EVENT_ID'			=> $eventid,
				'F_MULTISIGNIN'		=> 'listraids.php',
			));
			
			$this->core->set_vars(array(
				'page_title'		=> $this->user->lang('raidevent_raid_export_titel'),
				'template_file'		=> 'calendar/raidevent_export.html',
				'header_format'		=> 'simple',
				'display'			=> true
			));
		}
	}

	private function generateMenuStructure(){
		$export_array[] = "----";
		// Search for plugins and make sure they are registered
		if ( $dir = opendir($this->root_path . 'core/calendarexport/') ){
			while ( $d_plugin_code = @readdir($dir) ){
				$cwd = $this->root_path.'core/calendarexport/'.$d_plugin_code; // regenerate the link to the 'plugin'
				if((@is_file($cwd)) && valid_folder($d_plugin_code)){	// check if valid
					include($cwd);
					$export_array[$rpexport_plugin[$d_plugin_code]['function']] = $rpexport_plugin[$d_plugin_code]['name'];	// add to array
				}
			}
		}
		return $export_array;
	}
}
?>