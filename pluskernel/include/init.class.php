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
		global $jquery,$eqdkp,$user;				
		
		if (!check_auth_admin($user->data['user_id'])  && validate())
		{
			$g = strtolower($eqdkp->config['default_game']);
			$l = strtolower($user->data['user_lang']);
			$d = strtolower($eqdkp->config['default_lang']);
			
			if ($g == 'wow')
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

			";

			return $header;
	}
  
  function Footer(){
    return '</body></html>';
  }
  
	function generateWindows($eqdkprootpath){
	global $user, $eqdkp, $jquery;
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
			'about'			=> $eqdkprootpath.'pluskernel/about.php',
			'updates'		=> $eqdkprootpath.'pluskernel/updates.php',
		);

    // Check if the window Class is already loaded:
    if(!get_class($jquery)){
      $jquery  = new jQueryPLUS($eqdkprootpath.'pluskernel/include/');
    }

    // Generate the JavaScript Windows
    $js .= "<script language='JavaScript' type='text/javascript'>";

    // About Window
    $js .= 'function AboutPLUSDialog() {';
    $js .= $jquery->Dialog_URL('AboutPLUS', $plang['pk_plus_about'], $plus_urls['about'], '680', '540');
    $js .= '}';

    // Update Window
    $js .= 'function Updates() {';
    $js .= $jquery->Dialog_URL('UpdatePLUS', $plang['pk_plus_about'], $plus_urls['updates'], '500', '300');
    $js .= '}';

    $js .= "</script>";

		return $js;
	}
}// end of class
?>
