<?php

if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

function servers_info()
{

	return array(
		"name"			=> "Server List",
		"description"	=> "Creates a server list.",
		"website"		=> "http://clandev.net/",
		"author"		=> "Mini`",
		"authorsite"	=> "http://clandev.net/",
		"version"		=> "0.1",
		"compatibility" => "16*"
	);
}


function servers_install()
{
	global $db;
	
	$db->write_query("CREATE TABLE IF NOT EXISTS `".TABLE_PREFIX."servers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(240) NOT NULL,
  `image` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;");

	servers_templates();
	rebuild_settings();	
}

function servers_is_installed()
{
	global $db;
	
	if($db->table_exists("servers"))
 	{
 		return true;
	}	
	return false;
}

function servers_uninstall()
{
	global $db;
	
	$db->write_query("DROP TABLE ".TABLE_PREFIX."servers");
	
	$db->delete_query("templates","title ='servers_index'");
	$db->delete_query("templates","title ='servers_list'");
	$db->delete_query("templates","title ='servers_list_none'");
	
	rebuild_settings();	
}

function servers_templates()
{
	global $db;

	$temp = array(
		"sid"		=> "NULL",
		"title"		=> "servers_index",
		"template"	=> $db->escape_string('<html>
<head>
<title>{$mybb->settings[\'bbname\']}</title>
{$headerinclude}
</head>
<body>

{$header}
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder" style="clear: both; border-bottom-width: 0;">

<tr><td class="thead" colspan="2">Server List</td></tr>

{$servers_bit}
</table><br />
{$footer}
</body>
</html>'),
		"sid"		=> "-1",
		"version"	=> "1.0",
		"status"	=> "0",
		"dateline"	=> time(),
	);
	
	$db->insert_query("templates", $temp);
	
	$temp = array(
		"sid"		=> "NULL",
		"title"		=> "servers_list",
		"template"	=> $db->escape_string('
<tr><td class="trow1">{$server_image}</td></tr>'),
		"sid"		=> "-1",
		"version"	=> "1.0",
		"status"	=> "0",
		"dateline"	=> time(),
	);
	
	$db->insert_query("templates", $temp);
	
	$temp = array(
		"sid"		=> "NULL",
		"title"		=> "servers_list_none",
		"template"	=> $db->escape_string('<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder" style="clear: both; border-bottom-width: 0;">

<tr><td class="thead">No Servers</td></tr>

<tr><td class="trow1">There are currently no servers in the database.</td></tr>

</table'),
		"sid"		=> "-1",
		"version"	=> "1.0",
		"status"	=> "0",
		"dateline"	=> time(),
	);
	
	$db->insert_query("templates", $temp);		
}
 
?>