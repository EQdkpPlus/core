<?PHP
	header("Content-type: text/css");

	define('EQDKP_INC', true);
	$eqdkp_root_path = './../../';

//Plus Data Cache | handler for APC, Xcache, MemCache, Mysql caching
$param_array = array();
$param_array['prefix'] = $dbname;

if (function_exists('apc_store') & function_exists('apc_fetch')){
	$_cachetype = 'apc';
}

$cache = ( isset($_cachetype) ) ? 'cache_'.$_cachetype : 'cache_none';
require($eqdkp_root_path . 'pluskernel/cache/'.$cache.'.class.php');
$pdc = new $cache();
$pdc->init($param_array);

define('ENT', 1); // Escape HTML entities
define('TAG', 2); // Strip HTML tags
function sanitize($input, $options = 3, $ignore = null)
{
    if ( !is_null($ignore) )
    {
        trigger_error('Third parameter to sanitize is deprecated!', E_USER_WARNING);
    }

    $input = ( $options & TAG ) ? strip_tags($input) : $input;
    $input = ( $options & ENT )  ? htmlspecialchars($input, ENT_QUOTES) : $input;
    $input = ( get_magic_quotes_gpc() ) ? stripslashes($input) : $input;

    return $input;
}

if( !is_file($eqdkp_root_path . 'config.php') )
{
	die('Error: could not locate configuration file.');
}

$style = $pdc->get($dbname.'.templates.luna.style');
if (!$style) {
	require_once($eqdkp_root_path . 'config.php');
	include_once($eqdkp_root_path . 'includes/db/mysql.php');
	$db = new dbal_mysql();
	$db->sql_connect($dbhost, $dbname, $dbuser, $dbpass);
	$style_query = $db->query("SELECT * FROM __styles WHERE style_name='luna_wotlk'");
	$style = $db->fetch_record($style_query);
	$db->free_result($style_query);
	$pdc->put($dbname.'.templates.luna.style', $style, 86400);
}

	$bgcolor = sanitize($style['body_background']);
	$fontfamily1 = sanitize($style['fontface1']);
	$fontfamily2 = sanitize($style['fontface2']);
	$fontfamily3 = sanitize($style['fontface3']);
	$fontsize1 = sanitize($style['fontsize1']);
	$fontsize2 = sanitize($style['fontsize2']);
	$fontsize3 = sanitize($style['fontsize3']);
	$fontcolor1 = sanitize($style['fontcolor1']);
	$fontcolor2 = sanitize($style['fontcolor2']);
	$fontcolor3 = sanitize($style['fontcolor3']);
	$fontcolor_positive = sanitize($style['fontcolor_pos']);
	$fontcolor_negative = sanitize($style['fontcolor_neg']);
	$link_color = sanitize($style['body_link']);
	$link_style = sanitize($style['body_link_style']);
	$hover_link_color = sanitize($style['body_hlink']);
	$hover_link_style = sanitize($style['body_hlink_style']);
	$header_link_color = sanitize($style['header_link']);
	$header_link_style = sanitize($style['header_link_style']);
	$hover_header_link_color = sanitize($style['header_hlink']);
	$hover_header_link_style = sanitize($style['header_hlink_style']);
	$table_row1 = sanitize($style['tr_color1']);
	$table_row2 = sanitize($style['tr_color2']);
	$table_th = sanitize($style['th_color1']);
	$table_border_width = sanitize($style['table_border_width']);
	$table_border_color = sanitize($style['table_border_color']);
	$table_border_style = sanitize($style['table_border_style']);
	$inputfield_bgcolor = sanitize($style['input_color']);
	$inputfield_border_width = sanitize($style['input_border_width']);
	$inputfield_border_color = sanitize($style['input_border_color']);
	$inputfield_border_style = sanitize($style['input_border_style']);
?>

body {
	color: #FFFFFF;
	font-size: 11px;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	margin: 0;
	padding: 0;
}

/* General page style */

a:link, a:active, a:visited, a.postlink {
	color: #FFF;
	text-decoration: none;
}

a:hover{
	color: #fd5c10;
	text-decoration: none;
}

.checkbox {
	background: url(images/CheckboxUnchecked.gif) no-repeat center center;
	display: block;
	width: 16px;
	height:16px;
}
a.checkbox, a.checkboxchecked {
	display: block;
	width: 16px;
	height: 16px;
	cursor: pointer;
}

a.checkbox {
	background: url(images/CheckboxUnchecked.gif) no-repeat center center;
}

a.checkboxchecked {
	background: url(images/CheckboxChecked.gif) no-repeat center center;
}

.radiobox {
	background: url(images/RadioboxUnchecked.gif) no-repeat center center;
	display: block;
	width: 16px;
	height:16px;
}

a.radiobox, a.radioboxchecked {
	display: block;
	width: 16px;
	height: 16px;
	cursor: pointer;
}

a.radiobox {
	background: url(images/RadioboxUnchecked.gif) no-repeat center center;
}

