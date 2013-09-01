function EQdkpPortal(intPortalID){
	
	var target = "eqdkp_portal_"+intPortalID;
	
	var xmlHttpObject = false;

	// Überprüfen ob XMLHttpRequest-Klasse vorhanden und erzeugen von Objekte für IE7, Firefox, etc.
	if (typeof XMLHttpRequest != 'undefined') 
	{
	    xmlHttpObject = new XMLHttpRequest();
	}

	// Wenn im oberen Block noch kein Objekt erzeugt, dann versuche XMLHTTP-Objekt zu erzeugen
	// Notwendig für IE6 oder IE5
	if (!xmlHttpObject) 
	{
	    try 
	    {
	        xmlHttpObject = new ActiveXObject("Msxml2.XMLHTTP");
	    }
	    catch(e) 
	    {
	        try 
	        {
	            xmlHttpObject = new ActiveXObject("Microsoft.XMLHTTP");
	        }
	        catch(e) 
	        {
	            xmlHttpObject = false;
	        }
	    }
	}
	
	var scripts = document.getElementsByTagName("script");
	var url = false;
	for(var i = 0; i< scripts.length; i++){
		if (scripts[i].src != undefined && scripts[i].src != ""){
			var src = scripts[i].src;
			
			if (src.indexOf("/portal/widget.js") !=-1){
				var src = scripts[i].src;
				url = src.substr(0, src.length-16);
			}
		}
	}
	if (!url) return false;
	
	if (xmlHttpObject){
		var query = xmlHttpObject;
		if (query.readyState == 4 || query.readyState == 0) {
			query.open("GET", url+'exchange.php?out=portal&id=' + intPortalID, true);
			query.onreadystatechange = eqdkp_handle_data; 
			query.send(null);
		}
		
	}
	
	function eqdkp_handle_data(){
		if (query.readyState == 4) {
			result = query.responseText;
			document.getElementById(target).innerHTML = result;
		}
	}
}

