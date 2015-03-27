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

if(blnEqdkpExternal == undefined){
	var blnEqdkpExternal = true;
}

function eqdkp_onmessagelistener(event){
	//Determine the height
	if(event.data == "height"){
		var height = document.body.offsetHeight || document.body.scrollHeight;
		var width = document.body.scrollWidth;
		
		//Send it back
		parent.postMessage({type:"height", height:height, width:width},"*"); 
	}
}

function eqdkp_onclicklistener(e){		
	var elemntTagName = e.target.tagName;
		
	if(elemntTagName=='A')
	{
		var newurl = e.target.getAttribute("href");
		
		if(!eqdkp_check_base_url(newurl) && newurl.lastIndexOf("http", 0) === 0){
			e.preventDefault();
			parent.location.href = newurl;
		}
	}
}

function eqdkp_check_base_url(url){
	var parser = document.createElement("a");
	parser.href = url;
			
	var eqdkp_parser = document.createElement("a");
	eqdkp_parser.href = document.location.href;
		
	if(parser.hostname == eqdkp_parser.hostname){
		return true;
	}
			
	return false;
}

if (window.addEventListener){
  addEventListener("message", eqdkp_onmessagelistener, false);
  if(blnEqdkpExternal) document.addEventListener("click", eqdkp_onclicklistener, false);
} else {
  attachEvent("onmessage", eqdkp_onmessagelistener);
  if(blnEqdkpExternal) document.attachEvent("onclick", eqdkp_onclicklistener);
}