<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2010
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2006-2010 EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev$
 * 
 * $Id$
 */

if( !defined( 'EQDKP_INC' ) ) {
	die( 'Do not access this file directly.' );
}

if( !class_exists( "plus_exchange" ) ) {
	class plus_exchange {
		
		//module lists
		private $initialized_modules = array( );
		public 	$modules = array();
		public 	$feeds = array();
		private $modulepath = 'core/exchange/';

		//Constructor
		function __construct( ) {
			global $pdl, $core, $eqdkp_root_path;

			$this->init();
			
		}
		
		public function init(){
				$this->scan_modules();	
		}
		
		
		function register_module($module_name, $module_dir){
			global $eqdkp_root_path;
			//create object
			$module = 'exchange_'.$module_name;
			include( $eqdkp_root_path.$module_dir.'.php' );
			$class = new $module;
			if ($class->type){
				$this->modules[$class->type][$module_name]['path'] =  $module_dir;
				$this->modules[$class->type][$module_name]['options'] = $class->options;
			}
		}
		
		function register_feed($feed_name, $feed_url, $plugin_code = 'eqdkp'){
			global $eqdkp_root_path;
			
			$this->feeds[$feed_name] =  array('url'	=> $feed_url, 'plugin' => $plugin_code);
		}
		
		function create_wsdl(){
			global $core;
			
			$message = '';
			$port = '';
			$bindings = '';
			
			$out = "<?xml version ='1.0' encoding ='UTF-8' ?> 
<definitions name='TestServer' 
  xmlns:tns='".$core->BuildLink()."exchange.php?out=wsdl' 
  xmlns:soap='http://schemas.xmlsoap.org/wsdl/soap/' 
  xmlns:xsd='http://www.w3.org/2001/XMLSchema' 
  xmlns:soapenc='http://schemas.xmlsoap.org/soap/encoding/' 
  xmlns:wsdl='http://schemas.xmlsoap.org/wsdl/' 
  xmlns='http://schemas.xmlsoap.org/wsdl/'>";
	
			foreach ($this->modules['SOAP'] as $key => $value){
				$message .= "<message name='".$key."Request'> ";
				foreach ($value['options']['input'] as $name=>$type){
					$message .= "<part name='".$name."' type='xsd:".$type."'/>";
				}
				$message .= "</message>";
				$message .= "<message name='".$key."Response'> ";
				foreach ($value['options']['output'] as $name=>$type){
					$message .= "<part name='".$name."' type='xsd:".$type."'/>";
				}
				$message .= "</message>";	
				
				$port .= "  <operation name='".$key."'> 
    <input message='tns:".$key."Request'/> 
    <output message='tns:".$key."Response'/> 
  </operation>";
	
			$bindings .= "<operation name='".$key."'> 
    <soap:operation soapAction='urn:xmethodsTestServer#".$key."'/> 
    <input> 
      <soap:body use='encoded' namespace='urn:xmethodsTestServer' encodingStyle='http://schemas.xmlsoap.org/soap/encoding/'/> 
    </input> 
    <output> 
      <soap:body use='encoded' namespace='urn:xmethodsTestServer' encodingStyle='http://schemas.xmlsoap.org/soap/encoding/'/> 
    </output> 
  </operation>";
					
			}
			
			$out .= $message;
			
			$out .= "<portType name='TestServerPortType'>".$port."</portType>";
			
			$out .= "<binding name='TestServerBinding' type='tns:TestServerPortType'> 
  <soap:binding style='rpc' transport='http://schemas.xmlsoap.org/soap/http'/> 
  ".$bindings."
</binding> ";
			
			$out .= "<service name='TestServerService'> 
  <port name='TestServerPort' binding='TestServerBinding'> 
    <soap:address location='".$core->BuildLink()."exchange.php?out=soap'/> 
  </port> 
</service> 
</definitions>";
		return $out;
		}

		private function scan_modules( ) {

			global $eqdkp_root_path;
			$m_path = $eqdkp_root_path.$this->modulepath;
			
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
		}
		
		public function execute_rest(){
			global $eqdkp_root_path;
			//Get all Arguments
			$request_url = $_SERVER['REQUEST_URI'];
			$request_method = $_SERVER['REQUEST_METHOD'];
			
			$request_args['get'] = $_GET;
			$request_args['post'] = $_POST;
			$request_body = file_get_contents("php://input");
			parse_str($request_body, $request_args['put']);
			parse_str($request_body, $request_args['delete']);

			$function = $request_args['get']['function'];
			
			if(isset($this->modules['REST'][$function])){

				include ($eqdkp_root_path.$this->modules['REST'][$function]['path'].'.php');
				 $module = 'exchange_'.$function;
				 $class = new $module;
				 $method = strtolower($request_method).'_'.$function;
				 
				 if (method_exists($class, $method)){
					return $class->$method($request_args, $request_body);
				 }
			}

			return '<response><error>Function not available</error></response>';
		}
		
	}
	//end class
}
//end if


if( !class_exists( "plus_exchange_exec" ) ) {
	class plus_exchange_exec {
		var $modules = array();
		var $functions;
		
		function __construct(){
				global $pex;
				$this->modules = $pex->modules['SOAP'];
		}
		
		public function __call($name, $params){
			 global $eqdkp_root_path;
			 
			 if(isset($this->modules[$name])){
				 
				 include ($eqdkp_root_path.$this->modules[$name]['path'].'.php');
				 $module = 'exchange_'.$name;
				 $class = new $module;
				 $this->functions[$name] = array($class, $name);
				 return call_user_func_array($this->functions[$name], $params);

			}
		}
	
}}




?>