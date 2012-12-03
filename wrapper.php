<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
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
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');

$linkID = $in->get('id');
$title = 'Forum';

if (intval($linkID)>0)
{
 	$sql = "SELECT link_url, link_window , link_name
         	FROM ".PLUS_LINKS_TABLE."
         	WHERE link_id = '".$linkID."'
         	ORDER BY link_id";
 	
  	$result = $db->query($sql);
  	$urldata = $db->fetch_record($result);  	
  	$url= $urldata['link_url'];  	
  	$title = $urldata['link_name'];
}
elseif (($linkID == 'board') and (strlen($conf_plus['pk_bridge_cms_InlineUrl']) > 0))
{
	$url = $conf_plus['pk_bridge_cms_InlineUrl'] ;
}
elseif (($linkID == 'recemb') and (strlen($conf_plus['pk_recruitment_url']) > 0))
{
	$url = $conf_plus['pk_recruitment_url'] ;
}
elseif (($linkID == 'register') and (strlen($conf_plus['pk_bridge_cms_register_url']) > 0))
{
	$url = $conf_plus['pk_bridge_cms_register_url'] ; 
}
elseif (($linkID == 'quicksearch') and (strlen($conf_plus['pm_quicksearch_newwindow']) == 0))
{
	// whitelist...
	require($eqdkp_root_path.'portal/quicksearch/whitelist.php');
	if(VerifyLink(rawurldecode($in->get('f')), $mywpthing)){
  	$url = rawurldecode($in->get('f'));
	}
}
elseif (($linkID == 'lp') and (strlen($conf_plus['pk_latestposts_newwindow']) == 0))
{
	// Latestposts Module
	// Generate the whitelist & load the verified link...
	$mywpthing = parse_url($conf_plus['pk_latestposts_url']);
	if(VerifyLink(rawurldecode($in->get('f')), array($mywpthing['host']))){
  	$url = rawurldecode($in->get('f'));
	}
}elseif ($linkID == 'sf')
{		
	$userLang = getUserLanguage();	
	switch ($userLang) {
	    case "german":
	        $url = "http://s5.sfgame.de/?adv=0058" ;
	        break;
	    case "english":
	         $url = "http://www.sfgame.co.uk?adv=0212" ;
	        break;
	    case "english-us":
	         $url = "http://www.sfgame.us?adv=0211" ;
	        break;
	    case "french":
	        $url = "http://s1.sfgame.fr/?adv=0058" ;
	        break;
	}	

	$height="800";
	$noscroll = 'scrolling="no"';	
	$comm_settings = array('attach_id'=>'sf', 'page'=>'sf');
	$pcomments->SetVars($comm_settings);
	$COMMENT = ($conf_plus['pk_disable_comments'] == 1) ? '' : $pcomments->Show() ;
	$title = "Shakes & Fidget the Game!";
}

if (isset($url))
{
	$forum_out .= '<table width="100%" align="center" border="0">';
	$forum_out .= '<tr><td>';
	if (!isset($height)) {$height = "4024px";}
	if ($urldata['link_window'] == '2' OR $linkID == 'lp')
	{
	  $forum_out .='<iframe '. $noscroll. ' onload="window.scrollTo(0,0)" id="boardframe" src="';
	  $forum_out .= $url;
	  $forum_out .= '" allowtransparency="true" height="'.$height.'" width="99%" marginwidth="0" marginheight="0" frameborder="0" vspace="0" hspace="0"></iframe>';		
	}
	else
	{
		$forum_out .= $html->CreateDynamicIframeJS();			
		$forum_out .='<iframe onload="window.scrollTo(0,0)" id="boardframe" src="';
	  	$forum_out .= $url;
		$forum_out .= '" allowtransparency="true" height="'.$height.'" width="99%" scrolling="no" marginwidth="0" marginheight="0" frameborder="0" vspace="0" hspace="0"></iframe>';
	}
	
	$forum_out .= '</td></tr>';
	$forum_out .= '</table>';	 
	$forum_out .= $COMMENT;	 
 	  
}

	  $tpl->assign_vars(array(
	        'BOARD_OUPUT' => $forum_out)
	  );

	  $eqdkp->set_vars(array(
	      'page_title'    => sprintf($user->lang['title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$title,
	      'template_file' => 'forum.html',
	      'display'       => true)
	  );

?>