<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		    http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2009
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2009 GodMod
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

$user->check_auth('a_infopages_man');

class Info_Pages extends EQdkp_Admin
{


    function info_pages()
    {
        global $db, $core, $user, $tpl, $pm, $in, $pdh;
        global $SID;

        parent::eqdkp_admin();
				if ($in->getArray('page', 'int') && is_array($in->getArray('page', 'int'))){
					$page_ids = $in->getArray('page', 'int');
					
					foreach ($page_ids as $id){
						$titles[] = $pdh->get('infopages', 'title', array($id));
					}
					
					$lines = implode(', ', $titles);
					$ids = implode(', ', $page_ids);
				}
        $confirm_text = $user->lang['info_confirm_delete'];
				$confirm_text .= '<br /><br />' . $lines;
								
				$this->set_vars(array(
            'confirm_text'  => $confirm_text,
						'uri_parameter' => 'page_ids',
            'url_id'        => ( count($page_ids) > 0 ) ? $ids : '',
            'script_name'   => 'manage_infopages.php')
        );
				
				 $this->page_data = array(
						'id' => post_or_db('page_id'),
            'title' => post_or_db('title'),
            'alias'  => post_or_db('alias'),
            'content'  => post_or_db('page_content'),
      			'menu_link'  => post_or_db('ml'),
      			'visibility' => post_or_db('vis'),
						'voting'		=> post_or_db('voting'),
						'comments'		=> post_or_db('comments'),
        );
				 
			
        $this->assoc_buttons(array(

            'delete' => array(
                'name'    => 'delete',
                'process' => 'process_delete',
                'check'   => 'a_infopages_man'),
						'create' => array(
                'name'    => 'create',
                'process' => 'display_form',
                'check'   => 'a_infopages_man'),
						'save' => array(
                'name'    => 'save',
                'process' => 'process_save',
                'check'   => 'a_infopages_man'),
						
            'form' => array(
                'name'    => '',
                'process' => 'display_list',
                'check'   => 'a_infopages_man'))
        );
				
				$this->assoc_params(array(


						'reset_votings' => array(
							'name'		=> 'mode',
							'value'		=> 'reset_votings',
							'process'	=> 'process_reset_votings',
							'check'		=> 'a_infopages_man'),
						'delete_comments' => array(
							'name'		=> 'mode',
							'value'		=> 'delete_comments',
							'process'	=> 'process_delete_comments',
							'check'		=> 'a_infopages_man'),	
						
						'page' => array(
							'name'		=> 'page',
							'process'	=> 'display_form',
							'check'		=> 'a_infopages_man'),
					));


    }
		
		function error_check(){
			global $db, $core, $user, $tpl, $pm, $in, $pdh, $acl;
      global $SID, $user_id, $jquery;
			if ($in->get('save') != ""){
				$this->fv->is_filled(array(
							'title'  				=> $user->lang['fv_required'])
				);
				
				if ($in->get('alias') != ""){
					$id = $pdh->get('infopages', 'alias_to_page', array($in->get('alias')));
					if (($id && $id != $in->get('page_id'))|| is_numeric($in->get('alias'))){
						$this->fv->is_filled(array(
								'news_headline' => $user->lang['info_error_alias'])
						);
					}
					
				}
				return $this->fv->is_error();
			}
			
		}
		
		function process_confirm(){
			global $db, $core, $user, $tpl, $pm, $in, $pdh, $acl;
      global $SID, $user_id, $jquery;
			if ( $in->get('page_ids') )
        {
            $news_ids = explode(', ', $in->get('page_ids'));
            $sql = 'DELETE FROM __pages
                    WHERE page_id IN (' . implode(', ', $news_ids) . ')';
            $db->query($sql);
				}
			$core->message($user->lang['admin_delete_infopages_success'], $user->lang['del_suc'], 'green');
			$this->display_list();
		}
		
		function process_reset_votings(){
			global $db, $core, $user, $tpl, $pm, $in, $pdh, $acl;
      global $SID, $user_id, $jquery;

			if ($in->get('page') != ""){
				$result = $db->query("UPDATE __pages SET :params WHERE page_id = '".$db->escape($in->get('page'))."'", array(
					        	'page_ratingpoints'	=> 0,
					        	'page_votes'				=> 0,
					        	'page_rating'				=> 0,
										'page_voters'				=> "",
				));
			
			$pdh->enqueue_hook('infopages');
			$pdh->process_hook_queue();
			
			}
			$this->display_form();
		}
		
