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

include_once(registry::get_const('root_path').'core/html/html.aclass.php');
include_once(registry::get_const('root_path').'core/html/hhidden.class.php');

// this class acts as an alias for easier usability
/*
 * see hhidden for all available options
 */
class himageuploader extends hhidden {
	
	public $imageuploader = true;
	public $returnFormat = '';
	
	public function _inpval() {
		switch($this->returnFormat){
			case 'relative': return str_replace($this->environment->link, registry::get_const('root_path'), urldecode($this->in->get($this->name, '')));
			
			case 'in_data': return str_replace($this->pfh->FileLink('', 'files', 'absolute'), '', urldecode($this->in->get($this->name, '')));
			
			case 'filename': return  pathinfo(urldecode($this->in->get($this->name, '')), PATHINFO_BASENAME);
			
			case 'absolute':
			default: return urldecode($this->in->get($this->name, ''));
		}
	}
}
?>