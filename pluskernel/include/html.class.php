<?php
/******************************
 * EQDKP PLUGIN: PLUSkernel
 * (c) 2007 by EQDKP Plus Dev Team
 * originally written by S.Wallmann
 * http://www.eqdkp-plus.com
 * ----------------------------
 * html.class.php
 * Start: 2006
 * $Id$
 ******************************/

if ( !defined('EQDKP_INC') ){
    header('HTTP/1.0 404 Not Found');exit;
}

class htmlPlus
{

	function starttable(){
		$table = "<table border=0>" ;
		return $table ;
	}

	function endtable(){
		$table = "</table>" ;
		return $table ;
	}

	function CheckBox($name, $langname, $options, $help='', $value='1',$notable=false)
	{
			$is_checked = ( $options == 1 ) ? 'checked' : '';

			if ($notable)
			{
				$check = "&nbsp;&nbsp;".$this->HelpTooltip($help);
				$check .= "<input type='checkbox' name='".$name."' value='".$value."' ".$is_checked." /> ".$langname;

			}else
			{
				$check = "<tr class=row1><td>".$this->HelpTooltip($help)."</td>";
				$check .= "<td> <input type='checkbox' name='".$name."' value='".$value."' ".$is_checked." /> ".$langname ."</td></tr>";
			}

			return $check;
	}

	function TextField($name, $size, $value = '', $text='', $help='', $type = 'text',$notable=false)
	{

		if ($notable)
		{
			$textfield = "&nbsp;&nbsp;".$this->HelpTooltip($help);
			$textfield .= " <input type='".$type."' name='".$name."' size='".$size."' value='".$value."' class='input' />";
			$textfield .= " ".$text;
		}else {
			$textfield = "<tr class=row1><td>".$this->HelpTooltip($help)."</td><td>";
			$textfield .= " <input type='".$type."' name='".$name."' size='".$size."' value='".$value."' class='input' /> ".$text." </td></tr>";

		}

		return $textfield;
	}

	function DropDown($name, $list, $selected, $text='', $help = '',$notable=false){

		$dropdown  .= " <select size='1' name='".$name."'>";
			foreach ($list as $key => $value) {
				$selected_choice = ($key == $selected) ? 'selected' : '';
				$dropdown .= "<option value='".$key."' ".$selected_choice.">".$value."</option>";
		}
		$dropdown .= "</select>";
		$dropdown .= " ".$text;

		if ($notable)
		{
			$dropdown = $this->HelpTooltip($help).$dropdown;
		}
		else
		{
			$dropdown = "<tr class=row1><td>".$this->HelpTooltip($help)."</td><td>". $dropdown."</td></tr>";
		}

		return $dropdown;
	}

	function HelpTooltip($help){
		global $plang;
		if ($help != ''){
			$helptt .= "<a href='#' title='".$help."'><img src='images/help_small.png' border='0'  alt='' /></a>";
			//$helptt .= "<img src='images/help_small.png' border='0'  onmouseover=\"return overlib('".$help."', CAPTION, '".$plang['pk_help_header']."', BELOW, RIGHT);\" onmouseout='return nd();'>";
		}else{
			$helptt = '';
		}
		return $helptt;
	}

	function StartForm($name, $url, $action = 'post'){
		$startform = '<form method="'.$action.'" action="'.$url.'" name="'.$name.'">';
		return $startform;
	}

	function EndForm(){
		$endform = '</form>';
		return $endform;
	}

	function Button($name, $value, $type='submit'){
		$button = '<input name="'.$name.'" value="'.$value.'" class="mainoption" type="'.$type.'">';
		return $button;
	}

	function MsgBox($title, $value, $image, $width='100%', $center='false', $imgheight='48px', $imgwidth='48px', $reflect = false){
		if ($center == true){
			$startcenter 	= '<center>';
			$endcenter 		= '</center>';
		}else{
			$startcenter 	= '';
			$endcenter 		= '';
		}
		if($reflect == true){
			$showreflect = 'class="reflect"';
		}else{
			$showreflect = $divreflect = $enddivreflect = '';
		}
		$tdwidth= $imgwidth+6;
		$msgbox = $startcenter.'<table width="'.$width.'" border="0" cellspacing="1" cellpadding="2">
  							<tr>
    							<th align="center" colspan="2">'.$title.'</th>
  							</tr>
  							<tr>
    							 <td class="row1" width ="'.$tdwidth.'" ><img '.$showreflect.' src="'.$image.'" width ="'.$imgwidth.'" height="'.$imgheight.'" /></td>
    								<td class="row1">'.$value.'</td>
  							</tr>
  							<tr>
								</table>'.$endcenter;
		return $msgbox;
	}

