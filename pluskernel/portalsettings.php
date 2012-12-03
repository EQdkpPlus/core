<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2006
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
$eqdkp_root_path = '../';
include_once($eqdkp_root_path . 'common.php');
include_once('include/html.class.php');

// Load the language
$plang = $pluslang->NormalLanguage();

// Check user permission
$user->check_auth('a_config_man');

// Save
if (isset($_POST['processid'])){

}

// Plugin Code
$sql = "SELECT path, plugin FROM __portal WHERE id='".$db->escape($in->get('id', 0))."'";
$plugin_result = $db->query($sql);
$plugg = $db->fetch_record($plugin_result);
$portallanarry = array($plugg['path'] => $plugg['plugin']);
$plang = array_merge($plang, $pluslang->PortalLanguage($portallanarry));

if($plugg['plugin'])
{
  $modulfile = $eqdkp_root_path . 'plugins/'.$plugg['plugin'].'/portal/' . $plugg['path'] .'.php';
}else{
  $modulfile = $eqdkp_root_path.'portal/'.$plugg['path'].'/module.php';
}

include($modulfile);
$conf = $plusdb->InitConfig();

  foreach($portal_settings[$plugg['path']] as $confvars)
  {
  	$vertical_view	= false;
    if($confvars['property'] == 'checkbox')
    {
      $is_checked = ( $conf[$confvars['name']] == '1' ) ? 'checked' : '';
      $ccfield    = '<input name="'.$confvars['name'].'" value="1" '.$is_checked.' type="checkbox">';
    }elseif($confvars['property'] == 'text' or $confvars['property'] == 'password' or $confvars['property'] == 'hidden')
    {
      $myCustInput  = ($confvars['codeinput']) ? html_entity_decode(htmlspecialchars_decode($conf[$confvars['name']])) : $conf[$confvars['name']];
      $ccfield = '<input name="'.$confvars['name'].'" size="'.$confvars['size'].'" value="'.$myCustInput.'" class="input" type="'.$confvars['property'].'">';
    }elseif($confvars['property'] == 'bbcodeeditor')
    {
      $ccfield      = '<textarea id="'.$confvars['name'].'_bbcode" name="'.$confvars['name'].'" cols="'.$confvars['size'].'" rows="'.$confvars['rows'].'" class="input">'.$conf[$confvars['name']].'</textarea>';
			$ccfield      .= $jqueryp->wysiwyg($confvars['name'].'_bbcode');
			$vertical_view	= true;
    }elseif($confvars['property'] == 'textarea')
    {
      $myCustInput  = ($confvars['codeinput']) ? html_entity_decode(htmlspecialchars_decode($conf[$confvars['name']])) : $conf[$confvars['name']];
      $ccfield      = '<textarea name="'.$confvars['name'].'" cols="'.$confvars['size'].'" rows="'.$confvars['rows'].'" class="input">'.$myCustInput.'</textarea>';
    }elseif($confvars['property'] == 'multiselect')
    {
    	// make it translatable...
    	$tmpdrdwnrry = array();
    	foreach($confvars['options'] as $ddid=>$ddname){
    		$tmplangdd = '';
    		$tmplangdd = ($plugg['plugin']) ? $user->lang[$ddname] : $plang[$ddname];
    		$tmpdrdwnrry[$ddid] = ($tmplangdd) ? $tmplangdd : $ddname;	
    	}
      $ccfield = $jqueryp->MultiSelect($confvars['name'], $tmpdrdwnrry, $conf[$confvars['name']], '150');
    }elseif($confvars['property'] == 'dropdown')
    {
    	// make it translatable...
    	$tmpdrdwnrry = array();
    	foreach($confvars['options'] as $ddid=>$ddname){
    		$tmplangdd = '';
    		$tmplangdd = ($plugg['plugin']) ? $user->lang[$ddname] : $plang[$ddname];
    		$tmpdrdwnrry[$ddid] = ($tmplangdd) ? $tmplangdd : $ddname;	
    	}
      $ccfield = $html->DropDown($confvars['name'], $tmpdrdwnrry, $conf[$confvars['name']], '', '', true);
    }elseif($confvars['property'] == 'hidden')
    {
      $ccfield = '<input name="'.$confvars['name'].'" value="'.$confvars['value'].'" class="input" type="hidden"/><b>'.$confvars['text'].'</b>';
    }      
    
    if($ccfield)
    {
    
     $helpstring =	($plugg['plugin']) ? $user->lang[$confvars['help']] : $plang[$confvars['help']];	    	
     $help = (isset($confvars['help'])) ? " ".$html->HelpTooltip($helpstring) : '' ;	
    	
      $save_array[$confvars['name']] = array('value' => $_POST[$confvars['name']], 'codeinput'=>$confvars['codeinput'], 'property'=>$confvars['property']);
      $tpl->assign_block_vars('config_row', array(
            'NAME'      => ($plugg['plugin']) ? $user->lang[$confvars['language']].$help : $plang[$confvars['language']].$help,
            'FIELD'     => $ccfield,
            'VERTICAL'	=> $vertical_view,
          )
        );
     }
  }

// Save the settings
if ($_POST['issavebu']){
	// get an array with the config fields of the DB
	$isplusconfarray = $plusdb->CheckDBFields('plus_config', 'config_name');
	foreach($save_array as $name=>$val){
    $stripmytags = ($val['codeinput']) ? false : true;
    $implodeme	 = ($val['property'] == 'multiselect') ? implode("|",$val['value']) : $val['value'];
    $plusdb->UpdateConfig($name, $implodeme, $isplusconfarray, $stripmytags);
    
    $pdc->del_suffix('');
    System_Message($user->lang['plus_cache_reset_done'],$user->lang['plus_cache_reset_name']);
  }
  redirect('pluskernel/portalsettings.php' . $SID.'&amp;id='. $in->get('id', 0));
}
 $eqdkp->set_vars(array(
            'gen_simple_header' => true
  ));

$tpl->assign_vars(array(
    'F_SETTINGS'            => 'portalsettings.php' . $SID.'&amp;id='. $in->get('id', 0),
    'L_SUBMIT'              => $plang['portalplugin_save'],
    )
);

$eqdkp->set_vars(array(
    'page_title'    => sprintf($user->lang['title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$plang['portalplugin_management'],
    'template_file' => 'admin/portalsettings.html',
    'display'       => true)
);
?>