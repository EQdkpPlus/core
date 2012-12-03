/**
* slidingPanels is similar to a horizontal accordion navigation, or holding 
* some playing cards in your hand... sliding panels around to reveal one of
* interest.
* 
* slidingPanels r1 // 2008.02.18 // jQuery 1.2.3+
* <http://cherne.net/brian/resources/jquery.slidingPanels.html>
* 
* REQUIRES jQuery hoverIntent plug-in
* <http://cherne.net/brian/resources/jquery.hoverIntent.html>
*
* slidingPanels is currently available for use in all personal or commercial 
* projects under both MIT and GPL licenses. This means that you can choose 
* the license that best suits your project, and use it accordingly.
* 
* // basic usage // assumes ul/li html structure
* $("ul").slidingPanels();
* 
* // advanced usage receives configuration object only
* $("div#wrapper").slidingPanels({
*   panelSelector: "div.panel",  // cssSelector = indicates the panel elements (default = "li")
*   slideDuration: 200,          // number = milliseconds of slide animation (default = 200)
*   onEnterState: handleEnter,   // function = callback after panel has entered a state (undefined by default)
*   onLeaveState: handleLeave,   // function = callback before panel leaves a state (undefined by default)
*   onLeaveDefaultDelay: 0,      // number = milliseconds to delay sliding animation when leaving default state (default = 0)
*   onLeaveStateDelay: 0         // number = milliseconds to delay sliding animation when leaving min/max states (default = 0)
* });
* 
* @param  options  An object with configuration options
* @author    Brian Cherne <brian@cherne.net>
*/
(function($) {
	$.fn.slidingPanels = function(options) {
		// default configuration options
		var cfg = {
			panelSelector: "li",
			slideDuration: 200,
			onLeaveDefaultDelay: 0,
			onLeaveStateDelay: 0
		};
		// override configuration options with user supplied object
		cfg = $.extend(cfg, options);
		
		// iterate through each jQuery object sent to this plug-in and return it to allow chaining
		return this.each(function(){

			var wrapper = $(this).addClass("slidingPanelsActivated");
			var cPanels = $(cfg.panelSelector,wrapper);
			var nPanels = cPanels.length;
			var nPanelDefaultX = ((1/nPanels)*100);
			var nPanelBaseX = nPanelDefaultX/2;
			var nPanelWidth = 100-(nPanelBaseX*(nPanels-1));
			var unit = "%";
			var iMaximizedPanel; // set later

			// ////////////////////////////////////////////////////////////////
			// iterate through panels
			// ////////////////////////////////////////////////////////////////
			cPanels.each(function(i){
				var properties = {
					index: i,
					position: "D",
					state: "Default",
					D2L: -i,
					D2R: nPanels-i,
					L2D: i,
					R2D: i-nPanels,
					L2R: nPanels, // assumes nPanelBaseX = nPanelDefaultX/2
					R2L: -nPanels // assumes nPanelBaseX = nPanelDefaultX/2
				}
				$.data(this,"slidingPanels",properties);

				// for each panel, set default css
				$(this).attr("slidingPanelState","Default").css({
					left: nPanelDefaultX*i+unit,
					width: nPanelWidth+unit
				});
			}).hoverIntent(function(){

				iMaximizedPanel = cPanels.index( this );

				cPanels.each(function(i){

					var position = $.data(this,"slidingPanels").position;

					if ( i <= iMaximizedPanel ){
						if (position != "L"){ $.data(this,"slidingPanels").direction = position + "2L" }
					} else {
						if (position != "R"){ $.data(this,"slidingPanels").direction = position + "2R" }
					}
				}); // close each

				onLeavePanels();

			},function(){});//close cPanels.hoverIntent

			// ////////////////////////////////////////////////////////////////
			// attach events to wrapper
			// ////////////////////////////////////////////////////////////////
			wrapper.hoverIntent({
				over: function(){},
				out: function(){
					cPanels.each(function(i){
						var position = $.data(this,"slidingPanels").position;
						$.data(this,"slidingPanels").direction = position + "2D"
					});
					onLeavePanels();
				},
				// because we use relative animation, wait until any opening animations are done
				timeout: cfg.slideDuration+10
			});//close wrapper.hoverIntent


			// ////////////////////////////////////////////////////////////////
			// has changed state // returns true | false
			// ////////////////////////////////////////////////////////////////
			var hasChangedState = function( panel ){
				// captures panels going to or coming from default state
				if ( $.data(panel,"slidingPanels").direction && $.data(panel,"slidingPanels").direction.indexOf("D") != -1 ){
					return true;
				}
				// captures leaving minimized for maximized state // captures entering maximized state
				if ( $.data(panel,"slidingPanels").index == iMaximizedPanel ){
					return true;
				}
				// captures leaving maximized for minimized state // captures entering maximized state
				if ( $.data(panel,"slidingPanels").state == "Maximized" ){
					return true;
				}
				
				return false;
			};// close hasChangedState


			// ////////////////////////////////////////////////////////////////
			// on leave panels // before animate
			// ////////////////////////////////////////////////////////////////
			var onLeavePanels = function(){
				// if onLeaveState callback is defined
				if ( cfg.onLeaveState ){
					var state;
					// iterate through panels // determine who is moving and who is maximized, they are leaving a state
					cPanels.each(function(i){
						var direction = $.data(this,"slidingPanels").direction;
						state = $.data(this,"slidingPanels").state;
						if ( hasChangedState(this) ){ cfg.onLeaveState.apply(this,[state]); }
					});
					// choose the correct delay // if it's greater than zero, delay animating the panels
					var delay = (state == "Default") ? cfg.onLeaveDefaultDelay : cfg.onLeaveStateDelay;
					if (delay > 0){
						setTimeout(animatePanels,delay);
						return;
					}
				}
				animatePanels();
			};// close onLeavePanels


			// ////////////////////////////////////////////////////////////////
			// animate panels
			// ////////////////////////////////////////////////////////////////
			var animatePanels = function(){
				var difference = 0;
				
				// create a DIV that never gets appended to the DOM to animate from
				$('<div></div>').css("left",0).animate({left:nPanelBaseX},{
					// panels use "know" their direction (D2L,D2R,etc.) and use multiplier against step-by-step animate values
					// keeps panels moving together, allows bug free percent based movement and is always relative to previous position
					step:function(now){
						difference = now - difference;
						cPanels.each(function(i){
							var direction = $.data(this,"slidingPanels").direction;
							if ( direction != undefined ){
								var currentX = Number(this.style.left.substring(0,this.style.left.length-1));
								var desiredX = currentX + (difference * $.data(this,"slidingPanels")[direction]);
								$(this).css("left",desiredX+unit);
							}
						});
						// difference is really now "previous" but we're using the same variable to be efficient
						difference = now;
					},
					duration: cfg.slideDuration,
					// when animation is complete, clean up
					complete: function(){
						cPanels.each(function(i){
							var direction = $.data(this,"slidingPanels").direction;
							var position;
							var state = $.data(this,"slidingPanels").state;
							if ( direction != undefined ){
								// set new position based on direction
								position = $.data(this,"slidingPanels").position = direction.substring(2);
								// round x to the nearest tenth to keep things clean (doesn't really matter)
								var desiredX = Math.round(Number(this.style.left.substring(0,this.style.left.length-1))*100)/100;
								$(this).css("left",desiredX+unit);
							}
							// captures entering minimized from maximized
							var wasMaximized = ( state == "Maximized" ) ? true : false;
							// set state based on position and index of maximized panel
							if( position == "D" ) { state = "Default"; } else { state = (i == iMaximizedPanel) ? "Maximized" : "Minimized"; }
							$.data(this,"slidingPanels").state = state;
							// if the panel has changed state, make sure to call onEnterState function
							if ( cfg.onEnterState && ( wasMaximized || hasChangedState(this) ) ){ cfg.onEnterState.apply(this,[state]); }
							// clear direction
							$.data(this,"slidingPanels").direction = undefined;
						});// close cPanels.each
					}// close div.animate.complete
				});// close div.animate
			};// close animatePanels

		});// close each slidingPanels object
	};// close slidingPanels
})(jQuery);