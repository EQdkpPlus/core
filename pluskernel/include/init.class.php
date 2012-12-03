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


if ( !defined('EQDKP_INC') ){
    header('HTTP/1.0 404 Not Found');exit;
}

class InitPlus EXTENDS EQdkp_Plugin
{
  var $lang_name = '';
  var $lang_path = '';
  
  function Encoding(){
    global $user;
    
    return '<meta http-equiv="Content-Type" content="text/html; charset='.$user->lang['ENCODING'].'" />';
  }
  
	function Header($eqdkprootpath)
	{
		global $jqueryp,$eqdkp,$user,$_HMODE;				
		
		if ($_HMODE){if (validate()){$display=true;}}else {if (!check_auth_admin($user->data['user_id'])  && validate()){$display=true;}}
		if ($display)
		{
			$g = strtolower($eqdkp->config['default_game']);
			$l = strtolower($user->data['user_lang']);
			$d = strtolower($eqdkp->config['default_lang']);
			
			if ($_HMODE) 
	    	{	
					$add_header = "<script type='text/javascript' src='http://alvads.allvatar.com/delivery/spcjs.php?id=2&amp;target=_blank'></script>";    				
	    	}				
			elseif($g == 'wow')
			{
				if (($l == 'german') || ($d == 'german'))
				{
					$add_header = "<!-- [id13] Knaak - EQDKP WoW german --><script type='text/javascript' src='http://ads.allvatar.com/adserver/www/delivery/spcjs.php?id=13'></script>";
				}else {
					$add_header = "<!-- [id14] Knaak - EQDKP WoW englisch --><script type='text/javascript' src='http://ads.allvatar.com/adserver/www/delivery/spcjs.php?id=14'></script>";
				}
			}elseif($g=='lotro')
			{
				if (($l == 'german') || ($d == 'german'))
				{
					$add_header = "<!-- [id15] Knaak - EQDKP Lotro german --><script type='text/javascript' src='http://ads.allvatar.com/adserver/www/delivery/spcjs.php?id=15'></script>";
				}else {
					$add_header = "<!-- [id16] Knaak - EQDKP Lotro englisch --><script type='text/javascript' src='http://ads.allvatar.com/adserver/www/delivery/spcjs.php?id=16'></script>";
				}
			}else
			{
				if (($l == 'german') || ($d == 'german'))
				{
					$add_header = "<!-- [id11] Knaak - EQDKP Allgemein german --><script type='text/javascript' src='http://ads.allvatar.com/adserver/www/delivery/spcjs.php?id=11'></script>";
				}else {
					$add_header = "<!-- [id12] Knaak - EQDKP Allgemein englisch --> <script type='text/javascript' src='http://ads.allvatar.com/adserver/www/delivery/spcjs.php?id=12'></script>";					
				}
			}
			
			
		}		
					
    $header  = $add_header ;   	
	$header .= "

			<!--  Tab Part -->
			<script language='JavaScript' type='text/javascript' src='".$eqdkprootpath."pluskernel/include/tabs/js/tabpane.js'></script>
			<link href='".$eqdkprootpath."pluskernel/include/tabs/css/luna/tab.css' rel='stylesheet' type='text/css' ></link>

			<!--  Armory Menu -->
			<script language='JavaScript' type='text/javascript' src='".$eqdkprootpath."pluskernel/include/javascripts/menu.js'></script>

			<script language='JavaScript' type='text/javascript' src='".$eqdkprootpath."pluskernel/include/javascripts/switchcontent.js'></script>
			<script language='JavaScript' type='text/javascript' src='".$eqdkprootpath."pluskernel/include/javascripts/switchicon.js'></script>
			<script language='JavaScript' type='text/javascript' src='".$eqdkprootpath."pluskernel/include/javascripts/dropdowntabs.js'></script>

			<!--  EQDKP Plus Tooltip -->
			<link href='".$eqdkprootpath."pluskernel/include/css/eqdkpplus.css' rel='stylesheet' type='text/css' ></link>
			
			<style>

			/* Copyright thing */
				.copyis {
					font-size: 10px;
					color: #CECFEF;
				}

				copyis a:link, .copyis a:active, .copyis a:visited {
					font-size: 10px;
					color: #CECFEF;
					text-decoration: none;
					font-weight: bold
				}

				.copyis a:hover {
					font-size: 10px;
					color: #E6E6F5;
					text-decoration: underline;
				}

				.warning {
					font-family:Verdana;
					font-weight:bold;
					font-stretch:narrower;
					font-size: 22px;
					color: red;
				}

				table {
					font-family:Verdana;
					font-size: 11.5px;
				}

				table.updatetable th {
					background-color: #CFCFCF;
				}

				table.updatetable{
					font-family:Verdana;
					font-size: 11.5px;
				}

				.pluginname{
					font-weight:bold;
				}

				.downloadlink a:link,.downloadlink a:active, .downloadlink a:visited{
					font-family:Verdana;
					font-size: 11.5px;
					color: #5588cc;
					text-decoration: none;
				}

				.downloadlink a:hover{
					text-decoration: underline;
				}
				
				.image_resized{
					left:0px;
					top:0px;
					position:relative;
					margin:0;
					padding:1px;
				}
				
				.markImageResized{
					width:32px;
					height:32px;
					left:10px;
					top:10px;
					position:absolute;
					margin:0;
					padding:0;
					display: none;
				}

			/* PopUp Style */
			#popitmenu{
			position: absolute;
			background-color: white;
			border:1px solid black;
			font: normal 12px Verdana;
			line-height: 18px;
			z-index: 100;
			visibility: hidden;
			}
			
