<?php
 /***************************************
 * EQdkp Plugin Dev Classes Framework Kit
 * jQuery Class
 * (c) 2008 by WalleniuM
 * www.wallenium.de
 * ------------------
 * jQuery.class.php
 * $LastChangedDate$
 *
 * This Class is part of the Plugin 
 * Developer Framework Kit. You can 
 * use this Class in your Plugins,
 * but not remove this Copyright
 * 
 ****************************************/
if ( !defined('EQDKP_INC') ){
    header('HTTP/1.0 404 Not Found');exit;
}

class jQueryPLUS {
  
  var $version = '1.0.0';
  
  /**
  * Initialize the Class
  * 
  * @param $path    Set the path to the wpfc folder
  * @return CHAR
  */
  function jQueryPLUS($path, $lang='en'){
    $this->path = $path;
    if($lang != 'en'){
      $js_cal_language = $path."jquery/js/lang/ui.datepicker-".$lang.".js";
      if(file_exists($js_cal_language)){
        $tmp_jscript = "<script language='javascript' src='".$js_cal_language."'></script>";
      }else{
        $tmp_jscript = '';
      }
    }
    $this->headerr = "<link rel='stylesheet' href='".$path."jquery/css/jbox.css' type='text/css'>
                      <link type='text/css' href='".$path."jquery/css/confirm.css' rel='stylesheet'/>
                      <link type='text/css' href='".$path."jquery/css/ui.datepicker.css' rel='stylesheet'/>
                      <script language='javascript' src='".$path."jquery/js/jquery.min.js'></script>
                      <script type='text/javascript' src='".$path."jquery/js/jquery.jbox.js'></script>
                      <script type='text/javascript' src='".$path."jquery/js/jquery.accordion.min.js'></script>
                      <script type='text/javascript' src='".$path."jquery/js/jquery.humanmsg.js'></script>
                      <script type='text/javascript' src='".$path."jquery/js/jquery.impromptu.js'></script>
                      <script type='text/javascript' src='".$path."jquery/js/ui.datepicker.js'></script>
                      ".$tmp_jscript."
                      <script>
                        var jImages={
                          imgs:['".$path."jquery/img/min.gif', '".$path."jquery/img/close.gif', '".$path."jquery/img/restore.gif','".$path."jquery/img/resize.gif']
                        }
                      </script>";
  }
  
  /**
  * Return the Header for file include
  * 
  * @return CHAR
  */
  function Header(){
    return $this->headerr;
  }
  
