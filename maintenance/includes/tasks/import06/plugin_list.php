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

$plugin_names	= array(
	'shoutbox'	=> array(
		'table'        => 'shoutbox_config',
		'fieldprefix'  => 'sb_',
		'extra_tables' => array('shoutbox', 'shoutbox_config'),
	),
	'bosssuite'	=> array(
		'table'        => 'bs_config',
		'fieldprefix'  => 'bb_',
	),
	'raidlogimport'	=> array(
		'table'        => 'raidlogimport_config',
		'fieldprefix'  => 'rli_',
		'extra_tables' => array('raidlogimport_bz', 'raidlogimport_config'),
	),
	'gallery'	=> array(
		'table'        => 'gallery_config',
		'fieldprefix'  => '',
	),
	'guildrequest'	=> array(
		'table'        => 'guildrequest_config',
		'fieldprefix'  => 'gr_',
	),
);
?>