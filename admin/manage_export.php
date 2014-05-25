<?php
/*
* Project:		EQdkp-Plus
* License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
* Link:			http://creativecommons.org/licenses/by-nc-sa/3.0/
* -----------------------------------------------------------------------
* Began:		2009
* Date:			$Date: 2013-05-23 22:19:25 +0200 (Do, 23 Mai 2013) $
* -----------------------------------------------------------------------
* @author		$Author: godmod $
* @copyright	2006-2011 EQdkp-Plus Developer Team
* @link			http://eqdkp-plus.com
* @package		eqdkpplus
* @version		$Rev: 13378 $
*
* $Id: manage_menus.php 13378 2013-05-23 20:19:25Z godmod $
*/

//tbody not allowed withoud thead, 

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class Manage_Export extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'pdh', 'jquery', 'core', 'config', 'html', 'admin_index'=>'admin_index', 'xmltools' => 'xmltools');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public function __construct(){
		$this->user->check_auth('a_');
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
		$arrData = $myexp->export($withMemberItems, $withMemberAdjustments, true);
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
		$arrData = $myexp->export(true, true, true);
		
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
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_Manage_Export', Manage_Export::__shortcuts());
registry::register('Manage_Export');
?>