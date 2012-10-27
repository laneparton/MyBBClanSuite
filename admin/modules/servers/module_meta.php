<?php
/**
 *
 */

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

function servers_meta()
{
	// get access to everything we want
	global $page, $lang, $plugins, $db;

	if($db->table_exists("servers"))
	{
		return true;
	}
	return false;
}

function servers_action_handler($action)
{
	global $db, $page, $lang, $plugins;
	
	
	$servers_query 	= $db->query("SELECT * FROM " . TABLE_PREFIX . "servers");
	$servers_num		= $db->num_rows($servers_query);
	
	$page->active_module = "servers";
	
	// the available actions and their pages
	$actions = array(
		'addserver' => array('active' => 'addserver', 'file' => 'addserver.php'),
		'manage' => array('active' => 'manage', 'file' => 'manage.php'),
	);
	
	if(!isset($actions[$action]))
	{
		if($servers_num > 0)
		{
			$page->active_action	=	"manage";
		}
		else
		{
			$page->active_action	=	"addnew";
		}
	}
	else
	{
		$page->active_action = $actions[$action]['active'];
	}
	// more custom plugin hooks!
	$plugins->run_hooks_by_ref("admin_servers_action_handler", $actions);
	
	if($page->active_action == "manage" || $page->active_action == "addserver")
	{
	// this is a list of sub menus
	$sub_menu = array();
	$sub_menu['10'] = array("id" => "addserver", "title" => "Add Server", "link" => "index.php?module=servers/addserver");
	$sub_menu['20'] = array("id" => "manage", "title" => "Manage Servers", "link" => "index.php?module=servers/manage");
	
	$sidebar = new SidebarItem("Server Manager");
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
		if($servers_num > 0)
		{
			$page->active_action	=	"manage";
			return "manage.php";
		}
		else
		{
			$page->active_action	=	"addnew";
			return "addserver.php";
		}
	}
}