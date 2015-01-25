<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2015 EQdkp-Plus Developer Team
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU Affero General Public License as published
 *	by the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU Affero General Public License for more details.
 *
 *	You should have received a copy of the GNU Affero General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

if (!class_exists("comments")){
	class comments extends gen_class {

		public $count = array();
		public $userPerm = true;
		public $isAdmin = false;
		public $showReplies = false;
		private $id = '';
		private $showFormForGuests=false;
		public $ntfy_link = false;
		public $ntfy_user = false;
		public $ntfy_type = false;
		public $ntfy_title = false;
		public $ntfy_category = 0;

		// ---------------------------------------------------------
		// Constructor
		// ---------------------------------------------------------
		public function __construct($id=''){
			//ID for multiple instances on one page
			$this->id = $id;
			
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
			if(isset($array['replies'])){
				$this->showReplies	= $array['replies'];
			}
			if(isset($array['formforguests'])){
				$this->showFormForGuests = $array['formforguests'];
			}
			if(isset($array['ntfy_link'])){
				$this->ntfy_link = $array['ntfy_link'];
			}
			if(isset($array['ntfy_title'])){
				$this->ntfy_title = $array['ntfy_title'];
			}
			if(isset($array['ntfy_user'])){
				$this->ntfy_user = $array['ntfy_user'];
			}
			if(isset($array['ntfy_type'])){
				$this->ntfy_type = $array['ntfy_type'];
			}
			if(isset($array['ntfy_category'])){
				$this->ntfy_category = $array['ntfy_category'];
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
			$data = array(
				'user_id' 	=> $this->UserID,
				'attach_id' => 	$this->in->get('attach_id'),
				'comment'	=> $this->in->get('comment', '', 'htmlescape'),
				'page'		=> $this->in->get('page'),
				'reply_to'	=>  $this->in->get('reply_to', 0),
				'permission'=> ($this->UserID && $this->userPerm),
			);
			
			//Hooks
			$data = $this->hooks->process('comments_save', $data, true);
			
			if($data['permission']){
				$intCommentId = $this->pdh->put('comment', 'insert', array($data['attach_id'], $data['user_id'], $data['comment'], $data['page'], $data['reply_to']));
				
				$intToUserID = (int)$data['attach_id'];
				$intFromUserId = (int)$data['user_id'];
				$strToUsername = $this->pdh->get('user', 'name', array($intToUserID));
				$strFromUsername = $this->pdh->get('user', 'name', array($intFromUserId));
				
				//Notifications Userwall
				if ($data['page'] === 'userwall'){
					if ($this->in->get('reply_to', 0) === 0){
						$this->ntfy->add('comment_new_userwall', $intCommentId, $strFromUsername, $this->routing->build('user', $strToUsername, 'u'.$intToUserID, true, true), $intToUserID);
					} else {
						//Userwall User
						$this->ntfy->add('comment_new_userwall_response', $intCommentId, $strFromUsername, $this->routing->build('user', $strToUsername, 'u'.$intToUserID, true, true), $intToUserID);
						//Owner of Comment
						$userid = $this->pdh->get('comment', 'userid', array($this->in->get('reply_to', 0)));
						if ($userid) $this->ntfy->add('comment_new_response', $intCommentId, $strFromUsername, $this->routing->build('user', $strToUsername, 'u'.$intToUserID, true, true), $userid, $this->user->lang('user_wall').' '.$strToUsername);	
					}
				}
				
				//Other Notifications
				$ntfyType = $this->in->get('ntfy_type');
				if ($ntfyType != "" && $ntfyType != "comment_new_userwall" && $ntfyType != "comment_new_userwall_response"){
					$ntfyLink = $this->in->get('ntfy_link').'#comments';
					$ntfyCategory = $this->in->get('ntfy_category', 0);
					$ntfyTitle = $this->in->get('ntfy_title');
					$ntfyUser = (strlen($this->in->get('ntfy_user'))) ? explode(',', $this->in->get('ntfy_user')) : false;
					
					if ($ntfyType === 'comment_new_article'){
						$this->ntfy->add('comment_new_article', $intCommentId, $strFromUsername, $ntfyLink, $ntfyUser, $ntfyTitle, $ntfyCategory);
					} else {
						$this->ntfy->add($ntfyType, $intCommentId, $strFromUsername, $ntfyLink, $ntfyUser, $ntfyTitle);
					}
				
					//Notify Comment Writer if its a reply
					if ($this->in->get('reply_to', 0) !== 0){
						$userid = $this->pdh->get('comment', 'user_id', array($this->in->get('reply_to', 0)));
						if ($userid) $this->ntfy->add('comment_new_response', $intCommentId, $strFromUsername, $ntfyLink, $userid);
					}
				}

				//Mentions
				$strContent = $data['comment'];
				$arrMentions = $arrNormalMatches = array();
				$intMatches = preg_match_all("/@(\w*)/", $strContent, $arrNormalMatches);
				if($intMatches){
					foreach($arrNormalMatches[1] as $key => $strMatch){
						if($strMatch != "") $arrMentions[] = array(utf8_strtolower($strMatch), $arrNormalMatches[0][$key]);
					}
				}

				$intMatches = preg_match_all('/@&#34;(.*?)&#34;/', $strContent, $arrNormalMatches);
				if($intMatches){
					foreach($arrNormalMatches[1] as $key => $strMatch){
						if($strMatch != "") $arrMentions[] = array(utf8_strtolower($strMatch), $arrNormalMatches[0][$key]);
					}
				}
				$intMatches = preg_match_all("/@&#39;(.*?)&#39;/", $strContent, $arrNormalMatches);
				if($intMatches){
					foreach($arrNormalMatches[1] as $key => $strMatch){
						if($strMatch != "")  $arrMentions[] = array(utf8_strtolower($strMatch), $arrNormalMatches[0][$key]);
					}
				}

				if(count($arrMentions) > 0){
					$arrUsers = $this->pdh->aget('user', 'name', 0, array($this->pdh->get('user', 'id_list')));
					$arrDone = array();
					$ntfyLink = $this->in->get('ntfy_link').'#comment'.$intCommentId;
					$ntfyTitle = $this->in->get('ntfy_title');
					$blnTextChanged = false;
					foreach($arrMentions as $arrMention){
						$strMention = $arrMention[0];
						foreach($arrUsers as $userid => $username){
							if(utf8_strtolower($username) === $strMention){
								if(!in_array($userid, $arrDone)){
									$arrDone[] = $userid;
									$this->ntfy->add('comment_new_mentioned', $intCommentId, $strFromUsername, $ntfyLink, $userid, $ntfyTitle);
								}
								
								$strUserlink = $this->env->link.$this->routing->build('user', $username, 'u'.$userid, false, true);
								$data['comment'] = str_replace($arrMention[1], '[url="'.$strUserlink.'"]@'.$username.'[/url]', $data['comment']);
								$blnTextChanged = true;
							}
						}
					}
					if($blnTextChanged){
						$this->pdh->put('comment', 'update', array($intCommentId, $data['comment']));
					}
				}

				$this->pdh->process_hook_queue();
				echo $this->Content($data['attach_id'], $data['page'], ($data['reply_to'] || $this->in->get('replies', 0)));
			}
		}

		// ---------------------------------------------------------
		// Delete the Comment
		// ---------------------------------------------------------
		public function Delete($page, $blnShowReplies=false){
			$intCommentID = $this->in->get('deleteid',0);
			
			if($this->isAdmin || $this->pdh->get('comment', 'userid', array($intCommentID)) == $this->UserID){
				$this->pdh->put('comment', 'delete', array($intCommentID));
				$this->ntfy->deleteNotification('comment_new_article', $intCommentID);
				$this->ntfy->deleteNotification('comment_new_userwall', $intCommentID);
				$this->ntfy->deleteNotification('comment_new_userwall_response', $intCommentID);
				
				$this->pdh->process_hook_queue();
				echo $this->Content($this->in->get('attach_id',''), $this->in->get('page'), $blnShowReplies);
			}
		}

		// ---------------------------------------------------------
		// HTML Output Code
		// ---------------------------------------------------------
		public function Show(){
			$this->JScode();
			$html	= '<div id="plusComments'.$this->id.'"><div id="htmlCommentTable'.$this->id.'">';
			$html	.= $this->Content($this->attach_id, $this->page);
			$html	.= '</div>';
			if ($this->showReplies){
				$html .= $this->ReplyForm($this->attach_id, $this->page);
			}

			// the line for the comment to be posted
			if(($this->user->is_signedin() && $this->userPerm) || $this->showFormForGuests){
				$html .= $this->Form($this->attach_id, $this->page);
			} else {
				$html .= '<div id="comment_data'.$this->id.'"><input type="hidden" name="attach_id" value="'.$this->attach_id.'"/>
						<input type="hidden" name="page" value="'.$this->page.'"/></div>';
			}
			$html .= '</div>';
			return $html;
		}

		// ---------------------------------------------------------
		// Generate the Content
		// ---------------------------------------------------------
		public function Content($attachid, $page, $blnShowReplies=false){
			$i				= 0;
			$comments		= $this->pdh->get('comment', 'filtered_list', array($page, $attachid));
			$myrootpath		= $this->server_path;
			$this->bbcode->SetSmiliePath($myrootpath.'images/smilies');
			// The delete form
			$html	= '<form id="comment_delete" name="comment_delete" action="'.$this->server_path.'exchange.php'.$this->SID.'&amp;out=comments" method="post">';
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
					$out[] .= '<div class="comment '.(($i%2) ? 'rowcolor2' : 'rowcolor1').' clearfix" id="comment'.$row['id'].'">
								<div class="comment_id" style="display:none;">'.$row['id'].'</div>
								<div class="comment_avatar_container">
									<div class="comment_avatar"><a href="'.$this->routing->build('user', $row['username'], 'u'.$row['userid']).'"><img src="'.(($avatarimg) ? $this->pfh->FileLink($avatarimg, false, 'absolute') : $myrootpath.'images/global/avatar-default.svg').'" alt="Avatar" class="user-avatar"/></a></div>
								</div>
								<div class="comment_container">
									<div class="comment_author"><a href="'.$this->routing->build('user', $row['username'], 'u'.$row['userid']).'">'.sanitize($row['username']).'</a>, '.$this->time->createTimeTag($row['date'], $this->time->user_date($row['date'], true)).'</div>';
					if($this->isAdmin OR $row['userid'] == $this->UserID){
						$out[] .= '<div class="comments_delete bold floatRight hand"><i class="fa fa-times-circle fa-lg icon-grey"></i>';
						$out[] .= '<div style="display:none" class="comments_page">'.$page.'</div>';
						$out[] .= '<div style="display:none" class="comments_deleteid">'.$row['id'].'</div>';
						$out[] .= '<div style="display:none" class="comments_attachid">'.$attachid.'</div>';
						$out[] .= '<div style="display:none" class="comments_myrootpath">'.$myrootpath.'</div>';
						$out[] .= '</div>';
					}
					$out[] .= '<div class="comment_text">'.$this->bbcode->MyEmoticons($this->bbcode->toHTML($row['text'])).'</div><br/>
								</div>';
								
								
					$i++;
					
					//Replies
					if (($this->showReplies || $blnShowReplies) && count($row['replies'])) {
						$j=0;
						foreach($row['replies'] as $com){
							// Avatar
							$avatarimg = $this->pdh->get('user', 'avatarimglink', array($com['userid']));

							// output
							$out[] .= '<div class="clear"></div><br/><div class="comment-reply '.(($j%2) ? 'rowcolor2' : 'rowcolor1').' clearfix" id="comment'.$row['id'].'">
										<div class="comment_id" style="display:none;">'.$com['id'].'</div>
										<div class="comment_avatar_container">
											<div class="comment_avatar"><a href="'.$this->routing->build('user', $com['username'], 'u'.$com['userid']).'"><img src="'.(($avatarimg) ? $this->pfh->FileLink($avatarimg, false, 'absolute') : $myrootpath.'images/global/avatar-default.svg').'" alt="Avatar" class="user-avatar"/></a></div>
										</div>
										<div class="comment_container">
											<div class="comment_author"><a href="'.$this->routing->build('user', $com['username'], 'u'.$com['userid']).'">'.sanitize($com['username']).'</a>, '.$this->time->createTimeTag($com['date'], $this->time->user_date($com['date'], true)).'</div>';
							if($this->isAdmin OR $com['userid'] == $this->UserID){
								$out[] .= '<div class="comments_delete bold floatRight hand"><i class="fa fa-times-circle fa-lg icon-grey"></i>';
								$out[] .= '<div style="display:none" class="comments_page">'.$page.'</div>';
								$out[] .= '<div style="display:none" class="comments_deleteid">'.$com['id'].'</div>';
								$out[] .= '<div style="display:none" class="comments_attachid">'.$attachid.'</div>';
								$out[] .= '<div style="display:none" class="comments_myrootpath">'.$myrootpath.'</div>';
								$out[] .= '</div>';
							}
							$out[] .= '<div class="comment_text">'.$this->bbcode->MyEmoticons($this->bbcode->toHTML($com['text'])).'</div><br/>
										</div>
										</div>
										
										';
							$j++;
						}
					}
					if (($this->showReplies || $blnShowReplies) && $this->user->is_signedin()){
						$out[] .= '<div class="comment_reply_container">
										<button class="reply-trigger"><i class="fa fa-reply"></i>'.$this->user->lang('reply').'</button>
										<div class="reply-form-container">
										</div>
									</div>';
					}
								
					$out[] .='	</div>
								
								<br/>';
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
			$editor->editor_bbcode(array('mention' => true));
			$avatarimg = $this->pdh->get('user', 'avatarimglink', array($this->user->id));
			$html = '<div class="contentBox writeComments">';
			$html .= '<div class="boxHeader"><h1>'.$this->user->lang('comments_write').'</h1></div>';
			$html .= '<div class="boxContent"><br/>';
			$html .= '<form id="comment_data'.$this->id.'" name="comment_data" action="'.$this->server_path.'exchange.php'.$this->SID.'&amp;out=comments&replies='.(($this->showReplies) ? 1 : 0).'" method="post">
						<input type="hidden" name="attach_id" value="'.$attachid.'"/>
						<input type="hidden" name="page" value="'.$page.'"/>';
			if ($this->ntfy_type !== false){
				
				$html .= '<input type="hidden" name="ntfy_type" value="'.$this->ntfy_type.'"/>
						<input type="hidden" name="ntfy_category" value="'.$this->ntfy_category.'"/>
						<input type="hidden" name="ntfy_title" value="'.$this->ntfy_title.'"/>
						<input type="hidden" name="ntfy_user" value="'.implode(',',$this->ntfy_user).'"/>
						<input type="hidden" name="ntfy_link" value="'.$this->ntfy_link.'"/>';
			}
			
			$html .=	'<div class="clearfix">
							<div class="comment_avatar_container">
								<div class="comment_avatar"><a href="'.$this->routing->build('user', $this->user->data['username'], 'u'.$this->user->id).'"><img src="'.(($avatarimg) ? $this->pfh->FileLink($avatarimg, false, 'absolute') : $this->server_path.'images/global/avatar-default.svg').'" alt="Avatar" class="user-avatar"/></a></div>
							</div>
							<div class="comment_write_container">
								<textarea name="comment" rows="5" cols="80" class="mceEditor_bbcode" style="width:100%;"></textarea>
							</div>
						</div>
						<span id="comment_button'.$this->id.'"><input type="submit" value="'.$this->user->lang('comments_send_bttn').'" class="input"/></span>
					</form>';
			$html .= '</div></div>';
			
			
			
			return $html;
		}
		
		private function ReplyForm($attachid, $page){
			$editor = registry::register('tinyMCE');
			$editor->editor_bbcode(array('mention' => true));
			$avatarimg = $this->pdh->get('user', 'avatarimglink', array($this->user->id));
			
			$html = '<div class="commentReplyForm" style="display:none;">
					<form class="comment_reply" action="'.$this->server_path.'exchange.php'.$this->SID.'&amp;out=comments" method="post">
						<input type="hidden" name="attach_id" value="'.$attachid.'"/>
						<input type="hidden" name="page" value="'.$page.'"/>
						<input type="hidden" name="reply_to" value="0"/>
						<div class="clearfix">
							<div class="comment_avatar_container">
								<div class="comment_avatar"><a href="'.$this->routing->build('user', $this->user->data['username'], 'u'.$this->user->id).'"><img src="'.(($avatarimg) ? $this->pfh->FileLink($avatarimg, false, 'absolute') : $this->server_path.'images/global/avatar-default.svg').'" alt="Avatar" class="user-avatar"/></a></div>
							</div>
							<div class="comment_write_container">
								<textarea name="comment" rows="2" cols="80" class="" style="width:100%;"></textarea>
							</div>
							<span class="reply_button"><input type="submit" value="'.$this->user->lang('comments_send_bttn').'" class="input"/></span>
						</div>
					</form>
				</div>';
			return $html;
		}

		// ---------------------------------------------------------
		// Generate the JS Code
		// ---------------------------------------------------------
		private function JScode(){
			$jscode = "
						// Delete Function
						$(document).on('click', '#plusComments".$this->id." .comments_delete', function(){
							var page			= $('.comments_page',		this).text();
							var deleteid		= $('.comments_deleteid',	this).text();
							var attachid		= $('.comments_attachid',	this).text();

							$('#comment_delete').ajaxSubmit({
								target: '#htmlCommentTable".$this->id."',
								url:	'".$this->server_path."exchange.php".$this->SID."&out=comments&deleteid='+deleteid+'&page='+page+'&attach_id='+attachid+'&replies=".(($this->showReplies) ? 1 : 0)."',
								success: function() {
									$('#htmlCommentTable".$this->id."').fadeIn('slow');
								}
							});
						});
												
						//Show Reply Form
						$(document).on('click', '#plusComments".$this->id." .reply-trigger', function(){
							var reply_to = $(this).parent().parent().find('.comment_id:first').text();
							var newform = $('#plusComments".$this->id." .commentReplyForm').html();
							$('#plusComments".$this->id." .reply-trigger').show();
							$('#plusComments".$this->id." .form-active').remove();
							$(this).hide('fast');
							var container = $(this).parent().find('.reply-form-container');						
							$(container).html(newform);
							$(container).find('.comment_reply').addClass('form-active');					
							var myform = $(container).find('.comment_reply');
							$(myform).find('textarea').addClass('mceEditor_bbcode');
							$(myform).attr('id', 'comment_reply".$this->id."');
							$(myform).find('input[name=reply_to]').val(reply_to);
							
							initialize_bbcode_editor();
							initialize_submit_reply".$this->id."();
						});
									
						//Submit Reply
						function initialize_submit_reply".$this->id."(){
							$('#comment_reply".$this->id."').ajaxForm({
								target: '#htmlCommentTable".$this->id."',
								beforeSubmit:  function(){
									$('#plusComments".$this->id." .reply_button').html('<i class=\"fa fa-spinner fa-spin fa-lg\"></i> ".$this->user->lang('comments_savewait')."');
								},
								success: function() {
									$('#htmlCommentTable".$this->id."').fadeIn('slow');
									// clear the input field:
									$('#plusComments".$this->id." .reply_button').html('<input type=\"submit\" value=\"".$this->user->lang('comments_send_bttn')."\" class=\"input\"/>');
								}
							});
						}
											
						// submit Comment
						$('#comment_data".$this->id."').ajaxForm({
							target: '#htmlCommentTable".$this->id."',
							beforeSubmit:  function(){
								$('#comment_button".$this->id."').html('<i class=\"fa fa-spinner fa-spin fa-lg\"></i> ".$this->user->lang('comments_savewait')."');
							},
							success: function() {
								$('#htmlCommentTable".$this->id."').fadeIn('slow');
								// clear the input field:
								$(\".mceEditor_bbcode\").val('');
								$(\".mceEditor_bbcode\").tinymce().setContent('');
								$('#comment_button".$this->id."').html('<input type=\"submit\" value=\"".$this->user->lang('comments_send_bttn')."\" class=\"input\"/>');
							}
						});
						
						";
			$this->tpl->add_js($jscode, 'docready');
			
			$jscode =	"//Reload comments
						function reload_comments".$this->id."(){
							var form = $('#comment_data".$this->id."');
							var page = form.find(\"input[name='page']\").val();
							var attach_id = form.find(\"input[name='attach_id']\").val();
							window.setTimeout(\"reload_comments".$this->id."()\", 60000*5); // 5 Minute
							
							$.ajax({
							url: '".$this->server_path."exchange.php".$this->SID."&out=comments&page='+page+'&attach_id='+attach_id+'&replies=".(($this->showReplies) ? 1 : 0)."',
								success: function(data){ $('#htmlCommentTable".$this->id."').html(data);},
							});					
						}
						window.setTimeout(\"reload_comments".$this->id."()\", 60000*5); // 5 Minute
						";
			$this->tpl->add_js($jscode, 'eop');				

		}
	}
}
?>