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
		<link rel="shortcut icon" href="{FAVICON}" type="{FAVICON_TYPE}" />
		<link rel="icon" href="{FAVICON}" type="{FAVICON_TYPE}" />
		<link rel="apple-touch-icon" href="{TEMPLATE_PATH}/images/apple-touch-icon.png" />
		{RSS_FEEDS}
		<!-- LISTENER head -->
		<style type="text/css">
			{CSS_CODE}
			<!-- IF T_BACKGROUND_TYPE > 0 -->
			body {
				background:#000000 url('{TEMPLATE_BACKGROUND}') no-repeat center top;
				background-attachment: {T_BACKGROUND_POSITION};
			}
			<!-- ENDIF -->
		</style>

		<script type="text/javascript">
			//<![CDATA[
			{JS_CODE}
			//]]>
		</script>
	</head>
	<body id="top" data-template="{TEMPLATE_CLASS}" class="<!-- IF S_REPONSIVE -->responsive <!-- ENDIF --><!-- IF not S_NORMAL_HEADER -->simple-header<!-- ENDIF --> {BROWSER_CLASS}<!-- IF T_PORTAL_WIDTH --> fixed_width<!-- ENDIF --><!-- IF S_IN_ADMIN --> admin<!-- ELSE --> frontend<!-- ENDIF -->">
		<!-- LISTENER body_top -->

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
								<a href="{EQDKP_CONTROLLER_PATH}Settings{SEO_EXTENSION}{SID}" class="user-tooltip-trigger tooltip-trigger" data-tooltip="user-tooltip"><span class="user-avatar user-avatar-border user-avatar-smallest"><img src="{USER_AVATAR}" alt="{USER_NAME}"/></span> <span class="hiddenSmartphone">{USER_NAME}<!-- IF USER_IS_AWAY --> <i class="fa fa-suitcase fa-lg"></i><!-- ENDIF --></span></a>
								<ul class="dropdown-menu user-tooltip" role="menu" id="user-tooltip">
									<li>
										<a href="{U_USER_PROFILE}">
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
									<!-- BEGIN user_tooltip_addition -->
									<li class="{user_tooltip_addition.CLASS}">{user_tooltip_addition.TEXT}</li>
									<!-- END user_tooltip_addition -->
									<!-- IF USER_IS_AWAY -->
									<li class="user_tooltip_awaymode"><a href="{EQDKP_CONTROLLER_PATH}Settings{SEO_EXTENSION}{SID}#fragment-calendar"><i class="fa fa-suitcase fa-lg"></i> {L_calendar_user_is_away}</a></li>
									<!-- ENDIF -->
									<li><a href="{EQDKP_CONTROLLER_PATH}Settings{SEO_EXTENSION}{SID}"><i class="fa fa-cog fa-lg"></i> {L_settings}</a></li>
									<li><a href="{U_LOGOUT}"><i class="fa fa-sign-out fa-lg"></i> {L_logout}</a></li>
								</ul>
							</div>
						</li>
						<!-- IF S_ADMIN -->
						<li>
							<div class="admin-tooltip-container">
								<a href="{EQDKP_ROOT_PATH}admin/{SID}" class="admin-tooltip-trigger tooltip-trigger" data-tooltip="admin-tooltip"><i class="fa fa-cog fa-lg"></i> <span class="hiddenSmartphone">{L_menu_admin_panel}</span></a>
								<ul class="dropdown-menu admin-tooltip" role="menu" id="admin-tooltip">
									<li><a href="{EQDKP_ROOT_PATH}admin/{SID}"><i class="fa fa-cog fa-lg"></i> {L_menu_admin_panel}</a></li>
									<li class="tooltip-divider"></li>
									<div class="nav-header floatLeft">{L_favorits}</div>
									<div class="nav-header floatRight hand" title="{L_settings}"><i class="fa fa-cog fa-lg" onclick="window.location='{EQDKP_ROOT_PATH}admin/manage_menus.php{SID}&tab=1'"></i></div>
									<!-- BEGIN admin_tooltip -->
									<li><a href="{EQDKP_ROOT_PATH}{admin_tooltip.LINK}"><i class="fa {admin_tooltip.ICON} fa-lg"></i> {admin_tooltip.TEXT}</a></li>
									<!-- END admin_tooltip -->
								</ul>
							</div>
							<!-- ENDIF -->

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
									<a class="notification-tooltip-trigger"><i class="fa fa-bell fa-lg"></i> <span class="hiddenSmartphone">{L_notifications}</span></a>
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
				<!-- LISTENER header_top -->
				<div id="logoContainer" class="{T_LOGO_POSITION}">
					<div id="logoArea">
						<!-- IF HEADER_LOGO -->
						<a href="{EQDKP_CONTROLLER_PATH}{SID}"><img src="{HEADER_LOGO}" alt="{MAIN_TITLE}" id="mainlogo" /></a>
						<!-- ENDIF -->
					</div><!-- close logoArea -->

					<hgroup id="titles">
							<h1>{MAIN_TITLE}</h1><br />
							<h2>{SUB_TITLE}</h2>
					</hgroup><!-- close titles-->
					<!-- LISTENER logo_container -->
					<div class="clear noheight">&nbsp;</div>
				</div>
				<div class="portal_block1">{PORTAL_BLOCK1}</div>
				<!-- LISTENER header_bottom -->
			</div> <!-- close header-->
		</header>


		<section id="contentContainer">
			<a id="content"></a>
			<!-- LISTENER content_container_top -->
			<header>
				<nav id="nav">
					<div id="mainmenu">
						<div class="hiddenSmartphone">
							{MAIN_MENU}
							<div class="clear noheight">&nbsp;</div>
						</div>
						<div class="hiddenDesktop nav-mobile">
							<i class="fa fa-list hand" onclick="$('.nav-mobile .mobile-overlay').toggle();"></i>
							<div class="mobile-overlay">
								<div class="overlay-header">
									<a class="title" href="{EQDKP_CONTROLLER_PATH}{SID}">
										<!-- IF HEADER_LOGO --><img src="{HEADER_LOGO}" alt="{MAIN_TITLE}" /><!-- ELSE -->{MAIN_TITLE}<!-- ENDIF -->
									</a>
									<div class="close" onclick="$('.nav-mobile .mobile-overlay').toggle();"><i class="fa fa-times"></i></div>
								</div>
								<div class="overlay-content">
									<nav class="mainmenu-mobile-wrapper"><div class="heading">{L_menu_eqdkp}</div>{MAIN_MENU_MOBILE}</nav>
									<!-- IF S_IN_ADMIN -->
									<nav class="adminmenu-mobile-wrapper"><div class="heading">{L_menu_admin_panel}</div>{ADMIN_MENU_MOBILE}</nav>
									<!-- ENDIF -->
								</div>
								<div class="overlay-footer">
									<!-- IF S_ADMIN and not S_IN_ADMIN --><a href="{EQDKP_ROOT_PATH}admin/{SID}"><i class="fa fa-cog fa-lg"></i> {L_menu_admin_panel}</a><!-- ENDIF -->
								</div>
							</div>
						</div>
						<!-- LISTENER mainmenu -->
					</div><!-- close mainmenu -->

					<!-- IF S_IN_ADMIN -->
					<div id="adminmenu">
						<div class="hiddenSmartphone">
							{ADMIN_MENU}
						</div>
						<!-- LISTENER adminmenu -->
					</div>
					<!-- ENDIF -->
				</nav>
			</header>

			<div class="portal">
				<!-- LISTENER portal_top -->
				<div class="columnContainer">
					<!-- IF S_PORTAL_LEFT -->
					<aside class="first column portal-left" style="<!-- IF T_COLUMN_LEFT_WIDTH -->min-width:{T_COLUMN_LEFT_WIDTH};max-width:{T_COLUMN_LEFT_WIDTH};<!-- ELSE -->min-width: 200px;<!-- ENDIF -->">
						<div class="columnInner">
							<!-- LISTENER portal_left_top -->
							{PORTAL_LEFT}
							<!-- LISTENER portal_left_bottom -->
						</div> <!-- close first column -->
					</aside>
					<!-- ENDIF -->

					<article class="second column <!-- IF not S_PORTAL_RIGHT -->no_third_column<!-- ENDIF -->">
						<div class="columnInner">
							<!-- LISTENER content_middle_top -->

							<!-- IF S_SHOW_COOKIE_HINT -->
							<div class="infobox infobox-large infobox-blue clearfix">
								<i class="fa-info-circle fa pull-left fa-2x"></i> {COOKIE_HINT}
								<i class="fa-times fa pull-right hand" onclick="$(this).parent().hide()"></i>
							</div>
							<!-- ENDIF -->

							<!-- BEGIN global_warnings -->
							<header>
								<div class="infobox infobox-large infobox-{global_warnings.CLASS} clearfix">
									<i class="{global_warnings.ICON} fa-4x pull-left"></i> {global_warnings.MESSAGE}
									<!-- IF global_warnings.S_DISMISS -->
									<i class="fa-times fa pull-right hand" onclick="$(this).parent().hide()"></i>
									<!-- ENDIF -->
								</div>
							</header>
							<!-- END global_warnings -->
							<aside id="portal-middle">
								<!-- LISTENER portal-middle-top -->
								{PORTAL_MIDDLE}
								<!-- LISTENER portal-middle-bottom -->
							</aside>
							<!-- ENDIF -->
							<div id="contentBody" class="{PAGE_CLASS}<!-- IF not S_NORMAL_HEADER --> simple-header <!-- ENDIF --><!-- IF not S_NORMAL_FOOTER --> simple-footer <!-- ENDIF -->">
								<div id="contentBody2">
									<!-- LISTENER content_body_top -->
									{GBL_CONTENT_BODY}
									<!-- LISTENER content_body_bottom -->
								</div>
							</div><!-- close contentBody -->
							<!-- LISTENER content_middle_bottom -->

							<!-- IF S_NORMAL_FOOTER -->
							<aside id="portal-footer">
							<!-- LISTENER portal-bottom-top -->
							{PORTAL_BOTTOM}
							<!-- LISTENER portal-bottom-bottom -->
							</aside>
							<footer class="debug">
							<!-- LISTENER content-footer-debug -->
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
							<!-- LISTENER portal-right-top -->
							{PORTAL_RIGHT}
							<!-- LISTENER portal-right-bottom -->
						</div>
					</aside>
					<!-- ENDIF -->
				</div>
			</div>

			<footer id="contentFooter">
				<!-- LISTENER content-footer-top -->
				<div class="floatLeft">
					<!-- IF S_REPONSIVE -->
					<div class="hiddenDesktop toggleResponsive"><a href="{SID}&toggleResponsive=desktop"><i class="fa fa-lg fa-desktop"></i> {L_desktop_version}</a></div>
					<!-- ELSE -->
					<div class="toggleResponsive"><a href="{SID}&toggleResponsive=mobile"><a href="{SID}&toggleResponsive=mobile"><i class="fa fa-lg fa-mobile-phone"></i> {L_mobile_version}</a></div>
					<!-- ENDIF -->

					<!-- LISTENER content-footer-left -->
				</div>
				<div class="floatRight">
					<!-- LISTENER content-footer-right -->

					<!-- IF not S_LOGGED_IN and S_STYLECHANGER -->
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
				<!-- LISTENER footer_top -->
				<div class="portal_block2">{PORTAL_BLOCK2}</div>
				{EQDKP_PLUS_COPYRIGHT}
				<!-- LISTENER footer_bottom -->
		</footer><!-- close footer -->
	</div><!-- close wrapper -->

	<!-- ELSE -->
		<!-- IF S_SHOW_QUERIES --><br />{DEBUG_TABS}<!-- ENDIF -->
		<!-- LISTENER debug -->
	<!-- ENDIF -->

	<!-- IF not S_LOGGED_IN -->
	<div id="dialog-login" title="{L_login}">
		<form method="post" action="{EQDKP_CONTROLLER_PATH}Login{SEO_EXTENSION}{SID}" name="login" id="login" class="fv_checkit">
			<!-- LISTENER login_popup -->
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
						</div>

					</dd>
				</dl>
				<dl>
					<dt><label>{L_password}:</label></dt>
					<dd>
						<div class="input-icon">
							<i class="fa fa-key"></i>
							<input type="password" name="password" pattern=".{3,}" size="30" maxlength="32" class="input password" id="password" placeholder="{L_password}" required />
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
	<!-- ENDIF -->

	<div class="reponsiveTestClass" style="display:none;"><!-- This div is for testing the responsiveness --></div>
	<script type="text/javascript">
		//<![CDATA[
			<!-- IF not S_LOGGED_IN -->
			$(function() {
				/* Login Dialog */
				$( "#dialog-login" ).dialog({
					height: <!-- IF S_BRIDGE_INFO -->450<!-- ELSE -->350<!-- ENDIF -->,
					width: 530,
					modal: true,
					autoOpen: false,
				});
			});
			<!-- ENDIF -->

			<!-- IF S_NORMAL_HEADER -->

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


			function change_style(){
				$('<div>').html('<div class="style-switch-container"><i class="fa fa-lg fa-spin fa-spinner"></i></div>').dialog(
					{ open: function( event, ui ) {
						$.get("{EQDKP_ROOT_PATH}exchange.php{SID}&out=styles", function(data){
							$('.style-switch-container').html(data);
						});
					}, title: {L_change_style|jsencode}, width: 600, height: 500}
				);
			}

			/* User clock */
			var user_clock_format = "dddd, "+mmocms_user_dateformat_long+" "+ mmocms_user_timeformat;
			var mymoment = moment(mmocms_user_timestamp_atom).utcOffset(mmocms_user_timezone);

			$(function() {
				$('.notification-mark-all-read').on('click', function() {
					$('.notification-content ul').html({L_notification_none|jsencode});
					$('.notification-bubble-red, .notification-bubble-yellow, .notification-bubble-green').hide();
					notification_favicon(0, 0, 0);
					$.get(mmocms_controller_path+"Notifications"+mmocms_seo_extension+mmocms_sid+"&markallread");
				});

				//Update Favicon
				favicon = new Favico({animation:'none'});
				notification_favicon({NOTIFICATION_COUNT_RED}, {NOTIFICATION_COUNT_YELLOW}, {NOTIFICATION_COUNT_GREEN});
			});
			<!-- ELSE -->
				<!-- JS for simple header. Above is for normal header only -->
			<!-- ENDIF -->

			{JS_CODE_EOP}

			//Reset Favicon, for Bookmarks
			$(window).on('unload', function() {
				if (typeof favicon !== 'undefined'){ favicon.reset(); }
			});
		//]]>
	</script>
	{FOOTER_CODE}
	<!-- LISTENER body_bottom -->
	<a id="bottom"></a>
	</body>
</html>
<!-- ENDIF -->
