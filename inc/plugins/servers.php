<?php
/*
This file is part of the MyBBClanSuite.

    The MyBBClanSuite is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    The MyBBClanSuite is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with The MyBBClanSuite.  If not, see <http://www.gnu.org/licenses/>.
	*/

if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

function servers_info()
{

	return array(
		"name"			=> "Server List",
		"description"	=> "Creates a server list.",
		"website"		=> "http://mybb.com",
		"author"		=> "Mini`",
		"authorsite"	=> "http://mybb.com",
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