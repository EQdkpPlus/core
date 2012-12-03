<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
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

if ( !defined('EQDKP_INC') ){
    header('HTTP/1.0 404 Not Found');exit;
}

if (!class_exists("BBcode")) {
  class BBcode
  {
  
    function SetSmiliePath($path){
      $this->smiliepath = $path;
    }
    
    function escape($s) {
  		global $text;
  		$text = strip_tags($text);
  		return '<pre><code>'.htmlspecialchars($s[1]).'</code></pre>';
    }	
    
    // clean some tags to remain strict
    // not very elegant, but it works. No time to do better ;)
    function removeBr($s) {
      return str_replace("<br />", "", $s[0]);
    }	
    
    function MyEmoticons($text){
       // Smileys to find...
      $in = array( 	 
                    ':)', 	
              			':D',
              			':o',
              			':p',
              			':(',
              			';)'
                  );
      
      $out = array(	 
               '<img alt=":)" src="'.$this->smiliepath.'/happy.png" />',
    					 '<img alt=":D" src="'.$this->smiliepath.'/smile.png" />',
    					 '<img alt=":o" src="'.$this->smiliepath.'/surprised.png" />',
    					 '<img alt=":p" src="'.$this->smiliepath.'/tongue.png" />',
    					 '<img alt=":(" src="'.$this->smiliepath.'/unhappy.png" />',
    					 '<img alt=";)" src="'.$this->smiliepath.'/wink.png" />'
      );
      
      $text = preg_replace('/\<img(.*?)alt=\"(\W.*?)\"(.*?)\>/si' , '$2' , $text);
      $text = str_replace($in, $out, $text);
      return $text;
    }
    
    function toHTML($text){
      $text = trim($text);
      $text = preg_replace_callback('/\[code\](.*?)\[\/code\]/ms', array($this,"escape"), $text);
      
      // BBCode to find...
    	$in = array( 	 '/\[b\](.*?)\[\/b\]/ms',	
    					 '/\[i\](.*?)\[\/i\]/ms',
    					 '/\[u\](.*?)\[\/u\]/ms',
    					 '/\[img\](.*?)\[\/img\]/ms',
    					 '/\[email\](.*?)\[\/email\]/ms',
    					 '/\[url\="?(.*?)"?\](.*?)\[\/url\]/ms',
    					 '/\[size\="?(.*?)"?\](.*?)\[\/size\]/ms',
    					 '/\[color\="?(.*?)"?\](.*?)\[\/color\]/ms',
    					 '/\[quote](.*?)\[\/quote\]/ms',
    					 '/\[list\=(.*?)\](.*?)\[\/list\]/ms',
    					 '/\[list\](.*?)\[\/list\]/ms',
    					 '/\[\*\]\s?(.*?)\n/ms'
    	);
	
      // And replace them by...
    	$out = array(	 '<strong>\1</strong>',
    					 '<em>\1</em>',
    					 '<u>\1</u>',
    					 '<img src="\1" alt="\1" />',
    					 '<a href="mailto:\1">\1</a>',
    					 '<a href="\1">\2</a>',
    					 '<span style="font-size:\1%">\2</span>',
    					 '<span style="color:\1">\2</span>',
    					 '<blockquote>\1</blockquote>',
    					 '<ol start="\1">\2</ol>',
    					 '<ul>\1</ul>',
    					 '<li>\1</li>'
    	);
    	$text = preg_replace($in, $out, $text);
    	
    	// paragraphs
    	$text = str_replace("\r", "", $text);
    	$text = "<p>".ereg_replace("(\n){2,}", "</p><p>", $text)."</p>";
    	$text = nl2br($text);
	

    	$text = preg_replace_callback('/<pre>(.*?)<\/pre>/ms', array($this,"removeBr"), $text);
    	$text = preg_replace('/<p><pre>(.*?)<\/pre><\/p>/ms', "<pre>\\1</pre>", $text);
    	
    	$text = preg_replace_callback('/<ul>(.*?)<\/ul>/ms', array($this,"removeBr"), $text);
    	$text = preg_replace('/<p><ul>(.*?)<\/ul><\/p>/ms', "<ul>\\1</ul>", $text);
	
	return $text;
    }
  }
}
?>
