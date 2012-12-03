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
}elseif (($linkID == 'board') and (strlen($conf_plus['pk_bridge_cms_InlineUrl']) > 0))
{
	$url = $conf_plus['pk_bridge_cms_InlineUrl'] ;
}

if (isset($url))
{
	  $forum_out = $html->CreateDynamicIframeJS();
	  $forum_out .= '<table width="100%" align="center" border="0">';
	  $forum_out .= '<tr><td>';
	  $forum_out .='<iframe id="myframe" src="';
	  $forum_out .= $url;
	  $forum_out .= '" scrolling="no" name="blank" height=1024 marginwidth="0" marginheight="0" frameborder="0" vspace="0" hspace="0" style="overflow:visible; width:99%; display:none"></iframe>';
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

?>
