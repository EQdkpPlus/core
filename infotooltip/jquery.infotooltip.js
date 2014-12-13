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

(function($){
	$.fn.extend({

		//pass the options variable to the function
		infotooltips: function(options) {

		return this.each(function() {
				var mid = $(this).attr('id');

				//code to be inserted here
				var title = $('#'+mid).attr('title');
				if (title != '') {
					var url = mmocms_root_path + 'infotooltip/infotooltip_feed.php?data='+title+'&divid='+mid;
					$.get(url, function(data) {
						$('#'+mid).empty();
						$('#'+mid).prepend(data);
					});
				}
				// end of custom code...

			});
		}
	});
})(jQuery);