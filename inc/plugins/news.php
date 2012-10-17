<?php

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

function news_info()
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
		"name"			=> "News",
		"description"	=> "Adds a news feature",
		"website"		=> "http://www.c4powered.co.uk",
		"author"		=> "Benjamin Ely",
		"authorsite"	=> "http://www.c4powered.co.uk",
		"version"		=> "0.1",
		"compatibility" => "16*"
	);
}

function news_install()
{
	global $db;
	
	$db->write_query("CREATE TABLE IF NOT EXISTS `".TABLE_PREFIX."news` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(120) default NULL,
  `uid` int(10) default NULL,
  `date_posted` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `content` text,
  `image` varchar(250) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;");	
				
	$db->write_query("CREATE TABLE IF NOT EXISTS `".TABLE_PREFIX."newscomments` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `nid` int(10) default NULL,
  `uid` int(10) default NULL,
  `date_posted` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `content` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;");

	news_templates();			
				
	rebuild_settings();
}

function news_is_installed()
{
	global $db;
	
	if($db->table_exists("news"))
 	{
 		return true;
	}	
	return false;
}

function news_uninstall()
{
	global $db;
	
	$db->write_query("DROP TABLE ".TABLE_PREFIX."news");
	$db->write_query("DROP TABLE ".TABLE_PREFIX."newscomments");
	
	$db->delete_query("templates","title ='news_index'");
	$db->delete_query("templates","title ='news_item'");
	$db->delete_query("templates","title ='news_view'");
	$db->delete_query("templates","title ='news_view_comments'");
	$db->delete_query("templates","title ='news_view_nocomments'");
	$db->delete_query("templates","title ='news_view_commentsubmit'");	
	
	rebuild_settings();
}

function news_templates()
{
	global $db;
	
	$temp = array(
		"sid"		=> "NULL",
		"title"		=> "news_index",
		"template"	=> $db->escape_string('<html>
<head>
<title>{$mybb->settings[\'bbname\']}</title>
{$headerinclude}
</head>
<body>

{$header}

{$news_post}

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
		"title"		=> "news_item",
		"template"	=> $db->escape_string('<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder" style="clear: both; border-bottom-width: 0;">
<tr><td colspan="2" class="thead"><a href="news.php?id={$fetch[\'id\']}">{$title}</a></td></tr>
<tr><td class="tcat">{$posted} posted by: <a href="member.php?action=profile&uid={$uid}">{$poster}</a></td><td align="right" class="tcat">{$replies} replies</td>
<tr><td colspan="2" class="trow1">{$content}</td></tr>
<tr><td colspan="2" align="right" class="tfoot"><a href="news.php?id={$fetch[\'id\']}">Read more</a></td></tr>
</table>

<br />
'),
		"sid"		=> "-1",
		"version"	=> "1.0",
		"status"	=> "0",
		"dateline"	=> time(),
	);
	
	$db->insert_query("templates", $temp);
	
	$temp = array(
		"sid"		=> "NULL",
		"title"		=> "news_view",
		"template"	=> $db->escape_string('<html>
<head>
<title>{$mybb->settings[\'bbname\']}</title>
{$headerinclude}
</head>
<body>

{$header}

<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder" style="clear: both; border-bottom-width: 0;">
<tr><td colspan="2" class="thead">{$title}</td></tr>
<tr><td class="tcat">{$posted} posted by: <a href="member.php?action=profile&uid={$uid}">{$poster}</a></td><td align="right" class="tcat">{$replies} replies</td>
<tr><td colspan="2" class="trow1">{$article}</td></tr>
</table>

<br />

{$news_form}

<br />

<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder" style="clear: both; border-bottom-width: 0;">
<tr><td colspan="2" class="thead">Comments</td></tr>
{$news_comments}
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
		"title"		=> "news_view_comments",
		"template"	=> $db->escape_string('<tr>
<td class="trow1" width="25%">
<img src="{$commenter_avatar}" width="90px" alt="" /><br />
<a href="member.php?action=profile&uid={$comment_uid}">{$commenter_username}</a><br />
{$comment_posted}
</td>

<td class="trow1">
{$comment_content}
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
		"title"		=> "news_view_nocomments",
		"template"	=> $db->escape_string('
<tr><td colspan="2" class="trow1">No comments on this article</div></td></tr>
'),
		"sid"		=> "-1",
		"version"	=> "1.0",
		"status"	=> "0",
		"dateline"	=> time(),
	);
	
	$db->insert_query("templates", $temp);
		
	$temp = array(
		"sid"		=> "NULL",
		"title"		=> "news_view_commentsubmit",
		"template"	=> $db->escape_string('<form action="news.php?id={$id}" method="post"> 
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder" style="clear: both; border-bottom-width: 0;">
<tr><td class="thead">Submit a comment</td></tr>
<tr><td colspan="2" class="trow1"><textarea name="comment" id="comment" style="width: 98%;" cols="40" rows="6"></textarea></td></tr>
</table>
<div style="width: 100%; text-align: right; margin-top: 6px;">
<input type="submit" value="Submit">
</div>
</form>
'),
		"sid"		=> "-1",
		"version"	=> "1.0",
		"status"	=> "0",
		"dateline"	=> time(),
	);
	
	$db->insert_query("templates", $temp);
}

?>