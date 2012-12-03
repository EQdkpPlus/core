<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		    http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2010
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2010 sz3
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev$
 * 
 * $Id$
 */

// EQdkp required files/vars
define('EQDKP_INC', true);
define('IN_ADMIN', true);

$eqdkp_root_path = './../';
include_once ($eqdkp_root_path . 'common.php');

// Check user permission
$user->check_auth('a_config_man');

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
class ManageAutoPoints extends EQdkp_Admin {

				
	function ManageAutoPoints()
    {
        global $db, $core, $user, $tpl, $pm, $timekeeper, $eqdkp_root_path;
        global $SID;

        parent::eqdkp_admin();
				


        $this->assoc_buttons(array(				
            'add' => array(
                'name'    => 'add',
                'process' => 'process_add',
                'check'   => 'a_config_man'),
						 'edit' => array(
                'name'    => 'edit',
                'process' => 'process_edit_save',
                'check'   => 'a_config_man'),		
								
						'form' => array(
                'name'    => '',
                'process' => 'display_list',
                'check'   => 'a_config_man')
				));

        $this->assoc_params(array(
            'add' => array(
                'name'    => 'mode',
                'value'   => 'add',
                'process' => 'process_options',
                'check'   => 'a_config_man'),
						'edit' => array(
                'name'    => 'mode',
                'value'   => 'edit',
                'process' => 'process_edit',
                'check'   => 'a_config_man'),
						'delete' => array(
                'name'    => 'mode',
                'value'   => 'delete',
                'process' => 'process_delete',
                'check'   => 'a_config_man'),
						'list' => array(
                'name'    => 'mode',
                'value'   => 'list',
                'process' => 'process_list_adjustments',
                'check'   => 'a_config_man'),
						'recalculate' => array(
                'name'    => 'mode',
                'value'   => 'recalculate',
                'process' => 'process_recalculate',
                'check'   => 'a_config_man'),

        ));

    }

    function error_check()
    {
        return false;
    }
		
		function process_options(){
			global $db, $core, $user, $tpl, $pm, $SID, $jquery, $eqdkp_root_path, $html, $pcache, $apa, $pdh, $in;
			
			$options = $apa->get_apa_add_form($in->get('type'));
			if (is_array($options)){
				foreach ($options as $option){
					$ccfield = $html->widget($option);

					$tpl->assign_block_vars('option_row', array(
            'NAME'      => $option['name'],
            'FIELD'     => $ccfield,
          ));
				}
			}
		
		
			$tpl->assign_vars(array(
				'S_EDIT'	=> true,
				'S_ADD'		=> true,
				'F_HIDDEN_OPTIONS'	=> base64_encode(serialize(array($in->get('type'), $in->get('desc'), $in->get('pool'), $in->get('event')))),
				'L_APA_ADD' => $user->lang['apa_add'],
			));
			
			$core->set_vars(array (
				'page_title' => $user->lang['apa_manager'],
				'template_file' => 'admin/manage_auto_points.html',
				'header_format'	=> 'simple',
				'display' => true,
				)
			);
		
		}
		
		function process_add(){
			global $db, $core, $user, $tpl, $pm, $SID, $jquery, $eqdkp_root_path, $html, $pcache, $apa, $pdh, $in;
			
			list($type, $reason, $pool, $event) = unserialize(base64_decode($in->get('hidden_options')));
			$options = $apa->get_apa_add_form($type);
			if (is_array($options)){
				foreach ($options as $option){
					$options_array[$option['name']] = $html->widget_return($option);
				}
			}
			$apa->add_apa($pool, $event, $type, $reason, $options_array);
			
			$tpl->add_js("parent.window.location.href = 'manage_auto_points.php';");
			
		}
		
		function process_edit(){
			global $db, $core, $user, $tpl, $pm, $SID, $jquery, $eqdkp_root_path, $html, $pcache, $apa, $pdh, $in;
			
			$options = $apa->get_apa_edit_form($in->get('id'));
			if (is_array($options)){
				foreach ($options as $option){
					$ccfield = $html->widget($option);

					$tpl->assign_block_vars('option_row', array(
            'NAME'      => $option['name'],
            'FIELD'     => $ccfield,
          ));
				}
			}
			
			$job_list = $apa->list_apas();
			
			//fetch events
			$events_dd = $pdh->aget('event', 'name', 0, array($pdh->get('event', 'id_list')));
			asort($events_dd);
			
			//fetch mdkppools
			$pool_dd = $pdh->aget('multidkp', 'name', 0, array($pdh->get('multidkp', 'id_list')));
			asort($pool_dd);
			
			$tpl->assign_vars(array(
				'S_EDIT'	=> true,
				'POOL_DD'	=> $html->DropDown('apa_pool', $pool_dd, $job_list[$in->get('id')]['pool']),
				'EVENT_DD'	=> $html->DropDown('apa_event', $events_dd, $job_list[$in->get('id')]['event']),
				'APA_REASON'		=> $job_list[$in->get('id')]['reason'],
				'L_SAVE'	=> $user->lang['save'],
				'L_APA_POOL' => $user->lang['apa_pool'],
				'L_APA_EVENT' => $user->lang['apa_event'],
				'L_APA_REASON' => $user->lang['apa_reason'],
				'F_ACTION'	=> 'manage_auto_points.php?'.$SID.'&id='.sanitize($in->get('id')).'&type='.$job_list[$in->get('id')]['type'],
			));
			
			$core->set_vars(array (
				'page_title' => $user->lang['apa_manager'],
				'template_file' => 'admin/manage_auto_points.html',
				'header_format'	=> 'simple',
				'display' => true,
				)
			);	
		}
		
