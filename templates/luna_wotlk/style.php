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

$style = $pdc->get('templates.luna.style', false, true);
if (!$style) {
	require_once($eqdkp_root_path . 'config.php');
	include_once($eqdkp_root_path . 'includes/db/mysql.php');
	$db = new dbal_mysql();
	$db->sql_connect($dbhost, $dbname, $dbuser, $dbpass);
	$style_query = $db->query("SELECT * FROM __styles WHERE style_name='luna_wotlk'");
	$style = $db->fetch_record($style_query);
	$db->free_result($style_query);
	$pdc->put('templates.luna.style', $style, 86400, false, true);
}

$defgame = $pdc->get('templates.luna.defgame', false, true);
if (!$defgame) {
	require_once($eqdkp_root_path . 'config.php');
	include_once($eqdkp_root_path . 'includes/db/mysql.php');
	$db = new dbal_mysql();
	$db->sql_connect($dbhost, $dbname, $dbuser, $dbpass);
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
	color: #<?php echo $fontcolor1; ?>;
	font-size: <?php echo $fontsize1; ?>px;
	font-family: <?php echo $fontfamily1; ?>;
	margin: 0;
	padding: 0;
}

/* General page style */

a:link, a:active, a:visited, a.postlink {
	color: #<?php echo $link_color; ?>;
	text-decoration: <?php echo $link_style; ?>;
}

a:hover{
	color: #<?php echo $hover_link_color; ?>;
	text-decoration: <?php echo $hover_link_style; ?>;
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
	color: #<?php echo $fontcolor1; ?>;
	font-size: <?php echo $fontsize2; ?>px;
	font-family: <?php echo $fontfamily1; ?>;
}

th {
	background: #2b577c url(images/head.jpg) repeat-x;
	color:#<?php echo $header_link_color; ?>;
	font-size: <?php echo $fontsize2; ?>px;
	font-family: <?php echo $fontfamily1; ?>;
	height:25px;
	white-space:nowrap;
	text-align:left;
	padding-left:8px;
	padding-right:8px;
}

th a, th a:visited, th a:active {
	color: #<?php echo $header_link_color ?>;
	text-decoration: <?php echo $header_link_style; ?>;
	font-weight: bold;
}
th a:hover {
	color: #<?php echo $hover_header_link_color; ?>;
	text-decoration: <?php echo $hover_header_link_style; ?>;
	font-weight: bold;
}

th.smalltitle {
	font-size: <?php echo $fontsize1; ?>px;
	font-weight: bold;
	white-space: nowrap;
}

th.footer {
	text-align: right;
	white-space: nowrap;
	font-weight: normal;
}

td {
	font-family: <?php echo $fontfamily1; ?>;
	font-size: <?php echo $fontsize2; ?>px;
}

td.spacerow	{
	background: #<?php echo $bgcolor; ?>;
	height: 9px;
}

td.cat {
	font-weight:bold;
	letter-spacing:1px;
	background: #2b577c url(images/head.jpg) repeat-x;
	height:22px;
	text-indent:4px;
}

.row1viewnews {
	background: #<?php echo $table_row1;?> url(images/table.jpg) repeat-x top;
}

.row2viewnews {
	background:#<?php echo $table_row1; ?> url(images/table.jpg) repeat-x top;
}

.row1 {
	background:#<?php echo $table_row1; ?>;
	color: #<?php echo $fontcolor1; ?>;
}

.row2,.helpline	{
	background:#<?php echo $table_row2; ?>;
	color: #<?php echo $fontcolor1; ?>;
}

.row3 {
	background:#<?php echo $table_row1; ?>;
}

.row4 {
	background: #<?php echo $bgcolor; ?> url(images/head.jpg) repeat-x;
	height: 23px;
	font-size:9px;
	color: #<?php echo $header_link_color; ?>;
	font-weight:bold;
}

.positive {
	color: #<?php echo $fontcolor_positive; ?>;
}

.negative {
	color: #<?php echo $fontcolor_negative; ?>;
}

.maintitle {
	font-size: 24px;
	font-weight: bold;
	color:#<?php echo $fontcolor2; ?>;
}
.subtitle  {
	font-size: 12px;
	color:#<?php echo $fontcolor2; ?>;
}

