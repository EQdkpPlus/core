$(document).ready(function(){
	/* My Chars Points */
	$('.mychars-points-tooltip .char').on('click', function(){
		$(this).parent().parent().children('tr').removeClass("active");
		$(this).parent().addClass("active");
		var current = $(this).parent().find('.current').html();
		var icons = $(this).parent().find('.icons').html();
		$(".mychars-points-target").html(icons + " "+current);
		var id = $(this).parent().attr('id');
		localStorage.setItem('mcp_'+mmocms_userid, id);
	});
	var saved = localStorage.getItem('mcp_'+mmocms_userid);
	
	if (saved && saved != "" && $('#'+saved).find('.current').html() != undefined){
		$('#'+saved).addClass("active");
		var current = $('#'+saved).find('.current').html();
		var icons = $('#'+saved).find('.icons').html();
		$(".mychars-points-target").html(icons + " "+current);
	} else {
		$('.mychars-points-tooltip .main').addClass("active");
		var current = $('.mychars-points-tooltip .main').find('.current').html();
		var icons = $('.mychars-points-tooltip .main').find('.icons').html();
		$(".mychars-points-target").html(icons + " "+current);
	}
	
	/* Main Menu */
	$('ul.mainmenu li.link_li_indexphp a.link_indexphp, ul.mainmenu li.link_li_entry_home a.link_entry_home').html('');
	$('ul.mainmenu').addClass('sf-menu');
	jQuery('ul.mainmenu').superfish({
			delay:		400,
			animation:	{opacity:'show',height:'show'},
			speed:		'fast'
	});
	
	/* Tooltip Triggers */
	$('.tooltip-trigger').on('click', function(event){
		event.preventDefault();
		var mytooltip = $(this).data('tooltip');
		$("#"+mytooltip).show('fast');
		$(document).on('click', function(event) {
			var count = $(event.target).parents('.'+mytooltip+'-container').length;
			if (count == 0){
				$("#"+mytooltip).hide('fast');
			}
		});
	});
	
	/* User Tooltip Doubleclick */
	$('.user-tooltip-trigger').on('dblclick', function(event){
		$("#user-tooltip").hide('fast');
		window.location=mmocms_controller_path+"Settings"+mmocms_seo_extension+mmocms_sid;
	});
	
	user_clock();
	
	$( ".openLoginModal" ).on('click', function() {
		$( "#dialog-login" ).dialog( "open" );
	});
	
	/* Notifications */
	$('.notification-tooltip-trigger').on('click', function(event){
		$(".notification-tooltip").hide('fast');
		$("#notification-tooltip-all").show('fast');
		notification_show_only('all');
		var classList = $(this).attr('class').split(/\s+/);
		for (var i = 0; i < classList.length; i++) {
		   if (classList[i] === 'notification-bubble-red' || classList[i] === 'notification-bubble-yellow' || classList[i] === 'notification-bubble-green') {
		     notification_show_only(classList[i]);
		     break;
		   }
		}
		
		$(document).on('click', function(event) {
			var count = $(event.target).parents('.notification-tooltip-container').length;
			if (count == 0 && (!$(event.target).hasClass('notification-markasread')) ){
				$(".notification-tooltip").hide('fast');
			}
		});
		
	});
	
	$('.notification-content').on('click', '.notification-markasread', function() {
		var ids = $(this).parent().parent().data('ids');
		$(this).parent().parent().remove();
		recalculate_notification_bubbles();
		$.get(mmocms_controller_path+"Notifications"+mmocms_seo_extension+mmocms_sid+"&markread&ids="+ids);
	});
	$('.notification-filter').on('click', function(event){
		if ($(this).hasClass('filtered')){
			//Show all of this
			if ($(this).hasClass('notification-bubble-green')) $('.notification-content ul li.prio_0').show();
			if ($(this).hasClass('notification-bubble-yellow')) $('.notification-content ul li.prio_1').show();
			if ($(this).hasClass('notification-bubble-red')) $('.notification-content ul li.prio_2').show();
			
			$(this).removeClass('filtered');
		} else {
			//hide all of this
			if ($(this).hasClass('notification-bubble-green')) $('.notification-content ul li.prio_0').hide();
			if ($(this).hasClass('notification-bubble-yellow')) $('.notification-content ul li.prio_1').hide();
			if ($(this).hasClass('notification-bubble-red')) $('.notification-content ul li.prio_2').hide();
			$(this).addClass('filtered');
		}
	});
	//Periodic Update of Notifications
	window.setTimeout("notification_update()", 1000*60*5);
})

/* User clock */
function user_clock(){	
	var mydate = mymoment.format(user_clock_format);
	$('.user_time').html(mydate);
	mymoment.add(1, 's');
	window.setTimeout("user_clock()", 1000);
}

/* Some static Notification Functions */
var favicon;
function notification_favicon(red, yellow, green){
	if (typeof favicon === 'undefined') return;
	
	if (red > 0) {
		favicon.badge(red, {bgColor: '#d00'});
		return;
	}
	if (yellow > 0) {
		favicon.badge(yellow, {bgColor: '#F89406'});
		return;
	}
	if (green > 0) {
		favicon.badge(green, {bgColor: '#468847'});
		return;
	}
	favicon.reset();
}

function notification_show_only(name){
	if (name === 'all'){
		$('.notification-filter').removeClass('filtered');
		$('.notification-content ul li.prio_0, .notification-content ul li.prio_1, .notification-content ul li.prio_2').show();
	} else {
		$('.notification-content ul li.prio_0, .notification-content ul li.prio_1, .notification-content ul li.prio_2').hide();
		$('.notification-filter').addClass('filtered');
		$('.'+name+'.notification-filter').removeClass('filtered');
		if (name === 'notification-bubble-green') $('.notification-content ul li.prio_0').show();
		if (name === 'notification-bubble-yellow') $('.notification-content ul li.prio_1').show();
		if (name === 'notification-bubble-red') $('.notification-content ul li.prio_2').show();
	}
}

function notification_update(){			
	$.get(mmocms_controller_path+"Notifications"+mmocms_seo_extension+mmocms_sid+"&load", function(data){
		$('.notification-content ul').html(data);
		recalculate_notification_bubbles();
	});
		
	//5 Minute
	window.setTimeout("notification_update()", 1000*60*5);
}