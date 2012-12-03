<?php




/**
*
* soap_server allows the user to create a SOAP server
* that is capable of receiving messages and returning responses
*
* NOTE: WSDL functionality is experimental
*
* @author   Dietrich Ayala <dietrich@ganx4.com>
* @version  $Id: class.soap_server.php 6 2006-05-08 17:11:35Z tsigo $
* @access   public
*/
class soap_server extends nusoap_base {
	var $headers = array();			// HTTP headers of request
	var $request = '';				// HTTP request
	var $requestHeaders = '';		// SOAP headers from request (incomplete namespace resolution; special characters not escaped) (text)
	var $document = '';				// SOAP body request portion (incomplete namespace resolution; special characters not escaped) (text)
	var $requestSOAP = '';			// SOAP payload for request (text)
	var $methodURI = '';			// requested method namespace URI
	var $methodname = '';			// name of method requested
	var $methodparams = array();	// method parameters from request
	var $xml_encoding = '';			// character set encoding of incoming (request) messages
	var $SOAPAction = '';			// SOAP Action from request
    var $decode_utf8 = true;		// toggles whether the parser decodes element content w/ utf8_decode()

	var $outgoing_headers = array();// HTTP headers of response
	var $response = '';				// HTTP response
	var $responseHeaders = '';		// SOAP headers for response (text)
	var $responseSOAP = '';			// SOAP payload for response (text)
	var $methodreturn = false;		// method return to place in response
	var $methodreturnisliteralxml = false;	// whether $methodreturn is a string of literal XML
	var $fault = false;				// SOAP fault for response
	var $result = 'successful';		// text indication of result (for debugging)

	var $operations = array();		// assoc array of operations => opData
	var $wsdl = false;				// wsdl instance
	var $externalWSDLURL = false;	// URL for WSDL
	var $debug_flag = false;		// whether to append debug to response as XML comment


	/**
	* constructor
    * the optional parameter is a path to a WSDL file that you'd like to bind the server instance to.
	*
    * @param mixed $wsdl file path or URL (string), or wsdl instance (object)
	* @access   public
	*/
	function soap_server($wsdl=false){

		// turn on debugging?
		global $debug;
		global $_REQUEST;
		global $_SERVER;
		global $HTTP_SERVER_VARS;

		if (isset($debug)) {
			$this->debug_flag = $debug;
		} else if (isset($_REQUEST['debug'])) {
			$this->debug_flag = $_REQUEST['debug'];
		} else if (isset($_SERVER['QUERY_STRING'])) {
			$qs = explode('&', $_SERVER['QUERY_STRING']);
			foreach ($qs as $v) {
				if (substr($v, 0, 6) == 'debug=') {
					$this->debug_flag = substr($v, 6);
				}
			}
		} else if (isset($HTTP_SERVER_VARS['QUERY_STRING'])) {
			$qs = explode('&', $HTTP_SERVER_VARS['QUERY_STRING']);
			foreach ($qs as $v) {
				if (substr($v, 0, 6) == 'debug=') {
					$this->debug_flag = substr($v, 6);
				}
			}
		}

		// wsdl
		if($wsdl){
			if (is_object($wsdl) && is_a($wsdl, 'wsdl')) {
				$this->wsdl = $wsdl;
				$this->externalWSDLURL = $this->wsdl->wsdl;
				$this->debug('Use existing wsdl instance from ' . $this->externalWSDLURL);
			} else {
				$this->debug('Create wsdl from ' . $wsdl);
				$this->wsdl = new wsdl($wsdl);
				$this->externalWSDLURL = $wsdl;
			}
			$this->appendDebug($this->wsdl->getDebug());
			$this->wsdl->clearDebug();
			if($err = $this->wsdl->getError()){
				die('WSDL ERROR: '.$err);
			}
		}
	}