.menu {
	/* font-size: 9px; */
}
.a:link, .menu a:active, .menu a:visited {
	color:#<?php echo $fontcolor2; ?>;
	text-decoration: none;
}
.menu a:hover {
	text-decoration: underline;
}

.small {
	font-size: <?php echo $fontsize1; ?>px;
}
.small:hover {
	color: #<?php echo $header_link_color; ?>;
}

.copy {
	color: #<?php echo $fontcolor2; ?>;
}

.copy a:link, .copy a:active, .copy a:visited {
	color: #<?php echo $fontcolor2; ?>;
}

.copy a:hover {
	color: #<?php echo $fontcolor2; ?>;
}

form {
	display: inline;
}

.forumline {
	background:#14293b url(images/table.jpg) repeat-x top;
}

.input {
	background:#<?php echo $inputfield_bgcolor ?>;
	color: #<?php echo $fontcolor3; ?>;
	border: #<?php echo $inputfield_border_color; ?> <?php echo $inputfield_border_style; ?> <?php echo $inputfield_border_width; ?>px;
	font-size: <?php echo $fontsize2; ?>px;
}


input.helpline1 , input.helpline2 {
	background:#<?php echo $inputfield_bgcolor ?>;
	color: #<?php echo $fontcolor3; ?>;
	border: #<?php echo $inputfield_border_color; ?> <?php echo $inputfield_border_style; ?> <?php echo $inputfield_border_width; ?>px;
	font-size: <?php echo $fontsize2; ?>px;
}

input {
	color: #<?php echo $fontcolor3; ?>;
	padding-bottom:2px;
	padding-left:2px;
}

input.post {
	color:#<?php echo $fontcolor3; ?>;
	background:#<?php echo $inputfield_bgcolor ?>;
	border: #<?php echo $inputfield_border_color; ?> <?php echo $inputfield_border_style; ?> <?php echo $inputfield_border_width; ?>px;
	border-top: 1px solid #ABABAB;
	border-bottom: 1px solid #9A9A9A;
	border-right: 1px solid #9A9A9A;
	padding-bottom:2px;
	padding-left:2px;
}

textarea {
	color:#<?php echo $fontcolor3; ?>;
	background:#<?php echo $inputfield_bgcolor ?>;
	border: #<?php echo $inputfield_border_color; ?> <?php echo $inputfield_border_style; ?> <?php echo $inputfield_border_width; ?>px;
	border-top: <?php echo $inputfield_border_style; ?> <?php echo $inputfield_border_width; ?>px #ABABAB;
	border-bottom: <?php echo $inputfield_border_style; ?> <?php echo $inputfield_border_width; ?>px #9A9A9A;
	border-right: <?php echo $inputfield_border_style; ?> <?php echo $inputfield_border_width; ?>px #9A9A9A;
	padding-bottom:2px;
	padding-left:2px;
}

input.button, input.liteoption, input.mainoption {
	color:#<?php echo $fontcolor3; ?>;
	background:#<?php echo $inputfield_bgcolor ?>;
	border: #<?php echo $inputfield_border_color; ?> <?php echo $inputfield_border_style; ?> <?php echo $inputfield_border_width; ?>px;
	border-bottom: <?php echo $inputfield_border_style; ?> <?php echo $inputfield_border_width; ?>px #9A9A9A;
	border-right: <?php echo $inputfield_border_style; ?> <?php echo $inputfield_border_width; ?>px #9A9A9A;
	background:#e5e3e3;
	font-size:9px;
	padding : 1px;
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
	color:#<?php echo $fontcolor3; ?>;
	background:#<?php echo $inputfield_bgcolor ?>;
	border: #<?php echo $inputfield_border_color; ?> <?php echo $inputfield_border_style; ?> <?php echo $inputfield_border_width; ?>px;
	border-bottom: <?php echo $inputfield_border_style; ?> <?php echo $inputfield_border_width; ?>px #9A9A9A;
	border-right: <?php echo $inputfield_border_style; ?> <?php echo $inputfield_border_width; ?>px #9A9A9A;
	background:#e5e3e3;
	font-size:9px;
	padding : 1px;
}

.rowHover {
	background: #<?php echo $bgcolor; ?>;
	color:#<?php echo $fontcolor1; ?>;
}

