<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * point.php
 * Began: November 2005
 *
 * $ID:
 ******************************/


if ( !defined('EQDKP_INC') )
{
    die('Do not access this file directly.');
}


$name = "NA" ;
$class  = "NA" ;
$punkteSet  = "0" ;
$punkteNonSet  = "0" ;

function PointsGetClassNameByClassId($classid)
{
	global $db;
	$value = $db->query("SELECT `class_name` FROM ".CLASS_TABLE." WHERE `class_id` = '".mysql_escape_string($classid)."';");
	if($db->num_rows($value) >= 1)
	{
		$value = $db->fetch_record($value);
		return $value['class_name'];
	}
	else
	{
		return "Unknown";
	}
}


function PointsGetRaceNameByRaceId($raceid)
{
	global $db, $eqdkp;
	$value = $db->query("SELECT `race_name` FROM ".RACE_TABLE." WHERE `race_id` = '".mysql_escape_string($raceid)."';");
	if($db->num_rows($value) >= 1)
	{
		$value = $db->fetch_record($value);
		return $value['race_name'];
	}
	else
	{
		return "Unknown";
	}
}


if ( $user->data['user_id'] != ANONYMOUS )
{

	$quickdkp  ='<table width=100% class="borderless" cellspacing="0" cellpadding="2">
				<tr><td>
				<tr><th class="smalltitle" align="center" colspan="2">'.$user->lang['Points_header'].'</th></tr>';

	//get member ID from UserID
	$sql3 = 'SELECT member_id
			FROM ' . MEMBER_USER_TABLE . '
			WHERE user_id = '. $user->data['user_id'] .'';

 	$result3 = $db->query($sql3);
	while ( $row3 = $db->fetch_record($result3) )
	{
		$member_id = $row3[member_id];

		//get member info
		$sql	 = 'Select member_name, member_class_id
				   From '. MEMBERS_TABLE. ' where member_id = '.$member_id ;

		$result = $db->query($sql);

		$member_name = '' ;
		$member_classID = '';
		while ( $row = $db->fetch_record($result) )
		{
			$member_name = $row[member_name];
			$member_classID = $row[member_class_id];

			if($member_name != '')
			{
#				$quickdkp  .= '<tr class="'.$eqdkp->switch_row_class().'"><td>'.$user->lang['Points_Char'].'</td>
#											<td>'.get_classImgListmembers(PointsGetClassNameByClassId($member_classID)).get_coloredLinkedName($member_name). '</td></tr>';

				$quickdkp  .= ' <th colspan=2 >
											'.get_classImgListmembers(PointsGetClassNameByClassId($member_classID)).'&nbsp;'.get_coloredLinkedName($member_name). '</hd></tr>';


				if($conf_plus['pk_multidkp'] == 1)
				{

					$html = new htmlPlus(); // plus html class
					$member_multidkp = $html-> multiDkpMemberArray($row[member_name]) ; // create the multiDKP Table

					if(!empty($member_multidkp[$row[member_name]]))
					{
						 foreach ($member_multidkp[$row[member_name]] as $key)
						 {
							$quickdkp  .= '<tr class="'.$eqdkp->switch_row_class().'"><td>'.$key['name']." ".$user->lang['Points_DKP'].'</td>
														<td> <span class='.color_item($key['current']).'>
														  <b>'.$html->ToolTip($key['dkp_tooltip'],$key['current']). '</b> </span>
														</td></tr>';
						 } // end foreach
					}

				}
				else
				{
					//get DKP
					$sql2 = "SELECT member_earned + member_adjustment - member_spent as dkp
							FROM ".MEMBERS_TABLE." WHERE member_name = '".$member_name."'";
					$result2 = $db->query($sql2);
					$member_dkp = 0 ;
					while ( $row2 = $db->fetch_record($result2) )
					{
							$member_dkp = runden($row2[dkp]);

					}
					$db->free_result($result2);

						$quickdkp  .= '<tr class="'.$eqdkp->switch_row_class().'"><td>'.$user->lang['Points_DKP'].'</td><td><b>'.$member_dkp. '</b></td></tr>';

				} //end else config plus
			} // end if member
		} // end user2member while
		$db->free_result($result);
	} // end member while

	$quickdkp  .=  '</td></tr>
				   </table>';

	$db->free_result($result3);


	if(!$member_id > 0)
	{
	$quickdkp  ='<table width=100% class="borderless" cellspacing="0" cellpadding="2">
				<tr><td>
				<tr><th class="smalltitle" align="center" colspan="2">'.$user->lang['Points_header'].'</th></tr>
				<tr><td class="row1">'.$user->lang['Points_CHAR'].'</td></tr>
				</td></tr>
				</table>';

	}


	$tpl->assign_var('POINTSV', $quickdkp);
	#echo $quickdkp ;
}

?>