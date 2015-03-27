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

class wrapper_pageobject extends pageobject {

	private $data = false;

	public function __construct() {
		$handler = array(
			'id'	=> array('process'	=> 'handle_id'),
		);
		parent::__construct(false, $handler, array());
		$this->process();
	}

	private function handle_id($id, $url){
		$linkID = $id;

		//Plus-Link
		if((int)$linkID > 0){
			$urldata	= $this->pdh->get('links', 'data', array((int)$linkID));
			if ($urldata){
				$this->data = array(
					'url'	=> $urldata['url'],
					'title'	=> $urldata['name'],
					'window'=> (int)$urldata['window'],
					'height'=> $urldata['height'],
				);
			}
		}

		//Board
		if ((strtolower($linkID) == 'board') and (strlen($this->config->get('cmsbridge_url')) > 0)){
			$this->data = array(
				'url'	=> $this->config->get('cmsbridge_url'),
				'title'	=> $this->user->lang('forum'),
				'window'=> (int)$this->config->get('cmsbridge_embedded'),
				'height'=> '4024',
			);
		}

		//Register
		if((strtolower($linkID) == 'boardregister') and (strlen($this->config->get('cmsbridge_reg_url')) > 0)){
			$this->data = array(
				'url'	=> $this->config->get('cmsbridge_reg_url'),
				'title'	=> $this->user->lang('forum'),
				'window'=> (int)$this->config->get('cmsbridge_embedded'),
				'height'=> '4024',
			);
		}
		
		//Register
		if((strtolower($linkID) == 'lostpassword') and (strlen($this->config->get('cmsbridge_pwreset_url')) > 0)){
			$this->data = array(
				'url'	=> $this->config->get('cmsbridge_pwreset_url'),
				'title'	=> $this->user->lang('forum'),
				'window'=> (int)$this->config->get('cmsbridge_embedded'),
				'height'=> '4024',
			);
		}
		
		//Hooks
		$arrHooks = $this->hooks->process('wrapper', array('id'=>$linkID, 'link'=>rawurldecode($url)));

		if (count($arrHooks) > 0){
			foreach($arrHooks as $arrHook){
				//If wrapper id is not for hook, continue;
				if (!$arrHook) continue;
				//If wrapper id equals hook id
				if ($arrHook['id'] == $linkID){
					$arrData = $arrHook['data'];
					//If URL should be verified, do this
					if (isset($arrData['verify']) && strlen($arrData['verify']) && !$this->VerifyLink($arrData['url'], $arrData['verify'])) break;
					$this->data = $arrData;
					break;
				}
			}
		}
	}

	public function display(){
		$this->handle_id($this->url_id, $this->speaking_name);
		
		if (!$this->data || $this->data['url'] == ''){
			message_die('URL not found');
		} else {
			$this->data['base_url'] = $this->data['url'];
			//Direkt link to a page in the wrapper
			if (strlen($this->in->get('p'))){
				$arrReplace = array(':', '\\', '&#58');
				$direktLink = urldecode($this->in->get('p'));
				$direktLink = str_replace($arrReplace, "", $direktLink);
				$arrParts = parse_url($direktLink);
				$direktLink = $arrParts['path'];
				if (isset($arrParts['query'])) $direktLink .= '?'.$arrParts['query'];
				if (!filter_var($direktLink, FILTER_VALIDATE_URL)){
					$this->data['url'] = $this->data['url'].$direktLink;
				}		
			}
			$output = '<div id="wrapper">';
			$this->CreateDynamicIframeJS();
			$output .='<!--[IF IE]>
							<iframe id="boardframe" src="'.$this->data['url'].'" data-base-url="'.$this->data['base_url'].'" allowtransparency="true" width="100%" height="'.$this->data['height'].'" scrolling="no" marginwidth="0" marginheight="0" frameborder="0" vspace="0" hspace="0"></iframe>
						<![if ! IE]><!-->
							<iframe id="boardframe" src="'.$this->data['url'].'" data-base-url="'.$this->data['base_url'].'" width="100%" scrolling="no" marginwidth="0" marginheight="0" height="'.$this->data['height'].'" frameborder="0" vspace="0" hspace="0"></iframe>
						<!--><![ENDIF]><![ENDIF]-->';
	

			$output .= '</div>';

			$this->tpl->assign_vars(array(
				'BOARD_OUPUT' => $output,
			));
		}

		//switch page_body
		switch($this->data['window']){
			case '4': $page_body = "full_width";
			break;
			case '5': $page_body = "full";
			break;
			default: $page_body = '';
		}
		
		$this->core->set_vars(array(
			'page_title'		=> isset($this->data['title']) ? $this->data['title'] : 'Wrapper',
			'page_body'			=> $page_body,
			'template_file'		=> 'wrapper.html',
			'display'			=> true)
		);

	}


