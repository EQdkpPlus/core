<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2006
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

define('EQDKP_INC', true);
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');
if($core->config['pk_noRoster'] == 1) { redirect('listmember.php');}
$user->check_auth('u_member_list');
$roster_data = array();

# Amory Stuff
if(isset($core->config['uc_servername']) && isset($core->config['uc_server_loc']) && $core->config['default_game'] == 'wow')
{
	$armory = new ArmoryChars();
}

    $members = $pdh->aget('member', 'array', 0, array($pdh->get('member', 'id_list')), true);       

	foreach ($game->get('classes') as $key => $value)
	{
		if ($key > 0) 
		{		
			//Class Template Vars
			$class_array = array(
	          'CLASS_NAME'    => $value,
	          'CLASS_ID'	  => $key ,
	          'CLASS_ICONS'	  => $game->decorate('classes', array($key, true))
				);
		    $tpl->assign_block_vars('class_row', $class_array );
	
		    foreach ($members as $val )
		    {
		    	if ($val['array']['class_id'] == $key)
		    	{
	
					//Member Template Vars
					$member_array = array(
			          'MEMBER_NAME'    			=> $val['array']['name'],
			          'MEMBER_SPEC'    			=> ($menulink[2] <> '') ? $mySkill : '&nbsp;',
			          'MEMBER_CLASS_ICON'   	=> $game->decorate('classes', array($val['array']['class_id'], false)),
			          'MEMBER_RACE_ICON'		=> $game->decorate('races', array($val['array']['race_id'])),
			          'MEMBER_LEVEL'        	=> ( $val['array']['level'] > 0 ) ? '['. $val['array']['level'].']' : '&nbsp;',
			          'MEMBER_GILDE'        	=> ( $menulink[3] <> '' ) ? '<a href='.$menulink[3].' target=_blank>'.$row['guild'].'</a>' : $row['guild'],
			          'MEMBER_ARMORY_LINK1' 	=> $menulink[1],
			          'MEMBER_ARMORY_LINK2' 	=> $menulink[2],
			          'MEMBER_ARMORY_LINK3' 	=> $menulink[3],
			          'U_VIEW_MEMBER'   		=> 'viewcharacter.php'.$SID . '&amp;member_id='.$val['array']['main_id']
						);
				   	$tpl->assign_block_vars('class_row.member', $member_array );
		    		
		    		
		    	}    	
		    	
		    }	    
		}  
	}
	    
	/*
	 *  altes zeugs aus der 06er, fleigt raus, wenn Walle das CM Zeugs eingebaut hat.
	 * 
	    //Member SQL
		$sql = 'SELECT m.member_name, m.member_id , m.member_status, m.member_level, m.member_class_id, m.member_race_id, m.member_rank_id, r.rank_id, r.rank_hide
		FROM __member_ranks r, __members m
		WHERE m.member_class_id ='.$class_row['class_id'] .'
		AND (m.member_rank_id = r.rank_id)
		AND (r.rank_hide = 1 )';

		//CM Data
		if ($pm->check(PLUGIN_INSTALLED, 'charmanager'))
		{
			$sql = 'SELECT m.member_name, m.member_id , m.member_status, m.member_level, m.member_class_id, ma.* , m.member_race_id, m.member_rank_id, r.rank_id, r.rank_hide
							FROM __member_ranks r, __members m
							LEFT JOIN __member_additions ma ON (ma.member_id=m.member_id)
							WHERE (m.member_class_id ='.$class_row['class_id'] .')
							AND (m.member_rank_id = r.rank_id)
							AND (r.rank_hide = 1 )' ;
		}
    	$members_result = $db->query($sql);

		//Member Array
		while ( $row = $db->fetch_record($members_result) )
		{
			if ( $row )
			{
				if(is_object($armory))
		    	{
					$menulink = array(); $skill = $mySkill = '';
					$menulink[1] = $armory->Link($core->config['pk_server_region'], $row['member_name'], $core->config['pk_servername']);
					$menulink[2] = $armory->Link($core->config['pk_server_region'], $row['member_name'], $core->config['pk_servername'], 'talent1');

					// Spec/ DualSpec
					if(($pm->check(PLUGIN_INSTALLED, 'charmanager')) and ($row['member_id'] > 0))
					{
						$menulink[3] = ($row['guild']) ? $armory->Link($core->config['pk_server_region'], $row['guild'], $core->config['pk_servername'], 'guild') : '';
						$last_update = date($user->lang['uc_changedate'],$row['last_update']);
						$skill = $game->callFunc('get_wow_talent_spec', array(1, $row['skill_1'],$row['skill_2'] ,$row['skill_3'], $row['member_name'], $last_update, true));
						$mySkill = '<a href=' .$menulink[2] . ' target=_blank>'. $skill[icon].'</a>';
						if($row['skill2_1'] or $row['skill2_2'] or $row['skill2_3']){
							$skill = get_wow_talent_spec(ucwords(renameClasstoenglish($class_row['class_name'])), $row['skill2_1'],$row['skill2_2'] ,$row['skill2_3'], $row['member_name'], $last_update, true);
							$mySkill .= ' <a href=' .$menulink[2] . ' target=_blank>'. $skill[icon].'</a>';
						}
					} // end CM installed
					$roster_data[$class_row['class_name']] = $row['member_name'];
				} // end if object

				//Member Template Vars
				$member_array = array(
		          'MEMBER_NAME'    			=> $row['member_name'],
		          'MEMBER_SPEC'    			=> ($menulink[2] <> '') ? $mySkill : '&nbsp;',
		          'MEMBER_CLASS_ICON'   => get_classImgListmembers($row['member_class'] , $row['member_class_id']),
		          'MEMBER_RACE_ICON'		=> ($menulink[1] <> '') ? '<a href='.$menulink[1].' target=_blank>'.get_RaceIcon($row['member_race_id'],$row['member_name']).'</a>' : get_RaceIcon($row['member_race_id'],$row['member_name']),
		          'MEMBER_LEVEL'        => ( $row['member_level'] > 0 ) ? '['. $row['member_level'].']' : '&nbsp;',
		          'MEMBER_GILDE'        => ( $menulink[3] <> '' ) ? '<a href='.$menulink[3].' target=_blank>'.$row['guild'].'</a>' : $row['guild'],
		          'MEMBER_ARMORY_LINK1' => $menulink[1],
		          'MEMBER_ARMORY_LINK2' => $menulink[2],
		          'MEMBER_ARMORY_LINK3' => $menulink[3],
		          'U_VIEW_MEMBER'   		=> 'viewcharacter.php'.$SID . '&amp;name='.$row['member_name']
					);
			   	$tpl->assign_block_vars('class_row.member', $member_array );
			}	// end of if row
		} // end of while

	}	// end user
}	// end if class query
*/

	
// Template Output
$core->set_vars(array(
    'page_title'    => $user->lang['listmembers_title'],
    'template_file' => 'roster.html',
    'display'       => true)
);
?>