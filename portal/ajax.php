<?php
/*	Project:	EQdkp-Plus
 *	Package:	Voice Portal Module
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

$eqdkp_root_path = './../../../../';
include_once($eqdkp_root_path.'common.php');

$moduleID = registry::register('input')->get('mid');

//Check Permission
$objPortal = register('portal');
if(!$objPortal->check_visibility($moduleID)) exit;

$blnWideContent = register('in')->get('wide', 0);
$strPosition = register('in')->get('pos', 0);

$objModule = $objPortal->get_module($moduleID, $strPosition, $blnWideContent);

if(method_exists($objModule, 'output_ajax')){
	$strContent = $objModule->output_ajax();
}else{
	$strContent = $objModule->output();
}

if(!headers_sent()) header('content-type: text/html; charset=UTF-8');
echo $strContent;
exit;
