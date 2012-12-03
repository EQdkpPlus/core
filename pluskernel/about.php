<?php
/******************************
 * EQDKP PLUGIN: PLUSkernel
 * (c) 2006 - 2007 by WalleniuM
 * http://www.kompsoft.de   
 * ------------------
 * about.php
 * Changed: May 7, 2007
 * 
 ******************************/

define('EQDKP_INC', true);
$eqdkp_root_path = '../';
include_once($eqdkp_root_path . 'common.php');
include_once('include/init.class.php');
include_once('include/komptab.class.php');
include_once('include/php.class.php');

// the language include part
global $user, $eqdkp;
		// Set up language array
		if ( (isset($user->data['user_id'])) && ($user->data['user_id'] != ANONYMOUS) && (!empty($user->data['user_lang'])) )
    {
    	$lang_name = $user->data['user_lang'];
		}else{
			$lang_name = $eqdkp->config['default_lang'];
		}
		$lang_path = $eqdkp_root_path.'pluskernel/language/'.$lang_name.'/';
		include($lang_path . 'lang_main.php');
// end of language part

$init = new InitPlus();
$tabs = new kompTabs();
$specialphp = new phpAdditions();
echo $init->Header($eqdkp_root_path);

 // Build the data in arrays..
	$author = array(
		'personal_url'		=> 'http://www.corgan-net.de',
		'personal_name'		=> 'corgan-net.de',
		'web_url'					=> 'http://www.eqdkp-plus.com',
		'web_name'				=> 'EQKDP Plus',
		'name'						=> 'EQDKP PLUS dev team',
		'city'						=> 'Germany',
	);

	$images = array(
		'mainimage' 		=> 'pk_logo.png',
		'mainimage_alt'	=> 'EQDKP PLUS Logo',
	);

// here must be the html shit 
// Don't change anything below that.. all config is above..)
echo "
<html>
<head>
<style type='text/css'>
img { 
vertical-align: middle; 
border: 0px; 
}

.dynamic-tab-pane-control h2 {
	text-align:	center;
	width:		auto;
}

.dynamic-tab-pane-control h2 a {
	display:	inline;
	width:		auto;
}

.dynamic-tab-pane-control a:hover {
	background: transparent;
}

.dynamic-tab-pane-control .tab-page {
	height:		auto;
}

.dynamic-tab-pane-control .tab-page .dynamic-tab-pane-control .tab-page {
	height:		auto;
}

BODY { 
font-family: Verdana, Tahoma, Arial;
font-size: 11px;
color: #000000;
}

tr, td { 
font-family: Verdana, Tahoma, Arial;
font-size: 11px;
color: #000000; 
}

h3, td.h3 {
  font-size: 12px;
	font-weight: bold; 
	color:#F37D1F;
	padding-top:10px;
}
ul.intro_message, td.intro_message, tr.intro_message, table.intro_message {
  text-align:left;
  font-size: 11px;
  padding:0, 10px;
  margin:0;
  color: black;
}
ul.intro_message a, td.intro_message a, tr.intro_message a, table.intro_message a {
  text-decoration: none;
  color: #5588cc;
}
ul.intro_message a:hover, td.intro_message a:hover, tr.intro_message a:hover, table.intro_message a:hover {
  text-decoration: underline;
  color: #5588cc;
}
</style>
</head>

