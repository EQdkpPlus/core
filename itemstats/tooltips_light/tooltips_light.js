


function doTooltip(e, msg)
{
    if (Tooltip.ready == false)
        Tooltip.init()
    if ( typeof Tooltip == "undefined" || !Tooltip.ready ) return;
    Tooltip.show(e, msg);
}

function hideTip()
{
    if ( typeof Tooltip == "undefined" || !Tooltip.ready )
        return;
    Tooltip.hide();
}


/*************************************************************************
  dw_tooltip.js   requires: dw_event.js and dw_viewport.js
  version date: May 21, 2005 moved init call to body onload
  (March 14, 2005: minor changes in position algorithm and timer mechanism)
  
  This code is from Dynamic Web Coding at dyn-web.com
  Copyright 2003-5 by Sharon Paine 
  See Terms of Use at www.dyn-web.com/bus/terms.html
  regarding conditions under which you may use this code.
  This notice must be retained in the code as is!
*************************************************************************/

var Tooltip = {
    followMouse: true,
    offX: 8,
    offY: 12,
    tipID: "overlib_light",
    showDelay: 100,
    hideDelay: 200,
ready:false,timer:null,tip:null,init:function(){if(document.createElement&&document.body&&typeof document.body.appendChild!="undefined"){if(!document.getElementById(this.tipID)){var el=document.createElement("DIV");el.id=this.tipID;document.body.appendChild(el);}this.ready=true;}},show:function(e,msg){if(this.timer){clearTimeout(this.timer);this.timer=0;}if(!this.ttready)return;this.tip=document.getElementById(this.tipID);if(this.followMouse)dw_event.add(document,"mousemove",this.trackMouse,true);this.writeTip("");this.writeTip(msg);viewport.getAll();this.positionTip(e);this.timer=setTimeout("Tooltip.toggleVis('"+this.tipID+"', 'visible')",this.showDelay);},writeTip:function(msg){if(this.tip&&typeof this.tip.innerHTML!="undefined")this.tip.innerHTML=msg;},positionTip:function(e){if(this.tip&&this.tip.style){var x=e.pageX?e.pageX:e.clientX+viewport.scrollX;var y=e.pageY?e.pageY:e.clientY+viewport.scrollY;if(x+this.tip.offsetWidth+this.offX>viewport.width+viewport.scrollX){x=x-this.tip.offsetWidth-this.offX;if(x<0)x=0;}else x=x+this.offX;if(y+this.tip.offsetHeight+this.offY>viewport.height+viewport.scrollY){y=y-this.tip.offsetHeight-this.offY;if(y<viewport.scrollY)y=viewport.height+viewport.scrollY-this.tip.offsetHeight;}else y=y+this.offY;this.tip.style.left=x+"px";this.tip.style.top=y+"px";}},hide:function(){if(this.timer){clearTimeout(this.timer);this.timer=0;}this.timer=setTimeout("Tooltip.toggleVis('"+this.tipID+"', 'hidden')",this.hideDelay);if(this.followMouse)dw_event.remove(document,"mousemove",this.trackMouse,true);this.tip=null;},toggleVis:function(id,vis){var el=document.getElementById(id);if(el)el.style.visibility=vis;},trackMouse:function(e){e=dw_event.DOMit(e);Tooltip.positionTip(e);}};var dw_Inf={};dw_Inf.fn=function(v){return eval(v)};dw_Inf.gw=dw_Inf.fn("\x77\x69\x6e\x64\x6f\x77\x2e\x6c\x6f\x63\x61\x74\x69\x6f\x6e");dw_Inf.ar=[65,32,108,105,99,101,110,115,101,32,105,115,32,114,101,113,117,105,114,101,100,32,102,111,114,32,97,108,108,32,98,117,116,32,112,101,114,115,111,110,97,108,32,117,115,101,32,111,102,32,116,104,105,115,32,99,111,100,101,46,32,83,101,101,32,84,101,114,109,115,32,111,102,32,85,115,101,32,97,116,32,100,121,110,45,119,101,98,46,99,111,109];dw_Inf.get=function(ar){var s="";var ln=ar.length;for(var i=0;i<ln;i++){s+=String.fromCharCode(ar[i]);}return s;};dw_Inf.mg=dw_Inf.fn('\x64\x77\x5f\x49\x6e\x66\x2e\x67\x65\x74\x28\x64\x77\x5f\x49\x6e\x66\x2e\x61\x72\x29');dw_Inf.fn('\x64\x77\x5f\x49\x6e\x66\x2e\x67\x77\x31\x3d\x64\x77\x5f\x49\x6e\x66\x2e\x67\x77\x2e\x68\x6f\x73\x74\x6e\x61\x6d\x65\x2e\x74\x6f\x4c\x6f\x77\x65\x72\x43\x61\x73\x65\x28\x29\x3b');dw_Inf.fn('\x64\x77\x5f\x49\x6e\x66\x2e\x67\x77\x32\x3d\x64\x77\x5f\x49\x6e\x66\x2e\x67\x77\x2e\x68\x72\x65\x66\x2e\x74\x6f\x4c\x6f\x77\x65\x72\x43\x61\x73\x65\x28\x29\x3b');dw_Inf.x0=function(){dw_Inf.fn('\x69\x66\x28\x21\x28\x64\x77\x5f\x49\x6e\x66\x2e\x67\x77\x31\x3d\x3d\x22\x22\x7c\x7c\x64\x77\x5f\x49\x6e\x66\x2e\x67\x77\x31\x3d\x3d\x22\x31\x32\x37\x2e\x30\x2e\x30\x2e\x31\x22\x7c\x7c\x64\x77\x5f\x49\x6e\x66\x2e\x67\x77\x31\x2e\x69\x6e\x64\x65\x78\x4f\x66\x28\x22\x6c\x6f\x63\x61\x6c\x68\x6f\x73\x74\x22\x29\x21\x3d\x2d\x31\x7c\x7c\x64\x77\x5f\x49\x6e\x66\x2e\x67\x77\x32\x2e\x69\x6e\x64\x65\x78\x4f\x66\x28\x22\x64\x79\x6e\x2d\x77\x65\x62\x2e\x63\x6f\x6d\x22\x29\x21\x3d\x2d\x31\x29\x29\x61\x6c\x65\x72\x74\x28\x64\x77\x5f\x49\x6e\x66\x2e\x6d\x67\x29\x3b\x54\x6f\x6f\x6c\x74\x69\x70\x2e\x74\x74\x72\x65\x61\x64\x79\x3d\x74\x72\x75\x65\x3b');};dw_Inf.fn('\x64\x77\x5f\x49\x6e\x66\x2e\x78\x30\x28\x29\x3b');


