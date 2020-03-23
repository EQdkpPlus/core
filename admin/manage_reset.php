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

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class reset_eqdkp extends page_generic {
	private $resets		= array('raids', 'events', 'items', 'itempools', 'adjustments', 'multipools', 'chars', 'plugins', 'portal', 'user', 'logs', 'calendar');
	private $dependency	= array(
		'events'	=> array('raids', 'items', 'multipools', 'calendar'),
		'raids'		=> array('items'),
		'chars'		=> array('raids', 'items', 'adjustments'),
		'itempools'	=> array('multipools')
	);
	private $toreset	= array();
	private $resetted	= array();
	private $modules	= array(
		'raids'			=> 'raid',
		'events'		=> 'event',
		'items'			=> 'item',
		'itempools'		=> 'itempool',
		'adjustments'	=> 'adjustment',
		'multipools'	=> 'multidkp',
		'chars'			=> 'member',
		'user'			=> 'user'
	);

	public function __construct(){
		$this->user->check_auth('a_reset');

		$handler = array(
			'event' => array('process' => 'consolidate_event'),
		);

		parent::__construct(false, $handler, 'plain', null, '_class_');
		$this->process();
	}

	public function consolidate_event(){
		@set_time_limit(0);
		@ignore_user_abort(true);

		$intEventID = $this->in->get('eventid', 0);
		$arrMembers = $this->pdh->get('member', 'id_list', array(false, false, false));

		$blnError = false;

		$arrRaidIDs = $this->pdh->get('raid', 'raidids4eventid', array($intEventID));

		//Create Backup
		$arrTables = $this->db->listTables();
		foreach($arrTables as $name){
			if (!$this->db->isEQdkpTable($name) || $name == $this->table_prefix.'logs') continue;
			$tables[$name] = $name;
		}

		$strBackupFile = $this->backup->createDatabaseBackup('zip', true, $tables, true);

		$this->db->beginTransaction();

		$arrGlobalLastValues = array();

		foreach($arrMembers as $intMemberID){
			$arrLastValues = array();
			//Raids
			$objRaids = $this->db->prepare("SELECT SUM(r.raid_value) as summe, MAX(r.raid_date) as max_date FROM __raids r, __raid_attendees ra WHERE ra.raid_id = r.raid_id AND ra.member_id = ? AND r.event_id=?")->execute($intMemberID, $intEventID);
			if($objRaids){
				$arrResult = $objRaids->fetchAssoc();
				$fltRaidValue = (float)$arrResult['summe'];
				$arrLastValues[] = (int)$arrResult['max_date'];
			} else {
				$blnError = true;
				break;
			}

			//Items
			if(count($arrRaidIDs)){
				$objItems = $this->db->prepare("SELECT SUM(i.item_value) as summe, MAX(i.item_date) as max_date FROM __items i WHERE i.member_id=? AND i.raid_id :in")->in($arrRaidIDs)->execute($intMemberID);
				if($objItems){
					$arrResult = $objItems->fetchAssoc();
					$fltItemValue = (float)$arrResult['summe'];
					$arrLastValues[] = (int)$arrResult['max_date'];
				} else {
					$blnError = true;
					break;
				}
			} else $fltItemValue = 0;

			//Adjustments
			if(count($arrRaidIDs)){
				$objAdjustments = $this->db->prepare("SELECT SUM(a.adjustment_value) AS summe, MAX(a.adjustment_date) as max_date FROM __adjustments a WHERE a.member_id = ? AND (a.event_id=? OR a.raid_id :in)")->in($arrRaidIDs)->execute($intMemberID, $intEventID);
			} else {
				$objAdjustments = $this->db->prepare("SELECT SUM(a.adjustment_value) AS summe, MAX(a.adjustment_date) as max_date FROM __adjustments a WHERE a.member_id = ? AND a.event_id=?")->execute($intMemberID, $intEventID);
			}
			if($objAdjustments){
				$arrResult = $objAdjustments->fetchAssoc();
				$fltAdjValue = (float)$arrResult['summe'];
				$arrLastValues[] = (int)$arrResult['max_date'];
			}else {
				$blnError = true;
				break;
			}

			//Create consolidation Adjustment
			$fltSum = $fltRaidValue - $fltItemValue + $fltAdjValue;

			if($fltSum != 0){

				$intLastTime = (count($arrLastValues)) ? max($arrLastValues) : $this->time->time;
				$arrGlobalLastValues[] = $intLastTime;

				$blnResult = $this->pdh->put('adjustment', 'add_adjustment', array($fltSum, $this->user->lang('consolidate').' '.$this->time->user_date($this->time->time), $intMemberID, $intEventID, 0, $intLastTime));
				if(!$blnResult){
					$blnError = true;
					break;
				}
			}
		}

		if($blnError){
			//Error Message
			$this->db->rollbackTransaction();

			$this->core->message($this->user->lang('consolidate_error'), '', 'red');
			$this->pdh->enqueue_hook('adjustment_update');
			$this->pdh->process_hook_queue();
		} else {
			$this->db->commitTransaction();

			//Delete the data
			$this->pdh->put('raid', 'delete_raidsofevent', array($intEventID));

			$this->db->prepare("DELETE FROM __adjustments WHERE event_id=? AND adjustment_date < ?")->execute($intEventID, min($arrGlobalLastValues)-300);

			$this->pdh->enqueue_hook('adjustment_update');
			$this->pdh->process_hook_queue();

			$this->core->message($this->user->lang('consolidate_success'), '', 'green');
		}

	}

	public function delete(){
		//Make a backup
		if($this->in->get('backup', 0)){
			$arrTables = $this->db->listTables();
			foreach($arrTables as $name){
				if (!$this->db->isEQdkpTable($name) || $name == $this->table_prefix.'logs') continue;
				$tables[$name] = $name;
			}

			$strBackupFile = $this->backup->createDatabaseBackup('zip', true, $tables, true);
		}

		$this->toreset = $this->in->getArray('selected', 'string');
		$log_action = array('{L_entries_reset}' => '');
		foreach ($this->toreset as $value){
			$this->reset_val($value);
			$log_action['{L_'.$value.'}'] = '';
		}
		$this->pdh->process_hook_queue();
		$this->pdc->flush();
		$this->core->message($this->user->lang('reset_success'), $this->user->lang('success'), 'green');
		$this->logs->add('action_reset', $log_action);
		$this->display();
	}

	public function reset_val($value) {
		if(!in_array($value, $this->resets) || in_array($value, $this->resetted)) return false;
		if(isset($this->dependency[$value])) {
			foreach($this->dependency[$value] as $dep) {
				if (!in_array($dep, $this->toreset)){
					$this->core->message($this->user->lang('reset_dependency_info'), 'Error', 'red');
					return false;
				}
				$this->reset_val($dep);
			}
		}
		$function = 'reset__'.$value;
		if(method_exists($this, $function)) {
			$this->$function();
		} else {
			if(isset($this->modules[$value])) $this->pdh->put($this->modules[$value], 'reset');
		}
		$this->resetted[] = $value;
		return true;
	}

	public function reset__plugins(){
		foreach($this->pm->get_plugins(PLUGIN_INSTALLED) as $value){
			$this->pm->uninstall($value);
		}
	}
	
	public function reset__portal(){
		//Delete all Portal-Layouts
		$arrLayouts = $this->pdh->get('portal_layouts', 'id_list', array());
		foreach ($arrLayouts as $intLayoutID){
			if($intLayoutID == 1) continue;
			
			$this->pdh->put('portal_layouts', 'delete', array($intLayoutID));
		}
		
		//Now delete all Portal Modules
		$arrModules = $this->pdh->aget('portal', 'portal', 0, array($this->pdh->get('portal', 'id_list')));
		foreach($arrModules as $id => $value){
			if((int)$value['child'] === 1) continue;
			$path = $value['path'];
			$plugin = $value['plugin'];
			
			$this->portal->uninstall($path, $plugin);
			
		}
	}

	public function reset__calendar() {
		$this->pdh->put('calendar_events', 'reset');
		$this->pdh->put('calendar_raids_attendees', 'reset');
		$this->pdh->put('calendar_raids_guests', 'reset');
		$this->pdh->put('calendar_raids_templates', 'reset');
		$this->pdh->put('calendars', 'reset');
	}

	public function reset__multipools(){
		$this->pdh->put('multidkp', 'reset');
		$this->db->prepare("INSERT INTO __multidkp (`multidkp_id`, `multidkp_name`, `multidkp_desc`) VALUES ('1', 'Default', 'Default-Pool');")->execute();
	}

	public function reset__logs() {
		$this->pdh->put('logs', 'truncate_log');
	}

	public function display(){
		foreach($this->resets as $type) {
			$this->tpl->assign_block_vars('reset_row', array(
				'TYPE'			=> $this->user->lang($type),
				'DISC'			=> $this->user->lang('reset_'.$type.'_disc'),
				'VAL_NAME'		=> $type,
			));
		}

		$js = "
			$('.cb_select_class').change(function(){
				if(!$(this).prop('checked')) return;
				var id = $(this).attr('id').substr(3);
				var dependency = new Array();
				";
		foreach($this->dependency as $key => $deps) {
			$js .= "dependency['".$key."'] = new Object();
				";
			foreach($deps as $dk => $dep) {
				$js .= "dependency['".$key."']['".$dk."'] = '".$dep."';
				";
			}
		}
		$js .= "if(typeof dependency[id] != undefined) {
					for(var current in dependency[id]) {
						current = dependency[id][current];
						if(!$('#cb_'+current).attr('checked')) {
							$('#cb_'+id).prop('checked', false);
							eval(id+'_warning()');
							break;
						}
					}
				}
			});
		";
		$this->tpl->add_js($js, 'docready');
		foreach(array_keys($this->dependency) as $key) {
			$custom_js = "$('#cb_".$key."').prop('checked', true);$('#cb_".implode("').prop('checked', true);$('#cb_", $this->dependency[$key])."').prop('checked', true);";
			$this->jquery->Dialog($key.'_warning', $this->user->lang('attention'), array('message'=> $this->user->lang('reset_'.$key.'_warning'), 'custom_js'	=> $custom_js, 'height' => 260), 'confirm');
		}
		$this->confirm_delete($this->user->lang('reset_confirm'));

		$this->jquery->Dialog('consolidate_warning', '', array('custom_js'=>"$(\"#consolidate_form\").submit();", 'message'=>$this->user->lang('consolidate_detele_warning')), 'confirm');


		$this->tpl->add_js('$("#reset_selectall").click(function(){
					var checked_status = this.checked;
					$(".cb_select_class").each(function(){
						this.checked = checked_status;
					});
				});', 'docready');

		$this->jquery->Tab_header('reset_tabs');

		$events = $this->pdh->aget('event', 'name', 0, array($this->pdh->get('event', 'id_list')));
		asort($events);

		$this->tpl->assign_vars(array(
			'DD_EVENT' => (new hdropdown('eventid', array('options' => $events)))->output(),
		));

		$this->core->set_vars([
			'page_title'		=> $this->user->lang('consolidate_reset'),
			'template_file'		=> 'admin/manage_reset.html',
			'page_path'			=> [
				['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
				['title'=>$this->user->lang('consolidate_reset'), 'url'=>' '],
			],
			'display'			=> true
		]);
	}
}
registry::register('reset_eqdkp');
