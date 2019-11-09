<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2016 EQdkp-Plus Developer Team
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

if ( !defined('EQDKP_INC') ){
	die('Do not access this file directly.');
}

if ( !class_exists( "html_leaderboard" ) ) {
	class html_leaderboard extends gen_class {
		private $mdkpid;
		private $vpre;

		public function get_html_leaderboard($mdkpid, $view_list, $settings, $with_twinks=false, $filter="") {
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
			$this->sort_direction = ($sort == 'asc') ? 'asc' : 'desc';

			//system dependant output
			$this->vpre = $this->pdh->pre_process_preset('current', array(), 0);

			//check if reset is necessary
			$reset_time = $this->timekeeper->get('lb_reset_times', $this->mdkpid);
			$needs_update = false;
			$used_modules[] = $this->vpre[0];
			$used_modules[] = 'member';

			foreach($used_modules as $module_name){
				if($this->pdh->module_needs_update($module_name) || $this->pdh->get_module_update_time($module_name) >= $reset_time){
					$needs_update = true;
					break;
				}
			}

			if($needs_update) {
				//reset
				$this->pdc->del_prefix('lb_'.$this->mdkpid);
				//put new update time
				$this->timekeeper->put('lb_reset_times', $this->mdkpid, time(), true);
			}

			$strCacheKey = md5('lb_'.$this->mdkpid.'_'.$column.'_'.$sort.'_'.$max_member.'_'.$filter);
			$cachedViewList = $this->pdc->get('lb_'.$this->mdkpid.'_'.$strCacheKey);

			$member_classes_mapping = $this->pdh->aget('member', $column, 0, array($view_list));

			$mdkp_sel = (new hdropdown('lb_mdkpid', array('options' => $this->pdh->aget('multidkp', 'name', 0, array($this->pdh->get('multidkp', 'id_list'))), 'js' => ' onchange="$(\'#lbc\').val(1); form.submit();"', 'value' => $this->mdkpid)))->output();

			$strAdminLink = ($this->user->check_auth('a_tables_man', false)) ? '<a href="'.$this->server_path.'admin/manage_pagelayouts.php'.$this->SID.'&edit=true&layout='.$this->config->get('eqdkp_layout').'#page-'.md5('listmembers').'" title="'.$this->user->lang('edit_table').'"><i class="fa fa-pencil floatRight"></i></a>' : '';

			$leaderboard = '<div id="toggleLeaderboard"><div class="tableHeader"><h2>'.$this->user->lang('leaderboard').$strAdminLink.'<span class="toggle_button"></span></h2></div><div class="toggle_container">'.$this->user->lang('select_leaderboard').': '.$mdkp_sel.'<div class="leaderboard">';
			$colnr = 0;

			$with_twinks = ($with_twinks === false) ? (!intval($this->config->get('show_twinks'))) : $with_twinks;

			if($cachedViewList !== null){
				$column_list = $cachedViewList;
			} else {
				$params = $this->pdh->post_process_preset( $this->vpre[2], array('%dkp_id%' => $this->mdkpid, '%with_twink%' => !intval($this->config->get('show_twinks'))));
				$myfulllist = $this->pdh->sort($view_list, $this->vpre[0], $this->vpre[1], $this->sort_direction, $params, 0);
				//key = memberid, val = classid
				$column_list = array();
				foreach($myfulllist as $key => $member_id){
					$col = $member_classes_mapping[$member_id];
					if(is_array($columns) && in_array($col, $columns) ){
						$column_list[$col][] = $member_id;
					}
				}

				//Put to Cache
				$this->pdc->put('lb_'.$this->mdkpid.'_'.$strCacheKey, $column_list);
			}


			if(count($column_list) < 1 || count(current($column_list)) < 1) return '';
			$intWidth = (count($column_list) < $break) ? floor(100 / count($column_list)) : floor(100 / $break);


			foreach($columns as $col) {
				if(!isset($column_list[$col])) continue;

				$member_ids = $column_list[$col];

				$leaderboard .= '<div class="floatLeft '.(($column == 'classid') ? 'leaderboard_class_'.$col : 'leaderboard_role_'.$col).'" style="width:'.$intWidth.'%"><div><table class="table fullwidth borderless nowrap colorswitch"><tr><th colspan="2">';
				$leaderboard .= ($column == 'classid') ? $this->game->decorate('primary', $col).' <span class="class_'.$col.'">'.$this->game->get_name('primary', $col).'</span>' : $this->game->decorate('roles', $col).' '.$this->pdh->get('roles', 'name', array($col));
				$leaderboard .= '</th></tr>';

				$rows = ($max_member < count($member_ids)) ? $max_member : count($member_ids);
				for($i=0; $i<$rows; $i++){
					$leaderboard .= '<tr><td class="left">'.$this->pdh->geth('member', 'memberlink', array($member_ids[$i], register('routing')->build('character', false, false,false), '', false,false,true,true)).'</td><td class="right">'.$this->pdh->geth($this->vpre[0], $this->vpre[1], $this->vpre[2], array('%member_id%' => $member_ids[$i], '%dkp_id%' => $this->mdkpid, '%use_controller%' => true, '%with_twink%' => $with_twinks)).'</td></tr>';
				}
				$leaderboard .= '</table></div></div>';
			}
			$this->jquery->Collapse('#toggleLeaderboard');
			$leaderboard .= '</div><div class="clear"></div></div></div>';

			return $leaderboard;
		}

	}//end class
}//end if
