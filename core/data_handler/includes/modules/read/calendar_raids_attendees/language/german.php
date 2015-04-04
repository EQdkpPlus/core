<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
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

if ( !defined('EQDKP_INC') ){
    header('HTTP/1.0 404 Not Found');exit;
}

$module_lang = array(
	'html_status'						=> '',
	'html_calstat_lastraid'				=> 'Letzter Raid',
	'html_calstat_raids_confirmed'		=> 'Teilgenommen',
	'html_calstat_raids_signedin'		=> 'Angemeldet',
	'html_calstat_raids_signedoff'		=> 'Abgemeldet',
	'html_calstat_raids_backup'			=> 'Ersatzbank',
	'html_calstat_raids_confirmed_fromto' => 'Teilgenommen',
	'html_calstat_raids_signedin_fromto'	=> 'Angemeldet',
	'html_calstat_raids_signedoff_fromto'	=> 'Abgemeldet',
	'html_calstat_raids_backup_fromto'		=> 'Ersatzbank'
);

$preset_lang = array(
	'raidattendees_status'				=> 'Kalender-Raidteilnehmer Status',
	'raidcalstats_lastraid'				=> 'Kalender-Statsitik-Letzter Raid',
	'raidcalstats_raids_confirmed_90'	=> 'Kalender-Statsitik-Bestätigte Raids (90 Tage)',
	'raidcalstats_raids_signedin_90'	=> 'Kalender-Statsitik-Angemeldete Raids (90 Tage)',
	'raidcalstats_raids_signedoff_90'	=> 'Kalender-Statsitik-Abgemeldete Raids (90 Tage)',
	'raidcalstats_raids_backup_90'		=> 'Kalender-Statsitik-Ersatzbank Raids (90 Tage)',
	'raidcalstats_raids_confirmed_60'	=> 'Kalender-Statsitik-Bestätigte Raids (60 Tage)',
	'raidcalstats_raids_signedin_60'	=> 'Kalender-Statsitik-Angemeldete Raids (60 Tage)',
	'raidcalstats_raids_signedoff_60'	=> 'Kalender-Statsitik-Abgemeldete Raids (60 Tage)',
	'raidcalstats_raids_backup_60'		=> 'Kalender-Statsitik-Ersatzbank Raids (60 Tage)',
	'raidcalstats_raids_confirmed_30'	=> 'Kalender-Statsitik-Bestätigte Raids (30 Tage)',
	'raidcalstats_raids_signedin_30'	=> 'Kalender-Statsitik-Angemeldete Raids (30 Tage)',
	'raidcalstats_raids_signedoff_30'	=> 'Kalender-Statsitik-Abgemeldete Raids (30 Tage)',
	'raidcalstats_raids_backup_30'		=> 'Kalender-Statsitik-Ersatzbank Raids (30 Tage)',
	'raidcalstats_raids_confirmed_fromto'	=> 'Kalender-Statsitik-Bestätigte Raids (Def. Zeitraum)',
	'raidcalstats_raids_signedin_fromto'	=> 'Kalender-Statsitik-Angemeldete Raids (Def. Zeitraum)',
	'raidcalstats_raids_signedoff_fromto'	=> 'Kalender-Statsitik-Abgemeldete Raids (Def. Zeitraum)',
	'raidcalstats_raids_backup_fromto'		=> 'Kalender-Statsitik-Ersatzbank Raids (Def. Zeitraum)',
);
?>