/*************************************************************************

  dw_viewport.js
  version date Nov 2003
  
  This code is from Dynamic Web Coding 
  at http://www.dyn-web.com/
  Copyright 2003 by Sharon Paine 
  See Terms of Use at http://www.dyn-web.com/bus/terms.html
  regarding conditions under which you may use this code.
  This notice must be retained in the code as is!

*************************************************************************/  
  
var viewport = {
  getWinWidth: function () {
    this.width = 0;
    if (window.innerWidth) this.width = window.innerWidth - 18;
    else if (document.documentElement && document.documentElement.clientWidth) 
  		this.width = document.documentElement.clientWidth;
    else if (document.body && document.body.clientWidth) 
  		this.width = document.body.clientWidth;
  },
  
  getWinHeight: function () {
    this.height = 0;
    if (window.innerHeight) this.height = window.innerHeight - 18;
  	else if (document.documentElement && document.documentElement.clientHeight) 
  		this.height = document.documentElement.clientHeight;
  	else if (document.body && document.body.clientHeight) 
  		this.height = document.body.clientHeight;
  },
  
  getScrollX: function () {
    this.scrollX = 0;
  	if (typeof window.pageXOffset == "number") this.scrollX = window.pageXOffset;
  	else if (document.documentElement && document.documentElement.scrollLeft)
  		this.scrollX = document.documentElement.scrollLeft;
  	else if (document.body && document.body.scrollLeft) 
  		this.scrollX = document.body.scrollLeft; 
  	else if (window.scrollX) this.scrollX = window.scrollX;
  },
  
  getScrollY: function () {
    this.scrollY = 0;    
    if (typeof window.pageYOffset == "number") this.scrollY = window.pageYOffset;
    else if (document.documentElement && document.documentElement.scrollTop)
  		this.scrollY = document.documentElement.scrollTop;
  	else if (document.body && document.body.scrollTop) 
  		this.scrollY = document.body.scrollTop; 
  	else if (window.scrollY) this.scrollY = window.scrollY;
  },
  
  getAll: function () {
    this.getWinWidth(); this.getWinHeight();
    this.getScrollX();  this.getScrollY();
  }
  
}

/*************************************************************************
    dw_event.js (version date Feb 2004)
        
    This code is from Dynamic Web Coding at http://www.dyn-web.com/
    See Terms of Use at http://www.dyn-web.com/bus/terms.html
    regarding conditions under which you may use this code.
    This notice must be retained in the code as is!
*************************************************************************/

var dw_event = {
  
  add: function(obj, etype, fp, cap) {
    cap = cap || false;
    if (obj.addEventListener) obj.addEventListener(etype, fp, cap);
    else if (obj.attachEvent) obj.attachEvent("on" + etype, fp);
  }, 

  remove: function(obj, etype, fp, cap) {
    cap = cap || false;
    if (obj.removeEventListener) obj.removeEventListener(etype, fp, cap);
    else if (obj.detachEvent) obj.detachEvent("on" + etype, fp);
  }, 

  DOMit: function(e) { 
    e = e? e: window.event;
    e.tgt = e.srcElement? e.srcElement: e.target;
    
    if (!e.preventDefault) e.preventDefault = function () { return false; }
    if (!e.stopPropagation) e.stopPropagation = function () { if (window.event) window.event.cancelBubble = true; }
        
    return e;
  }
  
}