<!-- IF S_NO_HEADER_FOOTER -->
	{GBL_CONTENT_BODY}
<!-- ELSE --><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{L_XML_LANG}" lang="{L_XML_LANG}">
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
			//]]>
		</script>
	</head>
	<body>
		<a name="top"></a><!-- absolute top -->
		{STATIC_HTMLCODE}
		<!-- IF S_NORMAL_HEADER -->
		
		<div id="wrapper" <!-- IF T_PORTAL_WIDTH -->class="fixed_width"<!-- ENDIF -->>
			<div id="personalArea">
				<div id="personalAreaLogin">
					<!-- IF not S_LOGGED_IN -->
					<form method="post" action="{EQDKP_ROOT_PATH}login.php{SID}">
						<input name="username" size="20" maxlength="30" class="input username" id="loginarea_username" type="text" />
						<input name="password" size="20" maxlength="32" class="input password" id="loginarea_password" type="password" />
						<input type="checkbox" name="auto_login" title="{L_remember_password}" class="absmiddle" />
						<input type="submit" class="mainoption bi_key" value="{L_login}" name="login" /> {AUTH_LOGIN_BUTTON}
					{CSRF_TOKEN}
					</form>
					
					<!-- ELSE -->
					<a href="{EQDKP_ROOT_PATH}settings.php{SID}"><img src="{EQDKP_IMAGE_PATH}admin/manage_users.png" alt="user" class="absmiddle" /> {USER_NAME}</a> 
					<!-- BEGIN user_notfications -->
					&nbsp;&bull;&nbsp; {user_notifications.MESSAGE}
					<!-- END user_notifications -->
					<!-- IF S_ADMIN -->&nbsp;&bull;&nbsp; <a href="{EQDKP_ROOT_PATH}admin/index.php{SID}"><img src="{EQDKP_IMAGE_PATH}admin/task_manager.png" class="absmiddle" alt="Admin" /> {L_menu_admin_panel}</a> <!-- ENDIF -->
					&nbsp;&bull;&nbsp; <a href="{EQDKP_ROOT_PATH}login.php{SID}&amp;logout=true&amp;link_hash={CSRF_LOGOUT_TOKEN}"><img src="{EQDKP_IMAGE_PATH}glyphs/logout.png" alt="user" class="absmiddle" /> {L_logout}</a>		
					<!-- ENDIF -->
					<!-- BEGIN personal_area_addition -->
					&nbsp;&bull;&nbsp; {personal_area_addition.TEXT}
					<!-- END personal_area_addition -->
				</div>
				<div id="personalAreaTime">
					<!-- IF S_SEARCH -->
						<form method="post" action="{EQDKP_ROOT_PATH}search.php{SID}" id="search_form">
							<input name="svalue" size="20" maxlength="30" class="input search" id="loginarea_search" type="text" value="{L_search}..."/>
							<input type="submit" class="search_button" value="" title="{L_search_do}" />
						</form>
					&nbsp; &bull; &nbsp;
					<!-- ENDIF -->
					<!-- IF S_GAME_DROPDOWN -->
					{GAME_DROPDOWN}
					&nbsp; &bull; &nbsp;
					<!-- ENDIF -->
					<img src="{TEMPLATE_PATH}/images/clock.png" alt="Clock" class="absmiddle" /> {USER_TIME}
				</div>
				<div class="clear"></div>
			</div> <!-- close personalArea -->
	
		<div id="header">
			<a name="header"></a>
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
		
		<div id="mainmenu4_wrapper">
			<div id="mainmenu4_left">
				<div id="mainmenu4_right">
					<div id="mainmenu4">
						<div id="menu4text">
							{MAIN_MENU4}
						</div>
					</div>
				</div>
			</div>
			<div class="clear noheight">&nbsp;</div>
		</div><!-- close mainmenu4 -->
		
		<div id="contentContainerHeader">
		</div>
		<div id="contentContainerShadow">
			
			<div id="contentContainer">
							<a name="content"></a>
			<!-- IF S_IN_ADMIN -->
			<div id="adminmenu">
				{ADMIN_MENU}
			</div>
			<!-- ENDIF -->
		
			<div class="portal">
				<div class="columnContainer">
					<!-- IF FIRST_C -->
					<div class="first column" style="<!-- IF T_COLUMN_LEFT_WIDTH -->min-width:{T_COLUMN_LEFT_WIDTH};max-width:{T_COLUMN_LEFT_WIDTH};<!-- ELSE -->min-width: 180px;<!-- ENDIF -->">
						{PORTAL_LEFT1}
						<div id="main_menu1" class="portalbox">
							<div class="portalbox_head">
								<span class="toggle_button">&nbsp;</span>
								<span class="center">{L_menu_eqdkp}</span>
							</div>
							<div class="portalbox_content">
							<div class="toggle_container">
								{MAIN_MENU1}
							</div>
							</div>
						</div>
						
						<div id="main_menu2" class="portalbox">
							<div class="portalbox_head">
								<span class="toggle_button">&nbsp;</span>
								<span class="center">{L_menu_user}</span>
							</div>
							<div class="portalbox_content">
							<div class="toggle_container">
								{MAIN_MENU2}
							</div>
							</div>
						</div>
						<!-- IF S_MAIN_MENU3 -->
						<div id="main_menu3" class="portalbox">
							<div class="portalbox_head">
								<span class="toggle_button">&nbsp;</span>
								<span class="center">{L_menu_links_short}</span>
							</div>
							<div class="portalbox_content">
							<div class="toggle_container">
								{MAIN_MENU3}
							</div>
							</div>
						</div>
						<!-- ENDIF -->
						{PORTAL_LEFT2}
						
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

							{NEWS_TICKER_H}
							{PORTAL_MIDDLE}
							<!-- ENDIF -->
							<div id="contentBody" class="<!-- IF not S_NORMAL_HEADER -->simpleHeader <!-- ENDIF --><!-- IF not S_NORMAL_FOOTER -->simpleFooter <!-- ENDIF -->">
								{GBL_CONTENT_BODY}
							</div><!-- close contentBody -->
							<!-- IF S_NORMAL_FOOTER -->
							{PORTAL_BOTTOM}
							<!-- IF S_SHOW_QUERIES --><br />{DEBUG_TABS}<!-- ENDIF -->
							<!-- IF S_SHOW_DEBUG -->
							<br /><div class="center">
								<span class="copyright">SQL Querys: {EQDKP_QUERYCOUNT} | in {EQDKP_RENDERTIME} | {EQDKP_MEM_PEAK} |
									<a href="http://validator.w3.org/check/referer" target="_top">XHTML Validate</a>
								</span>
							</div>
							<!-- ENDIF -->
						</div>
					</div><!-- close second column -->
					
					<!-- IF THIRD_C -->
					<div class="third column" style="<!-- IF T_COLUMN_RIGHT_WIDTH -->min-width:{T_COLUMN_RIGHT_WIDTH};max-width:{T_COLUMN_RIGHT_WIDTH}<!-- ELSE -->min-width: 180px;<!-- ENDIF -->">
						<div class="columnInner">
							{PORTAL_RIGHT}
						
						</div>
					</div>
					<!-- ENDIF -->
				</div>
			</div>
			</div>
			<div id="contentContainerFooter">
			</div>
		</div>
		<div id="footer">
			{EQDKP_PLUS_COPYRIGHT}
		</div> <!-- close footer -->
	</div><!-- close wrapper -->
	
	<!-- ELSE -->
		<!-- IF S_SHOW_QUERIES --><br />{DEBUG_TABS}<!-- ENDIF -->
	<!-- ENDIF -->
	<script type="text/javascript">
		{JS_CODE_EOP}
		{JS_CODE_EOP2}
	</script>		
	<a name="bottom"></a>
	</body>
</html>
<!-- ENDIF -->