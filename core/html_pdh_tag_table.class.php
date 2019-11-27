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

if ( !class_exists( "html_pdh_tag_table" ) ) {
	class html_pdh_tag_table extends gen_class {

		private $columns				= array();
		private $sort_cid				= 1;
		private $sort_direction			= 'asc';
		private $super_row				= array();

		private $view_list				= array();
		private $filtered_list			= array();
		private $full_list				= array();

		private $counter				= 1;
		private $sub_array				= null;
		private $id_tag;
		private $total_count			= 0;

		//lets try some more caching
		private $page;
		private $cached_table_rows		= array();
		private $not_cached_row_count	= 0;
		private $cache_suffix			= '';
		private $cache_time				= null;
		private $settings				= array();

		private $dt_cssjs 				= false;
		private $dt_arrow 				= false;

		public function __construct($hptt_settings, $full_list, $filtered_list, $sub_array, $cache_suffix = '', $sort_suffix = 'sort'){
			$this->settings			= $hptt_settings;
			$this->id_tag			= $hptt_settings['table_main_sub'];
			$this->sub_array		= $sub_array;
			$this->sub_array['%no_root%'] = (isset($hptt_settings['no_root']) && $hptt_settings['no_root']) ? true : false;
			$this->initialise($hptt_settings);
			$this->full_list		= $full_list;
			$this->filtered_list	= $filtered_list;
			$this->cache_suffix		= '_'.$this->user->lang_name.(($cache_suffix != '') ? '_'.$cache_suffix : '');
			$this->sort_suffix		= $sort_suffix;

		}

		private function initialise(){
			$this->page = $this->settings['name'];

			$this->sort_cid			= isset($this->settings['table_sort_col']) ? $this->settings['table_sort_col'] : -1;
			$this->sort_direction	= isset($this->settings['table_sort_dir']) ? $this->settings['table_sort_dir'] : 'asc';
			$this->super_row		= isset($this->settings['super_row']) ? $this->settings['super_row'] : array();

			$used_modules = array();
			foreach($this->settings['table_presets'] as $preset){
				$pre = $this->pdh->pre_process_preset($preset['name'], $preset);				
				if(empty($pre))
					continue;
				$this->columns	= array_merge($this->columns, $pre);
				$used_modules[]	= $pre[0][0];
			}

			//Calculate the number of shown columns
			$this->column_count = count($this->columns);
			if($this->settings['show_select_boxes']){
				$this->column_count++;
			}
			if(isset($this->settings['show_numbers']) && $this->settings['show_numbers']){
				$this->column_count++;
			}

			//check if reset is necessary
			$reset_time = $this->timekeeper->get('hptt_reset_times', $this->page);
			$needs_update = false;
			foreach($used_modules as $module_name){
				if($this->pdh->module_needs_update($module_name) || $this->pdh->get_module_update_time($module_name) >= $reset_time){
					$needs_update = true;
					break;
				}
			}

			if($needs_update) {
				//reset
				$this->pdc->del_prefix($this->page.$this->cache_suffix);
				//put new update time
				$this->timekeeper->put('hptt_reset_times', $this->page, time(), true);
			}

			//Check if reset for APA is necessary
			$nextApaRun = register('auto_point_adjustments')->get_next_aparuns();
			if($nextApaRun ){
				$this->cache_time = $nextApaRun - $this->time->time + 360;
			}
		}

		public function get_column_count(){
			return $this->column_count;
		}

		public function get_row_count(){
			return count($this->full_list);
		}

		public function setPageRef($strPageRef){
			$this->settings['page_ref'] = $strPageRef;
		}

		private function sort_view_list($sort_string){
			if($sort_string != ''){
				list($this->sort_cid, $this->sort_direction) = explode('|', $sort_string);
			}else if($this->sort_cid == -1){
				//Sort ascending by the first sortable column
				foreach($this->columns as $cid => $column){
					if($column['sort'] == true){
						$this->sort_cid = $cid;
						break;
					}
				}
			}

			if(array_key_exists($this->sort_cid, $this->columns)){
				//don't use the cached data if a used module is outdated
				$cached_view_list = $this->pdc->get($this->page.$this->cache_suffix.'_vlc_'.$this->sort_cid.'_'.$this->sort_direction);
				if($cached_view_list === NULL){
					$id_position = array_search($this->id_tag, $this->columns[$this->sort_cid]['2']);
					$params = $this->pdh->post_process_preset($this->columns[$this->sort_cid]['2'], $this->sub_array);
					$this->full_list = $this->pdh->sort($this->full_list, $this->columns[$this->sort_cid]['0'], $this->columns[$this->sort_cid]['1'], $this->sort_direction, $params, $id_position);
					$this->pdc->put($this->page.$this->cache_suffix.'_vlc_'.$this->sort_cid.'_'.$this->sort_direction, $this->full_list, $this->cache_time);
				}else{
					$this->full_list = $cached_view_list;
				}

				if(!empty($this->full_list)) $this->view_list = array_intersect($this->full_list, $this->filtered_list);
			}
		}

		public function paginate($pagination_start=0, $pagination_length=1){
			$this->counter = $pagination_start+1;
			$this->total_count = count($this->view_list);
			if(!empty($this->view_list)) $this->view_list = array_slice($this->view_list, $pagination_start, $pagination_length);
		}

		public function get_html_table($sort_string = '', $url_suffix = '', $pagination_start = null, $pagination_length = 1, $footer_text = null, $no_footer=false, $arrCheckboxCheck=false){
			$this->sort_view_list($sort_string);

			if(isset($pagination_start))
				$this->paginate($pagination_start, $pagination_length);
			$table	 = $this->get_html_super_header_row();
			$table	 .= $this->get_html_header_row($url_suffix, $this->settings['show_select_boxes'], $this->settings['show_numbers']);
			$table	 .= $this->get_html_table_body($arrCheckboxCheck);
			if(!count($this->view_list)) $table .= $this->get_html_empty_row();

			if(!$no_footer && $footer_text !== false) $table	 .= $this->get_html_footer_row($footer_text);
			if($this->sub_array['%no_root%']) $table = str_replace('{ROOT_PATH}', $this->root_path, $table);
			return $table;
		}

		public function get_html_table_body($arrCheckboxCheck=false){
			$table = '';

			$this->cached_table_rows = $this->pdc->get($this->page.$this->cache_suffix);
			if($this->cached_table_rows === NULL){
				$this->cached_table_rows = array();
			}

			if(!empty($this->view_list)) {
				foreach($this->view_list as $view_id){
					$table .= $this->get_html_row($view_id,$arrCheckboxCheck);
				}
			}

			if($this->not_cached_row_count > 0){
				$this->pdc->put($this->page.$this->cache_suffix, $this->cached_table_rows, $this->cache_time);
			}
			return $table;
		}

		public function get_html_super_header_row($super_row = null){
			if(!isset($super_row)){
				$super_row = $this->super_row;
			}

			$html_super_row = '';
			if(is_array($super_row) && !empty($super_row)){
				$html_super_row .= '<tr>';
				foreach($super_row as $definition){
					$html_super_row .= '<th colspan="'.$definition['colspan'].'" align="'.$definition['align'].'">'.$definition['text'].'</th>';
				}
				$html_super_row .= '</tr>';
			}
			return $html_super_row;
		}

		public function get_html_header_row($url_suffix){
			//columns
			$header = '';
			foreach($this->columns as $cid => $column){
				$module		= $column[0];
				$tag		= $column[1];
				$params		= $this->pdh->post_process_preset($column[3], $this->sub_array);
				$th_add		= $column['th_add'];
				$caption = $this->pdh->get_html_caption($module, $tag, $params, $column['name']);
				if($column['sort'] == true){
					if($this->sort_cid == $cid){
						if($this->sort_direction == 'asc'){
							$sort_asc = '<a href="'.$this->settings['page_ref'].$this->SID.'&amp;'.$this->sort_suffix.'='.$cid.'%7casc'.$url_suffix.'" class="down_arrow_red"></a>';
							$sort_desc = '<a href="'.$this->settings['page_ref'].$this->SID.'&amp;'.$this->sort_suffix.'='.$cid.'%7cdesc'.$url_suffix.'" class="up_arrow_green"></a>';
							$sort_toggle = '<a href="'.$this->settings['page_ref'].$this->SID.'&amp;'.$this->sort_suffix.'='.$cid.'%7cdesc'.$url_suffix.'" >'.$caption.'</a>';
						}else{
							$sort_asc = '<a href="'.$this->settings['page_ref'].$this->SID.'&amp;'.$this->sort_suffix.'='.$cid.'%7casc'.$url_suffix.'" class="down_arrow_green"></a>';
							$sort_desc = '<a href="'.$this->settings['page_ref'].$this->SID.'&amp;'.$this->sort_suffix.'='.$cid.'%7cdesc'.$url_suffix.'" class="up_arrow_red"></a>';
							$sort_toggle = '<a href="'.$this->settings['page_ref'].$this->SID.'&amp;'.$this->sort_suffix.'='.$cid.'%7casc'.$url_suffix.'" >'.$caption.'</a>';
						}
					}else{
						$sort_asc = '<a href="'.$this->settings['page_ref'].$this->SID.'&amp;'.$this->sort_suffix.'='.$cid.'%7casc'.$url_suffix.'" class="down_arrow_green"></a>';
						$sort_desc = '<a href="'.$this->settings['page_ref'].$this->SID.'&amp;'.$this->sort_suffix.'='.$cid.'%7cdesc'.$url_suffix.'" class="up_arrow_green"></a>';
						$sort_toggle = '<a href="'.$this->settings['page_ref'].$this->SID.'&amp;'.$this->sort_suffix.'='.$cid.'%7casc'.$url_suffix.'" >'.$caption.'</a>';
					}
					$header .= "\t<th ".$th_add.'>'.$sort_asc.$sort_desc.' '.$sort_toggle."</th>\n";
				}else{
					$header .= "\t<th ".$th_add.'>'.$caption."</th>\n";
				}
			}

			$prefix = '';
			$hptt_checkboxname	= (isset($this->settings['selectbox_name'])) ? $this->settings['selectbox_name'] : 'selected_ids';
			if(($this->settings['show_select_boxes'] != 'signedin' && $this->settings['show_select_boxes'] == true) || ($this->settings['show_select_boxes'] == 'signedin' && $this->user->id > 0)){
				if(isset($this->settings['selectboxes_checkall']) && $this->settings['selectboxes_checkall']){
					$selAllCheckboxName = "pdh_selectall".$this->counter."_".substr(md5($hptt_checkboxname), 0, 7);
					$this->jquery->selectall_checkbox($selAllCheckboxName, $hptt_checkboxname.'[]');
					$prefix .= "\t<th style='width:13px'><input type='checkbox' id='".$selAllCheckboxName."' name='pdh_selectall' value='pdh_selectall' /></th>\n";
				}else{
					$prefix .= "\t<th style='width:13px'>&nbsp;</th>\n";
				}
			}
			if(isset($this->settings['show_numbers']) && $this->settings['show_numbers']){
				$prefix .= "\t<th>#</th>\n";
			}
			return "<tr>\n{$prefix}{$header}</tr>\n";
		}

		public function get_html_row($view_id,$arrCheckboxCheck=false){
			$prefix = '';
			$hptt_checkboxname	= (isset($this->settings['selectbox_name'])) ? $this->settings['selectbox_name'] : 'selected_ids';
			if(($this->settings['show_select_boxes'] != 'signedin' && $this->settings['show_select_boxes'] == true) || ($this->settings['show_select_boxes'] == 'signedin' && $this->user->id > 0)){
				$blnShowCheckbox = true;
				if ($arrCheckboxCheck !== false) {
					$blnShowCheckbox = $this->pdh->get($arrCheckboxCheck[0],$arrCheckboxCheck[1],array($view_id));
				}
				$prefix  .= ($blnShowCheckbox) ? "\t".'<td class="nowrap" align="center"><input type="checkbox" name="'.$hptt_checkboxname.'[]" value="'.((isset($this->settings['selectbox_valueprefix'])) ? $this->settings['selectbox_valueprefix'] : '').$view_id.'" id="cbrow'.$this->counter.'" /></td>'."\n" : '<td></td>';
			}
			if(isset($this->settings['show_numbers']) && $this->settings['show_numbers']){
				$prefix .= "\t".'<td><div style="float:right;">'.$this->counter.'</div></td>'."\n";
			}

			//add css/js for detail_twink if necessary
			$this->detail_twink_css_js();

			if(isset($this->cached_table_rows[$view_id])){
				$view_row = $this->cached_table_rows[$view_id];
				$view_row = str_replace("{SID}", $this->SID, $view_row);
			}else{
				$view_row = '';
				foreach($this->columns as $cid => $column){
					$module = $column[0];
					$tag = $column[1];
					$params = $column[2];
					$td_add = $column['td_add'];
					$this->sub_array[$this->id_tag] = $view_id;

					if(strpos($td_add, 'class') !== false){
						$td_add = preg_replace("/class=\"(.*)\"/U", "class=\"$1 twinktd\"", $td_add);
					}

					$view_row .= "\t".'<td '.$td_add.'>';
					if($this->config->get('detail_twink') AND $this->settings['show_detail_twink']) {
						$view_row .= $this->detail_twink(array_search('%member_id%', $params, true), array_search('%with_twink%', $params, true), $cid, $module, $tag, $params);
					} elseif(isset($this->settings['perm_detail_twink']) && $this->settings['perm_detail_twink']) {
						$view_row .= $this->detail_twink(array_search('%member_id%', $params, true), array_search('%with_twink%', $params, true), $cid, $module, $tag, $params);
					} else {
						if(isset($column['after'])){
							$arrAfterProcessed = $this->process_after($column['after'][0], $column['after'][1], $module, $tag, $params);
							$module = $arrAfterProcessed[0];
							$tag = $arrAfterProcessed[1];
							$params = $arrAfterProcessed[2];
							$this->sub_array[$arrAfterProcessed[3]] = $arrAfterProcessed[4];
						}
						
						$view_row .= $this->pdh->geth($module, $tag, $params, $this->sub_array);
					}
					
					
					$view_row .= "</td>\n";
				}
				$this->dt_arrow = false;
				$this->cached_table_rows[$view_id] = str_replace($this->SID, "{SID}", $view_row);
				$this->not_cached_row_count++;
			}

			$row = '<tr>'."\n".$prefix.$view_row."</tr>\n";
			$this->counter++;
			return $row;
		}
		
		public function process_after($strPreset, $strSubID, $module, $tag, $params){
			$mixValue = $this->pdh->get($module, $tag, $params, $this->sub_array);

			$pre = $this->pdh->pre_process_preset($strPreset);
			
			$module = $pre[0][0];
			$tag = $pre[0][1];
			$params = $pre[0][2];
			
			$this->sub_array[$strSubID] = $mixValue;
			
			if(isset($pre[0]['after'])){
				$arrAfterProcessed = $this->process_after($pre[0]['after'][0], $pre[0]['after'][1], $pre[0][0], $pre[0][1], $pre[0][2]);
				$module = $arrAfterProcessed[0];
				$tag = $arrAfterProcessed[1];
				$params = $arrAfterProcessed[2];
				$strSubID = $arrAfterProcessed[3];
				$mixValue = $arrAfterProcessed[4];
			}
			
			return array(
					$module, $tag, $params, $strSubID, $mixValue
			);
		}

		public function detail_twink_data($main_id_key, $wt_key, $type, $module, $tag, $params, $sub_arr = null) {
			if(!is_array($params)) $params = array($params);
			if($sub_arr != null) {
				$params = $this->pdh->post_process_preset( $params, $sub_arr );
			}
			if($wt_key !== false) {
				$params[$wt_key] = false; //single values everywhere
			}
			$data = array();
			$main_id = $params[$main_id_key];
			$members = $this->pdh->get('member', 'other_members', $main_id);
			$members[] = $main_id;
			$id_position = array_search($this->id_tag, $this->columns[$this->sort_cid]['2']);
			$sort_params = $this->pdh->post_process_preset($this->columns[$this->sort_cid]['2'], $this->sub_array);
			$sort_wt_pos = array_search('%with_twink%', $sort_params, true);
			$sort_params[$sort_wt_pos] = false;
			$members = $this->pdh->sort($members, $this->columns[$this->sort_cid]['0'], $this->columns[$this->sort_cid]['1'], $this->sort_direction, $sort_params, $id_position);
			$method = 'get_html_'.$tag;
			foreach($members as $member_id) {
				if($member_id) {
					$params[$main_id_key] = $member_id;
					$data[$member_id] = $this->pdh->geth($module, $tag, $params);
				}
			}
			if(strpos($type, 'lang:') !== false) {
				$data[0] = $this->pdh->get_lang($module, substr($type, 5));
			} elseif($type == 'summed_up') {
				$params[$wt_key] = true;
				$data[0] = $this->pdh->geth($module, $tag, $params);
			} else {
				$data[0] = '&nbsp;';
			}
			return $data;
		}

		public function detail_twink($view_id_key, $wt_key, $cid, $module, $tag, $params) {
			$dt_tags = $this->pdh->get_dt_tags($module);
			if (!$dt_tags) $dt_tags = array();
			$member_id = $this->sub_array['%member_id%'];
			if(!array_key_exists($tag, $dt_tags) || !$this->pdh->get('member', 'other_members', $member_id)) {
				return $this->pdh->geth($module, $tag, $params, $this->sub_array); //no detail-twink available for this tag
			}
			$data = $this->detail_twink_data($view_id_key, $wt_key, $dt_tags[$tag], $module, $tag, $params, $this->sub_array);
			$add = '';
			if(!$this->dt_arrow && !$this->settings['perm_detail_twink']) {
				$add = ' <i class="fa fa-caret-square-o-down fa-fw toggle_members" id="toggle_member_'.$member_id.'""></i> ';
				$this->dt_arrow = true;
			}
			$default = '<div style="height:20px; padding:1px; white-space:nowrap; display:inline-block;" class="def_toggle_member_'.$member_id.'">'.$data[(($dt_tags[$tag] == 'summed_up') ? 0 : $member_id)].'</div>'.$add;
			$sub_start = '<div class="toggle_member_'.$member_id.'" style="display:'.(($this->settings['perm_detail_twink']) ? 'block' : 'none').';">';
			$sub = '';
			foreach($data as $mem_id => $out) {
				$sub .= '<div style="height:20px; padding:1px; white-space: nowrap;">'.$out.'</div>';
			}
			$sub_end = '</div>';
			if($this->settings['perm_detail_twink']) {
				return $sub_start.$sub.$sub_end;
			}
			return $default.$sub_start.$sub.$sub_end;
		}

		private function detail_twink_css_js() {
			if(!$this->dt_cssjs AND ($this->config->get('detail_twink') AND $this->settings['show_detail_twink'])) {
				$this->tpl->add_css('.toggle_members { cursor: default; width: 10px; height: 10px; }');

				$this->tpl->add_js("$('.toggle_members').on('click', function(){
					if($(this).hasClass('fa-caret-square-o-up')){
						//show
						$('.'+$(this).attr('id')).hide();
						$('.def_'+$(this).attr('id')).show();
						$(this).removeClass('fa-caret-square-o-up');
						$(this).addClass('fa-caret-square-o-down');
					} else {
						//hide
						$('.'+$(this).attr('id')).attr('style', 'display:inline-block;');
						$('.def_'+$(this).attr('id')).hide();
						$(this).removeClass('fa-caret-square-o-down');
						$(this).addClass('fa-caret-square-o-up');
					}
				})");



				$this->dt_cssjs = true;
			}
		}

		public function get_html_footer_row($footer_text){
			$rowCount = count($this->columns);
			if($this->settings['show_select_boxes']) $rowCount++;
			if($this->settings['show_numbers']) $rowCount++;

			$footer  = "<tr>\n\t<th colspan=\"".$rowCount."\" class=\"footer\">";
			if($footer_text == null){
				$count = count($this->view_list);
				$footer .= ($this->total_count > $count) ? sprintf($this->user->lang('hptt_default_part_footcount'), $count, $this->total_count) : sprintf($this->user->lang('hptt_default_footcount'), $count);
			}else{
				$footer .= $footer_text;
			}
			$footer .= "</th>\n</tr>\n";
			return $footer;
		}

		public function get_html_empty_row(){
			$rowCount = count($this->columns);
			if($this->settings['show_select_boxes']) $rowCount++;
			if($this->settings['show_numbers']) $rowCount++;

			$footer  = "<tr>\n\t<td colspan=\"".$rowCount."\" class=\"emptyTable\"><span style='font-style:italic;'>";
			$footer .= $this->user->lang('hptt_empty_table');
			$footer .= "</span></td>\n</tr>\n";
			return $footer;
		}

		public function get_view_list(){
			return $this->view_list;
		}
	}//end class
}//end if
