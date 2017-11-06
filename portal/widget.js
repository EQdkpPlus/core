/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-Plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2016 EQdkp-Plus Developer Team
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
	var random_value = false;
	var nojs = false; //global
	var nocss = false; // global
	var context = new Array();
	var eqdkp_jquery = false;
	
	
	this.init = function(intModuleID, strRandomValue, eqdkp_url){
		moduleID = intModuleID;
		
		random_value = strRandomValue || "";
		if (random_value != "") random_value = random_value + "_";
		target = "eqdkp_portal_"+random_value + intModuleID;
		
		url = eqdkp_url || url;
		if(!url) getURL();
		saveContext();
		addResources(target);
		resetValues();
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
		if(varname == "url"){
			url = value;
		}
		if(varname == "nocss"){
			nocss = value;
		}
		saveContext();
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
	
	function addResources(localtarget){
		var links = document.getElementsByTagName("link");
		console.log(links);
		var loaded = false;
		for(var i = 0; i< links.length; i++){
			if (links[i].href != undefined && links[i].href != ""){
				var href = links[i].href;
				if (href.indexOf("/libraries/FontAwesome/font-awesome.min.css") !=-1){
					loaded = true;
				}
			}
		}
		
		if (!loaded){
			var mycontext = context[localtarget];
			if(typeof mycontext != "undefined"){
				url = mycontext[4];
			}
			
			
			var head = document.getElementsByTagName("head")[0];
			//Jquery CSS
			if (!nocss){
				var ac = document.createElement("link");
				ac.href = url + "libraries/FontAwesome/font-awesome.min.css";
				ac.type = 'text/css';
				ac.rel = 'stylesheet';
				head.appendChild(ac);
			}
			
			getModule(localtarget);

		} else {
			getModule(localtarget);
		}
	}

	function getModule(localtarget){
		var xmlHttpObject = false;
		var mycontext = context[localtarget];
		if(typeof mycontext != "undefined"){
			moduleID = mycontext[0];
			position = mycontext[1];
			header = mycontext[2];
			wide = mycontext[3];
			url = mycontext[4];
		}

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
				query.open("GET", url+'exchange.php?out=portal&id=' + moduleID+'&header='+header+'&position='+position+'&wide='+wide+'&nocss='+nocss, true);
				query.onreadystatechange = function(){
					if (this.readyState == 4) {
						result = this.responseText;
						html = parseHTML(result, document);
						var mycontent = html.querySelector('.external_module');
						document.getElementById(localtarget).innerHTML = mycontent.innerHTML;
					}
				}; 
				query.send(null);
			}
			
		}		
	}
	
	function resetValues(){
		position = "left";
		header = 1;
		width = 0;
		url = false;
		random_value = false;
		target = false;
	}
	
	function saveContext(){
		if(target == false) return;
		context[target] = new Array(moduleID, position, header, wide, url, random_value);
	}
	
	/** 
	 * jQuery 2.1.3's parseHTML (without scripts options).
	 * Unlike jQuery, this returns a DocumentFragment, which is more convenient to insert into DOM.
	 * MIT license.
	 * 
	 * If you only support Edge 13+ then try this:
	    function parseHTML(html, context) {
	        var t = (context || document).createElement('template');
	            t.innerHTML = html;
	        return t.content.cloneNode(true);
	    }
	 */
	var parseHTML = (function() {
	    var rxhtmlTag = /<(?!area|br|col|embed|hr|img|input|link|meta|param)(([\w:]+)[^>]*)\/>/gi,
	        rtagName = /<([\w:]+)/,
	        rhtml = /<|&#?\w+;/,
	        // We have to close these tags to support XHTML (#13200)
	        wrapMap = {
	            // Support: IE9
	            option: [1, "<select multiple='multiple'>", "</select>"],

	            thead: [1, "<table>", "</table>"],
	            col: [2, "<table><colgroup>", "</colgroup></table>"],
	            tr: [2, "<table><tbody>", "</tbody></table>"],
	            td: [3, "<table><tbody><tr>", "</tr></tbody></table>"],

	            _default: [0, "", ""]
	        };
	        
	    /**
	     * @param {String} elem A string containing html
	     * @param {Document} context
	     */
	    return function parseHTML(elem, context) {
	        context = context || document;

	        var tmp, tag, wrap, j,
	            fragment = context.createDocumentFragment();

	        if (!rhtml.test(elem)) {
	            fragment.appendChild(context.createTextNode(elem));

	            // Convert html into DOM nodes
	        } else {
	            tmp = fragment.appendChild(context.createElement("div"));

	            // Deserialize a standard representation
	            tag = (rtagName.exec(elem) || ["", ""])[1].toLowerCase();
	            wrap = wrapMap[tag] || wrapMap._default;
	            tmp.innerHTML = wrap[1] + elem.replace(rxhtmlTag, "<$1></$2>") + wrap[2];

	            // Descend through wrappers to the right content
	            j = wrap[0];
	            while (j--) {
	                tmp = tmp.lastChild;
	            }

	            // Remove wrappers and append created nodes to fragment
	            fragment.removeChild(fragment.firstChild);
	            while (tmp.firstChild) {
	                fragment.appendChild(tmp.firstChild);
	            }
	        }

	        return fragment;
	    };
	}());
}