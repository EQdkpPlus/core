// ----------------------------------------------------------------------------
// markItUp!
// ----------------------------------------------------------------------------
// Copyright (C) 2008 Jay Salvat
// http://markitup.jaysalvat.com/
// ----------------------------------------------------------------------------
// BBCode tags example
// http://en.wikipedia.org/wiki/Bbcode
// ----------------------------------------------------------------------------
// Feel free to add more tags
// ----------------------------------------------------------------------------
mySettings = {
	previewParserPath:	'', // path to your BBCode parser
	markupSet: [
		{name:'Bold', key:'B', openWith:'[b]', closeWith:'[/b]'},
		{name:'Italic', key:'I', openWith:'[i]', closeWith:'[/i]'},
		{name:'Underline', key:'U', openWith:'[u]', closeWith:'[/u]'},
		{separator:'---------------' },
		{name:'Picture', key:'P', replaceWith:'[img][![Url]!][/img]'},
		{name:'Link', key:'L', openWith:'[url=[![Url]!]]', closeWith:'[/url]', placeHolder:'Your text to link here...'},
		{separator:'---------------' },
		{name:'Colors', openWith:'[color=[![Color]!]]', closeWith:'[/color]', dropMenu: [
          {name:'Yellow', openWith:'[color=#FFFF00]', closeWith:'[/color]', className:"col1-1" },
          {name:'Orange', openWith:'[color=#FFA500]', closeWith:'[/color]', className:"col1-2" },
          {name:'Red', openWith:'[color=#FF0000]', closeWith:'[/color]', className:"col1-3" },
          {name:'Blue', openWith:'[color=#0000FF]', closeWith:'[/color]', className:"col2-1" },
          {name:'Purple', openWith:'[color=#800080]', closeWith:'[/color]', className:"col2-2" },
          {name:'Green', openWith:'[color=#008000]', closeWith:'[/color]', className:"col2-3" },
          {name:'White', openWith:'[color=#FFFFFF]', closeWith:'[/color]', className:"col3-1" },
          {name:'Gray', openWith:'[color=#808080]', closeWith:'[/color]', className:"col3-2" },
          {name:'Black', openWith:'[color=#000000]', closeWith:'[/color]', className:"col3-3" }
      ]},
    {name:'Size', key:'S', openWith:'[size=[![Text size]!]]', closeWith:'[/size]',
		dropMenu :[
			{name:'Big', openWith:'[size=200]', closeWith:'[/size]' },
			{name:'Normal', openWith:'[size=100]', closeWith:'[/size]' },
			{name:'Small', openWith:'[size=50]', closeWith:'[/size]' }
		]},
		{separator:'---------------' },
		{name:'Bulleted list', openWith:'[list]\n', closeWith:'\n[/list]'},
		{name:'List item', openWith:'[*] '},
		{separator:'---------------' },
		{name:'Code', openWith:'[code]', closeWith:'[/code]'}, 
		{separator:'---------------' },
		{name:'Clean', className:"clean", replaceWith:function(markitup) { return markitup.selection.replace(/\[(.*?)\]/g, "") } }
	]
}