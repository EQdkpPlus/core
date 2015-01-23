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
//File: language/english/lang_install.php
//Source-Language: german

$lang = array( 
	"page_title" => 'EQDKP-PLUS %s Installation',
	"back" => 'Save and back',
	"continue" => 'Proceed',
	"language" => 'Language',
	"inst_finish" => 'Complete installation',
	"error" => 'Error',
	"warning" => 'Warning',
	"success" => 'Success',
	"yes" => 'Yes',
	"no" => 'No',
	"retry" => 'Retry',
	"skip" => 'Skip',
	"step_order_error" => 'Step-Order error: Step not found. Please ensure that all files are uploaded correctly. For further information please visit our forums at <a href="http://eqdkp-plus.eu/forum">http://eqdkp-plus.eu/forum</a>.',
	"licence" => 'License Agreement',
	"php_check" => 'Pre-installation Check',
	"ftp_access" => 'FTP Settings',
	"encryptionkey" => 'Encryption Key',
	"data_folder" => 'Data Folder',
	"db_access" => 'Database Access',
	"inst_settings" => 'Settings',
	"admin_user" => 'Administrator Account',
	"end" => 'Complete the installation',
	"welcome" => 'Welcome to the installer for EQdkp Plus. We have worked hard to make this process easy and fast. To get started simply accept our license agreement by clicking \'Accept & Start Installation\' below.',
	"accept" => 'Accept & Start Installation',
	"license_text" => '<b>EQdkp Plus is published under AGPL v3.0 license.</b><br /><br /> The full license text can be found at <a href="http://opensource.org/licenses/AGPL-3.0" target="_blank">http://opensource.org/licenses/AGPL-3.0</a>.<br /><br />
	This is a summary of the most important terms of the AGPL v3.0. There is no claim to completeness and correctness.<br /><br />
	<h3><strong>You are permitted:</strong></h3>
<ul>
<li>to use this software for commercial use</li>
<li>to distribute this software</li>
<li>to modify this software</li>
</ul>
<h3><strong>You are required:</strong></h3>
<ul>
<li>to disclose the sourcecode of your complete application that uses EQdkp Plus, when you distribute your application</li>
<li>to disclose the sourcecode of your complete application that uses EQdkp Plus, if you don\'t distribute it, but users are using the software via network ("Hosting", "SaaS")</li>
<li>to remain the visible and unvisible Copyright Notices of this Project and to include a copy of the AGPL License at your application</li>
<li>to indicate significant changes made to the code</li>
</ul>
<h3><strong>It\'s forbidden:</strong></h3>
<ul>
<li>to held the author(s) of this software liable for any damages, the software is provided without warranty.</li>
<li>to license your application under another license than the AGPL</li>
</ul>',
	"table_pcheck_name" => 'Name',
	"table_pcheck_required" => 'Required',
	"table_pcheck_installed" => 'Current',
	"table_pcheck_rec" => 'Recommended',
	"module_php" => 'PHP version',
	"module_mysql" => 'MySQL database',
	"module_zLib" => 'zLib PHP module',
	"module_safemode" => 'PHP Safemode',
	"module_curl" => 'cURL PHP module',
	"module_fopen" => 'fopen PHP function',
	"module_soap" => 'SOAP PHP module',
	"module_autoload" => 'spl_autoload_register PHP function',
	"module_hash" => 'hash PHP function',
	"module_memory" => 'PHP memory limit',
	"module_json" => 'JSON PHP module',
	"safemode_warning" => '<strong>WARNING</strong><br/>Because the PHP Safe mode is active, you have to use the FTP mode in the next Step in order to use EQdkp Plus!',
	"phpcheck_success" => 'The minimum requirements for the installation of EQDKP-Plus are met. The installation can proceed.',
	"phpcheck_failed" => 'The minimum requirements for the installation of EQDKP-Plus are not met.<br />A selection of suitable hosting companies can be found on our <a href="http://eqdkp-plus.eu" target="_blank">website</a>',
	"do_match_opt_failed" => 'Some recommends are not met. EQDKP-Plus will work on this system; however, maybe not all features will be available.',
	"ftphost" => 'FTP host',
	"ftpport" => 'FTP port',
	"ftpuser" => 'FTP username',
	"ftppass" => 'FTP password',
	"ftproot" => 'FTP base dir',
	"ftproot_sub" => '(Path to the root directory of the FTP user)',
	"useftp" => 'Use FTP mode as file handler',
	"useftp_sub" => '(You can change it later by editing the config.php)',
	"safemode_ftpmustbeon" => 'Since PHP safe mode is on, the FTP details must be completed to continue the installation.',
	"ftp_connectionerror" => 'The FTP connection could not be established. Please check the FTP host and the FTP port.',
	"ftp_loginerror" => 'The FTP login was not successful. Please check your FTP username and FTP password.',
	"plain_config_nofile" => 'The file <b>config.php</b> is not available and automatic creation failed. <br />Please create a blank text file with the name <b>config.php</b> and set the permissions with chmod 777',
	"plain_config_nwrite" => 'The <b>config.php</b> file is not writeable. <br /> Please set the correct permissions. <b>chmod 0777 config.php</b>.',
	"plain_dataf_na" => 'The folder <b>'.registry::get_const('root_path').'data/</b> is not available.<br /> Please create this folder. <b>mkdir data</​​b>.',
	"plain_dataf_nwrite" => 'The folder <b>'.registry::get_const('root_path').'data/</b> is not writeable.<br /> Please set the correct permissions. <b>chmod -R 0777 data</​​b>.',
	"ftp_datawriteerror" => 'The Data folder could not be written to. Is the FTP root path set  correctly?',
	"ftp_info" => 'To improve security and functionality, you can choose to allow an ftp account of your choosing to perform file interactions on the server. This reduces the need to use more open server permissions, and may be required on some hosting configurations. To use this optional setting please provide a ftp user with permissions to access your installation, and select the \'FTP Mode\' tick box. If you are not using FTP Mode you may simply select proceed on this page.',
	"ftp_tmpinstallwriteerror" => 'The folder <b>'.registry::get_const('root_path').'data/97384261b8bbf966df16e5ad509922db/tmp/</b> is not writable.<br />To write the config-file, CHMOD 777 is required. This folder will be deleted after the installation process.',
	"ftp_tmpwriteerror" => 'The folder <b>'.registry::get_const('root_path').'data/%s/tmp/</b> is not writable.<br />Using FTP-Mode requires CHMOD 777 for this folder. This is the only folder needing writing permissions.',
	"dbtype" => 'Database type',
	"dbhost" => 'Database host',
	"dbname" => 'Database name',
	"dbuser" => 'Database username',
	"dbpass" => 'Database password',
	"table_prefix" => 'Prefix for EQDKP-Plus tables',
	"test_db" => 'Test database',
	"prefix_error" => 'No or invalid database prefix specified! Please enter a valid prefix.',
	"INST_ERR_PREFIX" => 'An EQdkp installation with that prefix already exists. Delete all tables with that prefix and repeat this step once you have used the "Back" button. Alternatively, you can choose a different prefix, e.g. if you want to install multiple sets of EQDKPlus data in a database.',
	"INST_ERR_DB_CONNECT" => 'Could not connect to the database, see error message below.',
	"INST_ERR_DB_NO_ERROR" => 'No error message given.',
	"INST_ERR_DB_NO_MYSQLI" => 'The version of MySQL installed on this machine is incompatible with the “MySQL with MySQLi Extension” option you have selected. Please try the “MySQL” option instead.',
	"INST_ERR_DB_NO_NAME" => 'No database name specified.',
	"INST_ERR_PREFIX_INVALID" => 'The table prefix you have specified is invalid for your database. Please try another, removing characters such as hyphen, apostrophe or forward- or back-slashes.',
	"INST_ERR_PREFIX_TOO_LONG" => 'The table prefix you have specified is too long. The maximum length is %d characters.',
	"dbcheck_success" => 'The database was checked. It found no errors or conflicts. The installation can be continued safely.',
	"encryptkey_info" => 'The encryption key is part of the encryption process used to protect sensitive data in the database, such as your users email addresses. Even if your database is compromised, without the encryption key your data remains encoded and secure. Therefore please choose a secure key, and ensure that you store a safe copy. Nobody else can ever retrieve it for you if it becomes lost!',
	"encryptkey" => 'Encryption-Key',
	"encryptkey_help" => '(min. length 6 chars)',
	"encryptkey_repeat" => 'Confirm the encryption Key',
	"encryptkey_no_match" => 'The encryptions keys do not match',
	"encryptkey_too_short" => 'The encryption key is too short. Minimum length is 6 chars.',
	"inst_db" => 'Install database',
	"lang_config" => 'Language settings',
	"default_lang" => 'Default language',
	"default_locale" => 'Default localization',
	"game_config" => 'Game settings',
	"default_game" => 'Default game',
	"server_config" => 'Server settings',
	"server_path" => 'Script path',
	"grp_guest" => 'Guests',
	"grp_super_admins" => 'Super administrators',
	"grp_admins" => 'administrators',
	"grp_officers" => 'officers',
	"grp_writers" => 'Editors',
	"grp_member" => 'Members',
	"grp_guest_desc" => 'Guests are not signed in users',
	"grp_super_admins_desc" => 'Super administrators have all rights',
	"grp_admins_desc" => 'Administrators do not have all admin rights',
	"grp_officers_desc" => 'Officers are able to manage raids',
	"grp_writers_desc" => 'Editors are able to write and manage news',
	"grp_member_desc" => 'member',
	"game_info" => 'More supported games can be downloaded after the installation at the extension-management.',
	"timezone" => 'Timezone of the server',
	"startday" => 'First day of the week',
	"sunday" => 'Sunday',
	"monday" => 'Monday',
	"time_format" => 'H:i',
	"date_long_format" => 'j. F Y',
	"date_short_format" => 'd.m.y',
	"style_jsdate_nrml" => 'dd/MM/YYYY',
	"style_jsdate_short" => 'd.M',
	"style_jstime" => 'h:mm tt',
	"welcome_news_title" => 'Welcome to EQDKP-Plus',
	"welcome_news" => '<p>The installation of your EQdkp Plus was completed successfully - you can now set it up according to your wishes.</p>
<p>You can find assistance to administration and general use in our <a href="http://eqdkp-plus.eu/wiki/" target="_blank">Wiki</a>.</p>
<p>For further support, please visit our <a href="http://eqdkp-plus.eu/forum" target="_blank">Forum</a>.</p>
<p>Have fun with EQdkp Plus! Your EQdkp Plus team</p>',
	"feature_news_title" => 'New Features of EQdkp Plus',
	"feature_news" => '<p>EQdkp Plus 2.0 contains a lot of new Features. This article should introduce the most importent of them.</p> <h3>Articlesystem</h3> <p>Instead of news and infopages, we introduced a complete new article system. Each news and page is now an article. You can group your articles using article-categories. Moreover, you can realise for example blogs for your guild and users.</p> <p>You can divide a single article using the Readmore- and Pagebreak-Methods. Also, you can insert Image-Galeries, Items or Raidloot using the appropriate Editor-Buttons.</p> <h3>Media-Management</h3> <p>Using the new Media-Management in ACP or Editor, you can now easily insert Media into your articles. For example, files can be uploaded using Drag&Drop. Also, you can even edit images in the Filebrowser.</p> <h3>Menu-Management</h3> <p>We have removed all menus except one. And the last one could be totally configured. You can position the entries using Drag&Drop in 3 levels, so it\'s possible to create submenus. You can still create links to external pages, but also add direct links to articles or articlecategories.</p> <h3>Portal-Management</h3> <p>In former times, there was only one portallayout, you had on every page the same portal modules. That\'s why we implemented the portallayouts. Now you can assign a portallayout to each articlecategory.</p> <p>Furthermore, you can create own portal blocks that you can embedd in your template, for example for editing links in your footer.</p>',
	"category1" => 'System',
	"category2" => 'News',
	"category3" => 'Events',
	"category4" => 'Items',
	"category5" => 'Raids',
	"category6" => 'Calendar',
	"category7" => 'Roster',
	"category8" => 'Points',
	"category9" => 'Character',
	"article5" => 'Character',
	"article6" => 'Roster',
	"article7" => 'Events',
	"article8" => 'Items',
	"article9" => 'Points',
	"article10" => 'Raids',
	"article12" => 'Calendarevent',
	"article13" => 'Calendar',
	"article14" => 'Guild Rules',
	"article15" => 'Privacy Policy',
	"article16" => 'Legal Notice',
	"role_healer" => 'Healer',
	"role_tank" => 'Tank',
	"role_range" => 'Ranged DPS',
	"role_melee" => 'Melee DPS',
	"create_user" => 'Create Access',
	"username" => 'Administrator username',
	"user_password" => 'Administrator password',
	"user_pw_confirm" => 'Confirm the administrator password',
	"user_email" => 'Administrator email address',
	"auto_login" => 'Remember me (cookie)',
	"user_required" => 'Username, email and password are required fields',
	"no_pw_match" => 'The passwords do not match.',
	"install_end_text" => 'The installation can now be completed successfully.',
	
);

?>