		function process_delete_comments(){
			global $db, $core, $user, $tpl, $pm, $in, $pdh, $acl;
      global $SID, $user_id, $jquery;

			if ($in->get('page') != ""){
				$db->query("DELETE FROM __comments WHERE page='infopages' AND attach_id='".$db->escape($in->get('page'))."'");
			
			}
			$this->display_form();
		}
		
		
		
		function process_save(){
			global $db, $core, $user, $tpl, $pm, $in, $pdh, $acl;
      global $SID, $user_id, $jquery;
			if ($in->get('page_id')){
				
				$db->query("UPDATE __pages SET :params WHERE page_id = '".$db->escape($in->get('page_id'))."'", array(
						"page_title"		=> $in->get('title'),
						"page_alias"		=> $in->get('alias'),
						"page_content"		=> $in->get('page_content', '', 'htmlescape'),
						"page_menu_link"	=> $in->get('ml'),
						"page_comments"	=> $in->get('comments', 0),
						"page_voting"	=> $in->get('voting', 0),
						"page_edit_user"	=> $user->data['user_id'],
						"page_visibility"	=> serialize($in->getArray('vis', 'int')),
						"page_edit_date"	=> time(),
				));
				
				$core->message($user->lang['admin_update_infopages_success'], $user->lang['save_suc'], 'green');
			
			} else {
				
				$db->query("INSERT INTO __pages :params ", array(
						"page_title"		=> $in->get('title'),
						"page_alias"		=> $in->get('alias'),
						"page_content"		=> $in->get('page_content', '', 'htmlescape'),
						"page_menu_link"	=> $in->get('ml'),
						"page_edit_user"	=> $user->data['user_id'],
						"page_visibility"	=> serialize($in->getArray('vis', 'int')),
						"page_edit_date"	=> time(),
						"page_comments"		=> $in->get('comments', 0),
						"page_voting"			=> $in->get('voting', 0),
				));
				
				$core->message(sprintf($user->lang['admin_save_infopages_success'], sanitize($in->get('title'))), $user->lang['save_suc'], 'green');
						
			}
				$pdh->enqueue_hook('infopages');
				$pdh->process_hook_queue();
				$this->display_list();
		}
		
		
		function display_form(){		
			 global $db, $core, $user, $tpl, $pm, $in, $pdh, $acl;
       global $SID, $user_id, $jquery, $html;

			 if ($this->page_data['id'] != ""){
					$page_titel = $this->page_data['title'];
			} else {
				
				if ($in->get('page') != ""){
					$page_titel = $pdh->get('infopages', 'title', array($in->get('page')));
						$this->page_data = array(
								'id' => $in->get('page'),
								'title' => $pdh->get('infopages', 'title', array($in->get('page'))),
								'alias'  => $pdh->get('infopages', 'alias', array($in->get('page'))),
								'content'  => $pdh->get('infopages', 'content', array($in->get('page'))),
								'menu_link'  => $pdh->get('infopages', 'menu_link', array($in->get('page'))),
								'visibility' => $pdh->get('infopages', 'visibility', array($in->get('page'))),
								'comments' => $pdh->get('infopages', 'comments', array($in->get('page'))),
								'voting' => $pdh->get('infopages', 'voting', array($in->get('page'))),
						);
				} else {
					$page_titel = $user->lang['info_create_page'];
					unset($this->page_data);
				}
				
			}

				//Menu-Link-Dropdown
				$mlvals[0] = $user->lang['info_opt_ml_0'];
				$mlvals[1] = $user->lang['info_opt_ml_1'];
				$mlvals[2] = $user->lang['info_opt_ml_2'];
				$mlvals[3] = $user->lang['info_opt_ml_3'];
				$mlvals[99] = $user->lang['info_opt_ml_99'];
				
				if ($this->page_data['menu_link'] == 4 || count($pdh->get('infopages', 'guildrule_page')) == 0){
					$mlvals[4] = $user->lang['guildrules'];
				}
				
				//Visibility-Dropdown
				$user_groups = $pdh->get('user_groups', 'id_list');
				foreach ($user_groups as $group){
					$visvals[$group] = $pdh->get('user_groups', 'name', array($group));
					$visvals_[] = $group;
				}
	
				if (!isset($this->page_data['visibility'])){
					$vis_selected = $visvals_;
				} else {
					$vis_selected = $this->page_data['visibility'];
				}
				
				$tpl->add_js('function info_check_form(){
				
					if(document.post.title.value == ""){
						
									show_fields_empty();
									return false;
					};
				}');
			 
			 $editor = new tinyMCE;
			
			$settings_array = array(
				'language' => $user->lang['XML_LANG'],
				'autoresize'	=> true,
				);
			$editor->generate($settings_array, $eqdkp_root_path);
			
			$tpl->add_js(
				"function check_ml_dropdown(value){

					if (value == '4'){
						show_guildrules_info();
					}
				}"
			);
			