a.radioboxchecked {
	background: url(images/RadioboxChecked.gif) no-repeat center center;
}

img {
	border: 0px; vertical-align: middle;
}

table {
	color: #FFFFFF;
	font-size: 12px;
	font-family: Verdana, Arial, Helvetica, sans-serif;
}

th {
    background: url("images/headline-bg.png") no-repeat scroll right 0px #1A1919;
    color: #FFFFFF;
    font-family: Verdana,Arial,Helvetica,sans-serif;
    font-size: 12px;
    height: 28px;
    padding-left: 8px;
    padding-right: 8px;
    text-align: left;
    white-space: nowrap;
}

th a, th a:visited, th a:active {
	color: #D5261B;
	text-decoration: none;
	font-weight: bold;
}
th a:hover {
	color: #fd5c10;
	text-decoration: none;
	font-weight: bold;
}

th.smalltitle {
	font-size: 11px;
	font-weight: bold;
	white-space: nowrap;
}

th.footer {
	text-align: right;
	white-space: nowrap;
	font-weight: normal;
}

td {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
   
   
}

td.spacerow	{
	background: #000;
	height: 9px;
   
}

td.cat {
	font-weight:bold;
	letter-spacing:1px;
	background: #9c0100 url(images/head.jpg) repeat-x;
	height:22px;
	text-indent:4px;
}

.row1viewnews {
	background: #000000 repeat-x top;
}

.row2viewnews {
	background:#000000  repeat-x top;
}

.row1 {
	background:#000;
	color: #FFFFFF transparent;
   padding-left:4px !important;
}

.row2,.helpline	{
	background:#0e0e0e;
	color: #FFFFFF;
    padding-left:4px !important;
}

.row3 {
	background:#000;
}

.row4 {
	background: #000 url(images/head.jpg) repeat-x;
	height: 23px;
	font-size:9px;
	color: #D5261B;
	font-weight:bold;
}

.positive {
	color: #008800;
}

.negative {
	color: #FF0000;
}

.maintitle {
	font-size: 24px;
	font-weight: bold;
	color:#fff;
}
.subtitle  {
	font-size: 12px;
	color:#e1e1e1;
}

.menu {
	/* font-size: 9px; */
}
.a:link, .menu a:active, .menu a:visited {
	color:#e1e1e1;
	text-decoration: none;
}
.menu a:hover {
	text-decoration:none;
}

.small {
	font-size: 11px;
}
.small:hover {
	color: #D5261B;
}

.copy {
	color: #FFF;
}

.copy a:link, .copy a:active, .copy a:visited {
	color: #FFF;
}

.copy a:hover {
	color: #FD5C10;
}

form {
	display: inline;
}

.forumline td.row2{
padding-bottom:4px;
	
}

.input {
	background:#EEEEEE;
	color: #000;
	border: #000000 solid 0px;;
	font-size: 12px;
}


input.helpline1 , input.helpline2 {
	background:#EEEEEE;
	color: #000;
	border: #000000 solid 0px;;
	font-size: 12px;
}

input {
	color: #000;
	padding-bottom:2px;
	padding-left:2px;
}

input.post {
	color:#000;
	background:#EEEEEE;
	border: #000000 solid 0px;
	border-top: 1px solid #ABABAB;
	border-bottom: 1px solid #9A9A9A;
	border-right: 1px solid #9A9A9A;
	padding-bottom:2px;
	padding-left:2px;
}

textarea {
	color:#000;
	background:#EEEEEE;
	border: #000000 solid 0px;
	border-top: solid 0px #ABABAB;
	border-bottom: solid 0px #9A9A9A;
	border-right: solid 0px #9A9A9A;
	padding-bottom:2px;
	padding-left:2px;
}

input.button, input.liteoption, input.mainoption {
	color:#d5261b;
	/*background:#EEEEEE;*/
	border: #000000 solid 0px;;
	border-bottom: solid 0px #9A9A9A;
	border-right: solid 0px #9A9A9A;
	background-image:url(images/button.jpg);
   width: auto;
   height: 18px;
	font-size:9px;
	padding : 1px;
	border:1px solid #D5261B;
}

input.mainoption {
	font-weight: bold;
}

input[disabled]{
  background:gray;
  color:silver;
}

input.hasDatepicker {
	font-weight:normal;
}

input.button:hover,	input.liteoption:hover,	input.mainoption:hover {
	color:#FD5C10;
	background-image:url(images/button.jpg);
   width:auto;
   height:18px;
	border: #000000 solid 0px;
	border-bottom: solid 0px #9A9A9A;
	border-right: solid 0px #9A9A9A;
	font-size:9px;
	padding : 1px;
	border:1px solid #D5261B;
  }

.rowHover {
	background: #000;
	color:#FFFFFF;
}

div.graph {
	position: relative;
	width: 98%;
	border: #000 solid 0px;
	padding: 2px;
	margin: 2px 0;
	text-align: center;
}

div.graph .bar {
	display: block;
	position: relative;
	background: #0e0e0e;
	color: #FFFFFF;
	height: 1.3em;
	line-height: 1.3em;
}

