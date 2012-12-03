<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2009
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

define('EQDKP_INC', true);
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');

$user->check_auth('u_infopages_view');

if ($in->get('page')){
	if (is_numeric($in->get('page'))) {
		$id = $in->get('page');

	}else {
		$id = $pdh->get('infopages', 'alias_to_page', array($in->get('page')));
	}

} else {
	message_die($user->lang['info_invalid_id'], $user->lang['info_invalid_id_title']);
}

if ($pdh->get('infopages', 'page_exists', array($id))){
	
		if (!$pdh->get('infopages', 'check_visibility', array($id))){
			message_die($user->lang['noauth_u_information_view'], $user->lang['noauth_default_title']);
		}
		
		//Vote-mode
		if ($in->get('mode') == 'vote'){

			if ($in->get('info_vote')){

				$vote = $pdh->get('infopages', 'data', array($id));

				if ($vote && $vote['voting'] == '1'){

						$users_voted = $vote['voters'];

						if (!$users_voted[$user->data['user_id']]){

							$users_voted[$user->data['user_id']] = $in->get('info_vote');
							$rating_points = $vote['rating'] + $in->get('info_vote');
							$votes = $vote['votes'] + 1;
							$rating = round($rating_points / $votes);
							
							$result = $db->query("UPDATE __pages SET :params WHERE page_id = '".$db->escape($id)."'", array(
					        	'page_ratingpoints'	=> $db->escape($rating_points),
					        	'page_votes'				=> $db->escape($votes),
					        	'page_rating'				=> $db->escape($rating),
										'page_voters'				=> ($users_voted) ? serialize($users_voted) : '',
							));

							$pdh->enqueue_hook('infopages');
							$pdh->process_hook_queue();
						};
		
					
				}
			
				die($jquery->StarRatingValue('vote', $rating));
			}else{	
				die();
			}
		} //END vote-mode

		$content = html_entity_decode($pdh->get('infopages', 'content', array($id)));
		
		if (is_array($pm->hooks['infopages_parse'])){
			foreach ($pm->hooks['infopages_parse'] as $plugin_code => $function){
				$content = $pm->do_hook('infopages_parse', $plugin_code, array('text'	=> $content));
			}
		}

		
		$myRatings = array(
			'1'		=> '1',
			'2'		=> '2',
			'3'		=> '3',
			'4'		=> '4',
			'5'		=> '5',
			'6'		=> '6',
			'7'		=> '7',
			'8'		=> '8',
			'9'		=> '9',
			'10'	=> '10',
		);
		
		$users_voted = $pdh->get('infopages', 'voters', array($id));
		$u_has_voted = (!$users_voted[$user->data['user_id']]) ? false : true;
		include_once($eqdkp_root_path.'infotooltip/infotooltip.class.php');
		$tpl->assign_vars(array(
			'PAGE_ID'							=> $id,
			'INFO_PAGE_CONTENT' 	=> $pdh->get('infopages', 'parse_infopages', array(replace_bbcode($content))),
			'INFO_PAGE_TITLE' 		=> sanitize($pdh->get('infopages', 'title', array($id))), 
			'EDITED' 							=> ($pdh->get('infopages', 'edit_date', array($id))) ? $user->lang['info_edit_user'].$pdh->get('user', 'name', array($pdh->get('infopages', 'edit_user', array($id)))).$user->lang['info_edit_date'].$time->date($user->style['date_notime_long'], $pdh->get('infopages', 'edit_date', array($id))) : '',	
			'S_IS_ADMIN'					=>	$user->check_auth('a_infopages_man', false),
			'EDIT_PAGE'						=> $user->lang['info_edit_page'],
			'STAR_RATING'					=> ($pdh->get('infopages', 'voting', array($id)) == '1') ? $jquery->StarRating('info_vote', $myRatings,'pages.php'.$SID.'&page='.sanitize($id).'&mode=vote',$pdh->get('infopages', 'rating', array($id)), $u_has_voted) : '',
		));
	
	
				//Comment-System
		if ($pdh->get('infopages', 'comments', array($id)) == '1'){

			if(is_object($pcomments) && $pcomments->version > '1.0.3'){
				$comm_settings = array(
					'attach_id' => $id, 
					'page'      => 'infopages',
					'auth'      => 'a_infopages_man',
				);
	  
				$pcomments->SetVars($comm_settings);
				$tpl->assign_vars(array(
					'ENABLE_COMMENTS'     => true,
					'COMMENTS'            => $pcomments->Show(),
				));
    
			};
		
		};
	
} else {
	message_die($user->lang['info_invalid_id'], $user->lang['info_invalid_id_title']);
}

$core->set_vars(array(
   'page_title'    => sanitize($pdh->get('infopages', 'title', array($id))),
   'template_file' => 'infopage.html',
   'display' 			 => true)
);


?>