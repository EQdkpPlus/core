<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2009
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2010 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 * 
 * $Id$
 */

define('EQDKP_INC', true);
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');

require_once($eqdkp_root_path . 'core/sitedisplay.class.php');
$siteDisplay = new siteDisplay();

 // Build the data in arrays..
	$author = array(
		'personal_url'		=> 'http://www.corgan-net.de',
		'personal_name'		=> 'corgan-net.de',
		'web_url'			=> 'http://www.eqdkp-plus.com',
		'web_name'			=> 'EQKDP Plus',
		'name'				=> 'EQDKP PLUS Developer Team',
		'city'				=> 'Germany',
	);

	$images = array(
		'mainimage' 		=> 'logo_eqdkp_plus.png',
		'mainimage_alt'		=> 'EQDKP PLUS Logo',
	);

	$siteowner = array(
		'admin'	 	=> (strlen($core->config['pk_contact_name']) > 0 ) ? "<a href=mailto:".$core->config['pk_contact_email']."> ".$core->config['pk_contact_name']."</a>" : '' ,
		'website'	=> (strlen($core->config['pk_contact_website']) > 0 ) ? "<a href='".$core->config['pk_contact_website']."'> ".$core->config['pk_contact_website']." </a>"  : '' ,
		'irc'	 	=> (strlen($core->config['pk_contact_irc']) > 0 ) ? "IRC: ".$core->config['pk_contact_irc']." | " : '' ,
		'messenger'	=> (strlen($core->config['pk_contact_admin_messenger']) > 0 ) ? "Messenger: ".$core->config['pk_contact_admin_messenger'] : '' ,
		'infos'	 	=> (strlen($core->config['pk_contact_custominfos']) > 0 ) ? "Infos: ".$core->config['pk_contact_custominfos'] : ''
	);

$jquery->Tab_header('plus_about_tabs');

$disclaimerfile = $eqdkp_root_path.'language/'.$user->data['user_lang'].'/disclaimer.php' ;
if (file_exists($disclaimerfile)){
  include_once($disclaimerfile);
}

$tpl->assign_vars(array(
  'IS_DISCLAIMER'     => (($disclaimer) ? true : false),
  'DISCLAIMER'        => (($disclaimer) ? $disclaimer : ''),
  'PLUS_VERSION'      => $core->config['plus_version'],
  'IMAGE'             => $images['mainimage'],
  'PK_INFO'           => $siteDisplay->info,
  'AUTHOR'            => $author['name'],
  'CITY'              => $author['city'],
  'WEB_URL'           => $author['web_url'],
  'WEB_NAME'          => $author['web_name'],
  'SITEOWNER_ADMIN'   => $siteowner['admin'],
  'SITEOWNER_WEB'     => $siteowner['website'],
  'SITEOWNER_IRC'     => $siteowner['irc'],
  'SITEOWNER_MESSGN'  => $siteowner['messenger'],
  'SITEOWNER_INFO'    => $siteowner['infos'],
  
  'L_DISCLAIMER'      => $user->lang['pk_disclaimer'],
  'L_CREATEDBY'       => $user->lang['pk_created_by'],
  'L_MODIFICATION'    => $user->lang['pk_modification'],
  'L_DEVELOPER'       => $user->lang['pk_developer'],
  'L_PKNAME'          => $user->lang['pk_tname'],
  'L_VERSION'         => $user->lang['pk_version'],
  'L_LINK'            => $user->lang['pk_weblink'],
  'L_DONATION'        => $user->lang['pk_donation'],
  'L_DONATOR'         => $user->lang['pk_dona_name'],
  'L_JOB'             => $user->lang['pk_job'],
  'L_SITENAME'        => $user->lang['pk_sitename'],
  'L_PLUGIN'          => $user->lang['pk_plugin'],
  'L_PRODUCTNAME'     => $user->lang['pk_prodcutname'],
  'L_CREDITS'         => $user->lang['pk_credits'],
  'L_THEMES'          => $user->lang['pk_themes'],
  'L_STUFF'           => $user->lang['pk_tab_stuff'],
  'L_MODIFICATIONS'   => $user->lang['pk_modifications'],
  'L_HELP'            => $user->lang['pk_tab_help'],
  'L_WEB_URL'         => $user->lang['web_url'],
  'L_CONTACT_ADMIN'   => $user->lang['pk_contact_owner'],
));

// Template Output
$core->set_vars(array(
    'page_title'        => $user->lang['listmembers_title'],
    'template_file'     => 'about.html',
    'header_format' 		=> 'simple',
    'display'           => true)
);
?>