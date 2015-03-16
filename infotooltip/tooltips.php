<?php
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
 
var EQdkpTooltip = new function(){
	//Init Vars
	var mmocms_root_path = '<?php echo $eqdkp_path; ?>';
	var cached_itts = new Array();
	var cache_labels = new Array();
	
	function addResources(){
		
		if ("complete" != document.readyState && "loaded" != document.readyState) setTimeout(addResources, 50);
		else {
			var head = document.getElementsByTagName("head")[0];
			//Jquery CSS
			var ac = document.createElement("link");
			ac.href = mmocms_root_path  + "libraries/jquery/core/core.css";
			ac.type = 'text/css';
			ac.rel = 'stylesheet';
			head.appendChild(ac);

			<?php if (is_file($eqdkp_root_path.'games/'.$itt->config['game'].'/infotooltip/'.$itt->config['game'].'.css')) { ?>
			//Game-Specific CSS
			var ac2 = document.createElement("link");
			ac2.href = mmocms_root_path  + "games/<?php echo $itt->config['game'];?>/infotooltip/<?php echo $itt->config['game'];?>.css";
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
		
		}
	}
	function scriptLoaded(){
		jQuery.noConflict();
		
		(function($){
			$.fn.extend({

				//pass the options variable to the function
				infotooltips: function(options) {

				return this.each(function() {
						var mid = $(this).attr('id');

						//code to be inserted here
						gameid = ($('#'+mid).attr('data-game_id')) ? $('#'+mid).attr('data-game_id') : 0;
						jsondata = {"name": $('#'+mid).attr('data-name'), "game_id": gameid}
						
						if (cache_labels['t_'+ mid] != undefined){
							$('#'+mid).empty();
							$('#'+mid).prepend(cache_labels['t_'+ mid]);
						} else {
							var url = mmocms_root_path + 'infotooltip/infotooltip_feed.php?jsondata='+$('#'+mid).attr('title')+'&divid='+mid;
							$.get(url, jsondata, function(data) {
								$('#'+mid).empty();
								$('#'+mid).prepend(data);
								cache_labels['t_'+ mid] = data;
							});
						}
					});
				}
			});
		})(jQuery);
		
		jQuery('body').append('<style type="text/css">.ui-infotooltip, .ui-tooltip, .ui-tooltip-content { border: 0px;} .ui-infotooltip { box-shadow:0 0 0 0 !important; max-width: 450px !important; padding: 0px; position: absolute;z-index: 9999;border-radius: 0px !important; background:none !important;}</style>');
		
		console.log("script loaded successful");
		replaceContent();
	}
	
	function replaceContent(){
		jQuery(document).ready(function($){
		
			$(".EQdkpTooltip").each(function(){
				var itemname = $(this).html();
				var random = Math.random()*1000;
				
				var random2 = Math.random()*100;
				var item_data = new Array();
				
				item_data['name'] = itemname.toString();
				var is_numeric = /^[0-9]+$/.test(itemname);
				var itemdatatag = ""
				if (is_numeric){
					item_data['game_id'] = parseInt(itemname);
					itemdatatag = 'data-game_id="'+item_data['game_id']+'"';
				}
				if (itemname.substring(0, 3) == 'id:'){
					myitemid = itemname.substr(3);
					item_data['game_id'] = myitemid;
					itemdatatag = 'data-game_id="'+item_data['game_id']+'"';
					item_data['name'] = myitemid;
					itemname = myitemid;
				}
				
				var out = '<span class="infotooltip" id="bb_'+parseInt(random)+ parseInt(random2) +'" data-name="'+itemname.toString()+'" '+itemdatatag+' title="0'+ mmo_encode64(js_array_to_php_array(item_data)) +'">'+itemname+'</span>';

				$(this).html(out);
			});

			$('.infotooltip').infotooltips();
			
			$('.infotooltip').tooltip({
				track: true,
				open: function(event, ui) {
					$(ui.tooltip).siblings(".tooltip").remove();
				},
				content: function(response) {
					var direct = $(this).attr('title').substr(0,1);
					var mytitle = $(this).attr('title');
					if(direct == '1') {
						$(this).attr('title', '');
						return '';
					}
					if (mytitle == ''){
						return;
					}
					if (cached_itts['t_'+$(this).attr('title')] != undefined){
						return cached_itts['t_'+$(this).attr('title')];
					} else {
						var bla = $.get(mmocms_root_path+'infotooltip/infotooltip_feed.php?direct=1&data='+$(this).attr('title'), response);
						bla.success(function(data) {
							cached_itts['t_'+mytitle] = $.trim(data);
						});
						return 'Loading...';
					}
				},
				tooltipClass: "ui-infotooltip",
			});
		});
	}
	
	
	
	this.init = function () {
		addResources();
	}
	
	function base64_encode (data) {
		// http://kevin.vanzonneveld.net
		// +   original by: Tyler Akins (http://rumkin.com)
		// +   improved by: Bayron Guevara
		// +   improved by: Thunder.m
		// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
		// +   bugfixed by: Pellentesque Malesuada
		// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
		// +   improved by: Rafał Kukawski (http://kukawski.pl)
		// *     example 1: base64_encode('Kevin van Zonneveld');
		// *     returns 1: 'S2V2aW4gdmFuIFpvbm5ldmVsZA=='
		// mozilla has this native
		// - but breaks in 2.0.0.12!
		//if (typeof this.window['btoa'] == 'function') {
		//    return btoa(data);
		//}
		var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
		var o1, o2, o3, h1, h2, h3, h4, bits, i = 0,
		ac = 0,
		enc = "",
		tmp_arr = [];

		if (!data) {
			return data;
		}

		do { // pack three octets into four hexets
			o1 = data.charCodeAt(i++);
			o2 = data.charCodeAt(i++);
			o3 = data.charCodeAt(i++);

			bits = o1 << 16 | o2 << 8 | o3;

			h1 = bits >> 18 & 0x3f;
			h2 = bits >> 12 & 0x3f;
			h3 = bits >> 6 & 0x3f;
			h4 = bits & 0x3f;

			// use hexets to index into b64, and append result to encoded string
			tmp_arr[ac++] = b64.charAt(h1) + b64.charAt(h2) + b64.charAt(h3) + b64.charAt(h4);
		} while (i < data.length);

		enc = tmp_arr.join('');
		var r = data.length % 3;
		return (r ? enc.slice(0, r - 3) : enc) + '==='.slice(r || 3);
	}


	function mmo_encode64(inp){
		return base64_encode(inp);
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
}


EQdkpTooltip.init();