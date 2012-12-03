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

define('EQDKP_INC', true);
$eqdkp_root_path = '../';
include_once($eqdkp_root_path . 'common.php');
include_once('include/php.class.php');
include_once('include/init.class.php');

// Load the language
$plang = $pluslang->NormalLanguage();

$init = new InitPlus();
$tabs = new Tabs();
$specialphp = new phpAdditions();

require_once($eqdkp_root_path . 'pluskernel/include/siteDisplay.class.php');
$siteDisplay = new siteDisplay();

echo $init->Header($eqdkp_root_path);

 // Build the data in arrays..
	$author = array(
		'personal_url'		=> 'http://www.corgan-net.de',
		'personal_name'		=> 'corgan-net.de',
		'web_url'			=> 'http://www.eqdkp-plus.com',
		'web_name'			=> 'EQKDP Plus',
		'name'				=> 'EQDKP PLUS Developer Team',
		'city'				=> 'Germany',
	);

	$images = array(
		'mainimage' 		=> 'logo_eqdkp_plus.gif',
		'mainimage_alt'		=> 'EQDKP PLUS Logo',
	);

	$siteowner = array(
		'admin'	 	=> (strlen($conf_plus['pk_contact_name']) > 0 ) ? "<a href=mailto:".$conf_plus['pk_contact_email']."> ".$conf_plus['pk_contact_name']."</a>" : '' ,
		'website'	=> (strlen($conf_plus['pk_contact_website']) > 0 ) ? "<a href='".$conf_plus['pk_contact_website']."'> ".$conf_plus['pk_contact_website']." </a>"  : '' ,
		'irc'	 	=> (strlen($conf_plus['pk_contact_irc']) > 0 ) ? "IRC: ".$conf_plus['pk_contact_irc']." | " : '' ,
		'messenger'	=> (strlen($conf_plus['pk_contact_admin_messenger']) > 0 ) ? "Messenger: ".$conf_plus['pk_contact_admin_messenger'] : '' ,
		'infos'	 	=> (strlen($conf_plus['pk_contact_custominfos']) > 0 ) ? "Infos: ".$conf_plus['pk_contact_custominfos'] : ''
	);


// here must be the html shit
// Don't change anything below that.. all config is above..)
echo "<style type='text/css'>
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


