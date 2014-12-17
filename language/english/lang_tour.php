<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-Plus Language File
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2015 EQdkp-Plus Developer Team
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU Affero General Public License as published
 *	by the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU Affero General Public License for more details.
 *
 *	You should have received a copy of the GNU Affero General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

 
if (!defined('EQDKP_INC')) {
	die('You cannot access this file directly.');
}

//Language: English	
//Created by EQdkp Plus Translation Tool on  2014-12-17 23:17
//File: language/english/lang_tour.php
//Source-Language: german

$lang = array( 
	"navi" => '<ul><li><a href="?tour=next"><b>Continue with the next step of this tour</b></a></li><li><a href="?tour=reload">Restart this step</a></li><li><a href="?tour=cancel">Exit the tour</a></li></ul>',
	"navi_title" => 'EQdkp Plus Tour',
	"steps" => 'Step %d of %d',
	"step_0" => 'Welcome to the EQdkp Plus Tour!<br /><br />This tour will show you the most important functionalities of our CMS & DKP-system. <br /><ul><li>Settings</li><li>Plugins</li><li>Modules</li><li>User managemant</li><li>Raid management</li><li>Layout/DKP-system</li><li>Create CMS pages</li><li>Backup</li></ul>',
	"step_0_title" => 'Start',
	"step_1" => 'On this page you can configure your EQdkp Plus. For example you will see settings for<ul><li>Layout settings like the page title</li><li>Game settings like your guild\'s name, server name, ...</li><li>Contact information</li><li>Email settings</li><li>Registration settings (i.e. CAPTCHA)</li><li>Itemstats settings</li></ul>',
	"step_1_title" => 'Settings',
	"step_2" => 'Plugins are extensions to expand the functionality of your EQdkp Plus. To install a plugin just click "Install".<br /><br />Suggested plugins:<ul><li>Raidlogimport: import raid logs from ingame addons</li></ul>',
	"step_2_title" => 'Plugins',
	"step_3" => 'With modules for the CMS you can put several interesting information on the front-end. For example:<ul><li>Teamspeak or other voice server</li><li>recent battle logs</li><li>upcoming birthdays</li><li>weather forecast</li></ul>etc. <br>By using the tab "Positioning" you can move each module to where it should show up in the front-end.<br><br>With the settings from step 1 you can also set which columns should show up on every page instead of only the index page which is the standard behaviour.',
	"step_3_title" => 'Portal modules',
	"step_4" => 'This is the user management. You can for example activate users which are still inactive due to registration settings.<br /><br />A user is only able to apply for a raid and collect DKP points if you assign him a character first.<br /><br />You can also manage user permissions here. Which means you are able to choose what each user is allowed to do within EQdkp Plus.<br />You can do that either by assigning different user groups to that user or by managing its permissions individually.<br /><br />Unregistered users are part of the "Guests" user group and with it you can manage their permissions.<br ><br >If you\'d like to have more information about permissions check out <a href="http://eqdkp-plus.eu/wiki//de/index.php/Benutzergruppen" target="_blank" style="color:#000;">this Wiki page</a>',
	"step_4_title" => 'User management',
	"step_5" => 'In general raids are used to assign DKP points to characters. The date, event, DKP value and of course the participants (the characters which should get DKP points) are set in a raid.<br>Furthermore you can also distribute the items to the characters and deduct the corresponding points from their account. Individual adjustments are also possible.<br /><br />If you\'re using an ingame addon to log the raid us the plugin "Raidlogimport" which will automatically create the raids for you.<br /><br />If you\'d like more information about raids check out <a href="http://eqdkp-plus.eu/wiki//de/index.php/Ein_Beispielraid" target="_blank" style="color:#000;">this Wiki page</a>.',
	"step_5_title" => 'Manage Raids',
	"step_6" => 'As everyone has own requirements of how his EQdkp plus should look like or which DKP points system to be used, you can select here, how the EQdkp plus shall look like.<br /><br />Beneath premade layouts like "normal", "EPGP" or "Suicide kings" you also can arrange your own layout.<br /><br />In every tab you can define for the respective page, which columns shall be displayed.',
	"step_6_title" => 'Manage layouts',
	"step_7" => 'Of course a CMS incorporates the ability to create your own content.<br >With our extensive editor and media manager most needs for content creation are satisfied.<br ><br >Additionaly, you can also set up your guild rules which each user has to accept in order to register.',
	"step_7_title" => 'Create Articles',
	"step_8" => 'Many have experienced it, no one liked it - system failure, data loss and backup absence<br /><br />This can\'t happen anymore!<br /><br />In addition to restoration from backups you now can set up automated backup jobs. Go to "Cron-job management" to set the options.<br /><br />If you\'d like more information about backup check out <a href="http://eqdkp-plus.eu/wiki//de/index.php/Sicherung" target="_blank" style="color:#000;">this Wiki page</a>.',
	"step_8_title" => 'Backup',
	"step_9" => 'Thank you very much for taking the EQdkp Plus tour.<br><br>For additional questions check out<ul><li>our <a href="http://eqdkp-plus.eu/wiki/" style="color:#000">Wiki</a></li><li>and <a href="http://eqdkp-plus.eu/forum" style="color:#000">Forum</a>.</li></ul>You can take this tour again whenever you like by going to the "Support" tab on the main page of the admin section.<br /><br />"Have fun" - the entire EQdkp Plus team.',
	"step_9_title" => 'End',
	
);

?>