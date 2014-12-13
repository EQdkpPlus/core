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

class calendareventexport_pageobject extends pageobject {

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
				'DROPDOWN'			=> new hdropdown('link', array('options' => $menu_structure, 'value' => $output, 'id' => 'exportdropdown')),
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
		$raidexport_folder = $this->root_path . 'core/calendarexport/';
		if ( $dir = opendir($raidexport_folder) ){
			while ( $d_plugin_code = @readdir($dir) ){
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
?>