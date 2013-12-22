<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2010
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

// EQdkp required files/vars
define('EQDKP_INC', true);
define('IN_ADMIN', true);

$eqdkp_root_path = './../';
include_once ($eqdkp_root_path . 'common.php');

/*
$pdh->enqueue_hook('test_hook');
$pdh->process_hook_queue();
$pdh->register_hook_callback('dummy', "test_hook");
$apa->register_test_hook();
function dummy(){
	echo("in dummy");
}
//d(uniqid("hallo_", false));
*/
class ManageAutoPoints extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'pdh', 'jquery', 'core', 'config', 'time', 'html', 'pdc', 'env', 'apa'=>'auto_point_adjustments');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public function __construct(){
		$this->user->check_auth('a_config_man');
		
		$handler = array(
			'recalc' => array('process' => 'recalculate'),
			'edit_func' => array('process' => 'edit_function'),
			'save_func'	=> array('process' => 'save_function', "csrf"=>true),
			'del_func'	=> array('process' => 'delete_function', "csrf"=>true),
		);
		parent::__construct(false, $handler);
		$this->process();
	}

	public function delete(){
		if($this->apa->del_apa($this->in->get('id'))) $message = array('text' => $this->user->lang('apa_del_suc'), 'title' => $this->user->lang('success'), 'color' => 'green');
		else $message = array('text' => $this->user->lang('apa_del_nosuc'), 'title' => $this->user->lang('error'), 'color' => 'red');
		$this->display($message);
	}

	public function update(){
		if($this->in->exists('id')) {
			$options = $this->apa->get_apa_edit_form($this->in->get('id'));
		} elseif($this->in->exists('type')) {
			$options = $this->apa->get_apa_add_form($this->in->get('type'));
		}
		$result = false;
		if (is_array($options) && $this->in->get('name') != ''){
			foreach ($options as $option){
				$options_array[$option['name']] = $this->in->get($option['name']);
				if($option['name'] == 'start_date') $options_array['start_date'] = $this->time->fromformat($options_array['start_date'], 1);
				if($option['name'] == 'pools') $options_array['pools'] = $this->in->getArray('pools', 'int');
				if($option['name'] == 'exectime') $options_array['exectime'] = $this->time->fromformat('01.01.70 '.$options_array['exectime'], 'd.m.y H:i');
			}
			if($this->in->exists('id')) {
				$result = $this->apa->update_apa($this->in->get('id'), $options_array);
			} else {
				$result = $this->apa->add_apa($this->in->get('type'), $options_array);
			}
		}
		$message = NULL;
		$this->tpl->add_js("jQuery.FrameDialog.closeDialog();");
		if(!$result) {
			$message = array('text' => $this->user->lang('apa_save_nosuc').'<br />'.$this->user->lang('apa_all_necessary'), 'title' => $this->user->lang('error'), 'color' => 'red', 'parent' => true);
		} else {
			$this->tpl->add_js("parent.location.href='manage_auto_points.php".$this->SID."'", 'docready');
		}
		$this->display($message);
	}

	public function recalculate(){
		$this->pdc->flush(); //maybe there will be more necessary if we support different types of apas
		$this->display(array('text' => $this->user->lang('apa_recalc_suc'), 'title' => $this->user->lang('success'), 'color' => 'green'));
	}
	
	public function delete_function() {
		$this->apa->delete_calc_function($this->in->get('func'));
		$this->display(array('text' => $this->user->lang('apa_func_del_suc'), 'title' => $this->user->lang('success'), 'color' => 'green'));
	}
	
	public function save_function() {
		$exprs = $this->in->getArray('exprs', 'raw');
		foreach($exprs as $key => &$expr) {
			if(!$expr = $this->parse_expr_in($expr)) {
				$error = $key;
				break;
			}
		}
		if(isset($error)) {
			$this->core->message(sprintf($this->user->lang('apa_func_error'), $error), $this->user->lang('error'), 'red');
			$this->edit_function($exprs);
		}
		//check for valid name
		if(preg_match('+[^a-zA-Z_0-9]+', $this->in->get('func'))) {
			$this->core->message($this->user->lang('apa_func_name_error'), $this->user->lang('error'), 'red');
			$this->edit_function($exprs);
		}
		$this->apa->update_calc_function('func_'.$this->in->get('func'), $exprs);
		$this->tpl->add_js("parent.$('body').data('func_name', '".$this->in->get('func')."');jQuery.FrameDialog.closeDialog();");
	}

	private function parse_expr_in($expr) {
		$syms = $this->apa->get_func_valid_symbols();
		$sym_str = '';
		foreach($syms as $sym) {
			if($sym == '(' || $sym == ')') continue;
			$sym_str .= preg_quote($sym).'|';
		}
		$sym_str = substr($sym_str, 0, -1);
		$replace_arr = $this->apa->get_calc_args();
		$replace_arr[] = 'Var';
		$replace_arr[] = 'pow';
		$re_ar = array();
		foreach($replace_arr as $key => $na) {
			$re_ar['~'.$key.'~'] = $na;
		}
		$expr = str_replace(array_values($re_ar), array_keys($re_ar), $expr);
		//remove all not allowed symbols (note: also letters not allowed here, thats why we replaced them in the line above)
		$regex = '~\s*[^\d\~\.'.preg_quote(implode('', $syms)).']*\s*~';
		if(version_compare(PHP_VERSION, '5.3.0', '<')) $regex = str_replace('-', '\-', $regex);
		$expr = preg_replace($regex, '', $expr);
		//rereplace the "allowed" words
		$expr = str_replace(array_keys($re_ar), array_values($re_ar), $expr);
		//remove double symbols and symbols from start / end of expression
		$expr = preg_replace('~\s*(^|'.$sym_str.')\s*('.$sym_str.')+~', '\1', $expr);
		$expr = preg_replace('~\s*('.$sym_str.')\s*($)+~', '', $expr);
		//add $ before our variables
		$expr = preg_replace('~\s*(Var[0-9]+|'.implode('|', $this->apa->get_calc_args()).')\s*(\(|\)|'.$sym_str.')+~', ' $\1 \2', $expr);
		$expr = preg_replace('~\s*(Var[0-9]+|'.implode('|', $this->apa->get_calc_args()).')\s*($)~', ' $\1', $expr);
		//set one space-character between all operations
		$expr = preg_replace('~\s*(\$[a-zA-Z0-9]+|\d*)\s*('.$sym_str.')\s*(\$[a-zA-Z0-9]+|\d*)~', '\1 \2 \3', $expr);
		//ensure paranthesis are set correctly
		$chars = count_chars($expr);
		if($chars[40] !== $chars[41]) return false;
		return trim($expr);
	}

	private function parse_expr_out($expr) {
		$expr = preg_replace('~\s*\$(Var[0-9]+|'.implode('|', $this->apa->get_calc_args()).')\s*~', ' \1 ', $expr);
		return trim($expr);
	}
	
	public function edit_function($function='') {
		if($this->in->get('func') && !$function) $function = $this->apa->get_calc_function($this->in->get('func'));
		if($function) {
			$last = max(array_keys($function));
			$last_helpvar = $last - 1;

			foreach($function as $key => $expr) {
				if ($key == $last){
					$this->tpl->assign_vars(array(
						'POINT_EXPR' => $this->parse_expr_out($expr),
					));
				} else {			
					$this->tpl->assign_block_vars('exprs', array(
						'VAR'	=> 'Var'.$key, 
						'EXPR'	=> $this->parse_expr_out($expr),
						'EXPR_LAST' => ($key == $last_helpvar) ? 'expr_last' : ''
					));
				}
			}
		} else {
			//$this->tpl->assign_block_vars('exprs', array('VAR' => 'Var0', 'EXPR' => '', 'NODEL' => true, 'EXPR_LAST' => ' expr_last'));
		}
		
		$examples = array(
			'no_sel'	=> $this->user->lang('apa_func_example_choose'),
			1			=> $this->user->lang('apa_func_example_1'),
			2			=> $this->user->lang('apa_func_example_2'),
		);
		
		$this->tpl->assign_vars(array(
			'FUNC_NAME'			=> str_replace('func_','',$this->in->get('func')),
			'EXAMPLE_FUNCS'		=> $this->html->DropDown('func_example', $examples, 'no_sel'),
			'AVAILABLE_ARGS'	=> $this->html->DropDown('func_args', $this->apa->get_calc_args(true), 'no_sel', '', '', 'input', '', array('no_sel')),
			'VALID_SYMBOLS'		=> implode("&nbsp;&nbsp;", $this->apa->get_func_valid_symbols()),
		));
		$this->tpl->add_js("
			var func_examples = new Array();
			func_examples['no_sel'] = '';
			func_examples[1] = \"value * 0.95\";
			func_examples[2] = \"value - 20\";
			
			$('#add_expr').click(function(){
				var row = $('#exprs_block > dl:last').clone();

				if (row.length == 0){
					newrow = $('#clone > dl:last').clone();
					$('#exprs_block').html(newrow);
				} else {
					var value = 'Var'+(parseInt($('.apa_var', row).text().substr(3))+1);
					$('.apa_var', row).empty().append(value);
					$('input', row).val('');
					$('#exprs_block > dl:last').after(row);
				}
			});
			
			$(document).on('focusout', '.expr_field', function(){
				$('.expr_last').removeClass('expr_last');
				$(this).addClass('expr_last');
			});
			$('#func_args').change(function(){
				$('.expr_last').val($('.expr_last').val() + ' ' + $(this).val());
			});
			
			$('#func_example').change(function(){
				$('.points').val(func_examples[$(this).val()]);
			});
			
			$(document).on('click', '.del_me', function(){
				$(this).parent().remove();
			});", 'docready');
		
		$this->core->set_vars(array (
			'page_title'		=> $this->user->lang('apa_manager'),
			'template_file'		=> 'admin/manage_auto_points_edit_function.html',
			'header_format'		=> $this->simple_head,
			'display'			=> true,
		));
	}

	//reminder: prohibit apas using different type but same pool AND module
	public function edit($foptions='', $ftype='') {
		if($this->in->exists('id')) {
			$options = $this->apa->get_apa_edit_form($this->in->get('id'));
			$type = $this->apa->get_data('type', $this->in->get('id'));
		} else {
			$type = $this->in->get('type');
			$options = $this->apa->get_apa_add_form($type);
		}
		$options = ($foptions) ? $foptions : $options;
		$type = ($ftype) ? $ftype : $type;
		if(!$options || !is_array($options)) $this->display();
		foreach ($options as $option){
			if($option['name'] == 'pools') {
				$option['options'] = $this->pdh->aget('multidkp', 'name', 0, array($this->pdh->get('multidkp', 'id_list')));
				if(!$this->in->exists('id')) {
					$used = $this->apa->get_pools_used($type);
					foreach($used as $dkpid) {
						if(!in_array($dkpid, $option['selected'])) unset($option['options'][$dkpid]);
					}
				}
			}
			if($option['type'] == 'dropdown' || $option['type'] == 'jq_multiselect') $option['selected'] = $option['value'];
			if($option['type'] == 'checkbox' && $option['value']) $option['selected'] = true;
			if($option['name'] == 'start_date') $option['value'] = $this->time->user_date($option['value'], true, false, false, function_exists('date_create_from_format'));
			if($option['name'] == 'exectime') $option['value'] = $this->time->date('H:i', $option['value']);
			$ccfield = $this->html->widget($option);
			$name = ($this->user->lang('apa_'.$type.'_'.$option['name'], false, false)) ? $this->user->lang('apa_'.$type.'_'.$option['name']) : '';
			$help = ($this->user->lang('apa_'.$type.'_'.$option['name'].'_help', false, false)) ? $this->user->lang('apa_'.$type.'_'.$option['name'].'_help') : '';
			$this->tpl->assign_block_vars('input', array(
				'NAME'		=> $name ? $name : $this->user->lang('apa_'.$option['name'], true, false),
				'HELP'		=> $help ? $help : $this->user->lang('apa_'.$option['name'].'_help', false, false),
				'FIELD'		=> $ccfield,
				'FUNC'		=> ($option['name'] == 'calc_func') ? true : false,
			));
			$name = $help = '';
		}
		$job_list = $this->apa->list_apas();
		
		//Add function button
		$beforeclose = "$('#calc_func').append('<option value=\"'+$('body').data('func_name')+'\">'+$('body').data('func_name')+'<option>');";
		$this->jquery->dialog('edit_function', $this->user->lang('apa_edit_function'), array('url' => "manage_auto_points.php".$this->SID."&simple_head=true&edit_func=true", 'width' =>'650', 'height' =>'600', 'beforeclose' => $beforeclose));
		$this->tpl->add_js("
			$('#add_func').click(function(){
				edit_function();
			});", 'docready');

		$this->jquery->Validate('apa_post', array(
			array('name' => 'name', 'value'=> $this->user->lang('apa_fv_name')),
			array('name' => 'calc_func', 'value'=> $this->user->lang('apa_fv_calc_func')),
			array('name' => 'exectime', 'value'=> $this->user->lang('apa_fv_exectime'))
		));
		//fetch events
		$events_dd = $this->pdh->aget('event', 'name', 0, array($this->pdh->get('event', 'id_list')));
		asort($events_dd);

		//fetch mdkppools
		$pool_dd = $this->pdh->aget('multidkp', 'name', 0, array($this->pdh->get('multidkp', 'id_list')));
		asort($pool_dd);
		
		$this->tpl->assign_vars(array(
			'HEAD_TEXT'		=> ($this->in->exists('id')) ? sprintf($this->user->lang('apa_edit'), "'".$this->user->lang('apa_of_type')." ".$this->user->lang('apa_type_'.$type)."'") : sprintf($this->user->lang('apa_add'), "'".$this->user->lang('apa_type_'.$type)."'"),
			'HIDDEN_NAME'	=> ($this->in->exists('id')) ? 'id' : 'type',
			'HIDDEN_VAL'	=> ($this->in->exists('id')) ? $this->in->get('id') : $type,
		));
		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('apa_manager'),
			'template_file'		=> 'admin/manage_auto_points_edit.html',
			'header_format'		=> $this->simple_head,
			'display'			=> true,
		));
	}

	public function display($messages = false){
		if($messages) $this->core->messages($messages);

		$job_list = $this->apa->list_apas();
		$used_funcs = array();
		if(is_array($job_list)){
			foreach($job_list as $key => $details){
				pd($details['exectime']);
				$this->tpl->assign_block_vars('apa_row', array(
					'ID'			=> $key,
					'TYPE'			=> $this->user->lang('apa_type_'.$details['type']),
					'NAME'			=> $details['name'],
					'EXECTIME'		=> $this->time->date('H:i', $details['exectime']),
					'POOLS'			=> implode(', ', $this->pdh->aget('multidkp', 'name', 0, array($details['pools']))),
				));
				if(isset($details['calc_func'])) $used_funcs[$details['calc_func']][] = $details['name'];
			}
		}
		
		$funcs = $this->apa->get_calc_function();
		if(is_array($funcs)){
			foreach($funcs as $name) {
				$this->tpl->assign_block_vars('func_row', array(
					'NAME'	=> $name,
					'USED'	=> (isset($used_funcs[$name])) ? implode(', ', $used_funcs[$name]) : '',
					'EXPL'	=> $this->apa->run_calc_func($name, array(100, $this->time->time, ($this->time->time-24*7*2*3600))),
					'NODEL'	=> (isset($used_funcs[$name])) ? true : false,
				));
			}
		}

		//Types
		$types = $this->apa->scan_types();
		$type_dd = array();
		foreach($types as $type) {
			$type_dd[$type] = $this->user->lang('apa_type_'.$type);
		}

		$this->jquery->dialog('apa_edit', sprintf($this->user->lang('apa_edit'), ''), array(
			'url' => "manage_auto_points.php".$this->SID."&simple_head=true&edit=true&id='+content+'",
			'width' =>'700',
			'height' =>'550',
			'withid' => 'content'
		));
		$this->jquery->dialog('apa_new', sprintf($this->user->lang('apa_new'), ''), array(
			'url' => "manage_auto_points.php".$this->SID."&simple_head=true&edit=true&type='+content+'",
			'width' =>'700',
			'height' =>'550',
			'withid' => 'content'
		));
		$this->jquery->dialog('func_edit', $this->user->lang('apa_edit_function'), array(
			'url' => "manage_auto_points.php".$this->SID."&simple_head=true&edit_func=true&func='+content+'",
			'width' =>'700',
			'height' =>'550',
			'withid' => 'content',
			'onclose' =>$this->env->link."admin/manage_auto_points.php".$this->SID
		));
		$this->jquery->dialog('func_new', $this->user->lang('apa_add_func'), array(
			'url' => "manage_auto_points.php".$this->SID."&simple_head=true&edit_func=true",
			'width' =>'700',
			'height' =>'550',
			'onclose' => $this->env->link."admin/manage_auto_points.php".$this->SID
		));
		$this->confirm_delete($this->user->lang('apa_confirm_delete'), "manage_auto_points.php".$this->SID."&id='+content+'", false, array('withid' => 'content', 'force_ajax' => true));
		$this->confirm_delete($this->user->lang('apa_confirm_delete_func'), "manage_auto_points.php".$this->SID."&func='+content+'", false, array('withid' => 'content', 'force_ajax' => true, 'function' => 'delete_func', 'handler' => 'del_func'));
		$this->tpl->add_js("
			$('#add_apa').click(function(){
				apa_new($('#apa_type').val());
			});
			$('.apa_edit').click(function(){
				apa_edit($(this).attr('alt'));
			});
			$('.apa_del').click(function(){
				delete_warning($(this).attr('alt'));
			});
			$('#add_func').click(function(){
				func_new($('#apa_type').val());
			});
			$('.func_edit').click(function(){
				console.log($(this).attr('alt'));
				func_edit($(this).attr('alt'));
			});
			$('.func_del').click(function(){
				delete_func($(this).attr('alt'));
			});", 'docready');
		
		$this->tpl->assign_vars(array (
			'L_APA_ADD'			=> sprintf($this->user->lang('apa_add'), ''),
			'TYPE_DD'			=> $this->html->DropDown('apa_type', $type_dd, false),
		));

		$this->core->set_vars(array (
			'page_title'		=> $this->user->lang('apa_manager'),
			'template_file'		=> 'admin/manage_auto_points.html',
			'header_format'		=> $this->simple_head,
			'display'			=> true,
		));
	}
	
	public function output_deletion_text() {
		header('content-type: text/html; charset=UTF-8');
		if($this->in->exists('id')) echo $this->apa->get_data('name', $this->in->get('id'));
		if($this->in->exists('func')) echo $this->in->get('func');
		exit;
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_ManageAutoPoints', ManageAutoPoints::__shortcuts());
registry::register('ManageAutoPoints');
?>