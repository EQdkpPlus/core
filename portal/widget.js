 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2010
 * Date:		$Date: 2013-08-09 13:43:59 +0200 (Fr, 09 Aug 2013) $
 * -----------------------------------------------------------------------
 * @author		$Author: godmod $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 13462 $
 * 
 * $Id: tooltips.php 13462 2013-08-09 11:43:59Z godmod $
 */
 
var EQdkpPortal = new function(){
	
	var url = false;
	var target = false;
	var moduleID = false;
	var query = null;
	//Default Values
	var wide = 0;
	var position = "left";
	var header = 1;
	
	
	this.init = function(intModuleID){
		target = "eqdkp_portal_"+intModuleID;
		moduleID = intModuleID;
		
		getURL();
		addResources();	
	}
	
	this.setVar = function(varname, value){
		if (varname == "header"){
			header = value;
		}
		if (varname == "wide"){
			wide = value;
		}
		if (varname == "position"){
			position = value;
		}
	}
	
	function getURL(){
		var scripts = document.getElementsByTagName("script");
		for(var i = 0; i< scripts.length; i++){
			if (scripts[i].src != undefined && scripts[i].src != ""){
				var src = scripts[i].src;
				
				if (src.indexOf("/portal/widget.js") !=-1){
					var src = scripts[i].src;
					url = src.substr(0, src.length-16);
				}
			}
		}
	}
	
	function addResources(){
		var scripts = document.getElementsByTagName("script");
		var loaded = false;
		for(var i = 0; i< scripts.length; i++){
			if (scripts[i].src != undefined && scripts[i].src != ""){
				var src = scripts[i].src;
				
				if (src.indexOf("/jquery/core/core.js") !=-1){
					loaded = true;
				}
			}
		}
		
		if (!loaded){	
			var head = document.getElementsByTagName("head")[0];
			//Jquery CSS
			/*
			var ac = document.createElement("link");
			ac.href = url + "libraries/jquery/core/core.css";
			ac.type = 'text/css';
			ac.rel = 'stylesheet';
			head.appendChild(ac);
			*/
			
			//JQuery core
			var aj = document.createElement("script");
			aj.src = url  + "libraries/jquery/core/core.js";
			aj.type = 'text/javascript';
			aj.onload=scriptLoaded;
			head.appendChild(aj);
		} else {
			getModule();
		}
	}

	function scriptLoaded(){
		jQuery.noConflict();
		getModule();
	}

	function getModule(){
		var xmlHttpObject = false;

		if (typeof XMLHttpRequest != 'undefined') {
			xmlHttpObject = new XMLHttpRequest();
		}

		if (!xmlHttpObject) {
			try {
				xmlHttpObject = new ActiveXObject("Msxml2.XMLHTTP");
			} catch(e) {
				try {
					xmlHttpObject = new ActiveXObject("Microsoft.XMLHTTP");
				} catch(e) {
					xmlHttpObject = false;
				}
			}
		}
		
		if (xmlHttpObject){
			query = xmlHttpObject;
			if (query.readyState == 4 || query.readyState == 0) {
				query.open("GET", url+'exchange.php?out=portal&id=' + moduleID+'&header='+header+'&position='+position+'&wide='+wide, true);
				query.onreadystatechange = handleData; 
				query.send(null);
			}
			
		}		
	}
	
	function handleData(){
		if (query.readyState == 4) {
			result = query.responseText;
			html = jQuery.parseHTML(result,document, true);
			document.getElementById(target).innerHTML = html[1].innerHTML;
			jQuery('head').append(html[0].innerHTML);
		}
	}
}