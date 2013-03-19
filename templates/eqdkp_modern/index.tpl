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
			
			$(document).ready(function() {
				$( "#dialog-login" ).dialog({
					height: 310,
					width: 500,
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
				$('ul.mainmenu li.link_li_indexphp a.link_indexphp').html('');
				$('ul.mainmenu').addClass('sf-menu');
				jQuery('ul.mainmenu').supersubs({
						minWidth:	14,
						maxWidth:	40,
						extraWidth:	2
					}).superfish({
						delay:		400,
						animation:	{opacity:'show',height:'show'},
						speed:		'fast'
					});
			});
			//]]>
		</script>
	</head>
	<body id="top" <!-- IF not S_NORMAL_HEADER -->class="simple-header"<!-- ENDIF -->>
		{STATIC_HTMLCODE}
		<!-- IF S_NORMAL_HEADER -->
		<header>
			<div id="personalArea">
				<div id="personalAreaUser">
					<!-- IF not S_LOGGED_IN -->
					<ul>
						<li><a href="#" class="openLoginModal"><i class="icon-signin"></i>{L_login}</a></li>
						<!-- IF U_REGISTER != "" --><li>{U_REGISTER}</li><!-- ENDIF -->
						<!-- BEGIN personal_area_addition -->
						<li>{personal_area_addition.TEXT}</li>
						<!-- END personal_area_addition -->
					</ul>
					
					<!-- ELSE -->					
						<ul>
							<li><a href="{EQDKP_ROOT_PATH}settings.php{SID}"><i class="icon-user"></i>{USER_NAME}</a></li>
							<!-- IF S_ADMIN --><li><a href="{EQDKP_ROOT_PATH}admin/index.php{SID}"><i class="icon-cog"></i>{L_menu_admin_panel}</a></li><!-- ENDIF -->
							<li><a href="{EQDKP_ROOT_PATH}login.php{SID}&amp;logout=true&amp;link_hash={CSRF_LOGOUT_TOKEN}"><i class="icon-signout"></i>{L_logout}</a></li>
							<!-- IF U_CHARACTERS != "" --><li><a href="{U_CHARACTERS}"><i class="icon-group"></i>{L_menu_members}</a></li><!-- ENDIF -->
							<li>
								<div class="notification-tooltip-container">
									<a class="notification-tooltip-trigger" data-type="all"><i class="icon-bolt"></i>Benachrichtigungen</a>
									<ul class="dropdown-menu notification-tooltip" role="menu" id="notification-tooltip-all">
										<li><!-- IF NOTIFICATION_COUNT_TOTAL == 0 -->{L_notification_none}<!-- ENDIF -->
											<!-- IF NOTIFICATION_COUNT_RED > 0 -->
											<h2><span class="notification-bubble-red">{NOTIFICATION_COUNT_RED}</span>{L_notification_red_prio}</h2>
											<ul>{NOTIFICATION_RED}</ul>
											<!-- ENDIF -->
											<!-- IF NOTIFICATION_COUNT_YELLOW > 0 -->
											<h2><span class="notification-bubble-yellow">{NOTIFICATION_COUNT_YELLOW}</span>{L_notification_yellow_prio}</h1>
											<ul>{NOTIFICATION_YELLOW}</ul>
											<!-- ENDIF -->
											<!-- IF NOTIFICATION_COUNT_GREEN > 0 -->
											<h2><span class="notification-bubble-green">{NOTIFICATION_COUNT_GREEN}</span>{L_notification_green_prio}</h1>
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
						</ul>				
					<!-- ENDIF -->
				</div>
				<div id="personalAreaTime" class="hiddenSmartphone">
					<ul>
						<li class="personalAreaTime"><i class="icon-time"></i>{USER_TIME}</li>
						<li><!-- IF S_SEARCH -->
						<form method="post" action="{EQDKP_ROOT_PATH}search.php{SID}" id="search_form">
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
					
					<div id="titles">
							<h1>{MAIN_TITLE}</h1><br />
							<h2>{SUB_TITLE}</h2>
					</div><!-- close titles-->
				
					<div class="clear noheight">&nbsp;</div>
				</div>
			</div> <!-- close header-->
		</header>
				
		<section>
		<div id="contentContainer">
			<a id="content"></a>
			
			<nav>
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
					{ADMIN_MENU}
				</div>
				<!-- ENDIF -->
			</nav>
		
			<div class="portal">
				<div class="columnContainer">
					<!-- IF FIRST_C -->
					<div class="first column" style="<!-- IF T_COLUMN_LEFT_WIDTH -->min-width:{T_COLUMN_LEFT_WIDTH};max-width:{T_COLUMN_LEFT_WIDTH};<!-- ELSE -->min-width: 200px;<!-- ENDIF -->">
						{PORTAL_LEFT}						
					</div> <!-- close first column -->
					<!-- ENDIF -->
					
					<div class="second column <!-- IF not THIRD_C -->no_third_column<!-- ENDIF -->">
						<div class="columnInner">
							<!-- BEGIN global_warnings -->
								<div class="{global_warnings.CLASS} roundbox">
									<div class="{global_warnings.ICON}">{global_warnings.MESSAGE}</div>
								</div>
								<br />
							<!-- END global_warnings -->

							{PORTAL_MIDDLE}
							<!-- ENDIF -->
							<div id="contentBody" class="{PAGE_CLASS} <!-- IF not S_NORMAL_HEADER -->simple-header <!-- ENDIF --><!-- IF not S_NORMAL_FOOTER -->simple-footer <!-- ENDIF -->">
								<div id="contentBody2">
									{GBL_CONTENT_BODY}
								</div>	
							</div><!-- close contentBody -->
							<!-- IF S_NORMAL_FOOTER -->
							{PORTAL_BOTTOM}
							<div class="debug">
							<!-- IF S_SHOW_QUERIES --><br />{DEBUG_TABS}<!-- ENDIF -->
							<!-- IF S_SHOW_DEBUG -->							
							<br /><div class="center">
								<span class="debug-info">SQL Querys: {EQDKP_QUERYCOUNT} | in {EQDKP_RENDERTIME} | {EQDKP_MEM_PEAK} |
									<a href="http://validator.w3.org/check/referer" target="_top">XHTML Validate</a>
								</span>
							</div>
							<!-- ENDIF -->
							</div>
						</div>
					</div><!-- close second column -->
					
					<!-- IF THIRD_C -->
					<div class="third column" style="<!-- IF T_COLUMN_RIGHT_WIDTH -->min-width:{T_COLUMN_RIGHT_WIDTH};max-width:{T_COLUMN_RIGHT_WIDTH}<!-- ELSE -->min-width: 200px;<!-- ENDIF -->">
						<div class="columnInner">
							{PORTAL_RIGHT}						
						</div>
					</div>
					<!-- ENDIF -->
				</div>
			</div>
		
		</div>
		</section>
		
		<footer>
			<div id="footer">
				{EQDKP_PLUS_COPYRIGHT}
			</div> <!-- close footer -->
		</footer>
	</div><!-- close wrapper -->
	
	<!-- ELSE -->
		<!-- IF S_SHOW_QUERIES --><br />{DEBUG_TABS}<!-- ENDIF -->
	<!-- ENDIF -->

	<div id="dialog-login" title="{L_login}">
		<form method="post" action="{EQDKP_ROOT_PATH}login.php{SID}" name="login" id="login">
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