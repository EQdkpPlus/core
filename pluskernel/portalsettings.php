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

// Save
if (isset($_POST['processid'])){

}

// Plugin Code
$sql = "SELECT path, plugin FROM __portal WHERE id='".(int) $_GET['id']."'";
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
    if($confvars['property'] == 'checkbox')
    {
      $is_checked = ( $conf[$confvars['name']] == '1' ) ? 'checked' : '';
      $ccfield    = '<input name="'.$confvars['name'].'" value="1" '.$is_checked.' type="checkbox">';
    }elseif($confvars['property'] == 'text')
    {
      $ccfield = '<input name="'.$confvars['name'].'" size="'.$confvars['size'].'" value="'.$conf[$confvars['name']].'" class="input" type="text">';
    }elseif($confvars['property'] == 'textarea')
    {
      $ccfield = '<textarea name="'.$confvars['name'].'" cols="'.$confvars['size'].'" rows="'.$confvars['rows'].'" class="input">'.$conf[$confvars['name']].'</textarea>';
    }elseif($confvars['property'] == 'dropdown')
    {
      $ccfield = $html->DropDown($confvars['name'], $confvars['options'], $conf[$confvars['name']], '', '', true);
    }elseif($confvars['property'] == 'hidden')
    {
      $ccfield = '<input name="'.$confvars['name'].'" value="'.$confvars['value'].'" class="input" type="hidden"/><b>'.$confvars['text'].'</b>';
    }      
    
    if($ccfield)
    {
    
     $helpstring =	($plugg['plugin']) ? $user->lang[$confvars['help']] : $plang[$confvars['help']];	    	
     $help = (isset($confvars['help'])) ? " ".$html->HelpTooltip($helpstring) : '' ;	
    	
      $save_array[$confvars['name']] = $_POST[$confvars['name']];
      $tpl->assign_block_vars('config_row', array(
            'NAME'      => ($plugg['plugin']) ? $user->lang[$confvars['language']].$help : $plang[$confvars['language']].$help,
            'FIELD'     => $ccfield,
          )
        );
     }
  }

// Save the settings
if ($_POST['issavebu']){
	// get an array with the config fields of the DB
	$isplusconfarray = $plusdb->CheckDBFields('plus_config', 'config_name');
	foreach($save_array as $name=>$val){
    $plusdb->UpdateConfig($name, $val, $isplusconfarray);
  }
  redirect('pluskernel/portalsettings.php' . $SID.'&amp;id='. $_GET['id']);
}
 $eqdkp->set_vars(array(
            'gen_simple_header' => true
  ));

$tpl->assign_vars(array(
    'F_SETTINGS'            => 'portalsettings.php' . $SID.'&amp;id='. $_GET['id'],
    'L_SUBMIT'              => $plang['portalplugin_save'],
    )
);

$eqdkp->set_vars(array(
    'page_title'    => sprintf($user->lang['title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$plang['portalplugin_management'],
    'template_file' => 'admin/portalsettings.html',
    'display'       => true)
);
?>
