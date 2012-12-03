<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       14 March 2008
 * Date:        $Date:  $
 * -----------------------------------------------------------------------
 * @author      $Author:  $
 * @copyright   2006-2008 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev:  $
 *
 * $Id:  $
 */

define('EQDKP_INC', true);
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');
$url=base64_encode("http://".$eqdkp->config['server_name'].$eqdkp->config['server_path']) ;

	$userLang = getUserLanguage();	
	switch ($userLang) {		
		case "german":
	        $suffix = "de" ;
	        break;
	    case "english":
	         $suffix = "en" ;
	        break;
	    case "english-us":
	         $suffix = "en" ;
	        break;
	    default:    
	    	$suffix = "de" ;
	}
	
	$games = ($_HMODE) ? "http://$suffix.allvatar.popmog.com/cobrands/25/cobrand_partner_pages/241?partner_uid=$url" : "http://$suffix.eqdkp.popmog.com/cobrands/25/cobrand_partner_pages/241?partner_uid=$url";
	$topPlayer = ($_HMODE) ? "http://$suffix.allvatar.popmog.com/cobrands/25/cobrand_partner_pages/243?partner_uid=$url" : "http://$suffix.eqdkp.popmog.com/cobrands/25/cobrand_partner_pages/243?partner_uid=$url";
	$activ = ($_HMODE) ? "http://$suffix.allvatar.popmog.com/cobrands/25/cobrand_partner_pages/245?partner_uid=$url" : "http://$suffix.eqdkp.popmog.com/cobrands/25/cobrand_partner_pages/245?partner_uid=$url";
	

$tpl->assign_vars(array(    
    'POPMOG' => $in->get('popmog'),
    'GAMES' => $games,
    'TOPPLAYER' => $topPlayer,
    'ACTIV' => $activ,
	'SF_TEXT' => $user->lang['sf_text'],
	'POPMOG_TEXT' => $user->lang['popmog_text'],
	'PM_PLAYER' => $user->lang['pm_player'],
	'PM_GAMES' => $user->lang['pm_games'],
	'PM_ACTIVITY' => $user->lang['pm_activity'],
	'SF_IMAGE' => $user->lang['sf_image'],
	'PM_IMAGE' => $user->lang['pm_image']
    )
);


$eqdkp->set_vars(array(
    'page_title'    => sprintf($user->lang['title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': Mini-Games powered by Eqdkp-Plus',
    'template_file' => 'mini-games.html',
    'display'       => true)
);
?>