  /**
  * Alert Window (direct)
  * 
  * @param $name    Name/ID of the window (must be unique)
  * @param $msg     The Message to show in Alert
  * @param $width   The width of the alert window
  * @param $height  The height of the alert window
  * @return CHAR
  */
  function Dialog_Alert($name, $msg, $width="600", $height="300"){
    $jscode = "function ".$name."Alert() {
                jBox.alert('".$msg."', ".$width.", ".$height.");
              }";
    return $jscode;
  }
  
  /**
  * Alert Window (indirect)
  * 
  * @param $width   the width of the alert window
  * @param $height  the height of the alert window
  * @return CHAR
  */
  function Dialog_Alert2($width=300, $height=100){
    $jscode = "function DisplayErrorMessage(errmsg){
                jBox.alert(errmsg, ".$width.", ".$height.");
              }";
    return $jscode;
  }
  
  /**
  * Close Dialog by Name/ID
  * 
  * @param $id        The Name/ID of the window to be closed
  * @param $tags      Add the <script> tags to the output?
  * @param $parent    Use the parent tag, use this if you want to close an window from main page
  * @param $function  Output as a JavaScript function
  * @return CHAR
  */
  function Dialog_close($id, $tags=false, $parent=true, $function=false){
    $jscode  = ($tags) ? "<script language=\"JavaScript\" type=\"text/javascript\">" : "";
    $jscode .= ($function) ? 'function closeWindow(){' : '';
    $parenttag = ($parent) ? "parent." : '';
    if(is_array($id)){
      foreach($id as $realid){
        $jscode .= $parenttag."jBox.close2('".$realid."');";
      }
      $jscode .= ($function) ? '}' : '';
      $jscode .= ($tags) ? "</script>" : "";
      return $jscode;
    }else{
      return false;
    }
  }
  
  /**
  * Window with iFrame & URL
  * 
  * @param $name      Name/ID of the window (must be unique)
  * @param $title     The Title of the window, shown in Header
  * @param $url       The URL to show in the iFrame
  * @param $height    The width of the alert window
  * @param $height    The height of the alert window
  * @param $onclose   URL of page to redirect onClose, if empty, no redirect
  * @param $minimize  Window minimizable? (true/false)
  * @param $modal     Window modal, rest of page greyed out? (true/false)
  * @param $scrolling Window scrollable? (true/false)
  * @param $resize    Window resizable? (true/false)
  * @param $draggable Window dragable? (true/false)
  * @return CHAR
  */
  function Dialog_URL($name, $title, $url, $width="600", $height="300", $onclose='', $minimize="true", $modal="false", $scrolling="true", $resize="true", $draggable="true"){
    $jscode  = "var box".$name." = jBox.open('".$name."','iframe','".$url."','".$title."','width=".$width.",height=".$height.",center=true,minimizable=".$minimize.",resize=".$resize.",draggable=".$draggable.",model=".$modal.",scrolling=".$scrolling."');";
    $jscode .= ($onclose) ? "box".$name.".onClosed = function(){ window.location.href = '".$onclose."';}" : '';
    return $jscode;
  }
  
  /**
  * Confirm Dialog
  * 
  * @param $name    Name/ID of the window (must be unique)
  * @param $text    The Message to show in Confirm dialog
  * @param $jscode  The javaScript Code to perform on confirmation
  * @return CHAR
  */
  function Dialog_Confirm($name, $text, $jscode){
    global $user;
    $jscode = "function submit_".$name."(v,m){
                if(v){
                  ".$jscode."
                }
                return true;
              }
              
              function ".$name."(){
                $.prompt('".$text."', {
                          buttons:{ ".$user->lang['wpfc_bttn_ok'].": true, ".$user->lang['wpfc_bttn_cancel'].": false },
                          submit: submit_".$name.",
                          prefix:'colsJqi',
                          show:'slideDown'}
                        );
              }
              ";
        return $jscode;
  }

  /**
  * Horizontal Accordion
  * 
  * @param $name    Name/ID of the accordion (must be unique)
  * @param $list    Content array in the format: title => content
  * @return CHAR
  */  
  function Accordion($name, $list){
    $jscode   = "<script>
                  jQuery('#".$name."').Accordion({
                      header: '.title',
                      animated: false 
                    });
                </script>";
    $acccode   = '<div id="'.$name.'">';
    foreach($list as $title=>$content){
      $acccode  .= '<div>
                      <div class="title">'.$title.'</div>
                      <div class="content">'.$content.'</div>
                    </div>';
    }
    $acccode  .= '</div>';
    return $acccode.$jscode;
  }
  
   /**
  * Humanized Messages
  * 
  * @param $name    Name/ID of the accordion (must be unique)
  * @param $list    Content array in the format: title => content
  * @return CHAR
  */  
  function HumanMsg($name, $content, $direct=true, $script=false){
    $jscode   = ($script) ? "<script>" : '';
    $jscode  .= ($direct) ? '' : "function ".$name."(){";
    $jscode  .= "humanMsg.displayMsg('".$content."');";
    $jscode  .= ($direct) ? '' : "}";
    $jscode  .= ($script) ? "</script>" : '';
    return $jscode;
  }
  
  function Calendar($name, $value, $format='dd.mm.yy'){
    $html = '<input type="text" id="'.$name.'" name="'.$name.'" value="'.$value.'">';
    $jscode = "<script>
                jQuery(function($){
				          $('#".$name."').datepicker({showOn: 'button', buttonImage: '".$this->path."jquery/img/calendar.gif', buttonImageOnly: true, dateFormat: '".$format."'}); 
                });
                </script>";
    return $html.$jscode;
  }
}
