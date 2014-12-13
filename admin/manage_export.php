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

class Manage_Export extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('admin_index'=>'admin_index', 'xmltools' => 'xmltools');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public function __construct(){
		$this->user->check_auth('a_export_data');
		$handler = array(
			'ajax_export' => array('process' => 'ajax_export'),
		);
		parent::__construct(false, $handler);
		$this->process();
	}
	
	public function ajax_export(){
		include_once($this->root_path . 'core/data_export.class.php');
		$myexp = new content_export();
		$withMemberItems = $this->in->get('memberitems', 0);
		$withMemberAdjustments = $this->in->get('memberadjs', 0);
		$arrData = $myexp->export($withMemberItems, $withMemberAdjustments, false, false, true);
		header('content-type: text/html; charset=UTF-8');
		if ($this->in->get('format') == 'json'){
			echo $this->returnJSON($arrData);
		} elseif ($this->in->get('format') == 'lua'){
			echo $this->returnLua($arrData);
		} else {
			echo $this->returnXML($arrData);
		}
		exit();
	}

	public function display() {
		include_once($this->root_path . 'core/data_export.class.php');
		$myexp = new content_export();
		$arrData = $myexp->export(true, true, false,false, true);
		
		$this->tpl->assign_vars(array(
			'EXPORT_DATA' => $this->returnLua($arrData),
		));
				
		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('manexport_title'),
			'template_file'		=> 'admin/manage_export.html',
			'display'			=> true)
		);
	}
	
	private function returnJSON($arrData){
		if (!isset($arrData['status']) || $arrData['status'] != 0){
			$arrData['status'] = 1;
		}
		return json_encode($arrData);
	}
		
	private function returnXML($arrData){
		if (!is_array($arrData)){
			$arrData = $this->error('unknown error');
		}
		
		if (!isset($arrData['status']) || $arrData['status'] != 0){
				$arrData['status'] = 1;
		}
		
		$xml_array = $this->xmltools->array2simplexml($arrData, 'response');

		$dom = dom_import_simplexml($xml_array)->ownerDocument;
		$dom->encoding='utf-8';
		//$dom->formatOutput = true;
		$string = $dom->saveXML();
		return trim($string);
	}
	
	private function returnLua($arrData, $arrRequestArgs){
		if (!isset($arrData['status']) || $arrData['status'] != 0){
			$arrData['status'] = 1;
		}
		include_once($this->root_path."libraries/lua/parser.php");
		$luaParser = new LuaParser(false);
		return $luaParser->array2lua($arrData);
	}
}

registry::register('Manage_Export');
?>