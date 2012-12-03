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
 * @copyright	2006-2010 EQdkp-Plus Developer Team
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
	class comments
	{

		// ---------------------------------------------------------
		// Constructor
		// ---------------------------------------------------------
		public function __construct(){
			global $user, $db, $in, $eqdkp_root_path, $tpl, $bbcode, $jquery;
			$this->Username		= $user->data['username'];
			$this->UserID		= $user->data['user_id'];
			$this->version		= '2.0.0';

			// Changeable
			$this->isAdmin		= $user->check_auth('a_config_man', false);
			$this->langprf		= 'comments_';
			$this->userPerm		= true;
			$this->count		= array();

			// mmo vars
			$this->db			= $db;
			$this->in			= $in;
			$this->user			= $user;
			$this->root_path	= $eqdkp_root_path;
			$this->tpl			= $tpl;
			$this->bbcode		= $bbcode;
			$this->jquery		= $jquery;
		}

		// ---------------------------------------------------------
		// Set the Comment Variables during runtime..
		// ---------------------------------------------------------
		public function SetVars($array){
			if($array['auth']){
				$this->isAdmin    = $this->user->check_auth($array['auth'], false);
			}
			if($array['userauth']){
				$this->userPerm   = $this->user->check_auth($array['userauth'], false);
			}
			if($array['language']){
				$this->langprf    = $array['language'];
			}
			if($array['page']){
				$this->page       = $array['page'];
			}
			if($array['attach_id']){
				$this->attach_id  = $array['attach_id'];
			}
		}

		// ---------------------------------------------------------
		// Get the Count of comments for that event/page
		// ---------------------------------------------------------
		public function Count(){
			if(count($this->count[$this->page]) < 1){
				$count_result = $this->db->query("SELECT attach_id, id FROM __comments WHERE page = '".$this->db->escape($this->page)."';");
				while($row = $this->db->fetch_record($comm_result)){
					if(!$this->count[$this->page][$row['attach_id']]){
						$this->count[$this->page][$row['attach_id']] = 0;	
					}
					$this->count[$this->page][$row['attach_id']]++;
				}
			}
			return ($this->count[$this->page][$this->attach_id]) ? $this->count[$this->page][$this->attach_id] : '0';
		}

		// ---------------------------------------------------------
		// Save the Comment
		// ---------------------------------------------------------
		public function Save(){
			if($this->UserID){
				$this->db->query("INSERT INTO __comments :params", array(
					'attach_id'		=> $this->db->escape($this->in->get('attach_id',0)),
					'date'			=> time(),
					'userid'		=> $this->db->escape($this->UserID),
					'text'			=> $this->db->escape(str_replace("\n", "[br]", $this->in->get('comment', '', 'htmlescape'))),
					'page'			=> $this->in->get('page'),
				));
				echo $this->Content($this->in->get('attach_id',0), $this->in->get('page'), $this->in->get('rpath'), true, $this->in->get('lang_prefix'));
			}
		}

		// ---------------------------------------------------------
		// Delete the Comment
		// ---------------------------------------------------------
		public function Delete($page, $rpath){
			$commentarry = $this->GetDBentries($page, $this->in->get('attach_id',0));
			if($this->isAdmin || $commentarry[$this->in->get('deleteid', 0)]['userid'] == $this->UserID){
				$this->db->query("DELETE FROM __comments WHERE id='".$this->db->escape($this->in->get('deleteid',0))."'");
				echo $this->Content($this->in->get('attach_id',0), $this->in->get('page'), $rpath, true, $this->in->get('lang_prefix'));
			}
		}
		
		// ---------------------------------------------------------
		// Delete all Comments of a page, p.e. for uninstall functions
		// ---------------------------------------------------------
		public function Uninstall($page){
			if($page){
				$this->db->query("DELETE FROM __comments WHERE page='".$this->db->escape($page)."'");
			}
		}
		
		// ---------------------------------------------------------
		// Delete all Comments of an id, p.e. for deleting comments on delete of the parent ID
		// ---------------------------------------------------------
		public function delete_all($id){
			if($page){
				$this->db->query("DELETE FROM __comments WHERE attach_id='".$this->db->escape($id)."'");
			}
		}

		// ---------------------------------------------------------
		// HTML Output Code
		// ---------------------------------------------------------
		public function Show(){
			$this->JScode();
			$html = '<div id="htmlCommentTable">';
			$html .= $this->Content($this->attach_id, $this->page);
			$html .= '</div>';
			
			// the line for the comment to be posted
			if($this->Username != ""){
				if($this->userPerm){
					$html .= $this->Form($this->attach_id, $this->page);
				}
			}
			return $html;
		}

		// ---------------------------------------------------------
		// Generate the Content
		// ---------------------------------------------------------
		public function Content($atachid, $page, $rpath='', $issave = false, $mylang=''){
			$i = 0;
			$commdata   = $this->GetDBentries($page, $atachid);
			$myrootpath = ($issave) ? $rpath : $this->root_path;
			$tmplang    = ($mylang) ? $mylang : $this->langprf;
			$this->bbcode->SetSmiliePath($myrootpath.'libraries/jquery/core/images/editor/icons');

			// The delete form
			$html  = '<form id="comment_delete" name="comment_delete" action="'.$this->root_path.'comments.php" method="post">';
			$html .= '</form>';

			// the content Box
			$html .= '<div class="contentBox">';
			$html .= '<div class="boxHeader"><h1>'.$this->user->lang[$tmplang.'comments_raid'].'</h1></div>';
			$html .= '<div class="boxContent">';

			if (is_array($commdata)){
				foreach($commdata as $row){
					$out[] .= '<div class="'.(($i%2) ? 'rowcolor2' : 'rowcolor1').' clearfix">
								<div class="floatLeft" style="overflow: hidden; width: 15%;">'.htmlspecialchars($row['username']).'</div>
								<div class="floatLeft" style="overflow: hidden; width: 85%;">
									<span class="small bold">'.$row['time'].'</span>';
					if($row['delete_button']){
						$out[] .= '<span class="comments_delete small bold floatRight hand" ><img src="'.$myrootpath.'images/global/delete.png" />';
						$out[] .= '<div style="display:none" class="comments_page">'.$page.'</div>';
						$out[] .= '<div style="display:none" class="comments_deleteid">'.$row['id'].'</div>';
						$out[] .= '<div style="display:none" class="comments_attachid">'.$atachid.'</div>';
						$out[] .= '<div style="display:none" class="comments_myrootpath">'.$myrootpath.'</div>';
						$out[] .= '</span>';
					}
					$out[] .= '<br class="clear"/><span class="comment_text">'.$this->bbcode->MyEmoticons($this->bbcode->toHTML($row['message'])).'</span><br/>
									</div>
								</div><br/>';
					$i++;
				
				}
			}

			if(count($out) > 0 ){
				foreach($out as $vvalues){
					$html .= $vvalues;
				}
			}else{
				$html .= $this->user->lang[$tmplang.'no_comments'];
			}
			$html .= '</div></div>';
			return $html;
		}

		// ---------------------------------------------------------
		// Private Functions
		// ---------------------------------------------------------
		private function Form($attachid, $page){
			$html  = $this->jquery->wysiwyg('bbcode');
			$html .= '<div class="contentBox">';
			$html .= '<div class="boxHeader"><h1>'.$this->user->lang[$this->langprf.'write_comment'].'</h1></div>';
			$html .= '<div class="boxContent"><br/>';
			$html .= '<form id="comment_data" name="comment_data" action="'.$this->root_path.'comments.php" method="post">
						<input type="hidden" name="attach_id" value="'.$attachid.'"/>
						<input type="hidden" name="page" value="'.$page.'"/>
						<input type="hidden" name="lang_prefix" value="'.$this->langprf.'"/>
						<input type="hidden" name="rpath" value="'.$this->root_path.'"/>
						<textarea name="comment" id="bbcode" rows="8" cols="69" class="jTagEditor"></textarea><br/><br/>
						<span id="comment_button"><input type="submit" value="'.$this->user->lang[$this->langprf.'send_comment'].'" class="input"/></span>
					</form>';
			$html .= '</div></div>';
			return $html;
		}

		// ---------------------------------------------------------
		// get the DB Entries once
		// ---------------------------------------------------------
		private function GetDBentries($page, $atachid){
			$sql = "SELECT com.text, com.date, com.id, com.userid, u.username, com.attach_id
					FROM __comments com, __users u
					WHERE com.attach_id='".$this->db->escape($atachid)."'
					AND com.page = '".$this->db->escape($page)."'
					AND com.userid = u.user_id
					ORDER BY com.date;";
			$comm_result = $this->db->query($sql);

			while($row = $this->db->fetch_record($comm_result)){
				$outputarray[$row['id']] = array(
					'time'				=> date($this->user->style['date_time'],$row['date']),
					'delete_button'		=> ($this->isAdmin || $row['userid'] == $this->UserID) ? true : false,
					'id'				=> $row['id'],
					'userid'			=> $row['userid'],
					'username'			=> $row['username'],
					'attach_id'			=> $row['attach_id'],
					'message'			=> $row['text']
				);
			}
			return $outputarray;
		}

		// ---------------------------------------------------------
		// Generate the JS Code
		// ---------------------------------------------------------
		private function JScode(){
			$jscode = "
					$(document).ready(function() {

						// Delete Function
						$('.comments_delete').live('click', function(){
							var page				= $('.comments_page',				this).text();
							var deleteid		= $('.comments_deleteid',		this).text();
							var attachid		= $('.comments_attachid',		this).text();
							var myrootpath	= $('.comments_myrootpath',	this).text();

							$('#comment_delete').ajaxSubmit({
								target: '#htmlCommentTable',
								url:		myrootpath+'comments.php?deleteid='+deleteid+'&page='+page+'&attach_id='+attachid+'&rpath='+myrootpath,
								success: function() { 
									$('#htmlCommentTable').fadeIn('slow'); 
								}
							});
						});

						// submit Comment
						$('#comment_data').ajaxForm({
							target: '#htmlCommentTable',
							beforeSubmit:  function(){
								document. comment_data.comment.disabled=true;
								document.getElementById('comment_button').innerHTML='<img src=\"".$this->root_path."images/global/loading.gif\" alt=\"Save\"/> ".$this->user->lang[$this->langprf.'save_wait']."';
							},
							success: function() {
								$('#htmlCommentTable').fadeIn('slow');
								// clear the input field:
								document. comment_data.comment.value = '';
								document. comment_data.comment.disabled=false;
								document.getElementById('comment_button').innerHTML='<input type=\"submit\" value=\"".$this->user->lang[$this->langprf.'send_comment']."\" class=\"input\"/>';
							}
						});
					});";
			$this->tpl->add_js($jscode);
		}
	}
}
?>