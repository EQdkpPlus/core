<?php
/******************************
 * EQdkp Plus
 * Copyright 2002-2008
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * wrapper.php
 * Began: 16 Februar 2008
 *
 * $Id:  $
 *
 ******************************/

define('EQDKP_INC', true);
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');


$linkID = mysql_escape_string($_GET[id]);
if (intval($linkID)>0)
{
 	$sql = "SELECT link_url
         	FROM ".PLUS_LINKS_TABLE."
         	WHERE link_id = '".$linkID."'
         	ORDER BY link_id";
  $url = $db->query_first($sql);
}


if (isset($url))
{
	$forum_out = '<script type="text/javascript">

	  var iframeids=["myframe"]

	  var iframehide="yes"

	  var getFFVersion=navigator.userAgent.substring(navigator.userAgent.indexOf("Fir efox")).split("/")[1]
	  var FFextraHeight=parseFloat(getFFVersion)>=0.1? 16 : 0
	  function resizeCaller() {
	  var dyniframe=new Array()
	  for (i=0; i<iframeids.length; i++){
	  if (document.getElementById)
	  resizeIframe(iframeids[i])
	  if ((document.all || document.getElementById) && iframehide=="no"){
	  var tempobj=document.all? document.all[iframeids[i]] : document.getElementById(iframeids[i])
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


	  <!--
	  function na_change_img_src(name, nsdoc, rpath, preload)
	  {
	  var img = eval((navigator.appName.indexOf(\'Netscape\', 0) != -1) ? nsdoc+\'.\'+name : \'document.all.\'+name);
	  if (name == \'\')
	  return;
	  if (img) {
	  img.altsrc = img.src;
	  img.src = rpath;
	  }
	  }

	  function na_preload_img()
	  {
	  var img_list = na_preload_img.arguments;
	  if (document.preloadlist == null)
	  document.preloadlist = new Array();
	  var top = document.preloadlist.length;
	  for (var i=0; i < img_list.length; i++) {
	  document.preloadlist[top+i] = new Image;
	  document.preloadlist[top+i].src = img_list[i+1];
	  }
	  }

	  function na_restore_img_src(name, nsdoc)
	  {
	  var img = eval((navigator.appName.indexOf(\'Netscape\', 0) != -1) ? nsdoc+\'.\'+name : \'document.all.\'+name);
	  if (name == \'\')
	  return;
	  if (img && img.altsrc) {
	  img.src = img.altsrc;
	  img.altsrc = null;
	  }
	  }

	  </script>';
	  $forum_out .= '<table width="100%" align="center" border="0">';
	  $forum_out .= '<tr class="row1"><th>Forum</th></tr><tr><td>';
	  $forum_out .='<iframe id="myframe" src="';
	  $forum_out .= $url;
	  $forum_out .= '" scrolling="no" name="blank" marginwidth="0" marginheight="0" frameborder="0" vspace="0" hspace="0" style="overflow:visible; width:99%; display:none"></iframe>';
	  $forum_out .= '</td></tr>';
	  $forum_out .= '</table>';
}

	  $tpl->assign_vars(array(
	        'BOARD_OUPUT' => $forum_out)
	  );

	  $eqdkp->set_vars(array(
	      'page_title'    => 'Forum',
	      'template_file' => 'forum.html',
	      'display'       => true)
	  );





function integrate_redirect (&$setLocation, $refresh)
{
	global $boardurl;
	if ($setLocation == '')
		$setLocation = DKP_URL;

	$setLocation = un_htmlspecialchars(ob_eqdkpfix($setLocation));

	return true;
}

function integrate_register($Options, $theme_vars)
{
  global $db, $db_name;

  mysql_select_db('doomdb');
  // Insert them into the users table
  $query = $db->build_query('INSERT', array(
      'username'       => str_replace('\'', '', $Options['register_vars']['memberName']),
      'user_password'  => md5($Options['password']),
      'user_email'     => str_replace('\'', '', $Options['register_vars']['emailAddress']),
      'user_alimit'    => 10,
      'user_elimit'    => 10,
      'user_ilimit'    => 10,
      'user_nlimit'    => 10,
      'user_rlimit'    => 10,
      'user_style'     => 1,
      'user_lang'      => 'german',
      'user_key'       => 123456789,
      'user_active'    => 1,
      'user_lastvisit' => 2345687452145)
  );
  $sql = 'INSERT INTO ' . USERS_TABLE . $query;
  $db->query($sql);

  mysql_select_db($db_name);
}


function eqdkp_smf_exit($with_output)
{
	global $tpl, $eqdkp, $context;

	$buffer = ob_get_contents();
	ob_end_clean();

	//If the admin has chosen Unwrapped, or the page is one that shouldn't be wrapped
	if (!$with_output)
	{
		echo ob_eqdkpfix($buffer);
		exit();
	}

  $forum_out = '<table width="100%" align="center" border="0">';
  $forum_out .= '<tr class="row1"><th>Forum</th></tr><tr><td>';
  $forum_out .= do_css_fixes($buffer);
  $forum_out .= '</td></tr>';
  $forum_out .= '</table>';

  $tpl->assign_vars(array(
        'BOARD_OUPUT' => $forum_out)
  );

  $eqdkp->set_vars(array(
      'page_title'    => 'Forum',
      'template_file' => 'forum.html',
      'display'       => true)
  );

  exit();
}

function do_css_fixes($buffer)
{
  //remove all css includes
  while(true)
  {
    $csss = strpos($buffer, '<link rel="stylesheet"');
    if($csss != 0)
    {
      $csse = strpos($buffer, '/>', $csss);
      $buffer = substr($buffer, 0, $csss).substr($buffer, $csse+2);
    }else{
    break;
    }
  }

  //remove all eqdkp ignores
  while(true)
  {
    $esis = strpos($buffer, '<!-- EQDKP_SMF_IGNORE_BEGIN -->');
    if($esis != 0)
    {
      $esie = strpos($buffer, '<!-- EQDKP_SMF_IGNORE_END -->', $esis);
      $buffer = substr($buffer, 0, $esis).substr($buffer, $esie+29);
    }else{
    break;
    }
  }

  $buffer = str_replace('class="windowbg"', 'class="row1"', $buffer);
  $buffer = str_replace('class="windowbg2"', 'class="row2"', $buffer);
  $buffer = str_replace('class="catbg"', 'class="rowth"', $buffer);
  $buffer = str_replace('class="catbg1"', 'class="rowth"', $buffer);
  $buffer = str_replace('class="catbg2"', 'class="rowth"', $buffer);
  $buffer = str_replace('class="catbg3"', 'class="rowth"', $buffer);
  $buffer = str_replace('class="titlebg"', 'class="rowth"', $buffer);

  $buffer = str_replace('class="quoteheader"', 'class="rowth"', $buffer);

  return $buffer;
}

function ob_eqdkpfix($buffer)
{
	global $scripturl, $sc;
	$buffer = str_replace($scripturl, DKP_URL, $buffer);
	$buffer = str_replace(DKP_URL.'?action=dlattach', $scripturl.'?action=dlattach', $buffer);
	$buffer = str_replace(DKP_URL.'?action=verificationcode', $scripturl.'?action=verificationcode', $buffer);
	$buffer = str_replace('name="seqnum" value="0"', 'name="seqnum" value="1"', $buffer);

  return $buffer;
}

/*
}else{

}
*/
?>
