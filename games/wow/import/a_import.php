<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2007
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
 
// EQdkp required files/vars
define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../../../';
include_once ($eqdkp_root_path . 'common.php');
$members = array();
$ii = 0;

$output = "<html><head><meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";

// Check user permission
$user->check_auth('a_members_man');

if($in->get('gimp') == 'true'){
	// Import the chars from armory
		
}else{
	// Mass Update the Chars
	$result = $db->query("SELECT member_name FROM __members ORDER BY member_name");
	while($row = $db->fetch_record($result)) {
		$members[$ii] = $row['member_name'];
		$ii++;
	}
	echo '<style>
					.uc_headerload{
						font-size: 14px;
						text-align:center;
					}
					.uc_headtxt2{
						margin:4px;
						margin-bottom: 10px;
					}
				</style>';		
	$output .= '<div id="loadingtext" class="uc_headerload">
					<div class="uc_headtxt2"><img src="../../../images/global/loading.gif" alt="update" \> '.$user->lang['uc_profile_updater'].'</div>
					<iframe src="_helpers/updateprofile.php?count='.$ii.'&actual=0" width="100%" height="60" name="item_update" frameborder=0 scrolling="no"></iframe>
				</div>';
}
echo $output;
?>