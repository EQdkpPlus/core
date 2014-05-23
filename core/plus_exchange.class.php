<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2010
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

if( !defined( 'EQDKP_INC' ) ) {
	die( 'Do not access this file directly.' );
}

if( !class_exists( "plus_exchange" ) ) {
	class plus_exchange extends gen_class {
		public static $shortcuts = array('xmltools' => 'xmltools', 'pm', 'portal', 'pdh');

		//module lists
		private $initialized_modules	= array();
		public $modules					= array();
		public $feeds					= array();
		private $modulepath				= 'core/exchange/';

		//Constructor
		public function __construct( ) {
			$this->scan_modules();
		}

		public function register_module($module_name, $module_dir){
			//create object
			$module = 'exchange_'.$module_name;
			if (!is_file($this->root_path.$module_dir.'.php')) return false;
			include($this->root_path.$module_dir.'.php');
			$class = register($module);
			$this->modules[$module_name] = array(
				'path'		=> $module_dir				
			);
			return true;
		}

		public function register_feed($feed_name, $feed_url, $plugin_code = 'eqdkp'){
			$this->feeds[$feed_name] =  array('url'	=> $feed_url, 'plugin' => $plugin_code);
		}


		private function scan_modules(){
			$m_path = $this->root_path.$this->modulepath;

			//Scan "local" modules
			$dh = opendir( $m_path );
			if ($dh){
				while( false !== ( $file = readdir( $dh ) ) ) {
					if( $file != '.' && $file != '..' && $file != '.svn' && !is_dir($file)) {
						$path_parts = pathinfo($file);
						$this->register_module( $path_parts['filename'], $this->modulepath.$path_parts['filename'] );
					}
				}
			}
			//Plugins
			$plugs = $this->pm->get_plugins(PLUGIN_INSTALLED);
			if (is_array($plugs)){
				foreach($plugs as $plugin_code) {
					$ems = $this->pm->get_plugin($plugin_code)->get_exchange_modules();
					foreach($ems as $module_name) {
						$module_dir = 'plugins/'.$plugin_code.'/exchange/'.$module_name;
						$this->register_module($module_name, $module_dir);
					}
					$efs = $this->pm->get_plugin($plugin_code)->get_exchange_modules(true);
					foreach($efs as $module) {
						$this->register_feed($module['name'], $module['url'], $plugin_code);
					}
				}
			}
			//Portal modules
			$enabled_modules = $this->pdh->aget('portal', 'position', 0, array($this->pdh->get('portal', 'id_list', array(array('enabled' => 1)))));
			if(is_array($enabled_modules)){
				foreach($enabled_modules as $module_id => $posi) {
					$path = $this->pdh->get('portal', 'path', array($module_id));
					$plugin = $this->pdh->get('portal', 'plugin', array($module_id));
					$obj = $this->portal->get_module($path, $plugin);
					
					if ($obj && $this->portal->check_visibility($module_id)){
						$arrExchangeModules = $obj->get_exchangeModules();
						foreach ($arrExchangeModules as $module_name){
							if ($plugin != ''){
								$module_dir = 'plugins/'.$plugin.'/portal/exchange/'.$module_name;
							} else {
								$module_dir = 'portal/'.$path.'/exchange/'.$module_name;
							}
							$this->register_module($module_name, $module_dir);
						}
					}
				}
			}
		}

		public function execute(){
			//Get all Arguments
			$request_url = $_SERVER['REQUEST_URI'];
			$request_method = $_SERVER['REQUEST_METHOD'];

			$request_args['get'] = $_GET;
			$request_args['post'] = $_POST;
			$request_body = file_get_contents("php://input");
			parse_str($request_body, $request_args['put']);
			parse_str($request_body, $request_args['delete']);

			$function = $request_args['get']['function'];

			if(isset($this->modules[$function])){

				include ($this->root_path.$this->modules[$function]['path'].'.php');
				$module = 'exchange_'.$function;
				$class = register($module);
				$method = strtolower($request_method).'_'.$function;

				if (method_exists($class, $method)){
					$out = $class->$method($request_args, $request_body);
				} else {
					$out = $this->error('function not found');
				}
			} else {
				$out = $this->error('function not found');
			}
			
			if ($request_args['get']['format'] == 'json'){
				return $this->returnJSON($out);
			} elseif ($request_args['get']['format'] == 'lua'){
				return $this->returnLua($out, $request_args);
			} else {
				return $this->returnXML($out);
			}

		}
		
		public function error($strErrorMessage){
			return array(
				'status'	=> 0,
				'error'		=> $strErrorMessage,
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
			$dom->formatOutput = true;
			$string = $dom->saveXML();
			return trim($string);
		}
		
		private function returnLua($arrData, $arrRequestArgs){
			if (!isset($arrData['status']) || $arrData['status'] != 0){
				$arrData['status'] = 1;
			}
			include_once($this->root_path."libraries/lua/parser.php");
			$luaParser = new LuaParser((isset($arrRequestArgs['get']['one_table']) && $arrRequestArgs['get']['one_table'] == "false") ? false : true);
			return $luaParser->array2lua($arrData);
		}
	}//end class
} //end if



if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_plus_exchange', plus_exchange::$shortcuts);
?>