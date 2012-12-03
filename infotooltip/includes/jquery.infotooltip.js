 /*
 * Project:     EQdkp-Plus Infotooltips
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		    http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2009
 * Date:        $Date: 2009-10-28 18:08:57 +0100 (Wed, 28 Oct 2009) $
 * -----------------------------------------------------------------------
 * @author      $Author: hoofy_leon $
 * @copyright   2009-2010 hoofy_leon
 * @link        http://eqdkp-plus.com
 * @package     infotooltip
 * @version     $Rev: 6294 $
 *
 * $Id: $
 */

(function($){
	$.fn.extend({

		//pass the options variable to the function
		infotooltips: function(options) {

		return this.each(function() {
				var mid = $(this).attr('id');

				//code to be inserted here
				var url = mmocms_root_path + 'infotooltip/infotooltip_feed.php?name='+$('#'+mid).attr('title')+'&divid='+mid;
				if($('#'+mid).attr('lang')) {
					url = url+'&lang='+$('#'+mid).attr('lang');
				}
				if($('#'+mid).attr('direct')) {
					url = url+'&direct='+$('#'+mid).attr('direct');
				}
				if($('#'+mid).attr('onlyicon')) {
					url = url+'&onlyicon='+$('#'+mid).attr('onlyicon');
				}
				if($('#'+mid).attr('use_game_id') == 1) {
					url = url+'&game_id='+$('#'+mid).attr('game_id');
				}
				if($('#'+mid).attr('server')) {
					url = url+'&server='+$('#'+mid).attr('server');
				}
				if($('#'+mid).attr('cname')) {
					url = url+'&cname='+$('#'+mid).attr('cname');
				}
				if($('#'+mid).attr('slotid')) {
					url = url+'&slotid='+$('#'+mid).attr('slotid');
				}
				$.get(url, function(data) {
					$('#'+mid).empty();
					$('#'+mid).prepend(data);
				});
				// end of custom code...

			});
		}
	});
})(jQuery);