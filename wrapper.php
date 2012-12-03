<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2006
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

$linkID = $in->get('id');
$title = 'Forum';

if (intval($linkID)>0)
{
 	$sql = "SELECT link_url, link_window , link_name
         	FROM __plus_links
         	WHERE link_id = '".$linkID."'
         	ORDER BY link_id";

  	$result = $db->query($sql);
  	$urldata = $db->fetch_record($result);
  	$url= $urldata['link_url'];
  	$title = $urldata['link_name'];
}
elseif (($linkID == 'board') and (strlen($core->config['pk_bridge_cms_InlineUrl']) > 0))
{
	$url = $core->config['pk_bridge_cms_InlineUrl'] ;
}
elseif (($linkID == 'recemb') and (strlen($core->config['pm_recruitment_url']) > 0))
{
	$url = $core->config['pm_recruitment_url'] ;
}
elseif (($linkID == 'register') and (strlen($core->config['pk_bridge_cms_register_url']) > 0))
{
	$url = $core->config['pk_bridge_cms_register_url'] ;
}
elseif (($linkID == 'quicksearch') and (strlen($core->config['pm_quicksearch_newwindow']) == 0))
{
  // whitelist...
	require($eqdkp_root_path.'portal/quicksearch/whitelist.php');
	if(VerifyLink(rawurldecode($in->get('f')), $mywpthing)){
  	$url = rawurldecode($in->get('f'));
	}
}
// Latestposts Module
elseif (($linkID == 'lp') and (strlen($core->config['pk_latestposts_newwindow']) == 0))
{
	// Generate the whitelist & load the verified link...
	$mywpthing = parse_url($core->config['pk_latestposts_url']);
	if(VerifyLink(rawurldecode($in->get('f')), array($mywpthing['host']))){
  	$url = rawurldecode($in->get('f'));
	}
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
		$forum_out .= CreateDynamicIframeJS();
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

	  $core->set_vars(array(
	      'page_title'    => $title,
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

function CreateDynamicIframeJS()
	{

		  $out = '
			<script type="text/javascript">

			//Input the IDs of the IFRAMES you wish to dynamically resize to match its content height:
			//Separate each ID with a comma. Examples: ["myframe1", "myframe2"] or ["myframe"] or [] for none:
			var iframeids=["boardframe"]

			//Should script hide iframe from browsers that dont support this script (non IE5+/NS6+ browsers. Recommended):
			var iframehide="no"

			var getFFVersion=navigator.userAgent.substring(navigator.userAgent.indexOf("Firefox")).split("/")[1]
			var FFextraHeight=parseFloat(getFFVersion)>=0.1? 3 : 0 //extra height in px to add to iframe in FireFox 1.0+ browsers

			function resizeCaller() {
			var dyniframe=new Array()
			for (i=0; i<iframeids.length; i++){
			if (document.getElementById)
			resizeIframe(iframeids)
			//reveal iframe for lower end browsers? (see var above):
			if ((document.all || document.getElementById) && iframehide=="no"){
			var tempobj=document.all? document.all[iframeids] : document.getElementById(iframeids)
			tempobj.style.display="block"
			}
			}
			}

			function resizeIframe(frameid){
			var currentfr=document.getElementById(frameid)
			if (currentfr && !window.opera){
			currentfr.style.display="block"
			if (currentfr.contentDocument && currentfr.contentDocument.body.offsetHeight) //ns6 syntax
			currentfr.height = currentfr.contentDocument.body.offsetHeight+FFextraHeight;
			else if (currentfr.Document && currentfr.Document.body.scrollHeight) //ie5+ syntax
			currentfr.height = currentfr.Document.body.scrollHeight;
			if (currentfr.addEventListener)
			currentfr.addEventListener("load", readjustIframe, false)
			else if (currentfr.attachEvent){
			currentfr.detachEvent("onload", readjustIframe) // Bug fix line
			currentfr.attachEvent("onload", readjustIframe)
			}
			}
			}

			function readjustIframe(loadevt) {
			var crossevt=(window.event)? event : loadevt
			var iframeroot=(crossevt.currentTarget)? crossevt.currentTarget : crossevt.srcElement
			if (iframeroot)
			resizeIframe(iframeroot.id);
			}

			function loadintoIframe(iframeid, url){
			if (document.getElementById)
			document.getElementById(iframeid).src=url
			}

			if (window.addEventListener)
			window.addEventListener("load", resizeCaller, false)
			else if (window.attachEvent)
			window.attachEvent("onload", resizeCaller)
			else
			window.onload=resizeCaller

			</script>
		';

		  return $out;
	} #end function
?>