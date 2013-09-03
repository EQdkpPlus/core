<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  {META}
  <title>EQdkp Plus {L_MMODE}</title>
  <link rel='stylesheet' href='{ROOT_PATH}templates/maintenance/css/maintenance.css' type='text/css' media='screen' />
   <link rel='stylesheet' href='{ROOT_PATH}templates/maintenance/css/font-awesome/css/font-awesome.min.css' type='text/css' media='screen' />
  
  <style type="text/css">
  		.debug_show {
			position:relative;
			display:inline;
		}
		.debug_hide {
			position:relative;
			display:none;
		}
  </style>
</head>

<body>
	<div class="wrapper">
		<div class="header">
			<img src="../templates/maintenance/images/logo.png" alt="EQdkp Plus" class="absmiddle" /> {L_MMODE}
		</div>
		
		<div class="innerWrapper">
			<!-- IF S_IS_ADMIN -->
			<div class="breadcrumbContainer">
				<ul class="breamcrumb">
					<li><a href="{ROOT_PATH}index.php"><i class="icon-home"></i></a></li>
					<li><a href="{U_ACP}">{L_ADMIN_PANEL}</a></li>
					<li><a href="{U_MMODE}">{L_MMODE}</a></li>
					<!-- BEGIN breadcrumps -->
					<li>{breadcrumps.BREADCRUMP}</li>
					<!-- END breadcrumps -->
				</ul>
				<div class="clear"></div>
			</div>
			<!-- ENDIF -->		
			
			<!-- IF not S_MMODE_ACTIVE -->
			<div id="layer">
			</div>
			<div id="inner_layer">
				<form action="task_manager.php{SID}" method="post" name="post">
					{L_ACTIVATE_INFO}
					<input type="text" name="maintenance_message" value="{MAINTENANCE_MESSAGE}" style="width:98%" /><br /><br />

					<input type="submit" value="{L_ACTIVATE_MMODE}" name="activate" class="mainoption" /> <input type="submit" value="{L_LEAVE_MMODE}" name="leave" class="mainoption" />
				</form>
			</div>
			<script type="text/javascript">
			document.post.maintenance_message.focus();
			</script>
			<!-- ENDIF -->
			
			<!-- IF S_SPLASH -->
			<div id="layer">
			</div>
			<div id="inner_layer">
				<h2>{L_SPLASH_WELCOME}</h2>
				<p>{L_SPLASH_DESC}</p>
				<div><img src="../images/admin/admin_index/support_tour.png" border="0" style="float:left;"/>
				
					<br />{L_SPLASH_NEW} <br /> <strong><a href="task_manager.php{SID}&start_tour=true">{L_TOUR_START}</a>
					<div class="clear"></div>
				</div>
				<p>
					<table>
						<tr>
							<td></td>
							<td></strong></td>
						</tr>
					</table>
				</p>
				<input type="button" value="{L_TOUR_START}" class="mainoption" onclick="window.location='task_manager.php{SID}&start_tour=true'" />
				<input type="button" value="{L_JUMP_TOUR}" class="mainoption" onclick="window.location='task_manager.php{SID}&no_tour=true'" />
				<br /><br />
				<input type="button" value="{L_06_IMPORT}" class="mainoption" onclick="window.location='{SID}&type=import'" />
				<input type="button" value="{L_GUILD_IMPORT} *" class="mainoption" onclick="window.location='task_manager.php{SID}&guild_import=true'" /> <br />{L_GUILD_IMPORT_INFO}
			</div>
			<!-- ENDIF -->
			
			<!-- IF NO_LEAVE -->
			<div id="layer"></div>
			<div id="inner_layer">
				<form action="task_manager.php{SID}" method="post" name="no_leave">
					{L_NO_LEAVE}
					<br />
					<br />
					<input type="submit" name="no_leave_accept" value="{L_NO_LEAVE_ACCEPT}" style="width:100%" class="mainoption" />
				</form>
			</div>

			<script language="javascript">
			document.post.no_leave_accept.focus();
			</script>
			<!-- ENDIF -->
			
			<div class="content">
			{GBL_CONTENT_BODY}
			</div>

		</div>
	</div>

	<div class="footer">
		 <!--
		  If you use this software and find it to be useful, we ask that you
		  retain the copyright notice below.  While not required for free use,
		  it will help build interest in the EQdkp-Plus project.
		//-->
		<a href="http://www.eqdkp-plus.eu" target="_new">EQdkp Plus</a> &copy; 2006 - {TYEAR} by EQdkp Plus Developer Team
	</div>

</body>
</html>