	/**
	 * Check a link with a whitelist....
	 *
	 * @param     string     $strUrl          URL to be checked
	 * @param     bool       $arrWhitelist    Array with allowed domain names, p.e. array('localhost','kompsoft.de');
	 * @param     string     $strProtocol     URL Protocol, default is http, might be ftp, http, https....
	 * @return    bool                   	  Return true if URL is valid
	 */
	function VerifyLink($strUrl, $arrWhitelist, $strProtocol = ''){

		//Parse the Domain...
		$arrUrlData = parse_url($strUrl);

		//Check Protocol
		if ($strProtocol != ''){
			if($arrUrlData['scheme'] != $strProtocol){
				return false;
			}
		} else {
			//Check also against https!
			if($arrUrlData['scheme'] != 'http' && $arrUrlData['scheme'] != 'https'){
				return false;
			}
		}

		//Add the Server path to the whitelist
		if (is_array($arrWhitelist)){
			array_push($arrWhitelist, $this->env->server_name);
		} else {
			$arrWhitelist = array($this->env->server_name);
		}

		// Check if the Server matches the whitelist...
		if(!in_array($arrUrlData['host'], $arrWhitelist)){
			return false;
		}

		// all ok, return true
		return true;
	}

	private function CreateDynamicIframeJS(){
		$out = '
			//ID of iFrame
			var iframeid = "boardframe";
			var wrapper_url = "'.$this->data['url'].'";
			var wrapper_base_url = "'.$this->data['base_url'].'";
			var wrapper_eqdkp_url = "'.$this->env->buildlink().'";

			function onloadIframeAdjustments(){
				resizeIframe();
				scrollToPosition();
				registerInnerIframeEvents();
				setURL();
				$("#"+iframeid).data("message", "false");
				sendMessagesToIframe();
			}
			
			function perodicIframeAdjustment(){
				resizeIframe();
			}
					
					
			//Resizes the Iframe		
			function resizeIframe(height, width){					
				var currentfr = document.getElementById(iframeid);
	
				if (currentfr){
					try {
						currentfr.style.display = "block";
						if(height == undefined){
							if (currentfr.contentDocument && currentfr.contentDocument.body && currentfr.contentDocument.body.offsetHeight){ //ns6 syntax
								currentfr.height = currentfr.contentDocument.body.offsetHeight;
							} else if (currentfr.Document && currentfr.Document.body && currentfr.Document.body.scrollHeight) {//ie5+ syntax
								currentfr.height = currentfr.Document.body.scrollHeight;
							}
						} else {
							currentfr.height = height;
						}
					
						//Set correct width
						if(width == undefined){
							if (currentfr.contentDocument && currentfr.contentDocument.body && currentfr.contentDocument.body.scrollWidth) {//ie5+ syntax
								var scrollwidth = currentfr.contentDocument.body.scrollWidth;
								var myscrollwidth = currentfr.scrollWidth;
		
								if (scrollwidth >  myscrollwidth+5){
									currentfr.width = scrollwidth;
								}	
							}
						} else {
							var myscrollwidth = currentfr.scrollWidth;
		
							if (width >  myscrollwidth+5){
								currentfr.width = width;
							}
						}

					} catch (e) {
						sendMessagesToIframe();
						
						if($("#"+iframeid).data("message") == "false") currentfr.height = 4000;
					}	
				}
			}

			function scrollToPosition(){
				try {
					var currentfr = document.getElementById(iframeid);
					var hash = "";
					if (currentfr.contentDocument) {
						hash = currentfr.contentDocument.location.hash;
					} else if (currentfr.Document){
						hash = currentfr.Document.location.hash;
					}
	
					if (hash && hash!= ""){
						hash = hash.substring(1);
	
						var el = false;
	
						if (currentfr.contentDocument) {
							el = currentfr.contentDocument.getElementById(hash);
							if (!el){
								el = currentfr.contentDocument.getElementsByName(hash)[0];
							}
						} else if (currentfr.Document){
							el = currentfr.Document.getElementById(hash);
							if (!el){
								el = currentfr.Document.getElementsByName(hash)[0];
							}
						}
	
						if (el){
							var elpos = findPos(el)[1];
							var framepos = findPos(currentfr)[1];
							scrollTo(0,elpos + framepos);
						} else {
							scrollTo(0,0);
						}
	
					} else {
						scrollTo(0,0);
					}
					
				} catch (e) {
					console.log(e);
				}			
			}

			function findPos(obj) {
				var curleft = curtop = 0;
					if (obj.offsetParent) {
						do {
						curleft += obj.offsetLeft;
						curtop += obj.offsetTop;
					} while (obj = obj.offsetParent);
				}
				return [curleft,curtop];
			}
				
			function setURL(){
				try {
					var currentfr = document.getElementById(iframeid);
					var location = "";
					if (currentfr.contentDocument) {
						location = currentfr.contentDocument.location.href;
					} else if (currentfr.Document){
						location = currentfr.Document.location.href;
					}
					
					if(location != ""){
						var baseurl = $("#"+iframeid).data("base-url");
						var myurl = window.location.search;
						var param = location.replace(baseurl, "");
						
						param = escape(param);
						
						if( param == "" || param.indexOf("http") == 0 || param.indexOf("sftp") == 0) {
							var newurl = updateQueryStringParameter(myurl, "p", "");
							history.pushState(null, document.title, newurl);
							return;
						}
						var newurl = updateQueryStringParameter(myurl, "p", param);
						history.pushState(null, document.title, newurl);
					}
	
				} catch (e) {
					console.log(e);
				}		
			}
				
			function updateQueryStringParameter(uri, key, value) {
			  var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
			  var separator = uri.indexOf("?") !== -1 ? "&" : "?";
			  if (uri.match(re)) {
			    return uri.replace(re, "$1" + key + "=" + value + "$2");
			  }
			  else {
			    return uri + separator + key + "=" + value;
			  }
			}
					
			function check_base_url(url){
				var parser = document.createElement("a");
				parser.href = url;
						
				var eqdkp_parser = document.createElement("a");
				eqdkp_parser.href = wrapper_base_url;

				console.log(parser.href);
				console.log(eqdkp_parser.href);
					
				if(parser.hostname == eqdkp_parser.hostname){
					if(parser.pathname.lastIndexOf(eqdkp_parser.pathname, 0) === 0){
						return true;
					}
				}
						
				return false;
			}
					
			function beforechangeEventhandler(e){
				console.log(e);
			}
					
			function iframeonclickEventhandler(e){
				console.log(e);
					
				var elemntTagName = e.target.tagName;
					
			    if(elemntTagName==\'A\')
			    {
			        var newurl = e.target.getAttribute("href");
					console.log(newurl);
					console.log(check_base_url(newurl));
					
					if(!check_base_url(newurl) && newurl.lastIndexOf("http", 0) === 0){
						e.preventDefault();
						window.location.href = newurl;
					}
					
			    }
			}
					
			function registerInnerIframeEvents(){
				try {
					console.log("registerInnerFrameEvents");
					var currentfr = document.getElementById(iframeid);
					if (currentfr){
						var iframeDoc = currentfr.contentDocument || currentfr.contentWindow.document;
						var iframeWin = currentfr.contentWindow || currentfr.contentDocument;

						/*
						if (typeof iframeWin.addEventListener != "undefined") {
						    iframeWin.addEventListener("beforeunload", beforechangeEventhandler, false);
						} else if (typeof iframeDoc.attachEvent != "undefined") {
						    iframeWin.attachEvent("onbeforeunload", beforechangeEventhandler);
						}
						*/
					
						if (typeof iframeDoc.addEventListener != "undefined") {
						    iframeDoc.addEventListener("click", iframeonclickEventhandler, false);
						} else if (typeof iframeDoc.attachEvent != "undefined") {
						    iframeDoc.attachEvent("onclick", iframeonclickEventhandler);
						}
					}
				} catch (e) {
					console.log(e);
				}		
			}		
					
			function registerIframeEvents(){
				try {
					var currentfr = document.getElementById(iframeid);
					if (currentfr){
						if (currentfr.addEventListener) {
							currentfr.addEventListener("load", onloadIframeAdjustments, false);
						} else if (currentfr.attachEvent){
							currentfr.detachEvent("onload", onloadIframeAdjustments); // Bug fix line
							currentfr.attachEvent("onload", onloadIframeAdjustments);
						}					
					}
				} catch (e) {
					
				}	
			}
					
			function sendMessagesToIframe(){
				try {
					var currentfr = document.getElementById(iframeid);
					if (currentfr){
						var iframeWin = currentfr.contentWindow || currentfr.contentDocument;
						iframeWin.postMessage("height", "*");
						console.log("send message to iframe");
					}
				} catch (e) {
					
				}	
			}
					
			function messageEventhandler(event){
				if(event.data.type == "height"){
					var height = event.data.height;
					var width = event.data.width;
					
					$("#"+iframeid).data("message", "true");
					resizeIframe(height, width);
				}
					
				if(event.data.type == "link"){
					$("#"+iframeid).data("message", "true");
				}
			}
	
			function init_wrapper(){
				var tempobj = document.getElementById(iframeid);
				tempobj.style.display = "block";
				
				registerIframeEvents();
					
				if (window.addEventListener){
				  addEventListener("message", messageEventhandler, false);
				} else {
				  attachEvent("onmessage", messageEventhandler);
				}
			}

			init_wrapper();		
					
			var aktiv = window.setInterval(perodicIframeAdjustment, 1000*2); //2 Sekunden
		';

		 $this->tpl->add_js($out, 'docready');
	} #end function

}
?>