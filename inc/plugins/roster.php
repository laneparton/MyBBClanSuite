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

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

function roster_info()
{
	/**
	 * Array of information about the plugin.
	 * name: The name of the plugin
	 * description: Description of what the plugin does
	 * website: The website the plugin is maintained at (Optional)
	 * author: The name of the author of the plugin
	 * authorsite: The URL to the website of the author (Optional)
	 * version: The version number of the plugin
	 * guid: Unique ID issued by the MyBB Mods site for version checking
	 * compatibility: A CSV list of MyBB versions supported. Ex, "121,123", "12*". Wildcards supported.
	 */
	return array(
		"name"			=> "Roster",
		"description"	=> "Creates a page to display teams and their rosters.",
		"website"		=> "http://www.c4powered.co.uk",
		"author"		=> "Benjamin Ely",
		"authorsite"	=> "http://www.c4powered.co.uk",
		"version"		=> "0.1",
		"compatibility" => "16*"
	);
}


function roster_install()
{
	global $db;
	
	$db->write_query("CREATE TABLE IF NOT EXISTS `".TABLE_PREFIX."rosterteams` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(240) NOT NULL,
  `image` text,
  `des` text,
  `tag` varchar(5) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;");

	$db->write_query("CREATE TABLE IF NOT EXISTS `".TABLE_PREFIX."rostermembers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `uid` int(10) default NULL,
  `uname` varchar(240) NOT NULL,
  `team` int(10) default NULL,
  `position` varchar(240) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ;");

	roster_templates();
				
	rebuild_settings();	
}

function roster_is_installed()
{
	global $db;
	
	if($db->table_exists("rostermembers") && $db->table_exists("rosterteams"))
 	{
 		return true;
	}	
	return false;
}

function roster_uninstall()
{
	global $db;
	
	$db->write_query("DROP TABLE ".TABLE_PREFIX."rosterteams");
	$db->write_query("DROP TABLE ".TABLE_PREFIX."rostermembers");
	
	$db->delete_query("templates","title ='roster_index'");
	$db->delete_query("templates","title ='roster_team'");
	$db->delete_query("templates","title ='roster_team_none'");
	$db->delete_query("templates","title ='roster_user'");
	$db->delete_query("templates","title ='roster_user_none'");
	
	rebuild_settings();	
}

function roster_templates()
{
	global $db;

	$temp = array(
		"sid"		=> "NULL",
		"title"		=> "roster_index",
		"template"	=> $db->escape_string('<html>
<head>
<title>{$mybb->settings[\'bbname\']}</title>
{$headerinclude}
</head>
<body>

{$header}

{$teams_bit}

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
		"title"		=> "roster_team_imgnull",
		"template"	=> $db->escape_string('<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder" style="clear: both; border-bottom-width: 0;">

<tr><td class="thead" colspan="2">{$team_name}</td></tr>

<tr><td class="tcat">Username</td><td class="tcat">Position</td></tr>

{$members_bit}

</table><br />'),
		"sid"		=> "-1",
		"version"	=> "1.0",
		"status"	=> "0",
		"dateline"	=> time(),
	);
	
	$db->insert_query("templates", $temp);
	
	$temp = array(
		"sid"		=> "NULL",
		"title"		=> "roster_team",
		"template"	=> $db->escape_string('<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder" style="clear: both; border-bottom-width: 0;">

<tr><td class="thead" colspan="2"><img src="{$team_image}" alt="{$team_name}" /></td></tr>

<tr><td class="tcat">Username</td><td class="tcat">Position</td></tr>

{$members_bit}

</table><br />'),
		"sid"		=> "-1",
		"version"	=> "1.0",
		"status"	=> "0",
		"dateline"	=> time(),
	);
	
	$db->insert_query("templates", $temp);
	
	$temp = array(
		"sid"		=> "NULL",
		"title"		=> "roster_team_none",
		"template"	=> $db->escape_string('<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder" style="clear: both; border-bottom-width: 0;">

<tr><td class="thead">No Teams</td></tr>

<tr><td class="trow1">There are currently no teams set up</td></tr>

</table'),
		"sid"		=> "-1",
		"version"	=> "1.0",
		"status"	=> "0",
		"dateline"	=> time(),
	);
	
	$db->insert_query("templates", $temp);
	
	$temp = array(
		"sid"		=> "NULL",
		"title"		=> "roster_user",
		"template"	=> $db->escape_string('<tr><td class="trow1"><a href="member.php?action=profile&uid={$members_uid}">{$user_name}</a></td><td class="trow1">{$members_pos}</td></tr>'),
		"sid"		=> "-1",
		"version"	=> "1.0",
		"status"	=> "0",
		"dateline"	=> time(),
	);
	
	$db->insert_query("templates", $temp);
	
	$temp = array(
		"sid"		=> "NULL",
		"title"		=> "roster_user_none",
		"template"	=> $db->escape_string('<tr><td class="trow1" colspan="2">This team does not have any users.</td></tr>'),
		"sid"		=> "-1",
		"version"	=> "1.0",
		"status"	=> "0",
		"dateline"	=> time(),
	);
	
	$db->insert_query("templates", $temp);				
}
 
?>