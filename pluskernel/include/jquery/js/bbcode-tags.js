// ----------------------------------------------------------------------------
// j(Universal?)TagEditor, JQuery plugin
// ----------------------------------------------------------------------------
// Copyright (C) 2007 Jay Salvat
// http://www.jaysalvat.com/jquery/jtageditor/
// ----------------------------------------------------------------------------
// BBCode tags example
// http://en.wikipedia.org/wiki/Bbcode
// ----------------------------------------------------------------------------
// Feel free to add more tags
// ----------------------------------------------------------------------------
[
	{label:"Picture", accessKey:"p", closeTag:"", openTag:"[img]@Url@[/img]"}, 
	{label:"Link", accessKey:"l", closeTag:"[/url]", openTag:"[url=@Url@]"}, 
	{label:"Size", accessKey:"s", closeTag:"[/size]", openTag:"[size=@Text size@]"}, 
	{label:"Color", accessKey:"c", closeTag:"[/color]", openTag:"[color=@Color@]"}, 
	{label:"Bold", accessKey:"b", closeTag:"[/b]", openTag:"[b]"}, 
	{label:"Italic", accessKey:"i", closeTag:"[/i]", openTag:"[i]"}, 
	{label:"Underline", accessKey:"u", closeTag:"[/u]", openTag:"[u]"}, 
	{label:"Bulleted list", accessKey:"", closeTag:"\n[/list]", openTag:"[list]\n"}, 
	{label:"List item", accessKey:"", closeTag:"", openTag:"[*] "}, 
	//{label:"Citation", accessKey:"", closeTag:"[/quote]", openTag:"[quote]"}, 
	{label:"Code", accessKey:"", closeTag:"[/code]", openTag:"[code]"}, 
	{label:"Emoticon :)", accessKey:"", closeTag:"", openTag:" :) "}, 
	{label:"Emoticon :(", accessKey:"", closeTag:"", openTag:" :( "}, 
	{label:"Emoticon :p", accessKey:"", closeTag:"", openTag:" :p "}, 
	{label:"Emoticon :o", accessKey:"", closeTag:"", openTag:" :o "}, 
	{label:"Close Tags", accessKey:"<", callBack:"closeAll"}, 
	{label:"Clean Tags", accessKey:"", callBack:"cleanAll"}, 
]