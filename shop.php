<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       14 March 2008
 * Date:        $Date:  $
 * -----------------------------------------------------------------------
 * @author      $Author:  $
 * @copyright   2006-2008 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev:  $
 * 
 * $Id:  $
 */

define('EQDKP_INC', true);
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');

$memberID = intval(mysql_escape_string($_GET['id']));

$sql = 'SELECT member_name, member_id
 		 FROM ' . MEMBERS_TABLE ;
$result = $db->query($sql);

while ( $row = $db->fetch_record($result) )
{
  	$tpl->assign_block_vars('members_row', array(
      'VALUE'    => $row['member_id'],
      'SELECTED' => ( $memberID == strtolower($row['member_id']) ) ? ' selected="selected"' : '',
      'OPTION'   => $row['member_name'])
	);

}

if (intval($memberID)<1) 
{
	if ( $user->data['user_id'] != ANONYMOUS )
	{
		$sql = 'SELECT member_id
				FROM ' . MEMBER_USER_TABLE . '
				WHERE user_id = '. $user->data['user_id'] .'';
		$memberID	= $db->query_first($sql);
	}	
}

$sql = "SELECT
		m.member_name, m.member_level, m.member_race_id, m.member_class_id,
		c.class_name,
		r.race_name
		FROM
		".MEMBERS_TABLE." m
		INNER JOIN ".CLASS_TABLE." c ON c.class_id = m.member_class_id
		INNER JOIN ".RACE_TABLE." r ON r.race_id = m.member_race_id
		WHERE m.member_id =".$memberID;

    $result = $db->query($sql);
    $row = $db->fetch_record($result) ;
    $shopurl = create_shop_link($row['member_name'],$row['class_name'], $row['race_name'],$eqdkp->config['guildtag'],$conf_plus['pk_servername'],$row['member_level'] );



$tpl->assign_vars(array(
    'CONTENT_TITLE' => "[".$eqdkp->config['guildtag'].'] Shop powered by Eqdkp-Plus',
    'SHOP_URL' => $shopurl,
    'L_ERROR_IFRAME' => $user->lang['error_iframe'],
    'L_NEW_WINDOW' => $user->lang['new_window'],
    'L_CHOOSE'	=> $user->lang['shirt_ad2'],
    'L_SHIRT4'	=> $user->lang['shirt_ad3'],
    'L_SHIRT4'	=> $user->lang['shirt_ad4']
    )
);


$eqdkp->set_vars(array(
    'page_title'    => sprintf($user->lang['title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': ',
    'template_file' => 'shop.html',
    'display'       => true)
);
?>
