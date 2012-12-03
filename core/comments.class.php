<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2008
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

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

if (!class_exists("comments")){
	class comments extends gen_class {
		public static $shortcuts = array('user', 'tpl', 'pdh', 'time', 'in', 'pfh',
			'bbcode'	=> 'bbcode',
		);

		public $count = array();
		public $userPerm = true;
		public $isAdmin = false;

		// ---------------------------------------------------------
		// Constructor
		// ---------------------------------------------------------
		public function __construct(){
			$this->UserID		= (isset($this->user->data['user_id']) && $this->user->is_signedin()) ? $this->user->data['user_id'] : false;
			$this->version		= '2.0.0';

			// Changeable
			$this->isAdmin		= $this->user->check_auth('a_config_man', false); //TODO: check for a specific group-membership
		}

		// ---------------------------------------------------------
		// Set the Comment Variables during runtime..
		// ---------------------------------------------------------
		public function SetVars($array){
			if(isset($array['auth'])){
				$this->isAdmin		= $this->user->check_auth($array['auth'], false);
			}
			if(isset($array['userauth'])){
				$this->userPerm		= $this->user->check_auth($array['userauth'], false);
			}
			if(isset($array['page'])){
				$this->page			= $array['page'];
			}
			if(isset($array['attach_id'])){
				$this->attach_id	= $array['attach_id'];
			}
		}

		// ---------------------------------------------------------
		// Get the Count of comments for that event/page
		// ---------------------------------------------------------
		public function Count(){
			return $this->pdh->get('comment', 'count', array($this->page, $this->attach_id));
		}

		// ---------------------------------------------------------
		// Save the Comment
		// ---------------------------------------------------------
		public function Save(){
			if($this->UserID){
				$this->pdh->put('comment', 'insert', array($this->in->get('attach_id',0), $this->UserID, $this->in->get('comment', '', 'htmlescape'), $this->in->get('page')));
				$this->pdh->process_hook_queue();
				echo $this->Content($this->in->get('attach_id',''), $this->in->get('page'), $this->in->get('rpath'), true);
			}
		}

		// ---------------------------------------------------------
		// Delete the Comment
		// ---------------------------------------------------------
		public function Delete($page, $rpath){
			if($this->isAdmin || $this->pdh->get('comment', 'userid', array($this->in->get('deleteid', 0))) == $this->UserID){
				$this->pdh->put('comment', 'delete', array($this->in->get('deleteid',0)));
				$this->pdh->process_hook_queue();
				echo $this->Content($this->in->get('attach_id',''), $this->in->get('page'), $rpath, true);
			}
		}

		// ---------------------------------------------------------
		// HTML Output Code
		// ---------------------------------------------------------
		public function Show(){
			$this->JScode();
			$html	= '<div id="htmlCommentTable">';
			$html	.= $this->Content($this->attach_id, $this->page);
			$html	.= '</div>';

			// the line for the comment to be posted
			if($this->user->is_signedin() && $this->userPerm){
				$html .= $this->Form($this->attach_id, $this->page);
			}
			return $html;
		}

		// ---------------------------------------------------------
		// Generate the Content
		// ---------------------------------------------------------
		public function Content($attachid, $page, $rpath='', $issave = false){
			$i				= 0;
			$comments		= $this->pdh->get('comment', 'filtered_list', array($page, $attachid));
			$myrootpath		= ($issave) ? clean_rootpath($rpath) : $this->root_path;
			$this->bbcode->SetSmiliePath($myrootpath.'images/smilies');

			// The delete form
			$html	= '<form id="comment_delete" name="comment_delete" action="'.$this->root_path.'exchange.php'.$this->SID.'&amp;out=comments" method="post">';
			$html	.= '</form>';

			// the content Box
			$html	.= '<div class="contentBox">';
			$html	.= '<div class="boxHeader"><h1>'.$this->user->lang('comments').'</h1></div>';
			$html	.= '<div class="boxContent">';

			$out = '';
			if (is_array($comments)){
				foreach($comments as $row){
					// Avatar
					$avatarimg = $this->pdh->get('user', 'avatarimglink', array($row['userid']));

					// output
					$out[] .= '<div class="'.(($i%2) ? 'rowcolor2' : 'rowcolor1').' clearfix">
								<div class="floatLeft" style="overflow: hidden; width: 15%;">
									<div class="comment_avatar"><img src="'.(($avatarimg) ? $this->pfh->FileLink($avatarimg, false, 'absolute') : $myrootpath.'images/no_pic.png').'" alt="Avatar" /></div>
								</div>
								<div class="floatLeft" style="overflow: hidden; width: 85%;">
									<span class="small bold">'.htmlspecialchars($row['username']).' am '.$this->time->user_date($row['date'], true).'</span>';
					if($this->isAdmin OR $row['userid'] == $this->UserID){
						$out[] .= '<div class="comments_delete small bold floatRight hand" ><img src="'.$myrootpath.'images/global/delete.png" alt="" />';
						$out[] .= '<div style="display:none" class="comments_page">'.$page.'</div>';
						$out[] .= '<div style="display:none" class="comments_deleteid">'.$row['id'].'</div>';
						$out[] .= '<div style="display:none" class="comments_attachid">'.$attachid.'</div>';
						$out[] .= '<div style="display:none" class="comments_myrootpath">'.$myrootpath.'</div>';
						$out[] .= '</div>';
					}
					$out[] .= '<br class="clear"/><span class="comment_text">'.$this->bbcode->MyEmoticons($this->bbcode->toHTML($row['text'])).'</span><br/>
									</div>
								</div><br/>';
					$i++;
				}
			}

			if(isset($out) && is_array($out) && count($out) > 0){
				foreach($out as $vvalues){
					$html .= $vvalues;
				}
			}else{
				$html .= $this->user->lang('comments_empty');
			}
			$html .= '</div></div>';
			return $html;
		}

		// ---------------------------------------------------------
		// Private Functions
		// ---------------------------------------------------------
		private function Form($attachid, $page){
			$editor = registry::register('tinyMCE');
			$editor->editor_bbcode();
			$html = '<div class="contentBox">';
			$html .= '<div class="boxHeader"><h1>'.$this->user->lang('comments_write').'</h1></div>';
			$html .= '<div class="boxContent"><br/>';
			$html .= '<form id="comment_data" name="comment_data" action="'.$this->root_path.'exchange.php'.$this->SID.'&amp;out=comments" method="post">
						<input type="hidden" name="attach_id" value="'.$attachid.'"/>
						<input type="hidden" name="page" value="'.$page.'"/>
						<input type="hidden" name="rpath" value="'.$this->root_path.'"/>
						<textarea name="comment" rows="10" cols="80" class="mceEditor_bbcode" style="width:100%;"></textarea><br/><br/>
						<span id="comment_button"><input type="submit" value="'.$this->user->lang('comments_send_bttn').'" class="input"/></span>
					</form>';
			$html .= '</div></div>';
			return $html;
		}

		// ---------------------------------------------------------
		// Generate the JS Code
		// ---------------------------------------------------------
		private function JScode(){
			$jscode = "
						// Delete Function
						$(document).on('click', '.comments_delete', function(){
							var page			= $('.comments_page',		this).text();
							var deleteid		= $('.comments_deleteid',	this).text();
							var attachid		= $('.comments_attachid',	this).text();
							var myrootpath		= $('.comments_myrootpath',	this).text();

							$('#comment_delete').ajaxSubmit({
								target: '#htmlCommentTable',
								url:		myrootpath+'exchange.php".$this->SID."&out=comments&deleteid='+deleteid+'&page='+page+'&attach_id='+attachid+'&rpath='+myrootpath,
								success: function() {
									$('#htmlCommentTable').fadeIn('slow');
								}
							});
						});

						// submit Comment
						$('#comment_data').ajaxForm({
							target: '#htmlCommentTable',
							beforeSubmit:  function(){
								document.getElementById('comment_button').innerHTML='<img src=\"".$this->root_path."images/global/loading.gif\" alt=\"Save\"/> ".$this->user->lang('comments_savewait')."';
							},
							success: function() {
								$('#htmlCommentTable').fadeIn('slow');
								// clear the input field:
								$(\".mceEditor_bbcode\").tinymce().setContent('');
								document.getElementById('comment_button').innerHTML='<input type=\"submit\" value=\"".$this->user->lang('comments_send_bttn')."\" class=\"input\"/>';
							}
						});";
			$this->tpl->add_js($jscode, 'docready');
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_comments', comments::$shortcuts);
?>