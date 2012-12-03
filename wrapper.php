<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date:  $
 * -----------------------------------------------------------------------
 * @author      $Author:  $
 * @copyright   2006-2008 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev:  $
 * 
 * $Id:  $
 * Browser Check by Azaradel
 */

define('EQDKP_INC', true);
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');

$linkID = mysql_escape_string($_GET[id]);
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
  $url = $_GET['f'];
}
// Latestposts Module
elseif (($linkID == 'lp') and (strlen($conf_plus['pk_latestposts_newwindow']) == 0))
{
  $url = rawurldecode($_GET['f']);
}

$sop = parseUrl($url);
$sop = ( $sop['host'] == $_SERVER['SERVER_NAME']) ? TRUE : FALSE;

if (isset($url))
{
	$forum_out .= '<table width="100%" align="center" border="0">';
	$forum_out .= '<tr><td>';

	if ($urldata['link_window'] == '2' OR $linkID == 'lp' OR !$sop OR !check_browser($_SERVER['HTTP_USER_AGENT']))
	{
	  $forum_out .='<iframe onload="window.scrollTo(0,0)" id="boardframe" src="';
	  $forum_out .= $url;
	  $forum_out .= '" allowtransparency="true" height="4024" width="99%" marginwidth="0" marginheight="0" frameborder="0" vspace="0" hspace="0"></iframe>';		
	}
	else
	{
		$forum_out .= $html->CreateDynamicIframeJS();			
		$forum_out .='<iframe onload="window.scrollTo(0,0)" id="boardframe" src="';
	  	$forum_out .= $url;
		$forum_out .= '" allowtransparency="true" width="99%" scrolling="no" marginwidth="0" marginheight="0" frameborder="0" vspace="0" hspace="0"></iframe>';
	}
	
	$forum_out .= '</td></tr>';
	$forum_out .= '</table>';	 
 
	  
}

	  $tpl->assign_vars(array(
	        'BOARD_OUPUT' => $forum_out)
	  );

	  $eqdkp->set_vars(array(
	      'page_title'    => sprintf($user->lang['title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$title,
	      'template_file' => 'forum.html',
	      'display'       => true)
	  );

function check_browser($browser){
      if (eregi("Mozilla/5", $browser)) return TRUE;
      elseif (eregi("Opera/9\.5",$browser)) return TRUE; 
      elseif (eregi("MSIE 7\.0",$browser)) return TRUE; 
      elseif (eregi("MSIE 6\.0",$browser)) return TRUE;  
}

function parseUrl($url) {
        $r  = "^(?:(?P<scheme>\w+)://)?";
        $r .= "(?:(?P<login>\w+):(?P<pass>\w+)@)?";
        $r .= "(?P<host>(?:(?P<subdomain>[-\w\.]+)\.)?" . "(?P<domain>[-\w]+\.(?P<extension>\w+)))";
        $r .= "(?::(?P<port>\d+))?";
        $r .= "(?P<path>[\w/]*/(?P<file>\w+(?:\.\w+)?)?)?";
        $r .= "(?:\?(?P<arg>[\w=&]+))?";
        $r .= "(?:#(?P<anchor>\w+))?";
        $r = "!$r!";                                                // Delimiters
      
        preg_match ( $r, $url, $out );
      
        return $out;
    }
?>
