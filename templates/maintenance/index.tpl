<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  {META}
  <title>EQdkp Plus {L_MMODE}</title>
  <link rel='stylesheet' href='{ROOT_PATH}templates/maintenance/css/maintenance.css' type='text/css' media='screen' />
  <link rel='stylesheet' href='{ROOT_PATH}libraries/FontAwesome/font-awesome.min.css' type='text/css' media='screen' />
  
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
  
  	<script type="text/javascript" language="javascript">
		function debug_show_me(id) {

            for(var i=1; i<={MAX_ID}; i++) {
            	var container = document.getElementById('debug_'+i);
            	if(i != id) {
            		container.className = 'debug_hide';
            	} else {
								if (container.className == 'debug_show'){
            			container.className = 'debug_hide';
								} else {
									container.className = 'debug_show';
								}
            	}
            }
        }
    </script>
</head>

<body>
	<div class="wrapper">
		<div class="header">
			<img src="../templates/maintenance/images/logo.svg" alt="EQdkp Plus" class="absmiddle" /> {L_MMODE}
		</div>
		
		<div class="innerWrapper">
			<!-- IF S_IS_ADMIN -->
			<div class="breadcrumbContainer">
				<ul class="breamcrumb">
					<li><a href="{ROOT_PATH}index.php"><i class="fa fa-home"></i></a></li>
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
				<form action="index.php{SID}" method="post" name="post">
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
				<p>{L_SPLASH_DESC}</p><br/>
				<div><span class="fa-stack fa-3x"  style="float:left;"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-tachometer fa-stack-1x fa-inverse"></i></span>
					<br />{L_SPLASH_NEW} <br /> <strong><a href="index.php{SID}&start_tour=true">{L_TOUR_START}</a>
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
				<input type="button" value="{L_TOUR_START}" class="mainoption" onclick="window.location='index.php{SID}&start_tour=true'" />
				<input type="button" value="{L_JUMP_TOUR}" class="mainoption" onclick="window.location='index.php{SID}&no_tour=true'" />
				<br /><br />
				<input type="button" value="{L_06_IMPORT}" class="mainoption" onclick="window.location='{SID}&type=import'" />
				<input type="button" value="{L_GUILD_IMPORT} *" class="mainoption" onclick="window.location='index.php{SID}&guild_import=true'" /> <br />{L_GUILD_IMPORT_INFO}
			</div>
			<!-- ENDIF -->
			
			<!-- IF NO_LEAVE -->
			<div id="layer"></div>
			<div id="inner_layer">
				<form action="index.php{SID}" method="post" name="no_leave">
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
			
			<!-- IF not S_HIDE_DEBUG -->
			<div class="debug">
				<br />
				<h2>Debug</h2>
				<ul class="nav nav-tabs">
					<!-- BEGIN debug_types -->
					<li><a href="javascript:debug_show_me('{debug_types.ID}')">{debug_types.TYPE} {L_CLICK}</a></li>
					<!-- END debug_types -->
				</ul>

				<!-- BEGIN debug_types -->
				<div id='debug_{debug_types.ID}' class='debug_hide'>
					<table border='1' cellspacing='0' cellpadding='1' class="task_table">
						<tr>
							<th>{debug_types.TYPE}</th>
						</tr>
						<!-- BEGIN debug_messages -->
						<tr class="{debug_types.debug_messages.ROW_CLASS}">
							<td>{debug_types.debug_messages.MESSAGE}</td>
						</tr>
						<!-- END debug_messages -->
					</table>
				</div>
				<!-- END debug_types -->
			</div>
			<!-- ENDIF -->
			
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