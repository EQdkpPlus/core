<?php
/*
* Project:		EQdkp-Plus
* License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
* Link:			http://creativecommons.org/licenses/by-nc-sa/3.0/
* -----------------------------------------------------------------------
* Began:		2010
* Date:			$Date$
* -----------------------------------------------------------------------
* @author		$Author$
* @copyright	2006-2011 EQdkp-Plus Developer Team
* @link			http://eqdkp-plus.com
* @package		eqdkpplus
* @version		$Rev$
*
* $Id$
*/

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class php_info extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'core', 'config');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public function __construct(){
		$this->user->check_auth('a_');
		parent::__construct(false, false, 'plain', null, '_class_');
		$this->process();
	}

	public function display(){
		ob_start();
		phpinfo();
		$pinfo = ob_get_contents();
		ob_end_clean();
		$pinfo = trim($pinfo);

		preg_match_all('%^.*<body>(.*)</body>.*$%ms', $pinfo, $output);

		$output = $output[1][0];
		$output = preg_replace('#<table[^>]+>#i', '<table>', $output);
		$output = str_replace('class="e"', 'class="row1"', $output);
		$output = str_replace('class="v"', 'class="row2"', $output);
		$output = str_replace('class="h"', '', $output);
		$output = str_replace('<hr />', '', $output);

		$this->tpl->assign_vars(array(
			'PHP_INFO'			=> $output,
		));

		$this->core->set_vars(array(
			'page_title'		=> 'PHP-Info',
			'template_file'		=> 'admin/info_php.html',
			'display'			=> true
		));
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_php_info', php_info::__shortcuts());
registry::register('php_info');
?>