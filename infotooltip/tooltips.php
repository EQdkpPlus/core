<?php

 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2010
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 * 
 * $Id$
 */

define('EQDKP_INC', true);
define('NO_MMODE_REDIRECT', true);

error_reporting(E_ERROR);
header('content-type: text/javascript; charset=UTF-8');
$eqdkp_root_path = './../';
include($eqdkp_root_path.'common.php');
$itt = register('infotooltip');

function httpHost(){
	$protocol = (isset($_SERVER['SSL_SESSION_ID']) || (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1))) ? 'https://' : 'http://';
	$xhost    = preg_replace('/[^A-Za-z0-9\.:-]/', '',(isset( $_SERVER['HTTP_X_FORWARDED_HOST']) ?  $_SERVER['HTTP_X_FORWARDED_HOST'] : ''));
	$host		= $_SERVER['HTTP_HOST'];
	if (empty($host)){
		$host	 = $_SERVER['SERVER_NAME'];
		$host	.= ($_SERVER['SERVER_PORT'] != 80) ? ':' . $_SERVER['SERVER_PORT'] : '';
	}
	return $protocol.(!empty($xhost) ? $xhost . '/' : '').preg_replace('/[^A-Za-z0-9\.:-]/', '', $host);
}
	
$strPath = substr(str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']), 0, -12);

$eqdkp_path = httpHost().$strPath;

?>

//Init Vars
var mmocms_root_path = '<?php echo $eqdkp_path; ?>';
var head = document.getElementsByTagName("head")[0];

//Jquery CSS
var ac = document.createElement("link");
ac.href = mmocms_root_path  + "libraries/jquery/core/core.css";
ac.type = 'text/css';
ac.rel = 'stylesheet';
head.appendChild(ac);

<?php if (is_file($eqdkp_root_path.'infotooltip/includes/'.$itt->config['game'].'.css')) { ?>
//Game-Specific CSS
var ac2 = document.createElement("link");
ac2.href = mmocms_root_path  + "infotooltip/includes/<?php echo $itt->config['game'];?>.css";
ac2.type = 'text/css';
ac2.rel = 'stylesheet';
head.appendChild(ac2);
<?php } ?>


//JQuery core
var aj = document.createElement("script");
aj.src = mmocms_root_path  + "libraries/jquery/core/core.js";
aj.type = 'text/javascript';
aj.onload=scriptLoaded;
head.appendChild(aj);

function scriptLoaded(){
	jQuery.noConflict();
}


window.onload = function(){
	jQuery(document).ready(function($){
		
		(function($){
			$.fn.extend({

				//pass the options variable to the function
				infotooltips: function(options) {

				return this.each(function() {
						var mid = $(this).attr('id');

						//code to be inserted here
						var url = mmocms_root_path + 'infotooltip/infotooltip_feed.php?data='+$('#'+mid).attr('title')+'&divid='+mid;
						$.get(url, function(data) {
							$('#'+mid).empty();
							$('#'+mid).prepend(data);
						});
						// end of custom code...

					});
				}
			});
		})(jQuery);
		
		$('body').append('<style type="text/css">.ui-infotooltip, .ui-tooltip, .ui-tooltip-content { border: 0px;}</style>');

	
		var my_html = $('body').html();
		var replaced = my_html.replace(/\[item(.*?)\](.*?)\[\/item\]/gi, function(str, p1, p2, offset, s){		
			if (p2 != ''){
				var random = Math.random()*1000;
				var random2 = Math.random()*100;
				
				var item_data = new Array();
				item_data['name'] = p2.toString();
				var is_numeric = /^[0-9]+$/.test(p2);
				if (is_numeric){
					item_data['game_id'] = parseInt(p2);
				}
				
				var out = '<span class="infotooltip" id="bb_'+parseInt(random)+ parseInt(random2) +'" title="0'+ mmo_encode64(js_array_to_php_array(item_data)) +'">'+p2+'</span>';
				return out;
			}
			return '';					
		});
		$('body').html(replaced);
		
		//Convert back to BBcode if it's an input field
		$(':input').each(function(){
			var value = $(this).val();
			value = value.replace(/<span class="infotooltip"(.*?)>(.*?)<\/span>/gi, function(str, p1, p2, offset, s){
				return '[item]' + p2 +'[/item]';			
			});
			$(this).val(value);
		});
		
		$(document).ready(function(){
		
			$('.infotooltip').infotooltips();

			$('.infotooltip').tooltip({
				content: function(response) {
					var direct = $(this).attr('title').substr(0,1);
					if(direct == 1) {
						$(this).attr('title', '');
						return '';
					}
					$.get( mmocms_root_path + 'infotooltip/infotooltip_feed.php?direct=1&data='+$(this).attr('title'), response);
					return 'Laden...';
				},
				open: function() {
					var tooltip = $(this).tooltip('widget');
					tooltip.removeClass('ui-tooltip ui-widget ui-corner-all ui-widget-content');
					tooltip.addClass('ui-infotooltip');
					$(document).mousemove(function(event) {
						tooltip.position({
							my: 'left center',
							at: 'right center',
							offset: '50 25',
							of: event
						});
					})
					// trigger once to override element-relative positioning
					.mousemove();
				},
				close: function() {
					$(document).unbind('mousemove');
				}
			});
		
		 });
		
	});

}
				
function mmo_encode64(inp){
	var key="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
	var chr1,chr2,chr3,enc3,enc4,i=0,out="";
	while(i<inp.length){
		chr1=inp.charCodeAt(i++);if(chr1>127) chr1=88;
		chr2=inp.charCodeAt(i++);if(chr2>127) chr2=88;
		chr3=inp.charCodeAt(i++);if(chr3>127) chr3=88;
		if(isNaN(chr3)) {enc4=64;chr3=0;} else enc4=chr3&63
		if(isNaN(chr2)) {enc3=64;chr2=0;} else enc3=((chr2<<2)|(chr3>>6))&63
		out+=key.charAt((chr1>>2)&63)+key.charAt(((chr1<<4)|(chr2>>4))&63)+key.charAt(enc3)+key.charAt(enc4);
	}
	return encodeURIComponent(out);
}

function js_array_to_php_array (a){
	var a_php = "";
	var total = 0;
	for (var key in a) {
		if (key != 'name' && key != 'game_id') {
			continue;
		}
		total++;
		a_php = a_php + "s:" +
		String(key).length + ":\"" + String(key) + "\";s:" +
		String(a[key]).length + ":\"" + String(a[key]) + "\";";
	}
	a_php = "a:" + total + ":{" + a_php + "}";
	return a_php;
}