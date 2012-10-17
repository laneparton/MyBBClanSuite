<?php
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

// start the page
	$page->output_header("Clan Suite Home");
	
	$sub_tabs['cshome'] = array(
		'title' => "Clan Suite",
		'link' => "index.php?module=clansuite",
		'description' => "Welcome to the Dashboard of the Clan Suite Mod for Mybb!<br /><br /> If you experience<strong> ANY </strong>problems, please feel free to <a href=\"mailto:admin@clandev.net\">email</a> me(Mini). <br /><br /> Further support can be obtained from the MyBB forums or <a href=\"http://clandev.net/\"> ClanDev.Net </a><br /><br /> The Clan Suite was originally made by <a href=\"http://community.mybb.com/user-18224.html\"> Benely </a> but has recently been updated by <a href=\"http://community.mybb.com/user-28414.html\"> Mini' </a>
	");
	
	$page->output_nav_tabs($sub_tabs, 'cshome');
	
	$page->output_footer();

?>