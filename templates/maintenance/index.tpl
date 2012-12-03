<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  {META}
  <title>EQdkp Plus {L_MMODE}</title>
  <style type="text/css">
  <!-- INCLUDE maintenance.css -->

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

<div id="hdr" align="center">
  <table width="100%" border="0" cellspacing="1" cellpadding="2" class="header">
    <tr>
      <td width="100%">
        <center>
  	      <img src="../templates/maintenance/images/logo.png" alt="EQdkp Plus" class="absmiddle" />
        {L_MMODE}
        </center>
      </td>
    </tr>
  </table>
  <!-- IF not S_HIDE_BREADCRUMP -->
    <table width="100%" border="0" cellspacing="1" cellpadding="2" class="breadcrumb">
    <tr>
      <td width="100%"><div style="float:left">
      &nbsp; <a href="{U_ACP}"><img src="../templates/maintenance/images/home.png" alt="Home" class="absmiddle" border="0"/> {L_ADMIN_PANEL}</a> &raquo; <a href="{U_MMODE}">{L_MMODE}</a><!-- BEGIN breadcrumps --> &raquo; {breadcrumps.BREADCRUMP} <!-- END breadcrumps --></div><div style="float:right"><!-- IF S_MMODE_ACTIVE --><a href="task_manager.php{SID}&amp;disable=true"><img src="../templates/maintenance/images/disable.png" alt="Disable" class="absmiddle" border="0"/>{L_DEACTIVATE_MMODE}</a><!-- ENDIF --></div> </td>
    </tr>
  </table>
	<div class="breadcrumb_shadow"></div>
  <!-- ENDIF -->
</div>
<br />
<!-- IF not S_MMODE_ACTIVE -->

<div id="layer">
<div id="inner_layer">
<form action="task_manager.php{SID}" method="post" name="post">
{L_ACTIVATE_INFO}
<input type="text" name="maintenance_message" value="{MAINTENANCE_MESSAGE}" style="width:98%" /><br /><br />

<input type="submit" value="{L_ACTIVATE_MMODE}" name="activate" class="mainoption" /> <input type="submit" value="{L_LEAVE_MMODE}" name="leave" class="mainoption" />
</form>
</div>
</div>
<script type="text/javascript">
document.post.maintenance_message.focus();
</script>
<!-- ENDIF -->

<!-- IF S_SPLASH -->
<div id="layer">
<div id="inner_layer">
	<p><strong>{L_SPLASH_WELCOME}</strong></p>
	<p>{L_SPLASH_DESC}</p>
	<p>
		<table>
			<tr>
				<td><img src="../images/admin/admin_index/support_tour.png" border="0" /></td>
				<td>{L_SPLASH_NEW} <br /> <strong><a href="task_manager.php{SID}&start_tour=true">{L_TOUR_START}</a></strong></td>
			</tr>
		</table>
	</p>
	<input type="button" value="{L_TOUR_START}" class="mainoption" onclick="window.location='task_manager.php{SID}&start_tour=true'" />
	<input type="button" value="{L_JUMP_TOUR}" class="mainoption" onclick="window.location='task_manager.php{SID}&no_tour=true'" />
	<input type="button" value="{L_06_IMPORT}" class="mainoption" onclick="window.location='{SID}&type=import'" />
	<input type="button" value="{L_GUILD_IMPORT} *" class="mainoption" onclick="window.location='task_manager.php{SID}&guild_import=true'" /> <br />{L_GUILD_IMPORT_INFO}
</div>
</div>
<!-- ENDIF -->

<!-- IF NO_LEAVE -->
<div id="layer">
<div id="inner_layer">
<form action="task_manager.php{SID}" method="post" name="no_leave">
{L_NO_LEAVE}
<br />
<br />
<input type="submit" name="no_leave_accept" value="{L_NO_LEAVE_ACCEPT}" style="width:95%" class="mainoption" />
</form>
</div>
</div>
<script language="javascript">
document.post.no_leave_accept.focus();
</script>
<!-- ENDIF -->

<div id="cont" align="center">
{GBL_CONTENT_BODY}
</div>
<br />
<div id="ftr" align="center">
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
    <!-- IF not S_HIDE_DEBUG -->
		<ul class="tabnav">
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
  <!-- ENDIF -->
  <!--
      If you use this software and find it to be useful, we ask that you
      retain the copyright notice below.  While not required for free use,
      it will help build interest in the EQdkp-Plus project.
  //-->
  <div class="copyright" <!-- IF S_HIDE_DEBUG -->style="border-top:1px solid #000;"<!-- ENDIF -->>
    <a href="http://www.eqdkp-plus.com" target="_new">EQDKP Plus</a> &copy; 2006 - {TYEAR} by EQDKP Plus Developer Team
  </div>
</div>

</body>
</html>