	function CopyRightYear(){
		$year = (date('Y', time())== '2006') ? date('Y', time()) :'2006 - '.date('Y', time());
		return $year;
	}

	function Copyright(){
	 global $eqdkp ;
		$copyright='<br/><center><span class="copy">
								<a onclick="javascript:AboutPLUSDialog();" style="cursor:pointer;" onmouseover="style.textDecoration=\'underline\';" onmouseout="style.textDecoration=\'none\';"><img src='.$eqdkp->config['server_path'].'/images/info.png> Credits</a>
								<br /><a href=http://www.eqdkp-plus.com target=_new class=copy>EQDKP Plus '.EQDKPPLUS_VERSION.'</a> '. SVN_REV . ' &copy; '.$this->CopyRightYear().' by
								<a href=http://www.eqdkp-plus.com target=_new class=copy>'.EQDKPPLUS_AUTHOR.'</a>
                 | based on <a href="http://eqdkp.com/" target="_new" class="copy">EQdkp</a>
								</span></center><br />';
		return $copyright;
	}

	function ToolTip($tooltip_content, $normaltext)
	{
		global $conf_plus ;

	#	if($conf_plus['pk_multiTooltip'] == 1 and strlen($tooltip_content) > 0)
   	#{
	if(strlen($tooltip_content)>0)
	{
   		$tt= "<table cellpadding='0' border='0' class='borderless'>
 			    <tr>
				 <td valign='top'> <!-- ICON --></td>
				 <td>
 				  <div class='eqdkp_tt' style='display:block'>
 				  <div class='tooldiv'>
 					".$tooltip_content."
 				   </div></div>
 				   </td>
 				 </tr>
 				</table>";


		$tt = str_replace(array("\n", "\r"), '', $tt);
		$tt = addslashes($tt);

		$tt = 'onmouseover="return overlib(' . "'" . $tt . "'" . ', MOUSEOFF, HAUTO, VAUTO,  FULLHTML, WRAP);" onmouseout="return nd();"';
		$tt = "<span " . $tt . ">" . $normaltext . "</span>";

		return $tt ;
		}
		else
		{
		 return $normaltext;
 		}
	}

function getEventIcon($event, $size='16')
{
	global $db, $eqdkp, $user, $conf_plus, $game_icons;
	$img_path = $eqdkp_root_path.'games/'.$eqdkp->config['default_game'].'/events/';

	if(!$game_icons['event'])
	{
		return ;
	}

	 $sql = 'SELECT event_icon
            FROM ' . EVENTS_TABLE . "
            WHERE event_name='" . $event . "'";


	if ($result = $db->query($sql))
	{
	     while ( $row = $db->fetch_record($result) )
        {
        	$icon = $row['event_icon'];
        }
	 }


	if(strlen($icon) > 0)
	{
		return "<img height='".$size."' width='".$size."'  src='".$img_path.$icon."'> " ;
	}
	else
	{
		return "" ;
	}
} #end function


function getRenderImages($class_id=-1, $race_id=-1, $member_name='')
{
	global $db, $eqdkp, $user, $conf_plus;

	$ret_val = "" ;
	$img_folder = './games/'.$eqdkp->config['default_game']."/3dmodel/" ;


	if ( (($race_id == -1) or ($class_id == -1)) and (strlen($member_name) >1) )
	{
		$sql = "SELECT member_class_id, member_race_id from ".MEMBERS_TABLE. " WHERE member_name ='".$member_name."'" ;
		$result = $db->query($sql);
		$row = $db->fetch_record($result);
		$race_id = $row['member_race_id'];
		$class_id = $row['member_class_id'];
	}


	$imgs = array();
	#$imgs[] = $img_folder.$class_id.$race_id.'.gif' ;   //T1
	#$imgs[] = $img_folder.$class_id.$race_id.'m.gif' ;  //T2
	#$imgs[] = $img_folder.$class_id.$race_id.'f.gif' ;  //T3


	$gender = 'm';
	if ($this->get_Gender($member_name)=='Female') {
		$gender = 'w';
	}

	$imgs[] = $img_folder.$class_id.$race_id.'4'.$gender.'.png' ; //T4
	$imgs[] = $img_folder.$class_id.$race_id.'5'.$gender.'.png' ; //T5
	$imgs[] = $img_folder.$class_id.$race_id.'6'.$gender.'.png' ; //T6

	foreach($imgs as $value)
	{
		 if(file_exists($value))
		 {
		 	$ret_val .= '<img src='.$value.'> &nbsp;&nbsp;' ;
		 }
	}

	return 	$ret_val ;

}# end function

function get_Gender($member)
{
	global $tableprefix , $pm,$db,$table_prefix;

	$ret = '';
	if ($pm->check(PLUGIN_INSTALLED, 'charmanager'))
	{
		$sql = 'SELECT member_id FROM '.$table_prefix.'members '.
				"WHERE member_name='".$member."'";
		$ID = $db->query_first($sql);

		$sql = 'SELECT gender FROM '.$table_prefix.'member_additions '.
				'WHERE member_id='.$ID;
		$ret = $db->query_first($sql);

	}
	return $ret ;
}

function itemstats_item($item, $itemid=-1)
{

	global  $conf_plus ;
	$ret_val = $item ;

   if($conf_plus['pk_itemstats'] == 1 )
   {
   		/*if ($itemid > 1)
   		{
   		 	$ret_val = itemstats_decorate_name($itemid) ;
   		}else
   		{*/
   			$ret_val = itemstats_decorate_name(stripslashes($item)) ;
   		#}
   }

	return $ret_val ;
}

function itemstats_itemHtml($item, $itemid=-1)
{

	global  $conf_plus ;
	$ret_val = $item ;

   if($conf_plus['pk_itemstats'] == 1 )
   {
   		/*if ($itemid > 1)
   		{
   		 	$ret_val = itemstats_get_html($itemid) ;

   		}else
   		{*/
   			$ret_val = itemstats_get_html(stripslashes($item)) ;
   		#}
   }

	return $ret_val ;
}

