<?php
/******************************
 * EQdkp ItemSpecials Plugin
 * (c) 2006 by WalleniuM [Simon Wallmann]
 * http://www.wallenium.de   
 * ------------------
 * setitems.php
 * Changed: Wed July 12, 2006
 * 
 ******************************/

define('EQDKP_INC', true);
define('PLUGIN', 'itemspecials');
$eqdkp_root_path = '../../';
include_once($eqdkp_root_path . 'common.php');

//$user->check_auth('u_setitems_view');

if (!$pm->check(PLUGIN_INSTALLED, 'itemspecials')) { message_die($user->lang['is_not_installed']); }

  // Build the data in arrays. Thats easier than editing the template file every time.
  $credits = array(
      'Corgan'                => ' for the very first basic Version, the many ideas & all the help',
  );
  
  $donators = array(
      'Shadowa'               => ' for the PHP-Security Book',
      'Carnivore'        			=> ' for the AJAX Book',
      'Webdancer'							=> ' for a DVD',
  );
  
   $additions = array(
      'ItemSpecials Logo'     => ' by Cattiebrie',
      'Icons used by ItemSpecials'=> ' are from the Nuvola Package for Linux',
      'SetItem Header Images' => ' by Seraph Creations &lt;<a href="http://seraphx.deviantart.com/" target="_blank">http://seraphx.deviantart.com/</a>&gt;',
      'Status Bar Images'     => ' by KeMo',
  );


        foreach ($credits as $key => $value)
        {
            $tpl->assign_block_vars('credits_row', array(
                'KEY'    => $key,
                'VALUE' => $value,
                )
            );
        }
        
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
    'I_ITEM_NAME'               => 'is_logo.png',
    
    'D_AUTHOR_NAME'             => 'WalleniuM [Simon Wallmann]',
    'D_AUTHOR_CITY'             => 'Heilbronn - Germany',
    'D_WEB_URL'                 => 'www.eqdkp.com',
    'D_PERSONAL_URL'            => 'www.kompsoft.de',
    
    'L_CREATED_BY'              => $user->lang['is_created by'],
    'L_CONTACT_INFO'            => $user->lang['is_contact_info'],
    'L_URL_PERSONAL'            => $user->lang['is_url_personal'],
    'L_URL_WEB'                 => $user->lang['is_url_web'],
    'L_CREDITS'                 => $user->lang['is_credits'],
    'L_DONATORS'                => $user->lang['is_sponsors'],
    'L_ADDITONS'                => $user->lang['is_additions'] ,
    'L_VERSION'                 => $pm->get_data('itemspecials', 'version')
));


$eqdkp->set_vars(array(
	'page_title'    => sprintf($user->lang['title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['ts'],
	'template_file' => 'about.html',
	'template_path' => $pm->get_data('itemspecials', 'template_path'),
	'display'       => true)
);
?>