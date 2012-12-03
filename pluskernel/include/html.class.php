<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       07.08.2006
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
	
	function CheckBox2($name, $options, $value='1')
	{
			$is_checked = ( $options == 1 ) ? 'checked' : '';
			return "<input type='checkbox' name='".$name."' value='".$value."' ".$is_checked." />";
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

	function AutoTextField($name, $size, $value = '', $text='', $help='', $type = 'text',$notable=false,$cssid='autocomplete')
	{
		$textfield = "<tr class=row1><td>".$this->HelpTooltip($help)."</td><td>";
		$textfield .= " <input name='".$name."' size='".$size."' value='".$value."' id='".$cssid."' /> ".$text." </td></tr>";
		return $textfield;
	}


	function DropDown($name, $list, $selected, $text='', $help = '',$notable=false){

		$dropdown  .= " <select size='1' name='".$name."'>";
		if(is_array($list)){
			foreach ($list as $key => $value) {
				$selected_choice = ($key == $selected) ? 'selected' : '';
				$dropdown .= "<option value='".$key."' ".$selected_choice.">".$value."</option>";
			}
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
	
	function PlainDropDown($name, $list, $selected, $javascr = '', $class = ''){
  		$dropdown  = "<select size='1' ".$javascr." name='".$name."' id='".$name."' class='".$class."'>";
  		if($list){
  			foreach ($list as $key => $value) {
  				$selected_choice = ($key == $selected) ? 'selected' : '';
  				$dropdown .= "<option value='".$key."' ".$selected_choice.">".$value."</option>";
  			}
  		}
  		$dropdown .= "</select>";
  		return $dropdown;
	}

  function HelpTooltip($help){
  		global $user, $eqdkp_root_path;
  		if ($help != ''){
  		  $helptt .= '<a '.$this->HTMLTooltip($help, 'pk_tt_help')."><img src='images/help_small.png' border='0' alt='' align='absmiddle' /></a>";
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
	 global $eqdkp_root_path;
		$copyright='<br/><center><span class="copy">
								<a onclick="javascript:AboutPLUSDialog();" style="cursor:pointer;" onmouseover="style.textDecoration=\'underline\';"
								   onmouseout="style.textDecoration=\'none\';"><img src='.$eqdkp_root_path.'images/info.png> Credits</a>
								<br />
								   <a href="http://www.eqdkp-plus.com" target="_new" class="copy">EQDKP Plus '.EQDKPPLUS_VERSION.'</a>
								   &copy; '.$this->CopyRightYear().' by <a href="http://www.eqdkp-plus.com" target="_new" class="copy">'.EQDKPPLUS_AUTHOR.'</a>
                 					| based on EQdkp
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
	function Overlib($tt){
      $tt = stripslashes($tt);
      $tt = str_replace('"', "'", $tt);
      $tt = str_replace(array("\n", "\r"), '', $tt);
      $tt = str_replace('---', '', $tt);
      $tt = addslashes($tt);
      $output = 'onmouseover="return overlib(' . "'" . $tt . "'" . ', MOUSEOFF, HAUTO, VAUTO,  FULLHTML, WRAP);" onmouseout="return nd();"';
      return $output;
    } 
	
	function ToolTip($tooltip_content, $normaltext, $title='', $icon='',$a_edge_text=null)
	{
		if(strlen($tooltip_content)>0)
		{

			//Outlines with title and icon
			$tt="<table class='wh_outer'>
				   	<tr><td valign='top'>";
			if($icon){
        $tt.="<div class='iconsmall' style='background-image: url(".$icon.");'>";
			}else{
        $tt.="<div class='iconsmall'>";
      }
			$tt.="<div class='tile'>".$title."</div></div>
						</td>
						<td>";
						
			//Tooltip itself - css
			$tt.="<table class='eqdkpplus_tooltip'>
								<tr>
									<td class='top-left'>".$a_edge_text['tl']."</td>
									<td class='top-right'>".$a_edge_text['tr']."</td>
								</tr>
								<tr>
									<td colspan='2' class='wh_left'>
										<div class='wh_right'>
										<div class='eqdkpplus_tooltip'> ".htmlspecialchars ($tooltip_content)." </div></div>
									</td>
								</tr>
								<tr>
									<td class='bottom-left'>".$a_edge_text['bl']."</td>
									<td class='bottom-right'>".$a_edge_text['br']."</td>
								</tr>
							</table>";

			$tt.="</td>
					</tr>
				</table>";

				$tt = "<span " . $this->Overlib($tt) . ">" . $normaltext . "</span>";

			return $tt ;
		}
		else
		{
			 return $normaltext;
	 	}
	}# end functions
  
  function HTMLTooltip($content, $divstyle, $icon='')
  {
    $output = $this->Overlib($this->TooltipStyle($content, $divstyle, $icon));
    return $output;
  }
    
  function TooltipStyle($content, $divstyle, $icon='')
  {
    global $eqdkp_root_path;
    $output = "<div class='".$divstyle."' style='display:block'>
                  <div class='pktooldiv'>
                  <table cellpadding='0' border='0' class='borderless'>
                  <tr>";
      if($icon){
        $output .= "<td valign='middle' width='70px' align='center'>
                      <img src='".$eqdkp_root_path."pluskernel/images/tooltip/".$icon."' alt=''/>
                    </td>";
      }
      $output .= "<td>
                    ".$content."
                    
                  </td>
                  </tr>
                  </table></div></div>";
      return $output;
    }

	/**
	 * itemstats_item()
	 * return the Itemstats data if itemstats is activeted
	 * used on the listitem page
	 *
	 * @param string $item
	 * @param integer $itemid
	 * @return Itemstats string
	 */
	function itemstats_item($item, $itemid=-1,$onlyIcon=false)
	{
		global  $conf_plus ;
		$ret_val = $item ;

	   if($conf_plus['pk_itemstats'] == 1 )
	   {
			#if(is_numeric($itemid) and ($itemid > 1) )
    		#{
			#	$ret_val = itemstats_decorate_name(stripslashes($itemid),$onlyIcon) ;	
			#}else 
			#{
				$ret_val = itemstats_decorate_name(stripslashes($item),$onlyIcon) ;	
			#}
	   		
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
			#if(is_numeric($itemid) and ($itemid > 1) )
    		#{
    		#	$ret_val = itemstats_get_html($itemid) ;
    		#}else 
    		#{
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
			
			// match a wegame video URL and replace it
			$ret = preg_replace("#(^|[\n ])([\w]+?://)(www\.wegame|wegame)(\.com/watch/)([\w-]+)(/)(^[\t <\n\r\]\[])*#is", '\\1' . $directurl . '\\2\\3\\4\\5" target="_blank" class="postlink">' . $user->lang['Jump_to'] . ' Wegame</a>' . $object . '<object width="480" height="387"><param name="movie" value="http://www.wegame.com/static/flash/player2.swf?tag=\\5"> </param><param name="wmode" value="transparent"></param><embed src="http://www.wegame.com/static/flash/player2.swf?tag=\\5" type="application/x-shockwave-flash" wmode="transparent" width="480" height="387"></embed></object>' . $tableend, $ret);

			return $ret ;

		}


	function createVTable($misc,$header)
    {
      global $eqdkp_root_path;
      $id = 'coll'.strtolower($header);
      $start = '<table width="100%" border="0" cellspacing="1" cellpadding="2" bgcolor="#000000" class="forumline">
			  	        <tr>
	  			   	      <th class="smalltitle" align="center">
                      <a href="javascript:animatedcollapse.toggle(\''.$id.'\')">
                        <img id="img'.$id.'" src="'.$eqdkp_root_path.'pluskernel/images/toggleportal.png" />
                      </a>'.$header.'
                    </th>
	  			        </tr>
                  <tr>
                    <td>
                      <div id="'.$id.'" style="display:show">
                        <table width="100%" border="0" cellspacing="1" cellpadding="2" bgcolor="#000000" class="forumline">';
      $end = "          </table>
                      </div>
                    </td>
                  </tr>
                </table>
                <script>animatedcollapse.addDiv('".$id ."', 'persist=1,hide=0');</script>";
        $out = $start.$misc.$end ;

        return $out;
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
	
	
	function toggleIcons($value,$icon_on,$icon_off, $path, $iconAltText, $url='' )
	{
		global $eqdkp_root_path ;
		
		$icon = (!empty($value))? $icon_on : $icon_off ;
		
		$ret_val =	'<img src="'.$eqdkp_root_path.$path.$icon.'" alt="'.$iconAltText.'" title="'.$iconAltText.'" />'	;
		
		if (!empty($url)) 
		{
			$ret_val = '<a href='.$url.'>'.$ret_val.'</a>';
		}
		
		return $ret_val ;
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
