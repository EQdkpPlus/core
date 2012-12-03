<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * file.php
 * Began: Day January 1 2003
 *
 * $Id$
 *
 ******************************/

if ( !defined('EQDKP_INC') )
{
     die('Do not access this file directly.');
}

$lang['inst_header'] = 'EQdkp Installation';

// ===========================================================
//	Step 1: PHP / Mysql Environment
// ===========================================================

$lang['inst_eqdkp'] = 'EQdkp';
	$lang['inst_version'] = 'Version';
	$lang['inst_using'] = 'Using';
	$lang['inst_latest'] = 'Latest';

$lang['inst_php'] = 'PHP';
	$lang['inst_view'] = 'View phpinfo()';
	$lang['inst_required'] = 'Required';
	$lang['inst_major_version'] = 'Major Version';
	$lang['inst_minor_version'] = 'Minor Version';
	$lang['inst_version_classification'] = 'Version Classification';
	$lang['inst_yes'] = 'Yes';
	$lang['inst_no'] = 'No';

$lang['inst_php_modules'] = 'PHP Modules';
	$lang['inst_Supported'] = 'Supported';

$lang['inst_step1'] = 'Installation: Step 1';
	$lang['inst_note1'] = 'EQdkp has scanned your system and determined that you meet the minimum requirements for installation.';
	$lang['inst_note1_error'] = '<B><FONT SIZE="+1" COLOR="red">WARNING</font></B><BR>EQdkp has scanned your system and determined that you do not meet the minimum requirements for installation.<BR>Please upgrade to the minimum requirements.';
	$lang['inst_button1'] = 'Start Install';

// ===========================================================
//	Step 2: Server / Database
// ===========================================================

$lang['inst_language_configuration'] = 'Language Configuration';
	$lang['inst_default_lang'] = 'Default Language';

$lang['inst_database_configuration'] = 'Database Configuration';
	$lang['inst_dbtype'] = 'Database Type';
	$lang['inst_dbhost'] = 'Database Host';
		$lang['inst_default_dbhost'] = 'localhost';
	$lang['inst_dbname'] = 'Database Name';
	$lang['inst_dbuser'] = 'Database Username';
	$lang['inst_dbpass'] = 'Database Password';
	$lang['inst_table_prefix'] = 'Prefix for EQdkp tables';
		$lang['inst_default_table_prefix'] = 'eqdkp_';

$lang['inst_server_configuration'] = 'Server Configuration';
	$lang['inst_server_name'] = 'Domain Name';
	$lang['inst_server_port'] = 'Webserver Port';
	$lang['inst_server_path'] = 'Script path';

$lang['inst_step2'] = 'Installation: Step 2';
	$lang['inst_note2'] = 'Before proceeding, please verify that your database is already created and that the username and password you provided have permission to create tables on that database';
	$lang['inst_button2'] = 'Install Database';


// ===========================================================
//	Step 3: Accounts
// ===========================================================

$lang['inst_administrator_configuration'] = 'Administrator Configuration';
	$lang['inst_username'] = 'Administrator Username';
	$lang['inst_user_password'] = 'Administrator Password';
	$lang['inst_user_password_confirm'] = 'Confrim Administrator Password';
	$lang['inst_user_email'] = 'Administrator Email Address';

$lang['inst_initial_accounts'] = 'Initial Accounts';
	$lang['inst_guild_members'] = 'Guild Members';

$lang['inst_step3'] = 'Installation: Step 3';
	$lang['inst_note3'] = 'Note: All initial accounts will be created with a password that matches the member name, please advise your members to change their passwords.';
	$lang['inst_button3'] = 'Install Accounts';


// ===========================================================
//	Step 4: EQdkp Preferences
// ===========================================================

$lang['inst_general_settings'] = 'General Settings';
	$lang['inst_guildtag'] = 'Guildtag / Alliance Name';
	$lang['inst_guildtag_note'] = 'Used in the title of nearly every page';
	$lang['inst_parsetags'] = 'Guildtags to Parse';
	$lang['inst_parsetags_note'] = 'Those listed will be available as options when parsing raid logs.';
	$lang['inst_domain_name'] = 'Domain Name';
	$lang['inst_server_port'] = 'Server Port';
	$lang['inst_server_port_note'] = 'Your webserver\'s port. Usually 80';
	$lang['inst_script_path'] = 'Script Path';
	$lang['inst_script_path_note'] = 'Path where EQdkp is located, relative to the domain name';
	$lang['inst_site_name'] = 'Site Name';
	$lang['inst_site_description'] = 'Site Description';
	$lang['inst_point_name'] = 'Point Name';
	$lang['inst_point_name_note'] = 'Ex: DKP, RP, etc.';
	$lang['inst_enable_account_activation'] = 'Enable Account Activation';
	$lang['inst_none'] = 'None';
	$lang['inst_user'] = 'User';
	$lang['inst_admin'] = 'Admin';
	$lang['inst_default_language'] = 'Default Language';
	$lang['inst_default_style'] = 'Default Style';
	$lang['inst_default_page'] = 'Default Index Page';
	$lang['inst_hide_inactive'] = 'Hide Inactive Members';
	$lang['inst_hide_inactive_note'] = 'Hide members that haven\'t attended a raid in [inactive period] days?';
	$lang['inst_inactive_period'] = 'Inactive Period';
	$lang['inst_inactive_period_note'] = 'Number of days a member can miss a raid and still be considered active';
	$lang['inst_inactive_point_adj'] = 'Inactive Point Adjustment';
	$lang['inst_inactive_point_adj_note'] = 'Point adjustment to make on a member when they become inactive.';
	$lang['inst_active_point_adj'] = 'Active Point Adjustment';
	$lang['inst_active_point_adj_note'] = 'Point Adjustment to make on a member when they become active.';
	$lang['inst_enable_gzip'] = 'Enable Gzip Compression';

	$lang['inst_preview'] = 'Preview';
	$lang['inst_account_settings'] = 'Account Settings';
	$lang['inst_adjustments_per_page'] = 'Adjustments per Page';
	$lang['inst_basic'] = 'Basic';
	$lang['inst_events_per_page'] = 'Events per Page';
	$lang['inst_items_per_page'] = 'Items per Page';
	$lang['inst_news_per_page'] = 'News Entries per Page';
	$lang['inst_raids_per_page'] = 'Raids per Page';

$lang['inst_step4'] = 'Installation: Step 4';
	$lang['inst_note4'] = 'Note: All of these settings are configurable from within the system. Simply go to Administration Pannel > Configuration.';
	$lang['inst_button4'] = 'Save Preferences';


// ===========================================================
//	Step 5: Finish
// ===========================================================

$lang['inst_step5'] = 'Finished';
	$lang['inst_note5'] = 'Installation is now complete, you may log in below.';

$lang['login'] = 'Login';
	$lang['username'] = 'Username';
	$lang['password'] = 'Password';
	$lang['remember_password'] = 'Remember me (cookie)';

	$lang['lost_password'] = 'Lost Password';


?>