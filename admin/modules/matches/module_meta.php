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

	if($db->table_exists("matches"))
	{	// plugin installed, so show this module's link
		return true;
	}
	// I assume returning false means "don't do anything"
	// no adverse effects so far.
	return false;
}

function matches_action_handler($action)
{
	global $db, $page, $lang, $plugins;
	
	
	//Check number of rows
	$matches_query 	=	$db->query("SELECT * FROM " . TABLE_PREFIX . "matches");
	$matches_num	=	$db->num_rows($matches_query);
	$teams_query	=	$db->query("SELECT * FROM " . TABLE_PREFIX . "rosterteams");
	$teams_num		=	$db->num_rows($teams_query);
	
	// our module's name
	$page->active_module = "matches";
	
	// the available actions and their pages
	$actions = array(
		'addnew' => array('active' => 'addnew', 'file' => 'addnew.php'),
		'manage' => array('active' => 'manage', 'file' => 'manage.php'),
	);
	
	//If the action isn't set, then let's set it!
	if(!isset($actions[$action]))
	{
		if($teams_num > 0)
		{
			//If there are matches in the DB, let's manage them first!
			if($matches_num > 0)
			{
				$page->active_action	=	"manage";
			}
			//If not, let's go to the add new page first.
			else
			{
				$page->active_action	=	"addnew";
			}
		}
		else
		{
			header("Location: index.php?module=roster/addteam");
		}
	}
	else
	{
		$page->active_action = $actions[$action]['active'];
	}
	// more custom plugin hooks!
	$plugins->run_hooks_by_ref("admin_matches_action_handler", $actions);
	
	if($page->active_action == "manage" || $page->active_action == "addnew")
	{
	// this is a list of sub menus
	$sub_menu = array();
	$sub_menu['10'] = array("id" => "addnew", "title" => "Add Match", "link" => "index.php?module=matches/addnew");
	$sub_menu['20'] = array("id" => "manage", "title" => "Manage Matches", "link" => "index.php?module=matches/manage");
	
	$sidebar = new SidebarItem("Matches Manager");
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
		if($matches_num > 0)
		{
			$page->active_action	=	"manage";
			return "manage.php";
		}
		else
		{
			$page->active_action	=	"addnew";
			return "addnew.php";
		}
	}
}