	/**
	* processes request and returns response
	*
	* @param    string $data usually is the value of $HTTP_RAW_POST_DATA
	* @access   public
	*/
	function service($data){
		global $QUERY_STRING;
		global $_SERVER;

		if(isset($_SERVER['QUERY_STRING'])){
			$qs = $_SERVER['QUERY_STRING'];
		} elseif(isset($GLOBALS['QUERY_STRING'])){
			$qs = $GLOBALS['QUERY_STRING'];
		} elseif(isset($QUERY_STRING) && $QUERY_STRING != ''){
			$qs = $QUERY_STRING;
		}

		if(isset($qs) && ereg('wsdl', $qs) ){
			// This is a request for WSDL
			if($this->externalWSDLURL){
              if (strpos($this->externalWSDLURL,"://")!==false) { // assume URL
				header('Location: '.$this->externalWSDLURL);
              } else { // assume file
                header("Content-Type: text/xml\r\n");
                $fp = fopen($this->externalWSDLURL, 'r');
                fpassthru($fp);
              }
			} elseif ($this->wsdl) {
				header("Content-Type: text/xml; charset=ISO-8859-1\r\n");
				print $this->wsdl->serialize($this->debug_flag);
			} else {
				header("Content-Type: text/html; charset=ISO-8859-1\r\n");
				print "This service does not provide WSDL";
			}
		} elseif ($data == '' && $this->wsdl) {
			// print web interface
			print $this->wsdl->webDescription();
		} else {
			// handle the request
			$this->parse_request($data);
			if (! $this->fault) {
				$this->invoke_method();
			}
			if (! $this->fault) {
				$this->serialize_return();
			}
			$this->send_response();
		}
	}

