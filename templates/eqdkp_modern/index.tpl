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
					
			var user_timestamp_atom = "{USER_TIMESTAMP_ATOM}";
			var user_clock_format = "dddd, {USER_DATEFORMAT_LONG} {USER_TIMEFORMAT}";
			var user_timezone = "{USER_TIMEZONE}";
			
			var mymoment = moment(user_timestamp_atom).zone(user_timezone);
			function user_clock(){	
				var mydate = mymoment.format(user_clock_format);
				$('.user_time').html(mydate);
				mymoment.add('s', 1);
				window.setTimeout("user_clock()", 1000);
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
					window.location="{EQDKP_CONTROLLER_PATH}Settings{SEO_EXTENSION}{SID}";		
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
	<body id="top" class="responsive <!-- IF not S_NORMAL_HEADER -->simple-header<!-- ENDIF --> {BROWSER_CLASS}">
		{STATIC_HTMLCODE}
		<!-- IF S_NORMAL_HEADER -->
		<header>
			<div id="personalArea">
				<div id="personalAreaUser">
					<!-- IF not S_LOGGED_IN -->
					<ul>
						<li><a href="{EQDKP_CONTROLLER_PATH}Login{SEO_EXTENSION}{SID}" class="openLoginModal" onclick="return false;"><i class="fa fa-sign-in"></i> {L_login}</a></li>
						<!-- IF U_REGISTER != "" --><li>{U_REGISTER}</li><!-- ENDIF -->
						<!-- BEGIN personal_area_addition -->
						<li>{personal_area_addition.TEXT}</li>
						<!-- END personal_area_addition -->
					</ul>
					
					<!-- ELSE -->					
						<ul>
							<li>
								<div class="user-tooltip-container">
									<a href="{EQDKP_CONTROLLER_PATH}Settings{SEO_EXTENSION}{SID}" class="user-tooltip-trigger"><i class="fa fa-user"></i> {USER_NAME}</a>
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
										<li><a href="{EQDKP_CONTROLLER_PATH}Settings{SEO_EXTENSION}{SID}"><i class="fa fa-cog"></i> {L_settings}</a></li>
										<li><a href="{U_LOGOUT}"><i class="fa fa-sign-out"></i> {L_logout}</a></li>
									</ul>
								</div>
							</li>
							<!-- IF S_ADMIN --><li><a href="{EQDKP_ROOT_PATH}admin/{SID}"><i class="fa fa-cog"></i> {L_menu_admin_panel}</a></li><!-- ENDIF -->
							
							<!-- IF U_CHARACTERS != "" --><li><a href="{U_CHARACTERS}"><i class="fa fa-group"></i> {L_menu_members}</a></li><!-- ENDIF -->
							<li>
								<div class="notification-tooltip-container">
									<a class="notification-tooltip-trigger" data-type="all"><i class="fa fa-bolt"></i> {L_notifications}</a>
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
							<li class="hiddenDesktop"><a href="{EQDKP_CONTROLLER_PATH}Search{SEO_EXTENSION}{SID}"><i class="fa fa-search"></i> {L_search}</a></li>
							<!-- ENDIF -->
							<!-- BEGIN personal_area_addition -->
							<li>{personal_area_addition.TEXT}</li>
							<!-- END personal_area_addition -->
						</ul>				
					<!-- ENDIF -->
				</div>
				<div id="personalAreaTime" class="hiddenSmartphone">
					<ul>
						<li class="personalAreaTime"><i class="fa fa-clock-o"></i> <span class="user_time">{USER_TIME}</span></li>
						<li><!-- IF S_SEARCH -->
						<form method="post" action="{EQDKP_CONTROLLER_PATH}Search{SEO_EXTENSION}{SID}" id="search_form">
							<input name="svalue" size="20" maxlength="30" class="input search" id="loginarea_search" type="text" value="{L_search}..."/>
							<button type="submit" class="search_button" value="" title="{L_search_do}">
								<i class="fa fa-search"></i>
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
					<div id="logoArea" class="hiddenSmartphone">
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
						<div class="hiddenDesktop mainmenu">
							<i class="fa fa-list"></i>
							{MAIN_MENU_SELECT}
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
		
		</section>
		
		<footer id="footer">
				{EQDKP_PLUS_COPYRIGHT}
		</footer><!-- close footer -->
	</div><!-- close wrapper -->
	
	<!-- ELSE -->
		<!-- IF S_SHOW_QUERIES --><br />{DEBUG_TABS}<!-- ENDIF -->
	<!-- ENDIF -->

	<div id="dialog-login" title="{L_login}">
		<form method="post" action="{EQDKP_CONTROLLER_PATH}Login{SEO_EXTENSION}{SID}" name="login" id="login">
			<!-- IF S_BRIDGE_INFO -->
			<div class="infobox infobox-large infobox-blue clearfix">
				<i class="fa fa-info-circle fa-4x pull-left"></i> {L_login_bridge_notice}
			</div>
			<!-- ENDIF -->
			<fieldset class="settings mediumsettings">	
				<dl>
					<dt><label>{L_username}:</label></dt>
					<dd><div class="input-icon"><i class="fa fa-user"></i><input type="text" name="username" size="30" maxlength="30" class="input required username" id="username" placeholder="{L_username}" /></div></dd>
				</dl>
				<dl>
					<dt><label>{L_password}:</label></dt>
					<dd><div class="input-icon"><i class="fa fa-key"></i><input type="password" name="password" size="30" maxlength="32" class="input required password" id="password" placeholder="{L_password}"/></div>
						<!-- IF S_SHOW_PWRESET_LINK -->
						<br />{U_PWRESET_LINK}<br />
						<!-- ENDIF -->
						<br /><label><input type="checkbox" name="auto_login" />{L_remember_password}</label>
					</dd>
				</dl>
			</fieldset>
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
	
	<script type="text/javascript">
		{JS_CODE_EOP}
		{JS_CODE_EOP2}
	</script>		
	<a id="bottom"></a>
	</body>
</html>
<!-- ENDIF -->