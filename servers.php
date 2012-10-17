<?php

define("IN_MYBB", 1);
define('THIS_SCRIPT', 'roster.php');

$templatelist 	= "servers_index";
require_once "./global.php";
add_breadcrumb("Servers", "servers.php");

$servers_query 	= $db->query("SELECT * FROM " . TABLE_PREFIX . "servers");
$servers_num		= $db->num_rows($servers_query);

if ($servers_num > 0)
{
	while ($servers_array = $db->fetch_array($servers_query))
	{
		$server_id	= $servers_array['id'];
		$server_name	= $servers_array['name'];
		$server_image	= $servers_array['image'];
		
		eval("\$servers_bit .= \"".$templates->get("servers_list")."\";");
	}
}
else
{
	eval("\$servers_bit = \"".$templates->get("servers_list_none")."\";");
}

eval("\$servers = \"".$templates->get("servers_index")."\";");
output_page($servers);

?>