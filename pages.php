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
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 *
 * $Id$
 */

define('EQDKP_INC', true);
define('USE_LIGHTBOX', true);
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');

class pages extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('user','tpl', 'in', 'pdh', 'jquery', 'db', 'config', 'core', 'time', 'pm', 'bbcode'	=> 'bbcode', 'comments'	=> 'comments', 'hooks');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public function __construct() {
		$handler = array(
			'mode'	=> array(
				array('process' => 'perform_vote',	'value' => 'vote'),
			),
		);
		parent::__construct(false, $handler, array(), null);
		$this->process();
	}

	public function perform_vote(){
		if ($this->in->get('info_vote')){
			$vote = $this->pdh->get('pages', 'data', array($id));
			if ($vote && $vote['voting'] == '1'){
				$users_voted = $vote['voters'];
				if (!$users_voted[$this->user->data['user_id']]){
					$users_voted[$this->user->data['user_id']]	= $this->in->get('info_vote');
					$rating_points							= $vote['rating'] + $this->in->get('info_vote');
					$votes									= $vote['votes'] + 1;
					$rating									= round($rating_points / $votes);
					$result = $this->db->query("UPDATE __pages SET :params WHERE page_id = '".$this->db->escape($id)."'", array(
						'page_ratingpoints'			=> $this->db->escape($rating_points),
						'page_votes'				=> $this->db->escape($votes),
						'page_rating'				=> $this->db->escape($rating),
						'page_voters'				=> ($users_voted) ? serialize($users_voted) : '',
					));
					$this->pdh->enqueue_hook('pages');
					$this->pdh->process_hook_queue();
				}
			}
			die($this->jquery->StarRatingValue('vote', $rating));
		}else{
			die();
		}
	}

	public function display(){
		if ($this->in->get('page', '')){
			if (is_numeric($this->in->get('page', ''))) {
				$id = $this->in->get('page', '');
			}else {
				$id = $this->pdh->get('pages', 'alias_to_page', array($this->in->get('page', '')));
			}
		}else{
			message_die($this->user->lang('info_invalid_id'), $this->user->lang('info_invalid_id_title'));
		}
		if(!$this->pdh->get('pages', 'page_exists', array($id))){
			message_die($this->user->lang('info_invalid_id'), $this->user->lang('info_invalid_id_title'));
		}

		if (!$this->pdh->get('pages', 'check_visibility', array($id))){
			message_die($this->user->lang('noauth_u_information_view'), $this->user->lang('noauth_default_title'));
		}

		$content = xhtml_entity_decode($this->pdh->get('pages', 'content', array($id)));
		$arrHooks = $this->hooks->process('pages_parse', array('text' => $content), true);

		$content = $arrHooks['text'];
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

		$users_voted = $this->pdh->get('pages', 'voters', array($id));
		$u_has_voted = (!$users_voted[$this->user->data['user_id']]) ? false : true;

		$this->tpl->assign_vars(array(
			'PAGE_ID'				=> $id,
			'INFO_PAGE_CONTENT'		=> $this->bbcode->parse_shorttags($content),
			'INFO_PAGE_TITLE'		=> sanitize($this->pdh->get('pages', 'title', array($id))),
			'EDITED' 				=> ($this->pdh->get('pages', 'edit_date', array($id))) ? $this->user->lang('info_edit_user').$this->pdh->get('user', 'name', array($this->pdh->get('pages', 'edit_user', array($id)))).$this->user->lang('info_edit_date').$this->time->user_date($this->pdh->get('pages', 'edit_date', array($id)), false, false, true) : '',
			'S_IS_ADMIN'			=>	$this->user->check_auth('a_pages_man', false),
			'STAR_RATING'			=> ($this->pdh->get('pages', 'voting', array($id)) == '1') ? $this->jquery->StarRating('info_vote', $myRatings,'pages.php'.$this->SID.'&page='.sanitize($id).'&mode=vote',$this->pdh->get('pages', 'rating', array($id)), $u_has_voted) : '',
		));

		//Comment-System
		if ($this->pdh->get('pages', 'comments', array($id)) == '1'){
			$this->comments->SetVars(array(
				'attach_id'	=> $id,
				'page'		=> 'custompages',
				'auth'		=> 'a_pages_man',
			));
			$this->tpl->assign_vars(array(
				'COMMENTS'			=> $this->comments->Show(),
			));
		};

		$this->core->set_vars(array(
			'page_title'		=> sanitize($this->pdh->get('pages', 'title', array($id))),
			'template_file'		=> 'pages.html',
			'description'		=> substr(strip_tags($this->bbcode->remove_embeddedMedia($this->bbcode->remove_shorttags(xhtml_entity_decode($content)))), 0, 250),
			'image'				=> register('socialplugins')->getFirstImage(xhtml_entity_decode($content)),
			'display'			=> true)
		);
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pages', pages::__shortcuts());
registry::register('pages');

?>