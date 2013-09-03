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
		</style>
		<script type="text/javascript">
			//<![CDATA[
			{JS_CODE}
					
			var user_timestamp = "{USER_TIMESTAMP}";
			var user_daynames = {USER_DAYNAMES};
			var user_monthnames = {USER_MONTHNAMES};
			var user_clock_format = "l, {USER_DATEFORMAT_LONG} {USER_TIMEFORMAT}";
			var date = new Date(user_timestamp);
			function user_clock(){
				var zeroPad = function(number) {
					 return ("0"+number).substr(-2,2);
				}

				var dateMarkers = {
					l: ['getDay',function(v) { return user_daynames[((v+6)%7)]; }],
					j: ['getDate'],
					d: ['getDate',function(v) { return zeroPad(v)}],
					F: ['getMonth',function(v) { return user_monthnames[v]; }],
					m: ['getMonth',function(v) { return zeroPad(v+1)}],
					n: ['getMonth',function(v) { return (v+1)}],
					Y: ['getFullYear'],
					y: ['getFullYear'],
					h: ['getHours',function(v) { return zeroPad(v%12)}],
					H: ['getHours',function(v) { return zeroPad(v)}],
					g: ['getHours',function(v) { return (v%12)}],
					G: ['getHours'],
					i: ['getMinutes',function(v) { return zeroPad(v)}],
					a: ['getHours',function(v) { if(v >= 12) {return "pm";} else {return "am";}}],
					A: ['getHours',function(v) { if(v >= 12) {return "PM";} else {return "AM";}}],
				};
				   
				   
				var dateTxt = this.user_clock_format.replace(/(.)/g, function(m, p) {
					if (dateMarkers[p] == undefined){
						return p;
					}

					var rv = date[(dateMarkers[p])[0]]();
						
					if ( dateMarkers[p][1] != null ) rv = dateMarkers[p][1](rv);

					return rv

				});

				$('.user_time').html(dateTxt);
				date.setMinutes(date.getMinutes() + 1);
				window.setTimeout("user_clock()", 60000); // 60 seconds
			}
			
			$(document).ready(function() {
				user_clock();
			
				$( "#dialog-login" ).dialog({
					height: <!-- IF S_BRIDGE_INFO -->410<!-- ELSE -->310<!-- ENDIF -->,
					width: 530,
					modal: true,
					autoOpen: false,
				});
				$( ".openLoginModal" ).on('click', function() {
					$( "#dialog-login" ).dialog( "open" );
				});
				
				$('.notification-tooltip-trigger').on('click', function(event){
					var dest = $(this).attr('data-type');
					$(".notification-tooltip").hide('fast');
					$("#notification-tooltip-"+dest).show('fast');
					$(document).on('click', function(event) {
						var count = $(event.target).parents('.notification-tooltip-container').length;									
						if (count == 0){
							$(".notification-tooltip").hide('fast');
						}
					});
					
				});
				
				$('.user-tooltip-trigger').on('click', function(event){
					event.preventDefault();
					$("#user-tooltip").show('fast');
					$(document).on('click', function(event) {
						var count = $(event.target).parents('.user-tooltip-container').length;									
						if (count == 0){
							$("#user-tooltip").hide('fast');
						}
					});					
				});
				
				$('.user-tooltip-trigger').on('dblclick', function(event){
					$("#user-tooltip").hide('fast');
					window.location="{EQDKP_CONTROLLER_PATH}Settings/{SID}";		
				});
				
				$('ul.mainmenu li.link_li_indexphp a.link_indexphp').html('');
				$('ul.mainmenu').addClass('sf-menu');
				jQuery('ul.mainmenu').superfish({
						delay:		400,
						animation:	{opacity:'show',height:'show'},
						speed:		'fast'
					});
			});
			//]]>
		</script>
	</head>
	<body id="top" class="<!-- IF not S_NORMAL_HEADER -->simple-header<!-- ENDIF --> {BROWSER_CLASS}">
		{STATIC_HTMLCODE}
		<!-- IF S_NORMAL_HEADER -->
		<header>
			<div id="personalArea">
				<div id="personalAreaUser">
					<!-- IF not S_LOGGED_IN -->
					<ul>
						<li><a href="{EQDKP_CONTROLLER_PATH}Login/{SID}" class="openLoginModal" onclick="return false;"><i class="icon-signin"></i>{L_login}</a></li>
						<!-- IF U_REGISTER != "" --><li>{U_REGISTER}</li><!-- ENDIF -->
						<!-- BEGIN personal_area_addition -->
						<li>{personal_area_addition.TEXT}</li>
						<!-- END personal_area_addition -->
					</ul>
					
					<!-- ELSE -->					
						<ul>
							<li>
								<div class="user-tooltip-container">
									<a href="{EQDKP_CONTROLLER_PATH}Settings/{SID}" class="user-tooltip-trigger"><i class="icon-user"></i>{USER_NAME}</a>
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
										<li><a href="{EQDKP_CONTROLLER_PATH}Settings/{SID}"><i class="icon-cog"></i>{L_settings}</a></li>
										<li><a href="{U_LOGOUT}"><i class="icon-signout"></i>{L_logout}</a></li>
									</ul>
								</div>
							</li>
							<!-- IF S_ADMIN --><li><a href="{EQDKP_ROOT_PATH}admin/{SID}"><i class="icon-cog"></i>{L_menu_admin_panel}</a></li><!-- ENDIF -->
							
							<!-- IF U_CHARACTERS != "" --><li><a href="{U_CHARACTERS}"><i class="icon-group"></i>{L_menu_members}</a></li><!-- ENDIF -->
							<li>
								<div class="notification-tooltip-container">
									<a class="notification-tooltip-trigger" data-type="all"><i class="icon-bolt"></i>{L_notifications}</a>
									<ul class="dropdown-menu notification-tooltip" role="menu" id="notification-tooltip-all">
										<li><!-- IF NOTIFICATION_COUNT_TOTAL == 0 -->{L_notification_none}<!-- ENDIF -->
											<!-- IF NOTIFICATION_COUNT_RED > 0 -->
											<h2><span class="notification-bubble-red">{NOTIFICATION_COUNT_RED}</span>{L_notification_red_prio}</h2>
											<ul>{NOTIFICATION_RED}</ul>
											<!-- ENDIF -->
											<!-- IF NOTIFICATION_COUNT_YELLOW > 0 -->
											<h2><span class="notification-bubble-yellow">{NOTIFICATION_COUNT_YELLOW}</span>{L_notification_yellow_prio}</h2>
											<ul>{NOTIFICATION_YELLOW}</ul>
											<!-- ENDIF -->
											<!-- IF NOTIFICATION_COUNT_GREEN > 0 -->
											<h2><span class="notification-bubble-green">{NOTIFICATION_COUNT_GREEN}</span>{L_notification_green_prio}</h2>
											<ul>{NOTIFICATION_GREEN}</ul>
											<!-- ENDIF -->
										</li>
									</ul>
								</div>
								<!-- IF NOTIFICATION_COUNT_RED > 0 -->
								<div class="notification-tooltip-container">
								<a class="notification-tooltip-trigger" data-type="red"><span class="notification-bubble-red">{NOTIFICATION_COUNT_RED}</span></a>
									<ul class="dropdown-menu notification-tooltip" role="menu" id="notification-tooltip-red">
										{NOTIFICATION_RED}
									</ul>
								</div>
								<!-- ENDIF -->
								<!-- IF NOTIFICATION_COUNT_YELLOW > 0 -->
								<div class="notification-tooltip-container">
								<a class="notification-tooltip-trigger" data-type="yellow"><span class="notification-bubble-yellow">{NOTIFICATION_COUNT_YELLOW}</span></a>
									<ul class="dropdown-menu notification-tooltip" role="menu" id="notification-tooltip-yellow">
										{NOTIFICATION_YELLOW}
									</ul>
								</div>
								<!-- ENDIF -->
								<!-- IF NOTIFICATION_COUNT_GREEN > 0 -->
								<div class="notification-tooltip-container">
								<a class="notification-tooltip-trigger" data-type="green"><span class="notification-bubble-green">{NOTIFICATION_COUNT_GREEN}</span></a>
									<ul class="dropdown-menu notification-tooltip" role="menu" id="notification-tooltip-green">
										{NOTIFICATION_GREEN}
									</ul>
								</div>
								<!-- ENDIF -->
							</li>
							<!-- IF S_SEARCH -->
							<li class="hiddenDesktop"><a href="{EQDKP_ROOT_PATH}search.php{SID}"><i class="icon-search"></i>{L_search}</a></li>
							<!-- ENDIF -->
							<!-- BEGIN personal_area_addition -->
							<li>{personal_area_addition.TEXT}</li>
							<!-- END personal_area_addition -->
						</ul>				
					<!-- ENDIF -->
				</div>
				<div id="personalAreaTime" class="hiddenSmartphone">
					<ul>
						<li class="personalAreaTime"><i class="icon-time"></i><span class="user_time">{USER_TIME}</span></li>
						<li><!-- IF S_SEARCH -->
						<form method="post" action="{EQDKP_CONTROLLER_PATH}Search/{SID}" id="search_form">
							<input name="svalue" size="20" maxlength="30" class="input search" id="loginarea_search" type="text" value="{L_search}..."/>
							<button type="submit" class="search_button" value="" title="{L_search_do}">
								<i class="icon-search"></i>
							</button>
						</form>
					<!-- ENDIF -->	</li>
					</ul>			
				</div>
				<div class="clear"></div>
			</div> <!-- close personalArea -->
		</header>
		<div id="wrapper" <!-- IF T_PORTAL_WIDTH -->class="fixed_width"<!-- ENDIF -->>
			
		<header>
			<div id="header">
				<div id="logoContainer" class="{T_LOGO_POSITION}">
					<div id="logoArea">
						<img src="{HEADER_LOGO}" alt="{MAIN_TITLE}" id="mainlogo" />
					</div><!-- close logoArea -->
					
					<hgroup id="titles">
							<h1>{MAIN_TITLE}</h1><br />
							<h2>{SUB_TITLE}</h2>
					</hgroup><!-- close titles-->
				
					<div class="clear noheight">&nbsp;</div>
				</div>
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
						<div class="hiddenDesktop">
							<select><option>Navigation</option></select>
						</div>
					</div><!-- close mainmenu -->
					
					<!-- IF S_IN_ADMIN -->
					<div id="adminmenu">
						<div class="hiddenSmartphone">
							{ADMIN_MENU}
						</div>
						<div class="hiddenDesktop">
							<select><option>Admin Navigation</option></select>
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
							<!-- BEGIN global_warnings -->
							<header>
								<div class="{global_warnings.CLASS} roundbox">
									<div class="{global_warnings.ICON}">{global_warnings.MESSAGE}</div>
								</div>
								<br />
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
		
		</section>
		
		<footer id="footer">
				{EQDKP_PLUS_COPYRIGHT}
		</footer><!-- close footer -->
	</div><!-- close wrapper -->
	
	<!-- ELSE -->
		<!-- IF S_SHOW_QUERIES --><br />{DEBUG_TABS}<!-- ENDIF -->
	<!-- ENDIF -->

	<div id="dialog-login" title="{L_login}">
		<form method="post" action="{EQDKP_CONTROLLER_PATH}Login/{SID}" name="login" id="login">
			<!-- IF S_BRIDGE_INFO -->
			<div class="bluebox roundbox">
				<div class="icon_info">{L_login_bridge_notice}</div>
			</div>
			<!-- ENDIF -->
			<fieldset class="settings mediumsettings">	
				<dl>
					<dt><label>{L_username}:</label></dt>
					<dd><div class="input-icon"><i class="icon-user"></i><input type="text" name="username" size="30" maxlength="30" class="input required username" id="username"/></div></dd>
				</dl>
				<dl>
					<dt><label>{L_password}:</label></dt>
					<dd><div class="input-icon"><i class="icon-key"></i><input type="password" name="password" size="30" maxlength="32" class="input required password" id="password"/></div>
						<!-- IF S_SHOW_PWRESET_LINK -->
						<br />{U_PWRESET_LINK}<br />
						<!-- ENDIF -->
						<br /><label><input type="checkbox" name="auto_login" />{L_remember_password}</label>
					</dd>
				</dl>
			</fieldset>
			<button type="submit" name="login" class="mainoption"><i class="icon-signin"></i> {L_login}</button>
			<!-- IF AUTH_LOGIN_BUTTON != "" -->
			<br /><br />
			<fieldset class="settings mediumsettings">
				<legend>{L_login_use_authmethods}</legend>
				{AUTH_LOGIN_BUTTON}
			</fieldset>
			<!-- ENDIF -->
		</form>
	</div>
	
	<script type="text/javascript">
		{JS_CODE_EOP}
		{JS_CODE_EOP2}
	</script>		
	<a id="bottom"></a>
	</body>
</html>
<!-- ENDIF -->