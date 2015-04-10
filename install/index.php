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

// ---------------------------------------------------------
// Set up environment
// ---------------------------------------------------------
//
define('EQDKP_INC', true);
$eqdkp_root_path = './../';

ini_set("display_errors", 0);
define('DEBUG', 99);
define('INSTALLER', true);

@set_time_limit(0);
@ignore_user_abort(true);

define('ANONYMOUS',	-1);
include_once($eqdkp_root_path.'core/super_registry.class.php');
include_once($eqdkp_root_path.'core/registry.class.php');
include_once($eqdkp_root_path.'core/gen_class.class.php');
	
registry::add_const('root_path', $eqdkp_root_path);
registry::add_const('lite_mode', true);
// switch to userdefined error-handling
registry::$aliases['pdl'] = array('plus_debug_logger', array(false, false));
registry::$aliases['user'] = 'auth_db';
$pdl = registry::register('plus_debug_logger', array(false, false));
set_error_handler(array($pdl,'myErrorHandler'));
register_shutdown_function(array($pdl, "catch_fatals"));
$pdl->set_debug_level(DEBUG); //to prevent errors on further adding of debug-levels
unset($pdl);

registry::load_config(true);


//New DBAL
if($dbtype = registry::get_const('dbtype')) {
	include_once(registry::get_const('root_path') .'libraries/dbal/dbal.class.php');
	require_once(registry::get_const('root_path') . 'libraries/dbal/' . registry::get_const('dbtype') . '.dbal.class.php');
	registry::$aliases['db'] = array('dbal_'.registry::get_const('dbtype'), array(array('open' => true)));		
}

include_once($eqdkp_root_path . 'core/constants.php');
include_once($eqdkp_root_path . 'core/core.functions.php');
include_once($eqdkp_root_path . 'install/install.class.php');
registry::load_html_fields();
registry::register('install')->init();

?>