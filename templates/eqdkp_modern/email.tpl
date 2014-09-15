<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
		<title>{SUBJECT}</title>
		<style type="text/css">
			html {
				height: 100%;
			}
			body {
				background: #2e78b0;
				background: -moz-linear-gradient(top,  #2e78b0 0%, #193759 100%);
				background: -webkit-linear-gradient(top,  #2e78b0 0%,#193759 100%);
				background: -ms-linear-gradient(top,  #2e78b0 0%,#193759 100%);
				background: linear-gradient(to bottom,  #2e78b0 0%,#193759 100%);
				font-size: 13px;
				font-family: Tahoma,Arial,Verdana,sans-serif;
				color: #000000;
				padding:0;
				margin:0;
				height: 100%;
			}
			#outerWrapper {
				background: url('{PLUSLINK}/templates/eqdkp_modern/images/background-head.svg') no-repeat scroll center 20px transparent;
				background-size: 100%;
			}
			#header {
				font-family:'Trebuchet MS',Arial,sans-serif;
				vertical-align: middle;
				width: 80%;
				height:120px;
				margin: 0px auto;
			}
			#logo {
				height: 100px;
				width:269px;
				margin-left: 15px;
				float: left;
			}
			#main{
				width:80%;
				overflow: auto;
				margin:0 auto;
				background-color: #F5F5F5;
				border: 1px solid #383838;
				border-radius: 4px 4px 4px 4px;
				box-shadow: 2px 2px 3px 0 #000000;
				padding: 10px;
			}
			#content{
				background-color: #FFFFFF;
				padding:20px;
				border:1px solid #AEAEAE;
			}
			#footer a, #footer a:hover {
				color: #fff;
				text-decoration: none;
			}
			#footer {
				margin-top:1em;
				font-size: 12px;
				text-align:center;
				padding:10px;
				color: #fff;
			}
			#signature{
				margin-top:1em;
			}
			h1, h2, h3 {
				font-family: 'Trebuchet MS',Arial,sans-serif;
				font-weight: bold;
				padding-bottom: 5px;
				border-bottom: 1px solid #CCCCCC;
				margin: 5px 0px 10px 0px;
			}
			h1 {
				font-size: 20px;
			}
			h2 {
				font-size: 18px;
			}
			table {
				display: table;
				width: 100%;
				border-collapse: collapse;
				border-bottom: 2px solid #303030;
				border-right: 1px solid #c7c7c7;
				border-left: 1px solid #c7c7c7;
				margin: 0 auto;
			}
			tr { display: table-row;}
			td, th {
				display: table-cell;
				padding: 10px 6px;
				border-bottom: 1px solid #e7e7e7;
				vertical-align: middle;
				text-align: left;
			}
			th{
				background-color: #404040;
				white-space:nowrap;
				font-size: 16px;
				color: #f7f7f7;
				border-bottom: 0px;
				border-collapse: collapse;
			}
			#outlook a {padding:0;} /* Force Outlook to provide a "view in browser" menu link. */
		</style>
	</head>

	<body>
		<div id="outerWrapper">
			<div id="header">
				<img src="{LOGO}" id="logo" />
			</div>
			<div id="main">
				<div id="content">
					<h1>{SUBJECT}</h1>
					{CONTENT}
				</div>
				
				<div id="signature">
					{SIGNATURE}
				</div>
			</div>
		</div>
		<div id="footer">
			<a href="{PLUSLINK}">EQDKP Plus {PLUSVERSION}</a>
		</div>
	</body>
</html>