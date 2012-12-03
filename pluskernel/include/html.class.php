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
		global $tpl, $db, $eqdkp;

		$main_menu3 = '';
		if ($activate == 1)
		{
			$tpl->assign_var('MENU3NAME', 'Links');

			// load the links from db
			$linkssql = 'SELECT link_name, link_url, link_window
     		 			 FROM '.PLUS_LINKS_TABLE.' ORDER BY link_id';
  			$plinks_result = $db->query($linkssql);

   			//output links
			while ( $pluslinkrow = $db->fetch_record($plinks_result) )
   			{
				// generate target
				if ($pluslinkrow['link_window'] == 1){
					$plus_target = 'target="_blank"';
				}else{
					$plus_target = '';
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
								<a href="' . $pluslinkrow['link_url'] . '" class="copy" '.$plus_target.'>' . $pluslinkrow['link_name'] . '</a>
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