			#popitmenu a{
			text-decoration: none;
			padding-left: 6px;
			color: black;
			display: block;
			}

			#popitmenu a:hover{ /*hover background color*/
			background-color: #CCFF9D;
			}

			.resists { width: 27px; text-align: center;color: white; font-weight: bold; font-size: 12px !important;}

			.uc_profmenu {
			text-align: right;
			}
			</style>
			
			<!--[if IE 6]>
			<script type=\"text/javascript\" src=\"".$eqdkprootpath."pluskernel/include/javascripts/ie6pngfix.js\"></script>
			<![endif]-->
					
			  <!--[if lt IE 8]>
  <div style='border: 1px solid #F7941D; background: #FEEFDA; text-align: center; clear: both; height: 75px; position: relative;'>
    <div style='position: absolute; right: 3px; top: 3px; font-family: courier new; font-weight: bold;'><a href='#' onclick='javascript:this.parentNode.parentNode.style.display=\"none\"; return false;'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-cornerx.jpg' style='border: none;' alt='Close this notice'/></a></div>
    <div style='width: 640px; margin: 0 auto; text-align: left; padding: 0; overflow: hidden; color: black;'>
      <div style='width: 75px; float: left;'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-warning.jpg' alt='Warning!'/></div>
      <div style='width: 275px; float: left; font-family: Arial, sans-serif;'>
        <div style='font-size: 14px; font-weight: bold; margin-top: 12px;'>You are using an outdated browser</div>
        <div style='font-size: 12px; margin-top: 6px; line-height: 12px;'>For a better experience using this site, please upgrade to a modern web browser.</div>
      </div>
      <div style='width: 75px; float: left;'><a href='http://www.firefox.com' target='_blank'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-firefox.jpg' style='border: none;' alt='Get Firefox 3.5'/></a></div>
      <div style='width: 75px; float: left;'><a href='http://www.browserforthebetter.com/download.html' target='_blank'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-ie8.jpg' style='border: none;' alt='Get Internet Explorer 8'/></a></div>
      <div style='width: 73px; float: left;'><a href='http://www.apple.com/safari/download/' target='_blank'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-safari.jpg' style='border: none;' alt='Get Safari 4'/></a></div>
      <div style='float: left;'><a href='http://www.google.com/chrome' target='_blank'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-chrome.jpg' style='border: none;' alt='Get Google Chrome'/></a></div>
    </div>
  </div>
  <![endif]-->";

			return $header;
	}
  
  function Footer(){
    return '</body></html>';
  }
  
	function generateWindows($eqdkprootpath){
	global $user, $eqdkp, $jqueryp;
		// Set up language array
		if ( (isset($user->data['user_id'])) && ($user->data['user_id'] != ANONYMOUS) && (!empty($user->data['user_lang'])) )
    {
    	$this->lang_name = $user->data['user_lang'];
		}else{
			$this->lang_name = $eqdkp->config['default_lang'];
		}
		$this->lang_path = $eqdkprootpath.'pluskernel/language/'.$this->lang_name.'/';
		include($this->lang_path . 'lang_main.php');

		$plus_urls = array(
			'about'			=> $eqdkprootpath.'pluskernel/about.php'
		);

    // Check if the window Class is already loaded:
    if(!get_class($jqueryp)){
      $jqueryp  = new jQueryPLUS($eqdkprootpath.'pluskernel/include/');
    }

    // Generate the JavaScript Windows
    $js .= "<script language='JavaScript' type='text/javascript'>";

    // About Window
    $js .= 'function AboutPLUSDialog() {';
    $js .= $jqueryp->Dialog_URL('AboutPLUS', $plang['pk_plus_about'], $plus_urls['about'], '680', '540');
    $js .= '}';

    $js .= "</script>";

		return $js;
	}
}// end of class
?>
