<?php
/******************************
 * EQdkp RaidPlan
 * (c) 2005 - 2007
 * past dev by Urox, A.Stranger
 * continued by Wallenium 
 * ---------------------------
 * $Id: bbcode.class.php 1502 2008-02-18 11:08:26Z wallenium $
 ******************************/

if ( !defined('EQDKP_INC') ){
    header('HTTP/1.0 404 Not Found');exit;
}

if (!class_exists("BBcode")) {
  class BBcode
  {
  
    function SetSmiliePath($path){
      $this->smiliepath = $path;
    }
  
    function toHTML($text){
      
      // secure the input against XSS
      $text = strip_tags($text);
      
      $text = nl2br($text); 
      $text = str_replace("[b]",    "<b>",    $text); 
      $text = str_replace("[/b]",   "</b>",   $text); 
      $text = str_replace("[i]",    "<i>",    $text); 
      $text = str_replace("[/i]",   "</i>",   $text); 
      $text = str_replace("[u]",    "<u>",    $text); 
      $text = str_replace("[/u]",   "</u>",   $text); 
      $text = eregi_replace("\\[url=([^\\[]*)\\]([^\\[]*)\\[/url\\]","<a href=\"\\1\" target=\"_blank\">\\2</a>",$text); 
      $text = eregi_replace("\\[url\\]([^\\[]*)\\[/url\\]","<a href=\"\\1\" target=\"_blank\">\\1</a>",$text); 
      $text = eregi_replace("\\[email=([^\\[]*)\\]([^\\[]*)\\[/email\\]","<a href=\"mailto:\\1\">\\2</a>",$text); 
      $text = eregi_replace("\\[img]([^\\[]*)\\[/img]","<img src=\"\\1\" border=0>",$text); 
      
      $text = eregi_replace("\\[code]([^\\[]*)\\[/code]","<code>\\1\</code>",$text); 
      $text = eregi_replace("\\[quote]([^\\[]*)\\[/quote]","<table width=100% bgcolor=lightgray><tr><td bgcolor=white>\\1\</td></tr></table>",$text); 
      $text = eregi_replace("\\[color=([^\\[]*)\\]([^\\[]*)\\[/color\\]","<span style=\"color:\\1\">\\2</span>",$text);  
      $text = eregi_replace("\\[size=([^\\[]*)\\]([^\\[]*)\\[/size\\]","<span style=\"font-size:\\1\">\\2</span>",$text);  
      
      $bbcode = array(
                    "[list]", "[*]", "[/list]", 
                    
                    // smilies...
                    ":)",             "",
                    ":(",             "",
  
                    ":p",             "",
                    ":o",             "",
                    
                    '"]');
      $htmlcode = array(
                    "<ul>", "<li>", "</ul>", 
                    
                    // smilies...
                    "<img src=\"".$this->smiliepath."/emoticon-smile.png\">", "",
                    "<img src=\"".$this->smiliepath."/emoticon-unhappy.png\">", "",
  
                    "<img src=\"".$this->smiliepath."/emoticon-tongue.png\">", "",
                    "<img src=\"".$this->smiliepath."/emoticon-surprised.png\">", "",
                    
                    '">');
      $newtext = str_replace($bbcode, $htmlcode, $text);
      $newtext = nl2br($newtext);//second pass
      return $newtext;
    }
  }
}
?>
