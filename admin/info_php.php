<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2016 EQdkp-Plus Developer Team
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
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class php_info extends page_generic {

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

		// is required for the regey on that page
		if (version_compare(phpversion(), '7.0', ">=")){
			$intJitSetting = ini_get('pcre.jit');
			if($intJitSetting) {
				ini_set('pcre.jit', 0);
			}
		}

		preg_match_all("/<body>(.*)\<\/body>(.*)/ms", $pinfo, $output);

		$output = $output[1][0];
		$output = preg_replace('#<table[^>]+>#i', '<table>', $output);
		$output = str_replace('class="e"', 'class="row1"', $output);
		$output = str_replace('class="v"', 'class="row2"', $output);
		$output = str_replace('class="h"', '', $output);
		$output = str_replace('<hr />', '', $output);

		$this->tpl->assign_vars(array(
			'PHP_INFO'			=> $output,
		));

		$this->core->set_vars([
			'page_title'		=> 'PHP-Info',
			'template_file'		=> 'admin/info_php.html',
			'page_path'			=> [
				['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
				['title'=>$this->user->lang('adminc_server'), 'url'=>' '],
			],
			'display'			=> true
		]);
	}
}
registry::register('php_info');
