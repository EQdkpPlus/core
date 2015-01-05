/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-Plus
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