/**
 * editor_plugin_src.js
 *
 * Copyright 2009, Moxiecode Systems AB
 * Released under LGPL License.
 *
 * License: http://tinymce.moxiecode.com/license
 * Contributing: http://tinymce.moxiecode.com/contributing
 */

(function() {
	// Load plugin specific language pack
	tinymce.PluginManager.requireLangPack('eqdkp_lightbox');

	tinymce.create('tinymce.plugins.eqdkp_lightboxPlugin', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function(ed, url) {
			// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceeqdkp_lightbox');
			ed.addCommand('mceeqdkp_lightbox', function() {

				ed.windowManager.open({
					file : url + '/dialog.php',
					width : 400 + parseInt(ed.getLang('eqdkp_lightbox.delta_width', 0)),
					height : 200 + parseInt(ed.getLang('eqdkp_lightbox.delta_height', 0)),
					inline : 1
				}, {
					plugin_url : url, // Plugin absolute URL
					some_custom_arg : 'custom arg' // Custom argument
				});
			});

			// Register eqdkp_lightbox button
			ed.addButton('eqdkp_lightbox', {
				title : 'eqdkp_lightbox.desc',
				cmd : 'mceeqdkp_lightbox',
				image : url + '/img/eqdkp_lightbox.png'
			});
		},

		/**
		 * Creates control instances based in the incomming name. This method is normally not
		 * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
		 * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
		 * method can be used to create those.
		 *
		 * @param {String} n Name of the control to create.
		 * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
		 * @return {tinymce.ui.Control} New control instance or null if no control was created.
		 */
		createControl : function(n, cm) {
			return null;
		},

		/**
		 * Returns information about the plugin as a name/value array.
		 * The current keys are longname, author, authorurl, infourl and version.
		 *
		 * @return {Object} Name/value array containing information about the plugin.
		 */
		getInfo : function() {
			return {
				longname : 'eqdkp_lightbox plugin',
				author : 'GodMod',
				authorurl : 'http://tinymce.moxiecode.com',
				infourl : 'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/eqdkp_lightbox',
				version : "1.0"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('eqdkp_lightbox', tinymce.plugins.eqdkp_lightboxPlugin);
})();