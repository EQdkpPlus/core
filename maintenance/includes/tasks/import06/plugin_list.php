<?php
$plugin_names	= array(
	'shoutbox'	=> array(
    	'table'        => 'shoutbox_config',
    	'fieldprefix'  => 'sb_',
		'extra_tables' => array('shoutbox', 'shoutbox_config'),
	),
	'bosssuite'	=> array(
    	'table'        => 'bs_config',
    	'fieldprefix'  => 'bb_',
	),
	'raidlogimport'	=> array(
    	'table'        => 'raidlogimport_config',
    	'fieldprefix'  => 'rli_',
		'extra_tables' => array('raidlogimport_bz', 'raidlogimport_config'),
	),
	'gallery'	=> array(
    	'table'        => 'gallery_config',
    	'fieldprefix'  => '',
	),
	'guildrequest'	=> array(
    	'table'        => 'guildrequest_config',
    	'fieldprefix'  => 'gr_',
	),
);
?>