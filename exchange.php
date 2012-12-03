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

$myOut = '';
if($in->get('out') != ''){
	
	switch ($in->get('out')){
		case 'news':					$myOut = BuildMyLink('last_news.xml',		'eqdkp');		break;
		case 'items':					$myOut = BuildMyLink('last_items.xml',	'eqdkp');		break;
		case 'raids':					$myOut = BuildMyLink('last_raids.xml',	'eqdkp');		break;

		case 'data':
			if ($user->check_auth('u_event_list', false) && $user->check_auth('u_event_view', false) && $user->check_auth('u_member_list', false) && $user->check_auth('u_member_view', false) && $user->check_auth('u_item_list', false) && $user->check_auth('u_item_view', false)){
				include_once($eqdkp_root_path . 'core/data_export.class.php');
				$myexp = new content_export();
				die($myexp->export());
			} else {
				header('HTTP/1.1 403 Forbidden');
				die();
			}
				
		break;
		case 'xsd': $myOut = $eqdkp_root_path.'core/xsd/data_export.xsd';
		break;
		case 'eqdkpstatus':
			$myOut = '<?xml version="1.0" encoding="UTF-8"?>
								<eqdkpstatus>
									<guildtag>'.$core->config['guildtag'].'</guildtag>
									<dkpname>'.$core->config['dkp_name'].'</dkpname>
									<version>'.$core->config['plus_version'].'</version>
									<game>'.$core->config['default_game'].'</game>
									<game_version>'.$core->config['game_version'].'</game_version>
									<game_lang>'.$core->config['game_lang'].'</game_lang>
								</eqdkpstatus>';
			die($myOut);
		break;
		case 'serverstatus':
			if (isset($core->config['uc_servername']) && strlen($core->config['uc_servername'])){
	      // build array by exploding
	      $realmnames		= explode(',', $core->config['rs_realm']);
	      $status_file	= $eqdkp_root_path.'portal/realmstatus/'.$core->config['default_game'].'/status.php';
	    }
	    
			$myOut = '<?xml version="1.0" encoding="UTF-8"?>
								<serverstatus>
									<servername>{servername}</servername>
									<status>{status}</status>
									<lastcheck>'.time().'</lastcheck>
								</serverstatus>';
			die($myOut);
		break;

		case 'wsdl':
			$myOut = $pex->create_wsdl();
			die($myOut);
		break;
		case 'soap':
			//The SOAP-Functions
				$server = new SoapServer(NULL, array('uri' => $core->buildlink(), 'exceptions'	=> 0));                
				$server->setClass("plus_exchange_exec");
				foreach ($pex->modules['SOAP'] as $key=>$value){
					$server->addFunction($key);
				}
				$server->handle();
				die();
		break;
		case 'rest':
				header('Content-type: text/xml');
				$return = $pex->execute_rest();
				die($return);				
		break;
		
	}
	
	if (isset($pex->feeds[$in->get('out')])){
		$myOut = BuildMyLink($pex->feeds[$in->get('out')]['url'], $pex->feeds[$in->get('out')]['plugin']);
	}
	
	if($myOut){
		if(!readfile($myOut)){
			die('no_data');	
		}
	}else{
		die('no_file');	
	}
}else{
	die('no_selection');	
}

function BuildMyLink($xml, $plugin){
	global $pcache, $core;
	if($pcache->FileExists($xml, $plugin)){
		return $core->BuildLink().$pcache->FileLink($xml, $plugin, false);
	}else{
		return '';
	}
}
?>