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
 */

define('EQDKP_INC', true);
$eqdkp_root_path = '../../';
include_once($eqdkp_root_path . 'common.php');

class calraids_export extends page_generic {
	public static $shortcuts = array('user', 'tpl', 'in', 'core', 'html', 'game');

	public function __construct() {
		$handler = array();
		$this->user->check_auth('u_calendar_view');
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

			$this->tpl->add_js('$("#exportdropdown").change(function(){ window.location.href = "exports.php'.$this->SID.'&eventid='.$eventid.'&output="+$(this).val();});', "docready");

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
		$raidexport_folder = $this->root_path . 'calendar/raidexport/plugins/';
		if($dir = opendir($raidexport_folder)){
			while($d_plugin_code = @readdir($dir)){
				$cwd = $raidexport_folder.$d_plugin_code; // regenerate the link to the 'plugin'
				if((@is_file($cwd)) && valid_folder($d_plugin_code)){	// check if valid
					include($cwd);
					$export_array[$rpexport_plugin[$d_plugin_code]['function']] = $rpexport_plugin[$d_plugin_code]['name'];	// add to array
				}
			}
		}
		// search for game export plugins
		$raidexport_game = $this->root_path.'games/'.$this->game->get_game().'/raidexport/';
		if(is_dir($raidexport_game) && $dir = opendir($raidexport_game)){
			while($d_plugin_code = @readdir($dir)){
				$cwd = $raidexport_game.$d_plugin_code; // regenerate the link to the 'plugin'
				if((@is_file($cwd)) && valid_folder($d_plugin_code)){	// check if valid
					include($cwd);
					$export_array[$rpexport_plugin[$d_plugin_code]['function']] = $rpexport_plugin[$d_plugin_code]['name'];	// add to array
				}
			}
		}
		return $export_array;
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_calraids_export', calraids_export::$shortcuts);
registry::register('calraids_export');
?>