<body>
<ul class='intro_message'>
  <table border='0' cellpadding='0' cellspacing='0' class='borderless' width='590'>
    <tr>
      <td style='padding-right: 10px;' valign='top' align='center' width='275'><img class='reflect' style='float: left;' src='images/".$images['mainimage']."' alt='".$images['mainimage_alt']."' border='0'><br>
      ".$plang['pk_version']." ".EQDKPPLUS_VERSION."</td>
      <td style='padding-right: 10px;' valign='top' width='335'>
		<table border='0' width='100%' id='table1' cellspacing='0' cellpadding='0' class='borderless'>
			<tr>
				<td valign='top'>
				<table border='0' width='100%' id='table2' cellspacing='1' class='borderless'>
					<tr>
						<td><b>".$plang['pk_created by'].":</b></td>
					</tr>
					<tr>
						<td>".$author['name']."</td>
					</tr>
					<tr>
						<td>".$author['city']."</td>
					</tr>
					<tr>
						<td>".$plang['web_url'].": <a class='linkcrap' href='".$author['web_url']."' target='_blank'>".$author['web_name']."</a></td>
					</tr>
					<tr>
						<td>".$plang['personal_url'].": <a class='linkcrap' href='".$author['personal_url']."' target='_blank'>".$author['personal_name']."</a> 
						</td>						
					</tr>
					<tr>
						<td><a href=http://sourceforge.net/donate/index.php?group_id=167016 target=_blank><img src=images/project-support.jpg></a></td>						
					</tr>					
					
					
				</table>
				</td>
			</tr>
		</table>
		</td>
    </tr>
    <tr>
      <td style='padding-right: 10px;' valign='top'>&nbsp;</td>
      <td style='padding-right: 10px;' valign='top'>
		&nbsp;</td>
    </tr>
    <tr>
      <td style='padding-right: 10px;' valign='top' colspan='2'>";
   echo $tabs->startPane('config');
   echo $tabs->startTab($plang['pk_credits'], 'ab_credits');
   echo '   <table width="100%" border="0"> 
     <tr>
          <td bgcolor="#CCCCCC"><strong>'.$plang['pk_prodcutname'].'</strong></td>
          <td bgcolor="#CCCCCC"><strong>'.$plang['pk_version'].'</strong></td>
          <td bgcolor="#CCCCCC"><strong>'.$plang['pk_developer'].'</strong></td>
          <td bgcolor="#CCCCCC"><strong>'.$plang['pk_weblink'].'</strong></td>
        </tr>
        <tr>
          <td>Original EQDKP</td>
          <td>1.3.1</td>
          <td>tsigo</td>
          <td><a href="http://eqdkp.com/" target="_blank">www.eqdkp.com</a></td>

        </tr>
        <tr>
          <td>EQDKP Plus</td>
          <td>'.EQDKPPLUS_VERSION.'</td>
          <td>Corgan</td>
          <td><a href="http://eqdkp.corgan-net.de" target="_blank">eqdkp.corgan-net.de</a></td>
        </tr>
        <tr>
          <td>EQDKP Plus Kernel</td>
          <td>'.EQDKPPLUS_VERSION.'</td>
          <td>wallenium</td>
          <td></td>
        </tr>
				</table>';
   echo ' <br/>';
   echo ' <br/>';        
   echo '© <a href=http://www.blizzard.com/legalfaq.shtml>World of Warcraft and Blizzard Entertainment </a> are trademarks or registered trademarks of Blizzard Entertainment, Inc. in the U.S. and/or other countries. wowcr.net is in no way associated with Blizzard Entertainment.
 <br/>';
   echo ' <br/>';
   echo ' <br/>';
   echo ' <br/>';
   echo ' <br/>';
   echo ' <br/>';
   echo ' <br/>';
   echo $tabs->endTab();
      
   echo $tabs->startTab($plang['pk_plugins'], 'ab_plugins');   
   echo '
    <table width="100%" border="0">
        <tr>
          <td bgcolor="#CCCCCC"><strong>'.$plang['pk_plugin'].'</strong></td>
          <td bgcolor="#CCCCCC"><strong>'.$plang['pk_version'].'</strong></td>
          <td bgcolor="#CCCCCC"><strong>'.$plang['pk_developer'].'</strong></td>
          <td bgcolor="#CCCCCC"><strong>'.$plang['pk_weblink'].'</strong></td>
          <td bgcolor="#CCCCCC">&nbsp;</td>
        </tr>
        <tr>
          <td>CT Raidtracker</td>
          <td>1.13 EQDKP Plus Version</td>
          <td>Freddy</td>
          <td><a href="http://www.curse-gaming.com/de/wow/addons-1837-1-eqdkp-ct_raidtracker-import-plugin.html%09" target="_blank">Link</a></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>Raidbanker</td>
          <td>1.0.1</td>
          <td>wallenium</td>
          <td><a href="http://forums.eqdkp.com/index.php?showtopic=3799" target="_blank">Link</a></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>Itemspecial</td>
          <td>3.0.2</td>
          <td>wallenium &amp; Corgan</td>
          <td><a href="http://forums.eqdkp.com/index.php?showtopic=4745%20" target="_blank">Link</a></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>Raidplaner</td>
          <td>2.02</td>
          <td>Stranger (Urox &amp; wallenium modded)</td>
          <td><a href="http://forums.eqdkp.com/index.php?showtopic=4232" target="_blank">Link</a></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>Twink Synchronizer</td>
          <td>1.1</td>
          <td>Tikki</td>
          <td><a href="http://forums.eqdkp.com/index.php?showtopic=5445" target="_blank">Link</a></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>Tradeskills</td>
          <td>0.97.5 Beta</td>
          <td><p>cns</p></td>
          <td><a href="http://forums.eqdkp.com/index.php?showtopic=5307" target="_blank">Link</a></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>Bossprogress </td>
          <td>2.0 Beta 14</td>
          <td>sz3</td>
          <td><a href="http://forums.eqdkp.com/index.php?showtopic=6027" target="_blank">Link</a></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>Charmanager</td>
          <td>1.0.3</td>
          <td>Wallenium</td>
          <td><a href="http://forums.eqdkp.com/index.php?showtopic=6274" target="_blank">Link</a></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>Ticket - Conversation</td>
          <td>0.06</td>
          <td>Achaz</td>
          <td></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr></table>';
   
   echo $tabs->endTab();
   echo $tabs->startTab($plang['pk_modifications'], 'ab_mods');   
   echo '
       <table width="100%" border="0">
    <tr> 
          <td width="26%" bgcolor="#CCCCCC"><strong>'.$plang['pk_modification'].'</strong></td>
          <td width="16%" bgcolor="#CCCCCC"><strong>'.$plang['pk_version'].'</strong></td>
          <td width="52%" bgcolor="#CCCCCC"><strong>'.$plang['pk_developer'].'</strong></td>
          <td width="13%" bgcolor="#CCCCCC"><strong>'.$plang['pk_weblink'].'</strong></td>
        </tr>
        <tr> 
          <td>Itemstats</td>
          <td>Ger/Eng v1.4</td>
          <td>Corgan</td>
          <td><a href="http://forums.eqdkp.com/index.php?showtopic=4950" target="_blank">Link</a></td>
        </tr>
        <tr> 
          <td>Bosscounter</td>
          <td>2.1</td>
          <td>Corgan</td>
          <td><a href="http://forums.eqdkp.com/index.php?showtopic=5041" target="_blank">Link</a></td>
        </tr>
         <tr> 
          <td>Newsloot</td>
          <td>&nbsp;</td>
          <td> Corgan (EQDKP Plus exklusiv)</td>
          <td>&nbsp;</td>
        </tr>
         <tr> 
          <td>Quick DKP</td>
          <td>&nbsp;</td>
          <td>Corgan (EQDKP Plus exklusiv)</td>
          <td>&nbsp;</td>
        </tr>
        <tr> 
          <td>Links</td>
          <td>&nbsp;</td>
          <td>Corgan (EQDKP Plus exklusiv)</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>Event Icons</td>
          <td></td>
          <td>Corgan (EQDKP Plus exklusiv)</td>
          <td></td>
        </tr>
        <tr>
          <td>GetDKP</td>
          <td>2.4</td>
          <td>Corgan</td>
          <td><a href="http://www.curse-gaming.com/en/wow/addons-2596-1-getdkp-plus.html%09" target="_blank">Link</a></td>
        </tr>
        <tr> 
          <td>Rassenicons</td>
          <td>&nbsp;</td>
          <td>Corgan</td>
          <td>&nbsp;</td>
        </tr>
        <tr> 
          <td>Leaderboard</td>
          <td>&nbsp;</td>
          <td>Legedric</td>
          <td><a href="http://forums.eqdkp.com/index.php?showtopic=5825" target="_blank">Link</a></td>
        </tr>
        <tr> 
          <td>3D Renderbilder </td>
          <td>&nbsp;</td>
          <td>Zeak (Corgan modded) </td>
          <td><a href="http://forums.eqdkp.com/index.php?showtopic=5636" target="_blank">Link</a></td>
        </tr>
        <tr> 
          <td>Rankicons</td>
          <td>&nbsp;</td>
          <td>Brear</td>
          <td>&nbsp;</td>
        </tr>
        <tr> 
          <td>Dropchance</td>
          <td>&nbsp;</td>
          <td>Trunkz</td>
          <td>&nbsp;</td>
        </tr>
        <tr> 
          <td>Klassenicons</td>
          <td>&nbsp;</td>
          <td>eqdkp forum</td>
          <td>&nbsp;</td>
        </tr>
        <tr> 
          <td>MMUser Hack</td>
          <td>&nbsp;</td>
          <td>Urox</td>
          <td>&nbsp;</td>
        </tr>
        </table>   
   ';
   echo $tabs->endTab();
   echo $tabs->startTab($plang['pk_themes'], 'ab_styles');   
   echo '
   <table width="100%" border="0"> 
     <tr>
          <td width="26%" bgcolor="#CCCCCC"><strong>'.$plang['pk_tname'].'</strong></td>
          <td width="16%" bgcolor="#CCCCCC"><strong>'.$plang['pk_version'].'</strong></td>
          <td width="52%" bgcolor="#CCCCCC"><strong>'.$plang['pk_developer'].'</strong></td>
          <td width="13%" bgcolor="#CCCCCC"><strong>'.$plang['pk_weblink'].'</strong></td>
          <td bgcolor="#CCCCCC">&nbsp;</td>
        </tr>
        <tr>
          <td>WoW_style</td>
          <td>2.0</td>
          <td>Hirogen(CD)</td>
          <td><a href="http://forums.eqdkp.com/index.php?showtopic=3002" target="_blank">Link</a></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>WoWMoonclaw01</td>
          <td>2.02</td>
          <td>MA&Euml;VAH</td>
          <td><a href="http://www.wowcr.net/templates" target="_blank">Link</a></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>WoWMaevahEmpire</td>
          <td>1.02</td>
          <td>MA&Euml;VAH</td>
          <td><a href="http://www.wowcr.net/templates" target="_blank">Link</a></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>WoW_Style_Vert</td>
          <td>0.1</td>
          <td>Urox</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>WoWV</td>
          <td>0.1</td>
          <td>Urox</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr></table>
   ';
   echo $tabs->endTab();
   echo $tabs->startTab($plang['pk_sponsors'], 'ab_donators');   
   echo '
   <table width="100%" border="0"> 
     <tr>
          <td bgcolor="#CCCCCC"><strong>'.$plang['pk_dona_name'].'</strong></td>
          <td bgcolor="#CCCCCC"><strong>'.$plang['pk_donation'].'</strong></td>
        </tr>
        <tr>
          <td>Garsti</td>
          <td>Paypal</td>
        </tr>
        <tr>
          <td>hennerich</td>
          <td>Paypal</td>
        </tr>
        <tr>
          <td>kalzen</td>
          <td>Paypal</td>
        </tr>
        <tr>
          <td>Corben</td>
          <td>Paypal</td>
        </tr>
        <tr>
          <td>Blackmikeks</td>
          <td>Paypal</td>
        </tr>
        <tr>
          <td>Many More Unknown</td>
          <td>Paypal</td>
        </tr>
        <tr>
          <td>MAF</td>
          <td>Amazon Wishlist</td>
        </tr>                
        <tr>
          <td>Klaus Damschen</td>
          <td>Amazon Wishlist</td>
        </tr>                
        <tr>
          <td>Shadowa</td>
          <td>Amazon Wishlist</td>
        </tr>                
        <tr>
          <td>Carnivore</td>
          <td>Amazon Wishlist</td>
        </tr>                
        <tr>
          <td>Webdancer</td>
          <td>Amazon Wishlist</td>
        </tr>                
			</table>
   ';
   echo $tabs->endTab();  
  echo $tabs->startTab($plang['pk_tab_stuff'], 'ab_team');   
   echo '
   <table width="100%" border="0"> 
     <tr>
          <td bgcolor="#CCCCCC"><strong>'.$plang['pk_dona_name'].'</strong></td>
          <td bgcolor="#CCCCCC"><strong>'.$plang['pk_job'].'</strong></td>
          <td bgcolor="#CCCCCC"><strong>'.$plang['pk_donation'].'</strong></td>
        </tr>
        <tr>
          <td><b>Corgan</b></td>
          <td>EQDKP Plus Project Founder, Developer</td>
          <td><a href=https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=eqdkp%40corgan%2dnet%2ede&item_name=EQDKP%20Plus%20Donation&item_number=3&page_style=PayPal&no_shipping=1&return=http%3a%2f%2feqdkp%2ecorgan%2dnet%2ede&cn=Optionale%20Angaben%2c%20W%c3%bcnsche%3a&tax=0&currency_code=EUR&bn=PP%2dDonationsBF&charset=UTF%2d8 target=_blank><img src=images/pp.gif></a> <a href=http://www.amazon.de/gp/registry/wishlist/1UEN3PVVN9TNY/028-1194709-0364549?reveal=unpurchased&filter=all&sort=priority&layout=standard&x=13&y=7 target=_blank><img src=images/amazon.de_wishlist.gif ></a> </td>
        </tr>
        <tr>
          <td><b>Wallenium</b></td>
          <td>Chief Software Architect</td>
          <td><a href=http://www.amazon.de/gp/registry/3ULZBCC258LM1 target=_blank><img src=images/amazon.de_wishlist.gif ></a> </td>
        </tr>
        <tr>
          <td><b>Cattiebrie</b></td>
          <td>Graphics & Design</td>
        </tr>
        <tr>
          <td><b>Hirogen(CD)</b></td>
          <td>Templates, Style, Documentation, Wiki</td>
          <td><a href=http://www.amazon.de/gp/registry/3QD941SZRG5J6 target=_blank><img src=images/amazon.de_wishlist.gif ></a> </td>
        </tr>
        <tr>
          <td><b>Spooky</b></td>
          <td>Support</td>
        </tr>
			</table>
			
			<table width="100%" border="0"> 
     <tr>
          <td bgcolor="#CCCCCC"><strong>'.$plang['pk_betateam1'].'</strong> '.$plang['pk_betateam2'].'</td>
        </tr>
        <tr>
          <td>Henri,Denis Christ,Martin Jäcke, Robin Schmidt, Stephan Stenz, Snipes,Christian Wiegel, 
          Patrick Bruner ,Michael Büsching, Thomas Luft , Holy, Onuris/Dante,Adam Chachaj, Jochen Rühl,
          Kevin Keils,Andre Melsbach, Miora Thetin,Sirflippi, CG66,  Martin Widemann,Pelzer-Iggy, Kalliope,
          Jochen Rühl, Maximilian Knop,Bernhard Gronau, Sven S, Sven Samoray, Chavez/Martin, Tobias Wirth, 
          phaces, anaj, Kevin Keils, Klaus Damschen, Martin Kratsch, Alexander Drees, Mike W., Thorge Schlünß, 
          Rondirai, Vampy, Flip, The Unknown, Claudi, Aoshi, Frank Schulzf, Markus Jessenitschnig, bennY aka Ferrus, 
          Alex Ax, Tim Kleinholz, Black, Henri Brumme, sector23, Achaz, Mirco Reimer, Sascha Büttner, Hycron,                 
          Stefan von der Forst,Robert Böck,Monique Haring,Christian Sell,Sylvio Richter, DDLM Admin, Stefan Dieter,
          Patrick Kebekus,Jean-Marc licht,Thomas Glatte,Frank Prior, Nico F, Lucas Brandstätter, Thorge Schlünß,        
          SeRoX, Leara, Manuela(cattiebrie), Lowmow, AGP-gilde,Thorge Schlünß, Blackhands United  </td>          
        </tr>
			</table>
   ';
   
   echo $tabs->endTab();
  echo $tabs->startTab($plang['pk_tab_help'], 'ab_help');   
   echo '
   <table width="100%" border="0"> 
     <tr>
          <td bgcolor="#CCCCCC"><strong>'.$plang['pk_sitename'].'</strong></td>
          <td bgcolor="#CCCCCC"><strong>'.$plang['pk_weblink'].'</strong></td>
        </tr>
        <tr>
          <td>German EQDKP Forum</td>
          <td><a href=http://forums.eqdkp.com/index.php?showforum=37 target=_blank> Link </a></td>
        </tr>
        <tr>
          <td>English EQDKP Forum</td>
          <td><a href=http://forums.eqdkp.com/index.php?act=idx target=_blank> Link </a></td>
        </tr>
        <tr>
          <td>German EQDKP Wiki</td>
          <td><a href=http://eqdkp.corgan-net.de/wiki/index.php/Hauptseite target=_blank> Link </a></td>
        </tr>
        <tr>
          <td>Bugtracker</td>
          <td><a href=http://eqdkp.corgan-net.de/bugtracker/?do=roadmap&project=4 target=_blank> Link </a></td>
        </tr>
			</table>
   ';
   
   echo $tabs->endTab();
   if($user->check_auth('a_config_man', false)){
   echo $tabs->startTab($plang['pk_tab_tech'], 'ab_tech');   
   echo '
   <table width="100%" border="0"> 
     <tr>
          <td bgcolor="#CCCCCC"><strong>'.$plang['pk_phpstring'].'</strong></td>
          <td bgcolor="#CCCCCC"><strong>'.$plang['pk_phpvalue'].'</strong></td>
        </tr>
        <tr>
          <td>Safe Mode:</td>
          <td>'.$specialphp->get_php_setting('safe_mode',1,0).'</td>
        </tr>
        <tr>
          <td>Register Globals:</td>
          <td>'.$specialphp->get_php_setting('register_globals',1,0).'</td>
        </tr>
         <tr>
          <td>CURL:</td>
          <td>'.$specialphp->get_curl_setting(1).'</td>
        </tr>
			</table>
   ';
   echo $tabs->endTab();
  }
   echo $tabs->endPane();
		echo "
		</td>
    </tr>
    
  </table>
</center>
</ul>

</body>
</html>
";

?>