div.graph {
	position: relative;
	width: 98%;
	border: #<?php echo $inputfield_border_color; ?> <?php echo $inputfield_border_style; ?> <?php echo $inputfield_border_width; ?>px;
	padding: 2px;
	margin: 2px 0;
	text-align: center;
}

div.graph .bar {
	display: block;
	position: relative;
	background: #<?php echo $table_row2; ?>;
	color: #<?php echo $fontcolor1; ?>;
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
	color: #<?php echo $link_color; ?>;
	text-decoration: <?php echo $link_style; ?>;
	font-size: <?php echo $fontsize2; ?>px;
}

#rrs_news div.content a:hover {
	color: #<?php echo $hover_link_color; ?>;
	text-decoration: <?php echo $hover_link_style; ?>;
}

#rrs_news div.content {
	margin-bottom : 10px;
	border: none;
	color: #<?php echo $fontcolor1; ?>;
	font-size: <?php echo $fontsize1; ?>px;
	margin: 0px;
	padding: 10px;
	background-color: #<?php echo $table_th; ?>;
	overflow: hidden;
}

#rrs_news div.title {
	cursor: pointer;
	display: block;
	padding: 5px;
	margin-top: 0px;
	text-decoration: none;
	font-weight: bold;
	font-size: <?php echo $fontsize1; ?>px;
	color: #<?php echo $header_link_color; ?>;
	background: #2b577c url(images/head.jpg) repeat-x top;
	border: 0px solid #252525;
}

#rrs_news div.title:hover {
	color: #<?php echo $hover_header_link_color; ?>;
	background: #346995 url(images/head_hl.jpg) repeat-x top;
}

#rrs_news div.title.selected {
	color: #<?php echo $header_link_color; ?>;
	background: #43759f url(images/head_active.jpg) repeat-x top;
	border-bottom: 1px solid black;
}

#wrapper{
	background: transparent;
	width: 1050px;
	margin: 0px auto;
}

#toptitles{
	position: absolute;
	right: 70px;
	top: 30%;
	text-transform: uppercase;
	color: #<?php echo $header_link_color; ?>;
	text-align: right;
}

#header{
  width: 1150px;
	margin: 0px auto;
	position: relative;
	height: 180px;
}

#header h1{
	margin-left: 100px;
	margin-right: 0px;
	margin-top: 0px;
	margin-bottom: 0px;
	text-indent: -9999px;
	height: 180px;
}

#navigation{
	position: absolute;
	right: 60px;
	bottom: 0px;
}

#navigation ul{
	margin: 0px;
	padding: 0px;
	list-style-type: none;
}

#navigation ul li{
	float: left;
}

#navigation ul li a{
	background: transparent url(images/nava.png) repeat-x top;
	/*text-transform: uppercase;*/
	margin-left: 5px;
	display: block;
	float: left;
	padding: 10px;
	padding-left: 15px;
	padding-right: 15px;
	color: #<?php echo $header_link_color; ?>;
	text-decoration: none;
	font-weight: bold;
	font-size: 15px;
  border-top: 1px solid #<?php echo $table_border_color; ?>;
  border-left: 1px solid #<?php echo $table_border_color; ?>;
  border-right: 1px solid #<?php echo $table_border_color; ?>;
}

#navigation ul li a:hover{
	background: #333333;
	text-decoration: underline;
	border-top: 1px solid #<?php echo $table_border_color; ?>;
	border-left: 1px solid #<?php echo $table_border_color; ?>;
	border-right: 1px solid #<?php echo $table_border_color; ?>;
}

#navigation ul li a:active{
	background: transparent url(images/nava.png) repeat-x top;
	border-top: 1px solid #<?php echo $table_th; ?>;
	border-left: 1px solid #<?php echo $table_th; ?>;
  	border-right: 1px solid #<?php echo $table_th; ?>;
}

#footer{
	text-align: center;
	padding: 20px;
	background: #333333 url(images/footbg.jpg) repeat-x top;
}

td.menu{
	background:#14293b url(images/table.jpg) repeat-x top;
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
	width:720px;
	margin-top: 10px;
	margin-right : auto;
	margin-bottom: 10px;
	margin-left: auto;
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
	border-top: 1px dashed ;
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
	color:#<?php echo $header_link_color; ?>;
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