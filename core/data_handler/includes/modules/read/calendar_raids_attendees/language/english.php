<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2010
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 * 
 * $Id$
 */

if ( !defined('EQDKP_INC') ){
    header('HTTP/1.0 404 Not Found');exit;
}

$module_lang = array(
	'html_status'						=> '',
	'html_calstat_lastraid'				=> 'RCS - Last Raid',
	'html_calstat_raids_confirmed'		=> 'Attended',
	'html_calstat_raids_signedin'		=> 'Signedin',
	'html_calstat_raids_signedoff'		=> 'Signedoff',
	'html_calstat_raids_backup'			=> 'Backup',
);

$preset_lang = array(
	'raidattendees_status'				=> 'Calendar-Raid-Attendee Status',
	'raidcalstats_lastraid'				=> 'Calendar-Stats-Last Raid',
	'raidcalstats_raids_confirmed_90'	=> 'Calendar-Stats-Raids confirmed (90 days)',
	'raidcalstats_raids_signedin_90'	=> 'Calendar-Stats-Raids signedin (90 days)',
	'raidcalstats_raids_signedoff_90'	=> 'Calendar-Stats-Raids signedoff (90 days)',
	'raidcalstats_raids_backup_90'		=> 'Calendar-Stats-Raids backup (90 days)',
	'raidcalstats_raids_confirmed_60'	=> 'Calendar-Stats-Raids confirmed (60 days)',
	'raidcalstats_raids_signedin_60'	=> 'Calendar-Stats-Raids signedin (60 days)',
	'raidcalstats_raids_signedoff_60'	=> 'Calendar-Stats-Raids signedoff (60 days)',
	'raidcalstats_raids_backup_60'		=> 'Calendar-Stats-Raids backup (60 days)',
	'raidcalstats_raids_confirmed_30'	=> 'Calendar-Stats-Raids confirmed (30 days)',
	'raidcalstats_raids_signedin_30'	=> 'Calendar-Stats-Raids signedin (30 days)',
	'raidcalstats_raids_signedoff_30'	=> 'Calendar-Stats-Raids signedoff (30 days)',
	'raidcalstats_raids_backup_30'		=> 'Calendar-Stats-Raids backup (30 days)',
);
?>