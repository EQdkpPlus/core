tinyMCEPopup.requireLangPack();

var PagesDialog = {
	init : function() {
		var f = document.forms[0];
		var inst = tinyMCEPopup.editor;
		var elm = inst.selection.getNode();
		
		elm = inst.dom.getParent(elm, "IMG");
		if (elm != null && elm.nodeName == "IMG"){
			var src = inst.dom.getAttrib(elm, 'src');
			f.src.value = tinyMCEPopup.editor.documentBaseURI.toAbsolute(src);
		}
	},

	insert : function() {
		// Insert the contents from the input into the document
		page = document.forms[0].page.value;
		
		name = document.forms[0].urlname.value;
		if (name != ""){
			output = '<a href="{{page_url::'+page+'}}">'+name+'</a>';
		} else {
			output = '{{page::'+page+'}}';
		}
		
		tinyMCEPopup.editor.execCommand('mceInsertContent', false, output);
		tinyMCEPopup.close();
	}
};

tinyMCEPopup.onInit.add(PagesDialog.init, PagesDialog);
