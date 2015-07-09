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

include_once(registry::get_const('root_path').'maintenance/includes/sql_update_task.class.php');

class update_2106 extends sql_update_task {
	public $author			= 'GodMod';
	public $version			= '2.1.0.6'; //new plus-version
	public $ext_version		= '2.1.0'; //new plus-version
	public $name			= '2.1.0 Update 7 Alpha 1';
	
	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_2106'	=> 'EQdkp Plus 2.1.0 Update 7',
					1 => 'Update for eqdkp_modern',
				),
			'german' => array(
				'update_2106'	=> 'EQdkp Plus 2.1.0 Update 7',
					1 => 'Update für eqdkp_modern',
			),
		);
		
		// init SQL querys
		$this->sqls = array(
			1	=> "UPDATE __styles SET misc_color1='rgb(78, 127, 168)', misc_color2='rgb(255, 255, 255)', additional_less='@styleCommentContainerBackgroundColor: #fff;
@styleCommentContainerBorderColor: #ccc;
@styleCommentAuthorColor: #9f9f9f;
@stylePaginationBorderColor: #ddd;
@stylePaginationBackgroundColor: #fff;
@stylePaginationActiveBackgroundColor: #F7F7F9;
@stylePaginationActiveColor: #999;
@stylePaginationActiveHoverBackgroundColor: #F7F7F9;
@stylePaginationActiveHoverColor: #000;
@styleArticleSitemapBorderColor: #ddd;
@styleArticleSitemapBackgroundColor: #fff;
@styleArticleSitemapActiveBackgroundColor: #F7F7F9;
@styleArticleSitemapActiveColor: #999999;
@styleArticleSitemapHoverColor: #000;
				' WHERE template_path='eqdkp_modern';",
		);
	}
}

?>