<!-- IF S_NO_HEADER_FOOTER -->
	{GBL_CONTENT_BODY}
<!-- ELSE --><!DOCTYPE html>
<html lang="{L_XML_LANG}">
	<head>
		<!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=9" /><![endif]-->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="keywords" content="{META_KEYWORDS}" />
		<meta name="description" content="{META_DESCRIPTION}" />
		<meta name="author" content="{GUILD_TAG}" />
		<!-- IF S_REPONSIVE -->
		<meta name="viewport" content="width=device-width,initial-scale=1.0" />
		<!-- ENDIF -->
		{META}
		{LINK}
		<title>{PAGE_TITLE}</title>
		{CSS_FILES}
		{JS_FILES}
		<link rel="shortcut icon" href="{TEMPLATE_PATH}/images/favicon.png" type="image/png" />
		<link rel="icon" href="{TEMPLATE_PATH}/images/favicon.png" type="image/png" />
		{RSS_FEEDS}
		<style type="text/css">
			{CSS_CODE}
			<!-- IF T_BACKGROUND_TYPE > 0 -->
			body {
				background:#000000 url('{TEMPLATE_BACKGROUND}') no-repeat center top;
				background-attachment: {T_BACKGROUND_POSITION};
			}
			
			#wrapper header {
				background: none !important;
			}
			<!-- ENDIF -->
		</style>
		
		<script type="text/javascript">
			//<![CDATA[
			{JS_CODE}
			
			$(document).ready(function() {
				/* Login Dialog */
				$( "#dialog-login" ).dialog({
					height: <!-- IF S_BRIDGE_INFO -->450<!-- ELSE -->350<!-- ENDIF -->,
					width: 530,
					modal: true,
					autoOpen: false,
				});
			});
			
			<!-- IF S_NORMAL_HEADER -->
			var user_timestamp_atom = "{USER_TIMESTAMP_ATOM}";
			var user_clock_format = "dddd, {USER_DATEFORMAT_LONG} {USER_TIMEFORMAT}";
			var user_timezone = "{USER_TIMEZONE}";
			
			var mymoment = moment(user_timestamp_atom).utcOffset(user_timezone);
			function user_clock(){	
				var mydate = mymoment.format(user_clock_format);
				$('.user_time').html(mydate);
				mymoment.add(1, 's');
				window.setTimeout("user_clock()", 1000);
			}
			
			function recalculate_notification_bubbles(){
				var red = 0; var green = 0; var yellow = 0;
				$('.notification-content ul li').each(function( index ) {
					var myclass = $(this).attr('class');
					var count = $(this).data('count');
					
					if (myclass == 'prio_0') green += parseInt(count);
					if(myclass == 'prio_1') yellow += parseInt(count);
					if(myclass == 'prio_2') red += parseInt(count);
				});
				if (green > 0) {
					$('.notification-bubble-green').html(green).show();
				} else {
					$('.notification-bubble-green').html(green).hide();
				}
				if (yellow > 0) {
					$('.notification-bubble-yellow').html(yellow).show();
				} else {
					$('.notification-bubble-yellow').html(yellow).hide();
				}
				if (red > 0) {
					$('.notification-bubble-red').html(red).show();
				} else {
					$('.notification-bubble-red').html(red).hide();
				}
				
				if (yellow ==0 && green==0 && red==0){
					$('.notification-content ul').html({L_notification_none|jsencode});
				}
				
				notification_favicon(red, yellow, green);
			}
			
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
				$.get("{EQDKP_CONTROLLER_PATH}Notifications{SEO_EXTENSION}{SID}&load", function(data){
					$('.notification-content ul').html(data);
					recalculate_notification_bubbles();
				});
					
				//5 Minute
				window.setTimeout("notification_update()", 1000*60*5);
			}

			function change_style(){
				$('<div>').html('<div class="style-switch-container"><i class="fa fa-lg fa-spin fa-spinner"></i></div>').dialog(
					{ open: function( event, ui ) {
						$.get("{EQDKP_ROOT_PATH}exchange.php{SID}&out=styles", function(data){
							$('.style-switch-container').html(data);
						});
					}, title: {L_change_style|jsencode}, width: 600, height: 500}
				);
			}
			
			$(document).ready(function() {
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
				$('.notification-mark-all-read').on('click', function() {
				    $('.notification-content ul').html({L_notification_none|jsencode});
					$('.notification-bubble-red, .notification-bubble-yellow, .notification-bubble-green').hide();
					notification_favicon(0, 0, 0);
					$.get("{EQDKP_CONTROLLER_PATH}Notifications{SEO_EXTENSION}{SID}&markallread");
				});
				$('.notification-content').on('click', '.notification-markasread', function() {
					var ids = $(this).parent().parent().data('ids');
					$(this).parent().parent().remove();
					recalculate_notification_bubbles();
					$.get("{EQDKP_CONTROLLER_PATH}Notifications{SEO_EXTENSION}{SID}&markread&ids="+ids);
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
				//Update Favicon
				favicon = new Favico({animation:'none'});
				notification_favicon({NOTIFICATION_COUNT_RED}, {NOTIFICATION_COUNT_YELLOW}, {NOTIFICATION_COUNT_GREEN});
				
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
				
				$('.user-tooltip-trigger').on('dblclick', function(event){
					$("#user-tooltip").hide('fast');
					window.location="{EQDKP_CONTROLLER_PATH}Settings{SEO_EXTENSION}{SID}";
				});
				
				$('ul.mainmenu li.link_li_indexphp a.link_indexphp, ul.mainmenu li.link_li_entry_home a.link_entry_home').html('');
				$('ul.mainmenu').addClass('sf-menu');
				jQuery('ul.mainmenu').superfish({
						delay:		400,
						animation:	{opacity:'show',height:'show'},
						speed:		'fast'
				});
				
				<!-- IF S_MYCHARS_POINTS and U_CHARACTERS != "" -->
				/* My Chars Points */
				$('.mychars-points-tooltip .char').on('click', function(){
					$(this).parent().parent().children('tr').removeClass("active");
					$(this).parent().addClass("active");
					var current = $(this).parent().find('.current').html();
					var icons = $(this).parent().find('.icons').html();
					$(".mychars-points-target").html(icons + " "+current);
					var id = $(this).parent().attr('id');
					localStorage.setItem('mcp_{USER_ID}', id);
				});
				var saved = localStorage.getItem('mcp_{USER_ID}');

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
				<!-- ENDIF -->
			});
			<!-- ELSE -->
				<!-- JS for simple header. Above is for normal header only -->
			<!-- ENDIF -->
			//]]>
		</script>
	</head>
	<body id="top" class="<!-- IF S_REPONSIVE -->responsive <!-- ENDIF --><!-- IF not S_NORMAL_HEADER -->simple-header<!-- ENDIF --> {BROWSER_CLASS}<!-- IF T_PORTAL_WIDTH --> fixed_width<!-- ENDIF --><!-- IF S_IN_ADMIN --> admin<!-- ELSE --> frontend<!-- ENDIF -->">
		{STATIC_HTMLCODE}
		<!-- IF S_NORMAL_HEADER -->
		<header>
			<div id="personalArea">
				<div id="personalAreaInner">
				<div id="personalAreaUser">
					<!-- IF not S_LOGGED_IN -->
					<ul>
						<li><a href="{EQDKP_CONTROLLER_PATH}Login{SEO_EXTENSION}{SID}" class="openLoginModal" onclick="return false;"><i class="fa fa-sign-in fa-lg"></i> {L_login}</a></li>
						<!-- IF U_REGISTER != "" --><li>{U_REGISTER}</li><!-- ENDIF -->
						
						<li>
							<div class="langswitch-tooltip-container">
								<a href="#" class="langswitch-tooltip-trigger tooltip-trigger" data-tooltip="langswitch-tooltip">{USER_LANGUAGE_NAME}</a>
								<ul class="dropdown-menu langswitch-tooltip" role="menu" id="langswitch-tooltip">
									<!-- BEGIN languageswitcher_row -->
									<li><a href="{languageswitcher_row.LINK}">{languageswitcher_row.LANGNAME}</a></li>
									<!-- END languageswitcher_row -->
								</ul>
							</div>
						</li>
						
						<!-- BEGIN personal_area_addition -->
						<li>{personal_area_addition.TEXT}</li>
						<!-- END personal_area_addition -->
					</ul>
					
					<!-- ELSE -->
						<ul>
							<li>
								<div class="user-tooltip-container">
									<a href="{EQDKP_CONTROLLER_PATH}Settings{SEO_EXTENSION}{SID}" class="user-tooltip-trigger tooltip-trigger" data-tooltip="user-tooltip"><span class="user-avatar user-avatar-border user-avatar-smallest"><img src="{USER_AVATAR}" alt="{USER_NAME}"/></span> <span class="hiddenSmartphone">{USER_NAME}</span></a>
									<ul class="dropdown-menu user-tooltip" role="menu" id="user-tooltip">
										<li><a href="{U_USER_PROFILE}">
												<div class="user-tooltip-avatar">
													<img src="{USER_AVATAR}" alt="{USER_NAME}"/>
												</div>
												<div class="user-tooltip-name">
													<span class="bold">{USER_NAME}</span><br />
													{L_my_profile}
												</div>
											</a>
										</li>
										<li class="tooltip-divider"></li>
										<li><a href="{EQDKP_CONTROLLER_PATH}Settings{SEO_EXTENSION}{SID}"><i class="fa fa-cog fa-lg"></i> {L_settings}</a></li>
										<li><a href="{U_LOGOUT}"><i class="fa fa-sign-out fa-lg"></i> {L_logout}</a></li>
									</ul>
								</div>
							</li>
							<!-- IF S_ADMIN --><li><a href="{EQDKP_ROOT_PATH}admin/{SID}"><i class="fa fa-cog fa-lg"></i> <span class="hiddenSmartphone">{L_menu_admin_panel}</span></a></li><!-- ENDIF -->
							
							<!-- IF U_CHARACTERS != "" --><li><a href="{U_CHARACTERS}"><i class="fa fa-group fa-lg"></i> <span class="hiddenSmartphone">{L_menu_members}</span></a></li><!-- ENDIF -->
							
							<!-- IF S_MYCHARS_POINTS and U_CHARACTERS != "" -->
								<li class="hiddenSmartphone">
									<div class="mychars-points-tooltip-container">
									<a class="mychars-points-tooltip-trigger tooltip-trigger" data-tooltip="mychars-points-tooltip"><i class="fa fa-trophy fa-lg"></i> <span class="mychars-points-target"></span></a>
									<ul class="dropdown-menu mychars-points-tooltip" role="menu" id="mychars-points-tooltip"><li>
										<table>
										<!-- BEGIN mychars_points -->
											<tr <!-- IF mychars_points.IS_MAIN -->class="main"<!-- ENDIF --> id="mcp{mychars_points.ID}">
												<td class="nowrap char hand"><span class="icons">{mychars_points.CHARICON}</span> {mychars_points.CHARNAME}</td>
												<td>{mychars_points.POOLNAME}</td>
												<td class="nowrap current">{mychars_points.CURRENT}</td>
												<td><a href="{mychars_points.CHARLINK}"><i class="fa fa-external-link fa-lg"></i></a></td>
											</tr>
										<!-- END mychars_points -->
										</table></li>
									</ul>
								</div>
								</li>
							<!-- ENDIF -->
							
							<li>
								<div class="notification-tooltip-container">
									<a class="notification-tooltip-trigger"><i class="fa fa-bolt fa-lg"></i> <span class="hiddenSmartphone">{L_notifications}</span></a>
									<span class="notification-tooltip-trigger bubble-red notification-bubble-red hand" <!-- IF NOTIFICATION_COUNT_RED == 0 -->style="display:none;"<!-- ENDIF --> >{NOTIFICATION_COUNT_RED}</span>
									<span class="notification-tooltip-trigger bubble-yellow notification-bubble-yellow hand" <!-- IF NOTIFICATION_COUNT_YELLOW == 0 -->style="display:none;"<!-- ENDIF -->>{NOTIFICATION_COUNT_YELLOW}</span>
									<span class="notification-tooltip-trigger bubble-green notification-bubble-green hand" <!-- IF NOTIFICATION_COUNT_GREEN == 0 -->style="display:none;"<!-- ENDIF -->>{NOTIFICATION_COUNT_GREEN}</span>
									<ul class="dropdown-menu notification-tooltip" role="menu" id="notification-tooltip-all">
										<li class="notification-action-bar"> 
											<div class="floatLeft">
												<span class="bubble-red notification-bubble-red notification-filter hand" <!-- IF NOTIFICATION_COUNT_RED == 0 -->style="display:none;"<!-- ENDIF --> >{NOTIFICATION_COUNT_RED}</span>
												<span class="bubble-yellow notification-bubble-yellow notification-filter hand" <!-- IF NOTIFICATION_COUNT_YELLOW == 0 -->style="display:none;"<!-- ENDIF -->>{NOTIFICATION_COUNT_YELLOW}</span>
												<span class="bubble-green notification-bubble-green notification-filter hand" <!-- IF NOTIFICATION_COUNT_GREEN == 0 -->style="display:none;"<!-- ENDIF -->>{NOTIFICATION_COUNT_GREEN}</span>
											</div>
												
											<div class="floatRight">
												<span class="hand notification-mark-all-read">{L_mark_all_as_read}</span> &bull; <span class="hand" onclick="window.location='{EQDKP_CONTROLLER_PATH}Settings{SEO_EXTENSION}{SID}#fragment-notifications'"><i class="fa fa-cog fa-lg"></i></span>
											</div>
											
											<div class="clear"></div>
										</li>
										<li class="tooltip-divider"></li>
										<li class="notification-content">
											<ul>{NOTIFICATIONS}</ul>
										</li>
										<li class="tooltip-divider"></li>
										<li class="notification-action-bar-btm"> <span class="hand" onclick="window.location='{EQDKP_CONTROLLER_PATH}Notifications{SEO_EXTENSION}{SID}'">{L_show_all}</span></li>
									</ul>
								</div>
							</li>
							
							
							<!-- IF S_SEARCH -->
							<li class="hiddenDesktop"><a href="{EQDKP_CONTROLLER_PATH}Search{SEO_EXTENSION}{SID}"><i class="fa fa-search"></i></a></li>
							<!-- ENDIF -->
							<!-- BEGIN personal_area_addition -->
							<li>{personal_area_addition.TEXT}</li>
							<!-- END personal_area_addition -->
						</ul>
					<!-- ENDIF -->
				</div>
				<div id="personalAreaTime" class="hiddenSmartphone">
					<ul>
						<li class="personalAreaTime"><i class="fa fa-clock-o fa-lg"></i> <span class="user_time">{USER_TIME}</span></li>
						<li><!-- IF S_SEARCH -->
						<form method="post" action="{EQDKP_CONTROLLER_PATH}Search{SEO_EXTENSION}{SID}" id="search_form">
							<input name="svalue" size="20" maxlength="30" class="input search" id="loginarea_search" type="text" value="{L_search}..."/>
							<button type="submit" class="search_button" value="" title="{L_search_do}">
								<i class="fa fa-search fa-lg"></i>
							</button>
						</form>
					<!-- ENDIF -->	</li>
					</ul>
				</div>
				<div class="clear"></div>
				</div>
			</div> <!-- close personalArea -->
		</header>
		<div id="wrapper">
			
		<header>
			<div id="header">
				<div id="logoContainer" class="{T_LOGO_POSITION}">
					<div id="logoArea">
						<!-- IF HEADER_LOGO -->
						<img src="{HEADER_LOGO}" alt="{MAIN_TITLE}" id="mainlogo" />
						<!-- ENDIF -->
					</div><!-- close logoArea -->
					
					<hgroup id="titles">
							<h1>{MAIN_TITLE}</h1><br />
							<h2>{SUB_TITLE}</h2>
					</hgroup><!-- close titles-->
				
					<div class="clear noheight">&nbsp;</div>
				</div>
				{PORTAL_BLOCK1}
			</div> <!-- close header-->
		</header>
		
		
		<section id="contentContainer">
			<a id="content"></a>
			<header>
				<nav id="nav">
					<div id="mainmenu">
						<div class="hiddenSmartphone">
							{MAIN_MENU}
							<div class="clear noheight">&nbsp;</div>
						</div>
						<div class="hiddenDesktop nav-mobile">
							<i class="fa fa-list hand" onclick="$('.nav-mobile-overlay').toggle();"></i>
							<div class="nav-mobile-overlay">
								<div class="nav-mobile-closebtn" onclick="$('.nav-mobile-overlay').toggle();">
									<i class="fa fa-lg fa-times hand"></i>
								</div>
							{MAIN_MENU_MOBILE}
							<!-- IF S_IN_ADMIN -->
							<div class="admin-headline"><i class="fa fa-cog fa-lg"></i> {L_menu_admin_panel}</div>
							{ADMIN_MENU_MOBILE}
							<!-- ELSE -->
								<!-- IF S_ADMIN --><div class="admin-headline"><a href="{EQDKP_ROOT_PATH}admin/{SID}"><i class="fa fa-cog fa-lg"></i> {L_menu_admin_panel}</a></div><!-- ENDIF -->
							<!-- ENDIF -->
							</div>
						</div>
					</div><!-- close mainmenu -->
					
					<!-- IF S_IN_ADMIN -->
					<div id="adminmenu">
						<div class="hiddenSmartphone">
							{ADMIN_MENU}
						</div>
					</div>
					<!-- ENDIF -->
				</nav>
			</header>
			
			<div class="portal">
				<div class="columnContainer">
					<!-- IF S_PORTAL_LEFT -->
					<aside class="first column portal-left" style="<!-- IF T_COLUMN_LEFT_WIDTH -->min-width:{T_COLUMN_LEFT_WIDTH};max-width:{T_COLUMN_LEFT_WIDTH};<!-- ELSE -->min-width: 200px;<!-- ENDIF -->">
						<div class="columnInner">
							{PORTAL_LEFT}
						</div> <!-- close first column -->
					</aside>
					<!-- ENDIF -->
					
					<article class="second column <!-- IF not S_PORTAL_RIGHT -->no_third_column<!-- ENDIF -->">
						<div class="columnInner">
							<!-- IF S_SHOW_COOKIE_HINT -->
							<div class="infobox infobox-large infobox-blue clearfix">
								<i class="fa-info-circle fa pull-left fa-2x"></i> {COOKIE_HINT}
							</div>
							<!-- ENDIF -->	
							<!-- BEGIN global_warnings -->
							<header>
								<div class="infobox infobox-large infobox-{global_warnings.CLASS} clearfix">
									<i class="{global_warnings.ICON} fa-4x pull-left"></i> {global_warnings.MESSAGE}
								</div>
							</header>
							<!-- END global_warnings -->	
							<aside id="portal-middle">
								{PORTAL_MIDDLE}
							</aside>
							<!-- ENDIF -->
							<div id="contentBody" class="{PAGE_CLASS}<!-- IF not S_NORMAL_HEADER --> simple-header <!-- ENDIF --><!-- IF not S_NORMAL_FOOTER --> simple-footer <!-- ENDIF -->">
								<div id="contentBody2">
									{GBL_CONTENT_BODY}
								</div>	
							</div><!-- close contentBody -->
							<!-- IF S_NORMAL_FOOTER -->
							<aside id="portal-footer">
							{PORTAL_BOTTOM}
							</aside>
							<footer class="debug">
							<!-- IF S_SHOW_QUERIES --><br />{DEBUG_TABS}<!-- ENDIF -->
							<!-- IF S_SHOW_DEBUG -->
							<br /><div class="center">
								<span class="debug-info">SQL Querys: {EQDKP_QUERYCOUNT} | in {EQDKP_RENDERTIME} | {EQDKP_MEM_PEAK} |
									<a href="http://validator.w3.org/check/referer" target="_top">XHTML Validate</a>
								</span>
							</div>
							<!-- ENDIF -->
							</footer>
						</div>
					</article><!-- close second column -->
					
					<!-- IF S_PORTAL_RIGHT -->
					<aside class="third column portal-right" style="<!-- IF T_COLUMN_RIGHT_WIDTH -->min-width:{T_COLUMN_RIGHT_WIDTH};max-width:{T_COLUMN_RIGHT_WIDTH}<!-- ELSE -->min-width: 200px;<!-- ENDIF -->">
						<div class="columnInner">
							{PORTAL_RIGHT}
						</div>
					</aside>
					<!-- ENDIF -->
				</div>
			</div>
			
			<footer id="contentFooter">
				<div class="floatLeft">
					<!-- IF S_REPONSIVE -->
					<div class="hiddenDesktop toggleResponsive"><a href="{SID}&toggleResponsive=desktop"><i class="fa fa-lg fa-desktop"></i> {L_desktop_version}</a></div>
					<!-- ELSE -->
					<div class="toggleResponsive"><a href="{SID}&toggleResponsive=mobile"><a href="{SID}&toggleResponsive=mobile"><i class="fa fa-lg fa-mobile-phone"></i> {L_mobile_version}</a></div>
					<!-- ENDIF -->
				</div>
				<div class="floatRight">
					<!-- IF not S_LOGGED_IN -->
					<a href="javascript:change_style();"><i class="fa fa-paint-brush"></i> {L_change_style}</a>
					<!-- ENDIF -->
					
					<!-- IF S_GLOBAL_RSSFEEDS -->
					<div class="rss-tooltip-container">
						<a class="rss-tooltip-trigger tooltip-trigger" data-tooltip="rss-tooltip"><i class="fa hand fa-rss fa-lg"></i></a>
						<ul class="dropdown-menu right-bottom rss-tooltip" role="menu" id="rss-tooltip">
							<!-- BEGIN global_rss_row -->
							<li><a href="{global_rss_row.LINK}"><i class="fa hand fa-rss fa-lg"></i> {global_rss_row.NAME}</a></li>
							<!-- END global_rss_row -->
						</ul>
					</div>
					<!-- ENDIF -->
				</div>
			</footer>
		</section>
		
		<footer id="footer">
				{PORTAL_BLOCK2}
				{EQDKP_PLUS_COPYRIGHT}
		</footer><!-- close footer -->
	</div><!-- close wrapper -->
	
	<!-- ELSE -->
		<!-- IF S_SHOW_QUERIES --><br />{DEBUG_TABS}<!-- ENDIF -->
	<!-- ENDIF -->

	<div id="dialog-login" title="{L_login}">
		<form method="post" action="{EQDKP_CONTROLLER_PATH}Login{SEO_EXTENSION}{SID}" name="login" id="login" class="fv_checkit">
			<!-- IF S_BRIDGE_INFO -->
			<div class="infobox infobox-large infobox-blue clearfix">
				<i class="fa fa-info-circle fa-4x pull-left"></i> {L_login_bridge_notice}
			</div>
			<!-- ENDIF -->
			<fieldset class="settings mediumsettings">
				<dl>
					<dt><label>{L_username}:</label></dt>
					<dd>
						<div class="input-icon">
							<i class="fa fa-user"></i><input type="text" name="username" size="30" maxlength="30" class="input username" id="username" placeholder="{L_username}" required />
							<div class="fv_msg" data-errormessage="{L_fv_required_user}"></div>
						</div>
						
					</dd>
				</dl>
				<dl>
					<dt><label>{L_password}:</label></dt>
					<dd>
						<div class="input-icon">
							<i class="fa fa-key"></i>
							<input type="password" name="password" pattern=".{3,}" size="30" maxlength="32" class="input password" id="password" placeholder="{L_password}" required />
							<div class="fv_msg" data-errormessage="{L_fv_required_password_pattern}"></div>
						</div>
						<!-- IF S_SHOW_PWRESET_LINK -->
						<br />{U_PWRESET_LINK}<br />
						<!-- ENDIF -->
						<br /><label><input type="checkbox" name="auto_login" />{L_remember_password}</label>
					</dd>
				</dl>
			</fieldset>
			<input type="text" name="{HONEYPOT_VALUE}" size="30" maxlength="30" class="userpass" />
			<button type="submit" name="login" class="mainoption"><i class="fa fa-sign-in"></i> {L_login}</button>
			<!-- IF AUTH_LOGIN_BUTTON != "" -->
			<br /><br />
			<fieldset class="settings mediumsettings">
				<legend>{L_login_use_authmethods}</legend>
				{AUTH_LOGIN_BUTTON}
			</fieldset>
			<!-- ENDIF -->
		</form>
	</div>
	<div class="reponsiveTestClass" style="display:none;"><!-- This div is for testing the responsiveness --></div>
	<script type="text/javascript">
		{JS_CODE_EOP}
		{JS_CODE_EOP2}
		
		//Reset Favicon, for Bookmarks
		$(window).on('unload', function() {
            if (typeof favicon !== 'undefined'){
				favicon.reset();
			}
   		 });
	</script>
	{FOOTER_CODE}
	<a id="bottom"></a>
	</body>
</html>
<!-- ENDIF -->