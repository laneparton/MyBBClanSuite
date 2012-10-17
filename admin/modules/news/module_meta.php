<?php
/**
 *
 */

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

function news_meta()
{
	// get access to everything we want
	global $page, $lang, $plugins, $db;

	// this is a list of sub menus
	$sub_menu = array();
	$sub_menu['10'] = array("id" => "addnew", "title" => "Add News", "link" => "index.php?module=news/addnew");
	$sub_menu['20'] = array("id" => "manage", "title" => "Manage News", "link" => "index.php?module=news/manage");
	
	// custom plugin hooks!
	$plugins->run_hooks_by_ref("admin_forum_menu", $sub_menu);
	
	$page->add_menu_item("News Manager", "news", "index.php?module=news", 80, $sub_menu);

	if($db->table_exists("news"))
	{
		return true;
	}
	return false;
}

function news_action_handler($action)
{
	global $page, $lang, $plugins;
	
	// our module's name
	$page->active_module = "news";
	
	// the available actions and their pages
	$actions = array(
		'addnew' => array('active' => 'addnew', 'file' => 'addnew.php'),
		'manage' => array('active' => 'manage', 'file' => 'manage.php'),
	);
	
	// more custom plugin hooks!
	$plugins->run_hooks_by_ref("admin_news_action_handler", $actions);
	
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