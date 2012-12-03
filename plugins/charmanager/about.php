<?php
/******************************
 * EQDKP PLUGIN: Charmanager
 * (c) 2006 - 2007 by WalleniuM [Simon Wallmann]
 * http://www.kompsoft.de  
 * ------------------
 * about.php
 * Changed: January 10, 2006
 * 
 ******************************/

define('EQDKP_INC', true);
define('PLUGIN', 'charmanager');
$eqdkp_root_path = '../../';
include_once($eqdkp_root_path . 'common.php');

if (!$pm->check(PLUGIN_INSTALLED, 'charmanager')) { message_die($user->lang['is_not_installed']); }

  // Build the data in arrays. Thats easier than editing the template file every time.
  $donators = array(
      'All donators'               => ' for their support. I\'ll release a list soon',
  );
  
   $additions = array(
      'CharManager Logo'     => ' by Cattiebrie',
  );
        
        foreach ($donators as $key => $value)
        {
            $tpl->assign_block_vars('donnators_row', array(
                'KEY'    => $key,
                'VALUE' => $value,
                )
            );
        }
        
         foreach ($additions as $key => $value)
        {
            $tpl->assign_block_vars('addition_row', array(
                'KEY'    => $key,
                'VALUE' => $value,
                )
            );
        }

$tpl->assign_vars(array(
    'I_ITEM_NAME'               => 'uc_logo.png',
    
    'D_AUTHOR_NAME'             => 'WalleniuM [Simon Wallmann]',
    'D_AUTHOR_CITY'             => 'Heilbronn - Germany',
    'D_WEB_URL'                 => 'www.eqdkp.com',
    'D_PERSONAL_URL'            => 'www.kompsoft.de',
    
    'L_CREATED_BY'              => $user->lang['uc_created by'],
    'L_CONTACT_INFO'            => $user->lang['uc_contact_info'],
    'L_URL_PERSONAL'            => $user->lang['uc_url_personal'],
    'L_URL_WEB'                 => $user->lang['uc_url_web'],
    'L_DONATORS'                => $user->lang['uc_sponsors'],
    'L_ADDITONS'                => $user->lang['uc_additions'] ,
    'L_VERSION'                 => $pm->get_data('charmanager', 'version')
));


$eqdkp->set_vars(array(
	'page_title'    => sprintf($user->lang['title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['ts'],
	'template_file' => 'about.html',
	'template_path' => $pm->get_data('charmanager', 'template_path'),
	'display'       => true)
);
?>