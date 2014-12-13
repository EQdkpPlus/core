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

//tbody not allowed withoud thead, 

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class Manage_Media extends page_generic {
	public function __construct(){
		$this->user->check_auth('a_files_man');
		$handler = array();
		parent::__construct(false, $handler);
		$this->process();
	}

	public function display(){
		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('manage_media'),
			'template_file'		=> 'admin/manage_media.html',
			'display'			=> true)
		);
	}
}
registry::register('Manage_Media');
?>