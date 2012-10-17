<?php
/**
 *
 */

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

function matches_meta()
{
	// get access to everything we want
	global $page, $lang, $plugins, $db;

	// this is a list of sub menus
	$sub_menu = array();
	$sub_menu['10'] = array("id" => "addnew", "title" => "Add Match", "link" => "index.php?module=matches/addnew");
	$sub_menu['20'] = array("id" => "manage", "title" => "Manage Matches", "link" => "index.php?module=matches/manage");
	
	// custom plugin hooks!
	$plugins->run_hooks_by_ref("admin_forum_menu", $sub_menu);
	
	$page->add_menu_item("Match Manager", "matches", "index.php?module=matches", 81, $sub_menu);

	if($db->table_exists("matches"))
	{	// plugin installed, so show this module's link
		// add_menu_item(title, name, link, display order, submenus)
		return true;
	}
	// I assume returning false means "don't do anything"
	// no adverse effects so far.
	return false;
}

function matches_action_handler($action)
{
	global $page, $lang, $plugins;
	
	// our module's name
	$page->active_module = "matches";
	
	// the available actions and their pages
	$actions = array(
		'addnew' => array('active' => 'addnew', 'file' => 'addnew.php'),
		'manage' => array('active' => 'manage', 'file' => 'manage.php'),
	);
	
	// more custom plugin hooks!
	$plugins->run_hooks_by_ref("admin_matches_action_handler", $actions);
	
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