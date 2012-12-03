// Animated Collapsible Content
// Based on Animated Collapsible DIV v2.0- (c) Dynamic Drive DHTML code library: http://www.dynamicdrive.com
// Rewritten by Simon Wallmann for better persistence and usage in EqdkpPLUS
// Version 1.0

var animatedcollapse={
  divholders: {}, //structure: {div.id, div.attrs, div.$divref}

  show:function(divids){ //public method
  	if (typeof divids=="object"){
  		for (var i=0; i<divids.length; i++)
  			this.showhide(divids[i], "show")
  	}
  	else
  		this.showhide(divids, "show")
  },

  // Hide Content
  hide:function(divids){ //public method
  	if (typeof divids=="object"){
  		for (var i=0; i<divids.length; i++)
  			this.showhide(divids[i], "hide")
  	}else{
      this.showhide(divids, "hide")
    }
  },

  // Toggle Content
  toggle:function(divid){ //public method
  	this.showhide(divid, "toggle")
  },

  // Add a new div to the content list
  addDiv:function(divid, attrstring){ //public function
  	this.divholders[divid]=({id: divid, $divref: null, attrs: attrstring})
  	this.divholders[divid].getAttr=function(name){ //assign getAttr() function to each divholder object
  		var attr=new RegExp(name+"=([^,]+)", "i") //get name/value config pair (ie: width=400px,)
  		return (attr.test(this.attrs) && parseInt(RegExp.$1)!=0)? RegExp.$1 : null //return value portion (string), or 0 (false) if none found
  	}
  },

  // Showhide helper function
  showhide:function(divid, action){
  	var $divref=this.divholders[divid].$divref //reference collapsible DIV
  	if (this.divholders[divid] && $divref.length==1){ //if DIV exists
  		this.slideengine(divid, action)
  	}
  },

  // The main slide function
  slideengine:function(divid, action){
  	var $divref=this.divholders[divid].$divref
  	if (this.divholders[divid] && $divref.length==1){ //if this DIV exists
  		var animateSetting={height: action}
  		if ($divref.attr('fade'))
  			animateSetting.opacity=action
  		$divref.animate(animateSetting, $divref.attr('speed')? parseInt($divref.attr('speed')) : 500)
  		return false
  	}
  },

  // generate the map
  generatemap:function(){
  	var map={}
  	for (var i=0; i<arguments.length; i++){
  		if (arguments[i][1]!=null){
  			map[arguments[i][0]]=arguments[i][1]
  		}
  	}
  	return map
  },

  // init the slides
  init:function(){
  	var ac=this
  	jQuery(document).ready(function($){
  		var persistopenids=ac.getCookie('acopendivids') //Get list of div ids that should be expanded due to persistence ('div1,div2,etc')
      if (persistopenids!=null){ //if cookie isn't null (is null if first time page loads, and cookie hasnt been set yet)
        persistopenids=(persistopenids=='nada')? [] : persistopenids.split(',') //if no divs are persisted, set to empty array, else, array of div ids
  		}
      jQuery.each(ac.divholders, function(){ //loop through each collapsible DIV object
  			this.$divref=$('#'+this.id)
  			if (this.getAttr('persist') && persistopenids!=null){
  				var cssdisplay=(jQuery.inArray(this.id, persistopenids)!=-1)? 'none' : 'block'
  			}else{
  				var cssdisplay=this.getAttr('hide')? 'none' : null
  			}
  			this.$divref.css(ac.generatemap(['height', this.getAttr('height')], ['display', cssdisplay]))
  			this.$divref.attr(ac.generatemap(['fade', this.getAttr('fade')], ['speed', this.getAttr('speed')]))
  		}) //end divholders.each
  		var $allcontrols=$('*[rel]').filter('[@rel^="collapse-"], [@rel^="expand-"], [@rel^="toggle-"]') //get all elements on page with rel="collapse-", "expand-" and "toggle-"
  		var controlidentifiers=/(collapse-)|(expand-)|(toggle-)/
  		$allcontrols.each(function(){
  			$(this).click(function(){
  				var relattr=this.getAttribute('rel')
  				var divid=relattr.replace(controlidentifiers, '')
  				var doaction=(relattr.indexOf("collapse-")!=-1)? "hide" : (relattr.indexOf("expand-")!=-1)? "show" : "toggle"
  				return ac.showhide(divid, doaction)
  			}) //end control.click
  		})// end control.each
  		$(window).bind('unload', function(){
  			ac.uninit()
  		})
  	}) //end doc.ready()
  },

  // uninit the slides
  uninit:function(){
  	var opendivids=''
  	var persistopenids=this.getCookie('acopendivids') //Get list of div ids that should be expanded due to persistence ('div1,div2,etc')
      if (persistopenids!=null){ //if cookie isn't null (is null if first time page loads, and cookie hasnt been set yet)
        persistopenids=(persistopenids=='nada')? [] : persistopenids.split(',') //if no divs are persisted, set to empty array, else, array of div ids
  		}
  	jQuery.each(this.divholders, function(){
      //store ids of DIVs that are collapsed when page unloads: 'div1,div2,etc'
      if(this.$divref.css('display')=='none'){
  		  opendivids+=this.id+',' 
  		}else{
  		  //alert(this.id+': '+this.$divref.css('display'))
  		  if(persistopenids!=null){
          if(jQuery.inArray(this.id, persistopenids)!=-1 && this.$divref.css('display')!='block'){
            opendivids+=this.id+','
          }
        }
      }
  	})
  	opendivids=(opendivids=='')? 'nada' : opendivids.replace(/,$/, '');
  	this.setCookie('acopendivids', opendivids, '16')
  },

  // **************************************************
  // Cookie Helper Functions
  // **************************************************
  getCookie:function(Name){ 
  	var re=new RegExp(Name+"=[^;]*", "i"); //construct RE to search for target name/value pair
  	if (document.cookie.match(re)) //if cookie found
  		return document.cookie.match(re)[0].split("=")[1] //return its value
  	return null
  },
  
  setCookie:function(name, value, days){
  	if (typeof days!="undefined"){ //if set persistent cookie
  		var expireDate = new Date()
  		expireDate.setDate(expireDate.getDate()+days)
  		document.cookie = name+"="+value+"; path=/; expires="+expireDate.toGMTString()
  	}else{
  		document.cookie = name+"="+value+"; path=/" //else if this is a session only cookie
  	}
  }
}