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

// EQdkp required files/vars
define('EQDKP_INC', true);
define('IN_ADMIN', true);

$eqdkp_root_path = './../';
include_once ($eqdkp_root_path . 'common.php');

class manage_cache extends page_generic {

	//private $pdc_cache_types =		array('none', 'file', 'xcache', 'memcache', 'apc');
	private $pdc_cache_types =		array('none', 'file', 'xcache', 'apc'); // removed memcache for now, as it does not work
	private $usable_cache_types = 	array('none', 'file', 'memcache');
	
	public function __construct() {
		$this->user->check_auth('a_config_man'); 
		$handler = array(
			'cache_clear' => array('process' => 'clear_cache', 'csrf'=>true),
			'cache_cleanup' => array('process' => 'cleanup_cache', 'csrf'=>true),
			'cache_save' => array('process' => 'save_cache', 'csrf'=>true)
		);
		parent::__construct(false, $handler);
		if(function_exists('apc_store') && function_exists('apc_fetch') && function_exists('apc_delete')) $this->usable_cache_types[] = 'apc';
		if(function_exists('xcache_set') && function_exists('xcache_get') && function_exists('xcache_unset')) $this->usable_cache_types[] = 'xcache';
		$this->process();
	}
	
	public function clear_cache() {
		$this->pdc->flush();
		$this->core->message($this->user->lang('pdc_clear'), $this->user->lang('success'), 'green');
		$this->display();
	}
	
	public function cleanup_cache() {
		$this->pdc->cleanup();
		$this->core->message($this->user->lang('pdc_cleanup'), $this->user->lang('success'), 'green');
		$this->display();
	}
	
	public function save_cache() {
		$selected_cache = $this->in->get('cache_selection', '');
		if(!in_array($selected_cache, $this->usable_cache_types)) {
			message_die("Invalid Cache selected!");
		} else {
			$pdc_array = $this->in->getArray('cache_'.$selected_cache, '');
			$pdc_array['mode'] = $selected_cache;
			$this->pdc->flush();
			$this->config->set($pdc_array, '', 'pdc');
			$this->core->message($this->user->lang('save_suc'), $this->user->lang('success'), 'green');
		}
	}
	
	public function display() {
		$current_cache = ($this->config->get('mode', 'pdc')) ? $this->config->get('mode', 'pdc') : 'none';

		foreach($this->pdc_cache_types as $cache_type){

			if(in_array($cache_type, $this->usable_cache_types)){
				$this->tpl->assign_block_vars('cache_selection_row', array(
					'VALUE'		=> $cache_type,
					'SELECTED'	=> ($cache_type == $current_cache) ? ' selected="selected"' : '',
					'OPTION'	=> ($cache_type == $current_cache) ? '* '.$this->user->lang("pdc_cache_name_$cache_type") : $this->user->lang("pdc_cache_name_$cache_type"),
				));
			}

			if($cache_type == $current_cache){
				$this->tpl->assign_var('DIV_CACHE_'.strtoupper($cache_type).'_VISIBLE', 'block');
			}else{
				$this->tpl->assign_var('DIV_CACHE_'.strtoupper($cache_type).'_VISIBLE', 'none');
			}

			if($this->config->get('dttl', 'pdc')){
				$this->tpl->assign_var('V_CACHE_'.strtoupper($cache_type).'_DTTL', $this->config->get('dttl', 'pdc'));
			}else{
				$this->tpl->assign_var('V_CACHE_'.strtoupper($cache_type).'_DTTL', 86400);
			}

			if($this->config->get('prefix', 'pdc')){
				$this->tpl->assign_var('V_CACHE_'.strtoupper($cache_type).'_PREFIX', $this->config->get('prefix', 'pdc'));
			}else{
				$this->tpl->assign_var('V_CACHE_'.strtoupper($cache_type).'_PREFIX', $this->table_prefix);
			}

			if($cache_type == 'memcache'){
				if($this->config->get('server', 'pdc')){
					$this->tpl->assign_var('V_CACHE_'.strtoupper($cache_type).'_SERVER', $this->config->get('server', 'pdc'));
				}else{
					$this->tpl->assign_var('V_CACHE_'.strtoupper($cache_type).'_SERVER', '127.0.0.1');
				}
				if($this->config->get('port', 'pdc')){
					$this->tpl->assign_var('V_CACHE_'.strtoupper($cache_type).'_PORT', $this->config->get('port', 'pdc'));
				}else{
					$this->tpl->assign_var('V_CACHE_'.strtoupper($cache_type).'_PORT', '11211');
				}
			}
		}
		//TODO: Remove when memcache works
		$this->tpl->assign_var('DIV_CACHE_MEMCACHE_VISIBLE', 'none');
		
		$this->tpl->add_js("
			$('#cache_select').change(function(){
				$('#all_cache_divs > div').each(function(){
					$(this).css('display', 'none');
				});
				$('#div_cache_'+$(this).val()).css('display', 'block');
			});", 'docready');

		$cache_list = $this->pdc->listing();
		$total = 0;
		$ctime = time();
		
		foreach($cache_list as $global_prefix => $keys){
			foreach($keys as $key => $expiry_date){
				$expiry_date = $expiry_date['exp_date'];
				$this->tpl->assign_block_vars('cache_entity_list_row', array (
					'GLOBAL_PREFIX'		=> $global_prefix,
					'KEY'				=> $key,
					'EXPIRED'			=> ($expiry_date < $ctime) ? '<span class="negative">'.$this->user->lang('pdc_entity_expired').'</span>':'<span class="positive">'.$this->user->lang('pdc_entity_valid').'</span>',
					'EXPIRY_DATE'		=> $this->time->date("Y-m-d H:i:s", $expiry_date),
				));
			}
		}

		$this->tpl->assign_vars(array (
			'L_CACHE_TABLE_INFO'		=> sprintf($this->user->lang('pdc_table_info'), $this->user->lang("pdc_cache_name_$current_cache")),
			'PDC_CACHE_ENABLED'			=> ($current_cache == 'none') ? false : true,  
		));


		$this->core->set_vars(array (
			'page_title'		=> $this->user->lang('pdc_manager'),
			'template_file'		=> 'admin/manage_cache.html',
			'display'			=> true
		));
	}
}
registry::register('manage_cache');
?>