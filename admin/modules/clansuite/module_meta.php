<?php
/**
 *
 */

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

function clansuite_meta()
{
	// get access to everything we want
	global $page, $lang, $plugins, $db;

	// this is a list of sub menus
	$sub_menu = array();
	if($db->table_exists("news"))
	{
		$sub_menu['10'] = array("id" => "News", "title" => "News Manager", "link" => "index.php?module=news");
	}
	if($db->table_exists("matches") && $db->table_exists("rosterteams"))
	{
		$sub_menu['20'] = array("id" => "Matches", "title" => "Match Manager", "link" => "index.php?module=matches");
	}
	if($db->table_exists("rosterteams"))
	{
		$sub_menu['30'] = array("id" => "Roster", "title" => "Roster Manager", "link" => "index.php?module=roster");
	}
	if($db->table_exists("servers"))
	{
		$sub_menu['40'] = array("id" => "Servers", "title" => "Server Manager", "link" => "index.php?module=servers");
	}
		
	// custom plugin hooks!
	$plugins->run_hooks_by_ref("admin_forum_menu", $sub_menu);
	
	$page->add_menu_item("Clan Suite", "clansuite", "index.php?module=clansuite", 81, $sub_menu);

	return true;
}

function clansuite_action_handler($action)
{
	global $page, $lang, $plugins;
	
	// our module's name
	$page->active_module = "clansuite";
	
	// the available actions and their pages
	$actions = array(
		'home' => array('active' => 'home', 'file' => 'home.php'),

	);
	
	if(isset($actions[$action]))
	{	// set the action and return the page
		$page->active_action = $actions[$action]['active'];
		return $actions[$action]['file'];
	}
	else
	{	// return the default page
		$page->active_action = "home";
		return "home.php";
	}
	
}