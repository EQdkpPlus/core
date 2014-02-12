<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2007
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

if ( !defined('EQDKP_INC') ){
	die('Do not access this file directly.');
}

if ( !class_exists( "html_leaderboard" ) ) {
	class html_leaderboard extends gen_class {
		private $mdkpid;
		private $vpre;

		public function get_html_leaderboard($mdkpid, $view_list, $settings) {
			$arrMdkpIDList = $this->pdh->get('multidkp', 'id_list');
			$this->mdkpid = ($mdkpid) ? $mdkpid : $settings['default_pool'];
			$this->mdkpid = (in_array($this->mdkpid, $arrMdkpIDList)) ? $this->mdkpid : ((isset($arrMdkpIDList[0])) ? $arrMdkpIDList[0] : 0);
			$columns = $settings['columns'];
			$arrGameClasses = array_keys($this->game->get('classes', 'id_0'));
			$arrDiff = array_diff($arrGameClasses, $columns);
			foreach($arrDiff as $val){
				array_push($columns, $val);
			}
			$break = (isset($settings['maxperrow'])) ? $settings['maxperrow'] : 5;
			$max_member = (isset($settings['maxpercolumn'])) ? $settings['maxpercolumn'] : 5;
			$sort = (isset($settings['sort_direction'])) ? $settings['sort_direction'] : 'asc';
			$column = (isset($settings['column_type'])) ? $settings['column_type'] : 'classid';

			//sort direction
			$this->sort_direction = ($sort == 'asc') ? 1 : -1;

			//system dependant output
			$this->vpre = $this->pdh->pre_process_preset('current', array(), 0);

			$view_list = $this->pdh->aget('member', $column, 0, array($view_list));

			$column_list = array();
			foreach($view_list as $member_id => $col){
				if(is_array($columns) && in_array($col, $columns) ){
					$column_list[$col][] = $member_id;
				}
			}
			if(count($column_list) < 1 || count(current($column_list)) < 1) return '';

			$mdkp_sel = new hdropdown('lb_mdkpid', array('options' => $this->pdh->aget('multidkp', 'name', 0, array($this->pdh->get('multidkp', 'id_list'))), 'js' => ' onchange="$(\'#lbc\').val(1); form.submit();"', 'value' => $this->mdkpid));

			$leaderboard = '<div id="toggleLeaderboard"><div class="tableHeader"><h2>'.$this->user->lang('leaderboard').'<span class="toggle_button"></span></h2></div><div class="toggle_container">'.$this->user->lang('select_leaderboard').': '.$mdkp_sel.'<table width="100%" border="0" cellpadding="1" cellspacing="1" class="leaderboard">';
			$colnr = 0;
			foreach($columns as $col) {
				if(!isset($column_list[$col])) continue;
				$member_ids = $column_list[$col];
				$leaderboard .= '<td align="center" valign="top" width="200"><table class="borderless nowrap colorswitch" border="0" cellpadding="2" cellspacing="0" width="100%"><tr><th colspan="2">';
				$leaderboard .= ($column == 'classid') ? $this->game->decorate('primary', $col).' <span class="class_'.$col.'">'.$this->game->get_name('primary', $col).'</span>' : $this->game->decorate('roles', $col).' '.$this->pdh->get('roles', 'name', array($col));
				$leaderboard .= '</th></tr>';
				usort($member_ids, array(&$this, "sort_by_points"));

				$rows = ($max_member < count($member_ids)) ? $max_member : count($member_ids);
				for($i=0; $i<$rows; $i++){
					$leaderboard .= '<tr><td align="left">'.$this->pdh->geth('member', 'memberlink', array($member_ids[$i], register('routing')->build('character', false, false,false), '', false,false,true,true)).'</td><td align="right">'.$this->pdh->geth($this->vpre[0], $this->vpre[1], $this->vpre[2], array('%member_id%' => $member_ids[$i], '%dkp_id%' => $this->mdkpid, '%use_controller%' => true, '%with_twink%' => !intval($this->config->get('show_twinks')))).'</td></tr>';
				}
				$leaderboard .= '</table></td>';
				$colnr++;
				if(($colnr % $break) == 0 && $colnr < count($column_list)) {
					$leaderboard .= '</tr><tr>';
				}
			}
			$this->jquery->Collapse('#toggleLeaderboard');
			$leaderboard .= '</tr></table></div></div>';
			return $leaderboard;
		}

		private function sort_by_points($a, $b){
			//return $this->pdh->comp('points', 'current', -1, array($a, $this->mdkpid ), array($b, $this->mdkpid ));
			$params1 = $this->pdh->post_process_preset($this->vpre[2], array('%member_id%' => $a, '%dkp_id%' => $this->mdkpid, '%with_twink%' => !intval($this->config->get('show_twinks'))));
			$params2 = $this->pdh->post_process_preset($this->vpre[2], array('%member_id%' => $b, '%dkp_id%' => $this->mdkpid, '%with_twink%' => !intval($this->config->get('show_twinks'))));

			return $this->pdh->comp($this->vpre[0], $this->vpre[1], $this->sort_direction, $params1, $params2);
		}
	}//end class
}//end if
?>