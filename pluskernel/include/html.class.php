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

	function RadioBox($name, $langname, $options , $help='', $value='0',$notable=false)
	{
		if ($value == $options) {
			$is_checked = 'checked';
		}

			$check = "&nbsp;&nbsp;".$this->HelpTooltip($help);
			$check .= "<input type='radio' name='".$name."' value='".$value."' ".$is_checked." /> ".$langname;

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
								<a onclick="javascript:AboutPLUSDialog();" style="cursor:pointer;" onmouseover="style.textDecoration=\'underline\';"
								   onmouseout="style.textDecoration=\'none\';"><img src='.$eqdkp->config['server_path'].'/images/info.png> Credits</a>
								<br />
								   <a href=http://www.eqdkp-plus.com target=_new class=copy>EQDKP Plus '.EQDKPPLUS_VERSION.'</a>
								   '. SVN_REV . ' &copy; '.$this->CopyRightYear().' by <a href=http://www.eqdkp-plus.com target=_new class=copy>'.EQDKPPLUS_AUTHOR.'</a>
                 					| based on <a href="http://eqdkp.com/" target="_new" class="copy">EQdkp</a>
									</span></center><br />';
		return $copyright;
	}


	/**
	 * Eqdkp Plus Tooltip
	 * used for the MultiDKP info, the talentspec tooltip and the news tooltip
	 *
	 * @param string $tooltip_content
	 * @param string $normaltext #the text wich is shown
	 * @param string $title
	 * @param string $icon
	 * @param array $a_edge_text [top left][top right] [buttom left] [buttom right]
	 * @return string
	 */
	function ToolTip($tooltip_content, $normaltext, $title='', $icon='',$a_edge_text=null)
	{
		if(strlen($tooltip_content)>0)
		{

			//Outlines with title and icon
			$tt='<table class=\'wh_outer\'>
				   	<tr><td valign=\'top\'>
							<div class=\'iconsmall\' style=\'background-image: url('.$icon.');\'>
							<div class=\'tile\'>'.$title.'</div></div>
						</td>
						<td>';

			//Tooltip itself - css
			$tt.='
							<table class=\'eqdkpplus_tooltip\'>
								<tr>
									<td class=\'top-left\'>'.$a_edge_text['tl'].'</td>
									<td class=\'top-right\'>'.$a_edge_text['tr'].'</td>
								</tr>
								<tr>
									<td colspan=\'2\' class=\'wh_left\'>
										<div class=\'wh_right\'>
										<div class=\'eqdkpplus_tooltip\'> '.$tooltip_content.' </div></div>
									</td>
								</tr>
								<tr>
									<td class=\'bottom-left\'>'.$a_edge_text['bl'].'</td>
									<td class=\'bottom-right\'>'.$a_edge_text['br'].'</td>
								</tr>
							</table>';

			$tt.='
						</td>
					</tr>
				</table>';


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
	}# end functions


	/**
	 * itemstats_item()
	 * return the Itemstats data if itemstats is activeted
	 * used on the listitem page
	 *
	 * @param string $item
	 * @param integer $itemid
	 * @return Itemstats string
	 */
	function itemstats_item($item, $itemid=-1)
	{
		global  $conf_plus ;
		$ret_val = $item ;

	   if($conf_plus['pk_itemstats'] == 1 )
	   {
			$ret_val = itemstats_decorate_name(stripslashes($item)) ;
	   }
		return $ret_val ;
	}

	/**
	 * Itemstats_itemHtml()
	 * return the Itemstats HTML Data if ttemstats is activeted
	 * used on the viewitem page
	 *
	 * @param string $item
	 * @param integer $itemid
	 * @return Itemstats string
	 */
	function itemstats_itemHtml($item, $itemid=-1)
	{
		global  $conf_plus ;
		$ret_val = $item ;

	   if($conf_plus['pk_itemstats'] == 1 )
	   {
	   		$ret_val = itemstats_get_html(stripslashes($item)) ;
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
	 */
	function EmbeddedVideo($ret)
	{

		global $user;

		$directurl = '<table border="0" cellpadding="0" cellspacing="2"><tr><td align="left"><a href="';
		$object = '</td><td align="right"><span class="gensmall"></span></td></tr><tr><td colspan="2">';
		$tableend = '</td></tr></table>';
		$tableend = '</table>';


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



	/**
	 * EQDKP PLUS Custom Links
	 * create the menu with the additional links
	 *
	 * @param boolean $activate
	 * @param String-Eqdkü Styöe $style
	 * @param String $root_path
	 * @return String
	 */
	function createLinkMenu($activate,$style,$root_path)
	{
		global $tpl, $db, $eqdkp,$eqdkp_root_path;

		$main_menu3 = '';
		if ($activate == 1)
		{
			$tpl->assign_var('MENU3NAME', 'Links');

			// load the links from db
			$linkssql = 'SELECT link_id, link_name, link_url, link_window
     		 			 FROM '.PLUS_LINKS_TABLE.' ORDER BY link_id';
  			$plinks_result = $db->query($linkssql);

   			//output links
			while ( $pluslinkrow = $db->fetch_record($plinks_result) )
   			{

   				$link = $pluslinkrow['link_url'];
   				$plus_target = '';

				// generate target
				if ($pluslinkrow['link_window'] == 1)
				{
					$plus_target = 'target="_blank"';
				}elseif ($pluslinkrow['link_window'] == 2) {
					$link = $eqdkp_root_path.'wrapper.php?id='.$pluslinkrow['link_id'];
				}

				// Output Link
				if($style == 'defaultV' or
				   $style == 'wow_styleV' or
				   $style == 'WoWMaevahEmpireV' or
				   $style == 'WoWMoonclaw01V' or
                   $style == 'wow3theme' or
                   $style == 'm9wow3eq' or
                   $style == 'wowV'
                   )
				{
					$main_menu3 .= '<tr nowrap>
								<td class="row'.($bi+1).'" nowrap>&nbsp;<img src="' .$root_path .'images/arrow.gif" alt="arrow"/> &nbsp;
								<a href="' . $link . '" class="copy" '.$plus_target.'>' . $pluslinkrow['link_name'] . '</a>
								</td></tr>';
					$bi = 1-$bi;
				}
				else
				{
					$main_menu3 .= '<a href="' . $pluslinkrow['link_url'] . '" class="copy" '.$plus_target.'>' . $pluslinkrow['link_name'] . '</a> | ';
				}
			} // end while
		} // end on/off
		return $main_menu3;
	}

	/**
	 * Create a Google Chart API Graph
	 * use the GoogleGraph Class
	 *
	 * @param Array $a_daten
	 * @return String
	 */
	function createGraph($a_daten)
	{

		global $conf_plus;

		if ($conf_plus['pk_itemhistory_dia'])
		{	return ; }

		$max = 0;
		$_a_daten = array();
		//Suche den Maxwert
		foreach ($a_daten as $value)
		{
			if($value > $max)
			{$max = $value;}
		}

		//Array umdrehen und Wert glätten auf max 97 da die
		//goddammedfuckshice google api im normalen modus nur 100 werte kann
		foreach (array_reverse($a_daten) as $value)
		{
			if ($max > 0)
			{
				$offset= $max / 97 ;
				if ($offset > 0)
				{
				$_a_daten[]= $value/ $offset ;
				}
			}
		}

		//Create Object
		$graph = new GoogleGraph();

		//Graph
		$graph->Graph->setType('line');
		$graph->Graph->setSubtype('chart');
		$graph->Graph->setSize(300, 100);

		#$graph->Graph->setAxis(); //no arguments means all on
		$graph->Graph->setGridLines(20, 20, 1, 0);

		//Title
		#$graph->Graph->setTitle('DKP History', '#FF0000', 10);

		//Background
		$graph->Graph->addFill('chart', '#000000', 'solid');
		$graph->Graph->addFill('background', '#FFFFFF', 'gradient', '#000000', 90, 0.5, 0);

		//Axis Labels -> ToDO
		#$graph->Graph->addAxisLabel(array('', '', '', '', '')); # rechts nach links unten
		#$graph->Graph->addAxisLabel(array('','','')); # rechts nach links oben
		#$graph->Graph->addAxisLabel(array('', '')); # oben nach unten links
		#$graph->Graph->addAxisLabel(array('', '', '')); #oben nach unten rechts
		#$graph->Graph->addLabelPosition(array(1, 10, 37, 75));
		#$graph->Graph->addLabelPosition(array(2, 0, 1, 2, 4));
		#$graph->Graph->setAxisRange('2,0,'.$max);
		#$graph->Graph->setAxisRange("0,0,3000|1,0,3000|2,0,3000|3,0,3000");
		#$graph->Graph->setAxisRange(array(1,0, 3000));
		#$graph->Graph->setAxisRange(array(2,0, 3000));
		#$graph->Graph->setAxisRange(array(3,0, 3000));
		#$graph->Graph->addAxisStyle(array(0, '#0000dd', 10));
		#$graph->Graph->addAxisStyle(array(3, '#0000dd', 12, 1));

		//Lines
		$graph->Graph->setLineColors(array('#FF0000', '#00FF00', '#0000FF'));

		//Data
		$graph->Data->addData($_a_daten);

		//Output Graph
		$img = $graph->printGraph();
		return $img;

	}


	 /**
	 * Create a Google Pie API Graph
	 * use the GoogleGraph Class
	 *
	 * @param array of $percent
	 * @param array of $colors
	 * @param array of $Labels
	 * @return String
	 */
	function createpieGraph($percent,$colors,$Labels)
	{

		global $conf_plus, $user;

		if ($conf_plus['pk_itemhistory_dia'])
		{	return ; }

		//Create Object
		$graph = new GoogleGraph();

		//Graph
		$graph->Graph->setType('pie');
		$graph->Graph->setSubtype('3d');
		$graph->Graph->setSize(500, 160);

		//Set Axis
		$graph->Graph->setAxis(); //no arguments means all on

		//Background -> Transparent
		$graph->Graph->addFill('background', '00000000', 'solid');

		//Labels
		$graph->Graph->addAxisLabel($Labels);
		$graph->Graph->addAxisStyle(array(0, '#'.$user->style['fontcolor2'], 11));

		//Lines
		$graph->Graph->setLineColors($colors);

		//Data
		$graph->Data->addData($percent);

		//Output Graph
		$img = $graph->printGraph();
		return $img;
	}

	/**
	 * Corgans Newsloot
	 *
	 * @param integer $showRaids_id
	 * @return String
	 */
	function newsloot($showRaids_id)
	{
		global $conf_plus,$db,$user,$eqdkp_root_path;

		$raid_ids = explode(",",$showRaids_id);
		$message = "";

		foreach($raid_ids as $raid_ID)
		{
			$loot = "" ;
			$raid_info = "";

			/*Gets the looted items */
			#############################################
			if($raid_ID)
			{
		    	$sql2 = 'SELECT item_id, item_buyer, item_name, item_date, item_value
				         FROM ' . ITEMS_TABLE . "
			             WHERE raid_id = " . $raid_ID."
			             ORDER BY item_value DESC
		            	 ";

		      	if ($conf_plus['pk_newsloot_limit'] > 0)
		      	{
		      		$sql2 .= ' limit '. $conf_plus['pk_newsloot_limit'] ;
		      	}

				if (($results = $db->query($sql2)) )
				{
					while ( $item = $db->fetch_record($results) )
					{
						$loot .= '<a href='.$eqdkp_root_path.'viewitem.php?'. URI_ITEM . '='.$item['item_id'].'>' ;
						if ($conf_plus['pk_itemstats'] == 1){
							$loot .= $this->itemstats_item(stripslashes($item['item_name'])).'</a> -> ';
						}else{
							$loot .= stripslashes($item['item_name']).'</a> -> ';
						}
						$loot .= get_coloredLinkedName($item['item_buyer']) . ' ('.round($item['item_value']).' DKP)<br>';
					}

					$db->free_result($results);

					//Raidname
					$sql2 = 'SELECT raid_name, raid_date, raid_note
							 FROM ' . RAIDS_TABLE . "
							 WHERE raid_id = " . $raid_ID."
							 ";

					if ( ($results = $db->query($sql2)) )
					{

						$raid = $db->fetch_record($results);
						$event_icon = getEventIcon($raid['raid_name']);

						$raid_info .= $event_icon.'<a href='.$eqdkp_root_path.'viewraid.php?' . URI_RAID . '='.$raid_ID.'>'.$raid['raid_name'].'</a> &nbsp;' ;
						$raid_info .= '('.$raid['raid_note'].') &nbsp;' ;
						$raid_info .= ( !empty($raid['raid_date']) ) ? date($user->style['date_notime_short'], $raid['raid_date']) : '&nbsp;' ;

						if(strlen($loot) > 1)
						{
							$message .='<br><hr noshade>'.$raid_info.' Loot:<br><br>'.$loot ;
						}
					}
					$db->free_result($results);
				}
			}//end if
	 	}// forech
		return $message ;
	}

	/**
	 * Create a TPL VAR the shows the recruitment Table
	 *
	 */
	function createRecruitmentTable()
	{
		global $conf_plus,$db,$user,$tpl,$eqdkp,$user,$eqdkp_root_path;

		$sql = 'SELECT class_id ,class_name
         		FROM '.CLASS_TABLE.' ORDER BY class_name';
  		$result = $db->query($sql);

  		$recruit = '<table width="100%" border="0" cellspacing="1" cellpadding="2" class="forumline">
  					<tr><th colspan=2 class="smalltitle" align="center">'.$user->lang['recruitment_open'].'</th></tr>';

	   	while ( $row = $db->fetch_record($result) )
	   	{


			if($eqdkp->config['default_game'] == 'WoW' and ($row['class_name'] <> 'Unknown' ))
			{
				$i = 0 ;
				$specs = $user->lang['talents'][renameClasstoenglish($row['class_name'])] ;
		   		foreach ($specs as $specname)
		   		{
		   			$i++;
		   			$classCount = $conf_plus['pk_recruitment_class['.$row['class_id'].']['.$i.']'] ;
			   		if ($classCount > 0)
			   	  	{
			   	  		$rowcolor = $eqdkp->switch_row_class();
			   	  		$c_color = renameClasstoenglish($row['class_name']);
  		 	   		    $img = $eqdkp_root_path."games/WoW/talents/".strtolower(renameClasstoenglish($row['class_name'])).($i-1).".png" ;
  		 	   		    $icon= "<img src='".$img."'>" ;
  		 	   		    $showntext = $this->ToolTip($specname.' - '.$row['class_name'],$icon.get_ClassIcon($row['class_name']).' '.$row['class_name'],$icon) ;
			   	  		$recruit .= '<tr class="'.$rowcolor.'"><td class="'.$c_color.'">'.$showntext.'</td>
			   	  						 					   <td>'. $classCount. '</td>
			   	  					</tr>';
			   	  		$show =true ;
			   	  	}
		   		}
			}else
			{

		   		if ($conf_plus['pk_recruitment_class['.$row['class_id'].']'] > 0)
		   	  	{
		   	  		$rowcolor = $eqdkp->switch_row_class();
		   	  		$c_color = renameClasstoenglish($row['class_name']);
		   	  		$recruit .= '<tr class="'.$rowcolor.'"><td class="'.$c_color.'">'.get_ClassIcon($row['class_name']).' '.$row['class_name'].'</td>
		   	  						 					   <td>'. $conf_plus['pk_recruitment_class['.$row['class_id'].']']. '</td>
		   	  					</tr>';
		   	  		$show =true ;
		   	  	}
			}
	   	}

	   	if (strlen($conf_plus['pk_recruitment_url']) > 1) {
	   		$url = '<a href="'.$conf_plus['pk_recruitment_url'].'">' ;
	   	}else
	   	{
	   		$url = '<a href="mailto:'.$conf_plus['pk_contact_email'].'">';
	   	}

	   	$recruit .= '<tr class="'.$rowcolor.'"><td colspan=2 class="smalltitle" align="center">'.$url.$user->lang['recruitment_contact'].' </a></td></tr>';
	   	$recruit .= ' </table>';
	   	if ($show) {
			$tpl->assign_var('RECRUITMENT',$recruit);
	   	}

	}

	/**
	 * Return a Arrow Icon with the given Text
	 *
	 * @param String $text
	 * @param String $direction
	 * @param String $color
	 * @return String
	 */
	function createToggleIcon($text='', $direction='up', $color='blue')
	{
		global $eqdkp_root_path ;

		$dir = ($direction == 'up') ? 'u' : 'd' ;
		$color = ($color=='blue') ? 'b' : 'e' ;
		$img = $eqdkp_root_path. '/pluskernel/include/jquery/img/toggle_'.$color.'_'.$dir.'.gif' ;

		if (file_exists($img))
		{
			$ret = $text." <img src=".$img.">";
			return $ret ;
		}

		return $text ;

	} #end function


	/**
	 * Create the TPL Var {NEXT_RAIDS}
	 *
	 * @return void
	 */
	function getNextRaids()
	{
		global $db, $eqdkp_root_path, $user, $tpl, $pm, $table_prefix, $eqdkp,$user,$conf_plus;

		$total_recent_raids = $total_raids = 0;

		if(!$pm->check(PLUGIN_INSTALLED, 'raidplan'))
		{
			return nil;
		}

		//looking for next Raids
		$sql = "SELECT * FROM `".$table_prefix."raidplan_raids` WHERE raid_date > ".time()."  ORDER BY `raid_date`";

		if ($conf_plus['pk_nextraids_limit'] > 0) {
		 $sql .= ' LIMIT '.$conf_plus['pk_nextraids_limit']	;
		}elseif ($conf_plus['pk_nextraids_limit'] == 0 ){
		 $sql .= ' LIMIT 3'	;
		}

		$result = $db->query($sql);
		$raidcount = $db->num_rows($result);

		//Do the rest only if we have at least one next raid
		if ($raidcount > 0)
		{
			//Get all MemberIDs from the active User
			if ( $user->data['user_id'] != ANONYMOUS )
			{
				$sql2 = 'SELECT member_id
						FROM ' . MEMBER_USER_TABLE . '
						WHERE user_id = '. $user->data['user_id'] .'';

		 		$result2 = $db->query($sql2);
		 		$member_ids = array();

		 		//get all memberIDs
				while ( $row2 = $db->fetch_record($result2) )
				{
					$member_ids[] = $row2[member_id]  ;
				}
			}

			//Table Header
		  	$out = '<table width="100%" border="0" cellspacing="0" cellpadding="2" class="forumline">
  					<tr><th colspan=2 class="smalltitle" align="center">'.$user->lang['next_raids_head'].'</th></tr>';
		}else
		{
			return nil ;
		}

		//go though all given next raids
	   	while ( $row = $db->fetch_record($result) )
	   	{
			$own_status = false;

		  	// count the signed in members
		    $sql = "SELECT count(*) FROM ".$table_prefix."raidplan_raid_attendees WHERE attendees_subscribed=1 AND raid_id=" . $row['raid_id'];
	        $count_signin = $db->query_first($sql);

	  		// count the confirmed members
	    	$sql = "SELECT count(*) FROM ".$table_prefix."raidplan_raid_attendees WHERE attendees_subscribed=0 AND raid_id=" . $row['raid_id'];
	      	$count_confirmed = $db->query_first($sql);

	  		// count the signedout members
	    	$sql = "SELECT count(*) FROM ".$table_prefix."raidplan_raid_attendees WHERE attendees_subscribed=2 AND raid_id=" . $row['raid_id'];
	    	$count_signedout = $db->query_first($sql);

	  		// count the total sum
	    	$sql = "SELECT raid_attendees FROM `".$table_prefix."raidplan_raids` WHERE raid_id='".$row['raid_id']."'";
	    	$count_total = $db->query_first($sql);

	    	//Raid-Event-Icon
	    	$sql = "SELECT event_icon FROM `".$table_prefix."events` WHERE event_name='".$row['raid_name']."'";
			$icon = $db->query_first($sql);

			//looking for the attend of the signed on user
			if(strlen($member_ids)>0)
			{
				//Beteiligung an RaidID des Members suchen
				$sql2 = "SELECT attendees_subscribed,attendees_note,attendees_signup_time FROM ".$table_prefix."raidplan_raid_attendees
							  WHERE raid_id=".$row['raid_id']."
							  AND member_id in (".join_array("','", $member_ids, "name").")" ;

				$result2 = $db->query($sql2);

				//found some raids
				if($row2 = $db->fetch_record($result2))
				{
					//only if the user has allready signed on
					if ($row2['attendees_signup_time'] > 0)
					{
						$own_status['status'] 	= $row2['attendees_subscribed'];
						$own_status['note'] 	= $row2['attendees_note'];
					}
				}
			}

			//calc
			$count_summ = $count_confirmed + $count_signin;
			$diffangemeldet = $count_total - $count_summ ;
			$diffgest = $count_total - $count_confirmed ;

			if(($count_affirmed > $count_summ) and  ($count_total - $count_affirmed >= 1))
    		{$diffangemeldet = $count_total - $count_affirmed ;}

    		$confirmstatus = '';

    		//Flags only for registered user
    		if (is_array($own_status))
    		{
	    		switch ($own_status['status']) {
		            case 0: $confirmstatus = ' <img src='.$eqdkp_root_path."plugins/raidplan/images/status/status0.gif />";break;
		            case 1: $confirmstatus = ' <img src='.$eqdkp_root_path."plugins/raidplan/images/status/status1.gif />";break;
		            case 2: $confirmstatus = ' <img src='.$eqdkp_root_path."plugins/raidplan/images/status/status2.gif />";break;
		            case 3: $confirmstatus = ' <img src='.$eqdkp_root_path."plugins/raidplan/images/status/status3.gif />";break;
	          		}
    		}elseif($user->data['user_id'] != ANONYMOUS) {
    			$confirmstatus = ' <img src='.$eqdkp_root_path."plugins/raidplan/images/status/status2.gif />";
    		}


			$out .= "<tr class=row1><td colspan=2><b>".strftime($user->style['strtime_date_short'], $row['raid_date']).$confirmstatus.
												  "</b></td></tr>";

    		$out .= "<tr valign=top class=row2>
    				 	<td valign=top>
			  		   		<a href='".$eqdkp_root_path."plugins/raidplan/viewraid.php?r=". stripslashes($row['raid_id'])."'>
			  		   		<img src=".$eqdkp_root_path."/games/".$eqdkp->config['default_game']."/events/".$icon."></a>
			  		   	</td>
			  			<td>
			  				<a href='".$eqdkp_root_path."plugins/raidplan/viewraid.php?r=".
    								   stripslashes($row['raid_id'])."'>".
    								   stripslashes($row['raid_name']) ." (".$count_total.") </a>
			  				<br>";

    		if (is_array($own_status))
    		{
			  $out .= "<font class='positive'>".$user->lang['next_raids_signon'].": ".$count_summ."</font><br>" ;
			  $out .= "<font class='neutral'>".$user->lang['next_raids_confirmed'].": ".$count_confirmed."</font><br>" ;
			  $out .= "<font class='negative'>".$user->lang['next_raids_signoff'].": ".$count_signedout."</font><br>" ;
 			  $out .=  ($diffangemeldet > 0) ? "<font class='negative'><b>".$user->lang['next_raids_missing'].": ".$diffangemeldet."</b></font>" : '' ;
    		}elseif ($user->data['user_id'] != ANONYMOUS)
    		{
    			$out .= "<a href='".$eqdkp_root_path."plugins/raidplan/viewraid.php?r=". $row['raid_id']."'>".$user->lang['next_raids_notsigned']."</a>" ;
    		}

			  $out .= "</td></tr>";

			  $show = true ;

	   	}#end while

	   	if ($show) {
	   		$out .= "</table>" ;
	   	}else {
	   		$out = "";
	   	}

		$tpl->assign_var('NEXT_RAIDS',$out);

	}# end function

	function createLastItems()
	{
		global $eqdkp_root_path , $user, $eqdkp,$tpl,$conf_plus;
		$path = $eqdkp_root_path ;

		include_once($eqdkp_root_path.'pluskernel/bridge/bridge_class.php');
		$br = new eqdkp_bridge();
		$limit = ($conf_plus['pk_last_items_limit'] > 0) ? $conf_plus['pk_last_items_limit'] : '5' ;
		$lastitems = $br->get_last_items($limit);

		if (is_array($lastitems))
		{
			$out = '<table width="100%" border="0" cellspacing="0" cellpadding="2" class="forumline">
  			<tr><th colspan=2 class="smalltitle" align="center">'.$user->lang['last_items'].'</th></tr>';

			foreach ($lastitems as $item)
			{
				$out .= "<tr class=".$eqdkp->switch_row_class()."><td>
						<a href='".$path."viewitem.php?i=". $item['id']."'>".$this->itemstats_item(stripslashes($item['name']))."</a><br>".
						get_coloredLinkedName($item['looter']).' ('.$item['value'].' DKP)</td></tr>';
			}
			$out .= '</table>';

			$tpl->assign_var('LAST_ITEMS',$out);
		}

	}



}// end of class

class Tabs
{

  function startPane($id)
	{
		$tab = "<div class='tab-pane' id='".$id."'>";
		return $tab;
	}

	/**
	* Ends Tab Pane
	*/
	function endPane() {
		$tab = "</div>";
		return $tab;

	}

	/*
	* Creates a tab with title text and starts that tabs page
	* @param tabText - This is what is displayed on the tab
	* @param paneid - This is the parent pane to build this tab on
	*/

	function startTab( $tabText, $paneid ) {
		$tab = "<div class='tab-page'><h2 class='tab' id='".$paneid."'>".$tabText."</h2>";

		return $tab;
	}

	/*
	* Ends a tab page
	*/
	function endTab() {

		$tab = "</div>";

		return $tab;
	}



function startTable() {
		$tab = "<table border=0 cellpadding=1 cellspacing=1 width=100%>";
		return $tab;
	}

	/*
	* Ends a tab page
	*/
	function endTable() {

		$tab = "</table>";
		return $tab;
	}

function tableheader($text) {
		$tab = "<tr><td align='left' colspan=2></td> </tr>";
		$tab .= "<tr class=row2><th align='left' colspan=2>".$text."</td> </tr>";

		return $tab;
	}


function tablerow($text,$row='row1')
{
		$tab = "<tr class=".$row."><td align='left' colspan=2>". $text ."</td> </tr>";

		return $tab;
}

}#end class tabs
?>
