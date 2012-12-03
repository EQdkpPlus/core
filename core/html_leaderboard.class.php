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
		public static $shortcuts = array('pdh', 'config', 'game', 'html', 'user');
		private $mdkpid;
		private $vpre;

		public function get_html_leaderboard($mdkpid, $view_list, $settings) {
			$arrMdkpIDList = $this->pdh->get('multidkp', 'id_list');
			$this->mdkpid = ($mdkpid) ? $mdkpid : $settings['default_pool'];
			$this->mdkpid = (in_array($this->mdkpid, $arrMdkpIDList)) ? $this->mdkpid : ((isset($arrMdkpIDList[0])) ? $arrMdkpIDList[0] : 0);
			$columns = $settings['columns'];
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

			$mdkp_sel = $this->html->DropDown('lb_mdkpid', $this->pdh->aget('multidkp', 'name', 0, array($this->pdh->get('multidkp', 'id_list'))), $this->mdkpid, '', ' onchange="form.submit();"');
			$leaderboard = '<table width="100%" border="0" cellpadding="1" cellspacing="1" class="colorswitch"><tr><th colspan="'.$break.'">'.$this->user->lang('leaderboard').': '.$mdkp_sel.'</th></tr><tr>';
			$colnr = 0;
			foreach($columns as $col) {
				if(!isset($column_list[$col])) continue;
				$member_ids = $column_list[$col];
				$leaderboard .= '<td align="center" valign="top" width="200"><table class="borderless nowrap" border="0" cellpadding="2" cellspacing="0" width="100%"><tr><th colspan="2">';
				$leaderboard .= ($column == 'classid') ? $this->game->decorate('classes', $col).' <span class="class_'.$col.'">'.$this->game->get_name('classes', $col).'</span>' : $this->game->decorate('roles', $col).' '.$this->pdh->get('roles', 'name', array($col));
				$leaderboard .= '</th></tr>';
				usort($member_ids, array(&$this, "sort_by_points"));

				$rows = ($max_member < count($member_ids)) ? $max_member : count($member_ids);
				for($i=0; $i<$rows; $i++){
					$leaderboard .= '<tr><td align="left">'.$this->pdh->geth('member', 'memberlink', array($member_ids[$i], 'viewcharacter.php', '', true, true)).'</td><td align="right">'.$this->pdh->geth($this->vpre[0], $this->vpre[1], $this->vpre[2], array('%member_id%' => $member_ids[$i], '%dkp_id%' => $this->mdkpid, '%with_twink%' => !intval($this->config->get('pk_show_twinks')))).'</td></tr>';
				}
				$leaderboard .= '</table></td>';
				$colnr++;
				if(($colnr % $break) == 0 && $colnr < count($column_list)) {
					$leaderboard .= '</tr><tr>';
				}
			}

			$leaderboard .= '</tr></table>';
			return $leaderboard;
		}

		private function sort_by_points($a, $b){
			//return $this->pdh->comp('points', 'current', -1, array($a, $this->mdkpid ), array($b, $this->mdkpid ));
			$params1 = $this->pdh->post_process_preset($this->vpre[2], array('%member_id%' => $a, '%dkp_id%' => $this->mdkpid, '%with_twink%' => !intval($this->config->get('pk_show_twinks'))));
			$params2 = $this->pdh->post_process_preset($this->vpre[2], array('%member_id%' => $b, '%dkp_id%' => $this->mdkpid, '%with_twink%' => !intval($this->config->get('pk_show_twinks'))));

			return $this->pdh->comp($this->vpre[0], $this->vpre[1], $this->sort_direction, $params1, $params2);
		}
	}//end class
}//end if
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_html_leaderboard', html_leaderboard::$shortcuts);
?>