	/**
	* parses HTTP request headers.
	*
	* The following fields are set by this function (when successful)
	*
	* headers
	* request
	* xml_encoding
	* SOAPAction
	*
	* @access   private
	*/
	function parse_http_headers() {
		global $HTTP_SERVER_VARS;
		global $_SERVER;

		$this->request = '';
		if(function_exists('getallheaders')){
			$this->headers = getallheaders();
			foreach($this->headers as $k=>$v){
				$this->request .= "$k: $v\r\n";
				$this->debug("$k: $v");
			}
			// get SOAPAction header
			if(isset($this->headers['SOAPAction'])){
				$this->SOAPAction = str_replace('"','',$this->headers['SOAPAction']);
			}
			// get the character encoding of the incoming request
			if(isset($this->headers['Content-Type']) && strpos($this->headers['Content-Type'],'=')){
				$enc = str_replace('"','',substr(strstr($this->headers["Content-Type"],'='),1));
				if(eregi('^(ISO-8859-1|US-ASCII|UTF-8)$',$enc)){
					$this->xml_encoding = strtoupper($enc);
				} else {
					$this->xml_encoding = 'US-ASCII';
				}
			} else {
				// should be US-ASCII for HTTP 1.0 or ISO-8859-1 for HTTP 1.1
				$this->xml_encoding = 'ISO-8859-1';
			}
		} elseif(isset($_SERVER) && is_array($_SERVER)){
			foreach ($_SERVER as $k => $v) {
				if (substr($k, 0, 5) == 'HTTP_') {
					$k = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($k, 5)))));
				} else {
					$k = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $k))));
				}
				if ($k == 'Soapaction') {
					// get SOAPAction header
					$k = 'SOAPAction';
					$v = str_replace('"', '', $v);
					$v = str_replace('\\', '', $v);
					$this->SOAPAction = $v;
				} else if ($k == 'Content-Type') {
					// get the character encoding of the incoming request
					if (strpos($v, '=')) {
						$enc = substr(strstr($v, '='), 1);
						$enc = str_replace('"', '', $enc);
						$enc = str_replace('\\', '', $enc);
						if (eregi('^(ISO-8859-1|US-ASCII|UTF-8)$', $enc)) {
							$this->xml_encoding = strtoupper($enc);
						} else {
							$this->xml_encoding = 'US-ASCII';
						}
					} else {
						// should be US-ASCII for HTTP 1.0 or ISO-8859-1 for HTTP 1.1
						$this->xml_encoding = 'ISO-8859-1';
					}
				}
				$this->headers[$k] = $v;
				$this->request .= "$k: $v\r\n";
				$this->debug("$k: $v");
			}
		} elseif (is_array($HTTP_SERVER_VARS)) {
			foreach ($HTTP_SERVER_VARS as $k => $v) {
				if (substr($k, 0, 5) == 'HTTP_') {
					$k = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($k, 5)))));
					if ($k == 'Soapaction') {
						// get SOAPAction header
						$k = 'SOAPAction';
						$v = str_replace('"', '', $v);
						$v = str_replace('\\', '', $v);
						$this->SOAPAction = $v;
					} else if ($k == 'Content-Type') {
						// get the character encoding of the incoming request
						if (strpos($v, '=')) {
							$enc = substr(strstr($v, '='), 1);
							$enc = str_replace('"', '', $enc);
							$enc = str_replace('\\', '', $enc);
							if (eregi('^(ISO-8859-1|US-ASCII|UTF-8)$', $enc)) {
								$this->xml_encoding = strtoupper($enc);
							} else {
								$this->xml_encoding = 'US-ASCII';
							}
						} else {
							// should be US-ASCII for HTTP 1.0 or ISO-8859-1 for HTTP 1.1
							$this->xml_encoding = 'ISO-8859-1';
						}
					}
					$this->headers[$k] = $v;
					$this->request .= "$k: $v\r\n";
					$this->debug("$k: $v");
				}
			}
		}
	}

	/**
	* parses a request
	*
	* The following fields are set by this function (when successful)
	*
	* headers
	* request
	* xml_encoding
	* SOAPAction
	* request
	* requestSOAP
	* methodURI
	* methodname
	* methodparams
	* requestHeaders
	* document
	*
	* This sets the fault field on error
	*
	* @param    string $data XML string
	* @access   private
	*/
	function parse_request($data='') {
		$this->debug('entering parse_request()');
		$this->parse_http_headers();
		$this->debug('got character encoding: '.$this->xml_encoding);
		// uncompress if necessary
		if (isset($this->headers['Content-Encoding']) && $this->headers['Content-Encoding'] != '') {
			$this->debug('got content encoding: ' . $this->headers['Content-Encoding']);
			if ($this->headers['Content-Encoding'] == 'deflate' || $this->headers['Content-Encoding'] == 'gzip') {
		    	// if decoding works, use it. else assume data wasn't gzencoded
				if (function_exists('gzuncompress')) {
					if ($this->headers['Content-Encoding'] == 'deflate' && $degzdata = @gzuncompress($data)) {
						$data = $degzdata;
					} elseif ($this->headers['Content-Encoding'] == 'gzip' && $degzdata = gzinflate(substr($data, 10))) {
						$data = $degzdata;
					} else {
						$this->fault('Client', 'Errors occurred when trying to decode the data');
						return;
					}
				} else {
					$this->fault('Client', 'This Server does not support compressed data');
					return;
				}
			}
		}
		$this->request .= "\r\n".$data;
		$this->requestSOAP = $data;
		// parse response, get soap parser obj
		$parser = new soap_parser($data,$this->xml_encoding,'',$this->decode_utf8);
		// parser debug
		$this->debug("parser debug: \n".$parser->getDebug());
		// if fault occurred during message parsing
		if($err = $parser->getError()){
			$this->result = 'fault: error in msg parsing: '.$err;
			$this->fault('Client',"error in msg parsing:\n".$err);
		// else successfully parsed request into soapval object
		} else {
			// get/set methodname
			$this->methodURI = $parser->root_struct_namespace;
			$this->methodname = $parser->root_struct_name;
			$this->debug('methodname: '.$this->methodname.' methodURI: '.$this->methodURI);
			$this->debug('calling parser->get_response()');
			$this->methodparams = $parser->get_response();
			// get SOAP headers
			$this->requestHeaders = $parser->getHeaders();
            // add document for doclit support
            $this->document = $parser->document;
		}
		$this->debug('leaving parse_request');
	}

	/**
	* invokes a PHP function for the requested SOAP method
	*
	* The following fields are set by this function (when successful)
	*
	* methodreturn
	*
	* Note that the PHP function that is called may also set the following
	* fields to affect the response sent to the client
	*
	* responseHeaders
	* outgoing_headers
	*
	* This sets the fault field on error
	*
	* @access   private
	*/
	function invoke_method() {
		$this->debug('entering invoke_method methodname: ' . $this->methodname . ' methodURI: ' . $this->methodURI);
		// if a . is present in $this->methodname, we see if there is a class in scope,
		// which could be referred to. We will also distinguish between two deliminators,
		// to allow methods to be called a the class or an instance
		$class = '';
		$method = '';
		if (strpos($this->methodname, '..') > 0) {
			$delim = '..';
		} else if (strpos($this->methodname, '.') > 0) {
			$delim = '.';
		} else {
			$delim = '';
		}

		if (strlen($delim) > 0 && substr_count($this->methodname, $delim) == 1 &&
			class_exists(substr($this->methodname, 0, strpos($this->methodname, $delim)))) {
			// get the class and method name
			$class = substr($this->methodname, 0, strpos($this->methodname, $delim));
			$method = substr($this->methodname, strpos($this->methodname, $delim) + strlen($delim));
			$this->debug("class: $class method: $method delim: $delim");
		}

		// does method exist?
		if ($class == '' && !function_exists($this->methodname)) {
			$this->debug("function '$this->methodname' not found!");
			$this->result = 'fault: method not found';
			$this->fault('Client',"method '$this->methodname' not defined in service");
			return;
		}
		if ($class != '' && !in_array(strtolower($method), get_class_methods($class))) {
			$this->debug("method '$this->methodname' not found in class '$class'!");
			$this->result = 'fault: method not found';
			$this->fault('Client',"method '$this->methodname' not defined in service");
			return;
		}

		if($this->wsdl){
			if(!$this->opData = $this->wsdl->getOperationData($this->methodname)){
			//if(
				$this->fault('Client',"Operation '$this->methodname' is not defined in the WSDL for this service");
				return;
			}
			$this->debug('opData:');
			$this->appendDebug($this->varDump($this->opData));
		}
		$this->debug("method '$this->methodname' exists");
		// evaluate message, getting back parameters
		// verify that request parameters match the method's signature
		if(! $this->verify_method($this->methodname,$this->methodparams)){
			// debug
			$this->debug('ERROR: request not verified against method signature');
			$this->result = 'fault: request failed validation against method signature';
			// return fault
			$this->fault('Client',"Operation '$this->methodname' not defined in service.");
			return;
		}

		// if there are parameters to pass
		$this->debug('params:');
		$this->appendDebug($this->varDump($this->methodparams));
		$this->debug("calling '$this->methodname'");
		if (!function_exists('call_user_func_array')) {
			if ($class == '') {
				$this->debug('calling function using eval()');
				$funcCall = "\$this->methodreturn = $this->methodname(";
			} else {
				if ($delim == '..') {
					$this->debug('calling class method using eval()');
					$funcCall = "\$this->methodreturn = ".$class."::".$method."(";
				} else {
					$this->debug('calling instance method using eval()');
					// generate unique instance name
					$instname = "\$inst_".time();
					$funcCall = $instname." = new ".$class."(); ";
					$funcCall .= "\$this->methodreturn = ".$instname."->".$method."(";
				}
			}
			if ($this->methodparams) {
				foreach ($this->methodparams as $param) {
					if (is_array($param)) {
						$this->fault('Client', 'NuSOAP does not handle complexType parameters correctly when using eval; call_user_func_array must be available');
						return;
					}
					$funcCall .= "\"$param\",";
				}
				$funcCall = substr($funcCall, 0, -1);
			}
			$funcCall .= ');';
			$this->debug('function call: '.$funcCall);
			@eval($funcCall);
		} else {
			if ($class == '') {
				$this->debug('calling function using call_user_func_array()');
				$call_arg = "$this->methodname";	// straight assignment changes $this->methodname to lower case after call_user_func_array()
			} elseif ($delim == '..') {
				$this->debug('calling class method using call_user_func_array()');
				$call_arg = array ($class, $method);
			} else {
				$this->debug('calling instance method using call_user_func_array()');
				$instance = new $class ();
				$call_arg = array(&$instance, $method);
			}
			$this->methodreturn = call_user_func_array($call_arg, $this->methodparams);
		}
        $this->debug('methodreturn:');
        $this->appendDebug($this->varDump($this->methodreturn));
		$this->debug("leaving invoke_method: called method $this->methodname, received $this->methodreturn of type ".gettype($this->methodreturn));
	}

	/**
	* serializes the return value from a PHP function into a full SOAP Envelope
	*
	* The following fields are set by this function (when successful)
	*
	* responseSOAP
	*
	* This sets the fault field on error
	*
	* @access   private
	*/
	function serialize_return() {
		$this->debug('Entering serialize_return methodname: ' . $this->methodname . ' methodURI: ' . $this->methodURI);
		// if fault
		if (isset($this->methodreturn) && (get_class($this->methodreturn) == 'soap_fault')) {
			$this->debug('got a fault object from method');
			$this->fault = $this->methodreturn;
			return;
		} elseif ($this->methodreturnisliteralxml) {
			$return_val = $this->methodreturn;
		// returned value(s)
		} else {
			$this->debug('got a(n) '.gettype($this->methodreturn).' from method');
			$this->debug('serializing return value');
			if($this->wsdl){
				// weak attempt at supporting multiple output params
				if(sizeof($this->opData['output']['parts']) > 1){
			    	$opParams = $this->methodreturn;
			    } else {
			    	// TODO: is this really necessary?
			    	$opParams = array($this->methodreturn);
			    }
			    $return_val = $this->wsdl->serializeRPCParameters($this->methodname,'output',$opParams);
			    $this->appendDebug($this->wsdl->getDebug());
			    $this->wsdl->clearDebug();
				if($errstr = $this->wsdl->getError()){
					$this->debug('got wsdl error: '.$errstr);
					$this->fault('Server', 'unable to serialize result');
					return;
				}
			} else {
				if (isset($this->methodreturn)) {
					$return_val = $this->serialize_val($this->methodreturn, 'return');
				} else {
					$return_val = '';
					$this->debug('in absence of WSDL, assume void return for backward compatibility');
				}
			}
		}
		$this->debug('return value:');
		$this->appendDebug($this->varDump($return_val));

		$this->debug('serializing response');
		if ($this->wsdl) {
			$this->debug('have WSDL for serialization: style is ' . $this->opData['style']);
			if ($this->opData['style'] == 'rpc') {
				$this->debug('style is rpc for serialization: use is ' . $this->opData['output']['use']);
				if ($this->opData['output']['use'] == 'literal') {
					$payload = '<'.$this->methodname.'Response xmlns="'.$this->methodURI.'">'.$return_val.'</'.$this->methodname."Response>";
				} else {
					$payload = '<ns1:'.$this->methodname.'Response xmlns:ns1="'.$this->methodURI.'">'.$return_val.'</ns1:'.$this->methodname."Response>";
				}
			} else {
				$this->debug('style is not rpc for serialization: assume document');
				$payload = $return_val;
			}
		} else {
			$this->debug('do not have WSDL for serialization: assume rpc/encoded');
			$payload = '<ns1:'.$this->methodname.'Response xmlns:ns1="'.$this->methodURI.'">'.$return_val.'</ns1:'.$this->methodname."Response>";
		}
		$this->result = 'successful';
		if($this->wsdl){
			//if($this->debug_flag){
            	$this->appendDebug($this->wsdl->getDebug());
            //	}
			// Added: In case we use a WSDL, return a serialized env. WITH the usedNamespaces.
			$this->responseSOAP = $this->serializeEnvelope($payload,$this->responseHeaders,$this->wsdl->usedNamespaces,$this->opData['style']);
		} else {
			$this->responseSOAP = $this->serializeEnvelope($payload,$this->responseHeaders);
		}
		$this->debug("Leaving serialize_return");
	}

	/**
	* sends an HTTP response
	*
	* The following fields are set by this function (when successful)
	*
	* outgoing_headers
	* response
	*
	* @access   private
	*/
	function send_response() {
		$this->debug('Enter send_response');
		if ($this->fault) {
			$payload = $this->fault->serialize();
			$this->outgoing_headers[] = "HTTP/1.0 500 Internal Server Error";
			$this->outgoing_headers[] = "Status: 500 Internal Server Error";
		} else {
			$payload = $this->responseSOAP;
			// Some combinations of PHP+Web server allow the Status
			// to come through as a header.  Since OK is the default
			// just do nothing.
			// $this->outgoing_headers[] = "HTTP/1.0 200 OK";
			// $this->outgoing_headers[] = "Status: 200 OK";
		}
        // add debug data if in debug mode
		if(isset($this->debug_flag) && $this->debug_flag){
        	$payload .= $this->getDebugAsXMLComment();
        }
		$this->outgoing_headers[] = "Server: $this->title Server v$this->version";
		ereg('\$Revisio' . 'n: ([^ ]+)', $this->revision, $rev);
		$this->outgoing_headers[] = "X-SOAP-Server: $this->title/$this->version (".$rev[1].")";
		// Let the Web server decide about this
		//$this->outgoing_headers[] = "Connection: Close\r\n";
		$this->outgoing_headers[] = "Content-Type: text/xml; charset=$this->soap_defencoding";
		//begin code to compress payload - by John
		// NOTE: there is no way to know whether the Web server will also compress
		// this data.
		if (strlen($payload) > 1024 && isset($this->headers) && isset($this->headers['Accept-Encoding'])) {	
			if (strstr($this->headers['Accept-Encoding'], 'gzip')) {
				if (function_exists('gzencode')) {
					if (isset($this->debug_flag) && $this->debug_flag) {
						$payload .= "<!-- Content being gzipped -->";
					}
					$this->outgoing_headers[] = "Content-Encoding: gzip";
					$payload = gzencode($payload);
				} else {
					if (isset($this->debug_flag) && $this->debug_flag) {
						$payload .= "<!-- Content will not be gzipped: no gzencode -->";
					}
				}
			} elseif (strstr($this->headers['Accept-Encoding'], 'deflate')) {
				// Note: MSIE requires gzdeflate output (no Zlib header and checksum),
				// instead of gzcompress output,
				// which conflicts with HTTP 1.1 spec (http://www.w3.org/Protocols/rfc2616/rfc2616-sec3.html#sec3.5)
				if (function_exists('gzdeflate')) {
					if (isset($this->debug_flag) && $this->debug_flag) {
						$payload .= "<!-- Content being deflated -->";
					}
					$this->outgoing_headers[] = "Content-Encoding: deflate";
					$payload = gzdeflate($payload);
				} else {
					if (isset($this->debug_flag) && $this->debug_flag) {
						$payload .= "<!-- Content will not be deflated: no gzcompress -->";
					}
				}
			}
		}
		//end code
		$this->outgoing_headers[] = "Content-Length: ".strlen($payload);
		reset($this->outgoing_headers);
		foreach($this->outgoing_headers as $hdr){
			header($hdr, false);
		}
		print $payload;
		$this->response = join("\r\n",$this->outgoing_headers)."\r\n\r\n".$payload;
	}

	/**
	* takes the value that was created by parsing the request
	* and compares to the method's signature, if available.
	*
	* @param	mixed
	* @return	boolean
	* @access   private
	*/
	function verify_method($operation,$request){
		if(isset($this->wsdl) && is_object($this->wsdl)){
			if($this->wsdl->getOperationData($operation)){
				return true;
			}
	    } elseif(isset($this->operations[$operation])){
			return true;
		}
		return false;
	}

	/**
	* add a method to the dispatch map
	*
	* @param    string $methodname
	* @param    string $in array of input values
	* @param    string $out array of output values
	* @access   public
	*/
	function add_to_map($methodname,$in,$out){
			$this->operations[$methodname] = array('name' => $methodname,'in' => $in,'out' => $out);
	}

	/**
	* register a service with the server
	*
	* @param    string $methodname
	* @param    string $in assoc array of input values: key = param name, value = param type
	* @param    string $out assoc array of output values: key = param name, value = param type
	* @param	string $namespace
	* @param	string $soapaction
	* @param	string $style optional (rpc|document)
	* @param	string $use optional (encoded|literal)
	* @param	string $documentation optional Description to include in WSDL
	* @access   public
	*/
	function register($name,$in=array(),$out=array(),$namespace=false,$soapaction=false,$style=false,$use=false,$documentation=''){
		if($this->externalWSDLURL){
			die('You cannot bind to an external WSDL file, and register methods outside of it! Please choose either WSDL or no WSDL.');
		}
		if(false == $namespace) {
		}
		if(false == $soapaction) {
			$SERVER_NAME = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : $GLOBALS['SERVER_NAME'];
			$SCRIPT_NAME = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : $GLOBALS['SCRIPT_NAME'];
			$soapaction = "http://$SERVER_NAME$SCRIPT_NAME/$name";
		}
		if(false == $style) {
			$style = "rpc";
		}
		if(false == $use) {
			$use = "encoded";
		}
		
		$this->operations[$name] = array(
	    'name' => $name,
	    'in' => $in,
	    'out' => $out,
	    'namespace' => $namespace,
	    'soapaction' => $soapaction,
	    'style' => $style);
        if($this->wsdl){
        	$this->wsdl->addOperation($name,$in,$out,$namespace,$soapaction,$style,$use,$documentation);
	    }
		return true;
	}

	/**
	* create a fault. this also acts as a flag to the server that a fault has occured.
	*
	* @param	string faultcode
	* @param	string faultstring
	* @param	string faultactor
	* @param	string faultdetail
	* @access   public
	*/
	function fault($faultcode,$faultstring,$faultactor='',$faultdetail=''){
		$this->fault = new soap_fault($faultcode,$faultactor,$faultstring,$faultdetail);
		$this->fault->soap_defencoding = $this->soap_defencoding;
	}

    /**
    * sets up wsdl object
    * this acts as a flag to enable internal WSDL generation
    *
    * @param string $serviceName, name of the service
    * @param string $namespace optional tns namespace
    * @param string $endpoint optional URL of service endpoint
    * @param string $style optional (rpc|document) WSDL style (also specified by operation)
    * @param string $transport optional SOAP transport
    * @param string $schemaTargetNamespace optional targetNamespace for service schema
    */
    function configureWSDL($serviceName,$namespace = false,$endpoint = false,$style='rpc', $transport = 'http://schemas.xmlsoap.org/soap/http', $schemaTargetNamespace = false)
    {
		$SERVER_NAME = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : $GLOBALS['SERVER_NAME'];
		$SERVER_PORT = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : $GLOBALS['SERVER_PORT'];
		if ($SERVER_PORT == 80) {
			$SERVER_PORT = '';
		} else {
			$SERVER_PORT = ':' . $SERVER_PORT;
		}
		$SCRIPT_NAME = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : $GLOBALS['SCRIPT_NAME'];
        if(false == $namespace) {
            $namespace = "http://$SERVER_NAME/soap/$serviceName";
        }
        
        if(false == $endpoint) {
        	if (isset($_SERVER['HTTPS'])) {
        		$HTTPS = $_SERVER['HTTPS'];
        	} elseif (isset($GLOBALS['HTTPS'])) {
        		$HTTPS = $GLOBALS['HTTPS'];
        	} else {
        		$HTTPS = '0';
        	}
        	if ($HTTPS == '1' || $HTTPS == 'on') {
        		$SCHEME = 'https';
        	} else {
        		$SCHEME = 'http';
        	}
            $endpoint = "$SCHEME://$SERVER_NAME$SERVER_PORT$SCRIPT_NAME";
        }
        
        if(false == $schemaTargetNamespace) {
            $schemaTargetNamespace = $namespace;
        }
        
		$this->wsdl = new wsdl;
		$this->wsdl->serviceName = $serviceName;
        $this->wsdl->endpoint = $endpoint;
		$this->wsdl->namespaces['tns'] = $namespace;
		$this->wsdl->namespaces['soap'] = 'http://schemas.xmlsoap.org/wsdl/soap/';
		$this->wsdl->namespaces['wsdl'] = 'http://schemas.xmlsoap.org/wsdl/';
		if ($schemaTargetNamespace != $namespace) {
			$this->wsdl->namespaces['types'] = $schemaTargetNamespace;
		}
        $this->wsdl->schemas[$schemaTargetNamespace][0] = new xmlschema('', '', $this->wsdl->namespaces);
        $this->wsdl->schemas[$schemaTargetNamespace][0]->schemaTargetNamespace = $schemaTargetNamespace;
        $this->wsdl->schemas[$schemaTargetNamespace][0]->imports['http://schemas.xmlsoap.org/soap/encoding/'][0] = array('location' => '', 'loaded' => true);
        $this->wsdl->schemas[$schemaTargetNamespace][0]->imports['http://schemas.xmlsoap.org/wsdl/'][0] = array('location' => '', 'loaded' => true);
        $this->wsdl->bindings[$serviceName.'Binding'] = array(
        	'name'=>$serviceName.'Binding',
            'style'=>$style,
            'transport'=>$transport,
            'portType'=>$serviceName.'PortType');
        $this->wsdl->ports[$serviceName.'Port'] = array(
        	'binding'=>$serviceName.'Binding',
            'location'=>$endpoint,
            'bindingType'=>'http://schemas.xmlsoap.org/wsdl/soap/');
    }
}




?>