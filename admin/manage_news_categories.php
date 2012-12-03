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

class Manage_News_Categories extends EQdkp_Admin
{
    
    function Manage_News_Categories()
    {
        global $db, $core, $user, $tpl, $pm, $in;
        global $SID;

        parent::eqdkp_admin();
 
        $this->assoc_buttons(array(
            'add' => array(
                'name'    => 'add',
                'process' => 'process_add',
                'check'   => 'a_news_add'),
			
            'form' => array(
                'name'    => '',
                'process' => 'display_form',
                'check'   => 'a_news_'))
        );
				$this->assoc_params(array(
            'delete_icon' => array(
                'name'    => 'mode',
								'value'		=> 'delicon',
                'process' => 'delete_icon',
                'check'   => 'a_news_'))
        );
		
		// Image Upload
		$logo_upload = new AjaxImageUpload;
		
		$_auioptions = array(
			'filesize'  => '2048576',  // 1 MB
			'maxheight' => '300',
			'maxwidth'  => '500'
		);
		
		
		//Für jedes Feld den Uploader
		if($in->get('performupload') != '')
		{
			$logo_upload->PerformUpload($in->get('performupload'), 'eqdkp', 'newscat_icons',$_auioptions);
			die();
		}

        
    }

    
    function delete_icon(){
			global $db, $core, $user, $tpl, $pm, $in;
      global $SID;
			if (is_numeric($in->get('id'))){
				$db->query("UPDATE __news_categories SET category_icon = '' WHERE category_id = ".$db->escape($in->get('id')));
			}
			$this->display_form();
		}    
    // ---------------------------------------------------------
    // Process Add
    // ---------------------------------------------------------
    function process_add()
    {
        global $db, $core, $user, $tpl, $pm, $in;
        global $SID;

        // Insert the new Category
		if ($in->get('new_cat_name') != ""){

			$query = $db->build_query('INSERT', array(
				'category_name'     	=> $in->get('new_cat_name'),
				'category_icon'      	=> $in->get('new_cat'),
				'category_color'		=> $in->get('new_color')
			  ));
			$result = $db->query('INSERT INTO __news_categories' . $query);
			$this_news_id = $db->insert_id();
		}
		
		//Update the whole others...
		$old_data = $in->getArray('user_groups', 'string');
		foreach ($old_data as $key=>$elem){
			if ($elem['name'] == "" && $key != 1){
				//Delete
				$db->query("DELETE FROM __news_categories WHERE category_id = '".$db->escape($key)."'");
				$db->query("UPDATE __news SET news_category=1 WHERE news_category =".$db->escape($key));
			} else {
				//Update
					$query = $db->build_query('UPDATE', array(
					'category_name'     => $elem['name'],
					'category_icon'      => $in->get('new_icon_'.$key),
					'category_color'		=>  $in->get('user_color_'.$key),
					
					));
					$db->query('UPDATE __news_categories SET ' . $query . " WHERE category_id='" . $db->escape($key) . "'");
				
			}
		}
        
       


        //
        // Success message
        //
        $success_message = $user->lang['admin_add_newscats_success'];
        $link_list = array(
            $user->lang['manage_newscategories']  => 'manage_news_categories.php' . $SID,
			$user->lang['add_news']  => 'addnews.php' . $SID,
            $user->lang['list_news'] => 'manage_news.php' . $SID);
        $this->admin_die($success_message, $link_list);
    }

    

    // ---------------------------------------------------------
    // Display form
    // ---------------------------------------------------------
    function display_form()
    {
        global $db, $core, $user, $tpl, $pm, $jquery, $html, $in;
        global $SID, $pcache;
		
		$logo_upload = new AjaxImageUpload;
		
		$order = $in->get('o', '0.0');
	 	$red = 'RED'.str_replace('.', '', $order);

		$sort_order = array(
				0 => array('category_name', 'category_name desc'),
				1 => array('category_icon', 'category_icon desc'),
				2 => array('category_color', 'category_color desc')
		);
		$current_order = switch_order($sort_order);
		
		$query = $db->query('SELECT * FROM __news_categories ORDER BY '.$current_order['sql']);
		$query = $db->fetch_record_set($query);
		foreach ($query as $row){
			
			$icon = ($row['category_icon'] != '') ? $pcache->FilePath('newscat_icons/'.$row['category_icon'], 'eqdkp') : '';
			
			$tpl->assign_block_vars('user_groups', array(
					'ID'	=> $row['category_id'],
					'NAME'	=> $row['category_name'],
					'ICON'  => '<input type="hidden" name="new_icon_'.$row['category_id'].'" id="new_icon_'.$row['category_id'].'" value="'.$row['category_icon'].'">'.$logo_upload->Show('new_icon_'.$row['category_id'], 'manage_news_categories.php?performupload=new_icon_'.$row['category_id'], $icon, false),
					'COLORPICKER'	=> $jquery->colorpicker('user_color_'.$row['category_id'], $row['category_color']),
					'ROW_CLASS' => $core->switch_row_class(),
					'S_ICON'	=> ($icon != "") ? true: false,
					)
															
			);
		}

		
		
        $tpl->assign_vars(array(
					'ACTION' 	=> 'manage_news_categories.php'.$SID,
					'ROW_CLASS' => $core->switch_row_class(),
					'TEXT_FIELD' => '<input type="hidden" name="new_cat" id="new_cat">',
					'ICON_UPLOADER'	=> $logo_upload->Show('new_cat', 'manage_news_categories.php?performupload=new_cat', false),
					'NEW_COLORPICKER' => $jquery->colorpicker('new_color', ''),
					'L_NAME'	=> $user->lang['name'],
					'L_SAVE'	=> $user->lang['save'],
					'L_DEL'		=> $user->lang['delete'],
					'L_NEWS_CATEGORIES'	=> $user->lang['manage_newscategories'],
					'L_COLOR'	=> $user->lang['color'],
					'L_ICON'	=> $user->lang['icon'],
					'L_ADD_CATEGORY'	=> $user->lang['add_newscategorie'],
					'L_MANAGE_NEWS' => $user->lang['manage_news'],
					
					 $red 				=> '_red',
						'O_DATE' 			=> $current_order['uri'][0],
						'O_USERNAME' => $current_order['uri'][2],
						'O_HEADLINE' => $current_order['uri'][1],
						'L_SORT_DESC'	=> $user->lang['sort_desc'],
						'L_SORT_ASC'	=> $user->lang['sort_asc'],
						'U_LIST_NEWS' => 'manage_news_categories.php'.$SID,

       ) );

        $core->set_vars(array(
            'page_title'    => $user->lang['manage_newscategories'],
            'template_file' => 'admin/manage_news_categories.html',
            'display'       => true)
        );
    }
}

$manage_newscategories = new Manage_News_Categories;
$manage_newscategories->process();
?>
