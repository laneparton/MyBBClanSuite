<?php
/**
 *
 */

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

function roster_meta()
{
	// get access to everything we want
	global $page, $lang, $plugins, $db;

	if($db->table_exists("rosterteams"))
	{	// plugin installed, so show this module's link
		// add_menu_item(title, name, link, display order, submenus)
		return true;
	}
	// I assume returning false means "don't do anything"
	// no adverse effects so far.
	return false;
}

function roster_action_handler($action)
{
	global $page, $lang, $plugins;
	
	// our module's name
	$page->active_module = "roster";
	
	// the available actions and their pages
	$actions = array(
		'addteam' => array('active' => 'addteam', 'file' => 'addteam.php'),
		'manage' => array('active' => 'manage', 'file' => 'manage.php'),
		'addplayer' => array('active' => 'addplayer', 'file' => 'addplayer.php'),
	);
	
	if(!isset($actions[$action]))
	{
		$page->active_action = "manage";
	}
	else
	{
		$page->active_action = $actions[$action]['active'];
	}
	// more custom plugin hooks!
	$plugins->run_hooks_by_ref("admin_roster_action_handler", $actions);
	
	if($page->active_action == "manage" || $page->active_action == "addteam" || $page->active_action == "addplayer")
	{
	// this is a list of sub menus
	// this is a list of sub menus
	$sub_menu = array();
	$sub_menu['10'] = array("id" => "addteam", "title" => "Create Team", "link" => "index.php?module=roster/addteam");
	$sub_menu['20'] = array("id" => "manage", "title" => "Manage Teams", "link" => "index.php?module=roster/manage");
	$sub_menu['30'] = array("id" => "addplayer", "title" => "Add Player", "link" => "index.php?module=roster/addplayer");
	
	$sidebar = new SidebarItem("Roster Manager");
	$sidebar->add_menu_items($sub_menu, $page->active_action);

	$page->sidebar .= $sidebar->get_markup();
	}
	
	if(isset($actions[$action]))
	{	// set the action and return the page
		$page->active_action = $actions[$action]['active'];
		return $actions[$action]['file'];
	}
	else
	{	// return the default page
		$page->active_action = "manage";
		return "manage.php";
	}
}