	/**
	 * Corgan
	 * Ersetzt einen Link zu einem Videoportal mit dem Embedded Code der jeweiligen Plattform
	 *
	 * @param String $ret
	 * @return String
	 *
	 * * @todo Stage6 !!
	 *
	 */
	function EmbeddedVideo($ret)
	{

		global $user;

		$directurl = '<table border="0" cellpadding="0" cellspacing="2"><tr><td align="left"><a href="';
		$object = '</td><td align="right"><span class="gensmall"></span></td></tr><tr><td colspan="2">';
		$tableend = '</td></tr></table>';
		$tableend = '</table>';


			// match a Stage6 URL and replace it
			#http://stage6.divx.com/user/corgan2222/video/1625385/Allvatar@Giga

			/*
			<object codebase="http://download.divx.com/player/DivXBrowserPlugin.cab" height="560" width="720" classid="clsid:67DABFBF-D0AB-41fa-9C46-CC0F21721616"><param name="autoplay" value="false"><param name="src" value="http://video.stage6.com/1625385/.divx" /><param name="custommode" value="Stage6" /><param name="showpostplaybackad" value="false" /><embed type="video/divx" src="http://video.stage6.com/1625385/.divx" pluginspage="http://go.divx.com/plugin/download/" showpostplaybackad="false" custommode="Stage6" autoplay="false" height="560" width="720" /></object>
			*/

			// match a google video URL and replace it
			$ret = preg_replace("#(^|[\n ])([\w]+?://video\.google\.[\w\.]+?/videoplay\?docid=)([\w-]+)([&][\w=+&;-]*)*(^[\t <\n\r\]\[])*#is", '\\1' . $directurl . '\\2\\3\\4" target="_blank" class="postlink">' . $user->lang['Jump_to'] . ' Google Video</a>' . $object . '<object><param name="wmode" value="transparent"></param><embed style="width:400px; height:326px;" id="VideoPlayback" type="application/x-shockwave-flash" wmode="transparent" src="http://video.google.com/googleplayer.swf?docId=\\3" flashvars=""></embed></object>' . $tableend, $ret);

			// match a youtube video URL and replace it
			$ret = preg_replace("#(^|[\n ])([\w]+?://)(www\.youtube|youtube)(\.[\w\.]+?/watch\?v=)([\w-]+)([&][\w=+&;%]*)*(^[\t <\n\r\]\[])*#is", '\\1' . $directurl . '\\2\\3\\4\\5\\6" target="_blank" class="postlink">' . $user->lang['Jump_to'] . ' Youtube</a>' . $object . '<object width="425" height="350"><param name="movie" value="http://www.youtube.com/v/\\5"></param><param name="wmode" value="transparent"></param><embed src="http://www.youtube.com/v/\\5" type="application/x-shockwave-flash" wmode="transparent" width="425" height="350"></embed></object>' . $tableend, $ret);

			// match a myvideo video URL and replace it
			$ret = preg_replace("#(^|[\n ])([\w]+?://)(www\.myvideo|myvideo)(\.[\w\.]+?/watch/)([\w]+)(^[\t <\n\r\]\[])*#is", '\\1' . $directurl . '\\2\\3\\4\\5" target="_blank" class="postlink">' . $user->lang['Jump_to'] . ' MyVideo</a>' . $object . '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="470" height="406"><param name="movie" value="http://www.myvideo.de/movie/\\5"></param><param name="wmode" value="transparent"></param><embed src="http://www.myvideo.de/movie/\\5" width="470" height="406" type="application/x-shockwave-flash" wmode="transparent"></embed></object>' . $tableend, $ret);

			// match a clipfish video URL and replace it
			$ret = preg_replace("#(^|[\n ])([\w]+?://)(www\.clipfish|clipfish)(\.[\w\.]+?/player\.php\?videoid=)([\w%]+)([&][\w=+&;]*)*(^[\t <\n\r\]\[])*#is", '\\1' . $directurl . '\\2\\3\\4\\5\\6" target="_blank" class="postlink">' . $user->lang['Jump_to'] . ' Clipfish</a>' . $object . '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="464" height="380" id="player" align="middle"><param name="allowScriptAccess" value="sameDomain" /><param name="movie" value="http://www.clipfish.de/videoplayer.swf?as=0&videoid=\\5&r=1" /><param name="wmode" value="transparent"><embed src="http://www.clipfish.de/videoplayer.swf?as=0&videoid=\\5&r=1" width="464" height="380" name="player" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" /></object>' . $tableend, $ret);

			// match a sevenload video URL and replace it
			$ret = preg_replace("#(^|[\n ])([\w]+?://[\w.]+?\.sevenload\.com/videos/)([\w]+?)(/[\w-]+)(^[\t <\n\r\]\[])*#is", '\\1' . $directurl . '\\2\\3\\4" target="_blank" class="postlink">' . $user->lang['Jump_to'] . ' Sevenload</a>' . $object . '<object width="425" height="350"><param name="FlashVars" value="slxml=de.sevenload.com"/><param name="movie" value="http://de.sevenload.com/pl/\\3/425x350/swf" /><embed src="http://de.sevenload.com/pl/\\3/425x350/swf" type="application/x-shockwave-flash" width="425" height="350" FlashVars="slxml=de.sevenload.com"></embed></object>' . $tableend, $ret);

			// match a metacafe video URL and replace it
			$ret = preg_replace("#(^|[\n ])([\w]+?://)(www\.metacafe|metacafe)(\.com/watch/)([\w]+?)(/)([\w-]+?)(/)(^[\t <\n\r\]\[])*#is", '\\1' . $directurl . '\\2\\3\\4\\5\\6\\7" target="_blank" class="postlink">' . $user->lang['Jump_to'] . ' Metacafe</a>' . $object . '<embed src="http://www.metacafe.com/fplayer/\\5/\\7.swf" width="400" height="345" wmode="transparent" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash"></embed>' . $tableend, $ret);

			// match a streetfire video URL and replace it
			$ret = preg_replace("#(^|[\n ])([\w]+?://videos\.streetfire\.net/.*?/)([\w-]+?)(\.htm)(^[\t <\n\r\]\[])*#is", '\\1' . $directurl . '\\2\\3\\4" target="_blank" class="postlink">' . $user->lang['Jump_to'] . ' Streetfire</a>' . $object . '<embed src="http://videos.streetfire.net/vidiac.swf" FlashVars="video=\\3" quality="high" bgcolor="#ffffff" width="428" height="352" name="ePlayer" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer"></embed>' . $tableend, $ret);

			return $ret ;

		}

}// end of class
?>
