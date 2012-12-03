<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:				http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2010
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2006-2008 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev$
 *
 * $Id$
 */

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class reset_eqdkp extends EQdkp_Admin
{
 
    function reset_eqdkp()
    {
        global $db, $core, $user, $tpl, $pm;
        global $SID, $in;

        parent::eqdkp_admin();

				if ( $in->getArray('selected', 'string'))
        {		
						$names = '';
            foreach ($in->getArray('selected', 'string') as $key => $value){
							$names .= sanitize($value).', ';
							$ids[] = $key;
						}
						$names = substr($names, 0, -2);
						$ids = implode(', ', $ids);
				}

				$confirm_text = $user->lang['reset_confirm'].'<br /><br />'.sanitize($names);

        $this->set_vars(array(
            'confirm_text'  => $confirm_text,
						'uri_parameter' => 'del',
            'url_id'        => $ids,
            'script_name'   => 'reset.php')
        );

        $this->assoc_buttons(array(
            'form' => array(
                'name'    => '',
                'process' => 'display_form',
                'check'   => 'a_reset'),
						
						'delete' => array(
                'name'    => 'delete',
                'process' => 'process_delete',
                'check'   => 'a_reset'),
						
        ));       
		 	  
				
		 	  
    } # end function
    
		function process_confirm(){
			global $db, $core, $user, $tpl, $pm, $in, $pdh, $acl;
      global $SID, $user_id, $jquery, $backup;
			
			//Make a backup
			$backup->create();
			$dependency['events'] = array('raids', 'items', 'multi');
			$dependency['raids'] = array('items');
			$dependency['chars'] = array('raids', 'items', 'adjustments');
			$dependency['itempools'] = array('multi');
			
			$elements = $in->get('del');
			$elements = explode(', ', $elements);

			foreach ($elements as $key => $value){
				if (isset($dependency[$value])){
					$perm = true;
					foreach ($dependency[$value] as $dep){
						
						if (in_array($dep, $elements)){
						}else {
							$core->message($user->lang['reset_dependency_info'], 'Error', 'red');
							$perm = false;
						}
					
					}
					if ($perm){
						$function = 'reset__'.$value;
						$this->$function();
					}
				} else {
					$function = 'reset__'.$value;
					$this->$function();
				}	
			}
			$pdh->process_hook_queue();
			$core->message($user->lang['reset_success'], $user->lang['success'], 'green');
			$this->display_form();
		}

		
		function reset__raids(){
			global $db, $pdh;
			$raids = $pdh->get('raid', 'id_list');
			foreach ($raids as $raid){
				$pdh->put('raid', 'delete_raid', array($raid));
			}
		}
		
		function reset__events(){
			global $db, $pdh;
			$events = $pdh->get('event', 'id_list');
			foreach ($events as $event){
				$pdh->put('event', 'delete_event', array($event));
			}
		}
		
		function reset__items(){
			global $db, $pdh;
			$items = $pdh->get('item', 'id_list');
			foreach ($items as $item){
				$pdh->put('item', 'delete_item', array($item));
			}
		}
   	
		function reset__itempools(){
			global $db, $pdh;
			$itempools = $pdh->get('itempool', 'id_list');
			foreach ($itempools as $itempool){
				$pdh->put('itempool', 'delete_itempool', array($itempool));
			}
		}
		
		function reset__adjustments(){
			global $db, $pdh;
			$adjustments = $pdh->get('adjustment', 'id_list');
			foreach ($adjustments as $adjustment){
				$pdh->put('adjustment', 'delete_adjustment', array($adjustment));
			}
		}
		
		function reset__multi(){
			global $db, $pdh;
			$multidkps = $pdh->get('multidkp', 'id_list');
			foreach ($multidkps as $multidkp){
				$pdh->put('multidkp', 'delete_multidkp', array($multidkp));
			}
		}
		
		function reset__chars(){
			global $db, $pdh;
			$members = $pdh->get('member', 'id_list');
			foreach ($members as $member){
				$pdh->put('member', 'delete_member', array($member));
			}
		}
		
		function reset__news(){
			global $db;
			$db->query("DELETE FROM __news");
		}
		
		function reset__plugins(){
			global $db, $pm;
			foreach($pm->installed as $value){
				$pm->uninstall($value);
			}

		}
		
		function reset__user(){
			global $db, $user;
			$db->query("DELETE FROM __users WHERE user_id != '".$db->escape($user->data['user_id'])."'");
			$db->query("DELETE FROM __auth_users WHERE user_id != '".$db->escape($user->data['user_id'])."'");
			$db->query("DELETE FROM __groups_users WHERE user_id != '".$db->escape($user->data['user_id'])."'");
		}
		
    // ---------------------------------------------------------
    // Display form
    // ---------------------------------------------------------
    function display_form()
    {
        global $db, $core, $user, $tpl, $pm, $jquery;
        global $SID, $dbname, $table_prefix, $a_system, $a_styles;
     					
				// Raids
				$tpl->assign_block_vars('reset_row', array(
                      'ROW_CLASS'  				=> $core->switch_row_class(),
                      'TYPE'  						=> $user->lang['raids'],
                      'DISC'  						=> $user->lang['reset_raids_disc'],
                      'VAL_NAME' 					=> 'raids'
                      ));							
        
        // Events
        $tpl->assign_block_vars('reset_row', array(
                      'ROW_CLASS'  				=> $core->switch_row_class(),
                      'TYPE'  						=> $user->lang['events'],
                      'DISC'  						=> $user->lang['reset_events_disc'],
                      'VAL_NAME' 					=> 'events'
                      )); 
				// Items
        $tpl->assign_block_vars('reset_row', array(
                      'ROW_CLASS'  				=> $core->switch_row_class(),
                      'TYPE'  						=> $user->lang['items'],
                      'DISC'  						=> $user->lang['reset_items_disc'],
                      'VAL_NAME' 					=> 'items'
                      ));       
				// Itempools
        $tpl->assign_block_vars('reset_row', array(
                      'ROW_CLASS'  				=> $core->switch_row_class(),
                      'TYPE'  						=> $user->lang['menu_itempools'],
                      'DISC'  						=> $user->lang['reset_itempools_disc'],
                      'VAL_NAME' 					=> 'itempools'
                      ));       
				// Korrekturen
        $tpl->assign_block_vars('reset_row', array(
                      'ROW_CLASS'  				=> $core->switch_row_class(),
                      'TYPE'  						=> $user->lang['adjustments'],
                      'DISC'  						=> $user->lang['reset_adjustments_disc'],
                      'VAL_NAME' 					=> 'adjustments'
                      ));
				// Multi-Konto
        $tpl->assign_block_vars('reset_row', array(
                      'ROW_CLASS'  				=> $core->switch_row_class(),
                      'TYPE'  						=> $user->lang['Multi_Accs'],
                      'DISC'  						=> $user->lang['reset_multi_disc'],
                      'VAL_NAME' 					=> 'multi'
                      ));
				// Chars
        $tpl->assign_block_vars('reset_row', array(
                      'ROW_CLASS'  				=> $core->switch_row_class(),
                      'TYPE'  						=> $user->lang['chars'],
                      'DISC'  						=> $user->lang['reset_chars_disc'],
                      'VAL_NAME' 					=> 'chars'
                      ));
				// News
        $tpl->assign_block_vars('reset_row', array(
                      'ROW_CLASS'  				=> $core->switch_row_class(),
                      'TYPE'  						=> $user->lang['news'],
                      'DISC'  						=> $user->lang['reset_news_disc'],
                      'VAL_NAME' 					=> 'news'
                      ));
				// Plugins
        $tpl->assign_block_vars('reset_row', array(
                      'ROW_CLASS'  				=> $core->switch_row_class(),
                      'TYPE'  						=> $user->lang['plugins'],
                      'DISC'  						=> $user->lang['reset_plugins_disc'],
                      'VAL_NAME' 					=> 'plugins'
                      ));
				// User
        $tpl->assign_block_vars('reset_row', array(
                      'ROW_CLASS'  				=> $core->switch_row_class(),
                      'TYPE'  						=> $user->lang['user'],
                      'DISC'  						=> $user->lang['reset_user_disc'],
                      'VAL_NAME' 					=> 'user'
                      ));
        
        $tpl->assign_vars(array(
        		'L_RESET_HEADER' 			  =>	$user->lang['reset_header'],  
        		'L_RESET_INFO'  			  =>	$user->lang['reset_infotext'],  
        		'L_RESET_TYPE' 			    =>	$user->lang['reset_type'],  
        		'L_RESET_DISC' 			    =>	$user->lang['reset_disc'],  
        		'L_RESET_SEC' 			    =>	$user->lang['reset_sec'],  
						'L_DELETE'							=>	$user->lang['delete_selected'],
        		
        		'L_STATUS'							=>	$user->lang['upd_status'],    		
        		'L_SQL_STRING' 					=>	$user->lang['upd_sql_string'],    	
        		'L_SQL_STATUS_DONE' 		=>  $user->lang['upd_sql_status_done'],  

        		'L_SQL_ERROR' 					=>	$user->lang['upd_sql_error'],    	
        		'L_EQDKP_SYSTEM_TITLE' 	=>  $user->lang['upd_eqdkp_system_title'],
        		'L_EQDKP_STATUS'				=>	$user->lang['upd_eqdkp_status'],
        		        		
        		));
        		
				$tpl->add_js("
			function check_dependecy(value){
			
				if (document.getElementById('cb_events').checked == true && (document.getElementById('cb_raids').checked == false || document.getElementById('cb_items').checked == false || document.getElementById('cb_multi').checked == false)){
					document.post.cb_events.checked = false;
					events_warning();
				}
				
				if (document.getElementById('cb_raids').checked == true && document.getElementById('cb_items').checked == false){
					document.post.cb_raids.checked = false;
					raid_warning();
				}
				
				if (document.getElementById('cb_chars').checked == true && (document.getElementById('cb_raids').checked == false || document.getElementById('cb_items').checked == false || document.getElementById('cb_adjustments').checked == false)){
					document.post.cb_chars.checked = false;
					chars_warning();
				}
				
				if (document.getElementById('cb_itempools').checked == true && document.getElementById('cb_multi').checked == false){
					document.post.cb_itempools.checked = false;
					itempools_warning();
				}
				
		}
		".$jquery->Dialog('events_warning', '', array('message'=> $user->lang['reset_event_warning'], 'custom_js'	=> 'document.post.cb_raids.checked = true; document.post.cb_events.checked = true; document.post.cb_items.checked = true; document.post.cb_multi.checked = true;'), 'confirm').
		$jquery->Dialog('raid_warning', '', array('message'=> $user->lang['reset_raids_warning'], 'custom_js'	=> 'document.post.cb_raids.checked = true; document.post.cb_items.checked = true;'), 'confirm').
		$jquery->Dialog('chars_warning', '', array('message'=> $user->lang['reset_chars_warning'], 'custom_js'	=> 'document.post.cb_chars.checked = true; document.post.cb_raids.checked = true; document.post.cb_items.checked = true; document.post.cb_adjustments.checked = true;'), 'confirm').
		$jquery->Dialog('itempools_warning', '', array('message'=> $user->lang['reset_itempools_warning'], 'custom_js'	=> 'document.post.cb_itempools.checked = true; document.post.cb_multi.checked = true;'), 'confirm')
		);

        $core->set_vars(array(
            'page_title'    => $user->lang['title_resetdkp'],
            'template_file' => 'admin/reset.html',
            'display'       => true)
        		);
    }


	}

$reset = new reset_eqdkp;
$reset->process();
?>