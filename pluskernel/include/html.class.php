<?php
/******************************
 * EQDKP PLUGIN: PLUSkernel
 * (c) 2006 by WalleniuM [Simon Wallmann]
 * http://www.kompsoft.de
 *
 * 2007 Corgan [Stefan Knaak]
 * http://www.eqdkp-plus.com
 *
 * ----------------------------
 * html.class.php
 * $Id$
 ******************************/

class htmlPlus
{

	function CheckBox($name, $langname, $options, $help='', $value='1')
	{
			$is_checked = ( $options == 1 ) ? 'checked' : '';
			$check = "&nbsp;&nbsp;".$this->HelpTooltip($help);
			$check .= "<input type='checkbox' name='".$name."' value='".$value."' ".$is_checked." /> ".$langname;
			return $check;
	}

	function TextField($name, $size, $value = '', $text='', $help='', $type = 'text'){
		$textfield = "&nbsp;&nbsp;".$this->HelpTooltip($help);
		$textfield .= " <input type='".$type."' name='".$name."' size='".$size."' value='".$value."' class='input' />";
		$textfield .= " ".$text;
		return $textfield;
	}

	function DropDown($name, $list, $selected, $text='', $help = ''){

		$dropdown  .= " <select size='1' name='".$name."'>";
			foreach ($list as $key => $value) {
				$selected_choice = ($key == $selected) ? 'selected' : '';
				$dropdown .= "<option value='".$key."' ".$selected_choice.">".$value."</option>";
		}
		$dropdown .= "</select>";
		$dropdown .= " ".$text;
		$dropdown = $this->HelpTooltip($help).$dropdown;
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
	global $db, $eqdkp, $user, $conf_plus;
	$img_path = WEB_IMG_PATH.'wow_events/';

	if(! ($eqdkp->config['default_game'] == 'WoW_german') or ($eqdkp->config['default_game'] == 'WoW_english'))
	{
		return -1;
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
	$img_folder = "./images/wow_3dmodel/" ;


	if ( (($race_id == -1) or ($class_id == -1)) and (strlen($member_name) >1) )
	{
		$sql = "SELECT member_class_id, member_race_id from ".MEMBERS_TABLE. " WHERE member_name ='".$member_name."'" ;
		$result = $db->query($sql);
		$row = $db->fetch_record($result);
		$race_id = $row['member_race_id'];
		$class_id = $row['member_class_id'];
	}

	$imgs = array();
	$imgs[] = $img_folder.$class_id.$race_id.'.gif' ;   //T1
	$imgs[] = $img_folder.$class_id.$race_id.'m.gif' ;  //T2
	$imgs[] = $img_folder.$class_id.$race_id.'f.gif' ;  //T3
	$imgs[] = $img_folder.$class_id.$race_id.'_4.gif' ; //T4
	$imgs[] = $img_folder.$class_id.$race_id.'_5.gif' ; //T5
	$imgs[] = $img_folder.$class_id.$race_id.'_6.gif' ; //T6

	foreach($imgs as $value)
	{
		 if(file_exists($value))
		 {
		 	$ret_val .= '<img src='.$value.'> &nbsp;&nbsp;' ;
		 }
	}

	return 	$ret_val ;

}# end function

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


}// end of class
?>