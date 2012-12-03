<?php
/******************************
 * EQdkp Comment System
 * (c) 2008 by Wallenium 
 * ---------------------------
 * $Id: comments.class.php 1743 2008-03-19 22:51:22Z wallenium $
 ******************************/

if ( !defined('EQDKP_INC') ){
    header('HTTP/1.0 404 Not Found');exit;
}

if (!class_exists("rpComments")) { 
  class rpComments
  { 
    
    // ---------------------------------------------------------
    // Constructor
    // ---------------------------------------------------------
    function rpComments($table, $adminauth='a_config_man'){
      global $user;
      $this->table    = $table;
      $this->isAdmin  = $user->check_auth($adminauth, false);
      $this->Username = $user->data['username'];
      $this->UserID   = $user->data['user_id'];
      $this->langprf  = ($adminauth == 'a_raidplan_') ? 'rp_' : 'comments_';
      $this->version  = '1.0.0';
    }
    
    function CheckUTF8($string){
      if (is_array($string)){
      	$enc = implode('', $string);
      	return @!((ord($enc[0]) != 239) && (ord($enc[1]) != 187) && (ord($enc[2]) != 191));
      }else{
      	return (utf8_encode(utf8_decode($string)) == $string);
      }   
    }
      
    // ---------------------------------------------------------
    // Get the Count of comments for that event/page
    // ---------------------------------------------------------
    function Count($attachid, $page){
      global $db;
      $comcount = 0;
      $sql = "SELECT count(id)
              FROM ".$this->table."
              WHERE attach_id='".(int) $attachid."'
              AND page = '".$page."';";
      $comcount = $db->query_first($sql);
      return ($comcount) ? $comcount : '0';
    }
    
    // ---------------------------------------------------------
    // Save the Comment
    // ---------------------------------------------------------
    function Save(){
      global $db, $bbcode, $eqdkp_root_path;
      if($_POST['page'] == 'raidplan'){
        $urltotrome = $eqdkp_root_path."plugins/raidplan/includes/wpfc/jquery/img/editor/icons";
      }else{
        $urltotrome = $eqdkp_root_path."pluskernel/include/jquery/img/editor/icons";
      }
      $bbcode->SetSmiliePath($urltotrome);
      $htmlinsert = $bbcode->toHTML($_POST['comment']);
      $htmlinsert = ($this->CheckUTF8($htmlinsert) == 1) ? utf8_decode($htmlinsert) : $htmlinsert;
      
      $sql = "INSERT INTO `" . $this->table . "` 
      ( `attach_id`, `date` , `userid`, `text`, `page`) 
      VALUES 
      ('".$_POST['attach_id']."', '".time()."', '".$_POST['userid']."', '".$htmlinsert."', '".$_POST['page']."');";
      $db->query($sql);
  		echo $this->Content($_POST['attach_id'], $_POST['page'], true);
    }
    
    // ---------------------------------------------------------
    // Delete the Comment
    // ---------------------------------------------------------
    function Delete(){
      global $db;
      $sql = 'DELETE FROM ' . $this->table . " WHERE id='" . (int) $_GET['deleteid'] . "'";
      $db->query($sql);
      echo $this->Content($_GET['attach_id'], $_GET['page'], true);
    }
    
    // ---------------------------------------------------------
    // Generate the JS Code
    // ---------------------------------------------------------
    function JScode(){
      global $user;
      $jscode = "<script type='text/javascript'> 
          // wait for the DOM to be loaded 
          $(document).ready(function() { 

             $('#Comments').ajaxForm({  
                target: '#htmlCommentTable', 
                beforeSubmit:  showRequest,
                success: function() { 
                  $('#htmlCommentTable').fadeIn('slow');
                  // clear the input field:
                  document.Comments.comment.value = '';
                  document.Comments.comment.disabled=false;
                  document.getElementById('comment_button').innerHTML='<input type=\"submit\" value=\"".$user->lang[$this->langprf.'send_comment']."\" class=\"input\"/>';
                } 
            }); 
          });
          
          function showRequest(formData, jqForm, options) { 
            document.Comments.comment.disabled=true;
            document.getElementById('comment_button').innerHTML='<img src=\"images/loading.gif\" alt=\"Save\"/> ".$user->lang[$this->langprf.'save_wait']."';
          } 
      </script>";
      return $jscode;
    }
    
    // ---------------------------------------------------------
    // HTML Output Code
    // ---------------------------------------------------------
    function Show($attachid, $page){
      if($page == 'raidplan'){ $this->langprf  = 'rp_'; }
      $html = $this->JScode();
      $html .= '<div id="htmlCommentTable">';
      $html .= $this->Content($attachid, $page);
      $html .= '</div>';
      // the line for the comment to be posted
      if($this->Username != ""){
        $html .= $this->Form($attachid, $page);
      }
      return $html;
    }
    
    // ---------------------------------------------------------
    // Generate the Content
    // ---------------------------------------------------------
    function Content($atachid, $page, $issave = false){
      global $db, $user, $stime, $conf;
      $i = 0;
      $sql = "SELECT com.text, com.date, com.id, com.userid, u.username
              FROM ".$this->table." com, ".USERS_TABLE." u
              WHERE com.attach_id='".$atachid."'
              AND com.page = '".$page."'
              AND com.userid = u.user_id
              ORDER BY com.date;";
      $comm_result = $db->query($sql);
      
      // The delete form
      $html  = '<form id="del_comment" name="del_comment" action="comments.php" method="post">';
      $html .= '</form>';
      
      // the content Box
      $html .= '<div class="contentBox">';
      $html .= '<div class="boxHeader"><h1>'.$user->lang[$this->langprf.'comments_raid'].'</h1></div>';
      $html .= '<div class="boxContent">';
      
      while($row = $db->fetch_record($comm_result)){
        $rowcolor       = ($i%2) ? 'rowcolor2' : 'rowcolor1';
        $formated_time  = (is_object($stime)) ? $stime->DoDate($conf['timeformats']['long'],$row['date']) : date('d.m.y',$row['date']);
        $delete_button  = ($this->isAdmin || $row['userid'] == $this->UserID) ? '<img src="images/global/delete.png" />' : '';
        $msg_text       = ($issave) ? utf8_encode($row['text']) : $row['text'];
        $out[] .= '<div class="'.$rowcolor.' clearfix">
                      <div class="floatLeft" style="overflow: hidden; width: 15%;">'.$row['username'].'</div>
                      <div class="floatLeft" style="overflow: hidden; width: 85%;">
                        <span class="small bold">'.$formated_time.'</span>
                        <span class="small bold floatRight hand" onclick="$(\'#del_comment\').ajaxSubmit({target: \'#htmlCommentTable\', url:\'comments.php?deleteid='.$row['id'].'&page='.$page.'&attach_id='.$atachid.'\', success: function() { $(\'#htmlCommentTable\').fadeIn(\'slow\'); } }); ">'.$delete_button.'</span>
                        <br class="clear"><span class="comment_text">'.$msg_text.'</span><br/>
                      </div>
                    </div><br/>';
        $i++;
      }
      if(count($out) > 0 ){
        foreach($out as $vvalues){
          $html .= $vvalues;
        }
      }else{
        $html .= $user->lang[$this->langprf.'no_comments'];
      }
      $html .= '</div></div>';
      return $html;
    }
    
    // ---------------------------------------------------------
    // Generate the Form
    // ---------------------------------------------------------
    function Form($attachid, $page){
      global $user, $jquery, $jqueryp;
      
      if(is_object($jqueryp)){
        $html  = $jqueryp->wysiwyg('bbcode');
      }else{
        $html  = $jquery->wysiwyg('bbcode');
      }
      $html .= '<div class="contentBox">';
      $html .= '<div class="boxHeader"><h1>'.$user->lang[$this->langprf.'write_comment'].'</h1></div>';
      $html .= '<div class="boxContent"><br/>';
      $html .= '<form id="Comments" name="Comments" action="comments.php" method="post"> 
                <input type="hidden" name="userid" value="'.$this->UserID.'"/>
                <input type="hidden" name="attach_id" value="'.$attachid.'"/>
                <input type="hidden" name="page" value="'.$page.'"/>
                <textarea name="comment" id="bbcode" rows="8" cols="69" class="jTagEditor"></textarea><br/><br/>
                <span id="comment_button"><input type="submit" value="'.$user->lang[$this->langprf.'send_comment'].'" class="input"/></span>
              </form>';
      $html .= '</div></div>';
      return $html;
    }
  }
}
?>
