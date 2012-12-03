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

// Check user permission
$user->check_auth('a_config_man');

// Plugin Code
$sql = "SELECT path, plugin FROM __portal WHERE id='".$db->escape($in->get('id', 0))."'";
$plugin_result = $db->query($sql);
$plugg = $db->fetch_record($plugin_result);
$portallanarry = array($plugg['path'] => $plugg['plugin']);
$user->lang = array_merge($user->lang, $pluslang->PortalLanguage($portallanarry));

if($plugg['plugin'])
{
  $modulfile = $eqdkp_root_path . 'plugins/'.$plugg['plugin'].'/portal/' . $plugg['path'] .'.php';
}else{
  $modulfile = $eqdkp_root_path.'portal/'.$plugg['path'].'/module.php';
}

include($modulfile);

  foreach($portal_settings[$plugg['path']] as $confvars)
  {
  	$options = $confvars;
		$options['type'] = $confvars['property'];
		$options['value']	= $options['selected'] = $core->config[$confvars['name']];
		
		$ccfield = $html->widget($options);   
    
    if($ccfield)
    {
    
     $helpstring =	($user->lang[$confvars['help']]) ? $user->lang[$confvars['help']] : $confvars['help'];	    	
     $help = (isset($confvars['help'])) ? " ".$html->HelpTooltip($helpstring) : '' ;	
    	
      $save_array[$confvars['name']] = $html->widget_return($options);
      $tpl->assign_block_vars('config_row', array(
            'NAME'      => ($user->lang[$confvars['language']]) ? $user->lang[$confvars['language']].$help : $confvars['language'].$help,
            'FIELD'     => $ccfield,
            'VERTICAL'	=> $vertical_view,
          )
        );
     }
  }

// Save the settings
if ($_POST['issavebu']){
	// get an array with the config fields of the DB
	if(count($save_array) > 0){
		$core->config_set($save_array);
	}
	$pdc->del_prefix('portal');
	$tpl->add_js("window.location.href = 'portalsettings.php".$SID."&id=".$in->get('id', 0)."';");
}

$tpl->assign_vars(array(
    'F_SETTINGS'            => 'portalsettings.php' . $SID.'&amp;id='. $in->get('id', 0),
    'L_SUBMIT'              => $user->lang['save'],
    )
);

$core->set_vars(array(
    'page_title'    => $user->lang['portalplugin_management'],
    'template_file' => 'admin/portalsettings.html',
    'header_format' => 'simple',
    'display'       => true)
);
?>
