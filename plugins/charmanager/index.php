<?php
/******************************
 * EQDKP PLUGIN: Charmanager
 * (c) 2006 by WalleniuM [Simon Wallmann]
 * http://www.wallenium.de   
 * ------------------
 * index.php
 * Changed: December 28, 2006
 * 
 ******************************/

define('EQDKP_INC', true);
define('PLUGIN', 'charmanager');
$eqdkp_root_path = './../../';
$closeWindows = false;
include_once($eqdkp_root_path . 'common.php');
include_once('include/usermanagement.class.php');

$user->check_auth('u_charmanager_add');

if (!$pm->check(PLUGIN_INSTALLED, 'charmanager')) { message_die($user->$lang['uc_not_installed']); }
if ($user->data['username']=="") { message_die($user->lang['uc_not_loggedin']); }
$raidplan = $pm->get_plugin('charmanager');

// ##################################################### the update checker code
$vc_link        = "http://eqdkp.corgan-net.de/vcheck/version.php";
$vc_server      = 'eqdkp.corgan-net.de';
$pluginname_vc  = 'charmanager';
// static part

// ** RB LINK CHECK FUNCTION
function CMchecklink($url){
	global $vc_server;
  if($url) {
   		$dat = @fsockopen ($vc_server, 80, $errno, $errstr, 4);
    	//$dat = @fopen ($url, "r");
  }
  if($dat){
    return true;
    fclose($dat);
  } else {
    return false;
  } 
}
// ** The Rest...
if(CMchecklink($vc_link)){
    // get the version Data:
    		if (function_exists('file_get_contents')){
					$getdata = @file_get_contents($vc_link.'?plugin='.$pluginname_vc);
				}else{
					$pparray = file ($vc_link.'?plugin='.$pluginname_vc);
					$getdata = $pparray[0];
				}
				$parse = explode('|', $getdata);
				$versions['version'] = $parse[0];
				$versions['level'] = $parse[1];
				$versions['changelog'] = $parse[2];
				$versions['download'] = $parse[3];
				$versions['release'] = $parse[5];
				$versions['name'] = $parse[6];
    if($versions['version'] == $pm->get_data($pluginname_vc, 'version') || $versions['version'] < $pm->get_data($pluginname_vc, 'version')){
        $vc_output = "";
        $vc_updatewindow = false;
    }else{
        $vc_output = $user->lang['uc_update_available_p1']." ".
          $user->lang['uc_update_available_p2']." <b>".$pm->get_data($pluginname_vc, 'version')."</b> ".
          $user->lang['uc_update_available_p3']." <b>".$versions['version'] ." (".$user->lang['uc_updated_date'].": ".date($user->lang['uc_timeformat'], $versions['release']).")</b><br>".
          "[".$user->lang['uc_release_level'].": ".$versions['level']."] <a target='_blank' href='".$versions['download']."'>".$user->lang['is_update_url']."</a> | <a target='_blank' href='".$versions['changelog']."'>".$user->lang['uc_changelog_url']."</a>";
        $vc_updatewindow = true;
    }
} else {
        $vc_output = $user->lang['uc_noserver'];
        $vc_updatewindow =true;
} 
$tpl->assign_vars(array(
      'VC_SHOW'         => $vc_updatewindow,
      'VC_TEXT'         => $vc_output,
));
// ######################################################### End of Update Check

$edit_mode = false;
if ($_POST['edit'] && $_POST['editmember']){
	$edit_mode = true;
	$edit_id = $_POST['editmember'];
}

// Build the memberTools
$CharTools = new CharTools();

// Get the save dialogues
	// save Connection
		if($_GET['mode'] == 'connection'&& isset($_POST['member_id']))
		{	
			$CharTools->updateConnection($_POST['member_id']);	
			$closeWindows = true;
		}
	
		
// Build member drop-down
$sort_order = array(
    0 => array('member_name', 'member_name desc'),
    6 => array('member_level desc', 'member_level'),
    7 => array('member_class', 'member_class desc')
);

$current_order = switch_order($sort_order);

$member_count = 0;
$previous_data = '';
$sort_index = explode('.', $current_order['uri']['current']);
$previous_source = preg_replace('/( (asc|desc))?/i', '', $sort_order[$sort_index[0]][$sort_index[1]]);               