				$tpl->assign_vars(array(
					'S_NEW_PAGE'	=> ($in->exists('page')) ? false : true,
					'INFO_PAGE_CONTENT' 	=> $this->page_data['content'],
					'INFO_PAGE_TITLE' 	=> sanitize($this->page_data['title']),
					'INFO_PAGE_ALIAS' 	=> sanitize($this->page_data['alias']),
					'INFO_PAGE_COMMENTS' => ($this->page_data['comments'] == '1') ? 'checked' : '',
					'INFO_PAGE_VOTING' => ($this->page_data['voting'] == '1') ? 'checked' : '',
					'PAGE_ID' 					=>	$this->page_data['id'],
					
					'JS_GUILDRULES_INFO'	=> $jquery->Dialog('guildrules_info', $user->lang['guildrules'], array('message' => $user->lang['guildrules_info'], 'width' => 300, 'height'=>200), 'alert'),

					'INFO_PAGE_ML_DROPDOWN'	=> $html->DropDown('ml', $mlvals,$this->page_data['menu_link'], '', "onChange='check_ml_dropdown(this.value);'"),
					'INFO_PAGE_VIS_DROPDOWN' => $jquery->MultiSelect('vis', $visvals, $vis_selected, 200, 200),
					
					'FV_TITLE' => $this->fv->generate_error('title'),
					'FV_ALIAS' => $this->fv->generate_error('news_headline'),
					
					'HELP_TITLE'		=> $html->HelpTooltip($user->lang['info_help_title']),
					'HELP_ALIAS'		=> $html->HelpTooltip($user->lang['info_help_alias']),
					'HELP_ML'		=> $html->HelpTooltip($user->lang['info_help_ml']),
					'HELP_VIS'		=> $html->HelpTooltip($user->lang['info_help_vis']),
					'HELP_COMMENTS'		=> $html->HelpTooltip($user->lang['info_help_comments']),
					'HELP_VOTING'		=> $html->HelpTooltip($user->lang['info_help_voting']),
				
				'L_INFO_PAGE_ALIAS'		=> $user->lang['info_alias'],
				'L_INFO_CONTENTOPT'		=> $user->lang['info_contentopt'],
				'L_INFO_PAGEOPT'			=> $user->lang['info_pageopt'],
				'L_INFO_PAGE_TITLE'		=> $user->lang['info_opt_title'],
				'L_INFO_PAGE_CONTENT'	=> $user->lang['info_opt_content'],
				'L_INFO_PAGE_ML'			=> $user->lang['info_opt_ml'],
				'L_INFO_PAGE_ML'			=> $user->lang['info_opt_ml'],
				'L_INFO_PAGE_VIS'  		=> $user->lang['permissions'],
				'L_INFO_PAGE_COMMENTS'=> $user->lang['info_comments'],
				'L_INFO_PAGE_VOTING'	=> $user->lang['info_voting'],
				'L_RESET_VOTINGS'			=> $user->lang['info_reset_votings'],
				'L_DELETE_COMMENTS'		=> $user->lang['info_delete_comments'],
				'L_CANCEL'						=> $user->lang['cancel'],
				'L_SAVE'							=> $user->lang['save'],
				'L_RESET'							=> $user->lang['reset'],
				'PAGE_HEADER'					=> $page_titel,
				'L_MANAGE_PAGES'			=> $user->lang['info_manage_pages'],
				
				
				'F_ACTION' 						=> 'manage_infopages.php' . $SID,
				
					)
			);
				
