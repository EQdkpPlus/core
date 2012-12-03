<?php
/******************************
 * EQDKP PLUGIN: PLUSkernel
 * (c) 2006 by WalleniuM [Simon Wallmann]
 * http://www.kompsoft.de
 * ------------------
 * init.class.php
 * Changed: October 18, 2006
 *
 ******************************/

class InitPlus EXTENDS EQdkp_Plugin
{
	var $lang_name = '';
  var $lang_path = '';

	function Header($eqdkprootpath){
		$header = "
			<!--  Reflection Part -->
			<script language='JavaScript' type='text/javascript' src='".$eqdkprootpath."pluskernel/include/javascripts/reflection.js'></script>

			<!--  Bubble Tooltip Library -->
			<script language='JavaScript' type='text/javascript' src='".$eqdkprootpath."pluskernel/include/javascripts/BubbleTooltips.js'></script>

			<!--  Tab Part -->
			<script language='JavaScript' type='text/javascript' src='".$eqdkprootpath."pluskernel/include/tabs/js/tabpane.js'></script>
			<link href='".$eqdkprootpath."pluskernel/include/tabs/css/luna/tab.css' rel='stylesheet' type='text/css' ></link>

			<!--  Prototype Window Class Part -->
			<script language='JavaScript' type='text/javascript' src='".$eqdkprootpath."pluskernel/include/javascripts/prototype.js'></script>
			<script language='JavaScript' type='text/javascript' src='".$eqdkprootpath."pluskernel/include/javascripts/effects.js'></script>
			<script language='JavaScript' type='text/javascript' src='".$eqdkprootpath."pluskernel/include/javascripts/window.js'></script>
			<script language='JavaScript' type='text/javascript' src='".$eqdkprootpath."pluskernel/include/javascripts/window_effects.js'></script>

			<!--  Armory Menu -->
			<script language='JavaScript' type='text/javascript' src='".$eqdkprootpath."pluskernel/include/javascripts/menu.js'></script>

			<!--  Themes (hier nur die verwendeten 2) -->
			<link href='".$eqdkprootpath."pluskernel/include/themes/default.css' rel='stylesheet' type='text/css' ></link>
			<link href='".$eqdkprootpath."pluskernel/include/themes/alphacube.css' rel='stylesheet' type='text/css' ></link>

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

	function SmallHeader($eqdkprootpath){
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

		$plus_urls = array(
			'about'			=> $eqdkprootpath.'pluskernel/about.php',
			'updates'		=> $eqdkprootpath.'pluskernel/updates.php',
			'settings'	=> $eqdkprootpath.'pluskernel/settings.php',
		);

		$js = "<script language='JavaScript' type='text/javascript'><!--
						var updatewin;
						var contentWin;
						var settingswin;

						function closeWindow()
							{
								var myId = window.name.replace('_content', '');   // resolve the id I was given
								window.parent.Windows.close(myId);  // call parent's windows collection to close me
							}

						function closeAllWindows()
							{
								var myId = window.name.replace('_content', '');
								window.parent.Windows.closeAll();
							}

						function AboutPLUSDialog() {
							if (contentWin != null) {
								Dialog.alert('".$plang['pk_close_jswin1']." Credits ".$plang['pk_close_jswin2']."', {windowParameters:{ className: 'alphacube', width:200, height:130}});
							}else {
								contentWin = new Window('AboutPLUS', {className: 'alphacube',  title: '".$plang['pk_plus_about']."',
                                              top:70, left:100, width:680, height:540,
                                              resizable: true, url: '".$plus_urls['about']."', showEffectOptions: {duration:1}})
								contentWin.setDestroyOnClose();
								contentWin.showCenter();
								contentWin.toFront();
						// Set up a windows observer, set open window check to null
  							myObserver = {
    							onDestroy: function(eventName, win) {
      							if (win == contentWin) {
        							contentWin = null;
        							Windows.removeObserver(this);
      							}
    							}
  							}
  							Windows.addObserver(myObserver);
							}
  					}

						function Updates() {
							if (updatewin != null) {
								Dialog.alert('".$plang['pk_close_jswin1']." Updates ".$plang['pk_close_jswin2']."', {windowParameters:{ className: 'alphacube', width:200, height:130}});
							}else {
								updatewin = new Window('UpdatePLUS', {className: 'alphacube',
                                              top:70, left:100, width:500, height:300,
                                              resizable: true, hideEffect: Effect.SwitchOff, url: '".$plus_urls['updates']."', showEffectOptions: {duration:1}})
								updatewin.setDestroyOnClose();
								updatewin.showCenter();
								updatewin.toFront();
							// Set up a windows observer, set open window check to null
  							myObserver = {
    							onDestroy: function(eventName, win) {
      							if (win == updatewin) {
        							updatewin = null;
        							Windows.removeObserver(this);
      							}
    							}
  							}
  							Windows.addObserver(myObserver);
							}
						}

						function Settings() {
							if (settingswin != null) {
								Dialog.alert('".$plang['pk_close_jswin1']." Updates ".$plang['pk_close_jswin2']."', {windowParameters:{ className: 'alphacube', width:200, height:130}});
							}else {
								settingswin = new Window('SettingsPLUS', {className: 'alphacube', title: '".$plang['pk_config_header']."',
                                              top:70, left:100, width:525, height:380,
                                              resizable: true, hideEffect: Effect.SwitchOff, url: '".$plus_urls['settings']."', showEffectOptions: {duration:1}})
								settingswin.setDestroyOnClose();
								settingswin.showCenter();
								// Set up a windows observer, set open window check to null
  							myObserver = {
    							onDestroy: function(eventName, win) {
      							if (win == settingswin) {
        							settingswin = null;
        							Windows.removeObserver(this);
      							}
    							}
  							}
  							Windows.addObserver(myObserver);
								}
							}

							function startLoading() {
    						Dialog.info('".$plang['loading']."',
               	{windowParameters: {className:'alphacube', width:250, height:100}, showProgress: true});
  						}

							function stopLoading() {
    						Dialog.closeInfo();
  						}

							Ajax.Responders.register({
    							onCreate : startLoading
  						});
					//-->
				</script>";
				return $js;
	}
}// end of class
?>