$sql = 'SELECT m.*, c.class_name AS member_class,
        c.class_armor_type AS armor_type
        FROM ' . MEMBERS_TABLE . ' m
        LEFT JOIN (' . MEMBER_USER_TABLE . ' mu,
        ' .CLASS_TABLE. ' c)       
        ON m.member_id = mu.member_id
        WHERE mu.user_id = '.$user->data['user_id'].'
        AND (m.member_class_id = c.class_id)
        GROUP BY m.member_name
        ORDER BY m.member_name';
if ( !($members_result = $db->query($sql)) )
{
    message_die($user->lang['uc_error_memberinfos'], '', __FILE__, __LINE__, $sql);
}
while ( $row = $db->fetch_record($members_result) )
{
    $member_count++;
    $tpl->assign_block_vars('members_row', array(
        'ROW_CLASS'     => $eqdkp->switch_row_class(),
        'ID'            => $row['member_id'],
        'COUNT'         => ($row[$previous_source] == $previous_data) ? '&nbsp;' : $member_count,
        'NAME'          => $row['member_name'],
        'LEVEL'         => ( $row['member_level'] > 0 ) ? $row['member_level'] : '&nbsp;',
        'ARMOR'         => ( !empty($row['armor_type']) ) ? $row['armor_type'] : '&nbsp;',
        'CLASS'         => ( $row['member_class'] != 'NULL' ) ? $row['member_class'] : '&nbsp;'
        )
    );
    
    // So that we can compare this member to the next member,
    // set the value of the previous data to the source
    $previous_data = $row[$previous_source];
}
$footcount_text = sprintf($user->lang['listmembers_footcount'], $db->num_rows($members_result));

$tpl->assign_vars(array(
    'F_MEMBERS' 			=> 'index.php' . $SID,
    'CLOSE_WINDOWS' 	=> ( $closeWindows == true ) ? true : false,
    'IS_ADMIN'				=> ( $user->check_auth('a_plugins_man', false) ) ? true : false,
    
    'L_NAME' 					=> $user->lang['name'],
    'L_LEVEL' 				=> $user->lang['level'],
    'L_CLASS' 				=> $user->lang['class'],
    'L_ARMOR'         => $user->lang['armor'],
    'L_EDIT_CHARS' 		=> $user->lang['uc_edit_char'],
    'L_SELECT_CHARS'  => $user->lang['uc_select_char'],
    'L_MEMBER_CHARS' 	=> $user->lang['uc_edit_char'],
    'L_ADD_CHAR'			=> $user->lang['uc_add_char'],
    'L_EDIT_MEMBER'		=> $user->lang['uc_edit_char'],
    'L_CONNECT_MEM'		=> $user->lang['uc_conn_members'],
    'L_ABOUT_HEADER'	=> $user->lang['about_header'],
    'L_AJAX_LOADING'	=> $user->lang['uc_ajax_loading'],
    'L_CHAR_CONN'			=> $user->lang['uc_connections'],
    'L_CREDIT_NAME'		=> $user->lang['uc_credit_name'],
    'L_INFO_BOX'			=> $user->lang['uc_info_box'],
    
    'L_TOOLTIP1'			=> $user->lang['uc_tt_n1'],
    'L_TOOLTIP3'			=> $user->lang['uc_tt_n3'],
    'L_TOOLTIP2'			=> $user->lang['uc_tt_n2'],
    
    'O_NAME' 					=> $current_order['uri'][0],
    'O_LEVEL' 				=> $current_order['uri'][6],
    'O_CLASS' 				=> $current_order['uri'][7],
    'O_ARMOR'      		=> $current_order['uri'][9],
    
    'EDIT_CHAR'				=> ( $edit_mode ) ? true : false,
    'EDIT_ID'					=> $edit_id,
    
    'LISTMEMBERS_FOOTCOUNT' => $footcount_text,
    'L_VERSION'             => $pm->get_data('charmanager', 'version'),
    'ICON_INFO'							=> 'images/info.png'
    )
);

$eqdkp->set_vars(array(
    'page_title'    => sprintf($user->lang['title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['listmembers_title'],
    'template_file' => 'main.html',
    'template_path' => $pm->get_data('charmanager', 'template_path'),
    'display'       => true)
);
?>