div.graph .bar span {
	position: absolute; left: 1em;
}

/* News Accodrion */
#rrs_news  {
/*	width: 180px; */
	margin:  0px;
	padding: 0px;
	border: 0px solid black;
}

#rrs_news div.content a {
	color: #ffffff;
	text-decoration: none;
	font-size: 12px;
}

#rrs_news div.content a:hover {
	color: #fd5c10;
	text-decoration: none;
}

#rrs_news div.content {
	margin-bottom : 10px;
	border: none;
	color: #FFFFFF;
	font-size: 11px;
	margin: 0px;
	padding: 10px;
	background-color: #000000;
	overflow: hidden;
}

#rrs_news div.title {
	cursor: pointer;
	display: block;
	padding: 5px;
	margin-top: 0px;
	text-decoration: none;
	font-weight: bold;
	font-size: 11px;
	color: #ffffff;
	background: #9c0100 url(images/head.jpg) repeat-x top;
	border: 0px solid #9c0100;
}

#rrs_news div.title:hover {
	color: #ffffff;
	background: #e80000 url(images/head_hl.jpg) repeat-x top;
}

#rrs_news div.title.selected {
	color: #D5261B;
	background: #43759f url(images/head_active.jpg) repeat-x top;
	border-bottom: 1px solid black;
}

#wrapper{
	background: transparent;
	width: 1050px;
	margin: 0px auto;
}

#toptitles{
	
	
	text-transform: uppercase;
	color: #D5261B;
	text-align: right;	
	
}

#header{
  width: 1258px;
	margin-right:auto;
	position: relative;
		margin-left:auto;
}





#navigation {
    background: url("images/navi-links.png") no-repeat scroll -2px -18px transparent;
    display: block;
    height: 165px;
    padding-left: 70px;
}


#navigation-expand{
    background: url("images/navi-rechts.png") no-repeat scroll right -18px transparent;
    font-style: normal;
    height: 175px;
    padding-left: 0px;
    width: 100%;
   
}


#navigation ul{
	margin: 0px;
	padding: 0px;
	list-style-type: none;
   padding-left:0px;
   padding-top:62px;
}

#navigation ul li{
	float: left;
}

#navigation ul li a{
	
	/*text-transform: uppercase;*/
	margin-left: 5px;
	display: block;
	float: left;
	padding: 8px;
	padding-left: 15px;
	padding-right: 15px;
	color: #EEEEEE;
	text-decoration: none;
	font-weight: bold;
	font-size: 19px;
 
   
  
}

#navigation ul li a:hover{
	color:#ff3737;
	text-decoration: none;
	
}

#navigation ul li a:active{
		color:#fd5c10;
}

#footer{
	text-align: center;
	padding: 20px;
	background: #000;
}

td.menu{
	
	margin:0px;
	font-weight:bold;
}

td.channel {
	font-weight:bold;
}

td.channel, td.player{
	font-size:9px
}

.errortable a {
	color:red;
	text-decoration: underline;
}

.image-warning a {
	color:black;
}

.image-warning {
	text-align: left;
}

#inner_wrapper {
	width:100%;
	margin-top: 10px;
	margin-right : auto;
	margin-bottom: 10px;
	margin-left: auto;
   background:url(images/wrapper-bg.png);
}

#inner_wrapper textarea{
width:689px;
}




.member_wrapper {
	width:690px;
	margin-top: 10px;
	margin-right : auto;
	margin-bottom: 10px;
	margin-left:auto;
}

.class_header {
	font-weight: bold;
	font-size: 15px;
}

.roster_hr_member {
	border: none 0;
	border-top: 1px;
	height: 1px;
}

roster_member_right {
	text-align: right;
}


.floatR {
	float:right;
}

.right {
	text-align:right;
	margin-top: 8px;
}

/* News Style */
.newscontainer {
	margin-top: 10px;
	margin-left: 30px;
	margin-right: 10px;
	margin-bottom: 3.5em;
	background-color:#000;
}

blockquote {
	margin-bottom: 0.5em;
	margin-top: 0.5em;
	margin-left: 0.1em;
	padding: 0;
	color: #CFCFCF;
	padding-left: 0.6em;
	border-left: solid 4px #336796;
	background-image: none;
}

/* Module Addisions */
.birthday_today{
	color:#D5261B;
	background:#285E8D;
}

.weather_temp {
	font-size: 18px;
	font-weight: bold;
	text-align: right;
}

.weather_wind {
	font-size: 11px;
}

.weather_city {
	font-size: 13px;
	font-weight: bold;
	text-align:center;
}

div.contentBox {
background:#0e0e0e !important;
}

hr {
background: url(images/red-line.jpg);
height:1px;
border:none;

}

.contentBox .boxHeader {
border-bottom:1px solid #353535 !important;
padding-bottom:0;
}


.rowcolor1 {
background-color:#000 !important;
}
table {
margin-top:

}



.textLight, .textLight:link, .textLight:visited {
color:#FFF !important;
}