				$core->set_vars(array (
						'page_title' => $user->lang['info_manage_pages'].': '.sanitize($this->page_data['title']),
						'template_file' => 'admin/manage_infopages.html',
						'display'       => true
					));
			 
			 
		} //Close function
		
		function display_list(){		
			 global $db, $core, $user, $tpl, $pm, $in, $pdh, $acl;
       global $SID, $user_id, $jquery;
			 
			 $order = $in->get('o', '0.0');
			 $red = 'RED'.str_replace('.', '', $order);

				$sort_order = array(
						0 => array('news_date desc', 'news_date'),
						1 => array('news_headline', 'news_headline desc'),
						2 => array('username', 'username desc')
				);
				
				$current_order = switch_order($sort_order);

			 $pagelist = $pdh->get('infopages', 'id_list', array($current_order['sql']));
			 
			 	$mlvals[0] = $user->lang['info_opt_ml_0'];
				$mlvals[1] = $user->lang['info_opt_ml_1'];
				$mlvals[2] = $user->lang['info_opt_ml_2'];
				$mlvals[3] = $user->lang['info_opt_ml_3'];
				$mlvals[4] = $user->lang['guildrules'];
				$mlvals[99] = $user->lang['info_opt_ml_99'];
		
				if (is_array($pagelist)){
						foreach ($pagelist as $id) {
							$visibility = array();
							if (is_array($pdh->get('infopages', 'visibility', array($id)))){
								foreach ($pdh->get('infopages', 'visibility', array($id)) as $group){
									if ($pdh->get('user_groups', 'name', array($group))) {
											$visibility[] = $pdh->get('user_groups', 'name', array($group));
									}
								}
							}
							$tpl->assign_block_vars('pages_row', array (
								'ID' 				=> $id,
								'ALIAS'			=> $pdh->get('infopages', 'alias', array($id)) ? sanitize($pdh->get('infopages', 'alias', array($id))) : '',
								'EDIT_USER' => sanitize($pdh->get('infopages', 'edit_user', array($id))),
								'EDIT_DATE' => sanitize($pdh->get('infopages', 'edit_date', array($id))),
								'TITLE' 		=> sanitize($pdh->get('infopages', 'title', array($id))),
								'ROW_CLASS'	=> $core->switch_row_class(),
								'ML'				=> $mlvals[$pdh->get('infopages', 'menu_link', array($id))], 
								'VIS'				=> implode(', ', $visibility),
								'EDITED' 		=> date($user->style['date_time'],  sanitize($pdh->get('infopages', 'edit_date', array($id)))).' ('.$pdh->get('user', 'name', array($pdh->get('infopages', 'edit_user', array($id)))).')',
								)
							);
						}
					}
					
					
					$tpl->assign_vars(array (
						'L_INFO_PAGE_TITLE'		=> $user->lang['info_opt_title'],
						'L_INFO_PAGE_ML'			=> $user->lang['info_opt_ml'],
						'L_INFO_PAGE_VIS'  		=> $user->lang['info_opt_visibility'],
						'L_LAST_CHANGE'				=> $user->lang['info_edit_user'],
						'L_DELETE'						=> $user->lang['delete_selected'],
						'L_ACTION'						=> $user->lang['info_action'],
						'S_NO_PAGES'					=> (count($pagelist) == 0 ) ? true : false,
						'L_NO_PAGES'					=> $user->lang['info_no_pages'],
						'L_MANAGE_PAGES'			=> $user->lang['info_manage_pages'],
						'L_CREATE'						=> $user->lang['info_create_page'],
						'L_ID'								=> $user->lang['ID'],
						'L_ALIAS'							=> $user->lang['alias'],
					));
					
					$core->set_vars(array (
						'page_title' => $user->lang['info_manage_pages'],
						'template_file' => 'admin/manage_infopages_list.html',
						'display'       => true
					));
				
		}

}
$infopages = new Info_Pages;
$infopages->process();
?>