<ul class='intro_message'>
  <table border='0' cellpadding='0' cellspacing='0' class='borderless' width='590'>
    <tr>
      <td style='padding-right: 10px;' valign='top' align='center' width='275'><img class='reflect' style='float: left;' src='".$eqdkp_root_path."/images/".$images['mainimage']."' alt='Eqdkp Plus Logo' border='0'><br>
      ".$plang['pk_version']." ".EQDKPPLUS_VERSION." " .$siteDisplay->info."</td>
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
					<!--
					<tr>
						<td>".$plang['personal_url'].": <a class='linkcrap' href='".$author['personal_url']."' target='_blank'>".$author['personal_name']."</a>
						</td>
					</tr>
					-->
					<tr>
						<td>

						</td>
					</tr>
				</table>
				</td>
				<td valign=top>
				<a href=https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=eqdkp%40corgan%2dnet%2ede&item_name=EQDKP%20Plus%20Donation&item_number=3&page_style=PayPal&no_shipping=1&return=http%3a%2f%2feqdkp%2ecorgan%2dnet%2ede&cn=Optionale%20Angaben%2c%20W%c3%bcnsche%3a&tax=0&currency_code=EUR&bn=PP%2dDonationsBF&charset=UTF%2d8 target=_blank><img src=images/project-support.jpg></a>
				</td>
			</tr>
			<tr>
			<td colspan=2>
			 <hr noshade> ".$plang['pk_contact_owner']."
			".$siteowner['admin']."
			".$siteowner['website']."<br>
			".$siteowner['irc']."
			".$siteowner['messenger']."<br>
			".$siteowner['infos']."
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
        <tr valign=top>
          <td>Original EQDKP</td>
          <td>1.3.2</td>
          <td>Tsigo</td>
          <td><a href="http://eqdkp.com/" target="_blank">Link</a></td>

        </tr>
        <tr valign=top>
          <td>EQDKP Plus</td>
          <td>'.EQDKPPLUS_VERSION.'</td>
          <td><a href=mailto:Corgan@eqdkp-plus.com>Corgan</a></td>
          <td><a href="http://eqdkp-plus.com" target="_blank">Link</a></td>
        </tr>
        <tr valign=top>
          <td>EQDKP Plus Kernel</td>
          <td>'.EQDKPPLUS_VERSION.'</td>
          <td><a href="mailto:wallenium@eqdkp-plus.com">Wallenium</a> & <a href=mailto:Corgan@eqdkp-plus.com>Corgan</a></td>
          <td><a href="http://eqdkp-plus.com" target="_blank">Link</a></td>
        </tr>
        <tr valign=top>
          <td>Developer Team</td>
          <td>'.EQDKPPLUS_VERSION.'</td>
          <td>Corgan, Wallenium, sz3, hoofy, Ghoschdi,<br> Badtwin, Aderyn, Sylna, Charla, <br>zAfLu, Luna, Lightstalker, Cattiebrie,<br> bargas, kirax89   </td>
          <td><a href="https://sourceforge.net/project/memberlist.php?group_id=167016" target="_blank">Link</a></td>
        </tr>
				</table>';
   echo ' <br/>';
   echo ' <br/>';
   echo '<a href=http://www.blizzard.com/legalfaq.shtml>World of Warcraft and Blizzard Entertainment </a> are trademarks or registered trademarks of Blizzard Entertainment, Inc. in the U.S. and/or other countries. EQdkp-Plus is in no way associated with Blizzard Entertainment.
 	<br/> <br/>';
   echo ' The EQdkp-Plus Kernel (the folder /pluskernel), the portal-moduls (/portal), the plugins, and WoW-Addons from
   <ul> 
   <li> Corgan (Kernel, Portal, Moduls)</li>
   <li> Wallenium (Itemspecials, Newsletter, Raidbanker, Raidplan, Charmanager)</li>
   <li> sz3 (Bosssuite, info)</li>
   <li> sylna, Charla (GetDKP)</li>
   </ul>
	are available under the <a href="http://creativecommons.org/licenses/by-nc-sa/3.0/" target="_blank">Creative Common Licence: Attribution-Noncommercial-Share Alike 3.0. </a>
	<br>';   
   echo '
   	<br>You are free to : <br>
	<ul>
	    <li>to Share — to copy, distribute and transmit the work</li>
	    <li> to Remix — to adapt the work</li></ul>
	<br>
	Under the following conditions:<br>
	<ul>
     <li> Attribution. You must attribute the work in the manner specified by the author or licensor (but not in any way that suggests that they endorse you or your use of the work). </li>
    <li> Noncommercial. You may not use this work for commercial purposes.</li>
    <li> Share Alike. If you alter, transform, or build upon this work, you may distribute the resulting work only under the same or similar license to this one.</li>
    <li> No operating of any hosting-platform or any other other kind of utilization which exceeds the level of private usage, even if it is not commercial</li>
    <li> The "Support EQdkp Plus-Link" and the shown commercials are not allowed to be removed.</li></ul>
   ';
   echo ' <br/>';
   echo ' <br/>';
   echo ' <br/>';
   echo $tabs->endTab();

   echo $tabs->startTab($plang['pk_plugins'], 'ab_plugins');
   echo '
    <table width="100%" border="0">
        <tr>
          <td bgcolor="#CCCCCC"><strong>'.$plang['pk_plugin'].'</strong></td>
          <td bgcolor="#CCCCCC"><strong>'.$plang['pk_developer'].'</strong></td>
          <td bgcolor="#CCCCCC"><strong>'.$plang['pk_weblink'].'</strong></td>
          <td bgcolor="#CCCCCC">&nbsp;</td>
        </tr>
        <tr>
          <td>CT Raidtracker</td>
          <td>Freddy, Logafive, Corgan</td>
          <td><a href="http://code.google.com/p/eqdkp-raidtracker" target="_blank">Link</a></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>Raidlogimporter</td>
          <td>hoofy</td>
          <td><a href="http://www.eqdkp-plus.com" target="_blank">Link</a></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>Raidbanker</td>
          <td>Wallenium</td>
          <td><a href="http://www.eqdkp-plus.com" target="_blank">Link</a></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>Itemspecial</td>
          <td>Wallenium</td>
          <td><a href="http://www.eqdkp-plus.com" target="_blank">Link</a></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>Charmanager</td>
          <td>Wallenium</td>
          <td><a href="http://www.eqdkp-plus.com" target="_blank">Link</a></td>
          <td>&nbsp;</td>
        </tr>
         <tr>
          <td>Newsletter</td>
          <td>Wallenium</td>
          <td><a href="http://www.eqdkp-plus.com" target="_blank">Link</a></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>Raidplanner</td>
          <td>Wallenium</td>
          <td><a href="http://www.eqdkp-plus.com" target="_blank">Link</a></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>BossSuite</td>
          <td>sz3</td>
          <td><a href="http://www.eqdkp-plus.com" target="_blank">Link</a></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>Info</td>
          <td>sz3</td>
          <td><a href="http://www.eqdkp-plus.com" target="_blank">Link</a></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>Tradeskills</td>
          <td><p>Achaz (cns)</p></td>
          <td><a href="http://www.eqdkp-plus.com" target="_blank">Link</a></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>Ticket - Conversation</td>
          <td>Achaz</td>
          <td><a href="https://sourceforge.net/project/showfiles.php?group_id=167016" target="_blank">Link</a></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>Gallery</td>
          <td>Badtwin & Lunary</td>
          <td><a href="http://www.eqdkp-plus.com" target="_blank">Link</a></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>Shoutbox</td>
          <td>Aderyn</td>
          <td><a href="http://www.eqdkp-plus.com" target="_blank">Link</a></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>GuildRequest</td>
          <td>Badtwin</td>
          <td><a href="http://www.eqdkp-plus.com" target="_blank">Link</a></td>
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
          <td width="48%" bgcolor="#CCCCCC"><strong>'.$plang['pk_modification'].'</strong></td>
          <td width="52%" bgcolor="#CCCCCC"><strong>'.$plang['pk_developer'].'</strong></td>
        </tr>
        <tr>
          <td>Itemstats (WoW-Head, Armory)</td>
          <td>Corgan, Yoghurt</td>
        </tr>
        <tr>
          <td>RSS Game News</td>
          <td>Corgan powered by <a href="http://www.allvatar.com" target=_blank>Allvatar</a> </td>
        </tr>
        <tr>
          <td>WoW-Signatur</td>
          <td>Corgan powered by <a href="http://www.allvatar.com" target=_blank>Allvatar</a></td>
        </tr>
        <tr>
          <td>Bosscounter</td>
          <td>Corgan</td>
        </tr>
         <tr>
          <td>Newsloot</td>
          <td> Corgan (EQDKP Plus exklusiv)</td>
        </tr>
         <tr>
          <td>Quick DKP</td>
          <td>Corgan (EQDKP Plus exklusiv)</td>
        </tr>
        <tr>
          <td>Links</td>
          <td>Corgan (EQDKP Plus exklusiv)</td>
        </tr>
        <tr>
          <td>Event Icons</td>
          <td>Corgan (EQDKP Plus exklusiv)</td>
        </tr>
        <tr>
          <td>GetDKP</td>
          <td>Corgan & Charla</td>
        </tr>
        <tr>
          <td>Rassenicons</td>
          <td>Corgan</td>
        </tr>
        <tr>
          <td>Adminicons</td>
          <td>Corgan</td>
        </tr>
        <tr>
          <td>Leaderboard</td>
          <td>Corgan</td>
        </tr>
        <tr>
          <td>3D Renderbilder </td>
          <td>Corgan images by Cattebrie</td>
        </tr>
        <tr>
          <td>Rankicons</td>
          <td>Brear</td>
        </tr>
        <tr>
          <td>Dropchance</td>
          <td>Trunkz, Sz3</td>
        </tr>
        <tr>
          <td>Klassenicons</td>
          <td>Eqdkp forum</td>
        </tr>
        <tr>
          <td>MMUser Hack</td>
          <td>Urox</td>
        </tr>
        <tr>
          <td>CMS Bridge</td>
          <td>Redpepper</td>
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
          <td><a href="http://www.wowcr.net/styles/" target="_blank">Link</a></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>WoWMaevahEmpire</td>
          <td>1.02</td>
          <td>MA&Euml;VAH</td>
          <td><a href="http://www.wowcr.net/styles/" target="_blank">Link</a></td>
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
        </tr>
   		<tr>
          <td>WoW - TBC Template</td>
          <td></td>
          <td>mergenine</td>
          <td><a href="http://www.mergenine.com" target="_blank">Link</a></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>Luna WotLk</td>
          <td>1.0</td>
          <td>Lunary</td>
          <td><a href="http://www.eqdkp-plus.com" target="_blank">Link</a></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>m9wotlk</td>
          <td>1.0</td>
          <td>Cattebrie</td>
          <td><a href="http://www.eqdkp-plus.com" target="_blank">Link</a></td>
          <td>&nbsp;</td>
        </tr>
        </table>
   ';
   echo $tabs->endTab();
   echo $tabs->startTab('Modules', 'ab_moduls');
   echo '
   <table width="100%" border="0">
     <tr>
          <td bgcolor="#CCCCCC"><strong>Module</strong></td>
          <td bgcolor="#CCCCCC"><strong>Coder</strong></td>
        </tr>
        <tr>
          <td>Advertising Module</td>
          <td>Corgan</td>
        </tr>
        <tr>
          <td>Bossguides</td>
          <td>Corgan & allvatar.com</td>
        </tr>
        <tr>
          <td>Custom Content Module</td>
          <td>WalleniuM  	</td>
        </tr>
        <tr>
          <td>DKPInfo Module</td>
          <td>Corgan</td>
        </tr>
        <tr>
          <td>LastItems Module</td>
          <td>Corgan</td>
        </tr>
        <tr>
          <td>LastRaids Module</td>
          <td>Corgan</td>
        </tr>
        <tr>
          <td>Latest Forum Posts</td>
          <td>WalleniuM</td>
        </tr>
        <tr>
          <td>Online Module</td>
          <td>Aderyn</td>
        </tr>
        <tr>
          <td>Quick Login</td>
          <td>BadTwin</td>
        </tr>
        <tr>
          <td>QuickDKP Module</td>
          <td>Corgan</td>
        </tr>
        <tr>
          <td>QuickSearch</td>
          <td>BadTwin</td>
        </tr>
        <tr>
          <td>RankImage Module</td>
          <td>Corgan</td>
        </tr>
        <tr>
          <td>Realmstatus Module</td>
          <td>Aderyn</td>
        </tr>
        <tr>
          <td>Recruitment Module</td>
          <td>Corgan</td>
        </tr>
        <tr>
          <td>Teamspeak Module</td>
          <td>Corgan</td>
        </tr>
        <tr>
          <td>Ventrilo Status</td>
          <td>Chex</td>
        </tr>
        <tr>
          <td>BossCounter </td>
          <td>sz3</td>
        </tr>

			</table>
			<br>
			
   ';
   echo $tabs->endTab();
  echo $tabs->startTab($plang['pk_tab_stuff'], 'ab_team');
   echo '
   <table width="100%" border="0" cellspacing=5 cellpadding=2>
     <tr>
          <td bgcolor="#CCCCCC"><strong>'.$plang['pk_dona_name'].'</strong></td>
          <td bgcolor="#CCCCCC"><strong>'.$plang['pk_job'].'</strong></td>
          <td bgcolor="#CCCCCC"><strong>'.$plang['pk_donation'].'</strong></td>
        </tr>
        <tr>
          <td valign=top><b>Stefan Knaak<br>"Corgan"</b></td>
          <td valign=top>EQDKP Plus Project Founder, Developer, Homepage</td>
          <td><a href=https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=eqdkp%40corgan%2dnet%2ede&item_name=EQDKP%20Plus%20Donation&item_number=3&page_style=PayPal&no_shipping=1&return=http%3a%2f%2feqdkp%2ecorgan%2dnet%2ede&cn=Optionale%20Angaben%2c%20W%c3%bcnsche%3a&tax=0&currency_code=EUR&bn=PP%2dDonationsBF&charset=UTF%2d8 target=_blank><img src=images/pp.gif></a> <a href=http://www.amazon.de/gp/registry/wishlist/1UEN3PVVN9TNY/028-1194709-0364549?reveal=unpurchased&filter=all&sort=priority&layout=standard&x=13&y=7 target=_blank><img src=images/amazon.de_wishlist.gif ></a> </td>
        </tr>
        <tr>
          <td valign=top><b>Simon Wallman <br>"Wallenium"</b></td>
          <td valign=top>Chief Software Architect</td>
          <td><a href=http://www.amazon.de/gp/registry/3ULZBCC258LM1 target=_blank><img src=images/amazon.de_wishlist.gif ></a> </td>
        </tr>
        <tr>
          <td><b>sz3</b></td>
          <td valign=top>Developer (Bosssuite)</td>
          <td></td>
        </tr>
        <tr>
          <td valign=top><b>Manuela Schwingenschrot<br>"Cattiebrie"</b></td>
          <td valign=top>Graphics, Design and Homepage</td>
        </tr>
        <tr>
          <td valign=top><b>Boris Meyer <br>"Charla"</b></td>
          <td valign=top>GetDKP</td>
          <td></td>
        </tr>
        <tr>
          <td valign=top><b>Sylna</b></td>
          <td valign=top>GetDKP</td>
          <td></td>
        </tr>
        <tr>
          <td valign=top><b>Chris Staudte <br>"Lightstalker"</b></td>
          <td valign=top>Support, IRC, Main-Tester</td>
          <td></td>
        </tr>
        <tr>
          <td valign=top><b>Falk Köppe <br>"Murphyslaw"</b></td>
          <td valign=top>Developer</td>
          <td></td>
        </tr>
        <tr>
          <td valign=top><b>Ghoschdi</b></td>
          <td valign=top>Developer, Bridge</td>
          <td></td>
        </tr>
        <tr>
          <td valign=top><b>Aderyn</b></td>
          <td valign=top>Shoutbox, Online Module, Realmstatus Module </td>
          <td></td>
        </tr>
        <tr>
          <td valign=top><b>BadTwin</b></td>
          <td valign=top>Gallery, Quick Login,QuickSearch</td>
          <td></td>
        </tr>
        <tr>
          <td valign=top><b>Lunary</b></td>
          <td valign=top>Gallery</td>
          <td></td>
        </tr>
        <tr>
          <td valign=top><b>Hoofy</b></td>
          <td valign=top>Raidlogimport, Itemstats</td>
          <td></td>
        </tr>
        <tr>
          <td valign=top><b> </b></td>
          <td valign=top></td>
          <td></td>
        </tr>
        <tr>
          <td valign=top><b> </b></td>
          <td valign=top></td>
          <td></td>
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
          <td>EQDKP-Plus Homepage</td>
          <td><a href="http://www.eqdkp-plus.com" target="_blank"> Link </a></td>
        </tr>
        <tr>
          <td>EQDKP Plus Forum</td>
          <td><a href="http://www.eqdkp-plus.com/forum/" target=_blank> Link </a></td>
        </tr>
        <tr>
          <td>German EQDKP Wiki</td>
          <td><a href="http://wiki.eqdkp-plus.com/wiki/index.php/Hauptseite" target="_blank"> Link </a></td>
        </tr>
        <tr>
          <td>Bugtracker</td>
          <td><a href="http://bugtracker.eqdkp-plus.com?do=roadmap&project=4" target="_blank"> Link </a></td>
        </tr>
		</table>
   ';

   echo $tabs->endTab();
   
   
   echo $tabs->startTab($plang['pk_disclaimer'], 'ab_help');
   
   $disclaimerfile = $eqdkp_root_path.'pluskernel/language/'.$user->data['user_lang'].'/disclaimer.php' ;  
   if (file_exists($disclaimerfile)) 
   {
   		include_once($disclaimerfile);
   		echo $disclaimer;
   	
   }  
   
   echo $tabs->endTab();   
   
   
   if($user->check_auth('a_config_man', false)){
   echo $tabs->startTab($plang['pk_tab_tech'], 'ab_tech');

   $our_php_version   = (( phpversion() >= '4.1.2' ) ? '<span class="positive">' : '<span class="negative">') . phpversion() . '</span>';

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
         <tr>
          <td>Fopen:</td>
          <td>'.$specialphp->check_PHP_Function('fopen',1).'</td>
        </tr>
         <tr>
          <td>PHP Version:</td>
          <td>'.$our_php_version.'</td>
        </tr>
         <tr>
          <td>Mysql Version:</td>
          <td> Client('.mysql_get_client_info().') Server('.mysql_get_server_info().')</td>
        </tr>
        <tr>
        <td></td><td></td>
        </tr>
        <tr>
        <td colspan=2>Based on <a href="http://eqdkp.com/" target="_new" class="copy">EQdkp</a> '.EQDKP_VERSION.'</td>
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
";

echo $init->Footer();
?>