		function process_edit_save(){
			global $db, $core, $user, $tpl, $pm, $SID, $jquery, $eqdkp_root_path, $html, $pcache, $apa, $pdh, $in;
			
			$options = $apa->get_apa_edit_form($in->get('id'));
			if (is_array($options)){
				foreach ($options as $option){
					$options_array[$option['name']] = $in->get($option['name']);
				}
			}
			
			$apa->update_apa($in->get('id'), $in->get('apa_pool'), $in->get('apa_event'), $in->get('type'), $in->get('apa_reason'), $options_array);

			$tpl->add_js("parent.window.location.href = 'manage_auto_points.php';");
			$this->display_list();
		}
		
		function process_list_adjustments(){
			global $db, $core, $user, $tpl, $pm, $SID, $jquery, $eqdkp_root_path, $html, $pcache, $apa, $pdh, $in;
			d($in->get('id'));
			echo "List Adjustments";	
			
			$core->set_vars(array (
				'page_title' => $user->lang['apa_manager'],
				'template_file' => 'admin/manage_auto_points.html',
				'display' => true,
				)
			);	
		}
		
		
		
		function process_delete(){
			global $db, $core, $user, $tpl, $pm, $SID, $jquery, $eqdkp_root_path, $html, $pcache, $apa, $pdh, $in;
			$apa->del_apa($in->get('id'));
			
			$this->display_list();
		}
		
		function process_recalculate(){
			global $db, $core, $user, $tpl, $pm, $SID, $jquery, $eqdkp_root_path, $html, $pcache, $apa, $pdh, $in;
			echo "Recalculate";
			d($in->get('id'));
			$this->display_list();
		}
		
		
		function display_list($messages = false){
			global $db, $core, $user, $tpl, $pm, $SID, $jquery, $eqdkp_root_path, $html, $pcache, $apa, $pdh, $in;
			
			if($messages)
			{
				$pdh->process_hook_queue();
				$core->messages($messages);
			}

			$job_list = $apa->list_apas();
			
			foreach($job_list as $key => $details){
				$tpl->assign_block_vars('apa_row', array(
					'ROW_CLASS' => $core->switch_row_class(),
					'ID'	=> $key,
					'TYPE' => $user->lang['apa_type_'.$details['type']],
					'POOL' => $pdh->get('multidkp', 'name', array($details['pool'])),
					'EVENT' => $pdh->get('event', 'name', array($details['event'])),
					'REASON' => $details['reason'],
					'ACTIONS' => '&nbsp;',
				));
			}
			
			//Types
			$type_dd['startpoints'] = $user->lang['apa_type_startpoints'];
			$type_dd['timedecay'] = $user->lang['apa_type_timedecay'];
			$type_dd['pointcap'] = $user->lang['apa_type_pointcap'];
			$type_dd['inactivity'] = $user->lang['apa_type_inactivity'];
			
			//fetch events
			$events_dd = $pdh->aget('event', 'name', 0, array($pdh->get('event', 'id_list')));
			asort($events_dd);
			
			//fetch mdkppools
			$pool_dd = $pdh->aget('multidkp', 'name', 0, array($pdh->get('multidkp', 'id_list')));
			asort($pool_dd);
			
			
			$jquery->dialog('r_add_event_durl', $user->lang['add_event'], array('url' => 'manage_events.php?update=true&simple_head=true', 'width' =>'700', 'height' =>'550', 'onclose' => 'manage_auto_points.php'));
					$tpl->add_js("$(document).ready(function() {
									$('#r_add_event').click(function() {
										r_add_event_durl();
									});
									$('#r_add_apa').click(function() {
										type = $('#apa_type').val();
										desc = $('#apa_reason').val();
										pool = $('#apa_pool').val();
										event = $('#apa_event').val();
										
										content = 'type='+type+'&desc='+desc+'&pool='+pool+'&event='+event;
										
										apa_add(content);
									});
									
									});");
									
			$jquery->dialog('apa_add', $user->lang['apa_add'], array('url' => "manage_auto_points.php?mode=add&'+content+'", 'width' =>'700', 'height' =>'550', 'withid' => 'content'));
			$jquery->dialog('apa_edit', $user->lang['apa_edit'], array('url' => "manage_auto_points.php?mode=edit&id='+content+'", 'width' =>'700', 'height' =>'550', 'withid' => 'content'));
			
			$tpl->assign_vars(array (
				'F_CONFIG' => 'manage_auto_points.php' . $SID,
				'L_APA_ADD' => $user->lang['apa_add'],
				'L_APA_TYPE' => $user->lang['apa_type'],
				'L_APA_POOL' => $user->lang['apa_pool'],
				'L_APA_EVENT' => $user->lang['apa_event'],
				'L_APA_REASON' => $user->lang['apa_reason'],
				'L_APA_NEW' => $user->lang['apa_new'],
				'L_ACTION' => $user->lang['action'],
				'L_EDIT' => $user->lang['edit'],
				'L_DELETE' => $user->lang['delete'],
				'L_LIST' => $user->lang['apa_list'],
				'L_RECALCULATE' => $user->lang['apa_recalculate'],
				'L_ADD_EVENT'		=> $user->lang['add_event'],
				'TYPE_DD'	=> $html->DropDown('apa_type', $type_dd, false),
				'POOL_DD'	=> $html->DropDown('apa_pool', $pool_dd, false),
				'EVENT_DD'	=> $html->DropDown('apa_event', $events_dd, false),
			));
			
			
			$core->set_vars(array (
				'page_title' => $user->lang['apa_manager'],
				'template_file' => 'admin/manage_auto_points.html',
				'header_format'	=> ($in->get('simple_head') == 'true') ? 'simple' : 'normal',
				'display' => true,
				)
			);
	
	}
}
$manage_apa = new ManageAutoPoints;
$manage_apa->process();
?>