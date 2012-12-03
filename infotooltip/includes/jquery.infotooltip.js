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