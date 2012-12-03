<?php
/******************************
 * EQDKP PLUGIN: PLUSkernel
 * (c) 2007 by EQDKP Plus Dev Team
 * originally written by S.Wallmann
 * http://www.eqdkp-plus.com
 * ------------------
 * init.class.php
 * Start: 2006
 * $Id$
 ******************************/

if ( !defined('EQDKP_INC') ){
    header('HTTP/1.0 404 Not Found');exit;
}

class InitPlus EXTENDS EQdkp_Plugin
{
  var $lang_name = '';
  var $lang_path = '';

	function Header($eqdkprootpath)
	{
	 global $jqueryp;
		$header = "
			<!--  Reflection Part -->
			<script language='JavaScript' type='text/javascript' src='".$eqdkprootpath."pluskernel/include/javascripts/reflection.js'></script>

			<!--  Bubble Tooltip Library -->
			<script language='JavaScript' type='text/javascript' src='".$eqdkprootpath."pluskernel/include/javascripts/BubbleTooltips.js'></script>

			<!--  Tab Part -->
			<script language='JavaScript' type='text/javascript' src='".$eqdkprootpath."pluskernel/include/tabs/js/tabpane.js'></script>
			<link href='".$eqdkprootpath."pluskernel/include/tabs/css/luna/tab.css' rel='stylesheet' type='text/css' ></link>

			<!--  Armory Menu -->
			<script language='JavaScript' type='text/javascript' src='".$eqdkprootpath."pluskernel/include/javascripts/menu.js'></script>

			<script language='JavaScript' type='text/javascript' src='".$eqdkprootpath."pluskernel/include/javascripts/switchcontent.js'></script>
			<script language='JavaScript' type='text/javascript' src='".$eqdkprootpath."pluskernel/include/javascripts/switchicon.js'></script>
			<script language='JavaScript' type='text/javascript' src='".$eqdkprootpath."pluskernel/include/javascripts/dropdowntabs.js'></script>

			<!--  Themes (hier nur die verwendeten 2) -->
			<link href='".$eqdkprootpath."pluskernel/include/themes/default.css' rel='stylesheet' type='text/css' ></link>
			<link href='".$eqdkprootpath."pluskernel/include/themes/alphacube.css' rel='stylesheet' type='text/css' ></link>

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
			<script language='JavaScript' type='text/javascript'>
					window.onload = function() {
    				enableTooltips('".$eqdkprootpath."pluskernel/include/bubble/');
					};
			</script>
			";

			return $header;
	}

	function SmallHeader($eqdkprootpath)
	{
		global $user, $eqdkp;
		// Set up language array
		if ( (isset($user->data['user_id'])) && ($user->data['user_id'] != ANONYMOUS) && (!empty($user->data['user_lang'])) )
    	{
    		$this->lang_name = $user->data['user_lang'];
		}else{
			$this->lang_name = $eqdkp->config['default_lang'];
		}
		$this->lang_path = $eqdkprootpath.'pluskernel/language/'.$this->lang_name.'/';
		include($this->lang_path . 'lang_main.php');
		$header = "
			<!--  Reflection Part -->
			<script language='JavaScript' type='text/javascript' src='".$eqdkprootpath."pluskernel/include/javascripts/reflection.js'></script>

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
			</style>";
			return $header;
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
			'about'			=> $eqdkprootpath.'pluskernel/about.php',
			'updates'		=> $eqdkprootpath.'pluskernel/updates.php',
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

    // Update Window
    $js .= 'function Updates() {';
    $js .= $jqueryp->Dialog_URL('UpdatePLUS', $plang['pk_plus_about'], $plus_urls['updates'], '500', '300');
    $js .= '}';

    $js .= "</script>";

		return $js;
	}
}// end of class
?>
