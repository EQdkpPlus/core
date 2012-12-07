<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2011
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

// ---------------------------------------------------------
// Set up environment
// ---------------------------------------------------------
//
define('EQDKP_INC', true);
$eqdkp_root_path = './../';

ini_set("display_errors", 0);
define('DEBUG', 99);

include_once($eqdkp_root_path.'core/super_registry.class.php');
if(!version_compare(phpversion(), '5.3.0', ">=")) {
	include_once($eqdkp_root_path.'core/registry.class.5.2.php');
	include_once($eqdkp_root_path.'core/gen_class.class.5.2.php');
} else {
	include_once($eqdkp_root_path.'core/registry.class.php');
	include_once($eqdkp_root_path.'core/gen_class.class.php');
}
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
if($dbtype = registry::get_const('dbtype')) {
	registry::$aliases['db'] = array('dbal_'.registry::get_const('dbtype'), array(array('open' => true)));
	include_once($eqdkp_root_path.'core/dbal/dbal.php');
	include_once($eqdkp_root_path.'core/dbal/'.$dbtype.'.php');
}
include_once($eqdkp_root_path . 'core/constants.php');
include_once($eqdkp_root_path . 'core/core.functions.php');
include_once($eqdkp_root_path . 'install/install.class.php');
registry::register('install')->init();

?>