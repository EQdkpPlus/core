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

define('EQDKP_INC', true);
$eqdkp_root_path = './';
$lite = true;
include_once($eqdkp_root_path . 'common.php');

@set_time_limit(0);
@ignore_user_abort(true);
//single task
if(registry::register('input')->get('task') != ""){
	$force_run = (registry::register('input')->get('force') == 'true' && register('user')->check_auth('a_config_man', false)) ? true : false;
	registry::register('timekeeper')->execute_cron(registry::register('input')->get('task'), $force_run);
}else{
	//all cronjobs with flag external
	registry::register('timekeeper')->handle_crons(true);
}
?>