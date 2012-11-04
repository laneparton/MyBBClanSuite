<?php

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

function matches_info()
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
		"name"			=> "Matches",
		"description"	=> "Adds a match reporting system",
		"website"		=> "http://www.c4powered.co.uk",
		"author"		=> "Benjamin Ely",
		"authorsite"	=> "http://www.c4powered.co.uk",
		"version"		=> "0.1",
		"compatibility" => "16*"
	);
}

function matches_install()
{
	global $db;
	
	$db->write_query("
CREATE TABLE IF NOT EXISTS `".TABLE_PREFIX."matches` (
  `id` int(10) NOT NULL auto_increment,
  `opponent_name` varchar(120) NOT NULL,
  `opponent_website` varchar(240) NOT NULL,
  `opponent_score` int(2) NOT NULL default '0',
  `team_id` int(10) NOT NULL,
  `team_score` int(2) NOT NULL default '0',
  `competition` varchar(240) NOT NULL,
  `date` text NOT NULL,
  `opponent_teamline` varchar(250) NOT NULL,
  `team_teamline` varchar(250) NOT NULL,
  `report` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=17 ;");				
			
	matches_templates();
				
	rebuild_settings();
}

function matches_is_installed()
{
	global $db;
	
	if($db->table_exists("matches"))
 	{
 		return true;
	}	
	return false;
}

function matches_uninstall()
{
	global $db;
	
	$db->write_query("DROP TABLE ".TABLE_PREFIX."matches");
	
	$db->delete_query("templates","title ='matches_index'");
	$db->delete_query("templates","title ='matches_row'");
	$db->delete_query("templates","title ='matches_view'");	
	
	rebuild_settings();
}

function matches_templates()
{
	global $db;
	
	$temp = array(
		"sid"		=> "NULL",
		"title"		=> "matches_index",
		"template"	=> $db->escape_string('<html>
<head>
<title>{$mybb->settings[\'bbname\']}</title>
{$headerinclude}
</head>
<body>

{$header}

<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder" style="clear: both; border-bottom-width: 0;">

<tr><td class="thead" colspan="6">Recent Results</td></tr>

<tr>
<td width="55px" class="tcat"><b>Team</b></td>
<td width="125px" class="tcat"><b>Opponent</b></td>
<td width="161px" class="tcat"><b>Competition</b></td>
<td width="70px" class="tcat"><b>Date</b></td>
<td width="77px" class="tcat"><b>Result</b></td>
<td width="150px" class="tcat"><b>Details</b></td>
</tr>

{$matches_row}

</table>

<br />

{$numbers}

<br />

{$footer}
</body>
</html>
'),
		"sid"		=> "-1",
		"version"	=> "1.0",
		"status"	=> "0",
		"dateline"	=> time(),
	);
	
	$db->insert_query("templates", $temp);
	
	$temp = array(
		"sid"		=> "NULL",
		"title"		=> "matches_row",
		"template"	=> $db->escape_string('<tr>
<td width="70px" class="trow1">{$team_tag}</td>
<td width="125px" class="trow1"><a href="http://{$match[\'opponent_website\']}">{$match[\'opponent_name\']}</a></td>
<td width="131px" class="trow1">{$match[\'competition\']}</td>
<td width="70px" class="trow1">{$match[\'date\']}</td>
<td width="77px" class="trow1">{$result}</td>
<td width="145px" class="trow1">
      <a href="matches.php?id={$match[\'id\']}">Link</a>
</td>
</tr>
'),
		"sid"		=> "-1",
		"version"	=> "1.0",
		"status"	=> "0",
		"dateline"	=> time(),
	);
	
	$db->insert_query("templates", $temp);
	
	$temp = array(
		"sid"		=> "NULL",
		"title"		=> "matches_view",
		"template"	=> $db->escape_string('<html>
<head>
<title>{$mybb->settings[\'bbname\']}</title>
{$headerinclude}
</head>
<body>

{$header}

<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder" style="clear: both; border-bottom-width: 0;">

<tr><td class="thead" colspan="2">vs {$match[\'opponent_name\']}</td></tr>

<tr><td width="35%" class="trow1">Squad:</td><td class="trow1">{$team_name}</td></tr>
<tr><td width="35%" class="trow1">Squad Line Up:</td><td class="trow1">{$match[\'team_teamline\']}</td></tr>
<tr><td width="35%" class="trow1">Opponent:</td><td class="trow1">{$match[\'opponent_name\']}</td></tr>
<tr><td width="35%" class="trow1">Opponent\'s Website:</td><td class="trow1">{$match[\'opponent_website\']}</td></tr>
<tr><td width="35%" class="trow1">Opponent\'s Line Up:</td><td class="trow1">{$match[\'opponent_teamline\']}</td></tr>
<tr><td width="35%" class="trow1">Result:</td><td class="trow1">{$result}</td></tr>
<tr><td width="35%" class="trow1">Date:</td><td class="trow1">{$match[\'date\']}</td></tr>
<tr><td width="35%" class="trow1">Competition:</td><td class="trow1">{$match[\'competition\']}</td></tr>

</table>

<br />

<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder" style="clear: both; border-bottom-width: 0;">

<tr><td class="thead">Match Report</td></tr>
<tr><td width="35%" class="trow1">{$match[\'report\']}</td></tr>
</table>

<br />

{$footer}
</body>
</html>
'),
		"sid"		=> "-1",
		"version"	=> "1.0",
		"status"	=> "0",
		"dateline"	=> time(),
	);
	
	$db->insert_query("templates", $temp);
}

?>